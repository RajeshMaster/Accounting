<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Model\LoanView;
use Session;
use Redirect;
use Auth;
use DateTime;

class LoanViewController extends Controller{

	/**  
	*  LoanDetails index details
	*  @author Madasamy 
	*  @param $request
	*  Created At 2020/08/27
	**/
	public function index(Request $request){

		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		// SORTING PROCESS
		if ($request->loanSort == "") {
			$request->loanSort = "id";
		}
		if (empty($request->sortOrder)) {
			$request->sortOrder = "asc";
		}
		if ($request->sortOrder == "asc") {
			$request->sortstyle="sort_asc";
		} else {
			$request->sortstyle="sort_desc";
		}
		$array = array("Emp_ID"=>trans('messages.lbl_empid'),
						"LastName"=>trans('messages.lbl_empName'));

		if ($request->userId == "") {
			$request->userId = "AD0000";
		}
		if (!isset($request->selYear) || $request->selYear == "") {
			$request->selYear = date('Y');
		}
		if (!isset($request->selMonth) || $request->selMonth == "") {
			$request->selMonth = date('m');
		} 

		$yrMnth = $request->selYear."-".$request->selMonth;
		$date = $yrMnth.'-'.date("t");

		// Used for pagination
		$loanDetails = LoanView::fnGetLoanEMIDetails($request,0);
		// $loanArr Used for loanwise records
		$loanArr = array();
		foreach ($loanDetails as $key => $value) {
			$loanArr[$key] = (array)$value;

			$loanDet = LoanView::fnGetLoanDetails($loanArr[$key]['loanId']);
			$loanArr[$key]['loanName'] = $loanDet['loanName'];
			$loanArr[$key]['loanAmount'] = $loanDet['loanAmount'];
			$loanArr[$key]['interestRate'] = $loanDet['interestRate'];
			$loanArr[$key]['loanTerm'] = $loanDet['loanTerm'];
			$loanArr[$key]['paymentCount'] = $loanDet['paymentCount'];

			$loanArr[$key]['belongsToId'] = $loanArr[$key]['belongsTo'];
			$loanArr[$key]['bankId'] = $loanArr[$key]['bank'];
			$loanArr[$key]['belongsTo'] = LoanView::fnGetFamilyMaster($loanArr[$key]['belongsTo']);
			$loanArr[$key]['bank'] = LoanView::fnGetBankMaster($loanArr[$key]['bank']);

			$loanBalance = 0;
			$allEmiData = LoanView::fnGetEMIData($value->loanId);
			$currEmiData = LoanView::fnGetEMIData($value->loanId,"","",$yrMnth);
			$nextEmiData = LoanView::fnGetEMIData($value->loanId,$date,"next");
			$prevEmiData = LoanView::fnGetEMIData($value->loanId,$date,"prev");
			if(!empty($currEmiData)){
				// Current EMI Data
				if (strtotime($currEmiData[0]->emiDate) < strtotime($date)) {
					$count = count($prevEmiData);
				} else{
					$count = count($prevEmiData) + 1;
				}
				if(count($prevEmiData) < 1){
					$loanBalance = $loanDet['loanAmount'];
				} else {
					$loanBalance = $currEmiData[0]->loanBalance/10000;
				}
		
			} elseif (!empty($nextEmiData)) {
				// Future EMI Data
				$count = count($allEmiData) - count($nextEmiData); 
				$loanBalance = $loanDet['loanAmount'];
			} else {    
				// Past EMI Data
				$count = count($allEmiData) - count($nextEmiData); 
			}
			$loanArr[$key]['nextCount'] = $count; 
			$loanArr[$key]['nextLoanBalance'] = $loanBalance;

		}

		// Used for relation and bankwise records in blade
		$loanArrBelongsTo = array();
		$loanArrBank = array();
		foreach ($loanArr as $key => $value) {
			$loanArrBelongsTo[$value['belongsToId']] = $value['belongsTo'];
			$loanArrBank[$value['bankId']] = $value['bank'];
		}

		// Used for subTotal calc and to display
		$loanArrVal = array();
		$totArr = array();
		foreach($loanArrBelongsTo as $belongsToId => $relation){
			$totRelAmount = 0;
			$loanAmountTotal = 0;
			$totArr[$belongsToId]['nextLoanBalance'] = 0;
			$totArr[$belongsToId]['totPrinciple'] = 0; 
			$totArr[$belongsToId]['totInterest'] = 0; 
			foreach($loanArrBank as $bankId => $bank){
				
				$relCount = 0;
				$totPrinciple = 0;
				$totInterest = 0;
				$totMonthAmount = 0;
				$totLoanBalance = 0;
				foreach($loanArr as $key => $value){
					if($value['belongsToId'] == $belongsToId && $value['bankId'] == $bankId){
						$loanArrVal[$relation][$bankId][] = $value;  // loanwise record
						$totPrinciple += $value['monthPrinciple']/10000;  //円 to 万
						$totInterest += $value['monthInterest']/10000;  //円 to 万
						$totLoanBalance += $value['nextLoanBalance'];  //円 to 万

						$loanAmountTotal += $value['loanAmount']; // total loan amount
					}
					// for relation wise row count to set rowspan
					if($value['belongsTo'] == $relation){
						$relCount++; 
					}
				}

				if ($totPrinciple != "" || $totInterest != "") {
					$loanArrVal[$relation][$bankId]['totPrinciple'] = $totPrinciple; // bankwise principle
					$loanArrVal[$relation][$bankId]['totInterest'] = $totInterest;  // bankwise interest
					$totMonthAmount = $totPrinciple + $totInterest;
					$loanArrVal[$relation][$bankId]['totMonthAmount'] = $totMonthAmount;  // bankwise total
					$totRelAmount += $totMonthAmount;
					
					$totArr[$belongsToId]['nextLoanBalance'] += $totLoanBalance; 
					$totArr[$belongsToId]['totPrinciple'] += $totPrinciple; 
					$totArr[$belongsToId]['totInterest'] += $totInterest; 
				}
			}
			$loanArrVal[$relation]['totRelAmount'] = $totRelAmount;  // relationwise total
			$loanArrVal[$relation]['relCount'] = $relCount;

			$totArr[$belongsToId]['totEMI'] = $totRelAmount; 
			$totArr[$belongsToId]['loanAmountTotal'] = $loanAmountTotal;
		}

		// YearMonth Bar Process
		$date = LoanView::fnGetCalenderBar($request);
		$total_yrs = array();
		if ($date[0] != "") {
			$prev_yrs = $date[0];
			$total_yrs1 = array_unique($date[1]);
			asort($total_yrs1);
			foreach ($total_yrs1 AS $key => $value) {
				array_push($total_yrs, $value);
			}
		} else {
			$prYrMn =explode('-', date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01')))));
			$prev_yrs=$prYrMn;
			array_push($total_yrs, $prYrMn[0]);
		} 

		$cur_year=date('Y');
		$cur_month=date('m');
		$curtime = date('YmdHis');
		if ($cur_month == 0) {
			$cur_year = $cur_year - 1;
			$cur_month = 12;
		}
		if (isset($request->selMonth) && !empty($request->selMonth)) {
			$selectedMonth=$request->selMonth;
			$selectedYear=$request->selYear;
			$cur_month=$selectedMonth;
			$cur_year=$selectedYear;
		} else {
			$selectedMonth=$cur_month;
			$selectedYear=$cur_year;
		}

		return view('LoanView.index',['loanDetails' => $loanDetails,
											'array' => $array,
											'loanArr' => $loanArr,
											'loanArrBelongsTo' => $loanArrBelongsTo,
											'loanArrBank' => $loanArrBank,
											'loanArrVal' => $loanArrVal,
											'totArr' => $totArr,
											'prev_yrs' => $prev_yrs,
											'cur_year' => $cur_year,
											'cur_month' => $cur_month,
											'total_yrs' => $total_yrs,
											'curtime' => $curtime,
											'request' => $request]);

	}

	/**
	*
	* LoanDetails Yearwise ListView Page
	* @author Madasamy
	* @return object to particular view page
	* Created At 2020/09/07
	*
	*/
	public function listview(Request $request) {

		if ($request->plimit == "") {
			$request->plimit = 50;
		}
		if ($request->userId == "") {
			$request->userId = "AD0000";
		}

		if (!isset($request->selYear) || $request->selYear == "") {
			$request->selYear = date('Y');
		} else {
			$request->selYear = $request->selYear;
		}

		$loanDetails = LoanView::fnGetYearwiseDetails($request);
		$loanArr = array();
		foreach ($loanDetails as $key => $value) {
			$loanDet = LoanView::fnGetLoanDetails($value->loanId);

			$loanArr[$key]['loanId'] = $value->loanId;
			$loanArr[$key]['loanName'] = $loanDet['loanName'];
			$loanArr[$key]['loanAmount'] = $loanDet['loanAmount'];
			$loanArr[$key]['interestRate'] = $loanDet['interestRate'];

			$loanArr[$key]['belongsToId'] = $value->belongsTo;
			$loanArr[$key]['bankId'] = $value->bank;
			$loanArr[$key]['belongsTo'] =  LoanView::fnGetFamilyMaster($value->belongsTo);
			$loanArr[$key]['bank'] =  LoanView::fnGetBankMaster($value->bank); 

			$amtArr = LoanView::fnGetYrTotPay($request,$value->loanId);
			$loanArr[$key]['principle'] = ($amtArr['monthPrinciple'] > 0 ) ? $amtArr['monthPrinciple']/10000 : 0;
			$loanArr[$key]['interest'] = ($amtArr['monthInterest'] > 0 ) ? $amtArr['monthInterest']/10000 : 0;
		}

		// Used for relation and bankwise records in blade
		$loanArrBelongsTo = array();
		$loanArrBank = array();
		foreach ($loanArr as $key => $value) {
			$loanArrBelongsTo[$value['belongsToId']] = $value['belongsTo'];
			$loanArrBank[$value['bankId']] = $value['bank'];
		}

		// Used for subTotal calc and to display
		$loanArrVal = array();
		$totArr = array();
		$grandTot = 0;
		foreach($loanArrBelongsTo as $belongsToId => $relation){

			// to declare empty var for relationwise monthPayTotal
			for ($j=1; $j <= 12 ; $j++) { 
				$totArr[$belongsToId]['monthPayTotal'][$j] = "";
			}

			$totRelAmount = 0;
			$loanAmountTotal = 0;
			$principleTotal = 0;
			$interestTotal = 0;
			$relationTotal = 0;
			foreach($loanArrBank as $bankId => $bank){  //bankwise
				
				foreach($loanArr as $key => $value){  //loanwise

					if($value['belongsToId'] == $belongsToId && $value['bankId'] == $bankId){
						$loanArrVal[$relation][$bankId][] = $value; // loanwise record
						$loanAmountTotal += $value['loanAmount']; // total loan amount
						$principleTotal += $value['principle']; // total loan amount
						$interestTotal += $value['interest']; // total loan amount

						// for monthwise
						$monthPayTotal = 0;
						for ($i=1; $i <= 12; $i++) {  
							$monthPayment = LoanView::fnGetLoanMonthPay($request,$value['loanId'],$i);
							$loanArrVal[$relation][$bankId][$value['loanId']]['monthPayment'][$i] = $monthPayment/10000; 
							$monthPayTotal += $monthPayment/10000;
							//relationwise monthpayTotal
							$totArr[$belongsToId]['monthPayTotal'][$i] += $monthPayment/10000;
						}

						$loanArrVal[$relation][$bankId][$value['loanId']]['monthPayTotal'] = $monthPayTotal;
						$grandTot += $monthPayTotal; //yearly total
						$relationTotal += $monthPayTotal; //relationwise total
					}

				}
			}

			$totArr[$belongsToId]['loanAmountTotal'] = $loanAmountTotal;
			$totArr[$belongsToId]['principleTotal'] = $principleTotal;
			$totArr[$belongsToId]['interestTotal'] = $interestTotal;
			$totArr[$belongsToId]['relationTotal'] = $relationTotal;
		}

		// YearBar Process
		$date = LoanView::fnGetYearCalenderBar($request);
		$total_yrs = array();
		if ($date[0] != "") {
			$prev_yrs = $date[0];
			$total_yrs1 = array_unique($date[1]);
			asort($total_yrs1);
			foreach ($total_yrs1 AS $key => $value) {
				array_push($total_yrs, $value);
			}
		} else {
			$prYrMn = explode('-', date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01')))));
			$prev_yrs = $prYrMn;
			array_push($total_yrs, $prYrMn[0]);
		} 
		$cur_year = date('Y');
		$curtime = date('YmdHis');
		if (isset($request->selYear) && !empty($request->selYear)) {
			$selectedYear = $request->selYear;
			$cur_year = $selectedYear;
		} else {
			$selectedYear = $cur_year;
		}
		
		return view('LoanView.listview', ['request' => $request,
											'loanDetails' => $loanDetails,
											'loanArr' => $loanArr,
											'loanArrBelongsTo' => $loanArrBelongsTo,
											'loanArrBank' => $loanArrBank,
											'loanArrVal' => $loanArrVal,
											'totArr' => $totArr,
											'grandTot' => $grandTot,
											'prev_yrs' => $prev_yrs,
											'cur_year' => $cur_year,
											'total_yrs' => $total_yrs,
											'curtime' => $curtime
										]);
	}

}
