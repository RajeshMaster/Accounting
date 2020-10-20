<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Accounting;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Fpdf;
use Fpdi;
require_once('vendor/setasign/fpdf/fpdf.php');
require_once('vendor/setasign/fpdi/fpdi.php');
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use Carbon;
use PHPExcel_Style_Conditional;
use PHPExcel_Style_Color;

class AccountingController extends Controller {

	/**
	*
	* Get  Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function index(Request $request) {
		$cashDetailsIndex = Accounting::fetchcashRegister();
		$cashDetails =array();
		$i = 0;
		foreach ($cashDetailsIndex as $key => $value) {
			$cashDetails[$i]['date'] = $value->date;
			$cashDetails[$i]['content'] = $value->content;
			$cashDetails[$i]['amount'] = $value->amount;
			$cashDetails[$i]['fee'] = $value->fee;
			$cashDetails[$i]['Bank_NickName'] = $value->Bank_NickName;
			$cashDetails[$i]['transcationType'] = $value->transcationType;
			$cashDetails[$i]['remarks'] = $value->remarks;
			$cashDetails[$i]['baseAmt'] = 0;
			$baseAmt = Accounting::baseAmt($value->bankIdFrom,$value->accountNumberFrom);
			if (isset($baseAmt[0]->amount)) {
				$cashDetails[$i]['baseAmt'] = $baseAmt[0]->amount;
			}
			$i++;
		}
		return view('Accounting.index',['request' => $request,
										'cashDetails' => $cashDetails,
										'cashDetailsIndex' => $cashDetailsIndex]);
	}

	/**
	*
	* Addedit Page for Cash
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function addedit(Request $request) {

		$bankDetail = Accounting::fetchbanknames();

		return view('Accounting.addedit',['request' => $request,
											'bankDetail' => $bankDetail
										]);
	}

	/**
	*
	* Get banck Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function bank_ajax(Request $request) {

		$bankacc = $request->bankacc;
		$getbankDtl = Accounting::fnGetbankName($bankacc);
		$bankarray = json_encode($getbankDtl);
		echo $bankarray;
		exit();

	}

	/**
	*
	* Addedit Process for Cash
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function addeditprocess(Request $request) {
		if($request->transtype != 3){
			$insertProcess = Accounting::insCashDtls($request);
		} else {
			$insertProcess = Accounting::insCashreduction($request ,1);
			$insertProcess = Accounting::insCashreduction($request,2);
		}
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* Addedit Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function transferaddedit(Request $request) {

		$bankDetail = Accounting::fetchbanknames();
		$mainExpDetail = Accounting::getMainExpName();

		return view('Accounting.transferaddedit',[ 'request' => $request,
													'mainExpDetail' => $mainExpDetail,
													'bankDetail' => $bankDetail
												]);
	}

	/**
	*
	* Emp Name Popup Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function empnamepopup(Request $request) {

		$empname = Accounting::fnGetEmpDetails($request);
		$empnamenonstaff = Accounting::fnGetNonstaffEmpDetails($request);

		return view('Invoice.empnamepopup',['request' => $request,
											'empname' => $empname,
											'empnamenonstaff' => $empnamenonstaff
										]);
	}

	/**
	*
	* Addedit Process for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function tranferaddeditprocess(Request $request) {

		$autoincId = Accounting::getautoincrement();
		$Transferno = "Transfer_".$autoincId;
		$fileName = "";
		$fileid = "transferBill";
		if($request->$fileid != "") {
			$extension = Input::file($fileid)->getClientOriginalExtension();
			$fileName = $Transferno.'.'.$extension;
			$file = $request->$fileid;
			$destinationPath = '../AccountingUpload/Accounting';
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777,true);
			}
			$file->move($destinationPath,$fileName);
		} else {
			$fileName = ""; 
		}
		$insertProcess = Accounting::insTransferDtls($request,$fileName);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Salary Details Popup Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function getsalarypopup(Request $request) {

		return view('Accounting.salarydetailspopup',['request' => $request]);
	}

	/**
	*
	* Addedit Page for AutoDebit
	* @author Sastha
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function autoDebitReg(Request $request) {

		$bankDetail = Accounting::fetchbanknames();
		$mainExpDetail = Accounting::getMainExpName();

		return view('Accounting.autoDebitReg',['request' => $request,
												'bankDetail' => $bankDetail,
												'mainExpDetail' => $mainExpDetail
											]);
		
	}

	/**
	*
	* Addedit Process for Auto Debit
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function AutoDebitRegprocess(Request $request) {

		$autoincId = Accounting::getautoincrement();
		$Transferno = "AutoDebit_".$autoincId;
		$fileName = "";
		$fileid = "autoDebitBill";
		if($request->$fileid != "") {
			$extension = Input::file($fileid)->getClientOriginalExtension();
			$fileName = $Transferno.'.'.$extension;
			$file = $request->$fileid;
			$destinationPath = '../AccountingUpload/Accounting';
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777,true);
			}
			$file->move($destinationPath,$fileName);
		} else {
			$fileName = ""; 
		}
		$insertProcess = Accounting::insAutoDebitDtls($request,$fileName);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Loan Details Popup Page for AutoDebit
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function getloanpopup(Request $request) {

		return view('Accounting.loandetailspopup',['request' => $request]);
	}


}