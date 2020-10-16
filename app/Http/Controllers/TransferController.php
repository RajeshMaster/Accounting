<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Common;
use App\Model\Transfer;
use App\Model\Expenses;
use DB;
use Input;
use Redirect;
use Config;
use Session;
use Illuminate\Support\Facades\Validator;
use Excel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;

class TransferController extends Controller {
	function index(Request $request) {
		$PAGING=0;
		$incr=0;
		$serialcolor="";
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
			$request->prevcnt =  Session::get('prevcnt');
			$request->nextcnt =  Session::get('nextcnt');
			$request->account_val =  Session::get('account_val');
			$request->previou_next_year =  Session::get('previou_next_year');
		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		if ($request->page=="") {
			$request->page = 1;
		}
		$last1 = "";
		$last = "";
		if (empty($request->selMonth)) {
			$date_month = date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}

		$last1 = date($date_month , strtotime($last . " last month"));
		$lastdate = explode('-',$last1);
		$lastyear =$lastdate[0];
		$lastmonth =$lastdate[1];
		$g_accountperiod = Transfer::fnGetAccountPeriodBK($request);
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

		$exp_query = Transfer::fnGetBKRecord($from_date, $to_date);

		$dbrecord = array();
		foreach ($exp_query as $key => $value) {
			array_push($dbrecord, $value->bankdate);
		}
		

		$bktr_query1 = Transfer::fnGetbkrsRecordPrevious($from_date);
		
		$dbprevious = array();
		foreach ($bktr_query1 as $key => $value) {
			array_push($dbprevious, $value->bankdate);
		}
		$bktr_query2 = Transfer::fnGetbkrsRecordNext($to_date);

		$dbnext = array();
		foreach ($bktr_query2 as $key => $value2) {
			array_push($dbnext, $value2->bankdate);
		}

		$dbrecord = array_unique($dbrecord);
		$db_year_month = array();

		foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
			$split_val = explode("-", $dbrecordvalue);
			$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
		}

		$split_date = explode('-', $date_month);

		$account_val = Common::getAccountPeriod($year_month1, $account_close_yr, $account_close_mn, $account_period);

		$slbk_query = Transfer::download_transfer($request,$lastyear,$lastmonth);
		$disp = 0;
		$disp = count($slbk_query);
		$i = 0;
		$totval = 0;
		$k = 0;
		$rsTotalAmount = "";
		$fee_rsTotalAmount = "";
		$getsuj = "";
		$getsub_suj = "";
		$temp = "";
		$temp1 = "";
		$temp2 = "";
		$bank_det_bname = "";
		$bank_det_nickname = "";
		$year = "";
		$month = "";
		$totalexptra = "";
		$getbktr_det = array();
		foreach ($slbk_query as $key => $value) {
			if($value->amount != "") {
				if($value->amount <0){
					$rsTotalAmount += $value->amount;
				} else if($value->amount==0) {
					$rsTotalAmount +=$value->amount;
				} else if($value->amount>0) {
					$rsTotalAmount += $value->amount;
				}
			} else {
				$getbktr_det[$k]['Amountdisplay']=  "&nbsp;";
			}
			if($value->fee != "") {
				if($value->fee<0){
					$fee_rsTotalAmount += $value->fee;
				} else if($value->fee==0) {
					$fee_rsTotalAmount += $value->fee;
				} else if($value->fee>0) {
					$fee_rsTotalAmount += $value->fee;
				}
			} else {
				$getbktr_det[$k]['charge']= "&nbsp;";
			}
			$k++;
		}
		$k = 0;
		// print_r($slbk_query);exit();
		foreach ($slbk_query as $key => $value) {
			$getbktr_det[$k]['id'] = $value->id;
			$getbktr_det[$k]['subject'] = $value->subject;
			$getbktr_det[$k]['details'] = $value->details;
			$getbktr_det[$k]['bankdate'] = $value->bankdate;
			$getbktr_det[$k]['fee'] = $value->fee;
			$getbktr_det[$k]['amount'] = $value->amount;
			$getbktr_det[$k]['bankname_id'] = $value->bankname;
			$getbktr_det[$k]['BankName'] = $value->bname;
			$getbktr_det[$k]['bankaccno'] = $value->bankaccno;
			$getbktr_det[$k]['FirstNames'] = $value->FirstNames;
			$getbktr_det[$k]['LastNames'] = $value->LastNames;
			$getbktr_det[$k]['remark_dtl'] = $value->remark_dtl;
			$getbktr_det[$k]['file_dtl'] = $value->file_dtl;
			$getbktr_det[$k]['Ins_DT'] = $value->Ins_DT;
			$getbktr_det[$k]['Up_DT'] = $value->Up_DT;
			$getbktr_det[$k]['copy_month_flg'] = $value->copy_month_day;
			$getbktr_det[$k]['others'] = $value->others;
			$getbktr_det[$k]['loan_flg'] = $value->loan_flg;
			$getbktr_det[$k]['salaryFlg'] = $value->salaryFlg;
			$getbktr_det[$k]['mainbankid'] = $value->mainbankid;
			$getbktr_det[$k]['edit_flg'] = $value->edit_flg;
			$getbktr_det[$k]['submit_flg'] = $value->submit_flg;
			$getbktr_det[$k]['empNo'] = $value->empNo;
			$getbktr_det[$k]['transaction_flg'] = $value->transaction_flg;
			$getbktr_det[$k]['transfer_flg'] = $value->transfer_flg;
			$getbktr_det[$k]['Bank_NickName'] = $value->Bank_NickName;
			$getbktr_det[$k]['del_flg'] = $value->del_flg;
			$getbktr_det[$k]['EmpName'] = ucwords(strtolower($value->LastName)). ".".
					ucwords(mb_substr($value->FirstName, 0, 1,'utf-8'));
			$getsuj = Transfer::selbksubname($value->subject);
			if(isset($getsuj[0])) {
				$getbktr_det[$k]['subjectbank']= $getsuj[0];
			}
			$getsub_suj = Transfer::selsubtransfersubjectname($value->details,$value->subject);
			if(isset($getsub_suj[0])) {
				$getbktr_det[$k]['detail']= $getsub_suj[0];
			}

			if($getbktr_det[$k]['bankdate'] != "") {
				$getbktr_det[$k]['loc']  = $getbktr_det[$k]['bankdate'];
			}
			if(isset($getbktr_det[$k]['loc']) && $getbktr_det[$k]['loc'] != $temp){
					if($value->bankdate != ""){
						 $getbktr_det[$k]['bankdatedetais']=$value->bankdate;
					} 
			}
			$getbktr_det[$k]['bankdatedetais'];
			if($getbktr_det[$k]['BankName'] != "") {
				$getbktr_det[$k]['loc1']  = $getbktr_det[$k]['BankName'];
			}
			if(isset($getbktr_det[$k]['loc1']) && $getbktr_det[$k]['loc1'] != $temp1){
					if($value->bname != ""){
						 $getbktr_det[$k]['bankdatedet']=$value->bname;
					} 
			}
			if($getbktr_det[$k]['bankaccno'] != "") {
				$getbktr_det[$k]['loc2']  = $getbktr_det[$k]['bankaccno'];
			}
			if(isset($getbktr_det[$k]['loc2']) && $getbktr_det[$k]['loc2'] != $temp2){
					if($value->bankaccno != ""){
						 $getbktr_det[$k]['bankdatede']=$value->bankaccno;
					} 
			}
			if($value->amount!= "") {
				if($value->amount<0){
					$fontColor = "<font color='red'>";
					$getbktr_det[$k]['Amountdisplay']= number_format($value->amount);
				} else if($value->amount==0) {
					$fontColor = "<font color='black'>";
					$getbktr_det[$k]['Amountdisplay']= number_format($value->amount);
				} else if($value->amount>0) {
					$fontColor = "<font color='black'>";
					$getbktr_det[$k]['Amountdisplay']= number_format($value->amount);
				}
			} else {
				$getbktr_det[$k]['Amountdisplay']=  "&nbsp;";
			}
			if($value->fee != "") {
				if($value->fee<0){
				$fontColor = "<font color='red'>";
				$getbktr_det[$k]['charge']= number_format($value->fee);
				} else if($value->fee==0) {
					$fontColor = "<font color='black'>";
					$getbktr_det[$k]['charge']= number_format($value->fee);
				} else if($value->fee>0) {
					$fontColor = "<font color='black'>";
					$getbktr_det[$k]['charge']= number_format($value->fee);
				}
			} else {
				$getbktr_det[$k]['charge']= "&nbsp;";
			}
			if($value->remark_dtl!= "") {
				$getbktr_det[$k]['remark']= $value->remark_dtl;
			} 
			$getbktr_det[$k]['bankname'] = $value->bankname;
			if ($getbktr_det[$k]['loan_flg'] == 1) {
				$sqlbankquery = Transfer::regGetBankId($value->bankname);
				// $i = 0;
				foreach ($sqlbankquery as $key => $value1) {
					$getbktr_det[$k]['bankaccno'] = $value1->AccNo;
					$value->bankaccno = $value1->AccNo;
					$getbktr_det[$k]['bankname'] = $value1->BankName;
					$getbktr_det[$k]['BankName'] =  $value1->bnName;
					// $i++;
				}
			}
			$sql1 = Transfer::regGetBankDetails($getbktr_det[$k]['bankname'],$getbktr_det[$k]['bankaccno']);
				foreach ($sql1 as $key => $value2) {
					$bank_det_bname = $value2->BankName;
					$bank_det_nickname = $value2->Bank_NickName;
				}
			$getbktr_det[$k]['bankname']=$bank_det_bname;
			$getbktr_det[$k]['banknickname']=$bank_det_nickname;
			$getbktr_det[$k]['bankaccno']=$value->bankaccno;
			if(isset($value->transaction_flg)) {
				$getbktr_det[$k]['transaction_flg']=$value->transaction_flg;
			}
			$orderdate = explode('-', $date_month);
			$year = $orderdate[0];
			$month = $orderdate[1];
			$soluexpanse = Transfer::soluexpanse($year,$month);
			$solutransfer = Transfer::solutransfer($year,$month);
			$totalexptra =  $soluexpanse[0]->amount + $solutransfer[0]->result;

			$current_year_month = array();
			$current_year_month[0] = date('Y-m');
			$current_year_month[1] = date ('Y-m', strtotime ( '+1 month' , strtotime ( $current_year_month[0]."-01" )));
			$current_year_month[2]= date ('Y-m', strtotime ( '-1 month' , strtotime ( $current_year_month[0]."-01" )));
			for($j=0; $j<count($current_year_month);$j++) {
				if($current_year_month[$j] == $date_month){
					$getbktr_det[$k]['copy'] ="1";
				}
			}
			$k++;
		}
		$balan = $rsTotalAmount- $fee_rsTotalAmount;
		$expamt[0] = $totalexptra;
		$kessanki = Transfer::getkessanki($request);
		$interval=$kessanki[0];
		$cur_year=date('Y');
		$cur_month=date('m');
		if (isset($request->selMonth)) {
			$selectedMonth=$request->selMonth;
			$selectedYear=$request->selYear;					
			$cur_month=$selectedMonth;
			$cur_year=$selectedYear;
		} else {
			$selectedMonth=$cur_month;
			$selectedYear=$cur_year;
		}
		// FOR RIGHT SIDE PROCESS CATEGORY CLICK
		$sqlMainCat = Transfer::getMainCategories($request);
		if (Session::get('languageval') == "en") {
			$selectedField = "Subject";
			$selectedFieldSub = "sub_eng";
		} else {
			$selectedField = "Subject_jp";
			$selectedFieldSub = "sub_jap";
		}
		$mn=0;
		$mainCatDetails = array();
		foreach ($sqlMainCat as $key => $value3) {
			$mainCatDetails[$mn]['mainCat'] = $value3->$selectedField;
			$mainCatDetails[$mn]['id'] = $value3->id;

			$sqlSubCat = Transfer::getSubCategories($value3->id);
			$sb=0;
			foreach ($sqlSubCat as $key => $value4) {
				//echo $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $value4->$selectedFieldSub;
				$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $value4->$selectedFieldSub;
				$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subId'] = $value4->id;
				$sb++;
			}
			$mn++;
		}

