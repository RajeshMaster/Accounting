<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;
use Illuminate\Database\Query\Builder;
class SalaryCalc extends Model{
	public static function fnGetdetailsfromemp() {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main_emp')
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
		$query = $db->table('inv_salary_main_emp')
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
		$query = $db->table('inv_salary_main')
					->SELECT('year_mon AS date','year','month')
					->WHERE('year_mon','>=',$from_date,' AND','year_mon','<',$to_date)
					->WHERE('delFlg','=',0)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function fnGetmnthRecordPrevious($from_date) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->SELECT(DB::raw("SUBSTRING(year_mon, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('year_mon','<=',$from_date)
					->ORDERBY('year_mon', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function fnGetmnthRecordNext($to_date) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->SELECT(DB::raw("SUBSTRING(year_mon, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('year_mon','>=',$to_date)
					->ORDERBY('year_mon', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	// Updated By Madasamy - 07/08/2020
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
		$selectedEmployees = $db->table('inv_salary_main_emp')
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
							->SELECT('Emp_ID',
									 'FirstName',
									 'LastName')
							->WHERE('delFLg', '=', 0)
							->WHERE('resign_id', '=', 0)
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
		$query = $db->table('mstsalary')
					->select('id','Name','nick_name','location','Salarayid')
					->where('location','=',$flg)
					->where('delflg','=',0)
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function getsalaryDetailsnoloc($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalary')
					->select('id','Name','nick_name','location','Salarayid')
					->where('delflg','=',0)
					->orderBy('location', 'ASC')
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function getsalaryDetailsnodelflg($request,$flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalary')
					->select('id','Name','nick_name','location','Salarayid')
					->where('location','=',$flg)
					->get();
		return $query;
	}

	public static function fnGetDataExistsCheck($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->select('*')
					->where('Emp_ID','=',$request->Emp_ID)
					->where('year','=',$request->selYear)
					->where('month','=',$request->month)
					->get();
		return $query;
	}

	public static function multiadd($request,$salary_det,$salary_ded) {
		$insert = '';
		$name = Session::get('FirstName').' '.Session::get('LastName');
		for ($i=0; $i < $request->count; $i++) {
			$Emp_ID = 'Emp_ID'.$i;
			$transferred = 'transferred_'.$request->$Emp_ID;
			$transferred_new = str_replace(",", "", $request->$transferred);
			$salary_final = '';
			foreach ($salary_det as $key => $value) {
				$detail = 'salary_'.$request->$Emp_ID.'_'.$value->Salarayid;
				$salaryDet = $request->$detail;
				if ($salaryDet != '') {
					$salary_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDet).'##';
				}
			}
			$deduction_final = '';
			foreach ($salary_ded as $key => $value) {
				$detail1 = 'Deduction_'.$request->$Emp_ID.'_'.$value->Salarayid;
				$salaryDed = $request->$detail1;
				if ($salaryDed != '') {
					$deduction_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDed).'##';
				}
			}
			if ($salary_final !="" || $deduction_final != "") {
				$insert=DB::table('inv_salary_main')
					->insert(
						['id' => '',
						'Emp_ID' => $request->$Emp_ID,
						'date' => $request->txt_startdate,
						'Salary' => !empty($salary_final) ? $salary_final : '',
						'Deduction' => !empty($deduction_final) ? $deduction_final : '',
						'Transferred' => !empty($transferred_new) ? $transferred_new : 0,
						'year' => $request->selYear,
						'month' => $request->month,
						'delFlg' => 0,
						'CreatedBy' => $name,
						'UpdatedBy' => $name,
						'CreatedDateTime' => date('Y-m-d H:i:s'),
						'UpdatedDateTime' => date('Y-m-d H:i:s')]);
			}
		}
		return $insert;
	}

	public static function fnsalarycalcadd($request,$salary_det,$salary_ded) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$salary_final = '';
		foreach ($salary_det as $key => $value) {
			$detail = 'salary_'.$value->Salarayid;
			$salaryDet = $request->$detail;
			if ($salaryDet != '') {
				$salary_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDet).'##';
			}
		}
		$deduction_final = '';
		foreach ($salary_ded as $key => $value) {
			$detail1 = 'deduction_'.$value->Salarayid;
			$salaryDed = $request->$detail1;
			if ($salaryDed != '') {
				$deduction_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDed).'##';
			}
		}
		$transferred = str_replace(",", "", $request->transferred);
		$Basic = str_replace(",", "", $request->Basic);
		$hra = str_replace(",", "", $request->hra);
		$train_daily = str_replace(",", "", $request->train_daily);
		$others = str_replace(",", "", $request->others);
		if ($salary_final !="" || $deduction_final != "" || $Basic != "" || $hra != "" || $train_daily != "" || $others != "") {
			$insert=DB::table('inv_salary_main')
				->insert(
					['id' => '',
					'Emp_ID' => $request->Emp_ID,
					'date' => $request->date,
					'Salary' => !empty($salary_final) ? $salary_final : '',
					'Deduction' => !empty($deduction_final) ? $deduction_final : '',
					'Transferred' => !empty($transferred) ? $transferred : 0,
					'Basic' => !empty($Basic) ? $Basic : '',
					'hra' => !empty($hra) ? $hra : '',
					'train_daily' => !empty($train_daily) ? $train_daily : '',
					'others' => !empty($others) ? $others : '',
					'year' => $request->selYear,
					'month' => $request->month,
					'year_mon' => $request->selYear.'-'.$request->month.'-10',
					'leave_cnt' => $request->leave_cnt,
					'remarks' => $request->remarks,
					'delFlg' => 0,
					'CreatedDateTime' => date('Y-m-d H:i:s'),
					'UpdatedDateTime' => date('Y-m-d H:i:s'),
					'CreatedBy' => $name,
					'UpdatedBy' => $name]
			);
		}
		return $insert;
	}

	public static function fnsalarycalcupd($request,$salary_det,$salary_ded) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$salary_final = '';
		foreach ($salary_det as $key => $value) {
			$detail = 'salary_'.$value->Salarayid;
			$salaryDet = $request->$detail;
			if ($salaryDet != '') {
				$salary_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDet).'##';
			}
		}
		$deduction_final = '';
		foreach ($salary_ded as $key => $value) {
			$detail1 = 'deduction_'.$value->Salarayid;
			$salaryDed = $request->$detail1;
			if ($salaryDed != '') {
				$deduction_final .= $value->Salarayid.'$'.str_replace(",", "", $salaryDed).'##';
			}
		}
		$transferred = str_replace(",", "", $request->transferred);
		$Basic = str_replace(",", "", $request->Basic);
		$hra = str_replace(",", "", $request->hra);
		$train_daily = str_replace(",", "", $request->train_daily);
		$others = str_replace(",", "", $request->others);
		$update=DB::table('inv_salary_main')
			->where('id',$request->id)
			->update(
				['date' => $request->date,
				'Salary' => !empty($salary_final) ? $salary_final : '',
				'Deduction' => !empty($deduction_final) ? $deduction_final : '',
				'Basic' => !empty($Basic) ? $Basic : '',
				'hra' => !empty($hra) ? $hra : '',
				'train_daily' => !empty($train_daily) ? $train_daily : '',
				'others' => !empty($others) ? $others : '',
				'Transferred' => !empty($transferred) ? $transferred : 0,
				'remarks' => $request->remarks,
				'year' => $request->selYear,
				'month' => $request->month,
				'year_mon' => $request->selYear.'-'.$request->month.'-10',
				'leave_cnt' => $request->leave_cnt,
				'mailFlg' => '0',
				'mailDatetime' => null,
				'UpdatedDateTime' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}

	public static function fngetid() {
		$Details = DB::TABLE('inv_salary_main')
						->max('id');
		return $Details;
	}

	public static function getsalaryDetailsCount($request,$flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalary')
					->select('id','Name','location')
					->where('location','=',$flg)
					->max('Salarayid');
		return $query;
	}

	public static function getsalaryempDetails($request,$empid) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->select('*')
					->WHERE('Emp_ID', '=', $empid)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
		return $query;
	}

	public static function getSystemDateTime() {
		$query = DB::select("SELECT CURRENT_DATE, CURRENT_TIME, CURRENT_TIMESTAMP, NOW() + 1 as NOW");
		return $query;
	}
	public static function updateMailFlg($request,$empid) {
		$gettime = self::getSystemDateTime();
		$update=DB::table('inv_salary_main')
            ->where('Emp_ID', $empid)
            ->where('month', $request->selMonth)
            ->where('year', $request->selYear)
            ->update(['mailFlg' => 1,
            			'mailDatetime' => $gettime[0]->CURRENT_TIMESTAMP]);
    	return $update;
	}

	public static function salaryDetailcheck($request,$empid) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->SELECT('Emp_ID')
					->WHERE('Emp_ID','=',$empid)
					->WHERE('year','=',$request->selYear)
					->WHERE('month','=',$request->selMonth)
	 	 			->GET();
	 	return $query;
	}

	public static function salarycalcview($request) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					->SELECT('*')
					->WHERE('id', '=', $request->id)
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
	 	return $query;
	}

