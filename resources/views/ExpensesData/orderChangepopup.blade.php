{{ HTML::script('resources/assets/js/expensesData.js') }}

<style>

	.highlight { background-color: #428eab !important; }

	 .modal {

			position: fixed;

			top: 50% !important;

			left: 50%;

			transform: translate(-50%, -50%);

	 }



	 #dwnArrow,#upArrow {

		text-decoration:none;

		font-size:22px;

		color:#bbb5b5;

		box-shadow: none;

		background-color: Transparent;

		border: none; 

		padding: 0px;

	}

	 

</style>



<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';

	$(document).on("click", "#swaptable1 tr", function(e) {

		if (event.target.nodeName != "SPAN") {

			$(this).find('td input:radio').prop('checked', true);

			document.getElementById("edit").disabled = false;

			$("#edit").css("background-color","#FF8C00");

			$('#textbox1').val('');

		}

	});

	$(document).ready(function() {

		$('#swaptable1').delegate('tr', 'click' , function(){

			if (event.target.type !== 'radio') {

				if (event.target.nodeName != "SPAN") {

					$(this).find('input[type=radio]').prop('checked', true).trigger("click");

				}

			}

		});

	});



	

</script>



{{ Form::hidden('type', '1', array('id' => 'type')) }}

{{ Form::hidden('process', '1', array('id' => 'process')) }}

{{ Form::hidden('hid_txtval', '', array('id' => 'hid_txtval')) }}

{{ Form::hidden('confirmflg', '', array('id' => 'confirmflg')) }}

{{ Form::hidden('flag', '', array('id' => 'flag')) }}

{{ Form::hidden('flashmessage', '', array('id' => 'flashmessage')) }}

{{ Form::hidden('hdnprocessid', '', array('id' => 'hdnprocessid')) }}



<div class="modal-content">

	<div class="modal-header">

		 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>

		 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_changeOrder') }}</B></h3>

	</div>

	<div class="col-xs-12 mt5">

		<div class="col-xs-6 clr_black text-left mt10">

			@if(isset($getBankDtls[0]->Bank_NickName))

				<h3>

					{{ $getBankDtls[0]->Bank_NickName }} - {{ $getBankDtls[0]->accountNumberFrom }}

				</h3>

			@endif

		</div>

		<div class="col-xs-6 clr_black text-right mt10" style="float: right">

			<button type="button" id="dwnArrow" class="fa fa-arrow-down" disabled="disabled"  style="" onclick="getdowndata()">

			</button>

			<button type="button" id="upArrow" class="fa fa-arrow-up" disabled="disabled"  style="" onclick="getupdata()">

			</button>

			<button type="button" style="background-color: #bbb5b5;" class="btn add mt10 mb10" id="commit_button" disabled="disabled" onclick="getcommitCheck('{{ $request->tablename }}','{{ $request->screen_name }}','');">

				<i class="glyphicon glyphicon-check"></i> {{ trans('messages.lbl_updatetimesheet') }} 

			</button>

		</div>

	</div>

	<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">

		 <table id="swaptable1" class="tablealternate box100per" style="height: 40px;">

				<colgroup>

				<col width="5%">

				<col width="14%">

				<col width="25%">

				<col width="25%">

				<col width="16%">


			</colgroup>

			<thead class="CMN_tbltheadcolor">

				<tr class="tableheader fwb tac"> 

					<th class="tac"></th>

					<th class="tac">{{ trans('messages.lbl_sno') }}</th>

					<th class="tac">{{ trans('messages.lbl_subject') }}</th>

					<th class="tac">{{ trans('messages.lbl_content') }}</th>

					<th class="vam">{{ trans('messages.lbl_debit') }}</th>

				</tr>

			</thead>

			<tbody>


			{{--*/ $idOrder="" /*--}}

			@php $i = 0; @endphp

			@forelse($getBankDtls as $key => $data)

				{{--*/ $j = $key /*--}}



				<tr>

					<td>

						<input type="radio" 

									name="rdoedit" 

									id="rdoedit{{ $data->id }}" 

									class="h13 w13 rdoedit" 

									value="{{ $data->id }}" 

									onclick="fnrdocheck(

											'{{ $data->Subject }}',

											'{{ $data->id }}',

											'<?php echo count($getBankDtls);?>',

											'<?php echo $j;?>')">

						{{ Form::hidden('id', $data->id , array('id' => 'id')) }}	

					</td>



					<td>{{ $i+1 }}</td>


					<td align="center">

						{{  $data->Subject}}

					</td>

					<td align="center">

						@if($data->content != '')

							{{  $data->content}}

						@else

							{{  $data->Empname }}

						@endif

					</td>

					<td>

						@php $debitAmt = $data->amount + $data->fee ; @endphp

						{{ number_format($debitAmt) }}


					</td>


				</tr>

				@php $i++; @endphp





				{{--*/ $idOrder .= $data->orderId."," /*--}}



			@empty

				<tr>

					<td class="text-center" colspan="4" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>

				</tr>

			@endforelse

			{{--*/ $idOrder = rtrim($idOrder, ",") /*--}}



			{{ Form::hidden('idOriginalOrder', $idOrder, array('id' => 'idOriginalOrder')) }}



			</tbody>

		</table>

	</div>

 	<div class="modal-footer bg-info mt10">

		<center>

			 <button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">

					<i class="fa fa-times" aria-hidden="true"></i>

						 {{ trans('messages.lbl_cancel') }}

			 </button>

		</center>

	</div>

</div>

