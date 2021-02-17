<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ExternalInvoice;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
use Illuminate\Support\Facades\Validator;

class ExternalInvoiceController extends Controller {

	/**
	*
	* Get Invoice Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/17
	*
	*/
	public function index(Request $request) {

		if(Session::get('selYear') != "") {
			$request->selYear = Session::get('selYear');
		}
		if(Session::get('selMonth') != "") {
			$request->selMonth =  Session::get('selMonth');
		}
		if ($request->plimit == "") {
			$request->plimit = 50;
		}
		$disabledall = "";
		$disabledcreating = "";
		$disabledapproved = "";
		$disabledunused = "";
		$disabledsend = "";
		$sortarray = "";
		$dispval1 = "";
		if (isset($request->invoicestatusid) && $request->invoicestatusid != "") {
			ExternalInvoice::updateClassification($request);
		}
		if(!isset($request->filter) || $request->filter == "") {
			$request->filter = 1;
			$fil = 1;
			$disabledall = "disabled fb";
		} else if($request->filter == 1) {
			$fil = 1;
			$disabledall = "disabled fb";
		} elseif($request->filter == 2) {
			$fil = 2;
			$disabledcreating = "disabled fb";
		} elseif($request->filter == 3) {
			$fil = 3;
			$disabledapproved = "disabled fb";
		} elseif($request->filter == 4) {
			$fil = 4;
			$disabledunused = "disabled fb";
		} elseif($request->filter == 5) {
			$fil = 5;
			$disabledsend="disabled fb";
		}
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		if (!empty($request->singlesearch) || $request->searchmethod == 2) {
			$sortMargin = "margin-right:230px;";
		} else {
			$sortMargin = "margin-right:0px;";
		}
		if (empty($request->pageclick)) {
			$page_no = 1;
		} else {
			$page_no = $request->pageclick;
		}
		$invoicesortarray = [$request->invoicesort => $request->invoicesort,
								'invoiceId' => trans('messages.lbl_invoiceno'),
								'quot_date'=> trans('messages.lbl_billingdate'),
								'userId'=> trans('messages.lbl_usernamesign')
							];
		$request->invoicesort = $request->sortOptn;
		$request->sortOptn = $request->sortOptn;
		$srt = $request->invoicesort;
		$odr = $request->sortOrder;
		if ($request->invoicesort == "") {
			$request->invoicesort = "invoiceId";
		}
		//SORTING PROCESS
		if (empty($request->sortOrder)) {
			$request->sortOrder = "desc";
		}
		if ($request->sortOrder == "asc") {  
			$request->sortstyle = "sort_asc";
		} else {
			$request->sortstyle = "sort_desc";
		}
		if ($request->searchmethod == 1 || $request->searchmethod == 2) {
			$sortMargin = "margin-right:260px;";
		} else {
			$sortMargin = "margin-right:0px;";
		}
		$search_flg = 0;
		$singlesearchtxt = trim($request->singlesearchtxt);
		$estimateno = trim($request->estimateno);
		$companyname = "";
		if ($request->companyname != "" ) {
			$companyname = trim($request->companyname);
			$request->companynameClick = "";
		} else if ($request->companynameClick != "" ) {
			$companyname = trim($request->companynameClick);
			$request->companyname = "";
			$disabledall = "";
		}
		$startdate = $request->startdate;
		$enddate = $request->enddate;
		if($request->projecttype == "a") {
			$projecttype = "";
		} else {
			$projecttype = $request->projecttype;
		}
		if($request->protype2 == "0"){
			$taxSearch = "";
		} else {
			$taxSearch = $request->protype2;
		}

		// Year Bar Process
		$accountperiod = ExternalInvoice::fnGetAccountPeriod($request);

		foreach ($accountperiod as $key => $value) {
			$account_close_yr = $value->Closingyear;
			$account_close_mn = $value->Closingmonth;
			$account_period = intval($value->Accountperiod);
		}

		$splityear = explode("-", $request->previou_next_year);
		if ($request->previou_next_year != "") {
			if (intval($splityear[1]) > $account_close_mn) {
				$last_year = intval($splityear[0]);
				$current_year = intval($splityear[0]) + 1;
			} else {
				$last_year = intval($splityear[0]) - 1;
				$current_year = intval($splityear[0]);
			}
		} else if ($request->selYear) {
			if ($request->selMonth > $account_close_mn) {
				$current_year = intval($request->selYear) + 1;
				$last_year = intval($request->selYear);
			} else {
				$current_year = intval($request->selYear);
				$last_year = intval($request->selYear) - 1;
			}
		} else {
			$start = new Carbon\Carbon('first day of last month');
			$start = $start->format('m');
			if ($start > $account_close_mn && $start!=12) {
				$current_year = date('Y')+1;
				$last_year = date('Y');
			} else {
				$current_year = date('Y');
				$last_year = date('Y') - 1;
			}
		}

		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
		$maxday = date('t', strtotime($year_month_day));
		$from_date = $last_year."-" .substr("0" . $account_close_mn, -2)."-".substr("0". $maxday, -2);
		$to_date = $current_year."-".substr("0" . ($account_close_mn + 1), -2) . "-01";

		$est_query = ExternalInvoice::fnGetEstimateRecord($from_date, $to_date);

		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[] = $value->quot_date;
		}

