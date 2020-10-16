<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Salary extends Model {
	public static function getTempDetailscheck($request) {
		$db = DB::connection('mysql');
		$query=$db->table('inv_temp_salaryemp')
					->SELECT('*')
					->get();
		$query1 = count($query);
		return $query1;
	}
	public static function getEmpDetailsinitialId($request) {
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
		$query=$db->table('emp_mstemployees')
					->SELECT('Emp_Id')
					->where('resign_id','=',0)
					->get();
		foreach ($query as $key => $value) {
			$empid = $value->Emp_Id;
			$insert=DB::table('inv_temp_salaryemp')
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
		$query=$db->table('inv_temp_salaryemp')
					->SELECT('*')
					->where('month','=',$month)
					->where('year','=',$year)
					->where('delFLg','=',0)
					->get();
		$query1 = count($query);
		return $query1;
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
		$query=$db->table('inv_temp_salaryemp')
					->SELECT('Emp_Id')
					->whereRaw("year = (SELECT year FROM inv_temp_salaryemp ORDER BY id DESC LIMIT 1) 
						AND month = (SELECT month FROM inv_temp_salaryemp ORDER BY id DESC LIMIT 1)")
					->get();
		foreach ($query as $key => $value) {
			$empid = $value->Emp_Id;
			$insert=DB::table('inv_temp_salaryemp')
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
	public static function fnGetAccountPeriodSal($request) {
		$db = DB::connection('mysql');
		$query=$db->table('dev_kessandetails')
					->SELECT('*')
					->where('delflg','=',0)
					->get();
		return $query;
	}
	public static function fnGetBKRecord($from_date, $to_date) {
		$db = DB::connection('mysql');
		$sql = "SELECT SUBSTRING(salaryDate, 1, 7) AS salaryDate FROM inv_salary 
						WHERE (salaryDate > '$from_date' AND salaryDate < '$to_date') ORDER BY salaryDate ASC";
		$query = DB::select($sql);
		return $query;
	}
	public static function fnGetbkrsRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$sql = "SELECT SUBSTRING(salaryDate, 1, 7) AS salaryDate FROM inv_salary WHERE (salaryDate <= '$from_date') ORDER BY salaryDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetbkrsRecordNext($to_date) {
		$db = DB::connection('mysql');
		$query=$db->table('inv_salary')
					->SELECT(DB::raw('SUBSTRING(salaryDate, 1, 7) AS salaryDate'))
					->where('salaryDate','>',$to_date)
					->orderBy('salaryDate','ASC')
					->get();
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
					->orderBy($request->salarysort, $request->sortOrder)
					->paginate($request->plimit);
				// ->toSql();
				// dd($query);
		return $query;
	}
	public static function detbankinsalaryDetail($lastyear,$lastmonth) {
		$db = DB::connection('mysql');
		$query=$db->table('inv_salary')
					->SELECT('*')
					->where('year','=',$lastyear)
					->where('month','=',$lastmonth)
					->orderBy('salaryDate','ASC')
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function detbankcalculationsalaryDetail($lastyear,$lastmonth,$bank) {
		$db = DB::connection('mysql');
		$query=$db->table('inv_salary')
					->SELECT(DB::raw("SUM(REPLACE(salary,',','')) AS sal"),DB::raw("SUM(REPLACE(charge,',','')) AS charg"))
					->where('bankId','=',$bank)
					->where('year','=',$lastyear)
					->where('month','=',$lastmonth)
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function detbank($lastyear,$lastmonth) {
		$db = DB::connection('mysql');
		$query=$db->table('dev_expenses')
					->SELECT('*')
					->where('salaryFlg','=',1)
					->where('year','=',$lastyear)
					->where('month','=',$lastmonth)
					->get();
					// ->toSql();
					// dd($query);
		return $query;
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
	public static function salarytoexpReg($date,$salary,$charge,$lastyear,$lastmonth,$account,$bankname) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$s = str_replace(",", "", $salary);
		$splitdate = split("-", $date);
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$sumitedInsert = ",1,1";
		} else {
			$sumitedInsert = ",0,0";
		}
		$db = DB::connection('mysql');
		$insert=DB::table('dev_expenses')
			->insert(
				['id' => '',
				'billno' => '',
				'date' => $date,
				'subject' => '',
				'details' => '',
				'currency_type' => 0,
				'amount' => $salary,
				'file_dtl' => '',
				'remark_dtl' => '',
				'user_id' => Session::get('Emp_ID'),
				'year' => $lastyear,
				'month' => $lastmonth,
				'copy_month_day' => '',
				'salaryFlg' => '',
				'submit_flg' => 1,
				'edit_flg' => $sumitedInsert,
				'transaction_flg' => '',
				'del_flg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Up_DT' => date('Y-m-d'),
				'Ins_TM' => date('H:i:s'),
				'Up_TM' => date('H:i:s'),
				'CreatedBy' => $name,
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function salarytoexpUpd($date,$salary,$charge,$lastyear,$lastmonth,$account,$bankname) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = split("-", $date);
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		$db = DB::connection('mysql');
		$update=DB::table('dev_expenses')
			->where('salaryFlg', 1)
			->where('year', $lastyear)
			->where('month', $lastmonth)
			->update(
				['amount' => $salary,
				'edit_flg' => $editflg,
				'Up_DT' => date('Y-m-d'),
				'Up_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function detfetch($bankval) {
		$db = DB::connection('mysql');
		$query=$db->table('mstbank')
					->SELECT('AccNo','BankName')
					->where('id','=',$bankval)
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function detfetchbanktransfer($AccNo,$BankName,$lastyear,$lastmonth) {
		$db = DB::connection('mysql');
		$query=$db->table('dev_banktransfer')
					->SELECT('*')
					->where('bankaccno','=',$AccNo)
					->where('bankname','=',$BankName)
					->where('year','=',$lastyear)
					->where('month','=',$lastmonth)
					->where('salaryFlg','=',1)
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function bankdetinsert($date,$salary,$charge,$lastyear,$lastmonth,$account,$bankname) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$s = str_replace(",", "", $salary);
		$a = preg_replace("/,/", "", $salary);
		$b = preg_replace("/,/", "", $charge);
		$splitdate = split("-", $date);
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$sumitedInsert = ",1,1";
		} else {
			$sumitedInsert = ",0,0";
		}
		$db = DB::connection('mysql');
		$insert=DB::table('dev_banktransfer')
			->insert(
				['id' => '',
				'billno' => '',
				'bankdate' => $date,
				'bankname' => $bankname,
				'bankaccno' => $account,
				'amount' => $a,
				'fee' => $b,
				'salaryFlg' => 1,
				'year' => $lastyear,
				'month' => $lastmonth,
				'submit_flg' => '',
				'edit_flg' => $sumitedInsert,
				'del_flg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Up_DT' => date('Y-m-d'),
				'Ins_TM' => date('H:i:s'),
				'Up_TM' => date('H:i:s'),
				'CreatedBy' => $name,
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function bankdetupdate($date,$salary,$charge,$lastyear,$lastmonth,$account,$bankname) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = split("-", $date);
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		$db = DB::connection('mysql');
		$update=DB::table('dev_banktransfer')
			->where('salaryFlg', 1)
			->where('bankaccno', $account)
			->where('bankname', $bankname)
			->where('year', $lastyear)
			->where('month', $lastmonth)
			->update(
				['amount' => $salary,
				'fee' => $charge,
				'edit_flg' => $editflg,
				'Up_DT' => date('Y-m-d'),
				'Up_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function getAllEmpDetails($request) {
		if(isset($request->datemonth)) {
			$splityearmonth = explode("-",$request->datemonth);
			$year=$splityearmonth[0];
			$month=$splityearmonth[1];
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}
		$db = DB::connection('mysql');
		$query=$db->TABLE($db->raw("(SELECT FirstName,LastName,Emp_ID FROM emp_mstemployees WHERE delFLg=0 AND 
					IF( (SELECT COUNT(*) FROM inv_salary AS afterRes 
					WHERE afterRes.empNo = emp_mstemployees.Emp_ID AND afterRes.month=$month
					AND afterRes.year=$year
					AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)
					AND Emp_ID NOT IN 
					(SELECT Emp_ID FROM inv_temp_salaryemp WHERE month=$month and year=$year) ORDER BY Emp_ID ASC) as tb1"))
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function getAllFilteredEmpDetails($request) {
		if(isset($request->datemonth)) {
			$splityearmonth = explode("-",$request->datemonth);
			$year=$splityearmonth[0];
			$month=$splityearmonth[1];
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}
		$db = DB::connection('mysql');
		$query=$db->TABLE($db->raw("(SELECT emp_mstemployees.FirstName,emp_mstemployees.LastName,emp_mstemployees.Emp_ID FROM inv_temp_salaryemp as salemp
				LEFT JOIN emp_mstemployees ON emp_mstemployees.Emp_ID = salemp.Emp_Id 
				WHERE salemp.month='$month' AND salemp.year='$year' AND
				IF( (SELECT COUNT(*) FROM inv_salary as afterRes 
				WHERE afterRes.empNo = emp_mstemployees.Emp_ID AND afterRes.month='$month' 
				AND afterRes.year='$year' 
				AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)
				AND  emp_mstemployees.delFLg=0 ORDER BY salemp.Emp_Id ASC) as tbl2"))
				->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function InsertEmpFlrDetails($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if(isset($request->datemonth)) {
			$splityearmonth = explode("-",$request->datemonth);
			$year=$splityearmonth[0];
			$month=$splityearmonth[1];
		} else {
			$previous = date('Y-m', strtotime('first day of last month'));
			$splitPrevious = explode("-", $previous);
			$year=$splitPrevious[0];
			$month=$splitPrevious[1];
		}
		DB::table('inv_temp_salaryemp')->where('year', '=', $year)
                          ->where('month', '=', $month)->delete();
    	$rows = array();
	    for ($i=0;$i<count($request->selected);$i++) {
	        $rows[] = array('id' => '',
								'Emp_Id' => $request->selected[$i],
								'delflg' => 0,
								'year' => $year,
								'month' => $month,
								'create_date' => date('Y-m-d H:i:s'),
								'create_by' => $name,
								'update_date' => date('Y-m-d H:i:s'),
								'update_by' => $name);
	    }
	    DB::table('inv_temp_salaryemp')->insert($rows);
	    return true;
	}
	public static function chkempid($empid) {
		$chkempid = DB::TABLE('inv_temp_salaryemp')
						->SELECT('Emp_Id')
						->WHERE('Emp_ID', '=', $empid)
						->count();
		return $chkempid; 
	}
	public static function fetchsingleview($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('inv_salary AS inv')
						->SELECT('inv.*','mst.AccNo','bank.BankName')
						->leftJoin('mstbank AS mst', 'mst.id', '=', 'inv.bankId')
						->leftJoin('mstbanks AS bank', 'mst.BankName', '=', 'bank.id')
						->WHERE('inv.id', '=', $request->ids)
						->get();
						// ->toSql();
						// dd($query);
		return $query; 
	}
	public static function fetchviewlist($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('inv_salary')
						->SELECT('inv_salary.*','mstbanks.bankname','mstbank.AccNo')
						->leftJoin('mstbank', 'inv_salary.bankId', '=', 'mstbank.id')
						->leftJoin('mstbanks', 'mstbank.BankName', '=', 'mstbanks.id')
						->WHERE('empNo', '=', $request->id)
						// ->orderBy('inv_salary.salaryDate','DESC')
						->orderBy($request->salaryviewsort, $request->sortOrder)
						->paginate($request->plimit);
						// ->toSql();
						// dd($query);
		return $query; 
	}
	public static function fetchbanknames($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(COALESCE(mstbanks.BankName,''),'-',COALESCE(mstbank.AccNo,'')) AS BANKNAME"),'mstbank.*','mstbanks.BankName')
						->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->orderBy('mstbank.id')
						->lists('BANKNAME','mstbanks.id');
						// ->toSql();
						// dd($query);
		return $query; 
	}
	public static function fetchdetails($request) {
		$db = DB::connection('mysql');
		$sql = "SELECT salary,charge,year,month,concat(year,'-',month) as yearmo,bankId 
	    			  FROM inv_salary WHERE 
	    			  empNo ='".$request->id."' AND (delFlg=0 OR delFlg IS NULL)
	    			  ORDER BY yearmo DESC LIMIT 1";
		$query = DB::select($sql);
		return $query; 
	}
	public static function fetcheditdetails($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_salary')
						->SELECT('salaryMonth AS salaryMonth',
								'salaryDate AS txt_startdate',
								'salary AS salary',
								'charge AS charge',
								'bankId AS bankId',
								'accountNo AS accountNo',
								'year AS year',
								'month AS month')
						->where('id','=',$request->ids)
						->get();
		return $query;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'inv_salary'");
		return $statement[0]->Auto_increment;
	}
	public static function insertsalaryRec($request,$date) {
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
			$charge = $request->charge;
		}
		$db = DB::connection('mysql');
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
	public static function updatesalaryRec($request,$date) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = explode("-", $date);
		$salarymonth = $request->salarymonth + 1;
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		if($request->bank == "999") {
			$charge = "";
		} else {
			$charge = $request->charge;
		}
		$db = DB::connection('mysql');
		$update=DB::table('inv_salary')
			->where('id', $request->ids)
			->update(
				['empNo' => $request->id,
				'salaryDate' => $request->txt_startdate,
				'salaryMonth' => $salarymonth,
				'salary' => $request->txt_salary,
				'charge' => $charge,
				'bankId' => $request->bank,
				'accountNo' => '',
				'year' => $splitdate[0],
				'month' => $splitdate[1],
				'edit_flg' => $editflg,
				'delFlg' => 0,
				'UpDT' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function fetchmultidetails($request) {
		$db = DB::connection('mysql');
		$query= "SELECT employ.* FROM inv_temp_salaryemp AS salemp
						LEFT JOIN emp_mstemployees employ ON salemp.Emp_Id=employ.Emp_ID 
						AND salemp.month=$request->selMonth AND salemp.year =$request->selYear
						WHERE employ.Emp_ID NOT IN (SELECT empNo FROM inv_salary WHERE year =$request->selYear AND month=$request->selMonth) 
						AND employ.resign_id=0 ORDER BY employ.Emp_ID";
		$query = DB::select($query);
		return $query;
	}
	public static function getSalaryCheckprevious($empid) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('inv_salary')
						->SELECT(DB::RAW("CONCAT(year,'-',month) AS year"),'salary','charge','year','month')
						->where('empNo', $empid)
						->where('delFlg', 0)
						->get();
						// ->toSql();
						// dd($query);
		return $query;
	}
	public static function getSalarypreCheck($request, $val) {
		if ($val != 1) {
			$empid = $request->empNo;
		} else {
			$empid = $request->empid;
		}
		$db = DB::connection('mysql');
		$query = "SELECT salary,charge,year,month,concat(year,'-',month) as yearmo,bankId 
	    			  FROM inv_salary WHERE 
	    			  empNo ='".$empid."' AND (delFlg=0 OR delFlg IS NULL)
	    			  ORDER BY yearmo DESC LIMIT 1";

		$query = $db->select($query);
		$submitcount = count($query);
		if ($val ==1) {
			$var = strval(intval($query[0]->month));
			$res = $query[0]->salary."-".$query[0]->charge."-".$query[0]->bankId;
			return $res;
		}  else {
			return $query;
		}
	}
	public static function salarymultireg($request,$day,$date) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = explode("-", $date);
		$valueget = self:: checkSubmited($splitdate);
		$splitdatemonth = explode("-", $request->txt_startdate);
		if ($valueget > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$empid = 'empNo_'.$request->day;
		$salary = 'salary'.$request->day;
		$charge = 'charge'.$request->day;
		if ($request->$charge=="") {
			$request->$charge= "";
		}
		$db = DB::connection('mysql');
		$insert=DB::table('inv_salary')
					->insert(
						array(
							'empNo'	=>	$request->$empid, 
							'salaryDate'	=>	$request->txt_startdate, 
							'salaryMonth'	=>	$request->salarymonth,
							'salary'=>	$request->$salary,
							'charge'	=>	$request->$charge,
							'bankId'	=>	$request->bank,
							'accountNo'	=>	'',
							'year'	=>	$splitdatemonth[0],
							'month'	=>	$splitdatemonth[1],
							'submit_flg'	=>	$sumitedInsert,
							'edit_flg'	=>	$sumitedInsert,
							'InsDT' => date('Y-m-d H:i:s'),
							'CreatedBy' => $name,
							'UpDT' => date('Y-m-d H:i:s'),
							'UpdatedBy' => $name
						)
					);
		return $insert;
	}
	public static function salarymultiupd($request,$day,$date) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = explode("-", $date);
		$splitdatemonth = explode("-", $request->txt_startdate);
		$valueget = self:: checkSubmited($splitdate);
		if ($valueget > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$empid = 'empNo_'.$request->day;
		$salary = 'salary'.$request->day;
		$charge = 'charge'.$request->day;
		if ($request->$charge=="") {
			$request->$charge= "";
		}
		$db = DB::connection('mysql');
		$update=DB::table('inv_salary')
					->update(
						array(
							'empNo'	=>	$request->$empid, 
							'salaryDate'	=>	$request->txt_startdate, 
							'salaryMonth'	=>	$request->salarymonth,
							'salary'=>	$request->$salary,
							'charge'	=>	$request->$charge,
							'bankId'	=>	$request->bank,
							'accountNo'	=>	'',
							'year'	=>	$splitdatemonth[0],
							'month'	=>	$splitdatemonth[1],
							'submit_flg'	=>	$sumitedInsert,
							'edit_flg'	=>	$sumitedInsert,
							'UpDT' => date('Y-m-d H:i:s'),
							'UpdatedBy' => $name
						)
					);
		return $update;
	}
}