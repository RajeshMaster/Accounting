<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Mailstatus;
use DB;
use Input;
use Redirect;
use Session;
ini_set('max_execution_time', 0);
/*
Class: About

Some functions related to display the users list and describing their particular details.
*/
class MailstatusController extends Controller {
	function index(Request $request) { 
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		if ($request->sendfilter=="") {
			$request->sendfilter = 1;
		}
		if (empty($request->pageclick)) {
			$page_no = 1;
		} else {
			$page_no = $request->pageclick;
		}
		$disableddraft="";
		$disabledsent="";
		if($request->sendfilter == 1) {
			$disabledsent ="disabled fwb black";

		}
		if($request->sendfilter == 0) {
			$disableddraft ="disabled fwb black";
		}
		$allmailstatus = Mailstatus::getallmailstatus($request);
		return view('Mailstatus.index',['allmailstatus' => $allmailstatus,
										'disableddraft' => $disableddraft,
										'disabledsent' => $disabledsent,
										'request' => $request]);
	}
	function mailstatusview(Request $request) {
		if (!isset($request->statusid)) {
			return Redirect::to('Mailstatus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$singlemailstatus = Mailstatus::getsinglemailstatus($request);
		return view('Mailstatus.view',['singlemailstatus' => $singlemailstatus,
										'request' => $request]);

	}
	/**
	*
	* Get Particular User Data based on request
	* @author Revathi.E
	* @param request  $request The request to use inside the argument
	* @return Object to particular view page
	* Created At 2017/11/06
	* Updated At 2017/11/06
	*
	*/
	function mailhistory(Request $request) {
		if (!isset($request->customerid)) {
			return Redirect::to('Mailstatus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}

		if ($request->historyfilter == "") {
			$request->historyfilter = "1";
		}

		$disableddraft="";
		$disabledsent="";
		if($request->historyfilter == 1) {
			$disabledsent ="disabled fwb black";

		}
		if($request->historyfilter == 0) {
			$disableddraft ="disabled fwb black";
		}
		$mailhistoryview = Mailstatus::getallmailstatus($request);
		return view('Mailstatus.mailhistory',['mailhistoryview' => $mailhistoryview,
												'disableddraft' => $disableddraft,
												'disabledsent' => $disabledsent,
												'request' => $request]);
	}
}