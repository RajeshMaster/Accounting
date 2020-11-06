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
}
