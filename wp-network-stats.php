<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://about.me/harshit
 * @since             0.0.1
 * @package           Network_Stats
 *
 * @wordpress-plugin
 * Plugin Name:       WP Network Stats
 * Plugin URI:        https://github.com/sanghviharshit/wp-network-stats
 * Description:       View/Export useful network statistics related to sites, users per site, plugins per site, themes and plugins for all the sites in a WordPress multisite network.
 * Version:           1.0.4
 * Author:            Harshit Sanghvi
 * Author URI:        http://about.me/harshit
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       network-stats
 * Domain Path:       /languages
 * Network:			  True
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-network-stats-activator.php
 */
function activate_network_stats() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-stats-activator.php';
	Network_Stats_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-network-stats-deactivator.php
 */
function deactivate_network_stats() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-stats-deactivator.php';
	Network_Stats_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_network_stats' );
register_deactivation_hook( __FILE__, 'deactivate_network_stats' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-network-stats.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_network_stats() {

	$plugin = new Network_Stats();
	$plugin->run();

}
/**
 * Define the defines.
 * These defines are used later for various reasons.
 *
 */
function set_globals() {
	if ( ! defined( 'NS_VERSION' ) )
		define( 'NS_VERSION', '1.0.4' );
	if ( ! defined( 'NS_PLUGIN' ) )
		define( 'NS_PLUGIN', plugin_basename( __FILE__ ) );
	if ( ! defined( 'NS_DIR' ) )
		define( 'NS_DIR', plugin_dir_path( __FILE__ ) );
	if ( ! defined( 'NS_URL' ) )
		define( 'NS_URL', plugin_dir_url( __FILE__ ) );

	if ( ! defined( 'NS_OPTIONS' ) )
		define( 'NS_OPTIONS', 'ns_options');
	if ( ! defined( 'NS_OPTIONS_SETTINGS' ) )
		define( 'NS_OPTIONS_SETTINGS', 'ns_options_settings');
	if ( ! defined( 'NS_OPTIONS_GENERATE' ) )
		define( 'NS_OPTIONS_GENERATE', 'ns_options_generate');
	if ( ! defined( 'NS_CURRENT_STATUS' ) )
		define( 'NS_CURRENT_STATUS', 'ns_current_status');

	if ( ! defined( 'NS_OPTIONS_GROUP' ) )
		define( 'NS_OPTIONS_GROUP', 'ns_options_group');
	if ( ! defined( 'NS_PAGE_SETTINGS' ) )
		define( 'NS_PAGE_SETTINGS', 'ns_settings' );
	//define( 'NS_SETTINGS_PAGE', NS_SETTINGS_PAGE . '_batch');
	if ( ! defined( 'NS_PAGE_GENERATE' ) )
		define( 'NS_PAGE_GENERATE', 'ns_settings_generate');

	if ( ! defined( 'NS_SECTION_GENERAL' ) )
		define( 'NS_SECTION_GENERAL', 'ns_section_general');
	if ( ! defined( 'NS_SECTION_GENERATE' ) )
		define( 'NS_SECTION_GENERATE', 'ns_section_generate');
	if ( ! defined( 'NS_SECTION_NOTIFICATION' ) )
		define( 'NS_SECTION_NOTIFICATION', 'ns_section_notification');
	if ( ! defined( 'NS_SECTION_BATCH' ) )
		define( 'NS_SECTION_BATCH', 'ns_section_batch');
	if ( ! defined( 'NS_SECTION_STATS_SELECTION' ) )
		define( 'NS_SECTION_STATS_SELECTION', 'ns_section_stats_selection');

	if ( ! defined( 'NS_UPLOADS' ) )
		define( 'NS_UPLOADS', 'ns_uploads' );
	if ( ! defined( 'NS_REPORT_DIRNAME' ) )
		define( 'NS_REPORT_DIRNAME', wp_upload_dir()['basedir'] . '/' . NS_UPLOADS);

	if ( ! defined( 'NS_STATS_FILE_PERMISSION' ) )
		define( 'NS_STATS_FILE_PERMISSION', 0660);

	//define( 'SSW_PLUGIN_DIR', 'nsd_ssw/ssw.php' );
	//define( 'MSP_PLUGIN_DIR', 'sitewide-privacy-options/sitewide-privacy-options.php' );

	if ( ! defined( 'NS_SITE_TABLE' ) )
		define( 'NS_SITE_TABLE', 'ns_site_stats' );
	if ( ! defined( 'NS_PLUGIN_TABLE' ) )
		define( 'NS_PLUGIN_TABLE', 'ns_plugin_stats' );
	if ( ! defined( 'NS_USER_TABLE' ) )
		define( 'NS_USER_TABLE', 'ns_user_stats' );
	if ( ! defined( 'NS_THEME_TABLE' ) )
		define( 'NS_THEME_TABLE', 'ns_theme_stats' );

	//define( 'SSW_TABLE_NAME', 'ssw_main_nsd' );

}

set_globals();
run_network_stats();
