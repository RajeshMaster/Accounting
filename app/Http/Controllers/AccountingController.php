<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Accounting;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Fpdf;
use Fpdi;
require_once('vendor/setasign/fpdf/fpdf.php');
require_once('vendor/setasign/fpdi/fpdi.php');
use Carbon;
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PHPExcel_Style_Conditional;
use PHPExcel_Style_Color;

class AccountingController extends Controller {

	/**
	*
	* Get  Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
			// $request->date =  Session::get('date');
			// $request->amount =  Session::get('amount');
		}

		$from_date = "";
		$to_date = "";
		$previous_date = "";
		$date_month = "";
		$temp = "";
		$bankAcconoforCheck = "";
		$bankNameforCheck ="";

		$g_accountperiod = Accounting::fnGetAccountPeriodAcc();
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
		$curDate= date('Y-m-d');
		$db_year_month = array();

		$expall_query = Accounting::fnGetCashExpenseAllRecord();
		$dballrecord = array();
		foreach ($expall_query as $key => $value) {
			array_push($dballrecord, $value->date);
			// $dballrecord[]=$value->date;
		}
		$dballrecord = array_unique($dballrecord);
		$inc=0;
		foreach ($dballrecord AS $dbrecordallkey => $dbrecordallvalue) {
			$split_val = explode("-", $dbrecordallvalue);
			$loc=$split_val[0];
			if ($loc != $temp) {
				$inc=0;
			}
			$db_year_monthall[$split_val[0]][$inc] = intval($split_val[1]);
			$temp=$loc;
			$inc++;
		}
		$y=0;
		$m=0;

		if (!empty($db_year_monthall)) {
			foreach ($db_year_monthall AS $dballkey => $dbllvalue) {
				foreach ($dbllvalue AS $dballsubkey => $dbllsubvalue) {
					$yearMonthCon = $dballkey."-".str_pad($dbllsubvalue, 2, 0, STR_PAD_LEFT);
					$db_year_monthfullarray[$y] = $yearMonthCon;
					if ($y!=0) {
						$yearMnarray[$m] = $yearMonthCon;
						$m++;
					}
					$y++;
				}
			}
		}

		if (!isset($request->selMonth)) {
			$date_month=date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}

		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		if ($request->selMonth == "") {
			$request->selMonth = date('m');
		}
		if ($request->selYear == "") {
			$request->selYear = date('Y');
		}

		$last=date('Y-m', strtotime('last month'));
		$last1=date($date_month , strtotime($last . " last month"));
		$lastdate=explode("-",$last1);
		$lastyear=$lastdate[0];
		$lastmonth=$lastdate[1];


		if (!empty($request->previou_next_year)) {
			$splityear = explode("-",$request->previou_next_year);
			if (isset($splityear)) {
				if (intval($splityear[1]) > $account_close_mn) {
					$last_year = intval($splityear[0]);
					$current_year = intval($splityear[0]) + 1;
				} else {
					$last_year = intval($splityear[0]) - 1;
					$current_year = intval($splityear[0]);
				}
			}
		} else if (isset($request->selYear)) {
			if ($request->selMonth > $account_close_mn) {
				$current_year = intval($request->selYear) + 1;
				$last_year = intval($request->selYear);
			} else {
				$current_year = intval($request->selYear);
				$last_year = intval($request->selYear) - 1;
			}
		} else {
			if (date('m') > $account_close_mn) {
			    $current_year = date('Y')+1;
				$last_year = date('Y');
			} else {
			    $current_year = date('Y');
				$last_year = date('Y') - 1;
			}
		}

		$current_month=date('m');
		$year_month=array();
		if ($account_close_mn == 12) {
			for ($i = 1; $i <= 12; $i++) {
				$year_month[$current_year][$i] = $i;
			} 
		} else {
			for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
				$year_month[$last_year][$i] = $i;
			}
			for ($i = 1; $i <= $account_close_mn; $i++) {
				$year_month[$current_year][$i] = $i;
			}
		}

		$year_month_day=$current_year . "-" . $account_close_mn . "-01";
		$maxday=Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date=$last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date=$current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";

		$est_query=Accounting::fnGetCashExpenseRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->date;
		}

		$est_query1 = Accounting::fnGetCashExpenseRecordPrevious($from_date);

		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->date, 0, 4);
			$dbprevious[]=$value->date;
			$pre++;
		}

		$est_query2=Accounting::fnGetCashExpenseRecordNext($to_date);

		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->date;
		}
		$dbrecord = array_unique($dbrecord);
		$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);

		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-",$dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}

		$start = $request->selYear .'-'.$request->selMonth.'-01';
		$end = $request->selYear .'-'.$request->selMonth.'-'.Common::fnGetMaximumDateofMonth($start);
	
		$cashDetailsIndex = Accounting::fetchcashRegister($start,$end,$request,0);
		$cashDetails =array();
		$i = 0;
		$balanceAmtonDownTr = 0;
		foreach ($cashDetailsIndex as $key => $value) {
			$cashDetails[$i]['id'] = $value->id;
			$cashDetails[$i]['emp_ID'] = $value->emp_ID;
			$cashDetails[$i]['loan_ID'] = $value->loan_ID;
			$cashDetails[$i]['date'] = $value->date;
			$cashDetails[$i]['content'] = $value->content;
			$cashDetails[$i]['amount'] = $value->amount;
			$cashDetails[$i]['fee'] = $value->fee;
			$cashDetails[$i]['FirstName'] = $value->FirstName;
			$cashDetails[$i]['Bank_NickName'] = $value->Bank_NickName;
			$cashDetails[$i]['transcationType'] = $value->transcationType;
			$cashDetails[$i]['remarks'] = $value->remarks;
			$cashDetails[$i]['baseAmt'] = 0;
			$cashDetails[$i]['bankId'] = $value->bankId;
			$cashDetails[$i]['banknm'] = $value->banknm;
			$cashDetails[$i]['brnchnm'] = $value->brnchnm;
			$cashDetails[$i]['brnchid'] = $value->brnchid;
			$cashDetails[$i]['bankIdFrom'] = $value->bankIdFrom;
			$cashDetails[$i]['fileDtl'] = $value->fileDtl;
			$cashDetails[$i]['transferId'] = $value->transferId;
			$cashDetails[$i]['pageFlg'] = $value->pageFlg;
			$cashDetails[$i]['completedFlg'] = $value->completedFlg;
			$cashDetails[$i]['accNo'] = $value->accountNumberFrom;
			$baseAmt = Accounting::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
			$cashDetails[$i]['subId'] = $value->subjectId;
			$cashDetails[$i]['subject'] = $value->Subject;
			$cashDetails[$i]['Subject_jp'] = $value->Subject_jp;
			$cashDetails[$i]['contentSub'] = $value->contentSub;
			$cashDetails[$i]['contentSub_jp'] = $value->contentSub_jp;
			$cashDetails[$i]['employeDetails'] = "";
			$cashDetails[$i]['invoiceDetails'] = "";
			$cashDetails[$i]['loanDetails'] = "";
			$cashDetails[$i]['pagecashSubject'] = "";
			if($cashDetails[$i]['pageFlg'] == 1 || $cashDetails[$i]['pageFlg'] == 999) {
				$cashDetails[$i]['pagecashSubject'] = $value->loanName;;
			}

			if (($cashDetails[$i]['pageFlg'] == 2 || $cashDetails[$i]['pageFlg'] == 5) && $value->emp_ID != "") {
				$empIdArr[0] = $value->emp_ID;
				$empname = Accounting::fnGetEmpName($value->emp_ID);
				if (isset($empname[0]->LastName)) {
					$name = $empname[0]->LastName;
				} else {
					$name ="";
				}
				$cashDetails[$i]['employeDetails'] = $value->emp_ID.'-'.$name;
			} elseif ($cashDetails[$i]['pageFlg'] == 4 && $value->loan_ID != "") {
				$empIdArr[0] = $value->loan_ID;
				$cashDetails[$i]['invoiceDetails'] = $value->loan_ID.'-'.$value->loanName;
			} elseif ($cashDetails[$i]['pageFlg'] == 3 && $value->loan_ID != "") {
				$empIdArr[0] = $value->loan_ID;
				$cashDetails[$i]['loanDetails'] = $value->loanName;
			}

			$cashDetails[$i]['balanceAmtonDownTr'] = 0;
			$cashDetails[$i]['curBal'] = 0;

			$checkPrevious = Accounting::fnGetRecordPreviousForAmountCheck(substr($start, 0, 7) ,$value->bankIdFrom,$value->accountNumberFrom);


			if (isset($baseAmt[0]->amount)) {
				$cashDetails[$i]['startDate'] = $baseAmt[0]->date;
				$cashDetails[$i]['baseAmt'] = $baseAmt[0]->amount;

				if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
					$curBal = $baseAmt[0]->amount;

					if (empty($checkPrevious)) {

						if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
							$curBal = $baseAmt[0]->amount;
						}
					} else {

						$prYrMn = date("Y-m", strtotime("-1 months", strtotime($date_month)));
						$prevBalanceAmt = Accounting::AccBalance($value->bankIdFrom,$value->AccNo,$baseAmt[0]->date,$prYrMn);
					
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
				$cashDetails[$i]['curBal'] = $curBal;
				$cashDetails[$i]['balanceAmtonDownTr'] = $curBal;
			}

			$bankAcconoforCheck = $value->accountNumberFrom;
			$bankNameforCheck = $value->Bank_NickName;
			$i++;
		}
		return view('Accounting.index',['request' => $request,
										'cashDetails' => $cashDetails,
										'cashDetailsIndex' => $cashDetailsIndex,
										'account_period' => $account_period,
										'year_month' => $year_month,
										'db_year_month' => $db_year_month,
										'date_month' => $date_month,
										'dbnext' => $dbnext,
										'dbprevious' => $dbprevious,
										'last_year' => $last_year,
										'current_year' => $current_year,
										'account_val' => $account_val,



									]);
	}

	/**
	*
	* Addedit Page for Cash
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function addedit(Request $request) {

		$bankDetail = Accounting::fetchbanknames();

		$contentDetail = Accounting::fetchcontent();

		return view('Accounting.addedit',['request' => $request,
											'bankDetail' => $bankDetail,
											'contentDetail' => $contentDetail
										]);
	}

	/**
	*
	* Edit Page for Cash
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/22
	*
	*/
	public function cashedit(Request $request) {
		if(!$request->edit_flg){ 
			return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$editData = Accounting::fetchEditData($request);
		$editDataTo = array();
		if ($request->edit_flg == 1) {
			if ($editData[0]->bankIdTo != "" && $editData[0]->bankIdTo != NULL) {
				$editDataTo = Accounting::fetchEditDataTo($request);
				$bankDetail = Accounting::fetchEditbanknames($request,$editDataTo[0]->bankId);
			} else {
				$bankDetail = Accounting::fetchEditbanknames($request);
			}
			$contentDetail = Accounting::fetchEditcontent($request);
		} else {
			$bankDetail = Accounting::fetchbanknames();
			$contentDetail = Accounting::fetchcontent();
			$editData[0]->date = "";
		}
		return view('Accounting.addedit',['request' => $request,
											'bankDetail' => $bankDetail,
											'contentDetail' => $contentDetail,
											'editData' => $editData,
											'editDataTo' => $editDataTo
										]);
	}

	/**
	*
	* Get banck Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function bank_ajax(Request $request) {

		$bankacc = $request->bankacc;
		$getbankDtl = Accounting::fnGetbankName($bankacc);
		$bankarray = json_encode($getbankDtl);
		echo $bankarray;
		exit();

	}

	/**
	*
	* Addedit Process for Cash
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function addeditprocess(Request $request) {
		$insertProcess = "";
		$updateProcess = "";
		if(!$request->edit_flg || $request->edit_flg == "2"){
			if($request->transtype != 3){
				$insertProcess = Accounting::insCashDtls($request);
			} else {
				$maxID = Accounting::getautoincrement();
				$insertProcess = Accounting::insCashreduction($request ,1, $maxID);
				$insertProcess = Accounting::insCashreduction($request,2, $maxID);
			}
		} else {
			if($request->transtype != 3){
				if($request->oldTransType == 3) {
					$delTrans = Accounting::DelCashDtls($request);
				}
				$updateProcess = Accounting::updCashDtls($request);
			} else {
				if($request->oldTransferId != "" && $request->oldTransferId != 0) {
					$maxID = $request->oldTransferId;
					$updateProcess = Accounting::updCashreduction($request ,1, $maxID);
					$updateProcess = Accounting::updCashreduction($request,2, $maxID);
				} else {
					$maxID = Accounting::getautoincrement();
					$updateProcess = Accounting::updCashreduction($request ,1, $maxID);
					$insertProcess = Accounting::insCashreduction($request,2, $maxID);
				}
			}
		}
		
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else if($updateProcess) {
			Session::flash('success', 'Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('success', 'Inserted UnSucessfully!'); 
			Session::flash('type', 'alert-success'); 
		}
		$accDate = explode("-", $request->accDate);
		if (isset($accDate[0])) {
			Session::flash('selYear', $accDate[0]); 
			Session::flash('selMonth', $accDate[1]);
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* Addedit Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function transferaddedit(Request $request) {

		$bankDetail = Accounting::fetchbanknames();
		$mainExpDetail = Accounting::getMainExpName();
		$contentDetail = Accounting::fetchcontent();

		return view('Accounting.transferaddedit',[ 'request' => $request,
													'mainExpDetail' => $mainExpDetail,
													'bankDetail' => $bankDetail,
													'contentDetail' => $contentDetail
												]);
	}



	/**
	*
	* Edit Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/21
	*
	*/
	public function transferedit(Request $request) {

		if(!$request->edit_flg){ 
			return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$transferEdit = array();
		$transferEdit = Accounting::tranferEditData($request);
		if (isset($transferEdit[0]->emp_ID)) {
			$empName = Accounting::fnGetEmpName($transferEdit[0]->emp_ID);
			if (isset($empName[0]->FirstName) && isset($empName[0]->LastName)) {
				$transferEdit[0]->Empname = $empName[0]->FirstName.' '.$empName[0]->LastName;
			}
		}
		if ($request->edit_flg == 1) {
			$bankDetail = Accounting::fetchEditbanknames($request);
			$contentDetail = Accounting::fetchEditcontent($request);
		} else {
			$bankDetail = Accounting::fetchbanknames();
			$contentDetail = Accounting::fetchcontent();
			$transferEdit[0]->date = "";
			$transferEdit[0]->fileDtl = "";
			$transferEdit[0]->Empname = "";
			if ($transferEdit[0]->content == 'Salary') {
				$transferEdit[0]->subjectId = "";
			}
		}
		
		$mainExpDetail = Accounting::getMainExpName();
		return view('Accounting.transferaddedit',[ 'request' => $request,
													'mainExpDetail' => $mainExpDetail,
													'bankDetail' => $bankDetail,
													'contentDetail' => $contentDetail,
													'transferEdit' => $transferEdit
												]);
	}

	/**
	*
	* Addedit Process for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function tranferaddeditprocess(Request $request) {

		if(!$request->edit_flg || $request->edit_flg == "2"){

			$autoincId = Accounting::getautoincrement();
			$Transferno = "Transfer_".$autoincId;
			$fileName = "";
			$fileid = "transferBill";

			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $Transferno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/Accounting';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}

			$insertProcess = Accounting::insTransferDtls($request,$fileName);
			
			if($insertProcess) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {

			$autoincId = Accounting::getautoincrement();
			$Transferno = "Transfer_".$autoincId;
			$fileName = "";
			$fileid = "transferBill";

			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $Transferno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/Accounting';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}

			$updateProcess = Accounting::updateTransferDtls($request,$fileName);

			if($updateProcess) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		$accDate = explode("-", $request->accDate);
		if (isset($accDate[0])) {
			Session::flash('selYear', $accDate[0]); 
			Session::flash('selMonth', $accDate[1]);
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Emp Name Popup Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function empnamepopup(Request $request) {

		$salPaid = array();
		$empIdArr = array();
		if ($request->transferDate != "") {
			$salPaid = Accounting::getsalaryPaid($request->transferDate);
			for ($i = 0; $i < count($salPaid) ; $i++) { 
				$empIdArr[$i] = $salPaid[$i]->emp_ID;
			}
		}

		$empname = Accounting::fnGetEmpDetails($request,$empIdArr);
		$empnamenonstaff = Accounting::fnGetNonstaffEmpDetails($request,$empIdArr);

		return view('Invoice.empnamepopup',['request' => $request,
											'empname' => $empname,
											'empnamenonstaff' => $empnamenonstaff
										]);
	}

	/**
	*
	* Salary Details Popup Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function getsalarypopup(Request $request) {

		$getBankDtls = array();
		$salPaid = array();
		if(Session::get('languageval') == "en") {
			$mainSub = Accounting::subjectName('Salary',1);
		} else {
			$mainSub = Accounting::subjectName('給料',2);
		}
		$SubIdVal = "";
		if ($mainSub != array()) {
			$SubId = array_keys($mainSub);
			$SubIdVal = $SubId[0];
		}
		if ($request->transferDate != "") {
			$getBankDtls = Accounting::fetchbanknames($request);
			$salPaid = Accounting::getsalaryPaid($request->transferDate);
		}

		$getSalaryDtls = array();
		$SalaryDtls = array();
		$already = 0;
		$salary_det = Accounting::getsalaryDetailsnodelflg($request,1);
		$salary_ded = Accounting::getsalaryDetailsnodelflg($request,2);
		$empIdArr = array();
		for ($i=0; $i < count($salPaid) ; $i++) { 
			$empIdArr[$i] = $salPaid[$i]->emp_ID;
		}

		if ($request->transferDate != "") {
			$getSalaryDtls = Accounting::getSalaryDtls($request ,$empIdArr);
			foreach ($getSalaryDtls as $key => $value) {
				$SalaryEmpName = Accounting::fnGetEmpName($value->Emp_ID);
				$SalaryDtls[$value->Emp_ID]['empName'] = $SalaryEmpName[0]->LastName;

				// SalaryPlus Salary Details
				$arr1 = array();
				$arr2 = array();
				$salArr = array();
				$salary = "";
				if ($value->Salary != "") {
					$salaryVal = explode('##', mb_substr($value->Salary, 0, -2));
					foreach ($salaryVal as $salKey => $salVal) {
						$salFinal = explode('$', $salVal);
						$arr1[$key] = $salFinal[0];
						$arr2[$salFinal[0]] = $salFinal[1];
					}
				}
				foreach ($salary_det as $key1 => $det) {
					$salArr[$det->Salarayid] = $det->Salarayid;
				}
				$salresult_a = array_intersect($salArr,$arr1);
				$salresult_b = array_diff($salArr,$arr1);
				$salresult = array_merge($salresult_a,$salresult_b);
				ksort($salresult);
				if(count($salary_det) != "" && is_array($salresult)) {
					$x = 0;
					foreach ($salresult as $key2 => $value2) {
						if ($key2 != '') {
							if($key2 == isset($arr2[$key2])) {
								$salary += $arr2[$key2];
							} 
						} 
						$x++;
					}
				}
				$SalaryDtls[$value->Emp_ID]['Salary'] = $salary;

				// SalaryPlus Deduction Details
				$arr3 = array();
				$arr4 = array();
				$dedArr = array();
				$deduction = "";
				if ($value->Deduction != "") {
					$deductionVal = explode('##', mb_substr($value->Deduction, 0, -2));
					foreach ($deductionVal as $dedKey => $dedVal) {
						$dedFinal = explode('$', $dedVal);
						$arr3[$dedKey] = $dedFinal[0];
						$arr4[$dedFinal[0]] = $dedFinal[1];
					}
				}
				foreach ($salary_ded as $key3 => $value3) {
					$ded_arr[$value3->Salarayid] = $value3->Salarayid;
				}
				$dedresult_a = array_intersect($ded_arr,$arr3);
				$dedresult_b = array_diff($ded_arr,$arr3);
				$dedresult = array_merge($dedresult_a,$dedresult_b);
				ksort($dedresult);
				if(count($salary_ded)!="") {
					$y = 0;
					foreach ($dedresult as $key4 => $value4) {
						if ($key4 != '') {
							if($key4 == isset($arr4[$key4])) {
								$deduction += $arr4[$key4];
							}
						}
						$y++;
					}
				}
				$SalaryDtls[$value->Emp_ID]['Deduction'] = $deduction;

				$SalaryDtls[$value->Emp_ID]['Amount'] = $salary + $deduction + $value->Travel;
			}
		}
		return view('Accounting.salarydetailspopup',['request' => $request,
													'mainSub' => $mainSub,
													'SubIdVal' => $SubIdVal,
													'getBankDtls' => $getBankDtls,
													'getSalaryDtls' => $getSalaryDtls,
													'SalaryDtls' => $SalaryDtls
													]);
	}

	/**
	*
	* Addedit Page for AutoDebit
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function autoDebitReg(Request $request) {

		$bankDetail = Accounting::fetchbanknames();
		$mainExpDetail = Accounting::getMainExpName();
		$contentDetail = Accounting::fetchcontent();

		return view('Accounting.autoDebitReg',['request' => $request,
												'bankDetail' => $bankDetail,
												'contentDetail' => $contentDetail,
												'mainExpDetail' => $mainExpDetail
											]);
		
	}

	/**
	*
	* Edit Page for Autodebit
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/10/22
	*
	*/
	public function autoDebitedit(Request $request) {

		if(!$request->edit_flg){ 
			return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$autodebitEdit = array();
		$autodebitEdit = Accounting::fetchEditData($request);
		if ($request->edit_flg == 1) {
			$bankDetail = Accounting::fetchEditbanknames($request);
			$contentDetail = Accounting::fetchEditcontent($request);
		} else {
			$bankDetail = Accounting::fetchbanknames();
			$contentDetail = Accounting::fetchcontent();
			$autodebitEdit[0]->date = "";
			$autodebitEdit[0]->loan_ID = "";
			$autodebitEdit[0]->loanName = "";
			$autodebitEdit[0]->fileDtl = "";
			$autodebitEdit[0]->remarks = "";
		}
		$mainExpDetail = Accounting::getMainExpName();
		return view('Accounting.autoDebitReg',[ 'request' => $request,
												'mainExpDetail' => $mainExpDetail,
												'bankDetail' => $bankDetail,
												'contentDetail' => $contentDetail,
												'autodebitEdit' => $autodebitEdit
											]);
	}

	/**
	*
	* Addedit Process for Auto Debit
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/10/22
	*
	*/
	public function AutoDebitRegprocess(Request $request) {
		if(!$request->edit_flg || $request->edit_flg == "2"){
			$autoincId = Accounting::getautoincrement();
			$Transferno = "AutoDebit_".$autoincId;
			$fileName = "";
			$fileid = "autoDebitBill";
			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $Transferno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/Accounting';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}
			$insertProcess = Accounting::insAutoDebitDtls($request,$fileName);
			if($insertProcess) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$autoincId = Accounting::getautoincrement();
			$Transferno = "AutoDebit_".$autoincId;
			$fileName = "";
			$fileid = "autoDebitBill";
			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$fileName = $Transferno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../AccountingUpload/Accounting';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, 0777,true);
				}
				$file->move($destinationPath,$fileName);
			} else {
				$fileName = $request->pdffiles;
			}
			$updateProcess = Accounting::updateAutodebitDtls($request,$fileName);

			if($updateProcess) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		$accDate = explode("-", $request->accDate);
		if (isset($accDate[0])) {
			Session::flash('selYear', $accDate[0]); 
			Session::flash('selMonth', $accDate[1]);
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Loan Details Popup Page for AutoDebit
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function getloanpopup(Request $request) {

		$getLoanDtls = array();
		$getBankDtls = array();
		$loanBankId = array();
		$loanPaidArr = array();
		if(Session::get('languageval') == "en") {
			$mainSub = Accounting::subjectName('Loan',1);
		} else {
			$mainSub = Accounting::subjectName('ローン',2);
		}
		$SubIdVal = "";
		if ($mainSub != array()) {
			$SubId = array_keys($mainSub);
			$SubIdVal = $SubId[0];
		}
		$getUserDtls = Accounting::getUserDtls($request);
		if (count($getUserDtls) == 1) {
			$userId = array_keys($getUserDtls);
			$request->userId = $userId[0];
		}
		$getBelongsToDtls = Accounting::getBelongsToDtls($request);
		// print_r($request->all());exit();
		if ($request->autoDebitDate != "" && $request->userId != "") {
			$getBankDtls = Accounting::fetchbanknames($request);
			$getLoanPaid = Accounting::getLoanPaid($request,1);
			for ($i = 0; $i <count($getLoanPaid) ; $i++) { 
				$loanPaidArr[$i] = $getLoanPaid[$i]->loan_ID;
			}
			$getLoanDtls = Accounting::getLoanDtls($request,$loanPaidArr);
			foreach ($getLoanDtls as $loankey => $loanval) {
				$loanBank = Accounting::getLoanBank($request,$loanval->loanId);
				if (isset($loanBank[0]->ID)) {
					$loanBankId[$loanval->loanId]['bankId'] = $loanBank[0]->ID;
					$loanBankId[$loanval->loanId]['bankName'] = $loanBank[0]->BANKNAME;
				}
			}
		}
		return view('Accounting.loandetailspopup',['request' => $request,
													'mainSub' => $mainSub,
													'SubIdVal' => $SubIdVal,
													'getUserDtls' => $getUserDtls,
													'getBelongsToDtls' => $getBelongsToDtls,
													'getLoanDtls' => $getLoanDtls,
													'getBankDtls' => $getBankDtls,
													'loanBankId' => $loanBankId
													]);
	}

	public function getcashDetails(Request $request){ 
		$fromDate =$request->selYear.'-'.$request->selMonth.'-01';
		$toDate = $request->selYear .'-'.$request->selMonth.'-'.Common::fnGetMaximumDateofMonth($fromDate);

		$getBankDtls = Accounting::fetchcashRegisterPopup($fromDate ,$toDate,$request);

		return view('Accounting.orderChangepopup',['request' => $request,
													'getBankDtls' => $getBankDtls,
													]);
	}

	/**  
	*  For Commit Process
	*  @author RAjesh 
	*  @param $request
	*  Created At 2020/10/23
	**/
	public function commitProcess(Request $request) {
		$commit = Accounting::fngetcommitProcess($request);
	}
	
	/**  
	*  For Commit Process
	*  @author RAjesh 
	*  @param $request
	*  Created At 2020/10/01
	**/
	public function getInvoicePopup(Request $request) {
		$TotEstquery = array();
		$inv = array();
		$invbal = array();
		$invoicePaidArr = array();
		$totalval = 0;
		$grandtotal = 0;
		$balance = 0;
		$paid_amo = 0;
		$divtotal = 0;
		$paid_amount = 0;
		$bal_amount = 0;
        $balance_style = "";
        $grand_style = "";
        if(Session::get('languageval') == "en") {
			$mainSub = Accounting::subjectName('Invoice',1);
		} else {
			$mainSub = Accounting::subjectName('請求書',2);
		}
        $SubIdVal = "";
		if ($mainSub != array()) {
			$SubId = array_keys($mainSub);
			$SubIdVal = $SubId[0];
		}
		if ($request->invoiceDate != "") {
			$getInvoicePaid = Accounting::getLoanPaid($request,2);

			for ($i = 0; $i < count($getInvoicePaid) ; $i++) {
				$invoicePaidArr[$i] = $getInvoicePaid[$i]->loan_ID;
			}

			$TotEstquery = Accounting::fetchinvoicePopup($request,$invoicePaidArr);
			$i = 0;
			foreach ($TotEstquery as $key => $value) {
				$inv[$i]['id'] = $value->id;
				$i++;
			}
			for ($k = 0; $k < count($inv); $k++) { 
				$query = Accounting::fnGetBalanceDetails($inv[$k]['id']);
				if(!empty($query)) {
					$split = explode(",", $query[0]->paid_id);
					for ($y = 0; $y < count($inv); $y++) {
						if (end($split) == (isset($inv[$y]['id']) ? $inv[$y]['id'] : "")) {
							$invbal[$y]['bal_amount'] = str_replace("," , "" , $query[0]->totalval);
						}
					}
				}
			}
		}
		$getBankDtls = Accounting::fetchbanknames($request);

		
		
		return view('Accounting.invoicedetailspopup',['request' => $request,
													'mainSub' => $mainSub,
													'SubIdVal' => $SubIdVal,
													'TotEstquery' => $TotEstquery,
													'invbal' => $invbal,
													'grandtotal' => $grandtotal,
													'totalval' => $totalval,
													'paid_amo' => $paid_amo,
													'divtotal'=> $divtotal,
													'paid_amount'=> $paid_amount,
                                				    'balance_style' => $balance_style,
                                				    'grand_style' => $grand_style,
													'bal_amount'=> $bal_amount,
													'getBankDtls'=> $getBankDtls,
													]);
	}

	/**
	*
	* Addedit Process for Auto Debit
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/10/22
	*
	*/
	public function invoiceaddeditprocess(Request $request) {
		$insertProcess = Accounting::insInvoiceDtls($request);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		$accDate = explode("-", $request->accDate);
		if (isset($accDate[0])) {
			Session::flash('selYear', $accDate[0]); 
			Session::flash('selMonth', $accDate[1]);
		}
		
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Expenses Date Details Popup Page for Transfer
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/02/08
	*
	*/
	public function getExpensespopup(Request $request) {

		$getBankDtls = array();
		$expensesData = array();
		$expData = array();
		$i = 1;
		
		$getBankDtls = Accounting::fetchExpensesBank($request);
		
		if ($request->expensesDate != "" && $request->bankIdAccNo != "") {
			$expensesData = Accounting::fetchExpensesData($request);
			foreach ($expensesData as $expkey => $expvalue) {
				$empName = Accounting::fnGetEmpName($expvalue->empId);
				if (isset($empName[0]->FirstName) && isset($empName[0]->LastName)) {
					$expData[$i]['empName'] = $empName[0]->FirstName.' '.$empName[0]->LastName;
				}
				$i++;
			}
		}

		return view('Accounting.expensesDatepopup',['request' => $request,
													'getBankDtls' => $getBankDtls,
													'expensesData' => $expensesData,
													'expData' => $expData
												]);
	}

	/**
	*
	* Addedit Process for Expenses Data
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/02/09
	*
	*/
	public function expensesDataaddeditprocess(Request $request) {
		$insertProcess = Accounting::insexpensesData($request);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		$accDate = explode("-", $request->accDate);
		if (isset($accDate[0])) {
			Session::flash('selYear', $accDate[0]); 
			Session::flash('selMonth', $accDate[1]);
		}
		
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}


	/**  
	*  For Confirmed and Not Confirmed Process
	*  @author Sastha 
	*  @param $request
	*  Created At 2021/02/03
	**/

	public function completedflg(Request $request) {
		$completedflg = Accounting::completedflg($request);
		Session::flash('selYear', $request->selYear); 
		Session::flash('selMonth', $request->selMonth);
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**  
	*  For Confirmed Bank Exists Process
	*  @author Sastha 
	*  @param $request
	*  Created At 2021/02/05
	**/

	public function dateBankExists(Request $request){
		$dateBankExists = Accounting::fnGetdateBankExists($request);
		if (isset($dateBankExists[0])) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}
		
	}

	/**
	*
	* Excel Download Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/10
	*
	*/
	public function accExlwnld(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/accounting_details.xlsx';
		if ($request->selYear == "") {
			$request->selYear = date('Y');
		}
		if ($request->selMonth == "") {
			$request->selMonth = date('m');
		}
		$excel_name = 'Accounting_'.$request->selYear.'_'.$request->selMonth;

		Excel::load($template_name, function($objPHPExcel) use($request) {

			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			$start = $request->selYear .'-'.$request->selMonth.'-01';
			$end = $request->selYear .'-'.$request->selMonth.'-'.Common::fnGetMaximumDateofMonth($start);

			$cashDetails =array();
			$i = 4;
			$j = 1;
			$bankAcconoforCheck = "";
			$bankNameforCheck = "";
			$lastBankAccno = "";
			$lastBankName = "";
			$preBankAccno = "";
			$preBankName = "";
			$debitTotal = 0;
			$creditTotal = 0;
			$debitAmt = 0;
			$creditAmt = 0;
			$TotalDebit = 0;
			$TotalCredit = 0;
			$realBalanceAmount = 0;

			$cashDetailsIndex = Accounting::fetchcashRegister($start,$end,$request,1);

			foreach ($cashDetailsIndex as $key => $value) {

				$pagecashSubject = "";
				$details = "";
				$debitAmount = "";
				$creditAmount = "";
				$baseAmt = Accounting::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
				if($value->pageFlg == 1 || $value->pageFlg == 999) {
					$pagecashSubject = $value->loanName;
				}
				if (($value->pageFlg == 2 || $value->pageFlg == 5) && $value->emp_ID != "") {
					$empIdArr[0] = $value->emp_ID;
					$empname = Accounting::fnGetEmpName($value->emp_ID);
					if (isset($empname[0]->LastName)) {
						$name = $empname[0]->LastName;
					} else {
						$name ="";
					}
					$details = $value->emp_ID.'-'.$name;
				} elseif ($value->pageFlg == 4 && $value->loan_ID != "") {
					$empIdArr[0] = $value->loan_ID;
					$details = $value->loan_ID.'-'.$value->loanName;
				} elseif ($value->pageFlg == 3 && $value->loan_ID != "") {
					$empIdArr[0] = $value->loan_ID;
					$details = $value->loanName;
				}

				$balanceAmtonDownTr = 0;
				$curBal = 0;

				$checkPrevious = Accounting::fnGetRecordPreviousForAmountCheck(substr($start, 0, 7) ,$value->bankIdFrom,$value->accountNumberFrom);

				if (isset($baseAmt[0]->amount)) {
					$cashDetails[$i]['startDate'] = $baseAmt[0]->date;
					$cashDetails[$i]['baseAmt'] = $baseAmt[0]->amount;

					if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
						$curBal = $baseAmt[0]->amount;

						if (empty($checkPrevious)) {

							if ($bankAcconoforCheck != $value->accountNumberFrom || $bankNameforCheck != $value->Bank_NickName) {
								$curBal = $baseAmt[0]->amount;
							}

						} else {
							$prYrMn = date("Y-m", strtotime("-1 months", strtotime($date_month)));
							$prevBalanceAmt = Accounting::AccBalance($value->bankIdFrom,$value->AccNo,$baseAmt[0]->date,$prYrMn);
						
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
				}

				$bankAcconoforCheck = $value->accountNumberFrom;
				$bankNameforCheck = $value->Bank_NickName;
				if($preBankAccno != "" && ($preBankAccno != $value->accountNumberFrom 
					|| $preBankName != $value->Bank_NickName)){
					// $objPHPExcel->getActiveSheet()->setCellValue("A".$i,'合計');
					// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
					// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					// $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
					// $objPHPExcel->getActiveSheet()->setCellValue("E".$i,$debitTotal);
					// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->setWrapText(true);
					// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// $objPHPExcel->getActiveSheet()->setCellValue("F".$i,$creditTotal);
					// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->setWrapText(true);
					// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					// $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// $objPHPExcel->getActiveSheet()->setCellValue("G".$i,$realBalanceAmount);
					// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
					// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					// $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$realBalanceAmount = 0;
					$debitTotal = 0;
					$creditTotal = 0;
					$i = $i + 1;

				}
				if($lastBankAccno != $value->accountNumberFrom || $lastBankName != $value->Bank_NickName) { 
					$realBalanceAmount = $curBal;
					$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$value->Bank_NickName."-".$value->accountNumberFrom);
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f1a2a2');
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
					// $objPHPExcel->getActiveSheet()->setCellValue("G".$i,$curBal);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f1a2a2');
					$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$i = $i + 1;
				}
				$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$j);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->setCellValue("B".$i,$value->date);
				$objPHPExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("B".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("B".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if($value->pageFlg == 1) {
					$subject = $pagecashSubject;
				} elseif($value->Subject != "" && Session::get('languageval') == "en") {
					$subject = $value->Subject;
				} elseif($value->Subject_jp != "" && Session::get('languageval') == "jp") {
					$subject = $value->Subject_jp;
				} else {
					$subject = $value->content;
				}
				$objPHPExcel->getActiveSheet()->setCellValue("C".$i,$subject);
				$objPHPExcel->getActiveSheet()->getStyle("C".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("C".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("C".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if($value->pageFlg == 999) {
					$content = $pagecashSubject;
				} else {
					if(($value->pageFlg == 2 || $value->pageFlg == 5) && $value->emp_ID != "") {
						$content = $details;
					} elseif(($value->pageFlg == 3 || $value->pageFlg == 4) && $value->loan_ID != "") {
						$content = $details;
					} else {
						$content = $value->content; 
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue("D".$i,$content);
				$objPHPExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("D".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("D".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if($value->transcationType == 1) {
					$debitAmount = $value->amount;
					$debitAmt = $value->amount + $value->fee;
					$debitTotal = $debitTotal + $debitAmt;
					$TotalDebit = $TotalDebit + $debitAmt;
					$objPHPExcel->getActiveSheet()->setCellValue("E".$i, number_format($debitAmount));
				}
				
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if($value->transcationType == 2 || $value->transcationType == 4) {
					$creditAmount = $value->amount;
					$creditAmt = $value->amount + $value->fee;
					$creditTotal = $creditTotal + $creditAmt;
					$TotalCredit = $TotalCredit + $creditAmt;
					$objPHPExcel->getActiveSheet()->setCellValue("F".$i, number_format($creditAmount));
				}
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->setCellValue("G".$i,$value->remarks);
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CEECF5');
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if ($value->fee != "") {
					$i = $i + 1;
					if ($value->pageFlg == 3) {
					$objPHPExcel->getActiveSheet()->setCellValue("A".$i,'金利');
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue("A".$i,'手数料');
					}
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
					$objPHPExcel->getActiveSheet()->setCellValue("E".$i, number_format($value->fee));
					$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// $objPHPExcel->getActiveSheet()->setCellValue("G".$i, $value->amount+ $value->fee);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
					$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				}
				if($value->transcationType == 1) {
					$realBalanceAmount = $realBalanceAmount - $debitAmt;
				} else {
					$realBalanceAmount = $realBalanceAmount + $creditAmt;
				}
				$lastBankAccno = $value->accountNumberFrom;
				$lastBankName = $value->Bank_NickName;
				$preBankAccno = $value->accountNumberFrom;
				$preBankName = $value->Bank_NickName;
				$i++;
				$j++;
			}
			if(count($cashDetailsIndex) != 0) {
				// $objPHPExcel->getActiveSheet()->setCellValue("A".$i,'合計');
				// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
				// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				// $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				// $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				// $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
				// $objPHPExcel->getActiveSheet()->setCellValue("E".$i,$debitTotal);
				// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->setWrapText(true);
				// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				// $objPHPExcel->getActiveSheet()->getStyle("E".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				// $objPHPExcel->getActiveSheet()->setCellValue("F".$i,$creditTotal);
				// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->setWrapText(true);
				// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				// $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				// $objPHPExcel->getActiveSheet()->getStyle("F".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				// $objPHPExcel->getActiveSheet()->setCellValue("G".$i,$realBalanceAmount);
				// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
				// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				// $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				// $objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$i = $i + 2;
				$objPHPExcel->getActiveSheet()->setCellValue("A".$i,'合計');
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue("E".$i,number_format($TotalDebit));
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle("E".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$i,number_format($TotalCredit));
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle("F".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				// $objPHPExcel->getActiveSheet()->setCellValue("G".$i,'');
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle("G".$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
			$objPHPExcel->setActiveSheetIndex();
			$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
		
		})->setFilename($excel_name)->download('xlsx');
	}
}