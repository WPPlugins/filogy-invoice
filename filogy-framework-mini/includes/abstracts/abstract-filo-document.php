<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Abstract FILO Document Class
 *
 * This can be extended by the different businness documents
 *
 * @package     Filogy/Abstracts
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Abstract Class
 * @based_on	abstract-wc-email.php -> class-wc-email.php files in WooCommerce plugin by WooThemes
 * 
 */
abstract class FILO_Document extends WC_Email {

	
	/** @public string $pseudo_doc_type */
	// e.g. if shop_order document has to be handled as a pseudo document, then it is set in case of shop_order */
	public $pseudo_doc_type = null;
	
	/**
	 * Constructor
	 */
	public function FILO_Document() { //__construct
		
		//wsl_log(null, 'abstract-filo-document.php $this: ' . wsl_vartotext($this));
		
		// moved from class-filo-document-sales-invoice.php
				
		// SEQUENCES fields array init
		$this->sequence_details = get_option( str_replace('document_', '', $this->id) . '_sequences', // e.g. 'filo_sales_invoice_sequences' from 'document_filo_sa_quotation' 
			array(
				array( //defaults:
					'sequence_id'		=> '',
					'sequence_name'		=> '',				
					'prefix'   			=> '',
					'first_number'		=> '',
					'padding_length'   	=> '',
					'padding_string'   	=> '',
					'suffix' 			=> '',
					'year_handling'   	=> '',
					'separator'			=> '',					
				)
			)
		);
		
		//wsl_log(null, 'abstract-filo-document.php __construct $this->sequence_details: ' . wsl_vartotext($this->sequence_details));
		//wsl_log(null, 'abstract-filo-document.php __construct str_replace(document_, , $this->id) . _sequences: ' . wsl_vartotext(str_replace('document_', '', $this->id) . '_sequences'));

		// Actions
		add_action( 'filo_update_options_document_' . $this->id, array( $this, 'save_sequence_details' )); // e.g: filo_update_options_document_document_sales_invoice, called by: class-filo-settings-documents.php / if ( class_exists( $current_section ) )

		// Init settings
		$this->init_form_fields();
		$this->init_settings();

		// Save settings hook
		add_action( 'filo_update_options_document_' . $this->id, array( $this, 'process_admin_options' ) );

		// Default template base if not declared in child constructor
		if ( is_null( $this->template_base ) ) {
			$this->template_base = FILO()->plugin_path() . '/templates/'; //RaPe MODIFIED DOC
		}

		// Settings
		$this->output_format  = $this->get_option( 'output_format' );
		$this->enabled     = $this->get_option( 'enabled' );
 
		// Find/replace
		$this->find['blogname']      = '{blogname}';
		$this->find['site-title']    = '{site_title}';

		$this->replace['blogname']   = $this->get_blogname();
		$this->replace['site-title'] = $this->get_blogname();

		// For default inline styles
		//add_filter( 'woocommerce_email_style_inline_tags',    array( $this, 'style_inline_tags' ) );
		//add_filter( 'woocommerce_email_style_inline_h1_tag',  array( $this, 'style_inline_h1_tag' ) );
		//add_filter( 'woocommerce_email_style_inline_h2_tag',  array( $this, 'style_inline_h2_tag' ) );
		//add_filter( 'woocommerce_email_style_inline_h3_tag',  array( $this, 'style_inline_h3_tag' ) );
		//add_filter( 'woocommerce_email_style_inline_a_tag',   array( $this, 'style_inline_a_tag' ) );
		//add_filter( 'woocommerce_email_style_inline_img_tag', array( $this, 'style_inline_img_tag' ) );

        //parent::__construct();  
		
	}

	/**
	 * get_output_format
	 *
	 * @return string
	 */
	public function get_output_format() {
		 
		wsl_log(null, 'abstract-filo-document.php get_output_format $this->output_format: ' . wsl_vartotext( $this->output_format ));		 
		return empty($this->output_format) ? 'pdf' : $this->output_format;
	}

	/**
	 * get_content_type function.
	 * 
	 * @todo to be verify
	 *
	 * @return string
	 * 
	 */
	public function get_content_type() {

		switch ( $this->get_output_format() ) {
			case "pdf" :	
				return 'application/pdf';
			case "html" :
				return 'text/html';
			default :
				return 'application/pdf';
				
		}
	}

