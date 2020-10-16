<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Accounting extends Model {

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
left join dev_payment_registration on dev_payment_registration.invoice_id = main.id
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
				//->toSql();dd($Estimate);

					  	->paginate($request->plimit);

			}
		}
		return $Estimate;
	}
}
