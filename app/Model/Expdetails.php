<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
use PDO;
class Expdetails extends Model {
	public static function fnGetAccountPeriod() {
		$result= DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
						->GET();
		return $result;
	}
	public static function datapreyear() {
		$curYear =date('Y');
		$sql = DB::SELECT(DB::raw(
					"SELECT * FROM  (
						SELECT main.id,sub.id AS subid,SUBSTRING(t.bankdate,1,7) AS date,IFNULL(main.Subject,'Loan Payment') AS Subject
						,IFNULL(sub.sub_eng,'Loan Payment') AS sub_eng
						,IFNULL(main.Subject_jp,'Loan Payment') AS Subject_jp
						,IFNULL(sub.sub_jap,'Loan Payment') AS sub_jap
						,(t.amount+t.fee) AS amount 
						FROM dev_banktransfer t 
						LEFT JOIN dev_expensesetting main ON t.subject=main.id
						LEFT JOIN inv_set_expensesub sub ON t.details=sub.id
						WHERE t.del_flg=0 
						UNION ALL
						SELECT main.id,sub.id AS subid,SUBSTRING(e.date,1,7) AS date,IFNULL(main.Subject,'Petty'),IFNULL(sub.sub_eng,'Petty'),
						IFNULL(main.Subject_jp,'Petty'),IFNULL(sub.sub_jap,'Petty'),
						e.amount
						FROM dev_expenses e
						LEFT JOIN dev_expensesetting main ON e.subject=main.id
						LEFT JOIN inv_set_expensesub sub ON e.details=sub.id
						 WHERE e.transaction_flg IS NULL  AND e.del_flg='1'
						) AS a WHERE SUBSTRING(a.date,1,4)<='".$curYear."' ORDER BY a.date DESC"));
		return $sql;
	}

	public static function yrmndetail($fromdate,$todate,$cur,$cusFlag) {
		$sql = "SELECT a.*,SUM(a.amount) AS totamt FROM 
						(
						SELECT main.id,sub.id AS subid,SUBSTRING(t.bankdate,1,7) AS date,IFNULL(main.Subject,'Loan Payment') AS Subject
						,IFNULL(sub.sub_eng,'Loan Payment') AS sub_eng
						,IFNULL(main.Subject_jp,'Loan Payment') AS Subject_jp
						,IFNULL(sub.sub_jap,'Loan Payment') AS sub_jap
						,(t.amount+t.fee) AS amount 
						FROM dev_banktransfer t 
						LEFT JOIN dev_expensesetting main ON t.subject=main.id
						LEFT JOIN inv_set_expensesub sub ON t.details=sub.id
						WHERE t.del_flg=0 
						UNION ALL
						SELECT main.id,sub.id AS subid,SUBSTRING(e.date,1,7) AS date,IFNULL(main.Subject,'Petty'),IFNULL(sub.sub_eng,'Petty'),
						IFNULL(main.Subject_jp,'Petty'),IFNULL(sub.sub_jap,'Petty'),
						e.amount
						FROM dev_expenses e
						LEFT JOIN dev_expensesetting main ON e.subject=main.id
						LEFT JOIN inv_set_expensesub sub ON e.details=sub.id
						 WHERE e.transaction_flg IS NULL  AND e.del_flg='1'
						) AS a  ";
		if($fromdate!=""&&$todate!="" && $cur!="")
		 {
			$sql.="WHERE a.date >'".$fromdate."' AND a.date <'".$todate."' AND a.date<='".$cur."'
				GROUP BY  a.sub_eng, a.Subject, a.date ORDER BY a.date ASC,a.Subject ASC";
		}else if($fromdate!="" && $todate=="" && $cur=="") {
			$sql.="WHERE a.date <='".$fromdate."'
					GROUP BY  a.sub_eng, a.Subject, a.date ORDER BY a.date ASC,a.Subject ASC";
		} else if($fromdate=="" && $todate!="" && $cur=="") {
			$sql.="WHERE  a.date >='".$todate."'
					GROUP BY  a.sub_eng, a.Subject, a.date ORDER BY a.date ASC,a.Subject ASC";
		}else if($cusFlag){
			$sql.="GROUP BY  a.sub_eng, a.Subject, a.date ORDER BY a.date DESC,a.Subject ASC";
		} else {
			$sql.="WHERE a.date >'".$fromdate."' AND a.date <'".$todate."'
					GROUP BY  a.sub_eng, a.Subject, a.date ORDER BY a.date ASC,a.Subject ASC";
		}
		$cards = DB::select($sql);
		return $cards;
	}
	public static function expensedetail($fromdate,$todate,$curvalue) {
		$result = DB::select("CALL dev_bank('$fromdate', '$todate','$curvalue')");
		return $result;
	}
	public static function cusdetail($fromdate,$todate,$case) {
				$expense_exe1=DB::unprepared(DB::raw("SET @sql1 = NULL;"));
				$expense_exe2=DB::unprepared(DB::raw("DROP TABLE IF EXISTS `temp1`;"));
				$selectdata ="CREATE  TEMPORARY TABLE temp1 ENGINE=MEMORY 
							SELECT a.*,SUM(a.amount) AS totamt ".$case."
							 FROM 
							(
							SELECT main.id,sub.id AS subid,SUBSTRING(t.bankdate,1,7) AS date,
							case when t.salaryFlg !=1 then IFNULL(main.Subject,'Loan Payment')else IFNULL(main.Subject,'Paid Salary')end AS Subject,
							case when t.salaryFlg !=1 then IFNULL(main.Subject_jp,'Loan Payment')else IFNULL(main.Subject_jp,'Paid Salary')end AS Subject_jp ,
							case when t.salaryFlg !=1 then IFNULL(sub.sub_eng,'Loan Payment')else IFNULL(sub.sub_eng,'Paid Salary')end AS sub_eng,
							case when t.salaryFlg !=1 then IFNULL(sub.sub_jap,'Loan Payment')else IFNULL(sub.sub_jap,'Paid Salary')end AS sub_jap ,
							(t.amount+t.fee) AS amount 
							FROM dev_banktransfer t 
							LEFT JOIN dev_expensesetting main ON t.subject=main.id
							LEFT JOIN inv_set_expensesub sub ON t.details=sub.id
							WHERE t.del_flg=0
							UNION ALL
							SELECT main.id,sub.id AS subid,SUBSTRING(e.date,1,7) AS date,
							case when e.salaryFlg !=1 then IFNULL(main.Subject,'Petty')else IFNULL(main.Subject,'Paid Salary')end AS Subject,
							case when e.salaryFlg !=1 then IFNULL(main.Subject_jp,'Petty')else IFNULL(main.Subject_jp,'Paid Salary')end AS Subject_jp ,
							case when e.salaryFlg !=1 then IFNULL(sub.sub_eng,'Petty')else IFNULL(sub.sub_eng,'Paid Salary')end AS sub_eng,
							case when e.salaryFlg !=1 then IFNULL(sub.sub_jap,'Petty')else IFNULL(sub.sub_jap,'Paid Salary')end AS sub_jap,
							e.amount
							FROM dev_expenses e
							LEFT JOIN dev_expensesetting main ON e.subject=main.id
							LEFT JOIN inv_set_expensesub sub ON e.details=sub.id
							 WHERE (e.transaction_flg IS NULL OR e.transaction_flg=0)  AND e.del_flg='1'
							) AS a WHERE a.date>='".$fromdate."' AND a.date<='".$todate."'
							group by a.Subject,a.sub_eng, a.date ORDER BY a.date DESC ;";
					$query = DB::select($selectdata);
				$result = DB::select("CALL expensesdetails_customer_create()");
				return $result;
		}
		public static function mondetail($fromdate,$todate,$case) {
				$expense_exe1=DB::unprepared(DB::raw("SET @sql1 = NULL;"));
				$expense_exe2=DB::unprepared(DB::raw("DROP TABLE IF EXISTS `temp1`;"));
				$selectdata ="CREATE  TEMPORARY TABLE temp1 ENGINE=MEMORY 
							SELECT a.*,SUM(a.amount) AS totamt ".$case." 
							 FROM 
							(
							SELECT SUBSTRING(t.bankdate,1,7) AS date,IFNULL(main.Subject,'Loan Payment') AS Subject
							,IFNULL(sub.sub_eng,'Loan Payment') AS sub_eng
							,(t.amount+t.fee) AS amount 
							FROM dev_banktransfer t 
							LEFT JOIN dev_expensesetting main ON t.subject=main.id
							LEFT JOIN inv_set_expensesub sub ON t.details=sub.id
							WHERE t.del_flg=0
							UNION ALL
							SELECT SUBSTRING(e.date,1,7) AS date,IFNULL(main.Subject,'Petty'),IFNULL(sub.sub_eng,'Petty'),e.amount
							FROM dev_expenses e
							LEFT JOIN dev_expensesetting main ON e.subject=main.id
							LEFT JOIN inv_set_expensesub sub ON e.details=sub.id
							 WHERE (e.transaction_flg IS NULL OR e.transaction_flg=0)  AND e.del_flg='1' 
							) AS a WHERE a.date>='".$fromdate."' AND a.date<='".$todate."'
							group by  SUBSTRING(a.date,6,2),Period ORDER BY a.date DESC;";
				$query = DB::select($selectdata);
				$result = DB::select("CALL expensesdetails_monthly_create()");
			return $result;
		}
}