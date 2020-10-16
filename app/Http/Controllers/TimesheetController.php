<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\timesheet;
use App\Model\SUBSTRING;
use App\Model\now;
use App\Http\Controllers\PAGING;
use DB;
use Input;
use Redirect;
use Session; 
use DateTime;
use DatePeriod;
use Illuminate\Support\Facades\Validator;
use DateIntercal;
use Config;
use Excel;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Style_Fill;
use PDF;
use App\Http\Controllers\Product;
ini_set("memory_limit",-1);
use Fpdf;
use Fpdi;
use getExtension;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use ExcelToPHPCal;
ini_set('max_execution_time', 0);


require_once('vendor/setasign/fpdf/fpdf.php');
require_once('vendor/setasign/fpdi/fpdi.php');



/**
* 
*/
class TimesheetController extends Controller
{
	
	function index(Request $request) 
	{	
		$pagevalue = array();
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}

		// To Add Last Month Time Sheet Data
	    timesheet::addPreMntTS();
	    if (!isset($request->selMonth) || empty($request->selMonth)) {
			$date_month = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
			$previousYrMn = date('Y-m-d', strtotime(date('Y-m')." -1 month"));
			$request->selYear = date("Y", strtotime($previousYrMn));
			$request->selMonth = date("m", strtotime($previousYrMn));
			// OLD
			/*$request->selYear = date('Y');
			$request->selMonth = date("m", strtotime("-1 months", strtotime(date('Y-m-01'))));*/
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
		$e_accountperiod = timesheet::fnGetAccountPeriod();
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
		$year_month = array();
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
		$est_execute = timesheet::fnGetTimeSheetRecord($from_date, $to_date);
	    $dbrecord = array();
	    foreach ($est_execute as $key => $value) {
	    	$res1= $value->workdate;
	    	array_push($dbrecord, $res1);
	    }
	    if (count($dbrecord) > 0) {
			$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
			if (end($dbrecord) < $lastMonthAsLink) {
				array_push($dbrecord, $lastMonthAsLink);
			}
		}
		$est_execute1 = timesheet::fnGetTimeSheetRecordPrevious($from_date);
		$dbprevious = array();
		foreach ($est_execute1 as $key => $value) {
	    	$res2 = $value->workdate;
	    	array_push($dbprevious, $res2);
	    }
	    $est_execute2 = timesheet::fnGetTimeSheetRecordNext($to_date);
	    $dbnext = array();
		foreach ($est_execute2 as $key => $value) {
	    	$res3 = $value->workdate;
	    	array_push($dbnext, $res3);
	    }
	    $dbrecord = array_unique($dbrecord);
		$db_year_month = array();
		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-", $dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}
		$split_date = explode('-', $date_month);
		$account_val ="";
		if (empty($_REQUEST['account_val'])) {
			$account_val = timesheet::getAccountingPeriod($e_accountperiod, $account_period, $account_val);
		} else {
			$account_val = $_REQUEST['account_val'];
		}
		
		$res = timesheet::fnGetValTimesheetEntry($request);
		$i = 0;
		$sectionarray=array("","","","","","","","","","");
		$sectiontitle=array("","","","","","","","","","");
		$titleArray = $sectionarray;
		foreach ($res as $key => $value) {
			$useridemp = $value->Emp_ID;
			$sectionupdate = timesheet::fnGetEmployeeTimeSheetData($useridemp,$request);
			foreach ($sectionupdate as $key => $sectionrow) {
				if ($sectionrow != '')
				{
					if (!empty($sectionrow->section)) {
						$arraysection = $sectionrow->section;
						$tde = timesheet::fnGetSpecificationAsKanji($sectionrow->section);
						if (!empty($tde[0]->specenglish)) {
							$sectionarray[$arraysection] = $tde[0]->specification;
					    	$sectiontitle[$arraysection] = $tde[0]->specenglish;
						}
					    
					}
				}
			}
		}

		if (count($sectionarray) > 0) {
			if (Session::get('languageval') == "en") {
	   			$titleArray = $sectiontitle;
		    } else {
			    $titleArray = $sectionarray;


		    }
		} 
		$i =0;
		$j = 0;
		$displayArray = array();
		$selYear = $request->selYear;
		$selMonth = $request->selMonth;
		foreach ($res as $key => $value) {
			$useridemp = $value->Emp_ID;
			$userid = $value->id;
			$getemployeedetails = timesheet::getAllemployeeDetails($useridemp,$selYear . "-" .$selMonth);
			$displayArray[$j]['Emp_ID'] = $useridemp;
			if (empty($getemployeedetails[$i]['workinghours'])) {
				$displayArray[$j]['workinghours'] = "-";
			}
			$displayArray[$j]['workinghours'] = $getemployeedetails[$i]['workinghours'];

			$displayArray[$j]['section'] = $getemployeedetails[$i]['section'];
			$displayArray[$j]['permission'] = $getemployeedetails[$i]['permission'];
			$displayArray[$j]['workplace'] = $getemployeedetails[$i]['workplace'];
			$displayArray[$j]['late'] = $getemployeedetails[$i]['late'];
			$displayArray[$j]['extradays'] = $getemployeedetails[$i]['extradays'];
	   		$displayArray[$j]['CREATEDDATE'] =substr($value->CREATEDDATE,0,10);
			$displayArray[$j]['upload_path'] = $getemployeedetails[0]['upload_path'];
			$displayArray[$j]['submit'] = $getemployeedetails[0]['submit'];
			$displayArray[$j]['FirstName'] = $value->FirstName;
			$displayArray[$j]['LastName'] = $value->LastName;
			// $displayArray[$j]['workingdays'] = $getemployeedetails['workingdays'];
			// $displayArray[$j]['delflg'] = $value['delFlg'];
			// echo'<pre>';print_r($displayArray[$j]['workinghours']);echo'</pre>';
			// pagination
			$pagevalue = timesheet::getAlldatas($useridemp,$selYear . "-" .$selMonth,$request);
			//print_r($pagevalue);exit();
			$j++;
		}	
		$disp = count($displayArray);
		return view('Timesheet.index',['request' => $request,
										'account_period' => $account_period,
										'year_month' => $year_month,
										'db_year_month' => $db_year_month,
								 		'displayArray' => $displayArray,
								 		'date_month' => $date_month,
										'dbnext' => $dbnext,
										'dbprevious' => $dbprevious,
										'last_year' => $last_year,
										'current_year' => $current_year,
										'account_val' => $account_val,
										'titleArray' => $titleArray,
										'displayArray' => $displayArray,
										'disp' => $disp,
										'res' => $res,
										'pagevalue' => $pagevalue]);
	}
	function importOldTimeSheetDetails(Request $request){
		$getConnectionQuery = timesheet::fnGetConnectionQuery($request);
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);

		$db = DB::connection('otherdb');

