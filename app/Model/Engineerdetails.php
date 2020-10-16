<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Input;
use Auth;
use Carbon\Carbon ;
class Engineerdetails extends Model {
	
	public static function fnGetEngineerdetails($request,$yearmonth,$flg) {
    if ($request->searchmethod == 3) {
      $yearmonth = "";
    }
		$db = DB::connection('mysql');
		$query = $db->TABLE($db->raw("(SELECT main.quot_date,main.id,main.user_id as InvoiceNo,emp.LastName,emp.Firstname,main.trading_destination_selection,emp.DOJ,
main.tax,works.emp_id as EMPID ,works.amount,works.work_specific as work_spec ,main.company_name 
      FROM   tbl_work_amount_details works 
      left join dev_invoices_registration main on works .invoice_id = main .user_id 
      LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=works.emp_id
      WHERE main.quot_date LIKE '%$yearmonth%' AND main.del_flg = 0 AND works.emp_id IS NOT NULL AND works.emp_id != ''
) AS DDD"));

      if ($request->searchmethod == 1) {
      $query = $query->where(function($joincont) use ($request) {
                                    $joincont->where('EMPID', 'LIKE', '%' . $request->singlesearch . '%')
                                         ->orwhere('LastName', 'LIKE', '%' . $request->singlesearch . '%');
                            });
    } elseif ($request->searchmethod == 2) {
      $query = $query->where(function($joincont) use ($request) {
                                $joincont->where('EMPID','LIKE','%'.trim($request->employeeno) . '%');
                                $joincont->where('LastName','LIKE','%'.trim($request->employeename) . '%');
                                // $joincont->where('DOJ','>=',$request->startdate,'AND',$request->enddate);
                            });
      }
      if ($request->searchmethod == 3) {
           if (!empty($request->engineeridClick)) {
             $query = $query->where(function($joincont) use ($request) {
              $joincont->where('EMPID','LIKE','%'.trim($request->engineeridClick) . '%');
            });
           }

      }
      if ($request->searchmethod == 3) {
        $query = $query->orderBy('quot_date', 'DESC')
                        ->orderBy('InvoiceNo', 'DESC');
      }
      $query = $query->orderBy($request->engineerdetailssort, $request->sortOrder);
      if ($flg == 1) {
          $query = $query->paginate($request->plimit);
      } else {
          $query = $query->get();
      }
                 
		  return $query;
	 }
   public static function fnGetEstimateRecord($from_date, $to_date) {
      // ACCESS RIGHTS
      // CONTRACT EMPLOYEE
      $accessQuery = "";
      if (Auth::user()->userclassification == 1) {
        $from_date = Auth::user()->accessDate;
        $accessQuery = " OR accessFlg = 1 ";
      }
      // END ACCESS RIGHTS
      $result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date > '$from_date' AND quot_date < '$to_date')".$accessQuery." AND (emp_ID1 != '' OR emp_ID2 != '' OR emp_ID3 != ''
 OR emp_ID4 != '' OR emp_ID5 != '' OR emp_ID6 != '' OR emp_ID7 != '' OR emp_ID8 != '' OR emp_ID9 != '' 
OR emp_ID10 != '' OR emp_ID11 != '' OR emp_ID12 != '' OR emp_ID13 != '' OR emp_ID14 != '' OR emp_ID15 != '') ORDER BY quot_date ASC) as tbl1"))
        ->get();
        // ->toSql();dd($result);
      return $result;
    }
    public static function fnGetEstimateRecordPrevious($from_date) {
    // ACCESS RIGHTS
    // CONTRACT EMPLOYEE
    $conditionAppend = "";
    if (Auth::user()->userclassification == 1) {
      $to_date = Auth::user()->accessDate;
      $conditionAppend = "AND ( quot_date >= '$to_date' OR accessFlg = 1 )";
    }
    // END ACCESS RIGHTS
    $result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date <= '$from_date' $conditionAppend)  AND (emp_ID1 != '' OR emp_ID2 != '' OR emp_ID3 != ''
 OR emp_ID4 != '' OR emp_ID5 != '' OR emp_ID6 != '' OR emp_ID7 != '' OR emp_ID8 != '' OR emp_ID9 != '' 
OR emp_ID10 != '' OR emp_ID11 != '' OR emp_ID12 != '' OR emp_ID13 != '' OR emp_ID14 != '' OR emp_ID15 != '') ORDER BY quot_date ASC) as tbl1"))
      ->get();
    return $result;
  }
  public static function fnGetEstimateRecordNext($to_date) {
    $result = DB::TABLE(DB::raw("(SELECT SUBSTRING(quot_date, 1, 7) AS quot_date FROM dev_invoices_registration WHERE del_flg = 0 AND (quot_date >= '$to_date')  AND (emp_ID1 != '' OR emp_ID2 != '' OR emp_ID3 != ''
 OR emp_ID4 != '' OR emp_ID5 != '' OR emp_ID6 != '' OR emp_ID7 != '' OR emp_ID8 != '' OR emp_ID9 != '' 
OR emp_ID10 != '' OR emp_ID11 != '' OR emp_ID12 != '' OR emp_ID13 != '' OR emp_ID14 != '' OR emp_ID15 != '') ORDER BY quot_date ASC) as tbl1"))
      ->get();
    return $result;
  }
  public static function fngetexpensedetail($year,$month,$request,$flg) {
    if ($request->historypage == 1) {
      $yearmonth = "";
      $empid1 = " AND main.emp_ID = '$request->empid'";
      $empid2 = " AND pettycash.emp_ID = '$request->empid'";
    } else {
      $empid1 = "";
      $empid2 = "";
      $yearmonth = $year.'-'.$month;
    }
    $result = DB::TABLE(DB::raw("(SELECT main.date,main.emp_ID,emp.Firstname,emp.LastName,main.subject,main.details,sub.Subject as subname,sub.Subject_jp as subjp,
    subdet.sub_eng,subdet.sub_jap,main.amount,NULL as charge,main.remark_dtl,main.file_dtl
    FROM dev_expenses as main
    LEFT JOIN dev_expensesetting sub on sub.id=main.subject
    LEFT JOIN inv_set_expensesub subdet on subdet.id=main.details AND subdet.mainid=main.subject
    LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
     WHERE date LIKE '%$yearmonth%' AND main.emp_ID != '' $empid1 
    UNION ALL
    SELECT pettycash.date,pettycash.emp_ID,emp.Firstname,emp.LastName,main_subject as subject,pettycash.sub_subject as details,sub.main_eng as subname,
    sub.main_jap as subjp,subdet.sub_eng,subdet.sub_jap,pettycash.amount,NULL as charge,pettycash.remark_dtl,pettycash.file_dtl  
    FROM inv_pettycash as pettycash
    LEFT JOIN inv_set_transfermain sub on sub.id=pettycash.main_subject
    LEFT JOIN inv_set_transfersub subdet on subdet.id=pettycash.sub_subject AND subdet.mainid=pettycash.main_subject
    LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=pettycash.emp_ID
     WHERE date LIKE '%$yearmonth%' AND pettycash.emp_ID != '' $empid2) as DDD"));
    if ($flg == 1) {
      $result = $result->get();
    } else {
      $result = $result->orderBy($request->engineerdetailssort, $request->sortOrder);
      $result = $result->paginate($request->plimit);
    }
    // ->toSql();dd($result);
    return $result;
  }
  /*UNION ALL
    SELECT main.bankdate as date,main.emp_ID,emp.Firstname,emp.LastName,main.subject,main.details,sub.Subject as subname,sub.Subject_jp as subjp,
    subdet.sub_eng,subdet.sub_jap,main.amount,main.fee as charge,main.remark_dtl,main.file_dtl 
    FROM dev_banktransfer as main
    LEFT JOIN dev_expensesetting sub on sub.id=main.subject
    LEFT JOIN inv_set_expensesub subdet on subdet.id=main.details AND subdet.mainid=main.subject
    LEFT JOIN emp_mstemployees emp ON emp.Emp_ID=main.emp_ID
     WHERE bankdate LIKE '%$yearmonth%' AND main.emp_ID != ''*/
  public static function fnGetExpenseRecord($request,$from_date, $to_date) {
    // ACCESS RIGHTS
    // CONTRACT EMPLOYEE
    $conditionAppend = "";
    if (Auth::user()->userclassification == 1) {
      $from_date = Auth::user()->accessDate;
      $conditionAppend = "OR accessFlg = 1";
    }
    // END ACCESS RIGHTS
    $sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM dev_expenses
            WHERE (date > '$from_date' AND date < '$to_date') AND emp_ID != '' $conditionAppend UNION ALL 
            /*SELECT SUBSTRING(bankdate, 1, 7) AS date FROM dev_banktransfer 
            WHERE (bankdate > '$from_date' AND bankdate < '$to_date') AND emp_ID != '' $conditionAppend UNION ALL*/
            SELECT SUBSTRING(date, 1, 7) AS date FROM inv_pettycash 
            WHERE (date > '$from_date' AND date < '$to_date') AND emp_ID != '' $conditionAppend ORDER BY date ASC";
    $query = DB::select($sql);
    return $query;
  }
  public static function fnGetExpenseRecordPrevious($request,$from_date) {
    // ACCESS RIGHTS
    // CONTRACT EMPLOYEE
    $conditionAppend = "";
    if (Auth::user()->userclassification == 1) {
      $from_date = Auth::user()->accessDate;
      $conditionAppend = "OR accessFlg = 1";
    }
    // END ACCESS RIGHTS
    $sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM dev_expenses
            WHERE (date <= '$from_date' ) AND emp_ID != '' $conditionAppend UNION ALL 
            /*SELECT SUBSTRING(bankdate, 1, 7) AS date FROM dev_banktransfer
            WHERE (date <= '$from_date' ) AND emp_ID != '' $conditionAppend UNION ALL*/
            SELECT SUBSTRING(date, 1, 7) AS date FROM inv_pettycash 
            WHERE (date <= '$from_date' ) AND emp_ID != '' $conditionAppend ORDER BY date ASC";
    $query = DB::select($sql);
    return $query;
  }
  public static function fnGetExpenseRecordNext($request,$to_date) {
    $sql = "SELECT SUBSTRING(date, 1, 7) AS date FROM dev_expenses 
            WHERE (date >= '$to_date') AND emp_ID != '' UNION ALL 
            SELECT SUBSTRING(date, 1, 7) AS date FROM inv_pettycash 
            WHERE (date >= '$to_date' ) AND emp_ID != '' ORDER BY date ASC";
    $cards = DB::select($sql);
    return $cards;
  }
} 	 			

