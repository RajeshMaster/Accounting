<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class CreditCardPay extends Model {

	public static function fetchcreditCardnames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_creditcard')
						->SELECT('*')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->orderBy('acc_creditcard.id','ASC')
						->lists('creditCardName','id');
						// ->toSql();
		return $query;
	}

	public static function updInvoice($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update=DB::table('dev_invoices_registration')
						->where('user_id', $request->invcId)
						->update(
							array(
								'confirmFlg' => $request->flg,
								'updated_by' => $name
							)
						);
		return $update;
	}

	public static function fetchcategorynames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_categorysetting')
						->SELECT('*')
						->orderBy('acc_categorysetting.id','ASC')
						->lists('Category','id');
		return $query;
	}

	public static function inscreditCardDtls($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$i = 1;
		$insert = 0;
		$sheetData = $request->sheetData;
		$selectedYearMonth = $request->mainYear.'-'.$request->selectedMonth.'-01';
		
		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$orderId = $statement[0]->Auto_increment;
		} else {
			$orderId = 1;
		}


		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_creditcard')
						->SELECT('*')
						->WHERE('id', '=', $request->creditCardId)
						->get();
						// ->toSql();
		$bankacc = explode('-', $query[0]->bank);
	
		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => "",
							'date' => $request->mainDate,
							'transcationType' => 1,
							'bankIdFrom' => $bankacc[0],
							'accountNumberFrom' => $bankacc[1],
							'bankIdTo' => '',
							'amount' => preg_replace("/,/", "",  $request->totalAmount),
							'content' => 'Credit Card Payment',
							'remarks' => '',
							'pageFlg' => 999,
							'createdBy' => $name,
							'orderId' => $orderId,
						]);

		$db = DB::connection('mysql');
		if ($sheetData != 0) {
			for ($i = 1; $i < $sheetData-1; $i++) { 
				$creditCardDate = "creditCardDate".$i;
				$creditCardContent = "creditCardContent".$i;
				$creditCardAmount = "creditCardAmount".$i;
				$rdoBill = "rdoBill".$i;
				$categoryId = "categoryId".$i;
				$remarks = "remarks".$i;
				$insert = $db->table('acc_creditcardpayment')
							->insert([
									'selectedYearMonth' => $selectedYearMonth,
									'mainDate' => $request->mainDate,
									'creditCardId' => $request->creditCardId,
									'creditCardDate' => $request->$creditCardDate,
									'creditCardContent' => $request->$creditCardContent,
									'creditCardAmount' => $request->$creditCardAmount,
									'rdoBill' => $request->$rdoBill,
									'categoryId' => $request->$categoryId,
									'remarks' => $request->$remarks,
									'createdBy' => $name,
								]);
			}
		}
		return $insert;
	}

	public static function fetchcreditcarddetails($from_date, $to_date,$request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT('acc_creditcardpayment.*','acc_creditcard.creditCardName','acc_categorysetting.Category')
						->leftJoin('acc_creditcard', 'acc_creditcard.id', '=', 'acc_creditcardpayment.creditCardId')
						->leftJoin('acc_categorysetting', 'acc_categorysetting.id', '=', 'acc_creditcardpayment.categoryId')
						->where('selectedYearMonth','>=',$from_date)
						->where('selectedYearMonth','<=',$to_date);
		if (isset($request->category) && $request->category!= "") {
			$query = $query->where('categoryId','=',$request->category);
		} 
		$query = $query->orderBy('creditCardId', 'ASC')
						->paginate($request->plimit);
						// ->get();
		return $query;
	}

	public static function fetchcreditcardEdit($request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT('acc_creditcardpayment.*','acc_creditcard.creditCardName','acc_categorysetting.Category')
						->leftJoin('acc_creditcard', 'acc_creditcard.id', '=', 'acc_creditcardpayment.creditCardId')
						->leftJoin('acc_categorysetting', 'acc_categorysetting.id', '=', 'acc_creditcardpayment.categoryId')
						->where('acc_creditcardpayment.id', $request->id)
						->get();
		return $query;
	}

	public static function updateDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update = DB::table('acc_creditcardpayment')
						->where('id', $request->id)
						->update(
							array(
								'mainDate' => $request->mainDate,
								'creditCardId' => $request->creditCard,
								'creditCardDate' => $request->creditCardDate,
								'creditCardContent' => $request->content,
								'creditCardAmount' => preg_replace("/,/", "", $request->amount),
								'rdoBill' => $request->rdoBill,
								'categoryId' => $request->categories,
								'remarks' => $request->remarks,
								'file' => $fileName,
								'UpdatedBy' => $name
							)
						);
		return $update;

	}

	public static function fnGetAccountPeriodAcc() {
		$accperiod=DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
	                    ->get();
	        return $accperiod;
	}

	public static function fnGetcreditCardAllRecord($request) {
		if (isset($request->category) && $request->category!= "") {
			$conditionAppend = "AND (categoryId = '$request->category')";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}
		$sql = "SELECT SUBSTRING(selectedYearMonth, 1, 7) AS date FROM acc_creditcardpayment 
				where (delFlg = '0' $conditionAppend) ORDER BY selectedYearMonth ASC";
		$cards = DB::select($sql);
		return $cards;
	}


	public static function fnGetcreditCardRecord($from_date, $to_date, $request) {
		if (isset($request->category) && $request->category!= "") {
			$conditionAppend = "AND (categoryId = '$request->category')";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}
		$tbl_name = "acc_creditcardpayment";
		$sql = "SELECT SUBSTRING(selectedYearMonth, 1, 7) AS date 
				FROM $tbl_name 
				WHERE (selectedYearMonth > '$from_date' AND selectedYearMonth < '$to_date') 
				AND delFlg = '0' $conditionAppend
				ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetcreditCardRecordPrevious($from_date, $request) {

		$tbl_name = "acc_creditcardpayment";

		if (isset($request->category) && $request->category!= "") {
			$conditionAppend = "AND (categoryId = '$request->category')";
		} else {
			$conditionAppend = "AND (1 = 1) AND (delFlg = 0)";
		}
		
		$sql = "SELECT SUBSTRING(selectedYearMonth, 1, 7) AS date FROM $tbl_name 
			WHERE (selectedYearMonth <= '$from_date' $conditionAppend) ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCreditCardRecordNext($to_date, $request) {
		if (isset($request->category) && $request->category!= "") {
			$conditionAppend = "AND (categoryId = '$request->category')";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}

		$sql = "SELECT SUBSTRING(selectedYearMonth, 1, 7) AS date FROM acc_creditcardpayment 
			WHERE (selectedYearMonth >= '$to_date') AND delFlg = '0' $conditionAppend ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function getDataBycard($request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT('acc_creditcardpayment.selectedYearMonth')
						->where('creditCardId','=',$request->creditCardVal)
						// ->where('creditCardId','=',1)
						->WHERE('selectedYearMonth', 'LIKE', '%'.$request->mainYear.'%')
						->orderBy('selectedYearMonth', 'ASC')
						->get();
		return $query;
	}

	public static function deletdRecorsForYM($request) {
		$query = DB::table('acc_cashregister')
						->WHERE('date', 'LIKE', '%'.$request->clearCashDate.'%')
						->WHERE('pageFlg','=',999)
						->DELETE();

		$query = DB::table('acc_creditcardpayment')
						->WHERE('selectedYearMonth', 'LIKE', '%'.$request->selYear.'-'.$request->selMonth.'%')
						->WHERE('creditCardId','=',$request->creditCardId)
						->DELETE();
		return $query;
	}

	public static function fetchAmountForYearlyWise($from_date, $to_date,$request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT('creditCardAmount as amount','creditCardDate')
						// ->leftJoin('acc_creditcard', 'acc_creditcard.id', '=', 'acc_creditcardpayment.creditCardId')
						// ->leftJoin('acc_categorysetting', 'acc_categorysetting.id', '=', 'acc_creditcardpayment.categoryId')
						->where('creditCardDate','>=',$from_date)
						->where('creditCardDate','<=',$to_date);
			$query = $query->orderBy('creditCardDate', 'ASC')
						->get();
		return $query;
	}

	public static function fetchpreviousNextRecord($year) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT(DB::RAW("COUNT(creditCardDate) as count"))
						->WHERE('creditCardDate', 'LIKE', '%'.$year.'%')
						->orderBy('creditCardDate', 'ASC')
						->get();
		return $query;
	}

	public static function fetchpreviousNextRecordCategory($year ,$request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT(DB::RAW("COUNT(selectedYearMonth) as count"))
						->WHERE('selectedYearMonth', 'LIKE', '%'.$year.'%')
						->where('categoryId','=',$request->category)
						->orderBy('selectedYearMonth', 'ASC')
						->get();
		return $query;
	}

	public static function fnGetcreditCardRecordMonthwise($from_date, $to_date, $request) {
		if (isset($request->content) && $request->content!= "") {
			$conditionAppend = "AND (categoryId = '$request->content')";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}
		$tbl_name = "acc_creditcardpayment";
		$sql = "SELECT SUBSTRING(creditCardDate, 1, 7) AS date 
				FROM $tbl_name 
				WHERE (creditCardDate > '$from_date' AND creditCardDate < '$to_date') 
				AND delFlg = '0' $conditionAppend
				ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetcreditCardRecordPreviousMonthwise($from_date, $request) {

		$tbl_name = "acc_creditcardpayment";

		if (isset($request->content) && $request->content!= "") {
			$conditionAppend = "AND (categoryId = '$request->content')";
		} else {
			$conditionAppend = "AND (1 = 1) AND (delFlg = 0)";
		}
		
		$sql = "SELECT SUBSTRING(creditCardDate, 1, 7) AS date FROM $tbl_name 
			WHERE (creditCardDate <= '$from_date' $conditionAppend) ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCreditCardRecordNextMonthwise($to_date, $request) {
		if (isset($request->content) && $request->content!= "") {
			$conditionAppend = "AND (categoryId = '$request->content') AND (delFlg = 0)";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}

		$sql = "SELECT SUBSTRING(creditCardDate, 1, 7) AS date FROM acc_creditcardpayment 
			WHERE (creditCardDate >= '$to_date') AND delFlg = '0' $conditionAppend ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fetchcreditcarddetailsMonthwise($from_date, $to_date,$request) {
		
		$db = DB::connection('mysql');
		$query = $db->table('acc_creditcardpayment')
						->SELECT('acc_creditcardpayment.*','acc_creditcard.creditCardName','acc_categorysetting.Category')
						->leftJoin('acc_creditcard', 'acc_creditcard.id', '=', 'acc_creditcardpayment.creditCardId')
						->leftJoin('acc_categorysetting', 'acc_categorysetting.id', '=', 'acc_creditcardpayment.categoryId')
						->where('creditCardDate','>=',$from_date)
						->where('creditCardDate','<=',$to_date);
			$query = $query->orderBy('creditCardId', 'ASC')
						->orderBy('creditCardDate', 'ASC')
						->paginate($request->plimit);
						// ->get();
		return $query;
	}

	public static function fnGetcreditCardAllRecordMonthwise($request) {
		if (isset($request->content) && $request->content!= "") {
			$conditionAppend = "AND (categoryId = '$request->content')";
		} else {
			$conditionAppend = "AND (1 = 1)";
		}
		$sql = "SELECT SUBSTRING(creditCardDate, 1, 7) AS date FROM acc_creditcardpayment 
				where (delFlg = '0' $conditionAppend) ORDER BY creditCardDate ASC";
		$cards = DB::select($sql);
		return $cards;
	}
}
