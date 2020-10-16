<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use DB;

use Session;

use Input;

use App;

use Auth;

use App\Http\Common\settingscommon;

class Setting extends Model { 

    public static function selectOnefieldDatas($fieldArray,$orderid,$request) {

        $db = DB::connection('mysql');

        $fieldNames="";

        for ($i=0; $i < count($fieldArray); $i++) {

            $fieldNames .= "".$fieldArray[$i].",";

        }

        $fieldNames = rtrim($fieldNames, ',');

        $query = $db->table($request->tablename)

                    ->select(DB::raw($fieldNames));

                    if (isset($request->location)) {

                        $query=$query->where('location',$request->location);

                    }

                    $query=$query->orderBy($orderid,'ASC')

                    ->get();

        return $query;

    }

	public static function fngetbankDetails($request) {

		$db = DB::connection('mysql');

		$query = $db->TABLE('mstbanks')

					->select('*')

					->WHERE('location','=', $request->location)

					->get();

		return $query;

	} 

    public static function Orderidgenerateforbank($location,$tbl_name) {

        $db = DB::connection('mysql');

        $query = $db->TABLE($tbl_name)

                    ->WHERE('location','=', $location)

                    ->count('id');

        return $query;            

    }

    public static function Orderidgeneratefortotal($location,$tbl_name) {

        $db = DB::connection('mysql');

        $query = $db->TABLE($tbl_name)

                    ->WHERE('location','=', $location)

                    ->max('id');

        return $query;            

    }

    public static function Orderidgenerateforbranchtotal($location,$tbl_name) {

        $db = DB::connection('mysql');

        $query = $db->TABLE($tbl_name)

                    ->max('id');

        return $query;            

    }

    public static function Orderidgenerateforbranch($tbl_name,$location) {

        $db = DB::connection('mysql');

        $query = $db->TABLE('mstbankbranch')

                    ->leftJoin('mstbanks','mstbanks.id', '=','mstbankbranch.BankId')

                    ->WHERE('location','=', $location)

                    ->count('mstbankbranch.id');

        return $query;             

    

    }

	// public static function Orderidgenerate($tbl_name) {

 //        $db = DB::connection('mysql');

 //        $query = $db->TABLE($tbl_name)

 //                    ->count('id');

 //        return $query;            

 //     }

    public static function Orderidgenerate($tbl_name) {

        $statement = DB::select("show table status like '$tbl_name'");

        return $statement[0]->Auto_increment;

    }

    public static function getsalaryDetailsCount($flg) {
        $db = DB::connection('mysql');
        $query = $db->table('mstsalary')
                    ->select('id','Name','location')
                    ->where('location','=',$flg)
                    ->max('Salarayid');
        return $query;
    }

    public static function insertqueryforbank($tbl_name,$request) { 

        $db = DB::connection('mysql');

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $location = $getTableFields[$tbl_name]['insertfields'][0];

        $bankname = $getTableFields[$tbl_name]['insertfields'][1];

        $DelFlg = $getTableFields[$tbl_name]['insertfields'][2];

        $InsDT = $getTableFields[$tbl_name]['insertfields'][3];

        $UpDT = $getTableFields[$tbl_name]['insertfields'][4];

        $CreatedBy = $getTableFields[$tbl_name]['insertfields'][5];

        $UpdatedBy = $getTableFields[$tbl_name]['insertfields'][6];

        $fieldcount = count($getTableFields[$tbl_name]['insertfields']);

        $CreatedByname = "Sathish Kumar"; //it will fix later

        if ($tbl_name == 'mstsalary') {
            $salaryid = $getTableFields[$tbl_name]['insertfields'][7];
            $maxid=self::getsalaryDetailsCount($request->location);
            if($maxid == "") {
                if ($request->location == '1') {
                    $cus3 = "SD001";
                } else {
                    $cus3 = "DD001";
                }
            } else {
                $aaa=$maxid;
                $saly = substr($aaa, 2,4);
                $cus1 = (int)$saly + 1;
                $cus2 = str_pad($cus1,3,"0",STR_PAD_LEFT);
                if ($request->location == '1') {
                    $cus4 = "SD";
                } else {
                    $cus4 = "DD";
                }
                $cus3 = $cus4 . $cus2;
            }
            $sql=$db->table($tbl_name)->insert(

                [$location => $request->location,

                $salaryid => $cus3,

                $bankname => $request->textbox1,

                $DelFlg =>'0',

                $InsDT => date('Y-m-d H-i-s'),

                $UpDT => date('Y-m-d H-i-s'),

                $CreatedBy => $CreatedByname,

                $UpdatedBy => $CreatedByname]

            ); 
        } else {

            $sql=$db->table($tbl_name)->insert(

                [$location => $request->location,

                $bankname => $request->textbox1,

                $DelFlg =>'0',

                $InsDT => date('Y-m-d H-i-s'),

                $UpDT => date('Y-m-d H-i-s'),

                $CreatedBy => $CreatedByname,

                $UpdatedBy => $CreatedByname]

            ); 
        }

        return  $sql;

    }

