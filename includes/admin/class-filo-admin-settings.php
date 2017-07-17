<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Settings') ) :

/**
 * Filogy Admin Settings
 * Add tabs to WooCommerce Admin Settings Page
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
class FILO_Admin_Settings extends WC_Admin_Settings{
	/**
	 * Override original get_wc_settings_pages function, to expand it
	 */
	public static function get_wc_settings_pages($settings) {
		$settings[] = include( 'settings/class-filo-settings-financials.php' );
		$settings[] = include( 'settings/class-filo-settings-documents.php' );
		$settings[] = include( FILO()->plugin_path() . '/templates/documents/class-filo-settings-documents-style.php' );
		return $settings;
	}

	/**
	 * Override original output_fields function, to expand it
	 */
	public static function output_fields( $options, $readonly = false ) {
		
		//wsl_log(null, 'output_fields $readonly: ' . wsl_vartotext($readonly));
				
		if ($readonly) {
			
			//wsl_log(null, 'output_fields $options: ' . wsl_vartotext($options));

			//add "readonly" html custom attribut to each html field 
			if (isset( $options ) && is_array( $options ) )	{
				foreach ($options as $key => $option ) {
					if ( $options[$key]['type'] == 'select' )
						$options[$key]['custom_attributes']['disabled'] = ''; //select fields has "disabled" attribute, other fields has readonly 
					else 
						$options[$key]['custom_attributes']['readonly'] = '';
					
					$options[$key]['type'] = str_replace(array ('email', 'number', 'color', 'multiselect', 'single_select_page', 
																'single_select_country', 'multi_select_countries'), 'text', $options[$key]['type']);
					$options[$key]['class'] = str_replace('date-picker-field', '', $options[$key]['class']);
				}
			}
			
		}
		
		$options = apply_filters('filo_form_output_fields', $options, $readonly );
		parent::output_fields( $options );
	}


	/**
	 * output_html_code
	 */
	static function output_html_code($value) {
			
		if ( ! isset( $value['html_content'] ) ) {
			$value['html_content'] = '';
		}
		
		//wsl_log(null, 'class-filo-admin-settings.php $value: ' . wsl_vartotext($value));
		echo $value['html_content'];
				
	}

	/**
	 * output_date_picker
	 */
	function output_date_picker($value) {
			
		if ( ! isset( $value['html_content'] ) ) {
			$value['html_content'] = '';
		}
		
		echo '<p class="form-field form-field-wide"><label for="' . $value['id'] . '">' . $value['title'] . '</label>';
		echo '<input type="text" class="date-picker-field" name="' . $value['id'] . '" id="' . $value['id'] . '" maxlength="10" value="' . $value['default'] . '" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />';
		echo '</p>';
		
	}

	/**
	 * Settings output_before
	 */
	public static function output_before() {
		global $current_section, $current_tab;

		//wp_enqueue_script( 'filo_settings', FILO()->plugin_url() . '/assets/js/admin/settings.js', array( 'jquery', 'iris', 'chosen' ), WC()->version, true );

	}

}

return new FILO_Admin_Settings();

endif;
