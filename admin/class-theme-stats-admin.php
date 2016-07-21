<?php

/**
 * The Theme-Stats specific functionality of the plugin.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.2.0
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 */

/**
 * The Theme-Stats specific functionality of the plugin.
 *
 * @package Network_Stats
 * @subpackage Network_Stats/admin
 * @author Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Theme_Stats_Admin {
	
	/**
	 * The name of the theme stats table
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the theme stats table
	 */
	private $table_name;
	
	/**
	 * The name of the theme stats table including prefix
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the theme stats table including prefix
	 */
	private $theme_table;
	
	
	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @var string $table_name	The table name for theme stats.
	 */
	public function __construct($table_name = NS_THEME_TABLE) {
		global $wpdb;
		$this->table_name = $table_name;
		$this->theme_table = $wpdb->base_prefix . $table_name;
	}
	
	/**
	 * Refresh the theme stats
	 *
	 * @since 0.1.0
	 */
	public function refresh_theme_stats($print = false) {
		global $wpdb;
		
		$themes_all = Network_Stats_Helper::get_list_all_themes ();
		
		$ns_theme_data = array ();
		$ns_theme_row = array ();
				
		$ns_theme_row = self::get_theme_stats_csvheaders();
		$ns_theme_data [] = $ns_theme_row;
		
		$themes_allowed_on_network = WP_Theme::get_allowed_on_network();

		$theme_update_info = array();
		$theme_update_needed = Network_Stats_Helper::themes_update_check($theme_update_info);
		
		foreach ( $themes_all as $theme) {
			$update_available = false;
			$new_version = '';
			if(isset($theme_update_info[$theme->get_template()])) {
				$update_available = true;
				$new_version = $theme_update_info[$theme->get_template()]['new_version'];
			}

			$allowed_on_network = $theme->is_allowed();
 
			$ns_theme_row = array (
				'template' => $theme->get_template(),
				'name' => $theme->get('Name'),
				'theme_uri' => $theme->get('ThemeURI'),
				'description' => $theme->get('Description'),
				'author' => $theme->get('Author'),
				'author_uri' => $theme->get('AuthorURI'),
				'version' => $theme->get('Version'),
				'status' => $theme->get('Status'),
				//'tags' => ,
				//'TextDomain' => $theme['TextDomain'],
				//'domain_path' => $theme['DomainPath'],
				'network_enabled' => $allowed_on_network ? 'yes' : 'no',
				'new_version' => $new_version,
				'update_available' => $update_available ? 'yes' : 'no',
			);
			
			$ns_theme_data [] = $ns_theme_row;
		}

		$report_dirname = NS_REPORT_DIRNAME;
		
		$report_theme_stats = $report_dirname . '/' . 'theme-stats.csv';
		chmod($report_theme_stats,NS_STATS_FILE_PERMISSION);

		$file_theme_stats = fopen($report_theme_stats,"w");

		foreach ($ns_theme_data as $theme_data) {
    		fputcsv($file_theme_stats, $theme_data);
		}

		fclose($file_theme_stats);
	}


	private function get_theme_stats_csvheaders() {
		$ns_theme_row = array (
			'template' => 'template',
			'name' => 'name',
			'theme_uri' => 'theme_uri',
			'description' => 'description',
			'author' => 'author',
			'author_uri' => 'author_uri',
			'version' => 'version',
			'status' => 'status',
			//'tags' => 'tags',
			//'text_domain' => 'text_domain',
			//'domain_path' => 'domain_path',
			'network_enabled' => 'network_enabled',
			'new_version' => 'new_version',
			'update_available' => 'update_available'
		);
		return $ns_theme_row;
	}
}
