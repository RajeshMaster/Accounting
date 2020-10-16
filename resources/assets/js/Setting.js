function settingpopupsinglefield(screen_name,tablename,location,tableselect,parametersub,popupname) {

    var req = "?screen_name="+screen_name+

				"&tablename="+tablename+

				"&location="+location;



	if (screen_name == "singletextpopup") {

		popupopenclose(1);

	    $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename+'&location='+location);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    }else if (screen_name == "twotextpopup") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/twotextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    }  else if (screen_name == "selectthreefieldDatasforbank") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/selectthreefieldDatas?screen_name='+screen_name+'&tablename='+tablename+'&tableselect='+tableselect+'&location='+location+'&parametersub='+parametersub);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "selectthreefieldDatas") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/selectthreefieldDatas?screen_name='+screen_name+'&tablename='+tablename+'&tableselect='+tableselect+'&location='+location+'&parametersub='+parametersub);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "projecttype") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "others") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "taxfree") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    }  else if (screen_name == "Allowance") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "deduction") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else if (screen_name == "bycompany") {

        popupopenclose(1);

        $('#showpopup').load('../Setting/singletextpopup?screen_name='+screen_name+'&tablename='+tablename);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    } else {

        popupopenclose(1);

        $('#showpopup').load('../Setting/uploadpopup?screen_name='+screen_name);

        $("#showpopup").modal({

           backdrop: 'static',

            keyboard: false

        });

    $('#showpopup').modal('show');

    }

}

function fnrdocheck(textbox1,editid) {


    $('#hid_txtval').val(textbox1);

    $('#rdoid').val(editid);

    // EDIT BUTTON ENABLE

    document.getElementById("edit").disabled = false;

    $("#rdoedit"+editid).attr("checked", true);

    $("#edit").css("background-color","#FF8C00");

}



function fneditcheck() {

    var editid = $('#rdoid').val();

    $("#edit").addClass("CMN_cursor_default");

    $('#process').val(2);

    document.getElementById("edit").disabled = true;

    $("#add_var").hide();

    $("#update_var").show();

    var dataname = $('#dataname'+editid).text();

    var hid_txtval = $('#hid_txtval').val();

    $('#textbox1').val(dataname);

    return false;

}

function fnrdochecktwofield(textbox1,textbox2,editid) {

    $('#hid_txtval').val(textbox1);

    $('#hid_txt2val').val(textbox2);

    $('#rdoid').val(editid);

    // EDIT BUTTON ENABLE

    document.getElementById("edit").disabled = false;

    $("#rdoedit"+editid).attr("checked", true);

    $("#edit").css("background-color","#FF8C00");

}



function fneditchecktwofield() {

    var editid = $('#rdoid').val();

    $("#edit").addClass("CMN_cursor_default");

    $('#process').val(2);

    document.getElementById("edit").disabled = true;

    $("#add_var").hide();

    $("#update_var").show();

    var dataname1 = $('#dataname1'+editid).text();

    var dataname2 = $('#dataname2'+editid).text();

    $('#textbox1').val(dataname1);

    $('#textbox2').val(dataname2);

    return false;

}

function fnrdocheckthreefield(selectbox1,textbox1,textbox2,editid) {

    $('#hid_txt3val').val(selectbox1);

    $('#hid_txtval').val(textbox1);

    $('#hid_txt2val').val(textbox2);

    $('#rdoid').val(editid);

    // EDIT BUTTON ENABLE

    document.getElementById("edit").disabled = false;

    $("#rdoedit"+editid).attr("checked", true);

    $("#edit").css("background-color","#FF8C00");

}



function fneditcheckthreefield() {

    var editid = $('#rdoid').val();

    $("#edit").addClass("CMN_cursor_default");

    $('#process').val(2);

    document.getElementById("edit").disabled = true;

    $("#add_var").hide();

    $("#update_var").show();

    var dataname1 = $('#dataname1'+editid).val();

    var dataname2 = $('#dataname2'+editid).text();

    var dataname3 = $('#dataname3'+editid).text();

    $('#selectbox1').val(dataname1);

    $('#textbox1').val(dataname2);

    $('#textbox2').val(dataname3);

    return false;

}

