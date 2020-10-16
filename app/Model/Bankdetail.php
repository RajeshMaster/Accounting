<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
use Paginator;
class Bankdetail extends Model {
	public static function bankindex($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbank AS bnk')
						->SELECT('banDet.id','banname.id AS bnkid','brncname.id AS brnchid','bnk.id AS bankid','bnk.AccNo','banDet.startDate','banDet.balance','banDet.processFlg','banDet.bankId AS balbankid','banname.BankName AS banknm','brncname.BranchName AS brnchnm')
						->leftJoin('inv_allbank_details AS banDet', 'bnk.id', '=', 'banDet.bankId')
						->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bnk.BankName')
						->leftJoin('mstbankbranch AS brncname', function($join)
							{
								$join->on('brncname.BankId', '=', 'bnk.BankName');
								$join->on('brncname.id', '=', 'bnk.BranchName');
							})
						->where('bnk.delflg','=','0')
						->orderByRaw("CAST(banDet.balance as SIGNED INTEGER) DESC");
						// ->toSql();
						// dd($query);
						// ->get();
		return $query;
	}
	public static function bankindex1($request) {
		$db = DB::connection('mysql');
		$query= $db->table('mstbank AS bnk')
						->SELECT('banDet.id','banname.id AS bnkid','brncname.id AS brnchid','bnk.id AS bankid','bnk.AccNo','banDet.startDate','banDet.balance','banDet.processFlg','banDet.bankId AS balbankid','banname.BankName AS banknm','brncname.BranchName AS brnchnm')
						->leftJoin('inv_allbank_details AS banDet', 'bnk.id', '=', 'banDet.bankId')
						->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bnk.BankName')
						->leftJoin('mstbankbranch AS brncname', function($join)
							{
								$join->on('brncname.BankId', '=', 'bnk.BankName');
								$join->on('brncname.id', '=', 'bnk.BranchName');
							})
						->where('bnk.delflg','=','0')
						->orderByRaw("CAST(banDet.balance as SIGNED INTEGER) DESC")
						// ->toSql();
						// dd($query);
						->get();
		return $query;
	}
	public static function getbankdetails($request,$bank_names,$branchname,$accno,$accnoid,$startdate,$curDate,$from_date,$to_date,$cdm,$pd,$paginateneed=null) {
		$year = "";
		$month = "";
		if($cdm!=""){
			$year = substr($cdm,0,4);
			$month =substr($cdm,5,2);
		}
		if($pd!=""){
			$year = substr($pd,0,4);
			$month =substr($pd,5,2);
		}
		$db = DB::connection('mysql');
		$sql = $db->TABLE($db->raw("(
				SELECT ln.id AS ID,ln.receivedDate AS date,NULL AS credit,CONCAT('-',ln.amount) AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee, NULL AS pamt,NULL AS debit,
					ln.loanName AS cmpny_name,NULL AS mnsub,NULL AS susub,ln.remarks AS remarks ,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID,ln.chk_flg AS chk_flg, 5 AS paymentsam,
					ln.id AS idcheck,ln.ins_Datetime AS dateTime FROM inv_loandetails ln WHERE ln.bankId='$accnoid' AND ln.reflectPassbookflg=1
				UNION ALL 
				SELECT NULL AS ID,p.payment_date AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,
					p.deposit_amount AS pamt,NULL AS debit,p.company_name AS cmpny_name,NULL AS mnsub,NULL AS susub,p.remarks AS remarks ,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID,chk_flg, 1 AS paymentsam,p.id AS idcheck,p.created_datetime AS dateTime
					FROM dev_payment_registration p WHERE p.bankid='$bank_names' AND p.branchid='$branchname' AND p.acc_no='$accnoid' 
					UNION ALL 
					SELECT NULL AS ID,c.date AS date ,c.amount AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,NULL AS pamt,
					NULL AS debit,NULL AS cmpny_name,NULL AS mnsub,NULL AS susub,c.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 2 AS paymentsam,c.id AS idcheck,CONCAT(c.Ins_DT,' ',c.Ins_TM) AS dateTime
					FROM dev_expenses c WHERE c.bankname='$bank_names' AND c.bankaccno='$accno'  AND c.transaction_flg = '2'
					UNION ALL
					SELECT NULL AS ID,c.date AS date ,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,
						NULL AS pamt,c.amount AS debit,NULL AS cmpny_name,NULL AS mnsub,NULL AS susub,c.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 2 AS paymentsam,c.id AS idcheck,CONCAT(c.Ins_DT,' ',c.Ins_TM) AS dateTime
					FROM dev_expenses c WHERE c.bankname='$bank_names' AND c.bankaccno='$accno' AND  c.transaction_flg = '1' 
					UNION ALL 
					SELECT NULL AS ID,c.date AS date ,c.amount AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,NULL AS pamt,
					NULL AS debit,NULL AS cmpny_name,NULL AS mnsub,NULL AS susub,c.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 2 AS paymentsam,c.id AS idcheck,CONCAT(c.Ins_DT,' ',c.Ins_TM) AS dateTime
					FROM dev_expenses c WHERE c.bankname='$bank_names' AND c.bankaccno='$accno'  AND c.transaction_flg = '3' AND c.transfer_flg = '2'
					UNION ALL
					SELECT NULL AS ID,c.date AS date ,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,
						NULL AS pamt,c.amount AS debit,NULL AS cmpny_name,NULL AS mnsub,NULL AS susub,c.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 2 AS paymentsam,c.id AS idcheck,CONCAT(c.Ins_DT,' ',c.Ins_TM) AS dateTime
					FROM dev_expenses c WHERE c.bankname='$bank_names' AND c.bankaccno='$accno' AND  c.transaction_flg = '3' AND c.transfer_flg = '1'
					UNION ALL 
					SELECT t.id AS ID,t.bankdate AS date,NULL AS credit,NULL AS loanamt,t.amount AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee,
					NULL AS pamt,NULL AS debit,NULL AS cmpny_name,t.subject AS mnsub,t.details AS susub,t.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 3 AS paymentsam,t.id AS idcheck,CONCAT(t.Ins_DT,' ',t.Ins_TM) AS dateTime
					FROM dev_banktransfer t WHERE t.bankname='$bank_names' AND t.bankaccno='$accno' AND t.amount!=0 AND
					 t.salaryFlg!=1
					UNION ALL 
					SELECT t.id AS ID,t.bankdate AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,t.fee AS fee,NULL AS lamt,NULL AS lfee,
					NULL AS pamt,NULL AS debit,NULL AS cmpny_name,t.subject AS mnsub,t.details AS susub,NULL AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 3 AS paymentsam,t.id AS idcheck,CONCAT(t.Ins_DT,' ',t.Ins_TM) AS dateTime
					FROM dev_banktransfer t WHERE t.bankname='$bank_names' AND t.bankaccno='$accno' AND t.fee!=0 AND 
					t.salaryFlg!=1
					UNION ALL 
					SELECT t.id AS ID,t.bankdate AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,t.amount AS lamt,NULL AS lfee,
					NULL AS pamt,NULL AS debit,NULL AS cmpny_name,t.subject AS mnsub,t.details AS susub,t.remark_dtl AS remarks,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 3 AS paymentsam,t.id AS idcheck,CONCAT(t.Ins_DT,' ',t.Ins_TM) AS dateTime
					FROM dev_banktransfer t WHERE t.bankname='$accnoid'  AND t.loan_flg ='1' AND t.amount!=0
					UNION ALL 
					SELECT t.id AS ID,t.bankdate AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,t.fee AS lfee,
					NULL AS pamt,NULL AS debit,NULL AS cmpny_name,NULL AS mnsub,NULL AS susub,NULL AS remarks
					,NULL AS samt,NULL AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID ,chk_flg, 3 AS paymentsam,t.id AS idcheck,CONCAT(t.Ins_DT,' ',t.Ins_TM) AS dateTime
					FROM dev_banktransfer t WHERE t.bankname='$accnoid'  AND t.loan_flg ='1' AND t.fee!=0
					UNION ALL 
					SELECT s.id AS ID,s.salaryDate AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee, NULL AS pamt,NULL AS debit,NULL AS cmpny_name,
					NULL AS mnsub,NULL AS susub,NULL AS remarks,s.salary AS samt,NULL AS sfee,e.FirstName AS fname,e.LastName AS lname,e.Emp_ID AS emp_ID,chk_flg, 4 AS paymentsam,s.id AS idcheck,s.InsDT AS dateTime FROM inv_salary s 
					LEFT JOIN emp_mstemployees e ON e.Emp_ID=s.empNo
					WHERE s.bankId='$accnoid' AND s.salary!=0
					UNION ALL
					SELECT s.id AS ID,s.salaryDate AS date,NULL AS credit,NULL AS loanamt,NULL AS tamt,NULL AS fee,NULL AS lamt,NULL AS lfee, NULL AS pamt,NULL AS debit,NULL AS cmpny_name,
					NULL AS mnsub,NULL AS susub,NULL AS remarks,NULL AS samt,s.charge AS sfee,NULL AS fname,NULL AS lname,NULL AS emp_ID,chk_flg, 4 AS paymentsam,s.id AS idcheck,s.InsDT AS dateTime FROM inv_salary s 
					WHERE s.bankId='$accnoid' AND s.charge!=0
					)
					 AS a"));
					 // if($from_date && $to_date !=""){ /*AND a.date > '$from_date' AND a.date < '$to_date'*/
					 // 	$sql.=" WHERE a.date >='$startdate' AND a.date <='$curDate' 
					 //  ORDER BY a.date ASC,a.ID ASC ";
					 // }
					 if($from_date!=""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$from_date) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'")
											->WHERERAW("a.date <= '$from_date'");
								});
					 }else if($to_date!=""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$to_date) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'")
											->WHERERAW("a.date >= '$to_date'");
								});
					 }else if($cdm!="" && $month!=""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$year,$month) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'")
											->WHERERAW("SUBSTRING(a.date,1,4)='$year'")
											->WHERERAW("SUBSTRING(a.date,6,2)='$month'");
								});
					 }else if($cdm!="" && $month==""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$year) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'")
											->WHERERAW("SUBSTRING(a.date,1,4)='$year'");
								});
					 }else if($pd!="" && $month!=""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$pd) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'")
											->WHERERAW("SUBSTRING(a.date,1,7) <='$pd'");
								});
					 }else if($pd!="" && $month==""){
					 	$sql = $sql->where(function($joincont) use ($startdate,$curDate,$year) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											->WHERERAW("a.date <= '$curDate'") 
											->WHERERAW("SUBSTRING(a.date,1,4) <='$year'");
								});
					 } else{
						$sql = $sql->where(function($joincont) use ($startdate,$curDate) {
									$joincont->WHERERAW("a.date >= '$startdate'")
											 ->WHERERAW("a.date <= '$curDate'");
								});
					 }
		if ($request->checkflg == "1" && $paginateneed!=1) {
			$sql = $sql->ORDERBY('a.date', 'ASC')
						->ORDERBY('a.dateTime', 'ASC')
						->ORDERBY('a.ID', 'ASC')
						->paginate($request->plimit);
						// ->toSql();
						// dd($sql);
		} else {
			$sql = $sql->ORDERBY('a.date', 'ASC')
						->ORDERBY('a.dateTime', 'ASC')
						->ORDERBY('a.ID', 'ASC')
						->get();
						// ->toSql();
						// dd($sql);
		}
		return $sql;
	}
	public static function updateReccheck($request) {
		if($request->pay == 1) {
			$check=DB::table('dev_payment_registration')
						->where('id', $request->idcheck)
						->update([
						'chk_flg' => 1,
						]);
		} else if($request->pay == 2) {
			$check=DB::table('dev_expenses')
						->where('id', $request->idcheck)
						->update([
						'chk_flg' => 1,
						]);
		} else if($request->pay == 3) {
			$check=DB::table('dev_banktransfer')
						->where('id', $request->idcheck)
						->update([
						'chk_flg' => 1,
						]);
		} else if($request->pay == 5) {
			$check=DB::table('inv_loandetails')
						->where('id', $request->idcheck)
						->update([
						'chk_flg' => 1,
						]);
		} else {
			$check=DB::table('inv_salary')
						->where('id', $request->idcheck)
						->update([
						'chk_flg' => 1,
						]);
		}
		return $check;
	}
	public static function countRec($request) {
		$sql = "SELECT max(id) AS id FROM inv_allbank_details";
		$query = DB::SELECT($sql);
		return $query[0]->id;
	}
	public static function fetch($count) {
		$query=DB::table('inv_allbank_details')
						->SELECT('*')
						->where('id', $count)
						->get();
		return $query;
	}
	public static function insertRec($request) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$insert=DB::table('inv_allbank_details')
						->insert([
						'bankId' => $request->bankid,
						'startDate' => $request->txt_startdate,
						'balance' => $request->txt_salary,
						'processFlg' => 1,
						'delflg'=> 0,
						'createdBy' => $name,
						'updatedBy' => $name,
						'ins_DateTime' => date('Y-m-d H:i:s'),
						'up_DateTime' => date('Y-m-d H:i:s'),
				]);
		return $insert;
	}
	public static function updateRec($request) {
		// print_r($_REQUEST);exit();
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$update=DB::table('inv_allbank_details')
						->where('id', $request->id)
						->update([
						'startDate' => $request->txt_startdate,
						'balance' => $request->txt_salary,
						'delflg'=> 0,
						'updatedBy' => $name,
						'up_DateTime' => date('Y-m-d H:i:s'),
				]);
		return $update;
	}
	public static function fnGetAccountPeriodBK($request) {
		$query=DB::table('dev_kessandetails')
						->SELECT('*')
						->where('delflg','=','0')
						->get();
		return $query;
	}
	public static function balance($balbankid,$startdate) {
		$query=DB::table('inv_allbank_details')
						->SELECT('balance')
						->where('bankID','=',$balbankid)
						->where('startDate','=',$startdate)
						->where('processFlg','=','1')
						->where('delFlg','=','0')
						->get();
		return $query;
	}
	public static function mainsub($mainid) {
		$query=DB::table('dev_expensesetting')
						->SELECT('Subject AS main_eng','Subject_jp AS main_jap')
						->where('id','=',$mainid)
						->get();
		return $query;
	}
	public static function susub($subid) {
		$query=DB::table('inv_set_expensesub')
						->SELECT('sub_eng','sub_jap')
						->where('id','=',$subid)
						->get();
		return $query;
	}
}