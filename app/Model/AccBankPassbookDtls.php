<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class AccBankPassbookDtls extends Model {
	public static function bankPassbookindex($from_date,$to_date,$request) {

		$db = DB::connection('mysql');
		
		$query = $db->table('acc_bankpassbookdtls AS bankPassbook')
						->SELECT('bankPassbook.*','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','bnkName.id AS bnkid','bnkName.BankName AS bnknm','brnchName.id AS brnchid','brnchName.BranchName AS brnchnm')
						->leftJoin('mstbank AS bank', 'bankPassbook.bankId', '=', 'bank.id')
						->leftJoin('mstbanks AS bnkName', 'bnkName.id', '=', 'bank.BankName')
						->leftJoin('mstbankbranch AS brnchName', function($join)
							{
								$join->on('brnchName.BankId', '=', 'bank.BankName');
								$join->on('brnchName.id', '=', 'bank.BranchName');
							});
						
		if ($request->searchmethod == 3) {
			if (!empty($request->bank_id)) {
				$query = $query->where('bankPassbook.bankId', '=', $request->bank_id);
			}
		} else {
			$query = $query->where('dateRangeFrom', '>=', $from_date)
							->where('dateRangeFrom', '<=', $to_date);
		}

			$query = $query->orderBy('bankPassbook.bankId', 'ASC')
							->orderBy('bankPassbook.pageNo', 'ASC')
							->paginate($request->plimit);

		return $query;
	}

	public static function fnGetAccountPeriodAcc() {
		$accperiod = DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
						->get();
		return $accperiod;
	}

	public static function fnGetCashExpenseAllRecord() {
		
		$sql = "SELECT SUBSTRING(dateRangeFrom, 1, 7) AS date FROM acc_bankpassbookdtls 
				ORDER BY date ASC ";

		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecord($from_date, $to_date) {
	
		$tbl_name = "acc_bankpassbookdtls";
		$sql = "SELECT SUBSTRING(dateRangeFrom, 1, 7) AS date 
				FROM $tbl_name 
				WHERE (dateRangeFrom > '$from_date' AND dateRangeFrom < '$to_date') 
				ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecordPrevious($from_date) {

		$tbl_name = "acc_bankpassbookdtls";
		$sql = "SELECT SUBSTRING(dateRangeFrom, 1, 7) AS date FROM $tbl_name
			WHERE (dateRangeFrom <= '$from_date') ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecordNext($to_date) {

		$tbl_name = "acc_bankpassbookdtls";
		$sql = "SELECT SUBSTRING(dateRangeFrom, 1, 7) AS date FROM $tbl_name 
			WHERE (dateRangeFrom >= '$to_date') ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fetchbanknames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->where('mstbank.delflg','=','0')
						->orderBy('mstbank.Bank_NickName','ASC')
						->lists('BANKNAME','mstbank.id');
						// ->toSql();
		return $query;
	}

	public static function insertRec($request,$fileName,$orderId) {

		if($request->edit_flg == "3" && $request->pageNo != "") {
			$pageNoOrg = explode("-", $request->pageNo);
			if (isset($pageNoOrg[1])) {
				$pageNoAdd = $pageNoOrg[1] + 1;
				if (strlen($pageNoAdd) == 1) {
					$pageNoAdd = "0".$pageNoAdd;
				} else {
					$pageNoAdd = $pageNoAdd;
				}
			} 
			if (isset($pageNoOrg[0]) && isset($pageNoOrg[1])) {
				$pageNo = $pageNoOrg[0]."-".$pageNoAdd;
			} else {
				$pageNo = $request->pageNo;
			} 
		} else {
			$pageNo =  self::getNxtRegPageNo();
		}
		

		$name = Session::get('FirstName').' '.Session::get('LastName');

		$db = DB::connection('mysql');

		$insert = $db->table('acc_bankpassbookdtls')

					->insert([ 
								'bankId' => $request->bankId,
								'pageNo' => $pageNo,
								'dateRangeFrom' => $request->dateRangeFrom,
								'dateRangeTo' => $request->dateRangeTo,
								'fileDtl' => $fileName,
								'orderId' => $orderId,
								'createdBy' => $name,

							]);

		if ($request->edit_flg == "3") {
			
			$update = DB::table('acc_bankpassbookdtls')

							->where('id', $request->edit_id)

							->update([ 

									'nxtFlg' => 1,
									'updatedBy' => $name,

								]);
		}
		return $insert;

	}


	public static function updateRec($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');

		$update = DB::table('acc_bankpassbookdtls')

						->where('id', $request->edit_id)

						->update([ 

								'bankId' => $request->bankId,
								'dateRangeFrom' => $request->dateRangeFrom,
								'dateRangeTo' => $request->dateRangeTo,
								'fileDtl' => $fileName,
								'updatedBy' => $name,

							]);

		return $update;

	}

	public static function getNxtRegPageNo() {

		$maxpageNo = DB::table('acc_bankpassbookdtls')
						->max('pageNo');

		$maxpageNo = $maxpageNo + 1;
		if (strlen($maxpageNo) == 1) {
			$pageNoAdd = "0".$maxpageNo;
		} else {
			$pageNoAdd = $maxpageNo;
		}

		$pageNo = $pageNoAdd ."-01";

		return $pageNo;

	}

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'acc_bankpassbookdtls'");
		if (isset($statement[0]->Auto_increment)) {
			$statement = $statement[0]->Auto_increment;
		} else {
			$statement = 1;
		}
		return $statement;

	}

	public static function accBankPassbook($id) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_bankpassbookdtls AS bankPassbook')
						->SELECT('bankPassbook.*','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','bnkName.id AS bnkid','bnkName.BankName AS bnknm','brnchName.id AS brnchid','brnchName.BranchName AS brnchnm')
						->leftJoin('mstbank AS bank', 'bankPassbook.bankId', '=', 'bank.id')
						->leftJoin('mstbanks AS bnkName', 'bnkName.id', '=', 'bank.BankName')
						->leftJoin('mstbankbranch AS brnchName', function($join)
							{
								$join->on('brnchName.BankId', '=', 'bank.BankName');
								$join->on('brnchName.id', '=', 'bank.BranchName');
							})
						->where('bankPassbook.id','=',$id)
						->orderBy('bankPassbook.bankId','ASC')
						->orderBy('bankPassbook.orderId','ASC')
						->get();
						// ->toSql();
						// dd($query);
		return $query;

	}

	public static function getDateExists($request) {

		$result = DB::table('acc_bankpassbookdtls')

						->SELECT('*');

		if ($request->edit_id != "") {
			$result = $result->WHERE('id', '!=' ,$request->edit_id)
							->WHERE('bankId', '=' ,$request->bankId)
							->WHERERAW("'$request->dateRangeFrom' BETWEEN dateRangeFrom AND IF(dateRangeTo IS NULL, CURDATE(),dateRangeTo)");
		} else {
			$result = $result->WHERE('bankId', '=' ,$request->bankId)
							->WHERERAW("'$request->dateRangeFrom' BETWEEN dateRangeFrom AND IF(dateRangeTo IS NULL, CURDATE(),dateRangeTo)");
		}

		$result = $result->get();
						
		return $result;

	}


}
