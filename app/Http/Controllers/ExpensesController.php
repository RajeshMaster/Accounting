<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Expenses;
use App\Model\Transfer;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use Excel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;

class ExpensesController extends Controller {
	function index(Request $request) {
		if(Session::get('selYear') !="") {
			$request->selYear =  Session::get('selYear');
			$request->selMonth =  Session::get('selMonth');
			// $request->date =  Session::get('date');
			// $request->amount =  Session::get('amount');
		}
		$disabl="";
		$balan1 = 0;
		$db_year_month = array();
		$yearMnarray = array();
		$g_query1 =array();
		$g_query = array();
		$year_monthslt = "";
		$get_det = array();
		$exp_rsTotalAmount = 0;
		$exp_rsTotalAmount1 = 0;
		$k = 0;
		$i = 0;
		$rowclr=0;
		$pettyCash="Petty Cash";
		$ExpensesDetails="Expenses";
		$totalYenColor="";
		$gett = "";
		$rsTotalAmount = 0;
		$rsTotalAmount1 = 0;
		$tempvar="";
		$PAGING=0;
		$incr=0;
		$serialcolor="";
		$last="";
		$totalexptra = "";
		$temp = "";
		$last1="";
		$future_date="";
		$selectedmonth = "";  
	   	$selectedYear = ""; 
	   	$selectedmonyear = "";
		$filepath="";
		$dwn_type="";
		$transcount="";
		$updated_date="";
		$db_updated_date="";
		$registered_date="";
		$db_inserted_date="";
		$bank_det_bname = "";
		$bank_det_bnickname="";
		$today_date = date('Y-m-d');
		// After Submit button is pressed, the action comes here
		if ($request->submitflg == 1) {
			$month = str_pad($request->selMonth, 2, 0, STR_PAD_LEFT);
			$year = $request->selYear;
			$expensesubmit = Expenses::fnsubmitexpense($month,$year);
			$sql1 = Expenses::submitflgupdatetransfer($month,$year);
			$sql2 = Expenses::submitflgupdatepettycash($month,$year);
		} elseif ($request->submitflg == 2) {
			$month = str_pad($request->selMonth, 2, 0, STR_PAD_LEFT);
			$year = $request->selYear;
			$expensesubmit = Expenses::fnrevertexpense($month,$year);
			$sql1 = Expenses::fnreverttransfer($month,$year);
			$sql2 = Expenses::fnrevertpetty($month,$year);
		}
		if($request->mainmenu == "expenses") {
			// FOR CARRIED FORWARD PROCESS
			$expall_query = Expenses::fnGetExpenseAllRecord();
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
				$insertQuery = Expenses::fnInsertPreBalance($yearMnarray, $db_year_monthfullarray,$request);
			}
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
		if (!isset($request->selMonth)) {
			$date_month=date('Y-m');
		} else {
			$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
		}
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
		$est_query=Expenses::fnGetExpenseRecord($request,$from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$dbrecord[]=$value->date;
		}
		$est_query1=Expenses::fnGetExpenseRecordPrevious($request,$from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		$pre = 0;
		foreach ($est_query1 as $key => $value) {
			$dbpreviousYr[]=substr($value->date, 0, 4);
			$dbprevious[]=$value->date;
			$pre++;
		}
		$est_query2=Expenses::fnGetExpenseRecordNext($request,$to_date);
		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->date;
		}
		$dbrecord = array_unique($dbrecord);
		//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH
		$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
		if($request->mainmenu == "pettycash") {
			$g_query = Expenses::pettycash_expenses($lastyear,$lastmonth,$request);
			$g_query1 = Expenses::pettycash_expenses1($lastyear,$lastmonth,$request);
		} else if($request->mainmenu == "expenses") {
			$g_query = Expenses::main_expenses($lastyear,$lastmonth,$request);
			$g_query1 = Expenses::main_expenses1($lastyear,$lastmonth,$request);
		}
		if(empty($g_query1)){
					$disabl="disabled";
				}
		$amount_val = array();
		$q = 0;
		foreach ($g_query1 as $key => $value0) {
			$amount_val[$q]['amount']= $value0->amount;
			if ($value0->amount != "" && $value0->del_flg == "2") {
				if ($value0->amount <0 && $value0->del_flg == "2") {
					$amount_val[$q]['totalamount']=$rsTotalAmount1 += $value0->amount;
				} elseif ($value0->amount ==0 && $value0->del_flg == "2") {
					$amount_val[$q]['totalamount']=$rsTotalAmount1 += $value0->amount;
				} elseif ($value0->amount >0 && $value0->del_flg == "2") {
					$rsTotalAmount1 += $value0->amount;
				}
			} else {
				$amount_val[$q]['cash']="&nbsp";
			}
			if ($value0->amount != "" && $value0->del_flg == "1") {
				if ($value0->amount <0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $amount_val[$q]['amount'];
				} elseif ($value0->amount ==0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $amount_val[$q]['amount'];
				} elseif ($value0->amount >0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $value0->amount;
				}
			} else if($value0->currency_type == "1") {
				$exp_rsTotalAmount1 += $amount_val[$i]['jp_amount'];
			} else {
				$amount_val[$q]['expenses']="&nbsp";
			}
		// expenses calculation
				if($value0->amount>0 && isset($value0->salaryFlg) && $value0->salaryFlg == 1) {
					$amount_val[$q]['expenses']=number_format($value0->amount)."</font>";
					$exp_rsTotalAmount1 += $value0->amount;
				}
				$balan1 = $rsTotalAmount1-$exp_rsTotalAmount1;
		$q++;
		}
		foreach ($g_query as $key => $value) {
			$get_det[$k]['amount']= $value->amount;
			if ($value->amount != "" && $value->del_flg == "2") {
				if ($value->amount <0 && $value->del_flg == "2") {
					$get_det[$k]['totalamount']=$rsTotalAmount += $value->amount;
				} elseif ($value->amount ==0 && $value->del_flg == "2") {
					$get_det[$k]['totalamount']=$rsTotalAmount += $value->amount;
				} elseif ($value->amount >0 && $value->del_flg == "2") {
					$rsTotalAmount += $value->amount;
				}
			} else {
				$get_det[$k]['cash']="&nbsp";
			}
			if ($value->amount != "" && $value->del_flg == "1") {
				if ($value->amount <0 && $value->del_flg == "1") {
					$exp_rsTotalAmount += $get_det[$k]['amount'];
				} elseif ($value->amount ==0 && $value->del_flg == "1") {
					$exp_rsTotalAmount += $get_det[$k]['amount'];
				} elseif ($value->amount >0 && $value->del_flg == "1") {
					$exp_rsTotalAmount += $value->amount;
				}
			} else if($value->currency_type == "1") {
				$exp_rsTotalAmount += $get_det[$i]['jp_amount'];
			} else {
				$get_det[$k]['expenses']="&nbsp";
			}
			$get_det[$k]['id'] = $value->id;
			$get_det[$k]['FirstNames'] = $value->FirstNames;
			$get_det[$k]['LastNames'] = $value->LastNames;
			$get_det[$k]['date'] = $value->date;
			if ($request->mainmenu == "pettycash") {
				$get_det[$k]['billno'] = $value->billno;
				$get_det[$k]['subject'] = $value->main_subject;
				$get_det[$k]['details'] = $value->sub_subject;
				$get_det[$k]['check_number'] = $value->check_number;
				$get_det[$k]['amount'] = $value->amount;
				// $get_det[$k]['jp_amount'] = $value->jp_amount;
				$get_det[$k]['banknamevalue'] = $value->bankname;
				$get_det[$k]['month'] = $value->bankname;
			} else {
				$get_det[$k]['subject'] =$value->subject;
				$get_det[$k]['details']= $value->details;
		    	$get_det[$k]['carryForwardFlg']= (isset($value->carryForwardFlg) ? $value->carryForwardFlg : '');
		    	$get_det[$k]['transfer_flg']= $value->transfer_flg;
		    	$get_det[$k]['pettyFlg']= $value->pettyFlg;
		    	$get_det[$k]['salaryFlg']= $value->salaryFlg;
			    $get_det[$k]['empNo']= $value->empNo;
			    $get_det[$k]['carryForward']= "";
				$get_det[$k]['bankId']= $value->bankId;
				if(isset($value->bankaccno)) {
					$get_det[$k]['bankaccno']= $value->bankaccno;
				}
			    $get_det[$k]['banknameTransfer']= $value->banknameTransfer;
			    $get_det[$k]['bankaccnoTransfer']= $value->bankaccnoTransfer;
			    $get_det[$k]['EmpName'] = ucwords(strtolower($value->LastName)). ".".
						ucwords(mb_substr($value->FirstName, 0, 1,'utf-8'));
			}
			$get_det[$k]['currency_type']= $value->currency_type;
			// $get_det[$k]['jp_amount'] = (isset($value->jp_amount) ? $value->jp_amount : '');
			$get_det[$k]['remark_dtl'] =$value->remark_dtl;
			$get_det[$k]['file_dtl']= $value->file_dtl;
			$get_det[$k]['del_flg']= $value->del_flg;
			$get_det[$k]['transaction_flg']=$value->transaction_flg;
			$get_det[$k]['year']= $value->year;
			$get_det[$k]['month']= $value->month;
			$get_det[$k]['submit_flg']= $value->submit_flg;
		    $get_det[$k]['copy_month_flg'] = $value->copy_month_day;
			$get_det[$k]['insert_date']= (isset($value->Ins_DT) ? $value->Ins_DT : '');
			$get_det[$k]['update_date']= (isset($value->Up_DT) ? $value->Up_DT : '');
		    $get_det[$k]['edit_flg']= $value->edit_flg;
		
					
		    if($request->mainmenu == "expenses") {
				// amount calculation
				if($value->carryForwardFlg == '1') {
					$gett += $value->amount;
			   	}
			   	// Subject definition
			   	if($value->subject == 'LastMonthTotal') {
					$get_det[$k]['bank']= 'LastMonthTotal';	

				} else if($value->subject == 'Last Month Balance') {
					$get_det[$k]['bank']= 'Last Month Balance';

				}else if($value->subject== 'Cash') {
					$get_det[$k]['bank']= $value->subject;


				}else {
					$getsuj = Expenses::selsubname($get_det[$k]['subject']);
					$get_det[$k]['bank']=$getsuj[0];

				}
			} else if($request->mainmenu == "pettycash") {
				// Subject definition
			   	if($value->main_subject == 'LastMonthTotal') {
					$get_det[$k]['bank']= 'LastMonthTotal';										
				} else if($value->main_subject == 'Last Month Balance') {
					$get_det[$k]['bank']= 'Last Month Balance';
				}else if($value->main_subject== 'Cash') {
					$get_det[$k]['bank']= $value->main_subject;
				}
				
				else {
					if($request->mainmenu == "pettycash") {
						$getsuj = Expenses::selsubnameforpettycash($get_det[$k]['subject']);
					} else {
						$getsuj = Expenses::selsubname($get_det[$k]['subject']);
					}
					$get_det[$k]['bank']=$getsuj[0];
				}
			}
				// for date
				if($get_det[$k]['date']!= "") { 
							$get_det[$k]['loc']  = $get_det[$k]['date'];
				}
		// Future use
				if( $value->date!= ""){
					$get_det[$k]['datedetail']=  $value->date;
				} 
				if($request->mainmenu == "pettycash") {
					if($get_det[$k]['loc'] != $temp){
						if($value->main_subject == 'Last Month Balance') {
							$get_det[$k]['a']= date('Y-m-d');
						} else if( $value->date!= ""){
							$get_det[$k]['datedetail']=  $value->date;
						} 
					}
				}
				if($request->mainmenu == "pettycash") {
					if($value->main_subject == 'Last Month Balance') {
						$get_det[$k]['detail']= 'Last Month Balance';
					} else {
						//$get_det[$k]['detail']= stripslashes($row['details']);
						$getsub_suj = Expenses::selsubsubjectnameforpettycash($value->sub_subject,$value->main_subject);
						$get_det[$k]['detail']=$getsub_suj[0];

					}
				} else {
					if($value->subject == 'Last Month Balance') {
						$get_det[$k]['detail']= 'Last Month Balance';

					} else {
						//$get_det[$k]['detail']= stripslashes($row['details']);
						$getsub_suj = Expenses::selsubsubjectname($value->details,$value->subject);
						$get_det[$k]['detail']=$getsub_suj[0];
					}

				}
		// cash amount calculation
				if($value->amount!= "" && $value->del_flg== "2") {
					if($value->amount<0 && $value->del_flg == "2"){
						$fontColor = "<font color='red'>";
					    $get_det[$k]['cash']= $fontColor.number_format($value->amount)."</font>";
					} else if($value->amount==0 && $value->del_flg == "2") {
						$fontColor = "<font color='black'>";
						$get_det[$k]['cash']= $fontColor.number_format($value->amount)."</font>";							
					} else if($value->amount>0 && $value->del_flg== "2") {
						$fontColor = "<font color='black'>";
						$get_det[$k]['cash']= $fontColor.number_format($value->amount)."</font>";						
					}
				} else {
					$get_det[$k]['cash']= "&nbsp;";
				}
		// expenses calculation
				if($value->amount>0 && isset($value->salaryFlg) && $value->salaryFlg == 1) {
					$get_det[$k]['expenses']=number_format($value->amount)."</font>";
					$exp_rsTotalAmount += $value->amount;
				} else if($value->amount!= "" && $value->del_flg == "1") {
					if($value->amount<0 &&$value->del_flg == "1"){
						$fontColor = "<font color='red'>";
						$get_det[$k]['expenses']=$fontColor.number_format($value->amount)."</font>";
					} else if($value->amount==0 && $value->del_flg == "1") {
						// $fontColor = "<font color='black'>";
						$get_det[$k]['expenses']=number_format($value->amount);
					} else if($value->amount>0 && $value->del_flg == "1") {
						// $fontColor = "<font color='black'>";
						$get_det[$k]['expenses']=number_format($value->amount);
					}
				} else if($value->currency_type== "1") {
					// $fontColor = "<font color='black'>";
					$get_det[$k]['expenses']=number_format($get_det[$i]['jp_amount']);
				} else {
					$get_det[$k]['expenses']="&nbsp;";
				}
		//Remarks 
				if($value->remark_dtl != "") {
					$get_det[$k]['remark']=nl2br($value->remark_dtl);
				} 
		//Bank name
				$get_det[$k]['bankname']=$value->bankname;
				$get_det[$k]['bankaccno']=$value->bankaccno;
		//Account number
				$sql1 = Expenses::regGetBankDetails($get_det[$k]['bankname'],$get_det[$k]['bankaccno']);
					foreach ($sql1 as $key => $value) {
						$bank_det_bname = $value->BankName;
						$bank_det_bnickname = $value->Bank_NickName;
					}
				$get_det[$k]['bankname']=$bank_det_bname;
				$get_det[$k]['banknickname'] = $bank_det_bnickname;

			if($request->mainmenu == "expenses") {
				$transcount="";
					if (isset($value->transaction_flg)) {
				   		if($value->transaction_flg == 3){
								$transcount += 1;
						}
					}
					if (isset($value->carryForwardFlg)) {
						if($value->carryForwardFlg == 1){
							$transcount += 1;
						}
					}
					if (isset($value->salaryFlg)) {
						if($value->salaryFlg == 1){
							$transcount += 1;
						}
					}
				$soluexpanse = Expenses::getsoluexpansedetails($get_det[$k]['year'],$get_det[$k]['month']);
				$solutransfer = Expenses::getsolutransferdetails($get_det[$k]['year'],$get_det[$k]['month']);
				$totalexptra =  $soluexpanse[0]->SUM + $solutransfer[0]->result;
				//Present,Past,Futeure date identify process
			}
				$get_det[$k]['copy'] =""; 
			   $orderdateval = explode('-', $get_det[$k]['date']);

			   $yearval = $orderdateval[0];
			   
			   $monthval  = $orderdateval[1];

			   $dateval  = $orderdateval[2];

			   $monyear =  $yearval . '-' . $monthval;

			//Present,Past,Futeure date identify process
			   
			   $monthvalcheck  = substr("0" .  $monthval, -2);
			   
			    if($request->mainmenu == "expenses") {
	       			$current_year_month = array();
			        $current_year_month[0] = date('Y-m');
			        $current_year_month[1] = date ('Y-m', strtotime ( '+1 month' , strtotime ( $current_year_month[0]."-01" )));
			        $current_year_month[2]= date ('Y-m', strtotime ( '-1 month' , strtotime ( $current_year_month[0]."-01" )));
					for($j=0; $j<count($current_year_month);$j++) {
						if($current_year_month[$j] == $monyear){
							$get_det[$k]['copy'] ="1";
						}
					}
			        //Present,Past,Futeure date identify process	
					if( $get_det[$k]['pettyFlg'] == 1 ) {
						if(Session::get('languageval') == "jp") {
				    		$get_det[$k]['bank'] = "小口現金";
			    			$get_det[$k]['detail'] =  "経費明細";
						} else {
				    		$get_det[$k]['bank'] = $pettyCash;
			    			$get_det[$k]['detail'] =  $ExpensesDetails;
						}
			    	}
		    	}
		    	if($request->mainmenu == "pettycash") {
					$totalexptra += $get_det[$k]['expenses'];
				}
		$k++;
		}
		$balan = $rsTotalAmount-$exp_rsTotalAmount;
			$kessanki = Expenses::getkessanki();
			$curdate = date('Y-m');
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
							$split_val = explode("-",$dbrecordvalue);
							$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
			}
			// Bill display
			$userfile_extn=array();
			$dwn_type="";
			$filepath="../webroot/img/Expenses/";
		// if (isset($get_det[$i]['file_dtl'])) {
		// $userfile_extn = explode(".", $get_det[$i]['file_dtl']);
		// if($userfile_extn[1]=="xls"){
		// 	$dwn_type="_self";
		// }else{ 
		// 	$dwn_type="_blank";
		// }
		// }
		// For Tree view process
		$curdate=date('Y-m-d');
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
		if($request->mainmenu == "pettycash") {
			$sqlMainCat = Expenses::getMainCategoriespettycash();
			if(Session::get('languageval') == "jp") {
				$selectedField = "main_jap";
				$selectedFieldSub = "sub_jap";
			} else {
				$selectedField = "main_eng";
				$selectedFieldSub = "sub_eng";
			}
		} else {
			$sqlMainCat = Expenses::getMainCategories();
			if(Session::get('languageval') == "jp") {
				$selectedField = "Subject_jp";
				$selectedFieldSub = "sub_jap";
			} else {
				$selectedField = "Subject";
				$selectedFieldSub = "sub_eng";
			}
		}
				
