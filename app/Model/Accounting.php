<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Accounting extends Model {

	public static function fetchbanknames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->orderBy('mstbank.id','ASC')
						->lists('BANKNAME','ID');
						// ->toSql();
		return $query;
	}

	public static function fnGetbankName($accNo) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->WHERE('AccNo', '!=', $accNo)
						->orderBy('mstbank.id','ASC')
						->get();
						// ->toSql();
		return $query;
	}

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$statement = $statement[0]->Auto_increment;
		} else {
			$statement = 1;
		}
		return $statement;

	}

	public static function insCashDtls($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->bank);
		$db = DB::connection('mysql');

		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => "",
							'date' => $request->date,
							'transcationType' => $request->transtype,
							'bankIdFrom' => $bankacc[0],
							'accountNumberFrom' => $bankacc[1],
							'bankIdTo' => $request->transfer,
							'amount' => preg_replace("/,/", "", $request->amount),
							'fee' => preg_replace("/,/", "", $request->fee),
							'content' => $request->content,
							'remarks' => $request->remarks,
							'createdBy' => $name,
						]);
		return $insert;
	}


	public static function insCashreduction($request ,$type) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		if($type == 1){
			$bankacc = explode('-', $request->bank);
			$transfer = explode('-', $request->transfer);
		} else {
			$bankacc = explode('-', $request->transfer);
			$transfer[0] = "";
			$transfer[1] = "";
			$request->fee = "";
		}

		$db = DB::connection('mysql');

		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => "",
							'date' => $request->date,
							'transcationType' => $type,
							'bankIdFrom' => $bankacc[0],
							'accountNumberFrom' => $bankacc[1],
							'bankIdTo' => $transfer[0],
							'accountNumberTo' => $transfer[1],
							'amount' => preg_replace("/,/", "", $request->amount),
							'fee' => preg_replace("/,/", "", $request->fee),
							'content' => $request->content,
							'remarks' => $request->remarks,
							'createdBy' => $name,
						]);
		return $insert;
	}

	public static function getMainExpName() {

		$db = DB::connection('mysql');
		$query = $db->TABLE('dev_expensesetting')
						->SELECT('id','Subject','Subject_jp')
						->orderBy('id','ASC')
						->lists('Subject','id');
		return $query;
		
	}

	public static function insTransferDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->transferBank);

		$db = DB::connection('mysql');

		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => $request->empid, 
							'date' => $request->transferDate,
							'transcationType' => 1,
							'bankIdFrom' => $bankacc['0'],
							'accountNumberFrom' => $bankacc['1'],
							'amount' => preg_replace("/,/", "", $request->transferAmount),
							'fee' => preg_replace("/,/", "", $request->transferFee),
							'content' => $request->transferContent,
							'subjectId' => $request->transferMainExp,
							'remarks' => $request->transFerRemarks,
							'fileDtl' => $fileName,
							'createdBy' => $name,
						]);

		return $insert;
	}

	public static function insAutoDebitDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->autoDebitBank);

		$db = DB::connection('mysql');

		$insert = $db->table('acc_cashregister')
					->insert([
							'date' => $request->autoDebitDate,
							'transcationType' => 1,
							'bankIdFrom' => $bankacc['0'],
							'accountNumberFrom' => $bankacc['1'],
							'amount' => preg_replace("/,/", "", $request->autoDebitAmount),
							'fee' => preg_replace("/,/", "", $request->autoDebitFee),
							'content' => $request->autoDebitContent,
							'subjectId' => $request->autoDebitMainExp,
							'remarks' => $request->autoDebitRemarks,
							'fileDtl' => $fileName,
							'createdBy' => $name,
						]);

		return $insert;
	}

	public static function fnGetEmpDetails($request) {

		$query = DB::table('emp_mstemployees')
						->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
						->WHERE('delFlg', '=', 0)
						->WHERE('resign_id', '=', 0)
						->WHERE('Title', '=', 2)
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
	
	public static function fetchcashRegister() {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.AccNo','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('transcationType','!=',9)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.date','ASC')
						->get();
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
						//->toSql();
		return $query;
	}

	public static function subjectMaster($subId) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_expensesetting')
						->SELECT('Subject','Subject_jp')
						->where('id','=',$subId)
						->get();
						//->toSql();
		return $query;
	}
}
