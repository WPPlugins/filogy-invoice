/*global woocommerce_admin_meta_boxes, woocommerce_admin, accounting, woocommerce_admin_meta_boxes_order */
jQuery( function ( $ ) {

	/**
	 * Order Data Panel
	 */
	var wc_meta_boxes_order = { //COPY of original only with init_tiptip function
		init: function() {
			
			/* when save button of entire order is clicked */
			$( '#woocommerce-order-actions' )
				//.on( 'click', '.button.save_order', this.confirm_save_order ) //ADD RaPe
			;
			
		},

		init_tiptip: function() { //COPY of original
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			});
		},
		
		confirm_save_order: function(e) {  //ADD RaPe
			if ( ! confirm( 'Are you sure that you wish to validate this financial document? The document cannot be modified furthermore and this action cannot be undone.' ) ) {
				e.preventDefault();      
			}			
			return;
		},
		

	};

	/**
	 * Order Items Panel
	 */
	var wc_meta_boxes_order_items = {
		init: function() {
			//this.stupidtable.init();
			
			console.log('wc_meta_boxes_order_items 0');
			
			var is_filo_save_clicked = false;
			
			$( '#woocommerce-order-items' )
			
				.on( 'click', 'button.add-order-fee-finaline', this.add_order_fee_finaline_filo ) //ADD RaPe
				
				//NOT WC3
				//.on( 'click', 'button.save-action-filo', function() { 
				//	is_filo_save_clicked = true; 
				//	$( 'button.save-action' ).click(); 
				//} ) //ADD RaPe
				
				//NOT WC3
				//.on( 'save_line_items_ajax_finished', function() { 
				//	if (is_filo_save_clicked) {
				//		$( 'button.calculate-tax-action' ).click(); 
				//		is_filo_save_clicked = false;
				//	}
				//} )
				
				//NOT WC3
				//.on( 'calculate_tax_ajax_finished', function() { 
				//	$( 'button.calculate-action' ).click(); 
				//} )
				
				//WC3 ok
				// Check if WC Updated
				.on( 'change', 'input.line_total', this.line_total_changed )
				.on( 'change', 'input.line_subtotal', this.line_total_changed )
				
				//WC3 ok
				// Check if WC Updated
				.on( 'click', 'button.add-line-item', this.disable_doc_save)				
				.on( 'click', 'a.edit-order-item', this.disable_doc_save)
				.on( 'click', 'a.delete-order-item', this.disable_doc_save)
				
				//WC3 ok
				// Check if WC Updated
				.on( 'click', 'button.cancel-action', this.enable_doc_save)
				.on( 'click', 'button.save-action', this.enable_doc_save)
				//.on( 'click', 'button.save-action-filo', this.enable_doc_save)
				
				// Check if WC Updated
				.on( 'click', 'a.delete-order-item', this.release_commited_ordered_qty )

				//wc3 ok
				// Check if WC Updated
				// after save items, a total recalculation is needed (it includes tax recalculation), WC does not recalculate it automatically 
				.on( 'save_line_items_ajax_finished', function() { // not items_saved event, because it is not wait until ajax success 
					//console.log('item_saved 1');
					$( 'button.calculate-action' ).click();
				} )
				
				//wc3 ok
				// Check if WC Updated
				// after cancel recalculation is also needed because in case of an item is deleted, it will not be canceled, thus the total has to be changed after cancel 				
				.on( 'items_canceled', function() {  
					//console.log('items_canceled 1');
					$( 'button.calculate-action' ).click();
				} )
				;

		},

		//copy of meta-boxes-order.js - block: function() 		
		block: function() {
			$( '#woocommerce-order-items' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		//copy of meta-boxes-order.js - unblock: function()
		unblock: function() {
			$( '#woocommerce-order-items' ).unblock();
		},
		
		
		add_order_fee_finaline_filo: function() {  //ADD RaPe
			// based on meta-boxes-order.js - add_fee: function()
			// Check if WC Updated
			//WC3
			
			var data = {
				action:   'woocommerce_add_order_fee_finaline',
				order_id: woocommerce_admin_meta_boxes.post_id,
				dataType : 'json',
				security: woocommerce_admin_meta_boxes.order_item_nonce
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				if ( response.success ) {
					$( 'table.woocommerce_order_items tbody#order_fee_line_items' ).append( response.data.html );
				} else {
					window.alert( response.data.error );
				}
				wc_meta_boxes_order_items.unblock();
			});
			

			return false;
		},

		// When order item is deleted, then release inventory commited/ordered qty
		release_commited_ordered_qty: function() {  //ADD RaPe
			
			console.log('release_commited_ordered_qty 43254325');

			var item = $( this ).closest( 'tr.item, tr.fee, tr.shipping' );
			var item_id = item.attr( 'data-order_item_id' );

			var data = {
				item_id:		item_id,
				document_id:	woocommerce_admin_meta_boxes.post_id,
				action:			'filo_release_deleted_item_commited_ordered_qty',
				security:		woocommerce_admin_meta_boxes.order_item_nonce
			};

			$.ajax({
				url:     woocommerce_admin_meta_boxes.ajax_url,
				data:    data,
				type:    'POST',
				success: function() {
				}
			});

			return false;
			
		},	
		
		save_action_filo: function() {  //ADD RaPe		
		//do save, calc tax and calc total together
			
			//addOrderItemsLoading();
			//console.log('save_action_filo');
			$( 'button.save-action' ).click();

			return false;
			
		},

		// When order item block is in 'edit mode'
		disable_doc_save: function() {  //ADD RaPe
			
			console.log('.wc-order-add-item block is now visible');
		      
			//Disable Save and Save Draft buttons
			$('input.save_order[type="submit"]').attr('disabled','disabled');
			$('input.save_draft_document[type="submit"]').attr('disabled','disabled');
		      
		      
			//Disable generate pdf link button, by removing and saving href attribute
			//http://stackoverflow.com/questions/970388/jquery-disable-a-link
			$('a.filo_generate_pdf').data("href", $('a.filo_generate_pdf').attr("href")).removeAttr("href");
			
		},	

		// When order item block is NOT in 'edit mode'
		enable_doc_save: function() {  //ADD RaPe
			
			console.log('.wc-order-add-item block is hidden');
		      
			//Enable Save and Save Draft buttons
			$('input.save_order[type="submit"]').removeAttr('disabled');
			$('input.save_draft_document[type="submit"]').removeAttr('disabled');
		      
			//Re-enable generate pdf link button, by setting the saved href attribute
			$('a.filo_generate_pdf').attr("href", $('a.filo_generate_pdf').data("href"));
			
		},


		/**
		 * 
		 * Unit price is not handled on WooCommerce order items.
		 * After inserting a new product into an order, the qty is 1, and the price is the product price stored in the product master data * 1.
		 * When change the price, then change the qty, the price is calculated again by the qty multilplied by the original unit price of product master data.
		 * It is not the best solution, because I would like the result to be calculated using the price that I changed.
		 * 
		 * E.g Master data unit price is $8
		 * insert an item qty: 1, price: $8
		 * change the price: qty: 1, price: $5
		 * change the qty: qty: 2, price: $16!! - this is the original WC functionality
		 * 
		 * this alternative function for the last step:
		 * change the qty: qty: 2, price: $10 - this is the modified functionality ensured by this function
		 * 
		 * This function overwrite "original" prices when the user changes them.
		 * 
		 * The "original" values are stored as html attributes of the input fields: data-total, data-subtotal, data-qty.
		 * These html attributes of input fields should be overwritten, when line_total or line_subtotal fields is changed. 
		 */
		// Check if WC Updated
		line_total_changed: function() {  //ADD RaPe
			
			console.log('line_total_changed 0');
			
			if ( 'true' == $('#filo_advanced_line_total_change_handling').val() ) {
				
				console.log('line_total_changed 1');
			
				var $row = $( this ).closest( 'tr.item' );
				var line_total    = $( 'input.line_total', $row );					
				var line_subtotal    = $( 'input.line_subtotal', $row );
				var quantity    = $( 'input.quantity', $row );
				
				console.log($row);
				console.log(line_total.val());
				console.log(line_subtotal.val());
				console.log(quantity.val());
									
				$( 'input.line_total', $row ).attr('data-total', line_total.val());
				$( 'input.line_subtotal', $row ).attr('data-subtotal', line_subtotal.val());
				$( 'input.quantity', $row ).attr('data-qty', quantity.val());
				
			}
			
		}


	};

	wc_meta_boxes_order.init();
	wc_meta_boxes_order_items.init();
	
	//$('input.save_order[type="submit"]').attr('disabled','disabled');
	$( "button.add-line-item" ).on( "click", function() {});
	
});
