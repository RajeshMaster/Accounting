<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Mailcontent extends Model {
	public static function fnfetchmailcontent($request) {
		$result= DB::table('mailContent')
						->SELECT('mailContent.*','mailType.typeName')
						->leftjoin('mailType', 'mailContent.mailType', '=', 'mailType.id')
						->WHERE('mailContent.delFlg', '=', 0)
						->orderBy('mailContent.mailId', 'ASC')
					  	->paginate($request->plimit);
		return $result;
	}
	public static function fnfetchmailtypes($request) {
		$result= DB::table('mailType')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->lists('typeName','id');
			return $result;
	}
	public static function fnupdatedefaults($request) {
		$upde=DB::table('mailContent')
			->update(['defaultMail' => 0]);

		$update=DB::table('mailContent')
			->where('id', $request->eid)
			->update(['defaultMail' => 1]);
			return $update;
	}
	public static function fnfetchupdatedata($request) {
		$result= DB::table('mailContent')
						->SELECT('*')
						->WHERE('id', '=', $request->emailid)
						->get();
		return $result;
	}
	public static function fnfetchlastmailtypeid() {
		$result=DB::table('mailType')
					->SELECT('*')
						->max('id');
			return $result;
	}
	public static function fnfetchmailid() {
		$cmn="MAIL";
		$result=DB::table('mailContent')
					->SELECT('mailId')
						->orderBy('mailId', 'DESC')
						->limit(1)
						->get();
		if (count($result) == 0) {
			$id = $cmn . "0001";
		} else {
			foreach ($result as $key => $value) {
				$g_id = intval(str_replace("MAIL", "", $value->mailId)) + 1;
				$id = $cmn . substr("0000" . $g_id, -4);
			}
			return $id;
		}
	}
	public static function fninsertnewmailtype($request) {
			$name = Session::get('FirstName').' '.Session::get('LastName');
			$insert=DB::table('mailType')
			->insert(
				[
				'typeName' => $request->mailother,
				'delFlg' => 0,
				'createdDate' => date('Y-m-d'),
				'updatedDate' => date('Y-m-d'),
				'createdBy' => $name,
				'updatedBy' => $name]
		);
		return $insert;
	}
	public static function fninsertnewmailcontent($request,$newid,$other) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
			if ($request->mailtype == 999) {
				$mailtype = $other;
			} else {
				$mailtype = $request->mailtype;
			}
			if (!empty($request->subject)) {
				$subject=$request->subject;
			} else {
				$subject="";
			}
			$insert=DB::table('mailContent')
			->insert(
				[
				'mailId' => $newid,
				'mailName' => $request->mailname,
				'mailType' => $mailtype,
				'content' => $request->content,
				'subject' => $subject,
				'defaultMail' => 0,
				'delFlg' => 0,
				'createdDate' => date('Y-m-d'),
				'updatedDate' => date('Y-m-d'),
				'createdBy' => $name,
				'updatedBy' => $name]);
		return $insert;
	}
	public static function fnupdatenewmailcontent($request,$other) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
			if ($request->mailtype == 999) {
				$mailtype = $other;
			} else {
				$mailtype = $request->mailtype;
			}
			if (!empty($request->subject)) {
				$subject=$request->subject;
			} else {
				$subject="";
			}
			$update=DB::table('mailContent')
            ->where('id', $request->emailid)
            ->update(
            	[
				'mailName' => $request->mailname,
				'mailType' => $mailtype,
				'content' => $request->content,
				'subject' => $subject,
				'defaultMail' => 0,
				'delFlg' => 0,
				'updatedDate' => date('Y-m-d'),
				'updatedBy' => $name]);
    	return $update;
	}
	public static function fnfetchviewmailcontent($request) {
		$result= DB::table('mailContent')
						->SELECT('mailContent.*','mailType.typeName')
						->leftjoin('mailType', 'mailContent.mailType', '=', 'mailType.id')
						->WHERE('mailContent.id', '=', $request->emailid)
						->orderBy('mailId', 'ASC')
					  	->get();
		return $result;
	}
	public static function fnfetchlastmaid() {
		$result=DB::table('mailContent')
					->SELECT('*')
						->max('id');
			return $result;
	}
}
?>