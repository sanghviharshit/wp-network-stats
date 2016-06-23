<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.0.1
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.0.1
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 * @author     Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Network_Stats_Deactivator {

	/**
	 * Function that runs on plugin deactivation.
	 *
	 * Delete/Drops database tables.
	 *
	 * @since    0.0.1
	 */
	public static function deactivate() {
		global $wpdb;
		
		/**
		 * @todo handle upgrades to tables
		*/
		
		//Delete/Drop tables
//		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}".NS_SITE_TABLE);
//		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}".NS_SITE_TABLE);
		
//		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}".NS_PLUGIN_TABLE);
//		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}".NS_PLUGIN_TABLE);
		
		//Delete options
		delete_option( 'network_stats_db_version');
	}

}
