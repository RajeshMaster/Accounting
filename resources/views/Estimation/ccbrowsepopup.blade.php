<style>
   .modal {
    position: fixed;
    top: 50% !important;
    left: 50%;
    transform: translate(-50%, -50%);
  }
   .imgtableheight{height: 15px;width: 15px;}
   .dispnone{display: none;}
</style>
<script type="text/javascript">
   var emailid1 = '@php echo "Other Mail Id 1 " @endphp';
   var emailid = '@php echo "Other Mail Id " @endphp';
</script>
   <div class="modal-content" style="height: 650px!important;">
      <div class="modal-header" style="padding:8px;">
         <button type="button" class="close" onclick="return ccPopupClose();" style="color: red;" aria-hidden="true">&#10006;</button>
         <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_mailaddress') }}</B></h3>
      </div>
      <div class="modal-body">
         {{ Form::hidden('sample',$request->sample, array('id' => 'sample')) }}
         <div class="col-md-12 pm0" style="border-bottom: 1px solid gray !important;">
            <?php if($customercnt=="0") { ?>
               <div class="divlabel mb9 mt15 tac">
                  <span class="fnt_blck fwb f13"><?php echo $lbl_NoDataFound;?></span>
               </div>
            <?php } else { ?>
               <div class="box100per" style="height:400px;overflow: auto;">
                  <?php for($i=0;$i<$customercnt;$i++) { ?>
                  <?php if($customerview[$i]['Counts']!="0" && $onlyoncew!="1") { ?>
                  <span class="fwb"><?php echo "With Incharge"; $onlyoncew="1";?></span>
                  <fieldset style="margin-top: 0px;padding: 10px;">
                  <?php } ?>
                  <?php if($customerview[$i]['Counts']=="0" && $onlyonce!="1") { ?>
                  </fieldset>
                  <span class="fwb"><?php echo "Without Incharge"; $onlyonce="1"; ?></span>
                  <fieldset style="margin-top: 0px;padding: 10px;">
                  <?php } ?>
                     <div class="box100per">
                        <div class="" id="CCcustopen<?php echo $i; ?>" style="display: none;">
                        <a style="text-decoration: none" class="tdn" href="javascript:fnCCcustplus(1,'<?php echo $i; ?>');"><img id="exceldownload" src="{{ URL::asset('resources/assets/images/plus.png') }}" class="vam csrp ml5 mt5 fll" style="height:15px;"><span class="pl5 csrp"><?php echo $customerview[$i]['customer_name']; ?></span></a></div>
                        <div class="" id="CCcustclose<?php echo $i; ?>">
                        <a style="text-decoration: none" class="tdn" href="javascript:fnCCcustplus(2,'<?php echo $i; ?>');"><img id="exceldownload" src="{{ URL::asset('resources/assets/images/minus.png') }}" class="vam csrp ml5 mt5 fll" style="height:15px;"><span class="pl5 csrp"><?php echo $customerview[$i]['customer_name']; ?></span></a></div>
                     </div>
                     <div id="CCinchargehid<?php echo $i; ?>">
                        <?php if($inchargecnt[$i]>1) { ?>
                        <div class="box100per" id="ccalldivid<?php echo $i; ?>">
                        <div class="pl25"><label class="pl5 fwn"><input class="CCchecksingle<?php echo $i; ?> CCmailallChk mr5" type="checkbox" name="CCcheck_all[]" id="CCcheck_all" value="CCchecksingle<?php echo $i; ?>"  onchange="return fnCancel_check();"　 onclick="return fnCCselectallemail('<?php echo $i; ?>');"><?php echo "All"; ?></label></div>
                        </div>
                        <?php } ?>
                     <?php  $s=0; for($j=0;$j<$inchargecnt[$i];$j++) { ?>
                        <div class="box100per">
                        <div class="pl25"><label class="pl5 fwn"><input <?php if($inchargeview[$i][$j]['disabled']==1) { ?> class="mr5" disabled="" <?php } else { $s=$s+1; ?>  class="CCcheckboxt<?php echo $i; ?> CCmailChk mr5" <?php } ?> type="checkbox" name="CCmail[]" id="CCmail<?php echo $i.$j; ?>" value="<?php echo $inchargeview[$i][$j]['id']."$".$inchargeview[$i][$j]['mailId']; ?>"  onchange="return fnCancel_check();"　 onclick="return fnCCunselectallemail('<?php echo $i; ?>');" data-id = "<?php echo $i.','.$i; ?>"><?php echo $inchargeview[$i][$j]['inchargeName']; ?><span><</span><?php echo $inchargeview[$i][$j]['mailId']; ?><span>></span></label></div>
                        </div>
                     <?php } ?>
                     <?php if(isset($s) && $s==1) { ?>
                        <script type="text/javascript">
                           fnccdisableall('<?php echo $i; ?>');
                        </script>
                     <?php } ?>
                     </div>
                  <?php } ?>
                  </fieldset>
                  <?php if(count($getothermailidfinal)!=0) { ?>
                  <span class="fwb"><?php echo "Others"; ?></span>
                  <fieldset style="margin-top: 0px;padding: 10px;">
                     <?php for($p=0;$p<count($getothermailidfinal);$p++) { ?>
                     <div class="box100per">
                     <div class=""><label class="pl5 fwn"><input <?php if (in_array($getothermailidfinal[$p], $getothermailidsame)) { ?> disabled="" <?php } ?> class="CCcheckboxt<?php echo $i; ?> CCmailChk mr5" type="checkbox" name="CCmail[]" id="CCmail<?php echo $i.$j.$p; ?>" value="<?php echo $getothermailidfinal[$p]."$".$getothermailidfinal[$p]; ?>" data-id = "<?php echo $i.','.$i; ?>"><?php echo $getothermailidfinal[$p]; ?></label></div>
                     </div>
                     <?php } ?>
                  </fieldset>
                  <?php } ?>
               </div>
            <?php } ?>
         </div>
         <div class="col-xs-12 pm0" style="height:110px !important;">
         <div class="col-xs-12 pm0 mt5" id="othercc_1">
            <div class="col-xs-3 pm0 text-right clr_blue">
               <label class="">
                  {{ Form::label('email', trans('messages.lbl_othermailid')." 1", 
                                       array('class' => 'txt mt2',
                                          'id'=>'otheremaillbl_1')) }}<span class="ml2 white"> * </span></label>
            </div>
            <div class="col-xs-9 pm0">
               {{ Form::text('name_1','',array(
                                 'id'=>'name_1',
                                 'placeholder' => trans('messages.lbl_name'),
                                 'class'=>'box30per inb form-control')) }}
               {{ Form::text('otherccmail_1','',array(
                                 'id'=>'otherccmail_1',
                                 'placeholder' => trans('messages.lbl_email'),
                                 'class'=>'box50per inb form-control')) }}
               <img id="add_emailid" 
                        onclick="javascript:cloneadd();"
                        class="center ml5 imgtableheight"
                        style="cursor: pointer;" 
                        title="{{ trans('messages.lbl_add') }}"
                        src="{{ URL::asset('resources/assets/images/plus.png') }}">
               <img class="center ml5 imgtableheight dispnone"
                        id="removeemailid_1"
                        onclick="javascript:cloneremove(this);"
                        style="cursor: pointer;" 
                        title="{{ trans('messages.lbl_remove') }}"
                        src="{{ URL::asset('resources/assets/images/minus.png') }}">
            </div>
         </div>
         <div id="forccappend"></div>
         </div>
      </div>
      <div class="col-md-12 mt5">
         <center class=" pt10 pb10 ml15 mr15" style="background-color: #CCf2ff;border-radius: 8px;border: 1px solid #136E83;">
            <button id="add" onclick="return CCcheckmailid();" class="btn btn-success CMN_display_block box100">Add
            </button>
            <button onclick="return ccPopupClose();" class="btn btn-danger CMN_display_block box100">
               Cancel
            </button>
            <!-- onclick="javascript:return cancelpopupclick();" -->
         </center>
      </div>
   </div>
<script type="text/javascript">
   var email = $("#ccname").val();
   if (email != "") {
      email = email.split(',');
      jQuery(".CCmailChk").each(function(){
         for (i = 0; i < email.length; i++) {
            var res = $(this).val().split("$");
            if (res[1] == email[i].trim()) {
               var ids = $(this).attr("data-id");
               ids=ids.split(',');
               $("#CCinchargehid"+ids[1]).css("display","block");
               $("#CCcustclose"+ids[0]).css("display","block");
               $("#CCcustopen"+ids[0]).css("display","none");
               $(this).prop("checked",true);
            }
         }
       });
      var CCallhidden = $("#CCallhidden").val();
      if(CCallhidden !="") {
         CCallhidden = CCallhidden.split(',');
         for (i = 0; i < CCallhidden.length-1; i++) {
            $("."+CCallhidden[i]).attr("checked", true);
         }
      }
   }
</script>