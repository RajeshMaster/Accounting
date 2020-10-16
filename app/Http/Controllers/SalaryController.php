<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Common;
use App\Model\Salary;
use DB;
use Input;
use Redirect;
use Config;
use Session;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller {
	function index(Request $request) {
		if(Session::get('datemonth') !="") {
			$date_month = Session::get('datemonth');
			$splitPrevious = explode("-", $date_month);
			$request->selYear = $splitPrevious[0];
			$request->selMonth = $splitPrevious[1];
		} else if(Session::get('multiflg') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		$db_year_month = array();
		$year_monthslt = "";
		$get_det = array();
		$exp_rsTotalAmount = 0;
		$k = 0;
		$i = 0;
		$reg = 0;
		$rowclr="";
		$lastyear="";
		$lastmonth="";
		$pettyCash="";
		$ExpensesDetails="";
		$totalYenColor="";
		$gett = "";
		$rsTotalAmount = 0;
		$tempvar="";
		$PAGING=0;
		$incr="";
		$serialcolor="";
		$future_date="";
		$updated_date="";
		$db_updated_date="";
		$registered_date="";
		$db_inserted_date="";
		$saltotal = "";
		$chartotal = "";
		$today_date = date('Y-m-d');
		$salarysortarray = [$request->salarysort=>$request->salarysort,
                    'salaryDate'=> trans('messages.lbl_Date'),
                    'LastName'=> trans('messages.lbl_empName'),
                    'Emp_ID'=> trans('messages.lbl_empid'),
                    'salary'=> trans('messages.lbl_salary')];
        $srt = $request->salarysort;
   		$odr = $request->sortOrder;
        if ($request->salarysort == "") {
        	$request->salarysort = "Emp_ID";
      	}
		if (empty($request->sortOrder)) {
        	$request->sortOrder = "ASC";
      	}
      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$temp_countcheck = Salary::getTempDetailscheck($request);
		if ($temp_countcheck == 0) {
			$temp_checkcount = Salary::getEmpDetailsinitialId($request);
		}
		//START PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$temp_count = Salary::getTempDetails($request);
		if ($temp_count == 0) {
			$empdetails = Salary::getEmpDetailsId($request);
		}
		//END PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		if (!isset($request->selMonth)) {
			$date_month=date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
		$last=date('Y-m', strtotime('last month'));
		$last1=date($date_month , strtotime($last . " last month"));
		$lastdate=explode("-",$last1);
		$lastyear=$lastdate[0];
		$lastmonth=$lastdate[1];
		$g_accountperiod=Salary::fnGetAccountPeriodSal($request);
		$account_close_yr=$g_accountperiod[0]->Closingyear;
		$account_close_mn=$g_accountperiod[0]->Closingmonth;
		$account_period=intval($g_accountperiod[0]->Accountperiod);
		
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
		} elseif (isset($request->selYear)) {
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
		$est_query=Salary::fnGetBKRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->salaryDate;
		}
		$est_query1=Salary::fnGetbkrsRecordPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->salaryDate, 0, 4);
			$dbprevious[]=$value->salaryDate;
			$pre++;
		}
		$est_query2=Salary::fnGetbkrsRecordNext($to_date);
		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->salaryDate;
		}
		//START PREVIOUS AND FUTURE MONTH LINK WITHOUT DATA IN THE DB
		$fu_date = date('Y')."-0".(date('m')+1);
		$pre_date = date('Y')."-0".(date('m')-1);
		$cur_date = date('Y')."-0".(date('m'));
		if (!in_array($pre_date, $dbrecord)) {
			array_push($dbrecord, $pre_date);
		}
		if (!in_array($cur_date, $dbrecord)) {
			array_push($dbrecord, $cur_date);
		}
		if (!in_array($fu_date, $dbrecord)) {
			array_push($dbrecord, $fu_date);
		}
		$dbrecord = array_unique($dbrecord);
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-",$dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}
		//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH
		if (empty($request->account_val)) {
			$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		} else {
			$account_val = $request->account_val;
		}
		$g_query=Salary::getsalaryDetail($request,$lastyear,$lastmonth);
		foreach ($g_query as $key => $value) {
			$get_det[$k]['id'] = $value->id;
			$get_det[$k]['empNo'] = $value->Emp_ID;
			$get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName))
				. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
			$get_det[$k]['salaryDate'] = $value->salaryDate;
			$get_det[$k]['salaryMonth'] = $value->salaryMonth;
			if ($value->salaryMonth == "" ) {
					$reg =1;
				}
			$saltotal += str_replace(",", "", $value->salary);
			$chartotal += str_replace(",", "", $value->charge);
			$get_det[$k]['salary'] = $value->salary;
			$get_det[$k]['charge'] = $value->charge;
			$get_det[$k]['BankName'] = $value->BankName;
			$get_det[$k]['bankId'] = $value->bankId;
			$get_det[$k]['accountNo'] = $value->AccNo;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$get_det[$k]['submit_flg'] = $value->submit_flg;
			$get_det[$k]['edit_flg'] = $value->edit_flg;
			$k++;
		}
		$fileCnt=count($get_det);
		$g_query2 = Salary::detbankinsalaryDetail($lastyear,$lastmonth);
		$bank = array();
		$salcou = array();
		foreach ($g_query2 as $key => $value) {
			$get_bankId[$i]['bankId'] = $value->bankId;
			$get_bankId[$i]['salaryDate'] = $value->salaryDate;
			// if (!in_array($get_bankId[$i]['bankId'],$bank)) {
			// 	array_push($bank, $get_bankId[$i]['bankId']);
			// }
			$i++;
		}
		for ($i=0; $i <count($bank) ; $i++) {
			if(isset($get_bankId[$i]) || isset($bankdetails[$i]) || isset($rowval) || isset($lastyear) || isset($lastmonth)) {
				$bankval = $bank[$i];
				if ($bankval == "999") {
					$rowval = Salary::detbankcalculationsalaryDetail($lastyear,$lastmonth,$bankval);
					$bankcount = Salary::detbank($lastyear,$lastmonth);
					if ($bankcount < 1) {
						$bankdetainsert =Salary::salarytoexpReg($get_bankId[$i]['salaryDate'],$rowval[$i]->sal,$rowval[$i]->charg,$lastyear,$lastmonth,$bankdetails[$i]->AccNo,$bankdetails[$i]->BankName);
					} else {
						$bankdetainsert =Salary::salarytoexpUpd($get_bankId[$i]['salaryDate'],$rowval[$i]->sal,$rowval[$i]->charg,$lastyear,$lastmonth,$bankdetails[$i]->AccNo,$bankdetails[$i]->BankName);
					}
				} else {
					$bankdetails = Salary::detfetch($bankval);
					$rowval = Salary::detbankcalculationsalaryDetail($lastyear,$lastmonth,$bankval);
					$bankcount = Salary::detfetchbanktransfer($bankdetails[$i]->AccNo,$bankdetails[$i]->BankName,$lastyear,$lastmonth);
					if ($bankcount < 1) {
						$bankdetainsert =Salary::bankdetinsert($get_bankId[$i]['salaryDate'],$rowval[$i]->sal,$rowval[$i]->charg,$lastyear,$lastmonth,$bankdetails[$i]->AccNo,$bankdetails[$i]->BankName);
					} else {
						$bankdetainsert =Salary::bankdetupdate($get_bankId[$i]['salaryDate'],$rowval->sal,$rowval->charg,$lastyear,$lastmonth,$bankdetails->AccNo,$bankdetails->BankName);
					}
				}
				$salcou[$i]['salary'] = $rowval->sal;
				$salcou[$i]['charge'] = $rowval->charg;
			}
		}
		return view('Salary.index',['account_val' => $account_val,
									'current_year' => $current_year,
									'current_month' => $current_month,
									'last_year' => $last_year,
									'dbprevious' => $dbprevious,
									'dbnext' => $dbnext,
									'year_month' => $year_month,
									'date_month' => $date_month,
									'year_monthslt' => $year_monthslt,
									'account_period' => $account_period,
									'db_year_month' => $db_year_month,
									'chartotal' => $chartotal,
									'saltotal' => $saltotal,
									'get_det' => $get_det,
									'salarysortarray' => $salarysortarray,
									'index' => $g_query,
									'reg' => $reg,
									'fileCnt' => $fileCnt,
									'request' => $request]);
	}
	function empselectionpopup(Request $request) {
		$employeeUnselect = Salary::getAllEmpDetails($request);
		$employeeSelect = Salary::getAllFilteredEmpDetails($request);
		return view('Salary.empselectionpopup',['employeeUnselect' => $employeeUnselect,
												'employeeSelect' => $employeeSelect,
												'request' => $request]);
	}
	function empselectionprocess(Request $request) {
		$insert=Salary::InsertEmpFlrDetails($request);
		Session::flash('success', 'Employees Selected Sucessfully!'); 
		Session::flash('type', 'alert-success'); 
		Session::flash('datemonth',$request->datemonth); 
		$request->selected = "";
		$request->removed = "";
		return Redirect::to('Salary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function Singleview(Request $request) {
		if(Session::get('ids') !="") {
			$request->ids = Session::get('ids');
			$request->empname = Session::get('empname');
			$request->gobackflg = Session::get('gobackflg');
			$request->id = Session::get('id');
			$request->selMonth = Session::get('selMonth');
			$request->bankid = Session::get('bank');
			$request->selYear = Session::get('selYear');
		}
		if(!isset($request->ids)){
			return $this->index($request);
		}
		$singleview=Salary::fetchsingleview($request);
		return view('Salary.Singleview',['singleview' => $singleview,
										'request' => $request]);
	}
	function Viewlist(Request $request) {
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		//FOR SORTING
		$salaryviewlistarray = [$request->salaryviewsort=>$request->salaryviewsort,
							'salaryDate'=> trans('messages.lbl_Date')];
		$srt = $request->salaryviewsort;
		$odr = $request->sortOrder;
		if ($request->salaryviewsort == "") {
			$request->salaryviewsort = "salaryDate";
		}
		if (empty($request->sortOrder)) {
			$request->sortOrder = "DESC";
		}
		if ($request->sortOrder == "desc") {  
			$request->sortstyle="sort_desc";
		} else {  
			$request->sortstyle="sort_asc";
		}
		$viewlist=Salary::fetchviewlist($request);
		$i = 0;
		$disp = "";
		$saltotal = "";
		$chartotal = "";
		$salaryviewlist = array();
		foreach($viewlist as $key=>$value) {
			$salaryviewlist[$i]['id'] = $value->id;
			$salaryviewlist[$i]['empNo'] = $value->empNo;
			$salaryviewlist[$i]['salaryDate'] = $value->salaryDate;
			$salaryviewlist[$i]['bankId'] = $value->bankId;
			$salaryviewlist[$i]['salaryMonth'] = $value->salaryMonth;
			$salaryviewlist[$i]['bankname'] = $value->bankname;
			$salaryviewlist[$i]['accountNo'] = $value->AccNo;
			$salaryviewlist[$i]['salary'] = $value->salary;
			if ($value->charge == 0) {
				$salaryviewlist[$i]['charge'] = "";
			} else {
				$salaryviewlist[$i]['charge'] = $value->charge;
			}
			$saltotal += str_replace(",", "", $value->salary);
			$chartotal += str_replace(",", "", $value->charge);
			$i++;
		}
		$disp = count($salaryviewlist);
		return view('Salary.Viewlist',['index' => $viewlist,
										'saltotal' => $saltotal,
										'chartotal' => $chartotal,
										'disp' => $disp,
										'salaryviewlist' => $salaryviewlist,
										'salaryviewlistarray' => $salaryviewlistarray,
										'request' => $request]);
	}
	function addedit(Request $request) {
		$bankname=Salary::fetchbanknames($request);
		$register=Salary::fetchdetails($request);
		// print_r($register);exit();
		return view('Salary.addedit',['bankname' => $bankname,
										'register' => $register,
										'request' => $request]);
	}
	function edit(Request $request) {
		if (!isset($request->ids)) {
			return Redirect::to('Salary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankname=Salary::fetchbanknames($request);
		$detedit=Salary::fetcheditdetails($request);
		return view('Salary.addedit',['bankname' => $bankname,
										'detedit' => $detedit,
										'request' => $request]);
	}
	function copy(Request $request) {
		if (!isset($request->ids)) {
			return Redirect::to('Salary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankname=Salary::fetchbanknames($request);
		$detedit=Salary::fetcheditdetails($request);
		return view('Salary.addedit',['bankname' => $bankname,
										'detedit' => $detedit,
										'request' => $request]);
	}
	function addeditprocess(Request $request) {
		if($request->editflg == "1" || $request->editflg == "3") {
			$date = substr($request->txt_startdate,0,7);
			$insert = Salary::insertsalaryRec($request,$date);
			$autoid = Salary::getautoincrement();
			$ids = $autoid -1;
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		Session::flash('ids', $ids);
		} else if($request->editflg == "2") {
			$date = substr($request->txt_startdate,0,7);
			$update = Salary::updatesalaryRec($request,$date);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		Session::flash('ids', $request->ids);
		}
		Session::flash('selMonth', $request->selMonth); 
		Session::flash('selYear', $request->selYear); 
		Session::flash('id', $request->id); 
		Session::flash('gobackflg', $request->gobackflg); 
		Session::flash('bank', $request->bank); 
		Session::flash('empname', $request->empname);
		return Redirect::to('Salary/Singleview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function multiaddedit(Request $request) {
		if(!isset($request->selYear) && (!isset($request->selMonth))){
			return $this->index($request);
		}
		$bankname=Salary::fetchbanknames($request);
		$register=Salary::fetchmultidetails($request);
		$i = 0;
		$get_multisalaryId = array();
		foreach ($register as $key => $value) {
			$get_multisalaryId[$i]['id'] = $value->id;
			$get_multisalaryId[$i]['empNo'] = $value->Emp_ID;
			$get_multisalaryId[$i]['EmpName'] = ucwords(strtolower($value->LastName))
				. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
			$checkpre = Salary::getSalaryCheckprevious($value->Emp_ID);
			$checkprecou = count($checkpre);
			if ( $checkprecou >0 ) {
				$get_multisalaryId[$i]['previous'] = 1;
			} else {
				$get_multisalaryId[$i]['previous'] = 0;
			}
			$i++;
		}
		$fileCnt=count($get_multisalaryId);
		return view('Salary.multiaddedit',['bankname' => $bankname,
										'register' => $register,
										'get_multisalaryId' => $get_multisalaryId,
										'fileCnt' => $fileCnt,
										'request' => $request]);
	}
	function copycheck(Request $request) {
		$insert = Salary::getSalarypreCheck($request, 1);
		$degreedata=json_encode($insert);
		echo $degreedata; exit;
	}
	function multiaddeditprocess(Request $request) {
		$day = 0;
		$splitdates = explode("-", $request->txt_startdate);
		for ($i=0; $i <= $request->fileCnt; $i++) {
			$empid = 'empNo_'.$i;
			$salary = 'salary'.$i;
			$charge = 'charge'.$i;
			$request->day = $i;
			// ADD
			if ($request->multiflg == 1) {
				if ($request->$salary != "") {
					$date = substr($request->txt_startdate,0,7);
					$insert = Salary::salarymultireg($request,$day,$date);
					if($insert) {
						Session::flash('success', 'Inserted Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Inserted Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				}
				Session::flash('multiflg', $request->multiflg); 
			} else {
				if ($request->$salary != "") {
					$date = substr($request->txt_startdate,0,7);
					$update = Salary::salarymultiupd($request,$day,$date);
					if($update) {
						Session::flash('success', 'Updated Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Updated Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				}
				Session::flash('multiflg', 2); 
			}
		}
		Session::flash('selMonth', $splitdates[1]); 
		Session::flash('selYear', $splitdates[0]); 
		return Redirect::to('Salary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}