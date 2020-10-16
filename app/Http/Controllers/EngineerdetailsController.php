<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Engineerdetails;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\Payment;
use App\Model\Expenses;
use App\Http\Common;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
class EngineerdetailsController extends Controller {

	function index(Request $request) {
		//sorting
		if ($request->engineerdetailssort == "") {
        	$request->engineerdetailssort = "InvoiceNo";
      	}
		if (empty($request->sortOrder)) {
        	$request->sortOrder = "DESC";
      	}
      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}

   		if (!empty($request->singlesearch) || $request->searchmethod == 2) {
          $sortMargin = "margin-right:230px;";
        } else {
          $sortMargin = "margin-right:0px;";
        }
		$engineerdetailssortarray = array("InvoiceNo"=>trans('messages.lbl_invoiceno'),
									"EMPID"=>trans('messages.lbl_employeeid'),
									"FirstName"=>trans('messages.lbl_empName')
									);
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		 $startdate = $request->startdate;
		 $enddate = $request->enddate;
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

		$est_query = Engineerdetails::fnGetEstimateRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->quot_date;
		}

		$est_query1 = Engineerdetails::fnGetEstimateRecordPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->quot_date, 0, 4);
			$dbprevious[]=$value->quot_date;
			$pre++;
		}

		$est_query2 = Engineerdetails::fnGetEstimateRecordNext($to_date);
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
	
		if($request->selYear=="") {
			$start = new Carbon\Carbon('first day of last month');
			$request->selYear=$start->format('Y');
			$request->selMonth=$start->format('m');
		}
		if (isset($request->date_month)) {
			$date_month = $request->date_month;
		} else {
			$date_month=$request->selYear."-".$request->selMonth;
		}
		$get_view=array();
	
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
		if($dbprevious == "" || $dbnext == "" || $db_year_month == "" || $year_month == "") {
			$dbnext = array();
			$dbprevious = array();
		}
		$grandtotal=0;
		$y=0;
		$divtotal=0;
		$balance=0;
		$paid_amount=0;
		$invoicedata=0;
		$engineerdet = Engineerdetails::fnGetEngineerdetails($request,$date_month,1);
		$engineerdets = Engineerdetails::fnGetEngineerdetails($request,$date_month,2);
		foreach ($engineerdets as $key => $value) {
			$grandtotal += preg_replace('/,/', '', $value->amount);
		}
		//print_r($grandtotal);
		return view('Engineerdetails.index',['account_period' => $account_period,
											'year_month' => $year_month,
											'db_year_month' => $db_year_month,
											'date_month' => $date_month,
											'dbnext' => $dbnext,
											'grandtotal' =>$grandtotal,
											'balance' => $balance,
											'paid_amount'=>$paid_amount,
											'divtotal'=>$divtotal,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' =>  $account_val,
											'engineerdet' => $engineerdet,
											'engineerdetailssortarray' => $engineerdetailssortarray,
											'sortMargin' => $sortMargin,
											'invoicedata'=>$invoicedata,
											'request' => $request]);
	}
	function expenseindex(Request $request) {
		$db_year_month = array();
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if ($request->selMonth == "") {
			$request->selMonth = date('m');
		}
		if ($request->selYear == "") {
			$request->selYear = date('Y');
		}
		//sorting
		if ($request->engineerdetailssort == "" || empty($request->engineerdetailssort)) {
			$request->engineerdetailssort = "date";
		}
		if (empty($request->sortOrder)) {
			$request->sortOrder = "DESC";
		}
		if ($request->sortOrder == "asc") {  
			$request->sortstyle="sort_asc";
		} else {  
			$request->sortstyle="sort_desc";
		}
		if (!isset($request->selMonth)) {
			$date_month=date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
		$engineerdetailssortarray = array("date"=>trans('messages.lbl_date'),
									"LastName"=>trans('messages.lbl_empName'));
		$g_query = array();
		$last=date('Y-m', strtotime('last month'));
		$last1=date($date_month , strtotime($last . " last month"));
		$lastdate=explode("-",$last1);
		$lastyear=$lastdate[0];
		$lastmonth=$lastdate[1];
		$g_accountperiod=Expenses::fnGetAccountPeriodexp($request);
		$account_close_yr=$g_accountperiod[0]->Closingyear;
		$account_close_mn=$g_accountperiod[0]->Closingmonth;
		$account_period=intval($g_accountperiod[0]->Accountperiod);
		if (!empty($request->previou_next_year)) {
			$splityear = explode("-",$request->previou_next_year);
			if (isset($splityear)) {
			if (intval($splityear[1]) > $account_close_mn) {
				$last_year = intval($splityear[0]);
				$current_year = intval($splityear[0]) + 1;
			} else {
				$last_year = intval($splityear[0]) - 1;
				$current_year = intval($splityear[0]);
			}
			}
		} else if (isset($request->selYear)) {
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
		$current_month=date('m');
		$year_month=array();
		if ($account_close_mn == 12) {
			for ($i = 1; $i <= 12; $i++) {
				$year_month[$current_year][$i] = $i;
			} 
		} else {
			for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
				$year_month[$last_year][$i] = $i;
			}
			for ($i = 1; $i <= $account_close_mn; $i++) {
				$year_month[$current_year][$i] = $i;
			}
		}
		$year_month_day=$current_year . "-" . $account_close_mn . "-01";
		$maxday=Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date=$last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date=$current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
		$est_query=Engineerdetails::fnGetExpenseRecord($request,$from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->date;
		}
		$est_query1=Engineerdetails::fnGetExpenseRecordPrevious($request,$from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->date, 0, 4);
			$dbprevious[]=$value->date;
			$pre++;
		}
		$est_query2=Engineerdetails::fnGetExpenseRecordNext($request,$to_date);
		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->date;
		}
		$dbrecord = array_unique($dbrecord);
		//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH
		$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		$total = 0;
		$g_query=Engineerdetails::fngetexpensedetail($lastyear,$lastmonth,$request,1);
		$g_query1=Engineerdetails::fngetexpensedetail($lastyear,$lastmonth,$request,2);

		foreach ($g_query as $key => $value) {
			$total += $value->amount;
		}
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-",$dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}
		return view('Engineerdetails.expenseindex',[
											'account_period' => $account_period,
											'year_month' => $year_month,
											'db_year_month' => $db_year_month,
											'date_month' => $date_month,
											'dbnext' => $dbnext,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' => $account_val,
											'g_query' => $g_query,
											'g_query1' => $g_query1,
											'total' => $total,
											'engineerdetailssortarray' => $engineerdetailssortarray,
											'request' => $request]);
	}
}
