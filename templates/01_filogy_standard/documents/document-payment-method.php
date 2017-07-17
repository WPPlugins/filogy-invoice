<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Payment Method Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

//add_filter('filo_payment_method_data_html', 'filo_payment_method_data_html');

?>

<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Payment_Data_Row">
	<div class="panel-grid" id="Filo_Payment_Data_Row">
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Payment_Data" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Payment_Data" id="filo_payment_data">
			
				<div class="filogy_widget filogy_normal_widget" id="filo_payment_data">
			
					<div class="filo_headline filo_h2 filo_widget_part">
						<?php _ex( 'Payment method details', 'filo_doc', 'filo_text' ); ?>
					</div>
					
					<div class="filo_content filo_widget_part">
						<?php
							echo do_shortcode('
								<p>[filogy_doc "document_data" "payment_method_title" br=true]</p>
								[filogy_doc "document_data" "payment_method_data_html_clear" br=true]
							');
						?>
					</div>
			
				</div>
						
			</div>
		</div>
	</div>
</div>