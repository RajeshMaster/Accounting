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
						->lists('BANKNAME','id');
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

	public static function fninsert($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$insert=$db->table('acc_cashRegister')->insert([
				'emp_ID' => "",
				'date' => $request->date,
				'transcationType' => $request->transtype,
				'bankIdFrom' => $request->bank,
				'bankIdTo' => $request->transfer,
				'amount' => preg_replace("/,/", "",$request->amount),
				'fee' => $request->fee,
				'content' => $request->content,
				'remarks' => $request->remarks,
				'createdBy' => $name,
				'UpdatedBy' => $name,
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

	public static function inserttransferdetails($request,$filename) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount_1);
		$charge = str_replace(",", "", $request->charge_1);
		$bank = explode("-", $request->bank);

		$db = DB::connection('mysql');
		$insert = DB::table('acc_cashregister')
			->insert([
					'emp_ID' => $request->empid, 
					'date' => $request->date,
					'transcationType' => 3,
					'bankIdFrom' => $bank[0],
					'bankIdTo' => '',
					'amount' => $amount,
					'fee' => $charge,
					'content' => $request->content,
					'subjectId' => $request->mainsubject,
					'remarks' => $request->Remarks,
					'fileDtl' => $filename,
					'createdBy' => $name,
					'updatedBy' => $name,
					'delFlg' => 0
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
		$query = $db->table('acc_cashRegister')
						->SELECT('acc_cashRegister.*','mstbank.AccNo','mstbank.AccNo','mstbank.FirstName','mstbank.LastName','mstbank.BankName','mstbank.Bank_NickName')
						->leftJoin('mstbank', 'mstbank.id', '=', 'acc_cashRegister.bankIdFrom')
						->where('transcationType','!=',9)
						->orderBy('bankIdFrom','ASC')
						->orderBy('id','DESC')
						->get();
						// ->toSql();
		return $query;

	}
}
