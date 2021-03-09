<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Bankdetail;
use App\Model\AccBankPassbookDtls;
use App\Http\Helpers;
use DB;
use Input;
use Redirect;
use Session;
use App\Http\Common;

class AccBankPassbookDtlsController extends Controller {

	/**
	*
	* Get  Process
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/01
	*
	*/

	public function index(Request $request) {
		if(Session::get('selYear') != "") {
			$request->selYear =  Session::get('selYear');
		}
		if($request->selYear == "") {
			$request->selYear =  date('Y');
		}
		if ($request->plimit == "") {
			$request->plimit = 50;
		}
		$bankPassbookindex = AccBankPassbookDtls::bankPassbookindex($request);
		$accBankPassbook = array();
		$i = 0;

		foreach ($bankPassbookindex as $key => $value) {
			$accBankPassbook[$i]['id'] = $value->id;
			$accBankPassbook[$i]['bankId'] = $value->bankId;
			$accBankPassbook[$i]['pageNoFrom'] = $value->pageNoFrom;
			$accBankPassbook[$i]['pageNoTo'] = $value->pageNoTo;
			$accBankPassbook[$i]['dateRangeFrom'] = $value->dateRangeFrom;
			$accBankPassbook[$i]['dateRangeTo'] = $value->dateRangeTo;
			$accBankPassbook[$i]['fileDtl'] = $value->fileDtl;
			$accBankPassbook[$i]['FirstName'] = $value->FirstName;
			$accBankPassbook[$i]['LastName'] = $value->LastName;
			$accBankPassbook[$i]['Bank_NickName'] = $value->Bank_NickName;
			$accBankPassbook[$i]['AccNo'] = $value->AccNo;
			$accBankPassbook[$i]['bnkid'] = $value->bnkid;
			$accBankPassbook[$i]['bnknm'] = $value->bnknm;
			$accBankPassbook[$i]['brnchid'] = $value->brnchid;
			$accBankPassbook[$i]['brnchnm'] = $value->brnchnm;
			$accBankPassbook[$i]['nxtFlg'] = $value->nxtFlg;
			$accBankPassbook[$i]['delFlg'] = $value->delFlg;
			$i++;
		}

		// year bar process
		$cur_year = date('Y');
		$curtime = date('YmdHis');
		$yearArr = AccBankPassbookDtls::getYears($request);
		$prev_yrs = array();
		$total_yrs = array();

		foreach ($yearArr as $value) {
			if (!in_array($value->yearFrom, $prev_yrs)) {
				$prev_yrs[] = $value->yearFrom;
			} 
			if (!in_array($value->yearFrom, $total_yrs)) {
				$total_yrs[] = $value->yearFrom;
			}
			if (!in_array($value->yearTo, $prev_yrs)) {
				array_push($prev_yrs,$value->yearTo);
			}
			if (!in_array($value->yearTo, $total_yrs)) {
				array_push($total_yrs,$value->yearTo);
			} 
		}

		if (!in_array($cur_year, $total_yrs)) {
		    array_push($total_yrs,$cur_year);
		}

		if (isset($request->selYear) && !empty($request->selYear)) {
			$selectedYear = $request->selYear;
			$cur_year = $selectedYear;
		} else {
			$selectedYear = $cur_year;
		}
		
		return view('AccBankPassbookDtls.index',[ 'request' => $request,
											'accBankPassbook' => $accBankPassbook,
											'bankPassbookindex' => $bankPassbookindex,
											'cur_year' =>  $cur_year,
											'curtime' =>  $curtime,
											'prev_yrs' =>  $prev_yrs,
											'total_yrs' =>  $total_yrs,
											'selectedYear' =>  $selectedYear
										]);
	}

