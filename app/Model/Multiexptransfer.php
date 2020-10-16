<?php 
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
Class Multiexptransfer extends Model {
	public static function fnGetSubject($request) {
			if(Session::get('languageval') == "jp") {
				$selectedField = "Subject_jp";
			} else {
				$selectedField = "Subject";
			}
			$accperiod=DB::table('dev_expensesetting')
						->SELECT('id',$selectedField)
						->where('delflg', '=', 0)
            			->where($selectedField, '!=', "")
	                    ->lists($selectedField,'id');
	        return $accperiod;
	}
	public static function fnfetchsubsubject($request) {
		if ($request->cashid == '3') {
			$tblname = 'inv_set_transfersub';
		} else {
			$tblname = 'inv_set_expensesub';
		}
		if(Session::get('languageval') == "jp") {
			$selectedField = "sub_jap";
		} else {
			$selectedField = "sub_eng";
		}
		$accperiod=DB::table($tblname)
					->SELECT('id',$selectedField,'mainid')
					->where('delflg',0)
        			->where('mainid',$request->mainid)
                    ->get();
        return $accperiod;
	}
	public static function fetchbankname($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbank as ban')
						->SELECT(DB::RAW("CONCAT(mstbanks.BankName,'-',ban.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbanks.id,'-',ban.AccNo) AS ID"))
						->Join('mstbanks', 'mstbanks.id', '=', 'ban.BankName')
						->lists('BANKNAME','ID');
		return $query;
	}
	public static function getpettymainsubject($request) {
			if(Session::get('languageval') == "jp") {
				$selectedField = "main_jap";
			} else {
				$selectedField = "main_eng";
			}
			$accperiod=DB::table('inv_set_transfermain')
						->SELECT('id',$selectedField)
						->where('delflg', '=', 0)
            			->where($selectedField, '!=', "")
	                    ->lists($selectedField,'id');
	        return $accperiod;
	}
	public static function fnpettysubsubject($request) {
		if ($request->cashid == '3') {
			$tblname = 'inv_set_transfersub';
		} else {
			$tblname = 'inv_set_expensesub';
		}
		if(Session::get('languageval') == "jp") {
			$selectedField = "sub_jap";
		} else {
			$selectedField = "sub_eng";
		}
		$accperiod=DB::table($tblname)
					->SELECT('id',$selectedField,'mainid')
					->where('delflg',0)
        			->where('mainid',$request->mainid)
                    ->get();
        return $accperiod;
	}
}