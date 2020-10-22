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
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use Carbon;
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
	
		$cashDetailsIndex = Accounting::fetchcashRegister($start, $end, $request);
		$cashDetails =array();
		$i = 0;
		foreach ($cashDetailsIndex as $key => $value) {
			$cashDetails[$i]['id'] = $value->id;
			$cashDetails[$i]['date'] = $value->date;
			$cashDetails[$i]['content'] = $value->content;
			$cashDetails[$i]['amount'] = $value->amount;
			$cashDetails[$i]['fee'] = $value->fee;
			$cashDetails[$i]['Bank_NickName'] = $value->Bank_NickName;
			$cashDetails[$i]['transcationType'] = $value->transcationType;
			$cashDetails[$i]['remarks'] = $value->remarks;
			$cashDetails[$i]['baseAmt'] = 0;
			$cashDetails[$i]['bankId'] = $value->bankId;
			$cashDetails[$i]['fileDtl'] = $value->fileDtl;
			$cashDetails[$i]['transferId'] = $value->transferId;
			$cashDetails[$i]['pageFlg'] = $value->pageFlg;
			$baseAmt = Accounting::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
			$cashDetails[$i]['subId'] = $value->subjectId;
			$cashDetails[$i]['subject'] = $value->Subject;

			if (isset($baseAmt[0]->amount)) {
				$cashDetails[$i]['baseAmt'] = $baseAmt[0]->amount;
			}
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

	// public function index(Request $request) {
	// 	if(Session::get('selYear') !="") {
	// 		$request->selYear =  Session::get('selYear');
	// 		$request->selMonth =  Session::get('selMonth');
	// 		// $request->date =  Session::get('date');
	// 		// $request->amount =  Session::get('amount');
	// 	}


	// 	$cashDetailsIndex = Accounting::fetchcashRegister();
	// 	$cashDetails =array();
	// 	$i = 0;
	// 	foreach ($cashDetailsIndex as $key => $value) {
	// 		$cashDetails[$i]['id'] = $value->id;
	// 		$cashDetails[$i]['date'] = $value->date;
	// 		$cashDetails[$i]['content'] = $value->content;
	// 		$cashDetails[$i]['amount'] = $value->amount;
	// 		$cashDetails[$i]['fee'] = $value->fee;
	// 		$cashDetails[$i]['Bank_NickName'] = $value->Bank_NickName;
	// 		$cashDetails[$i]['transcationType'] = $value->transcationType;
	// 		$cashDetails[$i]['remarks'] = $value->remarks;
	// 		$cashDetails[$i]['baseAmt'] = 0;
	// 		$cashDetails[$i]['bankId'] = $value->bankId;
	// 		$cashDetails[$i]['fileDtl'] = $value->fileDtl;
	// 		$cashDetails[$i]['transferId'] = $value->transferId;
	// 		$cashDetails[$i]['pageFlg'] = $value->pageFlg;
	// 		$baseAmt = Accounting::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
	// 		$cashDetails[$i]['subId'] = $value->subjectId;
	// 		$cashDetails[$i]['subject'] = $value->Subject;

	// 		if (isset($baseAmt[0]->amount)) {
	// 			$cashDetails[$i]['baseAmt'] = $baseAmt[0]->amount;
	// 		}
	// 		$i++;
	// 	}
	// 	return view('Accounting.index',['request' => $request,
	// 									'cashDetails' => $cashDetails,
	// 									'cashDetailsIndex' => $cashDetailsIndex]);
	// }

	/**
	*
	* Addedit Page for Cash
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function addedit(Request $request) {

		$editData =array();
		if($request->edit_flg) {
			$editData = Accounting::fetchEditData($request);
		}
		$bankDetail = Accounting::fetchbanknames();

		return view('Accounting.addedit',['request' => $request,
											'bankDetail' => $bankDetail,
											'editData' => $editData
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
		if(!$request->edit_flg){
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
		
		if($insertProcess || $updateProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
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

		return view('Accounting.transferaddedit',[ 'request' => $request,
													'mainExpDetail' => $mainExpDetail,
													'bankDetail' => $bankDetail
												]);
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

		$empname = Accounting::fnGetEmpDetails($request);
		$empnamenonstaff = Accounting::fnGetNonstaffEmpDetails($request);

		return view('Invoice.empnamepopup',['request' => $request,
											'empname' => $empname,
											'empnamenonstaff' => $empnamenonstaff
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
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
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

		$getSalaryDtls = array();
		$SalaryDtls = array();

		$salary_det = Accounting::getsalaryDetailsnodelflg($request,1);
		$salary_ded = Accounting::getsalaryDetailsnodelflg($request,2);
		
		if ($request->transferDate != "") {
			$getSalaryDtls = Accounting::getSalaryDtls($request);

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

		return view('Accounting.autoDebitReg',['request' => $request,
												'bankDetail' => $bankDetail,
												'mainExpDetail' => $mainExpDetail
											]);
		
	}

	/**
	*
	* Addedit Process for Auto Debit
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function AutoDebitRegprocess(Request $request) {

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
			$fileName = ""; 
		}
		$insertProcess = Accounting::insAutoDebitDtls($request,$fileName);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
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
		$getUserDtls = Accounting::getUserDtls($request);
		if ($request->autoDebitDate != "" && $request->userId != "") {
			$getLoanDtls = Accounting::getLoanDtls($request);
		}

		return view('Accounting.loandetailspopup',['request' => $request,
													'getUserDtls' => $getUserDtls,
													'getLoanDtls' => $getLoanDtls
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
		if ($request->edit_flg == 2) {
			$transferEdit[0]->date = "";
		}
		$bankDetail = Accounting::fetchbanknames();
		$mainExpDetail = Accounting::getMainExpName();
		return view('Accounting.transferaddedit',[ 'request' => $request,
													'mainExpDetail' => $mainExpDetail,
													'bankDetail' => $bankDetail,
													'transferEdit' => $transferEdit
												]);
	}

}