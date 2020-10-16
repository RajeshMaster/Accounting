@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/bank.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_2">
	{{ Form::open(array('name'=>'bankindex', 'id'=>'bankindex', 'url' => 'Bank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('sid', $request->sid , array('id' => 'sid')) }}
		{{ Form::hidden('loc', $request->loc , array('id' => 'loc')) }}
		{{ Form::hidden('id','', array('id' => 'id')) }}
		{{ Form::hidden('name', $request->name , array('id' => 'name')) }}
		{{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
		{{ Form::hidden('mainFlg', '' , array('id' => 'mainFlg')) }}
		<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/bank.png') }}">
			<h2 class="pull-left pl5 mt15 CMN_mw150">
					{{ trans('messages.lbl_bank_acc_dtl') }}<span>・</span>@if ($request->filterval == 1)<span class="colbl">{{ trans('messages.lbl_japan') }}</span>@elseif ($request->filterval == 2)<span class="colbl">{{ trans('messages.lbl_india') }}</span>@else<span class="colbl">{{ trans('messages.lbl_japan') }}</span>@endif
			</h2>
		</div>
	</div>
	<div class="box100per pr10 pl10 mt6">
		<div class="col-xs-12 pm0">
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
			<div class="col-xs-6" style="text-align: left;margin-left: -28px;">
				{{ Form::button(
							trans('messages.lbl_japan'),
							array('class'=>'pageload btn btn-link '.$disabledemp,
							'type'=>'button',
							'onclick' => 'javascript:return filter(1)')) 
				}}<span class="">|</span>{{ Form::button(
							trans('messages.lbl_india'),
							array('class'=>'pageload btn btn-link '.$disabledres,
							'type'=>'button',
							'onclick' => 'javascript:return filter(2)')) 
				}}
			</div>
			<div class="col-xs-6 pull-right fwb" style="text-align: right;padding-right: 0px;">
				<a href="javascript:bankreg('{{ date('YmdHis') }}');" class="btn btn-success box12per">
					<span class="glyphicon glyphicon-plus"></span>
					{{ trans('messages.lbl_add') }}
				</a>
			</div>
		</div>
	<div class="pt40 minh400">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="11%">
				<col>
				<col width="15%">
				<col width="14%">
				<col width="14%">
				<col width="11%">
				<col width="5%">
				<col width="6%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_account_no') }}</th>
					<th class="vam">{{ trans('messages.lbl_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_nickname') }}</th>
					<th class="vam">{{ trans('messages.lbl_branch_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_branch_number') }}</th>
					<th class="vam" title="">{{ trans('messages.lbl_mtype') }}</th>
					<th class="vam">{{ trans('messages.lbl_main') }}</th>
				</tr>
			</thead>
			<tbody>
				@if(count($empdetails)!="")
	 				@for ($i = 0; $i < count($empdetails); $i++)
						<tr>
							<td class="bor_rightbot_none text-center">
								{{ ($index->currentpage()-1) * $index->perpage() + $i + 1 }}
							</td>
							<td>
								<a href="javascript:getbankview({{ $empdetails[$i]['id'] }},{{ date('YmdHis') }})" class="anchorstyle">
									{{ $empdetails[$i]['AccNo'] }}
								</a>
							</td>
							<td @if(strlen($empdetails[$i]['FirstName']) > 17) 
										title="{{ $empdetails[$i]['FirstName'] }}"
										@endif>
								@if(singlefieldlength($empdetails[$i]['FirstName'],17))
									{{singlefieldlength($empdetails[$i]['FirstName'],17)}}
								@else
									{{$empdetails[$i]['FirstName']}}
								@endif
							</td>
							<td @if(strlen($empdetails[$i]['BankName']) > 15) 
										title="{{ $empdetails[$i]['BankName'] }}"
										@endif>
								@if(singlefieldlength($empdetails[$i]['BankName'],15))
									{{singlefieldlength($empdetails[$i]['BankName'],15)}}
								@else
									{{$empdetails[$i]['BankName']}}
								@endif
							</td>
							<td @if(strlen($empdetails[$i]['Bank_NickName']) > 13) 
										title="{{ $empdetails[$i]['Bank_NickName'] }}"
										@endif>
								@if(singlefieldlength($empdetails[$i]['Bank_NickName'],13))
									{{singlefieldlength($empdetails[$i]['Bank_NickName'],13)}}
								@else
									{{$empdetails[$i]['Bank_NickName']}}
								@endif
							</td>
							<td @if(strlen($empdetails[$i]['BranchName']) > 13) 
										title="{{ $empdetails[$i]['BranchName'] }}"
										@endif>
								@if(singlefieldlength($empdetails[$i]['BranchName'],13))
									{{singlefieldlength($empdetails[$i]['BranchName'],13)}}
								@else
									{{$empdetails[$i]['BranchName']}}
								@endif
							</td>
							<td @if(strlen($empdetails[$i]['BranchNo']) > 13) 
										title="{{ $empdetails[$i]['BranchNo'] }}"
										@endif>
								@if(singlefieldlength($empdetails[$i]['BranchNo'],13))
									{{singlefieldlength($empdetails[$i]['BranchNo'],13)}}
								@else
									{{$empdetails[$i]['BranchNo']}}
								@endif
							</td>
							<td class="text-center">
								@if($request->filterval == 1)
									{{ getJpnAccountType($empdetails[$i]['Type']) }}
								@elseif($request->filterval == 2)
									{{ getAccountType($empdetails[$i]['Type']) }}
								@endif
							</td>
							<td class="text-center">
								@if($empdetails[$i]['mainFlg']=="0") 
									<a class="colbl anchorstyle" href="javascript:getMain('{{ $empdetails[$i]['id'] }}','{{ $empdetails[$i]['Location'] }}','{{ $empdetails[$i]['mainFlg'] }}')">
										{{ trans('messages.lbl_tomain') }}
									</a>
								@else 
									<a class="anchorstylegreen" href="javascript:getMain('{{ $empdetails[$i]['id'] }}','{{ $empdetails[$i]['Location'] }}','{{ $empdetails[$i]['mainFlg'] }}')">
										<b>{{ trans('messages.lbl_main') }}</b>
									</a>
								@endif
							</td>
						</tr>
					@endfor
				@else
						<tr>
							<td class="text-center colred" colspan="9">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
				@endif
			</tbody>
		</table>
	</div>
	<div class="text-center">
		@if(!empty($index->total()))
			<span class="pull-left mt10">
				{{ $index->firstItem() }} ~ {{ $index->lastItem() }} / {{ $index->total() }}
			</span>
		@endif 
		{{ $index->links() }}
		<div class="CMN_display_block flr mr10">
			{{ $index->linkspagelimit() }}
		</div>	
	</div>
		{{ Form::close() }}
</article>
</div>
@endsection