=== Shortcode Pagination for WooCommerce ===
Contributors: iconicwp
Donate link: https://www.paypal.me/jamesckemp
Requires at least: 4.3.1
Tested up to: 4.9.8
Stable tag: trunk

Add pagination to WooCommerce product shortcodes

== Description ==

**Important: This functionality is now [included in WooCommerce core](https://docs.woocommerce.com/document/woocommerce-shortcodes/#display-product-attributes). Simply add `paginate="true"` to your `[products]` shortcode.**

Check out some of the other premium [WooCommerce plugins](https://iconicwp.com/) from Iconic:

* [WooThumbs - Additional Variation Images and Customisable Image Gallery](https://iconicwp.com/products/woothumbs/)
* [WooCommerce Show Single Variations](https://iconicwp.com/products/woocommerce-show-single-variations/)
* [WooCommerce Attribute Swatches](https://iconicwp.com/products/woocommerce-attribute-swatches/)
* [WooCommerce Quickview](https://iconicwp.com/products/woocommerce-quickview/)
* [WooCommerce Delivery Slots](https://iconicwp.com/products/woocommerce-delivery-slots/)

WooCommerce shortcodes do not have pagination by default. This is a simple plugin that adds it.

The plugin enables pagination for the following shortcodes:

* products
* product_category
* recent_products
* featured_products
* best_selling_products
* top_rated_products
* sale_products

== Installation ==
To install the plugin:

1. Open wp-admin and navigate to Plugins > Add New > Upload.
2. Click Choose File, and choose the file jck-woo-shortcode-pagination.zip from your CodeCanyon download zip.
3. Once uploaded, click activate plugin.
4. The plugin is now installed and activated.

== Frequently Asked Questions ==

= How do I enable the pagination? =

If you‘re using WooCommerce 3.2.4+, you need to add `pagination="true"` to any shortcode where you want pagination enabled.
For older versions of WooCommerce it is activated on all valid shortcodes.

== Changelog ==

v1.0.10 (01/12/2017)
[fix] Remove "Page" from breadcrumbs on single product page.

v1.0.9 (23/11/2017)
[update] Compatibility with 3.2.4+
[update] Add 'pagination' param to shortcodes. When 'true', pagination will be displayed.

v1.0.8 (21/11/2017)
[update] Remove Iconic dashboard
[update] Add notice to readme

v1.0.7 (08/06/2017)
[update] Compatibility with WooCommerce Composite Products

v1.0.6 (14/04/2017)
[update] Compatibility with WooCommerce 3.0

v1.0.5 (12/09/2016)
[fix] Fixed issue when using on front_page
[update] Added Iconic dashboard

v1.0.4 (07/09/2016)
[fix] Changed paged var for static front page

v1.0.3 (16/07/2016)
[fix] Fix post type archive check

v1.0.2 (14/07/2016)
[update] Enable for sale_products shortcode

v1.0.1 (14/07/2016)
[fix] Compatibility with Woo 2.6
[update] Now works with product_category, recent_products, featured_products, best_selling_products, and top_rated_products shortcodes

v1.0.0 (23/11/2015)
Initial Release