<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Addresses Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

?>

<!--<div class="filo_addresses">-->
<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Customer_Addresses_Row">	
	<div class="panel-grid" id="Filo_Customer_Addresses_Row"> <!--filo_doc_section-->
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Billing_Address" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Billing_Address" id="filo_billing_address">
			
				<div class="filogy_widget filo_address filogy_normal_widget" id="filo_billing_address">
			
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
	
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Shipping_Address" id="column_2">
			<div class="so-panel widget FILO_Widget_Invbld_Shipping_Address" id="filo_shipping_address">
				
				<div class="filogy_widget filo_address filogy_normal_widget" id="filo_shipping_address">
		
					<div class="filo_headline filo_h2">
						<?php _ex( 'Shipping address', 'filo_doc', 'filo_text' ); ?>
					</div>
	
					<div class="filo_content filo_widget_part">
						<?php
							echo do_shortcode('[filogy_doc "formatted_shipping_address" br=true]');
						?>
					</div
					>			
				</div>
		
			</div>
		</div>
			
	</div>
</div>
<!--</div>-->