		if ($db) {

			$db = DB::connection('otherdb');

			$dbstatus = $db = DB::connection('otherdb');
	// Test database connection
			if ($dbstatus) {

	//To Get The Latest Employee Details In New DataBase
		$exeQuery = timesheet::fnCheckTableExist($getConnectionQuery);
		
		$getRowCount = count($exeQuery);

		if ($getRowCount > 0) {
			$exeQuery = timesheet::fnGetOldTimeSheetDetails();

		if ($exeQuery) {
			$getOldUserRecordAsArray = array();
			$i = 0;
			foreach ($exeQuery as $key => $value) {
				$getOldUserRecordAsArray[$i]["emp_id"] = $value->emp_id;
				$getOldUserRecordAsArray[$i]["workdate"] = $value->workdate;
				$getOldUserRecordAsArray[$i]["starttime"] = $value->starttime;
				$getOldUserRecordAsArray[$i]["endtime"] = $value->endtime;
				$getOldUserRecordAsArray[$i]["non_work_starttime"] = $value->non_work_starttime;
				$getOldUserRecordAsArray[$i]["non_work_endtime"] = $value->non_work_endtime;
				$getOldUserRecordAsArray[$i]["section"] = $value->section;
				$getOldUserRecordAsArray[$i]["workingplace"] = $value->workingplace;
				$getOldUserRecordAsArray[$i]["remark"] = $value->remark;
				$getOldUserRecordAsArray[$i]["created_by"] = $value->created_by;
				$getOldUserRecordAsArray[$i]["created_date"] = $value->created_date;
				$getOldUserRecordAsArray[$i]["updated_by"] = $value->updated_by;
				$getOldUserRecordAsArray[$i]["updateed_date"] = $value->updateed_date;
				$getOldUserRecordAsArray[$i]["submit_date"] = $value->submit_date;
				$getOldUserRecordAsArray[$i]["upload_path"] = $value->upload_path;
				$i++;
			}
			for ($i = 0; $i < count($getOldUserRecordAsArray); $i++) {
				$exist = timesheet::fnOldTimeSheetExist($getOldUserRecordAsArray[$i]["emp_id"], $getOldUserRecordAsArray[$i]["workdate"]);
				$existCount = count($exist);
				if ($existCount == 0) {

					$column_name = "";
					$column_value = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						$column_name .= $key . ",";
						$column_value .= "'" . $value . "',";
					}
					$column_name = mb_substr($column_name, 0, mb_strlen($column_name) - 1);
					$column_value = mb_substr($column_value, 0, mb_strlen($column_value) - 1);
					$insertOldUserQuery = timesheet::fnInsertOldTimeSheetDetails($column_name, $column_value);

				} else {
					$column_name_value = "";
					$condition = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						if ($key != "emp_id" && $key != "workdate") {
							$column_name_value .= $key . " = '" . $value . "',";
						}
					}
					$condition = "emp_id = '" . $getOldUserRecordAsArray[$i]["emp_id"] . "' AND workdate = '" . $getOldUserRecordAsArray[$i]["workdate"] . "'";
					$column_name_value = mb_substr($column_name_value, 0, mb_strlen($column_name_value) - 1);

					$updateOldUserQuery = timesheet::fnUpdateOldTimeSheetDetails($column_name_value, $condition);

					Session::flash('success', 'Updated Sucessfully!'); 
					Session::flash('type', 'alert-success');
				}
				 
			}
			self::importOldTempTimeSheetDetails($request);
			} else {
				Session::flash('success', 'Record Not Up dated Sucessfully'); 
				Session::flash('type', 'alert-success'); 
			}
			} else {
					 Session::flash('success', 'No New Record Found'); 
	              	 Session::flash('type', 'alert-danger'); 
			}
		} else{
			Session::flash('success', 'Invalid Db Name'); 
			Session::flash('type', 'alert-danger'); 
		}
	} else{
		Session::flash('success', 'Invalid Db Connection'); 
		Session::flash('type', 'alert-danger'); 
	}
	return Redirect::to('Timesheet/timesheetindex?mainmenu=timesheet&time='.date('YmdHis'));
	}
	public static function importOldTempTimeSheetDetails(Request $request) {

		$getConnectionQuery = timesheet::fnGetConnectionQuery($request);

		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);

		$db = DB::connection('otherdb');

		if ($db) {
		

			$db = DB::connection('otherdb');

			$dbstatus = $db = DB::connection('otherdb');
	// Test database connection
			if ($dbstatus) {

		$getConnectionQuery = timesheet::fnGetConnectionQuery($request);
		$exeQuery = timesheet::fnChecktempTableExist($getConnectionQuery);
		$getRowCount = count($exeQuery);
		if ($getRowCount > 0) {
			$exeQuery = timesheet::fnGetOldTempTimeSheetDetails();
		
		if ($exeQuery) {

			$getOldUserRecordAsArray = array();
			$i = 0;
			foreach ($exeQuery as $key => $res) {
				$getOldUserRecordAsArray[$i]["Emp_Id"] = $res->Emp_Id;
				$getOldUserRecordAsArray[$i]["delflg"] = $res->delflg;
				$getOldUserRecordAsArray[$i]["resign_id"] = $res->resign_id;
				$getOldUserRecordAsArray[$i]["title"] = $res->title;
				$getOldUserRecordAsArray[$i]["year"] = $res->year;
				$getOldUserRecordAsArray[$i]["month"] = $res->month;
				$getOldUserRecordAsArray[$i]["create_date"] = $res->create_date;
				$getOldUserRecordAsArray[$i]["create_by"] = $res->create_by;
				$getOldUserRecordAsArray[$i]["update_date"] = $res->update_date;
				$getOldUserRecordAsArray[$i]["update_by"] = $res->update_by;
				$i++;
			}

			for ($i = 0; $i < count($getOldUserRecordAsArray); $i++) {
				$exist = timesheet::fnOldTempTimeSheetExist($getOldUserRecordAsArray[$i]["Emp_Id"],$getOldUserRecordAsArray[$i]["year"], 
					$getOldUserRecordAsArray[$i]["month"]);
				$existCount = count($exist);
				if ($existCount == 0) {

					$column_name = "";
					$column_value = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						$column_name .= $key . ",";
						$column_value .= "'" . $value . "',";
					}
					$column_name = mb_substr($column_name, 0, mb_strlen($column_name) - 1);
					$column_value = mb_substr($column_value, 0, mb_strlen($column_value) - 1);

					$insertOldUserQuery = timesheet::fnInsertOldTempTimeSheetDetails($column_name, $column_value);


				} else {	
					$column_name_value = "";
					$condition = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						if ($key != "Emp_Id" && $key != "year" && $key != "month") {
							$column_name_value .= $key . " = '" . $value . "',";
						}
					}
					$condition = "Emp_Id = '" . $getOldUserRecordAsArray[$i]["Emp_Id"] . 
											 "' AND year = '" . $getOldUserRecordAsArray[$i]["year"] . 
											 "' AND month = '" . $getOldUserRecordAsArray[$i]["month"] . "'";
					$column_name_value = mb_substr($column_name_value, 0, mb_strlen($column_name_value) - 1);
					$updateOldUserQuery = timesheet::fnUpdateOldTempTimeSheetDetails($column_name_value, $condition);

				}
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}
			self::importOldTimeSheetSpecificationDetails($request);			
			} else {
				Session::flash('success', 'Record Not Up dated Sucessfully'); 
				Session::flash('type', 'alert-success'); 
			}
			} else {
					 Session::flash('success', 'No New Record Found'); 
	              	 Session::flash('type', 'alert-danger'); 
			}
		} else{
			Session::flash('success', 'Invalid Db Name'); 
			Session::flash('type', 'alert-danger'); 
		}
	} else{
		Session::flash('success', 'Invalid Db Connection'); 
		Session::flash('type', 'alert-danger'); 
	}
		return Redirect::to('Timesheet/timesheetindex?mainmenu=timesheet&time='.date('YmdHis'));
	}
	public static function importOldTimeSheetSpecificationDetails(Request $request) {
		$getConnectionQuery = timesheet::fnGetConnectionQuery($request);
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);

		$db = DB::connection('otherdb');

		if ($db) {
			$db = DB::connection('otherdb');

			$dbstatus = $db = DB::connection('otherdb');
	// Test database connection
			if ($dbstatus) {
			$getConnectionQuery = timesheet::fnGetConnectionQuery($request);
			$exeQuery = timesheet::fnCheckTempTableExistSpec($getConnectionQuery);
			$getRowCount = count($exeQuery);
			if ($getRowCount > 0) {
				$exeQuery = timesheet::fnGetOldTimeSheetSpecificationDetails();
				if ($exeQuery) {
				$getOldUserRecordAsArray = array();
				$i = 0;
				foreach ($exeQuery as $key => $res) {
				$getOldUserRecordAsArray[$i]["id"] = $res->id;
				$getOldUserRecordAsArray[$i]["specification"] = $res->specification;
				$getOldUserRecordAsArray[$i]["specenglish"] = $res->specenglish;
				$getOldUserRecordAsArray[$i]["specsymbol"] = $res->specsymbol;
				$getOldUserRecordAsArray[$i]["Ins_DT"] = $res->Ins_DT;
				$getOldUserRecordAsArray[$i]["Upd_DT"] = $res->Upd_DT;
				$getOldUserRecordAsArray[$i]["CreatedBy"] = $res->CreatedBy;
				$getOldUserRecordAsArray[$i]["UpdatedBy"] = $res->UpdatedBy;
				$getOldUserRecordAsArray[$i]["Order_id"] = $res->Order_id;
				$getOldUserRecordAsArray[$i]["DelFlg"] = $res->DelFlg;
				$i++;
			}

			for ($i = 0; $i < count($getOldUserRecordAsArray); $i++) {
				$exist = timesheet::fnOldTimeSheetSpecificationExist($getOldUserRecordAsArray[$i]["id"]);
				$existCount = count($exist);
				if ($existCount == 0) {
					$column_name = "";
					$column_value = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						$column_name .= $key . ",";
						$column_value .= "'" . $value . "',";
					}
					$column_name = mb_substr($column_name, 0, mb_strlen($column_name) - 1);
					$column_value = mb_substr($column_value, 0, mb_strlen($column_value) - 1);
					$insertOldUserQuery = timesheet::fnInsertOldTimeSheetSpecificationDetails($column_name, $column_value);

				} else {
					$column_name_value = "";
					$condition = "";
					foreach ($getOldUserRecordAsArray[$i] AS $key => $value) {
						if ($key != "id") {
							$column_name_value .= $key . " = '" . $value . "',";
						}
					}
					$condition = "id = '" . $getOldUserRecordAsArray[$i]["id"] . "'";
					$column_name_value = mb_substr($column_name_value, 0, mb_strlen($column_name_value) - 1);
					$updateOldUserQuery = timesheet::fnUpdateOldTimeSheetSpecificationDetails($column_name_value, $condition);

				}
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success');
			}
		} else {
			Session::flash('success', 'Record Not Up dated Sucessfully'); 
			Session::flash('type', 'alert-success');
		} }else {
					 Session::flash('success', 'No New Record Found'); 
	              	 Session::flash('type', 'alert-danger'); 
			}
		} else{
			Session::flash('success', 'Invalid Db Name'); 
			Session::flash('type', 'alert-danger'); 
		}
	} else{
		Session::flash('success', 'Invalid Db Connection'); 
		Session::flash('type', 'alert-danger'); 
	}
	} 
	public static function timeSheetHistorydetails(Request $request){
		if (!isset($request->empid)) {
			return Redirect::to('Timesheet/timesheetindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 25;
		}
		//FOR SORTING
		$timesheetviewlistarray = [$request->timesheetviewsort=>$request->timesheetviewsort,
							'created_date'=> trans('messages.lbl_Date')];
		$srt = $request->timesheetviewsort;
		$odr = $request->sortOrder;
		if ($request->timesheetviewsort == "") {
			$request->timesheetviewsort = "created_date";
		}
		if (empty($request->sortOrder)) {
			$request->sortOrder = "DESC";
		}
		if ($request->sortOrder == "desc") {  
			$request->sortstyle="sort_desc";
		} else {  
			$request->sortstyle="sort_asc";
		}
		$gettimesheetdetails = timesheet::fngettimesheetdetails($request);
		$disp  = count($gettimesheetdetails);
		$empdet = timesheet::fuGetEmpDetails($request);
		$sectionarray=array("","","","","","","","","","");
		$sectiontitle=array("","","","","","","","","","");
		$titleArray = $sectionarray;
		$sno = 1;
		$i = 0;
		$value = "";
		$array = "";
		$update = array();
		$sectionrc = array();
		foreach ($gettimesheetdetails as $key => $sectionrc) {
			$sectionupdate=timesheet::fnGetEmployeeTimeSheetDetail($sectionrc->emp_id,
				$sectionrc->yearmonth);
			foreach ($sectionupdate as $key => $sectionrow) {
					if($sectionrow!='') {	

					if (!empty($sectionrow->section)) {

						$arraysection=$sectionrow->section;
						$tde=timesheet::viewkanji($sectionrow->section);
						if (isset($tde[0]->specification)) {
							$sectionarray[$arraysection]=$tde[0]->specification;
					    	$sectiontitle[$arraysection]=$tde[0]->specenglish;
						}
					   
					}	
				}
			}
			$array=array(0,0,0,0,0,0,0,0,0,0);
			$actual=0;
			$extra=0;	
			$update = timesheet::fnEmpDetail($sectionrc->emp_id,$sectionrc->yearmonth);
			foreach ($update as $key123 => $row) {
				if($row!='') {

					$value[$i][0]= $row->emp_id;
					$regdate= substr($row->created_date,0,10);
					if (!empty($row->workingplace)) {
						$value[$i][2]= $row->workingplace;
					}
					if ($row->section != "" && $row->section != "null") {
						if (isset($row->section)) {
							$section=$row->section;
							$array[$section]++;
						}
					}
					$arr=self::calculationdetails($row->starttime,$row->endtime,$row->non_work_starttime,$row->non_work_endtime,"",$row->section);
					$actual=$actual+$arr[4];
					$workdate=$row->workdate;
					$timestamp = strtotime($workdate);
					$temp=date('N', $timestamp);
					if(($temp==6 && ($row->starttime!='' && $row->starttime!="00:00:00") && ($row->endtime!='' && $row->endtime!="00:00:00") ) 
					|| ($temp==7 && ($row->starttime!='' && $row->starttime!="00:00:00") && ($row->endtime!='' && $row->endtime!="00:00:00") ) )
					{
						$extra++;
					}
				}
				$actualminutes = $actual % 60;
				$actualhours = ($actual - $actualminutes)/60;
				$result=$actualhours .":".str_pad($actualminutes, 2,"0",STR_PAD_LEFT);
				$value[$i][5]=$array[1];
				$value[$i][4]=$array[2];
				$value[$i][6]=$array[4];
				$value[$i][7]=$extra;
				$value[$i][3]=$result;
				$value[$i][1]=$regdate;
				$value[$i]['date']=substr($row->workdate,5,2);
				$value[$i]['workyear']=substr($row->workdate,0,4);
				$value[$i]['workmonth']=substr($row->workdate,5,2);
			}
			$i++;
		}
		if (count($sectionarray) > 0) {
			if (Session::get('languageval') == "en") {
	   			$titleArray = $sectiontitle;
		    } else {
			    $titleArray = $sectionarray;


		    }
		}
		$fncount = $i;
		$fileCnt = $i;
		$count = array();
		$count = timesheet::datas($request);
	return view('Timesheet.timeSheetHistorydetails',['request' => $request,
											'fncount' => $fncount,
											'timesheetviewlistarray' => $timesheetviewlistarray,
											'value' => $value,
											'titleArray' => $titleArray,
											'disp' => $disp,
											'count' => $count,
											'array' => $array,
											'empdet' => $empdet,
											'gettimesheetdetails' => $gettimesheetdetails]);
	}
	public static function calculationdetails($workstart,$workend,$nonstart,$nonend,$youbicolor,$section)
		{
			$nonworktime = "";
			$startTime = $workstart;
			$endTime   = $workend;
			$permissionstart=$nonstart;
    		$permissionend=$nonend;
    		$detuctionhours  ="";
		 	$deductionminutes  = "";
		 	$startInputHrs  = "";
		 	$startInputMins  = "";
		 	$endInputHrs  = "";
		 	$endInputMins  = "";

			$diff = $endTime - $startTime;
			$temp=abs($diff);
			$difference=$temp/60;
/////////////////////////////////////////
			if (isset($startTime)) {
				$startTimeArray = explode(':',$startTime);
   				$startInputHrs = $startTimeArray[0];
   				$startInputMins = $startTimeArray[1];
			}
			
   			// Need to check siva
   			/*$dumi = $startTimeArray[2];*/
   			if (isset($endTime)) {
   				$endTimeArray = explode(':',$endTime);
   				$endInputHrs = $endTimeArray[0];
   				$endInputMins = $endTimeArray[1];
   			}
   			
   			// Need to check siva
   			/*$dumy = $endTimeArray[2];*/
   			if ($startInputHrs != "" || $startInputHrs != "null") {
   				$startMin = $startInputHrs*60 + $startInputMins;
   			}
   			if ($endInputHrs != "" || $endInputHrs != "null") {
   				$endMin = $endInputHrs*60 + $endInputMins;
   			}
///////////////////////////////////////////////////
		if ($permissionend) {
   			$nonstartTimeArray = explode(':',$permissionstart);
  		 	$nonstartInputHrs = $nonstartTimeArray[0];
   			$nonstartInputMins = $nonstartTimeArray[1];

   			$nonendTimeArray = explode(':',$permissionend);
   			$nonendInputHrs = $nonendTimeArray[0];
   			$nonendInputMins = $nonendTimeArray[1];

   			$nonstartMin = $nonstartInputHrs*60 + $nonstartInputMins;
   			$nonendMin = $nonendInputHrs*60 + $nonendInputMins;
   			$nonworktime;
   		   if ($nonendMin < $nonstartMin) {
       			$nonminutesPerDay = 24*60; 
       			$nonworktime = $nonminutesPerDay - $nonstartMin;  // Minutes till midnight
       			$nonworktime += $nonendMin; // Minutes in the next day
   			}
   			else 
   			{
   
   				$nonworktime = $nonendMin - $nonstartMin;
   		
   			}
		} 

			$lunch=13*60;
    		$break1=19*60;
       		$break2=22*60;
       		$late=22*60 + 30;
        	$latenight=0;
  			$overtime=0;
   			$tem;
        $tem1;
        $tem2;
   			$deduction=0;
   			$result=0;
  	if ($endMin   < $startMin){
      	 $minutesPerDay = 24*60;
       	$result = $minutesPerDay - $startMin; // Minutes till midnight
       	$result += $endMin; /// Minutes in the next day
  	}
  	else 
 	{
   		if ($endMin > $break2){
        if ($endMin > $break2 && $endMin < 1350 && $startMin < 840)
        {
          if($startMin > 780 && $startMin < 840)
          {
            if($endMin > $break2 && $endMin < 1350)
            {
              $tem2 = $endMin - $break2;
              $tem2=$tem2+30;
            }
            else
            {
              $tem2 = 60;
            }
             $lb=840-$startMin;
             $result = $endMin - $startMin - ($tem2+$lb);
             $overtime=$result - 8*60;
             $latenight=$endMin - $late;
             $latenight=$latenight<0?'':$latenight;
             $overtime=$overtime - $latenight;
          }
          else
          {
          $tem2=$endMin - $break2;
          $result = $endMin - $startMin - ($tem2+90);
          $overtime=$result - 8*60;
          $latenight=$endMin - $late;
          $latenight=$latenight<0?'':$latenight;
          $overtime=$overtime - $latenight;
          }        
        }
        else if($startMin >= 840 && $endMin < 1350) {
               $tem5=$endMin - $break2;
               $result = $endMin - $startMin - (30+$tem5);
               $overtime=$result - 8*60;
               $overtime=$overtime<0?'':$overtime; 
               $latenight=$endMin - $late;   
               $latenight=$latenight<0?'':$latenight;

            }
        else if($startMin >= 840 && $endMin > 1350) {
               $result = $endMin - $startMin - (60);                
               $overtime=$result - 8*60;              
               $overtime=$overtime<0?'':$overtime; 
               $latenight=$endMin - $late;          
        }
        else
        {
          $result = $endMin - $startMin - 120;
          $overtime=$result - 8*60;
          $latenight=$endMin - $late;
          $latenight=$latenight<0?'':$latenight;
          $overtime=$overtime - $latenight;   
        }
   		}
   		else if ($endMin  > $break1)
   		{
        if (($endMin > $break1 && $endMin < 1170)||($startMin > 780))
        {
          if($startMin > 780 && $startMin < 840)
          {
            if($endMin > $break1 && $endMin < 1170)
            {
              $tem1=$endMin - $break1;
            }
            else
            {
              $tem1=30;
            }
             $lb=840-$startMin;
             $result = $endMin - $startMin - ($tem1+$lb);
             $overtime=$result - 8*60;
             $overtime=$overtime<0?'':$overtime;
          }
          else if($startMin >= 840) {
          $tem1=$endMin - $break1;
          $result = $endMin - $startMin - ($tem1);
          $overtime=$result - 8*60;
          $overtime=$overtime<0?'':$overtime;  
          
        }
          else
          {
          $tem1=$endMin - $break1;
          $result = $endMin - $startMin - ($tem1+60);
          $overtime=$result - 8*60;
          $overtime=$overtime<0?'':$overtime;
          }
        }
        else
        {
          $result = $endMin - $startMin - 90;
          $overtime=$result - 8*60;
          $overtime=$overtime<0?'':$overtime;   
        }
   		
   		}
   		else if ($endMin > $lunch) 
   		{
   			if ($endMin > $lunch && $endMin < 14 * 60)
   			{
   			$tem=$endMin - $lunch;
   			$result = $endMin - $startMin - $tem;
   			//$overtime=$result - 8*60;
   			}
        else if($startMin >= 840) {
          $tem1=$endMin - $break1;
          $result = $endMin - $startMin - ($tem1);
          $overtime=$result - 8*60;
          $overtime=$overtime<0?'':$overtime;  
          
        }
   			else
   			{
   				$result = $endMin - $startMin - 60;
   				$overtime=$result - 8*60;   
   				$overtime=$overtime<0?'':$overtime;				
   			}

   		}
   		else
   		{
     		$result = $endMin - $startMin;
     	 	$overtime= $result - 8*60;
     	 	$overtime=$overtime<0?'':$overtime;
   		}

  	}
  			 
   // Non Working 

   			if ($nonworktime > 0)
    		{
    			//echo $nonworktime;
    			$result = $result - $nonworktime;
    			$overtime = $overtime - $nonworktime;
    			$latenight = $latenight - $nonworktime;
    			$nonminutes = $nonworktime % 60;
   				$nonhours = ($nonworktime - $nonminutes) / 60;

    		}

    		if ($result < 480)
  			 {
  			 	//echo $result;
  			 	
          if(($workstart != $nonstart) && ($workend != $nonend))
          {
           if ($youbicolor !="日" && $youbicolor !="土" && $section !=9)
           {
   				   $deduction= 8*60 - $result;
   				   $deduction=$deduction<480?$deduction:"0:0";
   				   $deductionminutes = $deduction % 60;
   				   $detuctionhours = ($deduction - $deductionminutes) / 60;
   				   $detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
  				   $deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);
            }
          }
          else
          {
            if (($workstart !='' && $workstart !='00:00:00') && 
            	($workend !='' && $workend !='00:00:00'))
           {
             $deduction = 8*60; 
             $result="";
             $deductionminutes = $deduction % 60;
             $detuctionhours = ($deduction - $deductionminutes) / 60;
             $detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
             $deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);
             
           }        
          }
 			  }
 			 if($endMin > 1560)
    		{
 			 	if (($endMin > 26*60) && ($endMin < 29 * 60))
   				{
   				$morning=$endMin - 26*60;
   				$result =$result - $morning;
   				$latenight=$latenight - $morning;
   				}
   				else
   				{
   				$result =  $result - 180;
   				$latenight=$latenight - 180;
   				}
   			}
       
		//RESULT 
		$actualminutes = $result % 60;
		$actualhours = ($result - $actualminutes)/60;
		$actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
		$actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);
		$actual=$actualhours.":".$actualminutes;

		$overminutes = $overtime % 60;
		$overhours = ($overtime - $overminutes)/60;
		$overhours=str_pad($overhours,2,"0",STR_PAD_LEFT);
		$overminutes=str_pad($overminutes,2,"0",STR_PAD_LEFT);

		$over=$overhours.":".$overminutes;

		$lateminutes = $latenight % 60;
		$latehours = ($latenight - $lateminutes)/60;
		$latehours=str_pad($latehours,2,"0",STR_PAD_LEFT);
		$lateminutes=str_pad($lateminutes,2,"0",STR_PAD_LEFT);

		$la=$latehours.":".$lateminutes;
		$dut=$detuctionhours.":".$deductionminutes;
		$arr=array($actual,$over,$la,$dut,$result,$overtime,$latenight,$deduction);
		return $arr;
	}
	public static function timesheetview(Request $request) {
		if (!isset($request->empid)) {
			return Redirect::to('Timesheet/timesheetindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
			$youbi = "";
			if (Session::get('languageval') == "en") {
				$youbi = array('Sun','Mon','Tue','Wed','Thr','Fri','Sat','Sun' );
		    } else {
		    	$youbi = array('日','月','火','水','木','金','土','日' );
		    }		    
	    $youbiJab = array('日','月','火','水','木','金','土','日' );
		$Emp_ID = $request->empid;
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		$yr = $request->selYear;
		// $flag = $request->regHidden;
		$previousDtMn = $yr."-".$mon;
		$date_value = $previousDtMn."-01";
		$number = cal_days_in_month(CAL_GREGORIAN, $mon, $yr);
		$row = timesheet::empname($Emp_ID);
		$Name = ucwords(strtolower($row[0]->LastName)).".".ucwords(mb_substr($row[0]->FirstName,0,1,'UTF-8'));
		$date = $yr . "-" . $mon;
		$update = timesheet::datadb($Emp_ID, $date);
		$hidden1 = "";
		$hidden2 = "";
		$hidden3 = "";
		$hidden4 = "";
		$sectioncount=array(0,0,0,0,0,0,0,0,0,0);
		$j = 1;
		$displayArray = array();
		for ($i=1; $i <= $number; $i++) {
			$row = self::selectData($Emp_ID,$number,$yr,$mon,$i);
			$timestamp = strtotime($previousDtMn."-".$i);
			$date = new DateTime();
			$youbicolor = $youbiJab[date('N', $timestamp)];
			if ($youbicolor =="日" || $youbicolor =="土" ) {  //|| $row[0]->section =="9"
				$displayArray[$j]["leaveDisplay"] = 1;
			} else {
				$displayArray[$j]["leaveDisplay"] = 0;
			}
			$displayArray[$j]["i"] = $i;
			$displayArray[$j]["section"] = (isset($row[0]->section) && $row[0]->section != "null" && $row[0]->section != "") ? $row[0]->section : 0;
			$displayArray[$j]["workingplace"] = isset($row[0]->workingplace) ? $row[0]->workingplace : "";
			$displayArray[$j]["starttime"] = isset($row[0]->starttime) ? $row[0]->starttime : "";
			$displayArray[$j]["endtime"] = isset($row[0]->endtime) ? $row[0]->endtime : "";
			$displayArray[$j]["non_work_starttime"] = isset($row[0]->non_work_starttime) ? $row[0]->non_work_starttime : "";
			$displayArray[$j]["non_work_endtime"] = isset($row[0]->non_work_endtime) ? $row[0]->non_work_endtime : "";
			$displayArray[$j]["remark"] = isset($row[0]->remark) ? $row[0]->remark : "";
			$workstart= isset($row[0]->starttime) ? $row[0]->starttime : "00:00:00";
			$workend= isset($row[0]->endtime) ? $row[0]->endtime : "00:00:00";
			$nonstart= isset($row[0]->non_work_starttime) ? $row[0]->non_work_starttime : "00:00:00";
			$nonend =  isset($row[0]->non_work_endtime) ? $row[0]->non_work_endtime : "00:00:00";
			$arr = self::calculationdetails($workstart,$workend,$nonstart,$nonend,$youbicolor,$displayArray[$j]["section"]);
			$hidden1=$hidden1+$arr[4];
			$hidden2=$hidden2+$arr[5];
			$hidden3=$hidden3+$arr[6];
			$hidden4=$hidden4+$arr[7];
			$actualminutes = $hidden1 % 60;
			$actualhours = ($hidden1 - $actualminutes)/60;
			$actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
			$actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);
			$actual=$actualhours.":".$actualminutes;
 			$overminutes = $hidden2 % 60;
 			$overhours = ($hidden2 - $overminutes)/60;
 			$overhours=str_pad($overhours,2,"0",STR_PAD_LEFT);
			$overminutes=str_pad($overminutes,2,"0",STR_PAD_LEFT);
 			$over=$overhours.":".$overminutes;
 			$lateminutes = $hidden3 % 60;
 			$latehours = ($hidden3 - $lateminutes)/60;
 			$latehours=str_pad($latehours,2,"0",STR_PAD_LEFT);
			$lateminutes=str_pad($lateminutes,2,"0",STR_PAD_LEFT);
 			$la=$latehours.":".$lateminutes;
 			$deductionminutes = $hidden4 % 60;
			$detuctionhours = ($hidden4 - $deductionminutes) / 60;
			$detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
			$deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);
			$dut=$detuctionhours.":".$deductionminutes;
			$displayArray[$j]["actual"] = $actual;
			$displayArray[$j]["over"] = $over;
			$displayArray[$j]["la"] = $la;
			$displayArray[$j]["dut"] = $dut;
			$displayArray[$j]["arr"] = $arr;
			$displayArray[$j]["timestamp"] = $timestamp;
			$j++;
		}
		return view('Timesheet.view',['request'=> $request,
									  'yr'=> $yr,
									  'mon'=> $mon,
									  'Emp_ID'=> $Emp_ID,
									  'update'=> $update,
									  'Name'=> $Name,
									  'displayArray'=> $displayArray,
									  'youbi'=> $youbi,
									  'timestamp'=> $timestamp,
									  'sectioncount'=> $sectioncount,
									  'actual'=> $actual,
									  'over'=> $over,
									  'la'=> $la,
									  'dut'=> $dut,
									  'arr'=> $arr]); 

	}
	public static function selectData($Emp_ID,$number,$yr,$month,$i){
		if (empty($Emp_ID)) {
			$Emp_ID=$request->empinhide;
		}
		$date = $yr."-".$month."-".$i;
		$timesheet = timesheet::selection($Emp_ID, $date);
		return $timesheet;
	}
	public static function singlerow1(Request $request){
		$curTime =date('Y-m-d G:i:s');
		$Emp_ID = $request->empid;
		$date = $request->selYear."-".$request->selMonth."-".$request->seldate;
		$row=timesheet::selection($request->empid,$date);
		$rowname = timesheet::empname($request->empid);
		$Name = ucwords(strtolower($rowname[0]->LastName)).".".ucwords(mb_substr(
			$rowname[0]->FirstName,0,1,'UTF-8'));
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		$yr = $request->selYear;
		$updateby=$Name;
		$updateTime =date('Y-m-d G:i:s');
		if (empty($row)) {
			$inserval=timesheet::insertion($request->empid,$date,$request->start1hdn,
				$request->end1hdn,$request->start2hdn,$request->end2hdn,$request->sectionhdn,
				$request->worktxthdn,$request->remarkshdn,$rowname[0]->FirstName,$curTime);
		} else {
			$updateval = timesheet::updatetimesheet($Emp_ID,$date,$request->start1hdn,
				$request->end1hdn,$request->start2hdn,$request->end2hdn,$request->sectionhdn,
				$request->worktxthdn,$request->remarkshdn,$updateby,$updateTime);
		}
		return self::Timesheetview($request);

	}

	public static function timeSheetRegprocess(Request $request) {
		$date = $request->selYear."-".$request->selMonth."-".$request->seldate;
		$row=timesheet::selection($request->empid,$date);
		$rowname = timesheet::empname($request->empid);
		$Emp_ID = $request->empid;
		$Name = ucwords(strtolower($rowname[0]->LastName)).".".ucwords(mb_substr(
			$rowname[0]->FirstName,0,1,'UTF-8'));
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		
		$yr = $request->selYear;
		$number = cal_days_in_month(CAL_GREGORIAN, $mon, $yr);
		$flag = $request->flag;
		for ($i=1; $i <= $number; $i++) { 
			$date=$yr."-".$mon."-".$i;
			$sec = "classification".$i;
			$section = $request->$sec;
			$start1 = "start1".$i;
			$workstart=$request->$start1;
			$end1 = "end1".$i;
			$workend=$request->$end1;
			$start2 = "start2".$i;
			$nonworkstart=$request->$start2;
			$end2 = "end2".$i;
			$nonworkend=$request->$end2;
			$worktxt = "worktxt".$i;
			$workplace=$request->$worktxt;
			$remarks = "remarks".$i;
			$remarks=$request->$remarks;
			$createby=$Name;
			$curTime =date('Y-m-d G:i:s');
			$updateby=$Name;
			$updateTime =date('Y-m-d G:i:s');
			$row = timesheet::selection($Emp_ID, $date);
			if (!empty($row)) { 
				$updateval = timesheet::updatetimesheet($Emp_ID,$date,$workstart,$workend,$nonworkstart,
				$nonworkend,$section, $workplace,$remarks,$updateby,$updateTime);
			} else {
			if($section !="" || ($workstart !="" && $workend !="")){
			$inserval=timesheet::insertion($Emp_ID,$date,$workstart,$workend,$nonworkstart,
				$nonworkend,$section, $workplace,$remarks,$createby,$curTime);
			}
			}
		}
		return self::Timesheetview($request);
	}
	public static function addedit(Request $request) {
		if (!isset($request->empid)) {
			return Redirect::to('Timesheet/timesheetindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$viewval = array();
		$disabledeng = "";
		$disabledjp = "";
		$disabledsymbol = "";
		$flagvalue = $request->flagval;
		$youbi = "";
			if (Session::get('languageval') == "en") {
				$youbi = array('Sun','Mon','Tue','Wed','Thr','Fri','Sat','Sun' );
		    } else {
		    	$youbi = array('日','月','火','水','木','金','土','日' );
		    }		    
	    $youbiJab = array('日','月','火','水','木','金','土','日' );
		$Emp_ID = $request->empid;
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		$yr = $request->selYear;
		if(!isset($request->filtervalue) || $request->filtervalue=="") {
			$request->filtervalue=1;
	        $disabledeng="disabled fb";
		} else if($request->filtervalue==1) {
	        $disabledeng="disabled fb";
		} elseif($request->filtervalue==2) {
	        $disabledjp="disabled fb";
		} elseif($request->filtervalue==3) {
	        $disabledsymbol="disabled fb";
		}
		$filtervalue = $request->filtervalue;
		$row = timesheet::empname($Emp_ID);
		$Name = ucwords(strtolower($row[0]->LastName)).".".ucwords(mb_substr($row[0]->FirstName,0,1,'UTF-8'));
		$number = cal_days_in_month(CAL_GREGORIAN, $mon, $yr);
		$previousDtMn = $yr."-".$mon;
		$j = 1;
		$displayArray = array();

		for ($i=1; $i <= $number; $i++) {
			$row = self::selectData($Emp_ID,$number,$yr,$mon,$i);
			$viewvalue = timesheet::viewedit($Emp_ID);
			if (!empty($row)) {
				$viewval["start1".$i] = $row[0]->starttime != "00:00:00" ? substr($row[0]->starttime, 0, 5): "";
				$viewval["end1".$i] = $row[0]->endtime != "00:00:00" ? substr($row[0]->endtime, 0, 5): "";
				$viewval["classification".$i] = $row[0]->section;
				$viewval["worktxt".$i] = $row[0]->workingplace;
				$viewval["remarks".$i] = $row[0]->remark;
			}
			$timestamp = strtotime($previousDtMn."-".$i);
			$date = new DateTime();
			$youbicolor = $youbiJab[date('N', $timestamp)];
			if ($youbicolor =="日" || $youbicolor =="土" ) {  //|| $row[0]->section =="9"
				$displayArray[$j]["leaveDisplay"] = 1;
			} else {
				$displayArray[$j]["leaveDisplay"] = 0;
			}
			$displayArray[$j]["i"] = $i;
			$displayArray[$j]["timestamp"] = $timestamp;
			$j++;
		}
		$sprow = timesheet::selectboxvalue($filtervalue);
		$update = timesheet::updatedetails($Emp_ID);
		return view('Timesheet.addedit',['request' => $request,
											'number'=> $number,
											'yr'=> $yr,
									  	 	'mon'=> $mon,
									  	 	'Emp_ID'=> $Emp_ID,
									  	 	'Name'=> $Name,
									  	 	'update'=> $update,
									  	 	'flagvalue'=> $flagvalue,
									  	 	'disabledeng' => $disabledeng,
											'disabledjp' => $disabledjp,
											'disabledsymbol' => $disabledsymbol,
									  	 	'value'=> $viewval,
									  	 	'displayArray'=> $displayArray,
									  	 	'timestamp'=> $timestamp,
									  	 	'youbi'=> $youbi,
									  	 	'sprow'=> $sprow,
									  	 	'row'=> $row]);
	}
	public static function downloadexcel(Request $request) {
		$downloadsal_query[] = array();
    	$get_emp_dtls[] =array();
    	$template_name = Config::get('constants.TIMESHEET_DOWNLOAD_PATH');
    	$name=date('Ymdhis')."_TS_h";
    	$excel_name=$request->empid."_". date('YmdHis');
		Excel::load($template_name, function($objPHPExcel) use($request) {
		    $objPHPExcel->setActiveSheetIndex(0);  //set first sheet as active
		    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		    $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
		    $row=Timesheet::empname($request->empid);
		    $Name=ucwords($row[0]->LastName).".".ucwords(substr($row[0]->FirstName,0,1));
		    $kanaName=$row[0]->KanaLastName;
			$Leave=0;
			$Half_Day_Leave=0;
			$Absent=0;
			$late=0;
			$Others=0;
			$Compensation_Holiday=0;
			$Compensatory_Holiday=0;
			$Special_Leave=0;
			$National_Holiday=0;
    		$non_work_starttime = "";
    		$non_work_endtime = "";
    		$hidden1 = "";
		    $hidden2 = "";
		    $hidden3 = "";
		    $hidden4 = "";
			$empid=$request->empid;
			$emp_name=$Name;
			$date = $request->selYear."-".$request->selMonth;
		    $workdata=explode('-', $date);
		    $number_of_day = cal_days_in_month(CAL_GREGORIAN, $workdata[1], $workdata[0]); // 31
		    $objPHPExcel->getActiveSheet()->setCellValue('C4',$workdata[1]);
		    $objPHPExcel->getActiveSheet()->setCellValue('B4',$workdata[0]);
		    $objPHPExcel->getActiveSheet()->setCellValue('O4',$empid);
		    $objPHPExcel->getActiveSheet()->setCellValue('Q4',$emp_name."\n".$kanaName);
		    $objPHPExcel->getActiveSheet()->getStyle('Q4')->getAlignment()->setWrapText(true);
		    $row_value_day=8;
		    $cellval=28;

		    for($i=1;$i<=$number_of_day;$i++){
		      str_pad($i,2,"0",STR_PAD_LEFT);
		      $workingdate = $workdata[0] ."-" . $workdata[1] ."-". str_pad($i,2,"0",STR_PAD_LEFT);
		      $alpha_day = date('D', strtotime($workingdate));

		      if($alpha_day=="Sun")
		      {
		        $al_day="日";
		      }
		      else if($alpha_day=="Mon")
		      {
		        $al_day="月";
		      }
		      else if($alpha_day=="Tue")
		      {
		        $al_day="火";
		      }
		      else if($alpha_day=="Wed")
		      {
		        $al_day="水";
		      }
		      else if($alpha_day=="Thu")
		      {
		        $al_day="木";
		      }
		      else if($alpha_day=="Fri")
		      {
		        $al_day="金";
		      }
		      else {
		        $al_day="土";
		      }

		      $res=timesheet::dataa($request->empid,$workingdate);
		        if($res){
		                if($res[0]->section == "1")
		                {
		                  $section="○";
		                  $Leave++;
		                }
		                else if($res[0]->section == "2")
		                {
		                  $section="△";
		                  $Half_Day_Leave++;
		                }
		                else if($res[0]->section == "3")
		                {
		                  $section="×";
		                  $Absent++;
		                }
		                else if($res[0]->section == "4")
		                {
		                  $section="▲";
		                  $late++;
		                }
		                else if($res[0]->section == "5")
		                {
		                  $section="□";
		                  $Others++;
		                }
		                else if($res[0]->section == "6")
		                {
		                  $section="●";
		                  $Compensation_Holiday++;
		                }
		                else if($res[0]->section == "7")
		                {
		                  $section="◎";
		                  $Compensatory_Holiday++;
		                }
		                else if($res[0]->section == "8")
		                {
		                  $section="☆";
		                  $Special_Leave++;
		                }
		                else if($res[0]->section == "9")
		                {
		                  $section="★";
		                  $National_Holiday++;
		                }
		                else{
		                  $section=" ";
		                }
		            $start_time=explode(':',$res[0]->starttime);
		            $end_time=explode(':',$res[0]->endtime);
		            $time_calculation=self::calculationdetails($res[0]->starttime,$res[0]->endtime,$res[0]->non_work_starttime,$res[0]->non_work_endtime,$al_day,$res[0]->section);
		            if(isset($start_time[1])) {
		              if($start_time[0]==00 && $start_time[1]==00) {
		                $startworktime=" ";
		              } else {
		                $startworktime=$start_time[0].":".$start_time[1];
		              }
		            } else {
		                $startworktime=" ";
		            }
		            if(isset($end_time[1])) {
		              if($end_time[0]==00 && $end_time[1]==00)
		              {
		                $endworktime=" ";
		              }
		              else
		              {
		                $endworktime=$end_time[0].":".$end_time[1];
		              }
		            } else {
		                $endworktime=" ";
		            }
		            //$res['non_work_starttime'],$res['non_work_endtime']
		            $non_start_time=explode(':',$res[0]->non_work_starttime);
		            $non_end_time=explode(':',$res[0]->non_work_endtime);
		            if(isset($non_start_time[1])) {
		              if ($non_start_time[0]==00 && $non_start_time[1]==00) {
		                $non_work_starttime="";
		              } else {
		                $non_work_starttime=$non_start_time[0].":".$non_start_time[1];
		              }
		            }
		            if(isset($non_end_time[1])) {
		              if($non_end_time[0]==00 && $non_end_time[1]==00) {
		                $non_work_endtime="";
		              } else {
		                $non_work_endtime=$non_end_time[0].":".$non_end_time[1];
		              }
		            }
		            //time_calculation[0]
		            if($time_calculation[0]=="00:00") {
		              $worktime="";
		            } else {
		              $worktime=$time_calculation[0];
		            }
		            if($time_calculation[1]=="00:00")
		            {
		              $overtime="";
		            }
		            else
		            {
		              $overtime=$time_calculation[1];
		            }
		            if($time_calculation[2]=="00:00")
		            {
		              $latetime="";
		            }
		            else
		            {
		              $latetime=$time_calculation[2];
		            }
		            if($time_calculation[3]=="00:00" || $time_calculation[3]==":")
		            {
		              $deductiontime="";
		            }
		            else
		            {
		              $deductiontime=$time_calculation[3];
		            }
		            //$arr=$ts_reg->calculation($workstart,$workend,$nonstart,$nonend);
		                 /* $hidden1=$hidden1+$time_calculation[4];
		                  $hidden2=$hidden2+$time_calculation[5];
		                  $hidden3=$hidden3+$time_calculation[6];
		                  $hidden4=$hidden4+$time_calculation[7];*/
		            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row_value_day,$i);
		            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row_value_day,$al_day);
		            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row_value_day,$section);
		            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row_value_day,$res[0]->workingplace);
		            
		            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row_value_day,$startworktime);

		            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row_value_day,$endworktime);

		            $objPHPExcel->getActiveSheet()->setCellValue('K'.$row_value_day,
		            	$non_work_starttime);

		            $objPHPExcel->getActiveSheet()->setCellValue('L'.$row_value_day,$non_work_endtime);
		            $objPHPExcel->getActiveSheet()->setCellValue('M'.$row_value_day,$worktime);
		            $objPHPExcel->getActiveSheet()->setCellValue('N'.$row_value_day,$overtime);
		            $objPHPExcel->getActiveSheet()->setCellValue('O'.$row_value_day,$latetime);
		            $objPHPExcel->getActiveSheet()->setCellValue('P'.$row_value_day,$deductiontime);
		            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_value_day,$res[0]->remark);
		          $actualminutes = $hidden1 % 60;
		          $actualhours = ($hidden1 - $actualminutes)/60;
		          $actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
		          $actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);
		          $actual=$actualhours.":".$actualminutes;
		          // FOR TOTAL
		        }
		        else{
		          $objPHPExcel->getActiveSheet()->setCellValue('B'.$row_value_day,$i);
		          $objPHPExcel->getActiveSheet()->setCellValue('C'.$row_value_day,$al_day);
		        }
		        //$array_time[$i]['deductionhour']
		        if($al_day == "土" || $al_day == "日"){
		          $objPHPExcel->getActiveSheet()->getStyle('B'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('B'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('C'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('C'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('D'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('D'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('E'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('E'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('I'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('I'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('J'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('J'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('K'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('K'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('L'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('L'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('M'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('M'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('N'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('N'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('O'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('O'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('P'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('P'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		          $objPHPExcel->getActiveSheet()->getStyle('Q'.$row_value_day)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		          $objPHPExcel->getActiveSheet()->getStyle('Q'.$row_value_day)->getFill()->getStartColor()->setRGB('A7D4DD');
		      }
		        $row_value_day++;
		      }

				 $actualminutes = $hidden1 % 60;
		              $actualhours = ($hidden1 - $actualminutes)/60;
		              $actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
		              $actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);
		              $actual=$actualhours.":".$actualminutes;
		              $overminutes = $hidden2 % 60;
		              $overhours = ($hidden2 - $overminutes)/60;
		              $overminutes=str_pad($overminutes,2,"0",STR_PAD_LEFT);
		              $overhours=str_pad($overhours,2,"0",STR_PAD_LEFT);
		              $over=$overhours.":".$overminutes;
		              $lateminutes = $hidden3 % 60;
		              $latehours = ($hidden3 - $lateminutes)/60;
		              $lateminutes=str_pad($lateminutes,2,"0",STR_PAD_LEFT);
		              $latehours=str_pad($latehours,2,"0",STR_PAD_LEFT);
		              $la=$latehours.":".$lateminutes;
		              $deductionminutes = $hidden4 % 60;
		              $detuctionhours = ($hidden4 - $deductionminutes) / 60;
		              $deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);
		              $detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
		              $dut=$detuctionhours.":".$deductionminutes;
		              $totaltime=$request->actualTotal;
		              $totalovertime=$request->overTotal;
		              $totalnighttime=$request->laTotal;
		              $totaldeductiontime=$request->dutTotal;
		      $objPHPExcel->getActiveSheet()->setCellValue('D40',$Leave."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('D41',$Half_Day_Leave."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('D42',$Absent."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('D43',$late."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('D44',$Others."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('G40',$Compensation_Holiday."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('G41',$Compensatory_Holiday."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('G42',$Special_Leave."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('G43',$National_Holiday."回");
		      $objPHPExcel->getActiveSheet()->setCellValue('M39',$totaltime);
		      $objPHPExcel->getActiveSheet()->setCellValue('N39',$totalovertime);
		      $objPHPExcel->getActiveSheet()->setCellValue('O39',$totalnighttime);
		      $objPHPExcel->getActiveSheet()->setCellValue('P39',$totaldeductiontime);
		      $objPHPExcel->getActiveSheet()->freezePane('B8');
		      //set color in head
			$cellvalue=array('B3','C3','O3','Q3','U3','B6','C6','D6','E6','I6','J7','I7','K6','K7','L7','M6','N6','O6','P6',
							 'Q6','B39','R48','R49','T49');
			for ($i = 0; $i < count($cellvalue); $i++) {
				$objPHPExcel->getActiveSheet()->getStyle($cellvalue[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle($cellvalue[$i])->getFill()->getStartColor()->setRGB('A7D4FF');
			}
			$objPHPExcel->getActiveSheet()->setSelectedCells('A1');
		      $todaydate=date('Ymd');
		      $filename = $todaydate."_TS_" . $Name . ".xls";
		      $objPHPExcel->getActiveSheet()->setTitle($workdata[0]."年".$workdata[1]."月");
		      /*$dat = date("Ymd");
		      $flpath=$dat.'.xls';*/
		      header('Content-Type: application/vnd.ms-excel');
		      header('Content-Disposition: attachment;filename="'.$filename.'"');
		      header('Cache-Control: max-age=0');
		    })->setFilename($excel_name)->download('xls');
	}
	public static function pdfview(Request $request) {
		$Emp_ID = $request->empid;
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		$yr = $request->selYear;
		$workingdate =$yr."-".$mon."-01";
		$row = timesheet::empname($Emp_ID);
		$Name = ucwords(strtolower($row[0]->LastName)).".".ucwords(mb_substr($row[0]->FirstName,0,1,'UTF-8'));
        $kanaName=$row[0]->KanaLastName;
    	$youbi=array('日','月','火','水','木','金','土','日' );
        $Leave=0;
        $Half_Day_Leave=0;
        $Absent=0;
        $late=0;
        $Others=0;
        $x_value =0;
        $y_value =0;
        $Compensation_Holiday=0;
        $Compensatory_Holiday=0;
        $Special_Leave=0;
        $National_Holiday=0;
        $empid=$Emp_ID;
        $emp_name=$Name;
        $workdata=explode('-', $workingdate);
        $number_of_day = cal_days_in_month(CAL_GREGORIAN, $workdata[1], $workdata[0]); // 31
		$pdf = new FPDI();
			$pageCount = $pdf->setSourceFile("resources/assets/uploadandtemplates/templates/Timesheetpdf.pdf");
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
		    // use the imported page
		    $pdf->useTemplate($templateId);
		    $pdf->AddMBFont( 'MS-Gothic', 'SJIS' );
    		$pdf->SetFont( 'MS-Gothic' ,'',7);
			$pdf->SetXY($pdf->GetX() + $x_value, $pdf->GetY() +  $y_value); 
		    $pdf->SetXY(7,38 );
		    $pdf->Write(6, iconv('UTF-8', 'SJIS',$workdata[0] ));
		    $pdf->SetXY(15,38 );
		    $pdf->Write(6, iconv('UTF-8', 'SJIS',$workdata[1] ));
		    $pdf->SetXY(128,38 );
		    $pdf->Write(6, iconv('UTF-8', 'SJIS',$Emp_ID ));
		    $pdf->SetXY(157,35.5 );
		    $pdf->Write(6, iconv('UTF-8', 'SJIS', $Name ));
		    $pdf->SetXY(157,40.5 );
		    $pdf->Write(6, iconv('UTF-8', 'SJIS', $kanaName ));

			$Y = 52;
			$current_y = 53;

			$pdf->SetLineWidth(.2);
			$pdf->SetWidths(array(14.78, 7.46, 34.8, 8.664, 8.594, 8.72, 8.67, 12.7, 11.5, 11.6, 11.5,57.1));
			$pdf->SetvAligns(array( 'C', 'C', 'C', 'C', 'C','C', 'C', 'C', 'C', 'C', 'C', 'L'));
			$pdf->SetAligns(array( 'C', 'L', 'L', 'C', 'C','C', 'C', 'C', 'C', 'C', 'C', 'L'));

			$x=6.8;
			$y=52.7;

			for ($i=1;$i<=$number_of_day;$i++) {
				$hidden1 = 0;
			    $hidden2 = 0;
			    $hidden3 = 0;
			    $hidden4 = 0;
				str_pad($i,2,"0",STR_PAD_LEFT);
	            $workingdate = $workdata[0] ."-" . $workdata[1] ."-". str_pad($i,2,"0",STR_PAD_LEFT);
	            $alpha_day = date('D', strtotime( $workingdate));

	            if($alpha_day=="Sun")
	            {
	                $al_day="日";
	            }
	            else if($alpha_day=="Mon")
	            {
	                $al_day="月";
	            }
	            else if($alpha_day=="Tue")
	            {
	                $al_day="火";
	            }
	            else if($alpha_day=="Wed")
	            {
	                $al_day="水";
	            }
	            else if($alpha_day=="Thu")
	            {
	                $al_day="木";
	            }
	            else if($alpha_day=="Fri")
	            {
	                $al_day="金";
	            }
	            else if($alpha_day=="Sat"){
	                $al_day="土";
	            }
	            
	            $res = timesheet::dataa($empid, $workingdate);
	            if($res){
	                if($res[0]->section == "1")
	                {
	                    $section=" ○";
	                    $Leave++;
	                }
	                else if($res[0]->section == "2")
	                {
	                    $section=" △";
	                    $Half_Day_Leave++;
	                }
	                else if($res[0]->section == "3")
	                {
	                    $section=" ×";
	                    $Absent++;
	                }
	                else if($res[0]->section == "4")
	                {
	                    $section=" ▲";
	                    $late++;
	                }
	                else if($res[0]->section == "5")
	                {
	                    $section=" □";
	                    $Others++;
	                }
	                else if($res[0]->section == "6")
	                {
	                    $section=" ●";
	                    $Compensation_Holiday++;
	                }
	                else if($res[0]->section == "7")
	                {
	                    $section=" ◎";
	                    $Compensatory_Holiday++;
	                }
	                else if($res[0]->section == "8")
	                {
	                    $section=" ☆";
	                    $Special_Leave++;
	                }
	                else if($res[0]->section == "9")
	                {
	                    $section=" ★";
	                    $National_Holiday++;
	                }
	                else{
	                    $section=" ";
	                }
	                 
	                $start_time=explode(':',$res[0]->starttime);
	                $end_time=explode(':',$res[0]->endtime);
	                $time_calculation=self::calculationdetails($res[0]->starttime,$res[0]->endtime,$res[0]->non_work_starttime,$res[0]->non_work_endtime,$al_day,$res[0]->section);
	                if($start_time[0]==00 && $start_time[1]==00)
	                {
	                    $startworktime=" ";
	                }
	                else
	                {
	                    $startworktime=$start_time[0].":".$start_time[1];
	                }
	                if($end_time[0]==00 && $end_time[1]==00)
	                {
	                    $endworktime=" ";
	                }
	                else
	                {
	                    $endworktime=$end_time[0].":".$end_time[1];
	                }
	                $non_start_time=explode(':',$res[0]->non_work_starttime);
	                $non_end_time=explode(':',$res[0]->non_work_endtime);
	                if($non_start_time==00 && $non_start_time==00)
	                {
	                    $non_work_starttime="";
	                }
	                else
	                {
	                    $non_work_starttime=$non_start_time[0].":".$non_start_time[0];
	                }
	                if($non_end_time[0]==00 && $non_end_time[0]==00)
	                {
	                    $non_work_endtime="";
	                }
	                else
	                {
	                    $non_work_endtime=$non_end_time[0].":".$non_end_time[1];
	                }
	                if($time_calculation[0]=="00:00")
	                {
	                    $worktime="";
	                }
	                else
	                {
	                    $worktime=$time_calculation[0];
	                }
	                if($time_calculation[1]=="00:00")
	                {
	                    $overtime="";
	                }
	                else
	                {
	                    $overtime=$time_calculation[1];
	                }
	                if($time_calculation[2]=="00:00")
	                {
	                    $latetime="";
	                }
	                else
	                {
	                    $latetime=$time_calculation[2];
	                }
	                if($time_calculation[3]=="00:00" || $time_calculation[3]==":")
	                {
	                    $deductiontime="";
	                }
	                else
	                {
	                    $deductiontime=$time_calculation[3];
	                }
                
                    $hidden1=$hidden1+$time_calculation[4];
                    $hidden2=$hidden2+$time_calculation[5];
                    $hidden3=$hidden3+$time_calculation[6];
                    $hidden4=$hidden4+$time_calculation[7];
                            
    				$pdf->SetXY($x,$y);
				    if($al_day == "土" || $al_day == "日" || trim($section) == "★"){

				        $pdf->SetFillColors(array('D4FFFF'));
				    	$pdf->SetFills(array(1,1,1,1,1,1,1,1,1,1,1,1));
				    }
				    if ($al_day == "土") {

				        $day = " ".$i.$al_day." ";
				    } else {

				        $day = $i.$al_day." ";
				    }

       				$pdf->Row(array(iconv('UTF-8', 'SJIS', $day), iconv('UTF-8', 'SJIS', $section), iconv('UTF-8', 'SJIS',
       								 $res[0]->workingplace), $startworktime,$endworktime, 
       				$non_work_starttime,$non_work_endtime, 
     								 $worktime, $overtime, $latetime,$deductiontime, 
     								 iconv('UTF-8', 'SJIS',  $res[0]->remark)));

       				$pdf->SetFills(array(0));
				    $y = $pdf->GetY();
				}
				else
				{

				    $pdf->SetXY($x,$y);
				    if($al_day == "土" || $al_day == "日" || trim($section) == "★"){
				        // $pdf->SetFillColors(array('#D4FFFF'));
					    // $pdf->SetFills(array(1,1,1,1,1,1,1,1,1,1,1,1));
					    //$pdf->SetFills(array(0));
				    }
				    if ($al_day == "土") {
				        $day = " ".$i.$al_day." ";
				    } else {
				        $day = $i.$al_day." ";
				    }
    				$pdf->Row(array( iconv('UTF-8', 'SJIS', $day), "", "", "","", "","", "", "", "","", ""));
    				$y = $pdf->GetY();
				}
				// $pdf->SetFills(array(0));
			}

			$actualminutes = $hidden1 % 60;
            $actualhours = ($hidden1 - $actualminutes)/60;
                    
            $actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
            $actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);

            $actual=$actualhours.":".$actualminutes;

            $overminutes = $hidden2 % 60;
            $overhours = ($hidden2 - $overminutes)/60;

            $overminutes=str_pad($overminutes,2,"0",STR_PAD_LEFT);
            $overhours=str_pad($overhours,2,"0",STR_PAD_LEFT);

            $over=$overhours.":".$overminutes;

            $lateminutes = $hidden3 % 60;
            $latehours = ($hidden3 - $lateminutes)/60;

            $lateminutes=str_pad($lateminutes,2,"0",STR_PAD_LEFT);
            $latehours=str_pad($latehours,2,"0",STR_PAD_LEFT);
            $la=$latehours.":".$lateminutes;

            $deductionminutes = $hidden4 % 60;
            $detuctionhours = ($hidden4 - $deductionminutes) / 60;
            $deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);
            $detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
            $dut=$detuctionhours.":".$deductionminutes;
            if($actual=="00:00"){
            	$totaltime=" ";
            }
            else
            {
            	$totaltime=$actual;
            }
            if($over=="00:00")
            {
            	$totalovertime=" ";
            }
            else{
            	$totalovertime=$over;
            }
            if($la=="00:00"){
            	$totalnighttime=" ";
            }
            else{
            	$totalnighttime=$la;
            }
            if($dut=="00:00"){
            	$totaldeductiontime=" ";
            }else{
            	$totaldeductiontime=$dut;
        	}

			$pdf->SetFont( 'MS-Gothic' ,'B',7);//8.72, 8.67, 12.7, 11.5, 11.6, 11.5,57.1
			$pdf->SetWidths(array(91.66, 12.7, 11.5, 11.6, 11.5,57.1));
			$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));

			$pdf->SetXY(6.8,$y);

			$rowDown[0] = "合  計";
			// $pdf->SetFillColors(array('#A7D4DD'));
			// $pdf->SetFills(array(1));
			$pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDown[0]), $totaltime, $totalovertime,
			$totalnighttime, $totaldeductiontime, ""));
			// $pdf->SetFills(array(0));
			$y = $pdf->GetY();
			$pdf->SetFont( 'MS-Gothic' ,'',7);
			$pdf->SetWidths(array(16, 7.4, 18, 7.5 ));
			$pdf->SetAligns(array('L', 'L', 'L', 'L'));
			$rowDown1[0] = "有休     ○";
			$rowDown1[1] = $Leave."回";
			$rowDown1[2] = "代休　 　 ●";
			$rowDown1[3] = $Compensation_Holiday."回";
			$rowDown1[4] = "半休　   △";
			$rowDown1[5] = $Half_Day_Leave."回";
			$rowDown1[6] = "振替休日  ◎";
			$rowDown1[7] = $Compensatory_Holiday."回";
			$rowDown1[8] = "欠勤     ×";
			$rowDown1[9] =  $Absent."回";
			$rowDown1[10] = "特別休暇  ☆";
			$rowDown1[11] = $Special_Leave."回";
			$rowDown1[12] = "遅刻早退 ▲";
			$rowDown1[13] = $late."回";
			$rowDown1[14] = "祝日　 　 ★";
			$rowDown1[15] = $National_Holiday."回";
			$rowDown1[16] = "その他   □";
			$rowDown1[17] = $Others."回";
			$rowDown1[18] = "";
			$rowDown1[19] = "";

			$k = 0;
			for ($l=0;$l<5;$l++) {
			    $pdf->SetXY(6.8, $y);
			    $pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDown1[$k+0]), iconv('UTF-8', 'SJIS', $rowDown1[$k+1]),
			     iconv('UTF-8', 'SJIS', $rowDown1[$k+2]), 
			                    iconv('UTF-8', 'SJIS', $rowDown1[$k+3])));
			    $y = $pdf->GetY();
			      if ($i == 0) {
			        $temp = $y;
			    }
			    $k = $k+3;
			    $k++;
			}

			$pdf->SetFont( 'MS-Gothic' ,'',7);
			$pdf->SetWidths(array(147.18));
			$pdf->SetAligns(array('L'));
			$pdf->SetXY(55.7, $y-22.5);//
			$rowDownRt[0] = "１．13:00-14：00の1時間・19：00-19：30の0.5時間・22：00-22：30の0.5時間・2：00-5：00の3時間は                                               
				休憩時間として、自動で作業時間から引かれます。
２．提出前に左下のチェックボタンを押して、○が出ていることを確認して下さい。
３．毎月5日までに提出して下さい。
			";
			$pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDownRt[0])));

			$current_y = $pdf->GetY();
			$current_x = $pdf->GetX();

			$printdate=date('Y/m/d')."版";
			$pdf->SetXY(6.8,$current_y);
			$pdf->Write(6, iconv('UTF-8', 'SJIS', $printdate));

			$current_y = $current_y+5;
			$pdf->SetXY(149.75, $current_y);
			$pdf->SetLineWidth(.1);
			$pdf->SetWidths(array(53));
			$pdf->SetvAligns(array('C'));
			$pdf->SetAligns(array('C'));
			// $pdf->SetFillColors(array('#A7D4DD'));
			// $pdf->SetFills(array(1));
			$rowDownSignature[0] = "承認印";
			$pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDownSignature[0])));
			$current_y = $pdf->GetY();
			$pdf->SetXY(149.75, $current_y);
			$pdf->SetLineWidth(.1);
			$pdf->SetWidths(array(26.5,26.5));
			$pdf->SetvAligns(array('C','C'));
			$pdf->SetAligns(array('L','L'));
			// $pdf->SetFills(array(1,1));
			$rowDownSignature[0] = "    取締役/部課長";
			$rowDownSignature[1] = "    ﾌﾟﾛｼﾞｪｸﾄﾘｰﾀﾞｰ";
			$pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDownSignature[0]),iconv('UTF-8', 'SJIS', $rowDownSignature[1])));
			// $pdf->SetFills(array(0));
			$current_y = $pdf->GetY();
			$pdf->SetXY(149.75, $current_y);
			$pdf->SetLineWidth(.1);
			$pdf->SetWidths(array(26.5,26.5));
			$pdf->SetvAligns(array('C','C'));
			$pdf->SetAligns(array('L','L'));
			$rowDownSignature[0] = "              
			 


			 ";
			$rowDownSignature[1] = "";
			$pdf->SetHeight(array(1,2));
			$pdf->Row(array(iconv('UTF-8', 'SJIS', $rowDownSignature[0]),iconv('UTF-8', 'SJIS', $rowDownSignature[1])));
		}
		$pdfname = date('Ymd')."_TS_".$Name;

		header('Content-disposition: attachment; filename=' . $pdfname . '.pdf');
		header("Content-Transfer-Encoding:  binary");

	}
	public static function uploadpopup(Request $request) {
		$EmpID = $request->empid;
		$yr = $request->year;
		$mon = $request->month;
		return view('Timesheet.uploadpopup',['request' => $request,
												'EmpID' => $EmpID,
												'yr' => $yr,
												'mon' => $mon]);
	}
	public static function uploadprocess(Request $request) {
		//print_r($_REQUEST);exit();
		$EmpID = $request->empid;
		$mon = str_pad($request->selMonth,2,"0",STR_PAD_LEFT);
		$yr = $request->selYear;
		$mainmenu = "staff";
		$tmpFile="";
		if(isset($request)) {
			//print_r($_REQUEST);exit();
			$row = timesheet::empname($EmpID);
			$Name = ucwords(strtolower($row[0]->LastName)).".".ucwords(mb_substr($row[0]->FirstName,0,1,'UTF-8'));
			$todaydate=date('YmdHis');
			$excel_name=$todaydate."".$Name;
			$ifile = $excel_name.".". self::getExtension($_FILES["xlfile"]["name"]);
			$destinationPath = 'resources/assets/uploadandtemplates/upload/TS_upload';
		      	if(!is_dir($destinationPath)) {
		          	mkdir($destinationPath, true);
		      	}
		      	chmod($destinationPath, 0777);
		      	$destinationPath=$destinationPath."/";
				$tmpFile = $destinationPath.$ifile;
				if(move_uploaded_file($_FILES['xlfile']['tmp_name'],$tmpFile)){
					chmod($tmpFile,0777);
					if (file_exists($tmpFile)) {
						$objPHPExcel = PHPExcel_IOFactory::createReader('Excel2007');
						$objPHPExcel = PHPExcel_IOFactory::load($tmpFile);
						$sheetcount=$objPHPExcel->getSheetCount();
						if($sheetcount<4) {
							//print_r($sheetcount);exit();
							$objPHPExcel->setActiveSheetIndex(0);  //set first sheet as active
							$cell_year=$objPHPExcel->getActiveSheet()->getCell('B3')->getValue();
							$cell_month=$objPHPExcel->getActiveSheet()->getCell('C3')->getValue();
							$cell_empid=$objPHPExcel->getActiveSheet()->getCell('O3')->getValue();
							$cell_name=$objPHPExcel->getActiveSheet()->getCell('Q3')->getValue();
							$cell_section=$objPHPExcel->getActiveSheet()->getCell('D8')
																		->getValue();
							$format1="G:i:s";
							if($cell_year!="年" || $cell_month!="月" || $cell_empid!="社員番号" || $cell_name!="氏名") {
								unlink($tmpFile);
								Session::flash("invalidTemplate"); 

							} else {

								$cell = $objPHPExcel->getActiveSheet()->getCell("C4");
								$InvDate= $cell->getValue();
								if(PHPExcel_Shared_Date::isDateTime($cell)) {
									$format="m";
								     $InvMon = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate));
								     $format="Y"; 
								     $InvYear = date($format, PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
									
									$number_of_day = cal_days_in_month(CAL_GREGORIAN, $InvMon, $InvYear); // 31
									$date=$InvYear."-".$InvMon."-".$number_of_day;
									$yearmonth=$InvYear."-".$InvMon;
	            					$res = timesheet::fnGetEmployeeTimeSheetDetail($EmpID, $yearmonth);
	            					//print_r($res);exit();
									if (!empty($res)) {
										Session::flash('success', 'Data Already Imported!'); 
										Session::flash('type', 'alert-danger');
									} else {
										$nowdate=date('Y-m-d G:i:s');
										//print_r($nowdate);exit();
										$name=$objPHPExcel->getActiveSheet()->getCell('Q4')->getValue();
										$cell_count=8;
										for($i=1;$i<=$number_of_day;$i++) {
											$workingdate = $InvYear ."-" . $InvMon ."-". str_pad($i,2,"0",STR_PAD_LEFT);
											$alpha_day = date('D', strtotime( $workingdate));
											
											$place=$objPHPExcel->getActiveSheet()->getCell('E'.$cell_count)->getValue();
											$remark=$objPHPExcel->getActiveSheet()->getCell('Q'.$cell_count)->getValue();
											$section_value=$objPHPExcel->getActiveSheet()->getCell('D'.$cell_count)->getValue();
											//○△×▲□●◎☆★
											if($section_value=="○"){
												$sectionvalue=1;
											}
											else if($section_value=="△"){
												$sectionvalue=2;
											}
											else if($section_value=="×"){
												$sectionvalue=3;
											}
											else if($section_value=="▲"){
												$sectionvalue=4;
											}
											else if($section_value=="□"){
												$sectionvalue=5;
											}
											else if($section_value=="●"){
												$sectionvalue=6;
											}
											else if($section_value=="◎"){
												$sectionvalue=7;
											}
											else if($section_value=="☆"){
												$sectionvalue=8;
											}
											else if($section_value=="★"){
												$sectionvalue=9;
											}
											else
											{
												$sectionvalue="";
											}

											$work_date=$InvYear."-".$InvMon."-".$i;
											$start_cell=$objPHPExcel->getActiveSheet()->getCell('I'.$cell_count);
											$start_value=$start_cell->getValue();
											if(PHPExcel_Shared_Date::isDateTime($start_cell)) {
											   $start_time = date($format1, self::ExcelToPHPCal($start_value));

											}
											$end_cell=$objPHPExcel->getActiveSheet()->getCell('J'.$cell_count);
											$end_value=$end_cell->getValue();
											if(PHPExcel_Shared_Date::isDateTime($end_cell)) {
											    $end_time = date($format1, self::ExcelToPHPCal($end_value));
											    $scell_split=explode('.', $end_value);
											   $etime_split=explode(':', $end_time);
											   if($scell_split[0] != 0)
											   {
											   		$end_time=(($scell_split[0]*24)+$etime_split[0]).":".$etime_split[1].":".$etime_split[2];
											   }
											   else
											   {
											   		$end_time=$end_time;
											   }
											}
											$non_start_cell=$objPHPExcel->getActiveSheet()->getCell('K'.$cell_count);
											$non_start_value=$non_start_cell->getValue();
											if(PHPExcel_Shared_Date::isDateTime($non_start_cell)) {
											    $non_start_time = date($format1, self::ExcelToPHPCal($non_start_value));
											    }
											$non_end_cell=$objPHPExcel->getActiveSheet()->getCell('L'.$cell_count);
											$non_end_value=$non_end_cell->getValue();
											if(PHPExcel_Shared_Date::isDateTime($non_end_cell)) 	{
											     $non_end_time = date($format1, self::ExcelToPHPCal($non_end_value));
											     $necell_split=explode('.', $non_end_value);
											   $netime_split=explode(':', $non_end_time);
											   if($necell_split[0] != 0)
											   {
											   		$non_end_time=(($necell_split[0]*24)+$netime_split[0]).":".$netime_split[1].":".$netime_split[2];
											   }
											   else
											   {
											   		$non_end_time=$non_end_time;
											   }
											}
											$cell_count++;
											if($start_time != "0:00:00" || $end_time != "0:00:00" || $non_start_time != "0:00:00" || $non_end_time != "0:00:00" || $sectionvalue != "") {
												$column_name = "emp_id,workdate,starttime,endtime,non_work_starttime,non_work_endtime,section,workingplace,created_by,created_date,remark,upload_path";
												$column_value = "'" . $EmpID . "','".$work_date."','".$start_time."','".$end_time."','".
																$non_start_time ."','".$non_end_time."','".$sectionvalue."','".$place."','".$Name."','".
																$nowdate."','".$remark."','".$ifile."'";
										$rtn = timesheet::fnInsertOldTimeSheetDetails($column_name, $column_value);
										//print_r($rtn);exit();
											if ($rtn) {
													Session::flash('success', 'uploadedUnSuccessfully!');
													Session::flash('type', 'alert-danger');
												} else {
													Session::flash('success', 'uploadedSuccessfully!'); 
													Session::flash('type', 'alert-success');
												}
												}
											}
											}
										}
										else
										{
											unlink($tmpFile);
											if (Session::get('languageval') != 'jp') {
												Session::flash('success', 'excelError!');
												Session::flash('type', 'alert-danger');
											} else {
												Session::flash('success', 'エクセルエラー。');
												Session::flash('type', 'alert-danger');
											}	
										}
									}
								}
								else
								{
									unlink($tmpFile);
									if (Session::get('languageval') != 'jp') {
										Session::flash('success', 'The File Must Contain a Single Sheet.!');
										Session::flash('type', 'alert-danger');
									} else {
										Session::flash('success', 'ファイルには、一枚のシートが含まれている必要があります。');
										Session::flash('type', 'alert-danger');
									}
								}
							}
							else {
								unlink($tmpFile);
								if (Session::get('languageval') != 'jp') {
									Session::flash('success', 'Sorry, There was a problem uploading your file!');
									Session::flash('type', 'alert-danger');
								} else {
									Session::flash('success', '申し訳ありませんが、あなたのファイルをアップロードする問題が発生しました。');
									Session::flash('type', 'alert-danger');
								}
							}
						}
					}
		return self::Timesheetview($request);
    }

	public static function getExtension($str) {
	    $i = strrpos($str,".");
	    if (!$i) { return ""; }
	    $l = strlen($str) - $i;
	    $ext = substr($str,$i+1,$l);
	    return $ext;
	}
	public static function ExcelToPHPCal($dateValue = 0) {
 		$ExcelBaseDate	= 1900;
		if ($ExcelBaseDate == 1900) {
			$myExcelBaseDate = 1900;
			//	Adjust for the spurious 29-Feb-1900 (Day 60)
			if ($dateValue < 60) {
				--$myExcelBaseDate;
			}
		} else {
			$myExcelBaseDate = 24107;
		}

		// Perform conversion
		if ($dateValue >= 1) {
			$utcDays = $dateValue - $myExcelBaseDate;
			$returnValue = round($utcDays * 86400);
			if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
				$returnValue = (integer) $returnValue;
			}
		} else {
			$hours = round($dateValue * 24);
			$mins = round($dateValue * 1440) - round($hours * 60);
			$secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
			$returnValue = (integer) gmmktime($hours, $mins, $secs);
		}
		// Return
		return $returnValue;
	}
}
  