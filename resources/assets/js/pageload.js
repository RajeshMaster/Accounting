$(document).ready(function() {
    $(".pageload").click(function(){
      $(".se-pre-con").show();
    });
    $(".flgclick").click(function(){
    	alert("Under Construction");
    	return false;
    	var confirmlan = confirm("Do You Want to Change the Language?");
    	if (confirmlan) {
		    $.ajax({
		        type: 'POST',
		        url: '../app/changeLang',
		        success: function(data, textStatus, xhr) {
	        		location.reload();
		            // alert(data); // do with data e.g success message
		        },
		        error: function(xhr) {
		            alert(xhr.status);
		        }
	    	});
	    }
	});
	$('input, textarea').blur(function () {                     
		$(this).val(
			$.trim($(this).val())
		);
	});
});
$(window).bind("pageshow", function(event) {
  // Animate loader off screen
  $(".se-pre-con").fadeOut();
});
function pageload() {
  $(".se-pre-con").show();
}