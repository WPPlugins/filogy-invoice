<?php
/**
 * Customizer Control for submenu groups (nav menu items)
 * 
 * This class handle filo accordions that is similar to wp nav menu item control.
 * It is based on WP_Customize_Nav_Menu_Item_Control class.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

class FILO_Customize_Nav_Menu_Item_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu_item';
	//public $type = 'filo_accordion_item';

	/**
	 * The nav menu item setting.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var WP_Customize_Nav_Menu_Item_Setting
	 */
	public $setting;
	
	
	public $partition_id;
	public $panel_id;
	public $panel_category;

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @see WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      The control ID.
	 * @param array                $args    Optional. Overrides class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		
		parent::__construct( $manager, $id, $args );
		
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since 3.4.0
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		$class = 'customize-control customize-control-' . $this->type;
		?>
		
		<li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<div class="menu-item filo-accordion-item <?php echo 'filo-cust-ctrl-' . $this->partition_id; ?> <?php echo 'filo-cust-ctrl-' . $this->panel_category . '-panel-category'; ?>  menu-item-edit-inactive">
				<?php $this->render_content(); ?>
				<div class="filo-accordion-settings menu-item-settings" id="filo-accordion-item-settings-" style="display: none;">
					
					<?php if ( isset($this->description) and ! empty($this->description) ) {?>
						<span class="description customize-control-description filo-accordion-description">
							
							<?php echo $this->description; ?>
						</span>
					<?php } ?>
					
					<ul class="filogy-accordion-partition-content">
						
						<!-- the following control li elements will be moved here (it is the previous accordion) -->
						
					</ul>
				</div>
			</div>
		</li>

						
		<?php
		
	}

	/**
	 * Don't render the control's content - it's rendered with a JS template.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function render_content() {
		?>
		
		
			<div class="menu-item-bar filo-accordion-item-bar">
				<div class="menu-item-handle filo-accordion-item-handle">
					<span class="item-type" aria-hidden="true"></span>
					<span class="item-title" aria-hidden="true">
						<span class="spinner"></span>
						<span class="menu-item-title"><?php echo esc_html( $this->label ); ?></span>
					</span>
					<span class="item-controls">
						<button type="button" class="button-link item-edit" aria-expanded="false">
						<span class="toggle-indicator" aria-hidden="true"></span></button>
					</span>
				</div>
			</div>
		<?php
		
	}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function content_template() {
	}

	/*	
	public function json() {
		
		$exported                 = parent::json();
		$exported['menu_item_id'] = $this->setting->post_id;

		return $exported;
	}
	*/

}
