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
 * Defines the plugin name, version, and hooks for how to
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
	private $query_arg;

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
		$this->query_arg = array();

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

		//wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . '../vendor/bootstrap/css/bootstrap.min.css', false, $this->version, 'all');
		//wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/network-stats-admin.css', array('bootstrap'), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts($hook)
	{

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Network_Stats_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Network_Stats_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/network-stats-admin.js', array('jquery'), $this->version, false);
	}

	public function load_analytics_page_styles() {
		//wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . '../vendor/bootstrap/css/bootstrap.min.css', false, $this->version, 'all');
		wp_enqueue_style( 'nv-d3', plugin_dir_url( __FILE__ ) . '../vendor/nv.d3/nv.d3.min.css', false, $this->version, 'all' );
		wp_enqueue_style( 'parcoords', plugin_dir_url( __FILE__ ) . '../vendor/parcoords/d3.parcoords.css', false, $this->version, 'all');
		wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . '../vendor/bootstrap/css/bootstrap.min.css', false, $this->version, 'all');
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/network-stats-admin.css', array('bootstrap'), $this->version, 'all' );
		//wp_enqueue_style( $this->plugin_name . '-highlight', plugin_dir_url( __FILE__ ) . 'css/highlight.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->plugin_name . '-google', "https://fonts.googleapis.com/css?family=Open+Sans:400,700", array(), $this->version, 'all' );
	}
	/**
	 * Load the JS for Analytics page.
     */
	public function load_analytics_page_scripts() {
		//wp_enqueue_script($this->plugin_name . '-timeseries', plugin_dir_url(__FILE__) . 'js/timeseries.js', array(), $this->version, true);
		//wp_enqueue_script($this->plugin_name . '-d3', "https://d3js.org/d3.v3.min.js", false);
		wp_enqueue_script( 'd3', plugin_dir_url( __FILE__ ) . '../vendor/d3/d3.min.js', array(), $this->version, false );
		//wp_enqueue_script('d3', "https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.2/d3.min.js", false);
		wp_enqueue_script('nv-d3', plugin_dir_url(__FILE__) . '../vendor/nv.d3/nv.d3.min.js', array('d3'), $this->version, false);
		wp_enqueue_script('d3-svg-multibrush', plugin_dir_url( __FILE__ ) . '../vendor/d3/d3.svg.multibrush.js', array('d3'), $this->version, false );
		wp_enqueue_script('parcoords', plugin_dir_url(__FILE__) . '../vendor/parcoords/d3.parcoords.js', array('d3'), $this->version, false);
		wp_enqueue_script(
			$this->plugin_name . '-analytics',
			plugin_dir_url(__FILE__) . 'js/network-stats-admin-analytics.js',
			array('jquery', 'nv-d3', 'parcoords'),
			$this->version,
			false
		);
		/*
		wp_enqueue_script($this->plugin_name . '-lodash', "https://cdnjs.cloudflare.com/ajax/libs/lodash.js/3.1.0/lodash.min.js", array(), $this->version, false);
		wp_enqueue_script($this->plugin_name . '-moment', "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js", array(), $this->version, false);
		wp_enqueue_script($this->plugin_name . '-highlight', "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js", array(), $this->version, false);
		*/

		$file_path_url = network_admin_url( 'admin.php?page=' . $this->plugin_name . '-analytics' . '&file=');
		$data_to_js = array(
			'file_site_stats' => $file_path_url . 'site-stats.csv',
			'file_user_stats' => $file_path_url . 'user-stats.csv',
			'file_plugin_stats' => $file_path_url . 'plugin-stats.csv',
			'file_theme_stats' => $file_path_url . 'theme-stats.csv',
			'file_plugin_stats_per_site' => $file_path_url . 'plugin-stats-per-site.csv',
			'file_user_stats_per_site' => $file_path_url . 'user-stats-per-site.csv',
		);
		wp_localize_script($this->plugin_name . '-analytics', 'data_to_js', $data_to_js);
	}

	/**
	 * Handle File download requests
	 */
	public function handle_file_requests() {
		//global $pagenow;
		//die(var_dump($_GET));
		//if ($pagenow=='admin.php' &&
		if (isset($_GET['page']) &&
			$_GET['page'] = $this->plugin_name . '-analytics' &&
			current_user_can('manage_network') &&
			isset($_GET['file']) ) {

			$path_parts = pathinfo($_GET['file']);
			$file_name  = $path_parts['basename'];

			$file_path = NS_REPORT_DIRNAME . '/' . $file_name;

			if (file_exists($file_path) && is_file($file_path)) {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$file_name.'"');

				//For IE6
				header("Pragma: public");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file_path));


				readfile($file_path);

				/**
				 * The following code is added to handle large files
				 */
				/*
				flush();
				// set the download rate limit (=> 20,5 kb/s)
				$download_rate = 20.5;
				$file = fopen($file_path, "rb");
				while(!feof($file))
				{
					// send the current file part to the browser
					print fread($file, round($download_rate * 1024));
					// flush the content to the browser
					ob_flush();
					flush();
					// sleep one second
					//sleep(1);
				}
				fclose($file);
				*/
				// End: Handle large file code block

				exit;
			}
		}

		if(isset($_GET['file'])) {
			header("HTTP/1.0 404 Not Found");
			exit;
		} else {
			return;
		}

	}

	/**
	 * Registers the menu items for admin dashboard
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {

		if (!is_network_admin () || ! Network_Stats_Helper::is_plugin_network_activated ( NS_PLUGIN )) {
			return false;
		}
		/**
		 * Registering menu item in Network Dashboard, allowing it to be displayed for all super admin users
		 * with "manage_network" capability and displaying it somewhere on top position but below dashboard and Jetpack.
		 */

		/**
		 *
		 * @todo Get the read/write capabilities from db required to view/manage the plugin as set by the user.
		 */
		$read_cap = 'manage_network';
		$ns_home_page = add_menu_page ( 'Network Stats', 'Network Stats', $read_cap, $this->menu_slug, array (
				$this,
				'network_stats_admin_page'
		), 'dashicons-analytics', '3.756789' );

		$read_cap = 'manage_network';
		$ns_analytics_page = add_submenu_page ( $this->plugin_name, 'Analytics', 'Analytics', $read_cap, $this->menu_slug . '-analytics', array (
			$this,
			'network_stats_analytics_page'
		) );
		// http://wordpress.stackexchange.com/questions/41207/how-do-i-enqueue-styles-scripts-on-certain-wp-admin-pages
		add_action( 'load-' . $ns_home_page, array($this, 'enqueue_scripts' ) );
		add_action( 'load-' . $ns_home_page, array($this, 'enqueue_styles' ) );

		add_action( 'load-' . $ns_analytics_page, array($this, 'load_analytics_page_scripts' ) );
		add_action( 'load-' . $ns_analytics_page, array($this, 'load_analytics_page_styles' ) );

		$read_cap = 'manage_network_options';
		add_submenu_page( 'settings.php', 'WP Network Stats Settings', 'WP Network Stats', $read_cap, $this->menu_slug . '-settings', array( $this, 'show_network_settings' ) );

		/*
		$read_cap = 'manage_network_options';
		add_submenu_page ( $this->plugin_name, 'Settings', 'Settings', $read_cap, $this->menu_slug . '-settings', array (
				$this,
				'network_stats_settings'
		) );
		*/

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
	 * Displays WP Network Stats Plugin's main page.
	 * @since 0.1.0
	 */
	public function network_stats_admin_page()
	{
		?>
		<div class="wrap">
			<h2>WP Network Stats</h2>

			<?php
			if (isset($_GET['error'])):
				if (isset($_GET['error']['email'])):
					?>
					<div class="notice notice-error"><p><?php _e('Please enter a valid email address.'); ?></p></div>
				<?php endif;
			endif;
			?>
			<?php
			$requested_cron_id = get_site_option(NS_CURRENT_STATUS);
			$disabled = false;
			if ($requested_cron_id) {
				$disabled = true;
				?>
				<div class="notice update-nag">
					<p><?php _e('Reports are being generated in the background. Please wait before submitting new request.'); ?></p>
				</div>
				<?php
			}
			?>

			<?php
			/*if ( isset( $_GET['updated'] ) ): ?>
				<div class="updated"><p><?php _e( 'Reports are being generated in the background. Please wait for email notification.'); ?></p></div>
			<?php endif; */
			?>

			<form method="POST" id="options" action="edit.php?action=<?php echo NS_OPTIONS_GENERATE ?>">
				<?php
				// This prints out all hidden setting fields
				// settings_fields( $option_group )
				settings_fields(NS_OPTIONS_GROUP);
				?>
				<?php
				//do_settings_sections( $page )
				do_settings_sections(NS_PAGE_GENERATE); ?>
				<?php
				//submit_button('Save');
				?>
				<?php
				$other_attributes = array();
				if ($disabled) {
					$other_attributes['disabled'] = '';
				}
				$other_attributes['id'] = 'submit';
				submit_button('Generate Reports', 'primary', '', true, $other_attributes); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Prints the networks stats analytics page.
     */
	public function network_stats_analytics_page()
	{
		?>
		<div class="bootstrap-fluid" id="analytics">
			<div class="container-fluid">
				<div class="error" id="vis_loading_error"><p><strong>There was an error downloading some/all of analytics data. Please refresh stats.</strong></p></div>

				<h2>WP Network Stats <a href="<?php echo network_admin_url( 'admin.php?page=' . $this->plugin_name ) ?>" role="button" class="btn btn-warning">Refresh Stats</a></h2>
				<div class="update-nag">
					<p>This page doesn't update with latest data automatically. Please <strong>Refresh Stats</strong> and wait for the confirmation email to see latest analytics.</p>
					<p>If you don't see any charts, you have to generate reports from Network Stats page first.</p>
				</div>
				<h4 id="vis_loading_block">
					<div class="vis_loading"></div>
					<div>Loading data for Analytics.</div>
				</h4>

				<div class="row ">
					<div class="col-md-12">
						<h3>Number of Registerations</h3>
						<div class="vis_registrations"><svg id="line_registrations"></svg></div>
					</div>
				</div>
				<div class="h-divider">
				</div>
				<div class="row">
					<div class="col-md-4">
						<h3>Site Privacy</h3>
						<div class="vis_privacy with-transitions" id="vis_privacy"><svg id="pie_privacy"></svg></div>
					</div>
					<div class="col-md-4 v-divider">
						<h3>Curent Theme</h3>
						<div class="vis_theme"><svg id="pie_theme"></svg></div>
					</div>
					<div class="col-md-4 v-divider">
						<h3>DB Version</h3>
						<div class="vis_db_version"><svg id="pie_db_version"></svg></div>
					</div>
				</div>
				<div class="row" style="display:none">
					<div class="col-md-12">
						<h3>Site Registrations by time of day</h3>
						<div class="vis_site_registrations"><svg id="scatter_site_registrations"></svg></div>
					</div>
				</div>
				<div class="h-divider">
				</div>
				<div class="row" >
					<div class="col-md-12">
						<h3>Multidimensional Detective</h3>
						<div class="vis_multidimensional_detective"><div class="parcoords" id="parallel_multidimensional_detective" style="height:300px"></div></div>
						<button type="button" id="btnExport" class="btn btn-primary">Export Selected Data</button>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Prints the network settings
	 *
	 * @since 0.2
	 */
	public function show_network_settings() {
		?>
		<div class="wrap">
			<h2>WP Network Stats</h2>
			<?php
			if ( isset( $_GET['updated'] ) ):
				?><div id="message" class="updated notice is-dismissible"><p><?php _e( 'Options saved.' ) ?></p></div><?php
			endif;

			if (isset($_GET['error'])):
				foreach ($_GET['error'] as $error_msg) {
					?>
					<div class="notice notice-error"><p><?php _e(stripslashes(urldecode($error_msg))); ?></p></div>
					<?php
				}
			endif;
			?>
			<form method="POST" id="options" action="edit.php?action=<?php echo NS_OPTIONS_SETTINGS ?>">
				<?php
				// This prints out all hidden setting fields
				// settings_fields( $option_group )
				settings_fields(NS_OPTIONS_GROUP);
				?>
				<?php
				//do_settings_sections( $page )
				do_settings_sections(NS_PAGE_SETTINGS); ?>
				<?php
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * whitelist options.
	 * since 0.1.0
	 */
	public function register_settings() {
		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( NS_OPTIONS_GROUP, NS_OPTIONS_SETTINGS );
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

		$requested_cron_id = get_site_option(NS_CURRENT_STATUS);
		$disabled = true;
		if($requested_cron_id) {
			add_settings_field(
					'cancel_previous', 																			//ID
					'Cancel Pending Requests', 										//Title
					array( $this, 'ns_settings_cancel_previous'), 					//Callback
					NS_PAGE_GENERATE,					 										//Page
					NS_SECTION_GENERATE 											//Section
				);
		}
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
		/*
		add_settings_field(
				'number_users', 															//ID
				'Number of users', 														//Title
				array( $this, 'ns_settings_number_users'), 		//Callback
				NS_PAGE_SETTINGS,															//Page
				NS_SECTION_BATCH 															//Section
			);
		*/
		add_settings_field(
				'in_seconds', 																//ID
				'In seconds', 																//Title
				array( $this, 'ns_settings_in_seconds'), 			//Callback
				NS_PAGE_SETTINGS,						 									//Page
				NS_SECTION_BATCH 															//Section
			);
	}

	/**
	 * Echos a settings section header
	 *
	 * @since 0.1
	 *
	 * @param $option (unused)
	 * @echo Html
	 */
	public function add_settings_section( $option ) {
		echo '<p>The options in this section are provided by WP Network Stats. This plugin allows you to generate various stats for your multisite network.';
		echo '<br><em>' . __('These settings are only visible to you as Super Admin and these settings affect all sites on the network.') . '</em>';
		echo '</p>';
	}

	/**
	 * Notify the network admin if any protected settings changed.
     */
	public function notify_admin_settings_changed() {
		$site_admin_email = get_site_option('admin_email');
		$current_user = wp_get_current_user();

		$subj = "Network Settings Changed";
		$body = "Protected settings(s) for WP Network Stats were changed by " . $current_user->user_lastname . ', ' .$current_user->user_firstname . ' ('. $current_user->user_email . ").\n";
		$network_home_url = network_home_url();
		$body .= "Please check the settings for your network - " . $network_home_url . ", if this was not intentional. \n";
		if(wp_mail( $site_admin_email, $subj, $body )) {
			Network_Stats_Helper::write_log('Email sent successfully');
		} else {
			Network_Stats_Helper::write_log('Email could not be sent - protected settings changed.');
		}
	}

	/**
	 * Validate the settings for WP Network Stats.
	 * @param $arr_input
	 * @return options
     */
	public function ns_validate_settings($arr_input) {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		Network_Stats_Helper::write_log('Validate Settings Options from db: ' . print_r($options, $return = true));

		// @Todo use apply filter for default settings.
		$ns_options_def_settings = $this->get_default_settings();
		$notify_admin = $ns_options_def_settings['notify_admin'];
		$number_sites = !empty($options['number_sites']) ? $options['number_sites'] : $ns_options_def_settings['number_sites'];
		$number_users = !empty($options['number_users']) ? $options['number_users'] : $ns_options_def_settings['number_users'];
		$in_seconds = !empty($options['in_seconds']) ? $options['in_seconds'] : $ns_options_def_settings['in_seconds'];
		$always_cc_emails = !empty($options['always_cc']) ? $options['always_cc'] : array();

		if(!empty($arr_input['notify_admin'])) {
			if (!$this->is_network_owner() && $options['notify_admin'] != trim($arr_input['notify_admin'])) {
				$this->notify_admin_settings_changed();
			}
			$notify_admin = trim($arr_input['notify_admin']);
		}


		if(!empty($arr_input['number_sites']) && $arr_input['number_sites'] >0)
	 		$number_sites = trim( $arr_input['number_sites'] );

		if(!empty($arr_input['number_users']) && $arr_input['number_users'] >0)
			$number_users = trim( $arr_input['number_users'] );

		if(!empty($arr_input['in_seconds']) && $arr_input['in_seconds'] >0)
			$in_seconds = trim( $arr_input['in_seconds'] );

		$options['notify_admin'] = $notify_admin;
		$options['number_sites'] = $number_sites;
		$options['number_users'] = $number_users;
		$options['in_seconds'] = $in_seconds;

		$error_always_cc = false;
		if(!empty($arr_input['always_cc'])) {
			$always_cc = preg_replace('/\s+/', '', $arr_input['always_cc']);
			$always_cc_emails = explode(',', $always_cc);
			foreach ($always_cc_emails as $email) {
				if(Network_Stats_Helper::validate_email($email)) {
					$always_cc_emails_valid[] = $email;
				} else {
					$error_always_cc = true;
				}
			}
		}
		if($error_always_cc) {
			$options['error'][] = urlencode("Please enter valid list of email for 'Always CC'");
			$options['always_cc'] = $always_cc_emails;
		} else {
			$options['always_cc'] = $always_cc_emails_valid;
		}
		Network_Stats_Helper::write_log('Validate Settings Options: ' . print_r($options, $return = true));
		return $options;
	}

	/**
	 * Validate generate stats options from plugin's main page.
	 * @param $arr_input
	 * @return options
     */
	public function ns_validate_generate($arr_input) {
		$options = get_site_option(NS_OPTIONS_GENERATE);
		if(!isset($arr_input['email']) || empty(trim( $arr_input['email']))) {
			$current_user = wp_get_current_user();
			$input_email = $current_user->user_email;
		} else {
			$input_email = trim( $arr_input['email'] );
			if(!Network_Stats_Helper::validate_email($input_email)) {
				// Error with input_email
			}
		}
		$options['email'] = $input_email;

		if(isset($arr_input['whitelist_stats']) && !empty($arr_input['whitelist_stats']) && is_array($arr_input['whitelist_stats'])) {
			// @TODO - Further validation required to check if stats in the list.
			$options['whitelist_stats'] = $arr_input['whitelist_stats'];
		}

		return $options;
	}

	public function ns_section_general() {
		echo '<p>General Settings.</p>';
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

	/**
	 * Get Available Stats Information
	 * @return array
     */
	public function get_available_stats() {

		$stats = array(
			'network' => array(
				'name' => 'Network Stats Overview',
				'description' => 'Overview of the stats for your network.',
				'icon' => 'networking'
				),
			'sites' => array(
				'name' => 'Site Stats',
				'description' => 'Site stats for all the sites in the network.',
				'icon' => 'admin-multisite'
				),
			'users_per_site' => array(
				'name' =>'User Stats per Site',
				'description' => 'Stats for all the users per site for all the sites in the network.',
				'icon' => 'id-alt'
				),
			'plugins_per_site' => array(
				'name' => 'Plugin Stats per Site',
				'description' => 'Stats for all the plugins per site for all the sites in the network.',
				'icon' => 'filter'
				),
			'plugins' => array(
				'name' => 'Plugin Stats',
				'description' => 'Plugin stats for all the sites in the network.',
				'icon' => 'admin-plugins'
				),
			'users' => array(
				'name' => 'User Stats',
				'description' => 'User stats for all the sites in the network.',
				'icon' => 'admin-users'
				),
			'themes' => array(
				'name' => 'Theme Stats',
				'description' => 'Theme stats for all the sites in the network.',
				'icon' => 'admin-appearance'
				),
			);

		return $stats;
	}

	/**
	 * Show Stats Selection checkboxes.
	 *
     */
	public function ns_settings_stats_selection() {
		$whitelist_stats = array();

		$options = get_site_option(NS_OPTIONS_GENERATE);
		if($options != false) {
			$whitelist_stats = $options['whitelist_stats'];
		}
		if ( !is_array($whitelist_stats) ) $whitelist_stats = array();

		//$blacklist = get_site_option( NS_OPTIONS_GENERATE, array() );
		// blacklist must be an array, if anything else then just make it an empty array

		$available_stats = $this->get_available_stats();
		//asort($available_stats);

		?>
		<fieldset><legend class="screen-reader-text"><span>Stats Selection</span></legend>
		<?php
		foreach ( $available_stats as $slug => $stat ) {
			//$icon = isset($icons[$slug]) ? $icons[$slug] : self::$default_icon;
			if($slug == 'network') {
				$checked = true;
				$disabled = true;
			} else {
				$checked = in_array( $slug, $whitelist_stats );
				$disabled = false;
			}
			?>
			<label>
				<input type='checkbox' name='<?php echo NS_OPTIONS_GENERATE; ?>[whitelist_stats][]' value='<?php echo $slug; ?>'
					id='<?php echo "cb_" . $slug; ?>'
					<?php checked( $checked ); ?>
					<?php disabled( $disabled ); ?>
					<?php
					/*
						if($slug == 'users_per_site' || $slug == 'plugins_per_site') {
							echo "onclick='handleClickStatsPerSite(this);'";
						} else if($slug == 'sites') {
							echo "onclick='handleClickStatsSites(this);'";
						}
					*/
					?>
				/>
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
		<aside role="note" id="ns-note-1"><p class="description">*) Network Overview is part of the email notification and can not be unchecked.</p></aside>
		-->
		</fieldset>
		<?php
	}

	/**
	 * Show input box for email.
	 *
     */
	public function ns_settings_email() {
		$options = get_site_option(NS_OPTIONS_GENERATE);
		if(empty(trim($options['email']))) {
			$current_user = wp_get_current_user();
			$input_email = $current_user->user_email;
		} else {
			$input_email = trim($options['email']);
		}
		echo "<input id='email' name='" . NS_OPTIONS_GENERATE . "[email]' size='40' type='email' value='{$input_email}' />";
		?>
		<p class="description">This email address will be notified once the stats are available.</p>
		<?php
	}

	/**
	 * Shows cancel checkbox when there is already a cron job running.
     */
	public function ns_settings_cancel_previous() {
		$requested_cron_id = get_site_option(NS_CURRENT_STATUS);
		$disabled = true;
		if($requested_cron_id) {
			$disabled = false;
		}
		?>
		<label>
			<input type='checkbox' name='<?php echo NS_OPTIONS_GENERATE; ?>[cancel_previous]' value='' onclick='handleClickCancelPrevious(this);'
				<?php disabled( $disabled ); ?>
			/>
			<span class="dashicons dashicons-warning"></span> Cancel any pending requests by anyone in your network.
		</label>
		<?php
	}

	/**
	 * Checkbox to always cc the network admin.
	 *
     */
	public function ns_settings_notify_admin() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		$checked = $options['notify_admin'];
		?>
		<label>
			<input type='checkbox' name='<?php echo NS_OPTIONS_SETTINGS ?>[notify_admin]' value='1'
				<?php checked( $checked, '1' ); ?>
			>
			<span class="dashicons dashicons-email"></span>
			Always CC Site Admin
		</label>
		<p class="description">Note: The <b>Network Admin</b> will be notified if you change this.</p>
		<?php
	}

	/**
	 * Textbox for emails that should always be CC'ed with every report.
     */
	public function ns_settings_always_cc() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		if(empty($options['always_cc'])) {
			$always_cc_emails_valid = '';
		} else {
			$always_cc_emails_valid = implode(', ', $options['always_cc']);
		}
		echo "<input id='always_cc' name='" . NS_OPTIONS_SETTINGS . "[always_cc]' size='40' type='text' value='{$always_cc_emails_valid}' />";
		?>
		<p class="description">You can add multiple emails separated by a comma.</p>
		<?php
	}


	/**
	 * Batch processing setting - number of sites
     */
	public function ns_settings_number_sites() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		$count_blogs = get_blog_count();

		echo "<input id='number_sites' name='" . NS_OPTIONS_SETTINGS . "[number_sites]' size='40' type='number' value='{$options['number_sites']}' min='1' max='{$count_blogs}' />";
		echo "<p class='description'>The number of sites for one batch of the reports generation process.</p>";
	}

	/**
	 * Batch processing setting - number of users
     */
	public function ns_settings_number_users() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		$user_list = Network_Stats_Helper::get_list_all_users ();

		$count_users = count($user_list);

		echo "<input id='number_users' name='" . NS_OPTIONS_SETTINGS . "[number_users]' size='40' type='number' value='{$options['number_users']}' min='1' max='{$count_users}' />";
		echo "<p class='description'>(WIP) The number of users for one batch of the reports generation process.</p>";
	}

	/**
	 * Batch processing setting - time interval between batches.
     */
	public function ns_settings_in_seconds() {
		$options = get_site_option(NS_OPTIONS_SETTINGS);
		echo "<input id='in_seconds' name='" . NS_OPTIONS_SETTINGS . "[in_seconds]' size='40' type='number' value='{$options['in_seconds']}' min='1' max='30' />";
		echo "<p class='description'>Time interval between processing a batch of sites and users.</p>";
	}

	/**
	 * Get Default Settings
	 * @return array
     */
	private function get_default_settings() {
		$count_blogs = get_blog_count();
		$user_list = Network_Stats_Helper::get_list_all_users ();
		$count_users = count($user_list);

		$ns_options_def_settings = array(
				'number_sites' => ($count_blogs < 50)? $count_blogs : 50,
				'number_users' => ($count_users < 50)? $count_users : 50,
				'in_seconds' => '10',
				'notify_admin' => '0'
			);
		return $ns_options_def_settings;
	}

	/**
	 * Set Default Settings.
     */
	public function set_defaults() {
		$ns_options_def_settings = $this->get_default_settings();
		update_site_option( NS_OPTIONS_SETTINGS, $ns_options_def_settings);
	}

	/**
	 * Process new stats request.
     */
	public function ns_options_generate() {
		Network_Stats_Helper::write_log('New Stats Requested: ' . print_r($_POST, $return = true));

		if(empty($_POST['error'])) {
			update_site_option( NS_OPTIONS_GENERATE, $_POST[NS_OPTIONS_GENERATE] );
			$updated = true;
			$options = get_site_option(NS_OPTIONS_SETTINGS);
			if(!$options['number_sites'] || !$options['in_seconds']) {
				$this->set_defaults();
				$options = get_site_option(NS_OPTIONS_SETTINGS);
			}

			$to_email = $_POST[NS_OPTIONS_GENERATE]['email'];
			$number_sites = $options['number_sites'];
			$in_seconds = $options['in_seconds'];

			$this->generate_reports($number_sites = $number_sites, $in_seconds = $in_seconds, $to_email = $to_email);
		} else {
			$updated = false;
		}

		wp_redirect(
	    	add_query_arg(
		        array( 'page' => $this->menu_slug, 'updated' => $updated, /*'error' => $_POST['error']*/ ),
		        (is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ))
		    )
		);
		exit;
	}

	/**
	 * Save network settings
     */
	public function ns_options_settings() {
		Network_Stats_Helper::write_log('Save Settings Options: ' . print_r($_POST[NS_OPTIONS_SETTINGS], $return = true));

		// First validate
		$options = $this->ns_validate_settings($_POST[NS_OPTIONS_SETTINGS]);

		if(empty($options['error'])) {
			update_site_option( NS_OPTIONS_SETTINGS, $options );
			$updated = true;
		} else {
			$updated = false;
		}

		wp_redirect(
	    	add_query_arg(
		        array( 'page' => $this->menu_slug . '-settings', 'updated' => $updated, 'error' => !empty($options['error'])?$options['error'] : [] ),
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

		$cron_id = time();
		update_site_option( NS_CURRENT_STATUS, $cron_id);

		if (!file_exists($report_dirname)) {
		  mkdir($report_dirname, 0775, true);
		} else if(!is_dir($report_dirname)) {
			rename($report_dirname, $report_dirname . '.bak');
			mkdir($report_dirname, 0775, true);
		}

		$args = array(
        	'cron_id'		 => $cron_id
    	);

		$steps = 0;

		if($this->is_stat_checked('users')) {
			wp_schedule_single_event(time()+$in_seconds+($in_seconds*($steps++)), 'cron_refresh_user_stats', array($args));
		}
		if($this->is_stat_checked('plugins')) {
			wp_schedule_single_event(time()+$in_seconds+($in_seconds*($steps++)), 'cron_refresh_plugin_stats', array($args));
		}
		if($this->is_stat_checked('themes')) {
			wp_schedule_single_event(time()+$in_seconds+($in_seconds*($steps++)), 'cron_refresh_theme_stats', array($args));
		}

		if($this->is_stat_checked('users_per_site')) {
			$args['users_per_site'] = true;
		}
		if($this->is_stat_checked('plugins_per_site')) {
			$args['plugins_per_site'] = true;
		}
		if($this->is_stat_checked('sites')) {
			$args['sites'] = true;
		}
		if($args['users_per_site'] || $args['plugins_per_site'] || $args['sites']) {
			//Site Stats Cron
			$args['limit'] = $number_sites;
			$args['offset'] = 0;
			//$args['number_sites'] = $number_sites;
			$args['in_seconds'] = $in_seconds;
			wp_schedule_single_event(time()+$in_seconds+($in_seconds*($steps++)), 'cron_refresh_site_stats', array($args));
		} else {
			wp_schedule_single_event(time()+$in_seconds+($in_seconds*($steps++)), 'cron_send_notification_email', array($args));
			//Network_Stats_Helper::write_log('Scheduling Email: ' . print_r($args, $return = true) . "\n");
		}
	}

	public function is_valid_cron_id($args) {
		$current_cron_id = $args['cron_id'];
		$requested_cron_id = get_site_option(NS_CURRENT_STATUS);

		if(!$requested_cron_id || $current_cron_id != $requested_cron_id) {
			//Network_Stats_Helper::write_log('Invalid cron id: ' . print_r($args, $return = true) . "\n");
			return false;
		} else {
			//Network_Stats_Helper::write_log('Valid cron id: ' . print_r($args, $return = true) . "\n");
			return true;
		}
	}

	public function is_stat_checked($slug) {
		$options = get_site_option(NS_OPTIONS_GENERATE);
		if($options != false) {
			$whitelist_stats = $options['whitelist_stats'];
		}
		if ( !is_array($whitelist_stats) ) $whitelist_stats = array();

		$checked = in_array( $slug, $whitelist_stats );
		return $checked;
	}

	public function refresh_site_stats($args) {

		if(!$this->is_valid_cron_id($args)) {
			return;
		}
		$this->site_stats_admin->refresh_site_stats ($args);
	}

	public function refresh_plugin_stats($args) {
		if(!$this->is_valid_cron_id($args)) {
			return;
		}
		$this->plugin_stats_admin->refresh_plugin_stats ();
	}

	public function refresh_user_stats($args) {
		if(!$this->is_valid_cron_id($args)) {
			return;
		}
		$this->user_stats_admin->refresh_user_stats ();
	}

	public function refresh_theme_stats($args) {
		if(!$this->is_valid_cron_id($args)) {
			return;
		}
		$this->theme_stats_admin->refresh_theme_stats ();
	}

	public function send_notification_email($args) {
		if(!$this->is_valid_cron_id($args)) {
			return;
		}

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
		//Network_Stats_Helper::write_log('Blog List: ' . print_r($blog_list, $return = true));


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
		 * @todo get custom role type such as support managers - should be able to use get_users() passing meta_key and meta_value
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

		if($this->is_stat_checked('plugins')) {
			array_push($attachments, $report_plugin_stats);
		}
		if($this->is_stat_checked('users')) {
			array_push($attachments, $report_user_stats);
		}
		if($this->is_stat_checked('themes')) {
			array_push($attachments, $report_theme_stats);
		}

		if($this->is_stat_checked('sites')) {

			array_push($attachments, $report_site_stats);

	    if($this->is_stat_checked('users_per_site')) {
	    	array_push($attachments, $report_user_stats_per_site);
	    }
	    if($this->is_stat_checked('plugins_per_site')) {
	    	array_push($attachments, $report_plugin_stats_per_site);
			}
		}


		//////////////////
		// Mail Headers //
		//////////////////

		$ns_options_generate = get_site_option(NS_OPTIONS_GENERATE);
		$to_email = $ns_options_generate['email'];

		$ns_options_settings = get_site_option(NS_OPTIONS_SETTINGS);
		if($ns_options_settings['notify_admin']) {
			$site_admin_email = get_site_option('admin_email');
			$headers[] = 'Cc: ' . $site_admin_email;
		}

		if($ns_options_settings['always_cc'] && !empty($ns_options_settings['always_cc'])) {
			foreach ($ns_options_settings['always_cc'] as $always_cc_email) {
				$headers[] = 'Cc: ' . $always_cc_email;
			}
		}

		if(!wp_mail( $to_email, $subj, $body, $headers, $attachments )) {
			Network_Stats_Helper::write_log('Email could not be sent');
		}

		update_site_option(NS_CURRENT_STATUS, 0);

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

}
