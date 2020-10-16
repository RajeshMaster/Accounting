<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;
use Illuminate\Database\Query\Builder;
class SalaryCalc extends Model{
	public static function fnGetdetailsfromemp() {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main_emp')
					->where('delflg','=',0)
					->count();
		return $query;
	}

	public static function fninsertdetailsfromemp($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
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
		$getempdetails = DB::table('emp_mstemployees')
							->select('Emp_ID')
							->where('delFlg','=',0)
							->where('resign_id','=',0)
							->groupby('Emp_ID')
							->get();
		foreach ($getempdetails as $key => $value) {
			$empid = $value->Emp_ID;
			$insert=DB::table('inv_salary_main_emp')
					->insert(
						array(
							'id'	=>	'', 
							'Emp_Id'	=>	$empid, 
							'delflg'	=>	'0', 
							'year'	=>	$year,
							'month'=>	$month,
							'create_date'	=>	date('Y-m-d'),
							'create_by'	=>	$name,
							'update_date'	=>	date('Y-m-d'),
							'update_by'	=>	$name,
						)
					);
		}
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
		$db = DB::connection('mysql');
		$query=$db->table('inv_salary_main_emp')
					->SELECT('*')
					->where('month','=',$month)
					->where('year','=',$year)
					->where('delFLg','=',0)
					->get();
		$querycount = count($query);
		return $querycount;
	}

	public static function getEmpDetailsId($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$empid = array();
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
		$db = DB::connection('mysql');
		$query=$db->table('inv_salary_main_emp')
					->SELECT('Emp_Id')
					->whereRaw("year = (SELECT year FROM inv_salary_main_emp ORDER BY id DESC LIMIT 1) 
						AND month = (SELECT month FROM inv_salary_main_emp ORDER BY id DESC LIMIT 1)")
					->get();
		foreach ($query as $key => $value) {
			$empid = $value->Emp_Id;
			$insert=DB::table('inv_salary_main_emp')
					->insert(
						array(
							'id'	=>	'', 
							'Emp_Id'	=>	$empid, 
							'delflg'	=>	'0', 
							'year'	=>	$year,
							'month'=>	$month,
							'create_date'	=>	date('Y-m-d'),
							'create_by'	=>	$name,
							'update_date'	=>	date('Y-m-d'),
							'update_by'	=>	$name,
						)
					);
		}
	}

	public static function fnGetAccountPeriod() {
		$db = DB::connection('mysql');
		$query = $db->table('dev_kessandetails')
					->where('delflg','=',0)
					->get();
		return $query;	
	}

	public static function fnGetmnthRecord($from_date, $to_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'year','month')
					->WHERE('date','>',$from_date,' AND','date','<',$to_date)
					->WHERE('delFlg','=',0)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function fnGetmnthRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('date','<=',$from_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	 			// ->toSql();dd($query);
	 	return $query;
	}

	public static function fnGetmnthRecordNext($to_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('date','>=',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}

	public static function salaryDetail($request,$lastyear,$lastmonth,$flg,$empid = null) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT invsal.id,invsal.date,invsal.year,invsal.month,invsal.Salary,invsal.Deduction,invsal.Transferred,invsal.mailFlg,employ.Emp_ID,employ.FirstName,employ.LastName
			 			FROM inv_salary_main_emp AS salemp
						LEFT JOIN emp_mstemployees employ ON salemp.Emp_Id=employ.Emp_ID 
						AND salemp.month='$lastmonth' AND salemp.year ='$lastyear'
						LEFT JOIN inv_salary_main AS invsal ON invsal.Emp_ID=employ.Emp_ID 
						AND invsal.year ='$lastyear' AND invsal.month='$lastmonth'
						WHERE IF( (SELECT COUNT(*) FROM inv_salary_main AS afterRes 
						WHERE afterRes.Emp_ID = employ.Emp_ID AND afterRes.month='$lastmonth' 
						AND afterRes.year='$lastyear' 
						AND employ.resign_id=1)>0 ,employ.resign_id=1,employ.resign_id=0)) as tbl1"));
					if ($empid != '') {
						$query = $query->WHERE('Emp_ID','=',$empid);
					}
					if ($flg == 0) {
						$query = $query->orderBy('Emp_ID','ASC')
									// ->toSql();dd($query);
			        				->paginate($request->plimit);
			        } else {
			        	$query = $query->orderBy('Emp_ID','ASC')
			        					->get();
			        }
					// ->paginate($request->plimit);
		return $query;
	}

	public static function getAllEmpDetails($request) {
		if(($request->year != "")&&($request->month != "")) {
			$year = $request->year;
			$month = $request->month ;
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}
		$db = DB::connection('mysql');
		$unselectedEmployees = $db->TABLE('emp_mstemployees')
								->SELECT('Emp_ID',
								'FirstName',
								'LastName')
								->WHERE('delFLg', '=', 0)
								->WHERERAW("IF( (SELECT COUNT(*) FROM inv_salary_main AS afterRes 
								WHERE afterRes.Emp_ID = emp_mstemployees.Emp_ID AND afterRes.month='$month' 
								AND afterRes.year='$year' 
								AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)");
				$unselectedEmployees = $unselectedEmployees->whereNotIn('Emp_ID', function($query) use($year, $month) {
								$query->SELECT('Emp_ID')
								->FROM('inv_salary_main_emp')
								->WHERE('year', '=', $year)
								->WHERE('month', '=', $month);
								})->orderBy('Emp_ID', 'ASC')
								->get();
		return $unselectedEmployees;
	}

