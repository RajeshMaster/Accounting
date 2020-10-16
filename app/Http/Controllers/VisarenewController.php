<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Visarenew;
use App\Model\Staff;
use DB;
use Input;
use Redirect;
use Session;
use Carbon;
use Config;
use Auth;
use Excel;
use PHPExcel_Worksheet_MemoryDrawing;
use PHPExcel_Style_Border;
use Illuminate\Support\Facades\Validator;
ini_set('max_execution_time', 0);
ini_set('memory_limit','256M');

class VisarenewController extends Controller {
	function index(Request $request) {
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$visaRenewDetails = Visarenew::fnGetVisaRenewDetails($request);
		//returning to view page
		return view('Visarenew.index',compact('request',
												'visaRenewDetails'));
	}
	function visaimportpopup(Request $request) {
		//For Get The DataBase List
		$getOldDbDetails = Staff::fnOldDbDetails();
		return view('Visarenew.visaimportpopup',['getOldDbDetails'=> $getOldDbDetails,
										'request' => $request]);
	}
	function importprocess(Request $request) {
		$oldUserQueryArray=array();
		$old_Date_Join=NULL;
		$certificateName="";
		$nickName="";
		//Get The New DataBase Details
		$getConnectionQuery = Staff::fnGetConnectionQuery($request);
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);
		// try {
		$db = DB::connection('otherdb');
		$db->getPdo();
			if($db->getDatabaseName()){
				$employee_count = Visarenew::fnGetEmployeewithVisaCount();
				$empcount = count($employee_count);
				//To Get The Latest Employee Details In New DataBase
				foreach ($employee_count as $key => $value) {
					$oldUserQuery[$key]=Visarenew::fnGetAllVisaDetailsMB($value->Emp_ID);

				}
				$g_val = count($oldUserQuery);
					if ($oldUserQuery) {
						$getOldUserRecordAsArray = array();
						$i = 0;
							foreach ($oldUserQuery as $key => $value) {
								if (isset($value[0]->user_id)) {
									// Employee Certification Fetch
									$certificateDetails = Visarenew::fnGetEmployeeCertificateDetails($value[0]->user_id);
									if (isset($certificateDetails[0])) {
										$certificateName = $certificateDetails[0]->certificateName;
										$nickName = $certificateDetails[0]->nickName;
									}
									// for Getting old Date of Joining
									if((!empty($value[0]->Old_ID)) && (isset($value[0]->Old_ID))) {
										// Check for SS Employee 
										$old_SS_DateofJoin=Visarenew::fnGetOldJoiningDate($value[0]->Old_ID);
											$old_Date_Join = (isset($old_SS_DateofJoin[0]->DOJ)?$old_SS_DateofJoin[0]->DOJ:NULL);
										if ((!empty($old_SS_DateofJoin[0]->Old_ID)) && isset($old_SS_DateofJoin[0]->Old_ID) && ((substr($old_SS_DateofJoin[0]->Old_ID,0,2))=="TR" || (substr($old_SS_DateofJoin[0]->Old_ID,0,2))=="SI")) {
											// Check for TRN Employee 
											$old_TRN_DateofJoin=Visarenew::fnGetOldJoiningDate($old_SS_DateofJoin[0]->Old_ID);
												$old_Date_Join = (isset($old_TRN_DateofJoin[0]->DOJ)?$old_TRN_DateofJoin[0]->DOJ:NULL);
											if((!empty($old_TRN_DateofJoin[0]->Old_ID)) && isset($old_TRN_DateofJoin[0]->Old_ID) && (substr($old_TRN_DateofJoin[0]->Old_ID,0,3))=="STU") {
												// Check for STU Employee 
												$old_STU_DateofJoin=Visarenew::fnGetOldJoiningDate($old_TRN_DateofJoin[0]->Old_ID);
													$old_Date_Join = (isset($old_STU_DateofJoin[0]->DOJ)?$old_STU_DateofJoin[0]->DOJ:NULL);
											}
										}
									}
									// End for Getting old date of joining
									$insertOldUserQuery = Visarenew::fnInsertOLDMBDetails($value,$old_Date_Join,$certificateName,$nickName);
									$old_Date_Join=NULL;
								}
							}
							Session::flash('success', 'Imported Sucessfully!'); 
							Session::flash('type', 'alert-success');
					} else {
						Session::flash('success', 'Record Not Imported Sucessfully'); 
						Session::flash('type', 'alert-danger'); 
					}
			} else{
				Session::flash('success', 'Invalid Db Connection'); 
				Session::flash('type', 'alert-danger'); 
			}
		// } catch (\Exception $e) {
  //       	Session::flash('success', 'Invalid Db Connection.'); 
		// 	Session::flash('type', 'alert-danger'); 
  //   	}
    return Redirect::to('Visarenew/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function addedit(Request $request) {
		if(!isset($request->Emp_ID)){
    		return Redirect::to('Visarenew/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
		}
		$visaRenewDetails = Visarenew::fnGetVisaRenewdata($request);
		$religiontype = getreligiontype();
		$periodofstay = array('1' => '1', '3' => '3', '5' => '5');
		return view('Visarenew.addedit',compact('request',
												'religiontype',
												'periodofstay',
												'visaRenewDetails'));
	}
	public static function addeditprocess(Request $request) {
		$visaDetailsRegister = Visarenew::fnupdateAddedVisaDetails($request);
		if($visaDetailsRegister) {
			Session::flash('success', 'Inserted Unsucessfully!' );
			Session::flash('type', 'alert-success'); 
		} else {
			Session::flash('type', 'Inserted Unsucessfully!'); 
			Session::flash('type', 'alert-danger'); 
		}
    return Redirect::to('Visarenew/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function visaview(Request $request) {
		$visaRenewDetails = Visarenew::fnGetVisaRenewdata($request);
		return view('Visarenew.visaview',compact('request',
												'visaRenewDetails'));
	}
	public static function visaExtensionFormDownload(Request $request) {
		$datetime = date('Ymd');
		$excel_name = $datetime."-".$request->Emp_ID." ".$request->empname." "."Visa Extension";
		$template_name = Config::get('constants.VISA_EXTENSION_PATH');
			Excel::load($template_name, function($objTpl) use($request) {
				$m=0;
				$visaValidYear = "Years";
				$visaExtendedYear = "Years";
				$dateOfJoining = array();
				$citizenshipArray = array('1'=> $msg = "INDIAN",'2'=> $msg = "JAPANESE");
				$borderArray = array('0'=>'I8',
									'1'=>'G15',
									'2'=>'W15',
									'3'=>'AC15',
									'4'=>'AG15',
									'5'=>'G18',
									'6'=>'O21',
									'7'=>'E24',
									'8'=>'U24',
									'9'=>'G27',
									'10'=>'G30',
									'11'=>'Y30',
									'12'=>'I33',
									'13'=>'X33',
									'14'=>'AD33',
									'15'=>'AH33',
									'16'=>'I36',
									'17'=>'Z36',
									'18'=>'I39',
									'19'=>'O39',
									'20'=>'S39',
									'21'=>'I42',
									'22'=>'I45',
									'23'=>'I48',
									'24'=>'I52',
									'25'=>'E7',
									'26'=>'W7',
									'27'=>'F10',
									'28'=>'Y10',
									'29'=>'G18',
									'30'=>'V18',
									'31'=>'AA18',
									'32'=>'AE18',
									'37'=>'N41',
									'38'=>'F55',
									'39'=>'AA55',
									'40'=>'F57',
									'41'=>'G60',
									'42'=>'X60',
									'43'=>'E76',
									'44'=>'S76',
									'45'=>'C81',
									'46'=>'Y81',
									'47'=>'E7',
									'48'=>'X7',
									'49'=>'E15',
									'50'=>'X15',
									'51'=>'G40',
									'52'=>'G43',
									'53'=>'G46',
									'54'=>'K49',
									'55'=>'I52',
									'56'=>'AA52',
									'57'=>'G55',
									'58'=>'N58',
									'59'=>'H61',
									'60'=>'V61',
									'61'=>'E9',
									'62'=>'V9',
									'63'=>'G33',
									'64'=>'G36',
									'65'=>'G39',
									'66'=>'K42',
									'67'=>'H45',
									'68'=>'B52',
									'69'=>'U52',
									'70'=>'AA52',
									'71'=>'AE52');
				$relationBorderArray=array('0'=>'T61',
											'1'=>'T63',
											'2'=>'T65',
											'3'=>'T67',
											'4'=>'T69',
											'5'=>'T71');
				$outlineBorderArray = array(
				  						'borders' => array(
				    						'outline' => array(
				      							'style' => PHPExcel_Style_Border::BORDER_THIN
				    							)
				  							)
										);
				$topBorder = array(
								'borders' => array(
    	    						'top'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_THIN
    	    							),
									)
								);

	    		$leftBorder = array(
	    						'borders' => array(
    	    						'left'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_THIN
    	    							),
        							)
								);
				$rightBorder = array(
								'borders' => array(
    	    						'right'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_THIN
    	    							),
        							)
								);
				$bottomBorder = array(
								'borders' => array(
    	    						'bottom'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_THIN
    	    							),
        							)
								);
				$leftDottedBorder = array(
	    						'borders' => array(
    	    						'left'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_DOTTED
    	    							),
        							)
								);
				$bottomDoubleBorder = array(
								'borders' => array(
    	    						'bottom'    => array(
    	    							'style' =>PHPExcel_Style_Border::BORDER_DOUBLE
    	    							),
        							)
								);
				$visaRenewDetails = Visarenew::fnGetVisaRenewdata($request);
					foreach ($visaRenewDetails as $empkey => $empvalue) {
						$empvisainfo[$m]['sheetFlgone'] = "0";
						$empvisainfo[$m]['I8'] = "";
						$empvisainfo[$m]['G15'] = (isset($citizenshipArray[$empvalue->citizenShip])?$citizenshipArray[$empvalue->citizenShip]:'');
						if(isset($empvalue->DOB) && ($empvalue->DOB !="") && ($empvalue->DOB !="0000-00-00") && ($empvalue->DOB != NULL)) {
							$empdateofbirth = explode('-', $empvalue->DOB);
							$empvisainfo[$m]['W15'] = $empdateofbirth[0];
							$empvisainfo[$m]['AC15'] = (int)$empdateofbirth[1];
							$empvisainfo[$m]['AG15'] = (int)$empdateofbirth[2];
						} else {
							$empvisainfo[$m]['W15'] = "";
							$empvisainfo[$m]['AC15'] = "";
							$empvisainfo[$m]['AG15'] = "";
						}
						$empvisainfo[$m]['O21'] = ($empvalue->placeofBirth!="")?strtoupper($empvalue->placeofBirth):'';
						$empvisainfo[$m]['E24'] = $empvalue->DesignationNM;
						$empvisainfo[$m]['U24'] = $empvalue->indiaAddress;
						$empvisainfo[$m]['G27'] = $empvalue->full_address;
						$empvisainfo[$m]['Y30'] = $empvalue->Mobile1;
						$empvisainfo[$m]['G30'] = '';
						$empvisainfo[$m]['I33'] = $empvalue->passportNo;
						if(isset($empvalue->passportExpiryDate) && ($empvalue->passportExpiryDate !="") && ($empvalue->passportExpiryDate !="0000-00-00") && ($empvalue->passportExpiryDate != NULL)) {
							$passportExpiry = explode('-', $empvalue->passportExpiryDate);
							$empvisainfo[$m]['X33'] = $passportExpiry[0];
							$empvisainfo[$m]['AD33'] = (int)$passportExpiry[1];
							$empvisainfo[$m]['AH33'] = (int)$passportExpiry[2];
						} else {
							$empvisainfo[$m]['X33'] = "";
							$empvisainfo[$m]['AD33'] = "";
							$empvisainfo[$m]['AH33'] = "";
						}
						$empvisainfo[$m]['I36'] = ($empvalue->NewVisaStatus!="")?strtoupper($empvalue->NewVisaStatus):'';
						if ($empvalue->visaValidPeriod == 1) {
							$visaValidYear = "Year";
						}
						$empvisainfo[$m]['Z36'] = $empvalue->visaValidPeriod." ".$visaValidYear;
						if(isset($empvalue->passportExpiryDate) && ($empvalue->passportExpiryDate !="") && ($empvalue->passportExpiryDate !="0000-00-00") && ($empvalue->passportExpiryDate != NULL)) {
							$visaExpiry = explode('-', $empvalue->visaExpiryDate);
							$empvisainfo[$m]['I39'] = $visaExpiry[0];
							$empvisainfo[$m]['O39'] = $visaExpiry[1];
							$empvisainfo[$m]['S39'] = $visaExpiry[2];
						} else {
							$empvisainfo[$m]['I39'] = "";
							$empvisainfo[$m]['O39'] = "";
							$empvisainfo[$m]['S39'] = "";
						}
						$empvisainfo[$m]['I42'] = $empvalue->visaNo;
						if ($empvalue->visaExtensionPeriod == 1) {
							$visaExtendedYear = "Year";
						}
						$empvisainfo[$m]['I45'] = $empvalue->visaExtensionPeriod." ".$visaExtendedYear;
						$empvisainfo[$m]['I48'] = $empvalue->reasonforExtension;
						$empvisainfo[$m]['I52'] = "";
						$empvisainfo[$m]['sheetFlgtwo'] = "1";
						$empvisainfo[$m]['E7'] = ' 株式会社 Microbit';
						$empvisainfo[$m]['W7'] = ' 大阪';
						$empvisainfo[$m]['F10'] = '大阪市淀川区西中島　５－６－３－３０５';
						$empvisainfo[$m]['Y10'] = '06-605-1251';
						$empvisainfo[$m]['G18'] = $empvalue->universityName;
						$empvisainfo[$m]['V18'] = $empvalue->complete_year;
						$empvisainfo[$m]['AA18'] = $empvalue->complete_month;
						if($empvalue->complete_year !="") {
							$empvisainfo[$m]['AE18'] = '10';
						} else {
							$empvisainfo[$m]['AE18'] = '';
						}
						$m++;
					}
				foreach ($borderArray as $key => $value) {
					if($key<24) {
							$objTpl->setActiveSheetIndex(0);
							$objTpl->getActiveSheet()->setCellValue($value,((isset($empvisainfo[0][$value])?$empvisainfo[0][$value]:'')));
							$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($bottomBorder);
					}
					if($key>24 && $key<=47) {
							$objTpl->setActiveSheetIndex(1);
							$objTpl->getActiveSheet()->setCellValue($value,((isset($empvisainfo[0][$value])?$empvisainfo[0][$value]:'')));
							$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($bottomBorder);
					}
					if($key>46 && $key<=60) {
							$objTpl->setActiveSheetIndex(2);
							$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($bottomBorder);
					}
					if($key>60 && $key<=67) {
							$objTpl->setActiveSheetIndex(3);
							$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($bottomBorder);
					}
					if($key>67) {
							$objTpl->setActiveSheetIndex(3);
							$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($bottomDoubleBorder);
					}
				}
				// Start of option 18 in excel sheet
				$objTpl->setActiveSheetIndex(1);
				$objTpl->getActiveSheet()->getStyle('M22')->applyFromArray($rightBorder);
				$degreeTypeLowerCase = strtolower($visaRenewDetails[0]->degreeType);
				if ($degreeTypeLowerCase == "ug") {
					$objTpl->getActiveSheet()->setCellValue('P14',('■'));
				} elseif ($degreeTypeLowerCase == "pg") {
					$objTpl->getActiveSheet()->setCellValue('I14',('■'));
				} else {
					$objTpl->getActiveSheet()->setCellValue('P16',('■'));
					if ($degreeTypeLowerCase == "diplamo") {
						$objTpl->getActiveSheet()->setCellValue('T16',($visaRenewDetails[0]->degreeType));
					} else {
					$objTpl->getActiveSheet()->setCellValue('T16',($visaRenewDetails[0]->specificationothers));
					}
				}
				// End of option 18 in excel sheet
				// Start of option 19 in excel sheet part-1
				$degreeTypeLowerCase = strtolower($visaRenewDetails[0]->degreeType);
				if ($degreeTypeLowerCase == "ug") {
					$objTpl->getActiveSheet()->setCellValue('AC27',('■'));
					$objTpl->getActiveSheet()->setCellValue('V31',('■'));
					$objTpl->getActiveSheet()->setCellValue('Z31',($visaRenewDetails[0]->depatmentName));
				} else {
					$objTpl->getActiveSheet()->setCellValue('V31',('■'));
					if ($degreeTypeLowerCase == "pg") {
						$objTpl->getActiveSheet()->setCellValue('Z31',($visaRenewDetails[0]->depatmentName));
					}elseif ($degreeTypeLowerCase == "diplomo") {
						$objTpl->getActiveSheet()->setCellValue('Z31',($visaRenewDetails[0]->depatmentName));
					} else {
					$objTpl->getActiveSheet()->setCellValue('Z31',($visaRenewDetails[0]->departmentothers));
					}
				}
				// End of option 19 in excel sheet
				$objTpl->getActiveSheet()->setCellValue('N41',($visaRenewDetails[0]->certificateNickName));
				// Start of option 21 in excel sheet
				$objTpl->getActiveSheet()->setCellValue('A47',($visaRenewDetails[0]->complete_year));
				$objTpl->getActiveSheet()->setCellValue('C47',($visaRenewDetails[0]->complete_month));
				// $objTpl->getActiveSheet()->setCellValue('E47',('India '.$visaRenewDetails[0]->degreeName));
				if(isset($visaRenewDetails[0]->sathisysDOJ) 
							&& ($visaRenewDetails[0]->sathisysDOJ !="") 
							&& ($visaRenewDetails[0]->sathisysDOJ !="0000-00-00") 
							&& ($visaRenewDetails[0]->sathisysDOJ != NULL)) {
						$sathiDateOfJoining = explode('-', $visaRenewDetails[0]->sathisysDOJ);
					$objTpl->getActiveSheet()->setCellValue('A49',($sathiDateOfJoining[0]));
					$objTpl->getActiveSheet()->setCellValue('C49',($sathiDateOfJoining[1]));
				}
				if(isset($visaRenewDetails[0]->DOJ) 
							&& ($visaRenewDetails[0]->DOJ !="") 
							&& ($visaRenewDetails[0]->DOJ !="0000-00-00") 
							&& ($visaRenewDetails[0]->DOJ != NULL)) {
						$dateOfJoining = explode('-', $visaRenewDetails[0]->DOJ);
					$objTpl->getActiveSheet()->setCellValue('A51',($dateOfJoining[0]));
					$objTpl->getActiveSheet()->setCellValue('C51',($dateOfJoining[1]));
				}
				// End of option 21 in excel sheet
				// This is for series 20 in excel
				if($visaRenewDetails[0]->certificateNickName !="") {
					$CertificateImagePath = imagecreatefrompng('./resources/assets/images/excelyes.png');
					$CertificateCoordinate = 'AB38';
				} else {
					$CertificateImagePath = imagecreatefrompng('./resources/assets/images/excelno.png');
					$CertificateCoordinate = 'AD38';
				}
				$CertificateImageforProcess = Visarenew::fnGetPngImageForDrawing($CertificateImagePath,
																	$CertificateCoordinate,$objTpl);
				$objTpl->getActiveSheet()->getStyle('A51:V51')->applyFromArray($bottomBorder);
				$objTpl->getActiveSheet()->getStyle('V45:V51')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('S63:S64')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('D69:D71')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('R45')->applyFromArray($leftBorder);
				$objTpl->getActiveSheet()->getStyle('T45')->applyFromArray($leftDottedBorder);
				$objTpl->setActiveSheetIndex(0);
				foreach ($relationBorderArray as $key => $value) {
					$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($leftBorder
					);
					if ($value == "T61") {
						$objTpl->getActiveSheet()->getStyle($value)->applyFromArray($topBorder
						);
					}
				}
				// Employee namespace
					$fullName = $visaRenewDetails[0]->FirstName.' '.$visaRenewDetails[0]->LastName;
					$upperCase = strtoupper($fullName);
					$objTpl->getActiveSheet()->setCellValue('G18',($upperCase));
				$objTpl->getActiveSheet()->getStyle('AE57:AE72')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('AE5:AJ13')->applyFromArray($outlineBorderArray);
				// $objTpl->getActiveSheet()->getStyle('AE5:AJ5')->applyFromArray($topBorder);
				$objTpl->getActiveSheet()->setCellValue('B9',('To the Director General of'));
				$objTpl->getActiveSheet()->setCellValue('AG8',('写　真'));
				$objTpl->getActiveSheet()->setCellValue('AG11',('Photo'));
				$objTpl->getActiveSheet()->setCellValue('A1',('別記第三十号の二様式（第二十一条関係）'));
				$objTpl->getActiveSheet()->setCellValue('H5',('  　                   在留期間更新許可申請書'));
				if ($visaRenewDetails[0]->Gender == 1) {
					$objTpl->getActiveSheet()->setCellValue('E21',(''));
					$genderImagePath = imagecreatefrompng('./resources/assets/images/excelmale.png');
					$genderCoordinate = 'E21';
				} elseif ($visaRenewDetails[0]->Gender == 2) {
					$objTpl->getActiveSheet()->setCellValue('G21',(''));
					$genderImagePath = imagecreatefrompng('./resources/assets/images/excelfemale.png');
					$genderCoordinate = 'G21';
				}
				$genderImageforProcess = Visarenew::fnGetPngImageForDrawing($genderImagePath,
																		$genderCoordinate,$objTpl);
				if ($visaRenewDetails[0]->martialStatus == 1) {
					$objTpl->getActiveSheet()->setCellValue('AI21',(''));
					$objTpl->getActiveSheet()->setCellValue('AJ21',(''));
					$statusImagePath = imagecreatefrompng('./resources/assets/images/excelno.png');
					$statusCoordinate = 'AI21';
				} elseif ($visaRenewDetails[0]->martialStatus == 2) {
					$objTpl->getActiveSheet()->setCellValue('AF21',(''));
					$statusImagePath = imagecreatefrompng('./resources/assets/images/excelyes.png');
					$statusCoordinate = 'AF21';
				}
				$statusImageforProcess = Visarenew::fnGetPngImageForDrawing($statusImagePath,
																	$statusCoordinate,$objTpl);
				$objTpl->getActiveSheet()->getStyle('I52')->applyFromArray($bottomBorder);
				if ($visaRenewDetails[0]->crimeRecord == 2) {
					$objTpl->getActiveSheet()->setCellValue('AI52',(''));
					$crimeImagePath = imagecreatefrompng('./resources/assets/images/excelno.png');
					$crimeCoordinate = 'AI52';
				} elseif ($visaRenewDetails[0]->crimeRecord == 1) {
					$objTpl->getActiveSheet()->setCellValue('C52',(''));
					$crimeImagePath = imagecreatefrompng('./resources/assets/images/excelyes.png');
					$crimeCoordinate = 'C52';
					$objTpl->getActiveSheet()->setCellValue('I52',($visaRenewDetails[0]->crimeDetails));
				}
				$crimeImageforProcess = Visarenew::fnGetPngImageForDrawing($crimeImagePath,
																	$crimeCoordinate,$objTpl);
				$objTpl->setActiveSheetIndex(3);
				$objTpl->getActiveSheet()->getStyle('B50')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle("B50")->getFont()->setSize(2);
				$objTpl->setActiveSheetIndex(4);
				$objTpl->getActiveSheet()->getStyle('D4')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('H5:H6')->applyFromArray($rightBorder);
				$objTpl->getActiveSheet()->getStyle('A55')->applyFromArray($bottomBorder);
				$objTpl->getActiveSheet()->getStyle('C59')->applyFromArray($bottomBorder);
				$objTpl->getActiveSheet()->getStyle('D55:L55')->applyFromArray($bottomBorder);
				$objTpl->setActiveSheetIndex(0);
				$objTpl->getActiveSheet(0)->setSelectedCells('G15');
				$flpath='.xls';
          		header('Content-Type: application/vnd.ms-excel');
          		header('Content-Disposition: attachment;filename="'.$flpath.'"');
          		header('Cache-Control: max-age=0');
        	})->setFilename($excel_name)->download('xls');
	}
}