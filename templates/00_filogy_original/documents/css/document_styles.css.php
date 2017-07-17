<?php
/* After changing this file, cache clearing should be necessary in your browser! */

/** WordPress Bootstrap */
//require_once( dirname( __FILE__ ) . '/../../../../../../../wp-load.php' );
//TEST: http://yoursite.com/wp-content/plugins/filogy-framework/templates/documents/css/document_styles.css.php

//moved to class-filo-initial-functions.php add_individual_page_header():
	//header('content-type:text/css');
	////header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT");
	//header("Cache-Control: no-cache, must-revalidate"); //http://stackoverflow.com/questions/1341089/using-meta-tags-to-turn-off-caching-in-all-browsers 
	//header("Cache-Control: max-age=0, must-revalidate");
	//header("Expires: 0, must-revalidate");
	//header("Expires: Tue, 01 Jan 1980 1:00:00 GMT, must-revalidate");
	//header("Pragma: no-cache, must-revalidate");

	
// this individual page is loaded by class-filo-initial-functions.php
//TEST: http://yoursite.com/?filo_individual_page=template00_document_styles_css


$background_color 				= wc_clean (get_option( 'filo_document_body_background_color' ));
$cell_background_color          = wc_clean (get_option( 'filo_document_cell_background_color' ));
$text_color 					= wc_clean (get_option( 'filo_document_text_color' ));
$headline_background_colour 	= wc_clean (get_option( 'filo_document_headline_background_color' ));
$headline_text_colour_dark 		= wc_clean (get_option( 'filo_document_headline_text_color_dark' ));
$headline_text_colour_light 	= wc_clean (get_option( 'filo_document_headline_text_color_light' ));
$filo_document_border_color 	= wc_clean (get_option( 'filo_document_border_color' ));

$fileformat = isset($_GET['fileformat']) ? 	wc_clean( $_GET['fileformat'] ) : null; //+wc_clean

if ( $fileformat == 'pdf' ) {
	$filo_font_family            = wc_clean (get_option( 'filo_document_font_family_pdf' ));  //DejaVu Sans, Verdana, Arial, sans-serif //DejaVu Sans supports a wide range of UTF-8 characters in DOMPDF
} else {
	$filo_font_family            = wc_clean (get_option( 'filo_document_font_family_html' ));  //DejaVu Sans, Verdana, Arial, sans-serif //DejaVu Sans supports a wide range of UTF-8 characters in DOMPDF
}

$filo_document_size = wc_clean (get_option( 'filo_document_size' ));
$filo_document_orientation = wc_clean (get_option( 'filo_document_orientation' ));

$phisical_paper_size = FILO_Documents::wsl_document_paper_size( $filo_document_size, $filo_document_orientation );

?>

@page {
	margin: 0px;
	size: <?php echo $phisical_paper_size['x_mm']; ?>mm <?php echo $phisical_paper_size['y_mm']; ?>mm;
}	


body {

	<?php if ( $fileformat == 'html' ) { ?>
		max-width: <?php echo $phisical_paper_size['x_px'] - 100; ?>px;
	<?php } ?>
	

	/* Document "margins": */
	padding-left: 50px;
	padding-right: 50px;
	padding-top: 80px;
	padding-bottom: 80px;
	
	color: <?php echo $text_color; ?>;
	background-color: <?php echo $background_color; ?>;	
}

.filo_document,
.filo_draft {
	font-family: <?php echo $filo_font_family; // DejaVu Sans, Verdana, Arial, sans-serif ?>;
	font-size: 12px;
} 

#filo_subject {
	font-size: 24px;
	font-weight: bold;
	text-transform: uppercase;
	margin-top: 10px;
	margin-bottom: 5px;
}

#filo_logo {
	min-height: 100px;
	margin-right: 10px;
}

#filo_logo img{
	max-width: 100%;
	max-height: 160px;
}


<?php if ( $fileformat == 'pdf' ) { //this is repair a dompdf problem, only needed in case of pdf format?>

	/*#filo_logo_and_seller {
		width: 50%;
	}*/
	
<?php } ?>


#column_2 {
	margin-bottom: 20px;
}


