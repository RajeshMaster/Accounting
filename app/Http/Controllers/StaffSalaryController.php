<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\StaffSalary;
use App\Http\Common;
use Session;
use Carbon;
use Redirect;
Class StaffSalaryController extends Controller {
	public function index(Request $request)
	{
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$year_month = array();
		$db_year_month = array();
		$dbrecord = array();
		$dbnext = array();
		$dbprevious = array();
		$account_val ="";
		$displayArray = array();
		$get_pre_value = "";
		$index="";
		$total_allow=array();
		$k = 0;
		$i = 0;
		$salaryTotal = 0;
		$otTotal = 0;
		$travelTotal = 0;
		$othersTotal = 0;
		$main5Total = 0; 
		$main6Total = 0;
		$main7Total = 0;
		$main8Total = 0;
		$main9Total = 0;
		$main10Total = 0;
		$totalamount =0;
		$total_sal = 0;
		$grand_total = 0;
		$salaryTotalvalue =0;
		$salary_tot_value =array();
		$salary_tot_amount =array();
		$get_det = array();

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
			$g_accountperiod = Staffsalary::fnGetAccountPeriod();
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

			$year_month = array();
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
			
			$exp_query = StaffSalary::fnGetmnthRecord($from_date, $to_date);
			$dbrecord = array();
			foreach ($exp_query as $key => $res1) {
				$concat = $res1->year_ln.'-'.$res1->month_ln;
				//array_push($dbrecord, $res1['start_date']);
				array_push($dbrecord, $concat);
			}

			$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
				array_push($dbrecord, $lastMonthAsLink);
			$exp_query1 = StaffSalary::fnGetmnthRecordPrevious($from_date);
			$dbprevious = array();
			foreach ($exp_query1 as $key => $res2) {
				array_push($dbprevious, ($res2->year_ln."-".$res2->month_ln));
			}

			$exp_query2 = StaffSalary::fnGetmnthRecordNext($to_date);
			$dbnext = array();
			foreach ($exp_query2 as $key => $res3) {
				array_push($dbnext,($res3->year_ln."-".$res3->month_ln));
			}
			// print_r($dbnext); exit();

			$dbrecord = array_unique($dbrecord);
			$db_year_month = array();
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$split_val = explode("-",$dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			// print_r($db_year_month); exit();
			
			$split_date = explode('-', $date_month);

			$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
				if ($request->selYear=="") {
					$g_query = StaffSalary::salaryDetail($request,$split_date[0],$split_date[1]);
				} else {
					$g_query = StaffSalary::salaryDetail($request,$request->selYear,substr("0" . $request->selMonth, -2));
				}
			$previous_year_month = array();
			$previous_year_month[0] = date('Y-m', strtotime(date('Y-m')." -1 month"));
			$previous_year_month[1] = date ('Y-m', strtotime ( '+1 month' , strtotime ( $previous_year_month[0]."-01" )));
			$previous_year_month[2]= date ('Y-m', strtotime ( '-1 month' , strtotime ( $previous_year_month[0]."-01" )));
			
			$salTotal = 0;
			$salarytotamt = 0;
			$ottotamt = 0;
			$traveltotamt = 0;
			$otherstotamt = 0;
			$main5totamt = 0;
			$main6totamt = 0;
			$main7totamt = 0;
			$main8totamt = 0;
			$main9totamt = 0;
			$main10totamt = 0;
			$salary_tot = array();

		foreach ($g_query as $key => $value) {
				$get_det[$k]['id'] = $value->id;
				$get_det[$k]['grand_total'] = $value->grand_total;
				$get_det[$k]['Emp_ID'] = $value->Emp_ID;
				$get_det[$k]['month_ln'] = $value->month_ln;
				$get_det[$k]['year_ln'] = $value->year_ln;
				$get_det[$k]['FirstName'] = $value->FirstName;
				$get_det[$k]['LastName'] = $value->LastName;
				$get_det[$k]['DOJ'] = $value->DOJ;
				$get_det[$k]['copy_month_flg'] = $value->copy_month_flg;
				$amt = preg_replace("/,/", "", $value->grand_total);
				//$salaryDetailSql = StaffSalary::salaryDetailsByEmpid($get_det[$k]['Emp_ID'],$date_month);
				//print_r($salaryDetailSql);exit();
				$totalamount +=$amt;
				$mainArray = array("salary1","salary2","salary3","salary4","salary5",
						"salary6","salary7","salary8","salary9","salary10");
				for ($x=0; $x < 10; $x++) { 
					$sal_total = $value->$mainArray[$x];
				}
					$salaryTotalvalue += $sal_total;
					$grand_total=0;

				for ($i=0; $i < 10; $i++) { 
					$salaryRowName = $i+1;
					$salaryRowName = "salary".$salaryRowName;
					$salaryTotal+= str_replace(',', '', $value->$salaryRowName);
					if ($salaryTotal != 0 && $salaryTotal != "") {
						$total_allow[0]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$otRowName = $i+1;
					$otRowName = "ot".$otRowName;
					$otTotal+= str_replace(',', '', $value->$otRowName);
					if ($otTotal != 0 && $otTotal != "") {
						$total_allow[1]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$travelRowName = $i+1;
					$travelRowName = "travel".$travelRowName;
					$travelTotal+= str_replace(',', '', $value->$travelRowName);
					if ($travelTotal != 0 && $travelTotal != "") {
						$total_allow[2]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$othersRowName = $i+1;
					$othersRowName = "others".$othersRowName;
					$othersTotal+= str_replace(',', '', $value->$othersRowName);
					if ($othersTotal != 0 && $othersTotal != "") {
						$total_allow[3]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main5RowName = $i+1;
					$main5RowName = "main5_".$main5RowName;
					$main5Total+= str_replace(',', '', $value->$main5RowName);
					if ($main5Total != 0 && $main5Total != "") {
						$total_allow[4]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main6RowName = $i+1;
					$main6RowName = "main6_".$main6RowName;
					$main6Total+= str_replace(',', '', $value->$main6RowName);
					if ($main6Total != 0 && $main6Total != "") {
						$total_allow[5]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main7RowName = $i+1;
					$main7RowName = "main7_".$main7RowName;
					$main7Total+= str_replace(',', '', $value->$main7RowName);
					if ($main7Total != 0 && $main7Total != "") {
						$total_allow[6]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main8RowName = $i+1;
					$main8RowName = "main8_".$main8RowName;
					$main8Total+= str_replace(',', '', $value->$main8RowName);
					if ($main8Total != 0 && $main8Total != "") {
						$total_allow[7]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main9RowName = $i+1;
					$main9RowName = "main9_".$main9RowName;
					$main9Total+= str_replace(',', '', $value->$main9RowName);
					if ($main9Total != 0 && $main9Total != "") {
						$total_allow[8]['allow'] = "allow";
					}
				}
				for ($i=0; $i < 10; $i++) { 
					$main10RowName = $i+1;
					$main10RowName = "main10_".$main10RowName;
					$main10Total+= str_replace(',', '', $value->$main10RowName);
					if ($main10Total != 0 && $main10Total != "") {
						$total_allow[9]['allow'] = "allow";
					}
				}
				$get_det[$k][0] = $salaryTotal;
				$salTotal+= $salaryTotal;
				$salarytotamt += $salaryTotal;
				$salary_tot_amount[0] = $salarytotamt;
				
				$get_det[$k][1] = $otTotal;
				$salTotal += $otTotal;
				$ottotamt += $otTotal;
				$salary_tot_amount[1] = $ottotamt;
				
				$get_det[$k][2] = $travelTotal;
				$salTotal += $travelTotal;
				$traveltotamt += $travelTotal;
				$salary_tot_amount[2] = $traveltotamt;
				
				$get_det[$k][3] = $othersTotal;
				$salTotal += $othersTotal;
				$otherstotamt += $othersTotal;
				$salary_tot_amount[3] = $otherstotamt;

				$get_det[$k][4] = $main5Total;
				$salTotal += $main5Total;
				$main5totamt += $main5Total;
				$salary_tot_amount[4] = $main5totamt;

				$get_det[$k][5] = $main6Total;
				$salTotal += $main6Total;
				$main6totamt += $main6Total;
				$salary_tot_amount[5] = $main6totamt;

				$get_det[$k][6] = $main7Total;
				$salTotal += $main7Total;
				$main7totamt += $main7Total;
				$salary_tot_amount[6] = $main7totamt;

				$get_det[$k][7] = $main8Total;
				$salTotal += $main8Total;
				$main8totamt += $main8Total;
				$salary_tot_amount[7] = $main8totamt;

				$get_det[$k][8] = $main9Total;
				$salTotal += $main9Total;
				$main9totamt +=$main9Total;
				$salary_tot_amount[8] = $main9totamt;

				$get_det[$k][9] = $main10Total;
				$salTotal += $main10Total;
				$main10totamt += $main10Total;
				$salary_tot_amount[9] = $main10totamt;		

				$get_det[$k]['Total'] = $salTotal;
				$get_det[$k]['status'] = $value->status;
				$salary_tot[]+=$salTotal;

				$salaryTotal=0;$othersTotal=0;$travelTotal=0;$otTotal=0;
				$main5Total=0;$main6Total=0;$main7Total=0;$main8Total=0;$main9Total=0;$main10Total=0;
				for($j=0; $j<count($previous_year_month);$j++) {
					if($previous_year_month[$j] == $date_month){ 
						$get_det[$k]['copy'] ="0";
					} else {
						$get_det[$k]['copy'] ="1";
					}
				}
				$k++;
				}
				$j=0;
				for($i=0; $i < count($salary_tot_amount); $i++) {
					if($salary_tot_amount[$i] != 0 ) {
						$salary_tot_value[$j] = $salary_tot_amount[$i];
						$j++;
					}
				}
				$cur_year=date('Y');
				$cur_month=date('m')-1;
				if ($cur_month==0) {
					$cur_month = 12;
					$cur_year = $cur_year-1;
				}
				if (isset($request->selMonth)) {
					$selectedMonth=$request->selMonth;
					$selectedYear=$request->selYear;					
					$cur_month=$selectedMonth;
					$cur_year=$selectedYear;
				} else {
					$selectedMonth=$cur_month;
					$selectedYear=$cur_year;
				}
				$settingQuery = StaffSalary::fnGetSettingsDetails();
				$settingDetails = array();
				$settingDetail = array();
				$s=0;$mainflg_i = 0;
				foreach ($settingQuery as $key => $value) {
					$settingDetails[$s]['id'] = $value->id;
					if (Session::get('languageval') == "en") {
						$settingDetails[$s]['mainField'] =$value->main_eng;
						$settingDetails[$s]['subField']= $value->sub_eng;
					} else {
						$settingDetails[$s]['mainField'] =$value->main_jap;
						$settingDetails[$s]['subField']= $value->sub_jap;
					}
					$settingDetails[$s]['maindelflg']= $value->maindelflg;
					$settingDetails[$s]['subdelflg']= $value->subdelflg;
					if (!isset($settingDetails[$s-1]['mainField'])) {
						$settingDetails[$s-1]['mainField'] = "";
					}
						if ($settingDetails[$s]['mainField'] != $settingDetails[$s-1]['mainField']) {
							$settingDetail[$mainflg_i]['mainflg'] = $settingDetails[$s]['mainField'];
							$mainflg_i++;
						}
					$s++;
				}
				$mon = "月";
				return view('StaffSalary.index',['request'=> $request,
												'settingDetail'=>$settingDetail,
												'account_val'=>$account_val,
												'account_period'=> $account_period,
												'year_month'=> $year_month,
												'db_year_month'=> $db_year_month,
												'date_month'=> $date_month,
												'dbnext'=> $dbnext,
												'mon' => $mon,
												'get_det' => $get_det,
												'dbprevious'=> $dbprevious,
												'last_year'=> $last_year,
												'current_year'=> $current_year,
												'index'=>$index,
												'salary_tot_value'=>$salary_tot_value,
												'salary_tot'=>$salary_tot,
												'g_query'=>$g_query,
												'total_allow' => $total_allow,
												'settingDetails' => $settingDetails,
												'grand_total' => $grand_total,
												'totalval' => $totalamount,
												'salaryTotalvalue' => $salaryTotalvalue										]);
	}
	public static function getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period) {
		$arr_yr_mn = array_keys($year_month);
		if( $account_close_mn == 12 ) {
			$yr_mn = $arr_yr_mn[0];
		} else {
			$yr_mn = $arr_yr_mn[1];
		}
		if( $account_close_yr >  $yr_mn) {
			$diff = $account_close_yr -$yr_mn;
			$account_val = $account_period-$diff;
		} else if($account_close_yr <  $yr_mn) {
			$diff = $yr_mn-$account_close_yr;
			$account_val = $account_period+$diff;
		} else {
			$account_val = $account_period;
		}
		return $account_val;
	}
	public function fngetviewdata(Request $request)
	{
		$fngetviewdata = StaffSalary::fngetviewdata($request);
		return view('StaffSalary.index',['request'=>$request,
											  'fngetviewdata'=>$fngetviewdata]);
	}
	public function salaryview(Request $request)
	{
		if(!isset($request->viewid))
		{
			return $this->index($request);
		}
		$s = 0;
		$mainflg_i = 0;
		$rowVal=array();
		$settingQuery = StaffSalary::fnGetSettingsDetails();
		
		if (Session::get('languageval') == "en") {
			$mainField = "main_eng";
			$subField = "sub_eng";
		} else {
			$mainField = "main_jap";
			$subField = "sub_jap";
		}
		$salaryview = StaffSalary::salaryview($request);
		$disp = 1;
		$empid=$request->empid;
		$empdet = Staffsalary::salaryview($request);
		$month_val = $_REQUEST['selMonth'];
		$year_val = $_REQUEST['selYear'];
		$empid = $empdet[0]->Emp_ID;

		if (!isset($_REQUEST['selMonth'])) { 
			//$date_month = date('Y-m');
			$date_month = date('Y-m', strtotime("last month"));
		} else { 
			$date_month = $_REQUEST['selYear'] . "-" . substr("0" . $_REQUEST['selMonth'] , -2);
		}
		$settingQuery = StaffSalary::fnGetSettingsDetails();
		$settingSql = StaffSalary::fnGetMainSettingsDetails();
		$settingDetails = array();
		$s=0;$mf = 0;
		foreach ($settingQuery as $key => $value) {
			$settingDetails[$s]['id'] = $value->id;
			$settingDetails[$s]['mainField'] =$value->$mainField;
			$settingDetails[$s]['subField']= $value->$subField;
			$settingDetails[$s]['maindelflg']= $value->maindelflg;
			$settingDetails[$s]['subdelflg']= $value->subdelflg;
			
			if (isset($settingDetails[$s-1])) {
			if ($settingDetails[$s]['mainField'] != $settingDetails[$s-1]['mainField']) {
				$settingDetails[0]['mainflg']= $settingDetails[0]['maindelflg'].$s."-";	
			}
			}
			if ($settingDetails[$s]['subdelflg'] == 0) {
				$settingDetails[0]['delflg']= $settingDetails[0]['subdelflg'].$s."-";	
			}
			if (isset($settingDetails[$s-1])) {
			if ($settingDetails[$s]['mainField'] != $settingDetails[$s-1]['mainField']) {
				$mainflg[$mf] = $s;
				$mf++;
			}
			}
			$s++;
		}
		$s=0;
		foreach($settingSql as $key => $row) {
			$s1=$s+1;
			$settingMainDetails[$s]['mainField'] =$row->$mainField;
			$settingMainDetails[$s]['maindelflg']= $row->delflg;
			if(isset($settingMainDetails[$s1]['maindelflg']))
			{ 
				if ($settingMainDetails[$s1]['maindelflg'] == 1) {
					 $settingMainDetails[0]['mainflg']= $settingMainDetails[0]['mainflg'].$s1."-";	
				}
			}
			$s++;
		}
		$mainflg[$mf] = count($settingDetails);
		$settingDetails[0]['mainflg']= $settingDetails[0]['mainflg'].count($settingDetails);
		$salaryDetailSql = StaffSalary::salaryDetailsByEmpid($empdet[0]->Emp_ID,$date_month);
		$i = 0;
		$salaryTotal = 0;
		$salTotal = 0;
		$otTotal = 0;
		$travelTotal = 0;
		$othersTotal = 0;
		$main5Total = 0;
		$main6Total = 0;
		$main7Total = 0;
		$main8Total = 0;
		$main9Total = 0;
		$main10Total = 0;
		$field = 0;
		$get_det = array();
		$get_detailstot = array();
		$get_details_total = array();
		foreach ($salaryDetailSql as $key => $row) {
			if ($request->edit_mode == "") {
				for ($cnt=0; $cnt < 1; $cnt++) { 
					if(isset($view_mode))
						{
							if ($view_mode != 0) {
								break;
						}
					}
					$rowName = $i+1;
					$salaryRowName = "salary".$rowName;
					$rowVal[$i]['salary'] =  $row->$salaryRowName;
					$otRowName = "ot".$rowName;
					$rowVal[$i]['ot']= $row->$otRowName;
					$travelRowName = "travel".$rowName;
					$rowVal[$i]['travel']= $row->$travelRowName;
					$othersRowName = "others".$rowName;
					$rowVal[$i]['others']= $row->$othersRowName;
					$main5RowName = "main5_".$rowName;
					$rowVal[$i]['main5']= $row->$main5RowName;
					$main6RowName = "main6_".$rowName;
					$rowVal[$i]['main6']= $row->$main6RowName;
					$main7RowName = "main7_".$rowName;
					$rowVal[$i]['main7']= $row->$main7RowName;
					$main8RowName = "main8_".$rowName;
					$rowVal[$i]['main8']= $row->$main8RowName;
					$main9RowName = "main9_".$rowName;
					$rowVal[$i]['main9']= $row->$main9RowName;
					$main10RowName = "main10_".$rowName;
					$rowVal[$i]['main10']= $row->$main10RowName;
					if (($rowVal[$cnt]['salary'] == "") && ($rowVal[$cnt]['ot'] == "") 
						&& ($rowVal[$cnt]['travel'] == "") && ($rowVal[$cnt]['others'] == "")
						&& ($rowVal[$cnt]['main5'] == "") && ($rowVal[$cnt]['main6'] == "")
						&& ($rowVal[$cnt]['main7'] == "") && ($rowVal[$cnt]['main8'] == "")
						&& ($rowVal[$cnt]['main9'] == "") && ($rowVal[$cnt]['main10'] == "")
						&& ($rowVal[$cnt]['status'] == 1)) {
						$view_mode = 1;
					} else {
						$view_mode = 2;break;
					}
				}
				
			}
			$rowcountsalary = 0;
			for ($r1=0; $r1 < 10; $r1++) { 
				$rowName = $r1+1;
				$salaryRowName = "salary".$rowName;
				$sRowName = "Salary".$rowName;
				if(isset($salaryTotal)){ 
				$salaryTotal+= str_replace(',', '', $row->$salaryRowName);
				}
				$get_det[$i][$sRowName] =  $row->$salaryRowName;
				if($get_det[$i][$sRowName] != "" && $r1<$mainflg[1]) {
					if(isset($salArray)){ 
						$salArray[0] = $salArray[0].$r1."-";
					}
				}
				if ($get_det[$i][$sRowName] != "") {
					$rowcountsalary += 1;
				}
			}
			$rowcountot = 0;
			for ($r2=0; $r2 < 10; $r2++) { 
				$rowName = $r2+1;
				$otRowName = "ot".$rowName;
				$oRowName = "OverTime".$rowName;
				$otTotal+= str_replace(',', '', $row->$otRowName);
				$get_det[$i][$oRowName]= $row->$otRowName;
				if($get_det[$i][$oRowName] != "") { 
					$var = $r2+$mainflg[1];
					if($var<$mainflg[2] && $var>=$mainflg[1]) {
						if(isset($salArray)){ 
						$salArray[0] = $salArray[0].$var."-";
						}
					}
				}
				if ($get_det[$i][$oRowName] != "") {
					$rowcountot += 1;
				}
			}
			$rowcounttravel = 0;
			for ($r3=0; $r3 < 10; $r3++) { 
				$rowName = $r3+1;
				$travelRowName = "travel".$rowName;
				$tRowName = "Travel".$rowName;
				$travelTotal+= str_replace(',', '', $row->$travelRowName);
				$get_det[$i][$tRowName]= $row->$travelRowName;
				if($get_det[$i][$tRowName] != "") {
					$var = $r3+$mainflg[2];
					if($var<$mainflg[3] && $var>=$mainflg[2]) {
						if(isset($salArray)){ 
							$salArray[0] = $salArray[0].$var."-";
						}
					}
				}
				if ($get_det[$i][$tRowName] != "") {
					$rowcounttravel += 1;
				}
			}
			$rowcountothers = 0;
			for ($r4=0; $r4 < 10; $r4++) { 
				$rowName = $r4+1;
				$othersRowName = "others".$rowName;
				$otrsRowName = "Others".$rowName;
				$othersTotal+= str_replace(',', '', $row->$othersRowName);
				$get_det[$i][$otrsRowName]= $row->$othersRowName;
				if($get_det[$i][$otrsRowName] != "") {
					$var = $r4+$mainflg[3];
					if($var<$mainflg[4] && $var>=$mainflg[3]) {
						if(isset($salArray)){ 
							$salArray[0] = $salArray[0].$var."-";
						}
					}
				}
				if ($get_det[$i][$otrsRowName] != "") {
					$rowcountothers += 1;
				}
			}
			$rowcountmain5 = 0;
			for ($r5=0; $r5 < 10; $r5++) { 
				$rowName = $r5+1;
				$main5RowName = "main5_".$rowName;
				$m5RowName = "Main5_".$rowName;
				$main5Total+= str_replace(',', '', $row->$main5RowName);
				$get_det[$i][$m5RowName]= $row->$main5RowName;
				if($get_det[$i][$m5RowName] != "") {
					$var = $r5+$mainflg[4];
					if($var<$mainflg[5] && $var>=$mainflg[4])
						if(isset($salArray)){ 
							$salArray[0] = $salArray[0].$var."-";
						}
				}
				if ($get_det[$i][$m5RowName] != "") {
					$rowcountmain5 += 1;
				}
			}
			$rowcountmain6 = 0;
			for ($r6=0; $r6 < 10; $r6++) { 
				$rowName = $r6+1;
				$main6RowName = "main6_".$rowName;
				$m6RowName = "Main6_".$rowName;
				$main6Total+= str_replace(',', '', $row->$main6RowName);
				$get_det[$i][$m6RowName]= $row->$main6RowName;
				if($get_det[$i][$m6RowName] != "") {
					 $var = $r6+$mainflg[5];
					if (isset($mainflg[6])) {
						$var<$mainflg[6] && $var>=$mainflg[5];
						if (isset($salArray[0])) {
							$salArray[0] = $salArray[0].$var."-";
						 } 
					}
				}
				if ($get_det[$i][$m6RowName] != "") {
					$rowcountmain6 += 1;
				}
			}
			$rowcountmain7 = 0;
			for ($r7=0; $r7 < 10; $r7++) { 
				$rowName = $r7+1;
				$main7RowName = "main7_".$rowName;
				$m7RowName = "Main7_".$rowName;
				$main7Total+= str_replace(',', '', $row->$main7RowName);
				$get_det[$i][$m7RowName]= $row->$main7RowName;
				if($get_det[$i][$m7RowName] != "") {
					$var = $r7+$mainflg[6];
					if($var<$mainflg[7] && $var>=$mainflg[6])
					{
					$salArray[0] = $salArray[0].$var."-";
					}
				}
				if ($get_det[$i][$m7RowName] != "") {
					$rowcountmain7 += 1;
				}
			}
			$rowcountmain8 = 0;
			for ($r8=0; $r8 < 10; $r8++) { 
				$rowName = $r8+1;
				$main8RowName = "main8_".$rowName;
				$m8RowName = "Main8_".$rowName;
				$main8Total+= str_replace(',', '', $row->$main8RowName);
				$get_det[$i][$m8RowName]= $row->$main8RowName;
				if($get_det[$i][$m8RowName] != "") {
					$var = $r8+$mainflg[7];
					if($var<$mainflg[8] && $var>=$mainflg[7])
					$salArray[0] = $salArray[0].$var."-";
				}
				if ($get_det[$i][$m8RowName] != "") {
					$rowcountmain8 += 1;
				}
			}
			$rowcountmain9 = 0;
			for ($r9=0; $r9 < 10; $r9++) { 
				$rowName = $r9+1;
				$main9RowName = "main9_".$rowName;
				$m9RowName = "Main9_".$rowName;
				$main9Total += str_replace(',', '', $row->$main9RowName);
				$get_det[$i][$m9RowName]= $row->$main9RowName;
				if($get_det[$i][$m9RowName] != "") {
					$var = $r9+$mainflg[8];
					if($var<$mainflg[9] && $var>=$mainflg[8])
					$salArray[0] = $salArray[0].$var."-";
				}
				if ($get_det[$i][$m9RowName] != "") {
					$rowcountmain9 += 1;
				}
			}
			$rowcountmain10 = 0;
			for ($r10=0; $r10 < 10; $r10++) { 
				$rowName = $r10+1;
				$main10RowName = "main10_".$rowName;
				$m10RowName = "Main10_".$rowName;
				$main10Total += str_replace(',', '', $row->$main10RowName);
				$get_det[$i][$m10RowName]= $row->$main10RowName;
				if($get_det[$i][$m10RowName] != "") {
					$var = $r10+$mainflg[9];
					if($var<$mainflg[10] && $var>=$mainflg[9]) {
						$salArray[0] = $salArray[0].$var."-";
					}
				}
				if ($get_det[$i][$m10RowName] != "") {
					$rowcountmain10 += 1;
				}
			}
			$get_detailstot[$i]['total_1'] = $salaryTotal;
			$salTotal += $salaryTotal;

			$get_detailstot[$i]['total_2'] = $otTotal;
			$salTotal += $otTotal;

			$get_detailstot[$i]['total_3'] = $travelTotal;
			$salTotal += $travelTotal;

			$get_detailstot[$i]['total_4'] = $othersTotal;
			$salTotal += $othersTotal;

			$get_detailstot[$i]['total_5'] = $main5Total;
			$salTotal += $main5Total;

			$get_detailstot[$i]['total_6'] = $main6Total;
			$salTotal += $main6Total;

			$get_detailstot[$i]['total_7'] = $main7Total;
			$salTotal += $main7Total;

			$get_detailstot[$i]['total_8'] = $main8Total;
			$salTotal += $main8Total;

			$get_detailstot[$i]['total_9'] = $main9Total;
			$salTotal += $main9Total;

			$get_detailstot[$i]['total_10'] = $main10Total;
			$salTotal += $main10Total;

			$get_details_total[$i]['Total'] = $salTotal;

			$get_det[$i]['status'] = $row->status;

			$get_det[$i]['remarks'] = $row->remarks;

			$i++;
		}
			$fordisplayatlast = array($rowcountsalary,$rowcountot,$rowcounttravel,$rowcountothers,$rowcountmain5,$rowcountmain6,$rowcountmain7,$rowcountmain8,$rowcountmain9,$rowcountmain10);

		// print_r($get_details_total);	
		return view('StaffSalary.Singleview',['request'=>$request,
											 'salaryview'=>$salaryview,
											 'settingDetails' =>$settingDetails,
											 'settingSql' => $settingSql,
											 'get_det' => $get_det,
											 'field' => $field,
											 'fordisplayatlast' => $fordisplayatlast,
											 'get_details_total' => $get_details_total,
											 'get_detailstot' => $get_detailstot,
											 'salaryDetailSql' => $salaryDetailSql]);
	
}
	function viewsalary(Request $request) {
			if (!isset($request->empid)) {
				return Redirect::to('StaffSalary/index?mainmenu='.$request->mainmenu.
					'&time='.date('YmdHis'));
			}
			$empid=$request->empid;
			$DOJ=$request->doj;
			$current_date=date('Y-m-d');
			$date1 = $DOJ;
			$date2 = $current_date;
			$date1=date ('Y-m-d', strtotime ( '-1 month' , strtotime ( $date1)));
			$date2=date ('Y-m-d', strtotime ( '+1 month' , strtotime ( $date2)));
			$ts1 = strtotime($date1);
			$ts2 = strtotime($date2);
			$year1 = date('Y', $ts1);
			$year2 = date('Y', $ts2);
			$month1 = date('m', $ts1);
			$month2 = date('m', $ts2);
			$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
			$i=0;
			$x=0;
			$get_det= array();
			$mainArray = array();
			$total_sal=0;
			$total_diff=0;
			$total_bill=0;
			$grand_total = 0;
			$i=0;
			for ($j = 0; $j <$diff; $j++) {
				$futuremonth = date ('Y-m-d', strtotime ( '-1 month' , strtotime ( $date2)));
				$date2 = $futuremonth;
				$splitYrMn = explode("-", $date2);
				$date3= $splitYrMn[0]."年".$splitYrMn[1]."月";
				$staffview=StaffSalary::Staffsalaryview($request,$empid,$splitYrMn[0],$splitYrMn[1]);
				$get_det[$j]['date']=$date3;
				foreach ($staffview as $key => $value) {
					$mainArray = array("MainTotal1","MainTotal2","MainTotal3","MainTotal4","MainTotal5","MainTotal6","MainTotal7","MainTotal8","MainTotal9","MainTotal10");
					for ($x=0; $x < 10; $x++) { 
				    	$grand_total += $value->$mainArray[$x];
				    }
				    $get_det[$i]['salary']= $grand_total;
				    $total_sal += $grand_total;
				    $grand_total=0;
				   	$amt = preg_replace("/,/", "", $value->Amount);
			     	$otamt = preg_replace("/,/", "", $value->OTAmount);
					$get_det[$i]['billing']=$amt+$otamt;
					$total_bill += $amt+$otamt;
					$get_det[$i]['cname']=$value->cname;
				}
				$i++;
			}
		if (Session::get('languageval') == "en") {
			$mainField = "main_eng";
			$subField = "sub_eng";
		} else {
			$mainField = "main_jap";
			$subField = "sub_jap";
		}
		$settingQuery = StaffSalary::salaryprocessdetail($request);
		$settingDetails = array();
		$s=0;
			foreach ($settingQuery as $key => $setSal) {
			$settingDetails[$s]['mainField'] =$setSal->$mainField;
			$s++;
		}
			return view('StaffSalary.viewsalary',['staffview'=>$staffview,
													'request'=>$request,
													'getdata'=>$get_det,
													'empid'=>$empid,
													'doj'=>$DOJ,
												'total_sal'=>$total_sal,
												'total_bill'=>$total_bill,
												'settingDetails'=>$settingDetails]);
			}
			function salarystaff_ajax(Request $request) {
				$rslt="";
				$empid= $request->empid;
      			$yrmnth= $request->empdate;
      			$yr=substr($yrmnth, 0,4);
      			$mnth=substr($yrmnth,7,2);
      			$result=StaffSalary::view_salarybill_detail($empid,$yr,$mnth);
      			foreach ($result as $key => $viewquery) {
			$mainArray = array("MainTotal1","MainTotal2","MainTotal3","MainTotal4","MainTotal5","MainTotal6","MainTotal7","MainTotal8","MainTotal9","MainTotal10");
		    for ($x=0; $x < 10; $x++) { 
		    	$val = $mainArray[$x]; 
		    	if ($viewquery->$val=="") {
		    		$viewquery->$val=0;
		    	} else {
		    		$rslt .=  $viewquery->$val."$$";
		    	}
		    }
		    echo $rslt;
		    if ($viewquery->Amount == "") {
		    	$amt_val=0;
		    } else {
		    	$amt_val = $viewquery->Amount;
		    }
		     if ($viewquery->OTAmount == "") {
		    	$otamt_val=0;
		    } else {
		    	$otamt_val = $viewquery->OTAmount;
		    }
			$rslt .= "^".$amt_val."##".$otamt_val;
		}
		echo $rslt;
     	exit;
	}
}
