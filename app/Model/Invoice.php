<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Invoice extends Model {
	public static function fnGetPaymentCheck($request) {
		$result= DB::table('dev_payment_registration')
						->SELECT('*')
						->WHERE('del_flg', '=', 0)
						->get();
		return $result;
	}
	public static function fnUpdateInvoice($request) {
		$month=0;
		$update=DB::table('dev_invoices_registration')
            ->where('del_flg', $month)
            ->update(['paid_status' => 0]);
    	return $update;
	}
	public static function updateClassification($request) {
		$data[] =   [
			'classification' => $request->invoicestatus
		];
		$update=DB::table('dev_invoices_registration')->where('id', $request->invoicestatusid)->update($data[0]);
		return $update;
	}
	public static function fnGetEstimateRecordPrevious($from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND ( quot_date >= '$to_date' OR accessFlg = 1 )";
		}
		// END ACCESS RIGHTS
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date <= '$from_date' $conditionAppend) ORDER BY quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnGetEstimateRecordNext($to_date) {
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date >= '$to_date') ORDER BY quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnGetinvoiceTotalValue($request,$taxSearch,$date_month,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate,$filter) {
		if ($request->searchmethod == 1 || $request->searchmethod == 2 || $request->searchmethod == 3) {
			$filter="";
		}
		if (!empty($request->searchmethod)) {
					$wherecondition = "";
					$Estimate = db::table('dev_invoices_registration')
									->select('dev_invoices_registration.*',
										DB::raw("(CASE 
        										WHEN dev_invoices_registration.classification = 2 THEN 3
        										ELSE 0
    											END) AS orderbysent"),
										'dev_estimatesetting.ProjectType AS ProjectType')
									->leftJoin('dev_estimatesetting' , 'dev_estimatesetting.id' ,'=','dev_invoices_registration.project_type_selection')
									->WHERE('del_flg',0);
					if ($filter == "2") {
						$Estimate = $Estimate->where('dev_invoices_registration.classification',0)
											 ->where('dev_invoices_registration.quot_date', 'LIKE', '%' . 
											 	$date_month . '%');
					} else if ($filter == "3") {
						$Estimate = $Estimate->where('dev_invoices_registration.classification',1)
											 ->where('dev_invoices_registration.quot_date', 'LIKE', '%' . 
											 	$date_month . '%');
					} else if ($filter == "4") {
						$Estimate = $Estimate->where('dev_invoices_registration.classification',3)
											 ->where('dev_invoices_registration.quot_date', 'LIKE', '%' . 
											 	$date_month . '%');
					} else if ($filter == "5") {
						$Estimate = $Estimate->where('dev_invoices_registration.classification',2)
											 ->where('dev_invoices_registration.quot_date', 'LIKE', '%' . 
											 	$date_month . '%');
					} else if ($filter == "1") {
						$Estimate = $Estimate->where('dev_invoices_registration.quot_date', 'LIKE', '%' . 
											 	$date_month . '%');
					}
					if ($request->searchmethod == 3) {
		          	if (!empty($request->companynameClick)) {
   							$Estimate = $Estimate->where('dev_invoices_registration.company_name','LIKE','%'.$request->companynameClick.'%');
						}
		          	}
					if (!empty($estimateno)) {
						$Estimate = $Estimate->where('dev_invoices_registration.user_id','LIKE','%'.$estimateno.'%');
					}
					// if ($companyname) {
					// 	$Estimate = $Estimate->where('dev_invoices_registration.company_name','LIKE','%'.$companyname.'%');
					// }
					if (!empty($startdate) && !empty($enddate)) {
						$Estimate = $Estimate->where('dev_invoices_registration.quot_date','>=',$startdate);
						$Estimate = $Estimate->where('dev_invoices_registration.quot_date','<=',$enddate);
					}
					if (!empty($startdate) && empty($enddate)) {
						$Estimate = $Estimate->where('dev_invoices_registration.quot_date','>=',$startdate);
					}
					if (empty($startdate) &&!empty($enddate)) {
						$Estimate = $Estimate->where('dev_invoices_registration.quot_date','<=',$enddate);
					}
					if ($request->searchmethod == 1) {
          				$Estimate = $Estimate->where(function($joincont) use ($request) {
                                    $joincont->where('dev_invoices_registration.user_id', 'LIKE', '%' . trim($request->singlesearch) . '%')
                                    ->orWhere('dev_invoices_registration.company_name', 'LIKE', '%' . trim($request->singlesearch) . '%')
                                    ->orWhere('dev_invoices_registration.project_name', 'LIKE', '%' . trim($request->singlesearch) . '%');
                                    });
   					}
   					if ($request->searchmethod == 2) {
   						if (!empty($request->msearchusercode)) {
   							$Estimate = $Estimate->where('dev_invoices_registration.user_id', 'LIKE', '%' . trim($request->msearchusercode) . '%');
						}
						if (!empty($request->msearchcustomer)) {
   							$Estimate = $Estimate->where('dev_invoices_registration.company_name', 'LIKE', '%' . trim($request->msearchcustomer) . '%');
						}
						if(!empty($request->msearchstdate) && !empty($request->msearcheddate)) {
		            $Estimate = $Estimate->whereBetween('dev_invoices_registration.quot_date', [$request->msearchstdate, $request->msearcheddate]);
		          }
	          	if(!empty($request->msearchstdate) && empty($request->msearcheddate)) {
		             $Estimate = $Estimate->where(function($joincont) use ($request) {
                            $joincont->where('dev_invoices_registration.quot_date', '>=', $request->msearchstdate);
                                     // ->where(DB::raw('curdate()'), '<=', $request->msearchstdate);
                            });
		          }
		          if(!empty($request->msearcheddate) && empty($request->msearchstdate)) {
		              $Estimate = $Estimate->where('dev_invoices_registration.quot_date', '<=', $request->msearcheddate);
	         	}
            	if ($taxSearch == 999 && !empty($request->protype1)) {
		                $Estimate = $Estimate->where(function($joincont) use ($request) {
		                                      $joincont->where('dev_invoices_registration.project_type_selection', 'NOT LIKE', '%' . $request->protype1 . '%');
		                                      });
            	}
            	if ($taxSearch != 999 && !empty($request->protype1)) {
            			if ($request->protype1==1) {
            				$request->protype1="";
            			}
		                $Estimate = $Estimate->where(function($joincont) use ($request) {
		                                      $joincont->where('dev_invoices_registration.project_type_selection', 'LIKE', '%' . $request->protype1 . '%');
		                                      });
            			}
   				}
   				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate=$Estimate->WHERE(function($joincont) use($accessDate) {
                           $joincont->WHERE('dev_invoices_registration.quot_date', '>', 
                           						$accessDate)
                            		->ORWHERE('accessFlg','=',1);
                            });
				}
				// END ACCESS RIGHTS
				if ($request->checkdefault != 1) {
					$Estimate = $Estimate->orderByRaw("orderbysent ASC, user_id DESC")
						  	->paginate($request->plimit);
				} else {
					$Estimate = $Estimate->orderBy($request->invoicesort, $request->sortOrder)
						  	->paginate($request->plimit);

				}
   							// ->toSql();
   							// dd($Estimate);
		} else {
			// $Estimate = db::table('dev_invoices_registration')
			// 						->select('dev_invoices_registration.*',
			// 							DB::raw("(CASE 
   //      										WHEN dev_invoices_registration.classification = 2 THEN 3
   //      										ELSE 0
   //  											END) AS orderbysent"),
			// 							'dev_estimatesetting.ProjectType AS ProjectType',
			// 							DB::raw("(SELECT format(SUM(REPLACE(amount, ',', '')),0) FROM tbl_work_amount_details WHERE invoice_id = dev_invoices_registration.user_id) AS totalval"))
			// 						->leftJoin('dev_estimatesetting' , 'dev_estimatesetting.id' ,'=','dev_invoices_registration.project_type_selection')
			// 						->WHERE('dev_invoices_registration.quot_date','LIKE','%'.$date_month.'%')
			// 						->WHERE('del_flg',0);
				$db = DB::connection('mysql');
		$Estimate = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,main.trading_destination_selection,main.payment_date,main.del_flg,main.copyFlg,main.project_name,main.classification,
main.created_by,main.pdf_flg,main.project_type_selection,main.mailFlg, 
main.paid_date,main.paid_status,main.tax,main.estimate_id,main.company_name,main.bankid,main.bankbranchid,main.acc_no,works.amount,
works.work_specific,works.quantity,works.unit_price,works.remarks,works.emp_id,(CASE 
        WHEN main.classification = 2 THEN 3
        ELSE 0
    END) AS orderbysent,`dev_estimatesetting`.`ProjectType`,main.totalval 
FROM   tbl_work_amount_details works 
left join dev_invoices_registration main on works .invoice_id = main .user_id 
left join dev_estimatesetting on dev_estimatesetting.id = main.project_type_selection
WHERE main.del_flg = 0 AND main.quot_date LIKE '%$date_month%'
GROUP BY user_id Order By user_id Asc,quot_date Asc
			) AS DDD "));
   				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate=$Estimate->WHERE(function($joincont) use($accessDate) {
                           $joincont->WHERE('dev_invoices_registration.quot_date', '>', 
                           						$accessDate)
                            		->ORWHERE('accessFlg','=',1);
                            });
				}
				// END ACCESS RIGHTS
			if ($request->checkdefault != 1) {
				$Estimate = $Estimate->orderByRaw("orderbysent ASC, user_id DESC")
					  	 ->paginate($request->plimit);
				//->toSql();dd($Estimate);
			} else {
				$Estimate = $Estimate->orderBy($request->invoicesort, $request->sortOrder)
					  	->paginate($request->plimit);

			}
		}
		return $Estimate;

	}
	public static function fnGetBalanceDetails($invid) {
		$db=DB::connection('mysql');
		$query=$db->TABLE($db->raw("(SELECT invoice_id,id,totalval,paid_id,
						(SELECT SUM(replace(deposit_amount, ',', '')) 
						FROM dev_payment_registration WHERE invoice_id = $invid) 
						as deposit_amount FROM dev_payment_registration 
						WHERE invoice_id = $invid ORDER BY id DESC) as tb1"))
					->get();
		return $query;
	}
	public static function Fntogetprojecttype($request) {
		$certificateName = DB::TABLE('dev_estimatesetting')
	    						->SELECT('*')
	    						->WHERE('delFlg', '=', 0)
                                ->lists('ProjectType','id');
      	return $certificateName;
	}
	public static function fnGetInvoicePdfDetail($request){
		$certificateName = DB::TABLE('dev_invoices_registration')
	    						->SELECT('*')
	    						->WHERE('user_id', $request->userid)
	    						->WHERE('del_flg', 0)
                                ->get();
      	return $certificateName;
	}
	public static function fnGetEstDetail($estimateid){
			$certificateName = DB::TABLE('dev_estimate_registration')
	    						->SELECT('*')
	    						->WHERE('id', $estimateid)
                                ->get();
      	return $certificateName;	
	}
	public static function fnGetEstimateinvoiceData($request,$estimateid) {
		$db=DB::connection('mysql');
		$estimate_id=$estimateid;
		$query=$db->TABLE($db->raw("(SELECT dev_estimate_registration.*,mst_customerdetail.customer_id FROM dev_estimate_registration
	LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_estimate_registration.trading_destination_selection 
	WHERE dev_estimate_registration.id = '$estimate_id') as tb1"))
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}
	public static function fnGetEstimateUserData($request) {
		$db=DB::connection('mysql');
		$estimate_id=$request->invoiceid;
		// OLD LEFT JOIN mst_customerdetail ON mst_customerdetail.customer_name=dev_estimate_registration.company_name 
	
		$query=$db->TABLE($db->raw("(SELECT dev_estimate_registration.id,dev_estimate_registration.trading_selection,dev_estimate_registration.trading_destination_selection,dev_estimate_registration.company_name,dev_estimate_registration.branch_selection,dev_estimate_registration.project_personal,dev_estimate_registration.project_name,dev_estimate_registration.tax,dev_estimate_registration.project_type_selection,dev_estimate_registration.tighten_month_selection,dev_estimate_registration.cutoff_date_selection,dev_estimate_registration.quot_date,dev_estimate_registration.billing_month_selection,dev_estimate_registration.billing_date_selection,dev_estimate_registration.special_ins1,dev_estimate_registration.special_ins2,dev_estimate_registration.special_ins3,dev_estimate_registration.special_ins4,dev_estimate_registration.special_ins5,dev_estimate_registration.del_flg,dev_estimate_registration.classification,dev_estimate_registration.memo,dev_estimate_registration.accessflg,dev_estimate_registration.totalval,mst_customerdetail.customer_id FROM dev_estimate_registration
	LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_estimate_registration.trading_destination_selection 
	WHERE dev_estimate_registration.id = '$estimate_id') as tb1"))
					->get();
					// ->toSql();
					// dd($query);
		return $query;
	}

	public static function fnGetinvoiceUserDataADD($request){
		$db=DB::connection('mysql');
		$estimate_id=$request->invoiceid;
		$query=$db->TABLE($db->raw("(SELECT dev_invoices_registration.id,
																user_id,
																estimate_id,
																trading_selection,
																trading_destination_selection,
																company_name,
																branch_selection,
																project_personal,
																project_name,
																tax,
																project_type_selection,
																tighten_month_selection,
																cutoff_date_selection,
																quot_date,
																billing_month_selection,
																billing_date_selection,
																totalval,
																special_ins1,
																special_ins2,
																special_ins3,
																special_ins4,
																special_ins5,
																bankid,
																bankbranchid,
																acc_no,
																payment_date,
																personnel_mark,
																approver_mark,
																company_sign,
																imprint,
																paid_status,
																pdf_flg,
																mailFlg,
																accessFlg,
																pdf_name,
																classification,
																paid_date,
																paid_yearmonth,
																memo,
																copyFlg,
														        mst_customerdetail.customer_id FROM 
																dev_invoices_registration
															LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_invoices_registration.trading_destination_selection 
															WHERE  dev_invoices_registration.id = '$estimate_id') as tb1"))
							->get();
					// ->toSql();
					// dd($query);
		return $query;
	}

	public static function fnGetinvoiceUserData($request){
		$db=DB::connection('mysql');
		$estimate_id=$request->invoiceid;
		$query=$db->TABLE($db->raw("(SELECT dev_invoices_registration.id,
																user_id,
																estimate_id,
																trading_selection,
																trading_destination_selection,
																company_name,
																branch_selection,
																project_personal,
																project_name,
																tax,
																project_type_selection,
																tighten_month_selection,
																cutoff_date_selection,
																quot_date,
																billing_month_selection,
																billing_date_selection,
																totalval,
																special_ins1,
																special_ins2,
																special_ins3,
																special_ins4,
																special_ins5,
																bankid,
																bankbranchid,
																acc_no,
																payment_date,
																personnel_mark,
																approver_mark,
																company_sign,
																imprint,
																paid_status,
																pdf_flg,
																mailFlg,
																accessFlg,
																pdf_name,
																classification,
																paid_date,
																paid_yearmonth,
																memo,
																copyFlg,
																tbl_work_amount_details.inv_primery_key_id,
																						work_specific,
																						quantity,
																						amount,unit_price,
																						remarks,
																						emp_id,
																						mst_customerdetail.customer_id FROM 
																dev_invoices_registration
															LEFT JOIN tbl_work_amount_details ON tbl_work_amount_details.inv_primery_key_id=dev_invoices_registration.id
															LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_invoices_registration.trading_destination_selection 
															WHERE dev_invoices_registration.id = '$estimate_id') as tb1"))
							->get();
					// ->toSql();
					// dd($query);
		return $query;
	}

	public static function fnGetinvoiceUserDataForLoop($request) {
		$query = DB::TABLE('tbl_work_amount_details')
						->SELECT('*')
						->WHERE('tbl_work_amount_details.inv_primery_key_id', $request->invoiceid)
						->get();
						// ->toSql();
					// dd($query);
		return $query;	
	}

	public static function fnGetEstimateUserDataForLoop($request) {
		$query = DB::TABLE('tbl_estimate_work_details')
						->SELECT('*')
						->WHERE('tbl_estimate_work_details.est_primary_key_id', $request->invoiceid)
						->get();
		return $query;	
	}
	// TO SET THE BANK ID
	public static function fnGetSelectedDetails($invoicedata) {
		$query = DB::TABLE('mstbank')
						->SELECT('mstbank.id')
						->WHERE('mstbank.BankName', $invoicedata[0]->bankid)
						->WHERE('mstbank.BranchName', $invoicedata[0]->bankbranchid)
						->WHERE('mstbank.AccNo', $invoicedata[0]->acc_no)
						->get();
		return $query;	
	}
	public static function fnGetBankDetails($request) {
		$estimate_id=$request->invoiceid;
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbanks AS ban')
						->SELECT('ban.id', 'ban.BankName')
						->Join('mstbank AS mst', 'ban.id', '=', 'mst.BankName')
						->WHERE('ban.delflg', 0)
						->WHERE('ban.location', 2)
						->GROUPBY('ban.id')
						->lists('ban.BankName','ban.id');
						// ->toSql();
						// dd($query);
		return $query;	
	}
	public static function fnGetBankBranchDet($request,$invoicedata) {
		try {
			$db = DB::connection('mysql');
			$query = $db->TABLE('mstbanks AS ban')
						->SELECT('mst.id', 'ban.id as banid', 'ban.BankName', 'mstbra.BranchName',
								'mst.AccNo', 'mstbra.Id as braid', DB::RAW("CONCAT(COALESCE(ban.BankName,''),'-',COALESCE(mst.AccNo,'')) AS CONBANKACC"))
						->Join('mstbankbranch AS mstbra', 'ban.id', '=', 'mstbra.BankId')
						->Join('mstbank AS mst', function($join)
                         {
                             $join->on('ban.id', '=', 'mst.BankName');
                             $join->on('mst.BranchName', '=', 'mstbra.Id');
                         });
					$query = $query->WHERE('mst.mainFlg', 1)
							->WHERE('ban.delflg', 0)
							->WHERE('ban.location', 2);
					if($request->identEdit == 1 || $request->copyflg == 1) {
						$query = $query->ORWHERE('mst.BankName', $invoicedata[0]->bankid)
										->WHERE('mst.BranchName', $invoicedata[0]->bankbranchid)
										->WHERE('mst.AccNo', $invoicedata[0]->acc_no)
										->GROUPBY('mst.AccNo');
					}
					$query = $query->lists('CONBANKACC','mst.id');
			return $query;
		 } catch (Exception $e) {
					clserrorlog::fnPrintErrorLog($e);
				}
	}
	// pdf download start
	public static function fnGetEstiamteDetailsPDFDownload($id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('id', $id)
						->get();
		return $result;
		
	}
	public static function fnGetAmountDetails($id) {
		$result= DB::table('tbl_work_amount_details')
						->SELECT('id',
								'inv_primery_key_id',
								'invoice_id',
								'work_specific',
								'emp_id',
								'quantity',
								'unit_price',
								'amount',
								'remarks')
						->WHERE('inv_primery_key_id', $id)
						->get();
		return $result;
		
	}
	public static function fnfetchinvtaxdetails($date) {
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
	public static function fnfetchprojecttype($invprojecttype) {
		$result= DB::table('dev_estimatesetting')
						->SELECT('*')
						->WHERE('id', $invprojecttype)
						->get();
		return $result;
	}
	public static function fnGetBankAccountdetails($bankid,$branchid,$accountnumb){
		$result = DB::table('mstbank')
						->select('mstbank.*','mstbankbranch.BranchName AS bankbranch','mstbanks.BankName AS bankname')
						->leftjoin('mstbankbranch', 'mstbank.BranchName', '=', 'mstbankbranch.id')
						->leftjoin('mstbanks', 'mstbank.BankName', '=', 'mstbanks.id')
						->where('mstbank.AccNo',$accountnumb)
						->where('mstbank.BankName',$bankid)
						->where('mstbank.BranchName',$branchid)
						->get();
			return $result;
	}
	public static function fnGetEstimateUserDataPDF($id) {
		$result = DB::table('dev_invoices_registration')
						->select('dev_invoices_registration.*','mst_customerdetail.customer_id')
						->leftjoin('mst_customerdetail', 'mst_customerdetail.customer_name', '=', 'dev_invoices_registration.company_name')
						->where('dev_invoices_registration.id',$id)
						->get();
		return $result;
	}
	public static function fnfetchestimatequery($request) {
		$result = DB::table('dev_invoices_registration')
						->select('dev_invoices_registration.*','mst_customerdetail.customer_id')
						->leftjoin('mst_customerdetail', 'mst_customerdetail.customer_name', '=', 'dev_invoices_registration.company_name')
						->where('dev_invoices_registration.id',$request->estimateid)
						->get();
		return $result;
	}
	public static function fnGetCustomerDetailsView($id) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('id', '=', $id)
						->WHERE('delFlg', '=', 0)
						->get();
		return $result;
	}
	public static function fnGetEstimateRecord($from_date, $to_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$accessQuery = "";
		if (Auth::user()->userclassification == 1) {
			$from_date = Auth::user()->accessDate;
			$accessQuery = " OR accessFlg = 1 ";
		}
		// END ACCESS RIGHTS
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date > '$from_date' AND quot_date < '$to_date')".$accessQuery." ORDER BY quot_date ASC) as tbl1"))
			->get();
			// ->toSql();dd($result);
		return $result;
	}
	public static function fngetbranchdetails($request) {
		$bankid=$request->getbankval;
		$sql = "SELECT mstbra.BranchName as Branch,ban.* FROM mstbank ban JOIN mstbankbranch mstbra 
					ON ban.BranchName = mstbra.id WHERE ban.delflg = 0 AND ban.id = '$bankid' LIMIT 1";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetInvoice($id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();
		return $result;
	}
	public static function fnGetAccount($bankid,$branchid) {
		$result= DB::table('mstbank')
						->SELECT('*')
						->WHERE('BankName', '=', $bankid)
						->WHERE('BranchName', '=', $branchid)
						->get();
							// ->toSql();
							// dd($result);
		return $result;
	}
	public static function fnGetBranchName($bankid,$branchid) {
		$result= DB::table('mstbankbranch')
						->SELECT('*')
						->WHERE('BankId', '=', $bankid)
						->WHERE('id', '=', $branchid)
						->get();
		return $result;
	}
	public static function fnGetBankName($bankid) {
		$result= DB::table('mstbanks')
						->SELECT('*')
						->WHERE('id', '=', $bankid)
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
	// pdf download end
	// public static function fnGetCustomerDetails() {
	// 	$result= DB::table('mst_customerdetail')
	// 					->SELECT('*')
	// 					->WHERE('delFlg', '=', 0)
	// 					->orderBy('romaji', 'ASC')
	// 					->lists('customer_name','customer_id');
	// 	return $result;
	// }
	public static function fnGetCustomerDetails($request) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->limit(5)
						->orderBy('id', 'DESC')
						->lists('customer_name','customer_id');
		return $result;
	}
	public static function fnexistingcustomer($custarray) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('delFlg', '=', 0) 
						->orderBy('romaji', 'ASC')
						->lists('customer_name','customer_id');
		return $result;
	}
	public static function fnfetchsubsubject($request) {
	   $result = DB::table('mst_branchdetails')
						->select('mst_branchdetails.id','mst_branchdetails.branch_name','mst_branchdetails.branch_id')
						->leftjoin('mst_customerdetail', 'mst_branchdetails.customer_id', '=', 'mst_customerdetail.customer_id')
						->where('mst_customerdetail.customer_id',$request->mainid)
						->orderBy('mst_branchdetails.branch_id', 'ASC')
						->get();
		return $result;
	}
	public static function fnGetEmpDetails($request) {
	    $query= DB::table('emp_mstemployees')
	               ->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
	               ->WHERE('delFlg', '=', 0)
	               ->WHERE('resign_id', '=', 0)
	               ->WHERE('Title', '=', 2)
	               ->WHERE('Emp_ID', 'NOT LIKE', '%NST%')
	               ->orderBy('Emp_ID', 'ASC')
				   ->get();
	    return $query;
	}
	public static function fnGetNonstaffEmpDetails($request) {
	    $query= DB::table('emp_mstemployees')
	               ->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
	               ->WHERE('delFlg', '=', 0)
	               ->WHERE('resign_id', '=', 0)
	               ->WHERE('Emp_ID', 'LIKE', '%NST%')
	               ->orderBy('Emp_ID', 'ASC')
				   ->get();
	    return $query;
	}
	public static function fnGetOtherDetails($request) {
		$result= DB::table('dev_estimate_others')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->lists('content','id');
		return $result;
	}
	public static function fnGenerateInvoiceID($request){
		$result= DB::table('dev_invoices_registration')
						->SELECT('user_id')
						->orderBy('user_id', 'DESC')
						->limit(1)
						->get();
		$cmn = "INV";
		if (count($result) == 0) {
			$id = $cmn . "00001";
		} else {
			foreach ($result as $key => $value) {
				$g_id = intval(str_replace("INV", "", $value->user_id)) + 1;
				$id = $cmn . substr("00000" . $g_id, -5);
			}
		}
		return $id;
	}
	public static function pdfflgset($user_id,$pdf_name) {
		$update=DB::table('dev_invoices_registration')
			->where('user_id', $user_id)
			->update(
				['pdf_flg' => 1,
				'pdf_name' => $pdf_name]
		);
    	return $update;
	}
	public static function fnfetchbranchnumber($request) {
		$result= DB::table('mst_customerdetail')
						->SELECT('id')
						->WHERE('customer_id', '=', $request->companyid)
						->get();
		return $result;
	}
	public static function fnUpdateInvoicedetails($request,$totalinvvalue,$bid) {
		//print_r($request->tableamountcount); exit();
		$tableamountcount = $request->rowCount;
		$tablespecialcount = $request->tablespecialcount;
		$field = "";
		$fieldval = "";
		$accessrights = 0;
		$common_field = array("work_specific", "quantity", "unit_price", "amount", "remarks", "emp_ID");
		if (isset($request->accessrights)) {
			$accessrights = $request->accessrights;
		}
 		$data[] =   [
			'project_personal' => $request->projectpersonal,
			'user_id' => $request->usersid,
			'project_name' => $request->project_name,
			'project_type_selection' => $request->projecttype_sel,
			'trading_destination_selection' => $bid,
			'branch_selection' => $request->branchname_sel,
			'quot_date' => $request->quot_date,
			'imprint' => $request->mark,
			'bankid' => $request->bank_id,
			'bankbranchid' => $request->invoicebranchname,
			'totalval' => $request->totval,
			'acc_no' => $request->invoiceacctnumb,
			'payment_date' => $request->payment_date,
			'company_sign' => $request->impre,
			'updated_by' => Auth::user()->username,
			'updated_time' => date('Y-m-d H-i-s'),
			'company_name' => $request->companynames,
			'pdf_flg' => 0,
			'mailFlg' => 0,
			'memo' => $request->memo,
		];

		
		for ($i=1; $i <=5 ; $i++) { // loop for notice insert
			$stat='special_ins'.$i;
			$note='note'.$i;
			array_push_asociate($data[0], $stat, $request->$note);
		}
		if (Auth::user()->userclassification == 4) {
			array_push_asociate($data[0], 'accessFlg', $accessrights);
			$update=DB::table('dev_invoices_registration')->where('id', $request->invid)->update($data[0]);
		} else {
			$update=DB::table('dev_invoices_registration')->where('id', $request->invid)->update($data[0]);
		}
// New Table Update
		$deldetails = DB::table('tbl_work_amount_details')
						->WHERE('inv_primery_key_id', '=', $request->invid)
						->DELETE();
		$lo = 0;
		for ($i=1; $i <=$tableamountcount; $i++) { 
			$stat1='work_specific'.$i;
			$stat2='emp_ID'.$i;
			$stat3='quantity'.$i;
			$stat4='unit_price'.$i;
			$stat5='amount'.$i;
			$stat6='remarks'.$i;
			if ($request->$stat1!=''||
				$request->$stat2!=''||
			    $request->$stat3!=''||
				$request->$stat4!=''||
				$request->$stat5!=''||
				$request->$stat6!='') {
				$amount_details[$lo] =   [
		       		'inv_primery_key_id' => $request->invid,
					'invoice_id' =>  $request->usersid,// TODO
					'created_by' => Auth::user()->username,
					'updated_by' => Auth::user()->username,
					'created_time' => date('Y-m-d H-i-s'),
					'updated_time' => date('Y-m-d H-i-s'),
					'del_flg' => 0,
				];
				array_push_asociate($amount_details[$lo], 'work_specific', $request->$stat1);
				if(!empty($request->$stat2)){
						array_push_asociate($amount_details[$lo], 'emp_id', $request->$stat2);
						} 
						else
						{
						$request->$stat2 = NULL;
						array_push_asociate($amount_details[$lo], 'emp_id', $request->$stat2);
						}
				array_push_asociate($amount_details[$lo], 'quantity', $request->$stat3);
				array_push_asociate($amount_details[$lo], 'unit_price', $request->$stat4);
				array_push_asociate($amount_details[$lo], 'amount', $request->$stat5);
				// print_r($request->$stat5);exit();
				array_push_asociate($amount_details[$lo], 'remarks', $request->$stat6);
				$lo++;
			} 
		}
		if (!empty($amount_details)) {
			$insert=DB::table('tbl_work_amount_details')->insert($amount_details);
		}
		return $update;
	}
	public static function fnfetchlastid($request) {
		$result= DB::table('dev_invoices_registration')
						->max('id');
		return $result;
	}
	public static function fnfetchqdate($request,$id) {
		$result= DB::table('dev_invoices_registration')
						->select('quot_date')
						->WHERE('id', '=', $id)
						->get();
		return $result;
	}
	public static function fnInsertInvoice($request,$code,$totalinvvalue) {
		 $rowCount=$request->rowCount;
		 $tableamountcount = $request->tableamountcount;
		// $tablespecialcount = $request->tablespecialcount;
		$field = "";
		$fieldval = "";
		$common_field = array("work_specific", "quantity", "unit_price", "amount", "remarks", "emp_ID");

 		$data[] =   [
       		'id' => '',
			'user_id' => $code,
			'estimate_id' => $request->estimateid,
			'project_personal' => $request->projectpersonal,
			'project_name' => $request->project_name,
			'trading_destination_selection' => $request->tradename,
			'branch_selection' => $request->branchname_sel,
			'tax' => $request->tax,
			'tighten_month_selection'=>0,
			'project_type_selection' => $request->projecttype_sel,
			'cutoff_date_selection' => 0,
			'trading_selection' => 0,
			'quot_date' => $request->quot_date,
			'billing_month_selection' => 0,
			'billing_date_selection' => 0,
			'totalval' => $request->totval,
			'bankid' => $request->bank_id,
			'bankbranchid' => $request->invoicebranchname,
			'acc_no' => $request->invoiceacctnumb,
			'payment_date' => $request->payment_date,
			'imprint' => $request->mark,
			'company_sign' => $request->impre,
			'created_by' => Auth::user()->username,
			'updated_by' => Auth::user()->username,
			'created_time' => date('Y-m-d H-i-s'),
			'updated_time' => date('Y-m-d H-i-s'),
			'del_flg' => 0,
			'company_name' => $request->company_name,
			'classification' => 0,
			'pdf_flg' => 0,
			'memo' => $request->memo,
		];
		for ($i=1; $i <=15 ; $i++) { // loop for common field
			$stat1='work_specific'.$i;
			$stat2='quantity'.$i;
			$stat3='unit_price'.$i;
			$stat4='amount'.$i;
			$stat5='remarks'.$i;
			$stat6='emp_ID'.$i;
			array_push_asociate($data[0], $stat1, $request->$stat1);
			array_push_asociate($data[0], $stat2, $request->$stat2);
			array_push_asociate($data[0], $stat3, $request->$stat3);
			array_push_asociate($data[0], $stat4, $request->$stat4);
			array_push_asociate($data[0], $stat5, $request->$stat5);
			array_push_asociate($data[0], $stat6, $request->$stat6);
		}
		for ($i=1; $i <=5 ; $i++) { // loop for notice insert
			$stat='special_ins'.$i;
			$statics='note'.$i;
			array_push_asociate($data[0], $stat, $request->$statics);
		}

		if (Auth::user()->userclassification == 4) {
			array_push_asociate($data[0], 'accessFlg', $request->accessrights);
			$insert=DB::table('dev_invoices_registration')->insertGetId($data[0]);
		} else {
			$insert=DB::table('dev_invoices_registration')->insertGetId($data[0]);			
		}

		// For Copy Flag process Added by Kumaran
		if ($request->regflg==2) {
			$update=DB::table('dev_invoices_registration')
				->where('id', $request->invoiceid)
				->update(['copyFlg' => 1]	
								
			);				
		}
		// New Table Insert
		$lo = 0;
		for ($i=1; $i <=$rowCount ; $i++) { 
			$stat1='work_specific'.$i;
			$stat2='emp_ID'.$i;
			$stat3='quantity'.$i;
			$stat4='unit_price'.$i;
			$stat5='amount'.$i;
			$stat6='remarks'.$i;
			//print_r($stat2);
			if ($request->$stat1!=''||
				$request->$stat2!=''||
			    $request->$stat3!=''||
				$request->$stat4!=''||
				$request->$stat5!=''||
				$request->$stat6!='') {
				$amount_details[$lo] =   [
		       		'inv_primery_key_id' => $insert,
					'invoice_id' => $code,
					'created_by' => Auth::user()->username,
					'updated_by' => Auth::user()->username,
					'created_time' => date('Y-m-d H-i-s'),
					'updated_time' => date('Y-m-d H-i-s'),
					'del_flg' => 0,
				];
				array_push_asociate($amount_details[$lo], 'work_specific', $request->$stat1);
				if(!empty($request->$stat2)){
				array_push_asociate($amount_details[$lo], 'emp_id', $request->$stat2);
				} 
				else
				{
				$request->$stat2 = NULL;
				array_push_asociate($amount_details[$lo], 'emp_id', $request->$stat2);
				}
				array_push_asociate($amount_details[$lo], 'quantity', $request->$stat3);
				array_push_asociate($amount_details[$lo], 'unit_price', $request->$stat4);
				array_push_asociate($amount_details[$lo], 'amount', $request->$stat5);
				if(!empty($request->$stat6)){
				array_push_asociate($amount_details[$lo], 'remarks', $request->$stat6);
				} 
				else
				{
				$request->$stat6 = NULL;
				array_push_asociate($amount_details[$lo], 'remarks', $request->$stat6);
				}
				$lo++;
			} 
		}
		if (!empty($amount_details)) {
			$insert=DB::table('tbl_work_amount_details')->insert($amount_details);
		}
		return $insert;
	}
   // query for Insert Table menu
	public static function amtdet() {
		$result= DB::table('dev_invoices_registration')
						->SELECT('id', 'user_id', 
								'work_specific1', 'emp_ID1', 'quantity1', 'unit_price1', 'amount1', 'remarks1',
								'work_specific2', 'emp_ID2', 'quantity2', 'unit_price2', 'amount2', 'remarks2',
								'work_specific3', 'emp_ID3', 'quantity3', 'unit_price3', 'amount3', 'remarks3',
								'work_specific4', 'emp_ID4', 'quantity4', 'unit_price4', 'amount4', 'remarks4',
								'work_specific5', 'emp_ID5', 'quantity5', 'unit_price5', 'amount5', 'remarks5',
								'work_specific6', 'emp_ID6', 'quantity6', 'unit_price6', 'amount6', 'remarks6',
								'work_specific7', 'emp_ID7', 'quantity7', 'unit_price7', 'amount7', 'remarks7',
								'work_specific8', 'emp_ID8', 'quantity8', 'unit_price8', 'amount8', 'remarks8',
								'work_specific9', 'emp_ID9', 'quantity9', 'unit_price9', 'amount9', 'remarks9',
								'work_specific10', 'emp_ID10', 'quantity10', 'unit_price10','amount10', 'remarks10',
								'work_specific11', 'emp_ID11', 'quantity11', 'unit_price11', 'amount11', 'remarks11',
								'work_specific12', 'emp_ID12', 'quantity12', 'unit_price12', 'amount12', 'remarks12',
								'work_specific14', 'emp_ID14', 'quantity14', 'unit_price14', 'amount14', 'remarks14',
								'work_specific13', 'emp_ID13', 'quantity13', 'unit_price13', 'amount13', 'remarks13',
								'work_specific15', 'emp_ID15', 'quantity15', 'unit_price15', 'amount15', 'remarks15'
								)
						->get();
		return $result;
		
	}
	// Insert Table menu
	public static function amtnewtbl($amtinsert) {
		$exist_cnt= DB::table('tbl_work_amount_details')
						->SELECT('inv_primery_key_id')
						->count();
		if ($exist_cnt == 0) {
		$lo = 0;
		$j = 0;
		foreach ($amtinsert as $key => $value) {
			for ($i=1; $i <=15 ; $i++) {
				$stat1='work_specific'.$i;
				$stat2='emp_ID'.$i;
				$stat3='quantity'.$i;
				$stat4='unit_price'.$i;
				$stat5='amount'.$i;
				$stat6='remarks'.$i;
					if (!empty($value->$stat1) || !empty($value->$stat2) ||  !empty($value->$stat3)||
						!empty($value->$stat4) || !empty($value->$stat5) || !empty($value->$stat6)) {
						$amount_details[$lo] =   [
				       		'inv_primery_key_id' => $value->id,
							'invoice_id' => $value->user_id,
							'created_by' => Auth::user()->username,
							'updated_by' => Auth::user()->username,
							'created_time' => date('Y-m-d H-i-s'),
							'updated_time' => date('Y-m-d H-i-s'),
							'del_flg' => 0,
						];
						array_push_asociate($amount_details[$lo], 'work_specific', $value->$stat1);
						if(!empty($value->$stat2)){
						array_push_asociate($amount_details[$lo], 'emp_id', $value->$stat2);
						} 
						else
						{
						$value->$stat2 = NULL;
						array_push_asociate($amount_details[$lo], 'emp_id', $value->$stat2);
						}
						array_push_asociate($amount_details[$lo], 'quantity', $value->$stat3);
						array_push_asociate($amount_details[$lo], 'unit_price', $value->$stat4);
						array_push_asociate($amount_details[$lo], 'amount', $value->$stat5);
						if(!empty($value->$stat6)){
						array_push_asociate($amount_details[$lo], 'remarks', $value->$stat6);
						} 
						else
						{
						$value->$stat6 = NULL;
						array_push_asociate($amount_details[$lo], 'remarks', $value->$stat6);
						}
						$lo++;
					} 
				}
			}
			if(isset($amount_details)){
			foreach ($amount_details as $key => $value) {
			    $insert=DB::table('tbl_work_amount_details')->insert($value);
			}
		}
		else{
			$amount_details=null;
		}
		}
		
	}
	//send mail process start
	public static function getCompanyName($id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();
		return $result;
		
	}
	public static function fnGetallinvoice($id,$datemonth) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('trading_destination_selection', '=', $id)
						->WHERE('pdf_flg', '=', 1)
						->WHERE('quot_date','LIKE','%'.$datemonth.'%')
						->get();
		return $result;
	}
	public static function fnGetEmailPDf($cust_id)
	{
		$result= DB::table('tbl_work_amount_details')
						->SELECT('id',
								'inv_primery_key_id',
								'invoice_id',
								'work_specific',
								'emp_id',
								'quantity',
								'unit_price',
								'amount',
								'remarks')
						->WHERE('inv_primery_key_id', $cust_id)
						->get();
		return $result;
		
	}

	public static function fngetinvoicedetails($request, $all_invid=null) {
		if ($all_invid != null) {
			$invoiceid = $all_invid;
		} else {
			$invoiceid = $request->invoiceid;
		}

		$db = DB::connection('mysql');
		$result = $db->TABLE($db->raw("(SELECT main.trading_destination_selection,main.bankid,
				main.bankbranchid,main.acc_no,main.quot_date,main.company_name,main.user_id,
				main.tax,main.special_ins1,main.special_ins2,main.special_ins3,main.special_ins4,main.special_ins5,
				works.work_specific,works.quantity,works.unit_price,works.amount,works.remarks
				,works.invoice_id,main.totalval 
				FROM  dev_invoices_registration main  
				left join tbl_work_amount_details works on works .invoice_id = main .user_id 
				WHERE main.id='$invoiceid'  AND main.del_flg = 0 
				) AS DDD "));  
			$result = $result->get();

		return $result;
	}
	public static function fnGetinvoiceTotVal($request,$date_month) {
		if ($request->retedit==2) {
			$Estimate = db::table('dev_invoices_registration')
									->select('*')
									->WHERE('quot_date','LIKE','%'.$date_month.'%')
									->WHERE('del_flg',0);
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate = $Estimate->WHERE('quot_date', '>', $accessDate);
				}
				// END ACCESS RIGHTS	
				$Estimate = $Estimate->orderBy($request->sortOptn, $request->sortOrder)
								->get();
		} else {
			/*$Estimate = db::table('dev_invoices_registration')
									->select('*')
									->WHERE('quot_date','LIKE','%'.$date_month.'%')
									->WHERE('del_flg',0);	*/
			$Estimate = db::table('dev_invoices_registration')
								->select('dev_invoices_registration.*',
									DB::raw("(CASE 
    										WHEN dev_invoices_registration.classification = 2 THEN 3
    										ELSE 0
											END) AS orderbysent"))
								->WHERE('quot_date','LIKE','%'.$date_month.'%')
								->WHERE('del_flg',0);
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate = $Estimate->WHERE('quot_date', '>', $accessDate);
				}
				// END ACCESS RIGHTS
				/*$Estimate = $Estimate->orderBy($request->sortOptn, $request->sortOrder)
									->get();*/
				$Estimate = $Estimate->orderByRaw("orderbysent ASC, user_id DESC")->get();
		}
		return $Estimate;
	}
	public static function fngetestimatedetails($request, $all_estid = null) {
		if ($all_estid != null) {
			$estimateid = $all_estid;
		} else {
			$estimateid = $request->estimateid;
		}
		$result= DB::table('dev_estimate_registration')
						->SELECT('*')
						->WHERE('id', '=', $estimateid)
						->get();
		return $result;
	}
	public static function fnGetCustomerDetail($tselection) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('id', '=', $tselection)
						->WHERE('delFlg', '=', 0)
						->get();
		return $result;
	}
	public static function fnGetAccounts($bankid,$branchid,$acc_no) {
		$result = DB::table('mstbank')
						->select('mstbank.*','mstbankbranch.BranchName AS bankbranch','mstbanks.BankName AS bankname')
						->leftjoin('mstbankbranch', 'mstbank.BranchName', '=', 'mstbankbranch.id')
						->leftjoin('mstbanks', 'mstbank.BankName', '=', 'mstbanks.id')
						->where('mstbank.AccNo',$acc_no)
						->where('mstbank.BankName',$bankid)
						->where('mstbank.BranchName',$branchid)
						->get();
			return $result;
	}
	public static function getemp_details($invoice_id,$emp_id) {
		$result= DB::table('tbl_work_amount_details')
						->SELECT(DB::RAW("CONCAT(emp.KanaFirstName,'　',emp.KanaLastName) AS KanaName"),
								DB::RAW("CONCAT(emp.FirstName,'　',emp.LastName) AS EnglishName"),
								'tbl_work_amount_details.emp_id')
						->leftjoin('emp_mstemployees as emp', 'emp.Emp_ID', '=', 'tbl_work_amount_details.emp_id')
						->WHERE('tbl_work_amount_details.inv_primery_key_id', '=', $invoice_id)
						->WHERE('tbl_work_amount_details.emp_id', '=', $emp_id)
						->get();
						// ->toSql();
						// dd($query);
		return $result;
	}
	public static function getemp_detail($invoice_id,$id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT(DB::RAW("CONCAT(emp.KanaFirstName,'　',emp.KanaLastName) AS KanaName"),
								DB::RAW("CONCAT(emp.FirstName,'　',emp.LastName) AS EnglishName"),
							'dev_invoices_registration.emp_ID'.$id)
						->leftjoin('emp_mstemployees as emp', 'emp.Emp_ID', '=', 'dev_invoices_registration.emp_ID'.$id)
						->WHERE('dev_invoices_registration.id', '=', $invoice_id)
						->get();
		return $result;
	}
	public static function mailflgupdate($estnames) {
		$update="";
		for ($i=0; $i < count($estnames) ; $i++) { 
			$update=DB::table('dev_invoices_registration')
				->where('user_id', $estnames[$i])
				->update(['mailFlg' => 1]
			);
		}
		return $update;
	}
	public static function fnGetBillingDetails($request) {
		$currentto = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
		$past1month   = $currentto->subMonths(1);
		$past1month = $past1month->modify('first day of this month');
		$yearlink = $past1month->format('Y');
		$monthlink = $past1month->format('m');
		$result = DB::TABLE('inv_newbilling AS billing')
					->SELECT('billing.TotalAmount',
							'billing.OTAmount',
							'billing.minhrs',
							'billing.Amount',
							'billing.maxhrs',
							'billing.maxamt',
							'billing.minamt',
							'billing.timerange',
							DB::RAW("IF(timerange>maxhrs, timerange-maxhrs, IF(timerange<minhrs, minhrs-timerange, 0)) AS Quantity"),
							DB::RAW("IF(IF(timerange>maxhrs, timerange-maxhrs, 
								IF(timerange<minhrs, timerange-minhrs, 0))<0, maxamt, minamt) AS Unitprice"),
							DB::RAW("IF(employees.nickname='' OR employees.nickname IS NULL,CONCAT(employees.LastName,employees.FirstName),employees.nickname) AS NickName"))
					->LEFTJOIN('emp_mstemployees AS employees', 'employees.Emp_ID', '=', 'billing.Empno')
					->WHERE('billing.Clientid', $request->custid)
					->WHERE('billing.branch_id', $request->branchid)
					->WHERE('billing.yearlink',$yearlink)
					->WHERE('billing.monthlink',$monthlink)
					->orderBy('employees.Emp_ID', 'ASC')
					->GET();
		return $result;
	}
	public static function fnGetinvoiceDownload($request,$date_month) {
		
		$db = DB::connection('mysql');
		$Estimate = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,main.trading_destination_selection,main.payment_date,main.del_flg,main.copyFlg,main.project_name,
		main.created_by,main.pdf_flg,main.project_type_selection,main.mailFlg, 
		main.paid_date,main.paid_status,main.tax,main.estimate_id,main.company_name,main.bankid,main.bankbranchid,main.acc_no,works.amount,main.classification,
		works.work_specific,works.quantity,works.unit_price,works.remarks,works.emp_id,(CASE 
        WHEN main.classification = 2 THEN 3
        ELSE 0
    	END) AS orderbysent,`dev_estimatesetting`.`ProjectType`,main.totalval 
		FROM   tbl_work_amount_details works 
		left join dev_invoices_registration main on works .invoice_id = main .user_id 
		left join dev_estimatesetting on dev_estimatesetting.id = main.project_type_selection
		WHERE works.del_flg = 0 AND main.quot_date LIKE '%$date_month%'
		GROUP BY user_id Order By user_id Asc,quot_date Asc
			) AS DDD Order By user_id DESC"))
		->get();
		// ->toSql();dd($query);
		return $Estimate;


	}
	public static function fnGetinvoldDetails($request) {
		$db=DB::connection('mysql');
		$quot_date = $request->selYear."-".$request->selMonth;
		$query=$db->TABLE($db->raw("(SELECT dev_invoices_registration.id,
																user_id,
																estimate_id,
																trading_selection,
																trading_destination_selection,
																company_name,
																branch_selection,
																project_personal,
																project_name,
																tax,
																project_type_selection,
																tighten_month_selection,
																cutoff_date_selection,
																quot_date,
																billing_month_selection,
																billing_date_selection,
																special_ins1,
																special_ins2,
																special_ins3,
																special_ins4,
																special_ins5,
																bankid,
																bankbranchid,
																acc_no,
																payment_date,
																personnel_mark,
																approver_mark,
																company_sign,
																imprint,
																paid_status,
																pdf_flg,
																mailFlg,
																accessFlg,
																pdf_name,
																classification,
																paid_date,
																paid_yearmonth,
																memo,
																copyFlg,
																tbl_work_amount_details.inv_primery_key_id,
																						work_specific,
																						quantity,
																						amount,
																						unit_price,
																						
																						remarks,
																						emp_id,
																						mst_customerdetail.customer_id FROM 
																dev_invoices_registration
															LEFT JOIN tbl_work_amount_details ON tbl_work_amount_details.inv_primery_key_id=dev_invoices_registration.id
															LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_invoices_registration.trading_destination_selection 
															WHERE 
															dev_invoices_registration.del_flg = 0 AND dev_invoices_registration.quot_date LIKE '%$quot_date%'
																AND (tbl_work_amount_details.work_specific IS NOT NULL AND tbl_work_amount_details.work_specific != '') ORDER BY user_id DESC) as tb1"))
										->get();
		return $query;
	}
	public static function fninsertinvoldDetails($request) {
		for ($j=1; $j <= $request->invcount; $j++) {
			$invid='invid'.$j; 
			$newId = array();
			for ($i=1; $i < 15; $i++) {
				$emp_ID_id='emp_ID'.$i;
				$emp_ID='emp_ID_'.$request->$invid.'_'.$i;
				if ($request->$emp_ID != "") {
					$newId[$i] = $request->$emp_ID;
				}
				$update=DB::table('dev_invoices_registration')
							->where('id', $request->$invid)
							->update([$emp_ID_id => $request->$emp_ID]);
			}
			$result= DB::table('tbl_work_amount_details')
						->SELECT('id')
						->WHERE('inv_primery_key_id', '=', $request->$invid)
						->get();
			foreach ($result as $key => $value) {
				if (isset($newId[$key+1])) {
					$newId[$key] = $newId[$key+1];
				} else {
					$newId[$key] = null;
				}
				$update=DB::table('tbl_work_amount_details')
							->where('id', $value->id)
							->update(['emp_id' => $newId[$key]]);
			}

		}
		return $update;
	}
	public static function fnGetinvoiceUserDatabydate($request){
		$db=DB::connection('mysql');
		$quot_date = $request->selYear."-".$request->selMonth;
		$query=$db->TABLE($db->raw("(SELECT dev_invoices_registration.id,
																user_id,
																estimate_id,
																trading_selection,
																trading_destination_selection,
																company_name,
																branch_selection,
																project_personal,
																project_name,
																tax,
																project_type_selection,
																tighten_month_selection,
																cutoff_date_selection,
																quot_date,
																billing_month_selection,
																billing_date_selection,
																special_ins1,
																special_ins2,
																special_ins3,
																special_ins4,
																special_ins5,
																bankid,
																bankbranchid,
																acc_no,
																payment_date,
																personnel_mark,
																approver_mark,
																company_sign,
																imprint,
																paid_status,
																pdf_flg,
																mailFlg,
																accessFlg,
																pdf_name,
																classification,
																paid_date,
																paid_yearmonth,
																memo,
																copyFlg,
																tbl_work_amount_details.inv_primery_key_id,
																						work_specific,
																						quantity,
																						amount,
																						unit_price,
																						
																						remarks,
																						emp_id,
																						mst_customerdetail.customer_id FROM 
																dev_invoices_registration
															LEFT JOIN tbl_work_amount_details ON tbl_work_amount_details.inv_primery_key_id=dev_invoices_registration.id
															LEFT JOIN mst_customerdetail ON mst_customerdetail.id=dev_invoices_registration.trading_destination_selection 
															WHERE 
															dev_invoices_registration.copyFlg = 0 AND dev_invoices_registration.quot_date LIKE '%$quot_date%'
																AND (tbl_work_amount_details.work_specific IS NOT NULL AND tbl_work_amount_details.work_specific != '') ORDER BY user_id DESC) as tb1"))
										->get();
		return $query;
	}
	public static function fnGetinvoiceUserDatabyid($request, $invoiceid, $code){
		$dbrecord = array();
		$result = '';
		$db=DB::connection('mysql');
		$quot_date = $request->selYear."-".$request->selMonth;
		$sql = "SELECT * FROM dev_invoices_registration WHERE id = '$invoiceid' AND quot_date LIKE '%$quot_date%' AND copyFlg = 0";
		$query = DB::select($sql);
		if (!empty($query)) {
			$result = $query[0];
			$dbrecord['estimateid'] = $result->estimate_id;
			$dbrecord['projectpersonal'] = $result->project_personal;
			$dbrecord['project_name'] = $result->project_name;
			$dbrecord['tradename'] = $result->trading_destination_selection;
			$dbrecord['branchname_sel'] = $result->branch_selection;
			$dbrecord['tax'] = $result->tax;
			$dbrecord['projecttype_sel'] = $result->project_type_selection;
			$dbrecord['quot_date'] = $request['quot_date'];
			$dbrecord['bank_id'] = $result->bankid;
			$dbrecord['invoicebranchname'] = $result->bankbranchid;
			$dbrecord['invoiceacctnumb'] = $result->acc_no;
			$dbrecord['payment_date'] = $request['payment_date'];
			$dbrecord['mark'] = $result->imprint;
			$dbrecord['impre'] = $result->company_sign;
			$dbrecord['company_name'] = $result->company_name;
			$dbrecord['memo'] = $result->memo;
			$totalval = 0;
			$resultCount= DB::table('tbl_work_amount_details')
						->SELECT('id')
						->WHERE('inv_primery_key_id', '=', $invoiceid)
						->count();
			if ($resultCount <= 15) {
				$loop = 15;
			} else {
				$loop = $resultCount;
			}
			for ($r=1; $r <= $loop; $r++) {
				$dbrecord['emp_ID'.$r] = $request['emp_ID_'.$invoiceid.'_'.$r];
				$dbrecord['work_specific'.$r] = $request['work_specific_'.$invoiceid.'_'.$r];
				$dbrecord['quantity'.$r] = $request['quantity_'.$invoiceid.'_'.$r];
				$dbrecord['unit_price'.$r] = $request['unit_price_'.$invoiceid.'_'.$r];
				$dbrecord['amount'.$r] = $request['amount_'.$invoiceid.'_'.$r];
				$totalval = $totalval+$request['amount_'.$invoiceid.'_'.$r];
				$remarks = 'remarks'.$r;
				if (isset($result->$remarks)) {
					$dbrecord['remarks'.$r] = $result->$remarks;
				} else {
					$dbrecord['remarks'.$r] = null;
				}
			}
			$dbrecord['totval'] = $totalval;
			for ($i=1; $i <= 5; $i++) { 
				$special_ins = 'special_ins'.$i;
				$dbrecord['note'.$i] = $result->$special_ins;
			}
			$dbrecord['accessrights'] = $result->accessFlg;
			$dbrecord['invoiceid'] = $invoiceid;
			$dbrecord['regflg'] = 2;
			$dbrecord['tableamountcount'] = $request->tableamountcount;
			$dbrecord['rowCount'] = $loop;
			$result = self::fnInsertInvoice((object)$dbrecord,$code,0);
		}
		return $result;
	}
}


function array_push_asociate(&$array, $key, $value) {
    $array[$key] = $value;
    return $array;
}