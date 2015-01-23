  
jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery(".books-tip").click(function(e){
        var x = jQuery(this).offset().left;
        var y = jQuery(this).offset().top;

        jQuery("#books_dialog").dialog({
            position : [x,y]
            }); 
        e.preventDefault(); 
    });
    jQuery(".fields-tip").click(function(e){
        var x = jQuery(this).offset().left;
        var y = jQuery(this).offset().top;
        jQuery("#fields_dialog").dialog({
            position : [x,y]
            }); 
        e.preventDefault(); 
    });
       
    jQuery.noConflict();
    jQuery('#tabs').tabs();
    jQuery("#apiCredentials").validate();
    jQuery("#wording").validate({
        errorElement: "em"
   
        
    });
        
    jQuery(".sort_books").sortable(
    {
        cursor: 'move' ,
        placeholder: "ui-state-highlight"
    }    
    );  
       
    jQuery(".sort_fields").sortable(
    {
        cursor: 'move',
        placeholder: "ui-state-highlight"
    }
    );
        
    jQuery("form#single_sub").submit(function(e){
        var sb =  jQuery("input.single_book:checked").length;
        var sd =   jQuery("input.single_datafield:checked").length;
        if( sb == 0 || sd == 0 ){
                  
                 
            alert("Please select at least one address book and datafield!");
                 
            e.preventDefault();
        }
               
               
    });    
           
    jQuery("form#multiple_sub").submit(function(e){
        var mb =   jQuery("input.multiple_book:checked").length;
        var md =   jQuery("input.multiple_datafield:checked").length;
        if( mb == 0 || md == 0 ){
                  
                  
            alert("Please select at least one address book and datafield!");
            e.preventDefault();
        }
               
    });    
               
           
            
              
             
               
               
    jQuery("#datafields").css("display","none");
    
    jQuery("#include").click(function(){

        // If checked
        if (this.checked)
        {
            //show the hidden div
            jQuery("#datafields").show("fast");
        }
        else
        {
            //otherwise, hide it
            jQuery("#datafields").hide("fast");
        }
    });       
            
});