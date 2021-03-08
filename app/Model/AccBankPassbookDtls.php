<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class AccBankPassbookDtls extends Model {
	public static function bankPassbookindex($request) {

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
			$query = $query->WHERERAW("'$request->selYear' BETWEEN SUBSTRING(dateRangeFrom, 1, 4) AND SUBSTRING(dateRangeTo, 1, 4)");
		}

			$query = $query->orderBy('bankPassbook.bankId', 'ASC')
							->orderBy('bankPassbook.pageNoFrom', 'ASC')
							->orderBy('bankPassbook.pageNoTo', 'ASC')
							->paginate($request->plimit);

		return $query;
	}

	public static function getYears($request) {
		$db = DB::connection('mysql');
		$years = $db->table('acc_bankpassbookdtls')
					->select(DB::raw("SUBSTRING(dateRangeFrom, 1, 4) as yearFrom"), DB::raw("SUBSTRING(dateRangeTo, 1, 4) as yearTo"))
					->groupBy(DB::raw("SUBSTRING(dateRangeTo, 1, 4)"), DB::raw("SUBSTRING(dateRangeTo, 1, 4)"))
					->get();
	 	return $years;
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

	public static function fetchEditbanknames($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->orwhere('mstbank.delflg','=','0')
						->orwhere('mstbank.id','=',$request->bnk_id);
		
		$query = $query->orderBy('mstbank.Bank_NickName','ASC')
						->lists('BANKNAME','mstbank.id');
						// ->toSql();dd($query);
		return $query;
	}

	public static function fetchbankdtls($bankId) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->where('mstbank.id', '=', $bankId)
						->get();
		return $query;
	}

	public static function insertRec($request,$fileName,$orderId) {

		$name = Session::get('FirstName').' '.Session::get('LastName');

		$db = DB::connection('mysql');

		$insert = $db->table('acc_bankpassbookdtls')

					->insert([ 
								'bankId' => $request->bankId,
								'pageNoFrom' => $request->pageNoFrom,
								'pageNoTo' => $request->pageNoTo,
								'dateRangeFrom' => $request->dateRangeFrom,
								'dateRangeTo' => $request->dateRangeTo,
								'fileDtl' => $fileName,
								'orderId' => $orderId,
								'createdBy' => $name,

							]);

		self::updNxtFlg($request->pageNoFrom);

		return $insert;

	}


	public static function updateRec($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');

		$update = DB::table('acc_bankpassbookdtls')

						->where('id', $request->edit_id)

						->update([ 

								'bankId' => $request->bankId,
								'pageNoFrom' => $request->pageNoFrom,
								'pageNoTo' => $request->pageNoTo,
								'dateRangeFrom' => $request->dateRangeFrom,
								'dateRangeTo' => $request->dateRangeTo,
								'fileDtl' => $fileName,
								'updatedBy' => $name,

							]);

		self::updNxtFlg($request->pageNoFrom);

		return $update;

	}

	public static function updNxtFlg($pageNoFrom) {

		$name = Session::get('FirstName').' '.Session::get('LastName');

		$maxpageNoTo = DB::table('acc_bankpassbookdtls')

					->where('pageNoFrom','=', $pageNoFrom)

					->max('pageNoTo');

		$update = DB::table('acc_bankpassbookdtls')

					->where('pageNoFrom','=', $pageNoFrom)

					->where('pageNoTo','<', $maxpageNoTo)

					->update([ 

							'nxtFlg' => 1,
							'updatedBy' => $name,

						]);

		$update = DB::table('acc_bankpassbookdtls')

					->where('pageNoFrom','=', $pageNoFrom)

					->where('pageNoTo','=', $maxpageNoTo)

					->update([ 

							'nxtFlg' => 0,
							'updatedBy' => $name,

						]);

		return $update;

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

		if ($request->flg == 1) {
			$request->dateRange = date("Y-m-d", strtotime("1 day", strtotime($request->dateRange)));
		} else {
			$request->dateRange = date("Y-m-d", strtotime("-1 day", strtotime($request->dateRange)));
		}

		$result = DB::table('acc_bankpassbookdtls')

						->SELECT('*');

		if ($request->edit_flg == 2) {
			$result = $result->WHERE('id', '!=' ,$request->edit_id)
							->WHERE('bankId', '=' ,$request->bankId)
							->WHERERAW("'$request->dateRange' BETWEEN dateRangeFrom AND IF(dateRangeTo IS NULL, CURDATE(),dateRangeTo)");
		} else {
			$result = $result->WHERE('bankId', '=' ,$request->bankId)
							->WHERERAW("'$request->dateRange' BETWEEN dateRangeFrom AND IF(dateRangeTo IS NULL, CURDATE(),dateRangeTo)");
		}

		$result = $result->get();
						
		return $result;

	}

	public static function getpageNoExists($request) {

		$result = DB::table('acc_bankpassbookdtls')

						->SELECT('*');

		if ($request->edit_flg == 2) {
			$result = $result->WHERE('id', '!=' ,$request->edit_id)
							->WHERE('pageNoFrom', '=' ,$request->pageNoFrom)
							->WHERE('pageNoTo', '=' ,$request->pageNoTo);
		} else {
			$result = $result->WHERE('pageNoFrom', '=', $request->pageNoFrom)
							->WHERE('pageNoTo', '=' ,$request->pageNoTo);
		}

		$result = $result->get();
						
		return $result;

	}

	public static function getPassbookMinId($request) {
		$db = DB::connection('mysql');
		$latDetails = $db->table('acc_bankpassbookdtls')
						->WHERE('fileDtl','!=',NULL)
						->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
						->min('id');
		return $latDetails;
	}

	public static function getPassbookMaxId($request) {
		$db = DB::connection('mysql');
		$latDetails = $db->table('acc_bankpassbookdtls')
						->WHERE('fileDtl','!=',NULL)
						->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
						->max('id');
		return $latDetails;
	}

	public static function fnGetPassbookImgDtls($request) {
		$db = DB::connection('mysql');
		$result = $db->TABLE('acc_bankpassbookdtls')
						->SELECT('id','fileDtl')
						->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
						->ORDERBY('id','ASC')
						->GET();
		return $result;
	}

	public static function getPrevNxtImg($request) {
		$db = DB::connection('mysql');
		if ($request->imageFlg == 2) {
			$latDetails = $db->table('acc_bankpassbookdtls')
							->WHERE('fileDtl','!=',NULL)
							->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
							->WHERE('id', '>' , $request->imageId)
							->min('id');
		} else {
			$latDetails = $db->table('acc_bankpassbookdtls')
							->WHERE('fileDtl','!=',NULL)
							->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
							->WHERE('id', '<' , $request->imageId)
							->max('id');
		}
		$result = $db->TABLE('acc_bankpassbookdtls')
					->select('*')
					->WHERE(DB::raw("SUBSTRING(dateRangeFrom, 1, 4)"),'=', $request->selYear)
					->WHERE('id', '=' , $latDetails)
					->ORDERBY('id', 'ASC')
					->get();
		return $result;
	}


	
}
