<?php
class WcdouchetteApi {

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
   * check if the param is a numeric value
   */
  public function wc_douchette_param_is_numeric ($param, $request, $key) {
    return is_numeric($param);
  }

  /**
   *
   */
   function wc_douchette_authguard() {
     // return current_user_can('administrator');
     return true;
   }

  /**
   * todo function
   */
  public function wc_douchette_send(WP_REST_Request $request) {
    $parameters = $request->get_param('id');
    $user = wp_get_current_user();
    return new WP_REST_Response( ['pong', $parameters, $user], 200);
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
    add_action('rest_api_init', array($this, 'wcdouchette_register_routes'));
    add_filter('rest_authentication_errors', array($this, 'wcdouchette_register_routes'));
  }
}
