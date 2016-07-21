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
		$plugin_update_info = array();
		$plugin_update_needed = Network_Stats_Helper::plugins_update_check($plugin_update_info);
		
		$ns_plugin_row = self::get_plugin_stats_csvheaders();
		$ns_plugin_data [] = $ns_plugin_row;

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$active_on_network = Network_Stats_Helper::is_plugin_network_activated ( $plugin_file );
			$update_available = false;
			$new_version = '';
			if(isset($plugin_update_info[$plugin_file])) {
				$update_available = true;
				$new_version = $plugin_update_info[$plugin_file]->new_version;
				Network_Stats_Helper::write_log($plugin_update_info[$plugin_file]);
				$last_updated = '';
				/*
				if(isset($plugin_update_info[$plugin_file]->last_updated)) {
					$last_updated = $plugin_update_info[$plugin_file]->last_updated;
				}
				*/
			}
			

			$ns_plugin_row = array (
				'plugin_file' => $plugin_file,
				'name' => $plugin_data['Name'],
				//'title' => $plugin_data['Title'],
				'description' => $plugin_data['Description'],
				'author' => $plugin_data['Author'],
				'author_uri' => $plugin_data['AuthorURI'],
				'plugin_uri' => $plugin_data['PluginURI'],
				//'textdomain' => $plugin_data['TextDomain'],
				//'domainpath' => $plugin_data['DomainPath'],
				'network_only' => $plugin_data['Network'] ? 'yes':'no',
				'network_active' => $active_on_network ? 'yes':'no',
				'version' => $plugin_data['Version'],
				'new_version' => $new_version,
				'update_available' => $update_available ? 'yes' : '',
				//'last_updated' =>  $last_updated
			);
			
			$ns_plugin_data [] = $ns_plugin_row;
		}
		
		//$upload_dir = wp_upload_dir();
		//var_dump($upload_dir);
		//$report_dirname = $upload_dir['basedir'].'/'. NS_UPLOADS;
		$report_dirname = NS_REPORT_DIRNAME;
		//echo NS_REPORT_DIRNAME;
		// if ( ! file_exists( $report_dirname ) ) {
  //   		wp_mkdir_p( $report_dirname );
		// }
		$report_plugin_stats = $report_dirname . '/' . 'plugin-stats.csv';
		$file_plugin_stats = fopen($report_plugin_stats,"w");
		chmod($report_plugin_stats,NS_STATS_FILE_PERMISSION);

		foreach ($ns_plugin_data as $plugin_data) {
    		fputcsv($file_plugin_stats, $plugin_data);
		}

		fclose($file_plugin_stats);
	}


	private function get_plugin_stats_csvheaders() {
		$ns_plugin_row = array (
			'plugin_file' => 'plugin_file',
			'name' => 'plugin_name',
			//'title' => 'title',
			'description' => 'description',
			'author' => 'author',
			'author_uri' => 'author_uri',
			'plugin_uri' => 'plugin_uri',
			//'textdomain' => 'textdomain',
			//'domainpath' => 'domainpath',
			'network_only' => 'network_only',
			'network_active' => 'network_active',
			'version' => 'version',
			'new_version' => 'new_version',
			'update_available' => 'update_available',
			//'last_updated' => 'last_updated'
		);
		return $ns_plugin_row;
	}

}
