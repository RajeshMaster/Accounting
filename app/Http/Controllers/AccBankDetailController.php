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
	public function index(Request $request) {
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
			$request->page = 1;
		}
		$bankdetailindex = array();
		$j = 0;
		$index = AccBankDetail::bankindex($request)->paginate($request->plimit);

		$i = 0;
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
			$baseAmtInsChk = AccBankDetail::baseAmtInsChk($value->bnkid,$value->AccNo);
			$bankdetailindex[$i]['startDate'] = "";
			if (isset($baseAmtInsChk[0])) {
				$bankdetailindex[$i]['baseAmtInsChk'] = 1;
				$baseAmtVal = $baseAmtInsChk[0]->amount;
				$request->bankid = $value->bnkid;
				$request->accno = $value->AccNo;
				$balanceAmt = AccBankDetail::AccBalance($request,$baseAmtInsChk[0]->date,"");

				foreach ($balanceAmt AS $balKey => $balVal) {
					if ($balVal->transcationType == 2 || $balVal->transcationType == 4) {
						$baseAmtVal += $balVal->amount;
					} else {
						$baseAmtVal -= $balVal->amount;
					}
					$baseAmtVal -= $balVal->fee;
				} 
				$bankdetailindex[$i]['startDate'] = $baseAmtInsChk[0]->date;
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

			for ($j = 0; $j < count($bankrectype1) ; $j++) {
				$type1Total += $bankrectype1[$j]->amount + $bankrectype1[$j]->fee;
			}

			for ($j = 0; $j < count($bankrectype2) ; $j++) {
				$type2Total += $bankrectype2[$j]->amount + $bankrectype2[$j]->fee;
			}

			for ($j = 0; $j < count($bankrectype3) ; $j++) {
				$type3Total += $bankrectype3[$j]->amount + $bankrectype3[$j]->fee;
			}

			for ($j = 0; $j < count($bankrectype4) ; $j++) {
				$type4Total += $bankrectype4[$j]->amount + $bankrectype4[$j]->fee;
			}
			$singlebanktotal =  $baseAmtVal + ($type2Total + $type4Total) - ($type1Total +$type3Total);
			$bankdetailindex[$i]['balanceAmt'] = $baseAmtVal;

			$totalBalance += $baseAmtVal;
			$i++;
		}

		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('AccBankDetail.index',[ 'request' => $request,
											'bankdetailindex' => $bankdetailindex,
											'totalBalance' => $totalBalance,
											'index' => $index
										]);
	}

	public function add(Request $request) {

		return view('AccBankDetail.addedit',['request' => $request]);	
	}

	public function addeditprocess(Request $request) {

			if($request->editFlg != "1") {
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
			$baseAmtId = AccBankDetail::baseAmtInsChk($request->bankid,$request->accno);
			$update = AccBankDetail::updateRec($request,$baseAmtId[0]->id);
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

	public function Viewlist(Request $request) {

		if ($request->plimit == "") {
			$request->plimit = 50;
			$request->page = 1;
		}
		if (!isset($request->bankid)) {
			return Redirect::to('AccBankDetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankdetail = array();
		// if (!isset($request->fromDate) || $request->fromDate == "") {
		// 	$request->fromDate = date("Y-m-d");
		// }

		// Year Bar Process Start

		$from_date = "";
		$to_date = "";
		$baln = 0;
		$balance = array();
		$previous_date = "";
		$date_month = "";
		$total = 0;
		$get_bankdet = array();
		$get_mnsub = array();
		$bal = array();
		$g_accountperiod = Bankdetail::fnGetAccountPeriodBK($request);
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
		$startdate = $request->startdate;
		$curDate = date('Y-m-d');
		$balanceAmtonDownTr = 0;

		// Year Bar Process End

		$singleBank = AccBankDetail::bankview($request,$startdate,$curDate,$from_date,$to_date,"",1);

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

		for ($j = 0; $j < count($bankrectype1) ; $j++) {
			$type1Total += $bankrectype1[$j]->amount + $bankrectype1[$j]->fee;
		}
		for ($j = 0; $j < count($bankrectype2) ; $j++) {
			$type2Total += $bankrectype2[$j]->amount + $bankrectype2[$j]->fee;
		}
		for ($j = 0; $j < count($bankrectype3) ; $j++) {
			$type3Total += $bankrectype3[$j]->amount + $bankrectype3[$j]->fee;
		}
		for ($j = 0; $j < count($bankrectype4) ; $j++) {
			$type4Total += $bankrectype4[$j]->amount + $bankrectype4[$j]->fee;
		}
		$singlebanktotal =  $baseAmtVal + ($type2Total + $type4Total) - ($type1Total + $type3Total);

		
		// Year Bar Process Start
		$dbrecord = array();
		foreach ($singleBank as $key => $value) {
			$dbrecord[] = $value->date;
		}
		$dbrecord = array_unique($dbrecord); 
		$dbyears = array();
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$dbyear = substr($dbrecordvalue,0,4);
			$dbyears[$dbyear] = $dbyear;
		}
		$db_year_month = array();
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$lastdbrecord = substr($dbrecordvalue,0,7);
		}
		if (empty($request->selYear) && !empty($lastdbrecord)) {
			$date_month = $lastdbrecord;
			$selMonth = substr($date_month,5,2);
			$selYear = substr($date_month,0,4);
		} else if(empty($request->selYear) && empty($lastdbrecord)){
			$selMonth = date('m');
			$selYear= date('Y');
		} else if (empty($request->selMonth)) {
			$date_month = $request->selYear;
			$selYear = $request->selYear;
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			$selYear = $request->selYear;
			$selMonth = $request->selMonth;
		}

		// echo "<pre>";
		// print_r($dbrecord);
		// echo "</pre>";

		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			if(empty($selMonth)) {
				$dbrecords = substr($dbrecordvalue,0,4);
			} else {
				$dbrecords = substr($dbrecordvalue,0,7);
			}
			if($dbrecords < $date_month){
				$previous_date = $dbrecords;
			}
			$split_val = explode("-", $dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}

		$splityear = explode('-', $request->previou_next_year);
		if ($request->previou_next_year != "") {
			if (intval($splityear[1]) > $account_close_mn) {
				$last_year = intval($splityear[0]);
				$current_year = intval($splityear[0]) + 1;
			} else {
				$last_year = intval($splityear[0]) - 1;
				$current_year = intval($splityear[0]);
			}
		} else if ($selYear) {
			if ($selMonth > $account_close_mn) {
				$current_year = intval($selYear) + 1;
				$last_year = intval($selYear);
			} else {
				$current_year = intval($selYear);
				$last_year = intval($selYear) - 1;
			}
		} else {
			if ($selMonth > $account_close_mn) {
			    $current_year = $selYear + 1;
				$last_year = $selYear;
			} else {
			    $current_year = $selYear;
				$last_year = $selYear - 1;
			}
		}

		$year_month1 = array();
		if ($account_close_mn == 12) {
			for ($i = 1; $i <= 12; $i++) {
				$year_month1[$current_year][$i] = $i;
			}
		} else {
			for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
				$year_month1[$last_year][$i] = $i;
			}

			for ($i = 1; $i <= $account_close_mn; $i++) {
				$year_month1[$current_year][$i] = $i;
			}
		}

		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
		$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
		$bktr_query1 = AccBankDetail::bankview($request,$startdate,$curDate,$from_date,"","",2);
		$dbprevious = array();
		foreach ($bktr_query1 AS $key => $value) {
			array_push($dbprevious, $value->date);
		}
		$bktr_query2 = AccBankDetail::bankview($request,$startdate,$curDate,"",$to_date,"",2);
		$dbnext = array();
		foreach ($bktr_query2 AS $key => $value) {
			array_push($dbnext, $value->date);
		}
		$account_val = Common::getAccountPeriod($year_month1,$account_close_yr,$account_close_mn,$account_period);

		$g_query = AccBankDetail::bankview($request,$startdate,$curDate,"","",$date_month,2);

		$balance = $baseAmtInsChk[0]->amount;
		$balanceAmt = AccBankDetail::AccBalance($request,$startdate,"");
		foreach ($balanceAmt AS $balKey => $balVal) {
			if ($balVal->transcationType == 2 || $balVal->transcationType == 4) {
				$balance += $balVal->amount;
			} else {
				$balance -= $balVal->amount;
			}
			$balance -= $balVal->fee;
		} 


		$curBal = $baseAmtInsChk[0]->amount;
		if ($previous_date == "") {
			$curBal = $baseAmtInsChk[0]->amount;
		} else {
			$prYrMn = date("Y-m", strtotime("-1 months", strtotime($date_month)));
			$prevBalanceAmt = AccBankDetail::AccBalance($request,$startdate,$prYrMn);
			foreach ($prevBalanceAmt AS $prevBalKey => $prevBalVal) {
				if ($prevBalVal->transcationType == 2 || $prevBalVal->transcationType == 4) {
					$curBal += $prevBalVal->amount;
				} else {
					$curBal -= $prevBalVal->amount;
				}
				$curBal -= $prevBalVal->fee;
			} 
		}

		$sql_cnt = 0;
		$sql_cnt = count($g_query);

		// Year Bar Process End

		$i = 0;
		
		foreach ($g_query as $key => $value) {
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

		$balanceAmtonDownTr = $curBal;
		// print_r($balanceAmtonDownTr);exit;


		$date_monthday = $date_month.'-01';
		$debitAmt =0;
		$creditAmt =0;
		
		$endDateday = $date_month.'-'.Common::fnGetMaximumDateofMonth($date_month);

		if ($date_month == date('Y-m')) {
			$endDateday = date('Y-m-d');
		}

		for ($i=0; $i < count($singleBank) ; $i++) { 
			if ($date_monthday <= $singleBank[$i]->date  && $endDateday >= $singleBank[$i]->date) {
				// print_r($singleBank[$i]->date);echo "<br>";
				if ($singleBank[$i]->transcationType == 2 || $singleBank[$i]->transcationType == 4) {
					$balanceAmtonDownTr += $singleBank[$i]->amount;
				} else {
					$balanceAmtonDownTr -= $singleBank[$i]->amount;
				}
				$balanceAmtonDownTr -= $singleBank[$i]->fee;
			}
		}

		return view('AccBankDetail.Viewlist',[ 'request' => $request,
												'singleBank' => $singleBank,
												'bankdetail' => $bankdetail,
												'singlebanktotal' => $singlebanktotal,
												'baseAmtInsChk' => $baseAmtInsChk,
												'balance' => $balance,
												'curBal' => $curBal,
												'baseAmtVal' => $baseAmtVal,
												'previous_date' => $previous_date,
												'g_query' => $g_query,
												'account_period' => $account_period,
												'year_month' => $year_month1,
												'db_year_month' => $db_year_month,
												'date_month' => $date_month,
												'dbnext' => $dbnext,
												'dbprevious' => $dbprevious,
												'last_year' => $last_year,
												'current_year' => $current_year,
												'account_val' => $account_val,
												'balanceAmtonDownTr' => $balanceAmtonDownTr
											]);
	}
}