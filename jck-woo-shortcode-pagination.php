<?php
/*
Plugin Name: Shortcode Pagination for WooCommerce
Plugin URI: http://www.jckemp.com
Description: Adds pagination to WooCommerce Product Category Shortcode
Version: 1.0.5
Author: James Kemp
Author URI: http://www.jckemp.com
Text Domain: jck-wsp
*/

defined('JCK_WSP_PATH') or define('JCK_WSP_PATH', plugin_dir_path( __FILE__ ));
defined('JCK_WSP_URL') or define('JCK_WSP_URL', plugin_dir_url( __FILE__ ));

class JCK_WSP {

    public $name = 'WooCommerce Shortcode Pagination';
    public $shortname = 'Shortcode Pagination';
    public $slug = 'jck-wsp';
    public $version = "1.0.5";
    public $plugin_path;
    public $plugin_url;

    /**
     * Construct the plugin
     */

    public function __construct() {

        $this->set_constants();

        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'init', array( $this, 'initiate_hook' ) );

        require_once( $this->plugin_path.'inc/admin/vendor/class-dashboard.php' );

    }

    /**
     * Set up plugin constants
     */

    public function set_constants() {

        $this->plugin_path = JCK_WSP_PATH;
        $this->plugin_url = JCK_WSP_URL;

    }

    /**
     * Run on Plugins Loaded hook
     */

    public function plugins_loaded() {

        $this->textdomain();

    }

    /**
     * Run after the current user is set (http://codex.wordpress.org/Plugin_API/Action_Reference)
     */

     public function initiate_hook() {

        if( !is_admin() ) {

            add_action( 'pre_get_posts', array( $this, 'add_paged_param' ) );
            add_action( 'loop_end', array( $this, 'loop_end' ), 100 );
            add_action( 'woocommerce_after_template_part', array( $this, 'add_pagination' ), 10, 4 );

        }

    }

    /**
     * Load plugin textdomain
     */

    public function textdomain() {

        load_plugin_textdomain( 'jck-wsp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    /**
     * Frontend: Add the paged param to the shortcode product query
     *
     * @param obj $query WP_Query
     */

     public function add_paged_param( $query ) {

        // Get paged from main query only
        // ! frontpage missing the post_type

        $is_product_query = $this->is_product_query( $query );

        if ( is_archive() || is_post_type_archive() || !$is_product_query )
            return;

        $paged = $this->get_paged_var();

        if ( $query->is_main_query() && $is_product_query ) {
            $GLOBALS['woocommerce_loop']['paged'] = $paged;
        }

        $query->is_paged = true;
        $query->query['paged'] = $paged;
        $query->query_vars['paged'] = $paged;
        $query->query['no_found_rows'] = false;
        $query->query_vars['no_found_rows'] = false;

    }

    /**
     * Get paged var
     */
    public function get_paged_var() {

        $query_var = is_front_page() ? 'page' : 'paged';
        return get_query_var( $query_var ) ? get_query_var( $query_var ) : 1;

    }

    /**
     * Frontend: Add query params to enable the pagination
     *
     * @param obj $query WP_Query
     */

    public function loop_end( $query ) {

        if ( is_archive() || is_post_type_archive() || !$this->is_product_query( $query ) )
            return;

        $paged = $this->get_paged_var();

        $GLOBALS['woocommerce_loop']['pagination']['paged'] = $paged;
        $GLOBALS['woocommerce_loop']['pagination']['found_posts'] = $query->found_posts;
        $GLOBALS['woocommerce_loop']['pagination']['max_num_pages'] = $query->max_num_pages;
        $GLOBALS['woocommerce_loop']['pagination']['post_count'] = $query->post_count;
        $GLOBALS['woocommerce_loop']['pagination']['current_post'] = $query->current_post;

    }

    /**
     * Helper: Is query for products?
     *
     * @param $query
     * @return bool
     */
    public function is_product_query( $query ) {

        if( !isset( $query->query['post_type'] ) )
            return false;

        $post_type = $query->query['post_type'];

        if( is_array( $post_type ) && in_array('product', $post_type) )
            return true;

        if( $post_type == "product" )
            return true;

        return false;

    }

    /**
     * Frontend: Add pagination to the shortcode after loop end
     *
     * @param str $template_name
     */

    public function add_pagination( $template_name, $template_path, $located, $args ) {

        global $post;

        if ( $template_name !== 'loop/loop-end.php' )
            return;

        $queried_post = get_queried_object();

        if( !$queried_post || !isset( $queried_post->post_content ) )
            return;

        if( !$this->has_woo_shortcodes( $queried_post->post_content ) )
            return;

        global $wp_query;

        if ( ! isset( $GLOBALS['woocommerce_loop']['pagination'] ) )
            return;

        $wp_query->query_vars['paged'] = $GLOBALS['woocommerce_loop']['pagination']['paged'];
        $wp_query->query['paged'] = $GLOBALS['woocommerce_loop']['pagination']['paged'];
        $wp_query->max_num_pages = $GLOBALS['woocommerce_loop']['pagination']['max_num_pages'];
        $wp_query->found_posts = $GLOBALS['woocommerce_loop']['pagination']['found_posts'];
        $wp_query->post_count = $GLOBALS['woocommerce_loop']['pagination']['post_count'];
        $wp_query->current_post = $GLOBALS['woocommerce_loop']['pagination']['current_post'];

        // Custom pagination function or default woocommerce_pagination()
        woocommerce_pagination();

    }

    /**
     * Helper: Has shortcodes
     *
     * @param str $content
     * @return bool
     */
    public function has_woo_shortcodes( $content ) {

        $shortcodes = apply_filters('jck-wsp-shortcodes', array(
            'product_category',
            'recent_products',
            'featured_products',
            'sale_products',
            'best_selling_products',
            'top_rated_products'
        ));

        if( empty( $shortcodes ) )
            return false;

        foreach( $shortcodes as $shortcode ) {

            if( has_shortcode( $content, $shortcode ) )
                return true;

        }

        return false;

    }


}

$jck_wsp = new JCK_WSP();