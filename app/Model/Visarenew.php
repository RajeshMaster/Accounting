<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use PHPExcel_Worksheet_MemoryDrawing;
use Carbon\Carbon;
class Visarenew extends Model {
  public static function fnGetVisaRenewDetails($request) {
      $visadetails = DB::TABLE(DB::raw('(SELECT * FROM (SELECT * FROM visarenew WHERE
                                        VisaExpiryDate IS NOT NULL ORDER BY VisaExpiryDate DESC) AS t GROUP BY Emp_ID) AS visarenew'))
                          ->SELECT('visarenew.*','emp_mstemployees.LastName','emp_mstemployees.Emp_ID as empuserid','visastatus.VisaNM as NewVisaStatus','visaposition.VisaPosNM as NewVisaPosition','emp_mstemployees.FirstName',DB::raw("DATEDIFF(IFNULL(visarenew.VisaExpiryDate,CURDATE()),CURDATE()) as Validity"),
                            DB::raw("DATEDIFF(NOW(), visarenew.visaApplyedDate) AS days_diff"))
                          ->RIGHTJOIN('emp_mstemployees','visarenew.Emp_ID','=','emp_mstemployees.Emp_ID')
                          ->LEFTJOIN('visastatus','visarenew.visaStatus','=','visastatus.VisaCD')
                          ->LEFTJOIN('visaposition','visarenew.VisaPosNM','=','visaposition.VisaPosCD')
                          ->WHERE('emp_mstemployees.Title',2)
                          ->WHERE('emp_mstemployees.resign_id',0)
                          ->WHERE('emp_mstemployees.delFlg',0)
                          ->GROUPBY('emp_mstemployees.Emp_ID')
                          ->ORDERBY('visarenew.VisaExpiryDate', 'DESC')
                          ->ORDERBY('days_diff', 'DESC')
                          ->paginate($request->plimit);
      return $visadetails;
  }
  public static function fnGetVisaRenewdata($request) {
      $visadetails = DB::TABLE('visarenew')
                          ->SELECT('visarenew.*','emp_mstemployees.Mobile1','visastatus.VisaNM as NewVisaStatus','emp_mstemployees.Gender','emp_mstemployees.DOJ','emp_mstemployees.Address1','emp_mstemployees.DOB','sysdesignationtypes.DesignationNM','emp_mstemployees.LastName','emp_mstemployees.FirstName',DB::raw("CONCAT('〒',pincode,' ', jpstate,'',jpaddress,'-',roomno,'号') AS full_address"))
                          ->LEFTJOIN('emp_mstemployees','visarenew.Emp_ID','=','emp_mstemployees.Emp_ID')
                          ->LEFTJOIN('sysdesignationtypes','visarenew.designation','=','sysdesignationtypes.DesignationCD')
                          ->LEFTJOIN('visastatus','visarenew.visaStatus','=','visastatus.VisaCD')
                          ->LEFTJOIN('mstaddress','visarenew.Address','=','mstaddress.id')
                          ->WHERE('visarenew.Emp_ID', $request->Emp_ID)
                          ->WHERE('visarenew.visaNo', $request->visanumb)
                          ->ORDERBY('visarenew.visaExpiryDate', 'DESC')
                          ->LIMIT(1)
                          ->GET();
      return $visadetails;   
  }
	public static function fnGetEmployeewithVisaCount() {
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
	public static function fnGetEmployeeDetailsMB($empid) {
		  $db = DB::connection('otherdb');
      $EmpDetails = $db->table('emp_mstemployees')
                      ->SELECT('emp_mstemployees.Emp_ID','emp_mstemployees.Religion','emp_mstemployees.MartialStatus','emp_mstemployees.Address1',DB::raw("(SELECT DOE FROM `mstpassport` WHERE user_id = '$empid' order by DOE DESC limit 1) as passportexipry"),DB::raw("(SELECT PassportNo FROM `mstpassport` WHERE user_id = '$empid' order by DOE DESC limit 1) as passportnumber"),DB::raw("(SELECT placeofBirth FROM `mstpassport` WHERE user_id = '$empid' order by DOE DESC limit 1) as placeofBirth"),DB::raw("(select `VisaNM` from `mstalien` left join `visastatus` on `visastatus`.`VisaCD` = `mstalien`.`VisaStatus` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaStatus"),DB::raw("(select `VisaPosNM` from `mstalien` left join `visaposition` on `visaposition`.`VisaPosCD` = `mstalien`.`VisaPosition` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaPosition"),DB::raw("(select `CardNo` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as Visanumber"),DB::raw("(select `EdDate` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as Visaexpirydate"),DB::raw("(select `StDate` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as Visastartdate"),DB::raw("(select `applydate` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaAppliedDate"),DB::raw("(select `appliedplace` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaAppliedPlace"),DB::raw("(select `returneddate` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaReturnedDate"),DB::raw("(select `returnedplace` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaReturnedPlace"),DB::raw("(select `visapurpose` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaPurpose"),DB::raw("(select `returnstatus` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as VisaReturnStatus"),DB::raw("(select `NoYears` from `mstalien` where `user_id` = '$empid' and `EdDate` is not null order by `EdDate` desc limit 1) as Visastayperiod"))
                      ->WHERE('emp_mstemployees.delFlg',0)
                      ->WHERE('emp_mstemployees.resign_id',0)
                      ->WHERE('emp_mstemployees.title',2)
                      ->WHERE('emp_mstemployees.microbitflg',0)
                      ->WHERE('emp_mstemployees.Emp_ID',$empid)
                      ->ORDERBY('emp_mstemployees.Emp_ID', 'ASC')
                      ->GET();
        // 163 Employees
        return $EmpDetails;
	}
	public static function fnOldTempstaffExist($empid) {
		$sql= DB::table('visarenew')
						->SELECT('*')
						->WHERE('Emp_Id', '=', $empid)
						->get();
		return $sql;
	}
  public static function fnInsertOLDMBDetails($valuearray,$oldDateofJoin,$certificateName,$nickName) {
      $db = DB::connection('mysql');
      $result=0;
      for ($i=0; $i < count($valuearray); $i++) {
        $exitcheck = self::fncheckrecodexits($valuearray[$i]->CardNo, $valuearray[$i]->user_id);
          if (empty($exitcheck)) {
            $result= $insert=DB::table('visarenew')->insert(
              ['Emp_ID' => $valuearray[$i]->user_id,
                'martialStatus' => $valuearray[$i]->MartialStatus,
                'religion' => $valuearray[$i]->Religion,
                'designation' => $valuearray[$i]->Designation,
                'passportNo' => $valuearray[$i]->passportnumber,
                'passportExpiryDate' => $valuearray[$i]->passportexipry,
                'placeofBirth' => $valuearray[$i]->placeofBirth,
                'Address' => $valuearray[$i]->Address1,
                'visaNo' => $valuearray[$i]->CardNo,
                'visaStatus' => $valuearray[$i]->VisaStatus,
                'VisaPosNM' => $valuearray[$i]->VisaPosition,
                'visaValidPeriod' => $valuearray[$i]->NoYears,
                'visaStartDate' => $valuearray[$i]->StDate,
                'visaExpiryDate' => $valuearray[$i]->EdDate,
                'visaApplyedDate' => $valuearray[$i]->applydate,
                'appliedplace' => $valuearray[$i]->appliedplace,
                'returneddate' => $valuearray[$i]->returneddate,
                'returnedplace' => $valuearray[$i]->returnedplace,
                'visapurpose' => $valuearray[$i]->visapurpose,
                'returnstatus' => $valuearray[$i]->returnstatus,
                'Image1' => $valuearray[$i]->Image1,
                'rdoImg1' => $valuearray[$i]->rdoImg1,
                'Image2' => $valuearray[$i]->Image2,
                'rdoImg2' => $valuearray[$i]->rdoImg2,
                'Image3' => $valuearray[$i]->Image3,
                'rdoImg3' => $valuearray[$i]->rdoImg3,
                'Image4' => $valuearray[$i]->Image4,
                'rdoImg4' => $valuearray[$i]->rdoImg4,
                'Image5' => $valuearray[$i]->Image5,
                'rdoImg5' => $valuearray[$i]->rdoImg5,
                'citizenShip' => $valuearray[$i]->citizenShip,
                'indiaAddress' => $valuearray[$i]->passportAddress,
                'degreeType' => $valuearray[$i]->educationType,
                'degreeName' => $valuearray[$i]->degreeName,
                'depatmentName' => $valuearray[$i]->departmentName,
                'universityName' => $valuearray[$i]->universityName,
                'departmentothers' => $valuearray[$i]->otherDepartName,
                'universityothers' => $valuearray[$i]->otherUnivName,
                'specificationothers' => $valuearray[$i]->otherDegreeName,
                'city' => $valuearray[$i]->city,
                'complete_year' => $valuearray[$i]->completedYear,
                'complete_month' => $valuearray[$i]->CompletedMonth,
                'sathisysDOJ' => $oldDateofJoin,
                'certificateName' => $certificateName,
                'certificateNickName' => $nickName,
                'created_by' => Auth::user()->username,
                'created_time' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->username,
                'updated_time' => date('Y-m-d H:i:s'),
              ]);
        } else {
            $result=DB::table('visarenew')
                  ->where('Emp_ID', $valuearray[$i]->user_id)
                  ->where('visaNo', $valuearray[$i]->CardNo)
                  ->update([
                            'martialStatus' => $valuearray[$i]->MartialStatus,
                            'religion' => $valuearray[$i]->Religion,
                            'designation' => $valuearray[$i]->Designation,
                            'passportNo' => $valuearray[$i]->passportnumber,
                            'passportExpiryDate' => $valuearray[$i]->passportexipry,
                            'placeofBirth' => $valuearray[$i]->placeofBirth,
                            'Address' => $valuearray[$i]->Address1,
                            'visaStatus' => $valuearray[$i]->VisaStatus,
                            'VisaPosNM' => $valuearray[$i]->VisaPosition,
                            'visaValidPeriod' => $valuearray[$i]->NoYears,
                            'visaStartDate' => $valuearray[$i]->StDate,
                            'visaExpiryDate' => $valuearray[$i]->EdDate,
                            'visaApplyedDate' => $valuearray[$i]->applydate,
                            'appliedplace' => $valuearray[$i]->appliedplace,
                            'returneddate' => $valuearray[$i]->returneddate,
                            'returnedplace' => $valuearray[$i]->returnedplace,
                            'visapurpose' => $valuearray[$i]->visapurpose,
                            'returnstatus' => $valuearray[$i]->returnstatus,
                            'Image1' => $valuearray[$i]->Image1,
                            'rdoImg1' => $valuearray[$i]->rdoImg1,
                            'Image2' => $valuearray[$i]->Image2,
                            'rdoImg2' => $valuearray[$i]->rdoImg2,
                            'Image3' => $valuearray[$i]->Image3,
                            'rdoImg3' => $valuearray[$i]->rdoImg3,
                            'Image4' => $valuearray[$i]->Image4,
                            'rdoImg4' => $valuearray[$i]->rdoImg4,
                            'Image5' => $valuearray[$i]->Image5,
                            'rdoImg5' => $valuearray[$i]->rdoImg5,
                            'citizenShip' => $valuearray[$i]->citizenShip,
                            'indiaAddress' => $valuearray[$i]->passportAddress,
                            'degreeType' => $valuearray[$i]->educationType,
                            'degreeName' => $valuearray[$i]->degreeName,
                            'depatmentName' => $valuearray[$i]->departmentName,
                            'universityName' => $valuearray[$i]->universityName,
                            'departmentothers' => $valuearray[$i]->otherDepartName,
                            'universityothers' => $valuearray[$i]->otherUnivName,
                            'specificationothers' => $valuearray[$i]->otherDegreeName,
                            'city' => $valuearray[$i]->city,
                            'complete_year' => $valuearray[$i]->completedYear,
                            'complete_month' => $valuearray[$i]->CompletedMonth,
                            'sathisysDOJ' => $oldDateofJoin,
                            'certificateName' => $certificateName,
                            'certificateNickName' => $nickName,
                            'updated_by' => Auth::user()->username,
                            'updated_time' => date('Y-m-d H:i:s'),
                          ]);
        }  
      }
      return $result;
  }
  public static function fncheckrecodexits($visanumber, $Emp_ID) {
    $fetchrecord = DB::table('visarenew')
            ->SELECT('visaNo')
            ->WHERE('visaNo', '=', $visanumber)
            ->WHERE('Emp_ID', '=', $Emp_ID)
            ->get();
      return $fetchrecord;
  }
  public static function fnupdateAddedVisaDetails($request) {
    $db = DB::connection('mysql');
    if ($request->crime==2) {
      $request->crimedetail="";
    }
    $update=DB::table('visarenew')
      ->where('id', $request->visaid)
      ->update([
          'VisaExtensionPeriod' => $request->extyear,
          'ReasonforExtension' => $request->resonforext,
          'CrimeRecord' => $request->crime,
          'CrimeDetails' => $request->crimedetail,
          'delFlg' => 1,
      ]);
    return $update;
  }
  public static function fngetTableFields() {
    $db = DB::connection('mysql');
    $tbinsert = "visarenew";
    $auto_increment = $db->select('show columns from ' . $tbinsert);
    return $auto_increment;
  }
  public static function fnGetAllVisaDetailsMB($Emp_ID) {
    $db = DB::connection('otherdb');
    $employee_tableName = "mstalien";
    $EmpDetails = $db->table('mstalien')
                      ->SELECT('mstalien.*','emp_mstemployees.Religion','visaposition.VisaPosNM','visastatus.VisaNM','emp_mstemployees.MartialStatus','emp_mstemployees.Old_ID','emp_mstemployees.Designation','emp_mstemployees.Address1','emp_mstemployees.citizenShip',DB::raw("(SELECT DOE FROM `mstpassport` WHERE user_id = '$Emp_ID' order by DOE DESC limit 1) as passportexipry"),DB::raw("(SELECT Address FROM `mstpassport` WHERE user_id = '$Emp_ID' order by DOE DESC limit 1) as passportAddress"),DB::raw("(SELECT PassportNo FROM `mstpassport` WHERE user_id = '$Emp_ID' order by DOE DESC limit 1) as passportnumber"),DB::raw("(SELECT placeofBirth FROM `mstpassport` WHERE user_id = '$Emp_ID' order by DOE DESC limit 1) as placeofBirth"),DB::raw("(SELECT specification FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as educationType"),DB::raw("(SELECT degree FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as degreeName"),DB::raw("(SELECT depatment FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as departmentName"),DB::raw("(SELECT university FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as universityName"),DB::raw("(SELECT degreeothers FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as otherDegreeName"),DB::raw("(SELECT deptothers FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as otherDepartName"),DB::raw("(SELECT univothers FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as otherUnivName"),
                        DB::raw("(SELECT specificationothers FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as otherSpecName"),DB::raw("(SELECT city FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as city"),DB::raw("(SELECT complete_year FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as completedYear"),DB::raw("(SELECT complete_month FROM `emp_msteducations` WHERE employee_id = '$Emp_ID' order by complete_year DESC limit 1) as CompletedMonth"))
                      ->LEFTJOIN('emp_mstemployees','mstalien.user_id','=','emp_mstemployees.Emp_ID')
                      ->LEFTJOIN('visastatus','mstalien.VisaStatus','=','visastatus.VisaCD')
                      ->LEFTJOIN('visaposition','mstalien.VisaPosition','=','visaposition.VisaPosCD')
                      ->WHERE('user_id',$Emp_ID)
                      ->GET();
        return $EmpDetails;
  }
  public static function fnRegisterAddedVisaDetails($request) {
      if ($request->crime == 2) {
          $request->crimedetail = "";
      }
      $insert=DB::table('visarenew')->insert(
      [
      'Emp_ID' => $request->Emp_ID,
      'martialStatus' => $request->maritalstate,
      'designation' => $request->ocupation,
      'religion' => $request->religion,
      'passportNo' => $request->passportnumb,
      'passportExpiryDate' => $request->passportexipry,
      'placeofBirth' => $request->placeofBirth,
      'Address' => $request->address,
      'visaNo' => $request->visanumber,
      'visaStatus' => $request->statusofresid,
      'VisaPosNM' => $request->visaposition,
      'visaValidPeriod' => $request->stayperiod,
      'visaStartDate' => $request->startdate,
      'visaExpiryDate' => $request->EdDate,
      'visaExtensionPeriod' => $request->extyear,
      'reasonforExtension' => $request->resonforext,
      'crimeRecord' => $request->crime,
      'crimeDetails' => $request->crimedetail,
      'delFlg' => 1,
      'created_time' => date('Y-m-d h:i:s'),
      'created_by' => Auth::user()->username,
      'updated_time' => date('Y-m-d h:i:s'),
      'updated_by' => Auth::user()->username]
    );
    return $insert;
  }
  public static function fnGetPngImageForDrawing($imagePath,$coordinate,$objTpl) {
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample_image');
        $objDrawing->setDescription('Sample_image');
        $objDrawing->setImageResource($imagePath);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
        if ($coordinate == 'C52') {
          $objDrawing->setHeight(18);
          $objDrawing->setWidth(21);  
        } else {
          $objDrawing->setHeight(18);
          $objDrawing->setWidth(21);
        }
        $objDrawing->setCoordinates($coordinate);
        $objDrawing->setWorksheet($objTpl->getActiveSheet());
        return;
  }
  public static function fnGetOldJoiningDate($Old_ID) {
      $db = DB::connection('mysql_SS');
      $oldDateofJoin = $db->TABLE('emp_mstemployees')
                          ->SELECT('Emp_ID','Old_ID','DOJ')
                          ->WHERE('Emp_ID', '=', $Old_ID)
                          ->get();
      return $oldDateofJoin;
  }
  public static function fnGetEmployeeCertificateDetails($user_id) {
      $db = DB::connection('otherdb');
      $certificateDetails = $db->TABLE('emp_mstcertificates')
                              ->SELECT(DB::RAW("group_concat(`emp_certification`.`certificate_name` separator ',') as `certificateName`"),DB::RAW("group_concat(`emp_certification`.`nickname` separator ',') as `nickName` "))
                              ->LEFTJOIN('emp_certification','emp_mstcertificates.certificate_name','=','emp_certification.id')
                              ->WHERE('employee_id',$user_id)
                              ->GET();
                              // ->tosql();
          return $certificateDetails;
  }
}