    public static function insertqueryforbranch($tbl_name,$request) { 

        $db = DB::connection('mysql');

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $DBTypeCD = $getTableFields[$tbl_name]['insertfields'][0];

        $DBType1 = $getTableFields[$tbl_name]['insertfields'][1];

        $DBType2 = $getTableFields[$tbl_name]['insertfields'][2];

        $DelFlg = $getTableFields[$tbl_name]['insertfields'][3];

        $InsDT = $getTableFields[$tbl_name]['insertfields'][4];

        $UpDT = $getTableFields[$tbl_name]['insertfields'][5];

        $CreatedBy = $getTableFields[$tbl_name]['insertfields'][6];

        $UpdatedBy = $getTableFields[$tbl_name]['insertfields'][7];

        $fieldcount = count($getTableFields[$tbl_name]['insertfields']);

        $CreatedByname = "Sathish Kumar"; //it will fix later

        $sql= $db->table($tbl_name)->insert(

                [$DBTypeCD => $request->selectbox1,

                $DBType1 => $request->textbox1,

                $DBType2 => $request->textbox2,

                $DelFlg =>'0',

                $InsDT => date('Y-m-d H-i-s'),

                $UpDT => date('Y-m-d H-i-s'),

                $CreatedBy => $CreatedByname,

                $UpdatedBy => $CreatedByname]

        );

        return  $sql;

    }

    public static function insertquery($tbl_name,$request) {

        $db = DB::connection('mysql');

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $DBType = $getTableFields[$tbl_name]['insertfields'][0];

        $DelFlg = $getTableFields[$tbl_name]['insertfields'][1];

        $InsDT = $getTableFields[$tbl_name]['insertfields'][2];

        $UpDT = $getTableFields[$tbl_name]['insertfields'][3];

        $CreatedBy = $getTableFields[$tbl_name]['insertfields'][4];

        $UpdatedBy = $getTableFields[$tbl_name]['insertfields'][5];

        $CreatedByname = "Sathish Kumar"; //it will fix later

        $sql=$db->table($tbl_name)->insert(

                [$DBType => $request->textbox1,

                $DelFlg =>'0',

                $InsDT => date('Y-m-d H-i-s'),

                $UpDT => date('Y-m-d H-i-s'),

                $CreatedBy => $CreatedByname,

                $UpdatedBy => $CreatedByname]

            );

        return  $sql;

    }

    public static function insertquerytwofield($tbl_name,$request,$orderid) {

        $db = DB::connection('mysql');

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $DBType = $getTableFields[$tbl_name]['insertfields'][0];

        $DBType1 = $getTableFields[$tbl_name]['insertfields'][1];

        $DelFlg = $getTableFields[$tbl_name]['insertfields'][2];

        $InsDT = $getTableFields[$tbl_name]['insertfields'][3];

        $UpDT = $getTableFields[$tbl_name]['insertfields'][4];

        $CreatedBy = $getTableFields[$tbl_name]['insertfields'][5];

        $UpdatedBy = $getTableFields[$tbl_name]['insertfields'][6];

        $CreatedByname = "Sathish Kumar"; //it will fix later

         // $CreatedByname = Auth::user()->FirstName;

        if($tbl_name != "sysdesignationtypes") {

            $sql=$db->table($tbl_name)->insert(

                    [$DBType => $request->textbox1,

                    $DBType1 => $request->textbox2,

                    $DelFlg =>'0',

                    $InsDT => date('Y-m-d H-i-s'),

                    $UpDT => date('Y-m-d H-i-s'),

                    $CreatedBy => $CreatedByname,

                    $UpdatedBy => $CreatedByname]

                );

        } else {

            $sql=$db->table($tbl_name)->insert(

                    ['DesignationCD' => $orderid,

                    'Order_id' => $orderid,

                    $DBType => $request->textbox1,

                    $DBType1 => $request->textbox2,

                    $DelFlg =>'0',

                    $InsDT => date('Y-m-d H-i-s'),

                    $UpDT => date('Y-m-d H-i-s'),

                    $CreatedBy => $CreatedByname,

                    $UpdatedBy => $CreatedByname]

                );

        }

        return  $sql;

    }

