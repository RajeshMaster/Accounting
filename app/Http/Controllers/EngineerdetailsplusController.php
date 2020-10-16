<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Engineerdetailsplus;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\Salesdetails;
use App\Http\Common;
use Session;
use Carbon;
use Redirect;
use DB;
Class EngineerdetailsplusController extends Controller {
	public function index(Request $request) {
		$date_month="";
		$disabledsen="";
		$disabledman="";
		$disabledyen="";
		$disabledcustomer="";
		$disabledcurrentyear="";
		$account_val="";
		$year_monthslt="";
		$arrval="";
		$arrval_month1="";
		$mvalue="";
		$cnttl = 0;
		$array1="";
		$endperiod="";
		$array2 = array();
		$cnt_array = array();
		$jsdisparry = array();
		$jsdisp2arry = array();
		$jsarry2page = array();
		$employee = array();
		$get_td = array();
		$tblset = "";
		$jsarrypage = array();
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		$month_total_array=array();
		if(!isset($request->active_select) || $request->active_select=="") {
			$request->active_select=3;
			$disabledcurrentyear="disabled fb";
			$fil=1;
		} elseif($request->active_select==2) {
	        $fil=1;
	        $disabledcustomer="disabled fb";
		} elseif($request->active_select==3) {
	        $fil=1;
	        $disabledcurrentyear="disabled fb";
		}
		if(!isset($request->filter) || $request->filter=="") {
			$fil=1;
	        $disabledman="disabled fb";
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
		$accountperiod = Estimation::fnGetAccountPeriod($request);
		$account_close_yr = $accountperiod[0]->Closingyear;
		$account_period =""; 
		$account_close_mn = $accountperiod[0]->Closingmonth;
		$account_period = intval($accountperiod[0]->Accountperiod);
		if (!empty($request->account_val)) {
			$real_account_period = $account_period;
		}
		if (isset($request->previou_next_year)) {
			$splityear = explode('-', $request->previou_next_year);
		}
		if (!empty($request->previou_next_year)) {
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
		if($request->active_select == 3 || $request->active_select == 1) {
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
		}
		if( $request->active_select == 3 ) {
			//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH
			$account_val = Common::getAccountPeriod($year_monthslt, $account_close_yr, $account_close_mn, $account_period);
		}
		// echo $last_year; exit;
		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
		$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
		$est_query = Invoice::fnGetEstimateRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->quot_date;
		}
		$est_query1 = Engineerdetailsplus::fnGetEstimateRecordengPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->quot_date, 0, 4);
			$dbprevious[]=$value->quot_date;
			$pre++;
		}
		$est_query2 = Engineerdetailsplus::fnGetEstimateRecordengNext($to_date);
		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->quot_date;
		}
		$dbrecord = array_unique($dbrecord);
		// $dbpreviouscheck = array_unique($dbprevious);
		$db_year_month = array();
			foreach ($dbrecord AS $dbrecordkey => $dbrecordcheck) {
				$split_val = explode("-", $dbrecordcheck);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			//DISPLAY THE PERIOD FOR THE PARTICULAR YEAR MONTH LINK
			if($request->active_select == 2 || $request->active_select == 1) {
			if( $account_close_yr < $current_year ) {
				$dif_yr =  $current_year - $account_close_yr;
				$current_year = $current_year-$dif_yr;
			}
			if( !empty($dbnext) ) {
				$end_per_yrmn = end($dbnext);
				$end_yrmn = explode("-", $end_per_yrmn);
				if( $account_close_yr < $end_yrmn[0] ) {
					$dif =  $end_yrmn[0] - $account_close_yr;
					if( $account_close_mn < $end_yrmn[1] ) {
						$dif = $dif+1;
					}
					$current_year = $current_year + $dif;
					$last_year = $current_year-1;
					$account_period = $account_period + $dif;
				}
			} else {
				if( !empty($dbrecord) ) {
					$end_per_yrmn = end($dbrecord);
				} else {
					$end_per_yrmn = end($dbprevious);
				}
				$end_yrmn = explode("-", $end_per_yrmn);
				if( $account_close_yr < $end_yrmn[0] || $account_close_yr == $end_yrmn[0]) {
					$dif =  $end_yrmn[0] - $account_close_yr;
					if( $account_close_mn < $end_yrmn[1] ) {
						$dif = $dif+1;
					}
					$current_year = $account_close_yr + $dif;
					$last_year = $current_year-1;
					$account_period = $account_period + $dif;
				}
			}
		}
		$dispmnthyr = array();
		$fil2ndsql = array();
		if($request->active_select == 2 || $request->active_select == 1) {
			$cnt_array = array();
			for ($rg=$account_period;$rg>=1;$rg--) {
				$year_month = array();
				if ($account_close_mn == 12) {
					$year_month[$rg][0] = $current_year . "-01";
					$year_month[$rg][1] = $current_year . "-12";
					$dispmnthyr[$rg][0] = $current_year . "-01";
					$dispmnthyr[$rg][1] = $current_year . "-12";
				} else {
					for ($i = 12; $i > $account_close_mn; $i--) {
						$year_month[$rg][0] = $last_year."-".$i;
						$dispmnthyr[$rg][0] = $last_year."-".$i;
					}
					for ($i = 1; $i <= $account_close_mn; $i++) {
						$year_month[$rg][1] = $current_year."-".$i;
						$dispmnthyr[$rg][1] = $current_year."-".$i;
					}
				}
				$last_year--;
				$current_year--;
				$fryr = array();
				$frmnth = array();
				$fil2ndsql=Salesdetails::getfilcnt($year_month[$rg][0],$year_month[$rg][1], $rg);
				$cnt_array[$rg] = $fil2ndsql;
			}
		}
		if ($request->active_select == 3) {
			$getmonthtot=Salesdetails::fnGetEstimateMonthTotals($date_month,$from_date,$to_date);
			$granttot = 0;
			foreach ($getmonthtot as $key => $value) {
				$granttot += $value->totalval;
				$cnttl++;
			}
		}
		$cur_year=date('Y');
		$cur_month=date('m');
		if (isset($request->selMonth)) {
			$selectedMonth=$request->selMonth;
			$selectedYear=$request->selYear;					
			$cur_month=$selectedMonth;
			$cur_year=$selectedYear;
		} else {
			$selectedMonth=$cur_month;
			$selectedYear=$cur_year;
		}	
		$rangeFrom = $current_year."-04";
		$rangeTo = $last_year."-11";
		$rangeFromEn = $current_year."-10";
		$rangeToEn = $last_year."-11";
		$getdetails = Engineerdetailsplus::enggplus($request,1,$from_date,$to_date,$date_month);
		if($request->active_select == '2') {
			$getdetails = Engineerdetailsplus::getenggplus($request,1,$date_month);	
		}
		$getdetails2 = Engineerdetailsplus::enggplus($request,2,$from_date,$to_date,$date_month);
		if($request->active_select == '2') {
			$getdetails2 = Engineerdetailsplus::getenggplus($request,2,$date_month);	
		}
		$i=0;
		$temp = "";
		$totalval = 0;
		$temp1 = "";
			foreach ($getdetails as $key => $value) {
				$get_td[$i][0]= $value->id;
				$get_td[$i][1]= $value->user_id;
				$get_td[$i][2]= $value->Firstname;
				$get_td[$i]['empID']= $value->empID;
				$get_td[$i][3] = $value->totalval;
				$i++;
			}
		$fileCnt=count($get_td);
		if($fileCnt >0) {
			if($request->active_select == '3') {
				$arryr = array();
				$arrmn = array();	
				foreach ($db_year_month as $key => $value) {
					array_push($arryr, $key);
					array_push($arrmn, $value);
				}
				asort($arryr);
				asort($arrmn);
				$arryrsrtless = (isset($arryr[0])?$arryr[0]:"");
				$arryrsrtgreater = (isset($arryr[1])?$arryr[1]:"");
				if($arryrsrtgreater == '') {
					$arryrsrtgreater = $arryrsrtless;
				} else {
					$arryrsrtgreater = $arryrsrtgreater;
				}
				if(count($arryr) == 2) { 
					$minmnth = min($arrmn[0]);
					$maxmnth = max($arrmn[1]);
				} else {
					$minmnth = (isset($arrmn[0])?min($arrmn[0]):"");
					$maxmnth = (isset($arrmn[0])?max($arrmn[0]):"");
				}
				$arrval = array();
				if(count($arryr) == 2) {
					for($i=$arryrsrtgreater; $i>=$arryrsrtless; $i--) {
						if($i == $arryrsrtgreater) {
							for($j = $account_close_mn; $j >= 1; $j--) {
								$arrval[$i][$j] = $j;
							}
						} else {
							for($k = 12; $k >= ($account_close_mn+1); $k--) {
								$arrval[$i][$k] = $k;
							}
						}
					}
				} else {
					for($i=$arryrsrtgreater; $i>=$arryrsrtless; $i--) {
						for($j = $maxmnth; $j >= $minmnth; $j--) {
							$arrval[$i][$j] = $j; 
						}
					}
				}
			}
			if($request->active_select == '3') {
				$jsarry = array();
				$jsdisparry = array();
				$arrval_month1 = $arrval;
				$array1 = array();
				for ($cnt=0; $cnt<$fileCnt;$cnt++) {
					$filter_month1 = array();
					$employee = Engineerdetailsplus::selectdetails($get_td[$cnt]['empID'],$from_date,$to_date);
					$filpro = 0;
					$result=0;
					$h = 2;
					foreach ($arrval_month1 AS $year => $mvalue) {
						foreach ($mvalue AS $month => $mmonth) {
							if($month <= 9) {
								$conmnth = '0' . $mmonth;
							} else {
								$conmnth = $mmonth;
							}
							$yearmonth = $year .'-'. $conmnth;
							$slemp = Engineerdetailsplus::fnGetEmplyy($yearmonth,$get_td[$cnt]['empID']);
							$gettotalval1 = 0;
							foreach ($slemp as $key => $value) {
								$gettotalval1 += Engineerdetailsplus::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
							}
						if($fil == 1) {
								$filpro = $gettotalval1 / 10000;
								$filder_month11 = number_format($filpro); 
							} else if($fil == 2) {
								$filpro = $gettotalval1 / 1000;
								$filder_month11 = number_format($filpro); 
							} else if($fil == 3) {
								$filpro = $gettotalval1;
								$filder_month11 = number_format($filpro); 
							}
						array_push($filter_month1, $filder_month11);
						$h--;
						if (!isset($jsarry[$cnt][$year][$month])) {
							$jsarry[$cnt][$year][$month] = 0;
						}
						$jsarry[$cnt][$year][$month] += $filpro;
						$result+=$filpro;
						$jsdisparry[$cnt] = $result;
						$filpro = 0;
						}
					}
					array_push($array1, $filter_month1);
				}
				for ($cnt=0; $cnt<count($getdetails2);$cnt++) {
					$h = 2;
					foreach ($arrval_month1 AS $year => $mvalue) {
						foreach ($mvalue AS $month => $mmonth) {
							if($month <= 9) {
								$conmnth = '0' . $mmonth;
							} else {
								$conmnth = $mmonth;
							}
							$yearmonth = $year .'-'. $conmnth;
							$slemptemp =  Engineerdetailsplus::fnGetEmplyy($yearmonth,$get_td[$cnt]['empID']);
							$gettotalval123 = 0;
							foreach ($slemptemp as $key => $value) {
								$gettotalval123 += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
							}
						if($fil == 1) {
								$filpage = $gettotalval123 / 10000;
								$filder_monthpage = number_format($filpage); 
							} else if($fil == 2) {
								$filpage = $gettotalval123 / 1000;
								$filder_monthpage = number_format($filpage); 
							} else if($fil == 3) {
								$filpage = $gettotalval123;
								$filder_monthpage = number_format($filpage); 
							}
						$h--;
						if (!isset($jsarrypage[$cnt][$year][$month])) {
							$jsarrypage[$cnt][$year][$month] = 0;
						}
						$jsarrypage[$cnt][$year][$month] += $filpage;
						}
					}
				}
			}
		}
		if($request->active_select == '2') {
			$endperiod = 1;
				foreach ($cnt_array AS $period => $cntvalue) {
					foreach ($cntvalue AS $key => $value) {
						if ($value->qdate > 0) {
							$endperiod = $period;
						}
					}
				}
			for ($cnt=0; $cnt<$fileCnt;$cnt++) {
				$result=0;
				$filter_month2 = array();
				$filter_month_2 = array();
				$employee = Engineerdetailsplus::selectdetails1($get_td[$cnt]['empID'],$from_date,$to_date);
				$filpro = 0;
				$getcurrentrecord = $dispmnthyr[$account_period];
				$startcurdate = explode('-', $getcurrentrecord[0]);
				$endcurdate = explode('-', $getcurrentrecord[1]);
				$h = 2;
				for ($l = ($account_period + 2); $l > $account_period; $l--) {
					$startdate = intval($startcurdate[0] + $h) . "-" . substr("0" . $startcurdate[1], -2);
					$enddate = intval($endcurdate[0] + $h) . "-" . substr("0" . $endcurdate[1], -2);
					$indkival = Engineerdetailsplus::fnGetindikis($startdate,$enddate,$get_td[$cnt]['empID']);
					$getgetindkival = 0;
					foreach ($indkival as $key => $value) {
					$getgetindkival += Engineerdetailsplus::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
					}
						if($fil == 1) {
							$filpro = $getgetindkival / 10000;
							$filder_month21 = number_format($filpro); 
						} else if($fil == 2) {
							$filpro = $getgetindkival / 1000;
							$filder_month21 = number_format($filpro); 
						} else if($fil == 3) {
							$filpro = $getgetindkival;
							$filder_month21 = number_format($filpro); 
						} 
						array_push($filter_month2, $filder_month21);
					//echo "</td>";
					$h--;
					if (!isset($jsarry2[$l])) {
						$jsarry2[$l] = 0;
					}
					$jsarry2[$l] += $filpro;
					if (isset($jsdisp2arry[$cnt])) {
						$jsdisp2arry[$cnt] += $filpro;
					}
					$filpro = 0;
				}
				$filpro = 0;
				foreach ($dispmnthyr AS $period => $cntvalue) {
					if ($period >= $endperiod) {
						$cnt_val_str = explode("-", $cntvalue[0]);
						$cnt_val_end = explode("-", $cntvalue[1]);
						$startdate = $cnt_val_str[0]. "-" . substr("0" . $cnt_val_str[1], -2);
						$enddate = $cnt_val_end[0]. "-" . substr("0" . $cnt_val_end[1], -2);
						$indkival = Engineerdetailsplus::fnGetindikis($startdate,$enddate,$get_td[$cnt]['empID']);
						$getgetindkival = 0;
						foreach ($indkival as $key => $value) {
							$getgetindkival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
						}
							if($fil == 1) {
								$filpro = $getgetindkival / 10000;
								$filder_month22 = number_format($filpro); 
							} else if($fil == 2) {
								$filpro = $getgetindkival / 1000;
								$filder_month22 = number_format($filpro); 
							} else if($fil == 3) {
								$filpro = $getgetindkival;
								$filder_month22 = number_format($filpro); 
							}
							array_push($filter_month2, $filder_month22);
							$result+=$filpro;
							if (!isset($jsarry2[$period])) {
								$jsarry2[$period] = 0;
							}
							$jsarry2[$period] += $filpro;
							$jsdisp2arry[$cnt] = $result;
						$filpro = 0;
					}
				}
					array_push($array2, $filter_month2);
					
			}
			for ($cnt=0; $cnt<count($getdetails2);$cnt++) {
				$getcurrentrecordpage = $dispmnthyr[$account_period];
				$startcurdatepage = explode('-', $getcurrentrecord[0]);
				$endcurdatepage = explode('-', $getcurrentrecord[1]);
				$h = 2;
				for ($l = ($account_period + 2); $l > $account_period; $l--) {
					$startdatepage = intval($startcurdatepage[0] + $h) . "-" . substr("0" . $startcurdatepage[1], -2);
					$enddatepage = intval($endcurdatepage[0] + $h) . "-" . substr("0" . $endcurdatepage[1], -2);
					$indkivalpage = Engineerdetailsplus::fnGetindikis($startdatepage,$enddatepage,$getdetails2[$cnt]->empID);
					$getgetindkivalpage = 0;
					foreach ($indkivalpage as $key => $value) {
					$getgetindkivalpage += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
					}
						if($fil == 1) {
							$filpropage = $getgetindkivalpage / 10000;
							$filder_monthpage = number_format($filpropage); 
						} else if($fil == 2) {
							$filpropage = $getgetindkivalpage / 1000;
							$filder_monthpage = number_format($filpropage); 
						} else if($fil == 3) {
							$filpropage = $getgetindkivalpage;
							$filder_monthpage = number_format($filpropage); 
						} 
					//echo "</td>";
					$h--;
					if (!isset($jsarry2page[$l])) {
						$jsarry2page[$l] = 0;
					}
					$jsarry2page[$l] += $filpropage;
					$filpropage = 0;
				}
				$filpropage = 0;
				foreach ($dispmnthyr AS $period => $cntvalue) {
					if ($period >= $endperiod) {
						$cnt_val_str = explode("-", $cntvalue[0]);
						$cnt_val_end = explode("-", $cntvalue[1]);
						$startdate = $cnt_val_str[0]. "-" . substr("0" . $cnt_val_str[1], -2);
						$enddate = $cnt_val_end[0]. "-" . substr("0" . $cnt_val_end[1], -2);
						$indkival = Engineerdetailsplus::fnGetindikis($startdate,$enddate,$getdetails2[$cnt]->empID);
						$getgetindkival = 0;
						foreach ($indkival as $key => $value) {
							$getgetindkival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
						}
							if($fil == 1) {
								$filpro = $getgetindkival / 10000;
								$filder_month22 = number_format($filpro); 
							} else if($fil == 2) {
								$filpro = $getgetindkival / 1000;
								$filder_month22 = number_format($filpro); 
							} else if($fil == 3) {
								$filpro = $getgetindkival;
								$filder_month22 = number_format($filpro); 
							}
							if (!isset($jsarry2page[$period])) {
								$jsarry2page[$period] = 0;
							}
							$jsarry2page[$period] += $filpro;
						$filpro = 0;
					}
				}
			}
		}
		if($request->active_select == '3') {
			foreach ($jsarrypage AS $mkey => $mvalue)  {
				foreach ($mvalue AS $myear => $montharr) {
					foreach ($montharr AS $mmonth => $mmonthvalue) {
						if (!isset($month_total_array[$myear][$mmonth])) {
							$month_total_array[$myear][$mmonth] = 0;
						}
						$month_total_array[$myear][$mmonth] += $mmonthvalue;
					}
				}
			}
		}
		// start append 2019/02/21 Rajaguru td width calculation
		if($request->active_select == '3') {
			 if (is_array($arrval) || is_object($arrval)){
				$tblset=array_sum(array_map("count", $arrval));
				}
		} else {
			if (is_array($jsarry2page) || is_object($jsarry2page)){
				$tblset=array_sum(array_map("count", $jsarry2page));
			}
		}
		// end
		return view('Engineerdetailsplus.index',['request' => $request,
												 'account_period' => $account_period,
												 'account_val' => $account_val,
												 'arrval' => $arrval,
												 'arrval_month1' => $arrval_month1,
												 'date_month' => $date_month,
												 'dbnext' => $dbnext,
												 'disabledsen' => $disabledsen,
												 'disabledman' => $disabledman,
												 'disabledyen' => $disabledyen,
												 'disabledcustomer' => $disabledcustomer,
												 'year_monthslt' => $year_monthslt,
												 'last_year' => $last_year,
												 'current_year' => $current_year,
												 'dbprevious' => $dbprevious,
												 'db_year_month' => $db_year_month,
												 'disabledcurrentyear' => $disabledcurrentyear,
												 'mvalue' => $mvalue,
												 'cnttl' => $cnttl,
												 'fileCnt' => $fileCnt,
												 'array1' => $array1,
												 'array2' => $array2,
												 'employee' => $employee,
												 'jsdisparry' => $jsdisparry,
												 'cnt_array' => $cnt_array,
												 'month_total_array' => $month_total_array,
												 'jsarry2page' => $jsarry2page,
												 'jsdisp2arry' => $jsdisp2arry,
												'endperiod' => $endperiod,
												'tblset'=> $tblset,
												 'getdetails'=> $getdetails]);
	}
}