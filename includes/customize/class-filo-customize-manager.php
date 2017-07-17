<?php
/**
 * Filogy Document Customizer
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Manager {

	/**
	 * Customize Register
	 */
	static function filo_doc_customize_register( $wp_customize ) {
		
		wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $_GET: ' .  wsl_vartotext($_GET));
		//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $_SERVER: ' .  wsl_vartotext($_SERVER)); //big

		// filo_usage=doc get parameter sign that this customizer turn is specially for Filogy document customization
		// in case of sutomizer AJAX save, there is no $_GET parameter is given. For saving our settings, we have to execute this without any filo_usage parameter

		if ( strpos($_SERVER['HTTP_REFERER'], '/wp-admin/customize.php') ) {
			$is_customizer = true;
		} else {
			$is_customizer = 'halse'; //false causes error
		}
		
		// filo_usage=doc means that this is a filo document in customizer mode, and filo_usage=doc_vire means filo documents that is not in customizer
		// we have to register filo customization only if filo_usage=doc (this means customizatio), and do not need when filo_usage=doc_view (this means filo doc without customization) 
		if ( $is_customizer and (isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc') or defined( 'DOING_AJAX' )) {

			self::delete_skin();
			self::remove_other_customizer_elements( $wp_customize );
						
			wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register EXECUTED: ' .  wsl_vartotext( '' ));
			
			//add the following two GET parameters to preview url, because without this customizer does not know the ids, when the preview ajax load happenes
			
			//if customizer is called from WooCommerce settings page / Documents tab or SiteOrigin invoice builder editor
			$filo_sample_order_id = null;
			$url_query_params = array();
			if ( isset($_GET['filo_sample_order_id']) ) {
				$filo_sample_order_id = wc_clean( $_GET['filo_sample_order_id'] ); //+wc_clean
			}
				
			//we set empty value for filo_sample_order_id get parameter on WooCommerce document settings page, 
			//because when filo_sample_order_id option value is changed in customizer, the new sample should be displayed in customizer preview,
			//thus always query the option value instead of using of a previously set $_get parameter value.
			
			if ( empty($filo_sample_order_id) ) {
				//$filo_sample_order_id = get_option( 'filo_sample_order_id' );
				$filo_sample_order_id = FILO_Financial_Document::get_filo_sample_order_id_option();
				
			}
			
			//$url_query_params['filo_sample_order_id'] = $_GET['filo_sample_order_id'];
			$url_query_params['filo_sample_order_id'] = $filo_sample_order_id;
			//$filo_usage = 'doc'; //set filo_usage below
		
			
			$sample_order = wc_get_order( $filo_sample_order_id ); //filo_get_order
			//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $sample_order: ' .  wsl_vartotext($sample_order));
			
			//if customizer is called from SiteOrigin invoice builder editor, template id is set (id of SiteOrigin page) 
			if ( isset($_GET['filo_invoice_template_id']) ) {
				
				$invoice_template_id = wc_clean( $_GET['filo_invoice_template_id'] ); //+wc_clean
				
				$url_query_params['filo_invoice_template_id'] = $invoice_template_id; 
	
				//get link of invoice builder SO template page by the page_id 			
				$filo_invoice_template_permalink = get_permalink( $invoice_template_id );  //e.g. http://yoursite.com/filoinv_template/filoinv-template-1/
				
				//$filo_usage = 'doc'; //set filo_usage below
			}

			//Pass through filo_usage usage GET parameter (filo_usage=doc sign that this customizer turn is specially for Filogy document customization)
			if ( isset($_GET['filo_usage']) ) {
				$filo_usage = wc_clean( $_GET['filo_usage'] ); //+wc_clean
				$url_query_params['filo_usage'] = $filo_usage;
			}
			$url_query_params['filo_customizer'] = $is_customizer ? 'true' : 'false';
		
			//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_query_params: ' .  wsl_vartotext($url_query_params));
		
			$url_query_txt = http_build_query($url_query_params);
			
			//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_query_txt: ' .  wsl_vartotext($url_query_txt));
			//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $filo_invoice_template_permalink: ' .  wsl_vartotext($filo_invoice_template_permalink));
	 
	
			//$url_with_nonce = wp_nonce_url( FILOFW()->plugin_url() . '/includes/filo_generate_pdf.php' , 'filo_generate_pdf', 'filo_all_nonce') . '&doc_id=' . $post_id;
			
			//decide if we are came here from normal WC settings page or SiteOrigin invoice builder page
			if ( empty($filo_invoice_template_permalink) ) {
				
				// 1. Normal URL that is used by filogy templates (filogy template can also be the invoice builder template), this is when we call it from the Woocommerce settings page / Documents tab:
				//		e.g. http://webshoplogic.com/wp-content/plugins/filogy-framework/includes/filo_generate_pdf.php?doc_id=2944&
				
				
				//$url_with_nonce = wp_nonce_url( FILOFW()->plugin_url() . '/includes/filo_generate_pdf.php?doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce' ) . '&filo_usage=doc' . '&is_customiser=true';
				// Customizer cannot display /includes/filo_generate_pdf.php in the preview frame, thus we have to apply permalinks
				// this is the url of filo_generate_pdf.php, but we use the filo_finadoc tag and use rewrite rules to get the filo_generate_pdf.php file (as template).
				// rewrite_rules and template_include rules are applied here: filo-core-functions.php
				// htmlspecialchars_decode is needed for converting &amp; to &
				// BUT: We do not use permalinks (because it is not working if permalinks are not enabled on the site), 
				//      the plain link format is http://yoursite.com/?post_type=shop_order&p=123 (if publicly_queryable' => true in register_type)
				//$url_with_nonce = htmlspecialchars_decode( wp_nonce_url( get_site_url() . '/filo_finadoc?doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce') ) . '&filo_usage=doc' . '&is_customiser=true';
				
				// add_filter('template_include', 'filo_finadoc_template_include', 1, 1) is needed for this solution:
				//$url_with_nonce = htmlspecialchars_decode( wp_nonce_url( get_site_url() . '/?post_type=' . $sample_order->order_type . '&doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce') ) . '&filo_usage=doc' . '&is_customiser=true';												
				
				// Instead of the solution above, we use the filo_individual_page mechanism for displaying customizer preview page
				$url_with_nonce = htmlspecialchars_decode( wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce' ) . '&filo_usage=doc' . '&is_customiser=true' );				
				
				// the old url, that is cannot be used till wp 4.7: $url_with_nonce = htmlspecialchars_decode( wp_nonce_url( FILOFW()->plugin_url() . '/includes/filo_generate_pdf.php?doc_id=' . $filo_sample_order_id , 'filo_generate_pdf_' . $filo_sample_order_id, 'filo_nonce') ) . '&filo_usage=doc'; (it cannot be displayed in customizer preview, because main page of the site is displayed)
				
				wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_with_nonce: ' .  wsl_vartotext($url_with_nonce));
				
				//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_with_nonce 1: ' .  wsl_vartotext($url_with_nonce)); 
				
			} else {
				
				// 2. Invoice builder template (when invoice builder has to be called independently on the actually set filogy template), 
				//    this is when we use it from SiteOrigin editor page: 
				//		e.g. http://webshoplogic.com/filoinv_template/filoinv-template-1/?filo_invoice_template_id=2858
				//			filoinv-template-1 is the name of the page edited in SiteOrigin editor
				
				//$url_with_nonce = 'http://webshoplogic.com/filoinv_template/filoinv-template-1/?' .$url_query_txt;
				$url_with_nonce = $filo_invoice_template_permalink . '?' .$url_query_txt;
				
				//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_with_nonce 2: ' .  wsl_vartotext($url_with_nonce));
				
			}
	
			wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $url_with_nonce X: ' .  wsl_vartotext($url_with_nonce));
			// e.g. http://yoursite.com/?post_type=filo_pu_goods_rec&doc_id=73&filo_nonce=b6420cefd5&filo_usage=doc&is_customiser=true
	
			//set customizer preview url		
			$wp_customize->set_preview_url( $url_with_nonce );

			
			//Generate template panel data (so rows, widgets) for creating customizer panels and sections later. It can be generated from (1) SiteOrigin template panel data or using (2) definition of normal doc templates.
			
			//get template_panels_data
			$template_panels_data = self::get_template_panels_data();
			
			//wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_register $template_panels_data: ' . wsl_vartotext( $template_panels_data )); //VERY LARGE!
				
			FILO_Customize_Manager::add_panels($template_panels_data);
			
			FILO_Customize_Manager::add_color_palette_items();
			
			FILO_Customize_Manager::add_saving_option_fields();
			
			FILO_Customize_Manager::add_global_option_fields();
			
			FILO_Customize_Manager::add_template_custom_settings();
			
			//@based on class-egf-customize-manager.php as example
			//We have to register our custom control type to use JS content_template() in custom controls
			// 	(class-wp-customize-manager.php - render_control_templates() )
			//$wp_customize->register_control_type( 'FILO_Customize_Adv_Color_Control' );
			//$wp_customize->register_control_type( 'FILO_Customize_Fontselect_Control' );
			//$wp_customize->register_control_type( 'FILO_Customize_Header_Control' );
			
			$wp_customize->register_control_type( 'FILO_Customize_Nav_Menu_Item_Control' );
			
			// Change the title of top level customizer menu from the blogname to "Filogy Document Template"
			// Originally this title is displayed in customize.php: get_bloginfo( 'name' )
			// Option name is get in get_bloginfo of general-template.php: $output = get_option('blogname'); 
			// In option.php this filter can be used to change an option: apply_filters( 'option_' . $option, maybe_unserialize( $value ), $option );
			
			add_filter( 'option_' . 'blogname', function( $value ) { return $section_description = __( 'Filogy Document Template' , 'filo_text' ); }, 10, 1 );
			
		}	
	}
	
	/**
	 * Remove all customizer panels and controls that have been registered by WP Core, Theme or other plugins.
	 */
	static function remove_other_customizer_elements( $wp_customize ) {
		
		wsl_log(null, 'class-filo-customize-manager.php remove_other_customizer_elements 0: ' .  wsl_vartotext(''));
		
		
		/*
		// We do not remove settings, because during save of normal (not filogy) site customization "Setting does not exist or is unrecognized." error is displayed.
		// Regardless of this controls and sections can be removed below. 
		// That is why it is commented out.
		
		//remove_setting( $id )
		//settings()
		$settings = $wp_customize->settings();
		if ( isset($settings) and is_array($settings) )
		foreach ($settings as $key => $value) {
			//wsl_log(null, 'class-filo-customize-manager.php remove_other_customizer_elements settings $key: ' .  wsl_vartotext($key));
				
			// remove all settings, except 
			// - nav_menus_created_posts, because if ti is removed, the following JS error occures: Uncaught TypeError: Cannot set property '_value' of undefined ---> api( 'nav_menus_created_posts' )._value = []; ----> it causes problem in WP 4.7: $wp_customize->remove_setting( 'nav_menus_created_posts' );  https://wordpress.org/support/topic/wp-customizer-remove-nav_menus_created_posts-setting-problem/
			// - header_image_data, because after save, the button remains "Save & Plublish" and not chenge to "Saved"
			if ( strpos($key, 'nav_menus_created_posts') === false and strpos($key, 'header_image_data') === false ) { 
				
				//wsl_log(null, 'class-filo-customize-manager.php remove_other_customizer_elements REMOVED settings $key: ' .  wsl_vartotext($key));
				 
				$wp_customize->remove_setting( $key );
				
			}
		}
		*/
		
		//remove_control( $id )
		//controls()
		$controls = $wp_customize->controls();
		if ( isset($controls) and is_array($controls) )
		foreach ($controls as $key => $value) {

			wsl_log(null, 'class-filo-customize-manager.php remove_other_customizer_elements REMOVED controls $key: ' .  wsl_vartotext($key)); 

			$wp_customize->remove_control( $key );

		}
	
		//containers
		
		
		//remove_section( $id )
		//sections()
		$sections = $wp_customize->sections();
		if ( isset($sections) and is_array($sections) )
		foreach ($sections as $key => $value) {
			$wp_customize->remove_section( $key );
		}
		
		
		//remove_panel( $id )
		//panels()
		$panels = $wp_customize->panels();
		if ( isset($panels) and is_array($panels) )
		foreach ($panels as $key => $value) {
			$wp_customize->remove_panel( $key ); //@ was needed in front of function to suppress this warning: //Notice: WP_Customize_Manager::remove_panel was called incorrectly. Removing nav_menus manually will cause PHP warnings. Use the customize_loaded_components filter instead. (https://developer.wordpress.org/reference/hooks/customize_loaded_components/) Since we use disable_customizer_core_components filter, @ is not needed
		}

	}	


	/**
	 * check_if_customize_register_should_be_executed
	 * 
	 * Actions are called four times if we are in wp customizer
	 * That is why open customizer is slow if we have to apply a lot of settings.
	 * We use a temporary option to count the 1a, 1b, 2 and 3 loops.
	 * The first loop is not saved first (maybe it is executed concurrently), so we have two no 1. loops,
	 * the difference is if $_GET['return'] is set or not in the second no 1 loop. ($_GET['return'] is always set, except 2 'b' loop)
	 * 
	 * We need the first and that second loop, where the $_GET['return'] is not set. 
	 */
	function check_if_customize_register_should_be_executed() {
		
		wsl_log(null, 'class-filo-customize-manager.php check_if_customize_register_should_be_executed $_GET: ' .  wsl_vartotext($_GET));
		
		$filo_cust_reg_loopno_tmp = get_option('filo_cust_reg_loopno_tmp');
	
		//wsl_log(null, 'class-filo-customize-manager.php $filo_cust_reg_loopno_tmp A: ' .  wsl_vartotext($filo_cust_reg_loopno_tmp));
		
		//reset lopno if too mutch time (more than 60 seconds) has passed since the last loopno update (in case if it was not reseted for any reason)
		//(The loop no counts only the loops inside one customizing call, and if it is interrupted, thus not all the loops is finised and the counter is not reseted at the end, then at the last customizer call the counter has to be reseted.
		//That is why we reset the counter if more than 60 seconds passed since the last loopno update) 
	
		//initialize or reset $filo_cust_reg_loopno_tmp variable
		if ( empty($filo_cust_reg_loopno_tmp) or time() - get_option('filo_cust_reg_loopno_tmp_timest') > 60 ) {
			$filo_cust_reg_loopno_tmp = 0;
		}

		//increase the counter if return is set (thus the concurrent second loop, where return is not set is not a counted loop)
		if ( isset($_GET['return']) ) {	

			$filo_cust_reg_loopno_tmp += 1;
			
			update_option('filo_cust_reg_loopno_tmp', $filo_cust_reg_loopno_tmp);
			update_option('filo_cust_reg_loopno_tmp_timest', time() );
		
			//reset the option value in the last loop
			if ( $filo_cust_reg_loopno_tmp >= 3 ) {
				update_option('filo_cust_reg_loopno_tmp', null);
			}
			
		}

		//reset the option value if an AJAX save happaned, when there is just 1 loop
		if ( defined( 'DOING_AJAX' ) ) {
			wsl_log(null, 'class-filo-customize-manager.php check_if_customize_register_should_be_executed DOING_AJAX: ' .  wsl_vartotext(''));
			update_option('filo_cust_reg_loopno_tmp', null);
		}
		
	
		wsl_log(null, 'class-filo-customize-manager.php $filo_cust_reg_loopno_tmp X: ' .  wsl_vartotext($filo_cust_reg_loopno_tmp));
		wsl_log(null, 'class-filo-customize-manager.php $filo_cust_reg_loopno_tmp $_GET: ' .  wsl_vartotext($_GET));
	
		
		if (    $filo_cust_reg_loopno_tmp == 1    // Normal: We found experientially, that executing customize_register enought in loop no 1 (so in 1a and 1b) 
			 or $filo_cust_reg_loopno_tmp == 0) { // Transport Refresh: in case of transport refresh there is no $_GET['return'] thus loop no remains 0 			
			
			return true;
			
		} else {
			
			return false;
			
		}  

	}

	/**
	 * Add Panels
	 * 
	 * Add customizer panels (the highest level of customizer menu items) and some data for creating sections (the next level of menu items)
	 * Then call define_rows_sections function, that add sections for each panel, and partition of the sectors, and the settings of the partition.
	 * It is important, that we define all possible $selector_data for the sections/partions/settings of every panel (it is panel specific, because normal widgets and table form widgets, and other kind of widgets have different css selectors),
	 * then in define_rows_sections function can apply that selector that is defined for each setting (e.g. header_font_color setting will apply css_header_selector type selector of the panel)
	 * If a selector type is not defined for a panel, then those settings that used this tpye of undefined selector will not be displayed under this panel.
	 * 
	 */	
	static function add_panels( $template_panels_data, $for_css_render = false ) {
		global $wp_customize;
		
		//wsl_log(null, 'class-filo-customize-manager.php add_panels $template_panels_data 0: ' . wsl_vartotext( $template_panels_data )); //VERY LARGE!
		
		//***********************
		// PANELS
		//***********************
		
		$all_settings_data = array();


		//DOCUMENT CONTENTS
		$panel_id = 'fd_document_widgets';
		$section_description = __( 'Styling settings of all contents of the document. You can adjust all part of the document at once here.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Document General Settings' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 5,
		        )
		    );
		}
		
		//DOCUMENT WIDGETS
	    $panel_id = null; //this section has no parent panel, it goes right to the customizer "main menu"
		$section_description = __( 'Styling settings of all contents of the document. You can adjust all part of the document at once here.' , 'filo_text' );
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '_all_widgets', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'normal_widget',
			//$selector_data = self::get_panel_selector( 'ALL_WIDGETS' ),
			//$all_selector_data = null, //this panel does not have all.... section 
			$selector_data = null, // this panel type has only special widget, thus it is null
			$all_selector_data = array( //This is a special (not real) panel id for Document-General
				'Document-General' => self::get_panel_selector( 'Document-General' ),
			), 			
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description = '' 
		);
		
		//wsl_log(null, 'class-filo-customize-manager.php add_panels Document-General $sections: ' . wsl_vartotext( $sections )); //LARGE
		
		$section_priority = 5;
		
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render, $start_section_priority = $section_priority ); //$start_section_priority means that we place this main menu item according to the panels proirity (thus it means the main menu priority and not a sub section inside priority)
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css
		

		//ROW CONTENTS
		$panel_id = 'fd_row_widgets';
		$section_description = __( 'Styling settings of all contents of a selected row. You can adjust all part of the row at once here. The table level settings of a Item Table - like border collapse - can only be set here.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Rows' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 10,
		        )
		    );
		}
		
		$sections = self::define_rows_sections( 
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Not used!?
			$section_dynamic_tag_type = null,
			$selector_data = self::get_panel_selector( $panel_id ),
			$all_selector_data = array( //This is a special (not real) panel id
				'All-Normal-Rows' => self::get_panel_selector( $panel_id . '_All-Normal-Rows' ), 
				'Item-Table' => self::get_panel_selector( $panel_id . '_Item-Table' ),
			),			
			//dynamic part of css selector: ' #'. $grid['style']['id']
			$section_description
		);
		wsl_log(null, 'class-filo-customize-manager.php add_panels 1 $sections: ' . wsl_vartotext( $sections ));
		wsl_log(null, 'class-filo-customize-manager.php add_panels 1 self::get_panel_selector( $panel_id ): ' . wsl_vartotext( self::get_panel_selector( $panel_id ) ));
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css 

		
		/*
		//FULLWIDTH ROW CONTENTS
		$panel_id = 'fd_fullwidth_row_widgets';
		$section_description = __( 'Fullwidth styling settings of all contents of a selected row. You can adjust fullwidth background and other styling.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Fullwidth Row Settings' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 10,
		        )
		    );
		}
		
		$sections = self::define_rows_sections( 
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Not used!?
			$section_dynamic_tag_type = null,
			$selector_data = self::get_panel_selector( $panel_id ),
			$all_selector_data = array( //This is a special (not real) panel id
				'All-Rows' => self::get_panel_selector( $panel_id . '_All-Rows' ), 
			),			
			//dynamic part of css selector: . $grid['style']['id']
			$section_description
		);
		//wsl_log(null, 'class-filo-customize-manager.php add_panels 1 $sections: ' . wsl_vartotext( $sections ));
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css 
		*/
				
		
		
		/*//ALL WIDGETS
	    $panel_id = null; //this section has no parent panel, it goes right to the customizer "main menu"
		$section_description = __( 'Styling settings of all document widget of the page. Usually these settings are valid for normal widgets, please use <i>Data Table Widgets</i> and <i>Item Table Columns</i> menu item for setting these special widgets.' , 'filo_text' );
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '_all_widgets', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'all_widgets',
			//$selector_data = self::get_panel_selector( 'ALL_WIDGETS' ),
			//$all_selector_data = null, //this panel does not have all.... section 
			$selector_data = null, // this panel type has only special widget, thus it is null
			$all_selector_data = array( //This is a special (not real) panel id for All-Widgets
				'All-Widgets' => self::get_panel_selector( 'All-Widgets' ),
			), 
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description = '' 
		);*/
		
		$section_priority = 20;
		
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render, $start_section_priority = $section_priority ); //$start_section_priority means that we place this main menu item according to the panels proirity (thus it means the main menu priority and not a sub section inside priority)
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css
		
		//wsl_log(null, 'class-filo-customize-manager.php add_panels $all_settings_data: ' . wsl_vartotext( $all_settings_data ));		
		
		//NORMAL WIDGETS
		$panel_id = 'fd_normal_widgets';
		$section_description = __( 'Styling settings of the selected widget. These settings are valid for normal widgets, please use <i>Data Table Widgets</i> and <i>Item Table Columns</i> menu item for setting these special widgets.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Normal Widgets' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 30,
		        )
		    );
		}
		
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = 'normal_widget', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'normal_widget',
			$selector_data = self::get_panel_selector( $panel_id ), 
			$all_selector_data = array( //This is a special (not real) panel id for All-Widgets
				'All-Widgets' => self::get_panel_selector( $panel_id . '_All-Widgets' ),
			), 
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description 
		);
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css

		//-----------

		//DOC TITLE WIDGET
		$panel_id = 'fd_doc_title_widgets';
		$section_description = __( 'Styling settings of the document title.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Document Title Widget' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 40,
		        )
		    );
		}
		
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = 'normal_widget', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'doc_title_widget',
			$selector_data = self::get_panel_selector( $panel_id ), 
			$all_selector_data = null, //this panel does not have all.... section
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description 
		);
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css

		//DATA TABLE WIDGETS
		$panel_id = 'fd_data_table_widgets';
		$section_description = __( 'Data table widget contains data labels and values in a horizontal or vertival table format. Styling settings of the selected data table widget can be adjusted here.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Data Table Widgets' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 50,
		        )
		    );
		}
		
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '_data_table_widget', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'data_table_widget',
			$selector_data = self::get_panel_selector( $panel_id ),
			$all_selector_data = null, //this panel does not have all.... section 
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description 
		);
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css



		//ITEM TABLE WIDGETS
		$panel_id = 'fd_item_table_widgets';
		$section_description = __( 'Row widget contains document item rows in a table format. Styling settings of the selected table column can be adjusted here. You can apply settings for the whole row at once using the <i>Rows</i> section. Table level settings of a Item Table - like border collapse - can only be set in the <i>Row</i> section.' , 'filo_text' );
		if ( ! $for_css_render ) {
		    $wp_customize->add_panel(
		        $panel_id,
		        array(
		            'title' => __('Item Table Columns' , 'filo_text'),
		            'description' => $section_description,
		            'priority' => 60,
		        )
		    );
		}
		
		$sections = self::define_widget_sections(
			$panel_id, 
			$template_panels_data, 
			//$id_suffix = '_item_table_widgets', //Not used!? 
			$title_suffix = ' ' . __( '', 'filo_text' ), //Contents
			$section_dynamic_tag_type = 'item_table_widget',
			$selector_data = self::get_panel_selector( $panel_id ),
			$all_selector_data = array( //This is a special (not real) panel id for All-Item-Table-Columns
				'All-Item-Table-Columns' => self::get_panel_selector( $panel_id . '_All-Item-Table-Columns' ),
			), 
			//dynamic part of css selector: $widget['panels_info']['style']['widget_code']
			$section_description 
		);
		$generated_settings_data = self::add_sections( $panel_id, $sections, $template_panels_data, $for_css_render );
		$all_settings_data[$panel_id] = $generated_settings_data; //for render css
		
		return $all_settings_data;

	}




	/**
	 * define_rows_sections
	 * 
	 * Define sector for the row type panels 
	 */
	static function define_rows_sections( $panel_id, $panels_data, 
			//$id_suffix = null, 
			$title_suffix = null, //Not used!?
			$section_dynamic_tag_type = null,
			$selector_data,
			$all_selector_data = null, //if the panel has all.... section, than it is filled
			$section_description
		 ) {
		
		wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $panel_id 0: ' . wsl_vartotext( $panel_id ));
		//wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $selector_data 0: ' . wsl_vartotext( $selector_data )); //big
		wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $all_selector_data 0: ' . wsl_vartotext( $all_selector_data ));
		wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $panels_data[grids] 0: ' . wsl_vartotext( $panels_data['grids'] ));
		
		
		$sections = array();
		
		//DOC ROWS
		if ( isset($panels_data['grids']) and is_array($panels_data['grids']) ) {

			//in case of "all-rows" widget type generate a special virtual panel data

			//if in case of this panel All... section is needed, then add it as a "virtual" section
			$panels_data_2['grids'] = array();
			
			// Create a new "virtual" section for all special selector, e.g. 'All-Normal-Rows', 'Item-Table' 
			if ( isset($all_selector_data) and is_array($all_selector_data) ) {
			
				$count = -100;
				$array_i = array();
				
				foreach ( $all_selector_data as $key => $value) {
					
					$count++;
					$array_i[$count] = 
						array( 
							'style' => array(
								'id' => $key,
							),
						);
				}

				$panels_data_2['grids'] = $array_i;
			
			}

			$panels_data_3['grids'] = $panels_data['grids'];
			
			//add All.... section if it is filled
			$panels_data_3['grids'] = array_merge( $panels_data_2['grids'], $panels_data_3['grids'] );
			
			wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $panels_data_3[grids] 0: ' . wsl_vartotext( $panels_data_3['grids'] ));
		
			foreach ( $panels_data_3['grids'] as $grid_id => $grid ) {
				if ( isset($grid['style']['id']) ) {

				/*if ( 
					isset($widget['panels_info']['style']['widget_code'])
					and (
						0 == 1 
						//in case of normal widget and data_table_widget, the widget codes starting with FILO_Widget_Invbld_Line_ should not be displayed
						//or ( $section_dynamic_tag_type == 'all_widgets' )
						//or ( $widget['panels_info']['class'] == 'ALL_TYPE' )
						or in_array( $widget['panels_info']['style']['widget_code'], array('All-Widgets', 'Document-General') ) //, 'All-Item-Table-Columns'
						//or ( in_array( $section_dynamic_tag_type, array('normal_widget', 'doc_title_widget') ) and ! in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical') ) and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) === false ) )  //in case of table widget, only those widgets should be displayed that starts with FILO_Widget_Invbld_Line_
						or ( $section_dynamic_tag_type == 'normal_widget' and ! in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical', 'FILO_Widget_Invbld_Doc_Title') ) and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) === false ) )  //in normal widget, the given special widgets and those that starts with FILO_Widget_Invbld_Line_ is not displayed  
						or ( $section_dynamic_tag_type == 'doc_title_widget' and in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Doc_Title') ) )  //in doc title section only FILO_Widget_Invbld_Doc_Title widgets should be displayed
						or ( $section_dynamic_tag_type == 'data_table_widget' and in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical') ) ) //in data table section only the relevant widgets should be displayed
						or ( $section_dynamic_tag_type == 'item_table_widget' and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) !== false ) )   //in case of item table widget, only those widgets should be displayed that starts with FILO_Widget_Invbld_Line_
						or ( $section_dynamic_tag_type == 'item_table_widget' and in_array( $widget['panels_info']['style']['widget_code'], array('All-Item-Table-Columns') ) ) //in case of item table widget, 'All-Item-Table-Columns' also should be displayed
					)
				) {*/


	
					$actual_selector_data = $selector_data;
					$cell_selector = $grid['style']['id'];
					
					////wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $all_selector_data[$grid[style][id]]: ' . wsl_vartotext( $all_selector_data[$grid['style']['id']] ));
						
					if ( isset($all_selector_data[$grid['style']['id']]) and is_array($all_selector_data[$grid['style']['id']]) ) {

						$cell_selector = '';
						
						$my_selector_data = $all_selector_data[$grid['style']['id']]; 
						
						if ( is_array($my_selector_data) ) {
							$actual_selector_data = $my_selector_data; //in case of all types, we do not use the normal selector data, because we need a "wider" range, that contains $all_selector_data parameter if it is given for this panel, so let's use this
							//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements MZ/X: ' . wsl_vartotext( ''));
						}

					}
	
					wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $actual_selector_data 0: ' . wsl_vartotext( $actual_selector_data ));
	
					//create selectors 
					foreach ($actual_selector_data as $selector_data_key => $selector_data_value) {
						//PREFIX + dynamic_tag + SUFFIX
						$selectors[$selector_data_key] = $actual_selector_data[$selector_data_key]['prefix'] . $cell_selector . ' ' . $actual_selector_data[$selector_data_key]['suffix'];
					}
					
					wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $grid[style][id]: ' . wsl_vartotext( $grid['style']['id'] ));
					
					$sections_key = $grid['style']['id']; //section key is always the $section_dynamic_tag, because we have to create the css selector from the saved data
					
					$sections[$sections_key]  = array(
						'key' => $sections_key, 
						'title' => __('Row' , 'filo_text') . ': ' . $sections_key . $title_suffix, //e.g. Row: myrow Headers
						'description' => $section_description,
						'selectors' => $selectors,
						'settings_collection' => 'full',
					);
				}
			} 
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php define_rows_sections $sections: ' . wsl_vartotext( $sections )); //Big		
		return $sections;
				
	}
			

	/**
	 * define_widget_sections
	 * 
	 * According to definition of sectors for the widget type panels, it adds normal and special sections to the panel, and partitios to the sections 
	 * 
	 * Combine Sections and Selectors for the panel
	 * This function is get the panel data (especially the SECTION codes, that is the widget codes of the panel), 
	 * and also get the SELECTOR data (and optionally the special (all type) selector data), that is the "partitions" should be applied for the sections of this panel. 
	 * The selector data contains the "partition" codes of the panel and the selector prefixes and suffix of each section.
	 * This function combines the sections data and partition data, and inserts all partitions into all section (a kind of multiplying). 
	 * 
	 * There are special selector data (e.g all....). Sometimes it contain the same selector codes than the normal, just the prefix and postfix are different, and in this case dynamic tag is not concatenated (to be more general css selector)
	 * but in other cases the special selector data contains additional selector codes, that should be added only the section of this special section.  
	 * 
	 * This function contains some custom logic to separate some special widget types, like data tables, item tables. 
	 */
	static function define_widget_sections( $panel_id, $panels_data,
			//$id_suffix = null, 
			$title_suffix = null, //Not used!?
			$section_dynamic_tag_type = null, //'normal_widget', 'doc_title_widget', 'data_table_widget', 'item_table_widget', 'all_widgets'
			$selector_data,
			$all_selector_data = null, //if the panel has all.... section, than it is filled
			$section_description
		 ) {
		
		wsl_log(null, 'class-filo-customize-manager.php define_widget_sections $panel_id 0: ' . wsl_vartotext( $panel_id ));
		//wsl_log(null, 'class-filo-customize-manager.php define_widget_sections $all_selector_data 0: ' . wsl_vartotext( $all_selector_data ));
		
		// returns key / title data
		$sections = array();
		
		//CELLS
		if ( isset($panels_data['widgets']) and is_array($panels_data['widgets']) ) {

			
			//in case of "all_widgets" widget type generate a special virtual panel data
			//if in case of this panel All... section is needed, then add it as a "virtual" section
			
			$panels_data_2['widgets'] = array();
				
			// Create a new "virtual" section for all special selector, e.g. 'All-Item-Table-Columns'
			if ( isset($all_selector_data) and is_array($all_selector_data) ) {
			
				$count = -100;
				$array_i = array();
				
				foreach ( $all_selector_data as $key => $value) {
					
					$count++;
					$array_i[$count] = 
						array( 
							'panels_info' => array(
								'class' => $key,
								'style' => array(
									'widget_code' => $key,
								),
							),
						);
				}

				$panels_data_2['widgets'] = $array_i;
			
			}

			//Merge normal and virtual data
			$panels_data_3['widgets'] = $panels_data['widgets'];
			
			//add All.... section if it is filled
			$panels_data_3['widgets'] = array_merge( $panels_data_2['widgets'], $panels_data_3['widgets'] );
			
			//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $panels_data_3[widgets] 0: ' . wsl_vartotext( $panels_data_3['widgets'] )); //LARGE
			//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $panels_data_3 1: ' . wsl_vartotext( $panels_data_3 ));
			
			//go through on each sections and add them selectors  
			foreach ( $panels_data_3['widgets'] as $widget_id => $widget ) {
				
				wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $widget_id 0: ' . wsl_vartotext( $widget_id ));
				//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $widget[panels_info][style][widget_code] 0: ' . wsl_vartotext( $widget['panels_info']['style']['widget_code'] ));
				//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $widget 0: ' . wsl_vartotext( $widget ));
				
				//group sections into normal and special panels by custom roules, thus not all sections are created for all panel
				//e.g. if widget code is set, and not a Line field (e.g. item_name, qty, ...) we display the settings
				//thus widgets without code is not displayed, and line fileds settings are displayed in different list
				if ( 
					isset($widget['panels_info']['style']['widget_code'])
					and (
						0 == 1 
						//in case of normal widget and data_table_widget, the widget codes starting with FILO_Widget_Invbld_Line_ should not be displayed
						//or ( $section_dynamic_tag_type == 'all_widgets' )
						//or ( $widget['panels_info']['class'] == 'ALL_TYPE' )
						or in_array( $widget['panels_info']['style']['widget_code'], array('Document-General') ) //,'All-Widgets', 'All-Item-Table-Columns'
						//or ( in_array( $section_dynamic_tag_type, array('normal_widget', 'doc_title_widget') ) and ! in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical') ) and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) === false ) )  //in case of table widget, only those widgets should be displayed that starts with FILO_Widget_Invbld_Line_
						or ( $section_dynamic_tag_type == 'normal_widget' and ! in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical', 'FILO_Widget_Invbld_Doc_Title') ) and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) === false ) )  //in normal widget, the given special widgets and those that starts with FILO_Widget_Invbld_Line_ is not displayed  
						or ( $section_dynamic_tag_type == 'doc_title_widget' and in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Doc_Title') ) )  //in doc title section only FILO_Widget_Invbld_Doc_Title widgets should be displayed
						or ( $section_dynamic_tag_type == 'data_table_widget' and in_array($widget['panels_info']['class'], array('FILO_Widget_Invbld_Head_Data_Horizontal', 'FILO_Widget_Invbld_Head_Data_Vertical') ) ) //in data table section only the relevant widgets should be displayed
						or ( $section_dynamic_tag_type == 'item_table_widget' and (strpos( $widget['panels_info']['class'], 'FILO_Widget_Invbld_Line_' ) !== false ) )   //in case of item table widget, only those widgets should be displayed that starts with FILO_Widget_Invbld_Line_
						or ( $section_dynamic_tag_type == 'item_table_widget' and in_array( $widget['panels_info']['style']['widget_code'], array('All-Item-Table-Columns') ) ) //in case of item table widget, 'All-Item-Table-Columns' also should be displayed
					)
				) {
					
					wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $widget[panels_info][style][widget_code] OK 0: ' . wsl_vartotext( $widget['panels_info']['style']['widget_code'] ));
					wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements OK 0: ' . wsl_vartotext( '' ));
					
					//remove the standard "FILO_Widget_Invbld_" and "SiteOrigin_Widget_" prefix, because it is too long for the users to select
					$title = $widget['panels_info']['style']['widget_code'];
					$title = str_replace( 'FILO_Widget_Invbld_', '', $title);
					$title = str_replace( 'SiteOrigin_Widget_', '', $title);
					
					$actual_selector_data = $selector_data;
					
					//set cell selector for normal cases					
					$cell_selector = $widget['panels_info']['style']['widget_code']; //'. ' . $widget['panels_info']['style']['widget_code'];
					
					//We do not need any cell selector for special sections (e.g. for All_Widgets), because it is valid for all the cells of the row
					//if ( in_array( $widget['panels_info']['style']['widget_code'], array('All-Widgets', 'All-Item-Table-Columns') ) ) {
					if ( isset($all_selector_data[$widget['panels_info']['style']['widget_code']]) and is_array($all_selector_data[$widget['panels_info']['style']['widget_code']]) ) {

						$cell_selector = '';
												
						$my_selector_data = $all_selector_data[$widget['panels_info']['style']['widget_code']]; 
						
						if ( is_array($my_selector_data) ) {
							$actual_selector_data = $my_selector_data; //in case of all types, we do not use the normal selector data, because we need a "wider" range, that contains $all_selector_data parameter if it is given for this panel, so let's use this
							//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements MZ/X: ' . wsl_vartotext( ''));
						}

					}

					//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $selector_data: ' . wsl_vartotext( $selector_data ));
					//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $my_selector: ' . wsl_vartotext( $my_selector ));
					//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $actual_selector_data: ' . wsl_vartotext( $actual_selector_data ));
					
					
					//create selectors
					$selectors = array();
					if ( isset($actual_selector_data) and is_array($actual_selector_data) ) 
					foreach ($actual_selector_data as $selector_data_key => $selector_data_value) {
						//PREFIX + dynamic_tag + SUFFIX
						$selectors[$selector_data_key] = $actual_selector_data[$selector_data_key]['prefix'] . $cell_selector . ' ' . $actual_selector_data[$selector_data_key]['suffix'];
					}
					
					//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $widget[panels_info][style][widget_code]: ' . wsl_vartotext( $widget['panels_info']['style']['widget_code'] ));
					//wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $cell_selector: ' . wsl_vartotext( $cell_selector ));
					
					$sections_key = $widget['panels_info']['style']['widget_code']; //section key is always the $section_dynamic_tag, because we have to create the css selector from the saved data
					
					wsl_log(null, 'class-filo-customize-manager.php customizer_config_panel_elements $sections_key: ' . wsl_vartotext( $sections_key ));
					
					$sections[$sections_key] = array(
						'key' => $sections_key,
						'title' => $title . $title_suffix,
						'description' => $section_description,
						'selectors' => $selectors,
						'settings_collection' => 'full',						
					);
					
				}
			}
		}
		
		wsl_log(null, 'class-filo-customize-manager.php define_widget_sections $panel_id 9: ' . wsl_vartotext( $panel_id ));			
		//wsl_log(null, 'class-filo-customize-manager.php define_widget_sections $sections: ' . wsl_vartotext( $sections )); //LARGE
					
		return $sections;
				
	}
		
	/**
	 * add_sections
	 * 
	 * add the previously defined sections to a panel
	 * 
	 */
	static function add_sections( $panel_id, $sections, $template_panels_data, $for_css_render = false, $start_section_priority = 0  ) {	
		global $wp_customize;
		
		wsl_log(null, 'class-filo-customize-manager.php add_sections $panel_id: ' . wsl_vartotext( $panel_id ));
		//wsl_log(null, 'class-filo-customize-manager.php add_sections $sections: ' . wsl_vartotext( $sections )); //LARGE
		
		//***********************
		// SECTIONS
		//***********************

		$priority = $start_section_priority;
		
		if ( $sections and is_array($sections) )
		foreach ( $sections as $section_key => $section ) {
				
			$priority++;

			$section = apply_filters('filo_customize_section', $section, $section_key, $section, $sections, $panel_id);
				
			$description = $section['description'];
			
			$description = apply_filters('filo_customize_section_description', $description, $section_key, $section, $sections, $panel_id);
			
			if ( ! $for_css_render ) {
				$wp_customize->add_section(
			        $section_key,
			        array(
			            'title' => $section['title'],
			            'description' => $description,
			            'panel' => $panel_id,
			            'priority' => $priority,
			        )
			    );	
			}
			
			$generated_settings_data = self::add_settings_and_controls( $section_key, $section, $panel_id, $template_panels_data, $for_css_render );	
		}
		
		return $generated_settings_data;

	}
	
	/**
	 * protected_warning_in_section_description
	 * 
	 * Add warning to section descriptions if it is a protected skin.
	 * 
	 * called by filo_customize_section filter
	 * 
	 */
	static function protected_warning_in_section_description( $description ) {	
		global $filo_doc_act_opt_name_option;
		
		if (empty($filo_doc_act_opt_name_option)) {
			$filo_doc_act_opt_name_option = get_option('filo_doc_act_opt_name');
		}

		wsl_log(null, 'class-filo-customize-manager.php protected_warning_in_section_description $filo_doc_act_opt_name_option: ' . wsl_vartotext( $filo_doc_act_opt_name_option )); 

		//add protected warning to description			
		if ( strpos(strtoupper($filo_doc_act_opt_name_option), 'FILOPROTECT_') !== false ) {
			
			wsl_log(null, 'class-filo-customize-manager.php protected_warning_in_section_description $description 0: ' . wsl_vartotext( $description ));
			wsl_log(null, 'class-filo-customize-manager.php protected_warning_in_section_description empty($description) 0: ' . wsl_vartotext( empty($description) ));
			
			$description .=
				(empty($description) ? '' : '<br>') .  
				'<font color="red">' . __('Protected skin!', 'filo_text') . ' ' . '</font>' .
				__('Save it as a different Skin name before modify any settings.', 'filo_text');
				
				
			wsl_log(null, 'class-filo-customize-manager.php protected_warning_in_section_description $description 2: ' . wsl_vartotext( $description ));
		}
		
		return $description;
		
	}
	
	/**
	 * add_settings_and_controls
	 * 
	 * Add controls and settings to a panel section
	 * It uses partition settings data (that defines all possible controls and settings data of every partition type, including css selector types (css_header_selector, css_content_selector,  ...))
	 * Every panel has it's own selector list, that contains the really aplicable selector codes with the panel specific css selector prefixes and suffixes (get_panel_selector( $panel_id ), add_panels( $panel_id )) 
	 * add_panels() calls add_sections() that calls this add_settings_and_controls() function for creating section elements.
	 * The displayable controls/settings are the same for every section of the same panel, but different panels has differend controls/settings
	 * 
	 */
	static function add_settings_and_controls( $section_key, $section, $panel_id, $template_panels_data, $for_css_render = false ) {
		global $wp_customize;
			
		wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $section_key: ' . wsl_vartotext( $section_key ));
		//wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $section: ' . wsl_vartotext( $section )); //LARGE

		//e.g. $section: Array
		//(
		//    [key] => my-very-first-new-row
		//    [title] => Row: my-very-first-new-row 
		//    [description] => Styling settings of all contents of a selected row. You can adjust all part of the row at once here. The table level settings of a Item Table - like border collapse - can only be set here.
		//    [selectors] => Array
		//        (
		//            [css_cell_selector] =>  .filo_document #my-very-first-new-row  .panel-grid-cell 
		//            [css_widget_selector] =>  .filo_document #my-very-first-new-row  .panel-grid-cell .widget 
		//            [css_content_selector] =>  .filo_document #my-very-first-new-row  .panel-grid-cell .filo_content 
		//            [css_header_selector] =>  .filo_document #my-very-first-new-row  .panel-grid-cell .filo_headline 
		//            [css_item_table_selector] =>  .filo_document #my-very-first-new-row 
		//            [css_item_table_header_cell_selector] =>  .filo_document #my-very-first-new-row  .Filo_Item_Table_Header .panel-grid-cell 
		//            [css_item_table_body_cell_selector] =>  .filo_document #my-very-first-new-row  .Filo_Item_Table_Body .panel-grid-cell 
		//            [css_item_table_footer_cell_selector] =>  .filo_document #my-very-first-new-row  .Filo_Item_Table_Footer .panel-grid-cell 
		//            [css_data_table] =>  .filo_document #my-very-first-new-row  .filogy_data_table_widget .filogy_data_table 
		//            [css_data_table_label_cell_selector] =>  .filo_document #my-very-first-new-row  .filogy_data_table_widget .filogy_data_table .table_label 
		//            [css_data_table_value_cell_selector] =>  .filo_document #my-very-first-new-row  .Filo_Item_Table_Body .panel-grid-cell .table_label
		//        )
		//
		//    [settings_collection] => full
		//)
						

		//get all possible settings that can be used for the specific partitions
		$partition_settings_data = self::get_partition_settings_data();
		
		$generated_settings_data = array(); //settings data for render css
		
		//wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $partition_settings_data: ' . wsl_vartotext( $partition_settings_data ));

		foreach ($partition_settings_data as $partition_id => $partition_settings) {
			
			foreach ($partition_settings as $setting_key => $setting_attributes) {
			
				//let's decide whether this control should be displayed, in case of any possible css selector
				if ( isset( $section['selectors'][$setting_attributes['css_selector_key']] ) ) { 
					//e.g. if ( isset( $section['selectors']['css_header_selector'] ) ) {
					
					//settings_id define the structure of the saved data (option)
					//we use this structure when get back from the saved option during render of css styles
					//using panel_id we can get the possible css selector prefixes and suffixes, 
					//using css_selector_key (partition identifyer) we can decide which of them should be applied for rendering
					//section key is the $section_dynamic_tag, we include it from the saved option to the rendered css selector during css rendering
					//on the bottom of this structure there is the css_property that value will be used to render css_property names (i.g. background color)
					//e.g. filo_doc[fd_normal_widgets][FILO_Widget_Invbld_Seller_Address][css_widget_selector][background-color]";"
					
					$setting_id = 'filo_doc[' . $panel_id . '][' . $section['key'] . '][' . $setting_attributes['css_selector_key'] . '][' . $setting_attributes['setting_type'] . ']';
					
					//DELETE: $setting_id = 'filo_doc[' . $panel_id . '][' . $section['key'] . '][' . $setting_attributes['css_selector_key'] . '][' . $setting_attributes['css_property'] . ']';
					 
					wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $setting_id X: ' . wsl_vartotext( $setting_id ));
					//wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $setting_attributes: ' . wsl_vartotext( $setting_attributes )); //big				
					wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $setting_attributes[css_property]: ' . wsl_vartotext( $setting_attributes['css_property'] ));

					if ( ! $for_css_render ) { // $for_css_render variable is not used, this is always false 
						
						switch ($setting_attributes['control_type']) {
							
							case 'group':
								self::display_control_group_header( $setting_id, 
									$setting_attributes['label'], 
									$setting_attributes['description'], 
									$section['key'],
									$partition_id,
									$panel_id,
									$priority = $setting_attributes['priority'] );
										//e.g. self::display_control_group_header( $setting_id, __('Widget Header Settings' , 'filo_text'), __('' , 'filo_text'), $section['key'], $priority = 10 );
								break;

							case 'text':
							case 'checkbox':
							case 'radio':
							case 'select':
							case 'textarea':
							case 'dropdown-pages':
							case 'email':
							case 'url':
							case 'number':
							case 'hidden':
							case 'date':		
							
								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
									'default'           => '', //RaPe ToDo: $color_scheme[2]
									//'sanitize_callback' => 'sanitize_hex_color',
									'transport'         => 'postMessage',
									'type' => 'option',
									'filo_css_property' => $setting_attributes['css_property'],
									'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
									'filo_css_measurement_unit' => $setting_attributes['measurment_unit'],
								) ) );
							
								$wp_customize->add_control( $setting_id, array(
									'label' => $setting_attributes['label'],
									//'label' =>__( 'Primary Color <small>action buttons/price slider/layered nav UI222</small>', 'woocommerce-colors' ),
									'description' => $setting_attributes['description'],
									'section'  => $section['key'],
									//'settings' => $setting_id,
									'type'     => $setting_attributes['control_type'], //text, checkbox, radio, select, textarea, dropdown-pages, email, url, number, hidden, date
									'choices' => isset($setting_attributes['choices']) ? self::$setting_attributes['choices']() : null, //$setting_attributes['choices'] contains the function name to be called
									'priority' => $setting_attributes['priority'], 
								) );
								
								break;

							case 'text_datalist':		
							
								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
									'default'           => '',
									'transport'         => 'postMessage',
									'type' => 'option',
									'filo_css_property' => $setting_attributes['css_property'],
									'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
									'filo_css_measurement_unit' => $setting_attributes['measurment_unit'],
								) ) );
							
								//type: text_datalist
								$wp_customize->add_control( new FILO_Customize_Text_Datalist_Control( $wp_customize, $setting_id, array(
									'label' => $setting_attributes['label'],
									'description' => $setting_attributes['description'],
									'section'  => $section['key'],
									'settings'          => $setting_id,
									'type'              => 'text',
									'choices' => isset($setting_attributes['choices']) ? self::$setting_attributes['choices']() : null, //$setting_attributes['choices'] contains the function name to be called									
									'priority' => $setting_attributes['priority'], 
								) ) );				
								
								break;
																
							case 'fontselect':
								
								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
									'default'           => '',
									'transport'         => 'postMessage',
									'type' => 'option',
									'filo_css_property' => $setting_attributes['css_property'],
									'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
								) ) );
							
								//type: fontselect
								$wp_customize->add_control( new FILO_Customize_Fontselect_Control( $wp_customize, $setting_id, array(
									'label' => $setting_attributes['label'],
									'description' => $setting_attributes['description'],
									'section'  => $section['key'],
									'settings'          => $setting_id,
									'type'              => 'fontselect',
									'priority' => $setting_attributes['priority'], 
								) ) );			
								
								//options are set here: filogy-framework/modules/fontselect-jquery-plugin/jquery.fontselect.js	
								
								break;

							case 'color':
							
								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
									'default'           => '', //RaPe ToDo: $color_scheme[2]
									'sanitize_callback' => 'sanitize_hex_color',
									'transport'         => 'postMessage',
									'type' 				=> 'option',
									'filo_css_property' => $setting_attributes['css_property'],
									'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
								) ) );
							
								//type: color
								$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(								
									'label' => $setting_attributes['label'],
									'description' => $setting_attributes['description'],
									'section'  => $section['key'],
									'priority' => $setting_attributes['priority'],
								) ) );

								//------------
								
								//add a slect box for every color field for choosing an element from color palette 
								$setting_id2 = rtrim( $setting_id, "]" ) . '_mycolor_ref]'; //remove the original ] from the end and add _mycolor_ref], e.g. filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color] to filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color_mycolor_ref]
								
								//wsl_log(null, 'class-filo-customize-manager.php add_settings_and_controls $setting_id2: ' . wsl_vartotext( $setting_id2 ));
								
								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id2, array(
									'default'           => '', //RaPe ToDo: $color_scheme[2]
									//'sanitize_callback' => 'sanitize_hex_color',
									'transport'         => 'postMessage',
									'type' => 'option',
									'filo_css_property' => '',//$setting_attributes['css_property'],
									'filo_css_selector' => '',//$section['selectors'][$setting_attributes['css_selector_key']],
								) ) );
							
							
								$wp_customize->add_control( new FILO_Customize_Mycolor_Reference_Control( $wp_customize, $setting_id2, array(								
									'label' => null, //$setting_attributes['label'],
									'description' => null, //$setting_attributes['description'],
									'section'  => $section['key'],
									//'settings' => $setting_id2,
									'type'     => 'select', //text, checkbox, radio, select, textarea, dropdown-pages, email, url, number, hidden, date
									'choices' => self::get_color_palette_elements(),
									'priority' => $setting_attributes['priority'] + 1,
								) ) );
							
								break;
								
							case 'imageselect':
								

								$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
									'default'           => '',
									'transport'         => 'postMessage',
									'type' => 'option',
									'filo_css_property' => $setting_attributes['css_property'],
									'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
								) ) );
						
								//type: imageselect
								$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $setting_id, array(
									'label' => $setting_attributes['label'],
									'description' => $setting_attributes['description'],
									'section'     => $section['key'],
									'priority'    => $setting_attributes['priority'],
								) ) );
								
								//options are set here: filogy-framework/modules/fontselect-jquery-plugin/jquery.fontselect.js	
								
								break;								
															
						}

					} 
						
					$generated_settings_data[$setting_id] = array(
						'filo_css_property' => $setting_attributes['css_property'],
						'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
					
					); 
						
				}		
			}	 
		}		

		return $generated_settings_data;
	}
	

	/**
	 * display_control_group_header
	 * 
	 * Displays a submenu accordion (nav menu item) or a textual item title
	 * 
	 * $group_display_type = "nav_menu_item" / "textual_title_item"
	 */
	static function display_control_group_header($id, $title, $description, $section, $partition_id, $panel_id, $priority, $group_display_type = 'nav_menu_item') {
		global $wp_customize;

		//wsl_log(null, 'class-filo-customize-manager.php display_control_group_header $id: ' .  wsl_vartotext($id)); 
		//wsl_log(null, 'class-filo-customize-manager.php display_control_group_header $title: ' .  wsl_vartotext($title)); 
		//wsl_log(null, 'class-filo-customize-manager.php display_control_group_header $section: ' .  wsl_vartotext($section));
		//wsl_log(null, 'class-filo-customize-manager.php display_control_group_header $partition_id: ' .  wsl_vartotext($partition_id));
		//wsl_log(null, 'class-filo-customize-manager.php display_control_group_header $panel_id: ' .  wsl_vartotext($panel_id));

		$panel_category = self::get_panel_category($panel_id);

		$wp_customize->add_setting( $id, array() ); 

		
		if ($group_display_type == 'nav_menu_item') {
					
			$wp_customize->add_control( new FILO_Customize_Nav_Menu_Item_Control( $wp_customize, $id, array(
					'label'	=> $title,
					'description'	=> $description,
					'section' => $section,
					'partition_id' => $partition_id,
					'panel_id' => $panel_id,
					'panel_category' => $panel_category,
					'priority' => $priority,
				) 
			));
		
		} elseif ($group_display_type == 'textual_title_item') {
			
			$wp_customize->add_control( new FILO_Customize_Header_Control( $wp_customize, $id, array(
					'label'	=> $title,
					'description'	=> $description,
					'section' => $section,
					'priority' => $priority,
				) 
			));			
		}
	
	}

	/**
	 * render_css
	 * 
	 * print style code containing the customized css settings
	 * 
	 * Called from the template, e.g. from /filogy/templates/documents/document-header.php
	 * 
	 */
	static function render_css() { 
		global $wp;
		
		wsl_log(null, 'class-filo-customize-manager.php render_css $_SERVER: ' . wsl_vartotext( $_SERVER ));
		wsl_log(null, 'class-filo-customize-manager.php render_css $_GET: ' . wsl_vartotext( $_GET ));
		wsl_log(null, 'class-filo-customize-manager.php render_css $wp->request: ' . wsl_vartotext( $wp->request ));
		
		
		//if the page is displayed in customize preview, then css must not be printed
		//because inline css is added by jquery (render_initial_jquery_inline_styles())
		//in customize preview case, $_GET contains filo_usage=doc
		
		
		//in customize preview case, the http referer contains the followings: $_SERVER['HTTP_REFERER'] => http://mysite.com/wp-admin/customize.php?autofocus%5Bcontrol%5D=color1&filo_usage=doc&return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwc-settings%26tab%3Ddocument&filo_sample_order_id=2874
		//Thus if customize.php is not found in http_referer then the css can be displayed 
		if ( ( strpos($_SERVER['HTTP_REFERER'], '/wp-admin/customize.php') === false ) and ( ! isset($_GET['filo_customizer']) or $_GET['filo_customizer'] != 'true' ) ) {
		//if (true) {
			
			wsl_log(null, 'class-filo-customize-manager.php render_css printed YES2: ' . wsl_vartotext( '' ));
			
			$render_data = self::generate_render_data();
			
			$render_data_normal = $render_data['normal_selectors'];
			//$render_data_custom_css = $render_data['custom-css'];
			
			//1. render normal selectors, that css property - value pairs for the selector
			echo '<style type="text/css" id="filo-customize-manager-custom-css">';
			
			if ( isset($render_data_normal) and is_array($render_data_normal) )
			foreach ($render_data_normal as $css_selector => $prop_values) {
				
				echo $css_selector . ' { ';
				
				if ( isset($prop_values) and is_array($prop_values) )
				foreach ($prop_values as $css_property => $setting_value) {
					
					echo $css_property . ': ' . $setting_value . '; ';
					
				}
				
				echo ' }';
				
			}

			echo '</style>';
			
			self::render_custom_css( $render_data );
			
		}

	}	


	/**
	 * render_custom_css
	 * 
	 * print style code containing the customized css settings, that the user typed into the css fields
	 * 
	 * Called from the template, e.g. from /filogy/templates/documents/document-header.php
	 * 
	 */
	static function render_custom_css( $render_data ) {
		global $filogy_tmp_pass_css_selector_prefix; 	 

		//$render_data = self::generate_render_data();
		$render_data_custom_css = isset($render_data['custom_css']) ? $render_data['custom_css'] : null;

		
		// 2. render custom css, where the selector is a prefix, and in the field user optionally can enter the end of the selector, and in curly braces {} the properties and values
		
		// e.g. 
		// [custom_css] => Array(
		//	 [ .filo_document .widget.FILO_Widget_Invbld_Seller_Address .filogy_normal_widget .filo_headline ] => Array(
		//		[custom-css] => .h2 {color: red; background-color: white;}
		//	 )
		// )
		
		
		echo '<div id="filo-customize-manager-custom-css-container">';
		
		if ( isset($render_data_custom_css) and is_array($render_data_custom_css) )
		foreach ($render_data_custom_css as $css_selector_prefix => $selector_and_css_content0) {
			
			$css_selector_prefix_style_id = self::convert_selector2id($css_selector_prefix);
			//$css_selector_prefix_style_id = str_replace(' ', '-', $css_selector_prefix); //replace spaces to -
			
			echo '<style id="filo-customize-manager-custom-css-style_' . $css_selector_prefix_style_id . '">';
			//echo '<style>';

			if ( isset($selector_and_css_content0) and is_array($selector_and_css_content0) )
			foreach ($selector_and_css_content0 as $fix_custom_css_text => $selector_and_css_content) {

				//ALSO IMPLEMENTED IN JS: merge_css_selector_prefix_and_custom_css

				//remove php or other tags
				$css = strip_tags($selector_and_css_content);
				
				//replace all { character to ¤{. If there is not any character befor {, then this extra caracter ensures that it can be found as a string of which the prefix should be added. (At the end, the extra character will be removed, but the prefix will remain). Without this the prefix is not applied for a { character, if there is not any string before it.
				$css = str_replace('{', '¤{', $css );
				
				//remove comments
				//$css = preg_replace($pattern = '/(\/\*.*?\*\/|^\/\/.*?$)/m', $replacement = '', $subject = $css); //We remove it later
				
				//pass $css_selector_prefix parameter to the callback funtciont
				$filogy_tmp_pass_css_selector_prefix = $css_selector_prefix;
				 
				$repl = preg_replace_callback(
			        '/(\/\*.*?\*\/|^\/\/.*?$)|([^\r\n,{}]+)(,(?=[^}]*{)|\s*{)/m', 
			        	// the first capture block is the comments, this should be in the first place, because the other roules must not be applied on comments:  (\/\*.*?\*\/|^\/\/.*?$)
			        	// the following part is the roule for css selector
			        	// /m option is needed for handling the start and end of rows ^...$ (multiple rows) 
			        function ( $matches ) {
						global $filogy_tmp_pass_css_selector_prefix;
						
						//$matches contains all of the matched strings
			 
						//The different parts of regular expression in () brackets means captured blocks
						//In $matches[1], $matches[2], $matches[3] we are getting the found results of the different captured block if is there any.
						//So we have to evaluate if 1, 2, 3 is set, and if so we can modify the value and concatenate to the return string.     
						//$matches[1] is the comment (if it is a comment line or block), that must not be changed
						//$matches[2] is the css selector (if the result contains any), if it is set, we have to add the appropriate prefix and add to the return string 
						//$matches[3] is the other part of css like { and , it is also unchanged
						//$matches[0] contains the result of all capture blocks together, so it is not useful for us! 
			
						//$matches e.g.        	
						//[2016/10/10 02:44:17] filogy.php TEST $matches: Array
						//(
						//    [0] => /*higher mobile menubar*/
						//    [1] => /*higher mobile menubar*/
						//)
						//
						//[2016/10/10 02:44:17] filogy.php TEST $matches: Array
						//(
						//    [0] => #wprmenu_bar {
						//    [1] => 
						//    [2] => #wprmenu_bar 
						//    [3] => {
						//)
						
						
						//wsl_log(null, 'filogy.php TEST $matches: ' .  wsl_vartotext($matches));
						
			        	$return = '';
						
			        	if ( isset($matches[1]) ) {
			        		$return .= $matches[1];
						}
						
						if ( isset($matches[2]) ) {
							$matches[2] = $filogy_tmp_pass_css_selector_prefix . ' ' . $matches[2]; //Add prefix
							$return .= $matches[2];
						}
						
						if ( isset($matches[3]) ) {
							$return .= $matches[3];
						}

			            return $return; //return from this callbeck function
			        },
			        $css
			    ); //end of preg_replace_callback command

			    //remove the unnecessary ¤ characters that were inserted at the begnning of this function
				$repl = str_replace('¤{', '{', $repl );
						
			    			    
				echo $repl;
				
				//wsl_log(null, 'class-filo-customize-manager.php render_css $repl: ' .  wsl_vartotext($repl));
			}

			echo '</style>';

		}

		echo '</div>';
		
	}

	/**
	 * convert css selector to id
	 * 
	 * trim spaces then replace spaces to - character, remove special characters and duplicate spaces
	 * 
	 */
	static function convert_selector2id($selector) {
	 	$selector = trim($selector);
		$selector = preg_replace('/\s+/S', " ", $selector); //remove duplicate spaces (http://stackoverflow.com/questions/5539169/how-do-i-remove-extra-spaces-tabs-and-line-feeds-from-a-sentence-and-substitute)
		$selector = str_replace(' ', '-', $selector); //replace spaces to -
		//remove special characters, that cannot be contained by a html class or id (., #, ^)
		$selector = str_replace('.', '', $selector); //remove dots (.) 
		$selector = str_replace('#', '', $selector); //remove hashmarks (#)
		$selector = str_replace('^', '', $selector); //remove up arrows (^)
		return $selector;
	}
	
	/**
	 * render_jquery inline styling
	 * 
	 * This is for the preview mode, when we do not want print any css stylesheet, but print inline css into the html tags by jQuery.
	 * Because previewer apply inline css, and it does not overwrite css stylesheets, just other inline css settings.
	 * 
	 * print style code containing the customized inline styling
	 * 
	 */
	static function render_initial_jquery_inline_styles() {
	 			
		$filo_doc_option = self::get_root_value();
		$render_data = self::generate_render_data($filo_doc_option);
		//$render_data = $render_data['normal_selectors'];
		$render_data_normal = $render_data['normal_selectors'];
		
		?>
		
		<script type="text/javascript" id="filo-customize-manager-render-initial-jquery-inline-styles">
		
			( function( $ ) {

				$(document).ready(function(){
					
					<?php					
		
					if ( isset($render_data_normal) and is_array($render_data_normal) )
					foreach ($render_data_normal as $css_selector => $prop_values) {
						
						if ( isset($prop_values) and is_array($prop_values) )
						foreach ($prop_values as $css_property => $setting_value) {

							if ( ! empty($css_property) ) {
								
								wsl_log(null, 'class-filo-customize-manager.php render_initial_jquery_inline_styles $css_selector DDE: ' . wsl_vartotext( $css_selector ));
								wsl_log(null, 'class-filo-customize-manager.php render_initial_jquery_inline_styles $css_property DDE: ' . wsl_vartotext( $css_property ));
								wsl_log(null, 'class-filo-customize-manager.php render_initial_jquery_inline_styles $setting_value DDE: ' . wsl_vartotext( $setting_value ));
									
								?>
								
									var filo_css_selector = '<?php echo $css_selector ?>';
									var filo_css_property = '<?php echo $css_property ?>';
									var filo_setting_value = '<?php echo $setting_value ?>';
									
									// set css value for the actual selector/property
									$( filo_css_selector ).css(filo_css_property, filo_setting_value );
									
								<?php
							}
							
						}
						
					}
					
					// Special: document logo
					if ( isset($filo_doc_option['']['Document-General']['css_document_general_selector']['filo_logo']) ){
						$logo_url = $filo_doc_option['']['Document-General']['css_document_general_selector']['filo_logo'];
						
						?>
							$( '.filo_document #filo_logo img' ).attr( 'src', '<?php echo $logo_url; ?>' );
						<?php
						
					}
					
					// Special: document general font size 
					// Set document general font size. It is necessary to be handled special way, because of the 0 font size is applied and has to be reseted
					// This is the initial on-line version of it

					if ( isset($filo_doc_option['']['Document-General']['css_document_general_selector']['font_size']) ){
						$document_general_font_size = $filo_doc_option['']['Document-General']['css_document_general_selector']['font_size'];
					} else {
						$document_general_font_size = '12px';
					}
					
					wsl_log(null, 'class-filo-customize-manager.php render_initial_jquery_inline_styles $filo_doc_option: ' . wsl_vartotext( $filo_doc_option ));
					wsl_log(null, 'class-filo-customize-manager.php render_initial_jquery_inline_styles $document_general_font_size: ' . wsl_vartotext( $document_general_font_size ));					
					
					?>
					
					$( '.panel-grid-cell' ).css( 'font-size', '<?php echo $document_general_font_size; ?>' );
					
				});
			} )( jQuery );		

		</script>
		
		<?php self::render_custom_css( $render_data ); ?>
		
		<?php
		
	}

	/**
	 * generate_render_data
	 * 
	 * Generate style code in a structure that cannot be displayed. Thus we use this data for print css and inline jquery printing / rendering.
	 * 
	 */
	static function generate_render_data( $filo_doc_option = null ) {
				
		//saved filo_doc option e.g.: Array
		//(
		//    [fd_normal_widgets] => Array 									// PANEL: $panel_id - used for get possible selector prefixes and suffixes
		//        (
		//            [FILO_Widget_Invbld_Seller_Address] => Array			// SECTION: $section['key'] - this is the $section_dynamic_tag
		//                (
		//                    [css_widget_selector] => Array				// PARTITION (selector type): $setting_attributes['css_selector_key'] - this can be used to choose the right one from the possible selector prefixes and suffixes for the panel
		//                        (
		//                            [border_color] => #7c7c7c				//css_property / value pairs
		//                            [background-color] => #81d742
		//                        )
		//                    [css_header_selector] => Array
		//                        (
		//                            [color] => #d89031
		//                        )
		//                )
		//        )
		//)
				

		//get the appropriate option (according to filo_new_opt_name url get parameter or filo_doc_act_opt_name field)
		//$filo_doc_option = FILO_Customize_Setting::get_public_root_value();		
		if ( empty($filo_doc_option) ) {
			$filo_doc_option = self::get_root_value();
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php generate_render_data $filo_doc_option: ' . wsl_vartotext( $filo_doc_option ));
		
		//EXAMPLE: the style tag appeared here
		//echo '<style type="text/css">'; 
		
		//go through on the option levels according to the structure in above example
		
		$render_data['font_family_links'] = '';
		
		//first go through on every panel for that has any setting 
		if ( isset($filo_doc_option) and is_array($filo_doc_option) )
		foreach ($filo_doc_option as $panel_id => $panel_data) {
			
			wsl_log(null, 'class-filo-customize-manager.php generate_render_data $panel_id: ' . wsl_vartotext( $panel_id ));
			
			//get all possible selector prefix and postfix for tha actual panel
			$selector_data = self::get_panel_selector( $panel_id );
			
			wsl_log(null, 'class-filo-customize-manager.php generate_render_data $selector_data: ' . wsl_vartotext( $selector_data ));
			
			//e.g. for 'fd_normal_widgets': 
			//	$selector_data = array( 
			//		'css_cell_selector' => array(
			//			'prefix' => ' .filo_document .panel-grid-cell.cell-of-', //without space at the end //' .filo_document .Filo_Item_Table_Body .panel-grid-cell.cell-of-'
			//			'suffix' => '',
			//		),
			//		'css_widget_selector' => array(
			//			'prefix' => ' .filo_document .widget.', //without space at the end
			//			'suffix' => '',
			//		),  
			//		'css_content_selector' => array(
			//			'prefix' => ' .filo_document .widget.', //without space at the end
			//			'suffix' => ' .filo_content ',
			//		),
			//		'css_header_selector' => array(
			//			'prefix' => ' .filo_document .widget.', //without space at the end
			//			'suffix' => ' .filo_headline ',
			//		),
			//	);
			
			//if ( isset($selector_data) and is_array($selector_data)) {
						
			//go through on each section of the panel, and get css_dynamic tag and all settings for this 
			if ( isset($panel_data) and is_array($panel_data) )
			foreach ($panel_data as $section_dynamic_tag => $section_data) {
				
				wsl_log(null, 'class-filo-customize-manager.php generate_render_data $section_dynamic_tag: ' . wsl_vartotext( $section_dynamic_tag ));
				wsl_log(null, 'class-filo-customize-manager.php generate_render_data $section_data: ' . wsl_vartotext( $section_data ));
				
				if ( ! empty($section_data) ) {
				
					$used_selector_data = $selector_data;
					
					// Handle special cases
					// All widget is a special case, because it has no panel, just a section, called 'All-Widgets', and have to get $selector_data by ALL_WIDGETS keyword
					// Then $section_dynamic_tag content has to be deleted
					switch ($section_dynamic_tag) {
						case 'All-Widgets':
						
						
							//$used_selector_data = self::get_panel_selector( 'All-Widgets' ); 
							$used_selector_data = self::get_panel_selector( 'fd_normal_widgets_All-Widgets' );
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data All-Widgets: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;
	
						case 'Document-General':
						
							$used_selector_data = self::get_panel_selector( 'Document-General' ); 
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data Document-General: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;
						
						case 'All-Item-Table-Columns':
							
							$used_selector_data = self::get_panel_selector( 'fd_item_table_widgets_All-Item-Table-Columns' ); 
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data fd_item_table_widgets_all: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;
							
						case 'All-Normal-Rows':
							
							$used_selector_data = self::get_panel_selector( 'fd_row_widgets_All-Normal-Rows' ); 
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data fd_row_widgets_all: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;

						/*case 'All-Rows': //for fullwidth row settings
							
							$used_selector_data = self::get_panel_selector( 'fd_fullwidth_row_widgets_All-Rows' ); 
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data fd_fullwidth_row_widgets_All: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;*/
	
						case 'Item-Table':
							
							$used_selector_data = self::get_panel_selector( 'fd_row_widgets_Item-Table' ); 
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data fd_row_widgets_all: ' . wsl_vartotext( $used_selector_data ));
							$section_dynamic_tag = '';
							
							break;
							
					}
	
					wsl_log(null, 'class-filo-customize-manager.php generate_render_data $section_dynamic_tag: ' . wsl_vartotext( $section_dynamic_tag ));
					wsl_log(null, 'class-filo-customize-manager.php generate_render_data $section_data: ' . wsl_vartotext( $section_data ));
					
					$google_api_font_family_url = 'https://fonts.googleapis.com/css?family=';
					//print css settings for the actual selector
					if ( isset($section_data) and is_array($section_data) ) 
					foreach ($section_data as $selector_type_code => $partition_data) {
						
						wsl_log(null, 'class-filo-customize-manager.php generate_render_data $selector_type_code: ' . wsl_vartotext( $selector_type_code ));
						wsl_log(null, 'class-filo-customize-manager.php generate_render_data $panel_id 2: ' . wsl_vartotext( $panel_id ));
						
						
						//if there are css settings in the selector type, then we print the selector, and all the css settings
						if ( isset($used_selector_data[$selector_type_code]) and is_array($used_selector_data[$selector_type_code]) )
						
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $used_selector_data[$selector_type_code]: ' . wsl_vartotext( $used_selector_data[$selector_type_code] ));
							
							$css_selector = $used_selector_data[$selector_type_code]['prefix'] . $section_dynamic_tag . $used_selector_data[$selector_type_code]['suffix'];
	
							//EXAMPLE: the selector begin printed here					
							//echo $css_selector . ' { ';
							
							wsl_log(null, 'class-filo-customize-manager.php generate_render_data $css_selector CSSQQA: ' . wsl_vartotext( $css_selector ));
							
							if ( isset($partition_data) and is_array($partition_data) )
							foreach ($partition_data as $setting_property => $setting_value) {
							
								wsl_log(null, 'class-filo-customize-manager.php generate_render_data $selector_type_code: ' . wsl_vartotext( $selector_type_code ));
								wsl_log(null, 'class-filo-customize-manager.php generate_render_data $setting_property: ' . wsl_vartotext( $setting_property ));
								
								$css_property = self::get_css_property_by_selector_and_setting_type( $selector_type_code, $setting_property );

								//if we are in panel-grid-cell of a panel-table (thus an item table cell), then padding has to be applied in a lower level, because the cell background color has not as wild as the table cell (the padding causes a "frame" in table cell bacground)
								$css_selector2 = $css_selector; //we can modify $css_selector2, but $css_selector is not changed in the loop
								if ( strpos($css_selector2, ' .panel-table ') !== false and strpos($css_selector2, ' .panel-grid-cell') !== false and strpos( $css_property, 'padding' ) === 0 ) {  
									$css_selector2 = $css_selector2 . ' .filo_table_cell'; //this is a lower level (child) css element
								}
								
								
								if ( $css_property == 'background-image' ) { //in case of background image, this form has to be applied: background-image: url('my_url'); e.g. https://gist.github.com/srikat/95d118a4caa1a071dc1c, http://www.w3schools.com/cssref/css3_pr_background-size.asp, http://www.w3schools.com/cssref/css3_pr_background-size.asp
									$setting_value = 'url(' . $setting_value . ')';
								} elseif ( $css_property == 'background-color-odd' ) { // .myselector:nth-child(odd) { background: white; }; add odd to selector, and remove it from css property, e.g. http://www.w3schools.com/cssref/sel_nth-child.asp
									$css_selector2 = str_replace('.panel-grid ', '.panel-grid:nth-child(odd) ', $css_selector2);
									$css_property = 'background-color';
								} elseif ( $css_property == 'background-color-even' ) { // .myselector:nth-child(even) { background: white; }; add even to selector, and remove it from css property
									$css_selector2 = str_replace('.panel-grid ', '.panel-grid:nth-child(even) ', $css_selector2);
									$css_property = 'background-color';
								}
								
								wsl_log(null, 'class-filo-customize-manager.php generate_render_data $css_property CSSQQB: ' . wsl_vartotext( $css_property ));
								wsl_log(null, 'class-filo-customize-manager.php generate_render_data $setting_value CSSQQB: ' . wsl_vartotext( $setting_value ));
										
								//EXAMPLE: the css property and value printed here
								//echo $css_property . ': ' . $setting_value . '; ';
								
								if ( ! empty($css_property) ) { // it is for invoice free version -> if there is not any panel settings property defined for a saved css property (e.g. in free invoice version a property is not available, but the setting is saved in a pro version and imported to free version), the above algorithm create a data with empty $css_property. This is a wrong data and brokes down DOMPDF (some elements is not displayed), and make DOMPDF very slow. That is why we do not add data with empty $css_property, because it is wrong data.   

									//collect normal css selectors that contains property-value pairs, and custom css separately, because they have to be printed on different ways. 
									if ( $css_property == 'custom-css') {
										
										$render_data['custom_css'][$css_selector2][$css_property] = $setting_value;
										
									} else {
										
										$render_data['normal_selectors'][$css_selector2][$css_property] = $setting_value;
										
									}
									
								}
								
							}

						//EXAMPLE: the selector end printed here
						//echo ' }';
										
					}

				}

			}
			
		}
		
		//EXAMPLE: the style tag end printed here
		//echo '</style>';
		
		wsl_log(null, 'class-filo-customize-manager.php render_css $render_data: ' . wsl_vartotext( $render_data ));
		
		return $render_data;
		
	}

	/** 
	 * get_root_value
	 * 
	 * The get_root_value of WP_Customize_Setting class is overridden in FILO_Customize_Setting 
	 * for get the options of active skin dinamically, to be able to set these values in customizer fields when the customizer is opened/refreshed.
	 * 
	 * This function returns the options for the actual skin options of actual template. 
	 * It is also handled if the user changes the actual skin and/or template, then the appropriate url parameter is handled for getting the new option. 
	 *  
	 * The options are cleaned, the empty properties are deleted
	 */
	static function get_root_value( $default = null, $enable_cleaning = true, $is_simple = false ) {
		global $filo_get_root_value;
		
		if ( isset($filo_get_root_value) ) {
			return $filo_get_root_value;
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value $_REQUEST: ' .  wsl_vartotext( $_REQUEST ));
		
		$opt_fix_prefix = 'filo_doc_opt_';

		// Get actual template key. When we change another option, we can choose from the saved options of the actual template.
		
		// If the user choose another template name, then it is set in filo_new_template_name URL parameter,
		if ( isset($_GET['filo_new_template_name']) and $_GET['filo_new_template_name'] != '' ) {
			
			$actual_template_key = rawurlencode( wc_clean( $_GET['filo_new_template_name'] ) );  //$_GET automatically decode the encoded parameter, we need the encoded version. rawurlencode should be applied to get the same result as JavaScript encodeURI //+wc_clean 
			update_option( 'filo_document_template', $actual_template_key ); 
			
		} else {
			
			// Get the actual options name
			$actual_template_key = get_option( 'filo_document_template' );
			
		}

		$first_use = false;

		//set default for first use of the plugin		
		if ( empty($actual_template_key) ) {
			$actual_template_key = FILO_STANDARD_TEMPLATE;
			update_option( 'filo_document_template', $actual_template_key );
			$first_use = true;
		}

		wsl_log(null, 'class-filo-customize-manager.php get_root_value $actual_template_key 1: ' .  wsl_vartotext( $actual_template_key ));
		wsl_log(null, 'class-filo-customize-manager.php get_root_value $first_use 1: ' .  wsl_vartotext( $first_use ));
		
		
		// Get the name of that wp option that holds the name of the saved opiotns
		//$wp_option_name_of_actual_opt_name_of_template = 'filo_doc_act_opt_name_' . $actual_template_key;
		$wp_option_name_of_actual_opt_name_of_template = 'filo_doc_act_opt_name';
		
		wsl_log(null, 'class-filo-customize-manager.php get_root_value $wp_option_name_of_actual_opt_name_of_template: ' .  wsl_vartotext( $wp_option_name_of_actual_opt_name_of_template ));
		
		
		// If the user choose another opion name, then it is set in filo_new_opt_name URL parameter,
		// and we have to use the matching wp_option (id_base), and we also update that wp option that holds the actual opt name (here, when we open the customizer window!)  
		if ( isset($_GET['filo_new_opt_name']) and $_GET['filo_new_opt_name'] != '' ) {
			
			
			$id_base = rawurlencode( wc_clean( $_GET['filo_new_opt_name'] ) );  //$_GET automatically decode the encoded parameter, we need the encoded version. rawurlencode should be applied to get the same result as JavaScript encodeURI //+wc_clean
			update_option( $wp_option_name_of_actual_opt_name_of_template, $id_base ); 
			
		} elseif ( $first_use ) { // if we use the plugin first time, use the default skin (FILO_DEFAULT_SKIN) of standard template (FILO_STANDARD_TEMPLATE) 

			$id_base = rawurlencode( FILO_DEFAULT_SKIN );
			update_option( $wp_option_name_of_actual_opt_name_of_template, $id_base ); 
						
		} else {

			// Get the actual options name
			$actual_opt_name_of_template = get_option( $wp_option_name_of_actual_opt_name_of_template ); //wc_clean cannot be applied here, because the url encoded characters is eliminated
			
			$id_base = $actual_opt_name_of_template; 
			
		}
		
		$id_base = $opt_full_prefix = $opt_fix_prefix . $actual_template_key . '--' . $id_base; 
		wsl_log(null, 'class-filo-customize-manager.php get_root_value $id_base: ' .  wsl_vartotext( $id_base ));
		
		$options = get_option( $id_base, $default );
		
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value $options 0: ' .  wsl_vartotext( $options )); //LARGE

		//Cleaning option
		//delete empty properties, and the containing upper level elements if became empty
		if ( isset($options) and is_array($options) and $enable_cleaning ) {
			$options = self::clean_filo_doc_options( $options );
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value $options: ' .  wsl_vartotext( $options )); //big
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value json_decode(json_encode($options), $assoc = true ): ' .  wsl_vartotext( json_decode(json_encode($options), $assoc = true ) ));
		
		
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value $options JSON: ' .  wsl_vartotext(json_encode( $options ) ));
		
		// CONVERT:
		if ( isset($options['']['All-Widgets']) ) {
			$options['fd_normal_widgets']['All-Widgets'] = $options['']['All-Widgets'];
		}
		
		if ( isset($options['fd_normal_widgets']['All-Widgets']['css_document_general_selector']) ) {
			unset($options['fd_normal_widgets']['All-Widgets']['css_document_general_selector']);
		}
		
		if ( ! $is_simple ){
			$options = self::sort_filo_doc_options( $options );
		}
		
		// 3rd party plugins can add or clean opened options or e.g. unset some unused options, e.g. unset($options['fd_color_palette']['filo_color_standard_color']);
		$options = apply_filters('filo_customize_manager_get_root_value', $options, $enable_cleaning);

		//TEST RaPe:
		//unset($options['']['Document-General']['css_document_general_selector']['filo_image']);
		//unset($options['']['Document-General']['css_document_general_selector']['filo-logo']);
		//unset($options['']['Document-General']['css_document_general_selector']['filo_logo']);
				
		//wsl_log(null, 'class-filo-customize-manager.php get_root_value $options9: ' .  wsl_vartotext( $options )); //big
		
		//set the global
		$filo_get_root_value = $options;
		
		return $options;

	}

	/**
	 * change_full_url_to_filename_callback
	 * 
	 * Change filo_logo background_image values during export
	 * Change http://yoursite.com/wp-content/uploads/2017/01/abcdef.png -> abcdef.png
	 * This is needed to be able to move the exported data to another site 
	 */
	public static function change_full_url_to_filename_callback( $key, $value ) {
		
		wsl_log(null, 'class-filo-customize-manager.php change_full_url_to_filename_callback $key: ' .  wsl_vartotext( $key ));
		wsl_log(null, 'class-filo-customize-manager.php change_full_url_to_filename_callback $value: ' .  wsl_vartotext( $value ));

		//in case of filo_logo and background_image, change the full url to only the filename
		if ( in_array( $key, array('filo_logo', 'background_image') ) ) {
			$filename = pathinfo($value, PATHINFO_FILENAME);
			$extension = pathinfo($value, PATHINFO_EXTENSION);
			$value = $filename . '.' . $extension;
			
			wsl_log(null, 'class-filo-customize-manager.php change_full_url_to_filename_callback $value9: ' .  wsl_vartotext( $value ));
			
		}
		
		$return['result_value'] = $value;
		
		wsl_log(null, 'class-filo-customize-manager.php change_full_url_to_filename_callback $return: ' .  wsl_vartotext( $return ));
		
		return $return;
		
	}

	/** 
	 * sort_filo_doc_options
	 * 
	 * Customizet uses inline css during online preview. Thus css applied for every html element and not a higher level of the DOM structure.
	 * We have to apply the original settings this way, and disable the normal css script generation, because changing a setting in Customizer
	 * always results a change in inline css of the dom object. 
	 * We are loosing that method, that a higher level css settings can be overwritten on a lover level automatically. 
	 * We have to achieve by sorting css settings, and the higher level ones have to be applied first, and the lover level later, thus the lover level 
	 * css settings can overwrite the earlier printid higer level settings.
	 * 
	 * Sort the first level of the structure:
	 * 		fd_row_widgets
	 * 		fd_normal_widgets
	 * 		fd_data_table_widgets
	 * 		fd_item_table_widgets
	 * 		fd_doc_title_widgets
	 * 		''
	 * Then the second level of structure:
	 * 		filo_doc_template_custom_settings
	 * 		Document-General
	 * 		All-Normal-Rows
	 * 		Item-Table (rows)
	 * 		All-Widgets
	 * 		All-Item-Table-Columns
	 *  	(the normal rows get later, to be able to overwrite its finest data the earlier mentioned harder data)
	 *  
	 */
	static function sort_filo_doc_options( $options ) {
		
		$sort_1_level = array(
			'fd_row_widgets' => '01',
			'fd_normal_widgets' => '02',
			'fd_data_table_widgets' => '03',
			'fd_item_table_widgets' => '04',
			'fd_doc_title_widgets' => '05',
			'' => '06',
		);
		
		$sort_2_level = array(
			'filo_doc_template_custom_settings' => '01',
			'Document-General' => '02',
			'All-Normal-Rows' => '03',
			'Item-Table' => '04',  //rows
			'All-Widgets' => '05',
			'All-Item-Table-Columns' => '06',
		);		
		//sort first level
		$options = self::sort_array_by_sort_definition_array( $options, $sort_1_level );
		
		//sort second level (each second level array as a separate array)
		if ( isset($options) and is_array($options) )
		foreach ($options as $option_key => $option_value) {
			
			$option_value2 = self::sort_array_by_sort_definition_array( $option_value, $sort_2_level );
			
			$options[$option_key] = $option_value2;
			
		}
		
		return $options;
		
	}
		
	/**
	 * sort_array_by_sort_definition_array
	 * 
	 * sort an array by a definition array, that contains the original array keys, and a sort priority. 
	 */
	static function sort_array_by_sort_definition_array( $arr, $sort_definition_array ) {		
		
		//wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $arr 0: ' .  wsl_vartotext( $arr )); //big
		
		// e.g.
		//	$sort_definition_array = array(
		//		'' => '01',
		//		'my_efg_key' => '02',
		//		'my_abc_key' => '03',
		//	);				
							
		if ( isset($arr) and is_array($arr) ) {
			
			//add sorting prefixes
			foreach ($arr as $arr_key => $arr_value) {
					
				//if the arr key has a specific order number defined, then use it as prefix, otherwise use 99 prefix
				if ( isset($sort_definition_array[$arr_key] ) ) {
					$prefix = $sort_definition_array[$arr_key];
				} else {
					$prefix = '99';
				}
				
				$arr[$prefix . '¤' . $arr_key] = $arr_value; //set the new key using the prefix
				unset( $arr[$arr_key] ); //delete the old key
				
			}
			
			//wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $arr 1: ' .  wsl_vartotext( $arr )); //big
			
			//sort the array with the prefix
			ksort($arr);
			
			//wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $arr 2: ' .  wsl_vartotext( $arr )); //big
			
			//remove the prefixes
			foreach ($arr as $arr_key => $arr_value) {
				
				
				$new_arr_key = mb_substr($arr_key, 3); // remove the first 3 characters of the key 01¤abc -> abc 
				wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $arr_key: ' .  wsl_vartotext( $arr_key ));
				wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $new_arr_key: ' .  wsl_vartotext( $new_arr_key ));
				
				$arr[$new_arr_key] = $arr_value; //set the new key without the prefix
				unset( $arr[$arr_key] ); //delete the old key
				
			}
			
			//wsl_log(null, 'class-filo-customize-manager.php sort_array_by_sort_definition_array $arr 3: ' .  wsl_vartotext( $arr )); //big

		}			
		
		return $arr;
	}
	
	
	
	/** 
	 * clean_filo_doc_options
	 * 
	 * Delete empty properties, and the containing upper level elements if became empty
	 *  
	 */
	static function clean_filo_doc_options( $options ) {

		wsl_log(null, 'class-filo-customize-manager.php clean_filo_doc_options 0: ' .  wsl_vartotext( '' ));

		if ( isset($options) and is_array($options) ) {
			foreach ( $options as $panel_id => $panel_data ) {
				
				if ( isset($panel_data) and is_array($panel_data) ) {
					foreach ( $panel_data as $section_dynamic_tag => $section_data ) {
						
						if ( isset($section_data) and is_array($section_data) ) {
							foreach ( $section_data as $selector_type_code => $partition_data ) {
								
								if ( isset($partition_data) and is_array($partition_data) ) {
									foreach ( $partition_data as $setting_property => $setting_value ) {
										
										if ( empty($setting_value) or $setting_value == '' or $setting_value == null ) {
								
											wsl_log(null, 'class-filo-customize-manager.php clean_filo_doc_options unset: ' .  wsl_vartotext( $panel_id . '+' . $section_dynamic_tag . '+' . $selector_type_code . '+' . $setting_property ));
											unset($options[$panel_id][$section_dynamic_tag][$selector_type_code][$setting_property]);
										}
									}
									$setting_property = null; // set it null for the next loop should not have this value
								} //end if 
							}
							if ( empty($options[$panel_id][$section_dynamic_tag][$selector_type_code]) ) {
			
								wsl_log(null, 'class-filo-customize-manager.php clean_filo_doc_options $panel_id: ' .  wsl_vartotext( $panel_id ));
								//wsl_log(null, 'class-filo-customize-manager.php get_root_value $section_dynamic_tag: ' .  wsl_vartotext( $section_dynamic_tag ));
								//wsl_log(null, 'class-filo-customize-manager.php get_root_value $selector_type_code: ' .  wsl_vartotext( $selector_type_code ));
								//wsl_log(null, 'class-filo-customize-manager.php get_root_value $options[$panel_id][$section_dynamic_tag][$selector_type_code]: ' .  wsl_vartotext( $options[$panel_id][$section_dynamic_tag][$selector_type_code] ));
								unset($options[$panel_id][$section_dynamic_tag][$selector_type_code]);
							}
							$selector_type_code = null; // set it null for the next loop should not have this value = null; // set it null for the next loop should not have this value
						} //end if
					}
					if ( empty($options[$panel_id][$section_dynamic_tag]) ) {
						unset($options[$panel_id][$section_dynamic_tag]);
					}
					$section_dynamic_tag = null; // set it null for the next loop should not have this value = null; // set it null for the next loop should not have this value
				} //end if
				
			}
			if ( empty($options[$panel_id]) ) {
				unset($options[$panel_id]);
			}
			$panel_id = null; // set it null for the next loop should not have this value = null; // set it null for the next loop should not have this value
		} //end if
		//end of cleaning option
			
		return $options;
		
	}

	/**
	 * get_css_property_by_selector_and_setting_type
	 * 
	 * e.g. selector=css_header_selector, setting_type=background_color ===> return background-color (using - inside of _) 
	 */
	static function get_css_property_by_selector_and_setting_type( $css_selector_key, $setting_property ) {
		
		//convert selector key to partition key, e.g. 'css_widget_selector' -> 'widget_partition'  (so 'css_XXX_selector' -> 'XXX_partition' )
		
		$partition_sort_id = str_replace('css_', '', $css_selector_key); // remove css_
		$partition_sort_id = str_replace('_selector', '', $partition_sort_id);	// remove _selector
		
		$partition_id = $partition_sort_id . '_partition'; // add _partition
		
		wsl_log(null, 'class-filo-customize-manager.php get_css_property_by_selector_and_setting_type $partition_id: ' . wsl_vartotext( $partition_id ));
		
		$partition_and_setting_type = $partition_sort_id . '_' . $setting_property;
		
		wsl_log(null, 'class-filo-customize-manager.php get_css_property_by_selector_and_setting_type $partition_and_setting_type: ' . wsl_vartotext( $partition_and_setting_type ));
		
		$partition_settings_data = self::get_partition_settings_data();
		
		//wsl_log(null, 'class-filo-customize-manager.php get_css_property_by_selector_and_setting_type $partition_settings_data: ' . wsl_vartotext( $partition_settings_data ));
		
		//e.g. $partition_settings_data:
		//	'header_partition' => array(										//1. partiton_key 
		//		header_background_color => array(								//2. partition + setting_type
		//			'key_prefix' => 'header_',
		//			'setting_type' => 'background_color',
		//			'css_property' => 'background-color',
		//			'label' => __('Header Background Color' , 'filo_text'),
		//			'description' => __('' , 'filo_text'),
		//			'control_type' => 'color',
		//			'default_value' => '',
		//			'measurment_unit' => 'NA',
		//			'css_selector_key' => 'css_header_selector',
		//			'priority' => '80',
		//		),
		//		.......... => array(
		// 	 	),		
		//	),
		
		
		//get css property for the partition / partition+settingtype
		if ( isset($partition_settings_data[$partition_id][$partition_and_setting_type]['css_property']) ) {
			$css_property = $partition_settings_data[$partition_id][$partition_and_setting_type]['css_property'];
		} else {
			$css_property = null;
		}

		/*
		foreach ($partition_settings_data as $partioion_key => $partition_value) {
			
			foreach ($partition_settings_data[$partition_id] as $control_id => $control_data) {
				if ( $control_data['css_selector_key'] == $css_selector_key and $control_data['setting_type'] == $setting_property ) {
					return $control_data['css_property'];
				}
			}
				
		}
		*/
		return $css_property;
	}


	/**
	 * get_panel_selector
	 * 
	 * Register all possible css selectors for the panel (e.g. row, widget, content and header selectors )
	 */
	static function get_panel_selector( $panel_id ) {
		
		switch ($panel_id) {
			
			case 'fd_row_widgets':
				$selector_data = array(
					'css_row_selector' => array(
						'prefix' => ' .filo_document .panel-grid#',
						'suffix' => ' ',	
					),
					'css_fullwidth_row_selector' => array(
						'prefix' => ' .filo_document #panel-fullwidth-grid-wrapper-',
						'suffix' => ' ',
					),
				);		
				break;

			/*case 'fd_fullwidth_row_widgets':
				$selector_data = array(
					'css_fullwidth_row_selector' => array(
						'prefix' => ' .filo_document #panel-fullwidth-grid-wrapper-',
						'suffix' => ' ',
					),
				);		
				break;*/


			//SPECIAL: All-Normal-Rows
			case 'fd_row_widgets_All-Normal-Rows': 
				$selector_data = array(
					'css_row_selector' => array(
						'prefix' => ' .filo_document .panel-grid', //without # at the and, because no more selection is needed (because it is for all grid lines)
						'suffix' => ' ',
					),
					'css_fullwidth_row_selector' => array(
						'prefix' => ' .filo_document .panel-fullwidth-grid-wrapper', //without # at the and, because no more selection is needed (because it is for all grid lines)
						'suffix' => ' ',
					),
					
				);		
				break;

			/*
			//SPECIAL: All-Rows - fullwidth settings
			case 'fd_fullwidth_row_widgets_All-Rows': 
				$selector_data = array(
					'css_fullwidth_row_selector' => array(
						'prefix' => ' .filo_document .panel-fullwidth-grid-wrapper', //without # at the and, because no more selection is needed (because it is for all grid lines)
						'suffix' => ' ',
					),
				);		
				break;*/


			//SPECIAL: Item-Table (for all item tables, usually only one item table exists, thus we have not got separate selectors for each individual item table)
			case 'fd_row_widgets_Item-Table': 
				$selector_data = array(
					'css_row_selector' => array(
						'prefix' => ' .filo_document .panel-table-grid', //This is not really a row selector, it selects the wrapper divs of whole item data table (but this is a similar unit than a row (grid)) 
						'suffix' => ' ',
					),
					'css_fullwidth_row_selector' => array(
						'prefix' => ' .filo_document .panel-fullwidth-grid-wrapper.panel-table-fullwidth-grid-wrapper', //without # at the and, because no more selection is needed (because it is for all grid lines)
						'suffix' => ' ',
					),
				);		
				break;

			case 'fd_normal_widgets': 
				$selector_data = array( 
					'css_cell_selector' => array(
						'prefix' => ' .filo_document .panel-grid-cell.cell-of-', //without space at the end //' .filo_document .Filo_Item_Table_Body .panel-grid-cell.cell-of-'
						'suffix' => '',
					),
					'css_widget_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget',
					),  
					'css_content_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget .filo_content ',
					),
					'css_header_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget .filo_headline ',
					),
					'css_widget_layout_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget .filo_widget_part ',
					),  
					
				);
				break;

			case 'fd_normal_widgets_All-Widgets':
			//case 'All-Widgets':
			//case 'ALL_WIDGETS': 
				$selector_data = array(
					/*'css_document_general_selector' => array(
						'prefix' => ' .filo_document ', 
						'suffix' => '',
					),*/
					'css_cell_selector' => array(
						'prefix' => ' .filo_document .panel-grid-cell ', //with space at the end //' .filo_document .Filo_Item_Table_Body .panel-grid-cell.cell-of-'
						'suffix' => '',
					),
					'css_widget_selector' => array(
						'prefix' => ' .filo_document .widget ', //with space at the end
						'suffix' => ' .filogy_normal_widget ',
					),  
					'css_content_selector' => array(
						'prefix' => ' .filo_document .widget ', //with space at the end
						'suffix' => ' .filogy_normal_widget .filo_content  ',  //'suffix' => ' .filogy_normal_widget .filo_content  ',
					), 
					'css_header_selector' => array(
						'prefix' => ' .filo_document .widget ', //with space at the end
						'suffix' => ' .filogy_normal_widget .filo_headline ', //.filogy_data_table_widget
					),
					'css_widget_layout_selector' => array(
						'prefix' => ' .filo_document .widget ', //with space at the end
						'suffix' => ' .filogy_normal_widget .filo_widget_part ',
					),  
					
				);
				break;
			
			case 'fd_doc_title_widgets': 
				$selector_data = array( 
					'css_cell_selector' => array(
						'prefix' => ' .filo_document .panel-grid-cell.cell-of-', //without space at the end //' .filo_document .Filo_Item_Table_Body .panel-grid-cell.cell-of-'
						'suffix' => '',
					),
					'css_widget_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget',
					),  
					'css_content_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_normal_widget .filo_content ',
					),
				);
				break;

			
			case 'fd_data_table_widgets': 
				$selector_data = array( 
					'css_cell_selector' => array(
						'prefix' => ' .filo_document .panel-grid-cell.cell-of-', //without space at the end //' .filo_document .Filo_Item_Table_Body .panel-grid-cell.cell-of-'
						'suffix' => '',
					),
					'css_widget_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filogy_data_table_widget', 
					),  
					'css_data_table_content_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filo_content ', // earlier we used .filogy_data_table at the end because we have to override the table level user agent properties
					),
					'css_header_selector' => array(
						'prefix' => ' .filo_document .widget.', //without space at the end
						'suffix' => ' .filo_headline ',
					),
					'css_data_table_selector' => array(
						'prefix' => ' .filo_document .widget.',
						'suffix' => ' .filogy_data_table_widget .filogy_data_table ',
					),
					'css_data_table_label_cell_selector' => array(
						'prefix' => ' .filo_document .widget.', 
						'suffix' => ' .filogy_data_table_widget .filogy_data_table .table_label ',
					),
					'css_data_table_value_cell_selector' => array(
						'prefix' => ' .filo_document .widget.', 
						'suffix' => ' .filogy_data_table_widget .filogy_data_table .table_value',
					),
				);
				break;
				
			case 'fd_item_table_widgets': 
				$selector_data = array( 
					// Not used: css_cell_selector, css_widget_selector, css_content_selector, css_header_selector
					'css_item_table_header_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Header.panel-grid .panel-grid-cell.cell-of-', 
						'suffix' => '',
					),
					'css_item_table_body_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Body.panel-grid .panel-grid-cell.cell-of-', 
						'suffix' => '',
					),			
					'css_item_table_footer_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Footer.panel-grid .panel-grid-cell.cell-of-', 
						'suffix' => '',
					)
				);
				break;

			//SPECIAL: All-Item-Table-Columns
			//This is a special (not real) panel id for All-Item-Table-Columns
			case 'fd_item_table_widgets_All-Item-Table-Columns': 
				$selector_data = array( 
					// Not used: css_cell_selector, css_widget_selector, css_content_selector, css_header_selector
					'css_item_table_selector' => array(
						'prefix' => ' .filo_document .panel-table ', 
						'suffix' => '',
					),
					'css_item_table_header_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Header.panel-grid ', // .panel-grid-cell must not be used because Filo_Item_Table_Header contains only panel-grid-cell-s, and we have to apply this "all" setting to the header itself, instead of the individual cells. Otherwise the cell setting overwrite this, and after cleaning an individual cell setting, this setting is lost.  //'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Header .panel-grid-cell ', 
						'suffix' => ' .panel-grid-cell ',
					),
					'css_item_table_body_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Body.panel-grid ', //'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Body .panel-grid-cell ',
						'suffix' => ' .panel-grid-cell ',
					),			
					'css_item_table_footer_cell_selector' => array(
						'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Footer.panel-grid ',  //'prefix' => ' .filo_document .panel-table .Filo_Item_Table_Footer .panel-grid-cell ',
						'suffix' => ' .panel-grid-cell ',
					)
				);
				break;
			
			case 'Document-General':
				$selector_data = array(
					'css_document_general_selector' => array(
						'prefix' => ' .filo_document ', 
						'suffix' => '',
					),
				);		
				break;			
			
			default:
				$selector_data = null;
				break;
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php get_panel_selector $selector_data: ' . wsl_vartotext( $selector_data ));
		
		return $selector_data;
	}
	
	/**
	 * get_panel_category
	 * 
	 * Return normal, item_table, data_table or doc_title categories, according to the panel_id.
	 * This is used for displaying menu accordion icons.
	 */
	static function get_panel_category($panel_id) {
	
		$panel_categories = apply_filters( 'filo_customize_manager_panel_categories', array(
			'' => 'normal',	
			'fd_row_widgets' => 'normal',
			'fd_row_widgets_All-Normal-Rows' => 'normal',
			'fd_fullwidth_row_widgets' => 'normal',
			'fd_fullwidth_row_widgets_All-Normal-Rows' => 'normal',
			'fd_row_widgets_Item-Table' => 'item_table',
			'fd_normal_widgets' => 'normal',
			'fd_doc_title_widgets' => 'doc_title',
			'fd_data_table_widgets' => 'data_table',
			'fd_item_table_widgets' => 'item_table',
			'fd_item_table_widgets_All-Item-Table-Columns' => 'item_table',
			'All-Widgets' => 'normal',
			'Document-General' => 'normal',
		));
		
		if ( isset($panel_categories[$panel_id]) ) {
			return $panel_categories[$panel_id];
		} else {
			return null;
		}

	}
	
	
	/**
	 * Define partitions settings (controls) here.
	 * 
	 * All possible settings (controls) are defined here once,
	 * then it can be used for all panels.
	 * 
	 * The real code is in the included in filo-partition-settings-data.php file, that is a generated code. 
	 */
	static function get_partition_settings_data() {
		global $filogy_partition_settings_data;
		
		if ( isset($filogy_partition_settings_data) ) {
			return $filogy_partition_settings_data;
		}

		//-------------------------------------
		// INCLUDE PARTITON SETTINGS DATA		
		// Generated by customizer_fields_generator_v11.xlsx - SECOND block (T, U, V cols)
		//-------------------------------------
		
		include 'filo-partition-settings-data.php';


		//wsl_log(null, 'class-filo-customize-manager.php get_partition_settings_data $partition_settings_data 0: ' . wsl_vartotext( $partition_settings_data )); //LARGE


		//REMOVE THE SETTINGS CONTROLS THAT DO NOT HAVE TO BE DISPLAYED USING THE ACTUAL SETTING MODE
		
		$customize_setting_mode = get_option('filo_customize_setting_mode');
		
		if ( empty($customize_setting_mode) ) {
			$customize_setting_mode = 'advanced'; //set the default mode
		} 

		//set the field name that have to be checked whether the actual setting control has to be displayed
		$customize_setting_mode_selector_field = 'used_if_' . $customize_setting_mode; //e.g. 'used_if_advanced'

		//remove the settings that do not have to displayed for the actual customize_setting_mode
		if ( isset($partition_settings_data) and is_array($partition_settings_data) )		
		foreach ( $partition_settings_data as $partition_key => $partition_data ) {

			if ( isset($partition_data) and is_array($partition_data) )
			foreach ( $partition_data as $partition_setting_code => $partition_setting_data ) {

				//if used_if_basic or used_if_advanced or other set field does not exist or the value is false 
				//then delete this field
				if ( ! isset($partition_setting_data[$customize_setting_mode_selector_field]) or ! $partition_setting_data[$customize_setting_mode_selector_field] ) {

					unset($partition_settings_data[$partition_key][$partition_setting_code]);
				}
				
			}
			
		}

		//wsl_log(null, 'class-filo-customize-manager.php get_partition_settings_data $partition_settings_data: ' . wsl_vartotext( $partition_settings_data ));

		$filogy_partition_settings_data = $partition_settings_data;

		return $filogy_partition_settings_data;

	}
		
	/**
	 * Defined options for select fields
	 */
	 
	 static function get_font_size_choices() {
        $return = array(
        	'' => '',
			'xx-small' 	=> __('xx-small', 'filo_text'),
			'x-small' 	=> __('x-small', 'filo_text'),
			'small' 	=> __('small', 'filo_text'),
			'medium' 	=> __('medium', 'filo_text'),
			'large' 	=> __('large', 'filo_text'),
			'x-large' 	=> __('x-large', 'filo_text'),
			'xx-large' 	=> __('xx-large', 'filo_text'),
		);
		return $return;
	}
	 
	static function get_font_weight_choices() {
        $return = array(
        		'' => '',
				'normal' 	=> __('normal', 'filo_text'),
				'bold' 		=> __('bold', 'filo_text'),
				'bolder' 	=> __('bolder', 'filo_text'),
				'lighter' 	=> __('lighter', 'filo_text'),
				100 		=> 100,
				200 		=> 200,
				300 		=> 300,
				400 		=> 400,
				500 		=> 500,
				600 		=> 600,
				700 		=> 700,
				800 		=> 800,
				900 		=> 900,
		);
		return $return;
	}

	static function get_font_style_choices() {
        $return = array(
			'' => '',
			'inherit' => __('inherit', 'filo_text'),
			'italic' => __('italic', 'filo_text'),
			'normal' => __('normal', 'filo_text'),
			'oblique' => __('oblique', 'filo_text'),
		);
		return $return;
	}

	static function get_text_decoration_choices() {
        $return = array(
			'' => '',
			'none' => __('none', 'filo_text'),
			'inherit' => __('inherit', 'filo_text'),
			'line-through' => __('line-through', 'filo_text'),
			'overline' => __('overline', 'filo_text'),
			'underline' => __('underline', 'filo_text'),
		);
		return $return;
	}

	static function get_text_transform_choices() {
        $return = array(
			'' => '',
			'none' => __('none', 'filo_text'),
			'inherit' => __('inherit', 'filo_text'),
			'capitalize' => __('capitalize', 'filo_text'),
			'uppercase' => __('uppercase', 'filo_text'),
			'lowercase' => __('lowercase', 'filo_text'),
		);
		return $return;
	}
	
	static function get_text_align_choices() {
        $return = array(
			'' => '',
			'center' => __('center', 'filo_text'),
			'justify' => __('justify', 'filo_text'),
			'inherit' => __('inherit', 'filo_text'),
			'left' => __('left', 'filo_text'),
			'right' => __('right', 'filo_text'),
		);
		return $return;
	}

	static function get_border_style_choices() {
        $return = array(
			'' => '',
			'none' => __('none', 'filo_text'),
			'hidden' => __('hidden', 'filo_text'),
			'dotted' => __('dotted', 'filo_text'),
			'dashed' => __('dashed', 'filo_text'),
			'solid' => __('solid', 'filo_text'),
			'double' => __('double', 'filo_text'),
			'groove' => __('groove', 'filo_text'),
			'ridge' => __('ridge', 'filo_text'),
			'inset' => __('inset', 'filo_text'),
			'outset' => __('outset', 'filo_text'),
			'initial' => __('initial', 'filo_text'),
			'inherit' => __('inherit', 'filo_text'),  
		);
		return $return;
	}

	static function get_border_collapse_choices() {
        $return = array(
			'' => '',
			'collapse' => __('collapse', 'filo_text'),
			'separate' => __('separate', 'filo_text'), 
			'initial' => __('initial', 'filo_text'),
		);
		return $return;
	}
	
	static function get_vertical_align_choices() {
        $return = array(
			'' => '',
			'top' => __('top', 'filo_text'),
			'middle' => __('middle', 'filo_text'), 
			'bottom' => __('bottom', 'filo_text'),
			'initial' => __('initial', 'filo_text'),
		);
		return $return;
	}

	static function get_background_repeat_choices() {
        $return = array(
			'' => '',
			'no-repeat' => __('no repeat', 'filo_text'),
			'repeat' => __('repeat', 'filo_text'), 
			'repeat-x' => __('repeat-x', 'filo_text'),
			'repeat-y' => __('repeat-y', 'filo_text'),
			'round' => __('round', 'filo_text'),
			'space' => __('space', 'filo_text'),
			'initial' => __('initial', 'filo_text'),
		);
		return $return;
	}


	static function get_filo_document_size_choices() {
        $return = array(
			'a3'           => __( 'A3', 'filo_text' ),
			'a4'           => __( 'A4', 'filo_text' ),
			'a5'           => __( 'A5', 'filo_text' ),
			'letter'       => __( 'Letter', 'filo_text' ),
			'legal'        => __( 'Legal', 'filo_text' ),
		);
		return $return;
	}

	static function get_filo_document_orientation_choices() {
        $return = array(
			'portrait'		=> __( 'Portrait', 'filo_text' ),
			'landscape'		=> __( 'Landscape', 'filo_text' ),
		);
		return $return;
	}

	static function get_color_palette_elements() {
        $return = array(
        
			'filo_color_accent_color' => __('Accent Color', 'filo_text'),
			'filo_color_primary_color' => __('Primary Color', 'filo_text'),
			//'button_position' => 'button_position', //place of Generate_Color_Palette_Button
			'filo_color_dark_primary_color' => __('Dark Primary Color', 'filo_text'),
			'filo_color_light_primary_color' => __('Light Primary Color', 'filo_text'),

			'filo_color_accent_text_color' => __('Text on Accent Color', 'filo_text'),
			'filo_color_primary_text_color' => __('Text on Primary Color', 'filo_text'),
			'filo_color_dark_primary_text_color' => __('Text on Dark Primary Color', 'filo_text'),
			'filo_color_light_primary_text_color' => __('Text on Light Primary Color', 'filo_text'),
			
			'filo_color_main_text_color' => __('Main Text Color', 'filo_text'),
			'filo_color_secondary_text_color' => __('Secondary Text Color', 'filo_text'),
			'filo_color_delicate_color' => __('Delicate Color', 'filo_text'),
		
			/*	
			'filo_color_standard_color'=>'Standard Color',
			'filo_color_standard_background'=>'Standard Background',
			'filo_color_standard_border'=>'Standard Border',
			
			'filo_color_heading_color'=>'Heading Color',
			'filo_color_heading_background'=>'Heading Background',
			'filo_color_heading_border'=>'Heading Border',
			
			'filo_color_highlight_color_1'=>'Highlight Color 1',
			'filo_color_highlight_background_1'=>'Highlight Background 1',
			'filo_color_highlight_border_1'=>'Highlight Border 1',
			'filo_color_highlight_color_2'=>'Highlight Color 2',
			'filo_color_highlight_background_2'=>'Highlight Background 2',
			'filo_color_highlight_border_2'=>'Highlight Border 2',
			'filo_color_highlight_color_3'=>'Highlight Color 3',
			'filo_color_highlight_background_3'=>'Highlight Background 3',
			'filo_color_highlight_border_3'=>'Highlight Border 3',
			'filo_color_highlight_color_4'=>'Highlight Color 4',
			'filo_color_highlight_background_4'=>'Highlight Background 4',
			'filo_color_highlight_border_4'=>'Highlight Border 4',
			*/
			
			'filo_color_1' => __('My Color', 'filo_text') . ' 1', 
			'filo_color_2' => __('My Color', 'filo_text') . ' 2',
			'filo_color_3' => __('My Color', 'filo_text') . ' 3',
			'filo_color_4' => __('My Color', 'filo_text') . ' 4',
			'filo_color_5' => __('My Color', 'filo_text') . ' 5',
			'filo_color_6' => __('My Color', 'filo_text') . ' 6',
			'filo_color_7' => __('My Color', 'filo_text') . ' 7',
			'filo_color_8' => __('My Color', 'filo_text') . ' 8',
			'filo_color_9' => __('My Color', 'filo_text') . ' 9',
			'filo_color_10' => __('My Color', 'filo_text') . ' 10',
		);
		return $return;
	}
	
	static function get_display_choices() {
        $return = array(
			'' => '',
			'initial' => __('initial', 'filo_text'),
			'block' => __('normal blocks (under each other)', 'filo_text'),
			'inline-block' => __('inline blocks (side by side)', 'filo_text'),
		);
		return $return;
	}	

	/**
	 * get_template_panels_data
	 * 
	 * The customizer settings are displayed according to the actual template or SiteOrigin template parts of documents.
	 * 
	 */ 
	static function get_template_panels_data() {
	
		wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data 0: ' . wsl_vartotext( '' ));
	
		//get the actual template registration data
		global $filo_document_templates;

		//this action register all the document templates (this action is not called earlier, thus we should call it here)
		do_action( 'filo_register_document_template');
	
		wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $filo_document_templates: ' .  wsl_vartotext($filo_document_templates));
		 
		$actual_template_key = self::get_actual_template_key();
		//$actual_template_key = wc_clean (get_option( 'filo_document_template' ));
		
		//if no templat key option is set or the actual template value array is not exists (e.g. deactivate the plugin of which template is set), then use filo_standard_template
		if ( $actual_template_key == '' or !is_array($filo_document_templates[$actual_template_key]) )  
			$actual_template_key = FILO_STANDARD_TEMPLATE; //set default //'filo_standard_template'

		//set template panels data from the template panel definition file or from the SiteOrigin definition data
		$template_panels_data = array();
		
		wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $actual_template_key: ' . wsl_vartotext( $actual_template_key ));
		wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $filo_document_templates[$actual_template_key]: ' . wsl_vartotext( $filo_document_templates[$actual_template_key] ));

		//include template custom settings if it is registered for the actual template
		if (isset($filo_document_templates[$actual_template_key]['template_custom_settings']) ) {
			$template_custom_settings_definition_file = $filo_document_templates[$actual_template_key]['default_path'] . $filo_document_templates[$actual_template_key]['template_custom_settings'];
			wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $template_custom_settings_definition_file: ' . wsl_vartotext( $template_custom_settings_definition_file ));
			include_once($template_custom_settings_definition_file); 
		}

		//if template_panels_data is set in template registration data ($filo_document_templates), then it is a normal template (not SiteOrigin template), and use the panel definition file inside of the template 
		if (isset($filo_document_templates[$actual_template_key]['template_panels_data']) ) {	
			
			$template_panels_data_definition_file = $filo_document_templates[$actual_template_key]['default_path'] . $filo_document_templates[$actual_template_key]['template_panels_data'];

			//set $template_panels_data
			include($template_panels_data_definition_file); 

			//wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $template_panels_data NOT SO: ' . wsl_vartotext( $template_panels_data )); //LARGE			
		
			if ( isset($template_panels_data['grids']) and is_array($template_panels_data['grids']) )
			foreach ($template_panels_data['grids'] as $grid_key => $grid) {
				$template_panels_data2['grids'][$grid_key]['style']['id'] = $grid['id'];
			}
			
			if ( isset($template_panels_data['widgets']) and is_array($template_panels_data['widgets']) )
			foreach ($template_panels_data['widgets'] as $widget_key => $widget) {
				$template_panels_data2['widgets'][$widget_key]['panels_info']['class'] = $widget['class'];
				$template_panels_data2['widgets'][$widget_key]['panels_info']['style']['widget_code'] = $widget['widget_code'];
			}
			
			$template_panels_data = $template_panels_data2;
			
			//wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $template_panels_data: ' . wsl_vartotext( $template_panels_data )); //LARGE			
		
		} else { //get panel data from SiteOrigin template if template_panels_data is NOT set in template registration data. 
		
			$template_panels_data = self::get_so_panels_data(); //$so_panels_data
			wsl_log(null, 'class-filo-customize-manager.php get_template_panels_data $template_panels_data SO: ' . wsl_vartotext( $template_panels_data ));
		
		}
		
		return $template_panels_data;
		
	}	
	
	/**
	 * get_so_panels_data
	 * 
	 * The customizer settings are displayed according to the SiteOrigin parts of custom bulid documents.
	 * 
	 */ 
	static function get_so_panels_data() {
	
		global $post;
	
		//1. if filo_invoice_template_id GET parameter is set, than use that
		$invbld_template_id = wc_clean( $_GET['filo_invoice_template_id'] ); //+wc_clean
		
		wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data From $_SERVER[HTTP_REFERER]: ' .  wsl_vartotext($_SERVER["HTTP_REFERER"])); //RaPe
		wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data To $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI]: ' .  wsl_vartotext($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])); //RaPe
		wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data $_REQUEST: ' .  wsl_vartotext($_REQUEST)); //RaPe
		
		//2. If $invbld_template_id is not set according to above
		//Get filo_invoice_template_id from caller URL of the ajax call
		//in case of customize_save ajax action call, the ajax do not have the original $_GET parameters (e.g filo_invoice_template_id)
		//so we can parse the original caller URL (HTTP_REFERER) to get the original $GET parameters
		if ( empty($invbld_template_id) and isset($_SERVER['HTTP_REFERER']) ) {
		//get post 'sub argument' of return argument of the URL if we are on customizer page  
			
			$parsed_url = parse_url( wc_clean( $_SERVER['HTTP_REFERER'] ) ); // parsed url of ajax caller_page //+wc_clean
			
			if ( isset($parsed_url['query']) ) { //this part contains the parameters
				
				parse_str($parsed_url['query'], $parsed_url_query_args);
				
				if ( isset($parsed_url_query_args['filo_invoice_template_id'])) {
					$invbld_template_id = $parsed_url_query_args['filo_invoice_template_id'];
				}
				//if ( isset($parsed_url_query_args['filo_sample_order_id'])) {
				//	$filo_sample_order_id = $parsed_url_query_args['filo_sample_order_id'];
				//}
				
			}
			
			//e.g. 
			//	$_GET: Array
			//		(
			//		    [autofocus] => Array
			//		        (
			//		            [control] => color1
			//		        )
			//		
			//		    [filo_usage] => doc
			//		    [return] => /wp-admin/post.php?post=2943&action=edit
			//		)
			//	
			//$parsed_return_url: Array
			//	(
			//	    [path] => /wp-admin/post.php
			//	    [query] => post=2943&action=edit
			//	)
			//	
			//$parsed_return_url_query_args: Array
			//	(
			//	    [post] => 2943
			//	    [action] => edit
			//	)
			
			
		}

		//3. If $invbld_template_id is not set according to above
		//get the actual template, and in case of SO templates the template base data in $filo_document_templates contains invbld_template_id 
		if ( empty($invbld_template_id) ) {
			
			global $filo_document_templates; //every template insert their own parameters to this global variable
			
			//$template_key = wc_clean (get_option( 'filo_document_template' ));
			$template_key = self::get_actual_template_key();
			
			//if a SO template is active, then for its template key in $filo_document_templates, the id of the template post is set in 'invbld_template_id' key
			//we get the SO template post and it's link
			if ( isset($filo_document_templates[$template_key]['invbld_template_id']) ) {
				
				$invbld_template_id = $filo_document_templates[$template_key]['invbld_template_id']; //e.g. 2943
				
				//wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data $invbld_template_id: ' . wsl_vartotext( $invbld_template_id ));
			}
			
		}

		wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data $invbld_template_id: ' . wsl_vartotext( $invbld_template_id ));

		$so_panels_data = ! empty($invbld_template_id) ? get_post_meta( $invbld_template_id, 'panels_data', true ) : null;
		
		wsl_log(null, 'class-filo-customize-manager.php get_so_panels_data $so_panels_data: ' . wsl_vartotext( $so_panels_data ));
		
		return $so_panels_data;
	}			
		
	
	static function add_saving_option_fields() {
		global $wp_customize;
	
		$opt_fix_prefix = 'filo_doc_opt_';
		$section_key = 'filo_doc_saving_options';

		$wp_customize->add_section(
	        $section_key,
	        array(
	            'title' => __('Open / Save Options' , 'filo_text'),
	            //'description' => __('' , 'filo_text'),
	            'priority' => 0,
	        )
	    );	

		$priority = 0;

		// 1.  Actual template: We have one actual template, that can be selected and stored in a single option: filo_document_template
		// 2. - Actual scheme of template: for every template we can have an actual scheme, so we have as many options as many template: filo_doc_act_opt_name_<TEMP_NAME>, e.g. filo_doc_act_opt_name_filo_standard_template
		// 3. - - Scheme data: for every template we can have multiple schemes: filo_doc_opt_filo__<TEMP_NAME>-- <SCHEMA_NAME>, e.g. filo_doc_opt_filo_standard_template--Bravo1Schema
		 
		//-------

		// PREPARE:
		
		// 1. Actual template PREPARE
		// Every template add himself to $filo_document_templates global variable
		global $filo_document_templates;
				
		//wsl_log(null, 'class-filo-customize-manager.php add_saving_option_fields $filo_document_templates: ' .  wsl_vartotext( $filo_document_templates ));
		
		//generate an array containing template key and display name pairs. The source of it is the $filo_document_templates global variable, that is filled by the plugins that register templates
		$doc_template_list = array();		
		if ( isset($filo_document_templates) and is_array($filo_document_templates) )
		foreach ( $filo_document_templates as $filo_document_template_key => $filo_document_template_values ) {
			$doc_template_list[$filo_document_template_key] = $filo_document_template_values['display_name'];			
		}
		
		wsl_log(null, 'class-filo-customize-manager.php add_saving_option_fields $doc_template_list: ' .  wsl_vartotext( $doc_template_list ));


		// 2. Actual scheme PREPARE

		// Get actual template key. When we change another option, we can choose from the saved options of the actual template.
		//$actual_template_key = wc_clean (get_option( 'filo_document_template' ));
		$actual_template_key = self::get_actual_template_key();

		//set prefix, e.g. filo_doc_opt_filo_standard_template--	  ----> filo_doc_opt_filo_standar	d_template--Schema1
		$opt_full_prefix = $opt_fix_prefix . $actual_template_key . '--';
	
		$saved_option_names = self::get_saved_option_names( $opt_full_prefix );
		
		//if there is not any saved option yet for the template, then use the template dafault name
		if ( empty($saved_option_names) ) {
			$saved_option_names[] = 'Template Default';
		}
		
		//create an option name list, that array keys is the url encoded and option names are url decoded. This keys can be passed as post url parameters.
		$saved_option_list = array();
		foreach ($saved_option_names as $saved_option_name) {

			//remove prefix from saved option names, because prefix do not have to display in select list
			$saved_option_name = str_replace($search = $opt_full_prefix, $replace = '', $subject = $saved_option_name);
			
			// urlencode the value, and remove "filoprotect_" prefix
			$saved_option_list[$saved_option_name] = str_replace( 'filoprotect_', '', urldecode($saved_option_name) );
			 
		}		
		
		wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $actual_template_key: ' .  wsl_vartotext( $actual_template_key ));
		wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $saved_option_names: ' .  wsl_vartotext( $saved_option_names ));
		wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $saved_option_list: ' .  wsl_vartotext( $saved_option_list ));
		
		//-------			
			
		// EXECUTE:
		
		// 1. Actual template EXECUTE
					
		$setting_id = 'filo_document_template';
		
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: select
		$wp_customize->add_control( $setting_id, array(
			'label' => __('Actual Template' , 'filo_text'),
			//'description' => '',
			'section'  => $section_key,
			'type' => 'select',
			'choices' => $doc_template_list,
			'priority' => $priority,
		) );


		// 2. Actual skin EXECUTE
			
		//$setting_id = 'filo_doc_act_opt_name_' . $actual_template_key;
		$setting_id = 'filo_doc_act_opt_name';
		wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $setting_id 2: ' .  wsl_vartotext( $setting_id ));

		$priority ++;
			
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		$wp_customize->add_control( $setting_id, array(
		//$wp_customize->add_control( new FILO_Customize_Change_Act_Opt_Name( $wp_customize, $setting_id, array(
			'label' => __('Actual Skin Name' , 'filo_text'),
			//'description' => '',
			'section'  => $section_key,
			'type' => 'select',
			'choices' => $saved_option_list,
			'priority' => $priority,
		//) ) );
		) );


		// 3. Delete Skin
			
		//$setting_id = 'filo_doc_act_opt_name_' . $actual_template_key;
		
		$setting_id = 'filo_doc[fd_delete_link]';

		$priority ++;
			
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
		) );
	
		//type: text
		//$wp_customize->add_control( $setting_id, array(
		$wp_customize->add_control( new FILO_Customize_Delete_Opt_Control( $wp_customize, $setting_id, array(
			'label' => __('Delete actual Skin' , 'filo_text'),
			//'description' => '',
			'section'  => $section_key,
			'type' => 'text',
			'priority' => $priority,
		) ) );
		//) );




		
		
		/*
		// PROTECTED SKIN
		 
		$setting_id_prefix = 'filo_doc[fd_saving_options]'; 
		
		$priority ++;
		
		$code = 'protected_skin';
		$setting_id = $setting_id_prefix . '[' . $code . ']';
	
		$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) ) );
	
		//type: checkbox

		$wp_customize->add_control( $setting_id, array(
			'label' => __( 'Protected Skin', 'filo_text' ),
			'description' => __( 'Protected skin cannot be overwritten, but cannot be saved as another name.', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'checkbox',
			'priority' => $priority,
		) );
		*/		

		//---------------
		
		$setting_id = 'filo_change_act_opt_name';

		$priority ++;
			
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		$wp_customize->add_control( new FILO_Customize_Change_Act_Opt_Name( $wp_customize, $setting_id, array(
			'label_templ' => __('Change Template' , 'filo_text'),
			'label' => __('Change Skin' , 'filo_text'),
			//'description' => '',
			'section'  => $section_key,
			'type' => 'select',
			'choices_templ' => $doc_template_list,			
			'choices' => $saved_option_list,
			'priority' => $priority,
		) ) );

		//---------------

		// SAVE AS
		
		$setting_id_prefix = 'filo_doc[fd_saving_options]';
		
		$priority ++;
		
		$code = 'save_as_opt_name';
		$setting_id = $setting_id_prefix . '[' . $code . ']';
		//$setting_id = 'filo_save_as_opt_name';
		//wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $setting_id 2: ' .  wsl_vartotext( $setting_id ));
	
		$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) ) );
	
		//type: text
		
		$wp_customize->add_control( new FILO_Customize_Save_As_Opt_Control( $wp_customize, $setting_id, array(
			'label' => __('Save Skin as ' , 'filo_text'),
			//'description' => '',
			'section'  => $section_key,
			'type' => 'text',
			'priority' => $priority,
		) ) );

	}


	/**
	 * add_global_option_fields
	 * 
	 * Add global customizer options that independent of the used theme.
	 * - theme dequeue styles
	 */
	static function add_global_option_fields() {
		global $wp_customize;
	
	
		$section_key = 'filo_doc_global_options';
	
		$wp_customize->add_section(
	        $section_key,
	        array(
	            'title' => __('Global Options' , 'filo_text'),
	            //'description' => __('' , 'filo_text'),
	            'priority' => 310,
	        )
	    );	


		$priority = 0;
		
		//$setting_id = 'filo_doc_global[sample_order_id]';
		$setting_id = 'filo_sample_order_id';

		$wp_customize->add_setting( $setting_id, array(  //we do npt use FILO_Customize_Setting here, because it is not template and skin dependent, and the specialized get_root_value for filo_doc[....] settings cannot be used.
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
		) );
	
		//type: finadoc_select
		$wp_customize->add_control( new FILO_Customize_Finadoc_Select_Control( $wp_customize, $setting_id, array(
			'label' => __('Sample Order or Invoice ID' , 'filo_text'),
			'description' => __( 'You can choose which order or invoice or other document would you like to use as a sample for customizing the document style.', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'finadoc_select',
			'doc_types' => null,
			'orderby' => 'desc',
			'item_limit' => apply_filters('filo_customize_manager_sample_doc_select_item_limit', 2000),
			'show_option_none' => __( '— Select —' , 'filo_text' ),
			'option_none_value' => '',
			//'show_option_no_change' => ,
			'priority' => $priority,
		) ) );
		
		
		$priority ++;		
		
		
		$setting_id = 'filo_show_created_by_text';
		
		// we are using the normal settings class, and do not apply: new FILO_Customize_Setting, because we do not need to change it to the "by template" method. 
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'filo_doc_sanitize_style_names_setting',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		
		$wp_customize->add_control( $setting_id, array(
			'label' => __( 'Show "created by" text in document footer', 'filo_text' ),
			'description' => __( 'Set this checkbox to display "created by" text in document footer.', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'checkbox',
			'priority' => $priority,
		) );		
		


		$priority ++;
		
		$setting_id = 'filo_hide_error_messages_on_filodocs';

		// we are using the normal settings class, and do not apply: new FILO_Customize_Setting, because we do not need to change it to the "by template" method. 
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'filo_doc_sanitize_style_names_setting',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		
		$wp_customize->add_control( $setting_id, array(
			'label' => __( 'Hide error messages on financial documents', 'filo_text' ),
			'description' => __( 'Clear this checkbox if you create a document template, and want to see if the applied shortcodes are appropriate, or there is an error.', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'checkbox',
			'priority' => $priority,
		) );		

		$priority ++;
		
		$setting_id = 'filo_doc_global[dequeue_header]';

		$wp_customize->add_setting( $setting_id, array() ); 

		$wp_customize->add_control( new FILO_Customize_Header_Control( $wp_customize, $setting_id, array(
				'label'	=> __('Dequeue Styles' , 'filo_text'),
				'description'	=> __('Your theme and plugins may disturb the generated document style, if you use Filogy Invoice Builder SiteOrigine template. You can dequeue the unnecessary styles.' , 'filo_text'),
				'section' => $section_key,
				'priority' => $priority,
			) 
		));

		$priority ++;
		
		
		$setting_id = 'filo_doc_global[dequeue_theme_styles]';
		//$setting_id = 'filo_global_doc111';
		
		// we are using the normal settings class, and do not apply: new FILO_Customize_Setting, because we do not need to change it to the "by template" method. 
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'filo_doc_sanitize_style_names_setting',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		
		$wp_customize->add_control( $setting_id, array(
			'label' => __('Dequeue Theme Styles' , 'filo_text'),
			'description' => __('Check if you need to remove all styles of your theme when create documents.' , 'filo_text'),
			'section'  => $section_key,
			'type' => 'checkbox',
			'priority' => $priority,
		) );
		

		$priority ++;
		
		
		$setting_id = 'filo_doc_global[dequeue_plugins_styles]';
		//$setting_id = 'filo_global_doc111';
		
		// we are using the normal settings class, and do not apply: new FILO_Customize_Setting, because we do not need to change it to the "by template" method. 
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			//'sanitize_callback' => 'filo_doc_sanitize_style_names_setting',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		
		$wp_customize->add_control( $setting_id, array(
			'label' => __('Dequeue Plugins Styles' , 'filo_text'),
			'description' => __('Check if you need to remove styles of all plugins activated on your WP site when create documents.' , 'filo_text'),
			'section'  => $section_key,
			'type' => 'checkbox',
			'priority' => $priority,
		) );
				
		$priority ++;

		$setting_id = 'filo_doc_global[dequeue_entered_styles]';
		//$setting_id = 'filo_global_doc111';
			
		// we are using the normal settings class, and do not apply: new FILO_Customize_Setting, because we do not need to change it to the "by template" method. 
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', //RaPe ToDo: $color_scheme[2]
			'sanitize_callback' => 'filo_doc_sanitize_style_names_setting',
			'transport'         => 'postMessage', //'refresh',
			'type' 				=> 'option',
			//'filo_css_property' => $setting_attributes['css_property'],
			//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
		) );
	
		//type: text
		
		$wp_customize->add_control( $setting_id, array(
			'label' => __('Theme Dequeue Style Names' , 'filo_text'),
			'description' => __('List of CSS stylesheets, that you need to remove when create documents. You can dequeue the unnecessary styles, by entering the name of these. You can enter more stylesheet name in new lines.' , 'filo_text'),
			'section'  => $section_key,
			'type' => 'textarea',
			'priority' => $priority,
		) );




		//$priority ++;

		/*
		$setting_id = 'filo_document_logo';
		

		$wp_customize->add_setting( $setting_id, array(
			'type'       => 'option',
			'transport'  => 'postMessage', 
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $setting_id, array(
			'label'       => __('Logo' , 'filo_text'),
			'description' => __('Logo displayed on invoices or other financial documents. Do not use transparent images! Images with transparent background make document rendering extremely slow!', 'filo_text'),
			'section'     => 'Document-General', 
			'priority'    => 1000,
		) ) );
		*/
	}


	/**
	 * get_saved_option_names
	 * 
	 * Select option names from the database
	 */
	static function get_saved_option_names( $option_to_find ) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s",
			$option_to_find . '%'
		);
		
		wsl_log(null, 'class-filo-customize-manager.php get_saved_option_names $sql: ' .  wsl_vartotext( $sql ));
		
		$saved_option_names = $wpdb->get_col( $sql );
		
		wsl_log(null, 'class-filo-customize-manager.php get_saved_option_names $saved_option_names: ' .  wsl_vartotext( $saved_option_names ));
		
		return $saved_option_names;
		
	}

	/**
	 * get_saved_option_names_and_values
	 * 
	 * Select option names and values from the database
	 */
	static function get_saved_option_names_and_values( $option_to_find ) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "select option_name, option_value from {$wpdb->prefix}options where option_name like %s",
			$option_to_find . '%'
		);
		
		//wsl_log(null, 'class-filo-customize-manager.php get_saved_option_names_and_values $sql: ' .  wsl_vartotext( $sql ));
		
		$result = $wpdb->get_results( $sql );
		
		//wsl_log(null, 'class-filo-customize-manager.php get_saved_option_names_and_values $result: ' .  wsl_vartotext( $result ));
		
		// e.g. $result: Array
		//(
		//    [0] => stdClass Object
		//        (
		//            [option_name] => filo_doc_opt_filo_standard_template--Bravo%204
		//            [option_value] => ....
		//        )
		//
		//    [1] => stdClass Object
		//        (
		//            [option_name] => filo_doc_opt_filo_standard_template--Bravo%206
		//            [option_value] => ....
		//        )
		//)
		
		$saved_option_names_and_values = array();
		if ( isset($result) and is_array($result) )
		foreach ($result as $result_item_class) {
			$saved_option_names_and_values[$result_item_class->option_name] = $result_item_class->option_value;
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php get_saved_option_names_and_values $saved_option_names_and_values: ' .  wsl_vartotext( $saved_option_names_and_values ));
		
		return $saved_option_names_and_values;
		
	}

	static function add_template_custom_settings() {
		global $wp_customize;
	
		$opt_fix_prefix = 'filo_doc_templ_custset_';
		$panel_id = '';
		$section_key = 'filo_doc_template_custom_settings';

		$wp_customize->add_section(
	        $section_key,
	        array(
	            'title' => __('Template Specific Settings' , 'filo_text'),
	            'description' => __('Manual page refresh is necessary in your browser after Save.' , 'filo_text'),
	            'priority' => 300,
	        )
	    );	

		$setting_id_prefix = 'filo_doc[' . $panel_id . '][' . $section_key . ']';
		$start_priority = 0; 

		do_action( 'filo_doc_customizer_add_template_custom_settings', $wp_customize, $setting_id_prefix, $section_key, $start_priority ); 
	}		

	static function get_act_option_stored_name() {
		
		//get actual template key
		$actual_template_key = wc_clean (get_option( 'filo_document_template' ));
		//$actual_template_key = self::get_actual_template_key();
		
		wsl_log(null, 'class-filo-customize-manager.php get_act_option_stored_name $actual_template_key: ' . wsl_vartotext( $actual_template_key ));
		
		//$used_opt_name = get_option( 'filo_doc_act_opt_name_' . $actual_template_key );
		$used_opt_name = get_option( 'filo_doc_act_opt_name' );
		//$used_opt_name_encoded = urlencode( $used_opt_name ); //rawurlencode( $used_opt_name );
		
		$act_option_stored_name = 'filo_doc_opt_' . $actual_template_key . '--' . $used_opt_name;
		
		return $act_option_stored_name;
		
	}

	/**
	 * save_after
	 * 
	 * Customized values are saved into a filo_doc option in the database.
	 * This is the default functionality of the customizer.
	 * 
	 * This function copy the saved option values into the final database option
	 * where the optiona name contains the actual template name and option name belonging to the active skin.   
	 * 
	 * So customizer save settings into a fix temporary place (filo_doc option), and after it this function 
	 * copies this into another option with the final name.
	 * 
	 * get_root_value function will get this final option.
	 */
	static function save_after( $wp_customize ) {
		
		wsl_log(null, 'class-filo-customize-manager.php save_after 0: ' . wsl_vartotext( '' ));
		//wsl_log(null, 'class-filo-customize-manager.php save_after $wp_customize: ' . wsl_vartotext( $wp_customize )); //VERY BIG
		
		$wp_option_name_to_store = self::get_act_option_stored_name();
		
		//wsl_log(null, 'class-filo-customize-manager.php save_after $wp_option_name_to_store: ' . wsl_vartotext( $wp_option_name_to_store ));
		//wsl_log(null, 'class-filo-customize-manager.php save_after get_option(filo_doc): ' . wsl_vartotext( get_option( 'filo_doc' ) ));
		
		$options = get_option( 'filo_doc' );
		
		//wsl_log(null, 'class-filo-customize-manager.php save_after $options x: ' . wsl_vartotext( $options ));
		
		//Cleaning option
		//delete empty properties, and the containing upper level elements if became empty
		self::clean_filo_doc_options( $options );
		
		//wsl_log(null, 'class-filo-customize-manager.php save_after $options: ' . wsl_vartotext( $options )); //big
		
		//The normal save function saves the settings into filo_doc WP option. This is considered as a temporary option, and we copy it to the final option name.
		update_option( $wp_option_name_to_store, $options );
		
	}
	
	/**
	 * change_filename_to_media_lib_url
	 * 
	 * Change filo_logo background_image values during import
	 * 
	 */
	public static function change_filename_to_media_lib_url_callback( $key, $value ) {
		
		wsl_log(null, 'class-filo-customize-manager.php change_filename_to_media_lib_url_callback $key: ' .  wsl_vartotext( $key ));
		wsl_log(null, 'class-filo-customize-manager.php change_filename_to_media_lib_url_callback $value: ' .  wsl_vartotext( $value ));

		//in case of filo_logo and background_image, change the filename to media lib url
		if ( in_array( $key, array('filo_logo', 'background_image') ) ) {
			
			$media_lib_url = wsl_get_media_lib_url_by_filename( $filename = $value );
			
			wsl_log(null, 'class-filo-customize-manager.php change_filename_to_media_lib_url_callback $media_lib_url: ' .  wsl_vartotext( $media_lib_url ));
			
			if ( is_object ($media_lib_url) ) {
				$value = $media_lib_url->guid;
			}
			
		}
		
		$return['result_value'] = $value;
		
		wsl_log(null, 'class-filo-customize-manager.php change_filename_to_media_lib_url_callback $return[result_value]: ' .  wsl_vartotext( $return['result_value'] ));
		
		return $return;
		
	}

	
	/**
	 * Add My Color section and it's settings and controls to customizer
	 */		
	static function add_color_palette_items() {
		global $wp_customize;
				
		wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items0: ' . wsl_vartotext( '' )); 

		$section_key = 'filo_color_palette';
		
		$description = __('You can set pre-defined colors as a dinamic color palette, that colors can be linked to any color of your cutom design. If the palette colors is changed, then the referring colors will be changed automatically.' , 'filo_text');
		$description = apply_filters('filo_customize_section_description', $description, $section_key, $section = null, $sections = null, $panel_id = null);

		$wp_customize->add_section(
	        $section_key,
	        array(
	            'title' => __('My Colors' , 'filo_text'),
	            'description' => $description,
	            'priority' => 1,
	        )
	    );	

		
		$color_palette_elements = self::get_color_palette_elements();
		
		$setting_id_prefix = 'filo_doc[fd_color_palette]';
		
		$priority = 0;
		
		foreach ($color_palette_elements as $color_item_key => $color_item_label) {

			$priority++;
			
			// display the appropriate color field


			$setting_id = $setting_id_prefix . '[' . $color_item_key . ']';
			wsl_log(null, 'class-filo-customize-manager.php add_color_palette_items $setting_id: ' .  wsl_vartotext( $setting_id ));
				
	
			$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
				'default'           => '', //RaPe ToDo: $color_scheme[2]
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
				'type' 				=> 'option',
				//'filo_css_property' => $setting_attributes['css_property'],
				//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
			) ) );
		
			//type: color
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(								
				'label' => $color_item_label,
				//'description' => '',
				'section'  => $section_key,
				'priority' => $priority,
			) ) );
			
			//after filo_color_primary_color, we print the button ( button_position )
			if ( $color_item_key == 'filo_color_primary_color' ) { 
					
				// place Generate_Color_Palette_Button, after the first two fields
				
				$setting_id = $setting_id_prefix . '[Generate_Color_Palette_Button]';
				
				$wp_customize->add_setting( new FILO_Customize_Setting( $wp_customize, $setting_id, array(
					'default'           => '', //RaPe ToDo: $color_scheme[2]
					'transport'         => 'postMessage', //'refresh',
					'type' 				=> 'option',
					//'filo_css_property' => $setting_attributes['css_property'],
					//'filo_css_selector' => $section['selectors'][$setting_attributes['css_selector_key']],
				) ) );
			
				$wp_customize->add_control( new FILO_Customize_Generate_Color_Palette_Control( $wp_customize, $setting_id, array(
					'label' => '', //__('' , 'filo_text'),
					//'description' => '',
					'section'  => $section_key,
					'priority' => $priority,
				) ) );
										
			}

		}

	}	
	
	//deleted:
	//function filo_doc_sanitize_color_scheme( $value )
	//function filo_doc_get_color_scheme_choices()
	//function filo_doc_get_color_scheme() {
	
	/**
	 * filo_doc_customize_preview_js
	 * 
	 * Add Actions of live preview scripts
	 */
	static function filo_doc_customize_preview_js( $wp_customize ) {
		
		wsl_log(null, 'class-filo-customize-manager.php filo_doc_customize_preview_js 0: ' .  wsl_vartotext(''));
			
		global $filo_customize;
		
		$filo_customize = $wp_customize;

		//filo_document_footer action would be great to use instead of wp_footer, but it does not work, because this error is raised: Uncaught TypeError: wp.customize is not a function
		//thus the template has to contain the standard WP footer //QQQ21
		//It is good for templates using original wp_footer and also for alternative wp_footer_filo
		//add_action( 'wp_footer', 'FILO_Customize_Manager::print_preview_script', 9999 ); //it is must //QQQ21 
		//add_action( 'wp_footer', 'FILO_Customize_Manager::render_initial_jquery_inline_styles', 9999 ); //it 
		add_action( 'wp_footer_filo', 'FILO_Customize_Manager::print_preview_script', 9999 ); //it is must //QQQ21 
		add_action( 'wp_footer_filo', 'FILO_Customize_Manager::render_initial_jquery_inline_styles', 9999 ); //it is must //QQQ21 
		
	}

	/**
	 * print_preview_script
	 */		
	static function print_preview_script() {
		
		wsl_log(null, 'class-filo-customize-manager.php print_preview_script 0: ' .  wsl_vartotext(''));
		
		global $filo_customize;
		
		$wp_customize = $filo_customize;
			 
		?>
			<script type="text/javascript" id="filo-customize-manager-print-preview-script">
			/**
			 * This file adds some LIVE to the Theme Customizer live preview. To leverage
			 * this, set your custom settings to 'postMessage' and then add your handling
			 * here. Your javascript should grab settings from customizer controls, and 
			 * then make any necessary changes to the page using jQuery.
			 */
			( function( $ ) {
			
				<?php			
					
				$settings = $wp_customize->settings();
				
				////wsl_log(null, 'class-filo-customize-manager.php print_preview_script $settings: ' .  wsl_vartotext( $settings ));
				//var_dump($settings);
				
				if ( isset($settings) and is_array($settings) ) 
				foreach ($settings as $key => $value) {
					//$wp_customize->remove_setting( $key );
						
					//wsl_log(null, 'class-filo-customize-manager.php print_preview_script $key: ' .  wsl_vartotext( $key ));
					//wsl_log(null, 'class-filo-customize-manager.php print_preview_script $value: ' .  wsl_vartotext( $value ));
							
					$setting = $wp_customize->get_setting( $key );
					$filo_css_selector = property_exists($setting, 'filo_css_selector') ? $setting->filo_css_selector : null;
					$filo_css_property = property_exists($setting, 'filo_css_property') ? $setting->filo_css_property : null;
					$filo_css_measurement_unit = property_exists($setting, 'filo_css_measurement_unit') ? $setting->filo_css_measurement_unit : null;

					//wsl_log(null, 'class-filo-customize-manager.php print_preview_script $key 2: ' .  wsl_vartotext( $key ));
					//wsl_log(null, 'class-filo-customize-manager.php print_preview_script $filo_css_selector: ' .  wsl_vartotext( $filo_css_selector ));
					//wsl_log(null, 'class-filo-customize-manager.php print_preview_script $filo_css_property: ' .  wsl_vartotext( $filo_css_property ));
					
					if ( $filo_css_selector != '' and $filo_css_property != '' ) {
						
						if ( $filo_css_property == 'custom-css' ) { // Special: branch for live preview of custom css 
							?>
			
								// Update the site in real time...
								wp.customize( '<?php echo $key ?>', function( value ) {
									value.bind( function( newval ) {
										var filo_css_selector_id = '<?php echo self::convert_selector2id($filo_css_selector) ?>';
										
										$('#filo-customize-manager-custom-css-container style#filo-customize-manager-custom-css-style_' + filo_css_selector_id ).remove();

										var newval_2 = merge_css_selector_prefix_and_custom_css(newval, '<?php echo $filo_css_selector ?>')
										$('#filo-customize-manager-custom-css-container').append('<style id="filo-customize-manager-custom-css-style_' + filo_css_selector_id + '">'	+ newval_2 + '</style>');
										
										console.log('filo_css_selector: #filo-customize-manager-custom-css-container');
										console.log('append: <style id="filo-customize-manager-custom-css-style_' + filo_css_selector_id + '">'	+ newval_2 + '</style>');
										console.log('');
										
									} );
								} );
								
							<?php
							
						} elseif ( $filo_css_property == 'filo-logo' ) { // Special: branch for live preview of images css
							?>
								
								 
								// Update the site in real time...
								wp.customize( '<?php echo $key ?>', function( value ) {
									value.bind( function( newval ) {

										//$( '<?php echo $filo_css_selector ?>' + ' img' ).css('<?php echo $filo_css_property ?>', newval );
										$( '<?php echo $filo_css_selector ?>' + ' #filo_logo img' ).attr( 'src', newval );
										
										console.log('filo_css_selector: <?php echo $filo_css_selector ?>' + ' #filo_logo img');
										console.log('filo_crc_attr: ' + newval);
										console.log('');
										
									} );
								} );
								
							<?php
														
						} else { // STANDARD branch for live preview of custom css
							?>
			
								// Update the site in real time...
								wp.customize( '<?php echo $key ?>', function( value ) {
									value.bind( function( newval ) {

										var filo_css_selector = '<?php echo $filo_css_selector ?>';
										var filo_css_property = '<?php echo $filo_css_property ?>';

										var filo_css_measurement_unit = '<?php echo $filo_css_measurement_unit; ?>';										
										var add_measurement_unit = '';
										
										// in case of given measurement unit (e.g px) if newval is a valid number (NaN means not a number), then set the default measuremebt unit
										// we do not add measurement unit if the newval is not a number, because it can be a "small" font-size or a "10px 5px 10px 12px" padding (thus different values for the 4 side)
										if ( filo_css_measurement_unit != '' && filo_css_measurement_unit != 'NA' && ! isNaN(newval) && newval != '' ) {
											add_measurement_unit = filo_css_measurement_unit;
										}

										var filo_setting_value = newval;
																		
										//if we are in panel-grid-cell of a panel-table (thus an item table cell), then padding has to be applied in a lower level, because the cell background color has not as wild as the table cell (the padding causes a "frame" in table cell bacground)
										if ( filo_css_property.indexOf('padding') === 0 && filo_css_selector.indexOf(' .panel-table ') !== -1 && filo_css_selector.indexOf(' .panel-grid-cell') !== -1 ) {  
											filo_css_selector += ' .filo_table_cell'; //this is a lower level (child) css element
										}

										if ( filo_css_property == 'background-image' ) { //in case of background image, this form has to be applied: background-image: url('my_url'); e.g. https://gist.github.com/srikat/95d118a4caa1a071dc1c  
											filo_setting_value = 'url(' + filo_setting_value + ')';
										} else if ( filo_css_property == 'background-color-odd' ) { // .myselector(odd) { background: white; }; add odd to selector, and remove it from css property
											filo_css_selector = filo_css_selector.replace('.panel-grid ', '.panel-grid' + ':odd '); //add .panel-grid -> .panel-grid:odd
											filo_css_property = 'background-color';
										} else if ( filo_css_property == 'background-color-even' ) { // .myselector(even) { background: black; }; add even to selector, and remove it from css property
											filo_css_selector = filo_css_selector.replace('.panel-grid ', '.panel-grid' + ':even '); //add .panel-grid -> .panel-grid:even
											filo_css_property = 'background-color';
										} else { // normal way
											filo_setting_value += add_measurement_unit;
										}
										
										$( filo_css_selector ).css(filo_css_property, filo_setting_value );
										
										console.log('filo_css_selector: ' + filo_css_selector);
										console.log('filo_css_property: ' + filo_css_property);
										console.log('filo_setting_value: ' + filo_setting_value);
										console.log('');
										
										
									} );
								} );
								
							<?php
						}
							
					}
		
				}

				?>
				// Special: document general font size 
				// Set document general font size. It is necessary to be handled special way, because of the 0 font size is applied and has to be reseted
				// This is the on-line version of it
				wp.customize( 'filo_doc[][Document-General][css_document_general_selector][font_size]', function( value ) {
					value.bind( function( newval ) {

						var filo_css_measurement_unit = 'px';										
						var add_measurement_unit = '';
						
						// in case of given measurement unit (e.g px) if newval is a valid number (NaN means not a number), then set the default measuremebt unit
						// we do not add measurement unit if the newval is not a number, because it can be a "small" font-size or a "10px 5px 10px 12px" padding (thus different values for the 4 side)
						if ( filo_css_measurement_unit != '' && filo_css_measurement_unit != 'NA' && ! isNaN(newval) && newval != '' ) {
							add_measurement_unit = filo_css_measurement_unit;
						}

						$( '.panel-grid-cell' ).css('font-size', newval + add_measurement_unit);
						
						console.log('filo_css_selector (spec docu font size): ' + '.panel-grid-cell');
						console.log('filo_css_property (spec docu font size): ' + 'font-size');
						console.log('filo_setting_value (spec docu font size): ' + newval + add_measurement_unit);
						
					} );
				} );


				// User enterded a css style into a custom css field
				// We merge the appropriate selector prefix for every element.
				function merge_css_selector_prefix_and_custom_css(css, prefix) {
					
					//ALSO IMPLEMENTED IN PHP: render_custom_css
					
					//remove php or other tags - it is not necessary in this js funcrion, because if php tags would be entered, these did not be executed (we are after page load here) 
					//P $css = strip_tags($selector_and_css_content);
		
					//replace all { character to ¤{. If there is not any character befor {, then this extra caracter ensures that it can be found as a string of which the prefix should be added. (At the end, the extra character will be removed, but the prefix will remain). Without this the prefix is not applied for a { character, if there is not any string before it.
					css = css.replace("{", "¤{");
				
					//remove comments
					//$css = preg_replace($pattern = '/(\/\*.*?\*\/|^\/\/.*?$)/m', $replacement = '', $subject = $css); //We remove it later
					
					var repl = css.replace(
				        /(\/\*.*?\*\/|^\/\/.*?$)|([^\r\n,{}]+)(,(?=[^}]*{)|\s*{)/m, 
				        	// the first capture block is the comments, this should be in the first place, because the other roules must not be applied on comments:  (\/\*.*?\*\/|^\/\/.*?$)
				        	// the following part is the roule for css selector
				        	// /m option is needed for handling the start and end of rows ^...$ (multiple rows) 
				        function ( match, match_1, match_2, match_3 ) {

				        	var ret = '';
							
				        	if ( match_1 ) {
				        		ret += match_1;
							}
							
							if ( match_2 ) {
								match_2 = prefix + ' ' + match_2; //Add prefix
								ret += match_2;
							}
							
							if ( match_3 ) {
								ret += match_3;
							}
							
				            return ret; //return from this callbeck function

						}
					);

					//remove the unnecessary ¤ characters that were inserted at the begnning of this function						
					repl = repl.replace('¤{', '{');
				    
					return repl;
					
				}		
				
			} )( jQuery );		
			</script>
			
		<?php
				
	}

	/** print script of sections: (NOT USED, BUT TESTED)
	 * - if a section is opend, the custom css codemirror fields have to be refreshed 
	 */
	static function print_scripts_of_sectios() {
		/*
		//It would be used for generate onExpand bindings for sections, but it was moved to the controls part (because it has to be in the same plase as cm_editor field)
		//However it is tested and can be used later for any binding or other settings for section level
		//It should not be deleted!
	
		global $wp_customize;

		$panels_sections_keys = array();

		//standalone section keys (that has not inside a panel)
		$sections = $wp_customize->sections();
		$section_keys = array_keys($sections);
		
		$panels_sections_keys[''] = $section_keys;
		
		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_sectios $section_keys: ' .  wsl_vartotext( $section_keys ));
		
		
		//section keys of panels
		
		$panels = $wp_customize->panels();
		$panel_keys = array_keys($panels);

		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_sectios $panel_keys: ' .  wsl_vartotext( $panel_keys ));
		
		foreach ($panels as $panel_key => $panel) {
			
			$sections_of_panel = $panel->sections;
			$sections_of_panel_keys = array_keys($sections_of_panel);
			wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_sectios $sections_of_panel_keys: ' .  wsl_vartotext( $sections_of_panel_keys ));
			
			$panels_sections_keys[$panel_key] = $sections_of_panel_keys;
			
		}
		
		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_sectios $panels_sections_keys: ' .  wsl_vartotext( $panels_sections_keys ));
		
		?>
		<script type="text/javascript" id="filo_doc_customize_sections_js">
		
			( function( $ ) {
				
				$(document).ready(function(){
					
					<?php
					
					if ( isset($panels_sections_keys) and is_array($panels_sections_keys) )
					foreach ( $panels_sections_keys as $panel_key => $panel_sections ) {
						
						if ( isset($panel_sections) and is_array($panel_sections) )
						foreach ( $panel_sections as $section_key ) {
							?>
						
							//....													
							
						<?php
						}	
					}
					?>								
					
					
				});			
			
			})( jQuery );
		
		</script>
		
		<?php
		*/
	}

	/**
	 * print_footer_scripts
	 * 
	 * In normal usage of customizer class-wp-customize-widgets.php core file is used and in print_footer_scripts function
	 * do_action( 'admin_print_footer_scripts' ) is done. 
	 * It would load some scripts that is eliminated in Filogy customizer mode, without this javascript some errors occure (e.g. Uncaught ReferenceError: wp is not defined    in filo_mycolor_palette_change()) 
	 * We do it again 
	 */
	static function print_footer_scripts() {
		
		//if we are in filogy customizer, we load the scripts
		if ( (isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc') ) {
			do_action( 'admin_print_footer_scripts' );
		}
	}
	
	/** 
	 * print_scripts_of_controls_color_palette
	 * 
	 * My Colors
	 */
	static function print_scripts_of_controls_color_palette() {
		global $wp_customize;
		//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $wp_customize: ' .  wsl_vartotext( $wp_customize ));

		$controls = $wp_customize->controls();
						
		// FIND all color filogy fields (except palette colors) and generate a list of them
		
		$filo_color_field_control_and_setting_keys = array();
		
		if ( isset($controls) and is_array($controls) ) 
		foreach ($controls as $control_and_settings_key => $control) {

			wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $control_and_settings_key 0: ' .  wsl_vartotext( $control_and_settings_key ));
				
			// We need only those colors, that is used by filo (so begins with filo_doc), and color palette field have to be excluded (that begins with filo_doc[fd_color_palette])			
			if ($control->type == 'color' and strpos($control_and_settings_key, 'filo_doc') === 0 and strpos($control_and_settings_key, 'filo_doc[fd_color_palette]') !== 0) {
				
				$filo_color_field_control_and_setting_keys[] = $control_and_settings_key;
				
			}
			
		}

		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $filo_color_field_control_and_setting_keys: ' .  wsl_vartotext( $filo_color_field_control_and_setting_keys ));

		//get color panel element array
		$color_palette_elements = self::get_color_palette_elements();
		$color_palette_element_keys = array();
		if ( is_array($color_palette_elements) ) {
			$color_palette_element_keys = array_keys($color_palette_elements);
		}
		
		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $color_palette_element_keys: ' .  wsl_vartotext( $color_palette_element_keys ));
		

		$setting_id_prefix = 'filo_doc[fd_color_palette]';

		?>
		<script id="filogy_scripts_of_controls_color_palette">
			( function( $ ) {
			
				/**
				 * MY COLORS
				 */			
			
				//convert php array to javascript
				var filo_color_field_control_and_setting_keys = <?php echo json_encode( $filo_color_field_control_and_setting_keys ); ?>;
				var setting_id_prefix = <?php echo json_encode( $setting_id_prefix ); ?>;
				var color_palette_element_keys = <?php echo json_encode( $color_palette_element_keys ); ?>;
				
				
				filo_mycolor_palette_change();
				filo_normal_color_field_or_palette_item_selector_changed();
				
				$(document).ready(function(){

					<?php
					
					// Triggering that color reference select field options has background color according to the matched palette filed color value (filo_color_1, filo_color_2, ..)
					// change the value of the palette template color fields, than change back, for triggering the value.binds method of them
					// the reason of this, that we need to set the background and border color design of color palette reference select fields during initialisation (to show colors of the choosable options)
					foreach ($color_palette_elements as $color_item_key => $color_item_label) {
						?>
						/*
						// we changes the field value twice, then back to the original. This ensures that there will be change for every possible value.						
						var color_item_key = '<?php echo $color_item_key; ?>';
						var original_value = $( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').val();
						$( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').val('#000000'); $( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').trigger("change");
						$( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').val('#FFFFFF'); $( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').trigger("change");
						$( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').val(original_value); $( '#customize-control-filo_doc-fd_color_palette-' + color_item_key + ' .color-picker-hex').trigger("change");
						*/
						<?php
					}

					?>
					
					filo_set_color_picker_palette_colors();
					filo_set_initial_value_of_referef_color_fields();
					

				});				
				
				function filo_mycolor_palette_change() {
					
					<?php
					
					// 1, 2, 3: if a color palett color is changed, then change the selector field opitions, the referer field color, and the normal field color belonging to the referef field
					// If one color of the color palett is changed (e.g. filo_color_3 is changed to red),
					// then values of those color pickers for which the belonging color palette item selector value equal should be changed
					// the belonging mycolor palette reference field border and option background also should be changed 
	
	 				// Go trought on every color palette element (filo_color_1, filo_color_2, ...)
					foreach ($color_palette_elements as $color_item_key => $color_item_label) {
				
						// calculate the color palette item settings_id
						$color_palette_item_setting_id = $setting_id_prefix . '[' . $color_item_key . ']'; //e.g. filo_doc[fd_color_palette][filo_color_1]
		
						?>
	
						wp.customize( '<?php echo $color_palette_item_setting_id ?>', function( value ) {
							value.bind( function( newval ) {
	
								filo_change_reference_color_fields('<?php echo $color_item_key; ?>', newval);
	
								/*
								// foreach
								// loop through on each color type fields (normal color fields)
								filo_color_field_control_and_setting_keys.forEach(function( normal_color_field_control_and_setting_key ) {
	
	
									// 1. Change normal color fields value
									// If a mycolor palette item is changed, modify the normal color fields that is referenced that
	
									// calculate the mycolor palette reference field setting id that belongs to the normal color field								
									mycolor_reference_setting_key = normal_color_field_control_and_setting_key.slice(0, -1) + '_mycolor_ref]'; //remove the original last character ] from the end and add _mycolor_ref], e.g. filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color] to filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color_mycolor_ref] php: rtrim( $setting_id, "]" ) . '_mycolor_ref]';  
	
									// get the value of the mycolor reference key 								 
									mycolor_reference_value = wp.customize( mycolor_reference_setting_key ).get();
	
									// Examine whether the changed mycolor palette item field name is contained by the viewd mycolor reference key (e.g. filo_color_3)
									if ( mycolor_reference_value == '<?php echo $color_item_key; ?>' ) {
										
										// the mycolor reference field value of this normal color field is the same that palette color is changed (e.g. filo_color_2 - filo_color_2), 
										// so the belonging normal color field value should be changed to the changed palette color field value
										wp.customize( normal_color_field_control_and_setting_key ).set( newval );
									
									}
									
									// 2. COLORIZED opitions: modify selector OPTIONS background color
									// If a color palette item is changed, modify the color of the mycolor reference options for that palette item
									
									// generate css #id from settings id by replacing [] to - and using customize-control- prefix
									//var mycolor_reference_field_css_id = mycolor_reference_setting_key
									//mycolor_reference_field_css_id = mycolor_reference_field_css_id.replace(/\[/gi, '-'); //replace('[', '-') more times
									//mycolor_reference_field_css_id = mycolor_reference_field_css_id.replace(/\]/gi, ''); //replace(']', '') more times
									//mycolor_reference_field_css_id = 'customize-control-' + mycolor_reference_field_css_id;
									
									var mycolor_reference_field_css_id = filogy_convert_setting_key2css_id( mycolor_reference_setting_key );
	
									// calculate the contrast color: http://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color
									contrast_color = get_contrast_color( newval );
									
									// set background color of the given option item
									$( '#' + mycolor_reference_field_css_id + ' option[value="<?php echo $color_item_key; ?>"]').css('background-color', newval );  //option[value="filo_color_3"]
	
									// set font color to be in contrast of the background of the given option item
									$( '#' + mycolor_reference_field_css_id + ' option[value="<?php echo $color_item_key; ?>"]').css('color', contrast_color );  //option[value="filo_color_3"]
									
									
									// 3. ACTUAL SELECTOR color:
									// if the selected value in the mycolor reference field is the same as our actual color palette item (e.g. filo_color_3), then set a colored left border
									if ( mycolor_reference_value == '<?php echo $color_item_key; ?>') {
										$( '#' + mycolor_reference_field_css_id + ' .customize-control-color-palette-item_select_frame.frame1').css('background-color', newval );
									}
	
								});	*/				
							});
						});
						
						<?php
					}
					?>
				}
				
				function filo_set_initial_value_of_referef_color_fields() {
					
					var setting_id_prefix = 'filo_doc[fd_color_palette]';
					
					color_palette_element_keys.forEach(function( color_palette_item_key ) {
						
						var setting_id = setting_id_prefix + '[' + color_palette_item_key + ']';
						
						//var color_palette_item_css_id = filogy_convert_setting_key2css_id( color_palette_item_key );  
						
						//console.log('color_palette_item_css_id');console.log(color_palette_item_css_id);
						//color_palette_item_value = $( '#' + color_palette_item_css_id ).val();
						
						var color_palette_item_value = wp.customize( setting_id ).get();
						
						filo_change_reference_color_fields(color_palette_item_key, color_palette_item_value);
					});
				}
					
				
			
				function filo_change_reference_color_fields(color_palette_item_key, newval){


					// foreach
					// loop through on each color type fields (normal color fields)
					filo_color_field_control_and_setting_keys.forEach(function( normal_color_field_control_and_setting_key ) {


						// 1. Change normal color fields value
						// If a mycolor palette item is changed, modify the normal color fields that is referenced that

						// calculate the mycolor palette reference field setting id that belongs to the normal color field								
						mycolor_reference_setting_key = normal_color_field_control_and_setting_key.slice(0, -1) + '_mycolor_ref]'; //remove the original last character ] from the end and add _mycolor_ref], e.g. filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color] to filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color_mycolor_ref] php: rtrim( $setting_id, "]" ) . '_mycolor_ref]';  

						// get the value of the mycolor reference key 								 
						mycolor_reference_value = wp.customize( mycolor_reference_setting_key ).get();
						
						// Examine whether the changed mycolor palette item field name is contained by the viewd mycolor reference key (e.g. filo_color_3)
						if ( mycolor_reference_value == color_palette_item_key ) {
							
							// the mycolor reference field value of this normal color field is the same that palette color is changed (e.g. filo_color_2 - filo_color_2), 
							// so the belonging normal color field value should be changed to the changed palette color field value
							wp.customize( normal_color_field_control_and_setting_key ).set( newval );
						
						}
						
						// 2. COLORIZED opitions: modify selector OPTIONS background color
						// If a color palette item is changed, modify the color of the mycolor reference options for that palette item
						
						// generate css #id from settings id by replacing [] to - and using customize-control- prefix
						//var mycolor_reference_field_css_id = mycolor_reference_setting_key
						//mycolor_reference_field_css_id = mycolor_reference_field_css_id.replace(/\[/gi, '-'); //replace('[', '-') more times
						//mycolor_reference_field_css_id = mycolor_reference_field_css_id.replace(/\]/gi, ''); //replace(']', '') more times
						//mycolor_reference_field_css_id = 'customize-control-' + mycolor_reference_field_css_id;
						
						var mycolor_reference_field_css_id = filogy_convert_setting_key2css_id( mycolor_reference_setting_key );

						// calculate the contrast color: http://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color
						contrast_color = get_contrast_color( newval );
						
						// set background color of the given option item
						$( '#' + mycolor_reference_field_css_id + ' option[value="' + color_palette_item_key + '"]').css('background-color', newval );  //option[value="filo_color_3"]

						// set font color to be in contrast of the background of the given option item
						$( '#' + mycolor_reference_field_css_id + ' option[value="' + color_palette_item_key + '"]').css('color', contrast_color );  //option[value="filo_color_3"]
						
						
						// 3. ACTUAL SELECTOR color:
						// if the selected value in the mycolor reference field is the same as our actual color palette item (e.g. filo_color_3), then set a colored left border
						if ( mycolor_reference_value == color_palette_item_key) {
							$( '#' + mycolor_reference_field_css_id + ' .customize-control-color-palette-item_select_frame.frame1').css('background-color', newval );
						}

					});					
				};			
			
			
				function filo_normal_color_field_or_palette_item_selector_changed() {
					<?php
					
					// 2. change the value of color field if the belonging reference field is changed (under the color field)  
					// If a color palette item selector value is changed, then the belonging color picker value has to be changed
					
	 				// Go trought on every normal color fields
					foreach ( $filo_color_field_control_and_setting_keys as $normal_color_field_control_and_setting_key ) {
						
						// calculate the setting id of color palette reference field
						$mycolor_reference_setting_key = rtrim( $normal_color_field_control_and_setting_key, "]" ) . '_mycolor_ref]'; //remove the original ] from the end and add _mycolor_ref], e.g. filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color] to filo_doc[fd_row_widgets][Filo_Head_Row_1][css_item_table_body_cell_selector][background-color_mycolor_ref]
						
						//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $mycolor_reference_setting_key: ' .  wsl_vartotext( $mycolor_reference_setting_key ));
						
						// generate css #id from settings id by replacing [] to - and using customize-control- prefix
						$normal_color_field_css_id = str_replace( '[', '-', $normal_color_field_control_and_setting_key);
						$normal_color_field_css_id = str_replace( ']', '', $normal_color_field_css_id);
						$normal_color_field_css_id = 'customize-control-' . $normal_color_field_css_id;
						//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $normal_color_field_css_id: ' .  wsl_vartotext( $normal_color_field_css_id ));					
				
						?>
						// if a mycolor reference field is changed, then the belonging normal color field will be changed to the acual color of the referenced palette item field
						wp.customize( '<?php echo $mycolor_reference_setting_key ?>', function( value ) {
							value.bind( function( newval ) {
								
								// to avoid infinite loop, the empty mycolor reference field value has no effect, because the normal color field changes clear the ref field, and these change of re field would update the normal color field, that try to make it empty again 
								if ( newval != '') {
									// calculate the setting id of that color palette field, that has choosen in the color palette reference field
									//console.log('newval');console.log(newval);console.log('setting_id_prefix');console.log(setting_id_prefix);
									color_palette_item_setting_id = setting_id_prefix + '[' + newval + ']';
									
									//get the value of the color palette element field (the color of that field)
									//console.log('color_palette_item_setting_id');console.log(color_palette_item_setting_id);
									color_palette_item_value = wp.customize( color_palette_item_setting_id ).get();
	
									// generate palette ref field css id from normal color css id
									mycolor_reference_field_css_id = '<?php echo $normal_color_field_css_id; ?>_mycolor_ref';
									
									// if the color palette element field has a value, then set it to the normal color field, otherwise clear the normal color field
									if ( color_palette_item_value != '' ) {
									
										// the color palette reference field value is changed, thus we have to update the color of the belonging normal color field  
										wp.customize( '<?php echo $normal_color_field_control_and_setting_key; ?>' ).set( color_palette_item_value );
										
										
										//set a colored left border of the select field if the content is changed
										//$( '#' + mycolor_reference_field_css_id + ' select').css('border-width', '0px' ); 
										//$( '#' + mycolor_reference_field_css_id + ' select').css('border-left', '32px solid ' + color_palette_item_value );
										$( '#' + mycolor_reference_field_css_id + ' .customize-control-color-palette-item_select_frame.frame1').css('background-color', color_palette_item_value );
										
									} else {
		
										// empty value cannot be set in case of a color field, so that we trigger a click event on color picker Clear button
										// http://wordpress.stackexchange.com/questions/201717/how-to-clear-wpcolorpicker-iris-js?rq=1
										$( '<?php echo '#' . $normal_color_field_css_id; ?> .wp-picker-clear').trigger('click');
										
										//clear the colored left border of the select field if the content is changed
										//$( '#' + mycolor_reference_field_css_id + ' select').css('border-width', '0px' ); 
										$( '#' + mycolor_reference_field_css_id + ' .customize-control-color-palette-item_select_frame.frame1').css('background-color', '#f7f7f7' );
	
	
									
									}
								}
	
							});
						});
						
						
						// 3. if a normal color field is changed, then the belonging palette color reference field AND it's left border color should be cleared 
						wp.customize( '<?php echo $normal_color_field_control_and_setting_key ?>', function( value ) {
							value.bind( function( newval ) {
								
								// We have to check that the selected color of normal color field is different than the belonging palette color. If different, we should delete the belonging palette reference field, othewise must not delete, because we lose the reference value (e.g. if we choose a reference, the normal color will be changed (to the same that the palette color), but the referenc lost without this check) 
								
								// get the belonging normal field content 
								normal_color_value = wp.customize( '<?php echo $normal_color_field_control_and_setting_key ?>' ).get();
								
								// get actual reference value (e.g. filo_color_2)
								mycolor_reference_value = wp.customize( '<?php echo $mycolor_reference_setting_key; ?>' ).get();
								
								
								// if empty color palette reference, then we cannot get the value (it caused an error), and of course we do not have to clear the content
								if ( mycolor_reference_value != '') {
								
									// get the value of the referenced palette field
									color_palette_item_value = wp.customize( setting_id_prefix + '[' + mycolor_reference_value + ']' ).get();
									
									// examine if the new value is the same as the referenced color palette field value. If so, nothing to do 
									if ( color_palette_item_value != normal_color_value ) {
										
										//delete content of reference color field
										wp.customize( '<?php echo $mycolor_reference_setting_key; ?>' ).set( '' );
										
										// generate palette ref field css id from normal color css id
										mycolor_reference_field_css_id = '<?php echo $normal_color_field_css_id; ?>_mycolor_ref';
										
										//clear the colored left border of the select field if the content is changed
										//$( '#' + mycolor_reference_field_css_id + ' select').css('border-width', '0px' );
										$( '#' + mycolor_reference_field_css_id + ' .customize-control-color-palette-item_select_frame.frame1').css('background-color', '#f7f7f7' );
									}
								}
								
							});
						});					
						<?php
					}
					?>
				}
				
				function filo_set_color_picker_palette_colors() {
				
					// generate colors of color picker palette
					
					$( ".filo_customize_generate_color_palette_button" ).on( "click", function() {
						
						if (confirm('Are you sure you want to update your actual colors?')) { //it is commented out because color picker fields "store" the old values on page refresh (but we did not saved), this may cause confusion, and data loss.

							// Do it!
						
							var primary_hex = $( '#customize-control-filo_doc-fd_color_palette-filo_color_primary_color input.wp-color-picker' ).val();
							var accent_hex = $( '#customize-control-filo_doc-fd_color_palette-filo_color_accent_color input.wp-color-picker' ).val();
	
							//get primary color object						
							var primary = surfacecurve.color( primary_hex );
							
							//create dark and light primary color objec ba adjusting saturation and brightness value 
							var dark_primary = primary.saturation('*1').value('*0.70'); //0.8
							var light_primary = primary.saturation('*0.25').value('*1.35'); //1.25
							
							//convert color object to hexa string (e.g #222222)
							var dark_primary_hex = dark_primary.hex6();
							var light_primary_hex = light_primary.hex6();
	
							//create contrast colors (black or white) for promary colors and accent color
							var primary_text_hex = get_contrast_color( primary_hex );						
							var dark_primary_text_hex = get_contrast_color( dark_primary_hex );
							var light_primary_text_hex = get_contrast_color( light_primary_hex );
							var accent_text_hex = get_contrast_color( accent_hex );
							
							var main_text_hex = '#222222';
							var secondary_text_hex = '#777777';
							var delicate_hex = '#f4f4f4';
							
							var setting_id_prefix = 'filo_doc[fd_color_palette]';
							
							
							// In the lines below, we could change color values by using wp.customize .set method, in this case trigger("change") is not deeded to the change, but after the change, the referenced color picker value also have to be changed, that does not work without trigger("change"). That is why, we prefer the $(...).val(...) + trigger("change") solution.  
							//set the above calculated values
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_dark_primary_color input.wp-color-picker' ).val( dark_primary_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_dark_primary_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_dark_primary_color]' ).set(dark_primary_text_hex);													
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_light_primary_color input.wp-color-picker' ).val( light_primary_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_light_primary_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_light_primary_color]' ).set(light_primary_hex);
							
							
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_primary_text_color input.wp-color-picker' ).val( primary_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_primary_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_primary_text_color]' ).set(primary_text_hex);
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_dark_primary_text_color input.wp-color-picker' ).val( dark_primary_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_dark_primary_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_dark_primary_text_color]' ).set(dark_primary_text_hex);						
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_light_primary_text_color input.wp-color-picker' ).val( light_primary_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_light_primary_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_light_primary_text_color]' ).set(light_primary_text_hex);
							
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_accent_text_color input.wp-color-picker' ).val( accent_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_accent_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_accent_text_color]' ).set(accent_text_hex);
							
							//set fix text values
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_main_text_color input.wp-color-picker' ).val( main_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_main_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_main_text_color]' ).set(main_text_hex);
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_secondary_text_color input.wp-color-picker' ).val( secondary_text_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_secondary_text_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_secondary_text_color]' ).set(secondary_text_hex);
							$( '#customize-control-filo_doc-fd_color_palette-filo_color_delicate_color input.wp-color-picker' ).val( delicate_hex ); $( '#customize-control-filo_doc-fd_color_palette-filo_color_delicate_color input.wp-color-picker').trigger("change");
							//wp.customize( setting_id_prefix + '[filo_color_delicate_color]' ).set(delicate_hex);
							
						} else {
						    // Do nothing!
						}						        
					        
					});
					
						
					// change color picker palettes
					// http://automattic.github.io/Iris/	
					$('.wp-color-picker').iris({
						palettes: [
							'#84594a', //brown
							//'#aeaeae', //grey							
							'#66899a', //blue grey
							
							'#000000', //black
							'#ffffff', //white

							
							'#fc3223', //red 
							'#fc0a5b', //pink 
							'#a91bc2', //purple 
							'#384dc7', //indigo 
							'#0d91fc', //blue 
							'#03adfc', //light blue 
							'#02cee9', //cyan 
							'#48c24c', //green
							'#92d544', //light green
							'#def12e', //lime
							'#fce726', //yellow
							'#fcbe03', //amber
							'#fc9803', //orange
							'#fc430a' //deep orange
						]
					});
					 
				}		
				
			
			})( jQuery );
			
			/**
			 * Give back black or white color contrasting to the given color
			 */
			function get_contrast_color(color) {
			    
			    if ( ! color ) { 
			    	return ''; 
			    }
			    
				// get color object
				// we do not use contrastWhiteBlack, because offset (0x100000) cannot be applied
				//var color_obj = surfacecurve.color( color );
				//return color.contrastWhiteBlack().hex6();		    
			    
			    //return ( parseInt( color.replace( '#', '' ), 16 ) > 0xffffff / 2 ) ? '#000000' : '#ffffff';
			    return ( parseInt( color.replace( '#', '' ), 16 ) > 0xffffff / 2 + 0x100000 ) ? '#000000' : '#ffffff';
			    
			}

		</script>
		<?php
				
	}	


	/** 
	 * print_scripts_of_controls_filo_accordions
	 * 
	 * Filogy Accordions
	 */
	static function print_scripts_of_controls_filo_accordions() {
		global $wp_customize;
		//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_color_palette $wp_customize: ' .  wsl_vartotext( $wp_customize ));

		$controls = $wp_customize->controls();
						
		?>
		<script id="filogy_scripts_of_controls_filo_accordions">
			( function( $ ) {
			
				$(document).ready(function(){

					filo_on_click_menu_item();
					filo_move_controls_to_prev_accodion();
					filo_convert_small_tags_in_labels();
					filo_add_accordion_title_beside_icon_wrapper();
					
					/**
					 * Open or close menu item by clicking on it. 
					 */
					function filo_on_click_menu_item() {
			
						$( '.filo-accordion-item-handle' ).on( 'click', function( e ) {
						
							e.preventDefault();
							e.stopPropagation();

							filo_toggle_menu_item(null, $(this));
							
						} );
					}
			
					function filo_open_menu_item( element ) {
						filo_toggle_menu_item( true, element );
					}
			
					function filo_close_menu_item( element ) {
						filo_toggle_menu_item( false, element );
					}
			
			
					/**
					 * Change the menu item visibility
					 */
					function filo_toggle_menu_item( to_open, menu_item_element ) {
						var self = menu_item_element, accordion_item, first_accordion_settings, ready;
			
						//find the filo-accordion-item in parents
						accordion_item = menu_item_element.closest('.filo-accordion-item');

						//find first filo-accordion-settings inside our accordion_item   						
						first_accordion_settings = $( '.filo-accordion-settings:first', accordion_item );

						//if we have not got any parameter or it is null, then set the opposite of actual visibility						
						if ( 'undefined' === typeof to_open || null === to_open ) {
							to_open = ! first_accordion_settings.is( ':visible' );
						}

						// if it has to open and it is opened or it has not to open and it is closed, then nothing to do
						if ( first_accordion_settings.is( ':visible' ) === to_open ) {
							return;
						}
			
						if ( to_open ) {
							
							//get the id of upper customize-control-nav_menu_item (this id contains the unique id of this accordion item)
							$this_accordion_item_id = menu_item_element.closest('.customize-control-nav_menu_item').attr('id');
							
							//close all other menu item controls
							$( '.filo-accordion-item-handle' ).each( function( index ) {
								
								$other_accordion_item = $( this ).closest('.customize-control-nav_menu_item');
								$other_accordion_item_id = $other_accordion_item.attr('id');

								if ( $this_accordion_item_id !== $other_accordion_item_id ) {
									
									//find handler inside the other item   						
									//$other_item_handler = $( '.filo-accordion-item-handle:first', this );
									$other_item_handler = $( this );
									filo_close_menu_item($other_item_handler);
								}
								
							});
			
							ready = function() {
								accordion_item.removeClass( 'menu-item-edit-inactive' ).addClass( 'menu-item-edit-active' );
							};
			
							accordion_item.find( '.item-edit' ).attr( 'aria-expanded', 'true' );
							first_accordion_settings.slideDown( 'fast', ready );
			
						} else {
							ready = function() {
								accordion_item.addClass( 'menu-item-edit-inactive' ).removeClass( 'menu-item-edit-active' );
							};
			
							accordion_item.find( '.item-edit' ).attr( 'aria-expanded', 'false' );
							first_accordion_settings.slideUp( 'fast', ready );
						}
					}	
					

					
					/**
					 * Move controls into the previous accordion
					 */
					function filo_move_controls_to_prev_accodion() {
						
						//get all controls to move them into accordions					
						$( 'li.customize-control' ).each( function( index ) {
							
							//if the control is not in an exception of moving into a accordion
							//e.g. the accordion iself (customize-control-nav_menu_item) must not move into another accordion
							if ( ! $(this).hasClass( "customize-control-nav_menu_item" ) ) {
							
								// find our own (closest) parent li element, then the previous accordion li element (li.customize-control-nav_menu_item) (first is closest-preceding one)
								// in which this control has to be moved
								// http://stackoverflow.com/questions/16451214/jquery-find-previous-element-with-class
								the_li_block_to_be_moved = $(this).closest('li.customize-control');
														
								accordion_li_block = the_li_block_to_be_moved.prevAll('li.customize-control-nav_menu_item').first();
								
								//find the settings block of the accordion (filo-accordion-settings) 
								accordion_setting_block = $( '.filo-accordion-settings:first', accordion_li_block );
								
								// move the control li into the accordion li->ul
								// http://stackoverflow.com/questions/1279957/how-to-move-an-element-into-another-element
								
								accordion_setting_block.append(the_li_block_to_be_moved); 
								
							}
						});
					}
					
					/**
					 * filo_convert_small_tags_in_labels
					 * 
					 * labels are displayed using esc_html(), thus <small> tags are not working
					 * we have convert it:
					 * 
					 * &lt;small&gt; -> <small>
					 * &lt;/small&gt; -> </small>
					 */
					function filo_convert_small_tags_in_labels() {
						
						//&lt;small&gt; -> <small>
						//&lt;/small&gt; -> </small>
						
						
						$( '.customize-control-title' ).each( function( index ) {
							label = $(this).html();
							label = label.replace("&lt;small&gt;", "<small>");
							label = label.replace("&lt;/small&gt;", "</small>");
							$(this).html(label);
						});
						
					}

					/**
					 * filo_add_accordion_title_beside_icon_wrapper
					 * 
					 * We need an additional wrappar for the original customize menu title
					 * to be able to align inline-block display for the original menu title (and the menu icons (:before)).
					 */					
					function filo_add_accordion_title_beside_icon_wrapper() {
						
						$( 'h3.accordion-section-title' ).each( function( index ) {
							content = $(this).html();
							content = '<div class="accordion_title_beside_icon">' + content + '</div>'; 
							$(this).html(content);
						});

					}

				});
			
			})( jQuery );
			
		</script>
		<?php
				
	}	


	/** 
	 * print_scripts_of_controls_saving_options
	 */
	static function print_scripts_of_controls_saving_options() {
		global $wp_customize;

		$controls = $wp_customize->controls();

		if ( isset($_GET['filo_new_template_name']) ) {
			$filo_new_template_name = rawurlencode( wc_clean( $_GET['filo_new_template_name'] ) ); //$_GET automatically decode the encoded parameter, we need the encoded version. rawurlencode should be applied to get the same result as JavaScript encodeURI //+wc_clean 
		} else {
			$filo_new_template_name = '';
		}
		
		if ( isset($_GET['filo_new_opt_name']) ) {
			$filo_new_opt_name = rawurlencode( wc_clean( $_GET['filo_new_opt_name'] ) ); //$_GET automatically decode the encoded parameter, we need the encoded version. rawurlencode should be applied to get the same result as JavaScript encodeURI  //+wc_clean
		} else {
			$filo_new_opt_name = '';
		}
		
		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_saving_options $filo_new_opt_name: ' .  wsl_vartotext( $filo_new_opt_name ));

		$filo_doc_act_opt_names = self::get_saved_option_names_and_values( 'filo_doc_act_opt_name_%' );
		$filo_doc_options = self::get_saved_option_names_and_values( 'filo_doc_opt_%' );
		
		wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_saving_options $filo_doc_act_opt_names: ' .  wsl_vartotext( $filo_doc_act_opt_names ));
		//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_saving_options $filo_doc_options: ' .  wsl_vartotext( $filo_doc_options )); //big

		$wsl_helper_options = get_option('webshoplogic_helper', array());
		
		if (isset($wsl_helper_options['enable_saving_filoprotect_skins'])) {
			$enable_saving_filoprotect_skins = $wsl_helper_options['enable_saving_filoprotect_skins'];
		} else {
			$enable_saving_filoprotect_skins = null;
		}

		?>
		<script id="filogy_scripts_of_controls_saving_options">
			( function( $ ) {
			
				$(document).ready(function(){

					/**
					 * SAVING OPTIONS
					 */

					filo_refresh_filo_new_opt_name_select_field();
					filo_disable_readonly_saving_fields();
					filo_change_url_parameters_of_open_new_skin_and_delete_button();
					filo_change_actual_fields_by_url_parameters();
					filo_before_save_1();
					filo_before_delete_skin();
					filo_disable_edit_of_protected_skins();
					filo_dis_controls();
					filo_clear_url();
					filo_delete_unnecessary_customizer_url_parameters();
					filo_display_spinner_on_page_refresh();
					
					
					
					function filo_refresh_filo_new_opt_name_select_field() {

						// if Actual Document Template field is changed, then change the Actual option name and the openable option name list belonging to the choosen template					
						//wp.customize( 'filo_doc_filo_new_template_name', function( value ) { //OLD: filo_document_template
						//	value.bind( function( newval ) {
						$("#filo_doc_filo_new_template_name").change(function(){
							
							var newval = this.value;
							
							option_prefix = 'filo_doc_act_opt_name_';
							//convert php array to javascript
							var filo_doc_act_opt_names = <?php echo json_encode( $filo_doc_act_opt_names ); ?>;
							var filo_doc_options = <?php echo json_encode( $filo_doc_options ); ?>;
	
							var new_act_opt_name = filo_doc_act_opt_names[option_prefix + newval];
							
							//REFRESH Open option feild list
							
							//remove all existing options
							$('#filo_doc_filo_new_opt_name option').remove();
							
							//insert an empty option  
							$( "select#filo_doc_filo_new_opt_name" )
								.append($("<option></option>")
									.attr("value", '') //key
									.text(''));  //text
	
							 
							//choose array element for the choosen template
							
							//var schema_keys_values = []; //create the empty array
							for (full_schema_key in filo_doc_options) {
						    	
						    	if ( full_schema_key.indexOf('filo_doc_opt_' + newval) != -1 ) { //if arrey key contains the choosen template name
									schema_key_position = full_schema_key.indexOf('--'); //find the start position of the real schema key
									schema_key_position += 2; //increase position to handle -- separators
									schema_key = full_schema_key.substring(schema_key_position); //cut the full prefix and het the real schema key
									var schema_name = decodeURI(schema_key);
									schema_name = schema_name.replace("filoprotect_", "");
									
									//schema_keys_values[schema_key] = decodeURI(schema_key); //set key-value pairs
									
									//insert the value to open field  
									$( "select#filo_doc_filo_new_opt_name" )
										.append($("<option></option>")
											.attr("value",schema_key) //key
											.text(schema_name));  //text
									
								} 				    	
	
							}
							
							$('#filo_doc_filo_new_opt_name').val(new_act_opt_name);
						
						});

					}
					
					
					function filo_disable_readonly_saving_fields() {
						
						// Disable act-template and act-opt-name select field, it is never be changed by the user
						$( "#customize-control-filo_document_template select" ).attr('disabled', 'disabled');
						//$( "#customize-control-filo_doc_act_opt_name_filo_standard_template select" ).attr('disabled', 'disabled');
						$( "#customize-control-filo_doc_act_opt_name select" ).attr('disabled', 'disabled');
						
						//if save_as_opt_name field had a saved value, then clear it during page load
						$( "#customize-control-filo_doc-fd_saving_options-save_as_opt_name input" ).val('');
						
					}
					
					//----------
					
					function filo_change_url_parameters_of_open_new_skin_and_delete_button() {
						// Change the link
						// if filo_doc_act_opt_name field is changed, then update the filo_new_opt_name parameter in URL of "Change" link.
						// Refresh <filo_new_opt_name> in "Change" button link when Actual Options Name field is changed
						// We have to do the same with delete 2 link, because instead of the deleted skin a new one can be displayed if the user have selected one.
						
						//We call it on page load (to be initiated if the value is not changed), and also call at every change
						set_filo_new_opt_name_in_changebutton_and_delete_links();
						$( "select#filo_doc_filo_new_template_name" ).change(set_filo_new_opt_name_in_changebutton_and_delete_links);
						$( "select#filo_doc_filo_new_opt_name" ).change(set_filo_new_opt_name_in_changebutton_and_delete_links);
									
						function set_filo_new_opt_name_in_changebutton_and_delete_links() {
							
							//get the value of Actual Template Name and Actual Options Name fields (the select option keys are already url encoded, thus can be inserted into the link)
							var filo_doc_filo_new_template_name = $( "select#filo_doc_filo_new_template_name" ).val();
							var filo_doc_filo_new_opt_name = $( "select#filo_doc_filo_new_opt_name" ).val();


							// CHANGE LINK						  

							//get the href attribute of Change button											  	
							var filo_doc_change_act_opt_name_href = $("a.filo-doc-change-act-opt-name").attr("href");
							
							//change filo_new_template_name and filo_new_opt_name http parameters value of the Change link button to the filo_doc_act_opt_name field value
							filo_doc_change_act_opt_name_href = change_href_param_by_name___filocustman(filo_doc_change_act_opt_name_href, "filo_new_template_name", filo_doc_filo_new_template_name);    //changeHrefParamByName(href, paramName, newVal);
							filo_doc_change_act_opt_name_href = change_href_param_by_name___filocustman(filo_doc_change_act_opt_name_href, "filo_new_opt_name", filo_doc_filo_new_opt_name);    //changeHrefParamByName(href, paramName, newVal);
	
							//update the href attributs to the changed parameter
							$("a.filo-doc-change-act-opt-name").attr("href", filo_doc_change_act_opt_name_href);


							// DELETE LINK						  

							//get the href attribute of Delete Options button
							var filo_doc_delete_opt_href = $("a.filo-customize-delete-opt-button2").attr("href");
							
							//change filo_new_template_name and filo_new_opt_name http parameters value of the Change link button to the filo_doc_act_opt_name field value
							filo_doc_delete_opt_href = change_href_param_by_name___filocustman(filo_doc_delete_opt_href, "filo_new_template_name", filo_doc_filo_new_template_name);    //changeHrefParamByName(href, paramName, newVal);
							filo_doc_delete_opt_href = change_href_param_by_name___filocustman(filo_doc_delete_opt_href, "filo_new_opt_name", filo_doc_filo_new_opt_name);    //changeHrefParamByName(href, paramName, newVal);
	
							//update the href attributs to the changed parameter
							$("a.filo-customize-delete-opt-button2").attr("href", filo_doc_delete_opt_href);
						
						}

					}
					
					// Function for change a param inside of a href by name
					// http://blog.adrianlawley.com/jquery-change-url-parameter-value/
					// see also: filogy/assets/js/admin/settings.js
					function change_href_param_by_name___filocustman(href, paramName, newVal) {
						if (typeof href === 'string' || href instanceof String) {
							var tmpRegex = new RegExp("(" + paramName + "=)[[A-Za-z0-9%_\\-+]*", "ig"); //we need % for hendling umlencoded string and * (inside of +) to handle empty parameter, where there is no character after =
							return href.replace(tmpRegex, "$1"+newVal);  //$1 is the original parameter, and this is completed by the new value
						} 
					}
					
					// Function for delete a param inside of a href by name
					function delete_href_param_by_name___filocustman(href, paramName) {
						if (typeof href === 'string' || href instanceof String) {
							var tmpRegex = new RegExp("(" + paramName + "=)[[A-Za-z0-9%_\\-+]*", "ig");
							return href.replace(tmpRegex, "");  //we replace the parameter by '' string
						} 
					}
					
					//---------
					
					function filo_change_actual_fields_by_url_parameters() {
						// Update act opt name field, set the filo_new_opt_name GET url parameter into it
						// because if we selected to another opt name (this is in the change link), then we should use it in this field
						
						filo_new_template_name_in_url = '<?php echo $filo_new_template_name ?>'; //it is urlencoded by php code above
						filo_new_opt_name_in_url = '<?php echo $filo_new_opt_name ?>'; //it is urlencoded by php code above
						
						//console.log('filo_new_opt_name_in_url');
						//console.log(filo_new_opt_name_in_url);
						
						if ( filo_new_template_name_in_url != '') {
							$( ".customize-control-filo-doc-act-template-name select" ).val(filo_new_template_name_in_url);
						}
	
						if ( filo_new_opt_name_in_url != '') {
							//$( ".customize-control-filo-doc-act-opt-name select" ).val(filo_new_opt_name_in_url);
							$( "[id^='customize-control-filo_doc_act_opt_name'] select" ).val(filo_new_opt_name_in_url);   // select field inside of an id that begins with 'customize-control-filo_doc_act_opt_name', e.g.: customize-control-filo_doc_act_opt_name_filo_standard_template
						}
						
					}
					
					//---------
					
					function filo_before_save_1() {
						
						// set act_option_name field by save_as_option_name
						
						//if save as field is not empty, then before save, we have to copy it to actual opt name field, to be loaded next time this new option
						//We have to unbind the original click event from save button, then add our own, and then bind the original save event to a new hidden, save2 button.
						//When the user click on save button, our own event will be triggered that do the task mentioned above, 
						//and then, if it was successfull, it call the trigger of save2 button, that executes the original saving task of customizer.
						//http://stackoverflow.com/questions/22106192/jquery-onclick-before-jquery-click
						
						var save_btn = $( "input#save" );
						var save_btn2 = $( "input#filo-customize-save2" ); //this is a hidden button to be possible to click on it and done the original saving function of customizer
						
						// cache the original click event
						savedClickListener = jQuery._data(save_btn[0], 'events').click[0];
						
						// bind the original save event to the save2 button
						save_btn2.click(savedClickListener.handler);
						
						// Remove all click binds from original save button
						save_btn.off('click'); 
						
						
						
						// Add our own click event to the orignal save button
						// clicking on save button, copy the content of save as field to the actual opt name field, if the save as field has value. 
						save_btn.click(function(e){

							//prevent the original submit function of button (it is especially important for our own save as button)
							e.preventDefault();
	
							//get the value of Save as Option Name field
							var save_as_option_name = $( "#customize-control-filo_doc-fd_saving_options-save_as_opt_name input" ).val();
	
	
							//SAVE PROTECTION
							
							var enable_saving_filoprotect_skins = <?php echo $enable_saving_filoprotect_skins == 1 ? 1 : 0; ?>;
							
							//get the options of #filo_doc_filo_new_opt_name field
							var existing_options = $('#filo_doc_filo_new_opt_name option');
							
							var existing_option_values = $.map(existing_options ,function(existing_option) {
	    						return existing_option.value; //texts also can be selected: existing_option.text
							});
							
							var existing_option_texts = $.map(existing_options ,function(existing_option) {
	    						return existing_option.text; 
							});
							
							//var is_protected_skin = $( "#customize-control-filo_doc-fd_saving_options-protected_skin input" ).val();
							
							var act_skin_opt_name = $('#customize-control-filo_doc_act_opt_name select').val();
							
							var message_txt;
	
							//console.log('save_as_option_name');
							//console.log(encodeURI(save_as_option_name));
							//console.log('existing_option_values');
							//console.log(existing_option_values);
							//console.log('enable_saving_filoprotect_skins');
							//console.log(enable_saving_filoprotect_skins);
							
							// in case of normal save, where new save as option name is not entered and we are not developers (for whom overriden of protected skin is enabled) ensure that a protected skin cannot be overwritten.
							if ( save_as_option_name == '' && act_skin_opt_name.toUpperCase().search("FILOPROTECT_") == 0 ) {
								
								message_txt = "This is a protected skin, that cannot be overwritten. You can save as a different Skin name, that can be entered into Saving Options / Save Skin as field.";
								 
								if ( enable_saving_filoprotect_skins !== 1 ) { //for none developers
									alert(message_txt);
									return false;
								} else { //for developers
									alert("DEV MODE: Save will happen; Original message for none developers:  [" + message_txt + "]");
								}
								
							}
							
							// filoprotect_ is a reserved prefix, it is not allowed that the given skin name begins with it,
							// except if enable_saving_filoprotect_skins helper function is enabled, because then filoprotect_ prefix is allowed
							if ( save_as_option_name != '' && save_as_option_name.toUpperCase().search("FILOPROTECT_") === 0 ) {
								
								message_txt = "The following prefix cannot be applied in skin names: filoprotect_";
								 
								if ( enable_saving_filoprotect_skins !== 1 ) { //for none developers
									alert(message_txt);
									return false;
								} else { //for developers
									alert("DEV MODE: Save will happen; Original message for none developers:  [" + message_txt + "]");
								}
								
							}
	
							// if a protected skin exists (the given skin name extended by the prefix), that it is not savable, because a new skin generated with the same name as the protected one (the difference is the hidden prefix) 
							if ( save_as_option_name != '' && jQuery.inArray(encodeURI('filoprotect_' + save_as_option_name), existing_option_values) !== -1 ) {
								
								message_txt = "The given skin name already exists as a protected skin. It cannot be overwritten!";
								
								if ( enable_saving_filoprotect_skins !== 1 ) { //for none developers
									alert(message_txt);
									return false;
								} else { //for developers
									alert("DEV MODE: Save may happen; Original message for none developers:  [" + message_txt + "]"); //save will happen if it will be confirmed
								}

							}	
							
							// if the entered option name (skin name) is not empty and has already exist
							// more exactly if it exists by the encoded value, or exists by the not encoded text (these two is important because value may contain filoprotect_ prefix, but text not. None of them with or without prefix can be identical.)
							if ( save_as_option_name != '' && (
								jQuery.inArray(encodeURI(save_as_option_name), existing_option_values) !== -1 || 
								jQuery.inArray(save_as_option_name, existing_option_texts) !== -1 ) 
								) {
								//found
								
								if ( ! confirm( 'The skin already exists. Do you want to overwrite it?' ) ) {
									// if the option is exists and must not overwrite
									return false;
								}

							}
							
							
							var import_settings_json_field = $( "#customize-control-filo_doc-fd_import_settings textarea" ).val();
							//console.log(import_settings_json_field);
							if ( import_settings_json_field != '' && import_settings_json_field != undefined ) {
	
								if ( ! confirm( 'There is content entered in Import Settings (JSON) field. It will overwrite all the settings of actual skin. Do you want to overwrite this skin settings? If not, clear Import Settings (JSON) field before save.' ) ) {
									return false;
								}
								
							}

							// if the skin name does not exist or exist and the overwrite is confirmed, then continue saving
							
							//SAVE PROTECTION END
							
							
							//------------
							
							// Change_browser_address_bar_url, delete filo_new_template_name and filo_new_opt_name parameters, when save button is clicked.
							// That prevents that after a "change" this parameter got stuck. Thus after a save it will not "changes" automatically an earlier wrong option, instead of the newly saved option.
							
							//When we save, remove the filo_new_template_name and filo_new_opt_name parameters from the URL
							current_url = window.location.href;
							
							//delete filo_new_template_name and filo_new_opt_name parameters from url address bar of the browser  
							current_url = delete_href_param_by_name___filocustman(current_url, 'filo_new_template_name');
							current_url = delete_href_param_by_name___filocustman(current_url, 'filo_new_opt_name');
							current_url = delete_href_param_by_name___filocustman(current_url, 'filo_delete_skin');
							
							//change browser addressbar url
							var stateObj = {};
							history.replaceState(stateObj, "Customize", current_url); 
	
							//------------
	
							//Copy save as name into the actual option name field, if save as name is given
	
							if ( save_as_option_name != '' ) {
	
								$( "[id^='customize-control-filo_doc_act_opt_name'] select" ) // select field inside of an id that begins with 'customize-control-filo_doc_act_opt_name', e.g.: customize-control-filo_doc_act_opt_name_filo_standard_template
									.append($("<option></option>")
										.attr("value",encodeURI(save_as_option_name)) //key
										.text(save_as_option_name));  //text
	
		
								//set this value to act-opt-name select field before save (when save button is clicked), for the next turn to be read this option
								$( "[id^='customize-control-filo_doc_act_opt_name'] select" ).val(encodeURI(save_as_option_name));   // select field inside of an id that begins with 'customize-control-filo_doc_act_opt_name', e.g.: customize-control-filo_doc_act_opt_name_filo_standard_template
								
								$( "#customize-control-filo_doc-fd_saving_options-save_as_opt_name input" ).val('');
								
								//trigger change to customizer able to save it
								$( "[id^='customize-control-filo_doc_act_opt_name'] select" ).trigger("change");   // select input inside of an id that begins with 'customize-control-filo_doc_act_opt_name', e.g.: customize-control-filo_doc_act_opt_name_filo_standard_template
								$( "#customize-control-filo_doc-fd_saving_options-save_as_opt_name input" ).trigger("change");
	
								//also insert the new value to filo_new_opt_name field as a new option, but it need not be selected automatically  
								$( "select#filo_doc_filo_new_opt_name" )
									.append($("<option></option>")
										.attr("value",encodeURI(save_as_option_name)) //key
										.text(save_as_option_name.replace("filoprotect_", "")));  //text - remove filoprotect_ prefix
								
								
							}
							
							// if save happened as another name, then the old skin name in the delete link is not actual, thus hide the link
							// (link modification with the new skin name and the appropriate nonce would be hard in JS, that is why we hide it, and after a page reload it can be used again)
							if ( save_as_option_name != '' ) {
								$( "a.filo-customize-delete-opt-button" ).css('display', 'none' );
							}	
	
							
	
							//the save before tasks was succesfull, thus we call the original save function
							//by trigger a click on save2 button			
							var save_btn2 = $( "input#filo-customize-save2" );		
							save_btn2.trigger('click');
	
							
							// Rebind the original
							//save_btn.click(savedClickListener.handler);


						});
						
					}
					
					
					function filo_before_delete_skin() {					

						var delete_btn = $( "a.filo-customize-delete-opt-button" );
						var delete_btn2 = $( "a.filo-customize-delete-opt-button2 span" );
						var enable_saving_filoprotect_skins = <?php echo $enable_saving_filoprotect_skins == 1 ? 1 : 0; ?>;
						
						// delete_btn is only a button for executing confirmation.
						// if confirmed, it trigger a click on delete_btn2 button, that executes the real delet
						delete_btn.click(function(e){


							var act_skin_opt_name = $('#customize-control-filo_doc_act_opt_name select').val();
							
							var message_txt
	
							// ensure that a protected skin cannot be deleted
							if ( act_skin_opt_name.toUpperCase().search("FILOPROTECT_") == 0 ) {
								
								message_txt = "This is a protected skin, that cannot be deleted.";
								 
								if ( enable_saving_filoprotect_skins !== 1 ) { //for none developers
									alert(message_txt);
									return false;
								} else { //for developers
									alert("DEV MODE: Delete may happen; Original message for none developers:  [" + message_txt + "]");
								}
								
							}


							if ( ! confirm( 'Are you sure to delete the actual skin?' ) ) {
								// if it is not confirmed must not delete
								return false;
							}
							
						});
												
					}
					
					
					function filo_disable_edit_of_protected_skins() {
						
						var act_skin_opt_name = $('#customize-control-filo_doc_act_opt_name select').val();
						var enable_saving_filoprotect_skins = <?php echo $enable_saving_filoprotect_skins == 1 ? 1 : 0; ?>;

						// Disable fields of protected skins (its name contains filoprotect_ prefix) (for developers the modification can be enabled)						
						if ( act_skin_opt_name != null && act_skin_opt_name.toUpperCase().search("FILOPROTECT_") === 0 ) {
							
							message_txt = message_txt = "This is a protected skin, that cannot be modified. You can save it as a different Skin name. You can do it in Open / Save settings section.";
							
							if ( enable_saving_filoprotect_skins == 1 ) { //for developers
	
								alert("DEV MODE: Protected Skin modification is enabled; Original message for none developers:  [" + message_txt + "]"); 
	
							} else { //for none developers
								
								// disable all input fields, except Save Skin As field and "Save As ... & Publish" button
								$( "#customize-theme-controls input:not(#customize-control-filo_doc-fd_saving_options-save_as_opt_name input, .save.filo-customize-button)" ).attr("disabled", true);
								
								// disable all select fields, except new template and skin selector fields
								$( "#customize-theme-controls select:not(#filo_doc_filo_new_template_name, #filo_doc_filo_new_opt_name)" ).attr("disabled", true);
								
								// disable all select text area fields
								$( "#customize-theme-controls textarea" ).attr("disabled", true);
								
								// disable all links except Change Template and Change Skin buttons (also disable click event, because it can be triggered on a disabled elemene)
								$( "#customize-theme-controls a:not(.filo-doc-change-act-opt-name)" ).attr("disabled", true);
								$( "#customize-theme-controls a:not(.filo-doc-change-act-opt-name)" ).unbind("click");
								
								
								// disable click events on color picker, font selector and image (e.g.) logo selector
								$( "#customize-theme-controls a.wp-color-result" ).unbind("click");
								$( "#customize-theme-controls .font-select span" ).unbind("click");
								$( "#customize-theme-controls .customize-control-image" ).unbind("click");
								
								// disable all buttons, except section and panel back buttons (left upper)
								$( "#customize-theme-controls button:not(.customize-section-back, .customize-panel-back)" ).attr("disabled", true);

								alert(message_txt);
																	
							}	
						}					
					}
					function filo_dis_controls() {
						
						$( "#customize-theme-controls input[data-customize-setting-link*='filo-dis-']" ).attr("disabled", true);
						$( "#customize-theme-controls select[data-customize-setting-link*='filo-dis-']" ).attr("disabled", true);
						$( "#customize-theme-controls textarea[data-customize-setting-link*='filo-dis-']" ).attr("disabled", true);

						// remove click events of color picker, font selector and image (e.g.) logo selector
						$( "#customize-theme-controls li[id*='filo-dis-'] a.wp-color-result" ).unbind("click");
						$( "#customize-theme-controls li[id*='filo-dis-'] .font-select span" ).unbind("click");
						$( "#customize-theme-controls li[id*='filo-dis-'] .customize-control-image" ).unbind("click");
						
					}
					
					function filo_clear_url() {
						
						// The document is loaded, lets remove the filo_delete_skin from the URL address bar of the browser
						current_url = window.location.href;
						current_url = delete_href_param_by_name___filocustman(current_url, 'filo_delete_skin');
						current_url = delete_href_param_by_name___filocustman(current_url, 'filo_delete_skin_nonce');

						//change browser addressbar url
						var stateObj = {};
						history.replaceState(stateObj, "Customize", current_url); 
	
					}

					
					function filo_delete_unnecessary_customizer_url_parameters() {
					
						// Delete changeset_uuid parameter from url address bar of the browser
						// If we would not do this, than in case of the user changed some settings without saving it and do a page refresh (change to another skin or delete the actuel template are also causes page refresh),
						// the setting values of the old page was not be forgetted, and the old values got into the fields of the new page (especialy into color picker).
						// If a skin change happanes, the control field values should be changed to the newly selected skin values (by the get_root_value function),
						// but beside of these new skin values, the old page color picker valuse get there.
						// This way the newly selected skin colors are overwritten by the old page colors, thus we loose the original colors of the new skin.
						
						// Customizer write changeset_uuid parameter into the browser url field when any control field is changed: in customize-controls.js: changesetStatus.bind( function( newStatus ) { 	 \n   populateChangesetUuidParam( '' !== newStatus && 'publish' !== newStatus ); .....
						// We have to remove it. It cannot be removed by unbinding the used function (e.g. changesetStatus2 = state.create( 'changesetStatus' ); 	changesetStatus2.unbind(); ), 
						// also cannot be unbind by using url history change events (because there is no window.onreplacestate event or any other event (e.g.window.onpushstate) that triggers on replace state of history),
						// unload and onbeforeunload events cannot be used also, because onbeforeunload triggered too early and unload too late (e.g. $(window).unload(function() ....DELETE...).
						// Thus we have to check if the history (url) was changed regularly (e.g in every 100 ms), and if so, we delete changeset_uuid get parameter. (http://stackoverflow.com/questions/4570093/how-to-get-notified-about-changes-of-the-history-via-history-pushstate/25673946#25673946)

						// Check if url parameter is changed in every 100ms, end if so, call delete_changeset_uuid_url_parameter() 
					    var previousState = window.history.state;
					    setInterval(function() {
					    	
					    	//console.log('changeset_uuid timer');
					    	//console.log(previousState);
					    	//console.log(window.history.state);
					    	
					    	//if the url is shanged, then try to delete changeset uuid url parameter, and set the previous url to the deleted one
					        if (previousState !== window.history.state) {
					        	
					            previousState = window.history.state;
					            delete_changeset_uuid_url_parameter();
					            previousState = window.history.state;
					            
					        }
					        
					    }, 100);

						function delete_changeset_uuid_url_parameter() {
							
							//delete changeset_uuid parameter from url address bar of the browser
							//this is needed for forgetting the unsaved state (e.g. color fields) on pege refresh or even at switching to another skin
							
							current_url = window.location.href;  
							current_url = delete_href_param_by_name___filocustman(current_url, 'changeset_uuid');
							
							//change browser addressbar url
							var stateObj = {};
							history.replaceState(stateObj, "Customize", current_url);
							 
						}

					}		

											
					//place a spinner before page refresh					
					function filo_display_spinner_on_page_refresh() {
					
						// http://duenorthstudios.com/how-do-you-use-wordpresss-built-in-waiting-loading-spinner/
						// http://jsfiddle.net/mdxQs/  //Display spinner on the center of screen (without "white")
						// http://jsfiddle.net/manjula_dhamodharan/zook0L0f/1/  //Detecting the page refresh
						
						window.onbeforeunload = function(){

							// We had two solutions, the first is create a dummy error for preventing dialog box, the second returns null, that have the same result.
							// But none of them can be used, because in WP there is a quesntion: "Changes you made may not be saved. Do you want to reload this site?"
							// if the user choose not to reload, the spinner also will be displayed, and there will not be page refresh.
														
							/*try {
								
								$('.wp-full-overlay.preview-desktop').append('<div class="filo_customizer_unload_spinner"></div>'); 

								// we have to throw a dummy error to be prevent that onbeforeunload message box is appeared, we only have to start the spinner without any message box 
								throw "filo_dummy_error";
								
							}			
							catch(err) {
							    //catch dummy error, nothing to do
							}*/

							/*
							$('.wp-full-overlay.preview-desktop').append('<div class="filo_customizer_unload_spinner"></div>');
														
							return null;
							*/
																	
						}
						
					}		
					
				});


			})( jQuery );
			
			/** 
			 * filogy_convert_setting_key2css_id
			 */
			function filogy_convert_setting_key2css_id( setting_key ) {
		
				// generate css #id from settings id by replacing [] to - and using customize-control- prefix
				var css_id = setting_key
				css_id = css_id.replace(/\[/gi, '-'); //replace('[', '-') more times
				css_id = css_id.replace(/\]/gi, ''); //replace(']', '') more times
				css_id = 'customize-control-' + css_id;
				
				return css_id;
		
			}
			
		</script>
		<?php
				
	}	


	/** 
	 * convert_setting_key2css_id
	 */
	static function convert_setting_key2css_id( $setting_key ) {

		// generate css #id from settings id by replacing [] to - and using customize-control- prefix
		$css_id = str_replace( '[', '-', $setting_key);
		$css_id = str_replace( ']', '', $css_id);
		$css_id = 'customize-control-' . $css_id;
		//wsl_log(null, 'class-filo-customize-manager.php convert_setting_key2css_id $css_id: ' .  wsl_vartotext( $css_id ));		
		
		return $css_id;

	}

