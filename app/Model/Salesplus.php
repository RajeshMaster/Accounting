<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Salesplus extends Model {
	public static function getfilcnt($dtmnfrm,$dtmnto,$rg) {
		$sql = "SELECT count(quot_date) as qdate FROM dev_invoices_registration 
						WHERE SUBSTR(quot_date,1,7) >= '$dtmnfrm' 
						AND SUBSTR(quot_date,1,7) <= '$dtmnto' AND del_flg = 0
						ORDER BY user_id ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetEstimateMonthTotals($datemonth,$from,$to) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.tax,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.quot_date < '$to' AND main.quot_date > '$from' AND main.del_flg = 0
		GROUP BY SUBSTR(main.quot_date,1,7)
		ORDER BY user_id ASC) as tbl1"))
					->get();
					return $sql;
	}
	public static function fnGetEstimateDetails1s($datemonth, $from=null, $to=null, $request) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.company_name,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.quot_date < '$to' AND main.quot_date > '$from' AND main.del_flg = 0
		ORDER by quot_date DESC) as tbl1"))
					->GROUPBY('trading_destination_selection')
					->ORDERBY('quot_date', 'DESC')
					->paginate($request->plimit);
					// ->toSql();dd($sql);
		return $sql;
	}
	public static function fnGetEstimateDetailsales($datemonth, $from=null, $to=null, $request) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.company_name,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.quot_date < '$to' AND main.quot_date > '$from' AND main.del_flg = 0 GROUP BY trading_destination_selection
		ORDER by quot_date DESC) as tbl1"))
					->get();
		return $sql;
	}
	public static function fnGetEstimateDetailsFlgs($datemonth, $from=null, $to=null, $request) {	
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.company_name,
		main.quot_date ,SUBSTR(main.quot_date,1,7) ym,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.del_flg = 0
		ORDER by quot_date DESC) as tbl1"))
					->GROUPBY('trading_destination_selection')
					->ORDERBY('quot_date', 'DESC')
					->paginate($request->plimit);
		return $sql;
	}
	public static function fnGetEstimateDetailsFlgsales($datemonth, $from=null, $to=null, $request) {	
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.company_name,
		main.quot_date ,SUBSTR(main.quot_date,1,7) ym,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.del_flg = 0 GROUP BY trading_destination_selection
		ORDER by quot_date DESC) as tbl1"))
					->get();			
		return $sql;
	}
	public static function selectdetails($trading_destselection) {
		$result= DB::table('mst_customerdetail')
						->SELECT('customer_id','customer_name')
						->WHERE('id', $trading_destselection)
						->get();
		return $result;
	}
	public static function fnGetEmply($datemonth,$user_id) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.company_name,main.tax,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE main.del_flg = 0 and main.trading_destination_selection = $user_id and quot_date LIKE '%$datemonth%'
		ORDER by user_id ASC) as tbl1"))
					->get();			
		return $sql;
	}
	public static function fnGetTaxCalculation($quot_date,$totalval,$tax) {
		$execute_tax = Estimation::fnGetTaxDetails($quot_date);
		$grandtotal = "";
		$divtotal1=0;
			if (!empty($totalval)) {
				if ($tax != 2) {
					$totroundval = preg_replace("/,/", "", $totalval);
					$dispval = (($totroundval * intval((isset($execute_tax[0]->Tax)?$execute_tax[0]->Tax:0)))/100);
					$grandtotal = $totroundval + $dispval;
				} else {
					$totroundval = preg_replace("/,/", "", $totalval);
					$dispval = 0;
					$grandtotal = $totroundval + $dispval;
				}
			}
			if($grandtotal =="") {
				$grandtotal = '0';
			}
			$divtotal1 += $grandtotal;
			return $divtotal1;
	}
	public static function fnGetindikis($startdate,$enddate,$trading_destselection) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.tax,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE SUBSTR(main.quot_date,1,7) >= '$startdate' 
		AND SUBSTR(main.quot_date,1,7) <= '$enddate' AND del_flg = 0
		AND main.trading_destination_selection='".$trading_destselection."' ORDER BY user_id ASC) as tbl1"))
					->get();
					return $sql;
	}
	public static function getfil1recs($passyrmn) {
		$sql = DB::TABLE(DB::raw("(SELECT main.id, main.user_id, main.tax,
		main.quot_date ,main.trading_destination_selection,( 
		                 SELECT format(sum(replace(amount, ',', '')),0) 
		                 FROM   tbl_work_amount_details 
		                 WHERE  invoice_id = main.user_id) AS totalval 
		FROM dev_invoices_registration as main 
		WHERE SUBSTR(main.quot_date,1,7) LIKE '%$passyrmn%' AND del_flg = 0
		ORDER BY user_id ASC) as tbl1"))
					->get();
					return $sql;
	}

}
?>