	public static function getAllFilteredEmpDetails($request) {
		if(($request->year!="") && ($request->month!="")) {
			$year = $request->year;
			$month = $request->month;
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}
		$db = DB::connection('mysql');
		$selectedEmployees = $db->TABLE('inv_salary_main_emp')
								->SELECT('emp_mstemployees.Emp_ID',
								'emp_mstemployees.FirstName',
								'emp_mstemployees.LastName')
								->LEFTJOIN('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'inv_salary_main_emp.Emp_ID')
								->WHERE('emp_mstemployees.delFLg', '=', 0)
								->WHERE('inv_salary_main_emp.month', '=', $month)
								->WHERE('inv_salary_main_emp.year', '=', $year);
					$selectedEmployees = $selectedEmployees->WHERERAW("IF( (SELECT COUNT(*) FROM inv_salary_main AS afterRes 
					WHERE afterRes.Emp_ID = emp_mstemployees.Emp_ID AND afterRes.month='$month' 
					AND afterRes.year='$year' 
					AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)")
					->orderBy('emp_mstemployees.Emp_ID', 'ASC')
					->get();
		return $selectedEmployees;
	}

	public static function InsertEmpFlrDetails($request) {
		$db = DB::connection('mysql');
		$deldetails = $db->TABLE('inv_salary_main_emp')->WHERE('year', '=', $request->year)
						->WHERE('month', '=', $request->month)->DELETE();
		$rows = array();
		for ($i=0;$i<count($request->selected);$i++) {
			$rows[] = array('id' => '',
			'Emp_Id' => $request->selected[$i],
			'delflg' => 0,
			'year' => $request->year,
			'month' => $request->month,
			'create_date' => date('Y-m-d H:i:s'),
			'create_by' => Auth::user()->username,
			'update_date' => date('Y-m-d H:i:s'),
			'update_by' => Auth::user()->username);
		}
		DB::TABLE('inv_salary_main_emp')->INSERT($rows);
		return true;
	}

	public static function getsalaryDetails($request,$flg) {
		$db = DB::connection('mysql');
		$query = $db->table('mstsalary')
					->select('id','Name','location','Salarayid')
					->where('location','=',$flg)
					->where('delflg','=',0)
					->get();
		return $query;
	}

	public static function multiadd($request,$salary_det,$salary_ded,$lastday) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$date = explode("-", $lastday);
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
						'date' => $lastday,
						'Salary' => !empty($salary_final) ? $salary_final : 0,
						'Deduction' => !empty($deduction_final) ? $deduction_final : 0,
						'Transferred' => !empty($transferred_new) ? $transferred_new : 0,
						'year' => $date[0],
						'month' => $date[1],
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
		$date = explode("-", $request->date);
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
		if ($salary_final !="" || $deduction_final != "") {
			$insert=DB::table('inv_salary_main')
				->insert(
					['id' => '',
					'Emp_ID' => $request->Emp_ID,
					'date' => $request->date,
					'Salary' => !empty($salary_final) ? $salary_final : 0,
					'Deduction' => !empty($deduction_final) ? $deduction_final : 0,
					'Transferred' => !empty($transferred) ? $transferred : 0,
					'year' => $date[0],
					'month' => $date[1],
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
		$date = explode("-", $request->date);
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
		$update=DB::table('inv_salary_main')
			->where('id',$request->id)
			->update(
				['date' => $request->date,
				'Salary' => !empty($salary_final) ? $salary_final : 0,
				'Deduction' => !empty($deduction_final) ? $deduction_final : 0,
				'Transferred' => !empty($transferred) ? $transferred : 0,
				'year' => $date[0],
				'month' => $date[1],
				'mailFlg' => '0',
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
		$db = DB::connection('mysql');
		$query = $db->table('mstsalary')
					->select('id','Name','location')
					->where('location','=',$flg)
					->max('Salarayid');
		return $query;
	}

	public static function getsalaryempDetails($request,$empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main AS inv')
					->select('inv.*','emp.Emailpersonal','emp.FirstName','emp.LastName')
					->LEFTJOIN('emp_mstemployees AS emp', 'emp.Emp_ID', '=', 'inv.Emp_ID')
					->WHERE('emp.Emp_ID', '=', $empid)
					->WHERE('inv.Emp_ID', '=', $empid)
					->WHERE('inv.date', 'LIKE', $request->selYear.'-'.$request->selMonth.'%')
					->get();
					 // ->toSql();dd($query);
		return $query;
	}
	public static function updateMailFlg($request,$empid) {
		$update=DB::table('inv_salary_main')
            ->where('Emp_ID', $empid)
            ->where('month', $request->selMonth)
            ->where('year', $request->selYear)
            ->update(['mailFlg' => 1]);
    	return $update;
	}

	public static function salaryDetailcheck($request,$empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main')
					->SELECT('Emp_ID')
					->WHERE('Emp_ID','=',$empid)
					->WHERE('year','=',$request->selYear)
					->WHERE('month','=',$request->selMonth)
	 	 			->GET();
	 	return $query;
	}

	public static function salarycalcview($request) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary_main')
					->SELECT('*')
					->WHERE('id', '=', $request->id)
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->WHERE('year', '=', $request->selYear)
					->WHERE('month', '=', $request->selMonth)
					->get();
	 	return $query;
	}
}