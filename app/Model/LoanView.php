<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
use Config;

class LoanView extends Model {
	/**  
	*  LoanEMI Details
	*  @author Madasamy 
	*  @param $request,$flg
	*  Created At 2020/08/28
	**/
	public static function fnGetLoanEMIDetails($request,$flg){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select('loanId','userId','bank','belongsTo','emiDate','year','month',
								DB::raw('SUM(monthPayment) AS monthPayment'),
								DB::raw('SUM(monthPrinciple) AS monthPrinciple'),
								DB::raw('SUM(monthInterest) AS monthInterest'))
					->where('userId', '=', $request->userId);

			if ($flg == 0) {
				$query = $query	->where('year', '=', $request->selYear)
								->where('month', '=', $request->selMonth)
								->orderBy("belongsTo")
								->orderBy("bank")
								->orderBy("loanId")
								->GroupBy('loanId', DB::raw("SUBSTRING(emiDate, 1, 7)"))
								->paginate($request->plimit);
			} 
		
		return $query;
	}

	/**  
	*  Loan Details
	*  @author Madasamy 
	*  @param $loanId
	*  Created At 2020/08/28
	**/
	public static function fnGetLoanDetails($loanId){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_details')
					->select('*')
					->where('loanId', '=', $loanId)
					->get();

			$loan = array();
			if (isset($query[0])) {
				$loan = (array)$query[0];
			}
		
		return $loan;
	}

