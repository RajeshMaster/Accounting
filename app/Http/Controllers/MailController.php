<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Mail;
use DB;
use Input;
use Redirect;
use Session;
ini_set('max_execution_time', 0);

class MailController extends Controller {
	function index(Request $request) { 
		return view('Mail.index',['request' => $request]);
	}
}