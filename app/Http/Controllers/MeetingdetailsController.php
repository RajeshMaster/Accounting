<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Common; 
use App\Model\MeetingDetails;
use App\Model\User; 
use DB;
use Input;
use Redirect;
use Config;
use Session;
use Illuminate\Support\Facades\Validator;
class MeetingdetailsController extends Controller {
	function index(Request $request) {
	//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if (empty($request->selMonth)) {
			$date_month = date('Y-m');
		} else {
			$date_month = $request->selYear."-". $request->selMonth;
		}
		$last1 = $date_month;
		$lastdate = explode('-', $last1);
		$lastyear =$lastdate[0];
		$lastmonth =$lastdate[1];
		//Get the Account Details
		$g_accountperiod = MeetingDetails::fnGetAccountPeriodBK($request);
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
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
		$year_month = array();
			if ($account_close_mn == 12) {
				for ($i = 1; $i <= 12; $i++) {
					$year_month[$current_year][$i] = substr("0" . $i, -2);
				}
			} else {
				for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
					$year_month[$last_year][$i] = substr("0" . $i, -2);
				}

				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$current_year][$i] = substr("0" . $i, -2);
				}
			}
		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
	//Get the Maximum Date of the Month
		$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . 
					 substr("0" . $maxday, -2);
		$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
	//Get the Meeting Date 
		$exp_query = MeetingDetails::fnGetmeetRecord($from_date, $to_date);
		$dbrecord = array();
		$dbrecord = $exp_query;
		$prev_record = MeetingDetails::fnGetmeetRecordPrevious($from_date);
		$dbprevious = array();
		if (!empty($prev_record)) {
			$dbprevious = $prev_record;
		}
		$next_record = MeetingDetails::fnGetmeetRecordNext($to_date);
		$dbnext = array();
		if (!empty($next_record)) {
			$dbnext = $next_record;
		}
		$dbrecord = array_unique($dbrecord);
		$db_year_month = array();
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-", $dbrecordvalue);
			 $db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		} 
		$split_date = explode("-", $date_month);
		//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH
		$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, 
												$account_period);
		$details = MeetingDetails::selectmeetingdetails($lastyear,$lastmonth,$request);
		return view('Meetingdetils.index',['details' => $details,
										   'account_period' => $account_period,
										   'year_month' => $year_month,
										   'db_year_month' => $db_year_month,
										   'date_month' => $date_month,
										   'dbnext' => $dbnext,
										   'dbprevious' => $dbprevious,
										   'last_year' => $last_year,
										   'current_year' => $current_year,
										   'account_val' => $account_val,
										   'request' => $request]);
	}
	function view(Request $request) {
		//To View The Single User
		$viewdt=MeetingDetails::selectBymeetingview($request);
		if(!isset($request->viewid)){
	         return $this->index($request);
	    }
		return view('Meetingdetils.view',['request' => $request,
										  'viewdt' => $viewdt]);
	}
	function meetingaddedit(Request $request){
		if(!isset($request->editflg)){
	         return $this->index($request);
	    }
		if ($request->editflg == 'add') {
			//Select the Customer Name
			$details=MeetingDetails::selectcustomer();
			$customerarray = array();
			foreach ($details as $key => $value) {
				$customerarray[$value->customer_id] = $value->customer_name;
			}
			return view('Meetingdetils.addedit',['request' => $request,
												  'customerarray' => $customerarray]);
		} else {
			$details=MeetingDetails::selectcustomer();
			$customerarray = array();
			foreach ($details as $key => $value) {
				$customerarray[$value->customer_id] = $value->customer_name;
			}
			$viewdetails = MeetingDetails::viewdetails($request);
			return view('Meetingdetils.addedit',['request' => $request,
												'customerarray' => $customerarray,
												'viewdetails' => $viewdetails]);
		}
	}
	function branch_ajax(Request $request){
		$customerid = $request->getbankval;
		$get_sub_query = MeetingDetails::fnGetbranchName($customerid);
		$brancharray=json_encode($get_sub_query);
		echo $brancharray;
		exit();
	}
	function addeditprocess(Request $request){
		if($request->editflg =="edit") {
			$update = MeetingDetails::UpdateMeetingDetails($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$resignid = "";
			$request->viewid = MeetingDetails::getautoincrement();
			$insert = MeetingDetails::addeditprocess($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} 
		$request->selYear = substr($request->date, 0, 4);
		$request->selMonth = substr($request->date, 5, 2);?>
		<form name="frmedit" id="frmedit"  action="../MeetingDetails/view?mainmenu=<?php echo $request->mainmenu;?>&time=<?php echo date('YmdHis'); ?>" method="post">
			<input type = "hidden" id = "viewid" name = "viewid" value="<?php echo $request->viewid; ?>">
			<input type = "hidden" id = "selYear" name = "selYear" value="<?php echo $request->selYear; ?>">
			<input type = "hidden" id = "selMonth" name = "selMonth" value="<?php echo $request->selMonth; ?>">
		</form>
		<script type="text/javascript">
			document.forms['frmedit'].submit();
		</script>
	<?php }
	function meetinghistory(Request $request){
	// Get the history of the Particular Customer Name	
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$customerName = $request->customer_name;
		$historydetails = MeetingDetails::gethistory($request);
		if(!isset($request->customer_name)){
	         return $this->index($request);
	    }
		return view('Meetingdetils.meetinghistory',['request' => $request,
													 'customerName' => $customerName,
										   			'historydetails' => $historydetails]);
	}
	function newcustomerpopup(Request $request){
		return view('Meetingdetils.meetingnewreg',['request' => $request]);
	}
	function newcustomerregpopup(Request $request){
		$customerarray = array();
		$cusno = MeetingDetails::customerno($request);
		if($cusno == "") {
			$cus = "CST00001";
		} else {
			$customer = substr($cusno[0]->customer_id, 3,5);
			$cus = (int)$customer + 100;
			$cus = str_pad($cus,5,"0",STR_PAD_LEFT);
			$cus = "CST" . $cus;
		}
		$customer = substr($cus, 3,5);
		$cus4 = $customer+1;
		$cus5 = str_pad($cus4,5,"0",STR_PAD_LEFT);
	    $branchid = "CST" . $cus5;
		$custlastinsertid = MeetingDetails::insert($request, $cus);
		$custlastinsertdetails = MeetingDetails::getLastInsertedCusdetails($custlastinsertid);
		$customerarray[$custlastinsertdetails[0]->customer_id] = $custlastinsertdetails[0]->customer_name;
		$branchlastinsertid = MeetingDetails::branchinsertnew($request, $cus, $branchid);
		$getsunsubject=json_encode($customerarray);
		echo $getsunsubject;exit;
	}
	function cust_name_exist(Request $request){
	//Check the customer Name is already exists
		$customer_name = $_REQUEST['customer_name'];
		$custNameExist = MeetingDetails::fnGetcustnamecheck($customer_name);
		$countcust = count($custNameExist);
		print_r($countcust); exit();
	}
	function getmettingtiming(Request $request){
		$meetingtimeExist = MeetingDetails::fnGetmeetingtimeingcheck($request);
		$meetingtimeExist = count($meetingtimeExist);
		print_r($meetingtimeExist);exit();
	}
 }