//---
	/** 
	 * print_scripts_of_controls_measurment_unit
	 */
	static function print_scripts_of_controls_measurment_unit() {
		global $wp_customize;

		$settings = $wp_customize->settings();
		
		// FIND all filogy fields that has default measurement unit
		
		$filo_measurement_unit_field_control_and_setting_keys = array();
		
		if ( isset($settings) and is_array($settings) ) 
		foreach ($settings as $control_and_settings_key => $setting) {

			//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_measurment_unit $control_and_settings_key 0: ' .  wsl_vartotext( $control_and_settings_key ));
				
			// We need only those colors, that is used by filo (so begins with filo_doc), and color palette field have to be excluded (that begins with filo_doc[fd_color_palette])			
			if ( property_exists($setting, 'filo_css_measurement_unit') and $setting->filo_css_measurement_unit != '' && $setting->filo_css_measurement_unit != 'NA' ) {
				
				$filo_measurement_unit_field_control_and_setting_keys[$control_and_settings_key] = $setting->filo_css_measurement_unit;
				
			}
			
		}
		
		//wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_measurment_unit $filo_measurement_unit_field_control_and_setting_keys 0: ' .  wsl_vartotext( $filo_measurement_unit_field_control_and_setting_keys ));
		
		?>
		<script id="flogy_scripts_of_controls_measurment_unit">
			( function( $ ) {
			
				$(document).ready(function(){


					<?php
					
					// If a field with measurement unit is changed, then we have to add the unit if it is not empty and the value is a number
					
	 				// Go trought on every field that has measurement unit defined
	 				if ( isset($filo_measurement_unit_field_control_and_setting_keys) and is_array($filo_measurement_unit_field_control_and_setting_keys) ) 
					foreach ( $filo_measurement_unit_field_control_and_setting_keys as $filo_measurement_unit_field_control_and_setting_key => $measurement_unit ) {
						
						wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_measurment_unit $filo_measurement_unit_field_control_and_setting_keys ewewewe: ' .  wsl_vartotext( $filo_measurement_unit_field_control_and_setting_key ));
						wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_measurment_unit $filo_measurement_unit_field_control_and_setting_keys ewewewe222: ' .  wsl_vartotext( self::convert_selector2id($filo_measurement_unit_field_control_and_setting_key) ));
						wsl_log(null, 'class-filo-customize-manager.php print_scripts_of_controls_measurment_unit $filo_measurement_unit_field_control_and_setting_keys ewewewe333: ' .  wsl_vartotext( self::convert_setting_key2css_id($filo_measurement_unit_field_control_and_setting_key) ));
																																																		
						?>
						
						$( '#<?php echo self::convert_setting_key2css_id($filo_measurement_unit_field_control_and_setting_key);?> input' ).change(function(){

							var measurement_unit = "<?php echo $measurement_unit; ?>";
							var original_field_value = $(this).val();
							var new_value = '';
							
							//in case of numeric value, we concatenate the measurement unit and change the field value
							if ( $.isNumeric( original_field_value ) ) {
								new_value = original_field_value + measurement_unit;
								$(this).val( new_value );
								$(this).trigger("change");
							}

						
						})

						<?php
					
					};
					?>

				});
			
			})( jQuery );
			
		</script>
		<?php
				
	}	
