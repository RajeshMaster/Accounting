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
		$index = AccBankDetail::bankindex($request)->paginate($request->plimit);

		$i=0;
		$totalBalance = 0;
		foreach ($index as $key => $value) {
			$bankdetailindex[$i]['banknm'] = $value->banknm;
			$bankdetailindex[$i]['nickName'] = $value->NickName;
			$bankdetailindex[$i]['brnchnm'] = $value->brnchnm;
			$bankdetailindex[$i]['AccNo'] = $value->AccNo;


			$bankdetailindex[$i]['bankId'] = $value->bnkid;
			$bankdetailindex[$i]['brnchid'] = $value->brnchid;

			$baseAmtInsChk = array();
			$baseAmtVal = 0;
			$baseAmtInsChk = AccBankDetail::baseAmtInsChk($value->bnkid, $value->AccNo);
			$bankdetailindex[$i]['startDate'] = "";
			if (isset($baseAmtInsChk[0])) {
				$bankdetailindex[$i]['baseAmtInsChk'] = 1;
				$baseAmtVal = $baseAmtInsChk[0]->amount;
				$bankdetailindex[$i]['startDate'] = $value->startDate;
			} else {
				$bankdetailindex[$i]['baseAmtInsChk'] = 0;
			}
			$bankrectype1 = AccBankDetail::bankrectype($value->bnkid, $value->AccNo ,'1');
			$bankrectype2 = AccBankDetail::bankrectype($value->bnkid, $value->AccNo ,'2');
			$bankrectype3 = AccBankDetail::bankrectype($value->bnkid, $value->AccNo ,'3');
			$bankrectype4 = AccBankDetail::bankrectype($value->bnkid, $value->AccNo ,'4');

			$type1Total = 0; 
			$type2Total = 0; 
			$type3Total = 0; 
			$type4Total = 0; 
			for ($j=0; $j < count($bankrectype1) ; $j++) {
				$type1Total += $bankrectype1[$j]->amount + $bankrectype1[$j]->fee;
			}


			for ($j=0; $j < count($bankrectype2) ; $j++) {
				$type2Total += $bankrectype2[$j]->amount + $bankrectype2[$j]->fee;
			}

			for ($j=0; $j < count($bankrectype3) ; $j++) {
				$type3Total += $bankrectype3[$j]->amount + $bankrectype3[$j]->fee;
			}

			for ($j=0; $j < count($bankrectype4) ; $j++) {
				$type4Total += $bankrectype4[$j]->amount + $bankrectype4[$j]->fee;
			}

			// print_r($baseAmtVal);echo "<br/>";
			// print_r($type2Total);echo "<br/>";
			// print_r($type1Total);echo "<br/>";
			// print_r($type3Total);echo "<br/>";
			// exit;
			$singlebanktotal =  $baseAmtVal + ($type2Total + $type4Total) - ($type1Total +$type3Total);
			$bankdetailindex[$i]['balanceAmt'] = $singlebanktotal;

			$totalBalance += $singlebanktotal;
			$i++;
		}

		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/
		return view('AccBankDetail.index',[
								'bankdetailindex' => $bankdetailindex,
								'totalBalance' => $totalBalance,
								'index' => $index,
								'request' => $request]);
	}

	function add(Request $request) {
		return view('AccBankDetail.addedit',['request' => $request]);	
	}

	function addeditprocess(Request $request) {
			if($request->editflg == "1") {
			$insert = AccBankDetail::insertRec($request);
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
			$update = AccBankDetail::updateRec($request);
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

	function Viewlist(Request $request){
		if ($request->plimit=="") {
			$request->plimit = 50;
			$request->page = 1;
		}
		$bankdetail = array();
		if (!isset($request->fromDate) || $request->fromDate == "") {
			$request->fromDate = date("Y-m-d");
		}
		$singleBank = AccBankDetail::bankview($request);

		$baseAmtInsChk = AccBankDetail::baseAmtInsChk($request->bankid, $request->accno);
		$baseAmtVal = $baseAmtInsChk[0]->amount;
		$bankrectype1 = AccBankDetail::bankrectype($request->bankid, $request->accno ,'1');
		$bankrectype2 = AccBankDetail::bankrectype($request->bankid, $request->accno ,'2');
		$bankrectype3 = AccBankDetail::bankrectype($request->bankid, $request->accno ,'3');
		$bankrectype4 = AccBankDetail::bankrectype($request->bankid, $request->accno ,'4');

		$type1Total = 0;
		$type2Total = 0;
		$type3Total = 0;
		$type4Total = 0;
		for ($j=0; $j < count($bankrectype1) ; $j++) {
			$type1Total += $bankrectype1[$j]->amount + $bankrectype1[$j]->fee;
		}
		for ($j=0; $j < count($bankrectype2) ; $j++) {
			$type2Total += $bankrectype2[$j]->amount + $bankrectype2[$j]->fee;
		}
		for ($j=0; $j < count($bankrectype3) ; $j++) {
			$type3Total += $bankrectype3[$j]->amount + $bankrectype3[$j]->fee;
		}
		for ($j=0; $j < count($bankrectype4) ; $j++) {
			$type4Total += $bankrectype4[$j]->amount + $bankrectype4[$j]->fee;
		}
		$singlebanktotal =  $baseAmtVal + ($type2Total + $type4Total) - ($type1Total +$type3Total);
		
		$i=0;
		foreach ($singleBank as $key => $value) {
			$bankdetail[$i]['banknm'] = $value->FirstName;
			$bankdetail[$i]['nickName'] = $value->Bank_NickName;
			$bankdetail[$i]['brnchnm'] = $value->BranchName;
			$bankdetail[$i]['AccNo'] = $value->AccNo;
			$bankdetail[$i]['bankId'] = $value->bankId;
			$bankdetail[$i]['brnchid'] = $value->branchId;
			$bankdetail[$i]['content'] = $value->content;
			$bankdetail[$i]['remarks'] = $value->remarks;
			$bankdetail[$i]['date'] = $value->date;
			$bankdetail[$i]['transcationType'] = $value->transcationType;
			$bankdetail[$i]['amount'] = $value->amount;
			$bankdetail[$i]['fee'] = $value->fee;
			$bankdetail[$i]['baseAmtVal'] = $baseAmtVal;
			$i++;
		}

		return view('AccBankDetail.Viewlist',[
											'singleBank' => $singleBank,
											'bankdetail' => $bankdetail,
											'singlebanktotal' => $singlebanktotal,
											'baseAmtInsChk' => $baseAmtInsChk,
											'request' => $request]);
	}

}