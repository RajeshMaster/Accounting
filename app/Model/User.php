<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class User extends Model {
	public static function viewdetails($id) {
		$result= DB::table('dev_mstuser')
						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();
		return $result;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'dev_mstuser'");
		return $statement[0]->Auto_increment;
	}
	public static function insertRec($request,$Usercode) {
		if ($request->MstuserUserKbn == "4") {
			$request->DataView = "";
		}
		$phone = $request->MstuserTelNO.'-'.$request->MstuserTelNO1.'-'.$request->MstuserTelNO2;
		$insert=DB::table('dev_mstuser')->insert(
			['id' => '',
			'usercode' => $Usercode,
			'userid' => $request->MstuserUserID,
			'password' => md5($request->MstuserPassword),
			'conpassword' => md5($request->MstuserConPassword),
			'userclassification' => $request->MstuserUserKbn,
			'username' => $request->MstuserSurNM,
			'givenname' => $request->MstuserSurNMK,
			'nickName' => $request->Mstusernickname,
			'dob' => $request->MstuserBirthDT,
			'gender' => $request->MstuserSex,
			'mobileno' => $phone,
			'email' => $request->MstuserMailID,
			'accessDate' => $request->DataView,
			'delflg' => 0,
			'delchgflg' => 0,
			'Ins_DT' => date('Y-m-d'),
			'Ins_TM' => date('h:i:s'),
			'CreatedBy' => Auth::user()->username,
			'Up_DT' => date('Y-m-d'),
			'Up_TM' => date('h:i:s'),
			'UpdatedBy' => Auth::user()->username]
		);
		return $insert;
	}
	public static function UpdateReg($request) {
		if ($request->MstuserUserKbn == "4") {
			$request->DataView = "";
		}
		$phone = $request->MstuserTelNO.'-'.$request->MstuserTelNO1.'-'.$request->MstuserTelNO2;
		$update=DB::table('dev_mstuser')
			->where('id', $request->editid)
			->update(
				['userid' => $request->MstuserUserID,
				'userclassification' => $request->MstuserUserKbn,
				'username' => $request->MstuserSurNM,
				'givenname' => $request->MstuserSurNMK,
				'nickName' => $request->Mstusernickname,
				'dob' => $request->MstuserBirthDT,
				'gender' => $request->MstuserSex,
				'mobileno' => $phone,
				'email' => $request->MstuserMailID,
				'accessDate' => $request->DataView,
				'delflg' => 0,
				'delchgflg' => 0,
				'Up_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => Auth::user()->username]
		);
    	return $update;
	}
	public static function getUserDetails($request) {
		$srt = "usercode";
		$odr = "ASC";
		$result= DB::table('dev_mstuser')
						->SELECT('*');
		if(Session::get('userclassification') != "4") {
			$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('userclassification', '!=', 4)
                                      		   ->where('usercode', '=', Session::get('usercode'));
                                      });
		}
		if($request->filterval == 1){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '!=', '');
                                      });
		} else if($request->filterval==2){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 1)
                                      		   ->where('delchgflg', '=', 0);
                                      });
		} else if($request->filterval==3){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 0)
                                      		   ->where('userclassification', '=', 0)
                                      		   ->where('delchgflg', '=', 0);
                                      });
		} else if($request->filterval==4){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 0)
                                      		   ->where('userclassification', '=', 1)
                                      		   ->where('delchgflg', '=', 0);
                                      });
		} else if($request->filterval==5){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 0)
                                      		   ->where('userclassification', '=', 2)
                                      		   ->where('delchgflg', '=', 0);
                                      });
		} else if($request->filterval==6){
						$result = $result->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 0)
                                      		   ->where('userclassification', '=', 3)
                                      		   ->where('delchgflg', '=', 0);
                                      });
		}
		if ($request->searchmethod == 1) {
          				$result = $result->where(function($joincont) use ($request) {
                                    $joincont->where('usercode', 'LIKE', '%' . $request->singlesearch . '%')
                                    		 ->orwhere('username', 'LIKE', '%' . $request->singlesearch . '%');
                                    });
   		} elseif ($request->searchmethod == 2) {
   			if (!empty($request->msearchempid)) {
					$result = $result->where('usercode', 'LIKE', '%' . $request->msearchempid . '%');
			}
			if ($request->userclassification !== "") {
					$result = $result->where('userclassification', 'LIKE', '%' . $request->userclassification . '%');
			}
   		}
		$result= $result->orderBy($srt, $request->sortOrder)
					  	->paginate($request->plimit);
		return $result;
	}
	public static function fnChnagingTheUserFlag($request) {
		$reviseUserFlag=($request->delflag == 0 ?  1 : 0);
		$updateUserFlag=DB::table('dev_mstuser')
            ->where('id',$request->userid)
            ->update(['delflg' => $reviseUserFlag]);
        return $updateUserFlag;
	}
	public static function passwordchange($request) {
		$update=DB::table('dev_mstuser')
			->where('id', $request->id)
			->update(
				['password' => md5($request->password),
				'conpassword' => md5($request->confirmpassword),
				'Up_DT' => date('Y-m-d'),
				'UP_TM' => date('h:i:s'),
				'UpdatedBy' => Auth::user()->username]
		);
    	return $update;
	}
}