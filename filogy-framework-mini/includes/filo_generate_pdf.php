<?php
use Dompdf\Dompdf;
/**
 * PDF Generating Functions
 *  
 * @package     Filogy/Functions
 * @subpackage 	Framework
 * @category    Functions
 */

//if ( strpos( dirname( __FILE__ ), 'filogy-framework-mini' ) === false ) {
//	require_once( dirname( __FILE__ ) . '/../../../../wp-load.php' );  //frontend and admin too
//} else {
//	require_once( dirname( __FILE__ ) . '/../../../../../wp-load.php' );  //frontend and admin too
//}

// this individual page is loaded by class-filo-initial-functions.php

if ( isset($_GET['doc_id']) ) { //in this case $doc_id is a single number
	$doc_id = wc_clean( $_GET['doc_id'] ); //+wc_clean  
} elseif ( isset($_GET['doc_filter']) ) { //in this case $doc_id is an array of numbers

	wsl_log(null, 'filo_generate_pdf.php filo_generate_pdf_select_doc_ids $doc_filter 1: ' . wsl_vartotext( $doc_filter ));
	$doc_id = apply_filters( 'filo_generate_pdf_select_doc_ids', array(), $_GET );
}

$pseudo_doc_type = null;
if ( isset($_GET['filo_pseudo_doc_type']) ) { //in this case $doc_id is a single number
	$pseudo_doc_type = wc_clean( $_GET['filo_pseudo_doc_type'] ); //+wc_clean  
}

/*
if ( isset($_GET['doc_filter']) ) { //in this case $doc_id is an array of numbers

	wsl_log(null, 'filo_generate_pdf.php filo_generate_pdf_select_doc_ids $doc_filter 1: ' . wsl_vartotext( $doc_filter ));
	$doc_id = apply_filters( 'filo_generate_pdf_select_doc_ids', array(), $_GET );
	
} else {
	
	$doc_id = FILO_Financial_Document::get_doc_id_invbld(); //in this case $doc_id is a single number
	
}	
*/

wsl_log(null, 'filo_generate_pdf.php $_GET: ' . wsl_vartotext($_GET));

if (	
		( isset($_GET['filo_nonce']) and wp_verify_nonce($_GET['filo_nonce'], 'filo_generate_pdf_' . $doc_id) ) or
		( isset($_GET['filo_all_nonce']) and wp_verify_nonce($_GET['filo_all_nonce'], 'filo_generate_pdf') ) or
		( isset($_GET['filo_bulk_nonce']) and wp_verify_nonce($_GET['filo_bulk_nonce'], 'filo_generate_pdf') ) 
	) {

	//	add_filter('filo_gen_pdf_settings', 'filo_gen_pdf_settings', 10, 2);
	add_action('filo_document_head', 'filo_document_head');

	filo_generate_pdf($doc_id, $pseudo_doc_type);
	  
} else {
	echo __('Access denied - You are not authorized to access this page.', 'filofw_text');
}	

/**
 * filo_document_head
 */