//---
	/**
	 * print_css
	 * 
	 * Print css during normal display of documents (this is not used for preview)
	 */	
	public function print_css() {
		
		if (strpos($_SERVER[REQUEST_URI], '/filoinv_template/') !== false ) {
			
			wsl_log(null, 'class-filo-customize-manager.php print_css $_SERVER[REQUEST_URI]: ' .  wsl_vartotext( $_SERVER[REQUEST_URI] ));


			//$template_panels_data = self::get_so_panels_data(); 
			$template_panels_data = self::get_template_panels_data();
			
			wsl_log(null, 'class-filo-customize-manager.php print_css $template_panels_data: ' .  wsl_vartotext( $template_panels_data ));
		
			$all_settings_data = FILO_Customize_Manager::add_panels($template_panels_data, $for_css_render = true );
			
			wsl_log(null, 'class-filo-customize-manager.php print_css $all_settings_data: ' .  wsl_vartotext( $all_settings_data ));
				
		}  
		
	}
	
	//Originally wp_loaded in customize_manager calls customize_preview_init
	//that is add actions to standard wp_head and wp_footer hooks
	//This is not appropriate for us, because menu and other unnecessary content was loaded, 
	//thus in filogy document templates do not call wp_head() and wp_footer() functions,
	//instead of them do our own filo_document_header and filo_document_footer actions.
	//Here we add the original customize_... functions to this filo actions.
	public static function wp_loaded() {
		global $wp_customize;
		
		//wsl_log(null, 'class-filo-customize-manager.php wp_loaded 0: ' .  wsl_vartotext(''));

		//do_action( 'customize_register', $this );

		if ( isset($wp_customize) && $wp_customize->is_preview() && ! is_admin() ) {
			//$this->customize_preview_init();
			//add_action( 'wp_head_filo', array( $wp_customize, 'customize_preview_base' ) ); //wp_head
			//add_action( 'wp_head_filo', array( $wp_customize, 'customize_preview_html5' ) ); //wp_head
			//add_action( 'wp_head_filo', array( $wp_customize, 'customize_preview_loading_style' ) ); //wp_head
			add_action( 'wp_footer_filo', array( $wp_customize, 'customize_preview_settings' ), 20 ); //wp_footer
		}
			
	}
	
	/**
	 * get_actual_template_key
	 */
	public static function get_actual_template_key() {
			 
	 	if ( isset($_GET['filo_new_template_name']) and $_GET['filo_new_template_name'] != '' ) {
			$actual_template_key = wc_clean( $_GET['filo_new_template_name'] ); //if a user open another template, then use instead of saved option (the option will be overwritten, but the first displayed panels also should be the newly choosen template panels) //+wc_clean
		} else {
			$actual_template_key = wc_clean( get_option( 'filo_document_template' ) );
		}
		
		wsl_log(null, 'class-filo-customize-manager.php get_actual_template_key $actual_template_key: ' .  wsl_vartotext( $actual_template_key ));
		
		return $actual_template_key;
	}

	/**
	 * get_actual_opt_name_of_template
	 */	
	public static function get_actual_opt_name_of_template() {
		
		//get actual template key
		$actual_template_key = self::get_actual_template_key();
		
		$opt_fix_prefix = 'filo_doc_opt_';
		
		// Get the name of that wp option that holds the name of the saved opiotns
		//$wp_option_name_of_actual_opt_name_of_template = 'filo_doc_act_opt_name_' . $actual_template_key;
		$wp_option_name_of_actual_opt_name_of_template = 'filo_doc_act_opt_name';
		
		wsl_log(null, 'class-filo-customize-manager.php get_actual_opt_name_of_template $wp_option_name_of_actual_opt_name_of_template: ' .  wsl_vartotext( $wp_option_name_of_actual_opt_name_of_template ));

		// If the user choose another opion name, then it is set in filo_new_opt_name URL parameter,
		// and we have to use the matching wp_option (id_base), and we also update that wp option that holds the actual opt name (here, when we open the customizer window!)  
		if ( isset($_GET['filo_new_opt_name']) and $_GET['filo_new_opt_name'] != '' ) {
			$actual_option_name = rawurlencode( wc_clean( $_GET['filo_new_opt_name'] ) );  //$_GET automatically decode the encoded parameter, we need the encoded version. rawurlencode should be applied to get the same result as JavaScript encodeURI //+wc_clean 
		} else {
			// Get the actual options name
			$actual_option_name = get_option( $wp_option_name_of_actual_opt_name_of_template ); //wc_clean cannot be applied here, because the url encoded characters is eliminated
		}
		
		$full_actual_option_name = $opt_fix_prefix . $actual_template_key . '--' . $actual_option_name;
		
		wsl_log(null, 'class-filo-customize-manager.php get_actual_opt_name_of_template $full_actual_option_name: ' .  wsl_vartotext( $full_actual_option_name ));
		
		return $full_actual_option_name;
	}
	
	/**
	 * get_actual_options_of_template
	 */ 
	public static function get_actual_options_of_template() {
		
		$full_actual_option_name = self::get_actual_opt_name_of_template();
		$options = get_option( $full_actual_option_name );
		
		wsl_log(null, 'class-filo-customize-manager.php get_actual_options_of_template $options: ' .  wsl_vartotext( $options ));
		
		return $options;
		
	}
	 
	 
	/**
	 * disable_so_row_gutter
	 * 
	 * called from siteorigin_panels_css_row_gutter filter
	 * Give back empty value for disable gutter
	 */
	public static function disable_so_row_gutter( $gutter, $grid, $gi, $panels_data ) {

		$actual_options_of_template = self::get_actual_options_of_template();
		
		// if disable_so_gutters option is set as disabled, or it is not set (the default also disabled), then set the value of $gutter to null
		if ( ( isset($actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters']) and $actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters'] == 'disable' )
				or ! isset($actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters']) 
			) {
			$gutter = null;
		} 
		
		wsl_log(null, 'class-filo-customize-manager.php disable_so_gutters $actual_options_of_template: ' .  wsl_vartotext( $actual_options_of_template ));
		wsl_log(null, 'class-filo-customize-manager.php disable_so_gutters $gutter: ' .  wsl_vartotext( $gutter ));
				
		return $gutter;
	}

	/**
	 * disable_so_row_gutter
	 * 
	 * called from siteorigin_panels_css_row_margin_bottom filter
	 * Give back 'initial' value for eliminate unnecessary margin
	 */
	public static function disable_so_row_margin_bottom( $panels_margin_bottom, $grid, $gi, $panels_data, $post_id ) {
		
		$actual_options_of_template = self::get_actual_options_of_template();
		
		// if disable_so_gutters option is set as disabled, or it is not set (the default also disabled), then set the value of $panels_margin_bottom to initial
		if ( ( isset($actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters']) and $actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters'] == 'disable' )
				or ! isset($actual_options_of_template['']['filo_doc_template_custom_settings']['disable_so_gutters']) 
			) {
			$panels_margin_bottom = 'initial';
		} 
		
		wsl_log(null, 'class-filo-customize-manager.php disable_so_row_margin_bottom $panels_margin_bottom: ' .  wsl_vartotext( $panels_margin_bottom ));		
		
		return $panels_margin_bottom;
	}
	

	
	/*public static function dequeue_css() {
		global $wp_styles;
		wsl_log(null, 'class-filo-customize-manager.php dequeue_css $wp_styles 0: ' .  wsl_vartotext( $wp_styles ));
		
		if ( isset($wp_styles->registered) and is_array($wp_styles->registered) )
		foreach ($wp_styles->registered as $id => $data) {
			wp_dequeue_script( $id . '-css' );
			
			wsl_log(null, 'class-filo-customize-manager.php dequeue_css dequeue $id . -css: ' .  wsl_vartotext( $id . '-css' ));
		}			
		
		$wp_styles->registered = array();
		
		wsl_log(null, 'class-filo-customize-manager.php dequeue_css $wp_styles 9: ' .  wsl_vartotext( $wp_styles ));
		
	}*/


	/**
	 * enqueue_scripts
	 * 
	 */
	/*public static function enqueue_scripts() {
		
	}*/	
	
	/**
	 * dequeue_css
	 * 
	 * Dequeue scripts that name is entered by the user
	 * The styles encueued by themes can be decueued not to make wrong the document design.
	 */
	public static function dequeue_css() {

		wsl_log(null, 'class-filo-customize-manager.php dequeue_css $_GET: ' .  wsl_vartotext( $_GET ));
		wsl_log(null, 'class-filo-customize-manager.php dequeue_css $_SERVER: ' .  wsl_vartotext( $_SERVER ));

		//if ( strpos($_SERVER['HTTP_REFERER'], '/wp-admin/customize.php') ) {

		if ( ( isset($_GET['filo_usage']) and ( $_GET['filo_usage'] == 'doc' or $_GET['filo_usage'] == 'doc_view' ) ) or (isset($_GET['filo_customizer']) and $_GET['filo_customizer'] == 'true') ) {
			
			$filo_doc_global_option = get_option('filo_doc_global');
			
			wsl_log(null, 'class-filo-customize-manager.php dequeue_css $filo_doc_global_option: ' .  wsl_vartotext( $filo_doc_global_option ));
			wsl_log(null, 'class-filo-customize-manager.php dequeue_css $filo_doc_global_option[dequeue_entered_styles]: ' .  wsl_vartotext( $filo_doc_global_option['dequeue_entered_styles'] ));
			
			//Explode PHP string by enters
			//http://stackoverflow.com/questions/3997336/explode-php-string-by-new-line		
			$theme_dequeue_style_array = preg_split('/\r\n|\r|\n/', $filo_doc_global_option['dequeue_entered_styles']);
			
			wsl_log(null, 'class-filo-customize-manager.php dequeue_css $theme_dequeue_style_array: ' .  wsl_vartotext( $theme_dequeue_style_array ));
			
			if ( isset($theme_dequeue_style_array) or is_array($theme_dequeue_style_array) )
			foreach ( $theme_dequeue_style_array as $style_name ) {
				if ( ! empty($style_name) ) {
					wp_dequeue_style( $style_name );
				}
			}
			
			self::dequeue_auto_detected_css($filo_doc_global_option);
			
			// RaPe TEST:
			//wp_dequeue_style( 'corpo-css' );
			//wp_dequeue_style( 'corpo-fonts' );
			//wp_dequeue_style( 'font_awsome-css' );
			//wp_dequeue_style( 'color_scheme' );
		
		}
			
	}

	public static function dequeue_auto_detected_css($filo_doc_global_option) {
		global $wp_styles;
		//wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css $wp_styles 0: ' .  wsl_vartotext( $wp_styles )); //big
		wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css $filo_doc_global_option: ' .  wsl_vartotext( $filo_doc_global_option ));
		
		// list of plugins that styles have to bee kept and must not decueue automatically:
		$plugin_ccs_to_keep = array (
			'siteorigin-panels',
			'filogy',
			'filogy-framework',
			'filogy-invoice',
			'filogy-invoice-builder',
		);
		
		//dequeue themes css
		if ( isset($wp_styles->registered) and is_array($wp_styles->registered) )
		foreach ($wp_styles->registered as $id => $data) {
			
			//wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css plugins_url(): ' .  wsl_vartotext( plugins_url() ));
			//wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css $data->src: ' .  wsl_vartotext( $data->src ));
			
			// $data->src e.g.: http://yoursite.com/wp-content/themes/yourtheme/css/yourthemestyle.css
			//if ( strpos($data->src, '/wp-content/themes/' ) ) { //dequeue THEME styles
			$plugins_url = plugins_url();		//http://www.example.com/wp-content/plugins
			$themes_url = get_theme_root_uri();	//http://www.example.com/wp-content/themes
			
			wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css strpos($data->src, $plugins_url ): ' .  wsl_vartotext( strpos($data->src, $plugins_url ) ));
			
			
			if ( strpos($data->src, $themes_url) !== false ) { //dequeue THEME styles
							
				if ( isset($filo_doc_global_option['dequeue_theme_styles']) and $filo_doc_global_option['dequeue_theme_styles'] ) { //if dequeue_theme_styles is set and it is true
					wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css DEQUEUED $id 1: ' .  wsl_vartotext( $id ));
					wp_dequeue_style( $id );
				}
				
			} else if ( strpos($data->src, $plugins_url) !== false ) { //dequeue PLUGIN styles
				
				wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css PLUGIN: ' .  wsl_vartotext( '' ));
				
				if ( isset($filo_doc_global_option['dequeue_plugins_styles']) and $filo_doc_global_option['dequeue_plugins_styles'] ) { //if dequeue_plugins_styles is set and it is true
				
					if ( isset($plugin_ccs_to_keep) and is_array($plugin_ccs_to_keep) )
					foreach ($plugin_ccs_to_keep as $plugin_name) {
						
						$to_be_dequeue = true;
						if ( strpos($data->src, $plugin_name) === 0) { //if id begins with one of the name to keep, then it should not bee dequeues		
							$to_be_dequeue = false;
							break;
						}				
					
					}

					if ( $to_be_dequeue ) {
						
						wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css DEQUEUED $id 2: ' .  wsl_vartotext( $id ));
						wp_dequeue_style( $id );
						
					}
					
				}
				
			}	
		
		}
				
		//wsl_log(null, 'class-filo-customize-manager.php dequeue_auto_detected_css $wp_styles 9: ' .  wsl_vartotext( $wp_styles )); //big
		
	}

	static function delete_skin() {

		wsl_log(null, 'class-filo-customize-manager.php delete_skin 0: ' .  wsl_vartotext( '' ));
		wsl_log(null, 'class-filo-customize-manager.php delete_skin $_GET: ' .  wsl_vartotext( $_GET ));

		// if filo_delete_skin parameter is set, then we should delete that skin that name is contained by the parameter
		if ( isset($_GET['filo_delete_skin']) and ! empty($_GET['filo_delete_skin']) ) {
			
			$filo_delete_skin = rawurlencode( wc_clean( $_GET['filo_delete_skin'] ) ); //we have to encode it again, because $_GET decode it automatically //+wc_clean

			if ( isset($_GET['filo_delete_skin_nonce']) and wp_verify_nonce($_GET['filo_delete_skin_nonce'], 'filo_delete_skin_' . $filo_delete_skin) ) {
							

				//wsl_log(null, 'class-filo-customize-manager.php delete_skin $_GET[filo_delete_skin]: ' .  wsl_vartotext( $_GET['filo_delete_skin'] ));
				delete_option( $filo_delete_skin );
				
			} else {
				echo __('Access denied - You are not authorized to delete this skin.', 'filo_text');
				wsl_log(null, 'class-filo-customize-manager.php delete_skin: ' .  wsl_vartotext( 'Access denied - You are not authorized to delete this skin.' ));
			} 
			 
		}
		
	}
			
}

