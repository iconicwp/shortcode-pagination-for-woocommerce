<?php
/*
Plugin Name: Shortcode Pagination for WooCommerce
Plugin URI: http://www.jckemp.com
Description: Adds pagination to WooCommerce Product Category Shortcode
Version: 1.0.0
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
    public $version = "1.0.0";
    public $plugin_path;
    public $plugin_url;

    /**
     * Construct the plugin
     */

    public function __construct() {

        $this->set_constants();

        add_action( 'plugins_loaded',   array( $this, 'plugins_loaded' ) );
        add_action( 'init',             array( $this, 'initiate_hook' ) );

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

            add_action( 'pre_get_posts',                    array( $this, 'add_paged_param' ) );
            add_action( 'loop_end',                         array( $this, 'loop_end' ) );
            add_action( 'woocommerce_after_template_part',  array( $this, 'add_pagination' ) );

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

        global $woocommerce_loop;

        // Get paged from main query only
        // ! frontpage missing the post_type

        $post_type = ( isset( $query->query['post_type'] ) ) ? $query->query['post_type'] : false;

        if ( $query->is_main_query() && ( $post_type == 'product' || !$post_type ) ) {

            if ( isset($query->query['paged']) ){
                $woocommerce_loop['paged'] = $query->query['paged'];
            }
        }

        if ( ! $query->is_post_type_archive || $post_type !== 'product' ){
            return;
        }

        $query->is_paged = true;
        $query->query['paged'] = $woocommerce_loop['paged'];
        $query->query_vars['paged'] = $woocommerce_loop['paged'];

    }

    /**
     * Frontend: Add query params to enable the pagination
     *
     * @param obj $query WP_Query
     */

    public function loop_end( $query ) {

        if ( ! $query->is_post_type_archive || $query->query['post_type'] !== 'product' ){
            return;
        }

        // Cache data for pagination
        global $woocommerce_loop;

        $paged = ( isset( $woocommerce_loop['paged'] ) ) ? $woocommerce_loop['paged'] : 1;

        $woocommerce_loop['pagination']['paged'] = $paged;
        $woocommerce_loop['pagination']['found_posts'] = $query->found_posts;
        $woocommerce_loop['pagination']['max_num_pages'] = $query->max_num_pages;
        $woocommerce_loop['pagination']['post_count'] = $query->post_count;
        $woocommerce_loop['pagination']['current_post'] = $query->current_post;

    }

    /**
     * Frontend: Add pagination to the shortcode after loop end
     *
     * @param str $template_name
     */

    public function add_pagination( $template_name ) {

        if ( ! ( $template_name === 'loop/loop-end.php' && ( is_page() || is_single() ) ) ){
            return;
        }

        global $wp_query, $woocommerce_loop;

        if ( ! isset( $woocommerce_loop['pagination'] ) ){
            return;
        }

        $wp_query->query_vars['paged'] = $woocommerce_loop['pagination']['paged'];
        $wp_query->query['paged'] = $woocommerce_loop['pagination']['paged'];
        $wp_query->max_num_pages = $woocommerce_loop['pagination']['max_num_pages'];
        $wp_query->found_posts = $woocommerce_loop['pagination']['found_posts'];
        $wp_query->post_count = $woocommerce_loop['pagination']['post_count'];
        $wp_query->current_post = $woocommerce_loop['pagination']['current_post'];

        // Custom pagination function or default woocommerce_pagination()
        woocommerce_pagination();

    }

}

$jck_wsp = new JCK_WSP();