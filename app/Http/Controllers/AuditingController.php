<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\Auditing;
use App\Model\Payment;
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

class AuditingController extends Controller {

	public function index(Request $request) {
	if(Session::get('selYear') !="") {
		$request->selYear =  Session::get('selYear');
		$request->selMonth =  Session::get('selMonth');
	}
	$disabledall="";
	$disabledcreating="";
	$disabledapproved="";
	$disabledunused="";
	$disabledsend="";
	$sortarray="";
	$dispval1="";
	if (isset($request->invoicestatusid) && $request->invoicestatusid != "") {
		Invoice::updateClassification($request);
	}
	if(!isset($request->filter) || $request->filter=="") {
		$request->filter = 1;
		$fil = 1;
		$disabledall = "disabled fb";
	} else if($request->filter == 1) {
		$fil = 1;
		$disabledall = "disabled fb";
	} elseif($request->filter == 2) {
		$fil = 2;
		$disabledcreating="disabled fb";
	} elseif($request->filter==3) {
		$fil = 3;
		$disabledapproved="disabled fb";
	} elseif($request->filter == 4) {
		$fil=4;
		$disabledunused="disabled fb";
	} elseif($request->filter == 5) {
		$fil=5;
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
						'user_id'=> trans('messages.lbl_invoiceno'),
						'quot_date'=> trans('messages.lbl_billingdate'),
						'company_name'=> trans('messages.lbl_customer')];
	$request->invoicesort = $request->sortOptn;
	$request->sortOptn = $request->sortOptn;
	$srt = $request->invoicesort;
	$odr = $request->sortOrder;
	if ($request->invoicesort == "") {
		$request->invoicesort = "user_id";
	}
	//SORTING PROCESS
	if (empty($request->sortOrder)) {
		$request->sortOrder = "desc";
	}
	if ($request->sortOrder == "asc") {  
		$request->sortstyle="sort_asc";
	} else {
		$request->sortstyle="sort_desc";
	}
	if ($request->searchmethod == 1 || $request->searchmethod == 2) {
		$sortMargin = "margin-right:260px;";
	} else {
		$sortMargin = "margin-right:0px;";
	}
	$search_flg = 0;
	$prjtypequery = Estimation::fnGetProjectType($request);
	$singlesearchtxt = trim($request->singlesearchtxt);
	$estimateno = trim($request->estimateno);
	$companyname= "";
	if ( $request->companyname != "" ) {
		$companyname = trim($request->companyname);
		$request->companynameClick = "";
	} else if ($request->companynameClick != "" ) {
		$companyname = trim($request->companynameClick);
		$request->companyname = "";
		$disabledall = "";
	}
	$startdate = $request->startdate;
	$enddate = $request->enddate;
	if($request->projecttype=="a") {
		$projecttype = "";
	} else {
		$projecttype = $request->projecttype;
	}
	if($request->protype2=="0"){
		$taxSearch = "";
	}else{
		$taxSearch = $request->protype2;
	}
	// For Payment
	$get_payment_query = Invoice::fnGetPaymentCheck($request);
	if (empty($get_payment_query)) {
		$upt_invoice_query = Invoice::fnUpdateInvoice($request);
	}
	// End of Payment
	$accountperiod = Estimation::fnGetAccountPeriod($request);
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
	$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
	$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";

	$est_query = Invoice::fnGetEstimateRecord($from_date, $to_date);
	$dbrecord = array();
	foreach ($est_query as $key => $value) {
		$dbrecord[]=$value->quot_date;
	}
	$est_query1 = Invoice::fnGetEstimateRecordPrevious($from_date);
	$dbprevious = array();
	$dbpreviousYr = array();
	$pre = 0;
	foreach ($est_query1 as $key => $value) {
		$dbpreviousYr[]=substr($value->quot_date, 0, 4);
		$dbprevious[]=$value->quot_date;
		$pre++;
	}
	$est_query2 = Invoice::fnGetEstimateRecordNext($to_date);
	$dbnext = array();
	foreach ($est_query2 as $key => $value) {
		$dbnext[]=$value->quot_date;
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
	$account_val="";
	$arr_yr_mn = array_keys($year_month);
	$yr_mn="";
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
	if($request->selYear=="") {
		$request->selYear=date("Y");
		$request->selMonth=date("m");
	}
	if (isset($request->date_month)) {
		$date_month = $request->date_month;
	} else {
		$date_month=$request->selYear."-".$request->selMonth;
	}
	//------
	$TotEstquery = Auditing::fnGetinvoiceTotalValue($request,$taxSearch,$date_month,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate,$fil);
	$get_view=array();
	$totalcount=count($TotEstquery);
	$x = 1;
	foreach ($TotEstquery as $key => $value) {
		$get_view[$x]["id"] = $value->id;
		$x++;
	}
	$explode=array();
	$splitYrMn = explode("-", $date_month);
	$cur_year=$splitYrMn[0];
	$cur_month=str_pad($splitYrMn[1], 2, "0", STR_PAD_LEFT);
	if (isset($_REQUEST['selMonth'])) {
		$selectedMonth=$_REQUEST['selMonth'];
		$selectedYear=$_REQUEST['selYear'];
		$cur_month=$selectedMonth;
		$cur_year=$selectedYear;
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
			for($z=$B; $z<$cou;$z++) {
				unset($dbprevious[$z]);
			}
		}
	}
	$inv = array();
	$i = 0;
	$ckmail=array();
	foreach ($TotEstquery as $key => $value) {
		$inv[$i]['id'] = $value->id;
		$getallsendmail = Estimation::fnGetallsendmails($value->user_id,$date_month);
		if($getallsendmail) {
			$ckmail[] = $getallsendmail[0]->sendFlg;
		} else {
			$ckmail[] = 0;
		}
		$i++;
	}
	$invbal=array();
	for ($k=0; $k < count($inv); $k++) { 
		$query = Invoice::fnGetBalanceDetails($inv[$k]['id']);
		if(!empty($query)) {
			$split = explode(",", $query[0]->paid_id);
			for ($y=0; $y < count($inv); $y++) {
				if (end($split) == (isset($inv[$y]['id']) ? $inv[$y]['id'] : "") ) {
					$invbal[$y]['bal_amount'] = str_replace(",", "",$query[0]->totalval);
				}
			}
		}
	}
	if($dbprevious == "" || $dbnext == "" || $db_year_month == "" || $year_month == "") {
		$dbnext = array();
		$dbprevious = array();
	}
	$totalval=0;
	$divtotal=0;
	$invoicetotalamount=(isset($query[0]->totalval)?$query[0]->totalval:0);
	$invoicedepositamt=(isset($query[0]->deposit_amount)?$query[0]->deposit_amount:0);
	$paid_amount=0;
	$bal_amount=0;
	$grand_style="";
	$grandtotal=0;
	$balance_style="";
	$balance=0;
	$paid_amo=0;
	$selectboxtext = Invoice::Fntogetprojecttype($request);
	$othersArray = array('0' => trans('messages.lbl_creating'),
						'1' => trans('messages.lbl_approved'),
						'2' => trans('messages.lbl_sent'),
						'3' => trans('messages.lbl_unused'));
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

	return view('Auditing.index',[
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
								'prjtypequery' => $prjtypequery,
								'inv' => $inv,
								'paid_amo' => $paid_amo,
								'invbal' => $invbal,
								'divtotal'=>$divtotal,
								'paid_amount'=>$paid_amount,
								'bal_amount'=>$bal_amount,
								'grandtotal' =>$grandtotal,
								'balance_style' => $balance_style,
								'grand_style'=>$grand_style,
								'invoicetotalamount'=>$invoicetotalamount,
								'invoicedepositamt'=>$invoicedepositamt,
								'selectboxtext' => $selectboxtext,
								'ckmail' => $ckmail,
								'othersArray' => $othersArray,
								'request' => $request]);
	}