/**
 * sanitize_style_names_setting
 * 
 * we can accept only letters, numbers, _ and - signd and enters
 */
function filo_doc_sanitize_style_names_setting($value) {
	
	wsl_log(null, 'class-filo-customize-manager.php sanitize_style_names_setting $value: ' .  wsl_vartotext( $value ));
	
	$value = preg_replace('/[^a-zA-Z0-9\-\.\r\n_]/','', $value);
	
	return $value;
}


wsl_log(null, 'class-filo-customize-manager.php root 0: ' .  wsl_vartotext(''));
add_action( 'customize_register', 'FILO_Customize_Manager::filo_doc_customize_register', 10000 ); //we have to do it at the end, to leave others to register all of their customizations, then remove it if we are in filo doc mode

add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_footer_scripts', 5); //this should be executed before the following scripts (priority: 5) 

add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_scripts_of_controls_color_palette' );
add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_scripts_of_controls_filo_accordions' );
add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_scripts_of_controls_saving_options' );
add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_scripts_of_controls_measurment_unit' );

add_action( 'customize_controls_print_footer_scripts', 'FILO_Customize_Manager::print_scripts_of_sectios' );



add_action( 'customize_preview_init', 'FILO_Customize_Manager::filo_doc_customize_preview_js' );

add_action( 'customize_save_after', 'FILO_Customize_Manager::save_after', 10, 1 );