	public static function fnGetEmployeeDetails($request,$empid){
	  	$db = DB::connection('mysql_Salary');
	  	$query = $db->table('inv_salary_main')
					 ->select('Emp_ID','Transferred')
					 ->where('year', $request->selYear)
							->where('month', $request->selMonth)
							->whereIn('Emp_ID', $empid)
							->orderBy('Emp_ID', 'ASC')
					  		->get();
							
			return $query;
	}

	// Updated By Madasamy - 07/08/2020
	public static function salaryDetailhistory($request, $flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
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

	// For history process By Madasamy - 07/08/2020
	public static function salaryDetailhistory_Addedit($request,$flg) {
		$db = DB::connection('mysql_Salary');
		$query = $db->table('inv_salary_main')
					 ->select('*')
					 ->where('Emp_ID','=', $request->Emp_ID)
					 ->orderBy('year','DESC')
					 ->orderBy('month','DESC');
									
    				if ($flg == 0) {
						$query = $query->paginate(8);
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

	public static function getYears($request) {
		$db = DB::connection('mysql_Salary');
		$years = $db->table('inv_salary_main')
							->select(DB::raw('YEAR(date) as years'))
							->where('Emp_ID','=', $request->Emp_ID)
							->groupBy(DB::raw('YEAR(date)'))
							->get();
	 	return $years;
	}


	// Start Madasamy 07/08/2020
	public static function salaryDetail($request,$lastyear,$lastmonth,$flg,$empid = null){
		$db = DB::connection('mysql_Salary');
		$query = $db->TABLE($db->raw("(SELECT invsal.id
										    , invsal.date
										    , invsal.year
										    , invsal.month
										    , invsal.Salary
										    , invsal.Deduction
										    , invsal.Transferred
										    , invsal.mailFlg
										    , invsal.Basic
										    , invsal.hra
										    , invsal.train_daily
										    , invsal.others
										    , salemp.Emp_ID
										    , basic.mail_need
										FROM inv_salary_main_emp AS salemp 
										LEFT JOIN inv_salary_main AS invsal 
											ON invsal.Emp_ID = salemp.Emp_ID 
											AND invsal.year = ".$lastyear." 
											AND invsal.month= ".$lastmonth."
										LEFT JOIN inv_basic_salary AS basic 
										    ON basic.Emp_ID = salemp.Emp_ID 
										    AND basic.activeFlg = 0 
										WHERE salemp.year = ".$lastyear." 
											AND salemp.month = ".$lastmonth.") as tbl"));
						if ($empid != '') {
							$query = $query->WHERE('Emp_ID','=',$empid);
						}
						if ($flg == 0) {
							$query = $query ->orderBy('Emp_ID','ASC')
				        					->paginate($request->plimit);
				        } else {
				        	$query = $query->orderBy('Emp_ID','ASC')
				        					->get();
				        }
		
	 	return $query;
	}

	public static function fnGetEmpName($Emp_ID){
		$db = DB::connection('mysql_MB');
		$query = $db->table('emp_mstemployees')
					->select('FirstName','LastName','KanaFirstName','KanaLastName','Emailpersonal')
					->where('Emp_ID','=',$Emp_ID)
					->get();
		return $query;
	}
	// End Madasamy 07/08/2020


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
	
}