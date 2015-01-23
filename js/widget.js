/*PG FIX*/
/*jQuery.noConflict();*/  
jQuery(document).ready(function(){

    jQuery(".Date").datepicker({
        changeMonth: true,
        changeYear: true
    });
	
	var redir = jQuery("#dotMailer_redir").val();
	if (typeof(redir) !== 'undefined') {
		window.location.href=redir;
	}
	
});
 