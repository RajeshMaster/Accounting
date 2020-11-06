<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bankdetail;
use App\Model\AccBankDetail;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Fpdf;
use Fpdi;
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use Carbon;
use PHPExcel_Style_Conditional;
use PHPExcel_Style_Color;

class CreditCardPayController extends Controller {

	/**
	*
	* Get  Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	public function index(Request $request) {

		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('CreditCardPay.index',[ 'request' => $request
										]);
	}

	public function addedit(Request $request) {
		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('CreditCardPay.addedit',[ 'request' => $request
										]);
	}
}