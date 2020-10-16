<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Common;
use App\Model\Loan;
use DB;
use Input;
use Redirect;
use Config;
use Session;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller {
	function index(Request $request) { 
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$index = Loan::loanindex($request)->paginate($request->plimit);
		$disp = count($index);
		$i = 0;
		$loanindex =array();
		$remTot = "";
		foreach($index as $key=>$loan) {
			$loanindex[$i]['id'] = $loan->id;
			$loanindex[$i]['loanno'] = $loan->loanNo;
			$loanindex[$i]['bankid'] = $loan->BankName;
			$loanindex[$i]['AccNo'] = $loan->AccNo;
			$loanindex[$i]['BranchName'] = $loan->BranchName;
			$loanindex[$i]['amount'] = $loan->amount;
			$loanindex[$i]['payamount'] = number_format($loan->payamount);
			$loanindex[$i]['paycount'] = $loan->paycount;
			$loanindex[$i]['receiveddate'] = $loan->receivedDate;
			$loanindex[$i]['enddate'] = $loan->endDate;
			$loanindex[$i]['period'] = $loan->period;
			$loanindex[$i]['interest'] = $loan->interest;
			$loanindex[$i]['repaymentday'] = $loan->repaymentDate;
			$loanindex[$i]['currentbalance'] = $loan->currentBalance;
			$loanindex[$i]['remainingmonths'] = $loan->remainingMonths;
			$loanindex[$i]['pdffile'] = $loan->pdfFile;
			$loanindex[$i]['editflg'] = $loan->editFlg;
			$remTot += str_replace(",", "", $loan->currentBalance) - str_replace(",", "",$loan->payamount);
			$i++;
		}
		return view('Loandetails.index',['loanindex' => $loanindex,
										'remTot' => $remTot,
										'index' => $index,
										'disp' => $disp,
										'request' => $request]);
	}
	public function Singleview(Request $request) { 
		if(Session::get('id') !=""){
			$request->id = Session::get('id');
		}
		if(empty($request->id)){
			return $this->index($request);
		}
		$view = Loan::loansingleview($request);
		return view('Loandetails.Singleview',['view' => $view,
											'request' => $request]);
	}
	public function Loanconfirm(Request $request) {
		$view = Loan::loanconfirm($request);
		Session::flash('id', $request->id );
		if ($request->loan_confirm == "1") {
			Session::flash('success', 'Loan Confirmed Successfully!'); 
			Session::flash('type', 'alert-success');
		} else {
			Session::flash('success', 'Loan Discarded Due To Changes!'); 
			Session::flash('type', 'alert-success');
		}
		return Redirect::to('Loandetails/Singleview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	function Viewlist(Request $request) { 
		// PAGINATION
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$viewlist = Loan::loanview($request)->paginate($request->plimit);
		$amountval = Loan::loanamount($request);
		$i = 0;
		$loanview = array();
		foreach($viewlist as $key=>$loandetail) {
			if($i == 0 ) {
				$loanview[$i]['balance'] = $loandetail->currentBalance;
				$loanview[$i]['bankdate'] = "";
				$tempMon = $loandetail->remainingMonths;
				$loanview[$i]['monthsleft'] = "";
				$i++;
			} 
			$loanview[$i]['monthsleft'] = $tempMon;
			$tempMon = $loanview[$i]['monthsleft'] - 1;
			$loanview[$i]['id'] = $loandetail->id;
			$loanview[$i]['bankdate'] = $loandetail->paymentdate;
			$loanview[$i]['currentBalance'] = $loandetail->currentBalance;
			$loanview[$i]['BankName'] = "";
			$loanview[$i]['remainingmonths'] = $loandetail->remainingMonths;
			$loanview[$i]['amount'] = $loandetail->amount;
			$loanview[$i]['paymentamount'] = number_format($loandetail->paymentAmount);
			$loanview[$i]['interest'] = $loandetail->fee;
			$loanview[$i]['bankid'] = $loandetail->bankId;
			$loanview[$i]['remark_dtl'] = $loandetail->remark_dtl;
			if ($i == 1) {
				$loanview[$i]['balance'] = str_replace(',', '', $loandetail->currentBalance) - str_replace(',', '', $loandetail->paymentAmount);
				$tempBal = $loanview[$i]['balance'];
			} else {
				$loanview[$i]['balance'] = $tempBal - str_replace(',', '', $loandetail->paymentAmount);
				$tempBal = $loanview[$i]['balance'];
			}
			$i++;
		}
		if(!isset($request->id)){
			return $this->index($request);
		}
		return view('Loandetails.Viewlist',['viewlist' => $viewlist,
											'amountval' => $amountval,
											'loanview' => $loanview,
											'request' => $request]);
	}
	function addedit(Request $request) { 
		$bankname = Loan::fetchbankname($request);
		$loantype = Loan::fetchloantype($request);
		// print_r($bankname);exit();
		return view('Loandetails.addedit',['bankname' => $bankname,
											'loantype' => $loantype,
											// 'loanview' => $loanview,
											'request' => $request]);
	}
	public static function edit(Request $request) {
		if (!isset($request->id)) {
		return Redirect::to('Loandetails/indexs?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$bankname = Loan::fetchbankname($request);
		$loantype = Loan::fetchloantype($request);
		$detedit = Loan::viewedit($request);
		$fetchdetail = Loan::fetchdetails($request);
		return view('Loandetails.addedit',['detedit' => $detedit[0],
										'loantype'=> $loantype,
										'bankname'=> $bankname,
										'fetchdetail' => $fetchdetail,
										'request' => $request]);

	}
	function addeditprocess(Request $request) {
		if ($request->check == "on") {
			$request->check = "1";
		} else {
			$request->check = "0";
		}
		if ($request->reflectpass == "on") {
			$request->reflectpass = 0;
		} else {
			$request->reflectpass = 1;
		}
		if($request->editflg == "2") {
			$fileid="file1";
            if($request->$fileid != "") {
              $extension = Input::file($fileid)->getClientOriginalExtension();
              $filename=$request->id.'.'.$extension;
              $file = $request->$fileid;
              $destinationPath = 'resources/assets/uploadandtemplates/upload/Loandetails';
              if(!is_dir($destinationPath)) {
	            	mkdir($destinationPath, true);
	            }
	            chmod($destinationPath, 0777);
	            $file->move($destinationPath,$filename);
	            chmod($destinationPath."/".$filename, 0777);
            } else {
              $filename = $request->pdffiles; 
            }
			$update = Loan::UpdateloanRec($request,$filename);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $request->id); 
		} else {
			$autoincId=Loan::getautoincrement();
			$Loanno="LON".(str_pad($autoincId,'4','0',STR_PAD_LEFT));
			$fileid="file1";
			$filename="";
            if($request->$fileid != "") {
              $extension = Input::file($fileid)->getClientOriginalExtension();
              $filename=$Loanno.'.'.$extension;
              $file = $request->$fileid;
              $destinationPath = 'resources/assets/uploadandtemplates/upload/Loandetails';
              if(!is_dir($destinationPath)) {
	            	mkdir($destinationPath, true);
	            }
	            chmod($destinationPath, 0777);
	            $file->move($destinationPath,$filename);
	            chmod($destinationPath."/".$filename, 0777);
            }
			$insert = Loan::insertloanRec($request,$Loanno,$filename);
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('id', $Loanno); 
		}
		return Redirect::to('Loandetails/Singleview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
}