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
wsl_log(null, 'document-addresses.php $doc_financial_data: ' . wsl_vartotext( $doc_financial_data ));

//$filo_document_logo = get_option('filo_document_logo');
$filo_document_logo = $doc_financial_data['document_data']['filo_logo_url'];

//data table widgets are also normal widgets for handling tha general all widget attributes for them, that is why we applied filogy_data_table_widget AND filogy_normal_widget classes at the same time.

?>

<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Head_Row_1">
	<div class="panel-grid" id="Filo_Head_Row_1">
		
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Logo" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Logo" id="filo_logo">
	
				<div class="filogy_widget filogy_normal_widget">
					<div class="filo_content filo_widget_part">
	
						<?php if ( ! empty( $filo_document_logo ) ) { ?>
								<img src="<?php echo $filo_document_logo; ?>" alt="">
						<?php } else { ?>
								<img src="" alt="">
						<?php } ?>
						
					</div>
					
				</div>		
			
				
			</div>
		</div>
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Doc_Title" id="column_1">
			<!--filo_subject place 1 - under seller data-->
			<div class="so-panel widget FILO_Widget_Invbld_Doc_Title" id="filo_title">
	
				<div class="filogy_normal_widget" id="filo_doc_title">
					
					<div class="filo_content filo_h1 filo_widget_part">
						
						<?php echo do_shortcode('[filogy_doc "document_data" "document_type_short_name"]'); ?>
						
					</div>
					
				</div>
				
			</div>
			
		</div>
		
	</div>	
</div>

<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Addresses_Row">
	<div class="panel-grid" id="Filo_Addresses_Row"> <!--filo_doc_section-->
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Seller_Address" id="column_2">
			<div class="so-panel widget FILO_Widget_Invbld_Seller_Address" id="filo_seller">
				
				<div class="filo_address filogy_normal_widget" id="filo_seller_address">
					
					<div class="filo_headline filo_h2 filo_widget_part">
						<?php //echo $doc_financial_data['document_data']['shop_role_display_name']; ?>
						<?php
							echo do_shortcode('[filogy_doc "document_data" "shop_role_display_name"]'); //< !-- Seller / Customer -->
						?>
						
					</div>
					
					
					<div class="filo_content filo_widget_part">
						<?php //echo $order->get_formatted_seller_address( 'before_filo_start_order' ); ?>
						<?php //if ( $seller_vat_number != '' ) echo '<br>' . _x('VAT ID', 'filo_doc', 'filo_text') . ': ' . $seller_vat_number; ?>
						<?php //if ( $seller_domestic_vat_number != '' ) echo '<br>' . _x('VAT ID (domestic)', 'filo_doc', 'filo_text') . ': ' . $seller_domestic_vat_number; ?>
						<?php
							echo do_shortcode('
								[filogy_doc "formatted_seller_address" br=true]
								[filogy_doc "seller_address" "seller_vat_number" br=true]
								[filogy_doc "seller_address" "seller_domestic_vat_number" br=true]
							');
						?>
							
					</div>
					
				</div>
				
			</div>
		</div>		
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Billing_Address" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Billing_Address" id="filo_billing_address">
			
				<div class="filo_address filogy_normal_widget" id="filo_billing_address">
			
					<div class="filo_headline filo_h2 filo_widget_part">
						<?php //echo $doc_financial_data['document_data']['partner_role_display_name']; ?>
						<?php
							echo do_shortcode('[filogy_doc "document_data" "partner_role_display_name"]'); //< !-- Seller / Customer -->
						?>
					</div>
					
					<div class="filo_content filo_widget_part">
						<?php //echo $order->get_formatted_billing_address(); ?> 
						<?php //wsl_log(null, 'document-addresses.php $order->get_formatted_billing_address(): ' . wsl_vartotext( $order->get_formatted_billing_address() )); ?>
						<?php
							echo do_shortcode('[filogy_doc "formatted_billing_address" br=true]');
						?>
					</div>
			
				</div>
						
			</div>
		</div>
	</div>	
</div>
<!--filo_subject place 2 - under the whole header, seller data and document data-->
