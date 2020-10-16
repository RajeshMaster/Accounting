<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Staff;
use DB;
use Input;
use Redirect;
use Session;
use Config;
use Carbon;

class StaffController extends Controller {
	function index(Request $request) {
		
		$disabledEmp= "";
		$disabledNotEmp= "";
		$disabledRes= "";
		//$request="1";
	
	//Filter process
		if (empty($request->resignid)) {
			$resignid = 0;
			if (!empty($request->title) && $request->title != 2) {
				$title = 3;
				$disabledNotEmp= "disabled";
			} else {
				$title = 2;
				$disabledEmp= "disabled";
			}
		} else {
			$resignid = 1;
			$title = ""; 
			$disabledRes= "disabled";
		}

		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		
	//SORTING PROCESS
        if ($request->staffsort == "") {
        	$request->staffsort = "Emp_ID";
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
          $sortMargin = "margin-right:200px;";
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
		$file = "../resources/assets/images/upload/";
		$disPath = "./resources/assets/images/upload/";
		$filename = "";
		$empdetailsdet=array();
		//query to get the data
		$empdetails=Staff::fnGetEmployeeDetails($request, $resignid,$title);
		//print_r($empdetails);
		$i = 0;
		foreach($empdetails as $key=>$data) {
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
	    	// print_r($cusexpdetails);
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
		$detailage = Staff::GetAvgage($resignid);
		return view('Staff.index',['empdetails' => $empdetails,
									'array' => $array,
									'sortMargin' => $sortMargin,
									'noimage' => $noimage,
									'src' => $src,
									'resignid' => $resignid,
									'file' => $file,
									'filename' => $filename,
									'disPath' => $disPath,
								    'request' => $request,
								    'detailage' => $detailage,
								    'empdetailsdet' => $empdetailsdet,
								    'disabledEmp'	=>$disabledEmp,
								    'disabledNotEmp'=>$disabledNotEmp,
									'disabledRes'	=>$disabledRes]);
	}
	function staffaddedit(Request $request) {
		// To View the picture in the path
		$filepath = "../resources/assets/images/upload/";
		$check = "";
		$dob_year = "";
		/*if(!isset($request->editflg)) {
			return view('Staff.addedit',['request' =>$request]);
		}*/
		$staffview = staff::viewdetails($request->editid);
		$dob_year = Carbon\Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
		$dob_year   = $dob_year->subYears(18);
		$dob_year = $dob_year->format('Y-m-d');
		return view('Staff.addedit',['request' =>$request,
									 'staffview' => $staffview,
									 'filepath' => $filepath,
									 'check' => $check,
									 'dob_year' => $dob_year]);
	}
	function addeditprocess(Request $request) { 
		// print_r($_REQUEST);exit();
	//For Upload a Picture
		if ($request->picture !="") {
			if($request->editid!="") {
				$empId = $request->hdnempid;
			} else {
				$empId = $request->EmployeeId;
			}
			$extension = Input::file("picture")->getClientOriginalExtension();
			$filename= $empId.'.'.$extension;
			$file = $request->picture;
			$destinationPath = 'resources/assets/images/upload';
	            if(!is_dir($destinationPath)) {
	            	mkdir($destinationPath, true);
	            }
	            chmod($destinationPath, 0777);
	            $file->move($destinationPath,$filename);
	            chmod($destinationPath."/".$filename, 0777);
	            $imagename=$filename;
        } else {
	        $imagename=$request->pictureId;
        	$filename ="";
        }
        if(Session::get('viewid') !=""){
		        $request->viewid = Session::get('viewid');
	    	}
		if($request->editid!="") {
			$update = staff::updateprocess($request, $imagename);
			//print_r($update);exit();
			if($update){
		    	Session::flash('success', 'Employee Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    } else {
		    	Session::flash('success', 'Employee Updated UnSucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    }
			Session::flash('viewid', $request->viewid);
		} else {
			$resignid = "";
			$autoincId=staff::getautoincrement();
			$passid=$autoincId;
			$insert = staff::addeditprocess($request, $imagename);
			if($insert){
		    	Session::flash('success', 'Employee inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    } else {
		    	Session::flash('success', 'Employee inserted UnSucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    }
			Session::flash('viewid', $request->EmployeeId);
			//return Redirect::to('Staff/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
			return Redirect::to('Staff/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function view(Request $request) {
			//if($request->resignid == 1){
		if(Session::get('viewid') !="" && Session::get('resignid') !=""){
	    	$request->viewid = Session::get('viewid');
	    	$request->resignid = Session::get('resignid');
		   // }
		}else{
			if(Session::get('viewid') !=""){
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
		$staffdetail=Staff::fnGetstaffDetail($request);
		return view('Staff.view',['staffdetail' => $staffdetail,
								  'file' => $file,
								  'src' => $src,
								  'noimage' => $noimage,
								  'request' => $request]);
	}
	function importpopup(Request $request){
	//For Get The DataBase List
		$getOldDbDetails = Staff::fnOldDbDetails();
		return view('Staff.importpopup',['getOldDbDetails'=> $getOldDbDetails,
										'request' => $request]);
	}
	function importprocess(Request $request){
	//Get The Old Database Details
		$employee_count = Staff::fnGetEmployeeCount();

	//Get The New DataBase Details
		$getConnectionQuery = Staff::fnGetConnectionQuery($request);
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);
		try {
		$db = DB::connection('otherdb');
		$db->getPdo();
		if($db->getDatabaseName()){
	    
	//To Get The Latest Employee Details In New DataBase
				$oldUserQuery = Staff::fnGetEmployeeDetailsMB();
				$g_val = count($oldUserQuery);
				$getOldUserRecordsAsArray = array();
				$j = 0;
				foreach ($oldUserQuery as $key => $value1) {
					$getOldUserRecordsAsArray[$j]['Emp_ID'] = $value1->Emp_ID;
					$getOldUserRecordsAsArray[$j]['DOJ'] = $value1->DOJ;
					$getOldUserRecordsAsArray[$j]['FirstName'] = $value1->FirstName;
					$getOldUserRecordsAsArray[$j]['LastName'] = $value1->LastName;
					$getOldUserRecordsAsArray[$j]['Gender'] = $value1->Gender;
					$getOldUserRecordsAsArray[$j]['DOB'] = $value1->DOB;
					$getOldUserRecordsAsArray[$j]['Mobile1'] = $value1->Mobile1;
					$getOldUserRecordsAsArray[$j]['Emailpersonal'] = $value1->Emailpersonal;
					$getOldUserRecordsAsArray[$j]['Picture'] = $value1->Picture;

					 $return_address  =	$value1->Address1;
					// if (is_numeric(trim($return_address))) {
					// 	$exeAddressQuery = Staff::fnGetAddressMB($value1->Address1);
					// }
					$getOldUserRecordsAsArray[$j]['Address1'] = $return_address;
					$getOldUserRecordsAsArray[$j]['Ins_DT'] = $value1->Ins_DT;
					$getOldUserRecordsAsArray[$j]['Ins_TM'] = $value1->Ins_TM;
					$getOldUserRecordsAsArray[$j]['CreatedBy'] =	$value1->CreatedBy;
					$getOldUserRecordsAsArray[$j]['resign_id'] =	$value1->resign_id;
					$getOldUserRecordsAsArray[$j]['Title'] =	$value1->Title;
					$getOldUserRecordsAsArray[$j]['delFlg'] = $value1->delFlg;
					$getOldUserRecordsAsArray[$j]['UpdatedBy'] =	$value1->UpdatedBy;
					$getOldUserRecordsAsArray[$j]['Up_DT'] =	$value1->Up_DT;
					$getOldUserRecordsAsArray[$j]['Up_TM'] =	$value1->Up_TM;
					$getOldUserRecordsAsArray[$j]['KanaFirstName'] =	$value1->KanaFirstName;
					$getOldUserRecordsAsArray[$j]['KanaLastName'] = $value1->KanaLastName;
					$selectaccNo=Staff::selectaccNo($getOldUserRecordsAsArray[$j]['Emp_ID']);
					if (count($selectaccNo) > 0) {
						foreach($selectaccNo as $key=>$rec) { 
					        $getOldUserRecordsAsArray[$j]['AccNo']=$rec->AccNo;
					        $getOldUserRecordsAsArray[$j]['BankNames']=$rec->BankName;
					        $getOldUserRecordsAsArray[$j]['BranchNames']=$rec->BranchName;
					    }
					} else {
						$getOldUserRecordsAsArray[$j]['AccNo'] = "";
						$getOldUserRecordsAsArray[$j]['BankNames'] = "";
						$getOldUserRecordsAsArray[$j]['BranchNames'] = "";
					}
					$selectbankName=Staff::selectbankName($getOldUserRecordsAsArray[$j]['BankNames']);
					if (count($selectbankName) > 0) {
					    foreach($selectbankName as $key=>$rec1) { 
					        $getOldUserRecordsAsArray[$j]['BankName']=$rec1->BankName;
					    }
				    } else {
				    	$getOldUserRecordsAsArray[$j]['BankName'] = "";
				    }
				    $selectbranchName=Staff::selectbranchname($getOldUserRecordsAsArray[$j]['BranchNames']);
					if (count($selectbranchName) > 0) {
					    foreach($selectbranchName as $key=>$rec2) { 
					        $getOldUserRecordsAsArray[$j]['BranchName']=$rec2->BranchName;
					        $getOldUserRecordsAsArray[$j]['BranchNo']=$rec2->BranchNo;
					    }
				    } else {
				    	$getOldUserRecordsAsArray[$j]['BranchName'] = "";
					    $getOldUserRecordsAsArray[$j]['BranchNo'] = "";
				    }
					$j++;
				}
				// if( $g_val > $employee_count ){
					if ($getOldUserRecordsAsArray != "") {
						$getOldUserRecordAsArray = array();
						$i = 0;
						foreach ($getOldUserRecordsAsArray as $key => $value) {
							$getOldUserRecordAsArray[$i]['Emp_ID'] = $value['Emp_ID'];
							$getOldUserRecordAsArray[$i]['DOJ'] = $value['DOJ'];
							$getOldUserRecordAsArray[$i]['FirstName'] = $value['FirstName'];
							$getOldUserRecordAsArray[$i]['LastName'] = $value['LastName'];
							$getOldUserRecordAsArray[$i]['Gender'] = $value['Gender'];
							$getOldUserRecordAsArray[$i]['DOB'] = $value['DOB'];
							$getOldUserRecordAsArray[$i]['Mobile1'] = $value['Mobile1'];
							$getOldUserRecordAsArray[$i]['Emailpersonal'] = $value['Emailpersonal'];
							$getOldUserRecordAsArray[$i]['Picture'] = $value['Picture'];

							 $return_address  =	$value['Address1'];
							// if (is_numeric(trim($return_address))) {
							// 	$exeAddressQuery = Staff::fnGetAddressMB($value['Address1);
							// }
							$getOldUserRecordAsArray[$i]['Address1'] = $return_address;
							$getOldUserRecordAsArray[$i]['Ins_DT'] = $value['Ins_DT'];
							$getOldUserRecordAsArray[$i]['Ins_TM'] = $value['Ins_TM'];
							$getOldUserRecordAsArray[$i]['CreatedBy'] =	$value['CreatedBy'];
							$getOldUserRecordAsArray[$i]['resign_id'] =	$value['resign_id'];
							$getOldUserRecordAsArray[$i]['Title'] =	$value['Title'];
							$getOldUserRecordAsArray[$i]['delFlg'] = $value['delFlg'];
							$getOldUserRecordAsArray[$i]['UpdatedBy'] =	$value['UpdatedBy'];
							$getOldUserRecordAsArray[$i]['Up_DT'] =	$value['Up_DT'];
							$getOldUserRecordAsArray[$i]['Up_TM'] =	$value['Up_TM'];
							$getOldUserRecordAsArray[$i]['KanaFirstName'] =	$value['KanaFirstName'];
							$getOldUserRecordAsArray[$i]['KanaLastName'] = $value['KanaLastName'];
							$getOldUserRecordAsArray[$i]['BankName'] = $value['BankName'];
							$getOldUserRecordAsArray[$i]['BranchName'] = $value['BranchName'];
							$getOldUserRecordAsArray[$i]['AccNo'] =	$value['AccNo'];
							$getOldUserRecordAsArray[$i]['BranchNo'] =	$value['BranchNo'];
							$i++;
						}
						for ($i = 0; $i < count($getOldUserRecordAsArray); $i++) {
							$exist = Staff::fnOldTempstaffExist($getOldUserRecordAsArray[$i]["Emp_ID"]);
							$existCount = count($exist);
							if ($existCount == 0) {
								$column_name = "";
								$column_value = "";
								$fldarray = "";
								$valuearray = "";
								foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
									$fldarray[]= $key;
									$valuearray[]= $value;
								}
								$insertOldUserQuery = Staff::fnInsertOLDMBDetails($fldarray,$valuearray);
							} else {
								$tempvar=$getOldUserRecordAsArray[$i]['Emp_ID'];
								$tempTM=$getOldUserRecordAsArray[$i]['Emp_ID'];
								$column_name_value = "";
								$condition = "";
								$fldarray = "";
								$valuearray = "";
								foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
									if ($key != "Emp_ID") {
										$fldarray[]= $key;
										$valuearray[]= $value;
									}
								}
								$condition = "Emp_ID = '" . $tempvar. "'";
								$column_name_value = mb_substr($column_name_value, 0, mb_strlen($column_name_value) - 1);
								$updateOldUserQuery = Staff::fnUpdateOLDMBDetails($fldarray,$valuearray,$tempvar);
								Session::flash('success', 'Imported Sucessfully!'); 
								Session::flash('type', 'alert-success'); 
							}
						}
					} else {
						Session::flash('success', 'Record Not Imported Sucessfully'); 
						Session::flash('type', 'alert-success'); 
					}
				// } else {
				// 	 Session::flash('success', 'No New Record Found'); 
	   //            	 Session::flash('type', 'alert-danger'); 
				// }
	} else {
		Session::flash('success', 'Invalid Db Connection'); 
		Session::flash('type', 'alert-danger'); 
	}
	} catch (\Exception $e) {
        Session::flash('success', 'Invalid Db Connection.'); 
		Session::flash('type', 'alert-danger'); 
    }


	return Redirect::to('Staff/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	function rejoin(Request $request){
			$rejoin= Staff::rejoinupdate($request);
			if(Session::get('viewid') !=""){
		        $request->viewid = Session::get('viewid');
	    	}
		    if($rejoin == 1){
		    	Session::flash('success', 'Employee Rejoined Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    } else {
		    	Session::flash('success', 'Employee Rejoined UnSucessfully!'); 
				Session::flash('type', 'alert-success'); 
		    }
	    	Session::flash('viewid', $request->viewid);
			return Redirect::to('Staff/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function resign(Request $request){
		return view('Staff.resign',['request' => $request]);
	}
	function resignadd(Request $request){
		$resign= Staff::resignupdate($request);
		if(Session::get('viewid') !=""){
		        $request->viewid = Session::get('viewid');
	    }
	    if($resign == 1){
	    	Session::flash('success', 'Employee Resigned Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
	    } else {
	    	Session::flash('success', 'Employee Resigned UnSucessfully!'); 
			Session::flash('type', 'alert-success'); 
	    }
	    	Session::flash('viewid', $request->viewid);
	    	Session::flash('resignid', 1);
		return Redirect::to('Staff/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}