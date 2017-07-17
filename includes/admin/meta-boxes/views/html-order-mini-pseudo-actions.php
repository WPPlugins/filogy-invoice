<ul class="order_actions submitbox">

	<li class="wide" id="filo_pseudo_doc_actions">

		<a class="preview button filo_generate_pdf" href="<?php print wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $post->ID , 'filo_generate_pdf_' . $post->ID, 'filo_nonce') . '&filo_usage=doc_view&filo_pseudo_doc_type=' . $pseudo_doc_type; //ToDo RaPe ?>" target="_blank">
			<?php echo ! $is_pseudo_doc_valid ? __( 'Print Draft', 'filo_text' ) : sprintf( __( 'Print %s', 'filo_text' ), $pseudo_doc_type_name); ?>
		</a>
		
		<?php if ( ! $is_pseudo_doc_valid or $is_enabled_validated_doc_modification ) { ?>
			<input type="submit" class="button filo_last_button save_pseudo_doc_data tips" name="save_pseudo_doc_<?php echo $pseudo_doc_type; ?>" value="<?php printf( __( 'Save %s', 'filo_text' ), $pseudo_doc_type_name); ?>" data-tip="<?php _e( 'Save/update the draft document', 'filo_text' ); ?>" />
		<?php } ?>
		
	</li>

</ul>				

