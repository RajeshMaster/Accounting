<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
use Config;

class Staff extends Model {
	public static function fnGetEmployeeDetails($request, $resignid,$title){
	  	$db = DB::connection('mysql');
	  	$query = $db->table('emp_mstemployees')
					 ->select('*')
					 ->where([['delFlg', '=', 0],
					 		  ['resign_id', '=', $resignid],
					 		  ['Emp_ID', 'NOT LIKE', '%NST%']]);
	  	if($resignid == 0){
			$query = $query->where('Title', '=', $title);
		}
		if ($request->searchmethod == 1) {
			$query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('Emp_ID', 'LIKE', '%' . $request->singlesearch . '%')
                                    		 ->orwhere('nickname', 'LIKE', '%' . $request->singlesearch . '%');
                            });
		} elseif ($request->searchmethod == 2) {
			$query = $query->where(function($joincont) use ($request) {
                                $joincont->where([['Emp_ID', 'LIKE', '%' . $request->employeeno . '%'],
                                				 ['nickname', 'LIKE', '%' . $request->employeename . '%'],
                                				 ['DOJ', ' >= ', $request->startdate,' AND ',
                                				 										$request->enddate]]);
                            });
		}	 		  
			$query = $query	 ->orderBy($request->staffsort, $request->sortOrder)
					  		 ->paginate($request->plimit);
					  		 // ->tosql();
					  		 // dd($query);
		//print_r($query);exit();
			return $query;
	}
	public static function fngetjapanaddress($address) {
		$query = DB::table('mstaddress')
					->select(DB::raw("CONCAT('〒',pincode,jpstate,jpaddress,roomno,'号') AS address"))
					->where('id', '=', $address)
					->get();
		return $query;
	}
	public static function GetAvgage($resignid) {
		$sql = "SELECT AVG(YEAR(CURDATE()) - YEAR(dob) - (RIGHT(CURDATE(), 5) < RIGHT(dob, 5))) as avg_age FROM emp_mstemployees
		WHERE resign_id='$resignid' AND delFLg=0 AND Title = 2";
		$query = DB::SELECT($sql);
		return $query;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'emp_mstemployees'");
		return $statement[0]->Auto_increment;
	}

	public static function fnGetstaffDetail($request){
		if (!empty($request->viewid)) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees')
					->select('*')
					->leftJoin('mstaddress AS mst', 'mst.id', '=', 'emp_mstemployees.Address1')
					->where([['Emp_ID', '=', $request->viewid]])
					->get();
		} else {
			$query = "";
		}
		return $query;
	}
	public static function addeditprocess($request, $filename) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$result= $insert=DB::table('emp_mstemployees')->insert(
			['id' => '',
			'Emp_ID' => $request->EmployeeId,
			'DOJ' => $request->OpenDate,
			'FirstName' => $request->Surname,
			'LastName' => $request->Name,
			'nickname' => $request->NinkName,
			'Gender' => $request->Gender,
			'DOB' => $request->DateofBirth,
			'Mobile1' => $request->MobileNo,
			'Emailpersonal' => $request->Email,
			'Picture' => $filename,
			'Address1' => $request->StreetAddress,
			'BankName' => $request->BankName,
			'BranchName' => $request->BranchName,
			'AccNo' => $request->AccNo,
			'Ins_DT' => date('Y-m-d'),
			'Ins_TM' => date('h:i:s'),
			'CreatedBy' => $name,
			'Up_DT' => date('Y-m-d'),
			'Up_TM' => date('h:i:s'),
			'Title' => 2,
			'resign_id' => 0,
			'delflg' => 0]
		);
		return $result;
	}
	public static function viewdetails($id) {
		$db = DB::connection('mysql');
		$result= DB::table('emp_mstemployees')
						->SELECT('*')
						->leftJoin('mstaddress AS mst', 'mst.id', '=', 'emp_mstemployees.Address1')
						->WHERE('Emp_ID', '=', $id)
						->get();
		return $result;
	}
	public static function updateprocess($request, $imagename) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$update=DB::table('emp_mstemployees')
		->where('Emp_ID', $request->hdnempid)
		->update(
			[
			'DOJ' => $request->OpenDate,
			'FirstName' => $request->Surname,
			'LastName' => $request->Name,
			'nickname' => $request->NinkName,
			'Gender' => $request->Gender,
			'DOB' => $request->DateofBirth,
			'Mobile1' => $request->MobileNo,
			'Emailpersonal' => $request->Email,
			'Picture' =>  $imagename,
			'Address1' => $request->StreetAddress,
			'BankName' => $request->BankName,
			'BranchName' => $request->BranchName,
			'AccNo' => $request->AccountNo,
			'BranchNo' => $request->BranchNo,
			'Up_DT' => date('Y-m-d'),
			'Up_TM' => date('h:i:s'),
			'UpdatedBy' => $name]
		);
		return $update;
	}
	public static function fnOldDbDetails(){
		$db = DB::connection('mysql');
		$result= DB::table('olddbdetailsregistration')
						->SELECT('*')
						->WHERE('Delflg', '=', 0)
						->lists('DBName','id');
		return $result;
	}
	public static function fnGetConnectionQuery($request){
		// print_r($request->contentsel);exit();
		$db = DB::connection('mysql');
		$query= DB::table('olddbdetailsregistration')
						->SELECT('*')
						 ->where([['Delflg', '=', 0],
					 		     ['id', '=', $request->contentsel]])
						->get();
		return $query;
	} 
	public static function fnGetEmployeeDetailsMB() {
		$db = DB::connection('otherdb');
		$query= $db->table('emp_mstemployees as emp')
						->SELECT('emp.Emp_ID','emp.DOJ','emp.FirstName','emp.LastName','emp.KanaFirstName',
							'emp.KanaLastName','emp.Gender','emp.DOB','emp.Mobile1','emp.Emailpersonal',
							'emp.Picture','emp.Address1','emp.Ins_DT','emp.Ins_TM','emp.CreatedBy',
							'emp.resign_id','emp.Title','emp.delFlg','emp.UpdatedBy','emp.Up_DT','emp.Up_TM')
						->where('emp.delFlg', '=', 0)
					 	->where('emp.Title', '=', 2)
					 	->orWhere('emp.Title', '=', 3)
						->get();
		return $query;
	}
	public static function selectaccNo($userid) {
		$db = DB::connection('otherdb');
		$query= $db->table('mstbank')
						->SELECT('*')
						->where('delflg', '=', 0)
					 	->where('location', '=', 2)
					 	->Where('mainFlg', '=', 1)
						->where('user_id', '=', $userid)
					 	->GROUPBY('user_id')
						->get();
		return $query;
	}
	public static function selectbankName($bankid) {
		$db = DB::connection('otherdb');
		$query= $db->table('mstbanks')
						->SELECT('*')
						->where('delflg', '=', 0)
						->where('id', '=', $bankid)
						->get();
		return $query;
	}
	public static function selectbranchname($branchid) {
		$db = DB::connection('otherdb');
		$query= $db->table('mstbankbranch')
						->SELECT('*')
						->where('delflg', '=', 0)
						->where('id', '=', $branchid)
						->get();
		return $query;
	}
	public static function fnGetAddressMB($address) {
		print_r($address);exit();
		$sql= DB::table('mstaddress')
						->SELECT('*')
						->WHERE('id', '=', $address)
						->limit(1)
						->get();
		print_r($sql);exit();

	}
	public static function fnOldTempstaffExist($empid) {
		$sql= DB::table('emp_mstemployees')
						->SELECT('*')
						->WHERE('Emp_Id', '=', $empid)
						->get();
		return $sql;
	}
	public static function fnInsertOLDMBDetails($fldarray, $valuearray) {
		$db = DB::connection('mysql');
		$result= $insert=DB::table('emp_mstemployees')->insert(
			[$fldarray[0] => $valuearray[0],
			$fldarray[1] => $valuearray[1],
			$fldarray[2] => $valuearray[2],
			$fldarray[3] => $valuearray[3],
			$fldarray[4] => $valuearray[4],
			$fldarray[5] => $valuearray[5],
			$fldarray[6] => $valuearray[6],
			$fldarray[7] => $valuearray[7],
			$fldarray[8] => $valuearray[8],
			$fldarray[9] => $valuearray[9],
			$fldarray[10] => $valuearray[10],
			$fldarray[11] => $valuearray[11],
			$fldarray[12] => $valuearray[12],
			$fldarray[13] => $valuearray[13],
			$fldarray[14] => $valuearray[14],
			$fldarray[15] => $valuearray[15],
			$fldarray[16] => $valuearray[16],
			$fldarray[17] => $valuearray[17],
			$fldarray[18] => $valuearray[18],
			$fldarray[19] => $valuearray[19],
			$fldarray[20] => $valuearray[20],
			$fldarray[21] => $valuearray[21],
			$fldarray[22] => $valuearray[22],
			$fldarray[23] => $valuearray[23],
			$fldarray[24] => $valuearray[24],
			]
		);
		return $result;
	}
	//Already datas  are updated in the old database
	public static function fnUpdateOLDMBDetails($fldarray, $valuearray, $tempvar) {
		// print_r($fldarray);
		// print_r('</br>');
		// print_r($valuearray);
		//exit();
		$db = DB::connection('mysql');
		$update=DB::table('emp_mstemployees')
		->where('Emp_ID', $tempvar)
		->update(
			[$fldarray[0] => $valuearray[0],
			$fldarray[1] => $valuearray[1],
			$fldarray[2] => $valuearray[2],
			$fldarray[3] => $valuearray[3],
			$fldarray[4] => $valuearray[4],
			$fldarray[5] => $valuearray[5],
			$fldarray[6] => $valuearray[6],
			$fldarray[7] => $valuearray[7],
			$fldarray[8] => $valuearray[8],
			$fldarray[9] => $valuearray[9],
			$fldarray[10] => $valuearray[10],
			$fldarray[11] => $valuearray[11],
			$fldarray[12] => $valuearray[12],
			$fldarray[13] => $valuearray[13],
			$fldarray[14] => $valuearray[14],
			$fldarray[15] => $valuearray[15],
			$fldarray[16] => $valuearray[16],
			$fldarray[17] => $valuearray[17],
			$fldarray[18] => $valuearray[18],
			$fldarray[19] => $valuearray[19],
			$fldarray[20] => $valuearray[20],
			$fldarray[21] => $valuearray[21],
			$fldarray[22] => $valuearray[22],
			$fldarray[23] => $valuearray[23],
			]
		);
		return $update;
	}

	public static function fnGetEmployeeCount() {
		$db = DB::connection('mysql');
		$sql= DB::table('emp_mstemployees')
						->SELECT('*')
						->count();

		return $sql;

	}
	//Common Function
	  public static function getYrMonCountBtwnDates($startDT, $endDT){
	    $retVal['year']=0;
	    $retVal['month']=0;
	    if ($endDT == ""||$endDT=="") {
	      $endDT = date("Y-m-d");
	    }
	    if (($startDT!=""&&$startDT!="0000-00-00")&&($endDT!=""&&$endDT!="0000-00-00")){
	      $diff = abs(strtotime($endDT) - strtotime($startDT));
	      $dys = (int)((strtotime($endDT)-strtotime($startDT))/86400);
	      $retVal['year'] = floor($diff / (365*60*60*24));
	      $retVal['month'] = floor(($diff - $retVal['year'] * 365*60*60*24) / (30*60*60*24));
	    } 
	    return $retVal;
	  }
	  public static function fnAddZeroSubstring($val) {
	    return substr($val, -2);
	  }
	   public static function fnGetcusname($request,$empid){
			  $db = DB::connection('mysql');
			  $query = $db->table('mst_customerdetail')
                              ->SELECT('mst_customerdetail.customer_name')
                              ->leftJoin('clientempteam', function($join){
    								$join->ON('clientempteam.cust_id', '=', 'mst_customerdetail.customer_id')
  										->WHERE('clientempteam.status', '=', 1);
  								})
                              ->LEFTJOIN('emp_mstemployees', 'emp_mstemployees.Emp_ID' ,'=','clientempteam.emp_id')
                              ->where('emp_mstemployees.Emp_ID', '=', $empid)
                              ->get();
		  return $query; 
	 }
	 public static function rejoinupdate($request) {
		$db = DB::connection('mysql');
		$update=DB::table('emp_mstemployees')
		->where('Emp_ID', $request->viewid)
		->update(
			[
			'resign_id' => 0,
			'resignedDate' => NULL]);
		return $update;
	}
	 public static function resignupdate($request) {
	 	//print_r($_REQUEST);exit();
	 	$db = DB::connection('mysql');
		$update=DB::table('emp_mstemployees')
		->where('Emp_ID', $request->viewid)
			//$sql="UPDATE emp_mstemployees SET resign_id='0',resignedDate= NULL WHERE Emp_ID='$request->viewid'";
			//$cards = DB::select($sql);
            //return $cards;
		->update(
			[
			'resign_id' => 1,
			'resignedDate' => $request->txt_date]);
		return $update;
	}
}
	

