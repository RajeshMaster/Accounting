<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
use Config;

class StaffContract extends Model {
	public static function fnContractDetails($request, $resignid){
		$systemdate = date('Y-m-d');
		$db = DB::connection('mysql');
		$query = $db->table('mstcontract')
					->select('emp_mstemployees.Emp_ID','emp_mstemployees.FirstName','emp_mstemployees.LastName',
							 'mstcontract.StartDate','mstcontract.EndDate','mstcontract.Salary','mstcontract.Allowance9','mstcontract.Allowance10','mstcontract.Allowance1','mstcontract.Allowance2','mstcontract.Allowance3','mstcontract.Allowance5','mstcontract.Allowance6','mstcontract.Allowance7','mstcontract.Total','mstcontract.Allowance8','mstcontract.Contract_date','mstcontract.Id','mstcontract.Allowance4','mstcontract.Remarks', DB::raw("DATEDIFF(IFNULL(mstcontract.EndDate,CURDATE()),CURDATE())AS Validity"))
					->rightjoin('emp_mstemployees', 'mstcontract.Emp_id', '=', 'emp_mstemployees.Emp_ID');
		if ($resignid ==1) {
			$query = $query->whereRaw("mstcontract.EndDate<=CURDATE()");
		} else {
			$query = $query->whereRaw("(mstcontract.EndDate>CURDATE() OR mstcontract.EndDate IS NULL)");
			// $query = $query->where(function($joincont) use ($request) {
   //                          $joincont->whereRaw('mstcontract.EndDate', '>', CURDATE())
   //                             ->orWhereNull('mstcontract.EndDate');
   //                          });
		}
		if ($request->searchmethod == 1) {
			$query = $query->where(function($joincont) use ($request) {
                            $joincont->where('emp_mstemployees.Emp_ID', 'LIKE', '%' . $request->singlesearch . '%')
                                ->orwhere('emp_mstemployees.LastName', 'LIKE', '%' . $request->singlesearch . '%');
                            });
		} elseif ($request->searchmethod == 2) {
			$query = $query->where(function($joincont) use ($request) {
                    $joincont->where([['emp_mstemployees.Emp_ID', 'LIKE', '%' . 
                    												$request->employeeno . '%'],
                        		     ['emp_mstemployees.nickname', 'LIKE', '%' . 
                        		     									$request->employeename . '%'],
                        		     ['emp_mstemployees.DOJ', '>=', $request->startdate,'AND',$request->enddate ]]);
                            });
		}
		$query = $query->where('Title', '=', 2)
						->orderByRaw("CAST(replace(Salary, ',', '') AS UNSIGNED) $request->sortOrder")
			 		   ->orderBy($request->selectsort,$request->sortOrder)
					  ->paginate($request->plimit);
					  // ->tosql();
			 		// dd($query);
			 
					  		 
			return $query;

	}
	public static function emp_no_name($request) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees')
					->select('LastName','FirstName')
					->where([['Emp_ID', '=', $request->viewid]])
					->get();
		return $query;
	}
	public static function contract($request) {
		$db = DB::connection('mysql');
		$query = $db->table('mstcontract')
					->select('*')
					->where([['Emp_id', '=', $request->viewid]])
					->orderBy('EndDate', 'desc')
					->get();
		return $query;
	}
	public static function contract_add_edit($request) {
		//print_r($_REQUEST);exit();
		$db = DB::connection('mysql');
		$query = $db->table('mstcontract')
					->select('*')
					->where([['Emp_id', '=', $request->empnoadd],
							 ['Id', '=', $request->radio_emp]])
					->get();

		return $query;
	}
	public static function tablecreate() {
		$db = DB::connection('mysql');
		$query = $db->table('inv_set_contractallowance')
					->select('*')
					//->where([['delflg', '=', 0]])
					->get();
		
		return $query;
	}
	public static function addeditprocess($request) {
		$db = DB::connection('mysql');

		$allowance1 = $request->allowance_1;
		$allowance2 = $request->allowance_2;
		$allowance3 = $request->allowance_3;
		$allowance4 = $request->allowance_4;
		$allowance5 = $request->allowance_5;
	    $allowance6 = $request->allowance_6;
		$allowance7 = $request->allowance_7;
		$allowance8 = $request->allowance_8;
		$allowance9 = $request->allowance_9;
		$allowance10 = $request->allowance_10;

		if ( $allowance1 == "" ) {
				$allowance1 = 0;
			}
			if ( $allowance2 == "" ) {
				$allowance2 = 0;
			}
			if ( $allowance3 == "" ) {
				$allowance3 = 0;
			}
			if ( $allowance4 == "" ) {
				$allowance4 = 0;
			}
			if ( $allowance5 == "" ) {
				$allowance5 = 0;
			}
			if ( $allowance6 == "" ) {
				$allowance6 = 0;
			}
			if ( $allowance7 == "" ) {
				$allowance7 = 0;
			}
			if ( $allowance8 == "" ) {
				$allowance8 = 0;
			}
			if ( $allowance9 == "" ) {
				$allowance9 = 0;
			}
			if ( $allowance10 == "" ) {
				$allowance10 = 0;
			}
			$total=number_format(str_replace(',','',$request->Salary)
											+str_replace(',','',$allowance1)
											+str_replace(',','',$allowance2)
											+str_replace(',','',$allowance3)
											+str_replace(',','',$allowance4)
											+str_replace(',','',$allowance5)
											+str_replace(',','',$allowance6)
											+str_replace(',','',$allowance7)
											+str_replace(',','',$allowance8)
											+str_replace(',','',$allowance9)
											+str_replace(',','',$allowance10));
		$result= $insert=DB::table('mstcontract')->insert(
			['Emp_id' => $request->viewid,
			'StartDate' => $request->StartDate,
			'EndDate' => $request->EndDate,
			'Salary' => $request->Salary,
			'Allowance1' => $allowance1,
			'Allowance2' => $allowance2,
			'Allowance3' => $allowance3,
			'Allowance4' => $allowance4,
			'Allowance5' => $allowance5,
			'Allowance6' => $allowance6,
			'Allowance7' => $allowance7,
			'Allowance8' => $allowance8,
			'Allowance9' => $allowance9,
			'Allowance10' => $allowance10,
			'Total' => $total,
			'Contract_date' => $request->Contract_date,
			'Remarks' => $request->Remarks,
			'Created_Name' => Auth::user()->username,
			'Created_Date' => date('Y-m-d'),
			'Delete_flg' => 0]
		);
		return $result;
	}
	public static function updateprocess($request) {
		$db = DB::connection('mysql');

		$allowance1 = $request->allowance_1;
		$allowance2 = $request->allowance_2;
		$allowance3 = $request->allowance_3;
		$allowance4 = $request->allowance_4;
		$allowance5 = $request->allowance_5;
	    $allowance6 = $request->allowance_6;
		$allowance7 = $request->allowance_7;
		$allowance8 = $request->allowance_8;
		$allowance9 = $request->allowance_9;
		$allowance10 = $request->allowance_10;

		if ( $allowance1 == "" ) {
				$allowance1 = 0;
			}
			if ( $allowance2 == "" ) {
				$allowance2 = 0;
			}
			if ( $allowance3 == "" ) {
				$allowance3 = 0;
			}
			if ( $allowance4 == "" ) {
				$allowance4 = 0;
			}
			if ( $allowance5 == "" ) {
				$allowance5 = 0;
			}
			if ( $allowance6 == "" ) {
				$allowance6 = 0;
			}
			if ( $allowance7 == "" ) {
				$allowance7 = 0;
			}
			if ( $allowance8 == "" ) {
				$allowance8 = 0;
			}
			if ( $allowance9 == "" ) {
				$allowance9 = 0;
			}
			if ( $allowance10 == "" ) {
				$allowance10 = 0;
			}
		$total=number_format(str_replace(',','',$request->Salary)
											+str_replace(',','',$allowance1)
											+str_replace(',','',$allowance2)
											+str_replace(',','',$allowance3)
											+str_replace(',','',$allowance4)
											+str_replace(',','',$allowance5)
											+str_replace(',','',$allowance6)
											+str_replace(',','',$allowance7)
											+str_replace(',','',$allowance8)
											+str_replace(',','',$allowance9)
											+str_replace(',','',$allowance10));	

		$update=DB::table('mstcontract')
		->where([['Emp_id', '=', $request->viewid],
				['Id', '=', $request->radio_emp]])
		->update(
			['StartDate' => $request->StartDate,
			'EndDate' => $request->EndDate,
			'Salary' => $request->Salary,
			'Allowance1' => $allowance1,
			'Allowance2' => $allowance2,
			'Allowance3' => $allowance3,
			'Allowance4' => $allowance4,
			'Allowance5' => $allowance5,
			'Allowance6' => $allowance6,
			'Allowance7' => $allowance7,
			'Allowance8' => $allowance8,
			'Allowance9' => $allowance9,
			'Allowance10' => $allowance10,
			'Total' => $total,
			'Contract_date' => $request->Contract_date,
			'Remarks' => $request->Remarks,
			'Update_Name' => Auth::user()->username,
			'Update_Date' => date('Y-m-d')]);
		return $update;
	}
	public static function passport_table_exist_check(){
		$db = DB::connection('mysql');
		$tables = DB::select("SHOW TABLES LIKE 'mstpassport%'");
		return $tables;
	}
	public static function employee($request){
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees')
					->select('*')
					->where([['Emp_ID', '=', $request->empid]])
					->get();
		return $query;
	}
	public static function contractMaxDate($request){
    	$db=DB::connection('mysql');
		$query=$db->TABLE($db->raw("(SELECT c.*,m.Emp_id as mempid FROM mstcontract c inner join ( SELECT Emp_id , MAX(EndDate) AS MaxDate FROM mstcontract WHERE Emp_id='".$request->empid."' GROUP BY Emp_id) m on c.Emp_id = m.Emp_id and c.EndDate = m.MaxDate) as tb1"))
			->get();
		return $query;

	}
	// public static function contractAjax($empid,$end,$start) {
	// 	$db=DB::connection('mysql');
	// 		$query=$db->TABLE($db->raw("(SELECT count(*) as count from mstcontract where ((StartDate <= '$start' 
	// 				and EndDate >= '$end') or (StartDate >= '$start' and EndDate <= '$end') 
	// 				or (StartDate <= '$start' and EndDate >= '$start') or
	// 				(StartDate <= '$end' and EndDate >= '$end') /*or (StartDate >= '$end')*/) 
	// 				and (Emp_id='$empid')"))
	// 		->get();
	// 		return $query;
	// }
	// public static function contractAjax_id($empid,$end,$start,$id) {
	// 	$db=DB::connection('mysql');
	// 		$query=$db->TABLE($db->raw("(SELECT count(*) as count from mstcontract where ((StartDate <= '$start' 
	// 				and EndDate >= '$end') or (StartDate >= '$start' and EndDate <= '$end') 
	// 				or (StartDate <= '$start' and EndDate >= '$start') or
	// 				(StartDate <= '$end' and EndDate >= '$end') /*or (StartDate >= '$end')*/) 
	// 				and (Emp_id='$empid') and Id NOT IN($id)"))
	// 		->get();
	// 		return $query;
	// }
}