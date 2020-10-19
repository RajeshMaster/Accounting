<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class AccBankDetail extends Model {

	public static function bankrectype1($bankId) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashRegister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('transcationType','=',1)
						->get();
						// ->toSql();

		return $query;
	}

	public static function bankrectype2($bankId) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashRegister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('transcationType','=',2)
						->get();
						// ->toSql();

		return $query;
	}

	public static function bankrectype3($bankId) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashRegister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('transcationType','=',3)
						->get();
						// ->toSql();

		return $query;
	}

	public static function baseAmtInsChk($bankId) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashRegister')
						->SELECT('amount','fee','transcationType')
						->where('bankIdFrom','=',$bankId)
						->where('transcationType','=',9)
						->get();
						// ->toSql();

		return $query;
	}

	public static function insertRec($request) {
		print_r($request->all());exit;

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$insert= $db->table('acc_cashRegister')->insert([
			'emp_ID' => "",
			'date' => $request->date,
			'transcationType' => $request->transtype,
			'bankIdFrom' => $request->bank,
			'bankIdTo' => $request->transfer,
			'amount' => $request->amount,
			'fee' => $request->fee,
			'content' => $request->content,
			'remarks' => $request->remarks,
			'createdBy' => $name,
			'UpdatedBy' => $name,
			]);
		$id = DB::getPdo()->lastInsertId();;
		return $id;
	}

}
