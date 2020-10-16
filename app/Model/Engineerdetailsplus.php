<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Input;
use Carbon\Carbon;
use Session;
class Engineerdetailsplus extends Model{
	public static function enggplus($request,$flg,$from,$to,$date_month) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,emp.Firstname,main.trading_destination_selection,works.emp_id as empID,(SELECT format(sum(replace(amount, ',', '')),0) 
			FROM   tbl_work_amount_details 
			WHERE  invoice_id = main.user_id) AS totalval 
			FROM   tbl_work_amount_details works 
			left join dev_invoices_registration main on works .invoice_id = main .user_id 
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
			WHERE main.quot_date < '$to' AND main.quot_date > '$from' AND main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''
			GROUP BY empID,SUBSTRING(main.quot_date,-10,7)
			ORDER by quot_date DESC
			) AS DDD GROUP BY empID"));  
      if ($flg == 1) {
          $query = $query->paginate($request->plimit);
      } else {
          $query = $query->get();
      }       
		  return $query;
	 }
	public static function fnGetEstimateRecordengPrevious($from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND ( quot_date >= '$to_date' OR accessFlg = 1 )";
		}
		// END ACCESS RIGHTS
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date <= '$from_date' $conditionAppend) AND (emp_ID1 != '' OR emp_ID2 != '' OR emp_ID3 != '' OR emp_ID4 != '' OR emp_ID5 != '' OR emp_ID6 != '' OR emp_ID7 != '' OR emp_ID8 != '' OR emp_ID9 != '' OR emp_ID10 != '' OR emp_ID11 != '' OR emp_ID12 != '' OR emp_ID13 != '' OR emp_ID14 != '' OR emp_ID15 != '') ORDER BY quot_date ASC) as tbl1"))
		->get();
		return $result;
	}
	public static function fnGetEstimateRecordengNext($to_date) {
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date >= '$to_date') AND (emp_ID1 != '' OR emp_ID2 != '' OR emp_ID3 != '' OR emp_ID4 != '' OR emp_ID5 != '' OR emp_ID6 != '' OR emp_ID7 != '' OR emp_ID8 != '' OR emp_ID9 != '' OR emp_ID10 != '' OR emp_ID11 != '' OR emp_ID12 != '' OR emp_ID13 != '' OR emp_ID14 != '' OR emp_ID15 != '') ORDER BY quot_date ASC) as tbl1"))
		->get();
		return $result;
	}
	public static function selectdetails($trading_destselection,$from,$to) {
		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.id,main.user_id,emp.Firstname,main.trading_destination_selection,works.emp_id as empID
			FROM   tbl_work_amount_details works 
			left join dev_invoices_registration main on works.invoice_id = main.user_id 
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
			WHERE main.quot_date < '$to' AND main.quot_date > '$from' AND main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''
			GROUP BY empID,SUBSTRING(main.quot_date,-10,7)
			ORDER by quot_date DESC
			) as tbl1 GROUP BY empID"))
	   ->get();
		return $result;
	}
	public static function fnGetEmplyy($yearmonth,$emp_id) {
		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,emp.Firstname,main.trading_destination_selection,works.emp_id as empID,main.tax,sum(replace(amount, ',', '')) AS totalval 
			FROM   tbl_work_amount_details works 
			left join dev_invoices_registration main on works .invoice_id = main .user_id 
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
			WHERE  main.quot_date like '%$yearmonth%' AND works.emp_id='$emp_id' AND main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''
			GROUP BY empID,SUBSTRING(main.quot_date,-10,7)
			ORDER by quot_date DESC)as  tbl1 GROUP BY empID"))
		->get();		
		return $result;
	}
	public static function getenggplus($request,$flg,$date_month) {
		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.id,main.user_id,emp.Firstname,main.trading_destination_selection,works.emp_id as empID,sum(replace(amount, ',', ''))as totalval,main.tax,SUBSTR(main.quot_date,1,7) yearmonth
			FROM   tbl_work_amount_details works 
			left join dev_invoices_registration main on works .invoice_id = main .user_id 
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
			WHERE main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''
			GROUP BY empID,main.quot_date
			ORDER by quot_date DESC) AS DDD GROUP BY empID"));
		//->toSql();dd($result);
		if ($flg == 1) {
	        $result = $result->paginate($request->plimit);
	        } else {
	        $result = $result->get();
	    }
	  return $result;
	}
	public static function selectdetails1($emp_id,$from,$to) {
		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.id, main.user_id, emp.Firstname,
			main.quot_date ,work_det.emp_id as empID,main.amount1 as amount,main.trading_destination_selection,( 
                 SELECT format(sum(replace(amount, ',', '')),0) 
                 FROM   tbl_work_amount_details 
                 WHERE  invoice_id = main.user_id) AS totalval  
			FROM dev_invoices_registration as main 
			LEFT JOIN tbl_work_amount_details work_det ON main.id=work_det.inv_primery_key_id
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=work_det.emp_id
			WHERE main.del_flg = 0
			AND work_det.emp_id IS NOT NULL AND work_det.emp_id != ''
			) as tbl1 group by empID"))
	   ->get();
		return $result;
	}
	public static function fnGetindikis($startdate,$enddate,$emp_id) {

		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,emp.Firstname,main.trading_destination_selection,works.emp_id as empID,main.tax,sum(replace(amount, ',', '')) AS totalval 
			FROM   tbl_work_amount_details works 
			left join dev_invoices_registration main on works .invoice_id = main .user_id 
			LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
			WHERE SUBSTR(main.quot_date,1,7) >= '$startdate' AND SUBSTR(main.quot_date,1,7) <= '$enddate' AND works.emp_id='$emp_id' AND main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''	
			AND works.emp_id IS NOT NULL AND works.emp_id != '' GROUP BY empID,main.quot_date
			 ORDER by user_id ASC
 			) AS DDD "))
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
			return $divtotal1;
	}

}