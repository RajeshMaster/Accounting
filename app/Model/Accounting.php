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
						->where('mstbank.delflg','=','0')
						->orderBy('mstbank.Bank_NickName','ASC')
						->lists('BANKNAME','ID');
		return $query;
	}

	public static function fetchcontent() {
		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_contentsetting')
						->SELECT('id','Subject','Subject_jp')
						->where('delflg','=','0')
						->orderBy('Subject','ASC');
		if (Session::get('languageval') == "jp") {
			$query = $query->lists('Subject_jp','id');
		} else {
			$query = $query->lists('Subject','id');
		}
		return $query;
	}

	public static function fetchEditbanknames($request,$bankIdTo=null) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->orwhere('mstbank.delflg','=','0')
						->orwhere('mstbank.id','=',$request->bank_Id);
		if ($bankIdTo != null) {
			$query = $query->orwhere('mstbank.id','=',$bankIdTo);
		}
			$query = $query->orderBy('mstbank.Bank_NickName','ASC')
							->lists('BANKNAME','ID');
							// ->toSql();dd($query);
		return $query;
	}

	public static function fetchEditcontent($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_contentsetting')
						->SELECT('id','Subject','Subject_jp')
						->orwhere('delflg','=','0')
						->orwhere('id','=',$request->content_Id)
						->orderBy('Subject','ASC');
		if (Session::get('languageval') == "jp") {
			$query = $query->lists('Subject_jp','id');
		} else {
			$query = $query->lists('Subject','id');
		}
						// ->toSql();dd($query);
		return $query;
	}

	public static function fnGetbankName($accNo) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('mstbank')
						->SELECT(DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->WHERE('AccNo', '!=', $accNo)
						->where('mstbank.delflg','=','0')
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
		// print_r($request->all());exit;

		$statement = DB::select("show table status like 'acc_cashregister'");
		if (isset($statement[0]->Auto_increment)) {
			$orderId = $statement[0]->Auto_increment;
		} else {
			$orderId = 1;
		}
		$subject = "";
		if ($request->transtype == 1) {
			$subject = "Debit";
		} elseif ($request->transtype == 2) {
			$subject = "Credit";
		} elseif ($request->transtype == 4) {
			$subject = "Income";
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
							'loanName' => $subject,
							'remarks' => $request->remarks,
							'pageFlg' => 1,
							'createdBy' => $name,
							'orderId' => $orderId,
						]);
		return $insert;
	}

	public static function updCashDtls($request) {
		$subject = "";
		if ($request->transtype == 1) {
			$subject = "Debit";
		} elseif ($request->transtype == 2) {
			$subject = "Credit";
		} elseif ($request->transtype == 4) {
			$subject = "Income";
		}
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
								'loanName' => $subject,
								'transferId' => '',
								'UpdatedBy' => $name
							)
						);
		return $update;
	}


	public static function insCashreduction($request, $type, $maxID) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$subject = "Transfer";
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
							'loanName' => $subject,
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
						->orderBy('id','ASC');
		if (Session::get('languageval') == "jp") {
			$query = $query->lists('Subject_jp','id');
		} else {
			$query = $query->lists('Subject','id');
		}
						
		return $query;
		
	}

	public static function subjectName($subName,$flg) {

		$db = DB::connection('mysql');
		$query = $db->table('dev_expensesetting')
						->SELECT('id','Subject','Subject_jp');
		if ($flg == 1) {
			$query = $query->where('Subject','=',$subName)
							->orderBy('id','ASC')
							->lists('Subject','id');
		} else {
			$query = $query->where('Subject_jp','=',$subName)
							->orderBy('id','ASC')
							->lists('Subject_jp','id');
		}
						
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

				$statement = DB::select("show table status like 'acc_cashregister'");
				if (isset($statement[0]->Auto_increment)) {
					$orderId = $statement[0]->Auto_increment;
				} else {
					$orderId = 1;
				}

				$empArr = explode(":", $value);
				$bankacc = explode('-', $request->salaryBank);
				$insert = $db->table('acc_cashregister')
							->insert([
									'emp_ID' => $empArr['1'], 
									'date' => $request->accDate,
									'transcationType' => 1,
									'bankIdFrom' => $bankacc['0'],
									'accountNumberFrom' => $bankacc['1'],
									'amount' => preg_replace("/,/", "", $empArr['2']),
									'fee' => preg_replace("/,/", "", $empArr['3']),
									'content' => "Salary",
									'subjectId' => $request->salarySub,
									// 'remarks' => $request->transFerRemarks,
									// 'fileDtl' => $fileName,
									'orderId' => $orderId,
									'createdBy' => $name,
									'pageFlg' => 2,
								]);
			}

		} else {

				$statement = DB::select("show table status like 'acc_cashregister'");

				if (isset($statement[0]->Auto_increment)) {
					$orderId = $statement[0]->Auto_increment;
				} else {
					$orderId = 1;
				}

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
									'orderId' => $orderId,
									'createdBy' => $name,
									'pageFlg' => 2,
								]);
		}

		return $insert;
	}

	public static function tranferEditData($request) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('acc_cashregister.id','=',$request->editId)
						->orderBy('bankIdFrom','ASC')
						->orderBy('acc_cashregister.orderId','ASC')
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

				$statement = DB::select("show table status like 'acc_cashregister'");
				
				if (isset($statement[0]->Auto_increment)) {
					$orderId = $statement[0]->Auto_increment;
				} else {
					$orderId = 1;
				}

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
									'content' => "Loan",
									'subjectId' => $request->loanSub,
									'remarks' => $loanArr['5'],
									// 'fileDtl' => $fileName,
									'orderId' => $orderId,
									'createdBy' => $name,
									'pageFlg' => 3,
								]);
			}

		} else {

			$statement = DB::select("show table status like 'acc_cashregister'");
				
			if (isset($statement[0]->Auto_increment)) {
				$orderId = $statement[0]->Auto_increment;
			} else {
				$orderId = 1;
			}
				
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
								'orderId' => $orderId,
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
								'UpdatedBy' => $name
							)
						);
		return $update;

	}

	public static function fnGetEmpDetails($request,$empIdArr) {

		$db = DB::connection('mysql_MB');
		$date = explode("-", $request->transferDate);
		if (isset($date[0]) && isset($date[1])) {
			$date = $date[0]."-".$date[1];
		}
		$query = $db->table('emp_mstemployees')
						->select('Emp_ID','FirstName','LastName','KanaFirstName','KanaLastName','resigndate',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
						->WHERE('delFlg', '=', 0)
						->WHERE('Emp_ID', 'NOT LIKE', '%NST%')
						->WHERENOTIN('Emp_ID', $empIdArr);
		$query = $query->WHERE(function($joincont) use ($date) {
				$joincont->WHERE('resign_id', '=', 0)
						->ORWHERE(DB::raw("SUBSTRING(resigndate, 1, 7)"), '>=', $date);
		});
		$query = $query->orderBy('Emp_ID', 'ASC')
						->get();
		return $query;
	}

	public static function fnGetNonstaffEmpDetails($request,$empIdArr) {

		$db = DB::connection('mysql_MB');
		$query = $db->table('emp_mstemployees')
					->select('Emp_ID','FirstName','LastName','KanaFirstName','KanaLastName',DB::RAW("CONCAT(FirstName,' ', LastName) AS Empname"),DB::RAW("CONCAT(KanaFirstName,'　', KanaLastName) AS Kananame"))
					->WHERE('delFlg', '=', 0)
					->WHERE('resign_id', '=', 0)
					->WHERE('Emp_ID', 'LIKE', '%NST%')
					->whereNotIn('Emp_ID', $empIdArr)
					->orderBy('Emp_ID', 'ASC')
					->get();
		return $query;
	}
	
	public static function fetchcashRegister($from_date,$to_date,$request,$flg) {

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
					->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp','acc_contentsetting.Subject as contentSub','acc_contentsetting.Subject_jp as contentSub_jp','banname.BankName AS banknm','brncname.id AS brnchid','brncname.BranchName AS brnchnm')
					->leftJoin('mstbank AS bank', function($join)
						{
							$join->on('acc_cashregister.bankIdFrom', '=', 'bank.BankName');
							$join->on('acc_cashregister.accountNumberFrom', '=', 'bank.AccNo');
						})
					->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bank.BankName')
					->leftJoin('mstbankbranch AS brncname', function($join)
						{
							$join->on('brncname.BankId', '=', 'bank.BankName');
							$join->on('brncname.id', '=', 'bank.BranchName');
						})
					->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
					->leftJoin('acc_contentsetting', 'acc_contentsetting.id', '=', 'acc_cashregister.content')
					->where('transcationType','!=',9);
			if ($request->searchmethod == 3 && $flg == 0) {
				if (!empty($request->empId)) {
					$query = $query->where('acc_cashregister.emp_ID', '=', $request->empId);
				}
				if (!empty($request->loanId)) {
					$query = $query->where('acc_cashregister.loan_ID', '=', $request->loanId);
				}
				if (!empty($request->contentId)) {
					$query = $query->where('acc_cashregister.content', '=', $request->contentId);
				}
			} else {
				$query = $query->where('date', '>=', $from_date)
								->where('date', '<=', $to_date);
			}
			$query = $query->orderBy('acc_cashregister.bankIdFrom','ASC')
							->orderBy('acc_cashregister.accountNumberFrom','ASC')
							->orderBy('bank.Bank_NickName','ASC')
							->orderBy('acc_cashregister.orderId','ASC');
			if ($flg == 0) {
				$query = $query->paginate($request->plimit);
			} else {
				$query = $query->get();
			}
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
					->SELECT('loan.loanId','loan.loanName','loan.loanTerm','loan.paymentCount','loanEMI.monthPrinciple as loanAmount','loanEMI.monthInterest','bank.bankName','bank.id')
					->leftJoin('ams_loan_emidetails as loanEMI','loan.loanId','=','loanEMI.loanId')
					->leftJoin('ams_bankname_master as bank','loan.bank','=','bank.id')
					->where('loan.activeFlg','=',0)
					->where('loan.delFlg','=',0)
					->whereNotIn('loan.loanId', $loanIdArr);
		if ($request->userId != "") {
			$query = $query->where('loan.userId','=', $request->userId);
		}
		if ($request->belongsTo != "" && $request->userId != "") {
			$query = $query->where('loan.belongsTo','=', $request->belongsTo);
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

	public static function fetchEditDataTo($request) {
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
						->SELECT('acc_cashregister.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp')
						->leftJoin('mstbank AS bank', function($join)
							{
								$join->on('acc_cashregister.bankIdTo', '=', 'bank.BankName');
								$join->on('acc_cashregister.accountNumberTo', '=', 'bank.AccNo');
							})
						->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_cashregister.subjectId')
						->where('transcationType','!=',9)
						->where('acc_cashregister.id','=',$request->editId)
						->orderBy('bankIdTo','ASC')
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

	public static function getBelongsToDtls(){

		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_family_master')
					->select('id','familyName','nickName')
					->lists('familyName','id');
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
					->where('bank.delflg','=','0')
					->where('cashreg.emp_ID','=', $request->userId)
					->where('cashreg.loan_ID','=', $loanId)
					->where('cashreg.pageFlg','=', 3)
					->get();

		return $query;

	}

	public static function getsalaryPaid($date) {
		$date = substr($date, 0, 7);
		$query = DB::table('acc_cashregister')
						->select('date','emp_ID')
						->WHERERAW("SUBSTRING(acc_cashregister.date,1,7) = '$date'")
						->where('emp_ID','!=','')
						->where('pageFlg','=',2)
						->whereNotNull('emp_ID')
						->get();
		return $query;
	}

	public static function getLoanPaid($request,$flg) {
		if ($flg == 2) {
			$date = substr($request->invoiceDate, 0, 7);
		} else {
			$date = substr($request->autoDebitDate, 0, 7);
		}
		
		$query = DB::table('acc_cashregister')
						->select('date','loan_ID')
						->WHERERAW("SUBSTRING(acc_cashregister.date,1,7) = '$date'")
						->where('loan_ID','!=','')
						->whereNotNull('loan_ID');
		if ($flg == 2) {
			$query = $query->where('pageFlg','=', 4)
						->WHERE('loan_ID', 'LIKE', '%INV%');

		} else {
			$query = $query->where('pageFlg','=', 3)
						->WHERE('loan_ID', 'LIKE', '%LOAN%');

		}			
			$query = $query->get();
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

    public static function fetchinvoicePopup($request,$invoiceArr) {
		$db = DB::connection('mysql');
		// $invoiceArr = array('INSU0001','Insuv0002');
		// print_r($invoiceArr);exit();
		$date_month = substr($request->invoiceDate, 0, 7);


		$Estimate = $db->table('dev_payment_registration')
						->SELECT('dev_payment_registration.*','dev_invoices_registration.user_id')
						->leftJoin('dev_invoices_registration', 'dev_invoices_registration.id', '=', 'dev_payment_registration.invoice_id')
						->where('dev_payment_registration.payment_date','LIKE','%'.$date_month.'%');
		$Estimate = $Estimate->whereNotIn('dev_invoices_registration.user_id', $invoiceArr);
		$Estimate = $Estimate->orderByRaw("id ASC")
						->orderBy('dev_payment_registration.id','ASC')
						->get();



		// $Estimate = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id,main.trading_destination_selection,dev_payment_registration.payment_date,main.del_flg,main.copyFlg,main.project_name,main.classification,
		// main.created_by,main.pdf_flg,main.project_type_selection,main.mailFlg, 
		// main.paid_date,main.paid_status,main.tax,main.estimate_id,main.company_name,main.bankid,main.bankbranchid,main.acc_no,mstbank.Bank_NickName,works.amount,
		// works.work_specific,works.quantity,works.unit_price,works.remarks,works.emp_id,(CASE 
		//         WHEN main.classification = 2 THEN 3
		//         ELSE 0
		//     END) AS orderbysent,`dev_estimatesetting`.`ProjectType`,main.totalval 
		// FROM   tbl_work_amount_details works 
		// left join dev_invoices_registration main on works .invoice_id = main .user_id 
		// left join dev_estimatesetting on dev_estimatesetting.id = main.project_type_selection
		// left join dev_payment_registration on dev_payment_registration.invoice_id = main.id
		// left join mstbank on mstbank.AccNo = main.acc_no
		// WHERE main.del_flg = 0 AND main.paid_status = 1 AND SUBSTRING(main.quot_date,1,7) LIKE '%$date_month%'
		// GROUP BY user_id Order By user_id Asc,quot_date Asc) AS DDD "));
		// 		// ACCESS RIGHTS
		// 		// CONTRACT EMPLOYEE
		// 		// if (Auth::user()->userclassification == 1) {
		// 		// 	$accessDate = Auth::user()->accessDate;
		// 		// 	$Estimate = $Estimate->WHERE(function($joincont) use($accessDate) {
  //   //                        $joincont->WHERE('dev_invoices_registration.quot_date', '>', 
  //   //                        						$accessDate)
  //   //                         		->ORWHERE('accessFlg','=',1);
  //   //                         });
		// 		// }
		// 		// END ACCESS RIGHTS
		// 	$Estimate = $Estimate->whereNotIn('user_id', $invoiceArr);
		// 	$Estimate = $Estimate->orderByRaw("id ASC")
		// 			  			->get();
		// 		// ->toSql();dd($Estimate);
			return $Estimate;
	}


	public static function fnGetBalanceDetails($invid) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT invoice_id,id,totalval,paid_id,
						(SELECT SUM(replace(deposit_amount, ',', '')) 
						FROM dev_payment_registration WHERE invoice_id = $invid) 
						as deposit_amount FROM dev_payment_registration 
						WHERE invoice_id = $invid ORDER BY id DESC) as tb1"))
					->get();
		return $query;
	}

	public static function insInvoiceDtls($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$invId = "";
		$PaidInvId = "";
		$insert = 1;

		$db = DB::connection('mysql');

		if ($request->hidInvid != "") {

			$invId = explode(";", $request->hidInvid);

			foreach ($invId as $key => $value) {

				$statement = DB::select("show table status like 'acc_cashregister'");
				
				if (isset($statement[0]->Auto_increment)) {
					$orderId = $statement[0]->Auto_increment;
				} else {
					$orderId = 1;
				}

				$invArr = explode(":", $value);
				$bankAcc = explode("-", $invArr['3']);
				if (isset($invArr['4'])) {
					$hidInvArr = explode(",", $invArr['4']);
					foreach ($hidInvArr as $hidInvkey => $hidInvvalue) {
						$invoiceId = $db->table('dev_invoices_registration')
									->SELECT('dev_invoices_registration.*')
									->where('id','=',$hidInvvalue)
									->get();
						$PaidInvId = $PaidInvId.$invoiceId[0]->user_id.",";
					}
					$PaidInvId = rtrim($PaidInvId,",");
				}
				$insert = $db->table('acc_cashregister')
							->insert([
									'loan_ID' => $invArr['1'], 
									'loanName' => $invArr['0'], 
									'date' => $request->accDate,
									'transcationType' => 2,
									'bankIdFrom' => $bankAcc['0'],
									'accountNumberFrom' => $bankAcc['1'],
									'amount' => preg_replace("/,/", "", $invArr['2']),
									'content' => "Invoice",
									'subjectId' => $request->invSub,
									'remarks' => $PaidInvId,
									'orderId' => $orderId,
									'createdBy' => $name,
									'pageFlg' => 4,
								]);
			}
		} 

		return $insert;
	}


	public static function AccBalance($bankId,$accNo,$startDate,$prevDate) {
		$curDate = date('Y-m-d');
		$db = DB::connection('mysql');
			$query = $db->table('acc_cashregister')
						->SELECT('transcationType','amount','fee')
						->where('bankIdFrom','=',$bankId)
						->where('accountNumberFrom','=',$accNo)
						->where('transcationType','!=',9)
						->where('delFlg','=','0');
		
			$query = $query->WHERERAW("date >= '$startDate'");
			$query = $query->WHERERAW("date <= '$curDate'");
		if ($prevDate != "") {
			$query = $query->WHERERAW("SUBSTRING(date,1,7) <= '$prevDate'");
		}			
			$query = $query->get();

		return $query;
	}

	public static function fnGetRecordPreviousForAmountCheck($from_date) {
		$tbl_name = "acc_cashregister";
		$conditionAppend = "AND (transcationType != 9)";
		
		$sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM $tbl_name 
			WHERE (date < '$from_date' $conditionAppend) ORDER BY date ASC";
		$cards = DB::select($sql);
		return $cards;
	}

	public static function completedflg($request){

		if ($request->completedFlg == 0) {
			$completedFlg = 1;
		} else { 
			$completedFlg = 0;
		}
		$date = $request->selYear."-".$request->selMonth;

		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
					->where('bankIdFrom','=',$request->bankNo)
					->where('accountNumberFrom','=',$request->accNo);

		if ($completedFlg == 1) {
			$query = $query->WHERE(DB::raw("SUBSTRING(date, 1, 7)"), '<=' , $date);
		} else {
			$query = $query->WHERE(DB::raw("SUBSTRING(date, 1, 7)"), '=', $date);
		}
		// $query = $query->toSql();dd($query);
		$query = $query->update(['completedFlg' => $completedFlg]);

		return $query;
	}

	public static function fnGetdateBankExists($request) {
		$date = explode("-", $request->accDate);
		$date = $date[0]."-".$date[1];
		$db = DB::connection('mysql');
		$query = $db->table('acc_cashregister')
					->SELECT('*')
					->where('bankIdFrom','=',$request->bankIdFrom)
					->where('accountNumberFrom','=',$request->accountNumberFrom)
					->where('completedFlg','=',1);
		$query = $query->WHERE(DB::raw("SUBSTRING(date, 1, 7)"), '>=', $date);
		$query = $query->orderBy(DB::raw("SUBSTRING(date, 1, 7)"), "DESC")
						->get();	
		// $query = $query->toSql();dd($query);

		return $query;

	}

	public static function fetchExpensesBank($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE('acc_expensesData')
						->SELECT('acc_expensesData.*',DB::RAW("CONCAT(mstbank.Bank_NickName,'-',mstbank.AccNo) AS BANKNAME"),DB::RAW("CONCAT(mstbank.BankName,'-',mstbank.AccNo) AS ID"),'mstbank.id')
						->leftJoin('mstbank', function($join)
						{
							$join->on('acc_expensesData.bankIdFrom', '=', 'mstbank.BankName');
							$join->on('acc_expensesData.accountNumberFrom', '=', 'mstbank.AccNo');
						})
						->where('acc_expensesData.delFlg','=',0)
						->where('mstbank.delflg','=','0')
						->orderBy('mstbank.Bank_NickName','ASC')
						->lists('BANKNAME','ID');
		return $query;
	}

	public static function fetchExpensesData($request) {

		$db = DB::connection('mysql');
		$bankIdAccNo = explode("-", $request->bankIdAccNo);
		if (!isset($bankIdAccNo['0'])) {
			$bankIdAccNo['0'] = "";
		}
		if (!isset($bankIdAccNo['1'])) {
			$bankIdAccNo['1'] = "";
		}
		
		$query = $db->table('acc_expensesData')
					->SELECT('acc_expensesData.*','bank.id As bankId','bank.AccNo','bank.FirstName','bank.LastName','bank.BankName','bank.Bank_NickName','dev_expensesetting.Subject','dev_expensesetting.Subject_jp','banname.BankName AS banknm','brncname.id AS brnchid','brncname.BranchName AS brnchnm','acc_contentsetting.Subject as contentSub','acc_contentsetting.Subject_jp as contentSub_jp')
					->leftJoin('mstbank AS bank', function($join)
						{
							$join->on('acc_expensesData.bankIdFrom', '=', 'bank.BankName');
							$join->on('acc_expensesData.accountNumberFrom', '=', 'bank.AccNo');
						})
					->leftJoin('mstbanks AS banname', 'banname.id', '=', 'bank.BankName')
					->leftJoin('mstbankbranch AS brncname', function($join)
						{
							$join->on('brncname.BankId', '=', 'bank.BankName');
							$join->on('brncname.id', '=', 'bank.BranchName');
						})
					->leftJoin('dev_expensesetting', 'dev_expensesetting.id', '=', 'acc_expensesData.subjectId')
					->leftJoin('acc_contentsetting', 'acc_contentsetting.id', '=', 'acc_expensesData.content')
					->where('acc_expensesData.delFlg','=',0)
					->where('acc_expensesData.bankIdFrom','=',$bankIdAccNo['0'])
					->where('acc_expensesData.accountNumberFrom','=',$bankIdAccNo['1'])
					->orderBy('acc_expensesData.orderId','ASC')
					->get();
		
		return $query;
	}

	public static function insexpensesData($request) {

		$name = Session::get('FirstName').' '.Session::get('LastName');
		$empId = "";
		$insert = 1;

		$db = DB::connection('mysql');

		if ($request->hidempid != "" && $request->bankIdAccNo != "") {

			$empId = explode(";", $request->hidempid);

			foreach ($empId as $key => $value) {

				$statement = DB::select("show table status like 'acc_cashregister'");
				
				if (isset($statement[0]->Auto_increment)) {
					$orderId = $statement[0]->Auto_increment;
				} else {
					$orderId = 1;
				}

				$empArr = explode(":", $value);
				$bankAcc = explode("-", $request->bankIdAccNo);
				
				$insert = $db->table('acc_cashregister')
							->insert([
									'emp_ID' => $empArr['0'], 
									'date' => $request->accDate,
									'transcationType' => 1,
									'bankIdFrom' => $bankAcc['0'],
									'accountNumberFrom' => $bankAcc['1'],
									'amount' => preg_replace("/,/", "", $empArr['2']),
									'fee' => preg_replace("/,/", "", $empArr['3']),
									'content' => $empArr['4'],
									'subjectId' => $empArr['1'],
									'orderId' => $orderId,
									'createdBy' => $name,
									'pageFlg' => 5,
								]);
			}
		} 

		return $insert;
	}

	public static function fnGetEMIData($loanId,$date="",$type="",$yrMnth=""){
		$db = DB::connection('mysql_Salary');
		$query = $db->table('ams_loan_emidetails')
					->select("*")
					->where('loanId', '=', $loanId);

			if ($date != "" && $type != "") {
				if ($type == "next") {
					$query = $query ->where('emiDate', '>=', $date);
				} else if ($type == "prev") {
					$query = $query ->where('emiDate', '<=', $date);
				}
			} elseif ($yrMnth != "") {
				$query = $query ->where('emiDate', 'LIKE', $yrMnth."%");
			}

			$query = $query->get();
		return $query;
	}
}
