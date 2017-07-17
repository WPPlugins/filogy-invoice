<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Head Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

remove_all_filters( 'woocommerce_email_style_inline_tags' );
remove_all_filters( 'woocommerce_email_style_inline_h1_tag' );
remove_all_filters( 'woocommerce_email_style_inline_h2_tag' );
remove_all_filters( 'woocommerce_email_style_inline_h3_tag' );
remove_all_filters( 'woocommerce_email_style_inline_a_tag' );
remove_all_filters( 'woocommerce_email_style_inline_img_tag' );

//wsl_log(null, 'document-header.php 0X: ' . wsl_vartotext(''));

// <html>  // We do not use <html>, because in case of bulk generation, DOMPDF cannot handle more <html> tag (no <html> is no problem)
// the individual page /templates/01_filogy_standard/documents/css/document_general_layout.css.php is loaded by class-filo-initial-functions.php  
?>

<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--<title><?php echo get_bloginfo( 'name' ); ?></title>-->
        <link rel='stylesheet' id='filo_document_layout'  href='<?php echo FILO()->plugin_url() . '/templates/01_filogy_standard/documents/css/document_layout.css'; ?>' type='text/css' media='all' />
        <link rel='stylesheet' id='filo_document_styles_general'  href='<?php echo home_url() . '?filo_individual_page=template01_document_general_layout_css&fileformat=' . $output_format; ?>' type='text/css' media='all' />
        
		<?php
			//print css styling according to customizer settings
			FILO_Customize_Manager::render_css();
		?>		

	</head>
    <body class="filo_document_body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	
	<?php do_action('filo_document_head'); ?>
