$( function() {
	
	$.support.placeholder = false;
	test = document.createElement('input');
	if('placeholder' in test) $.support.placeholder = true;
	
    switchValue.init();
    divers.init();   
    forms.init();
    /*TEST FORCE SUBMIT TYPE (SearchButton) IE7*/
    search.init();
});
var search = function(){
	function _init(){
		$("#campaignlistfeatures").submit(function() {
			if ( navigator.appVersion.indexOf("MSIE 7.") != -1 ) {
				var url = window.location.pathname;
				var order = "";
				
				if ( url.indexOf( "/(order_field)/" ) != -1 ) {
					index = url.indexOf("/(order_field)/");
					order = url.substring(index);
					url = url.substring(0, index);
		        }
				if ( url.indexOf( "/search/" ) != -1 ) {
		            index = url.indexOf("/search/");
		            url = url.substring(0, index);
		        }
				if ( url.indexOf( "/(offset)/" ) != -1 ) {
					index = url.indexOf("/(offset)/");
					url = url.substring(0, index);
		        }
				url += "/search/" + $('#ezmailing_search_text').val() + order;
				$(location).attr('href',url);
				return false;
			}
		});
	}
	return {init:_init}
}();
//* =SWITCHVALUE */
var switchValue = function(){
	function _init(){
		$(".switchValue").bind('focus',function(){
			val = $(this).val();
			defaultVal = $(this).attr('alt');
			if(val==defaultVal){
				$(this).val("");
			}
		}).bind('blur',function(){
			if ( $(this).val() == "" ) {
				$(this).val(defaultVal);
			}
		});
	}
	return {init:_init}
}();
//* =DIVERS */
var divers = function(){
	function init() {
		/*left menu*/
		$('#ezmailing_left_menu > ul > li > a').click(function(){
			if($('ul',$(this).parent()).size() > 0) {
				$('ul',$(this).parent()).toggle();
				return false;
			}
		});
		$( 'ul#preview_list_mode a#iframe_preview' ).click( function() {
            var ulparent = $( this ).parents( 'ul:first' );
            var url = ulparent.data( 'url' );
            ulparent.after( '<iframe width="98%" height="800px" src="' + url + '" /></iframe>' );
            return false;
        } );

        $( ".ezmailing-controls-tabs ul a" ).click( function() {
            $( this ).parents( ".ezmailing-controls-tabs" ).find( "ul li" ).removeClass( 'selected' );
            $( this ).parents( ".ezmailing-controls-tabs" ).find( ".tabs-content > div" ).removeClass( 'selected' ).addClass( 'hide' );
            var id = $( this ).parent( 'li' ).attr( 'id' );
            $( ".ezmailing-controls-tabs ul li[id='" + id + "']" ).addClass( 'selected' );
            $( ".ezmailing-controls-tabs .tabs-content > div[id='" + id + "-content']" ).addClass( 'selected' ).removeClass( 'hide' );
            return false;
        } );
        $('#grabber a').click(function(){
        	$('#ezmailing_header_details').slideToggle();
        	return false;
        });
        
        /*if($('#ezmailing_mailing_lists ul li').size() > 0){
        	var height = 0;
        	var liste = $('#ezmailing_mailing_lists ul li');
        	for(var i=0; i < liste.size(); i++) {
        		if(height < $(liste[i]).height()) {
        			height = $(liste[i]).height();
        		}
        	}
        	$('#ezmailing_mailing_lists ul li').height(height);
        }
        
        if($('#user_registrations #ezmailing_record_list ul li').size() > 0){
        	var height = 0;
        	var liste = $('#user_registrations #ezmailing_record_list ul li');
        	for(var i=0; i < liste.size(); i++) {
        		if(height < $(liste[i]).height()) {
        			height = $(liste[i]).height();
        		}
        	}
        	$('#user_registrations #ezmailing_record_list ul li').height(height);
        	$('#user_registrations #ezmailing_record_list').jScrollPane();
        }
        */

        if($('#user_registrations #ezmailing_record_list ul li').size() > 0){
        	$('#user_registrations #ezmailing_record_list').jScrollPane();
        }
        if($('#mailing_lists_registrations form #ezmailing_record_list ul li').size() > 0) {
        	$('#mailing_lists_registrations form #ezmailing_record_list').jScrollPane();
        }
        if($('#form_import_users #ezmailing_record_list ul li').size() > 0) {
        	$('#form_import_users #ezmailing_record_list').jScrollPane();
        }
		if($('#dashboard_mailing_lists #dashboard_mailing_lists_registrations ul li').size() > 0) {
        	$('#dashboard_mailing_lists #dashboard_mailing_lists_registrations').jScrollPane();
        }
        //File input transform
        $('#file_transform #filewrapper, #file_transform #file_name').css('display','inline-block');
        $('#file_transform #input_file_import').hide();
        $('#filewrapper').click(function(){
			$(this).prev().trigger('click');
			return false;
		});
		$('#file_transform input[type="file"]').bind("change", function() {
            $('#file_name').text(document.getElementById("input_file_import").files[0].name);
    	});
    
	}
	return {init:init}
}();
//* =FORMS */
var forms = function(){
	function init() {
		function checkboxControl(){
			var selected = $('input[name="itemsActionCheckbox[]"]:checked').size();
        	if(selected > 0){
        		$('#remove_selected').addClass('on').find('button').removeAttr('disabled');
        	}
        	else {
        		$('#remove_selected').removeClass('on').find('button').attr('disabled','disabled');
        	}
		}
		checkboxControl();
        /*Enhanced Form*/
		$('form.jqTransform').jqTransform();
		
		if (!$.support.placeholder) {
			if ( $('#SendTestEmail').val() == "" ) {
				$('#SendTestEmail').val( 'email@email.net' );
			}
			$('#SendTestEmail').blur(function() {
				if ( $(this).val() == "" ) {
					$(this).val( 'email@email.net' );
				}
			}).focus(function() {
				if ( $(this).val() == "email@email.net" ) {
					$(this).val( '' );
				}
			});
		}
        
        $('#ezmailing_record_list ul li .ezmailing_open_actions a').click(function(){            
            $(this).hide();
            $(this).parents('li.ezmailing_list_item').css('margin-bottom','80px').find('.ezmailing_actions_buttons').slideDown();
            return false;
        });
         $('#ezmailing_record_list ul li .ezmailing_close_actions a').click(function(){
             $(this).parents('.ezmailing_actions_buttons').slideUp('fast');
             $(this).parents('li.ezmailing_list_item').css('margin-bottom','0').find('.ezmailing_open_actions a').show(100);
           return false;
        });
        
         $('.ezmailing_list_item').each(function() {
             $(this).find('.ezmailing_actions_buttons').css('top',$(this).outerHeight()+'px');
        });

        //Tooltip
        var tooltip = $('.ezmailing_tooltip');
        var tooltip_element;
        for(var i = 0 ; i < tooltip.size() ; i++){
        	tooltip_element = $(tooltip[i])[0];
        	$.data(tooltip_element, "default_position", {
        		top:   $(tooltip[i]).css('top'),
        		right: $(tooltip[i]).css('right'),
        		left:  $(tooltip[i]).css('left')
        	});
        }

        $('#ezmailing_header .ezmailing_header_update_tooltip a, #ezmailing_record_list ul li p.ezmailing_sending_date a, #mailing_lists_registrations ul li.ezmailing_list_item .mailing_lists_registration_date a, .ezmailing_registrations_container #ezmailing_record_list ul li .ezmailing_mailing_list p.registration_date a, #mailing_lists_registrations #ezmailing_record_list ul li .ezmailing_mailing_list p.registration_date a, #dashboard_campaigns .dashboard_record_list ul li .ezmailing_mailing_list p.registration_date a, #dashboard_mailing_lists .dashboard_record_list ul li .ezmailing_mailing_list p:first-child a').hover(
            function () {
            	if($(this).parents('.jspContainer').size() > 0){
            		var x = $(this).offset().left;
                    var y = $(this).offset().top-$(window).scrollTop();
                    var tooltip_element = $(this).next('.ezmailing_tooltip');
                    
                    var top = parseInt($.data(tooltip_element[0],'default_position').top);
                    var right = parseInt($.data(tooltip_element[0],'default_position').right);
                    var left = parseInt($.data(tooltip_element[0],'default_position').left);
                    
                    tooltip_element.css({
                    	display: 'block',
                        position : 'fixed',
                        top : y+top+'px'                        
                    });
                    
                    if(isNaN(right)){
                    	tooltip_element.css('left',x+left+'px');
                    }
                    else {
                    	tooltip_element.css('right',x+right+'px');
                    }
            	}
            	else{
                	$(this).next('.ezmailing_tooltip').css('display','block');
            	}
            }, 
            function () {
            	if($(this).parents('.jspContainer').size() > 0){
            		var tooltip_element = $(this).next('.ezmailing_tooltip');
            		tooltip_element.css({
            			display: 'none',
                        position : 'absolute',
                        top : 'auto',
                        left: 'auto'
                    });
            	}
            	else{
                	$(this).next('.ezmailing_tooltip').hide();
            	}
            }
        ).click(function(){
        	return false;
        });
        $('#dashboard_mailing_lists .dashboard_record_list ul li .ezmailing_mailing_list p:odd').hover(
            function () {
            	if($(this).parents('.jspContainer').size() > 0){
            		var x = $(this).offset().left;
                    var y = $(this).offset().top-$(window).scrollTop();
                    var tooltip_element = $(this).find('.ezmailing_tooltip');
                    
                    var top = parseInt($.data(tooltip_element[0],'default_position').top);
                    var right = parseInt($.data(tooltip_element[0],'default_position').right);
                    var left = parseInt($.data(tooltip_element[0],'default_position').left);
                    
                    tooltip_element.css({
                    	display: 'block',
                        position : 'fixed',
                        top : y+top+'px'                        
                    });
                    
                    if(isNaN(right)){
                    	tooltip_element.css('left',x+left+'px');
                    }
                    else {
                    	tooltip_element.css('right',x+right+'px');
                    }                    
                    
            	}
            	else {
            		$(this).find('.ezmailing_tooltip').css('display','block');
            	}
                
            }, 
            function () {
            	if($(this).parents('.jspContainer').size() > 0){
            		var tooltip_element = $(this).find('.ezmailing_tooltip');
            		tooltip_element.css({
            			display: 'none',
                        position : 'absolute',
                        top : 'auto',
                        left: 'auto'
                    });                    
            	}
            	else {
            		$(this).find('.ezmailing_tooltip').hide();
            	}                
            }
        ).click(function(){
        	return false;
        });;
        
        if($('select[name="node_id"]').attr('disabled') == 'disabled'){
        	$('select[name="node_id"]').parents('td').find('.jqTransformSelectWrapper').addClass('disabled');
        }
        if($('select[name="recurrency_period"]').attr('disabled') == 'disabled'){
        	$('select[name="recurrency_period"]').parents('.block').find('.jqTransformSelectWrapper').addClass('disabled');
        }
        $('input#content_type_1').click(function(){
        	$('textarea[name="manual_content"]').attr('disabled','disabled');        	
        	$('input[name="recurrency"]').removeAttr('disabled');
        	$('select[name="recurrency_period"]').removeAttr('disabled').parents('.jqTransformSelectWrapper').removeClass('disabled');
        	$('select[name="node_id"]').removeAttr('disabled').parents('.jqTransformSelectWrapper').removeClass('disabled');
        });
        $('input#content_type_2').click(function(){
        	$('textarea[name="manual_content"]').removeAttr('disabled');
        	$('input[name="recurrency"]').attr('disabled','disabled');
        	$('select[name="recurrency_period"]').attr('disabled','disabled').parents('.jqTransformSelectWrapper').addClass('disabled');
        	$('select[name="node_id"]').attr('disabled','disabled').parents('.jqTransformSelectWrapper').addClass('disabled');
        });
        $('.ezmailing_checkbox_button input[name="itemsActionCheckbox[]"]').change(function(){
        	checkboxControl();
        });
        
	}
	return {init:init}
}();



$(document).ready(function() {
   $( "#ezmailing_sort div.jqTransformSelectWrapper ul li a").click(function() {
        var offset      = false;
        var order_field = $('#order_field option:selected').val();
        var order_dir   = $('#order_field option:selected').attr('dir');
        var url         = $('#campaignlistform').attr("action");
        
        if (order_field == '#') {
            alert($('#order_field').attr('info'));
            return false;
        }
        
        if (order_dir == '#') {
            alert($('#order_dir').attr('info'));
            return false;
        }
            
        if ( url == undefined ) {
            url = window.location.pathname;
        }

        if ( url.indexOf( "/(offset)/" ) != -1 ) {
            offset = url.slice(url.indexOf("/(offset)/") + 10);
            if ( offset.indexOf( "/" ) != -1 ) {
                offset = offset.slice(0,offset.indexOf("/"))
        }
        }
        
        if ( url.indexOf( "/(" ) != -1 ) {
            url = url.slice(0,url.indexOf("/("));
        }
        
        url += '/(order_field)/' + order_field + '/(order_dir)/' + order_dir;
        
        if ( offset ) {
            url += '/(offset)/' + offset;
        }
        
        $(location).attr('href',url);
        return false;
    });
});