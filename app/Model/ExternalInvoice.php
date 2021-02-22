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

	public static function fnGetinvoiceTotalValue($request,$date_month,$singlesearchtxt,$username,$startdate,$enddate,$filter) {

		if ($request->searchmethod == 1 || $request->searchmethod == 2 || $request->searchmethod == 3) {
			$filter = "";
		}

		if (!empty($request->searchmethod)) {
			$wherecondition = "";
			$Invoice = db::table('ext_invoice_registration AS main')
							->select('main.*','works.amount','works.work_specific','works.quantity','users.userName', DB::raw("(CASE 
								WHEN main.classification = 2 THEN 3
								ELSE 0
								END) AS orderbysent"))
							->leftJoin('ext_mstuser AS users', 'main.userId', '=', 'users.id')
							->leftJoin('extinv_work_amount_det AS works', 'main.invoiceId', '=', 'works.invoice_id')
							->WHERE('main.delFlg',0);

			if ($filter == "2") {
				$Invoice = $Invoice->where('main.classification',0)
									->where('main.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "3") {
				$Invoice = $Invoice->where('main.classification',1)
									 ->where('main.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "4") {
				$Invoice = $Invoice->where('main.classification',3)
									 ->where('main.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "5") {
				$Invoice = $Invoice->where('main.classification',2)
									->where('main.quot_date', 'LIKE', '%' . $date_month . '%');
			} else if ($filter == "1") {
				$Invoice = $Invoice->where('main.quot_date', 'LIKE', '%' . $date_month . '%');
			}

			if ($request->searchmethod == 3) {
				if (!empty($request->usernameclick)) {
						$Invoice = $Invoice->where('main.userId','LIKE','%'.$request->usernameclick.'%');
				}
			}

			if (!empty($invoiceno)) {
				$Invoice = $Invoice->where('main.invoiceId','LIKE','%'.$Invoiceno.'%');
			}

			if (!empty($startdate) && !empty($enddate)) {
				$Invoice = $Invoice->where('main.quot_date','>=',$startdate);
				$Invoice = $Invoice->where('main.quot_date','<=',$enddate);
			}

			if (!empty($startdate) && empty($enddate)) {
				$Invoice = $Invoice->where('main.quot_date','>=',$startdate);
			}

			if (empty($startdate) &&!empty($enddate)) {
				$Invoice = $Invoice->where('main.quot_date','<=',$enddate);
			}

			if ($request->searchmethod == 1) {
				$Invoice = $Invoice->where(function($joincont) use ($request) {
					$joincont->where('main.invoiceId', 'LIKE', '%' . trim($request->singlesearch) . '%')
							->orWhere('users.userName', 'LIKE', '%' . trim($request->singlesearch) . '%')
							->orWhere('main.projectName', 'LIKE', '%' . trim($request->singlesearch) . '%');
				});
			}

			if ($request->searchmethod == 2) {
				if (!empty($request->msearchusercode)) {
					$Invoice = $Invoice->where('main.invoiceId', 'LIKE', '%' . trim($request->msearchusercode) . '%');

				}

				if (!empty($request->msearchusers)) {
					$Invoice = $Invoice->where('users.userName', 'LIKE', '%' . trim($request->msearchusers) . '%');
				}

				if(!empty($request->msearchstdate) && !empty($request->msearcheddate)) {
					$Invoice = $Invoice->whereBetween('main.quot_date', [$request->msearchstdate, $request->msearcheddate]);
				}

				if(!empty($request->msearchstdate) && empty($request->msearcheddate)) {
					$Invoice = $Invoice->where(function($joincont) use ($request) {
						$joincont->where('main.quot_date', '>=', $request->msearchstdate);
					});
				}

				if(!empty($request->msearcheddate) && empty($request->msearchstdate)) {
					$Invoice = $Invoice->where('main.quot_date', '<=', $request->msearcheddate);
				}
			}

   			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Invoice = $Invoice->WHERE(function($joincont) use($accessDate) {
				$joincont->WHERE('main.quot_date', '>',$accessDate)
						->ORWHERE('accessFlg','=',1);

				});
			}
			// END ACCESS RIGHTS

			if ($request->checkdefault != 1) {
				$Invoice = $Invoice->groupBy('invoiceId')
									->orderByRaw("orderbysent ASC, invoiceId DESC")
					  				->paginate($request->plimit);
			} else {
				$Invoice = $Invoice->groupBy('invoiceId')
									->orderBy($request->invoicesort,$request->sortOrder)
									->paginate($request->plimit);
			}
		} else {

			$db = DB::connection('mysql');
			$Invoice = $db->TABLE($db->raw("(SELECT main.quot_date, main.id, main.userId,main.invoiceId, main.payment_date, main.delFlg, main.copyFlg, main.projectName, main.classification, main.createdBy, main.projectType, main.mailFlg, main.pdfFlg, main.tax, main.paid_status, works.amount, works.work_specific, works.quantity, works.unit_price, works.remarks, users.userName,
				(CASE WHEN main.classification = 2 THEN 3
        		ELSE 0
				END) AS orderbysent,main.totalval 
			FROM  ext_invoice_registration main
			left join extinv_work_amount_det works on works.invoice_id = main.invoiceId 
			left join ext_mstuser users on users.id = main.userId 
			WHERE main.delFlg = 0 AND main.quot_date LIKE '%$date_month%'
			GROUP BY invoiceId Order By invoiceId Asc, quot_date Asc) AS DDD"));

			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Invoice = $Invoice->WHERE(function($joincont) use($accessDate) {
					$joincont->WHERE('ext_invoice_registration.quot_date', '>',$accessDate)
							->ORWHERE('accessFlg','=',1);

				});

			}
			// END ACCESS RIGHTS

			if ($request->checkdefault != 1) {
				$Invoice = $Invoice->orderByRaw("orderbysent ASC, invoiceId DESC")
					  				->paginate($request->plimit);
				//->toSql();dd($Invoice);
			} else {
				$Invoice = $Invoice->orderBy($request->invoicesort,$request->sortOrder)
					  				->paginate($request->plimit);
			}
		}
		return $Invoice;
	}

	public static function updateClassification($request) {
		$data[] = [ 'classification' => $request->invoicestatus ];

		$update = DB::table('ext_invoice_registration')
					->where('id', $request->invoicestatusid)->update($data[0]);

		return $update;

	}
	
	public static function fnGenerateInvoiceID() {

		$result = DB::table('ext_invoice_registration')

						->SELECT('invoiceId')
						->orderBy('invoiceId', 'DESC')
						->limit(1)
						->get();

		$inv = "INV";
		if (count($result) == 0) {
			$invoiceId = $inv . "00001";
		} else {
			foreach ($result as $key => $value) {
				$invoice = intval(str_replace("INV", "", $value->invoiceId)) + 1;
				$invoiceId = $inv . substr("00000" . $invoice, -5);
			}
		}

		return $invoiceId;

	}

	public static function insExtInvoice($request,$invoiceId) {

		$tableamountcount = $request->rowCount;
		$accessrights = 0;
		$common_field =array("work_specific","quantity","unit_price","amount","remarks");

		if (isset($request->accessrights)) {
			$accessrights = $request->accessrights;
		}

 		$data[] =	[	
 						'invoiceId' =>  $invoiceId,
						'userId' => $request->userId,
						'projectName' => $request->projectName,
						'projectType' => $request->projectType,
						'quot_date' => $request->quot_date,
						'payment_date' => $request->payment_date,
						'tax' => $request->tax,
						'personalMark' => $request->personalMark,
						'memo' => $request->memo,
						'totalval' => $request->totval,
						'createdBy' => Auth::user()->username
					];

		for ($i = 1; $i <= 5 ; $i++) { // loop for notice insert
			$stat = 'special_ins'.$i;
			$note = 'note'.$i;
			array_push_asociate($data[0],$stat,$request->$note);
		}

		if (Auth::user()->userclassification == 4) {
			array_push_asociate($data[0],'accessFlg',$accessrights);
			$insert = DB::table('ext_invoice_registration')->insert($data[0]);
		} else {
			$insert = DB::table('ext_invoice_registration')->insert($data[0]);
		}

		// To get Id For Register Amount

		$result = DB::table('ext_invoice_registration')
						->SELECT('*')
						->orderBy('id', 'DESC')
						->limit(1)
						->get();

		if (isset($result[0])) {
			$lo = 0;
			for ($i = 1; $i <= $tableamountcount; $i++) { 
				$stat1 = 'work_specific'.$i;
				$stat3 = 'quantity'.$i;
				$stat4 = 'unit_price'.$i;
				$stat5 = 'amount'.$i;
				$stat6 = 'remarks'.$i;

				if ($request->$stat1 !=''|| $request->$stat3 !=''|| $request->$stat4 !=''
					|| $request->$stat5!=''|| $request->$stat6!='') {
					$amount_details[$lo] =   [
						'inv_primery_key_id' => $result[0]->id,
						'invoice_id' =>  $result[0]->invoiceId,
						'user_id' =>  $request->userId,
						'createdBy' => Auth::user()->username,
						'updatedBy' => Auth::user()->username,
						'delFlg' => 0,

					];
					array_push_asociate($amount_details[$lo],'work_specific',$request->$stat1);
					array_push_asociate($amount_details[$lo],'quantity',$request->$stat3);
					array_push_asociate($amount_details[$lo],'unit_price',$request->$stat4);
					array_push_asociate($amount_details[$lo],'amount',$request->$stat5);
					array_push_asociate($amount_details[$lo],'remarks',$request->$stat6);
					$lo++;
				} 
			}
			if (!empty($amount_details)) {
				$insert = DB::table('extinv_work_amount_det')->insert($amount_details);
			}
		}

		return $insert;
	}

	public static function updExtInvoice($request) {

		$tableamountcount = $request->rowCount;
		$accessrights = 0;
		$common_field =array("work_specific","quantity","unit_price","amount","remarks");

		if (isset($request->accessrights)) {
			$accessrights = $request->accessrights;
		}

 		$data[] =	[	
 						'invoiceId' =>  $request->invoiceId,
						'userId' => $request->userId,
						'projectName' => $request->projectName,
						'projectType' => $request->projectType,
						'quot_date' => $request->quot_date,
						'payment_date' => $request->payment_date,
						'tax' => $request->tax,
						'personalMark' => $request->personalMark,
						'memo' => $request->memo,
						'totalval' => $request->totval,
						'updatedBy' => Auth::user()->username
					];

		for ($i = 1; $i <= 5 ; $i++) { // loop for notice insert
			$stat = 'special_ins'.$i;
			array_push_asociate($data[0],$stat,$request->$stat);
		}

		if (Auth::user()->userclassification == 4) {
			array_push_asociate($data[0],'accessFlg',$accessrights);
			$update = DB::table('ext_invoice_registration')->where('id', $request->editid)->update($data[0]);
		} else {
			$update = DB::table('ext_invoice_registration')->where('id', $request->editid)->update($data[0]);
		}

		// New Table Update

		$deldetails = DB::table('extinv_work_amount_det')

						->WHERE('inv_primery_key_id', '=', $request->editid)

						->DELETE();

		$lo = 0;

		for ($i = 1; $i <= $tableamountcount; $i++) { 

			$stat1 = 'work_specific'.$i;
			$stat3 = 'quantity'.$i;
			$stat4 = 'unit_price'.$i;
			$stat5 = 'amount'.$i;
			$stat6 = 'remarks'.$i;

			if ($request->$stat1 !=''|| $request->$stat3 !=''|| $request->$stat4 !=''||
				$request->$stat5!=''|| $request->$stat6!='') {

				$amount_details[$lo] =   [

					'inv_primery_key_id' => $request->editid,
					'invoice_id' =>  $request->invoiceId,
					'user_id' =>  $request->userId,
					'createdBy' => Auth::user()->username,
					'updatedBy' => Auth::user()->username,
					'delFlg' => 0,

				];

				array_push_asociate($amount_details[$lo], 'work_specific', $request->$stat1);
				array_push_asociate($amount_details[$lo], 'quantity', $request->$stat3);
				array_push_asociate($amount_details[$lo], 'unit_price', $request->$stat4);
				array_push_asociate($amount_details[$lo], 'amount', $request->$stat5);
				array_push_asociate($amount_details[$lo], 'remarks', $request->$stat6);
				$lo++;

			} 

		}

		if (!empty($amount_details)) {
			$insert = DB::table('extinv_work_amount_det')->insert($amount_details);
		}

		return $update;
	}

	public static function getUserDetails($request) {

		$result = DB::table('ext_mstuser')

						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->LISTS('userName','id');

		return $result;

	}

	public static function getProjectType($request) {

		$certificateName = DB::TABLE('dev_estimatesetting')

							->SELECT('*')
							->WHERE('delFlg', '=', 0)
							->LISTS('ProjectType','id');

		return $certificateName;

	}


	public static function getbankdetails($userId) {

		$result= DB::table('ext_mstuser')

						->SELECT('*')
						->WHERE('id', '=', $userId)
						->WHERE('delFlg', '=', 0)
						->GET();

		return $result;

	}

	public static function fnGetInvoiceWorkDtls($id) {

		$query = DB::TABLE('extinv_work_amount_det')

						->SELECT('*')
						->WHERE('inv_primery_key_id', $id)
						->get();

		return $query;	

	}

	public static function fnGetinvoiceUserData($id){

		$db = DB::connection('mysql');

		$query = $db->TABLE(
			$db->raw("(SELECT ext_invoice_registration.id, invoiceId, userId, projectName, projectType, tax, quot_date, totalval, special_ins1, special_ins2, special_ins3, special_ins4, special_ins5, payment_date, personalMark, paid_status, pdfFlg, mailFlg, accessFlg, classification, memo, copyFlg, inv_primery_key_id, work_specific, quantity, amount, unit_price, remarks, ext_mstuser.userName FROM ext_invoice_registration

				LEFT JOIN extinv_work_amount_det ON extinv_work_amount_det.inv_primery_key_id = ext_invoice_registration.id

				LEFT JOIN ext_mstuser ON ext_mstuser.id = ext_invoice_registration.userId 

				WHERE ext_invoice_registration.id = '$id') as tb1"))

				->get();

				// ->toSql();// dd($query);

		return $query;

	}

	public static function fnGetinvoiceTotVal($request,$date_month) {

		$Invoice = db::table('ext_invoice_registration')
						->select('ext_invoice_registration.*', 
							DB::raw("(CASE 
								WHEN ext_invoice_registration.classification = 2 THEN 3
								ELSE 0
								END) AS orderbysent"))
							->WHERE('quot_date','LIKE','%'.$date_month.'%')
							->WHERE('delFlg',0);

			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Invoice = $Invoice->WHERE('quot_date', '>', $accessDate);
			}
			// END ACCESS RIGHTS

			$Invoice = $Invoice->orderByRaw("orderbysent ASC, invoiceId DESC")->get();

		return $Invoice;

	}

	public static function fnGetExtinvoiceDownload($request,$date_month) {

		$db = DB::connection('mysql');

		$ExtInvoice = $db->TABLE($db->raw("(SELECT main.quot_date, main.id, main.invoiceId, main.userId, main.payment_date, main.delFlg, main.copyFlg, main.projectName, main.CreatedBy, main.pdfFlg, main.projectType, main.mailFlg, main.paid_status, main.tax, main.classification, users.userName, users.bankKanaName, users.bankName, users.branchName, users.branchNo, users.accountNo, works.amount, works.work_specific, works.quantity, works.unit_price, works.remarks, main.totalval,
			(CASE WHEN main.classification = 2 THEN 3
				ELSE 0
				END) AS orderbysent
		FROM ext_invoice_registration main 
		LEFT JOIN extinv_work_amount_det works on main.invoiceId = works.invoice_id 
		LEFT JOIN ext_mstuser users ON main.userId = users.id 

		WHERE main.delFlg = 0 AND main.quot_date LIKE '%$date_month%'

		GROUP BY invoiceId Order By invoiceId Asc, quot_date Asc
			) AS DDD Order By invoiceId DESC"))

		->get();

		// ->toSql();dd($query);

		return $ExtInvoice;

	}

	// pdf download start

	public static function fnGetEstiamteDetailsPDFDownload($id) {

		$result = DB::table('ext_invoice_registration AS main')

						->SELECT('main.*', 'users.userName', 'users.bankKanaName', 'users.bankName', 'users.branchName', 'users.branchNo', 'users.accountNo', 'users.accountType')
						->leftJoin('ext_mstuser AS users', 'main.userId', '=', 'users.id')
						->WHERE('main.id', $id)
						->get();

		return $result;

	}

	public static function fnGetAmountDetails($id) {

		$result = DB::table('extinv_work_amount_det')

						->SELECT('id','inv_primery_key_id', 'invoice_id','work_specific', 'quantity', 'unit_price', 'amount', 'remarks')
						->WHERE('inv_primery_key_id', $id)
						->get();

		return $result;

	}

}

function array_push_asociate(&$array, $key, $value) {

	$array[$key] = $value;
	return $array;

}