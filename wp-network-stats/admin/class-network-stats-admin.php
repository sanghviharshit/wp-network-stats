<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.0.1
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 * @author     Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Network_Stats_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Network_Stats_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Network_Stats_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/network-stats-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Network_Stats_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Network_Stats_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/network-stats-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registers the menu items for admin dashboard
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {
		
		/**
		 * Adding Menu item "Export" in Network Dashboard, allowing it to be displayed for all super admin users
		 * with "manage_network" capability and displaying it with position "1.74"
		 */
		
		/**
		 *
		 * @todo Get the read/write capabilities from db required to view/manage the plugin as set by the user.
		 */
		// $read_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('read_capability', 'manage_options') );
		$read_cap = 'manage_network';
		// $manage_cap = wp_statistics_validate_capability( $WP_Statistics->get_option('manage_capability', 'manage_options') );
		$manage_cap = 'manage_network';
		
		if (is_network_admin () && ! Network_Stats_Helper::is_plugin_network_activated ( NS_PLUGIN )) {
			return false;
		}
		
		add_menu_page ( 'Network Stats', 'Network Stats', $read_cap, $this->plugin_name, array (
				$this,
				'network_stats_overview' 
		), NS_URL . 'assets/icon-16x16.png', '3.756789' );
		
		add_submenu_page ( $this->plugin_name, 'Overview', 'Overview', $read_cap, $this->plugin_name, array (
				$this,
				'network_stats_overview' 
		) );
		
		add_submenu_page ( $this->plugin_name, 'Network Stats - Site Stats', 'Site Stats', $read_cap, $this->plugin_name . '/Site_Stats', array (
				$this,
				'print_site_stats' 
		) );
		
		/**
		 *
		 * @todo Plugin Stats
		 */
		add_submenu_page ( $this->plugin_name, 'Network Stats - Plugin Stats', 'Plugin Stats', $read_cap, $this->plugin_name . '/Plugin_Stats', array (
				$this,
				'network_stats_overview' 
		) );
		
		/**
		 *
		 * @todo export menu
		 *       add_submenu_page($this->plugin_name, 'Network Stats - Export Stats', 'Export Stats', $read_cap, ENS_EXPORT_CSV_SLUG,
		 *       'ens_charts_shortcode' );
		 */
		
		add_submenu_page ( $this->plugin_name, 'Network Stats - Options', 'Options', $read_cap, $this->plugin_name . '/Options', array (
				$this,
				'network_stats_overview' 
		) );
	}
	
	/**
	 * Displays overview of the network stats.
	 * @since 0.1.0
	 */
	public function network_stats_overview() {
		/*
		 * @TODO Overview page
		 */
		echo '<h1>Export Network Stats</h1><br/>';
		echo '<br/><h3>TODO: This page will include options to export stats into CSV file</h3><br/>';
	}
	
	/**
	 * Print Site Stats.
	 * @since 0.1.0
	 */
	public function print_site_stats() {
		$site_stats = new Site_Stats_Admin ();
		$site_stats->refresh_site_stats ();
		$site_stats->print_site_stats ();
	}
}
