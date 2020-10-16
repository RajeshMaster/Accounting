<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
/*
Class: About

Some functions related to display the users list and describing their particular details.
*/
class Mailstatus extends Model {
	/**
	*
	* Get All User Data
	* @author Revathi.E
	* @return Object to particular view page
	* Updated At 2017/11/06
	*
	*/
	public static function getallmailstatus($request) {
		$result = db::table('mailStatus')
						->select('mailStatus.*','mst_customerdetail.customer_name')
						->leftJoin('mst_customerdetail' , 'mst_customerdetail.id' ,'=','mailStatus.companyId');
						if ($request->customerid  != "") {
							$result = $result->WHERE('mailStatus.sendFlg','=',$request->historyfilter)
											->WHERE('mailStatus.companyId','=',$request->customerid);
										$result = $result->orderby('mailStatus.id','DESC')
														->paginate($request->plimit);
						} else {
							$result = $result	->WHERE('mailStatus.sendFlg',$request->sendfilter)
												->WHERE('mailStatus.delFlg',0);
									$result = $result->orderby('mailStatus.id','DESC')
								->paginate($request->plimit);
						}
						// $result = $result->tosql();
						// dd($result);
		return $result;
	}
	public static function getsinglemailstatus($request) {
		$result = db::table('mailStatus')
						->select('mailStatus.*','mst_customerdetail.customer_name')
						->leftJoin('mst_customerdetail' , 'mst_customerdetail.id' ,'=','mailStatus.companyId')
						->WHERE('mailStatus.delFlg',0)
						->WHERE('mailStatus.id','=',$request->statusid)
						->get();
		return $result;
	}
}