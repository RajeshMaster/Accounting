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

		return view('Accounting.index',['request' => $request]);
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
											'bankDetail' => $bankDetail]);
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
												'mainExpDetail' => $mainExpDetail]);
		
	}


	public function addeditprocess(Request $request) {

		$insertProcess = Accounting::fninsert($request);
		print_r($insertProcess);exit;

		print_r($request->all());exit;
	}

}