		$subCatDetails=array();
		$mainCatDetails=array();
		$mn=0;
		if($request->mainmenu == "pettycash") {
			foreach ($sqlMainCat as $key => $value) {
				if(Session::get('languageval') == "jp") {
					if (isset($value->main_jap)) {
						$value->Subject_jp=$value->main_jap;
					} else {
						$value->Subject_jp="";
					}
					$mainCatDetails[$mn]['mainCat']=$value->Subject_jp;
				} else {
					if (isset($value->main_eng)) {
						$value->Subject=$value->main_eng;
					} else {
						$value->Subject="";
					}
					$mainCatDetails[$mn]['mainCat']=$value->Subject;
				}
				$mainCatDetails[$mn]['id'] = $value->id;
				if($request->mainmenu == "pettycash") {
					$sqlSubCat = Expenses::getSubCategoriespettycash($value->id);
				} else {
					$sqlSubCat = Expenses::getSubCategories($value->id);
				}
						$sb=0;
							foreach ($sqlSubCat as $key => $displayval) {
							if(Session::get('languageval') == "jp") {
								if (isset($displayval->main_jap)) {
									$displayval->Subject_jp=$displayval->main_jap;
								} else {
									$displayval->Subject_jp="";
								}
							$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $displayval->sub_jap;
							} else {
								if (isset($displayval->main_eng)) {
									$displayval->Subject=$displayval->main_eng;
								} else {
									$displayval->Subject="";
								}
							$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $displayval->sub_eng;
							}
								$subCatDetails[$mainCatDetails[$mn]['mainCat']] [$sb]['subId'] = $displayval->id;
								$sb++;
							}
				$mn++;
			}
			} else {
				foreach ($sqlMainCat as $key => $value) {
				if(Session::get('languageval') == "jp") {
					if (isset($value->Subject_jp)) {
						$value->Subject_jp=$value->Subject_jp;
					} else {
						$value->Subject_jp="";
					}
					$mainCatDetails[$mn]['mainCat']=$value->Subject_jp;
				} else {
					if (isset($value->Subject)) {
						$value->Subject=$value->Subject;
					} else {
						$value->Subject="";
					}
					$mainCatDetails[$mn]['mainCat']=$value->Subject;
				}
				$mainCatDetails[$mn]['id'] = $value->id;
					$sqlSubCat = Expenses::getSubCategories($value->id);
						$sb=0;
							foreach ($sqlSubCat as $key => $displayval) {
							if(Session::get('languageval') == "jp") {
								if (isset($displayval->Subject_jp)) {
									$displayval->Subject_jp=$displayval->Subject_jp;
								} else {
									$displayval->Subject_jp="";
								}

							$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $displayval->sub_jap;
							} else {
								if (isset($displayval->Subject)) {
									$displayval->Subject=$displayval->Subject;
								} else {
									$displayval->Subject="";
								}
							$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] = $displayval->sub_eng;
							}
								$subCatDetails[$mainCatDetails[$mn]['mainCat']] [$sb]['subId'] = $displayval->id;
								$sb++;
							}
				$mn++;
			}
			}
			 					   $previousmonth=date('m', strtotime('-1 months'));
								   $futuremonth=date('m', strtotime('1 months'));
								   $currentmonth=date('m', strtotime('0 months'));
								   $previousyear=date('Y', strtotime('-1 year'));
								   $futureyear=date('Y', strtotime('1 year'));
								   $currentyear=date('Y', strtotime('0 year'));
								    // next month count
								    if(isset($get_det[0]['date'])) {
									   $orderdate = explode('-',$get_det[0]['date']);
									   $selectedmonth = $orderdate[1];  
									   $selectedYear = $orderdate[0]; 
									   $selectedmonyear = $selectedmonth."".$selectedYear;
									}
		if ($request->mainmenu == "expenses") {
			$chechsql = Expenses::balance_sal($lastyear,$lastmonth,$request);
		} else {
			$chechsql = "0";
		}
		$actionName = $request->actionName;
		if ($actionName == "expensesexceldownload") {
			$selectedYearMonth = explode("-", $request->selYearMonth);
			$curTime = date('Y/m/d  H:i:s');
			$template_name = 'resources/assets/uploadandtemplates/templates/Download_expenses.xls';
			$tempname = "Expenses_detail";
			$excel_name=$tempname;

		Excel::load($template_name, function($objPHPExcel) use($request,$selectedYearMonth,$get_det) {

		// Read the file
				$loc = "";
				$temp = "";
				$temp_i=4;
				$rsTotalAmount = 0;
				$rsTotalAmount1 = 0;
				$exp_rsTotalAmount = 0;
				$exp_rsTotalAmount1 = 0;
			  	$getbktr_det = array();
			  	$sumtotalval = 0;
			  	$sumWithdrawTotalval = 0;
			  	$sumDepositTotalval = 0;
			  	$sumTotalval = 0;
			  	$getsuj = "";
			  	$destinationPath = '../InvoiceUpload/Expenses/';
			  	$transdate="";
			  	$transferdate="";
			  	$color='C4D79B';
				$k = 0;
				$id=1; 
				$pettyCash="Petty Cash";
				$ExpensesDetails="Expenses";
				$selectedYearMonth = explode("-", $request->selYearMonth);
				$year = $selectedYearMonth[0];
				$month = $selectedYearMonth[1];
				$rowclr="";
				$g_query = Expenses::main_expenses($year,$month,$request);

				$writeflag =0;
				$styleArray = array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				);
				$styleArray1 = array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)	
				);
				$fontStyle = [
	    						'font' => [
	      							  'size' => 12,
	      							  'color' => ['argb' => '#000000'],
	      							  'bold' => true
										  ]
							];
				$initial_value = 4;
				$g_query1 = Expenses::main_expenses1($year,$month,$request);
				$g_query = Expenses::main_expenses($year,$month,$request);
				$amount_val = array();
				$q = 0;
		foreach ($g_query1 as $key => $value0) {
			$amount_val[$q]['amount']= $value0->amount;
			if ($value0->amount != "" && $value0->del_flg == "2") {
				if ($value0->amount <0 && $value0->del_flg == "2") {
					$amount_val[$q]['totalamount']=$rsTotalAmount1 += $value0->amount;
				} elseif ($value0->amount ==0 && $value0->del_flg == "2") {
					$amount_val[$q]['totalamount']=$rsTotalAmount1 += $value0->amount;
				} elseif ($value0->amount >0 && $value0->del_flg == "2") {
					$rsTotalAmount1 += $value0->amount;
				}
			} else {
				$amount_val[$q]['cash']="&nbsp";
			}
			if ($value0->amount != "" && $value0->del_flg == "1") {
				if ($value0->amount <0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $amount_val[$q]['amount'];
				} elseif ($value0->amount ==0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $amount_val[$q]['amount'];
				} elseif ($value0->amount >0 && $value0->del_flg == "1") {
					$exp_rsTotalAmount1 += $value0->amount;
				}
			} else if($value0->currency_type == "1") {
				$exp_rsTotalAmount1 += $amount_val[$i]['jp_amount'];
			} else {
				$amount_val[$q]['expenses']="&nbsp";
			}
			$get_det[$k]['pettyFlg'] =$value0->pettyFlg;
		// expenses calculation
				if($value0->amount>0 && isset($value0->salaryFlg) && $value0->salaryFlg == 1) {
					$amount_val[$q]['expenses']=number_format($value0->amount)."</font>";
					$exp_rsTotalAmount1 += $value0->amount;
				}
				$balan1 = $rsTotalAmount1-$exp_rsTotalAmount1;
			$q++;
		}
		$db_updated_date="";
		foreach ($g_query as $key => $value) {
				$temp_i = $k+$initial_value;
				$get_det[$k]['date'] =$value->date;
				$loc = $get_det[$k]['date'];
					if($loc != $temp){
			   			$transferdate=$get_det[$k]['date']; 
			   			$transdate=explode("-" , $transferdate); 
		 				$temp_val = $k;

							if($transdate[2] < 10){
								$transdate=$transferdate[9]."日"; 
							}
							else{
				 				$transdate= $transferdate[8].$transferdate[9]."日";
							}
					
					if($rowclr==1){
						$style='dff1f4ff';
						$rowclr=0;
					} else {
						$style='FFFFFF';
						$rowclr=1;
					}
					}
					$get_det[$k]['subject'] =$value->subject;
					$getsuj = Expenses::selsubname($get_det[$k]['subject']);
					$getbktr_det[$k]['details'] = $value->details;
		 		 	$getsub_suj = Expenses::selsubsubjectname($value->details,$value->subject);
					if($value->pettyFlg == 1) {
				    		$get_det[$k]['bank']=$pettyCash;
			    			$get_det[$k]['detail']=$ExpensesDetails;
			    	}
			    	else{
					$get_det[$k]['bank']=$getsuj[0];
					$get_det[$k]['detail']=$getsub_suj[0];
					}
					$getbktr_det[$k]['amount']=$value->amount;
					$get_det[$k]['FirstNames'] = $value->FirstNames;
					$get_det[$k]['LastNames'] = $value->LastNames;
					if(isset($value->LastNames) && isset($value->FirstNames)){
					$get_det[$k]['EmpName'] = ucwords(strtoupper($value->LastNames)). '.'.
						ucwords(mb_substr($value->FirstNames, 0, 1));
					}
					else{
						$get_det[$k]['EmpName']="";
						}	
					$getbktr_det[$k]['remark_dtl']=$value->remark_dtl;

			 		$objPHPExcel->getActiveSheet()->setCellValue('F1', $year."年".$month."月分");
			 		$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(19);

			 		$objPHPExcel->getActiveSheet()
			 		->setCellValue('A3', (trans('messages.lbl_Date')))
				  	->setCellValue('B3', (trans('messages.lbl_mainsubject')))
				  	->setCellValue('C3', (trans('messages.lbl_subsubject')))
				  	->setCellValue('D3', (trans('messages.lbl_empName')))
				  	->setCellValue('E3', (trans('messages.lbl_expenses')))
				  	->setCellValue('F3', (trans('messages.lbl_remarks')));
				  	$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				  	$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			 		$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":F".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($style);
			 		

		 			$objPHPExcel->getActiveSheet()
		 			->setCellValue('A'.$temp_i, ltrim($transdate, '0'))
		 			->setCellValue('B'.$temp_i, $get_det[$k]['bank'])
		 			->setCellValue('C'.$temp_i, $get_det[$k]['detail'])
		 			->setCellValue('D'.$temp_i, $get_det[$k]['EmpName'])
		 			->setCellValue('E'.$temp_i, number_format($getbktr_det[$k]['amount']))
		 			->setCellValue('F'.$temp_i, $getbktr_det[$k]['remark_dtl']);
		 			$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		 			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		 			$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':'.'F'.$temp_i)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		 			$mercell_val1=$temp_val+$initial_value;
					$mercell_val2=abs($temp_val-$k)+$mercell_val1;
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$mercell_val1.':A'.$mercell_val2);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$mercell_val1)->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$temp = $loc;
		 			$k++;
					$id++;
					$temp_i++;
		 	}
			if ($exp_rsTotalAmount1 < 0) {
				$totalRsColor = "#FF0000";
			} else {
				$totalRsColor = "#0000FFff";
			}
			$temp_i=$temp_i+1;
			$objPHPExcel->getActiveSheet()
	 		->setCellValue('D'.$temp_i,"Total Amount");
	 		$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->applyFromArray($fontStyle);
	 		$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($fontStyle);
			$objPHPExcel->getActiveSheet()
	 		->setCellValue('E'.$temp_i, "¥ ".number_format($exp_rsTotalAmount1));
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->getColor()->setARGB($totalRsColor);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->applyFromArray($styleArray1);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()
				 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		  	// $objPHPExcel->setActiveSheetIndex();
		 	$objPHPExcel->setActiveSheetIndex()
		    	->mergeCells('A1:E1');
			$objPHPExcel->getActiveSheet()
		    	->getCell('A1')
		    	->setValue('株式会社Microbit　　資金明細');
	 		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($fontStyle);
		 	$objPHPExcel->getActiveSheet()
		    	->getStyle('A1')
		    	->getAlignment()
   			 	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->setTitle($year."-".$month);
			$flpath='.xls';

			header('Content-Type: application/vnd.ms-excel');
		    header('Content-Disposition: attachment;filename="'.$flpath.'"');
		    header('Cache-Control: max-age=0');
			})->setFilename($excel_name)->download('xls');

		}
		return view('Expenses.index',[
									'chechsql' => $chechsql,
									'account_val' => $account_val,
									'current_year' => $current_year,
									'current_month' => $current_month,
									'last_year' => $last_year,
									'previousmonth' => $previousmonth,
									'futuremonth' => $futuremonth,
									'currentmonth' => $currentmonth,
									'previousyear' => $previousyear,
									'currentyear' => $currentyear,
									'futureyear' => $futureyear,
									'selectedmonth' => $selectedmonth,
									'selectedYear' => $selectedYear,
									'dbprevious' => $dbprevious,
									'dbnext' => $dbnext,
									'year_month' => $year_month,
									'date_month' => $date_month,
									'year_monthslt' => $year_monthslt,
									'account_period' => $account_period,
									'db_year_month' => $db_year_month,
									'g_query' => $g_query,
									'get_det' => $get_det,
									'gett' => $gett,
									'balan' => $balan,
									'balan1' => $balan1,
									'curdate' => $curdate,
									'rsTotalAmount' => $rsTotalAmount,
									'rsTotalAmount1' => $rsTotalAmount1,
									'filepath' => $filepath,
									'dwn_type' => $dwn_type,
									'tempvar' => $tempvar,
									'rowclr' => $rowclr,
									'PAGING' => $PAGING,
									'totalexptra' => $totalexptra,
									'incr' => $incr,
									'serialcolor' => $serialcolor,
									'totalYenColor' => $totalYenColor,
									'transcount' => $transcount,
									'mainCatDetails' => $mainCatDetails,
									'subCatDetails' => $subCatDetails,
									'future_date' => $future_date,
									'updated_date' => $updated_date,
									'registered_date' => $registered_date,
									'db_inserted_date' => $db_inserted_date,
									'db_updated_date' => $db_updated_date,
									'today_date' => $today_date,
									'exp_rsTotalAmount1' => $exp_rsTotalAmount1,		
									'exp_rsTotalAmount' => $exp_rsTotalAmount,
									'disabl' => $disabl,		
									'request' => $request]);
	}
	function addedit(Request $request) {
		$getkessanki = Expenses::kessanki_ListView($request);
		$expbillno = Expenses::expbillno_ListView($request,$getkessanki[0]);
		$getsubject = Expenses::fnGetSubject($request);
		$edit_flg=2; 
		return view('Expenses.addedit',['request' => $request,
										'getsubject' => $getsubject,
										'edit_flg' => $edit_flg,
										'expbillno' => $expbillno]);
	}
	public static function ajaxsubsubject(Request $request) {
		$getsunsubject=Expenses::fnfetchsubsubject($request);
		$degreedata=json_encode($getsunsubject);
		echo $degreedata;
	}
	public static function addeditprocess(Request $request) {
		if($request->edit_flg == "2" || $request->edit_flg == "3") {
			$autoincId=Expenses::getautoincrement($request);
			$expno="Expenses_".date('YmdHis');
			$fileid="file1";
			$filename="";
            if($request->$fileid != "") {
              $extension = Input::file($fileid)->getClientOriginalExtension();
              $filename=$expno.'.'.$extension;
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
			$getkessanki = Expenses::kessanki_ListView($request);
			$expbillno = Expenses::expbillno_ListView($request,$getkessanki[0]);
        	$spldm = explode('-', $request->date);
        	if($request->mainmenu != "pettycash") {
	        	if ($request->edit_flg == "3") {
					$update = Expenses::addupdcash($request);
				}
			}
			$checkSubmitCount = Expenses::checkSubmited($spldm);
			$fnaddexpenses=Expenses::fnadddatatodatabase($request,$filename,$checkSubmitCount,$expbillno);
			Session::flash('date', $request->date); 
			Session::flash('amount', $request->amount); 
			if($request->mainmenu == "pettycash") {
				$disp = Expenses::checkexpensesadd($spldm);
				if($disp > 0) {
					$fnaddexpenses=Expenses::fnaddtodev($request,$filename,$checkSubmitCount,$expbillno,1);
				} else {
					$fnaddexpenses=Expenses::fnadduptodev($request,$filename,$checkSubmitCount,$expbillno,1);
				}
			}
				if($fnaddexpenses) {
					Session::flash('success', 'Inserted Sucessfully!'); 
					Session::flash('type', 'alert-success'); 
				} else {
					Session::flash('type', 'Inserted Unsucessfully!'); 
					Session::flash('type', 'alert-danger'); 
				}
		} else if($request->edit_flg == "1") {
			$fileid="file1";
			if($request->$fileid != "") {
				$extension = Input::file($fileid)->getClientOriginalExtension();
				$expno="Expenses_".date('YmdHis');
				$fileid="file1";
				$filename="";
				$filename=$expno.'.'.$extension;
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
			$fneditexpenses=Expenses::fneditdatatodatabase($request,$filename);
			Session::flash('date', $request->date); 
			Session::flash('amount', $request->amount); 
			$spldm = explode('-', $request->date);
			$checkSubmitCount = Expenses::checkSubmited($spldm);
			$getkessanki = Expenses::kessanki_ListView($request);
			$expbillno = Expenses::expbillno_ListView($request,$getkessanki[0]);
			if($request->mainmenu == "pettycash") {
				$disp = Expenses::checkexpensesadd($spldm);
				if($disp > 0) {
					$fnaddexpenses=Expenses::fnaddtodev($request,$filename,$checkSubmitCount,$expbillno,1);
				} else {
					$fnaddexpenses=Expenses::fnadduptodev($request,$filename,$checkSubmitCount,$expbillno,1);
				}
			}
			// if($fneditexpenses) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			/*} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}*/
		}
			$spldm = explode('-', $request->date);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]); 
		return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function edit(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$expcash_sql = Expenses::fnGetCashEdit($request);
		$getsubject = Expenses::fnGetSubject($request);
		$edit_flg=1; 
		$path = "resources/assets/images/Expenses";
		return view('Expenses.addedit',['request' => $request,
										'getsubject' => $getsubject,
										'edit_flg' => $edit_flg,
										'path' => $path,
										'expcash_sql' => $expcash_sql]);
	}
	public static function copy(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$expcash_sql = Expenses::fnGetCashEdit($request);
		$getsubject = Expenses::fnGetSubject($request);
		$edit_flg=3; 
		return view('Expenses.addedit',['request' => $request,
										'getsubject' => $getsubject,
										'edit_flg' => $edit_flg,
										'expcash_sql' => $expcash_sql]);
	}
	public static function multipleregister(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$multi_reg="";
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
		$query = Expenses::fnexpensemultireg($multi_reg,$month,$year);
		$i=0;
		foreach ($query as $key => $value) {
			$getreg_det[$i]['transaction'] = $value->transaction_flg;
			$getreg_det[$i]['Bank_No'] = $value->bankname;
			$getreg_det[$i]['Bank_NickName'] =$value->Bank_NickName;
			$getreg_det[$i]['bankaccno']= $value->bankaccno;
			$getreg_det[$i]['id']= $value->id;
			$getreg_det[$i]['subjectcode']= $value->subject;
			$getreg_det[$i]['details']= $value->details;
			$getreg_det[$i]['amount']= number_format($value->amount);
			if(Session::get('languageval') == "jp") {
				$getreg_det[$i]['subsubject'] = $value->sub_jap;
				$getreg_det[$i]['subject'] = $value->Subject_jp;
			} else {
				$getreg_det[$i]['subsubject'] = $value->sub_eng;
				$getreg_det[$i]['subject'] = $value->mainSubject;
			}
		$i++;
		}
		return view('Expenses.multipleregister',['request' => $request,
												 'getreg_det' => $getreg_det]);
	}
	public static function multipleregprocess(Request $request) {
		$spldm = explode('-', $request->date);
		$checkSubmitCount = Expenses::checkSubmited($spldm);
			for ($i=0; $i <= $request->count ; $i++) {
				$slt_subject = "subjectcode_".$i;
				$slt_subsubject = "details_".$i;
				$expenses = "expenses".$i;
				$cash = "cash".$i;
				$remarks="remarks".$i;
				$request->day = $i;
				$transaction="transaction_".$i;
				$Bank_NickName="Bank_NickName".$i;
				$Bank_subName="Bank_subName".$i;
				$slt_bkbranch = "Bank_No_".$i."-"."bankaccno_".$i;
				if ($request->$cash!="") {
					$cash=Expenses::cashmultipleregister($request,$checkSubmitCount);
					if($cash) {
						Session::flash('success', 'Inserted Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Inserted Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				} elseif ($request->$expenses!="") {
					$query=Expenses::expensesmultipleregister($request,$checkSubmitCount);
					if($query) {
						Session::flash('success', 'Inserted Sucessfully!'); 
						Session::flash('type', 'alert-success'); 
					} else {
						Session::flash('type', 'Inserted Unsucessfully!'); 
						Session::flash('type', 'alert-danger'); 
					}
				}
			}
			$spldm = explode('-', $request->date);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]);
		return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function ajaxmainsubject(Request $request) {
		$getsunsubject=Expenses::fnfetchmainsubject($request);
		$degreedata=json_encode($getsunsubject);
		echo $degreedata;
	}
	public static function cashaddedit(Request $request) {
		$sql = Expenses::fetchbanknames($request);
		if (!isset($request->cashflg)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		return view('Expenses.cashaddedit',['request' => $request,
											'sql' => $sql]);
	}
	public static function cashedit(Request $request) {
		if (!isset($request->id)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$expcash_sql = Expenses::fnGetCashEdit($request);
		$cash_sql = Expenses::fnGetCashEditdetails($expcash_sql[0]->billno);
		// print_r($cash_sql);exit();
		$sql = Expenses::fetchbanknames($request);
		return view('Expenses.cashaddedit',['request' => $request,
											'expcash_sql' => $expcash_sql,
											'cash_sql' => $cash_sql,
											'sql' => $sql]);
	}
	public static function cashaddeditprocess(Request $request) {
		$carry = 0;
		if($request->cashflg == 1 || $request->cashflg == 3) {
			$seperatemonandyr=explode("-" , $request->date);
			$getkessanki = Expenses::kessanki_ListView($request);
			$expbillno = Expenses::expbillno_ListView($request,$getkessanki[0]);
			$checkSubmitCount = Expenses::checkSubmited($seperatemonandyr);
			if($request->mainmenu != "pettycash") {
				if ($request->cashflg == 3) {
					$update = Expenses::addupdcash($request);
				}
			}
			$insert=Expenses::addcash($request,$carry,$checkSubmitCount,$expbillno);
			if($request->mainmenu == "pettycash") {
				$disp = Expenses::checkcashpettyadd($seperatemonandyr);
				if($disp > 0) {
					$fnaddexpenses=Expenses::fnaddtodev($request,"",$checkSubmitCount,$expbillno,2);
				} else {
					$fnaddexpenses=Expenses::fnadduptodev($request,"",$checkSubmitCount,$expbillno,2);
				}
			}
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
		} else {
			$update = Expenses::updatecash($request);
			$seperatemonandyr=explode("-" , $request->date);
			$checkSubmitCount = Expenses::checkSubmited($seperatemonandyr);
			$getkessanki = Expenses::kessanki_ListView($request);
			$expbillno = Expenses::expbillno_ListView($request,$getkessanki[0]);
			if($request->mainmenu == "pettycash") {
				$disp = Expenses::checkcashpettyadd($seperatemonandyr);
				if($disp > 0) {
					$fnaddexpenses=Expenses::fnaddtodev($request,"",$checkSubmitCount,$expbillno,2);
				} else {
					$fnaddexpenses=Expenses::fnadduptodev($request,"",$checkSubmitCount,$expbillno,2);
				}
			}
			// if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			// } else {
			// 	Session::flash('type', 'Updated Unsucessfully!'); 
			// 	Session::flash('type', 'alert-danger'); 
			// }
		}
			$spldm = explode('-', $request->date);
			Session::flash('selMonth', $spldm[1]); 
			Session::flash('selYear', $spldm[0]); 
			Session::flash('date', $request->date); 
			Session::flash('amount', $request->amount);
		if($request->mainmenu == "company_transfer" || $request->mainmenu == "expenses"){
			return Redirect::to('Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		} else{
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
	}
	public static function expenseshistory(Request $request) {
		if (!isset($request->bname) || !isset($request->delflg)) {
			return Redirect::to('Expenses/index?mainmenu=expenses&time='.date('YmdHis'));
		}
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		$accno = $request->accNo;
		$bname = $request->bname;
		$year = $request->selYear;
		$month = $request->selMonth;
		$trans_flg = $request->trans_flg;
 		$subject_type = $request->subject_type;
		if ($request->pettyflg != 1) {
			if($request->subject_type == "main_subject") {
			} else if($request->subject_type == "sub_subject") {
			} else if($request->subject_type == "bank_main_subject") {
				$view=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);
			} else {
				$view=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);
			}
		} else {
			if($request->delflg == 0) {
				$view=Expenses::pettycash_subhistoryvalues_details($request,$request->delflg,'','');
			} else {
				$view=Expenses::pettycash_subhistoryvalues_detailsdelflg1($request,$request->delflg,'','');				
			}
		}
		// print_r($view);exit();
		if($request->pettyflg != 1){
			if ($subject_type == "bank_main_subject") {
				 $grantTotalview=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);	
			} else {
				 $grantTotalview=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);	
			}
		} else{ 
			if($request->delflg == 0){
				$grantTotalview=Expenses::pettycash_subhistoryvalues_detailsamountdel($request,$request->delflg,'','');	
			}else{ 
				$grantTotalview=Expenses::pettycash_subhistoryvalues_detailsdelflg1amount($request,$request->delflg,'','');
			}
		}
		$amountTotal = 0;
		$chargeTotal = 0;
		foreach ($grantTotalview as $key => $value) {
			$amountTotal = $amountTotal+$value->amount;
			if($request->pettyflg == 1){
			} else {
			$chargeTotal = $chargeTotal+$value->amount;
			}
		}
		$disp = 0;
		$get_det = array();
		$disp = count($view);
		$i=0;
		foreach ($view as $key => $value) {
			// if ($value->remarks == "") {
			// 	$value->remarks = $value->remark_dtl;
			// }
			$get_det[$i]['id'] = $value->id;
			$get_det[$i]['year'] = $value->year;
			$get_det[$i]['month'] = $value->month;
			$get_det[$i]['amount'] = $value->amount;
			$get_det[$i]['date'] = $value->date;
			if(isset($value->Subject) || isset($value->Subject_jp) || isset($value->sub_eng) || isset($value->sub_jap) || isset($value->mainid) || isset($value->Bank_NickName)) {
				$get_det[$i]['Subject'] = $value->Subject;
				$get_det[$i]['Subject_jp'] = $value->Subject_jp;
				$get_det[$i]['sub_eng'] = $value->sub_eng;
				$get_det[$i]['sub_jap'] = $value->sub_jap;
				if($request->type != "4" && isset($value->mainid)) {
					$get_det[$i]['mainid'] = $value->mainid;
				}
				$get_det[$i]['Bank_NickName'] = $value->Bank_NickName;
			}
			$get_det[$i]['remarks'] = $value->remark_dtl;
			$get_det[$i]['bankname'] = $value->bankname;
			$get_det[$i]['bankaccno'] = $value->bankaccno;
			$get_det[$i]['transaction_flg'] = $value->transaction_flg;
			$get_det[$i]['del_flg'] = $value->del_flg;
			$i++;
		}
		// print_r($get_det);exit();
		if($request->pettyflg == 1){
			return view('Transfer.transferhistory',['request' => $request,
										'get_det' => $get_det,
										'disp' => $disp,
										'index' => $view,
										'view' => $view,
										'chargeTotal' => $chargeTotal,
										'amountTotal' => $amountTotal]);
		} else {
			return view('Expenses.expenseshistory',['request' => $request,
										'get_det' => $get_det,
										'disp' => $disp,
										'view' => $view,
										// 'view' => $disp,
										'amountTotal' => $amountTotal]);
		}
	}
	public static function pettycashhistory(Request $request) {
		if (!isset($request->subject) || !isset($request->bname)) {
			return Redirect::to('Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$amountTotal = 0;
		$disp = 0;
		$i = 0;
		$get_det = array();
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 100;
		}
		if($request->subject_type == "main_subject") {
			$view=Expenses::pettycash_historydetails($request,$request->subject,$request->selYear,$request->selMonth);
		} else if($request->subject_type == "sub_subject") {
			$view=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$request->selYear,$request->selMonth);
		} else if($request->subject_type == "bank_main_subject") {
			$view=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
		} else {
			 $view=Expenses::pettycash_history_bankdetails_subSubject($request,$request->bankName,$request->accNo,$request->selYear,$request->selMonth,$request->trans_flg);
		}
		if($request->subject_type == "main_subject") {
			$view1=Expenses::pettycash_historydetailsamount($request,$request->subject,$request->selYear,$request->selMonth);
		} else if($request->subject_type == "sub_subject") {
			$view1=Expenses::pettycashsubsubjhistorydetailsamount($request,$request->detail,$request->selYear,$request->selMonth);
		} else if($request->subject_type == "bank_main_subject") {
			$view1=Expenses::pettycash_bankmain_historydetailsamount($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
		} else {
			 $view1=Expenses::pettycash_history_bankdetails_subSubjectamount($request,$request->bankName,$request->accNo,$request->selYear,$request->selMonth,$request->trans_flg);
		}
		// print_r($view);exit();
		foreach ($view1 as $key => $value) {
			$amountTotal = $amountTotal+$value->amount;
		}
		$disp = count($view);
		foreach ($view as $key => $value) {
			$get_det[$i]['id'] = $value->id;
			$get_det[$i]['year'] = $value->year;
			$get_det[$i]['month'] = $value->month;
			$get_det[$i]['amount'] = $value->amount;
			$get_det[$i]['date'] = $value->date;
			if(isset($value->Subject) || isset($value->Subject_jp) || isset($value->sub_eng) || isset($value->sub_jap) || isset($value->mainid) || isset($value->pettyid) || isset($value->main_subject_name) || isset($value->sub_subject_name)) {
				$get_det[$i]['main_eng'] = $value->main_eng;
				$get_det[$i]['main_jap'] = $value->main_jap;
				$get_det[$i]['sub_eng'] = $value->sub_eng;
				$get_det[$i]['sub_jap'] = $value->sub_jap;
				$get_det[$i]['mainid'] = $value->mainid;
				$get_det[$i]['pettyid'] = $value->pettyid;
				$get_det[$i]['main_subject_name'] = $value->main_subject_name;
				$get_det[$i]['sub_subject_name'] = $value->sub_subject_name;
			}
			$get_det[$i]['remarks'] = $value->remark_dtl;
			$get_det[$i]['bankname'] = $value->bankname;
			$get_det[$i]['bankaccno'] = $value->bankaccno;
			$get_det[$i]['transaction_flg'] = $value->transaction_flg;
			$get_det[$i]['del_flg'] = $value->del_flg;
			$i++;
		}
		return view('Expenses.pettycashhistory',['request' => $request,
										'get_det' => $get_det,
										'disp' => $disp,
										'index' => $disp,
										'view' => $view,
										'amountTotal' => $amountTotal]);
	}
	public static function pettycashdownload(Request $request) {
		$template_name = 'resources/assets/uploadandtemplates/templates/expenses_detail.xls';
		$tempname = "Expenses_detail_";
		$excel_name=$tempname;
		$request->plimit = 200000;
		$request->page = "";
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
			$objPHPExcel->getActiveSheet()
					 ->setCellValue('G1', $request->selYear."年".$request->selMonth."月");
		    $g_query = Expenses::download_expenses($request->selYear,$request->selMonth);
			$initial_value = 4;
		    $disp = 0;
			$disp = count($g_query);
			$i = 0;
			$totval = 0;
			$k = 0;
			$rowclr=0;
			$exp_rsTotalAmount = 0;
			$temp = "";
			$get_det = array();
			$rsTotalAmount = 0;
			    foreach ($g_query as $key => $value) {
			    	$get_det[$k]['id'] = $value->id;
			    	$get_det[$k]['date'] = $value->date;
					$get_det[$k]['subject'] =$value->main_subject;
					$get_det[$k]['Subject'] =$value->main_eng;
					$get_det[$k]['Subject_jp'] =$value->main_jap;
					$get_det[$k]['details']= $value->sub_subject;
					$get_det[$k]['sub_eng']= $value->sub_eng;
					$get_det[$k]['sub_jap']= $value->sub_jap;
					$get_det[$k]['amount']= $value->amount;
					$get_det[$k]['currency_type']= $value->currency_type;
					if (isset($value->jp_amount)) {
						$get_det[$k]['jp_amount'] = $value->jp_amount;
					}
					$get_det[$k]['remark_dtl'] =$value->remark_dtl;
					$get_det[$k]['file_dtl']= $value->file_dtl;
					$get_det[$k]['del_flg']= $value->del_flg;
					$get_det[$k]['Bank_NickName']= $value->Bank_NickName;
					$get_det[$k]['check_number']= $value->check_number;
						if($value->main_subject == 'LastMonthTotal') {
							$get_det[$k]['bank']= 'LastMonthTotal';										
						} else if($value->main_subject == 'Last Month Balance') {
							$get_det[$k]['bank']= 'Last Month Balance';
						}else if($value->main_subject== 'Cash') {
							$get_det[$k]['bank']= $value->main_subject;
						}else {
							if (Session::get('languageval') == "en") {
								$selectedField = "Subject";
							} else {
								$selectedField = "Subject_jp";
							}
							$get_det[$k]['bank']=($get_det[$k][$selectedField]);
						}
						if($get_det[$k]['date']!= "") { 
							$get_det[$k]['loc']  = $get_det[$k]['date'];
						}	
						if($get_det[$k]['loc'] != $temp){
							if($value->main_subject == 'Last Month Balance') {
								$get_det[$k]['a']= date('Y-m-d');
							} else if( $value->date != ""){
								$get_det[$k]['datedetail']=  $value->date;
							} 
						}
						if($value->main_subject == 'Last Month Balance') {
							$get_det[$k]['detail']= 'Last Month Balance';
						} else {
							if (Session::get('languageval') == "en") {
								$selectedField = "sub_eng";
							} else {
								$selectedField = "sub_jap";
							}
							$get_det[$k]['detail']=($get_det[$k][$selectedField]);
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

						if($value->amount!= "" && $value->del_flg == "1") {
							if($value->amount<0 &&$value->del_flg == "1"){
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
							$get_det[$k]['trans_flg']=$value->transaction_flg;
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
					if ($get_det[$i]['bank'] != "Cash") { 
						$main_subject_val = $get_det[$i]['bank'];
					} else {
						$main_subject_val = $get_det[$i]['Bank_NickName']."-".$get_det[$i]['bankaccno']; 
					}}else{
						$main_subject_val =  $pre_balance;
					}
					if($get_det[$i]['subject'] != "") { 
					if (isset($get_det[$i]['trans_flg']) && $get_det[$i]['trans_flg'] == 1) { 
						if (Session::get('languageval') == "en")
							$trans_flg = "Debit";
						else
							$trans_flg = "引出";
					} else if (isset($get_det[$i]['trans_flg']) && $get_det[$i]['trans_flg'] == 2) { 
						if (Session::get('languageval') == "en")
							$trans_flg = "Credit";
						else
							$trans_flg = "入金";
					} else {
						$trans_flg = $get_det[$i]['detail'];
					}}else{
						$pos = mb_substr_count($get_det[$i]['cash'], '-');
						if ( $pos == 0) { 
							$trans_flg = "Debit";	
						} else {
							$trans_flg = "Credit";	
						} 
					}
					if($get_det[$i]['check_number'] == ""){

						$valuecheck = $get_det[$i]['remark_dtl'];

					} else {											
						$valuecheck = $check_no.":".$get_det[$i]['check_number'].PHP_EOL.$get_det[$i]['remark_dtl'];
					}
					$objPHPExcel->setActiveSheetIndex(0);
					/*$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()
								->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
								->getStartColor()->setARGB($style);*/
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":G".$temp_i)->getFill()->getStartColor()->setRGB($style);

					$objPHPExcel->getActiveSheet()
					 ->setCellValue('A'.$temp_i, $sno)
					 ->setCellValue('B'.$temp_i, ltrim($date_val, '0'))
					 ->setCellValue('C'.$temp_i, $main_subject_val)
					 ->setCellValue('D'.$temp_i, $trans_flg)
					 ->setCellValue('E'.$temp_i, $get_det[$i]['cash'])
					 ->setCellValue('F'.$temp_i, $get_det[$i]['expenses'])
					 ->setCellValue('G'.$temp_i, $valuecheck);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$temp_i)->getAlignment()->setWrapText(true);
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
				// if ($get_det[$i]['totalamount'] < 0) {
				// 	$totalRsColor = "#FF0000";
				// } else {
					$totalRsColor = "#0000FFff";
				// }
				$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getFont()->getColor()->setARGB($totalRsColor);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$temp_i)->getFont()->getColor()->setARGB($totalYenColor);
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
				// Write the file
				$flpath='.xls';
	      		header('Content-Type: application/vnd.ms-excel');
	      		header('Content-Disposition: attachment;filename="'.$flpath.'"');
	      		header('Cache-Control: max-age=0');
		  	}
		})->setFilename($excel_name. "_" . date("Ymd"))->download('xls');
	}
	public static function pettycashmainhistory(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesCashHistory.xls';
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
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3 =='Sub Subject' 				
					&& $cell4 =='Amount' && $cell5 =='Remarks') {
				$writeflag ='1'; 
			}
			if($writeflag == "1"){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "副件名")
					->setCellValue('D2', "単価")
					->setCellValue('E2', "備考");
					$debit_lab = "引出";
					$credit_lab = "入金";
				} else {
					$debit_lab = "Debit";
					$credit_lab = "Credit";
				}
				$amountTotal = 0;
				if ($request->subject_type == "bank_main_subject") {

					$view=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);
				} else if ($request->subject_type == "main_subject") {
					$view=Expenses::pettycash_historydetails($request,$request->subject,$request->selYear,$request->selMonth);	 
				} else {
					$view=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$request->selYear,$request->selMonth);	
				}
				if ($request->subject_type == "bank_main_subject") {
					$grantTotalview=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);	
				} else if ($request->subject_type == "main_subject") {
					 $grantTotalview=Expenses::pettycash_historydetails($request,$request->subject,$request->selYear,$request->selMonth);	
				} else {
					 $grantTotalview=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$request->selYear,$request->selMonth);	
				}
				foreach ($grantTotalview as $key => $value1) {
					$amountTotal = $amountTotal+$value1->amount;
				}
				$disp = 0;
				$disp = count($view);
				$i=0;
				$get_det = array();
				foreach ($view as $key => $value) {
					$get_det[$i]['id'] = $value->id;
					$get_det[$i]['year'] = $value->year;
					$get_det[$i]['month'] = $value->month;
					$get_det[$i]['amount'] = $value->amount;
					$get_det[$i]['date'] = $value->date;
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
					if(isset($value->remark_dtl)) {
						$get_det[$i]['remarks'] = $value->remark_dtl;
					} else {
						$get_det[$i]['remarks'] = "";
					}
					if(isset($value->mainid)) {
						$get_det[$i]['mainid'] = $value->mainid;
					}
					$get_det[$i]['bankname'] = $value->bankname;
					if (isset($value->main_subject_name)) {
						$get_det[$i]['main_subject_name'] = $value->main_subject_name;
					}
					if (isset($value->sub_subject_name)) {
						$get_det[$i]['sub_subject_name'] = $value->sub_subject_name;
					}
					$get_det[$i]['bankaccno'] = $value->bankaccno;
					$get_det[$i]['transaction_flg'] = $value->transaction_flg;
					$i++;
				}
				$temp_i = 3;
				$initial_value = 5;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				if ($request->subject_type == "bank_main_subject") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $request->detail ."->". $request->accNo);
				} else if ($request->subject_type == "main_subject") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $get_det[0]['main_subject_name']);
				} else {
					$msub = $get_det[0]['main_subject_name']."->".$get_det[0]['sub_subject_name'];
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $msub);
				} 
				$rowbktrclrr=0;
				$tmpyr=0;
				$tempdate=0;
				$sno=0;
				for ($j = 0; $j <count($get_det); $j++) {
					$temp_val = $j;
					$temp = $get_det[$j]['bankname'];
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
						$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('D3', "¥ ". number_format($amountTotal));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;
						$temp_i++;
					    /*$view=transferModel::transferhistorydetails($mnsub,$get_det[$j]['year'],$get_det[$j]['month']);
						$res = mysql_query($view);
						while($result=mysql_fetch_assoc($res)) {
							 $results=$result['amount'];
							 $result1=$result['fee'];
							 $amt=$amt+$results;
							 $fee=$fee+$result1;
						}*/
						if ($request->subject_type == "bank_main_subject") {
							$amtsql=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);
						} else if ($request->subject_type == "main_subject") {
							$amtsql=Expenses::pettycash_historydetails($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']);
						} else {
							$amtsql=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$get_det[$j]['year'],$get_det[$j]['month']);
						}
						foreach ($amtsql as $key => $value2) {
							$results=$value2->amount;
							 $amt=$amt+$results; 
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':C'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('D'.$temp_i, number_format($amt));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
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
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					if ($request->subject_type == "bank_main_subject") {	
							if($get_det[$j]['transaction_flg'] == 2){
								$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i,$credit_lab);
							} else{
								$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i,$debit_lab);
							}
					} else if ($request->subject_type == "main_subject") {
			
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['sub_subject_name']);
					} else {
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $get_det[$j]['sub_subject_name']);
					}
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, number_format($get_det[$j]['amount']));
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i, $get_det[$j]['remarks']);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":E".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":E".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:E'.$temp_i)->applyFromArray($styleArray);
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
					$temp = $get_det[$j]['bankname'];
					$tempdate = $get_det[$j]['date'];
					$i++;
				}
		// print_r($get_det);exit();
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
	public static function pettycashsubhistorydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesSubCashHistory.xls';
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
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3 =='Amount' && $cell4 =='Remarks') {
				$writeflag ='1'; 
			}
			
			if($writeflag == '1'){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "単価")
					->setCellValue('D2', "備考");
					$debit_lab = "引出";
					$credit_lab = "入金";
				} else {
					$debit_lab = "Debit";
					$credit_lab = "Credit";
				}
				$amountTotal = 0;
				if ($request->subject_type == "sub_subject") {

					$view=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$request->selYear,$request->selMonth);
				} else if ($request->subject_type == "bank_main_subject") {
					$view=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);	 
				} else {
					$view=Expenses::pettycash_history_bankdetails_subSubject($request,$request->bankName,$request->accNo,$request->selYear,$request->selMonth,$request->trans_flg);	
				}
				if ($request->subject_type == "sub_subject") {
					$grantTotalview=Expenses::pettycashsubsubjhistorydetails($request,$request->detail,$request->selYear,$request->selMonth);	
				} else if ($request->subject_type == "bank_main_subject") {
					 $grantTotalview=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$request->selYear,$request->selMonth);	
				} else {
					 $grantTotalview=Expenses::pettycash_history_bankdetails_subSubject($request,$request->bankName,$request->accNo,$request->selYear,$request->selMonth,$request->trans_flg);	
				}
				foreach ($grantTotalview as $key => $value1) {
					$amountTotal = $amountTotal+$value1->amount;
				}
				$disp = 0;
				$disp = count($view);
				$i=0;
				$get_det = array();
				foreach ($view as $key => $value) {
					$get_det[$i]['id'] = $value->id;
					$get_det[$i]['year'] = $value->year;
					$get_det[$i]['month'] = $value->month;
					$get_det[$i]['amount'] = $value->amount;
					$get_det[$i]['date'] = $value->date;
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
					if(isset($value->remark_dtl)) {
						$get_det[$i]['remarks'] = $value->remark_dtl;
					} else {
						$get_det[$i]['remarks'] = "";
					}
					if(isset($value->mainid)) {
						$get_det[$i]['mainid'] = $value->mainid;
					}
					$get_det[$i]['bankname'] = $value->bankname;
					if (isset($value->main_subject_name)) {
						$get_det[$i]['main_subject_name'] = $value->main_subject_name;
					}
					if (isset($value->sub_subject_name)) {
						$get_det[$i]['sub_subject_name'] = $value->sub_subject_name;
					}
					$get_det[$i]['bankaccno'] = $value->bankaccno;
					$get_det[$i]['transaction_flg'] = $value->transaction_flg;
					$i++;
				}
				$temp_i = 3;
				$initial_value = 5;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				if ($request->subject_type == "sub_subject") {
					$msub = $get_det[0]['main_subject_name']."->".$get_det[0]['sub_subject_name'];
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $msub);
				} else if ($request->subject_type == "bank_main_subject") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $request->detail ."->". $request->accNo);
				} else {
					if($request->trans_flg == 2){
						$objPHPExcel->getActiveSheet()->setCellValue('C1',$request->detail ."-". $request->accNo ."->".$credit_lab);
					} else{
						$objPHPExcel->getActiveSheet()->setCellValue('C1',$request->detail ."-". $request->accNo ."->".$debit_lab);
					}
				} 
				$rowbktrclrr=0;
				$temp=0;
				$tmpyr=0;
				$tempdate=0;
				$sno=0;
				for ($j = 0; $j <count($get_det); $j++) {
					$temp_val = $j;
					// $temp = $get_det[$j]['bankname'];
					// $tempdate = $get_det[$j]['date'];
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
						$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('C3', "¥ ". number_format($amountTotal));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;
						$temp_i++;
						if ($request->subject_type == "sub_subject") {
							$amtsql=Expenses::expenses_historydetails_subSubject1($request->detail,$get_det[$j]['year'],$get_det[$j]['month']);	
						} else if ($request->subject_type == "bank_main_subject") {
							 $amtsql=Expenses::pettycash_bankmain_historydetails($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);	
						} else {
							 $amtsql=Expenses::pettycash_history_bankdetails_subSubject($request,$request->bankName,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month'],$request->trans_flg);	
						}
						foreach ($amtsql as $key => $value2) {
							$results=$value2->amount;
							 $amt=$amt+$results; 
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':B'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('C'.$temp_i, number_format($amt));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
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
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, number_format($get_det[$j]['amount']));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['remarks']);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":D".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":D".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$temp_i)->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(-1);
					$temp = $get_det[$j]['bankname'];
					$tempdate = $get_det[$j]['date'];
					$i++;
				}
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
	public static function expensesmainhistorydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesCashHistory.xls';
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
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3 =='Sub Subject' && $cell4 =='Amount' && $cell5 =='Remarks') {
				$writeflag ='1'; 
			}
			
			if($writeflag == '1'){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "副件名")
					->setCellValue('D2', "単価")
					->setCellValue('E2', "備考");
					$debit_lab = "引出";
					$credit_lab = "入金";
				} else {
					$debit_lab = "Debit";
					$credit_lab = "Credit";	
				}
				$amountTotal = 0;
				$accno = $request->accNo;
				$bname = $request->bname;
				$year = $request->selYear;
				$month = $request->selMonth;
				$trans_flg = $request->trans_flg;
				if($request->subject_type == "bank_main_subject") {
					$view=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);
				} else {
					$view=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);
				}
				if($request->subject_type == "bank_main_subject") {
					$grantTotalview=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);
				} else {
					$grantTotalview=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);
				}
				foreach ($grantTotalview as $key => $value) {
					$amountTotal = $amountTotal+$value->amount;
				}
				$disp = 0;
				$get_det = array();
				$disp = count($view);
				$i=0;
				foreach ($view as $key => $value1) {
					$get_det[$i]['id'] = $value1->id;
					$get_det[$i]['year'] = $value1->year;
					$get_det[$i]['month'] = $value1->month;
					$get_det[$i]['amount'] = $value1->amount;
					$get_det[$i]['date'] = $value1->date;
					if(isset($value1->Subject)) {
						$get_det[$i]['Subject'] = $value1->Subject;
					}
					if(isset($value1->Subject_jp)) {
						$get_det[$i]['Subject_jp'] = $value1->Subject_jp;
					}
					if(isset($value1->sub_eng)) {
						$get_det[$i]['sub_eng'] = $value1->sub_eng;
					}
					if(isset($value1->sub_jap)) {
						$get_det[$i]['sub_jap'] = $value1->sub_jap;
					}
					if(isset($value1->remark_dtl)) {
						$get_det[$i]['remarks'] = $value1->remark_dtl;
					} else {
						$get_det[$i]['remarks'] = "";
					}
					if(isset($value1->mainid)) {
						$get_det[$i]['mainid'] = $value1->mainid;
					}
					$get_det[$i]['bankname'] = $value1->bankname;
					$get_det[$i]['bankaccno'] = $value1->bankaccno;
					$get_det[$i]['transaction_flg'] = $value1->transaction_flg;
					$i++;
				}
				$temp_i = 3;
				$initial_value = 5;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				if ($request->subject_type == "bank_main_subject") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $request->bankName."-".$request->accNo);
				} else {
					$msub = $request->bankName."-".$request->accNo." -> ";
					if ($trans_flg == 1) {
						$msub .=  "Debit";
					} else {
						$msub .= "Credit";
					}
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $msub);
				}
				$rowbktrclrr=0;
				$tempdate=0;
				$tmpyr=0;
				$sno=0;
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
						$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('D3', "¥ ".number_format($amountTotal));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;
						$temp_i++;
						if($request->subject_type == "bank_main_subject") {
							$amtsql=Expenses::expenses_history_bankdetails($request,$bname,$accno,$get_det[$j]['year'],$get_det[$j]['month']);
						} else {
							$amtsql=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$get_det[$j]['year'],$get_det[$j]['month']);
						}
						foreach ($amtsql as $key => $value2) {
							$results=$value2->amount;
							$amt=$amt+$results; 
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':C'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('D'.$temp_i, number_format($amt));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':E'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
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
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					if ($request->subject_type == "bank_main_subject") {
						if ($get_det[$j]['transaction_flg'] == 1) {
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $debit_lab);
						} else {
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, $credit_lab);
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, number_format($get_det[$j]['amount']));
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$temp_i, $get_det[$j]['remarks']);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":E".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":E".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:E'.$temp_i)->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$temp_i)->getAlignment()->setWrapText(true);
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
	public static function expensessubhistorydownload(Request $request) {
		//Setting page limit
		$request->plimit = 200000;
		$request->page = "";
		$template_name = 'resources/assets/uploadandtemplates/templates/expensesSubCashHistory.xls';
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
			if($cell1 =='S.No' && $cell2 =='Date' && $cell3 =='Amount' && $cell4 =='Remarks') {
				$writeflag ='1'; 
			}
			
			if($writeflag == '1'){ 
				if (Session::get('languageval') != "en") {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A2', "連番")
					->setCellValue('B2', "日付")
					->setCellValue('C2', "単価")
					->setCellValue('D2', "備考");
					$debit_lab = "引出";
					$credit_lab = "入金";
				} else {
					$debit_lab = "Debit";
					$credit_lab = "Credit";	
				}
				$amountTotal = 0;
				$accno = $request->accNo;
				$bname = $request->bname;
				$year = $request->selYear;
				$month = $request->selMonth;
				$trans_flg = $request->trans_flg;
				if($request->subject_type == "bank_main_subject") {
					$view=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);
				} else {
					$view=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);
				}
				if($request->subject_type == "bank_main_subject") {
					$grantTotalview=Expenses::expenses_history_bankdetails($request,$bname,$accno,$year,$month);
				} else {
					$grantTotalview=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$year,$month);
				}
				foreach ($grantTotalview as $key => $value) {
					$amountTotal = $amountTotal+$value->amount;
				}
				$disp = 0;
				$get_det = array();
				$disp = count($view);
				$i=0;
				foreach ($view as $key => $value1) {
					$get_det[$i]['id'] = $value1->id;
					$get_det[$i]['year'] = $value1->year;
					$get_det[$i]['month'] = $value1->month;
					$get_det[$i]['amount'] = $value1->amount;
					$get_det[$i]['date'] = $value1->date;
					if(isset($value1->Subject)) {
						$get_det[$i]['Subject'] = $value1->Subject;
					}
					if(isset($value1->Subject_jp)) {
						$get_det[$i]['Subject_jp'] = $value1->Subject_jp;
					}
					if(isset($value1->sub_eng)) {
						$get_det[$i]['sub_eng'] = $value1->sub_eng;
					}
					if(isset($value1->sub_jap)) {
						$get_det[$i]['sub_jap'] = $value1->sub_jap;
					}
					if(isset($value1->remark_dtl)) {
						$get_det[$i]['remarks'] = $value1->remark_dtl;
					} else {
						$get_det[$i]['remarks'] = "";
					}
					if(isset($value1->mainid)) {
						$get_det[$i]['mainid'] = $value1->mainid;
					}
					$get_det[$i]['bankname'] = $value1->bankname;
					$get_det[$i]['bankaccno'] = $value1->bankaccno;
					$get_det[$i]['transaction_flg'] = $value1->transaction_flg;
					$i++;
				}
				$temp_i = 3;
				$initial_value = 5;
				$bluecolor = '#0000FFff';
				$headerbgcolor = 'D3D3D3';
				if ($request->subject_type == "bank_main_subject") {
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $request->bankName."-".$request->accNo);
				} else {
					$msub = $request->bankName."-".$request->accNo." -> ";
					if ($trans_flg == 1) {
						$msub .=  "Debit";
					} else {
						$msub .= "Credit";
					}
					$objPHPExcel->getActiveSheet()->setCellValue('C1', $msub);
				}
				$rowbktrclrr=0;
				$tempdate=0;
				$tmpyr=0;
				$sno=0;
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
						$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A3', "Grand Total")
					 	 ->setCellValue('C3', "¥ ".number_format($amountTotal));
						$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->getColor()->setARGB($bluecolor);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->getStartColor()->setRGB($headerbgcolor);
					} 
					if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
						$amt=0;$results=0;
						$temp_i++;
						if($request->subject_type == "bank_main_subject") {
							$amtsql=Expenses::expenses_history_bankdetails($request,$bname,$accno,$get_det[$j]['year'],$get_det[$j]['month']);
						} else {
							$amtsql=Expenses::expenses_history_bankdetails_subSubject($request,$bname,$accno,$trans_flg,$get_det[$j]['year'],$get_det[$j]['month']);
						}
						foreach ($amtsql as $key => $value2) {
							$results=$value2->amount;
							$amt=$amt+$results; 
						}
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$temp_i.':B'.$temp_i);
						$objPHPExcel->getActiveSheet()
					 	 ->setCellValue('A'.$temp_i, $get_det[$j]['year']."年".$get_det[$j]['month']."月")
					 	 ->setCellValue('C'.$temp_i, number_format($amt));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.':D'.$temp_i)->getFill()->getStartColor()->setRGB($headerbgcolor);
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
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$temp_i, $get_det[$j]['date']);
					}
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$temp_i, number_format($get_det[$j]['amount']));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$temp_i, $get_det[$j]['remarks']);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":D".$temp_i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i.":D".$temp_i)->getFill()->getStartColor()->setRGB($style);
					$objPHPExcel->getActiveSheet()->getStyle('A2:D'.$temp_i)->applyFromArray($styleArray);
					$objPHPExcel->getActiveSheet()->getRowDimension($temp_i)->setRowHeight(20);
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$temp_i)->getAlignment()->setWrapText(true);
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
	}