<?php
/* After changing this file, cache clearing should be necessary in your browser! */

/** WordPress Bootstrap */
//require_once( dirname( __FILE__ ) . '/../../../../../../../wp-load.php' );
//TEST: http://yoursite.com/wp-content/plugins/filogy/templates/01_filogy_standard/documents/css/document_general_layout.css.php

//moved to class-filo-initial-functions.php add_individual_page_header():
	//header('content-type:text/css');
	////header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT");
	//header("Cache-Control: no-cache, must-revalidate"); //http://stackoverflow.com/questions/1341089/using-meta-tags-to-turn-off-caching-in-all-browsers 
	//header("Cache-Control: max-age=0, must-revalidate");
	//header("Expires: 0, must-revalidate");
	//header("Expires: Tue, 01 Jan 1980 1:00:00 GMT, must-revalidate");
	//header("Pragma: no-cache, must-revalidate");

// this individual page is loaded by class-filo-initial-functions.php
//TEST: http://yoursite.com/?filo_individual_page=template01_document_general_layout_css

$doc_customizer_root_settings = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true );  //use it e.g. $doc_customizer_settings['']['filo_doc_template_custom_settings']['pdf_gen_doc_format']


if ( isset($doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['font_size']) ){
	$document_general_font_size = $doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['font_size'];
} else {
	$document_general_font_size = '12px';
}

if ( isset($doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['padding_top']) ){
	$document_general_padding_top = $doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['padding_top'];
} else {
	$document_general_padding_top = '0';
}

if ( isset($doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['padding_bottom']) ){
	$document_general_padding_bottom = $doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['padding_bottom'];
} else {
	$document_general_padding_bottom = '0';
}

$fileformat = isset($_GET['fileformat']) ? wc_clean( $_GET['fileformat'] ) : null; //+wc_clean

if ( $fileformat == 'pdf' ) {
	$filo_font_family            = wc_clean (get_option( 'filo_document_font_family_pdf' ));  //DejaVu Sans, Verdana, Arial, sans-serif //DejaVu Sans supports a wide range of UTF-8 characters in DOMPDF
} else {
	$filo_font_family            = wc_clean (get_option( 'filo_document_font_family_html' ));  //DejaVu Sans, Verdana, Arial, sans-serif //DejaVu Sans supports a wide range of UTF-8 characters in DOMPDF
}

//$filo_document_size = wc_clean (get_option( 'filo_document_size' ));
//$filo_document_orientation = wc_clean (get_option( 'filo_document_orientation' ));
$filo_document_size = FILO_Documents::get_filo_document_size();
$filo_document_orientation = FILO_Documents::get_filo_document_orientation();

$phisical_paper_size = FILO_Documents::wsl_document_paper_size( $filo_document_size, $filo_document_orientation );
wsl_log(null, 'document_general_layout.css.php $phisical_paper_size: ' . wsl_vartotext($phisical_paper_size));

// Set PDF top and bottom margin to 50px, and set it to 0 for the first PDF page (to be able to insert headers without top border) (using @page and @page :first)
// https://github.com/dompdf/dompdf/issues/385
?>

@page {
	margin: 50px 0 50px 0;
	size: <?php echo $phisical_paper_size['x_mm']; ?>mm <?php echo $phisical_paper_size['y_mm']; ?>mm;
}
@page :first { 
	margin: 0; 
}	

.filo_document_body {

	<?php if ( $fileformat == 'html' ) { ?>
	width: <?php echo $phisical_paper_size['x_px']; ?>px;
	<?php } ?>

	margin-left: auto;
	margin-right: auto;	
	
}

.filo_document {
	<?php if ( $fileformat == 'html' ) { ?>	
	min-height: <?php echo $phisical_paper_size['y_px'] - $document_general_padding_top - $document_general_padding_bottom; //.filo_document{min-height: ...} is set the inner side without margins, but phisical_paper_size contains margins, thus we have to deduct padding size. ?>px;	
	<?php } ?>
}

/*
.panel-grid, .panel-table-grid {
	padding-left: 50px;
	padding-right: 50px;
}
*/

/* elimimate space between inline elements (panel-grids), and set back in under of it */
/* https://css-tricks.com/fighting-the-space-between-inline-block-elements/ */
.panel-grid {
	font-size: 0; 	 
}

<?php // it is applied only not customizer mode. In customizer mode, in a special bransh of print_preview_script() ?>
<?php if ( ( ! isset($_GET['filo_usage']) or $_GET['filo_usage'] != 'doc' ) and ( ! isset($_GET['filo_customizer']) or $_GET['filo_customizer'] != 'true' ) ) { ?>
.panel-grid-cell {
	font-size: <?php echo $document_general_font_size; ?>; /* reset font size, not to be 0 */
}
<?php } ?>

/* SiteOrigin float:left cannot be applied in dompdf, thus we use display:inline-block instead ot that. */
/*.filo_head_data_column, #filo_logo, #filo_seller {*/
.panel-grid-cell {
	display: inline-block;
	vertical-align: top;
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


/* data table layout BEGIN */

/* table */
.filogy-table {
    display: table;
    width: 100%;
    clear: both;
}

/* row */
.filogy-table .filogy-table-row {
    display: table-row;
    clear: both;
}

/* cell */
.filogy-table .filogy-table-cell {
    display: table-cell;
    /*float: inherit !important;*/
}

/* data table layout END */


/* items table layout BEGIN */

/* table */
.panel-table {
    display: table;
    width: 100%;
    clear: both;
}

/* row */
.panel-table .panel-grid {
    display: table-row;
    clear: both;
}

/* cell */
.panel-table .panel-grid-cell {
    display: table-cell;
    float: inherit !important;
}

/* items table layout END */


/* logo */
#filo_logo img {
	max-width: 100%;
}

