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
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @var string $table_name	The table name for site stats.
	 */
	public function __construct($table_name = NS_SITE_TABLE) {
		global $wpdb;
		$this->table_name = $table_name;
		$this->site_table = $wpdb->base_prefix . $table_name;
	}
	
	/**
	 * Refresh the site stats
	 *
	 * @since 0.1.0
	 */
	public function refresh_site_stats($args = array()) {
		global $wpdb;
		
		//$blogs = $wpdb->get_results ( $wpdb->prepare ( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d", $wpdb->siteid ) );

		/**
		 *
		 * @todo Take care of spam, deleted and archived sites
		 *      
		 *       AND spam = '0'
		 *       AND deleted = '0'
		 *       AND archived = '0'
		 *       AND mature = '0'
		 */
		
		$blog_list = Network_Stats_Helper::get_network_blog_list( $args );
	
		Network_Stats_Helper::write_log(' Refresh Site Stats Args:  ' . print_r($args, $return = true) . "\n");

		$ns_site_data = array ();
		$ns_site_row = array ();
		$ns_plugin_data_per_site = array ();
		$ns_user_data_per_site = array ();

		/**
		 * To add Table headers:
		 * $ns_site_row = array("Blog ID", "Blog Name", "Blog URL", "Privacy", "Current Theme", "Admin Email", "Total Users", "Active Plugins", "Site Type");
		 * $ns_site_data[] = $ns_site_row;
		 */
		
		
		// $site_admins_list = '';
		/* Get all the site admins
		$users_query = new WP_User_Query( array(
					'role' => 'administrator',
					'orderby' => 'display_name'
			) );
			 
			$results = $users_query->get_results();
			foreach($results as $user)
			{
				$option_admin_email .= $user->user_email . ',';
			}
		*/

		$ns_site_row = self::get_site_stats_csvheaders();
		$ns_site_data [] = $ns_site_row;

		$ns_plugin_data_per_site_row = array('blog_id', 'plugin_file', 'plugin_name');
		$ns_plugin_data_per_site [] = $ns_plugin_data_per_site_row;

		$ns_user_data_per_site_row = array('blog_id', 'user_id', 'user_role', 'comments_count');
		$ns_user_data_per_site [] = $ns_user_data_per_site_row;
			
		$report_dirname = NS_REPORT_DIRNAME;
		
		$report_site_stats = $report_dirname . '/' . 'site-stats.csv';
		$report_plugin_stats_per_site = $report_dirname . '/' . 'plugin-stats-per-site.csv';
		$report_user_stats_per_site = $report_dirname . '/' . 'user-stats-per-site.csv';
		
		$mode = "a";
		if( $args['offset'] == 0 ) {
			$mode = "w";
		}

		$file_site_stats = fopen($report_site_stats, $mode);
		$file_plugin_stats_per_site = fopen($report_plugin_stats_per_site, $mode);
		$file_user_stats_per_site = fopen($report_user_stats_per_site, $mode);
		
		chmod($report_site_stats,0644);
		chmod($report_plugin_stats_per_site,0644);
		chmod($report_user_stats_per_site,0644);

		if( $args['offset'] == 0 ) {
			fputcsv($file_site_stats, $ns_site_row);
			fputcsv($file_plugin_stats_per_site, $ns_plugin_data_per_site_row);
			fputcsv($file_user_stats_per_site, $ns_user_data_per_site_row);
		}
		
    // Increase maximum execution time to prevent "Maximum execution time exceeded" error ##
    $memory_limit = Network_Stats_Helper::return_bytes( ini_get('memory_limit') ) * .75;
    $max_execution_time = Network_Stats_Helper::return_bytes( ini_get('max_execution_time') ) * .75;

		//ini_set( 'max_execution_time', -1 );
        //ini_set( 'memory_limit', -1 ); // looks like a bad idea ##
		
		// we need to disable caching while exporting because we export so much data that it could blow the memory cache
    // if we can't override the cache here, we'll have to clear it later...
    /*    
    if ( function_exists( 'override_function' ) ) {

        override_function('wp_cache_add', '$key, $data, $group="", $expire=0', '');
        override_function('wp_cache_set', '$key, $data, $group="", $expire=0', '');
        override_function('wp_cache_replace', '$key, $data, $group="", $expire=0', '');
        override_function('wp_cache_add_non_persistent_groups', '$key, $data, $group="", $expire=0', '');

    } elseif ( function_exists( 'runkit_function_redefine' ) ) {

        runkit_function_redefine('wp_cache_add', '$key, $data, $group="", $expire=0', '');
        runkit_function_redefine('wp_cache_set', '$key, $data, $group="", $expire=0', '');
        runkit_function_redefine('wp_cache_replace', '$key, $data, $group="", $expire=0', '');
        runkit_function_redefine('wp_cache_add_non_persistent_groups', '$key, $data, $group="", $expire=0', '');

    }
    */
   
		$time_start = microtime(true);	//In seconds 

		foreach ( $blog_list as $blog ) {
			//if($blog['blog_id'] == 1) {
			//	continue;
			//}
			switch_to_blog ( $blog['blog_id']);

			// check if we're hitting any Memory limits, if so flush them out ##
            // per http://wordpress.org/support/topic/how-to-exporting-a-lot-of-data-out-of-memory-issue?replies=2
			if ( memory_get_usage( true ) > $memory_limit ) {
                wp_cache_flush();
            }

			$result = count_users ();
			
			/** @var object blog details using https://codex.wordpress.org/Function_Reference/get_blog_details */
			$blog_details = get_blog_details ( $blog['blog_id'] );

			/** @var blog description using https://developer.wordpress.org/reference/functions/get_bloginfo/ */
			$blog_description = get_bloginfo ( 'description' );

			/** @var int blog privacy */
			$option_privacy = get_option ( 'blog_public', '' );

			/** @var string blog template */
			$current_theme = Network_Stats_Helper::get_active_theme_name( $blog['blog_id'] );
			//or = get_option ( 'current_theme', '' );

			/** @var string blog admin (who created the site) email */
			$option_admin_email = get_option ( 'admin_email', '' );
			
			// $blogs = $wpdb->get_results($wpdb->prepare("SELECT FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}'"));
			

			$active_plugins = Network_Stats_Helper::get_active_plugins($blog['blog_id']);
			
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start);

			foreach ($active_plugins as $plugin_file => $plugin_data_per_site) {

				/*
				 * @todo Add these fields for debugging $memory_limit, memory_get_usage( true ), $max_execution_time, $execution_time
				 */
				
				$ns_plugin_data_per_site_row = array($blog['blog_id'], $plugin_file, $plugin_data_per_site ['Name']);
				//$ns_plugin_data_per_site []= $ns_plugin_data_per_site_row;
				fputcsv($file_plugin_stats_per_site, $ns_plugin_data_per_site_row);
			}

			/** @var array Array of WP_User objects. */
			$all_users = get_users();			
			foreach ( $all_users as $user ) {
				
				//https://codex.wordpress.org/Function_Reference/get_comments
				$args = array(
					'user_id' => $user->ID, // use user_id
        	'count' => true //return only the count
				);
				$comments_count = get_comments($args);
			
				$roles = '';
				foreach ( $user->roles as $role ) {
					if( $roles != '') {
						$roles .= ',';	
					}
					$roles .= $role;
				}
				$ns_user_data_per_site_row = array($blog['blog_id'], $user->ID, $roles, $comments_count);
				//$ns_user_data_per_site[] = $ns_user_data_per_site_row; 
				fputcsv($file_user_stats_per_site, $ns_user_data_per_site_row);

			}

			/** @var integer total number of active plugins on the blog */
			$active_plugins_count = count ( $active_plugins );
			
			/** @var object count of comments */
			$comments_count = wp_count_comments();
			
			/** @var object count of posts - the properties of the object are the count of each post status of a post type. https://codex.wordpress.org/Function_Reference/wp_count_posts also refer to this https://codex.wordpress.org/Post_Status */
			//or $post_count = $blog_details->post_count;
			$count_posts = wp_count_posts();
			$count_posts_vars = get_object_vars($count_posts);
			$posts_count = 0;
			foreach ($count_posts_vars as $status => $count) {
				$posts_count += $count;
			}

			/** @var object count of pages - the properties of the object are the count of each post status of a post type. https://codex.wordpress.org/Function_Reference/wp_count_posts also refer to this https://codex.wordpress.org/Post_Status */
			$count_pages = wp_count_posts('page');
			$count_pages_vars = get_object_vars($count_pages);
			$pages_count = 0;
			foreach ($count_pages_vars as $status => $count) {
				$pages_count += $count;
			}			

			/** @var integer count of attachments http://codex.wordpress.org/Function_Reference/wp_count_attachments 
			or http://wpsnipp.com/index.php/functions-php/count-total-number-of-jpg-gif-png-images-in-media-library/*/
			$attachments = wp_count_attachments();
			$attachments_vars = get_object_vars($attachments);
			$attachments_count = 0;
			foreach ($attachments_vars as $mime_type => $count) {
				$attachments_count += $count;
			}

			/*
			$themes_allowed = WP_Theme::get_allowed();
			$themes_allowed_count = count($themes_allowed);
			*/
			
			//
			//https://developer.wordpress.org/reference/classes/wp_theme/
			//
			$themes_allowed_on_site = WP_Theme::get_allowed_on_site();
			$themes_allowed_on_site_count = count($themes_allowed_on_site);


			$ns_site_row = array (
				'blog_id' => $blog['blog_id'],
				'blog_name' => $blog_details->blogname,
				'blog_descripiton' => $blog_description,
				'siteurl' => $blog_details->siteurl,
				'blog_url' => $blog_details->path,
				'privacy' => $option_privacy,
				'admin_email' => $option_admin_email,
				'users_count' => $result ['total_users'],
				'active_plugins_count' => $active_plugins_count,
				'db_version' => get_option('db_version'),

				'current_theme' => $current_theme,
				'themes_allowed_per_site' => $themes_allowed_on_site_count,

				'posts_count' => $posts_count,
				'posts_published' => $count_posts->publish,
				'posts_future' => $count_posts->future,
				'posts_draft' => $count_posts->draft,
				'posts_pending' => $count_posts->pending,
				'posts_private' => $count_posts->private,
				'posts_trash' => $count_posts->trash,
				//'posts_auto_draft' => $count_posts['auto-draft'],
				'posts_draft' => $count_posts->draft,
				
				'pages_count' => $pages_count,
				'pages_published' => $count_pages->publish,
				'pages_future' => $count_pages->future,
				'pages_draft' => $count_pages->draft,
				'pages_pending' => $count_pages->pending,
				'pages_private' => $count_pages->private,
				'pages_trash' => $count_pages->trash,
				//'pages_auto_draft' => $count_pages['auto-draft'],

				'pages_draft' => $count_pages->draft,
				'registered' => $blog_details->registered,
				'last_updated' => $blog_details->last_updated,
				'comments_count' => $comments_count->total_comments,
				'comments_approved' => $comments_count->approved,
				'comments_trash' => $comments_count->trash,
				'comments_spam' => $comments_count->spam,
				'comments_moderated' => $comments_count->moderated,
				'public' => $blog_details->public,
				'archived' => $blog_details->archived,
				'mature' => $blog_details->mature,
				'spam' => $blog_details->spam,
				'deleted' => $blog_details->deleted,
				'attachments_count' => $attachments_count
			);
			//var_dump($ns_site_row);

			//$ns_site_data [] = $ns_site_row;
			fputcsv($file_site_stats, $ns_site_row);
			//Have to call everytime switch_to_blog() is called - https://codex.wordpress.org/Function_Reference/switch_to_blog
			restore_current_blog();
		}
		
		
		/*
		$report_dirname = NS_REPORT_DIRNAME;
		
		$report_site_stats = $report_dirname . '/' . 'site-stats.csv';
		chmod($report_site_stats,0600);

		$file_site_stats = fopen($report_site_stats,"w");

		foreach ($ns_site_data as $site_data) {
    		fputcsv($file_site_stats, $site_data);
		}

		$report_plugin_stats_per_site = $report_dirname . '/' . 'plugin-stats-per-site.csv';
		chmod($report_plugin_stats_per_site,0600);

		$file_plugin_stats_per_site = fopen($report_plugin_stats_per_site,"w");

		foreach ($ns_plugin_data_per_site as $plugin_data_per_site) {
    		fputcsv($file_plugin_stats_per_site, $plugin_data_per_site);
		}

		$report_user_stats_per_site = $report_dirname . '/' . 'user-stats-per-site.csv';
		chmod($report_user_stats,0600);

		$file_user_stats_per_site = fopen($report_user_stats_per_site,"w");

		foreach ($ns_user_data_per_site as $user_data_per_site) {
    		fputcsv($file_user_stats_per_site, $user_data_per_site);
		}
		*/


		fclose($file_site_stats);
		fclose($file_plugin_stats_per_site);
		fclose($file_user_stats_per_site);

		/*
		$wpdb->query ( 'TRUNCATE table ' . $this->site_table );
		foreach ( $ns_site_data as $site_data ) {
			$wpdb->insert ( $this->site_table, $site_data );
		}
		*/
	}
	
	private function get_site_stats_csvheaders() {
		$ns_site_row = array (
			'blog_id' => 'blog_id',
			'blog_name' => 'blog_name',
			'blog_descripiton' => 'blog_descripiton',
			'siteurl' => 'siteurl',
			'blog_url' => 'blog_url',
			'privacy' => 'privacy',
			'admin_email' => 'admin_email',
			'users_count' => 'users_count',
			'active_plugins_count' => 'active_plugins_count',
			'db_version' => 'db_version',
						
			'current_theme' => 'current_theme',
			'themes_allowed_per_site' => 'themes_allowed_per_site',

			'posts_count' => 'posts_count',
			'posts_published' => 'posts_published',
			'posts_future' => 'posts_future',
			'posts_draft' => 'posts_draft',
			'posts_pending' => 'posts_pending',
			'posts_private' => 'posts_private',
			'posts_trash' => 'posts_trash',
			//'posts_auto_draft' => 'posts_auto_draft',
			'posts_draft' => 'posts_draft',

			'pages_count' => 'pages_count',
			'pages_published' => 'pages_published',
			'pages_future' => 'pages_future',
			'pages_draft' => 'pages_draft',
			'pages_pending' => 'pages_pending',
			'pages_private' => 'pages_private',
			'pages_trash' => 'pages_trash',
			//'pages_auto_draft' => 'pages_auto_draft',
			'pages_draft' => 'pages_draft',

			'registered' => 'registered',
			'last_updated' => 'last_updated',
			
			'comments_count' => 'comments_count',
			'comments_approved' => 'comments_approved',
			'comments_trash' => 'comments_trash',
			'comments_spam' => 'comments_spam',
			'comments_moderated' => 'comments_moderated',
			
			'public' => 'public',
			'archived' => 'archived',
			'mature' => 'mature',
			'spam' => 'spam',
			'deleted' => 'deleted',
			'attachments_count' => 'attachments_count'
		);
		return $ns_site_row;
	}
	/**
	 * Print Site Stats
	 *
	 * @since 0.1.0
	 */
	public function print_site_stats($ns_site_data) {
		global $wpdb;
		
		echo '<H1>Site Stats</H1><br/>';
		
		/*
		 * echo "
		 * SELECT *
		 * FROM " . $this->site_table;
		 */
		
		/*		
		$site_stats_query = "
			SELECT `blog_id`, `blog_name`, `blog_url`, `privacy`, `current_theme`, `admin_email`, `total_users`, `active_plugins`
			FROM " . $this->site_table;
	
		$ns_site_data_in_db = $wpdb->get_results ( $site_stats_query );
		
		if ($ns_site_data_in_db) {
			foreach ( $ns_site_data_in_db as $ns_site_row ) {
				$ns_site_data [] = $ns_site_row;
			}
		} 

		else {
			echo 'There is no data to display';
			exit ();
		}
		*/
		echo '
				<table border="1">
				';

		foreach ( $ns_site_data as $site_data ) {
			echo '<tr>';
			foreach ( $site_data as $site_data_field ) {
				echo '<td>' . $site_data_field . '</td>';
			}
			echo '</tr>';
		}
		
		echo '
			</table>';
		/*		
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
		*/
	}
}
