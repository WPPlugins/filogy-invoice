<?php
/**
 * This file is modify WC meta-boxes-order.min.js script file
 * In class-filo-admin-assets.php we deregister original WC wc-admin-order-meta-boxes script,
 * and register this file instead.
 * This is load the same script with some modifications.
 * 
 * Modifications:
 * - eliminate window.confirm( woocommerce_admin_meta_boxes.calc_line_taxes ) confirmation question
 * - eliminate window.confirm( woocommerce_admin_meta_boxes.calc_totals ) confirmation question
 */

/** WordPress Bootstrap */
//if ( strpos( dirname( __FILE__ ), 'filogy-framework-mini' ) === false ) {
//	require_once( dirname( __FILE__ ) . '/../../../../../../wp-load.php' );
//} else {
//	require_once( dirname( __FILE__ ) . '/../../../../../../../wp-load.php' );
//}
//TEST: http://yoursite.com/wp-content/plugins/filogy-framework/assets/js/admin/meta-boxes-order.js.php

//header('content-type:application/javascript');
//header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT");

// this individual page is loaded by class-filo-initial-functions.php
//TEST: http://yoursite.com/?filo_individual_page=meta_boxes_order_js

//wsl_log(null, 'meta-boxes-order.js.php 0: ' . wsl_vartotext( WC()->plugin_path() . '/assets/js/admin/meta-boxes-order.min.js' ));
//include( WC()->plugin_path() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js' );

// Check if WC Updated

//http://stackoverflow.com/questions/18487709/replace-string-from-php-include-file
function callback($buffer)
{
	// disable calc_line_taxes confirmation window
//NEM:
	$buffer = str_replace(
		"if ( window.confirm( woocommerce_admin_meta_boxes.calc_line_taxes ) ) {", 
		"if ( true ) { //Add filogy (010)", 
		$buffer
	);
	/*$buffer = str_replace( 
		"if(window.confirm(woocommerce_admin_meta_boxes.calc_line_taxes)){", 
		"if(true ){", 
		$buffer
	);*/
	
	// disable calc_totals confirmation window
	// WC3 ok
	$buffer = str_replace(
		"if ( window.confirm( woocommerce_admin_meta_boxes.calc_totals ) ) {", 
		"if ( true ) { //Add filogy (020)", 
		$buffer
	);
	/*$buffer = str_replace(
		"if(window.confirm(woocommerce_admin_meta_boxes.calc_totals)){",
		"if(true){",
		$buffer
	);*/			
	
	//Insert the following lines after the given starting point found first matched line
	//This is for impelemt that back-end save button also calculate_taxes and totals

	//wsl_str_replace_after_string( $search, $replace , $subject, $count = null, $start_string ) {
		
	// add calculate_tax_ajax_finished trigger
	// WC3 ok (needed?)
	$buffer = wsl_str_replace_after_string( 
		"stupidtable.init();", 														//$search
		"stupidtable.init();
		$( '#woocommerce-order-items' ).trigger( 'calculate_tax_ajax_finished' ); //Add filogy (030)", //$replace
		$buffer, 																	//$subject
		1,																			//$count
		"action:   'woocommerce_calc_line_taxes'" 									//$start_string - the first occurrence after this string
	);
	/*$buffer = wsl_str_replace_after_string( 
		"stupidtable.init();", 														//$search
		"stupidtable.init();$('#woocommerce-order-items').trigger('calculate_tax_ajax_finished');", //$replace
		$buffer, 																	//$subject
		1,																			//$count
		'action:"woocommerce_calc_line_taxes"'	 									//$start_string - the first occurrence after this string
	);*/

//NEM?
	//add totals_calculated trigger
	$buffer = wsl_str_replace_after_string( 
		"$( 'button.save-action' ).click();", 										//$search
		"$( this ).trigger( 'totals_calculated' ); //Add filogy (040)
		$( 'button.save-action' ).click();", 										//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"calculate_totals:" 														//$start_string - the first occurrence after this string
	);
	/*$buffer = wsl_str_replace_after_string( 
		'a("button.save-action").click()', 											//$search
		'$(this).trigger("totals_calculated");a("button.save-action").click()', 	//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		'calculate_totals:'		 													//$start_string - the first occurrence after this string
	);*/

	
	// add save_line_items_ajax_finished trigger
	// WC3 ok
	$buffer = wsl_str_replace_after_string( 
		"stupidtable.init();", 														//$search
		"stupidtable.init();
		$( '#woocommerce-order-items' ).trigger( 'save_line_items_ajax_finished' ); //Add filogy (050)",//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"action:   'woocommerce_save_order_items'" 									//$start_string - the first occurrence after this string
	);
	/*$buffer = wsl_str_replace_after_string( 
		"stupidtable.init();", 														//$search
		"stupidtable.init();$('#woocommerce-order-items').trigger('save_line_items_ajax_finished');", //$replace
		$buffer, 																	//$subject
		1, 																			//$count
		'action:"woocommerce_save_order_items"'	 									//$start_string - the first occurrence after this string
	);*/

	/*
	//After cancel, tax and total recalculation must be called, because new lines and deleted lines is not cancelled, so the items are changed even it was cancelled, thus recalculation must be done.
	//After cancellation, call save-action by "click" on save-action button
	$buffer = wsl_str_replace_after_string( 
		"return false;", 															//$search
		"$( 'button.save-action-filo' ).click(); //Add filogy (060)
		return false;",																//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"cancel: function() {" 														//$start_string - the first occurrence after this string
	);
	*/

	/*
	//After cancel, tax and total recalculation must be called, because new lines and deleted lines is not cancelled, so the items are changed even it was cancelled, thus recalculation must be done.
	//After cancellation, call save-action by "click" on save-action button
	$buffer = wsl_str_replace_after_string( 
		"return false;", 															//$search
		"$( 'button.save-action-filo' ).click(); //Add filogy (060)
		return false;",																//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"cancel: function() {" 														//$start_string - the first occurrence after this string
	);
	*/

	//WC3		
	//After cancel, recalculate is needed because delete item cannot be redu after cancel, thus an order total recalculation is needed
	$buffer = wsl_str_replace_after_string( 
		"return false;", 															//$search
		"$( this ).trigger( 'items_canceled' ) //Add filogy (060);
		return false;",																//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"cancel: function() {" 														//$start_string - the first occurrence after this string
	);
			
	// after click on delete item icon, switch to save mode
	$buffer = wsl_str_replace_after_string( 
		"if ( answer ) {", 															//$search
		"$( 'div.wc-order-add-item' ).slideDown(); //Add filogy (070)
		$( 'div.wc-order-bulk-actions' ).slideUp(); //Add filogy (080)
		if ( answer ) {",															//$replace
		$buffer, 																	//$subject
		1, 																			//$count
		"delete_item: function() {" 												//$start_string - the first occurrence after this string
	);
	
	return $buffer;
	
}

ob_start("callback");
//include the normal (not min) version, because at this moment code repalces works onnly at this version
//include( WC()->plugin_path() . '/assets/js/admin/meta-boxes-order.min.js' );
include( WC()->plugin_path() . '/assets/js/admin/meta-boxes-order.js' );
ob_end_flush();