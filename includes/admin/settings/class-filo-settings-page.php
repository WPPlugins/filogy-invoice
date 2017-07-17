<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Settings_Page') ) :
if ( !class_exists('WC_Settings_Page') ) require_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );

/**
 * FILO_Settings_Page
 * Specialize WooCommerce Settings Page, by adding a function that setup default initial settings defined in the specific filed settings fuctions (get_field_settings() functions)
 * 
 * @package     Filogy/Admin/Settings
 * @subpackage 	Framework
 * @category    Admin/Settings
 */
 
class FILO_Settings_Page extends WC_Settings_Page {

	/**
	 * Set Initial Default settings for each settings fiels defined in specific get_field_settings() functions, if default value is defined for the field there
	 * This is used for standard settings, like single fields of general document settings, the type specific document settnings, general financial settings (it has not exist yet)
	 * This is NOT used for list table settings, additional fields of none FILO settings tab (e.g. Product / Inventory settings WC settings page)  
	 */
	public function set_initial_default_settings() {

		wsl_log(null, 'class-filo-settings-page.php set_initial_default_settings 0:' . wsl_vartotext(''));
		
		//get field settings with disable html generation because a lot of code would be called during generation
		$settings = $this->get_field_settings( false ); //$enable_html_generation = false 
				
		wsl_log(null, 'class-filo-settings-documents.php save $settings: ' . wsl_vartotext($settings));

		if ( isset($settings) && is_array($settings) )
		foreach ( $settings as $setting ) { //go through on each settings field
			if ( is_array($setting) && isset($setting['id']) && isset($setting['default']) ) { //if the field has default value defined
				
				$option_name = $setting['id'];
				$option_default_value = $setting['default'];
			
				//if otions does not exist, then set default value	
				$current_option_value = get_option( $option_name, '###not_exist###');
				if ( $current_option_value == '###not_exist###' ) {
						
					update_option( $option_name, $option_default_value ); // e.g. 'filo_document_orientation, portrait'
					
				}
				
			}
			
		}
		
	}

}
endif;
