<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class Customer extends Model {
	public static function fnGetOnsiteHistoryDetails($empid,$request) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees AS emp')->SELECT('emp.Emp_ID',
								'emp.FirstName',
								'emp.LastName',
								'emp.Title',
								'cli.cust_id',
								'cli.status',
								'cli.start_date',
								'cli.end_date',
								'cus.customer_name')
					->JOIN('clientempteam AS cli','emp.Emp_ID','=','cli.emp_id')
					->JOIN('mst_customerdetail AS cus','cli.cust_id','=','cus.customer_id')
        			->where('emp.Emp_ID', '=', $empid)
					->where('emp.delFlg',0)
					->where('cli.delFLg',0)	
					->where('cus.delflg',0)
					->paginate($request->plimit);
		return $query;
	}
	public static function fnGetOnsiteHistory($empid,$request) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees AS emp')->SELECT('emp.Emp_ID',
								'emp.FirstName',
								'emp.LastName',
								'emp.Title',
								'cli.cust_id',
								'cli.status',
								'cli.start_date',
								'cli.end_date',
								'cus.customer_name')
					->JOIN('clientempteam AS cli','emp.Emp_ID','=','cli.emp_id')
					->JOIN('mst_customerdetail AS cus','cli.cust_id','=','cus.customer_id')
        			->where('emp.Emp_ID', '=', $empid)
					->where('emp.delFlg',0)
					->where('cli.delFLg',0)	
					->where('cus.delflg',0)
					->get();
					//->paginate($request->plimit);
		return $query;
	}
	 //Common Function
	  public static function getYrMonCountBtwnDates($startDT, $endDT){
	    $retVal['year']=0;
	    $retVal['month']=0;
	    if ($endDT == ""||$endDT=="0000-00-00") {
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
	  public static function CustomerDetails($request) {
	  	$db = DB::connection('mysql');
	  	$query = $db->TABLE($db->raw("(select *,
				(SELECT count(clientempteam.cust_id) AS cnt FROM emp_mstemployees 
								LEFT JOIN clientempteam ON emp_mstemployees.Emp_ID=clientempteam.emp_id
								where clientempteam.status = '1' AND emp_mstemployees.resign_id = '0' AND clientempteam.cust_id = mst_customerdetail.customer_id) 
				CNT 
				from mst_customerdetail) as tbl1"));
	  
	  			if ($request->filterval == 1) {
  					//print_r($request->filterval);exit();
					$query = $query->where(function($joincont) use ($request) {
                                      $joincont->where('CNT', '>', 0);
                                      $joincont->where('delflg', '=', 0);
                                      });
				} else if ($request->filterval == 2) {
					$query = $query->where(function($joincont) use ($request) {
                                      $joincont->where('CNT', '=', 0);
                                      $joincont->where('delflg', '=', 0);
                                      });
				} else {
					$query = $query->where(function($joincont) use ($request) {
                                      $joincont->where('delflg', '=', 1);
                                      });
				}
	  	
			if (!empty($request->singlesearchtxt)) {
					$query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('customer_name', 'LIKE', '%' . $request->singlesearchtxt . '%')
                                    ->orWhere('customer_address', 'LIKE', '%' . $request->singlesearchtxt . '%');
                                    });
				}
			
				if (!empty($request->name)) {
					$query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('customer_name', 'LIKE', '%' . $request->name . '%');
                                   // ->orWhere('customer_address', 'LIKE', '%' . $request->address . '%');
                                    });
				}
				if (!empty($request->address)) {
					$query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('customer_address', 'LIKE', '%' . $request->address . '%');
                                   // ->orWhere('customer_address', 'LIKE', '%' . $request->address . '%');
                                    });
				}
				if (!empty($request->name && $request->address)) {
					$query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('customer_name', 'LIKE', '%' . $request->name . '%')
                                    ->orWhere('customer_address', 'LIKE', '%' . $request->address . '%');
                                    });
				}
				if (!empty($request->startdate) && !empty($request->enddate)) {
						$query = $query->where('contract','>=',$request->startdate);
						$query = $query->where('contract','<=',$request->enddate);
					}
					if (!empty($request->startdate) && empty($request->enddate)) {
						$query = $query->where('contract','>=',$request->startdate);
					}
					if (empty($request->startdate) &&!empty($request->enddate)) {
						$query = $query->where('contract','<=',$request->enddate);
					}
				if($request->oldfilter == $request->filterval){
					$query = $query->ORDERBY($request->cussort, $request->sortOrder)
								   ->ORDERBY('customer_id', 'DESC');	
				} else {
					$query = $query->ORDERBY($request->cussort, $request->sortOrder)
									->ORDERBY('customer_id', 'DESC');
									$request->cussort = "customer_id";
				}
    			$query =$query->paginate($request->plimit);
    										// ->tosql()
    										// dd($query);
			return $query;
			}
		public static function getSelectedMember($id) {
			$db = DB::connection('mysql');
			$query= $db->table('mst_branchdetails')
						->SELECT('mst_branchdetails.*')
						->leftJoin('mst_customerdetail', 'mst_customerdetail.customer_id', '=', 'mst_branchdetails.customer_id')
						->where('mst_customerdetail.customer_id','=', $id)
						->ORDERBY('mst_branchdetails.branch_id','ASC')
						->get();
			return $query;
			}
		public static function customerchange($request) {
			$db = DB::connection('mysql');
			$update=DB::table('mst_customerdetail')
				->where('id', $request->id)
				->update(
					['update_date' => date('Y-m-dh:i:s'),
					 'delflg' => $request->useval]
			);
			return $update;
	}	
		public static function getmaxid() {
				$db = DB::connection('mysql');
				$maxid=DB::table('mst_customerdetail')
					->max('customer_id');
				return $maxid;
		}
		public static function insertRec($request,$cus) {
			$insert=DB::table('mst_customerdetail')->insert([
				'id' => '',
	            'customer_id' => $cus,
				'customer_name' => $request->txt_custnamejp,
	             'contract' => $request->txt_custagreement,
	             'create_date' => date('Y-m-d'),
				'create_by' => Auth::user()->username,
	           // 'update_date' => date('Y-m-d'),
	            //'Update_by' => Auth::user()->username,
	             'customer_contact_no' => $request->txt_mobilenumber,
	             'customer_email_id'=> '',
	             'customer_fax_no'=> $request->txt_fax,
	             'customer_website' => $request->txt_url,
	             'customer_address'=>$request->txt_address,
	             'romaji'=> $request->txt_kananame,
	             'delflg'=> 0,
	             'nickname'=> $request->txt_repname,
				]);
	}

	public static function updaterec($request) { 
        $db = DB::connection('mysql');
        $tbl_name = "mst_customerdetail";
        $allupdatequery= $db->table($tbl_name)
                    ->where('id', $request->editid)
                    ->update([ 'customer_id' => $request->custid,
							'customer_name' => $request->txt_custnamejp,
				             'contract' => $request->txt_custagreement,
				             'update_date' => date('Y-m-d'),
				             'update_by' => Auth::user()->username,
				             'customer_contact_no' => $request->txt_mobilenumber,
				             'customer_email_id'=> '',
				             'customer_fax_no'=> $request->txt_fax,
				             'customer_website' => $request->txt_url,
				             'customer_address'=>$request->txt_address,
				             'romaji'=> $request->txt_kananame,
				             'nickname'=> $request->txt_repname]);
          return $allupdatequery;
     }
     public static function insertbranchrec($request,$branchid,$cus) { 
        $insert=DB::table('mst_branchdetails')->insert([
				'id' => '',
	            'customer_id' => $cus,
	            'branch_id' => $branchid,
				'branch_name' => $request->txt_branch_name,
	             'branch_contact_no' => $request->txt_mobilenumber,
	             'branch_fax_no' => $request->txt_fax,
	             'branch_address' => $request->txt_address,
	             'create_date' => date('Y-m-d'),
				'create_by' => Auth::user()->username,
	            //'update_date' => date('Y-m-d'),
	            //'Update_by' => Auth::user()->username,
	            'delflg' => 0
				]);
     }	
     public static function updatebranchrec($request,$branchid) { 
        $db = DB::connection('mysql');
        $tbl_name = "mst_branchdetails";
        $allupdatequery= $db->table($tbl_name)
                    ->where('customer_id','=', $request->custid)
                   ->where('branch_id','=', $branchid)
                    ->update([ 'branch_name' => $request->txt_branch_name,
                    			'branch_contact_no' => $request->txt_mobilenumber,
	             				'branch_fax_no' => $request->txt_fax,
	             				'branch_address' => $request->txt_address,
				             	'delflg'=> 0,
				             	'update_date' => date('Y-m-d'),
	            				'update_by' => Auth::user()->username]);
          return $allupdatequery;
     }		
     public static function getcustomerdetails($request) {
         $db =DB::connection('mysql');
        $tbl_name = "mst_customerdetail";
        $query= $db->table($tbl_name)
                   ->select('id AS id',
                   			 'customer_id AS custid', 
                             'customer_name AS txt_custnamejp',
                             'contract AS txt_custagreement', 
                             'customer_contact_no AS txt_mobilenumber',
                             'customer_fax_no AS txt_fax', 
                             'customer_website AS txt_url', 
                             'customer_address As txt_address',
                             'romaji As txt_kananame',
                             'nickname As txt_repname',
                             'cover_letter As coverletter'
                             )
                   ->where('id','=', $request->id)
                   ->get();
        return $query;
     }
     public static function getbranchdetails($request,$branchid) { 
         $db =DB::connection('mysql');
        $tbl_name = "mst_branchdetails";
        $query= $db->table($tbl_name)
                   ->select('id AS id',
                   			 'branch_id AS branch_id',
                   			 'branch_name AS branch_name',
                   			 'branch_contact_no AS txt_mobilenumber',
                   			 'branch_fax_no AS txt_fax',
                   			 'branch_address AS txt_address' 
                             )
                   ->where('customer_id','=', $request->custid)
                   ->where('branch_id','=', $branchid)
                   ->get();
        return $query;
     }
     public static function fetchmaxid($request) {
        $db = DB::connection('mysql');
        $latDetails = $db->table('mst_customerdetail')
                           ->max('id');
            return $latDetails;
    }
     public static function getinchargedetails($id) {
			$db = DB::connection('mysql');
			$query= $db->table('mst_cus_inchargedetail')
						->SELECT('mst_cus_inchargedetail.*','sysdesignationtypes.DesignationNM')
						->leftJoin('sysdesignationtypes', 'sysdesignationtypes.DesignationCD', '=', 'mst_cus_inchargedetail.designation')
						->where('mst_cus_inchargedetail.branch_name','=', $id)
						->get();
			return $query;
			}
      public static function getbdetails($id) { 
        $db =DB::connection('mysql');
        $tbl_name = "mst_branchdetails";
        $query= $db->table($tbl_name)
                   ->select('mst_branchdetails.*')
                   ->where('customer_id','=', $id)
                   ->ORDERBY('branch_id','ASC')
                   ->get();
        return $query;
     }
     public static function selectByIdclient($id) { 
        $sql="SELECT * FROM  mst_customerdetail LEFT JOIN clientempteam ON clientempteam.cust_id = mst_customerdetail.customer_id 
					AND LEFT(clientempteam.emp_id, 3) NOT LIKE '%MBC%'
					where clientempteam.cust_id = '".$id."' ORDER BY clientempteam.start_date DESC";
		$cards = DB::select($sql);
		return $cards;
     }
     public static function selectByIdchangeclient($id) { 
        $sql="SELECT * FROM  mst_customerdetail LEFT JOIN clientempteam ON clientempteam.cust_id = mst_customerdetail.customer_id 
					AND LEFT(clientempteam.emp_id, 3) NOT LIKE '%MBC%'
					where clientempteam.cust_id = '".$id."' ORDER BY clientempteam.end_date DESC";
		$cards = DB::select($sql);
		return $cards;
     }	
     public static  function  emplastname($empid) {
     	$db =DB::connection('mysql');
        $tbl_name = "emp_mstemployees";
        $query= $db->table($tbl_name)
                   ->select('Emp_ID','FirstName','LastName','nickname')
                   ->where('Emp_ID','=', $empid)
                   ->get();
        return $query;
		}
		public static  function getClientStatus() {
			return array('1'=>"StayIN",'2'=>"Returned",'3'=>"Client Changed",'4'=>"Others");
		}
		public static  function getemployeedetail($request) {
			$db =DB::connection('mysql');
	        $tbl_name = "mst_customerdetail";
	        $query= $db->table($tbl_name)
	                   ->select('mst_customerdetail.*')
	                   ->where('customer_id','=', $request->custid)
	                   ->get();
	        return $query;
		}
		public static function selectEmpAddress($request) {
		$query = DB::table('emp_mstemployees')
					->select('emp_mstemployees.FirstName','emp_mstemployees.LastName','emp_mstemployees.Emp_ID'
						,'clientempteam.start_date',
				'clientempteam.cust_id','clientempteam.end_date','clientempteam.status','mst_customerdetail.customer_name', 'emp_mstemployees.nickname', 'clientempteam.branch_id','mst_branchdetails.branch_name')
					->leftjoin('clientempteam', 'emp_mstemployees.Emp_ID', '=', 'clientempteam.emp_id') 
					->leftjoin('mst_customerdetail', 'mst_customerdetail.customer_id', '=', 'clientempteam.cust_id') 
					->leftjoin('mst_branchdetails', 'mst_branchdetails.branch_id', '=', 'clientempteam.branch_id')
					->where('emp_mstemployees.Emp_ID', $request->employeeid)
					->where('clientempteam.cust_id', $request->custid)
					->where('clientempteam.status','=',1)
					->get();
				return $query;
	} 
		public static function getUserNameByCustomer($request) {
				$query = DB::table('mst_customerdetail')
						->select('customer_name','customer_id')
						->WHERE('delflg', '=', 0)
                        ->WHERERAW("customer_id NOT IN (SELECT cust_id FROM clientempteam WHERE cust_id = '$request->custid')")
                        ->lists('customer_name','customer_id');
                return $query;	
	} 	
		public static function fnGetBranchDetails($customerid) {
			$result = DB::table('mst_branchdetails')
							->select('mst_branchdetails.branch_name','mst_branchdetails.branch_id')
							->leftjoin('mst_customerdetail', 'mst_branchdetails.customer_id', '=', 'mst_customerdetail.customer_id')
							->where('mst_customerdetail.customer_id',$customerid)
							->orderBy('branch_id', 'ASC')
							->lists('branch_name','branch_id');
							// ->get();
			return $result;
		}
		public static function updateemployeeedit($request) {
		$update=DB::table('clientempteam')
	            ->where('emp_id', $request->empidd)
	            ->update(
	            ['status' => $request->status,
	            'start_date' => $request->txt_start_date,
	            'end_date' => $request->txt_end_date,
	            'Up_DT' => date('Y-m-d'),
	            'UP_TM' => date('H-i-s'),
	            'UpdatedBy' => Auth::user()->username]);
	    	return $update;
		}
		public static function insertemployeefn($request) {
		$insert=DB::table('clientempteam')
	            ->insert(
	            ['cust_id' => $request->newemployee,
	            'status' => 1,
	            'emp_id' => $request->empidd,
	            'start_date' => $request->txt_start_date,
	            'end_date' => $request->txt_end_date,
	            //'Up_DT' => date('Y-m-d'),
	            //'UP_TM' => date('H-i-s'),
	            'delFLg' => 0,
	            'branch_id' => $request->newbranch,
	            //'UpdatedBy' => Auth::user()->username,
	            'Ins_DT' => date('Y-m-d'),
	            'Ins_TM' => date('H-i-s'),
	            'CreatedBy' => Auth::user()->username]);
		}
		public static function insertemployee($request) {
		$insert=DB::table('clientempteam')
	            ->insert(
	            ['cust_id' => $request->custid,
	            'status' => 1,
	            'emp_id' => $request->newemployeename,
	            'start_date' => $request->txt_start_date,
	            'end_date' => '',
	            //'Up_DT' => date('Y-m-d'),
	            //'UP_TM' => date('H-i-s'),
	            'delFLg' => 0,
	            'branch_id' => $request->newbranches,
	            //'UpdatedBy' => Auth::user()->username,
	            'Ins_DT' => date('Y-m-d'),
	            'Ins_TM' => date('H-i-s'),
	            'CreatedBy' => Auth::user()->username]);
		}
		public static function branchadd($request)	{
			// print_r($request->custid);exit();
			$db =DB::connection('mysql');
	        $tbl_name = "mst_branchdetails";
	        $query= $db->table($tbl_name)
	                   ->select('branch_id')
	                   ->where('customer_id','=', $request->custid)
	                   ->ORDERBY('branch_id', 'DESC')
	                   ->lists('branch_id');
	                   //->first();
	        return $query;
		}
	public static function getbname() {
		return array('1'=>$msg = "本社");
	}
	public static function getdesname()	{
			$db =DB::connection('mysql');
	        $tbl_name = "sysdesignationtypes";
	        $query= $db->table($tbl_name)
	                   ->select('DesignationCD','DesignationNM')
	                   ->where('DelFlg','=', 0)
	                   ->ORDERBY('Order_id', 'ASC')
	                   ->lists('DesignationNM','DesignationCD');
	        return $query;
		}
	 public static function insertinchargerec($request,$cus) { 
	    $insert=DB::table('mst_cus_inchargedetail')->insert([
				'id' => '',
	            'customer_id' => $cus,
	            'incharge_name' => $request->txt_incharge_name,
				'incharge_name_romaji' => $request->txt_incharge_namekana,
	             'incharge_contact_no' => $request->txt_mobilenumber,
	             'incharge_email_id' => $request->txt_mailid,
	             'password' => '',
	             'create_date' => date('Y-m-d'),
				'create_by' => Auth::user()->username,
	            //'update_date' => date('Y-m-d'),
	            //'Update_by' => Auth::user()->username,
	            'delflg' =>0,
	            'designation' => $request->designation,
	            'confirmpassword' =>'',
	            'branch_name' => $cus
				]);
	 }
	 public static function getinchargeupdatedetails($request,$inchargeid) { 
         $db =DB::connection('mysql');
        $tbl_name = "mst_cus_inchargedetail";
        $query= $db->table($tbl_name)
                   ->select('id AS id',
                   			 'incharge_name AS txt_incharge_name',
                   			 'incharge_name_romaji AS txt_incharge_namekana',
                   			 'incharge_contact_no AS txt_mobilenumber',
                   			 'incharge_email_id AS txt_mailid',
                   			 'designation AS designation',
                   			 'branch_name AS bname'
                             )
                   ->where('id','=', $inchargeid)
                   ->get();
        return $query;
     }
     public static function updateinchargerec($request,$id) { 
        $db = DB::connection('mysql');
        $tbl_name = "mst_cus_inchargedetail";
        $allupdatequery= $db->table($tbl_name)
                   ->where('id','=', $id)
                    ->update(['customer_id' => $request->custid,
				            'incharge_name' => $request->txt_incharge_name,
							'incharge_name_romaji' => $request->txt_incharge_namekana,
				             'incharge_contact_no' => $request->txt_mobilenumber,
				             'incharge_email_id' => $request->txt_mailid,
				             'password' => '',
				            'designation' => $request->designation,
				            'confirmpassword' =>'',
				            'branch_name' => $request->custid,
				             'delflg'=> 0,
				             'update_date' => date('Y-m-d'),
	            			'update_by' => Auth::user()->username]);
          return $allupdatequery;
     }
     public static function getUserNameByEmployee($request) {
				$query = DB::table('emp_mstemployees')
						->select('FirstName', 'LastName', 'nickname','Emp_ID')
						->WHERE('resign_id', '=', 0)
						->WHERE('delFlg', '=', 0)
						->WHERE('Title', '=', 2)
                        ->WHERERAW("Emp_ID NOT IN (SELECT emp_id FROM clientempteam WHERE status = '1')")
                        ->ORDERBY('nickname','ASC' ) 
                        ->lists('nickname','Emp_ID');
				return $query;	
	} 
	public static function branchaddemployee($request)	{
			$db =DB::connection('mysql');
	        $tbl_name = "mst_branchdetails";
	        $query= $db->table($tbl_name)
	                   ->select('branch_name','branch_id')
	                   ->where('customer_id','=', $request->custid)
	                   ->ORDERBY('branch_id', 'DESC')
	                   ->lists('branch_name','branch_id');
	                   //->first();
	        return $query;
		}
	public static function updatecoverrec($request,$id) { 
    $db = DB::connection('mysql');
    $tbl_name = "mst_customerdetail";
    $allupdatequery= $db->table($tbl_name)
               ->where('customer_id','=', $request->custid)
                ->update(['cover_letter' => $id,
			             'update_date' => date('Y-m-d'),
            			'update_by' => Auth::user()->username]);
      return $allupdatequery;
 	}
 	public static function getaddrByChdcustomer($id) {
		$query = DB::table('clientempteam')
				->select('cust_id')
				->WHERE('emp_id', '=', $id)
				->WHERE('status', '!=', 3)
				->WHERE('delFLg', '=', 0)
				->get();
		return $query;	
	}
	public static function getnameByCustomer($request) {
				$query = DB::table('mst_customerdetail')
						->select('customer_name','customer_id')
						->WHERE('delflg', '=', 0)
                        ->WHERERAW("customer_id NOT IN (SELECT cust_id FROM clientempteam WHERE id = '$request->id')")
                        //->lists('customer_name','customer_id');
                        ->get();
				return $query;	
	} 	 			
}