	/**
	*
	* AddEdit Page for Passbook
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/01
	*
	*/
	public function addedit(Request $request) {

		if (!isset($request->edit_flg)) {
			return Redirect::to('AccBankPassbookDtls/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}

		$accBankPassbook = array();
		if ($request->edit_flg == "2") {
			$bankDetail = AccBankPassbookDtls::fetchEditbanknames($request);
		} else {
			$bankDetail = AccBankPassbookDtls::fetchbanknames();
		}

		if ($request->edit_id != "") {
			$accBankPassbook = AccBankPassbookDtls::accBankPassbook($request->edit_id);
		} if($request->edit_flg == "3" && isset($accBankPassbook[0])) {
			$accBankPassbook[0]->dateRangeFrom = "";
			$accBankPassbook[0]->dateRangeTo = "";
			$accBankPassbook[0]->fileDtl = "";
			$pageNoAdd = $accBankPassbook[0]->pageNoTo + 1;
			if (strlen($pageNoAdd) == 1) {
				$pageNoAdd = "0".$pageNoAdd;
			} 		
			$accBankPassbook[0]->pageNoTo = $pageNoAdd;
		}

		return view('AccBankPassbookDtls.addedit',['request' => $request,
												'bankDetail' => $bankDetail,
												'accBankPassbook' => $accBankPassbook,
												]);	
	}

	/**
	*
	* AddEdit Process for Passbook
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/01
	*
	*/
	public function addeditprocess(Request $request) {

		$autoincId = AccBankPassbookDtls::getautoincrement();
		$bankDetail = AccBankPassbookDtls::fetchbankdtls($request->bankId);
		if(isset($bankDetail[0])) {
			$AccBankPassbookNo = $bankDetail[0]->Bank_NickName."_".$request->pageNoFrom."_".$request->pageNoTo;
		} else {
			$AccBankPassbookNo = "AccBankPassbook"."_".$request->pageNoFrom."_".$request->pageNoTo;
		}	
		$fileName = "";
		$fileid = "bankPassbook";

		if($request->$fileid != "") {
			$extension = Input::file($fileid)->getClientOriginalExtension();
			$fileName = $AccBankPassbookNo.'.'.$extension;
			$file = $request->$fileid;
			$destinationPath = '../AccountingUpload/AccBankPassbook';
			if(!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777,true);
			}
			$file->move($destinationPath,$fileName);
		} else {
			$fileName = $request->pdffiles;
		}

		if($request->edit_flg != "2") {

			$insert = AccBankPassbookDtls::insertRec($request,$fileName,$autoincId);

			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('danger', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			
		} else {

			$update = AccBankPassbookDtls::updateRec($request,$fileName);

			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			} else {
				Session::flash('danger', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			
		}
		
		$date = explode("-", $request->dateRangeFrom);
		if (isset($date[0])) {
			Session::flash('selYear', $date[0]); 
		} 

		return Redirect::to('AccBankPassbookDtls/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}

	/**
	*
	* Date Already Exists Process for Passbook
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/02
	*
	*/
	public function DateExists(Request $request){

		$DateExists = AccBankPassbookDtls::getDateExists($request);
		if (count($DateExists) != 0) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}

	}

	/**
	*
	* Page No Exists Process for Passbook
	* @author Sastha
	* @return object to particular view page
	* Created At 2021/03/02
	*
	*/
	public function pageNoExists(Request $request){

		$pageNoExists = AccBankPassbookDtls::getpageNoExists($request);

		if (count($pageNoExists) != 0) {
			print_r("1");exit;
		} else {
			print_r("0");exit;
		}
	}

	/**  
	*  Image View Popup For Passbook
	*  @author Sastha 
	*  @return object to particular view page
	*  Created At 2021/03/03
	**/
	public function imgViewPopup(Request $request){


		$passbookPrevId = AccBankPassbookDtls::getPassbookMinId($request);
		$passbookNextId = AccBankPassbookDtls::getPassbookMaxId($request);
		$passbookImgdetails = AccBankPassbookDtls::fnGetPassbookImgDtls($request);
		return view('AccBankPassbookDtls.imgViewPopup',
										[	'request' => $request,
											'passbookImgdetails' => $passbookImgdetails,
											'passbookPrevId' => $passbookPrevId,
											'passbookNextId' => $passbookNextId,
										]);
	}

	/**  
	*  To Get Next Image Name
	*  @author Sastha 
	*  @return object to particular view page
	*  Created At 2021/03/03
	**/
	public function prevNxtImg_ajax(Request $request){
		$prevNxtImg = AccBankPassbookDtls::getPrevNxtImg($request);
		$prevNxtImgArray = json_encode($prevNxtImg);
		echo $prevNxtImgArray;
		exit();
	}


}