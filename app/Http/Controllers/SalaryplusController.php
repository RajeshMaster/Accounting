<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\SalaryPlus;
use App\Model\Salary;
use App\Model\Invoice;
use App\Http\Common;
use Session;
use Carbon;
use Redirect;
use DateTime;

Class SalaryplusController extends Controller {
	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$getdetailsemp = SalaryPlus::fnGetdetailsfromemp();
		if ($getdetailsemp == 0) {
			$insertdetailsemp = SalaryPlus::fninsertdetailsfromemp($request);
		}
		//START PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$temp_count = SalaryPlus::getTempDetails($request);
		if ($temp_count == 0) {
			$empdetails = SalaryPlus::getEmpDetailsId($request);
		}
		//END PREVIOUS CURRENT YEAR MONTH RECORD CHECK AND REGISTER
		$year_month = array();
		$db_year_month = array();
		$dbrecord = array();
		$dbnext = array();
		$dbprevious = array();
		$account_val ="";
		$displayArray = array();
		$get_det = array();
		$g_query_tot = array();

			if (!isset($request->selMonth)) { 
				$date_month = date('Y-m', strtotime("last month"));
			} else { 
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
			}
			$last=date('Y-m', strtotime('last month'));
			$last1 = date($date_month , strtotime($last . " last month"));
			$lastdate = explode('-',$last1);
			$lastyear =$lastdate[0];
			$lastmonth =$lastdate[1];
			$request->selMonth = $lastmonth;
			$request->selYear = $lastyear;
			$g_accountperiod = SalaryPlus::fnGetAccountPeriod();
			$account_close_yr = $g_accountperiod[0]->Closingyear;
			$account_close_mn = $g_accountperiod[0]->Closingmonth;
			$account_period = intval($g_accountperiod[0]->Accountperiod);

			$splityear = explode('-', $request->previou_next_year); 
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
			$year_month_day = $current_year . "-" . $account_close_mn . "-01";
			$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
			$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
			$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
			
			$exp_query = SalaryPlus::fnGetmnthRecord($from_date, $to_date);
			foreach ($exp_query as $key => $res1) {
				$concat = $res1->year.'-'.$res1->month;
				//array_push($dbrecord, $res1['start_date']);
				array_push($dbrecord, $concat);
			}

			$lastMonthAsLink = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
				array_push($dbrecord, $lastMonthAsLink);
			$exp_query1 = SalaryPlus::fnGetmnthRecordPrevious($from_date);
			foreach ($exp_query1 as $key => $res2) {
				array_push($dbprevious, $res2->date);
			}
			$dbprevious = array_unique($dbprevious);

			$exp_query2 = SalaryPlus::fnGetmnthRecordNext($to_date);
			foreach ($exp_query2 as $key => $res3) {
				array_push($dbnext, $res3->date);
			}
			//START PREVIOUS AND FUTURE MONTH LINK WITHOUT DATA IN THE DB
			$fu_date = date('Y')."-0".(date('m')+1);
			$pre_date = date('Y')."-0".(date('m')-1);
			$cur_date = date('Y')."-0".(date('m'));
			if (!in_array($pre_date, $dbrecord)) {
				array_push($dbrecord, $pre_date);
			}
			if (!in_array($cur_date, $dbrecord)) {
				array_push($dbrecord, $cur_date);
			}
			if (!in_array($fu_date, $dbrecord)) {
				array_push($dbrecord, $fu_date);
			}
			$dbrecord = array_unique($dbrecord);
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$split_val = explode("-",$dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			
			$split_date = explode('-', $date_month);

			$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		$g_query = SalaryPlus::salaryDetail($request,$lastyear,$lastmonth,0);
		$g_query_tot = SalaryPlus::salaryDetailtot($request,$lastyear,$lastmonth);
		$g_query_totall = SalaryPlus::salaryDetailtotall($request,$lastyear,$lastmonth);
		$k = 0;
		foreach ($g_query as $key => $value) {
			$get_det[$k]['status'] = "";
			$exist_check = SalaryPlus::salaryExistcheck($request,$value->Emp_ID);
			if ($exist_check == 0 && $value->id != "") {
				$get_det[$k]['status'] = 0;
			} else if ($exist_check != 0 && $value->id != "") {
				$get_det[$k]['status'] = 1;
			}
			$get_det[$k]['id'] = $value->id;
			$get_det[$k]['Emp_ID'] = $value->Emp_ID;
			$get_det[$k]['FirstName'] = $value->FirstName;
			$get_det[$k]['LastName'] = $value->LastName;
			$get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName))
				. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
			$get_det[$k]['Basic'] = $value->Basic;
			$get_det[$k]['HrAllowance'] = $value->HrAllowance;
			$get_det[$k]['OT'] = $value->OT;
			if ($value->Leave == '0') {
				$get_det[$k]['Leave'] = '0';
			} else {
				$get_det[$k]['Leave'] = -$value->Leave;
			}

			$get_det[$k]['Bonus'] = $value->Bonus;
			if ($value->ESI == '0') {
				$get_det[$k]['ESI'] = '0';
			} else {
				$get_det[$k]['ESI'] = -$value->ESI;
			}

			if ($value->IT == '0') {
				$get_det[$k]['IT'] = '0';
			} else {
				$get_det[$k]['IT'] = -$value->IT;
			}
			$get_det[$k]['Travel'] = $value->Travel;
			$get_det[$k]['MonthlyTravel'] = $value->MonthlyTravel;
			$get_det[$k]['year'] = $value->year;
			$get_det[$k]['month'] = $value->month;
			$checkdet[$k]['checkedit'] = SalaryPlus::salaryDetailcheck($request,$value->Emp_ID);
				if (!empty($checkdet[$k]['checkedit'])) {
					$get_det[$k]['editcheck'] = "1";
				} else {
					$get_det[$k]['editcheck'] = "0";
				}
				if (isset($value->id)) {
					$g_query_sep_tot[$k] = SalaryPlus::salaryDetailseperatetot($request,$value->id);
					$get_det[$k]['Total'] = $g_query_sep_tot[$k]['Total'];
					$get_det[$k]['TotalAmount'] = str_replace(',','',$get_det[$k]['Total']);

					$g_querysalary=SalaryPlus::getsalaryDetail($request,$lastyear,$lastmonth);
					$salary[$k] = SalaryPlus::getsalary($request,$value->Emp_ID);
					foreach ($salary[$k] as $key => $value) {
						$get_det[$k]['salarychk'] = $value['Salary'];
					}
				}
			$k++;
		}
		return view('Salaryplus.index',['request' => $request,
										'g_query'=>$g_query,
										'g_query_tot'=>$g_query_tot,
										'g_query_totall'=>$g_query_totall,
										'get_det'=>$get_det,
										'account_val'=>$account_val,
										'account_period'=> $account_period,
										'year_month'=> $year_month,
										'db_year_month'=> $db_year_month,
										'date_month'=> $date_month,
										'dbnext'=> $dbnext,
										'dbprevious'=> $dbprevious,
										'last_year'=> $last_year,
										'current_year'=> $current_year]);
	}
	function salarypluspopup(Request $request) {
		$employeeUnselect = SalaryPlus::getAllEmpDetails($request);
		$employeeSelect = SalaryPlus::getAllFilteredEmpDetails($request);
		return view('Salaryplus.salarypluspopup',['employeeUnselect' => $employeeUnselect,
												'employeeSelect' => $employeeSelect,
												'request' => $request]);
	}
	function empselectprocess(Request $request) {
		$insert=SalaryPlus::InsertEmpFlrDetails($request);
		if($insert){
			Session::flash('success', 'Employees Selected Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		}else {
			Session::flash('type', 'Employees Selected Unsucessfully!!'); 
			Session::flash('type', 'alert-danger'); 
		}
		Session::flash('selMonth', $request->month); 
		Session::flash('selYear', $request->year); 
		$request->selected = "";
		$request->removed = "";
		return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public function view(Request $request) {
		if(Session::get('Emp_ID') !="" && Session::get('id') !="") {
			$request->id =  Session::get('id');
			$request->Emp_ID =  Session::get('Emp_ID');
			$request->firstname =  Session::get('firstname');
			$request->lastname =  Session::get('lastname');
			$request->editcheck =  Session::get('editcheck');
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
		}
		if (!isset($request->Emp_ID)) {
			return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$total = SalaryPlus::salaryDetailseperatetot($request,$request->id);
		$detedit = SalaryPlus::salaryplusview($request);
		return view('Salaryplus.view',['request' => $request,
											'total' => $total,
											'detedit' => $detedit[0]]);
	}
	public function addedit(Request $request) {
		return view('Salaryplus.addedit',['request' => $request]);
	}
	public function edit(Request $request) {
		if (!isset($request->Emp_ID)) {
			return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$detedit = SalaryPlus::salaryplusDetedit($request);
		$detedit[0]['leaveAmount'] = '-'.$detedit[0]['leaveAmount'];
		$detedit[0]['ESI'] = '-'.$detedit[0]['ESI'];
		$detedit[0]['IT'] = '-'.$detedit[0]['IT'];
		return view('Salaryplus.addedit',['request' => $request,
											'detedit' => $detedit[0]]);
	}
	public function addeditprocess(Request $request) {
		if($request->editcheck == 1) {
			$update = SalaryPlus::fnsalaryplusupd($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->id); 
		} else {
			$insert = SalaryPlus::fnsalaryplusadd($request);
			$getid = SalaryPlus::fngetid();
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $getid); 
		}
		$date = explode("-", $request->date);
		Session::flash('editcheck', '2'); 
		Session::flash('Emp_ID', $request->Emp_ID); 
		Session::flash('firstname', $request->firstname); 
		Session::flash('lastname', $request->lastname); 
		Session::flash('selMonth', $date[1]); 
		Session::flash('selYear', $date[0]);  
		Session::flash('prevcnt', $request->prevcnt); 
		Session::flash('nextcnt', $request->nextcnt); 
		Session::flash('account_val', $request->account_val); 
		Session::flash('previou_next_year', $request->previou_next_year);
		return Redirect::to('Salaryplus/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function getdatexist(Request $request){
		$dateexistcheck = SalaryPlus::fnGetdatecheck($request);
		$dateexistcheck = count($dateexistcheck);
		print_r($dateexistcheck);exit();
	}
	function addeditnew(Request $request) {
		$bankname=Salary::fetchbanknames($request);
		$register=Salary::fetchdetails($request);
		$salary = SalaryPlus::getsalary($request,$request->id);
		return view('Salaryplus.addeditsalaryplus',['bankname' => $bankname,
										'register' => $register,
										'salary' => $salary,
										'request' => $request]);
	}
	function addeditprocessnew(Request $request) {
		if($request->editflg == "1" || $request->editflg == "3") {
			$date = substr($request->txt_startdate,0,7);
			$insert = SalaryPlus::insertsalary($request,$date);
			$autoid = Salary::getautoincrement();
			$ids = $autoid -1;
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		Session::flash('ids', $ids);
		} else if($request->editflg == "2") {
			$date = substr($request->txt_startdate,0,7);
			$update = Salary::updatesalaryRec($request,$date);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!');
				Session::flash('type', 'alert-danger'); 
			}
		Session::flash('ids', $request->ids);
		}
		Session::flash('selMonth', $request->selMonth); 
		Session::flash('selYear', $request->selYear); 
		Session::flash('id', $request->id); 
		Session::flash('gobackflg', $request->gobackflg);
		Session::flash('bank', $request->bank); 
		Session::flash('empname', $request->empname);
		Session::flash('total', $request->total);
		return Redirect::to('Salaryplus/singleview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function singleview(Request $request) {
		if(Session::get('ids') !="") {
			$request->ids = Session::get('ids');
			$request->empname = Session::get('empname');
			$request->gobackflg = Session::get('gobackflg');
			$request->id = Session::get('id');
			$request->selMonth = Session::get('selMonth');
			$request->bankid = Session::get('bank');
			$request->selYear = Session::get('selYear');
			$request->total = Session::get('total');
		}
		if(!isset($request->ids)){
			return $this->index($request);
		}
		$singleview=Salary::fetchsingleview($request);
		return view('Salaryplus.Singleview',['singleview' => $singleview,
										'request' => $request]);
	}
	function editprocess(Request $request) {
		if (!isset($request->ids)) {
			return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankname=Salary::fetchbanknames($request);
		$detedit=Salary::fetcheditdetails($request);
		$salary = SalaryPlus::getsalary($request,$request->id);
		return view('Salaryplus.addeditsalaryplus',['bankname' => $bankname,
										'detedit' => $detedit,
										'salary' => $salary,
										'request' => $request]);
	}
	function multieditprocess(Request $request){
		$get_det = array();
		$g_query = SalaryPlus::salaryDetail($request,$request->selYear,$request->selMonth,1);
		$k = 0;
		foreach ($g_query as $key => $value) {
			if ($value->Basic == "") {
				$get_det[$k]['id'] = $value->id;
				$get_det[$k]['Emp_ID'] = $value->Emp_ID;
				$get_det[$k]['FirstName'] = $value->FirstName;
				$get_det[$k]['LastName'] = $value->LastName;
				$k++;
			}
		}
		return view('Salaryplus.multiedit',[
											'g_query'=>$g_query,
											'get_det'=>$get_det,
											'request' => $request]);
	}
	public function multiregister(Request $request) {	
		$query_date = $request->selYear.'-'.$request->selMonth.'-1';
		$date = new DateTime($query_date);
		// Last day of month
		$date->modify('last day of this month');
		$lastday = $date->format('Y-m-d');
		$request->date_hdn = $lastday;
			$insert = SalaryPlus::multiadd($request);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			$date = explode("-", $request->date_hdn);
			// $chkmonth =" " . substr("0" . $date);
			Session::flash('selMonth', $date[1]); 
			Session::flash('selYear', $date[0]);
			return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	function multipaymentscreen(Request $request) {
		if(!isset($request->selYear) && (!isset($request->selMonth))){
			return $this->index($request);
		}
		$bankname=SalaryPlus::fnGetBankDetails($request);
		$register = SalaryPlus::salaryDetail($request,$request->selYear,$request->selMonth,1);
		$k = 0;
		$get_multisalaryId = array();
		foreach ($register as $key => $value) {
			if ($value->Basic != "") {
				$exist_check = SalaryPlus::salaryExistcheck($request,$value->Emp_ID);
				if ($exist_check == 0) {
					$get_multisalaryId[$k]['id'] = $value->id;
					$get_multisalaryId[$k]['empNo'] = $value->Emp_ID;
					$get_multisalaryId[$k]['EmpName'] = ucwords(strtolower($value->LastName))
					. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
					$g_query = SalaryPlus::salaryDetailseperatetot($request,$value->id);
					$get_multisalaryId[$k]['Total'] = $g_query->Total;
					$k++;
				}
			}
		}
		$fileCnt=count($get_multisalaryId);
		return view('Salaryplus.paymentadd',['bankname' => $bankname,
										'register' => $register,
										'get_multisalaryId' => $get_multisalaryId,
										'fileCnt' => $fileCnt,
										'request' => $request]);
	}

	function multiaddeditprocess(Request $request) {
		$day = 0;
		$splitdates = explode("-", $request->txt_startdate);
		for ($i=0; $i <= $request->fileCnt; $i++) {
			$empid = 'empNo_'.$i;
			$salary = 'salary'.$i;
			$charge = 'charge'.$i;
			$request->day = $i;
			// ADD
			if ($request->multiflg == 1) {
				if ($request->$salary != "") {
					$date = substr($request->txt_startdate,0,7);
					$insert = Salary::salarymultireg($request,$day,$date);
					if($insert) {
						Session::flash('success', 'Inserted Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Inserted Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				}
				Session::flash('multiflg', $request->multiflg); 
			} else {
				if ($request->$salary != "") {
					$date = substr($request->txt_startdate,0,7);
					$update = Salary::salarymultiupd($request,$day,$date);
					if($update) {
						Session::flash('success', 'Updated Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Updated Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				}
				Session::flash('multiflg', 2); 
			}
		}
		Session::flash('selMonth', $splitdates[1]); 
		Session::flash('selYear', $splitdates[0]); 
		return Redirect::to('Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

}