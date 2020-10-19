<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bankdetail;
use App\Model\AccBankdetail;
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

class AccBankDetailController extends Controller {

	/**
	*
	* Get  Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
	*
	*/
	function index(Request $request) {
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
			$request->page = 1;
		}
		$bankdetailindex = array();
		$j = 0;
		$index = Bankdetail::bankindex($request)->paginate($request->plimit);

		$i=0;
		foreach ($index as $key => $value) {
			$bankdetailindex[$i]['banknm'] = $value->banknm;
			$bankdetailindex[$i]['brnchnm'] = $value->brnchnm;
			$bankdetailindex[$i]['AccNo'] = $value->AccNo;
			$bankdetailindex[$i]['startDate'] = $value->startDate;
			$bankdetailindex[$i]['bankId'] = $value->bnkid;
			$bankdetailindex[$i]['brnchid'] = $value->brnchid;

			$baseAmtInsChk = array();
			$baseAmtVal = 0;
			$baseAmtInsChk = AccBankdetail::baseAmtInsChk($value->bnkid);
			if (isset($baseAmtInsChk[0])) {
				$bankdetailindex[$i]['baseAmtInsChk'] = 1;
				$baseAmtVal = $baseAmtInsChk[0]->amount;
			} else {
				$bankdetailindex[$i]['baseAmtInsChk'] = 0;
			}
			$bankrectype1 = AccBankdetail::bankrectype1($value->bnkid);
			$bankrectype2 = AccBankdetail::bankrectype2($value->bnkid);
			$bankrectype3 = AccBankdetail::bankrectype3($value->bnkid);

			$type1Total = 0; 
			$type2Total = 0; 
			$type3Total = 0; 
			for ($j=0; $j < count($bankrectype1) ; $j++) {
				$type1Total += $bankrectype1[$j]->amount + $bankrectype1[$j]->fee;
			}


			for ($j=0; $j < count($bankrectype2) ; $j++) {
				$type2Total += $bankrectype2[$j]->amount + $bankrectype2[$j]->fee;
			}

			for ($j=0; $j < count($bankrectype3) ; $j++) {
				$type3Total += $bankrectype3[$j]->amount + $bankrectype3[$j]->fee;
			}
			$singlebanktotal =  $baseAmtVal + $type2Total - ($type1Total +$type3Total);
			$bankdetailindex[$i]['balanceAmt'] = $singlebanktotal;
			$i++;
		}
	/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/
		return view('AccBankdetail.index',[
								'bankdetailindex' => $bankdetailindex,
								'index' => $index,
								'request' => $request]);
	}

	function add(Request $request) {
		return view('AccBankdetail.addedit',['request' => $request]);	
	}

	function addeditprocess(Request $request) {
			if($request->editflg == "1") {
			$insert = AccBankdetail::insertRec($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $insert); 
			Session::flash('bankids', $request->bankids); 
			Session::flash('branchids', $request->branchids); 
			Session::flash('accno', $request->accno); 
			Session::flash('bankid', $request->bankid); 
			Session::flash('startdate', $request->startDate); 
			Session::flash('balbankid', $request->bankId); 
			Session::flash('bankname', $request->bankname); 
			Session::flash('branchname', $request->branchname);  
		} else {
			$update = Bankdetail::updateRec($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->id); 
			Session::flash('date_month', $request->date_month); 
			Session::flash('bankids', $request->bankids); 
			Session::flash('branchids', $request->branchids); 
			Session::flash('accno', $request->accno); 
			Session::flash('bankid', $request->bankid); 
			Session::flash('startdate', $request->txt_startdate); 
			Session::flash('balbankid', $request->balbankid); 
			Session::flash('bankname', $request->bankname); 
			Session::flash('branchname', $request->branchname); 
		}
		return Redirect::to('AccBankDetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

}