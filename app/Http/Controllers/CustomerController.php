<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Customer;
use DB;
use Input;
use Redirect;
use Session;
class CustomerController extends Controller {
	function index(Request $request) { 
		$cstviews=array();
		$branchNames=array();
		$disabledactive=" ";
		$disabledinactive=" ";
		$disabledusenotuse=" ";
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
	//Filter process
       		if (!isset($request->filterval) || $request->filterval == "") {
	        	$request->filterval = 1;
	      	}
	    	if ($request->filterval == 1) {
	        	$disabledactive="disabled fb";
      		} else if($request->filterval == 2) {
        		$disabledinactive="disabled fb";
      		}  else if($request->filterval == 3) {
        		$disabledusenotuse="disabled fb";
      		} 
      			
	//Sorting process
      		$customersortarray = array("customer_id"=>trans('messages.lbl_CustId'),
						"customer_name"=>trans('messages.lbl_name'),
						"customer_address"=>trans('messages.lbl_address')
						);
      		// $customersortarray = [$request->cussort=>$request->cussort,
        //             'customer_id'=> 'CustomerID',
        //             'customer_name'=>  'Name',
        //             'customer_address'=> 'Address'];
			
	 //SORT POSITION
        if (!empty($request->singlesearchtxt) || $request->searchmethod == 2) {
          $sortMargin = "margin-right:260px;";
        } else {
          $sortMargin = "margin-right:0px;";
        }
        if ($request->cussort == "") {
        	$request->cussort = "customer_id";
        	$request->sortOrder = "DESC";
      	}
      	if($request->oldfilter == $request->filterval){
		if (empty($request->sortOrder)) {
        	$request->sortOrder = "DESC";
      	}
      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}
	   	} else {
	   		if (empty($request->sortOrder)) {
	        	$request->sortOrder = "DESC";
	      	}
	      	if ($request->sortOrder == "asc") {  
	      		$request->sortstyle="sort_asc";
	      	} else {  
	   			$request->sortstyle="sort_desc";
	   		}
	   	}
		$src = "";
		$customerchange=Customer::customerchange($request);
		$customerdetailview=Customer::CustomerDetails($request);
		$i = 0;
	    foreach($customerdetailview as $key=>$cstview) {
	    	$cstviews[$i]['customer_name'] = $cstview->customer_name;
	    	$cstviews[$i]['contract'] = $cstview->contract;
	    	$cstviews[$i]['customer_contact_no'] = $cstview->customer_contact_no;
	    	$cstviews[$i]['customer_fax_no'] = $cstview->customer_fax_no;
	    	$cstviews[$i]['customer_website'] = $cstview->customer_website;
	    	$cstviews[$i]['customer_address'] = $cstview->customer_address;
	    	$cstviews[$i]['romaji'] = $cstview->romaji;
	    	$cstviews[$i]['customer_id'] = $cstview->customer_id;
	    	$cstviews[$i]['delflg'] = $cstview->delflg;
	    	$cstviews[$i]['id'] = $cstview->id;
	    	$branchNames = Customer::getSelectedMember($cstviews[$i]['customer_id']);
	    	$j = 0;
	    	foreach($branchNames as $key=>$rec) { 
	 			$cstviews[$i]['BranchName'][$j]=$rec->branch_name;
	 			$j++;
			}
	    	$i++;
	    }
	    //print_r($cstviews);exit();
		return view('Customer.index',['request' => $request,
									 'cstviews' => $cstviews,
									 'customersortarray' => $customersortarray,
									 'sortMargin' => $sortMargin,
									 'detailview' => $customerdetailview,
									 'src' => $src,
									 'disabledactive' => $disabledactive,
									 'disabledinactive' => $disabledinactive,
									 'disabledusenotuse' => $disabledusenotuse]);
	}
	function Onsitehistory(Request $request) {
		//print_r($_REQUEST);exit();
		$customerhistory = array();
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$cushistory = Customer::fnGetOnsiteHistoryDetails($request->hdnempid,$request);
		$i = 0;
	    foreach($cushistory as $key=>$chistory) {
	    	$customerhistory[$i]['LastName'] = $chistory->LastName;
	    	$customerhistory[$i]['start_date'] = $chistory->start_date;
	    	$customerhistory[$i]['end_date'] = $chistory->end_date;
	    	$customerhistory[$i]['status'] = $chistory->status;
	    	$customerhistory[$i]['customer_name'] = $chistory->customer_name;
	    	if($chistory->end_date=="0000-00-00") {
				$customerhistory[$i]['end_date'] ="";
			}
	    	$cusexpdetails = Customer::getYrMonCountBtwnDates($customerhistory[$i]['start_date'],$customerhistory[$i]['end_date']);
	    	if ($cusexpdetails['year'].".".$cusexpdetails['month'] == 0.0) {
				$customerhistory[$i]['experience'] = "0.0";
			} else {
				$customerhistory[$i]['experience'] = $cusexpdetails['year'].".".Customer::fnAddZeroSubstring($cusexpdetails['month']);
			}
	    	$i++;
	    }
		return view('Customer.Onsitehistory',['request' => $request,
											'cushistory' => $cushistory,
											'customerhistory' => $customerhistory
											]);
	}
	function addedit(Request $request) {
		$maxid=array();
		if(isset($request->flg)){
		$customer_id = substr($request->custid, 3,5);
		$cus = $customer_id+1;
		$cus = str_pad($cus,5,"0",STR_PAD_LEFT);
		//print_r($cus);exit();
		if(isset($_REQUEST['hid_branch_id']) != "") {
			$branchid = "CST" . $cus;
		} else {
			$branchid = $_REQUEST['hid_branch_id'];
		}
		//print_r($branchid);exit();
		$getbranchdetails=Customer::getbranchdetails($request,$branchid);
		//print_r($getbranchdetails);exit();
		$getdetails=Customer::getcustomerdetails($request);
		return view('Customer.addedit',['request' => $request,
										'getdetails' => $getdetails,
										'getbranchdetails' => $getbranchdetails]);
	} else {
		$custmaxid=Customer::getmaxid();
		//print_r($branchid);exit();
		if($custmaxid == "") {
					$cus = "CST00001";
				} else {
					$aaa=$custmaxid;
					$customer = substr($aaa, 3,5);
					$cus = (int)$customer + 100;
					$cus = str_pad($cus,5,"0",STR_PAD_LEFT);
					$cus = "CST" . $cus;
				}
		return view('Customer.addedit',['request' => $request,
										'maxid' => $cus]);
	}
}	
	function addeditprocess(Request $request) {
		    if($request->editid!="") {
				$customer_id = substr($request->custid, 3,5);
				$cus = $customer_id+1;
				$cus = str_pad($cus,5,"0",STR_PAD_LEFT);
				if($_REQUEST['hid_branch_id'] == "") {
					$branchid = "CST" . $cus;
				} else {
					$branchid = $_REQUEST['hid_branch_id'];
				}	
			$update = Customer::updaterec($request);
			$update= Customer::updatebranchrec($request,$branchid);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
	    Session::flash('custid', $request->custid );
	    Session::flash('id', $request->id );
		}else { 
			$custmaxid=Customer::getmaxid();
			if($custmaxid == "") {
				$cus3 = "CST00001";
			} else {
				$aaa=$custmaxid;
				$customer = substr($aaa, 3,5);
				$cus1 = (int)$customer + 100;
				$cus2 = str_pad($cus1,5,"0",STR_PAD_LEFT);
				$cus3 = "CST" . $cus2;
			}
			if($_REQUEST['hid_branch_id'] == "") {
					$customer = substr($cus3, 3,5);
					$cus4 = $customer+1;
					$cus5 = str_pad($cus4,5,"0",STR_PAD_LEFT);
				    $branchid = "CST" . $cus5;
				}else{
					$branchid = $_REQUEST['hid_branch_id'];
				}    
			$insert = Customer::insertRec($request,$cus3);
			$getmaxid = Customer::fetchmaxid($request);
			$insert= Customer::insertbranchrec($request,$branchid,$cus3);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
	     Session::flash('id', $getmaxid );
	     Session::flash('custid', $cus3 );
		}
		return Redirect::to('Customer/View?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function View(Request $request) {
		$inchargeview=array();
		$branchview=array();
		$currentview=array();
		$currentempview=array();
		$emp_type=array();
		$emp_type1=array();
		if(Session::get('custid') !="" && Session::get('id') !=""){
          $request->custid = Session::get('custid');
          $request->id = Session::get('id');
     	 }
		if(!isset($request->id)){
	         return $this->index($request);
	      }
		$customer_id = substr($request->custid, 3,5);
		$cus = $customer_id+1;
		$cus = str_pad($cus,5,"0",STR_PAD_LEFT);
		//print_r($cus);exit();
		if(isset($_REQUEST['hid_branch_id']) != "") {
			$branchid = "CST" . $cus;
		} else {
			$branchid = " ";
		}
		$getbranchdetails=Customer::getbranchdetails($request,$branchid);
		$id=$request->custid;
		$getinchargedetails=Customer::getinchargedetails($id);
		$i=0;
		foreach($getinchargedetails as $key=>$inchview) {
	    	$inchargeview[$i]['incharge_name'] = $inchview->incharge_name;
	    	$inchargeview[$i]['incharge_contact_no'] = $inchview->incharge_contact_no;
	    	$inchargeview[$i]['incharge_email_id'] = $inchview->incharge_email_id;
	    	$inchargeview[$i]['id'] = $inchview->id;
	    	$inchargeview[$i]['DesignationNM'] = $inchview->DesignationNM;
	    	$inchargeview[$i]['incharge_name_romaji'] = $inchview->incharge_name_romaji;
	    	$i++;
	    }
	    $getbranchdetails=Customer::getbdetails($id);
		$i=0;
		foreach($getbranchdetails as $key=>$bview) {
	    	$branchview[$i]['branch_name'] = $bview->branch_name;
	    	$branchview[$i]['branch_contact_no'] = $bview->branch_contact_no;
	    	$branchview[$i]['branch_fax_no'] = $bview->branch_fax_no;
	    	$branchview[$i]['id'] = $bview->branch_id;
	    	$branchview[$i]['branch_address'] = $bview->branch_address;
	    	$branchview[$i]['customer_id'] = $bview->customer_id;
	    	//$branchview[$i]['romaji'] = $bview->romaji;
	    	$i++;
	    }
	     $currentemployeedetails = Customer::selectByIdclient($id);
	     $i=0;
		foreach($currentemployeedetails as $key=>$cempview) {
	    	$currentview[$i]['customer_id'] = $cempview->customer_id;
	    	$currentview[$i]['customer_name'] = $cempview->customer_name;
	    	$currentview[$i]['emp_id'] = $cempview->emp_id;
	    	$currentview[$i]['status'] = $cempview->status;
	    	$currentview[$i]['start_date'] = $cempview->start_date;
	    	$currentview[$i]['end_date'] = $cempview->end_date;
	    	$currentview[$i]['update_by'] = $cempview->update_by;
	    	$viewname = Customer::emplastname($currentview[$i]['emp_id']);
	    	foreach($viewname as $key=>$rec) { 
	 		$currentview[$i]['LastName']=$rec->LastName;
	 		$currentview[$i]['FirstName']=$rec->FirstName;
			}
			if($cempview->end_date=="0000-00-00") {
				$currentview[$i]['end_date'] ="";
			}
	    	$cusexpdetails = Customer::getYrMonCountBtwnDates($currentview[$i]['start_date'],$currentview[$i]['end_date']);
	    	if ($cusexpdetails['year'].".".$cusexpdetails['month'] == 0.0) {
				$currentview[$i]['experience'] = "-";
			} else {
				$currentview[$i]['experience'] = $cusexpdetails['year'].".".Customer::fnAddZeroSubstring($cusexpdetails['month']);
			}
	    	$i++;
	    }
	    $currentempdetails = Customer::selectByIdchangeclient($id);
	     $i=0;
		foreach($currentempdetails as $key=>$cemployeeview) {
	    	$currentempview[$i]['customer_id'] = $cemployeeview->customer_id;
	    	$currentempview[$i]['customer_name'] = $cemployeeview->customer_name;
	    	$currentempview[$i]['emp_id'] = $cemployeeview->emp_id;
	    	$currentempview[$i]['status'] = $cemployeeview->status;
	    	$currentempview[$i]['start_date'] = $cemployeeview->start_date;
	    	$currentempview[$i]['end_date'] = $cemployeeview->end_date;
	    	$currentempview[$i]['update_by'] = $cemployeeview->update_by;
	    	$viewname = Customer::emplastname($currentempview[$i]['emp_id']);
	    	foreach($viewname as $key=>$rec) { 
		 		$currentempview[$i]['LastName']=$rec->LastName;
		 		$currentempview[$i]['FirstName']=$rec->FirstName;
			}
			if($cemployeeview->end_date=="0000-00-00") {
				$currentempview[$i]['end_date'] ="";
			}
	    	$cusexpdetails = Customer::getYrMonCountBtwnDates($currentempview[$i]['start_date'],$currentempview[$i]['end_date']);
	    	if ($cusexpdetails['year'].".".$cusexpdetails['month'] == 0.0) {
				$currentempview[$i]['experience'] = "-";
			} else {
				$currentempview[$i]['experience'] = $cusexpdetails['year'].".".Customer::fnAddZeroSubstring($cusexpdetails['month']);
			}
			$cushistory = Customer::fnGetOnsiteHistory($currentempview[$i]['emp_id'],$request);
			foreach($cushistory as $key=>$rec) { 
		 		$currentempview[$i]['customername']=$rec->customer_name;
			}
	    	$i++;
	    }
		$getdetails=Customer::getcustomerdetails($request);
		return view('Customer.View',['request' => $request,
										'getdetails' => $getdetails,
										'inchargeview' => $inchargeview,
										'branchview' => $branchview,
										'currentview' => $currentview,
										'currentempview' =>$currentempview,
										'getbranchdetails' =>$getbranchdetails
										]);
	}
	function empnamepopup(Request $request) {
		//print_r($_REQUEST);exit();
		$cemployeeview=array();
		$clientstatus = Customer::getClientStatus();
		$employeedetail = Customer::getemployeedetail($request);
		$selectionaddress = Customer::selectEmpAddress($request);
		 $i=0;
		foreach($selectionaddress as $key=>$cpopupview) {
	    	$cemployeeview[$i]['branch_name'] = $cpopupview->branch_name;
	    	$cemployeeview[$i]['LastName'] = $cpopupview->LastName;
	    	$cemployeeview[$i]['Emp_ID'] = $cpopupview->Emp_ID;
	    	$cemployeeview[$i]['start_date'] = $cpopupview->start_date;
	    	$cemployeeview[$i]['end_date'] = $cpopupview->end_date;
	    	$cemployeeview[$i]['status'] = $cpopupview->status;
	    	if($cpopupview->end_date=="0000-00-00") {
				$cemployeeview[$i]['end_date'] ="";
			} else
			{
				$cemployeeview[$i]['end_date'] = $cpopupview->end_date;
			}
			$i++;
	    }
		$emp_type=Customer::getUserNameByCustomer($request);
		$emp_type1=Customer::getUserNameByEmployee($request);
		$branchdetails=Customer::branchaddemployee($request);
		return view('Customer.empnamepopup',['request' => $request,
											'clientstatus' => $clientstatus,
											'employeedetail' => $employeedetail,
											'cemployeeview' => $cemployeeview,
											'empname' => $emp_type,
											'empname1' => $emp_type1,
											'bname' => $branchdetails
											]);
		}
	function empnamepopupeditprocess(Request $request) {
		//print_r($_REQUEST);exit();
		if($request->selectionid == 1){
			$insert=Customer::insertemployee($request);
			if($insert) {
				Session::flash('success', 'Registered Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Registered Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		    Session::flash('custid', $request->custid );
		    Session::flash('id', $request->id );
		}else{
			//print_r($_REQUEST);exit();
			$update=Customer::updateemployeeedit($request);
			$insert=Customer::insertemployeefn($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		    Session::flash('custid', $request->custid );
		    Session::flash('id', $request->id );
		}
		return Redirect::to('Customer/View?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
	//print_r($cus3);exit();

	function branchname_ajax(Request $request) {
		$customerid = $request->customerid;
		$branchquery = Customer::fnGetBranchDetails($customerid);
		$branchquery=json_encode($branchquery);
		echo $branchquery;exit;
	}
	function Branchaddedit(Request $request) {
		if($request->flg !="") {
			$bid=$request->branchid;
			$bdetails=Customer::getbranchdetails($request,$bid);
			//print_r($bdetails);exit();
			return view('Customer.Branchaddedit',['request' => $request,
												'bdetails'=>$bdetails]);
		} else {
		$maxbranchid = Customer::branchadd($request);
		if(empty($maxbranchid)) {
				$aaa=$request->custid;
				$customer = substr($aaa, 3,5);
				$cus1 = (int)$customer + 1;
				$cus2 = str_pad($cus1,5,"0",STR_PAD_LEFT);
				$cus3 = "CST" . $cus2;
			} else {
				$aaa=$maxbranchid[0];
				$customer = substr($aaa, 3,5);
				$cus1 = (int)$customer + 1;
				$cus2 = str_pad($cus1,5,"0",STR_PAD_LEFT);
				$cus3 = "CST" . $cus2;
			}
		}
		return view('Customer.Branchaddedit',['request' => $request,
												'max' => $cus3]);
	}

	function Branchaddeditprocess(Request $request) {
		//print_r($_REQUEST);exit();
		if($request->editid =="") {
		if(Session::get('custid') !=""){
          $request->custid = Session::get('custid');
     	 }
		if(!isset($request->id)){
	         return $this->index($request);
	      }
	      	$maxbranchid = Customer::branchadd($request);
			if(empty($maxbranchid)) {
				$aaa=$request->custid;
				$customer = substr($aaa, 3,5);
				$cus1 = (int)$customer + 1;
				$cus2 = str_pad($cus1,5,"0",STR_PAD_LEFT);
				$cus3 = "CST" . $cus2;
			} else {
				$aaa=$maxbranchid[0];
				$customer = substr($aaa, 3,5);
				$cus1 = (int)$customer + 1;
				$cus2 = str_pad($cus1,5,"0",STR_PAD_LEFT);
				$cus3 = "CST" . $cus2;
			}
			$custid=$request->custid;
			$insert= Customer::insertbranchrec($request,$cus3,$custid);
			Session::flash('custid', $request->custid );
			Session::flash('id', $request->id );
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$branchid=$request->branid;
			$update= Customer::updatebranchrec($request,$branchid);
			Session::flash('custid', $request->custid );
	    	Session::flash('id', $request->id );
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		return Redirect::to('Customer/View?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function Inchargeaddedit(Request $request) {
		$getbname=customer::getbname();
		$getdesname=customer::getdesname();
		if($request->flg !="") {
			$inchargeid=$request->editid;
			//print_r($inchargeid);exit();
			$indetails=Customer::getinchargeupdatedetails($request,$inchargeid);
			//print_r($indetails);exit();
			return view('Customer.Inchargeaddedit',['request' => $request,
													'indetails'=>$indetails,
													'getbname' => $getbname,
													'getdesname' => $getdesname]);
		} else {
			
			return view('Customer.Inchargeaddedit',['request' => $request,
													'getbname' => $getbname,
													'getdesname' => $getdesname]);
		}
	}
	function Inchargeaddeditprocess(Request $request) {
		//print_r($_REQUEST);exit();
		if($request->editid =="") {
		if(Session::get('custid') !=""){
          $request->custid = Session::get('custid');
     	 }
		if(!isset($request->id)){
	         return $this->index($request);
	      }
			$custid=$request->custid;
			$insert= Customer::insertinchargerec($request,$custid);
			Session::flash('custid', $request->custid );
			Session::flash('id', $request->id );
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			//print_r($_REQUEST);exit();
			$id=$request->editid;
			$update= Customer::updateinchargerec($request,$id);
			Session::flash('custid', $request->custid );
	    	Session::flash('id', $request->id );
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		return Redirect::to('Customer/View?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function coverletterpopup(Request $request) {
		//print_r($request);exit();
		$employeedetail = Customer::getemployeedetail($request);
		//print_r($employeedetail);exit();
		return view('Customer.coverletterpopup',['request' => $request,
												'employeedetail' => $employeedetail]);
	}
	function letterupload(Request $request){
     if($request->letter != "") {
		//print_r($_FILES['letter']);exit();
      $extension= self::getExtension($_FILES['letter']['name']);
      $data['fileName']=$request->custid.'.'.$extension;
      $update=Customer::updatecoverrec($request,$data['fileName']);
      $file = $request->letter;
      $destinationPath = 'resources/assets/uploadandtemplates/upload/Coverletter';
      if(!is_dir($destinationPath)) {
          mkdir($destinationPath, true);
      }
      chmod($destinationPath, 0777);
      $file->move($destinationPath,$data['fileName']);
      chmod($destinationPath."/".$data['fileName'], 0777);
           Session::flash('custid', $request->custid); 
           Session::flash('id', $request->id );
      if($file) {
        Session::flash('success', 'File Uploaded Sucessfully!'); 
        Session::flash('type', 'alert-success'); 
      } else {
        Session::flash('type', 'File Uploaded Unsucessfully!'); 
        Session::flash('type', 'alert-danger'); 
      }
    	return Redirect::to('Customer/View?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
     }
  }
  function getExtension($str) {
    $i = strrpos($str,".");
    if (!$i) { return ""; }
    $l = strlen($str) - $i;
    $ext = substr($str,$i+1,$l);
    return $ext;
  }
}