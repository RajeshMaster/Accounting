<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ExternalBank;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
use Illuminate\Support\Facades\Validator;

class ExternalBankController extends Controller {

	/**
	*
	* Get Bank Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function index(Request $request) {

		if ($request->plimit == "") {
			$request->plimit = 50;
		}

		//Query to get data
		$bankdetails = ExternalBank::getbankDetails($request);


		//returning to view page
		return view('ExternalBank.index', [ 'request' => $request,
											'bankdetails' => $bankdetails
										]);

	}

	/**
	*
	* Add Edit Page for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function addedit(Request $request) {

		if(!isset($request->editflg)){
			return Redirect::to('ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$bankview = ExternalBank::viewBankDetails($request->editId);

		$jpnaccounttype = ExternalBank::getJapanAccount();

		return view('ExternalBank.addedit', ['request' => $request,
												'bankview' => $bankview,
												'jpnaccounttype' => $jpnaccounttype
											]);

	}

	/**
	*
	* Addedit Process for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function addeditprocess(Request $request) {

		if($request->editId != "") {

			$update = ExternalBank::updateBank($request);
			Session::flash('viewId', $request->editId); 

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {

			$autoincId = ExternalBank::getautoincrement();
			$insert = ExternalBank::insertBank($request);
			Session::flash('viewId', $autoincId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		}

		return Redirect::to('ExternalBank/bankView?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* View Process for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function BankView(Request $request) {

		if(Session::get('viewId') != ""){
			$request->viewId = Session::get('viewId');
		}

		//ON URL ENTER REDIRECT TO INDEX PAGE
		if(!isset($request->viewId)){
			return Redirect::to('ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$bankview = ExternalBank::viewBankDetails($request->viewId);

		return view('ExternalBank.view', [	'request' => $request,
											'bankview' => $bankview
										]);

	}

	/**
	*
	* Mail Exists Process for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function accountNoExists(Request $request){

		$accountNoExists = ExternalBank::getaccountNoExists($request);

		if (count($accountNoExists) != 0) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}

	}

	/**
	*
	* Change DelFlg Process for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/16
	*
	*/
	public function changeDelFlg(Request $request){

		$changeDelFlg = ExternalBank::changeDelFlg($request);

		return Redirect::to('ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* Change MainFlg Process for Bank
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/16
	*
	*/
	public function changeMainFlg(Request $request){

		$changeMainFlg = ExternalBank::changeMainFlg($request);

		return Redirect::to('ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

}