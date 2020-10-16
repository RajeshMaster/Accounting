<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class Bank extends Model {
	public static function index($request) {
		if (empty($request->filterval)) {
			if (empty($request->filterval)) {
				$request->filterval = 1;
						  } else { 
						$request->filterval = $request->filterval;
						  }
					 }
		$db = DB::connection('mysql');
		$query= $db->table('mstbank')
						->SELECT('*')
							->where('user_id','!=','""')
						  ->where('delflg','=',0);
						  if($request->filterval == 1){
								$query= $query ->where('location','=',2);
						  }else {
								$query= $query ->where('location','=',1);
						  }
						$query= $query->orderBy('mainFlg','DESC')
                                      ->orderBy('FirstName','ASC')
							  ->paginate($request->plimit);
		return $query;
	}
	public static function selectbankName($request=null,$bankid=null){
					 $db = DB::connection('mysql');
			  $query = $db->table('mstbanks')
						->select('*')
						  ->where('id','=',$bankid)
						  ->get();
				return $query;  
	 }
	 public static function selectbranchname($request=null,$branchid=null){
			  $db = DB::connection('mysql');
			  $query = $db->table('mstbankbranch')
						->select('*')
						  ->where('id','=',$branchid)
						  ->where('delflg','=',0)
						  ->get();
		  return $query; 
	 }
	 public static function singledetailview($request=null){
			  $db = DB::connection('mysql');
			  $query = $db->table('mstbank')
                              ->SELECT('mstbank.user_id',
                              			'mstbank.FirstName',
                              			'mstbank.AccNo',
                              			'mstbank.Location',
                                        'mstbank.BranchName',
                              			'mstbank.BranchNo',
                              			'mstbank.Type',
                              			'mstbanks.id',
										'mstbanks.BankName' ,
										'mstbankbranch.BranchNo' ,
                                        'mstbankbranch.BranchName',
										'mstbank.Bank_NickName')
                              ->LEFTJOIN('mstbanks', 'mstbanks.id' ,'=','mstbank.BankName')
                              ->LEFTJOIN('mstbankbranch', 'mstbankbranch.id' ,'=','mstbank.BranchName')
                              ->where('mstbank.id', '=', $request->id)
                              ->get();
		  return $query; 
	 }
	 public static function selectByMainflg($request=null){
        if ($request->mainFlg == 0) {
            $mainFlg = 1;
        } else if ($request->mainFlg == 1) {
            $mainFlg = 0;
        }
		$db = DB::connection('mysql');
		$query= $db->table('mstbank')
					->where('location', $request->loc)
                    ->where('id','=', $request->sid)
					->update(['mainFlg' => $mainFlg]);
		return $query;
	}
	public static function selectByallMainflg($request=null){
			$db = DB::connection('mysql');
		$allupdatequery= $db->table('mstbank')
				->where('id','!=', $request->sid)
				->where('Location', $request->loc)
  				->update(['mainFlg' => 0]);
			return $allupdatequery;
	}
	public static function selectbankaccNo($request=null) { 
		$db = DB::connection('mysql');
        $tbl_name = "mstbanks";
        $query = $db->table($tbl_name)
                    ->select('*')
                    ->where('delflg', 0)
                    ->where('location',$request->loc)
                    ->get();
            return $query;
     }
     public static function insertBankname($request,$orderid) { 
       // print_r($request->loc);exit();
        $db = DB::connection('mysql');
        $tbl_name = "mstbanks";
        $insert=$db->table($tbl_name)
                 ->insertGetId([
                 'BankName' =>$request->bnkname,
                 'location' => $request->loc,
                 'romaji' => $request->romaji,
                 'delflg' => 0,
                 'Ins_DT' => date('Ymd'),
                 //'Up_DT' => date('Ymd'),
                 'Ins_TM' => date('His'),
                 //'Up_TM' => date('His'),
                 'CreatedBy' => Auth::user()->username,
                // 'UpdatedBy' => Auth::user()->username,
                 ]
              );
        return  $insert;
    }
     public static function fetchBankname($request) {
        $db = DB::connection('mysql');
        $tbl_name = "mstbanks";
        $query = $db->table($tbl_name)
                    ->select('*')
                    ->where('id', $request->bankuid)
                    ->get();
            return $query;
     }
      public static function mstbankbranch($request) { 
        $db = DB::connection('mysql');
        $tbl_name = "mstbankbranch";
        $query = $db->table($tbl_name)
                    ->select('*')
                    ->where('BankId', $request->bankuid)
                    ->get();
            return $query;
     }
     public static function Orderidgenerate($request) {
        $db = DB::connection('mysql');
            $tbl_name = "mstbanks";
        $query = $db->TABLE($tbl_name)->max('id');
        return $query;            
     }
     public static function Ordergeneration($request) {
            $db = DB::connection('mysql');
            $tbl_name = "mstbankbranch";
        $query = $db->TABLE($tbl_name)->max('id');
        return $query;  
     }
     public static function insertBranchname($request,$orderid) {
        $db = DB::connection('mysql');
        $tbl_name = "mstbankbranch";
        $insert=$db->table($tbl_name)
                 ->insertGetId([
                 'BankId' =>$request->bankuid,
                 'BranchName' => $request->branchs,
                 'BranchNo' => $request->bno,
                 'delflg' => 0,
                 'Ins_DT' => date('Ymd'),
                 //'Up_DT' => date('Ymd'),
                 'Ins_TM' => date('His'),
                 //'Up_TM' => date('His'),
                 'CreatedBy' => Auth::user()->username,
                // 'UpdatedBy' => Auth::user()->username,
                 ]
              );
        return  $insert;
    }
    public static function getJapanAccount() {
    	return array('1'=>$msg = "普通");
	}
 	public static function getIndianAccount() {
    	return array('1'=>$msg = "Saving",//'2'=>$msg = constant("Current"),
                 '2'=>$msg = "Others");
	}
	public static function insertRec($request,$mainFlg,$branchid) {
		if($request->nation=="2") {
            $type=1;
        }
        if($request->nation=="1") {
                $type=$request->type;
        }
		$insert=DB::table('mstbank')->insert([
			'id' => '',
            'user_id' => Session::get('userid'),
			'AccNo' => $request->txt_accnumber,
             'FirstName' => $request->txt_kananame,
             'Ins_DT' => date('Y-m-d'),
			'Ins_TM' => date('h:i:s'),
			'CreatedBy' => Auth::user()->username,
           // 'Up_DT' => date('Y-m-d'),
           // 'UP_TM' => date('h:i:s'),
            //'UpdatedBy' => Auth::user()->username,
             'Location' => $request->nation,
             'BankName'=> $request->bankuid,
             'BranchName'=> $branchid,
             'Bank_NickName' => $request->txt_nickname,
             'BranchNo'=>$branchid,
             'Type'=> $type,
             'delflg'=> 0,
             'mainFlg'=> $mainFlg,
			]);
	}
	 public static function bankcount($location,$request) { 
            $db =DB::connection('mysql');
        $tbl_name = "mstbank";
        $query= $db->table($tbl_name)
                   ->select('*')
                   ->where('Location','=', $location)
                   ->get();
        return $query;
     }
      public static function getempbankdetails($request) {
            $db = DB::connection('mysql');
            $tbl_name = "mstbank";
           $query = $db->table($tbl_name);
           $query= $query
                    ->select('mstbank.FirstName AS txt_kananame', 
                             'mstbank.AccNo AS txt_accnumber',
                             'mstbank.Location',
                             'mstbank.BranchNo',
                             'mstbank.BranchName',
                             'mstbank.Type AS type', 
                             'mstbanks.id', 
                             'mstbanks.BankName AS bankname',
                             'mstbankbranch.BranchName AS branchname', 
                             'mstbankbranch.BranchNo AS branchno', 
                             'mstbank.BankName As mstbankname',
                             'mstbank.BranchName As mstbranchname',
                             'mstbank.BranchNo As mstbranchno',
                             'mstbank.Bank_NickName As txt_nickname'
                             )
                    ->leftJoin('mstbanks','mstbanks.id', '=','mstbank.BankName')
                    ->leftJoin('mstbankbranch','mstbankbranch.id', '=','mstbank.BranchName')
                    ->where('mstbank.id','=',$request->id)
                    ->get();
                     // ->toSql();
            // dd($query);
                    //print_r($query);
            return $query;                 
    }
     public static function updaterec($request) { 
        $db = DB::connection('mysql');
        $tbl_name = "mstbank";
        if($request->nation=="2") {
            $type=1;
        }
        if($request->nation=="1") {
              $type=$request->type;
        }
        if (empty($request->branid)) {
            $branch = $request->branchuid;
        } else {
            $branch = $request->branid;
        }
        $allupdatequery= $db->table($tbl_name)
                    ->where('id', $request->editid)
                    ->update(['AccNo' => $request->txt_accnumber,
                             'FirstName' => $request->txt_kananame,
                             'Up_DT' => date('Ymd'),
                             'UP_TM' => date('His'),
                             'UpdatedBy' => Auth::user()->username,
                             'Location' => $request->nation,
                             'BankName'=> $request->bankuid,
                             'Type' => $type,
                             'BranchName'=> $branch,
                             'BranchNo'=> $request->txt_branchnumber,
                             'Bank_NickName'=>$request->txt_nickname]);
          return $allupdatequery;
     }
     public static function fetchbankid($request) {
        $db = DB::connection('mysql');
        $tbl_name = "mstbankbranch";
                $query= $db->table($tbl_name)
                   ->select('id')
                   ->where('BankId','=', $request->bankuid)
                   ->get();
        return $query;
    }
    public static function fetchmaxid($request) {
        $db = DB::connection('mysql');
        $latDetails = $db->table('mstbank')
                           ->max('id');
            return $latDetails;
    }
}