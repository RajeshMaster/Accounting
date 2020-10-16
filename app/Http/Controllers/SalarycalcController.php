<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Input;
use App\Model\SalaryPlus;
use App\Model\SalaryCalc;
use App\Model\Salary;
use App\Model\Invoice;
use App\Http\Common;
use Session;
use Carbon;
use Redirect;
use DateTime;
use Auth;
use Mail;
use View;


Class SalarycalcController extends Controller {
	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$getdetailsemp = SalaryCalc::fnGetdetailsfromemp();
		if ($getdetailsemp == 0) {
			$insertdetailsemp = SalaryCalc::fninsertdetailsfromemp($request);
		}
		//START PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$temp_count = SalaryCalc::getTempDetails($request);
		if ($temp_count == 0) {
			$empdetails = SalaryCalc::getEmpDetailsId($request);
		}
		//END PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$year_month = array();
		$db_year_month = array();
		$dbrecord = array();
		$dbnext = array();
		$dbprevious = array();
		$account_val ="";
		$displayArray = array();
		$get_det = array();
		$g_query_tot = array();

			if (!isset($request->selMonth)) { 
				$date_month = date('Y-m', strtotime("last month"));
			} else { 
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
			$last=date('Y-m', strtotime('last month'));
			$last1 = date($date_month , strtotime($last . " last month"));
			$lastdate = explode('-',$last1);
			$lastyear =$lastdate[0];
			$lastmonth =$lastdate[1];
			$request->selMonth = $lastmonth;
			$request->selYear = $lastyear;
			$g_accountperiod = SalaryCalc::fnGetAccountPeriod();
			$account_close_yr = $g_accountperiod[0]->Closingyear;
			$account_close_mn = $g_accountperiod[0]->Closingmonth;
			$account_period = intval($g_accountperiod[0]->Accountperiod);

			$splityear = explode('-', $request->previou_next_year); 
			if ($request->previou_next_year != "") { 
				if (intval($splityear[1]) > $account_close_mn) {
					$last_year = intval($splityear[0]);
					$current_year = intval($splityear[0]) + 1;
				} else {
					$last_year = intval($splityear[0]) - 1;
					$current_year = intval($splityear[0]);
				}
			} else if ($request->selYear) {
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
			$year_month_day = $current_year . "-" . $account_close_mn . "-01";
			$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
			$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
			$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
			
			$exp_query = SalaryCalc::fnGetmnthRecord($from_date, $to_date);
			foreach ($exp_query as $key => $res1) {
				$concat = $res1->year.'-'.$res1->month;
				//array_push($dbrecord, $res1['start_date']);
				array_push($dbrecord, $concat);
			}

			$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
				array_push($dbrecord, $lastMonthAsLink);
			$exp_query1 = SalaryCalc::fnGetmnthRecordPrevious($from_date);
			foreach ($exp_query1 as $key => $res2) {
				array_push($dbprevious, $res2->date);
			}
			$dbprevious = array_unique($dbprevious);

			$exp_query2 = SalaryCalc::fnGetmnthRecordNext($to_date);
			foreach ($exp_query2 as $key => $res3) {
				array_push($dbnext, $res3->date);
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
			
			$split_date = explode('-', $date_month);

			$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		$g_query = SalaryCalc::salaryDetail($request,$lastyear,$lastmonth,0);
		// $g_query_tot = SalaryPlus::salaryDetailtot($request,$lastyear,$lastmonth);
		// $g_query_totall = SalaryPlus::salaryDetailtotall($request,$lastyear,$lastmonth);
		$k = 0;
		foreach ($g_query as $key => $value) {
			$get_det[$k]['status'] = "";
			$exist_check = SalaryPlus::salaryExistcheck($request,$value->Emp_ID);
			if ($exist_check == 0 && $value->id != "") {
				$get_det[$k]['status'] = 0;
			} else if ($exist_check != 0 && $value->id != "") {
				$get_det[$k]['status'] = 1;
			}
			$get_det[$k]['id'] = $value->id;
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$get_det[$k]['FirstName'] = $value->FirstName;
			$get_det[$k]['LastName'] = $value->LastName;
			$get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName))
				. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
			$get_det[$k]['Salary'] = $value->Salary;
			$get_det[$k]['Deduction'] = $value->Deduction;
			$get_det[$k]['Transferred'] = $value->Transferred;
			$get_det[$k]['mailFlg'] = $value->mailFlg;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$checkdet[$k]['checkedit'] = SalaryCalc::salaryDetailcheck($request,$value->Emp_ID);
				if (!empty($checkdet[$k]['checkedit'])) {
					$get_det[$k]['editcheck'] = "1";
				} else {
					$get_det[$k]['editcheck'] = "0";
				}
				if (isset($value->id)) {
					$g_query_sep_tot[$k] = SalaryPlus::salaryDetailseperatetot($request,$value->id);
				}
			$k++;
		}
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		// print_r($salary_det);exit();
		return view('salarycalc.index',['request' => $request,
										'salary_det'=>$salary_det,
										'salary_ded'=>$salary_ded,
										'g_query'=>$g_query,
										'g_query_tot'=>$g_query_tot,
										// 'g_query_totall'=>$g_query_totall,
										'get_det'=>$get_det,
										'account_val'=>$account_val,
										'account_period'=> $account_period,
										'year_month'=> $year_month,
										'db_year_month'=> $db_year_month,
										'date_month'=> $date_month,
										'dbnext'=> $dbnext,
										'dbprevious'=> $dbprevious,
										'last_year'=> $last_year,
										'current_year'=> $current_year]);
	}

	function salarypopup(Request $request) {
		$employeeUnselect = SalaryCalc::getAllEmpDetails($request);
		$employeeSelect = SalaryCalc::getAllFilteredEmpDetails($request);
		return view('salarycalc.salarypopup',['employeeUnselect' => $employeeUnselect,
												'employeeSelect' => $employeeSelect,
												'request' => $request]);
	}

	function empselectprocess(Request $request) {
		$insert=SalaryCalc::InsertEmpFlrDetails($request);
		if($insert){
			Session::flash('success', 'Employees Selected Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		}else {
			Session::flash('type', 'Employees Selected Unsucessfully!!'); 
			Session::flash('type', 'alert-danger'); 
		}
		Session::flash('selMonth', $request->month); 
		Session::flash('selYear', $request->year); 
		$request->selected = "";
		$request->removed = "";
		return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	public function view(Request $request) {
		if(Session::get('Emp_ID') !="" && Session::get('id') !="") {
			$request->id =  Session::get('id');
			$request->Emp_ID =  Session::get('Emp_ID');
			$request->firstname =  Session::get('firstname');
			$request->lastname =  Session::get('lastname');
			$request->editcheck =  Session::get('editcheck');
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if (!isset($request->Emp_ID)) {
			return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		/*$total = SalaryPlus::salaryDetailseperatetot($request,$request->id);*/
		$detedit = SalaryCalc::salarycalcview($request);
		return view('salarycalc.view',['request' => $request/*,
											'total' => $total*/,
											'salary_det' => $salary_det,
											'salary_ded' => $salary_ded,
											'detedit' => $detedit[0]]);
	}

	public function edit(Request $request) {
		if (!isset($request->Emp_ID)) {
			return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$detedit = array();
		$details = SalaryCalc::salarycalcview($request);
		foreach ($details as $key => $value) {
			$detedit['id'] = $value->id;
			$detedit['Emp_ID'] = $value->Emp_ID;
			$detedit['date'] = $value->date;
			$detedit['year'] = $value->year;
			$detedit['month'] = $value->month;
			$sal = explode('##', mb_substr($value->Salary, 0, -2));
			foreach ($sal as $key1 => $value1) {
				$sal_final = explode('$', $value1);
				$detedit += array('salary_'.$sal_final[0] => ($sal_final[1] != '') ?number_format($sal_final[1]):'');
			}
			$ded = explode('##', mb_substr($value->Deduction, 0, -2));
			foreach ($ded as $key2 => $value2) {
				$ded_final = explode('$', $value2);
				$detedit += array('deduction_'.$ded_final[0] => ($ded_final[1] != '') ? number_format($ded_final[1]): '');
			}
			$detedit['transferred'] = number_format($value->Transferred);
		}
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		return view('salarycalc.addedit',['request' => $request,
											'salary_det' => $salary_det,
											'salary_ded' => $salary_ded,
											'detedit' => $detedit]);
	}

	public function addedit(Request $request) {
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		return view('salarycalc.addedit',['request' => $request,
											'salary_det' => $salary_det,
											'salary_ded' => $salary_ded]);
	}

	public function addeditprocess(Request $request) {
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		if($request->editcheck == 1) {
			$update = SalaryCalc::fnsalarycalcupd($request,$salary_det,$salary_ded);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->id); 
		} else {
			$insert = SalaryCalc::fnsalarycalcadd($request,$salary_det,$salary_ded);
			$getid = SalaryCalc::fngetid();
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $getid); 
		}
		$date = explode("-", $request->date);
		Session::flash('editcheck', '2'); 
		Session::flash('Emp_ID', $request->Emp_ID); 
		Session::flash('firstname', $request->firstname); 
		Session::flash('lastname', $request->lastname); 
		Session::flash('selMonth', $date[1]); 
		Session::flash('selYear', $date[0]);  
		Session::flash('prevcnt', $request->prevcnt); 
		Session::flash('nextcnt', $request->nextcnt); 
		Session::flash('account_val', $request->account_val); 
		Session::flash('previou_next_year', $request->previou_next_year);
		return Redirect::to('salarycalc/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	
	function multieditprocess(Request $request){
		$get_det = array();
		$detedit = array();
		$g_query = SalaryCalc::salaryDetail($request,$request->selYear,$request->selMonth,1);
		$k = 0;
		foreach ($g_query as $key => $value) {
			if ($value->Salary == "") {
				$get_det[$k]['id'] = $value->id;
				$get_det[$k]['Emp_ID'] = $value->Emp_ID;
				$get_det[$k]['FirstName'] = $value->FirstName;
				$get_det[$k]['LastName'] = $value->LastName;
				$k++;
			}
		}

		// For Previous Data Getting Process
		if ($request->salflg == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'. substr("0" . $request->selMonth , -2).' -1 month');
			$prev_month = date('Y-m', $prev_month_ts);
			$last_month_year = explode('-', $prev_month);

			$split_array = explode(',', $request->hdn_salid_arr);

			foreach ($g_query as $key => $value) {
				if ($value->Salary == "") {
					$single_data = $g_query = SalaryCalc::salaryDetail($request,$last_month_year[0],$last_month_year[1],1,$value->Emp_ID);
					foreach ($single_data as $key1 => $value1) {
						$Salary = explode('##', mb_substr($value1->Salary, 0, -2));
						foreach ($Salary as $key2 => $value2) {
							$sal_final = explode('$', $value2);
							for ($i=0; $i < count($split_array); $i++) { 
								if ($split_array[$i] == $sal_final[0]) {
									$detedit['salary_'.$value1->Emp_ID.'_'.$sal_final[0]] = ($sal_final[1] != '') ?number_format($sal_final[1]):'';
								}
							}
						}
						$Deduction = explode('##', mb_substr($value1->Deduction, 0, -2));
						foreach ($Deduction as $key3 => $value3) {
							$ded_final = explode('$', $value3);
							for ($j=0; $j < count($split_array); $j++) { 
								if ($split_array[$j] == $ded_final[0]) {
									$detedit['Deduction_'.$value1->Emp_ID.'_'.$ded_final[0]] = ($ded_final[1] != '') ?number_format($ded_final[1]):'';
								}
							}
						}
					}
				}
			}
		}
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		return view('salarycalc.multiedit',[
											'g_query'=>$g_query,
											'salary_det'=>$salary_det,
											'salary_ded'=>$salary_ded,
											'get_det'=>$get_det,
											'detedit'=>$detedit,
											'request' => $request]);
	}

	public function multiregister(Request $request) {	
		$query_date = $request->selYear.'-'.$request->selMonth.'-1';
		$date = new DateTime($query_date);
		// Last day of month
		$date->modify('last day of this month');
		$lastday = $date->format('Y-m-d');
		$request->date_hdn = $lastday;
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
			$insert = Salarycalc::multiadd($request,$salary_det,$salary_ded,$lastday);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			$date = explode("-", $request->date_hdn);
			// $chkmonth =" " . substr("0" . $date);
			Session::flash('selMonth', $date[1]); 
			Session::flash('selYear', $date[0]);
			return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	public function mailsendprocess(Request $request) {
		$hdn_empid = explode(',', $request->hdn_empid_arr);
		foreach ($hdn_empid as $key => $value) {
			$month_name = date("F", mktime(0, 0, 0, $request->selMonth, 10));
			$salary_details=SalaryCalc::getsalaryempDetails($request,$value);
			$salary_det=SalaryCalc::getsalaryDetails($request,'1');
			$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
			$arr1 = array();
    		$arr2 = array();
    		$sal_arr = array();
    		if ($salary_details[0]->Salary != '') {
				$Salary = explode('##', mb_substr($salary_details[0]->Salary, 0, -2));
				foreach ($Salary as $key => $value) {
					$sal_final = explode('$', $value);
					$arr1[$key] = $sal_final[0];
					$arr2[$sal_final[0]] = $sal_final[1];
				}
    		}
    		if(count($salary_det) != "") {
        		foreach ($salary_det as $key1 => $value1) {
        			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
        		}
    		}
    		$arr3 = array();
    		$arr4 = array();
    		$ded_arr = array();
    		if ($salary_details[0]->Deduction != '') {
				$Deduction = explode('##', mb_substr($salary_details[0]->Deduction, 0, -2));
				foreach ($Deduction as $key => $value1) {
					$ded_final = explode('$', $value1);
					$arr3[$key] = $ded_final[0];
					$arr4[$ded_final[0]] = $ded_final[1];
				}
    		}
    		if(count($salary_ded) != "") {
        		foreach ($salary_ded as $key2 => $value2) {
        			$ded_arr[$value2->Salarayid] = $value2->Salarayid;
        		}
    		}
    		if ($salary_details[0]->Emailpersonal != '') {
    			$send = Mail::send('salarycalc/mailtemplate',compact(
											'arr1',
											'arr2',
											'sal_arr',
											'arr3',
											'arr4',
											'ded_arr',
											'salary_det',
											'salary_ded',
											'salary_details',
											'month_name',
											'request'), 
						function($message) use ($request,$month_name,$salary_details) {
						$message->from('staff@microbit.co.jp','HR INDIA');
						$message->cc('staff@microbit.co.jp.com');
						$message->to($salary_details[0]->Emailpersonal)->subject('Salary Details_'.$request->selYear.'_'.$month_name.' : Reg');
					});
				if ($send) {
					$salary_details=SalaryCalc::updateMailFlg($request,$salary_details[0]->Emp_ID);
				}
    		}
			
		}
		Session::flash('success', 'Mail Sent Sucessfully!'); 
		Session::flash('type', 'alert-success');
		Session::flash('selMonth', $request->selMonth); 
		Session::flash('selYear', $request->selYear);
		return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

}