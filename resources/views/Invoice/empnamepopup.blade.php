<style>
  .highlight { background-color: #428eab !important; }
   .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
   input[type=radio] {
    margin: 0px !important;
   }
</style>
<script type="text/javascript">
  $(document).ready(function() {
    $("#Staff").prop("checked", true);
    $('.nonstaff').hide();

    $("#data tr").click(function() {
        var selected = $(this).hasClass("highlight");
        $("#data tr").removeClass("highlight");
        if(!selected)
                $(this).addClass("highlight");
    });

    $("#staffsearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#search tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    $("#staffsearch").on("focus", function() {
      var value = $(this).val().toLowerCase();
      $("#search tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
  function changestaff(id) {
    if (id == '2') {
      $("#staffsearch").val('');
      $("#staffsearch").focus();
      $('.nonstaff').show();
      $('.staff').hide();
    } else {
      $("#staffsearch").val('');
      $("#staffsearch").focus();
      $('.nonstaff').hide();
      $('.staff').show();
    }
  }
  function fngetempid(id,kananame) {
    $('#empid').val(id);
    $('#empKanaName').val(kananame);
  }
</script>
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
         <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_employeenameselection') }}</B></h3>
      </div>
      <div>
        <div style="padding-left: 3%;margin-top: 5px;display: inline-block;">
          <label style="font-weight: normal;">
            {{ Form::radio('Staff', '1', '',array('id' =>'Staff',
                      'name' => 'Staff',
                      'class' => 'comp',
                      'style' => 'margin:0 0 0 !important',
                      'onchange' => 'changestaff("1")')) }}
            <span class="vam">{{ trans('messages.lbl_staff') }}</span>
          </label>
          <label style="font-weight: normal;">
            {{ Form::radio('Staff', '2', '',array('id' =>'Nonstaff',
                      'name' => 'Staff',
                      'class' => 'ntcomp',
                      'style' => 'margin:0 0 0 !important',
                      'onchange' => 'changestaff("2")')) }}
          <span class="vam">{{ trans('messages.lbl_nonstaff') }}</span>
          </label>
        </div>
        <div style="display: inline-block;float: right;margin-top: 5px;">
          {!! Form::text('staffsearch', $request->staffsearch,
                array('','class'=>' form-control box85per pull-left','style'=>'height:30px;','id'=>'staffsearch','placeholder'=>'Search')) !!}
        </div>
      </div>
      <div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
         {{ Form::hidden('empid', '', array('id' => 'empid')) }}
         {{ Form::hidden('empKanaName', '', array('id' => 'empKanaName')) }}
         {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
         <table id="data" class="tablealternate box100per" style="height: 40px;">
            <colgroup>
            <col width="6%">
            <col width="8%">
            <col width="15%">
            <col width="">
            <col width="">
          </colgroup>
          <thead class="CMN_tbltheadcolor">
            <tr class="tableheader fwb tac"> 
              <th class="tac"></th>
              <th class="tac">{{ trans('messages.lbl_sno') }}</th>
              <th class="tac">{{ trans('messages.lbl_empid') }}</th>
              <th class="tac">{{ trans('messages.lbl_empName') }}</th>
              <th class="tac">{{ trans('messages.lbl_kananame') }}</th>
            </tr>
          </thead>
           <tbody id="search" class="staff">
            <?php $i=0; ?>
             @forelse($empname as $key => $value)
             @if($request->cash == 1 || $request->cash == 2 || $request->cash == 3 || $request->cash == 5)
                <tr ondblclick="fndbclickexp('<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo 
                $value->Kananame; ?>');" onclick="fngetDetexp('<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');">
             @else
                <tr ondblclick="fndbclick('<?php $request['table_id'] ?>','<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');" onclick="fngetDet('<?php $request['table_id'] ?>','<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');">
             @endif
                  <td align="center">
                    <input  type="radio" id="<?php echo $value->Emp_ID; ?>" name="empid" onclick="fngetempid(this.id,this.value);">
                  </td>
                  <td align="center">
                    {{ $i + 1 }}
                  </td>
                  <td align="center">
                    {{ $value->Emp_ID }}
                  </td>
                  <td>
                    {{ $value->Empname }}
                  </td>
                  <td>
                    {{ $value->Kananame }}
                  </td>
                </tr>
                <?php $i++; ?>
            @empty
              <tr>
                <td class="text-center" colspan="5" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
              </tr>
             @endforelse
           </tbody>
           <tbody id="search" class="nonstaff">
            <?php $i=0; ?>
             @forelse($empnamenonstaff as $key => $value)
                @if($request->cash == 1 || $request->cash == 2 || $request->cash == 3 || $request->cash == 5)
                  <tr ondblclick="fndbclickexp('<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');" onclick="fngetDetexp('<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');">
                @else
                  <tr ondblclick="fndbclick('<?php $request['table_id'] ?>','<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');" onclick="fngetDet('<?php $request['table_id'] ?>','<?php echo $value->Emp_ID; ?>','<?php echo $value->Empname; ?>','<?php echo $value->Kananame; ?>');">
                @endif
                  <td align="center">
                    <input  type="radio" id="<?php echo $value->Emp_ID; ?>" name="empid" onclick="fngetempid(this.id,this.value);">
                  </td>
                  <td align="center">
                    {{ $i + 1 }}
                  </td>
                  <td align="center">
                    {{ $value->Emp_ID }}
                  </td>
                  <td>
                    {{ $value->Empname }}
                  </td>
                  <td>
                    {{ $value->Kananame }}
                  </td>
                </tr>
                <?php $i++; ?>
            @empty
              <tr>
                <td class="text-center" colspan="5" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
              </tr>
             @endforelse
           </tbody>
         </table>
         </div>
   <div class="modal-footer bg-info mt10">
      <center>
         <button id="add" onclick="return fnaddempid();" class="btn btn-success CMN_display_block box100">
            <i class="fa fa-plus" aria-hidden="true"></i>
               {{ trans('messages.lbl_select') }}
         </button>
         <button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
            <i class="fa fa-times" aria-hidden="true"></i>
               {{ trans('messages.lbl_cancel') }}
         </button>
         <!-- onclick="javascript:return cancelpopupclick();" -->
      </center>
   </div>
      </div>
   </div>