<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class EmpHistory extends Model {
    public static function emphistorydetails($request) {
        $db = DB::connection('mysql');
        $query= $db->table('emp_mstemployees')
                ->SELECT('clientempteam.*','mst_customerdetail.customer_name','mst_customerdetail.id','mst_customerdetail.customer_id','mst_branchdetails.branch_name','emp_mstemployees.FirstName','emp_mstemployees.LastName','emp_mstemployees.Emp_ID')
                ->leftJoin('clientempteam', function($join)
                  {
                    $join->on('emp_mstemployees.Emp_ID', '=', 'clientempteam.emp_id');
                    $join->where('clientempteam.status','=','1');
                  })
                ->leftJoin('mst_customerdetail', 'mst_customerdetail.customer_id', '=', 'clientempteam.cust_id')
                ->leftJoin('mst_branchdetails', function($join)
                  {
                    $join->on('mst_branchdetails.customer_id', '=', 'clientempteam.cust_id');
                    $join->on('mst_branchdetails.branch_id', '=', 'clientempteam.branch_id');
                  })
                 ->where('emp_mstemployees.resign_id','!=','1')
                 ->orderBy('clientempteam.cust_id', 'is', 'null')
                 ->orderBy('clientempteam.cust_id', 'ASC')
                ->orderBy('emp_mstemployees.Emp_ID','ASC')
                ->paginate($request->plimit);
    return $query;
    }

    //Common Function
  public static function getYrMonCountBtwnDates($startDT, $endDT){
    $retVal['year']=0;
    $retVal['month']=0;
    if ($endDT == ""||$endDT=="0000-00-00") {
      $endDT = date("Y-m-d");
    }
    if (($startDT!=""&&$startDT!="0000-00-00")&&($endDT!=""&&$endDT!="0000-00-00")){
      $diff = abs(strtotime($endDT) - strtotime($startDT));
      $dys = (int)((strtotime($endDT)-strtotime($startDT))/86400);
      $retVal['year'] = floor($diff / (365*60*60*24));
      $retVal['month'] = floor(($diff - $retVal['year'] * 365*60*60*24) / (30*60*60*24));
    } 
    return $retVal;
  }
  public static function fnAddZeroSubstring($val) {
    return substr("0" . $val, -2);
  }
  }  