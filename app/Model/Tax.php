<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
class Tax extends Model {
	public static function fnGetEmployeewithTaxCount() {
		$db = DB::connection('otherdb');
		$employee_tableName = "emp_mstemployees";
		$EmpDetails = $db->table($employee_tableName)
                      ->SELECT('Emp_ID','LastName','FirstName')
                      ->WHERE('delFlg',0)
                      ->WHERE('resign_id',0)
                      ->WHERE('title',2)
                      ->WHERE('emp_mstemployees.microbitflg',0)
                      ->orderBy('Emp_ID', 'ASC')
                      ->GET();
        // 163 Employees
        return $EmpDetails;
	}
	public static function getfamily($EmpID) {
		$familyselectdetails = DB::TABLE('temp_familydetails')
                              ->SELECT('family_relation')->WHERE('Emp_ID', '=', $EmpID)
                              ->orderBy('id', 'ASC')
                              ->count();
    	return $familyselectdetails;
	}
	public static function fnGetEmployeeTaxDetails() {
		$employee = DB::TABLE('inv_taxdetails')
						->SELECT('id')
						->COUNT();
		return $employee; 
	}
	public static function fnGetEmployeeDetails($request) {
		$employee = DB::TABLE(DB::raw("(
							select `emp_mstemployees`.`LastName`, `inv_taxdetails`.`Emp_ID` as `Emp_ID`, `inv_taxdetails`.`empInsurence`, `inv_taxdetails`.`citizenShip`, 
								`inv_taxdetails`.`excelFlg`, `visarenew`.`visaStatus`, 
								`visastatus`.`JapNM`, 
								`emp_mstemployees`.`Emp_ID` as `empuserid`, `emp_mstemployees`.`DOB`, 
								`emp_mstemployees`.`FirstName`, `emp_mstemployees`.`KanaFirstName`, 
								`emp_mstemployees`.`KanaLastName`, `emp_mstemployees`.`Gender`, 
								`mstaddress`.*, CONCAT('〒',pincode,' ', jpstate,'',jpaddress,'-',roomno,'号') AS full_address 
								from `temp_empview` 
								left join `inv_taxdetails` on `temp_empview`.`Emp_ID` = `inv_taxdetails`.`Emp_ID` 
								left join `emp_mstemployees` on `inv_taxdetails`.`Emp_ID` = `emp_mstemployees`.`Emp_ID` 
								left join `mstaddress` on `inv_taxdetails`.`address` = `mstaddress`.`id` 
								left join `visarenew` on `inv_taxdetails`.`Emp_ID` = `visarenew`.`Emp_ID` 
								AND visarenew.id IN (
								    SELECT MAX(id)
								    FROM visarenew
								    GROUP BY visarenew.Emp_ID
								)
								left join `visastatus` on `visarenew`.`visaStatus` = `visastatus`.`VisaCD` 
								WHERE `temp_empview`.`delflg` = 0
								and `emp_mstemployees`.`resign_id` = 0
								and `emp_mstemployees`.`Title` = 2
								group by `emp_mstemployees`.`Emp_ID` 
								order by `emp_mstemployees`.`Emp_ID` asc
						) as VisaStatus"))
                    ->paginate($request->plimit);
		return $employee;
	}
	public static function fnGetEmployeeFamilyDetails($userId) {
		$db = DB::connection('otherdb');
		$employeeDetails = $db->TABLE('emp_mstemployees')
                          ->SELECT('*')
                          ->WHERE('Emp_ID', '=', $userId)
                          ->get();
      	return $employeeDetails;
	}
	public static function getinsuranceamount($empid,$start,$end) {
		$db = DB::connection('otherdb');
		$main_sub = '7';
	    $su_sub = '17';
	    $query=$db->TABLE('mstexpenses_dtl')
	                  ->SELECT(DB::raw("FORMAT(SUM(Amount), 0) AS SUM"))
	                  ->WHERE('Emp_ID', '=', $empid)
	                  ->WHERE('main_sub', '=', $main_sub)
	                  ->WHERE('su_sub', '=', $su_sub)
	                  ->WHERERAW("Date BETWEEN '$start' AND '$end'")
	                  ->get();
	    return $query;
	}
	public static function fnGetFamilyDetails($empid) {
		$db = DB::connection('otherdb');
		$query=$db->TABLE('temp_familydetails')
	                  ->SELECT('family_relation')
	                  ->WHERE('Emp_ID', '=', $empid)
	                  ->get();
	    return $query;
	}
	public static function fnInsertCheckflagData($request,$flg) {
		$update=DB::table('inv_taxdetails')
            ->where('Emp_ID', $request->empid)
            ->update(
            ['excelFlg'=>1]
          );
	}
	public static function fnInsertEmployeeFamilyDetails($valuearray,$sum,$familyDetailsList) {
      	$insert=0;
      	$familyinsert=0;
      	$kanaName=0;
      	$kanaNameCheck = self::fnKanaNameCheck($valuearray[0]->Emp_ID);
      	if(isset($kanaNameCheck[0])) {
	      	if($kanaNameCheck[0]->KanaFirstName=="" && $kanaNameCheck[0]->KanaLastName=="") {
	      		$getKanaNames = self::fnFetchKanaNames($valuearray[0]->Emp_ID);
	      		$kanaName = DB::table('emp_mstemployees')
										->where('Emp_ID', $valuearray[0]->Emp_ID)
										->update([
	      							'KanaFirstName' => $getKanaNames[0]->KanaFirstName,
	      							'KanaLastName' => $getKanaNames[0]->KanaLastName,
	      									]);	
	      	}
	    }
	    $empTempinsert = DB::TABLE('temp_empview')->insert([
      							'Emp_ID' => $valuearray[0]->Emp_ID,
      							'create_date' => date('Y-m-d H:i:s'),
      							'create_by' => Auth::user()->username,
      							'delflg' => 0,
      							'update_date' => date('Y-m-d H:i:s'),
      							'update_by' => Auth::user()->username,
      									]);	
      	for ($i=0; $i < count($valuearray); $i++) {
      	$exitcheck = self::fncheckrecodexits($valuearray[$i]->Emp_ID);
        if (empty($exitcheck)) {
        	foreach ($familyDetailsList as $key => $value) {
        		// print_r($value->family_relation); exit;
        		$familyinsert = DB::TABLE('temp_familydetails')->insert(
      									[
      							'Emp_ID' => $valuearray[$i]->Emp_ID,
      							'family_relation' => $value->family_relation,
      							'create_date' => date('Y-m-d H:i:s'),
      							'create_by' => Auth::user()->username,
      							'delflg' => 0,
      							'update_date' => date('Y-m-d H:i:s'),
      							'update_by' => Auth::user()->username,
      									]);	
        	}
        	// exit;
			$insert=DB::TABLE('inv_taxdetails')->insert(
      			[
      			'Emp_ID' => $valuearray[$i]->Emp_ID,
				'address' => $valuearray[$i]->Address1,
				'empInsurence' => $sum,
				'citizenShip' => $valuearray[$i]->citizenShip,
				'Father' =>  $valuearray[$i]->Father,
				'FatherName' =>  $valuearray[$i]->FatherName,
				'FatherkanaName' => $valuearray[$i]->FatherkanaName,
				'FatherWork' => $valuearray[$i]->FatherWork,
				'FatherWorkIncome' => $valuearray[$i]->FatherWorkIncome,
				'FatherDOB' => $valuearray[$i]->FatherDOB,
				'Mother' => $valuearray[$i]->Mother,
				'MotherName' => $valuearray[$i]->MotherName,
				'MotherkanaName' => $valuearray[$i]->MotherkanaName,
				'MotherWork' => $valuearray[$i]->MotherWork,
				'MotherWorkIncome' => $valuearray[$i]->MotherWorkIncome,
				'MotherDOB' => $valuearray[$i]->MotherDOB,
				'GrandFather' => $valuearray[$i]->GrandFather,
				'GrandFatherName' => $valuearray[$i]->GrandFatherName,
				'GrandFatherkanaName' => $valuearray[$i]->GrandFatherkanaName,
				'GrandFatherDOB' => $valuearray[$i]->GrandFatherDOB,
				'GrandMother' => $valuearray[$i]->GrandMother,
				'GrandMotherName' => $valuearray[$i]->GrandMotherName,
				'GrandMotherkanaName' => $valuearray[$i]->GrandMotherkanaName,
				'GrandMotherDOB' => $valuearray[$i]->GrandMotherDOB,
				'YoungerSister' => $valuearray[$i]->YoungerSister,
				'YoungerBrother' => $valuearray[$i]->YoungerBrother,
				'ElderSister' => $valuearray[$i]->ElderSister,
				'ElderBrother' => $valuearray[$i]->ElderBrother,
				'ElderBrother1Name' => $valuearray[$i]->ElderBrother1Name,
				'ElderBrother1kanaName' => $valuearray[$i]->ElderBrother1kanaName,
				'ElderBrother1DOB' => $valuearray[$i]->ElderBrother1DOB,
				'ElderBrother1' => $valuearray[$i]->ElderBrother1,
				'ElderBrother1Work' => $valuearray[$i]->ElderBrother1Work,
				'ElderBrother1WorkIncome' => $valuearray[$i]->ElderBrother1WorkIncome,
				'ElderBrother2Name' => $valuearray[$i]->ElderBrother2Name,
				'ElderBrother2kanaName' => $valuearray[$i]->ElderBrother2kanaName,
				'ElderBrother2DOB' => $valuearray[$i]->ElderBrother2DOB,
				'ElderBrother2' => $valuearray[$i]->ElderBrother2,
				'ElderBrother2Work' => $valuearray[$i]->ElderBrother2Work,
				'ElderBrother2WorkIncome' => $valuearray[$i]->ElderBrother2WorkIncome,
				'ElderBrother3Name' => $valuearray[$i]->ElderBrother3Name,
				'ElderBrother3kanaName' => $valuearray[$i]->ElderBrother3kanaName,
				'ElderBrother3DOB' => $valuearray[$i]->ElderBrother3DOB,
				'ElderBrother3' => $valuearray[$i]->ElderBrother3,
				'ElderBrother3Work' => $valuearray[$i]->ElderBrother3Work,
				'ElderBrother3WorkIncome' => $valuearray[$i]->ElderBrother3WorkIncome,
				'ElderBrother4Name' => $valuearray[$i]->ElderBrother4Name,
				'ElderBrother4kanaName' => $valuearray[$i]->ElderBrother4kanaName,
				'ElderBrother4DOB' => $valuearray[$i]->ElderBrother4DOB,
				'ElderBrother4' => $valuearray[$i]->ElderBrother4,
				'ElderBrother4Work' => $valuearray[$i]->ElderBrother4Work,
				'ElderBrother4WorkIncome' => $valuearray[$i]->ElderBrother4WorkIncome,
				'ElderBrother5Name' => $valuearray[$i]->ElderBrother5Name,
				'ElderBrother5kanaName' => $valuearray[$i]->ElderBrother5kanaName,
				'ElderBrother5DOB' => $valuearray[$i]->ElderBrother5DOB,
				'ElderBrother5' => $valuearray[$i]->ElderBrother5,
				'ElderBrother5Work' => $valuearray[$i]->ElderBrother5Work,
				'ElderBrother5WorkIncome' => $valuearray[$i]->ElderBrother5WorkIncome,
				'ElderBrother6Name' => $valuearray[$i]->ElderBrother6Name,
				'ElderBrother6kanaName' => $valuearray[$i]->ElderBrother6kanaName,
				'ElderBrother6DOB' => $valuearray[$i]->ElderBrother6DOB,
				'ElderBrother6' => $valuearray[$i]->ElderBrother6,
				'ElderBrother6Work' => $valuearray[$i]->ElderBrother6Work,
				'ElderBrother6WorkIncome' => $valuearray[$i]->ElderBrother6WorkIncome,
				'ElderSister1Name' => $valuearray[$i]->ElderSister1Name,
				'ElderSister1kanaName' => $valuearray[$i]->ElderSister1kanaName,
				'ElderSister1DOB' => $valuearray[$i]->ElderSister1DOB,
				'ElderSister1' => $valuearray[$i]->ElderSister1,
				'ElderSister1Work' => $valuearray[$i]->ElderSister1Work,
				'ElderSister1WorkIncome' => $valuearray[$i]->ElderSister1WorkIncome,
				'ElderSister2Name' => $valuearray[$i]->ElderSister2Name,
				'ElderSister2kanaName' => $valuearray[$i]->ElderSister2kanaName,
				'ElderSister2DOB' => $valuearray[$i]->ElderSister2DOB,
				'ElderSister2' => $valuearray[$i]->ElderSister2,
				'ElderSister2Work' => $valuearray[$i]->ElderSister2Work,
				'ElderSister2WorkIncome' => $valuearray[$i]->ElderSister2WorkIncome,
				'ElderSister3Name' => $valuearray[$i]->ElderSister3Name,
				'ElderSister3kanaName' => $valuearray[$i]->ElderSister3kanaName,
				'ElderSister3DOB' => $valuearray[$i]->ElderSister3DOB,
				'ElderSister3' => $valuearray[$i]->ElderSister3,
				'ElderSister3Work' => $valuearray[$i]->ElderSister3Work,
				'ElderSister3WorkIncome' => $valuearray[$i]->ElderSister3WorkIncome,
				'ElderSister4Name' => $valuearray[$i]->ElderSister4Name,
				'ElderSister4kanaName' => $valuearray[$i]->ElderSister4kanaName,
				'ElderSister4DOB' => $valuearray[$i]->ElderSister4DOB,
				'ElderSister4' => $valuearray[$i]->ElderSister4,
				'ElderSister4Work' => $valuearray[$i]->ElderSister4Work,
				'ElderSister4WorkIncome' => $valuearray[$i]->ElderSister4WorkIncome,
				'ElderSister5Name' => $valuearray[$i]->ElderSister5Name,
				'ElderSister5kanaName' => $valuearray[$i]->ElderSister5kanaName,
				'ElderSister5DOB' => $valuearray[$i]->ElderSister5DOB,
				'ElderSister5' => $valuearray[$i]->ElderSister5,
				'ElderSister5Work' => $valuearray[$i]->ElderSister5Work,
				'ElderSister5WorkIncome' => $valuearray[$i]->ElderSister5WorkIncome,
				'ElderSister6Name' => $valuearray[$i]->ElderSister6Name,
				'ElderSister6kanaName' => $valuearray[$i]->ElderSister6kanaName,
				'ElderSister6DOB' => $valuearray[$i]->ElderSister6DOB,
				'ElderSister6' => $valuearray[$i]->ElderSister6,
				'ElderSister6Work' => $valuearray[$i]->ElderSister6Work,
				'ElderSister6WorkIncome' => $valuearray[$i]->ElderSister6WorkIncome,
				'YoungerBrother1Name' => $valuearray[$i]->YoungerBrother1Name,
				'YoungerBrother1KanaName' => $valuearray[$i]->YoungerBrother1KanaName,
				'YoungerBrother1DOB' => $valuearray[$i]->YoungerBrother1DOB,
				'YoungerBrother1' => $valuearray[$i]->YoungerBrother1,
				'YoungerBrother1Work' => $valuearray[$i]->YoungerBrother1Work,
				'YoungerBrother1WorkIncome' => $valuearray[$i]->YoungerBrother1WorkIncome,
				'YoungerBrother2Name' => $valuearray[$i]->YoungerBrother2Name,
				'YoungerBrother2KanaName' => $valuearray[$i]->YoungerBrother2KanaName,
				'YoungerBrother2DOB' => $valuearray[$i]->YoungerBrother2DOB,
				'YoungerBrother2' => $valuearray[$i]->YoungerBrother2,
				'YoungerBrother2Work' => $valuearray[$i]->YoungerBrother2Work,
				'YoungerBrother2WorkIncome' => $valuearray[$i]->YoungerBrother2WorkIncome,
				'YoungerBrother3Name' => $valuearray[$i]->YoungerBrother3Name,
				'YoungerBrother3KanaName' => $valuearray[$i]->YoungerBrother3KanaName,
				'YoungerBrother3DOB' => $valuearray[$i]->YoungerBrother3DOB,
				'YoungerBrother3' => $valuearray[$i]->YoungerBrother3,
				'YoungerBrother3Work' => $valuearray[$i]->YoungerBrother3Work,
				'YoungerBrother3WorkIncome' => $valuearray[$i]->YoungerBrother3WorkIncome,
				'YoungerBrother4Name' => $valuearray[$i]->YoungerBrother4Name,
				'YoungerBrother4KanaName' => $valuearray[$i]->YoungerBrother4KanaName,
				'YoungerBrother4DOB' => $valuearray[$i]->YoungerBrother4DOB,
				'YoungerBrother4' => $valuearray[$i]->YoungerBrother4,
				'YoungerBrother4Work' => $valuearray[$i]->YoungerBrother4Work,
				'YoungerBrother4WorkIncome' => $valuearray[$i]->YoungerBrother4WorkIncome,
				'YoungerBrother5Name' => $valuearray[$i]->YoungerBrother5Name,
				'YoungerBrother5KanaName' => $valuearray[$i]->YoungerBrother5KanaName,
				'YoungerBrother5DOB' => $valuearray[$i]->YoungerBrother5DOB,
				'YoungerBrother5' => $valuearray[$i]->YoungerBrother5,
				'YoungerBrother5Work' => $valuearray[$i]->YoungerBrother5Work,
				'YoungerBrother5WorkIncome' => $valuearray[$i]->YoungerBrother5WorkIncome,
				'YoungerBrother6Name' => $valuearray[$i]->YoungerBrother6Name,
				'YoungerBrother6KanaName' => $valuearray[$i]->YoungerBrother6KanaName,
				'YoungerBrother6DOB' => $valuearray[$i]->YoungerBrother6DOB,
				'YoungerBrother6' => $valuearray[$i]->YoungerBrother6,
				'YoungerBrother6Work' => $valuearray[$i]->YoungerBrother6Work,
				'YoungerBrother6WorkIncome' => $valuearray[$i]->YoungerBrother6WorkIncome,
				'YoungerSister1Name' => $valuearray[$i]->YoungerSister1Name,
				'YoungerSister1KanaName' => $valuearray[$i]->YoungerSister1KanaName,
				'YoungerSister1DOB' => $valuearray[$i]->YoungerSister1DOB,
				'YoungerSister1' => $valuearray[$i]->YoungerSister1,
				'YoungerSister1Work' => $valuearray[$i]->YoungerSister1Work,
				'YoungerSister1WorkIncome' => $valuearray[$i]->YoungerSister1WorkIncome,
				'YoungerSister2Name' => $valuearray[$i]->YoungerSister2Name,
				'YoungerSister2KanaName' => $valuearray[$i]->YoungerSister2KanaName,
				'YoungerSister2DOB' => $valuearray[$i]->YoungerSister2DOB,
				'YoungerSister2' => $valuearray[$i]->YoungerSister2,
				'YoungerSister2Work' => $valuearray[$i]->YoungerSister2Work,
				'YoungerSister2WorkIncome' => $valuearray[$i]->YoungerSister2WorkIncome,
				'YoungerSister3Name' => $valuearray[$i]->YoungerSister3Name,
				'YoungerSister3KanaName' => $valuearray[$i]->YoungerSister3KanaName,
				'YoungerSister3DOB' => $valuearray[$i]->YoungerSister3DOB,
				'YoungerSister3' => $valuearray[$i]->YoungerSister3,
				'YoungerSister3Work' => $valuearray[$i]->YoungerSister3Work,
				'YoungerSister3WorkIncome' => $valuearray[$i]->YoungerSister3WorkIncome,
				'YoungerSister4Name' => $valuearray[$i]->YoungerSister4Name,
				'YoungerSister4KanaName' => $valuearray[$i]->YoungerSister4KanaName,
				'YoungerSister4DOB' => $valuearray[$i]->YoungerSister4DOB,
				'YoungerSister4' => $valuearray[$i]->YoungerSister4,
				'YoungerSister4Work' => $valuearray[$i]->YoungerSister4Work,
				'YoungerSister4WorkIncome' => $valuearray[$i]->YoungerSister4WorkIncome,
				'YoungerSister5Name' => $valuearray[$i]->YoungerSister5Name,
				'YoungerSister5KanaName' => $valuearray[$i]->YoungerSister5KanaName,
				'YoungerSister5DOB' => $valuearray[$i]->YoungerSister5DOB,
				'YoungerSister5' => $valuearray[$i]->YoungerSister5,
				'YoungerSister5Work' => $valuearray[$i]->YoungerSister5Work,
				'YoungerSister5WorkIncome' => $valuearray[$i]->YoungerSister5WorkIncome,
				'YoungerSister6Name' => $valuearray[$i]->YoungerSister6Name,
				'YoungerSister6KanaName' => $valuearray[$i]->YoungerSister6KanaName,
				'YoungerSister6DOB' => $valuearray[$i]->YoungerSister6DOB,
				'YoungerSister6' => $valuearray[$i]->YoungerSister6,
				'YoungerSister6Work' => $valuearray[$i]->YoungerSister6Work,
				'YoungerSister6WorkIncome' => $valuearray[$i]->YoungerSister6WorkIncome,
				'Relation1' => $valuearray[$i]->Relation1,
				'RelationName1' => $valuearray[$i]->RelationName1,
				'Relationkana1' => $valuearray[$i]->Relationkana1,
				'Relation1DOB' => $valuearray[$i]->Relation1DOB,
				'Relation1lives' => $valuearray[$i]->Relation1lives,
				'Relation2' => $valuearray[$i]->Relation2,
				'RelationName2' => $valuearray[$i]->RelationName2,
				'Relationkana2' => $valuearray[$i]->Relationkana2,
				'Relation2DOB' => $valuearray[$i]->Relation2DOB,
				'Relation2lives' => $valuearray[$i]->Relation2lives,
				'Relation3' => $valuearray[$i]->Relation3,
				'RelationName3' => $valuearray[$i]->RelationName3,
				'Relationkana3' => $valuearray[$i]->Relationkana3,
				'Relation3DOB' => $valuearray[$i]->Relation3DOB,
				'Relation3lives' => $valuearray[$i]->Relation3lives,
				'Relation4' => $valuearray[$i]->Relation4,
				'RelationName4' => $valuearray[$i]->RelationName4,
				'Relationkana4' => $valuearray[$i]->Relationkana4,
				'Relation4DOB' => $valuearray[$i]->Relation4DOB,
				'Relation4lives' => $valuearray[$i]->Relation4lives,
				'Relation5' => $valuearray[$i]->Relation5,
				'RelationName5' => $valuearray[$i]->RelationName5,
				'Relationkana5' => $valuearray[$i]->Relationkana5,
				'Relation5DOB' => $valuearray[$i]->Relation5DOB,
				'Relation5lives' => $valuearray[$i]->Relation5lives,
				'created_by' => Auth::user()->username,
				'created_time' => date('Y-m-d H:i:s'),
				'updated_by' => Auth::user()->username,
				'updated_time' => date('Y-m-d H:i:s'),
      			]);	
			}
		}
		return $insert; 
	}
	public static function fnFetchKanaNames($EmpID) {
		$db = DB::connection('otherdb');
		$fetchrecord = $db->table('emp_mstemployees')
            ->SELECT('KanaLastName','KanaFirstName')
            ->WHERE('Emp_ID', '=', $EmpID)
            ->get();
      	return $fetchrecord;
	}
	public static function fnKanaNameCheck($EmpID) {
		$fetchrecord = DB::table('emp_mstemployees')
            ->SELECT('KanaLastName','KanaFirstName')
            ->WHERE('Emp_ID', '=', $EmpID)
            ->get();
      	return $fetchrecord;
	}
	public static function fncheckrecodexits($Emp_ID) {
    	$fetchrecord = DB::table('inv_taxdetails')
            ->SELECT('id')
            ->WHERE('Emp_ID', '=', $Emp_ID)
            ->get();
      	return $fetchrecord;
  	}
  	public static function fnGetEmployeeDownload($request) {
      	$employeeDetails = DB::TABLE('emp_mstemployees')
                              ->SELECT('inv_taxdetails.*','emp_mstemployees.FirstName','emp_mstemployees.KanaFirstName','emp_mstemployees.KanaLastName','emp_mstemployees.DOB','emp_mstemployees.Gender','emp_mstemployees.LastName',DB::raw("CONCAT('', jpstate,'',jpaddress,'-',roomno,'号') AS full_address"))
                              ->LEFTJOIN('inv_taxdetails', 'inv_taxdetails.Emp_ID', '=', 'emp_mstemployees.Emp_ID')
                              ->LEFTJOIN('mstaddress', 'mstaddress.id', '=', 'emp_mstemployees.Address1')
                              ->WHERE('emp_mstemployees.Emp_ID',$request->empid)
                              ->GET();
            // print_r($employeeDetails); exit;
      	return $employeeDetails;
  	}
  	public static function getfamilyArray($empid) {
	    $family_sel = array();
	    $familyselectdetails = DB::TABLE('temp_familydetails')
	                              ->SELECT('family_relation')->WHERE('Emp_ID', '=', $empid)
	                              ->orderBy('id', 'ASC')
	                              ->get();
	    $i=0;
	    foreach ($familyselectdetails as $familymembers) {
	      $family_sel[$i] = $familymembers->family_relation;
	      $i++;
	    }
	    return $family_sel;
  	}
  	public static function selectfamilylist($fam_list, $empid, $unselected = array(), $gender=null) {
	    $diffArray = array_diff($fam_list, $unselected);
	    $familyselect = array();
	    $fam_listOrder = $fam_list;
	    if (empty($fam_list) || $fam_list[0] == "") {
	      $familyselect = 0;
	    } else {
	    $relationUsed = 4;
	    $relation['Mother'] = array('MotherName','MotherWork','MotherDOB','MotherkanaName');
	    $relation['Father'] = array('FatherName','FatherWork','FatherDOB','FatherkanaName');

	    $relation['GrandFather'] = array('GrandFatherName','GrandFather','GrandFatherDOB','GrandFatherkanaName');
	    $relation['GrandMother'] = array('GrandMotherName','GrandMother','GrandMotherDOB','GrandMotherkanaName');
	    $relation['ElderBrother1'] = array('ElderBrother1Name','ElderBrother1Work','ElderBrother1DOB','ElderBrother1kanaName');
	    $relation['ElderBrother2'] = array('ElderBrother2Name','ElderBrother2Work','ElderBrother2DOB','ElderBrother2kanaName');
	    $relation['ElderBrother3'] = array('ElderBrother3Name','ElderBrother3Work','ElderBrother3DOB','ElderBrother3kanaName');
	    $relation['ElderBrother4'] = array('ElderBrother4Name','ElderBrother4Work','ElderBrother4DOB','ElderBrother4kanaName');
	    $relation['ElderBrother5'] = array('ElderBrother5Name','ElderBrother5Work','ElderBrother5DOB','ElderBrother5kanaName');
	    $relation['ElderBrother6'] = array('ElderBrother6Name','ElderBrother6Work','ElderBrother6DOB','ElderBrother6kanaName');

	    $relation['ElderSister1'] = array('ElderSister1Name','ElderSister1Work','ElderSister1DOB','ElderSister1kanaName');
	    $relation['ElderSister2'] = array('ElderSister2Name','ElderSister2Work','ElderSister2DOB','ElderSister2kanaName');
	    $relation['ElderSister3'] = array('ElderSister3Name','ElderSister3Work','ElderSister3DOB','ElderSister3kanaName');
	    $relation['ElderSister4'] = array('ElderSister4Name','ElderSister4Work','ElderSister4DOB','ElderSister4kanaName');
	    $relation['ElderSister5'] = array('ElderSister5Name','ElderSister5Work','ElderSister5DOB','ElderSister5kanaName');
	    $relation['ElderSister6'] = array('ElderSister6Name','ElderSister6Work','ElderSister6DOB','ElderSister6kanaName');
	    if ($gender == 2) {
	      $relation['Husband'] = array('RelationName1','Relation1','Relation1DOB','Relationkana1');
	    } else {
	      $relation['Wife'] = array('RelationName1','Relation1','Relation1DOB','Relationkana1');
	    }
	    $relation['Children1'] = array('RelationName2','Relation2','Relation2DOB','Relationkana2');
	    $relation['Children2'] = array('RelationName3','Relation3','Relation3DOB','Relationkana3');
	    $relation['Children3'] = array('RelationName4','Relation4','Relation4DOB','Relationkana4');
	    $relation['Children4'] = array('RelationName5','Relation5','Relation5DOB','Relationkana5');
	    $querySelect = "";
	    for ($i=0; $i < count($fam_list); $i++) {
	      for ($j=0; $j < $relationUsed; $j++) {
	        if ( ($i == count($fam_list)-1) && $j == ($relationUsed-1) ) {
	          $comma = "";
	        } else {
	          $comma = ",";
	        }
	        $querySelect .= $relation[$fam_list[$i]][$j].$comma;
	      }
	    }
	    if ($querySelect != "") {
	      $empfamilyrelation = DB::TABLE('inv_taxdetails')
	                                ->SELECT(DB::raw($querySelect))
	                                ->WHERE('Emp_ID', '=', $empid)
	                                ->get();
	      if(count($fam_list) != 0) {
	        $k=0;
	        $a=0;
	        foreach ($empfamilyrelation as $userfamily) {
	          for ($k=0; $k < count($fam_list) ; $k++) {
	            for ($a=0; $a < $relationUsed+1; $a++){
	                if (isset($relation[$fam_list[$k]][$a])) {
	                  $objectidntity = $relation[$fam_list[$k]][$a];
	                  $familyselect[$k][$a] = $userfamily->$objectidntity;
	                }
	              if ($a==4) {
	                if (strpos($fam_list[$k], "Sister") != "") {
	                  $fam_list[$k] = "ElderSister";
	                }
	                if (strpos($fam_list[$k], "Brother") != "") {
	                  $fam_list[$k] = "ElderBrother";
	                }
	                if (strpos($fam_list[$k], "dren") != "") {
	                  $fam_list[$k] = "Children";
	                }
	                $familyselect[$k][$a] = $fam_list[$k];
	              }
	            }
	            if (isset($diffArray[$k])) {
	              if ($fam_listOrder[$k] == $diffArray[$k]) {
	                  $familyselect[$k][5] = 1;
	                  $familyselect[$k][6] = $fam_listOrder[$k];
	              }
	            } else {
	                $familyselect[$k][5] = 0;
	                $familyselect[$k][6] = $fam_listOrder[$k];
	            }
	          }
	        }
	      }
	    }
	    }
	  	return $familyselect;
  	}
  	public static function getfamilyDetails($empid) {
  	$familyselect = array();
    $familydetails = DB::TABLE('inv_taxdetails')
            ->SELECT('emp_mstemployees.id', 
                     'inv_taxdetails.Emp_ID', 
                     'emp_mstemployees.LastName',
                     'inv_taxdetails.FatherName',
                     'inv_taxdetails.FatherDOB',
                     'inv_taxdetails.MotherName',
                     'inv_taxdetails.MotherDOB',
                     'inv_taxdetails.GrandFatherName',
                     'inv_taxdetails.GrandFatherDOB',
                     'inv_taxdetails.GrandMotherName',
                     'inv_taxdetails.GrandMotherDOB',
                     'inv_taxdetails.ElderBrother1Name',
                     'inv_taxdetails.ElderBrother1DOB',
                     'inv_taxdetails.ElderBrother2Name',
                     'inv_taxdetails.ElderBrother2DOB',
                     'inv_taxdetails.ElderBrother3Name',
                     'inv_taxdetails.ElderBrother3DOB',
                     'inv_taxdetails.ElderBrother4Name', 
                     'inv_taxdetails.ElderBrother4DOB',
                     'inv_taxdetails.ElderBrother5Name',
                     'inv_taxdetails.ElderBrother5DOB',
                     'inv_taxdetails.ElderBrother6Name',
                     'ElderBrother6DOB',
                     'ElderSister1Name',
                     'ElderSister1DOB',
                     'ElderSister2Name',
                     'ElderSister2DOB',
                     'ElderSister3Name',
                     'ElderSister3DOB',
                     'ElderSister4Name',
                     'ElderSister4DOB',
                     'ElderSister5Name',
                     'ElderSister5DOB',
                     'ElderSister6Name',
                     'ElderSister6DOB',
                     'RelationName1',
                     'Relation1DOB',
                     'RelationName2',
                     'Relation2DOB',
                     'RelationName3',
                     'Relation3DOB',
                     'RelationName4',
                     'Relation4DOB',
                     'RelationName5',
                     'Relation5DOB')
            	->LEFTJOIN('emp_mstemployees', 'inv_taxdetails.Emp_ID', '=', 'emp_mstemployees.Emp_ID')
            	->WHERE('inv_taxdetails.Emp_ID', '=', $empid)
            	->get();
            	// ->Tosql();
            	// dd($familydetails);
    foreach ($familydetails as $familymembers) {
      $familyselect['0']=$familymembers->id;
      $familyselect['1']=$familymembers->LastName;
      $familyselect['2']=$familymembers->FatherName."/".$familymembers->FatherDOB;
      $familyselect['3']=$familymembers->MotherName."/".$familymembers->MotherDOB;
      $familyselect['4']=$familymembers->GrandFatherName."/".$familymembers->GrandFatherDOB;
      $familyselect['5']=$familymembers->GrandMotherName."/".$familymembers->GrandMotherDOB;
      $familyselect['6']=$familymembers->ElderBrother1Name."/".$familymembers->ElderBrother1DOB;
      $familyselect['7']=$familymembers->ElderBrother2Name."/".$familymembers->ElderBrother2DOB;
      $familyselect['8']=$familymembers->ElderBrother3Name."/".$familymembers->ElderBrother3DOB;
      $familyselect['9']=$familymembers->ElderBrother4Name."/".$familymembers->ElderBrother4DOB;
      $familyselect['10']=$familymembers->ElderBrother5Name."/".$familymembers->ElderBrother5DOB;
      $familyselect['11']=$familymembers->ElderBrother6Name."/".$familymembers->ElderBrother6DOB;
      $familyselect['12']=$familymembers->ElderSister1Name."/".$familymembers->ElderSister1DOB;
      $familyselect['13']=$familymembers->ElderSister2Name."/".$familymembers->ElderSister2DOB;
      $familyselect['14']=$familymembers->ElderSister3Name."/".$familymembers->ElderSister3DOB;
      $familyselect['15']=$familymembers->ElderSister4Name."/".$familymembers->ElderSister4DOB;
      $familyselect['16']=$familymembers->ElderSister5Name."/".$familymembers->ElderSister5DOB;
      $familyselect['17']=$familymembers->ElderSister6Name."/".$familymembers->ElderSister6DOB;

      $familyselect['18']=$familymembers->RelationName1."/".$familymembers->Relation1DOB;
      $familyselect['19']=$familymembers->RelationName2."/".$familymembers->Relation2DOB;
      $familyselect['20']=$familymembers->RelationName3."/".$familymembers->Relation3DOB;
      $familyselect['21']=$familymembers->RelationName4."/".$familymembers->Relation4DOB;
      $familyselect['22']=$familymembers->RelationName5."/".$familymembers->Relation5DOB;
    }
    return $familyselect;
  }
  public static function fnGettaxviewDetails($empid) {
  	$employee = DB::TABLE('inv_taxdetails')
					->SELECT('emp_mstemployees.LastName',
								'inv_taxdetails.Emp_ID as Emp_ID', 
								'inv_taxdetails.empInsurence',
								'inv_taxdetails.citizenShip',
								'inv_taxdetails.excelFlg',
								'visarenew.visaStatus',
								'visastatus.JapNM',
								'emp_mstemployees.Emp_ID as empuserid',
								'emp_mstemployees.DOB',
								'emp_mstemployees.FirstName',
								'emp_mstemployees.KanaFirstName',
								'emp_mstemployees.KanaLastName',
								'emp_mstemployees.Gender',
								'mstaddress.*',
								DB::raw("CONCAT('〒',pincode,' ', jpstate,'',jpaddress,'-',roomno,'号') AS full_address"))
					->RIGHTJOIN('emp_mstemployees','inv_taxdetails.Emp_ID','=','emp_mstemployees.Emp_ID')
					->LEFTJOIN('mstaddress','inv_taxdetails.address','=','mstaddress.id')
					->LEFTJOIN('visarenew','inv_taxdetails.Emp_ID','=','visarenew.Emp_ID')
					->LEFTJOIN('visastatus','visarenew.visaStatus','=','visastatus.VisaCD');
		$employee = $employee->where( function($joincont) {
                        $joincont->where('emp_mstemployees.Title', '=', 2)
                                ->orWhere('emp_mstemployees.Title', '=', 3);
                        })
                    ->WHERE('emp_mstemployees.resign_id',0)
                    ->WHERE('emp_mstemployees.delFlg',0)
                    ->WHERE('emp_mstemployees.Emp_ID', '=', $empid);
        $employee = $employee->groupBy('emp_mstemployees.Emp_ID')
        						->get();
        						// ->tosql();
        						// dd($employee);
    	// print_r($employee); exit;
		return $employee;
  	}
  	public static function insertfamilyDetails($request) {
	    for ($i=0;$i<count($request->selected);$i++) {
	      $emp_chk=self::chkfamid($request->empid, $request->selected[$i]);
	      if($emp_chk == 0) {
	        $insert = DB::table('temp_familydetails')->insert(
	            ['id' => '',
	             'Emp_ID' => $request->empid,
	             'family_relation' => $request->selected[$i],
	             'create_date' => date('Y-m-d H:i:s'),
	             'create_by' => Auth::user()->FirstName,
	             'delflg' => 0,
	             'update_date' => date('Y-m-d H:i:s'),
	             'update_by' => Auth::user()->FirstName]
	          );
	      }
	    }
	    return true;
  	}
  	public static function deletefamilyDetails($request) {
	    for ($i=0;$i<count($request->removed);$i++) {
	      	$emp_chk=self::chkfamid($request->empid, $request->removed[$i]);
	      	if($emp_chk != 0) {
	        	$delete = DB::table('temp_familydetails')
	        					->where('Emp_Id', '=', $request->empid)
	                            ->WHERE('family_relation', '=', $request->removed[$i])
	                            ->delete();
	      	}
	    }
	    return true;
  	}
  	public static function chkfamid($empid,$familyrel) {
     	$update=DB::table('inv_taxdetails')
        			    ->where('Emp_ID', $empid)
			            ->update(
			            ['excelFlg' => 0]
			          );
    	$chkfamid = DB::TABLE('temp_familydetails')
                                ->SELECT('Emp_Id')
                                ->WHERE('Emp_ID', '=', $empid)
                                ->WHERE('family_relation', '=', $familyrel)
                                ->count();
    	return $chkfamid; 
  	}
  	public static function getAllEmpDetails() {
	    $unselectedEmployees = DB::TABLE('emp_mstemployees')
	                                ->SELECT('Emp_ID',
	                                         'FirstName',
	                                         'LastName')
	                                ->WHERE('resign_id', '=', 0)
	                                ->WHERE('delFLg', '=', 0);
	    $unselectedEmployees = $unselectedEmployees->where( function($joincont) {
	                              $joincont->where('title', '=', 2);
	                              })->whereNotIn('Emp_ID', function($query)
	                              {
	                                  $query->select(DB::raw('Emp_ID'))
	                                        ->from('temp_empview');
	                              })->orderBy('Emp_ID', 'ASC')
	                                ->get();
	    return $unselectedEmployees;
	}
	public static function getAllFilteredEmpDetails() {
	    $selectedEmployees = DB::TABLE('temp_empview')
	                                ->SELECT('emp_mstemployees.Emp_ID',
	                                         'emp_mstemployees.FirstName',
	                                         'emp_mstemployees.LastName')
	                                ->LEFTJOIN('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'temp_empview.Emp_ID')
	                                ->WHERE('emp_mstemployees.resign_id', '=', 0)
	                                ->WHERE('emp_mstemployees.delFLg', '=', 0);
	     $selectedEmployees = $selectedEmployees->where( function($joincont) {
	                              $joincont->where('emp_mstemployees.title', '=', 2);
	                              })->orderBy('emp_mstemployees.Emp_ID', 'ASC')
	                                ->get();
	    return $selectedEmployees;
  	}
  	public static function InsertEmpFlrDetails($request) {
  		$empViewTrancate = DB::table('temp_empview')->truncate();
	    for ($i=0;$i<count($request->selected);$i++) {
				$insert = DB::table('temp_empview')->insert(
				    ['id' => '',
				     'Emp_Id' => $request->selected[$i],
				     'delflg' => 0,
				 	 'create_date' => date('Y-m-d H:i:s'),
	             	 'create_by' => Auth::user()->username,
	             	 'update_date' => date('Y-m-d H:i:s'),
	             	 'update_by' => Auth::user()->username]
				);
	    }
	    return true;
  	}
  	public static function deleteEmpFlrDetails($request) {
    	for ($i=0;$i<count($request->removed);$i++) {
			$emp_chk=self::chkempid($request->removed[$i]);
				if($emp_chk != 0) {
				$delete = DB::table('temp_empview')->where('Emp_Id', '=', $request->removed[$i])->delete();
				}
    	}
    	return true;
  	}
  	public static function chkempid($empid) {
    	$chkempid = DB::TABLE('temp_empview')
                                ->SELECT('Emp_Id')
                                ->WHERE('Emp_ID', '=', $empid)
                                ->count();
    	return $chkempid; 
  	}
  	public static function fnFetchSalaryDetails($empid,$previousYear) {
  		$empSalary = DB::TABLE('inv_salaryplus')
					->SELECT(DB::raw("FORMAT(SUM(Basic+OT+Bonus+Travel+MonthlyTravel)-SUM(HrAllowance+`Leave`+ESI+IT),0) AS Total"))
					->WHERE('Emp_ID','=',$empid)
					->WHERE('year','=',$previousYear)
					->WHERE('delFlg',0) 
					->get();
  		return $empSalary;
  	}
  	public static function fnFetchTaxDetails() {
  		return null;
  	}
}