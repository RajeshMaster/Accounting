<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Ourdetail;
use DB;
use Input;
use Redirect;
use Session;
use Illuminate\Support\Facades\Validator;

class OurdetailController extends Controller {
	function index(Request $request) { 
		$result = Ourdetail::viewdetails($request);
		$viewtaxdetails = Ourdetail::viewtaxdetails($request);
		$kessan = Ourdetail::viewkessandetails();
		// print_r($viewtaxdetails);exit;
		return view('Ourdetail.index',['result' => $result,
										'viewtaxdetails'=> $viewtaxdetails,
										'kessan' => $kessan,
										'request' => $request]);
	}
	public static function add(Request $request) {
		$result="";
		return view('Ourdetail.addedit',['result' => $result,
										// 'viewtaxdetails'=> $viewtaxdetails,
										// 'kessan' => $kessan,
										'request' => $request]);

	}
	public static function edit(Request $request) {
		$detedit = Ourdetail::viewdetails($request);
		$tel = explode("-", $detedit[0]->TEL);
		$fax = explode("-", $detedit[0]->FAX);
		return view('Ourdetail.addedit',['detedit' => $detedit,
										'tel'=> $tel,
										'fax'=> $fax,
										// 'kessan' => $kessan,
										'request' => $request]);

	}
	public static function addeditprocess(Request $request) {
		if($request->editflg == "1") {
			$update = Ourdetail::UpdateuserReg($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$insert = Ourdetail::insertuserRec($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		return Redirect::to('Ourdetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function taxpopup(Request $request) {
		return view('Ourdetail.taxpopup',['request' => $request]);
	}
	function balancesheetpopup(Request $request) {
		if($request->balid!="") {
			$edit = Ourdetail::balanceedit($request);
		} else {
			$edit = array();
		}
		$gradyear = array_combine(range(date("Y"),1975),range(date("Y"),1975));
		return view('Ourdetail.balancesheetpopup',['request' => $request,
													'edit' => $edit,
													'gradyear' => $gradyear]);
	}
	function taxprocess(Request $request) {
		$insert = Ourdetail::inserttaxRec($request);
		if($insert) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('Ourdetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function balsheetprocess(Request $request) {
		if($request->balid != "") {
			$update = Ourdetail::UpdatebalReg($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$insert = Ourdetail::insertbalRec($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		return Redirect::to('Ourdetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}