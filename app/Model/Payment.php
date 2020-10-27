<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Payment extends Model {
	//-----------Create Payment Start

	public static function fnGeneratePaymentID() {
		$result= DB::table('dev_payment_registration')
						->SELECT('user_id')
						->orderBy('user_id', 'DESC')
						->limit(1)
						->get();
		$cmn = "NYU";
		if (count($result) == 0) {
			$id = $cmn . "00001";
		} else {
			foreach ($result as $key => $value) {
				$g_id = intval(str_replace("NYU", "", $value->user_id)) + 1;
				$id = $cmn . substr("00000" . $g_id, -5);
			}
		}
		return $id;
	}
	public static function fnGetBankDetails($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbanks AS ban')
					->SELECT('mst.id', 'ban.id as banid', 'ban.BankName', 'mstbra.BranchName',
							'mst.AccNo', 'mstbra.Id as braid', DB::RAW("CONCAT(COALESCE(ban.BankName,''),'-',COALESCE(mst.AccNo,'')) AS CONBANKACC"))
					->Join('mstbankbranch AS mstbra', 'ban.id', '=', 'mstbra.BankId')
					->Join('mstbank AS mst', function($join) {
						$join->on('ban.id', '=', 'mst.BankName');
						$join->on('mst.BranchName', '=', 'mstbra.Id');
					})
					->WHERE('ban.delflg', 0)
					->WHERE('ban.location', 2)
					->get();
		return $query;
	}
	public static function fnGetInvoiceDtl($id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('del_flg', 0)
						->WHERE('id', $id)
						->get();
		return $result;
	}
	public static function fnGetEstimateDtl($id) {
		$result= DB::table('dev_estimate_registration')
						->SELECT('*')
						->WHERE('del_flg', 0)
						->WHERE('id', $id)
						->get();
		return $result;
	}
	public static function fnGetTaxDetails($date) {
		$result= DB::table('dev_taxdetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
						->WHERE('Startdate', '<=', $date)
						->orderBy('Startdate', 'DESC')
						->orderBy('Ins_TM', 'DESC')
						->limit(1)
						->get();
		return $result;
	}
	public static function fnGetInvoiceDetails($estimate_id, $oldbalance_from, $date, $companyid, $paid_status) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('del_flg', '=', 0)
						->WHERE('trading_destination_selection', '=', $companyid)
						->WHERE('quot_date','LIKE','%'.$date.'%');
			// if (Auth::user()->userclassification == 1) {
			// 	$accessDate = Auth::user()->accessDate;
			// 	$result=$result->WHERE(function($joincont) use($accessDate) {
   //              $joincont->WHERE('quot_date', '>', $accessDate)
   //                          ->ORWHERE('accessFlg','=',1);
   //              });
			// }
		$result=$result->orderBy('user_id', 'ASC')
						->orderBy('quot_date', 'ASC')
						->get();
		return $result;
	}
	public static function fnCheckDataExist($company_name,$oldbalance_from, $date_month) {
		$result = DB::TABLE(DB::raw("(SELECT (REPLACE(deposit_amount, ',', '') + REPLACE(bank_charge, ',', '')) totalval,payment_date,invoice_id,invoice_payment_date,paid_id,totalval as balance FROM dev_payment_registration WHERE del_flg = 0 AND company_name = '$company_name' AND date_month = '$date_month' ORDER BY created_datetime ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnCheckDataBalanceInvoice($oldbalance_from,$date, $company_name) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND ( quot_date >= '$to_date' OR accessFlg = 1 )";
		}
		// END ACCESS RIGHTS
		$result = DB::TABLE(DB::raw("(SELECT * FROM dev_invoices_registration WHERE del_flg = 0 AND company_name = '$company_name' AND SUBSTRING(quot_date,1,7) >'$oldbalance_from' AND SUBSTRING(quot_date,1,7) <'$date' $conditionAppend ORDER BY user_id ASC,quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnCheckDataBalance($oldbalance_from,$date, $company_name) {
		$result = DB::TABLE(DB::raw("(SELECT SUM(replace(deposit_amount, ',', '') + replace(bank_charge, ',', '')) totalval, invoice_id FROM (SELECT * FROM dev_payment_registration WHERE del_flg = 0 AND date_month > '$oldbalance_from' AND date_month < '$date' AND company_name = '$company_name' ORDER BY invoice_payment_date, created_datetime DESC) dev_payment_registration  GROUP BY date_month DESC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnInsertPayment($request,$paymentId) {
		$accessFlg = 0;
		$hididconcade = $request->hididconcade;
		$split = explode(",",$hididconcade);
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		for( $i=0;$i<count($split);$i++ ) {
			$id =$split[$i];
			$update=DB::table('dev_invoices_registration')
				->where('id', $id)
				->update(
					['bankid' => $request->bank_id,
					'bankbranchid' => $request->bankbranch_id,
					'acc_no' => $request->acc_no]
			);
		}
		$insert=DB::table('dev_payment_registration')->insert(
				['id' => '',
				'user_id' => $paymentId,
				'invoice_id' => $request->estimate_id,
				'payment_date' => $request->payment_date,
				'bankid' => $request->bank_id,
				'branchid' => $request->bankbranch_id,
				'deposit_amount' => $request->deposit_amount,
				'bank_charge' => $request->bank_charge,
				'remarks' => $request->remarks,
				'totalval' => $request->totalval,
				'del_flg' => 0,
				'accessFlg'	=>	$accessFlg,
				'created_by' => Auth::user()->username,
				'created_datetime' => date('Y-m-d H-i-s'),
				'updated_by' => Auth::user()->username,
				'updated_datetime' => date('Y-m-d H-i-s'),
				'company_name' => $request->company_name,
				'billing_date' => $request->quot_date,
				'project_name' => $request->project_name,
				'invoice_payment_date' => $request->invoice_payment_date,
				'date_month' => $request->date_month,
				'paid_id' => $request->hididconcade,
				'acc_no' => $request->bankname_sel_pay]
		);
		return $insert;
	}
	public static function fnUpdatePaidInvoice($request,$id,$pay_yrmon) {
		$update=DB::table('dev_invoices_registration')
			->where('id', $id)
			->update(
				['paid_status' => 1,
				'paid_yearmonth' => $pay_yrmon,
				'paid_date' => $request->payment_date]
		);
		return $update;
	}
	public static function fnGetBankAccountDetails($bankid) {
		$sql = "SELECT mstbra.BranchName as Branch,ban.* FROM mstbank ban JOIN mstbankbranch mstbra 
					ON ban.BranchName = mstbra.id WHERE ban.delflg = 0 AND ban.id = '$bankid' LIMIT 1";
		$cards = DB::select($sql);
		return $cards;
	}
	//----------------------------------------------------------------------------
	public static function fnGetAccountPeriod($request) {
		$result= DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
						->get();
		return $result;
	}
	public static function fnGetEstimateRecord($from_date, $to_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$from_date = Auth::user()->accessDate;
		}
		// END ACCESS RIGHTS
		$query=DB::TABLE(DB::raw("(SELECT SUBSTRING(payment_date, 1, 7) AS invoice_payment_date,payment_date FROM dev_invoices_registration WHERE del_flg = 0 AND (payment_date > '$from_date' AND payment_date < '$to_date') ORDER BY payment_date ASC) as tb1"))
					->get();
			return $query;
	}
	public static function fnGetEstimateRecordPrevious($from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND payment_date >= '$to_date'";
		}
		// END ACCESS RIGHTS
		$result=DB::TABLE(DB::raw("(SELECT SUBSTRING(payment_date, 1, 7) AS invoice_payment_date FROM dev_invoices_registration WHERE del_flg = 0 AND (payment_date <= '$from_date' $conditionAppend) ORDER BY payment_date ASC) as tb1"))
					->get();
			return $result;
	}
	public static function fnGetEstimateRecordNext($to_date) {
		$query=DB::TABLE(DB::raw("(SELECT SUBSTRING(payment_date, 1, 7) AS invoice_payment_date FROM dev_invoices_registration WHERE del_flg = 0 AND (payment_date >= '$to_date') ORDER BY payment_date ASC) as tb1"))
					->get();
			return $query;
	}
	public static function fnfetchfordisplay($request,$intfrom,$intto) {
		$query="select invoice.id FROM `dev_invoices_registration` as `invoice` WHERE `invoice`.`payment_date` 
										BETWEEN '$intfrom' AND '$intto'";
			$cards = DB::select($query);
			return $cards;
	}
	public static function fntocheckpayment($request,$upvalue) {
		
		$result=DB::TABLE(DB::raw("(SELECT invoice.id,
									`invoice`.`user_id` as `invoiceno`, 
									invoice.tax,
									invoice.company_name AS clientName,
									invoice.payment_date AS invpaymentdate,
									invoice.quot_date,
									invoice.paid_status,
									invoice.id as invoiceid,
									invoice.trading_destination_selection AS clientnumber,
									FORMAT(IF(invoice.tax=1,
										((REPLACE(invoice.totalval,',','')*
											(SELECT Tax FROM dev_taxdetails WHERE delflg = 0 AND Startdate <= invoice.quot_date ORDER BY Startdate DESC, Ins_TM DESC LIMIT 1 ))/100)+
										REPLACE(invoice.totalval,',',''),
									REPLACE(invoice.totalval,',','')),0) AS TOTALVALUE
									FROM `dev_invoices_registration` as `invoice` 
									WHERE `invoice`.`id`='$upvalue') as tb1"))
							->get();
		return $result;
	}
	public static function fnGetPaymentDetails($request,$intfrom,$intto) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = " WHERE (accessRights > '$accessDate' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		// $result= DB::TABLE('dev_payment_registration AS payment')
		// 				->SELECT('payment.*',
		// 						'invoice.user_id as invoiceno',
		// 						'invoice.totalval as totalvalue',
		// 						'invoice.trading_destination_selection AS clientnumber',
		// 						'invoice.payment_date',
		// 						'invoice.quot_date',
		// 						'invoice.tax as TAX', 
		// 						'invoice.company_name AS clientName')
		// 				->RIGHTJOIN('dev_invoices_registration as invoice', function ($joins){
  //                                         $joins->on('invoice.id', '=', 'payment.invoice_id')
  //                                         ->where('payment.del_flg','=', 0);
  //                                   })
		// 				->WHEREBETWEEN('invoice.payment_date', [$intfrom, $intto])
		// 				->orderBy('invoice.payment_date', $request->sortOrder)
		// 				->paginate($request->plimit);
						// ->GET();
						// ->tosql();
						// dd($result);
		$result=DB::statement(DB::raw("SET @balances = 0"));
		
		$result=DB::TABLE(DB::raw("(SELECT * FROM(select *,
										IF(CHK=0,@balances :=0,@balances :=@balances) AS assign,
											(@balances := @balances + REPLACE(IF(paymentamount IS NULL,0,paymentamount),',','')) as Balances,
											(combinedpay-@balances) AS JB,
										IF(combinedpay-@balances<0 OR combinedpay-@balances IS NULL,IF(combinedpay-@balances<0, FORMAT(abs(combinedpay-@balances+IF(BCtotal IS NULL,0,BCtotal)),0) ,TOTALVALUE) ,0) AS Debitval,
										IF((combinedpay-@balances>0 OR combinedpay-@balances=0),
											TOTALVALUE,
										IF(combinedpay-@balances<0, FORMAT(REPLACE(paymentamount,',','')+(combinedpay-@balances),0), FORMAT(abs(combinedpay-@balances),0) )
											) AS Creditval
										from (select invoice.trading_destination_selection AS custid,`invoice`.`user_id` as `invoiceno`, 
													invoice.id AS invid,
													invoice.tax, 
													invoice.payment_date AS accessRights,
													invoice.company_name AS clientName, 
													IF(invoice.paid_status=1,
														(SELECT pay.payment_date FROM dev_payment_registration AS pay WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1),invoice.payment_date) AS invpaymentdate,
													invoice.paid_status,
													payment.payment_date AS paymentdate, 
													payment.paid_id AS payinvid,
													(SELECT pay.paid_id FROM dev_payment_registration AS pay WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1) AS payidtotal,
													invoice.quot_date,
													invoice.trading_destination_selection AS clientnumber,
										FORMAT(IF(invoice.tax=1, ((REPLACE(invoice.totalval,',','')* (SELECT Tax FROM dev_taxdetails WHERE delflg = 0 AND Startdate <= invoice.quot_date 
										ORDER BY Startdate DESC, Ins_TM DESC LIMIT 1 ))/100)+ REPLACE(invoice.totalval,',',''), REPLACE(invoice.totalval,',','')),0) AS TOTALVALUE, 
										IF(payment.id IS NULL, REPLACE(`invoice`.`totalval`,',',''), 
											REPLACE(payment.deposit_amount,',','') + REPLACE(payment.bank_charge,',','') + REPLACE(payment.totalval,',','')) AS Gtotal, IF(payment.id IS NULL, 
											REPLACE(`invoice`.`totalval`,',',''), REPLACE(payment.totalval,',','')) AS Dtotal,
										(SELECT REPLACE(pay.deposit_amount,',','') FROM dev_payment_registration AS pay WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1) AS combinedpay,
										(SELECT REPLACE(bank_charge,',','') FROM dev_payment_registration AS pay WHERE 
											LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1) AS BCtotal,
										(SELECT TOTALVALUE FROM dev_payment_registration AS pay WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1) AS paymentamount,
										IF( LOCATE(CONCAT(',', invoice.id ,','),
											CONCAT(',',(SELECT pay.paid_id FROM dev_payment_registration AS pay 
									WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1),',')) AND locate(',',(SELECT pay.paid_id FROM dev_payment_registration AS pay 
								WHERE LOCATE(CONCAT(',', invid ,','),CONCAT(',',pay.paid_id,',')) LIMIT 1))>0 , 1, 0) AS CHK,
								payment.* FROM `dev_payment_registration` as `payment` 
								RIGHT JOIN `dev_invoices_registration` as `invoice` ON `invoice`.`id` = `payment`.`invoice_id` 
								AND `payment`.`del_flg` = 0 
								WHERE `invoice`.`payment_date` 
								BETWEEN '$intfrom' AND '$intto') as tb1 
								order by `custid` asc, payidtotal asc) AS sub 
								$conditionAppend) as tb1"))
						->get();
						// ->orderBy('invpaymentdate', $request->sortOrder)
						// ->toSql();
						// print_r($result);exit;
			return $result;
	}
	public static function fnGetPaymentTotalValue($datemonth) {
		$query=DB::TABLE(DB::raw("(SELECT SUM(REPLACE(totalval, ',', '')) totalval FROM dev_payment_registration WHERE invoice_payment_date LIKE '$datemonth%') as tb1"))
					->get();
			return $query;
	}
	public static function fngetpreviousdata($request,$previousfrom,$previousto) {
		$result= DB::TABLE('dev_payment_registration AS payment')
						->SELECT(
								'invoice.totalval as totalvalue'
								)
						->RIGHTJOIN('dev_invoices_registration as invoice', function ($joins){
                                          $joins->on('invoice.id', '=', 'payment.invoice_id')
                                          ->where('payment.del_flg','=', 0);
                                    })
						->WHEREBETWEEN('invoice.payment_date', [$previousfrom, $previousto])
						->orderBy($request->paymentsort, $request->sortOrder)
						// ->paginate($request->plimit);
						->GET();
						// ->tosql();
						// dd($result);

	}
	public static function fnGETtotalAmount($request,$premonth,$intervalto,$clientnumb) {
		$query="SELECT FORMAT(SUM(REPLACE(inv_totalval,',','')-REPLACE(payment_total,',','')),0) AS Ctotal,
								payremainingamount,
								payment_total,
								inv_totalval 
						FROM(
							select `invoice`.`tax`,
								   `invoice`.`quot_date`, 
								IF(FORMAT(SUM(REPLACE(payment.totalval,',','')),0) IS NULL, 0, 
									FORMAT(SUM(REPLACE(payment.totalval,',','')),0)) AS payremainingamount,
								IF(FORMAT(SUM(REPLACE(payment.deposit_amount,',',''))+SUM(REPLACE(payment.bank_charge,',','')),0) IS NULL, 0,
									FORMAT(SUM(REPLACE(payment.deposit_amount,',',''))+SUM(REPLACE(payment.bank_charge,',','')),0)) AS payment_total,
								(SELECT FORMAT(SUM(IF(sub_inv.tax=1,
									((REPLACE(sub_inv.totalval,',','')*
									(SELECT Tax FROM dev_taxdetails WHERE delflg = 0 AND Startdate <= sub_inv.quot_date ORDER BY Startdate DESC, Ins_TM DESC LIMIT 1 ))/100)+
									REPLACE(sub_inv.totalval,',',''),
									REPLACE(sub_inv.totalval,',',''))),0) FROM dev_invoices_registration AS sub_inv
								left join `dev_payment_registration` as `sub_pay` on find_in_set(sub_inv.id,sub_pay.paid_id) 
								and `sub_pay`.`del_flg` = 0 WHERE `sub_inv`.`trading_destination_selection` = '$clientnumb'
								and `sub_inv`.`payment_date` between '$premonth' and '$intervalto'
							 	)  AS inv_totalval
							  from `dev_payment_registration` as `payment` 
						left join `dev_invoices_registration` as `invoice` 
						on `invoice`.`id` = `payment`.`invoice_id` 
						and `payment`.`del_flg` = 0 
						WHERE `invoice`.`trading_destination_selection` = '$clientnumb'
							and `invoice`.`payment_date` 
							between '$premonth' and '$intervalto') AS oldbalance";
			$cards = DB::select($query);
			return $cards;
	}
	public static function fnGetPaymentAllTotal($request,$intfrom,$intto) {
		$query=DB::select(DB::raw("SELECT
									FORMAT(SUM(REPLACE(bank_charge,',','')),0) AS BCtotal,
									FORMAT(SUM(REPLACE(TOTALVALUE,',',''))-IF(SUM(REPLACE(deposit_amount,',','')) IS NULL,0, SUM(REPLACE(deposit_amount,',','')))-IF(SUM(REPLACE(bank_charge,',','')) IS NULL,0, SUM(REPLACE(bank_charge,',',''))),0) AS Dtotal,
									FORMAT(SUM(REPLACE(deposit_amount,',','')),0) AS Ctotal, 
									FORMAT(SUM(REPLACE(TOTALVALUE,',','')),0) AS Gtotal
								FROM(select payment.*, 
									`invoice`.`user_id` as `INVOICENUMBER`,
									`invoice`.`tax`,
									`invoice`.`quot_date`,
									FORMAT(IF(invoice.tax=1,
												((REPLACE(invoice.totalval,',','')*
													(SELECT Tax FROM dev_taxdetails WHERE delflg = 0 AND Startdate <= invoice.quot_date ORDER BY Startdate DESC, Ins_TM DESC LIMIT 1 ))/100)+
												REPLACE(invoice.totalval,',',''),
												REPLACE(invoice.totalval,',','')),0) AS TOTALVALUE,
									IF(payment.id IS NULL, 
										REPLACE(`invoice`.`totalval`,',',''), 
										REPLACE(payment.deposit_amount,',','') + REPLACE(payment.bank_charge,',','') + REPLACE(payment.totalval,',','')) AS Gtotal,
									IF(payment.id IS NULL, 
										REPLACE(`invoice`.`totalval`,',',''), 
										REPLACE(payment.totalval,',','')) AS Dtotal
									FROM `dev_payment_registration` as `payment` 
									RIGHT JOIN `dev_invoices_registration` as `invoice` 
										ON `invoice`.`id` = `payment`.`invoice_id` 
										AND `payment`.`del_flg` = 0 
									WHERE `invoice`.`payment_date` 
									BETWEEN '$intfrom' AND '$intto' ORDER BY `payment_date` ASC) AS pay"));
		return $query;
	}
	public static function fnGetEstimateUserData($id) {
		$result= DB::table('dev_payment_registration')
						->SELECT('*')
						->WHERE('del_flg', '=', 0)
						->WHERE('id', '=', $id)
						->limit(1)
						->get();
		return $result;
		
	}
	public static function fnUpdatePayment($request) {
		$hididconcade = $request->hididconcade;
		$split = explode(",",$hididconcade);
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		for( $i=0;$i<count($split);$i++ ) {
			$id =$split[$i];
			$update=DB::table('dev_invoices_registration')
				->where('id', $id)
				->update(
					['bankid' => $request->bank_id,
					'bankbranchid' => $request->bankbranch_id,
					'acc_no' => $request->acc_no]
			);
		}
		$update=DB::table('dev_payment_registration')
				->where('id', $request->estimate_id)
				->update(
					['acc_no' => $request->bankname_sel_pay,
					'payment_date' => $request->payment_date,
					'bankid' => $request->bank_id,
					'branchid' => $request->bankbranch_id,
					'deposit_amount' => $request->deposit_amount,
					'bank_charge' => $request->bank_charge,
					'remarks' => $request->remarks,
					'totalval' => $request->totalval,
					'updated_by' => Auth::user()->username,
					'updated_datetime' => date('Y-m-d H-i-s'),
					'del_flg' => 0]
			);
		if (Auth::user()->userclassification == 4) {
					$update=DB::table('dev_payment_registration')
						->where('id', $request->estimate_id)
						->update(['accessFlg'	=>	$accessFlg]);
				}
		return $update;
	}
	public static function fnPaymentlistview($request) {
		$query= DB::table('dev_payment_registration')
						->SELECT('mstbanks.id',
								 'dev_payment_registration.deposit_amount',
								 'dev_payment_registration.bank_charge',
								 'dev_payment_registration.payment_date',
								 'dev_payment_registration.paid_id',
								 'mstbanks.BankName'
								)
						->leftjoin('mstbanks', 'dev_payment_registration.bankid', '=', 'mstbanks.id');
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query=$query->WHERE(function($joincont) use($accessDate) {
                $joincont->WHERE('payment_date', '>', $accessDate)
                            ->ORWHERE('accessFlg','=',1);
                });
			}
			// END ACCESS RIGHTS
			$query = $query->where('dev_payment_registration.company_name',$request->companyname)
						->orderBy('dev_payment_registration.payment_date', 'ASC')
						->paginate($request->plimit);
						// ->toSql();
						// print_r($query); exit;
			return $query;
	}
	public static function fninvoicelistview($request,$companynameClick,$splitval) {
		$result=DB::TABLE(DB::raw("(SELECT id,user_id,company_name,project_name,bankid,totalval,quot_date,tax 
				FROM dev_invoices_registration where  id IN ($splitval)
				 ORDER BY quot_date ASC) as tb1"))
						->paginate($request->plimit);
		return $result;
	}
	public static function fnGetEstimateUserDatas($request) {
			$result= DB::table('dev_payment_registration')
						->SELECT('*')
						->WHERE('del_flg', 0)
						->WHERE('invoice_id', $request->invoiceid)
						->get();
		return $result;
	}
	public static function fnGetEstimateUserDataEst($request) {
			$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('del_flg', 0)
						->WHERE('id', $request->invoiceid)
						->get();
		return $result;
	}
	public static function fnGetCustomerDetailsView($tradingselection) {
			$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('delFlg', 0)
						->WHERE('id', $tradingselection)
						->get();
		return $result;
	}
	public static function fnfetchInvoiceDetails($request,$invoice_quot_date,$tradingselection) {
			$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('del_flg', 0)
						->WHERE('trading_destination_selection', $tradingselection)
						->WHERE('quot_date','LIKE','%'.$invoice_quot_date.'%');
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$result=$result->WHERE(function($joincont) use($accessDate,$invoice_quot_date) {
                $joincont->WHERE('quot_date', '>', $accessDate)
                            ->ORWHERE('accessFlg','=',1);
                });
		}
			$result=$result->orderBy('user_id', 'ASC')
						->orderBy('quot_date', 'ASC')
						->get();
		return $result;
	}
	public static function fnCheckDataExistSingle($getcompany_name, 
												$invoice_quot_date, $created_datetime) {
			$result="SELECT (REPLACE(deposit_amount, ',', '') + REPLACE(bank_charge, ',', '')) totalval,payment_date,invoice_id,invoice_payment_date FROM dev_payment_registration WHERE del_flg = 0 AND company_name = '$getcompany_name' AND date_month = '$invoice_quot_date' 
					AND created_datetime <= '$created_datetime' 
					ORDER BY created_datetime ASC";
			$cards = DB::select($result);
			return $cards;
	}
	public static function fnfetchCheckDataBalanceInvoice($invoice_quot_date,$getcompany_name) {
			$result="SELECT * FROM dev_invoices_registration WHERE del_flg = 0 AND company_name = '$getcompany_name' AND /*payment_date <*/
				SUBSTRING(quot_date,1,7) <'$invoice_quot_date' ORDER BY user_id ASC,quot_date ASC";
			$cards = DB::select($result);
			return $cards;
	}
	public static function fnfetchCheckDataBalancePayment($invoice_quot_date,$getcompany_name) {
			$result="SELECT SUM(replace(deposit_amount, ',', '') + 
						replace(bank_charge, ',', '')) totalval, invoice_id FROM 
						(SELECT * FROM dev_payment_registration WHERE del_flg = 0 AND date_month < '$invoice_quot_date' AND 
						company_name = '$getcompany_name' ORDER BY invoice_payment_date, created_datetime DESC)
						dev_payment_registration  GROUP BY date_month DESC";
			$cards = DB::select($result);
			return $cards;
	}	
}
?>