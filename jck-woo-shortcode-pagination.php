<?php
/*
 * Plugin Name: Shortcode Pagination for WooCommerce
 * Plugin URI: https://iconicwp.com/products/shortcode-pagination-woocommerce/
 * Description: Adds pagination to WooCommerce Product Shortcodes
 * Version: 1.0.10
 * Author: James Kemp
 * Author URI: https://iconicwp.com
 * Text Domain: jck-wsp
 * WC requires at least: 2.6.0
 * WC tested up to: 3.2.4
 */

class JCK_WSP {
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
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
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