<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Items Header Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

		 
$widget_content['FILO_Widget_Invbld_Line_Item_Name'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Item_Name">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Item_Name">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="description_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'Description', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';

$widget_content['FILO_Widget_Invbld_Line_Qty'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Qty">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Qty">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="qty_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'Qty', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';

$widget_content['FILO_Widget_Invbld_Line_Unit_Total_Net'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Unit_Total_Net">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Unit_Total_Net">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell " id="unit_price_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'Unit Price', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';

$widget_content['FILO_Widget_Invbld_Line_Total_Net'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Net">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Net">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="net_amount_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'Net Amount', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';
	
$widget_content['FILO_Widget_Invbld_Line_Tax_Labels'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Tax_Labels">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Tax_Labels">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="tax_label_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'VAT Rates', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';

	

//if ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_without_tax_values
$widget_content['FILO_Widget_Invbld_Line_Total_Tax'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Tax">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Tax">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="vat_amount_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'VAT Amount', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';

$widget_content['FILO_Widget_Invbld_Line_Total_Gross'] = '
	<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Gross">
		<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Gross">
			<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
			<div class="filo_table_cell" id="gross_amount_cell">
				
				<div class="filo_headline filo_h3 filo_widget_part">'
				
					. _x( 'Gross Amount', 'filo_doc', 'filo_text' ) . '
					
				</div>
				
			</div>
		</div>
	</div>';
		
	//}

?>

<div class="panel-fullwidth-grid-wrapper panel-table-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-invoice_items">
	<div class="panel-table-grid" id="invoice_items-grid">
		<div class="panel-table" id="invoice_items"> <!--filo_document_items-->
			<div class="panel-grid Filo_Item_Table_Header" id="invoice_items-header">
		
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
			 
				wc_get_template( 'documents/document-items.php', 
								array(
									'order'                 => $order,
									'document_settings' 	=> $document_settings,
									'doc_customizer_template_settings' => $doc_customizer_template_settings,
									'doc_financial_data'	=> $doc_financial_data,
									'actual_template_key' => $actual_template_key,
									'filo_document_templates' => $filo_document_templates, 
								),
								$filo_document_templates[$actual_template_key]['template_path'], //FILO()->template_path(), 
								$filo_document_templates[$actual_template_key]['default_path'] //FILO()->plugin_path() . '/templates/' //$default_path until includes subdir, this is important to locate WC it not from WC own plugin path
				);
			?>
				
				
			
		
			<?php		
			
			$footer_line_ids = do_shortcode( '[filogy_doc "footer_line_ids"]' );
		
			//convert comma separated list to array
			$footer_line_id_array = explode(', ', $footer_line_ids); //array(341, 342, 343);
			
			foreach ( $footer_line_id_array as $footer_line_id ) {
						
				$footer_line_label = do_shortcode('[filogy_doc "footer_lines" "' . $footer_line_id . '" "label"]') ;
				
				//var_dump($doc_customizer_template_settings['item_table_footer_label_column']);
				//var_dump($doc_customizer_template_settings);
				
				$is_footer_label = false;
				if ( ! isset($doc_customizer_template_settings['item_table_footer_label_column']) or $doc_customizer_template_settings['item_table_footer_label_column'] == 'FILO_Widget_Invbld_Line_Item_Name' ) {
					$content = $footer_line_label;
					$is_footer_label = true;
				} else {
					$content = '';
				}
				$widget_content['FILO_Widget_Invbld_Line_Item_Name'] = '
					<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Item_Name">
						<div class="so-panel widget FILO_Widget_Invbld_Line_Item_Name">
							<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
							<div class="filo_table_cell" id="description_cell">
								
								<div class="filo_content filo_widget_part">'
									
									. $content . '
									
								</div>
								
							</div>
						</div>
					</div>';
		
				$is_footer_label = false;
				if ( $doc_customizer_template_settings['item_table_footer_label_column'] == 'FILO_Widget_Invbld_Line_Qty' ) {
					$content = $footer_line_label;
					$is_footer_label = true;
				} else {
					$content = '';
				}	
				$widget_content['FILO_Widget_Invbld_Line_Qty'] = '
					<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Qty">
						<div class="so-panel widget FILO_Widget_Invbld_Line_Qty">
							<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
							<div class="filo_table_cell ' . ($is_footer_label ? '' : 'filo_num') . '" id="qty_cell">
								
								<div class="filo_content filo_widget_part">'
		
									. $content . '
		
								</div>
								
							</div>
						</div>
					</div>';
					
				wsl_log(null, 'document-items_header.php $widget_content[FILO_Widget_Invbld_Line_Qty]: ' . wsl_vartotext($widget_content['FILO_Widget_Invbld_Line_Qty']));
		
				$is_footer_label = false;
				if ( $doc_customizer_template_settings['item_table_footer_label_column'] == 'FILO_Widget_Invbld_Line_Unit_Total_Net' ) {
					$content = $footer_line_label;
					$is_footer_label = true;
				} else {
					$content = '';
				}		
				$widget_content['FILO_Widget_Invbld_Line_Unit_Total_Net'] = '
					<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Unit_Total_Net">
						<div class="so-panel widget FILO_Widget_Invbld_Line_Unit_Total_Net">
							<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
							<div class="filo_table_cell ' . ($is_footer_label ? '' : 'filo_num') . '" id="unit_price_cell">
								
								<div class="filo_content filo_widget_part">'
		
									. $content . '
		
								</div>
								
							</div>
						</div>
					</div>';
					
				$is_footer_label = false;
						
				$widget_content['FILO_Widget_Invbld_Line_Total_Net'] = '
					<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Total_Net">
						<div class="so-panel widget FILO_Widget_Invbld_Line_Total_Net">
							<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
							<div class="filo_table_cell filo_num" id="net_amount_cell">
								
								<div class="filo_content filo_widget_part">'
		
									. do_shortcode('[filogy_doc format="currency" "footer_lines" "' . $footer_line_id . '" "line_total_net"]') . '
		
								</div>
								
							</div>
						</div>
					</div>';

				$is_footer_label = false;
				if ( $doc_customizer_template_settings['item_table_footer_label_column'] == 'FILO_Widget_Invbld_Line_Tax_Labels' ) {
					$content = $footer_line_label;
					$is_footer_label = true;
				} else {
					$content = '';
				}		
				$widget_content['FILO_Widget_Invbld_Line_Tax_Labels'] = '
					<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Line_Tax_Labels">
						<div class="so-panel widget FILO_Widget_Invbld_Line_Tax_Labels">
							<!--<div class="so-widget-filo-widget-invbld-line-item-name">-->
							<div class="filo_table_cell" id="tax_label_cell">
								
								<div class="filo_content filo_widget_part">'
								
									. $content . '
																		
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
		
									. do_shortcode('[filogy_doc format="currency" "footer_lines" "' . $footer_line_id . '" "line_total_tax"]') . '
		
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
		
									. do_shortcode('[filogy_doc format="currency" "footer_lines" "' . $footer_line_id . '" "line_total_gross"]') . '
		
								</div>
								
							</div>
						</div>
					</div>';
						
				//}
		
		
				?>
				<div class="panel-grid panel-grid Filo_Item_Table_Footer <?php echo do_shortcode('[filogy_doc "footer_lines" "' . $footer_line_id . '" "class"]');?>_row" id="invoice_items-<?php echo $footer_line_id; ?>">
					
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
		
			<?php } //end for ?>
		
		</div>
	</div>
</div>