function fnaddeditsinglefield(location,mainmenu,tablename,flag,messageflag) {

	mainmenu = "Setting";

      	if($("#textbox1").val()==""){

      	$("#empty_textbox1").show(); 

      	$("#textbox1").focus();

        } else if($("#textbox1").val()!=""){

            $("#empty_textbox1").hide(); 

            var opr=$('#process').val();

            var messageflags = $('#flashmessage').val(messageflag)

            if ($('#process').val() == 1) {

                var err_cnfirm = confirm(err_confreg);

            } else {

                var err_cnfirm = confirm(err_confup);

            }

            if(err_cnfirm) {   

                var textbox1 =  $('#textbox1').val();

                $('#textbox1').val(textbox1);

                $('#location').val(location);

                $('#tablename').val(tablename);

                $('#flag').val(flag);

                var editid = $('#rdoid').val();

                var url = 'SingleFieldaddedit';

                    $.ajax({

                    async: true,

                        type: 'GET',

                        url: url,

                        data: {"textbox1": textbox1,"location": location,"mainmenu": mainmenu,"tablename": tablename,"id": editid,"flag": flag},

                      	success: function(data) {



                             if (data != "") {

                                var res = $.parseJSON(data);

                                var orderid=res.orderid;

                                var totalid=res.totalid;

                                var data=orderid;

                                  $('#textbox1').val('');

                                  if(opr==1) {

                                    var tempdata= parseInt(data)+1;

                                    var data='<tr class="h37" onclick="fnrdocheck(\''+textbox1+'\',\''+totalid+'\')"><td class="tac" title="Select"><input type = "radio" name="rdoedit" id="rdoedit'+data+'" class="h13 w13" onclick="fnrdocheck(\''+textbox1+'\',\''+data+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+data+'"></td><td class="text-center pt7" title="S.No">'+data+'</td><td class="pl7 pt7" id="dataname'+totalid+'">'+textbox1+'</td><td class="tac pt7" title="Use/Not Use"><a href="javascript:useNotuse(\''+totalid+'\',\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+tempdata+'" name="editid'+tempdata+'" type="hidden" value="'+totalid+'"></td></tr>';

                                    $('#swaptable1 tr:last').after(data);

                                    $("#popupsessionreg").css("display", "block");

                                     $("#popupsessionupd").css("display", "none");

                                   } else {

                                    $("#dataname"+editid).text(textbox1);

                                    $("#add_var").show();

                                    $("#update_var").hide();

                                    $('#process').val(1);

                                    $("#popupsessionupd").css("display", "block");

                                    $("#popupsessionreg").css("display", "none");



                                   }

                                    var rowCount = $('#swaptable1 tr').length;

                                    if ($('#swaptable1 tr').hasClass('nodata')) {

                                        $('#swaptable1 tr:first').remove();

                                }



                             }

                      	},

	                       error: function(data) {

	                    }

                    });

            }

    }

}

