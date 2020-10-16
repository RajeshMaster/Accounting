<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Common;
use App\Model\Expdetails;
use Session;
use DateTime;
use DateInterval;
use DatePeriod;
class ExpensesDetailsController extends Controller {
	function index(Request $request) { 
		// print_r($_REQUEST);exit();
		$grndmontotal = array();
		$grndmontotals = 0;
		$disabledmonthly="";
		$disabledcustomer="";
		$disabledcurrentyear="";
		$disabledsen="";
		$disabledman="";
		$disabledyen="";
		$splityear="";
		$date_month="";
		$account_val="";
		$get_det = array();
		if(!isset($request->active_select) || $request->active_select=="") {
			$request->active_select=3;
			$disabledcurrentyear="disabled fb";
			$fil=1;
		} else if($request->active_select==1) {
	        $fil=1;
	        $disabledmonthly="disabled fb";
		} elseif($request->active_select==2) {
	        $fil=1;
	        $disabledcustomer="disabled fb";
		} elseif($request->active_select==3) {
	        $fil=1;
	        $disabledcurrentyear="disabled fb";
		}
		if(!isset($request->filter) || $request->filter=="") {
			$fil=3;
	        $disabledyen="disabled fb";
		} elseif ($request->filter==1 && $request->firstclick=="") {
	        $fil=1;
	        $disabledman="disabled fb";
		} elseif ($request->filter==2 && $request->firstclick=="") {
	        $fil=2;
	        $disabledsen="disabled fb";
		} elseif ($request->filter==3 && $request->firstclick=="") {
	        $fil=3;
	        $disabledyen="disabled fb";
		}

		if ($request->active_select == 3) {
			if (!isset($request->selMonth)) {
				$date_month = date('Y-m');
			} else { 
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
			$last=date('Y-m', strtotime('last month'));
			$last1 = date($date_month , strtotime($last . " last month"));
			$lastdate = explode('-',$last1);
			$lastyear =$lastdate[0];
			$lastmonth =$lastdate[1]; 
			$e_accountperiod = Expdetails::fnGetAccountPeriod();
			$account_close_yr = $e_accountperiod[0]->Closingyear;
			$account_close_mn = $e_accountperiod[0]->Closingmonth;
			$account_period = intval($e_accountperiod[0]->Accountperiod);
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
				$currYr = Expdetails::datapreyear();
				if($currYr[0]->date){
					if (substr($currYr[0]->date,5,2)> $account_close_mn) {
					    $current_year = substr($currYr[0]->date,0,4)+1;
						$last_year =substr($currYr[0]->date,0,4);
					} else {
					    $current_year = substr($currYr[0]->date,0,4);
						$last_year = substr($currYr[0]->date,0,4) - 1;
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
			}
				$year_monthslt = array();
				if ($account_close_mn == 12) {
					for ($i = 1; $i <= 12; $i++) {
						$year_monthslt[$current_year][$i] = $i;
					}
				} else {
					for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
						$year_monthslt[$last_year][$i] = $i;
					}

					for ($i = 1; $i <= $account_close_mn; $i++) {
						$year_monthslt[$current_year][$i] = $i;
					}
				}
			$year_month_day = $current_year . "-" . $account_close_mn . "-01";
			$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
			$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
			$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
			$from_date=substr($from_date, 0,7);
			$to_date=substr($to_date,0,7);
			$cur=date('Y-m-d');
			$cur_time =new DateTime($cur);
			$from_time =new DateTime($from_date);
			$to_time =new DateTime($to_date);
			if($cur_time>=$from_time && $cur_time<=$to_time ) {
				$curDate=date('Y-m');
			} else{
				$curDate="";
			}
			$exp_execute = Expdetails::yrmndetail($from_date, $to_date,"","");
			$dbrecord = array();
			if (isset($exp_execute)) {
				foreach ($exp_execute as $key => $res1) {
		    		$res1= $res1->date;
		    		array_push($dbrecord, $res1);
		    	}
			}
			
		    $bktr_execute1 = Expdetails::yrmndetail($from_date,"","","");
			$dbprevious = array();
			if (isset($bktr_execute1)) {
				foreach ($bktr_execute1 as $key => $res2) {
		    		$res2= $res2->date;
		    		array_push($dbprevious, $res2);
		    	}
			}
			$bktr_execute2 = Expdetails::yrmndetail("",$to_date,"","");
			$dbnext = array();
			if (isset($bktr_execute2)) {
				foreach ($bktr_execute2 as $key => $res3) {
		    		$res3= $res3->date;
		    		array_push($dbnext, $res3);
		    	}
			}
			$dbrecord = array_unique($dbrecord);
			$db_year_month = array();
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
					$split_val = explode("-", $dbrecordvalue);
					$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			$split_date = explode('-', $date_month);
			$account_val = Common::getAccountPeriod($year_monthslt, $account_close_yr, $account_close_mn, $account_period);
			$yrmn_exe = Expdetails::yrmndetail($from_date,$to_date,$curDate,"");
			// print_r($yrmn_exe);exit();
			$i=0;
			$last_date = "";
			$det = array();
			if(isset($yrmn_exe)){
				foreach ($yrmn_exe as $key => $row) {
		    		$det[$i] =$row->date;
					$last_date=$row->date;
					$i++;
		    	}
			}
			$start    = new DateTime(isset($det[0]) ? $det[0] : '');
			$start->modify('first day of this month');
			$end      = new DateTime($last_date);
			$end->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);
			$k=0;
			foreach ($period as $dt) {
			  $date[$k]=$dt->format("Y-m");
			    $dateindex[$k]=$dt->format("Y/m");
			    $k++;
			}
			$date_cnt =count($date);
			$expensequery = Expdetails::expensedetail($from_date,$to_date,$curDate);
			$m=0;
			$grandTotal=0;
			$utf_lngth = "";
			if($request->filter){
					$fil=$request->filter;
			} else {
				$fil=3;
			}
			if ($fil==1) {
				$div =10000;
				$pix="70px";
			} else if($fil==2) {
				$div =1000;
				$pix="90px";
			} else {
				$div =1;
				$pix="100px";
			}
			if($expensequery) {
				foreach($expensequery as $object) {
					 $convarray[] = (array) $object;
				}
				if (isset($convarray[0]['ERROR_FLG'])) {
					if($convarray[0]['ERROR_FLG'] == 1) {
					} 
				} else {
				foreach ($convarray as $key => $value1) {
					if (Session::get('languageval') == "en") {
						$get_det[$m]['Subject']=$value1['Subject'];
						$get_det[$m]['sub']=$value1['sub_eng'];
						$utf_lngth =12;
					} else {
						$get_det[$m]['Subject']=$value1['Subject_jp'];
						$get_det[$m]['sub']=$value1['sub_jap'];
						$utf_lngth =9;
					}
					$get_det[$m]['id']=$value1['id'];
					$get_det[$m]['subid']=$value1['subid'];
					for ($d=0; $d < $date_cnt; $d++) { 
						$dates= $date[$date_cnt-$d-1];
						if(!isset($value1[$dates])) {
							$value1[$dates] = "";
						}
						if($value1[$dates]){
							$get_det[$m][$dates]=$value1[$dates]/$div;
							$get_det[$m][$dates]=number_format($get_det[$m][$dates]);
							$get_det[$m][$dates]=str_replace(",","", $get_det[$m][$dates]);
						} else {
							$get_det[$m][$dates]="";
						}
						$grandTotal=$grandTotal+$get_det[$m][$dates];
					}
					$m++;
				}
				}
			}
			$avg=number_format($grandTotal/$date_cnt);
			$fileCnt=count($get_det);
			for ($cnt=0;$cnt<$fileCnt;$cnt++) {
				for ($d=0;$d<$date_cnt;$d++) {
					$dates=$date[$date_cnt-$d-1];
					if(!isset($grndmontotal[$d])) {
						$grndmontotal[$d] = 0;
					}
					$grndmontotal[$d] =$grndmontotal[$d]+$get_det[$cnt][$dates];
				}
			}
			// print_r($get_det);exit();
			return view('ExpensesDetails.index',['request' => $request,
											'disabledsen' => $disabledsen,
											'disabledman' => $disabledman,
											'disabledyen' => $disabledyen,
											'disabledmonthly' => $disabledmonthly,
											'disabledcustomer' => $disabledcustomer,
											'disabledcurrentyear' => $disabledcurrentyear,
											'account_period' => $account_period,
											'year_monthslt' => $year_monthslt,
											'db_year_month' => $db_year_month,
									 		'date_month' => $date_month,
											'dbnext' => $dbnext,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' => $account_val,
											'dateindex' => $dateindex,
											'utf_lngth' => $utf_lngth,
											'get_det' => $get_det,
											'date' => $date,
											'date' => $date,
											'pix' => $pix,
											'date_cnt' => $date_cnt,
											'fileCnt' => $fileCnt,
											'avg' => $avg,
											'grndmontotal' => $grndmontotal,
											'grandTotal' => $grandTotal]);
		} else if ($request->active_select == 2) {
			if($request->filter){
					$fil=$request->filter;
			} else {
				$fil=3;
			}
			if ($fil==1) {
				$div =10000;
				$pix="70px";
			} else if($fil==2) {
				$div =1000;
				$pix="90px";
			} else {
				$div =1;
				$pix="100px";
			}
			$e_accountperiod = Expdetails::fnGetAccountPeriod();
			$account_start_yr = $e_accountperiod[0]->Startingyear;
			$account_start_mn = $e_accountperiod[0]->Startingmonth;
			$account_close_yr = $e_accountperiod[0]->Closingyear;
			$account_close_mn = $e_accountperiod[0]->Closingmonth;
			$account_period = intval($e_accountperiod[0]->Accountperiod);
			$strt_yrmnth =$account_start_yr."-".$account_start_mn;
			$close_yrmnth =$account_close_yr."-".$account_close_mn;
			$data_query = Expdetails::yrmndetail("","","",1);
			$i=0;
			$date =array();
			foreach ($data_query as $key => $value3) {
				$date[$i] =$value3->date;
				$start_date =$value3->date;
				$i++;
			}
			/*trial start*/
				$start    = new DateTime($start_date);
				$end      = new DateTime($date[0]);
				$strt_date_form =new DateTime($strt_yrmnth);
				$close_date_form = new DateTime($close_yrmnth);
				if($start<$strt_date_form ){
					$year = substr($account_start_yr,0,4)-substr($start_date,0,4);
					if(substr($start_date,5,2)<$account_close_mn){
						$strt_acprd=$account_period-$year-1;
					}else{
						$strt_acprd=$account_period-$year;
					}
				} else if($start>=$strt_date_form ){
					$year = substr($start_date,0,4)-substr($account_start_yr,0,4);
					if(substr($start_date,5,2)<$account_close_mn){
						$strt_acprd=$account_period+$year-1;
					}else{
						$strt_acprd=$account_period+$year;
					}
				}
				if($end<$strt_date_form ){
					$year = substr($account_start_yr,0,4)-substr($date[0],0,4);
					if(substr($date[0],5,2)<$account_close_mn){
						$end_acprd=$account_period-$year-1;
					}else{
						$end_acprd=$account_period-$year;
					}
				} else if($end>=$strt_date_form ){
					$year = substr($date[0],0,4)-substr($account_start_yr,0,4);
					if(substr($date[0],5,2)<$account_close_mn){
						$end_acprd=$account_period+$year-1;
					}else{
						$end_acprd=$account_period+$year;
					}
				}
				$acArray=array();
				$diff = $end_acprd-$strt_acprd;
				if($diff ==0){
					$acArray[0]=$strt_acprd;
				}else{
					for($i=0;$i<$diff+1;$i++){
						$acArray[$i]=$strt_acprd+$i;
					}
				}
			/*trial end*/
			$acArray =array_reverse($acArray);
			$ac_cnt=count($acArray);
			if($acArray){
				$case =", CASE ";
				for($i=0;$i<$ac_cnt;$i++){
					$m = $acArray[$i]-$account_period;
					$startdate=$account_start_yr+$m."-".$account_start_mn;
					$enddate = $account_close_yr+$m."-".$account_close_mn;
					$case.="WHEN a.date>='".$startdate."' AND a.date<='".$enddate."' THEN '".$acArray[$i]."' ";
				}
				$case.="ELSE NULL ";
				$case.="END AS Period ";
			}
			$data_exe = Expdetails::cusdetail($start_date,$date[0],$case);
			$i = 0;
			$cus_det = array();
			if (isset($data_exe)) {
				/*foreach($data_exe as $object) {
					$fou = "13";
					print_r($object->$fou);print_r("<br/>");print_r("<br/>");exit;
					 $convarray[] = (array) $object;
				}*/
				foreach ($data_exe as $key => $value4) {
					$cus_det[$i]['id']=$value4->id;
					$cus_det[$i]['subid']=$value4->subid;
					if (Session::get('languageval') == "en") {
						$cus_det[$i]['Subject']=$value4->Subject;
						$cus_det[$i]['sub']=$value4->sub_eng;
						$utf_lngth =12;
					} else {
						$cus_det[$i]['Subject']=$value4->Subject_jp;
						$cus_det[$i]['sub']=$value4->sub_jap;
						$utf_lngth =9;
					}
					for($m=0;$m<$ac_cnt;$m++){
						$indexnumber = $acArray[$m];
						if (!isset($value4->$indexnumber)) {
							$value4->$indexnumber = 0;
						}
						$cus_det[$i][$acArray[$m]]=$value4->$indexnumber/$div;
					}
					$i++;
				}
			}
			$fileCnt = count($cus_det);
			for ($cnt=0;$cnt<$fileCnt;$cnt++) {
				for ($d=0;$d<$ac_cnt;$d++) {
					if(!isset($grndmontotal[$d])) {
						$grndmontotal[$d] = 0;
					}
					$grndmontotal[$d] =$grndmontotal[$d]+$cus_det[$cnt][$acArray[$d]];
				}
			}
			// print_r($grndmontotal);exit();
			return view('ExpensesDetails.index',['request' => $request,
											'disabledsen' => $disabledsen,
											'disabledman' => $disabledman,
											'disabledyen' => $disabledyen,
											'disabledmonthly' => $disabledmonthly,
											'disabledcustomer' => $disabledcustomer,
											'disabledcurrentyear' => $disabledcurrentyear,
											'account_period' => $account_period,
									 		'date_month' => $date_month,
											'account_val' => $account_val,
											'cus_det' => $cus_det,
											'utf_lngth' => $utf_lngth,
											'grndmontotal' => $grndmontotal,
											'date' => $date,
											'acArray' => $acArray,
											'pix' => $pix,
											'ac_cnt' => $ac_cnt,
											'fileCnt' => $fileCnt]);
		} else if ($request->active_select == 1) {
			if($request->filter){
				$fil=$request->filter;
			} else {
				$fil=3;
			}
			if ($fil==1) {
				$div =10000;
				$pix="70px";
			}else if($fil==2){
				$div =1000;
				$pix="90px";
			}else{
				$div =1;
				$pix="100px";
			}
			$e_accountperiod = Expdetails::fnGetAccountPeriod();
			$account_start_yr = $e_accountperiod[0]->Startingyear;
			$account_start_mn = $e_accountperiod[0]->Startingmonth;
			$account_close_yr = $e_accountperiod[0]->Closingyear;
			$account_close_mn = $e_accountperiod[0]->Closingmonth;
			$account_period = intval($e_accountperiod[0]->Accountperiod);
			$strt_yrmnth =$account_start_yr."-".$account_start_mn;
			$close_yrmnth =$account_close_yr."-".$account_close_mn;
			$data_query = Expdetails::yrmndetail("","","",1);
			$i=0;
			$date =array();
			foreach ($data_query as $key => $value3) {
				$date[$i] =$value3->date;
				$start_date =$value3->date;
				$i++;
			}
			/*trial start*/
			$start    = new DateTime($start_date);
			$end      = new DateTime($date[0]);
			$strt_date_form =new DateTime($strt_yrmnth);
			$close_date_form = new DateTime($close_yrmnth);
			if($start<$strt_date_form ){
				$year = substr($account_start_yr,0,4)-substr($start_date,0,4);
				if(substr($start_date,5,2)<$account_close_mn){
					$strt_acprd=$account_period-$year-1;
				}else{
					$strt_acprd=$account_period-$year;
				}
			} else if($start>=$strt_date_form ){
				$year = substr($start_date,0,4)-substr($account_start_yr,0,4);
				if(substr($start_date,5,2)<$account_close_mn){
					$strt_acprd=$account_period+$year-1;
				}else{
					$strt_acprd=$account_period+$year;
				}
			}
			if($end<$strt_date_form ){
				$year = substr($account_start_yr,0,4)-substr($date[0],0,4);
				if(substr($date[0],5,2)<$account_close_mn){
					$end_acprd=$account_period-$year-1;
				}else{
					$end_acprd=$account_period-$year;
				}
			} else if($end>=$strt_date_form ){
				$year = substr($date[0],0,4)-substr($account_start_yr,0,4);
				if(substr($date[0],5,2)<$account_close_mn){
					$end_acprd=$account_period+$year-1;
				}else{
					$end_acprd=$account_period+$year;
				}
			}
			$acArray=array();
			$mnArray = array();
			$mnArrays = array();
			$diff = $end_acprd-$strt_acprd;
			if($diff ==0){
				$acArray[0]=$strt_acprd;
			}else{
				for($i=0;$i<$diff+1;$i++){
					$acArray[$i]=$strt_acprd+$i;
				}
			}
			/*trial end*/
			$acArray =array_reverse($acArray);
			$ac_cnt=count($acArray);
			if($acArray){
				$case =", CASE ";
				for($i=0;$i<$ac_cnt;$i++){
					$m = $acArray[$i]-$account_period;
					$startdate=$account_start_yr+$m."-".$account_start_mn;
					$enddate = $account_close_yr+$m."-".$account_close_mn;
					$case.="WHEN a.date>='".$startdate."' AND a.date<='".$enddate."' THEN '".$acArray[$i]."' ";
				}
				$case.="ELSE NULL ";
				$case.="END AS Period ";
			}
				$mn =$account_start_mn;
				$mns =$account_start_mn;
				for($i=0;$i<12;$i++){
					if (!isset($mnArray[$i-1])) {
						$mnArray[$i-1] = "0";
					}
					if($mnArray[$i-1]==12){
						$mn =1;
					}
					if($mn<10){
						$mn ="0".$mn;
					}
					$mnArray[$i]=$mn;
					$mn++;
				}
				for($i=0;$i<12;$i++){
					if(isset($mnArrays[$i-1])) {
						if($mnArrays[$i-1]==12){
							$mns =1;
						}
					}
					$mnArrays[$i]=$mns;
					$mns++;
				}
				$removed = array_shift($mnArray);
				$mn_cnt =count($mnArray);
				$mn_cnts =count($mnArrays);
			$data_exe1 = Expdetails::mondetail($start_date,$date[0],$case);
			$i=0;
			$mon = array();
			if (isset($data_exe1)) {
				foreach ($data_exe1 as $key => $value5) {
					$mon[$i]['Period']=$value5->Period;
							for($m=0;$m<$mn_cnt;$m++){
								if (!isset($mnArray[$m])) {
									$mnArray[$m] = "";
								}
								$mon[$i][$mnArray[$m]]=$value5->$mnArray[$m]/$div;
							}
					$i++;
				}
			}
			$data_cnt =count($mon);
				for($x=0;$x<$data_cnt;$x++){
					$perArray[$x]=$mon[$x]['Period'];
				}
				$m=0;
				for($k=0;$k<$ac_cnt;$k++){
					if(in_array($acArray[$k],$perArray)){
						$mon_det[$k]['Period'] =$acArray[$k];
						for($s=0;$s<$mn_cnt;$s++){
							if (!isset($mnArray[$s])) {
								$mnArray[$s] = "";
							}
							$mon_det[$k][$mnArray[$s]]=$mon[$m][$mnArray[$s]];
						}
						$m++;
					} else {
						$mon_det[$k]['Period'] =$acArray[$k];
						for($s=0;$s<$mn_cnt;$s++){
							$mon_det[$k][$mnArray[$s]]=0;
						}
					 }
				}
				$fileCnt =count($mon_det);
				$grndmontotal5 =0;
				for ($cnt=0;$cnt<$fileCnt;$cnt++) {
					for ($d=0;$d<$mn_cnt;$d++) {
						if (!isset($grndmontotal[$d])) {
							$grndmontotal[$d] = 0;
						}
						$grndmontotal[$d] = $grndmontotal[$d]+$mon_det[$cnt][$mnArray[$d]];
					}
				}
			return view('ExpensesDetails.index',['request' => $request,
											'disabledsen' => $disabledsen,
											'disabledman' => $disabledman,
											'disabledyen' => $disabledyen,
											'disabledmonthly' => $disabledmonthly,
											'disabledcustomer' => $disabledcustomer,
											'disabledcurrentyear' => $disabledcurrentyear,
											'account_period' => $account_period,
									 		'date_month' => $date_month,
											'account_val' => $account_val,
											'mon_det' => $mon_det,
											'mn_cnts' => $mn_cnts,
											'mn_cnt' => $mn_cnt,
											'pix' => $pix,
											'mnArray' => $mnArray,
											'mnArrays' => $mnArrays,
											'grndmontotal' => $grndmontotal,
											'fileCnt' => $fileCnt]);
		}
	}
}