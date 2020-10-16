<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use Session;
use DB;
class AjaxController extends Controller {
   public function index(Request $request) {
	   	if ($request->langvalue == "en") {
	   		Session::put('languageval', 'en');
	   		Session::put('setlanguageval', 'jp');
	   	} else {
	   		Session::put('languageval', 'jp');
	   		Session::put('setlanguageval', 'en');
	   	}
    }
}