<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class MeetingDetails extends Model {
	public static function fnGetAccountPeriodBK($request) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
						->get();
		return $result;
	}
	public static function fnGetmeetRecord($from_date, $to_date){
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails')
					->select(DB::raw('substr(date, 1, 7) as date'))
					->WHERE('date', '>', $from_date)
					->WHERE('date', '<', $to_date)
					//->WHERE('date', '!=', 0000-00)
					->orderBy('date', 'ASC')
					->get();
		
		$res1 = array();
		foreach ($result as $key => $value) {
			$res1[] = $value->date;
		}			
		return $res1; 
	}
	public static function fnGetmeetRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails')
					->select(DB::raw('substr(date, 1, 7) as date'))
					->WHERE('date', '<=', $from_date)
					->WHERE('date', '!=', "0000-00")
					->orderBy('date', 'ASC')
					->get();
		$res2 = array();
		foreach ($result as $key => $value) {
			if($value->date!="0000-00"){
				$res2[] = $value->date;
			}else{

			}
		}		
		return $res2;
	}
	public static function fnGetmeetRecordNext($to_date) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails')
					->select(DB::raw('substr(date, 1, 7) as date'))
					->WHERE('date', '>=', $to_date)
					//->WHERE('date', '!=', 0000-00)
					->orderBy('date', 'ASC')
					->get();
		$res3 = array();
		foreach ($result as $key => $value) {
			$res3[] = $value->date;
		}		
		return $res3;
	}  
	public static function selectmeetingdetails($year, $month, $request) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails AS m')
					->SELECT('m.*','c.customer_name',
							 'b.branch_name')
					->leftjoin('mst_customerdetail AS c', 'c.customer_id', '=', 
														'm.customerId')
					->leftjoin('mst_branchdetails AS b', 'b.branch_id', '=', 
														'm.branchId')
					->WHERE((DB::raw('substr(m.date, 1, 4)')), '=', $year)
					->WHERE((DB::raw('substr(m.date, 6, 2)')), '=', $month)
					->WHERE('m.delFlg', '=', 0)
					->orderBy('m.date', 'ASC')
					->orderBy('m.startTime', 'ASC')
					->orderBy('m.endTime', 'ASC')
					->paginate($request->plimit);
		return $result;
	}
	public static function selectBymeetingview($request){
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails AS m')
					->SELECT('m.*','c.customer_name',
							 'b.branch_name')
					->leftjoin('mst_customerdetail AS c', 'c.customer_id', '=', 
														'm.customerId')
					->leftjoin('mst_branchdetails AS b', function($join)
                         {
                             $join->on('b.customer_id', '=', 'm.customerId');
                             $join->on('b.branch_id', '=', 'm.branchId');
                         })
					->WHERE('m.id', '=', $request->viewid)
					->get();
		return $result;
	}
	public static function selectcustomer() {
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_customerdetail')
					->select('customer_id','customer_name')
					->get();
		return $result;
	}
	public static function fnGetbranchName($customerid) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_branchdetails')
					->select('*')
					->WHERE('delFlg', '=', 0)
					->WHERE('customer_id', '=', $customerid)
					->get();
		return $result;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'inv_meetingdetails'");
		return $statement[0]->Auto_increment;
	}
	public static function addeditprocess($request) {
		$db = DB::connection('mysql');
		$result= $insert=DB::TABLE('inv_meetingdetails')->insert(
			['date' => $request->date,
			'startTime' => $request->startTime,
			'endTime' => $request->endTime,
			'customerId' => $request->customerId,
			'branchId' => $request->branchId,
			'personName' => $request->personName,
			'Remarks' => $request->Remarks,
			'ins_DateTime' => date('Y-m-d'),
			'createdBy' => Auth::user()->username,
			'delFlg' => 0]
		);
		return $result;
	}
	public static function viewdetails($request) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails AS m')
					->SELECT('m.*','c.customer_name',
							 'b.branch_name')
					->leftjoin('mst_customerdetail AS c', 'c.customer_id', '=', 
														'm.customerId')
					->leftjoin('mst_branchdetails AS b', function($join)
                         {
                             $join->on('b.customer_id', '=', 'm.customerId');
                             $join->on('b.branch_id', '=', 'm.branchId');
                         })
					->WHERE('m.id', '=', $request->editid)
					->get();
		return $result;
	}
	public static function UpdateMeetingDetails($request){
		$db = DB::connection('mysql');
		$update=DB::TABLE('inv_meetingdetails')
			->WHERE('id', $request->editid)
			->update(
				['date' => $request->date,
				'startTime' => $request->startTime,
				'endTime' => $request->endTime,
				'customerId' => $request->customerId,
				'branchId' => $request->branchId,
				'personName' => $request->personName,
				'Remarks' => $request->Remarks,
				'up_DateTime' => date('Y-m-d'),
				'updatedBy' => Auth::user()->username,
				'delFlg' => 0]
		);
    	return $update;
	}
	public static function gethistory($request){
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails AS m')
					->SELECT('m.*','c.customer_name',
							 'b.branch_name')
					->leftjoin('mst_customerdetail AS c', 'c.customer_id', '=', 
														'm.customerId')
					->leftjoin('mst_branchdetails AS b', 'b.branch_id', '=', 
														'm.branchId')
					->WHERE('c.customer_name', '=', $request->customer_name)
					->orderBy('m.date', 'ASC')
					->paginate($request->plimit);
		return $result;
	}
	public static function customerno($request){
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_customerdetail')
					->select('customer_id')
					->orderBy('customer_id', 'DESC')
					->get();
		return $result;
	}
	public static function insert($request, $customer_id){
		$db = DB::connection('mysql');
		$result= $insert=DB::TABLE('mst_customerdetail')
				->insertGetId(['customer_id' => $customer_id,
						  'customer_name' => $request->customer_name,
						  'delflg' => 0]);
		return $result;
	}
	public static function branchinsertnew($request, $customer_id, $branchid) {
		$db = DB::connection('mysql');
		$result= $insert=DB::TABLE('mst_branchdetails')
							->insertGetId(['customer_id' => $customer_id,
									  'branch_id' => $branchid,
									  'branch_name' => $request->branch_name,
									  'delflg' => 0]);
		return $result;
	}
	public static function getLastInsertedCusdetails($id){
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_customerdetail')
					->select('customer_id','customer_name')
					->WHERE('id', '=', $id)
					->get();
		return $result;
	}
	public static function getLastInsertedBrchdetails($id){
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_branchdetails')
					->select('branch_id', 'branch_name')
					->WHERE('id', '=', $id)
					->get();
		return $result;
	}
	public static function fnGetcustnamecheck($customer_name){
		$custname=trim(iconv(mb_detect_encoding($customer_name), 'UTF-8', $customer_name));
		$db = DB::connection('mysql');
		$result= $db->TABLE('mst_customerdetail')
					->select('customer_name')
					->WHERE('customer_name', '=', $customer_name)
					->get();
		return $result;
	}

	public static function fnGetmeetingtimeingcheck($request) {
		$db = DB::connection('mysql');
		$result= $db->TABLE('inv_meetingdetails')
					->select('*')
					->WHERE('date', '=', $request->txt_date)
					->WHERE( function($joincont) use($request) {
                     	$joincont->whereBetween('startTime',
                     							[$request->starttime, $request->endtime])
                             	 ->orwhereBetween('endTime', 
                             					[$request->starttime, $request->endtime])
                             	 ->orwhere([['startTime', '<', $request->starttime],
                             					['endTime', '>=', $request->endtime]]);
                    });
		if($request->editflg == 'edit'){
			$result	= $result->WHERE('id','!=', $request->editid)->get();
		} else {
			$result = $result->get();
		}
		return $result;
	}
}