<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use Input;
use Auth;
class Forgetpassword extends Model {
	public static function getValidUserID($request) {
		$db = DB::connection('mysql');
		if (strlen($request->email) <= 10) {
			$userid = "userid";
		} else {
			$userid = "email";
		}
		$sql = $db->table('dev_mstuser')
				->SELECT('userid',DB::raw('COUNT(*) AS total'))
                ->WHERE($userid, '=', $request->email)
				->get();
		return $sql;
	}
    public static function updatepass($password,$userid,$request){
	    $db = DB::connection('mysql');
		$update=$db->table('dev_mstuser')
            ->where('userid', $userid)
            ->update(
            ['password' => md5($password)]
          	);
    	return $update;
	}
	public  static function getMailId($request){
		$content = "
Your password has been updated.

Your Login Details:

AAAA

Thank And Regards,
Admin


Note: Please Dont reply to this mail.";
return $content;
	}
	public  static function getMailemppersonId($userid,$request){
	    $db = DB::connection('mysql');
		$sql = $db->table('dev_mstuser')
				->SELECT('*')
				->where('userid',$userid)
				->get();
		return $sql;
	}
	public static function getMailContentemp($oldid,$password){
		$content="";
		$content.="\r\n EmployeeID   :".$oldid;
		$content.="\r\n NewPassword  :".$password;
		return $content;
	}
}