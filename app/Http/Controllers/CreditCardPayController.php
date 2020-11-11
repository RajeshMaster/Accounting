<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\CreditCardPay;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Fpdf;
use Fpdi;
use Excel;

class CreditCardPayController extends Controller {

	/**
	*
	* Get  Process
	* @author Rajesh
	* @return object to particular view page
	* Created At 2020/10/19
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

		$g_accountperiod = CreditCardPay::fnGetAccountPeriodAcc();
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
		$curDate= date('Y-m-d');
		$db_year_month = array();

		$expall_query = CreditCardPay::fnGetcreditCardAllRecord($request);
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
			$date_month=date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}

		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		if ($request->selMonth == "") {
			$request->selMonth = date('m');
		}
		if ($request->selYear == "") {
			$request->selYear = date('Y');
		}

		$last=date('Y-m', strtotime('last month'));
		$last1=date($date_month , strtotime($last . " last month"));
		$lastdate=explode("-",$last1);
		$lastyear=$lastdate[0];
		$lastmonth=$lastdate[1];


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
		

		$est_query=CreditCardPay::fnGetcreditCardRecord($from_date, $to_date, $request);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->date;
		}


		$est_query1 = CreditCardPay::fnGetcreditCardRecordPrevious($from_date, $request);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->date, 0, 4);
			$dbprevious[]=$value->date;
			$pre++;
		}


		$est_query2=CreditCardPay::fnGetCreditCardRecordNext($to_date, $request);

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

		$creditcardDetails = CreditCardPay::fetchcreditcarddetails($start, $end, $request);
		
		return view('CreditCardPay.index',[ 'request' => $request,
											'creditcardDetails' => $creditcardDetails,
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
		$yearArr = array();
		$creditcard = CreditCardPay::fetchcreditCardnames();
		$year = date('Y');
		$yearArr[date('Y')-1] = date('Y')-1;
		$yearArr[date('Y')] = date('Y');
		$yearArr[date('Y')+1] = date('Y')+1;

		/*echo "<pre>";
		print_r($bankdetailindex);
		echo "</pre>";*/

