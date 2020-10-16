<script type="text/javascript">
  $(document).ready(function() {
    // initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.addeditprocess').click(function () {
        $("#uploadpopup").validate({
            showErrors: function(errorMap, errorList) {
            // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                        var $element = $(element);
                        $element.data("title", "") // Clear the title - there is no error associated anymore
                                .removeClass("error")
                                .tooltip("destroy");
                });
                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                        var $element = $(error.element);
                        $element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                                .data("title", error.message)
                                .addClass("error")
                                .tooltip(); // Create a new tooltip based on the error messsage we just set in the title
                });
            },
            rules: {
                xlfile : {required: true,extension: "xls,xlsx", filesize : (2 * 1024 * 1024)},
            },
            submitHandler: function(form) { // for demo
                    // var confirmprocess = confirm("Do You Want Update File?");
                    if(confirm("Do You Want Update File?")) {
                        pageload();
                        return true;
                    } else {
                        return false
                    }
            }
        });
        $.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + err_fieldreq;
        }
        $.validator.messages.extension = function (param, input) {
            return err_extension;
        }
    });
    $('#swaptable tr').click(function(event) {
      if (event.target.type !== 'radio') {
        if (event.target.nodeName != "SPAN") {
          $(':radio', this).trigger('click');
        }
      }
    }
                            );
  }
                   );
  var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style>
  .scrollbar {
    overflow-y: 220px;
    padding: 5px;
    height: 100%;
    width:100%;
    max-width: 100%;
    margin-bottom: 0px;
  }
  .table_Scroll_limit_set{
    overflow-y: scroll;
    overflow-x: hidden;
    height: 214px;
    background-color: #FFFFFF;
  }
  .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
<body>
{{ Form::open(array('name'=>'uploadpopup', 'id'=>'uploadpopup',
                      'url' => 'Setting/settingpopupupload?time='.date('YmdHis'),
                      'files'=>true,
                      'method' => 'POST')) }}
  <div class="modal-content">
      <div class="modal-header">
        <div class="col-md-2 pull-right mt10">
           <a data-dismiss="modal" onclick="fnclosepopdig();" class="close fr">
            &#10006; </a>
        </div>
          <div class="box70per ml10  h33">
            <h2 class="fs30 mt10">{{ $headinglbl }}
            {{ Form::hidden('heading',  $headinglbl, array('id' => 'heading')) }}
            </h2>
          </div>
      </div>
      <div class="modal-body">
        <div class="box100per ">
          <fieldset class="box100per" style="height: 50px;">
            <div class="box25per pull-left text-right clr_blue fwb mt10 mb10 ml10 h50">
            {{ trans('messages.lbl_uploadfile') }}
            <span style="color:red;"> * 
            </span>
            </div>
            <div class="ml15 pull-left box70per mb10 mt10">
            {{ Form::file('xlfile',array('id'=>'xlfile','name' => 'xlfile',
                'data-label' => trans('messages.lbl_uploadfile'),
                'height' =>'30px',
                'class'=>'box70per')) }}
             </div>
          </fieldset>
        </div>
        <div class="modal-footer bg-info">
          <center>
            <button type="submit" name ="upload" id="upload" 
                    class="btn btn-success btn box15per addeditprocess">
              <i class="fa fa-edit">
              </i>{{ trans('messages.lbl_upload') }}
            </button>
            <button type="button" onclick="divpopupclose();" 
                  class="btn btn-danger CMN_display_block box18per button" >
            <i class="fa fa-remove" aria-hidden="true">
            </i> 
            {{ trans('messages.lbl_cancel') }}
          </button>
          </center>
        </div>
        </div>
</div>
{{ Form::close() }}
</body>