		$est_query1 = ExternalInvoice::fnGetEstimateRecordPrevious($from_date);

		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[] = substr($value->quot_date, 0, 4);
			$dbprevious[] = $value->quot_date;
			$pre++;
		}

		$est_query2 = ExternalInvoice::fnGetEstimateRecordNext($to_date);

		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[] = $value->quot_date;
		}

		$dbrecord = array_unique($dbrecord);
		$dbpreviouscheck = array_unique($dbprevious);
		$db_year_month = array();

		if(empty($dbrecord)){
			foreach ($dbpreviouscheck AS $dbrecordkey => $dbrecordcheck) {
				$split_val = explode("-", $dbrecordcheck);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
		} else {
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$split_val = explode("-", $dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
		}

		$year_month = array();
		if(!empty($dbprevious[$pre-1])) {
			$split_vpre = explode("-", $dbprevious[$pre-1]);
			if(isset($split_vpre)) {
				if( $account_close_mn < $split_vpre[1] ) {
					$pre_yr_mn = $split_vpre[0];
					$nex_yr_mn = $split_vpre[0]+1;
				} else {
					$pre_yr_mn = $split_vpre[0]-1;
					$nex_yr_mn = $split_vpre[0];

				}

			}

		}

		if ($account_close_mn == 12) {
			if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {
				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$nex_yr_mn][$i] = $i;
				}
				$last_year = $pre_yr_mn;
				$current_year = $nex_yr_mn;
			} else {
				for ($i = 1; $i <= 12; $i++) {
					$year_month[$current_year][$i] = $i;
				}
			}
		} else {
			if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {
				for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
					$year_month[$pre_yr_mn][$i] = $i;
				}
				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$nex_yr_mn][$i] = $i;
				}
				$last_year = $pre_yr_mn;
				$current_year = $nex_yr_mn;
			} else {
				for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
					$year_month[$last_year][$i] = $i;
				}
				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$current_year][$i] = $i;
				}
			}
		}

		if (isset($request->date_month)) {
			$date_month = $request->date_month;
		} else {
			if (!isset($request->selMonth) || empty($request->selMonth)) {
				// $dbrecordvalue this array is for CurrentYr and CurrentMonth Record
				if (empty($dbrecordvalue)) {
				// $dbprevious this array is for previous Record 
					if (empty($dbprevious)) {
						$date_month = date("Y-m");
					} else {
						$date_month = $dbprevious[$pre-1];
					}
				} else {
					$date_month = $dbrecordvalue;
				}
			} else {
				if (isset($request->selMonth) && !empty($request->selMonth) ) {
					$date_month = $request->selYear."-".$request->selMonth;
				} else {
					$date_month = $request->date_month;
				}
			}
		}

		$split_date = explode('-', $date_month);
		$account_val = "";
		$arr_yr_mn = array_keys($year_month);
		$yr_mn = "";
		if( $account_close_mn == 12 ) {
			if(isset($arr_yr_mn[0])) {
				$yr_mn = $arr_yr_mn[0];
			}
		} else {
			if(isset($arr_yr_mn[1])) {
				$yr_mn = $arr_yr_mn[1];
			}
		}

		if( $account_close_yr >  $yr_mn) {
			$diff = $account_close_yr -$yr_mn;
			$account_val = $account_period-$diff;
		} else if($account_close_yr <  $yr_mn) {
			$diff = $yr_mn-$account_close_yr;
			$account_val = $account_period+$diff;
		} else if (isset($request->account_val)) {
			$account_val = $request->account_val;
		} else {
			$account_val = $account_period;
		}

		$disp = 0;

		//-----Added by anto... Please check the output
		if($request->selYear == "") {
			$request->selYear = date("Y");
			$request->selMonth = date("m");
		}

		if (isset($request->date_month)) {
			$date_month = $request->date_month;
		} else {
			$date_month = $request->selYear."-".$request->selMonth;
		}

		//------
		$TotEstquery = ExternalInvoice::fnGetinvoiceTotalValue($request,$taxSearch,$date_month,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate,$fil);

		$get_view = array();
		$totalcount = count($TotEstquery);
		$x = 1;
		foreach ($TotEstquery as $key => $value) {
			$get_view[$x]["id"] = $value->id;
			$x++;
		}

		$explode = array();
		$splitYrMn = explode("-", $date_month);
		$cur_year = $splitYrMn[0];
		$cur_month = str_pad($splitYrMn[1], 2, "0", STR_PAD_LEFT);
		if (isset($_REQUEST['selMonth'])) {
			$selectedMonth = $_REQUEST['selMonth'];
			$selectedYear = $_REQUEST['selYear'];
			$cur_month = $selectedMonth;
			$cur_year = $selectedYear;
		} else {
			$selectedMonth=$cur_month;
			$selectedYear=$cur_year;
			$_POST['selYear'] = $selectedYear;
			$_POST['selMonth'] = $selectedMonth;
		}

		if (empty($dbrecordvalue)) {
			if (!empty($dbpreviousYr)) {
				$aryUnique = array_unique($dbpreviousYr);
				$aryEnd = array_keys($aryUnique);
				$B = end($aryEnd);
				$cou = count($dbprevious);
				for($z = $B; $z < $cou; $z++) {
					unset($dbprevious[$z]);
				}
			}
		}

		if($dbprevious == "" || $dbnext == "" || $db_year_month == "" || $year_month == "") {
			$dbnext = array();
			$dbprevious = array();
		}

		$totalval = 0;
		$divtotal = 0;
		$paid_amount = 0;
		$bal_amount = 0;
		$grand_style = "";
		$grandtotal = 0;
		$balance_style = "";
		$balance = 0;
		$paid_amo = 0;

		$othersArray = array('0' => trans('messages.lbl_creating'),
							'1' => trans('messages.lbl_approved'),
							'2' => trans('messages.lbl_sent'),
							'3' => trans('messages.lbl_unused')
						);

		// Copy Flag Display process
		$copyFlag = 0;
		$twoMthBefore = new Carbon\Carbon('first day of last month');
		$twomonthBefore = $twoMthBefore->subMonth(2)->format('Y-m');
		$strTwoMthBefore = strtotime($twomonthBefore);
		$currentDate_time =  Carbon\Carbon::createFromFormat('Y-m', $date_month);
		$currentTwoMth = $currentDate_time->subMonth(1)->format('Y-m');
		$strDateTime = strtotime($currentTwoMth);
		if ($strTwoMthBefore == $strDateTime) {
			$copyFlag = 1;
		}

		//returning to view page
		return view('ExternalInvoice.index', [ 'request' => $request,
											'account_period' => $account_period,
											'year_month' => $year_month,
											'db_year_month' => $db_year_month,
											'date_month' => $date_month,
											'dbnext' => $dbnext,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' => $account_val,
											'totalval' => $totalval,
											'get_view' => $get_view,
											'disabledall' => $disabledall,
											'totalcount' => $totalcount,
											'sortMargin' => $sortMargin,
											'dispval1' => $dispval1,
											'balance' => $balance,
											'copyFlag' => $copyFlag,
											'disabledcreating' => $disabledcreating,
											'disabledapproved' => $disabledapproved,
											'disabledunused' => $disabledunused,
											'disabledsend' => $disabledsend,
											'TotEstquery' => $TotEstquery,
											'invoicesortarray' => $invoicesortarray,
											'divtotal' => $divtotal,
											'paid_amount'=> $paid_amount,
											'bal_amount'=> $bal_amount,
											'grandtotal' => $grandtotal,
											'balance_style' => $balance_style,
											'grand_style' => $grand_style,
											'othersArray' => $othersArray
										]);

	}

	/**
	*
	* Add Edit Page for Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/17
	*
	*/
	public function addedit(Request $request) {

		if (!isset($request->editid)) {
			return Redirect::to('ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$amtcount = 0;
		$montharray = array("1"=>trans('messages.lbl_presentmonth'),
							"2"=>trans('messages.lbl_nextmonth'),
							"3"=>trans('messages.lbl_nextnextmonth'),
							"4"=>trans('messages.lbl_Others'));

		return view('ExternalInvoice.addedit',[ 'request' => $request,
												'montharray' => $montharray,
												'amtcount' => $amtcount
										]);

	}

	/**
	*
	* Addedit Process for Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/17
	*
	*/
	public function addeditprocess(Request $request) {

		if($request->editId != "") {

			$update = ExternalUser::updateUser($request);
			Session::flash('viewId', $request->editId); 

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {

			$autoincId = ExternalUser::getautoincrement();
			$insert = ExternalUser::insertUser($request);
			Session::flash('viewId', $autoincId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		}

		return Redirect::to('ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* Notice Popup for Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/17
	*
	*/
	public static function noticepopup(Request $request) {

		$notice = ExternalInvoice::fnGetOtherDetails($request);

		return view('ExternalInvoice.noticepopup',[ 'request' => $request,
													'notice' => $notice
												]);

	}

}