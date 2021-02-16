<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;

class ExternalUser extends Model {


	public static function getUserDetails($request) {

		$result = DB::table('ext_mstuser')

						->SELECT('*')
						->ORDERBY('id','ASC')
						->PAGINATE($request->plimit);

		return $result;

	}

	

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'ext_mstuser'");

		return $statement[0]->Auto_increment;

	}

	public static function insertUser($request) {

		$phone = $request->userTelNo1.'-'.$request->userTelNo2.'-'.$request->userTelNo3;

		$insert = DB::table('ext_mstuser')

						->insert([ 

							'emailId' => $request->emailId,
							'userName' => $request->userName,
							'password' => md5($request->userPassword),
							'conpassword' => md5($request->userConPassword),
							'address' => $request->address,
							'buildingName' => $request->buildingName,
							'pincode' => $request->pincode,
							'mobileno' => $phone,
							'bankKanaName' => $request->bankKanaName,
							'accountNo' => $request->accountNo,
							'accountType' => $request->accountType,
							'bankName' => $request->bankName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'delflg' => 0,
							'CreatedBy' => Auth::user()->username,
							'UpdatedBy' => Auth::user()->username

						]);

		return $insert;



	}



	public static function updateUser($request) {

		$phone = $request->userTelNo1.'-'.$request->userTelNo2.'-'.$request->userTelNo3;

		$update = DB::table('ext_mstuser')

						->where('id', $request->editId)

						->update([

							'emailId' => $request->emailId,
							'userName' => $request->userName,
							'mobileno' => $phone,
							'address' => $request->address,
							'buildingName' => $request->buildingName,
							'pincode' => $request->pincode,
							'bankKanaName' => $request->bankKanaName,
							'accountNo' => $request->accountNo,
							'accountType' => $request->accountType,
							'bankName' => $request->bankName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'UpdatedBy' => Auth::user()->username

						]);

    		return $update;

	}


	public static function viewUserDetails($id) {

		$result = DB::table('ext_mstuser')

						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();

		return $result;

	}

	public static function getemailIdExists($request) {

		$result = DB::table('ext_mstuser')

						->SELECT('*');

		if ($request->editId != "") {
			$result = $result->WHERE('id', '!=' ,$request->editId)
							->WHERE('emailId', '=' ,$request->emailId);
		} else {
			$result = $result->WHERE('emailId', '=', $request->emailId);
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

		$result = DB::table('ext_mstuser')

						->where('id', $request->id)

						->update([

							'delflg' => $delflg

						]);

		return $result;

	}

	public static function getJapanAccount() {

		return array('1'=>$msg = "普通");

	}

	public static function passwordchange($request) {

		$update = DB::table('ext_mstuser')

			->where('id', $request->id)

			->update([

				'password' => md5($request->password),
				'conpassword' => md5($request->confirmpassword),
				'UpdatedBy' => Auth::user()->username
			]);

		return $update;

	}

	/*public static function getbankDetails() {

		$result = DB::table('ext_mstbank as bank')

					->SELECT(DB::RAW("CONCAT(bank.bankName,'-',bank.accountNo) AS BANKNAME"),'bank.id AS ID')
					->WHERE('bank.delflg','=','0')
					->ORDERBY('bank.bankName','ASC')
					->LISTS('BANKNAME','ID');
						
		return $result;

	}*/


	

}