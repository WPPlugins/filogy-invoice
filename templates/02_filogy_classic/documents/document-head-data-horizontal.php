<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Head Data Template
 *
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

?>

<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Data_Horizontal">
	<div class="panel-grid" id="Filo_Data_Horizontal">
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Head_Data_Horizontal" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Head_Data_Horizontal">
	
				<div class="so-widget-filo-widget-invbld-head-data-horizontal">
					
					<!-- A header and a data row, values are side by side -->		
					<div class="filogy_widget filogy_data_table_widget filogy_normal_widget" id="filo_head_data_horizontal">
				
						<!-- <div class="filo_headline filo_h2 filo_widget_part"> -->
							<?php //echo do_shortcode('[filogy_doc "document_data" "document_type_short_name"]') . ' Data'; ?>						
						<!-- </div> -->
			
							<?php
							
							echo do_shortcode('
								<!-- A header and a data row, values are side by side -->
								<div class="filogy-table filogy_data_table filogy_horizontal_data_table">
								
									<!-- Table head row -->
									<div class="filogy-table-row table_label-row">
										<div class="filogy-table-cell table_label document_number_cell">[filogy_doc "document_data" "document_type_short_name"] Number</div>
										
										<div class="filogy-table-cell table_label creation_date_short_cell">' . __( 'Creation Date', 'filoinvbld_text') . '</div>
										
										[filogy_doc_show_if "document_data" "is_cancel_type"]
											<div class="filogy-table-cell table_label base_document_number_cell">' . __( 'Original document', 'filoinvbld_text') . '</div>
										[/filogy_doc_show_if]
										
										[filogy_doc_show_if "document_data" "base_order_number" hide_error=true]
											<div class="filogy-table-cell table_label base_order_number_cell">' . __( 'Order Number', 'filoinvbld_text') . '</div>
										[/filogy_doc_show_if]
										
										[filogy_doc_show_if "is_invoice"]
											<div class="filogy-table-cell table_label completion_date_cell">' . __( 'Completion Date', 'filoinvbld_text') . '</div>
											<div class="filogy-table-cell table_label due_date_cell">' . __( 'Due Date', 'filoinvbld_text') . '</div>	
											<div class="filogy-table-cell table_label payment_method_title_cell">' . __( 'Payment Method', 'filoinvbld_text') . '</div>
										[/filogy_doc_show_if]
									</div>
								
								
									<!-- Table data row -->	
									<div class="filogy-table-row table_value-row">
										<div class="filogy-table-cell table_value document_number_cell">[filogy_doc "document_data" "document_number"]</div>
										
										<div class="filogy-table-cell table_value creation_date_short_cell">[filogy_doc "document_data" "creation_date_short"]</div>
										
										[filogy_doc_show_if "document_data" "is_cancel_type"]
											<div class="filogy-table-cell table_value base_document_number_cell">[filogy_doc "document_data" "base_document_number"]</div>
										[/filogy_doc_show_if]
										
										[filogy_doc_show_if "document_data" "base_order_number" hide_error=true]
											<div class="filogy-table-cell table_value base_order_number_cell">[filogy_doc "document_data" "base_order_number"]</div>
										[/filogy_doc_show_if]
										
										[filogy_doc_show_if "is_invoice"]
											<div class="filogy-table-cell table_value completion_date_cell">[filogy_doc "document_data" "completion_date"]</div>
											<div class="filogy-table-cell table_value due_date_cell">[filogy_doc "document_data" "due_date"]</div>
											<div class="filogy-table-cell table_value payment_method_title_cell">[filogy_doc "document_data" "payment_method_title"]</div>
										[/filogy_doc_show_if]
										
									</div>
									
								</div>
							');
							?>	
					</div>
				</div>
			</div>
			
		</div>		
	</div>	
</div>