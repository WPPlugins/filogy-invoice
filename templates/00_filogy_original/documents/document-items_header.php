<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Items Header Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */


?>

<div class="filo_document_items"> <!--filo_doc_section-->
	
	<table id="doc_items_table" class="filo_table" > <!--style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee"-->
		<?php if ( $document_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values ?>
			<thead>
				<tr class="classic_formats">
					<th id="description_header" class="filo_table_headline" ><?php _ex( 'Description', 'filo_doc', 'filo_text' ); ?></th> 
					<th id="vat_rates_header" class="filo_table_headline" ><?php _ex( 'VAT Rates', 'filo_doc', 'filo_text' ); ?></th>				
					<th id="qty_header" class="filo_table_headline" ><?php _ex( 'Qty', 'filo_doc', 'filo_text' ); ?></th>
					<th id="unit_price_header" class="filo_table_headline" ><?php _ex( 'Unit Price', 'filo_doc', 'filo_text' ); ?></th>
					<th id="net_amount_header" class="filo_table_headline" ><?php _ex( 'Net Amount', 'filo_doc', 'filo_text' ); ?></th>
				</tr>
			</thead>
		<?php } elseif ( $document_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_contains_tax_values ?>
			<thead>
				<tr class="detailed_format">
					<th id="description_header" class="filo_table_headline" ><?php _ex( 'Description', 'filo_doc', 'filo_text' ); ?></th>
					<th id="qty_header" class="filo_table_headline" ><?php _ex( 'Qty', 'filo_doc', 'filo_text' ); ?></th>
					<th id="unit_price_header" class="filo_table_headline" ><?php _ex( 'Unit Price', 'filo_doc', 'filo_text' ); ?></th>								 
					<th id="net_amount_header" class="filo_table_headline" ><?php _ex( 'Net Amount', 'filo_doc', 'filo_text' ); ?></th>
					<th id="vat_rates_header" class="filo_table_headline" ><?php _ex( 'VAT Rates', 'filo_doc', 'filo_text' ); ?></th>				
					<th id="vat_amount_header" class="filo_table_headline" ><?php _ex( 'VAT Amount', 'filo_doc', 'filo_text' ); ?></th>
					<th id="gross_amount_header" class="filo_table_headline" ><?php _ex( 'Gross Amount', 'filo_doc', 'filo_text' ); ?></th>
				</tr>
			</thead>
			
		<?php } ?>
		
		<tbody>
			<?php 
			
				$items = array();
				$items['line_item'] = $line_items          = $order->get_items( 'line_item' );
				$items['shipping'] = $line_items_shipping = $order->get_items( 'shipping' );
				$items['fee'] = $line_items_fee      = $order->get_items( 'fee' );
				
				foreach ( $items as $item_type => $item ) {
					
					wc_get_template( 'documents/document-items.php', 
									array(
										'order'                 => $order,
										'items'                 => $item,
										'show_download_links'   => $order->is_download_permitted(), // 1: $show_download_links,
										'show_sku'              => true, // 2: $show_sku,
										'show_purchase_note'    => $order->has_status( 'processing' ), // 3: $show_purchase_note,
										'show_image'            => $show_image,
										'image_size'            => $image_size,
										'item_type'             => $item_type,
										'document_settings' => $document_settings,
										'doc_financial_data'	=> $doc_financial_data,
										'actual_template_key' => $actual_template_key,
										'filo_document_templates' => $filo_document_templates, 
									),
									$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
									$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
					);
				} 
			?>
			
		</tbody>
		
		<tfoot>
			
			<?php 
			
			if ( in_array( $document_settings['pdf_gen_doc_format'], array('extra_lines', 'detailed') ) ) { //pdf_shipping_fees_place == shipping_fees_in_item_lines
				
				$disp_line_total_net_without_rounding_dif = $doc_financial_data['lines_total']['line_total_net'];
				$disp_line_total_tax_without_rounding_dif = $doc_financial_data['lines_total']['line_total_tax'];
				$disp_line_total_gross_without_rounding_dif = $doc_financial_data['lines_total']['line_total_gross'];
				
				$disp_rounding_difference_total_net = $doc_financial_data['rounding_difference']['total_net'];
				$disp_rounding_difference_total_tax = $doc_financial_data['rounding_difference']['total_tax'];
				$disp_rounding_difference_total_gross = $doc_financial_data['rounding_difference']['total_gross'];
	
				$disp_line_total_net = $disp_line_total_net_without_rounding_dif + $disp_rounding_difference_total_net;
				$disp_line_total_tax = $disp_line_total_tax_without_rounding_dif + $disp_rounding_difference_total_tax;
				$disp_line_total_gross = $disp_line_total_gross_without_rounding_dif + $disp_rounding_difference_total_gross;
				
				
			} elseif ( $document_settings['pdf_gen_doc_format'] == 'classic' ) { //pdf_shipping_fees_place == shipping_fees_in_summarys_lines
	
				$disp_line_total_net = $doc_financial_data['line_types_total']['line_item']['line_total_net'];
				$disp_line_total_tax = $doc_financial_data['line_types_total']['line_item']['line_total_tax'];
				$disp_line_total_gross = $doc_financial_data['line_types_total']['line_item']['line_total_gross'];
				
			}
	
			
			if ( $document_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values	?>
		
				<tr class="filo_summary_line" id="order_subtotal_row">
					<td></td>
					<td id="order_subtotal_cell" class="filo_table_cell" colspan="3" ><?php echo _x('Subtotal', 'filo_doc', 'filo_text') . ':'; ?></td> <!-- class="filo_table_subheader_cell"-->
					<td id="total_net_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_line_total_net, $currency ); ?></td>
				</tr>
			
			<?php } elseif 	( $document_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_contains_tax_values ?> 
	
				<?php if ( isset( $doc_financial_data['rounding_difference'])) { ?>
					<tr class="filo_summary_line" id="order_difference_row">
						<td></td>
						<td id="order_subheader_cell" class="filo_table_cell" colspan="2" ><?php echo _x('Rounding difference', 'filo_doc', 'filo_text') . ':'; ?></td> <!-- class="filo_table_subheader_cell"-->
						<td id="total_net_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_rounding_difference_total_net, $currency ); ?></td>
						<td id="vat_rates_cell" class="filo_table_cell" >&nbsp;</td>
						<td id="vat_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_rounding_difference_total_tax, $currency ) ?></td>
						<td id="total_gross_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_rounding_difference_total_gross, $currency ); ?></td>								
					</tr>
				<?php } ?>
		
				<tr class="filo_summary_line" id="order_subtotal_row">
					<td></td>
					<td id="order_subheader_cell" class="filo_table_cell"  colspan="2" ><?php echo _x('Subtotal', 'filo_doc', 'filo_text') . ':'; ?></td> <!-- class="filo_table_subheader_cell"-->
					<td id="total_net_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_line_total_net, $currency ); ?></td>
					<td id="vat_rates_cell" class="filo_table_cell" >&nbsp;</td>
					<td id="vat_amount_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_line_total_tax, $currency ) ?></td>
					<td id="total_gross_cell" class="filo_table_cell filo_num" ><?php echo wc_price( $disp_line_total_gross, $currency ); ?></td>								
				</tr>
			
			<?php } 			
	
	
			?>
			<!--
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			-->		
			<?php
			
				//in classic case, shipping and fee linse are added to item summary lines
				if ( $document_settings['pdf_gen_doc_format'] == 'classic' ) {
					$doc_sum_lines = $order->get_doc_sum_lines( $doc_financial_data, array('shipping','fee') );
				} else {
					$doc_sum_lines = array();
				}
	
				wsl_log(null, 'document-items_header.php $doc_sum_lines: ' . wsl_vartotext($doc_sum_lines));
	
				// Display tax lines in case of classic and extra lines format
				// and display discount and total 
				$display_tax_lines = $document_settings['pdf_gen_doc_format'] == 'detailed' ? false : true; 
				$display_discont_and_total_lines = true; 
				$format_numbers = false;
				$doc_item_taxtotal = $order->get_doc_item_taxtotal('', $display_tax_lines, $display_discont_and_total_lines, $format_numbers ); //'pdf_item_complexity == items_without_tax_values
	
				
				wsl_log(null, 'document-items_header.php $doc_item_taxtotal: ' . wsl_vartotext($doc_item_taxtotal));			
				
				$totals = array_merge($doc_sum_lines, $doc_item_taxtotal);
			
				if (isset( $totals ) && is_array( $totals ) ) {
	
					foreach ( $totals as $total ) {
	
						?>
						<tr class="filo_summary_line" id="<?php echo $total['class'] . '_row'; ?>">
							
							<?php if ( $document_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values {	?>
	
								<td></td>			
								<td class="filo_summary_line_label filo_table_cell" id="<?php echo $total['class']; ?>" colspan="3"><?php echo $total['label']; ?></td>
								<td class="filo_summary_line_value filo_table_cell filo_num" id="<?php echo $total['class']; ?>"><?php echo wc_price( $total['value'], $currency); ?></td>
							
							<?php } elseif ( $document_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_contains_tax_values { ?>
	
								<td></td>			
								<td class="filo_summary_line_label filo_table_cell" id="<?php echo $total['class']; ?>" colspan="5"><?php echo $total['label']; ?></td>
								<td class="filo_summary_line_value filo_table_cell filo_num" id="<?php echo $total['class']; ?>"><?php echo wc_price( $total['value'], $currency); ?></td>
							
							<?php } ?>
													
						</tr><?php
					}
				}
			?>
	
		</tfoot>
	</table>

	<?php
		// Tax report lines under total line to detailed list
		if ( $document_settings['pdf_gen_doc_format'] == 'detailed' ) { 
			
			// Display tax lines in case of detailed format, under the Total line
			$display_tax_lines = true; 
			$display_discont_and_total_lines = false; 
			$format_numbers = false;
			$tax_detail_lines = $order->get_doc_item_taxtotal('', $display_tax_lines, $display_discont_and_total_lines, $format_numbers ); //'pdf_item_complexity == items_without_tax_values
			
	
			
			wsl_log(null, 'document-items_header.php $document_settings: ' . wsl_vartotext($document_settings));
			wsl_log(null, 'document-items_header.php count( $tax_detail_lines ): ' . wsl_vartotext(count( $tax_detail_lines )));
			
			//if exists tax detail line and is enabled
			if (isset( $tax_detail_lines ) && is_array( $tax_detail_lines ) && count( $tax_detail_lines ) > 0 
				&& $document_settings['enable_tax_detail_lines'] == 'yes' ) {
			?>
	
				<table id="tax_detail_table" class="filo_table" >
		
					<thead>			
						<tr>
							<th id="filo_summary_line_empty_column_header" class="filo_headline" ></th>
							<th id="filo_summary_line_label_header" class="filo_headline" ><?php _ex( 'VAT Rates', 'filo_doc', 'filo_text' ); ?></th>
							<th id="filo_summary_line_value_header" class="filo_headline" ><?php _ex( 'VAT Totals', 'filo_doc', 'filo_text' ); ?></th>
						</tr> 
					</thead>
		
					<tbody>		
		
						<?php
						foreach ( $tax_detail_lines as $tax_detail_line ) {
						?>
			
				
							<tr class="filo_summary_line" id="tax_detail_row">
								
								<td></td>
								<td class="filo_table_cell" id="<?php echo $tax_detail_line['class']; ?>" ><?php echo $tax_detail_line['label']; ?></td>
								<td class="filo_table_cell filo_num" id="<?php echo $tax_detail_line['class']; ?>"><?php echo wc_price( $tax_detail_line['value'], $currency); ?></td>
				
							</tr>
							<?php
						}?>

						<?php if ( ! empty($doc_financial_data['tax_summary_rounding_difference']['total_tax']) and $doc_financial_data['tax_summary_rounding_difference']['total_tax'] != 0 ) { ?> 
							<tr class="filo_summary_line" id="tax_summary_rounding_difference_row">
								
								<td></td>
								<td class="filo_table_cell" id="<?php echo $tax_detail_line['class']; ?>" ><?php echo _x('Rounding difference', 'filo_doc', 'filo_text') . ':'; ?></td>
								<td class="filo_table_cell filo_num" id="<?php echo $tax_detail_line['class']; ?>"><?php echo wc_price( $doc_financial_data['tax_summary_rounding_difference']['total_tax'], $currency); ?></td>
				
							</tr>
						<?php } ?>

						
						<tr style="height: 0px;">
							<td></td>
							<td class="closing_line_cell"></td>
							<td class="closing_line_cell"></td>
						</tr>
	
					</tbody>				
			</table>
	
			<?php
			}
		}
	?>

</div>
