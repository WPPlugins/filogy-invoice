<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Settings_Documents')) :
if ( !class_exists('FILO_Settings_Page') ) require_once( 'class-filo-settings-page.php' );

/**
 * Generate Document Settings Tab on WooCommerce Settings Page
 * 
 * @package     Filogy/Admin/Settings
 * @subpackage 	Financials
 * @category    Admin/Settings
 */
class FILO_Settings_Documents extends FILO_Settings_Page {

	/**
	 * construct
	 */
	 	 
	public function __construct() {
		
		$this->id    = 'document';
		$this->label = __( 'Documents', 'filo_text' );

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
			'' => __( 'Document Options', 'filo_text' )
		);


		//Additional tabs for each financial document types
		//Define documebts that can be customised here
		$documenter          = FILO()->documenter();
		$document_templates = $documenter->get_documents();
		
		wsl_log(null, 'class-filo-settings-documents.php get_sections $documenter: ' . wsl_vartotext($documenter));
		wsl_log(null, 'class-filo-settings-documents.php get_sections $document_templateS: ' . wsl_vartotext($document_templates));
		
		foreach ( $document_templates as $document_template ) {
				
			wsl_log(null, 'class-filo-settings-documents.php $document_template: ' . wsl_vartotext($document_template));	
			
			$title = empty( $document_template->title ) ? ucfirst( $document_template->id ) : ucfirst( $document_template->title );

			$sections[ strtolower( get_class( $document_template ) ) ] = esc_html( $title );
		}

		wsl_log(null, 'class-filo-settings-documents.php get_sections $sections: ' . wsl_vartotext($sections));
		
