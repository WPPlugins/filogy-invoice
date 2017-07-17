/*global colorScheme */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 * Based on: twentysixteen theme
 */

//MOVED TO class-filo-customize-manager.php

( function( $ ) {

	// If one color of the color palett is changed, then the followings have to be changed (e.g. color3 is changed to red):
	// - css of color palette item selector field, e.g. the color css style is changed to red for color3 options of selectors (thus in the selectors the changed color can be choosen, and the choosed color also be changed )
	// - values of those color pickers, for which the belonging color palette item selevtor value is color3 
	
	// If a color palette item selector value is changed, then the belonging color picker value has to be changed


	//wp.customize( 'filo_doc_section[scheme]', function( value ) {
	wp.customize( 'filo_doc[fd_color_palette][filo_color_1]', function( value ) {
		value.bind( function( newval ) {

			////schme: default, dark, red, green, ...
			////colors: color array of the selected scheme "#000000", "#001122", ...			
			
			//var colors = colorScheme[scheme].colors;
			
			// Update primary color.
			
			//var color = colors[0]; //e.g. '#555555'
			//var color = '#555555';
						
			//wp.customize( 'filo_doc_section[primary]' ).set( color );
			//wp.customize( 'filo_doc[fd_color_palette][filo_color_2]' ).set( newval );
			wp.customize( 'filo_doc[fd_normal_widgets][FILO_Widget_Invbld_Seller_Address][css_header_selector][color]' ).set( newval );
			
			
			//wp.customize.control( 'filo_primary' ).container.find( '.color-picker-hex' )
			//wp.customize.control( 'filo_doc[fd_color_palette][filo_color_2]' ).container.find( '.color-picker-hex' )
			wp.customize.control( 'filo_doc[fd_normal_widgets][FILO_Widget_Invbld_Seller_Address][css_header_selector][color]' ).container.find( '.color-picker-hex' )
				.data( 'data-default-color', newval )
				.wpColorPicker( 'defaultColor', newval );
			
		});
	});
	

	//wp.customize( 'filo_doc_section[primary]', function( value ) {
	/*wp.customize( 'filo_doc[fd_color_palette][filo_color_2]', function( value ) {
		value.bind( function( newval ) {
			$('body').css('background-color', newval );
		} );
	} );*/


/*  wp.customize( 'setting_id', function( value ) {
    value.bind( function( to ) {
      $( '#custom-theme-css' ).html( to );
    } );
  } );
  wp.customize( 'custom_plugin_css', function( value ) {
    value.bind( function( to ) {
      $( '#custom-plugin-css' ).html( to );
    } );
  } );	
*/

})( jQuery );
