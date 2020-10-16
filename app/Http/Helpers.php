<?php
namespace App\Http;
use stdClass;
use Session;
use DB;
use Config;
use Input;
use File;
use DateTime;
class Helpers {
	public static function array_to_obj($array, &$obj) {
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$obj->$key = new stdClass();
				self::array_to_obj($value, $obj->$key);
			} else {
				$obj->$key = $value;
			}
	 	 }
		return $obj;
	}
	public static function displayYear_MonthEst($account_period, $year_month, $db_year_month, $seldate, 
													$dbnext, $dbprevious, $lastyear, $currentyear, $account_val) {
			//SYSTEM CURRENT YEAR
			if (empty($seldate)) {
				$sys_cur_month=date('n');
				$sys_cur_year=date('Y');
			} else {
				$split_seldate = explode('-', $seldate);
				$sys_cur_month=$split_seldate[1];
				$sys_cur_year=$split_seldate[0];
			}
			$n_mnt = "";
			$n_yr = "";
			$p_filename = "";
			$n_filename = "";
			$nextcnt = count($dbnext);
			if (count($dbnext) > 0) {
				$splitval = explode('-', current($dbnext));
				$n_mnt = $splitval[1];
				$n_yr = $splitval[0];
				$n_filename = "nextenab.png";
			} else {
				$n_filename = "nextdisab.png";
			}

			$p_mnt = "";
			$p_yr = "";
			$prevcnt = count($dbprevious);
			if (count($dbprevious) > 0) {
				$splitval = explode('-', end($dbprevious));
				$p_mnt = $splitval[1];
				$p_yr = $splitval[0];
				$p_filename = "previousenab.png";
			} else {
				$p_filename = "previousdisab.png";
			} 
			if($prevcnt!=0){
				$style="style='cursor:pointer'";
			}else{
				$style="style='cursor:default'";
			}
			if($nextcnt!=0){
				$style1="style='cursor:pointer'";
			}else{
				$style1="style='cursor:default'";
			}

			$count_yrs=count($year_month);
			//YEAR ROW
			echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:0px;height:25px;\"><div style=\"margin-top:2px;\">&nbsp;&nbsp;";
			echo "<span $style><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15' onclick = 'return getData($p_mnt,$p_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val - 1))';></span>&nbsp;";
			echo "<b>".$account_val."&nbsp;期</b>&nbsp;";
			echo "<span $style1><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15' onclick = 'return getData($n_mnt,$n_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val + 1))';></span>&nbsp;";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

			foreach ($year_month AS $year => $montharr) {
				if ($year == $sys_cur_year) {
					echo "<span class=\"currentheader\">&nbsp;".$year."年&nbsp;</span>&nbsp";
				} else {
					echo "&nbsp;&nbsp;".$year."年&nbsp;";
				}
				foreach ($montharr AS $month => $monthval) {
					if ($month == $sys_cur_month) {
						echo "<span class=\"currentheader\">&nbsp;".$month."月&nbsp;</span>&nbsp";
					} else if (isset($db_year_month[$year][$month]) && $month == $db_year_month[$year][$month]) {
						$mon = substr("0" . $month, -2);
						echo "<span class=\"spnOver\"><a href=\"javascript:getData('$mon','$year', 0, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, $account_val);\" class=\"bordera\">&nbsp;".$month."月&nbsp;</a></span>";
					} else {
						echo "&nbsp;".$month."月&nbsp;";
					}
				}
				echo "&nbsp;&nbsp;";
			}
			echo "</div></div>";
		}
		//年月public 
		public static function displayYear_Monthtimesheet($account_period, $year_month, $db_year_month, $seldate, $dbnext, $dbprevious, $lastyear, $currentyear, $account_val) {
			//SYSTEM CURRENT YEAR
			if (empty($seldate)) {
				$sys_cur_month=date('n');
				$sys_cur_year=date('Y');
			} else {
				$split_seldate = explode('-', $seldate);
				$sys_cur_month=$split_seldate[1];
				$sys_cur_year=$split_seldate[0];
			}
			$n_mnt = "";
			$n_yr = "";
			$p_filename = "";
			$n_filename = "";
			$nextcnt = count($dbnext);
			if (count($dbnext) > 0) {
				$splitval = explode('-', current($dbnext));
				$n_mnt = $splitval[1];
				$n_yr = $splitval[0];
				$n_filename = "nextenab.png";
			} else {
				$n_filename = "nextdisab.png";
			}
			$p_mnt = "";
			$p_yr = "";
			$prevcnt = count($dbprevious);
			if (count($dbprevious) > 0) {
				$splitval = explode('-', end($dbprevious));
				$p_mnt = $splitval[1];
				$p_yr = $splitval[0];
				$p_filename = "previousenab.png";
			} else {
				$p_filename = "previousdisab.png";
			}
			if($prevcnt!=0){
				$style="style='cursor:pointer'";
			}else{
				$style="style='cursor:default'";
			}
			if($nextcnt!=0){
				$style1="style='cursor:pointer'";
			}else{
				$style1="style='cursor:default'";
			}
			$count_yrs=count($year_month);
			//YEAR ROW
			echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:0px;height:25px;\"><div style=\"margin-top:2px;\">&nbsp;&nbsp;";
			echo "<span $style><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15' onclick = 'return getData($p_mnt,$p_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val - 1))';></span>&nbsp;";
			echo "<b>".$account_val."&nbsp;期</b>&nbsp;";
			echo "<span $style1><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15' onclick = 'return getData($n_mnt,$n_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val + 1))';></span>&nbsp;";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			foreach ($year_month AS $year => $montharr) {
				if ($year == $sys_cur_year) {
					echo "<span class=\"currentheader\">&nbsp;".$year."年&nbsp;</span>&nbsp";
				} else {
					echo "&nbsp;&nbsp;".$year."年&nbsp;";
				}
				foreach ($montharr AS $month => $monthval) {
					if (isset($db_year_month[$year][$month])) {
						$db_year_month[$year][$month] = $db_year_month[$year][$month];
					} else {
						$db_year_month[$year][$month] = "";
					}
					if ($month == $sys_cur_month) {
						echo "<span class=\"currentheader\">&nbsp;".$month."月&nbsp;</span>&nbsp";
					} else if ($month == $db_year_month[$year][$month]) {
						$mon = substr("0" . $month, -2);
						echo "<span class=\"spnOver\"><a href=\"javascript:getData('$mon','$year', 0, 
						$prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, $account_val);\" 
						class=\"bordera\">&nbsp;".$month."月&nbsp;</a></span>";
					} else {
						echo "&nbsp;".$month."月&nbsp;";
					}
				}
				echo "&nbsp;&nbsp;";
			}
			echo "</div></div>";
		}
	public static function displayYear_Monthpayment($account_period, $year_month, 								$db_year_month, $seldate,$dbnext, $dbprevious, $lastyear,$currentyear, $account_val) {
			//SYSTEM CURRENT YEAR
			if (empty($seldate)) {
				$sys_cur_month=date('n');
				$sys_cur_year=date('Y');
			} else {
				$split_seldate = explode('-', $seldate);
				$sys_cur_month=$split_seldate[1];
				$sys_cur_year=$split_seldate[0];
			}
			$n_mnt = "";
			$n_yr = "";
			$p_filename = "";
			$n_filename = "";
			$nextcnt = count($dbnext);
			if (count($dbnext) > 0) {
				$splitval = explode('-', current($dbnext));
				$n_mnt = $splitval[1];
				$n_yr = $splitval[0];
				$n_filename = "nextenab.png";
			} else {
				$n_filename = "nextdisab.png";
			}

			$p_mnt = "";
			$p_yr = "";

			$prevcnt = count($dbprevious);
			if (count($dbprevious) > 0 && isset($dbprevious[1])) {
				$splitval = explode('-', end($dbprevious));
				$p_mnt = $splitval[1];
				$p_yr = $splitval[0];
				$p_filename = "previousenab.png";
			} else {
				$p_filename = "previousdisab.png";
			} 
			if($prevcnt!=0){
				$style="style='cursor:pointer'";
			}else{
				$style="style='cursor:default'";
			}
			if($nextcnt!=0){
				$style1="style='cursor:pointer'";
			}else{
				$style1="style='cursor:default'";
			}

			$count_yrs=count($year_month);
			//YEAR ROW
			echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:0px;height:25px;\"><div style=\"margin-top:2px;\">&nbsp;&nbsp;";
			echo "<span $style><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15' onclick = 'return getData($p_mnt,$p_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val - 1))';></span>&nbsp;";
			echo "<b>".$account_val."&nbsp;期</b>&nbsp;";
			echo "<span $style1><img style=\"vertical-align:middle;padding-bottom:3px;\" src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15' onclick = 'return getData($n_mnt,$n_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val + 1))';></span>&nbsp;";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

			foreach ($year_month AS $year => $montharr) {
				if ($year == $sys_cur_year) {
					echo "<span class=\"currentheader\">&nbsp;".$year."年&nbsp;</span>&nbsp";
				} else {
					echo "&nbsp;&nbsp;".$year."年&nbsp;";
				}
				foreach ($montharr AS $month => $monthval) {
					if ($month == $sys_cur_month) {
						echo "<span class=\"currentheader\">&nbsp;".$month."月&nbsp;</span>&nbsp";
					} else if (isset($db_year_month[$year][$month]) && $month == $db_year_month[$year][$month]) {
						$mon = substr("0" . $month, -2);
						echo "<span class=\"spnOver\"><a href=\"javascript:getData('$mon','$year', 0, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, $account_val);\" class=\"bordera\">&nbsp;".$month."月&nbsp;</a></span>";
					} else {
						echo "&nbsp;".$month."月&nbsp;";
					}
				}
				echo "&nbsp;&nbsp;";
			}
			echo "</div></div>";
		}
	public static function ordinalize($num) {
		$suff = 'th';
        if ( ! in_array(($num % 100), array(11,12,13))){
            switch ($num % 10) {
                case 1:  $suff = 'st'; break;
                case 2:  $suff = 'nd'; break;
                case 3:  $suff = 'rd'; break;
            }
            return "{$num}{$suff}";
        }
        return "{$num}{$suff}";
	}
	function singlefieldlength($stringname, $len=null)
    {
        if (mb_strlen($stringname,'UTF-8')> $len) {
            $stringname=mb_substr($stringname, 0, $len,'UTF-8')."...";
            return $stringname;
        }
        return $stringname;
    }
    public static function fnGetTaxDetails($quotdate) {
    	$Estimate = db::table('dev_taxdetails')
									->select('*')
									->where('Startdate','<=',$quotdate)
									->WHERE('delflg',0)
									->orderBy('Startdate', 'DESC')
									->orderBy('Ins_TM', 'DESC')
									->LIMIT(1)
									->get();
			return $Estimate;
    }
    public static function displayYearMon_view($search_flg,$totalRec,$currentRec,
													$date_month,$get_view,$curTime,$order,$sort,$invid) {
			//SYSTEM CURRENT YEAR
			if (empty($date_month)) {
				$sys_cur_month=date('n');
				$sys_cur_year=date('Y');
			} else {
				$split_seldate = explode('-', $date_month);
				$sys_cur_month=$split_seldate[1];
				$sys_cur_year=$split_seldate[0];
			}
			$p_filename = "";
			$n_filename = "";
			
			if ($totalRec > $currentRec) {
				$n_filename = "nextenab.png";
				$stylePre = "style='cursor:pointer'";
			} else {
				$n_filename = "nextdisab.png";
				$stylePre = "style='cursor:default'";
			}
			if ( 1 < $currentRec ) {
				$p_filename = "previousenab.png";
				$stylePost = "style='cursor:pointer'";
			} else {
				$p_filename = "previousdisab.png";
				$stylePost = "style='cursor:default'";
			}
			if ( $order == "DESC" ) {
				$currentRec1 = $currentRec+1;
			} else{
				$currentRec1 = $currentRec-1;
			}
			if (isset($get_view[$currentRec - 1]['id'])) {
				$get_view[$currentRec - 1]['id'] = $get_view[$currentRec - 1]['id'];
			} else {
				$get_view[$currentRec - 1]['id'] = 0;
			}
			if (isset($get_view[$currentRec + 1]['id'])) {
				$get_view[$currentRec + 1]['id'] = $get_view[$currentRec + 1]['id'];
			} else {
				$get_view[$currentRec + 1]['id'] = 0;
			}
			$get_viewleft = $get_view[$currentRec - 1]['id'];
			$get_viewright = $get_view[$currentRec + 1]['id'];
			if (!empty($search_flg)) {
				$mon_select_val= "<b>".$currentRec."/".$totalRec."&nbsp;</b>&nbsp;";
			}else{
				$mon_select_val= "<b>".$sys_cur_month."&nbsp;月分"." ".$currentRec."/".$totalRec."&nbsp;</b>&nbsp;";
			}
			//YEAR ROW
			//echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:-18px;height:20px;\"><div style=\"background-color: #FFFFFF;margin-top:2px;\">&nbsp;&nbsp;";
			if ($currentRec == 1) {
				echo "<span $stylePost><img class='vam' src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15'></span>&nbsp;";
			} else {
				echo "<span $stylePost><img class='vam' src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15' onclick = 'return getData_view($totalRec,$currentRec-1,$date_month,$get_viewleft,$curTime,$invid)';></span>&nbsp;";
			}

			    echo $mon_select_val;

			if ($currentRec == $totalRec) {
				echo "<span $stylePre><img class='vam' src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15'></span>&nbsp;";
			} else {
				echo "<span $stylePre><img class='vam' src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15' onclick = 'return getData_view($totalRec,$currentRec+1,$date_month,$get_viewright,$curTime,$invid)';></span>&nbsp;";
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
    public static function fnfetchinvoicebalance($did){
    	$db=DB::connection('mysql');
		$query=$db->TABLE($db->raw("(SELECT invoice_id,id,totalval,paid_id,
						(SELECT SUM(replace(deposit_amount, ',', '')) 
						FROM dev_payment_registration WHERE invoice_id = '$did') 
						as deposit_amount FROM dev_payment_registration 
						WHERE invoice_id = '$did' ORDER BY id DESC) as tb1"))
					->get();
		return $query;
    }
    public static function checkTELFAX($str) {
			$rval = "";
			if (!empty($str)) {
				if (strlen($str) == 10) {
					$rval = substr($str, 0, 2) . '-' . substr($str, 2, 4) . '-' . substr($str, 6);
					return $rval;
				} else if (strlen($str) == 11) {
					$rval = substr($str, 0, 3) . '-' . substr($str, 3, 4) . '-' . substr($str, 7);
					return $rval;
				} else {
					return $str;
				}
			} else {
				return $str;
			}
		}
	public static function displayYear_MonthEst1($account_period, $year_month, $db_year_month, $seldate, $dbnext, $dbprevious, $lastyear, $currentyear, $account_val) {
			if (empty($seldate)) {
				$sys_cur_month=date('n');
				$sys_cur_year=date('Y');
			} else {
				$split_seldate = explode('-', $seldate);
				$sys_cur_month=$split_seldate[1];
				$sys_cur_year=$split_seldate[0];
			}
			$n_mnt = "";
			$n_yr = "";
			$p_filename = "";
			$n_filename = "";
			$nextcnt = count($dbnext);
			if (count($dbnext) > 0) {
				$splitval = explode('-', current($dbnext));
				$n_mnt = $splitval[1];
				$n_yr = $splitval[0];
				$n_filename = "nextenab.png";
			} else {
				$n_filename = "nextdisab.png";
			}
			$p_mnt = "";
			$p_yr = "";
			$prevcnt = count($dbprevious);
			if (count($dbprevious) > 0) {
				$splitval = explode('-', end($dbprevious));
				$p_mnt = $splitval[1];
				$p_yr = $splitval[0];
				$p_filename = "previousenab.png";
			} else {
				$p_filename = "previousdisab.png";
			}
			$count_yrs=count($year_month);
			echo "<span style='cursor:pointer'><img src='" . '../resources/assets/images/' . $p_filename . "' width='15' height='15' onclick = 'return getData($p_mnt,$p_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val - 1))';></span>&nbsp;";
			echo "<b>".$account_val."&nbsp;期</b>&nbsp;";
			echo "<span style='cursor:pointer'><img src='" . '../resources/assets/images/' . $n_filename . "' width='15' height='15' onclick = 'return getData($n_mnt,$n_yr, 1, $prevcnt, $nextcnt, $account_period,$lastyear, $currentyear, ($account_val + 1))';></span>&nbsp;";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
}