<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Redirect;
use App\Model\AccessRight;

class Accessrights
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		$roles = array_slice(func_get_args(), 2);
		foreach ($roles as $role) {
			try {
				// $Classificationarray = array(
				// 							"0" => trans('messages.lbl_staff'),
				// 							"1" => trans('messages.lbl_conEmployee'),
				// 							"2" => trans('messages.lbl_subEmployee'),
				// 							"3" => trans('messages.lbl_pvtPerson'),
				// 							"4" => trans('messages.lbl_superadmin'),
				// 							"5" => trans('messages.lbl_auditing'),
				// 						);
				// $roleInformation = in_array($role, $Classificationarray);
				if($role == Auth::user()->userclassification) {
					return $next($request);
				}
			} catch (ModelNotFoundException $exception) {
				dd('Could not find role ' . $role);
			}
		}
		return redirect('/');
    }
}
