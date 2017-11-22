<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSP_Pagination_Below_3_2_4.
 *
 * Methods for WooCommerce versions before (and not including) 3.2.4
 *
 * @class Iconic_WSP_Pagination_Below_3_2_4
 */
class Iconic_WSP_Pagination_Below_3_2_4 {
	/**
	 * Run.
	 */
	public static function run() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'loop_end', array( __CLASS__, 'loop_end' ), 100 );
		add_action( 'woocommerce_after_template_part', array( __CLASS__, 'add_pagination' ), 10, 4 );
	}

	/**
	 * Frontend: Add query params to enable the pagination
	 *
	 * @param WP_Query $query
	 */
	public function loop_end( $query ) {

		if ( is_archive() || is_post_type_archive() || ! Iconic_WSP_Pagination::is_product_query( $query ) ) {
			return;
		}

		$paged = Iconic_WSP_Pagination::get_paged_var();

		$GLOBALS['woocommerce_loop']['pagination']['paged']         = $paged;
		$GLOBALS['woocommerce_loop']['pagination']['found_posts']   = $query->found_posts;
		$GLOBALS['woocommerce_loop']['pagination']['max_num_pages'] = $query->max_num_pages;
		$GLOBALS['woocommerce_loop']['pagination']['post_count']    = $query->post_count;
		$GLOBALS['woocommerce_loop']['pagination']['current_post']  = $query->current_post;
	}

	/**
	 * Frontend: Add pagination to the shortcode after loop end
	 *
	 * @param string $template_name
	 */
	public function add_pagination( $template_name, $template_path, $located, $args ) {
		if ( $template_name !== 'loop/loop-end.php' ) {
			return;
		}

		$queried_post = get_queried_object();

		if ( ! $queried_post || ! isset( $queried_post->post_content ) ) {
			return;
		}

		if ( ! self::has_woo_shortcodes( $queried_post->post_content ) ) {
			return;
		}

		global $wp_query;

		if ( ! isset( $GLOBALS['woocommerce_loop']['pagination'] ) ) {
			return;
		}

		$wp_query->query_vars['paged'] = $GLOBALS['woocommerce_loop']['pagination']['paged'];
		$wp_query->query['paged']      = $GLOBALS['woocommerce_loop']['pagination']['paged'];
		$wp_query->max_num_pages       = $GLOBALS['woocommerce_loop']['pagination']['max_num_pages'];
		$wp_query->found_posts         = $GLOBALS['woocommerce_loop']['pagination']['found_posts'];
		$wp_query->post_count          = $GLOBALS['woocommerce_loop']['pagination']['post_count'];
		$wp_query->current_post        = $GLOBALS['woocommerce_loop']['pagination']['current_post'];

		// Custom pagination function or default woocommerce_pagination()
		woocommerce_pagination();
	}

	/**
	 * Helper: Has shortcodes
	 *
	 * @param string $content
	 *
	 * @return bool
	 */
	public function has_woo_shortcodes( $content ) {
		$shortcodes = Iconic_WSP_Pagination::get_shortcodes();

		if ( empty( $shortcodes ) ) {
			return false;
		}

		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $content, $shortcode ) ) {
				return true;
			}
		}

		return false;
	}
}