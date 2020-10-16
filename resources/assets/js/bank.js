$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('#swaptable tr').click(function(event) {
		if (event.target.type !== 'radio') {
			if (event.target.nodeName != "SPAN") {
				$(':radio', this).trigger('click');
			}
		}
	});
	$('.addeditprocess').click(function () {
		$("#frmbankaddedit").validate({
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
				nation: {required: true},
				txt_name: {required: true},
				txt_kananame: {required: true},
				txt_accnumber: {required: true ,minlength:6,number: true},
				type: {required: true},
				txt_bankname: {required: true},
				txt_nickname: {required: true},
				txt_branchname: {required: true},
				txt_branchnumber: {required: true,number: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editid').val() == "") {
					var confirmprocess = confirm(err_confreg);
				} else {
					var confirmprocess = confirm(err_confup);
				}
				if(confirmprocess) {
					pageload();
					//form.submit();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Atleast Enter 6 Numbers";
		}
		$.validator.messages.number = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter Numbers Only";
		}
	});
});
function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    
function filter(filterval) {
	//pageload();
	$('#page').val('');
	$('#plimit').val('');
	$("#filterval").val(filterval);
	$("#bankindex").submit();
}
function getbankview(id,datetime) {
	// pageload();
	var mainmenu=$('#bankindex #mainmenu').val();
	$('#id').val(id);
	$('#bankindex').attr('action', 'Singleview?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankindex").submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#bankindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#bankindex").submit();
}
function goback() {
	$("#bankindex").submit();
}
function bankreg(datetime) {
	pageload();
	var mainmenu=$('#bankindex #mainmenu').val();
	$('#bankindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankindex").submit();
}
function getNamesbyajax(id) {
	$("#location").val(id);
	$("#bnkpopup").removeAttr("disabled"); 
	$('#bnkpopup').css( "background-color", "hsl(120, 39%, 54%)" );
	$("#txt_bankname").val("");
	$("#txt_branchname").val("");
	$("#txt_branchnumber").val("");
	if(id==1){
		$("#exampleName").hide();
		$("#kananame").hide();
		$("#name").show();
		$('#txt_kananame').removeClass('ime_mode_active');
		$('#txt_kananame').addClass('ime_mode_disable');
		$("#jpacc").hide(); 
		$("#indacc").show();
	}else{
		$('#txt_kananame').removeClass('ime_mode_disable');
		$('#txt_kananame').addClass('ime_mode_active');
		$("#radioval").val(id);
		$("#kananame").show();
		$("#name").hide();
		$("#exampleName").show();
		$("#jpacc").show(); 
		$("#indacc").hide();
	}
	$.ajax({
		type: 'GET',
		dataType: "JSON",
		url: 'branch_ajax',
		data: {"locid": id,"mainmenu": mainmenu},
		success: function(resp) {
			$("#type option").remove();
			$.each( resp, function( key, value ) {
				$('#type').append( '<option value="'+key+'">'+value+'</option>' );
			})
		},
		error: function(data) {
			alert(data.status);
			$("#regbutton").attr("data-dismiss","modal");
		}
	});
}
function getMain(id,loc,mainFlg){
	if(confirm("Do You Want to Change the Status?")) {
		$("#sid").val(id);
		$("#loc").val(loc);
		$("#mainFlg").val(mainFlg);
		$("#bankindex").submit();
	}
}
function popupenable(mainmenu){
	popupopenclose(1);
	var loc = $("#location").val();
	if(loc==1){
		$('#banknamepopup').load('../Bank/banknamepopup?loc=1&mainmenu='+mainmenu);
	}
	else{
		$('#banknamepopup').load('../Bank/banknamepopup?loc=2&mainmenu='+mainmenu);
	}
	$("#banknamepopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#banknamepopup').modal('show');
}
function divpopupclose() {
	popupopenclose(0);
	if (confirm(cancel_msg)) {
		//$( "body div" ).removeClass( "modalOverlay" );
		$( '#banknamepopup' ).empty();
		$('#banknamepopup').modal('toggle');
	} else {
		return false;
	}
}

function addeditvalidationa(nation,mainmenu) {
	//alert(nation);
	if(nation==1) {
		if($("#bnkname").val()==""){
			$("#empty_bnkname").show(); 
			$("#bnkname").focus();
		}else if($("#bnkname").val()!=""){
			$("#empty_bnkname").hide(); 
			var err_cnfirm="Do You Want to Add The Details?";
			if(confirm(err_cnfirm)) {
				document.getElementById('txt_branchname').value= "";
				document.getElementById('txt_branchnumber').value= "";
				var bankname =  $('#bnkname').val();
				$('#bankname').val(bankname);
				// var loc =  nation;
				// alert(loc);
				var url = 'bankbranchRegister';
				$.ajax({
					async: true,
					type: 'POST',
					url: url,
					data: {"bnkname": bankname,"loc": nation,"mainmenu": mainmenu},
					success: function(resp) {
						$("#bankuid").val(resp);
						$("#branchid").val(resp);
						document.getElementById('txt_bankname').value= bankname;
						resetErrors();
						$('#banknamepopup').modal('toggle');
						$("#brchpopup").removeAttr("disabled"); 
						$('#brchpopup').css( "background-color", "hsl(120, 39%, 54%)" );
					},
					error: function(data) {
						// alert(data.status);
						$("#regbutton").attr("data-dismiss","modal");
					}
				});
			}
		}
	}else if(nation==2){
		if($("#bnkname").val()=="" && $("#romaji").val()==""){
			$("#bnkname").focus();
			$("#empty_bnkname").show(); 
			$("#empty_romname").show(); 
		}else if($("#bnkname").val()!="" && $("#romaji").val()=="") {
			$("#romaji").focus();
			$("#empty_bnkname").hide(); 
			$("#empty_romname").show(); 
		}else if($("#bnkname").val()=="" && $("#romaji").val()!="") {
			$("#bnkname").focus();
			$("#empty_bnkname").show(); 
			$("#empty_romname").hide(); 
		}else if($("#bnkname").val()!="" && $("#romaji").val()!="") {
			$("#empty_romname").hide(); 
			$("#empty_bnkname").hide(); 
			var err_cnfirm="Do You Want to Add The Details?";
			if(confirm(err_cnfirm)) {
				document.getElementById('txt_branchname').value= "";
				document.getElementById('txt_branchnumber').value= "";
				var bnkname =  $('#bnkname').val();
				$('#bankname').val(bnkname);
				var romaji =  $('#romaji').val();
				// var loc =  $('#nation').val();
				var url = 'bankbranchRegister';
				$.ajax({
					type: 'POST',
					url: url,
					data: {"bnkname": bnkname,"loc": nation,"romaji": romaji,"mainmenu": mainmenu},
					success: function(resp) {
						resetErrors();
						$("#bankuid").val(resp);
						$('#banknamepopup').modal('toggle');
						$("#brchpopup").removeAttr("disabled"); 
						$('#brchpopup').css( "background-color", "hsl(120, 39%, 54%)" );
						document.getElementById('txt_bankname').value= bnkname;  
					},
					error: function(data) {
						$("#regbutton").attr("data-dismiss","modal");
					}
				});
			}
		}
	}
}
function branchvalidation(mainmenu) {
	if($("#branchs").val()=="" && $("#bno").val()==""){
		document.getElementById('branchs').focus();
		$("#empty_branchname").show(); 
		$("#empty_bno").show(); 
	}else if($("#branchs").val()!="" && $("#bno").val()=="") {
		document.getElementById('bno').focus();
		$("#empty_branchname").hide(); 
		$("#empty_bno").show(); 
	}else if($("#branchs").val()=="" && $("#bno").val()!="") {
		document.getElementById('branchs').focus();
		$("#empty_branchname").show(); 
		$("#empty_bno").hide(); 
	}else if($("#branchs").val()!="" && $("#bno").val()!="") {
		$("#empty_branchname").hide(); 
		$("#empty_bno").hide(); 
		var err_cnfirm="Do You Want to Add The Details?";
		if(confirm(err_cnfirm)) {
			var branchid =  $('#branchid').val();
			var branch =  $('#branchs').val();
			var bankuid =  $('#bankuid').val();
			$('#branchname').val(branch);
			var bno =  $('#bno').val();
			$('#branchno').val(bno);
			var url = 'branchRegister';
			$.ajax({
				type: 'POST',
				url: url,
				data: {"bankid": branchid,"bankuid": bankuid,"branchs": branch,"bno": bno,"mainmenu": mainmenu},
				success: function(resp) {
					$('#branchuid').val(resp)
					$('#branchnamepopup').modal('toggle');
					document.getElementById('txt_branchname').value=branch;
					document.getElementById('txt_branchnumber').value=bno;
				},
				error: function(data) {
					//alert(data.status);
					$("#regbutton").attr("data-dismiss","modal");
				}
			});
		}
	}
}
function branchpopupenable(mainmenu) {
	popupopenclose(1);
	var bid = $("#branchid").val();
	var loc = $("#location").val();
	var bankuid = $("#bankuid").val();
	if(loc==1){
		$('#branchnamepopup').load('../Bank/branchnamepopup?loc=1&id='+bid+'&mainmenu='+mainmenu+'&bankuid='+bankuid);
	}
	else{
		$('#branchnamepopup').load('../Bank/branchnamepopup?loc=2&id='+bid+'&mainmenu='+mainmenu+'&bankuid='+bankuid);
	}
	$("#branchnamepopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#branchnamepopup').modal('show');
}
function goindexpage(mainmenu,datetime) {
	pageload();
	$('#frmbankview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmbankview").submit();
}
function goindex(mainmenu,datetime) {
	pageload();
	$('#frmbankaddedit').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmbankaddedit").submit();
}

function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}
function edit(mainmenu,id,datetime) {
	pageload();
	$('#id').val(id);
	$('#editid').val(id);
	$('#flg').val("1");
	$('#frmbankview').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmbankview").submit();
}
function gotoindexpage(viewflg,mainmenu,datetime) {
	if (cancel_check == false) {
		if (!confirm(cancel_msg)) {
			return false;
		}
	}
	if (viewflg == "1") {
		pageload();
		$('#frmbankaddeditcancel').attr('action', 'Singleview?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmbankaddeditcancel").submit();
	} else {
		pageload();
		$('#frmbankaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmbankaddeditcancel").submit();
	}
}