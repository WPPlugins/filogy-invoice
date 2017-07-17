<?php
/**
 * Handle printable documents
 * 
 * @package     Filogy/Documents/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
class FILO_Documents {

	/**
	 * construct
	 */
	function __construct() {

		$this->init();
		
		wsl_log(null, 'class-filo-documents.php __construct: ');
		
		add_action( 'filo_document_header', array( $this, 'document_header' ), 10, 2 );
		add_action( 'filo_document_footer', array( $this, 'document_footer' ) );
		
		do_action( 'filo_register_document_template' );		
		do_action( 'filo_financial_document', $this );
		
				
	}

	protected static $_instance = null;

	/**
	 * instance 
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Init document classes
	 */
	function init() {

		// Include document classes
		wsl_log(null, 'class-filo-documents.php init: ' . wsl_vartotext(''));
		
		$this->documents = null;
		//include_once( WC()->plugin_path() . '/includes/' . 'abstracts/abstract-wc-email.php' );	//WC v2.2.6
		include_once( WC()->plugin_path() . '/includes/' . 'emails/class-wc-email.php' );			//WC v2.4.10
		
		include_once( FILOFW()->plugin_path() . '/includes/abstracts/abstract-filo-document.php' );	//ADD RaPe		

		//include all financial document types
		//e.g. $this->documents['FILO_Document_Sales_invoice'] = include( 'documents/class-filo-document-sales-invoice.php' );
		
		global $filo_post_types_financial_documents;
		
		//wsl_log(null, 'class-filo-documents.php init $filo_post_types_financial_documents: ' . wsl_vartotext($filo_post_types_financial_documents));
		
		if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )
		foreach ($filo_post_types_financial_documents as $doc_type) {

			//wsl_log(null, 'class-filo-documents.php init $doc_type: ' . wsl_vartotext($doc_type));				
			
			$post_type_obj = get_post_type_object( $doc_type );

			if ( ! empty($post_type_obj) ) {

				if ( property_exists($post_type_obj, 'class_name') ) {
					
					$class_name = $post_type_obj->class_name; //e.g: FILO_FinaDoc_Sa_Invoice
		
					//wsl_log(null, 'class-filo-documents.php init $post_type_obj: ' . wsl_vartotext($post_type_obj));
					wsl_log(null, 'class-filo-documents.php init $class_name: ' . wsl_vartotext($class_name));
								
					//$doc_type_cap = wsl_word_cap( $doc_type, '_' ); //e.g. sales_invoice -> Sales_Invoice
					//$class_name = 'FILO_Document_' . $doc_type_cap; //e.g: FILO_FinaDoc_Sa_Invoice
					
					$doc_type_without_filo = str_replace('filo_', '', $doc_type); //e.g. filo_sa_invoice -> sales_invoice
					
					$doc_type_hyphen = str_replace('_', '-', $doc_type_without_filo); //e.g. sales_invoice -> sales-invoice
					
					$file = 'documents/class-filo-document-' . $doc_type_hyphen . '.php';
					
					wsl_log(null, 'class-filo-documents.php init $file: ' . wsl_vartotext($file));
					wsl_log(null, 'class-filo-documents.php init get_include_path(): ' . wsl_vartotext( get_include_path() ));
					
					//if ( file_exists( $file ) )
									
					$this->documents[$class_name] = include( $file );
				}
				
			}
			
		}

		$this->documents = apply_filters( 'filo_document_classes', $this->documents );
		//wsl_log(null, 'class-filo-documents.php init $this->documents: ' . wsl_vartotext($this->documents));	
	}

	/**
	 * get_documents
	 */
	function get_documents() {
		//This can be used for document settings
		return $this->documents;
	}
	
	
	/**
	 * get_document
	 */
	function get_document( $finadoc_type ) {
		
		wsl_log(null, 'class-filo-documents.php get_document $finadoc_type: ' . wsl_vartotext($finadoc_type));
		//wsl_log(null, 'class-filo-documents.php get_document $this->documents: ' . wsl_vartotext($this->documents));
		
		if ( isset($this->documents[$finadoc_type]) ) {
			
			return $this->documents[$finadoc_type];
			
		} else {
			
			return null;
			
		}
	}	

	/**
	 * document_header
	 */
	function document_header( $document_heading, $output_format ) {
		global $filo_document_templates;

		$template_key = wc_clean (get_option( 'filo_document_template' ));
		//if no templat key option is set or the actual template value array is not exists (e.g. deactivate the plugin of which template is set), then use filo_standard_template
		if ( $template_key == '' or !is_array($filo_document_templates[$template_key]) )  
			$template_key = FILO_STANDARD_TEMPLATE; //set default //'filo_standard_template'
			
		wsl_log(null, 'class-filo-documents.php document_header $document_heading: ' . wsl_vartotext($document_heading));
		wsl_log(null, 'class-filo-documents.php document_header $filo_document_templates[$template_key][default_path]: ' . wsl_vartotext($filo_document_templates[$template_key]['default_path']));
		wsl_log(null, 'class-filo-documents.php document_header $output_format: ' . wsl_vartotext($output_format));
		
		wc_get_template( 
			'documents/document-header.php', 
			array( 
				'document_heading' => $document_heading, 
				'output_format' => $output_format,
			), 
			$filo_document_templates[$template_key]['template_path'], //FILO()->template_path(), 
			$filo_document_templates[$template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);

	}

	/**
	 * document_footer
	 */
	function document_footer() {
		global $filo_document_templates;
		
		$template_key = wc_clean (get_option( 'filo_document_template' ));
		//if no templat key option is set or the actual template value array is not exists (e.g. deactivate the plugin of which template is set), then use filo_standard_template
		if ( $template_key == '' or !is_array($filo_document_templates[$template_key]) )  
			$template_key = FILO_STANDARD_TEMPLATE; //set default //'filo_standard_template'
					
		wc_get_template( 
			'documents/document-footer.php', 
			array(), 
			$filo_document_templates[$template_key]['template_path'], //FILO()->template_path(), 
			$filo_document_templates[$template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
		
	}

	public static function get_filo_document_size() {
		global $filo_doc_root_options;
		
		if ( ! isset($filo_doc_root_options) ) {
			$filo_doc_root_options = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true ); //disable cleaning, for quicker run	
		}
		
		if ( isset( $filo_doc_root_options['']['Document-General']['css_document_general_selector']['filo_document_size']) ) {
			$filo_document_size = $filo_doc_root_options['']['Document-General']['css_document_general_selector']['filo_document_size'];
		} else {
			//set default
			$filo_document_size = 'a4';
		}
		
		return wc_clean($filo_document_size);
	}

	public static function get_filo_document_orientation() {
		global $filo_doc_root_options;
		
		
		
		if ( ! isset($filo_doc_root_options) ) {
			$filo_doc_root_options = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true ); //disable cleaning, for quicker run	
		}

		//wsl_log(null, 'class-filo-documents.php wsl_document_paper_size $filo_doc_root_options: ' . wsl_vartotext($filo_doc_root_options));
		
		if ( isset( $filo_doc_root_options['']['Document-General']['css_document_general_selector']['filo_document_orientation']) ) {
			$filo_document_orientation = $filo_doc_root_options['']['Document-General']['css_document_general_selector']['filo_document_orientation'];
		} else {
			//set default
			$filo_document_orientation = 'portrait';
		}
		
		return wc_clean($filo_document_orientation);
	}
	
	/**
	 * filo_document_paper_size
	 * 
	 * Convert document_paper_size type to x, y sizes in mm
	 * This is called when generating html (but not at pdf) 
	 */
	public static function wsl_document_paper_size( $document_paper_size, $document_orientation ) {
		
		//portrait paper sizes
		switch ($document_paper_size) {
			case 'a3': $return 		= array('x_mm' => 297, 'y_mm' => 420 ); break;
			case 'a4': $return 		= array('x_mm' => 210, 'y_mm' => 297 ); break;
			case 'a5': $return 		= array('x_mm' => 148, 'y_mm' => 210 ); break;
			case 'letter': $return 	= array('x_mm' => 216, 'y_mm' => 279 ); break;
			case 'legal': $return 	= array('x_mm' => 216, 'y_mm' => 356 ); break;
			default: $return 		= array('x_mm' => 210, 'y_mm' => 297 ); break;
		}		
		
		//if landscape, change x and y
		if ($document_orientation == 'landscape') {
			$x = $return['x_mm'];
			$y = $return['y_mm'];
			$return['x_mm'] = $y;
			$return['y_mm'] = $x;
		}

		//add sizes in px with 96dpi screen resolution
		//http://www.papersizes.org/a-sizes-in-pixels.htm		
		$inch_mm_rate = 25.4;
		$dpi = 96;
		$return['x_px'] = $return['x_mm'] / $inch_mm_rate * $dpi;
		$return['y_px'] = $return['y_mm'] / $inch_mm_rate * $dpi;
		
		wsl_log(null, 'class-filo-documents.php wsl_document_paper_size $document_paper_size: ' . wsl_vartotext($document_paper_size));
		wsl_log(null, 'class-filo-documents.php wsl_document_paper_size $document_orientation: ' . wsl_vartotext($document_orientation));
		wsl_log(null, 'class-filo-documents.php wsl_document_paper_size $return: ' . wsl_vartotext($return));
				
		return apply_filters( 'filo_document_paper_size', $return, $document_paper_size );
	}

}