		/*Transfer Excel Download*/
		$actionName = $request->actionName;
		if ($actionName == "transfersexceldownload") {

			$selectedYearMonth = explode("-", $request->selYearMonth);
			$curTime = date('Y/m/d  H:i:s');
			$template_name = 'resources/assets/uploadandtemplates/templates/transfer_bank_download.xls';
			$tempname = "Transfer_details";
		  	$excel_name=$tempname;
		  	Excel::load($template_name, function($objTpl) use($request, $curTime, $selectedYearMonth, $slbk_query) {
			$selectedYearMonth = explode("-", $request->selYearMonth);
			$year = $selectedYearMonth[0];
			$month = $selectedYearMonth[1];
		  	$slbk_query = Transfer::download_transferforexcel($request,$year,$month);
			$disp = count($slbk_query);
			if($disp>0){
				$objTpl->getActiveSheet()->getStyle('A3:L3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('76933C');
			  	$objTpl->getActiveSheet()->getStyle('A4:L4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4D79B');
			  	$objTpl->getActiveSheet()->setCellValue('L1', $selectedYearMonth[0]."年".$selectedYearMonth[1]."月分");
			  	
			  	$objTpl->getActiveSheet()
			  	->setCellValue('B4', (trans('messages.lbl_Date')))
			  	->setCellValue('D4', (trans('messages.lbl_item_name')))
			  	->setCellValue('E4', (trans('messages.lbl_employee_no')))
			  	->setCellValue('F4', (trans('messages.lbl_ryo_claim')))
			  	->setCellValue('G4', (trans('messages.lbl_loan')))
			  	->setCellValue('H4', (trans('messages.lbl_sal_exp')))
			  	->setCellValue('I4', (trans('messages.lbl_cash_withdrawal')))
			  	->setCellValue('J4', (trans('messages.lbl_withdrawal_deposit')))
			  	->setCellValue('K4', (trans('messages.lbl_sales_deposit')))
			  	->setCellValue('L4', (trans('messages.lbl_balance_confirmed')));

			  	/*$objTpl->getActiveSheet()->getStyle('B8:F8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
			  	$objTpl->getActiveSheet()->getStyle('K8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6B8B7');
			  	$objTpl->getActiveSheet()->getStyle('A10:L10')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('948A54');*/

			  	
			  	$k = 0;
			  	//$x = 6;
			  	$loc1 = "";
				$temp1 = "";
			  	$getbktr_det = array();
			  	$sumtotalval = 0;
			  	$sumWithdrawTotalval = 0;
			  	$sumDepositTotalval = 0;
			  	$sumTotalval = 0;
			  	$getsuj = "";
			  	$destinationPath = '../InvoiceUpload/Expenses/';
			  	// print_r($slbk_query);exit();
				foreach ($slbk_query as $key => $value) {
					$getbktr_det[$k]['empNo'] = $value->empNo;
					$getbktr_det[$k]['bankdate'] = $value->bankdate;
					$getbktr_det[$k]['amount'] = $value->amount;
					$getbktr_det[$k]['fee'] = $value->fee;
					$getbktr_det[$k]['remark_dtl'] = $value->remark_dtl;
					$getbktr_det[$k]['subject'] =$value->subject;
					$getbktr_det[$k]['details']= $value->details;
					$getbktr_det[$k]['del_flg']= $value->del_flg;
					$getbktr_det[$k]['others']= $value->others;
					$getbktr_det[$k]['transaction_flg']= $value->transaction_flg;
					$getbktr_det[$k]['transfer_flg']= $value->transfer_flg;
					$getbktr_det[$k]['file_dtl']= $value->file_dtl;
					$getsub_suj = Transfer::selsubtransfersubjectname($value->details,$value->subject);
					if(isset($getsub_suj[0])) {
						$getbktr_det[$k]['details'] = $getsub_suj[0];
						$getbktr_det[$k]['detail']=$getsub_suj[0];
					}
					$getbktr_det[$k]['banknameId']= $value->bankname;
					$getbktr_det[$k]['BankName'] = $value->bname;
					$getbktr_det[$k]['bankaccno'] = $value->bankaccno;
					$getbktr_det[$k]['loan_flg'] = $value->loan_flg;
					$getbktr_det[$k]['salaryFlg'] = $value->salaryFlg;
					$get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName)). ".".
					ucwords(mb_substr($value->FirstName, 0, 1,'utf-8'));

					if ($getbktr_det[$k]['loan_flg'] == 1) {
						$getbktr_det[$k]['details'] = trans('messages.lbl_loanpay');
					    $sqlbankquery = Transfer::regGetBankId($value->bankname);
						foreach ($sqlbankquery as $key => $value2) {
							$getbktr_det[$k]['bankaccno'] = $value2->AccNo;
							$getbktr_det[$k]['Bank_NickName'] = $value2->Bank_NickName;
							$getbktr_det[$k]['BankName'] =  $value2->bnName;
						}
					}
					if ($value->del_flg == 2) {
						$sql1 = Expenses::regGetBankDetails($value->bankname,$value->bankaccno);
						foreach ($sql1 as $key => $value1) {
							$getbktr_det[$k]['BankName'] = $value1->Bank_NickName;
							$getbktr_det[$k]['Bank_NickName'] = $value1->Bank_NickName;
						}
					}

					$k++;
					//$x++;
				}//exit();

				$temp_i = 6;
				$loan = 0;
				$withdraw = 0;
				$expen = 0;
				$credit = 0;
				$sales = 0;
				$deposit = 0;
				$result = 0;
				$loc1 = "";
				$loc2 = "";
				$loc = "";
				for ($i=0;$i<count($getbktr_det);$i++) {
					$Amtdisplay = str_replace("-","",$getbktr_det[$i]['amount']);
					$loc1 = $getbktr_det[$i]['BankName']."-".$getbktr_det[$i]['bankaccno'];
					$empNo = substr($getbktr_det[$i]['empNo'],(strlen($getbktr_det[$i]['empNo'])-3));
					$empNo = isset($empNo)?$empNo:'';
					
					if ($empNo == "") {
						$empNo = " ";
					} else {
						if ($empNo > 100) {
							$empNo;
						} else {
							$empNo = substr($empNo, 1);
							$empNo;
						}
					}
					$MonthDate = explode("-", $getbktr_det[$i]['bankdate']);
					if ($getbktr_det[$i]['others'] == 1) {
						$Mainsubject = trans('messages.lbl_Others');
					} else if ($getbktr_det[$i]['del_flg'] == 2) {
						$Mainsubject = $getbktr_det[$i]['subject'];
					} else if($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['loan_flg'] != 1 && $value->del_flg != 2){//ss
						$Mainsubject = trans('messages.lbl_transfer');
					} //ss
					else if($getbktr_det[$i]['loan_flg'] != 1 && $value->del_flg != 2){
						$getsuj = Transfer::selsubname($getbktr_det[$i]['subject']);
						$Mainsubject = $getsuj[0];
					}
					elseif($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['loan_flg'] == 1){//ss
						$Mainsubject = trans('messages.lbl_loan');
					}//ss
					else if ($getbktr_det[$i]['loan_flg'] == 1) {
						$Mainsubject = $getbktr_det[$i]['BankName'];
					}
					else if($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['del_flg'] != 2){//ss
						$Mainsubject = trans('messages.lbl_transfer');
					}//ss
					else{//ss
						$getsuj = Transfer::selsubname($getbktr_det[$i]['subject']);
						$Mainsubject = $getsuj[0];
					}//ss
					if ($getbktr_det[$i]['others'] == 1) {
						$Subsubject = trans('messages.lbl_Others');
					} else if($getbktr_det[$i]['loan_flg'] != 1 && $getbktr_det[$i]['fee'] == ""  && $getbktr_det[$i]['del_flg'] != 2){//ss
						$Subsubject = trans('messages.lbl_charge');
					}//ss
					else if($getbktr_det[$i]['loan_flg'] != 1){
						$Subsubject = $getbktr_det[$i]['details'];
					} 
					else if ($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['loan_flg'] == 1){//ss
						$Subsubject = trans('messages.lbl_charge');
					}//ss
					else{
						$Subsubject = "Loan Payment";
					}
					if ($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['salaryFlg'] == 1) {//ss
						$Mainsubject = "Salary";
					}//ss
					else if($getbktr_det[$i]['salaryFlg'] == 1){
						$Mainsubject = $get_det[$i]['EmpName'];
					} 
					if ($getbktr_det[$i]['fee'] == "" && $getbktr_det[$i]['salaryFlg'] == 1) {//ss
						$Subsubject = trans('messages.lbl_charge');
					}//ss
					else if($getbktr_det[$i]['salaryFlg'] == 1){
						$Subsubject = "Salary";
					} 
					if ($getbktr_det[$i]['del_flg'] == 2) {
						if($getbktr_det[$i]['transaction_flg'] == 1){
							$Subsubject = trans('messages.lbl_debit');
						} else if($getbktr_det[$i]['transaction_flg'] == 3){
							if($getbktr_det[$i]['transfer_flg']==1){
								$Subsubject = trans('messages.lbl_transfer')." ・".trans('messages.lbl_debit');
							} else {
								$Subsubject = trans('messages.lbl_transfer')." ・".trans('messages.lbl_credit');
							}
						} else {
							$Subsubject = trans('messages.lbl_credit');
						}
					}

					if($loc1 != $temp1 ){
						if($i != 0){
							if($withdraw == 0){
								$withdrawamt="";
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $withdrawamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
								$withdraw = 0;
							} else {
							  	$withdrawamt=$withdraw;
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $withdrawamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
								$withdraw = 0;
							}

							if($deposit == 0){
								$depositamt="";
								$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);
								$deposit = 0;
							} else {
							  	$depositamt=$deposit;
								$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);
								$deposit = 0;
							}

							if($credit == 0){
								$creditamt="";
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $creditamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$credit = 0;
							} else {
							  	$creditamt=$credit;
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $creditamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$credit = 0;
							}

							if($loan == 0){
								$loanamt="";
								$objTpl->getActiveSheet()->setCellValue('G'.$temp_i, $loanamt)->getStyle('A'.$temp_i.':'.'G'.$temp_i)->getFont()->setBold(true);
								$loan = 0;
							} else {
							  	$loanamt=$loan;
								$objTpl->getActiveSheet()->setCellValue('G'.$temp_i, $loanamt)->getStyle('A'.$temp_i.':'.'G'.$temp_i)->getFont()->setBold(true);
								$loan = 0;
							}

							if($expen == 0){
								$expenamt="";
								$objTpl->getActiveSheet()->setCellValue('H'.$temp_i, $expenamt)->getStyle('A'.$temp_i.':'.'H'.$temp_i)->getFont()->setBold(true);
								$expen = 0;
							} else {
							  	$expenamt=$expen;
								$objTpl->getActiveSheet()->setCellValue('H'.$temp_i, $expenamt)->getStyle('A'.$temp_i.':'.'H'.$temp_i)->getFont()->setBold(true);
								$expen = 0;
							}
							if($sales == 0){
								$salesamt="";
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$sales = 0;
							} else {
							  	$salesamt=$sales;
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$sales = 0;
							}

						$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objTpl->getActiveSheet()
						 ->setCellValue('A'.$temp_i);
						 $temp_i++;
						
						$result=$loanamt+$expenamt+$withdrawamt;
							if($result== 0){
								$resultamt="";
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
							} else{
								$resultamt=$result;
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
							}
						$objTpl->getActiveSheet()->setCellValue('D'.($temp_i), "Grand Total")->mergeCells('D'.($temp_i).':'.'E'.($temp_i))->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
						$objTpl->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f1a2a2');	

						// echo ($depositamt);
						$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);

						$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);

						$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objTpl->getActiveSheet()
						 ->setCellValue('A'.$temp_i);
						 $temp_i++;
						}
						// if ($getbktr_det[$i]['del_flg'] == 2) {
						// 	$bank= $getbktr_det[$i]['BankName']."-".$getbktr_det[$i]['bankaccno']."";
						// } else {
						// $name= ((is_numeric($getbktr_det[$i]['bankname']))?$getbktr_det[$i]['BankName']:$getbktr_det[$i]['bankname'])."-".$getbktr_det[$i]['bankaccno'];
						// }

						$bank_data = $loc1;
						$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objTpl->getActiveSheet()
						 ->setCellValue('A'.$temp_i, $bank_data);
						$objTpl->getActiveSheet()->mergeCells('A'.$temp_i.':'.'L'.$temp_i)->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getFont()->setBold(true);
			  			$objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('B7DEE8');
			  			$objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
			  			$objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$temp_i++;
					}
					$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(19);
					$objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					$objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objTpl->getActiveSheet()->setCellValue('B'.$temp_i, $MonthDate[2]."日");
					$objTpl->getActiveSheet()->setCellValue('D'.$temp_i, $Mainsubject."-".$Subsubject);
					$objTpl->getActiveSheet()->setCellValue('E'.$temp_i, $empNo);
					$destinationPath1 = $destinationPath.$getbktr_det[$i]['file_dtl'];
					if ($getbktr_det[$i]['file_dtl'] != "") {
						if (isset($getbktr_det[$i]['file_dtl']) && file_exists($destinationPath1)) {
							$objTpl->getActiveSheet()->setCellValue('F'.$temp_i,'●')->getStyle('F'.$temp_i)->getFont()->setName("ＭＳ ゴシック");
							$objTpl->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
					}
					// $Amtdisplay = str_replace("-","",$getbktr_det[$i]['amount']);
					if ($getbktr_det[$i]['del_flg'] == 2) {
						if ($getbktr_det[$i]['transaction_flg']==1 || $getbktr_det[$i]['transfer_flg']==1) {
							$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, number_format(round($Amtdisplay)))->getStyle('I'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$withdraw += preg_replace('/,/', '', $Amtdisplay);
							// $sumWithdrawTotalval += $totalval;
						} else if ($getbktr_det[$i]['transaction_flg']==2) {
						 	$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, number_format(round($Amtdisplay)))->getStyle('K'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$sales += preg_replace('/,/', '', $Amtdisplay);
						}
						else if ($getbktr_det[$i]['transfer_flg']==2){
							$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, number_format(round($Amtdisplay)))->getStyle('J'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$deposit += preg_replace('/,/', '', $Amtdisplay);
							// $sumDepositTotalval += $totalval;
						}else {
							$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, number_format(round($Amtdisplay)))->getStyle('K'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$credit += preg_replace('/,/', '', $Amtdisplay);
							// $sumDepositTotalval += $totalval;
						}
					} else if ($getbktr_det[$i]['loan_flg'] ==1 || $getbktr_det[$i]['others'] ==1) {
						$objTpl->getActiveSheet()->setCellValue('G'.$temp_i, number_format(round($Amtdisplay)))->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$loan += preg_replace('/,/', '', $Amtdisplay);
							// $sumTotalval += $totalval;
					} else if($getbktr_det[$i]['salaryFlg']==1 || $getbktr_det[$i]['transaction_flg'] =="" && $getbktr_det[$i]['loan_flg'] !=1 && $getbktr_det[$i]['others'] !=1){
						$objTpl->getActiveSheet()->setCellValue('H'.$temp_i, number_format(round($Amtdisplay)));
							$expen += preg_replace('/,/', '', $Amtdisplay);
							// $sumTotalval += $totalval;
					}
					
					$objTpl->getActiveSheet()->getStyle('H'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objTpl->getActiveSheet()->setCellValue('L'.$temp_i, $getbktr_det[$i]['remark_dtl']);
					$objTpl->getActiveSheet()->getStyle('L'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					//if ($temp_inc == 1) {
						$temp_i++;
					//}
						if(count($getbktr_det) == ($i+1)){
							if($withdraw == 0){
								$withdrawamt="";
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $withdrawamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
								$withdraw = 0;
							} else {
							  	$withdrawamt=$withdraw;
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $withdrawamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
								$withdraw = 0;
							}

							if($deposit == 0){
								$depositamt="";
								$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);
								$deposit = 0;
							} else {
							  	$depositamt=$deposit;
								$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);
								$deposit = 0;
							}

							if($credit == 0){
								$creditamt="";
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $creditamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$credit = 0;
							} else {
							  	$creditamt=$credit;
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $creditamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$credit = 0;
							}

							if($loan == 0){
								$loanamt="";
								$objTpl->getActiveSheet()->setCellValue('G'.$temp_i, $loanamt)->getStyle('A'.$temp_i.':'.'G'.$temp_i)->getFont()->setBold(true);
								$loan = 0;
							} else {
							  	$loanamt=$loan;
								$objTpl->getActiveSheet()->setCellValue('G'.$temp_i, $loanamt)->getStyle('A'.$temp_i.':'.'G'.$temp_i)->getFont()->setBold(true);
								$loan = 0;
							}

							if($expen == 0){
								$expenamt="";
								$objTpl->getActiveSheet()->setCellValue('H'.$temp_i, $expenamt)->getStyle('A'.$temp_i.':'.'H'.$temp_i)->getFont()->setBold(true);
								$expen = 0;
							} else {
							  	$expenamt=$expen;
								$objTpl->getActiveSheet()->setCellValue('H'.$temp_i, $expenamt)->getStyle('A'.$temp_i.':'.'H'.$temp_i)->getFont()->setBold(true);
								$expen = 0;
							}

							if($sales == 0){
								$salesamt="";
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$sales = 0;
							} else {
							  	$salesamt=$sales;
								$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
								$sales = 0;
							}

						$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objTpl->getActiveSheet()
						 ->setCellValue('A'.$temp_i);
						 $temp_i++;
		  				
		  				$result=$loanamt+$expenamt+$withdrawamt;
		  					if($result== 0){
								$resultamt="";
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
							} else{
								$resultamt=$result;
								$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);
							}
						$objTpl->getActiveSheet()->setCellValue('D'.($temp_i), "Grand Total")->mergeCells('D'.($temp_i).':'.'E'.($temp_i))->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'I'.$temp_i)->getFont()->setBold(true);
						$objTpl->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objTpl->getActiveSheet()->setCellValue('I'.$temp_i, $resultamt)->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f1a2a2');
						// $objTpl->getActiveSheet()->getRowDimension(($temp_i+1))->setRowHeight(20);
						// $objTpl->getActiveSheet()
						// 	   ->setCellValue('D'.($temp_i+1), "Grand Total");
						// $objTpl->getActiveSheet()->mergeCells('G'.($temp_i+1).':'.'I'.($temp_i+1));