	/**  
	*  Loan Details
	*  @author Madasamy 
	*  @param $loanId
	*  Created At 2020/08/28
	**/
	public static function fnGetCalenderBar($loanId){

		$db = DB::connection('mysql_Salary');
	    $date = $db ->Table('ams_loan_emidetails')
	                ->Select(DB::raw("SUBSTRING(emiDate, 1, 4) AS year,SUBSTRING(emiDate, 6, 2) AS month"))
	                ->Where('delFlg',0)
	                ->GroupBy(DB::raw("SUBSTRING(emiDate, 1, 7) ASC"))
					->get();
		$cursheet=array();
      	$dataAll=array();
      	$prYrMn =explode('-', date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01')))));
	    $cursheet['year']=$prYrMn[0];
	    $cursheet['month']=$prYrMn[1];
	    $k = 0;
		foreach ($date as $datevalue) {
			if($k == 0) {
				$arr = (object) $cursheet;
				array_push($dataAll,$arr);
			} 
			$dataAll[]=$datevalue;
			$k++;
		}

		if(count($dataAll) != 0){      
			$i=-1;
			$j=1;
			$first_yr=0;
			$first_month=0;
			for($yr=0;$yr<count($dataAll);$yr++) {
				$prev=$dataAll[$yr]->year;
				if($prev!=$first_yr){
					$i++;
					$prev_yrs[$i][0]=$prev;
					$first_yr=$prev;
					$first_month=0;
					$j=1;
				}
				$prevmon=$dataAll[$yr]->month;

				if($prevmon!=$first_month){
					$prev_yrs[$i][$j]=$prevmon;
					$j++;
					$first_month=$prevmon;
				}
			}

			$cur_year_flg = "0";
			for($i=0;$i<count($prev_yrs);$i++){
				if($prev_yrs[$i][0] == date("Y")){
					$cur_year_flg = "1";
				}
			}
			$previous[0]=$prev_yrs;
			
			//Get Total Years for all data
			for($i=0;$i<count($prev_yrs);$i++){
				$yrs[$i]=$prev_yrs[$i][0];
			}
			if((date("d")=="31") && (date("m")=="12")) {
				$yrs[$i]=$yrs[$i-1]+1;
			}
			$previous[1]=$yrs;
			return $previous;
		}
		
	}

	/**  
	*  LoanDetails Yearwise
	*  @author Madasamy 
	*  @param $request
	*  Created At 2020/09/01
	**/
	public static function fnGetYearCalenderBar($request){
		$db = DB::connection('mysql_Salary');
	    $date = $db ->Table('ams_loan_emidetails')
	                ->Select('Year as year','Month as month');
	    $date = $date->Where('delFlg',0)
		            // ->GroupBy(DB::raw("SUBSTRING(Date, 1, 7) ASC"))
					->get();
		$cursheet = array();
      	$dataAll = array();
      	$prYrMn = explode('-', date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01')))));
	    $cursheet['year'] = $prYrMn[0];
	    $cursheet['month'] = $prYrMn[1];
	    $k = 0;
		foreach ($date as $datevalue) {
			$dataAll[] = $datevalue;
			if( $k == 0 ) {
				$arr = (object) $cursheet;
				array_push($dataAll,$arr);
			} 
			$k++;
		}
		if(count($dataAll) != 0){      
			$i = -1;
			$j = 1;
			$first_yr = 0;
			$first_month = 0;
			for($yr = 0; $yr < count($dataAll); $yr++) {
				$prev = $dataAll[$yr]->year;
				if($prev != $first_yr){
					$i++;
					$prev_yrs[$i][0] = $prev;
					$first_yr = $prev;
					$first_month = 0;
					$j = 1;
				}
				$prevmon = $dataAll[$yr]->month;

				if($prevmon != $first_month){
					$prev_yrs[$i][$j] = $prevmon;
					$j++;
					$first_month = $prevmon;
				}
			}

			$cur_year_flg = "0";
			for($i = 0; $i < count($prev_yrs); $i++){
				if($prev_yrs[$i][0] == date("Y")){
					$cur_year_flg = "1";
				}
			}
			$previous[0] = $prev_yrs;
			
			//Get Total Years for all data
			for($i = 0; $i< count($prev_yrs); $i++){
				$yrs[$i] = $prev_yrs[$i][0];
			}
			if((date("d") == "31") && (date("m") == "12")) {
				$yrs[$i] = $yrs[$i-1]+1;
			}
			$previous[1] = $yrs;
			return $previous;
		}
		
	}

	/**  
	*  LoanEMI Details
	*  @author Madasamy 
	*  @param $request
	*  Created At 2020/08/28
	**/
	public static function fnGetYearwiseDetails($request){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select('*')
					->where('userId', '=', $request->userId)
					->where('year', '=', $request->selYear)
					->orderBy("belongsTo")
					->orderBy("bank")
					->orderBy("loanId")
					->groupBy("loanId")
					->paginate($request->plimit);
		
		return $query;
	}

	/**  
	*  LoanEMI Details
	*  @author Madasamy 
	*  @param $request,$loanId,$month
	*  Created At 2020/08/28
	**/
	public static function fnGetLoanMonthPay($request,$loanId,$month){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select(DB::raw('SUM(monthPayment) AS monthPayment'))
					->where('userId', '=', $request->userId)
					->where('loanId', '=', $loanId)
					->where('year', '=', $request->selYear)
					->where('month', '=', $month)
					->get();

		$monthPayment = "";
		if (isset($query[0])) {
			$monthPayment = $query[0]->monthPayment;
		} 

		return $monthPayment;
	}

	/**  
	*  LoanEMI Yearwise Payment Details
	*  @author Madasamy 
	*  @param $request,$loanId
	*  Created At 2020/09/15
	**/
	public static function fnGetYrTotPay($request,$loanId){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select(DB::raw('SUM(monthPrinciple) AS monthPrinciple'),
							 DB::raw('SUM(monthInterest) AS monthInterest'))
					->where('userId', '=', $request->userId)
					->where('loanId', '=', $loanId)
					->where('year', '=', $request->selYear)
					->get();
					
		$amtArr = array();
		if (isset($query[0])) {
			foreach ($query as $key => $value) {
				$amtArr['monthPrinciple'] = $query[0]->monthPrinciple;
				$amtArr['monthInterest'] = $query[0]->monthInterest;
			}
		} 
		return $amtArr;
	}

	/**  
	*  LoanEMI Yearwise Payment Details
	*  @author Madasamy 
	*  @param $request,$belongsToId,$year
	*  Created At 2020/08/28
	**/
	public static function fnGetYearwiseTot($request,$belongsToId,$year){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select(DB::raw('SUM(monthPayment) AS monthPayment'))
					->where('userId', '=', $request->userId)
					->where('belongsTo', '=', $belongsToId)
					->where('year', '=', $year)
					->get();

		$monthPayment = "";
		if (isset($query[0])) {
			$monthPayment = $query[0]->monthPayment;
		} 

		return $monthPayment;
	}

	/**  
	*  Family Member Details
	*  @author Madasamy 
	*  @param $orderId=""
	*  Created At 2020/08/25
	**/
	public static function fnGetFamilyMaster($id=""){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_family_master')
					->select('id','familyName','nickName')
					->where('delFlg', '=', 0);

				if ($id !="") {
					$query = $query ->where('id', '=', $id);
				}
				$query = $query->get();

			$members = array();
			foreach ($query as $key => $value) {
				if ($id !="") {
					return $value->familyName;
				} else {
					$members[$value->id] = $value->familyName;
				}
			}

		return $members;
	}

	/**  
	*  Bank Details
	*  @author Madasamy 
	*  @param 
	*  Created At 2020/08/25
	**/
	public static function fnGetBankMaster($id=""){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_bankname_master')
					->select('id','bankName')
					->where('delFlg', '=', 0);

				if ($id !="") {
					$query = $query ->where('id', '=', $id);
				}
				$query = $query->get();
			$bank = array();
			foreach ($query as $key => $value) {
				if ($id !="") {
					return $value->bankName;
				} else {
					$bank[$value->id] = $value->bankName;
				}
			}

		return $bank;
	}

	/**  
	*  Bank Details
	*  @author Madasamy 
	*  @param $loanId,$date="",$type="",$yrMnth=""
	*  Created At 2020/08/25
	**/
	public static function fnGetEMIData($loanId,$date="",$type="",$yrMnth=""){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select("*")
					->where('loanId', '=', $loanId);

			if ($date != "" && $type != "") {
				if ($type == "next") {
					$query = $query ->where('emiDate', '>=', $date);
				} else if ($type == "prev") {
					$query = $query ->where('emiDate', '<=', $date);
				}
			} elseif ($yrMnth != "") {
				$query = $query ->where('emiDate', 'LIKE', $yrMnth."%");
			}

			$query = $query->get();
		return $query;
	}
}
