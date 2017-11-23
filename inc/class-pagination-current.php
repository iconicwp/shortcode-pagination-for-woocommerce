<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Iconic_WSP_Pagination_Current.
 *
 * The most up to date pagination methods.
 *
 * @class Iconic_WSP_Pagination_Current
 */
class Iconic_WSP_Pagination_Current {
	/**
	 * Run.
	 */
	public static function run() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'shortcode_products_query_args' ), 100, 3 );
		self::add_pagination_to_shortcodes();
		self::add_pagination_att_to_shortcodes();
	}

	/**
	 * Generate and return the transient name for this shortcode based on the query args.
	 *
	 * @param array  $atts
	 * @param string $loop_type
	 *
	 * @return string
	 */
	protected static function get_transient_name( $atts, $loop_type ) {
		$transient_name = 'iconic_shortcode_query_data_' . substr( md5( wp_json_encode( $atts ) . $loop_type ), 28 );

		return $transient_name;
	}

	/**
	 * Use the shortcode query args to fetch our product
	 * collection data and cache the results.
	 *
	 * @param array  $query_args
	 * @param array  $atts
	 * @param string $loop_type
	 *
	 * @return array
	 */
	public static function shortcode_products_query_args( $query_args, $atts, $loop_type ) {
		$transient_name = self::get_transient_name( $atts, $loop_type );
		$query_data     = get_transient( $transient_name );

		if ( $query_data === false ) {
			$products   = new WP_Query( $query_args );
			$query_data = array(
				'found_posts'   => $products->found_posts,
				'max_num_pages' => $products->max_num_pages,
				'post_count'    => $products->post_count,
				'current_post'  => $products->current_post,
			);
			set_transient( $transient_name, $query_data, DAY_IN_SECONDS * 30 );
		}

		return $query_args;
	}

	/**
	 * Add pagination to all shortcode types.
	 */
	public static function add_pagination_to_shortcodes() {
		$shortcodes = Iconic_WSP_Pagination::get_shortcodes();

		foreach ( $shortcodes as $shortcode ) {
			add_action( 'woocommerce_shortcode_after_' . $shortcode . '_loop', array( __CLASS__, 'add_pagination' ), 10, 4 );
		}
	}

	/**
	 * Add pagination attribute to all shortcode types.
	 */
	public static function add_pagination_att_to_shortcodes() {
		$shortcodes = Iconic_WSP_Pagination::get_shortcodes();

		foreach ( $shortcodes as $shortcode ) {
			add_filter( 'shortcode_atts_' . $shortcode, array( __CLASS__, 'add_pagination_att' ), 10, 4 );
		}
	}

	/**
	 * Add pagination.
	 *
	 * @param $atts
	 */
	public static function add_pagination( $atts ) {
		if ( empty( $atts['pagination'] ) ) {
			return;
		}

		$loop_type      = str_replace( array( 'woocommerce_shortcode_after_', '_loop' ), '', current_action() );
		$transient_name = self::get_transient_name( $atts, $loop_type );
		$query_data     = get_transient( $transient_name );

		if ( $query_data['max_num_pages'] <= 1 ) {
			return;
		}
		?>
		<nav class="woocommerce-pagination">
			<?php
			echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
				'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, Iconic_WSP_Pagination::get_paged_var() ),
				'total'     => $query_data['max_num_pages'],
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3,
			) ) );
			?>
		</nav>
		<?php
	}

	/**
	 * Add shortcode 'pagination' parameter.
	 *
	 * @param array  $out
	 * @param array  $pairs
	 * @param array  $atts
	 * @param string $shortcode
	 *
	 * @return mixed
	 */
	public static function add_pagination_att( $out, $pairs, $atts, $shortcode ) {
		$out['pagination'] = empty( $atts['pagination'] ) ? false : wc_string_to_bool( $atts['pagination'] );

		return $out;
	}
}