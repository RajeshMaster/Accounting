<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\Salesdetails;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use App\Http\Helpers;
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use Carbon;

class SalesdetailsController extends Controller {
	public static function index(Request $request) {
		$disabledmonthly="";
		$disabledcustomer="";
		$disabledcurrentyear="";
		$disabledsen="";
		$disabledman="";
		$disabledyen="";
		$splityear="";
		$date_month="";
		$account_val="";
		$arrval="";
		$mvalue="";
		$array1="";
		$period="";
		$jsarry="";
		$year_monthslt="";
		$cnt_array = array();
		$cntvalue="";
		$arrval_month1="";
		$array2 = array();
		$jsdisp2arry = array();
		$cus_list = array(); 
		$jsarry2=array();
		$filproki_2 = array();
		$filproki_3 = array();
		$array3 = array();
		$year_mon_array = array();
		$jsdisp1arry = array();
		$jsarry3=array();
		$getgetindkival="";
		$jsdisparry = array();
		$get_td = array();
		$jsarrypage = array();
		$cnttl = 0;
		$addedvalue=0;
		$avgmnth = 0;
		$actionName = "";
		$jsarry2page = array();
		$tblset = "";
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		$month_total_array=array();
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
		$est_query1 = Invoice::fnGetEstimateRecordPrevious($from_date);
		// print_r($est_query1); exit;
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->quot_date, 0, 4);
			$dbprevious[]=$value->quot_date;
			$pre++;
		}
		// print_r($dbprevious); exit;
		$est_query2 = Invoice::fnGetEstimateRecordNext($to_date);
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
					/*for ($i = ($account_period + 1); $i <= 12; $i++) {
						$year_month[$rg][0] = $last_year."-".$i;
						$dispmnthyr[$rg][0] = $last_year."-".$i;print_r($year_month[$rg][0]);print_r("</br>");
					}*/
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
		$sql = Salesdetails::fnGetEstimateDetails1s($date_month, $from_date, $to_date, $request);
		if($request->active_select == 2 || $request->active_select == 1) {
			$sql = Salesdetails::fnGetEstimateDetailsFlgs($date_month, $from_date, $to_date, $request);	
		}
		$sql_sales = Salesdetails::fnGetEstimateDetailsales($date_month, $from_date, $to_date, $request);
		if($request->active_select == 2 || $request->active_select == 1) {
			$sql_sales = Salesdetails::fnGetEstimateDetailsFlgsales($date_month, $from_date, $to_date, $request);	
		}
		$i=0;
		$temp = "";
		$totalval = 0;
		$temp1 = "";
			foreach ($sql as $key => $value) {
				$get_td[$i][0]= $value->id;
				$get_td[$i][1]= $value->user_id;
				$get_td[$i][2]= $value->company_name;
				$get_td[$i]['trading_destination_selection']= $value->trading_destination_selection;
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
				$cus_list = array();
				for ($cnt=0; $cnt<$fileCnt;$cnt++) {
					$filter_month1 = array();
					$customer = Salesdetails::selectdetails($get_td[$cnt]['trading_destination_selection']);
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
							$slemp = Salesdetails::fnGetEmply($yearmonth,$get_td[$cnt]['trading_destination_selection']);
							$gettotalval1 = 0;
							foreach ($slemp as $key => $value) {
								$gettotalval1 += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
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
					array_push($cus_list, $customer);
				}
				for ($cnt=0; $cnt<count($sql_sales);$cnt++) {
					$h = 2;
					foreach ($arrval_month1 AS $year => $mvalue) {
						foreach ($mvalue AS $month => $mmonth) {
							if($month <= 9) {
								$conmnth = '0' . $mmonth;
							} else {
								$conmnth = $mmonth;
							}
							$yearmonth = $year .'-'. $conmnth;
							$slemptemp = Salesdetails::fnGetEmply($yearmonth,$sql_sales[$cnt]->trading_destination_selection);
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
				$customer = Salesdetails::selectdetails($get_td[$cnt]['trading_destination_selection']);
				$filpro = 0;
				$getcurrentrecord = $dispmnthyr[$account_period];
				$startcurdate = explode('-', $getcurrentrecord[0]);
				$endcurdate = explode('-', $getcurrentrecord[1]);
				$h = 2;
				for ($l = ($account_period + 2); $l > $account_period; $l--) {
					$startdate = intval($startcurdate[0] + $h) . "-" . substr("0" . $startcurdate[1], -2);
					$enddate = intval($endcurdate[0] + $h) . "-" . substr("0" . $endcurdate[1], -2);
					$indkival = Salesdetails::fnGetindikis($startdate,$enddate,$get_td[$cnt]['trading_destination_selection']);
					$getgetindkival = 0;
					foreach ($indkival as $key => $value) {
					$getgetindkival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
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
						$indkival = Salesdetails::fnGetindikis($startdate,$enddate,$get_td[$cnt]['trading_destination_selection']);
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
					array_push($cus_list, $customer);
			}
			for ($cnt=0; $cnt<count($sql_sales);$cnt++) {
				$getcurrentrecordpage = $dispmnthyr[$account_period];
				$startcurdatepage = explode('-', $getcurrentrecord[0]);
				$endcurdatepage = explode('-', $getcurrentrecord[1]);
				$h = 2;
				for ($l = ($account_period + 2); $l > $account_period; $l--) {
					$startdatepage = intval($startcurdatepage[0] + $h) . "-" . substr("0" . $startcurdatepage[1], -2);
					$enddatepage = intval($endcurdatepage[0] + $h) . "-" . substr("0" . $endcurdatepage[1], -2);
					$indkivalpage = Salesdetails::fnGetindikis($startdatepage,$enddatepage,$sql_sales[$cnt]->trading_destination_selection);
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
						$indkival = Salesdetails::fnGetindikis($startdate,$enddate,$sql_sales[$cnt]->trading_destination_selection);
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
		if ($request->active_select == 1) {
			$endperiod = 1;
				foreach ($cnt_array AS $period => $cntvalue) {
					foreach ($cntvalue AS $key => $value) {
						if ($value->qdate > 0) {
							$endperiod = $period;
						}
					}
				}
			foreach ($cnt_array AS $period => $cntvalue) {
				$result=0;
				$resulta=0;
				$resultb=0;
				$resultc=0;
				$year_mon_array_1 = array();
				$filproki_1 = array();
				if ($period >= $endperiod) {
					if ($account_close_mn == 12) {
						$getyear = explode('-', $dispmnthyr[$period][0]);
						for ($i = 1; $i <= $account_close_mn; $i++) {
							$passyrmn = $getyear[0]."-".substr("0".$i, -2);
							$fil1rec = Salesdetails::getfil1recs($passyrmn);
							$fil1reckival = 0;
							foreach ($fil1rec as $key => $value) {
								$fil1reckival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
							}
							if($fil == 1) {
								$filproki = $fil1reckival / 10000;
								$filproki_a = number_format($filproki); 
							} else if($fil == 2) {
								$filproki = $fil1reckival / 1000;
								$filproki_a =  number_format($filproki); 
							} else if($fil == 3) {
								$filproki = $fil1reckival;
								$filproki_a =  number_format($filproki); 
							} 

							array_push($filproki_1, $filproki_a);
							array_push($year_mon_array_1, $passyrmn);
							if (!isset($jsdisp1arry[$i])) {
								$jsdisp1arry[$i] = 0;
							}
							$jsdisp1arry[$i] += str_replace(',', '', $filproki_a);
							$result+=$filproki;
							$jsarry3[$period] = $result;
							$filproki = 0;
						}
					} else {
						$getyear1 = explode('-', $dispmnthyr[$period][0]);
						$getyear2 = explode('-', $dispmnthyr[$period][1]);
						for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
							//echo "<td style='border-top:1px dotted #136E83;' align='right'>";
							$passyrmn = $getyear1[0]."-".substr("0".$i, -2);
							$fil1rec = Salesdetails::getfil1recs($passyrmn);
							$fil1reckival = 0;
							foreach ($fil1rec as $key => $value) {
								$fil1reckival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
							}
								if($fil == 1) {
									$filproki = $fil1reckival / 10000;
									$filproki_b = number_format($filproki); 
								} else if($fil == 2) {
									$filproki = $fil1reckival / 1000;
									$filproki_b = number_format($filproki); 
								} else if($fil == 3) {
									$filproki = $fil1reckival;
									$filproki_b = number_format($filproki); 
								}
								array_push($filproki_1, $filproki_b);
								array_push($year_mon_array_1, $passyrmn);
								// echo $resultb+=$filproki_b; echo "<br>";
								if (!isset($jsdisp1arry[$i])) {
									$jsdisp1arry[$i] = 0;
								}
								$jsdisp1arry[$i] +=str_replace(',', '', $filproki_b);
							$result+=$filproki;
							$jsarry3[$period] = $result;
							$filproki = 0;
						}
						for ($i = 1; $i <= $account_close_mn; $i++) {
							//echo "<td style='border-top:1px dotted #136E83;' align='right'>";
							$passyrmn = $getyear2[0]."-".substr("0".$i, -2);
							$fil1rec = Salesdetails::getfil1recs($passyrmn);
							$fil1reckival = 0;
							foreach ($fil1rec as $key => $value) {
								$fil1reckival += Salesdetails::fnGetTaxCalculation($value->quot_date,$value->totalval,$value->tax);
							}
								if($fil == 1) {
									$filproki = $fil1reckival / 10000;
									$filproki_c = number_format($filproki); 
								} else if($fil == 2) {
									$filproki = $fil1reckival / 1000;
									$filproki_c = number_format($filproki); 
								} else if($fil == 3) {
									$filproki = $fil1reckival;
									$filproki_c = number_format($filproki); 
								}
								array_push($filproki_1, $filproki_c);
								if (!isset($jsdisp1arry[$i])) {
									$jsdisp1arry[$i] = 0;
								}
								$jsdisp1arry[$i] +=str_replace(',', '', $filproki_c);
							$result+=$filproki;
							$jsarry3[$period] = $result;
							array_push($year_mon_array_1, $passyrmn);
							$filproki = 0;
						}
					}
				}
					array_push($year_mon_array, $year_mon_array_1);
					array_push($array3, $filproki_1);
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
		if($request->active_select == '3') {
			if (is_array($arrval) || is_object($arrval)){
				foreach ($arrval AS $year => $mvalue) {
					foreach ($mvalue AS $month => $mmonth) {
					$avgmnth++;
					}
				}
			}
		}
		// start append 2019/02/25 Rajaguru td width calculation
		if($request->active_select == '3') {
			 if (is_array($arrval) || is_object($arrval)){
				$tblset=array_sum(array_map("count", $arrval));
				}
		} 
		else {
			if (is_array($jsarry2page) || is_object($jsarry2page)){
				$tblset=array_sum(array_map("count", $jsarry2page));
			}
		} 
		// end
		$yr="/";
		$getcnt1 = 0;
		$Period = "Period";
		$monthnumb = "Month";
		$endperiod = 1;
		$ym[] = array();
		$totalvertical[] = array();
		$get_td[] = array();

		$actionName = $request->actionName;
		if ($actionName == "salesexceldownload") {
			$curTime = date('Y/m/d  H:i:s');
			if($request->active_select == '1') {
				$template_name = 'resources/assets/uploadandtemplates/templates/Sales_details_Monthly.xls';
			} else {
				$template_name = 'resources/assets/uploadandtemplates/templates/Sales_details.xls';
			}
			
			$tempname = "Sales_details";
	  		$excel_name=$tempname;
	  		Excel::load($template_name, function($objTpl) use($request, $curTime, $cus_list, $arrval, $array1, $array2, $array3, $cnt_array, $year_monthslt, $account_period,$jsarry3) {
			$y = 0;
			$rowstartpos = 6;
			$z = $rowstartpos;
			if($request->active_select == '1') {
				$array = $array3;
				$endperiod = 1;
				foreach ($cnt_array AS $period => $cntvalue) {
					foreach ($cntvalue AS $key => $value) {
						if ($value->qdate > 0) {
							$endperiod = $period;
						}
					}
				}
				foreach ($cnt_array AS $period => $cntvalue) {
					if ($period >= $endperiod) {
						$period = intval($period);
						$objTpl->getActiveSheet()->setCellValue('A'.$z, $period."Period");
					}
					$z++;
				}
				$b = 0;
				foreach ($year_monthslt AS $year => $mvalue) {
					foreach ($mvalue AS $month => $mmonth) {
						$ym[$b] = $month." Month";
						$b++;
					}
				}
			}
			if($request->active_select == '2') {
				$array = $array2;
				$endperiod = 1;
				$b = 0;
				foreach ($cnt_array AS $period => $cntvalue) {
					foreach ($cntvalue AS $key => $value) {
						if ($value->qdate > 0) {
							$endperiod = $period;
						}
					}
				}
				foreach ($cnt_array AS $period => $cntvalue) {
					if ($period >= $endperiod) {  
						$lastrec = (intval($account_period) + 2)." Period";
						$lastsecrec = (intval($account_period) + 1)." Period";
						$totarrrec = array($lastrec,$lastsecrec);
						$ym[$b] = $period." Period";
						$b++;
					}
				}
				$ym = array_merge($totarrrec,$ym);
			}
			if($request->active_select == '3') {
				$array = $array1;
				$b = 0;
				foreach ($arrval AS $year => $mvalue) {
					foreach ($mvalue AS $month => $mmonth) {
						$ym[$b] = $year."/".$month;
						$b++;
					}
				}
			}

			if($request->active_select == ('2'||'3')) {
				$x = 1;
				foreach ($cus_list AS $key => $value) {
					$get_td['srl'][$x] = $x;
					$get_td['customer_id'][$x] = isset($value[0]->customer_id)?$value[0]->customer_id:'';
					$get_td['customer_name'][$x] = isset($value[0]->customer_name)?$value[0]->customer_name:'';
					$x++;
				}
			}
			

			$y = 0;
			$rowstartpos = 6;
			$avertot = '';
			$z = $rowstartpos+$y;
			
			foreach ($array AS $key => $value) {
				$arr[$y] = $value;
				$arr[$y] =  preg_replace("/,/", "", $arr[$y]);
				$tot = array_sum($arr[$y]);
				$avertot += array_sum($arr[$y]);
				if($request->active_select == '1') {
					$rowArr = $value;
						$objTpl->getActiveSheet()->fromArray(
				        $rowArr,
				        NULL,
				        'C'.$z
				    );

					//$objTpl->getActiveSheet()->setCellValue('B'.$z, number_format($tot));
				} else {
					$rowArr = $value;
						$objTpl->getActiveSheet()->fromArray(
				        $rowArr,
				        NULL,
				        'E'.$z
				    );

					$objTpl->getActiveSheet()->setCellValue('D'.$z, number_format($tot));
				}
				$z++;
			}
			if($request->active_select == '3') {
				$objTpl->getActiveSheet()->setCellValue('D1', "合計金額 (".count($ym)."): ".number_format($avertot));
				$objTpl->getActiveSheet()->setCellValue('D2', "平均月分: ".number_format(round($avertot / count($ym))));
			}
			if($request->filter == '1') {
				$Unitval = "10,000";
			} else if($request->filter == '2') {
				$Unitval = "1,000";
			} else if($request->filter == '3') {
				$Unitval = "1円";
			} else {
				$Unitval = "10,000";
			}
			$objTpl->getActiveSheet()->setCellValue('D3', "単位: ".$Unitval);
			if($request->active_select == '1') {
				$z = 6;
				foreach ($jsarry3 AS $key => $value) {
					$objTpl->getActiveSheet()->setCellValue('B'.$z, number_format($value));
					$z++;
				}
			}
			// Replace comma in array for calculation
			for ($ch=0; $ch < count($array); $ch++) { 
				$replaceComma = array();
				for ($in=0; $in < count($array[$ch]); $in++) { 
					$replaceComma[$in] = preg_replace("/,/", "", $array[$ch][$in]);
				}
				$array[$ch] = $replaceComma;
			}

			for ($i=0; $i <count($array[0]) ; $i++) {
    			$totalvertical[$i] = array_sum(array_column($array, $i));
    		}
    		// Set comma for array values
    		$totalvertical = array_map(function($num){return number_format($num);}, $totalvertical);

			if($request->active_select == '1') {
				$rowArray = $ym;
					$objTpl->getActiveSheet()->fromArray(
			        $rowArray,
			        NULL,
			        'C4'
			    );

				$rowtot = $totalvertical;
					$objTpl->getActiveSheet()->fromArray(
			        $rowtot,
			        NULL,
			        'C5'
			    );
			} else {
				$rowArray = $ym;
					$objTpl->getActiveSheet()->fromArray(
			        $rowArray,
			        NULL,
			        'E4'
			    );

				$rowtot = $totalvertical;
					$objTpl->getActiveSheet()->fromArray(
			        $rowtot,
			        NULL,
			        'E5'
			    );
			}

		    if(($request->active_select != '1') && ($request->active_select == ('2'||'3'))) {
		    	$rowSrlnumber = $get_td['srl'];
				$columnArray = array_chunk($rowSrlnumber, 1);
				$objTpl->getActiveSheet()
				    ->fromArray(
				        $columnArray,
				        NULL,
				        'A6'
				    );

				$rowCustomer_id = $get_td['customer_id'];
				$columnArray = array_chunk($rowCustomer_id, 1);
				$objTpl->getActiveSheet()
				    ->fromArray(
				        $columnArray,
				        NULL,
				        'B6'
				    );

		    	$rowCustomer_name = $get_td['customer_name'];
				$columnArray = array_chunk($rowCustomer_name, 1);
				$objTpl->getActiveSheet()
				    ->fromArray(
				        $columnArray,
				        NULL,
				        'C6'
				    );
		    }
			

			/*スタイル適用*/
			//if(($request->active_select != '1') && ($request->active_select == ('2'||'3'))) {
			if($request->active_select == '1') {
				if (count($ym) ==1) {
					$monthpos = 'C4:C4';
					$horizontaltotalpos = 'C5:C5';
					$endcolumn = 'C';
				} else if (count($ym) ==2) {
					$monthpos = 'C4:D4';
					$horizontaltotalpos = 'C5:D5';
					$endcolumn = 'D';
				} else if (count($ym) ==3) {
					$monthpos = 'C4:E4';
					$horizontaltotalpos = 'C5:E5';
					$endcolumn = 'E';
				} else if (count($ym) ==4) {
					$monthpos = 'C4:F4';
					$horizontaltotalpos = 'C5:F5';
					$endcolumn = 'F';
				} else if (count($ym) ==5) {
					$monthpos = 'C4:G4';
					$horizontaltotalpos = 'C5:G5';
					$endcolumn = 'G';
				} else if (count($ym) ==6) {
					$monthpos = 'C4:H4';
					$horizontaltotalpos = 'C5:H5';
					$endcolumn = 'H';
				} else if (count($ym) ==7) {
					$monthpos = 'C4:I4';
					$horizontaltotalpos = 'C5:I5';
					$endcolumn = 'I';
				} else if (count($ym) ==8) {
					$monthpos = 'C4:J4';
					$horizontaltotalpos = 'C5:J5';
					$endcolumn = 'J';
				} else if (count($ym) ==9) {
					$monthpos = 'C4:K4';
					$horizontaltotalpos = 'C5:K5';
					$endcolumn = 'K';
				} else if (count($ym) ==10) {
					$monthpos = 'C4:L4';
					$horizontaltotalpos = 'C5:L5';
					$endcolumn = 'L';
				} else if (count($ym) ==11) {
					$monthpos = 'C4:M4';
					$horizontaltotalpos = 'C5:M5';
					$endcolumn = 'M';
				} else if (count($ym) ==12) {
					$monthpos = 'C4:N4';
					$horizontaltotalpos = 'C5:N5';
					$endcolumn = 'N';
				}
			} else {
				if (count($ym) == 1) {
					$monthpos = 'E4:E4';
					$horizontaltotalpos = 'E5:E5';
					$endcolumn = 'E';
				} else if (count($ym) ==2) {
					$monthpos = 'E4:F4';
					$horizontaltotalpos = 'E5:F5';
					$endcolumn = 'F';
				} else if (count($ym) ==3) {
					$monthpos = 'E4:G4';
					$horizontaltotalpos = 'E5:G5';
					$endcolumn = 'G';
				} else if (count($ym) ==4) {
					$monthpos = 'E4:H4';
					$horizontaltotalpos = 'E5:H5';
					$endcolumn = 'H';
				} else if (count($ym) ==5) {
					$monthpos = 'E4:I4';
					$horizontaltotalpos = 'E5:I5';
					$endcolumn = 'I';
				} else if (count($ym) ==6) {
					$monthpos = 'E4:J4';
					$horizontaltotalpos = 'E5:J5';
					$endcolumn = 'J';
				} else if (count($ym) ==7) {
					$monthpos = 'E4:K4';
					$horizontaltotalpos = 'E5:K5';
					$endcolumn = 'K';
				} else if (count($ym) ==8) {
					$monthpos = 'E4:L4';
					$horizontaltotalpos = 'E5:L5';
					$endcolumn = 'L';
				} else if (count($ym) ==9) {
					$monthpos = 'E4:M4';
					$horizontaltotalpos = 'E5:M5';
					$endcolumn = 'M';
				} else if (count($ym) ==10) {
					$monthpos = 'E4:N4';
					$horizontaltotalpos = 'E5:N5';
					$endcolumn = 'N';
				} else if (count($ym) ==11) {
					$monthpos = 'E4:O4';
					$horizontaltotalpos = 'E5:O5';
					$endcolumn = 'O';
				} else if (count($ym) ==12) {
					$monthpos = 'E4:P4';
					$horizontaltotalpos = 'E5:P5';
					$endcolumn = 'P';
				} else if (count($ym) ==13) {
					$monthpos = 'E4:Q4';
					$horizontaltotalpos = 'E5:Q5';
					$endcolumn = 'Q';
				}
			}
			
			/*month columns*/
			$default_border = array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb'=>'000000')
			);
			$style_header = array(
			    'borders' => array(
			        'bottom' => $default_border,
			        'left' => $default_border,
			        'top' => $default_border,
			        'right' => $default_border,
			        'left' => $default_border,
			    ),
			    'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb'=>'B4E9F1'),
			    ),
			    'font' => array(
			        'bold' => true,
			    )
			    ,
			    'rowDimension' => array(
			        'rowHeight' => '100',
			    )
			);

			/*horizontal columns*/
			$horizontal_column = array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb'=>'000000')
			);
			$horizontal_style_header = array(
			    'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb'=>'DCDCDC'),
			    ),
			    'font' => array(
			        'bold' => true,
			    ),
			    'rowDimension' => array(
			        'rowHeight' => '100',
			    )
			);
			$rowstartpos = $rowstartpos;
			$noofrows  = count($array);
			$rowendpos = ($rowstartpos+$noofrows)-1;
			$objTpl->getActiveSheet()->getStyle($monthpos)->applyFromArray( $style_header );
			for ($rowHgt=5; $rowHgt <= $rowendpos; $rowHgt++) { 
				$objTpl->getActiveSheet()->getRowDimension($rowHgt)->setRowHeight(25);
				if($rowHgt % 2 == 0){
			        $objTpl->getActiveSheet()->getStyle('A'.$rowHgt.':'.$endcolumn.$rowHgt)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6F4F9');
			        $objTpl->getActiveSheet()->getStyle('A'.$rowHgt.':'.$endcolumn.$rowHgt)->getFont()->setBold(false);
			    }
			    if($request->active_select == '1') {
			    	$objTpl->getActiveSheet()->getStyle('B'.$rowHgt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			    } else {
			    	$objTpl->getActiveSheet()->getStyle('D'.$rowHgt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			    }
			    

			    $objTpl->getActiveSheet()->getStyle('A4'.':'.$endcolumn.$rowHgt)->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			    $objTpl->getActiveSheet()->getStyle('A4'.':'.$endcolumn.$rowHgt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
			//Set bottom border
			$objTpl->getActiveSheet()->getStyle('A'.$rowendpos.':'.$endcolumn.$rowendpos)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$objTpl->getActiveSheet()->getStyle($horizontaltotalpos)->applyFromArray( $horizontal_style_header );
			$objTpl->getActiveSheet()->getStyle('A5:D5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DCDCDC');
			if($request->active_select == '1') {
				$objTpl->getActiveSheet()->getStyle('B'.$rowstartpos.':'.'B'.$rowendpos)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DCDCDC');
				$objTpl->getActiveSheet()->getStyle('B'.$rowstartpos.':'.'B'.$rowendpos)->getFont()->setBold(true);
			} else {
				$objTpl->getActiveSheet()->getStyle('D'.$rowstartpos.':'.'D'.$rowendpos)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DCDCDC');
				$objTpl->getActiveSheet()->getStyle('D'.$rowstartpos.':'.'D'.$rowendpos)->getFont()->setBold(true);
			}
			
			$col = 'E';
			while(true){
			    $tempCol = $col++;
			    $objTpl->getActiveSheet()->getColumnDimension($tempCol)->setAutoSize(false);
			    $objTpl->getActiveSheet()->getColumnDimension($tempCol)->setWidth("15");
			    if($tempCol == $objTpl->getActiveSheet()->getHighestDataColumn()){
			        break;
			    }
			}

			$objTpl->setActiveSheetIndex();
			
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
	      	$flpath='.xls';
	      	header('Content-Type: application/vnd.ms-excel');
	      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      	header('Cache-Control: max-age=0');
	      	})->setFilename($excel_name)->download('xls');
		}
		return view('Salesdetails.index',[
									'account_period' => $account_period,
									'account_val' => $account_val,
									'fileCnt' => $fileCnt,
									'arrval' => $arrval,
									'mvalue' => $mvalue,
									'arrval_month1' => $arrval_month1,
									'date_month' => $date_month,
									'year_monthslt' => $year_monthslt,
									'dbnext' => $dbnext,
									'cus_list' => $cus_list,
									'yr'=>$yr,
									'sql' => $sql,
									'month_total_array' => $month_total_array,
									'jsdisp1arry' => $jsdisp1arry,
									'cntvalue' => $cntvalue,
									'Period' => $Period,
									'jsarry2' => $jsarry2,
									'jsdisp2arry' => $jsdisp2arry,
									'period' => $period,
									'array1' => $array1,
									'array2' => $array2,
									'array3' => $array3,
									'cnttl' => $cnttl,
									'avgmnth' => $avgmnth,
									'jsarry2page' => $jsarry2page,
									'addedvalue' => $addedvalue,
									'jsdisparry' => $jsdisparry,
									'year_mon_array' => $year_mon_array, 
									'monthnumb' => $monthnumb,
									'endperiod' => $endperiod,
									'cnt_array' => $cnt_array,
									'jsarry' => $jsarry,
									'getcnt1' => $getcnt1,
									'jsarry3' => $jsarry3,
									'last_year' => $last_year,
									'current_year' => $current_year,
									'dbprevious' => $dbprevious,
									'db_year_month' => $db_year_month,
									'disabledsen' => $disabledsen,
									'disabledman' => $disabledman,
									'disabledyen' => $disabledyen,
									'disabledmonthly' => $disabledmonthly,
									'disabledcustomer' => $disabledcustomer,
									'tblset' => $tblset,
									'disabledcurrentyear' => $disabledcurrentyear,
									'request' => $request]);
	}
}
?>