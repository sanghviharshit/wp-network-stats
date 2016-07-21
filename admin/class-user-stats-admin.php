<?php

/**
 * The User-Stats specific functionality of the plugin.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.2.0
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/admin
 */

/**
 * The User-Stats specific functionality of the plugin.
 *
 * @package Network_Stats
 * @subpackage Network_Stats/admin
 * @author Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class User_Stats_Admin {
	
	/**
	 * The name of the user stats table
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the user stats table
	 */
	private $table_name;
	
	/**
	 * The name of the user stats table including prefix
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $table_name The name of the user stats table including prefix
	 */
	private $user_table;
	
	
	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @var string $table_name	The table name for user stats.
	 */
	public function __construct($table_name = NS_USER_TABLE) {
		global $wpdb;
		$this->table_name = $table_name;
		$this->user_table = $wpdb->base_prefix . $table_name;
	}
	
	/**
	 * Refresh the user stats
	 *
	 * @since 0.1.0
	 */
	public function refresh_user_stats($print = false) {
		global $wpdb;
		
		$users = Network_Stats_Helper::get_list_all_users ();
		
		$ns_user_data = array ();
		$ns_user_row = array ();
				
		$ns_user_row = self::get_user_stats_csvheaders();
		$ns_user_data [] = $ns_user_row;

		foreach ( $users as $user) {
			// For fields in usermeta table - https://codex.wordpress.org/Function_Reference/get_userdata
			$userdata = get_userdata( $user->ID );

			$ns_user_row = array (
				'ID' => $user->ID,
				'user_login' => $user->user_login,
				'user_email' => $user->user_email,
				'first_name' => $userdata->first_name,
				'last_name' => $userdata->last_name,
				'display_name' => $user->display_name,
				'user_registered' => $user->user_registered,
				'user_activated' => $user->user_activation_key ? 'no' : 'yes',
				'user_url' => $user->user_url,
				'spam' => $user->spam,
				'deleted' => $user->deleted
			);
			$ns_user_data [] = $ns_user_row;
		}

		$report_dirname = NS_REPORT_DIRNAME;
		
		$report_user_stats = $report_dirname . '/' . 'user-stats.csv';
		$file_user_stats = fopen($report_user_stats,"w");
		chmod($report_user_stats,NS_STATS_FILE_PERMISSION);
		foreach ($ns_user_data as $user_data) {
    		fputcsv($file_user_stats, $user_data);
		}

		fclose($file_user_stats);

		if($print) {
			self::print_user_stats($ns_user_data);
		}		
	}

	private function get_user_stats_csvheaders() {
		$ns_user_row = array (
			'ID' => 'ID',
			'user_login' => 'user_login',
			'user_email' => 'user_email',
			'first_name' => 'first_name',
			'last_name' => 'last_name',
			'display_name' => 'display_name',
			'user_registered' => 'user_registered',
			'user_activated' => 'user_activated',
			'user_url' => 'user_url',
			'spam' => 'spam',
			'deleted' => 'deleted'
		);
		return $ns_user_row;
	}
}
