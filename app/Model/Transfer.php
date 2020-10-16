<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
use Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
class Transfer extends Model {
	public static function fnGetAccountPeriodBK($request) {
		$query=DB::table('dev_kessandetails')
						->SELECT('*')
						->where('delflg','=','0')
						->get();
		return $query;
	}
	public static function fnGetBKRecord($from_date, $to_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$from_date = Auth::user()->accessDate;
			$conditionAppend = "OR accessFlg = 1";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');
		$sql = "SELECT SUBSTRING(bankdate, 1, 7) AS bankdate 
				FROM dev_banktransfer 
				WHERE (bankdate > '$from_date' AND bankdate < '$to_date') $conditionAppend ORDER BY bankdate ASC";
		$query = DB::select($sql);
		return $query;
	}
	public static function fnGetbkrsRecordPrevious($from_date) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$to_date = Auth::user()->accessDate;
			$conditionAppend = "AND (bankdate >= '$to_date' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');
		$sql = "SELECT SUBSTRING(bankdate, 1, 7) AS bankdate FROM dev_banktransfer 
				WHERE (bankdate <= '$from_date' $conditionAppend) ORDER BY bankdate ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function fnGetbkrsRecordNext($to_date) {
		$db = DB::connection('mysql');
		$sql = "SELECT SUBSTRING(bankdate, 1, 7) AS bankdate FROM dev_banktransfer WHERE (bankdate >= '$to_date') ORDER BY bankdate ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function download_transfer($request,$year,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (main.bankdate > '$accessDate' OR main.accessFlg = 1)";
			$conditionAppendSalary = "AND sal.salaryDate > '$accessDate'";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');

		$query = $db->TABLE($db->raw("(SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.amount,
											main.fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											main.others,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 

									UNION ALL
										SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.fee AS amount,
											NULL AS fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											NULL AS others,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				AND others!=1 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 

							UNION ALL
								SELECT sal.id,
											NULL AS billno,
											mstbank.BankName AS OrderBankName,
											mstbank.AccNo AS OrderAccNo,
											sal.empNo AS empNo,
											emp_mstemployees.FirstName AS FirstNames,
											emp_mstemployees.LastName AS LastNames,
											sal.salaryDate as bankdate,
											sal.salaryMonth as salaryMonth,
											NULL AS subject,
											NULL AS details,
											mstbank.BankName AS bankname,
											mstbank.AccNo AS bankaccno,
											REPLACE(sal.salary, ',', '') AS amount,
											REPLACE(sal.charge, ',', '') AS fee,
											sal.bankId AS bankId,
											NULL AS file_dtl,
											NULL AS remark_dtl,
											sal.delFlg,
											NULL AS loan_flg,	
											mstbank.Bank_NickName,
											1 AS salaryFlg,
											emp_mstemployees.FirstName,
											emp_mstemployees.LastName,
											banks.BankName bname,
											mstbank.AccNo,
											InsDT AS Ins_DTTM,
											mstbank.id AS mainbankid,
											0 AS loanType,
											sal.year,
											sal.month,
											NULL AS submit_flg,
											NULL AS edit_flg,
											NULL AS copy_month_day,
											NULL AS others,
											NULL AS Ins_DT,
											NULL AS Up_DT,
											NULL AS UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'
										$conditionAppendSalary

									
										UNION ALL
										SELECT sal.id,
										NULL AS billno,
										mstbank.BankName AS OrderBankName,
										mstbank.AccNo AS OrderAccNo,
										sal.empNo AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										sal.salaryDate as bankdate,
										sal.salaryMonth as salaryMonth,
										NULL AS subject,
										NULL AS details,
										mstbank.BankName AS bankname,
										mstbank.AccNo AS bankaccno,
										REPLACE(sal.charge, ',', '') AS amount,
										NULL AS fee,
										sal.bankId AS bankId,
										NULL AS file_dtl,
										NULL AS remark_dtl,
										sal.delFlg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										1 AS salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										banks.BankName bname,
										mstbank.AccNo,
										InsDT AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										sal.year,
										sal.month,
										NULL AS submit_flg,
										NULL AS edit_flg,
										NULL AS copy_month_day,
										NULL AS others,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										NULL AS transaction_flg,
										NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'


										 AND bankId!='999'


							 UNION ALL
								SELECT main.id,
										main.billno,
										main.bankname AS OrderBankName,
										main.bankaccno AS OrderAccNo,
										main.emp_ID AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										main.date as bankdate,
										NULL AS salaryMonth,
										main.subject AS subject,
										main.details AS details,
										mstbanks.BankName AS bankname,
										main.bankaccno AS bankaccno,
										main.amount AS amount,
										NULL AS fee,
										NULL AS bankId,
										main.file_dtl,
										main.remark_dtl,
										main.del_flg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										main.salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										mstbanks.BankName AS bname,
										mstbank.AccNo,
										CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										main.year,
										main.month,
										main.edit_flg,
										main.submit_flg,
										main.copy_month_day,
										NULL AS others,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										main.transaction_flg,
										main.transfer_flg
										FROM dev_expenses main 
										LEFT JOIN emp_mstemployees ON main.emp_ID=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank on main.bankname=mstbank.BankName  and main.bankaccno=mstbank.AccNo
										LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
										WHERE main.year='$year' AND main.month='$month'
										$conditionAppendSalary
										AND main.carryForwardFlg!=1
										AND main.transaction_flg IS NOT NULL) 
																	AS DDD"
																));