						// echo ($depositamt);
						$objTpl->getActiveSheet()->setCellValue('J'.$temp_i, $depositamt)->getStyle('A'.$temp_i.':'.'J'.$temp_i)->getFont()->setBold(true);


						$objTpl->getActiveSheet()->setCellValue('K'.$temp_i, $salesamt)->getStyle('A'.$temp_i.':'.'K'.$temp_i)->getFont()->setBold(true);


						$objTpl->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objTpl->getActiveSheet()
						 ->setCellValue('A'.$temp_i);
						 $temp_i++;
		  				}

					$temp1 = $loc1;
				}
				// $sumtotalval = $sumWithdrawTotalval + $sumTotalval;
				// $objTpl->getActiveSheet()->getStyle('A'.$temp_i.':'.'L'.$temp_i)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
				// $objTpl->getActiveSheet()->getRowDimension(($temp_i+1))->setRowHeight(20);
				// 		$objTpl->getActiveSheet()
				// 		 ->setCellValue('D'.($temp_i+1), "振込金額　・　入金　・　残高");
				// $objTpl->getActiveSheet()->mergeCells('G'.($temp_i+1).':'.'I'.($temp_i+1))->getStyle('A'.($temp_i+1).':'.'L'.($temp_i+1))->getFont()->setBold(true);

				// $objTpl->getActiveSheet()->getStyle('A'.($temp_i+1).':'.'L'.($temp_i+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');
				// $objTpl->getActiveSheet()->getStyle('G'.($temp_i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				// $objTpl->getActiveSheet()->getStyle('J'.($temp_i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				// $objTpl->getActiveSheet()->setCellValue('G'.($temp_i+1), number_format(round($sumtotalval)));
				// $objTpl->getActiveSheet()->setCellValue('J'.($temp_i+1), number_format(round($sumDepositTotalval)));
				// $objTpl->getActiveSheet()->getStyle('A'.($temp_i+1).':'.'L'.($temp_i+1))->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}

		  	$objTpl->setActiveSheetIndex();
			$objTpl->getActiveSheet(0)->setSelectedCells('A1');
			$noofdays=cal_days_in_month(CAL_GREGORIAN,$selectedYearMonth[1],$selectedYearMonth[0]);
			$objTpl->getActiveSheet()->setTitle($selectedYearMonth[1]."月1～".$noofdays."日");
	      	$flpath='.xls';
	      	header('Content-Type: application/vnd.ms-excel');
	      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      	header('Cache-Control: max-age=0');
	      	})->setFilename($excel_name)->download('xls');
		}
		return view('Transfer.index',['account_val' => $account_val,
									'year_month' => $year_month1,
									'current_year' => $current_year,
									'last_year' => $last_year,
									'dbprevious' => $dbprevious,
									'dbnext' => $dbnext,
									'date_month' => $date_month,
									'account_period' => $account_period,
									'db_year_month' => $db_year_month,
									'PAGING' => $PAGING,
									'incr' => $incr,
									'serialcolor' => $serialcolor,
									'disp' => $disp,
									'temp' => $temp,
									'temp1' => $temp1,
									'temp2' => $temp2,
									'expamt' => $expamt,
									'mainCatDetails' => $mainCatDetails,
									'subCatDetails' => $subCatDetails,
									'getbktr_det' => $getbktr_det,
									'index' => $slbk_query,
									'fee_rsTotalAmount' => $fee_rsTotalAmount,
									'rsTotalAmount' => $rsTotalAmount,
									'request' => $request]);
	}
	public static function ajaxsubsubject(Request $request) {
		$getsunsubject=Transfer::fnfetchsubsubject($request);
		$degreedata=json_encode($getsunsubject);
		echo $degreedata;
	}
	public static function ajaxloanname(Request $request) {
		$getsunsubject=Transfer::fnfetchloanname($request);
		$degreedata=json_encode($getsunsubject);
		echo $degreedata;
	}
	function addedit(Request $request) {
		$mainsub = Transfer::getmainsub($request);
		$bankname = Transfer::fetchbankname($request);
		// print_r($bankname);exit();
		return view('Transfer.addedit',['mainsub' => $mainsub,
										'bankname' => $bankname,
										'request' => $request]);
	}
	function edit(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankedit = "";
		$query = Transfer::editquery($request);
		$mainsub = Transfer::getmainsub($request);
		$bankname = Transfer::fetchbankname($request);
		// if (empty($query)) {
		// 	return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		// }
		$bankedit = $query[0]->bankname."-".$query[0]->bankaccno;
		return view('Transfer.addedit',['mainsub' => $mainsub,
										'bankname' => $bankname,
										'query' => $query,
										'bankedit' => $bankedit,
										'request' => $request]);
	}
	function addeditprocess(Request $request) {
		if($request->editflg == "1" || $request->editflg == "3") {
			$autoincId=Transfer::getautoincrement();
			$Loanno="Expenses_".date('YmdHis');
			$fileid="file1";
			$filename="";
            if($request->$fileid != "") {
              $extension = Input::file($fileid)->getClientOriginalExtension();
              $filename=$Loanno.'.'.$extension;
              $file = $request->$fileid;
              $destinationPath = '../InvoiceUpload/Expenses';
              if(!is_dir($destinationPath)) {
	            	mkdir($destinationPath, true);
	            }
	            chmod($destinationPath, 0777);
	            $file->move($destinationPath,$filename);
	            chmod($destinationPath."/".$filename, 0777);
            } else {
				$filename = $request->pdffiles; 
			}
			$spldm = explode('-', $request->txt_startdate);
			$checkSubmitCount = Transfer::checkSubmited($spldm);
			if ($request->editflg == "3") {
				$update = Transfer::addupdtransfer($request);
			}
			$insert = Transfer::inserttransferRec($request,$checkSubmitCount,$filename);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			$spldm = explode('-', $request->txt_startdate);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);  
			Session::flash('prevcnt', $request->prevcnt); 
			Session::flash('nextcnt', $request->nextcnt); 
			Session::flash('account_val', $request->account_val); 
			Session::flash('previou_next_year', $request->previou_next_year); 
		} else if($request->editflg == "2") {
			$fileid="file1";
			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$Loanno="Expenses_".date('YmdHis');
				$fileid="file1";
				$filename="";
				$filename=$Loanno.'.'.$extension;
				$file = $request->$fileid;
				$destinationPath = '../InvoiceUpload/Expenses';
				if(!is_dir($destinationPath)) {
					mkdir($destinationPath, true);
				}
				chmod($destinationPath, 0777);
				$file->move($destinationPath,$filename);
				chmod($destinationPath."/".$filename, 0777);
			} else {
				$filename = $request->pdffiles; 
			}
			$update = Transfer::UpdatetransferRec($request,$filename);
			// if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			// } else {
			// 	Session::flash('type', 'Updated Unsucessfully!'); 
			// 	Session::flash('type', 'alert-danger'); 
			// }
			$spldm = explode('-', $request->txt_startdate);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);  
			Session::flash('prevcnt', $request->prevcnt); 
			Session::flash('nextcnt', $request->nextcnt); 
			Session::flash('account_val', $request->account_val); 
			Session::flash('previou_next_year', $request->previou_next_year); 
		}
		return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function loanaddedit(Request $request) {
		$bankname = Transfer::banknames($request);
		$loantype = Transfer::getloantype($request);
		// print_r($loantype);exit();
		return view('Transfer.loanaddedit',[/*'mainsub' => $mainsub,*/
										'bankname' => $bankname,
										'loantype' => $loantype,
										'request' => $request]);
	}
	function loanedit(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$query = Transfer::editquerys($request);
		$bankname = Transfer::banknames($request);
		$loantype = Transfer::getloantype($request);
		return view('Transfer.loanaddedit',['query' => $query,
										'bankname' => $bankname,
										'loantype' => $loantype,
										'request' => $request]);
	}
	public static function loanaddeditprocess(Request $request) {
		if($request->editflg == "2") {
			$update = Transfer::updateLoanpaymentRec($request);
			// if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			// } else {
			// 	Session::flash('type', 'Updated Unsucessfully!'); 
			// 	Session::flash('type', 'alert-danger'); 
			// }
			$spldm = explode('-', $request->txt_startdate);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);  
			Session::flash('prevcnt', $request->prevcnt); 
			Session::flash('nextcnt', $request->nextcnt); 
			Session::flash('account_val', $request->account_val); 
			Session::flash('previou_next_year', $request->previou_next_year);
		} else {
			$spldm = explode('-', $request->txt_startdate);
			$checkSubmitCount = Transfer::checkSubmited($spldm);
			if ($request->editflg == "3") {
				$update = Transfer::addupdtransfer($request);
			}
			$insert = Transfer::insertLoanpaymentRec($request,$checkSubmitCount);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);  
		}
		return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function mulreg(Request $request) {
		$multi_reg="";
		$getreg_det = array();
		if(!empty($request->selMonth)) {
			$month=$request->selMonth;
		} else {
			$month=date('m');
		}
		if (!empty($request->selYear)) {
			$year=$request->selYear;
		} else {
			$year=date('Y');
		}
		$prefix = '';
		foreach ($request->multi_reg as $reg)
		{
			$multi_reg .= $prefix . '' . $reg . '';
			$prefix = ', ';
		}
		$query = Transfer::transfermultiregister($multi_reg,$month,$year);
		// print_r($query);exit();
		$i=0;
		foreach ($query as $key => $value) {
			$getreg_det[$i]['id'] = $value->id;
			$getreg_det[$i]['loc'] = $value->bankdate;
			$getreg_det[$i]['subject'] = $value->subject;
			$getreg_det[$i]['mainSubject'] = $value->mainSubject;
			$getreg_det[$i]['details'] =$value->details;
			$getreg_det[$i]['loan_flg'] =$value->loan_flg;
			$getreg_det[$i]['loanType'] =$value->loanType;
			$getreg_det[$i]['billno'] =$value->billno;
			$getreg_det[$i]['amount'] = number_format($value->amount);
			$getreg_det[$i]['fee'] = number_format($value->fee);
			$getreg_det[$i]['remark_dtl']= $value->remark_dtl;
			$getreg_det[$i]['bankname_id']= $value->bankname;
			$getreg_det[$i]['BankName']= $value->BankName;
			$getreg_det[$i]['bankname']= $value->bankname;
			$getreg_det[$i]['Bank_NickName']= $value->Bank_NickName;
			$getreg_det[$i]['bankaccno']= $value->AccNo;
			$getreg_det[$i]['salaryFlg']= $value->salaryFlg;
			if(Session::get('languageval') == "jp") {
				$getreg_det[$i]['Subject_jp'] = $value->Subject_jp;
				$getreg_det[$i]['sub_jap'] = $value->sub_jap;
			} else {
				$getreg_det[$i]['sub_eng'] = $value->sub_eng;
				$getreg_det[$i]['subject'] = $value->subject;
			}
		$i++;
		}
		$count = 0;
		$count =count($getreg_det);
		// print_r($getreg_det);exit();
		return view('Transfer.multireg',['request' => $request,
										'count' => $count,
										'getreg_det' => $getreg_det]);
	}
	public static function multiregprocess(Request $request) {
		$spldm = explode('-', $request->txt_startdate);
		$checkSubmitCount = Transfer::checkSubmited($spldm);
		$day = 0;
		$count = $request->count-1;
		for ($i=0; $i <= $count; $i++) {
			$loanType = 'loanType_'.$i;
			$request->day = $i;
			// ADD
				$splitdate = explode("-", $request->txt_startdate);
				$date = substr($request->txt_startdate,0,7);
				$insert = Transfer::transfermultireg($request,$day,$date,$checkSubmitCount);
				if($insert) {
					Session::flash('success', 'Inserted Sucessfully!'); 
					Session::flash('type', 'alert-success'); 
				} else {
					Session::flash('type', 'Inserted Unsucessfully!'); 
					Session::flash('type', 'alert-danger'); 
				}
				Session::flash('selMonth', $splitdate[1]); 
				Session::flash('selYear', $splitdate[0]); 
				Session::flash('prevcnt', $request->prevcnt); 
				Session::flash('nextcnt', $request->nextcnt); 
				Session::flash('account_val', $request->account_val); 
				Session::flash('previou_next_year', $request->previou_next_year); 
		}
		return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function download(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/expenses_detail.xls';
		$tempname = "Expenses_detail";
		$excel_name=$tempname;
		Excel::load($template_name, function($objPHPExcel) use($request) {
		$request->plimit = 1000;
		// Read the file

			$writeflag =0;
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$request->selMonth = str_pad($request->selMonth, 2, 0, STR_PAD_LEFT);
			$initial_value = 4;
			$objPHPExcel->setActiveSheetIndex(0);
			$cell1=$objPHPExcel->getActiveSheet()->getCell('A2')->getValue();
			$cell2=$objPHPExcel->getActiveSheet()->getCell('B2')->getValue();
			$cell3=$objPHPExcel->getActiveSheet()->getCell('C2')->getValue();
			$cell4=$objPHPExcel->getActiveSheet()->getCell('D2')->getValue();
			$cell5=$objPHPExcel->getActiveSheet()->getCell('E2')->getValue();
			$cell6=$objPHPExcel->getActiveSheet()->getCell('E3')->getValue();
			$cell7=$objPHPExcel->getActiveSheet()->getCell('F3')->getValue();
			$cell8=$objPHPExcel->getActiveSheet()->getCell('G2')->getValue();
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3=='Main Subject' && $cell4 =='Sub Subject' 
							   && $cell5 =='Amount' && $cell6 =='Cash' && $cell7 =='Expenses'&& $cell8 =='Remarks') {
				$writeflag ='1'; 
			}
			if($writeflag == '1'){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					 ->setCellValue('F1', "日付")
					 ->setCellValue('A2', "連番")
					 ->setCellValue('B2', "日付")
					 ->setCellValue('C2', "メイン 件名")
					 ->setCellValue('D2', "副件名")
					 ->setCellValue('E2', "単価")
					 ->setCellValue('E3', "現金")
					 ->setCellValue('F3', "経費")
					 ->setCellValue('G2', "備考");
				}
				$objPHPExcel->getActiveSheet()->mergeCells("A1:E1"); 
				if (Session::get('languageval') == "en") {
					$objPHPExcel->getActiveSheet()
					 ->setCellValue("A1",  "EXPENSES");
					$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				} else {
					$objPHPExcel->getActiveSheet()
				 	->setCellValue("A1", "経費");
				 	$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()
				 	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}
				$objPHPExcel->getActiveSheet()
					 ->setCellValue('G1', $request->selYear."年".$request->selMonth."月");
				$g_query = Transfer::download_transfer_1($request->selYear,$request->selMonth);
				$disp = 0;
				$temp_i = "";
				$temp = "";
				$rowclr = "";
				$amount1 = 0;
				$disp = count($g_query);
				$disptem=$disp;
				if(!$disp>0){
					$objPHPExcel->getActiveSheet()->removeRow(1,3);
					$objPHPExcel->getActiveSheet()->getRowDimension(1)->setVisible(false);
					$objPHPExcel->getActiveSheet()->getRowDimension(2)->setVisible(false);
					$objPHPExcel->getActiveSheet()->getRowDimension(3)->setVisible(false);
				}
				if($disp>0){
				$i = 0;
				$totval = 0;
				$k = 0;
				$gett = "";
				$temp = "";
				$rowclr = "";
				$loc = "";
				$exp_rsTotalAmount = 0;
				$get_det = array();
				$rsTotalAmount = 0;
				foreach ($g_query as $key => $value) {
					$get_det[$k]['id'] = $value->id;
					$get_det[$k]['date'] = $value->date;
					$get_det[$k]['subject'] = $value->subject;
					$get_det[$k]['Subject'] =$value->subject;
					// $get_det[$k]['Subject_jp'] =$value->Subject_jp;
					$get_det[$k]['details'] =$value->details;
					// $get_det[$k]['sub_eng'] =$value->sub_eng;
					// $get_det[$k]['sub_jap'] =$value->sub_jap;
					$get_det[$k]['currency_type']= $value->currency_type;
					// $get_det[$k]['jp_amount']= $value->jp_amount;
					$get_det[$k]['pettyFlg']= $value->pettyFlg;
					$get_det[$k]['remark_dtl']= $value->remark_dtl;
					$get_det[$k]['remark']= $value->remark_dtl;
					$get_det[$k]['amount']= $value->amount;
					$get_det[$k]['del_flg']= $value->del_flg;
					// $get_det[$k]['loan_flg']= $value->loan_flg;
					$get_det[$k]['salaryFlg']= $value->salaryFlg;
					$get_det[$k]['file_dtl']= $value->file_dtl;
					$get_det[$k]['Bank_NickName']= $value->Bank_NickName;
					$get_det[$k]['carryForwardFlg']= $value->carryForwardFlg;
					$get_det[$k]['transfer_flg']= $value->transfer_flg;
					$get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName)). ".".
					ucwords(mb_substr($value->FirstName, 0, 1,'utf-8'));
					if($value->carryForwardFlg == '1')
			    	{
						 	$gett += $value->amount;
			    	}
					if($value->subject == 'LastMonthTotal') {
						$get_det[$k]['bank']= 'LastMonthTotal';										
					} else if($value->subject == 'Last Month Balance') {
						$get_det[$k]['bank']= 'Last Month Balance';
					}else if($value->subject== 'Cash') {
						$get_det[$k]['bank']= $value->subject;
					}else {
						//$getsuj = expensesModel::selsubname($get_det[$k]['subject']);
							// if (Session::get('languageval') == "en") {
								$selectedField = "Subject";
							// } else {
								// $selectedField = "Subject_jp";
							// }
							$get_det[$k]['bank']=($get_det[$k][$selectedField]);
					}
					if($get_det[$k]['date']!= "") { 
						$get_det[$k]['loc']  = $get_det[$k]['date'];
					}	
					if($get_det[$k]['loc'] != $temp){
						if($value->subject == 'Last Month Balance') {
							$get_det[$k]['a']= date('Y-m-d');
						} else if( $value->date!= ""){
							$get_det[$k]['datedetail']=  $value->date;
						} 
					}
					if($value->subject == 'Last Month Balance') {
						$get_det[$k]['detail']= 'Last Month Balance';
					} else {
						//$get_det[$k]['detail']= stripslashes($row['details']);
							//$getsub_suj = expensesModel::selsubsubjectname($row['details'],$row['subject']);
							if (Session::get('languageval') == "en") {
								$selectedField = "sub_eng";
							} else {
								$selectedField = "sub_jap";
							}
							// $get_det[$k]['detail']=($get_det[$k][$selectedField]);
					}
					if($value->amount!= "" && $value->del_flg== "2") {
						if($value->amount<0 && $value->del_flg == "2"){
							$get_det[$k]['cash']= number_format($value->amount);
	                	    $get_det[$k]['totalamount']=$rsTotalAmount += $get_det[$k]['amount'];
						} else if($value->amount==0 && $value->del_flg == "2") {
							$get_det[$k]['cash']= number_format($value->amount);
							$get_det[$k]['totalamount']=$rsTotalAmount += $get_det[$k]['amount'];
						} else if($value->amount>0 && $value->del_flg== "2") {
							$get_det[$k]['cash']= number_format($value->amount);
							$rsTotalAmount += $value->amount;
						}
					} else {
						$get_det[$k]['cash']= "";
					}
					if($value->amount>0 && $value->salaryFlg == 1) {
						$get_det[$k]['expenses']=number_format($value->amount);
						$exp_rsTotalAmount += $value->amount;
					}
					else if($value->amount!= "" && $value->del_flg == "1") {
						if($value->amount <0 && $value->del_flg == "1"){
							$get_det[$k]['expenses']=number_format($value->amount);
	                    	$exp_rsTotalAmount += $get_det[$k]['amount'];
						} else if($value->amount==0 && $value->del_flg == "1") {
							$get_det[$k]['expenses']=number_format($value->amount);
							$exp_rsTotalAmount += $get_det[$k]['amount'];
						} else if($value->amount>0 && $value->del_flg == "1") {
							$get_det[$k]['expenses']=number_format($value->amount);
							$exp_rsTotalAmount += $value->amount;
						} 
					} else if($value->currency_type == "1") {
						$get_det[$k]['expenses']=number_format($get_det[$i]['jp_amount']);
						$exp_rsTotalAmount += $get_det[$i]['jp_amount'];
					} else {
						$get_det[$k]['expenses']="";
					}
					if($value->transaction_flg != "") {
						$get_det[$k]['transaction_flg']=$value->transaction_flg;
					} 
					if($value->remark_dtl != "") {
						$get_det[$k]['remark']=$value->remark_dtl;
					} 
					if($value->bname != "") {
						$get_det[$k]['bname']=$value->bname;
					} 
					if($value->bankaccno != "") {
						$get_det[$k]['bankaccno']=$value->bankaccno;
					} 
				$k++;
			}
			for ($i=0;$i<count($get_det);$i++) {
				$temp_i = $i+$initial_value;
				$sno = $i+1;
				if($get_det[$i]['date'] != "") { 
					$loc = $get_det[$i]['date'];
				}
				if($loc != $temp){
					$transferdate=$get_det[$i]['datedetail'];
					$transdate=explode("-" , $transferdate);
		 			$date_val = $transdate[2].'日';
		 			$temp_val = $i;
		 			if($rowclr==1){
						$style='dff1f4ff';
						$rowclr=0;
					} else {
						$style='FFFFFFFF';
						$rowclr=1;
					}
				} 
				if($get_det[$i]['subject'] != "") {

					if($get_det[$i]['subject'] == 'LastMonthTotal') {
						$main_subject_val= 'LastMonthTotal';										
					} else if($get_det[$i]['subject'] == 'Last Month Balance') {
						$main_subject_val= 'Last Month Balance';
					} else if($get_det[$i]['subject']== 'Cash' && $get_det[$i]['carryForwardFlg']!= '1' ) {
						$main_subject_val= $get_det[$i]['Bank_NickName']."-".$get_det[$i]['bankaccno'];
					} else if($get_det[$i]['carryForwardFlg']== '1' || $get_det[$i]['subject']== 'Cash'){
						$main_subject_val= "CarryForward";
					} else {
						$getsuj = Transfer::selsubname($get_det[$i]['subject']);
						$main_subject_val=$getsuj[0];
					}
				} else {
					if ($get_det[$i]['pettyFlg'] != 1) {
						$main_subject_val =  $get_det[$i]['EmpName'];
					} else {
						$main_subject_val =  "Petty Cash";
					}
				}
				if($get_det[$i]['subject'] != "") {
					if ($get_det[$i]['transfer_flg'] == 1) { 
						if (Session::get('languageval') == "en") {
							$trans_flg = "Debit";
						} else {
							$trans_flg = "引出";
						}
					} else if ($get_det[$i]['transfer_flg'] == 2) { 
						if (Session::get('languageval') == "en") {
							$trans_flg = "Credit";
						} else {
							$trans_flg = "入金";
						}
					} else if ($get_det[$i]['transfer_flg'] == 3) { 
						if ($get_det[$i]['transfer_flg'] == 1) {
							if (Session::get('languageval') == "en") {
							 $trans_flg = "Transfer・Debit";
							} else {
							 $trans_flg = "送金・引出";
							}
						} else {
							if (Session::get('languageval') == "en") {
								$trans_flg = "Transfer・Credit";
							} else {
								$trans_flg = "送金・入金";
							}
						}
					} else {
							$getsub_suj = Transfer::selsubsubjectname($get_det[$i]['details'],
																			$get_det[$i]['subject']);
							$trans_flg=$getsub_suj[0];
					}
				} else{
					if($get_det[$i]['del_flg'] == 2){
						$trans_flg = "Cash";
					} else {
						if ($get_det[$i]['pettyFlg'] != 1) {
							$trans_flg =  "Salary";
						} else {
							$trans_flg =  "Expenses";
						}
					}
					
				}


				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->getStartColor()->setRGB($style);

				$objPHPExcel->getActiveSheet()
				 ->setCellValue('A'.$temp_i, $sno)
				 ->setCellValue('B'.$temp_i, ltrim($date_val, '0'))
				 ->setCellValue('C'.$temp_i, $main_subject_val)
				 ->setCellValue('D'.$temp_i, $trans_flg)
				 ->setCellValue('E'.$temp_i, $get_det[$i]['cash'])
				 ->setCellValue('F'.$temp_i, $get_det[$i]['expenses'])
				->setCellValue('G'.$temp_i, $get_det[$i]['remark']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getAlignment()->setWrapText(true);
				//$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
				$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$mercell_val1=$temp_val+$initial_value;
				$mercell_val2=abs($temp_val-$i)+$mercell_val1;
				$objPHPExcel->getActiveSheet()->mergeCells('B'.$mercell_val1.':B'.$mercell_val2);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$mercell_val1)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$temp = $loc;
			}

			$cellvalue=array('F1','A2','B2','C2','D2','E2','E3','F3','G2');
			for ($i = 0; $i < count($cellvalue); $i++) {
				$objPHPExcel->getActiveSheet()->getStyle($cellvalue[$i])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle($cellvalue[$i])->getFill()->getStartColor()->setRGB('A7D4DD');
				
			}
			$objPHPExcel->getActiveSheet()->getStyle('A'.$initial_value.':G'.$temp_i)->applyFromArray($styleArray);
			$temp_i = $temp_i+2;
			$objPHPExcel->setActiveSheetIndex(0);
			if (Session::get('languageval') == "en") {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i,  "Total Amount   ");
				$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			} else {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i, "合計金額   ");
				 $objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			$amount1=$rsTotalAmount;
			$objPHPExcel->getActiveSheet()
			 ->setCellValue('E'.$temp_i, "¥ ".number_format($rsTotalAmount))
			 ->setCellValue('F'.$temp_i, "¥ ".number_format($exp_rsTotalAmount));
			$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i.':F'.$temp_i)->applyFromArray($styleArray);
			$yenTotalAmount = 0;
			if ($yenTotalAmount < 0) {
				$totalYenColor = "#FF0000";
			} else {
				$totalYenColor = "#0000FFff";
			}
			if (isset($get_det[$i]['totalamount']) && $get_det[$i]['totalamount'] < 0) {
				$totalRsColor = "#FF0000";
			} else {
				$totalRsColor = "#0000FFff";
			}
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->getColor()->setARGB($totalRsColor);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getFont()->getColor()->setARGB($totalYenColor);
			$temp_i = $temp_i+2;

			$thismonth = $rsTotalAmount-$gett;
			if (Session::get('languageval') == "en") {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i,  "This Month  ");
				$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			} else {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i, "今月   ");
				 $objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			$objPHPExcel->getActiveSheet()
			 ->setCellValue('E'.$temp_i, "¥ ".number_format($thismonth));
			$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->applyFromArray($styleArray);
			$temp_i = $temp_i+2;
			$balan = $rsTotalAmount- $exp_rsTotalAmount;
			if (Session::get('languageval') == "en") {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i,  "Balance   ");
				$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			} else {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i, "残高   ");
				 $objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			$objPHPExcel->getActiveSheet()
			 ->setCellValue('E'.$temp_i, "¥ ".number_format($balan));
			$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->setBold(true);
			if($balan == 0) {
				$balance_color="0000FFff";
			} else if($balan < 0) {
				$balance_color="#FF0000";
			} else if($balan > 10){
				$balance_color="#00FF0000";
			}
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->getColor()->setARGB($balance_color);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
			// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$objPHPExcel->getActiveSheet()->setTitle($request->selYear."-".$request->selMonth);
			}
			// Write the file
			//tranfer_download
			$g_query1 = Transfer::download_transfer($request,$request->selYear,$request->selMonth);
			$disp = 0;
			$disp = count($g_query1);
			if($disp>0){
				if(!$disptem>0){
					$trans=$temp_i+5;
					$trans1=$temp_i+4;
				} else {
					$trans=$temp_i+4;
					$trans1=$temp_i+3;
				}
			$initial_value=$trans;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$trans, "S.No");
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$trans, "Date");		
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$trans, "Main Subject");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$trans, "Sub Subject");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$trans, "Amount");
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$trans, "Charge");
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$trans1, "Date");
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$trans, "Remarks");

			$objPHPExcel->getActiveSheet()->getRowDimension($trans)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans.":G".$trans)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans.":G".$trans)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans.":G".$trans)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans.":G".$trans)->getFill()->getStartColor()->setRGB("A7D4DD");
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans1.':G'.$trans)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			
			$objPHPExcel->getActiveSheet()->getRowDimension($trans1)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$trans1.':G'.$trans1)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$trans1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$trans1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$trans1)->getFill()->getStartColor()->setRGB("A7D4DD");
			$objPHPExcel->getActiveSheet()->getStyle('F'.$trans1.':G'.$trans1)->applyFromArray($styleArray);

				if (Session::get('languageval') == "jp") {
					$objPHPExcel->getActiveSheet()
					 ->setCellValue('A'.$trans, "連番")
					 ->setCellValue('B'.$trans, "日付")
					 ->setCellValue('C'.$trans, "メイン 件名")
					 ->setCellValue('D'.$trans, "副件名")
					/* ->setCellValue('E2', "銀行")*/
					 ->setCellValue('E'.$trans, "単価")
					 ->setCellValue('F'.$trans1, "日付")
					 ->setCellValue('F'.$trans, "料金")
					 ->setCellValue('G'.$trans, "備考");
				} else {
					$objPHPExcel->getActiveSheet()
					 ->setCellValue('A'.$trans, "S.No")
					 ->setCellValue('B'.$trans, "Date")
					 ->setCellValue('C'.$trans, "Main Subject")
					 ->setCellValue('D'.$trans, "Sub Subject")
					/* ->setCellValue('E2', "銀行")*/
					 ->setCellValue('E'.$trans, "Amount")
					 ->setCellValue('F'.$trans1, "Date")
					 ->setCellValue('F'.$trans, "Charge")
					 ->setCellValue('G'.$trans, "Remarks");
				}
				$objPHPExcel->getActiveSheet()->mergeCells("A".$trans1.":E".$trans1); 
				if (Session::get('languageval') == "en") {
					$objPHPExcel->getActiveSheet()
					 ->setCellValue("A".$trans1,  "TRANSFER");
					$objPHPExcel->getActiveSheet()->getStyle("A".$trans1)->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				} else {
					$objPHPExcel->getActiveSheet()
				 	->setCellValue("A".$trans1, "振込");
				 	$objPHPExcel->getActiveSheet()->getStyle("A".$trans1)->getAlignment()
				 	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}
				$objPHPExcel->getActiveSheet()
					 ->setCellValue('G'.$trans1, $request->selYear."年".$request->selMonth."月");
				$objPHPExcel->getActiveSheet()->getStyle("G".$trans1)->getAlignment()
				 	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i = 0;
				$totval = 0;
				$k = 0;
				$fee_rsTotalAmount = 0;
				$loc1 = "";
				$temp1 = "";
				$getbktr_det = array();
				$rsTotalAmount = 0;$sno = 0;
				foreach ($g_query1 as $key => $value1) {
					$getbktr_det[$k]['id'] = $value1->id;
					$getbktr_det[$k]['bankdate'] = $value1->bankdate;
					$getbktr_det[$k]['subject'] =$value1->subject;
					$getbktr_det[$k]['details']= $value1->details;
					$getbktr_det[$k]['fee']= $value1->fee;
					$getbktr_det[$k]['banknameId']= $value1->bankname;
					$getbktr_det[$k]['amount']= $value1->amount;
					$getbktr_det[$k]['BankName']= $value1->bname;
					$getbktr_det[$k]['Bank_NickName']= $value1->Bank_NickName;
					$getbktr_det[$k]['AccNo']= $value1->bankaccno;
					$getbktr_det[$k]['remark_dtl'] =$value1->remark_dtl;
				    $getbktr_det[$k]['del_flg'] =$value1->del_flg;
				    $getbktr_det[$k]['loan_flg'] =$value1->loan_flg;
				    $getbktr_det[$k]['salaryFlg'] = $value1->salaryFlg;
					$getbktr_det[$k]['file_dtl']= $value1->file_dtl; 
					$get_det[$k]['EmpName'] = ucwords(strtolower($value1->LastName)). ".".
					ucwords(mb_substr($value1->FirstName, 0, 1,'utf-8'));
					$getsuj = Transfer::selbksubname($value1->subject);
					if(isset($getsuj[0])) {
						$getbktr_det[$k]['subject'] = $getsuj[0];
						$getbktr_det[$k]['subjectbank']= $getsuj[0];
					}
					//$getbktr_det[$k]['detail']=stripslashes($row['details']);
					$getsub_suj = Transfer::selsubtransfersubjectname($value1->details,$value1->subject);
					if(isset($getsub_suj[0])) {
						$getbktr_det[$k]['details'] = $getsub_suj[0];
						$getbktr_det[$k]['detail']=$getsub_suj[0];
					}
					if($getbktr_det[$k]['bankdate'] != "") {
						$getbktr_det[$k]['loc']  = $getbktr_det[$k]['bankdate'];
					}
					if($getbktr_det[$k]['loc'] != $temp){
						if($value1->bankdate!= ""){
							 $getbktr_det[$k]['bankdatedetais']=$value1->bankdate;
						} 
					}
					if(isset($getbktr_det[$k]['BankName'])) {
						if($getbktr_det[$k]['BankName'] != "") {
							$getbktr_det[$k]['loc1']  = $getbktr_det[$k]['BankName'];
						}
						if($getbktr_det[$k]['loc1'] != $temp1){
							if($value1->bankname!= ""){
								 $getbktr_det[$k]['bankdatedet']=$value1->bankname;
							} 
						}
					}
					if ($value1->amount!= "" && $getbktr_det[$i]['salaryFlg'] == 1 ) {
						$getbktr_det[$k]['Amountdisplay']=  number_format($value1->amount);
						$rsTotalAmount += $value1->amount;
					} else if($value1->amount!= "") {
						if($value1->amount<0){
							$getbktr_det[$k]['Amountdisplay']=  number_format($value1->amount);
                        	$rsTotalAmount += $value1->amount;
						} else if($value1->amount==0) {
							$getbktr_det[$k]['Amountdisplay']=  number_format($value1->amount);
							$rsTotalAmount +=$value1->amount;
						} else if($value1->amount>0) {
							$getbktr_det[$k]['Amountdisplay']=  number_format($value1->amount);
							$rsTotalAmount += $value1->amount;
						}
					} else {
						$getbktr_det[$k]['Amountdisplay']=  "";
					}
					if($value1->fee != "") {
						if($value1->fee<0){
						$getbktr_det[$k]['charge']= number_format($value1->fee);
                    	$fee_rsTotalAmount += $value1->fee;
						} else if($value1->fee==0) {
							$getbktr_det[$k]['charge']= number_format($value1->fee);
							$fee_rsTotalAmount += $value1->fee;
						} else if($value1->fee>0) {
							$getbktr_det[$k]['charge']= number_format($value1->fee);
							$fee_rsTotalAmount += $value1->fee;
						}
					} else {
						$getbktr_det[$k]['charge']= "";
					}
					if($value1->remark_dtl!= "") {
						$getbktr_det[$k]['remark']= $value1->remark_dtl;
					}  
					if($value1->month!= "") {
						$getbktr_det[$k]['month']= $value1->month;
					} 
					if($value1->year!= "") {
						$getbktr_det[$k]['year']= $value1->year;
					} 
					if ($getbktr_det[$k]['loan_flg'] == 1) {
						    $sqlbankquery = Transfer::regGetBankId($getbktr_det[$k]['banknameId']);
							foreach ($sqlbankquery as $key => $value2) {
								$getbktr_det[$k]['AccNo'] = $value2->AccNo;
								$getbktr_det[$k]['Bank_NickName'] = $value2->Bank_NickName;
								//$getbktr_det[$k]['Bank_NickName'] = $value2->bnName'];
								$getbktr_det[$k]['BankName'] =  $value2->bnName;
							}
						}

					$k++;
				}
				$temp_i = $trans+1;
				$temp_inc = 0;
				for ($i=0;$i<count($getbktr_det);$i++) {
					//$temp_i = $i+$initial_value;
					$loc = $getbktr_det[$i]['loc'];
					$loc1 = $getbktr_det[$i]['BankName']."-".$getbktr_det[$i]['AccNo'];
					$loc2 = $getbktr_det[$i]['AccNo'];
					if($loc1 != $temp1){
						$temp_val1 = $i;
					}
					if($loc != $temp){
						$temp_val = $i;
						$transferdate=$getbktr_det[$i]['bankdate'];
						$transdate=explode("-" , $transferdate);
						if($transdate[2]<10){
							$date_val = $transferdate[9].'日'; 
						}
						else{
			 				$date_val = $transferdate[8].$transferdate[9].'日';
						}
			 			if($rowclr==1){
							$style='dff1f4ff';
							$rowclr=0;
						} else {
							$style='FFFFFFFF';
							$rowclr=1;
						}
					} 
				
					if($getbktr_det[$i]['loan_flg'] != 1){
						$Mainsubject = $getbktr_det[$i]['subject'];
					}else{
						$Mainsubject = $getbktr_det[$i]['BankName'];
					}
					if($getbktr_det[$i]['loan_flg'] != 1){
						$Subsubject = $getbktr_det[$i]['details'];
					}else{
						$Subsubject = "Loan Payment";
					}
					if ($getbktr_det[$i]['salaryFlg'] == 1) {
						$Mainsubject = $get_det[$i]['EmpName'];
					}
					if ($getbktr_det[$i]['salaryFlg'] == 1) {
						$Subsubject = "Salary";
					}
					/*$bank_data = $getbktr_det[$i]['BankName']."-".$getbktr_det[$i]['AccNo'];*/
					//print_r($bank_data);exit();
					$objPHPExcel->setActiveSheetIndex(0);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->getStartColor()->setRGB($style);
					/*$objPHPExcel->getActiveSheet()*/
					if($loc1 != $temp1 ){
						$temp_val++;
						$temp_inc = 1;
						$bank_data=$getbktr_det[$i]['BankName']."-".$getbktr_det[$i]['AccNo'];
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objPHPExcel->getActiveSheet()
						 ->setCellValue('A'.$temp_i, $bank_data);
						$objPHPExcel->getActiveSheet()->getStyle("A".$temp_i)->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':G'.$temp_i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->getStartColor()->setRGB("D3D3D3");
						$sno++;
						$temp_i++;
						//print_r($temp_i);exit();
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $sno)
					 	 ->setCellValue('B'.$temp_i, ltrim($date_val, '0'))
						 ->setCellValue('C'.$temp_i, $Mainsubject)
						 ->setCellValue('D'.$temp_i, $Subsubject)
						 ->setCellValue('E'.$temp_i, $getbktr_det[$i]['Amountdisplay'])
						 ->setCellValue('F'.$temp_i, $getbktr_det[$i]['charge'])
						 ->setCellValue('G'.$temp_i, $getbktr_det[$i]['remark_dtl']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
						//$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					 	$mercell_date_val1=$temp_val+$initial_value;
					 	$mercell_date_val2=abs($temp_val-$i)+$mercell_date_val1;
					 	if ($temp_inc == 1) {
							$temp_i++;
							$mercell_date_val1=$temp_val+$initial_value;
							$mercell_date_val2=abs($temp_val-$i)+$mercell_date_val1;
						} 
					}else{
					    $sno++;
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $sno)
					 	 ->setCellValue('B'.$temp_i, ltrim($date_val, '0'))
						 ->setCellValue('C'.$temp_i, $Mainsubject)
						 ->setCellValue('D'.$temp_i, $Subsubject)
						 ->setCellValue('E'.$temp_i, $getbktr_det[$i]['Amountdisplay'])
						 ->setCellValue('F'.$temp_i, $getbktr_det[$i]['charge'])
						 ->setCellValue('G'.$temp_i, $getbktr_det[$i]['remark_dtl']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
						//$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						if (isset($temp_merger) && $temp_merger==1) {
							$mercell_date_val2=abs($temp_val-$i)+$mercell_date_val1;
							$temp_merger =0;
						}
						if ($i<(count($getbktr_det)-1)) {
							$temp_i=$temp_i+1;
						}
					}
					$temp = $loc;
					$temp1 = $loc1;
					$temp2 = $loc2; 
				}//exit();

			$objPHPExcel->getActiveSheet()->getStyle('A'.$initial_value.':G'.$temp_i)->applyFromArray($styleArray);
			$temp_i = $temp_i+2;
			$objPHPExcel->setActiveSheetIndex(0);
			if (Session::get('languageval') == "en") {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i,  "Total Amount   ");
				$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			} else {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i, "合計金額   ");
				 $objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			$objPHPExcel->getActiveSheet()
			 ->setCellValue('E'.$temp_i, "¥ ".number_format($rsTotalAmount))
			 ->setCellValue('F'.$temp_i, "¥ ".number_format($fee_rsTotalAmount));
			$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i.':F'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i.':F'.$temp_i)->applyFromArray($styleArray);
			$temp_i = $temp_i+2;
			$objPHPExcel->setActiveSheetIndex(0);
			if (Session::get('languageval') == "en") {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i,  "Expenses&Transfer Total");
				$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			} else {
				$objPHPExcel->getActiveSheet()
				 ->setCellValue('D'.$temp_i, "Expenses&Transfer Total");
				 $objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			$amount2=$rsTotalAmount;
			$amount3=$fee_rsTotalAmount;
			$exp_trans_amnt=$amount1+$amount2+$amount3;
			$totalRsColor = "#0000FFff";
			$objPHPExcel->getActiveSheet()
			 ->setCellValue('E'.$temp_i, "¥ ".number_format($exp_trans_amnt));
			$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i.':E'.$temp_i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->getColor()->setARGB($totalRsColor);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->applyFromArray($styleArray);
			$temp_i = $temp_i+2;
			$yenTotalAmount = 0;
			if ($yenTotalAmount < 0) {
				$totalYenColor = "#FF0000";
			} else {
				$totalYenColor = "#0000FFff";
			}
			if ($rsTotalAmount < 0) {
				$totalRsColor = "#FF0000";
			} else {
				$totalRsColor = "#0000FFff";
			}
			$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getFont()->getColor()->setARGB($totalRsColor);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getFont()->getColor()->setARGB($totalYenColor);
			$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
			$objPHPExcel->getActiveSheet()->setTitle($_REQUEST['selYear']."-".$_REQUEST['selMonth']);
			}
		}
			$flpath='.xls';
	      	header('Content-Type: application/vnd.ms-excel');
	      	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      	header('Cache-Control: max-age=0');
		})->setFilename($excel_name. "_" . date("Ymd"))->download('xls');
	}
	public static function transferhistory(Request $request) {

		if (!isset($request->subject) || !isset($request->bname)) {
			return Redirect::to('Transfer/index?mainmenu=company_transfer&time='.date('YmdHis'));
		}
		//Setting page limit
		if ($request->page=="") {
			$request->page = 1;
		}
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		$disp = 0;
		$i = 0;
		$amt = 0;
		$fee = 0;
		$totval = 0;
		$i=0;
		$chargeTotal=0;
		$amountTotal=0;
		$get_det = array();
		$slbk_query = array();
		$row = array();

		if($request->loan_flg == "1") {
			$slbk_query = Transfer::loanhistorydetails($request,$request->subject,$request->selYear,$request->selMonth);
		} else if($request->salaryflg == "1") {
			if ($request->bname == "") {
				$request->bname = 999;
				$slbk_query = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			} else {
				$slbk_query = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			}
		} else {
			$slbk_query = Transfer::transferhistorydetails($request,$request->subject,$request->selYear,$request->selMonth);
		}
		// For Amount Without Pagination
		if($request->loan_flg == "1") {
			$slbk_query2 = Transfer::loanhistorydetailsamount($request,$request->subject,$request->selYear,$request->selMonth);
		} else if($request->salaryflg == "1") {
			if ($_REQUEST['bname'] == "") {
				$request->bname = 999;
				$slbk_query2 = Transfer::salaryhistorydetailsamount($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			} else {
				$slbk_query2 = Transfer::salaryhistorydetailsamount($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			}
		} else {
			$slbk_query2 = Transfer::transferhistorydetailsamount($request,$request->subject,$request->selYear,$request->selMonth);
		}

		$disp = count($slbk_query);
		foreach ($slbk_query2 as $key => $value2) {
			if ($value2->BankName == "") {
				$value2->BankName = "Cash";
			}
			if($value2->BankName != "") {
				if ($request->salaryflg == "1") {
					$resultval = str_replace(",", "", $value2->amount);
				} else {
					$resultval = $value2->amount;
				}
				$amountTotal += $resultval;
			}
			if($value2->fee != "") {
				if ($request->salaryflg == "1") {
					$resultfee = str_replace(",", "", $value2->fee);
				} else {
					$resultfee = $value2->fee;
				}
				$chargeTotal = $chargeTotal+$resultfee;
			}
		}
		// print_r($request->all());exit;
		foreach ($slbk_query as $key => $value) {
			$get_det[$i]['id'] = $value->id;
			// $get_det[$i]['bankdetailid'] = $value->bankdetailid;
			$get_det[$i]['year'] = $value->year;
			$get_det[$i]['month'] = $value->month;
			$get_det[$i]['amount'] = $value->amount;
			$get_det[$i]['date'] = $value->bankdate;
			if(isset($value->salaryMonth)) {
				$get_det[$i]['salaryMonth'] = $value->salaryMonth;
			} else {
				$get_det[$i]['salaryMonth'] = "";
			}
			if($request->flgs != 5 || $request->flgs != "1") {
				if(isset($value->Subject) || isset($value->Subject_jp) || isset($value->sub_eng) || isset($value->sub_jap) || isset($value->remarks) || isset($value->mainid)) {
					$get_det[$i]['Subject'] = $value->Subject;
					$get_det[$i]['Subject_jp'] = $value->Subject_jp;
					$get_det[$i]['sub_eng'] = $value->sub_eng;
					$get_det[$i]['sub_jap'] = $value->sub_jap;
					$get_det[$i]['remarks'] = $value->remarks;
					$get_det[$i]['mainid'] = $value->mainid;
				}
			}
			if(isset($value->file_dtl)) {
				$get_det[$i]['file_dtl'] = $value->file_dtl;
			}
			$get_det[$i]['fee'] = $value->fee;
			$get_det[$i]['bankname'] = $value->BankName;
			$get_det[$i]['bankaccno'] = $value->bankaccno;
			if(isset($value->bankname) || isset($value->salaryFlg) || isset($value->AccNo) || isset($value->empNo) || isset($value->bankId) || isset($value->LastName)) {
				if($request->type == "sub") {

				} else {
					if ($request->flgs != "2") {
						$get_det[$i]['bank_id'] = $value->bankname;
						$get_det[$i]['salaryFlg'] = $value->salaryFlg;
						$get_det[$i]['AccNo'] = $value->AccNo;
					}
				}
				if($request->mainmenu == "company_transfer") {

				} else {
					$get_det[$i]['empNo'] = $value->empNo;
					$get_det[$i]['bankId'] = $value->bankId;
					$get_det[$i]['EmpName'] = ucwords(strtolower($value->LastName))
					. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
				}
				if ($request->flgs == "2") {
					$get_det[$i]['empNo'] = $value->empNo;
					$get_det[$i]['bankId'] = $value->bankId;
					$get_det[$i]['EmpName'] = ucwords(strtolower($value->LastName))
					. ".".ucwords(mb_substr($value->FirstName, 0, 1, 'utf-8'));
				}
			} else {
				$get_det[$i]['bank_id'] = "";
				$get_det[$i]['salaryFlg'] = "";
				$get_det[$i]['AccNo'] = "";
				$get_det[$i]['empNo'] = "";
				$get_det[$i]['bankId'] = "";
				$get_det[$i]['EmpName'] = "";
			}
			if ($get_det[$i]['bankname']=="") {
				$get_det[$i]['bankname']="Cash";
			}
			if($request->type == "sub") {
				if (isset($value->bankId)) {
					$value->bankid = $value->bankId;
				}
				$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankId);
			} else if($request->mainmenu == "company_transfer") {
				if ($request->flgs == "1") {
					$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankid);
				} else if ($request->flgs == "2") {
					if (isset($value->bankId)) {
						$value->bankid = $value->bankId;
					}
					$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankid);

				} else if($request->exptype1 == "2") {
					$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankid);
				} else {
					if($request->flgs == "5") {
						if ($request->mainmenu == "company_transfer") {
							$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankname);
						} else {
							$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankname);
						}
					} else {
						$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankname);
					}
				}
			} else {
				$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankid);
			}
			foreach ($sql as $key => $value) {
					$get_det[$i]['nickname'] = $value->Bank_NickName ;
			}
			$row[$i] = $sql;
			$i++;
		}
		return view('Transfer.transferhistory',['chargeTotal' => $chargeTotal,
										'amountTotal' => $amountTotal,
										'get_det' => $get_det,
										'disp' => $disp,
										'index' => $slbk_query,
										'row' => $row,
										'request' => $request]);
	}
	public static function transfersubhistory(Request $request) {
		if (!isset($request->subject)) {
			return Redirect::to('Transfer/index?mainmenu=company_transfer&time='.date('YmdHis'));
		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		$yr = $request->selYear;
		$mnth = $request->selMonth;
		$mnsub = $request->subject;
 		$salaryflg = $request->salaryflg;
 		$get_det = array();
 		$row = array();
 		$disp = 0;
		$i = 0;
		$totval = 0;
		$i=0;
		$chargeTotal=0;
		$amountTotal=0;
 		$slbk_query = Transfer::transfer_subhistorydetails($request,$mnsub,$yr,$mnth);
		// print_r($request->all());
 		// For Amount without Pagination 
 		$slbk_query1 = Transfer::transfer_subhistorydetailsamount1($request,$mnsub,$yr,$mnth);
		$disp = count($slbk_query);
		foreach ($slbk_query1 as $key => $value1) {
			if ($value1->BankName == "") {
				$value1->BankName = "Cash";
			}
        	if ( $value1->BankName != "" ) {
				$amountTotal = $amountTotal+$value1->amount;
			} 
			if ( $value1->fee != "" ) {
				$chargeTotal = $chargeTotal+$value1->fee;
			}
		}
		foreach ($slbk_query as $key => $value) {
			$get_det[$i]['id'] = $value->id;
			$get_det[$i]['year'] = $value->year;
			$get_det[$i]['month'] = $value->month;
			$get_det[$i]['amount'] = $value->amount;
			$get_det[$i]['date'] = $value->bankdate;
			$get_det[$i]['Subject'] = $value->Subject;
			$get_det[$i]['Subject_jp'] = $value->Subject_jp;
			$get_det[$i]['sub_eng'] = $value->sub_eng;
			$get_det[$i]['sub_jap'] = $value->sub_jap;
			$get_det[$i]['remarks'] = $value->remarks;
			if(isset($value->mainid)) {
				$get_det[$i]['mainid'] = $value->mainid;
			} else {
				$get_det[$i]['mainid'] = "";
			}
			$get_det[$i]['file_dtl'] = $value->file_dtl;
			$get_det[$i]['fee'] = $value->fee;
			$get_det[$i]['bankname'] = $value->BankName;
			$get_det[$i]['bankaccno'] = $value->bankaccno;
			if ($get_det[$i]['bankname'] == "") {
				$get_det[$i]['bankname'] = "Cash";
			}
			$sql = Transfer::nickname($get_det[$i]['bankaccno'],$value->bankid);
			foreach ($sql as $key => $value) {
				if(isset($value->Bank_NickName)) {
					$get_det[$i]['nickname'] = $value->Bank_NickName ;
				} else {
					$get_det[$i]['nickname'] = "";
				}
			}
			$row[$i] = $sql;
			$i++;
		}
		return view('Transfer.transfersubhistory',['chargeTotal' => $chargeTotal,
										'amountTotal' => $amountTotal,
										'get_det' => $get_det,
										'index' => $slbk_query,
										'row' => $row,
										'request' => $request]);
	}
	public static function empnamehistory(Request $request) {
		if (!isset($request->empid)) {
			return Redirect::to('Transfer/index?mainmenu=company_transfer&time='.date('YmdHis'));
		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		$getsalary = array();
		$getsalary1 = array();
		$amountTotal = 0;
		$chargeTotal = 0;
		$salaryquery = Transfer::fnsalaryData($request);
		// For Amount without Pagination
		$salaryquery1 = Transfer::fnsalaryDataamount($request);
		$q = 0;
		foreach ($salaryquery1 as $key => $value) {
				$resultval =  $value->amount;
				$amountTotal = $amountTotal+str_replace(",", "", $resultval);
				$resultfee =  $value->fee;
				$chargeTotal = $chargeTotal+str_replace(",", "", $resultfee);
			}
		$disp = 0;
		$disp = count($salaryquery);
	 	$i = 0;
	 	foreach ($salaryquery as $key => $value) {
	 		$getsalary[$i]['id'] = $value->id;
	 		$getsalary[$i]['bankdate'] = $value->bankdate;
	 		$getsalary[$i]['month'] = $value->month;
	 		$getsalary[$i]['year'] = $value->year;
	 		if(isset($value->Subject) || isset($value->remarks) || isset($value->file_dtl) || isset($value->salaryFlg)) {
	 			$getsalary[$i]['Subject'] = $value->Subject;
	 			$getsalary[$i]['remarks'] = $value->remarks;
	 			$getsalary[$i]['file_dtl'] = $value->file_dtl;
	 			$getsalary[$i]['salaryFlg'] = $value->salaryFlg;
	 		}
	 		$getsalary[$i]['BankName'] = $value->BankName;
	 		$getsalary[$i]['amount'] = $value->amount;
	 		$getsalary[$i]['fee'] = $value->fee;
	 		$getsalary[$i]['bankId'] = $value->bankId;
	 		$getsalary[$i]['bankaccno'] = $value->bankaccno;
	 		$i++;
	 	}
	 	$filecount = count($getsalary);
	 	return view('Transfer.empsalaryhistory',['index' => $salaryquery,
										'amountTotal' => $amountTotal,
										'chargeTotal' => $chargeTotal,
										'disp' => $disp,
										'getsalary' => $getsalary,
										'filecount' => $filecount,
										'request' => $request]);
	}
	public static function historydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesHistory.xls';
		$tempname = "ExpensesHistory";
		$excel_name=$tempname;
		Excel::load($template_name, function($objPHPExcel) use($request) {
		// Read the file
			$writeflag =0;
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex(0);				
			$cell1=$objPHPExcel->getActiveSheet()->getCell('A2')->getValue();				
			$cell2=$objPHPExcel->getActiveSheet()->getCell('B2')->getValue();				
			$cell3=$objPHPExcel->getActiveSheet()->getCell('C2')->getValue();				
			$cell4=$objPHPExcel->getActiveSheet()->getCell('D2')->getValue();				
			$cell5=$objPHPExcel->getActiveSheet()->getCell('E2')->getValue();				
			$cell6=$objPHPExcel->getActiveSheet()->getCell('F2')->getValue();				
			$cell7=$objPHPExcel->getActiveSheet()->getCell('G2')->getValue();				
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3=='Bank' && $cell4 =='Sub Subject' 				
					&& $cell5 =='Amount' && $cell6 =='Charge' && $cell7 =='Remarks') {
				$writeflag =1; 
			}
			if($writeflag == "1"){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "銀行")
					->setCellValue('D2', "副件名")
					->setCellValue('E2', "単価")
					->setCellValue('F2', "料金")
					->setCellValue('G2', "備考");
				}
				if($request->bname == "") {
					$request->bname = "999";
				}
				if($request->pettyflg != 1 ) {
					if($request->loan_flg == 1 ) {
						$sql = Transfer::loanhistorydetails($request,$request->subject,$request->selYear,$request->selMonth);
					} else if($request->salaryflg == "1") {
						$sql = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
					} else {
						$sql = Transfer::transferhistorydetails($request,$request->subject,$request->selYear,$request->selMonth);
					}
				} else {
					if ($request->delflg == "0") {
						$sql = Expenses::pettycash_subhistoryvalues_details($request,$request->delflg,$request->selYear,$request->selMonth);
					} else {
						$sql = Expenses::pettycash_subhistoryvalues_detailsdelflg1($request,$request->delflg,$request->selYear,$request->selMonth);
					}
				}
				// For Amount Without Pagination
					if($request->pettyflg != 1 ) {
						if($request->loan_flg == "1") {
							$slbk_query2 = Transfer::loanhistorydetailsamount($request,$request->subject,$request->selYear,$request->selMonth);
						} else if($request->salaryflg == "1") {
							if ($_REQUEST['bname'] == "") {
								$request->bname = 999;
								$slbk_query2 = Transfer::salaryhistorydetailsamount($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
							} else {
								$slbk_query2 = Transfer::salaryhistorydetailsamount($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
							}
						} else {
							$slbk_query2 = Transfer::transferhistorydetailsamount($request,$request->subject,$request->selYear,$request->selMonth);
						}
					} else {
						if ($request->delflg == "0") {
							$slbk_query2 = Expenses::pettycash_subhistoryvalues_detailsamountdel($request,$request->delflg,$request->selYear,$request->selMonth);
						} else {
							$slbk_query2 = Expenses::pettycash_subhistoryvalues_detailsdelflg1amount($request,$request->delflg,$request->selYear,$request->selMonth);
						}
					}
					$disp = 0;
					$get_det = array();
					$row = array();
					$disp = count($sql);
					$i = 0;
					$totval = 0;
					$i=0;
					$chargeTotal=0;
					$amountTotal=0;
					foreach ($slbk_query2 as $key => $value2) {
						if (isset($value2->BankName) && $value2->BankName == "") {
							$value2->BankName = "Cash";
						} else if(empty($value2->bankname)) {
							$value2->bankname = "Cash";
						}
						if(isset($value2->BankName)) {
							if ($request->salaryflg == "1") {
								$resultval = str_replace(",", "", $value2->amount);
							} else {
								$resultval = $value2->amount;
							}
							$amountTotal += $resultval;
						} else if (isset($value2->bankname)) {
							if ($request->salaryflg == "1") {
								$resultval = str_replace(",", "", $value2->amount);
							} else {
								$resultval = $value2->amount;
							}
							$amountTotal += $resultval;
						}
						if(isset($value2->fee)) {
							if ($request->salaryflg == "1") {
								$resultfee = str_replace(",", "", $value2->fee);
							} else {
								$resultfee = $value2->fee;
							}
							$chargeTotal = $chargeTotal+$resultfee;
						}
					}
				foreach ($sql as $key => $value1) {
					$get_det[$i]['id'] = $value1->id;
					$get_det[$i]['year'] = $value1->year;
					$get_det[$i]['month'] = $value1->month;
					$get_det[$i]['amount'] = $value1->amount;
					if (isset($value1->bankdate)) {
						$get_det[$i]['date'] = $value1->bankdate;
					}
					if (isset($value1->date)) {
						$get_det[$i]['pettycashdate'] = $value1->date;
					}
					if(isset($value1->Subject)) {
						$get_det[$i]['Subject'] = $value1->Subject;
					}
					if(isset($value1->Subject_jp)) {
						$get_det[$i]['Subject_jp'] = $value1->Subject_jp;
					}
					if(isset($value1->sub_eng)) {
						$get_det[$i]['sub_eng'] = $value1->sub_eng;
					} else {
						$get_det[$i]['sub_eng'] = "";
					}
					if(isset($value1->sub_jap)) {
						$get_det[$i]['sub_jap'] = $value1->sub_jap;
					}
					if(isset($value1->remarks)) {
						$get_det[$i]['remarks'] = $value1->remarks;
					} else {
						$get_det[$i]['remarks'] = "";
					}
					if(isset($value1->mainid)) {
						$get_det[$i]['mainid'] = $value1->mainid;
					}
					if(isset($value1->file_dtl)) {
						$get_det[$i]['file_dtl'] = $value1->file_dtl;
					}
					if(isset($value1->pettyFlg)) {
						$get_det[$i]['petty_flg'] = $value1->pettyFlg;
					}
					if(isset($value1->transaction_flg)) {
						$get_det[$i]['transaction_flg'] = $value1->transaction_flg;
					}
					if(isset($value1->Bank_NickName)) {
						$get_det[$i]['Bank_NickName'] = $value1->Bank_NickName;
					} else {
						$get_det[$i]['Bank_NickName'] = "";
					}
					if(isset($value1->AccNo)) {
						$get_det[$i]['AccNo'] = $value1->AccNo;
					}
					if(isset($value1->del_flg)) {
						$get_det[$i]['del_flg'] = $value1->del_flg;
					}
					if(isset($value1->loan_flg)) {
						$get_det[$i]['loan_flg'] = $value1->loan_flg;
					}
					if(isset($value1->salaryFlg)) {
						$get_det[$i]['salaryFlg'] = $value1->salaryFlg;
					}
					if(isset($value1->bankid)) {
						$value1->bankid = $value1->bankid;
					} else {
						$value1->bankid  = "";
					}
					if(isset($value1->LastName) || isset($value1->FirstName)) {
						$get_det[$i]['EmpName'] = ucwords(strtolower($value1->LastName)). ".".
					ucwords(mb_substr($value1->FirstName, 0, 1,'utf-8'));
					}
					if (isset($value1->fee)) {
						$get_det[$i]['fee'] = $value1->fee;
					} else {
						$get_det[$i]['fee'] = 0;
					}
					if (isset($value1->BankName)) {
						$get_det[$i]['bankname'] = $value1->BankName;
					} else if (isset($value1->bankname)) {
						$get_det[$i]['bankname'] = $value1->bankname;
					}
					// else {
						// $get_det[$i]['bankname'] = "";
					// }
					$get_det[$i]['bankaccno'] = $value1->bankaccno;

					if( $request->pettyflg == 1 ) {
						$get_det[$i]['date'] = $value1->date;
					}
					if (isset($get_det[$i]['bankname'])) {
						$get_det[$i]['bankname']=$get_det[$i]['bankname'];
					} else {
						$get_det[$i]['bankname']="Cash";
					}
					$nickname = Transfer::nickname($get_det[$i]['bankaccno'],$value1->bankid);
		       		    foreach ($nickname as $key => $value4) {
		       		    	if (isset($value4->Bank_NickName)) {
		       		    		$row[$i]['Bank_NickName']=$value4->Bank_NickName;	
		       		    	} else {
		       		    		$row[$i]['Bank_NickName']="";
		       		    	}
		       		    }
					$i++;
				} 
				$temp_i = 3;
				$initial_value = 5;
				$rowbktrclrr=0;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				if($request->pettyflg == 1 && $request->delflg == 0 ) {
               		$get_title = "PettyCash";
               } else if($request->delflg == 1){
               		$get_title = "PettyCash"."->"."Expenses";
               } else {
               		$get_title = "PettyCash"."->"."Cash";
               }
			    if($request->loan_flg == 1) {
			    	$get_det[0]['Subject'] = $get_det[0]['bankname']."-".$get_det[0]['AccNo'];
			    	$get_det[0]['Subject_jp'] = $get_det[0]['bankname']."-".$get_det[0]['AccNo'];
			    } else if($request->salaryflg == 1){
			    	$get_det[0]['Subject'] = $get_det[0]['bankname']."-".$get_det[0]['bankaccno'];
			    	$get_det[0]['Subject_jp'] = $get_det[0]['bankname']."-".$get_det[0]['bankaccno'];
			    }
			    if (count($get_det)=="") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', "");
				}else{
					if ($request->pettyflg == 1) {
							$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_title);	
					} else {
						if (Session::get('languageval') == "en") { 
							$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['Subject']);
						} else { 
							$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['Subject_jp']);
						} 
					}
				}
				$tmpyr=0;
				$sno=0;
				$j=0;
					$tempdate = 0;
					$temp = 0;
				for ($j = 0; $j <count($get_det); $j++) {
					$temp_val = $j;
					if ($tempdate !=$get_det[$j]['id']) {
						if($rowbktrclrr==1){
							$style='dff1f4ff';
							$rowbktrclrr=0;
						} else {
							$style='FFFFFFFF';
							$rowbktrclrr=1;
						}
					} 
					if ($j == 0 ) { 
						$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('E3', "¥ ".number_format($amountTotal))
						 ->setCellValue('F3', "¥ ".number_format($chargeTotal));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					}
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;$result1=0;$fee=0;
						$temp_i++;
						if($request->pettyflg != 1 ) {
							if($request->loan_flg == 1 ) {
								$sql1 = Transfer::loanhistorydetails($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']);
							} else if($request->salaryflg == "1") {
								$sql1 = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);
							} else {
								$sql1 = Transfer::transferhistorydetailsamount($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']);
							}
						} else {
							if ($request->delflg == "0") {
								$sql1 = Transfer::pettycash_history_details($get_det[$j]['year'],$get_det[$j]['month']);
							} else {
								$sql1 = Transfer::pettycash_subhistory_details($request->delflg,$get_det[$j]['year'],$get_det[$j]['month']);
							}
						}
						$feeval = 0;
						foreach ($sql1 as $key => $value3) {
							if ($request->salaryflg == "1") {
								$amount = str_replace(",", "", $value3->amount);
				        		$feeval = str_replace(",", "", $value3->fee);
							} else {
								$amount =  $value3->amount;
								if (isset($value3->fee)) {
									$feeval =  $value3->fee;
								}
							}
							$results=$amount;
							$result1=$feeval;
							$amt=$amt+$amount;
							$fee=$fee+$result1;
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':D'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('E'.$temp_i,number_format($amt))
						 ->setCellValue('F'.$temp_i,number_format($fee));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i.':F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':F'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':G'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':G'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$tmpyr=0;	
					}
					$tmpyr=$get_det[$j]['year'];$tmpmth=$get_det[$j]['month'];
					$temp_i++;
					$objPHPExcel->setActiveSheetIndex(0);
					$sno++;
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$temp_i, $sno);
					if ($tempdate !=$get_det[$j]['date']) {
						$mercell_val1=$temp_val+$initial_value;
						$mercell_val2=abs($temp_val-$j)+$mercell_val1;
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$mercell_val1.':B'.$mercell_val2);
						//$objPHPExcel->getActiveSheet()->getStyle('B'.$mercell_val1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						//print_r($mercell_val1);print_r($mercell_val2);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					if(isset($get_det[$j]['loan_flg']) && $get_det[$j]['loan_flg'] == 1 ) {
			     		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, 'Loan');
				    } else if($request->pettyflg == 1 ){
				    	if ($get_det[$j]['del_flg'] == 1) {
							if (Session::get('languageval') == "en") {
				    			$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i,  
				    				$get_det[$j]['Subject']);
				    		} else {
				    			$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i,  
				    				$get_det[$j]['Subject_jp']);
				    		} 
				    	} else {
				    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i,  
				    			$get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno']);
				    	}
				    } else if($request->salaryflg == 1 ){ 
				    	$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['EmpName']);
				    } else {
				    		// echo $get_det[$j]['bankname'];
					     	if ($get_det[$j]['bankname']=="Cash") {
					     		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, 'Cash');
					     	} else {
					     		$bankNickname="";
					     		if (isset($row[$j]['Bank_NickName'])) {
					     			$bankNickname = $row[$j]['Bank_NickName'];
					     		}
					     		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $bankNickname."-".$get_det[$j]['bankaccno']);
					     		// $objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, 'Cash');
					     	}
			     	}
			     	if($request->loan_flg == 1 ) {
				     	if (Session::get('languageval') == "en") {
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, 'Loan Payment');
						} else {
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, 'Loan Payment');
						}
			     	} else if($request->salaryflg == 1 ) {
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, 'Salary');
			     	}else if($request->pettyflg == 1 ) {
			     		if($get_det[$j]['del_flg'] == 1){
			     			if (Session::get('languageval') == "en") {
			     				$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['sub_eng']);
			     			} else {
			     				$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['sub_jap']);
			     			}
			     		} else {
			     			if($get_det[$j]['transaction_flg'] == 1){
								$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, 
									'Debit');
							} else {
								$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, 
									'Credit');
							}
						}
     				}
     				else {
						if (Session::get('languageval') == "en") {
			     				$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['sub_eng']);
			     			} else {
			     				$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['sub_jap']);
			     			}
					}
			     	if($request->salaryflg != 1 ) {
			     		$amunt = number_format($get_det[$j]['amount']);
			     	} else {
			     		$amunt = $get_det[$j]['amount'];
			     	}
			     	if($request->salaryflg != 1 ) {
			     		$fee = number_format($get_det[$j]['fee']);
			     	} else {
			     		$fee = $get_det[$j]['fee'];
			     	}
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i, $amunt);
					if ($get_det[$j]['bankname']=="Cash") {
			     		$objPHPExcel->getActiveSheet()->setCellValue('F'.$temp_i, $fee);
			     	} else {
			     		$objPHPExcel->getActiveSheet()->setCellValue('F'.$temp_i, $fee);
			     	}
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$temp_i, $get_det[$j]['remarks']);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:G'.$temp_i)->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
					$temp = $get_det[$j]['bankname'];
					$tempdate = $get_det[$j]['date'];
					$i++;
					
				}
			$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
			$objPHPExcel->getActiveSheet()->setTitle($request->selYear."-".$request->selMonth);
			}
		$flpath='.xls';
		header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="'.$flpath.'"');
	    header('Cache-Control: max-age=0');
		})->setFilename($excel_name. "_" . date("Ymd"))->download('xls');
	}
	public static function salaryhistorydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/emp_salary_history.xls';
		$tempname = "emp_salary_history";
		$excel_name=$tempname;
		Excel::load($template_name, function($objPHPExcel) use($request) {
		// Read the file
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			if($request->bname == "") {
				$request->bname = "999";
			}
			if ($request->salaryflg == "1") {
				$sql = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			} else {
				$sql = Transfer::fnsalaryData($request);
			}
			$get_det = array();
			$amtval=0;
			$feeval=0;
			$disp = 0;
			$disp = count($sql);
			$i = 0;
			$j = 0;
			$sno = 0;
			$totval = 0;
			$chargeTotal=0;
			$amountTotal=0;
			if ($request->salaryflg == "1") {
				$forgrantTotal = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			} else {
				$forgrantTotal = Transfer::fnsalaryDatatotal($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
			}
			foreach ($forgrantTotal as $key => $value) {
				$resultval =  $value->amount;
				$amountTotal = $amountTotal+str_replace(",", "", $resultval);
				$resultfee =  $value->fee;
				$chargeTotal = $chargeTotal+str_replace(",", "", $resultfee);
			}
			// print_r($sql);exit();
			foreach ($sql as $key => $value) {
				$get_det[$i]['id'] = $value->id;
				$get_det[$i]['year'] = $value->year;
				$get_det[$i]['month'] = $value->month;
				$get_det[$i]['amount'] = $value->amount;
				$get_det[$i]['date'] = $value->bankdate;
				if(isset($value->date)) {
					$get_det[$i]['pettycashdate'] = $value->date;
				}
				if(isset($value->Subject)) {
					$get_det[$i]['Subject'] = $value->Subject;
				}
				if(isset($value->Subject_jp)) {
					$get_det[$i]['Subject_jp'] = $value->Subject_jp;
				}
				if(isset($value->sub_eng)) {
					$get_det[$i]['sub_eng'] = $value->sub_eng;
				}
				if(isset($value->sub_jap)) {
					$get_det[$i]['sub_jap'] = $value->sub_jap;
				}
				if(isset($value->remarks)) {
					$get_det[$i]['remarks'] = $value->remarks;
				} else {
					$get_det[$i]['remarks'] = "";
				}
				if(isset($value->mainid)) {
					$get_det[$i]['mainid'] = $value->mainid;
				}
				if(isset($value->file_dtl)) {
					$get_det[$i]['file_dtl'] = $value->file_dtl;
				}
				if(isset($value->AccNo)) {
					$get_det[$i]['AccNo'] = $value->AccNo;
				}
				if(isset($value->del_flg)) {
					$get_det[$i]['del_flg'] = $value->del_flg;
				}
				if(isset($value->loan_flg)) {
					$get_det[$i]['loan_flg'] = $value->loan_flg;
				}
				if(isset($value->pettyFlg)) {
					$get_det[$i]['petty_flg'] = $value->pettyFlg;
				}
				if(isset($value->salaryFlg)) {
					$get_det[$i]['salaryFlg'] = $value->salaryFlg;
				}
				$get_det[$i]['fee'] = $value->fee;
				$get_det[$i]['bankname'] = $value->BankName;
				$get_det[$i]['bankaccno'] = $value->bankaccno;
				$get_det[$i]['bankId'] = $value->bankId;
				$get_det[$i]['salaryMonth'] = $value->salaryMonth;
				$get_det[$i]['EmpName'] = ucwords(strtolower($value->LastName)). ".".
					ucwords(mb_substr($value->FirstName, 0, 1,'utf-8'));
				$amtval += str_replace(",", "", $value->amount);
				$feeval += str_replace(",", "", $value->fee);
				$i++;
			}
			$temp_i = 3;
			$initial_value = 5;
			$rowbktrclrr = 0;
			$tmpyr=0;
			$bluecolor = '#0000FFff';
			$headerbgcolor = 'D3D3D3';
			if ($request->salaryflg == "1") {
				if(($get_det[0]['bankId'])==999){
					$objPHPExcel->getActiveSheet()->setCellValue('C1',"Cash");
				} else {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['bankname']."-".$get_det[0]['bankaccno']);
				}
			} else{
				$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['EmpName']);
			}
			$tempmonth ="";
			$temp = $get_det[$j]['bankname'];
			$tempdate = $get_det[$j]['date'];
				for ($j = 0; $j <count($get_det); $j++) {
					$temp_val = $j;
					if ($tempdate !=$get_det[$j]['id']) {
						if($rowbktrclrr==1){
							$style='dff1f4ff';
							$rowbktrclrr=0;
						} else {
							$style='FFFFFFFF';
							$rowbktrclrr=1;
						}
					}
					if ( $j == 0 ) { 
						$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('F3', "¥ ".number_format($amtval))
						 ->setCellValue('G3', "¥ ".number_format($feeval));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$fee=0;$fee=0;
						$temp_i++;
						if($request->salaryflg == "1") {
							 $data = Transfer::salaryhistorydetails($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);
						} else {
							$data = Transfer::fnsalaryDatatotal($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);
						}
						foreach ($data as $key => $value) {
							$resultval =  $value->amount;
							$resultfee =  $value->fee;
							$amt = $amt+str_replace(",", "", $value->amount);
							$fee = $fee+str_replace(",", "", $value->fee);
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':E'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('F'.$temp_i, " ".number_format($amt))
						 ->setCellValue('G'.$temp_i, " ".number_format($fee));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i.':G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':G'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':H'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':H'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$tmpyr=0;	
					}
					$tmpyr=$get_det[$j]['year'];$tmpmth=$get_det[$j]['month'];
					$temp_i++;
					$objPHPExcel->setActiveSheetIndex(0);
					$sno++;
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$temp_i, $sno);
					if ($tempdate !=$get_det[$j]['date']) {
						$mercell_val1=$temp_val+$initial_value;
						$mercell_val2=abs($temp_val-$j)+$mercell_val1;
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$mercell_val1.':B'.$mercell_val2);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$mercell_val1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					if ($request->salaryflg == "1") {
						$monthCheck = $get_det[$j]['salaryMonth'];
					} else {
						$monthCheck = $get_det[$j]['month'];
					}
					if (($tempmonth != $monthCheck)) {
						$mercell_val1=$temp_val+$initial_value;
						$mercell_val2=abs($temp_val-$j)+$mercell_val1;
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$mercell_val1.':C'.$mercell_val2);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$mercell_val1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						if ($request->salaryflg == "1") {
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['salaryMonth']);
						}else{
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['month']);
						}
					}
					if ($request->salaryflg == "1") {
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['EmpName']);
						}else{
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i,"Salary");
					}
					if ($request->salaryflg == "1") {
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i,"Salary");
					}else{
						if(($get_det[$j]['bankId'])==999){
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i,"Cash");
						} else {
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i,$get_det[$j]['bankname']."-".$get_det[$j]['bankaccno']);
						}
					}
					$amunt = $get_det[$j]['amount'];
					$fee = $get_det[$j]['fee'];
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$temp_i, $amunt);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$temp_i, $fee);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$temp_i, $get_det[$j]['remarks']);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":H".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":H".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$temp_i)->applyFromArray($styleArray);
					//$objPHPExcel->getDefaultStyle()->getFont()->setName( 'Arial');
					//$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$temp = $get_det[$j]['bankname'];
					$tempdate = $get_det[$j]['date'];
					if($request->salaryflg == "1"){
						$tempmonth = $get_det[$j]['salaryMonth'];
					}else{
						$tempmonth = $get_det[$j]['month'];
					}
					$i++;
				}
				if($request->salaryflg == "1"){
					$objPHPExcel->getActiveSheet()->setCellValue('A1', "MainSubject");
					$objPHPExcel->getActiveSheet()->setCellValue('D2', "EmpName");
					$objPHPExcel->getActiveSheet()->setCellValue('E2', "Subject");
				}
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
				$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
				// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				if($request->salaryflg == "1"){
					$objPHPExcel->getActiveSheet()->setTitle("SalaryHistory");
				}else{
					$objPHPExcel->getActiveSheet()->setTitle("EmployeeSalaryHistory");
				}
		$flpath='.xls';
		header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="'.$flpath.'"');
	    header('Cache-Control: max-age=0');
		})->setFilename($excel_name. "_" . date("Ymd"))->download('xls');
	}
	public static function transfersubhistorydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesSubHistory.xls';
		$tempname = "ExpensesHistory";
		$excel_name=$tempname;
		Excel::load($template_name, function($objPHPExcel) use($request) {
		// Read the file
			$writeflag ='0';
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$objPHPExcel->setActiveSheetIndex(0);				
			$cell1=$objPHPExcel->getActiveSheet()->getCell('A2')->getValue();				
			$cell2=$objPHPExcel->getActiveSheet()->getCell('B2')->getValue();				
			$cell3=$objPHPExcel->getActiveSheet()->getCell('C2')->getValue();				
			$cell4=$objPHPExcel->getActiveSheet()->getCell('D2')->getValue();				
			$cell5=$objPHPExcel->getActiveSheet()->getCell('E2')->getValue();				
			$cell6=$objPHPExcel->getActiveSheet()->getCell('F2')->getValue();			
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3=='Bank' && $cell4 =='Amount' && $cell5 =='Charge'
				&& $cell6 =='Remarks') {
				$writeflag ='1'; 
			}

			if($writeflag == '1'){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "銀行")
					->setCellValue('D2', "単価")
					->setCellValue('E2', "料金")
					->setCellValue('F2', "備考");
				}
				$yr = $request->selYear;
		       	$mnth = $request->selMonth;
		       	$mnsub = $request->subject;
		       	$sql = Transfer::transfer_subhistorydetails($request,$mnsub,$yr,$mnth);
		       	$disp = 0;
		       	$get_det = array();
		       	$row = array();
				$disp = count($sql);
				$i = 0;
				$totval = 0;
				$i=0;
				$chargeTotal=0;
				$amountTotal=0;
				$forgrantTotal = Transfer::transfer_subhistorydetails($request,$mnsub,$yr,$mnth);
				foreach ($forgrantTotal as $key => $value) {
					if ($value->BankName == "") {
						$value->BankName = "Cash";
					}
		        	if($value->BankName != "") {
						$amountTotal = $amountTotal+$value->amount;
					} 
					if ($value->fee != "") {
						$chargeTotal = $chargeTotal+$value->fee;
					}
				}
				foreach ($sql as $key => $value1) {
					$get_det[$i]['id'] = $value1->id;
					$get_det[$i]['year'] = $value1->year;
					$get_det[$i]['month'] = $value1->month;
					$get_det[$i]['amount'] = $value1->amount;
					$get_det[$i]['date'] = $value1->bankdate;
					$get_det[$i]['Subject'] = $value1->Subject;
					$get_det[$i]['Subject_jp'] = $value1->Subject_jp;
					$get_det[$i]['sub_eng'] = $value1->sub_eng;
					$get_det[$i]['sub_jap'] = $value1->sub_jap;
					$get_det[$i]['remarks'] = $value1->remarks;
					if(isset($value1->mainid)) {
						$get_det[$i]['mainid'] = $value1->mainid;
					}
					$get_det[$i]['file_dtl']= $value1->file_dtl;
					$get_det[$i]['fee'] = $value1->fee;
					$get_det[$i]['bankname'] = $value1->BankName;
					$get_det[$i]['bankaccno'] = $value1->bankaccno;
					if (empty($get_det[$i]['bankname'])) {
						$get_det[$i]['bankname']="Cash";
					}
					 $sql = Transfer::nickname($get_det[$i]['bankaccno'],$value1->bankid);
					 foreach ($sql as $key => $value2) {
					 	$get_det[$i]['Bank_NickName']=$value2->Bank_NickName;
					 }
					$i++;
				}
				if (count($get_det)=="") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', ''."->". '');
				}else{
					if (Session::get('languageval') == "en") { 
						$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['Subject']." -> ".$get_det[0]['sub_eng']);
					} else { 
						$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['Subject_jp']." -> ".$get_det[0]['sub_jap']);
					} 
				}
				$temp_i = 3;
				$rowbktrclrr=0;
				$tempdate=0;
				$tmpyr=0;
				$sno=0;
				$temp=0;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				for ($j = 0; $j <count($get_det); $j++) {
					if ($tempdate !=$get_det[$j]['id']) {
						if($rowbktrclrr==1){
							$style='dff1f4ff';
							$rowbktrclrr=0;
						} else {
							$style='FFFFFFFF';
							$rowbktrclrr=1;
						}
					} 
					if ($j == 0 ) { 
						$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('D3', "¥ ".number_format($amountTotal))
						 ->setCellValue('E3', "¥ ".number_format($chargeTotal));
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFill()->getStartColor()->setRGB($headerbgcolor);
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;$result1=0;$fee=0;
						$temp_i++;
		       			$view = Transfer::transfer_subhistorydetails($request,$mnsub,$get_det[$j]['year'],$get_det[$j]['month']);
		       			foreach ($view as $key => $value3) {
		       				$results=$value3->amount;
							$result1=$value3->fee;
							$amt=$amt+$results;
							$fee=$fee+$result1;
		       			}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':C'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('D'.$temp_i, number_format($amt))
						 ->setCellValue('E'.$temp_i, number_format($fee));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i.':E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':F'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':F'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':F'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
						$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
						$tmpyr=0;	
					}	
					$tmpyr=$get_det[$j]['year'];$tmpmth=$get_det[$j]['month'];
					$temp_i++;
					$objPHPExcel->setActiveSheetIndex(0);
					$sno++;
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()
				 	 ->setCellValue('A'.$temp_i, $sno);
					if ($tempdate !=$get_det[$j]['date']) {
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
				     	if ($get_det[$j]['bankname']=="Cash") {
				     		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, 'Cash');
				     	} else {
				     		if(isset($get_det[$j]['Bank_NickName'])) {
					     		$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno']);
				     		}
				     	}
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, number_format($get_det[$j]['amount']));
					if ($get_det[$j]['bankname']=="Cash") {
			     		$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i, "");
			     	} else {
			     		$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i, number_format($get_det[$j]['fee']));
			     	}
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$temp_i, $get_det[$j]['remarks']);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":F".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":F".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:F'.$temp_i)->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
					$temp = $get_det[$j]['bankname'];
					$tempdate = $get_det[$j]['date'];
					$i++;
					
				}
				$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
				$objPHPExcel->getActiveSheet()->setTitle($request->selYear."-".$request->selMonth);
			}
			$objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');
			// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			$objPHPExcel->getActiveSheet()->setTitle($request->selYear."-".$request->selMonth);
			$flpath='.xls';
			header('Content-Type: application/vnd.ms-excel');
	    	header('Content-Disposition: attachment;filename="'.$flpath.'"');
	    	header('Cache-Control: max-age=0');
		})->setFilename($excel_name. "_" . date("Ymd"))->download('xls');
	}
	public static function others(Request $request){
		if (!isset($request->id)) {
			return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankedit = "";
		$query = Transfer::editothersquery($request,$request->editid);
		$bankname = Transfer::fetchbankname($request);
		$bankedit = $query[0]->bankname."-".$query[0]->bankaccno;
		return view('Transfer.editothers',['query' =>$query,
											'bankname'=>$bankname,
											'bankedit'=>$bankedit,
											'request' => $request]);
	}	
	public static  function editothersprocess(Request $request){
		if($request->editflg=='edit') {
			$update=Transfer::othersupdate($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
				Session::flash('viewid', $request->editid);
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else{
			$other=Transfer::fngetothers($request);
			if($other) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
				Session::flash('viewid', $request->editid);
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} 
			$spldm = explode('-', $request->txt_startdate);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);  
		return Redirect::to('Transfer/index?mainmenu=company_transfer&time='.date('YmdHis'));

	}
}