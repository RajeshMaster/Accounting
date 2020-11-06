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

}
