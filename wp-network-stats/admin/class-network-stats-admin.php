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

	private	$site_stats_admin;
	private	$plugin_stats_admin;
	private	$user_stats_admin;
	private	$theme_stats_admin;


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

		$this->site_stats_admin = new Site_Stats_Admin ();
		$this->plugin_stats_admin = new Plugin_Stats_Admin ();
		$this->user_stats_admin = new User_Stats_Admin ();
		$this->theme_stats_admin = new Theme_Stats_Admin ();

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
		 * Registering menu item in Network Dashboard, allowing it to be displayed for all super admin users
		 * with "manage_network" capability and displaying it with position "3.756789"
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
			<?php
				//$report_dirname = NS_REPORT_DIRNAME;
				//echo NS_REPORT_DIRNAME;
		
				//echo $plugin->get_plugin_name() . '<br/>';
				//$upload_dir = wp_upload_dir();
				//var_dump($upload_dir);
				//$report_dirname = $upload_dir['basedir'].'/'. NS_UPLOADS;
				//echo '<br/>' . $report_dirname;
			?>
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
		
		//self::generate_reports($print = true);
		$options = get_site_option('ns_options');
		$to_email = $options['email'];

		wp_schedule_single_event(time(), 'cron_generate_reports', array($number_sites = 300, $in_minutes = 1, $to_email = $to_email));

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
	
	public function generate_reports($number_sites, $in_minutes, $to_email) {

		$blog_list = Network_Stats_Helper::get_network_blog_list( );		
		$steps = count($blog_list) / $number_sites;
		for($step = 0; $step < $steps; $step++) {
			$limit = $number_sites;
			$offset = $step * $number_sites;
			$args = array(
        'limit'      => $limit,
        'offset'     => $offset,
    	);
			wp_schedule_single_event(time()+($in_minutes*60*$step), 'cron_refresh_site_stats', array($args));
			Network_Stats_Helper::write_log('Step ' . $step . ' args: ' . print_r($args, $return = true) . "\n");
		}

		wp_schedule_single_event(time()+(2*60*($steps+1)), 'cron_refresh_plugin_stats');
		wp_schedule_single_event(time()+(2*60*($steps+2)), 'cron_refresh_user_stats');
		wp_schedule_single_event(time()+(2*60*($steps+3)), 'cron_refresh_theme_stats');
		wp_schedule_single_event(time()+(2*60*($steps+4)), 'cron_send_notification_email', array($to_email));

	}

	public function refresh_site_stats($args = array()) {
		$this->site_stats_admin->refresh_site_stats ($args);
	}

	public function refresh_plugin_stats() {
		$this->plugin_stats_admin->refresh_plugin_stats ();
	}

	public function refresh_user_stats() {
		$this->user_stats_admin->refresh_user_stats ();
	}

	public function refresh_theme_stats() {
		$this->theme_stats_admin->refresh_theme_stats ();
	}

	public function send_notification_email($to_email) {
		
		/*
		$multiple_recipients = array(
    		'recipient1@example.com',
    		'recipient2@foo.example.com'
		);
		*/

		$network_home_url = network_home_url();

		$subj = 'WP Network Stats - Report';
		$body = 'A report of network stats was requested for ' . $network_home_url . "\n\n";
		

		/////////////////////
		// Plugin Summary  //
		/////////////////////

		$all_plugins = Network_Stats_Helper::get_list_all_plugins();

		$list_network_active_plugins = Network_Stats_Helper::get_list_network_active_plugins();
		$count_network_active_plugins = count($list_network_active_plugins);

		///////////////////
		// Theme Summary //
		///////////////////
		/**
		 *
		 * @todo Get theme with error and highlight if one found. Use get_list_all_themes(array( 'errors' => true ))
		 */
		$all_themes = Network_Stats_Helper::get_list_all_themes();

		$network_active_themes = WP_Theme::get_allowed_on_network ();
		$count_network_active_themes = count($network_active_themes);

		//////////////////
		// User Summary //
		//////////////////

		$count_users = Network_Stats_Helper::get_count_all_users();
		//https://codex.wordpress.org/Function_Reference/get_super_admins
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


		/////////////////////
		// Updates Summary //
		/////////////////////

		$wp_update_info = array();
		$wp_update_available = Network_Stats_Helper::do_update_check($wp_update_info);

		if($wp_update_available) {
			$body .= 'Notice: The following updates are available - ' ."\n";

			if(isset($wp_update_info['core']['new_version'])) {
				$body .= '- WP-Core: WordPress is out of date. Please update from version ' . $wp_update_info['core']['old_version'] . ' to ' . $wp_update_info['core']['new_version'] . '.' . "\n";	
			}
			if(count($wp_update_info['plugins']) > 0) {
				$body .= '- Plugins: ' . count($wp_update_info['plugins']) . "\n";
			}
			if(count($wp_update_info['themes']) > 0) {
				$body .= '- Themes: ' . count($wp_update_info['themes']) . "\n";
			}
		}

		///////////////
		// Body Text //
		///////////////

		$body .= "\n";
		$body .= 'Summary: ' . "\n";
		$body .= 'There are ' . count($all_themes) . ' themes. ' . $count_network_active_themes . ' are network enabled.' . "\n";
		$body .= 'There are ' . count($all_plugins) . ' plugins. ' . $count_network_active_plugins . ' are network enabled.' . "\n";
		$body .= 'There are ' . $count_users . ' users. ' . count($all_super_admins) . ' are Super admins.' . "\n";

		
		$body .= "\n\n" . 'For more information on how to use this data, please visit https://github.com/sanghviharshit/WP-Network-Stats' . "\n\n";


		/////////////////
		// Attachments //
		/////////////////
		///
		$upload_dir = wp_upload_dir();
		$report_dirname = $upload_dir['basedir'].'/'. NS_UPLOADS;
		$report_site_stats = $report_dirname . '/' . 'site-stats.csv';
		$report_plugin_stats_per_site = $report_dirname . '/' . 'plugin-stats-per-site.csv';
		$report_user_stats_per_site = $report_dirname . '/' . 'user-stats-per-site.csv';
		$report_plugin_stats = $report_dirname . '/' . 'plugin-stats.csv';
		$report_user_stats = $report_dirname . '/' . 'user-stats.csv';
		$report_theme_stats = $report_dirname . '/' . 'theme-stats.csv';

		$attachments = array();
		array_push($attachments, $report_site_stats);
		array_push($attachments, $report_plugin_stats_per_site);
		array_push($attachments, $report_user_stats_per_site);
		array_push($attachments, $report_plugin_stats);
		array_push($attachments, $report_user_stats);
		array_push($attachments, $report_theme_stats);


		//////////////////
		// Mail Headers //
		//////////////////

		//$headers[] = 'From: WP Network Stats <me@wp.org>';
		
		/*
		$options = get_site_option('ns_options');
		$to_email = $options['email'];
		*/
	
		//$headers[] = 'Cc: Harshit Sanghvi <sanghvi.harshit@gmail.com>';
		$headers[] = 'Cc: hs2619@nyu.edu';

		if(wp_mail( $to_email, $subj, $body, $headers, $attachments )) {
			//echo 'Email sent successfully';
		} else {
			//echo 'Email could not be sent';
		}
	}


	/**
	 * Print Site Stats.
	 * @since 0.1.0
	 */
	public function print_site_stats() {
		//$site_stats = new Site_Stats_Admin ();
		//$site_stats->refresh_site_stats ();
		//$site_stats->print_site_stats ();
	}
	
	/**
	 * Print Plugin Stats.
	 * @since 0.2.0
	 */
	public function print_plugin_stats() {
		//$plugin_stats = new Plugin_Stats_Admin ();
		//$plugin_stats->refresh_plugin_stats ();
		//$plugin_stats->print_plugin_stats ();
	}
}
				