<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Items Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

$line_item_ids = do_shortcode( '[filogy_doc "line_item_ids"]' );

//wsl_log(null, 'document-order-items.php $line_item_ids: ' . wsl_vartotext($line_item_ids));

//convert comma separated list to array
$line_item_id_array = explode(', ', $line_item_ids); //array(341, 342, 343);

foreach ( $line_item_id_array as $item_id ) :
	
	if ( isset($doc_financial_data['lines'][$item_id]['line_total_net']) and $doc_financial_data['lines'][$item_id]['line_total_net'] != 0 ) :
	
		$item_type = $doc_financial_data['lines'][$item_id]['item_type'];
		wsl_log(null, 'document-order-items.php $item_type: ' . wsl_vartotext($item_type));
		
		//wsl_log(null, 'document-order-items.php $item: ' . wsl_vartotext($item));
		//wsl_log(null, 'document-order-items.php get_order_item_display_metas: ' . wsl_vartotext(   $order->get_order_item_display_metas( $item_id )  ));
		
		
		if ( $item_type == 'line_item' or in_array( $doc_customizer_template_settings['pdf_gen_doc_format'], array('extra_lines', 'detailed') ) ) : //pdf_shipping_fees_place == shipping_fees_in_item_lines
	
			$widget_content['FILO_Widget_Invbld_Line_Item_Name'] = '		
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Item_Name">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Item_Name">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell" id="description_cell">
							
							<div class="filo_content filo_widget_part"> '
								
								. do_shortcode('
										[filogy_doc "lines" "' . $item_id . '" "item_name"]
										[filogy_doc "lines" "' . $item_id . '" "item_meta"]
									') . '

							</div>
							
						</div>
					</div>
				</div>';
		
			$widget_content['FILO_Widget_Invbld_Line_Qty'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Qty">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Qty">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell filo_num" id="qty_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc "lines" "' . $item_id . '" "line_qty"]') . '

							</div>
							
						</div>
					</div>
				</div>';
		
			$widget_content['FILO_Widget_Invbld_Line_Unit_Total_Net'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Unit_Total_Net">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Unit_Total_Net">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell filo_num" id="unit_price_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc format="currency" "lines" "' . $item_id . '" "unit_total_net"]') . '

							</div>
							
						</div>
					</div>
				</div>';
		
			$widget_content['FILO_Widget_Invbld_Line_Total_Net'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Net">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Net">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell filo_num" id="net_amount_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc format="currency" "lines" "' . $item_id . '" "line_total_net"]') . '

							</div>
							
						</div>
					</div>
				</div>';


			$widget_content['FILO_Widget_Invbld_Line_Tax_Labels'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Tax_Labels">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Tax_Labels">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell" id="net_amount_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc format="currency" "lines" "' . $item_id . '" "display_tax_labels"]') . '

							</div>
							
						</div>
					</div>
				</div>';
		
			//if ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_without_tax_values 
			$widget_content['FILO_Widget_Invbld_Line_Total_Tax'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Tax">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Tax">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell filo_num" id="vat_amount_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc format="currency" "lines" "' . $item_id . '" "line_total_tax"]') . '

							</div>
							
						</div>
					</div>
				</div>';
	
			$widget_content['FILO_Widget_Invbld_Line_Total_Gross'] = '
				<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Gross">
					<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Gross">
						<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
						<div class="filo_table_cell filo_num" id="gross_amount_cell">
							
							<div class="filo_content filo_widget_part">'
								
								. do_shortcode('[filogy_doc format="currency" "lines" "' . $item_id . '" "line_total_gross"]') . '

							</div>
							
						</div>
					</div>
				</div>';
				
				//}		
			
			?>

				<div class="panel-grid Filo_Item_Table_Body" id="invoice_items-<?php echo $item_id; ?>">
					 
					<?php
					
					if ( $doc_customizer_template_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values
					
						echo $widget_content['FILO_Widget_Invbld_Line_Item_Name'];
						echo $widget_content['FILO_Widget_Invbld_Line_Tax_Labels'];
						echo $widget_content['FILO_Widget_Invbld_Line_Qty'];
						echo $widget_content['FILO_Widget_Invbld_Line_Unit_Total_Net'];
						echo $widget_content['FILO_Widget_Invbld_Line_Total_Net'];
											
					} else {
					
						echo $widget_content['FILO_Widget_Invbld_Line_Item_Name'];
						echo $widget_content['FILO_Widget_Invbld_Line_Qty'];
						echo $widget_content['FILO_Widget_Invbld_Line_Unit_Total_Net'];
						echo $widget_content['FILO_Widget_Invbld_Line_Total_Net'];
						echo $widget_content['FILO_Widget_Invbld_Line_Tax_Labels'];
						if ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_without_tax_values
							echo $widget_content['FILO_Widget_Invbld_Line_Total_Tax'];
							echo $widget_content['FILO_Widget_Invbld_Line_Total_Gross'];
						}
						
					}
					?>			 
			
				</div>				
			
			<?php
			 
		endif; 

	endif; 

endforeach; 
?>
