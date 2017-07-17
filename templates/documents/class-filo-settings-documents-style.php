<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Settings_Documents_Style')) :
if ( !class_exists('FILO_Settings_Page') ) require_once( FILO()->plugin_path() . '/includes/admin/settings/class-filo-settings-page.php' );

/**
 * Generate Document Settings Tab on WooCommerce Settings Page
 * 
 * @package     Filogy/Admin/Settings
 * @subpackage 	Financials
 * @author      WebshopLogic
 * @category    Admin/Settings
 */
class FILO_Settings_Documents_Style extends FILO_Settings_Page {

	/**
	 * construct
	 */
	 	 
	public function __construct() {
		
		$this->id    = 'doc_style';
		$this->label = __( 'Doc Style', 'filo_text' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * get_sections
	 */
	public function get_sections() {
		
		//Set the sub-tabs inside of Accounting settings tab

		//First sub-tab
		$sections = array(
			'' => __( 'Document Style Options', 'filo_text' )
		);


		//Additional tabs for each financial document types
		//Define documebts that can be customised here
		$documenter          = FILO()->documenter();
		$document_templates = $documenter->get_documents();
		
	}

	/**
	 * get_field_settings
	 * Fields are the same in every section (sub-tab), that is why there is no $section parameter of this function (in contrast to other settings page)
	 *
	 * @return array
	 */
	/*
	public function get_field_settings( $enable_html_generation = true ) { //get_settings() was earlyer, but it was called by something (so get_settings maybe a kind a "reversed function name")
		// It is a display only tab, there is not any field
		
		$settings = apply_filters('filo_document_settings', array(

			array( 'type' => 'sectionend', 'id' => 'doc_styles_options' ),
			array( 'title' => __( 'Document styles options', 'filo_text' ), 'type' => 'title', 'desc' => __( '', 'filo_text' ), 'id' => 'doc_styles_options' ),
			
			array(
				'title'    => __( 'My Fiels', 'filo_text' ),
				'desc'     => __( '', 'filo_text' ),
				'id'       => 'filo_my_field',
				'type'     => 'color',
			),
			array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

		) );

		return apply_filters( 'filo_get_settings_' . $this->id, $settings );
	}*/

	/**
	 * output
	 */
	public function output() {
		global $current_section;
		
		//we do not handle sections on this settings tab (thus $current_section is always empty), if we had more sections, than we can handle it in this if statement
		if ( ! $current_section ) {
			
			$options = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true );
			
			unset($options['fd_export_settings']); 
			
			echo '<h2>' .  __( 'Document Design Customizer', 'filo_text' ) . '</h2>';
			echo '<p>' .  __( 'You can customizer your financial document design by using Design Customizer. Click on the button below to start is.', 'filo_text' ) . '</p>';
			
			echo('
				<tr valign="top">
					<th scope="row" class="titledesc">
						<!--no label-->
					</th>
					<td class="forminp">' .
						sprintf(
							' <a class="filo_design_customizer_action page-title-action" href="%1$s" target="_blank">%2$s</a>',
							esc_url( add_query_arg(
								array(
									array( 'autofocus' => array( 'control' => 'color1' ) ),
									'filo_usage' => 'doc', //apply '&filo_usage=doc' url parameter
									'return' => urlencode( wp_unslash( wc_clean( $_SERVER['REQUEST_URI'] ) ) ), //+wc_clean
									'filo_sample_order_id' => '', //it is attantionally empty, because the value is alwas read from the appropriate option (it is always refreshed), if it is empty
								),
								admin_url( 'customize.php' )
							) ),
							__( 'Design Customizer', 'filo_text' )
						) . '
					</td>
				</tr>
			');
			
			echo '<h2>' .  __( 'Actual Skin Settings', 'filo_text' ) . '</h2>';
			echo '<p>' .  __( 'You can see an overview of applied properties and values of your actual document skin. This can be changed in Document Design Customizer.', 'filo_text' ) . '</p>';
			echo '<div class="filo_skin_otpions_overview">';
			echo(wsl_vartotext( $options, $convert_to_html_format = true, $removable_techn_keywords = array('Array', 'css_', '_selector') ));
			echo '</div>';

			//$settings = $this->get_field_settings();
			//wsl_log(null, 'class-filo-settings-documents.php save output() $settings: ' . wsl_vartotext($settings));
			//FILO_Admin_Settings::output_fields( $settings );
			
		}
	}

	/**
	 * save
	 */
	public function save() {
		// It is a display only tab, there is not any saveable field
		/*
		global $current_section;
		
		wsl_log(null, 'cldass-filo-settings-documents.php save $_POST: ' . wsl_vartotext($_POST));		
		wsl_log(null, 'class-filo-settings-documents.php save $current_section: ' . wsl_vartotext($current_section));
			
		if ( ! $current_section ) { //if section is empty (first sub tab), save the general "Document Options" tab

			$settings = $this->get_field_settings();
			FILO_Admin_Settings::save_fields( $settings );
			
		} else { //if we are on a specific document settings sub-tab (section), let's call this document settings updater

		}
		*/
	}



}
endif;

return new FILO_Settings_Documents_Style();