	/**
	 * Proxy to parent's get_option and attempt to localize the result using gettext.
	 *
	 * @param string $key
	 * @param mixed  $empty_value
	 * @return string
	 */
	public function get_option( $key, $empty_value = null ) {
			
		$value = parent::get_option( $key, $empty_value );

		//In case of customization of a document pdf output format must not be used, just html. We override the original settings here.
		if ( $key == 'output_format' and isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc' ) { //filo_usage is set if we customize it. In this case we change the output format to html
			$value = 'html';  
		}
		
		return apply_filters( 'filo_document_get_option', $value, $this, $value, $key, $empty_value );
	}

	/**
	 * get_content function.
	 *
	 * @return string
	 */
	public function get_content() {

		wsl_log(null, 'abstract-filo-document.php get_content FILO()->template_path(): ' . wsl_vartotext(FILO()->template_path()));
		wsl_log(null, 'abstract-filo-document.php get_content FILO()->plugin_path() . /templates/: ' . wsl_vartotext(FILO()->plugin_path() . '/templates/'));
		wsl_log(null, 'abstract-filo-document.php get_content $this->get_output_format(): ' . wsl_vartotext( $this->get_output_format() ));

		// remove all previously enqueued css (e.g. style.css) before generate the page content
		add_action( 'wp_enqueue_scripts', array($this, 'dequeue_all_css_remove_all_actions'), 9999 );

		if ( $this->get_output_format() == 'pdf' ) {
			$document_content = $this->style_inline( $this->get_content_pdf() );
		} elseif ( $this->get_output_format() == 'html' ) {
			$document_content = $this->style_inline( $this->get_content_html() );
		}
		
		//wsl_log(null, 'abstract-filo-document.php get_content $document_content: ' . wsl_vartotext($document_content)); //LARGE
		
		//return wordwrap( $document_content, 70 ); //wordrap make the pdf content wrong, it cannot be rendered by dompdf
		return $document_content;
	}

