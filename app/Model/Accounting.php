<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon;
class Accounting extends Model {

	public static function fetchbanknames() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->orderBy('mstbank.id','ASC')
						->lists('BANKNAME','ID');
						// ->toSql();
		return $query;
	}

	public static function fnGetbankName($accNo) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						// ->leftJoin('mstbanks', 'mstbanks.id', '=', 'mstbank.BankName')
						->WHERE('AccNo', '!=', $accNo)
						->orderBy('mstbank.id','ASC')
						->get();
						// ->toSql();
		return $query;
	}

	public static function getautoincrement() {

		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$statement = $statement[0]->Auto_increment;
		} else {
			$statement = 1;
		}
		return $statement;

	}

	public static function insCashDtls($request) {


		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$orderId = $statement[0]->Auto_increment;
		} else {
			$orderId = 1;
		}

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->bank);
		$db = DB::connection('mysql');

		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => "",
							'date' => $request->accDate,
							'transcationType' => $request->transtype,
							'bankIdFrom' => $bankacc[0],
							'accountNumberFrom' => $bankacc[1],
							'bankIdTo' => $request->transfer,
							'amount' => preg_replace("/,/", "", $request->amount),
							'fee' => preg_replace("/,/", "", $request->fee),
							'content' => $request->content,
							'remarks' => $request->remarks,
							'pageFlg' => 1,
							'createdBy' => $name,
							'orderId' => $orderId,
						]);
		return $insert;
	}

	public static function updCashDtls($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->bank);
		$update=DB::table('acc_cashregister')
						->where('id', $request->editId)
						->update(
							array(
								'emp_ID'	=> '', 
								'date' => $request->accDate, 
								'transcationType' => $request->transtype,
								'bankIdFrom' => $bankacc[0],
								'accountNumberFrom' => $bankacc[1],
								'bankIdTo' => $request->transfer,
								'amount' => preg_replace("/,/", "", $request->amount),
								'fee' => preg_replace("/,/", "", $request->fee),
								'content' => $request->content,
								'remarks' => $request->remarks,
								'pageFlg' => 1,
								'transferId' => '',
								'UpdatedBy' => $name
							)
						);
		return $update;
	}


	public static function insCashreduction($request, $type, $maxID) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		if($type == 1){
			$bankacc = explode('-', $request->bank);
			$transfer = explode('-', $request->transfer);
			$maxID = $maxID + 1 ;
		} else {
			$bankacc = explode('-', $request->transfer);
			$transfer[0] = "";
			$transfer[1] = "";
			$request->fee = "";
			if($request->edit_flg) {
				$maxID = $maxID;
			} else {
				$maxID = $maxID + 1;
			}
		}

		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$orderId = $statement[0]->Auto_increment;
		} else {
			$orderId = 1;
		}


		$db = DB::connection('mysql');
		$insert = $db->table('acc_cashregister')
					->insert([
							'emp_ID' => "",
							'date' => $request->accDate,
							'transcationType' => $type,
							'bankIdFrom' => $bankacc[0],
							'accountNumberFrom' => $bankacc[1],
							'bankIdTo' => $transfer[0],
							'accountNumberTo' => $transfer[1],
							'amount' => preg_replace("/,/", "", $request->amount),
							'fee' => preg_replace("/,/", "", $request->fee),
							'content' => $request->content,
							'remarks' => $request->remarks,
							'transferId' => $maxID,
							'pageFlg' => 1,
							'orderId' => $orderId,
							'createdBy' => $name,
						]);
		$id = DB::getPdo()->lastInsertId();
		return $id;
	}

	public static function updCashreduction($request, $type, $maxID) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		if($type == 1){
			$bankacc = explode('-', $request->bank);
			$transfer = explode('-', $request->transfer);
			$maxID = $maxID;
		} else {
			$bankacc = explode('-', $request->transfer);
			$transfer[0] = "";
			$transfer[1] = "";
			$request->fee = "";
			$request->editId = $maxID;
			$maxID = $maxID;
		}
		$update=DB::table('acc_cashregister')
						->where('id', $request->editId)
						->update(
							array(
								'emp_ID'	=> '', 
								'date' => $request->accDate, 
								'transcationType' => $type,
								'bankIdFrom' => $bankacc[0],
								'accountNumberFrom' => $bankacc[1],
								'bankIdTo' => $transfer[0],
								'accountNumberTo' => $transfer[1],
								'amount' => preg_replace("/,/", "", $request->amount),
								'fee' => preg_replace("/,/", "", $request->fee),
								'content' => $request->content,
								'remarks' => $request->remarks,
								'pageFlg' => 1,
								'transferId' => $maxID,
								'UpdatedBy' => $name
							)
						);
		return $update;
	}

	public static function DelCashDtls($request) {
		$query = DB::table('acc_cashregister')
  						->WHERE('id', '=', $request->oldTransferId)
  						->DELETE();
		return $query;
  	}

	public static function getMainExpName() {

		$db = DB::connection('mysql');
		$query = $db->TABLE('dev_expensesetting')
						->SELECT('id','Subject','Subject_jp')
						->orderBy('id','ASC')
						->lists('Subject','id');
		return $query;
		
	}

	public static function insTransferDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$empId = "";
		

		$db = DB::connection('mysql');

		if($request->empid == "") {
			$request->empid = $request->empID;
		}
		if($request->txt_empname == "") {
			$request->empid = "";
		}

		if ($request->hidempid != "") {
			
			$empId = explode(";", $request->hidempid);

			foreach ($empId as $key => $value) {
				$empArr = explode(":", $value);
				$bankacc = explode('-', $empArr['4']);
				$insert = $db->table('acc_cashregister')
							->insert([
									'emp_ID' => $empArr['1'], 
									'date' => $request->accDate,
									'transcationType' => 1,
									'bankIdFrom' => $bankacc['0'],
									'accountNumberFrom' => $bankacc['1'],
									'amount' => preg_replace("/,/", "", $empArr['2']),
									'fee' => preg_replace("/,/", "", $empArr['3']),
									// 'content' => $request->transferContent,
									// 'subjectId' => $request->transferMainExp,
									// 'remarks' => $request->transFerRemarks,
									// 'fileDtl' => $fileName,
									'createdBy' => $name,
									'pageFlg' => 2,
								]);
			}

		} else {

				$bankacc = explode('-', $request->transferBank);

				$insert = $db->table('acc_cashregister')
							->insert([
									'emp_ID' => $request->empid, 
									'date' => $request->accDate,
									'transcationType' => 1,
									'bankIdFrom' => $bankacc['0'],
									'accountNumberFrom' => $bankacc['1'],
									'amount' => preg_replace("/,/", "", $request->transferAmount),
									'fee' => preg_replace("/,/", "", $request->transferFee),
									'content' => $request->transferContent,
									'subjectId' => $request->transferMainExp,
									'remarks' => $request->transFerRemarks,
									'fileDtl' => $fileName,
									'createdBy' => $name,
									'pageFlg' => 2,
								]);
		}

		return $insert;
	}

	public static function tranferEditData($request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp',DB::RAW("CONCAT(emp_mstemployees.FirstName,' ', emp_mstemployees.LastName) AS Empname"))
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->leftJoin('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'acc_cashregister.Emp_ID')
						->where('acc_cashregister.id','=',$request->editId)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.date','ASC')
						->get();
						// ->toSql();
						// dd($query);
		return $query;

	}

	public static function updateTransferDtls($request,$fileName) {

		if($request->empid == "") {
			$request->empid = $request->empID;
		}

		if($request->txt_empname == "") {
			$request->empid = "";
		}

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->transferBank);

		$update = DB::table('acc_cashregister')
						->where('id', $request->editId)
						->update(
							array(
								'emp_ID'	=> $request->empid, 
								'date' => $request->accDate, 
								'subjectId' => $request->transferMainExp,
								'bankIdFrom' => $bankacc[0],
								'accountNumberFrom' => $bankacc[1],
								'bankIdTo' => $request->transfer,
								'amount' => preg_replace("/,/", "", $request->transferAmount),
								'fee' => preg_replace("/,/", "", $request->transferFee),
								'content' => $request->transferContent,
								'remarks' => $request->transFerRemarks,
								'fileDtl' => $fileName,
								'pageFlg' => 2,
								'UpdatedBy' => $name
							)
						);
		return $update;

	}

	public static function insAutoDebitDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$loanId = "";
		

		$db = DB::connection('mysql');

		if ($request->hidloan != "") {

			$loanId = explode(";", $request->hidloan);
			foreach ($loanId as $key => $value) {
				$loanArr = explode(":", $value);
				$bankacc = explode('-', $loanArr['4']);
				$insert = $db->table('acc_cashregister')
							->insert([
									'emp_ID' => $request->assetsUser, 
									'loan_ID' => $loanArr['1'], 
									'loanName' => $loanArr['0'], 
									'date' => $request->accDate,
									'transcationType' => 1,
									'bankIdFrom' => $bankacc['0'],
									'accountNumberFrom' => $bankacc['1'],
									'amount' => preg_replace("/,/", "", $loanArr['2']),
									'fee' => preg_replace("/,/", "", $loanArr['3']),
									// 'content' => $request->autoDebitContent,
									// 'subjectId' => $request->autoDebitMainExp,
									// 'remarks' => $request->autoDebitRemarks,
									// 'fileDtl' => $fileName,
									'createdBy' => $name,
									'pageFlg' => 3,
								]);
			}

		} else {
			$bankacc = explode('-', $request->autoDebitBank);
			$insert = $db->table('acc_cashregister')
						->insert([
								'date' => $request->accDate,
								'transcationType' => 1,
								'bankIdFrom' => $bankacc['0'],
								'accountNumberFrom' => $bankacc['1'],
								'amount' => preg_replace("/,/", "", $request->autoDebitAmount),
								'fee' => preg_replace("/,/", "", $request->autoDebitFee),
								'content' => $request->autoDebitContent,
								'subjectId' => $request->autoDebitMainExp,
								'remarks' => $request->autoDebitRemarks,
								'fileDtl' => $fileName,
								'createdBy' => $name,
								'pageFlg' => 3,
							]);
		}

		return $insert;
	}

	public static function updateAutodebitDtls($request,$fileName) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$bankacc = explode('-', $request->autoDebitBank);

		$update = DB::table('acc_cashregister')
						->where('id', $request->editId)
						->update(
							array(
								'date' => $request->accDate, 
								'subjectId' => $request->autoDebitMainExp,
								'bankIdFrom' => $bankacc[0],
								'accountNumberFrom' => $bankacc[1],
								'amount' => preg_replace("/,/", "", $request->autoDebitAmount),
								'fee' => preg_replace("/,/", "", $request->autoDebitFee),
								'content' => $request->autoDebitContent,
								'remarks' => $request->autoDebitRemarks,
								'fileDtl' => $fileName,
								'pageFlg' => 3,
								'UpdatedBy' => $name
							)
						);
		return $update;

	}

	public static function fnGetEmpDetails($request) {

		$query = DB::table('emp_mstemployees')
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

		$query = DB::table('emp_mstemployees')
						->select('Emp_ID','FirstName','LastName','nickname','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
						->WHERE('delFlg', '=', 0)
						->WHERE('resign_id', '=', 0)
						->WHERE('Emp_ID', 'LIKE', '%NST%')
						->orderBy('Emp_ID', 'ASC')
						->get();
		return $query;
	}
	
	public static function fetchcashRegister($from_date, $to_date, $request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('transcationType','!=',9)
						->where('date','>=',$from_date)
						->where('date','<=',$to_date)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.orderId','ASC')
						->paginate($request->plimit);


						 // ->toSql();
						// dd($query);
		return $query;
	}

	public static function baseAmt($bankId ,$acc) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('amount','fee','transcationType','date')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$acc)
						->where('transcationType','=',9)
						->get();
		return $query;
	}

	public static function getLoanDtls($request,$loanIdArr) {

		$db = DB::connection('mysql_Salary');
		$MnthYear = explode("-", $request->autoDebitDate);
		$query = $db->table('ams_loan_details as loan')
					->SELECT('loan.loanId','loan.loanName','loan.loanAmount','loanEMI.monthInterest','bank.bankName','bank.id')
					->leftJoin('ams_loan_emidetails as loanEMI','loan.loanId','=','loanEMI.loanId')
					->leftJoin('ams_bankname_master as bank','loan.bank','=','bank.id')
					->where('loan.activeFlg','=',0)
					->where('loan.delFlg','=',0)
					->whereNotIn('loan.loanId', $loanIdArr);
		if ($request->userId != "") {
			$query = $query->where('loan.userId','=', $request->userId);
		}
		$query = $query->WHERE(DB::raw("SUBSTRING(loanEMI.emiDate, 1, 4)"),'=', $MnthYear[0]);
		$query = $query->WHERE(DB::raw("SUBSTRING(loanEMI.emiDate, 6, 2)"),'=', $MnthYear[1])
						->orderBy('loanEMI.belongsTo','ASC')
						->get();
		return $query;
	}

	public static function getSalaryDtls($request ,$empIdArr) {

		$db = DB::connection('mysql_Salary');
		$MnthYear = explode('-', date("Y-m", strtotime("-1 months", strtotime($request->transferDate))));
		$query = $db->table('inv_salaryplus_main')
					->SELECT('Emp_ID','Salary','Deduction','Travel','salamt')
					->whereNotIn('Emp_ID', $empIdArr)
					->where('delFlg','=',0);
		$query = $query->WHERE(DB::raw("SUBSTRING(year_mon, 1, 4)"),'=', $MnthYear[0]);
		$query = $query->WHERE(DB::raw("SUBSTRING(year_mon, 6, 2)"),'=', $MnthYear[1])
						->orderBy('Emp_ID','ASC')
						->get();
		return $query;
	}

	public static function subjectMaster($subId) {

		$db = DB::connection('mysql');
		$query = $db->table('dev_expensesetting')
						->SELECT('Subject','Subject_jp')
						->where('id','=',$subId)
						->get();
		return $query;
	}

	public static function getsalaryDetailsnodelflg($request,$flg) {
		
		$db = DB::connection('mysql_Salary');
		$query = $db->table('mstsalaryplus')
					->select('id','Name','nick_name','location','Salarayid')
					->where('location','=',$flg)
					->orderBy('Salarayid', 'ASC')
					->get();
		return $query;
	}

	public static function fetchEditData($request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('transcationType','!=',9)
						->where('acc_cashregister.id','=',$request->editId)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.orderId','ASC')
						->get();
						// ->toSql();
						// dd($query);
		return $query;
	}

	public static function fnGetEmpName($Emp_ID){

		$db = DB::connection('mysql_MB');
		$query = $db->table('emp_mstemployees')
					->select('FirstName','LastName','KanaFirstName','KanaLastName')
					->where('Emp_ID','=',$Emp_ID)
					->get();
		return $query;
	}

	public static function getUserDtls(){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_users')
					->select('userId','firstName','lastName')
					->lists('lastName','userId');
		return $query;
	}

	public static function fnGetAccountPeriodAcc() {
		$accperiod=DB::table('dev_kessandetails')
						->SELECT('*')
						->WHERE('delflg', '=', 0)
	                    ->get();
	        return $accperiod;
	}

	public static function fnGetCashExpenseAllRecord() {
		
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM acc_cashregister 
				where transcationType != '9'  ORDER BY date ASC";

		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecord($from_date, $to_date) {
	
		$tbl_name = "acc_cashregister";
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date 
				FROM $tbl_name 
				WHERE (date > '$from_date' AND date < '$to_date') 
				AND transcationType != '9'
				ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecordPrevious($from_date) {

		$tbl_name = "acc_cashregister";
		$conditionAppend = "AND (transcationType != 9)";
		
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM $tbl_name 
			WHERE (date <= '$from_date' $conditionAppend) ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function fnGetCashExpenseRecordNext($to_date) {
		$tbl_name = "acc_cashregister";
		
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM acc_cashregister 
			WHERE (date >= '$to_date') AND transcationType != '9' ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function getLoanBank($request,$loanId){

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister as cashreg')
					->SELECT(DB::RAW("CONCAT(bank.Bank_NickName,'-',bank.AccNo) AS BANKNAME"),
						DB::RAW("CONCAT(bank.BankName,'-',bank.AccNo) AS ID"),DB::RAW("SUBSTRING(cashreg.date,1,7) AS Date"))
					->leftJoin('mstbank AS bank', function($join) {
							$join->on('cashreg.bankIdFrom', '=', 'bank.BankName');
							$join->on('cashreg.accountNumberFrom', '=', 'bank.AccNo');
						})
					->where('cashreg.emp_ID','=', $request->userId)
					->where('cashreg.loan_ID','=', $loanId)
					->get();

		return $query;

	}

	public static function getsalaryPaid($date) {
		$date = substr($date, 0, 7);
		$query = DB::table('acc_cashregister')
						->select('date','emp_ID')
						->WHERERAW("SUBSTRING(acc_cashregister.date,1,7) = '$date'")
						->where('emp_ID','!=','')
						->whereNotNull('emp_ID')
						->get();
		return $query;
	}

	public static function getLoanPaid($request) {
		$date = substr($request->autoDebitDate, 0, 7);
		$query = DB::table('acc_cashregister')
						->select('date','loan_ID')
						->WHERERAW("SUBSTRING(acc_cashregister.date,1,7) = '$date'")
						->where('loan_ID','!=','')
						->whereNotNull('loan_ID')
						->get();
		return $query;
	}



	public static function fetchcashRegisterPopup($from_date, $to_date, $request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('transcationType','!=',9)
						->where('date','>=',$from_date)
						->where('date','<=',$to_date)
						->where('bankIdFrom','=',$request->bankId)
						->where('accountNumberFrom','=',$request->AccNo)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.orderId','ASC')
						->get();


						 // ->toSql();
						// dd($query);
		return $query;
	}

	/**  
    *  For Commit Process 
    *  @author Rajesh 
    *  @param $request,$getTableFields
    *  Created At 2020/09/21
    **/
    public static function fngetcommitProcess($request) {
        $tablename = 'acc_cashregister';
        $cmtfield = 'orderId';
        $splitactualid = explode(",", $request->actualId);
        $splitidnew = explode(",", $request->idnew);
        $db = DB::connection('mysql');
        for ($i = 0; $i < count($splitactualid); $i++) {
            $update = $db->table($tablename)
                        ->where('id', $splitidnew[$i])
                        ->update([ $cmtfield => $splitactualid[$i] ]);
        }
        return true;
    }

}
