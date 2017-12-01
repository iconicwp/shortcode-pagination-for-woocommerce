<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSP_Pagination.
 *
 * The core pagination methods for all WooCommerce versions.
 *
 * @class Iconic_WSP_Pagination
 */
class Iconic_WSP_Pagination {
	/**
	 * Run.
	 */
	public static function run() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'pre_get_posts', array( __CLASS__, 'add_paged_param' ) );
		add_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'shortcode_products_query_args' ), 10, 3 );
		add_filter( 'woocommerce_composite_component_options_query_args', array( __CLASS__, 'composite_component_options_query_args' ), 10, 3 );
		add_filter( 'woocommerce_get_breadcrumb', array( __CLASS__, 'remove_breadcrumb' ), 10, 2 );
	}

	/**
	 * Frontend: Add the paged param to the shortcode product query.
	 *
	 * @param WP_Query $query
	 */
	public static function add_paged_param( $query ) {
		$is_product_query = self::is_product_query( $query );

		if ( is_archive() || is_post_type_archive() || ! $is_product_query ) {
			return;
		}

		$paged = self::get_paged_var();

		if ( $query->is_main_query() && $is_product_query ) {
			$GLOBALS['woocommerce_loop']['paged'] = $paged;
		}

		$query->is_paged                    = true;
		$query->query['paged']              = $paged;
		$query->query_vars['paged']         = $paged;
		$query->query['no_found_rows']      = false;
		$query->query_vars['no_found_rows'] = false;
	}

	/**
	 * Shortcode products query args.
	 *
	 * @param array  $query_args
	 * @param array  $atts
	 * @param string $loop_type
	 *
	 * @return array
	 */
	public static function shortcode_products_query_args( $query_args, $atts, $loop_type ) {
		$query_args['paged'] = self::get_paged_var();

		return $query_args;
	}

	/**
	 * Add arg to composite product component.
	 *
	 * @param array $args
	 * @param array $query_args
	 * @param array $component_data
	 *
	 * @return array
	 */
	public static function composite_component_options_query_args( $args, $query_args, $component_data ) {
		$args['composite_component'] = true;

		return $args;
	}

	/**
	 * Get paged var
	 */
	public static function get_paged_var() {
		$query_var = is_front_page() ? 'page' : 'paged';

		return get_query_var( $query_var ) ? get_query_var( $query_var ) : 1;
	}

	/**
	 * Helper: Is query for products?
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	public static function is_product_query( $query ) {
		if ( ! isset( $query->query['post_type'] ) || ! empty( $query->query['p'] ) || isset( $query->query['composite_component'] ) ) {
			return false;
		}

		$post_type = $query->query['post_type'];

		if ( is_array( $post_type ) && in_array( 'product', $post_type ) ) {
			return true;
		}

		if ( $post_type == "product" ) {
			return true;
		}

		return false;
	}

	/**
	 * Get shortcodes.
	 *
	 * @return array
	 */
	public static function get_shortcodes() {
		return apply_filters( 'jck-wsp-shortcodes', array(
			'products',
			'product_category',
			'recent_products',
			'featured_products',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
		) );
	}

	/**
	 * Remove paged breadcrumb from product page.
	 *
	 * @param array         $crumbs
	 * @param WC_Breadcrumb $breadcrumb_object
	 *
	 * @return array
	 */
	public static function remove_breadcrumb( $crumbs, $breadcrumb_object ) {
		if ( is_product() ) {
			array_pop( $crumbs );
		}

		return $crumbs;
	}
}