function fnaddedittwofield(location,mainmenu,tablename,flag) {

    mainmenu = "Setting";

    var opr=$('#process').val();

        if($("#textbox1").val()==""){

        $("#empty_textbox1").show(); 

        $("#textbox1").focus();

        } else if($("#textbox2").val()==""){

        $("#empty_textbox2").show(); 

        $("#textbox2").focus();

        $("#empty_textbox1").hide(); 

        } else {

            $("#empty_textbox1").hide(); 

            $("#empty_textbox2").hide(); 

            if ($('#process').val() == 1) {

                var err_cnfirm = confirm(err_confreg);

            } else {

                var err_cnfirm = confirm(err_confup);

            }

            if(err_cnfirm) {   

                var textbox1 =  $('#textbox1').val();

                $('#textbox1').val(textbox1);

                var textbox2 =  $('#textbox2').val();

                $('#textbox2').val(textbox2);

                $('#location').val(location);

                $('#tablename').val(tablename);

                $('#flag').val(flag);

                var editid = $('#rdoid').val();

                var url = 'twoFieldaddedit';

                    $.ajax({

                    async: true,

                        type: 'GET',

                        url: url,

                        data: {"textbox1": textbox1,"textbox2": textbox2,"location": location,"mainmenu": mainmenu,"tablename": tablename,"id": editid,"flag": flag},

                        success: function(data) {

                           if (data != "") {

                                // $('#swaptable1 tr:last').remove();

                                $('#textbox1').val('');

                                $('#textbox2').val('');

                                if(opr==1) {

                                    var tempdata= parseInt(data)+1;

                                    var data='<tr class="h37"><td class="tac" title="Select"><input type = "radio" name="rdoedit" id="rdoedit'+data+'" class="h13 w13" onclick="fnrdochecktwofield(\''+textbox1+'\',\''+textbox2+'\',\''+data+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+data+'"></td><td class="text-center pt7" title="S.No">'+data+'</td><td class="pl7 pt7" id="dataname1'+data+'">'+textbox1+'</td><td class="pl7 pt7" id="dataname2'+data+'">'+textbox2+'</td><td class="tac pt7" title="Use/Not Use"><a href="javascript:useNotuse(\''+data+'\',\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+tempdata+'" name="editid'+tempdata+'" type="hidden" value="'+data+'"></td></tr>';

                                    $('#swaptable1 tr:last').after(data);

                                    $("#popupsessionreg").css("display", "block");

                                    $("#popupsessionupd").css("display", "none");

                                    $("#swaptable1 tr:last").css('cursor', 'hand');

                                } else {

                                    $("#dataname1"+editid).text(textbox1);

                                    $("#dataname2"+editid).text(textbox2);

                                    $("#add_var").show();

                                    $("#update_var").hide();

                                    $('#process').val(1);

                                    $("#popupsessionupd").css("display", "block");

                                    $("#popupsessionreg").css("display", "none");

                                }

                                var rowCount = $('#swaptable1 tr').length;

                                if ($('#swaptable1 tr').hasClass('nodata')) {

                                    $('#swaptable1 tr:first').remove();

                                }

                            }

                                

                        },

                           error: function(data) {

                        }

                    });

            }

    }

}



