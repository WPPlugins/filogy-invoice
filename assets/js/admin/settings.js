jQuery( function ( $ ) {
	
	$( '#filo_seller_user' ).on( 'change', function() {

		var data = {
			action:   'filo_get_formatted_seller_data',
			security: filo_settings.get_formatted_seller_data_nonce,			 
			seller_user_id: $( '#filo_seller_user' ).val(),
		};

		$.ajax({
			url:  filo_settings.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {

				$( 'p#seller_formatted_data' ).html(response);

			}
		});
		
	});



	// Refresh sample_order_id in Design Customizer button link when Sample Order or Invoice ID is changed
	// We call it on page load (to be initiated if the value is not changed), and also call at every change
	set_sample_order_id_in_customizer_button_link(); //call this at the beginning
	$( "#filo_sample_order_id" ).change(set_sample_order_id_in_customizer_button_link);	
								
	function set_sample_order_id_in_customizer_button_link() {
		
		//get the value of filo_sample_order_id field
		var filo_sample_order_id = $( "#filo_sample_order_id" ).val();
	  
		//get the href attribute of Design Customizer and Print Sample buttons											  	
		var filo_design_customizer_action_href = $("a.filo_design_customizer_action").attr("href");
		//var filo_print_sample_action_href = $("a.filo_print_sample_action").attr("href");
		
		//change filo_sample_order_id http parameter value of the Design Customitzer and Print Sample link buttons to the filo_sample_order_id field value
		//we do not use it, because filo_sample_order_id is leaved empty for reading it from otions 
		//filo_design_customizer_action_href = change_href_param_by_name(filo_design_customizer_action_href, "filo_sample_order_id", filo_sample_order_id);    //change_href_param_by_name(href, paramName, newVal);
		
		//update the href attributs to the changed parameter
		$("a.filo_design_customizer_action").attr("href", filo_design_customizer_action_href);
		//$("a.filo_print_sample_action").attr("href", filo_print_sample_action_href); //Print Sample button cannot be modified, because nonce will be invalid
		
	
	}

	// Function for change a param inside of a href by name
	// http://blog.adrianlawley.com/jquery-change-url-parameter-value/
	// see also: class-filo-customize-manager.php
	function change_href_param_by_name(href, paramName, newVal) {
		if (typeof href === 'string' || href instanceof String) {
			var tmpRegex = new RegExp("(" + paramName + "=)[[A-Za-z0-9%_\\-+]*", "ig"); //we need % for hendling umlencoded string and * (inside of +) to handle empty parameter, where there is no character after =
			return href.replace(tmpRegex, "$1"+newVal);
		}
	}
	
});
