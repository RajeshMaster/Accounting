<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Model\Setting;

use session;

use Redirect;

use App\Http\Common\settingscommon;

use Excel;

use PHPExcel_Worksheet_PageSetup;

use PHPExcel_Style_Fill;

use PHPExcel_IOFactory;

use PHPExcel_Shared_Date;

use ExcelToPHPCal;

use PHPExcel_Reader_Excel5;

use Input;



class SettingController extends Controller {

	function index(Request $request) { 

		return view('Setting.index',['request'=> $request]);

	}



	function singletextpopup(Request $request) {

		$getTableFields = settingscommon::getDbFieldsforProcess();

		if (($request->location == 2) && ($request->tablename == "mstbanks") ) {

			$tablename = $request->tablename.$request->location;

		} else {

			$tablename = $request->tablename;

		}

	 	$query = setting::selectOnefieldDatas($getTableFields[$tablename]['selectfields'],

	 										  $getTableFields[$tablename]['commitfields'][0],

	 										  $request);

		$requestAsJSONArray = json_encode($request->all());

		$headinglbl = $getTableFields[$tablename]['labels']['heading'];

		$field1lbl = $getTableFields[$tablename]['labels']['field1lbl'];

		$selectfiled  = $getTableFields[$tablename]['selectfields'];

		return view('Setting.singletextpopup',['getdetails' => $query,

												'request'=>$request,

												'headinglbl'=>$headinglbl,

												'field1lbl' => $field1lbl,

												'selectfiled' => $selectfiled,

												'getTableFields'=> $getTableFields,

												'requestAsJSONArray' => $requestAsJSONArray]);

	}

	

	function SingleFieldaddedit(Request $request) {

		if ($request->flag == 2) {

	 		echo $update_query=Setting::updateSingleField($request);

	 		exit();

		} 

		$tbl_name = $request->tablename;

		if (!empty($request->location)) {

			$location = $request->location;

			$orderidval = Setting::Orderidgenerateforbank($location,$tbl_name);

			// echo $orderid = $orderidval+1;

	 		// $ins_query=Setting::insertqueryforbank($tbl_name,$request);

	 		$orderidarray['orderid'] = $orderidval+1;

	 		$ins_query=Setting::insertqueryforbank($tbl_name,$request);

	 		$orderidval = Setting::Orderidgeneratefortotal($location,$tbl_name);

	 		$orderidarray['totalid'] = $orderidval;

	 		echo json_encode($orderidarray);

		} else {

			$orderidval = Setting::Orderidgenerate($tbl_name);

	 		$orderidarray['orderid'] = $orderidval+1;

	 		$ins_query=Setting::insertquery($tbl_name,$request);

	 		$location="";

	 		$orderidval = Setting::Orderidgenerateforbranchtotal($location,$tbl_name);

	 		$orderidarray['totalid'] = $orderidval;



	 		echo json_encode($orderidarray);



		}

	}

	public static function twotextpopup(Request $request) {

		$tbl_name = $request->tablename;

		$getTableFields = settingscommon::getDbFieldsforProcess();

		$query = setting::selectOnefieldDatas($getTableFields[$tbl_name]['selectfields'],

											  $getTableFields[$tbl_name]['commitfields'][0],

											  $request);

		$requestAsJSONArray = json_encode($request->all());

		$headinglbl = $getTableFields[$tbl_name]['labels']['heading'];

		$field1lbl = $getTableFields[$tbl_name]['labels']['field1lbl'];

		$field2lbl = $getTableFields[$tbl_name]['labels']['field2lbl'];

		$selectfiled  = $getTableFields[$tbl_name]['selectfields'];

		return view('Setting.twofieldpopup',['query' => $query,

												'request'=>$request,

												'headinglbl'=>$headinglbl,

												'field1lbl' => $field1lbl,

												'field2lbl' => $field2lbl,

												'selectfiled' => $selectfiled,

												'getTableFields'=> $getTableFields,

												'requestAsJSONArray' => $requestAsJSONArray]);

	}

	function twoFieldaddedit(Request $request) {

		if ($request->flag == 2) {

	 		echo $update_query=Setting::updatetwoField($request);

	 		exit();

		}

		$tbl_name = $request->tablename;

		$orderidval = Setting::Orderidgenerate($tbl_name);

	 	echo $orderid = $orderidval;

	 	$ins_query=Setting::insertquerytwofield($tbl_name,$request,$orderid);

	}

