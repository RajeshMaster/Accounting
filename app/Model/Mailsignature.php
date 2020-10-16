<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Mailsignature extends Model {
	public static function fnfetchmailsignature($request) {
		$result= DB::table('mailsignature')
						->SELECT('mailsignature.*','user.nickName','user.usercode','user.givenname','user.username')
						->LEFTJOIN('dev_mstuser as user','user.usercode','=','mailsignature.user_ID')
						//->WHERE('delflg', '=', 0)
						->orderBy('signId', 'ASC')
					  	->paginate($request->plimit);
		return $result;
	}
	public static function fnGetUserDetails($request) {
		$query= DB::table('dev_mstuser')
					->SELECT('usercode','userid','username','givenname','nickName')
					->WHERE('delflg', '=', 0)
					->orderBy('usercode', 'ASC')
				  	->get();
		return $query;
	}
	public static function signidprocess() {
		$query = DB::select("SELECT CONCAT('SIGN', LPAD(MAX(SUBSTRING(signID,5))+1,5,0)) AS signid FROM mailsignature WHERE signID LIKE '%SIGN%'");
		return $query;
	}
	public static function fninsertmailsignature($request,$signatureId,$userid) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$insert=DB::table('mailsignature')
		->insert(
			[
			'signID' => $signatureId,
			'user_ID' => $userid,
			'content' => $request->content,
			'delFlg' => 0,
			'Ins_DT' => date('Y-m-d'),
			'UP_DT' => date('Y-m-d'),
			'createdBy' => $name,
			'updatedBy' => $name]);
	return $insert;
	}
	public static function fnfetchupdatedata($request) {
		$result= DB::table('mailsignature')
						->SELECT('mailsignature.*','user.nickName','user.usercode','user.givenname','user.username')
						->LEFTJOIN('dev_mstuser as user','user.usercode','=','mailsignature.user_ID')
						->WHERE('signID', '=', $request->id)
						->get();
		return $result;
	}
	public static function fnfetchviewMailsignature($request) {
		$query= DB::table('mailsignature')
						->SELECT('mailsignature.*','user.nickName','user.usercode','user.givenname','user.username')
						->LEFTJOIN('dev_mstuser as user','user.usercode','=','mailsignature.user_ID')
						->WHERE('signID', '=', $request->signid)
					  	->get();
		return $query;
	}
	public static function fnfetchviewdata($id) {
		$query= DB::table('mailsignature')
						->SELECT('*')
						->WHERE('user_ID', '=', $id)
					  	->get();
		return $query;
	}
	public static function fnupdatemailsignature($request,$id) {
		if($id != ""){
			 $request->id = $id;
		} else {
			 $request->id = $request->id;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update=DB::table('mailsignature')
				->where('signID', $request->id)
				->update(
				[
				'content' => $request->content,
				'delFlg' => 0,
				'UP_DT' => date('Y-m-d'),
				'updatedBy' => $name]);
	return $update;
	}
	public static function fnfetchlastmaid() {
		$result=DB::table('mailsignature')
				->SELECT('*')
					->max('signID');
		return $result;
	}
	public static function fnfetchmailsigdata($request) {
		$query= DB::table('mailsignature')
						->SELECT('user_ID',
								'content')
						->WHERE('user_ID', '=', $request->userid)
					  	->get();
		return $query;
	}
}
