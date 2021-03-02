<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bankdetail;
use App\Model\AccBankPassbookDtls;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;

class AccBankPassbookDtlsController extends Controller {

	/**
	*
	* Get  Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/01
	*
	*/

	public function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
			// $request->date =  Session::get('date');
			// $request->amount =  Session::get('amount');
		}

		$from_date = "";
		$to_date = "";
		$previous_date = "";
		$date_month = "";
		$temp = "";
		$bankAcconoforCheck = "";
		$bankNameforCheck ="";

		$g_accountperiod = AccBankPassbookDtls::fnGetAccountPeriodAcc();
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
		$curDate= date('Y-m-d');
		$db_year_month = array();

		$expall_query = AccBankPassbookDtls::fnGetCashExpenseAllRecord();
		$dballrecord = array();
		foreach ($expall_query as $key => $value) {
			array_push($dballrecord, $value->date);
			// $dballrecord[]=$value->date;
		}
		$dballrecord = array_unique($dballrecord);
		$inc=0;
		foreach ($dballrecord AS $dbrecordallkey => $dbrecordallvalue) {
			$split_val = explode("-", $dbrecordallvalue);
			$loc=$split_val[0];
			if ($loc != $temp) {
				$inc=0;
			}
			$db_year_monthall[$split_val[0]][$inc] = intval($split_val[1]);
			$temp=$loc;
			$inc++;
		}
		$y=0;
		$m=0;

		if (!empty($db_year_monthall)) {
			foreach ($db_year_monthall AS $dballkey => $dbllvalue) {
				foreach ($dbllvalue AS $dballsubkey => $dbllsubvalue) {
					$yearMonthCon = $dballkey."-".str_pad($dbllsubvalue, 2, 0, STR_PAD_LEFT);
					$db_year_monthfullarray[$y] = $yearMonthCon;
					if ($y!=0) {
						$yearMnarray[$m] = $yearMonthCon;
						$m++;
					}
					$y++;
				}
			}
		}

		if (!isset($request->selMonth)) {
			$date_month = date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}

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

		$last = date('Y-m', strtotime('last month'));
		$last1 = date($date_month , strtotime($last . " last month"));
		$lastdate = explode("-",$last1);
		$lastyear = $lastdate[0];
		$lastmonth = $lastdate[1];


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

		$year_month_day = $current_year . "-" . $account_close_mn . "-01";
		$maxday = Common::fnGetMaximumDateofMonth($year_month_day);
		$from_date=$last_year . "-" . substr("0" . $account_close_mn, -2). "-" . substr("0" . $maxday, -2);
		$to_date=$current_year . "-" . substr("0" . ($account_close_mn + 1), -2) . "-01";

		$est_query = AccBankPassbookDtls::fnGetCashExpenseRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->date;
		}

		$est_query1 = AccBankPassbookDtls::fnGetCashExpenseRecordPrevious($from_date);

		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->date, 0, 4);
			$dbprevious[]=$value->date;
			$pre++;
		}

		$est_query2 = AccBankPassbookDtls::fnGetCashExpenseRecordNext($to_date);

		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->date;
		}
		$dbrecord = array_unique($dbrecord);
		$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);

		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-",$dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}

		$start = $request->selYear .'-'.$request->selMonth.'-01';
		$end = $request->selYear .'-'.$request->selMonth.'-'.Common::fnGetMaximumDateofMonth($start);
	
		$bankPassbookindex = AccBankPassbookDtls::bankPassbookindex($start,$end,$request);
		$accBankPassbook = array();
		$i = 0;

		foreach ($bankPassbookindex as $key => $value) {
			$accBankPassbook[$i]['id'] = $value->id;
			$accBankPassbook[$i]['bankId'] = $value->bankId;
			$accBankPassbook[$i]['pageNo'] = $value->pageNo;
			$accBankPassbook[$i]['dateRangeFrom'] = $value->dateRangeFrom;
			$accBankPassbook[$i]['dateRangeTo'] = $value->dateRangeTo;
			$accBankPassbook[$i]['fileDtl'] = $value->fileDtl;
			$accBankPassbook[$i]['FirstName'] = $value->FirstName;
			$accBankPassbook[$i]['LastName'] = $value->LastName;
			$accBankPassbook[$i]['Bank_NickName'] = $value->Bank_NickName;
			$accBankPassbook[$i]['AccNo'] = $value->AccNo;
			$accBankPassbook[$i]['bnkid'] = $value->bnkid;
			$accBankPassbook[$i]['bnknm'] = $value->bnknm;
			$accBankPassbook[$i]['brnchid'] = $value->brnchid;
			$accBankPassbook[$i]['brnchnm'] = $value->brnchnm;
			$accBankPassbook[$i]['nxtFlg'] = $value->nxtFlg;
			$accBankPassbook[$i]['delFlg'] = $value->delFlg;
			$i++;
		}
		
		return view('AccBankPassbookDtls.index',[ 'request' => $request,
											'accBankPassbook' => $accBankPassbook,
											'bankPassbookindex' => $bankPassbookindex,
											'account_period' => $account_period,
											'year_month' => $year_month,
											'db_year_month' => $db_year_month,
											'date_month' => $date_month,
											'dbnext' => $dbnext,
											'dbprevious' => $dbprevious,
											'last_year' => $last_year,
											'current_year' => $current_year,
											'account_val' => $account_val,
										]);
	}
	public function addedit(Request $request) {

		if (!isset($request->edit_flg)) {
			return Redirect::to('AccBankPassbookDtls/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$bankDetail = AccBankPassbookDtls::fetchbanknames();
		$accBankPassbook = array();
		if ($request->edit_id != "") {
			$accBankPassbook = AccBankPassbookDtls::accBankPassbook($request->edit_id);
		} if($request->edit_flg == "3" && isset($accBankPassbook[0])) {
			$accBankPassbook[0]->dateRangeFrom = "";
			$accBankPassbook[0]->dateRangeTo = "";
			$accBankPassbook[0]->fileDtl = "";
		}

		return view('AccBankPassbookDtls.addedit',['request' => $request,
												'bankDetail' => $bankDetail,
												'accBankPassbook' => $accBankPassbook,
												]);	
	}
	public function addeditprocess(Request $request) {

		$autoincId = AccBankPassbookDtls::getautoincrement();
		if ($request->edit_flg == "2") {
			$AccBankPassbookNo = "AccBankPassbook_".$request->edit_id;
		} else {
			$AccBankPassbookNo = "AccBankPassbook_".$autoincId;
		}
		
		$fileName = "";
		$fileid = "bankPassbook";

		if($request->$fileid != "") {
			$extension = Input::file($fileid)->getClientOriginalExtension();
			$fileName = $AccBankPassbookNo.'.'.$extension;
			$file = $request->$fileid;
			$destinationPath = '../AccountingUpload/AccBankPassbook';
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777,true);
			}
			$file->move($destinationPath,$fileName);
		} else {
			$fileName = $request->pdffiles;
		}

		if($request->edit_flg != "2") {

			$insert = AccBankPassbookDtls::insertRec($request,$fileName,$autoincId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('danger', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			
		} else {

			$update = AccBankPassbookDtls::updateRec($request,$fileName);

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('danger', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			
		}

		if ($request->edit_flg == "3" && isset($accBankPassbook[0])) {
			$date = explode("-", $accBankPassbook[0]->dateRangeFrom);
		} else {
			$date = explode("-", $request->dateRangeFrom);
		}
		if (isset($date[0])) {
			Session::flash('selYear', $date[0]); 
		} if (isset($date[1])) {
			Session::flash('selMonth', $date[1]); 
		}

		return Redirect::to('AccBankPassbookDtls/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Page No Exists Process for User
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/02/12
	*
	*/
	public function DateExists(Request $request){

		$DateExists = AccBankPassbookDtls::getDateExists($request);

		if (count($DateExists) != 0) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}

	}

}