	function selectthreefieldDatas(Request $request) {

		$getTableFields = settingscommon::getDbFieldsforProcess();

		if (($request->location == 2) && ($request->tablename == "mstbankbranch") ) {

			$tablename = $request->tablename.$request->location;

		} else {

			$tablename = $request->tablename;

		}

		if ($request->parametersub == 1) {

			$query = Setting::selectthreefieldDatasforbank(

	 		$getTableFields[$tablename]['selectfields'],

	 		$getTableFields[$tablename]['commitfields'][0],

	 		$getTableFields[$tablename]['selectboxfields'][1],$request);

		} else {

			$query = Setting::selectthreefieldDatas(

	 		$getTableFields[$tablename]['selectfields'],

	 		$getTableFields[$tablename]['commitfields'][0],

	 		$getTableFields[$tablename]['selectboxfields'][1],$request);

		}

		$requestAsJSONArray = json_encode($request->all());

		$headinglbl = $getTableFields[$tablename]['labels']['heading'];

		$field1lbl = $getTableFields[$tablename]['labels']['field1lbl'];

		$field2lbl = $getTableFields[$tablename]['labels']['field2lbl'];

		$field3lbl = $getTableFields[$tablename]['labels']['field3lbl'];

		$selectfiled  = $getTableFields[$tablename]['selectfields'];

		if ($request->parametersub == 2 ) {

        	$tablename = "dev_expensesetting";

    	} else if($request->parametersub == 3 ) {

    		$tablename = "inv_set_transfermain";

    	} else if($request->parametersub == 4 ) {

    		$tablename = "inv_set_salarymain";

    	} else {

    		$tablename = $request->tablename;

    	}

		if ($request->tableselect!="" && $request->tableselect!="text") {

                $selectboxval = Setting::selectboxDatas(

                $getTableFields[$tablename]['selectboxfields'],

				$getTableFields[$tablename]['commitfields'][0],$request);

		}

		if ($request->parametersub == 1) {

			return view('Setting.selectthreefieldDatasforbank',['query'=>$query,

												'request'=>$request,

												'headinglbl'=>$headinglbl,

												'field1lbl' => $field1lbl,

												'field2lbl' => $field2lbl,

												'field3lbl' => $field3lbl,

												'selectfiled' => $selectfiled,

												'getTableFields'=> $getTableFields,

												'selectboxval'=> $selectboxval,

												'requestAsJSONArray' => $requestAsJSONArray]);

		} else {

			return view('Setting.selectthreefieldDatas',['query'=>$query,

												'request'=>$request,

												'headinglbl'=>$headinglbl,

												'field1lbl' => $field1lbl,

												'field2lbl' => $field2lbl,

												'field3lbl' => $field3lbl,

												'selectfiled' => $selectfiled,

												'getTableFields'=> $getTableFields,

												'selectboxval'=> $selectboxval,

												'requestAsJSONArray' => $requestAsJSONArray]);

		}

	}

	function threeFieldaddeditforbank(Request $request) {

		if ($request->flag == 2) {

	 		echo $update_query=Setting::updatethreeField($request);

	 		exit();

		} 

		$location = $request->location;

		$tbl_name = $request->tablename;

		if (!empty($location)) {

			$orderidval = Setting::Orderidgenerateforbranch($tbl_name,$location);

	 		$orderid = $orderidval+1;



	 		// $orderidarray['orderid'] = $orderidval+1;

	 		// $ins_query=Setting::insertqueryforbranch($tbl_name,$request);

	 		$ins_query=Setting::insertqueryforbranch($tbl_name,$request);

	 		$orderidval = Setting::Orderidgenerateforbranchtotal($location,$tbl_name);

	 		$orderidarray['totalid'] = $orderidval;

	 		$orderidarray['orderid'] = $orderid;

	 		

	 		echo json_encode($orderidarray);



		}

	}

	function threeFieldaddedit(Request $request) {

		if ($request->flag == 2) {

	 		echo $update_query=Setting::updatethreeField($request);

	 		exit();

		} 

		$tbl_name = $request->tablename;

			$orderidval = Setting::Orderidgenerate($tbl_name);

	 		echo $orderid = $orderidval+1;

	 		$ins_query=Setting::insertqueryforbranch($tbl_name,$request);

	}

	function uploadpopup(Request $request) {

		if ($request->screen_name == "uploadestimatepopup") {

			$headinglbl = "Upload Estimate Template";

		} else if ($request->screen_name == "uploadinvoicepopup") {

			$headinglbl = "Upload Invoice Template";

		}

		$getTableFields = settingscommon::getDbFieldsforProcess();

		return view('Setting.uploadpopup',['headinglbl'=> $headinglbl ]);

	}

	function useNotuse(Request $request) {

		$usenotuse = setting::updateUseNotUse($request);

	}

