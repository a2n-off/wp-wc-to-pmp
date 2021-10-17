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
   * create a custom button on action field in woocommerce orders table page
   * @param $order
   */
  public function wcdouchette_btn_in_wcactions_column($order) {
      $tooltip = __('Send to Mission Prepa', 'textdomain');
      $label = __('Send to PMP', 'textdomain');
      $id = json_decode($order)->id;
			echo '<a href="'.get_site_url().'/wp-json/wcdouchette/v1/send/'.$id.'" type="button" class="pmp-sender button tips" style="text-indent: 0 !important; width: fit-content; padding: 0 10px !important" data-tip="'.$tooltip.'">'.$label.'</a>';
  }

	/**
	 * add action btn into the order table
	 */
	private function init() {
    add_action('woocommerce_admin_order_actions_end', array($this, 'wcdouchette_btn_in_wcactions_column'));
	}
}
