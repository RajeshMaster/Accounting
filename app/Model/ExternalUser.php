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

						->orderBy('id','ASC')

						->paginate($request->plimit);

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

							'dob' => $request->dob,

							'gender' => $request->gender,

							'mobileno' => $phone,

							'address' => $request->address,

							'buildingName' => $request->buildingName,

							'pincode' => $request->pincode,

							'bankId' => $request->bankId,

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
							
							'password' => md5($request->userPassword),

							'conpassword' => md5($request->userConPassword),

							'dob' => $request->dob,

							'gender' => $request->gender,

							'mobileno' => $phone,

							'address' => $request->address,

							'buildingName' => $request->buildingName,

							'pincode' => $request->pincode,

							'bankId' => $request->bankId,

							'delflg' => 0,

							'CreatedBy' => Auth::user()->username,

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

						->SELECT('*')

						->WHERE('emailId', '=', $request->emailId)

						->get();

		return $result;

	}

}