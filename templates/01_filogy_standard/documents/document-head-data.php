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
//$post_type_name = $finadoc->get_doc_type_label_singular_short_name();
//$seller_vat_number = get_option('filo_seller_vat_number');
$seller_vat_number = $doc_financial_data['seller_address']['seller_vat_number'];
$seller_domestic_vat_number = $doc_financial_data['seller_address']['seller_domestic_vat_number'];

//$filo_document_logo = get_option('filo_document_logo');
$filo_document_logo = $doc_financial_data['document_data']['filo_logo_url'];

//data table widgets are also normal widgets for handling tha general all widget attributes for them, that is why we applied filogy_data_table_widget AND filogy_normal_widget classes at the same time.

?>
<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Head_Row_1">
	<div class="panel-grid" id="Filo_Head_Row_1"> <!--filo_doc_section-->
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
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Seller_Address" id="column_2">
			<div class="so-panel widget FILO_Widget_Invbld_Seller_Address" id="filo_seller">
				
				<div class="filogy_widget filo_address filogy_normal_widget" id="filo_seller_address">
					
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
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Head_Data_Vertical" id="column_3">
			<div class="so-panel widget FILO_Widget_Invbld_Head_Data_Vertical" class="filo_head_data">
	
				<div class="filogy_widget filogy_data_table_widget filogy_normal_widget" id="filo_head_data">
			
					<div class="filo_headline filo_h2 filo_widget_part">
						<?php echo do_shortcode('[filogy_doc "document_data" "document_type_short_name"]') . ' Data'; ?>
					</div>
					
					<div class="filo_content filo_widget_part">
						<?php
							echo do_shortcode('
								<!-- Label-Value pairs in rows -->
								<div class="filogy-table filogy_data_table filogy_vertical_data_table_row">
								
									<div class="filogy-table-row document_number_row">
										<div class="filogy-table-cell table_label">[filogy_doc "document_data" "document_type_short_name"] Number</div>
										<div class="filogy-table-cell table_value">[filogy_doc "document_data" "document_number"]</div>
									</div>
								
									<div class="filogy-table-row creation_date_short_row">
										<div class="filogy-table-cell table_label">' . __( 'Creation Date', 'filoinvbld_text') . '</div>
										<div class="filogy-table-cell table_value">[filogy_doc "document_data" "creation_date_short"]</div>
									</div>
								
								
									[filogy_doc_show_if "document_data" "is_cancel_type"]
										<div class="filogy-table-row base_document_number_row">
											<div class="filogy-table-cell table_label">' . __( 'Original document', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_value">[filogy_doc "document_data" "base_document_number"]</div>
										</div>
									[/filogy_doc_show_if]
								
								
									[filogy_doc_show_if "document_data" "base_order_number" hide_error=true]
										<div class="filogy-table-row base_order_number_row">
											<div class="filogy-table-cell table_label">' . __( 'Order Number', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_value">[filogy_doc "document_data" "base_order_number"]</div>
										</div>
									[/filogy_doc_show_if]
								
								
									[filogy_doc_show_if "is_invoice"]
										<div class="filogy-table-row completion_date_row">
											<div class="filogy-table-cell table_label">' . __( 'Completion Date', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_value">[filogy_doc "document_data" "completion_date"]</div>
										</div>
										
										<div class="filogy-table-row due_date_row">
											<div class="filogy-table-cell table_label">' . __( 'Due Date', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_value">[filogy_doc "document_data" "due_date"]</div>
										</div>
											
										<div class="filogy-table-row payment_method_title_row">
											<div class="filogy-table-cell table_label">' . __( 'Payment Method', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_value">[filogy_doc "document_data" "payment_method_title"]</div>
										</div>
										
									[/filogy_doc_show_if]
									
								</div>						
							');
						?>
					</div>
			
				</div>
						
			</div>
		</div>
	</div>	
</div>
<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Head_Row_2">
	<div class="panel-grid" id="Filo_Head_Row_2">
		
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Doc_Title" id="column_1">
			<!--filo_subject place 1 - under seller data-->
			<div class="so-panel widget FILO_Widget_Invbld_Doc_Title" id="filo_title">
	
				<div class="filogy_widget filogy_normal_widget" id="filo_doc_title">
					
					<div class="filo_content filo_h1 filo_widget_part">
						
						<?php echo do_shortcode('[filogy_doc "document_data" "document_type_short_name"]'); ?>
						
					</div>
					
				</div>
				
			</div>
			
		</div>
		
	</div>
</div>
<!--filo_subject place 2 - under the whole header, seller data and document data-->
		


