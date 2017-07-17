<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Head Data Template
 *
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

$finadoc = new FILO_Financial_Document($order->get_id());
$post_type_name = $finadoc->get_doc_type_label_singular_short_name();
//$seller_vat_number = get_option('filo_seller_vat_number');
$seller_vat_number = $doc_financial_data['seller_address']['seller_vat_number'];
$seller_domestic_vat_number = $doc_financial_data['seller_address']['seller_domestic_vat_number'];

$filo_document_logo = get_option('filo_document_logo');
?>

<div class="filo_head_data"> <!--filo_doc_section-->
	<div class="filo_head_data_column" id="column_1">

		<div id="filo_logo_and_seller">
			<?php if ( ! empty( $filo_document_logo ) ) { ?>
				<div id="filo_logo">
					<img src="<?php echo $filo_document_logo; ?>" alt="">
				</div>
			<?php } ?>
	
			<div id="filo_seller">
				<div class="filo_headline" id="filo_seller_head_data"><?php echo $doc_financial_data['document_data']['shop_role_display_name']; ?></div>
				<?php wsl_log(null, 'document-head-data.php $doc_financial_data[document_data][shop_role_display_name]: ' . wsl_vartotext($doc_financial_data)); ?>
				<div class="filo_address" id="filo_seller_address">
					<?php echo $order->get_formatted_seller_address( 'before_filo_start_order' ); ?>
					<?php if ( $seller_vat_number != '' ) echo '<br>' . _x('VAT ID', 'filo_doc', 'filo_text') . ': ' . $seller_vat_number; ?>
					<?php if ( $seller_domestic_vat_number != '' ) echo '<br>' . _x('VAT ID (domestic)', 'filo_doc', 'filo_text') . ': ' . $seller_domestic_vat_number; ?>
				</div>
				
			</div>
		</div>

		<!--filo_subject place 1 - under seller data-->
		<div id="filo_subject">
			<div class="filo_headline_2"><?php _ex( $post_type_name, 'filo_doc', 'filo_text' ); ?></div>
			
			<?php wsl_log(null, 'document-head-data.php $draft_text: ' . wsl_vartotext($draft_text)); ?>
			
			<?php /*if ($draft_text !='' ) { ?>
				<div class="filo_draft_wrapper">
					<div class="filo_draft" ><?php echo $draft_text; ?></div>
				</div>
			<?php }*/ ?>
		</div>
		
	
	</div>
	
	<div class="filo_head_data_column" id="column_2">

			<div class="filo_head_data_row" id="document_head_data">
				<div class="filo_headline"><?php printf( _x( '%s data', 'filo_doc', 'filo_text' ), _x( $post_type_name, 'filo_doc', 'filo_text' ) ); ?></div>
				
			</div>
			<div class="filo_head_data_row" id="document_number">
				<div class="filo_label"><?php printf( _x( '%s Number', 'filo_doc', 'filo_text' ), _x( $post_type_name, 'filo_doc', 'filo_text' ) ); ?></div>
				<div class="filo_value"><?php echo esc_html( $order->get_document_number() ); ?></div>
			</div>
	
			<!-- only at reverse invoice
			<div class="filo_head_data_row"  id="original_invoice_number">
				<div class="filo_label"><?php _ex( 'Original Invoice Number', 'filo_doc', 'filo_text' ); ?></div>
				<div class="filo_value"><?php echo 'xxx'; ?></div>
			</div>
			-->
			
			<div class="filo_head_data_row" id="creation_date">
				<div class="filo_label"><?php _ex( 'Creation Date', 'filo_doc', 'filo_text' ); ?></div>
				<div class="filo_value"><?php echo date_i18n( 'Y-m-d', strtotime( $order->get_creation_date() ) ); ?></div>
			</div>
			
			<?php if ( $doc_financial_data['document_data']['is_cancel_type'] ) { ?>
				<div class="filo_head_data_row" id="base_document_number">
					<div class="filo_label"><?php _ex( 'Original document', 'filo_doc', 'filo_text' ); ?></div>
					<div class="filo_value"><?php echo  $doc_financial_data['document_data']['base_document_number']; ?></div>
				</div>
			<?php } ?>
			
			<?php //if our doc is a sales document after the order in logical chronological order, then dispay the order number ?>
			<?php if ( in_array( $doc_financial_data['document_data']['document_type'], array('filo_sa_deliv_note', 'filo_sa_invoice') ) ) { ?>
				<div class="filo_head_data_row" id="base_order_number">
					<div class="filo_label"><?php _ex( 'Order Number', 'filo_doc', 'filo_text' ); ?></div>
					<div class="filo_value"><?php echo  $doc_financial_data['document_data']['base_order_number']; ?></div>
				</div>
			<?php } ?>
			

			<?php wc_get_template( 'documents/document-' . $order->get_doc_type() . '-data.php', 
								array( 'order' => $order ),
								FILO()->template_path(), 
								FILO()->plugin_path() . '/templates/' );
			?>
		
	</div>
	
	<!--filo_subject place 2 - under the whole header, seller data and document data-->
		
</div>

