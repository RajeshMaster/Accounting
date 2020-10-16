<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Estimation;
use App\Model\Invoice;
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

class InvoiceController extends Controller {
    function index(Request $request) {
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
            $request->filter=1;
            $fil=1;
            $disabledall="disabled fb";
        } else if($request->filter==1) {
            $fil=1;
            $disabledall="disabled fb";
        } elseif($request->filter==2) {
            $fil=2;
            $disabledcreating="disabled fb";
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
        $invoicesortarray = [$request->invoicesort=>$request->invoicesort,
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
        $estimateno =trim($request->estimateno);
        $companyname="";
        if ( $request->companyname != "" ) {
            $companyname = trim($request->companyname);
            $request->companynameClick = "";
            
        } else if ($request->companynameClick != "" ) {
            $companyname = trim($request->companynameClick);
            $request->companyname = "";
            $disabledall="";
        }
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        if($request->projecttype=="a") {
            $projecttype="";
        } else {
            $projecttype = $request->projecttype;
        }
        
        if($request->protype2=="0"){
            $taxSearch="";
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
        }else{
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
        $TotEstquery = Invoice::fnGetinvoiceTotalValue($request,$taxSearch,$date_month,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate,$fil);
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
                $B=end($aryEnd);
                $cou=count($dbprevious);
                for($z=$B; $z<$cou;$z++) {
                    unset($dbprevious[$z]);
                }
            }
        }
        $inv=array();
        $i=0;
        $ckmail=array();
        foreach ($TotEstquery as $key => $value) {
            $inv[$i]['id'] = $value->id;
            $getallsendmail = Estimation::fnGetallsendmails($value->user_id,$date_month);
            if($getallsendmail) {
                $ckmail[]=$getallsendmail[0]->sendFlg;
            } else {
                $ckmail[]=0;
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
        $grandtotal =0;
        $balance_style ="";
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
            // print_r($TotEstquery); exit();
        // End of Copy Flag Display process
        return view('Invoice.index',[
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
public static function ajaxsubsubject(Request $request) {
    $getsunsubject=Invoice::fnfetchsubsubject($request);
    $getsunsubject=json_encode($getsunsubject);
    echo $getsunsubject; exit;
}
public static function noticepopup(Request $request) {
        $notice = Invoice::fnGetOtherDetails($request);
      return view('Invoice.noticepopup',['notice' => $notice,
                                        'request' => $request]);
    }
public static function addeditprocess(Request $request){
    //print_r($request->totval); exit();
    $totalinvvalue=0;
    for ($i=1; $i <=15 ; $i++) { // loop for common field
            $stat4='amount'.$i;
            $totalinvvalue+=$request->$stat4;
    }       
    // print_r($totalinvvalue); exit();
    if ($request->regflg==1 || $request->regflg==2) {
        $code = Invoice::fnGenerateInvoiceID($request);
        $insert = Invoice::fnInsertInvoice($request,$code,$totalinvvalue);
        if($insert) {
            Session::flash('success', trans('messages.lbl_insertsucss') );
            Session::flash('type', 'alert-success'); 
        } else {
            Session::flash('type', 'Inserted Unsucessfully!');
            Session::flash('type', 'alert-danger'); 
        }
            $getlastid=Invoice::fnfetchlastid($request);
            $getqdate=Invoice::fnfetchqdate($request,$getlastid);
                $qtdate=array();
                $inyear="";
                $inmonth="";
                $retedit=2;
                $totalRec = "";
                $currentRec = "";
                $qtdate=explode('-',$getqdate[0]->quot_date);
                $inyear=$qtdate[0];
                $inmonth=$qtdate[1];
                $request->cerid = $getlastid;
                $estimate_id = $getlastid;
        } else if (empty($request->regflg)) {
            $getbranchid=Invoice::fnfetchbranchnumber($request);
            //$code = Invoice::fnGenerateInvoiceID($request);
            $update = Invoice::fnUpdateInvoicedetails($request,$totalinvvalue,$getbranchid[0]->id);
            $getqdate=array();
            $retedit=1;
            $estimate_id = $request->invid;
            $totalRec = $request->totalRec;
            $currentRec = (!empty($request->currentRec)?$request->currentRec:"1");
            if($update) {
                Session::flash('success', trans('messages.lbl_updatesucss') );
                Session::flash('type', 'alert-success'); 
            } else {
                Session::flash('type', 'Updated Unsucessfully!'); 
                Session::flash('type', 'alert-danger'); 
            }       
        }           
                if ($request->regflg==1 || $request->regflg==2) {
                    $selYear = $inyear;
                    $selMonth = $inmonth;
                } else {
                    $selYear = $request->selYear;
                    $selMonth = $request->selMonth;                 
                }
                $sortOrder = (!empty($request->sortOrder)?$request->sortOrder:"DESC");
                $sortOptn = (!empty($request->sortOptn)?$request->sortOptn:"user_id");
                $filter = $request->filter;
                $plimit = $request->plimit;
                $page = $request->page;
                $mainmenu= $request->mainmenu;
                $time=date('YmdHis');
    ?>
        <form name="frmedit" id="frmedit"  action="../Invoice/specification?mainmenu=<?php echo $mainmenu;?>&time=<?php echo $time ?>" method="post">
            <input type = "hidden" id = "qdate" name = "qdate" value="<?php print_r((!empty($getqdate[0]->quot_date)?$getqdate[0]->quot_date:$getqdate)); ?>">
            <input type = "hidden" id = "invoiceid" name = "invoiceid" value="<?php echo $estimate_id; ?>">
            <input type = "hidden" id = "estimate_id" name = "estimate_id" value="<?php echo $estimate_id; ?>">
            <input type = "hidden" id = "selYear" name = "selYear" value="<?php echo $selYear; ?>">
            <input type = "hidden" id = "selMonth" name = "selMonth" value="<?php echo $selMonth; ?>">
            <input type = "hidden" id = "sortOrder" name = "sortOrder" value="<?php echo $sortOrder; ?>">
            <input type = "hidden" id = "sortOptn" name = "sortOptn" value="<?php echo $sortOptn; ?>">
            <input type = "hidden" id = "filter" name = "filter" value="<?php echo $filter; ?>">
            <input type = "hidden" id = "totalrecords" name = "totalrecords" value="<?php echo $totalRec; ?>">
            <input type = "hidden" id = "currentRec" name = "currentRec" value="<?php echo $currentRec; ?>">
            <input type = "hidden" id = "plimit" name = "plimit" value="<?php echo $plimit; ?>">
            <input type = "hidden" id = "page" name = "page" value="<?php echo $page; ?>">
            <input type = "hidden" id = "retedit" name = "retedit" value="<?php echo $retedit; ?>">
        </form>
        <script type="text/javascript">
            document.forms['frmedit'].submit();
        </script>
<?php }
public static function ajaxgetbankdetails(Request $request) {
    $getsunsubject=Invoice::fngetbranchdetails($request);
    $getsunsubject=json_encode($getsunsubject);
    echo $getsunsubject; exit;      
}
function specification(Request $request){
        if($request->selYear=="") {
            return $this->index($request);
        }
        $companyname="";
        if ( $request->companyname != "" ) {
            $companyname = trim($request->companyname);
            $request->companynameClick = "";
            
        } else if ($request->companynameClick != "" ) {
            $companyname = trim($request->companynameClick);
            $request->companyname = "";
            $search_flg = 1;
        }
        if($request->taxSearch=="0"){
            $taxSearch="";
        }else{
            $taxSearch = $request->taxSearch;
        }
        $projecttype=1;
        $date_month=$request->selYear."-".$request->selMonth;
        if (!empty($date_month)) {
            $date_month=$date_month;
        } else {
            $date_month=substr($request->qdate, 0, 7);
        }
        $search_flg=0;
        $estimateno =trim($request->estimateno);
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $fil=$request->filter;
        $singlesearchtxt=$request->singlesearch;
        $TotEstquery = Invoice::fnGetinvoiceTotVal($request,$date_month);
        $order=$request->sortOrder;
        $get_view=array();
            $x = 1;
        foreach ($TotEstquery as $key => $value) {
            $get_view[$x]["id"] = $value->id;
            $x++;
        }
        if(!empty($request->totalrecords)){
            $totalRec=$request->totalrecords;
            $currentRec=$request->currentRec;
        }else{
            $totalRec=count($get_view);
                if($order == "DESC"){
                    $currentRec=1;
                }else{
                    $currentRec=count($get_view);
                }
        }
        $curTime = date('YmdHis');
        $sort=$request->sortOptn;
        $da_mon_inv_view=$request->selYear."-".$request->selMonth;
        $invoicedata = Invoice::fnGetinvoiceUserData($request);
        $estimatedata = Invoice::fnGetEstimateinvoiceData($request,$invoicedata[0]->estimate_id);
        $get_customer_detail = Invoice::fnGetCustomerDetail($invoicedata[0]->trading_destination_selection);
        $bankid=$invoicedata[0]->bankid;
        $branchid=$invoicedata[0]->bankbranchid;
        $accountnumb=$invoicedata[0]->acc_no;
        $account_details=Invoice::fnGetBankAccountdetails($bankid,$branchid,$accountnumb);
            $type="";
        if (!empty($account_details)) {
            if ($account_details[0]->Type == 1) {
                $type = "普通";
            } else if ($account_details[0]->Type == 2) {
                $type = "Other";
            }
        } else {
            $type="";
        }
        $invprojecttype=$invoicedata[0]->project_type_selection;
        $getprojecttype=Invoice::fnfetchprojecttype($invprojecttype);
        $getinvtaxdetails=Invoice::fnfetchinvtaxdetails($invoicedata[0]->quot_date);
        // print_r($invoicedata); echo "<br>"; echo "<br>"; echo "<br>";
        // print_r($estimatedata); exit;
        $grandtotal=0;
        if ($invoicedata[0]->tax != 2) {
            $totroundval =  preg_replace("/,/", "", $invoicedata[0]->totalval);
            $dispval = (($totroundval * intval((isset($getinvtaxdetails[0]->Tax)?$getinvtaxdetails[0]->Tax:0)))/100);
            $grandtotal = $totroundval + $dispval;
        } else {
            $totroundval =  preg_replace("/,/", "", $invoicedata[0]->totalval);
            $dispval = 0;
            $grandtotal = $totroundval + $dispval;
        }
        $j = 1;
        for ($i=1; $i <= 5 ; $i++) { 
            $special_insfromdb = "special_ins".$i;
            if ($invoicedata[0]->$special_insfromdb != "") {
                $special_rearrabge = "special_ins".$j;
                $invoicedata[0]->$special_rearrabge = $invoicedata[0]->$special_insfromdb;
                if ($i != $j) {
                    $invoicedata[0]->$special_insfromdb = "";
                }
                $j++;
            }
        }
        // if(empty($invoicedata[0]->amount)){
        //     $invoicedata[0]->totalval =0;
        //     $dispval =0;
        //     $grandtotal =0;
        // }
        $dat = array();
        foreach ($invoicedata as $key => $value) {
            $dat[]= $value->amount;
        } 
        $amtcount = count($dat);

    return view('Invoice.specification',['invoicedata' => $invoicedata,
                                         'estimatedata' => $estimatedata,
                                         'order' => $order,
                                         'totalRec' => $totalRec,
                                         'currentRec' => $currentRec,
                                         'sort' => $sort,
                                         'type' => $type,
                                         'curTime' => $curTime,
                                         'search_flg' => $search_flg,
                                         'date_month' => $date_month,
                                         'get_view' => $get_view,
                                         'dispval' => $dispval,
                                         'grandtotal' => $grandtotal,
                                         'get_customer_detail' => $get_customer_detail,
                                         'getprojecttype' => $getprojecttype,
                                         'account_details' => $account_details,
                                         'amtcount' => $amtcount,
                                         'request' => $request]);
}
public static function addeditinv(Request $request) {

    // For Customer
    if (!isset($request->invoiceid)) {
        return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
    }
    $sample="";
    $selectedBank = "";
    $selectval = "";
    $selectjsonArray = array();
    $recentcustomer = Invoice::fnGetCustomerDetails($request);
    $totalcustomer = Invoice::fnexistingcustomer($request);
    $existingcustomer=array_diff($totalcustomer,$recentcustomer);
    $prjtypequery = Estimation::fnGetProjectType($request);
    $notice = Estimation::fnGetOtherDetails($request);
    if (isset($request->estflg)) {
        $regflag=1;
        $invoicedata = Invoice::fnGetEstimateUserData($request);
        $invoicedataforloop = Invoice::fnGetEstimateUserDataForLoop($request);
    } elseif (isset($request->copyflg) && $request->copyflg!="") {
        $regflag=2;
        $invoicedata = Invoice::fnGetinvoiceUserDataADD($request);
        $invoicedataforloop = Invoice::fnGetinvoiceUserDataForLoop($request);

    } else {
        $regflag="";
        $invoicedata = Invoice::fnGetinvoiceUserDataADD($request);
        $invoicedataforloop = Invoice::fnGetinvoiceUserDataForLoop($request);

    }

    $invoicedata = array_merge($invoicedata,$invoicedataforloop);
//print_r($invoicedata); exit();
    if ($regflag != 1) {
        $selectedBank =  Invoice::fnGetSelectedDetails($invoicedata);
        if (!empty($selectedBank)) {
            $selectval = $selectedBank[0]->id;
        }
    }

    for ($i=1; $i <=31 ; $i++) {
        $day[]=$i;
    }

    $montharray = array("1"=>trans('messages.lbl_presentmonth'),
                                    "2"=>trans('messages.lbl_nextmonth'),
                                    "3"=>trans('messages.lbl_nextnextmonth'),
                                    "4"=>trans('messages.lbl_Others'));
    // $get_bank_query = Invoice::fnGetBankDetails($request);
    $get_bank_query = Invoice::fnGetBankBranchDet($request,$invoicedata);
    $request->mainid = $invoicedata[0]->customer_id;
    $selectboxArray = Invoice::fnfetchsubsubject($request);
    foreach ($selectboxArray as $key => $value) {
        $selectjsonArray[$value->id] = $value->branch_id;
    }
    // Last working day of Previous month calculation By kumaran.
    $newLastMthLstDay = "";
    $lastDayPreviousMth = new Carbon\Carbon('last day of last month');
    if ($lastDayPreviousMth->isWeekday()) {
        $newLastMthLstDay = $lastDayPreviousMth->format('Y-m-d');
    } else {
        if($lastDayPreviousMth->isSaturday()) {
            $newLastMthLstDay = $lastDayPreviousMth->subDays(1)->format('Y-m-d');
        } else if ($lastDayPreviousMth->isSunday()) {
            $newLastMthLstDay = $lastDayPreviousMth->subDays(2)->format('Y-m-d');
        }
    }
    // End of Calculating Last working day of Previous month
    // if(empty($invoicedataforloop[0]->amount)) {
    //     $invoicedata[0]->totalval=0;
    //     $grandtotal = 0;
    //     $dispval = 0;
    // }
    $dat = array();
        foreach ($invoicedataforloop as $key => $value) {
            $dat[]= $value->amount;
        } 
        $amtcount = count($dat);
    return view('Invoice.addedit',[ 'numbdays' => $day,
                                    'recentcustomer' => $recentcustomer,
                                    'existingcustomer' => $existingcustomer,
                                    'prjtypequery' => $prjtypequery,
                                    'montharray' => $montharray,
                                    'notice' => $notice,
                                    'regflag' => $regflag,
                                    'invoicedata' => $invoicedata,
                                    'get_bank_query' => $get_bank_query,
                                    'selectval' => $selectval,
                                    'lastMtnLastDay' => $newLastMthLstDay,
                                    'amtcount' => $amtcount,
                                    'selectjsonArray' => json_encode($selectjsonArray),
                                    'request' => $request]);
}
    public static function newpdf(Request $request) {
       $totalval = 0;
        $id = $request->invoice_id;
        $in_query = Invoice::fnGetEstiamteDetailsPDFDownload($id);
        $in_amount_query = Invoice::fnGetAmountDetails($id);
        $data_count=count($in_amount_query);
        // print_r($data_count); exit();         
        $amount_array = array();
        $set_amount_array = array();
        if (!empty($in_amount_query)) {
            $set_amount_array[0]['id'] = $in_query[0]->id;
            $set_amount_array[0]['estimate_id'] = $in_query[0]->estimate_id;
            $set_amount_array[0]['bankid'] = $in_query[0]->bankid;
            $set_amount_array[0]['bankbranchid'] = $in_query[0]->bankbranchid;
            $set_amount_array[0]['acc_no'] = $in_query[0]->acc_no;
            $set_amount_array[0]['quot_date'] =$in_query[0]->quot_date;
            $set_amount_array[0]['trading_destination_selection'] = $in_query[0]->trading_destination_selection;
            $set_amount_array[0]['tax'] = $in_query[0]->tax;
            $set_amount_array[0]['user_id'] = $in_query[0]->user_id;
            $set_amount_array[0]['company_name'] = $in_query[0]->company_name;
            $set_amount_array[0]['pdf_flg'] = $in_query[0]->pdf_flg;
            $set_amount_array[0]['special_ins1'] = $in_query[0]->special_ins1;
            $set_amount_array[0]['special_ins2'] = $in_query[0]->special_ins2;
            $set_amount_array[0]['special_ins3'] = $in_query[0]->special_ins3;
            $set_amount_array[0]['special_ins4'] = $in_query[0]->special_ins4;
            $set_amount_array[0]['special_ins5'] = $in_query[0]->special_ins5;
            $parent_array = array('work_specific', 'emp_ID', 'quantity', 'unit_price', 'amount', 'remarks');
            for ($am=0; $am < count($in_amount_query); $am++) { 
                for ($qu=0; $qu < count($parent_array); $qu++) { 
                    $amount_array[$am][$qu] = $parent_array[$qu].($am+1);
                }
            }
            foreach ($in_amount_query as $key => $value) {
                
                for ($st=0; $st < count($parent_array); $st++) { 
                    $get_value = strtolower($parent_array[$st]);
                    $set_amount_array[0][$amount_array[$key][$st]] = $value->$get_value;                 
                }
                    $totalval = $totalval + str_replace(',', '', $value->amount);
                }           
            $set_amount_array[0]['totalval'] = number_format($totalval);
            $set_amount_array[0] = (object)$set_amount_array[0];
            $in_query = $set_amount_array;
           }else
           {
            for($i=1;$i<=15;$i++) { 
               $work_specificarr="work_specific".$i;
                $quantityarr="quantity".$i;
                $unit_pricearr="unit_price".$i;
                $amountarr="amount".$i;
                $remarksarr="remarks".$i;
                if(!empty($in_query)) {
                    $in_query[0]->$work_specificarr="";
                    $in_query[0]->$quantityarr="";
                    $in_query[0]->$unit_pricearr="";
                    $in_query[0]->$amountarr="";
                    $in_query[0]->$remarksarr="";
                    $in_query[0]->totalval=0;
                }
            }
           }            
        $estimateid = $in_query[0]->estimate_id;     
        $e_query = Invoice::fnGetEstDetail($estimateid);                    
        $get_data = Invoice::fnGetEstimateUserDataPDF($id);        
        $get_customer_data = Invoice::fnGetCustomerDetailsView($in_query[0]->trading_destination_selection);     
        $type = "";    
        $bankid = "";    
        $branchid = "";    
        $acc_no = "";    
        $bankid=$in_query[0]->bankid;
        $branchid=$in_query[0]->bankbranchid; 
        $acc_no=$in_query[0]->acc_no;                   
        $a_query = Invoice::fnGetAccounts($bankid,$branchid,$acc_no);  
        if($a_query) {
            if ($a_query[0]->Type == 1) {        
                $type = "普通";   
            } else if ($a_query[0]->Type == 2) {     
                $type = "Other";    
            } else {        
                $type = $a_query[0]->Type;   
            }
        }    
        $bran_query = Invoice::fnGetBranchName($bankid,$branchid);
                
        $bank_query = Invoice::fnGetBankName($bankid);      
                
        $execute_tax = Invoice::fnGetTaxDetails($in_query[0]->quot_date);
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
        $pdf = new FPDI();
        $x_value="";
        $y_value="";
            $pdf->AddMBFont( 'MS-Mincho', 'SJIS' );
        $pageCount = $pdf->setSourceFile("resources/assets/uploadandtemplates/templates/invoicepdf.pdf");
        //$pageCount=1;
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
            $pdf->Cell(20, 5, mb_convert_encoding("ご請求金額", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
            $pdf->SetFont( 'MS-Mincho' ,'B',20);
            if($pageNo == 2) {
                $note = "請求書(控)";
                $pdf->SetXY(90, 21 );        
                $pdf->Write(10, iconv('UTF-8', 'SJIS', $note));
            } else {
                $note = "請求書";
                $pdf->SetXY(90, 21 );        
                $pdf->Write(10, iconv('UTF-8', 'SJIS', $note));
            }
           /* $display = "株式会社 Microbit 
        〒532-0011
        大阪市淀川区西中島５丁目６－３
        チサンマンション第２新大阪３０５号
        Tel:06-6305-1251,Fax:06-6305-1250";   */    
            $pdf->SetFont( 'MS-Mincho' ,'B',10);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetXY(18, 86);
            $pdf->Cell(73, 1, "", 0, 0.8, 'L', true);
            $pdf->SetXY(148, 20);
            $pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
            $pdf->SetXY(192, 20);
            $pdf->Cell(6.5, 6.1, "", 0, 0, 'L', true);
            $pdf->SetXY(120.2, 45);  
           // $pdf->MultiCell(65, 4, mb_convert_encoding($display, 'SJIS', 'UTF-8'), 0,'L', 0);   
            $pdf->Image("resources/assets/images/address.png", 120, 35, 70, 55, 'PNG' );
            $pdf->SetFont( 'MS-Mincho' ,'',9); 
            $pdf->SetXY(170, 29 );        
            $pdf->Write(4, iconv('UTF-8', 'SJIS', $in_query[0]->user_id ));
            // BABU
            $pdf->SetXY(153, 20 );
            $pdf->Cell(20, 6, "", 0, 1, 'L', true);
            $pdf->SetXY(172, 15.5 );
            $pdf->Cell(20, 6, "", 0, 1, 'L', true);
            
            $pdf->SetFont( 'MS-Mincho' ,'B',10);      
            $pdf->SetXY(170, 15.2 );
            $pdf->Write(6, $in_query[0]->quot_date);
            $pdf->SetFont( 'MS-Mincho' ,'B',11);      
            $pdf->SetXY(19, 37 );       
            $pdf->Write(6, mb_convert_encoding( $in_query[0]->company_name."  御中", 'SJIS', 'UTF-8'));

            // CHANGED BY BABU
            $pdf->SetXY(19, 41.6);
            $pdf->Cell(60, 8, "", 0, 1, 'L', true);
            $pdf->Line(19, 43, 100, 43); // 20mm from each edge

            // CHANGED BY BABU
            //下記の通りご請求申し上げます。
            if ($pageNo != 2) {
                $pdf->SetFont('MS-Mincho' ,'','');
                $pdf->SetXY(20, 70);
                $pdf->Cell(60, 6, mb_convert_encoding( "下記の通りご請求申し上げます。", 'SJIS', 'UTF-8'), 0, 1, 'L', true);
            } else {
                $pdf->SetXY(120, 65);
                $pdf->Cell(24, 25, "", 0, 1, 'L', true);
            }

            $pdf->SetFont('MS-Mincho' ,'B',16);     
            if($grandtotal=="") {       
                $grandtotal='0';
            }       
            $amount="¥ ".number_format($grandtotal)."-";        
            $pdf->SetXY(43, 76.3 );
            $pdf->Cell(41.3, 9.1, iconv('UTF-8', 'SJIS', $amount), 0, 0, 'R');    
            $pdf->SetFont( 'MS-Mincho' ,'B',9);
            $pdf->SetFillColor(175, 175, 175);
            $pdf->SetXY(14.5, 90.8);         
            $pdf->Cell(79.9, 6.4, iconv('UTF-8', 'SJIS', "                        品名"), 'LTRB', 1, 'L', true);
            $pdf->SetXY(94.2, 90.8);         
            $pdf->Cell(14.6, 6.4, iconv('UTF-8', 'SJIS', "  数量"), 'LRTB', 0, 'L', true);
            $pdf->SetXY(108.7, 90.8);            
            $pdf->Cell(28.4, 6.4, iconv('UTF-8', 'SJIS', "      単価"), 'LRTB', 0, 'L', true);
            $pdf->SetXY(137.1, 90.8);            
            $pdf->Cell(30.3, 6.4, iconv('UTF-8', 'SJIS', "       金額"), 'LRTB', 0, 'L', true);
            $pdf->SetXY(167.3, 90.8);            
            $pdf->Cell(29, 6.4, iconv('UTF-8', 'SJIS', "      摘要"), 'LRTB', 0, 'L', true);
            $y = 0;
            $n = 0;
            $y_axis = 96.9;
            if($data_count<19){
                $tb_count = 19;
            } else {
                $tb_count = $data_count;
            }
            for ($i = 1; $i <= $tb_count; $i++) {
                $work_specificarr="work_specific".$i;
                $quantityarr="quantity".$i;
                $unit_pricearr="unit_price".$i;
                $amountarr="amount".$i;
                $remarksarr="remarks".$i;
                if(!isset($in_query[0]->$work_specificarr)) {
                    $in_query[0]->$work_specificarr="";
                    $in_query[0]->$quantityarr="";
                    $in_query[0]->$unit_pricearr="";
                    $in_query[0]->$amountarr="";
                    $in_query[0]->$remarksarr="";
                }
                $pdf->SetFont( 'MS-Mincho' ,'B', '10');          
                if(($i%2)==0){          
                $pdf->SetFillColor(220, 220, 220);          
                }           
                else{           
                    $pdf->SetFillColor(255, 255, 255);          
                } 
                $inaxis = 96.9 + $y; 
                if($inaxis >= $pdf->h - 20) {
                    $pdf->AddPage();
                    $y = 0;
                    $n = 1;
                    $y_axis = 10;
                }         
                if($i>=19){
                    $pdf->SetXY(14.5, $y_axis+$y);         
                    $pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);       
                    $pdf->SetXY(19.5, $y_axis+$y);         
                    $pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
                    $pdf->SetXY(19.5, $y_axis+$y);   
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
                        $pdf->SetXY(108.7, $y_axis+$y);            
                        $pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);
                        $pdf->SetXY(108.7, $y_axis+$y);   
                        if ($in_query[0]->$unit_pricearr < 0) {
                            $pdf->SetTextColor(255,0,0);
                        }
                        $pdf->drawTextBox($in_query[0]->$unit_pricearr, 28.4, 6.0301, 'R', 'B', 0);
                    } else {            
                        $pdf->SetXY(108.7, $y_axis+$y);            
                        $pdf->Cell(28.4, 6.0301, "", 'LRTB', 0, 'R', true);          
                    }
                    $pdf->SetTextColor(0,0,0);    
                    if (!empty($in_query[0]->$amountarr)) {         
                        $pdf->SetXY(137.1, $y_axis+$y);            
                        $pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);
                        $pdf->SetXY(137.1, $y_axis+$y); 
                        if ($in_query[0]->$amountarr < 0) {
                            $pdf->SetTextColor(255,0,0);
                        }           
                        $pdf->drawTextBox($in_query[0]->$amountarr, 30.3, 6.0301, 'R', 'B', 0);        
                    } else {            
                        $pdf->SetXY(137.1, $y_axis+$y);            
                        $pdf->Cell(30.3, 6.0301, "", 'LRTB', 0, 'R', true);          
                    }
                    $pdf->SetTextColor(0,0,0);       
                    $pdf->SetXY(167.3, $y_axis+$y);            
                    $pdf->Cell(29, 6.0301, "", 'LRTB', 0, 'LB', true);
                    $pdf->SetXY(167.3, $y_axis+$y);            
                    $pdf->drawTextBox(iconv('UTF-8', 'SJIS', $in_query[0]->$remarksarr), 29, 6.0301, 'L', 'B', 0);         
        } else {
                    $pdf->SetXY(14.5, 96.9+$y);         
                    $pdf->Cell(5, 6.0301, "", 'LTB', 0, 'L', true);       
                    $pdf->SetXY(19.5, 96.9+$y);         
                    $pdf->Cell(74.8, 6.0301, "", 'TB', 0, 'L', true);
                    $pdf->SetXY(19.5, 96.9+$y);   
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
                        $pdf->SetXY(94.2, 96.9+$y);         
                        $pdf->Cell(14.6, 6.0301, "", 'LRTB', 0, 'C', true);
                        $pdf->SetXY(94.2, 96.9+$y);         
                        $pdf->drawTextBox($in_query[0]->$quantityarr, 14.6, 6.0301, 'C', 'B', 0);
                    } else {
                        $pdf->SetXY(94.2, 96.9+$y);         
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
                $y=$y+6.065;            
            }

            if ($n>0) {
                $ynew = $y + 10;    //px = 212
                $yn = $y + 16.065;      //px = 218
                $yn1 = $y + 16.165;   //px = 218.1
                $new = $y + 22.13;     //px = 224
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
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"小計   " ), 28.4, 6.1, 'C', 'B');
                $pdf->SetXY(108.7, $yn);          
                $pdf->Cell(28.4, 6.3, "", 'LBRT', 0, 'C', true);
                $pdf->SetXY(108.7, $yn);
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税      " ), 28.4, 6.3, 'C', 'B');
                $pdf->SetXY(108.7, $new1);          
                $pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
                $pdf->SetXY(108.7, $new1);
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計       " ), 28.4, 6.1, 'C', 'B');
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
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"非課税      " ), 28.4, 6.3, 'C', 'B');     
                $pdf->SetXY(108.7, $new1);          
                $pdf->Cell(28.4, 6.1, "", 'LBRT', 0, 'C', true);
                $pdf->SetXY(108.7, $new1);
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"税込合計       " ), 28.4, 6.1, 'C', 'B');        
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
                $pdf->drawTextBox(iconv('UTF-8', 'SJIS',"消費税      " ), 28.4, 6.3, 'C', 'B');         
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
            $pdf->Cell(64, 6.1, mb_convert_encoding((isset($bank_query[0]->BankName)) ? $bank_query[0]->BankName : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
            $pdf->SetXY(14.5, $yn1);         
            $pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "口座番号  "), 'L', 0, 'C', true); 
            $pdf->SetXY(39.5, $yn1);         
            $pdf->Cell(5, 6.1, "", 0, 0, 'C');         
            $pdf->SetXY(44.5, $yn1);         
            if(!isset($a_query[0]->AccNo)) {
                $accno = "";
            } else {
                $accno = $a_query[0]->AccNo;
            }
            $pdf->Cell(64, 6.3, mb_convert_encoding($type."  ".$accno, 'SJIS', 'UTF-8'), 0, 0, 'L');
            $pdf->SetXY(14.5, $new11);         
            $pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "    支店名"), 'L', 0, 'C', true);
            $pdf->SetXY(39.5, $new11);         
            $pdf->Cell(5, 6.1, "", 0, 0, 'C');  
            $pdf->SetXY(44.5, $new11);         
            $pdf->Cell(64, 6.3,  mb_convert_encoding((isset($a_query[0]->bankbranch)) ? $a_query[0]->bankbranch : '', 'SJIS', 'UTF-8'), 0, 0, 'L');
            $pdf->SetXY(14.5, $new21);         
            $pdf->Cell(25, 6.3, iconv('UTF-8', 'SJIS', "  口座名"), 'LB', 0, 'C', true);
            $pdf->SetXY(39.5, $new21);         
            $pdf->Cell(5, 6.3, "", 'B', 0, 'C');
            $pdf->SetXY(44.5, $new21);         
            $pdf->Cell(64, 6.3, mb_convert_encoding((isset($a_query[0]->FirstName)) ? $a_query[0]->FirstName : '', 'SJIS', 'UTF-8'), 'B', 0, 'L');
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
            $x=0;
            for ($rccnt=0; $rccnt < count($arrval); $rccnt++) { 
            }

           if(count($arrval) != 0) {

                $ynot = $ynew + 26.5;
                if (($n==0 && $ynot+20>=$pdf->h - 15 && count($arrval)==5) || ($n==0 && ($ynot+9)>=$pdf->h - 20 && count($arrval)==4) || ($n==0 && ($ynot+3)>=$pdf->h - 20 && count($arrval)<=3)  )
                {
                    $pdf->AddPage();
                    $ynot = 10;
                }
                $y=0;
                $exvalue=$rccnt-1;
                $pdf->SetFont( 'MS-Mincho' ,'B',11);
                $pdf->SetXY(22.5 ,$ynot);
                $pdf->Write(6, iconv('UTF-8', 'SJIS',  "【特記事項】"));
                $tilde = '~';//～,〜
                $japtilde = '〜';
                $japreptilde = "～";

                for($i = 0; $i<count($arrval); $i++) {
                    $pdf->SetFont( 'MS-Mincho' ,'',10);
                    $no=($rccnt-$exvalue).")";
                    $pdf->SetXY(22.5 ,($ynot+6)+$y );
                    $pdf->Write(6, iconv('UTF-8', 'SJIS', $no ));
                    $pdf->SetFont( 'MS-Mincho' ,'B',10);
                    $pdf->SetXY(26.5 ,($ynot+5)+$y );
                    $dispStr = $arrval[$i];
                    $dispStr = mb_convert_encoding($dispStr, 'SJIS', 'UTF-8'); 
                    $pdf->Write(9, $dispStr);

                    $y=$y+5.5;
                    $exvalue=$exvalue-1;
                }
            }                       
        }       
        //download secction
        $path= "resources/assets/uploadandtemplates/upload/Invoice";       
        $id=$in_query[0]->user_id;
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
        if($in_query[0]->pdf_flg == 0){
        if($filecount != 0){
                $pdf_name=$in_query[0]->user_id."_".str_pad($filecount , 2, '0', STR_PAD_LEFT);
                $pdfnamelist=$pdf_name;
            } else {
                $pdf_name=$in_query[0]->user_id;
                $pdfnamelist=$pdf_name;
            }
        } else {
            $pdf_name=$in_query[0]->user_id;
            $pdfnamelist=$pdf_name;
        }
        $pdfflg = Invoice::pdfflgset($in_query[0]->user_id,$pdfnamelist); 

        if((isset($in_query[0]->pdf_flg)?$in_query[0]->pdf_flg:"") == 0){
            $filepath = "resources/assets/uploadandtemplates/upload/Invoice/".$pdf_name.".pdf";
        } else {
            $filepath = "resources/assets/uploadandtemplates/upload/Invoice/".$pdf_name.".pdf";
        }
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
  // send mail process start 

    function sendmail(Request $request) {
        if($request->invoice_id=="") {
            return $this->index($request);
        }
        $sample="";
        $id = $request->invoice_id;
        $cust_id = $request->cust_id;
        $CompanyName = Invoice::getCompanyName($id);
        $CustomerDetails = Invoice::fnGetCustomerDetailsView($id);
        $selYear = date('Y');
        $selMonth = date('m');
        if($request->selYear!="") {
            $selYear = $request->selYear;
        }
        if($request->selMonth!="") {
            $selMonth = $request->selMonth;
        }
        $datemonth=$selYear."-".$selMonth;
        $getpdf = Invoice::fnGetallinvoice($cust_id,$datemonth);
        $e_getpdf = Invoice::fnGetEmailPDf($cust_id);
        $set_amount_array = array();
        if (!empty($e_getpdf)) {
                    $set_amount_array[0]['id'] = $getpdf[0]->id;
                    $set_amount_array[0]['estimate_id'] = $getpdf[0]->estimate_id;
                    $set_amount_array[0]['user_id'] = $e_getpdf[0]->invoice_id;
                    $getpdf = $set_amount_array;
                }
        return view('Invoice.sendmail',[
                                    'CompanyName' => $CompanyName,
                                    'CustomerDetails' => $CustomerDetails,
                                    'getpdf' => $getpdf,
                                    'datemonth' => $datemonth,
                                    'sample' => $sample,
                                    'request' => $request]);
    }

     function exceldownloadprocess(Request $request) {
            $template_name = 'resources/assets/uploadandtemplates/templates/invoice.xls';
            $tempname = "Invoice";
            $excel_name=$tempname;
            Excel::load($template_name, function($objTpl) use($request) {
            $request->plimit = 1000;
            $getinvoicedetails = Invoice::fngetinvoicedetails($request);
            $getestimatedetails = Invoice::fngetestimatedetails($request);
            $getestimatequery = Invoice::fnfetchestimatequery($request);
            $get_customer_detail = Invoice::fnGetCustomerDetail($getinvoicedetails[0]->trading_destination_selection);
            $bankid=$getinvoicedetails[0]->bankid;
            $branchid=$getinvoicedetails[0]->bankbranchid;
            $acc_no=$getinvoicedetails[0]->acc_no;
            $acc_details = Invoice::fnGetAccounts($bankid,$branchid,$acc_no);
            $gettaxquery = Invoice::fnGetTaxDetails($getinvoicedetails[0]->quot_date);
                if (!empty($acc_details)) {
                    if ($acc_details[0]->Type == 1) {
                        $type = "普通";
                    } else if ($acc_details[0]->Type == 2) {
                        $type = "Other";
                    } else {
                        $type = $acc_details[0]->Type;
                    }
                } else {
                    $type="";
                }
            $grandtotal = "";
            if (!empty($getinvoicedetails[0]->totalval)) {
                if ($getinvoicedetails[0]->tax != 2) {
                    $totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
                    $dispval = (($totroundval * intval($gettaxquery[0]->Tax))/100);
                    $grandtotal = $totroundval + $dispval;
                } else {
                    $totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
                    $dispval = 0;
                    $grandtotal = $totroundval + $dispval;
                }
            }
            if($grandtotal =="") {
                $grandtotal = '0';
                $dispval = 0;
                $getinvoicedetails[0]->totalval='0';
            }
            $objTpl->setActiveSheetIndex();
            $objTpl->setActiveSheetIndex(0);  //set first sheet as active

            $objTpl->getActiveSheet()->setCellValue('AE1', $getinvoicedetails[0]->quot_date);
            $objTpl->getActiveSheet()->setCellValue('AE52', $getinvoicedetails[0]->quot_date);
            $objTpl->getActiveSheet()->setCellValue('C5', $getinvoicedetails[0]->company_name."  御中");
            $objTpl->getActiveSheet()->setCellValue('C56', $getinvoicedetails[0]->company_name."  御中");
            $objTpl->getActiveSheet()->getStyle('H14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objTpl->getActiveSheet()->setCellValue('H14','¥ '. number_format($grandtotal).'-');
            $objTpl->getActiveSheet()->getStyle('H65')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objTpl->getActiveSheet()->setCellValue('H65','¥ '. number_format($grandtotal).'-');
            $objTpl->getActiveSheet()->getStyle('H14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objTpl->getActiveSheet()->getStyle('H65')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objTpl->getActiveSheet()->setCellValue('X39', $getinvoicedetails[0]->totalval);
            $objTpl->getActiveSheet()->setCellValue('X40', number_format($dispval));
            $objTpl->getActiveSheet()->setCellValue('X42', number_format($grandtotal));
                //client excel
            $objTpl->getActiveSheet()->setCellValue('X90', $getinvoicedetails[0]->totalval);
            $objTpl->getActiveSheet()->setCellValue('X91', number_format($dispval));
            $objTpl->getActiveSheet()->setCellValue('X93', number_format($grandtotal));
            if ($getinvoicedetails[0]->tax == 1) {
                $objTpl->getActiveSheet()->setCellValue('U41', "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X41', number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U42', "");
                $objTpl->getActiveSheet()->setCellValue('X42', "");
                //client excel
                $objTpl->getActiveSheet()->setCellValue('U92', "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X92', number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U93', "");
                $objTpl->getActiveSheet()->setCellValue('X93', "");
            } 
            if ($getinvoicedetails[0]->tax == 2) {
                $objTpl->getActiveSheet()->setCellValue('U40', "非課税");
                $objTpl->getActiveSheet()->setCellValue('X40', "0");
                $objTpl->getActiveSheet()->setCellValue('U41', "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X41', number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U42', "");
                $objTpl->getActiveSheet()->setCellValue('X42', "");
                //client excel
                $objTpl->getActiveSheet()->setCellValue('U91', "非課税");
                $objTpl->getActiveSheet()->setCellValue('X91', "0");
                $objTpl->getActiveSheet()->setCellValue('U92', "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X92', number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U93', "");
                $objTpl->getActiveSheet()->setCellValue('X93', "");
            }
            $na=(isset($get_customer_detail[0]->customer_name)?$get_customer_detail[0]->customer_name:"")."\r\n".(isset($get_customer_detail[0]->customer_address)?$get_customer_detail[0]->customer_address:"")."\r\n".(isset($get_customer_detail[0]->customer_contact_no)?$get_customer_detail[0]->customer_contact_no:"");
            $objTpl->getActiveSheet()->setCellValue('H39', (isset($acc_details[0]->bankname)?$acc_details[0]->bankname:""));
            $objTpl->getActiveSheet()->setCellValue('H40', $type);
            $objTpl->getActiveSheet()->setCellValue('H41', (isset($acc_details[0]->bankbranch)?$acc_details[0]->bankbranch:""));
            $objTpl->getActiveSheet()->setCellValue('H42', (isset($acc_details[0]->FirstName)?$acc_details[0]->FirstName:""));
            $objTpl->getActiveSheet()->setCellValue('K40', (isset($acc_details[0]->AccNo)?$acc_details[0]->AccNo:""));
            $objTpl->getActiveSheet()->setCellValue('AE3', (isset($getinvoicedetails[0]->user_id)?$getinvoicedetails[0]->user_id:""));
            //client excel
            $objTpl->getActiveSheet()->setCellValue('H90', (isset($acc_details[0]->bankname)?$acc_details[0]->bankname:""));
            $objTpl->getActiveSheet()->setCellValue('H91', $type);
            $objTpl->getActiveSheet()->setCellValue('H92', (isset($acc_details[0]->bankbranch)?$acc_details[0]->bankbranch:""));
            $objTpl->getActiveSheet()->setCellValue('H93', (isset($acc_details[0]->FirstName)?$acc_details[0]->FirstName:""));
            $objTpl->getActiveSheet()->setCellValue('K91', (isset($acc_details[0]->AccNo)?$acc_details[0]->AccNo:""));
            $objTpl->getActiveSheet()->setCellValue('AE54', (isset($getinvoicedetails[0]->user_id)?$getinvoicedetails[0]->user_id:""));

            $cellval = 19;
            $clientcellval = 70;
            $k = 1;
            foreach ($getinvoicedetails as $key=> $value){
                $workloop= $value->work_specific;
                $quantityloop =$value->quantity;
                $unit_priceloop=$value->unit_price;
                $amountloop=$value->amount;
                $remarksloop=$value->remarks;
                    // if(($k%2) == 0) {
                    //     $objTpl->getActiveSheet()->getStyle('B' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('B' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('R' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('R' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('C' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('C' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('U' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('U' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('X' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('X' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('AC' . ($cellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('AC' . ($cellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     //client excel
                    //     $objTpl->getActiveSheet()->getStyle('B' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('B' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('R' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('R' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('C' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('C' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('U' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('U' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('X' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('X' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    //     $objTpl->getActiveSheet()->getStyle('AC' . ($clientcellval + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    //     $objTpl->getActiveSheet()->getStyle('AC' . ($clientcellval + $k))->getFill()->getStartColor()->setRGB('DCDCDC');
                    // }
                
        $objTpl->getActiveSheet()->setCellValue('C' . ($cellval + $k),$workloop);

        $dotOccur = strpos($quantityloop, ".");
        if( ($quantityloop) != "" ){
            if ($dotOccur) {
                $quantityValue = "\0" . $quantityloop;
            } else {
                $quantityValue = "\0" .$quantityloop . ".0";
            }
        } else {
            $quantityValue = "";
        }
        $objTpl->getActiveSheet()->setCellValue('R' . ($cellval + $k), $quantityValue);
        
        if (!empty($unit_priceloop)) {
            if ($unit_priceloop < 0) {
                $objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k),$unit_priceloop)->getStyle('U' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
            }
            $objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k), $unit_priceloop);
        }
        if (!empty($amountloop)) {
            if ($amountloop < 0) {
                $objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k),$amountloop)->getStyle('X' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
            }
            $objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k), $amountloop);
        }
        $objTpl->getActiveSheet()->setCellValue('AC' . ($cellval + $k), $remarksloop);
        //client excel
        $objTpl->getActiveSheet()->setCellValue('C' . ($clientcellval + $k), $workloop);
        $objTpl->getActiveSheet()->setCellValue('R' . ($clientcellval + $k), $quantityValue);
        
        if (!empty($unit_priceloop)) {
            if ($unit_priceloop < 0) {
                $objTpl->getActiveSheet()->setCellValue('U' . ($clientcellval + $k), $unit_priceloop)->getStyle('U' . ($clientcellval + $k))->getFont()->getColor()->setRGB('FF0000');
            }
            $objTpl->getActiveSheet()->setCellValue('U' . ($clientcellval + $k),$unit_priceloop);
        }
        if (!empty($amountloop)) {
            if ($amountloop < 0) {
                $objTpl->getActiveSheet()->setCellValue('X' . ($clientcellval + $k), $amountloop)->getStyle('X' . ($clientcellval + $k))->getFont()->getColor()->setRGB('FF0000');
            }
            $objTpl->getActiveSheet()->setCellValue('X' . ($clientcellval + $k), $amountloop);
        }
        $objTpl->getActiveSheet()->setCellValue('AC' . ($clientcellval + $k), $remarksloop);
                $k++;
            }
        $cellval = 45;
        $clientcellval = 96;
        $arrval = array();
        for ($i = 1; $i <= 5; $i++) {
            $special_ins = "special_ins".$i;
            if($value->$special_ins != "") {
                array_push($arrval, $value->$special_ins);
            }
        }
        for ($rccnt=0; $rccnt < count($arrval); $rccnt++) { 
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), $arrval[$rccnt]);
            //client excel
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), $arrval[$rccnt]);
        }
        if(count($arrval) == 1) {
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E47:AB47');
            $objTpl->getActiveSheet()->unmergeCells('E47:AB47');
            $objTpl->getActiveSheet()->mergeCells('E48:AB48');
            $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
            $objTpl->getActiveSheet()->mergeCells('E49:AB49');
            $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
            $objTpl->getActiveSheet()->mergeCells('E50:AB50');
            $objTpl->getActiveSheet()->unmergeCells('E50:AB50');
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E98:AB98');
            $objTpl->getActiveSheet()->unmergeCells('E98:AB98');
            $objTpl->getActiveSheet()->mergeCells('E99:AB99');
            $objTpl->getActiveSheet()->unmergeCells('E99:AB99');
            $objTpl->getActiveSheet()->mergeCells('E100:AB100');
            $objTpl->getActiveSheet()->unmergeCells('E100:AB100');
            $objTpl->getActiveSheet()->mergeCells('E101:AB101');
            $objTpl->getActiveSheet()->unmergeCells('E101:AB101');
        } else if(count($arrval) == 2) {
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E48:AB48');
            $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
            $objTpl->getActiveSheet()->mergeCells('E49:AB49');
            $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
            $objTpl->getActiveSheet()->mergeCells('E50:AB50');
            $objTpl->getActiveSheet()->unmergeCells('E50:AB50');
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E99:AB99');
            $objTpl->getActiveSheet()->unmergeCells('E99:AB99');
            $objTpl->getActiveSheet()->mergeCells('E100:AB100');
            $objTpl->getActiveSheet()->unmergeCells('E100:AB100');
            $objTpl->getActiveSheet()->mergeCells('E101:AB101');
            $objTpl->getActiveSheet()->unmergeCells('E101:AB101');
        } else if(count($arrval) == 3) {
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E49:AB49');
            $objTpl->getActiveSheet()->unmergeCells('E49:AB49');    
            $objTpl->getActiveSheet()->mergeCells('E50:AB50');
            $objTpl->getActiveSheet()->unmergeCells('E50:AB50');
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E100:AB100');
            $objTpl->getActiveSheet()->unmergeCells('E100:AB100');  
            $objTpl->getActiveSheet()->mergeCells('E101:AB101');
            $objTpl->getActiveSheet()->unmergeCells('E101:AB101');
        } else if(count($arrval) == 4) {
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E50:AB50');
            $objTpl->getActiveSheet()->unmergeCells('E50:AB50');
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-3), $rccnt-3 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            $objTpl->getActiveSheet()->mergeCells('E101:AB101');
            $objTpl->getActiveSheet()->unmergeCells('E101:AB101');
        } else if(count($arrval) == 5) {
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-4), $rccnt-4 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-4), $rccnt-4 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-3), $rccnt-3 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-2), $rccnt-2 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt-1), $rccnt-1 . ")");
            $objTpl->getActiveSheet()->setCellValue('D' . ($clientcellval + $rccnt), $rccnt . ")");
            $objTpl->getActiveSheet()->setCellValue('E' . ($clientcellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
        } else {
            $objTpl->getActiveSheet()->setCellValue('D45', "");
            $objTpl->getActiveSheet()->mergeCells('E46:AB46');
            $objTpl->getActiveSheet()->unmergeCells('E46:AB46');
            $objTpl->getActiveSheet()->mergeCells('E47:AB47');
            $objTpl->getActiveSheet()->unmergeCells('E47:AB47');
            $objTpl->getActiveSheet()->mergeCells('E48:AB48');
            $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
            $objTpl->getActiveSheet()->mergeCells('E49:AB49');
            $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
            $objTpl->getActiveSheet()->mergeCells('E50:AB50');
            $objTpl->getActiveSheet()->unmergeCells('E50:AB50');
            //client excel
            $objTpl->getActiveSheet()->setCellValue('D96', "");
            $objTpl->getActiveSheet()->mergeCells('E97:AB97');
            $objTpl->getActiveSheet()->unmergeCells('E97:AB97');
            $objTpl->getActiveSheet()->mergeCells('E98:AB98');
            $objTpl->getActiveSheet()->unmergeCells('E98:AB98');
            $objTpl->getActiveSheet()->mergeCells('E99:AB99');
            $objTpl->getActiveSheet()->unmergeCells('E99:AB99');
            $objTpl->getActiveSheet()->mergeCells('E100:AB100');
            $objTpl->getActiveSheet()->unmergeCells('E100:AB100');
            $objTpl->getActiveSheet()->mergeCells('E101:AB101');
            $objTpl->getActiveSheet()->unmergeCells('E101:AB101');
        }
        $objTpl->getActiveSheet()->getStyle("AC19:AC38")->applyFromArray(
                array(
                    'borders' => array(
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $objTpl->getActiveSheet()->getStyle("AC70:AC89")->applyFromArray(
                array(
                    'borders' => array(
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $objTpl->getActiveSheet()->getStyle('W13')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objTpl->getActiveSheet()->getStyle('C14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objTpl->getActiveSheet()->getStyle('H14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objTpl->getActiveSheet()->getStyle('C65')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objTpl->getActiveSheet()->getStyle('H65')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objTpl->getActiveSheet()->setTitle($getinvoicedetails[0]->user_id);
            $objTpl->getActiveSheet(0)->setSelectedCells('B1');
            $objTpl->getActiveSheet(0)->setSelectedCells('A1');
            $flpath='.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$flpath.'"');
            header('Cache-Control: max-age=0');
        })->setFilename($excel_name)->download('xls');
    }
   
    function newexceldownloadprocess(Request $request) {
			$template_name = 'resources/assets/uploadandtemplates/templates/invoicenew.xls';
			$tempname = "Invoice";
			$excel_name=$tempname;
		Excel::load($template_name, function($objTpl) use($request) {
			$request->plimit = 1000;
			$getinvoicedetails = Invoice::fngetinvoicedetails($request);
			$getestimatedetails = Invoice::fngetestimatedetails($request);
			$getestimatequery = Invoice::fnfetchestimatequery($request);
			$get_customer_detail = Invoice::fnGetCustomerDetail($getinvoicedetails[0]->trading_destination_selection);
			$bankid=$getinvoicedetails[0]->bankid;
			$branchid=$getinvoicedetails[0]->bankbranchid;
			$acc_no=$getinvoicedetails[0]->acc_no;
			$acc_details = Invoice::fnGetAccounts($bankid,$branchid,$acc_no);
			$gettaxquery = Invoice::fnGetTaxDetails($getinvoicedetails[0]->quot_date);
				if (!empty($acc_details)) {
					if ($acc_details[0]->Type == 1) {
						$type = "普通";
					} else if ($acc_details[0]->Type == 2) {
						$type = "Other";
					} else {
						$type = $acc_details[0]->Type;
					}
				} else {
						$type="";
				}
			$grandtotal = "";
			if (!empty($getinvoicedetails[0]->totalval)) {
				if ($getinvoicedetails[0]->tax != 2) {
					$totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
					$dispval = (($totroundval * intval($gettaxquery[0]->Tax))/100);
					$grandtotal = $totroundval + $dispval;
				} else {
					$totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
					$dispval = 0;
					$grandtotal = $totroundval + $dispval;
				}
			}
			if($grandtotal =="") {
				$grandtotal = '0';
				$dispval = 0;
				$getinvoicedetails[0]->totalval='0';
			}
			$objTpl->setActiveSheetIndex();
			$objTpl->setActiveSheetIndex(0);  //set first sheet as active
			$objTpl->getActiveSheet()->setCellValue('AE1', $getinvoicedetails[0]->quot_date);
			$objTpl->getActiveSheet()->setCellValue('C5', $getinvoicedetails[0]->company_name."  御中");
			$objTpl->getActiveSheet()->getStyle('H14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objTpl->getActiveSheet()->setCellValue('H14','¥ '. number_format($grandtotal).'-');
			$objTpl->getActiveSheet()->getStyle('H14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objTpl->getActiveSheet()->setCellValue('X39', $getinvoicedetails[0]->totalval);
			$objTpl->getActiveSheet()->setCellValue('X40', number_format($dispval));
			$objTpl->getActiveSheet()->setCellValue('X42', number_format($grandtotal));
			if ($getinvoicedetails[0]->tax == 1) {
				$objTpl->getActiveSheet()->setCellValue('U41', "税込合計");
				$objTpl->getActiveSheet()->setCellValue('X41', number_format($grandtotal));
				$objTpl->getActiveSheet()->setCellValue('U42', "");
				$objTpl->getActiveSheet()->setCellValue('X42', "");
			} 

			if ($getinvoicedetails[0]->tax == 2) {
				$objTpl->getActiveSheet()->setCellValue('U40', "非課税");
				$objTpl->getActiveSheet()->setCellValue('X40', "0");
				$objTpl->getActiveSheet()->setCellValue('U41', "税込合計");
				$objTpl->getActiveSheet()->setCellValue('X41', number_format($grandtotal));
				$objTpl->getActiveSheet()->setCellValue('U42', "");
				$objTpl->getActiveSheet()->setCellValue('X42', "");
			}
			$na=(isset($get_customer_detail[0]->customer_name)?$get_customer_detail[0]->customer_name:"")."\r\n".(isset($get_customer_detail[0]->customer_address)?$get_customer_detail[0]->customer_address:"")."\r\n".(isset($get_customer_detail[0]->customer_contact_no)?$get_customer_detail[0]->customer_contact_no:"");
			$objTpl->getActiveSheet()->setCellValue('H39', (isset($acc_details[0]->bankname)?$acc_details[0]->bankname:""));
			$objTpl->getActiveSheet()->setCellValue('H40', $type);
			$objTpl->getActiveSheet()->setCellValue('H41', (isset($acc_details[0]->bankbranch)?$acc_details[0]->bankbranch:""));
			$objTpl->getActiveSheet()->setCellValue('H42', (isset($acc_details[0]->FirstName)?$acc_details[0]->FirstName:""));
			$objTpl->getActiveSheet()->setCellValue('K40', (isset($acc_details[0]->AccNo)?$acc_details[0]->AccNo:""));
			$objTpl->getActiveSheet()->setCellValue('AE3', (isset($getinvoicedetails[0]->user_id)?$getinvoicedetails[0]->user_id:""));
			$cellval = 19;
			// Rajaguru Update
			
			$k = 1;
			$setcolor=38;
			foreach ($getinvoicedetails as $key=> $value) {
				$workloop= $value->work_specific;
				$quantityloop =$value->quantity;
				$unit_priceloop=$value->unit_price;
				$amountloop=$value->amount;
				$remarksloop=$value->remarks;
				$colorarray=array(
						        'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => 'C0C0C0')
						        )
						    );
				
				if ($k>19) {
					$objTpl->getActiveSheet()->insertNewRowBefore(($cellval + $k), 1);
                    $setcolor++;
					if (($k%2)!=0) {
						$objTpl->getActiveSheet()->getStyle('B'.$setcolor.':AC'.$setcolor)->applyFromArray(
							array(
						        'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => 'FFFFFF')
						        )
						    )
						  
						);
					} else {
						$objTpl->getActiveSheet()->getStyle('B'.$setcolor.':AC'.$setcolor)->applyFromArray(
						     $colorarray
						);
					}
                    $objTpl->getActiveSheet()->getRowDimension('1')->setRowHeight(24.75);
					if ($cellval + $k == '39') {
						$objTpl->getActiveSheet()->getStyle('B' . ('38') . ':AI' . ('38'))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					}
					$objTpl->getActiveSheet()->getStyle('B' . ($cellval + $k) . ':AI' . ($cellval + $k))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					$objTpl->getActiveSheet()->mergeCells('C' . ($cellval + $k) . ':P' . ($cellval + $k));
					$objTpl->getActiveSheet()->mergeCells('R' . ($cellval + $k) . ':T' . ($cellval + $k));
					$objTpl->getActiveSheet()->mergeCells('U' . ($cellval + $k) . ':W' . ($cellval + $k));
					$objTpl->getActiveSheet()->mergeCells('X' . ($cellval + $k) . ':AB' . ($cellval + $k));
					$objTpl->getActiveSheet()->mergeCells('AC' . ($cellval + $k) . ':AI' . ($cellval + $k));
				}
				$objTpl->getActiveSheet()->setCellValue('C' . ($cellval + $k),$workloop);

				$dotOccur = strpos($quantityloop, ".");
				if( ($quantityloop) != "" ){
					if ($dotOccur) {
						$quantityValue = "\0" . $quantityloop;
					} else {
						$quantityValue = "\0" .$quantityloop . ".0";
					}
				} else {
					$quantityValue = "";
				}
				$objTpl->getActiveSheet()->setCellValue('R' . ($cellval + $k), $quantityValue);
				if (!empty($unit_priceloop)) {
					if ($unit_priceloop < 0) {
						$objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k),$unit_priceloop)->getStyle('U' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
					}
					$objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k), $unit_priceloop);
				}
				if (!empty($amountloop)) {
					if ($amountloop < 0) {
						$objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k),$amountloop)->getStyle('X' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
					}
					$objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k), $amountloop);
				}
				$objTpl->getActiveSheet()->setCellValue('AC' . ($cellval + $k), $remarksloop);
				$k++;
			}
			if (($cellval + $k)>38) {
				$objTpl->getActiveSheet()->getStyle('B' . ($cellval + ($k-1)) . ':AI' . ($cellval + ($k-1)))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
			$cellval = 45;
			// Rajaguru Update
			if ($k>19) {
				$cellval = $k+25;
			}
			else{
				$cellval = 45;
			}
			$arrval = array();
			for ($i = 1; $i <= 5; $i++) {
				$special_ins = "special_ins".$i;
				if($value->$special_ins != "") {
					array_push($arrval, $value->$special_ins);
				}
			}
			for ($rccnt=0; $rccnt < count($arrval); $rccnt++) { 
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), $arrval[$rccnt]);
			}
			if(count($arrval) == 1) {
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

				//Datas is not showed in excel due to merge cells (20/02/19)
				// $objTpl->getActiveSheet()->mergeCells('E47:AB47');
				// $objTpl->getActiveSheet()->unmergeCells('E47:AB47');
				// $objTpl->getActiveSheet()->mergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->mergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->mergeCells('E50:AB50');
				// $objTpl->getActiveSheet()->unmergeCells('E50:AB50');

			} else if(count($arrval) == 2) {
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

				//Datas is not showed in excel due to merge cells
				// $objTpl->getActiveSheet()->mergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->mergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->mergeCells('E50:AB50');
				// $objTpl->getActiveSheet()->unmergeCells('E50:AB50');

			} else if(count($arrval) == 3) {
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

				//Datas is not showed in excel due to merge cells
				// $objTpl->getActiveSheet()->mergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->unmergeCells('E49:AB49');    
				// $objTpl->getActiveSheet()->mergeCells('E50:AB50');
				// $objTpl->getActiveSheet()->unmergeCells('E50:AB50');

			} else if(count($arrval) == 4) {
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

				//Datas is not showed in excel due to merge cells
				// $objTpl->getActiveSheet()->mergeCells('E50:AB50');
				// $objTpl->getActiveSheet()->unmergeCells('E50:AB50');

			} else if(count($arrval) == 5) {
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-4), $rccnt-4 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-3), $rccnt-3 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-2), $rccnt-2 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt-1), $rccnt-1 . ")");
				$objTpl->getActiveSheet()->setCellValue('D' . ($cellval + $rccnt), $rccnt . ")");
				$objTpl->getActiveSheet()->setCellValue('E' . ($cellval + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
			} else {
				$objTpl->getActiveSheet()->setCellValue('D45', "");
				//Datas is not showed in excel due to merge cells
				// $objTpl->getActiveSheet()->mergeCells('E46:AB46');
				// $objTpl->getActiveSheet()->unmergeCells('E46:AB46');
				// $objTpl->getActiveSheet()->mergeCells('E47:AB47');
				// $objTpl->getActiveSheet()->unmergeCells('E47:AB47');
				// $objTpl->getActiveSheet()->mergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->unmergeCells('E48:AB48');
				// $objTpl->getActiveSheet()->mergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->unmergeCells('E49:AB49');
				// $objTpl->getActiveSheet()->mergeCells('E50:AB50');
				// $objTpl->getActiveSheet()->unmergeCells('E50:AB50');

			}
			if ($k>19) {
				$cellval=19;
				$objTpl->getActiveSheet()->getStyle('AC19'  . ':AC' . ($cellval + ($k-1)))->applyFromArray(
					array(
						'borders' => array(
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
						)
					)
					);
				
			}
            
			$objTpl->getActiveSheet()->getStyle("AC19:AC38")->applyFromArray(
					array(
						'borders' => array(
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$objTpl->getActiveSheet()->getStyle('W13')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle('C14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objTpl->getActiveSheet()->getStyle('H14')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$objTpl->getActiveSheet()->setTitle('請求書');
			$sheet1 = $objTpl->getActiveSheet()->copy();
			$sheet2 = clone $sheet1;
			$sheet_title = '請求書(控)';
			$sheet2->setTitle($sheet_title);
			$objTpl->addSheet($sheet2);
			$sheet2->setCellValue('B2', "請求書(控)");
			unset($sheet2);
			unset($sheet1);
			$objTpl->getActiveSheet(0)->setSelectedCells('B1');
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
			$flpath='.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$flpath.'"');
			header('Cache-Control: max-age=0');
		})->setFilename($excel_name)->download('xls');
	}

    public static function paymentaddedit(Request $request) {
        if(!isset($request->estimate_id)){
            return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));  
        }
        $estimate_id = $request->estimate_id;
        if($request->quot_date !="") {
            $request->selYear = substr($request->quot_date, 0,4);
            $request->selMonth = substr($request->quot_date, 5,2);
        }
        $date_month = $request->selYear."-".$request->selMonth;
        $oldbalance_from="";
        $currentfrom = Carbon\Carbon::createFromFormat('Y-m-d', $date_month.'-01');
        $past1year   = $currentfrom->subMonths(13);
        $past1year = $past1year->modify('first day of this month');
        $past1year->addDays(15);
        $oldbalance_from = $past1year->format('Y-m-d');

        $g_bank = Payment::fnGetBankDetails($request);
        $get_data = Payment::fnGetInvoiceDtl($estimate_id);
       // print_r($get_data); exit();
        // GET TAX FROM ESTIMATE TABLE
        $get_tax = Payment::fnGetEstimateDtl($get_data[0]->estimate_id);

        // GET PERCENTAGE OF TAX FROM KESSAN TABLE
        $execute_tax = Payment::fnGetTaxDetails($get_data[0]->quot_date);

        $get_row = Payment::fnGetInvoiceDetails($estimate_id,$oldbalance_from, $date_month, $get_data[0]->trading_destination_selection, $get_data[0]->paid_status);
        //print_r($get_row); exit();
        // CHECK IF ALREADY DATA EXIST IN THIS MONTH
        $recexe = Payment::fnCheckDataExist($get_data[0]->company_name ,$oldbalance_from, $date_month);

        // CHECK IF ANY PREVIOUS MONTH BALANCE
        $g_invoice_amount = Payment::fnCheckDataBalanceInvoice($oldbalance_from,$date_month, $get_data[0]->company_name);
        $dispval_inv = 0;
        $grandtotal_inv = 0;
        $divtotal_inv = 0;
        $divtotal_inv_temp = 0;
        $invoice_amount=0;
        $temp = false;
        $i = 0;
        $disp_record = array();
        foreach ($g_invoice_amount as $key => $g_invoice_amount) {
            $get_taxval = Payment::fnGetTaxDetails($g_invoice_amount->quot_date);

            if (!empty($g_invoice_amount->totalval)) {
                if ($g_invoice_amount->tax != 2) {
                    $totroundval = preg_replace("/,/", "", $g_invoice_amount->totalval);
                    $dispval_inv = (($totroundval * intval(isset($get_taxval[0]->Tax)?$get_taxval[0]->Tax:0))/100);
                    $dis = number_format($dispval_inv);
                    $dispval_inv1 = preg_replace("/,/", "", $dis);
                    $grandtotal_inv = $totroundval + $dispval_inv1;
                } else {
                    $totroundval = preg_replace("/,/", "", $g_invoice_amount->totalval);
                    $dispval_inv = 0;
                    $grandtotal_inv = $totroundval + $dispval_inv;
                    $dispval_inv1 = $dispval_inv;
                }
                if ( $g_invoice_amount->paid_status != 1) {
                    $disp_record[$i]['id'] = $g_invoice_amount->id;
                    $disp_record[$i]['user_id'] = $g_invoice_amount->user_id;
                    $disp_record[$i]['quot_date'] = $g_invoice_amount->quot_date;
                    $disp_record[$i]['company_name'] = $g_invoice_amount->company_name;
                    $disp_record[$i]['payment_date'] = $g_invoice_amount->payment_date;
                    $disp_record[$i]['totalval'] = $grandtotal_inv;
                    $disp_record[$i]['tax'] = $g_invoice_amount->tax;
                    $disp_record[$i]['oldRec'] = "1";
                    $disp_record[$i]['paid_status'] =$g_invoice_amount->paid_status;
                    $paid_status=$g_invoice_amount->paid_status;
                    $disp_record[$i]['pre_paid_status'] = 3;
                    $temp = true;
                    $i++;
                    $divtotal_inv_temp += $grandtotal_inv;
                }
                if ( $g_invoice_amount->paid_status == 1 ) {
                //previous record paid After current month, that record dont view current month record.         
                    if( $g_invoice_amount->paid_yearmonth> $date_month){
                        $divtotal_inv_temp += $grandtotal_inv;
                    }
                    $currentpay_prerecord = Payment::fnCheckDataExist($get_data[0]->company_name,$oldbalance_from, $date_month);

                    foreach ($currentpay_prerecord as $key => $currentpay_prerecord) {
                            $pre_paidval=explode(',', $currentpay_prerecord->paid_id);
                            for ($j = 0; $j < count($pre_paidval); $j++) {
                                if( $pre_paidval[$j] == $g_invoice_amount->id ){
                //previous record paid current month particular invoice_id, that record only view the particular invoice_id not for all current month record.
                //current month paid record also view the previous paid detail for the current month        
                                    if($estimate_id ==$currentpay_prerecord->invoice_id || 
                                        ($g_invoice_amount->paid_status == "1" && $get_data[0]->paid_status == "1")){
                                        $disp_record[$i]['id'] = $g_invoice_amount->id;
                                        $disp_record[$i]['user_id'] = $g_invoice_amount->user_id;
                                        $disp_record[$i]['quot_date'] = $g_invoice_amount->quot_date;
                                        $disp_record[$i]['company_name'] = $g_invoice_amount->company_name;
                                        $disp_record[$i]['payment_date'] = $g_invoice_amount->payment_date;
                                        $disp_record[$i]['totalval'] = $grandtotal_inv;
                                        $disp_record[$i]['tax'] = $g_invoice_amount->tax;
                                        $disp_record[$i]['oldRec'] = "1";
                                        $disp_record[$i]['paid_status'] =$g_invoice_amount->paid_status;
                                        $paid_status=$g_invoice_amount->paid_status;
                                        if($g_invoice_amount->paid_status== "1"){
                                            $invoice_amount += preg_replace("/,/", "",$grandtotal_inv);
                                        }

                                        $temp = true;
                                        $i++;
                                        $divtotal_inv_temp += $grandtotal_inv;

                                    }else{
                                        $divtotal_inv_temp += $grandtotal_inv;
                                    }
                                }

                            }
                    }
                }
                $divtotal_inv += $grandtotal_inv;
            }
        }
        $g_bal = Payment::fnCheckDataBalance($oldbalance_from,$date_month, $get_data[0]->company_name);
        $pre_balance = 0;
        $pre_date = array();
        foreach ($g_bal as $key => $g_bal) {
            $pre_balance += preg_replace("/,/", "", $g_bal->totalval);
            array_push($pre_date, $g_bal->invoice_id);
        }

        if (count($pre_date) > 0) {
            $e_prev_query = Payment::fnGetInvoiceDtl(reset($pre_date));
        }
        

        $balance_invoice = $divtotal_inv - $pre_balance;
        $balance_invoice_temp = $balance_invoice - $divtotal_inv_temp;
        // CURRENT YEAR  ANY ONE RECORD PAID THE BALANCE OR EXCESS AMOUNT CALCULATED 
        // AND THE BALANCE AMOUNT OR EXCESS AMOUNT CHANGE PROCESS
            if($get_data[0]->paid_status !=1 ){
                foreach ($recexe as $key => $rec) {
                    $balance =preg_replace("/,/", "",$rec->balance);
                    $balance_invoice_temp = $balance_invoice_temp + $balance;
                }
            }
        // // SET FROM DATE
        // $currentfrom = Carbon\Carbon::createFromFormat('Y-m-d', $date_month);
        // $past6months   = $currentfrom->subMonths(7);
        // $past6months = $past6months->modify('first day of this month');
        // $past6months->addDays(15);
        // $oldbalance_from = $past6months->format('Y-m-d');

        // $currentto = Carbon\Carbon::createFromFormat('Y-m-d', $date_month);
        // $past1month   = $currentto->subMonths(1);
        // $past1month = $past1month->modify('first day of this month');
        // $past1month->addDays(14);
        // $oldbalance_to = $past1month->format('Y-m-d');
        // //----------------------------
        if ( $balance_invoice_temp != 0) {
            $excessOccur = strpos($balance_invoice, "-");
            if ($excessOccur !== false) {
                $disp_record[$i]['company_name'] = "未払残高";
            } else {
                $disp_record[$i]['company_name'] = "未払残高";
            }
            // $disp_record[$i]['totalval'] = Payment::fnGETtotalAmount($request,$oldbalance_from,$oldbalance_to,$value->clientnumber);
            $disp_record[$i]['totalval'] = $balance_invoice_temp;
            $disp_record[$i]['tax'] = 2;
            $disp_record[$i]['paid_status'] =0;
            $disp_record[$i]['oldRec'] = "1";
            $i++;
        }
        if ( (!empty($balance_invoice) && $temp)) {
            $disp_record[$i]['id'] = "";
            $disp_record[$i]['user_id'] = "";
            $disp_record[$i]['quot_date'] = (isset($e_prev_query[0]->payment_date)) ? $e_prev_query[0]->payment_date : '';
            $disp_record[$i]['company_name'] = "pre_unpaid_record";
            $disp_record[$i]['payment_date'] = "";
            $disp_record[$i]['totalval'] = "";//$pre_balance;
            $disp_record[$i]['tax'] = 2;
            $i++;
        }
        $paidactual_day="";
        $payactual_day="";
        $paymentactual_day="";
        $paid_day="";
        $cur_paidstatus="";
        $depo_amo=0;
        $depo_balance=0;
        $deposit_amount=0;
        $value=0;
        $grandtotal1="";
        foreach ($get_row as $key => $get_row) {
        // CURRENT CLICK RECORD PAID ONLY VIEW PAID RECORD ELSE ONLY VIEW UNPAID RECORD
            if ( $get_row->paid_status == $get_data[0]->paid_status ) {
                $disp_record[$i]['id'] = $get_row->id;
                $disp_record[$i]['user_id'] = $get_row->user_id;
                $disp_record[$i]['quot_date'] = $get_row->quot_date;
                $disp_record[$i]['company_name'] = $get_row->company_name;
                $disp_record[$i]['payment_date'] = $get_row->payment_date;
                $disp_record[$i]['totalval'] = $get_row->totalval;
                $disp_record[$i]['tax'] = $get_row->tax;
                $disp_record[$i]['paid_yearmonth']=$get_row->paid_yearmonth;
                $paid_status=$get_row->paid_status;
                // START BABU
                $paid_day = $get_row->paid_date;
                $paymentactual_day = $get_row->payment_date;
                $paidactual_day .= $get_row->paid_date."@";
                $payactual_day .= $get_row->payment_date."@";
                // END BABU
                $cur_paidstatus .= $paid_status.",";
                if($get_row->paid_status== "1"){
                    if ($disp_record[$i]['tax'] != 2) {
                        $totroundval = preg_replace("/,/", "", $disp_record[$i]['totalval']);
                        $dispval = (($totroundval * intval($execute_tax[0]->Tax))/100);
                        $grandtotal1 = $totroundval + $dispval;
                        $invoice_amount += preg_replace("/,/", "",$grandtotal1);
                    }else{
                        $invoice_amount += preg_replace("/,/", "",$get_row->totalval);
                    }
                }
                $i++;
            }
        }


        if ((count($recexe) > 0) && ($get_data[0]->paid_status == 1)) {
            $a=0;
            $checkboxdisabled = "disabled";
            $cur_paidamount=explode(',', $cur_paidstatus);
            foreach ($recexe as $key => $rec) {
                $disp_record[$i]['id'] = "";
                $disp_record[$i]['user_id'] = "";
                $disp_record[$i]['quot_date'] = $rec->invoice_payment_date;
                $disp_record[$i]['company_name'] = "入金";
                $disp_record[$i]['payment_date'] = $rec->payment_date;
                $disp_record[$i]['totalval'] = -$rec->totalval;
                $disp_record[$i]['tax'] = 2;
                $deposit_amount=-$rec->totalval;
                $depo_amo += $rec->totalval;
                $depo_balance =preg_replace("/,/", "",$rec->balance);
                $i++;
                $a++;
            }
            $afterpay_amount = $invoice_amount - ($depo_amo+ ($depo_balance));
            if ( $afterpay_amount != 0) {
            for ($j = $a; $j < (count($cur_paidamount)-1); $j++) {
                $disp_record[$i]['id'] = "";
                $disp_record[$i]['user_id'] = "";
                $disp_record[$i]['quot_date'] = $paymentactual_day;
                $disp_record[$i]['company_name'] = "入金";
                $disp_record[$i]['payment_date'] = $paid_day;
                $disp_record[$i]['totalval'] = -$afterpay_amount;
                $disp_record[$i]['tax'] = 2;

            }}
            //
        } else {
            if ($get_data[0]->paid_status == 1) {
                $disp_record[$i]['quot_date'] = $paymentactual_day;
                $disp_record[$i]['company_name'] = "入金";
                $disp_record[$i]['payment_date'] = $paid_day;
                $disp_record[$i]['totalval'] = -$invoice_amount;
                $disp_record[$i]['tax'] = 2;
                $deposit_amount=-$invoice_amount;
                $checkboxdisabled = "disabled";
            } else {
                $checkboxdisabled = "";
            }
        }
        if($deposit_amount<$invoice_amount){
            $value=abs($deposit_amount+($invoice_amount));
        }else if($deposit_amount>$invoice_amount){
            $value=abs($deposit_amount-($invoice_amount));
        }
        if($balance_invoice_temp <= $value){
            $new_balance_amount=(($invoice_amount +$balance_invoice_temp)+($deposit_amount));
        }
        //print_r($disp_record); exit();
        $sample = Invoice::fnGetOtherDetails($request);
        return view('Invoice.paymentaddedit',['disp_record' => $disp_record,
                                            'checkboxdisabled' => $checkboxdisabled,
                                            'get_data' => $get_data,
                                            'g_bank' => $g_bank,
                                            'balance_invoice_temp' => $balance_invoice_temp,
                                            'value' => $value,
                                            'grandtotal1' => $grandtotal1,
                                            'execute_tax' => $execute_tax,
                                            'date_month' => $date_month,
                                            'request' => $request]);
    }
    public static function ajaxgetbillingdetails(Request $request) {
        $getbillingdetails = Invoice::fnGetBillingDetails($request);
        $billingresult=json_encode($getbillingdetails);
        echo $billingresult; exit;
    }
    public static function empnamepopup(Request $request) {
        $empname = Invoice::fnGetEmpDetails($request);
        $empnamenonstaff = Invoice::fnGetNonstaffEmpDetails($request);
        return view('Invoice.empnamepopup',['empname' => $empname,
                                        'empnamenonstaff' => $empnamenonstaff,
                                        'request' => $request]);
    }
    public static function assignemployee(Request $request) {
        if (!isset($request->year_month)) {
        return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
        }
        $assignemp = Invoice::fnGetinvoldDetails($request);
        return view('Invoice.assignemp',['assignemp' => $assignemp,
                                        'request' => $request]);
    }
    public static function invoicecopy(Request $request) {
        if (!isset($request->year_month)) {
        return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
        }
        $assignemp = Invoice::fnGetinvoiceUserDatabydate($request);
        $newLastMthLstDay = "";
        $lastDayPreviousMth = new Carbon\Carbon('last day of last month');
        if ($lastDayPreviousMth->isWeekday()) {
            $newLastMthLstDay = $lastDayPreviousMth->format('Y-m-d');
        } else {
            if($lastDayPreviousMth->isSaturday()) {
                $newLastMthLstDay = $lastDayPreviousMth->subDays(1)->format('Y-m-d');
            } else if ($lastDayPreviousMth->isSunday()) {
                $newLastMthLstDay = $lastDayPreviousMth->subDays(2)->format('Y-m-d');
            }
        }
        return view('Invoice.invoicemultiplecopy',['assignemp' => $assignemp,
                                        'lastMtnLastDay' => $newLastMthLstDay,
                                        'request' => $request]);
    }
    public static function invoicecopyprocess(Request $request) {
        $insert = false;
        $invoicedata = array();
        if (isset($request->invcount)) {
            // for ($j=1; $j <= $request->invcount; $j++) {
            //     $invid='invid'.$j;
            //     $invoiceid = $request->$invid;
            //     if (isset($request->addcheck[$invoiceid])) {
            //         $code = Invoice::fnGenerateInvoiceID($request);
            //         $insert = Invoice::fnGetinvoiceUserDatabyid($request, $invoiceid, $code);
            //     }
            // }
            if (isset($request->addcheck)) {
                foreach ($request->addcheck as $invoiceid => $value) {
                    $code = Invoice::fnGenerateInvoiceID($request);
                    $insert = Invoice::fnGetinvoiceUserDatabyid($request, $invoiceid, $code);
                }
            }
        }
        if($insert) {
            Session::flash('success', trans('messages.lbl_insertsucss') );
            Session::flash('type', 'alert-success'); 
        } else {
            Session::flash('type', 'Inserted Unsucessfully!');
            Session::flash('type', 'alert-danger'); 
        }
        // }
       $spldm = explode('-', $request->quot_date);
            Session::flash('selMonth', $spldm[1]); 
            Session::flash('selYear', $spldm[0]);
        return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
    }
    public static function editempassignprocess(Request $request) {
        $updateassignemp = Invoice::fninsertinvoldDetails($request);
        if($updateassignemp) {
            Session::flash('success', 'Updated Sucessfully!' );
            Session::flash('type', 'alert-success'); 
        } else {
            Session::flash('type', 'Updated Unsucessfully!'); 
            Session::flash('type', 'alert-danger'); 
        }
        Session::flash('selYear', $request->selYear);
        Session::flash('selMonth', $request->selMonth);
        return Redirect::to('Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
    }
    function invoiceexceldownloadprocess(Request $request) {
         $curTime = date('Y/m/d  H:i:s');
        $selectedYearMonth = explode("-", $request->selYearMonth);
        $date_month = $request->selYearMonth;
        //print_r($date_month);
        $TotEstquery = Invoice::fnGetinvoiceDownload($request,$date_month);
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
        $totalval = 0;
        $sumdispval1 = 0;
        $sumtotalval = 0;
        $sumgrandtotal = 0;
        $grandtax = 0;
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
                    }
                else{
                    $totroundval = preg_replace("/,/", "", $value->totalval);
                    $dispval1 = 0;
                    $grandtotal = $totroundval + $dispval1;

                }
                $grandtax = preg_replace("/,/", "", $dispval1);
                $sumdispval1 += $grandtax;
                $sumgrandtotal += round($grandtotal);
            }
            else{
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

    function allinvoiceexceldownloadprocess(Request $request) {
        $template_name = 'resources/assets/uploadandtemplates/templates/invoicenew_alldetail.xls';
            $tempname = "Invoice";
            $excel_name=$tempname;
        Excel::load($template_name, function($objTpl) use($request) {
            $inv_split = explode(",", $request->hdn_invoice_arr);

            $no_of_lines_to_copy = 51;
            $merge_array = array('51','52','53','55','56','61','63','64','66','67','69','89','90','91','92','96','97','98','99','100');
            $border_array = array('13','19','38');
            $cellval_1 = 69;
            foreach ($inv_split as $key_id => $value_id) {
                $est_split[$key_id] = explode("#", $value_id);

                $request->plimit = 1000;
                $getinvoicedetails = Invoice::fngetinvoicedetails($request,$est_split[$key_id][0]);
                
                if ($key_id != 0) {
                    self::copyPhpExcelWorkSheetRows($objTpl->getActiveSheet(),1,$no_of_lines_to_copy,50,35);
                    $no_of_lines_to_copy += 50;

                    // For Date
                    $objTpl->getActiveSheet()->mergeCells('AB'.$merge_array[0].':AD'.$merge_array[0]);
                    $objTpl->getActiveSheet()->mergeCells('AE'.$merge_array[0].':AI'.$merge_array[0]);
                    //For Header
                    $objTpl->getActiveSheet()->mergeCells('B'.$merge_array[1].':AI'.$merge_array[1]);
                    //For Invoice ID
                    $objTpl->getActiveSheet()->mergeCells('AB'.$merge_array[2].':AD'.$merge_array[2]);
                    $objTpl->getActiveSheet()->mergeCells('AE'.$merge_array[2].':AI'.$merge_array[2]);
                    // For Address
                    $objTpl->getActiveSheet()->mergeCells('W'.$merge_array[4].':AH'.$merge_array[5]);
                    // For Customer Name
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[3].':U'.$merge_array[3]);
                    // For Price
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[7].':G'.$merge_array[8]);
                    $objTpl->getActiveSheet()->mergeCells('H'.$merge_array[7].':O'.$merge_array[8]);
                    //For 承認者
                    $objTpl->getActiveSheet()->mergeCells('W'.$merge_array[6].':Y'.$merge_array[6]);
                    $objTpl->getActiveSheet()->mergeCells('W'.$merge_array[7].':Y'.$merge_array[9]);
                    // For Table Header
                    $objTpl->getActiveSheet()->mergeCells('B'.$merge_array[10].':P'.$merge_array[10]);
                    $objTpl->getActiveSheet()->mergeCells('R'.$merge_array[10].':T'.$merge_array[10]);
                    $objTpl->getActiveSheet()->mergeCells('U'.$merge_array[10].':W'.$merge_array[10]);
                    $objTpl->getActiveSheet()->mergeCells('X'.$merge_array[10].':AB'.$merge_array[10]);
                    $objTpl->getActiveSheet()->mergeCells('AC'.$merge_array[10].':AI'.$merge_array[10]);
                    // For Bank Details
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[11].':F'.$merge_array[11]);
                    $objTpl->getActiveSheet()->mergeCells('H'.$merge_array[11].':T'.$merge_array[11]);
                    $objTpl->getActiveSheet()->mergeCells('U'.$merge_array[11].':W'.$merge_array[11]);
                    $objTpl->getActiveSheet()->mergeCells('X'.$merge_array[11].':AB'.$merge_array[11]);
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[12].':F'.$merge_array[12]);
                    $objTpl->getActiveSheet()->mergeCells('H'.$merge_array[12].':J'.$merge_array[12]);
                    $objTpl->getActiveSheet()->mergeCells('K'.$merge_array[12].':T'.$merge_array[12]);
                    $objTpl->getActiveSheet()->mergeCells('U'.$merge_array[12].':W'.$merge_array[12]);
                    $objTpl->getActiveSheet()->mergeCells('X'.$merge_array[12].':AB'.$merge_array[12]);
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[13].':F'.$merge_array[13]);
                    $objTpl->getActiveSheet()->mergeCells('H'.$merge_array[13].':T'.$merge_array[13]);
                    $objTpl->getActiveSheet()->mergeCells('U'.$merge_array[13].':W'.$merge_array[13]);
                    $objTpl->getActiveSheet()->mergeCells('X'.$merge_array[13].':AB'.$merge_array[13]);
                    $objTpl->getActiveSheet()->mergeCells('C'.$merge_array[14].':F'.$merge_array[14]);
                    $objTpl->getActiveSheet()->mergeCells('H'.$merge_array[14].':T'.$merge_array[14]);
                    $objTpl->getActiveSheet()->mergeCells('U'.$merge_array[14].':W'.$merge_array[14]);
                    $objTpl->getActiveSheet()->mergeCells('X'.$merge_array[14].':AB'.$merge_array[14]);
                    // For Remarks
                    $objTpl->getActiveSheet()->mergeCells('E'.$merge_array[15].':AB'.$merge_array[15]);
                    $objTpl->getActiveSheet()->mergeCells('E'.$merge_array[16].':AB'.$merge_array[16]);
                    $objTpl->getActiveSheet()->mergeCells('E'.$merge_array[17].':AB'.$merge_array[17]);
                    $objTpl->getActiveSheet()->mergeCells('E'.$merge_array[18].':AB'.$merge_array[18]);
                    $objTpl->getActiveSheet()->mergeCells('E'.$merge_array[19].':AB'.$merge_array[19]);


                    // Inside Loop Merge
                    for ($l=1; $l<=19 ; $l++) {
                        $objTpl->getActiveSheet()->mergeCells('C'.($cellval_1 + $l).':P'.($cellval_1 + $l));
                        $objTpl->getActiveSheet()->mergeCells('R'.($cellval_1 + $l).':T'.($cellval_1 + $l));
                        $objTpl->getActiveSheet()->mergeCells('U'.($cellval_1 + $l).':W'.($cellval_1 + $l));
                        $objTpl->getActiveSheet()->mergeCells('X'.($cellval_1 + $l).':AB'.($cellval_1 + $l));
                        $objTpl->getActiveSheet()->mergeCells('AC'.($cellval_1 + $l).':AI'.($cellval_1 + $l));
                    }

                    $objTpl->getActiveSheet()->getStyle("AC".$border_array[1].":AC".$border_array[2])->applyFromArray(
                            array(
                                'borders' => array(
                                    'right' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        )
                    );
                    $objTpl->getActiveSheet()->getStyle('W'.$border_array[0])->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $border_array[0] += 50;
                    $border_array[1] += 50;
                    $border_array[2] += 50;
                    $merge_array[0] += 50;
                    $merge_array[1] += 50;
                    $merge_array[2] += 50;
                    $merge_array[3] += 50;
                    $merge_array[4] += 50;
                    $merge_array[5] += 50;
                    $merge_array[6] += 50;
                    $merge_array[7] += 50;
                    $merge_array[8] += 50;
                    $merge_array[9] += 50;
                    $merge_array[10] += 50;
                    $merge_array[11] += 50;
                    $merge_array[12] += 50;
                    $merge_array[13] += 50;
                    $merge_array[14] += 50;
                    $merge_array[15] += 50;
                    $merge_array[16] += 50;
                    $merge_array[17] += 50;
                    $merge_array[18] += 50;
                    $merge_array[19] += 50;
                    $cellval_1 += 50;
                }
            }

        $header_array = array('1','3','5','14','39','40','41','42');
        $cellval = 19;
        $cellval_notice = 45;
        
        foreach ($inv_split as $key_id => $value_id) {
            $est_split[$key_id] = explode("#", $value_id);

            $request->plimit = 1000;
            $getinvoicedetails = Invoice::fngetinvoicedetails($request,$est_split[$key_id][0]);
            $getestimatedetails = Invoice::fngetestimatedetails($request,$est_split[$key_id][1]);
            $get_customer_detail = Invoice::fnGetCustomerDetail($getinvoicedetails[0]->trading_destination_selection);
            $bankid=$getinvoicedetails[0]->bankid;
            $branchid=$getinvoicedetails[0]->bankbranchid;
            $acc_no=$getinvoicedetails[0]->acc_no;
            $acc_details = Invoice::fnGetAccounts($bankid,$branchid,$acc_no);
            $gettaxquery = Invoice::fnGetTaxDetails($getinvoicedetails[0]->quot_date);
                if (!empty($acc_details)) {
                    if ($acc_details[0]->Type == 1) {
                        $type = "普通";
                    } else if ($acc_details[0]->Type == 2) {
                        $type = "Other";
                    } else {
                        $type = $acc_details[0]->Type;
                    }
                } else {
                        $type="";
                }
            $grandtotal = "";
            if (!empty($getinvoicedetails[0]->totalval)) {
                if ($getinvoicedetails[0]->tax != 2) {
                    $totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
                    $dispval = (($totroundval * intval($gettaxquery[0]->Tax))/100);
                    $grandtotal = $totroundval + $dispval;
                } else {
                    $totroundval = preg_replace("/,/", "", $getinvoicedetails[0]->totalval);
                    $dispval = 0;
                    $grandtotal = $totroundval + $dispval;
                }
            }
            if($grandtotal =="") {
                $grandtotal = '0';
                $dispval = 0;
                $getinvoicedetails[0]->totalval='0';
            }
            $objTpl->setActiveSheetIndex();
            $objTpl->setActiveSheetIndex(0);  //set first sheet as active
            $objTpl->getActiveSheet()->setCellValue('AE'.$header_array[0], $getinvoicedetails[0]->quot_date);
            $objTpl->getActiveSheet()->setCellValue('C'.$header_array[2], $getinvoicedetails[0]->company_name."  御中");
            $objTpl->getActiveSheet()->getStyle('H'.$header_array[3])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objTpl->getActiveSheet()->setCellValue('H'.$header_array[3],'¥ '. number_format($grandtotal).'-');
            $objTpl->getActiveSheet()->getStyle('H'.$header_array[3])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objTpl->getActiveSheet()->setCellValue('X'.$header_array[4], $getinvoicedetails[0]->totalval);
            $objTpl->getActiveSheet()->setCellValue('X'.$header_array[5], number_format($dispval));
            $objTpl->getActiveSheet()->setCellValue('X'.$header_array[7], number_format($grandtotal));
            if ($getinvoicedetails[0]->tax == 1) {
                $objTpl->getActiveSheet()->setCellValue('U'.$header_array[6], "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X'.$header_array[6], number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U'.$header_array[7], "");
                $objTpl->getActiveSheet()->setCellValue('X'.$header_array[7], "");
            } 

            if ($getinvoicedetails[0]->tax == 2) {
                $objTpl->getActiveSheet()->setCellValue('U'.$header_array[5], "非課税");
                $objTpl->getActiveSheet()->setCellValue('X'.$header_array[5], "0");
                $objTpl->getActiveSheet()->setCellValue('U'.$header_array[6], "税込合計");
                $objTpl->getActiveSheet()->setCellValue('X'.$header_array[6], number_format($grandtotal));
                $objTpl->getActiveSheet()->setCellValue('U'.$header_array[7], "");
                $objTpl->getActiveSheet()->setCellValue('X'.$header_array[7], "");
            }
            $na=(isset($get_customer_detail[0]->customer_name)?$get_customer_detail[0]->customer_name:"")."\r\n".(isset($get_customer_detail[0]->customer_address)?$get_customer_detail[0]->customer_address:"")."\r\n".(isset($get_customer_detail[0]->customer_contact_no)?$get_customer_detail[0]->customer_contact_no:"");
            $objTpl->getActiveSheet()->setCellValue('H'.$header_array[4], (isset($acc_details[0]->bankname)?$acc_details[0]->bankname:""));
            $objTpl->getActiveSheet()->setCellValue('H'.$header_array[5], $type);
            $objTpl->getActiveSheet()->setCellValue('H'.$header_array[6], (isset($acc_details[0]->bankbranch)?$acc_details[0]->bankbranch:""));
            $objTpl->getActiveSheet()->setCellValue('H'.$header_array[7], (isset($acc_details[0]->FirstName)?$acc_details[0]->FirstName:""));
            $objTpl->getActiveSheet()->setCellValue('K'.$header_array[5], (isset($acc_details[0]->AccNo)?$acc_details[0]->AccNo:""));
            $objTpl->getActiveSheet()->setCellValue('AE'.$header_array[1], (isset($getinvoicedetails[0]->user_id)?$getinvoicedetails[0]->user_id:""));
            
            $k = 1;
            $setcolor=38;
            // print_r($getinvoicedetails);exit();
            foreach ($getinvoicedetails as $key=> $value) {
                $workloop= $value->work_specific;
                $quantityloop =$value->quantity;
                $unit_priceloop=$value->unit_price;
                $amountloop=$value->amount;
                $remarksloop=$value->remarks;
                $colorarray=array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'C0C0C0')
                                )
                            );
                
                if ($k>19) {
                    /*$objTpl->getActiveSheet()->insertNewRowBefore(($cellval + $k), 1);
                    $setcolor++;
                    if (($k%2)!=0) {
                        $objTpl->getActiveSheet()->getStyle('B'.$setcolor.':AC'.$setcolor)->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'FFFFFF')
                                )
                            )
                          
                        );
                    } else {
                        $objTpl->getActiveSheet()->getStyle('B'.$setcolor.':AC'.$setcolor)->applyFromArray(
                             $colorarray
                        );
                    }
                    $objTpl->getActiveSheet()->getRowDimension('1')->setRowHeight(24.75);
                    if ($cellval + $k == '39') {
                        $objTpl->getActiveSheet()->getStyle('B' . ('38') . ':AI' . ('38'))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
                    }
                    $objTpl->getActiveSheet()->getStyle('B' . ($cellval + $k) . ':AI' . ($cellval + $k))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
                    $objTpl->getActiveSheet()->mergeCells('C' . ($cellval + $k) . ':P' . ($cellval + $k));
                    $objTpl->getActiveSheet()->mergeCells('R' . ($cellval + $k) . ':T' . ($cellval + $k));
                    $objTpl->getActiveSheet()->mergeCells('U' . ($cellval + $k) . ':W' . ($cellval + $k));
                    $objTpl->getActiveSheet()->mergeCells('X' . ($cellval + $k) . ':AB' . ($cellval + $k));
                    $objTpl->getActiveSheet()->mergeCells('AC' . ($cellval + $k) . ':AI' . ($cellval + $k));*/
                }
                $objTpl->getActiveSheet()->setCellValue('C' . ($cellval + $k),$workloop);

                $dotOccur = strpos($quantityloop, ".");
                if( ($quantityloop) != "" ){
                    if ($dotOccur) {
                        $quantityValue = "\0" . $quantityloop;
                    } else {
                        $quantityValue = "\0" .$quantityloop . ".0";
                    }
                } else {
                    $quantityValue = "";
                }
                $objTpl->getActiveSheet()->setCellValue('R' . ($cellval + $k), $quantityValue);
                if (!empty($unit_priceloop)) {
                    if ($unit_priceloop < 0) {
                        $objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k),$unit_priceloop)->getStyle('U' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
                    }
                    $objTpl->getActiveSheet()->setCellValue('U' . ($cellval + $k), $unit_priceloop);
                }
                if (!empty($amountloop)) {
                    if ($amountloop < 0) {
                        $objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k),$amountloop)->getStyle('X' . ($cellval + $k))->getFont()->getColor()->setRGB('FF0000');
                    }
                    $objTpl->getActiveSheet()->setCellValue('X' . ($cellval + $k), $amountloop);
                }
                $objTpl->getActiveSheet()->setCellValue('AC' . ($cellval + $k), $remarksloop);
                $k++;
            }
            $arrval = array();
            for ($i = 1; $i <= 5; $i++) {
                $special_ins = "special_ins".$i;
                if($getinvoicedetails[0]->$special_ins != "") {
                    array_push($arrval, $value->$special_ins);
                }
            }
            
            for ($rccnt=0; $rccnt < count($arrval); $rccnt++) { 
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), $arrval[$rccnt]);
            }
            if(count($arrval) == 1) {
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt), $rccnt . ")");
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            } else if(count($arrval) == 2) {
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-1), $rccnt-1 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt), $rccnt . ")");
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            } else if(count($arrval) == 3) {
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-2), $rccnt-2 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-1), $rccnt-1 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt), $rccnt . ")");
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));


            } else if(count($arrval) == 4) {
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-3), $rccnt-3 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-2), $rccnt-2 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-1), $rccnt-1 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt), $rccnt . ")");
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));

            } else if(count($arrval) == 5) {
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-4), $rccnt-4 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-3), $rccnt-3 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-2), $rccnt-2 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt-1), $rccnt-1 . ")");
                $objTpl->getActiveSheet()->setCellValue('D' . ($cellval_notice + $rccnt), $rccnt . ")");
                $objTpl->getActiveSheet()->setCellValue('E' . ($cellval_notice + $rccnt+1), (isset($arrval[$rccnt])?$arrval[$rccnt]:""));
            } else {
                $objTpl->getActiveSheet()->setCellValue('D'.$cellval_notice, "");

            }
            $header_array[0] += 50;
            $header_array[1] += 50;
            $header_array[2] += 50;
            $header_array[3] += 50;
            $header_array[4] += 50;
            $header_array[5] += 50;
            $header_array[6] += 50;
            $header_array[7] += 50;
            $cellval += 50;
            $cellval_notice += 50;
        }
            /*if (($cellval + $k)>38) {
                $objTpl->getActiveSheet()->getStyle('B' . ($cellval + ($k-1)) . ':AI' . ($cellval + ($k-1)))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
            $cellval = 45;
            // Rajaguru Update
            if ($k>19) {
                $cellval = $k+25;
            }
            else{
                $cellval = 45;
            }
            if ($k>19) {
                $cellval=19;
                $objTpl->getActiveSheet()->getStyle('AC19'  . ':AC' . ($cellval + ($k-1)))->applyFromArray(
                    array(
                        'borders' => array(
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                        )
                    )
                    );
                
            }
            
            $objTpl->getActiveSheet()->getStyle('W13')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            */

            $border_array = array('13','19','38','14');
            $print_area1 = 1;
            $print_area2 = 50;
            $i = 1;
            foreach ($inv_split as $key_id => $value_id) {
                $objTpl->getActiveSheet()->getStyle("AC".$border_array[1].":AC".$border_array[2])->applyFromArray(
                        array(
                            'borders' => array(
                                'right' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    )
                );
                $objTpl->getActiveSheet()->getStyle('W'.$border_array[0])->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


            $objTpl->getActiveSheet()->setBreak('A'.$print_area2, 1)->getPageSetup()->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0)->setPrintArea('A'.$print_area1.':AI'.$print_area2,$i,'I');
            $objTpl->getActiveSheet()->getStyle('C'.$border_array[3])->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objTpl->getActiveSheet()->getStyle('H'.$border_array[3])->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $border_array[0] += 50;
                $border_array[1] += 50;
                $border_array[2] += 50;
                $border_array[3] += 50;
                $print_area1 += 50;
                $print_area2 += 50;
                $i++;
            }

            $objTpl->getActiveSheet()->setTitle('請求書');
            $sheet1 = $objTpl->getActiveSheet()->copy();
            $sheet2 = clone $sheet1;
            $sheet_title = '請求書(控)';
            $sheet2->setTitle($sheet_title);
            $objTpl->addSheet($sheet2);
            $header_cell_lbl = 2;
            foreach ($inv_split as $key_id => $value_id) {
                $sheet2->setCellValue('B'.$header_cell_lbl, "請求書(控)");
                $header_cell_lbl += 50;
            }
            unset($sheet2);
            unset($sheet1);
            $objTpl->getActiveSheet(0)->setSelectedCells('B1');
            $objTpl->getActiveSheet(0)->setSelectedCells('A1');


            $flpath='.xls';/*
            ob_end_clean();*/
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$flpath.'"');
            header('Cache-Control: max-age=0');
        })->setFilename($excel_name)->download('xls');
    }
    /**
     * [PHPExcel] Copy the specified line of the sheet completely
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int $srcRow Copy source row
     * @param int $dstRow Destination row
     * @param int $height Number of lines to copy
     * @param int $width Number of columns to copy
     */
    function copyPhpExcelWorkSheetRows ($sheet, $srcRow, $dstRow, $height, $width) {
        for ($row = 0;$row<$height;$row ++) {
            // cell format and value replication
            for ($col = 0;$col<$width;$col ++) {
                $cell = $sheet->getCellByColumnAndRow ($col, $srcRow + $row);
                $style = $sheet->getStyleByColumnAndRow ($col, $srcRow + $row);
                $dstCell = PHPExcel_Cell :: stringFromColumnIndex ($col). (string) ($dstRow + $row);
                $sheet->setCellValue ($dstCell, $cell->getValue ());
                $sheet->duplicateStyle ($style, $dstCell);
            }
            // Duplicate row height.
            $h = $sheet->getRowDimension ($srcRow + $row)->getRowHeight ();
            $sheet->getRowDimension ($dstRow + $row)->setRowHeight ($h);
        }
        // duplicate cell merge
        //-$mergeCell = "AB12: AC15" Restored by adding lines only to those in the replication range.
        //-$merge = "AB16: AC19"
        foreach ($sheet->getMergeCells () as $mergeCell) {
            $mc = explode (":", $mergeCell);
            $col_s = preg_replace ("/ [0-9] * /", "", $mc [0]);
            $col_e = preg_replace ("/ [0-9] * /", "", $mc [1]);
            $row_s = ((int) preg_replace ("/ [A-Z] * /", "", $mc [0]))-$srcRow;
            $row_e = ((int) preg_replace ("/ [A-Z] * /", "", $mc [1]))-$srcRow;
            // If it is a line range to copy to.
            if (0<= $row_s&&$row_s<$height) {
                $merge = $col_s. (string) ($dstRow + $row_s). ":". $col_e. (string) ($dstRow + $row_e);
                $sheet->mergeCells ($merge);
            }
        }
    }
}