<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class Loan extends Model {
	public static function loanindex($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails AS loan')
						->SELECT('mst.AccNo','ban.BankName','mstbra.BranchName','loan.*',
							DB::raw("(SELECT count(amount) FROM dev_banktransfer WHERE bankname= mst.id
				AND loan_flg =1 AND loanType =loan.loanType AND billno=loan.loanNo) as paycount"),
							DB::raw("(SELECT sum(amount) FROM dev_banktransfer WHERE bankname= mst.id
				AND loan_flg =1 AND loanType =loan.loanType AND billno=loan.loanNo) as payamount
							"))
						->leftJoin('mstbank AS mst', 'loan.bankId', '=', 'mst.id')
						->Join('mstbanks AS ban', 'ban.id', '=', 'mst.BankName')
						->leftJoin('mstbankbranch AS mstbra', function($join)
							{
								$join->on('mst.BankName', '=', 'mstbra.BankId');
								$join->on('mst.BranchName', '=', 'mstbra.id');
							})
						->where('loan.delFlg','=','0');
						// ->toSql();
						// dd($query);
		return $query;
	}
	public static function loanview($request) {
		$db = DB::connection('mysql');
		$query= $db->table('dev_banktransfer AS bts')
						->SELECT('bts.amount as paymentAmount','bts.bankdate as paymentdate','bts.fee','bts.remark_dtl','lnd.*')
						->leftJoin('inv_loandetails AS lnd', function($join)
							{
								$join->on('lnd.loanType', '=', 'bts.loanType');
								$join->on('lnd.bankId', '=', 'bts.bankname');
								$join->on('bts.billno', '=', 'lnd.loanNo');
							})
						->where('bts.loan_flg','=',1)
						->where('bts.billno','=',$request->id)
						->orderBy('bankdate', 'ASC');
						// ->toSql();
						// dd($query);
		return $query;
	}
	public static function loanamount($request) {
		$db = DB::connection('mysql');
		$query= $db->table('dev_banktransfer')
						->SELECT(DB::raw("SUM(amount) as amount"),DB::raw("SUM(fee) as fee"))
						->where('billno','=',$request->id)
						->get();
		return $query;
	}
	public static function createnewloanid($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails')
						->SELECT(DB::raw('loanNo'))
			        	->where('loanNo', 'LIKE', 'LON%')
						->orderBy('loanNo','desc')
						->limit('1')
						->get();
		return $query;
	}
	public static function fetchloantype($request) {
		$db = DB::connection('mysql');
		if(Session::get('languageval') == "en") {
			$query= $db->table('inv_set_loantype')
						->SELECT('*')
						->where('delflg','=',0)
						->lists('loanEng','id');
		} else {
			$query= $db->table('inv_set_loantype')
						->SELECT('*')
						->where('delflg','=',0)
						->lists('loanJap','id');			
		}
		return $query;
	}
	public static function fetchbankname($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbanks as ban')
						->SELECT(DB::RAW("CONCAT(ban.BankName,'-',mst.AccNo) AS BANKNAME"),'mst.id','ban.id as banid','ban.BankName','mstbra.BranchName','mst.AccNo','mstbra.Id as braid')
						->Join('mstbankbranch AS mstbra', 'ban.id', '=', 'mstbra.BankId')
						->Join('mstbank AS mst', function($join)
							{
								$join->on('ban.id', '=', 'mst.BankName');
								$join->on('mst.BranchName', '=', 'mstbra.Id');
							})
						->where('ban.delflg','=',0)
						->where('ban.location','=',2)
						->lists('BANKNAME','mst.id');
						// ->get();
						// ->toSql();
						// dd($query);
		return $query;
	}
	public static function viewedit($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails AS loan')
						->SELECT('bankId AS bankname',
								'loanType AS loantype',
								'loanName AS loanname',
								'amount AS amount',
								'receivedDate AS txt_startdate',
								'endDate AS txt_end_date',
								'period AS loanperiod',
								'interest AS interest',
								'repaymentDate AS paymentday',
								'currentBalance AS currentbalance',
								'remainingMonths AS remainingmonths',
								'checkFlg AS checkFlg',
								'remarks AS Remarks')
						->where('loanNo','=',$request->id)
						->get();
		return $query;
	}
	public static function fetchdetails($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails')
						->SELECT('*')
						->where('loanNo','=',$request->id)
						->get();
		return $query;
	}
	public static function loansingleview($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails AS loan')
						->SELECT('mst.AccNo','ban.BankName','mstbra.BranchName','loantype.loanEng','loantype.loanJap','loan.*')
						->leftJoin('mstbank AS mst', 'loan.bankId', '=', 'mst.id')
						->leftJoin('mstbanks AS ban', 'ban.id', '=', 'mst.BankName')
						->leftJoin('mstbankbranch AS mstbra', 'mst.BankName', '=', 'mstbra.BankId')
						->leftJoin('inv_set_loantype AS loantype', 'loantype.id', '=', 'loan.loanType')
						->where('loan.delFlg','=',0)
						->where('loan.loanNo','=',$request->id)
						->get();
		return $query;
	}
	public static function loanconfirm($request) {
		$db = DB::connection('mysql');
		$update=DB::table('inv_loandetails')
			->where('loanNo', $request->id)
			->where('delFlg','=',0)
			->update(
				['editFlg' => $request->loan_confirm,
				'checkFlg' => $request->loan_confirm,
				'up_Datetime' => date('Y-m-dh:i:s')]
		);
		return $update;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'inv_loandetails'");
		return $statement[0]->Auto_increment;
	}
	public static function insertloanRec($request,$newid,$data) {
		if ($data == "") {
			$data = "";
		} else {
			$data = $data;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$insert=DB::table('inv_loandetails')
			->insert(
				['loanNo' => $newid,
				'bankId' => $request->bankname,
				'loanType' => $request->loantype,
				'loanName' => $request->loanname,
				'amount' => $request->amount,
				'receivedDate' => $request->txt_startdate,
				'endDate' => $request->end_dates,
				'period' => $request->loanperiod,
				'interest' => $request->interest,
				'repaymentDate' => $request->paymentday,
				'currentBalance' => $request->currentbalance,
				'remainingMonths' => $request->remainingmonths,
				'remarks' => $request->Remarks,
				'pdfFile' => $data,
				'checkFlg' => $request->check,
				'delflg' => 0,
				'reflectPassbookflg' => $request->reflectpass,
				'ins_Datetime' => date('Y-m-d H:i:s'),
				'createdBy' => $name,
				'up_Datetime' => date('Y-m-d H:i:s'),
				'updatedBy' => $name]
		);
		return $insert;
	}
	public static function UpdateloanRec($request,$data) {
		if($data != "") {
			$files = $data;
		} else {
			$files = $request->pdffiles;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$update=DB::table('inv_loandetails')
			->where('loanNo', $request->id)
			->update(
				['bankId' => $request->bankname,
				'loanType' => $request->loantype,
				'loanName' => $request->loanname,
				'amount' => $request->amount,
				'receivedDate' => $request->txt_startdate,
				'endDate' => $request->end_dates,
				'period' => $request->loanperiod,
				'interest' => $request->interest,
				'repaymentDate' => $request->paymentday,
				'currentBalance' => $request->currentbalance,
				'remainingMonths' => $request->remainingmonths,
				'remarks' => $request->Remarks,
				'pdfFile' => $files,
				'checkFlg' => $request->check,
				'delflg' => 0,
				'reflectPassbookflg' => $request->reflectpass,
				'up_Datetime' => date('Y-m-d H:i:s'),
				'updatedBy' => $name]
		);
		return $update;
	}
}