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

		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('CreditCardPay.index',[ 'request' => $request
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
		print_r($request->all());exit;
		$sheetData = array();
        if (($handle = fopen($_FILES["fileToUpload"]["tmp_name"], "r")) !== FALSE) 
        {
            while (($dat = fgetcsv($handle, 1000, ",")) !== FALSE) 
            {
                $sheetData[] = $dat;
            }
            fclose($handle);
        }


        return view('CreditCardPay.addedit',[ 'request' => $request,
												'creditcard' => $creditcard
										]);
	}
}