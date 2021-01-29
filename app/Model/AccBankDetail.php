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
						->SELECT('banDet.id','banname.id AS bnkid','brncname.id AS brnchid','bnk.id AS bankid','bnk.AccNo','banDet.balance','banDet.processFlg','banDet.bankId AS balbankid','banname.BankName AS banknm','brncname.BranchName AS brnchnm','bnk.Bank_NickName AS NickName')
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

	public static function baseAmtInsChk($bankId,$acc) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType','date','id')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$acc)
						->where('transcationType','=',9)
						// ->orderBy('acc_cashregister.date','DESC')
						->get();
						//->toSql();
		return $query;
	}

	public static function insertRec($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$insert= $db->table('acc_cashregister')->insert([
			'emp_ID' => "",
			'date' => $request->txt_startdate,
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

	public static function updateRec($request ,$id) {
		// print_r($_REQUEST);exit();
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update=DB::table('acc_cashregister')
						->where('id', $id)
						->update([
						'date' => $request->txt_startdate,
						'amount' => preg_replace("/,/", "",$request->txt_salary),
						'updatedBy' => $name,
				]);
		return $update;
	}

	public static function bankview($request,$startdate,$curDate,$from_date,$to_date,$cdm,$flg) {

		$year = "";
		$month = "";
		if($cdm != ""){
			$year = substr($cdm,0,4);
			$month = substr($cdm,5,2);
		}

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister as cashreg')
						->SELECT('cashreg.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.Bank_NickName','bank.BranchName as branchId','branch.BranchName')
						->leftJoin('mstbank AS bank', function($join) {
								$join->on('cashreg.bankIdFrom', '=', 'bank.BankName');
								$join->on('cashreg.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('mstbankbranch AS branch', function($join) {
								$join->on('bank.BranchName', '=', 'branch.id');
							})
						->where('cashreg.bankIdFrom','=',$request->bankid)
						->where('cashreg.accountNumberFrom','=',$request->accno)
						->where('cashreg.transcationType','!=',9);

			if($from_date != ""){

				$query = $query->where(function($joincont) use ($startdate,$curDate,$from_date) {
								$joincont->WHERERAW("cashreg.date >= '$startdate'")
										->WHERERAW("cashreg.date <= '$curDate'")
										->WHERERAW("cashreg.date <= '$from_date'");
								});

			} else if($to_date != ""){

				$query = $query->where(function($joincont) use ($startdate,$curDate,$to_date) {
								$joincont->WHERERAW("cashreg.date >= '$startdate'")
										->WHERERAW("cashreg.date <= '$curDate'")
										->WHERERAW("cashreg.date >= '$to_date'");
								});

			} else if($cdm != "" && $month != ""){

				$query = $query->where(function($joincont) use ($startdate,$curDate,$year,$month) {
								$joincont->WHERERAW("cashreg.date >= '$startdate'")
										->WHERERAW("cashreg.date <= '$curDate'")
										->WHERERAW("SUBSTRING(cashreg.date,1,4)='$year'")
										->WHERERAW("SUBSTRING(cashreg.date,6,2)='$month'");
								});

			} else if($cdm != "" && $month == ""){

			 	$query = $query->where(function($joincont) use ($startdate,$curDate,$year) {
								$joincont->WHERERAW("cashreg.date >= '$startdate'")
										->WHERERAW("cashreg.date <= '$curDate'")
										->WHERERAW("SUBSTRING(cashreg.date,1,4) = '$year'");
								});

			} else {

				$query = $query->where(function($joincont) use ($startdate,$curDate) {
								$joincont->WHERERAW("cashreg.date >= '$startdate'")
										->WHERERAW("cashreg.date <= '$curDate'");
								});

			}

			$query = $query->orderBy('cashreg.orderId','ASC');
			
			if ($flg == 1) {
				$query = $query->get();
			} else {
				$query = $query->paginate($request->plimit);
			}
							
		return $query;
	}

	public static function fnGetAccountPeriodBK($request) {
		$query=DB::table('dev_kessandetails')
						->SELECT('*')
						->where('delflg','=','0')
						->get();
		return $query;
	}

	public static function AccBalance($request,$startDate,$prevDate) {
		$curDate = date('Y-m-d');
		$db = DB::connection('mysql');
			$query = $db->table('acc_cashregister')
						->SELECT('transcationType','amount','fee')
						->where('bankIdFrom','=',$request->bankid)
						->where('accountNumberFrom','=',$request->accno)
						->where('transcationType','!=',9)
						->where('delFlg','=','0');
		
			$query = $query->WHERERAW("date >= '$startDate'");
			$query = $query->WHERERAW("date <= '$curDate'");
		if ($prevDate != "") {
			$query = $query->WHERERAW("SUBSTRING(date,1,7) <= '$prevDate'");
		}			
			$query = $query->get();

		return $query;
	}

}
