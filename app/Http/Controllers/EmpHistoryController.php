<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\EmpHistory;
use DB;
use Input;
use Redirect;
use Session;
use Illuminate\Support\Facades\Validator;

class EmpHistoryController extends Controller {
	function index(Request $request) {
		$empdetails=array();
		if (!isset($request->plimit)) {
          $request->plimit = 50;
        }
        $emphistory = EmpHistory::emphistorydetails($request);
     	$i = 0;
	    foreach($emphistory as $key=>$emp) {
	    	//print_r($emphistory);exit();
	    	$empdetails[$i]['id'] = $emp->id;
	    	$empdetails[$i]['custid'] = $emp->customer_id;
	    	$empdetails[$i]['Emp_ID'] = $emp->Emp_ID;
	    	$empdetails[$i]['FirstName'] = $emp->FirstName;
	    	$empdetails[$i]['LastName'] = $emp->LastName;
	    	$empdetails[$i]['StartDate'] = $emp->start_date;
	    	$empdetails[$i]['CustomerName'] = $emp->customer_name;
	    	$empdetails[$i]['BranchName'] = $emp->branch_name;
	    	$expdetails = EmpHistory::getYrMonCountBtwnDates($emp->start_date,'');
	    	if ($expdetails['year'].".".$expdetails['month'] == 0.0) {
				$empdetails[$i]['experience'] = "-";
			} else {
				$empdetails[$i]['experience'] = $expdetails['year'].".".EmpHistory::fnAddZeroSubstring($expdetails['month']);
			}
	    	$empdetails[$i]['BranchName'] = $emp->branch_name;
        	$i++;
		}
        return view('EmpHistory.index',['emphistory' => $emphistory,
        								'empdetails' => $empdetails,
        								'request' => $request]);
	} 
}