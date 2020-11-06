<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\CreditCardPay;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Fpdf;
use Fpdi;
use Excel;

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

		$creditcardDetails = CreditCardPay::fetchcreditcarddetails($request);
		
		return view('CreditCardPay.index',[ 'request' => $request,
											'creditcardDetails' => $creditcardDetails,
										]);
	}

	public function addedit(Request $request) {

		$creditcard = CreditCardPay::fetchcreditCardnames();
		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('CreditCardPay.addedit',[ 'request' => $request,
												'creditcard' => $creditcard
										]);
	}

	public function addeditprocess(Request $request) {

		$sheetData = array();
		$categoryName = CreditCardPay::fetchcategorynames();

		$sjis = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
		$utf8 = mb_convert_encoding($sjis, 'UTF-8', 'SJIS-win');
		file_put_contents('utf8.csv', $utf8);
		if (($handle = fopen('utf8.csv', "r")) !== FALSE) {
			while (($dat = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$sheetData[] = $dat;
			}
			fclose($handle);
		}
		return view('CreditCardPay.creditCardDetail',[
											'request' => $request,
											'categoryName' => $categoryName,
											'sheetData' => $sheetData
										]);
	}

	public function creditCardAddDtls(Request $request) {
		$insertProcess = CreditCardPay::inscreditCardDtls($request);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}