#document_head_data, #filo_seller_head_data {
	margin-bottom: 10px;
}

#filo_seller_head_data {
	width: 100%;
}

#filo_seller_head_data,
#document_head_data .filo_headline, 
.filo_address .filo_headline,
.filo_table_headline,
.filo_payment_method_data .filo_headline,
.filo_notes .filo_headline,
#tax_detail_table #filo_summary_line_label_header,
#tax_detail_table #filo_summary_line_value_header{
	color: <?php echo $headline_text_colour_light; ?>;
	background: <?php echo $headline_background_colour; ?>;
	border: 1px solid <?php echo $filo_document_border_color; ?>;
	padding: 3px;
	font-size: smaller;
	font-weight: normal;
	text-transform: uppercase;
}

#tax_detail_table .filo_headline{
	text-align: left;
}

#tax_detail_table {
	margin-top: 20px;
	margin-bottom: 20px;
}


#filo_billing_address .filo_value,
#filo_shipping_address .filo_value,
.filo_document_items,
.filo_payment_method_title,
.filo_payment_method_data .filo_headline_3,
.filo_note {
	margin-top: 10px;
    
  }

.filo_payment_method_data .filo_headline_2{
	font-size: smaller;
	font-weight: bold;
	text-transform: uppercase;
}

#document_number,
#due_date,
.filo_address .filo_address_name,
#order_total {
	font-size: larger;
	font-weight: bold;	
  }

.filo_customer_addresses .filo_address {
	border-style: solid; /*solid none none none*/ 
	border: none;
	margin-right: -2px;
}

.filo_table_cell{
	padding: 5px;
}


#doc_items_table thead, #doc_items_table tbody {
	border: 1px solid <?php echo $filo_document_border_color; ?>;
}

thead .filo_table_cell,
tbody .filo_table_cell,
tfoot .filo_table_cell {
    vertical-align: top;
	border-left: 1px solid <?php echo $filo_document_border_color; ?>;
	border-right: 1px solid <?php echo $filo_document_border_color; ?>;
}
  
/*tfoot .filo_table_cell {
	vertical-align: top;
	border: 1px solid <?php echo $filo_document_border_color; ?>;
}*/

tfoot #order_subtotal_row .filo_table_cell,
tfoot #order_total_row .filo_table_cell {
	color: <?php echo $headline_text_colour_light; ?>;
	background: <?php echo $headline_background_colour; ?>;
}


.closing_line_cell {
	border-top: 1px solid <?php echo $filo_document_border_color; ?>;
	
}


.filo_table_subheader_cell {
	display: none;
}

.filo_payment_method_data p {
	display: none;
}

.filo_draft {

	position: absolute;
	
	/*border: 1px solid red;*/	
	
	<?php if ( $fileformat == 'html' ) { ?>
		
		<?php //for html format we apply the phisical paper size ?>	
		width: <?php echo $phisical_paper_size['x_px']; ?>px;	
		top: 300px; 
	<?php } else { ?>

		<?php //for pdf format we apply 100% width, because dompdf sets the paper size not a html way, and the pixel size is different tha our $phisical_paper_size['x_px'] value ?>		
		width: 100%;		
		top: 400px;
		
	<?php } ?>
		
	text-align: center;
		
	<?php
		//200 px font size is good for 'DRAFT', that is 5 letters
		//other text length is changed depending on its length

		$original_draft_font_size = 180; //px
		$original_draft_text_len = 5; //character 
		
		//translated draft text
		$draft_text_len = strlen( _x( 'Draft', 'filo_doc', 'filo_text' ) );		
		
		$correction = sqrt(( $draft_text_len - $original_draft_text_len )) * 1.5; 
		
		$changed_draft_font_size = $original_draft_font_size * $phisical_paper_size['x_px']/150 / $draft_text_len + $correction;
	?>
		
	font-size: <?php echo $changed_draft_font_size; ?>px; 
	opacity: 0.06; 
	font-weight: bold;
	text-transform: uppercase;
	-webkit-transform: rotate(-0.4rad); 
	-moz-transform: rotate(-0.4rad); 
	-ms-transform: rotate(-0.4rad);
}
 