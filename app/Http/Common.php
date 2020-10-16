<?php
namespace App\Http;
use stdClass;
use Session;
use DB;
use Config;
use Input;
use File;
use DateTime;
class Common {
	const DEF_PAGE_COUNT = 50;
	public static function fnGetMaximumDateofMonth($year_month_day) {
		$noofdays = date('t', strtotime($year_month_day));
			return $noofdays;
	}
	public static function getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period) {
		$arr_yr_mn = array_keys($year_month);
		if( $account_close_mn == 12 ) {
			$yr_mn = $arr_yr_mn[0];
		} else {
			if(isset($arr_yr_mn[1])) { 
			$yr_mn = $arr_yr_mn[1];
			} else {
			$yr_mn = "";
			} 
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
	public static function fnGetDispMonthRecordeng($in) {
		$month_record = array('', 'PresentMonth', 'NextMonth', 'MonthAfter Next', 'OTHERS');
		return $month_record[$in];
	}
	public static function fnGetDispMonthRecordjap($in) {
		$month_record = array('', '当月', '翌月', '翌々月', 'その他');
		return $month_record[$in];
	}
}
?>