			$query = $query->ORDERBY('DDD.OrderBankName', 'ASC')
							->ORDERBY('DDD.OrderAccNo', 'ASC')
							->ORDERBY('DDD.bankdate', 'ASC')
							->ORDERBY('DDD.Ins_DTTM', 'ASC')
							->paginate($request->plimit);
							// ->toSql();
							// dd($query);
		return $query;
	}
	public static function download_transferforexcel($request,$year,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (main.bankdate > '$accessDate' OR main.accessFlg = 1)";
			$conditionAppendSalary = "AND sal.salaryDate > '$accessDate'";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');

		$query = $db->TABLE($db->raw("(SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.amount,
											main.fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											main.others,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 


									UNION ALL
										SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.fee AS amount,
											NULL AS fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											NULL AS others,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				AND others!=1 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 

							UNION ALL
								SELECT sal.id,
											NULL AS billno,
											mstbank.BankName AS OrderBankName,
											mstbank.AccNo AS OrderAccNo,
											sal.empNo AS empNo,
											emp_mstemployees.FirstName AS FirstNames,
											emp_mstemployees.LastName AS LastNames,
											sal.salaryDate as bankdate,
											sal.salaryMonth as salaryMonth,
											NULL AS subject,
											NULL AS details,
											mstbank.BankName AS bankname,
											mstbank.AccNo AS bankaccno,
											REPLACE(sal.salary, ',', '') AS amount,
											REPLACE(sal.charge, ',', '') AS fee,
											sal.bankId AS bankId,
											NULL AS file_dtl,
											NULL AS remark_dtl,
											sal.delFlg,
											NULL AS loan_flg,	
											mstbank.Bank_NickName,
											1 AS salaryFlg,
											emp_mstemployees.FirstName,
											emp_mstemployees.LastName,
											banks.BankName bname,
											mstbank.AccNo,
											InsDT AS Ins_DTTM,
											mstbank.id AS mainbankid,
											0 AS loanType,
											sal.year,
											sal.month,
											NULL AS submit_flg,
											NULL AS edit_flg,
											NULL AS copy_month_day,
											NULL AS others,
											NULL AS Ins_DT,
											NULL AS Up_DT,
											NULL AS UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'
										$conditionAppendSalary

									
										UNION ALL
										SELECT sal.id,
										NULL AS billno,
										mstbank.BankName AS OrderBankName,
										mstbank.AccNo AS OrderAccNo,
										sal.empNo AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										sal.salaryDate as bankdate,
										sal.salaryMonth as salaryMonth,
										NULL AS subject,
										NULL AS details,
										mstbank.BankName AS bankname,
										mstbank.AccNo AS bankaccno,
										REPLACE(sal.charge, ',', '') AS amount,
										NULL AS fee,
										sal.bankId AS bankId,
										NULL AS file_dtl,
										NULL AS remark_dtl,
										sal.delFlg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										1 AS salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										banks.BankName bname,
										mstbank.AccNo,
										InsDT AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										sal.year,
										sal.month,
										NULL AS submit_flg,
										NULL AS edit_flg,
										NULL AS copy_month_day,
										NULL AS others,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										NULL AS transaction_flg,
										NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'


										 AND bankId!='999'


							 UNION ALL
								SELECT main.id,
										main.billno,
										main.bankname AS OrderBankName,
										main.bankaccno AS OrderAccNo,
										main.emp_ID AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										main.date as bankdate,
										NULL AS salaryMonth,
										main.subject AS subject,
										main.details AS details,
										mstbanks.BankName AS bankname,
										main.bankaccno AS bankaccno,
										main.amount AS amount,
										NULL AS fee,
										NULL AS bankId,
										main.file_dtl,
										main.remark_dtl,
										main.del_flg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										main.salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										mstbanks.BankName AS bname,
										mstbank.AccNo,
										CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										main.year,
										main.month,
										main.edit_flg,
										main.submit_flg,
										main.copy_month_day,
										NULL AS others,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										main.transaction_flg,
										main.transfer_flg
										FROM dev_expenses main 
										LEFT JOIN emp_mstemployees ON main.emp_ID=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank on main.bankname=mstbank.BankName  and main.bankaccno=mstbank.AccNo
										LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
										WHERE main.year='$year' AND main.month='$month'
										$conditionAppendSalary
										AND main.carryForwardFlg!=1
										AND main.transaction_flg IS NOT NULL) 
																	AS DDD"
																));

			$query = $query->ORDERBY('DDD.OrderBankName', 'ASC')
							->ORDERBY('DDD.OrderAccNo', 'ASC')
							->ORDERBY('DDD.bankdate', 'ASC')
							->ORDERBY('DDD.Ins_DTTM', 'ASC')
							->get();
							// ->toSql();
							// dd($query);
		return $query;
	}
	public static function download_transfer_1($year,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND main.date>'$accessDate'";
			$conditionAppendSalary = "AND sal.salaryDate>'$accessDate'";
		}
		// END ACCESS RIGHTS
		$db = DB::connection('mysql');
		$sql = "SELECT * FROM(SELECT main.id,
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
								WHERE main.year='$year' AND main.month='$month' 
								$conditionAppend
								AND salaryFlg!=1 
						UNION ALL
						SELECT sal.id,
							NULL AS billno,
							sal.empNo AS empNo,
							sal.salaryDate as date,
							sal.salaryMonth as salaryMonth,
							NULL AS subject,
							NULL AS details,
							NULL AS bankname,
							NULL AS bankaccno,
							REPLACE(sal.salary, ',', '') AS amount,
							REPLACE(sal.charge, ',', '') AS charge,
							sal.bankId AS bankId,
							NULL AS file_dtl,
							NULL AS remark_dtl,
							NULL AS del_flg,
							NULL AS transaction_flg,
							NULL AS pettyFlg,
							0 AS carryForwardFlg,	
							NULL AS currency_type,
							NULL AS Bank_NickName,
							1 AS salaryFlg,
							emp_mstemployees.FirstName,
							emp_mstemployees.LastName,
							NULL AS bname,
							NULL AS Ins_TM,
							NULL AS transfer_flg,
							NULL AS edit_flg,
							NULL AS submit_flg,
							NULL AS copy_month_day,
							sal.year,
							sal.month,
							NULL AS banknameTransfer,
							NULL AS bankaccnoTransfer,
							InsDT AS Ins_DT
							FROM inv_salary AS sal 
								LEFT JOIN emp_mstemployees ON empNo=emp_mstemployees.Emp_ID
								WHERE sal.year='$year' AND sal.month='$month' 
								$conditionAppendSalary
								AND bankId='999') 
								AS DDD ORDER BY DDD.date ASC,DDD.carryForwardFlg DESC,DDD.Ins_DT ASC,DDD.Ins_TM ASC,DDD.del_flg DESC,DDD.id";
		$cards = DB::select($sql);
		return $cards;
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
	public static function selbksubname($id) {
		$db = DB::connection('mysql');
		$sql = "SELECT Subject,Subject_jp FROM dev_expensesetting WHERE id='".$id."' AND delflg = 0";
		$cards = DB::select($sql);
		$selectedField = "";
		$val = array();
		if(Session::get('languageval') == "jp") {
			$selectedField = "Subject_jp";
		} else {
			$selectedField = "Subject";
		}
		foreach ($cards as $key => $value) {
			$val[0]= $value->$selectedField;
		}
		return $val;
	}
	public static function selsubtransfersubjectname($sub_id,$main_id) {
		$db = DB::connection('mysql');
		$sql = "SELECT sub_eng,sub_jap FROM inv_set_expensesub WHERE id='".$sub_id."' AND mainid ='".$main_id."'";
		$cards = DB::select($sql);
		$selectedField = "";
		$val = array();
		if(Session::get('languageval') == "jp") {
			$selectedField = "sub_jap";
		} else {
			$selectedField = "sub_eng";
		}
		foreach ($cards as $key => $value) {
			$val[0]= $value->$selectedField;
		}
		return $val;
	}
	public static function regGetBankId($id) {
		$db = DB::connection('mysql');
		$sql = "SELECT mstbank.*,mstbanks.BankName AS bnName FROM mstbank 
					LEFT JOIN mstbanks ON mstbank.BankName=mstbanks.id WHERE mstbank.id='$id'";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function regGetBankDetails($bankname ,$acc) {
		$db = DB::connection('mysql');
		$sql = "SELECT * FROM mstbank LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
				where mstbank.BankName = '$bankname' AND mstbank.AccNo = '$acc'";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function soluexpanse($year ,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (date >'".$accessDate."' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		$accessDate = Auth::user()->accessDate;
		$db = DB::connection('mysql');
		$sql = "SELECT SUM(amount) as amount FROM dev_expenses WHERE del_flg = 1 $conditionAppend AND year = '".$year."' AND month = '".$month."'";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function solutransfer($year ,$month) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (bankdate >'".$accessDate."' OR accessFlg = 1)";
		}
		// END ACCESS RIGHTS
		$accessDate = Auth::user()->accessDate;
		$db = DB::connection('mysql');
		$sql = "SELECT (SELECT SUM(amount) FROM dev_banktransfer WHERE del_flg = 0 $conditionAppend AND year = '".$year."' AND month = '".$month."') + (SELECT SUM(fee) FROM dev_banktransfer where del_flg = 0 $conditionAppend AND year = '".$year."' AND month = '".$month."') as result";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function getkessanki($request) {
		$db = DB::connection('mysql');
		$sql = "SELECT Accountperiod FROM dev_kessandetails";
		$cards = DB::select($sql);
		foreach ($cards AS $key => $val) {
			$value[0]= $val->Accountperiod;
		}
		return $value;
	}
	public static function getMainCategories($request) {
		$db = DB::connection('mysql');
		$sql = "SELECT * FROM dev_expensesetting";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function getSubCategories($mainId) {
		$db = DB::connection('mysql');
		$sql = "SELECT * FROM inv_set_expensesub WHERE mainid=$mainId";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function getmainsub($id) {
		$db = DB::connection('mysql');
		if(Session::get('languageval') == "jp") {
			$query= $db->table('dev_expensesetting')
						->SELECT(DB::RAW("CONCAT(Subject_jp) AS MAINSUB"),'id','Subject_jp')
						->where('delflg','=',0)
						->lists('MAINSUB','id');
		} else {
			$query= $db->table('dev_expensesetting')
						->SELECT(DB::RAW("CONCAT(Subject) AS MAINSUB"),'id','Subject')
						->where('delflg','=',0)
						->lists('MAINSUB','id');
		}
		return $query;
	}
	public static function fnfetchsubsubject($request) {
		if(Session::get('languageval') == "jp") {
			$selectedField = "sub_jap";
		} else {
			$selectedField = "sub_eng";
		}
		$accperiod=DB::table('inv_set_expensesub')
						->SELECT('id',$selectedField,'mainid')
						->where('delflg',0)
						->where('mainid',$request->mainid)
						->get();
		return $accperiod;
	}
	public static function fnfetchloanname($request) {
		$accperiod=DB::table('inv_loandetails')
						->SELECT('*')
						->where('delFlg',0)
						->where('bankId',$request->mainid)
						->get();
		return $accperiod;
	}
	public static function fetchbankname($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbank as ban')
						->SELECT(DB::RAW("CONCAT(mstbanks.BankName,'-',ban.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbanks.id,'-',ban.AccNo) AS ID"))
						->Join('mstbanks', 'mstbanks.id', '=', 'ban.BankName')
						->lists('BANKNAME','ID');
		return $query;
	}
	public static function getloantype($request) {
		$db = DB::connection('mysql');
		if(Session::get('languageval') == "jp") {
			$query= $db->table('inv_set_loantype')
					->SELECT(DB::RAW("CONCAT(loanJap) AS LOAN"),'id','loanJap')
					->where('delflg','=',0)
					->lists('LOAN','id');
		} else {
			$query= $db->table('inv_set_loantype')
					->SELECT(DB::RAW("CONCAT(loanEng) AS LOAN"),'id','loanEng')
					->where('delflg','=',0)
					->lists('LOAN','id');
		}
		return $query;
	}
	public static function banknames($request) {
		$db = DB::connection('mysql');
		$query= $db->table('inv_loandetails AS banDet')
						->SELECT(DB::RAW("CONCAT(bnk.Bank_NickName,'-',bnk.AccNo) AS BANKNAME"),'bnk.id AS bankid','bnk.AccNo','banname.BankName AS banknm','bnk.Bank_NickName AS banknick','banDet.bankId AS BANKID','brncname.BranchName AS brnchnm')
						->leftjoin('mstbank AS bnk', 'bnk.id', '=', 'banDet.bankId')
						->leftjoin('mstbanks AS banname', 'banname.id', '=', 'bnk.BankName')
						->Join('mstbankbranch AS brncname', function($join)
							{
								$join->on('brncname.BankId', '=', 'bnk.BankName');
								$join->on('brncname.id', '=', 'bnk.BranchName');
							})
						->where('bnk.delflg','=',0)
						->where('bnk.Location','=',2)
						->groupby('banDet.bankId')
						->lists('BANKNAME','bankid');
						// ->get();
						// ->toSql();
						// dd($query);
		return $query;
	}
	public static function getautoincrement() {
		$statement = DB::select("show table status like 'dev_banktransfer'");
		return $statement[0]->Auto_increment;
	}
	public static function 	editquery($request) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer')
					->SELECT('*')
					->where('id','=',$request->id)
					->where('del_Flg','=',0)
					->get();
		return $query;
	}
	public static function editquerys($request) {
		$db = DB::connection('mysql');
		if ($request->loandetail == "5") {
			$sql = "SELECT dev_banktransfer.*,inv_loandetails.repaymentDate FROM dev_banktransfer LEFT JOIN inv_loandetails ON inv_loandetails.loanNo=dev_banktransfer.billno WHERE dev_banktransfer.loan_flg=1 AND dev_banktransfer.billno='$request->id' ORDER BY dev_banktransfer.id DESC LIMIT 1";
			$query = DB::select($sql);
			if (empty($query)) {
				$request->editflg = "1";
			}
		} else {
			$query = $db->table('dev_banktransfer')
					->SELECT('dev_banktransfer.*')
					->where('dev_banktransfer.id','=',$request->id)
					->where('dev_banktransfer.loan_flg','=',1)
					->get();
		}
		return $query;
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
	public static function getbktrbillno($accperiod) {
		$db = DB::connection('mysql');
		$sql="SELECT MAX(billno) AS tot FROM dev_banktransfer WHERE billno NOT LIKE 'LON%'";
		$query = DB::select($sql);
		if (empty($query[0]->tot)) {
			$bill_no=$accperiod.(str_pad($query[0]->tot+1,'5','0',STR_PAD_LEFT));
		} else {
			$bill_no=$query[0]->tot+1;
		}
		return $bill_no;
	}
	public static function getaccno($accno) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer')
					->SELECT('*')
					->where('bankname','=',$accno)
					->get();
		$acc = "";
		if (isset($query[0]->bankaccno)) {
			$acc = $query[0]->bankaccno;
		}
		return $acc;
	}
	public static function addupdtransfer($request) {
		$update=DB::table('dev_banktransfer')
						->where('id', $request->id)
						->update(
							array(
								'copy_month_day'	=>	1
							)
						);
		return $update;
	}
	public static function inserttransferRec($request,$checkSubmitCount=null,$filename) {
		$accessFlg = 0;
		$receipt = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		if (isset($request->receipt)) {
			$receipt = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount_1);
		$charge = str_replace(",", "", $request->charge_1);
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$splitdate = explode("-", $request->txt_startdate);
		$valueget = self:: getbktrbillno(10);
		$bank = explode("-", $request->bankname);
		$db = DB::connection('mysql');
		$insert=DB::table('dev_banktransfer')
			->insert(
				['id' => '',
				'billno' => $valueget,
				'emp_ID' => $request->emp_IDs, 
				'bankdate' => $request->txt_startdate,
				'subject' => $request->mainsubject,
				'details' => $request->subsubject,
				'bankname' => $bank[0],
				'bankaccno' => $bank[1],
				'amount' => $amount,
				'fee' => $charge,
				'receipt' => $receipt,
				'file_dtl' => $filename,
				'remark_dtl' => $request->Remarks,
				'year' => $splitdate[0],
				'month' => $splitdate[1],
				'copy_month_day' => 0,
				'submit_flg' => $sumitedInsert,
				'edit_flg' => $sumitedInsert,
				'accessFlg'	=>	$accessFlg,
				'del_flg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Ins_TM' => date('H:i:s'),
				'CreatedBy' => $name,
				'Up_DT' => date('Y-m-d'),
				'UP_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function fnaddothersdatatodatabase($request){
		// print_r($request->all());exit();
		$accessFlg = 0;
		$receipt = 0;
		$bank = explode("-", $request->bankname);
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount);
		$spldm = explode('-', $request->date);
		$insert=DB::table('dev_banktransfer')
							->insert(
						    ['id'	=>	'', 
							'bankdate' => $request->date, 
							'bankname' => $request->bankname, 
							'bankname' => $bank[0],
							'bankaccno' => $bank[1],
							'amount'	=>	$amount,
							'receipt' => $receipt,
							'remark_dtl' =>	$request->remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'=> 0,
							'del_flg' => 1,
							'others' => 1,
							'accessFlg'	=>	$accessFlg,
							'Ins_DT' => date('Y-m-d'),
							'Ins_TM' => date('H:i:s'),
							'CreatedBy' => $name,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
		return $insert;
	}
	public static function UpdatetransferRec($request,$filename) {
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount_1);
		$charge = str_replace(",", "", $request->charge_1);
		$spldm = explode("-", $request->txt_startdate);
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
		$bank = explode("-", $request->bankname);
		$db = DB::connection('mysql');
		$update=DB::table('dev_banktransfer')
			->where('id', $request->id)
			->update(
				['bankdate' => $request->txt_startdate,
				'subject' => $request->mainsubject,
				'details' => $request->subsubject,
				'bankname' => $bank[0],
				'bankaccno' => $bank[1],
				'amount' => $amount,
				'fee' => $charge,
				'file_dtl' => $files,
				'remark_dtl' => $request->Remarks,
				'year' => $spldm[0],
				'month' => $spldm[1],
				'edit_flg' => $editflg,
				'del_flg' => 0,
				'Up_DT' => date('Y-m-d'),
				'UP_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
		// ACCESS RIGHTS
			if (Auth::user()->userclassification == 4) {
				$update=DB::table('dev_banktransfer')
					->where('id', $request->id)
					->update(
						['accessFlg'	=>	$accessFlg]);
			}
		//END ACCESS RIGHTS
		return $update;
	}
	public static function transfermultiregister($id,$month,$year) {
		$db = DB::connection('mysql');
		$sql = "SELECT mstbank.id as mainbankid,dev_banktransfer.*,mstbanks.BankName,mstbank.AccNo,mstbank.Bank_NickName,
				    	IF(dev_banktransfer.loan_flg=1, mstbanks.BankName, dev_expensesetting.Subject) as mainSubject,
						IF(dev_banktransfer.loan_flg=1, mstbanks.BankName, dev_expensesetting.Subject_jp) as Subject_jp,
						inv_set_expensesub.sub_eng,inv_set_expensesub.sub_jap 
				    FROM dev_banktransfer  
				    LEFT JOIN dev_expensesetting ON dev_expensesetting.id=dev_banktransfer.subject 
					LEFT JOIN inv_set_expensesub ON inv_set_expensesub.mainid=dev_expensesetting.id AND inv_set_expensesub.id=dev_banktransfer.details
				    LEFT JOIN mstbanks ON 
				    	IF(dev_banktransfer.loan_flg=1,mstbanks.id=(SELECT BankName FROM mstbank WHERE id=dev_banktransfer.bankname),
						mstbanks.id=dev_banktransfer.bankname)
				    LEFT JOIN mstbank ON IF(dev_banktransfer.loan_flg=1,
						mstbank.id=dev_banktransfer.bankname,
						mstbank.BankName=mstbanks.id AND mstbank.AccNo=dev_banktransfer.bankaccno ) 
				    WHERE year='".$year."'
				    AND month='".$month."' AND dev_banktransfer.id IN ($id)
				    ORDER BY dev_banktransfer.bankname ASC,mstbank.AccNo ASC,dev_banktransfer.bankdate ASC,dev_banktransfer.Ins_TM ASC";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function insertLoanpaymentRec($request,$checkSubmitCount=null) {
		$db = DB::connection('mysql');
		$accessFlg = 0;
		if (isset($request->cashid)) {
			$request->txt_startdate = $request->date;
			$request->bankname = $request->banknameloan;
			$request->Remarks = $request->remarks;
		}
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount);
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$splitdate = explode("-", $request->txt_startdate);
		$accno = self:: getaccno($request->bankname);
		$insert=DB::table('dev_banktransfer')
			->insert(
				['id' => '',
				'billno' => $request->loanname,
				'bankdate' => $request->txt_startdate,
				'subject' => '',
				'details' => '',
				'bankname' => $request->bankname,
				'bankaccno' => '',
				'amount' => $amount,
				'fee' => $request->interest,
				'loanType' => $request->loantype,
				'file_dtl' => '',
				'remark_dtl' => $request->Remarks,
				'year' => $splitdate[0],
				'month' => $splitdate[1],
				'copy_month_day' => 0,
				'loan_flg' => 1,
				'submit_flg' => $sumitedInsert,
				'edit_flg' => $sumitedInsert,
				'accessFlg'	=>	$accessFlg,
				'del_flg' => 0,
				'Ins_DT' => date('Y-m-d'),
				'Ins_TM' => date('H:i:s'),
				'CreatedBy' => $name,
				'Up_DT' => date('Y-m-d'),
				'UP_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
		return $insert;
	}
	public static function updateLoanpaymentRec($request,$checkSubmitCount=null) {
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount);
		$spldm = explode("-", $request->txt_startdate);
		$valueget = self:: checkSubmited($spldm);
		if ($valueget > 0) {
			$editflg = "1";
		} else {
			$editflg = "0";
		}
		$accno = self:: getaccno($request->bankname);
		$db = DB::connection('mysql');
		$update=DB::table('dev_banktransfer')
			->where('id', $request->ids)
			->update(
				['billno' => $request->loanname,
				'bankdate' => $request->txt_startdate,
				'subject' => '',
				'details' => '',
				'bankname' => $request->bankname,
				'bankaccno' => $accno,
				'amount' => $amount,
				'fee' => $request->interest,
				'loanType' => $request->loantype,
				'file_dtl' => '',
				'remark_dtl' => $request->Remarks,
				'year' => $spldm[0],
				'month' => $spldm[1],
				'copy_month_day' => 0,
				'loan_flg' => 1,
				'edit_flg' => $editflg,
				'del_flg' => 0,
				'Up_DT' => date('Y-m-d'),
				'UP_TM' => date('H:i:s'),
				'UpdatedBy' => $name]
		);
			// ACCESS RIGHTS
				if (Auth::user()->userclassification == 4) {
					$update=DB::table('dev_banktransfer')
						->where('id', $request->ids)
						->update(
							['accessFlg'	=>	$accessFlg]);
				}
			//END ACCESS RIGHTS
		return $update;
	}
	public static function transfermultireg($request,$day,$date,$checkSubmitCount=null) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$splitdate = explode("-", $request->txt_startdate);
		if ($checkSubmitCount > 0) {
			$sumitedInsert = "1";
		} else {
			$sumitedInsert = "0";
		}
		$accessFlg = 0;
		if (isset($request->accessrights)) {
			$accessFlg = 1;
		}
		$id = 'id_'.$request->day;
		$loanno = 'loanno_'.$request->day;
		$subject = 'subject_'.$request->day;
		$details = 'details_'.$request->day;
		$bankname = 'bankname_'.$request->day;
		$bankaccno = 'bankaccno_'.$request->day;
		$loan_flg = 'loan_flg_'.$request->day;
		$loanType = 'loanType_'.$request->day;
		$amount = 'amount'.$request->day;
		$charge = 'charge'.$request->day;
		$remarks = 'remarks'.$request->day;
		$amounts = str_replace(",", "", $amount);
		$amounts1 = str_replace(",", "",$request->$amounts);
		$charges = str_replace(",", "", $charge);
		$charges1 = str_replace(",", "",$request->$charge);
		if ($request->$loanno=="") {
			$request->$loanno= "";
		}
		if ($request->$subject=="") {
			$request->$subject= "";
		}
		if ($request->$details=="") {
			$request->$details= "";
		}
		if ($request->$amounts=="") {
			$request->$amounts= "";
		}
		if ($request->$charges=="") {
			$request->$charges= "";
		}
		if ($request->$loanType=="") {
			$request->$loanType= "";
		}
		$db = DB::connection('mysql');
		$insert=DB::table('dev_banktransfer')
					->insert(
						array(
							'id'	=>	'', 
							'billno'	=>	$request->$loanno, 
							'bankdate' => $request->txt_startdate, 
							'subject' => $request->$subject,
							'details' => $request->$details,
							'bankname'	=>	$request->$bankname,
							'bankaccno'=>	$request->$bankaccno,
							'amount'	=>	$amounts1,
							'fee'	=>	$charges1,
							'loanType'	=>	$request->$loanType,
							'file_dtl' => '',
							'remark_dtl'	=>	$request->$remarks,
							'year'	=>	$splitdate[0],
							'month'	=>	$splitdate[1],
							'copy_month_day'	=>	0,
							'loan_flg'	=>	$request->$loan_flg,
							'del_flg' => 0,
							'submit_flg'	=>	$sumitedInsert,
							'edit_flg'	=>	$sumitedInsert,
							'accessFlg'	=>	$accessFlg,
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
	public static function salaryhistorydetailsamount($request,$bankid,$accNo,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary')
					->SELECT('emp_mstemployees.FirstName','emp_mstemployees.LastName','inv_salary.salary as amount','inv_salary.charge as fee','inv_salary.salaryDate as bankdate','inv_salary.*','mstbanks.BankName','mstbank.AccNo As bankaccno')
					->leftJoin('mstbank', 'mstbank.id', '=', 'inv_salary.bankId')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->leftJoin('emp_mstemployees', 'inv_salary.empNo', '=', 'emp_mstemployees.Emp_ID')
					->where('inv_salary.bankId','=',$bankid);
		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($yr,$mnth) {
				$joincont->where('year','=',$yr)
							->where('month','=',$mnth);
			});	
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query = $query->WHERE('inv_salary.salaryDate', '>', $accessDate);
			}
		// END ACCESS RIGHTS
		$query = $query->orderBy('inv_salary.year','DESC')
						->orderBy('inv_salary.month','DESC')
						->orderBy('inv_salary.salaryDate','ASC')
						->orderBy('inv_salary.empNo','ASC')
						->get();
		return $query;
	}
	public static function salaryhistorydetails($request,$bankid,$accNo,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salary')
					->SELECT('emp_mstemployees.FirstName','emp_mstemployees.LastName','inv_salary.salary as amount','inv_salary.charge as fee','inv_salary.salaryDate as bankdate','inv_salary.*','mstbanks.BankName','mstbank.AccNo As bankaccno')
					->leftJoin('mstbank', 'mstbank.id', '=', 'inv_salary.bankId')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->leftJoin('emp_mstemployees', 'inv_salary.empNo', '=', 'emp_mstemployees.Emp_ID')
					->where('inv_salary.bankId','=',$bankid);
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query = $query->WHERE('inv_salary.salaryDate', '>', $accessDate);
			}
			// END ACCESS RIGHTS
		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($yr,$mnth) {
				$joincont->where('year','=',$yr)
							->where('month','=',$mnth);
			});	
		}
		$query = $query->orderBy('inv_salary.year','DESC')
						->orderBy('inv_salary.month','DESC')
						->orderBy('inv_salary.salaryDate','ASC')
						->orderBy('inv_salary.empNo','ASC')
						->paginate($request->plimit);
								// ->toSQL();dd($query);
		return $query;
		}
	public static function loanhistorydetailsamount($request,$bankid,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer')
					->SELECT('dev_banktransfer.*','mstbank.AccNo','mstbank.Bank_NickName','mstbanks.BankName')
					->leftJoin('mstbank', 'mstbank.id', '=', 'dev_banktransfer.bankname')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->where('dev_banktransfer.loan_flg','=','1')
					->where('dev_banktransfer.bankname','=',$bankid);
		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($yr,$mnth,$bankid) {
				$joincont->where('dev_banktransfer.year','=',$yr)
							->where('dev_banktransfer.month','=',$mnth)
							->where('dev_banktransfer.bankname','=',$bankid)
							->where('dev_banktransfer.loan_flg','=','1');
			});	
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('dev_banktransfer.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('dev_banktransfer.year','DESC')
						->orderBy('dev_banktransfer.month','DESC')
						->orderBy('dev_banktransfer.bankdate','ASC')
						->get();
		return $query;
	}
	public static function loanhistorydetails($request,$bankid,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer')
					->SELECT('dev_banktransfer.*','mstbank.AccNo','mstbank.Bank_NickName','mstbanks.BankName')
					->leftJoin('mstbank', 'mstbank.id', '=', 'dev_banktransfer.bankname')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->where('dev_banktransfer.loan_flg','=','1')
					->where('dev_banktransfer.bankname','=',$bankid);
		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($yr,$mnth,$bankid) {
				$joincont->where('dev_banktransfer.year','=',$yr)
							->where('dev_banktransfer.month','=',$mnth)
							->where('dev_banktransfer.bankname','=',$bankid)
							->where('dev_banktransfer.loan_flg','=','1');
			});	
		}
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('dev_banktransfer.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query = $query->orderBy('dev_banktransfer.year','DESC')
						->orderBy('dev_banktransfer.month','DESC')
						->orderBy('dev_banktransfer.bankdate','ASC')
						->paginate($request->plimit);
		return $query;
	}
	public static function transferhistorydetailsamount($request,$sub,$yr,$mn) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer as main')
					->SELECT('bank.BankName','main.bankaccno','main.year','main.month','subCat.mainid','main.amount',
						'main.fee','main.remark_dtl AS remarks','main.file_dtl',
						'main.bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','main.id','bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'main.subject');
								$join->on('subCat.id', '=', 'main.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'main.bankname', '=', 'bank.id');


		if($yr!=""&&$mn!="") {		
			$query = $query->where(function($joincont) use ($sub,$yr,$mn) {
				$joincont->where('subCat.mainid','=',$sub)
							->where('main.year','=',$yr)
							->where('main.month','=',$mn);
			});	
		} else {
			$query = $query->where(function($joincont) use ($sub) {
				$joincont->where('subCat.mainid','=',$sub);
			});
		}	
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query1 = $db->table('dev_expenses as exp')
					->SELECT('bank.BankName','exp.bankaccno','exp.year','exp.month','subCat.mainid',
						'exp.amount',DB::raw('null as fee'),'exp.remark_dtl AS remarks','exp.file_dtl',
						'exp.date AS bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','exp.id' ,'bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'exp.subject');
								$join->on('subCat.id', '=', 'exp.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'exp.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query1 = $query1->WHERE('exp.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS

		if($yr!=""&&$mn!="") {		
			$query1 = $query1->where(function($joincont) use ($sub,$yr,$mn) {
				$joincont->where('subCat.mainid','=',$sub)
							->where('exp.year','=',$yr)
							->where('exp.month','=',$mn);
			});	
		} else {
			$query1 = $query1->where(function($joincont) use ($sub) {
				$joincont->where('subCat.mainid','=',$sub);
			});
		}
		$combined = $query->union($query1)
							->orderBy('year','DESC')
							->orderBy('month','DESC')
							->orderBy('BankName','ASC')
							->orderBy('bankaccno','ASC')
							->get();
		return $combined;
	}
	public static function transferhistorydetails($request,$sub,$yr,$mn) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer as main')
					->SELECT('bank.BankName','main.bankaccno','main.year','main.month','subCat.mainid','main.amount',
						'main.fee','main.remark_dtl AS remarks','main.file_dtl',
						'main.bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','main.id','bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'main.subject');
								$join->on('subCat.id', '=', 'main.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'main.bankname', '=', 'bank.id');
		if($yr!=""&&$mn!="") {		
			$query = $query->where(function($joincont) use ($sub,$yr,$mn) {
				$joincont->where('subCat.mainid','=',$sub)
							->where('main.year','=',$yr)
							->where('main.month','=',$mn);
			});	
		} else {
			$query = $query->where(function($joincont) use ($sub) {
				$joincont->where('subCat.mainid','=',$sub);
			});
		}	
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS
		$query1 = $db->table('dev_expenses as exp')
					->SELECT('bank.BankName','exp.bankaccno','exp.year','exp.month','subCat.mainid',
						'exp.amount',DB::raw('null as fee'),'exp.remark_dtl AS remarks','exp.file_dtl',
						'exp.date AS bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','exp.id' ,'bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'exp.subject');
								$join->on('subCat.id', '=', 'exp.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'exp.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query1 = $query1->WHERE('exp.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS			

		if($yr!=""&&$mn!="") {		
			$query1 = $query1->where(function($joincont) use ($sub,$yr,$mn) {
				$joincont->where('subCat.mainid','=',$sub)
							->where('exp.year','=',$yr)
							->where('exp.month','=',$mn);
			});	
		} else {
			$query1 = $query1->where(function($joincont) use ($sub) {
				$joincont->where('subCat.mainid','=',$sub);
			});
		}
		$page = Input::get('page', $request->page);
		$paginate = $request->plimit;
		$combined = $query->union($query1)
							->orderBy('year','DESC')
							->orderBy('month','DESC')
							->orderBy('BankName','ASC')
							->orderBy('bankaccno','ASC')
							->get();
							// ->toSql();
							// dd($combined);
		$slice = array_slice($combined, $paginate * ($request->page - 1), $paginate);
		return new LengthAwarePaginator($slice, count($combined), $paginate, $request->page);
	}
	public static function nickname($Acc,$bankid) {
		$sql = "SELECT * FROM mstbank WHERE AccNo='".$Acc."' AND BankName='".$bankid."'";
		$cards = DB::select($sql);
		return $cards;
	}
	public static function pettycash_history_details($yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = " AND date > '$accessDate'";
		}
		// END ACCESS RIGHTS
			$sql="SELECT * FROM dev_expenses ";

			if( $yr == "" && $mnth == "" )
				{
					$sql.="where pettyFlg = 1 $conditionAppend ORDER BY year DESC , month DESC ,date ASC";
				} else {
					$sql.="where pettyFlg = 1 $conditionAppend AND year ='$yr' AND month ='$mnth' ORDER BY year DESC , month DESC ,date ASC";
				}
			$cards = DB::select($sql);
		return $cards;
	}
	public static function pettycash_subhistory_details($delflg,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND date>'$accessDate'";
		}
		// END ACCESS RIGHTS
			$sql="SELECT * FROM dev_expenses ";
			if( $yr == "" && $mnth == "" )
				{
					$sql.="where pettyFlg = 1 $conditionAppend AND del_flg = '$delflg' ORDER BY year DESC , month DESC ,date ASC";
				} else {
					$sql.="where pettyFlg = 1 $conditionAppend AND del_flg = '$delflg' AND year ='$yr' AND month ='$mnth' ORDER BY year DESC , month DESC ,date ASC";
				}
			$cards = DB::select($sql);
		return $cards;
	}
	public static function transfer_subhistorydetailsamount($sub,$yr,$mnth) {
		$sql= "SELECT bank.BankName,main.bankaccno, main.year,subCat.id,main.month,main.amount,main.fee,
								main.remark_dtl AS remarks, main.file_dtl,
								main.bankdate,mainCat.Subject,mainCat.Subject_jp,subCat.sub_eng,subCat.sub_jap,main.id,bank.id AS bankid
								FROM dev_banktransfer AS main 
								LEFT JOIN inv_set_expensesub AS subCat ON subCat.mainid=main.subject AND 
								subCat.id=main.details 
								LEFT JOIN dev_expensesetting AS mainCat ON mainCat.id=subCat.mainid 
								LEFT JOIN mstbanks AS bank ON main.bankname=bank.id ";
				if($yr!=""&&$mnth!="")
				{			
				$sql.=" WHERE subCat.id='$sub' AND main.year='$yr' AND main.month='$mnth' ";
				}
				else{
				$sql.=" WHERE subCat.id='$sub' ";					
				}	
				$sql.=" UNION SELECT bank.BankName,exp.bankaccno, exp.year,subCat.id,exp.month,exp.amount,
								null as fee,exp.remark_dtl AS remarks, exp.file_dtl,
								exp.date as bankdate,mainCat.Subject,mainCat.Subject_jp,subCat.sub_eng,
								subCat.sub_jap,exp.id,bank.id AS bankid
								FROM dev_expenses AS exp 
								LEFT JOIN inv_set_expensesub AS subCat ON subCat.mainid=exp.subject 
								AND subCat.id=exp.details 
								LEFT JOIN dev_expensesetting AS mainCat ON mainCat.id=subCat.mainid 
								LEFT JOIN mstbanks AS bank ON exp.bankname=bank.id ";
								
				if($yr!=""&&$mnth!="")
				{			
				$sql.=" WHERE subCat.id='$sub' AND exp.year='$yr' AND exp.month='$mnth' ";
				}
				else{
				$sql.=" WHERE subCat.id='$sub' ";					
				}		
				$sql .= "ORDER BY year DESC, month DESC ,BankName ASC,bankaccno ASC ";
		$query = DB::select($sql);
		return $query;		
	}
	public static function transfer_subhistorydetailsamount1($request,$sub,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer as main')
					->SELECT('bank.BankName','main.bankaccno','main.year','main.month','subCat.id','main.amount',
						'main.fee','main.remark_dtl AS remarks','main.file_dtl',
						'main.bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','main.id','bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'main.subject');
								$join->on('subCat.id', '=', 'main.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'main.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS

		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($sub,$yr,$mnth) {
				$joincont->where('subCat.id','=',$sub)
							->where('main.year','=',$yr)
							->where('main.month','=',$mnth);
			});	
		} else {
			$query = $query->where(function($joincont) use ($sub) {
				$joincont->where('subCat.id','=',$sub);
			});
		}
		$query1 = $db->table('dev_expenses as exp')
					->SELECT('bank.BankName','exp.bankaccno','exp.year','exp.month','subCat.id',
						'exp.amount',DB::raw('null as fee'),'exp.remark_dtl AS remarks','exp.file_dtl',
						'exp.date AS bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','exp.id' ,'bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'exp.subject');
								$join->on('subCat.id', '=', 'exp.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'exp.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query1 = $query1->WHERE('exp.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS

		if($yr!=""&&$mnth!="") {		
			$query1 = $query1->where(function($joincont) use ($sub,$yr,$mnth) {
				$joincont->where('subCat.id','=',$sub)
							->where('exp.year','=',$yr)
							->where('exp.month','=',$mnth);
			});	
		} else {
			$query1 = $query1->where(function($joincont) use ($sub) {
				$joincont->where('subCat.id','=',$sub);
			});
		}
		$combined = $query->union($query1)
							->orderBy('year','DESC')
							->orderBy('month','DESC')
							->orderBy('BankName','ASC')
							->orderBy('bankaccno','ASC')
							->get();
		return $combined;
	}
	public static function transfer_subhistorydetails($request,$sub,$yr,$mnth) {
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer as main')
					->SELECT('bank.BankName','main.bankaccno','main.year','main.month','subCat.id','main.amount',
						'main.fee','main.remark_dtl AS remarks','main.file_dtl',
						'main.bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','main.id','bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'main.subject');
								$join->on('subCat.id', '=', 'main.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'main.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query = $query->WHERE('main.bankdate', '>', $accessDate);
		}
		// END ACCESS RIGHTS

		if($yr!=""&&$mnth!="") {		
			$query = $query->where(function($joincont) use ($sub,$yr,$mnth) {
				$joincont->where('subCat.id','=',$sub)
							->where('main.year','=',$yr)
							->where('main.month','=',$mnth);
			});	
		} else {
			$query = $query->where(function($joincont) use ($sub) {
				$joincont->where('subCat.id','=',$sub);
			});
		}
		$query1 = $db->table('dev_expenses as exp')
					->SELECT('bank.BankName','exp.bankaccno','exp.year','exp.month','subCat.id',
						'exp.amount',DB::raw('null as fee'),'exp.remark_dtl AS remarks','exp.file_dtl',
						'exp.date AS bankdate','mainCat.Subject','mainCat.Subject_jp','subCat.sub_eng','subCat.sub_jap','exp.id' ,'bank.id AS bankid')
					->leftJoin('inv_set_expensesub AS subCat', function($join)
							{
								$join->on('subCat.mainid', '=', 'exp.subject');
								$join->on('subCat.id', '=', 'exp.details');
							})
					->leftJoin('dev_expensesetting AS mainCat', 'mainCat.id', '=', 'subCat.mainid')
					->leftJoin('mstbanks AS bank', 'exp.bankname', '=', 'bank.id');
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$query1 = $query1->WHERE('exp.date', '>', $accessDate);
		}
		// END ACCESS RIGHTS

		if($yr!=""&&$mnth!="") {		
			$query1 = $query1->where(function($joincont) use ($sub,$yr,$mnth) {
				$joincont->where('subCat.id','=',$sub)
							->where('exp.year','=',$yr)
							->where('exp.month','=',$mnth);
			});	
		} else {
			$query1 = $query1->where(function($joincont) use ($sub) {
				$joincont->where('subCat.id','=',$sub);
			});
		}
		$page = Input::get('page', $request->page);
		$paginate = $request->plimit;
		$combined = $query->union($query1)
							->orderBy('year','DESC')
							->orderBy('month','DESC')
							->orderBy('BankName','ASC')
							->orderBy('bankaccno','ASC')
							->get();
		$slice = array_slice($combined, $paginate * ($request->page - 1), $paginate);
		return new LengthAwarePaginator($slice, count($combined), $paginate, $request->page);		
	}
	public static function fnsalaryDataamount($request) {
		$db = DB::connection('mysql');
		$query1 = $db->table('inv_salary')
					->SELECT('emp_mstemployees.FirstName','emp_mstemployees.LastName','inv_salary.salary as amount','inv_salary.charge as fee','inv_salary.salaryDate as bankdate','inv_salary.*','mstbanks.BankName','mstbank.AccNo As bankaccno')
					->leftJoin('mstbank', 'mstbank.id', '=', 'inv_salary.bankId')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->leftJoin('emp_mstemployees', 'inv_salary.empNo', '=', 'emp_mstemployees.Emp_ID')
					->where('inv_salary.empNo','=',$request->empid);
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query1 = $query1->WHERE('inv_salary.salaryDate', '>', $accessDate);
			}
			// END ACCESS RIGHTS
				$query1 = $query1->orderBy('inv_salary.year', 'DESC')
						->orderBy('inv_salary.month', 'DESC')
						->orderBy('inv_salary.salaryDate', 'ASC')
						->orderBy('inv_salary.empNo', 'ASC')
						->get();
		return $query1;
	}
	public static function fnsalaryData($request) {
		$db = DB::connection('mysql');
		$query1 = $db->table('inv_salary')
					->SELECT('emp_mstemployees.FirstName','emp_mstemployees.LastName','inv_salary.salary as amount','inv_salary.charge as fee','inv_salary.salaryDate as bankdate','inv_salary.*','mstbanks.BankName','mstbank.AccNo As bankaccno')
					->leftJoin('mstbank', 'mstbank.id', '=', 'inv_salary.bankId')
					->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
					->leftJoin('emp_mstemployees', 'inv_salary.empNo', '=', 'emp_mstemployees.Emp_ID')
					->where('inv_salary.empNo','=',$request->empid);
			// ACCESS RIGHTS
			// CONTRACT EMPLOYEE
			if (Auth::user()->userclassification == 1) {
				$accessDate = Auth::user()->accessDate;
				$query1 = $query1->WHERE('inv_salary.salaryDate', '>', $accessDate);
			}
			// END ACCESS RIGHTS
				$query1 = $query1->orderBy('inv_salary.year', 'DESC')
					->orderBy('inv_salary.month', 'DESC')
					->orderBy('inv_salary.salaryDate', 'ASC')
					->orderBy('inv_salary.empNo', 'ASC')
					->paginate($request->plimit);
		return $query1;
	}
	public static function fnsalaryDatatotal($request,$bankid,$accNo,$yr,$mnth) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND inv_salary.salaryDate >'$accessDate'";
		}
		// END ACCESS RIGHTS
			$sql = "SELECT emp_mstemployees.FirstName,emp_mstemployees.LastName,inv_salary.salary as amount,inv_salary.charge as 	 fee, inv_salary.salaryDate as bankdate,inv_salary.*,mstbanks.BankName,mstbank.AccNo As bankaccno 
					FROM inv_salary LEFT JOIN mstbank ON mstbank.id=inv_salary.bankId 
			 		LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
			 		LEFT JOIN emp_mstemployees 
				  	ON inv_salary.empNo=emp_mstemployees.Emp_ID WHERE  inv_salary.empNo='$request->empid' $conditionAppend";
				  	if($yr!=""&&$mnth!=""){			
						$sql.=" AND year='$yr' AND  month='$mnth'";
					} 
					$sql .= " ORDER BY inv_salary.year DESC, inv_salary.month DESC,inv_salary.salaryDate ASC,inv_salary.empNo ASC";
		    $cards = DB::select($sql);
		return $cards;
		}
	public static function excel_download($request) {
		// ACCESS RIGHTS
		// CONTRACT EMPLOYEE
		$conditionAppend = "";
		$conditionAppendSalary = "";
		if (Auth::user()->userclassification == 1) {
			$accessDate = Auth::user()->accessDate;
			$conditionAppend = "AND (main.bankdate > '$accessDate' OR main.accessFlg = 1)";
			$conditionAppendSalary = "AND sal.salaryDate > '$accessDate'";
		}
		// END ACCESS RIGHTS
		$selectedYearMonth = explode("-", $request->selYearMonth);
		$year = $selectedYearMonth[0];
		$month = $selectedYearMonth[1];
		$sql = "SELECT * from (SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.amount,
											main.fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				AND others!=1 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 


									UNION ALL
										SELECT main.id,
											main.billno,
											CASE main.loan_flg
												 WHEN 1 THEN (SELECT BankName FROM mstbank WHERE id=main.bankname) ELSE 
												 main.bankname
											END as OrderBankName,

											CASE main.loan_flg
												 WHEN 1 THEN (SELECT AccNo FROM mstbank WHERE id=main.bankname) ELSE 
												 bank.AccNo
											END as OrderAccNo,
											main.emp_ID AS empNo,
											emp.FirstName AS FirstNames,
											emp.LastName AS LastNames,
											main.bankdate,
											NULL AS salaryMonth,
											main.subject,
											main.details,
											main.bankname,
											main.bankaccno,
											main.fee AS amount,
											NULL AS fee,
											NULL AS bankId,
											main.file_dtl,
											main.remark_dtl,
											main.del_flg,
											main.loan_flg,
											bank.Bank_NickName,
											main.salaryFlg,
											NULL AS FirstName,
											NULL AS LastName,
											banks.BankName AS bname,
											bank.AccNo,
											CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
											bank.id as mainbankid,
											main.loanType,
											main.year,
											main.month,
											main.submit_flg,
											main.edit_flg,
											main.copy_month_day,
											main.Ins_DT,
											main.Up_DT,
											main.UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM dev_banktransfer main 
										LEFT JOIN mstbank bank on main.bankname=bank.BankName and main.bankaccno=bank.AccNo
										LEFT JOIN mstbanks banks ON banks.id=bank.BankName 
										LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
					   	 				WHERE year='$year' AND month='$month' 
					   	 				AND others!=1 
					   	 				$conditionAppend
					   	 				AND salaryFlg!=1 

							UNION ALL
								SELECT sal.id,
											NULL AS billno,
											mstbank.BankName AS OrderBankName,
											mstbank.AccNo AS OrderAccNo,
											sal.empNo AS empNo,
											emp_mstemployees.FirstName AS FirstNames,
											emp_mstemployees.LastName AS LastNames,
											sal.salaryDate as bankdate,
											sal.salaryMonth as salaryMonth,
											NULL AS subject,
											NULL AS details,
											mstbank.BankName AS bankname,
											mstbank.AccNo AS bankaccno,
											REPLACE(sal.salary, ',', '') AS amount,
											REPLACE(sal.charge, ',', '') AS fee,
											sal.bankId AS bankId,
											NULL AS file_dtl,
											NULL AS remark_dtl,
											sal.delFlg,
											NULL AS loan_flg,	
											mstbank.Bank_NickName,
											1 AS salaryFlg,
											emp_mstemployees.FirstName,
											emp_mstemployees.LastName,
											banks.BankName bname,
											mstbank.AccNo,
											InsDT AS Ins_DTTM,
											mstbank.id AS mainbankid,
											0 AS loanType,
											sal.year,
											sal.month,
											NULL AS submit_flg,
											NULL AS edit_flg,
											NULL AS copy_month_day,
											NULL AS Ins_DT,
											NULL AS Up_DT,
											NULL AS UP_TM,
											NULL AS transaction_flg,
											NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'
										$conditionAppendSalary

									
										UNION ALL
										SELECT sal.id,
										NULL AS billno,
										mstbank.BankName AS OrderBankName,
										mstbank.AccNo AS OrderAccNo,
										sal.empNo AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										sal.salaryDate as bankdate,
										sal.salaryMonth as salaryMonth,
										NULL AS subject,
										NULL AS details,
										mstbank.BankName AS bankname,
										mstbank.AccNo AS bankaccno,
										REPLACE(sal.charge, ',', '') AS amount,
										NULL AS fee,
										sal.bankId AS bankId,
										NULL AS file_dtl,
										NULL AS remark_dtl,
										sal.delFlg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										1 AS salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										banks.BankName bname,
										mstbank.AccNo,
										InsDT AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										sal.year,
										sal.month,
										NULL AS submit_flg,
										NULL AS edit_flg,
										NULL AS copy_month_day,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										NULL AS transaction_flg,
										NULL AS transfer_flg
										FROM inv_salary AS sal 
										LEFT JOIN emp_mstemployees ON sal.empNo=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank ON mstbank.id=sal.bankId
										LEFT JOIN mstbanks banks ON banks.id=mstbank.BankName 
										WHERE sal.year='$year' AND sal.month='$month'


										 AND bankId!='999'


							 UNION ALL
								SELECT main.id,
										main.billno,
										main.bankname AS OrderBankName,
										main.bankaccno AS OrderAccNo,
										main.emp_ID AS empNo,
										emp_mstemployees.FirstName AS FirstNames,
										emp_mstemployees.LastName AS LastNames,
										main.date as bankdate,
										NULL AS salaryMonth,
										main.subject AS subject,
										main.details AS details,
										mstbanks.BankName AS bankname,
										main.bankaccno AS bankaccno,
										main.amount AS amount,
										NULL AS fee,
										NULL AS bankId,
										main.file_dtl,
										main.remark_dtl,
										main.del_flg,
										NULL AS loan_flg,
										mstbank.Bank_NickName,
										main.salaryFlg,
										emp_mstemployees.FirstName,
										emp_mstemployees.LastName,
										mstbanks.BankName AS bname,
										mstbank.AccNo,
										CONCAT(main.Ins_DT,' ',main.Ins_TM) AS Ins_DTTM,
										mstbank.id AS mainbankid,
										0 AS loanType,
										main.year,
										main.month,
										main.edit_flg,
										main.submit_flg,
										main.copy_month_day,
										NULL AS Ins_DT,
										NULL AS Up_DT,
										NULL AS UP_TM,
										main.transaction_flg,
										main.transfer_flg
										FROM dev_expenses main 
										LEFT JOIN emp_mstemployees ON main.emp_ID=emp_mstemployees.Emp_ID
										LEFT JOIN mstbank on main.bankname=mstbank.BankName  and main.bankaccno=mstbank.AccNo
										LEFT JOIN mstbanks ON mstbanks.id=mstbank.BankName 
										WHERE main.year='$year' AND main.month='$month'
										$conditionAppendSalary
										AND main.carryForwardFlg!=1
										AND main.transaction_flg IS NOT NULL) 
																	AS DDD";
		$result = DB::select($sql);
		return $result;
	}
	public static function editothersquery($request,$id){
		$db = DB::connection('mysql');
		$query = $db->table('dev_banktransfer')
					->SELECT('*')
					->where('id','=',$id)
					->get();
		return $query;
	}
	public static function othersupdate($request){
		$accessFlg = 0;
		$receipt = 0;
		$bank = explode("-", $request->bankname);
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount_1);
		$spldm = explode('-', $request->txt_startdate);

		$update=DB::table('dev_banktransfer')
							->where('id','=',$request->editid)
							->update(
						    ['bankdate' => $request->txt_startdate, 
							'bankname' => $request->bankname, 
							'bankname' => $bank[0],
							'bankaccno' => $bank[1],
							'amount'	=> $amount,
							'receipt' => $receipt,
							'remark_dtl' =>	$request->Remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'=> 0,
							'del_flg' => 1,
							'others' => 1,
							'accessFlg'	=>	$accessFlg,
							'Ins_DT' => date('Y-m-d'),
							'Ins_TM' => date('H:i:s'),
							'CreatedBy' => $name,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
		return $update;
	}
	public static function fngetothers($request){
		$accessFlg = 0;
		$receipt = 0;
		$bank = explode("-", $request->bankname);
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$amount = str_replace(",", "", $request->amount_1);
		$spldm = explode('-', $request->txt_startdate);
		$insert=DB::table('dev_banktransfer')
							->insert(
						    ['id'	=>'', 
							'bankdate' => $request->txt_startdate, 
							'bankname' => $request->bankname, 
							'bankname' => $bank[0],
							'bankaccno' => $bank[1],
							'amount'	=>	$amount,
							'receipt' => $receipt,
							'remark_dtl' =>	$request->Remarks,
							'year'	=>	$spldm[0],
							'month'	=>	$spldm[1],
							'copy_month_day'=> 0,
							'del_flg' => 1,
							'others' => 1,
							'accessFlg'	=>	$accessFlg,
							'Ins_DT' => date('Y-m-d'),
							'Ins_TM' => date('H:i:s'),
							'CreatedBy' => $name,
							'Up_DT' => date('Y-m-d'),
							'UP_TM' => date('H:i:s'),
							'UpdatedBy' => $name]);
		return $insert;
	}
}