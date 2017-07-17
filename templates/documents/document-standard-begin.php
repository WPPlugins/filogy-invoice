<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Complex Layout Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @author      WebshopLogic
 * @category    DocumentTemplates
 */

$document_settings = get_option('woocommerce_document_' . $order->get_doc_type() . '_settings'); //e.g. 'filo_document_sales_invoice_settings
$doc_customizer_root_settings = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true );  //use it e.g. $doc_customizer_settings['']['filo_doc_template_custom_settings']['pdf_gen_doc_format']

global $filo_document_templates;

$actual_template_key = wc_clean (get_option( 'filo_document_template' ));

//if no template key option is set then use filo_standard_template
if ( $actual_template_key == '' )  
	$actual_template_key = FILO_STANDARD_TEMPLATE; //set default

$standard_template_key = '01_filogy_standard';

//var_dump($doc_customizer_root_settings['']['filo_doc_template_custom_settings']);

if ( isset($doc_customizer_root_settings['']['filo_doc_template_custom_settings']) ) {
	$doc_customizer_template_settings = $doc_customizer_root_settings['']['filo_doc_template_custom_settings'];
} 

$currency = array( 'currency' => $order->get_currency() ); 

//create documenter and then document for handling replacement of find / replace {} tags defined in trigger() functions of documents.
//first create classname because document can be find about this
//document means documenter documents (not financial documents)
$doctype_obj = get_post_type_object( $order->get_doc_type() );
$doctype_classname = $doctype_obj->class_name; //e.g: FILO_FinaDoc_Sa_Invoice
$documenter = FILO()->documenter();
$document = $documenter->get_document( $doctype_classname );
// e.g. in notes we can use: $my_replaced_text = $document->format_string('some text {order_date}');

$doc_financial_data = $order->get_doc_financial_data( $type = '', $calculate_footer_lines = false, $pseudo_doc_type ); //default parameters, plus pseudo_doc_type if exists
wsl_log(null, 'document-standard-complex.php $doc_financial_data: ' . wsl_vartotext($doc_financial_data));

//put name parts of addresses into div, class = filo_address_name
add_filter('woocommerce_localisation_address_formats', array(FILOFW()->countries, 'filo_localisation_address_formats_namediv') ,1); //priority 0 is important, to be the first this procedure in the filter, becouse it overwrites the formats, and the following registered functions can modify it. (If it would run later, then it would overwrite others!)    

//wp_head(); //QQQ21
do_action('wp_head_filo'); //QQQ21
do_action('filo_document_header', $document_heading = null, $output_format); //use our own action instead of wp_head(), because we do not need to display other wp elements like menu.

do_action( 'filo_document_before_order_table', $order, $sent_to_admin = null, $plain_text = null );
if ( FILO_TYPE == 'filo_invoice_type' ) {
	$orderstatus = $order->get_doc_post_status(); //$orderstatus = $order->post_status;
}

//$draft_text = ( $order->get_doc_status() == 'draft' or $order->get_doc_status() == '') ? _x('Draft', 'filo_doc', 'filo_text' ) . ' ' : '';
$draft_text = ( $orderstatus == 'draft' or $orderstatus == '') ? _x('Draft', 'filo_doc', 'filo_text' ) . ' ' : '';

//wsl_log(null, 'document-standard-complex.php $order->get_doc_status(): ' . wsl_vartotext($order->get_doc_status()));
//wsl_log(null, 'document-standard-complex.php $draft_text: ' . wsl_vartotext($draft_text));