	/**
	 * dequeue_all_css
	 * 
	 * Remove all previously registered css, and remove all registered filters and actions, not to modify our document style
	 */
	static function dequeue_all_css_remove_all_actions() {
		global $wp_styles;
		wsl_log(null, 'abstract-filo-document.php dequeue_css $wp_styles: ' . wsl_vartotext( $wp_styles ));
		
		foreach ( $wp_styles->registered as $css_reg_key => $value) {
			wp_dequeue_style( $css_reg_key );
		}
		
		global $wp_filter;
		//wsl_log(null, 'abstract-filo-document.php dequeue_css $wp_filter: ' . wsl_vartotext( $wp_filter ));

		//Remove all actions and filters, except, the given ones (except e.g. wp_enqueue_scripts)
		//foreach ( $wp_filter as $filter_tag => $value) {
		//	wsl_log(null, 'abstract-filo-document.php dequeue_css $filter_tag: ' . wsl_vartotext( $filter_tag ));
		//	if ( ! in_array( $filter_tag, array('wp_enqueue_scripts') ) ) {
		//		remove_all_actions( $filter_tag );
		//	}
		//}
		
	}
	
	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	function get_content_html() {
		global $filo_document_templates;

		ob_start();

		$template_key = wc_clean (get_option( 'filo_document_template' ));
		
		wsl_log(null, 'abstract-filo-document.php get_content_html $template_key: ' . wsl_vartotext($template_key));
		
		//if no templat key option is set or the actual template value array is not exists (e.g. deactivate the plugin of which template is set), then use filo_standard_template
		if ( $template_key == '' or !is_array($filo_document_templates[$template_key]) )  
			$template_key = FILO_STANDARD_TEMPLATE;  //set default //'filo_standard_template'

		wsl_log(null, 'abstract-filo-document.php get_content_html $filo_document_templates[$template_key][default_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['default_path']));
		wsl_log(null, 'abstract-filo-document.php get_content_html $this->template_html: ' . wsl_vartotext($this->template_html));
		
		wsl_log(null, 'abstract-filo-document.php get_content_html $filo_document_templates[$template_key][template_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['template_path']));
		wsl_log(null, 'abstract-filo-document.php get_content_html $filo_document_templates[$template_key][default_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['default_path']));
		
		wc_get_template( 
			$this->template_html, 
			array(
				'order' 		 => $this->object, //pass $order variable to templates
				'output_format' => 'html',
				'filo_document_object' => $this,
				'pseudo_doc_type' => $this->pseudo_doc_type,
			),
			$filo_document_templates[$template_key]['template_path'], //FILO()->template_path(), //'', //$template_path 
			$filo_document_templates[$template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
 
		return ob_get_clean();
	}

	/**
	 * get_content_pdf function.
	 *
	 * @return string
	 */
	function get_content_pdf() {

		global $filo_document_templates;

		ob_start();

		$template_key = wc_clean (get_option( 'filo_document_template' ));
		//if no templat key option is set or the actual template value array is not exists (e.g. deactivate the plugin of which template is set), then use filo_standard_template
		if ( $template_key == '' or !is_array($filo_document_templates[$template_key]) )  
			$template_key = FILO_STANDARD_TEMPLATE; //set default //'filo_standard_template'

		wsl_log(null, 'abstract-filo-document.php get_content_pdf $filo_document_templates[$template_key][default_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['default_path']));
		wsl_log(null, 'abstract-filo-document.php get_content_pdf $this->template_html: ' . wsl_vartotext($this->template_html));
		
		wsl_log(null, 'abstract-filo-document.php get_content_pdf $filo_document_templates[$template_key][template_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['template_path']));
		wsl_log(null, 'abstract-filo-document.php get_content_pdf $filo_document_templates[$template_key][default_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['default_path']));
		
		
			
		wc_get_template( 
			$this->template_html, 
			array(
				'order' 		 => $this->object,
				'output_format' => 'pdf',
				'filo_document_object' => $this,
				'pseudo_doc_type' => $this->pseudo_doc_type,
			),
			$filo_document_templates[$template_key]['template_path'], //FILO()->template_path(), //'', //$template_path 
			$filo_document_templates[$template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
 
		return ob_get_clean();


		/*		
		ob_start();

		wc_get_template( 
			$this->template_html, 
			array(
				'order' 		 => $this->object,
				'output_format' => 'pdf',
			),
			FILO()->template_path(), //'', //$template_path
			FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC if not from WC own plugin path 
		);
 
		return ob_get_clean();
		*/
	}


    /**
     * Initialise Settings Form Fields
     *
     * @return array
     */
    function init_form_fields() {
    	global $filo_pseudo_types_financial_documents;
		
		wsl_log(null, 'abstract-filo-document.php init_form_fields filter: ' . wsl_vartotext('filo_settings_' . $this->id . '_data_fields'));
		//wsl_log(null, 'abstract-filo-document.php init_form_fields $this: ' . wsl_vartotext($this)); 
		
		// set if it is pseudo doc type (if the doc type is in $filo_pseudo_types_financial_documents global, and we are in INVOICE plugin (free or premium) than it is pseudo)
		$doc_type = $this->id; // e.g. document_filo_sa_deliv_note
		$doc_type = str_replace('document_', '', $doc_type); //remove 'document_' prefix
		$is_pseudo_doc_type = false;
		if ( in_array($doc_type, $filo_pseudo_types_financial_documents) and FILO_TYPE == 'filo_invoice_type' ) {
			$is_pseudo_doc_type = true;
		} 
		
		//wsl_log(null, 'abstract-filo-document.php init_form_fields $this->is_pseudo_doc_type: ' . wsl_vartotext($this->is_pseudo_doc_type));

		//in free version comment for pseudo docs is not available
		$if_readonly_comment_custom_attributes = array();
		$title_postfix = null;
		if ( $is_pseudo_doc_type and FILO_IS_FREE ) {  
			$if_readonly_comment_custom_attributes['readonly'] = '';
			$title_postfix = ' [premium]';
		}
		
    	$form_fields = apply_filters('filo_settings_' . $this->id . '_data_fields', array( //e.g: filo_settings_document_sales_invoice_data_fields

			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'filofw_text' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable usage of this document', 'filofw_text' ),
				'default' 		=> 'yes',
				'field_order'	=> 10,
			),

			//moved to template custom settings of doc customizer  (template_custom_settings.php)
			/*'pdf_gen_doc_format' => array(
				'title'         => __( 'PDF Document Format', 'filofw_text' ),
				'type'          => 'select',
				'desc_tip'   => 
					__( 'Choose format of invoice items: ', 'filofw_text') . '</br>' .
					'- ' . __( 'Classic: Simple item line columns, shipping fees and other fees are in invoice summary lines.', 'filofw_text') . '</br>' .
					'- ' . __( 'Extra Lines: Simple item line columns, shipping fee and other fees placed as invoice line item.', 'filofw_text') . '</br>' .
					'- ' . __( 'Detailed: More item line columns, contain VAT Amounts and Gross Totals for every line.', 'filofw_text') . '</br>',
				'default'       => 'classic',
				'class'         => 'pdf_gen_doc_format',
				'options'       => array(
					'classic'           => __( 'Classic', 'filofw_text' ),
					'extra_lines'       => __( 'Extra Lines', 'filofw_text' ),
					'detailed'          => __( 'Detailed', 'filofw_text' ),
				),
				'field_order'	=> 20,
			),

			'item_table_footer_label_column' => array(
				'title'         => __( 'PDF Document Items Table Footer Label Column', 'filofw_text' ),
				'type'          => 'select',
				'desc_tip'   => 
					__( 'Choose that column, in which the labels (e.g. Subtotal, Total) of footer lines should be displayed.', 'filofw_text'),
				'default'       => 'FILO_Widget_Invbld_Line_Item_Name',
				'class'         => 'item_table_footer_label_column',
				'options'       => array(
					'FILO_Widget_Invbld_Line_Item_Name'      => __( 'Description (default)', 'filofw_text' ),
					'FILO_Widget_Invbld_Line_Qty'            => __( 'Qty', 'filofw_text' ),
					'FILO_Widget_Invbld_Line_Unit_Total_Net' => __( 'Unit Price', 'filofw_text' ),
				),
				'field_order'	=> 25,
			),

			'enable_tax_detail_lines' => array(
				'title' 		=> __( 'Display Tax detail lines', 'filofw_text' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'In case of "Detailed" format display Tax summary lines in a separate table.', 'filofw_text' ),
				'default' 		=> 'yes',
				'field_order'	=> 30,
			),
			*/

			'default_comment' => array(
				'title' 		=> __( 'Default Comment', 'filofw_text' ) . $title_postfix,
				'type'        => 'textarea',
				'desc_tip' 	=> sprintf( __( 'Default comment of this type of documents.', 'filofw_text' ) ),
				'default'     => '',
				'placeholder' => __( 'E.g. Thank you for your purchase.', 'filofw_text' ),
				'custom_attributes' => array_merge( $if_readonly_comment_custom_attributes, 
					array(
					)
				),
				'field_order'	=> 40,
			),
			  
			'output_format' => array(
				'title'         => __( 'Document Output Format', 'filofw_text' ),
				'type'          => 'select',
				'desc_tip'   => __( 'Choose which format of document to be generated.', 'filofw_text' ),
				'default'       => 'pdf',
				'class'         => 'output_format',
				'options'       => array(
					//'plain'         => __( 'Plain text', 'filofw_text' ),
					'pdf'          => __( 'PDF', 'filofw_text' ),
					'html'          => __( 'HTML', 'filofw_text' ),
					//'multipart'     => __( 'Multipart', 'filofw_text' ),
				),
				'field_order'	=> 50,
			),
			
			'sequence_details' => array(
				'type'        => 'sequence_details', //this is an own type, should be defined in 'generate_' . $v['type'] . '_html'() = generate_sequence_details_html() function below
				'field_order'	=> 60,
			),
			
		));
		
		$this->form_fields = wsl_array_column_sort($form_fields, "field_order", SORT_ASC); //sort by "field_order" field
		
		return $this->form_fields;

    }


    /**
     * generate_sequence_details_html
	 * 
	 * generate_sequence_details_html function (sequence_details type is defined above)
	 * generate_..._html() called by abstract-wc-settings-api.php, inside generate_settings_html(..)
	 * 
	 * @return string
     */
    public function generate_sequence_details_html() {
    	ob_start();
	    ?>
	    <tr valign="top">
	    	
		    <table id="sequence_details" class="widefat wc_input_table sortable" cellspacing="0">
	    		<thead>
	    			<h3><?php _e( 'Sequence Details', 'filofw_text' ); ?></h3>
	    			<tr>
	    				<th class="sort">&nbsp;</th>
	    				<th><?php _e( 'Sequence ID', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Code of actual sequence row.', 'filofw_text'); ?>">[?]</span></th>
	    				<th><?php _e( 'Sequence Name', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Name of actual sequence row.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Prefix', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Prefix of number format.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'First Number', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('First generated sequential number (the generated number of first document or first document of the year)', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Padding Length', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Length of the number without prefix and suffix. The number will be filled with the given padding string to achieve this length.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Padding String', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('This string will be applied to fill the number.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Suffix', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Suffix of number format.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Year Handling', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Include year into the number.', 'filofw_text'); ?>">[?]</span></th>
						<th><?php _e( 'Separator', 'filofw_text' ); ?>&nbsp;<span class="tips" data-tip="<?php _e('Separator character of financial document number parts.', 'filofw_text'); ?>">[?]</span></th>							
	    			</tr>
	    		</thead>
	    		<tbody class="data_rows">
	            	<?php
	            	$i = -1;

					wsl_log(null, 'abstract-filo-document.php generate_sequence_details_html $this: ' . wsl_vartotext($this));
					wsl_log(null, 'abstract-filo-document.php generate_sequence_details_html $this->sequence_details: ' . wsl_vartotext($this->sequence_details));

	            	if ( $this->sequence_details ) {
	            		foreach ( $this->sequence_details as $sequence_detail ) {
	                		$i++;

	                		echo '<tr class="data_row">
	                			<td class="sort"></td>
	                			<td><input type="text" value="' . esc_attr( $sequence_detail['sequence_id'] ) . '" name="field_sequence_id[' . $i . ']" required /></td>
								<td><input type="text" value="' . esc_attr( $sequence_detail['sequence_name'] ) . '" name="field_sequence_name[' . $i . ']" required /></td>
								<td><input type="text" value="' . esc_attr( $sequence_detail['prefix'] ) . '" name="field_prefix[' . $i . ']" maxlength="15" /></td>
								<td><input type="number" value="' . esc_attr( $sequence_detail['first_number'] ) . '" name="field_first_number[' . $i . ']" min="0" max="999999999999" required /></td>
								<td><input type="number" value="' . esc_attr( $sequence_detail['padding_length'] ) . '" name="field_padding_length[' . $i . ']" min="0" max="14" required /></td>
								<td><input type="text" value="' . esc_attr( $sequence_detail['padding_string'] ) . '" name="field_padding_string[' . $i . ']" maxlength="5" required /></td>
								<td><input type="text" value="' . esc_attr( $sequence_detail['suffix'] ) . '" name="field_suffix[' . $i . ']" maxlength="15"  /></td>
								<td>
									<select name="field_year_handling[' . $i . ']">
										<option value="no" ' . selected( 'no', esc_attr( $sequence_detail['year_handling']), false ) . '>' . __('Not include', 'filofw_text') . '</option>
										<option value="yes" ' . selected( 'yes', esc_attr( $sequence_detail['year_handling']), false ) . '>' . __('Include', 'filofw_text') . '</option>
										<option value="yes_restart" ' . selected( 'yes_restart', esc_attr( $sequence_detail['year_handling']), false ) . '>' . __('Include and Restart', 'filofw_text') . '</option>
									</select>
								</td>
								<td><input type="text" value="' . esc_attr( $sequence_detail['separator'] ) . '" name="field_separator[' . $i . ']" /></td>
		                    </tr>';
	            		}
	            	}
	            	?>
	        	</tbody>
	    		<tfoot>
	    			<tr>
	    				<th colspan="30"><a href="#" class="add button"><?php _e( '+ Add Row', 'filofw_text' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected row(s)', 'filofw_text' ); ?></a></th>
	    			</tr>
	    		</tfoot>
	        </table>
	       	<script type="text/javascript">
				jQuery(function() {
					jQuery('#sequence_details').on( 'click', 'a.add', function(){

						var size = jQuery('#sequence_details tbody .data_row').size();


						jQuery('<tr class="data_row">\
	                			<td class="sort"></td>\
	                			<td><input type="text" name="field_sequence_id[' + size + ']" required /></td>\
	                			<td><input type="text" name="field_sequence_name[' + size + ']" required /></td>\
	                			<td><input type="text" name="field_prefix[' + size + ']" maxlength="15" /></td>\
	                			<td><input type="number" name="field_first_number[' + size + ']" min="0" max="999999999999" required /></td>\
								<td><input type="number" name="field_padding_length[' + size + ']" value="6" min="0" max="14" required ></td>\
	                			<td><input type="text" name="field_padding_string[' + size + ']" value="0" maxlength="5" /></td>\
	                			<td><input type="text" name="field_suffix[' + size + ']" maxlength="15" /></td>\
								<td>\
									<select name="field_year_handling[' + size + '] required">\
										<option value="no">Not include</option>\
										<option value="yes">Includ</option>\
										<option value="yes_restart">Include and Restart</option>\
									</select>\
								</td>\
	                			<td><input type="text" name="field_separator[' + size + ']"  value="/" /></td>\
		                    </tr>').appendTo('#sequence_details tbody');

						return false;
					});
				});
			</script>

	    </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Save sequence details table
	 * 
	 * @return void
     */
    public function save_sequence_details() {
    	$data_rows = array();

		wsl_log(null, 'class-filo-document-sales-invoice.php $_POST: ' . wsl_vartotext($_POST));	
    	
    	if ( isset( $_POST['field_sequence_id'] ) ) {
   		
			$sequence_id_arr	= array_map( 'wc_clean', $_POST['field_sequence_id'] );
			$sequence_name_arr	= array_map( 'wc_clean', $_POST['field_sequence_name'] );
			$prefix_arr			= array_map( 'wc_clean', $_POST['field_prefix'] );
			$first_number_arr	= array_map( 'wc_clean', $_POST['field_first_number'] );
			$padding_length_arr	= array_map( 'wc_clean', $_POST['field_padding_length'] );
			$padding_string_arr	= array_map( 'wc_clean', $_POST['field_padding_string'] );
			$suffix_arr			= array_map( 'wc_clean', $_POST['field_suffix'] );
			$year_handling_arr	= array_map( 'wc_clean', $_POST['field_year_handling'] );
			$separator_arr		= array_map( 'wc_clean', $_POST['field_separator'] );

			$sequence_rows = array();

			foreach ( $sequence_id_arr as $i => $sequence_id ) {
				if ( ! isset( $sequence_id_arr[ $i ] ) ) {
					continue;
				}

	    		$sequence_rows[ $sequence_id_arr[ $i ] ] = array(
	    			'sequence_id'      => $sequence_id_arr[ $i ],
					'sequence_name'    => $sequence_name_arr[ $i ],
					'prefix'           => $prefix_arr[ $i ],
					'first_number'     => $first_number_arr[ $i ],
					'padding_length'   => $padding_length_arr[ $i ],
					'padding_string'   => $padding_string_arr[ $i ],
					'suffix'           => $suffix_arr[ $i ],
					'year_handling'    => $year_handling_arr[ $i ],
					'separator'        => $separator_arr[ $i ],
	    		);
	    	}
    	}

    	wsl_log(null, 'class-filo-document-sales-invoice.php str_replace(document_, , $this->id) . _sequences: ' . wsl_vartotext(str_replace('document_', '', $this->id) . '_sequences'));
		wsl_log(null, 'class-filo-document-sales-invoice.php $sequence_rows: ' . wsl_vartotext($sequence_rows));
    	
    	update_option( str_replace('document_', '', $this->id) . '_sequences', $sequence_rows ); // e.g. 'filo_sales_invoice_sequences' from 'document_filo_sa_quotation'
    }

	/**
	 * Admin Panel Options Processing
	 * - Saves the options to the DB
	 * 
	 * @todo to be verify 
	 *
	 * @return void
	 */
	public function process_admin_options() {

		// Save regular options
		parent::process_admin_options();

	}

	/**
	 * Admin Options
	 *
	 * @return void
	 */
	public function admin_options() {

		?>
		<h3><?php echo ( ! empty( $this->title ) ) ? $this->title : __( 'Settings','woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->description ) ) ? wpautop( $this->description ) : ''; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>

		<?php 
	}

	//Override parent function with an empty one
	public function style_inline( $content ) {
		return $content;
	}

}
