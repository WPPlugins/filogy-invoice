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
<div class="filo_addresses"> <!--filo_doc_section-->
		
	<div class="filo_customer_addresses">
	
		<div id="filo_billing_address" class="filo_address">
	
			<div class="filo_headline"><?php echo $doc_financial_data['document_data']['partner_role_display_name']; ?></div>
	
			<div class="filo_value"><?php echo $order->get_formatted_billing_address(); ?></div>
			<?php wsl_log(null, 'document-addresses.php $order->get_formatted_billing_address(): ' . wsl_vartotext( $order->get_formatted_billing_address() )); ?>
			
		</div>
	
		<div id="filo_shipping_address" class="filo_address">
	
			<div class="filo_headline"><?php _ex( 'Shipping address', 'filo_doc', 'filo_text' ); ?></div>
	
			<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
			
				<div class="filo_value"><?php echo $shipping; ?></div>
		
			<?php endif; ?>
	
		</div>
	</div>
		
</div>
<!--</div>-->
