<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\NonStaff;
use App\Model\Staff;
use Common;
use DB;
use Input;
use Redirect;
use Session;
use Config;
use Carbon;
use File;
class NonStaffController extends Controller {
	function index(Request $request) {
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		//Filter process
		if (empty($request->resignid)) {
			$resignid = 0;
		} else {
			$resignid = 1;
		}
		
		//SORTING PROCESS
        if ($request->nonstaffsort == "") {
        	$request->nonstaffsort = "Emp_ID";
      	}
		if (empty($request->sortOrder)) {
        	$request->sortOrder = "DESC";
      	}
      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}
	 	//SORT POSITION
        if (!empty($request->singlesearch) || $request->searchmethod == 2) {
          $sortMargin = "margin-right:225px;";
        } else {
          $sortMargin = "margin-right:0px;";
        }
        $array = array("Emp_Id"=>trans('messages.lbl_empid'),
						"FirstName"=>trans('messages.lbl_empName'),
						"DOJ"=>trans('messages.lbl_dateofjoining'),
						"DOB"=>trans('messages.lbl_age')
						);
        $src = "";
		$noimage = "../resources/assets/images";
		$file = "../resources/assets/images/upload/thumbnail/";
		$uploadcheck = "../resources/assets/images/upload/";
		$disPath = "./resources/assets/images/upload/thumbnail/";
		$filename = "";
		$empdetailsdet=array();
		$getNSTdetails = NonStaff::fnGetNonStaffDetails($request, $resignid);
		$i = 0;
		foreach($getNSTdetails as $key=>$data) {
			$empdetailsdet[$i]['FirstName'] = $data->FirstName;
			$empdetailsdet[$i]['LastName'] = $data->LastName;
			$empdetailsdet[$i]['KanaFirstName'] = $data->KanaFirstName;
			$empdetailsdet[$i]['KanaLastName'] = $data->KanaLastName;
			$empdetailsdet[$i]['Gender'] = $data->Gender;
			$empdetailsdet[$i]['Picture'] = $data->Picture;
			if (is_numeric($data->Address1)) {
				$japanaddress = Staff::fngetjapanaddress($data->Address1);
				$empdetailsdet[$i]['Address1'] = (!empty($japanaddress[0]->address)) ?  $japanaddress[0]->address : $data->Address1;
			} else {
				$empdetailsdet[$i]['Address1'] = $data->Address1;
			}
			$empdetailsdet[$i]['nickname'] = $data->nickname;
			$empdetailsdet[$i]['Mobile1'] = $data->Mobile1;
			$empdetailsdet[$i]['DOJ'] = $data->DOJ;
			$empdetailsdet[$i]['DOB'] = $data->DOB;
			$empdetailsdet[$i]['Emp_ID'] = $data->Emp_ID;
			$empdetailsdet[$i]['Emailpersonal'] = $data->Emailpersonal;
	    	$cusexpdetails = Staff::getYrMonCountBtwnDates($empdetailsdet[$i]['DOJ'],'');
	    	if ($cusexpdetails['year'].".".$cusexpdetails['month'] == 0.0) {
				$empdetailsdet[$i]['experience'] = "0.0";
			} else {
				$empdetailsdet[$i]['experience'] = $cusexpdetails['year'].".".Staff::fnAddZeroSubstring($cusexpdetails['month']);
			}
			$cusname=Staff::fnGetcusname($request,$empdetailsdet[$i]['Emp_ID']);
			foreach($cusname as $key=>$value) {
				$empdetailsdet[$i]['customer_name'] = $value->customer_name;
			}
		$i++;	
		}
		return view('NonStaff.index',['request' => $request,
									  'sortMargin' => $sortMargin,
									  'array' => $array,
									  'noimage' => $noimage,
									  'src' => $src,
									  'file' => $file,
									  'uploadcheck' => $uploadcheck,
									  'resignid' => $resignid,
									  'disPath' => $disPath,
									  'getNSTdetails' => $getNSTdetails,
									  'empdetailsdet' => $empdetailsdet]);
	}
	function nonstaffadd(Request $request) {
		$filepath = "../resources/assets/images/upload/thumbnail/";
		$destinationPath = '../resources/assets/images/upload';
		$dob_year = "";
		$staffview = NonStaff::viewdetails($request->editid);
		$dob_year = Carbon\Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
		$dob_year   = $dob_year->subYears(18);
		$dob_year = $dob_year->format('Y-m-d');
		return view('NonStaff.add',['request' =>$request,
								'filepath' => $filepath,
								'staffview' => $staffview,
								'dob_year' => $dob_year]);
	}
	function nonstfaddeditprocess(Request $request) { 
		$nonstaffID = "NST00001";
		$empIdcnt = NonStaff::empidprocess();
		$empcnt = count($empIdcnt[0]->nstid);
		if($empcnt == 0){
			$EmployeeId = $nonstaffID;
		} else {
			$EmployeeId	= $empIdcnt[0]->nstid;
		}
		$filename1 = "";
		$extension1="";
		$file = "";
		if (Input::hasFile('picture') != "") { 
			$destinationPath = 'resources/assets/images/upload';
			$file=$request->picture;
			$extension1 = Input::file('picture')->getClientOriginalExtension();
			if ($request->hdnempid != "") {
				$empId = $request->hdnempid;
			} else {
				$empId = $EmployeeId;
			}
			$filename1=$empId.'.'.$extension1;
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}
			chmod($destinationPath, 0777);
			$file->move($destinationPath,$filename1);
			chmod($destinationPath."/".$filename1, 0777);
			$desthumbPath = 'resources/assets/images/upload/thumbnail';
			if(!is_dir($desthumbPath)) {
				mkdir($desthumbPath, 0777, true);
			}
			$extension1 = Input::file('picture')->getClientOriginalExtension();
			$savedimage=File::get($destinationPath.'/'.$filename1);
			$pict=$filename1;
			$max_upload_width = '120';
			$max_upload_height = '120';
			$pict =NonStaff::thumbnailUpload($pict,$destinationPath,$desthumbPath,$max_upload_width,$max_upload_height);
		} elseif($request->pdffiles !="") {
			$filename1 = $request->pdffiles;
		}
		if(Session::get('viewid') !=""){
			$request->viewid = Session::get('viewid');
		}
		if($request->editid!="") {
			$update = NonStaff::updateprocess($request, $filename1);
		if($update){
			Session::flash('success', 'NonStaff Updated Sucessfully!');
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('success', 'NonStaff Updated UnSucessfully!'); 
			Session::flash('type', 'alert-success'); 
		}
			Session::flash('viewid', $request->viewid);
		} else {
		$resignid = "";
		//$autoincId=staff::getautoincrement();
		//$passid=$autoincId;
		$insert = NonStaff::addprocess($request, $filename1,$EmployeeId);
		if($insert){
			Session::flash('success', 'NonStaff inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('success', 'NonStaff inserted UnSucessfully!');
			Session::flash('type', 'alert-success');
		}
			Session::flash('viewid', $EmployeeId);
		}
			return Redirect::to('NonStaff/nonstaffview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function nonstaffview(Request $request) {
		if(Session::get('viewid') !="" && Session::get('resignid') !=""){
			$request->viewid = Session::get('viewid');
			$request->resignid = Session::get('resignid');
		} else {
			if(Session::get('viewid') !="") {
				$request->viewid = Session::get('viewid');
				$request->resignid="";
			}
		}
		if(!isset($request->viewid)){
			return $this->index($request);
		}
		$file = "../resources/assets/images/upload/";
		$noimage = "../resources/assets/images";
		$src = "";
		//query to get the data
		$staffdetail=NonStaff::fnGetnonstaffDetail($request);
		return view('NonStaff.nonstaffview',['staffdetail' => $staffdetail,
											'file' => $file,
											'src' => $src,
											'noimage' => $noimage,
											'request' => $request]);
		}
}