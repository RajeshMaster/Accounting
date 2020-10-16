<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bank;
use DB;
use Input;
use Redirect;
use Session;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller {
	function index(Request $request) { 
		// FILTER DISABLED
		$disabledemp = '';
		$disabledres = '';
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		// FILTERING PROCESS
		if (!isset($request->filterval) || $request->filterval == "") {
			$request->filterval = 1;
		}
		if ($request->filterval == 2) {
			$disabledres="disabled fb";
		} elseif ($request->filterval == 1) {
			$disabledemp="disabled fb";
		}
		// END FILTERING PROCESS
		//FLAG CHANGE
		if(isset($request->loc)){
			Session::flash('sid', $request->sid );
			$selectByMainflg = Bank::selectByMainflg($request);
			// $selectByallMainflg = Bank::selectByallMainflg($request);
			if($selectByMainflg) {
				Session::flash('message', 'Main Flag Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else { 
				Session::flash('type', 'Main Flag Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		//END FLAG CHANGE
		$name = "";
		$userID = "";
		$name = Session::get('givenname');
		$userID = Session::get('userid');
		$index = Bank::index($request);
		$empdetails = array();
		$bankdtls = array();
		$branchdtls = array();
		$i = 0;
		foreach($index as $key=>$data) {
			$empdetails[$i]['AccNo'] = $data->AccNo;
			$empdetails[$i]['FirstName'] = $data->FirstName;
			$empdetails[$i]['Bank_NickName']=$data->Bank_NickName;
			$empdetails[$i]['Location']=$data->Location;
			$empdetails[$i]['BankName']=$data->BankName;
			$empdetails[$i]['BranchName']=$data->BranchName;
			$empdetails[$i]['BranchNo']=$data->BranchNo;
			$empdetails[$i]['Type']=$data->Type;
			$empdetails[$i]['mainFlg']=$data->mainFlg;
			$empdetails[$i]['Location']=$data->Location;
			$empdetails[$i]['id']=$data->id;

			$selectbankName=Bank::selectbankName($request,$empdetails[$i]['BankName']);
			foreach($selectbankName as $key=>$rec) { 
				$empdetails[$i]['BankName']=$rec->BankName;
			}
			$selectbranchName=Bank::selectbranchname($request,$empdetails[$i]['BranchName']);
			foreach($selectbranchName as $key=>$rec) { 
				$empdetails[$i]['BranchName']=$rec->BranchName;
				$empdetails[$i]['BranchNo']=$rec->BranchNo;
			}
			$i++;
		}
		return view('Bank.index',['index' => $index,
									'disabledemp' => $disabledemp,
									'disabledres' => $disabledres,
									'empdetails' => $empdetails,
									'request' => $request]);
	}
	public function Singleview(Request $request) {
		if(Session::get('id') !=""){
			$request->id = Session::get('id');
		}
		if(!isset($request->id)){
			return $this->index($request);
		}
		$view = Bank::singledetailview($request);
		return view('Bank.Singleview',['view' => $view
										,'request' => $request]);
	}
	public function addedit(Request $request) {
		$getempdtls="";
		$getempdetails="";
		$location="";
		$jpnaccounttype=Bank::getJapanAccount();
		$indiaaccounttype=Bank::getIndianAccount();
		if(isset($request->flg)){
			$getdetails=bank::getempbankdetails($request);
			if (isset($getdetails[0])) {
				$getempdetails = $getdetails[0];
				$location = $getdetails[0]->Location;
			}
			return view('Bank.addedit',['request' => $request,
										'jpnaccounttype' => $jpnaccounttype,
										'indiaaccounttype' => $indiaaccounttype,
										'location' => $location,
										'getdetails'=>$getempdetails]);
			}else{
				return view('Bank.addedit',['request' => $request,
											'jpnaccounttype' => $jpnaccounttype,
											'indiaaccounttype' => $indiaaccounttype
											]);
		}
	}
	public function banknamepopup(Request $request) {
		$details=bank::selectbankaccNo($request);
		return view('Bank.banknamepopup',['request' => $request,
											'details'=>$details]);
	}
	public function bankadd(Request $request) {
		$orderid = Bank::Orderidgenerate($request,1);
		$ins_query=Bank::insertBankname($request,$orderid);
		return  $currid = Bank::Orderidgenerate($request);
	}
	public function branchadd(Request $request) {
		$orderid = Bank::Orderidgenerate($request,2);
		$ins_query=Bank::insertBranchname($request,$orderid);
		return  $currid = Bank::Ordergeneration($request);
	}
	public function branchnamepopup(Request $request) {
		$bankname = "";
		$details=Bank::mstbankbranch($request);
		$getBankname = Bank::fetchBankname($request);
		if (isset($getBankname)) {
			$bankname = $getBankname[0]->BankName;
		} 
		return view('Bank.branchnamepopup',['request' => $request,
											'bankname' => $bankname,
											'details'=> $details]);
	}
	function addeditprocess(Request $request) {
		//print_r($_REQUEST);exit();
		$filecnt=Bank::bankcount($request->nation,$request);
		$maxx=count($filecnt);
		if($maxx=="") {
			$mainFlg=1;
		}else {
			$mainFlg=0;
		}
		if($request->editid!="") {
			$update = Bank::updaterec($request);
			if (empty($request->branid)) {
				$branch = $request->branchuid;
			} else {
				$branch = $request->branid;
			}
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->editid );
		}else { 
			$getbranchid = bank::fetchbankid($request);
			$branchid = "";
			if (isset($getbranchid[0]->id)) {
				$branchid = $getbranchid[0]->id;
			} else {
				$branchid = $request->branchid;
			}   
			$insert = Bank::insertRec($request,$mainFlg,$branchid);
			$getmaxid = Bank::fetchmaxid($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $getmaxid );
		}
		return Redirect::to('Bank/Singleview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function branch_ajax(Request $request) {
		$id = $_REQUEST['locid'];
		if($id==2) {
			$acctype=Bank::getJapanAccount();
		} else {
			$acctype=Bank::getIndianAccount();
		}
		$branchquery=json_encode($acctype);
		echo $branchquery;exit;
	}
}