function filo_document_head() {

	$filo_gen_pdf_settings['margin_left'] 		= 50; //footer left margin (px)
	$filo_gen_pdf_settings['margin_right'] 		= 50; //footer right margin (px)
	$filo_gen_pdf_settings['margin_bottom'] 	= 50; //footer bottom margin
	$filo_gen_pdf_settings['footer_padding_top']= 2;  //space between footer text and line
	
	//yes was the old deprecated value, 1 is the new customizer value
	$filo_gen_pdf_settings['footer_text_left']	= in_array( get_option( 'filo_show_created_by_text' ), array('yes', '1') ) ? sprintf( _x('Created by %s', 'filo_doc', 'filo_text'), FILO_PROGRAM_FULLNAME ) : "";
	$filo_gen_pdf_settings['footer_text_right']	= _x('Page', 'filo_doc', 'filo_text') . ": PAGE_NUM / PAGE_COUNT"; 
	$filo_gen_pdf_settings['left_text_position_shift'] 	= 0;  //shift left side text, if automatic positioning is not accurate (+/-)
	$filo_gen_pdf_settings['right_text_position_shift'] = 0; //shift right side text, if automatic positioning is not accurate (+/-)
	
	$filo_gen_pdf_settings['font_type']			= "helvetica";
	$filo_gen_pdf_settings['font_weight']		= "normal";
	$filo_gen_pdf_settings['font_size']			= 9;


	//$hex_color = "#ff9900";
	$hex_color = get_option( 'filo_document_text_color' );
	list($color_r, $color_g, $color_b) = sscanf($hex_color, "#%02x%02x%02x");
	
	$filo_gen_pdf_settings = apply_filters( 'filo_gen_pdf_settings', $filo_gen_pdf_settings );
	wsl_log(null, 'filo_generate_pdf.php $filo_gen_pdf_settings: ' . wsl_vartotext($filo_gen_pdf_settings));

	//When using DOMPDF inline script the values in the color array have to be in the range 0-1. So array(152,100,0) should actually be array(0.596, 0.392, 0). Divide your 0-255 values by 255 to get a decimal approximation.
	$color_r = $color_r/255; $color_b = $color_b/255; $color_g = $color_g/255;
	
	
	//the following tags is removed, because we use $canvas->page_script: <script type="text/php"> </script>
	$html_content_dompdf_settings = '
		
			if (isset($pdf)) {


				//------------------------------

				$margin_left 		= '.$filo_gen_pdf_settings["margin_left"].'; //footer left margin (px)
				$margin_right 		= '.$filo_gen_pdf_settings["margin_right"].'; //footer right margin (px)
				$margin_bottom 		= '.$filo_gen_pdf_settings["margin_bottom"].'; //footer bottom margin
				$footer_padding_top = '.$filo_gen_pdf_settings["footer_padding_top"].'; //space between footer text and line
				
				$footer_text_left	= "'.$filo_gen_pdf_settings["footer_text_left"].'"; 
				$footer_text_right	= "'.$filo_gen_pdf_settings["footer_text_right"].'";

				//replace {PAGE_NUM} and {PAGE_COUNT} in left and/or right footer text
				$footer_text_left = str_replace("PAGE_NUM", $PAGE_NUM, $footer_text_left);
				$footer_text_left = str_replace("PAGE_COUNT", $PAGE_COUNT, $footer_text_left);
				$footer_text_right = str_replace("PAGE_NUM", $PAGE_NUM, $footer_text_right);
				$footer_text_right = str_replace("PAGE_COUNT", $PAGE_COUNT, $footer_text_right);
				
				//shift left side text, if automatic positioning is not accurate +-
				$left_text_position_shift 	= '.$filo_gen_pdf_settings["left_text_position_shift"].';
				//shift right side text, if automatic positioning is not accurate +-
				$right_text_position_shift 	= '.$filo_gen_pdf_settings["right_text_position_shift"].';
				
				$font_type			= "'.$filo_gen_pdf_settings["font_type"].'"; //"helvetica";
				$font_weight		= "'.$filo_gen_pdf_settings["font_weight"].'"; //"normal";
				$font_size			= '.$filo_gen_pdf_settings["font_size"].'; //9;
				$color 				= array(".$color_r.",".$color_b.",".$color_g.");							
				//------------------------------

				//Convert px to pt
				$margin_left = $margin_left * 3/4; 
				$margin_right = $margin_right * 3/4;
				$margin_bottom = $margin_bottom * 3/4;
				$footer_padding_top = $footer_padding_top * 3/4;
				$left_text_position_shift = $left_text_position_shift * 3/4;
				$right_text_position_shift = $right_text_position_shift * 3/4;

				//Footer
								
				//$footer = $pdf->open_object();
				
				$w = $pdf->get_width();
				$h = $pdf->get_height();
				
				//$font = Font_Metrics::get_font($font_type, $font_weight);
				$font = $fontMetrics->get_font($font_type, $font_weight);
				
				//$txtHeight = Font_Metrics::get_font_height($font, $font_size);
				$txtHeight = $fontMetrics->get_font_height($font, $font_size);
				
				$y = ($h - 2 * $txtHeight - $margin_bottom) + 5;
				
				//$width = Font_Metrics::get_text_width($footer_text_right, $font, $font_size);
				$width = $fontMetrics->get_text_width($footer_text_right, $font, $font_size);
				
				
				$pdf->line($margin_left-3, $y-2, $w - $margin_right, $y-2, $color, 1);
				
				$pdf->text($margin_left-3 + $left_text_position_shift +1, $y, $footer_text_left, $font, $font_size, $color);

				$pdf->text($w - $width - $margin_right + $right_text_position_shift -1, $y, $footer_text_right, $font, $font_size, $color);
				
			}
		

	';	  
	//wsl_log(null, 'filo_generate_pdf.php html1: ' . wsl_vartotext($html_content_dompdf_settings));
	
	//echo $html_content_dompdf_settings;
	return $html_content_dompdf_settings;
	
}
 
