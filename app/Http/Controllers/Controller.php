<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Session;
use App;
use View;
use DB;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    public function __construct()
    {
      // IF ACCESSDATE IS EMPTY
        if (!empty(Auth::user())) {
          if (Auth::user()->userclassification == 1 && Auth::user()->accessDate == "") {
            Auth::user()->accessDate = "0001-01-01";
          }
        }
      // END IF ACCESSDATE IS EMPTY
    	// CHANGE LANGUAGE PROCESS
		if (Session::get('languageval') == "en") {
	        App::setLocale("en");
      	} else {
	        App::setLocale("jp");
      	}
      	// END CHANGE LANGUAGE PROCESS
    }
}