function fnaddeditthreefieldforbank(location,mainmenu,tablename,flag) {

    mainmenu = "Setting";

    var opr=$('#process').val();

        if($("#selectbox1").val()==""){

            $("#empty_selectbox1").show();

            $("#empty_textbox1").hide(); 

            $("#empty_textbox2").hide(); 

            $("#selectbox1").focus();

        } else if($("#textbox1").val()==""){

            $("#empty_textbox1").show(); 

            $("#empty_selectbox1").hide();

            $("#empty_textbox2").hide();

            $("#textbox1").focus();

        } else if($("#textbox2").val()==""){

            $("#empty_textbox2").show(); 

            $("#empty_selectbox1").hide();

            $("#empty_textbox1").hide();

            $("#textbox2").focus();

        } else {

            $("#empty_selectbox1").hide();

            $("#empty_textbox1").hide(); 

            $("#empty_textbox2").hide();

            if ($('#process').val() == 1) {

                var err_cnfirm = confirm(err_confreg);

            } else {

                var err_cnfirm = confirm(err_confup);

            }

            if(err_cnfirm) {

                var selectbox1 =  $('#selectbox1').val();

                $('#selectbox1').val(selectbox1);

                var textbox1 =  $('#textbox1').val();

                $('#textbox1').val(textbox1);

                var textbox2 =  $('#textbox2').val();

                $('#textbox2').val(textbox2);

                $('#location').val(location);

                $('#tablename').val(tablename);

                $('#flag').val(flag);

                var editid = $('#rdoid').val();

                var url = 'threeFieldaddeditforbank';

                    $.ajax({

                    async: true,

                        type: 'GET',

                        url: url,

                        data: {"selectbox1": selectbox1,"textbox1": textbox1,"textbox2": textbox2,"location": location,"mainmenu": mainmenu,"tablename": tablename,"id": editid,"flag": flag},

                        success: function(data) {

                            if (data != "") {

                                var res = $.parseJSON(data);

                                var orderid=res.orderid;

                                var totalid=res.totalid;

                                var data=orderid;

                                 $('#selectbox1').val('');

                                 $('#textbox1').val('');

                                 $('#textbox2').val('');

                                 if(opr==1) {

                                    var tempdata= parseInt(data)+1;

                                    var tempselecctvalue=$("#selectbox1 option[value="+selectbox1+"]").text();

                                    var data='<tr class="h37" onclick="fnrdocheckthreefield(\''+selectbox1+'\',\''+textbox1+'\',\''+textbox2+'\',\''+totalid+'\')"><td class="tac" title="Select"><input type = "radio"" name="rdoedit" id="rdoedit'+totalid+'" class="h13 w13" onclick="fnrdocheckthreefield(\''+tempselecctvalue+'\',\''+textbox1+'\',\''+textbox2+'\',\''+totalid+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+data+'"></td><td class="text-center pt7" title="S.No">'+data+'</td><td class="pl7 pt7" id="datanametd2'+totalid+'">'+tempselecctvalue+'<input type="hidden" name="hiddenselectvalue" id="dataname1'+totalid+'" value="'+selectbox1+'"></td><td class="pl7 pt7" id="dataname2'+totalid+'">'+textbox1+'</td><td class="pl7 pt7" id="dataname3'+totalid+'">'+textbox2+'</td><td class="tac pt7" title="Use/Not Use"><a href="javascript:useNotuse(\''+totalid+'\',\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+tempdata+'" name="editid'+tempdata+'" type="hidden" value="'+totalid+'"></td></tr>'; 

                                    $('#swaptable1 tr:last').after(data);

                                    $("#popupsessionreg").css("display", "block");

                                    $("#popupsessionupd").css("display", "none");

                                    $("#swaptable1 tr:last").css('cursor', 'hand');



                                } else {

                                var tempselecctvalue=$("#selectbox1 option[value="+selectbox1+"]").text();

                                   $("#datanametd2"+editid).html('');

                                   var tempdata=tempselecctvalue+'<input type="hidden" name="hiddenselectvalue" id="dataname1'+editid+'" value="'+selectbox1+'">'

                                   $("#datanametd2"+editid).html(tempdata);

                                    $("#dataname2"+editid).text(textbox1);

                                    $("#dataname3"+editid).text(textbox2); 

                                    $('#dataname1'+editid).val(selectbox1);

                                    $("#add_var").show();

                                    $("#update_var").hide();

                                    $('#process').val(1);

                                    $("#popupsessionupd").css("display", "block");

                                    $("#popupsessionreg").css("display", "none");



                                }

                                    var rowCount = $('#swaptable1 tr').length;

                                    if ($('#swaptable1 tr').hasClass('nodata')) {

                                        $('#swaptable1 tr:first').remove();

                                }



                            }

                        },

                           error: function(data) {

                        }

                    });

            }

    }

}

