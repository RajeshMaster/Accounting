<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Staff;
use App\Model\Visarenew;
use App\Model\Tax;
use DB;
use Input;
use Redirect;
use Session;
use Carbon\Carbon;
use App\Http\eradate;
use Config;
use Auth;
use Excel;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
ini_set('max_execution_time', 0);
ini_set('memory_limit','256M');
Class TaxController extends Controller {
	public function index(Request $request) {
        $i=0;
        $rela_count=0;
        $get_detail = array();
        $excel_details = array();
		//Setting page limit
		if ($request->plimit=="") {
			$request->plimit = 50;
		}
		$employeeDetails = Tax::fnGetEmployeeDetails($request);
        foreach ($employeeDetails as $key => $value) {
            // TO GET RELATION COUNT
                $relation_check=Tax::getfamilyDetails($value->Emp_ID);
                $rela_count =0;
                for($m=2;$m<count($relation_check);$m++) {
                    if($relation_check[$m] != "" && $relation_check[$m] != "/0000-00-00" && $relation_check[$m] != "/") {
                            $rela_count++;
                    }
                }
                $get_detail[$i]['Relation_count'] = $rela_count;
            // END FAMILY RELATION COUNT
            // GET DEPENDS COUNT
                $family_sel_list = Tax::getfamily($value->Emp_ID);
            // END DEPENDS COUNT
                $get_detail[$i]['dep_cou'] = $family_sel_list;
            // CONVERT NORMAL DATE TO JEPANESE ERA DATE FORMAT
                $empjapancalender=eradate::geteradate($value->DOB, 6);
                $empdobdate = explode('/', $empjapancalender);
                if (isset($empdobdate[4])) {
                    $empdobdate[4] = $empdobdate[4];
                } else {
                    $empdobdate[4] = "";
                }
                $get_detail[$i]['DOB'] = $empdobdate[4];
                $get_detail[$i]['date'] = substr($empdobdate[0], 1,2)."-".$empdobdate[1]."-".$empdobdate[2];
            // END CONVERT NORMAL DATE TO JEPANESE ERA DATE FORMAT
            if($value->citizenShip == "1"){
                $get_detail[$i]['citizenShip'] = trans('messages.lbl_india');
            } else {
                $get_detail[$i]['citizenShip'] = trans('messages.lbl_japan');
            }
            $get_detail[$i]['visaStatus'] = $value->JapNM;
            $i++;
        }
		return view('Tax.index',['request' => $request,
                                    'employeeDetails' => $employeeDetails,
                                    'get_detail' => $get_detail]);
	}
	public function taximportpopup(Request $request) {
		//For Get The DataBase List
		$getOldDbDetails = Staff::fnOldDbDetails();
		return view('Tax.taximportpopup',['getOldDbDetails'=> $getOldDbDetails,
										'request' => $request]);
	}
	function importprocess(Request $request) {
        // For Record count
        $employeeTaxcount = Tax::fnGetEmployeeTaxDetails();
		//Get The New DataBase Details
		$getConnectionQuery = Staff::fnGetConnectionQuery($request);
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);
		try {
		$db = DB::connection('otherdb');
		$db->getPdo();
			if($db->getDatabaseName()){
				$employee_count = Tax::fnGetEmployeewithTaxCount();
				$g_val = count($employee_count);
				//To Get The Latest Employee Details In New DataBase
				foreach ($employee_count as $key => $value) {
					$oldUserQuery[$key]=Tax::fnGetEmployeeFamilyDetails($value->Emp_ID);
				}
                if( $g_val > $employeeTaxcount){
				    if ($oldUserQuery) {
						$getOldUserRecordAsArray = array();
						$i = 0;
						$sum = 0;
							foreach ($oldUserQuery as $key => $value) {
								if (isset($value[0]->Emp_ID)) {
									// Start of Insurence total calculation
						            $start = new Carbon('first day of last year');
						            $start->startOfYear();
						            $start = $start->format('Y-m-d');
						            $end = new Carbon('last day of last year');
						            $end = $end->endofYear();
						            $end = $end->format('Y-m-d');
						            $insuranceamount = Tax::getinsuranceamount($value[0]->Emp_ID,$start,$end);
						              	if (isset($insuranceamount[0]->SUM)) {
						                	$sum = $insuranceamount[0]->SUM;
						              	}
						          	// End of Insurence total calculation
						            // Start of Family list Fetch
						            $familyDetailsList = Tax::fnGetFamilyDetails($value[0]->Emp_ID);
						            // End of Family list Fetch
									$importFamilyDetails = Tax::fnInsertEmployeeFamilyDetails($value,$sum,$familyDetailsList);
								}
							}
						Session::flash('success', 'Imported Sucessfully!'); 
						Session::flash('type', 'alert-success');
				    } else {
					   Session::flash('success', 'Record Not Imported Sucessfully'); 
					   Session::flash('type', 'alert-danger'); 
				    }
                } else {
                     Session::flash('success', 'No New Record Found'); 
                     Session::flash('type', 'alert-danger'); 
                }
			} else{
				Session::flash('success', 'Invalid Db Connection'); 
				Session::flash('type', 'alert-danger'); 
			}
		} catch (\Exception $e) {
        	Session::flash('success', 'Invalid Db Connection.'); 
			Session::flash('type', 'alert-danger'); 
    	}
    	return Redirect::to('Tax/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	public static function taxPersonalDownload(Request $request) {
      $template_name = Config::get('constants.TAX_PERSONAL');
      $tempname = $request->empname."_Tax";
      $excel_name=$tempname;
        Excel::load($template_name, function($objTpl) use($request) {
          $japaneseDOBformat="";
          $sum = 0;
          $FatherDOB="";
          $MotherDOB="";
          $GrandFatherDOB="";
          $GrandMotherDOB="";
          $WifeDOB = "";
          $relationname = "";
          $relationkananame = "";
          $WifeDOB = "";
          $familyRelationsArray = array();
          $childrenRelationsArray = array();
          $genderRelationsArray = array();
          $familyslicedArray = array();
          $genderarray = array();
          $family_list = array();
          $m=0;
          $n=0;
          $q=0;
            $topBorder = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => 'FF6600')
                                        ),
                                    )
                                );
            $topBorderThick = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                                        'color' => array('rgb' => 'FF6600')
                                        ),
                                    )
                                );
            $topBorderDoubleLine = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                        'color' => array('rgb' => 'FF6600')
                                        ),
                                    )
                                );
            $leftBorder = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => 'FF6600')
                                        ),
                                    )
                                );
            $leftBorderDottedLine = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOTTED,
                                        'color' => array('rgb' => 'FF6600')
                                        ),
                                    )
                                );
            $rightBorder = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'FF6600')
                        ),
                      )
                );
            $rightBorderNone = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_NONE,
                        'color' => array('rgb' => 'FF6600')
                        ),
                      )
                );
            $fontColorOrange = array(
                                'font'  => array(
                                    'color' => array('rgb' => 'FF6600'),
                                ));
            $rightBorderThick = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => 'FF6600')
                        ),
                      )
                );
            $bottomBorder = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'FF6600')
                        ),
                      )
                );
            $bottomBorderThick = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => 'FF6600')
                        ),
                      )
                );
            $bottomBorderDoubleLine = array(
                                'borders' => array(
                                    'bottom'    => array(
                                    'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => 'FF6600')
                                    ),
                                  )
                                );
            $topBorderGreen = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '008000')
                                        ),
                                    )
                                );
            $topBorderThickGreen = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                                        'color' => array('rgb' => '008000')
                                        ),
                                    )
                                );
            $fontColorGreen = array(
                                'font'  => array(
                                    'color' => array('rgb' => '008000'),
                                ));
            $topBorderDoubleLineGreen = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                        'color' => array('rgb' => '008000')
                                        ),
                                    )
                                );
            $leftBorderGreen = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '008000')
                                        ),
                                    )
                                );
            $leftBorderDottedLineGreen = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOTTED,
                                        'color' => array('rgb' => '008000')
                                        ),
                                    )
                                );
            $rightBorderGreen = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '008000')
                        ),
                      )
                );
            $rightBorderThickGreen = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => '008000')
                        ),
                      )
                );
            $bottomBorderGreen = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '008000')
                        ),
                      )
                );
            $bottomBorderThickGreen = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => '008000')
                        ),
                      )
                );
            $bottomBorderDoubleLineGreen = array(
                                'borders' => array(
                                    'bottom'    => array(
                                    'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '008000')
                                    ),
                                  )
                                );
            $topBorderBlack = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '272822')
                                        ),
                                    )
                                );
            $topBorderThickBlack = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                                        'color' => array('rgb' => '272822')
                                        ),
                                    )
                                );
            $topBorderDoubleLineBlack = array(
                                'borders' => array(
                                    'top'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                        'color' => array('rgb' => '272822')
                                        ),
                                    )
                                );
            $leftBorderBlack = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '272822')
                                        ),
                                    )
                                );
            $leftBorderDottedLineBlack = array(
                                'borders' => array(
                                    'left'    => array(
                                        'style' =>PHPExcel_Style_Border::BORDER_DOTTED,
                                        'color' => array('rgb' => '272822')
                                        ),
                                    )
                                );
            $rightBorderBlack = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '272822')
                        ),
                      )
                );
            $rightBorderThickBlack = array(
                'borders' => array(
                      'right'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => '272822')
                        ),
                      )
                );
            $bottomBorderBlack = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '272822')
                        ),
                      )
                );
            $bottomBorderThickBlack = array(
                'borders' => array(
                      'bottom'    => array(
                        'style' =>PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('rgb' => '272822')
                        ),
                      )
                );
            $bottomBorderDoubleLineBlack = array(
                                'borders' => array(
                                    'bottom'    => array(
                                    'style' =>PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '272822')
                                    ),
                                  )
                                );
            $rightborderArray = array("0"=>"G5",
                                    "1"=>"I5",
                                    "2"=>"K5",
                                    "3"=>"M5",
                                    "4"=>"O5",
                                    "5"=>"Q5",
                                    "6"=>"S5",
                                    "7"=>"U5",
                                    "8"=>"W5",
                                    "9"=>"Y5",
                                    "10"=>"AA5",
                                    "11"=>"AC5",
                                    "12"=>"AE5",
                                    "13"=>"AG5",
                                    "14"=>"AI5",
                                    "15"=>"AK5",
                                    "16"=>"AM5",
                                    "17"=>"J6",
                                    "18"=>"I32",
                                    "19"=>"I33",
                                    "20"=>"I35",
                                    "21"=>"I36",
                                    "22"=>"I38",
                                    "23"=>"I39",
                                    "24"=>"I40",
                                    "25"=>"I41",
                                    "26"=>"I44",
                                    "27"=>"I45",
                                    "28"=>"AN35",
                                    "29"=>"AN36",
                                    "30"=>"AN38",
                                    "31"=>"AN39",
                                    "32"=>"AN41",
                                    "33"=>"AN42",
                                    "34"=>"AN44",
                                    "35"=>"AN45",
                                    "36"=>"J7",
                                    "37"=>"AR9",
                                    "38"=>"W28",
                                    "39"=>"W30",
                                    "40"=>"AG32",
                                    "41"=>"BL35",
                                    "42"=>"BL38",
                                    "43"=>"BL44",
                                    "44"=>"CD5",
                                    "45"=>"CF5",
                                    "46"=>"CH5",
                                    "47"=>"CJ5",
                                    "48"=>"CL5",
                                    "49"=>"CN5",
                                    "50"=>"CP5",
                                    "51"=>"CR5",
                                    "52"=>"CT5",
                                    "53"=>"CV5",
                                    "54"=>"CX5",
                                    "55"=>"CZ5",
                                    "56"=>"DB5",
                                    "57"=>"DD5",
                                    "58"=>"DF5",
                                    "59"=>"DH5",
                                    "60"=>"",
                                    "61"=>"CP14",
                                    "62"=>"EH17",
                                    "63"=>"EH18",
                                    "64"=>"CD26",
                                    "65"=>"CH30",
                                    "66"=>"CG32",
                                    "67"=>"CG35",
                                    "68"=>"CG36",
                                    "69"=>"CG37",
                                    "70"=>"DC32",
                                    "71"=>"DC35",
                                    "72"=>"DC38",
                                    "73"=>"DC41",
                                    "74"=>"DC44",
                                    "75"=>"EJ44",
                                    "76"=>"DE32",
                                    "77"=>"CG33",
                                    "78"=>"CG45",
                                    "79"=>"AO73",
                                    "80"=>"AO74",
                                    "81"=>"AH69",
                                    "82"=>"AH70",
                                    "83"=>"AN70",
                                    "84"=>"AY69",
                                    "85"=>"AY70",
                                    "86"=>"BN69",
                                    "87"=>"BN70",
                                    "88"=>"J78",
                                    "89"=>"W78",
                                    "90"=>"AW78",
                                    "91"=>"BJ78",
                                    "92"=>"AJ78",
                                    "93"=>"J80",
                                    "94"=>"W80",
                                    "95"=>"W82",
                                    "96"=>"AV80",
                                    "97"=>"AV82",
                                    "98"=>"BB80",
                                    "99"=>"BB82",
                                    "100"=>"I84",
                                    "101"=>"I85",
                                    "102"=>"AK84",
                                    "103"=>"AW84",
                                    "104"=>"AN89",
                                    "105"=>"BI88",
                                    "106"=>"BI91",
                                    "107"=>"BI94",
                                    "108"=>"BJ96",
                                    "109"=>"AV107",
                                    "110"=>"AY58",
                                    "111"=>"AY60",
                                    "112"=>"BL61",
                                    "113"=>"BL62",
                                    "114"=>"BU64",
                                    "115"=>"BI65",
                                    "116"=>"BR66",
                                    "117"=>"BR69",
                                    "118"=>"BR70",
                                    "119"=>"BF72",
                                    "120"=>"BF73",
                                    "121"=>"BF74",
                                    "122"=>"BF75",
                                    "123"=>"BU78",
                                    "124"=>"BP79",
                                    "125"=>"BU80",
                                    "126"=>"BJ83",
                                    "127"=>"BU84",
                                    "128"=>"BP85",
                                    "129"=>"BP87",
                                    "130"=>"BP88",
                                    "131"=>"BP89",
                                    "132"=>"BP90",
                                    "133"=>"BP94",
                                    '135'=>'CQ63',
                                    '136'=>'DB66',
                                    '137'=>'DB67',
                                    '138'=>'DI68',
                                    '139'=>'DS68',
                                    '140'=>'DF69',
                                    '141'=>'DF70',
                                    '142'=>'CD70',
                                    '143'=>'DP69',
                                    '144'=>'DP70',
                                    '145'=>'DW69',
                                    '146'=>'DW70',
                                    '147'=>'EL69',
                                    '148'=>'EL70',
                                    '149'=>'CH78',
                                    '150'=>'CH80',
                                    '151'=>'CH82',
                                    '152'=>'DT80',
                                    '153'=>'CG91',
                                    '154'=>'DZ80',
                                    '155'=>'CU78',
                                    '156'=>'CU80',
                                    '157'=>'CU82',
                                    '158'=>'CG84',
                                    '159'=>'CG85',
                                    '160'=>'DI84',
                                    '161'=>'DU84',
                                    '162'=>'CG87',
                                    '163'=>'CG88',
                                    '164'=>'CG90',
                                    '165'=>'CG93',
                                    '166'=>'CG94',
                                    '167'=>'CG97',
                                    '168'=>'DL87',
                                    '169'=>'DL88',
                                    '170'=>'DL90',
                                    '171'=>'DL91',
                                    '172'=>'DL93',
                                    '173'=>'DL94',
                                    '174'=>'DL97',
                                    '175'=>'DB91',
                                    '176'=>'DB94',
                                    '177'=>'EG88',
                                    '178'=>'EG91',
                                    '179'=>'EG94',
                                    '180'=>'EH96',
                                    '181'=>'EH96',
                                    '182'=>'ED106',
                                    '183'=>'N75',
                                    '184'=>'BJ81',
                                    '185'=>'',
                                    '186'=>'BU82',
                                    '187'=>'DW58',
                                    '188'=>'DF59',
                                    '189'=>'DW60',
                                    '190'=>'EJ61',
                                    '191'=>'EJ62',
                                    '192'=>'ES64',
                                    '193'=>'EG65',
                                    '194'=>'EP66',
                                    '195'=>'EP69',
                                    '196'=>'EP70',
                                    '197'=>'ED73',
                                    '198'=>'ED74',
                                    '199'=>'CL75',
                                    '200'=>'ES78',
                                    '201'=>'EN79',
                                    '202'=>'ES80',
                                    '203'=>'EH81',
                                    '204'=>'ES82',
                                    '205'=>'EH83',
                                    '206'=>'ES84',
                                    '207'=>'EN85',
                                    '208'=>'EN87',
                                    '209'=>'CG104',
                                    '210'=>'DT82',
                                    '211'=>'DZ82',
                                    '212'=>'DP59',
                                    '213'=>'AU68',
                                    '214'=>'I87',
                                    '215'=>'I88',
                                    '216'=>'I90',
                                    '217'=>'I91',
                                    '218'=>'I93',
                                    '219'=>'I94',
                                    '220'=>'I97',
                                    '221'=>'AD91',
                                    '222'=>'AD94',
                                    '223'=>'BF106',
                                    );
            $leftborderArray = array("0"=>"J6",
                                    "1"=>"BI28",
                                    "2"=>"BP39",
                                    "3"=>"BP41",
                                    "4"=>"BP36",
                                    "5"=>"BP38",
                                    "6"=>"BP44",
                                    "7"=>"BP45",
                                    "8"=>"DB7",
                                    "9"=>"DB9",
                                    "10"=>"DS10",
                                    "11"=>"DS12",
                                    "12"=>"DS13",
                                    "13"=>"DF7",
                                    "14"=>"DF9",
                                    "15"=>"BB66",
                                    "16"=>"BB69",
                                    "17"=>"BB70",
                                    );
            $bottomborderArray = array('0'=>"AU13",
                                        "1"=>"P28",
                                        "2"=>"AW32",
                                        "3"=>"BC33",
                                        "4"=>"BJ32",
                                        "5"=>"AG32",
                                        "6"=>"AG35",
                                        "7"=>"AG38",
                                        "8"=>"AG41",
                                        "9"=>"AG44",
                                        "10"=>"BL35",
                                        "11"=>"BL38",
                                        "12"=>"BL41",
                                        "13"=>"U33",
                                        "14"=>"U36",
                                        "15"=>"U39",
                                        "16"=>"U42",
                                        "17"=>"U45",
                                        "18"=>"AE32",
                                        "19"=>"AE35",
                                        "20"=>"AE38",
                                        "21"=>"AE44",
                                        "22"=>"AE41",
                                        "23"=>"AZ36",
                                        "24"=>"AZ39",
                                        "25"=>"AZ42",
                                        "26"=>"BJ35",
                                        "27"=>"BJ38",
                                        "27"=>"BJ41",
                                        "28"=>"AM47",
                                        "29"=>"F52",
                                        "30"=>"CD12",
                                        "31"=>"EP18",
                                        "32"=>"DW18",
                                        "33"=>"DZ18",
                                        "34"=>"DP18",
                                        "35"=>"DF18",
                                        "36"=>"CH18",
                                        "37"=>"DH26",
                                        "38"=>"DU32",
                                        "39"=>"CD47",
                                        "40"=>"CG47",
                                        "41"=>"CJ47",
                                        "42"=>"CM47",
                                        "43"=>"CP47",
                                        "44"=>"DK49",
                                        "45"=>"DN49",
                                        "46"=>"DQ49",
                                        "47"=>"DT49",
                                        "48"=>"DW49",
                                        "49"=>"AO80",
                                        "50"=>"AV80",
                                        "51"=>"BB80",
                                        "52"=>"CH80",
                                        "53"=>"CN80",
                                        "54"=>"CU80",
                                        "55"=>"DM80",
                                        "56"=>"DT80",
                                        "57"=>"DZ80",
                                        "58"=>"J80",
                                        "59"=>"P80",
                                        "60"=>"W80",
                                        );
            $rightThickBorderArray = array('0'=>'AG12',
                                            '1'=>'AG13',
                                            '2'=>'F17',
                                            '3'=>'AD17',
                                            '4'=>'AN17',
                                            '5'=>'AU17',
                                            '6'=>'BN17',
                                            '7'=>'F18',
                                            '8'=>'AD18',
                                            '9'=>'AN18',
                                            '10'=>'AU18',
                                            '11'=>'BN18',
                                            '12'=>'AM47',
                                            '13'=>'AY49',
                                            '14'=>'F47',
                                            '15'=>'F51',
                                            '16'=>'AY51',
                                            "17"=>"DE12",
                                            "18"=>"DE13",
                                            "19"=>"CD17",
                                            "20"=>"CD18",
                                            "21"=>"DB17",
                                            "22"=>"DB18",
                                            "23"=>"DL17",
                                            "24"=>"DL18",
                                            "25"=>"DS17",
                                            "26"=>"DS18",
                                            "27"=>"EL18",
                                            "28"=>"EL17",
                                            "29"=>"DM21",
                                            "30"=>"DM22",
                                            "31"=>"CS26",
                                            "32"=>"CN27",
                                            "33"=>"DF26",
                                            "34"=>"DA27",
                                            "35"=>"EF26",
                                            "36"=>"DN27",
                                            "37"=>"DS26",
                                            "38"=>"EA27",
                                            "39"=>"DS32",
                                            "40"=>"DN33",
                                            );
            $topborderArray = array('0'=>'AH17',
                                    '1'=>'AR17',
                                    '2'=>'AY17',
                                    '3'=>'BR17',
                                    '4'=>'F20',
                                    '5'=>'X20',
                                    '6'=>'AO20',
                                    '7'=>'BF20',
                                    '8'=>'N35',
                                    '9'=>'W35',
                                    '10'=>'N38',
                                    '11'=>'W38',
                                    '12'=>'AS38',
                                    '13'=>'BB38',
                                    '14'=>'BL38',
                                    '15'=>'N41',
                                    '16'=>'W41',
                                    '17'=>'AS41',
                                    '18'=>'BB41',
                                    '19'=>'BL41',
                                    '20'=>'N44',
                                    '21'=>'W44',
                                    '22'=>'AS44',
                                    '23'=>'BB44',
                                    '24'=>'BL44',
                                    '25'=>'AG35',
                                    '26'=>'AG38',
                                    '27'=>'AG41',
                                    '28'=>'AG44',
                                    '29'=>'U35',
                                    '30'=>'U38',
                                    '31'=>'U41',
                                    '32'=>'U44',
                                    '33'=>'AE35',
                                    '34'=>'AE38',
                                    '35'=>'AE41',
                                    '36'=>'AE44',
                                    '37'=>'AZ38',
                                    '38'=>'AZ41',
                                    '39'=>'AZ44',
                                    '40'=>'BJ38',
                                    '41'=>'BJ41',
                                    '42'=>'BJ44',
                                    '43'=>'BE49',
                                    '44'=>'BB49',
                                    '45'=>'DU32',
                                    '46'=>'DQ51',
                                    '47'=>'CQ63',
                                    );
            $bottomThickBorderArray = array('0'=>'F18',
                                            '1'=>'N18',
                                            '2'=>'R18',
                                            '3'=>'AD18',
                                            '4'=>'AN18',
                                            '5'=>'AK18',
                                            '6'=>'AU18',
                                            '7'=>'BF18',
                                            '8'=>'BJ18',
                                            '9'=>'BN18',
                                            '10'=>'O27',
                                            '11'=>'AB27',
                                            '12'=>'AP33',
                                            '13'=>'BP33',
                                            '14'=>'CD18',
                                            '15'=>'CL18',
                                            '16'=>'CP18',
                                            '17'=>'DB18',
                                            '18'=>'DI18',
                                            '19'=>'DL18',
                                            '20'=>'DS18',
                                            '21'=>'ED18',
                                            '22'=>'EH18',
                                            '23'=>'EL18',
                                            '24'=>'CM27',
                                            '25'=>'CZ27',
                                            );
            $topBorderDoubleLineArray = array('0'=>'AI28',
                                            '1'=>'AV28',
                                            '2'=>'F32',
                                            '3'=>'I32',
                                            '4'=>'M32',
                                            '5'=>'BD32',
                                            '6'=>'BE32',
                                            '7'=>'BF32',
                                            '8'=>'BG32',
                                            '9'=>'BH32',
                                            '10'=>'DU32');
            $topBorderThickArray = array("0"=>"EN32",
                                        "1"=>"EO32",
                                        "2"=>"EP32",
                                        "3"=>"EQ32",
                                        "4"=>"ER32",
                                        "5"=>"ES32");
            $bottomBorderDoubleLineArray = array("0" => "CD28",
                                                "1" => "CH30",
                                                "2" => "CU30",
                                                "3" => "DZ30",
                                                "4" => "CD80",
                                                "5" => "CH82",
                                                "6" => "CU82",
                                                "7" => "DM82",
                                                "8" => "DT82",
                                                "9" => "DZ82",
                                                "10" => "F80",
                                                "11" => "J82",
                                                "12" => "P83",
                                                "13" => "W82",
                                                "14" => "AO82",
                                                "15" => "AV82",
                                                "16" => "BB82",
                                                "17" => "P83",
                                                "18" => "BJ83",
                                                "19" => "BI83",
                                                "20" => "AC83",
                                                "21" => "AF83",
                                                "22" => "AG83",
                                                "23" => "AH83",
                                                "24" => "AJ83",
                                                "25" => "AK83",
                                                "26" => "AL83",
                                                "27" => "AN83",
                                                );
            $textWrapArray = array('0'=>'AY9','1'=>'AY10','2'=>'BL9','3'=>'BL10',
                                    '4'=>'AY61','5'=>'AY62','6'=>'BL61','7'=>'BL62',
                                    '8'=>'DW9','9'=>'DW10','10'=>'EJ9','11'=>'EJ10',
                                    '12'=>'DW61','13'=>'DW62','14'=>'BL61','15'=>'BL62',);
          $Relation_kanji = array('Father' => '父','Mother' => '母','GrandFather' =>'祖父','GrandMother' =>'祖母', 'ElderBrother' => '兄','ElderSister'=>'姉','YoungerBrother'=>'弟','YoungerSister'=>'妹','others'=>'その他');
          $genderstatus = array('Wife' => '妻', 'Husband' => '夫','Child' => '子');
          $livingstatus = array('livetogether' => '同居', 'outsider' => '直系以外');
          $family = array('','','Father','Mother','GrandFather','GrandMother','ElderBrother1','ElderBrother2',
                      'ElderBrother3','ElderBrother4','ElderBrother5','ElderBrother6','ElderSister1',
                      'ElderSister2','ElderSister3','ElderSister4','ElderSister5','ElderSister6');
          $unselectArray = array();
          // Start of Employee Details Fetch
          $employeeDetails = Tax::fnGetEmployeeDownload($request);
          // End of Employee Details Fetch
          // Excel flag display update
          $checkflgdata = Tax::fnInsertCheckflagData($request,1);
          // Array selection for family after marriage
            if ($employeeDetails[0]->Gender == 1) {
              $genderarray = array('Wife');
            } else if ($employeeDetails[0]->Gender == 2) {
              $genderarray = array('Husband');
            }
            $childernarray = array('Children1', 'Children2', 'Children3', 'Children4');
          // Family Details Selection
          $family_sel_list=Tax::getfamilyArray($request->empid);
          if ($family_sel_list != "") {
              $family_list=Tax::selectfamilylist($family_sel_list,$request->empid);
              // Taking the count of 4 for any family Members
              // $familyslicedArray = array_slice($family_list,0,4);
          }
          // Working on DOB
            $DOB = "";
            if ($family_list == 0) {
              $family_list = array();
            }
            foreach ($family_list as $key => $value) {
                if ($value[4] == "Children") {
                  $childrenRelationsArray[$q]['name'] = $value[0];
                  $childrenRelationsArray[$q]['kananame'] = $value[3];
                  if ($value[2] != "0000-00-00" || $value[2] != null || $value[2] != "") {
                      $DOBDB = explode('-', $value[2]);
                      if (isset($DOBDB[1])) {
                        $DOB = $DOBDB[0].'/'.$DOBDB[1].'/'.$DOBDB[2];
                      }
                  }
                  $childrenRelationsArray[$q]['dateofbirth'] = $DOB;
                  $childrenRelationsArray[$q]['relationtype'] = '子';
                  $childrenRelationsArray[$q]['childlivestype'] = '同居';
                  $q++; 
                }  else if ($value[4] == "Wife" || $value[4] == "Husband") {
                  $genderRelationsArray[$n]['name'] = $value[0];
                  $genderRelationsArray[$n]['kananame'] = $value[3];
                  if ($value[2] != "0000-00-00" || $value[2] != null || $value[2] != "") {
                      $DOBDB = explode('-', $value[2]);
                      if (isset($DOBDB[1])) {
                        $DOB = $DOBDB[0].'/'.$DOBDB[1].'/'.$DOBDB[2];
                      }
                  }
                  $genderRelationsArray[$n]['dateofbirth'] = $DOB;
                  $genderRelationsArray[$n]['relationtype'] = $value[4];
                  $n++; 
                } else {
                  $familyRelationsArray[$m]['name'] = $value[0];
                  $familyRelationsArray[$m]['kananame'] = $value[3];
                  if ($value[2] != "0000-00-00" || $value[2] != null || $value[2] != "") {
                      $DOBDB = explode('-', $value[2]);
                      if (isset($DOBDB[1])) {
                        $DOB = $DOBDB[0].'/'.$DOBDB[1].'/'.$DOBDB[2];
                      }
                  }
                  $familyRelationsArray[$m]['dateofbirth'] = $DOB;
                  $familyRelationsArray[$m]['relationtype'] = $value[4];
                  $m++;
                }
            }
          // Assigning Employee First and Last names
          $objTpl->getActiveSheet()->setCellValue("E11",('6  1200  0206  7788'));
          $objTpl->getActiveSheet()->setCellValue("M11",($employeeDetails[0]->KanaFirstName));
          $objTpl->getActiveSheet()->getStyle("M11")->getAlignment()->setWrapText(true);
          $objTpl->getActiveSheet()->setCellValue("N11",($employeeDetails[0]->KanaLastName));
          $objTpl->getActiveSheet()->getStyle("N11")->getAlignment()->setWrapText(true);
          $objTpl->getActiveSheet()->setCellValue("M12",($employeeDetails[0]->FirstName));
          $objTpl->getActiveSheet()->getStyle("M12")->getAlignment()->setWrapText(true);
          $objTpl->getActiveSheet()->setCellValue("N12",($employeeDetails[0]->LastName));
          $objTpl->getActiveSheet()->getStyle("N12")->getAlignment()->setWrapText(true);
          // Assign Family details for the Employee
          $familycellval = 17;
          if (!empty($familyRelationsArray)) {
              $familyslicedArray = array_slice($familyRelationsArray,0,4);
              foreach ($familyslicedArray as $key => $value) {
                  $objTpl->getActiveSheet()->setCellValue('D'.($familycellval + $key),( $value['name'] ));
                  $objTpl->getActiveSheet()->getStyle('D'.($familycellval + $key))->getAlignment()->setWrapText(true);
                  $objTpl->getActiveSheet()->getStyle('E'.($familycellval + $key))->getAlignment()->setWrapText(true);
                  $objTpl->getActiveSheet()->setCellValue('F'.($familycellval + $key),( $value['kananame'] ));
                  $objTpl->getActiveSheet()->getStyle('F'.($familycellval + $key))->getAlignment()->setWrapText(true);
                  $objTpl->getActiveSheet()->getStyle('G'.($familycellval + $key))->getAlignment()->setWrapText(true);
                  $objTpl->getActiveSheet()->setCellValue('J'.($familycellval + $key),( $value['dateofbirth'] ));
                  $objTpl->getActiveSheet()->setCellValue('I'.($familycellval + $key),( $Relation_kanji[$value['relationtype']] ));
                  $objTpl->getActiveSheet()->setCellValue('L'.($familycellval + $key),( $livingstatus['outsider'] ));
                  $objTpl->getActiveSheet()->setCellValue('P'.($familycellval + $key),( '○' ));
              }
          }
          // Assigning Wife or Husband names
            if (!empty($genderRelationsArray)) {
              $objTpl->getActiveSheet()->setCellValue("D16",($genderRelationsArray[0]['name']));
              $objTpl->getActiveSheet()->getStyle("D16")->getAlignment()->setWrapText(true);
              $objTpl->getActiveSheet()->getStyle("E16")->getAlignment()->setWrapText(true);
              $objTpl->getActiveSheet()->setCellValue("F16",($genderRelationsArray[0]['kananame']));
              $objTpl->getActiveSheet()->getStyle("F16")->getAlignment()->setWrapText(true);
              $objTpl->getActiveSheet()->getStyle("G16")->getAlignment()->setWrapText(true);
              $objTpl->getActiveSheet()->setCellValue("J16",($genderRelationsArray[0]['dateofbirth']));
              $objTpl->getActiveSheet()->setCellValue("L16",($livingstatus['livetogether']));
                        if ($employeeDetails[0]->Gender == 1) {
                            $objTpl->getActiveSheet()->setCellValue("I16",($genderstatus['Wife']));
                        } elseif ($employeeDetails[0]->Gender == 2) {
                            $objTpl->getActiveSheet()->setCellValue("I16",($genderstatus['Husband']));
                        }
            }
          // Assigning Childrens
            $cellval = 23;
            if (!empty($childrenRelationsArray)) {
                foreach ($childrenRelationsArray as $childkey => $childvalue) {
                    $objTpl->getActiveSheet()->setCellValue('D'.($cellval + $childkey),($childvalue['name']));
                    $objTpl->getActiveSheet()->getStyle('D'.($cellval + $childkey))->getAlignment()->setWrapText(true);
                    $objTpl->getActiveSheet()->getStyle('E'.($cellval + $childkey))->getAlignment()->setWrapText(true);
                    $objTpl->getActiveSheet()->setCellValue('F'.($cellval + $childkey),($childvalue['kananame']));
                    $objTpl->getActiveSheet()->getStyle('F'.($cellval + $childkey))->getAlignment()->setWrapText(true);
                    $objTpl->getActiveSheet()->getStyle('G'.($cellval + $childkey))->getAlignment()->setWrapText(true);
                    $objTpl->getActiveSheet()->setCellValue('J'.($cellval + $childkey),($childvalue['dateofbirth']));
                    $objTpl->getActiveSheet()->setCellValue('I'.($cellval + $childkey),($childvalue['relationtype']));
                    $objTpl->getActiveSheet()->setCellValue('L'.($cellval + $childkey),($childvalue['childlivestype']));
                }
            }
            $companyID = $objTpl->getActiveSheet()->getCell('E11')->getValue();
            $text = str_replace(' ', '', $companyID);
            $objTpl->getActiveSheet()->setCellValue("E11",($text));
            // Assining Address
            $objTpl->getActiveSheet()->setCellValue("M13",($employeeDetails[0]->full_address));
            $empDOBDB = explode('-', $employeeDetails[0]->DOB);
            $EmpDOB = $empDOBDB[0].'/'.$empDOBDB[1].'/'.$empDOBDB[2];
            // Assing Japanese Date of birth format
            $objTpl->getActiveSheet()->setCellValue("O12",($EmpDOB));
            // Start of Insurence total calculation
            $sum = $employeeDetails[0]->empInsurence;
            // End of Insurence total calculation
            // Salary Calculation for Employees
                $previousYear = new Carbon('first day of last year');
                $previousYear->startOfYear();
                $previousYear = $previousYear->format('Y');
                $employeeSalaryData = Tax::fnFetchSalaryDetails($request->empid,$previousYear);
                if(isset($employeeSalaryData[0])) {
                    $objTpl->getActiveSheet()->setCellValue("V20",($employeeSalaryData[0]->Total));
                    $objTpl->getActiveSheet()->getStyle('V20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }
            // End of Salary Calculation for Employees
            // Salary Tax Calculation for employees
                $employeeTax = Tax::fnFetchTaxDetails();
            // End of Salary Tax Calculation for Employees
            // Assinging health Inssurence 
            $objTpl->getActiveSheet()->setCellValue("P35",($sum));
            $objTpl->getActiveSheet()->getStyle('P35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objTpl->setActiveSheetIndex(1);
            for ($i=0; $i < count($rightborderArray); $i++) {
                if(isset($rightborderArray[$i])) {
                if($rightborderArray[$i] !=""){
                  if($i<=43) {
                    $objTpl->getActiveSheet()->getStyle($rightborderArray[$i])->applyFromArray($rightBorder);
                  } 
                  if ($i >= 44 && $i<=78) {
                    $objTpl->getActiveSheet()->getStyle($rightborderArray[$i])->applyFromArray($rightBorderGreen);
                  }
                  if ($i >= 79) {
                    $objTpl->getActiveSheet()->getStyle($rightborderArray[$i])->applyFromArray($rightBorderBlack);
                  }
                  }
                }
                }
            for ($k=0; $k < count($leftborderArray); $k++) {
                if($k<=7) {
                $objTpl->getActiveSheet()->getStyle($leftborderArray[$k])->applyFromArray($leftBorder);
                }
                if($k>=8 && $k<=14){
                $objTpl->getActiveSheet()->getStyle($leftborderArray[$k])->applyFromArray($leftBorderGreen);
                }
                if($k>=15){
                $objTpl->getActiveSheet()->getStyle($leftborderArray[$k])->applyFromArray($leftBorderBlack);
                }
            }
            for ($l=0; $l < count($bottomborderArray); $l++) {
                if($l<=29) {
                $objTpl->getActiveSheet()->getStyle($bottomborderArray[$l])->applyFromArray($bottomBorder);
                }
                if($l>=30 && $l<=48){
                $objTpl->getActiveSheet()->getStyle($bottomborderArray[$l])->applyFromArray($bottomBorderGreen);
                }
                if($l>=49){
                $objTpl->getActiveSheet()->getStyle($bottomborderArray[$l])->applyFromArray($bottomBorderBlack);
                }
            }
            for ($m=0; $m < count($rightThickBorderArray); $m++) {
                if($m<=16) { 
                $objTpl->getActiveSheet()->getStyle($rightThickBorderArray[$m])->applyFromArray($rightBorderThick);
                }
                if($m>=17){
                $objTpl->getActiveSheet()->getStyle($rightThickBorderArray[$m])->applyFromArray($rightBorderThickGreen);
                }
            }
            for ($n=0; $n < count($topborderArray); $n++) {
                if($n<=44){
                $objTpl->getActiveSheet()->getStyle($topborderArray[$n])->applyFromArray($topBorder);
                }
                if($n>=45){
                $objTpl->getActiveSheet()->getStyle($topborderArray[$n])->applyFromArray($topBorder);
                }
            }
            for ($p=0; $p < count($bottomThickBorderArray); $p++) {
                if($p<=13){
                $objTpl->getActiveSheet()->getStyle($bottomThickBorderArray[$p])->applyFromArray($bottomBorderThick);
                }
                if($p>=14){
                $objTpl->getActiveSheet()->getStyle($bottomThickBorderArray[$p])->applyFromArray($bottomBorderThickGreen);
                }
            }
            for ($q=0; $q < count($topBorderDoubleLineArray); $q++) {
                if($q<=9){
                $objTpl->getActiveSheet()->getStyle($topBorderDoubleLineArray[$q])->applyFromArray($topBorderDoubleLine);
                }
                if($q>=10){
                $objTpl->getActiveSheet()->getStyle($topBorderDoubleLineArray[$q])->applyFromArray($topBorderDoubleLineGreen);
                }
            } 
            for ($r=0; $r < count($topBorderThickArray); $r++) {
                $objTpl->getActiveSheet()->getStyle($topBorderThickArray[$r])->applyFromArray($topBorderThick);
            }
            for ($s=0; $s < count($bottomBorderDoubleLineArray); $s++) {
                if($s<=3){
                $objTpl->getActiveSheet()->getStyle($bottomBorderDoubleLineArray[$s])->applyFromArray($bottomBorderDoubleLineGreen);
                }
                if($s>=4) {
                $objTpl->getActiveSheet()->getStyle($bottomBorderDoubleLineArray[$s])->applyFromArray($bottomBorderDoubleLineBlack); 
                }
            }
            // Applying border for single border missing fields
            $objTpl->getActiveSheet()->getStyle('I42')->applyFromArray($rightBorder);
            $objTpl->getActiveSheet()->getStyle('X22')->applyFromArray($rightBorderThick);
            $objTpl->getActiveSheet()->getStyle('X21')->applyFromArray($rightBorderThick);
            $objTpl->getActiveSheet()->getStyle('BP88')->applyFromArray($topBorderBlack);
            $objTpl->getActiveSheet()->getStyle('P83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('BJ83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('BI83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AC83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AF83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AG83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AH83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AJ83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AK83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AL83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('AN83')->applyFromArray($bottomBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('EA33')->applyFromArray($rightBorderGreen);
            $objTpl->getActiveSheet()->getStyle('EF32')->applyFromArray($rightBorderGreen);
            $objTpl->getActiveSheet()->getStyle('DJ5')->applyFromArray($rightBorderGreen);
            $objTpl->getActiveSheet()->getStyle('EF32')->applyFromArray($rightBorderGreen);
            $objTpl->getActiveSheet()->getStyle('EA33')->applyFromArray($rightBorderGreen);
            $objTpl->getActiveSheet()->getStyle('DI32')->applyFromArray($topBorderDoubleLineBlack);
            $objTpl->getActiveSheet()->getStyle('CN28')->applyFromArray($bottomBorderGreen);
            $objTpl->getActiveSheet()->getStyle('DU26')->applyFromArray($bottomBorderDoubleLineGreen);
            $objTpl->getActiveSheet()->getStyle('DH26')->applyFromArray($bottomBorderDoubleLineGreen);
            $objTpl->getActiveSheet()->getStyle('DP7')->applyFromArray($leftBorderGreen);
            $objTpl->getActiveSheet()->getStyle('DP9')->applyFromArray($leftBorderGreen);
            $objTpl->getActiveSheet()->getStyle('DI28')->applyFromArray($leftBorderDottedLineGreen);
            $objTpl->getActiveSheet()->getStyle('DI30')->applyFromArray($leftBorderDottedLineGreen);
            $objTpl->getActiveSheet()->getStyle('DN33')->applyFromArray($bottomBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('CM27')->applyFromArray($bottomBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('CZ27')->applyFromArray($bottomBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('DF35')->applyFromArray($topBorder);
            $objTpl->getActiveSheet()->getStyle('DQ35')->applyFromArray($topBorder);
            $objTpl->getActiveSheet()->getStyle('S63')->applyFromArray($topBorderBlack);
            $objTpl->getActiveSheet()->getStyle('CQ63')->applyFromArray($topBorderBlack);
            $objTpl->getActiveSheet()->getStyle('AK28')->applyFromArray($leftBorderDottedLine);
            $objTpl->getActiveSheet()->getStyle('AK30')->applyFromArray($leftBorderDottedLine);
            $objTpl->getActiveSheet()->getStyle('CD63')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('F63')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('J82')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('BF106')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('R54')->applyFromArray($rightBorderNone);
            $objTpl->getActiveSheet()->getStyle('CP54')->applyFromArray($rightBorderNone);
            $objTpl->getActiveSheet()->getStyle('R106')->applyFromArray($rightBorderNone);
            $objTpl->getActiveSheet()->getStyle('CP106')->applyFromArray($rightBorderNone);
            // Double line and dark border adjustment
            $objTpl->getActiveSheet()->getStyle('J26')->applyFromArray($bottomBorderDoubleLine);
            $objTpl->getActiveSheet()->getStyle('J28')->applyFromArray($topBorderThick);
            $objTpl->getActiveSheet()->getStyle('W26')->applyFromArray($bottomBorderDoubleLine);
            $objTpl->getActiveSheet()->getStyle('W28')->applyFromArray($topBorderThick);
            $objTpl->getActiveSheet()->getStyle('AW26')->applyFromArray($bottomBorderDoubleLine);
            $objTpl->getActiveSheet()->getStyle('AV28')->applyFromArray($topBorderThick);
            $objTpl->getActiveSheet()->getStyle('CH26')->applyFromArray($bottomBorderDoubleLineGreen);
            $objTpl->getActiveSheet()->getStyle('CH28')->applyFromArray($topBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('CU26')->applyFromArray($bottomBorderDoubleLineGreen);
            $objTpl->getActiveSheet()->getStyle('CU28')->applyFromArray($topBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('AI28')->applyFromArray($topBorderThick);
            $objTpl->getActiveSheet()->getStyle('AJ26')->applyFromArray($bottomBorderDoubleLine);
            // Dark border left and right sides
            $objTpl->getActiveSheet()->getStyle('AK68')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('BJ31')->applyFromArray($bottomBorderThick);
            $objTpl->getActiveSheet()->getStyle('BJ32')->applyFromArray($topBorderDoubleLine);
            $objTpl->getActiveSheet()->getStyle('EH31')->applyFromArray($bottomBorderThickGreen);
            $objTpl->getActiveSheet()->getStyle('EH32')->applyFromArray($topBorderDoubleLineGreen);
            // Text Wrap for names fields
            for ($t=0; $t < count($textWrapArray); $t++) { 
                $objTpl->getActiveSheet()->getStyle($textWrapArray[$t])->getAlignment()->setWrapText(true);
            }
            $objTpl->setActiveSheetIndex(0);
            $objTpl->getActiveSheet()->getStyle('M13')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet()->getStyle('E12')->applyFromArray($rightBorderBlack);
            $objTpl->getActiveSheet(0)->setSelectedCells('A1');
            $flpath='.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$flpath.'"');
            header('Cache-Control: max-age=0');
        })->setFilename($excel_name)->download('xls');
  	}
    public function taxview(Request $request) {
        $family = array('','','Father','Mother','GrandFather','GrandMother','ElderBrother1','ElderBrother2',
                      'ElderBrother3','ElderBrother4','ElderBrother5','ElderBrother6','ElderSister1',
                      'ElderSister2','ElderSister3','ElderSister4','ElderSister5','ElderSister6',
                      'Wife', 'Children1', 'Children2', 'Children3', 'Children4');
        $unselectArray = array();
        $selectedArray = array();
        $family_listselected = array();
        $family_listunselected = array();
        if(Session::get('empid') != ""){
            $request->empid = Session::get('empid');
        }
        if($request->empid == ""){
          return $this->index($request);
        }
        $tax_result = Tax::fnGettaxviewDetails($request->empid);
        if ($tax_result[0]->Gender == 2) {
            $family[18] = "Husband";
        }
        $family_sel_list=Tax::getfamilyArray($tax_result[0]->Emp_ID);
        for ($se=0; $se < count($family_sel_list); $se++) { 
            $removeinteger = preg_replace('/[0-9]+/', '', $family_sel_list[$se]);
            $selectedArray[$se] = $removeinteger;
        }
        $emp_unsel_pgmlng=Tax::getfamilyDetails($tax_result[0]->Emp_ID);
        $un=0;
        for($i=2;$i<count($emp_unsel_pgmlng);$i++) {
            if($emp_unsel_pgmlng[$i] != "" &&
                                  $emp_unsel_pgmlng[$i] != "/0000-00-00" &&
                                  $emp_unsel_pgmlng[$i] != "/"
                                  && (!in_array($family[$i], $family_sel_list))){
                $unselectArray[$un] = $family[$i];
                $un++;
            }
        }
        if (!empty($family_sel_list)) {
            $family_listselected=Tax::selectfamilylist($family_sel_list, $tax_result[0]->Emp_ID, $unselectArray, $tax_result[0]->Gender);
        }
        if (!empty($unselectArray)) {
            $family_listunselected=Tax::selectfamilylist($unselectArray, $tax_result[0]->Emp_ID, $unselectArray, $tax_result[0]->Gender);
        }
        self::array_sort_by_column($family_listselected, 2, SORT_ASC);
        self::array_sort_by_column($family_listunselected, 2, SORT_ASC);
        if (!empty($family_listselected) && !empty($family_listunselected)) {
            $family_list = array_merge($family_listselected, $family_listunselected);
        } else {
            $family_list = array();
        }
        $Relation_kanji = array('Father' => '父',
                                'Mother' => '母',
                                'GrandFather' =>'祖父',
                                'GrandMother' =>'祖母',
                                'ElderBrother' => '兄',
                                'ElderSister'=>'姉',
                                'YoungerBrother'=>'弟',
                                'YoungerSister'=>'妹',
                                'Wife' => '妻',
                                'Husband' => '夫',
                                'Children' => '子');
        return view('Tax.taxview', ['request' => $request,
                                    'tax_result' => $tax_result,
                                    'family_list' => $family_list,
                                    'Relation_kanji' => $Relation_kanji,
                                    'family_sel_list' => $family_sel_list,
                                    'selectedArray' => $selectedArray]);
    }
    function array_sort_by_column(&$arr, $col, $dir) {
        if (!empty($arr)) {
          $sort_col = array();
          foreach ($arr as $key=> $row) {
              $sort_col[$key] = $row[$col];
          }
          array_multisort($sort_col, $dir, $arr);
        }
    }
    public function familyselectionprocess(Request $request) {
        if($request->hdnflg == "1") {
          $request->selected = explode(",", $request->selected);
          $request->removed = explode(",", $request->removed);
        }
        if ($request->selected[0] != "") {
          if(count($request->selected) > 0 ) { 
            $insert = Tax::insertfamilyDetails($request);
          }
        }
        if(count($request->removed) > 0 ) { 
          $deleted=Tax::deletefamilyDetails($request);
        }
        Session::flash('message', 'Family Members Selected Sucessfully!');
        Session::flash('type', 'alert-success');
        Session::flash('empid', $request->empid );
        return Redirect::to('Tax/taxview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
    }
    public function empselectionpopup(Request $request) {
        $employeeUnselect = Tax::getAllEmpDetails();
        $employeeSelect = Tax::getAllFilteredEmpDetails();
        return view('Tax.empselectionpopup', ['request' => $request,
                                          'employeeUnselect' => $employeeUnselect,
                                          'employeeSelect' => $employeeSelect]);
    }
    public function empselectionprocess(Request $request) {
        if(count($request->selected) > 0 ) { 
            $insert=Tax::InsertEmpFlrDetails($request);
        }
        if(count($request->removed) > 0 ) { 
            $deleted=Tax::deleteEmpFlrDetails($request);
        }
        Session::flash('message', 'Employees Selected Successfully!');
        Session::flash('type', 'alert-success');
        $request->selected = "";
        $request->removed = "";
        return Redirect::to('Tax/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
    }
}