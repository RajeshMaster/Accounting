<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ExpensesData;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Carbon;

class ExpensesDataController extends Controller {

	/**
	*
	* Get  Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function index(Request $request) {

		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$bankAcconoforCheck = "";
		$bankNameforCheck ="";
		$expensesDetailsIndex = ExpensesData::fetchExpensesData($request);
		$expensesDetails = array();
		$i = 0;
		$balanceAmtonDownTr = 0;
		foreach ($expensesDetailsIndex as $key => $value) {
			$expensesDetails[$i]['id'] = $value->id;
			$expensesDetails[$i]['empId'] = $value->empId;
			$expensesDetails[$i]['content'] = $value->content;
			$expensesDetails[$i]['amount'] = $value->amount;
			$expensesDetails[$i]['fee'] = $value->fee;
			$expensesDetails[$i]['FirstName'] = $value->FirstName;
			$expensesDetails[$i]['Bank_NickName'] = $value->Bank_NickName;
			$expensesDetails[$i]['remarks'] = $value->remarks;
			$expensesDetails[$i]['delFlg'] = $value->delFlg;
			$expensesDetails[$i]['baseAmt'] = 0;
			$expensesDetails[$i]['bankId'] = $value->bankId;
			$expensesDetails[$i]['banknm'] = $value->banknm;
			$expensesDetails[$i]['brnchnm'] = $value->brnchnm;
			$expensesDetails[$i]['brnchid'] = $value->brnchid;
			$expensesDetails[$i]['bankIdFrom'] = $value->bankIdFrom;
			$expensesDetails[$i]['fileDtl'] = $value->fileDtl;
			$expensesDetails[$i]['accNo'] = $value->accountNumberFrom;
			$baseAmt = ExpensesData::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
			$expensesDetails[$i]['subId'] = $value->subjectId;
			$expensesDetails[$i]['subject'] = $value->Subject;
			$expensesDetails[$i]['Subject_jp'] = $value->Subject_jp;
			$expensesDetails[$i]['employeDetails'] = "";
			$expensesDetails[$i]['invoiceDetails'] = "";
			$expensesDetails[$i]['loanDetails'] = "";
			$expensesDetails[$i]['pagecashSubject'] = "";

			$empname = ExpensesData::fnGetEmpName($value->empId);
			if (isset($empname[0]->LastName)) {
				$name = $empname[0]->LastName;
			} else {
				$name = "";
			}

			$expensesDetails[$i]['employeDetails'] = $value->empId.'-'.$name;

			$expensesDetails[$i]['balanceAmtonDownTr'] = 0;
			$expensesDetails[$i]['curBal'] = 0;

			$checkPrevious = ExpensesData::fnGetRecordPreviousForAmountCheck(date('Y-m'),$value->bankIdFrom,$value->accountNumberFrom);


			if (isset($baseAmt[0]->amount)) {
				$expensesDetails[$i]['startDate'] = $baseAmt[0]->date;
				$expensesDetails[$i]['baseAmt'] = $baseAmt[0]->amount;

				if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
					$curBal = $baseAmt[0]->amount;

					if (empty($checkPrevious)) {

						if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
							$curBal = $baseAmt[0]->amount;
						}
					} else {

						$prevBalanceAmt = ExpensesData::AccBalance($value->bankIdFrom,$value->AccNo,$baseAmt[0]->date);
					
						foreach ($prevBalanceAmt AS $prevBalKey => $prevBalVal) {
							if ($prevBalVal->transcationType == 2 || $prevBalVal->transcationType == 4) {
								$curBal += $prevBalVal->amount;
							} else {
								$curBal -= $prevBalVal->amount;
							}
							$curBal -= $prevBalVal->fee;
						} 
					}

				}
				$expensesDetails[$i]['curBal'] = $curBal;
				$expensesDetails[$i]['balanceAmtonDownTr'] = $curBal;
			}

			$bankAcconoforCheck = $value->accountNumberFrom;
			$bankNameforCheck = $value->Bank_NickName;
			$i++;
		}

		return view('ExpensesData.index',['request' => $request,
											'expensesDetails' => $expensesDetails,
											'expensesDetailsIndex' => $expensesDetailsIndex
										]);
	}


	/**
	*
	* Get banck Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function bank_ajax(Request $request) {

		$bankacc = $request->bankacc;
		$getbankDtl = ExpensesData::fnGetbankName($bankacc);
		$bankarray = json_encode($getbankDtl);
		echo $bankarray;
		exit();

	}

	/**
	*
	* Addedit Page for Expenses Data
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function addedit(Request $request) {

		$bankDetail = ExpensesData::fetchbanknames();
		$mainSubDetail = ExpensesData::getMainExpName();

		return view('ExpensesData.addedit',[ 'request' => $request,
												'mainSubDetail' => $mainSubDetail,
												'bankDetail' => $bankDetail
											]);
	}



	/**
	*
	* Edit Page for Expenses Data
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function expensesDataEdit(Request $request) {

		if(!$request->edit_flg){ 
			return Redirect::to('ExpensesData/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$expensesDataEdit = array();
		$expensesDataEdit = ExpensesData::expensesDataEdit($request);
		$bankDetail = ExpensesData::fetchbanknames();
		if ($request->edit_flg == 1) {
		} else {
			$expensesDataEdit[0]->fileDtl = "";
			$expensesDataEdit[0]->Empname = "";
			$expensesDataEdit[0]->subjectId = "";
		}
		
		$mainSubDetail = ExpensesData::getMainExpName();
		return view('ExpensesData.addedit',[ 'request' => $request,
												'mainSubDetail' => $mainSubDetail,
												'bankDetail' => $bankDetail,
												'expensesDataEdit' => $expensesDataEdit
											]);
	}

	/**
	*
	* Addedit Process for Expenses Data
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function addeditprocess(Request $request) {

		if(!$request->edit_flg || $request->edit_flg == "2"){

			$autoincId = ExpensesData::getautoincrement();
			$ExpensesDataNo = "ExpensesData_".$autoincId;
			$fileName = "";
			$fileid = "expensesDataBill";

			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $ExpensesDataNo.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/ExpensesData';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}

			$insertProcess = ExpensesData::insExpensesData($request,$fileName);
			
			if($insertProcess) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {

			$autoincId = ExpensesData::getautoincrement();
			$ExpensesDataNo = "ExpensesData_".$autoincId;
			$fileName = "";
			$fileid = "expensesDataBill";

			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $ExpensesDataNo.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/ExpensesData';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}

			$updateProcess = ExpensesData::updExpensesData($request,$fileName);

			if($updateProcess) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		
		return Redirect::to('ExpensesData/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Emp Name Popup Page for Transfer
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/08
	*
	*/
	public function empnamepopup(Request $request) {

		$empname = ExpensesData::fnGetEmpDetails($request);
		$empnamenonstaff = ExpensesData::fnGetNonstaffEmpDetails($request);

		return view('Invoice.empnamepopup',['request' => $request,
											'empname' => $empname,
											'empnamenonstaff' => $empnamenonstaff
										]);
	}

	public function getExpDataDetails(Request $request){ 

		$getBankDtls = ExpensesData::fetchExpDataPopup($request);

		return view('ExpensesData.orderChangepopup',['request' => $request,
														'getBankDtls' => $getBankDtls,
													]);
	}

	/**  
	*  For Commit Process
	*  @author Sastha 
	*  @param $request
	*  Created At 2021/02/08
	**/
	public function commitProcess(Request $request) {
		
		$commit = ExpensesData::fngetcommitProcess($request);
		return Redirect::to('ExpensesData/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**  
	*  For DelFlg Process
	*  @author Sastha 
	*  @param $request
	*  Created At 2021/02/03
	**/

	public function changeDelFlg(Request $request) {

		$changeDelFlg = ExpensesData::changeDelFlg($request);
		return Redirect::to('ExpensesData/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

}