<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Items Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

//get tax types (tax rates) used in an order e.g. arrays of GB-20% VAT, GB-3% VAT, ...
$order_taxes = $order->get_taxes();
$currency = array( 'currency' => $order->get_currency() );	
wsl_log(null, 'document-order-items.php $doc_financial_data: ' . wsl_vartotext($doc_financial_data));		

foreach ( $items as $item_id => $item ) :
	
	if ($doc_financial_data['lines'][$item_id]['line_total_net'] != 0 ) :
	
		wsl_log(null, 'document-order-items.php $item_type: ' . wsl_vartotext($item_type));
		
		wsl_log(null, 'document-order-items.php $item: ' . wsl_vartotext($item));
		//wsl_log(null, 'document-order-items.php get_order_item_display_metas: ' . wsl_vartotext(   $order->get_order_item_display_metas( $item_id )  ));
		
		
		if ( $item_type == 'line_item' or in_array( $document_settings['pdf_gen_doc_format'], array('extra_lines', 'detailed') ) ) : //pdf_shipping_fees_place == shipping_fees_in_item_lines
			
			$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			//$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product ); //display() is depricated
			
			?>
			<tr>
				<td id="description_cell" class="filo_table_cell" ><?php
		
					//Item name
					echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
		
					//SKU
					if ( $show_sku ) {
						if ( is_object( $_product ) and $_product->get_sku() ) {
							
							$sku = $_product->get_sku();
							 
							echo ' (#' . $sku . ')';
							
						}
						
					}
		
					//Product variation meta data (we need meta data only for product line items, e.g. for shipping items we don't need metas)
					/*if ( $item['type'] == 'line_item' and $item_meta->meta ) { 
						
						$item_meta_data = $item_meta->display( true, true ); //display() is depricated
						
						echo '<br/>' . nl2br( $item_meta_data );
					}*/
		
				?></td>
				<?php 
					
					// make <br> separated string from tax_labels array
					if (isset( $doc_financial_data['lines'][$item_id]['tax_labels'] ) && is_array( $doc_financial_data['lines'][$item_id]['tax_labels'] ) )
						$disp_order_item_tax_labels = implode( "<br>", $doc_financial_data['lines'][$item_id]['tax_labels'] );
	
				?>
		
				<?php if ( $document_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values ?>
						
					<td id="vat_rates_cell" class="filo_table_cell" ><?php echo $disp_order_item_tax_labels; ?></td>
					<td id="qty_cell" class="filo_table_cell filo_num" ><?php echo $doc_financial_data['lines'][$item_id]['line_qty']; ?></td>		
					<td id="unit_price_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['unit_total_net'], $currency); ?></td>
					<td id="net_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['line_total_net'], $currency); ?></td>
				
				<?php } elseif ( $document_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_contains_tax_values ?>
		
					<td id="qty_cell" class="filo_table_cell filo_num" ><?php echo $doc_financial_data['lines'][$item_id]['line_qty']; ?></td>		
					<td id="unit_price_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['unit_total_net'], $currency); ?></td>
					<td id="net_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['line_total_net'], $currency); ?></td>
					<td id="vat_rates_cell" class="filo_table_cell" ><?php echo $disp_order_item_tax_labels; ?></td>
					<td id="vat_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['line_total_tax'], $currency); ?></td>
					<td id="gross_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $doc_financial_data['lines'][$item_id]['line_total_gross'], $currency); ?></td>
				
				<?php } ?>		
				
			</tr>
		
			<?php if ( $show_purchase_note && is_object( $_product ) && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) : ?>
				<tr>
					<td colspan="3" style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo wpautop( do_shortcode( $purchase_note ) ); ?></td>
				</tr>
			<?php endif; ?>
			
		<?php 
		endif; 

	endif; 

endforeach; 
?>