    public static function updateSingleField($request) {

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $Typename = $getTableFields[$request->tablename]['updatefields'][0];

        $UpDT = $getTableFields[$request->tablename]['updatefields'][1];

        $UpdatedBy = $getTableFields[$request->tablename]['updatefields'][2];

        $db = DB::connection('mysql');

        $CreatedByname = "Sathish Kumar"; //it will fix later

        $update = $db->table($request->tablename)

            ->where('id', $request->id)

            ->update(

                [$Typename => $request->textbox1,

                $UpDT => date('Y-m-d H-i-s'),

                $UpdatedBy => $CreatedByname]

        );

        return $update;            

    }

    public static function updatetwoField($request) {

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $Typename1 = $getTableFields[$request->tablename]['updatefields'][0];

        $Typename2 = $getTableFields[$request->tablename]['updatefields'][1];

        $UpDT = $getTableFields[$request->tablename]['updatefields'][2];

        $UpdatedBy = $getTableFields[$request->tablename]['updatefields'][3];

        $db = DB::connection('mysql');

        $CreatedByname = "Sathish Kumar"; //it will fix later

        $update = $db->table($request->tablename)

            ->where('id', $request->id)

            ->update(

                [$Typename1 => $request->textbox1,

                $Typename2 => $request->textbox2,

                $UpDT => date('Y-m-d H-i-s'),

                $UpdatedBy => $CreatedByname]

        );

        return $update;            

    }

    public static function updatethreeField($request) {

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $selectbox1 = $getTableFields[$request->tablename]['updatefields'][0];

        $Typename1 = $getTableFields[$request->tablename]['updatefields'][1];

        $Typename2 = $getTableFields[$request->tablename]['updatefields'][2];

        $UpDT = $getTableFields[$request->tablename]['updatefields'][3];

        $UpdatedBy = $getTableFields[$request->tablename]['updatefields'][4];

        $db = DB::connection('mysql');

        $CreatedByname = "Sathish Kumar"; //it will fix later

        $update = $db->table($request->tablename)

            ->where('id', $request->id)

            ->update(

                [$selectbox1 => $request->selectbox1,

                $Typename1 => $request->textbox1,

                $Typename2 => $request->textbox2,

                $UpDT => date('Y-m-d H-i-s'),

                $UpdatedBy => $CreatedByname]

        );

        return $update;            

     }

    public static function updateUseNotUse($request) {

        $db = DB::connection('mysql');

        $getTableFields = settingscommon::getDbFieldsforProcess();

        $tablename = $request->tablename;

        $updfield = $getTableFields[$request->tablename]['usenotusefields'][0];

        if ($request->curtFlg == 0) {

            $upvalue = 1;

        } else {

            $upvalue = 0;

        } 

        $sql = $db->table($tablename)

                ->where('id', $request->editid)

                ->update([$updfield => $upvalue]);

                print_r($request->editid);

        return $sql;

    }

    public static function selectTwofieldDatas($fieldArray,$orderid,$request) {

        $fieldNames="";

        $mainfield="";

        $db = DB::connection('mysql');

        $con="";

        if ($request->tableselect!="" && $request->tableselect!="text") {

            for ($i=0; $i < count($fieldArray); $i++) {

                if ($i==2) {

                    $con=" ON main.".$fieldArray[$i]."=sub.id";

                    $mainfield="main.".$fieldArray[$i];

                    $fieldNames .= "sub.".$selectfield." AS ".$fieldArray[$i].",";

                } else {

                    $fieldNames .= "main.".$fieldArray[$i].",";

                }

                if ($i==(count($fieldArray)-1)) {

                    $fieldNames .= $mainfield." AS selectfield,";

                }

            }

            $maintbl=" FROM $request->tablename main";

            $join=" LEFT JOIN";

            $subtbl=" $request->tableselect sub";

            $orderby=" ORDER BY ".$orderid." ASC";

        } else {

            for ($i=0; $i < count($fieldArray); $i++) { 

                $fieldNames .= $fieldArray[$i].",";

            }

        }

        $fieldNames = rtrim($fieldNames, ',');

        if ($request->tableselect!="" && $request->tableselect!="text") {

            $sql = $db ->select("SELECT $fieldNames $maintbl $join $subtbl $con $orderby");

        } else {

            $sql = $db ->select("SELECT $fieldNames FROM $_REQUEST[tablename] ORDER BY $orderid ASC");

        }

        $bypopupAjax = 1;

        return $sql;

    }

