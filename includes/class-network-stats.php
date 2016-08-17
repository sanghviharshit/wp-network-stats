<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.0.1
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 * @author     Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Network_Stats {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      Network_Stats_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->plugin_name = 'wp-network-stats';
		$this->version = NS_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Network_Stats_Loader. Orchestrates the hooks of the plugin.
	 * - Network_Stats_i18n. Defines internationalization functionality.
	 * - Network_Stats_Admin. Defines all hooks for the dashboard.
	 * - Network_Stats_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-network-stats-loader.php';

		/**
		 * The class contains all plugin helper functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-helper.php';
		
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-network-stats-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-network-stats-admin.php';

		/**
		 * The class responsible for defining all functions related to site stats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-site-stats-admin.php';
		
		/**
		 * The class responsible for defining all functions related to plugin stats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugin-stats-admin.php';

		/**
		 * The class responsible for defining all functions related to user stats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-user-stats-admin.php';

		/**
		 * The class responsible for defining all functions related to theme stats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-theme-stats-admin.php';

		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-network-stats-public.php';

		$this->loader = new Network_Stats_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Network_Stats_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Network_Stats_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Network_Stats_Admin( $this->get_plugin_name(), $this->get_version() );


		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 10, 1 );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 10, 1 );

		/* Add action to whitelist options that the form is able to save. */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

		/** Add settings to Network Settings
		 * http://zao.is/2013/07/adding-settings-to-network-settings-for-wordpress-multisite
		 */
		//$this->loader->add_filter( 'wpmu_options', $plugin_admin, 'show_network_settings' );
		//$this->loader->add_action( 'update_wpmu_options', $plugin_admin, 'save_network_settings' );

		/* Add action to process options form data */
		$this->loader->add_action( 'network_admin_edit_' . NS_OPTIONS_SETTINGS, $plugin_admin, 'ns_options_settings' );
		$this->loader->add_action( 'network_admin_edit_' . NS_OPTIONS_GENERATE, $plugin_admin, 'ns_options_generate' );
		
		/* Add action to display the menu items in Network Admin's Dashboard */
		$this->loader->add_action( 'network_admin_menu', $plugin_admin, 'register_menu' );

		/* Add action to handle file requests */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'handle_file_requests' );

		/* Add action to run when cron job fires */
		$this->loader->add_action( 'cron_generate_reports', $plugin_admin, 'generate_reports', 10, 3 );
		
		/* Add action to run refresh_site_stats with limit and offset when cron job fires */
		$this->loader->add_action( 'cron_refresh_site_stats', $plugin_admin, 'refresh_site_stats');

		/* Add action to run refresh_plugin_stats with limit and offset when cron job fires */
		$this->loader->add_action( 'cron_refresh_plugin_stats', $plugin_admin, 'refresh_plugin_stats');

		/* Add action to run refresh_user_stats with limit and offset when cron job fires */
		$this->loader->add_action( 'cron_refresh_user_stats', $plugin_admin, 'refresh_user_stats');

		/* Add action to run refresh_theme_stats with limit and offset when cron job fires */
		$this->loader->add_action( 'cron_refresh_theme_stats', $plugin_admin, 'refresh_theme_stats');

		/* Add action to run refresh_site_stats with limit and offset when cron job fires */
		$this->loader->add_action( 'cron_send_notification_email', $plugin_admin, 'send_notification_email', 10, 1);
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Network_Stats_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    Network_Stats_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
