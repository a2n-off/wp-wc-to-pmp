<?php
class WcdouchetteApi {

  /**
   * the single instance of the class
   */
  protected static $_instance = null;

  protected static $_user = null;
  protected static $_isDev = null;

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
   * check if the param is a numeric value
   */
  public function wc_douchette_param_is_numeric ($param, $request, $key) {
    return is_numeric($param);
  }

  /**
   * check if the user have the authorization
   */
   function wc_douchette_authguard() {
     return user_can($this->_user, 'administrator');
   }

  /**
   * core function for the call to pmp
   */
  public function wc_douchette_send(WP_REST_Request $request) {
    $order_id = $request->get_param('id');
    $consumer_key=esc_attr(get_option('wcdouchette_option_pmp_ck'));
    $consumer_secret=esc_attr(get_option('wcdouchette_option_pmp_cs'));
    $pmp_url=esc_attr(get_option('wcdouchette_option_pmp_url'));
    // send to pmp
    $current_order = wp_remote_get(get_site_url().'/wp-json/wc/v3/orders/'.$order_id, array(
      'sslverify' => !$this->_isDev, // ONLY IN DEV
      'headers' => array(
        'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
      )
    ));
    $order = wp_remote_retrieve_body($current_order);
    wcdouchettepmp::wc_douchette_send_product($order);
    // update if success
    $update = wp_remote_request(get_site_url().'/wp-json/wc/v3/orders/'.$order_id, array(
      'method' => 'PUT',
      'headers' => array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
      ),
      'body' => json_encode(array("status" => "on-hold")),
      'sslverify' => !$this->_isDev, // ONLY IN DEV
    ));
    $update_info = wp_remote_retrieve_body($update);
    if (json_decode($update_info)->data->status === 401) return new WP_REST_Response('check your woocommerce key', 401);
    return new WP_REST_Response( ['updated', $order_id], 200);
  }

  /**
   * custom wp rest api endpoint
   */
   public function wcdouchette_register_routes() {
     register_rest_route( 'wcdouchette/v1', '/send/(?P<id>\d+)', array(
       'methods'  => 'POST',
       'callback' => array( $this, 'wc_douchette_send'),
       'args' => array('id' => array('validate_callback' => array($this, 'wc_douchette_param_is_numeric'))),
       'permission_callback' => array($this, 'wc_douchette_authguard'),
     ));
   }

  /**
   * load custom api endpoint
   */
  private function init() {
    // check env
    if (get_site_url() === 'https://wp-pp.test') $this->_isDev = true;
    // populate user info
    global $current_user;
    get_currentuserinfo();
    $this->_user = $current_user;
    // add api routes
    add_action('rest_api_init', array($this, 'wcdouchette_register_routes'));
    add_filter('rest_authentication_errors', array($this, 'wcdouchette_register_routes'));
  }
}
