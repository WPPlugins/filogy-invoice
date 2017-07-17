<?php

define( 'FILO_STANDARD_TEMPLATE', '01_filogy_standard' ); 
define( 'FILO_DEFAULT_SKIN', 'filoprotect_01 Default Skin - eco' );

if ( ! defined('ABSPATH') ) exit;

if ( ! class_exists('FILO_Do_Setup') ) :
	
/**
 * Make initial setup actions called from FILO setup page (class-filo-admin-setup-page.php)
 * 
 * @package     Filogy/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
class FILO_Do_Setup extends WC_Install {

	/**
	 * construct
	 */
	public function __construct() {

		
		add_action( 'admin_init', array( $this, 'call_settings_fuctions' ) );
				
		wsl_log(null, 'class-filo-setup.php __construct: ' . wsl_vartotext(''));		
		wsl_log(null, 'class-filo-setup.php __construct FILO_PLUGIN_FILE: ' . wsl_vartotext(FILO_PLUGIN_FILE));

		//plugin activation
		//First we just mark in an option that activation needed, then later we call the activation according to this option
		//This is necessary because WP activation is called too early, and before it some mandarory function had not finished.
		//register_activation_hook( FILO_PLUGIN_FILE, array( $this, 'filo_activation' ) );		
		//register_activation_hook( FILO_PLUGIN_FILE, array( $this, 'filo_activation_needed' ) ); //moved to main file
		add_action( 'filo_activation', array( $this, 'filo_activation' ) );
		
	}


	/**
	 * Do setup actions like set initial default settings
	 * Actions called from links of FILO setup page (class-filo-admin-setup-page.php), 
	 * the appropriate do_... parameter are set to true e.g. 'do_filo_chart_of_accounts_settings' => 'true'
	 * In this function do the action belong to the parameter.
	 */
	public function call_settings_fuctions() {
		// Update button
		if ( !empty( $_GET['do_filo_sequence_settings'] ) ) {

			$this->sequence_settings();

			delete_transient( '_wc_activation_redirect' );

			// Redirect back to filo-admin-setup page
			wp_redirect( admin_url( 'admin.php?page=filo_admin_setup_jedi' ) );
			
			exit;		
		} elseif ( !empty( $_GET['do_filo_predefined_customizer_skin_install'] ) ) {
						
			$this->install_predefined_customizer_skins();

			delete_transient( '_wc_activation_redirect' );

			// Redirect back to filo-admin-setup page
			wp_redirect( admin_url( 'admin.php?page=filo_admin_setup_jedi' ) );
			exit;
						
		} elseif ( !empty( $_GET['skip_install_woocommerce_pages'] ) ) {

			exit;
		}
	}

	/**
	 * Set default sequence_settings
	 */
	public function sequence_settings() {
		wsl_log(null, 'class-filo-setup.php sequence_settings 0: ' . wsl_vartotext(''));
		
		global $filo_post_types_financial_documents;
		
		$post_types = $filo_post_types_financial_documents;
		$post_types[] = 'filo_case'; // case also has sequence
		
		foreach ($post_types as $post_type) {

			$option = get_option( $post_type . '_sequences' );
			
			wsl_log(null, 'class-filo-setup.php sequence_settings $post_type: ' . wsl_vartotext($post_type));
			wsl_log(null, 'class-filo-setup.php sequence_settings $option: ' . wsl_vartotext($option));
			
			//if this option is not exists, then set it, otherwise stay the erlier value
			if ( $option == null or $option == '') {
				wsl_log(null, 'class-filo-setup.php sequence_settings $post_type 1: ' . wsl_vartotext($post_type));

	            /* Data example:
				[sequence_id] => SO_standard
	            [sequence_name] => SO Standard
	            [prefix] => SO
	            [first_number] => 1
	            [padding_length] => 1
	            [padding_string] => 0
	            [suffix] => 
	            [year_handling] => no
	            [separator] =>
				*/ 
	
				//get doc type code (e.g. 'SO')
				$doc_type_label_data = FILO_Financial_Document::get_doc_type_registration_value( 
					$post_type, 	//$doc_type, 
					null, 			//$doc_subtype, 
					'labels'  		//$registered_data_key ) 
				);
				
				if ( ! empty($doc_type_label_data) ) {
					
					wsl_log(null, 'class-filo-setup.php sequence_settings $doc_type_label_data: ' . wsl_vartotext($doc_type_label_data));
					
					$doc_type_code = $doc_type_label_data->code; 
		
					$sequence_rows = array();
					
				    $sequence_rows[ $doc_type_code . __('_standard', 'filo_text') ] = array(
		    			'sequence_id'      => $doc_type_code . __('_standard', 'filo_text'),
						'sequence_name'    => $doc_type_code . ' ' . __('Standard', 'filo_text'),
						'prefix'           => $doc_type_code,
						'first_number'     => 1,
						'padding_length'   => 5,
						'padding_string'   => '0',
						'suffix'           => null,
						'year_handling'    => 'no',
						'separator'        => null,
		    		);
					
	
					wsl_log(null, 'class-filo-setup.php sequence_settings $post_type _sequences: ' . wsl_vartotext($post_type . '_sequences'));
					wsl_log(null, 'class-filo-setup.php sequence_settings $sequence_rows: ' . wsl_vartotext($sequence_rows));
									
					update_option( $post_type . '_sequences', $sequence_rows ); // e.g. 'filo_sales_invoice_sequences'
					
				}

			}	
							
		}
		
	}

	
	/**
	 * Set set_document_general_defaults
	 */
	public function set_document_general_defaults() {

		wsl_log(null, 'class-filo-setup.php set_document_general_defaults 0: ' . wsl_vartotext(''));
		
		//General Document Settings		
		//$settings_document = new FILO_Settings_Documents();
		$settings_document = include( 'admin/settings/class-filo-settings-documents.php' );
		$settings_document->set_initial_default_settings();  
		
		//General Financials Settings
		$settings_financial = include( 'admin/settings/class-filo-settings-financials.php' );
		$settings_financial->set_initial_default_settings();  
		
	}

	/**
	 * Set set_document_dependent_defaults
	 */
	public function set_document_dependent_defaults() {

		wsl_log(null, 'class-filo-setup.php set_document_dependent_defaults 0: ' . wsl_vartotext(''));

		// Define documents that can be customised here
		$documenter         = FILO()->documenter();
		$document_templates = $documenter->get_documents();

		//go through on all kind of documents 
		foreach ( $document_templates as $document_template ) {
				
			wsl_log(null, 'class-filo-setup.php set_document_dependent_defaults $document_template: ' . wsl_vartotext($document_template));
			
			//Option names e.g.:
			//$document_template->id: document_filo_sa_invoice
			//option_name: filo_document_filo_sa_invoice_settings
			
			$option_name = 'woocommerce_' . $document_template->id . '_settings';
			
			//Option values e.g:
			//Array(
			//    [enabled] => Array
			//        (
			//            [title] => Enable/Disable
			//            [type] => checkbox
			//            [label] => Enable usage of this document
			//            [default] => yes
			//            [field_order] => 10
			//        )
			//    [pdf_gen_doc_format] => Array
			//        (
			//            [title] => PDF Document Format
			//            [type] => select
			//            [default] => Classic
			//    ......			
			
			
			$settings = $document_template->init_form_fields(); //e.g. FILO_Document_Purchase_Invoice->admin_options() or called it's parent class FILO_Document->admin_options() (abstract-filo-document.php)
			wsl_log(null, 'class-filo-setup.php set_document_dependent_defaults $settings: ' . wsl_vartotext($settings));

			if ( isset($settings) && is_array($settings) ) {

				//option is an array of key/value pairs			
				$option_value = array();
				foreach ( $settings as $key => $values ) { //go through on each settings field
					if ( isset($values['default']) ) { //if the field has default value defined
						
						$option_value[$key] = $values['default']; //e.g. enable => yes
						
					}
					
				}
				
				//if otions does not exist, then set default value	
				$current_option_value = get_option( $option_name, '###not_exist###');

				if ( $current_option_value == '###not_exist###' ) {

					update_option( $option_name, $option_value ); // e.g. filo_document_filo_sa_invoice_settings => array [enabled] => yes, [due_days] => 15
					
				} 
				
			}

		}

	}



	/**
	 * install_default_customizer_skin
	 */
	public function install_default_customizer_skin() {

		wsl_log(null, 'class-filo-setup.php install_default_customizer_skin 0: ' . wsl_vartotext(''));
		
		$default_filo_document_skins = apply_filters( 'filo_default_customizer_skins', array(
			'01_filogy_standard' => array(
				// This name has to be defined in FILO_DEFAULT_SKIN constant: 01 Default Skin - eco
				'01 Default Skin - eco' => '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"5px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"text_transform":"uppercase","font_weight":"bold","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","padding":"6px 0px 6px 0px","font_size":"15px"},"css_content_selector":{"padding_top":"5","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color"},"css_widget_selector":{"padding":"5px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Logo":{"css_content_selector":{"padding":"10px 10px 0 0"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Vertical":{"css_cell_selector":{"custom_css":".document_number_row, .due_date_row  {\n  font-weight: bold;\n  font-size: larger;\n}"},"css_data_table_label_cell_selector":{"padding":"0 2px 2px 0"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"collapse","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 15px;\n}"},"css_item_table_header_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","padding":"6px 3px 6px 3px","font_size":"15px","text_transform":"uppercase","text_align":"center","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color","font_weight":"bold"},"css_item_table_body_cell_selector":{"padding":"3px","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color"},"css_item_table_footer_cell_selector":{"padding":"3px"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_header_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_body_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"30px","font_weight":"bold","text_transform":"uppercase"}}},"":{"filo_doc_template_custom_settings":{"":{"pdf_gen_doc_format":"classic"},"pdf_gen_doc_format":"detailed","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Qty"},"Document-General":{"css_document_general_selector":{"font_family":"DejaVu Sans","background_color":"#ffffff","font_size":"12px","padding_top":"50px","padding_bottom":"50px","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#f4f4f4","filo_color_highlight_border_4":"","filo_color_light_primary_color":"","filo_color_light_primary_text_color":"#ffffff","filo_color_main_text_color":"#222222","filo_color_primary_color":"#000000","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
			),
		) );
		
		self::install_customizer_skins($default_filo_document_skins);

	}
	
	/**
	 * get_count_possible_skins_of_version
	 * 
	 * Setup Jedi shows to install skins if the actuallí installed protected skin's number is less than the possible skins returned ba this function.
	 */
	public static function get_count_possible_skins_of_version() {
		$possible_skins_count = 4+3;
		
		return $possible_skins_count;
	}
	
	/**
	 * install_predefined_customizer_skins
	 */
	public function install_predefined_customizer_skins() {
		
		wsl_insert_file_to_media_lib( __DIR__ . '../../assets/images/demo/demo_logo_01_mid-white.png', $image_title = 'Filogy Demo Logo 01' ); // 2/1
		wsl_insert_file_to_media_lib( __DIR__ . '../../assets/images/demo/demo_logo_05_mid-white.png', $image_title = 'Filogy Demo Logo 05' ); // 2/2
		wsl_insert_file_to_media_lib( __DIR__ . '../../assets/images/demo/demo_logo_08_mid-white.png', $image_title = 'Filogy Demo Logo 08' ); // 1/2
		wsl_insert_file_to_media_lib( __DIR__ . '../../assets/images/demo/demo_logo_09_mid-white.png', $image_title = 'Filogy Demo Logo 09' ); // 1/3
		wsl_insert_file_to_media_lib( __DIR__ . '../../assets/images/demo/demo_logo_12_mid-orange.png', $image_title = 'Filogy Demo Logo 12' ); // 1/4

		wsl_log(null, 'class-filo-setup.php install_predefined_customizer_skins 0: ' . wsl_vartotext(''));
		
		//filo_doc_opt_01_filogy_standard--abc
		
		$default_filo_document_skins = apply_filters( 'filo_default_customizer_skins', array(
			'01_filogy_standard' => array(
				// This name has to be defined in FILO_DEFAULT_SKIN constant: 01 Default Skin - eco
				// This one template is installed by activation hook
				//'01 Default Skin - eco' 				=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"5px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"text_transform":"uppercase","font_weight":"bold","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","padding":"6px 0px 6px 0px","font_size":"15px"},"css_content_selector":{"padding_top":"5","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color"},"css_widget_selector":{"padding":"5px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Logo":{"css_content_selector":{"padding":"10px 10px 0 0"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Vertical":{"css_cell_selector":{"custom_css":".document_number_row, .due_date_row  {\n  font-weight: bold;\n  font-size: larger;\n}"},"css_data_table_label_cell_selector":{"padding":"0 2px 2px 0"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"collapse","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 15px;\n}"},"css_item_table_header_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","padding":"6px 3px 6px 3px","font_size":"15px","text_transform":"uppercase","text_align":"center","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color","font_weight":"bold"},"css_item_table_body_cell_selector":{"padding":"3px","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color"},"css_item_table_footer_cell_selector":{"padding":"3px"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_header_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_body_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","border_style":"solid","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"30px","font_weight":"bold","text_transform":"uppercase"}}},"":{"filo_doc_template_custom_settings":{"":{"pdf_gen_doc_format":"classic"},"pdf_gen_doc_format":"detailed","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Qty"},"Document-General":{"css_document_general_selector":{"font_family":"DejaVu Sans","background_color":"#ffffff","font_size":"12px","padding_top":"50px","padding_bottom":"50px","filo_logo":"demo_logo_06_mid-white.png","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#f4f4f4","filo_color_highlight_border_4":"","filo_color_light_primary_color":"","filo_color_light_primary_text_color":"#ffffff","filo_color_main_text_color":"#222222","filo_color_primary_color":"#000000","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
				'02 Basic Dark Background Titles'		=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"5px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"text_transform":"uppercase","background_color":"#808080","font_weight":"normal","font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color_mycolor_ref":"filo_color_primary_color","padding":"4px 3px 4px 3px","font_size":"11px"},"css_content_selector":{"padding_top":"5","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color","padding":"8px 0 0 0"},"css_widget_selector":{"padding":"5px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Logo":{"css_content_selector":{"padding":"0 40px 0 0"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Vertical":{"css_cell_selector":{"custom_css":".document_number_row, .due_date_row  {\n  font-weight: bold;\n  font-size: larger;\n}"},"css_data_table_label_cell_selector":{"padding":"0 2px 2px 0"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"collapse","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 15px;\n}"},"css_item_table_header_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","padding":"6px 3px 6px 3px","font_size":"11px","text_transform":"uppercase","text_align":"center","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_body_cell_selector":{"padding":"3px","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_footer_cell_selector":{"padding":"3px"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_header_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#f5f5f5","font_color_mycolor_ref":"filo_color_heading_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_body_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#808080","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#808080","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"30px","font_weight":"bold","text_transform":"uppercase"}}},"":{"filo_doc_template_custom_settings":{"":{"pdf_gen_doc_format":"classic"},"pdf_gen_doc_format":"detailed","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Qty"},"Document-General":{"css_document_general_selector":{"font_family":"DejaVu Sans","background_color":"#ffffff","font_size":"12px","padding_top":"50px","padding_bottom":"50px","filo_logo":"demo_logo_08_mid-white.png","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"#fcbe03","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"#c42318","filo_color_accent_text_color":"#000000","filo_color_dark_primary_color":"#666666","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#bbbbbb","filo_color_highlight_border_4":"","filo_color_light_primary_color":"#9f9f9f","filo_color_light_primary_text_color":"#000000","filo_color_main_text_color":"#222222","filo_color_primary_color":"#808080","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
				'03 Calm Aquamarine'					=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"5px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"text_transform":"uppercase","background_color":"#02ccb4","font_weight":"normal","font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color_mycolor_ref":"filo_color_primary_color","padding":"6px 3px 6px 3px","font_size":"13px"},"css_content_selector":{"padding_top":"5","font_color":"#15424d","font_color_mycolor_ref":"filo_color_main_text_color","padding":"8px 0 0 0"},"css_widget_selector":{"padding":"5px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Logo":{"css_content_selector":{"padding":"0 20px 0 0"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Vertical":{"css_cell_selector":{"custom_css":".document_number_row, .due_date_row  {\n  font-weight: bold;\n  font-size: larger;\n}"},"css_data_table_label_cell_selector":{"padding":"0 2px 2px 0"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"collapse","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 15px;\n}"},"css_item_table_header_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","padding":"6px 3px 6px 3px","font_size":"13px","text_transform":"uppercase","text_align":"center","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_body_cell_selector":{"padding":"3px","border_style":"solid","border_width":"1px 0 1px 0","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color","font_color":"#15424d","font_color_mycolor_ref":"filo_color_main_text_color"},"css_item_table_footer_cell_selector":{"padding":"3px"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_header_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#419a1c","background_color_mycolor_ref":"filo_color_delicate_color","border_style":"solid","border_width":"1px","border_color":"#419a1c","border_color_mycolor_ref":"filo_color_delicate_color"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_body_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#02ccb4","background_color_mycolor_ref":"filo_color_primary_color","border_style":"solid","border_width":"1px","border_color":"#02ccb4","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"30px","font_weight":"bold","text_transform":"uppercase"}}},"":{"filo_doc_template_custom_settings":{"":{"pdf_gen_doc_format":"classic"},"pdf_gen_doc_format":"detailed","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Qty"},"Document-General":{"css_document_general_selector":{"font_family":"DejaVu Sans","background_color":"#ffffff","padding_top":"50px","padding_bottom":"50px","filo_logo":"demo_logo_09_mid-white.png","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"#000000","filo_color_accent_text_color":"#ffffff","filo_color_dark_primary_color":"#1d788c","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#419a1c","filo_color_highlight_border_4":"","filo_color_light_primary_color":"#bdfcf5","filo_color_light_primary_text_color":"#15424d","filo_color_main_text_color":"#15424d","filo_color_primary_color":"#02ccb4","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
				'04 Golden Wide' 						=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":[],"css_fullwidth_row_selector":{"padding_left":"0px","padding_right":"0px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"text_transform":"uppercase","font_weight":"bold","font_color":"#a27320","font_color_mycolor_ref":"filo_color_dark_primary_color","padding":"6px 0px 6px 0px","font_size":"15px"},"css_content_selector":{"padding_top":"5","font_color":"#222222","font_color_mycolor_ref":"filo_color_main_text_color"},"css_widget_selector":{"padding":"5px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Vertical":{"css_cell_selector":{"custom_css":".document_number_row, .due_date_row  {\n  font-weight: bold;\n  font-size: larger;\n}"},"css_data_table_label_cell_selector":{"padding":"0 2px 2px 0"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"collapse","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 15px;\n}"},"css_item_table_header_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","padding":"6px 3px 6px 3px","font_size":"15px","text_transform":"uppercase","text_align":"center","font_weight":"bold","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_body_cell_selector":{"padding":"3px","background_color_odd":"#fff5e5","background_color_even":"#fff1d1","background_color_odd_mycolor_ref":"filo_color_1","background_color_even_mycolor_ref":"filo_color_2"},"css_item_table_footer_cell_selector":{"padding":"3px"}},"FILO_Widget_Invbld_Line_Item_Name":{"css_item_table_header_cell_selector":{"padding":"5px 5px 5px 50px"},"css_item_table_body_cell_selector":{"padding":"5px 5px 5px 50px"},"css_item_table_footer_cell_selector":{"padding":"5px 5px 5px 50px"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"font_color_mycolor_ref":"filo_color_accent_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_header_cell_selector":{"padding":"5px 50px 5px 5px"},"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color","padding":"5px 50px 5px 5px"},"css_item_table_body_cell_selector":{"padding":"5px 50px 5px 5px"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_body_cell_selector":[],"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"font_color":"#ffffff","font_color_mycolor_ref":"filo_color_primary_text_color","background_color":"#e8a32e","background_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"30px","font_weight":"bold","text_transform":"uppercase","font_color":"#e8a32e","font_color_mycolor_ref":"filo_color_primary_color"}}},"":{"filo_doc_template_custom_settings":{"":{"pdf_gen_doc_format":"classic"},"pdf_gen_doc_format":"detailed","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Qty"},"Document-General":{"css_document_general_selector":{"font_family":"DejaVu Sans","background_color":"#ffffff","font_size":"12px","padding_top":"50px","padding_bottom":"50px","background_image":"demo_logo_12_mid-orange.png","background_repeat":"no-repeat","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"#fff5e5","filo_color_2":"#fff1d1","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"#a27320","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#f4f4f4","filo_color_highlight_border_4":"","filo_color_light_primary_color":"#fceaca","filo_color_light_primary_text_color":"#000000","filo_color_main_text_color":"#222222","filo_color_primary_color":"#e8a32e","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
			),
			'02_filogy_classic' => array(
				'01 Stylish Green Lines and Table' 		=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"},"css_row_selector":{"padding":"0 4px 0 4px"}},"Item-Table":[],"Filo_Data_Horizontal":{"css_row_selector":{"padding":"0px"}}},"fd_normal_widgets":{"All-Widgets":{"css_header_selector":{"border_style":"none none solid none","border_width":"6px","border_color_mycolor_ref":"filo_color_dark_primary_color","padding":"1px 0px","font_size":"14px","font_weight":"bold","border_color":"#6ea834"},"css_widget_selector":{"margin":"2px"},"css_content_selector":{"padding":"10px 0px 0px 0px"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Horizontal":{"css_data_table_label_cell_selector":{"border_style":"none none solid none","border_width":"6px","border_color_mycolor_ref":"filo_color_dark_primary_color","padding":"1px 0","font_size":"14px","font_weight":"bold","border_color":"#6ea834"},"css_data_table_selector":{"border_spacing":"4px"},"css_data_table_value_cell_selector":{"padding":"7px","font_color_mycolor_ref":"filo_color_primary_text_color","background_color_mycolor_ref":"filo_color_primary_color","font_color":"#000000","background_color":"#92d544"},"css_header_selector":[],"css_cell_selector":{"custom_css":".filogy-table-cell.document_number_cell, \n.filogy-table-cell.due_date_cell{\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_selector":{"border_collapse":"separate","border_spacing":"4px","custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 20px;\n}"},"css_item_table_header_cell_selector":{"border_style":"none none solid none","border_width":"6px","border_color_mycolor_ref":"filo_color_dark_primary_color","padding":"1px 0","font_size":"14px","font_weight":"bold","border_color":"#6ea834"},"css_item_table_body_cell_selector":{"padding":"10px","font_color_mycolor_ref":"filo_color_primary_text_color","background_color_mycolor_ref":"filo_color_primary_color","font_color":"#000000","background_color":"#92d544"},"css_item_table_footer_cell_selector":{"padding":"10px"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_footer_cell_selector":{"font_color_mycolor_ref":"filo_color_light_primary_text_color","background_color_mycolor_ref":"filo_color_light_primary_color","font_color":"#000000","background_color":"#ddfcbd"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_footer_cell_selector":{"border_color_mycolor_ref":"filo_color_light_primary_color","border_color":"#ddfcbd"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"40px","font_weight":"normal","text_transform":"uppercase"},"css_widget_selector":{"padding":"0 0 25px 110px"}}},"":{"filo_doc_template_custom_settings":{"pdf_gen_doc_format":"extra_lines","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Unit_Total_Net"},"Document-General":{"css_document_general_selector":{"background_color":"#ffffff","filo_document_size":"a4","filo_document_orientation":"portrait","padding_top":"50px","padding_bottom":"50px","filo_logo":"demo_logo_01_mid-white.png","font_family":"DejaVu Sans"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_8":"","filo_color_9":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"#6ea834","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#f4f4f4","filo_color_heading_background":"","filo_color_heading_color":"","filo_color_highlight_background":"","filo_color_highlight_color":"","filo_color_light_primary_color":"#ddfcbd","filo_color_light_primary_text_color":"#000000","filo_color_main_text_color":"#222222","filo_color_primary_color":"#92d544","filo_color_primary_text_color":"#000000","filo_color_secondary_text_color":"#777777"}}',
				'02 Traditional BW - eco' 				=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"0 5px"}}},"fd_normal_widgets":{"All-Widgets":{"css_widget_selector":{"margin":"15px 5px"},"css_header_selector":{"font_weight":"bold","padding":"0 0 5px 0","text_transform":"uppercase"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Horizontal":{"css_data_table_label_cell_selector":{"font_weight":"bold","padding":"0 0 5px 0","text_transform":"uppercase"},"css_cell_selector":{"custom_css":".table_value-row .filogy-table-cell.document_number_cell, \n.table_value-row .filogy-table-cell.due_date_cell{\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_header_cell_selector":{"font_weight":"bold","padding":"5px 0","border_style":"none none solid none","border_width":"2px","text_transform":"uppercase","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_body_cell_selector":{"padding":"5px 0","border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_footer_cell_selector":{"padding":"5px 0"},"css_item_table_selector":{"custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 20px;\n}"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_header_cell_selector":{"text_align":"right"}},"FILO_Widget_Invbld_Line_Tax_Labels":{"css_item_table_footer_cell_selector":{"border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_footer_cell_selector":{"border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color","border_style":"none none solid none","border_width":"1px"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_header_cell_selector":{"text_align":"right"},"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_header_cell_selector":{"text_align":"right"},"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","text_align":"left","font_weight":"bold","text_transform":"uppercase","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"40px","text_transform":"uppercase"},"css_widget_selector":{"margin":"0 0 25px 110px"},"css_cell_selector":{"custom_css":"vertical-align: bottom;"}}},"":{"filo_doc_template_custom_settings":{"pdf_gen_doc_format":"extra_lines","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Unit_Total_Net"},"Document-General":{"css_document_general_selector":{"background_color":"#ffffff","padding_top":"50px","padding_bottom":"50px","filo_logo":"demo_logo_05_mid-white.png","font_family":"DejaVu Sans","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"","filo_color_dark_primary_text_color":"#ffffff","filo_color_delicate_color":"#f4f4f4","filo_color_light_primary_color":"","filo_color_light_primary_text_color":"#ffffff","filo_color_main_text_color":"#222222","filo_color_primary_color":"#000000","filo_color_primary_text_color":"#ffffff","filo_color_secondary_text_color":"#777777"}}',
				'03 Harmonic Horizontal Lines BW - eco'	=> '{"fd_row_widgets":{"All-Normal-Rows":{"css_fullwidth_row_selector":{"padding_left":"50px","padding_right":"50px"}},"Item-Table":{"css_row_selector":{"margin":"0 5px"}},"Filo_Addresses_Row":{"css_row_selector":{"border_style":"solid none","border_width":"2px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"Filo_Data_Horizontal":{"css_row_selector":{"margin":"0 0 20px 0","border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_normal_widgets":{"All-Widgets":{"css_widget_selector":{"margin":"15px 5px"},"css_header_selector":{"font_weight":"bold","padding":"0 0 5px 0","text_transform":"uppercase"}},"FILO_Widget_Invbld_Billing_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}},"FILO_Widget_Invbld_Seller_Address":{"css_cell_selector":{"custom_css":".filo_address_name {\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_data_table_widgets":{"FILO_Widget_Invbld_Head_Data_Horizontal":{"css_data_table_label_cell_selector":{"font_weight":"bold","padding":"0 0 5px 0","text_transform":"uppercase"},"css_cell_selector":{"custom_css":".table_value-row .filogy-table-cell.document_number_cell, \n.table_value-row .filogy-table-cell.due_date_cell{\n  font-weight: bold;\n  font-size: larger;\n}"}}},"fd_item_table_widgets":{"All-Item-Table-Columns":{"css_item_table_header_cell_selector":{"font_weight":"bold","padding":"5px 0","border_style":"none none solid none","border_width":"2px","text_transform":"uppercase","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_body_cell_selector":{"padding":"5px 0","border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"},"css_item_table_footer_cell_selector":{"padding":"5px 0"},"css_item_table_selector":{"custom_css":".order_total_row .panel-grid-cell{\n  font-weight: bold;\n  font-size: 20px;\n}"}},"FILO_Widget_Invbld_Line_Qty":{"css_item_table_header_cell_selector":{"text_align":"right"}},"FILO_Widget_Invbld_Line_Total_Gross":{"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Net":{"css_item_table_header_cell_selector":{"text_align":"right"},"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Total_Tax":{"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}},"FILO_Widget_Invbld_Line_Unit_Total_Net":{"css_item_table_header_cell_selector":{"text_align":"right"},"css_item_table_footer_cell_selector":{"border_style":"none none solid none","border_width":"1px","text_align":"left","font_weight":"bold","text_transform":"uppercase","border_color":"#000000","border_color_mycolor_ref":"filo_color_primary_color"}}},"fd_doc_title_widgets":{"FILO_Widget_Invbld_Doc_Title":{"css_content_selector":{"font_size":"40px","text_transform":"uppercase"},"css_widget_selector":{"margin":"0 0 25px 75px"},"css_cell_selector":{"custom_css":"vertical-align: bottom;"}}},"":{"filo_doc_template_custom_settings":{"pdf_gen_doc_format":"extra_lines","item_table_footer_label_column":"FILO_Widget_Invbld_Line_Unit_Total_Net"},"Document-General":{"css_document_general_selector":{"background_color":"#ffffff","padding_top":"50px","padding_bottom":"50px","font_family":"DejaVu Sans","filo_document_size":"a4","filo_document_orientation":"portrait"}}},"fd_color_palette":{"filo_color_1":"","filo_color_2":"","filo_color_3":"","filo_color_4":"","filo_color_5":"","filo_color_6":"","filo_color_7":"","filo_color_accent_color":"","filo_color_accent_text_color":"","filo_color_dark_primary_color":"","filo_color_dark_primary_text_color":"","filo_color_delicate_color":"","filo_color_light_primary_color":"","filo_color_light_primary_text_color":"","filo_color_main_text_color":"","filo_color_primary_color":"#000000","filo_color_primary_text_color":"#ffffff"}}',
			),
		) );
		
		self::install_customizer_skins($default_filo_document_skins);

	}

	/**
	 * install_customizer_skins
	 */
	public function install_customizer_skins( $skin_data ) {
		
		$opt_fix_prefix = 'filo_doc_opt_';

		if ( isset($skin_data) and is_array($skin_data) ) 
		foreach ($skin_data as $template_key => $template_skins ) {
			
			if ( isset($template_skins) and is_array($template_skins) ) 
			foreach ($template_skins as $skin_opt_name => $skin_settings_json ) {
				
				$wp_option_name_to_store = rawurlencode( $opt_fix_prefix . $template_key . '--' . 'filoprotect_' . $skin_opt_name );
				
				wsl_log(null, 'class-filo-setup.php install_predefined_customizer_skins $wp_option_name_to_store: ' . wsl_vartotext($wp_option_name_to_store));
				wsl_log(null, 'class-filo-setup.php install_predefined_customizer_skins $skin_settings_json: ' . wsl_vartotext($skin_settings_json));

				
				$options = json_decode($skin_settings_json, $assoc = true); //$import_settings; //json_decode($import_settings);
				
				$options = wsl_handle_hierarchical_array_values_recursively( $options, array('FILO_Customize_Manager::change_filename_to_media_lib_url_callback') );

				wsl_log(null, 'class-filo-setup.php install_predefined_customizer_skins $options: ' . wsl_vartotext( $options ));
												
				update_option( $wp_option_name_to_store, $options );
				
			}
			
		}
		
	}	


	/**
	 * Activate FILO
	 */
	public function filo_activation() {
		
		wsl_log(null, 'class-filo-setup.php filo_activation 0: ' . wsl_vartotext(''));
		//var_dump('class-filo-setup.php filo_activation 0');
		
		$this->create_options();
		//$this->create_tables();

		// Register post types
		include_once( 'class-filo-post-types.php' );
		FILO_Post_Types::register_post_types_1();
		FILO_Post_Types::register_post_types_2();
		//WC_Post_types::register_taxonomies();

		$this->create_roles(); //parent
		
		$this->add_capabilities_to_roles();
				
		$this->set_document_general_defaults();
		
		$this->set_document_dependent_defaults();
		
		// we do not install at activation, user can start the installation in Setup Jedi
		//$this->install_predefined_customizer_skins();
		$this->install_default_customizer_skin();
		
		add_action('init', array($this, 'FILO_Do_Setup::filo_flush_rewrite_rules_on_activation') );
				
		
		//after activation is done, we clear the activation needed option
		update_option( 'filo_activation_needed', 'no' );
		
	}

	/**
	 * filo_flush_rewrite_rules_on_activation (init)
	 *
	 * Flush rewrite rules needed for set filo_rewrite_rules (e.g. it is needed for customizer to be able to get filo_generate_pdf.php)
	 * 
	 * rewrite rules are set here: filo-core-functions.php / filo_rewrite_rules()
	 *
	 */
	public static function filo_flush_rewrite_rules_on_activation() {

		wsl_log(null, 'class-filo-setup.php filo_flush_rewrite_rules_on_activation 0: ' . wsl_vartotext(''));
		
		global $wp_rewrite; 
		$wp_rewrite->flush_rules();

	}


	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	private static function create_options() {

	}
	
	/**
	 * Create Supplier Role, and call WP parent function
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			
			// Seller role (we ourselves)
			add_role( 'seller', __( 'Seller (our own data)', 'filo_text' ), array(
				'read' 						=> true,
				'edit_posts' 				=> false,
				'delete_posts' 				=> false
			) );
			

		}

		parent::create_roles();
		
	}
	
	/**
	 * add_capabilities_to_roles
	 */
	private static function add_capabilities_to_roles() {
		global $wp_roles; //this is set in create_roles() function of parent
		global $filo_post_types;
		
		$capabilities = array();
		
		$capabilities['filo_admin_setup_jedi'] = array('filo_admin_setup_jedi');
		$capabilities['filo_setup'] = array('filo_do_setup');

		wsl_log(null, 'class-filo-do-setup.php add_capabilities_to_roles $capabilities: ' . wsl_vartotext($capabilities));
		
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'shop_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}
	
	/**
	 * count_installed_predefined_customizer_skins_data
	 */
	static function count_installed_predefined_customizer_skins_data() {
			
		global $wpdb, $count_installed_predefined_customizer_skins_data;
		
		if ( isset($count_installed_predefined_customizer_skins_data) ) {
			return $count_installed_predefined_customizer_skins_data;
		}

		//count existing account records (posts)
		
		$skin_count = $wpdb->get_results("
			select count(*) as cnt 
			from $wpdb->options as options 
			where option_name like 'filo_doc_opt_%'
		");

		$count_installed_predefined_customizer_skins_data = $skin_count[0]->cnt;
		
		wsl_log(null, 'class-filo-do-setup.php $count_installed_predefined_customizer_skins_data: ' . wsl_vartotext($count_installed_predefined_customizer_skins_data));
		
		return $count_installed_predefined_customizer_skins_data;

	}		
		
	static function is_filo_settings_ok( $strict = true ) {
		
		//global $is_filo_settings_ok;
		//if ( isset($is_filo_settings_ok) ) {
		//	return $is_filo_settings_ok;
		//}

		$is_all_sequences_are_set = FILO_Financial_Document::is_all_sequences_are_set();
		
		$is_filo_settings_ok = $is_all_sequences_are_set; // We set it for none essential (mini) mode for filogy invoice, but it will be overwritten in esseintial or higher mode
				
		wsl_log(null, 'class-filo-do-setup.php is_filo_settings_ok $is_filo_settings_ok: ' . wsl_vartotext($is_filo_settings_ok));				
		
		return $is_filo_settings_ok;

	}
	
}

endif;

return new FILO_Do_Setup();
