<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Session;
use Illuminate\Database\Query\Builder;
class SalaryPlus extends Model{
	public static function fnGetdetailsfromemp() {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salaryplus_emp')
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
			$insert=DB::table('inv_salaryplus_emp')
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
		$query=$db->table('inv_salaryplus_emp')
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
		$query=$db->table('inv_salaryplus_emp')
					->SELECT('Emp_Id')
					->whereRaw("year = (SELECT year FROM inv_salaryplus_emp ORDER BY id DESC LIMIT 1) 
						AND month = (SELECT month FROM inv_salaryplus_emp ORDER BY id DESC LIMIT 1)")
					->get();
		foreach ($query as $key => $value) {
			$empid = $value->Emp_Id;
			$insert=DB::table('inv_salaryplus_emp')
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
		$query = $db->table('inv_salaryplus')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'year','month')
					->WHERE('date','>',$from_date,' AND','date','<',$to_date)
					->WHERE('delFlg','=',0)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}
	public static function fnGetmnthRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salaryplus')
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
		$query = $db->table('inv_salaryplus')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"))
					->WHERE('delFlg','=',0)
					->WHERE('date','>=',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}
	public static function salaryDetailcheck($request,$empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salaryplus')
					->SELECT('Emp_ID')
					->WHERE('Emp_ID','=',$empid)
					->WHERE('year','=',$request->selYear)
					->WHERE('month','=',$request->selMonth)
	 	 			->GET();
	 	return $query;
	}

	public static function salaryDetail($request,$lastyear,$lastmonth,$flg) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT invsal.id,invsal.date,invsal.year,invsal.month,invsal.Basic,invsal.HrAllowance,invsal.OT,invsal.Leave,invsal.Bonus,invsal.ESI,invsal.IT,invsal.Travel,invsal.MonthlyTravel,employ.Emp_ID,employ.FirstName,employ.LastName
			 			FROM inv_salaryplus_emp AS salemp
						LEFT JOIN emp_mstemployees employ ON salemp.Emp_Id=employ.Emp_ID 
						AND salemp.month='$lastmonth' AND salemp.year ='$lastyear'
						LEFT JOIN inv_salaryplus AS invsal ON invsal.Emp_ID=employ.Emp_ID 
						AND invsal.year ='$lastyear' AND invsal.month='$lastmonth'
						WHERE IF( (SELECT COUNT(*) FROM inv_salaryplus AS afterRes 
						WHERE afterRes.Emp_ID = employ.Emp_ID AND afterRes.month='$lastmonth' 
						AND afterRes.year='$lastyear' 
						AND employ.resign_id=1)>0 ,employ.resign_id=1,employ.resign_id=0)) as tbl1"));
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

	public static function fnGetBankDetails($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(COALESCE(mstbanks.BankName,''),'-',COALESCE(mstbank.AccNo,'')) AS BANKNAME"),'mstbank.*','mstbanks.BankName')
						->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->WHERE('mstbanks.delflg', 0)
						->WHERE('mstbank.mainFlg', 1)
						->GROUPBY('mstbanks.id')
						->orderBy('mstbank.id')
						->lists('BANKNAME','mstbanks.id');
		return $query;
	}

