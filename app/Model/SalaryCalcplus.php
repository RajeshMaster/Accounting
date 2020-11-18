<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;
use Illuminate\Database\Query\Builder;

class SalaryCalcplus extends Model{
	
	public static function fnGetdetailsfromemp() {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main_emp')
					->where('delflg','=',0)
					->count();
		return $query;
	}

	public static function getTempDetails($request) {
		if (!isset($request->selMonth)) { 
			$month = date("m", strtotime("-1 months", strtotime(date('Y-m-01'))));
		} else{
			$month = $request->selMonth;
		}
		if (!isset($request->selYear)) { 
			$year = date('Y');
		} else{
			$year = $request->selYear;
		}
		$db = DB::connection('mysql_Salary');
		$query=$db->table('inv_salaryplus_main_emp')
					->SELECT('*')
					->where('month','=',$month)
					->where('year','=',$year)
					->where('delFLg','=',0)
					->get();
		$querycount = count($query);
		return $querycount;
	}

	public static function fnGetAccountPeriod() {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('dev_kessandetails')
					->where('delflg','=',0)
					->get();
		return $query;	
	}

	public static function fnGetmnthRecord($from_date, $to_date) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->SELECT('year_mon AS date','year','month')
					->WHERE('year_mon','>=',$from_date,' AND','year_mon','<',$to_date)
					->WHERE('delFlg','=',0)
					->ORDERBY('year_mon', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function fnGetmnthRecordPrevious($from_date) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->SELECT(DB::raw("SUBSTRING(year_mon, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('year_mon','<=',$from_date)
					->ORDERBY('year_mon', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function fnGetmnthRecordNext($to_date) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->SELECT(DB::raw("SUBSTRING(year_mon, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('year_mon','>=',$to_date)
					->ORDERBY('year_mon', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function salaryDetail($request,$lastyear,$lastmonth,$flg,$empid = null){
		$db = DB::connection('mysql_Salary');
		$query = $db->TABLE(
					$db->raw("
						(SELECT salemp.Emp_ID,
								invsal.id,
								invsal.date,
								invsal.year,
								invsal.month,
								invsal.Salary,
								invsal.Deduction,
								invsal.mailFlg,invsal.Travel,
								invsal.salamt FROM inv_salaryplus_main_emp AS salemp 
						LEFT JOIN inv_salaryplus_main AS invsal 
							ON invsal.Emp_ID = salemp.Emp_ID 
							AND invsal.year = ".$lastyear." 
							AND invsal.month= ".$lastmonth." 
						WHERE salemp.year = ".$lastyear." 
							AND salemp.month = ".$lastmonth.") as tbl"));

						if ($empid != '') {
							$query = $query->WHERE('Emp_ID','=',$empid);
						}
						$query = $query ->orderBy('Emp_ID','ASC')
			        			->get();
	 	return $query;
	}

	public static function getAllEmpDetails($request,$flg) {
		if(($request->year != "")&&($request->month != "")) {
			$year = $request->year;
			$month = $request->month ;
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}

		$db = DB::connection('mysql_Salary');
		$selectedEmployees = $db->table('inv_salaryplus_main_emp')
				->SELECT('Emp_ID')
				->WHERE('year','=', $year)
				->WHERE('month','=',$month)
				->ORDERBY('Emp_ID', 'ASC')
 	 			->GET();
 	 	$hdn_empid = array();
 		foreach ($selectedEmployees as $k => $v) {
			$hdn_empid[$k] = $v->Emp_ID;
		}

		$db_mb = DB::connection('mysql_MB');
		$employees = $db_mb->TABLE('emp_mstemployees')
							->SELECT('Emp_ID','FirstName','LastName','resign_id','resigndate')
							->WHERE('delFLg', '=', 0)
							// ->WHERE('resign_id', '=', 0)
							->WHERE('Title', '=', 2)
							->where('Emp_ID', 'NOT LIKE', '%NST%');
			if ($flg == 0) {
				$employees = $employees ->whereNotIn('Emp_ID', $hdn_empid);
			} else if ($flg == 1) {
				$employees = $employees ->whereIn('Emp_ID', $hdn_empid);
			}
			$employees = $employees ->orderBy('Emp_ID', 'ASC')
									->get();
		return $employees;
	}

	public static function getsalaryDetails($request,$flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalaryplus')
					->select('id','Name','nick_name','location','Salarayid')
					->where('location','=',$flg)
					->where('delflg','=',0)
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function getsalaryDetailsnoloc($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalaryplus')
					->select('id','Name','nick_name','location','Salarayid')
					->where('delflg','=',0)
					->orderBy('location', 'ASC')
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function getsalaryDetailsnodelflg($request,$flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalaryplus')
					->select('id','Name','nick_name','location','Salarayid')
					->where('location','=',$flg)
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function fnGetDataExistsCheck($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->select('*')
					->where('Emp_ID','=',$request->Emp_ID)
					->where('year','=',$request->selYear)
					->where('month','=',$request->month)
					->get();
		return $query;
	}

	public static function salarycalcview($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->SELECT('*')
					->WHERE('id', '=', $request->id)
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
	 	return $query;
	}

	public static function salarycalcplusview_tot($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->SELECT('*')
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
	 	return $query;
	}

	public static function salaryDetailhistory($request, $flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					 ->select('*')
					 ->where('Emp_ID','=', $request->Emp_ID)
					 ->where('date','LIKE', $request->selYear.'%')
					 ->orderBy('year','DESC')
					 ->orderBy('month','DESC');

					if ($flg == 0) {
						$query = $query->paginate($request->plimit);
			        } else {
			        	$query = $query->get();
			        }
		return $query;
	}

	public static function getbasichraDetails($empid,$yearmonth) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_basic_salary as basic')
					->select('basic.*',DB::raw('(basic.basic_amount + basic.increment_amount) as tot_basicAmount'))
					->where('basic.activeFlg','=', 0)
					->where('basic.Emp_ID','=', $empid)
					->WHERERAW("SUBSTRING(basic.year_month_from,1,7) <='$yearmonth'")
					->WHERERAW("SUBSTRING(basic.year_month_to,1,7) >='$yearmonth'")
    				->get();
		return $query;
	}

	public static function salaryDetail_Empid($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					 ->select('*')
					 ->where('Emp_ID','=', $request->Emp_ID)
					 ->orderBy('year','DESC')
					 ->orderBy('month','DESC')
    				 ->get();
		return $query;
	}

	// vengad 06/07/2020
    public static function fnGetCompanyDetails($request){
      
      $db = DB::connection('mysql_Salary');
      $query = $db->table('company_details')
          ->select('id',
                    'companyNumber',
                    'companyName',
                    'companyBranch',
                    'capital',
                    'address',
                    'TEL',
                    'CEO',
                    'referencenumber')
          ->where('id', '=', 1)
          ->get();
      return $query;
    }

    public static function fnGetEmpDetail($request){
    	$db = DB::connection('mysql_MB');
    	$query = $db->table('emp_mstemployees'.' AS emp')
                  ->SELECT('emp.*')
                  ->LEFTJOIN('mstaddress AS ma', 'ma.id' ,'=','emp.Address1')
                  ->where('Emp_ID', '=', $request->empid)
                  ->get();
        return $query;
    }

    public static function fnGetAddressMB($address) {
		$db = DB::connection('mysql_MB');
		$query= $db->table('mstaddress')
						->SELECT('*')
						->WHERE('id', '=', $address)
						->limit(1)
						->get();
		return $query;

	}

	public static function inv_salary_main_transferedamt($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->SELECT('Transferred')
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
	 	return $query;
	}

	public static function getYears($request) {

		$db = DB::connection('mysql_Salary');
		$years = $db->table('inv_salaryplus_main')
							->select(DB::raw('YEAR(date) as years'))
							->where('Emp_ID','=', $request->Emp_ID)
							->groupBy(DB::raw('YEAR(date)'))
							->get();
	 	return $years;
	}

	// Start Madasamy 31/07/2020
	public static function getYearsTotalHistory($request) {
		$db = DB::connection('mysql_Salary');
		$years = $db->table('inv_salaryplus_main_emp')
					->select(DB::raw('year as years'))
					->groupBy("year")
					->get();
	 	return $years;
	}

	public static function fnGetEmpId($request){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main_emp')
					->select('Emp_ID')
					->where('year','LIKE', $request->selYear.'%')
					->groupby("Emp_ID")
					->orderBy("Emp_ID")
					->get();
		return $query;
	}

	public static function fnGetEmpName($Emp_ID){
		$db = DB::connection('mysql_MB');
		$query = $db->table('emp_mstemployees')
					->select('FirstName','LastName','KanaFirstName','KanaLastName','resign_id','resigndate')
					->where('Emp_ID','=',$Emp_ID)
					->get();
		return $query;
	}

	public static function fnGetmailFlg($Emp_ID){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					->select('mailFlg')
					->where('Emp_ID','=', $Emp_ID)
					->orderBy('id','DESC')
					->limit(1)
					->get();

		if (isset($query[0]->mailFlg)) {
			return $query[0]->mailFlg;
		} else {
			return 0;
		}
	}

	public static function fnGetEmpSalHistory($Emp_ID,$year,$month = ''){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main')
					 ->select('*')
					 ->where('Emp_ID','=', $Emp_ID);
			if ($month !== "") {
				$query = $query ->where('year','=', $year)
								->where('month','=', $month);
			} else {
				$query = $query ->where('date','LIKE', $year.'%');
			}			 
			
			$query = $query ->orderBy('date')
					 		->get();
		return $query;
	}

	public static function fnGetEmpIdList($year,$month){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salaryplus_main_emp')
					->select('Emp_ID')
					->where('year','=', $year)
					->where('month','=', $month)
					->orderBy("Emp_ID")
					->get();
		return $query;
	}
	
	// End Madasamy 31/07/2020
}