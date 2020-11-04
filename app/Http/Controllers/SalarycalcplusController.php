<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Model\SalaryCalcplus;
use App\Model\SalaryCalc;
use App\Http\Common;
use Session;
use Carbon;
use Redirect;
use DateTime;
use Input;
use Auth;
use View;
// use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PHPExcel_Style_Conditional;
use PHPExcel_Style_Color;
use Maatwebsite\Excel\Facades\Excel;


Class SalarycalcplusController extends Controller {
	
	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if ($request->tblchg == "") {
			$request->tblchg = 0;
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		// $getdetailsemp = SalaryCalcplus::fnGetdetailsfromemp();
		// if ($getdetailsemp == 0) {
		// 	$insertdetailsemp = SalaryCalcplus::fninsertdetailsfromemp($request);
		// }
		//START PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		// $temp_count = SalaryCalcplus::getTempDetails($request);
		// if ($temp_count == 0) {
		// 	$empdetails = SalaryCalcplus::getEmpDetailsId($request);
		// }
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
			if ($request->get_prev_yr == 1) {
				$prev_month_ts = strtotime($request->selYear.'-'. substr("0" . $request->selMonth , -2).' -1 month');
				$date_month = date('Y-m', $prev_month_ts);
			} else {
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
		}
		$last=date('Y-m', strtotime('last month'));
		$last1 = date($date_month , strtotime($last . " last month"));
		$lastdate = explode('-',$last1);
		$lastyear =$lastdate[0];
		$lastmonth =$lastdate[1];
		$request->selMonth = $lastmonth;
		$request->selYear = $lastyear;
		$g_accountperiod = SalaryCalcplus::fnGetAccountPeriod();
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
		
		$exp_query = SalaryCalcplus::fnGetmnthRecord($from_date, $to_date);
		foreach ($exp_query as $key => $res1) {
			$concat = $res1->year.'-'.$res1->month;
			//array_push($dbrecord, $res1['start_date']);
			array_push($dbrecord, $concat);
		}

		$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
			array_push($dbrecord, $lastMonthAsLink);
		$exp_query1 = SalaryCalcplus::fnGetmnthRecordPrevious($from_date);
		foreach ($exp_query1 as $key => $res2) {
			array_push($dbprevious, $res2->date);
		}
		$dbprevious = array_unique($dbprevious);

		$exp_query2 = SalaryCalcplus::fnGetmnthRecordNext($to_date);
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
		$empArrVal = SalaryCalcplus::fnGetEmpIdList($lastyear,$lastmonth);
		$empArr = array();
		foreach ($empArrVal as $key => $value) {
			$empArr[] = $value->Emp_ID;
		}

		$salPlus = self::getSalaryDetailsTotal($empArr,$lastyear,$lastmonth);
		$salArr = $salPlus['salArr'];
		$temp_salaryDetails = $salPlus['salArrTot']['temp_salaryDetails'];
		$temp_salaryDetails_DD = $salPlus['salArrTot']['temp_salaryDetails_DD'];
		$tot_travel_amt = $salPlus['salArrTot']['tot_travel_amt'];
		$salresult = $salPlus['salArrTot']['salresult'];
		$dedresult = $salPlus['salArrTot']['dedresult'];

		$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
		if ($request->get_prev_yr != 1) {
			$prev_month_ts = strtotime($date_month.' +1 month');
			$date_month = date('Y-m', $prev_month_ts);
		} else {
			$prev_month_ts = strtotime($date_month.' +1 month');
			$date_month = date('Y-m', $prev_month_ts);
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
		
		return view('salarycalcplus.index',['request' => $request,
											'salArr'=>$salArr,
											'salresult' => $salresult,
											'dedresult' => $dedresult,
											'tot_travel_amt'=>$tot_travel_amt,
											'temp_salaryDetails'=>$temp_salaryDetails,
											'temp_salaryDetails_DD'=>$temp_salaryDetails_DD,
											'salary_det'=>$salary_det,
											'salary_ded'=>$salary_ded,
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
			return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		
		$salary_det=SalaryCalcplus::getsalaryDetailsnodelflg($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetailsnodelflg($request,'2');
		$g_query=SalaryCalcplus::salaryDetailhistory($request,0);
		$k = 0;
		$get_det = array();
		foreach ($g_query as $key => $value) {
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$get_det[$k]['Salary'] = $value->Salary;
			$get_det[$k]['Deduction'] = $value->Deduction;
			$get_det[$k]['Travel'] = $value->Travel;
			$get_det[$k]['mailFlg'] = $value->mailFlg;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$get_det[$k]['date'] = $value->date;
			$k++;
		}
		// Total For Salary Details
		$g_query1 = SalaryCalcplus::salaryDetailhistory($request,1);
	    $a = 0;
		$get_master_tot = array();
		$get_master_tot1 = array();
		$tot_travel_amt = '';
		foreach ($g_query1 as $key => $value) {
			//For Travel,Salary Amount & Transferred Details
			if ($value->Travel != '') {
				$tot_travel_amt += $value->Travel;
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
	    	if(count($salary_det)!="" && is_array($salresult)) {
	    		$x = 0;
				foreach ($salresult as $key2 => $value2) {
					if ($key2 != '') {
		    			if($key2 == isset($arr2[$key2])) {
		    				$val1 += $arr2[$key2];
		    				$get_master_tot[$a][$key2] = $arr2[$key2];
		    			} else {
		    				$get_master_tot[$a][$key2] = 0;
		    			}
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
					if ($key2 != '') {
		    			if($key2 == isset($arr4[$key2])) {
		    				$val2 += $arr4[$key2];
		    				$get_master_tot1[$a][$key2] = $arr4[$key2];
		    			}
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
		$yearArr = SalaryCalcplus::getYears($request);
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

		return view('salarycalcplus.history',['salary_det' => $salary_det,
												'salary_ded' => $salary_ded,
												'tot_travel_amt'=>$tot_travel_amt,
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

	function salarypopup(Request $request) {

		if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->year.'-'.$request->month.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->year = $date_month[0];
			$request->month = $date_month[1];
		}
		$employeeUnselect = SalaryCalcplus::getAllEmpDetails($request,0);
		$employeeSelect = SalaryCalcplus::getAllEmpDetails($request,1);
		
		return view('salarycalcplus.salarypopup',['employeeUnselect' => $employeeUnselect,
													'employeeSelect' => $employeeSelect,
													'request' => $request]);
	}

	function empselectprocess(Request $request) {
		
		$insert=SalaryCalcplus::InsertEmpFlrDetails($request);
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
		
		return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
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
			return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
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
		$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
		$detedit = SalaryCalcplus::salarycalcview($request);

		if (isset($detedit[0])) {
			$detedit = $detedit[0];
		} else {
			return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		
		return view('salarycalcplus.view',['request' => $request,
											'salary_det' => $salary_det,
											'salary_ded' => $salary_ded,
											'detedit' => $detedit ]);
	}

	public function getTransferedAmount(Request $request) {

		$transferedamt = SalaryCalcplus::inv_salary_main_transferedamt($request);
		if (isset($transferedamt[0]->Transferred) && $transferedamt[0]->Transferred != '') {
			print_r(number_format($transferedamt[0]->Transferred));exit();
		} else {
			print_r(isset($transferedamt[0]->Transferred) && $transferedamt[0]->Transferred != '');exit();
		}
	}

	public function getajaxtotamt(Request $request) {

		$salary_det=SalaryCalc::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalc::getsalaryDetails($request,'2');
		$request->selMonth = $request->month;
		$detedit = SalaryCalc::salarycalcview_tot($request);
		$arr1 = array();
		$arr2 = array();
		$arr3 = array();
		$arr4 = array();
		$sal_arr = array();
		$ded_arr = array();
		$sal_arr_name = array();
		$ded_arr_name = array();
		$val1 = '';
		$val2 = '';
		$val3 = '';
		// For Salary Details
		if (!empty($detedit)) {
			if ($detedit[0]->Basic != '') {
				$val1 += $detedit[0]->Basic;
			}
			if ($detedit[0]->hra != '') {
				$val1 += $detedit[0]->hra;
			}
			if ($detedit[0]->train_daily != '') {
				$val1 += $detedit[0]->train_daily;
			}
			if ($detedit[0]->others != '') {
				$val1 += $detedit[0]->others;
			}
			if ($detedit[0]->Salary != '') {
				$Salary = explode('##', mb_substr($detedit[0]->Salary, 0, -2));
				foreach ($Salary as $key => $value) {
					$sal_final = explode('$', $value);
					$arr1[$key] = $sal_final[0];
					$arr2[$sal_final[0]] = $sal_final[1];
				}
			}
			if(count($salary_det) != "") {
	    		foreach ($salary_det as $key1 => $value1) {
	    			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
	    			$sal_arr_name[$value1->Salarayid] = $value1->Name;
	    		}
			}
			$salresult_a=array_intersect($sal_arr,$arr1);
			$salresult_b=array_diff($sal_arr,$arr1);
			$salresult = array_merge($salresult_a,$salresult_b);
			sort($salresult);
			if(count($salary_det)!="") {
				foreach ($salresult as $key2 => $value2) {
					foreach ($sal_arr_name as $key3 => $value3) {
						if($key3 == $value2) {
							$val1 += isset($arr2[$key3])?$arr2[$key3]:'';
						}
					}
				}
			}
			
			// For Deduction Details
			if ($detedit[0]->Deduction != '') {
				$Deduction = explode('##', mb_substr($detedit[0]->Deduction, 0, -2));
				foreach ($Deduction as $key => $value) {
					$ded_final = explode('$', $value);
					$arr3[$key] = $ded_final[0];
					$arr4[$ded_final[0]] = $ded_final[1];
				}
			}
			if(count($salary_ded) != "") {
				foreach ($salary_ded as $key1 => $value1) {
					$ded_arr[$value1->Salarayid] = $value1->Salarayid;
					$ded_arr_name[$value1->Salarayid] = $value1->Name;
				}
			}
			$dedresult_a=array_intersect($ded_arr,$arr3);
			$dedresult_b=array_diff($ded_arr,$arr3);
			$dedresult = array_merge($dedresult_a,$dedresult_b);
			sort($dedresult);
			if(count($salary_ded)!="") {
				foreach ($dedresult as $key2 => $value2) {
					foreach ($ded_arr_name as $key3 => $value3) {
						if($key3 == $value2) {
							$val2 += isset($arr4[$key3])?$arr4[$key3]:'';
						}
					}
				}
			}
			$val3 = $val1 + $val2;
		}
		if ($val3 != '') {
			print_r(number_format($val3));exit();
		} else {
			print_r($val3);exit();
		}
	}

	public function getlastmonthdet(Request $request) {

		$prev_month_ts = strtotime($request->selYear.'-'.$request->month.' -1 month');
		$date_month = date('Y-m', $prev_month_ts);
		$date_month = explode('-', $date_month);
		$request->selYear = $date_month[0];
		$request->selMonth = $date_month[1];
		$detedit = array();
		$details = SalaryCalcplus::salarycalcplusview_tot($request);
		foreach ($details as $key => $value) {
			$detedit['remarks'] = $value->remarks;
			$sal = explode('##', mb_substr($value->Salary, 0, -2));
			foreach ($sal as $key1 => $value1) {
				if ($value1 != '') {
					$sal_final = explode('$', $value1);
					$detedit += array('salary_'.$sal_final[0] => ($sal_final[1] != '') ?number_format($sal_final[1]):'');
				}
			}
			$ded = explode('##', mb_substr($value->Deduction, 0, -2));
			foreach ($ded as $key2 => $value2) {
				if ($value2 != '') {
					$ded_final = explode('$', $value2);
					$detedit += array('deduction_'.$ded_final[0] => ($ded_final[1] != '') ? number_format($ded_final[1]): '');
				}
			}
			if ($value->Travel != '') {
				$detedit['Travel'] = number_format($value->Travel);
			}
		}
		echo json_encode($detedit);exit();
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

	public function addedit(Request $request) {

		if (!isset($request->selYear) && !isset($request->selMonth)) {
			return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$stop_next = '';
		$get_count = '';
		$emp_array = array();
		$get_det = array();
		$get_det_count = array();
		if ($request->next_record == 1) {
			if ($request->get_prev_yr != '') {
				$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
				$date_month = date('Y-m', $prev_month_ts);
				$date_month = explode('-', $date_month);
				$request->selYear = $date_month[0];
				$request->selMonth = $date_month[1];
			}
			$g_query = SalaryCalcplus::salaryDetail($request,$request->selYear,$request->selMonth,1);
			foreach ($g_query as $key => $value) {
				if ($value->Salary == "" && $value->Deduction == "") {
					$empName = SalaryCalcplus::fnGetEmpName($value->Emp_ID);
					if (isset($empName[0])) {
						$firstName = $empName[0]->FirstName;
						$lastName = $empName[0]->LastName;
					}
					$get_det_count[$value->Emp_ID] = $firstName.'$'.$lastName;
				}
			}
			$get_count = count($get_det_count);
			foreach ($g_query as $key => $value) {
				if ($value->Emp_ID === $request->Emp_ID && $get_count != 1) {
					$empName = SalaryCalcplus::fnGetEmpName($value->Emp_ID);
					if (isset($empName[0])) {
						$firstName = $empName[0]->FirstName;
						$lastName = $empName[0]->LastName;
					}
					$get_det[$value->Emp_ID] = $firstName.'$'.$lastName;
				} else {
					if ($value->Salary == "" && $value->Deduction == "") {
						$empName = SalaryCalcplus::fnGetEmpName($value->Emp_ID);
						if (isset($empName[0])) {
							$firstName = $empName[0]->FirstName;
							$lastName = $empName[0]->LastName;
						}
						$get_det[$value->Emp_ID] = $firstName.'$'.$lastName;
					}
				}
			}
			$lastKey = key(array_slice($get_det, -1, true));
			if ($lastKey == $request->Emp_ID) {
				$operation = 0;
			} else {
				$operation = 1;
			}
			$Emp_ID = self::getPrevKey($request->Emp_ID, $operation, $get_det);
			$emp_array['stop_next'] = '';
			if (count($get_det) == 1) {
				$emp_array['stop_next'] = '1';
			}
			if ($Emp_ID != '') {
				$names = '';
				$names = explode('$', $get_det[$Emp_ID]);
				$_REQUEST['Emp_ID'] = $Emp_ID;
				$_REQUEST['firstname'] = $names[0];
				$_REQUEST['lastname'] = $names[1];
				$emp_array['Emp_ID'] = $Emp_ID;
				$emp_array['firstname'] = $names[0];
				$emp_array['lastname'] = $names[1];
				$request->Emp_ID = $Emp_ID;
				$request->firstname = $names[0];
				$request->lastname = $names[1];
			} else {
				Session::flash('selMonth', $request->selMonth); 
				Session::flash('selYear', $request->selYear);
				Session::flash('success', 'All Data Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success');
				return Redirect::to('salarycalcplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
			}
			$_REQUEST['next_record'] = '';
		} else {
			if ($request->get_prev_yr != '') {
				$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
				$date_month = date('Y-m', $prev_month_ts);
				$date_month = explode('-', $date_month);
				$request->selYear = $date_month[0];
				$request->selMonth = $date_month[1];
			}
			$g_query = SalaryCalcplus::salaryDetail($request,$request->selYear,$request->selMonth,1);
			foreach ($g_query as $key => $value) {
				if ($value->Salary == "" && $value->Deduction == "") {
					$empName = SalaryCalcplus::fnGetEmpName($value->Emp_ID);
					if (isset($empName[0])) {
						$firstName = $empName[0]->FirstName;
						$lastName = $empName[0]->LastName;
					}
					$get_det[$value->Emp_ID] = $firstName.'$'.$lastName;
				}
			}
			if (count($get_det) == 1) {
				$stop_next = '1';
			}
		}
		$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
		
		return view('salarycalcplus.addedit',['request' => $request,
											'stop_next' => $stop_next,
											'emp_array' => $emp_array,
											'salary_det' => $salary_det,
											'salary_ded' => $salary_ded]);
	}

	public function getdataExists(Request $request) {

		$dataExist = SalaryCalcplus::fnGetDataExistsCheck($request);
		$countdata = count($dataExist);
		print_r($countdata);exit();
  	}

  	public function dataReg(Request $request) {

  		$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
		$dataReg = SalaryCalcplus::fnsalarycalcadd($request,$salary_det,$salary_ded);
		$getid = SalaryCalcplus::fngetid();
		print_r($getid);exit();
  	}

	function salarydownloadprocess(Request $request) {

		$template_name = 'resources/assets/uploadandtemplates/templates/salary_plus_details.xls';
        $tempname = strtoupper($request->lastname);
        $excel_name=$tempname;
		Excel::load($template_name, function($objTpl) use($request) {
			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');
        	$g_query = SalaryCalcplus::salaryDetail_Empid($request);
        	$salary_det=SalaryCalcplus::getsalaryDetailsnodelflg($request,'1');
			$salary_ded=SalaryCalcplus::getsalaryDetailsnodelflg($request,'2');
			$a = 1;
			$break_arr = array();
			$total_cnt = '';
			if (count($g_query) == 1) {
				$total_cnt = count($g_query);
				$total_cnt_sub = count($g_query);
			} else if ((count($g_query) -2) % 2 == 0) {
				// For Add two Slip below original
				$total_cnt_sub = count($g_query);
				$total_cnt = count($g_query) -2;
				for ($j=1; $j <= $total_cnt ; $j++) { 
					if ($j % 2 == 0) {
						$break_arr[$j] = $j;
					}
				}
				$break_arr[0] = 'even';
			} else if ((count($g_query) - 2) % 2 != 0) {
				// For Add one Slip below original
				$total_cnt_sub = count($g_query);
				$total_cnt = count($g_query) -2;
				for ($j=1; $j <= $total_cnt ; $j++) { 
					if ($j % 2 == 0) {
						$break_arr[$j] = $j;
					}
				}
				$break_arr[0] = 'odd';
				$break_arr[$total_cnt + 1] = 'odd';
			}
			$i = 1;
			$i_sub = 1;
			$start_line = '';
			// For Payment Slip
			$pay_row = 2;
			// For Payment Slip
			$year_month_row = 3;
			// For Payment Slip
			$address_row = 4;
			// For payment & Deduction Header
			$pay_header = 5;
			// For Salary Details & Deduction Loop
			$merge_sal = 6;
			$merge_sal_1_1 = 6;
			$merge_sal_1_2 = 6;
			$merge_sal_2_1 = 6;
			$merge_sal_2_2 = 6;

        	foreach ($g_query as $key => $value) {
        		// print_r($value);echo "<BR>";echo "<BR>";
        		if ($i % 2 == 0) {
					// For Even Values
					// For Payment Slip Merge
					$objTpl->getActiveSheet()->mergeCells('H'.$pay_row.':M'.$pay_row);
					// For Year & Month Merge
					$objTpl->getActiveSheet()->mergeCells('L'.$year_month_row.':M'.$year_month_row);
					// For Address Merge
					$objTpl->getActiveSheet()->mergeCells('L'.$address_row.':M'.$address_row);
					// For Employee Name Set
					$objTpl->getActiveSheet()->getStyle('H'.$year_month_row)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle('H'.$year_month_row)->getFont()->setUnderline(true);
					$objTpl->getActiveSheet()->setCellValue('H'.$year_month_row, 'Mr. '.strtoupper($request->firstname).' '.strtoupper($request->lastname));

					// For Year & Month Set
					$month_name = date("F", mktime(0, 0, 0, $value->month, 10));
					$objTpl->getActiveSheet()->setCellValue('L'.$year_month_row, $value->year.' '.$month_name);

					// For Payment & Deduction Merge
					$objTpl->getActiveSheet()->mergeCells('H'.$pay_header.':J'.$pay_header);
					$objTpl->getActiveSheet()->mergeCells('L'.$pay_header.':M'.$pay_header);

					// For Salary Details Payment
					if ($value->Salary != '') {
						$Salary = explode('##', mb_substr($value->Salary, 0, -2));
						$sal_final_arr = array();
						foreach ($Salary as $key_valsal => $value_valsal) {
							$sal_final = explode('$', $value_valsal);
							$sal_final_arr += array($sal_final[0] => $sal_final[1]);
						}
					}
					$amt1_2_sal = '';
					foreach ($salary_det as $key_sal => $value_sal) {
						// For Salary Deduction
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_1)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_1)->getFont()->setSize(9);
						// For Salary Details
						$objTpl->getActiveSheet()->mergeCells('H'.$merge_sal_2_1.':I'.$merge_sal_2_1);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('J'.$merge_sal_2_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('J'.$merge_sal_2_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_1)->getFont()->setSize(9);
						$objTpl->getActiveSheet()->getStyle('J'.$merge_sal_2_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        				$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_2_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        				$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_1)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						//Set Name Field
						$objTpl->getActiveSheet()->setCellValue('H'.$merge_sal_2_1, $value_sal->Name);
	        			$objTpl->getActiveSheet()->getRowDimension($merge_sal_2_1)->setRowHeight(22.5);
	        			// Set Amount Field
	        			$objTpl->getActiveSheet()->setCellValue('J'.$merge_sal_2_1, (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?number_format($sal_final_arr[$value_sal->Salarayid]):'');
						$amt1_2_sal += (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?$sal_final_arr[$value_sal->Salarayid]:'';
						$merge_sal_2_1++;
					}
					if ($value->Deduction != '') {
						$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
						$ded_final_arr = array();
						foreach ($Deduction as $key_valded => $value_valded) {
							$ded_final = explode('$', $value_valded);
							$ded_final_arr += array($ded_final[0] => $ded_final[1]);
						}
					}
					$amt1_2_ded = '';
					foreach ($salary_ded as $key_ded => $value_ded) {
						// For Salary Deduction
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_2_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getFont()->setSize(9);
						// For Salary Details
						$objTpl->getActiveSheet()->mergeCells('H'.$merge_sal_2_2.':I'.$merge_sal_2_2);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('J'.$merge_sal_2_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_2)->getFont()->setSize(9);
	        			$objTpl->getActiveSheet()->getRowDimension($merge_sal_2_2)->setRowHeight(22.5);
	        			$objTpl->getActiveSheet()->getStyle('J'.$merge_sal_2_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        				$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        				$objTpl->getActiveSheet()->getStyle('H'.$merge_sal_2_2)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						$objTpl->getActiveSheet()->setCellValue('L'.$merge_sal_2_2, $value_ded->Name);
						// Set Amount Field
						$amt1_2_ded += (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?$ded_final_arr[$value_ded->Salarayid]:'';
	        			$objTpl->getActiveSheet()->setCellValue('M'.$merge_sal_2_2, (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?number_format(substr($ded_final_arr[$value_ded->Salarayid],1)):'');
						$merge_sal_2_2++;
					}
					// Set Salary Deduction Total Amount
					$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->setCellValue('L'.$merge_sal_2_2, 'Total');
        			$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					if ($amt1_2_ded != '') {
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->setCellValue('M'.$merge_sal_2_2, number_format(substr($amt1_2_ded, 1)));
					}

					$cnt_even = '';
					if ($merge_sal_2_2 == $merge_sal_2_1) {
						$cnt_even = $merge_sal_2_1;
					} else if ($merge_sal_2_2 > $merge_sal_2_1) {
						$cnt_even = $merge_sal_2_2;
					} else if ($merge_sal_2_2 < $merge_sal_2_1) {
						$cnt_even = $merge_sal_2_1;
					}
					// For Salary Details As Taxable Payment
					$objTpl->getActiveSheet()->mergeCells('H'.$cnt_even.':I'.$cnt_even);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_even)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->setCellValue('H'.$cnt_even, 'Taxable payment');
        			$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        			$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($merge_sal_2_2 == $cnt_even) {
        				$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			} else {
        				$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			}
        			$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($amt1_2_sal != '') {
        				$objTpl->getActiveSheet()->setCellValue('J'.$cnt_even, number_format($amt1_2_sal));
        			}
        			$cnt_even = $cnt_even + 1;
        			// For Salary Details As Travel
					$objTpl->getActiveSheet()->mergeCells('H'.$cnt_even.':I'.$cnt_even);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_even)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->setCellValue('H'.$cnt_even, 'Travel');
        			$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        			$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($merge_sal_2_2 == $cnt_even) {
        				$objTpl->getActiveSheet()->getStyle('L'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objTpl->getActiveSheet()->getStyle('M'.$merge_sal_2_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			} else {
        				$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			}
        			$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($value->Travel != '' && $value->Travel != 0) {
        				$objTpl->getActiveSheet()->setCellValue('J'.$cnt_even, number_format($value->Travel));
        			}
        			$cnt_even = $cnt_even + 1;
        			// For Salary Details As Total payment
					$objTpl->getActiveSheet()->mergeCells('H'.$cnt_even.':I'.$cnt_even);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even.':J'.$cnt_even)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_even)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->getStyle('H'.$cnt_even)->getFont()->setBold( true );
        			$objTpl->getActiveSheet()->setCellValue('H'.$cnt_even, 'Total Payment');
        			$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$tot_sum_2 = '';
        			$tot_sum_2 = $amt1_2_sal + $value->Travel;

        			if ($tot_sum_2 != '' && $tot_sum_2 != 0) {
        				$objTpl->getActiveSheet()->getStyle('J'.$cnt_even)->getFont()->setBold( true );
        				$objTpl->getActiveSheet()->setCellValue('J'.$cnt_even, number_format($tot_sum_2));
        			}
        			$objTpl->getActiveSheet()->getStyle('K'.$cnt_even)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			// For Salary Details As Gross payment
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even.':M'.$cnt_even)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_even)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->getStyle('L'.$cnt_even)->getFont()->setBold( true );
        			$objTpl->getActiveSheet()->setCellValue('L'.$cnt_even, 'Gross Payment');
        			$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$gross_sum_2 = '';
        			$gross_sum_2 = $tot_sum_2 + $amt1_2_ded;

        			if ($gross_sum_2 != '' && $gross_sum_2 != 0) {
        				$objTpl->getActiveSheet()->getStyle('M'.$cnt_even)->getFont()->setBold( true );
        				$objTpl->getActiveSheet()->setCellValue('M'.$cnt_even, number_format($gross_sum_2));
        			}

        			// Set Border
        			$objTpl->getActiveSheet()->getStyle('H2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('L3')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('L4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('L5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$merge_sal_2_1 += $cnt_even;
        			$merge_sal_2_2 += $cnt_even;
				} else {
					// For Odd Values
					// For Payment Slip Merge
					$objTpl->getActiveSheet()->mergeCells('A'.$pay_row.':F'.$pay_row);
					// For Year & Month Merge
					$objTpl->getActiveSheet()->mergeCells('E'.$year_month_row.':F'.$year_month_row);
					// For Address Merge
					$objTpl->getActiveSheet()->mergeCells('E'.$address_row.':F'.$address_row);
					// For Employee Name Set
					$objTpl->getActiveSheet()->getStyle('A'.$year_month_row)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle('A'.$year_month_row)->getFont()->setUnderline(true);
					$objTpl->getActiveSheet()->setCellValue('A'.$year_month_row, 'Mr. '.strtoupper($request->firstname).' '.strtoupper($request->lastname));

					// For Year & Month Set
					$month_name = date("F", mktime(0, 0, 0, $value->month, 10));
					$objTpl->getActiveSheet()->setCellValue('E'.$year_month_row, $value->year.' '.$month_name);
					// For Payment & Deduction Merge
					$objTpl->getActiveSheet()->mergeCells('A'.$pay_header.':C'.$pay_header);
					$objTpl->getActiveSheet()->mergeCells('E'.$pay_header.':F'.$pay_header);

					// For Salary Details Payment
					if ($value->Salary != '') {
						$Salary = explode('##', mb_substr($value->Salary, 0, -2));
						$sal_final_arr = array();
						foreach ($Salary as $key_valsal => $value_valsal) {
							$sal_final = explode('$', $value_valsal);
							$sal_final_arr += array($sal_final[0] => $sal_final[1]);
						}
					}
					$amt1_1_sal = '';
					foreach ($salary_det as $key_sal => $value_sal) {
						// For Salary Deduction
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_1)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_1)->getFont()->setSize(9);
						// For Salary Details
						$objTpl->getActiveSheet()->mergeCells('A'.$merge_sal_1_1.':B'.$merge_sal_1_1);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('C'.$merge_sal_1_1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('C'.$merge_sal_1_1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_1)->getFont()->setSize(9);
						$objTpl->getActiveSheet()->getStyle('C'.$merge_sal_1_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        				$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        				$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_1)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						//Set Name Field
						$objTpl->getActiveSheet()->setCellValue('A'.$merge_sal_1_1, $value_sal->Name);
	        			$objTpl->getActiveSheet()->getRowDimension($merge_sal_1_1)->setRowHeight(22.5);
	        			// Set Amount Field
	        			$objTpl->getActiveSheet()->setCellValue('C'.$merge_sal_1_1, (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?number_format($sal_final_arr[$value_sal->Salarayid]):'');
						$amt1_1_sal += (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?$sal_final_arr[$value_sal->Salarayid]:'';
						$merge_sal_1_1++;
					}
					if ($value->Deduction != '') {
						$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
						$ded_final_arr = array();
						foreach ($Deduction as $key_valded => $value_valded) {
							$ded_final = explode('$', $value_valded);
							$ded_final_arr += array($ded_final[0] => $ded_final[1]);
						}
					}
					$amt1_1_ded = '';
					foreach ($salary_ded as $key_ded => $value_ded) {
						// For Salary Deduction
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getFont()->setSize(9);
						// For Salary Details
						$objTpl->getActiveSheet()->mergeCells('A'.$merge_sal_1_2.':B'.$merge_sal_1_2);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('C'.$merge_sal_1_2)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_2)->getFont()->setSize(9);
	        			$objTpl->getActiveSheet()->getRowDimension($merge_sal_1_2)->setRowHeight(22.5);
	        			$objTpl->getActiveSheet()->getStyle('C'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        				$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        				$objTpl->getActiveSheet()->getStyle('A'.$merge_sal_1_2)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						$objTpl->getActiveSheet()->setCellValue('E'.$merge_sal_1_2, $value_ded->Name);
						// Set Amount Field
						$amt1_1_ded += (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?$ded_final_arr[$value_ded->Salarayid]:'';
	        			$objTpl->getActiveSheet()->setCellValue('F'.$merge_sal_1_2, (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?number_format(substr($ded_final_arr[$value_ded->Salarayid],1)):'');
						$merge_sal_1_2++;
					}
					// Set Salary Deduction Total Amount
					$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->setCellValue('E'.$merge_sal_1_2, 'Total');
        			$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					if ($amt1_1_ded != '') {
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->setCellValue('F'.$merge_sal_1_2, number_format(substr($amt1_1_ded, 1)));
					}

					$cnt_odd = '';
					if ($merge_sal_1_2 == $merge_sal_1_1) {
						$cnt_odd = $merge_sal_1_1;
					} else if ($merge_sal_1_2 > $merge_sal_1_1) {
						$cnt_odd = $merge_sal_1_2;
					} else if ($merge_sal_1_2 < $merge_sal_1_1) {
						$cnt_odd = $merge_sal_1_1;
					}
					// For Salary Details As Taxable Payment
					$objTpl->getActiveSheet()->mergeCells('A'.$cnt_odd.':B'.$cnt_odd);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_odd)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->setCellValue('A'.$cnt_odd, 'Taxable payment');
        			$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        			$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($merge_sal_1_2 == $cnt_odd) {
        				$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			} else {
        				$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			}
        			$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($amt1_1_sal != '') {
        				$objTpl->getActiveSheet()->setCellValue('C'.$cnt_odd, number_format($amt1_1_sal));
        			}
        			$cnt_odd = $cnt_odd + 1;
        			// For Salary Details As Travel
					$objTpl->getActiveSheet()->mergeCells('A'.$cnt_odd.':B'.$cnt_odd);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_odd)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->setCellValue('A'.$cnt_odd, 'Travel');
        			$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
        			$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($merge_sal_1_2 == $cnt_odd) {
        				$objTpl->getActiveSheet()->getStyle('E'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objTpl->getActiveSheet()->getStyle('F'.$merge_sal_1_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			} else {
        				$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
						$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			}
        			$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getleft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			if ($value->Travel != '' && $value->Travel != 0) {
        				$objTpl->getActiveSheet()->setCellValue('C'.$cnt_odd, number_format($value->Travel));
        			}
        			$cnt_odd = $cnt_odd + 1;
        			// For Salary Details As Total payment
					$objTpl->getActiveSheet()->mergeCells('A'.$cnt_odd.':B'.$cnt_odd);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd.':C'.$cnt_odd)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_odd)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->getStyle('A'.$cnt_odd)->getFont()->setBold( true );
        			$objTpl->getActiveSheet()->setCellValue('A'.$cnt_odd, 'Total Payment');
        			$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$tot_sum_1 = '';
        			$tot_sum_1 = $amt1_1_sal + $value->Travel;

        			if ($tot_sum_1 != '' && $tot_sum_1 != 0) {
        				$objTpl->getActiveSheet()->getStyle('C'.$cnt_odd)->getFont()->setBold( true );
        				$objTpl->getActiveSheet()->setCellValue('C'.$cnt_odd, number_format($tot_sum_1));
        			}
        			$objTpl->getActiveSheet()->getStyle('D'.$cnt_odd)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			// For Salary Details As Gross payment
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd.':F'.$cnt_odd)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
					$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getFont()->setSize(9);
        			$objTpl->getActiveSheet()->getRowDimension($cnt_odd)->setRowHeight(22.5);
        			$objTpl->getActiveSheet()->getStyle('E'.$cnt_odd)->getFont()->setBold( true );
        			$objTpl->getActiveSheet()->setCellValue('E'.$cnt_odd, 'Gross Payment');
        			$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        			$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$gross_sum_1 = '';
        			$gross_sum_1 = $tot_sum_1 + $amt1_1_ded;

        			if ($gross_sum_1 != '' && $gross_sum_1 != 0) {
        				$objTpl->getActiveSheet()->getStyle('F'.$cnt_odd)->getFont()->setBold( true );
        				$objTpl->getActiveSheet()->setCellValue('F'.$cnt_odd, number_format($gross_sum_1));
        			}

        			// Set Border
        			$objTpl->getActiveSheet()->getStyle('A2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('E3')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('E4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$objTpl->getActiveSheet()->getStyle('E5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        			$no_of_lines_to_copy = $cnt_odd + 1;
        			/*if ($total_cnt == 1) {
        				print_r("Main");
        				// Set Print Area
        				$objTpl->getActiveSheet()->setBreak('A'.$cnt_odd, 1)->getPageSetup()->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0)->setPrintArea('A1:F'.$cnt_odd,$i_sub,'I');
        			} else {
        				print_r("SUB");*/
	        			// Set Print Area
	        			if ($i_sub == 1) {
	        				$start_line = $cnt_odd;
	        				$objTpl->getActiveSheet()->setBreak('A'.$cnt_odd, 1)->getPageSetup()->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0)->setPrintArea('A1:M'.$cnt_odd,$i_sub,'I');
	        			} else{
		        			$objTpl->getActiveSheet()->setBreak('A'.$cnt_odd, 1)->getPageSetup()->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0)->setPrintArea('A'.($cnt_odd - $start_line).':M'.$cnt_odd,$i_sub,'I');
		        		}
	        		// }
        			if ($i == 1) {
        				$cnt_odd_sub = $cnt_odd;
        			}
        			$i_sub++;
				}

				$reset = '';
				if (isset($break_arr[$i]) && $break_arr[$i] == $i && $break_arr[0] == 'even') {
					$reset = 2;
				} else if (isset($break_arr[$i]) && $break_arr[$i] == $i && $break_arr[0] == 'odd') {
					$reset = 2;
				} else if (isset($break_arr[$i]) && $break_arr[$i] == 'odd' && $break_arr[0] == 'odd') {
					$reset = 1;
				}

				if ($reset != '') {
					if ($reset == 2) {
						self::copyPhpExcelWorkSheetRows($objTpl->getActiveSheet(),1,$no_of_lines_to_copy,$cnt_odd_sub,13);
						$reset = '';
						$merge_sal_1_1 = 6 + $cnt_odd;
	        			$merge_sal_1_2 = 6 + $cnt_odd;
	        			$merge_sal_2_1 = 6 + $cnt_odd;
	        			$merge_sal_2_2 = 6 + $cnt_odd;
	        			$merge_sal = $merge_sal_1_1;
						// For Payment Slip
						$pay_row = $cnt_odd + 2;
						// For Payment Slip
						$year_month_row = $cnt_odd + 3;
						// For Payment Slip
						$address_row = $cnt_odd + 4;
						$pay_header = $cnt_odd + 5;
					} else if ($reset == 1) {
						self::copyPhpExcelWorkSheetRows($objTpl->getActiveSheet(),1,$no_of_lines_to_copy,$cnt_odd_sub,6);
						$reset = '';
						$merge_sal_1_1 = 6 + $cnt_odd;
	        			$merge_sal_1_2 = 6 + $cnt_odd;
	        			$merge_sal_2_1 = 6 + $cnt_odd;
	        			$merge_sal_2_2 = 6 + $cnt_odd;
	        			$merge_sal = $merge_sal_1_1;
						// For Payment Slip
						$pay_row = $cnt_odd + 2;
						// For Payment Slip
						$year_month_row = $cnt_odd + 3;
						// For Payment Slip
						$address_row = $cnt_odd + 4;
						$pay_header = $cnt_odd + 5;
					}

				}

				if ($total_cnt_sub == 1) {
					$objTpl->getActiveSheet()->removeColumn('G');
					$objTpl->getActiveSheet()->removeColumn('H');
					$objTpl->getActiveSheet()->removeColumn('I');
					$objTpl->getActiveSheet()->removeColumn('J');
					$objTpl->getActiveSheet()->removeColumn('K');
					$objTpl->getActiveSheet()->removeColumn('L');
					$objTpl->getActiveSheet()->removeColumn('M');
				}
					
				$i++;
        	}

			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');
			$objTpl->getActiveSheet()->setTitle('Payment_Slip_'.strtoupper($request->lastname));
        	$flpath='.xls';
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="'.$flpath.'"');
        	header('Cache-Control: max-age=0');
        	
        })->setFilename($excel_name)->download('xls');
	}

	/**
     * [PHPExcel] Copy the specified line of the sheet completely
     * Created By Easa @2020/06/12
     * @param PHPExcel_Worksheet $sheet
     * @param int $srcRow Copy source row
     * @param int $dstRow Destination row
     * @param int $height Number of lines to copy
     * @param int $width Number of columns to copy
     */
    function copyPhpExcelWorkSheetRows ($sheet, $srcRow, $dstRow, $height, $width) {
        for ($row = 0;$row<$height;$row ++) {
            // cell format and value replication
            for ($col = 0;$col<$width;$col ++) {
                $cell = $sheet->getCellByColumnAndRow ($col, $srcRow + $row);
                $style = $sheet->getStyleByColumnAndRow ($col, $srcRow + $row);
                $dstCell = PHPExcel_Cell :: stringFromColumnIndex ($col). (string) ($dstRow + $row);
                $sheet->setCellValue ($dstCell, $cell->getValue ());
                $sheet->duplicateStyle ($style, $dstCell);
            }
            // Duplicate row height.
            $h = $sheet->getRowDimension ($srcRow + $row)->getRowHeight ();
            $sheet->getRowDimension ($dstRow + $row)->setRowHeight ($h);
        }
        // duplicate cell merge
        //-$mergeCell = "AB12: AC15" Restored by adding lines only to those in the replication range.
        //-$merge = "AB16: AC19"
        foreach ($sheet->getMergeCells () as $mergeCell) {
            $mc = explode (":", $mergeCell);
            $col_s = preg_replace ("/ [0-9] * /", "", $mc [0]);
            $col_e = preg_replace ("/ [0-9] * /", "", $mc [1]);
            $row_s = ((int) preg_replace ("/ [A-Z] * /", "", $mc [0]))-$srcRow;
            $row_e = ((int) preg_replace ("/ [A-Z] * /", "", $mc [1]))-$srcRow;
            // If it is a line range to copy to.
            if (0<= $row_s&&$row_s<$height) {
                $merge = $col_s. (string) ($dstRow + $row_s). ":". $col_e. (string) ($dstRow + $row_e);
                $sheet->mergeCells ($merge);
            }
        }
    }

    public function transferdetailsdownload(Request $request) {
    	$hdn_empid = explode(',', $request->hdn_empid_arr);
    	if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->selYear = $date_month[0];
			$request->selMonth = $date_month[1];
		}
		$template_name = 'resources/assets/uploadandtemplates/templates/salary_details.xls';
        $tempname = "Salary+_".$request->selMonth.'_'.$request->selYear;
        $excel_name=$tempname;
		Excel::load($template_name, function($objTpl) use($request, $hdn_empid) {
			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');

			if (!isset($hdn_empid) && $hdn_empid == '') {
				$hdn_empid = array();
			}
			
	    	$salPlus = self::getSalaryDetailsTotal($hdn_empid,$request->selYear,$request->selMonth);
			$salArr = $salPlus['salArr'];
			$salArrTot = $salPlus['salArrTot'];

	        $x = 4;
	        $start_pos = $x;
	        $y = 1;
	        $z = $x + count($salArr);
	        	
	        $objTpl->getActiveSheet()->getStyle('A3:E3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        	$grandtotalval = 0;
        	foreach ($salArr as $key => $value) {

        		$grandtotalval += $value['grandTotal'];

        		$objTpl->getActiveSheet()->getRowDimension($x)->setRowHeight(28);
        		$objTpl->getActiveSheet()->getStyle('A'.$x.':E'.$x)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        		$objTpl->getActiveSheet()->getStyle('A'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        		$objTpl->getActiveSheet()->getStyle('A'.$x)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('C'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('D'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            $objTpl->getActiveSheet()->getStyle('E'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	            // $objTpl->getActiveSheet()->setCellValue('A'.$x, $y);
	            $objTpl->getActiveSheet()->setCellValue('A'.$x,'=ROW()-3');
	            $objTpl->getActiveSheet()->getStyle('B'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	            $objTpl->getActiveSheet()->setCellValue('B'.$x, $value['Emp_ID']);
	            $objTpl->getActiveSheet()->getStyle('E'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	            $objTpl->getActiveSheet()->setCellValue('C'.$x, strtoupper($value['FirstName'].' '.$value['LastName']));
	            $objTpl->getActiveSheet()->setCellValue('D'.$x, $value['KanaFirstName'].' '.$value['KanaLastName']);
	            $objTpl->getActiveSheet()->setCellValue('E'.$x, number_format($value['grandTotal']));
            	$objTpl->getActiveSheet()->getStyle('E'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	            $x++;
	            $y++;
        	}
        	$end_pos = $x-1;

	        $objTpl->getActiveSheet()->getStyle('A3:E3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
	        $objTpl->getActiveSheet()->getRowDimension($z)->setRowHeight(30);
	        $objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getFont()->setBold(true);
        	$objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        	$objTpl->getActiveSheet()->getStyle('D'.$z.':'.'E'.$z)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
	        $objTpl->getActiveSheet()->setCellValue('D'.$z, "合計");
			// $objTpl->getActiveSheet()->setCellValue('E'.$z, number_format($grandtotalval));
			$objTpl->getActiveSheet()->setCellValue('E'.$z,'=SUM(E'.$start_pos.':'.'E'.$end_pos.')');
        	$objTpl->getActiveSheet()->getStyle('D'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        	$objTpl->getActiveSheet()->getStyle('E'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        	$objTpl->getActiveSheet()->mergeCells('B1:C1');
	        $objTpl->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        	$objTpl->getActiveSheet()->mergeCells('B2:C2');
        	$objTpl->getActiveSheet()->getCell('B1')->setValue('株式会社Microbit');
	        $objTpl->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	        $objTpl->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
	        
        	$objTpl->getActiveSheet()->getCell('D1')->setValue('給料月:   '.$request->selYear.'-'.$request->selMonth.' '.'振込日:   '.date('Y-m-d'));
        	$objTpl->getActiveSheet()->setTitle('Salary_'.$request->selMonth.'_'.$request->selYear);
        	$objTpl->setActiveSheetIndex();
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
        	$flpath='.xls';
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="'.$flpath.'"');
        	header('Cache-Control: max-age=0');
        })->setFilename($excel_name)->download('xls');
    }

    public function salaryplusPayrollSingleDownload(Request $request) {
    	$template_name = 'resources/assets/uploadandtemplates/templates/salary_plus_details_single_history.xls';
    	if ($request->get_prev_yr == 1) {
			$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
			$date_month = date('Y-m', $prev_month_ts);
			$date_month = explode('-', $date_month);
			$request->selYear = $date_month[0];
			$request->selMonth = $date_month[1];
		}
    	$excel_name='Salary+_History_'.strtoupper($request->lastname).'_'.$request->selYear;
		Excel::load($template_name, function($objTpl) use($request) {

			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');

        	$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
		    $salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
		    $g_query=SalaryCalcplus::salaryDetailhistory($request,1);
		    $k = 6;
			$i = 1;
			$fixed_column_1 = 4;
			$fixed_column_2 = 5;
			// For Salary Details
			if (count($salary_det) != "") {
 				for ($a = 0; $a < count($salary_det); $a++) {
 					$record = self::num_to_letters($fixed_column_1);
 					self::commonHeaders($objTpl,$record,$fixed_column_2,$salary_det[$a]->Name,'C0C0C0', 10);
					$fixed_column_1++;
 				}
 				// For Total Payment
 				$record = self::num_to_letters($fixed_column_1);
 				self::commonHeaders($objTpl,$record,$fixed_column_2,'支給合計','FFC107', 12);
 				$fixed_column_1 = $fixed_column_1 + 1;

 			}
			// For Salary Deduction
			if (count($salary_ded) != "") {
 				for ($b = 0; $b < count($salary_ded); $b++) {
 					$record = self::num_to_letters($fixed_column_1);
 					self::commonHeaders($objTpl,$record,$fixed_column_2,$salary_ded[$b]->Name,'C0C0C0', 10);
					$fixed_column_1++;
 				}
 				// For Total Payment
 				$record = self::num_to_letters($fixed_column_1);
 				self::commonHeaders($objTpl,$record,$fixed_column_2,'控除合計','FFC107', 12);
 				$fixed_column_1 = $fixed_column_1 + 1;

 			}
 			// For Travel Amount
 			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'Travel','C0C0C0', 10);
			$fixed_column_1 = $fixed_column_1 + 1;
			// For Total Travel Amount
			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'合計','FFC107', 12);
			$fixed_column_1 = $fixed_column_1 + 1;
			// For Total Amount
			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'合計 金額','A3CECA', 12);

			$travel_tot = '';
			foreach ($g_query as $key => $value) {
        		$objTpl->getActiveSheet()->getRowDimension($k)->setRowHeight(22.5);
				$objTpl->getActiveSheet()->setCellValue('A'.$k, $i);
				$objTpl->getActiveSheet()->getStyle('A'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$objTpl->getActiveSheet()->setCellValue('B'.$k, $value->year.'-'.date('m',mktime(0, 0, 0, $value->month)));
				$objTpl->getActiveSheet()->getStyle('B'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$objTpl->getActiveSheet()->setCellValue('C'.$k, $value->date);
				$objTpl->getActiveSheet()->getStyle('C'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				// For Salary Details Payment
				$sal_final_arr = array();
				if ($value->Salary != '') {
					$Salary = explode('##', mb_substr($value->Salary, 0, -2));
					foreach ($Salary as $key_valsal => $value_valsal) {
						$sal_final = explode('$', $value_valsal);
						$sal_final_arr += array($sal_final[0] => $sal_final[1]);
					}
				}
				$l = 4;
				$amt_sal = '';
				foreach ($salary_det as $key_sal => $value_sal) {
					$record = self::num_to_letters($l);
					$amt_sal += (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?$sal_final_arr[$value_sal->Salarayid]:'';
					$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($sal_final_arr[$value_sal->Salarayid]) && $sal_final_arr[$value_sal->Salarayid] != 0)?number_format($sal_final_arr[$value_sal->Salarayid]):'');
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$l++;
				}
				// For Total Payment Amount
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($amt_sal) && $amt_sal != 0)?number_format($amt_sal):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				// For Salary Deduction
				$ded_final_arr = array();
				if ($value->Deduction != '') {
					$Deduction = explode('##', mb_substr($value->Deduction, 0, -2));
					foreach ($Deduction as $key_valded => $value_valded) {
						$ded_final = explode('$', $value_valded);
						$ded_final_arr += array($ded_final[0] => $ded_final[1]);
					}
				}

				$amt_ded = '';
				foreach ($salary_ded as $key_sal => $value_ded) {
					$record = self::num_to_letters($l);
					$amt_ded += (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?$ded_final_arr[$value_ded->Salarayid]:'';
					$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($ded_final_arr[$value_ded->Salarayid]) && $ded_final_arr[$value_ded->Salarayid] != 0)?number_format($ded_final_arr[$value_ded->Salarayid]):'');
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$l++;
				}
				// For Total Deduction Amount
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($amt_ded) && $amt_ded != 0)?number_format($amt_ded):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				// For Travel
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value->Travel) && $value->Travel != 0)?number_format($value->Travel):'');
				$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
				$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$l = $l + 1;
				// For Travel Total Amount
				$travel_tot += $value->Travel;
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value->Travel) && $value->Travel != 0)?number_format($value->Travel):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				// For Total Amount
				$tot_amt_fnl = '';
				$tot_amt_fnl = $amt_sal + $amt_ded + $value->Travel;
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($tot_amt_fnl) && $tot_amt_fnl != 0)?number_format($tot_amt_fnl):'');
				self::commonTotal($objTpl,$record,$k,'A3CECA');

				$k++;
				$i++;
			}

			// For Overall Total Process
			$empArr = array($request->Emp_ID);
			$salPlus = self::getSalaryDetailsTotal($empArr,$request->selYear);
			$salArrTot = $salPlus['salArrTot'];

			// For Overall Total Process
			$objTpl->getActiveSheet()->getRowDimension($k)->setRowHeight(22.5);
			$objTpl->getActiveSheet()->mergeCells('A'.$k.':C'.$k);
			$objTpl->getActiveSheet()->getStyle('A'.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
			$objTpl->getActiveSheet()->getStyle('A'.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle('A'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->setCellValue('A'.$k, '合計');
			$fixed_column = 4;

			// For Salary Details Total Amount ColumnWise
			$tot_sub_det = '';
			if (count($salary_det) != "") {
				for ($i = 0; $i < count($salary_det); $i++) {
					$record = self::num_to_letters($fixed_column);
					if (isset($salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid]) && $salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid] != '') {
						$tot_sub_det += $salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid];
						$objTpl->getActiveSheet()->setCellValue($record.$k, number_format($salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid]));
					} else {
						$objTpl->getActiveSheet()->setCellValue($record.$k, '');
					}
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
					$fixed_column++;
				}
			}
			// For Salary Details Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($tot_sub_det) && $tot_sub_det != '')?number_format($tot_sub_det):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Salary Deduction Total Amount ColumnWise
			$tot_sub_ded = '';
			if (count($salary_ded) != "") {
				for ($j = 0; $j < count($salary_ded); $j++) {
					$record = self::num_to_letters($fixed_column);
					if (isset($salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid]) && $salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid] != '') {
						$tot_sub_ded += $salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid];
						$objTpl->getActiveSheet()->setCellValue($record.$k, number_format($salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid]));
					} else {
						$objTpl->getActiveSheet()->setCellValue($record.$k, '');
					}
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
					$fixed_column++;
				}
			}
			// For Salary Details Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($tot_sub_ded) && $tot_sub_ded != '')?number_format($tot_sub_ded):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Travel Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($travel_tot) && $travel_tot != '')?number_format($travel_tot):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'C0C0C0')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($travel_tot) && $travel_tot != '')?number_format($travel_tot):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Grand Total
			$grand_total = '';
			$grand_total = $tot_sub_det + $tot_sub_ded + $travel_tot;
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($grand_total) && $grand_total != '')?number_format($grand_total):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'A3CECA')
						)
					)
				);


			$objTpl->getActiveSheet()->freezePane('A5');
			$objTpl->getActiveSheet()->freezePane('B5');
			$objTpl->getActiveSheet()->freezePane('C5');
			$objTpl->getActiveSheet()->freezePane('D5');
			$objTpl->getActiveSheet()->setCellValue('C3', $request->Emp_ID);
			$objTpl->getActiveSheet()->setCellValue('E3', ucwords(strtolower($request->lastname))
					. ".".ucwords(mb_substr($request->firstname, 0, 1, 'utf-8')));


			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');
			$objTpl->getActiveSheet()->setTitle('Salary_'.$request->selYear.'_'.$request->selMonth);
        	$flpath='.xls';
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="'.$flpath.'"');
        	header('Cache-Control: max-age=0');
		})->setFilename($excel_name)->download('xls');
    }

    public function gensenDownload(Request $request) {

		$template_name = 'resources/assets/uploadandtemplates/templates/gensen_details.xls';
		$excel_name ='Gensen_'.strtoupper($request->lastname).'_'.$request->selYear;
		Excel::load($template_name, function($objPHPExcel) use($request) {
			
			$companyDetails = SalaryCalcplus::fnGetCompanyDetails($request);
			$empdetail = SalaryCalcplus::fnGetEmpDetail($request);
			$firstname = ($empdetail[0]->FirstName) ? $empdetail[0]->FirstName : "" ;
			$lastname = ($empdetail[0]->LastName) ? $empdetail[0]->LastName : "" ;
			$DOB = str_replace("-","/",($empdetail[0]->DOB) ? $empdetail[0]->DOB : "");
			$FatherDOB = str_replace("-","/",($empdetail[0]->FatherDOB) ? $empdetail[0]->FatherDOB : "");
			$MotherDOB = str_replace("-","/",($empdetail[0]->MotherDOB) ? $empdetail[0]->MotherDOB : "");
			$return_address = ($empdetail[0]->Address1) ? $empdetail[0]->Address1 : "" ;
			if (is_numeric(trim($return_address))) {
				$oldAddress = SalaryCalcplus::fnGetAddressMB($return_address);
				if (isset($oldAddress[0])) {
					$return_address = '〒'.$oldAddress[0]->pincode.' '.$oldAddress[0]->jpstate.$oldAddress[0]->jpaddress.' - '.$oldAddress[0]->roomno;
				} else {
					$return_address = '';
				}
			}else{
				$return_address = $return_address;
			}
			

			$address = $return_address." ".$firstname." ".$lastname; 
			
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue("E9", $return_address);
			$objPHPExcel->getActiveSheet()->setCellValue("E10", ($companyDetails[0]->companyName) ? $companyDetails[0]->companyName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("E11", ($companyDetails[0]->companyNumber) ? $companyDetails[0]->companyNumber : "");
			$objPHPExcel->getActiveSheet()->setCellValue("E12", ($companyDetails[0]->TEL) ? $companyDetails[0]->TEL : "");
			$objPHPExcel->getActiveSheet()->setCellValue("I13", ($companyDetails[0]->referencenumber) ? $companyDetails[0]->referencenumber : "");

			$objPHPExcel->getActiveSheet()->setCellValue("M11", ($empdetail[0]->KanaFirstName) ? $empdetail[0]->KanaFirstName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("N11", ($empdetail[0]->KanaLastName) ? $empdetail[0]->KanaLastName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("M12", $firstname);
			$objPHPExcel->getActiveSheet()->setCellValue("N12", $lastname);
			$objPHPExcel->getActiveSheet()->setCellValue("O12", $DOB);
			$objPHPExcel->getActiveSheet()->getStyle("M13")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue("M13", $address);

			$objPHPExcel->getActiveSheet()->setCellValue("D17", ($empdetail[0]->FatherName) ? $empdetail[0]->FatherName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("E17", ".");
			$objPHPExcel->getActiveSheet()->setCellValue("F17", ($empdetail[0]->FatherkanaName) ? $empdetail[0]->FatherkanaName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("J17", $FatherDOB);
			$objPHPExcel->getActiveSheet()->setCellValue("D18", ($empdetail[0]->MotherName) ? $empdetail[0]->MotherName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("E18", ".");
			$objPHPExcel->getActiveSheet()->setCellValue("F18", ($empdetail[0]->MotherkanaName) ? $empdetail[0]->MotherkanaName : "");
			$objPHPExcel->getActiveSheet()->setCellValue("J18", $MotherDOB);

			$objPHPExcel->setActiveSheetIndex(0);
          	$objPHPExcel->getActiveSheet()->setSelectedCells("A1");
		})->setFilename($excel_name)->download('xls');
	}

    function commonTotal($objTpl,$record,$k,$color=null) {
    	$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
									array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
								);
		$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => $color)
							)
						)
					);
    }

    function commonHeaders($objTpl,$record,$fixed_column_2,$Name,$color, $width) {
    	$objTpl->getActiveSheet()->getColumnDimension($record)->setWidth($width);
		$objTpl->getActiveSheet()->setCellValue($record.$fixed_column_2, $Name);
		$objTpl->getActiveSheet()->getStyle($record.$fixed_column_2)->getAlignment()->setWrapText(true); 
		$objTpl->getActiveSheet()->getStyle($record.$fixed_column_2)->getAlignment()->applyFromArray(
							array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
						);
		$objTpl->getActiveSheet()->getStyle($record.$fixed_column_2)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => $color)
							)
						)
					);
		$objTpl->getActiveSheet()->getStyle($record.$fixed_column_2)->getFont()->setBold( true );
		$objTpl->getActiveSheet()->getStyle($record.$fixed_column_2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }

	function num_to_letters($n) {
		$n -= 1;
		for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
		$r = chr($n % 26 + 0x41) . $r;
		return $r;
	}

	// Start Madasamy 31/07/2020
	public function historyTotal(Request $request) {

		if ($request->selYear == "") {
			$request->selYear = date('Y');
		}

		$salary_det=SalaryCalcplus::getsalaryDetailsnodelflg($request,'1');
		$salary_ded=SalaryCalcplus::getsalaryDetailsnodelflg($request,'2');
		$salresult = array();
		$dedresult = array();
		$empArr = array();
		$empArrVal = SalaryCalcplus::fnGetEmpId($request);
		foreach ($empArrVal as $key => $value) {
			$empArr[] = $value->Emp_ID;
		}
		$salArr = array();
		$dataArr = array();
		$year = $request->selYear;
		$i = 0;

		// Used for YearWise Details
		$salPlus = self::getSalaryDetailsTotal($empArr,$year);
		$salArr = $salPlus['salArr'];
		$temp_salaryDetails = $salPlus['salArrTot']['temp_salaryDetails'];
		$temp_salaryDetails_DD = $salPlus['salArrTot']['temp_salaryDetails_DD'];
		$tot_travel_amt = $salPlus['salArrTot']['tot_travel_amt'];
		$salresult = $salPlus['salArrTot']['salresult'];
		$dedresult = $salPlus['salArrTot']['dedresult'];

		// year bar process
		$cur_year = date('Y');
		$curtime = date('YmdHis');
		$yearArr = SalaryCalcplus::getYearsTotalHistory($request);
		$prev_yrs = array();
		$total_yrs = array();

		foreach ($yearArr as $value) {
			if ($value->years > 0) {
				$prev_yrs[] = $value->years;
				$total_yrs[] = $value->years;
			}
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

		return view('salarycalcplus.historytotal',['salary_det' => $salary_det,
													'salary_ded' => $salary_ded,
													'salresult' => $salresult,
													'dedresult' => $dedresult,
													'tot_travel_amt'=>$tot_travel_amt,
													'temp_salaryDetails'=>$temp_salaryDetails,
													'temp_salaryDetails_DD'=>$temp_salaryDetails_DD,
													'cur_year' =>  $cur_year,
													'curtime' =>  $curtime,
													'prev_yrs' =>  $prev_yrs,
													'total_yrs' =>  $total_yrs,
													'selectedYear' =>  $selectedYear,
													'salArr' => $salArr,
													'request' => $request]);
		
	}

	// Common Function for SalaryDetails
	public function getSalaryDetailsTotal($empArr,$year,$month = ""){
		$a = 0;
		$get_master_tot = array();
		$get_master_tot1 = array();
		$tot_travel_amt = '';
		$tot_salary_amt = '';
		$tot_deduct_amt = '';
		$salArr = array();
		$salArrTot = array();
		$salPlus = array();
		$dataArr = array();

		$salary_det=SalaryCalcplus::getsalaryDetailsnodelflg('','1');
		$salary_ded=SalaryCalcplus::getsalaryDetailsnodelflg('','2');
		$salresult = array();
		$dedresult = array();
		$i = 0;
		foreach ($empArr as $key => $value) {
			$a = 0;
			$get_master_tot = array();
			$get_master_tot1 = array();
			$tot_travel_amt = '';
			$tot_salary_amt = '';
			$tot_deduct_amt = '';
			// $dataArr = "";
			$dataArr[$value] = SalaryCalcplus::fnGetEmpSalHistory($value,$year,$month);
			$salArr[$value]['id'] = '';

			$salDetalilsTotal = array();
			$dedDetalilsTotal = array();
			$salArrKey = array();
			$deducArrKey = array();
			foreach ($dataArr[$value] as $salKey => $empSal) {

				// For Index page monthwise
				if ($month !== "") {
					$salArr[$value]['id'] = $empSal->id;
				}
				
				//For Travel Details
				if ($empSal->Travel != '') {
					$tot_travel_amt += $empSal->Travel;
				}

				//For Salary Details
	    		$arr1 = array();
	    		$arr2 = array();
	    		$sal_arr = array();
	    		$salary = '';
	    		if ($empSal->Salary != '') {
					$Salary = explode('##', mb_substr($empSal->Salary, 0, -2));
					foreach ($Salary as $key => $value_key) {
						$sal_final = explode('$', $value_key);
						$arr1[$key] = $sal_final[0];
						$arr2[$sal_final[0]] = $sal_final[1];
					}
	    		}
	    		if(count($salary_det) != "") {
	        		foreach ($salary_det as $key1 => $det) {
	        			$sal_arr[$det->Salarayid] = $det->Salarayid;
	        		}
	    		}
	    		$salresult_a=array_intersect($sal_arr,$arr1);
	    		$salresult_b=array_diff($sal_arr,$arr1);
	    		$salresult = array_merge($salresult_a,$salresult_b);
	    		ksort($salresult);
		    	if(count($salary_det)!="" && is_array($salresult)) {
		    		$x = 0;
					foreach ($salresult as $key2 => $value2) {
						if ($key2 != '') {
			    			if($key2 == isset($arr2[$key2])) {
			    				$salary += $arr2[$key2];
			    				$get_master_tot[$a][$key2] = $arr2[$key2];
			    			} else {
			    				$get_master_tot[$a][$key2] = 0;
			    			}
						}

		    			$x++;
		    			$salDetalilsTotal[$key2] = array_sum(array_column($get_master_tot,$value2));
		    		}
				}
				$tot_salary_amt += $salary;

				// Salary Deduction
	    		$arr3 = array();
	    		$arr4 = array();
	    		$ded_arr = array();
	    		$deduction = '';
	    		if ($empSal->Deduction != '') {
					$Deduction = explode('##', mb_substr($empSal->Deduction, 0, -2));
					foreach ($Deduction as $key => $value_key) {
						$ded_final = explode('$', $value_key);
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
						if ($key2 != '') {
			    			if($key2 == isset($arr4[$key2])) {
			    				$deduction += $arr4[$key2];
			    				$get_master_tot1[$a][$key2] = $arr4[$key2];
			    			}
		    			}
		    			$y++;
		    			$dedDetalilsTotal[$key2] = array_sum(array_column($get_master_tot1,$value2));
		    		}
				}
				$tot_deduct_amt += $deduction;

				$a++;

				$salArrKey = array_merge($salArrKey,$arr1);
				$deducArrKey = array_merge($deducArrKey,$arr3);
			}

			$salArrKey = array_unique($salArrKey);
			foreach ($salDetalilsTotal as $keySal => $keyVal) {
				if (!in_array($keySal, $salArrKey)) {
					unset($salDetalilsTotal[$keySal]);
				}
			}

			$deducArrKey = array_unique($deducArrKey);
			foreach ($dedDetalilsTotal as $keyDed => $keyVal) {
				if (!in_array($keyDed, $deducArrKey)) {
					unset($dedDetalilsTotal[$keyDed]);
				}
			}

			$salArr[$value]['Emp_ID'] = $value;
			$empName = SalaryCalcplus::fnGetEmpName($value);
			if (!empty($empName)) {
				$salArr[$value]['FirstName'] = $empName[0]->FirstName;
				$salArr[$value]['LastName'] = $empName[0]->LastName;
				$salArr[$value]['KanaFirstName'] = $empName[0]->KanaFirstName;
				$salArr[$value]['KanaLastName'] = $empName[0]->KanaLastName;

				$salArr[$value]['totSalary'] = $tot_salary_amt;
				$salArr[$value]['salDetTotal'] = $salDetalilsTotal;
				$salArr[$value]['dedDetTotal'] = $dedDetalilsTotal;
				$salArr[$value]['totDetuct'] = $tot_deduct_amt;
				$salArr[$value]['totTravel'] = $tot_travel_amt;
				$salArr[$value]['grandTotal'] = $tot_salary_amt + $tot_deduct_amt + $tot_travel_amt;
				$salArr[$value]['mailFlg'] = SalaryCalcplus::fnGetmailFlg($value);

			} else {
				$salArr[$value]['totSalary'] = "";
				$salArr[$value]['salDetTotal'] = "";
				$salArr[$value]['dedDetTotal'] = "";
				$salArr[$value]['totDetuct'] = "";
				$salArr[$value]['totTravel'] = "";
				$salArr[$value]['grandTotal'] = "";
				$salArr[$value]['mailFlg'] = "";
			}

			$i++;

			// Check Salary Exist or Not
			if(empty($dataArr[$value])){
				$salArr[$value]['salExist'] = false;
			} else{
				$salArr[$value]['salExist'] = true;
			} 
		}

		// Total Salary
		$salaryDetails = array();
		$tot_travel_amt = "";
		foreach ($salArr as $key => $sal) {
			if (isset($sal['salDetTotal']) && $sal['salDetTotal'] != "") {
				foreach ($sal['salDetTotal'] as $key_sid => $amount) {
					$salaryDetails[$key_sid][] = $amount;
				}
				$tot_travel_amt += $sal['totTravel'];
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
		
		// Total Salary Deduction
		$salaryDetails_DD = array();
		foreach ($salArr as $key_DD => $deduc) {
			if (isset($deduc['dedDetTotal']) && $deduc['dedDetTotal'] != "") {
				foreach ($deduc['dedDetTotal'] as $key_sid_DD => $amount_DD) {
					$salaryDetails_DD[$key_sid_DD][] = $amount_DD;
				}
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

		$salArrTot['temp_salaryDetails'] = $temp_salaryDetails;
		$salArrTot['temp_salaryDetails_DD'] = $temp_salaryDetails_DD;
		$salArrTot['tot_travel_amt'] = $tot_travel_amt;
		$salArrTot['salresult'] = $salresult;
		$salArrTot['dedresult'] = $dedresult;

		$salPlus['salArr'] = $salArr;
		$salPlus['salArrTot'] = $salArrTot;
		return $salPlus;
	}

	// Used For MonthWise and YearWise payroll excel download 
	public function salaryplusdownload(Request $request) {

    	$hdn_empid = explode(',', $request->hdn_empid_arr);
    	$template_name = 'resources/assets/uploadandtemplates/templates/salary_details_plus_index.xls';
    	if (!isset($request->payrollExcel)) {
    		// for historyTotal page payroll
        	$excel_name='Salary+_'.$request->selYear;
    	} else {
    		// for index page payroll
    		if ($request->get_prev_yr == 1) {
				$prev_month_ts = strtotime($request->selYear.'-'.$request->selMonth.' -1 month');
				$date_month = date('Y-m', $prev_month_ts);
				$date_month = explode('-', $date_month);
				$request->selYear = $date_month[0];
				$request->selMonth = $date_month[1];
			}
    		$excel_name='Salary+_'.$request->selYear.'_'.$request->selMonth;
    	}
		Excel::load($template_name, function($objTpl) use($request, $hdn_empid) {
			
			// Get salary Details
			if (!isset($request->payrollExcel)) {
				$salPlus = self::getSalaryDetailsTotal($hdn_empid,$request->selYear);
			} else {
				$salPlus = self::getSalaryDetailsTotal($hdn_empid,$request->selYear,$request->selMonth);
			}
			$salArr = $salPlus['salArr'];
			$salArrTot = $salPlus['salArrTot'];

			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');

			$salary_det=SalaryCalcplus::getsalaryDetails($request,'1');
			$salary_ded=SalaryCalcplus::getsalaryDetails($request,'2');
			$k = 5;
			$i = 1;
			$fixed_column_1 = 4;
			$fixed_column_2 = 4;
			// For Salary Details
			if (count($salary_det) != "") {
 				for ($a = 0; $a < count($salary_det); $a++) {
 					$record = self::num_to_letters($fixed_column_1);
 					self::commonHeaders($objTpl,$record,$fixed_column_2,$salary_det[$a]->Name,'C0C0C0', 12);
					$fixed_column_1++;
 				}
 				// For Total Payment
 				$record = self::num_to_letters($fixed_column_1);
 				self::commonHeaders($objTpl,$record,$fixed_column_2,'支給合計','FFC107', 14);
 				$fixed_column_1 = $fixed_column_1 + 1;

 			}
			// For Salary Deduction
			if (count($salary_ded) != "") {
 				for ($b = 0; $b < count($salary_ded); $b++) {
 					$record = self::num_to_letters($fixed_column_1);
 					self::commonHeaders($objTpl,$record,$fixed_column_2,$salary_ded[$b]->Name,'C0C0C0', 12);
					$fixed_column_1++;
 				}
 				// For Total Payment
 				$record = self::num_to_letters($fixed_column_1);
 				self::commonHeaders($objTpl,$record,$fixed_column_2,'控除合計','FFC107', 14);
 				$fixed_column_1 = $fixed_column_1 + 1;

 			}
 			// For Travel Amount
 			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'Travel','C0C0C0', 12);
			$fixed_column_1 = $fixed_column_1 + 1;
			// For Total Travel Amount
			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'合計','FFC107', 14);
			$fixed_column_1 = $fixed_column_1 + 1;
			// For Total Amount
			$record = self::num_to_letters($fixed_column_1);
			self::commonHeaders($objTpl,$record,$fixed_column_2,'合計 金額','A3CECA', 14);

			$travel_tot = '';
			foreach ($salArr as $key => $value) {
				$fullname = $value['FirstName'] . " " . $value['LastName']."\n";
           		$kananame = $value['KanaFirstName'] . " " . $value['KanaLastName'];
        		$objTpl->getActiveSheet()->getRowDimension($k)->setRowHeight(40);
				$objTpl->getActiveSheet()->setCellValue('A'.$k, $i);
				$objTpl->getActiveSheet()->getStyle('A'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$objTpl->getActiveSheet()->setCellValue('B'.$k, $value['Emp_ID']);
				$objTpl->getActiveSheet()->getStyle('B'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				// $objTpl->getActiveSheet()->setCellValue('C'.$k, ucwords(strtolower($value['LastName']))
				// 	. ".".ucwords(mb_substr($value['FirstName'], 0, 1, 'utf-8')));
				$objTpl->getActiveSheet()->setCellValue('C'.$k, stripslashes(strtoupper($fullname)).$kananame);
				$objTpl->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setWrapText(true);
				$objTpl->getActiveSheet()->getStyle('C'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$l = 4;
				$amt_sal = '';
				foreach ($salary_det as $key_sal => $value_sal) {
					$record = self::num_to_letters($l);
					$amt_sal += (!empty($value['salDetTotal'][$value_sal->Salarayid]) && $value['salDetTotal'][$value_sal->Salarayid] != 0) ? $value['salDetTotal'][$value_sal->Salarayid]:'';
					$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value['salDetTotal'][$value_sal->Salarayid]) && $value['salDetTotal'][$value_sal->Salarayid] != 0)?number_format($value['salDetTotal'][$value_sal->Salarayid]):'');
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$l++;
				}
				// For Total Payment Amount
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($amt_sal) && $amt_sal != 0)?number_format($amt_sal):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				$amt_ded = '';
				foreach ($salary_ded as $key_sal => $value_ded) {
					$record = self::num_to_letters($l);
					$amt_ded += (!empty($value['dedDetTotal'][$value_ded->Salarayid]) && $value['dedDetTotal'][$value_ded->Salarayid] != 0)?$value['dedDetTotal'][$value_ded->Salarayid]:'';
					$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value['dedDetTotal'][$value_ded->Salarayid]) && $value['dedDetTotal'][$value_ded->Salarayid] != 0)?number_format($value['dedDetTotal'][$value_ded->Salarayid]):'');
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$l++;
				}
				// For Total Deduction Amount
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($amt_ded) && $amt_ded != 0)?number_format($amt_ded):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				// For Travel
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value['totTravel']) && $value['totTravel'] != 0)?number_format($value['totTravel']):'');
				$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
				$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$l = $l + 1;
				// For Travel Total Amount
				$travel_tot += $value['totTravel'];
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($value['totTravel']) && $value['totTravel'] != 0)?number_format($value['totTravel']):'');
				self::commonTotal($objTpl,$record,$k,'FFC107');
				$l = $l + 1;

				// For Total Amount
				$tot_amt_fnl = '';
				$tot_amt_fnl = $amt_sal + $amt_ded + $value['totTravel'];
				$record = self::num_to_letters($l);
				$objTpl->getActiveSheet()->setCellValue($record.$k, (!empty($tot_amt_fnl) && $tot_amt_fnl != 0)?number_format($tot_amt_fnl):'');
				self::commonTotal($objTpl,$record,$k,'A3CECA');

				$k++;
				$i++;
			}

			// For Overall Total Process
			$objTpl->getActiveSheet()->getRowDimension($k)->setRowHeight(22.5);
			$objTpl->getActiveSheet()->mergeCells('A'.$k.':C'.$k);
			$objTpl->getActiveSheet()->getStyle('A'.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
			$objTpl->getActiveSheet()->getStyle('A'.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle('A'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->setCellValue('A'.$k, '合計');
			$fixed_column = 4;

			// For Salary Details Total Amount ColumnWise
			$tot_sub_det = '';
			if (count($salary_det) != "") {
				for ($i = 0; $i < count($salary_det); $i++) {
					$record = self::num_to_letters($fixed_column);
					if (isset($salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid]) && $salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid] != '') {
						$tot_sub_det += $salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid];
						$objTpl->getActiveSheet()->setCellValue($record.$k, number_format($salArrTot['temp_salaryDetails'][$salary_det[$i]->Salarayid]));
					} else {
						$objTpl->getActiveSheet()->setCellValue($record.$k, '');
					}
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
					$fixed_column++;
				}
			}
			// For Salary Details Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($tot_sub_det) && $tot_sub_det != '')?number_format($tot_sub_det):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Salary Deduction Total Amount ColumnWise
			$tot_sub_ded = '';
			if (count($salary_ded) != "") {
				for ($j = 0; $j < count($salary_ded); $j++) {
					$record = self::num_to_letters($fixed_column);
					if (isset($salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid]) && $salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid] != '') {
						$tot_sub_ded += $salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid];
						$objTpl->getActiveSheet()->setCellValue($record.$k, number_format($salArrTot['temp_salaryDetails_DD'][$salary_ded[$j]->Salarayid]));
					} else {
						$objTpl->getActiveSheet()->setCellValue($record.$k, '');
					}
					$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
										array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
									);
					$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
					$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'C0C0C0')
								)
							)
						);
					$fixed_column++;
				}
			}
			// For Salary Details Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($tot_sub_ded) && $tot_sub_ded != '')?number_format($tot_sub_ded):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Travel Amount
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($travel_tot) && $travel_tot != '')?number_format($travel_tot):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'C0C0C0')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($travel_tot) && $travel_tot != '')?number_format($travel_tot):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFC107')
						)
					)
				);
			$fixed_column = $fixed_column + 1;

			// For Grand Total
			$grand_total = '';
			$grand_total = $tot_sub_det + $tot_sub_ded + $travel_tot;
			$record = self::num_to_letters($fixed_column);
			$objTpl->getActiveSheet()->setCellValue($record.$k, (isset($grand_total) && $grand_total != '')?number_format($grand_total):'');
			$objTpl->getActiveSheet()->getStyle($record.$k)->getAlignment()->applyFromArray(
								array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,)
							);
			$objTpl->getActiveSheet()->getStyle($record.$k)->getFont()->setBold( true );
			$objTpl->getActiveSheet()->getStyle($record.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle($record.$k)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'A3CECA')
						)
					)
				);

			$objTpl->getActiveSheet()->freezePane('A5');
			$objTpl->getActiveSheet()->freezePane('B5');
			$objTpl->getActiveSheet()->freezePane('C5');
			$objTpl->getActiveSheet()->freezePane('D5');
			if (!isset($request->payrollExcel)) {
				$objTpl->getActiveSheet()->setCellValue('C2', $request->selYear.'年分   給与一覧');
			} else {
				$objTpl->getActiveSheet()->setCellValue('C2', $request->selYear.'年 '.$request->selMonth.'月分   給与一覧');
			}
			$objTpl->setActiveSheetIndex(0);
        	$objTpl->getActiveSheet(0)->setSelectedCells('A1');
        	if (!isset($request->payrollExcel)) {
				$objTpl->getActiveSheet()->setTitle('Salary_'.$request->selYear);
			} else {
				$objTpl->getActiveSheet()->setTitle('Salary_'.$request->selYear.'_'.$request->selMonth);
			}
        	$flpath='.xls';
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="'.$flpath.'"');
        	header('Cache-Control: max-age=0');
        	
        })->setFilename($excel_name)->download('xls');
    }
	// End Madasamy 31/07/2020

}