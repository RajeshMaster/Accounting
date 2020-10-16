<?php 
	namespace App\Model;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	class StaffSalary extends Model{
		public static function fnGetAccountPeriod() 
	 	{
	 	$db = DB::connection('mysql');
	 	$query = $db->table('dev_kessandetails')
	 	 			->where('delflg','=',0)
	 	 			->get();
	 	return $query;	
	 	}
	public static function fnGetmnthRecord($from_date, $to_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salarydetails')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'year_ln','month_ln')
					->WHERE('date','>',$from_date,' AND','date','<',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}
	public static function fnGetmnthRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salarydetails')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'year_ln','month_ln')
					->WHERE('date','<=',$from_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}
		public static function fnGetmnthRecordNext($to_date) {
		$db = DB::connection('mysql');
		$query = $db->table('inv_salarydetails')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'year_ln','month_ln')
					->WHERE('date','>=',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $query;
	}
		public static function salaryDetail($request,$yr,$mnth) {
			$query = DB::table('emp_mstemployees AS cet')
						->select('emp.*','cet.*')
						->leftJoin('inv_salarydetails AS emp', function($join) use($yr,$mnth)
	                         {
	                             $join->on('cet.Emp_ID', '=', 'emp.empid');
	                             $join->WHERE('emp.year_ln','=',$yr);
	                             $join->WHERE('emp.month_ln','=',$mnth);
	                         })
						->where('cet.delFlg','=',0)
						->where('cet.resign_id','=',0)
						->where('cet.Title','=',2)
						->ORDERBY('emp.salary1', 'DESC')
						->orderBy('cet.Emp_ID','ASC')
						  // ->get();
						  // ->tosql();
						  // dd($query);
						->paginate($request->plimit);
			return $query;
		}
		public static function fnGetSettingsDetails() {
			$query = DB::table('inv_set_salarymain AS main')
							->select('main.id','main.main_eng','main.main_jap','sub.sub_eng','sub.sub_jap','main.delflg AS maindelflg','sub.delflg AS subdelflg')
							->leftJoin('inv_set_salarysub AS sub','sub.mainid','=','main.id')
							->ORDERBY('main.id','ASC')
							->ORDERBY('sub.id','ASC')
							->get();
			return $query;
		}
		public static function fnGetMainSettingsDetails() {
			$query = DB::table('inv_set_salarymain')
						->select('*')
						->get();
			return $query;
		}
		public static function salaryDetailsByEmpid($empid,$date) {
			$sql = "SELECT emp.*,cet.*  FROM  emp_mstemployees AS cet 
				LEFT JOIN inv_salarydetails AS emp ON cet.Emp_ID=emp.empid
				WHERE empid = '$empid' AND date LIKE '%$date%'";
			$result = DB::select($sql);
			return $result;
		}
		public static function salaryview($request) {
			$query = DB::table('emp_mstemployees')
						->select('*')
						->WHERE('id','=',$request->viewid)
						->get();
			return $query;
		}

		public static function view($year_ln,$month_ln) {
			$sql = "SELECT emp.*,cet.*  FROM  emp_mstemployees AS cet 
				LEFT JOIN inv_salarydetails AS emp ON cet.Emp_ID=emp.empid AND emp.year_ln LIKE '$year_ln' AND emp.month_ln LIKE '$month_ln' 
				WHERE cet.delFlg = 0 AND cet.resign_id=0 AND cet.Title=2 ORDER BY emp.salary1 DESC,cet.Emp_ID ASC";
			return $sql;
}

	public static function fuGetEmpDetails($id) {
				$query = DB::table('emp_mstemployees')
						->select('*')
						->WHERE('id','=',$id)
						->get();
			return $query;
	    }
		
		public static function Staffsalaryview($request,$empid,$yr,$mn) {
		$db = DB::connection('mysql');
		$dbArray = array("salary","ot","travel","others","main5_","main6_","main7_","main8_","main9_","main10_");
		$mainArray = array("MainTotal1","MainTotal2","MainTotal3","MainTotal4","MainTotal5","MainTotal6","MainTotal7","MainTotal8","MainTotal9","MainTotal10");
	    $yrmnth=$yr."-".$mn;
	    $sql="SELECT cus.customer_name AS cname,cl.*,(";
				for ($i=0; $i < 10; $i++) { 
					//$mainArrayval = $mainArray[$i];//echo $val;
					for ($j=1; $j < 11; $j++) {  
						$val = $dbArray[$i].$j;//echo $val;
						if ($j<10) {
							$sql .= "replace(sal.".$val.", ',', '')+";
						} else {
							$sql .= "replace(sal.".$val.", ',', ''))";
						}
					}
					if ($i<9) {
							$sql .= " AS ".$mainArray[$i].",(";
						} else {
							$sql .= " AS ".$mainArray[$i].",";
						}
				} 
					$sql.="sal.grand_total,bill.Amount,bill.OTAmount FROM clientempteam cl 
						LEFT JOIN mst_customerdetail cus ON cl.cust_id=cus.customer_id 
						LEFT JOIN inv_salarydetails sal ON sal.empid='$empid' AND sal.year_ln='$yr' AND sal.month_ln='$mn' 
						LEFT JOIN inv_newbilling bill ON bill.Empno=cl.emp_id AND bill.yearlink='$yr' AND bill.monthlink='$mn'
						 WHERE cl.emp_id='$empid' AND SUBSTRING(cl.start_date, 1, 7)<='$yrmnth'  
						AND (SUBSTRING(cl.end_date, 1, 7)>='$yrmnth' OR cl.end_date='0000-00-00' OR cl.end_date IS NULL)";
				// echo $sql; exit;
				$result = DB::select($sql);
			return $result;
		}
		public static function view_salarybill_detail($empid,$yr,$mn) {
			$dbArray = array("salary","ot","travel","others","main5_","main6_","main7_","main8_","main9_","main10_");
			$mainArray = array("MainTotal1","MainTotal2","MainTotal3","MainTotal4","MainTotal5","MainTotal6","MainTotal7","MainTotal8","MainTotal9","MainTotal10");
	        $yrmnth=$yr."-".$mn;
      		$sql="SELECT cus.customer_name AS cname,cl.*,(";
				for ($i=0; $i < 10; $i++) { 
					//$mainArrayval = $mainArray[$i];//echo $val;
					for ($j=1; $j < 11; $j++) {  
						$val = $dbArray[$i].$j;//echo $val;
						if ($j<10) {
							$sql .= "replace(sal.".$val.", ',', '')+";
						} else {
							$sql .= "replace(sal.".$val.", ',', ''))";
						}
					}
					if ($i<9) {
							$sql .= " AS ".$mainArray[$i].",(";
						} else {
							$sql .= " AS ".$mainArray[$i].",";
						}
				}
					$sql.="sal.grand_total,bill.Amount,bill.OTAmount FROM clientempteam cl 
						LEFT JOIN mst_customerdetail cus ON cl.cust_id=cus.customer_id 
						LEFT JOIN inv_salarydetails sal ON sal.empid='$empid' AND sal.year_ln='$yr' AND sal.month_ln='$mn' 
						LEFT JOIN inv_newbilling bill ON bill.Empno=cl.emp_id AND bill.yearlink='$yr' AND bill.monthlink='$mn'
						WHERE cl.delflg= '0' AND cl.emp_id='$empid' AND SUBSTRING(cl.start_date, 1, 7)<='$yrmnth'  
						AND (SUBSTRING(cl.end_date, 1, 7)>='$yrmnth' OR cl.end_date='0000-00-00' OR cl.end_date IS NULL)";
				$result = DB::select($sql);
			return $result;
		}
		public static function salaryprocessdetail($request) {
			$result = DB::table('inv_set_salarymain')
						->SELECT('main_eng','main_jap','delflg','id')
						->get();
			return $result;
		}
}
