jQuery(document).ready(function(){
	var i = 0;
	jQuery('.Date').each(function(){
      	jQuery(this).attr('id', 'aaaa'+i)
	    jQuery(this).datepicker({
	    	changeMonth: true,
			changeYear: true
	    });
	    i++;
	});
    
    

	var redir = jQuery("#dotMailer_redir").val();
	if (typeof(redir) !== 'undefined') {
		window.location.href=redir;
	}

});
