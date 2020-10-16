<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Flightdetail;
use App\Model\Totalexp;
use DB;
use Input;
use Validator;
use Crypt;
use Redirect;
use Session;
use File;
use Carbon\Carbon;
use Mail;
use Config;
use View;
class FlightdetailController extends Controller
{
/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(Request $request) {
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if(Session::get('travelpaidmon') !=""){
			$request->selMonth = Session::get('travelpaidmon');
			$request->selYear = Session::get('travelpaidyr');
		}
		/*Function To Show Year Bar Starts Here */
		$date = flightdetail::fnGetLocalTravelCalenderBar($request);
		$total_yrs = array(); 
		if ($date[0] != "") {
			$prev_yrs = $date[0];
			$total_yrs1 = array_unique($date[1]);
			asort($total_yrs1);
			foreach ($total_yrs1 AS $key => $value) {
				array_push($total_yrs, $value);
			}
		} else {
			$prYrMn = explode('-', date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01')))));
			$prev_yrs = $prYrMn[1];
			array_push($total_yrs, $prYrMn[0]);
		}
		if(Session::get('pass_date') !=""){
		   $prYrMn =explode('-',Session::get('pass_date'));
		   $request->pass_date = Session::get('pass_date');
		   $cur_month = $prYrMn[1];
		   $cur_year= $prYrMn[0];
		} else {
		   $cur_month=date('m')-1;
		   $cur_year=date('Y');
		}
		$curtime = date('YmdHis');
		if ($cur_month == 0) {
			$cur_year = $cur_year - 1;
			$cur_month = 12;
		}
		if (isset($request->selMonth) && !empty($request->selMonth)) {
			$selectedMonth=$request->selMonth;
			$selectedYear=$request->selYear;
			$cur_month=$selectedMonth;
			$cur_year=$selectedYear;
		} else {
			$selectedMonth=$cur_month;
			$selectedYear=$cur_year;
		}
		$request->selMonth=$selectedMonth;
		$request->selYear=$selectedYear;
		$pass_date = $selectedYear . "-" . substr("0" . $selectedMonth, -2);
		$request->pass_date = $pass_date;
		$request->selMonth=substr($request->pass_date, 5, 2);
		$request->selYear=substr($request->pass_date, 0, 4);
		/* Function To Show Year Bar Ends Here */
		$travelamout = Flightdetail::fnfetchloctraveltot($pass_date);
		$employeesindex = Flightdetail::fnindex($request,$pass_date);
		return view('Flightdetail.index',compact('request',
												'prev_yrs',
												'cur_year',
												'cur_month',
												'total_yrs',
												'curtime',
												'pass_date',
												'travelamout',
												'employeesindex'));
	}
	/**
	* Validation For The Edit Flight Details
	* @author Rajesh
	* Created At 2018/06/12
	* Updated At 2018/1/13
	*/
	public function addeditvalidation(Request $request) {
		$rulesAppend = array();
		$rulesdate = array();
		$otherflight = array();
		$previousDay = "";
		$after = date('Y-m-d', strtotime(' +1 day'));
		if ($request->trip == 2) {
			$rulesAppend = array(
				'startplaceround' => 'required',
				'arrivalplaceround' => 'required|different:startplaceround',
				);
		}
		if ($request->confirmval == "index1") {
			$rulesdate = array(
				'InchargeId' => 'required',
				);
		}
		$commonrules = array(
			'startplace' => 'required',
			'ariivalplace' => 'required|different:startplace',
			'departuredate' => 'required|date_format:"Y-m-d"',
			'todate' => 'required|date_format:"Y-m-d"|curafterdate',
			'aircraft' => 'required',
			'totalamount' => 'required|min:1',
			'holiday' => 'required|numeric',
		);
		if ($request->aircraft == 999) {
			$otherflight = array(
				'otherflight' => 'required',
				);
		}
		$rules = $rulesdate+$commonrules+$rulesAppend+$otherflight;
		$customizedNames = array(
			'InchargeId' => 'Employee Id',
			'startplace' => 'Place',
			'ariivalplace' => 'Place',
			'departuredate' => 'Date',
			'todate' => 'Date',
			'holiday' => 'Holiday',
			'aircraft' => 'Aircraft Name',
			'totalamount' => 'Total Amount',
			'arrivalplaceround' => 'Place',
			'startplaceround' => 'Place',
			'otherflight' => 'Aircraft Name',
		);
		$validator = Validator::make($request->all(), $rules);
		$validator->setAttributeNames($customizedNames);
		if ($validator->fails()) {
			return response()->json($validator->messages(), 200);exit;
		} else {
			$success = true;
			echo json_encode($success);exit;
		}
	}
	/**
	* Register Screen Flight Details
	* @return Object to particular Flight View page
	* @author Rajesh
	* Created At 2018/06/10
	* Updated At 2018/1/13
	*/
	public function register(Request $request) {
		if (!isset($request->emp_Id)) {
			return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
		}
		/*Get The Reqired Air craft,Employee Name For Register*/
		$aircraftname = Flightdetail::fntravelaircraft($request);
		$empName = Flightdetail::fngetemployName($request);
		$mbemployee = Flightdetail::fnmbemployee($request);
		return view('Flightdetail.register',compact('request',
													'aircraftname',
													'empName',
													'mbemployee'));
	}
	/**
	* Get Holidays value
	* @author Mayandi
	* Created At 2018/06/21
	*/
	public function getholidays(Request $request) {
		$fromDate = $request->fromDate;
		$toDate = $request->enddate;
		$k = 1;
		$start = Carbon::parse($fromDate);
    	$end =  Carbon::parse($toDate);
    	//date subtract 
    	$totaldays = $end->diffInDays($start);
		$holidayArray = array();
		for ($i=0; $i <= $totaldays; $i++) {
			if ($i == 0) {
				$splitdate = explode('-', $fromDate);
				$dt = Carbon::create($splitdate[0], $splitdate[1], $splitdate[2], 0);
				$date = $dt;
			} else {
				//date add
				$date = $dt->addDays($k);
			}
			$result = flightdetail::fndateexist($date);
			//day formate
			$day = $date->format('D');
			if ($day == 'Sat' || $day == 'Sun' || !empty($result)) {
				$holidayArray[]++;
			}
		}
		$holiday = count($holidayArray);
		echo $totdays = ($totaldays+1) - $holiday;
		// echo json_encode($totdays);
		exit;
	}
	/**
	* Insert The Flight details
	* @author Rajesh
	* Created At 2018/06/10
	* Updated At 2018/1/11
	*/
	public function addedit(Request $request) {
		$insert = "" ;
		$fileNamedoc = "";
		$getflightName = "";
		$flightName = "";
		$name = "";
		$fileNamedocname = "";
		$originalname = "";
		$adminMail_id = "";
		$destinationPathdoc = "";
		$candidate_view = "";
		//document upload
		if ($request->confirmval == 'index1') {
			$emp_Id = $request->InchargeId;
		} else {
			$emp_Id = $request->emp_Id;
		}
		if (Input::hasFile('resume') != "") { 
			$file = $request->resume;
			$destinationPathdoc = '../Com.sathisys/ss/emp/doc/flightdetail';
			$extensiondoc = Input::file('resume')->getClientOriginalExtension();
			$fileNamedoc =$emp_Id.'_'.date('YmdHis').'.'.$extensiondoc;
			$fileNamedocname = $emp_Id.'.'.$extensiondoc;
			$originalname = Input::file('resume')->getClientOriginalName(); 
			if(!is_dir($destinationPathdoc)) {
				mkdir($destinationPathdoc, true);
			}
			chmod($destinationPathdoc, 0777);
			$file->move($destinationPathdoc,$fileNamedoc);
			chmod($destinationPathdoc."/".$fileNamedoc, 0777);
		} 
		//end document upload
		if ($request->aircraft == '999') {
			$insert = Flightdetail::fnregflightName($request);
		}
		// To get Flight  Name For Mail//
		if ($request->aircraft != "") {
			if ($request->aircraft == '999') {
				$flightid = $insert;
			} else {
				$flightid = $request->aircraft;
			}
			$getflightName = Flightdetail::fnflightnameformail($flightid);
		}
		if (isset($getflightName[0]->aircraft_name)) {
			$flightName = $getflightName[0]->aircraft_name;
		}
		//flight Name End
		// Employee Name Take For To Send Mail// 
		$employName = Flightdetail::fngetemployNamemail($emp_Id);
		if (isset($employName[0]->LastName)) {
			$name = $employName[0]->LastName;
		}
		//Employee Name End
		//Insert Process
		$adminMail_id = Config::get('constants.ADMIN_MAIL');
		$input = Input::all();
		$register = Flightdetail::fnregflightdetail($request,$insert,$fileNamedoc);
		//Mail Sending Process
		$candidate_view = View::make('Flightdetail/flightdetailtemplate',
									 	['request' => $request,
										'name'	=> $name,
										'flightName' => $flightName
										]);
		$contentsCandidate = $candidate_view->render();
		$content = "REG : Registration Successfully";
		Mail::send('Flightdetail/flightdetailtemplate', 
					[
						'request' => $request,
						'name'	=> $name,
						'flightName' => $flightName
					],
					function($message)
					use(
						$request,
						$input,
						$content,
						$destinationPathdoc,
						$fileNamedoc,
						$originalname,
						$adminMail_id) {
						$message->from('staff@microbit.co.jp','Microbit');
						$message->to($adminMail_id)->subject("Flight Register");
						if ($fileNamedoc != "") {
							$message->attach($destinationPathdoc.'/'.$fileNamedoc, 
									array(
										'as' => $originalname,
										'mime' => 'application/pdf')
								);
						}
					}
				);
		$insertmail = Flightdetail::fnmailinsertdetail($request,$adminMail_id,$contentsCandidate);
		//Mail sending Process Ends Here
		$request->departuredate =substr($request->departuredate, 0, 7);
			if($register) {
				Session::flash('message', 'Inserted and Mail Sent Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			if ($request->confirmval == "index1") {
				Session::flash('pass_date', $request->departuredate );
				return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
			}
				Session::flash('employ_Id', $request->emp_Id ); 
				Session::flash('pass_date', $request->departuredate );
		return Redirect::to('Flightdetail/view?mainmenu=flight_detail&time='.date('YmdHis'));
	}
	/**
	* Update The Edit Flight Details
	* @return Object to particular Edit page
	* @author Rajesh
	* Created At 2018/06/12
	* Updated At 2018/1/13
	*/
	public function editflightdetail(Request $request) {
		if (!isset($request->tableid)) {
			return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
		}
		$editfield = Flightdetail::fngetFlightDetail($request);
		$aircraftname = Flightdetail::fntravelaircraft($request);
		return view('Flightdetail.register',['request' => $request,
											'editfield'=>$editfield,
											'aircraftname'=>$aircraftname
										]);
	}
	/**
	* Update The Flight details
	* @author Rajesh
	* Created At 2018/06/12
	* Updated At 2018/1/13
	*/
	public function updateflight(Request $request) {
		$fileNamedoc = "";
		//document upload
			if (Input::hasFile('resume') != "") { 
				$file = $request->resume;
				$destinationPathdoc = '../Com.sathisys/ss/emp/doc/flightdetail';
				$extensiondoc = Input::file('resume')->getClientOriginalExtension();
				$fileNamedoc = $request->emp_Id.'.'.$extensiondoc;
				// Input::file('resume')->move($destinationPathdoc, $fileNamedoc);
				if(!is_dir($destinationPathdoc)) {
					mkdir($destinationPathdoc, true);
				}
				chmod($destinationPathdoc, 0777);
				$file->move($destinationPathdoc,$fileNamedoc);
				chmod($destinationPathdoc."/".$fileNamedoc, 0777);
			} elseif($request->res!="") {
				$fileNamedoc = $request->res;
			}
		//end document upload
		$updatepaidsate = Flightdetail::fnupdateflightdetail($request,$fileNamedoc);
		$request->departuredate =substr($request->departuredate, 0, 7);
		/*Update Session  Flash MEssage in Update Starts Here*/
		if($updatepaidsate) {
			Session::flash('message', 'Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Updated Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
			Session::flash('employ_Id', $request->emp_Id ); 
			Session::flash('pass_date', $request->departuredate );
		/*Update Session  Flash MEssage in Update Ends Here*/	
		return Redirect::to('Flightdetail/view?mainmenu=flight_detail&time='.date('YmdHis'));
	}
	/**
	* Return View To The Edit Flight Details
	* @return Object to particular Flight View page
	* @author Rajesh
	* Created At 2018/06/12
	* Updated At 2018/1/13
	*/
	public function view(Request $request) {
		if ($request->plimit == "") {
			$request->plimit = 50;
		}
		if (Session::get('employ_Id') !="" && Session::get('pass_date') != "") {
			$request->pass_date = Session::get('pass_date');
			$request->emp_Id = Session::get('employ_Id');
		}
		if (!isset($request->pass_date) || ($request->emp_Id=="")) {
			return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
		}
		/*Used for to update Submit record and Confirm record Starts Here*/
		if($request->subcnfm != ""){
			if($request->subcnfm == 1){
				$subcnfm = Flightdetail::fnsubmitrec($request);
				if($subcnfm) {
					Session::flash('message', 'Submitted Sucessfully!'); 
					Session::flash('type', 'alert-success');
				} else {
					Session::flash('type', 'Submitted Unsucessfully!'); 
					Session::flash('type', 'alert-danger'); 
				}
			} else {
				$subcnfm = Flightdetail::fnconfirmrec($request);
				if($subcnfm) {
					Session::flash('message', 'Confirmed Sucessfully!'); 
					Session::flash('type', 'alert-success');
				} else {
					Session::flash('type', 'Confirmed Unsucessfully!'); 
					Session::flash('type', 'alert-danger'); 
				}
			}
		}
		/*Used for to update Submit record and Confirm record Ends Here*/
		$singleviewdetail = Flightdetail::fnsingleview($request);
		$totalamtdetail = Flightdetail::fnexpdetTot($request);
		$request->selMonth=substr($request->pass_date, 5, 2);
		$request->selYear=substr($request->pass_date, 0, 4);
		$prevyears = Flightdetail::fnprevyears($request);
		$nextyears = Flightdetail::fnnextyears($request);
		$prevcount = count($prevyears);
		$nextcount = count($nextyears);
		if($prevcount == 0) {
			$preval = '0';
		} else {
			$preval = end($prevyears[0]);
		}
		if($nextcount == 0) {
			$nextval = '0';
		} else {
			$nextval = end($nextyears[0]);
		}
		return view('Flightdetail.view',['request' => $request,
									'singleviewdetail' => $singleviewdetail,
									'totalamtdetail'=> $totalamtdetail,
									'preval' => $preval,
									'prevcount' => $prevcount,
									'nextval' => $nextval,
									'nextcount' => $nextcount]);
	}
	/**
	* View Revert and Paid popup
	* @author kasthuri
	* Created At 2018/06/21
	*/
	public function revertpopup(Request $request) {
		return view('Flightdetail.revertpopup',compact('request'));
	}
	/**
	* Revert Process
	* @author kasthuri
	* Created At 2018/06/21
	*/
	public function revertregister(Request $request) {
		$revertreg = Flightdetail::fnrevertupdate($request);
		if($revertreg) {
			Session::flash('message', 'Reverted Sucessfully!'); 
			Session::flash('type', 'alert-success');
		} else {
			Session::flash('type', 'Reverted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		Session::flash('travelpaidmon', $request->selMonth);
		Session::flash('travelpaidyr', $request->selYear); 
		return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
	}
	
	/**
	* Paid Process
	* @author kasthuri
	* Created At 2018/06/22
	*/
	public function paidreg(Request $request) {
		$updatepaidstate = flightdetail::updatepaiddate($request);
		if($updatepaidstate) {
			Session::flash('message', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success');
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		Session::flash('travelpaidmon', $request->selMonth);
		Session::flash('travelpaidyr', $request->selYear); 
		return Redirect::to('Flightdetail/index?mainmenu=flight_detail&time='.date('YmdHis'));
	}
}
