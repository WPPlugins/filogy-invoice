<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Payment Method Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */
?>

<div class="panel-fullwidth-grid-wrapper" id="panel-fullwidth-grid-wrapper-Filo_Notes_Row">
	<div class="panel-grid" id="Filo_Notes_Row">
		<div class="panel-grid-cell cell-of-FILO_Widget_Invbld_Notes" id="column_1">
			<div class="so-panel widget FILO_Widget_Invbld_Notes" id="filo_notes">
			
				<div class="filogy_widget filogy_normal_widget" id="filo_notes">
			
					<div class="filo_headline filo_h2 filo_widget_part">
						<?php _ex( 'Notes', 'filo_doc', 'filo_text' ); ?>
					</div>
					
					<div class="filo_content filo_widget_part">
						<?php
							echo do_shortcode('<p>[filogy_doc "document_data" "notes"]</p>');
						?>
					</div>
			
				</div>
						
			</div>
		</div>
	</div>
</div>