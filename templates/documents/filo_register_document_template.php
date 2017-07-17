<?php
/**
 * Register Filo Document Template
 * 
 * @package     Filogy/Documents/Classes
 * @subpackage 	Financials
 * @author      WebshopLogic
 * @category    Class
 * 
 */

//FILO_STANDARD_TEMPLATE and FILO_DEFAULT_SKIN are set in class-filo-do-setup.php
//define( 'FILO_STANDARD_TEMPLATE', '01_filogy_standard' ); 
//define( 'FILO_DEFAULT_SKIN', '01 Default Skin - eco' );
 
function filo_register_document_template() {
	global $filo_document_templates;
	
	// Template load order is here: wc-core-functions.php
	// We use default_path / template_name

	// 00_filogy_original is DEPRECATED
	if ( get_option('filo_enable_deprecated_template') == 'yes' ) { 
		$filo_document_templates['00_filogy_original'] = array (
			'display_name' => _x( 'Filogy original template (deprecated)', 'filo_doc', 'filo_text' ),
			'template_name' => 'documents/document-standard-complex.php', 
			//'template_path' => FILO()->template_path() . '01_filogy_standard', // filogy/ - this is important to be able to overwrite it
			'template_path' => FILO()->template_path() . 'templates/00_filogy_original', // filogy/ - this is important to be able to overwrite it
			'default_path' => FILO()->plugin_path() . '/templates/00_filogy_original/',  //absolute path: /..../wp-content/plugins/filogy/templates/
			'template_panels_data' => 'documents/template_panels_data.php',
			'template_custom_settings' => 'documents/template_custom_settings.php',
		);
	}
			
	$filo_document_templates['01_filogy_standard'] = array (
		'display_name' => _x( 'Filogy Standard', 'filo_doc', 'filo_text' ),
		'template_name' => 'documents/document-standard-complex.php', 
		//'template_path' => FILO()->template_path() . '01_filogy_standard', // filogy/ - this is important to be able to overwrite it
		'template_path' => FILO()->template_path() . 'templates/01_filogy_standard', // filogy/ - this is important to be able to overwrite it
		'default_path' => FILO()->plugin_path() . '/templates/01_filogy_standard/',  //absolute path: /..../wp-content/plugins/filogy/templates/
		'template_panels_data' => 'documents/template_panels_data.php',
		'template_custom_settings' => 'documents/template_custom_settings.php',
	);
	
	$filo_document_templates['02_filogy_classic'] = array (
			'display_name' => _x('Filogy Classic', 'filo_doc', 'filo_text' ),
			'template_name' => 'documents/document-standard-complex.php', 
			//'template_path' => FILO()->template_path() . '02_filogy_classic/', 
			'template_path' => FILO()->template_path() . 'templates/02_filogy_classic/',
			'default_path' => FILO()->plugin_path() . '/templates/02_filogy_classic/',
			'template_panels_data' => 'documents/template_panels_data.php',
			'template_custom_settings' => '../01_filogy_standard/documents/template_custom_settings.php', //use standard template custom settings file instead of actual template: ../01_filogy_standard 	
	);
	
		
}
add_action( 'filo_register_document_template', 'filo_register_document_template' );