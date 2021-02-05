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


	/**  
	*  YearMonth Bar
	*  @author Madasamy 
	*  @param $prev_yrs,$cur_year,$cur_month,$total_yrs,$curtime
	*  Created At 2020/08/28
	**/
	public static function displayYear_Month($prev_yrs,$cur_year,$cur_month,$total_yrs,$curtime){
		//SYSTEM CURRENT YEAR
		$months[] = "";
		$sys_cur_month=date('m');
		$sys_cur_year=date('Y');
		$count_yrs=count($total_yrs);
		//YEAR ROW
		echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:0px;height:25px;\"><div style=\"background-color: white;margin-top:0px;\">&nbsp;&nbsp;";
		if($count_yrs==0) {
			echo "<b>1&nbsp;年間</b>&nbsp;&nbsp;";
		} else {
			echo "<b>".$count_yrs."&nbsp;年間</b>&nbsp;&nbsp;";
		}
		if($count_yrs==0){
			echo "＜＜&nbsp;<span class=\"currentheader\">&nbsp;".$sys_cur_year."年&nbsp;</span>&nbsp;＞＞";
		} else if($count_yrs==1){
			echo "＜＜&nbsp;<span class=\"currentheader\">&nbsp;".$total_yrs[0]."年&nbsp;</span>&nbsp;＞＞";
		} else if($count_yrs<=2){
		$cnt=$count_yrs-1;
		echo "<span>＜＜</span>";
		for($year=0;$year<$count_yrs;$year++){
			if($cur_year==$total_yrs[$year]){
				echo "<span class=\"currentheader\">&nbsp;".$cur_year."年&nbsp;</span>&nbsp;";
			} else {
				$yr=$total_yrs[$year];
				echo "<span class=\"spnOver\"><a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">&nbsp;".$yr."年&nbsp;</a></span>&nbsp;";
			}
		}
		echo "<span>＞＞</span>";
		} else if($count_yrs>2){
			//FIND THE INDEX OF THE SELECTED YEAR
			$inx=0;
			$cnt=$count_yrs;
			for($year=0;$year<$count_yrs;$year++){
				if($cur_year==$total_yrs[$year]){
					$inx=$year;
				}
			}
			if($inx==0){  //FIRST YEAR
				echo "<span>＜＜</span>";
				echo "<span class=\"currentheader \">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>&nbsp;";
				$yr=$total_yrs[$inx+1];
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>";
				$yr=$total_yrs[$inx+2];
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">＞＞</a></span>";
			}else if($inx==$cnt-1){       //LAST YEAR
				$yr=$total_yrs[$inx-2];
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">＜＜</a></span>";
				$yr=$total_yrs[$inx-1];
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>&nbsp;";
				echo "<span class=\"currentheader\">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>";
				echo "<span>＞＞</span>";
			}else{    //OTHERWISE
				// else if for no previous year identification(updated on 2019-12-26).
				if($inx-2 >= 0) {
					$yr=$total_yrs[$inx-2]; 
				} else if($inx-1 == 0) {
					$yr = ""; 
				} else  {
					$yr=$total_yrs[$inx]; 
				}
				if($yr==''){
					echo "<span>＜＜</span>";
				}else{
					echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">＜＜</a></span>";
				}
				$yr=$total_yrs[$inx-1];
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>&nbsp;";
				echo "<span class=\"currentheader\">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>";
				$yr=$total_yrs[$inx+1];
				// $yr=$total_yrs[$inx+2];
				if($yr==''){
					echo "<span>＞＞</span>";
				}else{
					echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$cur_month','$yr','$curtime');\" class=\"bordera pageload\">＞＞</a></span>";
				}
			}
		}
		echo "&nbsp;&nbsp;";
		//FIND THE MONTHS WHICH HAS DATA FOR SELECTED YEAR
		for($i=0;$i<count($prev_yrs);$i++){
			if($cur_year==$prev_yrs[$i][0]){
				$months=$prev_yrs[$i];
			}
		}
		//MONTH ROW
		for($month=1;$month<=12;$month++){
			if($month==$cur_month){
				echo "&nbsp;<span class=\"currentheader\">&nbsp;".$month."月&nbsp;</span>&nbsp;";
			} else if(count($months)<1 || array_search($month, $months)==NULL){
				echo "&nbsp;&nbsp;".$month."月&nbsp;";
			} else if(($month<$cur_month)&&($month<=$sys_cur_month)){
				$mon=str_pad($month,2,"0",STR_PAD_LEFT);
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$mon','$cur_year','$curtime');\" class=\"bordera pageload\">&nbsp;".$month."月&nbsp;</a></span>";
			} else if($month<=$sys_cur_month){
				$mon=str_pad($month,2,"0",STR_PAD_LEFT);
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$mon','$cur_year','$curtime');\" class=\"bordera pageload\">&nbsp;".$month."月&nbsp;</a></span>";
			} else if($cur_year<$sys_cur_year){
				$mon=str_pad($month,2,"0",STR_PAD_LEFT);
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$mon','$cur_year','$curtime');\" class=\"bordera pageload\">&nbsp;".$month."月&nbsp;</a></span>";
			} else if($month > $sys_cur_month){
				$mon=str_pad($month,2,"0",STR_PAD_LEFT);
				echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getDataMonth('$mon','$cur_year','$curtime');\" class=\"bordera pageload\">&nbsp;".$month."月&nbsp;</a></span>";
			} else{
				echo $month."月&nbsp;";
			}
		}
		echo "</div></div>";
	}


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

	public static function displayYear($prev_yrs,$cur_year,$total_yrs,$curtime){

      //SYSTEM CURRENT YEAR

      $months[] = "";

      $sys_cur_month=date('m');

      $sys_cur_year=date('Y');

      $count_yrs=count($total_yrs);

      //YEAR ROW

      echo "<div class=\"yrBorder\" align=\"center\" style=\"margin-top:0px;height:25px;\"><div style=\"background-color: white;margin-top:0px;\">&nbsp;&nbsp;";

      if($count_yrs==0) {

        echo "<b>1&nbsp;年間</b>&nbsp;&nbsp;";

      } else {

        echo "<b>".$count_yrs."&nbsp;年間</b>&nbsp;&nbsp;";

      }

      if($count_yrs==0){

        echo "＜＜&nbsp;<span class=\"currentheader\">&nbsp;".$sys_cur_year."年&nbsp;</span>&nbsp;＞＞";

      } else if($count_yrs==1){

        echo "＜＜&nbsp;<span class=\"currentheader\">&nbsp;".$total_yrs[0]."年&nbsp;</span>&nbsp;＞＞";

      } else if($count_yrs<=2){

        $cnt=$count_yrs-1;

        echo "<span>＜＜</span>";

        for($year=0;$year<$count_yrs;$year++){

          if($cur_year==$total_yrs[$year]){

            echo "<span class=\"currentheader\">&nbsp;".$cur_year."年&nbsp;</span>&nbsp;";

          } else {

            $yr=$total_yrs[$year];

            echo "<span class=\"spnOver\"><a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">&nbsp;".$yr."年&nbsp;</a></span>&nbsp;";

          }

        }

        echo "<span>＞＞</span>";

      } else if($count_yrs>2){

        //FIND THE INDEX OF THE SELECTED YEAR

        $inx=0;

        $cnt=$count_yrs;

        for($year=0;$year<$count_yrs;$year++){

          if($cur_year==$total_yrs[$year]){

            $inx=$year;

          }

        }

        if($inx==0) {            //FIRST YEAR

          echo "<span>＜＜</span>";

          echo "<span class=\"currentheader \">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>&nbsp;";

          $yr=$total_yrs[$inx+1];

          echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>";

          /*echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera\">".$yr."年</a></span>";

           $yr=$total_yrs[$inx+3];*/

          echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">＞＞</a></span>";

        } else if($inx==$cnt-1){       //LAST YEAR

          $yr=$total_yrs[$inx-1];

          echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">＜＜</a></span>";

          /*$yr=$total_yrs[$inx-2];

           echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera\">".$yr."年</a></span>";*/

          $yr=$total_yrs[$inx-1];

          echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>&nbsp;";

          echo "<span class=\"currentheader\">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>";

          echo "<span>＞＞</span>";

        } else {                //OTHERWISE

          if($inx-2 > 0) {

            $yr = $total_yrs[$inx-1]; 

          } else {

            $yr = $total_yrs[$inx-1]; 

          }

          if($yr==''){

            echo "<span>＜＜</span>";

          }else {

            echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">＜＜</a></span>";

          }

          $yr=$total_yrs[$inx-1];

          echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">".$yr."年</a></span>&nbsp;";

          echo "<span class=\"currentheader\">&nbsp;".$total_yrs[$inx]."年&nbsp;</span>";

          $yr=$total_yrs[$inx+1];

          /*echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera\">".$yr."年</a></span>";

           $yr=$total_yrs[$inx+2];*/

          if($yr==''){

            echo "<span>＞＞</span>";

          }else{

            echo "<span class=\"spnOver\">&nbsp;<a href=\"javascript:getData('$yr','$curtime');\" class=\"bordera pageload\">＞＞</a></span>";

          }

        }

      }

      echo "&nbsp;&nbsp;";

      echo "</div></div>";

    }

}