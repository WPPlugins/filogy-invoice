<?php
/**
 * General core functions available on both the front-end and admin.
 * 
 * @package     Filogy/Functions
 * @category    Functions
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Rewrite Rules has to be applied for handling filo_generate_pdf.php file from customizer
 * if we use filo_generate_pdf.php in preview url, customizer cannot be display it in the preview windows, the site main page is displayed instead of generated "pdf"
 * We set the following link format here, to be able to applied:
 * http://yoursite.com/filo_finadoc?doc_id=26&filo_nonce=738059476e&filo_usage=doc&is_customiser=true
 * 
 * http://stackoverflow.com/questions/25310665/wordpress-how-to-create-a-rewrite-rule-for-a-file-in-a-custom-plugin
 * http://wordpress.stackexchange.com/questions/176204/add-rewrite-rule-not-working
 *
 * BUT: if pretty permalins is not enabled in Settings / Permalinks wp-admin menu, thus it is set to "plain" = 	http://yoursite.com/?p=123,
 * then cannot be used any other pretty permalink, thus our permalink.
 * That is why, we eliminate this functionality.
 * We can use this plain link format (without using permalinks): http://yoursite.com/?post_type=shop_order&p=123
 * This plain link can be used only if 'publicly_queryable' => true is set in register_type! 
 * In the 'template_include' filter we add filo_generate_pdf.php template for these post types, and it checks nonce, thus it is not really public (Access denied - You are not authorized to access this page.)
 */

/*
// Rewrite Rules 
add_action('init', 'filo_rewrite_rules');
function filo_rewrite_rules() {
	
	wsl_log(null, 'filo-core-functions.php filo_rewrite_rules 0: ' . wsl_vartotext(''));
	
	global $wp_rewrite; // Global WP_Rewrite class object
	
	//decide if pertty permalinks is enabled(1) or not
	$is_permalinks_enabled = $wp_rewrite->using_permalinks();
	
	wsl_log(null, 'filo-core-functions.php filo_rewrite_rules $is_permalinks_enabled: ' . wsl_vartotext($is_permalinks_enabled));
	
	//global $wp_rewrite; // Global WP_Rewrite class object
	//$wp_rewrite->flush_rules(); //flush_rules function is called in class-filo-do-setup.php
	
	add_rewrite_tag('%filo_finadoc_var%', '([^&]+)'); //This is register the new query var (get parameter)
    add_rewrite_rule( 'filo_finadoc/?$', 'index.php?filo_finadoc_var=true', 'top' );
}
*/

// Query Vars
// It is not needed because we use add_rewrite_tag instead
// but this function is tested and can be used
/*  
add_filter( 'query_vars', 'filo_register_query_var' );
function filo_register_query_var( $vars ) {
    $vars[] = 'filo_finadoc_var';
	//wsl_log(null, 'filo-core-functions.php filo_register_query_var $vars: ' . wsl_vartotext($vars));
    return $vars;
}
*/

// Template Include
// This is not necessary, because we eliminate this solution: //$url_with_nonce = htmlspecialchars_decode( wp_nonce_url( get_site_url() . '/?post_type=' . $sample_order->order_type . '&doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce') ) . '&filo_usage=doc' . '&is_customiser=true';
// Instead of this we use filo_individual_page mechanism for displaying customizer preview page 
/*add_filter('template_include', 'filo_finadoc_template_include', 1, 1); 
function filo_finadoc_template_include($template) {
	global $filo_post_types_financial_documents, $wp_query; //Load $wp_query object
	wsl_log(null, 'filo-core-functions.php filo_finadoc_template_include $wp_query->query_vars: ' . wsl_vartotext($wp_query->query_vars));

	//this is the previous functionality by using pretty permalinks for fina docs    
	//$page_value = $wp_query->query_vars['filo_finadoc_var']; //Check for query var "filo_finadoc_var"
	//if ($page_value && $page_value == "true") { //Verify "filo_finadoc_var" exists and value is "true".
    
    //we do not use permalinks, the plain link format is http://yoursite.com/?post_type=shop_order&p=123 (if publicly_queryable' => true in register_type)
	$post_type_by_query_var = $wp_query->query_vars['post_type']; //Check for query var "filo_finadoc_var"
	if ( $post_type_by_query_var and in_array($post_type_by_query_var, $filo_post_types_financial_documents) ) { //Verify "filo_finadoc_var" exists and value is "true".
		
		//set template file
		//return wp_nonce_url( FILOFW()->plugin_url() . '/includes/filo_generate_pdf.php' , 'filo_generate_pdf', 'filo_all_nonce') . '&doc_id=' . $post_id;
		$template = home_url() . '?filo_individual_page=filo_generate_pdf';
		
	}
    
	wsl_log(null, 'filo-core-functions.php filo_finadoc_template_include $template: ' . wsl_vartotext($template));

	return $template; 
}*/

/**
 * END Rewrite Rules 
 */