		return apply_filters( 'filo_get_sections_' . $this->id, $sections );
	}

	/**
	 * get_field_settings
	 * Fields are the same in every section (sub-tab), that is why there is no $section parameter of this function (in contrast to other settings page)
	 *
	 * @return array
	 */
	public function get_field_settings( $enable_html_generation = true ) { //get_settings() was earlyer, but it was called by something (so get_settings name was in conflict with another function)
		global $filo_document_templates, $is_filo_settings_ok; // Every template add himself to $filo_document_templates global variable

		//in case of save settings, $filo_document_templates global variable is not set, thus we have to call filo_register_document_template to know the available $filo_document_templates in the global variable (the value will be validated based on the options before save)   
		if ( ! isset($filo_document_templates) or empty($filo_document_templates) ) {
			do_action( 'filo_register_document_template');
		}
		
		//generate an array containing template key and display name pairs. The source of it is the $filo_document_templates global variable, that is filled by the plugins that register templates
		$doc_template_list = array();		
		if ( isset($filo_document_templates) and is_array($filo_document_templates) )
		foreach ( $filo_document_templates as $filo_document_template_key => $filo_document_template_values ) {
			$doc_template_list[$filo_document_template_key] = $filo_document_template_values['display_name'];			
		}
		
		
		wsl_log(null, 'class-filo-settings-documents.php get_settings 0: ' . wsl_vartotext(''));
		wsl_log(null, 'class-filo-settings-documents.php get_settings get_option(filo_document_logo): ' . wsl_vartotext(get_option( 'filo_document_logo' )));

		//check if there is any financial doc in the system. If not, then Doc Design Customizer must not be displayed
		$finadoc_title_list = FILO_Financial_Document::get_finadoc_title_list( $doc_types = null, $orderby = null, $item_limit = 1, $is_detailed = false );
		
		$settings = array(

			array( 'type' => 'sectionend', 'id' => 'document_options' ),
			array( 'title' => __( 'Document Options', 'filo_text' ), 'type' => 'title', 'desc' => __( 'Set options of financial document generation.', 'filo_text' ), 'id' => 'document_options' ),

			array( 'type' 			=> 'html_code',
					'html_content'	=> $enable_html_generation ? $this->generate_seller_data_html() : ''
			),
			
			array(
				'title'    => __( 'Seller VAT Number', 'filo_text' ),
				'desc'     => __( 'Type Your VAT Number here, this will be displayed on financial documents.', 'filo_text' ),
				'id'       => 'filo_seller_vat_number',
				'css'      => 'min-width:400px;',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Seller Domestic VAT Number', 'filo_text' ),
				'desc'     => __( 'Type Your Domestic VAT Number here, this will be displayed on financial documents.', 'filo_text' ),
				'id'       => 'filo_seller_domestic_vat_number',
				'css'      => 'min-width:400px;',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			//Design Customizer button
			array( 
				'type' 			=> 'html_code',
				'html_content'	=> ($is_filo_settings_ok and ! empty($finadoc_title_list) ) ? ////check if there is any financial doc in the system. If not, then Doc Design Customizer must not be displayed 
					'
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
										'return' => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
										'filo_sample_order_id' => '', //it is attantionally empty, because the value is alwas read from the appropriate option (it is always refreshed), if it is empty
									),
									admin_url( 'customize.php' )
								) ),
								__( 'Doc Design Customizer', 'filo_text' )
							) . '
						</td>
					</tr>
				' : '',
			),					
			// just for invoicing
			array(
				'title'         => __( 'Enable modification of an already saved pseudo document', 'filo_text' ),
				'desc'          => __( 'Only for those documents that is created from Filogy Invoice plugin (e.g. invoice). It can be enabled if you want to allow modification of a saved document (e.g. invoice).', 'filo_text' ),
				'id'            => 'filo_enable_modification_validated_pseudo_doc',
				'default'       => '',
				'type'          => 'checkbox',
				'autoload'      => false
			),
			
		) ;
			
		$settings_depr = array();
		if ( get_option('filo_enable_deprecated_template') == 'yes' ) {
			
			$settings_depr = array(
				array( 'type' => 'sectionend', 'id' => 'gen_deprecated_styles_options' ),
				array( 
					'title' => __( 'Filogy Original template (00) styling options (only for deprecated templates!)', 'filo_text' ), 
					'type' => 'title', 
					'desc' => __( 'These settings are aplicable only for old, deprecated templates! These documents cannot be customized by the document customizer, you can change the look and feel by changing the following options.', 'filo_text' ), 
					'id' => 'gen_deprecated_styles_options' 
				),
	
				
				array( 
					'type' 			=> 'html_code',
					//'html_content'	=> $enable_html_generation ?  $this->filo_generate_logo_uploader_html( $this->id ) : ''
					'html_content'	=> $enable_html_generation ?  
						wsl_file_uploader_html( 
							'filo_document_logo', //$field_id,
							get_option( 'filo_document_logo' ), //$actual_value_of_uploaded_url, 
							$field_label = 'Upload logo',
							$field_tip = 'Upload or delete logo displayed on PDF document. Images with transparent background make document rendering extremely slow.', 
							$field_description = 'Upload or delete logo displayed on PDF document.', 
							$is_image = true
						) : '',
				),
	
	
				array(
					'title'    => __( 'Document Size', 'filo_text' ),
					'desc'     => __( 'Choose size of PDF document.', 'filo_text' ),
					'id'       => 'filo_document_size',
					'css'      => 'min-width:150px;',
					'default'  => 'a4',
					'type'     => 'select',
					'options'  => array(
						'a3'           => __( 'A3', 'filo_text' ),
						'a4'           => __( 'A4', 'filo_text' ),
						'a5'           => __( 'A5', 'filo_text' ),
						'letter'       => __( 'Letter', 'filo_text' ),
						'legal'        => __( 'Legal', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
				//https://code.google.com/p/dompdf/source/browse/branches/dompdf_0-6_test/dompdf/include/cpdf_adapter.cls.php?r=269
				
				array(
					'title'    => __( 'Orientation', 'filo_text' ),
					'desc'     => __( 'Choose orientation of PDF document.', 'filo_text' ),
					'id'       => 'filo_document_orientation',
					'css'      => 'min-width:150px;',
					'default'  => 'portrait',
					'type'     => 'select',
					'options'  => array(
						'portrait'		=> __( 'Portrait', 'filo_text' ),
						'landscape'		=> __( 'Landscape', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Template', 'filo_text' ),
					'desc'     => __( 'Choose document template (more templates can be added by using template plugins or by custom development).', 'filo_text' ),
					'id'       => 'filo_document_template',
					'css'      => 'min-width:150px;',
					'default'  => FILO_STANDARD_TEMPLATE, //'filo_standard_template',
					'type'     => 'select',
					'options'  => $doc_template_list,
					'desc_tip' =>  true,
				),
	
				/*
				//moved above and changed
				array(
					'title'    => __( 'Sample Order or Invoice ID', 'filo_text' ),
					'desc'     => __( 'You can choose which order or invoice or other document would you like to use as a sample for customizing the document style, by typing the id of that document.', 'filo_text' ),
					'id'       => 'filo_sample_order_id',
					'css'      => 'min-width:400px;',
					'default'  => '',
					'type'     => 'number',
					'desc_tip' =>  true,
				),
	
				//Design Customizer button and a javascript that updates the filo_sample_order_id in the link of the button to the above field value.
				array( 
					'type' 			=> 'html_code',
					'html_content'	=> '
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
											'return' => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
											//'filo_usage' => 'doc',
											'filo_sample_order_id' => 'NA', //it is refreshed by filogy\assets\js\admin\settings.js if filo_sample_order_id value is changed by the user
											//'filo_invoice_template_id' =>  
										),
										admin_url( 'customize.php' )
									) ),
									__( 'Design Customizer', 'filoinv_text' )
								) . '
							</td>
						</tr>
						
	
					',
				),
				*/
				
				array(
					'title'    => __( 'Body Background Colour', 'filo_text' ),
					'desc'     => __( 'Background colour of document body. Delete the color code and leave this field empty if you do not need background. Default <code>#ffffff</code>.', 'filo_text' ),
					'id'       => 'filo_document_body_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#ffffff',
					'autoload' => false
				),
	
				array(
					'title'    => __( 'Cell Background Colour', 'filo_text' ),
					'desc'     => __( 'Background colour of document cells. Delete the color code and leave this field empty if you do not need background. Default <code>#ffffff</code>.', 'filo_text' ),
					'id'       => 'filo_document_cell_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#ffffff',
					'autoload' => false
				),
	
	
				array(
					'title'    => __( 'Text Colour', 'filo_text' ),
					'desc'     => __( 'Text colour of document. Default <code>#000000</code>.', 'filo_text' ),
					'id'       => 'filo_document_text_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#000000',
					'autoload' => false
				),
				
				array(
					'title'    => __( 'Headline Background Colour', 'filo_text' ),
					'desc'     => __( 'Background colour of headlines. Delete the color code and leave this field empty if you do not need background. Default <code>#808080</code>.', 'filo_text' ),
					'id'       => 'filo_document_headline_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#808080',
					'autoload' => false
				),
				
				array(
					'title'    => __( 'Headline Text Colour Dark', 'filo_text' ),
					'desc'     => __( 'Text colour of headlines. Default <code>#000000</code>.', 'filo_text' ),
					'id'       => 'filo_document_headline_text_color_dark',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#000000',
					'autoload' => false
				),
	
				array(
					'title'    => __( 'Headline Text Colour Light', 'filo_text' ),
					'desc'     => __( 'Text colour of headlines. Default <code>#f5f5f5</code>.', 'filo_text' ),
					'id'       => 'filo_document_headline_text_color_light',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#f5f5f5',
					'autoload' => false
				),			
				array(
					'title'    => __( 'Border Colour', 'filo_text' ),
					'desc'     => __( 'Table border colour. Delete the color code and leave this field empty if you do not need background. Default <code>#808080</code>.', 'filo_text' ),
					'id'       => 'filo_document_border_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#808080',
					'autoload' => false
				),
	
				array(
					'title'    => __( 'Headline Font Weight', 'filo_text' ),
					'desc'     => __( 'Choose font weight of headlines.', 'filo_text' ),
					'id'       => 'filo_document_headline_font_weight',
					'css'      => 'min-width:150px;',
					'default'  => 'normal',
					'type'     => 'select',
					'options'  => array(
						'normal'	=> __( 'Normal', 'filo_text' ),
						'bold'		=> __( 'Bold', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Headline Font Style', 'filo_text' ),
					'desc'     => __( 'Choose font style of headlines.', 'filo_text' ),
					'id'       => 'filo_document_headline_font_style',
					'css'      => 'min-width:150px;',
					'default'  => 'normal',
					'type'     => 'select',
					'options'  => array(
						'normal'	=> __( 'Normal', 'filo_text' ),
						'italic'	=> __( 'Italic', 'filo_text' ),
						'oblique'	=> __( 'Oblique', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Headline Font Size', 'filo_text' ),
					'desc'     => __( 'Choose font site of headlines.', 'filo_text' ),
					'id'       => 'filo_document_headline_font_size',
					'css'      => 'min-width:150px;',
					'default'  => 'smaller',
					'type'     => 'select',
					'options'  => array(
						'smaller'	=> __( 'Smaller', 'filo_text' ),
						'small'		=> __( 'Small', 'filo_text' ),
						'medium'	=> __( 'Medium', 'filo_text' ),
						'larger'	=> __( 'Larger', 'filo_text' ),
						'large'		=> __( 'Large', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Headline Font Case', 'filo_text' ),
					'desc'     => __( 'Choose font case of headlines.', 'filo_text' ),
					'id'       => 'filo_document_headline_font_case',
					'css'      => 'min-width:150px;',
					'default'  => 'uppercase',
					'type'     => 'select',
					'options'  => array(
						'initial'		=> __( 'Initial', 'filo_text' ),
						'uppercase'		=> __( 'Uppercase', 'filo_text' ),
						'lowercase'		=> __( 'Lowercase', 'filo_text' ),
						'capitalize'	=> __( 'Capitalize', 'filo_text' ),
					),
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Font-Family of documents (for PDF)', 'filo_text' ),
					'desc'     => __( 'Choose font family of PDF documents. If you have problem displaying special UTF-8 characters, you can type here the built in: "DejaVu Sans" font-family name, or install additional fonts according to this description: https://github.com/dompdf/ dompdf/wiki/UnicodeHowTo', 'filo_text' ),
					'id'       => 'filo_document_font_family_pdf',
					'css'      => 'min-width:400px;',
					'default'  => 'DejaVu Sans, Verdana, Arial, sans-serif',
					'type'     => 'text',
					'desc_tip' =>  true,
				),
	
				array(
					'title'    => __( 'Font-Family of documents (for HTML)', 'filo_text' ),
					'desc'     => __( 'Choose font family of HTML documents.', 'filo_text' ),
					'id'       => 'filo_document_font_family_html',
					'css'      => 'min-width:400px;',
					'default'  => 'Verdana, Arial, sans-serif',
					'type'     => 'text',
					'desc_tip' =>  true,
				),
	
				/*
				//moved to customizer
				array(
					'title'         => __( 'Show "created by" text in document footer', 'filo_text' ),
					'desc'          => __( 'Set this checkbox to display "created by" text in document footer.', 'filo_text' ),
					'id'            => 'filo_show_created_by_text',
					'default'       => 'yes',
					'type'          => 'checkbox',
					//'checkboxgroup' => 'filo_display_options',
					'autoload'      => false
				),
				*/
	
			);
			
		}

		//closing tag has to be applied for displaying save button all the time (indepentendly on deprecated options)
		$settings_closing = array( 
			array( 'type' => 'sectionend', 'id' => 'email_template_options' ),
		);

		$settings = array_merge($settings, $settings_depr, $settings_closing);
		$settings = apply_filters('filo_document_settings', $settings);
		
		wsl_log(null, 'class-filo-settings-documents.php get_field_settings $settings: ' . wsl_vartotext($settings)); 

		return apply_filters( 'filo_get_settings_' . $this->id, $settings );
	}

	/**
	 * output
	 */
	public function output() {
		//RaPe ToDo
		global $current_section;

		// Define documents that can be customised here
		$documenter          = FILO()->documenter();
		$document_templates = $documenter->get_documents();

		if ( $current_section ) { 

			//output settings sub-tab of the different kind of documents 
			foreach ( $document_templates as $document_template ) {

				if ( strtolower( get_class( $document_template ) ) == $current_section ) {
					$document_template->admin_options(); //e.g. FILO_Document_Purchase_Invoice->admin_options() or called it's parent class FILO_Document->admin_options() (abstract-filo-document.php)
					break;
				}
			}
		} else { //if section is empty (first sub tab), display the general "Document Options" tab
				
			$settings = $this->get_field_settings();
			wsl_log(null, 'class-filo-settings-documents.php save output() $settings: ' . wsl_vartotext($settings));

			FILO_Admin_Settings::output_fields( $settings );
		}
	}


	/**
	 * save
	 */
	public function save() {
		//RaPe ToDo
		global $current_section;
		
		wsl_log(null, 'cldass-filo-settings-documents.php save $_POST: ' . wsl_vartotext($_POST));		
		wsl_log(null, 'class-filo-settings-documents.php save $current_section: ' . wsl_vartotext($current_section));
			
		if ( ! $current_section ) { //if section is empty (first sub tab), save the general "Document Options" tab

			$settings = $this->get_field_settings();
			FILO_Admin_Settings::save_fields( $settings );
			
			wsl_log(null, 'class-filo-settings-documents.php save $settings: ' . wsl_vartotext($settings));
			
			if ( isset( $_POST['filo_seller_user'] ) ) {
   		
				$seller_user = wc_clean( $_POST['filo_seller_user'] ); //+wc_clean
		    	update_option( 'filo_' . $this->id . '_seller_user', $seller_user ); // e.g. 'filo_document_sales_invoice_seller_user'
			}

			if ( isset( $_POST['filo_document_logo'] ) ) {
   		
				$filo_document_logo = wc_clean( $_POST['filo_document_logo'] ); //+wc_clean
				
				wsl_log(null, 'class-filo-settings-documents.php save $filo_document_logo: ' . wsl_vartotext($filo_document_logo));
								
		    	update_option( 'filo_document_logo', $filo_document_logo ); // e.g. 'filo_document_sales_invoice_seller_user'
		    	
			}

			
		} else { //if we are on a specific document settings sub-tab (section), let's call this document settings updater

			// Gen documenter
			$documenter          = FILO()->documenter();

			//wsl_log(null, 'class-filo-settings-documents.php save $documenter: ' . wsl_vartotext($documenter));

			if ( class_exists( $current_section ) ) {
				
				wsl_log(null, 'class-filo-settings-documents.php save class_exists( $current_section ): ');
				
				$current_section_class = new $current_section();
				do_action( 'filo_update_options_' . $this->id . '_' . $current_section_class->id );
				
				wsl_log(null, 'class-filo-settings-documents.php save filo_update_options_ ACTION NAME: ' . wsl_vartotext('filo_update_options_' . $this->id . '_' . $current_section_class->id));
								
				FILO()->documenter()->init();
				
			} else {
				
				wsl_log(null, 'class-filo-settings-documents.php save NOT class_exists( $current_section ): ');
								
				do_action( 'filo_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}

    /**
     * generate_seller_data_html function (seller_data type is defined above)
	 * generate_..._html() called by abstract-wc-settings-api.php, inside generate_settings_html(..)
     */
    public function generate_seller_data_html() {

    	ob_start();
			?>
			<tr valign="top">		
				<th scope="row" class="titledesc">
					<?php _e( 'Default Seller data', 'filo_text' ); ?>
				</th>
				<td class="forminp">		    	
						
						
					
					<p class="form-field form-field-wide wc-customer-user">
						
						<?php
						$user_string = '';
						$user_id     = '';
						$formatted_billing_address = '';

						$seller_user_options = array();
				
						$seller_user_value = get_option('filo_' . $this->id . '_seller_user');
						
						if ( $seller_user_value ) {
							$user_id = $seller_user_value;
							$user = get_user_by( 'id', $seller_user_value );
							$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
						}
						
						// Check if WC Updated:
						?>
						<select class="wc-customer-search" 
								id="filo_seller_user" 
								name="filo_seller_user" 
								data-allow_clear="true" 
								data-placeholder="<?php esc_attr_e( 'Select the user containing your own shop data', 'filo_text' ); ?>" >
							<option value="<?php echo esc_attr( $user_id ); ?>" 
									selected="selected">
									<?php echo htmlspecialchars( $user_string ); ?>
							</option>
						</select>
						
					</p>
					
					<p>
						<?php				
							echo '<a href="' . admin_url( 'users.php' ) . '" target="_blank">' .  __('Create new seller user (if you cannot chose an existing)', 'filo_text') . ' →</a>';											
						?>
					</p>
				
					<div class="order_data_column">

						<?php
							
							// Display values
							echo '<div class="address">';

							if ( ! empty($user) ) {

								// Formatted Addresses
								$address = array(
									'first_name'    => get_user_meta( $user->ID, 'billing_first_name', true ), //$this->billing_first_name,
									'last_name'     => get_user_meta( $user->ID, 'billing_last_name', true ), //$this->billing_last_name,
									'company'       => get_user_meta( $user->ID, 'billing_company', true ), //$this->billing_company,
									'address_1'     => get_user_meta( $user->ID, 'billing_address_1', true ), //$this->billing_address_1,
									'address_2'     => get_user_meta( $user->ID, 'billing_address_2', true ), //$this->billing_address_2,
									'city'          => get_user_meta( $user->ID, 'billing_city', true ), //$this->billing_city,
									'state'         => get_user_meta( $user->ID, 'billing_state', true ), //$this->billing_state,
									'postcode'      => get_user_meta( $user->ID, 'billing_postcode', true ), //$this->billing_postcode,
									'country'       => get_user_meta( $user->ID, 'billing_country', true ), //$this->billing_country
								);
					
								$formatted_billing_address = WC()->countries->get_formatted_address( $address );
								
							}

							echo '<p id="seller_formatted_data"><strong>' . __( 'Seller', 'filo_text' ) . ': </strong>' . wp_kses( $formatted_billing_address, array( 'br' => array() ) ) . '</p>';

						//END: Content of seller data
						?>
					</div>
	
	
					
				</td>
			</tr>
		<?php
		// Ajax Chosen Customer Selectors JS
			
		return ob_get_clean();
	}


	/**
	 * generate_logo_uploader_html
	 * 
	 * @based_on	WP_Add_Logo_To_Admin->add_logo_options_page v1.6
	 */
	function filo_generate_logo_uploader_html( $logo_id ) {
		ob_start();
		    		
	    //$filo_logo = get_option('filo_' . $this->id . '_logo');
	    $filo_logo = get_option('filo_' . $logo_id . '_logo');
		
		//wsl_log(null, 'class-filo-settings-documents.php $filo_logo: ' . wsl_vartotext($filo_logo));
		//wsl_log(null, 'class-filo-settings-documents.php $filo_logo_PARAM_NAME: ' . wsl_vartotext('filo_' . $logo_id . '_logo'));
			
	    $image = 
	    $display = ( $filo_logo ) ? '' : 'style="display: none;"';
		?>
	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="filo_document_logo"><?php _e( 'Upload Logo', 'filofw_text' ); ?></label>
				<img class="help_tip" data-tip="Upload or delete logo displayed on PDF document. Images with transparent background make document rendering extremely slow." src="<?php echo(plugins_url()); ?>/woocommerce/assets/images/help.png" height="16" width="16" />	
			</th>
			<td class="forminp">
				<div class="order_data_column">
	
	                <input type="text" id="filo_document_logo" name="filo_document_logo" value="<?php echo esc_url( $filo_logo ); ?>" readonly />
	                <div id="filo_document_logo_image">
	                	<?php echo ( $filo_logo ) ? '<img src="' . esc_url( $filo_logo ) . '" alt="" />' : ''; ?>
	                </div>
	                <div id="filo_upload_buttons">
	                	<a href="#" class="filo_upload_image button"><?php _e( 'Upload', 'filofw_text' ); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="filo_delete_image button" <?php echo $display; ?>><?php _e( 'Delete', 'filofw_text' ); ?></a>
	                    <span class="description"><?php _e( 'Upload or delete logo displayed on PDF document.', 'filofw_text' ); ?></span>
	                </div>
		
				</div>
			</td>
		</tr>
	
		<?php
	
		return ob_get_clean();
	
	}

}
endif;

return new FILO_Settings_Documents();
