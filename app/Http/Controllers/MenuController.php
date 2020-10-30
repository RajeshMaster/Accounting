<?php
namespace App\Http\Controllers;
use App\Model\Invoice;
use App\Model\Estimation;
use Illuminate\Http\Request;
use App\Http\Requests;

class MenuController extends Controller {
	function index(Request $request) { 
		return view('Menu.index',['request'=> $request]);
	}
}

?>