<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

use DB;
use Session;
use Input;
use Auth;
use File;

class NonStaff extends Model {
	public static function fnGetNonStaffDetails($request,$resignid){

		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees')
					 ->select('*')
					 ->WHERE('Emp_ID','LIKE','%NST%')
					 ->WHERE('resign_id','=',$resignid);
		if ($request->searchmethod == 1) {
			$query = $query->where(function($joincont) use ($request) {
                    $joincont->where('Emp_ID', 'LIKE', '%NST%' . $request->singlesearch . '%')
                    		 ->orwhere('FirstName', 'LIKE', '%' . $request->singlesearch . '%')
                    		 ->orwhere('LastName', 'LIKE', '%' . $request->singlesearch . '%')
                    		 ->orwhere('nickname', 'LIKE', '%' . $request->singlesearch . '%');
                            });
		 } elseif ($request->searchmethod == 2) {
		 		$query = $query->where(function($joincont) use ($request) {
                  	  $joincont->where('Emp_ID','LIKE','%'.$request->employeeno . '%');
                  	  //s ->toSql();dd($query);
                      });
        if(!empty($request->startdate) && !empty($request->enddate)) {
		     	$query = $query->whereBetween('DOJ', [$request->startdate, $request->enddate]);
		          }
      	if(!empty($request->startdate) && empty($request->enddate)) {
        	 	$query = $query->where('DOJ', '=', $request->startdate);
		          }
      	if(!empty($request->enddate) && empty($request->startdate)) {
          		$query = $query->where('DOJ', '=', $request->enddate);
	         	}

			}
			$query = $query->orderBy($request->nonstaffsort, $request->sortOrder)
					->paginate($request->plimit);	
					 // ->toSql();dd($query);
		return $query;
	}
	public static function empidprocess() {
		$query = DB::select("SELECT CONCAT('NST', LPAD(MAX(SUBSTRING(Emp_ID,4))+1,5,0)) AS nstid FROM emp_mstemployees WHERE Emp_ID LIKE '%NST%'");
		return $query;
	}
	public static function addprocess($request, $filename,$EmployeeId) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$result= $insert=DB::table('emp_mstemployees')->insert(
			['id' => '',
			'Emp_ID' => $EmployeeId,
			'DOJ' => $request->OpenDate,
			'FirstName' => $request->Surname,
			'LastName' => $request->Name,
			'nickname' => $request->NinkName,
			'KanaFirstName' => $request->KanaFirstName,
			'KanaLastName' => $request->KanaLastName,
			'Gender' => $request->Gender,
			'DOB' => $request->DateofBirth,
			'Mobile1' => $request->MobileNo,
			'Emailpersonal' => $request->Email,
			'Picture' => $filename,
			'Address1' => $request->StreetAddress,
			'Ins_DT' => date('Y-m-d'),
			'Ins_TM' => date('h:i:s'),
			'CreatedBy' => $name,
			'Up_DT' => date('Y-m-d'),
			'Up_TM' => date('h:i:s'),
			'Title' => 2,
			'resign_id' => 0,
			'delflg' => 0,
			'BankName' => $request->BankName,
			'BranchName' => $request->BranchName,
			'AccNo' => $request->AccountNo,
			'BranchNo' => $request->BranchNo ]
		);
		return $result;
	}
	public static function fnGetnonstaffDetail($request){
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees')
					->select('*')
					->where([['Emp_ID', '=', $request->viewid]])
					->get();
		return $query;
	}
	public static function updateprocess($request, $imagename) {
		$name = Session::get('FirstName').' '.Session::get('LastName');
		$db = DB::connection('mysql');
		$update=DB::table('emp_mstemployees')
		->where('Emp_ID', $request->hdnempid)
		->update(
			[
			'DOJ' => $request->OpenDate,
			'FirstName' => $request->Surname,
			'LastName' => $request->Name,
			'nickname' => $request->NinkName,
			'KanaFirstName' => $request->KanaFirstName,
			'KanaLastName' => $request->KanaLastName,
			'Gender' => $request->Gender,
			'DOB' => $request->DateofBirth,
			'Mobile1' => $request->MobileNo,
			'Emailpersonal' => $request->Email,
			'Picture' =>  $imagename,
			'Address1' => $request->StreetAddress,
			'Up_DT' => date('Y-m-d'),
			'Up_TM' => date('h:i:s'),
			'UpdatedBy' => $name,
			'BankName' => $request->BankName,
			'BranchName' => $request->BranchName,
			'AccNo' => $request->AccountNo,
			'BranchNo' => $request->BranchNo]
		);
		return $update;
	}
	public static function viewdetails($id) {
		$db = DB::connection('mysql');
		$result= DB::table('emp_mstemployees')
						->SELECT('*')
						->leftJoin('mstaddress AS mst', 'mst.id', '=', 'emp_mstemployees.Address1')
						->WHERE('Emp_ID', '=', $id)
						->get();
		return $result;
	}
	public static function thumbnailUpload($pict,$path,$thumbpath,$max_upload_width,$max_upload_height) {
		$ext = explode(".", $pict);
		$filechk =glob($path."/".$ext[0]."*");
		if( file_exists($filechk[0]) && $pict !="" ){
			if(!File::exists($thumbpath)) {
				// thumbpath does not exist
				File::makeDirectory($thumbpath, 0775, true);
			}
			chmod($thumbpath, 0777);
			// $thumbfilechk =glob($thumbpath."/".$ext[0]."*");
			/* if( file_exists($thumbfilechk[0]) ){
			unlink($thumbfilechk[0]);
			}*/
			list($image_width, $image_height) = getimagesize($path."/".$pict);
			if($image_width>$max_upload_width || $image_height >$max_upload_height){
				if($image_width>$image_height){
					$proportions = $max_upload_width / $image_width;
					$new_width  = $max_upload_width;
					$new_height = round($image_height*$proportions);
				}
				else{
					$proportions = $max_upload_height / $image_height;
					$new_height = $max_upload_height;
					$new_width  = round($image_width*$proportions);
				}     
			$new_image = imagecreatetruecolor($new_width , $new_height);
			$extension =strtolower($ext[1]);
				if($extension == "gif") {
					$image_source = imagecreatefromgif($path."/".$pict);
					imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
					imagegif($new_image, $thumbpath."/".$pict, 100); // save
				}
				if($extension == "jpeg"||$extension == "jpg"||$extension == "pjpeg") {
					$image_source = imagecreatefromjpeg($path."/".$pict);
					imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
					imagejpeg($new_image, $thumbpath."/".$pict, 100); // save
				}
				if($extension == "png"||$extension == "x-png") {
					$image_source = imagecreatefrompng($path."/".$pict);
					imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
					$background = imagecolorallocate($new_image , 0, 0, 0);
					imagecolortransparent($new_image, $background);
					imagepng($new_image, $thumbpath."/".$pict); // save
				}
				if($extension == "bmp") {
					$image_source = imagecreatefrombmp($path."/".$pict);
					imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
					imagewbmp($new_image, $thumbpath."/".$pict, 100); // save
				}
			imagedestroy($new_image);
			}
		}
		return true;
	}
}