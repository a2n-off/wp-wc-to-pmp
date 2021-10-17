<?php
class WcdouchetteApi {

  /**
   * the single instance of the class
   */
  protected static $_instance = null;

  protected static $_current_user = null;

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
     return user_can($this->user, 'administrator');
   }

  /**
   * todo function
   */
  public function wc_douchette_send(WP_REST_Request $request) {
    $order_id = $request->get_param('id');
    $consumer_key=esc_attr(get_option('wcdouchette_option_pmp_ck'));
    $consumer_secret=esc_attr(get_option('wcdouchette_option_pmp_cs'));
    $args = array(
      'sslverify' => false,
      'headers' => array(
        'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
      )
    );
    $response = wp_remote_get(get_site_url().'/wp-json/wc/v3/orders/'.$order_id, $args);
    $body = wp_remote_retrieve_body($response);
    // return new WP_REST_Response( ['pong', $order_id, json_decode($body), $this->user], 200);
    wp_redirect('https://wp-pp.test/wp-admin/edit.php?post_type=shop_order');
  }

  /**
   * custom wp rest api endpoint
   */
   public function wcdouchette_register_routes() {
     register_rest_route( 'wcdouchette/v1', '/send/(?P<id>\d+)', array(
       'methods'  => 'GET',
       'callback' => array( $this, 'wc_douchette_send'),
       'args' => array('id' => array('validate_callback' => array($this, 'wc_douchette_param_is_numeric'))),
       'permission_callback' => array($this, 'wc_douchette_authguard'),
     ));
   }

  /**
   * load custom api endpoint
   */
  private function init() {
    // populate user info
    global $current_user;
    get_currentuserinfo();
    $this->user = $current_user;
    // add api routes
    add_action('rest_api_init', array($this, 'wcdouchette_register_routes'));
    add_filter('rest_authentication_errors', array($this, 'wcdouchette_register_routes'));
  }
}
