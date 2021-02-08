<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class ExpensesData extends Model {


	public static function fetchExpensesData($request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_expensesData')
						->SELECT('acc_expensesData.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp','banname.BankName AS banknm','brncname.id AS brnchid','brncname.BranchName AS brnchnm')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_expensesData.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_expensesData.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bank.BankName')
						->leftJoin('mstbankbranch AS brncname', function($join)
							{
								$join->on('brncname.BankId', '=', 'bank.BankName');
								$join->on('brncname.id', '=', 'bank.BranchName');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_expensesData.subjectId')
						->orderBy('acc_expensesData.bankIdFrom','ASC')
						->orderBy('acc_expensesData.accountNumberFrom','ASC')
						->orderBy('bank.Bank_NickName','ASC')
						->orderBy('acc_expensesData.orderId','ASC')
						->paginate($request->plimit);
						// ->toSql();
						// dd($query);
		return $query;
	}

	public static function baseAmt($bankId ,$acc) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType','date')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$acc)
						->where('transcationType','=',9)
						->get();
		return $query;
	}

	public static function fnGetEmpName($Emp_ID){

		$db = DB::connection('mysql_MB');
		$query = $db->table('emp_mstemployees')
					->select('FirstName','LastName','KanaFirstName','KanaLastName')
					->where('Emp_ID','=',$Emp_ID)
					->get();
		return $query;
	}

	public static function fnGetRecordPreviousForAmountCheck($from_date) {
		$tbl_name = "acc_cashregister";
		
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM $tbl_name 
			WHERE (date < '$from_date') ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function AccBalance($bankId,$accNo,$startDate) {
		$curDate = date('Y-m-d');
		$db = DB::connection('mysql');
			$query = $db->table('acc_cashregister')
						->SELECT('transcationType','amount','fee')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$accNo)
						->where('transcationType','!=',9)
						->where('delFlg','=','0');
		
			$query = $query->WHERERAW("date >= '$startDate'");
			$query = $query->WHERERAW("date <= '$curDate'");
			
			$query = $query->get();

		return $query;
	}

	public static function fetchbanknames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->where('mstbank.delflg','=','0')
						->orderBy('mstbank.Bank_NickName','ASC')
						->lists('BANKNAME','ID');
						// ->toSql();
		return $query;
	}

	public static function getMainExpName() {

		$db = DB::connection('mysql');
		$query = $db->TABLE('dev_expensesetting')
						->SELECT('id','Subject','Subject_jp')
						->orderBy('id','ASC')
						->lists('Subject','id');
		return $query;
		
	}


	public static function fnGetbankName($accNo) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->WHERE('AccNo', '!=', $accNo)
						->where('mstbank.delflg','=','0')
						->orderBy('mstbank.id','ASC')
						->get();
						// ->toSql();
		return $query;
	}

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'acc_expensesData'");
		if (isset($statement[0]->Auto_increment)) {
			$statement = $statement[0]->Auto_increment;
		} else {
			$statement = 1;
		}
		return $statement;

	}
	

	public static function subjectName($subName,$flg) {

		$db = DB::connection('mysql');
		$query = $db->table('dev_expensesetting')
						->SELECT('id','Subject','Subject_jp');
		if ($flg == 1) {
			$query = $query->where('Subject','=',$subName)
							->orderBy('id','ASC')
							->lists('Subject','id');
		} else {
			$query = $query->where('Subject_jp','=',$subName)
							->orderBy('id','ASC')
							->lists('Subject_jp','id');
		}
						
		return $query;
	}

	public static function insExpensesData($request,$fileName) {

		$db = DB::connection('mysql');

		if($request->empid == "") {
			$request->empid = $request->empID;
		}
		if($request->txt_empname == "") {
			$request->empid = "";
		}

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$statement = DB::select("show table status like 'acc_expensesData'");
		$bankacc = explode('-', $request->bankIdAccNo);

		if (isset($statement[0]->Auto_increment)) {
			$orderId = $statement[0]->Auto_increment;
		} else {
			$orderId = 1;
		}


		$insert = $db->table('acc_expensesData')
					->insert([
							'empId' => $request->empid, 
							'bankIdFrom' => $bankacc['0'],
							'accountNumberFrom' => $bankacc['1'],
							'amount' => preg_replace("/,/", "", $request->expensesDataAmount),
							'fee' => preg_replace("/,/", "", $request->expensesDataFee),
							'content' => $request->expensesDataContent,
							'subjectId' => $request->subjectId,
							'remarks' => $request->expensesDataRemarks,
							'fileDtl' => $fileName,
							'orderId' => $orderId,
							'createdBy' => $name,
						]);

		return $insert;
	}

	public static function expensesDataEdit($request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_expensesData')
						->SELECT('acc_expensesData.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp',DB::RAW("CONCAT(emp_mstemployees.FirstName,' ', emp_mstemployees.LastName) AS Empname"))
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_expensesData.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_expensesData.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_expensesData.subjectId')
						->leftJoin('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'acc_expensesData.empId')
						->where('acc_expensesData.id','=',$request->editId)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_expensesData.orderId','ASC')
						->get();
						// ->toSql();
						// dd($query);
		return $query;

	}

	public static function updExpensesData($request,$fileName) {

		if($request->empid == "") {
			$request->empid = $request->empID;
		}

		if($request->txt_empname == "") {
			$request->empid = "";
		}

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->bankIdAccNo);

		$update = DB::table('acc_expensesData')
						->where('id', $request->editId)
						->update(
							array(
								'empId'	=> $request->empid, 
								'subjectId' => $request->subjectId,
								'bankIdFrom' => $bankacc['0'],
								'accountNumberFrom' => $bankacc['1'],
								'amount' => preg_replace("/,/", "", $request->expensesDataAmount),
								'fee' => preg_replace("/,/", "", $request->expensesDataFee),
								'content' => $request->expensesDataContent,
								'remarks' => $request->expensesDataRemarks,
								'fileDtl' => $fileName,
								'UpdatedBy' => $name
							)
						);
		return $update;

	}

	public static function fnGetEmpDetails($request) {

		$query = DB::table('emp_mstemployees')
						->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
						->WHERE('delFlg', '=', 0)
						->WHERE('Emp_ID', 'NOT LIKE', '%NST%')
						->orderBy('Emp_ID', 'ASC')
						->get();
		return $query;
	}

	public static function fnGetNonstaffEmpDetails($request) {

		$query = DB::table('emp_mstemployees')
						->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
						->WHERE('delFlg', '=', 0)
						->WHERE('resign_id', '=', 0)
						->WHERE('Emp_ID', 'LIKE', '%NST%')
						->orderBy('Emp_ID', 'ASC')
						->get();
		return $query;
	}
	

	public static function subjectMaster($subId) {

		$db = DB::connection('mysql');
		$query = $db->table('dev_expensesetting')
						->SELECT('Subject','Subject_jp')
						->where('id','=',$subId)
						->get();
		return $query;
	}

	public static function fetchExpDataPopup($request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_expensesData')
						->SELECT('acc_expensesData.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp',DB::RAW("CONCAT(emp_mstemployees.FirstName,' ', emp_mstemployees.LastName) AS Empname"))
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_expensesData.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_expensesData.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_expensesData.subjectId')
						->leftJoin('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'acc_expensesData.empId')
						->where('bankIdFrom','=',$request->bankId)
						->where('accountNumberFrom','=',$request->AccNo)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_expensesData.orderId','ASC')
						->get();
						 // ->toSql();
						// dd($query);
		return $query;
	}

	/**  
    *  For Commit Process 
    *  @author Rajesh 
    *  @param $request,$getTableFields
    *  Created At 2020/09/21
    **/
    public static function fngetcommitProcess($request) {

        $db = DB::connection('mysql');
        $tablename = 'acc_expensesData';
        $cmtfield = 'orderId';
        $splitactualid = explode(",", $request->actualId);
        $splitidnew = explode(",", $request->idnew);
        for ($i = 0; $i < count($splitactualid); $i++) {
            $update = $db->table($tablename)
                        ->where('id', $splitidnew[$i])
                        ->update([ $cmtfield => $splitactualid[$i] ]);
        }
        return true;
    }

    public static function changeDelFlg($request){

		if ($request->delFlg == 0) {
			$delFlg = 1;
		} else { 
			$delFlg = 0;
		}
		
		$db = DB::connection('mysql');
		$query = $db->table('acc_expensesData')
					->where('id','=',$request->editId);

		$query = $query->update(['delFlg' => $delFlg]);
		// $query = $query->toSql();dd($query);

		return $query;
	}


}
