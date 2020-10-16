<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Model\Forgetpassword;
use Illuminate\Support\Facades\Validator;
use App\Model\SendingMail;
use App\User;
use Input;
use Redirect;
use Session;

class LoginController extends Controller
{
	public function index()
	{
		return view('login.login');
	}
	public function authenticate(Request $request)
	{
		$userdata = array(
		'userid' => Input::get('userid'),
		'password' => Input::get('password'),
		'delflg' => 0,   // For Resigned Users Not Logged in By Easa at 2018-01-06
		);
		if (Auth::validate($userdata)) {
			if (Auth::attempt($userdata)) {
				$getscreenname=User::fnfetchscreenname();
				Session::put('userid',Auth::user()->userid);
				Session::put('username',Auth::user()->username);
				Session::put('usercode',Auth::user()->usercode);
	        	Session::put('givenname',Auth::user()->givenname);
	        	Session::put('FirstName',Auth::user()->givenname);
	        	Session::put('LastName',Auth::user()->username);
	        	Session::put('nickName',Auth::user()->nickName);
	        	Session::put('sessionfrommail',Auth::user()->email);
	        	Session::put('userclassification',Auth::user()->userclassification);
	        	Session::put('accessDate',Auth::user()->accessDate); // FOR CONTRACT EMP ACCESS RIGHTS.
	        	Session::put('systemname',$getscreenname[0]->systemname);
		        Session::put('langFlg',Auth::user()->langFlg);
	        	
		        if(Session::get('langFlg')==0){
		        	Session::put('languages','jp');
		        	Session::put('languageval', 'jp');
	   				Session::put('setlanguageval', 'en');
		        } else {
		        	Session::put('languages','en');
		        	Session::put('languageval', 'en');
	   				Session::put('setlanguageval', 'jp');
		        }

	        	// print_r(Auth::user()->FirstName);exit;
				return Redirect::to('Menu/index?mainmenu=home&time='.date('YmdHis'));
			}
		} else {
			// if any error send back with message.
			Session::flash('error', 'This Credential Does Not Match');
			return Redirect::to('/');
		}
	}
	public function logout(Request $request) {
		Auth::logout();
		Session::flush();
		return redirect('/');
	}
	public function forgetpassword(Request $request) {
		return view('login.forgetpassword',['request' => $request]);
	}
	public function formValidation(Request $request) {
		$rules = array();
	    if (strlen($request->email) <= 10) {
	    	$rules = array('email' => 'required');
	    } else {
	      	$rules = array('email' => 'required|email');
	    }
	    $customizedNames = array(
	         'email' => trans('Email / UserID'),
	    );
	    $validator = Validator::make($request->all(), $rules);
	    $validator->setAttributeNames($customizedNames);
	    if ($validator->fails()) {
	        return response()->json($validator->messages(), 200);exit;
	    } else {
	    	$success = true;
	        echo json_encode($success);exit;
	    }
    }
    public function addeditprocess(Request $request) {
		$msgid = Forgetpassword::getValidUserID($request);
		$userid = $msgid[0]->userid;
		if ($msgid[0]->total == "" || $msgid[0]->total == 0) {
			Session::flash('message', 'Please Enter Valid User Id Or Email.'); 
          	Session::flash('type', 'alert-danger');
          	return Redirect::to('/forgetpassword');
		} else {
			$password=self::rand_string(6);
			$update = Forgetpassword::updatepass($password,$userid,$request);
			if ($update !="") {
				Session::flash('message', 'Your Password Successfully Reset!'); 
          		Session::flash('type', 'alert-success');
          		$pass=$password;
          		$data=Forgetpassword::getMailId($request);
          		$val1=Forgetpassword::getMailemppersonId($userid,$request);
          		$body1=Forgetpassword::getMailContentemp($val1[0]->userid,$pass);
          		$body2="Dear"." ".$val1[0]->username." ".$val1[0]->givenname."\r\n";
          		$bodyrep = str_replace('AAAA',$body1, $data);
          		$mailformat = [$body2.$bodyrep];
          		$mail=SendingMail::sendIntimationMail($mailformat,$val1[0]->email,"Successfully Password Changed");
          		if ($mail) {
          			Session::flash('message', 'Please Check Your Mail to know the Login Details.'); 
          			Session::flash('type', 'alert-success');
          			return Redirect::to('/');
          		} else {
          			Session::flash('message', 'But Mail has not been Send.'); 
          			Session::flash('type', 'alert-danger');
          			return Redirect::to('/forgetpassword');
          		}
			}
		}
   	}
   	public function rand_string($length) {
		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$size = strlen( $chars );
		$str="";
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}
}