/**
 * Load the "Iconic Plugin" Gulp module.
 */
require( 'iconic-plugin' )( {
	plugin_id: false,
	plugin_filename: 'jck-woo-shortcode-pagination',
	is_envato_constant: false,
	is_svn: true,
	deps: {
		// 'src' : 'dest'
		'vendor/jamesckemp/WordPress-Settings-Framework/wp-settings-framework.php': 'inc/vendor/wp-settings-framework',
		'vendor/jamesckemp/WordPress-Settings-Framework/assets/css/main.css': 'inc/vendor/wp-settings-framework/assets/css',
		'vendor/jamesckemp/WordPress-Settings-Framework/assets/js/main.js': 'inc/vendor/wp-settings-framework/assets/js',
		'vendor/jamesckemp/WordPress-Settings-Framework/assets/vendor/jquery-timepicker/jquery.ui.timepicker.js': 'inc/vendor/wp-settings-framework/assets/vendor/jquery-timepicker',
		'vendor/jamesckemp/WordPress-Settings-Framework/assets/vendor/jquery-timepicker/jquery.ui.timepicker.css': 'inc/vendor/wp-settings-framework/assets/vendor/jquery-timepicker',
		'vendor/freemius/wordpress-sdk/**/*': 'inc/freemius'
	}
} );