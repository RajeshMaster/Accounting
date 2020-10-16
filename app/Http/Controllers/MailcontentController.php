<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Mailcontent;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;

class MailcontentController extends Controller {
	function index(Request $request) {
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		if(isset($request->eid)) {
				$updatedefaults=Mailcontent::fnupdatedefaults($request);
			if($updatedefaults) {
			Session::flash('success', 'Default Mail Changed Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
			} else {
			Session::flash('type', 'Default Mail Changed Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
			}
		}
		$getmaildetails=Mailcontent::fnfetchmailcontent($request);
		return view('Mailcontent.index',[
										'getmaildetails' => $getmaildetails,
										'request' => $request]);
	}
	function addedit(Request $request) {
		if (!isset($request->editflg)) {
			return Redirect::to('Mailcontent/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$getmailtypes=Mailcontent::fnfetchmailtypes($request);
		if ($request->editflg == 1) {
			$getdataforupdate=Mailcontent::fnfetchupdatedata($request);
		} else {
			$getdataforupdate=array();
		}
		return view('Mailcontent.addedit',[
											'getmailtypes' => $getmailtypes,
											'getdataforupdate' => $getdataforupdate,
											'request' => $request]);
	}
	function addeditprocess(Request $request) {
			$getmailtypeid="";
			if ($request->mailtype == 999) {
				$insertnewmailtype=Mailcontent::fninsertnewmailtype($request);
				$getmailtypeid=Mailcontent::fnfetchlastmailtypeid();
			}
		if ($request->editflg == 1) {
			$updatemailcontent=Mailcontent::fnupdatenewmailcontent($request,$getmailtypeid);
			if($updatemailcontent) {
			Session::flash('success', 'Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
			} else {
			Session::flash('type', 'Updated Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
			}
		} else { 
			$getmaileid=Mailcontent::fnfetchmailid();
			$insertmailcontent=Mailcontent::fninsertnewmailcontent($request,$getmaileid,$getmailtypeid);
			if($insertmailcontent) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
			} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
			}
		}
		if ($request->editflg == 1) {
			Session::flash('qid', $request->emailid );
			return Redirect::to('Mailcontent/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		} else { 
			$request->emailid=Mailcontent::fnfetchlastmaid();
			Session::flash('qrids', $request->emailid );
			return Redirect::to('Mailcontent/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
	}
	function view(Request $request) {
		if(Session::get('qid') !=""){
          $request->emailid = Session::get('qid');
      	}
      	if(Session::get('qrids') !=""){
          $request->emailid = Session::get('qrids');
      	}
      	if (!isset($request->emailid)) {
			return Redirect::to('Mailcontent/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$getmaildetails=Mailcontent::fnfetchviewmailcontent($request);
			return view('Mailcontent.view',[
											'getmaildetails' => $getmaildetails,
											'request' => $request]);
	}
}