function fnaddeditthreefield(mainmenu,tablename,flag) {

    mainmenu = "Setting";

    var opr=$('#process').val();

        if($("#selectbox1").val()==""){

            $("#empty_selectbox1").show(); 

            $("#selectbox1").focus();

            $("#empty_textbox1").hide(); 

            $("#empty_textbox2").hide();

        } else if($("#textbox1").val()==""){

            $("#empty_textbox1").show(); 

            $("#textbox1").focus();

            $("#empty_selectbox1").hide();

            $("#empty_textbox2").hide();

        } else if($("#textbox2").val()==""){

            $("#empty_textbox2").show(); 

            $("#textbox2").focus();

            $("#empty_selectbox1").hide();

            $("#empty_textbox1").hide(); 

        } else {

            $("#empty_selectbox1").hide();

            $("#empty_textbox1").hide(); 

            $("#empty_textbox2").hide();

            if ($('#process').val() == 1) {

                var err_cnfirm = confirm(err_confreg);

            } else {

                var err_cnfirm = confirm(err_confup);

            }

            if(err_cnfirm) {

                var selectbox1 =  $('#selectbox1').val();

                $('#selectbox1').val(selectbox1);

                var textbox1 =  $('#textbox1').val();

                $('#textbox1').val(textbox1);

                var textbox2 =  $('#textbox2').val();

                $('#textbox2').val(textbox2);

                $('#tablename').val(tablename);

                $('#flag').val(flag);

                var editid = $('#rdoid').val();

                var url = 'threeFieldaddedit';

                    $.ajax({

                    async: true,

                        type: 'GET',

                        url: url,

                        data: {"selectbox1": selectbox1,"textbox1": textbox1,"textbox2": textbox2,"mainmenu": mainmenu,"tablename": tablename,"id": editid,"flag": flag},

                        success: function(data) {

                            // alert(data);

                            if (data != "") {

                                 $('#selectbox1').val('');

                                 $('#textbox1').val('');

                                 $('#textbox2').val('');

                                 if(opr==1) {

                                 var tempdata= parseInt(data)+1;

                                    var tempselecctvalue=$("#selectbox1 option[value="+selectbox1+"]").text();

                                    var data='<tr class="h37" onclick="fnrdocheckthreefield(\''+selectbox1+'\',\''+textbox1+'\',\''+textbox2+'\',\''+data+'\')"><td class="tac" title="Select"><input type = "radio"" name="rdoedit" id="rdoedit'+data+'" class="h13 w13" onclick="fnrdocheckthreefield(\''+tempselecctvalue+'\',\''+textbox1+'\',\''+textbox2+'\',\''+data+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+data+'"></td><td class="text-center pt7" title="S.No">'+data+'</td><td class="pl7 pt7 box30per" id="datanametd2'+data+'">'+tempselecctvalue+'<input type="hidden" name="hiddenselectvalue" id="dataname1'+data+'" value="'+selectbox1+'"></td><td class="pl7 pt7" id="dataname2'+data+'">'+textbox1+'</td><td class="pl7 pt7" id="dataname3'+data+'">'+textbox2+'</td><td class="tac pt7" title="Use/Not Use"><a href="javascript:useNotuse(\''+data+'\',\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+tempdata+'" name="editid'+tempdata+'" type="hidden" value="'+data+'"></td></tr>'; 

                                    $('#swaptable1 tr:last').after(data);

                                    $("#popupsessionreg").css("display", "block");

                                    $("#popupsessionupd").css("display", "none");

                                    $("#swaptable1 tr:last").css('cursor', 'hand');



                                } else {

                                var tempselecctvalue=$("#selectbox1 option[value="+selectbox1+"]").text();

                                   // $("#datanametd2"+editid).append(tempselecctvalue);

                                   $("#datanametd2"+editid).html('');

                                   var tempdata=tempselecctvalue+'<input type="hidden" name="hiddenselectvalue" id="dataname1'+editid+'" value="'+selectbox1+'">'

                                   $("#datanametd2"+editid).html(tempdata);

                                    $("#dataname2"+editid).text(textbox1);

                                    $("#dataname3"+editid).text(textbox2); 

                                    $('#dataname1'+editid).val(selectbox1);

                                    $("#add_var").show();

                                    $("#update_var").hide();

                                    $('#process').val(1);

                                    $("#popupsessionupd").css("display", "block");

                                    $("#popupsessionreg").css("display", "none");

                                }

                                    var rowCount = $('#swaptable1 tr').length;

                                    if ($('#swaptable1 tr').hasClass('nodata')) {

                                    $('#swaptable1 tr:first').remove();

                                }



                            }

                        },

                           error: function(data) {

                        }

                    });

            }

    }

}

function useNotuse(editid,i) {

    var tablename =  $('#tablename').val();

    var editid =  $('#editid'+i).val();

    var curtFlg =  $("#usenotuselabel"+i).attr('data-type');

    var url = 'useNotuse';

        $.ajax({

        async: true,

            type: 'GET',

            url: url,

            data: {"tablename": tablename,"editid": editid,"curtFlg": curtFlg },

            success: function(data) {

                 if(curtFlg==1) {

                     $("#usenotuselabel"+i).text('Use');

                     $("#usenotuselabel"+i).css('color','blue');

                     $("#usenotuselabel"+i).attr('data-type','0');

                     $("#usenotuseanchor"+i).css('text-decoration', 'none');

                 } else {

                     $("#usenotuselabel"+i).text('Not Use');

                     $("#usenotuselabel"+i).css('color','red');

                     $("#usenotuselabel"+i).attr('data-type','1');

                     $("#usenotuseanchor"+i).css('text-decoration', 'none');

                 }

            },

               error: function(data) {

            }

        });

}

