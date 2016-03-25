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

	public $menu_slug;


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
		$this->menu_slug = $plugin_name;

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
		
		if (is_network_admin () && ! Network_Stats_Helper::is_plugin_network_activated ( NS_PLUGIN )) {
			return false;
		}
		
		add_menu_page ( 'Network Stats', 'Network Stats', $read_cap, $this->menu_slug, array (
				$this,
				'network_stats_overview' 
		), 'dashicons-analytics', '3.756789' );
		

		/*
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
		/*
		add_submenu_page ( $this->plugin_name, 'Network Stats - Plugin Stats', 'Plugin Stats', $read_cap, $this->plugin_name . '/Plugin_Stats', array (
				$this,
				'print_plugin_stats' 
		) );
		*/
		/**
		 *
		 * @todo export menu
		 *       add_submenu_page($this->plugin_name, 'Network Stats - Export Stats', 'Export Stats', $read_cap, ENS_EXPORT_CSV_SLUG,
		 *       'ens_charts_shortcode' );
		 */
		/*
		add_submenu_page ( $this->plugin_name, 'Network Stats - Options', 'Options', $read_cap, $this->plugin_name . '/Options', array (
				$this,
				'network_stats_overview' 
		) );
		*/
	}
	
	/**
	 * Displays overview of the network stats.
	 * @since 0.1.0
	 */
	public function network_stats_overview() {
		?>
		<div class="wrap">
	        <h2>WP Network Stats</h2>
				
			<?php if ( isset( $_GET['updated'] ) ): ?>
				<div class="updated"><p><?php _e( 'Reports are being generated in the background. Please wait for email notification.'); ?></p></div>
			<?php endif; ?>

	        <form method="POST" id="options" action="edit.php?action=ns_options">
	            <?php settings_fields( NS_SETTINGS ); ?>
	            <?php do_settings_sections( NS_SETTINGS_SECTION ); ?>
	            <!--<?php submit_button(); ?>-->
	            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Generate Report'); ?>" />
	        </form>

    	</div>
    <?php
		//self::generate_reports();
	}
	
	/**
	 * whitelist options.
	 * since 0.1.0
	 */
	public function register_settings() { 
		register_setting( NS_SETTINGS_SECTION, 'ns_options' );
		
		add_settings_section(NS_SETTINGS, 'WP Network Stats Settings', array( $this, 'ns_section_text'), NS_SETTINGS_SECTION);
		
		add_settings_field('email', 'Email Id for notification', array( $this, 'ns_setting_string'), NS_SETTINGS_SECTION, NS_SETTINGS);

	}


	public function ns_section_text() {
		echo '<p>Change the settings for WP Network Stats plugin.</p>';
	}

	public function ns_setting_string() {
		$options = get_site_option('ns_options');
		echo "<input id='email' name='ns_options[email]' size='40' type='text' value='{$options['email']}' />";
	}

	/**
     * Check if data in POST
     *
     * @since 1.0
     */
    public function admin_options_page_posted() {
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== $this->menu_slug )
            return;
/*
        if (isset($_POST['Submit'])) {
        	echo "Saving";
		}
*/
	}

	public function ns_options_process() {
		update_site_option( 'ns_options', $_POST['ns_options'] );

		wp_schedule_single_event(time(), 'cron_generate_reports');

		wp_redirect(
	    	add_query_arg(
		        array( 'page' => $this->menu_slug, 'updated' => 'true' ),
		        (is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ))
		    )
		);
		exit;
	}

	/**
	 * Generate Reports
	 * @since 0.1.0
	 */
	
	public function generate_reports() {
		$site_stats = new Site_Stats_Admin ();
		$site_stats->refresh_site_stats ();
		//$site_stats->print_site_stats ();

		/*
		$plugin_stats = new Plugin_Stats_Admin ();
		$plugin_stats->refresh_plugin_stats ();
		//$plugin_stats->print_plugin_stats ();
		*/

		self::send_notification_email();
	}

	private function send_notification_email() {
		
		/*
		$multiple_recipients = array(
    		'recipient1@example.com',
    		'recipient2@foo.example.com'
		);
		*/
		$network_home_url = network_home_url();

		$subj = 'WP Network Stats - Report';
		$body = 'A report of network stats was requested for ' . $network_home_url . "\n\n";
		
		$all_themes = Network_Stats_Helper::get_list_all_themes();

		/**
		 *
		 * @todo Get theme with error and highlight if one found. Use get_list_all_themes(array( 'errors' => true ))
		 */

		$all_plugins = Network_Stats_Helper::get_list_all_plugins();

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$active_on_network = Network_Stats_Helper::is_plugin_network_activated ( $plugin_file );
			
			if ($active_on_network) {
				$count_network_active_plugins++;
			}
		}

		$network_active_themes = WP_Theme::get_allowed_on_network ();
		$count_network_active_themes = count($network_active_themes);

		$body .= 'There are ' . count($all_themes) . ' themes. ' . $count_network_active_themes . ' are network enabled.' . "\n";
		$body .= 'There are ' . count($all_plugins) . ' plugins. ' . $count_network_active_plugins . ' are network enabled.' . "\n";
		
		//Get All WordPress Users
		global $wpdb;
		$count_users = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
		
		$all_super_admins = get_super_admins();
		/*
		$body .= 'List of super-admin users: ';
		foreach ($super_admins as $admin) {
  			echo '<li>' . $admin . '</li>';
		}
		*/

		/**
		 * @todo get support managers - should be able to use get_users() passing meta_key and meta_value
		 *
		 */

		$body .= 'There are ' . $count_users . ' users. ' . count($all_super_admins) . ' are Super admins.' . "\n";

		
		$body .= "\n\n" . 'For more information on how to use this data, please visit https://github.com/sanghviharshit/WP-Network-Stats' . "\n\n";


		$upload_dir = wp_upload_dir();
		$report_dirname = $upload_dir['basedir'].'/'. NS_UPLOADS;
		$report_site_stats = $report_dirname . '/' . 'site-stats.csv';
		$report_plugin_stats = $report_dirname . '/' . 'plugin-stats.csv';
		
		$attachments = array();
		array_push($attachments, $report_site_stats);
		array_push($attachments, $report_plugin_stats);

		//$headers[] = 'From: WP Network Stats <me@wp.org>';
		
		$options = get_site_option('ns_options');
		$to_email = $options['email'];

		//$headers[] = 'Cc: Harshit Sanghvi <sanghvi.harshit@gmail.com>';
		$headers[] = 'Cc: hs2619@nyu.edu';

		if(wp_mail( $to_email, $subj, $body, $headers, $attachments )) {
			echo 'Email sent successfully';
		} else {
			echo 'Email could not be sent';
		}
	}
	/**
	 * Print Site Stats.
	 * @since 0.1.0
	 */
	public function print_site_stats() {
		$site_stats = new Site_Stats_Admin ();
		//$site_stats->refresh_site_stats ();
		//$site_stats->print_site_stats ();
	}
	
	/**
	 * Print Plugin Stats.
	 * @since 0.2.0
	 */
	public function print_plugin_stats() {
		$plugin_stats = new Plugin_Stats_Admin ();
		$plugin_stats->refresh_plugin_stats ();
		//$plugin_stats->print_plugin_stats ();
	}
}