/**
 * filo_generate_pdf
 */ 
function filo_generate_pdf( $doc_id, $pseudo_doc_type ) {
	
	wsl_log(null, 'filo_generate_pdf.php $doc_id: ' . wsl_vartotext( $doc_id ));	

	global $output_format; //make it global to be available in document_styles.css.php to decide which font-family have to be used pdf or html
	
	//require_once( FILOFW()->plugin_path() . '/modules/dompdf/dompdf_config.inc.php' );
	
	$documenter = FILO()->documenter();  //new FILO_Documents;  - set triggerable document templates (this should be before WC email templates)

	//wsl_log(null, 'filo_generate_pdf.php $doc_id_arr: ' . wsl_vartotext($doc_id_arr ));

	// Decide if doc_id is a single number or an array of array (single / bulk)
	
	if ( ! is_array( $doc_id ) ) { //single	
	
		$mode = 'single';
		
		wsl_log(null, 'filo_generate_pdf.php get_post_type( $doc_id ): ' . wsl_vartotext(get_post_type( $doc_id )));
		
		//This filter gets the apropriate object, which can generate html content of the apropriate post type. (e.g filo_generate_sales_invoice_document)
		//TRIGGERS Document creation
		$FILO_Document_Object = apply_filters( 'filo_generate_' . get_post_type( $doc_id ) . '_document', '', $doc_id, $pseudo_doc_type ); //e.g: document_filo_sa_invoice, document_shop_order (earlier: FILO_Document_Sales_Invoice, filo_generate_filo_sa_invoice_document)

		if ( empty($FILO_Document_Object) ) {
			echo 'The document of the doc_id does not exist.';
			return false;
		}
		
		//if we generate a pseudo document, then we need to use some settings of it (e.g. output format), that is why we create FILO_Pseudo_Document_Object here.
		if ( ! empty($pseudo_doc_type) ) { 		
			$FILO_Pseudo_Document_Object = apply_filters( 'filo_generate_' . $pseudo_doc_type . '_document', '', $doc_id, null ); //e.g: document_filo_sa_invoice, document_shop_order (earlier: FILO_Document_Sales_Invoice, filo_generate_filo_sa_invoice_document)
		}
	
		//wsl_log(null, 'filo_generate_pdf.php filo_generate_pdf $FILO_Document_Object: ' . wsl_vartotext($FILO_Document_Object ));
		
		$html_content = $FILO_Document_Object->get_content();

		//if we generate a pseudo document, then we have to get the output format that is set for the pseudo document
		if ( empty($FILO_Pseudo_Document_Object) ) {
			$output_format = $FILO_Document_Object->get_option( 'output_format' );
		} else {
			$output_format = $FILO_Pseudo_Document_Object->get_option( 'output_format' );
		}

		wsl_log(null, 'filo_generate_pdf.php filo_generate_pdf $output_format 0: ' . wsl_vartotext( $output_format ));
		
		//Comment:  In case of customization of a document pdf output format must not be used, just html. 
		//			This is done in FILO_Document::get_option function
		
		//wsl_log(null, 'filo_generate_pdf.php $FILO_Document_Object 2: ' . wsl_vartotext($FILO_Document_Object ));
		//wsl_log(null, 'filo_generate_pdf.php $html_content: ' . wsl_vartotext($html_content));
		//wsl_log(null, 'filo_generate_pdf.php $output_format: ' . wsl_vartotext($output_format));
		
		//$order = FILO_Order_Factory::get_order($doc_id);
		$order = wc_get_order( $doc_id ); //filo_get_order
		//wsl_log(null, 'filo_generate_pdf.php $order: ' . wsl_vartotext($order));

	} else { //bulk
	
		$mode = 'bulk';
		
		$html_content = '';
		foreach ( $doc_id as $the_doc_id ) {
			
			wsl_log(null, 'filo_generate_pdf.php $the_doc_id: ' . wsl_vartotext( $the_doc_id ));

			$FILO_Document_Object = apply_filters( 'filo_generate_' . get_post_type( $the_doc_id ) . '_document', '', $the_doc_id ); //e.g: filo_generate_filo_sa_invoice_document (earlier: FILO_Document_Sales_Invoice)
			
			$html_content .= $FILO_Document_Object->get_content();
			
			if ( ! isset($output_format) ) { //we use output format in case of first doc in the loop
				$output_format = $FILO_Document_Object->get_option( 'output_format' );
			}
						
		}

	}
	
	//wsl_log(null, 'filo_generate_pdf.php $html_content 1: ' . wsl_vartotext( $html_content ));
	 
	/*$html_content =
	  '<html><body>'.
	  '<p>Put your html here, or generate it with your favourite '.
	  'templating system.</p>'.
	  '</body></html>';
	*/

	
	//UTF-8 template
	/*$html_content = '
		<html>
		<head>
		    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		</head>
		<body>
		    <p style="font-family: DejaVu Sans;">árvíztűrőtükörfúrógép</p>
		</body>
		</html>	
	';*/	

	if ( $output_format=='pdf' ) {

		//if filo_deactivate_rewrite_rules() is called, NextGEN Gallery plugin write an unnecessary content: <!-- ngg_resource_manager_marker -->, and pdf cannot be displayed: ERR_INVALID_RESPONSE
		//filo_deactivate_rewrite_rules();
	  
	  	//$filo_document_size = get_option( 'filo_document_size' );
		//$filo_document_orientation = get_option( 'filo_document_orientation' );
		$filo_document_size = FILO_Documents::get_filo_document_size();
		$filo_document_orientation = FILO_Documents::get_filo_document_orientation();
		
		//dompdf_0.7.0
		require_once( FILOFW()->plugin_path() . '/modules/dompdf/autoload.inc.php' );		

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->set_option('isRemoteEnabled', 'true'); //to load logo using absolute url
		$dompdf->loadHtml($html_content,'UTF-8'); 
		
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper( $filo_document_size, $filo_document_orientation ); // a4-letter-...; portrait-landscape
		
		$dompdf->set_option("isPhpEnabled", true);
		
		// Render the HTML as PDF
		$dompdf->render(); 

		$fontMetrics = $dompdf->getFontMetrics();
		$canvas = $dompdf->get_canvas();
		$font = $fontMetrics->getFont('Helvetica');
		
		//$canvas->page_text(30, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
		////$canvas->line(30,25,560,25,array(0,0,0),1);
		
		//http://stackoverflow.com/questions/30273033/why-lines-images-draw-only-on-last-page-of-pdf-using-dompdf-in-php
		//$canvas->page_script('
  		//	$pdf->line(30,25,560,25,array(0,0,0),1);
		//	$pdf->text(30, 10, "Page " . $PAGE_NUM ." of " . $PAGE_COUNT , $font, 10, array(0, 0, 0));
		//');
		
		$canvas->page_script( filo_document_head() );
				
		//----
		/*
		$fontMetrics = $dompdf->getFontMetrics();
		$canvas = $dompdf->get_canvas();
		$font = $fontMetrics->getFont('Helvetica');
		$canvas->page_text(30, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
		$canvas->line(30,25,560,25,array(0,0,0),1);
		*/
		
		//----
		/*
		//$font = Font_Metrics::get_font("helvetica", "bold");
		$fontMetrics = $dompdf->getFontMetrics();
		$canvas = $dompdf->get_canvas();
		$font = $fontMetrics->getFont('Helvetica');
		$footer = $canvas->open_object();
		
		$canvas->page_text(30, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0, 0, 0));
		$canvas->line(30,25,560,25,array(0,0,0),1);
		
		$canvas->close_object();
		$canvas->add_object($footer, "all");
		*/
		//----
		//dompdf_0.7.0 END
		
		/* 
		//dompdf_0.6.1		
		require_once( FILOFW()->plugin_path() . '/modules/dompdf/dompdf_config.inc.php' );
		
		$dompdf = new DOMPDF();
		$dompdf->set_paper( $filo_document_size, $filo_document_orientation ); // a4-letter-...; portrait-landscape
		$dompdf->load_html($html_content,'UTF-8');
		$dompdf->render();
		//dompdf_0.6.1 END
		*/
		  
		if ( $mode == 'single' ) {
			//$document_number = get_post_meta( $doc_id, '_document_number', true );
			
			$document_number = $order->get_document_number();
			
			if ( ! empty($pseudo_doc_type) ) { //in this case $doc_id is a single number
				$doc_type = $pseudo_doc_type;
				$doc_status = ($order->is_pseudo_doc_valid( $doc_type ) ? 'valid' : 'draft');
			} else {
				$doc_type = $order->get_doc_type();
				if ( FILO_TYPE == 'filo_invoice_type' ) {
					$doc_status = 'valid'; //set a value, that not draft, not to insert draft_text into the filename
				} else {
					$doc_status = $order->get_doc_status();					
				}
			}
			
			//if ( isset($document_number) and $document_number != '' ) {
			if ($doc_status == '' or $doc_status == 'draft' ) {
				$file_name = 'draft_' . $doc_type . '_' . $order->get_id();
			} else {
				$file_name = $doc_type . '_' . $document_number;
			}
		} elseif ( $mode == 'bulk' ) {
			
			$file_name = 'bulk_documents';
		
		}
		
		wsl_log(null, 'filo_generate_pdf.php $file_name: ' . wsl_vartotext($file_name));		
		
		// Output the generated PDF to Browser
		// If logging is not enabled, then suppress displaying dompdf streaming notes
		$is_logging_enabled = wsl_is_logging_enabled();
		if ( $is_logging_enabled == '1' ) {
			$dompdf->stream( $file_name . ".pdf", array('compress' => 1, 'Attachment' => 0));
		} else {
			@$dompdf->stream( $file_name . ".pdf", array('compress' => 1, 'Attachment' => 0));
		} 
		
	} else {

		//wp_head(); //QQQ21
		echo $html_content;

		//wp_footer(); //QQQ21
		
	}


}

/**
 * filo_deactivate_rewrite_rules
 */
function filo_deactivate_rewrite_rules() {
	global $wp_rewrite;
	
	$wp_rewrite = null;
	wsl_log(null, 'filo_generate_pdf.php filo_deactivate_rewrite_rules $wp_rewrite: ' . wsl_vartotext($wp_rewrite));

}

?>