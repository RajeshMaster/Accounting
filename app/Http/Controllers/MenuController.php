<?php
namespace App\Http\Controllers;
use App\Model\Invoice;
use App\Model\Estimation;
use Illuminate\Http\Request;
use App\Http\Requests;

class MenuController extends Controller {
	function index(Request $request) { 
		$amtinsert = Invoice::amtdet(); 
		$insert = Invoice::amtnewtbl($amtinsert);
		$estinsert = Estimation::estworkdet(); 
		$estworkinsert = Estimation::estnewtbl($estinsert);
		return view('Menu.index',['request'=> $request]);
	}
}

?>