<?php

/**
 * The Plugin-Stats specific functionality of the plugin.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.2.0
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 */

/**
 * The Plugin-Stats specific functionality of the plugin.
 *
 * @package Network_Stats
 * @subpackage Network_Stats/admin
 * @author Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Plugin_Stats_Admin {
	
	/**
	 * The name of the plugin stats table
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the plugin stats table
	 */
	private $table_name;
	
	/**
	 * The name of the plugin stats table including prefix
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the plugin stats table including prefix
	 */
	private $plugin_table;
	
	
	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @var string $table_name	The table name for plugin stats.
	 */
	public function __construct($table_name = NS_PLUGIN_TABLE) {
		global $wpdb;
		$this->table_name = $table_name;
		$this->plugin_table = $wpdb->base_prefix . $table_name;
	}
	
	/**
	 * Refresh the plugin stats
	 *
	 * @since 0.1.0
	 */
	public function refresh_plugin_stats() {
		global $wpdb;
		
		$all_plugins = Network_Stats_Helper::get_list_all_plugins ();
		
		$ns_plugin_data = array ();
		$ns_plugin_row = array ();
		
		/**
		 * To add Table headers:
		 * $ns_plugin_row = array("Plugin", "Number of Sites");
		 * $ns_plugin_data[] = $ns_plugin_row;
		 */
		
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$active_on_network = Network_Stats_Helper::is_plugin_network_activated ( $plugin_file );
			
			if ($active_on_network) {
				// We don't need to check any further for network active plugins
				$ns_plugin_row = array (
						'plugin' => $plugin_data ['Title'],
						'total_sites' => "Network Activated" 
				);
			} else {
				// Is this plugin Active on any blogs in this network?
				$active_on_blogs = Network_Stats_Helper::is_plugin_active_on_blogs ( $plugin_file );
				if (is_array ( $active_on_blogs )) {
					$count_blogs_plugin_is_active = count ( $active_on_blogs );
					$ns_plugin_row = array (
							'plugin' => $plugin_data ['Title'],
							'total_sites' => $count_blogs_plugin_is_active 
					);
					
					/** 
					 * @todo List all the sites the plugin is active on.
					 * Loop through the blog list, gather details and append them to the output string
					 * foreach ( $active_on_blogs as $blog_id ) {
					 * $blog_id = trim( $blog_id );
					 * if ( ! isset( $blog_id ) || $blog_id == '' ) {
					 * continue;
					 * }
					 *
					 * $blog_details = get_blog_details( $blog_id, true );
					 *
					 * if ( isset( $blog_details->siteurl ) && isset( $blog_details->blogname ) ) {
					 * $blog_url = $blog_details->siteurl;
					 * $blog_name = $blog_details->blogname;
					 *
					 * //$output .= '<li><nobr><a title="' . esc_attr( __( 'Manage plugins on ', 'npa' ) . $blog_name ) .'" href="'.esc_url( $blog_url ).'/wp-admin/plugins.php">' . esc_html( $blog_name ) . '</a></nobr></li>';
					 * }
					 *
					 * unset( $blog_details );
					 * }
					 */
				}
			}
			
			$ns_plugin_data [] = $ns_plugin_row;
		}
		
		/*
		$wpdb->query ( 'TRUNCATE table ' . $this->plugin_table );
		
		foreach ( $ns_plugin_data as $plugin_data ) {
			$wpdb->insert ( $this->plugin_table, $plugin_data );
		}
		*/

		echo '
				<table border="1">
				';
		
		echo '
				<tr>
					<td>Plugin</td>
					<td>Number of Sites</td>
				</tr>';
		
		foreach ($ns_plugin_data as $plugin_data) {
			echo '<tr>';
			foreach ($plugin_data as $plugin_data_field) {
				echo '<td>' . $plugin_data_field . '</td>';
			}
			echo '</tr>';
		}
		
		echo '
			</table>';
		
	}
	
	/**
	 * Print Plugin Stats from Database
	 *
	 * @since 0.1.0
	 */
	public function print_plugin_stats() {
		global $wpdb;

		echo '<H1>Plugin Stats</H1><br/>';
		
		$plugin_stats_query = "
				SELECT `plugin`, `total_sites`
				FROM " . $this->plugin_table;
		
		$ns_plugin_data_in_db = $wpdb->get_results ( $plugin_stats_query );
		
		if ($ns_plugin_data_in_db) {
			foreach ( $ns_plugin_data_in_db as $ns_plugin_row ) {
				$ns_plugin_data [] = $ns_plugin_row;
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
					<td>Plugin</td>
					<td>Number of Sites</td>
				</tr>';
		
		foreach ($ns_plugin_data as $plugin_data) {
			echo '<tr>';
			foreach ($plugin_data as $plugin_data_field) {
				echo '<td>' . $plugin_data_field . '</td>';
			}
			echo '</tr>';
		}
		
		echo '
			</table>';
		

	}
}
