<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\AudPayment;
use App\Model\Estimation;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use App\Http\Helpers;
use Carbon;



class AudPaymentController extends Controller {

	public static function index(Request $request) {
		$pre = 0;	
		$intervaldayfrom="16";
		$intervaldayto="15";
		$preArray=array();
		$split_date="";
		$date_month="";
		$cur_key=array();
		$gettotalforaperiod=array();
		$paymentsortarray = [$request->paymentsort=>$request->paymentsort,
                    'payment_date'=> trans('messages.lbl_paymentdate')];

        if ($request->paymentsort == "") {
        	$request->paymentsort = "payment_date";
      	}

		if (empty($request->sortOrder)) {
        	$request->sortOrder = "asc";
      	}

      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}

		$g_accountperiod = AudPayment::fnGetAccountPeriod($request);
		$account_close_yr = $g_accountperiod[0]->Closingyear;
		$account_close_mn = $g_accountperiod[0]->Closingmonth;
		$account_period = intval($g_accountperiod[0]->Accountperiod);
		$splityear = explode("-", $request->previou_next_year);
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}

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
		$from_date = $last_year . "-" . substr("0" . $account_close_mn, -2). "-" . $intervaldayfrom;
		$to_date = $current_year . "-" . substr("0" . ($account_close_mn + 1), -2). "-"  . $intervaldayto;
		$est_query = AudPayment::fnGetEstimateRecord($from_date, $to_date);
		$dbrecord = array();
		foreach ($est_query as $key => $value) {
			$currentdate = Carbon\Carbon::createFromFormat('Y-m-d', $value->payment_date);
			$currentdate = $currentdate->modify('first day of this month');
			$currentdate->addDays(14);
			$currentdate = $currentdate->format('Y-m-d');
			if ($value->payment_date>$currentdate) {
				$addmonth = Carbon\Carbon::createFromFormat('Y-m-d', $value->payment_date);
				$addmonth   = $addmonth->addMonths(1);
				$addmonth = $addmonth->modify('first day of this month');
				$addmonth = $addmonth->format('Y-m');
				$value->invoice_payment_date = $addmonth;
			}

			$dbrecord[]=$value->invoice_payment_date;
		}

		$est_query1 = AudPayment::fnGetEstimateRecordPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		foreach ($est_query1 as $key => $value) {
			$dbprevious[]=$value->invoice_payment_date;
			$dbpreviousYr[]=substr($value->invoice_payment_date, 0, 4);
		}

		$est_query2 = AudPayment::fnGetEstimateRecordNext($to_date);
		$dbnext = array();
		foreach ($est_query2 as $key => $value) {
			$dbnext[]=$value->invoice_payment_date;
		}

		$dbrecord = array_unique($dbrecord);

		$dbpreviouscheck = array_unique($dbprevious);
		if(empty($dbrecord)) {
			$db_year_month = array();
				foreach ($dbpreviouscheck AS $dbrecordkey => $dbrecordcheck) {
					$split_val = explode("-", $dbrecordcheck);
					$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
				}
	    } else {
			$db_year_month = array();
			$t = 0;
			foreach ($dbrecord AS $dbrecordkey => $dbrecordvalue) {
				$split_val = explode("-", $dbrecordvalue);
				$db_year_month[$split_val[0]][intval($split_val[1])] = intval($split_val[1]);
				$preArray[$t] = $dbrecordvalue;
				$t++;			
			}
		}

		if (isset($dbprevious[$pre-1])) {
		$split_vpre = explode("-", $dbprevious[$pre-1]);

		// $split_vpre = explode("-", $dbprevious[$pre-1]);

		if ($account_close_mn == 12) {
			if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {

									/*for ($i = ($account_period + 1); $i <= 12; $i++) {

										$year_month[($split_vpre[0]-1)][$i] = $i;

									}

*/

				for ($i = 1; $i <= $account_close_mn; $i++) {
					$year_month[$split_vpre[0]][$i] = $i;
				}
					$last_year = $split_vpre[0]- 1;
			        $current_year = $split_vpre[0];
				}else{
					for ($i = 1; $i <= 12; $i++) {
						$year_month[$current_year][$i] = $i;
					}
				}
			} else {

				if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {
					for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
						$year_month[($split_vpre[0]-1)][$i] = $i;
					}
					for ($i = 1; $i <= $account_close_mn; $i++) {
						$year_month[$split_vpre[0]][$i] = $i;
					}
					$last_year = $split_vpre[0]- 1;
					$current_year = $split_vpre[0];
				}else{
					for ($i = ($account_close_mn + 1); $i <= 12; $i++) {
						$year_month[$last_year][$i] = $i;
					}

					for ($i = 1; $i <= $account_close_mn; $i++) {
						$year_month[$current_year][$i] = $i;
					}
				}
			}

			}

			// Future USe

			if(isset($date_month)) {
				$split_date = explode('-', $date_month);
			}

			if (!empty($preArray)) {
				for ($i = 0; $i < count($preArray); $i++) {
					if ($preArray[$i] == date('Y-m')) {
						if(isset($preArray[$i - 1])) {
							$cur_key=array_keys($preArray,$preArray[$i - 1]);
							if(!empty($preArray[$i - 2])) {
								$preArray[$i] = $preArray[$i - 2];
							} else {
								if (isset($preArray[$cur_key[0]-1])) {

									$preArray[$i] = $preArray[$cur_key[0]-1];
								}
							}
						}
					}
				}
			}

			$currentkey = "";

			if (!isset($request->selMonth) || empty($request->selMonth)) {
				// $dbrecordvalue this array is for CurrentYr and CurrentMonth Record
				if ((empty($dbrecordvalue))&&(!empty($dbprevious))) {
						$date_month = $dbprevious[$pre-1];
				} else {
					if (isset($cur_key[0])) {
						$currentkey = $cur_key[0]-1;
					}

					if(empty($preArray[$currentkey])){
						$date_month=date('Y-m');
					}else{
						if (isset($preArray[count($preArray) - 1])) {
							$date_month = $preArray[count($preArray) - 1];
						}
			        }
				}
			} else {
				if (isset($request->selMonth) && !empty($request->selMonth) ) {
							/*$date_month = $_REQUEST['selYear']."-".$_REQUEST['selMonth'];*/
					$date_month = $request->selYear . "-" . substr("0" . $request->selMonth , -2);
				} else {
					$date_month = $request->date_month;
				}
			}

			if($request->selYear=="") {
				$request->selYear=date("Y");
				$request->selMonth=date("m");
			}

			$totaldispval=0;
			$date_month=$request->selYear."-".$request->selMonth;

			//ACCOUNT PERIOD FOR PARTICULAR YEAR MONTH

			$priormonth = date ('Y-m', strtotime ( '-1 month' , strtotime ( $date_month )));
			$datemonths=$date_month."-".$intervaldayfrom;
			$intervalfrom=$priormonth."-".$intervaldayfrom;
			$intervalto=$date_month."-".$intervaldayto;
			$premonth = date ('Y-m-d', strtotime ( '-1 month' , strtotime ( $intervalfrom )));
			$account_val = Common::getAccountPeriod($year_month, $account_close_yr, $account_close_mn, $account_period);
			$g_query = AudPayment::fnGetPaymentDetails($request,$intervalfrom,$intervalto);
			$get_det = array();
			$k = 0;
			$rsTotalAmount = 0;

			// SET FROM DATE

			$currentfrom = Carbon\Carbon::createFromFormat('Y-m-d', $datemonths);
			$past6months   = $currentfrom->subMonths(7);
			$past6months = $past6months->modify('first day of this month');
			$past6months->addDays(15);
			$oldbalance_from = $past6months->format('Y-m-d');


			$currentto = Carbon\Carbon::createFromFormat('Y-m-d', $datemonths);
			$past1month   = $currentto->subMonths(1);
			$past1month = $past1month->modify('first day of this month');
			$past1month->addDays(14);
			$oldbalance_to = $past1month->format('Y-m-d');

			//----------------------------

			$bankcharge_total = 0;
			$debit_total = 0;
			$credit_total = 0;
			$grand_total = 0;
			$balances = 0;
			$Debitval = 0;
			$Creditval = 0;
			$firsttimeonly = 0;

			foreach ($g_query as $key => $value) {

				if ($value->BCtotal == 0 || $value->BCtotal === NULL) {
					$value->BCtotal = 0;
				}

				if ($value->payidtotal == 0 || $value->payidtotal === NULL) {
					$value->payidtotal = 10000000;
				}

				$get_det[$k]['bank_charge'] = "";
				$get_det[$k]['previousamountstyle'] = "";

				// FOR DEBIT AND CREDIT PROCESS

				$flg = 0;

				if(isset($g_query[$key+1])) {
					if($g_query[$key+1]->custid == $value->custid 
							&& $firsttimeonly == 0) {
						$flg = 1;
						$firsttimeonly = 1;
					} else if($value->custid != $g_query[$key+1]->custid) {
						$firsttimeonly = 0;
						$get_det[$k]['bank_charge'] = number_format($value->BCtotal);
					}

					if ($g_query[$key+1]->custid == $value->custid && $g_query[$key+1]->payidtotal != $value->payidtotal) {
						$firsttimeonly = 0;
						$get_det[$k]['bank_charge'] = number_format($value->BCtotal);
					}

					// Start Same invoice multiple payment bank charge

					if ($g_query[$key+1]->invoiceno == $value->invoiceno) {
						$get_det[$k]['bank_charge'] = $value->bank_charge;
						$get_det[$k]['previousamountstyle']='style="color:#E65100;"';
					}

					// End Same invoice multiple payment bank charge

				} else {
					$get_det[$k]['bank_charge'] = $value->bank_charge;
				}

				// Start Same invoice multiple payment bank charge

				if (isset($g_query[$key-1])) {
					if ($g_query[$key-1]->invoiceno == $value->invoiceno) {
						$value->payidtotal = "10000000";
						$get_det[$k]['bank_charge'] = $value->bank_charge;
					}
				}

				// End Same invoice multiple payment bank charge

				if ($value->CHK == 0 || $flg == 1) {
					$balances = 0;
				} else {
					$balances = $balances;
				}

				if (isset($g_query[$key-1])) {
					if ($value->invoiceno == $g_query[$key-1]->invoiceno) {
						$value->paymentamount=$g_query[$key-1]->totalval;
						$value->combinedpay=str_replace(",","",$value->deposit_amount);
						$value->BCtotal=str_replace(",","",$value->bank_charge);
						$value->TOTALVALUE=$value->deposit_amount;
					}
				}

				$balances = $balances+str_replace(",","",$value->paymentamount);
				$minusresult = $value->combinedpay-$balances;
				if ($value->combinedpay === NULL && $balances == 0) {
					$minusresult = NULL;
				}

				if ($minusresult < 0 || $minusresult === NULL) {
					if ($minusresult < 0) {
						$Debitval = number_format(abs($minusresult+$value->BCtotal));
					} else {
						$Debitval = $value->TOTALVALUE;
					}
				} else {
					$Debitval = 0;
				}

				if ($minusresult > 0 || ($minusresult == 0 && $minusresult !== NULL) ) {
					if ($value->totalval < 0) { 
					// Excess payment logic
						$Creditval = number_format(str_replace(",","",$value->TOTALVALUE)-str_replace(",","",$value->totalval));
						$get_det[$k]['excessamountstyle']='style="color:#FF0051;"';
					} else {
						$Creditval = $value->TOTALVALUE;
						$get_det[$k]['excessamountstyle']='';
					}

				}

				else {
					if ($minusresult < 0) {
						$Creditval = number_format(str_replace(",","",$value->paymentamount)+$minusresult);
						$get_det[$k]['excessamountstyle']='';
					} else {
						$Creditval = number_format(abs($minusresult));
						$get_det[$k]['excessamountstyle']='';
					}
				}

				// END FOR DEBIT AND CREDIT PROCESS
				$get_det[$k]['id'] = $value->id;
				$get_det[$k]['invpaymentdate'] = $value->invpaymentdate; 
				$get_det[$k]['user_id'] = $value->user_id;
				$get_det[$k]['invid'] = $value->invid;
				$get_det[$k]['payidtotal'] = $value->payidtotal;
				$get_det[$k]['quot_date'] = $value->quot_date;
				$get_det[$k]['company_name'] = $value->company_name;
				$get_det[$k]['payment_date'] = $value->payment_date;
				$get_det[$k]['project_name'] = $value->project_name;
				$get_det[$k]['clientName'] = $value->clientName;
				$get_det[$k]['deposit_amount'] = $value->deposit_amount;
				$get_det[$k]['totalval'] = $value->totalval;
				$get_det[$k]['invoiceno'] = $value->invoiceno;
				$get_det[$k]['Debitval'] = $Debitval;
				$get_det[$k]['Creditval'] = $Creditval;
				$get_det[$k]['payinvid'] = $value->payinvid;
				$get_det[$k]['paid_status'] = $value->paid_status;
				// $get_det[$k]['totalvalue'] = $value->totalvalue;
				$get_det[$k]['clientnumber'] = $value->clientnumber;
				$get_det[$k]['oldbalance']=AudPayment::fnGETtotalAmount($request,$oldbalance_from,$oldbalance_to,$value->clientnumber);
				if(isset($g_query[$key+1])) {
					if($g_query[$key+1]->invoiceno == $value->invoiceno) {
						$Debitval = 0;
					}
				}
				$k++;
				$bankcharge_total += str_replace(",","",$value->bank_charge);
				$debit_total += str_replace(",","",$Debitval);
				$credit_total += str_replace(",","",$Creditval);
			}
			self::array_sort_by_column($get_det, 'invpaymentdate', $request->sortOrder,
					'payidtotal');
			$grand_total = $debit_total+$credit_total+$bankcharge_total;
			$allTotal = AudPayment::fnGetPaymentAllTotal($request,$intervalfrom,$intervalto);
			$g_tot_query = AudPayment::fnGetPaymentTotalValue($date_month);
		return view('AudPayment.index',[

									'g_query' => $g_query,

									'get_det' => $get_det,

									'g_tot_query' => $g_tot_query,

									'account_period' => $account_period,

									'year_month' => $year_month,

									'db_year_month' => $db_year_month,

									'date_month' => $date_month,

									'dbnext' => $dbnext,

									'dbprevious' => $dbprevious,

									'last_year' => $last_year,

									'paymentsortarray' => $paymentsortarray,

									'gettotalforaperiod' => $gettotalforaperiod,

									'current_year' => $current_year,

									'account_val' => $account_val,

									'allTotal' => $allTotal,

									'debit_total' => $debit_total,

									'bankcharge_total' => $bankcharge_total,

									'credit_total' => $credit_total,

									'grand_total' => $grand_total,

									'request' => $request]);

	}

	public static function array_sort_by_column(&$arr, $col, $dir, $col1) {

		if (strtolower($dir) == "asc") {
			$order = SORT_ASC;
		} else {
			$order = SORT_DESC;
		}

	    $sort_col = array();
	    $sort_col1 = array();
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	        $sort_col1[$key] = $row[$col1];
	    }
	    // array_multisort($sort_col, $dir, $arr);
	    array_multisort($sort_col, $order, $sort_col1, $order, $arr);

	}



	public function getaccount() {

		$bank_id = $_REQUEST['bank_id'];
		$res = Payment::fnGetBankAccountDetails($bank_id);
		$rslt = "";
		$type  = "";

		if ($res[0]->Type == 1) {
			$type = "普通";
		} 

		else if ($res[0]->Type == 2) {
			$type = "Other";
		} 

		else {
			$type = $res[0]->Type;
		}
		$rslt = $type."$".$res[0]->AccNo."$".$res[0]->FirstName."$".$res[0]->Branch."$".$res[0]->BankName."$". $res[0]->BranchName;
		echo $rslt;
		exit;

	}

}