/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery.noConflict();
jQuery(document).ready(function(){

    if(jQuery(".form-table > tbody > tr > th").is(':empty')){

        jQuery(".form-table > tbody > tr > th").css({
            margin:'0px',
            padding:'0px',
            width:'1px'


        })

    }



    jQuery("input.bookselector").each(function(){

        if(jQuery(this).is(':checked')){


            var parent = jQuery(this).closest('th');
            // jQuery(parent).find("span.handle").hide();
            jQuery(parent).find("input.bookselector").css('margin-left','8px');
            var siblings = jQuery(parent).siblings();
            var sibling_inputs =jQuery(siblings).find('input');
            jQuery(sibling_inputs).each(function(index, element) {
                if(jQuery(element).attr("disabled")){
                    jQuery(element).removeAttr("disabled");
                }else{
                    jQuery(element).attr("disabled","disabled");
                }
            })
        }
    })


    jQuery("input.radioselector").change(function(){
		var parent = jQuery(this).closest('th');
		var siblings = jQuery(parent).siblings();
		var sibling_inputs =jQuery(siblings).find('input');
		var sibling_selects =jQuery(siblings).find('select');
        if(jQuery(this).is(':checked')){
            jQuery(sibling_inputs).each(function(index, element) {
                if(jQuery(element).attr("disabled")){
                    jQuery(element).removeAttr("disabled");
                }else{
                    jQuery(element).attr("disabled","disabled");
                }
            });
            jQuery(sibling_selects).each(function(index, element) {
                if(jQuery(element).attr("disabled")){
                    jQuery(element).removeAttr("disabled");
                }else{
                    jQuery(element).attr("disabled","disabled");
                }
            });
        }
        jQuery("input.radioselector").not(this).parent().siblings().find('input').attr("disabled","disabled");
        jQuery("input.radioselector").not(this).parent().siblings().find('select').attr("disabled","disabled");
    })




    jQuery(".multiselector").change(function(){

       
        jQuery("input.bookselector").each(function(){


            if(jQuery(this).is(':checked')){

                jQuery(this).removeAttr("checked")
                var parent = jQuery(this).closest('th');

                jQuery(parent).find("input.bookselector").css('margin-left','8px');
                var siblings = jQuery(parent).siblings();
                var sibling_inputs =jQuery(siblings).find('input');
                jQuery(sibling_inputs).each(function(index, element) {
                    if(jQuery(element).attr("disabled")){
                        jQuery(element).removeAttr("disabled");
                    }else{
                        jQuery(element).attr("disabled","disabled");
                    }
                })


            }else{

                jQuery(this).attr("checked","checked")
                var parent = jQuery(this).closest('th');
                var siblings = jQuery(parent).siblings();
                var sibling_inputs =jQuery(siblings).find('input');
                jQuery(sibling_inputs).each(function(index, element) {
                    if(jQuery(element).attr("disabled")){
                        jQuery(element).removeAttr("disabled");
                    }else{
                        jQuery(element).attr("disabled","disabled");
                    }
                })

            }




        })
    })






    jQuery("input.bookselector").change(function(){


        var parent = jQuery(this).closest('th');

        jQuery(parent).find("input.bookselector").css('margin-left','8px');
        var siblings = jQuery(parent).siblings();
        var sibling_inputs =jQuery(siblings).find('input');
        jQuery(sibling_inputs).each(function(index, element) {
            if(jQuery(element).attr("disabled")){
                jQuery(element).removeAttr("disabled");
            }else{
                jQuery(element).attr("disabled","disabled");
            }
        });




    })

    var fixHelperModified = function(e, tr) {
        var originals = tr.children();
        var helper = tr.clone();
        helper.children().each(function(index)
        {
            jQuery(this).width(originals.eq(index).width())
        });
        return helper;
    };

    jQuery("tbody.sort_books").sortable(
    {
        start: function () {

            jQuery('span.handle').css({
                cursor : '-webkit-grabbing',
                cursor : '-moz-grabbing',
                cursor : '-grabbing'

            });
        },
        stop: function () {
            jQuery('span.handle').css({
                cursor : '-webkit-grab',
                cursor : '-moz-grab',
                cursor : '-grab'

            });
        },

        handle:'.handle',
        helper:fixHelperModified,
        placeholder: "dm_placeholder",
        'start': function (event, ui) {
            ui.placeholder.html('<th></th><td></td><td></td><td></td>');

        },
        scroll:true,
        forcePlaceholderSize: true,
        items:"tr"
    }
    );


    jQuery("tbody.sort_fields").sortable(
    {
        cursor: 'grabbing' ,
        handle:'.handle',

        helper:fixHelperModified,
        placeholder: "dm_placeholder",
        'start': function (event, ui) {
            ui.placeholder.html('<th></th><td></td><td></td><td></td>');

        },
        scroll:true,
        forcePlaceholderSize: true
    }
    );

})
