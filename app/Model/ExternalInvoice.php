<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;

class ExternalInvoice extends Model {


	public static function fnGetAccountPeriod($request) {

		$result = DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->get();

		return $result;

	}

	public static function fnGetEstimateRecord($from_date, $to_date) {

		// ACCESS RIGHTS
		$accessQuery = "";
		if (Auth::user()->userclassification == 1) {
			$from_date = Auth::user()->accessDate;
			$accessQuery = " OR accessFlg = 1 ";
		}
		// END ACCESS RIGHTS

		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM ext_invoice_registration WHERE delFlg = 0 AND (quot_date > '$from_date' AND quot_date < '$to_date')".$accessQuery." ORDER BY quot_date ASC) as tbl1"))

			->get();

		return $result;
	}

	public static function fnGetEstimateRecordPrevious($from_date) {

		// ACCESS RIGHTS
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND ( quot_date >= '$to_date' OR accessFlg = 1 )";
		}
		// END ACCESS RIGHTS

		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM ext_invoice_registration WHERE delFlg = 0 AND (quot_date <= '$from_date' $conditionAppend) ORDER BY quot_date ASC) as tbl1"))

			->get();

		return $result;

	}

	public static function fnGetEstimateRecordNext($to_date) {

		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM ext_invoice_registration WHERE delFlg = 0 AND (quot_date >= '$to_date') ORDER BY quot_date ASC) as tbl1"))

			->get();

		return $result;

	}

	public static function fnGetinvoiceTotalValue($request,$taxSearch,$date_month,$search_flg, $projecttype,$singlesearchtxt, $estimateno, $companyname, $startdate, $enddate,$filter) {

		if ($request->searchmethod == 1 || $request->searchmethod == 2 || $request->searchmethod == 3) {
			$filter = "";
		}

		if (!empty($request->searchmethod)) {
			$wherecondition = "";
			$Estimate = db::table('ext_invoice_registration')
							->select('ext_invoice_registration.*', DB::raw("(CASE 
								WHEN ext_invoice_registration.classification = 2 THEN 3
								ELSE 0
								END) AS orderbysent"))
							->WHERE('delFlg',0);

			if ($filter == "2") {
				$Estimate = $Estimate->where('ext_invoice_registration.classification',0)
									->where('ext_invoice_registration.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "3") {
				$Estimate = $Estimate->where('ext_invoice_registration.classification',1)
									 ->where('ext_invoice_registration.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "4") {
				$Estimate = $Estimate->where('ext_invoice_registration.classification',3)
									 ->where('ext_invoice_registration.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "5") {
				$Estimate = $Estimate->where('ext_invoice_registration.classification',2)
									->where('ext_invoice_registration.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "1") {
				$Estimate = $Estimate->where('ext_invoice_registration.quot_date', 'LIKE', '%' . $date_month . '%');
			}

			if ($request->searchmethod == 3) {
				if (!empty($request->companynameClick)) {
						$Estimate = $Estimate->where('ext_invoice_registration.userId','LIKE','%'.$request->companynameClick.'%');
				}
			}

			if (!empty($estimateno)) {
				$Estimate = $Estimate->where('ext_invoice_registration.invoiceId','LIKE','%'.$estimateno.'%');
			}

			if (!empty($startdate) && !empty($enddate)) {
				$Estimate = $Estimate->where('ext_invoice_registration.quot_date','>=',$startdate);
				$Estimate = $Estimate->where('ext_invoice_registration.quot_date','<=',$enddate);
			}

			if (!empty($startdate) && empty($enddate)) {
				$Estimate = $Estimate->where('ext_invoice_registration.quot_date','>=',$startdate);
			}

			if (empty($startdate) &&!empty($enddate)) {
				$Estimate = $Estimate->where('ext_invoice_registration.quot_date','<=',$enddate);
			}

			if ($request->searchmethod == 1) {
				$Estimate = $Estimate->where(function($joincont) use ($request) {
					$joincont->where('ext_invoice_registration.invoiceId', 'LIKE', '%' . trim($request->singlesearch) . '%')
							->orWhere('ext_invoice_registration.userId', 'LIKE', '%' . trim($request->singlesearch) . '%')
							->orWhere('ext_invoice_registration.project_name', 'LIKE', '%' . trim($request->singlesearch) . '%');
				});
			}

			if ($request->searchmethod == 2) {
				if (!empty($request->msearchusercode)) {
					$Estimate = $Estimate->where('ext_invoice_registration.invoiceId', 'LIKE', '%' . trim($request->msearchusercode) . '%');

				}

				if (!empty($request->msearchcustomer)) {
					$Estimate = $Estimate->where('ext_invoice_registration.userId', 'LIKE', '%' . trim($request->msearchcustomer) . '%');
				}

				if(!empty($request->msearchstdate) && !empty($request->msearcheddate)) {
					$Estimate = $Estimate->whereBetween('ext_invoice_registration.quot_date', [$request->msearchstdate, $request->msearcheddate]);
				}

				if(!empty($request->msearchstdate) && empty($request->msearcheddate)) {
					$Estimate = $Estimate->where(function($joincont) use ($request) {
						$joincont->where('ext_invoice_registration.quot_date', '>=', $request->msearchstdate);
					});
				}

				if(!empty($request->msearcheddate) && empty($request->msearchstdate)) {
					$Estimate = $Estimate->where('ext_invoice_registration.quot_date', '<=', $request->msearcheddate);
				}

				if ($taxSearch == 999 && !empty($request->protype1)) {
					$Estimate = $Estimate->where(function($joincont) use ($request) {
						$joincont->where('ext_invoice_registration.projectType', 'NOT LIKE', '%' . $request->protype1 . '%');

					});
				}

				if ($taxSearch != 999 && !empty($request->protype1)) {
					if($request->protype1 == 1) {
						$request->protype1 = "";
					}
					$Estimate = $Estimate->where(function($joincont) use ($request) {
						$joincont->where('ext_invoice_registration.projectType', 'LIKE', '%' . $request->protype1 . '%');
					});

				}

			}

   			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Estimate = $Estimate->WHERE(function($joincont) use($accessDate) {
				$joincont->WHERE('ext_invoice_registration.quot_date', '>',$accessDate)
						->ORWHERE('accessFlg','=',1);

				});
			}
			// END ACCESS RIGHTS

			if ($request->checkdefault != 1) {
				$Estimate = $Estimate->orderByRaw("orderbysent ASC, invoiceId DESC")
					  				->paginate($request->plimit);
			} else {
				$Estimate = $Estimate->orderBy($request->invoicesort,$request->sortOrder)
									->paginate($request->plimit);
			}
		} else {

			$db = DB::connection('mysql');
			$Estimate = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.userId,main.invoiceId,main.payment_date,main.delFlg,main.copyFlg,main.projectName,main.classification,main.createdBy,main.projectType,main.mailFlg,main.tax,
				(CASE WHEN main.classification = 2 THEN 3
        		ELSE 0
				END) AS orderbysent,main.totalval 
			FROM  ext_invoice_registration main
			WHERE main.delFlg = 0 AND main.quot_date LIKE '%$date_month%'
			GROUP BY invoiceId Order By invoiceId Asc, quot_date Asc) AS DDD"));

			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Estimate = $Estimate->WHERE(function($joincont) use($accessDate) {
					$joincont->WHERE('ext_invoice_registration.quot_date', '>',$accessDate)
							->ORWHERE('accessFlg','=',1);

				});

			}
			// END ACCESS RIGHTS

			if ($request->checkdefault != 1) {
				$Estimate = $Estimate->orderByRaw("orderbysent ASC, invoiceId DESC")
					  				->paginate($request->plimit);
				//->toSql();dd($Estimate);
			} else {
				$Estimate = $Estimate->orderBy($request->invoicesort,$request->sortOrder)
					  				->paginate($request->plimit);
			}
		}
		return $Estimate;
	}

	public static function updateClassification($request) {
		$data[] =   [ 'classification' => $request->invoicestatus ];

		$update = DB::table('ext_invoice_registration')
					->where('id', $request->invoicestatusid)->update($data[0]);

		return $update;

	}
	
	public static function getautoincrement() {

		$statement = DB::select("show table status like 'ext_mstuser'");

		return $statement[0]->Auto_increment;

	}

	public static function insertUser($request) {

		$phone = $request->userTelNo1.'-'.$request->userTelNo2.'-'.$request->userTelNo3;

		$insert = DB::table('ext_mstuser')

						->insert([ 

							'emailId' => $request->emailId,
							'userName' => $request->userName,
							'password' => md5($request->userPassword),
							'conpassword' => md5($request->userConPassword),
							'address' => $request->address,
							'buildingName' => $request->buildingName,
							'pincode' => $request->pincode,
							'mobileno' => $phone,
							'bankKanaName' => $request->bankKanaName,
							'accountNo' => $request->accountNo,
							'accountType' => $request->accountType,
							'bankName' => $request->bankName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'delflg' => 0,
							'CreatedBy' => Auth::user()->username,
							'UpdatedBy' => Auth::user()->username

						]);

		return $insert;



	}



	public static function updateUser($request) {

		$phone = $request->userTelNo1.'-'.$request->userTelNo2.'-'.$request->userTelNo3;

		$update = DB::table('ext_mstuser')

						->where('id', $request->editId)

						->update([

							'emailId' => $request->emailId,
							'userName' => $request->userName,
							'mobileno' => $phone,
							'address' => $request->address,
							'buildingName' => $request->buildingName,
							'pincode' => $request->pincode,
							'bankKanaName' => $request->bankKanaName,
							'accountNo' => $request->accountNo,
							'accountType' => $request->accountType,
							'bankName' => $request->bankName,
							'branchName' => $request->branchName,
							'branchNo' => $request->branchNo,
							'UpdatedBy' => Auth::user()->username

						]);

    		return $update;

	}


	public static function fnGetOtherDetails($request) {

		$result= DB::table('dev_estimate_others')

						->SELECT('*')

						->WHERE('delFlg', '=', 0)

						->lists('content','id');

		return $result;

	}

}