	public static function salaryExistcheck($request, $empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary')
					->SELECT('empNo')
					->WHERE('empNo','=',$empid)
					->WHERE('year','=',$request->selYear)
					->WHERE('salaryMonth','=',$request->selMonth)
	 	 			->COUNT();
	 	return $query;
	}
	public static function getsalaryDetail($request,$lastyear,$lastmonth) {
			$db = DB::connection('mysql');
			$query = $db->TABLE($db->raw("(SELECT invsal.*,employ.Emp_ID,employ.FirstName,employ.LastName,
				 			mstbanks.BankName,mstbank.AccNo AS AccNo
				 			FROM inv_temp_salaryemp AS salemp
							LEFT JOIN emp_mstemployees employ ON salemp.Emp_Id=employ.Emp_ID 
							AND salemp.month='$lastmonth' AND salemp.year ='$lastyear'
							LEFT JOIN inv_salary AS invsal ON invsal.empNo=employ.Emp_ID 
							AND invsal.year ='$lastyear' AND invsal.month='$lastmonth'
							LEFT JOIN mstbank ON mstbank.id=invsal.bankId
							LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
							WHERE IF( (SELECT COUNT(*) FROM inv_salary AS afterRes 
							WHERE afterRes.empNo = employ.Emp_ID AND afterRes.month='$lastmonth' 
							AND afterRes.year='$lastyear' 
							AND employ.resign_id=1)>0 ,employ.resign_id=1,employ.resign_id=0)) as tbl1"))
						->orderBy(DB::raw('CASE WHEN id IS NULL THEN 1 ELSE 0 END'), 'ASC')
						->orderBy('id','ASC')
						->get();
					// ->toSql();
					// dd($query);
			return $query;
		}
	public static function getsalary($request,$empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary')
					->SELECT(DB::raw("SUM(REPLACE(inv_salary.Salary,',','')) As Salary"),'empNo')
					->WHERE('empNo','=',$empid)
					->WHERE('year','=',$request->selYear)
					->WHERE('salaryMonth','=',$request->selMonth)
	 	 			->GET();
	 	 			//->toSql();
					//dd($query);
	 	return $query;
	}
	public static function insertsalary($request,$date) {
		//print_r($request->all());exit();
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = explode("-", $date);
		$salarymonth = $request->salarymonth + 1;
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		if($request->bank == "999") {
			$charge = "";
		} else {
			$charge = "";
		}
		$insert=DB::table('inv_salary')
			->insert(
				['id' => '',
				'empNo' => $request->id,
				'salaryDate' => $request->txt_startdate,
				'salaryMonth' => $salarymonth,
				'salary' => $request->txt_salary,
				'charge' => $charge,
				'bankId' => $request->bank,
				'accountNo' => '',
				'year' => $splitdate[0],
				'month' => $splitdate[1],
				'submit_flg' => $sumitedInsert,
				'edit_flg' => $sumitedInsert,
				'delFlg' => 0,
				'InsDT' => date('Y-m-d H:i:s'),
				'CreatedBy' => $name,
				'UpDT' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function checkSubmited($spldm) {
		$submitcount = 0;
		$db = DB::connection('mysql');
	    $sqlSelect = $db->table('dev_expenses')
					->SELECT('*')
					->where('year','=',$spldm[0])
					->where('month','=',$spldm[1])
					->where('submit_flg','=',1)
					->get();
		$submitcount = count($sqlSelect);
		return $submitcount;
	}
	public static function salaryDetailtot($request,$lastyear,$lastmonth) {
		DB::setFetchMode(\PDO::FETCH_ASSOC);
		$query = DB::TABLE('inv_salaryplus AS invsal')
					->SELECT(DB::raw("FORMAT(SUM(invsal.Basic), 0) AS BasicTotal"),
						DB::raw("FORMAT(SUM(invsal.HrAllowance), 0) AS HrAllowanceTotal"),
						DB::raw("FORMAT(SUM(invsal.OT), 0) AS OTTotal"),
						DB::raw("FORMAT(SUM(invsal.Leave), 0) AS LeaveTotal"),
						DB::raw("FORMAT(SUM(invsal.Bonus), 0) AS BonusTotal"),
						DB::raw("FORMAT(SUM(invsal.ESI), 0) AS ESITotal"),
						DB::raw("FORMAT(SUM(invsal.IT), 0) AS ITTotal"),
						DB::raw("FORMAT(SUM(invsal.Travel), 0) AS TravelTotal"),
						DB::raw("FORMAT(SUM(invsal.MonthlyTravel), 0) AS MonthlyTravelTotal"))
					->WHERE('year','=',$lastyear) 
					->WHERE('month','=',$lastmonth) 
					->WHERE('delFlg',0) 
					->get();
		return $query[0];
	}
	public static function salaryDetailtotall($request,$lastyear,$lastmonth) {
		DB::setFetchMode(\PDO::FETCH_ASSOC);
		$query = DB::TABLE('inv_salaryplus AS invsal')
					->SELECT(DB::raw("FORMAT(SUM(Basic+OT+Bonus+Travel+MonthlyTravel+HrAllowance)-SUM(`Leave`+ESI+IT),0) AS Total"))
					->WHERE('year','=',$lastyear) 
					->WHERE('month','=',$lastmonth) 
					->WHERE('delFlg',0) 
					->get();
		return $query[0];
	}
	public static function salaryDetailseperatetot($request,$id) {
		$query = DB::TABLE('inv_salaryplus')
					->SELECT(DB::raw("FORMAT(SUM(Basic+OT+Bonus+Travel+MonthlyTravel+HrAllowance)-SUM(`Leave`+ESI+IT),0) AS Total"))
					->WHERE('id','=',$id) 
					->WHERE('delFlg',0) 
					->get();
					//->toSql();dd($query);
		return $query[0];
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
								->WHERERAW("IF( (SELECT COUNT(*) FROM inv_salaryplus AS afterRes 
								WHERE afterRes.Emp_ID = emp_mstemployees.Emp_ID AND afterRes.month='$month' 
								AND afterRes.year='$year' 
								AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)");
				$unselectedEmployees = $unselectedEmployees->whereNotIn('Emp_ID', function($query) use($year, $month) {
								$query->SELECT('Emp_ID')
								->FROM('inv_salaryplus_emp')
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
		$selectedEmployees = $db->TABLE('inv_salaryplus_emp')
								->SELECT('emp_mstemployees.Emp_ID',
								'emp_mstemployees.FirstName',
								'emp_mstemployees.LastName')
								->LEFTJOIN('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'inv_salaryplus_emp.Emp_ID')
								->WHERE('emp_mstemployees.delFLg', '=', 0)
								->WHERE('inv_salaryplus_emp.month', '=', $month)
								->WHERE('inv_salaryplus_emp.year', '=', $year);
					$selectedEmployees = $selectedEmployees->WHERERAW("IF( (SELECT COUNT(*) FROM inv_salaryplus AS afterRes 
					WHERE afterRes.Emp_ID = emp_mstemployees.Emp_ID AND afterRes.month='$month' 
					AND afterRes.year='$year' 
					AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)")
					->orderBy('emp_mstemployees.Emp_ID', 'ASC')
					->get();
		return $selectedEmployees;
	}
	public static function InsertEmpFlrDetails($request) {
		$db = DB::connection('mysql');
		$deldetails = $db->TABLE('inv_salaryplus_emp')->WHERE('year', '=', $request->year)
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
		DB::TABLE('inv_salaryplus_emp')->INSERT($rows);
		return true;
	}
	public static function salaryplusDetedit($request) {
		DB::setFetchMode(\PDO::FETCH_ASSOC);
		$Details = DB::TABLE('inv_salaryplus')
						->SELECT(
								DB::raw('FORMAT(inv_salaryplus.Basic, 0) AS Basic'),
								DB::raw('FORMAT(inv_salaryplus.HrAllowance, 0) AS HrAllowance'),
								DB::raw('FORMAT(inv_salaryplus.OT, 0) AS OT'),
								DB::raw('FORMAT(inv_salaryplus.Leave, 0) AS leaveAmount'),
								DB::raw('FORMAT(inv_salaryplus.Bonus, 0) AS Bonus'),
								DB::raw('FORMAT(inv_salaryplus.ESI, 0) AS ESI'),
								DB::raw('FORMAT(inv_salaryplus.IT, 0) AS IT'),
								DB::raw('FORMAT(inv_salaryplus.Travel, 0) AS Travel'),
								DB::raw('FORMAT(inv_salaryplus.MonthlyTravel, 0) AS MonthlyTravel'),
								'inv_salaryplus.year AS year',
								'inv_salaryplus.month AS month',
								'inv_salaryplus.date AS date',
								'inv_salaryplus.id AS id',
								'inv_salaryplus.Emp_ID AS Emp_ID')
						->WHERE('id', '=', $request->id)
						->WHERE('Emp_ID', '=', $request->Emp_ID)
						->WHERE('year', '=', $request->selYear)
						->WHERE('month', '=', $request->selMonth)
						->get();
		return $Details;
	}
	public static function salaryplusview($request) {
		DB::setFetchMode(\PDO::FETCH_ASSOC);
		$Details = DB::TABLE('inv_salaryplus')
						->SELECT(
								DB::raw('FORMAT(inv_salaryplus.Basic, 0) AS Basic'),
								DB::raw('FORMAT(inv_salaryplus.HrAllowance, 0) AS HrAllowance'),
								DB::raw('FORMAT(inv_salaryplus.OT, 0) AS OT'),
								DB::raw('FORMAT(inv_salaryplus.Leave, 0) AS leaveAmount'),
								DB::raw('FORMAT(inv_salaryplus.Bonus, 0) AS Bonus'),
								DB::raw('FORMAT(inv_salaryplus.ESI, 0) AS ESI'),
								DB::raw('FORMAT(inv_salaryplus.IT, 0) AS IT'),
								DB::raw('FORMAT(inv_salaryplus.Travel, 0) AS Travel'),
								DB::raw('FORMAT(inv_salaryplus.MonthlyTravel, 0) AS MonthlyTravel'),
								'inv_salaryplus.year AS year',
								'inv_salaryplus.month AS month',
								'inv_salaryplus.date AS date',
								'inv_salaryplus.id AS id',
								'inv_salaryplus.Emp_ID AS Emp_ID')
						->WHERE('id', '=', $request->id)
						->WHERE('Emp_ID', '=', $request->Emp_ID)
						->WHERE('year', '=', $request->selYear)
						->WHERE('month', '=', $request->selMonth)
						->get();
		return $Details;
	}
	public static function fngetid() {
		$Details = DB::TABLE('inv_salaryplus')
						->max('id');
		return $Details;
	}
	public static function fnsalaryplusadd($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$date = explode("-", $request->date);
		$Basic = str_replace(",", "", $request->Basic);
		$HrAllowance = str_replace(",", "", $request->HrAllowance);
		$OT = str_replace(",", "", $request->OT);
		$Leave = str_replace(",", "", $request->Leave);
		$Leave1 = str_replace("-", "", $Leave);
		$Bonus = str_replace(",", "", $request->Bonus);
		$ESI = str_replace(",", "", $request->ESI);
		$ESI1 = str_replace("-", "", $ESI);
		$IT = str_replace(",", "", $request->IT);
		$IT1 = str_replace("-", "", $IT);
		$Travel = str_replace(",", "", $request->Travel);
		$MonthlyTravel = str_replace(",", "", $request->MonthlyTravel);
		$insert=DB::table('inv_salaryplus')
			->insert(
				['id' => '',
				'Emp_ID' => $request->Emp_ID,
				'date' => $request->date,
				'Basic' => $Basic,
				'HrAllowance' => $HrAllowance,
				'OT' => $OT,
				'Leave' => $Leave1,
				'Bonus' => $Bonus,
				'ESI' => $ESI1,
				'IT' => $IT1,
				'Travel' => $Travel,
				'MonthlyTravel' => $MonthlyTravel,
				'year' => $date[0],
				'month' => $date[1],
				'delFlg' => 0,
				'CreatedDateTime' => date('Y-m-d H:i:s'),
				'UpdatedDateTime' => date('Y-m-d H:i:s'),
				'CreatedBy' => $name,
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function fnsalaryplusupd($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$date = explode("-", $request->date);
		$Basic = str_replace(",", "", $request->Basic);
		$HrAllowance = str_replace(",", "", $request->HrAllowance);
		$OT = str_replace(",", "", $request->OT);
		$Leave = str_replace(",", "", $request->Leave);
		$Leave1 = str_replace("-", "", $Leave);
		$Bonus = str_replace(",", "", $request->Bonus);
		$ESI = str_replace(",", "", $request->ESI);
		$ESI1 = str_replace("-", "", $ESI);
		$IT = str_replace(",", "", $request->IT);
		$IT1 = str_replace("-", "", $IT);
		$Travel = str_replace(",", "", $request->Travel);
		$MonthlyTravel = str_replace(",", "", $request->MonthlyTravel);
		$update=DB::table('inv_salaryplus')
			->where('id',$request->id)
			->update(
				['date' => $request->date,
				'Basic' => $Basic,
				'HrAllowance' => $HrAllowance,
				'OT' => $OT,
				'Leave' => $Leave1,
				'Bonus' => $Bonus,
				'ESI' => $ESI1,
				'IT' => $IT1,
				'Travel' => $Travel,
				'year' => $date[0],
				'month' => $date[1],
				'MonthlyTravel' => $MonthlyTravel,
				'UpdatedDateTime' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function fnGetdatecheck($request) {
		$date = explode("-", $request->date);
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_salaryplus')
					->select('*')
					->WHERE('year', '=', $date[0])
					->WHERE('month', '=', $date[1])
					->WHERE('Emp_ID', '=', $request->Emp_ID)
					->get();
		return $result;
	}
	public static function multiadd($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$date = explode("-", $request->date_hdn);
		for ($i=0; $i < $request->count; $i++) {
			$Emp_ID = 'Emp_ID'.$i;
			$basic = 'basic'.$i;
			$hra = 'hra'.$i;
			$ot = 'ot'.$i;
			$esi = 'esi'.$i;
			$it = 'it'.$i;
			$travel = 'travel'.$i;
			$mtravel = 'mtravel'.$i;
			$bonus = 'bonus'.$i;
			$leave = 'leave'.$i;
			$request->date_hdn; 
			$date[0]; 
			$date[1]; 
			$request->$Emp_ID; 
			$Basic1 = str_replace(",", "", $request->$basic); 
			$HrAllowance1 = str_replace(",", "", $request->$hra); 
			$OT1 = str_replace(",", "", $request->$ot);

			$Leave = str_replace(",", "", $request->$leave);
			$Leave1 = str_replace("-", "", $Leave);

			$Bonus1 = str_replace(",", "", $request->$bonus); 

			$ESI = str_replace(",", "", $request->$esi);
			$ESI1 = str_replace("-", "", $ESI);

			$IT = str_replace(",", "", $request->$it);
			$IT1 = str_replace("-", "", $IT);
			
			$Travel1 = str_replace(",", "", $request->$travel); 
			$MonthlyTravel1 = str_replace(",", "", $request->$mtravel); 
			if ($Basic1!="") {
				$insert=DB::table('inv_salaryplus')
					->insert(
						['id' => '',
						'Emp_ID' => $request->$Emp_ID,
						'date' => $request->date_hdn,
						'Basic' => !empty($Basic1) ? $Basic1 : 0,
						'HrAllowance' => !empty($HrAllowance1) ? $HrAllowance1 : 0,
						'OT' => !empty($OT1) ? $OT1 : 0,
						'ESI' => !empty($ESI1) ? $ESI1 : 0,
						'IT' => !empty($IT1) ? $IT1 : 0,
						'Travel' => !empty($Travel1) ? $Travel1 : 0,
						'Leave' => !empty($Leave1) ? $Leave1 : 0,
						'Bonus' => !empty($Bonus1) ? $Bonus1 : 0,
						'MonthlyTravel' => !empty($MonthlyTravel1) ? $MonthlyTravel1 : 0,
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
}