@extends('layouts.app')

@section('content')

{{ HTML::script('resources/assets/js/Setting.js') }}

{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}

<style type="text/css">

	.alertboxalign {

	    	margin-bottom: -60px !important;

	}

	.alert {

		    display:inline-block !important;

		    height:30px !important;

		    padding:5px !important;

		    margin-top: 10px !important;

	}

</style>

<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->

	<article id="setting" class="DEC_flex_wrapper" data-category="setting setting_sub_1">

		<div class="row hline">

			<div class="col-xs-12" style="padding-left: 5px;">

				<!-- Session msg -->

				@if(Session::has('success'))

					<div align="center" class="alertboxalign" role="alert">

						<p class="alert {{ Session::get('alert', Session::get('type') ) }}">

			            	{{ Session::get('success') }}

			          	</p>

					</div>

				@endif

				@php Session::forget('success'); @endphp

				<!-- Session msg -->

				<img class="pull-left box35 mt10 ml10" src="{{ URL::asset('resources/assets/images/setting.jpg') }}">

				<h2 class="pull-left pl5 mt10 CMN_mw150">

					{{ trans('messages.lbl_setting') }}

				</h2>

			</div>

        </div>

        <div class="col-xs-12">

        	<div class="col-xs-6 mb20">

        		@if(Session::get('userclassification') != 1)

	        	<div>

	        		<div class="mt20 box475" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_userdesignation') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_userdesignation') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'sysdesignationtypes'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_userdesignationtype') }}

	        			</a></br>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

        		<div>

	        		<div class="mt20 box475" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_bank') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_india') }}&nbsp;{{ trans('messages.lbl_bank') }}

	        		</label> 

	        		<div>

	        		@php

	        		$tbl_name = 'mstbanks'

	        		@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('singletextpopup',

	        				'{{ $tbl_name }}','{{ 1 }}');">

	        				{{ trans('messages.lbl_bankinsetting') }}

	        			</a></br>

	        			{{--*/ $tbl_name = 'mstbankbranch' /*--}}

						{{--*/ $tbl_select = 'mstbanks' /*--}}

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('selectthreefieldDatasforbank',

	        				'{{ $tbl_name }}','{{ 1 }}','{{ $tbl_select }}','{{ 1 }}');">

	        				{{ trans('messages.lbl_branchinsetting') }}

	        			</a>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

        			<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_japan') }}&nbsp;{{ trans('messages.lbl_bank') }}

        			</label> 

	        		<div>

	        		@php

	        			$tbl_name = 'mstbanks'

        			@endphp

	        			<a class="mt20 ml80 btn-link banklink" id="banklink" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('singletextpopup',

	        				'{{ $tbl_name }}','{{ 2 }}');">

	        				{{ trans('messages.lbl_bankinsetting') }}

	        			</a></br>

	        			{{--*/ $tbl_name = 'mstbankbranch' /*--}}

						{{--*/ $tbl_select = 'mstbanks' /*--}}

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('selectthreefieldDatasforbank',

	        				'{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 1 }}');">

	        				{{ trans('messages.lbl_branchinsetting') }}

	        			</a>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	@endif

	        	<div>

	        		<div class="mt20 box475" 

	        			style="background-color: #43C0E8">

	        			@php

		        		$tbl_name = 'mstbanks'

		        		@endphp

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_estimates') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_estimates') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'dev_estimatesetting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('projecttype','{{$tbl_name}}');">

	        				{{ trans('messages.lbl_projecttypeinsetting') }}

	        			</a></br>

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('uploadestimatepopup');">

	        				{{ trans('messages.lbl_uploadestimatetemplate') }}

	        			</a></br>

	        			@php

	        			$tbl_name = 'inv_estimate_others'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}');">

	        				{{ trans('messages.lbl_Othersstatus') }}

	        			</a></br>

	        				@php

		        			$tbl_name = 'dev_estimate_others'

		        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('others','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_Othersinsetting') }}

	        			</a></br>

	        		</div>

	        		<div class="mt25" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	<div>

	        		<div class="mt20 box475" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_invoice') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_invoice') }}

	        		</label>

	        		<div>

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('uploadinvoicepopup');">

	        				{{ trans('messages.lbl_uploadinvoicetemplate') }}

	        			</a></br>

	        		</div>

	        		<div class="mt25" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	<div>

	        		<div class="mt20 box475" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_expenses') }} 

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_expenses') }}

	        		</label> 

	        		<div>

	        			@php

	        			$tbl_name = 'dev_expensesetting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_mainsubjectinsetting') }}

	        			</a></br>

	        			{{--*/ $tbl_name = 'inv_set_expensesub' /*--}}

						{{--*/ $tbl_select = 'dev_expensesetting' /*--}}

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('selectthreefieldDatas',

	        				'{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 2 }}');">

	        				{{ trans('messages.lbl_subsubjectinsetting') }}

	        			</a>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

        	</div>

        	<div class="col-xs-6 pl40">

        		@if(Session::get('userclassification') != 1)

	        	<div>

	        		<div class="mt20 box455" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_loan') }} 

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_loan') }}

	        		</label> 

	        		<div>

	        			@php

	        			$tbl_name = 'inv_set_loantype'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_loantypeinsetting') }}

	        			</a></br>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	@endif

        		<div>

	        		<div class="mt20 box455" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_pettycash') }} 

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_pettycash') }}

	        		</label> 

	        		<div>

	        			@php

	        			$tbl_name = 'inv_set_transfermain'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_mainsubjectinsetting') }}

	        			</a></br>

	        			{{--*/ $tbl_name = 'inv_set_transfersub' /*--}}

						{{--*/ $tbl_select = 'inv_set_transfermain' /*--}}

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('selectthreefieldDatas',

	        				'{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 3 }}');">

	        				{{ trans('messages.lbl_subsubjectinsetting') }}

	        			</a>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

        		@if(Session::get('userclassification') != 1)

	        	<div>

	        		<div class="mt110 box455" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_salary') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_salary') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'dev_allowancesetting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('Allowance','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_allowance') }}

	        			</a></br>

	        			@php

	        			$tbl_name = 'dev_taxfreesetting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('taxfree','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_taxfree') }}

	        			</a></br>

	        			@php

	        			$tbl_name = 'dev_deductionsetting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('deduction','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_deductioninsetting') }}

	        			</a></br>

	        			@php

	        			$tbl_name = 'dev_bycompany1setting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('bycompany','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_bycompany1') }}

	        			</a></br>

	        			@php

	        			$tbl_name = 'dev_bycompany2setting'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;"

	        			href="javascript:settingpopupsinglefield('bycompany','{{ $tbl_name }}');">

	        				{{ trans('messages.lbl_bycompany2') }}

	        			</a>

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_salary_calc') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'mstsalary'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('singletextpopup',

	        				'{{ $tbl_name }}','{{ 1 }}');">

	        				{{ trans('messages.lbl_salary_det') }}

	        			</a></br>
	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('singletextpopup',

	        				'{{ $tbl_name }}','{{ 2 }}');">

	        				{{ trans('messages.lbl_salary_ded') }}

	        			</a> 

	        		</div>

	        		<div class="" style="border-bottom:1px solid #136E83;margin-top: 54px;"></div>

	        	</div>

	        	<div>

	        		<div class="mt20 box455" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_contract') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_contract') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'inv_set_contractallowance'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        				href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_allowances') }}

	        			</a></br> 

	        		</div>

	        		<div class="mt25" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	<div>

	        		<div class="mt20 box455" 

	        			style="background-color: #43C0E8">

	        			<label class="mt2 ml5"> 

	        				{{ trans('messages.lbl_catagories') }}

	        			</label> 

	        		</div>

	        		<label class="ml40 mt20">>>&nbsp;&nbsp;

	        			{{ trans('messages.lbl_catagories') }}

	        		</label>

	        		<div>

	        			@php

	        			$tbl_name = 'inv_set_salarymain'

	        			@endphp

	        			<a class="mt20 ml80 btn-link" style="color:blue;" 

	        			href="javascript:settingpopupsinglefield('twotextpopup',

	        				'{{ $tbl_name}}')">

	        				{{ trans('messages.lbl_maincategories') }}

	        			</a></br>

	        			{{--*/ $tbl_name = 'inv_set_salarysub' /*--}}

						{{--*/ $tbl_select = 'inv_set_salarymain' /*--}}

	        			<a class="mt20 ml80 btn-link" style="color:blue;"

	        			href="javascript:settingpopupsinglefield('selectthreefieldDatas',

	        				'{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 4 }}');">

	        				{{ trans('messages.lbl_subcategories') }}

	        			</a></br> 

	        		</div>

	        		<div class="mt4" style="border-bottom:1px solid #136E83;"></div>

	        	</div>

	        	@endif

        	</div>

        </div>

	</article>

</div>

<div id="showpopup" class="modal fade" style="width: 775px;">

    <div id="login-overlay">

        <div class="modal-content">

            <!-- Popup will be loaded here -->

        </div>

    </div>

</div>

@endsection

 <?php if(isset($_REQUEST['frompopup'])=="1") {?>

  <!--   <script type="text/javascript">

    $(document).ready(function(){

	    $("#banklink").trigger('click'); 

	});

    </script> -->

    <?php }?>