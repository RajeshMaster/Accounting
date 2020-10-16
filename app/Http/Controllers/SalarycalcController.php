<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Input;
use App\Model\SalaryCalc;
use App\Model\Totalexp;
use App\Http\Common;
use Session;
use Config;
use Carbon;
use Redirect;
use DateTime;
use Auth;
use Mail;
use View;
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PHPExcel_Style_Conditional;
use PHPExcel_Style_Color;


Class SalarycalcController extends Controller {
	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if ($request->tblchg == "") {
			$request->tblchg = 1;
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$getdetailsemp = SalaryCalc::fnGetdetailsfromemp();
		
		//START PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$temp_count = SalaryCalc::getTempDetails($request);
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
			// $date_month = date('Y-m');
		} else { 
			if ($request->get_prev_yr == 1) {
				$prev_month_ts = strtotime($request->selYear.'-'. substr("0" . $request->selMonth , -2).' -1 month');
				$date_month = date('Y-m', $prev_month_ts);
			} else {
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
		}
		// print_r($date_month);exit();
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
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-01"/* . substr("0" . $maxday, -2)*/;
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
		$account_val = SalaryCalc::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		$g_query = SalaryCalc::salaryDetail($request,$lastyear,$lastmonth,1);

		$k = 0;
		foreach ($g_query as $key => $value) {
			$get_det[$k]['id'] = $value->id;
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$empName = SalaryCalc::fnGetEmpName($value->Emp_ID);
			if (isset($empName[0])) {
				$firstName = $empName[0]->FirstName;
				$lastName = $empName[0]->LastName;
			} else {
				$firstName = '';
				$lastName = '';
			}
			$get_det[$k]['FirstName'] = $firstName;
			$get_det[$k]['LastName'] = $lastName;
			$get_det[$k]['mail_need'] = $value->mail_need;
			$get_det[$k]['EmpName'] = ucwords(strtolower($lastName))
				. ".".ucwords(mb_substr($firstName, 0, 1, 'utf-8'));
			$get_det[$k]['Salary'] = $value->Salary;
			$get_det[$k]['Deduction'] = $value->Deduction;
			$get_det[$k]['Basic'] = $value->Basic;
			$get_det[$k]['hra'] = $value->hra;
			$get_det[$k]['train_daily'] = $value->train_daily;
			$get_det[$k]['others'] = $value->others;
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
			$k++;
		}

		$salary_det = SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded = SalaryCalc::getsalaryDetails($request,'2');
		if ($request->get_prev_yr != 1) {
			$prev_month_ts = strtotime($date_month.' +1 month');
			$date_month = date('Y-m', $prev_month_ts);
		} else {
			$prev_month_ts = strtotime($date_month.' +1 month');
			$date_month = date('Y-m', $prev_month_ts);
		}

		// Total For Salary Details
		$g_query1 = SalaryCalc::salaryDetail($request,$lastyear,$lastmonth,1);
	    $a = 0;
		$get_master_tot = array();
		$get_master_tot1 = array();
		$tot_basic_amt = '';
		$tot_hra_amt = '';
		$tot_train_amt = '';
		$tot_exp_amt = '';
		$tot_transferred_amt = '';
		foreach ($g_query1 as $key => $value) {
			//For Basic,HRA,Train, Expenses & Transferred Details
			if ($value->Basic != '') {
				$tot_basic_amt += $value->Basic;
			}
			if ($value->hra != '') {
				$tot_hra_amt += $value->hra;
			}
			if ($value->train_daily != '') {
				$tot_train_amt += $value->train_daily;
			}
			if ($value->others != '') {
				$tot_exp_amt += $value->others;
			}
			if ($value->Transferred != '') {
				$tot_transferred_amt += $value->Transferred;
			}
			//For Salary Details
    		$arr1 = array();
    		$arr2 = array();
    		$sal_arr = array();
    		$val1 = '';
    		if ($value->Salary != '') {
					$Salary = explode('##', mb_substr($value->Salary, 0, -2));
					foreach ($Salary as $key => $value_key) {
						$sal_final = explode('$', $value_key);
						$arr1[$key] = $sal_final[0];
						$arr2[$sal_final[0]] = $sal_final[1];
					}
    		}
    		if(count($salary_det) != "") {
        		foreach ($salary_det as $key1 => $value1) {
        			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
        		}
    		}
    		$salresult_a=array_intersect($sal_arr,$arr1);
    		$salresult_b=array_diff($sal_arr,$arr1);
    		$salresult = array_merge($salresult_a,$salresult_b);
    		ksort($salresult);
	    	if(count($salary_det)!="") {
	    		$x = 0;
				foreach ($salresult as $key2 => $value2) {
	    			if($key2 == isset($arr2[$key2])) {
	    				$val1 += $arr2[$key2];
	    				$get_master_tot[$a][$key2] = $arr2[$key2];
	    			} else {
	    				$get_master_tot[$a][$key2] = 0;
	    			}
	    			$x++;
	    		}
			}
			// Salary Deduction
    		$arr3 = array();
    		$arr4 = array();
    		$ded_arr = array();
    		$val2 = '';
    		if ($value->Deduction != '') {
				$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
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
    		$dedresult_a=array_intersect($ded_arr,$arr3);
    		$dedresult_b=array_diff($ded_arr,$arr3);
    		$dedresult = array_merge($dedresult_a,$dedresult_b);
    		ksort($dedresult);
			if(count($salary_ded)!="") {
				$y = 0;
				foreach ($dedresult as $key2 => $value2) {
	    			if($key2 == isset($arr4[$key2])) {
	    				$val2 += $arr4[$key2];
	    				$get_master_tot1[$a][$key2] = $arr4[$key2];
	    			}
	    			$y++;
	    		}
			}
	    $a++;
		}
		// Salary Details
		$salaryDetails = array();
		foreach ($get_master_tot as $key => $value) {
			foreach ($value as $key_sid => $amount) {
				$salaryDetails[$key_sid][] = $amount;
			}
		}
		$temp_salaryDetails = array();
		foreach ($salaryDetails as $key => $value) {
			$b = '';
			foreach ($value as $key_sid => $amount) {
				$b += $amount;
			}
			$temp_salaryDetails[$key] = $b;
		}

		// Salary Deduction
		$salaryDetails_DD = array();
		foreach ($get_master_tot1 as $key_DD => $value_DD) {
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$salaryDetails_DD[$key_sid_DD][] = $amount_DD;
			}
		}
		$temp_salaryDetails_DD = array();
		foreach ($salaryDetails_DD as $key_DD => $value_DD) {
			$c = '';
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$c += $amount_DD;
			}
			$temp_salaryDetails_DD[$key_DD] = $c;
		}
		// For Year Bar Logic
		$db_year_month_new = array();
		foreach ($db_year_month as $key_yr => $value_mn) {
			foreach ($value_mn as $key_sub => $value_sub) {
				if ($value_sub == 12) {
					$db_year_month_new[$key_yr+1]['1'] = 1;
				} else if ($value_sub != 0) {
					$db_year_month_new[$key_yr][$key_sub+1] = $value_sub + 1;
				}
			}
		}

		$db_year_month = $db_year_month_new;

		// For Master Wise Record
		$main_array = array();
		$main_array_ded = array();
		if ($request->tblchg == 1) {
			$salary_full_det=SalaryCalc::getsalaryDetailsnoloc($request);
			// $sal_det_arr = array();
			$e = 0;
			foreach ($salary_full_det as $key => $value) {
				$f = 0;
				if ($value->location == 1) {
					// $sal_det_sub = 0;
					foreach ($get_det as $key1 => $value1) {
						$main_array[$value->Name.'$'.$value->Salarayid.'$0$'.$temp_salaryDetails[$value->Salarayid].'$'.$value->nick_name][$f] = '';
						if ($value1['Salary'] != '') {
							$Salary = explode('##', mb_substr($value1['Salary'], 0, -2));
							foreach ($Salary as $key2 => $value2) {
								$sal_final = explode('$', $value2);
								if($sal_final[0] == $value->Salarayid) {
									$main_array[$value->Name.'$'.$value->Salarayid.'$0$'.$temp_salaryDetails[$value->Salarayid].'$'.$value->nick_name][$f] = number_format($sal_final[1]);
								}
							}
						}
						$f++;
					}
				}
				$e++;
			}
			$e1 = 0;
			foreach ($salary_full_det as $key => $value) {
				$f1 = 0;
				if ($value->location == 2) {
					foreach ($get_det as $key1 => $value1) {
						if (isset($temp_salaryDetails_DD[$value->Salarayid]) && $temp_salaryDetails_DD[$value->Salarayid] != '') {
							$temp_DDs = $temp_salaryDetails_DD[$value->Salarayid];
						} else {
							$temp_DDs = '';
						}
						$main_array_ded[$value->Name.'$'.$value->Salarayid.'$1$'.$temp_DDs.'$'.$value->nick_name][$f1] = '';
						if ($value1['Deduction'] != '') {
							$Deduction = explode('##', mb_substr($value1['Deduction'], 0, -2));
							foreach ($Deduction as $key3 => $value3) {
								$ded_final = explode('$', $value3);
								if($ded_final[0] == $value->Salarayid) {
									$main_array_ded[$value->Name.'$'.$value->Salarayid.'$1$'.$temp_salaryDetails_DD[$value->Salarayid].'$'.$value->nick_name][$f1] = number_format($ded_final[1]);
								}
							}
						}
						$f1++;
					}
				}
				$e1++;
			}
			$main_array_sub = array();
			for ($i=0; $i < 4; $i++) { 
				$v = 0;
				foreach ($get_det as $key1 => $value1) {
					if ($i == 0) {
						$main_array_sub[trans('messages.lbl_basic').'$$0$'.$tot_basic_amt.'$'][$v] = ($value1['Basic'] != '')?number_format($value1['Basic']):'';
					} else if ($i == 1) {
						$main_array_sub[trans('messages.lbl_HRA').'$$0$'.$tot_hra_amt.'$'][$v] = ($value1['hra'] != '')?number_format($value1['hra']):'';
					} else if ($i == 2) {
						$main_array_sub[trans('messages.lbl_train_daily').'$$0$'.$tot_train_amt.'$'][$v] = ($value1['train_daily'] != '')?number_format($value1['train_daily']):'';
					} else if ($i == 3) {
						$main_array_sub[trans('messages.lbl_Others').'$$0$'.$tot_exp_amt.'$'][$v] = ($value1['others'] != '')?number_format($value1['others']):'';
					}
					$v++;
				}
			}
			$main_array = array_merge($main_array_sub,$main_array);
			$main_array = array_merge($main_array,$main_array_ded);
		}

		return view('salarycalc.index',['request' => $request,
										'main_array'=>$main_array,
										'tot_basic_amt'=>$tot_basic_amt,
										'tot_hra_amt'=>$tot_hra_amt,
										'tot_train_amt'=>$tot_train_amt,
										'tot_exp_amt'=>$tot_exp_amt,
										'tot_transferred_amt'=>$tot_transferred_amt,
										'temp_salaryDetails'=>$temp_salaryDetails,
										'temp_salaryDetails_DD'=>$temp_salaryDetails_DD,
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

	function history(Request $request) {
		if (!isset($request->Emp_ID)) {
			return Redirect::to('salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->selYear = $date_month[0];
			$request->selMonth = $date_month[1];
		}
		$salary_det=SalaryCalc::getsalaryDetailsnodelflg($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetailsnodelflg($request,'2');
		$g_query=SalaryCalc::salaryDetailhistory($request,0);
		$k = 0;
		$get_det = array();
		foreach ($g_query as $key => $value) {
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$get_det[$k]['Salary'] = $value->Salary;
			$get_det[$k]['Deduction'] = $value->Deduction;
			$get_det[$k]['Basic'] = $value->Basic;
			$get_det[$k]['hra'] = $value->hra;
			$get_det[$k]['train_daily'] = $value->train_daily;
			$get_det[$k]['others'] = $value->others;
			$get_det[$k]['Transferred'] = $value->Transferred;
			$get_det[$k]['mailFlg'] = $value->mailFlg;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$get_det[$k]['date'] = $value->date;
			$k++;
		}

		$g_query1=SalaryCalc::salaryDetailhistory($request,1);
		$a = 0;
		$get_master_tot = array();
		$get_master_tot1 = array();
		$tot_basic_amt = '';
		$tot_hra_amt = '';
		$tot_train_amt = '';
		$tot_exp_amt = '';
		$tot_transferred_amt = '';
		foreach ($g_query1 as $key => $value) {
			//For Basic,HRA,Train, Expenses & Transferred Details
			if ($value->Basic != '') {
				$tot_basic_amt += $value->Basic;
			}
			if ($value->hra != '') {
				$tot_hra_amt += $value->hra;
			}
			if ($value->train_daily != '') {
				$tot_train_amt += $value->train_daily;
			}
			if ($value->others != '') {
				$tot_exp_amt += $value->others;
			}
			if ($value->Transferred != '') {
				$tot_transferred_amt += $value->Transferred;
			}
			//For Salary Details
    		$arr1 = array();
    		$arr2 = array();
    		$sal_arr = array();
    		$val1 = '';
    		if ($value->Salary != '') {
					$Salary = explode('##', mb_substr($value->Salary, 0, -2));
					foreach ($Salary as $key => $value_key) {
						$sal_final = explode('$', $value_key);
						$arr1[$key] = $sal_final[0];
						$arr2[$sal_final[0]] = $sal_final[1];
					}
    		}
    		if(count($salary_det) != "") {
        		foreach ($salary_det as $key1 => $value1) {
        			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
        		}
    		}
    		$salresult_a=array_intersect($sal_arr,$arr1);
    		$salresult_b=array_diff($sal_arr,$arr1);
    		$salresult = array_merge($salresult_a,$salresult_b);
    		ksort($salresult);
	    	if(count($salary_det)!="") {
	    		$x = 0;
				foreach ($salresult as $key2 => $value2) {
	    			if($key2 == isset($arr2[$key2])) {
	    				$val1 += $arr2[$key2];
	    				$get_master_tot[$a][$key2] = $arr2[$key2];
	    			} else {
	    				$get_master_tot[$a][$key2] = 0;
	    			}
	    			$x++;
	    		}
			}
			// Salary Deduction
    		$arr3 = array();
    		$arr4 = array();
    		$ded_arr = array();
    		$val2 = '';
    		if ($value->Deduction != '') {
				$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
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
    		$dedresult_a=array_intersect($ded_arr,$arr3);
    		$dedresult_b=array_diff($ded_arr,$arr3);
    		$dedresult = array_merge($dedresult_a,$dedresult_b);
    		ksort($dedresult);
			if(count($salary_ded)!="") {
				$y = 0;
				foreach ($dedresult as $key2 => $value2) {
	    			if($key2 == isset($arr4[$key2])) {
	    				$val2 += $arr4[$key2];
	    				$get_master_tot1[$a][$key2] = $arr4[$key2];
	    			}
	    			$y++;
	    		}
			}
	    $a++;
		}
		// Salary Details
		$salaryDetails = array();
		foreach ($get_master_tot as $key => $value) {
			foreach ($value as $key_sid => $amount) {
				$salaryDetails[$key_sid][] = $amount;
			}
		}
		$temp_salaryDetails = array();
		foreach ($salaryDetails as $key => $value) {
			$b = '';
			foreach ($value as $key_sid => $amount) {
				$b += $amount;
			}
			$temp_salaryDetails[$key] = $b;
		}

		// Salary Deduction
		$salaryDetails_DD = array();
		foreach ($get_master_tot1 as $key_DD => $value_DD) {
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$salaryDetails_DD[$key_sid_DD][] = $amount_DD;
			}
		}
		$temp_salaryDetails_DD = array();
		foreach ($salaryDetails_DD as $key_DD => $value_DD) {
			$c = '';
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$c += $amount_DD;
			}
			$temp_salaryDetails_DD[$key_DD] = $c;
		}

		// year bar process
		$cur_year = date('Y');
		$curtime = date('YmdHis');
		$yearArr = SalaryCalc::getYears($request);
		$prev_yrs = array();
		$total_yrs = array();

		foreach ($yearArr as $value) {
			$prev_yrs[] = $value->years;
			$total_yrs[] = $value->years;
		}

		if (!in_array($cur_year, $total_yrs)) {
		    array_push($total_yrs,$cur_year);
		}

		if (isset($request->selYear) && !empty($request->selYear)) {
			$selectedYear=$request->selYear;
			$cur_year=$selectedYear;
		} else {
			$selectedYear=$cur_year;
		}

		return view('salarycalc.history',['salary_det' => $salary_det,
												'salary_ded' => $salary_ded,
												'tot_basic_amt'=>$tot_basic_amt,
												'tot_hra_amt'=>$tot_hra_amt,
												'tot_train_amt'=>$tot_train_amt,
												'tot_exp_amt'=>$tot_exp_amt,
												'tot_transferred_amt'=>$tot_transferred_amt,
												'temp_salaryDetails'=>$temp_salaryDetails,
												'temp_salaryDetails_DD'=>$temp_salaryDetails_DD,
												'get_det' => $get_det,
												'g_query' => $g_query,
												'cur_year' =>  $cur_year,
												'curtime' =>  $curtime,
												'prev_yrs' =>  $prev_yrs,
												'total_yrs' =>  $total_yrs,
												'selectedYear' =>  $selectedYear,
												'request' => $request]);
	}

	public function view(Request $request) {
		if ($request->hdn_id != '') {
			$request->id = $request->hdn_id;
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success');
		}
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
		if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->selYear = $date_month[0];
			$request->selMonth = $date_month[1];
		}
		if ($request->next_record == 1 && $request->hdn_id != '') {
			$request->id = $request->hdn_id;
		}
		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		$detedit = SalaryCalc::salarycalcview($request);
		return view('salarycalc.view',['request' => $request,
										'salary_det' => $salary_det,
										'salary_ded' => $salary_ded,
										'detedit' => $detedit[0]]);
	}

	
	function getPrevKey($key, $operation, $hash = array()) {
	    $keys = array_keys($hash);
	    $keys1 = count($keys);
	    $found_index = array_search($key, $keys);
		if ($keys1 === 1) {
			return $keys[0];
		}
	    if ($found_index === false)
	        return false;
	    if ($operation == 1) {
	    	return $keys[$found_index+1];
	    } else {
	    	return $keys[$found_index-1];
	    }
	}

	// For History process - Madasamy 07/07
	public function getHistoryData(Request $request){

		$salary_det1=SalaryCalc::getsalaryDetailsnodelflg($request,'1');
		$salary_ded1=SalaryCalc::getsalaryDetailsnodelflg($request,'2');
		$g_query=SalaryCalc::salaryDetailhistory_Addedit($request,0);
		$k = 0;
		$get_det = array();
		foreach ($g_query as $key => $value) {
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$get_det[$k]['Salary'] = $value->Salary;
			$get_det[$k]['Deduction'] = $value->Deduction;
			$get_det[$k]['Basic'] = $value->Basic;
			$get_det[$k]['hra'] = $value->hra;
			$get_det[$k]['train_daily'] = $value->train_daily;
			$get_det[$k]['others'] = $value->others;
			$get_det[$k]['Transferred'] = $value->Transferred;
			$get_det[$k]['mailFlg'] = $value->mailFlg;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$get_det[$k]['date'] = $value->date;
			$k++;
		}
		$g_query1=SalaryCalc::salaryDetailhistory_Addedit($request,1);
		$a = 0;
		$get_master_tot = array();
		$get_master_tot1 = array();
		$tot_basic_amt = '';
		$tot_hra_amt = '';
		$tot_train_amt = '';
		$tot_exp_amt = '';
		$tot_transferred_amt = '';
		foreach ($g_query1 as $key => $value) {
			//For Basic,HRA,Train, Expenses & Transferred Details
			if ($value->Basic != '') {
				$tot_basic_amt += $value->Basic;
			}
			if ($value->hra != '') {
				$tot_hra_amt += $value->hra;
			}
			if ($value->train_daily != '') {
				$tot_train_amt += $value->train_daily;
			}
			if ($value->others != '') {
				$tot_exp_amt += $value->others;
			}
			if ($value->Transferred != '') {
				$tot_transferred_amt += $value->Transferred;
			}
			//For Salary Details
    		$arr1 = array();
    		$arr2 = array();
    		$sal_arr = array();
    		$val1 = '';
    		if ($value->Salary != '') {
					$Salary = explode('##', mb_substr($value->Salary, 0, -2));
					foreach ($Salary as $key => $value_key) {
						$sal_final = explode('$', $value_key);
						$arr1[$key] = $sal_final[0];
						$arr2[$sal_final[0]] = $sal_final[1];
					}
    		}
    		if(count($salary_det1) != "") {
        		foreach ($salary_det1 as $key1 => $value1) {
        			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
        		}
    		}
    		$salresult_a=array_intersect($sal_arr,$arr1);
    		$salresult_b=array_diff($sal_arr,$arr1);
    		$salresult = array_merge($salresult_a,$salresult_b);
    		ksort($salresult);
	    	if(count($salary_det1)!="") {
	    		$x = 0;
				foreach ($salresult as $key2 => $value2) {
	    			if($key2 == isset($arr2[$key2])) {
	    				$val1 += $arr2[$key2];
	    				$get_master_tot[$a][$key2] = $arr2[$key2];
	    			} else {
	    				$get_master_tot[$a][$key2] = 0;
	    			}
	    			$x++;
	    		}
			}
			// Salary Deduction
    		$arr3 = array();
    		$arr4 = array();
    		$ded_arr = array();
    		$val2 = '';
    		if ($value->Deduction != '') {
				$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
				foreach ($Deduction as $key => $value1) {
					$ded_final = explode('$', $value1);
					$arr3[$key] = $ded_final[0];
					$arr4[$ded_final[0]] = $ded_final[1];
				}
    		}
    		if(count($salary_ded1) != "") {
        		foreach ($salary_ded1 as $key2 => $value2) {
        			$ded_arr[$value2->Salarayid] = $value2->Salarayid;
        		}
    		}
    		$dedresult_a=array_intersect($ded_arr,$arr3);
    		$dedresult_b=array_diff($ded_arr,$arr3);
    		$dedresult = array_merge($dedresult_a,$dedresult_b);
    		ksort($dedresult);
			if(count($salary_ded1)!="") {
				$y = 0;
				foreach ($dedresult as $key2 => $value2) {
	    			if($key2 == isset($arr4[$key2])) {
	    				$val2 += $arr4[$key2];
	    				$get_master_tot1[$a][$key2] = $arr4[$key2];
	    			}
	    			$y++;
	    		}
			}
	    $a++;
		}
		// Salary Details
		$salaryDetails = array();
		foreach ($get_master_tot as $key => $value) {
			foreach ($value as $key_sid => $amount) {
				$salaryDetails[$key_sid][] = $amount;
			}
		}
		$temp_salaryDetails = array();
		foreach ($salaryDetails as $key => $value) {
			$b = '';
			foreach ($value as $key_sid => $amount) {
				$b += $amount;
			}
			$temp_salaryDetails[$key] = $b;
		}

		// Salary Deduction
		$salaryDetails_DD = array();
		foreach ($get_master_tot1 as $key_DD => $value_DD) {
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$salaryDetails_DD[$key_sid_DD][] = $amount_DD;
			}
		}
		$temp_salaryDetails_DD = array();
		foreach ($salaryDetails_DD as $key_DD => $value_DD) {
			$c = '';
			foreach ($value_DD as $key_sid_DD => $amount_DD) {
				$c += $amount_DD;
			}
			$temp_salaryDetails_DD[$key_DD] = $c;
		}

		$historyData = array();
		$historyData['salary_det1'] = $salary_det1;
		$historyData['salary_ded1'] = $salary_ded1;
		$historyData['tot_basic_amt'] = $tot_basic_amt;
		$historyData['tot_hra_amt'] = $tot_hra_amt;
		$historyData['tot_train_amt'] = $tot_train_amt;
		$historyData['tot_exp_amt'] = $tot_exp_amt;
		$historyData['tot_transferred_amt'] = $tot_transferred_amt;
		$historyData['temp_salaryDetails'] = $temp_salaryDetails;
		$historyData['temp_salaryDetails_DD'] = $temp_salaryDetails_DD;
		$historyData['get_det'] = $get_det;
		$historyData['g_query'] = $g_query;

		return $historyData;

	}

	function salarydownload(Request $request) {
		if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->selYear = $date_month[0];
			$request->selMonth = $date_month[1];
		}
		$template_name = 'resources/assets/uploadandtemplates/templates/salary_details.xls';
        $tempname = "Salary_".$request->selMonth.'_'.$request->selYear;
        $excel_name=$tempname;
		Excel::load($template_name, function($objTpl) use($request) {
			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');

			$g_query = SalaryCalc::salaryDetail($request,$request->selYear,$request->selMonth,1);
			$hdn_empid = array();
			$q = 0;
			foreach ($g_query as $key_id => $value_set) {
				$hdn_empid[$q] = $value_set->Emp_ID;
				$q++;
			}
	        $empdetails=SalaryCalc::fnGetEmployeeDetails($request,$hdn_empid);
	        $x = 5;
	        $y = 1;
	        $z = $x + count($empdetails);
	        $sumtotalval = 0;
	        $objTpl->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        	foreach ($empdetails as $key => $value) {
        		$sumtotalval += $value->Transferred;

        		// Added By Madasamy - 07/08/2020
        		$empName = SalaryCalc::fnGetEmpName($value->Emp_ID);
				if (isset($empName[0])) {
					$firstName = $empName[0]->FirstName;
					$lastName = $empName[0]->LastName;
					$kanaFirstName = $empName[0]->KanaFirstName;
					$kanaLastName = $empName[0]->KanaLastName;
				} else {
					$firstName = '';
					$lastName = '';
					$firstName = '';
					$lastName = '';
				}

        		$objTpl->getActiveSheet()->getRowDimension($x)->setRowHeight(28);
        		$objTpl->getActiveSheet()->getStyle('A'.$x.':E'.$x)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        		$objTpl->getActiveSheet()->getStyle('A'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        		$objTpl->getActiveSheet()->getStyle('A'.$x)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('C'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('D'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('E'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->setCellValue('A'.$x, $y);
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	            $objTpl->getActiveSheet()->setCellValue('B'.$x, $value->Emp_ID);
	            $objTpl->getActiveSheet()->getStyle('E'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	            $objTpl->getActiveSheet()->setCellValue('C'.$x, strtoupper($firstName.' '.$lastName));
	            $objTpl->getActiveSheet()->setCellValue('D'.$x, $kanaFirstName.' '.$kanaLastName);
	            $objTpl->getActiveSheet()->setCellValue('E'.$x, number_format($value->Transferred));
            	$objTpl->getActiveSheet()->getStyle('E'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	            $x++;
	            $y++;
        	}
	        $objTpl->getActiveSheet()->getStyle('A4:E4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
	        $objTpl->getActiveSheet()->getRowDimension($z)->setRowHeight(30);
	        $objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getFont()->setBold(true);
        	$objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        	$objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
	        $objTpl->getActiveSheet()->setCellValue('D'.$z, "合計");
	        $objTpl->getActiveSheet()->setCellValue('E'.$z, number_format($sumtotalval));
        	$objTpl->getActiveSheet()->getStyle('D'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        	$objTpl->getActiveSheet()->getStyle('E'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        	$objTpl->getActiveSheet()->mergeCells('B1:C1');
	        $objTpl->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        	$objTpl->getActiveSheet()->mergeCells('B2:C2');
        	$objTpl->getActiveSheet()->getCell('B1')->setValue('株式会社Microbit');
	        $objTpl->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	        $objTpl->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        	$objTpl->getActiveSheet()->getCell('B2')->setValue('給料月:   '.$request->selYear.'-'.$request->selMonth);
        	$objTpl->getActiveSheet()->setTitle('Salary_'.$request->selMonth.'_'.$request->selYear);
        	$objTpl->setActiveSheetIndex();
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
        	$flpath='.xls';
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="'.$flpath.'"');
        	header('Cache-Control: max-age=0');
        })->setFilename($excel_name)->download('xls');
	}

}