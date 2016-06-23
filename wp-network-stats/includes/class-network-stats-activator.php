<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.0.1
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 * @author     Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Network_Stats_Activator {

	/**
	 * Function that runs on plugin activation.
	 *
	 * Creates tables and inserts initial data.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {

		if ( ! file_exists( NS_REPORT_DIRNAME ) ) {
			wp_mkdir_p( NS_REPORT_DIRNAME );
		}
		self::maybe_upgrade();
		update_site_option( 'wp_network_stats_version', NS_VERSION );

		/**
		 * @todo Create database tables on plugin install http://codex.wordpress.org/Creating_Tables_with_Plugins
		 */
		//self::network_stats_install();
		//self::network_stats_install_data();
	}
	
	/**
	 * Creates tables on plugin activation.
	 *
	 * Creates tables.
	 *
	 * @since    0.1.0
	 */	
	private static function network_stats_install() {
		global $wpdb;
		$installed_ver = get_option( "network_stats_db_version" );
		/**
		 * @todo handle upgrades to tables
		 */
		
		$charset_collate = $wpdb->get_charset_collate();

		$site_table_name = $wpdb->prefix . NS_SITE_TABLE;
		$plugin_table_name = $wpdb->prefix . NS_PLUGIN_TABLE;
		
		
		$site_stats_sql = "CREATE TABLE $site_table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NOT NULL,
			blog_name varchar(255) NOT NULL,
			blog_url varchar(100) NOT NULL,
			privacy varchar(100) NOT NULL,
			current_theme varchar(100) NOT NULL,
			admin_email varchar(100) NOT NULL,
			total_users bigint(20) NOT NULL,
			active_plugins bigint(20) NOT NULL,
			site_type varchar(100) NOT NULL,
			UNIQUE KEY (id)
		) $charset_collate;";
	
		$plugin_stats_sql = "CREATE TABLE $plugin_table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			plugin varchar(255) NOT NULL,
			total_sites varchar(255) NOT NULL,
			UNIQUE KEY (id)
		) $charset_collate;";
				
		// This includes the dbDelta function from WordPress.
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		// Create/update the plugin tables.
		dbDelta( $site_stats_sql );
		dbDelta( $plugin_stats_sql );
		
		add_option( 'network_stats_db_version', NS_VERSION );
	}
	
	/**
	 * Inserts initial data on plugin activation.
	 *
	 * Inserts initial data on plugin activation.
	 *
	 * @since    0.1.0
	 */	
	private static function network_stats_install_data() {
		global $wpdb;
	
		/**
		 * @todo insert init data
		 */
	}

	/**
	 * Check if the db needs and upgrade.
	 *
     */
	private static function maybe_upgrade()
	{
		$current_network_version = get_site_option( 'wp_network_stats_version' );

		if ( $current_network_version == NS_VERSION )
			return;

		if ( $current_network_version === false ) {
			return;
		}

		do_action( 'wp_network_stats_upgrade', $current_network_version, NS_VERSION );

		// Do the actual upgrade here
		//

		update_site_option( 'wp_network_stats_version', NS_VERSION );

	}

}
