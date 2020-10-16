<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\SendingMail;
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


class EstimationController extends Controller {
	function index(Request $request) { 
		if(Session::get('selYear') !=""){
			$request->selYear=Session::get('selYear');
		}
		if(Session::get('selMonth') !=""){
			$request->selMonth=Session::get('selMonth');
		}
		if (isset($request->estimatestatusid) && $request->estimatestatusid != "") {
			$done = Estimation::updateClassification($request);
			if($done) {
				Session::flash('success', 'Status Changed Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Status Changed Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		}
		$sample=array();
		$getmail=array();
		$disabledall="";
		$disabledestimates="";
		$disabledapproved="";
		$disabledunused="";
		$disabledsend="";
		$sortarray="";
		$hideyearbar="";
		$estimationsortarray = array("user_id"=>trans('messages.lbl_estimateno'),
									"quot_date"=>trans('messages.lbl_dateofissue'),
									"company_name"=>trans('messages.lbl_customer'));
		if(!isset($request->filter) || $request->filter=="") {
			$request->filter=1;
			$fil=1;
			$disabledall="disabled fb";
		} else if($request->filter==1) {
			$fil=1;
			$disabledall="disabled fb";
		} elseif($request->filter==2) {
			$fil=2;
			$disabledestimates="disabled fb";
		} elseif($request->filter==3) {
			$fil=3;
			$disabledapproved="disabled fb";
		} elseif($request->filter==4) {
			$fil=4;
			$disabledunused="disabled fb";
		} elseif($request->filter==5) {
			$fil=5;
			$disabledsend="disabled fb";
		}
		if(isset($request->selMonth)) {
			$request->selMonth = str_pad($request->selMonth, 2, "0", STR_PAD_LEFT);
		}
		// if(isset($request->topclick) && $request->topclick == 1) {
		// 	$request->plimit = "";
		// 	$request->pageclick = "";
		// }
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		if (empty($request->pageclick)) {
			$page_no = 1;
		} else {
			$page_no = $request->pageclick;
		}
		if (isset($request->dbselect)) {
			$dbselect = $request->dbselect;
		} else {
			$dbselect = 1;
		}
		// $userid = $_SESSION['Emp_ID'];
		if (empty($request->sorting)) {
			$srt = "user_id";
			$request->sorting = "user_id";
		}
		if ($request->sorting == "user_id") {
			$srt = "user_id";
		} else if ($request->sorting == "quot_date") {
			$srt = "quot_date";
		} else if ($request->sorting == "company_name") {
			$srt = "company_name";
		} else {
			$srt = "user_id";
		}
		// if (empty($request->ordervalue)) {
		// 	$odr = "desc";
		// 	$request->sortstyle = "sort_desc";
		// }
		//SORTING PROCESS
		// if (empty($request->sortOrder)) {
  //   		$request->sortOrder = "asc";
  // 		}
  // 		if ($request->sortOrder == "asc") {  
  // 			$request->sortstyle="sort_asc";
  // 		} else {  
  // 			$request->sortstyle="sort_desc";
  // 		}
		if ($request->ordervalue == "asc") {
			$odr = "asc";
			$request->sortstyle = "sort_asc";
		} else {
			$request->sortstyle = "sort_desc";
			$odr = "desc";
		}
		// if (empty($request->sorting)) {
		// 	$lsort = 1;
		// 	$request->sorting = 1;
		// } else {
		// 	$lsort = $request->sorting;
		// }
		// if (empty($request->ordervalue)) {
		// 	$odrval = 0;
		// } else {
		// 	$odrval = $request->ordervalue;
		// }
		$search_flg = 0;
		$searchmethod = 0;
		if (!empty($request->singlesearchtxt)||$request->singlesearchtxt=="0") {
			$search_flg = 1;
			$hideyearbar="1";
			$searchmethod = 1;
		} else if (!empty($request->estimateno)||$request->estimateno=="0") {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		} else if (!empty($request->companyname)||$request->companyname=="0") {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		} 
		else if (!empty($request->startdate)) {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		} else if (!empty($request->enddate)) {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		}else if (!empty($request->projecttype)) {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		}else if (!empty($request->taxSearch)) {
			$search_flg = 2;
			$hideyearbar="1";
			$searchmethod = 1;
		}
		//SORT POSITION
		$prjtypequery = Estimation::fnGetProjectType($request);
		$singlesearchtxt = trim($request->singlesearchtxt);
		$estimateno =trim($request->estimateno);
		$companyname="";
		if ( $request->companyname != "" ) {
			$companyname = trim($request->companyname);
			$request->companynameClick = "";
			$hideyearbar="1";
			$searchmethod = 1;
			
		} else if ($request->companynameClick != "" ) {
			$estimationsortarray = array("user_id"=>trans('messages.lbl_estimateno'),
									"quot_date"=>trans('messages.lbl_dateofissue'));
			$companyname = trim($request->companynameClick);
			$request->companyname = "";
			$hideyearbar="1";
			$searchmethod = 0;
		}
        $sortMargin = "margin-right:0px;";
		if($hideyearbar=="1") {
			$request->hideyearbar=1;
			$disabledall="";
			if($request->companynameClick=="") {
				$sortMargin = "margin-right:260px;";
			}
		} else {
			$request->hideyearbar=0;
          	$sortMargin = "margin-right:0px;";
        }
		$startdate = $request->startdate;
		$enddate = $request->enddate;
		if($request->projecttype=="a") {
			$projecttype="";
		} else {
			$projecttype = $request->projecttype;
		}
		
		if($request->taxSearch=="0"){
			$taxSearch="";
		}else{
			$taxSearch = $request->taxSearch;
		}
		$accountperiod = Estimation::fnGetAccountPeriod($request);
		// print_r($accountperiod);exit;
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
			if (date('m') > $account_close_mn) {
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
		
		$est_query = Estimation::fnGetEstimateRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->quot_date;
		}

		$est_query1 = Estimation::fnGetEstimateRecordPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->quot_date, 0, 4);
			$dbprevious[]=$value->quot_date;
			$pre++;
		}

		$est_query2 = Estimation::fnGetEstimateRecordNext($to_date);
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
		}else{
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$split_val = explode("-", $dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
		}

		$year_month = array();
		if(!empty($dbprevious[$pre-1])) {
			$split_vpre = explode("-", $dbprevious[$pre-1]);
			if( $account_close_mn < $split_vpre[1] ) {
				$pre_yr_mn = $split_vpre[0];
				$nex_yr_mn = $split_vpre[0]+1;
			} else {
				$pre_yr_mn = $split_vpre[0]-1;
				$nex_yr_mn = $split_vpre[0];
			}
		}
		if ($account_close_mn == 12) {
			if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {
				/*for ($i = ($account_period + 1); $i <= 12; $i++) {
					$year_month[($split_vpre[0]-1)][$i] = $i;
				}*/

				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$nex_yr_mn][$i] = $i;
				}
				$last_year = $pre_yr_mn;
				$current_year = $nex_yr_mn;
			}else{
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
			}else{
				for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
					$year_month[$last_year][$i] = $i;
				}

				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$current_year][$i] = $i;
				}
			}
		}

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
		} else {
			$account_val = $account_period;
		}
		if($request->selYear=="") {
			$request->selYear=date("Y");
			$request->selMonth=date("m");
		}
		$date_month=$request->selYear."-".$request->selMonth;
		if ($request->companynameClick != "" ) {
			$dtmn="";
		} else {
			$dtmn=$date_month;
		}
		$Estquery = Estimation::fnGetEstimateDetails($request,$taxSearch,$projecttype,$search_flg,$dtmn, $singlesearchtxt, $estimateno, $companyname, $startdate, $enddate, $srt, $odr, $fil);
		$disp = 0;
		$disp = count($Estquery);
		$TotEstquery = Estimation::fnGetEstimateTotalValue($request,$taxSearch,$dtmn,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate, $srt, $odr);
		$splitYrMn = explode("-", $date_month);
		$cur_year=$splitYrMn[0];
		$cur_month=str_pad($splitYrMn[1], 2, "0", STR_PAD_LEFT);
		if (isset($request->selMonth)) {
			$selectedMonth=$request->selMonth;
			$selectedYear=$request->selYear;
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
				$B=end($aryEnd);
				$cou=count($dbprevious);
				for($z=$B; $z<$cou;$z++) {
					unset($dbprevious[$z]);
				}
			}
		}

		$taxarray = array("3"=>trans('messages.lbl_only'),
						"2"=>trans('messages.lbl_notincluded'),
						"1"=>trans('messages.lbl_withoutax')
						);
		$totval=0;
		foreach ($Estquery as $key => $data) {
			$totval += preg_replace('/,/', '', $data->totalval);
		}
		$othersArray = array('0' => trans('messages.lbl_estimates'),
							 '1' => trans('messages.lbl_approved'),
							 '2' => trans('messages.lbl_sent'),
							 '3' => trans('messages.lbl_unused'));
		return view('Estimation.index',[
									'account_period' => $account_period,
									'year_month' => $year_month,
									'db_year_month' => $db_year_month,
									'date_month' => $date_month,
									'dbnext' => $dbnext,
									'dbprevious' => $dbprevious,
									'last_year' => $last_year,
									'current_year' => $current_year,
									'account_val' => $account_val,
									'Estquery' => $Estquery,
									'TotEstquery' => $TotEstquery,
									'sortarray' => $sortarray,
									'sortMargin' => $sortMargin,
									'totval' => $totval,
									'estimationsortarray' => $estimationsortarray,
									'disabledall' => $disabledall,
									'disabledestimates' => $disabledestimates,
									'disabledapproved' => $disabledapproved,
									'disabledunused' => $disabledunused,
									'disabledsend' => $disabledsend,
									'prjtypequery' => $prjtypequery,
									'taxarray' => $taxarray,
									'othersArray' => $othersArray,
									'hideyearbar' => $hideyearbar,
									'searchmethod' => $searchmethod,
									'request' => $request]);
	}
	function addedit(Request $request) { 
		if (!isset($request->editid)) {
		return Redirect::to('Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$sample="";
		$recentcustomer = Estimation::fnGetCustomerDetails($request);
		$totalcustomer = Estimation::fnexistingcustomer($request);
		$existingcustomer=array_diff($totalcustomer,$recentcustomer);
		$prjtypequery = Estimation::fnGetProjectType($request);
		$notice = Estimation::fnGetOtherDetails($request);
		$estimate="";
		$amtcount = 0;
		if($request->editid!="" && $request->editflg!="add") {
			$estimate = Estimation::fnGetEstimateUserDataADD($request);
			
			$dat = array();
        	foreach ($estimate as $key => $value) {
            $dat[]= $value->amount;
        	} 
        	$amtcount = count($dat);
		}
		$montharray = array("1"=>trans('messages.lbl_presentmonth'),
									"2"=>trans('messages.lbl_nextmonth'),
									"3"=>trans('messages.lbl_nextnextmonth'),
									"4"=>trans('messages.lbl_Others'));
		return view('Estimation.addedit',[
									'recentcustomer' => $recentcustomer,
									'existingcustomer' => $existingcustomer,
									'prjtypequery' => $prjtypequery,
									'montharray' => $montharray,
									'notice' => $notice,
									'estimate' => $estimate,
									'amtcount' => $amtcount,
									'sample' => $sample,
									'sample' => $sample,
									'sample' => $sample, // all sample will be remove later
									'sample' => $sample,
									'sample' => $sample,
									'request' => $request]);
	}
	function noticepopup(Request $request) {
		$notice = Estimation::fnGetOtherDetails($request);
		return view('Estimation.noticepopup',['notice' => $notice,
											'request' => $request]);
	}
	function branch_ajax(Request $request) {
		$customerid = $_REQUEST['customerid'];
		$branchquery = Estimation::fnGetBranchDetails($customerid);
		// print_r($branchquery);exit;
		$branchquery=json_encode($branchquery);
		echo $branchquery;
	}
	function addeditprocess(Request $request) {
	
		if($request->editflg=="edit" || $request->editflg=="viewedit") {
			$process = Estimation::fnUpdateEstimates($request);
		} else {
			$code = Estimation::fnGenerateEstimateID();
			$process = Estimation::fnInsertEstimates($request,$code);
			$getmaxid = Estimation::fetchmaxid($request);
			$request->editid=$getmaxid;
		}
		if($process) {
			if($request->editflg=="edit" || $request->editflg=="viewedit") {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
			$estimate_id = $request->editid;
			if($request->editflg=="edit") {
        		$selYear = $request->selYear;
				$selMonth = $request->selMonth;
			} else {
				$dat=explode("-", $request->quot_date);
				$selYear = $dat[0];
				$selMonth = $dat[1];
			}
			$sorting = $request->sorting;
			$lastsortvalue = $request->lastsortvalue;
			$lastordervalue = $request->lastordervalue;
			$ordervalue = $request->ordervalue;

			$filter = $request->filter;
			if($request->editflg=="edit") {
				$totalRec = $request->totalrecords;
				$currentRec = (!empty($request->currentRec)?$request->currentRec:"1");
			} else {
				$totalRec = "";
				$currentRec = "";
			}
			$plimit = $request->plimit;
			$page = $request->page;
			$mainmenu= $request->mainmenu;
			$time=date('YmdHis'); ?>

		<form name="frmedit" id="frmedit"  action="../Estimation/view?mainmenu=<?php echo $mainmenu;?>&time=<?php echo $time ?>" method="post">
			<input type = "hidden" id = "editid" name = "editid" value="<?php echo $estimate_id; ?>">
			<input type = "hidden" id = "selYear" name = "selYear" value="<?php echo $selYear; ?>">
			<input type = "hidden" id = "selMonth" name = "selMonth" value="<?php echo $selMonth; ?>">
			<input type = "hidden" id = "sorting" name = "sorting" value="<?php echo $sorting; ?>">
			<input type = "hidden" id = "lastsortvalue" name = "lastsortvalue" value="<?php echo $lastsortvalue; ?>">
			<input type = "hidden" id = "lastordervalue" name = "lastordervalue" value="<?php echo $lastordervalue; ?>">
			<input type = "hidden" id = "ordervalue" name = "ordervalue" value="<?php echo $ordervalue; ?>">
			<input type = "hidden" id = "filter" name = "filter" value="<?php echo $filter; ?>">
			<input type = "hidden" id = "totalrecords" name = "totalrecords" value="<?php echo $totalRec; ?>">
			<input type = "hidden" id = "currentRec" name = "currentRec" value="<?php echo $currentRec; ?>">
			<input type = "hidden" id = "plimit" name = "plimit" value="<?php echo $plimit; ?>">
			<input type = "hidden" id = "page" name = "page" value="<?php echo $page; ?>">
		</form>
		<script type="text/javascript">
			document.forms['frmedit'].submit();
		</script>
<?php
	}
	//pdf download start
	function newpdf(Request $request) {
			$totalval = 0;
			$id = $request->estimate_id;
			$query = Estimation::fnGetEstiamteDetailsPDFDownload($id);
			$estamountetails = Estimation::fnGetEstimateAmountDetails($id);
			$estamount_array = array();
			$set_estamount_array = array();
			if(!empty($estamountetails)) {
				$set_estamount_array[0]['id'] = $query[0]->id;
				$set_estamount_array[0]['quot_date'] =$query[0]->quot_date;
				$set_estamount_array[0]['trading_destination_selection'] = $query[0]->trading_destination_selection;
				$set_estamount_array[0]['tax'] = $query[0]->tax;
				$set_estamount_array[0]['user_id'] = $query[0]->user_id;
				$set_estamount_array[0]['company_name'] = $query[0]->company_name;
				$set_estamount_array[0]['pdf_flg'] = $query[0]->pdf_flg;
				$set_estamount_array[0]['special_ins1'] = $query[0]->special_ins1;
				$set_estamount_array[0]['special_ins2'] = $query[0]->special_ins2;
				$set_estamount_array[0]['special_ins3'] = $query[0]->special_ins3;
				$set_estamount_array[0]['special_ins4'] = $query[0]->special_ins4;
				$set_estamount_array[0]['special_ins5'] = $query[0]->special_ins5;
				$parent_array = array('work_specific','quantity','unit_price','amount','remarks');
				for($es=0; $es < count($estamountetails); $es++) {
					for ($pa=0; $pa < count($parent_array) ; $pa++) { 
						$estamount_array[$es][$pa] = $parent_array[$pa].($es+1);
					}
				}
			foreach ($estamountetails as $key => $value) {
					for ($amt=0; $amt < count($parent_array); $amt++) { 
						$get_value = strtolower($parent_array[$amt]);
						$set_estamount_array[0][$estamount_array[$key][$amt]] = $value->$get_value;
					}
					$totalval = $totalval+str_replace(',', '', $value->amount);
				}	
				$set_estamount_array[0]['totalval'] = number_format($totalval);
				$set_estamount_array[0] = (object)$set_estamount_array[0];
				$query = $set_estamount_array; 
			}else {
            for($i=1;$i<=15;$i++) { 
               $work_specificarr="work_specific".$i;
                $quantityarr="quantity".$i;
                $unit_pricearr="unit_price".$i;
                $amountarr="amount".$i;
                $remarksarr="remarks".$i;
                if(!empty($query)) {
                    $query[0]->$work_specificarr="";
                    $query[0]->$quantityarr="";
                    $query[0]->$unit_pricearr="";
                    $query[0]->$amountarr="";
                    $query[0]->$remarksarr="";
                    $query[0]->totalval=0;
                }
            }
           }
			$get_data = Estimation::fnGetEstimateUserDataPDF($id);
			$invoice_qry = Estimation::fnGetInvoice($id);
			$bankid="";
			$branchid="";
			$type="";
			$dispval = 0;
			if($invoice_qry) {
				$bankid=$invoice_qry[0]->bankid;
				$branchid=$invoice_qry[0]->bankbranchid;
				$a_query = Estimation::fnGetAccount($bankid,$branchid);
				if (!empty($a_query)) {
					if ($a_query[0]->Type == 1) {
						$type = "Saving";
					} else if ($a_query[0]->Type == 2) {
						$type = "Other";
					} else {
						$type = $a_query[0]->Type;
					}
				}
			}
			$bran_query = Estimation::fnGetBranchName($bankid,$branchid);
			$bank_query = Estimation::fnGetBankName($bankid);

			//this qry need mb connection
			$customer_detail = Estimation::fnGetCustomerDetailsView($query[0]->trading_destination_selection); 

			// $get_customer_data = mysql_fetch_assoc(mysql_query($get_customer_detail, $conn));
			
			$execute_tax = Estimation::fnGetTaxDetails($query[0]->quot_date);
			$grandtotal = "";
			if (!empty($query[0]->totalval)) {
				if ($query[0]->tax != 2) {
					$totroundval = preg_replace("/,/", "", $query[0]->totalval);
					$dispval = (($totroundval * intval($execute_tax[0]->Tax))/100);
					$grandtotal = $totroundval + $dispval;
				} else {
					$totroundval = preg_replace("/,/", "", $query[0]->totalval);
					$dispval = 0;
					$grandtotal = $totroundval + $dispval;
				}
			}
			if($grandtotal =="") {
				$grandtotal = '0';
			}

			$pdf = new FPDI();
			$x_value="";
			$y_value="";
   			$pdf->AddMBFont( 'MS-Mincho', 'SJIS' );
			$pageCount = $pdf->setSourceFile("resources/assets/uploadandtemplates/templates/invoicepdf.pdf");
			//$pageCount = 1;
			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
			// import a page
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
			$pdf->Cell(20, 5, mb_convert_encoding("ご見積金額", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
			$pdf->SetFont( 'MS-Mincho' ,'B',20);
			if($pageNo == 2) {
		        $note = "御見積書(控)";
		        $pdf->SetXY(90, 21 );        
		        $pdf->Write(10, iconv('UTF-8', 'SJIS', $note));
		    } else {
		        $note = "御見積書";
		        $pdf->SetXY(90, 21 );        
		        $pdf->Write(10, iconv('UTF-8', 'SJIS', $note));
		    }
		//     $display = "株式会社 Microbit 
		// 〒532-0011
		// 大阪市淀川区西中島５丁目６－３
		// チサンマンション第２新大阪３０５号

		// Tel:06-6305-1251,Fax:06-6305-1250";       
			$pdf->SetFont( 'MS-Mincho' ,'B',9);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetXY(18, 86);
			$pdf->Cell(73, 0.8, "", 0, 1, 'L', true);
			$pdf->SetXY(148, 20);
			$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
			$pdf->SetXY(192, 20);
			$pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
			$pdf->SetXY(120.2, 45);
			$pdf->Image("resources/assets/images/address.png", 120, 35, 70, 55, 'PNG' );
			//$pdf->MultiCell(65, 4,mb_convert_encoding($display, 'SJIS', 'UTF-8'), 0,'L', 0);
			// CHANGED BY BABU
			$pdf->SetXY(150, 28);
			$pdf->Cell(20, 6.1, "", 0, 0, 'L', true);
			$pdf->SetXY(154.5, 28);
			$pdf->SetFont( 'MS-Mincho' ,'B',9);       
			$pdf->Cell(20, 6, mb_convert_encoding("見積番号：", 'SJIS', 'UTF-8'), 0, 1, 'L', true);

			$pdf->SetXY(172, 29 );
			$pdf->SetFont( 'MS-Mincho' ,'',9);       
			$pdf->Write(4, iconv('UTF-8', 'SJIS', $get_data[0]->user_id ));

			$pdf->SetXY(153, 20 );
			$pdf->Cell(20, 6, "", 0, 1, 'L', true);
			$pdf->SetXY(172, 15.5 );
			$pdf->Cell(20, 6, "", 0, 1, 'L', true);

			// CHANGED BY BABU
		   /* $pdf->SetXY(146, 14 );
			$pdf->Cell(20, 8, "", 0, 1, 'L', true);
			$pdf->SetFont( 'MS-Mincho' ,'B',12);
			$pdf->SetXY(153.5, 14 );
			$pdf->Cell(15, 8, mb_convert_encoding("発行日：", 'SJIS', 'UTF-8'), 0, 1, 'L', true);*/
			$pdf->SetFont( 'MS-Mincho' ,'B',10);      
			$pdf->SetXY(170, 15.2 );
			$pdf->Write(6, $query[0]->quot_date);       
			$pdf->SetXY(19, 37 );       
			$pdf->Write(6, mb_convert_encoding($query[0]->company_name."  御中", 'SJIS', 'UTF-8'));

			// CHANGED BY BABU
			$pdf->SetXY(19, 41.6);
			$pdf->Cell(60, 8, "", 0, 1, 'L', true);
			$pdf->Line(19, 43, 100, 43); // 20mm from each edge

			$pdf->SetFont( 'MS-Mincho' ,'B',16);
			$amount="¥ ".number_format($grandtotal)."-";
			$pdf->SetXY(42.5, 79);
			$pdf->Cell(40.4, 6.1, iconv('UTF-8', 'SJIS', $amount), 0, 0, 'R');
			
				// $pdf->SetFont( 'MS-Mincho' ,'B', '');
				// $pdf->SetXY(42, 213 );
				// $pdf->Write(6, mb_convert_encoding((isset($bank_query[0]->BankName)) ? $bank_query[0]->BankName : '', 'SJIS', 'UTF-8'));
				  //       $pdf->SetXY(42, 218 );
				  //       $pdf->Write(6, mb_convert_encoding($type, 'SJIS', 'UTF-8'));
				  //       $pdf->SetXY(55, 218 );
				  //       $pdf->Write(6, mb_convert_encoding((isset($a_query[0]->AccNo)) ? $a_query[0]->AccNo : '', 'SJIS', 'UTF-8'));
				  //       $pdf->SetXY(42, 223 );
				  //       $pdf->Write(6, mb_convert_encoding((isset($bran_query[0]->BranchName)) ? $bran_query[0]->BranchName : '', 'SJIS', 'UTF-8'));
				  //       $pdf->SetXY(42, 228);
				  //       $pdf->Write(6, mb_convert_encoding((isset($a_query[0]->FirstName)) ? $a_query[0]->FirstName : '', 'SJIS', 'UTF-8'));


	        // its hide because of remove bank view in estimation
	       
	        	$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetFillColor(175, 175, 175);
		        $pdf->SetXY(14.5, 90.5);         
		        $pdf->Cell(79.8, 6.4, iconv('UTF-8', 'SJIS', "                      品名"), 'LTRB', 1, 'L', true);
		        $pdf->SetXY(94.2, 90.5);         
		        $pdf->Cell(14.6, 6.4, iconv('UTF-8', 'SJIS', "  数量"), 'LRTB', 0, 'L', true);
		        $pdf->SetXY(108.7, 90.5);            
		        $pdf->Cell(28.4, 6.4, iconv('UTF-8', 'SJIS', "      単価"), 'LRTB', 0, 'L', true);
		        $pdf->SetXY(137.1, 90.5);            
		        $pdf->Cell(30.3, 6.4, iconv('UTF-8', 'SJIS', "      金額"), 'LRTB', 0, 'L', true);
		        $pdf->SetXY(167.3, 90.5);            
		        $pdf->Cell(29, 6.4, iconv('UTF-8', 'SJIS', "      摘要"), 'LRTB', 0, 'L', true);
		        //edited by rajaguru
				$y=0;
				$yy=0;
				$y_axis=96.8;
				$count=count($estamountetails);
				if ($count<19) {
					$count=19;
				}
				for ($i =1; $i <=$count; $i++) {
					$work_specificarr="work_specific".$i;
					$quantityarr="quantity".$i;
					$unit_pricearr="unit_price".$i;
					$amountarr="amount".$i;
					$remarksarr="remarks".$i;
					if(!isset($query[0]->$work_specificarr)) {
						$query[0]->$work_specificarr="";
						$query[0]->$quantityarr="";
						$query[0]->$unit_pricearr="";
						$query[0]->$amountarr="";
						$query[0]->$remarksarr="";
					}
					$pdf->SetFont( 'MS-Mincho' ,'B', '10');
					if(($i%2)==0){
					$pdf->SetFillColor(220, 220, 220);
					}
					else{
						$pdf->SetFillColor(255, 255, 255);
					}
					//edited by Rajaguru
					
					if ($y+96.8>=$pdf->h - 20)
					{
					    $pdf->AddPage();
					    $y=0;
					    $yy=1;
					    $y_axis=10;
					}

					if($i>=19){
						$pdf->SetXY(14.5, $y_axis+$y);
						$pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);
						$pdf->SetXY(19.5, $y_axis+$y);
						$pdf->Cell(74.8, 6.0301, "", 'RBT', 0, 'L', true);
						$pdf->SetXY(19.5, $y_axis+$y);
						$work_specificarr="work_specific".$i;
						$pdf->drawTextBox(mb_convert_encoding($query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
						$quantityarr="quantity".$i;
						if(!empty($query[0]->$quantityarr)) {
							$dotOccur = strpos($query[0]->$quantityarr, ".");
							if( $query[0]->$quantityarr != "" ){
								if ($dotOccur) {
									$query[0]->$quantityarr = $query[0]->$quantityarr;
								} else {
									$query[0]->$quantityarr = $query[0]->$quantityarr.".0";
								}
							}
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LBRT', 0, 'C', true);
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->drawTextBox($query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
						} else {
							$pdf->SetXY(94.2, $y_axis+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LBRT', 0, 'C', true);
						}
						$pdf->SetTextColor(0,0,0);
						$unit_pricearr="unit_price".$i;
						if (!empty($query[0]->$unit_pricearr)) {
							$pdf->SetXY(108.7, $y_axis+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LRBT', 0, 'R', true);
							$pdf->SetXY(108.7, $y_axis+$y);
							if ($query[0]->$unit_pricearr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(108.7, $y_axis+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LBRT', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$amountarr="amount".$i;
						if (!empty($query[0]->$amountarr)) {
							$pdf->SetXY(137.1, $y_axis+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LBRT', 0, 'R', true);
							$pdf->SetXY(137.1, $y_axis+$y);
							if ($query[0]->$amountarr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(137.1, $y_axis+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LRBT', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$pdf->SetXY(167.3, $y_axis+$y);
						$pdf->Cell(29, 6.0301, "", 'LBRT', 0, 'L', true);
						$pdf->SetXY(167.3, $y_axis+$y);
						$remarksarr="remarks".$i;
						$pdf->drawTextBox(iconv('UTF-8', 'SJIS', $query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
					}
					 else {
						$pdf->SetXY(14.5, 96.8+$y);
						$pdf->Cell(5, 6.0301, "", 'LT', 0, 'L', true);
						$pdf->SetXY(19.5, 96.8+$y);
						$pdf->Cell(74.8, 6.0301, "", 'RT', 0, 'L', true);
						$pdf->SetXY(19.5, 96.8+$y);
						$work_specificarr="work_specific".$i;
						$pdf->drawTextBox(mb_convert_encoding($query[0]->$work_specificarr, 'SJIS', 'UTF-8'), 74.8, 6.0301, 'L', 'B', 0);
						$quantityarr="quantity".$i;
						if(!empty($query[0]->$quantityarr)) {
							$dotOccur = strpos($query[0]->$quantityarr, ".");
							if( $query[0]->$quantityarr != "" ){
								if ($dotOccur) {
									$query[0]->$quantityarr = $query[0]->$quantityarr;
								} else {
									$query[0]->$quantityarr = $query[0]->$quantityarr.".0";
								}
							}
							$pdf->SetXY(94.2, 96.8+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LRT', 0, 'C', true);
							$pdf->SetXY(94.2, 96.8+$y);
							$pdf->drawTextBox($query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
						} else {
							$pdf->SetXY(94.2, 96.8+$y);
							$pdf->Cell(14.6, 6.0301, "", 'LRT', 0, 'C', true);
						}
						$pdf->SetTextColor(0,0,0);
						$unit_pricearr="unit_price".$i;
						if (!empty($query[0]->$unit_pricearr)) {
							$pdf->SetXY(108.7, 96.8+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LRT', 0, 'R', true);
							$pdf->SetXY(108.7, 96.8+$y);
							if ($query[0]->$unit_pricearr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(108.7, 96.8+$y);
							$pdf->Cell(28.4, 6.0301, "", 'LRT', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$amountarr="amount".$i;
						if (!empty($query[0]->$amountarr)) {
							$pdf->SetXY(137.1, 96.8+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LRT', 0, 'R', true);
							$pdf->SetXY(137.1, 96.8+$y);
							if ($query[0]->$amountarr < 0) {
								$pdf->SetTextColor(255,0,0);
							}
							$pdf->drawTextBox($query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);
						} else {
							$pdf->SetXY(137.1, 96.8+$y);
							$pdf->Cell(30.3, 6.0301, "", 'LRT', 0, 'R', true);
						}
						$pdf->SetTextColor(0,0,0);
						$pdf->SetXY(167.3, 96.8+$y);
						$pdf->Cell(29, 6.0301, "", 'LRT', 0, 'L', true);
						$pdf->SetXY(167.3, 96.8+$y);
						$remarksarr="remarks".$i;
						$pdf->drawTextBox(iconv('UTF-8', 'SJIS', $query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);
					}
					$y=$y+6.065;
				}
				//edited by Rajaguru
				if ($yy>0) {
					$ynew=$y+10;
					$yn=$y+16;
					$yyn=$y+22;
				}
				
				else{
					$ynew=$y+96.8;
					$yn=$y+102.8;
					$yyn=$y+108.8;
				}

			$pdf->SetFont( 'MS-Mincho' ,'B',11);
			$pdf->SetXY(137, $ynew);
			$pdf->Cell(30.3, 6, "", 1, 0, 'R');
			$pdf->SetXY(137, $ynew);
			$pdf->drawTextBox($query[0]->totalval, 30.3, 6.1, 'R', 'B', 0);
			$pdf->SetXY(137, $yn);
			$pdf->Cell(30.3, 6.1, "", 1, 0, 'R');
			$pdf->SetXY(137, $yn);
			$pdf->drawTextBox(number_format($dispval), 30.3, 6.1, 'R', 'B', 0);
			$pdf->SetXY(137, $yyn);
			$pdf->Cell(30.3, 6.1, "", 1, 0, 'R');
			$pdf->SetXY(137, $yyn);
			$pdf->drawTextBox(number_format($grandtotal), 30.3, 6.1, 'R', 'B', 0);
			$pdf->SetFont( 'MS-Mincho' ,'B',9);
			$pdf->SetFillColor(175, 175, 175);
			if ($query[0]->tax == 1) {
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税      " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計       " ), 28.4, 6.1, 'R', 'B', 0);
	        } else if ($query[0]->tax == 2) {
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"非課税      " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計       " ), 28.4, 6.1, 'R', 'B', 0);
	        } else {
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $ynew);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税      " ), 28.4, 6.1, 'R', 'B', 0);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->Cell(28.4, 6.1, "", 'LBTR', 0, 'C', true);
	            $pdf->SetXY(108.7, $yyn);
	            $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計       " ), 28.4, 6.1, 'R', 'B', 0);
	        }
	      	
	        
	        $arrval = array();
			for ($i = 1; $i <= 5; $i++) {
				$special_insarr="special_ins".$i;
				if($query[0]->$special_insarr != "") {
					array_push($arrval, $query[0]->$special_insarr);
				}
			}
			$x=0;
			for ($rccnt=0; $rccnt < count($arrval); $rccnt++) {
			}

			if(count($arrval) != 0) {
				//edited by Rajaguru
				$yax=$ynew+26.5;
					if (($yy==0 && $yax+20>=$pdf->h - 20 && count($arrval)==5) || ($yy==0 && ($yax+14)>=$pdf->h - 20 && count($arrval)==4) || ($yy==0 && ($yax+8)>=$pdf->h - 20 && count($arrval)<=3)  )
					{
						$pdf->AddPage();
						$yax=10;
					}
				$y=0;
				$exvalue=$rccnt-1;
				$pdf->SetFont( 'MS-Mincho' ,'B',11);
				$pdf->SetXY(22.5 ,$yax );
				$pdf->Write(6, iconv('UTF-8', 'SJIS',  "【特記事項】"));
				$tilde = '~';//～,〜
				$japtilde = '〜';
				$japreptilde = "～";
				for($i = 0; $i<count($arrval); $i++) {
					$pdf->SetFont( 'MS-Mincho' ,'',10);
					$no=($rccnt-$exvalue).")";
					$pdf->SetXY(22.5 ,($yax+6)+$y );
				 	$pdf->Write(6, iconv('UTF-8', 'SJIS', $no ));
					$pdf->SetFont( 'MS-Mincho' ,'B',10);
					$pdf->SetXY(26.5 ,($yax+5)+$y );
					$dispStr = $arrval[$i];
					$dispStr = mb_convert_encoding($dispStr, 'SJIS', 'UTF-8'); 
					$pdf->Write(9, $dispStr);
					$y=$y+5.5;
					$exvalue=$exvalue-1;
				}
			}
		}
		   //download secction
			$path= "resources/assets/uploadandtemplates/upload/Estimation";
				$id=$query[0]->user_id;
			if(!is_dir($path)){
				mkdir($path, true);
			}
			chmod($path, 0777); 
			$files = glob($path . '/' . $id . '*.pdf');
			if ( $files !== false )
			{
				$filecount = count( $files );
			}
			$pdf_name = "";
			if($query[0]->pdf_flg == 0){
				if($filecount != 0){
					$pdf_name=$id."_".str_pad($filecount , 2, '0', STR_PAD_LEFT);
					$pdfnamelist=$pdf_name;
				} else {
					$pdf_name=$id;
					$pdfnamelist=$pdf_name;
				}
			} else {
				$pdf_name=$id;
				$pdfnamelist=$pdf_name;
			}
			$done = Estimation::pdfflgset($query[0]->user_id,$pdfnamelist);
			
			if($query[0]->pdf_flg == 0){
				$filepath = "resources/assets/uploadandtemplates/upload/Estimation/".$pdf_name.".pdf";
			} else {
				$filepath = "resources/assets/uploadandtemplates/upload/Estimation/".$pdf_name.".pdf";
			}
		$pdf->Output($filepath, 'F');
		chmod($filepath, 0777);
		$pdfname = $pdf_name;
		if($request->frmsendmail=="1") {
			return $pdfname;
		}

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
	//pdf download end
	//send mail start
	function sendmail(Request $request) {
		if($request->estimate_id=="" && $request->mailstatusid=="") {
			return $this->index($request);
		}
		$id = $request->estimate_id;
		$cust_id = $request->cust_id;
		$CustomerDetails = Estimation::fnGetCustomerDetailsView($id);
		$viewdonemail="";
		$viewdoneall="";
		$viewdone=0;
		$signature = array();
		$atcnt=1;
		$getallcustomer =Estimation::getallcustomer($cust_id);
		// print_r($cust_id);exit();
		$tomailfrmmailStatus = Estimation::fngettomailfrmmailStatus($cust_id);
		if(isset($tomailfrmmailStatus[0]) &&  $tomailfrmmailStatus[0]!="") {
			$viewdonemail = 1;
		}
		if(isset($getallcustomer[0]->Counts)) {
			$viewdoneall = $getallcustomer[0]->Counts;
		}
		if($viewdonemail == 1 || $viewdoneall !=0) {
			$viewdone = 1;
		}
		$selYear = date('Y');
		$selMonth = date('m');
		if($request->selYear!="") {
			$selYear = $request->selYear;
		}
		if($request->selMonth!="") {
			$selMonth = $request->selMonth;
		}

		$datemonth=$selYear."-".$selMonth;
		if($request->sendmailfrom=="Estimation") {
			$mailId="MAIL0001"; // for Estimation Content
			$tblname="dev_estimate_registration";
			$nxtmonth = ltrim(date('m', strtotime('+1 month')),'0');
			$subject=$nxtmonth.'月分の見積書';		
		} else {
			$mailId="MAIL0002"; // for Invoice Content
			$tblname="dev_invoices_registration";
			$nxtmonth = ltrim(date('m', strtotime('-1 month')),'0');
			$subject=$nxtmonth.'月分の請求書';
			}
		$CompanyName = Estimation::getCompanyName($id,$tblname);
		$getpdf = Estimation::fnGetallestimation($cust_id,$datemonth,$tblname);
		$mailIdpwd="MAIL0003"; // for Pdf Password Content
		$maildata = Estimation::fngetMailContent($mailId);
		$pwddata = Estimation::fngetMailContent($mailIdpwd);
		// random Password generation Process...
     	$pdfpassword=substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', 8)), 0, 8);
		$prevsendmail = Estimation::fngetprevsendmail($cust_id);
		if($request->mailstatusid!="") {
			$prevsendmail = Estimation::fngetmailstatus($request->mailstatusid);
			$CompanyName = Estimation::fnGetCustomerDetailsView($prevsendmail[0]->companyId);
			$CompanyName[0]->company_name = $CompanyName[0]->customer_name;
			$request->cust_id = $CompanyName[0]->id;
			$maildata = $prevsendmail;
			$atcnt = $prevsendmail[0]->attachCount;
			$pdfpassword = $prevsendmail[0]->pdfPassword;
			$getpdf = Estimation::fnGetallestimation($cust_id,$datemonth,$tblname);
			$est_getpdf = Estimation::fnGetEstimateAmountDetails($cust_id);		
			$set_amount_array = array();			
				if(!empty($est_getpdf)) {
					$set_amount_array[0]['id'] = $getpdf[0]->id;
					$set_amount_array[0]['user_id'] = $est_getpdf[0]->estimate_id;
					$getpdf = $set_amount_array;
				}		
		}
		// Getting Signature of Login User
		$signature = Estimation::getSignature($request);
		return view('Estimation.sendmail',[
									'CompanyName' => $CompanyName,
									'CustomerDetails' => $CustomerDetails,
									'getpdf' => $getpdf,
									'datemonth' => $datemonth,
									'pdfpassword' => $pdfpassword,
									'maildata' => $maildata,
									'pwddata' => $pwddata,
									'subject' => $subject,
									'prevsendmail' => $prevsendmail,
									'viewdone' => $viewdone,
									'atcnt' => $atcnt,
									'signature' => $signature,
									'request' => $request]);
	}
	function view(Request $request) {
		if($request->editid=="") {
			return $this->index($request);
		}
		$sample="";
		$id = $request->editid;
		
		if (empty($request->sorting)) {
			$srt = "user_id";
		}
		if ($request->sorting == "user_id") {
			$srt = "user_id";
		} else if ($request->sorting == "quot_date") {
			$srt = "quot_date";
		} else if ($request->sorting == "company_name") {
			$srt = "company_name";
		} else {
			$srt = "user_id";
		}
		if (empty($request->ordervalue)) {
			$odr = "desc";
			$request->ordervalue = "desc";
		}
		if (!empty($request->ordervalue)) {
			$odr = $request->ordervalue;
			$request->ordervalue = $request->ordervalue;
		}
		if ($request->ordervalue == "1") {
			$odr = "asc";
			$request->sortstyle = "sort_asc";
		} elseif ($request->ordervalue == 0 && $request->sorting != "") {
			$odr = "desc";
		}
		$date_month=$request->selYear."-".$request->selMonth;
		$startdate = $request->startdate;
		$enddate = $request->enddate;
		$fil=$request->filter;
		$singlesearchtxt=$request->singlesearch;
		$TotEstquery = Estimation::fnGetestimateTotVal($request,$date_month,$srt,$odr);
		$get_view=array();
			$x = 1;
		foreach ($TotEstquery as $key => $value) {
			$get_view[$x]["id"] = $value->id;
			$x++;
		}
		$search_flg=0;
		$order=$odr;
		if(!empty($request->totalrecords)){
			$totalRec=$request->totalrecords;
			$currentRec=$request->currentRec;
		}else{
			$totalRec=count($get_view);
				if($order == "desc"){
					$currentRec=1;
				}else{
					$currentRec=count($get_view);
				}
		}
		$curTime = date('YmdHis');
		$sort=$srt;
		$get_estimate_project_type=array();
		$get_data = Estimation::fnGetEstimateUserData($request);
		//print_r($get_data); exit();
		if(isset($get_data[0]->project_type_selection) && $get_data[0]->project_type_selection!="") {
			$get_estimate_project_type = Estimation::fnGetEstimateprojecttype($get_data[0]->project_type_selection);

		}
		$get_customer_data = Estimation::fnGetCustomerDetailsView($get_data[0]->trading_destination_selection);
		$execute_tax = Estimation::fnGetTaxDetails($get_data[0]->quot_date);
		$i_query = Estimation::fnGetInvoice($id);
		$bankid="";
		$branchid="";
		$type="";
		if(isset($i_query[0]->bankid)) {
			$bankid=$i_query[0]->bankid;
		}
		if(isset($i_query[0]->bankbranchid)) {
			$branchid=$i_query[0]->bankbranchid;
		}
		$a_query = Estimation::fnGetAccount($bankid,$branchid);
		if(isset($a_query[0]->Type)) {
			if ($a_query[0]->Type == 1) {
				$type = "Saving";
			} 
			else if ($a_query[0]->Type == 2) {
				$type = "Other";
			} 
			else {
				$type = $a_query[0]->Type;
			}
		}
		$bran_query = Estimation::fnGetBranchName($bankid,$branchid);
		$bank_query = Estimation::fnGetBankName($bankid);
		$grandtotal = "0";

		if (!empty($get_data[0]->totalval)) {
			if ($get_data[0]->tax != 2) {
				$totroundval = preg_replace("/,/", "", $get_data[0]->totalval);
				$dispval = (($totroundval * intval($execute_tax[0]->Tax))/100);
				$grandtotal = $totroundval + $dispval;
			} 
			else {
				$totroundval = preg_replace("/,/", "", $get_data[0]->totalval);
				$dispval = 0;
				$grandtotal = $totroundval + $dispval;
			}
		} else {
			$dispval = 0;
			$grandtotal = '0';
		}
		// print_r($dispval);exit();
		$j = 1;
		for ($i=1; $i <= 5 ; $i++) { 
			$special_insfromdb = "special_ins".$i;
			if ($get_data[0]->$special_insfromdb != "") {
				$special_rearrabge = "special_ins".$j;
				$get_data[0]->$special_rearrabge = $get_data[0]->$special_insfromdb;
				if ($i != $j) {
					$get_data[0]->$special_insfromdb = "";
				}
				$j++;
			}
		}
		// if(empty($get_data[0]->amount)){
  //           $get_data[0]->totalval =0;
  //           $dispval =0;
  //           $grandtotal =0;
  //       }
        $dat = array();
        foreach ($get_data as $key => $value) {
        	$dat[]= $value->amount;
        } 

        $amtcount = count($dat);
        //print_r($get_data); exit();
		return view('Estimation.view',[
									'get_data' => $get_data,
									'get_estimate_project_type' => $get_estimate_project_type,
									'get_customer_data' => $get_customer_data,
									'execute_tax' => $execute_tax,
									'i_query' => $i_query,
									'a_query' => $a_query,
									'bran_query' => $bran_query,
									'bank_query' => $bank_query,
									'dispval' => $dispval,
									'grandtotal' => $grandtotal,
									'sample' => $sample,
									'sample' => $sample,
									'sample' => $sample,
									'order' => $order,
									'totalRec' => $totalRec,
									'currentRec' => $currentRec,
									'sort' => $sort,
									'curTime' => $curTime,
									'search_flg' => $search_flg,
									'date_month' => $date_month,
									'get_view' => $get_view,
									'amtcount' => $amtcount,
									//'totroundval' => $totroundval,
									'request' => $request]);

	}
	public static function exceldownloadprocess(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/estimation.xls';
      	$tempname = "Estimation";
      	$excel_name=$tempname;
      	Excel::load($template_name, function($objPHPExcel) use($request) {
      	$request->plimit = 1000;
      	$type="";
      	$g_query = Estimation::fnGetEstiamteDetailsPDF($request);
      	$get_estimate_query = Estimation::fnGetEstimateUserData($request);
		$get_customer_detail = Estimation::fnGetCustomerDetailsView($g_query[0]->trading_destination_selection);
		$invoice_detail = Estimation::fnGetInvoice($g_query[0]->id);
		$bankid=(isset($invoice_detail[0]->bankid)?$invoice_detail[0]->bankid:"");
		$branchid=(isset($invoice_detail[0]->bankbranchid)?$invoice_detail[0]->bankbranchid:"");
		$acc_details = Estimation::fnGetAccount($bankid,$branchid);
		// if ($acc_details[0]->Type == 1) {
		// 	$type = "Saving";
		// } else if ($acc_details[0]->Type == 2) {
		// 	$type = "Other";
		// } else {
			$type = (isset($acc_details[0]->Type)?$acc_details[0]->Type:"");
		// }
		$branch_details = Estimation::fnGetBranchName($bankid,$branchid);
		$bank_details = Estimation::fnGetBankName($bankid);
		$gettaxquery = Estimation::fnGetTaxDetails($g_query[0]->quot_date);
		$grandtotal = "";
		$dispval=0;
		if (!empty($g_query[0]->totalval) && !empty($g_query[0]->est_primary_key_id)) {
			if ($g_query[0]->tax != 2) {
				$totroundval = preg_replace("/,/", "", $g_query[0]->totalval);
				$dispval = (($totroundval * intval($gettaxquery[0]->Tax))/100);
				$grandtotal = $totroundval + $dispval;
			} else {
				$totroundval = preg_replace("/,/", "", $g_query[0]->totalval);
				$dispval = 0;
				$grandtotal = $totroundval + $dispval;
			}
		}
		if($grandtotal =="") {
			$grandtotal = '0';
			$dispval = 0;
			$g_query[0]->totalval= '0';
		}
		$objPHPExcel->setActiveSheetIndex();
  		$objPHPExcel->setActiveSheetIndex(0);  //set first sheet as active

  		$objPHPExcel->getActiveSheet()->setCellValue('AD1', $g_query[0]->quot_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C7', $g_query[0]->company_name."  御中");	
		$objPHPExcel->getActiveSheet()->getStyle('H15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->setCellValue('H16','¥ '. number_format($grandtotal).'-');
		$objPHPExcel->getActiveSheet()->setCellValue('Y40', $g_query[0]->totalval);
		$objPHPExcel->getActiveSheet()->setCellValue('Y41', number_format($dispval));
		$objPHPExcel->getActiveSheet()->setCellValue('Y43', number_format($grandtotal));
		//client excel
		$objPHPExcel->getActiveSheet()->setCellValue('AD52', $g_query[0]->quot_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C58', $g_query[0]->company_name."  御中");
		$objPHPExcel->getActiveSheet()->getStyle('H66')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->setCellValue('H67','¥ '. number_format($grandtotal).'-');
		$objPHPExcel->getActiveSheet()->setCellValue('Y91', $g_query[0]->totalval);
		$objPHPExcel->getActiveSheet()->setCellValue('Y92', number_format($dispval));
		$objPHPExcel->getActiveSheet()->setCellValue('Y94', number_format($grandtotal));

		if ($g_query[0]->tax == 1) {
			$objPHPExcel->getActiveSheet()->setCellValue('U42', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y42', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U43', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y43', "");
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('U93', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y93', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U94', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y94', "");
		} 
		if ($g_query[0]->tax == 2) {
			$objPHPExcel->getActiveSheet()->setCellValue('U41', "非課税");
			$objPHPExcel->getActiveSheet()->setCellValue('Y41', "0");
			$objPHPExcel->getActiveSheet()->setCellValue('U42', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y42', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U43', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y43', "");
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('U92', "非課税");
			$objPHPExcel->getActiveSheet()->setCellValue('Y92', "0");
			$objPHPExcel->getActiveSheet()->setCellValue('U93', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y93', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U94', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y94', "");
		}
		$na=$get_customer_detail[0]->customer_name."\r\n".$get_customer_detail[0]->customer_address."\r\n".$get_customer_detail[0]->customer_contact_no;
		// $objPHPExcel->getActiveSheet()->setCellValue('H40', $bank_details[0]->BankName);
		// $objPHPExcel->getActiveSheet()->setCellValue('H41', $type);
		// $objPHPExcel->getActiveSheet()->setCellValue('H42', $branch_details[0]->BranchName);
		// $objPHPExcel->getActiveSheet()->setCellValue('H43', $acc_details[0]->FirstName);
		$objPHPExcel->getActiveSheet()->getStyle('K41')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->mergeCells('K41:O41');
		// $objPHPExcel->getActiveSheet()->setCellValue('K41', $acc_details[0]->AccNo);
		$objPHPExcel->getActiveSheet()->setCellValue('AD3', $get_estimate_query[0]->user_id);
		//client excel
		// $objPHPExcel->getActiveSheet()->setCellValue('H91', $bank_details[0]->BankName);
		// $objPHPExcel->getActiveSheet()->setCellValue('H92', $type);
		// $objPHPExcel->getActiveSheet()->setCellValue('H93', $branch_details[0]->BranchName);
		// $objPHPExcel->getActiveSheet()->setCellValue('H94', $acc_details[0]->FirstName);
		$objPHPExcel->getActiveSheet()->getStyle('K92')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->mergeCells('K92:O92');
		// $objPHPExcel->getActiveSheet()->setCellValue('K92', $acc_details[0]->AccNo);
		$objPHPExcel->getActiveSheet()->setCellValue('AD54', $get_estimate_query[0]->user_id);
		$cellval = 20;
		$clientcellval = 71;
		$i=1;
		foreach ($g_query as $key => $value) {
			$workloop = $value->work_specific;
			$quantityloop = $value->quantity; 
			$unit_priceloop = $value->unit_price;
			$amountloop =$value->amount; 
			$remarksloop = $value->remarks;
			$objPHPExcel->getActiveSheet()->setCellValue('C' . ($cellval + $i),  $workloop );
			$dotOccur = strpos($quantityloop, ".");
	        if( trim($quantityloop) != "" ){
	            if ($dotOccur) {
	                $quantityValue = "\0" . $quantityloop;
	            } else {
	                $quantityValue = "\0" . $quantityloop . ".0";
	            }
	        } else {
	        	$quantityValue = "";
	        }
			$objPHPExcel->getActiveSheet()->setCellValue('R' . ($cellval + $i), $quantityValue);
			if (!empty($unit_priceloop)) {
				if ($unit_priceloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('U' . ($cellval + $i), $unit_priceloop)->getStyle('U' . ($cellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('U' . ($cellval + $i),$unit_priceloop);
			}
			if (!empty($amountloop)) {
				if ($amountloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($cellval + $i),$amountloop)->getStyle('Y' . ($cellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($cellval + $i), $amountloop);
			}
			$objPHPExcel->getActiveSheet()->setCellValue('AD' . ($cellval + $i), $remarksloop);
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('C' . ($clientcellval + $i),  $workloop);
			$objPHPExcel->getActiveSheet()->setCellValue('R' . ($clientcellval + $i), $quantityValue);
			if (!empty($unit_priceloop)) {
				if ($unit_priceloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('U' . ($clientcellval + $i),$unit_priceloop)->getStyle('U' . ($clientcellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('U' . ($clientcellval + $i),$unit_priceloop);
			}
			if (!empty($amountloop)) {
				if ($amountloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($clientcellval + $i),$amountloop)->getStyle('Y' . ($clientcellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($clientcellval + $i),$amountloop);
			}
			$objPHPExcel->getActiveSheet()->setCellValue('AD' . ($clientcellval + $i), $remarksloop);
			$i++;
		}
		$cellval = 45;
		$clientcellval = 96;
		$rccnt = 0;
		$arrval = array();
		for ($i = 1; $i <= 5; $i++) {
			$special_ins = "special_ins".$i;
			if(isset($g_query[0]->$special_ins) && $g_query[0]->$special_ins != "") {
				array_push($arrval, $g_query[0]->$special_ins);
			}
		}
		for ($rccnt=0; $rccnt < count($arrval); $rccnt++) {
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), $arrval[$rccnt]);
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), $arrval[$rccnt]);
		}
		if(count($arrval) == 1) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
			$objPHPExcel->getActiveSheet()->mergeCells('E47:AB47');
			$objPHPExcel->getActiveSheet()->unmergeCells('E47:AB47');
			$objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			$objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
			$objPHPExcel->getActiveSheet()->mergeCells('E98:AB98');
			$objPHPExcel->getActiveSheet()->unmergeCells('E98:AB98');
			$objPHPExcel->getActiveSheet()->mergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->unmergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->mergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->unmergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->mergeCells('E101:AB101');
			$objPHPExcel->getActiveSheet()->unmergeCells('E101:AB101');
		} else if(count($arrval) == 2) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			$objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->unmergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->mergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->unmergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->mergeCells('E101:AB101');
			$objPHPExcel->getActiveSheet()->unmergeCells('E101:AB101');
		} else if(count($arrval) == 3) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');	
			$objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			$objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->unmergeCells('E100:AB100');	
			$objPHPExcel->getActiveSheet()->mergeCells('E101:AB101');
			$objPHPExcel->getActiveSheet()->unmergeCells('E101:AB101');
		} else if(count($arrval) == 4) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			$objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt), $arrval[$rccnt-1]);
			$objPHPExcel->getActiveSheet()->mergeCells('E101:AB101');
			$objPHPExcel->getActiveSheet()->unmergeCells('E101:AB101');
		} else if(count($arrval) == 5) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-4), $rccnt-4 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), (isset($arrval[$rccnt-1])?$arrval[$rccnt-1]:""));
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-4), $rccnt-4 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt), (isset($arrval[$rccnt-1])?$arrval[$rccnt-1]:""));
		} else {
			$objPHPExcel->getActiveSheet()->setCellValue('D45', "");
			$objPHPExcel->getActiveSheet()->mergeCells('E46:AB46');
			$objPHPExcel->getActiveSheet()->unmergeCells('E46:AB46');
			$objPHPExcel->getActiveSheet()->mergeCells('E47:AB47');
			$objPHPExcel->getActiveSheet()->unmergeCells('E47:AB47');
			$objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			$objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			$objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			$objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			//client excel
			$objPHPExcel->getActiveSheet()->setCellValue('D96', "");
			$objPHPExcel->getActiveSheet()->mergeCells('E97:AB97');
			$objPHPExcel->getActiveSheet()->unmergeCells('E97:AB97');
			$objPHPExcel->getActiveSheet()->mergeCells('E98:AB98');
			$objPHPExcel->getActiveSheet()->unmergeCells('E98:AB98');
			$objPHPExcel->getActiveSheet()->mergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->unmergeCells('E99:AB99');
			$objPHPExcel->getActiveSheet()->mergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->unmergeCells('E100:AB100');
			$objPHPExcel->getActiveSheet()->mergeCells('E101:AB101');
			$objPHPExcel->getActiveSheet()->unmergeCells('E101:AB101');
		}
		$objPHPExcel->getActiveSheet()->getStyle("AD20:AD39")->applyFromArray(
			    array(
			        'borders' => array(
			            'right' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN
			            )
			        )
			    )
			);
		$objPHPExcel->getActiveSheet()->getStyle("AD71:AD90")->applyFromArray(
			    array(
			        'borders' => array(
			            'right' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN
			            )
			        )
			    )
			);
		$objPHPExcel->getActiveSheet()->getStyle('W13')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('W14')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('W14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// $objPHPExcel->getActiveSheet()->getStyle('W64')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// $objPHPExcel->getActiveSheet()->getStyle('W65')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// $objPHPExcel->getActiveSheet()->getStyle('W65')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C67')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H67')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->setTitle($g_query[0]->user_id);
		$objPHPExcel->getActiveSheet(0)->setSelectedCells('B1');
	      	$flpath='.xls';
	      	header('Content-Type: application/vnd.ms-excel');
	      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      	header('Cache-Control: max-age=0');
      	})->setFilename($excel_name)->download('xls');
	}

	public static function newexceldownloadprocess(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/estimatenew.xls';
      	$tempname = "Estimation";
      	$excel_name=$tempname;
      	Excel::load($template_name, function($objPHPExcel) use($request) {
      	$request->plimit = 1000;
      	$type="";
      	$g_query = Estimation::fnGetEstiamteDetailsPDF($request);
      	$get_estimate_query = Estimation::fnGetEstimateUserData($request);
		$get_customer_detail = Estimation::fnGetCustomerDetailsView($g_query[0]->trading_destination_selection);
		$invoice_detail = Estimation::fnGetInvoice($g_query[0]->id);
		$bankid=(isset($invoice_detail[0]->bankid)?$invoice_detail[0]->bankid:"");
		$branchid=(isset($invoice_detail[0]->bankbranchid)?$invoice_detail[0]->bankbranchid:"");
		$acc_details = Estimation::fnGetAccount($bankid,$branchid);
		// if ($acc_details[0]->Type == 1) {
		// 	$type = "Saving";
		// } else if ($acc_details[0]->Type == 2) {
		// 	$type = "Other";
		// } else {
			$type = (isset($acc_details[0]->Type)?$acc_details[0]->Type:"");
		// }
		$branch_details = Estimation::fnGetBranchName($bankid,$branchid);
		$bank_details = Estimation::fnGetBankName($bankid);
		$gettaxquery = Estimation::fnGetTaxDetails($g_query[0]->quot_date);
		$grandtotal = "";
		$dispval=0;
		if (!empty($g_query[0]->totalval) && !empty($g_query[0]->est_primary_key_id)) {
			if ($g_query[0]->tax != 2) {
				$totroundval = preg_replace("/,/", "", $g_query[0]->totalval);
				$dispval = (($totroundval * intval($gettaxquery[0]->Tax))/100);
				$grandtotal = $totroundval + $dispval;
			} else {
				$totroundval = preg_replace("/,/", "", $g_query[0]->totalval);
				$dispval = 0;
				$grandtotal = $totroundval + $dispval;
			}
		}
		if($grandtotal =="") {
			$grandtotal = '0';
			$dispval = 0;
			$g_query[0]->totalval= '0';
		}
		$objPHPExcel->setActiveSheetIndex();
  		$objPHPExcel->setActiveSheetIndex(0);  //set first sheet as active

  		$objPHPExcel->getActiveSheet()->setCellValue('AD1', $g_query[0]->quot_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C7', $g_query[0]->company_name."  御中");	
		$objPHPExcel->getActiveSheet()->getStyle('H15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->setCellValue('H16','¥ '. number_format($grandtotal).'-');
		$objPHPExcel->getActiveSheet()->setCellValue('Y40', $g_query[0]->totalval);
		$objPHPExcel->getActiveSheet()->setCellValue('Y41', number_format($dispval));
		$objPHPExcel->getActiveSheet()->setCellValue('Y43', number_format($grandtotal));
		if ($g_query[0]->tax == 1) {
			$objPHPExcel->getActiveSheet()->setCellValue('U42', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y42', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U43', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y43', "");
		} 
		if ($g_query[0]->tax == 2) {
			$objPHPExcel->getActiveSheet()->setCellValue('U41', "非課税");
			$objPHPExcel->getActiveSheet()->setCellValue('Y41', "0");
			$objPHPExcel->getActiveSheet()->setCellValue('U42', "税込合計");
			$objPHPExcel->getActiveSheet()->setCellValue('Y42', number_format($grandtotal));
			$objPHPExcel->getActiveSheet()->setCellValue('U43', "");
			$objPHPExcel->getActiveSheet()->setCellValue('Y43', "");
		}
		$na=$get_customer_detail[0]->customer_name."\r\n".$get_customer_detail[0]->customer_address."\r\n".$get_customer_detail[0]->customer_contact_no;
		$objPHPExcel->getActiveSheet()->getStyle('K41')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->mergeCells('K41:O41');
		$objPHPExcel->getActiveSheet()->setCellValue('AD3', $get_estimate_query[0]->user_id);
		$cellval = 20;
		$i=1;
		$setf = 40;
		$colorarray=array(
						        'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => 'C0C0C0')
						        )
						    );
		$colorarrywh= array(
						        'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => 'FFFFFF')
						        )
						    );
		foreach ($g_query as $key => $value) {
			$workloop = $value->work_specific;
			$quantityloop = $value->quantity; 
			$unit_priceloop = $value->unit_price;
			$amountloop =$value->amount; 
			$remarksloop = $value->remarks;
			if ($i>19) {
				
					$objPHPExcel->getActiveSheet()->insertNewRowBefore(($cellval + $i), 1);
					if ($i%2==0) {
						$objPHPExcel->getActiveSheet()->getStyle('B'.$setf.':AD'.$setf)->applyFromArray(
						$colorarray
						);
					} else {
						$objPHPExcel->getActiveSheet()->getStyle('B'.$setf.':AD'.$setf)->applyFromArray(
						   $colorarrywh
						);
					}
					$setf++;
					$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(24.75);

					// $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('B39'.':AI39'),'B' . ($cellval + $i) . ':AI' . ($cellval + $i));
					if ($cellval + $i == '40') {
						$objPHPExcel->getActiveSheet()->getStyle('B' . ('39') . ':AI' . ('39'))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);

					}

					$objPHPExcel->getActiveSheet()->getStyle('B' . ($cellval + $i) . ':AI' . ($cellval + $i))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					$objPHPExcel->getActiveSheet()->mergeCells('C' . ($cellval + $i) . ':P' . ($cellval + $i));
					$objPHPExcel->getActiveSheet()->mergeCells('R' . ($cellval + $i) . ':T' . ($cellval + $i));
					$objPHPExcel->getActiveSheet()->mergeCells('U' . ($cellval + $i) . ':X' . ($cellval + $i));
					$objPHPExcel->getActiveSheet()->mergeCells('Y' . ($cellval + $i) . ':AC' . ($cellval + $i));
					$objPHPExcel->getActiveSheet()->mergeCells('AD' . ($cellval + $i) . ':AI' . ($cellval + $i));



			}
			$objPHPExcel->getActiveSheet()->setCellValue('C' . ($cellval + $i),  $workloop );
			$dotOccur = strpos($quantityloop, ".");
	        if( trim($quantityloop) != "" ){
	            if ($dotOccur) {
	                $quantityValue = "\0" . $quantityloop;
	            } else {
	                $quantityValue = "\0" . $quantityloop . ".0";
	            }
	        } else {
	        	$quantityValue = "";
	        }
			$objPHPExcel->getActiveSheet()->setCellValue('R' . ($cellval + $i), $quantityValue);
			if (!empty($unit_priceloop)) {
				if ($unit_priceloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('U' . ($cellval + $i), $unit_priceloop)->getStyle('U' . ($cellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('U' . ($cellval + $i),$unit_priceloop);
			}
			if (!empty($amountloop)) {
				if ($amountloop < 0) {
					$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($cellval + $i),$amountloop)->getStyle('Y' . ($cellval + $i))->getFont()->getColor()->setRGB('FF0000');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('Y' . ($cellval + $i), $amountloop);
			}
			$objPHPExcel->getActiveSheet()->setCellValue('AD' . ($cellval + $i), $remarksloop);



			$i++;
			// print_r($value);
		}
		if (($cellval + $i)>39) {
			$objPHPExcel->getActiveSheet()->getStyle('B' . ($cellval + ($i-1)) . ':AI' . ($cellval + ($i-1)))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}
		if ($i>19) {
			$cellval = $i+25;
		}
		else{
			$cellval = 45;
		}
		$rccnt = 0;
		$arrval = array();
		for ($k = 1; $k <= 5; $k++) {
			$special_ins = "special_ins".$k;
			if(isset($g_query[0]->$special_ins) && $g_query[0]->$special_ins != "") {
				array_push($arrval, $g_query[0]->$special_ins);
			}
		}
		for ($rccnt=0; $rccnt < count($arrval); $rccnt++) {
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), $arrval[$rccnt]);
		}
		if(count($arrval) == 1) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
			//Datas is not showed in excel due to merge cells (20/02/19)
			// $objPHPExcel->getActiveSheet()->mergeCells('E47:AB47');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E47:AB47');
			// $objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			
		} else if(count($arrval) == 2) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			//Datas is not showed in excel due to merge cells
			// $objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			
		} else if(count($arrval) == 3) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			//Datas is not showed in excel due to merge cells
			// $objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');	
			// $objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			
		} else if(count($arrval) == 4) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), $arrval[$rccnt-1]);
			//Datas is not showed in excel due to merge cells
			// $objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
			
		} else if(count($arrval) == 5) {
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-4), $rccnt-4 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt), (isset($arrval[$rccnt-1])?$arrval[$rccnt-1]:""));
			
		} else {
			$objPHPExcel->getActiveSheet()->setCellValue('D45', "");
			//Datas is not showed in excel due to merge cells
			// $objPHPExcel->getActiveSheet()->mergeCells('E46:AB46');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E46:AB46');
			// $objPHPExcel->getActiveSheet()->mergeCells('E47:AB47');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E47:AB47');
			// $objPHPExcel->getActiveSheet()->mergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E48:AB48');
			// $objPHPExcel->getActiveSheet()->mergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E49:AB49');
			// $objPHPExcel->getActiveSheet()->mergeCells('E50:AB50');
			// $objPHPExcel->getActiveSheet()->unmergeCells('E50:AB50');
		
		}
		if ($i>19) {
				$cellval=20;
				$objPHPExcel->getActiveSheet()->getStyle('AD21'  . ':AD' . ($cellval + ($i-1)))->applyFromArray(
					array(
						'borders' => array(
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
						)
					)
					);

				
		}
		$objPHPExcel->getActiveSheet()->getStyle("AD20:AD39")->applyFromArray(
			    array(
			        'borders' => array(
			            'right' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN
			            )
			        )
			    )
			);

		
		$objPHPExcel->getActiveSheet()->getStyle('W13')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('W14')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('W14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->setTitle('御見積書');
		$sheet1 = $objPHPExcel->getActiveSheet()->copy();
		$sheet2 = clone $sheet1;
		$sheet_title = '御見積書(控)';
		$sheet2->setTitle($sheet_title);
		$objPHPExcel->addSheet($sheet2);
		$sheet2->setCellValue('B2', "御見積書(控)");
		unset($sheet2);
		unset($sheet1);
		$objPHPExcel->getActiveSheet(0)->setSelectedCells('B1');
	      	$flpath='.xls';
	      	header('Content-Type: application/vnd.ms-excel');
	      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      	header('Cache-Control: max-age=0');
      	})->setFilename($excel_name)->download('xls');
	}
	
	function browsepopup(Request $request) {
		$sample = "";
		$customercnt = "";
		$customerview = "";
		$branchcnt = "";
		$branchview = "";
		$inchargecnt = "";
		$inchargeview = "";
		$onlyoncew=0;
		$onlyonce=0;
		$cust_id = $_REQUEST['cust_id'];
		$_REQUEST['anothertxt'] = str_replace("$","&lt;",$_REQUEST['anothertxt']);
		$_REQUEST['anothertxt'] = str_replace("-","&gt;",$_REQUEST['anothertxt']);
		$anothertxt = $_REQUEST['anothertxt'];
		$anothertxtarr =array();
		$anothertxtarr = explode(",", $anothertxt);
		$customer_id = "";
		$getallcustomer =Estimation::getallcustomer($cust_id);
		$customercnt = count($getallcustomer);
		$i=0;
		foreach ($getallcustomer as $key => $cusval) {
			$customerview[$i]['id']=$cusval->id;
			$customerview[$i]['customer_id']=$cusval->customer_id;
			$customerview[$i]['customer_name']=$cusval->customer_name;
			$customerview[$i]['Counts']=$cusval->Counts;
			$getallincharge =Estimation::getallincharge($customerview[$i]['customer_id']);
			$inchargecnt[$i]=count($getallincharge);
			$j=0;
			
			foreach ($getallincharge as $key => $inchargeval) {
				$inchargeview[$i][$j]['id']=$inchargeval->id;
				// print_r($inchargeview[$i][$j]['id']);print_r("<br>");exit();
				$inchargeview[$i][$j]['inchargeName']=$inchargeval->incharge_name;
				$inchargeview[$i][$j]['mobile']=$inchargeval->incharge_contact_no;
				if (in_array($inchargeval->incharge_email_id, $anothertxtarr)) {
					$inchargeview[$i][$j]['mailId']=$inchargeval->incharge_email_id;
					$inchargeview[$i][$j]['disabled']=1;
				} else {
					$inchargeview[$i][$j]['mailId']=$inchargeval->incharge_email_id;
					$inchargeview[$i][$j]['disabled']=0;
				}
				$j++;
			}
			$i++;
			$customer_id = $customerview[0]['customer_id'];
		}
		$getothermailidfinal =array();
		$tomailfrmmailStatus =array();
		$getothermailid =array();
		$tomailfrmmailStatus = Estimation::fngettomailfrmmailStatus($cust_id);
		$getothermailid = Estimation::fngetothermailid($tomailfrmmailStatus,$customer_id);
		$getothermailidfinal = array_values(array_diff($tomailfrmmailStatus, $getothermailid));
		$getothermailidsame = array_intersect($getothermailidfinal, $anothertxtarr);
		if($_REQUEST['type'] == "cc") {
			$view = "Estimation.ccbrowsepopup";
		} else {
			$view = "Estimation.browsepopup";
		}
		return view($view,['customercnt' => $customercnt,
											'customerview' => $customerview,
											'branchcnt' => $branchcnt,
											'branchview' => $branchview,
											'inchargecnt' => $inchargecnt,
											'inchargeview' => $inchargeview,
											'onlyoncew' => $onlyoncew,
											'onlyonce' => $onlyonce,
											'getothermailidfinal' => $getothermailidfinal,
											'getothermailidsame' => $getothermailidsame,
											'request' => $request]);
	}
	function coverpopup(Request $request) {
		$request->custid = $_REQUEST['custid'];
		return view('Estimation.coverpopup',['request' => $request]);
	}
	public static function coverdownloadprocess(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/coveringletter.xls';
      	$tempname = "CoveringLetter";
      	$excel_name=$tempname;
      	Excel::load($template_name, function($objPHPExcel) use($request) {
		$objPHPExcel->setActiveSheetIndex();
  		$objPHPExcel->setActiveSheetIndex(0);  //set first sheet as active
      	$custdata = Estimation::getcustdetails($request);
      	if(Session::get('languageval')=="en") {
      		$designation = $custdata[0]->DesignationNM;
      	} else {
      		$designation = $custdata[0]->DesignationNMJP;
      	}
  		$objPHPExcel->getActiveSheet()->setCellValue('B7', $custdata[0]->incharge_name);
  		$objPHPExcel->getActiveSheet()->setCellValue('B8', $designation);
  		$objPHPExcel->getActiveSheet()->setCellValue('B9', $custdata[0]->customer_name);
  		$customer_contact_no="";
  		$customer_fax_no="";
  		if($custdata[0]->customer_contact_no!="") {
  			$customer_contact_no = substr($custdata[0]->customer_contact_no, 0,2)." (".substr($custdata[0]->customer_contact_no, 2,4).") ".substr($custdata[0]->customer_contact_no, 6);
  		}
  		if($custdata[0]->customer_fax_no!="") {
  			$customer_fax_no = substr($custdata[0]->customer_fax_no, 0,2)." (".substr($custdata[0]->customer_fax_no, 2,4).") ".substr($custdata[0]->customer_fax_no, 6);
  		}
  		$objPHPExcel->getActiveSheet()->setCellValue('B11', $customer_contact_no);
  		$objPHPExcel->getActiveSheet()->setCellValue('B13', $customer_fax_no);
		$objPHPExcel->getActiveSheet()->mergeCells('F9:K9');
		$year =date('Y');
		$month =date('m');
		$day =date('d');
		$finaldate = $year."年".$month."月".$day."日";
  		$objPHPExcel->getActiveSheet()->setCellValue('F9', $finaldate);
  		$roundval = "①";
  		if($request->estcnt !="" && $request->estcnt !=0) {
			$objPHPExcel->getActiveSheet()->setCellValue('A28', $roundval);
			$objPHPExcel->getActiveSheet()->setCellValue('F28', $request->estcnt);
  			$roundval = "②";
  		}
  		if($request->invcnt !="" && $request->invcnt !=0) {
			$objPHPExcel->getActiveSheet()->setCellValue('A29', $roundval);
			$objPHPExcel->getActiveSheet()->setCellValue('F29', $request->invcnt);
		}
		if(($request->estcnt =="" || $request->estcnt == 0) && ($request->invcnt =="" || $request->invcnt ==0)) {
			$objPHPExcel->getActiveSheet()->removeRow(28,2);
  		} else {
			if($request->estcnt =="" || $request->estcnt == 0) {
				$objPHPExcel->getActiveSheet()->removeRow(28);
	  		}
	  		if($request->invcnt =="" || $request->invcnt == 0) {
				$objPHPExcel->getActiveSheet()->removeRow(29);
			}
		}
		$objPHPExcel->setActiveSheetIndex(1);
		// second sheet contents
		if($custdata[0]->customer_address!="") {
			$customer_contact_no_second ="";
	  		if($custdata[0]->customer_contact_no!="") {
	  			$customer_contact_no_second = substr($custdata[0]->customer_contact_no, 0,2)."(".substr($custdata[0]->customer_contact_no, 2,4).")".substr($custdata[0]->customer_contact_no, 6);
	  		}
			$cusaddress = $custdata[0]->customer_address."\nTEL ： ".$customer_contact_no_second;
	  		$objPHPExcel->getActiveSheet()->setCellValue('F4', $cusaddress);
	  		$objPHPExcel->getActiveSheet()->getStyle('F4')->getAlignment()->setWrapText(true);
	  	}
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setTitle("Covering Letter");
		$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
      	$flpath='.xls';
      	header('Content-Type: application/vnd.ms-excel');
      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
      	header('Cache-Control: max-age=0');
      	})->setFilename($excel_name)->download('xls');
	}
}