<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Mailsignature;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;

class MailsignatureController extends Controller {
	function index(Request $request) {
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		$getlist=Mailsignature::fnfetchmailsignature($request);
		return view('Mailsignature.index',['request' => $request,
											'getlist' => $getlist]);
	}
	function addedit(Request $request) {
		if (!isset($request->editflg)) {
			return Redirect::to('Mailsignature/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		if ($request->editflg == 1) {
			$getdataforupdate=Mailsignature::fnfetchupdatedata($request);
			$getname = $getdataforupdate[0]->username." ".$getdataforupdate[0]->givenname." ".$getdataforupdate[0]->nickName;
		} else {
			$getdataforupdate=array();
			$getname = "";
		}
		return view('Mailsignature.addedit',['request' => $request,
											'getname' => $getname,
											'getdataforupdate' => $getdataforupdate]);
	}
	function mailsignaturepopup(Request $request) {
		$empname = Mailsignature::fnGetUserDetails($request);
		return view('Mailsignature.mailsignaturepopup',['request' => $request,
														'empname' => $empname]);
	}
	function addeditprocess(Request $request) {
		$signID = "SIGN00001";
		$signIdcnt = Mailsignature::signidprocess();
		$signcnt = count($signIdcnt[0]->signid);
		if($signcnt == 0){
			$signatureId = $signID;
		} else {
			$signatureId	= $signIdcnt[0]->signid;
		}
		if($request->userid == ""){
			$userid = Session::get('usercode');
		} else {
			$userid = $request->userid;
		}
		if ($request->editflg == 1 || $request->updateprocess == 2) {
			$id = "";
			if($request->updateprocess == 2) {
				$id = $request->userid;
				$update=Mailsignature::fnfetchviewdata($id);
				$id = $update[0]->signID;
			} 
			$updatemailcontent=Mailsignature::fnupdatemailsignature($request,$id);
			if($updatemailcontent) {
			Session::flash('success', 'Updated Sucessfully!');
			Session::flash('type', 'alert-success'); 
			} else {
			Session::flash('type', 'Updated Unsucessfully!');
			Session::flash('type', 'alert-danger'); 
			}
		} else {
			$insertmailsignature = Mailsignature::fninsertmailsignature($request,$signatureId,$userid);
			if($insertmailsignature) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger');
			}
		}
		if ($request->editflg == 1) {
			Session::flash('successview', $request->id );
			return Redirect::to('Mailsignature/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		} else { 
			$request->id = Mailsignature::fnfetchlastmaid();
			Session::flash('successview', $request->id );
			return Redirect::to('Mailsignature/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
	}
	public function view(Request $request) {
		$getlist = array();
		$getdata = array();
		if(Session::get('userclassification') != "4"){
			$request->signid = "";
			$id = Session::get('usercode');
			$getdata =Mailsignature::fnfetchviewdata($id);
			if(!empty($getdata)){
				$request->signid = $getdata[0]->signID;
			}
		}
		if(Session::get('successview') !=""){
			$request->signid = Session::get('successview');
		}
      	if (!isset($request->signid)) {
			return Redirect::to('Mailsignature/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$getlist=Mailsignature::fnfetchviewMailsignature($request);
		return view('Mailsignature.view',['request' => $request,
										'getlist' => $getlist]);
		
	}
	function getdatexist(Request $request){
		$dateexistcheck = Mailsignature::fnfetchmailsigdata($request);
		if (!empty($dateexistcheck)) {
			$dateexistcheck = $dateexistcheck[0];
		}
		//$dataexistchk = $dateexistcheck;
		// $dateexistcheck = $dateexistcheck;
		//print_r($dataexistchk);
		echo json_encode($dateexistcheck);exit();
	}
}