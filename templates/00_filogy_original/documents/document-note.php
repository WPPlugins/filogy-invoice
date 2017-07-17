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

<div class="filo_notes"> <!--filo_doc_section-->

	<div class="filo_headline"><?php _ex( 'Notes', 'filo_doc', 'filo_text' ); ?></div>
	
	<?php
	
		$notes = $order->get_customer_order_notes();
		krsort($notes); //sort notes descending by key, it will be the chronological order
	
			foreach ($notes as $note) {
			
			?>
	
				<div class="filo_note">
					<?php
						//<br> tags are cleaned by format_string function unnecessarily
						//to prevent this, we replace <br> to [br] and after formatting, replace back from [br] to <br>
						$note_text = $note->comment_content;
						
						$note_text = str_replace( 
							array('<br>','<p>','</p>',"\n"), 
							array('[br]','[p]','[/p]','[br]'),
							$note_text);
						$note_text =  strip_tags( $document->format_string( $note_text ) ); //remove html elements (for example links for base document)
						$note_text = str_replace( 
							array('[br]','[p]','[/p]'),
							array('<br>','<p>','</p>'),							
							$note_text);
							
						echo $note_text;
						//echo strip_tags( $document->format_string( $note->comment_content) ); //remove html elements (for example links for base document)
					?>
				</div>
			
			<?php
	
		}
	
	?>
	
</div>	
