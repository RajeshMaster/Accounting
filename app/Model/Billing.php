<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Query\Builder;
use Auth;
class Billing extends Model { 
	public static function fnGetAccountPeriod() 
	 {
	 	$db = DB::connection('mysql');
	 	$query = $db->table('dev_kessandetails')
	 	 			->where('delflg','=',0)
	 	 			->get();
	 	return $query;	
	 }
	public static function fnGetmnthbillRecord($from_date, $to_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_newbilling AS mst')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'yearlink','monthlink')
					->WHERE('del_flg','=',0)
					->WHERE('date','>',$from_date)
					->WHERE('date','<',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $sql;
	}
	public static function fnGetmnthbillRecordNext($to_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_newbilling')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'yearlink','monthlink')
					->WHERE('del_flg','=',0)
					->WHERE('date','>=',$to_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $sql;
	}
	public static function fnGetmnthbillRecordPrevious($from_date) {
		$db = DB::connection('mysql');
		$sql = $db->table('inv_newbilling')
					->SELECT(DB::raw("SUBSTRING(date, 1, 7) AS date"),'yearlink','monthlink')
					->WHERE('del_flg','=',0)
					->WHERE('date','<=',$from_date)
					->ORDERBY('date', 'ASC')
	 	 			->GET();
	 	return $sql;
	}
	public static function getTempcurrecDetails($request,$date_month) {
		$yearmonth = explode("-", $date_month);
		$db = DB::connection('mysql');
		$sql = $db->table('inv_newbilling')
					->WHERE('monthlink','=',$yearmonth[1])
					->WHERE('yearlink','=',$yearmonth[0])
          ->WHERE('Empno','=',$request->hdnempid)
	 	 			->GET();
	 	$sql = count($sql);
	 	return $sql;
	}
	public static function mnthbilldetails($request,$date_month) {
		if(isset($request->selMonth)){
			$month = $request->selMonth;
			$year = $request->selYear;
		} else {
			$previousYrMn = date('Y-m-d', strtotime(date('Y-m')." -1 month"));
			$month=date("m", strtotime($previousYrMn));	
			$year=date("Y", strtotime($previousYrMn));
		}
		$db = DB::connection('mysql');
		$sql = $db->table('inv_temp_billingemp as temptbl')
                    ->SELECT('bd.branch_id','cet.id',
                    		'emp.Emp_ID','emp.nickname','emp.LastName',
                    		'mstcus.customer_id','mstcus.customer_name',
                    		'bd.date','cet.start_date',
                    		'br.branch_name','bd.tcheckcalc',
                    		'bd.Amount','bd.OTAmount',
                    		'bd.timerange','bd.minhrs',
                    		'bd.maxhrs','bd.TotalAmount',
                        'bd.monthlink','bd.yearlink');
        $sql = $sql->LEFTJOIN('emp_mstemployees AS emp', function($join) use($month,$year)
                             {
                            $join->ON('emp.Emp_ID','=', 'temptbl.Emp_Id');
                            $join->WHERE('temptbl.month','=', $month);
                            $join->WHERE('temptbl.year','=', $year);
                             });
        $sql = $sql->LEFTJOIN('clientempteam AS cet', function($join)
                             {
                            $join->ON('cet.emp_id', '=', 'emp.Emp_ID');
                            $join->WHERE('cet.end_date','=', '0000-00-00');
                            $join->ORWHERE('cet.end_date','=', '');
                             });
        $sql = $sql->LEFTJOIN('inv_newbilling As bd', function($join) use($month,$year)
                             {
                            $join->ON('bd.Empno', '=', 'emp.Emp_ID');
                            $join->WHERE('bd.monthlink','=', $month);
                            $join->WHERE('bd.yearlink','=', $year);
                             });
       	$sql = $sql->LEFTJOIN('mst_customerdetail AS mstcus', function($join)
                             {
                            $join->ON('mstcus.customer_id', '=', 'bd.Clientid');
                             });
       	$sql = $sql->LEFTJOIN('mst_branchdetails As br', function($join)
                             {
                            $join->ON('br.customer_id', '=', 'bd.Clientid');
                            $join->ON('br.branch_id', '=', 'bd.branch_id');
                             });
       	$sql = $sql ->WHERE('emp.delFlg','=',0);
       	$sql = $sql ->WHERERAW("IF( (SELECT COUNT(*) FROM inv_newbilling AS afterRes 
					WHERE afterRes.Empno = emp.Emp_ID AND afterRes.monthlink='$month' AND
					afterRes.yearlink='$year' AND 
					afterRes.TotalAmount!='' AND emp.resign_id=1)>0 ,emp.resign_id=1,emp.resign_id=0)");
       	if ($request->billsort !='') {
       		$sql = $sql ->orderBy($request->billsort, $request->sortOrder);
       	} else {
       		$sql = $sql ->orderBy('mstcus.customer_name', 'DESC')
       					      ->orderBy('br.branch_name', 'DESC')
       					      ->orderBy('emp.Emp_ID', 'ASC');
       	}
       		$sql = $sql ->paginate($request->plimit);
	 	return $sql;
	}
	public static function fntotalamtval($date_month) {
			$spliYrMn = explode("-", $date_month);
			$db = DB::connection('mysql');
			$sql = $db->table('inv_temp_billingemp AS tempbill')
                    ->SELECT(DB::raw("SUM(REPLACE(TotalAmount, ',', ''))TotalAmount,
				                    	SUM(REPLACE(Amount, ',', ''))Amount, 
				                    	SUM(REPLACE(OTAmount, ',', '')) OTAmount"));
            $sql = $sql->LEFTJOIN('inv_newbilling As newbill', function($join) use($spliYrMn)
                             {
                            $join->ON('tempbill.Emp_Id', '=', 'newbill.Empno');
                            $join->WHERE('tempbill.year','=', $spliYrMn[0]);
                            $join->WHERE('tempbill.month','=', $spliYrMn[1]);
                             });
           	$sql = $sql ->WHERE('date', 'LIKE', '%' . $date_month . '%')
           				->GET();
			return $sql;
	}
	public static function getTaxDetails($date) {
		$db = DB::connection('mysql');
		$sql = $db->table('dev_taxdetails')
					->WHERE('delflg','=',0)
					->WHERE('Startdate','<=',$date)
					->orderBy('Startdate', 'DESC')
					->orderBy('Ins_TM', 'DESC')
					->limit(1)
	 	 			->GET();
		return $sql;
	}
	public static function getAllEmpDetails($request) {
    if(($request->year != "")&&($request->month != "")) {
      $year = $request->year;
      $month = $request->month ;
    } else {
      $previous = date('Y-m', strtotime('first day of last month'));
      $splitPrevious = explode("-", $previous);
      $year=$splitPrevious[0];
      $month=$splitPrevious[1];
    }
	$db = DB::connection('mysql');
    $unselectedEmployees = $db->TABLE('emp_mstemployees')
                                ->SELECT('Emp_ID',
                                         'FirstName',
                                         'LastName')
                                ->WHERE('delFLg', '=', 0)
                                ->WHERERAW("IF( (SELECT COUNT(*) FROM inv_newbilling AS afterRes 
					WHERE afterRes.Empno = emp_mstemployees.Emp_ID AND afterRes.monthlink='$month' 
					AND afterRes.yearlink='$year' AND afterRes.TotalAmount!='' 
					AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)");
    $unselectedEmployees = $unselectedEmployees->whereNotIn('Emp_ID', function($query) use($year, $month)
                              {
                              $query->SELECT('Emp_ID')
                                    ->FROM('inv_temp_billingemp')
                                    ->WHERE('year', '=', $year)
                                    ->WHERE('month', '=', $month);
                              })->orderBy('Emp_ID', 'ASC')
                                ->get();
    return $unselectedEmployees;
  }
  public static function getAllFilteredEmpDetails($request) {
    if(($request->year!="") && ($request->month!="")) {
      $year = $request->year;
      $month = $request->month;
    } else {
      $previous = date('Y-m', strtotime('first day of last month'));
      $splitPrevious = explode("-", $previous);
      $year=$splitPrevious[0];
      $month=$splitPrevious[1];
    }
    $db = DB::connection('mysql');
    $selectedEmployees = $db->TABLE('inv_temp_billingemp')
                                ->SELECT('emp_mstemployees.Emp_ID',
                                         'emp_mstemployees.FirstName',
                                         'emp_mstemployees.LastName')
                                ->LEFTJOIN('emp_mstemployees', 'emp_mstemployees.Emp_ID', '=', 'inv_temp_billingemp.Emp_ID')
                                ->WHERE('emp_mstemployees.delFLg', '=', 0)
                                ->WHERE('inv_temp_billingemp.month', '=', $month)
                                ->WHERE('inv_temp_billingemp.year', '=', $year);
    $selectedEmployees = $selectedEmployees->WHERERAW("IF( (SELECT COUNT(*) FROM inv_newbilling AS afterRes 
				WHERE afterRes.Empno = emp_mstemployees.Emp_ID AND afterRes.monthlink='$month' 
				AND afterRes.yearlink='$year' AND afterRes.TotalAmount!='' 
				AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)")
                                ->orderBy('emp_mstemployees.Emp_ID', 'ASC')
                                ->get();
    return $selectedEmployees;
  }
  public static function InsertEmpFlrDetails($request) {
  	$db = DB::connection('mysql');
    $deldetails = $db->TABLE('inv_temp_billingemp')->WHERE('year', '=', $request->year)
                     ->WHERE('month', '=', $request->month)->DELETE();
    $rows = array();
    for ($i=0;$i<count($request->selected);$i++) {
        $rows[] = array('id' => '',
                        'Emp_Id' => $request->selected[$i],
                        'delflg' => 0,
                        'year' => $request->year,
                        'month' => $request->month,
                        'create_date' => date('Y-m-d H:i:s'),
                        'create_by' => Auth::user()->username,
                        'update_date' => date('Y-m-d H:i:s'),
                        'update_by' => Auth::user()->username);
    }
    DB::TABLE('inv_temp_billingemp')->INSERT($rows);
    return true;
  }
  public static function billing_history_data($request){
    $db = DB::connection('mysql');
    $selectedEmployees = $db->TABLE('inv_newbilling AS nb')
                            ->SELECT('nb.date','nb.Empno',
                                    'cus.customer_name','br.branch_name',
                                    'emp.nickname','nb.timerange AS totalhrs',
                                    'nb.OTAmount','nb.TotalAmount',
                                    'nb.branch_id','cus.customer_id')
                            ->LEFTJOIN('mst_customerdetail AS cus', 'cus.customer_id', '=', 'nb.Clientid')
                            ->LEFTJOIN('emp_mstemployees AS emp', 'emp.Emp_ID', '=', 'nb.Empno');
    $selectedEmployees = $selectedEmployees->LEFTJOIN('mst_branchdetails AS br', function($join)
                             {
                            $join->ON('br.customer_id', '=', 'nb.Clientid');
                            $join->ON('br.branch_id', '=', 'nb.branch_id'); 
                            });  
    $selectedEmployees = $selectedEmployees ->WHERE('nb.Empno','=',$request->empid)
                                            ->WHERE('nb.timerange','!=',0) 
                                            ->WHERE('nb.timerange','!=',"") 
                                            ->WHERE('nb.TotalAmount','!=',0) 
                                            ->WHERE('nb.TotalAmount','!=',"");  
    $selectedEmployees = $selectedEmployees ->orderBy('nb.date', 'DESC')
                                            ->paginate($request->plimit);   
                                            // print_r($selectedEmployees);exit();          
    return $selectedEmployees;
  }
  public static function fngetempdetails($request,$date){
      $splitYrMn = explode("-", $date);
      $db = DB::connection('mysql');
      $query = $db->TABLE('emp_mstemployees AS emp')
                            ->SELECT('bd.*','emp.nickname','emp.LastName',
                                    'mstcus.customer_id','mstcus.customer_name',
                                    'cet.start_date','br.branch_name')
                            ->LEFTJOIN('clientempteam AS cet', 'cet.emp_id', '=', 'emp.Emp_ID')
                            ->LEFTJOIN('inv_newbilling As bd', 'bd.Empno', '=', 'emp.Emp_ID')
                            ->LEFTJOIN('mst_customerdetail AS mstcus', 'mstcus.customer_id', '=', 'bd.Clientid');
      $query = $query->LEFTJOIN('mst_branchdetails AS br', function($join)
                             {
                            $join->ON('br.customer_id', '=', 'bd.Clientid');
                            $join->ON('br.branch_id', '=', 'bd.branch_id');
                            });
      $query = $query ->WHERE('bd.Empno','=',$request->hdnempid)
                      ->WHERE('bd.yearlink','=',$splitYrMn[0])
                      ->WHERE('bd.monthlink','=',$splitYrMn[1])
                      ->WHERE('emp.delFlg','=',0);
      $query = $query ->ORDERBY('emp.Emp_ID', 'ASC')
                      ->limit(1)
                      ->GET();       
    return $query;
  }
  public static function billUpdChk($request) {
    $db = DB::connection('mysql');
    $query= $db->table('inv_newbilling')
          ->where('Empno', $request->hdnempidchk)
          ->where('id', $request->editbillregidchk)
          ->update(['tcheckcalc' => $request->upcheckval]);
    return $query;
    }
    public static function getempdetails($request) {
      $db = DB::connection('mysql');
      $query = $db->TABLE('clientempteam AS a')
                  ->SELECT('a.*','b.*')
                  ->LEFTJOIN('mst_customerdetail AS b', 'a.cust_id', '=', 'b.customer_id')
                  ->WHERE('a.emp_id', $request->hdnempid)
                  ->GROUPBY('b.customer_id')
                  ->GET();
      return $query;
    }
    public static function fnGetcusDetails($id) {
      $db = DB::connection('mysql');
       $subject = $db->table('mst_customerdetail')
                      ->SELECT('customer_id','customer_name')
                      ->WHERE('customer_id', $id)
                      ->orderBy('id', 'ASC')
                      ->lists('customer_name','customer_id');
    return $subject;
  }
  public static function fnGetBranchDetails($request) {
    $db = DB::connection('mysql');
      $result = $db->table('mst_branchdetails')
                      ->SELECT('branch_id','branch_name')
                      ->WHERE('customer_id', '=',$request->clientname)
                      ->WHERE('delflg', '=',0)
                      ->orderBy('create_date', 'ASC')
                      ->get();
    return $result;
  }
  public static function insertprocess($request) {
        $date_month = $request->selYear."-".$request->selMonth;
        $month = $request->selYear;
        $year = $request->selMonth;
      if($request->chkval == "on") {
        $request->chkval = 1;
      } else {
        $request->chkval = 2;
      }
      if($request->chkvalMB == "on") {
        $request->chkvalMB = 1;
      } else {
        $request->chkvalMB = 2;
      }
      if($request->chkvalTS == "on") {
        $request->chkvalTS = 1;
      } else {
        $request->chkvalTS = 0;
      }
       if($request->caldone == "on") {
        $request->caldone = 1;
      } else {
        $request->caldone = 2;
      }
    $startdate = $date_month."-01";
    $enddate = $date_month;
    $db = DB::connection('mysql');
    $result= $db->table('inv_newbilling')
                ->insert(['Empno' => $request->hdnempid,
                        'branch_id' => $request->branchname,
                        'Clientid' => $request->clientname,
                        'start_date' => $request->startdate,
                        'end_date' => $enddate,
                        'monthlink' => $request->selMonth,
                        'yearlink' => $request->selYear,
                        'date' => $startdate,
                        'tcheckcalc' => $request->caldone,
                        'bdcheckcalc' => $request->chkval,
                        'mbcheckcalc' => $request->chkvalMB,
                        'wknghrschk' => $request->chkvalTS,
                        'TotalAmount' => $request->hdn_lblBillingAmt,
                        'OTAmount' => $request->otamount,
                        'Amount' => $request->amount,
                        'minhrs' => $request->time_start,
                        'maxhrs' => $request->time_end,
                        'minamt' => $request->ot_end,
                        'maxamt' => $request->ot_start,
                        'del_flg' => '0',
                        'Ins_DT' => date('Y-m-d'),
                        'Ins_TM' => date('h:i:s'),
                        'CreatedBy' => Auth::user()->username,
                        'timerange' => $request->timerange]);
    return $result;
  }
  public static function updateprocess($request) {
    $db = DB::connection('mysql');
    if($request->chkval == "on") {
        $request->chkval = 1;
      } else {
        $request->chkval = 2;
      }
      if($request->chkvalMB == "on") {
        $request->chkvalMB = 1;
      } else {
        $request->chkvalMB = 2;
      }
      if($request->chkvalTS == "on") {
        $request->chkvalTS = 1;
      } else {
        $request->chkvalTS = 0;
      }
      if($request->caldone == "on") {
        $request->caldone = 1;
      } else {
        $request->caldone = 2;
      }
    $query= $db->table('inv_newbilling')
          ->where('Empno', $request->hdnempid)
          ->where('id', $request->id)
          ->update(['OTAmount' => $request->otamount,
                    'Amount' => $request->amount,
                    'start_date' => $request->startdate,
                    'branch_id' => $request->branchname,
                    'Clientid' => $request->clientname,
                    'TotalAmount' => $request->hdn_lblBillingAmt,
                    'monthlink' => $request->selMonth,
                    'yearlink' => $request->selYear,
                    'minhrs' => $request->time_start,
                    'tcheckcalc' => $request->caldone,
                    'bdcheckcalc' => $request->chkval,
                    'mbcheckcalc' => $request->chkvalMB,
                    'wknghrschk' => $request->chkvalTS,
                    'maxhrs' => $request->time_end,
                    'minamt' => $request->ot_end,
                    'maxamt' => $request->ot_start,
                    'timerange' => $request->timerange,
                    'Up_DT' => date('Y-m-d'),
                    'UP_TM' => date('h:i:s'),
                    'UpdatedBy' => Auth::user()->username]);
    return $query;
  }
  public static function fnGetBranchDtls($id) {
    $db = DB::connection('mysql');
      $result = $db->table('mst_branchdetails')
                      ->SELECT('branch_id','branch_name')
                      ->WHERE('customer_id', '=',$id)
                      ->WHERE('delflg', '=',0)
                      ->orderBy('create_date', 'ASC')
                      ->lists('branch_name','branch_id');
    return $result;
  }
  //Months name
    public static function getMonthname() {
      return array('1'=>$msg = "01",'2'=>$msg = "02",'3'=>$msg = "03",'4'=>$msg = "04",'5'=>$msg = "05",'6'=>$msg = "06",'7'=>$msg = "07",'8'=>$msg = "08",'9'=>$msg = "09",'10'=>$msg = "10",'11'=>$msg = "11",'12'=>$msg = "12");
  }
  //Year name
    public static function getYearname() {
      return array('2017'=>$yr = "2017",'2016'=>$yr = "2016",'2015'=>$yr = "2015",'2014'=>$yr = "2014",'2013'=>$yr = "2013",'2012'=>$yr = "2012");
  }
  public static function get_preval($request) {
    $db = DB::connection('mysql');
    $date_month = $request->selYear."-".$request->selMonth;
    $date = $date_month."-01";
    $splitdate = explode('-', $date_month);
    $prioryrmonth = date ('Y-m', strtotime ( '-1 month' , strtotime ( $date )));
    $futuremonth = date ('Y-m', strtotime ( '+1 month' , strtotime ( $date )));
    $prioryrmonth = explode("-", $prioryrmonth);
    $query = $db->table('inv_newbilling')
                ->SELECT('*')
                ->LEFTJOIN('inv_temp_billingemp', 'inv_temp_billingemp.Emp_Id', '=', 'inv_newbilling.Empno')
                ->LEFTJOIN('emp_mstemployees', 'inv_temp_billingemp.Emp_Id', '=', 'emp_mstemployees.Emp_ID')
                ->WHERE('inv_temp_billingemp.month', '=', $prioryrmonth['1'])
                ->WHERE('inv_temp_billingemp.year', '=', $prioryrmonth['0'])
                ->WHERE('inv_newbilling.yearlink', '=', $prioryrmonth['0'])
                ->WHERE('inv_newbilling.monthlink', '=', $prioryrmonth['1'])
                ->WHERERAW("IF((SELECT COUNT(*) FROM inv_newbilling AS afterRes 
                  WHERE afterRes.Empno = emp_mstemployees.Emp_ID AND afterRes.monthlink='$splitdate[1]' 
                  AND afterRes.yearlink='$splitdate[0]' AND afterRes.TotalAmount!=''  
                  AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)")
                ->get();
    return $query;         
  }
  public static function getEmp_temp_Details($request) {
    $db = DB::connection('mysql');
    $cur_date = date('Y-m-d h:i:s');
    $year = $request->selYear;
    $month = $request->selMonth; 
    //DELET CURRENT MONTH EMPLOYEE RECORD DETAIL ONLY
    $query = $db->table('inv_temp_billingemp')
                ->WHERE('year','=',$year)
                ->WHERE('month','=',$month)
                ->DELETE();
    return $query; 
  }
   public static function getempdtls($request) {
    $cur_date = date('Y-m-d h:i:s');
    $year = $request->selYear;
    $month = $request->selMonth; 
    if ($month == "01") { 
      $pre_month = "12";
      $pre_year = $year-1;
    } else {
      $pre_month = $month-1;
      $pre_year = $year;
    }
    $db = DB::connection('mysql');
    $query = $db->table('inv_temp_billingemp')
                ->SELECT('*')
                ->LEFTJOIN('emp_mstemployees', 'inv_temp_billingemp.Emp_Id', '=', 'emp_mstemployees.Emp_ID')
                ->WHERE('inv_temp_billingemp.month', '=', $pre_month)
                ->WHERE('inv_temp_billingemp.year', '=', $pre_year)
                ->WHERERAW("IF((SELECT COUNT(*) FROM inv_newbilling AS afterRes 
                            WHERE afterRes.Empno = emp_mstemployees.Emp_ID AND afterRes.monthlink='$month' 
                            AND afterRes.yearlink='$year' AND afterRes.TotalAmount!='' 
                            AND emp_mstemployees.resign_id=1)>0 ,emp_mstemployees.resign_id=1,emp_mstemployees.resign_id=0)")
                ->get();
    return $query;
  }
  public static function insertgetempdtls($request,$id) {
    $db = DB::connection('mysql');
    $cur_date = date('Y-m-d h:i:s');
    $year = $request->selYear;
    $month = $request->selMonth; 
    $query= $db->table('inv_temp_billingemp')
                ->insert(['id' => '',
                        'Emp_Id' => $id,
                        'delflg' => '0',
                        'year' => $year,
                        'month' => $month,
                        'create_date' => date('Y-m-d'),
                        'create_by' => Auth::user()->username,
                        ]);
  }
  public static function Regpreviousdetails($request,$empno,$bid,$cid,$amt,$mhrs,$maxhrs,$mamt,$maxamt) {
      $db = DB::connection('mysql');
      $cur_date = date('Y-m-d');
      $cur_time= date('h:i:s');
      $createdby=Auth::user()->username;
        $date_month = $request->selYear."-".$request->selMonth;
        $month = $request->selMonth;
        $year = $request->selYear;
      //WITHOUT DATA IN CURRENT MONTH
      $startdate =$request->selYear."-".$request->selMonth."-01";
       $query= $db->table('inv_newbilling')
                ->insert(['Empno' => $empno,
                        'branch_id' => $bid,
                        'Clientid' => $cid,
                        'start_date' => $cur_date,
                        'end_date' => $cur_date,
                        'monthlink' => $request->selMonth,
                        'yearlink' => $request->selYear,
                        'date' => $startdate,
                        'tcheckcalc' => '',
                        'bdcheckcalc' => 2,
                        'mbcheckcalc' => 2,
                        'TotalAmount' => '',
                        'OTAmount' => '',
                        'Amount' => $amt,
                        'minhrs' => $mhrs,
                        'maxhrs' => $maxhrs,
                        'maxamt' => $maxamt,
                        'minamt' => $mamt,
                        'del_flg' => 0,
                        'Ins_DT' => $cur_date,
                        'Ins_TM' => $cur_time,
                        'CreatedBy' => Auth::user()->username,
                        'timerange' => '',
                        'wknghrschk' => 0]);
    return $query;
  }
   public static function inserttempvalues($request) {
    $db = DB::connection('mysql');
    $cur_date = date('Y-m-d h:i:s');
    $query= $db->table('inv_temp_billingemp')
                ->insert(['id' => '',
                        'Emp_Id' => $request->hdnempid,
                        'delflg' => '0',
                        'year' => $request->selYear,
                        'month' => $request->selMonth,
                        'create_date' => date('Y-m-d'),
                        'create_by' => Auth::user()->username,
                        ]
                        );
  }
}