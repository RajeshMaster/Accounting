<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Expenses extends Model {
	public static function viewdetails($request) {
		$result=DB::table('dev_Expensess')
						->SELECT('*')
	                    ->get();
		return $result;
	}
	public static function fnGetAccountPeriodexp($request) {
		$accperiod=DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
	                    ->get();
	        return $accperiod;
	}
	public static function fnGetExpenseAllRecord() {
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM dev_expenses ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetExpenseRecord($request,$from_date, $to_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$from_date = Auth::user()->accessDate;
			$conditionAppend = "OR accessFlg = 1";
		}
		// END ACCESS RIGHTS
		$tbl_name = "dev_expenses";
		if ($request->mainmenu == "pettycash") {
			$tbl_name = "inv_pettycash";
		}
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date 
				FROM $tbl_name 
				WHERE (date > '$from_date' AND date < '$to_date') $conditionAppend 
				ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetExpenseRecordPrevious($request,$from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND (date >= '$to_date' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		$tbl_name = "dev_expenses";
		if ($request->mainmenu == "pettycash") {
			$tbl_name = "inv_pettycash";
		}
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM $tbl_name 
			WHERE (date <= '$from_date' $conditionAppend) ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetExpenseRecordNext($request,$to_date) {
		$tbl_name = "dev_expenses";
		if ($request->mainmenu == "pettycash") {
			$tbl_name = "inv_pettycash";
		}
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM $tbl_name 
			WHERE (date >= '$to_date') ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function pettycash_expenses($year="",$month="",$request) { 
		$query = DB::table('inv_pettycash')
						->select('inv_pettycash.*','emp.FirstName as FirstNames','emp.LastName as LastNames')
						->leftJoin('emp_mstemployees as emp','emp.Emp_ID','=','inv_pettycash.emp_ID')
						->WHERE('month', '=', $month)
						->WHERE('year', '=', $year);
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query=$query->WHERE(function($joincont) use($accessDate) {
                       $joincont->WHERE('date', '>', $accessDate)
                        		->ORWHERE('accessFlg','=','1');
                });
			}
			// END ACCESS RIGHTS
				$query = $query->orderBy('date','ASC')
						->orderBy('del_flg','ASC')
						->orderByRaw("CONCAT(inv_pettycash.Ins_DT,' ',inv_pettycash.Ins_TM) ASC")
	                    ->paginate($request->plimit);
	    return $query;
	}
	public static function pettycash_expenses1($year="",$month="",$request) { 
		$query = DB::table('inv_pettycash')
						->WHERE('month', '=', $month)
						->WHERE('year', '=', $year);
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query=$query->WHERE(function($joincont) use($accessDate) {
                       $joincont->WHERE('date', '>', $accessDate)
                        		->ORWHERE('accessFlg','=','1');
                });
			}
			// END ACCESS RIGHTS
						// ->orderBy('main_subject', 'is', 'null')
						// ->orderBy('main_subject','ASC') 
				$query = $query->orderBy('date','ASC')
						->orderBy('del_flg','ASC')
						->orderByRaw("CONCAT(Ins_DT,' ',Ins_TM) ASC")
	                    ->get();
	    return $query;
	}
	public static function balance_sal($year,$month,$request) { 
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT FORMAT(Sum(Case When `subject` = 'cash' 
        								Then amount Else 0 End),0) AS cashTotal,
									FORMAT(Sum(Case When `subject` != 'cash' 
        								Then amount Else 0 End),0) AS expensesTotal,
									FORMAT(Sum(Case When `subject` = 'cash' 
        								Then amount Else 0 End)-
									Sum(Case When `subject` != 'cash' 
         								Then amount Else 0 End),0) AS balance,
									FORMAT(Sum(Case When `subject` = 'cash' AND carryForwardFlg!=1
         								Then amount Else 0 End),0) AS thisMonth
									FROM `dev_expenses` WHERE
									year=$year AND month=$month) AS DDD"))
									->get();
		return $query;
	}
	public static function main_expenses1($year,$month,$request) { 
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (main.date > '$accessDate' OR main.accessFlg = 1)";
			$conditionAppendSalary = "AND sal.salaryDate>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT main.id,
							main.billno,
							NULL AS empNo,
							main.date,
							NULL AS salaryMonth,
							main.subject,
							main.details,
							main.bankname,
							main.bankaccno,
							main.amount,
							NULL AS charge,
							NULL AS bankId,
							main.file_dtl,
							main.remark_dtl,
							main.del_flg,
							main.transaction_flg,
							main.pettyFlg,
							main.carryForwardFlg,	
							main.currency_type,
							bank.Bank_NickName,
							main.salaryFlg,
							NULL AS FirstName,
							NULL AS LastName,
							banks.BankName AS bname,
							main.Ins_TM,
							main.transfer_flg,
							main.edit_flg,
							main.submit_flg,
							main.copy_month_day,
							main.year,
							main.month,
							main.banknameTransfer,
							main.bankaccnoTransfer,
							CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DT
							FROM dev_expenses main 
								LEFT JOIN mstbank bank on main.bankname=bank.BankName  and main.bankaccno=bank.AccNo
								LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
								WHERE main.year='$year' AND main.month='$month' AND main.del_flg!=2 
								$conditionAppend 
								AND salaryFlg!=1) 
							AS DDD"));
			$query = $query->ORDERBY('DDD.date', 'ASC')
							->ORDERBY('DDD.carryForwardFlg', 'DESC')
							->ORDERBY('DDD.Ins_DT', 'ASC')
							->ORDERBY('DDD.Ins_TM', 'ASC')
							->ORDERBY('DDD.del_flg', 'DESC')
							->ORDERBY('DDD.id')
							->get();
							// ->toSql();
							// dd($query);
		return $query;
	}
	public static function main_expenses($year,$month,$request) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (main.date > '$accessDate' OR main.accessFlg = 1)";
			$conditionAppendSalary = "AND sal.salaryDate > '$accessDate'";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT main.id,
							main.billno,
							main.emp_ID AS empNo,
							emp.FirstName AS FirstNames,
							emp.LastName AS LastNames,
							main.date,
							NULL AS salaryMonth,
							main.subject,
							main.details,
							main.bankname,
							main.bankaccno,
							main.amount,
							NULL AS charge,
							NULL AS bankId,
							main.file_dtl,
							main.remark_dtl,
							main.del_flg,
							main.transaction_flg,
							main.pettyFlg,
							main.carryForwardFlg,	
							main.currency_type,
							bank.Bank_NickName,
							main.salaryFlg,
							NULL AS FirstName,
							NULL AS LastName,
							banks.BankName AS bname,
							main.Ins_TM,
							main.transfer_flg,
							main.edit_flg,
							main.submit_flg,
							main.copy_month_day,
							main.year,
							main.month,
							main.banknameTransfer,
							main.bankaccnoTransfer,
							CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DT
							FROM dev_expenses main 
								LEFT JOIN mstbank bank on main.bankname=bank.BankName  and main.bankaccno=bank.AccNo
								LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
								LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
								WHERE main.year='$year' AND main.month='$month' AND main.del_flg!=2
								$conditionAppend 
								AND salaryFlg!=1) 
							AS DDD"));
			$query = $query->ORDERBY('DDD.date', 'ASC')
							->ORDERBY('DDD.carryForwardFlg', 'DESC')
							->ORDERBY('DDD.Ins_DT', 'ASC')
							->ORDERBY('DDD.Ins_TM', 'ASC')
							->ORDERBY('DDD.del_flg', 'DESC')
							->ORDERBY('DDD.id')
							->paginate($request->plimit);
							// ->toSql();
							// dd($query);
		return $query;
	}
	public static function selsubname($subject) {
		$sql = "SELECT Subject,Subject_jp FROM dev_expensesetting WHERE id='".$subject."'";
		$cards = DB::select($sql);
		$variable[0]="";
		foreach ($cards as $key => $value) {
			if(Session::get('languageval') == "jp") {
				$variable[0]= $value->Subject_jp;
			} else {
				$variable[0]= $value->Subject;
			}
		}
		return $variable;
	}
	public static function selsubnameforpettycash($subject) {
		$sql = "SELECT main_eng,main_jap FROM inv_set_transfermain WHERE id='".$subject."'";
		$cards = DB::select($sql);
		$variable[0]="";
		foreach ($cards as $key => $value) {
			if(Session::get('languageval') == "jp") {
				$variable[0]= $value->main_jap;
			} else {
				$variable[0]= $value->main_eng;
			}
		}
		return $variable;
	}
	public static function selsubsubjectname($details,$subject) {
		$sql = "SELECT sub_eng,sub_jap FROM inv_set_expensesub WHERE id='".$details."' AND mainid ='".$subject."'";
		$cards = DB::select($sql);
		$variable[0]="";
		foreach ($cards as $key => $value) {
			if(Session::get('languageval') == "jp") {
				$variable[0]= $value->sub_jap;
			} else {
				$variable[0]= $value->sub_eng;
			}
		}
		return $variable;
	}
	public static function selsubsubjectnameforpettycash($details,$subject) {
		$sql = "SELECT sub_eng,sub_jap FROM inv_set_transfersub WHERE id='".$details."' AND mainid ='".$subject."'";
		$cards = DB::select($sql);
		$variable[0]="";
		foreach ($cards as $key => $value) {
			if(Session::get('languageval') == "jp") {
				$variable[0]= $value->sub_jap;
			} else {
				$variable[0]= $value->sub_eng;
			}
		}
		return $variable;
	}
	public static function getkessanki() {
		$sql = "SELECT Accountperiod FROM dev_kessandetails";
		$cards = DB::select($sql);
		$variable[0]="";
		foreach ($cards as $key => $value) {
				$variable[0]= $value->Accountperiod;
		}
		return $variable;
	}
	public static function regGetBankDetails($bankname,$bankaccno) {
		$sql="SELECT * FROM mstbank LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
				where mstbank.BankName = '$bankname' AND mstbank.AccNo = '$bankaccno'";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function pettyexp_cashcount($delflg,$spldm) {
		$update=DB::table('inv_pettycash')
					->SELECT(DB::RAW("SUM(amount) as amount"))
					->where('del_flg', $delflg)
					->where('year', $spldm[0])
					->where('month', $spldm[1])
					->get();
					// ->toSql();
					// dd($update);
		$sql = $update[0]->amount;
		return $sql;
	}
	public static function getsoluexpansedetails($getyear,$getmonth) {
		$sql="SELECT SUM(amount) as SUM FROM dev_expenses WHERE del_flg = 1 AND year = '".$getyear."' AND month = '".$getmonth."' ";;
		$cards = DB::select($sql);
		return $cards;
	}
	public static function getsolutransferdetails($getyear,$getmonth) {
		$sql= "SELECT (SELECT SUM(amount) FROM dev_banktransfer WHERE del_flg = 0 AND year = '".$getyear."' AND month = '".$getmonth."') + (SELECT SUM(fee) FROM dev_banktransfer where del_flg = 0 AND year = '".$getyear."' AND month = '".$getmonth."') as result ";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function getautoincrement($request) {
		if($request->mainmenu == "pettycash") {
			$statement = DB::select("show table status like 'inv_pettycash'");
		} else {
			$statement = DB::select("show table status like 'dev_expenses'");
		}
		return $statement[0]->Auto_increment;
	}
	public static function fnsubmitexpense($month,$year) {
		$update=DB::table('dev_expenses')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 1]);
    	return $update;
	}
	public static function submitflgupdatetransfer($month,$year) {
		$update=DB::table('dev_banktransfer')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 1]);
    	return $update;
	}
	public static function submitflgupdatepettycash($month,$year) {
		$update=DB::table('inv_pettycash')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 1]);
    	return $update;
	}
	public static function fnrevertexpense($month,$year) {
		$update=DB::table('dev_expenses')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 0]);
    	return $update;
	}
	public static function fnreverttransfer($month,$year) {
		$update=DB::table('dev_banktransfer')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 0]);
    	return $update;
	}
	public static function fnrevertpetty($month,$year) {
		$update=DB::table('inv_pettycash')
            ->where('month', $month)
            ->where('year', $year)
            ->update(['submit_flg' => 0]);
    	return $update;
	}
	public static function getMainCategories() {
		$accperiod=DB::table('dev_expensesetting')
						->SELECT('*')
	                    ->get();
	        return $accperiod;
	}
	public static function getMainCategoriespettycash() {
		$accperiod=DB::table('inv_set_transfermain')
						->SELECT('*')
	                    ->get();
	        return $accperiod;
	}
	public static function getSubCategories($vid) {
		$accperiod=DB::table('inv_set_expensesub')
						->SELECT('*')
						->WHERE('mainid', '=', $vid)
	                    ->get();
	        return $accperiod;
	}
	public static function getSubCategoriespettycash($vid) {
		$accperiod=DB::table('inv_set_transfersub')
						->SELECT('*')
						->WHERE('mainid', '=', $vid)
	                    ->get();
	        return $accperiod;
	}
	public static function kessanki_ListView($request) {
		$accperiod=DB::table('dev_kessandetails')
						->SELECT('Accountperiod')
	                    ->get();
	    $variable[0]="";
		foreach ($accperiod as $key => $value) {
				$variable[0]= $value->Accountperiod;
		}
		return $variable;
	}
	public static function expbillno_ListView($request,$getkessanki) {
		$getkessanki = self::getkessanki();
		if($request->mainmenu == "pettycash") {
			$accperiod=DB::table('inv_pettycash')
						->max('billno');
		} else {
			$accperiod=DB::table('dev_expenses')
						->max('billno');
		}
	    if (empty($accperiod)) {
	    		$bill_no="1000001";
	    } else {
	    		$bill_no=$accperiod+1;
	    }
	    return $bill_no;
	}
	public static function fnGetSubject($request) {
		if($request->mainmenu == "pettycash") {
			if(Session::get('languageval') == "jp") {
				$selectedField = "main_jap";
			} else {
				$selectedField = "main_eng";
			}
			$accperiod=DB::table('inv_set_transfermain')
						->SELECT('id',$selectedField)
						->where('delflg', '=', 0)
            			->where($selectedField, '!=', "")
	                    ->lists($selectedField,'id');
		} else {
			if(Session::get('languageval') == "jp") {
				$selectedField = "Subject_jp";
			} else {
				$selectedField = "Subject";
			}
			$accperiod=DB::table('dev_expensesetting')
						->SELECT('id',$selectedField)
						->where('delflg', '=', 0)
            			->where($selectedField, '!=', "")
	                    ->lists($selectedField,'id');
	    }
	        return $accperiod;
	}
	public static function fnfetchsubsubject($request) {
		if(Session::get('languageval') == "jp") {
			$selectedField = "sub_jap";
		} else {
			$selectedField = "sub_eng";
		}
		if($request->mainmenu == "pettycash") {
			$accperiod=DB::table('inv_set_transfersub')
						->SELECT('id',$selectedField,'mainid')
						->where('delflg',0)
            			->where('mainid',$request->mainid)
	                    ->get();
		} else {
			$accperiod=DB::table('inv_set_expensesub')
						->SELECT('id',$selectedField,'mainid')
						->where('delflg',0)
            			->where('mainid',$request->mainid)
	                    ->get();
		}
	        return $accperiod;
	}
	public static function fnGetExpenseBalCfd($yearMn) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT CshTot-ExpTot AS balance FROM
			(SELECT IFNULL(SUM(expTotal), 0) AS ExpTot,IFNULL(SUM(cashTotal), 0) AS CshTot FROM(SELECT date,amount,del_flg,transaction_flg,SUM(amount) AS expTotal,NULL AS cashTotal 
			FROM dev_expenses WHERE date LIKE '%$yearMn%' AND del_flg=1
			UNION ALL
			SELECT date,amount,del_flg,transaction_flg,NULL as expTotal,SUM(amount) AS cashTotal FROM dev_expenses 
			WHERE date LIKE '%$yearMn%' AND del_flg=2) AS bal)
			AS balance) as tbl1"))
			->get();
			$data=array();
			foreach ($query as $key => $value) {
				$data['balance'] = $value->balance;
			}
		return $data;
	}
	public static function fnGetcarryForward($month,$year) {
		// echo $month; echo "<BR>";
		// echo $year; echo "<BR>";
		$query=DB::table('dev_expenses')
					->select('*')
					->where('month', $month)
					->where('year', $year)
					->where('carryForwardFlg','=',1)
					->get();
		return $query;
	}
	public static function fnInsertPreBalance($yearmnarray, $db_year_monthfullarray,$request) {
		$loop = count($yearmnarray);
		for ($i=0; $i < $loop ; $i++) {
			$expall_query = Expenses::fnGetExpenseBalCfd($db_year_monthfullarray[$i]);
			$request->date = $yearmnarray[$i]."-01";
			$request->amount_type = 0;
			$request->amount = $expall_query['balance'];
			if ($expall_query['balance'] < 0) {
				$request->transtype = 2;
			} else {
				$request->transtype = 1;
			}
			$spldm = explode('-', $request->date);
			$value = Expenses::fnGetcarryForward($spldm[1],$spldm[0]); 
			$carry_forwflg = count($value);
			foreach ($value as $key => $valuess) {
				$request->id = $valuess->id;
			}
			if($carry_forwflg < 1) {
				self::addcash($request,'1','','');
			} else { 
				self::updatecash($request);
			}
		}
	}
	public static function fnadddatatodatabase($request,$filename,$checkSubmitCount=null,$expbillno) {
		$accessFlg = 0;
		$receipt = 0;
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount);
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		if (isset($request->receipt)) {
			$receipt = 1;
		}
		$spldm = explode('-', $request->date);
		$valueget = self:: getexpbillno($request,10);
		if($request->mainmenu == "pettycash") {
			$insert=DB::table('inv_pettycash')
							->insert(
						    ['id'	=>	'', 
							'billno'	=>	$expbillno, 
							'date' => $request->date, 
							'emp_ID' => $request->emp_IDs, 
							'main_subject' => $request->mainsubject,
							'sub_subject' => $request->subsubject,
							'currency_type'	=>	'',
							'amount'	=>	$amount,
							'receipt' => $receipt,
							'file_dtl' => $filename,
							'remark_dtl'	=>	$request->remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'	=>	0,
							'del_flg' => 1,
							'submit_flg'	=>	$sumitedInsert,
							'edit_flg'	=>	$sumitedInsert,
							'accessFlg'	=>	$accessFlg,
							'user_id'	=>	Session::get('userid'),
							'Ins_DT' => date('Y-m-d'),
							'Ins_TM' => date('H:i:s'),
							'CreatedBy' => $name,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
		} else {
			$insert=DB::table('dev_expenses')
							->insert(
						    ['id'	=>	'', 
							'billno'	=>	$expbillno, 
							'emp_ID' => $request->emp_IDs, 
							'date' => $request->date, 
							'subject' => $request->mainsubject,
							'details' => $request->subsubject,
							'currency_type'	=>	'',
							'amount'	=>	$amount,
							'file_dtl' => $filename,
							'receipt' => $receipt,
							'remark_dtl'	=>	$request->remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'	=>	0,
							'del_flg' => 1,
							'submit_flg'	=>	$sumitedInsert,
							'edit_flg'	=>	$sumitedInsert,
							'accessFlg'	=>	$accessFlg,
							'user_id'	=>	Session::get('userid'),
							'Ins_DT' => date('Y-m-d'),
							'Ins_TM' => date('H:i:s'),
							'CreatedBy' => $name,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
		}
			return $insert;
	}
	public static function fneditdatatodatabase($request,$filename) {
		$accessFlg = 0;
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount);
		$spldm = explode('-', $request->date);
		$valueget = self:: checkSubmited($spldm);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		if($filename != "") {
			$files = $filename;
		} else {
			$files = $request->pdffiles;
		}
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		if($request->mainmenu == "pettycash") {
			$update=DB::table('inv_pettycash')
						->where('id', $request->id)
						->update(
							['billno'	=>	$request->billno, 
							'date' => $request->date, 
							'main_subject' => $request->mainsubject,
							'sub_subject' => $request->subsubject,
							'currency_type'	=>	'',
							'amount'	=>	$amount,
							'file_dtl' => $files,
							'remark_dtl'	=>	$request->remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'del_flg' => 1,
							'edit_flg'	=>	$editflg,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
			// ACCESS RIGHTS
				if (Auth::user()->userclassification == 4) {
					$update=DB::table('inv_pettycash')
						->where('id', $request->id)
						->update(['accessFlg'	=>	$accessFlg]);
				}
			//END ACCESS RIGHTS
		} else {
			$update=DB::table('dev_expenses')
						->where('id', $request->id)
						->update(
							['billno'	=>	$request->billno, 
							'date' => $request->date, 
							'subject' => $request->mainsubject,
							'details' => $request->subsubject,
							'currency_type'	=>	'',
							'amount'	=>	$amount,
							'file_dtl' => $files,
							'remark_dtl'	=>	$request->remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'	=>	0,
							'del_flg' => 1,
							'edit_flg'	=>	$editflg,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
			// ACCESS RIGHTS
				if (Auth::user()->userclassification == 4) {
					$update=DB::table('dev_expenses')
						->where('id', $request->id)
						->update(['accessFlg'	=>	$accessFlg]);
				}
			//END ACCESS RIGHTS
		}
    	return $update;
	}
	public static function fnGetCashEdit($request) {
		$tbl_name = "dev_expenses";
		if($request->mainmenu == "pettycash") {
			$tbl_name = "inv_pettycash";
		}
		$accperiod=DB::table($tbl_name)
						->SELECT('*')
						->WHERE('id', '=', $request->id)
	                    ->get();
	        return $accperiod;
	}
	public static function fnGetCashEditdetails($billno) {
		$sql="SELECT *,banks.BankName AS bname FROM dev_expenses main 
							LEFT JOIN mstbank bank on main.bankname=bank.BankName 
							LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
							where main.billno = '$billno'";
	    $cards = DB::select($sql);
		return $cards;
	}
	public static function fnexpensemultireg($multi_reg,$month,$year) {
		$sql = "SELECT mstbank.Bank_NickName,dev_expenses.*,dev_expensesetting.Subject AS mainSubject,dev_expensesetting.Subject_jp,inv_set_expensesub.sub_eng,
					inv_set_expensesub.sub_jap
					FROM dev_expenses 
					LEFT JOIN dev_expensesetting ON dev_expensesetting.id=dev_expenses.subject 
					LEFT JOIN inv_set_expensesub ON inv_set_expensesub.mainid=dev_expensesetting.id AND inv_set_expensesub.id=dev_expenses.details 
					LEFT JOIN mstbank ON mstbank.AccNo=dev_expenses.bankaccno AND mstbank.BankName=dev_expenses.bankname
					WHERE year='".$year."' AND month='".$month."' AND dev_expenses.id IN ($multi_reg) 
					ORDER BY date ASC,carryForwardFlg DESC;";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function checkSubmited($spldm) {
		$db = DB::connection('mysql');
		$sqlSelect = $db->table('dev_expenses')
					->SELECT('*')
					->where('submit_flg','=',1)
					->where('year','=',$spldm[0])
					->where('month','=',$spldm[1])
					->get();
		$submitcount = count($sqlSelect);
		return $submitcount;
	}
	public static function checkexpensesadd($spldm) {
		$db = DB::connection('mysql');
		$sqlSelect = $db->table('dev_expenses')
					->SELECT('*')
					->where('del_flg','=',1)
					->where('pettyFlg','=',1)
					->where('year','=',$spldm[0])
					->where('month','=',$spldm[1])
					->get();
		$submitcount = count($sqlSelect);
		return $submitcount;
	}
	public static function checkcashpettyadd($spldm) {
		$db = DB::connection('mysql');
		$sqlSelect = $db->table('dev_expenses')
					->SELECT('*')
					->where('del_flg','=',2)
					->where('pettyFlg','=',1)
					->where('year','=',$spldm[0])
					->where('month','=',$spldm[1])
					->get();
		$submitcount = count($sqlSelect);
		return $submitcount;
	}
	public static function getexpbillno($accperiod) {
		$db = DB::connection('mysql');
		$sql="SELECT max(billno) AS tot FROM dev_expenses";
		$query = DB::select($sql);
		$tot[0] = "";
		if (empty($query[0])) {
			$bill_no=$accperiod.(str_pad($query[0]->tot+1,'5','0',STR_PAD_LEFT));
		} else {
			$bill_no=$query[0]->tot+1;
		}
		return $bill_no;
	}
	public static function expensesmultipleregister($request,$checkSubmitCount) {
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$spldm = explode('-', $request->date);
		$valueget = self:: getexpbillno(10);
		$remarks = "remarks".$request->day;
		$slt_subject = "subjectcode_".$request->day;
		$slt_subsubject = "details_".$request->day;
		$expenses = "expenses".$request->day;
		$amounts = str_replace(",", "", $request->$expenses);
		$insert=DB::table('dev_expenses')
						->insert(
							array(
								'id'	=>	'', 
								'billno'	=>	$valueget, 
								'date' => $request->date, 
								'subject' => $request->$slt_subject,
								'details' => $request->$slt_subsubject,
								'currency_type'	=>	0,
								'amount'	=>	$amounts,
								'file_dtl' => '',
								'remark_dtl'	=>	$request->$remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'copy_month_day'	=>	0,
								'del_flg' => 1,
								'submit_flg'	=>	$sumitedInsert,
								'edit_flg'	=>	$sumitedInsert,
								'accessFlg'	=>	$accessFlg,
								'user_id'	=>	Session::get('userid'),
								'Ins_DT' => date('Y-m-d'),
								'Ins_TM' => date('H:i:s'),
								'CreatedBy' => $name,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
			return $insert;
	}
	public static function cashmultipleregister($request,$checkSubmitCount) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$spldm = explode('-', $request->date);
		$valueget = self:: getexpbillno(10);
		$transaction = 'transaction_'.$request->day;
		$cash = 'cash'.$request->day;
		$amounts = str_replace(",", "", $request->$cash);
		$slt_bkbranch = "Bank_No_".$request->day."-"."bankaccno_".$request->day;
		$bankname = "Bank_No_".$request->day;
		$bankaccno = "bankaccno_".$request->day;
		$cash = "cash".$request->day;
		$remarks = "remarks".$request->day;
		$transbankname1 = "";
		$transbankname2 = "";
		if($transaction == 3){
			$loop = '2';
		} else {
			$loop = '1';
			$transfer = "";
		}
		for ($i=0; $i<$loop; $i++) { 
				if($i == 0){
					$txt_amount =preg_replace("/,/", "", $cash);
					$bankacc = explode('-', $request->$slt_bkbranch);
					if($transaction == 3 && $i == 0){
						$bankacc1 = explode('-', $request->$slt_bkbranchtransfer);
						$transbankname1 = $bankacc1[0];
						$transbankname2 = $bankacc1[1];
						$transfer = '1';
					}
				} else if($i == 1) {
					$transbankname1 = "";
					$transbankname2 = "";
					$valueget= $valueget+1;
					$txt_amount ="-".preg_replace("/,/", "",$cash);
					$bankacc = explode('-', $request->$slt_bkbranchtransfer);
					// $bankacc = split('-', substr($str, strrpos($str, ' ') + 1));
					$transfer = '2';
				}
			$db = DB::connection('mysql');
			$insert=DB::table('dev_expenses')
						->insert(
							array(
								'id'	=>	'', 
								'billno'	=>	$valueget, 
								'date' => $request->date, 
								'subject' => 'Cash',
								'details' => '',
								'bankname'	=>	$request->$bankname,
								'bankaccno'=>	$request->$bankaccno,
								'currency_type'	=>	'',
								'amount'	=>	$amounts,
								'file_dtl' => '',
								'remark_dtl'	=>	$request->$remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'copy_month_day'	=>	0,
								'del_flg' => 2,
								'transaction_flg'	=>	$request->$transaction,
								'submit_flg'	=>	$sumitedInsert,
								'edit_flg'	=>	$sumitedInsert,
								'accessFlg'	=>	$accessFlg,
								'user_id'	=>	Session::get('userid'),
								'carryForwardFlg'	=>	'',
								'transfer_flg'	=>	$transfer,
								'banknameTransfer'	=>	$transbankname1,
								'bankaccnoTransfer'	=>	$transbankname2,
								'Ins_DT' => date('Y-m-d'),
								'Ins_TM' => date('H:i:s'),
								'CreatedBy' => $name,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
			}
		return $insert;
	}
	public static function fetchbanknames($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->orderBy('mstbank.id','ASC')
						->lists('BANKNAME','ID');
						// ->toSql();
		return $query;
	}
	public static function getaccno($accno) {
		$db = DB::connection('mysql');
		$query = $db->table('mstbank')
					->SELECT('*')
					->where('id','=',$accno)
					->get();
					// ->toSql();
					// dd($query);
		$acc = "";
		if (isset($query[0]->AccNo)) {
			$acc = $query[0]->AccNo;
		}
		return $acc;
	}
	public static function fnfetchmainsubject($request) {
		$accperiod=DB::table('mstbank')
						->SELECT('mstbank.*')
						// ->leftjoin('mstbanks', 'mstbanks.id','=', 'mstbank.BankName')
						->whereRaw("CONCAT(mstbank.BankName,'-', mstbank.AccNo)!= '$request->mainid'")
						// ->ORDERBY('mstbank.BankName','DESC')
	                    ->get();
	        return $accperiod;
	}
	public static function addcash($request,$carry,$checkSubmitCount=null,$expbillno) {
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$spldm = explode('-', $request->date);
		$valueget = self:: getexpbillno(10);
		$transbankname1 = "";
		$transbankname2 = "";
		$transaction = $request->transtype;
		$transfer = "";
		if($request->transtype == 3) {
			$loop = '2';
		} else {
			$loop = '1';
			$transfer = "";
		}
		if($request->mainmenu == "pettycash") {
			$bankacc = explode('-', $request->bank);
			$amounts = preg_replace("/,/", "",$request->amount);
				$insert=DB::table('inv_pettycash')
						->insert(
							array(
								'id'	=>	'', 
								'emp_ID' => $request->emp_IDs, 
								'billno'	=>	$expbillno, 
								'date' => $request->date, 
								'main_subject' => 'Cash',
								'sub_subject' => '',
								'bankname'	=>	$bankacc[0],
								'bankaccno'=>	$bankacc[1],
								'currency_type'	=>	'',
								'amount'	=>	$amounts,
								'file_dtl' => '',
								'remark_dtl'	=>	$request->remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'copy_month_day'	=>	0,
								'del_flg' => 2,
								'transaction_flg'	=>	$request->transtype,
								'submit_flg'	=>	$sumitedInsert,
								'edit_flg'	=>	$sumitedInsert,
								'accessFlg'	=>	$accessFlg,
								'user_id'	=>	Session::get('userid'),
								'Ins_DT' => date('Y-m-d'),
								'Ins_TM' => date('H:i:s'),
								'CreatedBy' => $name,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
		} else {
			$bankname1 = "";
			$bankname2 = "";
		for ($i=0; $i<$loop; $i++) { 
				if($i == 0){
					$txt_amount =preg_replace("/,/", "", $request->amount);
					if (isset($request->bank)) {
						$bankacc = explode('-', $request->bank);
						$bankname1 = $bankacc[0];
						$bankname2 = $bankacc[1];
					}
					if($request->transtype == 3 && $i == 0) {
						$transbank = explode('-', $request->transfer);
						$transbankname1 = $transbank[0];
						$transbankname2 = $transbank[1];
						$transfer = "1";
					}
				} else if($i == 1) {
					$transbankname1 = "";
					$transbankname2 = "";
					$valueget= $valueget+1;
					$txt_amount ="-".preg_replace("/,/", "",$request->amount);
					if (isset($request->transfer)) {
						$bankacc = explode('-', $request->transfer);
						$bankname1 = $bankacc[0];
						$bankname2 = $bankacc[1];
					}
					$transfer = 2;
				}
			$db = DB::connection('mysql');
			$insert=DB::table('dev_expenses')
						->insert(
							array(
								'id'	=>	'', 
								'emp_ID' => $request->emp_IDs, 
								'billno'	=>	$valueget, 
								'date' => $request->date, 
								'subject' => 'Cash',
								'details' => '',
								'bankname'	=>	$bankname1,
								'bankaccno'=>	$bankname2,
								'currency_type'	=>	'',
								'amount'	=>	$txt_amount,
								'file_dtl' => '',
								'remark_dtl'	=>	$request->remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'copy_month_day'	=>	0,
								'del_flg' => 2,
								'transaction_flg'	=>	$request->transtype,
								'submit_flg'	=>	$sumitedInsert,
								'edit_flg'	=>	$sumitedInsert,
								'accessFlg'	=>	$accessFlg,
								'user_id'	=>	Session::get('userid'),
								'carryForwardFlg'	=>	$carry,
								'transfer_flg'	=>	$transfer,
								'banknameTransfer'	=>	$transbankname1,
								'bankaccnoTransfer'	=>	$transbankname2,
								'Ins_DT' => date('Y-m-d'),
								'Ins_TM' => date('H:i:s'),
								'CreatedBy' => $name,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
			}
			}
		return $insert;
	}
	public static function addupdcash($request) {
		$update=DB::table('dev_expenses')
						->where('id', $request->id)
						->update(
							array(
								'copy_month_day'	=>	1
							)
						);
		return $update;
	}
	public static function updatecash($request) {
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$spldm = explode('-', $request->date);
		$valueget = self:: checkSubmited($spldm);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		$transaction = $request->transtype;
		$transbankname1 = "";
		$transbankname2 = "";
		if($transaction == 3){
			$loop = '2';
		} else {
			$loop = '1';
			$transfer = "";
		}
		if($request->mainmenu == "pettycash") {
			$bankacc = explode('-', $request->bank);
			$amounts = preg_replace("/,/", "",$request->amount);
				$update=DB::table('inv_pettycash')
						->where('id', $request->id)
						->update(
							array(
								'billno'	=>	$request->billno, 
								'date' => $request->date, 
								'bankname'	=>	$bankacc[0],
								'bankaccno'=>	$bankacc[1],
								'amount'	=>	$amounts,
								'remark_dtl'	=>	$request->remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'del_flg' => 2,
								'transaction_flg'	=>	$request->transtype,
								'edit_flg'	=>	$editflg,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
			// ACCESS RIGHTS
				if (Auth::user()->userclassification == 4) {
					$update=DB::table('inv_pettycash')
						->where('id', $request->id)
						->update(
							array('accessFlg'	=>	$accessFlg));
				}
			//END ACCESS RIGHTS
		} else {
			$bankname1 = "";
			$bankname2 = "";
		for ($i=0; $i<$loop; $i++) { 
			if($i == 0){
				$txt_amount =preg_replace("/,/", "", $request->amount);
				if (isset($request->bank)) {
					$bankacc = explode('-', $request->bank);
					$bankname1 = $bankacc[0];
					$bankname2 = $bankacc[1];
				}
				if($request->transtype == 3 && $i == 0) {
					$transbank = explode('-', $request->transfer);
					$transbankname1 = $transbank[0];
					$transbankname2 = $transbank[1];
					$transfer = "1";
				}
			} else if($i == 1) {
				$transbankname1 = "";
				$transbankname2 = "";
				$request->id= $request->id+1;
				$request->billno = $request->billno + 1;
				$txt_amount ="-".preg_replace("/,/", "",$request->amount);
				if (isset($request->transfer)) {
					$bankacc = explode('-', $request->transfer);
					$bankname1 = $bankacc[0];
					$bankname2 = $bankacc[1];
				}
				$transfer = 2;
			}
			$db = DB::connection('mysql');
			$update=DB::table('dev_expenses')
						->where('id', $request->id)
						->update(
							array(
								'billno'	=>	$request->billno, 
								'date' => $request->date, 
								'details' => '',
								'bankname'	=>	$bankname1,
								'bankaccno'=>	$bankname2,
								'currency_type'	=>	'',
								'amount'	=>	$txt_amount,
								'remark_dtl'	=>	$request->remarks,
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'del_flg' => 2,
								'transaction_flg'	=>	$request->transtype,
								'edit_flg'	=>	$editflg,
								'banknameTransfer'	=>	$transbankname1,
								'bankaccnoTransfer'	=>	$transbankname2,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
				// ACCESS RIGHTS
					if (Auth::user()->userclassification == 4) {
						$update=DB::table('dev_expenses')
							->where('id', $request->id)
							->update(
								array('accessFlg'	=>	$accessFlg));
					}
				//END ACCESS RIGHTS
			}
		}
		return $update;
	}
	public static function fnaddtodev($request,$filename,$checkSubmitCount=null,$expbillno,$delfg) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if ($checkSubmitCount > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		$insert_date = $request->date;
		$lastdate = date('Y-m-t', strtotime($insert_date)); 
		$spldm = explode('-', $request->date);
		$spldmval = explode('-', $insert_date);
		$expcashtot = self:: pettyexp_cashcount($delfg,$spldm);
		if($filename != "") {
			$files = $filename;
		} else {
			$files = $request->pdffiles;
		}
		$update=DB::table('dev_expenses')
						->where('pettyFlg', 1)
						->where('del_flg', $delfg)
						->where('year', $spldmval[0])
						->where('month', $spldmval[1])
						->update(
							array(
								'billno'	=> '', 
								'date' => $lastdate, 
								'subject' => '',
								'details' => '',
								'currency_type'	=>	'',
								'amount'	=>	$expcashtot,
								'file_dtl' => $files,
								'remark_dtl'	=>	'',
								'year'	=>	$spldmval[0],
								'month'	=>	$spldmval[1],
								'del_flg' => $delfg,
								'edit_flg'	=>	$editflg,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
		return $update;
	}
	public static function fnadduptodev($request,$filename,$checkSubmitCount=null,$expbillno,$delfg) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$insert_date = $request->date;
		$lastdate = date('Y-m-t', strtotime($insert_date)); 
		$spldm = explode('-', $request->date);
		$spldmval = explode('-', $insert_date);
		$expcashtot = self:: pettyexp_cashcount($delfg,$spldm);
		if($filename != "") {
			$files = $filename;
		} else {
			$files = $request->pdffiles;
		}
		$insert=DB::table('dev_expenses')
						->insert(
							array(
								'id'	=> '', 
								'billno'	=> '', 
								'date' => $lastdate, 
								'subject' => '',
								'details' => '',
								'currency_type'	=>	'',
								'amount' => $expcashtot,
								'file_dtl' => $files,
								'remark_dtl'	=>	'',
								'year'	=>	$spldm[0],
								'month'	=>	$spldm[1],
								'del_flg' => $delfg,
								'copy_month_day' => 0,
								'pettyFlg' => 1,
								'submit_flg'	=>	$sumitedInsert,
								'edit_flg'	=>	$sumitedInsert,
								'user_id'	=>	Session::get('userid'),
								'Ins_DT' => date('Y-m-d'),
								'Ins_TM' => date('H:i:s'),
								'CreatedBy' => $name,
								'Up_DT' => date('Y-m-d'),
								'UP_TM' => date('H:i:s'),
								'UpdatedBy' => $name
							)
						);
		return $insert;
	}
	public static function expenses_history_bankdetails($request,$mnsub,$subsub,$yr,$mnth) {
		$db = DB::connection('mysql');
		$wherecondition = "";
		$query = $db->table('dev_expenses as main')
					->SELECT('main.*')
					->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('main.bankname', '=', 'bank.BankName');
								$join->on('main.bankaccno', '=', 'bank.AccNo');
							})
					->leftJoin('mstbanks AS banks', 'banks.id', '=', 'bank.BankName');
		if($yr!=""&&$mnth!="") {
			$query = $query->where(function($joincont) use ($mnsub,$subsub,$yr,$mnth) {
				$joincont->where('banks.BankName','=',$mnsub)
							->where('main.bankaccno','=',$subsub)
							->where('main.year','=',$yr)
							->where('main.month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($mnsub,$subsub) {
				$joincont->where('banks.BankName','=',$mnsub)
							->where('main.bankaccno','=',$subsub);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('main.year','DESC')
						->orderBy('main.month','DESC')
						->orderBy('main.date','ASC')
						->paginate($request->plimit);
								// ->toSQL();dd($query);
		return $query;
	}
	public static function expenses_history_bankdetailspageview($request,$mnsub,$subsub,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$sql= "SELECT * FROM inv_pettycash ";
							
				if($yr!=""&&$mnth!="")
				{			
					$sql.="WHERE month = '$mnth' $conditionAppend AND year = '$yr' AND bankname = '$mnsub' AND bankaccno = '$subsub' ORDER BY year DESC , month DESC ,date ASC ";
				}
				else{
					$sql.="WHERE bankname = '$mnsub' $conditionAppend AND bankaccno = '$subsub' ORDER BY year DESC , month DESC ,date ASC ";					
				}
		$query = DB::select($sql);
		return $query;
	}
	public static function expenses_history_bankdetails_subSubjectpetty($request,$bname,$accno,$trans_flg,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$sql= "SELECT * FROM inv_pettycash ";
							
				if($yr!=""&&$mnth!="")
				{			
					$sql.="WHERE month = '$mnth' $conditionAppend AND year = '$yr' AND bankname = '$bname' AND bankaccno = '$accno' 
					AND transaction_flg = '$trans_flg' ORDER BY year DESC , month DESC ,date ASC ";
				}
				else{
					$sql.="WHERE bankname = '$bname' $conditionAppend AND bankaccno = '$accno' AND transaction_flg = '$trans_flg' ORDER BY year DESC , month DESC ,date ASC ";					
				}		
		$query = DB::select($sql);
		return $query;
	}
	public static function expenses_history_bankdetails_subSubject($request,$mnsub,$subsub,$trans_flg,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_expenses as main')
					->SELECT('main.*','banks.BankName AS bname')
					->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('main.bankname', '=', 'bank.BankName');
								$join->on('main.bankaccno', '=', 'bank.AccNo');
							})
					->leftJoin('mstbanks AS banks', 'banks.id', '=', 'bank.BankName');
		if($yr!=""&&$mnth!="") {
			$query = $query->where(function($joincont) use ($mnsub,$subsub,$yr,$mnth,$trans_flg) {
				$joincont->where('banks.BankName','=',$mnsub)
							->where('main.bankaccno','=',$subsub)
							->where('main.transaction_flg','=',$trans_flg)
							->where('main.year','=',$yr)
							->where('main.month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($mnsub,$subsub,$trans_flg) {
				$joincont->where('banks.BankName','=',$mnsub)
							->where('main.transaction_flg','=',$trans_flg)
							->where('main.bankaccno','=',$subsub);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('main.year','DESC')
						->orderBy('main.month','DESC')
						->orderBy('main.date','ASC')
						->paginate($request->plimit);
		return $query;
	}
	public static function pettycash_subhistoryvalues_details($request,$delflg,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('inv_pettycash.*','inv_set_transfermain.main_eng AS Subject',
					'inv_set_transfermain.main_jap AS Subject_jp',
					'inv_set_transfersub.sub_eng','inv_set_transfersub.sub_jap','mstbank.Bank_NickName')
					->leftJoin('inv_set_transfermain', 'inv_set_transfermain.id', '=', 'inv_pettycash.main_subject')
					->leftJoin('inv_set_transfersub', function($join)
							{
								$join->on('inv_set_transfersub.id', '=', 'inv_pettycash.sub_subject');
								$join->on('inv_set_transfersub.mainid', '=', 'inv_pettycash.main_subject');
							})
					->leftJoin('mstbank', function($join)
							{
								$join->on('mstbank.BankName', '=', 'inv_pettycash.bankname');
								$join->on('mstbank.AccNo', '=', 'inv_pettycash.bankaccno');
							});
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$query = $query->WHERE('inv_pettycash.date', '>', $accessDate);
				}
				// END ACCESS RIGHTS
			$query = $query->orderBy('inv_pettycash.year','DESC')
					->orderBy('inv_pettycash.month','DESC')
					->orderBy('date','ASC')
					->paginate($request->plimit);
					// ->toSql();dd($query);
		return $query;
	}
	public static function pettycash_subhistoryvalues_detailsamountdel($request,$delflg,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('inv_pettycash.*','inv_set_transfermain.main_eng AS Subject',
					'inv_set_transfermain.main_jap AS Subject_jp',
					'inv_set_transfersub.sub_eng','inv_set_transfersub.sub_jap','mstbank.Bank_NickName')
					->leftJoin('inv_set_transfermain', 'inv_set_transfermain.id', '=', 'inv_pettycash.main_subject')
					->leftJoin('inv_set_transfersub', function($join)
							{
								$join->on('inv_set_transfersub.id', '=', 'inv_pettycash.sub_subject');
								$join->on('inv_set_transfersub.mainid', '=', 'inv_pettycash.main_subject');
							})
					->leftJoin('mstbank', function($join)
							{
								$join->on('mstbank.BankName', '=', 'inv_pettycash.bankname');
								$join->on('mstbank.AccNo', '=', 'inv_pettycash.bankaccno');
							});
				// ACCESS RIGHTS
				// CONTRACT EMPLOYEE
				if (Auth::user()->userclassification == 1) {
					$accessDate = Auth::user()->accessDate;
					$query = $query->WHERE('inv_pettycash.date', '>', $accessDate);
				}
				// END ACCESS RIGHTS
			$query = $query->orderBy('inv_pettycash.year','DESC')
					->orderBy('inv_pettycash.month','DESC')
					->orderBy('date','ASC')
					->get();
		return $query;
	}
	public static function pettycash_subhistoryvalues_detailsdelflg1($request,$delflg,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('inv_pettycash.*','inv_set_transfermain.main_eng AS Subject',
					'inv_set_transfermain.main_jap AS Subject_jp',
					'inv_set_transfersub.sub_eng','inv_set_transfersub.sub_jap','mstbank.Bank_NickName')
					->leftJoin('inv_set_transfermain', 'inv_set_transfermain.id', '=', 'inv_pettycash.main_subject')
					->leftJoin('inv_set_transfersub', function($join)
							{
								$join->on('inv_set_transfersub.id', '=', 'inv_pettycash.sub_subject');
								$join->on('inv_set_transfersub.mainid', '=', 'inv_pettycash.main_subject');
							})
					->leftJoin('mstbank', function($join)
							{
								$join->on('mstbank.BankName', '=', 'inv_pettycash.bankname');
								$join->on('mstbank.AccNo', '=', 'inv_pettycash.bankaccno');
							});
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query = $query->WHERE('inv_pettycash.date', '>', $accessDate);
			}
			// END ACCESS RIGHTS
			$query = $query->where('del_flg','=',$delflg)
					->orderBy('inv_pettycash.year','DESC')
					->orderBy('inv_pettycash.month','DESC')
					->orderBy('date','ASC')
					->paginate($request->plimit);
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycash_subhistoryvalues_detailsdelflg1amount($request,$delflg,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('inv_pettycash.*','inv_set_transfermain.main_eng AS Subject',
					'inv_set_transfermain.main_jap AS Subject_jp',
					'inv_set_transfersub.sub_eng','inv_set_transfersub.sub_jap','mstbank.Bank_NickName')
					->leftJoin('inv_set_transfermain', 'inv_set_transfermain.id', '=', 'inv_pettycash.main_subject')
					->leftJoin('inv_set_transfersub', function($join)
							{
								$join->on('inv_set_transfersub.id', '=', 'inv_pettycash.sub_subject');
								$join->on('inv_set_transfersub.mainid', '=', 'inv_pettycash.main_subject');
							})
					->leftJoin('mstbank', function($join)
							{
								$join->on('mstbank.BankName', '=', 'inv_pettycash.bankname');
								$join->on('mstbank.AccNo', '=', 'inv_pettycash.bankaccno');
							});
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query = $query->WHERE('inv_pettycash.date', '>', $accessDate);
			}
			// END ACCESS RIGHTS
			$query = $query->where('del_flg','=',$delflg)
					->orderBy('inv_pettycash.year','DESC')
					->orderBy('inv_pettycash.month','DESC')
					->orderBy('date','ASC')
					->get();
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycash_historydetailsamount($request,$mnsub,$yr,$mnth) {
		$subsubj = "";
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*','inv_pettycash.id AS pettyid','main_eng AS main_subject_name','sub_eng AS sub_subject_name')
					->leftJoin('inv_set_transfermain', 'inv_pettycash.main_subject', '=', 'inv_set_transfermain.id')
					->leftJoin('inv_set_transfersub', 'inv_pettycash.sub_subject', '=', 'inv_set_transfersub.id');
		if($yr!=""&&$mnth!=""&&$subsubj== "") {
			$query = $query->where(function($joincont) use ($mnsub,$yr,$mnth) {
				$joincont->where('main_subject','=',$mnsub)
							->where('year','=',$yr)
							->where('month','=',$mnth);
			});
		} else if($subsubj!= "" && $mnsub!= "") {
			$query = $query->where(function($joincont) use ($subsub) {
				$joincont->where('sub_subject','=',$subsub);
			});
		} else {
			$query = $query->where(function($joincont) use ($mnsub) {
				$joincont->where('main_subject','=',$mnsub);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->get();
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycash_historydetails($request,$mnsub,$yr,$mnth) {
		$subsubj = "";
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*','inv_pettycash.id AS pettyid','main_eng AS main_subject_name','sub_eng AS sub_subject_name')
					->leftJoin('inv_set_transfermain', 'inv_pettycash.main_subject', '=', 'inv_set_transfermain.id')
					->leftJoin('inv_set_transfersub', 'inv_pettycash.sub_subject', '=', 'inv_set_transfersub.id');
		if($yr!=""&&$mnth!=""&&$subsubj== "") {
			$query = $query->where(function($joincont) use ($mnsub,$yr,$mnth) {
				$joincont->where('main_subject','=',$mnsub)
							->where('year','=',$yr)
							->where('month','=',$mnth);
			});
		} else if($subsubj!= "" && $mnsub!= "") {
			$query = $query->where(function($joincont) use ($subsub) {
				$joincont->where('sub_subject','=',$subsub);
			});
		} else {
			$query = $query->where(function($joincont) use ($mnsub) {
				$joincont->where('main_subject','=',$mnsub);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->paginate($request->plimit);
		return $query;
	}
	public static function expenses_historydetails($request,$mnsub,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$sql= "SELECT *,inv_pettycash.id AS pettyid , main_eng AS main_subject_name ,sub_eng AS sub_subject_name
				FROM inv_pettycash LEFT JOIN inv_set_transfermain
				ON inv_pettycash.main_subject =inv_set_transfermain.id  LEFT JOIN inv_set_transfersub 
				ON inv_pettycash.sub_subject =inv_set_transfersub.id ";
							
				if($yr!=""&&$mnth!="")
				{			
					$sql.="WHERE main_subject = '$mnsub' $conditionAppend AND month = '$mnth' AND year = '$yr' ORDER BY year DESC , month DESC ,date ASC";
				}else{
					$sql.="WHERE main_subject = '$mnsub' $conditionAppend ORDER BY year DESC , month DESC ,date ASC";					
				}
				$cards = DB::select($sql);
		return $cards;
	}
	public static function pettycash_bankmain_historydetailsamount($request,$bname,$accno,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*');
		if($yr!=""&&$mnth!="") {
			$query = $query->where(function($joincont) use ($bname,$accno,$yr,$mnth) {
				$joincont->where('bankname','=',$bname)
						->where('bankaccno','=',$accno)
						->where('year','=',$yr)
						->where('month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($bname,$accno) {
				$joincont->where('bankname','=',$bname)
						->where('bankaccno','=',$accno);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->get();
		return $query;
	}
	public static function pettycash_bankmain_historydetails($request,$bname,$accno,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*');
		if($yr!=""&&$mnth!="") {
			$query = $query->where(function($joincont) use ($bname,$accno,$yr,$mnth) {
				$joincont->where('bankname','=',$bname)
						->where('bankaccno','=',$accno)
						->where('year','=',$yr)
						->where('month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($bname,$accno) {
				$joincont->where('bankname','=',$bname)
						->where('bankaccno','=',$accno);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->paginate($request->plimit);
		return $query;
	}
	public static function pettycash_history_bankdetails_subSubjectamount($request,$bname,$accno,$yr,$mnth,$trans_flg) {
			$db = DB::connection('mysql');
			$query = $db->table('inv_pettycash')
					->SELECT('*');
						
			if($yr!=""&&$mnth!="")
			{			
				$query = $query->where(function($joincont) use ($bname,$accno,$yr,$mnth,$trans_flg) {
					$joincont->where('bankname','=',$bname)
							->where('bankaccno','=',$accno)
							->where('transaction_flg','=',$trans_flg)
							->where('year','=',$yr)
							->where('month','=',$mnth);
				});
			}
			else{
				$query = $query->where(function($joincont) use ($bname,$accno,$trans_flg) {
					$joincont->where('bankname','=',$bname)
							->where('transaction_flg','=',$trans_flg)
							->where('bankaccno','=',$accno);
				});					
			}	
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS	
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->get();
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycash_history_bankdetails_subSubject($request,$bname,$accno,$yr,$mnth,$trans_flg) {
			$db = DB::connection('mysql');
			$query = $db->table('inv_pettycash')
					->SELECT('*');
						
			if($yr!=""&&$mnth!="")
			{			
				$query = $query->where(function($joincont) use ($bname,$accno,$yr,$mnth,$trans_flg) {
					$joincont->where('bankname','=',$bname)
							->where('bankaccno','=',$accno)
							->where('transaction_flg','=',$trans_flg)
							->where('year','=',$yr)
							->where('month','=',$mnth);
				});
			}
			else{
				$query = $query->where(function($joincont) use ($bname,$accno,$trans_flg) {
					$joincont->where('bankname','=',$bname)
							->where('transaction_flg','=',$trans_flg)
							->where('bankaccno','=',$accno);
				});					
			}		
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->paginate($request->plimit);
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycashsubsubjhistorydetailsamount($request,$subsubj,$yr,$mnth) { 
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*','inv_pettycash.id AS pettyid','main_eng AS main_subject_name','sub_eng AS sub_subject_name')
					->leftJoin('inv_set_transfermain', 'inv_pettycash.main_subject', '=', 'inv_set_transfermain.id')
					->leftJoin('inv_set_transfersub', 'inv_pettycash.sub_subject', '=', 'inv_set_transfersub.id');
						
		if($yr!=""&&$mnth!=""&&$subsubj== "") {
			$query = $query->where(function($joincont) use ($subsubj,$yr,$mnth) {
				$joincont->where('sub_subject','=',$subsubj)
							->where('year','=',$yr)
							->where('month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($subsubj) {
				$joincont->where('sub_subject','=',$subsubj);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->get();
								// ->toSQL();dd($query);
		return $query;
	}
	public static function pettycashsubsubjhistorydetails($request,$subsubj,$yr,$mnth) { 
		$db = DB::connection('mysql');
		$query = $db->table('inv_pettycash')
					->SELECT('*','inv_pettycash.id AS pettyid','main_eng AS main_subject_name','sub_eng AS sub_subject_name')
					->leftJoin('inv_set_transfermain', 'inv_pettycash.main_subject', '=', 'inv_set_transfermain.id')
					->leftJoin('inv_set_transfersub', 'inv_pettycash.sub_subject', '=', 'inv_set_transfersub.id');
						
		if($yr!=""&&$mnth!=""&&$subsubj== "") {
			$query = $query->where(function($joincont) use ($subsubj,$yr,$mnth) {
				$joincont->where('sub_subject','=',$subsubj)
							->where('year','=',$yr)
							->where('month','=',$mnth);
			});
		} else {
			$query = $query->where(function($joincont) use ($subsubj) {
				$joincont->where('sub_subject','=',$subsubj);
			});
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('date', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('year','DESC')
						->orderBy('month','DESC')
						->orderBy('date','ASC')
						->paginate($request->plimit);
								// ->toSQL();dd($query);
		return $query;
	}
	public static function expenses_historydetails_subSubject1($subsubj,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$sql= "SELECT *,inv_pettycash.id AS pettyid , main_eng AS main_subject_name ,sub_eng AS sub_subject_name
				FROM inv_pettycash LEFT JOIN inv_set_transfermain
				ON inv_pettycash.main_subject =inv_set_transfermain.id  LEFT JOIN inv_set_transfersub 
				ON inv_pettycash.sub_subject =inv_set_transfersub.id ";
							
				if($yr!=""&&$mnth!=""&&$subsubj!= "" )
				{			
					$sql.="WHERE sub_subject = '$subsubj' $conditionAppend AND month = '$mnth' AND year = '$yr' ORDER BY year DESC , month DESC ,date ASC";
				}
				else{
					$sql.="WHERE sub_subject = '$subsubj' $conditionAppend ORDER BY year DESC , month DESC ,date ASC";					
				}
		$cards = DB::select($sql);
		return $cards;
	}
	public static function expenses_historydetails_subSubject($mnsub,$subsub,$yr,$mnth) {
			$sql= "SELECT main.year,subCat.mainid,main.month,main.amount,main.remark_dtl AS remarks,
						main.date,mainCat.Subject,mainCat.Subject_jp,subCat.sub_eng,subCat.sub_jap 
						FROM dev_expenses AS main
						LEFT JOIN inv_set_expensesub AS subCat ON subCat.mainid=main.subject 
						AND subCat.id=main.details
						LEFT JOIN dev_expensesetting AS mainCat ON mainCat.id=subCat.mainid
						";
			if($yr!=""&&$mnth!="")
			{			
				$sql.="WHERE subCat.mainid='$mnsub' AND subCat.id='$subsub' AND main.year='$yr' 
						AND main.month='$mnth' ORDER BY main.year DESC,main.month DESC, main.date ASC";
			}
			else{
				$sql.="WHERE subCat.mainid='$mnsub' AND subCat.id='$subsub' 
						ORDER BY main.year DESC,main.month DESC, main.date ASC";					
			}
		$cards = DB::select($sql);
		return $cards;	
	}
	public static function download_expenses($year,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date > '$accessDate'";
		}
		// END ACCESS RIGHTS
		$sql="SELECT *,banks.BankName AS bname FROM inv_pettycash main 
							LEFT JOIN mstbank bank on main.bankname=bank.BankName  and main.bankaccno=bank.AccNo
							LEFT JOIN mstbanks banks ON banks.id=bank.BankName
							LEFT JOIN inv_set_transfermain des on des.id = main.main_subject 
							LEFT JOIN inv_set_transfersub ise on ise.id = main.sub_subject  
							WHERE main.year='$year' AND main.month='$month' 
							$conditionAppend 
							ORDER BY  main.date ASC , main.id ASC";
		$cards = DB::select($sql);
		return $cards;
	}
}