		return view('CreditCardPay.addedit',[ 'request' => $request,
												'creditcard' => $creditcard,
												'yearArr' => $yearArr,
												'year' => $year
										]);
	}

	public function addeditprocess(Request $request) {
		if($request->fileToUpload == "") {
			return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$sheetData = array();
		$categoryName = CreditCardPay::fetchcategorynames();

		$request->selectedMonth = str_pad($request->selectedMonth, 2, '0', STR_PAD_LEFT);
		$sjis = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
		$utf8 = mb_convert_encoding($sjis, 'UTF-8', 'SJIS-win');
		file_put_contents('utf8.csv', $utf8);

		if (($handle = fopen('utf8.csv', "r")) !== FALSE) {
			while (($dat = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$sheetData[] = $dat;
			}
			fclose($handle);
		}

		$errorFlg = 0;
		$errorStatus = "";
		for ($i=0; $i < count($sheetData); $i++) {
			if ($i != 0 && $i != count($sheetData)-1) {
				if (!isset($sheetData[$i][0]) || $sheetData[$i][0] == "") {
					$errorFlg = 1;
					$errorStatus = "Date";
				} 

				if (!isset($sheetData[$i][1]) ||$sheetData[$i][1] == "") {
					$errorFlg = 1;
					$errorStatus = "Content";
				}

				if (!isset($sheetData[$i][5]) || $sheetData[$i][5] == "") {
					$errorFlg = 1;
					$errorStatus = "Amount";
				} elseif (!is_numeric($sheetData[$i][5])) {
					$errorFlg = 1;
					$errorStatus = "Amount";
				}

				if (!isset($sheetData[$i][6])) {
					$errorFlg = 1;
					$errorStatus = "Remarks";
				}
				if ($errorFlg) {
					break;
				}
			}
		}

		if ($errorFlg) {
			Session::flash('danger', 'Line No '.($i+1).' '.$errorStatus.' has Error!'); 
			Session::flash('type', 'alert-danger');
			return Redirect::to('CreditCardPay/addedit?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		return view('CreditCardPay.creditCardDetail',[
											'request' => $request,
											'categoryName' => $categoryName,
											'sheetData' => $sheetData
										]);
	}

	public function creditCardAddDtls(Request $request) {
		$insertProcess = CreditCardPay::inscreditCardDtls($request);
		if($insertProcess) {
			Session::flash('success', 'Inserted Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	public function detailsaddedit(Request $request) {
		if(!$request->id){ 
			return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$creditcardDetails = CreditCardPay::fetchcreditcardEdit($request);
		$creditcard = CreditCardPay::fetchcreditCardnames();
		$categoryName = CreditCardPay::fetchcategorynames();
		return view('CreditCardPay.detailsaddedit',[
											'request' => $request,
											'creditcardDetails' => $creditcardDetails,
											'categoryName' => $categoryName,
											'creditcard' => $creditcard,
										]);
	}

	public function detailseditprocess(Request $request) {
		$Transferno = "creditCard".$request->id;
		$fileName = "";
		$fileid = "transferBill";

		if($request->$fileid != "") {

			$extension = Input::file($fileid)->getClientOriginalExtension();
			$fileName = $Transferno.'.'.$extension;
			$file = $request->$fileid;
			$destinationPath = '../AccountingUpload/CreditCard';
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777,true);
			}
			$file->move($destinationPath,$fileName);
		} else {
			$fileName = $request->imgfiles;
		}

		$updateProcess = CreditCardPay::updateDtls($request,$fileName);

		if($updateProcess) {
			Session::flash('success', 'Updated Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Updated Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	public function GetInsMonth_ajax(Request $request) {

		$selectData = CreditCardPay::getDataBycard($request);
		$selectedArr = array();
		$yrMnth = array();
		$recYMonth ="";
		$i = 0;
		foreach ($selectData as $key => $value) {
			if($value->selectedYearMonth != "" && $recYMonth != $value->selectedYearMonth) {
				$yrMnth = explode('-', $value->selectedYearMonth);
				$selectedArr[$i] = ltrim($yrMnth[1], '0');
				$recYMonth = $value->selectedYearMonth;
				$i++;
			}
		}
		$selectedArr = json_encode($selectedArr);
		echo $selectedArr;
		exit();
	}

	public function deleteRecords(Request $request) {
		$deletData = CreditCardPay::deletdRecorsForYM($request);
		if($deletData) {
			Session::flash('success', 'Cleared Sucessfully!'); 
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Cleared Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
		return Redirect::to('CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	public function yearindex(Request $request) {

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


		$start = $request->selYear .'-01-01';
		$end = $request->selYear .'-12-31';
		$creditcardDetails = CreditCardPay::fetchAmountForYearlyWise($start, $end, $request);

		$getPreviousCount = CreditCardPay::fetchpreviousNextRecord($request->selYear-1);
		$getNextCount = CreditCardPay::fetchpreviousNextRecord($request->selYear+1);

		return view('CreditCardPay.yearindex',[ 'request' => $request,
											'creditcardDetails' => $creditcardDetails,
											'getPreviousCount' => $getPreviousCount,
											'getNextCount' => $getNextCount,
										]);
	}

	public function categorySelect(Request $request) {
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


		$start = $request->selYear .'-01-01';
		$end = $request->selYear .'-12-31';
		$creditcardDetails = CreditCardPay::fetchcreditcarddetails($start, $end, $request);
		$getPreviousCount = CreditCardPay::fetchpreviousNextRecordCategory($request->selYear-1,$request);
		$getNextCount = CreditCardPay::fetchpreviousNextRecordCategory($request->selYear+1,$request);

		return view('CreditCardPay.categorySelect',[ 'request' => $request,
											'creditcardDetails' => $creditcardDetails,
											'getPreviousCount' => $getPreviousCount,
											'getNextCount' => $getNextCount,
										]);
	}
}