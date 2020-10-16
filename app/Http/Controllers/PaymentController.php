<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Payment;
use App\Model\Estimation;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;
use App\Http\Helpers;
use Carbon;

class PaymentController extends Controller {
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
		$g_accountperiod = Payment::fnGetAccountPeriod($request);
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
		$est_query = Payment::fnGetEstimateRecord($from_date, $to_date);
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
		$est_query1 = Payment::fnGetEstimateRecordPrevious($from_date);
		$dbprevious = array();
		$dbpreviousYr = array();
		foreach ($est_query1 as $key => $value) {
			$dbprevious[]=$value->invoice_payment_date;
			$dbpreviousYr[]=substr($value->invoice_payment_date, 0, 4);
		}
		$est_query2 = Payment::fnGetEstimateRecordNext($to_date);
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
			$g_query = Payment::fnGetPaymentDetails($request,$intervalfrom,$intervalto);
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
				$get_det[$k]['oldbalance']=Payment::fnGETtotalAmount($request,$oldbalance_from,$oldbalance_to,$value->clientnumber);
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
			$allTotal = Payment::fnGetPaymentAllTotal($request,$intervalfrom,$intervalto);
			$g_tot_query = Payment::fnGetPaymentTotalValue($date_month);
		return view('Payment.index',[
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
	public static function  paymentaddeditprocess(Request $request) {
		if($request->type=="edit") {
			$g_query = Payment::fnUpdatePayment($request);
		} else {
			if($request->invoice_payment_date !="") {
				$day = substr($request->invoice_payment_date, 8,2);
				$mn = substr($request->invoice_payment_date, 5,2);
				$yr = substr($request->invoice_payment_date, 0,4);
				if($day>=16) {
					if($mn==12) {
						$mn=1;
						$yr=$yr+1;
					} else {
						$mn=$mn+1;
					}
				}
				$request->selYear = $yr;
				$request->selMonth = $mn;
			}
			$paymentId = Payment::fnGeneratePaymentID();
			$insert = Payment::fnInsertPayment($request,$paymentId);
			if ($insert) {
				$hididconcade = $request->hididconcade;
				$splitval = explode(",", $hididconcade);
				for ($i = 0; $i < count($splitval); $i++) {
					$update = Payment::fnUpdatePaidInvoice($request,$splitval[$i],$request->date_month);
				}
			}
		} ?>
		<form name="frmedit" id="frmedit"  action="../Payment/index?mainmenu=<?php echo $request->mainmenu;?>&time=<?php echo date('YmdHis'); ?>" method="post">
			<input type = "hidden" id = "selYear" name = "selYear" value="<?php echo $request->selYear; ?>">
			<input type = "hidden" id = "selMonth" name = "selMonth" value="<?php echo $request->selMonth; ?>">
		</form>
		<script type="text/javascript">
			document.forms['frmedit'].submit();
		</script>
<?php }
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
	public function PaymentEdit(Request $request) {
		// if (!isset($request->backflg)) {
		// 	return Redirect::to('Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		// }
		$estimate_id = $request->estimate_id;
        $g_bank = Payment::fnGetBankDetails($request);
		$get_paymentdata = Payment::fnGetEstimateUserData($estimate_id);
		$bankid = $get_paymentdata[0]->bankid;
		$bankbranchid = $get_paymentdata[0]->branchid;
		$acc_no = $get_paymentdata[0]->acc_no;
		
		$get_data = Payment::fnGetInvoiceDtl($get_paymentdata[0]->invoice_id);
		$oldbalance_from="";
		$currentfrom = Carbon\Carbon::createFromFormat('Y-m-d', $get_paymentdata[0]->date_month.'-01');
		$past6months   = $currentfrom->subMonths(13);
		$past6months = $past6months->modify('first day of this month');
		$past6months->addDays(15);
		$oldbalance_from = $past6months->format('Y-m-d');
		$get_row = Payment::fnGetInvoiceDetails($get_paymentdata[0]->invoice_id,$oldbalance_from, $get_paymentdata[0]->date_month, $get_data[0]->trading_destination_selection, $get_data[0]->paid_status);
		$get_cnt = count($get_row);

		// GET TAX FROM ESTIMATE TABLE
		$get_tax = Payment::fnGetEstimateDtl($get_data[0]->estimate_id);

		// GET PERCENTAGE OF TAX FROM KESSAN TABLE
		$execute_tax = Payment::fnGetTaxDetails($get_data[0]->quot_date);

		return view('Payment.paymentedit',['g_bank' => $g_bank,
											'get_paymentdata' => $get_paymentdata,
											'get_data' => $get_data,
											'get_row' => $get_row,
											'get_tax' => $get_tax,
											'execute_tax' => $execute_tax,
											'request' => $request]);
	}
	public static function customerview(Request $request) {
		if (!isset($request->companyname)) {
			return Redirect::to('Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$companynameClick=$request->companyname;
		if (empty($request->plimit)) {
			$request->plimit = 50;
		}
		$query = Payment::fnPaymentlistview($request);
		$payid = array();
		$userValue=array();
		$inv_query=array();
		$i = 0;
		$payment_total=0;
		$invoice_total=0;
		foreach ($query as $key => $value) {
			$inarraylist=0;
			$grandtotal =0;
			$balance_amount =0;
			$userValue[$i]['paid_id']=$value->paid_id;
			if (!in_array($value->paid_id, $payid)) {
				array_push($payid, $value->paid_id);
				$inarraylist=1;
			}
			$inv_query = Payment::fninvoicelistview($request,$companynameClick,$value->paid_id);
			foreach ($inv_query as $key => $data) {
				if ($inarraylist==1) {
					$userValue[$i]['id'] = $data->id;
					$userValue[$i]['user_id'] = $data->user_id;
					$userValue[$i]['company_name'] = $data->company_name;
					$userValue[$i]['project_name'] = $data->project_name;
					$userValue[$i]['pay_inv_date'] = $data->quot_date;
				}
				$invoice_amount =str_replace(',', '',$data->totalval);
				if($data->tax !=2 ){
					$gettaxquery = Estimation::fnGetTaxDetails($data->quot_date);
					$totroundval = preg_replace("/,/", "", $data->totalval);
					$dispval = (($totroundval * intval(isset($gettaxquery[0]->Tax)?$gettaxquery[0]->Tax:0))/100);
					$dispval_inv1 = $totroundval + $dispval;
					$grandtotal = number_format($dispval_inv1);
					$tax_amount= str_replace(',', '',$grandtotal);
						if ($inarraylist==1) {
							$invoice_total +=$tax_amount;
						}
						if (!empty($tax_amount)) {
							$data->totalval = (number_format($tax_amount));
						}else{
							$data->totalval ="";
						}
				} else{
					if ($inarraylist==1) {
						$invoice_total +=$invoice_amount;
					}
				}
				if ($inarraylist==1) {
					$userValue[$i]['totalval']=str_replace(',', '',$data->totalval);
					$i++;
				}
			}
			$userValue[$i]['id'] = $value->id;
			$userValue[$i]['payment_date'] = $value->payment_date;  
			$userValue[$i]['BankName'] = $value->BankName;
			$payment_amount= str_replace(',', '',$value->deposit_amount);
			$fee =str_replace(',', '',$value->bank_charge);
			$payment_fee_amount=$payment_amount + $fee;
			if (!empty($payment_fee_amount)) {
				$userValue[$i]['deposit_amount'] = (number_format($payment_fee_amount));
			}else{
				$userValue[$i]['deposit_amount'] ="";
			}
			if (!empty($fee)) {
				$userValue[$i]['bank_charge'] = (number_format($fee));
			}else{
				$userValue[$i]['bank_charge'] ="";
			}
				$payment_total +=$payment_fee_amount;
				$i++;
		}
			$balance_amount = (isset($invoice_total)?$invoice_total:0) - (isset($payment_total)?$payment_total:0);
			$datacount=(count($userValue)-1);
			return view('Payment.customerpaymentdetails',[
									'userValue' => $userValue,
									'datacount' => $datacount,
									'inv_query' => $inv_query,
									'balance_amount' => $balance_amount,
									'request' => $request]);

	}
	public static function specificationview(Request $request) {
		if (!isset($request->invoiceid) || !isset($request->payid)) {
			return Redirect::to('Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$g_prev_quey=array();
		$get_estimate_query = Payment::fnGetEstimateUserDatas($request);
		$getquery = Payment::fnGetEstimateUserDataEst($request);
		// print_r($getquery); exit;
		$get_customer_detail = Payment::fnGetCustomerDetailsView($getquery[0]->trading_destination_selection);
		$datemonth=$request->selYear."-".$request->selMonth;
		$gettaxquery =Estimation::fnGetTaxDetails($getquery[0]->quot_date);
		$invoice_quot_date = $get_estimate_query[0]->date_month;
		$get_tax_query = Payment::fnGetEstimateDtl($getquery[0]->estimate_id);
		$get_invoice_query = Payment::fnfetchInvoiceDetails($request, $invoice_quot_date, $getquery[0]->trading_destination_selection);
		// CHECK IF ALREADY DATA EXIST IN THIS MONTH
		$get_exist = Payment::fnCheckDataExistSingle($get_estimate_query[0]->company_name, $invoice_quot_date, $get_estimate_query[0]->created_datetime);
		// CHECK IF ANY PREVIOUS MONTH BALANCE
		$get_balance_invoice = Payment::fnfetchCheckDataBalanceInvoice($invoice_quot_date,$get_estimate_query[0]->company_name);
		$dispval_inv = 0;
		$grandtotal_inv = 0;
		$divtotal_inv = 0;
		foreach ($get_balance_invoice as $key => $value) {
			$getTaxquery = Payment::fnGetTaxDetails($value->quot_date);
				if (!empty($value->totalval)) {
							if ($value->tax == 1) {
								$totroundval = preg_replace("/,/", "", $value->totalval);
								$dispval_inv = (($totroundval * intval((isset($getTaxquery[0]->Tax)?$getTaxquery[0]->Tax:0)))/100);
								$dispval_inv1 = number_format($dispval_inv);
								$grandtotal_inv = $totroundval + $dispval_inv;
							} else {
								$totroundval = preg_replace("/,/", "", $value->totalval);
								$dispval_inv = 0;
								$grandtotal_inv = $totroundval + $dispval_inv;
								$dispval_inv1 = $dispval_inv;
							}
						}
			$divtotal_inv += $grandtotal_inv;
		}
		$ext_balance = Payment::fnfetchCheckDataBalancePayment($invoice_quot_date,$get_estimate_query[0]->company_name);
		$pre_balance = 0;
		$pre_date = array();
		if (isset($ext_balance)) {
			foreach ($ext_balance as $key => $value) {
				$pre_balance += preg_replace("/,/", "", $value->totalval);
				array_push($pre_date, $value->invoice_id);
			}
		}
		if (isset($pre_date)) {
			if (count($pre_date)>0) {
				$g_prev_quey = Payment::fnGetInvoiceDtl(reset($pre_date));
			}
		}
		$balance_invoice = $divtotal_inv - $pre_balance;
		$i = 0;
		$disp_record = array();
		if (!empty($balance_invoice)) {
			$disp_record[$i]['id'] = "";
			$disp_record[$i]['user_id'] = "";
			$disp_record[$i]['quot_date'] = isset($g_prev_quey[0]->payment_date) ? $g_prev_quey[0]->payment_date : "";
			$disp_record[$i]['company_name'] = "未払残高";
			$disp_record[$i]['payment_date'] = "";
			$disp_record[$i]['totalval'] = $balance_invoice;//$pre_balance;
			$disp_record[$i]['tax'] = 2;
			$i++;
		}
		foreach ($get_invoice_query as $key => $value) {
			$disp_record[$i]['id'] = $value->id;
			$disp_record[$i]['user_id'] = $value->user_id;
			$disp_record[$i]['quot_date'] = $value->quot_date;
			$disp_record[$i]['company_name'] = $value->company_name;
			$disp_record[$i]['payment_date'] = $value->payment_date;
			$disp_record[$i]['totalval'] = $value->totalval;
			$disp_record[$i]['tax'] = $value->tax;
			$i++;
		}
		if (count($get_exist)>0) {
			$checkboxdisabled = "disabled";
			foreach ($get_exist as $key => $value) {
				$disp_record[$i]['id'] = "";
				$disp_record[$i]['user_id'] = "";
				$disp_record[$i]['quot_date'] = $value->invoice_payment_date;
				$disp_record[$i]['company_name'] = "入金";
				$disp_record[$i]['payment_date'] = $value->payment_date;
				$disp_record[$i]['totalval'] = -$value->totalval;
				$disp_record[$i]['tax'] = 2;
				$i++;
			}
		}
			return view('Payment.paymentview',[
									'disp_record' => $disp_record,
									'get_estimate_query' => $get_estimate_query,
									'get_customer_detail' => $get_customer_detail,
									'request' => $request]);
	}
}