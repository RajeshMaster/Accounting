<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ExternalUser;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
use Illuminate\Support\Facades\Validator;

class ExternalUserController extends Controller {

	/**
	*
	* Get User Process
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
		$userdetails = ExternalUser::getUserDetails($request);


		//returning to view page
		return view('ExternalUser.index', [ 'request' => $request,
											'userdetails' => $userdetails
										]);

	}

	/**
	*
	* Add Edit Page for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function addedit(Request $request) {

		if(!isset($request->editflg)){
			return Redirect::to('ExternalUser/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$userview = ExternalUser::viewUserDetails($request->editId);

		$jpnaccounttype = ExternalUser::getJapanAccount();

		return view('ExternalUser.addedit', ['request' => $request,
												'userview' => $userview,
												'jpnaccounttype' => $jpnaccounttype
											]);

	}

	/**
	*
	* Addedit Process for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function addeditprocess(Request $request) {

		if($request->editId != "") {

			$update = ExternalUser::updateUser($request);
			Session::flash('viewId', $request->editId); 

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {

			$autoincId = ExternalUser::getautoincrement();
			$insert = ExternalUser::insertUser($request);
			Session::flash('viewId', $autoincId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		}

		return Redirect::to('ExternalUser/userView?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* View Process for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function userView(Request $request) {

		if(Session::get('viewId') != ""){
			$request->viewId = Session::get('viewId');
		}

		//ON URL ENTER REDIRECT TO INDEX PAGE
		if(!isset($request->viewId)){
			return Redirect::to('ExternalUser/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$userview = ExternalUser::viewUserDetails($request->viewId);

		return view('ExternalUser.view', [	'request' => $request,
											'userview' => $userview
										]);

	}

	/**
	*
	* Mail Exists Process for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function emailIdExists(Request $request){

		$emailIdExists = ExternalUser::getemailIdExists($request);

		if (count($emailIdExists) != 0) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}

	}

	/**
	*
	* Change DelFlg Process for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/16
	*
	*/
	public function changeDelFlg(Request $request){

		$changeDelFlg = ExternalUser::changeDelFlg($request);

		return Redirect::to('ExternalUser/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

}