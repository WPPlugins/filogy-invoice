<?php
/**
 * Complex version of standard document
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */
// include BEGIN
include(FILO()->plugin_path() .  '/templates/documents/document-standard-begin.php');
?>

<?php if ($draft_text !='' ) { ?>
	<div class="filo_draft_wrapper">
		<div class="filo_draft" ><?php echo $draft_text; ?></div>
	</div>
<?php } ?>

<div id="filo_document_<?php echo $order->get_doc_type(); ?>" class="filo_document" >

	<?php wc_get_template( 'documents/document-head-data.php', 
							array( 
								'order' 		=> $order,
								'output_format' => $output_format,
								'draft_text'	=> $draft_text,
								'doc_financial_data' => $doc_financial_data, 
								'actual_template_key' => $actual_template_key,
								'standard_template_key' => $standard_template_key,
								'filo_document_templates' => $filo_document_templates, 
							),
							$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
							$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
	?>
	
		
	<?php wc_get_template( 'documents/document-addresses.php', 
							array( 
								'order' => $order,
								'doc_financial_data'	=> $doc_financial_data, 
								'actual_template_key' => $actual_template_key,
								'standard_template_key' => $standard_template_key,
								'filo_document_templates' => $filo_document_templates, 
							),
							$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
							$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
	?>

	
	<?php wc_get_template( 'documents/document-items_header.php', 
							array( 
								'order'                 => $order,
								//'items'                 => $item,
								'show_download_links'   => $order->is_download_permitted(), // 1: $show_download_links,
								'show_sku'              => true, // 2: $show_sku,
								'show_purchase_note'    => $order->has_status( 'processing' ), // 3: $show_purchase_note,
								//'show_image'            => $show_image,
								//'image_size'            => $image_size,
								//'item_type'             => $item_type,
								'document_settings' 	=> $document_settings,
								'doc_financial_data'	=> $doc_financial_data,
								'actual_template_key' => $actual_template_key,
								'standard_template_key' => $standard_template_key,
								'filo_document_templates' => $filo_document_templates, 
							),
							$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
							$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);							
	?>
		

	<?php do_action( 'filo_document_after_order_table', $order, $sent_to_admin, $plain_text ); ?>
	<p></p>
	<?php
	
		wc_get_template( 'documents/document-payment-method.php', 
						array(
							'order'                 => $order,
							'actual_template_key' => $actual_template_key,
							'standard_template_key' => $standard_template_key,
							'filo_document_templates' => $filo_document_templates, 
						),
						$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
						$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);
	?>
		
	<?php
		wc_get_template( 'documents/document-note.php', 
						array(
							'order'                 => $order,
							'document'				=> $document,
							'actual_template_key' => $actual_template_key,
							'standard_template_key' => $standard_template_key,
							'filo_document_templates' => $filo_document_templates, 
						),
						$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
						$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
		);

	?>

	<?php do_action( 'filo_document_order_meta', $order, $sent_to_admin, $plain_text ); ?>

</div>

<?php 
// include END
include(FILO()->plugin_path() .  '/templates/documents/document-standard-end.php');