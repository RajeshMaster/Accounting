<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;

class ExternalBank extends Model {


	public static function getbankDetails($request) {

		$result = DB::table('ext_mstbank')

						->SELECT('*')
						->orderBy('id','ASC')
						->paginate($request->plimit);

		return $result;

	}

	

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'ext_mstbank'");

		return $statement[0]->Auto_increment;

	}

	public static function insertBank($request) {

		$insert = DB::table('ext_mstbank')

						->insert([ 

							'accountNo' => $request->accountNo,
							'bankName' => $request->bankName,
							'bankKanaName' => $request->bankKanaName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'accountType' => $request->accountType,
							'mainflg' => 0,
							'delflg' => 0,
							'CreatedBy' => Auth::user()->username,
							'UpdatedBy' => Auth::user()->username

						]);

		return $insert;



	}



	public static function updateBank($request) {

		$update = DB::table('ext_mstbank')

						->where('id', $request->editId)

						->update([

							'accountNo' => $request->accountNo,
							'bankName' => $request->bankName,
							'bankKanaName' => $request->bankKanaName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'accountType' => $request->accountType,
							'UpdatedBy' => Auth::user()->username

						]);

    		return $update;

	}


	public static function viewBankDetails($id) {

		$result = DB::table('ext_mstbank')

						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();

		return $result;

	}

	public static function getaccountNoExists($request) {

		$result = DB::table('ext_mstbank')

						->SELECT('*');

		if ($request->editId != "") {
			$result = $result->WHERE('id', '!=' ,$request->editId)
							->WHERE('accountNo', '=' ,$request->accountNo);
		} else {
			$result = $result->WHERE('accountNo', '=', $request->accountNo);
		}

		$result = $result->get();
						
		return $result;

	}

	public static function changeDelFlg($request) {

		if ($request->delflg == 0) {
			$delflg = 1;
		} else {
			$delflg = 0;
		}

		$result = DB::table('ext_mstbank')

						->where('id', $request->id)

						->update([

							'delflg' => $delflg,
							'mainflg' => 0

						]);

		return $result;

	}

	public static function changeMainFlg($request) {

		if($request->mainflg == 0) {
			$mainflg = 1;
		} else {
			$mainflg = 0;
		}

		$result = DB::table('ext_mstbank')

						->where('id', $request->id)

						->update([

							'mainflg' => $mainflg

						]);

		return $result;

	}

	public static function getJapanAccount() {

		return array('1'=>$msg = "普通");

	}

}