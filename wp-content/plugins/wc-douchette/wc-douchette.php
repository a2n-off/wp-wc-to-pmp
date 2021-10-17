<?php
/**
* Plugin Name: wc-douchette
* Description: Allow admin to send a woocommerce order to a pickeos mission prepa api
* Version: 1.0.0
* Author: A2n
* License: GPL v3 or later
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * prevent data leaks
 */
defined( 'ABSPATH' ) || exit;

/**
 * Activation hooks for WordPress
 */
function wcdouchette_extension_activate() {
	// activation logic
}
register_activation_hook( __FILE__, 'wcdouchette_extension_activate' );

/**
 * Deactivation hooks for WordPress
 */
function wcdouchette_extension_deactivate() {
	// deactivation logic
}
register_deactivation_hook( __FILE__, 'wcdouchette_extension_deactivate' );

/**
 * Delaying initialization of the extension until after WooComerce is loaded
 */
function wcdouchette_initialize() {
	// check for the existence of the WooCommerce class
  // todo add a WordPress admin notice for this error
	if (!class_exists('WooCommerce')) return;
	$GLOBALS['wcdouchette'] = Wcdouchette::instance();
}
add_action('plugins_loaded', 'wcdouchette_initialize', 10);

/**
 * Wcdouchette singleton
 */
if (!class_exists('Wcdouchette')) {
	class Wcdouchette extends WP_REST_Controller {

		/**
		 * the single instance of the class
		 */
		protected static $_instance = null;

    /**
     * launch init()
     * @throws Exception
     */
		protected function __construct() {
      $this->includes();
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
		 * cloning is forbidden
		 */
		public function __clone() {
			return;
		}

		/**
		 * unserializing instances of this class is forbidden
		 */
		public function __wakeup() {
			return;
		}

    /**
     * include require file
     * @throws Exception
     */
    public function includes() {
      if (!class_exists('WcdouchetteOptions', false)) {
        include_once plugin_dir_path( __FILE__ ) . 'wc-douchette-options.php';
        $wcdouchetteoptions = WcdouchetteOptions::instance();
      }
      if (!class_exists('WcdouchetteButton', false)) {
        include_once plugin_dir_path( __FILE__ ) . 'wc-douchette-button.php';
        $wcdouchettebutton = WcdouchetteButton::instance();
      }
      if (!class_exists('WcdouchetteApi', false)) {
        include_once plugin_dir_path( __FILE__ ) . 'wc-douchette-api.php';
        $wcdouchetteapi = WcdouchetteApi::instance();
      }
			if (!class_exists('WcdouchetteService', false)) {
        include_once plugin_dir_path( __FILE__ ) . 'wc-douchette-service.php';
        $wcdouchetteservice = WcdouchetteService::instance();
      }
    }

		/**
		 * init class
		 * add option submenu into the woocommerce primary menu
		 */
		private function init() {
      //
		}
	}
}
