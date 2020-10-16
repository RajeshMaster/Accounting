<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Salesdetails extends Model {
	public static function getfilcnt($dtmnfrm,$dtmnto,$rg) {
		$sql = "SELECT count(quot_date) as qdate FROM dev_invoices_registration 
						WHERE SUBSTR(quot_date,1,7) >= '$dtmnfrm' 
						AND SUBSTR(quot_date,1,7) <= '$dtmnto' AND del_flg = 0
						ORDER BY user_id ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetEstimateMonthTotals($datemonth,$from,$to) {
		$sql = "SELECT * FROM dev_invoices_registration 
						WHERE quot_date <= '$to' 
						AND quot_date >= '$from' AND del_flg = 0 GROUP BY SUBSTR(quot_date, 1, 7) 
						ORDER BY user_id ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetEstimateDetails1s($datemonth, $from=null, $to=null, $request) {
		$sql = DB::TABLE(DB::raw("(SELECT id, user_id, company_name, totalval,quot_date ,trading_destination_selection  FROM dev_invoices_registration 
					WHERE quot_date < '$to' AND quot_date > '$from' AND del_flg = 0 
					ORDER BY quot_date DESC) as tbl1"))
					->GROUPBY('trading_destination_selection')
					->ORDERBY('quot_date', 'DESC')
					->paginate($request->plimit);
		return $sql;
	}
	public static function fnGetEstimateDetailsales($datemonth, $from=null, $to=null, $request) {
		$sql = DB::TABLE(DB::raw("(SELECT id, user_id, company_name, totalval,quot_date ,trading_destination_selection  FROM dev_invoices_registration 
					WHERE quot_date < '$to' AND quot_date > '$from' AND del_flg = 0 
					GROUP BY trading_destination_selection ORDER BY paid_date DESC) as tbl1"))
					->get();
		return $sql;
	}
	public static function fnGetEstimateDetailsFlgs($datemonth, $from=null, $to=null, $request) {	
		$sql = DB::TABLE(DB::raw("(SELECT id, user_id, company_name, totalval, quot_date, 					SUBSTR(quot_date,1,7),trading_destination_selection  
							FROM dev_invoices_registration WHERE del_flg = 0 ORDER BY 
							quot_date DESC) as tbl1"))
					->GROUPBY('trading_destination_selection')
					->ORDERBY('quot_date', 'DESC')
					->paginate($request->plimit);
		return $sql;
	}
	public static function fnGetEstimateDetailsFlgsales($datemonth, $from=null, $to=null, $request) {	
		$sql = DB::TABLE(DB::raw("(SELECT id, user_id, company_name, totalval, quot_date, 					SUBSTR(quot_date,1,7),trading_destination_selection  
							FROM dev_invoices_registration WHERE del_flg = 0 GROUP BY trading_destination_selection ORDER BY 
							paid_date DESC) as tbl1"))
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
		$result= DB::table('dev_invoices_registration')
						->SELECT('id', 'user_id', 'company_name', 'totalval', 'quot_date','tax', 'trading_destination_selection')
						->WHERE('del_flg', 0)
						->WHERE('trading_destination_selection', $user_id)
						->WHERE('quot_date','LIKE','%'.$datemonth.'%')
						->orderBy('user_id', 'ASC')
						->orderBy('Payment_date', 'ASC')
						->get();
		return $result;
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
			// echo $divtotal1;
			return $divtotal1;
	}
	public static function fnGetindikis($startdate,$enddate,$trading_destselection) {
		$sql = "SELECT * FROM dev_invoices_registration WHERE SUBSTR(quot_date,1,7) >= '$startdate' AND SUBSTR(quot_date,1,7) <= '$enddate' AND del_flg = 0
					AND trading_destination_selection='".$trading_destselection."' ORDER BY user_id ASC";
			$cards = DB::select($sql);
			return $cards;
	}
	public static function fnGetinvoiceEstimateTotalValues($date) {
		$sql = "SELECT SUM(REPLACE(totalval, ',', '')) totalval FROM dev_invoices_registration WHERE quot_date LIKE '$date%'";
			$cards = DB::select($sql);
			return $cards;
	}
	public static function getfil1recs($passyrmn) {
		$sql = "SELECT * FROM dev_invoices_registration WHERE SUBSTR(quot_date,1,7) LIKE '%$passyrmn%' AND del_flg = 0 ORDER BY user_id ASC";
				$cards = DB::select($sql);
				return $cards;
	}
}
?>