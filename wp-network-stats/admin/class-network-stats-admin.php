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
		
		if (!is_network_admin () || ! Network_Stats_Helper::is_plugin_network_activated ( NS_PLUGIN )) {
			return false;
		}
		
		add_menu_page ( 'Network Stats', 'Network Stats', $read_cap, $this->menu_slug, array (
				$this,
				'network_stats_overview' 
		), 'dashicons-analytics', '3.756789' );
		
		$read_cap = 'manage_network_options';

		add_submenu_page ( $this->plugin_name, 'Settings', 'Settings', $read_cap, $this->menu_slug . '-settings', array (
				$this,
				'network_stats_settings' 
		) );
		
		/*
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
				$blog_list = Network_Stats_Helper::get_network_blog_list();
				$args = array(
						'public'     => 1,
					);
				$blog_public_list = Network_Stats_Helper::get_network_blog_list($args);
				echo 'There are ' . count($blog_list) . ' sites. ' . count($blog_public_list) . ' are public.' . "\n";
			?>
	        <form method="POST" id="options" action="edit.php?action=<?php echo NS_OPTIONS_GENERATE ?>">
	            <?php 
	            	// This prints out all hidden setting fields
								// settings_fields( $option_group )
								settings_fields( NS_OPTIONS_GROUP ); 
							?>
	            <?php
	            	//do_settings_sections( $page )
	            	do_settings_sections( NS_PAGE_GENERATE ); ?>
	            <?php 
	            	//submit_button('Save'); 
	            ?>
	            <?php submit_button('Generate Reports'); ?>
	        </form>
    	</div>
    <?php
		//self::generate_reports();
	}
	
		/**
	 * Displays overview of the network stats.
	 * @since 0.1.0
	 */
	public function network_stats_settings() {
		?>
		<div class="wrap">
	        <h2>WP Network Stats - Settings</h2>
				
			<?php if ( isset( $_GET['updated'] ) ): ?>
				<div class="updated"><p><?php _e( 'Settings have been saved.'); ?></p></div>
			<?php endif; ?>
	        <form method="POST" id="options" action="edit.php?action=<?php echo NS_OPTIONS_SETTINGS ?>">
	            <?php 
	            	// This prints out all hidden setting fields
								// settings_fields( $option_group )
								settings_fields( NS_OPTIONS_GROUP ); 
							?>
	            <?php
	            	//do_settings_sections( $page )
	            	do_settings_sections( NS_PAGE_SETTINGS ); ?>
	            <?php 
	            	//submit_button('Save'); 
	            ?>
	            <?php submit_button('Save Changes'); ?>
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
		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( NS_OPTIONS_GROUP, NS_OPTIONS_SETTINGS, array($this, 'ns_validate_settings') );
		register_setting( NS_OPTIONS_GROUP, NS_OPTIONS_GENERATE, array($this, 'ns_validate_generate'));

		// add_settings_section( $id, $title, $callback, $page )
		add_settings_section(
				NS_SECTION_GENERATE,											//ID
				'Settings for generating Network Stats',	//Title
				array( $this, 'ns_section_generate'),			//Callback
				NS_PAGE_GENERATE															//Page
			);

		add_settings_section(
				NS_SECTION_GENERAL,														//ID 
				'General Settings', 													//Title
				array( $this, 'ns_section_general'), 					//Callback
				NS_PAGE_SETTINGS															//Page
			);

		add_settings_section(
				NS_SECTION_BATCH,															//ID 
				'Batch Processing Settings', 									//Title
				array( $this, 'ns_section_batch'), 						//Callback
				NS_PAGE_SETTINGS															//Page
			);

		add_settings_field(
				'stats_selection', 																	//ID
				'Stats Selection',	//Title
				array( $this, 'ns_settings_stats_selection'), 		//Callback
				NS_PAGE_GENERATE,			 												//Page
				NS_SECTION_GENERATE 										//Section
			);

		add_settings_field(
				'email', 																			//ID
				'Email for notification', 										//Title
				array( $this, 'ns_settings_email'), 					//Callback
				NS_PAGE_GENERATE,					 										//Page
				NS_SECTION_GENERATE 											//Section
			);

		add_settings_field(
				'notify_admin', 															//ID
				'Notification', 															//Title
				array( $this, 'ns_settings_notify_admin'), 		//Callback
				NS_PAGE_SETTINGS,			 												//Page
				NS_SECTION_GENERAL 														//Section
			);
		add_settings_field(
				'always_cc', 																	//ID
				'Always CC Email', 														//Title
				array( $this, 'ns_settings_always_cc'), 			//Callback
				NS_PAGE_SETTINGS,			 												//Page
				NS_SECTION_GENERAL 														//Section
			);
		add_settings_field(
				'number_sites', 															//ID
				'Number of sites', 														//Title
				array( $this, 'ns_settings_number_sites'), 		//Callback
				NS_PAGE_SETTINGS,															//Page
				NS_SECTION_BATCH 															//Section
			);
		add_settings_field(
				'in_seconds', 																//ID
				'In seconds', 																//Title
				array( $this, 'ns_settings_in_seconds'), 			//Callback
				NS_PAGE_SETTINGS,						 									//Page
				NS_SECTION_BATCH 															//Section
			);
	}

	public function ns_validate_settings($arr_input) {
		
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		if($this->is_network_owner()) {
 			$options['notify_admin'] = trim( $arr_input['notify_admin'] );
 		} else if($options['notify_admin'] != trim( $arr_input['notify_admin'] )) {
 			$site_admin_email = get_site_option('admin_email');
 			$current_user = wp_get_current_user();
    	
 			$subj = "Network Settings Changed";
 			$body = "Protected settings(s) for WP Network Stats were changed by " . $current_user->user_lastname . ', ' .$current_user->user_firstname . ' ('. $current_user->user_email . ").\n";
 			$body .= "Please check the settings if this was not intentional. \n";
 			if(wp_mail( $site_admin_email, $subj, $body )) {
				//echo 'Email sent successfully';
			} else {
				//echo 'Email could not be sent';
			}
 		}
    $options['number_sites'] = trim( $arr_input['number_sites'] );
		$options['in_seconds'] = trim( $arr_input['in_seconds'] );
		$options['always_cc'] = trim( $arr_input['always_cc'] );
		
		return $options;
	}

	public function ns_validate_generate($arr_input) {
		$options = get_site_option(NS_OPTIONS_GENERATE);
		if(!isset($arr_input['email']) || empty(trim( $arr_input['email']))) {
			$current_user = wp_get_current_user();	
			$input_email = $current_user->user_email;
		} else {
			$input_email = trim( $arr_input['email'] );
		}
		$options['email'] = $input_email;
		$options['whitelist_stats'] = $arr_input['whitelist_stats'];

		return $options;
	}

	public function ns_section_general() {
		//echo '<p>General Settings.</p>';
	}

	public function ns_section_generate() {
		echo '<p>Settings for generating Network stats.</p>';
	}

	public function ns_section_notification() {
		echo '<p>Update the settings for generating Network Stats.</p>';
	}

	public function ns_section_batch() {
		echo '<p>The network stats are generated using WordPress Cron Jobs. Please configure these settings very carefully.</p>';
	}

	public function get_available_stats() {

		$stats = array(
			'site' => array(
				'name' => 'Site Stats',
				'description' => 'Site stats for all the sites in the network.',
				'icon' => 'networking'
				),
			'users-per-site' => array(
				'name' =>'User Stats per Site',
				'description' => 'Stats for all the users per site for all the sites in the network.',
				'icon' => 'id-alt'
				),
			'plugins-per-site' => array(
				'name' => 'Plugin Stats per Site',
				'description' => 'Stats for all the plugins per site for all the sites in the network.',
				'icon' => 'filter'
				),
			'plugins' => array(
				'name' => 'Plugin Stats',
				'description' => 'Plugin stats for all the sites in the network.',
				'icon' => 'admin-plugins'
				),
			'user' => array(
				'name' => 'User Stats',
				'description' => 'User stats for all the sites in the network.',
				'icon' => 'admin-users'
				),
			'theme' => array(
				'name' => 'Theme Stats',
				'description' => 'Theme stats for all the sites in the network.',
				'icon' => 'admin-appearance'
				),
			);

		return $stats;
	}

	public function ns_settings_stats_selection() {
		$whitelist_stats = array();

		$options = get_site_option(NS_OPTIONS_GENERATE);
		if($options != false) {
			$whitelist_stats = $options['whitelist_stats'];	
		}
		if ( !is_array($whitelist_stats) ) $whitelist_stats = array();
		
		//$blacklist = get_site_option( NS_OPTIONS_GENERATE, array() );
		$disabled = false;

		// blacklist must be an array, if anything else then just make it an empty array
		
		$available_stats = $this->get_available_stats();
		//asort($available_stats);

		?>
		<fieldset><legend class="screen-reader-text"><span>Stats Selection</span></legend>
		<?php
		foreach ( $available_stats as $slug => $stat ) {
			//$icon = isset($icons[$slug]) ? $icons[$slug] : self::$default_icon;
			?>
			<label>
				<input type='checkbox' name='<?php echo NS_OPTIONS_GENERATE; ?>[whitelist_stats][]' value='<?php echo $slug; ?>'
				<?php checked( in_array( $slug, $whitelist_stats ), true ); ?>
				<?php disabled( $disabled ); ?>>
				<span class="dashicons dashicons-<?php echo $stat['icon']; ?>"></span> <?php echo $stat['name'] ?>
			</label>
			<?php
			/*
			<p class="description"><?php echo $stat['description'];?></p>
			*/
			?>
			<br>
			<?php
		}
		?>
		<!--
		<aside role="note" id="jmc-note-1"><p class="description">*) Modules marked with an asterisk require a WordPress.com connection. They will be unavailable if Jetpack is forced into development mode.</p></aside>
		-->
		</fieldset>
		<?php
	}

	public function ns_settings_email() {
		$options = get_site_option(NS_OPTIONS_GENERATE);
		if(empty(trim($options['email']))) {
			$current_user = wp_get_current_user();	
			$input_email = $current_user->user_email;
		}
		echo "<input id='email' name='" . NS_OPTIONS_GENERATE . "[email]' size='40' type='email' value='{$options['email']}' />";
		?>
		<p class="description">This email address will be notified once the stats are available.</p>
		<?php
	}

	public function ns_settings_notify_admin() {
		if(!$this->is_network_owner()) {
			$disabled = false;
		}
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		$checked = $options['notify_admin'];
		?>
		<label>
			<input type='checkbox' name='<?php echo NS_OPTIONS_SETTINGS ?>[notify_admin]' value='1'
			<?php checked( $checked, '1' ); ?>
			<?php disabled( $disabled ); ?>>
			<span class="dashicons dashicons-email"></span>
			Always CC Site Admin
		</label>
		<p class="description">Note: The <b>Network Admin</b> will be notified if you change this.</p>
		<?php
	}

	public function ns_settings_always_cc() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		echo "<input id='always_cc' name='" . NS_OPTIONS_SETTINGS . "[always_cc]' size='40' type='text' value='{$options['always_cc']}' />";
		?>
		<p class="description">Note: You can add multiple emails separated by a comma.</p>
		<?php
	}


	public function ns_settings_number_sites() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		$blog_list = Network_Stats_Helper::get_network_blog_list();		
		$count_blogs = count($blog_list);
		
		echo "<input id='number_sites' name='" . NS_OPTIONS_SETTINGS . "[number_sites]' size='40' type='number' value='{$options['number_sites']}' min='1' max='{$count_blogs}' />";
	}

	public function ns_settings_in_seconds() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		echo "<input id='in_seconds' name='" . NS_OPTIONS_SETTINGS . "[in_seconds]' size='40' type='number' value='{$options['in_seconds']}' min='1' max='30' />";
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

	public function ns_options_generate() {
		update_site_option( NS_OPTIONS_GENERATE, $_POST[NS_OPTIONS_GENERATE] );
		
		//self::generate_reports($print = true);
		//$options = get_site_option('ns_options_notification');
		$to_email = $_POST[NS_OPTIONS_GENERATE]['email'];

		wp_schedule_single_event(time(), 'cron_generate_reports', array($number_sites = 300, $in_seconds = 10, $to_email = $to_email));

		wp_redirect(
	    	add_query_arg(
		        array( 'page' => $this->menu_slug, 'updated' => 'true' ),
		        (is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ))
		    )
		);
		exit;
	}
	
	public function ns_options_settings() {
		update_site_option( NS_OPTIONS_SETTINGS, $_POST[NS_OPTIONS_SETTINGS] );
		
		wp_redirect(
	    	add_query_arg(
		        array( 'page' => $this->menu_slug . '-settings', 'updated' => 'true' ),
		        (is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ))
		    )
		);
		exit;
	}
	/**
	 * Generate Reports
	 * @since 0.1.0
	 */
	
	public function generate_reports($number_sites, $in_seconds, $to_email) {
		
		$upload_dir = wp_upload_dir();
		$report_dirname = $upload_dir['basedir'].'/'. NS_UPLOADS;
		
		if (!file_exists($report_dirname)) {
		  mkdir($report_dirname, 0775, true);
		} else if(!is_dir($report_dirname)) {
			rename($report_dirname, $report_dirname . '.bak');
			mkdir($report_dirname, 0775, true);
		}

		$blog_list = Network_Stats_Helper::get_network_blog_list();		
		$steps = count($blog_list) / $number_sites;
		for($step = 0; $step < $steps; $step++) {
			$limit = $number_sites;
			$offset = $step * $number_sites;
			$args = array(
        'limit'      => $limit,
        'offset'     => $offset,
    	);
			wp_schedule_single_event(time()+($in_seconds*$step), 'cron_refresh_site_stats', array($args));
			Network_Stats_Helper::write_log('Step ' . $step . ' args: ' . print_r($args, $return = true) . "\n");
		}

		wp_schedule_single_event(time()+60+($in_seconds*($steps+1)), 'cron_refresh_plugin_stats');
		wp_schedule_single_event(time()+60+($in_seconds*($steps+2)), 'cron_refresh_user_stats');
		wp_schedule_single_event(time()+60+($in_seconds*($steps+3)), 'cron_refresh_theme_stats');
		wp_schedule_single_event(time()+60+($in_seconds*($steps+4)), 'cron_send_notification_email', array($to_email));

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
		// Sites Summary   //
		/////////////////////

		$blog_list = Network_Stats_Helper::get_network_blog_list();
		$args = array(
				'public'     => 1,
			);
		$blog_public_list = Network_Stats_Helper::get_network_blog_list($args);

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
		$body .= 'There are ' . count($blog_list) . ' sites. ' . count($blog_public_list) . ' are public.' . "\n";
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

	public function is_network_owner() {
		// Retrieving the network admin's email
		$site_admin_email = get_site_option('admin_email');
		// Getting the current users info
		$current_user = wp_get_current_user();
    // and then checking against the network admin data against the user data.
		if ($current_user->user_email == $site_admin_email) {
			return true;
		} else {
			return falwse;
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
				