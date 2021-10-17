<?php
class WcdouchettePMP {

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
   * loop on woocommerce line and send to pmp
   */
  public function wc_douchette_send_product($order) {
    var_dump($order);
  }

  /**
   * add option submenu into the woocommerce primary menu
   */
  private function init() {
    //
  }
}
