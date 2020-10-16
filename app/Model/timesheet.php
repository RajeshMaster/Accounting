<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use DB;
use Session;
use Input;
use Auth;
use now;
use Config;
use Carbon\Carbon ;
class timesheet extends Model { 
	const DEF_PAGE_COUNT = 50;
	public static function getAllemployeeDetails($useridemp,$date) 
	{		
			$splitDate = explode("-", $date);
			$year = $splitDate['0'];
			$month = str_pad($splitDate['1'], 2,"0",STR_PAD_LEFT);
			$date = $year."-".$month;
			if ($useridemp != '') {
		 		$db = DB::connection('mysql');
		 		$query = DB::SELECT(DB::raw("SELECT * FROM emp_mstemployees 
		 								WHERE emp_mstemployees.Emp_ID= '$useridemp' 
		 								AND IF( (SELECT COUNT(*) FROM inv_timesheet_entry AS afterRes 
				          				WHERE afterRes.emp_id = emp_mstemployees.Emp_ID 
				          				AND SUBSTRING(afterRes.workdate,1,7) = '$date'
				          				AND emp_mstemployees.resign_id=1)>0 ,
                          				emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0) AND delFlg=0"));
		 		$i = 0;
		 		$allpgmlang = array();
		 		foreach ($query as $key => $value) {
					$allpgmlang[$i]['id'] = $value->Emp_ID;
					$actual = timesheet::calculation($allpgmlang[$i]['id'],$date);
					$actualminutes = $actual[1] % 60;
	  				$actualhours = ($actual[1] - $actualminutes)/60;
	  				$allpgmlang[$i]['workinghours'] = $actualhours.":".str_pad($actualminutes, 2,"0",STR_PAD_LEFT);
	  				$section = $actual[0];
	  				$allpgmlang[$i]['workplace'] = $actual[2];
	  				$allpgmlang[$i]['section'] = $actual[0];
	  				$allpgmlang[$i]['permission'] = $section[2];
	  				$allpgmlang[$i]['late'] = $section[4];
	  				$allpgmlang[$i]['extradays'] = $actual[3];
	  				$allpgmlang[$i]['createddate'] = $actual[4];
	  				$allpgmlang[$i]['submit'] = $actual[6];
	  				$allpgmlang[$i]['upload_path'] = $actual[5];
					$i++;
				}
			}

		return $allpgmlang;
	}
	public static function getAlldatas($useridemp,$date,$request) {
			$splitDate = explode("-", $date);
			$year = $splitDate['0'];
			$month = str_pad($splitDate['1'], 2,"0",STR_PAD_LEFT);
			$date = $year."-".$month;
		 	$db = DB::connection('mysql');
			$query = $db->TABLE($db->raw("(SELECT * FROM emp_mstemployees 
											WHERE emp_mstemployees.Emp_ID= '$useridemp' 
											AND IF( (SELECT COUNT(*) FROM inv_timesheet_entry AS afterRes  WHERE afterRes.emp_id = emp_mstemployees.Emp_ID 
											AND SUBSTRING(afterRes.workdate,1,7) = '$date'
											AND emp_mstemployees.resign_id=1)>0, emp_mstemployees.resign_id=1,
											emp_mstemployees.resign_id=0) AND delFlg=0) as tb1"))
											->paginate($request->plimit);
		return $query;
	}
	public static function fnGetAccountPeriod() 
	 {
	 	$db = DB::connection('mysql');
	 	$query = $db->table('dev_kessandetails')
	 	 			->where('delflg','=',0)
	 	 			->get();
	 	return $query;	
	 }
	public static function addPreMntTS()
	{

		$db = DB::connection('mysql');
		$previousDtMn = date("Y-m", strtotime("-1 months", strtotime(date('Y-m-01'))));
		$splitPrYrMn = explode("-", $previousDtMn);
		$year = $splitPrYrMn[0];
		$month = $splitPrYrMn[1];
		$chkExist = $db->table('inv_timesheet_temp AS mst')
                    ->SELECT('*')
                    ->JOIN('emp_mstemployees AS emp','emp.Emp_ID','=','mst.Emp_Id')
                    ->WHERE('emp.resign_id', '=', 0)
                    ->WHERE('emp.Title', '=', 2)
                    ->WHERE('mst.year', '=', $year)
                    ->WHERE('mst.month', '=', $month)
                    ->GET();
		if (count($chkExist) == 0) {
			$cntForPreMn = $db->table('inv_timesheet_temp')
                    ->SELECT(DB::raw("DISTINCT  CONCAT(year, '-', month) AS yrmn"))
                    ->ORDERBY('year', 'DESC')
                    ->ORDERBY('month', 'DESC')
                    ->LIMIT(1)
                    ->GET();

			if (count($cntForPreMn) != 0) {
				$cntForPreMnDis = $cntForPreMn[0]->yrmn;
				$splitcntPrYrMn = explode("-", $cntForPreMnDis);
				$Pr_Year = $splitcntPrYrMn[0];
				$Pr_Month = $splitcntPrYrMn[1];
				$chkPreExist = $db->table('inv_timesheet_temp AS mst')
					->SELECT('*')
                   	->JOIN('emp_mstemployees AS emp','emp.Emp_ID','=','mst.Emp_Id')
                    ->WHERE('emp.resign_id', '=', 0)
                    ->WHERE('emp.Title', '=', 2)
                    ->WHERE('mst.year', '=',$Pr_Year)
                    ->WHERE('mst.month', '=',$Pr_Month)
                    ->GET();
					foreach ($chkPreExist as $key => $value) {
						$Emp_Id = $value->Emp_Id;
		       			$tbl_name = 'inv_timesheet_temp';
						$insert=$db->table($tbl_name)
		                 ->insertGetId([
		                 'id' =>'',
		                 'Emp_Id' => $Emp_Id,
		                 'delflg' => 0,
		                 'resign_id' => 0,
		                 'title' => 2,
		                 'year' => $year,
		                 'month' => $month,
		                 'create_date' => date('Ymd'),
		                 'create_by' =>  Auth::user()->username,
		                 ]
		              ); 
				}
			}
		}
	}
	public static function fnGetTimeSheetRecord($from_date, $to_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_timesheet_entry AS mst')
					->SELECT(DB::raw("SUBSTRING(workdate, 1, 7) AS workdate"))
					->WHERE('del_flg','=',0)
					->WHERE('workdate','>',$from_date)
					->WHERE('workdate','<',$to_date)
					->ORDERBY('workdate', 'ASC')
	 	 			->GET();
	 	return $sql;
	}
	public static function fnGetTimeSheetRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_timesheet_entry')
					->SELECT(DB::raw("SUBSTRING(workdate, 1, 7) AS workdate"))
					->WHERE('del_flg','=',0)
					->WHERE('workdate','<=',$from_date)
					->ORDERBY('workdate', 'ASC')
	 	 			->GET();
	 	return $sql;
	}

	public static function fnGetTimeSheetRecordNext($to_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_timesheet_entry')
					->SELECT(DB::raw("SUBSTRING(workdate, 1, 7) AS workdate"))
					->WHERE('del_flg','=',0)
					->WHERE('workdate','>=',$to_date)
					->ORDERBY('workdate', 'ASC')
	 	 			->GET();
	 	return $sql;
	}
	public static function getAccountingPeriod($e_accountperiod, $account_period, $account_val) {
		if (!empty($e_accountperiod)) {

			$year_month_day = $e_accountperiod[0]->Closingyear. "-" . $e_accountperiod[0]->Closingmonth . "-01";

			$d1 = new \DateTime($year_month_day);
			$d2 = new \DateTime(date('Y-m-d'));
			$yr_m = date('Y-m');
			$cl_yr_mn = $e_accountperiod[0]->Closingyear. "-" . $e_accountperiod[0]->Closingmonth;
			$diff = $d2->diff($d1);
			$yrMnt[0] = $diff->y;
			$yrMnt[1] = $diff->m;
			if ( $yrMnt[0] > 0 && $yrMnt[1] > 0) {
				$account_val = $account_period + $yrMnt[0] + 1;
			} else if ( $yrMnt[0] > 0 && $yrMnt[1] == 0 ) {
				$account_val = $account_period + $yrMnt[0];
			} else if ($yrMnt[1] > 0 && ($yr_m > $cl_yr_mn)) {
				$account_val = $account_period + 1;
			} else {
				$account_val = $account_period;
			}
			return $account_val;
		}
	}
	public static function fnGetValTimesheetEntry($request) {
		$selYear = $request->selYear;
		$selMonth = $request->selMonth;
		$db = DB::connection('mysql');
		$sql =$db->TABLE($db->raw("(SELECT mu.id,mu.Emp_ID,emp_mstemployees.FirstName,emp_mstemployees.LastName, 
					(SELECT sum(case when workdate LIKE '%$selYear-$selMonth%'=0 then 1 else 0 end)  
						FROM inv_timesheet_entry ts WHERE ts.emp_id= mu.Emp_ID AND ts.workdate LIKE 
						'%$selYear-$selMonth%') AS CNT, 
					(SELECT created_date FROM inv_timesheet_entry ts WHERE ts.emp_id= mu.Emp_ID AND ts.workdate LIKE '%$selYear-$selMonth%' GROUP BY ts.emp_id) AS CREATEDDATE 
					FROM inv_timesheet_temp mu 
					LEFT JOIN emp_mstemployees ON mu.Emp_Id= emp_mstemployees.Emp_ID 
					WHERE mu.year=$selYear and mu.month=$selMonth AND 
					IF( (SELECT COUNT(*) FROM inv_timesheet_entry AS afterRes 
					WHERE afterRes.emp_id = emp_mstemployees.Emp_ID AND 
					SUBSTRING(afterRes.workdate,1,7)='$selYear-$selMonth'
					AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)
					GROUP BY mu.Emp_ID ORDER BY CNT,CREATEDDATE DESC, LastName ASC) as tbl1"))
				->paginate($request->plimit);
			return $sql;
    }
    public static function fnGetEmployeeTimeSheetData($empid,$request) {
    	$selYear = $request->selYear;
		$selMonth = $request->selMonth;
    	$date =$selYear. "-" . $selMonth;
		$db = DB::connection('mysql');
		$sql = $db->table('inv_timesheet_entry')
					->SELECT('*')
					->WHERE('emp_id','=',$empid)
					->WHERE('workdate','LIKE',$date.'%')
	 	 			->GET();

	 	return $sql;
	}
	public static function fnGetSpecificationAsKanji($sectionrow) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_timesheet_specification')
					->SELECT('*')
					->WHERE('id','=',$sectionrow)
	 	 			->GET();
	 	return $sql;
	}

	public static function calculation($user_id, $date)
		{
			$array = array(0,0,0,0,0,0,0,0,0,0);
			$extra = 0;
			$workplace = "";
			$createdate ="";
			$upload_path ="";
			$db = DB::connection('mysql');
			$query = $db->table('inv_timesheet_entry')
					->SELECT('*')
					->WHERE('emp_id','=',$user_id)
					->WHERE('workdate','LIKE',$date.'%')
	 	 			->GET();
	 	 			$actual_val = "";
	 	 			$submit = "";
			foreach ($query as $key => $value) {
				$start = $value->starttime;
				$end = $value->endtime;
				$nonstart = $value->non_work_starttime;
				$nonend = $value->non_work_endtime;
				$section = $value->section;
				if ($section != "" && $section != "null") {
					if (isset($array[$section])) {
						$array[$section]++;
					}
				}
				if (!empty($value->workingplace)) {
					$workplace = $value->workingplace;
				}
				if (empty($val->submit_date)) {
					$submit = $value->submit_date;
				}
				$result = self::calval($start,$end,$nonstart,$nonend);
				$workdate = $value->workdate;
				$timestamp = strtotime($workdate);
				$temp = date('N', $timestamp);
				$createdate = $value->created_date;
				$upload_path = $value->upload_path;
				if(($temp==6 && ($value->starttime!='' && $value->starttime!='00:00:00' ) && ($value->endtime!=''  && $value->endtime!='00:00:00')) 
				|| ($temp==7 && ($value->starttime!='' && $value->starttime!='00:00:00' ) && ($value->endtime!=''  && $value->endtime!='00:00:00')))
				{
					$extra++;
				}
				$actual_val=$actual_val+$result[4];			
			}
			$arr=array($array,$actual_val,$workplace,$extra,$createdate,$upload_path,$submit);
			return $arr;
			}


	public static function calval($workstart,$workend,$nonstart,$nonend)
		{
			$startTime = $workstart;
			$endTime   = $workend;
			$permissionstart=$nonstart;
    		$permissionend=$nonend;
    		$detuctionhours = "";
    		$deductionminutes = "";
    		$startInputHrs = "";
    		$startInputMins = "";
    		$endInputHrs = "";
    		$endInputMins = "";
    		if (isset($startTime)) {
    			$startTimeArray = explode(':',$startTime);
   				$startInputHrs = $startTimeArray[0];
   				$startInputMins = $startTimeArray[1];
			}
			
   			// $dumi = $startTimeArray[2];
			if (isset($endTime)) {
				$endTimeArray = explode(':',$endTime);
   				$endInputHrs = $endTimeArray[0];
   				$endInputMins = $endTimeArray[1];
			}
   			
   			// $dumy = $endTimeArray[2];

  			$startMin = $startInputHrs*60 + $startInputMins;
   			$endMin = $endInputHrs*60 + $endInputMins;

			if ($permissionend) {
	   			$nonstartTimeArray = explode(':',$permissionstart);
	  		 	$nonstartInputHrs = $nonstartTimeArray[0];
	   			$nonstartInputMins = $nonstartTimeArray[1];

	   			$nonendTimeArray = explode(':',$permissionend);
	   			$nonendInputHrs = $nonendTimeArray[0];
	   			$nonendInputMins = $nonendTimeArray[1];

	   			$nonstartMin = $nonstartInputHrs*60 + $nonstartInputMins;
	   			$nonendMin = $nonendInputHrs*60 + $nonendInputMins;
	   			$nonworktime  ="";
	   		   if ($nonendMin < $nonstartMin) {
	       			$nonminutesPerDay = 24*60; 
	       			$nonworktime = $nonminutesPerDay - $nonstartMin;  // Minutes till midnight
	       			$nonworktime += $nonendMin; // Minutes in the next day
	   			}
	   			else 
	   			{
	   				$nonworktime = $nonendMin - $nonstartMin;
	   			}
			}

			$lunch=13*60;
    		$break1=19*60;
       		$break2=22*60;
       		$late=22*60 + 30;
        	$latenight=0;
  			$overtime=0;
   			$tem;
   			$deduction=0;
   			$result=0;

		  	if ($endMin   < $startMin){
		      	 $minutesPerDay = 24*60;
		       	$result = $minutesPerDay - $startMin; // Minutes till midnight
		       	$result += $endMin; /// Minutes in the next day
		  	}
		  	else 
		 	{
		   		if ($endMin > $break2){
		        if ($endMin > $break2 && $endMin < 1350 && $startMin < 840)
		        {
		          if($startMin > 780 && $startMin < 840)
		          {
		            if($endMin > $break2 && $endMin < 1350)
		            {
		              $tem2 = $endMin - $break2;
		              $tem2=$tem2+30;
		            }
		            else
		            {
		              $tem2 = 60;
		            }
		             $lb=840-$startMin;
		             $result = $endMin - $startMin - ($tem2+$lb);
		             $overtime=$result - 8*60;
		             $latenight=$endMin - $late;
		             $latenight=$latenight<0?'':$latenight;
		             $overtime=$overtime - $latenight;
		          }
		          else
		          {
		          $tem2=$endMin - $break2;
		          $result = $endMin - $startMin - ($tem2+90);
		          $overtime=$result - 8*60;
		          $latenight=$endMin - $late;
		          $latenight=$latenight<0?'':$latenight;
		          $overtime=$overtime - $latenight;
		          }        
		        }
		        else if($startMin >= 840 && $endMin < 1350) {
		               $tem5=$endMin - $break2;
		               $result = $endMin - $startMin - (30+$tem5);
		               $overtime=$result - 8*60;
		               $overtime=$overtime<0?'':$overtime; 
		               $latenight=$endMin - $late;   
		               $latenight=$latenight<0?'':$latenight;

		            }
		        else if($startMin >= 840 && $endMin > 1350) {
		               $result = $endMin - $startMin - (60);                
		               $overtime=$result - 8*60;              
		               $overtime=$overtime<0?'':$overtime; 
		               $latenight=$endMin - $late;          
		        }
		        else
		        {
		          $result = $endMin - $startMin - 120;
		          $overtime=$result - 8*60;
		          $latenight=$endMin - $late;
		          $latenight=$latenight<0?'':$latenight;
		          $overtime=$overtime - $latenight;   
		        }
		   		}
		   		else if ($endMin  > $break1)
		   		{
		        if (($endMin > $break1 && $endMin < 1170)||($startMin > 780))
		        {
		          if($startMin > 780 && $startMin < 840)
		          {
		            if($endMin > $break1 && $endMin < 1170)
		            {
		              $tem1=$endMin - $break1;
		            }
		            else
		            {
		              $tem1=30;
		            }
		             $lb=840-$startMin;
		             $result = $endMin - $startMin - ($tem1+$lb);
		             $overtime=$result - 8*60;
		             $overtime=$overtime<0?'':$overtime;
		          }
		          else if($startMin >= 840) {
		          $tem1=$endMin - $break1;
		          $result = $endMin - $startMin - ($tem1);
		          $overtime=$result - 8*60;
		          $overtime=$overtime<0?'':$overtime;  
		          
		        }
		          else
		          {
		          $tem1=$endMin - $break1;
		          $result = $endMin - $startMin - ($tem1+60);
		          $overtime=$result - 8*60;
		          $overtime=$overtime<0?'':$overtime;
		          }
		        }
		        else
		        {
		          $result = $endMin - $startMin - 90;
		          $overtime=$result - 8*60;
		          $overtime=$overtime<0?'':$overtime;   
		        }
		   		
		   		}
		   		else if ($endMin > $lunch) 
		   		{
		   			if ($endMin > $lunch && $endMin < 14 * 60)
		   			{
		   			$tem=$endMin - $lunch;
		   			$result = $endMin - $startMin - $tem;
		   			//$overtime=$result - 8*60;
		   			}
		        else if($startMin >= 840) {
		          $tem1=$endMin - $break1;
		          $result = $endMin - $startMin - ($tem1);
		          $overtime=$result - 8*60;
		          $overtime=$overtime<0?'':$overtime;  
		          
		        }
		   			else
		   			{
		   				$result = $endMin - $startMin - 60;
		   				$overtime=$result - 8*60;   
		   				$overtime=$overtime<0?'':$overtime;				
		   			}

		   		}
		   		else
		   		{
		     		$result = $endMin - $startMin;
		     	 	$overtime= $result - 8*60;
		     	 	$overtime=$overtime<0?'':$overtime;
		   		}

		  	}
		  			 
   			// if ($nonworktime > 0)
    		// {
    		// 	$result = $result - $nonworktime;
    		// 	$nonminutes = $nonworktime % 60;
   			// 	$nonhours = ($nonworktime - $nonminutes) / 60;

    		// }

    		if ($result < 480)
  			 {
   				$deduction= 8*60 - $result;
   				$deduction=$deduction<480?$deduction:"0:0";
   				$deductionminutes = $deduction % 60;
   				$detuctionhours = ($deduction - $deductionminutes) / 60;
   				$detuctionhours=str_pad($detuctionhours,2,"0",STR_PAD_LEFT);
  				 $deductionminutes=str_pad($deductionminutes,2,"0",STR_PAD_LEFT);

 			 }
 			 if($endMin > 1560)
    		{
 			 	if (($endMin > 26*60) && ($endMin < 29 * 60))
   				{
   				$morning=$endMin - 26*60;
   				$result =$result - $morning;
   				$latenight=$latenight - $morning;
   				}
   				else
   				{
   				$result =  $result - 180;
   				$latenight=$latenight - 180;
   				}
   			}

			$actualminutes = $result % 60;
			$actualhours = ($result - $actualminutes)/60;
			$actualhours=str_pad($actualhours,2,"0",STR_PAD_LEFT);
			$actualminutes=str_pad($actualminutes,2,"0",STR_PAD_LEFT);
			$actual=$actualhours.":".$actualminutes;

			$overminutes = $overtime % 60;
			$overhours = ($overtime - $overminutes)/60;
			$overhours=str_pad($overhours,2,"0",STR_PAD_LEFT);
			$overminutes=str_pad($overminutes,2,"0",STR_PAD_LEFT);

			$over=$overhours.":".$overminutes;

			$lateminutes = $latenight % 60;
			$latehours = ($latenight - $lateminutes)/60;
			$latehours=str_pad($latehours,2,"0",STR_PAD_LEFT);
			$lateminutes=str_pad($lateminutes,2,"0",STR_PAD_LEFT);

			$la=$latehours.":".$lateminutes;
			$dut=$detuctionhours.":".$deductionminutes;
			$arr=array($actual,$over,$la,$dut,$result,$overtime,$latenight,$deduction);
			return $arr;
		}

// index end
//importOldTimesheet
	public static function fnGetConnectionQuery($request){
 		$db = DB::connection('mysql');
		$query = $db->table('olddbdetailsregistration')
					->SELECT('*')
					->WHERE('id','=',$request->contentsel)
					->WHERE('Delflg','=',0)
					->GET();
 		return $query;
	}

	public static function fnCheckTableExist($getConnectionQuery){
		self::setotherdbconnection($getConnectionQuery);
		$db = DB::connection('otherdb');
 		$query = $db->table('tbl_time_sheet_download')
 					->SELECT('*')
 					->GET();
 		return $query;
	}
	public static function fnGetOldTimeSheetDetails(){
		$db = DB::connection('otherdb');
		$query = $db->table('tbl_time_sheet_download')
					->SELECT('*')
					->GET();
		return $query;
	}
	public static function setotherdbconnection($getConnectionQuery) {
		$dbName = $getConnectionQuery[0]->DBName;
		$dbUser = $getConnectionQuery[0]->UserName;
		$dbPass = $getConnectionQuery[0]->Password;
		Config::set('database.connections.otherdb.database', $dbName);
		Config::set('database.connections.otherdb.username', $dbUser);
		Config::set('database.connections.otherdb.password', $dbPass);
		$db = DB::connection('otherdb');
	}
	public static function fnOldTimeSheetExist($empid, $workdate) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry')
					->SELECT('*')
					->WHERE('emp_id','=',$empid)
					->WHERE('workdate','=',$workdate)
					->GET();
		return $query;
	}
	public static function fnInsertOldTimeSheetDetails($column_name, $column_value) {
		$db = DB::connection('mysql');
		$insert = DB::insert("INSERT INTO inv_timesheet_entry ($column_name) VALUES ($column_value)");

	}
	public static function fnUpdateOldTimeSheetDetails($column_name_value,$condition){
		$db = DB::connection('mysql');
		$query = DB::update("UPDATE inv_timesheet_entry SET $column_name_value 
							WHERE $condition");
	}
// END importOldTimesheet
	// importOldTempTimesheet
	public static function fnChecktempTableExist($getConnectionQuery){
		self::setotherdbconnection($getConnectionQuery);
		$db = DB::connection('otherdb');
 		$query = $db->table('mst_tsemp')
 					->SELECT('*')
 					->GET();
 		return $query;
	}
	public static function fnGetOldTempTimeSheetDetails(){
		$db = DB::connection('otherdb');
 		$query = $db->table('mst_tsemp')
 					->SELECT('*')
 					->GET();
 		return $query;
	}
	public static function fnOldTempTimeSheetExist($empid,$year,$month) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_temp')
					->SELECT('*')
					->WHERE('Emp_Id','=',$empid)
					->WHERE('year','=',$year)
					->WHERE('month','=',$month)
					->GET();
		return $query;
	}
	public static function fnInsertOldTempTimeSheetDetails($column_name, $column_value) {
		$db = DB::connection('mysql');
		$insert = DB::insert("INSERT INTO inv_timesheet_temp ($column_name) VALUES ($column_value)");
	}
	public static function fnUpdateOldTempTimeSheetDetails($column_name_value,$condition){
		$db = DB::connection('mysql');
		$query = DB::update("UPDATE inv_timesheet_temp SET $column_name_value 
							WHERE $condition");
	}
// END importOldTempTimesheet
	// importOldTempTimesheetSpec
	public static function fnCheckTempTableExistSpec($getConnectionQuery){
		self::setotherdbconnection($getConnectionQuery);
		$db = DB::connection('otherdb');
 		$query = $db->table('mst_tsemp')
 					->SELECT('*')
 					->GET();
 		return $query;
	}
	public static function fnGetOldTimeSheetSpecificationDetails(){
		$db = DB::connection('otherdb');
 		$query = $db->table('timesheet_specification')
 					->SELECT('*')
 					->GET();
 		return $query;
	}
	public static function fnOldTimeSheetSpecificationExist($id) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_specification')
					->SELECT('*')
					->WHERE('id','=',$id)
					->GET();
		return $query;
	}
	public static function fnInsertOldTimeSheetSpecificationDetails($column_name, $column_value) {
		$db = DB::connection('mysql');
		$insert = DB::insert("INSERT INTO inv_timesheet_specification ($column_name) VALUES ($column_value)");
	}
	public static function fnUpdateOldTimeSheetSpecificationDetails($column_name_value,$condition){
		$db = DB::connection('mysql');
		$query = DB::update("UPDATE inv_timesheet_specification SET $column_name_value 
							WHERE $condition");
	}
// END importOldTempTimesheet
// Timesheet Details 
	public static function fngettimesheetdetails($request) {
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT * from (
							select emp_id, substring(workdate, 1, 7) yearmonth, created_date from (select * from inv_timesheet_entry 
								WHERE emp_id = '$request->empid' 
								ORDER BY workdate DESC)inv_timesheet_entry 
								GROUP BY SUBSTRING(workdate, 1, 7) DESC) 
								inv_timesheet_entry) as tbl1"))
								->orderBy($request->timesheetviewsort, $request->sortOrder)
								->paginate($request->plimit);
		return $query;
	}
	public static function fuGetEmpDetails($request) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees') 
					->SELECT('*') 
					->WHERE('Emp_ID','=',$request->empid) 
					->GET();
		return $query;
	}
	public static function fnGetEmployeeTimeSheetDetail($emp_id,$yearmonth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$emp_id) 
					->WHERE('workdate','LIKE',$yearmonth.'%')
					->GET();
		return $query;
	}
	public static function viewkanji($id) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_specification') 
					->SELECT('specification','specenglish','specsymbol') 
					->WHERE('id','=',$id) 
					->GET();
		return $query;
	}
	public static function fnEmpDetail($empid,$yearmonth) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid) 
					->WHERE('workdate','LIKE',$yearmonth.'%')
					->GET();
		return $query;
	}
	public static function datas($empid) {
		$db = DB::connection('mysql');
		$month=date("m", strtotime("-1 months", strtotime(date('Y-m-01'))));	
		$yr=date('Y');
		$da=$yr."-".$month;
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid) 
					->WHERE('workdate','LIKE',$da.'%')
					->GET();
		return $query;
	}