//make up for action of missing wp_footer call in default-filters.php
add_action( 'wp_footer_filo', 'wp_print_footer_scripts', 20 ); //it is must //QQQ21!
add_action( 'wp_footer_filo', 'wp_admin_bar_render', 1000 ); //it is optional //QQQ21
add_action( 'wp_loaded',  'FILO_Customize_Manager::wp_loaded' ); //it is must //QQQ21

add_filter('siteorigin_panels_css_row_gutter', 'FILO_Customize_Manager::disable_so_row_gutter', 10,  4);
add_filter('siteorigin_panels_css_row_margin_bottom', 'FILO_Customize_Manager::disable_so_row_margin_bottom', 10,  5);


add_action('wp_enqueue_scripts', 'FILO_Customize_Manager::dequeue_css', 99999); // Add Theme Stylesheet
add_action('admin_enqueue_scripts', 'FILO_Customize_Manager::dequeue_css', 99999); // Add Theme Stylesheet
//add_action('wp_enqueue_scripts', 'FILO_Customize_Manager::enqueue_scripts'); // Add Theme Stylesheet
add_action('wp_print_scripts', 'FILO_Customize_Manager::dequeue_css'); // Add Theme Stylesheet
add_filter( 'filo_customize_section_description', 'FILO_Customize_Manager::protected_warning_in_section_description', 10, 1 ); // the first parameter is enough