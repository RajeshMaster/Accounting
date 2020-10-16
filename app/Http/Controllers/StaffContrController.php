<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\StaffContract;
use App\Model\User;
use DB;
use Input;
use Redirect;
use Session;
use Config;
use Fpdf;
use Fpdi;
require_once('vendor/setasign/fpdf/fpdf.php');
require_once('vendor/setasign/fpdi/fpdi.php');
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_MemoryDrawing;

class StaffContrController extends Controller {
	function index(Request $request) {
	//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
	//Filter Process
		if (empty($request->resignid)) {
			$resignid = 0;
		} else {
			$resignid = 1;
		}
		if ($request->selectsort == "") {
        	$request->selectsort = "EndDate";
      	}
	//SORT POSITION
       $sortMargin = "margin-right:0px;";
	//Select box Values
       $selectVal = array("Emp_ID"=>trans('messages.lbl_empid'),
						"LastName"=>trans('messages.lbl_empName'),
						"EndDate"=>trans('messages.lbl_periodofWork'),
						"Salary"=>trans('messages.lbl_salary'),
						);
       
	//Sording Order
		if (empty($request->sortOrder)) {
        	$request->sortOrder = "DESC";
      	}
      	if ($request->sortOrder == "asc") {  
      		$request->sortstyle="sort_asc";
      	} else {  
   			$request->sortstyle="sort_desc";
   		}
   		if ($request->searchmethod == 1 || $request->searchmethod == 2) {
        $sortMargin = "margin-right:260px;";
      	} else {
        $sortMargin = "margin-right:0px;";
      	}
	//Query For Contract Details
     	$contractdet = array();
	$contract=StaffContract::fnContractDetails($request, $resignid);
	//print_r($contract);exit();
	$i = 0;
		foreach($contract as $key=>$data) {
			$contractdet[$i]['Emp_ID'] = $data->Emp_ID;
			$contractdet[$i]['FirstName'] = $data->FirstName;
			$contractdet[$i]['LastName']=$data->LastName;
			$contractdet[$i]['Id']=$data->Id;
			$contractdet[$i]['StartDate']=$data->StartDate;
			$contractdet[$i]['EndDate']=$data->EndDate;
			$contractdet[$i]['Salary']=$data->Salary;
			$contractdet[$i]['Allowance1']=$data->Allowance1;
			$contractdet[$i]['Allowance2']=$data->Allowance2;
			$contractdet[$i]['Allowance3']=$data->Allowance3;
			$contractdet[$i]['Allowance4']=$data->Allowance4;
			$contractdet[$i]['Allowance5']=$data->Allowance5;
			$contractdet[$i]['Allowance6']=$data->Allowance6;
			$contractdet[$i]['Allowance7']=$data->Allowance7;
			$contractdet[$i]['Allowance8']=$data->Allowance8;
			$contractdet[$i]['Allowance9']=$data->Allowance9;
			$contractdet[$i]['Allowance10']=$data->Allowance10;
			$contractdet[$i]['Total']=$data->Total;
			$contractdet[$i]['Contract_date']=$data->Contract_date;
			$contractdet[$i]['Remarks']=$data->Remarks;
			$contractdet[$i]['Validity']=$data->Validity;
	$i++;
	}		
	// print_r($contractdet);
	// exit();
	return view('StaffContract.index',['request' => $request,
									  'selectVal' => $selectVal,
									  'contractdet' => $contractdet,
									  'resignid' => $resignid,
									  'sortMargin' => $sortMargin,
									  'contract' => $contract]);
	}
	function contractdetails(Request $request) {
		$get_det=array();
		$emp_Name=array();
		if(Session::get('viewid') !=""){
	        $request->viewid = Session::get('viewid');
	        $request->Name = Session::get('Name');
	    } 
	  
		$emp_Id = $request->viewid; 
	//Get The Employee Name
		$emp_Names=StaffContract::emp_no_name($request);
		 if (isset($emp_Names)) {
			$emp_Name = empnamelength($emp_Names[0]->LastName, $emp_Names[0]->FirstName, 50);
		} else {
		 	$emp_Name = $request->Name = Session::get('Name');

		}
	//Get The Contract Details	
		$get_det=StaffContract::contract($request);
		return view('StaffContract.contractdetails',['request' => $request,
											       'emp_Name' => $emp_Name,
												   'emp_Id' => $emp_Id,
												  'get_det' => $get_det]);
	}
	function contractview(Request $request){
	//Variable Declare
		//print_r($request->rid);exit();
		$emp_Id = $request->viewid;
		$edit_query = array();
		$totallowance = array();
		$numyear = "";
		$radioId = "";
		$start = "";
		$end = "";
		$date = date('Ymd');
		if ($request->rid !=1 ) {
	//Get the contract employee details
		$edit_query = StaffContract::contract_add_edit($request);
		$radioId = $edit_query[0]->Id;
		$start = $edit_query[0]->StartDate;
		$end = $edit_query[0]->EndDate;
		$numyear = $end-$start;

		$totallowance=number_format(str_replace(',','',$edit_query[0]->Salary)
											+str_replace(',','',$edit_query[0]->Allowance1)
											+str_replace(',','',$edit_query[0]->Allowance2)
											+str_replace(',','',$edit_query[0]->Allowance3)
											+str_replace(',','',$edit_query[0]->Allowance4)
											+str_replace(',','',$edit_query[0]->Allowance5)
											+str_replace(',','',$edit_query[0]->Allowance6)
											+str_replace(',','',$edit_query[0]->Allowance7)
											+str_replace(',','',$edit_query[0]->Allowance8)
											+str_replace(',','',$edit_query[0]->Allowance9)
											+str_replace(',','',$edit_query[0]->Allowance10));
		}
		//print_r($totallowance);exit();
	//Get Fields from the table
		$tabsql=StaffContract::tablecreate();
		//print_r($tabsql);exit();
		$get_tabFld = array();
		foreach ($tabsql as $key => $value) {
			 $get_tabFld[$key]['delflg'] = $value->delflg;
			 //print_r(session::all());exit();
			 if (Session::get('languageval') != 'jp') {
			 	$get_tabFld[$key]['allowance_lan'] = $value->allowance_eng;
			 } else {
			 	$get_tabFld[$key]['allowance_lan'] = $value->allowance_jap;
			 }
		} 
		
		return view('StaffContract.contractview',['request' => $request,
												   'edit_query' => $edit_query,
												   'numyear' => $numyear,
												   'totallowance' => $totallowance,
												   'get_tabFld' => $get_tabFld,
												   'emp_Id' => $emp_Id,
												   'radioId ' => $radioId,
												   'date' => $date, 
												   ]);
	}
	function addeditprocess(Request $request) {
		if ($request->rid == 2) {
			$update = StaffContract::updateprocess($request);
			if($update) {
				Session::flash('success', 'Updated Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Updated Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('viewid',$request->viewid);
			Session::flash('Name',$request->Name);
		} else {
			$autoincId=User::getautoincrement();
			$passid=$autoincId;
			$insert = StaffContract::addeditprocess($request);
			//print_r($insert);exit();
			if($insert) {
				Session::flash('success', 'Inserted Sucessfully!'); 
				Session::flash('type', 'alert-success'); 
			}else {
				Session::flash('type', 'Inserted Unsucessfully!'); 
				Session::flash('type', 'alert-danger'); 
			}
			Session::flash('viewid',$request->viewid);
			Session::flash('Name',$request->Name);
		}
		// $autoincId=User::getautoincrement();
		// $passid=$autoincId;
		// $insert = StaffContract::addeditprocess($request);
		// Session::flash('viewid',$request->viewid);
		// Session::flash('Name',$request->Name);
		return Redirect::to('StaffContr/contractdetails?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}
	function contractdownload(Request $request){
		$template_name = 'resources/assets/uploadandtemplates/templates/contract.xls';
		$tempname = "Invoice";
		$lastname = $request->empname;
      	$dat = date("Ymd");
		$excel_name=$lastname."_contract_".$dat.'.xls';
		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
      	Excel::load($template_name, function($objPHPExcel) use($request) {
      	$objPHPExcel->setActiveSheetIndex(0);
		$cell1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,52)->getValue();
		$cell2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,52)->getValue();
		$cell3 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,3)->getValue();
		$cell4 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,3)->getValue();
		if($cell1 =='123' && $cell2 =='123' && $cell3=='123' && $cell4 =='123'){
			$writeflag ='1';
		}
		if($writeflag == '1'){
			$passport_tablesql= StaffContract::passport_table_exist_check();
			if ($passport_tablesql == "") {

			} 
			$tableverify = 0;
			$detail = StaffContract::employee($request);
			$Passport_value = array();
			$passport_valueflg ='OFF';
			foreach ($detail as $key => $value) {
				$Passport_value['FirstName'] = $value->FirstName;
				$Passport_value['LastName'] = $value->LastName;
				$Passport_value['Gender'] = $value->Gender;
				$Passport_value['DOB'] = $value->DOB;
				$Passport_value['Address1'] = $value->Address1;
				$passport_valueflg ='ON';
			}
			if (count($passport_tablesql) == 1) {
				
				if($passport_valueflg == 'OFF'){
					$Passport_value['PassportNo'] = '';
					$Passport_value['FirstName'] = '';
					$Passport_value['LastName'] = '';
					$Passport_value['Gender'] = '';
					$Passport_value['DOB'] = '';
					$Passport_value['Address1'] = '';
				}
			}
			 $Passport_value['PassportNo']= '';
			 $Employee_sql = StaffContract::employee($request);
			 $Employee_value = array();
			 foreach ($Employee_sql as $key => $value) {
			 	$Employee_value['Address1'] = $value->Address1;
			 	$Employee_value['Mobile1'] = $value->Mobile1;
			 	$Employee_value['KanaFirstName'] = $value->KanaFirstName;
			 	$Employee_value['KanaLastName'] = $value->KanaLastName;
			 }
			 $contract_sql =StaffContract::contractMaxDate($request);
			 $contract_value = array();
			 foreach ($contract_sql as $key => $value) {
			 	$contract_value['StartDate'] = $value->StartDate;
			 	$contract_value['EndDate'] = $value->EndDate;
			 	$contract_value['Salary'] = $value->Salary;
			 	$contract_value['Contract_date'] = $value->Contract_date;
			 	$contract_value['Remarks'] = $value->Remarks;
			 	$contract_value['Total'] = $value->Total;
			 	//$contract_value['Travel_Expense'] = $value->Travel_Expense;
			 }
			$firstname =trim($Passport_value['FirstName']);
			$lastname =trim($Passport_value['LastName']);
			$kanafirstname=trim($Employee_value['KanaFirstName']);
			$kanalastname=trim($Employee_value['KanaLastName']);
			$name_lower =$firstname.' '.$lastname;
			$name = strtoupper($name_lower);

			$kananame =$kanafirstname.' '.$kanalastname;
		//Start Date
			if(!empty($contract_value['StartDate'])){
				$periodwork_split= explode('-', $contract_value['StartDate']);
				$periodwork_start = $periodwork_split[0].'/'.$periodwork_split[1].'/'.
								$periodwork_split[2];	
			} else {
				$periodwork_start ="";
			}
			
		//End Date
			if(!empty($contract_value['EndDate'])){
				$periodend_split= explode('-', $contract_value['EndDate']);
				$periodwork_end = $periodend_split[0].'/'.$periodend_split[1].'/'.$periodend_split[2];	
			} else{
				$periodwork_end="";
			}
		//Both Start Date and End Date
			$periodwork ='['.$periodwork_start.' to '.$periodwork_end.']';
		//Contract Date
			if(!empty($contract_value['Contract_date']))
			{
				$contractdate_split= explode('-', $contract_value['Contract_date']);
				//India Contract Date
				$contractdate = '['.$contractdate_split[0].'/'.$contractdate_split[1].'/'.
							$contractdate_split[2].']';	
				//Japan Contract date
				$wareki_contract =$contractdate_split[0].$contractdate_split[1].$contractdate_split[2];

				$contract_date = self::to_wareki($wareki_contract);			
			}else{
				$contractdate="";
				$contract_date="";
			}
		
		//性別
		if($Passport_value['Gender'] == 1){
			$gender ="男[Male]";
		}
		if($Passport_value['Gender'] == 2){
			$gender  = "女[Female]";
		}

		//生年月日
		if(!empty($Passport_value['DOB'])) {
			$date_split= explode('-', $Passport_value['DOB']);
			$dob = $date_split[0].' 年'.$date_split[1].'月'.$date_split[2].'日';
			//年齢
			$age_disp = floor((time() - strtotime($Passport_value['DOB']))/31556926);
			$age = $age_disp.' 歳';
		} else {
			$dob  ='';
			$age='';
		}

		//Address
		if (!empty($Employee_value['Address1'])) {
			// TODO
			/*if (is_numeric($Employee_value['Address1'])) {
				$addrs=contractModel::contractAddrs($Employee_value['Address1']);
				$addrs_result=mysql_query($addrs);
				if (!$$addrs_result) {
					throw new Exception(mysql_error());
				}
				$Address11 = mysql_fetch_assoc($addrs_result);
				$Address22 = "〒" . $Address11['pincode'] . $Address11['jpstate'] . $Address11['jpaddress'] . "-" . $Address11['roomno'] . "号";
				$Address = str_replace("\r\n", ' ', $Address22);
			} else {*/
			$Address = str_replace("\r\n", ' ', trim($Employee_value['Address1']));	
			// }
		} else {
			$Address = str_replace("\r\n", ' ', trim($Employee_value['Address1']));
		}

	//Telephone Number
		if (!empty($Employee_value['Mobile1'])) {
			if (substr($Employee_value['Mobile1'], 0, 1) == "0") {
				$Tel = "'" . $Employee_value['Mobile1'];
			} else {
				$Tel =$Employee_value['Mobile1'];	
			}
		} else {
			$Tel =$Employee_value['Mobile1'];
		}

		if(empty($Tel)){
			$Tel = '-';
		}
		if(!empty($contract_value['StartDate'])){
			$wareki_start =$periodwork_split[0].$periodwork_split[1].$periodwork_split[2];
		} else {
			$wareki_start ="";
		}	
		if(!empty($contract_value['EndDate'])){
			$wareki_end =$periodend_split[0].$periodend_split[1].$periodend_split[2];
		} else {
			$wareki_end ="";
		}
		$stardate = self::to_wareki($wareki_start);
		$enddate = self::to_wareki($wareki_end);
		$period =$stardate.'　～　'. $enddate;
		if(!empty($contract_value['Salary'])){
			$toatal =$contract_value['Salary'];
			$salary = $contract_value['Salary'];
			$travel = $contract_value['Salary'];
			$accommodation = $contract_value['Salary'];
		} else {
			$toatal = "";
			$salary = "";
			$travel = "";
			$accommodation = "";
		}
		if(!empty($contract_value['Total'])){
			$total= $contract_value['Total'];
		} else {
			$total="";
		}
		$objPHPExcel->setActiveSheetIndex(0);
		if($tableverify==0) {
		 	$objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setSize(10)->getColor()->setRGB('FF0000');
			$objPHPExcel->getActiveSheet()->getStyle('C8')->getFont()->setSize(10)->getColor()->setRGB('FF0000');			
		}
		$objPHPExcel->getActiveSheet()
		 ->setCellValue('F5', $name)//Name
		 ->setCellValue('M5', $gender)//Gender
		 ->setCellValue('F6', $kananame)//ふりカナ
		 ->setCellValue('F7', $Passport_value['PassportNo'])//Passport No
		 ->setCellValue('F9', $dob)//生年月日
		 ->setCellValue('M9', $age)//ﾌﾘｶﾞﾅ
		 ->setCellValue('F10', $Address)//住所
		 ->setCellValue('F11', $Tel)//ＴＥＬ
		 ->setCellValue('F15', $period)//雇用期間
		 ->setCellValue('L15', $periodwork)//雇用期間
		 ->setCellValue('G27', $salary)//基本給
		 ->setCellValue('H29', $travel .' 円')//通勤
		 ->setCellValue('K29', $accommodation.' 円')//住居
		 ->setCellValue('F37', ''/*$contract_value[6]*/)//その他
		 ->setCellValue('F41', $contract_date)//契約日
		 ->setCellValue('I41', ' '.$contractdate)//契約日
		 ->setCellValue('O27', $total.' 円')//労働者
		 ->setCellValue('F49', $name);//労働者*/
		 $objPHPExcel->getActiveSheet(0)->setSelectedCells('A1');

		$gdImage = imagecreatefromjpeg('resources/assets/images/signImg/sign.jpg');

		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('Sample image');
		$objDrawing->setDescription('Sample image');
		$objDrawing->setImageResource($gdImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		$objDrawing->setHeight(50);
		$objDrawing->setCoordinates('L48');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		// Write the file
		$dat = date("Ymd");
		$flpath=$lastname."_contract_".$dat.'.xls';
		//print_r($salary);exit();

			 //print_r($Employee_value);exit();
			//print_r($Passport_value);exit();
			//$passportexist=mysql_query($passport_tablesql);
		}
      	})->setFilename($excel_name)->download('xls');

	}
	//和暦変換用の関数
function to_wareki($seireki) {
	//年月日を文字列として結合
	if ($seireki <= "19120729") {
		$gg = "明治";
		$yy = substr($seireki, 0, 4) - 1867;
	} elseif ($seireki >= "19120730" && $seireki <= "19261224") {
		$gg = "大正";
		$yy = substr($seireki, 0, 4) - 1911;
	} elseif ($seireki >= "19261225" && $seireki <= "19890107") {
		$gg = "昭和";
		$yy = substr($seireki, 0, 4) - 1925;
	} elseif ($seireki >= "19890108") {
		$gg = "平成";
		$yy = substr($seireki, 0, 4) - 1988;
	}
		$wareki = "{$gg}　{$yy}年　".substr($seireki, 4, 2)."月　".substr($seireki, 6, 2)."日";
	return $wareki;
}
// public function cdate_ajax(){
// 		$start=$_REQUEST['startdate'];
// 		$end=$_REQUEST['enddate'];
// 		$empid=$_REQUEST['empid'];
// 		$systemdate = date('Y-m-d');
// 		$id=$_REQUEST['id'];
// 		if (empty($id)) {
// 			$dateQuery = StaffContract::contractAjax($empid,$end,$start);
// 		} else {
// 			$dateQuery = StaffContract::contractAjax_id($empid,$end,$start,$id);
// 		}
// 		// $cnt = mysql_query($dateQuery);
// 		// if (!$cnt) {
// 		// 			throw new Exception(mysql_error());
// 		// 		}
// 		// $row=mysql_fetch_array($cnt);
// 		//$branchquery=json_encode($dateQuery);
// 		echo $branchquery;exit;
// 	}
}