	function settingpopupupload(Request $request) {

		$tmpFile="";

		if($request->xlfile != "") {

			if ($request->heading == "Upload Estimate Template") {

				$excel_name="estimation";

				$ifile = $excel_name.".". self::getExtension($_FILES["xlfile"]["name"]);

				$destinationPath = 'resources/assets/uploadandtemplates/templates';

		      	chmod($destinationPath, 0777);

		      	$destinationPath=$destinationPath."/";

				$tmpFile = $destinationPath.$ifile;

				if (!is_dir($destinationPath)) {

					mkdir($destinationPath, true);

				}



				Excel::load($tmpFile, function($objPHPExcel) use($request, $destinationPath, $ifile) {

						$objPHPExcel->setActiveSheetIndex();

						$objPHPExcel->setActiveSheetIndex(0);



						$a1 = $objPHPExcel->getActiveSheet()->getCell('B2')->getValue();

						$d1 = $objPHPExcel->getActiveSheet()->getCell('B20')->getValue();

						$e1 = $objPHPExcel->getActiveSheet()->getCell('R20')->getValue();

						$f1 = $objPHPExcel->getActiveSheet()->getCell('U20')->getValue();

						$g1 = $objPHPExcel->getActiveSheet()->getCell('Y20')->getValue();

						$h1 = $objPHPExcel->getActiveSheet()->getCell('AD20')->getValue();

						$i1 = $objPHPExcel->getActiveSheet()->getCell('U40')->getValue();

						$j1 = $objPHPExcel->getActiveSheet()->getCell('U41')->getValue();



						if ($a1 != "御見積書" || $d1 != "品名" || $e1 != "数量" || $f1 != "単価" 

							|| $g1 != "金額" || $h1 != "摘要" ||

							$i1 != "小計" || $j1 != "消費税") {

								echo "Invalid Template";

								exit;

						} 

						else {

							$extensiondoc = Input::file('xlfile')->getClientOriginalExtension();

							$fileNamedoc = "estimation".'.'.$extensiondoc;

							move_uploaded_file($_FILES['xlfile']['tmp_name'], $destinationPath.$fileNamedoc);

						}

					});

				if($tmpFile) {

					Session::flash('success', 'Template File Has Been Uploaded Successfully!'); 

					Session::flash('type', 'alert-success'); 

				} else {

					Session::flash('type', 'Template File Has Been Uploaded Unsuccessfully!'); 

					Session::flash('type', 'alert-danger'); 

				}

			} else {

				$excel_name="invoice";

				$ifile = $excel_name.".". self::getExtension($_FILES["xlfile"]["name"]);

				$destinationPath = 'resources/assets/uploadandtemplates/templates';

		      	chmod($destinationPath, 0777);

		      	$destinationPath=$destinationPath."/";

				$tmpFile = $destinationPath.$ifile;

				if (!is_dir($destinationPath)) {

					mkdir($destinationPath, true);

				}



				Excel::load($tmpFile, function($objPHPExcel) use($request, $destinationPath, $ifile) {

						$objPHPExcel->setActiveSheetIndex();

						$objPHPExcel->setActiveSheetIndex(0);



						$a1 = $objPHPExcel->getActiveSheet()->getCell('B2')->getValue();

						$d1 = $objPHPExcel->getActiveSheet()->getCell('B19')->getValue();

						$e1 = $objPHPExcel->getActiveSheet()->getCell('R19')->getValue();

						$f1 = $objPHPExcel->getActiveSheet()->getCell('U19')->getValue();

						$g1 = $objPHPExcel->getActiveSheet()->getCell('X19')->getValue();

						$h1 = $objPHPExcel->getActiveSheet()->getCell('AC19')->getValue();

						$i1 = $objPHPExcel->getActiveSheet()->getCell('U39')->getValue();

						$j1 = $objPHPExcel->getActiveSheet()->getCell('U40')->getValue();



						if ($a1 != "請求書" || $d1 != "品名" || $e1 != "数量" || $f1 != "単価" 

							|| $g1 != "金額" || $h1 != "摘要" ||

							$i1 != "小計" || $j1 != "消費税") {

								echo "Invalid Template";

								exit;

						} 

						else {

							$extensiondoc = Input::file('xlfile')->getClientOriginalExtension();

							$fileNamedoc = "invoice".'.'.$extensiondoc;

							move_uploaded_file($_FILES['xlfile']['tmp_name'], $destinationPath.$fileNamedoc);

						}

					});

				if($tmpFile) {

					Session::flash('success', 'Template File Has Been Uploaded Successfully!'); 

					Session::flash('type', 'alert-success'); 

				} else {

					Session::flash('type', 'Template File Has Been Uploaded Unsuccessfully!'); 

					Session::flash('type', 'alert-danger'); 

				}

			}

		}

		$request->mainmenu = "Setting";		

		return Redirect::to('Setting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));

	}

	public static function getExtension($str) {

	    $i = strrpos($str,".");

	    if (!$i) { return ""; }

	    $l = strlen($str) - $i;

	    $ext = substr($str,$i+1,$l);

	    return $ext;

	}

}