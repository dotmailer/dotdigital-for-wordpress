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
});
