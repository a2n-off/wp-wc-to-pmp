<?php
class WcdouchetteButton {

	/**
	 * the single instance of the class
	 */
	protected static $_instance = null;

  /**
   * launch init()
   * @throws Exception
   */
	protected function __construct() {
    $this->init();
	}

	/**
	 * singleton the instance
	 */
	public static function instance() {
		if (is_null( self::$_instance )) self::$_instance = new self();
		return self::$_instance;
	}

  /**
	 * add custom colum in admin orders table
	 */
	function wcdouchette_add_pmp_column($columns) {
    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        if ( 'order_total' === $column_name ) $new_columns['order_details'] = __( 'PMP actions', 'my-textdomain' );
    }
    return $new_columns;
	}

	/**
	 * add content into the custom column
	 */
	function wcdouchette_add_pmp_column_content($column) {
		global $post;
		if ( 'order_details' === $column ) {
			$tooltip = __('Send to Mission Prepa', 'textdomain');
			$label = __('Send to PMP', 'textdomain');
			$order = wc_get_order( $post->ID );
			$id = $order->id;
			echo '
			<button
			  type="button"
			  class="pmp-sender button tips"
			  style="text-indent: 0 !important; width: fit-content; padding: 0 10px !important"
			  data-tip="'.$tooltip.'"
				data-pmp="'.$id.'"
			>
			  '.$label.'
			</button>
			';
    }
	}

	/**
	 * include script file
	 * @throws Exception
	 */
	public function wcdouchette_includes_js() {
		wp_enqueue_script('wcdouchette_script',  plugin_dir_url( __FILE__ ) . 'script.js', [], null, true);
	}

	/**
	 * add action btn into the order table
	 */
	private function init() {
		add_action('admin_enqueue_scripts', array($this, 'wcdouchette_includes_js'), 100);
		add_filter('manage_edit-shop_order_columns', array($this, 'wcdouchette_add_pmp_column'), 20);
		add_action('manage_shop_order_posts_custom_column', array($this, 'wcdouchette_add_pmp_column_content'));
	}
}