// End of history
// Timesheet View
	public static function empname($Emp_ID) {
		$db = DB::connection('mysql');
		$query = $db->table('emp_mstemployees') 
					->SELECT('FirstName','LastName','KanaFirstName','KanaLastName') 
					->WHERE('Emp_ID','=',$Emp_ID) 
					->GET();
		return $query;
	}
	public static function datadb($empid, $date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid) 
					->WHERE('workdate','LIKE',$date.'%')
					->GET();
		return $query;
	}
	public static function selection($empid, $date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid) 
					->WHERE('workdate','=',$date)
					->LIMIT(1)
					->GET();
		return $query;
	}
// Timesheet AddEdit
	public static function selectboxvalue($request) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_specification') 
					->SELECT('*') 
					->GET();
		foreach ($query as $key => $value) {
			if ($request == 1) {
				$result[$value->id] = $value->specenglish;
			} else if ($request == 2) {
				$result[$value->id] = $value->specification;
			} else {
				$result[$value->id] = $value->specsymbol;
			}
		}
		return $result;
	}
	public static function alldatas($empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid) 
					->GET();
		return $query;
	}
	public static function insertion($empid,$date,$workstart,$workend,$nonworkstart,$nonworkend,$section,$workplace,$remarks,$createby,$curTime) {
        $db = DB::connection('mysql');
        $tbl_name = "inv_timesheet_entry";
        $insert=$db->table($tbl_name)
                 ->insertGetId([
                 'emp_id' =>$empid,
                 'workdate' => $date,
                 'starttime' => $workstart,
                 'endtime' => $workend,
                 'non_work_starttime' => $nonworkstart,
                 'non_work_endtime' => $nonworkend,
                 'section' => $section,
                 'workingplace' => $workplace,
                 'remark' => $remarks,
                 'created_by' => $createby,
                 'created_date' =>$curTime,
                 ]
              );
        return  $insert;
    }
    // Update process
    public static function updatedetails($empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry') 
					->SELECT('*') 
					->WHERE('emp_id','=',$empid)
					->GET();
		return $query;
	}
	public static function viewedit($empid) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_timesheet_entry AS timesheet') 
					->SELECT('starttime AS start1',
							'endtime AS end1',
							'non_work_starttime AS start2',
							'non_work_endtime AS end2',
							'section AS classification',
							'workingplace AS worktxt',
							'remark AS remarks')
					->WHERE('emp_id','=',$empid)
					->GET();
		return $query;
	}
	public static function updatetimesheet($empid,$date,$workstart,$workend,$nonworkstart,$nonworkend,$section,$workplace,$remarks,$updateby,$updateTime) {

        $db = DB::connection('mysql');
        $tbl_name = "inv_timesheet_entry";
        $update=$db->table($tbl_name)
        			->where('emp_id', $empid)
        			->where('workdate', $date)
                 ->update([
                 'workdate' => $date,
                 'starttime' => $workstart,
                 'endtime' => $workend,
                 'non_work_starttime' => $nonworkstart,
                 'non_work_endtime' => $nonworkend,
                 'section' => $section,
                 'workingplace' => $workplace,
                 'remark' => $remarks,
                 'updated_by' => $updateby,
                 'updateed_date' =>$updateTime,
                 ]
              );
        return  $update;
    }
// Download Timesheet
    public static function dataa($empid,$date)  {
    $results = DB::TABLE('inv_timesheet_entry')
                      ->SELECT('*')
                      ->WHERE('emp_id', '=', $empid)
                      ->WHERE('workdate', 'LIKE', '%' . $date . '%')
                      ->get();
     return $results;
  }
}
