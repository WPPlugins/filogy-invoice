			<div class="filo_head_data_row" id="comletion_date">
				<div class="filo_label"><?php _ex( 'Completion Date', 'filo_doc', 'filo_text' ); ?></div>
				<div class="filo_value"><?php echo $compdate = $order->get_completion_date(); ?></div>
			</div>
			<div class="filo_head_data_row" id="due_date">
				<div class="filo_label"><?php _ex( 'Due Date', 'filo_doc', 'filo_text' ); ?></div>
				<div class="filo_value"><?php echo $duedate = $order->get_due_date(); ?></div>
			</div>
			
			<div class="filo_head_data_row" id="payment_method">
				<div class="filo_label"><?php _ex( 'Payment Method', 'filo_doc', 'filo_text' ); ?></div>
				<div class="filo_value"><?php echo $order->get_payment_method_title(); ?></div>
			</div>