    public static function selectthreefieldDatasforbank($fieldArray,$orderid,$selectfield=null,$request) {

        $db = DB::connection('mysql');

        $location="";

        $fieldNames="";

        $mainfield="";

        $con="";

        for ($i=0; $i < count($fieldArray); $i++) {

            if ($i==1) {

                $con=" ON main.".$fieldArray[$i]."=sub.id";

                $mainfield="main.".$fieldArray[$i];

                $fieldNames .= "sub.".$selectfield." AS ".$fieldArray[$i].",";

            } else {

                $fieldNames .= "main.".$fieldArray[$i].",";

            }

            if ($i==(count($fieldArray)-1)) {

                $fieldNames .= $mainfield." AS selectfield,";

            }

        }

        $maintbl=" FROM $request->tablename main";

        $join=" LEFT JOIN ";

        $subtbl=" $request->tableselect sub";

        $cond=" WHERE sub.location=".$request->location;

        $fieldNames = rtrim($fieldNames, ',');

        $query = $db->select("SELECT $fieldNames $maintbl $join $subtbl $con $cond");

        return $query;

    }



    public static function selectthreefieldDatas($fieldArray,$orderid,$selectfield,$request) {

        $db = DB::connection('mysql');

        $fieldNames="";

        $mainfield="";

        $con="";

        for ($i=0; $i < count($fieldArray); $i++) {

            if ($i==1) {

                $con=" ON main.".$fieldArray[$i]."=sub.id";

                $mainfield="main.".$fieldArray[$i];

                if ($request->tablename == "inv_set_salarysub") {

                    $fieldNames .= "sub."."main_eng"." AS ".$fieldArray[$i].",";

                } elseif ($request->tablename == "inv_set_transfersub") {

                    $fieldNames .= "sub."."main_eng"." AS ".$fieldArray[$i].",";

                } elseif ($request->tablename == "inv_set_expensesub") {

                    $fieldNames .= "sub."."Subject"." AS ".$fieldArray[$i].",";

                } else {

                    $fieldNames .= "main.".$selectfield." AS ".$fieldArray[$i].",";

                }

            } else {

                $fieldNames .= "main.".$fieldArray[$i].",";

            }

            if ($i==(count($fieldArray)-1)) {

                $fieldNames .= $mainfield." AS selectfield,";

            }

        }

        $maintbl=" FROM $request->tablename main";

        $join=" LEFT JOIN ";

        $subtbl=" $request->tableselect sub";

        $fieldNames = rtrim($fieldNames, ',');

        $query = $db->select("SELECT $fieldNames $maintbl $join $subtbl $con");

        return $query;

    }

    public static function selectboxDatas($selectfieldArray,$orderid,$request) {

        $db = DB::connection('mysql');

        $wheredelflg="";

        $getTableFields = settingscommon::getDbFieldsforProcess();

        if ($request->parametersub == "1") {

                $request->tablename = "inv_set_expensesub";

        }

        $wheredelflg = $getTableFields[$request->tablename]['usenotusefields'][0];

        $fieldNames="";

        for ($i=0; $i < count($selectfieldArray); $i++) {

            $fieldNames .= "".$selectfieldArray[$i].",";

        }

        if ($request->tableselect == "mstbanks") {

            if ($request->location==2) {

                $location=2;

            } else {

                $location=1;

            }

        }

        $fieldNames = rtrim($fieldNames, ',');

        $idvalue = $selectfieldArray[0];

        $textvalue = $selectfieldArray[1];

        $query = $db->table($request->tableselect)

                    ->select(DB::raw($fieldNames))

                    ->where($wheredelflg,0);

                    if ($request->location != "" && $request->tableselect=="mstbanks") {

                        $query=$query->where('location',$location);

                    }

        $query=$query->orderBy($selectfieldArray[0],'ASC')

                    ->lists($textvalue,$idvalue);

        return $query;

    }



}