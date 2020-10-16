<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Billing;
use App\Http\Common;
use DB;
use Input;
use Redirect;
use Session;
use Illuminate\Support\Facades\Validator;
class BillingController extends Controller {
	function index(Request $request) { 
		$year_month = array();
		$db_year_month = array();
	    $dbrecord = array();
	    $dbnext = array();
		$dbprevious = array();
		$account_val ="";
		$displayArray = array();
		$get_pre_value = "";
		if(Session::get('year') !="" && Session::get('month') !="") {
        $request->selYear = Session::get('year');
        $request->selMonth = Session::get('month');
      	}
		//SORT POSITION
        $sortMargin = "margin-right:0px;";
        //SORTING PROCESS
		if (empty($request->billsort)) {
    		$request->sortOrder = "ASC";
    		$request->billsort = "";
  		}
  		if ($request->sortOrder == "asc") {  
  			$request->sortstyle="sort_asc";
  		} else {
  			$request->sortstyle="sort_desc";
  		}
  		// PAGINATION
        if (empty($request->plimit)) {
			$request->plimit = Common::DEF_PAGE_COUNT;
		} else {
			$request->plimit = $request->plimit;
		}
        if (!isset($request->selMonth) || empty($request->selMonth)) {
			$date_month = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
			$previousYrMn = date('Y-m-d', strtotime(date('Y-m')." -1 month"));
			$request->selYear = date("Y", strtotime($previousYrMn));
			$request->selMonth = date("m", strtotime($previousYrMn));
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
		$array = array("Emp_Id"=>trans('messages.lbl_empid'),
						"nickname"=>trans('messages.lbl_empName'),
						"customer_name"=>trans('messages.lbl_client'),
						"tcheckcalc"=>trans('messages.lbl_status')
						);
		//PREVIOUS RECORD GET IN CURRENT MONTH 
		$temp_current_count = Billing::getTempcurrecDetails($request,$date_month);
		if ($temp_current_count != 0) {
			$get_pre_value =1;
		}
		$e_accountperiod = Billing::fnGetAccountPeriod();
		$account_close_yr = $e_accountperiod[0]->Closingyear;
		$account_close_mn = $e_accountperiod[0]->Closingmonth;
		$account_period = intval($e_accountperiod[0]->Accountperiod);
		$request->previou_next_year  = "";
		$splityear = explode('-', $request->previou_next_year);
		if ($request->previou_next_year != "") {
			if (intval($splityear[1]) > $account_period) {
				$last_year = intval($splityear[0]);
				$current_year = intval($splityear[0]) + 1;
			} else {
				$last_year = intval($splityear[0]) - 1;
				$current_year = intval($splityear[0]);
			}
		} else if ($request->selYear) {
			if ($request->selMonth > $account_period) {
				$current_year = intval($request->selYear) + 1;
				$last_year = intval($request->selYear);
			} else {
				$current_year = intval($request->selYear);
				$last_year = intval($request->selYear) - 1;
			}
		} else {
			$last_year = date('Y') - 1;
			$current_year = date('Y');
		}
		if ($account_period == 12) {
			for ($i = 1; $i <= 12; $i++) {
				$year_month[$current_year][$i] = $i;
			}
		} else {
			for ($i = ($account_period + 1); $i <= 12; $i++) {
				$year_month[$last_year][$i] = $i;
			}

			for ($i = 1; $i <= $account_period; $i++) {
				$year_month[$current_year][$i] = $i;
			}
		}
		$year_month_day = $current_year . "-" . $account_period . "-01";
		$maxday = date('t', strtotime($year_month_day));
		$from_date = $last_year . "-" . $account_period . "-" . substr("0" . $maxday, -2);
		$to_date = $current_year . "-" . ($account_period + 1) . "-01";
		$est_execute = Billing::fnGetmnthbillRecord($from_date, $to_date);
		foreach ($est_execute as $key => $value) {
	    	$res1= $value->date;
	    	array_push($dbrecord, $res1);
	    }
	    $cur_year_month = date('Y-m');
		array_push($dbrecord, $cur_year_month);
		// previous month to make a link
		$pre_month_link=date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
		array_push($dbrecord, $pre_month_link);
		//FUTURE TWO MONTH LINK
		$fut_1month_link=date("Y-m", strtotime("1 months", strtotime(date('Y-m-01'))));
		$fut_2month_link=date("Y-m", strtotime("2 months", strtotime(date('Y-m-01'))));
		array_push($dbrecord, $fut_1month_link,$fut_2month_link);
	    $est_execute1 = Billing::fnGetmnthbillRecordPrevious($from_date);
		foreach ($est_execute1 as $key => $value) {
	    	$res2 = $value->date;
	    	array_push($dbprevious, $res2);
	    }
	    $est_execute2 = Billing::fnGetmnthbillRecordNext($to_date);
		foreach ($est_execute2 as $key => $value) {
	    	$res3 = $value->date;
	    	array_push($dbnext, $res3);
	    }
	    if (count($dbrecord) > 0) {
			$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
			if (end($dbrecord) < $lastMonthAsLink) {
				array_push($dbrecord, $lastMonthAsLink);
			}
		}
	    $dbrecord = array_unique($dbrecord);
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-", $dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}
		if (empty($_REQUEST['account_val'])) {
			$account_val = self::getAccountPeriod($year_month, $account_close_yr, $account_close_mn,
				$account_period);
		} else {
			$account_val = $_REQUEST['account_val'];
		}
		$selYear = $request->selYear;
		$selMonth = $request->selMonth;
		$g_query = Billing::mnthbilldetails($request,$date_month);
		$j = 0;
		foreach ($g_query as $key => $value) {
			$displayArray[$j]['id'] = $value->id;
			$displayArray[$j]['Emp_ID'] = $value->Emp_ID;
			$displayArray[$j]['nickname'] = $value->nickname;
			$displayArray[$j]['LastName'] = $value->LastName;
			$displayArray[$j]['customer_name'] = $value->customer_name;
			$displayArray[$j]['start_date'] = $value->start_date;
			$displayArray[$j]['date'] = $value->date;
			$displayArray[$j]['Amount'] = $value->Amount;
			$displayArray[$j]['tcheckcalc'] = $value->tcheckcalc;
			$displayArray[$j]['customer_id'] = $value->customer_id;
			$displayArray[$j]['OTAmount'] = $value->OTAmount;
			$displayArray[$j]['TotalAmount'] = $value->TotalAmount;
			$displayArray[$j]['Calcdone'] = $value->tcheckcalc;
			$displayArray[$j]['monthlink'] = $value->monthlink;
			$displayArray[$j]['yearlink'] = $value->yearlink;
			if ( $value->customer_id == $value->branch_id ) {
			$displayArray[$j]['branch_name'] = $value->customer_name;
			} else {
			$displayArray[$j]['branch_name'] = $value->branch_name;
			}
			$displayArray[$j]['branch_id'] = $value->branch_id;
			$displayArray[$j]['MinHrs'] = $value->minhrs;
			$displayArray[$j]['MaxHrs'] = $value->maxhrs;
			$displayArray[$j]['TotalHrs'] = $value->timerange;
			$j++;
		}
		$totamt_sql = Billing::fntotalamtval($date_month);
		$tax_sal = Billing::getTaxDetails($date_month);
		return view('Billing.index',['array' => $array,
									'fut_1month_link' => $fut_1month_link,
									'fut_2month_link' => $fut_2month_link,
									'cur_year_month' => $cur_year_month,
									'last_year' => $last_year,
									'current_year' => $current_year,
									'year_month' => $year_month,
									'date_month' => $date_month,
									'dbprevious' => $dbprevious,
									'dbnext' => $dbnext,
									'db_year_month' => $db_year_month,
									'account_val' => $account_val,
									'account_period' => $account_period,
									'sortMargin' => $sortMargin,
									'displayArray' => $displayArray,
									'g_query' => $g_query,
									'get_pre_value' => $get_pre_value,
									'totamt_sql' => $totamt_sql,
									'tax_sal' => $tax_sal,
									'request' => $request]);
	}
	public static function getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period) {
		$arr_yr_mn = array_keys($year_month);
		if( $account_close_mn == 12 ) {
			$yr_mn = $arr_yr_mn[0];
		} else {
			$yr_mn = $arr_yr_mn[1];
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
		return $account_val;
	}
	function staffselectpopup(Request $request) {
		$employeeUnselect = Billing::getAllEmpDetails($request);
      	$employeeSelect = Billing::getAllFilteredEmpDetails($request);
		return view('Billing.staffselectpopup',['employeeUnselect' => $employeeUnselect,
                                            'employeeSelect' => $employeeSelect,
                                            'request' => $request]);
	}
	function empselectprocess(Request $request) {
	  $insert=Billing::InsertEmpFlrDetails($request);
      Session::flash('year', $request->year); 
      Session::flash('month', $request->month); 
      if($insert){
				Session::flash('success', 'Employees Selected Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Employees Selected Unsucessfully!!'); 
				Session::flash('type', 'alert-danger'); 
			}
      $request->selected = "";
      $request->removed = "";
      return Redirect::to('Billing/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function billhistory(Request $request) {
		$grndotamt = "";
		$grndtotamt = "";
		$displayArray = array();
		if(!isset($request->empid) && !isset($request->empname)){
	        return $this->index($request);
	    }
		// PAGINATION
        if (empty($request->plimit)) {
			$request->plimit = Common::DEF_PAGE_COUNT;
		} else {
			$request->plimit = $request->plimit;
		}
		$g_query = Billing::billing_history_data($request);
		$j = 0;
		foreach ($g_query as $key => $value) {
			$displayArray[$j]['date'] = $value->date;
			$displayArray[$j]['Empno'] = $value->Empno;
			$displayArray[$j]['customer_name'] = $value->customer_name;
			if( $value->customer_id == $value->branch_id){
				$displayArray[$j]['branch_name'] = $value->customer_name;
			}
			else{
				$displayArray[$j]['branch_name'] = $value->branch_name;
			}
			$displayArray[$j]['nickname'] = $value->nickname;
			$displayArray[$j]['totalhrs'] = $value->totalhrs;
			$displayArray[$j]['OTAmount'] = $value->OTAmount;
			$displayArray[$j]['TotalAmount'] = $value->TotalAmount;
			$j++;
		}
		$l=0;
		for ($i=0;$i<count($displayArray);$i++) {
			if($displayArray[$i]['totalhrs'] && $displayArray[$i]['TotalAmount']!=""){
				$grndotamt =$grndotamt+str_replace(",", "", $displayArray[$i]['OTAmount']);
				$grndtotamt =$grndtotamt+str_replace(",", "", $displayArray[$i]['TotalAmount']);
				$l++;
			}
		}
		return view('Billing.billhistory',['displayArray' => $displayArray,
											'g_query' => $g_query,
											'grndotamt' => $grndotamt,
											'grndtotamt' => $grndtotamt,
											'request' => $request]);
	}
	function billdetailview(Request $request) {
		if(Session::get('selMonth') !="" && Session::get('selYear') !="" && Session::get('hdnempid') !=""){
          $request->selYear = Session::get('selYear');
          $request->selMonth = Session::get('selMonth');
          $request->hdnempid = Session::get('hdnempid');
      }
		if (!isset($request->selMonth)) { 
			$date_month = date('Y-m', strtotime("last month"));
		} else { 
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
		$id = $request->hdnempid;
		if(!empty($request->upcheckval)) {
			$sql = Billing::billUpdChk($request);
			if($sql){
				Session::flash('success', 'Calculation Done Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Calculation Done Unsucessfully!!'); 
				Session::flash('type', 'alert-danger'); 
			}
      		$request->hdnempid = $request->hdnempidchk;
		} else {
			$request->hdnempid = $request->hdnempid;
		}
		$sqlquery = Billing::fngetempdetails($request,$date_month);
		if (empty($sqlquery)) {
			return $this->index($request);
		} else {
			if ( $sqlquery[0]->branch_id == $sqlquery[0]->Clientid ) {
				$sqlquery['branch_name'] = $sqlquery[0]->customer_name;
			} else {
				$sqlquery['branch_name'] = $sqlquery[0]->branch_name;
			}
		}
		if(!isset($request->hdnempid)){
	        return $this->index($request);
	    }
		return view('Billing.billdetailview',['result_query' => $sqlquery,
												'request' => $request]);
	}
	function addedit(Request $request) {
		$cust_id =array();
		$sqlquery =array();
		$row =array();
		$getmonthname=array();
		$getyearname=array();
		$getbranchname=array();
		if(!isset($request->hdnempid)){
	        return $this->index($request);
	    }
	    if (!isset($request->selMonth)) { 
			$date_month = date('Y-m', strtotime("last month"));
		} else { 
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
	    $request->hdnempid = $request->hdnempid;
	    if ($request->scrname == 'edit' || $request->scrname == 'copy') {
		$sqlquery = Billing::fngetempdetails($request,$date_month);
				$row['nickname'] = $sqlquery[0]->nickname;
				$row['LastName'] = $sqlquery[0]->LastName;
				$row['clientname'] = $sqlquery[0]->Clientid;
				$row['branchname'] = $sqlquery[0]->branch_id;
				$row['selMonth'] = $sqlquery[0]->monthlink;
				$row['selYear'] = $sqlquery[0]->yearlink;
				$row['amount'] = $sqlquery[0]->Amount;
				$row['time_start'] = $sqlquery[0]->minhrs;
				$row['time_end'] = $sqlquery[0]->maxhrs;
				$row['ot_start'] = $sqlquery[0]->maxamt;
				$row['ot_end'] = $sqlquery[0]->minamt;
				$row['timerange'] = floatval($sqlquery[0]->timerange);
				$row['otamount'] = $sqlquery[0]->OTAmount;
				$row['id'] = $sqlquery[0]->id;
				$row['totalamount'] = $sqlquery[0]->TotalAmount;
				$row['checkboxval'] = $sqlquery[0]->bdcheckcalc;
				$row['TotalCalc'] = $sqlquery[0]->mbcheckcalc;
				$row['chkvalTS'] = $sqlquery[0]->wknghrschk;
	    }
		$sql = Billing::getempdetails($request);
		if (!empty($sql)) {
			$cust_id=Billing::fnGetcusDetails($sql[0]->cust_id);
			$getbranchname = Billing::fnGetBranchDtls($sql[0]->cust_id);
			$getyearname = Billing::getYearname();
			$getmonthname = Billing::getMonthname();
		}
		return view('Billing.billingaddedit',['getdata' => $sql,
											  'sqlquery' => $row,
											  'cust_id' => $cust_id,
											  'getbranchname' => $getbranchname,
											  'getyearname' => $getyearname,
											  'getmonthname' => $getmonthname,
											  'request' => $request]);
	}
	function addeditprocess(Request $request) {
		if ($request->scrname == 'edit') {
			$copy = Billing::inserttempvalues($request);
			$update = Billing::updateprocess($request);
			if($update){
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('selMonth', $request->selMonth );
			Session::flash('selYear', $request->selYear );
			Session::flash('hdnempid', $request->hdnempid );
		} else if ($request->scrname == 'copy') {
			 if (!isset($request->selMonth) || empty($request->selMonth)) {
				$date_month = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
				$previousYrMn = date('Y-m-d', strtotime(date('Y-m')." -1 month"));
				$request->selYear = date("Y", strtotime($previousYrMn));
				$request->selMonth = date("m", strtotime($previousYrMn));
			} else {
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
			$temp_current_count = Billing::getTempcurrecDetails($request,$date_month);
			if ($temp_current_count == '0') {
				$copy = Billing::inserttempvalues($request);
				$insert = Billing::insertprocess($request);
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success');
			} else {
				Session::flash('success', 'Already this data Registered for '.$date_month.'!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('selMonth', $request->selMonth );
			Session::flash('selYear', $request->selYear );
			Session::flash('hdnempid', $request->hdnempid );
		}else{
			$insert = Billing::insertprocess($request);
			if($insert){
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('selMonth', $request->selMonth );
			Session::flash('selYear', $request->selYear );
			Session::flash('hdnempid', $request->hdnempid );
		}
		return Redirect::to('Billing/billdetailview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function ajaxbranchname(Request $request, $flg=null) {
		$branchname=array();
		$getbranchname = Billing::fnGetBranchDetails($request);
		$j=0;
		foreach ($getbranchname as $key => $value) {
				if ($flg != 1) {
				// REGISTER PROCESS
				$branchname[$j]['branchname_value'] = $value->branch_id;
				$branchname[$j]['branchname_text'] = $value->branch_name;
				} 
		$j++;
		}
		echo json_encode($branchname);exit();
	}
	function getpreviousdetails(Request $request) {
		$mainmenu= $request->mainmenu;
		$time=date('YmdHis');
     	$getpre_sql = Billing::get_preval($request);
     	 $j=0;
     	foreach ($getpre_sql as $key => $value) {
  		$empdtls[$j]['Empno'] = $value->Empno;
  		$empdtls[$j]['branch_id'] = $value->branch_id;
  		$empdtls[$j]['Clientid'] = $value->Clientid;
  		$empdtls[$j]['Amount'] = $value->Amount;
  		$empdtls[$j]['minhrs'] = $value->minhrs;
  		$empdtls[$j]['maxhrs'] = $value->maxhrs;
  		$empdtls[$j]['minamt'] = $value->minamt;
  		$empdtls[$j]['maxamt'] = $value->maxamt;
  		$empdtls[$j]['monthlink'] = $value->monthlink;
		$empdtls[$j]['yearlink'] = $value->yearlink;
		$sql = Billing::Regpreviousdetails($request,$empdtls[$j]['Empno'],$empdtls[$j]['branch_id'],$empdtls[$j]['Clientid']
			,$empdtls[$j]['Amount'],$empdtls[$j]['minhrs'],$empdtls[$j]['maxhrs'],$empdtls[$j]['minamt'],$empdtls[$j]['maxamt']);
		if($sql){
				Session::flash('success', 'PreviousDetails created Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'PreviousDetails created Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		$j++;
	}
		$emp_temp_details = Billing::getEmp_temp_Details($request);
		$emp_details = Billing::getempdtls($request);
		$i=0; 
     	foreach ($emp_details as $key => $data) {
      	$empdetails[$i]['Emp_Id'] = $data->Emp_Id;
      	$insertemp_details = Billing::insertgetempdtls($request,$data->Emp_Id);
      	$i++;
      }
		Session::flash('selMonth', $request->selMonth );
		Session::flash('selYear', $request->selYear );
		 ?>
			<form name="frmedit" id="frmedit"  action="../Billing/index?mainmenu=<?php echo $mainmenu;?>&time=<?php echo $time ?>" method="post">
		      <input type="hidden" name="selMonth" id="selMonth" value="<?php echo $request->selMonth;?>">
		      <input type="hidden" name="selYear" id="selYear" value="<?php echo $request->selYear;?>">
		      <input type="hidden" name="pageclick" id="pageclick" value="<?php echo $request->pageclick;?>">
		      <input type="hidden" name="plimit" id="plimit" value="<?php echo $request->plimit;?>">
		      <input type="hidden" name="page" id="page" value="<?php echo $request->page;?>">
		      <input type="hidden" name="sortOptn" id="sortOptn" value="<?php echo $request->billsort;?>">
		      <input type="hidden" name="sortOrder" id="sortOrder" value="<?php echo $request->sortOrder;?>">
		      <input type="hidden" name="selYear" id="selYear" value="<?php echo $request->selYear;?>">
		      <input type="hidden" name="mainmenu" id="mainmenu" value="<?php echo $request->mainmenu;?>">
		      <input type="hidden" name="sorting" id="sorting" value="<?php echo $request->sorting;?>">
		      <input type="hidden" name="scrname" id="scrname" value="<?php echo $request->scrname;?>">
	      </form>
	      	<script type="text/javascript">
	  		document.forms['frmedit'].submit();
     		</script>
		<?php
	}
}