function fnsettingpopup(heading) {

    var ifile = document.getElementById('xlfile').value;



    if (ifile == "") {

        alert("Please Select a File");

        document.getElementById('xlfile').focus();

        document.getElementById('xlfile').select();

        return false;

    } else if (ifile != "") {

        var arr1 = new Array;

        arr1 = ifile.split("\\");

        var len = arr1.length;

        var doc1 = arr1[len - 1];

        var filext = doc1.substring(doc1.lastIndexOf(".") + 1);

        // Checking Extension

        if (filext == "xls" || filext == "xlsx") {

        } else {

            alert("The Uploaded File Should Be a xls or xlsx Format");

            return false;

        }

    }

    var cmsg = "Do You Want Update File";

    if (confirm(cmsg)) {

        document.getElementById("upload").disabled = true;

        document.getElementById("cancel").disabled = true;

        document.forms["uploadpopup"].submit();



        return true;

    } else {

        return false;

    }

}

function fnclosepopdig() {

    $(this).removeData('bs.modal');

    $('#modal-container .modal-content').empty();

}

function divpopupclose() {

    var confirmmsg = cancel_msg;

    if (confirm(confirmmsg)) {

        $( "body div" ).removeClass( "modalOverlay" );

        $( '#showpopup' ).empty();

        $('#showpopup').modal('toggle');

    } else {

        return false;

    }

}

function fnselecttoggleclose() {

   $("#styleSelector").removeClass('open');

}

//                          var res = $.parseJSON(data);

//                             var orderid=res.orderid;

//                             var totalid=res.totalid;

//                             var data=orderid;

//                              if (orderid != "") {

//                                   $('#textbox1').val('');

//                                   if(opr==1) {

//                                     var tempdata= parseInt(orderid)+1;

//                                     var data1='<tr class="h37" onclick="fnrdocheck(\''+textbox1+'\',\''+orderid+'\')"><td class="tac"><input type = "radio" name="rdoedit" id="rdoedit'+orderid+'" class="h13 w13" onclick="fnrdocheck(\''+textbox1+'\',\''+orderid+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+orderid+'"></td><td class="text-center pt7">'+orderid+'</td><td class="pl7 pt7" id="dataname'+data+'">'+textbox1+'</td><td class="tac pt7"><a href="javascript:useNotuse(\''+totalid+'\',\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+tempdata+'" name="editid'+tempdata+'" type="hidden" value="'+totalid+'"></td></tr>';

//                                     // var data='<tr class="h37" onclick="fnrdocheck(\''+textbox1+'\',\''+data+'\')"><td class="tac"><input type = "radio" name="rdoedit" id="rdoedit'+data+'" class="h13 w13" onclick="fnrdocheck(\''+textbox1+'\',\''+data+'\');"><input id="rdoid" name="rdoid" type="hidden" value="'+data+'"></td><td class="text-center pt7">'+data+'</td><td class="pl7 pt7" id="dataname'+data+'">'+textbox1+'</td><td class="tac pt7"><a href="javascript:useNotuse(1,\''+tempdata+'\');" class="btn-link" style="color:blue;"><label class="btn-link" id="usenotuselabel'+tempdata+'" data-type="0" style="color: blue;">Use</label></a><input id="curtFlg'+tempdata+'" name="curtFlg'+tempdata+'" type="hidden" value="1"><input id="editid'+data+'" name="editid'+data+'" type="hidden" value="'+data+'"></td></tr>';

//                                     $('#swaptable1 tr:last').after(data1);

//                                    } else {

//                                     $("#dataname"+editid).text(textbox1);

//                                     $("#add_var").show();

//                                     $("#update_var").hide();

//                                     $('#process').val(1);

//                                    }

//                              }

//                         },