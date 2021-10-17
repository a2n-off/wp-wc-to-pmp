<?php
class WcdouchetteService {

  /**
   * the single instance of the class
   */
  protected static $_instance = null;

  protected static $_isDev = null;

  protected static $_consumer_key = null;
  protected static $_consumer_secret = null;

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
   * get the current order
   */
   function wc_douchette_get_order($order_id) {
     $current_order = wp_remote_get(get_site_url().'/wp-json/wc/v3/orders/'.$order_id, array(
       'sslverify' => !self::$_isDev, // ONLY IN DEV
       'headers' => array(
         'Authorization' => 'Basic ' . base64_encode( self::$_consumer_key . ':' . self::$_consumer_secret )
       )
     ));
     return wp_remote_retrieve_body($current_order);
   }

   /**
    * update the status of an order to on-hold
    */
   function wc_douchette_update_order($order_id) {
     $update = wp_remote_request(get_site_url().'/wp-json/wc/v3/orders/'.$order_id, array(
       'method' => 'PUT',
       'headers' => array(
         'Content-Type' => 'application/json',
         'Authorization' => 'Basic ' . base64_encode( self::$_consumer_key . ':' . self::$_consumer_secret )
       ),
       'body' => json_encode(array("status" => "on-hold")),
       'sslverify' => !self::$_isDev, // ONLY IN DEV
     ));
     return wp_remote_retrieve_body($update);
   }

  /**
   * loop on woocommerce line and send to pmp
   */
  public function wc_douchette_send_product($order) {
    $pmp_url=esc_attr(get_option('wcdouchette_option_pmp_url'));
    return $order;
  }

  /**
   * add option submenu into the woocommerce primary menu
   */
  private function init() {
    // check env
    if (get_site_url() === 'https://wp-pp.test') $this->_isDev = true;
    $this->_consumer_key=esc_attr(get_option('wcdouchette_option_pmp_ck'));
    $this->_consumer_secret=esc_attr(get_option('wcdouchette_option_pmp_cs'));
  }
}
