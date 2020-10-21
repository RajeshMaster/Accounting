<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class AccBankDetail extends Model {

	public static function bankindex($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbank AS bnk')
						->SELECT('banDet.id','banname.id AS bnkid','brncname.id AS brnchid','bnk.id AS bankid','bnk.AccNo','banDet.startDate','banDet.balance','banDet.processFlg','banDet.bankId AS balbankid','banname.BankName AS banknm','brncname.BranchName AS brnchnm','bnk.Bank_NickName AS NickName')
						->leftJoin('inv_allbank_details AS banDet', 'bnk.id', '=', 'banDet.bankId')
						->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bnk.BankName')
						->leftJoin('mstbankbranch AS brncname', function($join)
							{
								$join->on('brncname.BankId', '=', 'bnk.BankName');
								$join->on('brncname.id', '=', 'bnk.BranchName');
							})
						->where('bnk.delflg','=','0')
						->orderBy('bnk.id','ASC');
						// ->orderByRaw("CAST(banDet.balance as SIGNED INTEGER) DESC");
						// ->toSql();
						// dd($query);
						// ->get();
		return $query;
	}

	public static function bankrectype($bankId ,$accNo ,$type) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$accNo)
						->where('transcationType','=',$type)
						->get();
						// ->toSql();

		return $query;
	}

	public static function bankrectype2($bankId,$acc) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$acc)
						->where('transcationType','=',2)
						->get();
						// ->toSql();

		return $query;
	}

	public static function baseAmtInsChk($bankId ,$acc) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType','date')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$acc)
						->where('transcationType','=',9)
						->orderBy('acc_cashregister.date','DESC')
						->get();
						//->toSql();
		return $query;
	}

	public static function insertRec($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$insert= $db->table('acc_cashregister')->insert([
			'emp_ID' => "",
			'date' => $request->startdate,
			'transcationType' => 9,
			'bankIdFrom' => $request->bankid,
			//'branchIdFrom' => $request->branchids,
			'accountNumberFrom' => $request->accno,
			'bankIdFrom' => $request->bankid,
			'bankIdTo' => $request->transfer,
			'amount' => preg_replace("/,/", "",$request->txt_salary),
			'createdBy' => $name,
			'UpdatedBy' => $name,
			]);
		$id = DB::getPdo()->lastInsertId();;
		return $id;
	}

	public static function bankview($request) {
		$fromDate = $request->fromDate;
		$fromDate = date('Y-m-01', strtotime($fromDate));
		// Last day of the month.
		$toDate = date('Y-m-t', strtotime($fromDate));

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.Bank_NickName','bank.BranchName as branchId','branch.BranchName')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('mstbankbranch AS branch', function($join)
							{
								$join->on('bank.BranchName', '=', 'branch.id');
							})
						->where('bankIdFrom','=',$request->bankid)
						->where('accountNumberFrom','=',$request->accno)
						->where('date','>=',$fromDate)
						->where('date','<=',$toDate)
						->where('transcationType','!=',9)
						->orderBy('acc_cashregister.date','ASC')
						->paginate($request->plimit);
						//->toSql();
		return $query;
	}

}