	public function auditingexldwnldprocess(Request $request) {
		$curTime = date('Y/m/d  H:i:s');
		$selectedYearMonth = explode("-", $request->selYearMonth);
		$date_month = $request->selYearMonth;
		//print_r($date_month);
		$TotEstquery = Auditing::fnGetinvoiceDownload($request,$date_month);
		$rowcnt = count($TotEstquery);
		// print_r($TotEstquery);exit();
		$template_name = 'resources/assets/uploadandtemplates/templates/invoice_details.xls';
		$tempname = "Invoice_".$selectedYearMonth[0].$selectedYearMonth[1];
		$excel_name=$tempname;
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
			$totalval=0;
			$sumdispval1=0;
			$sumtotalval=0;
			$sumgrandtotal=0;
			$grandtax=0;
			$get_dat=array();
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
				//$totalval = number_format($totalval);
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
				$objTpl->getActiveSheet()->setCellValue('B'.$x, $value->user_id);
				$objTpl->getActiveSheet()->setCellValue('C'.$x, $condition);
				$objTpl->getActiveSheet()->getStyle('D'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objTpl->getActiveSheet()->setCellValue('D'.$x, $value->payment_date);
				$objTpl->getActiveSheet()->setCellValue('E'.$x, $value->company_name);
				$objTpl->getActiveSheet()->setCellValue('F'.$x, $value->ProjectType);
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
					}else{
						$totroundval = preg_replace("/,/", "", $value->totalval);
						$dispval1 = 0;
						$grandtotal = $totroundval + $dispval1;
					}
					$grandtax = preg_replace("/,/", "", $dispval1);
					$sumdispval1 += $grandtax;
					$sumgrandtotal += round($grandtotal);
				}else{
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
			$flpath='.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$flpath.'"');
			header('Cache-Control: max-age=0');
		})->setFilename($excel_name)->download('xls');
	}

}