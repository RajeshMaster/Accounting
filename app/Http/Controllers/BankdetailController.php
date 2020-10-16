<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bankdetail;
use App\Http\Common;
use DB;
use Input;
use Redirect;
use Session;
use Illuminate\Support\Facades\Validator;

class BankdetailController extends Controller {
	function index(Request $request) { 
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
			$request->page = 1;
		}
		$get_det1 = array();
		$j = 0;
		$bankdetailindex = Bankdetail::bankindex($request)->paginate($request->plimit);
		$bankdetailindex1 = Bankdetail::bankindex1($request);
		// print_r($bankdetailindex1);exit();
		foreach ($bankdetailindex1 as $key => $value) {
			$get_det1[$j][4]=$value->balance;
			$j++;
		}

		$i = 0;
		$filcnt = 0;
		$filcnt = count($bankdetailindex);
		$get_det = array();
		$get_dets = array();
		$get_bankdet = array();
		foreach($bankdetailindex as $key=>$data) {
			$get_det[$i]['id'] = $data->id;
			$get_det[$i][0] = $data->AccNo;
			$get_det[$i][1]=$data->banknm;
			$get_det[$i][2]=$data->brnchnm;
			$get_det[$i][3]=$data->bankid;
			$get_det[$i][4]=$data->balance;
			$get_det[$i][5]=$data->processFlg;
			$get_det[$i][6]=$data->startDate;
			$get_det[$i][7]=$data->bnkid;
			$get_det[$i][8]=$data->brnchid;
			$get_det[$i][9]=$data->balbankid;
			$bankid =$get_det[$i][7];
			$branchid = $get_det[$i][8];
			$accno = $get_det[$i][0];
			$accnoid = $get_det[$i][3];
			$startdate = $get_det[$i][6];
			$curDate= date('Y-m-d');
			$get_bankdet[$i]['blnc'] = str_replace(",", "", $get_det[$i][4]);
			$g_query1 = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","","","",1);
				foreach($g_query1 as $key=>$val1) {
					$get_bankdet[$i]['debit'] = -str_replace(",", "", $val1->debit);
					$get_bankdet[$i]['pamt'] = str_replace(",", "", $val1->pamt);
					$get_bankdet[$i]['credit'] = -str_replace(",", "", $val1->credit);
					$get_bankdet[$i]['tamt'] = -str_replace(",", "", $val1->tamt);
					$get_bankdet[$i]['fee'] = -str_replace(",", "", $val1->fee);
					$get_bankdet[$i]['lamt'] = -str_replace(",", "", $val1->lamt);
					$get_bankdet[$i]['lfee'] = -str_replace(",", "", $val1->lfee);
					$get_bankdet[$i]['samt'] = -str_replace(",", "", $val1->samt);
					$get_bankdet[$i]['sfee'] = -str_replace(",", "", $val1->sfee);
					$get_bankdet[$i]['blnc'] =  $get_bankdet[$i]['blnc']+$get_bankdet[$i]['debit'] + 
												$get_bankdet[$i]['pamt'] + $get_bankdet[$i]['credit'] +
												$get_bankdet[$i]['tamt'] + $get_bankdet[$i]['fee']+
												$get_bankdet[$i]['lamt'] +$get_bankdet[$i]['lfee']+
												$get_bankdet[$i]['samt'] +$get_bankdet[$i]['sfee'] ;
					$get_bankdet[$i]['chk_flg'] = $val1->chk_flg;
				}
			$i++;
		}
		$fileCnt=count($get_det);
		$i =0;
		$get_dets = array();
		$get_bankdets = array();
		foreach($bankdetailindex as $key=>$value) {
			$get_dets[$i][0] = $value->AccNo;
			$get_dets[$i][3]=$value->bankid;
			$get_dets[$i][4]=$value->balance;
			$get_dets[$i][6]=$value->startDate;
			$get_dets[$i][7]=$value->bnkid;
			$get_dets[$i][8]=$value->brnchid;
			$bankid =$get_dets[$i][7];
			$branchid = $get_dets[$i][8];
			$accno = $get_dets[$i][0];
			$accnoid = $get_dets[$i][3];
			$startdate = $get_dets[$i][6];
			$curDate= date('Y-m-d');
			$get_bankdets[$i]['blnc'] = str_replace(",", "", $get_dets[$i][4]);
			$g_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","","","",1);
				foreach($g_query as $key=>$val) {
					$get_bankdets[$i]['debit'] = -str_replace(",", "", $val->debit);
					$get_bankdets[$i]['pamt'] = str_replace(",", "", $val->pamt);
					$get_bankdets[$i]['credit'] = -str_replace(",", "", $val->credit);
					$get_bankdets[$i]['tamt'] = -str_replace(",", "", $val->tamt);
					$get_bankdets[$i]['fee'] = -str_replace(",", "", $val->fee);
					$get_bankdets[$i]['lamt'] = -str_replace(",", "", $val->lamt);
					$get_bankdets[$i]['lfee'] = -str_replace(",", "", $val->lfee);
					$get_bankdets[$i]['samt'] = -str_replace(",", "", $val->samt);
					$get_bankdets[$i]['sfee'] = -str_replace(",", "", $val->sfee);
					$get_bankdets[$i]['blnc'] =  $get_bankdets[$i]['blnc']+$get_bankdets[$i]['debit'] + 
												$get_bankdets[$i]['pamt'] + $get_bankdets[$i]['credit'] +
												$get_bankdets[$i]['tamt'] + $get_bankdets[$i]['fee']+
												$get_bankdets[$i]['lamt'] +$get_bankdets[$i]['lfee']+
												$get_bankdets[$i]['samt'] +$get_bankdets[$i]['sfee'] ;
					
				}
			$i++;
		}
		$s =0;
		$get_dets2 = array();
		$get_bankdets2 = array();
		foreach($bankdetailindex1 as $key=>$value3) {
			$get_dets2[$s][0] = $value3->AccNo;
			$get_dets2[$s][3]=$value3->bankid;
			$get_dets2[$s][4]=$value3->balance;
			$get_dets2[$s][6]=$value3->startDate;
			$get_dets2[$s][7]=$value3->bnkid;
			$get_dets2[$s][8]=$value3->brnchid;
			$bankid =$get_dets2[$s][7];
			$branchid = $get_dets2[$s][8];
			$accno = $get_dets2[$s][0];
			$accnoid = $get_dets2[$s][3];
			$startdate = $get_dets2[$s][6];
			$curDate= date('Y-m-d');
			$get_bankdets2[$s]['blnc'] = str_replace(",", "", $get_dets2[$s][4]);
			$g_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","","","",1);
				foreach($g_query as $key=>$val3) {
					$get_bankdets2[$s]['debit'] = -str_replace(",", "", $val3->debit);
					$get_bankdets2[$s]['pamt'] = str_replace(",", "", $val3->pamt);
					$get_bankdets2[$s]['credit'] = -str_replace(",", "", $val3->credit);
					$get_bankdets2[$s]['tamt'] = -str_replace(",", "", $val3->tamt);
					$get_bankdets2[$s]['fee'] = -str_replace(",", "", $val3->fee);
					$get_bankdets2[$s]['lamt'] = -str_replace(",", "", $val3->lamt);
					$get_bankdets2[$s]['lfee'] = -str_replace(",", "", $val3->lfee);
					$get_bankdets2[$s]['samt'] = -str_replace(",", "", $val3->samt);
					$get_bankdets2[$s]['sfee'] = -str_replace(",", "", $val3->sfee);
					$get_bankdets2[$s]['blnc'] =  $get_bankdets2[$s]['blnc']+$get_bankdets2[$s]['debit'] + 
												$get_bankdets2[$s]['pamt'] + $get_bankdets2[$s]['credit'] +
												$get_bankdets2[$s]['tamt'] + $get_bankdets2[$s]['fee']+
												$get_bankdets2[$s]['lamt'] +$get_bankdets2[$s]['lfee']+
												$get_bankdets2[$s]['samt'] +$get_bankdets2[$s]['sfee'] ;
					
				}
			$s++;
		}
		$fileCnts=count($get_dets2);
		$bal = "";
		$acc = "";
		for ($cnt=0; $cnt<$fileCnts;$cnt++) {
			if($get_det1[$cnt][4]!=""){
				$bal = $bal+str_replace(",", "", $get_bankdets2[$cnt]['blnc']);
				$acc = number_format($bal);
			}
		}
		// print_r($get_det);exit();
		return view('Bankdetails.index',['bal' => $acc,
								'get_bankdets' => $get_bankdets,
								'get_bankdet' => $get_bankdet,
								'get_det' => $get_det,
								'get_dets' => $get_dets,
								'filcnt' => $filcnt,
								'index' => $bankdetailindex,
								'request' => $request]);
	}
	function Viewlist(Request $request) {
		if(Session::get('id') !="") {
			$request->id = Session::get('id');
			$date_month = Session::get('date_month');
			$request->bankids = Session::get('bankids');
			$request->branchids = Session::get('branchids');
			$request->accno = Session::get('accno');
			$request->bankid = Session::get('bankid');
			$request->startdate = Session::get('startdate');
			$request->balbankid = Session::get('balbankid');
			$request->bankname = Session::get('bankname');
			$request->branchname = Session::get('branchname');
			$request->selMonth = Session::get('selMonth');
			$request->selYear = Session::get('selYear');
			$request->checkflg = Session::get('checkflg');
		}
		if (!isset($request->balbankid)) {
			return Redirect::to('Bankdetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		if(Session::get('editflg') =="1") {
			$date_month = Session::get('startdate');
		}
		$from_date = "";
		$to_date = "";
		$baln = 0;
		$balance = array();
		$previous_date = "";
		$j = 0;
		$i = 0;
		$date_month = "";
		$total = 0;
		$get_bankdet = array();
		$get_mnsub = array();
		$bal = array();
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if ($request->page=="") {
			$request->page = 1;
		}
		$disablednormal="";
		$disableddetail="";
		if($request->detfilter == 0) {
			$disablednormal ="disabled fwb black";
		}
		if($request->detfilter == 1) {
			$disableddetail ="disabled fwb black";
		}
		$g_accountperiod = Bankdetail::fnGetAccountPeriodBK($request);
			$account_close_yr = $g_accountperiod[0]->Closingyear;
			$account_close_mn = $g_accountperiod[0]->Closingmonth;
			$account_period = intval($g_accountperiod[0]->Accountperiod);
			$bankid =$request->bankids;
			$branchid = $request->branchids;
			$accno = $request->accno;
			$accnoid = $request->bankid;
			$startdate = $request->startdate;
			$balbankid = $request->balbankid;
			$curDate= date('Y-m-d');
		$exp_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,$from_date,$to_date,"","",1);
		$dbrecord = array();
		foreach ($exp_query as $key => $value) {
			$dbrecord[]=$value->date;
		}
		$dbrecord = array_unique($dbrecord); 
			$dbyears = array();
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$dbyear =substr($dbrecordvalue,0,4);
			$dbyears[$dbyear] =$dbyear;
			}
			$db_year_month = array();
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$lastdbrecord = substr($dbrecordvalue,0,7);
			}
			if (empty($request->selYear) && !empty($lastdbrecord)) {
				$date_month = $lastdbrecord;
				$selMonth =substr($date_month,5,2);
				$selYear=substr($date_month,0,4);
			}else if(empty($request->selYear) && empty($lastdbrecord)){
				$selMonth =date('m');
				$selYear=date('Y');
			}else if (empty($request->selMonth)) {
				$date_month = $request->selYear;
				$selYear = $request->selYear;
			} else {
				$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
				$selYear = $request->selYear;
				$selMonth = $request->selMonth;
			}
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				if(empty($selMonth)){
					$dbrecords = substr($dbrecordvalue,0,4);
				}else{
					$dbrecords = substr($dbrecordvalue,0,7);
				}
				if($dbrecords<$date_month){
					$previous_date=$dbrecords;
				}
				$split_val = explode("-", $dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			$splityear = explode('-', $request->previou_next_year);
			if ($request->previou_next_year != "") {
				if (intval($splityear[1]) > $account_close_mn) {
					$last_year = intval($splityear[0]);
					$current_year = intval($splityear[0]) + 1;
				} else {
					$last_year = intval($splityear[0]) - 1;
					$current_year = intval($splityear[0]);
				}
			} else if ($selYear) {
				if ($selMonth > $account_close_mn) {
					$current_year = intval($selYear) + 1;
					$last_year = intval($selYear);
				} else {
					$current_year = intval($selYear);
					$last_year = intval($selYear) - 1;
				}
			} else {
				if ($selMonth > $account_close_mn) {
				    $current_year = $selYear+1;
					$last_year = $selYear;
				} else {
				    $current_year = $selYear;
					$last_year = $selYear - 1;
				}
			}

			$year_month1 = array();
			if ($account_close_mn == 12) {
				for ($i = 1; $i <= 12; $i++) {
					$year_month1[$current_year][$i] = $i;
				}
			} else {
				for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
					$year_month1[$last_year][$i] = $i;
				}

				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month1[$current_year][$i] = $i;
				}
			}
		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
		$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";
		$bktr_query1 = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,$from_date,"","","",1);
		$dbprevious = array();
			foreach ($bktr_query1 AS $key => $value) {
				array_push($dbprevious, $value->date);
			}
		$bktr_query2 = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"",$to_date,"","",1);
		$dbnext = array();
			foreach ($bktr_query2 AS $key => $value) {
				array_push($dbnext, $value->date);
			}
		$split_date = explode('-', $date_month);
		$account_val = Common::getAccountPeriod($year_month1, $account_close_yr, $account_close_mn, $account_period);
		$g_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","",$date_month,"");
		$g_queryhdn = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","",$date_month,"",1);
		$query = Bankdetail::balance($balbankid,$startdate);
		foreach ($query AS $key => $value) {
			$balance['balance']=$value->balance;
		}
		$sql_cnt=0;
		$sql_cnt=count($g_query);
		$i = 0;
		foreach ($g_query as $key => $value) {
			$get_bankdet[$i]['date'] = $value->date;
			$get_bankdet[$i]['debit'] = str_replace(",", "", $value->debit);
			$get_bankdet[$i]['pamt'] = str_replace(",", "", $value->pamt);
			$get_bankdet[$i]['credit'] = str_replace(",", "", $value->credit);
			$get_bankdet[$i]['loanamt'] = str_replace(",", "", $value->loanamt);
			$get_bankdet[$i]['tamt'] = -str_replace(",", "", $value->tamt);
			$get_bankdet[$i]['lamt'] = -str_replace(",", "", $value->lamt);
			$get_bankdet[$i]['lfee'] = -str_replace(",", "", $value->lfee);
			$get_bankdet[$i]['samt'] = -str_replace(",", "", $value->samt);
			$get_bankdet[$i]['sfee'] = -str_replace(",", "", $value->sfee);
			$get_bankdet[$i]['fname'] = $value->fname;
			$get_bankdet[$i]['lname'] = $value->lname;
			$get_bankdet[$i]['emp_ID'] = $value->emp_ID;
			$get_bankdet[$i]['cmpny_name'] = $value->cmpny_name;
			$get_bankdet[$i]['mnsub'] = $value->mnsub;
			$get_bankdet[$i]['susub'] = $value->susub;
			$get_bankdet[$i]['remarks'] = $value->remarks;
			$get_bankdet[$i]['fee'] = -str_replace(",", "", $value->fee);
			$get_bankdet[$i]['chk_flg'] = $value->chk_flg;
			$get_bankdet[$i]['paymentsam'] = $value->paymentsam;
			$get_bankdet[$i]['idcheck'] = $value->idcheck;
			$i++;
		}
		$fileCnt=count($get_bankdet);
		for ($cnt=0; $cnt<$fileCnt;$cnt++) {
			$query1 =Bankdetail::mainsub($get_bankdet[$cnt]['mnsub']);
				foreach ($query1 AS $key => $value) {
					$get_mnsub[$cnt]['main_eng'] = $value->main_eng;
					$get_mnsub[$cnt]['main_jap'] = $value->main_jap;
				}
			$query2 =Bankdetail::susub($get_bankdet[$cnt]['susub']);
				foreach ($query2 AS $key => $value) {
					$get_mnsub[$cnt]['sub_eng'] = $value->sub_eng;
					$get_mnsub[$cnt]['sub_jap'] = $value->sub_jap;
				}
		}
		$balanc =str_replace(",", "", $balance['balance']);
		$baln=$balanc;
			$get_bankdet_s = array();
		if($previous_date!=""){
			$p_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","","",$previous_date,1)/*->paginate($request->plimit)*/;
			$m=0;
			foreach ($p_query as $key => $value) {
				$get_bankdet_s[$m]['debitp'] = -str_replace(",", "", $value->debit);
				$get_bankdet_s[$m]['pamtp'] = str_replace(",", "", $value->pamt);
				$get_bankdet_s[$m]['creditp'] = -str_replace(",", "", $value->credit);
				$get_bankdet_s[$m]['tamtp'] = -str_replace(",", "", $value->tamt);
				$get_bankdet_s[$m]['lamtp'] = -str_replace(",", "", $value->lamt);
				$get_bankdet_s[$m]['lfeep'] = -str_replace(",", "", $value->lfee);
				$get_bankdet_s[$m]['feep'] = -str_replace(",", "", $value->fee);
				$get_bankdet_s[$m]['sfeep'] = -str_replace(",", "", $value->sfee);
				$get_bankdet_s[$m]['samtp'] = -str_replace(",", "", $value->samt);
				$m++;
			}
			$fileCnts=count($get_bankdet_s);
			for ($cnt=0; $cnt<$fileCnts;$cnt++) {
				$balanc =$balanc + $get_bankdet_s[$cnt]['debitp'] + $get_bankdet_s[$cnt]['pamtp'] + $get_bankdet_s[$cnt]['creditp'] + $get_bankdet_s[$cnt]['tamtp'] + $get_bankdet_s[$cnt]['feep'] + $get_bankdet_s[$cnt]['lamtp'] + $get_bankdet_s[$cnt]['lfeep'] + $get_bankdet_s[$cnt]['samtp'] + $get_bankdet_s[$cnt]['sfeep'];
				$balanc."</br>";
			}
		}
		$balances=$balanc;
		$m=0;
		$get_bankdet_new = array();
		foreach ($g_queryhdn as $key => $value) {
			$get_bankdet_new[$m]['debits'] = -str_replace(",", "", $value->debit);
			$get_bankdet_new[$m]['pamts'] = str_replace(",", "", $value->pamt);
			$get_bankdet_new[$m]['credits'] = -str_replace(",", "", $value->credit);
			$get_bankdet_new[$m]['loanamt'] = str_replace(",", "", $value->loanamt);
			$get_bankdet_new[$m]['tamts'] = -str_replace(",", "", $value->tamt);
			$get_bankdet_new[$m]['lamts'] = -str_replace(",", "", $value->lamt);
			$get_bankdet_new[$m]['lfees'] = -str_replace(",", "", $value->lfee);
			$get_bankdet_new[$m]['fees'] = -str_replace(",", "", $value->fee);
			$get_bankdet_new[$m]['samts'] = -str_replace(",", "", $value->samt);
			$get_bankdet_new[$m]['sfees'] = -str_replace(",", "", $value->sfee);
			$m++;
		}
		$fileCnts=count($get_bankdet_new);
		$x = "";
		$balx = "";
		for ($cnt=0; $cnt<$fileCnts;$cnt++) {
			$balances =$balances + $get_bankdet_new[$cnt]['debits'] + $get_bankdet_new[$cnt]['pamts'] + $get_bankdet_new[$cnt]['credits'] + $get_bankdet_new[$cnt]['loanamt'] + $get_bankdet_new[$cnt]['tamts'] + $get_bankdet_new[$cnt]['fees'] + $get_bankdet_new[$cnt]['lamts'] + $get_bankdet_new[$cnt]['lfees'] + $get_bankdet_new[$cnt]['samts'] + $get_bankdet_new[$cnt]['sfees'];
			$bal[$cnt]=$balances;
		}
		$balanca =str_replace(",", "", $balance['balance']);
		$b_query = Bankdetail::getbankdetails($request,$bankid,$branchid,$accno,$accnoid,$startdate,$curDate,"","","","",1);
		$cnt = 0;
		foreach ($b_query as $key => $value) {
			$get_bankdet[$cnt]['debita'] = -str_replace(",", "", $value->debit);
			$get_bankdet[$cnt]['pamta'] = str_replace(",", "", $value->pamt);
			$get_bankdet[$cnt]['credita'] = -str_replace(",", "", $value->credit);
			$get_bankdet[$cnt]['tamta'] = -str_replace(",", "", $value->tamt);
			$get_bankdet[$cnt]['lamta'] = -str_replace(",", "", $value->lamt);
			$get_bankdet[$cnt]['lfeea'] = -str_replace(",", "", $value->lfee);
			$get_bankdet[$cnt]['feea'] = -str_replace(",", "", $value->fee);
			$get_bankdet[$cnt]['samta'] = -str_replace(",", "", $value->samt);
			$get_bankdet[$cnt]['sfeea'] = -str_replace(",", "", $value->sfee);
			$balanca = $balanca+$get_bankdet[$cnt]['debita'] + $get_bankdet[$cnt]['pamta'] + 
								$get_bankdet[$cnt]['credita'] +$get_bankdet[$cnt]['tamta'] + 
								$get_bankdet[$cnt]['feea'] + $get_bankdet[$cnt]['lamta'] +
								$get_bankdet[$cnt]['lfeea'] + $get_bankdet[$cnt]['samta'] +
								$get_bankdet[$cnt]['sfeea'] ;
			$cnt++;
		}
		$grandbal =number_format($balanca);
		if(strtolower($baln) == "nan") {
			$baln = 0;
		}
		return view('Bankdetails.Viewlist',['account_period' => $account_period,
											'year_month' => $year_month1,
											'db_year_month' => $db_year_month,
											'date_month' => $date_month,
											'dbnext' => $dbnext,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' => $account_val,
											'balanc' => $balanc,
											'grandbal' => $grandbal,
											'baln' => $baln,
											'balx' => $balx,
											'bal' => $bal,
											'startdate' => $startdate,
											'fileCnt' => $fileCnt,
											'previous_date' => $previous_date,
											'balances' => $balances,
											'get_bankdet' => $get_bankdet,
											'index' => $g_query,
											'disablednormal' => $disablednormal,
											'disableddetail' => $disableddetail,
											'get_mnsub' => $get_mnsub,
											'request' => $request]);	
	}
	function add(Request $request) {
		return view('Bankdetails.addedit',['request' => $request]);	
	}
	function edit(Request $request) {
		if (!isset($request->balance)) {
			return Redirect::to('Bankdetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		return view('Bankdetails.addedit',['request' => $request]);	
	}
	function addeditprocess(Request $request) {
		if($request->editflg == "1") {
			$insert = Bankdetail::insertRec($request);
			$count = Bankdetail::countRec($request);
			$fetch = Bankdetail::fetch($count);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $fetch[0]->id); 
			Session::flash('bankids', $request->bankids); 
			Session::flash('branchids', $request->branchids); 
			Session::flash('accno', $request->accno); 
			Session::flash('bankid', $request->bankid); 
			Session::flash('startdate', $fetch[0]->startDate); 
			Session::flash('balbankid', $fetch[0]->bankId); 
			Session::flash('bankname', $request->bankname); 
			Session::flash('branchname', $request->branchname);  
			Session::flash('editflg', $request->editflg);  
			Session::flash('checkflg', 1);  
		} else {
			$update = Bankdetail::updateRec($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->id); 
			Session::flash('date_month', $request->date_month); 
			Session::flash('bankids', $request->bankids); 
			Session::flash('branchids', $request->branchids); 
			Session::flash('accno', $request->accno); 
			Session::flash('bankid', $request->bankid); 
			Session::flash('startdate', $request->txt_startdate); 
			Session::flash('balbankid', $request->balbankid); 
			Session::flash('bankname', $request->bankname); 
			Session::flash('branchname', $request->branchname); 
			Session::flash('editflg', $request->editflg);  
			Session::flash('checkflg', $request->checkflg);  
		}
		return Redirect::to('Bankdetails/Viewlist?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function checked(Request $request) {
		$check = Bankdetail::updateReccheck($request);
		Session::flash('id', $request->id); 
		Session::flash('bankids', $request->bankids); 
		Session::flash('branchids', $request->branchids); 
		Session::flash('accno', $request->accno); 
		Session::flash('bankid', $request->bankid); 
		Session::flash('startdate', $request->startdate); 
		Session::flash('selMonth', $request->selMonth); 
		Session::flash('selYear', $request->selYear); 
		Session::flash('balbankid', $request->balbankid); 
		Session::flash('bankname', $request->bankname); 
		Session::flash('branchname', $request->branchname);
		Session::flash('checkflg', $request->checkflg);  
		return Redirect::to('Bankdetails/Viewlist?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}