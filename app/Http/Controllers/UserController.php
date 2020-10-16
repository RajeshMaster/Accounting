<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\User;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
	function index(Request $request) {
	//Variable Declaration 
		$disabledall="";
		$disabledunused="";
		$disabledstaff="";
		$disabledcontract="";
		$disabledsubcontract="";
		$disabledprivate="";
	//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
	//Filter process
       		if (!isset($request->filterval) || $request->filterval == "") {
	        	$request->filterval = 1;
	      	}
	    	if ($request->filterval == 1) {
	        	$disabledall="disabled fb";
      		} elseif ($request->filterval == 2) {
        		$disabledunused="disabled fb";
      		} elseif ($request->filterval == 3) {
        		$disabledstaff="disabled fb";
      		} elseif ($request->filterval == 4) {
        		$disabledcontract="disabled fb";
      		} elseif ($request->filterval == 5) {
        		$disabledsubcontract="disabled fb";
      		} elseif ($request->filterval == 6) {
        		$disabledprivate="disabled fb";
      		}
    //SORTING PROCESS
    		if (!isset($request->usersort)) {
        		$request->usersort = "usercode";
      		}
      		if ($request->usersort == "") {
        		$request->usersort = "usercode";
      		}
      		if (empty($request->sortOrder)) {
        		$request->sortOrder = "asc";
      		}
      		if ($request->sortOrder == "asc") {  
      			$request->sortstyle="sort_asc";
      		} else {  
      			$request->sortstyle="sort_desc";
      		}
      		$sortarray = [$request->usersort=>$request->usersort,
                    'usercode'=> trans('messages.lbl_usercode'),
                    'usercode'=> trans('messages.lbl_usercode')];
    //SORT POSITION
        if (!empty($request->singlesearch) || $request->searchmethod == 2) {
          $sortMargin = "margin-right:260px;";
        } else {
          $sortMargin = "margin-right:0px;";
        }
    //Changing User status
        if ($request->userid) {
        	$changeuserflag=User::fnChnagingTheUserFlag($request);
        	if($changeuserflag) {
              Session::flash('success', 'User status changed Sucessfully!'); 
              Session::flash('type', 'alert-success'); 
            } else {
              Session::flash('type', 'User status change is Unsucessfully!'); 
              Session::flash('type', 'alert-danger'); 
            }
        }
    //values for multisearch select box
		$Classificationarray = array("0"=>trans('messages.lbl_staff'),
									"1"=>trans('messages.lbl_conEmployee'),
									"2"=>trans('messages.lbl_subEmployee'),
									"3"=>trans('messages.lbl_pvtPerson'),
									"4"=>trans('messages.lbl_superadmin'),);
	//Query to get data
		$userdetails=User::getUserDetails($request);
	//returning to view page
		return view('User.index',['userdetails' => $userdetails,
								  'disabledall' => $disabledall,
								  'disabledunused' => $disabledunused,
								  'disabledstaff' => $disabledstaff,
								  'disabledcontract' => $disabledcontract,
								  'disabledsubcontract' => $disabledsubcontract,
								  'disabledprivate' => $disabledprivate,
								  'sortarray' => $sortarray,
								  'Classificationarray'=>$Classificationarray,
								  'sortMargin' => $sortMargin,
								  'request' => $request]);
	}
	function addedit(Request $request) {
		if(!isset($request->editflg)){
			return $this->index($request);
		}
		$userview = User::viewdetails($request->editid);
		$dob_year = Carbon\Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
		$dob_year   = $dob_year->subYears(18);
		$dob_year = $dob_year->format('Y-m-d');
		if (Session::get('userclassification') == "4") {
			$Classificationarray = array("0"=>trans('messages.lbl_staff'),
									"1"=>trans('messages.lbl_conEmployee'),
									"2"=>trans('messages.lbl_subEmployee'),
									"3"=>trans('messages.lbl_pvtPerson'),
									"4"=>trans('messages.lbl_superadmin'),);
		} else {
			$Classificationarray = array("0"=>trans('messages.lbl_staff'),
									"1"=>trans('messages.lbl_conEmployee'),
									"2"=>trans('messages.lbl_subEmployee'),
									"3"=>trans('messages.lbl_pvtPerson'),);
		}
		return view('User.addedit',['Classificationarray' => $Classificationarray,
									'userview' => $userview,
									'request' => $request,
									'dob_year' => $dob_year]);
	}
	function addeditprocess(Request $request) {
		if($request->editid!="") {
			$update = User::UpdateReg($request);
	        Session::flash('viewid', $request->editid); 
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$autoincId=User::getautoincrement();
			$Usercode="MBINV".(str_pad($autoincId,'3','0',STR_PAD_LEFT));
			$insert = User::insertRec($request,$Usercode);
	        Session::flash('viewid', $autoincId); 
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		return Redirect::to('User/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function view(Request $request) {
		if(Session::get('viewid') !=""){
	        $request->viewid = Session::get('viewid');
	    }
	    if(Session::get('id') !=""){
	        $request->viewid = Session::get('viewid');
			Session::flash('success', 'Password Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
	    }
		//ON URL ENTER REDIRECT TO INDEX PAGE
		if(!isset($request->viewid)){
			return $this->index($request);
		}
		$userview = User::viewdetails($request->viewid);
		// For Gender
		if ($userview[0]->gender == 1) {
			$userview[0]->gender = "Male";
		} else if ($userview[0]->gender == 2) {
			$userview[0]->gender = "Female";
		}
		// For User Classification
		if ($userview[0]->userclassification == 0 && $userview[0]->delflg == 0) {
			$userview[0]->userclassification = trans('messages.lbl_staff');
		} else if ($userview[0]->userclassification == 1 && $userview[0]->delflg == 0) {
			$userview[0]->userclassification = trans('messages.lbl_conEmployee');
		} else if ($userview[0]->userclassification == 2 && $userview[0]->delflg == 0) {
			$userview[0]->userclassification = trans('messages.lbl_subEmployee');
		} else if ($userview[0]->userclassification == 3 && $userview[0]->delflg == 0) {
			$userview[0]->userclassification = trans('messages.lbl_pvtPerson');
		} else if ($userview[0]->userclassification == 4 && $userview[0]->delflg == 0) {
			$userview[0]->userclassification = trans('messages.lbl_superadmin');
		} 
		return view('User.view',['userview' => $userview,
								'request' => $request]);
	}
	function changepassword(Request $request) {
		if(!isset($request->id)){
			return $this->index($request);
		}
		$view = User::viewdetails($request->id);
		return view('User.changepassword',['view' => $view,'request' => $request]);
	}
	function passwordchangeprocess(Request $request) {
		$update = User::passwordchange($request);
		if($update) {
			Session::flash('message', 'Password Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Password Updated Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		Session::flash('viewid', $request->id);
		Session::flash('id', $request->id); 
		return Redirect::to('User/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}