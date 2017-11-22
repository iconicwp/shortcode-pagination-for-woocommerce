<?php
/*
 * Plugin Name: Shortcode Pagination for WooCommerce
 * Plugin URI: http://www.jckemp.com
 * Description: Adds pagination to WooCommerce Product Category Shortcode
 * Version: 1.0.8
 * Author: James Kemp
 * Author URI: http://www.jckemp.com
 * Text Domain: jck-wsp
 * WC requires at least: 2.6.0
 * WC tested up to: 3.2.3
 */

defined( 'JCK_WSP_PATH' ) or define( 'JCK_WSP_PATH', plugin_dir_path( __FILE__ ) );
defined( 'JCK_WSP_URL' ) or define( 'JCK_WSP_URL', plugin_dir_url( __FILE__ ) );

class JCK_WSP {
	public $name = 'WooCommerce Shortcode Pagination';

	public $shortname = 'Shortcode Pagination';

	public $slug = 'jck-wsp';

	public $version = "1.0.8";

	public $plugin_path;

	public $plugin_url;

	/**
	 * Class prefix
	 *
	 * @since  4.5.0
	 * @access protected
	 * @var string $class_prefix
	 */
	protected $class_prefix = "Iconic_WSP_";

	/**
	 * Construct the plugin.
	 */
	public function __construct() {
		$this->set_constants();

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * Set up plugin constants.
	 */
	public function set_constants() {
		$this->plugin_path = JCK_WSP_PATH;
		$this->plugin_url  = JCK_WSP_URL;
	}

	/**
	 * Run on Plugins Loaded hook.
	 */
	public function plugins_loaded() {
		$this->textdomain();
		$this->load_classes();
	}

	/**
	 * Load classes
	 */
	private function load_classes() {
		spl_autoload_register( array( $this, 'autoload' ) );

		Iconic_WSP_Pagination::run();

		$pagination_version = self::get_pagination_version();
		call_user_func( array( $pagination_version, 'run' ) );
	}

	/**
	 * Get pagination version to run.
	 *
	 * @return string
	 */
	protected static function get_pagination_version() {
		switch ( true ) {
			case version_compare( WC_VERSION, '3.2.4', '>=' ):
				return "Iconic_WSP_Pagination_Current";
			case version_compare( WC_VERSION, '3.2.4', '<' ):
				return "Iconic_WSP_Pagination_Below_3_2_4";
		}
	}

	/**
	 * Autoloader
	 *
	 * Classes should reside within /inc and follow the format of
	 * Iconic_The_Name ~ class-the-name.php or {{class-prefix}}The_Name ~ class-the-name.php
	 */
	private function autoload( $class_name ) {
		/**
		 * If the class being requested does not start with our prefix,
		 * we know it's not one in our project
		 */
		if ( 0 !== strpos( $class_name, 'Iconic_' ) && 0 !== strpos( $class_name, $this->class_prefix ) ) {
			return;
		}

		$file_name = strtolower( str_replace(
			array( $this->class_prefix, 'Iconic_', '_' ),      // Prefix | Plugin Prefix | Underscores
			array( '', '', '-' ),                              // Remove | Remove | Replace with hyphens
			$class_name
		) );

		// Compile our path from the current location
		$file = dirname( __FILE__ ) . '/inc/class-' . $file_name . '.php';

		// If a file is found
		if ( file_exists( $file ) ) {
			// Then load it up!
			require( $file );
		}
	}

	/**
	 * Load plugin textdomain.
	 */
	public function textdomain() {
		load_plugin_textdomain( 'jck-wsp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

$jck_wsp = new JCK_WSP();