<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class Ourdetail extends Model {
	public static function viewdetails($request) {
		$db = DB::connection('mysql');
		$result= $db->table('dev_ourdetails')
						->SELECT('*')
						->get();
		return $result;
	}
	public static function viewtaxdetails($request) {
		$db = DB::connection('mysql');
		$result= $db->table('dev_taxdetails')
						->SELECT('*')
						->orderBy('id','ASC')
						->get();
		return $result;
	}
	public static function viewkessandetails() {
		$db = DB::connection('mysql');
		$result= $db->table('dev_kessandetails')
						->SELECT('*')
						->orderBy('id','ASC')
						->get();
		return $result;
	}
	public static function UpdateuserReg($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$post = $request->txt_pincode1.'-'.$request->txt_pincode2;
		$tel = $request->Tel1.'-'.$request->Tel2.'-'.$request->Tel3;
		$fax = $request->fax1.'-'.$request->fax2.'-'.$request->fax3;
		$update=DB::table('dev_ourdetails')
			->where('id', $request->editid)
			->update(
				['CompanyName' => $request->txt_cmyname,
				'CompanyNamekana' => $request->txt_kananame,
				'pincode' => $post,
				'Prefecturename' => $request->txt_prefectname,
				'Streetaddress' => $request->txt_jpaddress,
				'BuildingName' => $request->txt_buildingname,
				'TEL' => $tel,
				'FAX' => $fax,
				'Commonmail' => $request->txt_commonmail,
				'URL' => $request->txt_websiteurl,
				'Establisheddate' => $request->txt_establishdate,
				'Closingmonth' => $request->txt_clsmonth,
				'Closingdate' => $request->txt_clsdate,
				'systemname' => $request->txt_systemname,
				'delflg' => 0,
				'UP_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function insertuserRec($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$post = $request->txt_pincode1.'-'.$request->txt_pincode2;
		$tel = $request->Tel1.'-'.$request->Tel2.'-'.$request->Tel3;
		$fax = $request->fax1.'-'.$request->fax2.'-'.$request->fax3;
		$insert=DB::table('dev_ourdetails')
			->insert(
				['CompanyName' => $request->txt_cmyname,
				'CompanyNamekana' => $request->txt_kananame,
				'pincode' => $post,
				'Prefecturename' => $request->txt_prefectname,
				'Streetaddress' => $request->txt_jpaddress,
				'BuildingName' => $request->txt_buildingname,
				'TEL' => $tel,
				'FAX' => $fax,
				'Commonmail' => $request->txt_commonmail,
				'URL' => $request->txt_websiteurl,
				'Establisheddate' => $request->txt_establishdate,
				'Closingmonth' => $request->txt_clsmonth,
				'Closingdate' => $request->txt_clsdate,
				'systemname' => $request->txt_systemname,
				'delflg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Ins_TM' => date('h:i:s'),
				'CreatedBy' => $name,
				'Up_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function inserttaxRec($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$insert=DB::table('dev_taxdetails')
			->insert(
				['Tax' => $request->txt_tax,
				'Startdate' => $request->txt_startdate,
				'delflg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Ins_TM' => date('h:i:s'),
				'CreatedBy' => $name,
				'Up_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function balanceedit($request) {
		$db = DB::connection('mysql');
		$result= $db->table('dev_kessandetails')
						->SELECT('*')
						->where('id', $request->balid)
						->get();
		return $result;
	}
	public static function UpdatebalReg($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update=DB::table('dev_kessandetails')
			->where('id', $request->balid)
			->update(
				['Accountperiod' => $request->period,
				'Startingyear' => $request->startyear,
				'Startingmonth' => $request->startmonth,
				'Closingyear' => $request->endyear,
				'Closingmonth' => $request->endmonth,
				'delflg' => 0,
				'UP_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function insertbalRec($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$insert=DB::table('dev_kessandetails')
			->insert(
				['Accountperiod' => $request->period,
				'Startingyear' => $request->startyear,
				'Startingmonth' => $request->startmonth,
				'Closingyear' => $request->endyear,
				'Closingmonth' => $request->endmonth,
				'delflg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Ins_TM' => date('h:i:s'),
				'CreatedBy' => $name,
				'UP_DT' => date('Y-m-d'),
				'Up_TM' => date('h:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
}