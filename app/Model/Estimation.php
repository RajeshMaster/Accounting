<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Estimation extends Model {
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'mailStatus'");
		return $statement[0]->Auto_increment;
	}
	public static function fnGetProjectType($request) {
		$result= DB::table('dev_estimatesetting')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->WHERE('ProjectType', '!=', '')
						->lists('ProjectType','id');
		return $result;
	}
	public static function fnGetAccountPeriod($request) {
		$result= DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->get();
		return $result;
	}
	public static function updateClassification($request) {
		$data[] =   [
			'classification' => $request->estimatestatus
		];
		$update=DB::table('dev_estimate_registration')->where('id', $request->estimatestatusid)->update($data[0]);
		return $update;
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
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_estimate_registration WHERE del_flg = 0 AND (quot_date > '$from_date' AND quot_date < '$to_date')".$accessQuery."ORDER BY quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnGetEstimateRecordPrevious($from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND (quot_date >= '$to_date' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_estimate_registration WHERE del_flg = 0 AND (quot_date <= '$from_date' $conditionAppend) ORDER BY quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnGetEstimateRecordNext($to_date) {
		$result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_estimate_registration WHERE del_flg = 0 AND (quot_date >= '$to_date') ORDER BY quot_date ASC) as tbl1"))
			->get();
		return $result;
	}
	public static function fnGetEstimateDetails($request,$taxSearch,$projecttype,$search_flg,$datemonth, $singlesearchtxt, $estimateno, $companyname, $startdate, $enddate, $srt, $odr, $filter) {
		$Estimate = db::table('dev_estimate_registration')
					->select('dev_estimate_registration.*',
										DB::raw("(CASE 
        										WHEN dev_estimate_registration.classification = 2 THEN 3
        										ELSE 0
    											END) AS orderbysent"),
										DB::raw("(SELECT format(SUM(REPLACE(amount, ',', '')),0) FROM tbl_estimate_work_details WHERE estimate_id = dev_estimate_registration.user_id) AS totalval"))
									->WHERE('del_flg',0);
					if ($search_flg =="1") {
						$Estimate=$Estimate->WHERE('del_flg',0);
						$Estimate=$Estimate->WHERE(function($joincont) use($singlesearchtxt) {
                           $joincont->WHERE('quot_date','LIKE','%'.trim($singlesearchtxt).'%')
                            ->ORWHERE('user_id','LIKE','%'.trim($singlesearchtxt).'%')
							->ORWHERE('company_name','LIKE','%'.trim($singlesearchtxt).'%')
							->ORWHERE('project_type_selection','LIKE','%'.trim($singlesearchtxt).'%')								
							->ORWHERE('tax','LIKE','%'.trim($singlesearchtxt).'%');
                            });
					} else if ($filter == "2") {
						$Estimate = $Estimate->where('del_flg',0)
													->where('classification',0)
													->where('quot_date','LIKE','%'.$datemonth.'%');
					} else if ($filter == "3") {
						$Estimate = $Estimate->where('del_flg',0)
													->where('classification',1)
													->where('quot_date','LIKE','%'.$datemonth.'%');
					} else if ($filter == "4") {
						$Estimate = $Estimate->where('del_flg',0)
													->where('classification',3)
													->where('quot_date','LIKE','%'.$datemonth.'%');
					} else if ($filter == "5") {
						$Estimate = $Estimate->where('del_flg',0)
													->where('classification',2)
													->where('quot_date','LIKE','%'.$datemonth.'%');
					} else if($request->hideyearbar=="0") {
						$Estimate = $Estimate->where('del_flg',0)
											->where('quot_date','LIKE','%'.$datemonth.'%');
					}
					if (!empty($estimateno)) {
						$Estimate = $Estimate->where('user_id','LIKE','%'.trim($estimateno).'%');
					}
					if ((!empty($companyname))||($companyname=="0")) {
						$Estimate = $Estimate->where('company_name','LIKE','%'.trim($companyname).'%');
					}
					if (!empty($projecttype)) {
						if($projecttype=="b"){
							$projecttype="";
						}
						$Estimate = $Estimate->where('project_type_selection','LIKE','%'.$projecttype.'%');
					}
					if (!empty($taxSearch)) {
						if($taxSearch=="3"){
							$taxSearch="";
						}
						$Estimate = $Estimate->where('tax','LIKE','%'.trim($taxSearch).'%');
					}
					if (!empty($startdate) && !empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','>=',$startdate);
						$Estimate = $Estimate->where('quot_date','<=',$enddate);
					}
					if (!empty($startdate) && empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','>=',$startdate);
					}
					if (empty($startdate) &&!empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','<=',$enddate);
					}
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate=$Estimate->WHERE(function($joincont) use($accessDate) {
                           $joincont->WHERE('quot_date', '>', $accessDate)
                            		->ORWHERE('accessFlg',1);
                            });
				}
				// END ACCESS RIGHTS
					if ($request->checkdefault != 1) {
						$Estimate = $Estimate->orderByRaw("orderbysent ASC, user_id DESC")
							  	->paginate($request->plimit);
					} else {
						$Estimate = $Estimate->orderBy($srt, $odr)
							  	->paginate($request->plimit);
					}
		return $Estimate;
	}
	public static function fnGetEstimateTotalValue($request,$taxSearch,$date,$search_flg,$projecttype, $singlesearchtxt, $estimateno, $companyname, $startdate, $enddate, $srt, $odr) {
		if (!empty($search_flg)) {
		$Estimate = db::table('dev_estimate_registration')
					->select(DB::raw("SUM(REPLACE(totalval, ',', '' )) totalal"),
							DB::raw("(SELECT format(SUM(REPLACE(amount, ',', '')),0) FROM tbl_estimate_work_details WHERE estimate_id = dev_estimate_registration.user_id) AS totalval")
						);
						$Estimate = $Estimate->where('del_flg',0);
					if (!empty($estimateno)) {
						$Estimate = $Estimate->where('user_id','LIKE','%'.$estimateno.'%');
					}
					if (!empty($companyname)) {
						$Estimate = $Estimate->where('company_name','LIKE','%'.$companyname.'%');
					}
					if (!empty($projecttype)) {
						if($projecttype=="b"){
							$projecttype="";
						}
						$Estimate = $Estimate->where('project_type_selection','LIKE','%'.$projecttype.'%');
					}
					if (!empty($taxSearch)) {
						if($taxSearch=="3"){
							$taxSearch="";
						}
						$Estimate = $Estimate->where('tax','LIKE','%'.$taxSearch.'%');
					}
					if (!empty($startdate) && !empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','>=',$startdate);
						$Estimate = $Estimate->where('quot_date','<=',$enddate);
					}
					if (!empty($startdate) && empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','>=',$startdate);
					}
					if (empty($startdate) &&!empty($enddate)) {
						$Estimate = $Estimate->where('quot_date','<=',$enddate);
					}
		} else {
			$Estimate = db::table('dev_estimate_registration')
					->select(DB::raw("SUM(REPLACE(totalval, ',','')) totalal"),
							DB::raw("(SELECT format(SUM(REPLACE(amount, ',', '')),0) FROM tbl_estimate_work_details WHERE estimate_id = dev_estimate_registration.user_id) AS totalval"));
						$Estimate = $Estimate->where('del_flg',0)
						                            ->Where('quot_date', 'LIKE', '%'.$date.'%');
		}
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$Estimate = $Estimate->WHERE('quot_date', '>', $accessDate)
										->ORWHERE('accessFlg','=',1);
				}
				// END ACCESS RIGHTS
		$Estimate = $Estimate->orderBy($srt, $odr)
					  	->paginate($request->plimit);
							// ->get();
							// ->toSql();
							// dd($Estimate);
		return $Estimate;
	}
	//addedit
	public static function fnGetCustomerDetails($request) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->limit(5)
						->orderBy('id', 'DESC')
						->lists('customer_name','id');
		return $result;
	}
	public static function fnexistingcustomer($custarray) {
		$result= DB::table('mst_customerdetail')
						->SELECT('*')
						->WHERE('delFlg', '=', 0) 
						->orderBy('romaji', 'ASC')
						->lists('customer_name','id');
		return $result;
	}
	public static function fnGetBranchDetails($customerid) {
		$result = DB::table('mst_branchdetails')
						->select('mst_branchdetails.branch_name','mst_branchdetails.id')
						->leftjoin('mst_customerdetail', 'mst_branchdetails.customer_id', '=', 'mst_customerdetail.customer_id')
						->where('mst_customerdetail.id',$customerid)
						->orderBy('branch_id', 'ASC')
						// ->lists('branch_name','id');
						->get();
							// ->toSql();
							// dd($Estimate);
		return $result;
	}
	public static function fnGenerateEstimateID() {
		$result= DB::table('dev_estimate_registration')
						->SELECT('user_id')
						->orderBy('user_id', 'DESC')
						->limit(1)
						->get();
		$cmn = "EST";
		if (count($result) == 0) {
			$id = $cmn . "00001";
		} else {
			foreach ($result as $key => $value) {
				$g_id = intval(str_replace("EST", "", $value->user_id)) + 1;
				$id = $cmn . substr("00000" . $g_id, -5);
			}
		}
		return $id;
	}
	public static function fnInsertEstimates($request,$code) {
		$tblrowcnt=$request->rowCount;
		$tableamountcount = $request->tableamountcount;
		$tablespecialcount = $request->tablespecialcount;
		$accessFlg = array();
		$field = "";
		$fieldval = "";
		$common_field = array("work_specific", "quantity", "unit_price", "amount", "remarks");

 		$data[] =   [
       		'id' => '',
			'user_id' => $code,
			'trading_selection' => 0,
			'trading_destination_selection' => $request->trading_destination_sel,
			'branch_selection' => $request->branchname_sel,
			'project_personal' => $request->projectpersonal,
			'project_name' => $request->project_name,
			'tax' => $request->tax,
			'project_type_selection' => $request->projecttype_sel,
			'tighten_month_selection' => $request->tighten_month_sel,
			'cutoff_date_selection' => $request->cutoff_date_sel,
			'quot_date' => $request->quot_date,
			'billing_month_selection' => $request->billing_month_sel,
			'billing_date_selection' => $request->billing_date_sel,
			'totalval' => $request->totval,
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
			array_push_assoc($data[0], $stat1, $request->$stat1);
			array_push_assoc($data[0], $stat2, $request->$stat2);
			array_push_assoc($data[0], $stat3, $request->$stat3);
			array_push_assoc($data[0], $stat4, $request->$stat4);
			array_push_assoc($data[0], $stat5, $request->$stat5);
		}
		for ($i=1; $i <=5 ; $i++) { // loop for notice insert
			$stat='special_ins'.$i;
			array_push_assoc($data[0], $stat, $request->$stat);
		}
		if (Auth::user()->userclassification == 4) {
			array_push_assoc($data[0], 'accessFlg', $request->accessrights);
			$insert=DB::table('dev_estimate_registration')->insertGetId($data[0]);
		} else {
			$insert=DB::table('dev_estimate_registration')->insertGetId($data[0]);
		}
		//New Table Insert
		$est=0;
		for ($i=1; $i <=$tblrowcnt ; $i++) { 
			$stat1='work_specific'.$i;
			$stat3='quantity'.$i;
			$stat4='unit_price'.$i;
			$stat5='amount'.$i;
			$stat6='remarks'.$i;
			if ($request->$stat1!=''||
			    $request->$stat3!=''||
				$request->$stat4!=''||
				$request->$stat5!=''||
				$request->$stat6!='') {
				$amount_details[$est] =   [
		       		'est_primary_key_id' => $insert,
					'estimate_id' => $code,
					'created_by' => Auth::user()->username,
					'updated_by' => Auth::user()->username,
					'created_time' => date('Y-m-d H-i-s'),
					'updated_time' => date('Y-m-d H-i-s'),
					'del_flg' => 0,
				];
				array_push_assoc($amount_details[$est], 'work_specific', $request->$stat1);
				array_push_assoc($amount_details[$est], 'quantity', $request->$stat3);
				array_push_assoc($amount_details[$est], 'unit_price', $request->$stat4);
				array_push_assoc($amount_details[$est], 'amount', $request->$stat5);
				if(!empty($request->$stat6)){
				array_push_assoc($amount_details[$est], 'remarks', $request->$stat6);
				} 
				else
				{
				$request->$stat6 = NULL;
				array_push_assoc($amount_details[$est], 'remarks', $request->$stat6);
				}
				$est++;
			} 
		}
		if (!empty($amount_details)) {
			$insert=DB::table('tbl_estimate_work_details')->insert($amount_details);
		}
		return $insert;
	}
    public static function fetchmaxid($request) {
        $db = DB::connection('mysql');
        $latDetails = $db->table('dev_estimate_registration')
                           ->max('id');
            return $latDetails;
    }
	public static function fnUpdateEstimates($request) {
		$tblrowcnt=$request->rowCount;
		$tableamountcount = $request->tableamountcount;
		$tablespecialcount = $request->tablespecialcount;
		$accessrights = 0;
		$field = "";
		$fieldval = "";
		$common_field = array("work_specific", "quantity", "unit_price", "amount", "remarks");
		if (isset($request->accessrights)) {
			$accessrights = $request->accessrights;
		}
 		$data[] =   [
			'trading_selection' => 0,
			'trading_destination_selection' => $request->trading_destination_sel,
			'branch_selection' => $request->branchname_sel,
			'project_personal' => $request->projectpersonal,
			'project_name' => $request->project_name,
			'tax' => $request->tax,
			'project_type_selection' => $request->projecttype_sel,
			'tighten_month_selection' => $request->tighten_month_sel,
			'cutoff_date_selection' => $request->cutoff_date_sel,
			'quot_date' => $request->quot_date,
			'billing_month_selection' => $request->billing_month_sel,
			'billing_date_selection' => $request->billing_date_sel,
			'totalval' => $request->totval,
			// 'created_time' => date('Y-m-d H-i-s'),
			'updated_time' => date('Y-m-d H-i-s'),
			'del_flg' => 0,
			'classification' => 0,
			'pdf_flg' => 0,
			'mailFlg' => 0,
			'memo' => $request->memo,
		];
		if($request->company_name!="") {
			array_push_assoc($data[0], 'company_name', $request->company_name);
		}

		for ($i=1; $i <=15 ; $i++) { // loop for common field
			$stat1='work_specific'.$i;
			$stat2='quantity'.$i;
			$stat3='unit_price'.$i;
			$stat4='amount'.$i;
			$stat5='remarks'.$i;
			array_push_assoc($data[0], $stat1, $request->$stat1);
			array_push_assoc($data[0], $stat2, $request->$stat2);
			array_push_assoc($data[0], $stat3, $request->$stat3);
			array_push_assoc($data[0], $stat4, $request->$stat4);
			array_push_assoc($data[0], $stat5, $request->$stat5);
		}
		for ($i=1; $i <=5 ; $i++) { // loop for notice update
			$stat='special_ins'.$i;
			array_push_assoc($data[0], $stat, $request->$stat);
		}
		if (Auth::user()->userclassification == 4) {
			array_push_assoc($data[0], 'accessFlg', $accessrights);
			$update=DB::table('dev_estimate_registration')->where('id', $request->editid)->update($data[0]);
		} else {
			$update=DB::table('dev_estimate_registration')->where('id', $request->editid)->update($data[0]);
		}
		$deldetails = DB::table('tbl_estimate_work_details')
						->WHERE('est_primary_key_id', '=', $request->editid)
						->DELETE();
		$lo = 0;
		for ($i=1; $i <=$tblrowcnt ; $i++) { 
			$stat1='work_specific'.$i;
			$stat3='quantity'.$i;
			$stat4='unit_price'.$i;
			$stat5='amount'.$i;
			$stat6='remarks'.$i;
			if ($request->$stat1!=''||
			    $request->$stat3!=''||
				$request->$stat4!=''||
				$request->$stat5!=''||
				$request->$stat6!='') {
				$amount_details[$lo] =   [
		       		'est_primary_key_id' => $request->editid,
					'estimate_id' =>  $request->userid,
					'created_by' => Auth::user()->username,
					'updated_by' => Auth::user()->username,
					'created_time' => date('Y-m-d H-i-s'),
					'updated_time' => date('Y-m-d H-i-s'),
					'del_flg' => 0,
				];
				array_push_assoc($amount_details[$lo], 'work_specific', $request->$stat1);
				array_push_assoc($amount_details[$lo], 'quantity', $request->$stat3);
				array_push_assoc($amount_details[$lo], 'unit_price', $request->$stat4);
				array_push_assoc($amount_details[$lo], 'amount', $request->$stat5);
				if(!empty($request->$stat6)){
				array_push_assoc($amount_details[$lo], 'remarks', $request->$stat6);
				} 
				else
				{
				$request->$stat6 = NULL;
				array_push_assoc($amount_details[$lo], 'remarks', $request->$stat6);
				}
				$lo++;
			} 
		}
		if (!empty($amount_details)) {
			$insert=DB::table('tbl_estimate_work_details')->insert($amount_details);
		}
		return $update;
	}
	public static function fnGetOtherDetails($request) {
		$result= DB::table('dev_estimate_others')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->lists('content','content');
		return $result;
	}
	public static function fnGetEstimateUserData($request) {
		$db=DB::connection('mysql');
		$estimate_id=$request->editid;
		$result=$db->TABLE($db->raw("(SELECT dev_estimate_registration.id,
																user_id,
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
																memo,
																tbl_estimate_work_details.est_primary_key_id,
											work_specific,
											quantity,
											amount,unit_price,
											remarks,(SELECT format(sum(replace(amount, ',', '')),0) 
								            FROM   tbl_estimate_work_details 
								            WHERE  est_primary_key_id = dev_estimate_registration.id) AS totalval
											FROM 
																dev_estimate_registration
															LEFT JOIN tbl_estimate_work_details ON tbl_estimate_work_details.est_primary_key_id=dev_estimate_registration.id
															WHERE dev_estimate_registration.id = '$estimate_id') as tb1"))
															// LEFT JOIN mst_customerdetail ON mst_customerdetail.customer_name=dev_estimate_registration.company_name   // by Sakthi
							// ->toSql();
							// dd($result);
							->get();
		return $result;
	}
	public static function fnGetEstimateUserDataADD($request) {
		$db=DB::connection('mysql');
		$estimate_id=$request->editid;
		$result=$db->TABLE($db->raw("(SELECT dev_estimate_registration.id,
																user_id,
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
																memo,
																pdf_flg,
																mailFlg,
																accessFlg,
																pdf_name,
																classification,
																tbl_estimate_work_details.est_primary_key_id,
																						work_specific,
																						quantity,
																						amount,
																						unit_price,
																						remarks,(SELECT format(sum(replace(amount, ',', '')),0) 
																			            FROM   tbl_estimate_work_details 
																			            WHERE  est_primary_key_id = dev_estimate_registration.id) AS totalval
																						FROM 
																dev_estimate_registration
															LEFT JOIN tbl_estimate_work_details ON tbl_estimate_work_details.est_primary_key_id=dev_estimate_registration.id
															WHERE dev_estimate_registration.id = '$estimate_id') as tb1"))
							->get();
		return $result;
	}
	public static function fnGetestimateUserDataForLoop($request){
		$query = DB::TABLE('tbl_estimate_work_details')
						->SELECT('*')
						->WHERE('tbl_estimate_work_details.est_primary_key_id', $request->editid)
						->get();
		return $query;
	}
	// pdf download start
	public static function fnGetEstiamteDetailsPDFDownload($id) {
		$result= DB::table('dev_estimate_registration')
						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();
		return $result;
		
	}
	public static function fnGetEstimateAmountDetails($id) {
		$result= DB::table('tbl_estimate_work_details')
						->SELECT('id',
								'est_primary_key_id',
								'estimate_id',
								'work_specific',
								'quantity',
								'unit_price',
								'amount',
								'remarks')
						->WHERE('est_primary_key_id', $id)
						->get();
		return $result;
		
	}
	   // query for Insert Table menu
	public static function estworkdet() {
		$result= DB::table('dev_estimate_registration')
						->SELECT('id', 'user_id', 
								'work_specific1',  'quantity1', 'unit_price1', 'amount1', 'remarks1',
								'work_specific2',  'quantity2', 'unit_price2', 'amount2', 'remarks2',
								'work_specific3',  'quantity3', 'unit_price3', 'amount3', 'remarks3',
								'work_specific4',  'quantity4', 'unit_price4', 'amount4', 'remarks4',
								'work_specific5',  'quantity5', 'unit_price5', 'amount5', 'remarks5',
								'work_specific6',  'quantity6', 'unit_price6', 'amount6', 'remarks6',
								'work_specific7',  'quantity7', 'unit_price7', 'amount7', 'remarks7',
								'work_specific8',  'quantity8', 'unit_price8', 'amount8', 'remarks8',
								'work_specific9',  'quantity9', 'unit_price9', 'amount9', 'remarks9',
								'work_specific10',  'quantity10', 'unit_price10','amount10', 'remarks10',
								'work_specific11',  'quantity11', 'unit_price11', 'amount11', 'remarks11',
								'work_specific12', 'quantity12', 'unit_price12', 'amount12', 'remarks12',
								'work_specific13',  'quantity13', 'unit_price13', 'amount13', 'remarks13',
								'work_specific14',  'quantity14', 'unit_price14', 'amount14', 'remarks14',
								'work_specific15',  'quantity15', 'unit_price15', 'amount15', 'remarks15'
								)
						->get();
		return $result;
		
	}
	// Insert Table menu
	public static function estnewtbl($estinsert) {
		$exist_cnt= DB::table('tbl_estimate_work_details')
						->SELECT('est_primary_key_id')
						->count();
		if ($exist_cnt == 0) {
		$lo = 0;
		$j = 0;
		foreach ($estinsert as $key => $value) {
			for ($i=1; $i <=15 ; $i++) {
				$stat1='work_specific'.$i;
				$stat2='quantity'.$i;
				$stat3='unit_price'.$i;
				$stat4='amount'.$i;
				$stat5='remarks'.$i;
					if (!empty($value->$stat1) || !empty($value->$stat2)||
						!empty($value->$stat3) || !empty($value->$stat4) || !empty($value->$stat5)) {
						$amount_details[$lo] =   [
				       		'est_primary_key_id' => $value->id,
							'estimate_id' => $value->user_id,
							'created_by' => Auth::user()->username,
							'updated_by' => Auth::user()->username,
							'created_time' => date('Y-m-d H-i-s'),
							'updated_time' => date('Y-m-d H-i-s'),
							'del_flg' => 0,
						];
						array_push_asociate($amount_details[$lo], 'work_specific', $value->$stat1);
						array_push_asociate($amount_details[$lo], 'quantity', $value->$stat2);
						array_push_asociate($amount_details[$lo], 'unit_price', $value->$stat3);
						array_push_asociate($amount_details[$lo], 'amount', $value->$stat4);
						if(!empty($value->$stat5)){
						array_push_asociate($amount_details[$lo], 'remarks', $value->$stat5);
						} 
						else
						{
						$value->$stat5 = NULL;
						array_push_asociate($amount_details[$lo], 'remarks', $value->$stat5);
						}
						$lo++;
					} 
				}
			}
			if(isset($amount_details)){
			foreach ($amount_details as $key => $value) {
			    $insert=DB::table('tbl_estimate_work_details')->insert($value);
			}
		}
		else{
			$amount_details=null;
		}
		}
		
	}
	public static function fnGetEstiamteDetailsPDF($request) {
		$result=DB::table('dev_estimate_registration')
                            ->SELECT('tbl_estimate_work_details.est_primary_key_id',
                                'tbl_estimate_work_details.work_specific',
                                'tbl_estimate_work_details.quantity',
                                'tbl_estimate_work_details.unit_price',
                                'tbl_estimate_work_details.amount',
                                'tbl_estimate_work_details.remarks',
                                'dev_estimate_registration.id',
                                'dev_estimate_registration.user_id',
                                'dev_estimate_registration.trading_destination_selection',
                                'dev_estimate_registration.quot_date',
                                'dev_estimate_registration.totalval',
                                'dev_estimate_registration.company_name',
                                'dev_estimate_registration.tax',
                                'dev_estimate_registration.special_ins1',
                                'dev_estimate_registration.special_ins2',
                                'dev_estimate_registration.special_ins3',
                                'dev_estimate_registration.special_ins4',
                                'dev_estimate_registration.special_ins5')
                          	->leftjoin('tbl_estimate_work_details', 'tbl_estimate_work_details.est_primary_key_id', '=','dev_estimate_registration.id')
                            ->WHERE('dev_estimate_registration.id', '=', $request->editid)
						->get();
		return $result;
	}
	// public static function fnGetWorkDetails($id) {
	// 	$result= DB::table('tbl_estimate_work_details')
	// 					->select('tbl_estimate_work_details.est_primary_key_id',
 //                                'tbl_estimate_work_details.work_specific',
 //                                'tbl_estimate_work_details.emp_id',
 //                                'tbl_estimate_work_details.quantity',
 //                                'tbl_estimate_work_details.unit_price',
 //                                'tbl_estimate_work_details.amount',
 //                                'tbl_estimate_work_details.remarks')
	// 					->leftjoin('dev_estimate_registration', 'dev_estimate_registration.id', '=', 'tbl_estimate_work_details.est_primary_key_id')
	// 					->where('tbl_estimate_work_details.est_primary_key_id',$id)
	// 					->get();
	// 	return $result;
	// }
	public static function fnGetEstimateUserDataPDF($id) {
		$result = DB::table('dev_estimate_registration')
						->select('dev_estimate_registration.*','mst_customerdetail.customer_id')
						->leftjoin('mst_customerdetail', 'mst_customerdetail.customer_name', '=', 'dev_estimate_registration.company_name')
						->where('dev_estimate_registration.id',$id)
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
	public static function fnGetInvoice($id) {
		$result= DB::table('dev_invoices_registration')
						->SELECT('*')
						->WHERE('estimate_id', '=', $id)
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
	public static function pdfflgset($user_id,$pdf_name) {
		$update=DB::table('dev_estimate_registration')
			->where('user_id', $user_id)
			->update(
				['pdf_flg' => 1,
				'pdf_name' => $pdf_name]
		);
    	return $update;
	}
	public static function fnGetallestimation($id,$datemonth,$tblname) {
		$result= DB::table($tblname)
						->SELECT('*')
						->WHERE('trading_destination_selection', '=', $id)
						->WHERE('pdf_flg', '=', 1)
						->WHERE('quot_date','LIKE','%'.$datemonth.'%')
						->get();
		return $result;
	}
	// pdf download end
	public static function getCompanyName($id,$tblname) {
		$result= DB::table($tblname)
						->SELECT('*')
						->WHERE('id', '=', $id)
						->get();
		return $result;
		
	}
	public static function fnGetallsendmails($id,$datemonth) {
		$result= DB::table('mailStatus')
						->SELECT('sendFlg')
						->WHERE('attachments','LIKE','%'.$id.'%')
						->WHERE('quot_date','LIKE','%'.$datemonth.'%')
						->get();
							// ->toSql();
							// dd($result);
		return $result;
	}
	public static function insertmailstatus($request,$tomailfinal,$ccnamefinal,$mailflg,$splitcclessmail,$splittolessmail) {
		if ($_FILES['file1']['name'] != "") {
			$request->pdfcnt = $request->pdfcnt + 1;
			if ($request->pdfNames != "") {
				$request->pdfNames = rtrim($request->pdfNames,',').','.$_FILES['file1']['name'];
			} else {
				$request->pdfNames = $_FILES['file1']['name'];
			}
		} else {
			$request->pdfNames = rtrim($request->pdfNames,',');
		}
		if ($request->nopassword == 1) {
			$request->pdfpassword = "";
		}
		if ($request->pdfcnt != 0) {
			$request->pdfpassword = $request->pdfpassword;
		} else {
			$request->pdfpassword = "";
		}
		$quot_date=$request->selYear."-".$request->selMonth;
		$insert=DB::table('mailStatus')->insert(
			['id' => '',
			'companyId' => $request->cust_id,
			// 'toMail' => implode(",", $tomailfinal),
			// 'toMail' => htmlspecialchars_decode($splittolessmail),
			// 'cc' => htmlspecialchars_decode($splitcclessmail),
			'toMail' => $splittolessmail,
			'cc' => $splitcclessmail,
			// 'cc' => implode(",", $ccnamefinal),
			'subject' => $request->subject,
			'content' => $request->content,
			'attachCount' => $request->pdfcnt,
			'attachments' => rtrim($request->attachments,','),
			'pdfNames' => $request->pdfNames,
			'pdfPassword' => $request->pdfpassword,
			'quot_date' => $quot_date,
			'sendFlg' => $mailflg,
			'createdBy' => Auth::user()->username,
			'updatedBy' => Auth::user()->username]
		);
		return $insert;
	}
	public static function updatemailstatus($request,$tomailfinal,$ccnamefinal,$mailflg) {
		$update="";
			$update=DB::table('mailStatus')
				->where('id', $request->mailstatusid)
				->update(['sendFlg' => 1,
			'toMail' => implode(",", $tomailfinal),
			'cc' => implode(",", $ccnamefinal),
						'subject' => $request->subject,
						'content' => $request->content,
						'updatedBy' => Auth::user()->username]
			);
		return $update;
	}
	public static function fngetMailContent($mailId) {
		$result= DB::table('mailContent')
						->SELECT('*')
						->WHERE('mailId',$mailId)
						->get();
		return $result;
	}
	public static function fnGetestimateTotVal($request,$date_month,$srt,$odr) {
			$Estimate = db::table('dev_estimate_registration')
									->select('*')
									->WHERE('quot_date','LIKE','%'.$date_month.'%')
									->WHERE('del_flg',0);	
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$Estimate = $Estimate->WHERE('quot_date', '>', $accessDate)
										->ORWHERE('accessFlg','=',1);
			}
			// END ACCESS RIGHTS
			$Estimate = $Estimate->orderBy($srt, $odr)
					  	->get();
		return $Estimate;
	}
	public static function fnGetEstimateprojecttype($id) {
		$result= DB::table('dev_estimatesetting')
						->SELECT('*')
						->WHERE('delFlg', '=', 0)
						->WHERE('id',$id)
						->get();
		return $result;
	}
	public static function mailflgupdate($estnames) {
		$update="";
		for ($i=0; $i < count($estnames) ; $i++) { 
			$update=DB::table('dev_estimate_registration')
				->where('user_id', $estnames[$i])
				->update(['mailFlg' => 1]
			);
		}
		return $update;
	}
	public static function getallcustomer($cust_id) {
		// $result= DB::table('mst_customerdetail')
		// 				->SELECT('*')
		// 				->WHERE('delflg', '=', 0)
		// 				->orderBy('customer_id', 'DESC')
		// 				->get();
		// return $result;
		
		$sql = "SELECT mst_customerdetail.id,mst_customerdetail.customer_id,mst_customerdetail.customer_name,mst_customerdetail.customer_id,
				IFNULL(mst_cus_inchargedetail.customer_id,UUID()) as inc_cusid,
				COUNT(mst_cus_inchargedetail.customer_id) AS Counts
				FROM mst_customerdetail 
				LEFT JOIN mst_cus_inchargedetail ON mst_customerdetail.customer_id=mst_cus_inchargedetail.customer_id 
				WHERE mst_customerdetail.delflg=0 AND mst_customerdetail.id = '$cust_id'
				Group BY inc_cusid
				Order by Counts DESC,mst_customerdetail.customer_id ASC";
		$result = DB::select($sql);
		return $result;
	}
	public static function getallbranch($customerid) {
		$result= DB::table('mst_branchdetails')
						->SELECT('*')
						->WHERE('customer_id', $customerid)
						->orderBy('customer_id', 'DESC')
						->get();
		return $result;
	}
	public static function getallincharge($customerid) {
		$result= DB::table('mst_cus_inchargedetail')
						->SELECT('*')
						->WHERE('customer_id', $customerid)
						->orderBy('id', 'DESC')
						->get();
		return $result;
	}
	public static function fngettomailfrmmailStatus($id) {
		$result= DB::table('mailStatus')
						->SELECT('toMail','cc')
						->orderBy('id', 'DESC')
						->WHERE('companyId', $id)
						->get();
		$toMailval = array();
		$mailarrayfinal = array();
		foreach ($result as $key => $value) {
			$toMailval[] = $value->toMail;
			if($value->cc!="") {
				$toMailval[] = $value->cc;
			}
		}
		$imp = implode(",",$toMailval);
		$exp = explode(",",$imp);
		$mailarrayfinal = array_unique($exp);
		return $mailarrayfinal;
	}
	public static function fngetothermailid($mailarray,$customer_id) {
		$result= DB::table('mst_cus_inchargedetail')
						->SELECT('incharge_email_id')
						->WHEREIN('incharge_email_id', $mailarray)
						->WHERE('customer_id', $customer_id)
						->groupby('incharge_email_id')
						->orderBy('id', 'DESC')
						->get();
		$toMailval = array();
		$mailarrayfinal = array();
		foreach ($result as $key => $value) {
			$toMailval[] = $value->incharge_email_id;
		}
		$imp = implode(",",$toMailval);
		$exp = explode(",",$imp);
		$mailarrayfinal = array_unique($exp);
		return $mailarrayfinal;
	}
	public static function fngetprevsendmail($id) {
		$result= DB::table('mailStatus')
						->SELECT('toMail','cc')
						->WHERE('companyId', $id)
						->orderBy('id', 'DESC')
						->limit(1)
						->get();
		return $result;
	}
	public static function fngetmailstatus($id) {
		$result= DB::table('mailStatus')
						->SELECT('*')
						->WHERE('id', $id)
						->get();
		return $result;
	}
	public static function getcustdetails($request) {
		$result = DB::table('mst_customerdetail')
						->select('mst_customerdetail.*','mst_cus_inchargedetail.incharge_name','mst_cus_inchargedetail.incharge_name','sysdesignationtypes.DesignationNM','sysdesignationtypes.DesignationNMJP')
						->leftjoin('mst_cus_inchargedetail', 'mst_customerdetail.customer_id', '=', 'mst_cus_inchargedetail.customer_id')
						->leftjoin('sysdesignationtypes', 'sysdesignationtypes.id', '=', 'mst_cus_inchargedetail.designation')
						->where('mst_customerdetail.id',$request->custid)
						->orderBy('mst_cus_inchargedetail.id', 'DESC')
						->limit(1)
						->get();
		return $result;
	}
	public static function getSignature($request) {
		$query = DB::table('mailsignature')
						->select('*')
						->where('user_ID', '=', Session::get('usercode'))
						->get();
		return $query;
	}
}
function array_push_assoc(&$array, $key, $value) {
    $array[$key] = $value;
    return $array;
}
