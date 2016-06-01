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
 * Plugin URI:        https://github.com/sanghviharshit/
 * Description:       View/Export useful network information (e.g. #sites/user, #sites/theme, #sites/plugin, privacy settings, etc) of all the sites in a WordPress multisite network. 
 * Version:           0.0.1
 * Author:            Harshit Sanghvi
 * Author URI:        http://about.me/harshit
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       network-stats
 * Domain Path:       /languages
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

// These defines are used later for various reasons.
define( 'NS_VERSION', '0.2.0' );
define( 'NS_PLUGIN', plugin_basename( __FILE__ ) );
define( 'NS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NS_URL', plugin_dir_url( __FILE__ ) );

define( 'NS_OPTIONS', 'ns_options');
define( 'NS_OPTIONS_SETTINGS', 'ns_options_settings');
define( 'NS_OPTIONS_GENERATE', 'ns_options_generate');

define( 'NS_OPTIONS_GROUP', 'ns_options_group');
define( 'NS_PAGE_SETTINGS', 'ns_settings' );
//define( 'NS_SETTINGS_PAGE', NS_SETTINGS_PAGE . '_batch');
define( 'NS_PAGE_GENERATE', 'ns_settings_generate');

define( 'NS_SECTION_GENERAL', 'ns_section_general');
define( 'NS_SECTION_GENERATE', 'ns_section_generate');
define( 'NS_SECTION_NOTIFICATION', 'ns_section_notification');
define( 'NS_SECTION_BATCH', 'ns_section_batch');
define( 'NS_SECTION_STATS_SELECTION', 'ns_section_stats_selection');

define( 'NS_UPLOADS', 'ns_uploads' );
define( 'NS_REPORT_DIRNAME', wp_upload_dir()['basedir'] . '/' . NS_UPLOADS);


//define( 'SSW_PLUGIN_DIR', 'nsd_ssw/ssw.php' );
//define( 'MSP_PLUGIN_DIR', 'sitewide-privacy-options/sitewide-privacy-options.php' );
define( 'NS_SITE_TABLE', 'ns_site_stats' );
define( 'NS_PLUGIN_TABLE', 'ns_plugin_stats' );
define( 'NS_USER_TABLE', 'ns_user_stats' );
define( 'NS_THEME_TABLE', 'ns_theme_stats' );
//define( 'SSW_TABLE_NAME', 'ssw_main_nsd' );


run_network_stats();
