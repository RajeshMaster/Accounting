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
		$cashDetails = Accounting::fetchcashRegister();

		return view('Accounting.index',['request' => $request,
										'cashDetails' => $cashDetails]);
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

	public function addeditprocess(Request $request) {

		$insertProcess = Accounting::fninsert($request);
		print_r($insertProcess);exit;

		print_r($request->all());exit;
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
	* Addedit Page for Transfer
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
											'empnamenonstaff' => $empnamenonstaff,
										]);
	}

	/**
	*
	* Addedit Page for Transfer
	* @author Sarath
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function tranferaddeditprocess(Request $request) {

		$Loanno = "Expenses_".date('YmdHis');
		$fileid = "file1";
			$filename="";
			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$filename=$Loanno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../InvoiceUpload/Expenses';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, true);
				}
				chmod($destinationPath, 0777);
				$file->move($destinationPath,$filename);
				chmod($destinationPath."/".$filename, 0777);
			} else {
				$filename = $request->pdffiles; 
			}
			$insert = Accounting::inserttransferdetails($request,$filename);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		return Redirect::to('Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

}