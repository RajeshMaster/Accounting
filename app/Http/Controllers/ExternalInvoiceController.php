<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ExternalInvoice;
use App\Http\Common;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
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
								'userName'=> trans('messages.lbl_usernamesign')
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

		$singlesearchtxt = trim($request->singlesearchtxt);
		$username = "";
		if ($request->usernameclick != "" ) {
			$username = trim($request->usernameclick);
		}
		$startdate = $request->startdate;
		$enddate = $request->enddate;

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
		$TotEstquery = ExternalInvoice::fnGetinvoiceTotalValue($request,$date_month,$singlesearchtxt,$username,$startdate,$enddate,$fil);

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
		$selectval = "";
		$getUserDetails = ExternalInvoice::getUserDetails($request);
		$getProjectType = ExternalInvoice::getProjectType($request);

		$dat = array();
		$invoicedata = array();
		if ($request->editid != "") {
			$invoicedataforloop = ExternalInvoice::fnGetInvoiceWorkDtls($request->editid);
			foreach ($invoicedataforloop as $key => $value) {
				$dat[] = $value->amount;
			} 
			$amtcount = count($dat);
			$invoicedata = ExternalInvoice::fnGetinvoiceUserData($request->editid);
			if (isset($invoicedata[0])) {
				$selectval = $invoicedata[0]->userId;
			}
		}

		return view('ExternalInvoice.addedit',[ 'request' => $request,
												'invoicedata' => $invoicedata,
												'getUserDetails' => $getUserDetails,
												'getProjectType' => $getProjectType,
												'amtcount' => $amtcount,
												'selectval' => $selectval
										]);

	}

	/**
	*
	* Addedit Process for Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/18
	*
	*/
	public function addeditprocess(Request $request) {

		if($request->editflg == "edit") {

			$update = ExternalInvoice::updExtInvoice($request);

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}

		} else {
			$invoiceId = ExternalInvoice::fnGenerateInvoiceID();
			$autoincId = ExternalInvoice::getautoincrement();
			$insert = ExternalInvoice::insExtInvoice($request,$invoiceId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}

		$date = explode("-", $request->quot_date);
		if (isset($date[0])) {
			Session::flash('selYear', $date[0]);
		} if (isset($date[1])) {
			Session::flash('selMonth', $date[1]);
		}

		return Redirect::to('ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	/**
	*
	* Bank Detail for User Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/18
	*
	*/
	public static function getBankDetails(Request $request) {

		$getbankdetails = ExternalInvoice::getbankdetails($request->userId);
		$getbankdetails = json_encode($getbankdetails);
		echo $getbankdetails; exit;

	}

	/**
	*
	* View Detail for External Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/18
	*
	*/
	public static function view(Request $request){

		if(Session::get('viewid') != ""){
			$request->viewid = Session::get('viewid');
		}

		//ON URL ENTER REDIRECT TO INDEX PAGE
		if(!isset($request->viewid) || $request->viewid == ""){
			return Redirect::to('ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$invoicedata = ExternalInvoice::fnGetinvoiceUserData($request->viewid);
		$getbankdetails = array();
		if (isset($invoicedata[0])) {
			$getbankdetails = ExternalInvoice::getbankdetails($invoicedata[0]->userId);
		}

		$search_flg = 0;
		$order = $request->sortOrder;
		$sort = $request->sortOptn;
		$curTime = date('YmdHis');

		$date_month = $request->selYear."-".$request->selMonth;
		if (!empty($date_month)) {
			$date_month = $date_month;
		} else {
			$date_month = substr($request->qdate, 0, 7);
		}

		$get_view = array();
		$x = 1;
		$TotEstquery = ExternalInvoice::fnGetinvoiceTotVal($request,$date_month);
		foreach ($TotEstquery as $key => $value) {
			$get_view[$x]["id"] = $value->id;
			$x++;
		}

		if(!empty($request->totalrecords)) {
			$totalRec = $request->totalrecords;
			$currentRec = $request->currentRec;
		} else {
			$totalRec = count($get_view);
			if($order == "DESC"){
				$currentRec = 1;
			} else {
				$currentRec = count($get_view);
			}
		}

		$amtcount = 0;
		$dat = array();
		$invoicedataforloop = ExternalInvoice::fnGetInvoiceWorkDtls($request->viewid);
		foreach ($invoicedataforloop as $key => $value) {
			$dat[] = $value->amount;
		} 
		$amtcount = count($dat);

		$grandtotal = 0;
		$dispval = "";
		if (isset($invoicedata[0])){
			if ($invoicedata[0]->tax != 2) {
				$totroundval =  preg_replace("/,/", "", $invoicedata[0]->totalval);
				$dispval = (($totroundval * intval((isset($getinvtaxdetails[0]->Tax)?$getinvtaxdetails[0]->Tax:0)))/100);
				$grandtotal = $totroundval + $dispval;
			} else {
				$totroundval =  preg_replace("/,/", "", $invoicedata[0]->totalval);
				$dispval = 0;
				$grandtotal = $totroundval + $dispval;
			}
		}
		

		$type = "";
		if (isset($getbankdetails[0])) {
			if ($getbankdetails[0]->accountType == 1) {
				$type = "普通";
			} else if ($getbankdetails[0]->accountType == 2) {
				$type = "Other";
			}
		} else {
			$type = "";
		}
		

		return view('ExternalInvoice.view', [	'request' => $request,
												'invoicedata' => $invoicedata,
												'getbankdetails' => $getbankdetails,
												'search_flg' => $search_flg,
												'order' => $order,
												'curTime' => $curTime,
												'sort' => $sort,
												'date_month' => $date_month,
												'get_view' => $get_view,
												'totalRec' => $totalRec,
												'currentRec' => $currentRec,
												'amtcount' => $amtcount,
												'grandtotal' => $grandtotal,
												'dispval' => $dispval,
												'type' => $type
										]);

	}

	/**
	*
	* Excel Download Process for External Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/22
	*
	*/

	public function extinvExceldwnldprocess(Request $request) {

		$curTime = date('Y/m/d  H:i:s');
		$selectedYearMonth = explode("-", $request->selYearMonth);
		$date_month = $request->selYearMonth;

		$TotEstquery = ExternalInvoice::fnGetExtinvoiceDownload($request,$date_month);
		$rowcnt = count($TotEstquery);

		$template_name = 'resources/assets/uploadandtemplates/templates/extinvoice_details.xls';
		$tempname = "ExternalInvoice_".$selectedYearMonth[0].$selectedYearMonth[1];
		$excel_name = $tempname;

		Excel::load($template_name, function($objTpl) use($request, $selectedYearMonth, $TotEstquery, $rowcnt, $curTime) {

			$objTpl->setActiveSheetIndex();
			$objTpl->setActiveSheetIndex(0);
			$objTpl->getActiveSheet()->mergeCells('H1:I1')->getStyle('H1:I1')->getFont()->setBold(false);
			$objTpl->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setCellValue('H1', $curTime);
			$objTpl->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setCellValue('I2', $selectedYearMonth[0]."年".$selectedYearMonth[1]."月分");

			$x = 5;
			$y = 1;
			$z = $x + $rowcnt;
			$totalval = 0;
			$sumdispval1 = 0;
			$sumtotalval = 0;
			$sumgrandtotal = 0;
			$grandtax = 0;
			$get_dat = array();

			foreach ($TotEstquery as $key => $value) {
				if($value->classification == 0) {
					$condition = "作成中";
				} else if ($value->classification == 1) {
					$condition = "承諾済";
				} else if ($value->classification == 2) {
					$condition = "送信済";
				} else {
					$condition = "未使用";
				}
				$totalval = preg_replace('/,/', '', $value->totalval);
				// $totalval = number_format($totalval);
				$sumtotalval += $totalval;

				if($x % 2 == 0){ 
					$objTpl->getActiveSheet()->getStyle('A'.$x.':'.'I'.$x)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
					$objTpl->getActiveSheet()->getStyle('A'.$x.':'.'I'.$x)->getFont()->setBold(false);
				}

				$objTpl->getActiveSheet()->getRowDimension($x)->setRowHeight(28);
				$objTpl->getActiveSheet()->getStyle('A'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('B'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('C'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('D'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('E'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('F'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('G'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->getStyle('H'.$x)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objTpl->getActiveSheet()->setCellValue('A'.$x, $y);
				$objTpl->getActiveSheet()->getStyle('B'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objTpl->getActiveSheet()->setCellValue('B'.$x, $value->invoiceId);
				$objTpl->getActiveSheet()->setCellValue('C'.$x, $condition);
				$objTpl->getActiveSheet()->getStyle('D'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objTpl->getActiveSheet()->setCellValue('D'.$x, $value->payment_date);
				$objTpl->getActiveSheet()->setCellValue('E'.$x, $value->userName);
				$objTpl->getActiveSheet()->setCellValue('F'.$x, $value->ProjectTypeName);
				$objTpl->getActiveSheet()->setCellValue('G'.$x, round($totalval));
				$objTpl->getActiveSheet()->getStyle('G'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$totalval = preg_replace('/,/', '', $value->totalval);
				$getTaxquery = Helpers::fnGetTaxDetails($value->quot_date);

				if(!empty($value->totalval)) {
					if($value->tax != 2) {
						$totroundval = preg_replace("/,/", "", $value->totalval);
						$dispval = (($totroundval * intval((isset($getTaxquery[0]->Tax)?$getTaxquery[0]->Tax:0)))/100);
						$dispval1 = number_format($dispval);
						$dispval1 = preg_replace("/,/", "", $dispval1);
						$grandtotal = $totroundval + $dispval1;
					} else {
						$totroundval = preg_replace("/,/", "", $value->totalval);
						$dispval1 = 0;
						$grandtotal = $totroundval + $dispval1;
					}
					$grandtax = preg_replace("/,/", "", $dispval1);
					$sumdispval1 += $grandtax;
					$sumgrandtotal += round($grandtotal);
				} else {
					$grandtotal = '0';
					$dispval1 = 0;
					$value->totalval='0';
				}

				$objTpl->getActiveSheet()->setCellValue('H'.$x, (isset($dispval1)? $dispval1:'0'));
				$objTpl->getActiveSheet()->getStyle('H'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objTpl->getActiveSheet()->setCellValue('I'.$x, number_format($grandtotal));
				$objTpl->getActiveSheet()->getStyle('I'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$x++;
				$y++;
			}

			$objTpl->getActiveSheet()->mergeCells('A'.$z.':'.'F'.$z)->getStyle('A'.$z.':'.'I'.$z)->getFont()->setBold(true);
			$objTpl->getActiveSheet()->getStyle('A'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
			$objTpl->getActiveSheet()->getStyle('A'.$z.':'.'I'.$z)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
			$objTpl->getActiveSheet()->getRowDimension($z)->setRowHeight(30);
			$objTpl->getActiveSheet()->getStyle('A'.$z.':'.'I'.$z)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->setCellValue('A'.$z, "合計");
			$objTpl->getActiveSheet()->setCellValue('G'.$z, number_format($sumtotalval));
			$objTpl->getActiveSheet()->getStyle('G'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setCellValue('H'.$z, number_format($sumdispval1));
			$objTpl->getActiveSheet()->getStyle('H'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setCellValue('I'.$z, number_format($sumgrandtotal));
			$objTpl->getActiveSheet()->getStyle('I'.$z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setTitle($selectedYearMonth[0].$selectedYearMonth[1]);
			$objTpl->getActiveSheet()->getStyle('A4'.':'.'I'.$z)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->setActiveSheetIndex(0);
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
			$flpath ='.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$flpath.'"');
			header('Cache-Control: max-age=0');

		})->setFilename($excel_name)->download('xls');

	}

	/**
	*
	* Pdf Download Process for External Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/22
	*
	*/

	public static function extinvPdfdwnldprocess(Request $request) {

		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		if (empty($request->pageclick)) {
			$page_no = 1;
		} else {
			$page_no = $request->pageclick;
		}
		$date_month = $request->selYear.'-'.$request->selMonth;
		$singlesearchtxt = $request->singlesearch;
		$username = "";
		$startdate = $request->startdate;
		$enddate = $request->enddate;
		$fil = $request->filter;
		$TotEstquery = ExternalInvoice::fnGetinvoiceTotalValue($request,$date_month,$singlesearchtxt,$username,$startdate,$enddate,$fil);

		$pdf = new FPDI();
		$x_value = "";
		$y_value = "";
		$pdf->AddMBFont( 'MS-Mincho', 'SJIS' );
		$pageCount = $pdf->setSourceFile("resources/assets/uploadandtemplates/templates/extinvoicepdf.pdf");
		$tpl = $pdf->importPage(1);

		for ($m = 0; $m < count($TotEstquery) ; $m++) {
			$totalval = 0;
			$id = $TotEstquery[$m]->id;
			$in_query = ExternalInvoice::fnGetEstiamteDetailsPDFDownload($id);
			$in_amount_query = ExternalInvoice::fnGetAmountDetails($id);
			$data_count = count($in_amount_query);

			$amount_array = array();
			$set_amount_array = array();
			if (isset($in_amount_query[0])) {
				$set_amount_array[0]['id'] = $in_query[0]->id;
				$set_amount_array[0]['invoiceId'] = $in_query[0]->invoiceId;
				$set_amount_array[0]['invoiceNumber'] = $in_query[0]->invoiceNumber;
				$set_amount_array[0]['userId'] = $in_query[0]->userId;
				$set_amount_array[0]['quot_date'] = $in_query[0]->quot_date;
				$set_amount_array[0]['tax'] = $in_query[0]->tax;
				$set_amount_array[0]['pdfFlg'] = $in_query[0]->pdfFlg;
				$set_amount_array[0]['userName'] = $in_query[0]->userName;
				$set_amount_array[0]['bankName'] = $in_query[0]->bankName;
				$set_amount_array[0]['accountNo'] = $in_query[0]->accountNo;
				$set_amount_array[0]['branchName'] = $in_query[0]->branchName;
				$set_amount_array[0]['branchNo'] = $in_query[0]->branchNo;
				$set_amount_array[0]['bankKanaName'] = $in_query[0]->bankKanaName;
				$set_amount_array[0]['special_ins1'] = $in_query[0]->special_ins1;
				$set_amount_array[0]['special_ins2'] = $in_query[0]->special_ins2;
				$set_amount_array[0]['special_ins3'] = $in_query[0]->special_ins3;
				$set_amount_array[0]['special_ins4'] = $in_query[0]->special_ins4;
				$set_amount_array[0]['special_ins5'] = $in_query[0]->special_ins5;
				$parent_array = array('work_specific', 'quantity', 'unit_price', 'amount', 'remarks');
				for ($am = 0; $am < count($in_amount_query); $am++) { 
					for ($qu = 0; $qu < count($parent_array); $qu++) { 
						$amount_array[$am][$qu] = $parent_array[$qu].($am+1);
					}
				}
				foreach ($in_amount_query as $key => $value) {
					for ($st = 0; $st < count($parent_array); $st++) { 
						$get_value = strtolower($parent_array[$st]);
						$set_amount_array[0][$amount_array[$key][$st]] = $value->$get_value;
					}
					$totalval = $totalval + str_replace(',', '', $value->amount);
				}
				$set_amount_array[0]['totalval'] = number_format($totalval);
				$set_amount_array[0] = (object)$set_amount_array[0];
				$in_query = $set_amount_array;
			} else {
				for($i = 1;$i <= 15; $i++) { 
					$work_specificarr = "work_specific".$i;
					$quantityarr = "quantity".$i;
					$unit_pricearr = "unit_price".$i;
					$amountarr = "amount".$i;
					$remarksarr = "remarks".$i;
					if(!empty($in_query)) {
						$in_query[0]->$work_specificarr = "";
						$in_query[0]->$quantityarr = "";
						$in_query[0]->$unit_pricearr = "";
						$in_query[0]->$amountarr = "";
						$in_query[0]->$remarksarr = "";
						$in_query[0]->totalval = 0;
					}
				}
			}

			$execute_tax = Helpers::fnGetTaxDetails($in_query[0]->quot_date);
			$grandtotal = "";
			$dispval = 0;

			if (!empty($in_query[0]->totalval)) {
				if (isset($in_query[0]->tax) && $in_query[0]->tax!= 2) {
					$totroundval = preg_replace("/,/", "", $in_query[0]->totalval);
					$dispval = (($totroundval * intval($execute_tax[0]->Tax))/100);
					$grandtotal = $totroundval + $dispval;
				} else {
					$totroundval = preg_replace("/,/", "", $in_query[0]->totalval);
					$dispval = 0;
					$grandtotal = $totroundval + $dispval;
				}
			}


			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				$templateId = $pdf->importPage($pageNo, '/MediaBox');
				// get the size of the imported page
				$size = $pdf->getTemplateSize($templateId);
				// create a page (landscape or portrait depending on the imported page size)
				if ($size['w'] > $size['h']) {
					$pdf->AddPage('L', array($size['w'], $size['h']));
				} else {
					$pdf->AddPage('P', array($size['w'], $size['h']));
				}

				$pdf->SetAutoPageBreak(false);
				$pdf->useTemplate($templateId);
				// use the imported
				$pdf->SetXY($pdf->GetX() + $x_value, $pdf->GetY() +  $y_value);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetXY(90, 21);
				$pdf->Cell(50, 10, "", 0, 1, 'L', true);
				$pdf->SetXY(20, 76);
				$pdf->Cell(23, 8, "", 0, 1, 'L', true);
				$pdf->SetFont( 'MS-Mincho' ,'B',12);
				$pdf->SetXY(20, 79.5);
				$pdf->Cell(20, 5, mb_convert_encoding("ご請求金額", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
				$pdf->SetFont( 'MS-Mincho' ,'B',20);
				$note = "請求書(控)";
				$pdf->SetXY(90, 21 );
				$pdf->Write(10, iconv('UTF-8', 'SJIS', $note));

				$display = "株式会社 Microbit";
				$display1 = "〒532-0011";
				$display2 = "大阪市淀川区西中島５丁目６-３";
				$display3 = "チサンマンション第２新大阪３０５号";
				$display4 = "Tel:06-6305-1251,Fax:06-6305-1250";

				$pdf->SetFont( 'MS-Mincho' ,'B',10);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetXY(18, 86);
				$pdf->Cell(73, 1, "", 0, 0.8, 'L', true);
				$pdf->SetXY(148, 20);
				$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
				$pdf->SetXY(192, 20);
				$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
				$pdf->SetXY(120.2, 45);

				$pdf->SetFont( 'MS-Mincho' ,'B',10); 
				$pdf->SetXY(135, 45 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $display));

				$pdf->SetFont( 'MS-Mincho' ,'B',10); 
				$pdf->SetXY(135, 50 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $display1));

				$pdf->SetFont( 'MS-Mincho' ,'B',10); 
				$pdf->SetXY(135, 55 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $display2));

				$pdf->SetFont( 'MS-Mincho' ,'B',10); 
				$pdf->SetXY(135, 60 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $display3));

				$pdf->SetFont( 'MS-Mincho' ,'B',10); 
				$pdf->SetXY(135, 65 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $display4));

				$pdf->SetFont( 'MS-Mincho', '', 9); 
				$pdf->SetXY(170, 29 );
				$pdf->Write(4, iconv('UTF-8', 'SJIS', $in_query[0]->invoiceNumber));

				$pdf->SetXY(153, 20 );
				$pdf->Cell(20, 6, "", 0, 1, 'L', true);
				$pdf->SetXY(172, 15.5 );
				$pdf->Cell(20, 6, "", 0, 1, 'L', true);

				$pdf->SetFont( 'MS-Mincho' ,'B',10);
				$pdf->SetXY(170, 15.2 );
				$pdf->Write(6, $in_query[0]->quot_date);

				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(19, 37 );
				$pdf->Write(6, mb_convert_encoding($in_query[0]->userName." 御中", 'SJIS', 'UTF-8'));

				// User Id Red Color
				$pdf->SetTextColor(194,8,8);
				$pdf->SetXY(19, 49 );
				$pdf->Write(6, mb_convert_encoding("",'SJIS','UTF-8'));

				$pdf->SetTextColor(0,0,0);
				$pdf->SetXY(19, 41.6);
				$pdf->Cell(60, 8, "", 0, 1, 'L', true);
				$pdf->Line(19, 43, 100, 43); // 20mm from each edge

				// 下記の通りご請求申し上げます。
				if ($pageNo != 2) {
					$pdf->SetFont('MS-Mincho' ,'','');
					$pdf->SetXY(20, 70);
					$pdf->Cell(60, 6, mb_convert_encoding( "下記の通りご請求申し上げます。", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
				} else {
					$pdf->SetXY(120, 65);
					$pdf->Cell(24, 25, "", 0, 1, 'L', true);
				}

				$pdf->SetFont('MS-Mincho' ,'B',16);
				if($grandtotal == "") {
					$grandtotal = '0';
				}

				$amount = "¥ ".number_format($grandtotal)."-";
				$pdf->SetXY(43, 76.3 );
				$pdf->Cell(41.3, 9.1, iconv('UTF-8', 'SJIS', $amount), 0, 0, 'R');    
				$pdf->SetFont( 'MS-Mincho' ,'B',9);
				$pdf->SetFillColor(175, 175, 175);
				$pdf->SetXY(14.5, 90.8);
				$pdf->Cell(79.9, 6.4, iconv('UTF-8', 'SJIS', "品名"), 'LTRB', 1, 'L', true);
				$pdf->SetXY(94.2, 90.8);
				$pdf->Cell(14.6, 6.4, iconv('UTF-8', 'SJIS', "数量"), 'LRTB', 0, 'L', true);
				$pdf->SetXY(108.7, 90.8);
				$pdf->Cell(28.4, 6.4, iconv('UTF-8', 'SJIS', "単価"), 'LRTB', 0, 'L', true);
				$pdf->SetXY(137.1, 90.8);
				$pdf->Cell(30.3, 6.4, iconv('UTF-8', 'SJIS', "金額"), 'LRTB', 0, 'L', true);
				$pdf->SetXY(167.3, 90.8);
				$pdf->Cell(29, 6.4, iconv('UTF-8', 'SJIS', "摘要"), 'LRTB', 0, 'L', true);

				$y = 0;
				$n = 0;
				$y_axis = 96.9;
				if($data_count < 19){
					$tb_count = 19;
				} else {
					$tb_count = $data_count;
				}

				for ($i = 1; $i <= $tb_count; $i++) {
					$work_specificarr = "work_specific".$i;
					$quantityarr = "quantity".$i;
					$unit_pricearr = "unit_price".$i;
					$amountarr = "amount".$i;
					$remarksarr = "remarks".$i;
					if(!isset($in_query[0]->$work_specificarr)) {
						$in_query[0]->$work_specificarr = "";
						$in_query[0]->$quantityarr = "";
						$in_query[0]->$unit_pricearr = "";
						$in_query[0]->$amountarr = "";
						$in_query[0]->$remarksarr = "";
					}
					$pdf->SetFont( 'MS-Mincho' ,'B', '10');
					if(($i%2)==0){
						$pdf->SetFillColor(220, 220, 220);
					} else {
						$pdf->SetFillColor(255, 255, 255);
					} 

					$inaxis = 96.9 + $y; 
					if($inaxis >= $pdf->h - 20) {
						$pdf->AddPage();
						$y = 0;
						$n = 1;
						$y_axis = 10;
					}

					if($i >= 19) {
						$pdf->SetXY(14.5, $y_axis + $y);
						$pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);
						$pdf->SetXY(19.5, $y_axis + $y);
						$pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
						$pdf->SetXY(19.5, $y_axis + $y);
						$pdf->drawTextBox(mb_convert_encoding($in_query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
						if(!empty($in_query[0]->$quantityarr)) {
							$dotOccur = strpos($in_query[0]->$quantityarr, ".");
							if( $in_query[0]->$quantityarr != "" ){
								if ($dotOccur) {
									$in_query[0]->$quantityarr = $in_query[0]->$quantityarr;
								} else {
									$in_query[0]->$quantityarr = $in_query[0]->$quantityarr.".0";
								}
							}
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->drawTextBox($in_query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
						} else {
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
						}

						$pdf->SetTextColor(0,0,0);
						if (!empty($in_query[0]->$unit_pricearr)) {
							$pdf->SetXY(108.7, $y_axis + $y);
							$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
							$pdf->SetXY(108.7, $y_axis + $y);
							if ($in_query[0]->$unit_pricearr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($in_query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(108.7, $y_axis + $y);
							$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						if (!empty($in_query[0]->$amountarr)) {
							$pdf->SetXY(137.1, $y_axis + $y);
							$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
							$pdf->SetXY(137.1, $y_axis + $y); 
							if($in_query[0]->$amountarr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($in_query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(137.1, $y_axis + $y);
							$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$pdf->SetXY(167.3, $y_axis + $y);
						$pdf->Cell(29, 6.0301, "", 'LRTB', 0, 'LB', true);
						$pdf->SetXY(167.3, $y_axis + $y);
						$pdf->drawTextBox(iconv('UTF-8', 'SJIS',$in_query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
					} else {
						$pdf->SetXY(14.5, 96.9 + $y);
						$pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);
						$pdf->SetXY(19.5, 96.9 + $y);
						$pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
						$pdf->SetXY(19.5, 96.9 + $y);
						$pdf->drawTextBox(mb_convert_encoding($in_query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
						if(!empty($in_query[0]->$quantityarr)) {
							$dotOccur = strpos($in_query[0]->$quantityarr, ".");
							if($in_query[0]->$quantityarr != "" ){
								if ($dotOccur) {
									$in_query[0]->$quantityarr = $in_query[0]->$quantityarr;
								} else {
									$in_query[0]->$quantityarr = $in_query[0]->$quantityarr.".0";
								}
							}
							$pdf->SetXY(94.2, 96.9+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
							$pdf->SetXY(94.2, 96.9+$y);
							$pdf->drawTextBox($in_query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
						} else {
							$pdf->SetXY(94.2, 96.9 + $y);
							$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
						}

						$pdf->SetTextColor(0,0,0);
						if (!empty($in_query[0]->$unit_pricearr)) {
							$pdf->SetXY(108.7, 96.9+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
							$pdf->SetXY(108.7, 96.9+$y);
							if ($in_query[0]->$unit_pricearr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($in_query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(108.7, 96.9+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
						}

						$pdf->SetTextColor(0,0,0);
						if (!empty($in_query[0]->$amountarr)) {
							$pdf->SetXY(137.1, 96.9+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
							$pdf->SetXY(137.1, 96.9+$y); 
							if ($in_query[0]->$amountarr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($in_query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(137.1, 96.9+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$pdf->SetXY(167.3, 96.9+$y);
						$pdf->Cell(29, 6.0301, "", 'LRTB', 0, 'LB', true);
						$pdf->SetXY(167.3, 96.9+$y);
						$pdf->drawTextBox(iconv('UTF-8', 'SJIS', $in_query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
					}
					$y = $y + 6.065;
				}

				if ($n > 0) {
					$ynew = $y + 10; //px = 212
					$yn = $y + 16.065; //px = 218
					$yn1 = $y + 16.165; //px = 218.1
					$new = $y + 22.13; //px = 224
					$new1 = $y + 22.33;  //px = 224.2
					$new11 = $y + 22.53; //px = 224.4
					$new2 = $y + 28.495;  //px = 230.3
					$new21 = $y + 28.695; //px = 230.5
				} else {
					$ynew = $y + 96.9;
					$yn = $y + 102.965;
					$yn1 = $y + 103.065;
					$new = $y + 109.03;
					$new1 = $y + 109.23;
					$new11 = $y + 109.43;
					$new2 = $y + 115.195;
					$new21 = $y + 115.595; 
				}

				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(137, $ynew);
				$pdf->Cell(30.3, 6.1, "", 1, 0, 'R');
				$pdf->SetXY(137, $ynew);
				$pdf->drawTextBox($in_query[0]->totalval, 30.3, 6.1, 'R', 'B');
				$pdf->SetFillColor(175, 175, 175);
				$pdf->SetFont( 'MS-Mincho' ,'B',9);

				if (isset($in_query[0]->tax) && $in_query[0]->tax == 1) {
					$pdf->SetXY(108.7, $ynew);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
					$pdf->SetXY(108.7, $ynew);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計" ), 28.4, 6.1, 'C', 'B');
					$pdf->SetXY(108.7, $yn);
					$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
					$pdf->SetXY(108.7, $yn);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税" ), 28.4, 6.3, 'C', 'B');
					$pdf->SetXY(108.7, $new1);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
					$pdf->SetXY(108.7, $new1);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計" ), 28.4, 6.1, 'C', 'B');
					$pdf->SetFont( 'MS-Mincho' ,'B',11);
					$pdf->SetXY(137, $yn);
					$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
					$pdf->SetXY(137, $yn);
					$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR'); 
					$pdf->SetXY(137, $new); 
					$pdf->Cell(30.3, 6.3, "", 'RB', 0, 'R');
					$pdf->SetXY(137, $new); 
					$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.3, 'R', 'B', 'BR');
					$pdf->SetFont( 'MS-Mincho' ,'B',9);
				} else if (isset($in_query[0]->tax) && $in_query[0]->tax == 2) {
					$pdf->SetXY(108.7, $ynew);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
					$pdf->SetXY(108.7, $ynew);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'C', 'B');
					$pdf->SetXY(108.7, $yn);
					$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
					$pdf->SetXY(108.7, $yn);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"非課税" ), 28.4, 6.3, 'C', 'B');
					$pdf->SetXY(108.7, $new1);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
					$pdf->SetXY(108.7, $new1);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計" ), 28.4, 6.1, 'C', 'B');
					$pdf->SetFont( 'MS-Mincho' ,'B',11);
					$pdf->SetXY(137, $yn);
					$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
					$pdf->SetXY(137, $yn);
					$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR');
					$pdf->SetXY(137, $new); 
					$pdf->Cell(30.3, 6.3, "", 'RB', 0, 'R');
					$pdf->SetXY(137, $new); 
					$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.3, 'R', 'B', 'BR');
					$pdf->SetFont( 'MS-Mincho' ,'B',9);
				} else {
					$pdf->SetXY(108.7, $ynew);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
					$pdf->SetXY(108.7, $ynew);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'C', 'B');
					$pdf->SetXY(108.7, $yn);
					$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
					$pdf->SetXY(108.7, $yn);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税" ), 28.4, 6.3, 'C', 'B');
					$pdf->SetXY(108.7, $new1);
					$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'L', true);
					$pdf->SetFont( 'MS-Mincho' ,'B',11);
					$pdf->SetXY(137, $yn);
					$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
					$pdf->SetXY(137, $yn);
					$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR');
					$pdf->SetXY(137, $new21); 
					$pdf->Cell(30.3, 6.3, "", 'R', 0, 'L');
					$pdf->SetXY(137, $new21);
					$pdf->Cell(30.3, 6.5, "", 1, 0, 'R');
					$pdf->SetXY(137, $new1);
					$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.5, 'R', 'B', 'BR');
					$pdf->SetFont( 'MS-Mincho' ,'B',9);
				}

				$pdf->SetXY(14.5, $ynew);
				$pdf->Cell(25, 6.1, iconv('UTF-8', 'SJIS', "振込口座 "), 'L', 0, 'C', true);  
				$pdf->SetXY(39.5, $ynew);
				$pdf->Cell(5, 6.1, "", 0, 0, 'C');
				$pdf->SetXY(44.5, $ynew);
				$pdf->Cell(64, 6.1, mb_convert_encoding((isset($in_query[0]->bankName)) ? $in_query[0]->bankName : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
				$pdf->SetXY(14.5, $yn1);
				$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "口座番号  "), 'L', 0, 'C', true); 
				$pdf->SetXY(39.5, $yn1);
				$pdf->Cell(5, 6.1, "", 0, 0, 'C');
				$pdf->SetXY(44.5, $yn1);
				if(!isset($in_query[0]->accountNo)) {
					$accountNo = "";
				} else {
					$accountNo = $in_query[0]->accountNo;
				}
				if (isset($in_query[0]->accountType)) {
					if ($in_query[0]->accountType == 1) {
						$type = "普通";
					} else if(isset($in_query[0]->accountType) && $in_query[0]->accountType == 2) {
						$type = "Other";
					} else {
						$type = $in_query[0]->accountType;
					}
				} else {
					$type = "";
				}
				$pdf->Cell(64, 6.3, mb_convert_encoding($type."  ".$accountNo, 'SJIS', 'UTF-8'), 0, 0, 'L');
				$pdf->SetXY(14.5, $new11);
				$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "    支店名"), 'L', 0, 'C', true);
				$pdf->SetXY(39.5, $new11);
				$pdf->Cell(5, 6.1, "", 0, 0, 'C');  
				$pdf->SetXY(44.5, $new11);
				$pdf->Cell(64, 6.3,  mb_convert_encoding((isset($in_query[0]->branchName)) ? $in_query[0]->branchName : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
				$pdf->SetXY(14.5, $new21);
				$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "  口座名"), 'LB', 0, 'C', true);
				$pdf->SetXY(39.5, $new21);
				$pdf->Cell(5, 6.3, "", 'B', 0, 'C');
				$pdf->SetXY(44.5, $new21);
				$pdf->Cell(64, 6.3, mb_convert_encoding((isset($in_query[0]->bankKanaName)) ? $in_query[0]->bankKanaName : '', 'SJIS', 'UTF-8'), 'B', 0, 'L');
				$pdf->SetXY(108.7, $new21);
				$pdf->Cell(28.4, 6.3, "", 'LBR', 0, 'L', true);
				$pdf->SetXY(137, $new21);
				$pdf->Cell(30.3, 6.3, "", 'BR', 0, 'L');

				$arrval = array();
				for ($i = 1; $i <= 5; $i++) {
					$special_insarr="special_ins".$i;
					if($in_query[0]->$special_insarr != "") {
						array_push($arrval, $in_query[0]->$special_insarr);
					}
				}

				$x = 0;
				for ($rccnt = 0; $rccnt < count($arrval); $rccnt++) { 
				}

				if(count($arrval) != 0) {
					$ynot = $ynew + 26.5;
					if (($n == 0 && $ynot + 20 >= $pdf->h - 15 
						&& count($arrval) == 5) 
						|| ($n == 0 && ($ynot + 9) >= $pdf->h - 20 
						&& count($arrval) == 4) 
						|| ($n == 0 && ($ynot + 3) >= $pdf->h - 20 
						&& count($arrval) <= 3)) {

						$pdf->AddPage();
						$ynot = 10;
					}
					$y = 0;
					$exvalue = $rccnt-1;
					$pdf->SetFont( 'MS-Mincho' ,'B',11);
					$pdf->SetXY(22.5 ,$ynot);
					$pdf->Write(6, iconv('UTF-8', 'SJIS',  "【特記事項】"));
					$tilde = '~';//～,〜
					$japtilde = '〜';
					$japreptilde = "～";
					for($i = 0; $i<count($arrval); $i++) {
						$pdf->SetFont( 'MS-Mincho' ,'',10);
						$no = ($rccnt-$exvalue).")";
						$pdf->SetXY(22.5 ,($ynot + 6) + $y);
						$pdf->Write(6, iconv('UTF-8', 'SJIS', $no ));
						$pdf->SetFont( 'MS-Mincho' ,'B',10);
						$pdf->SetXY(26.5 ,($ynot+5)+$y );
						$dispStr = $arrval[$i];
						$dispStr = mb_convert_encoding($dispStr, 'SJIS', 'UTF-8'); 
						$pdf->Write(9, $dispStr);
						$y = $y+5.5;
						$exvalue = $exvalue-1;
					}
				}
				$pdf->SetXY(100 ,-10 );
				$pdf->Write(6, iconv('UTF-8', 'SJIS', $m + 1 .'/'.count($TotEstquery)));
			}

		}

		//download secction
		$path = "../AccountingUpload/ExternalInvoice";
		$id = $date_month;
		if(!is_dir($path)){
			mkdir($path, 0777,true);
		}
		chmod($path, 0777); 
		$files = glob($path . '/' . $id . '*.pdf');
		if ( $files !== false ) {
			$filecount = count($files);
		}
		$pdf_name = "";
		if(isset($in_query[0]->pdfFlg)) { 
			if($in_query[0]->pdfFlg == 0){
				if($filecount != 0){
					$pdf_name = $date_month."_".str_pad($filecount , 2, '0', STR_PAD_LEFT);
					$pdfnamelist = $pdf_name;
				} else {
					$pdf_name = $date_month;
					$pdfnamelist = $pdf_name;
				}
			} else {
				$pdf_name = $date_month;
				$pdfnamelist = $pdf_name;
			}
		}

		// $pdfflg = Invoice::pdfflgset($in_query[0]->invoiceId,$pdfnamelist); 
		$filepath = "../AccountingUpload/ExternalInvoice/".$pdf_name.".pdf";
		$pdf->Output($filepath, 'F');
		chmod($filepath, 0777);
		$pdfname = "MB_EXTERNALINVOICE_".$date_month;
		header('Pragma: public');  // required
		header('Expires: 0');  // no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: application/pdf; charset=utf-8');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filepath)) . ' GMT');
		header('Content-disposition: attachment; filename=' . $pdfname . '.pdf');
		header("Content-Transfer-Encoding:  binary");
		header('Content-Length: ' . filesize($filepath)); // provide file size
		header('Connection: close');
		readfile($filepath);
	}

	/**
	*
	* New Pdf Download Process for External Invoice
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/22
	*
	*/

	public static function extinvnewPdfdwnldproces(Request $request) {

		$totalval = 0;
		$id = $request->invoice_id;
		$in_query = ExternalInvoice::fnGetEstiamteDetailsPDFDownload($id);
		$in_amount_query = ExternalInvoice::fnGetAmountDetails($id);
		$data_count = count($in_amount_query);

		$amount_array = array();
		$set_amount_array = array();
		if (isset($in_amount_query[0])) {
			$set_amount_array[0]['id'] = $in_query[0]->id;
			$set_amount_array[0]['invoiceId'] = $in_query[0]->invoiceId;
			$set_amount_array[0]['invoiceNumber'] = $in_query[0]->invoiceNumber;
			$set_amount_array[0]['userId'] = $in_query[0]->userId;
			$set_amount_array[0]['quot_date'] = $in_query[0]->quot_date;
			$set_amount_array[0]['tax'] = $in_query[0]->tax;
			$set_amount_array[0]['pdfFlg'] = $in_query[0]->pdfFlg;
			$set_amount_array[0]['userName'] = $in_query[0]->userName;
			$set_amount_array[0]['bankName'] = $in_query[0]->bankName;
			$set_amount_array[0]['accountNo'] = $in_query[0]->accountNo;
			$set_amount_array[0]['branchName'] = $in_query[0]->branchName;
			$set_amount_array[0]['branchNo'] = $in_query[0]->branchNo;
			$set_amount_array[0]['bankKanaName'] = $in_query[0]->bankKanaName;
			$set_amount_array[0]['special_ins1'] = $in_query[0]->special_ins1;
			$set_amount_array[0]['special_ins2'] = $in_query[0]->special_ins2;
			$set_amount_array[0]['special_ins3'] = $in_query[0]->special_ins3;
			$set_amount_array[0]['special_ins4'] = $in_query[0]->special_ins4;
			$set_amount_array[0]['special_ins5'] = $in_query[0]->special_ins5;
			$parent_array = array('work_specific', 'quantity', 'unit_price', 'amount', 'remarks');
			for ($am = 0; $am < count($in_amount_query); $am++) { 
				for ($qu = 0; $qu < count($parent_array); $qu++) { 
					$amount_array[$am][$qu] = $parent_array[$qu].($am+1);
				}
			}
			foreach ($in_amount_query as $key => $value) {
				for ($st = 0; $st < count($parent_array); $st++) { 
					$get_value = strtolower($parent_array[$st]);
					$set_amount_array[0][$amount_array[$key][$st]] = $value->$get_value;
				}
				$totalval = $totalval + str_replace(',', '', $value->amount);
			}
			$set_amount_array[0]['totalval'] = number_format($totalval);
			$set_amount_array[0] = (object)$set_amount_array[0];
			$in_query = $set_amount_array;
		} else {
			for($i = 1;$i <= 15; $i++) { 
				$work_specificarr = "work_specific".$i;
				$quantityarr = "quantity".$i;
				$unit_pricearr = "unit_price".$i;
				$amountarr = "amount".$i;
				$remarksarr = "remarks".$i;
				if(!empty($in_query)) {
					$in_query[0]->$work_specificarr = "";
					$in_query[0]->$quantityarr = "";
					$in_query[0]->$unit_pricearr="";
					$in_query[0]->$amountarr="";
					$in_query[0]->$remarksarr="";
					$in_query[0]->totalval=0;
				}
			}
		}

		$execute_tax = Helpers::fnGetTaxDetails($in_query[0]->quot_date);
		$grandtotal = "";
		$dispval = 0;

		if (!empty($in_query[0]->totalval)) {
			if (isset($in_query[0]->tax) && $in_query[0]->tax != 2) {
				$totroundval = preg_replace("/,/", "", $in_query[0]->totalval);
				$dispval = (($totroundval * intval($execute_tax[0]->Tax))/100);
				$grandtotal = $totroundval + $dispval;
			} else {
				$totroundval = preg_replace("/,/", "", $in_query[0]->totalval);
				$dispval = 0;
				$grandtotal = $totroundval + $dispval;
			}
		}

		$pdf = new FPDI();
		$x_value = "";
		$y_value = "";
		$pdf->AddMBFont( 'MS-Mincho', 'SJIS' );
		$pageCount = $pdf->setSourceFile("resources/assets/uploadandtemplates/templates/extinvoicepdf.pdf");

		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
			$templateId = $pdf->importPage($pageNo, '/MediaBox');
			// get the size of the imported page
			$size = $pdf->getTemplateSize($templateId);
			// create a page (landscape or portrait depending on the imported page size)
			if ($size['w'] > $size['h']) {
				$pdf->AddPage('L', array($size['w'], $size['h']));
			} else {
				$pdf->AddPage('P', array($size['w'], $size['h']));
			}

			$pdf->SetAutoPageBreak(false);
			$pdf->useTemplate($templateId);
			// use the imported
			$pdf->SetXY($pdf->GetX() + $x_value, $pdf->GetY() +  $y_value);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetXY(90, 21);
			$pdf->Cell(50, 10, "", 0, 1, 'L', true);
			$pdf->SetXY(20, 76);
			$pdf->Cell(23, 8, "", 0, 1, 'L', true);
			$pdf->SetFont( 'MS-Mincho' ,'B',12);
			$pdf->SetXY(20, 79.5);
			$pdf->Cell(20, 5, mb_convert_encoding("ご請求金額", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
			$pdf->SetFont( 'MS-Mincho' ,'B',20);
			$note = "請求書";
			$pdf->SetXY(90, 21 );
			$pdf->Write(10, iconv('UTF-8', 'SJIS', $note));

			$pdf->SetFont( 'MS-Mincho' ,'B',10);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetXY(18, 86);
			$pdf->Cell(73, 1, "", 0, 0.8, 'L', true);
			$pdf->SetXY(148, 20);
			$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
			$pdf->SetXY(192, 20);
			$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
			$pdf->SetXY(120.2, 45);
			$pdf->Image("resources/assets/images/address.png", 120, 35, 70, 55, 'PNG' );

			$pdf->SetFont( 'MS-Mincho' ,'',9); 
			$pdf->SetXY(170, 29 );
			$pdf->Write(4, iconv('UTF-8', 'SJIS', $in_query[0]->invoiceNumber));

			$pdf->SetXY(153, 20 );
			$pdf->Cell(20, 6, "", 0, 1, 'L', true);
			$pdf->SetXY(172, 15.5 );
			$pdf->Cell(20, 6, "", 0, 1, 'L', true);

			$pdf->SetFont( 'MS-Mincho' ,'B',10);
			$pdf->SetXY(170, 15.2 );
			$pdf->Write(6, $in_query[0]->quot_date);

			$pdf->SetFont( 'MS-Mincho' ,'B',11);
			$pdf->SetXY(19, 37 );
			$pdf->Write(6, mb_convert_encoding($in_query[0]->userName." 御中",'SJIS', 'UTF-8'));

			// User Id Red Color
			$pdf->SetTextColor(194,8,8);
			$pdf->SetXY(19, 49 );
			$pdf->Write(6, mb_convert_encoding("",'SJIS','UTF-8'));

			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(19, 41.6);
			$pdf->Cell(60, 8, "", 0, 1, 'L', true);
			$pdf->Line(19, 43, 100, 43); // 20mm from each edge

			// 下記の通りご請求申し上げます。
			if ($pageNo != 2) {
				$pdf->SetFont('MS-Mincho' ,'','');
				$pdf->SetXY(20, 70);
				$pdf->Cell(60, 6, mb_convert_encoding( "下記の通りご請求申し上げます。", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
			} else {
				$pdf->SetXY(120, 65);
				$pdf->Cell(24, 25, "", 0, 1, 'L', true);
			}

			$pdf->SetFont('MS-Mincho' ,'B',16);
			if($grandtotal == "") {
				$grandtotal = '0';
			}

			$amount = "¥ ".number_format($grandtotal)."-";
			$pdf->SetXY(43, 76.3 );
			$pdf->Cell(41.3, 9.1, iconv('UTF-8', 'SJIS', $amount), 0, 0, 'R');    
			$pdf->SetFont( 'MS-Mincho' ,'B',9);
			$pdf->SetFillColor(175, 175, 175);
			$pdf->SetXY(14.5, 90.8);
			$pdf->Cell(79.9, 6.4, iconv('UTF-8', 'SJIS', "品名"), 'LTRB', 1, 'L', true);
			$pdf->SetXY(94.2, 90.8);
			$pdf->Cell(14.6, 6.4, iconv('UTF-8', 'SJIS', "数量"), 'LRTB', 0, 'L', true);
			$pdf->SetXY(108.7, 90.8);
			$pdf->Cell(28.4, 6.4, iconv('UTF-8', 'SJIS', "単価"), 'LRTB', 0, 'L', true);
			$pdf->SetXY(137.1, 90.8);
			$pdf->Cell(30.3, 6.4, iconv('UTF-8', 'SJIS', "金額"), 'LRTB', 0, 'L', true);
			$pdf->SetXY(167.3, 90.8);
			$pdf->Cell(29, 6.4, iconv('UTF-8', 'SJIS', "摘要"), 'LRTB', 0, 'L', true);

			$y = 0;
			$n = 0;
			$y_axis = 96.9;
			if($data_count < 19){
				$tb_count = 19;
			} else {
				$tb_count = $data_count;
			}

			for ($i = 1; $i <= $tb_count; $i++) {
				$work_specificarr = "work_specific".$i;
				$quantityarr = "quantity".$i;
				$unit_pricearr = "unit_price".$i;
				$amountarr = "amount".$i;
				$remarksarr = "remarks".$i;
				if(!isset($in_query[0]->$work_specificarr)) {
					$in_query[0]->$work_specificarr = "";
					$in_query[0]->$quantityarr = "";
					$in_query[0]->$unit_pricearr = "";
					$in_query[0]->$amountarr = "";
					$in_query[0]->$remarksarr = "";
				}
				$pdf->SetFont( 'MS-Mincho' ,'B', '10');
				if(($i%2)==0){
					$pdf->SetFillColor(220, 220, 220);
				} else {
					$pdf->SetFillColor(255, 255, 255);
				} 

				$inaxis = 96.9 + $y; 
				if($inaxis >= $pdf->h - 20) {
					$pdf->AddPage();
					$y = 0;
					$n = 1;
					$y_axis = 10;
				}

				if($i >= 19) {
					$pdf->SetXY(14.5, $y_axis + $y);
					$pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);
					$pdf->SetXY(19.5, $y_axis + $y);
					$pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
					$pdf->SetXY(19.5, $y_axis + $y);
					$pdf->drawTextBox(mb_convert_encoding($in_query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
					if(!empty($in_query[0]->$quantityarr)) {
						$dotOccur = strpos($in_query[0]->$quantityarr, ".");
						if( $in_query[0]->$quantityarr != "" ){
							if ($dotOccur) {
								$in_query[0]->$quantityarr = $in_query[0]->$quantityarr;
							} else {
								$in_query[0]->$quantityarr = $in_query[0]->$quantityarr.".0";
							}
						}
						$pdf->SetXY(94.2, $y_axis+$y);
						$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
						$pdf->SetXY(94.2, $y_axis+$y);
						$pdf->drawTextBox($in_query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
					} else {
						$pdf->SetXY(94.2, $y_axis+$y);
						$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
					}

					$pdf->SetTextColor(0,0,0);
					if (!empty($in_query[0]->$unit_pricearr)) {
						$pdf->SetXY(108.7, $y_axis + $y);
						$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
						$pdf->SetXY(108.7, $y_axis + $y);
						if ($in_query[0]->$unit_pricearr < 0) {
							$pdf->SetTextColor(255,0,0);
						}
						$pdf->drawTextBox($in_query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
					} else {
						$pdf->SetXY(108.7, $y_axis + $y);
						$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
					}
					$pdf->SetTextColor(0,0,0);
					if (!empty($in_query[0]->$amountarr)) {
						$pdf->SetXY(137.1, $y_axis + $y);
						$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
						$pdf->SetXY(137.1, $y_axis + $y); 
						if($in_query[0]->$amountarr < 0) {
							$pdf->SetTextColor(255,0,0);
						}
						$pdf->drawTextBox($in_query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
					} else {
						$pdf->SetXY(137.1, $y_axis + $y);
						$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
					}
					$pdf->SetTextColor(0,0,0);
					$pdf->SetXY(167.3, $y_axis + $y);
					$pdf->Cell(29, 6.0301, "", 'LRTB', 0, 'LB', true);
					$pdf->SetXY(167.3, $y_axis + $y);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS',$in_query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
				} else {
					$pdf->SetXY(14.5, 96.9 + $y);
					$pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);
					$pdf->SetXY(19.5, 96.9 + $y);
					$pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
					$pdf->SetXY(19.5, 96.9 + $y);
					$pdf->drawTextBox(mb_convert_encoding($in_query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
					if(!empty($in_query[0]->$quantityarr)) {
						$dotOccur = strpos($in_query[0]->$quantityarr, ".");
						if($in_query[0]->$quantityarr != "" ){
							if ($dotOccur) {
								$in_query[0]->$quantityarr = $in_query[0]->$quantityarr;
							} else {
								$in_query[0]->$quantityarr = $in_query[0]->$quantityarr.".0";
							}
						}
						$pdf->SetXY(94.2, 96.9+$y);
						$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
						$pdf->SetXY(94.2, 96.9+$y);
						$pdf->drawTextBox($in_query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
					} else {
						$pdf->SetXY(94.2, 96.9 + $y);
						$pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
					}

					$pdf->SetTextColor(0,0,0);
					if (!empty($in_query[0]->$unit_pricearr)) {
						$pdf->SetXY(108.7, 96.9+$y);
						$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
						$pdf->SetXY(108.7, 96.9+$y);
						if ($in_query[0]->$unit_pricearr < 0) {
							$pdf->SetTextColor(255,0,0);
						}
						$pdf->drawTextBox($in_query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
					} else {
						$pdf->SetXY(108.7, 96.9+$y);
						$pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
					}

					$pdf->SetTextColor(0,0,0);
					if (!empty($in_query[0]->$amountarr)) {
						$pdf->SetXY(137.1, 96.9+$y);
						$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
						$pdf->SetXY(137.1, 96.9+$y); 
						if ($in_query[0]->$amountarr < 0) {
							$pdf->SetTextColor(255,0,0);
						}
						$pdf->drawTextBox($in_query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
					} else {
						$pdf->SetXY(137.1, 96.9+$y);
						$pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
					}
					$pdf->SetTextColor(0,0,0);
					$pdf->SetXY(167.3, 96.9+$y);
					$pdf->Cell(29, 6.0301, "", 'LRTB', 0, 'LB', true);
					$pdf->SetXY(167.3, 96.9+$y);
					$pdf->drawTextBox(iconv('UTF-8', 'SJIS', $in_query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
				}
				$y = $y + 6.065;
			}

			if ($n > 0) {
				$ynew = $y + 10; //px = 212
				$yn = $y + 16.065; //px = 218
				$yn1 = $y + 16.165; //px = 218.1
				$new = $y + 22.13; //px = 224
				$new1 = $y + 22.33;  //px = 224.2
				$new11 = $y + 22.53; //px = 224.4
				$new2 = $y + 28.495;  //px = 230.3
				$new21 = $y + 28.695; //px = 230.5
			} else {
				$ynew = $y + 96.9;
				$yn = $y + 102.965;
				$yn1 = $y + 103.065;
				$new = $y + 109.03;
				$new1 = $y + 109.23;
				$new11 = $y + 109.43;
				$new2 = $y + 115.195;
				$new21 = $y + 115.595; 
			}

			$pdf->SetFont( 'MS-Mincho' ,'B',11);
			$pdf->SetXY(137, $ynew);
			$pdf->Cell(30.3, 6.1, "", 1, 0, 'R');
			$pdf->SetXY(137, $ynew);
			$pdf->drawTextBox($in_query[0]->totalval, 30.3, 6.1, 'R', 'B');
			$pdf->SetFillColor(175, 175, 175);
			$pdf->SetFont( 'MS-Mincho' ,'B',9);

			if (isset($in_query[0]->tax) && $in_query[0]->tax == 1) {
				$pdf->SetXY(108.7, $ynew);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
				$pdf->SetXY(108.7, $ynew);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計" ), 28.4, 6.1, 'C', 'B');
				$pdf->SetXY(108.7, $yn);
				$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
				$pdf->SetXY(108.7, $yn);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税" ), 28.4, 6.3, 'C', 'B');
				$pdf->SetXY(108.7, $new1);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
				$pdf->SetXY(108.7, $new1);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計" ), 28.4, 6.1, 'C', 'B');
				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(137, $yn);
				$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
				$pdf->SetXY(137, $yn);
				$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR'); 
				$pdf->SetXY(137, $new); 
				$pdf->Cell(30.3, 6.3, "", 'RB', 0, 'R');
				$pdf->SetXY(137, $new); 
				$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.3, 'R', 'B', 'BR');
				$pdf->SetFont( 'MS-Mincho' ,'B',9);
			} else if (isset($in_query[0]->tax) && $in_query[0]->tax == 2) {
				$pdf->SetXY(108.7, $ynew);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
				$pdf->SetXY(108.7, $ynew);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'C', 'B');
				$pdf->SetXY(108.7, $yn);
				$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
				$pdf->SetXY(108.7, $yn);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"非課税" ), 28.4, 6.3, 'C', 'B');
				$pdf->SetXY(108.7, $new1);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
				$pdf->SetXY(108.7, $new1);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計" ), 28.4, 6.1, 'C', 'B');
				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(137, $yn);
				$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
				$pdf->SetXY(137, $yn);
				$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR');
				$pdf->SetXY(137, $new); 
				$pdf->Cell(30.3, 6.3, "", 'RB', 0, 'R');
				$pdf->SetXY(137, $new); 
				$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.3, 'R', 'B', 'BR');
				$pdf->SetFont( 'MS-Mincho' ,'B',9);
			} else {
				$pdf->SetXY(108.7, $ynew);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', 'B', true);
				$pdf->SetXY(108.7, $ynew);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'C', 'B');
				$pdf->SetXY(108.7, $yn);
				$pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
				$pdf->SetXY(108.7, $yn);
				$pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税" ), 28.4, 6.3, 'C', 'B');
				$pdf->SetXY(108.7, $new1);
				$pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'L', true);
				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(137, $yn);
				$pdf->Cell(30.3, 6.2, "", 'BR', 0, 'R');
				$pdf->SetXY(137, $yn);
				$pdf->drawTextBox(number_format($dispval), 30.3, 6.2, 'R', 'B', 'BR');
				$pdf->SetXY(137, $new21); 
				$pdf->Cell(30.3, 6.3, "", 'R', 0, 'L');
				$pdf->SetXY(137, $new21);
				$pdf->Cell(30.3, 6.5, "", 1, 0, 'R');
				$pdf->SetXY(137, $new1);
				$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.5, 'R', 'B', 'BR');
				$pdf->SetFont( 'MS-Mincho' ,'B',9);
			}

			$pdf->SetXY(14.5, $ynew);
			$pdf->Cell(25, 6.1, iconv('UTF-8', 'SJIS', "振込口座 "), 'L', 0, 'C', true);  
			$pdf->SetXY(39.5, $ynew);
			$pdf->Cell(5, 6.1, "", 0, 0, 'C');
			$pdf->SetXY(44.5, $ynew);
			$pdf->Cell(64, 6.1, mb_convert_encoding((isset($in_query[0]->bankName)) ? $in_query[0]->bankName : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
			$pdf->SetXY(14.5, $yn1);
			$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "口座番号  "), 'L', 0, 'C', true); 
			$pdf->SetXY(39.5, $yn1);
			$pdf->Cell(5, 6.1, "", 0, 0, 'C');
			$pdf->SetXY(44.5, $yn1);
			if (isset($in_query[0]->accountType)) {
				if ($in_query[0]->accountType == 1) {
					$type = "普通";
				} else if(isset($in_query[0]->accountType) && $in_query[0]->accountType == 2) {
					$type = "Other";
				} else {
					$type = $in_query[0]->accountType;
				}
			} else {
				$type = "";
			}
			if(!isset($in_query[0]->accountNo)) {
				$accountNo = "";
			} else {
				$accountNo = $in_query[0]->accountNo;
			}
			$pdf->Cell(64, 6.3, mb_convert_encoding($type."  ".$accountNo, 'SJIS', 'UTF-8'), 0, 0, 'L');
			$pdf->SetXY(14.5, $new11);
			$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "    支店名"), 'L', 0, 'C', true);
			$pdf->SetXY(39.5, $new11);
			$pdf->Cell(5, 6.1, "", 0, 0, 'C');  
			$pdf->SetXY(44.5, $new11);
			$pdf->Cell(64, 6.3,  mb_convert_encoding((isset($in_query[0]->branchName)) ? $in_query[0]->branchName : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
			$pdf->SetXY(14.5, $new21);
			$pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "  口座名"), 'LB', 0, 'C', true);
			$pdf->SetXY(39.5, $new21);
			$pdf->Cell(5, 6.3, "", 'B', 0, 'C');
			$pdf->SetXY(44.5, $new21);
			$pdf->Cell(64, 6.3, mb_convert_encoding((isset($in_query[0]->bankKanaName)) ? $in_query[0]->bankKanaName : '', 'SJIS', 'UTF-8'), 'B', 0, 'L');
			$pdf->SetXY(108.7, $new21);
			$pdf->Cell(28.4, 6.3, "", 'LBR', 0, 'L', true);
			$pdf->SetXY(137, $new21);
			$pdf->Cell(30.3, 6.3, "", 'BR', 0, 'L');

			$arrval = array();
			for ($i = 1; $i <= 5; $i++) {
				$special_insarr="special_ins".$i;
				if($in_query[0]->$special_insarr != "") {
					array_push($arrval, $in_query[0]->$special_insarr);
				}
			}

			$x = 0;
			for ($rccnt = 0; $rccnt < count($arrval); $rccnt++) { 
			}

			if(count($arrval) != 0) {
				$ynot = $ynew + 26.5;
				if (($n == 0 && $ynot + 20 >= $pdf->h - 15 
					&& count($arrval) == 5) 
					|| ($n == 0 && ($ynot + 9) >= $pdf->h - 20 
					&& count($arrval) == 4) 
					|| ($n == 0 && ($ynot + 3) >= $pdf->h - 20 
					&& count($arrval) <= 3)) {

					$pdf->AddPage();
					$ynot = 10;
				}
				$y = 0;
				$exvalue = $rccnt-1;
				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(22.5 ,$ynot);
				$pdf->Write(6, iconv('UTF-8', 'SJIS',  "【特記事項】"));
				$tilde = '~';//～,〜
				$japtilde = '〜';
				$japreptilde = "～";
				for($i = 0; $i<count($arrval); $i++) {
					$pdf->SetFont( 'MS-Mincho' ,'',10);
					$no = ($rccnt-$exvalue).")";
					$pdf->SetXY(22.5 ,($ynot + 6) + $y);
					$pdf->Write(6, iconv('UTF-8', 'SJIS', $no ));
					$pdf->SetFont( 'MS-Mincho' ,'B',10);
					$pdf->SetXY(26.5 ,($ynot + 5) + $y );
					$dispStr = $arrval[$i];
					$dispStr = mb_convert_encoding($dispStr, 'SJIS', 'UTF-8'); 
					$pdf->Write(9, $dispStr);
					$y = $y + 5.5;
					$exvalue = $exvalue - 1;
				}
			}
		}

		//download secction
		$path = "../AccountingUpload/ExternalInvoice";
		if (isset($in_query[0])) {
			$id = $in_query[0]->invoiceId;
		} else {
			$id = "PdfDwnld";
		} 
		if(!is_dir($path)){
			mkdir($path, 0777,true);
		}
		chmod($path, 0777); 
		$files = glob($path . '/' . $id . '*.pdf');
		if ( $files !== false ) {
			$filecount = count($files);
		}
		$pdf_name = "";
		if(isset($in_query[0]->pdfFlg) && $in_query[0]->pdfFlg == 0) { 
			if($filecount != 0){
				$pdf_name = $in_query[0]->invoiceId."_".str_pad($filecount , 2, '0', STR_PAD_LEFT);
				$pdfnamelist = $pdf_name;
			} else {
				$pdf_name = $in_query[0]->invoiceId;
				$pdfnamelist = $pdf_name;
			}
		} else {
			$pdf_name = $in_query[0]->invoiceId;
			$pdfnamelist = $pdf_name;
		}

		$pdfflg = ExternalInvoice::pdfflgset($in_query[0]->invoiceId,$pdfnamelist); 
		$filepath = "../AccountingUpload/ExternalInvoice/".$pdf_name.".pdf";
		$pdf->Output($filepath, 'F');
		chmod($filepath, 0777);
		$pdfname = $pdf_name;
		header('Pragma: public');  // required
		header('Expires: 0');  // no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: application/pdf; charset=utf-8');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filepath)) . ' GMT');
		header('Content-disposition: attachment; filename=' . $pdfname . '.pdf');
		header("Content-Transfer-Encoding:  binary");
		header('Content-Length: ' . filesize($filepath)); // provide file size
		header('Connection: close');
		readfile($filepath);
	}

}