<?php

/**
 * The Site-Stats specific functionality of the plugin.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.1.0
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 */

/**
 * The Site-Stats specific functionality of the plugin.
 *
 * @package Network_Stats
 * @subpackage Network_Stats/admin
 * @author Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Site_Stats_Admin {
	
	/**
	 * The name of the site stats table
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the site stats table
	 */
	private $table_name;
	
	/**
	 * The name of the site stats table including prefix
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the site stats table including prefix
	 */
	private $site_table;
	
	/**
	 * Site Setup Wizard table name including prefix.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $version Site Setup Wizard table name including prefix.
	 */
	private $ssw_table_name;
	
	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @var string $table_name	The table name for site stats.
	 */
	public function __construct($table_name = NS_SITE_TABLE) {
		global $wpdb;
		$this->table_name = $table_name;
		$this->site_table = $wpdb->base_prefix . $table_name;
		
		$this->ssw_table_name = $wpdb->base_prefix . SSW_TABLE_NAME;
	}
	
	/**
	 * Refresh the site stats
	 *
	 * @since 0.1.0
	 */
	public function refresh_site_stats() {
		global $wpdb;
		
		$blogs = $wpdb->get_results ( $wpdb->prepare ( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d", $wpdb->siteid ) );
		/**
		 *
		 * @todo Take care of spam, deleted and archived sites
		 *      
		 *       AND spam = '0'
		 *       AND deleted = '0'
		 *       AND archived = '0'
		 *       AND mature = '0'
		 */
		
		$ens_site_data = array ();
		$ens_site_row = array ();
		
		/**
		 * To add Table headers:
		 * $ens_site_row = array("Blog ID", "Blog Name", "Blog URL", "Privacy", "Current Theme", "Admin Email", "Total Users", "Active Plugins", "Site Type");
		 * $ens_site_data[] = $ens_site_row;
		 */
		
		/* Get List of all plugins */
		$plugins = Network_Stats_Helper::get_list_all_plugins ();
		
		// $site_admins_list = '';
		
		foreach ( $blogs as $blog ) {
			switch_to_blog ( $blog->blog_id );
			$result = count_users ();
			
			/* http://codex.wordpress.org/Function_Reference/get_blog_details */
			$blog_details = get_blog_details ( $blog->blog_id );
			
			$option_privacy = get_option ( 'blog_public', '' );
			$option_theme = get_option ( 'template', '' );
			$option_admin_email = get_option ( 'admin_email', '' );
			
			// $blogs = $wpdb->get_results($wpdb->prepare("SELECT FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}'"));
			
			$apl = get_option ( 'active_plugins' );
			
			$activated_plugins = array ();
			foreach ( $apl as $p ) {
				if (isset ( $plugins [$p] )) {
					array_push ( $activated_plugins, $plugins [$p] );
				}
			}
			
			$count_active_plugins = count ( $activated_plugins );
			
			$site_type = $wpdb->get_var ( 'SELECT site_usage FROM ' . $ssw_table_name . ' WHERE blog_id = ' . $blog->blog_id );
			
			if (Network_Stats_Helper::is_plugin_network_activated ( SSW_PLUGIN_DIR )) {
				$ens_site_row = array (
						'blog_id' => $blog->blog_id,
						'blog_name' => $blog_details->blogname,
						'blog_url' => $blog_details->path,
						'privacy' => $option_privacy,
						'current_theme' => $option_theme,
						'admin_email' => $option_admin_email,
						'total_users' => $result ['total_users'],
						'active_plugins' => $count_active_plugins,
						'site_type' => $site_type 
				);
			} else {
				$ens_site_row = array (
						'blog_id' => $blog->blog_id,
						'blog_name' => $blog_details->blogname,
						'blog_url' => $blog_details->path,
						'privacy' => $option_privacy,
						'current_theme' => $option_theme,
						'admin_email' => $option_admin_email,
						'total_users' => $result ['total_users'],
						'site_type' => $count_active_plugins 
				);
			}
			
			$ens_site_data [] = $ens_site_row;
		}
		
		restore_current_blog ();
		
		$wpdb->query ( 'TRUNCATE table ' . $this->site_table );
		foreach ( $ens_site_data as $site_data ) {
			$wpdb->insert ( $this->site_table, $site_data );
		}
	}
	
	/**
	 * Print Site Stats from Database
	 *
	 * @since 0.1.0
	 */
	public function print_site_stats() {
		global $wpdb;
		
		echo '<H1>Site Stats</H1><br/>';
		
		/*
		 * echo "
		 * SELECT *
		 * FROM " . $this->site_table;
		 */
		
		if (Network_Stats_Helper::is_plugin_network_activated ( SSW_PLUGIN_DIR )) {
			$site_stats_query = "
				SELECT `blog_id`, `blog_name`, `blog_url`, `privacy`, `current_theme`, `admin_email`, `total_users`, `active_plugins`, `site_type`
				FROM " . $this->site_table;
		} else {
			$site_stats_query = "
				SELECT `blog_id`, `blog_name`, `blog_url`, `privacy`, `current_theme`, `admin_email`, `total_users`, `active_plugins`
				FROM " . $this->site_table;
		}
		
		$ens_site_data_in_db = $wpdb->get_results ( $site_stats_query );
		
		if ($ens_site_data_in_db) {
			foreach ( $ens_site_data_in_db as $ens_site_row ) {
				$ens_site_data [] = $ens_site_row;
			}
		} 

		else {
			echo 'There is no data to display';
			exit ();
		}
		
		echo '
				<table border="1">
				';
		
		echo '
				<tr>
					<td>Blog ID</td>
					<td>Blog Name</td>
					<td>Blog URL</td>
					<td>Privacy</td>
					<td>Current Theme</td>
					<td>Admin Email</td>
					<td>Total Users</td>
					<td>Active Plugins</td>';
		
		if (Network_Stats_Helper::is_plugin_network_activated ( SSW_PLUGIN_DIR )) {
			echo '
					<td>Site Type</td>';
		}
		
		echo '
				</tr>
				';
		
		foreach ( $ens_site_data as $site_data ) {
			echo '<tr>';
			foreach ( $site_data as $site_data_field ) {
				echo '<td>' . $site_data_field . '</td>';
			}
			echo '</tr>';
		}
		
		echo '
			</table>';
		
		echo '<br /><br /><br />
					<strong>Privacy: </strong><br />
					1 : I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers. (default) <br />
					0 : I would like to block search engines, but allow normal visitors. <br />
					-1: Visitors must have a login - anyone that is a registered user of Web Publishing @ NYU can gain access. <br />
					-2: Only registered users of this blogs can have access - anyone found under Users > All Users can have access. <br />
			    	-3: Only administrators can visit - good for testing purposes before making it live. <br />
			    ';
		if (Network_Stats_Helper::is_plugin_network_activated ( MSP_PLUGIN_DIR )) {
			echo '-1: Visitors must have a login - anyone that is a registered user of Web Publishing @ NYU can gain access. <br />
						-2: Only registered users of this blogs can have access - anyone found under Users > All Users can have access. <br />
					   	-3: Only administrators can visit - good for testing purposes before making it live. <br />
					';
		}
	}
}
