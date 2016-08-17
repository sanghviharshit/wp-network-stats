<?php

/**
 * The file that defines the core plugin helper class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://github.com/sanghviharshit/
 * @since      0.1.0
 *
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 */

/**
 * The core plugin helper class.
 *
 * This is used to define helper functions.
 *
 * @since      0.1.0
 * @package    Network_Stats
 * @subpackage Network_Stats/includes
 * @author     Harshit Sanghvi <sanghvi.harshit@gmail.com>
 */
class Network_Stats_Helper {

	
	/**
	 * Use transient cache
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      bool    use_transient    use transient cache
	 */
	private static $use_transient = false;
	
	
	/**
	 * Initialize the class.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		
	}
		
	/**
	 * Get the database prefix
	 *
	 * @since    0.1.0
	 * @access   public
	 * @var		int		$blog_id	Blog ID.
	 * @return    string 	the blog prefix.
	 */
	public static function get_blog_prefix( $blog_id=null ) {
		global $wpdb;
	
		if ( null === $blog_id ) {
			$blog_id = $wpdb->blogid;
		}
		$blog_id = (int) $blog_id;
	
		if ( defined( 'MULTISITE' ) && ( 0 == $blog_id || 1 == $blog_id ) ) {
			return $wpdb->base_prefix;
		} else {
			return $wpdb->base_prefix . $blog_id . '_';
		}
	}
	
	/**
	 * Get the list of blogs
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int		$blog_id	Blog ID.
	 * @return    array 	list of the blogs.
	 * 			e.g.
	  			[blog_id] => 13
				[site_id] => 1
				[domain] => localhost
				[path] => /wptest/test12/
				[registered] => 2016-05-20 02:03:55
				[last_updated] => 2016-05-20 02:03:55
				[public] => 1
				[archived] => 0
				[mature] => 0
				[spam] => 0
				[deleted] => 0
				[lang_id] => 0
	 */
	public static function get_network_blog_list($args = array()) {
		global $wpdb;
		$blog_list = array();
	
		$defaults = array(
            'network_id' => $wpdb->siteid,
            'public'     => null,
            'archived'   => null,
            'mature'     => null,
            'spam'       => null,
            'deleted'    => null,
            'limit'      => 10000,
            'offset'     => 0,
        );
 
    $args = wp_parse_args( $args, $defaults );

		if ( function_exists( 'wp_get_sites' ) && function_exists( 'wp_is_large_network' ) ) {
			// If wp_is_large_network() returns TRUE, wp_get_sites() will return an empty array.
			// By default wp_is_large_network() returns TRUE if there are 10,000 or more sites or users in your network.
			// This can be filtered using the wp_is_large_network filter.
			if ( ! wp_is_large_network( 'sites' ) ) {
				$blog_list = wp_get_sites( $args );
			}
	
		} else {
			// Fetch the list from the transient cache if available
			$blog_list = get_site_transient( 'ns_blog_list' );
			if ( self::$use_transient !== true || $blog_list === false ) {
				$query = "SELECT * FROM $wpdb->blogs WHERE 1=1 ";
		 
		    if ( isset( $args['network_id'] ) && ( is_array( $args['network_id'] ) || is_numeric( $args['network_id'] ) ) ) {
		        $network_ids = implode( ',', wp_parse_id_list( $args['network_id'] ) );
		        $query .= "AND site_id IN ($network_ids) ";
		    }
		 
		    if ( isset( $args['public'] ) )
		        $query .= $wpdb->prepare( "AND public = %d ", $args['public'] );
		 
		    if ( isset( $args['archived'] ) )
		        $query .= $wpdb->prepare( "AND archived = %d ", $args['archived'] );
		 
		    if ( isset( $args['mature'] ) )
		        $query .= $wpdb->prepare( "AND mature = %d ", $args['mature'] );
		 
		    if ( isset( $args['spam'] ) )
		        $query .= $wpdb->prepare( "AND spam = %d ", $args['spam'] );
		 
		    if ( isset( $args['deleted'] ) )
		        $query .= $wpdb->prepare( "AND deleted = %d ", $args['deleted'] );
		 
		    if ( isset( $args['limit'] ) && $args['limit'] ) {
		        if ( isset( $args['offset'] ) && $args['offset'] )
		            $query .= $wpdb->prepare( "LIMIT %d , %d ", $args['offset'], $args['limit'] );
		        else
		            $query .= $wpdb->prepare( "LIMIT %d ", $args['limit'] );
		    }
		 
		    $blog_list = $wpdb->get_results( $query, ARRAY_A );
		 
		 		if(self::$use_transient) {
					// Store for one hour
					set_transient( 'ns_blog_list', $blog_list, 3600 );
				}
			}
		}
	
		//error_log( print_r( $blog_list, true ) );
		return $blog_list;
	}
	
	/////////////////////////////////////////////////////
	//The following functions are plugin-stats helpers //
	/////////////////////////////////////////////////////
	
	/** 
	 * Get list of all plugins
	 * 
	 * @since	0.1.0
	 * @access	public
	 * @return array Key is the plugin file path and the value is an array of the plugin data.
	 * 			e.g. hello-dolly/hello.php => Plugin Data
	 */
	public static function get_list_all_plugins() {
		// Get List of all plugins using get_plugins()
		if (! function_exists ( 'get_plugins' )) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		return get_plugins();
	}
	
	public static function get_list_network_active_plugins() {
		$list_network_active_plugins = get_site_option( 'active_sitewide_plugins');
		return $list_network_active_plugins;
	}

	/**
	 * Check if plugin is network activated
	 *
	 * @since	0.1.0
	 * @access	public
	 * @return	bool
	 */
	public static function is_plugin_network_activated($plugin) {
		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		return is_plugin_active_for_network($plugin);
	}
	
	/**
	 * Determine if the given plugin is active on a list of blogs 
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		string		$plugin_file	plugin file path.
	 * @return    array 	list of the blog IDs.
	 */
	public static function is_plugin_active_on_blogs( $plugin_file ) {
		// Get the list of blogs
		$blog_list = self::get_network_blog_list( );
	
		if ( isset( $blog_list ) && $blog_list != false ) {
			// Fetch the list from the transient cache if available
			$ns_active_plugins = get_site_transient( 'ns_active_plugins' );
			if ( ! is_array( $ns_active_plugins ) ) {
				$ns_active_plugins = array();
			}
			$transient_name = self::get_transient_friendly_name( $plugin_file );
	
			if ( self::$use_transient !== true || ! array_key_exists( $transient_name, $ns_active_plugins ) ) {
				// We're either not using or don't have the transient index
				$active_on = array();
	
				// Gather the list of blogs this plugin is active on
				foreach ( $blog_list as $blog ) {
					// If the plugin is active here then add it to the list
					if ( self::is_plugin_active( $blog['blog_id'], $plugin_file ) ) {
						array_push( $active_on, $blog['blog_id'] );
					}
				}
	
				if(self::$use_transient) {
	
					// Store our list of blogs
					$ns_active_plugins[$transient_name] = $active_on;
		

					// Store for one hour
					set_site_transient( 'ns_active_plugins', $ns_active_plugins, 3600 );
				}
				return $active_on;
	
			} else {
				// The transient index is available, return it.
				$active_on = $ns_active_plugins[$transient_name];
	
				return $active_on;
			}
		}
	
		return false;
	}
	
	/**
	 * Given a blog id and plugin path, determine if that plugin is active.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		Blog ID.
	 * @param		string		$plugin_file	plugin file path.
	 * @return    	bool
	 */
	public static function is_plugin_active( $blog_id, $plugin_file ) {
		// Get the active plugins for this blog_id
		$plugins_active_here = self::get_active_plugins( $blog_id );
	
		// Is this plugin listed in the active blogs?
		if ( isset( $plugins_active_here ) && strpos( $plugins_active_here, $plugin_file ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get the list of active plugins for a single blog
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		Blog ID.
	 * @return    	string
	 */
	public static function get_active_plugins( $blog_id ) {
		global $wpdb;

		/* Get List of all plugins */
		$all_plugins = Network_Stats_Helper::get_list_all_plugins ();

		//$blog_prefix = self::get_blog_prefix( $blog_id );
	
		$active_plugins_list = get_option ( 'active_plugins' );
			
		$active_plugins = array ();
		foreach ( $active_plugins_list as $plugin_file ) {
			if (isset ( $all_plugins [$plugin_file] )) {
				$active_plugins[$plugin_file] = $all_plugins[$plugin_file];
			}
		}
				
		return $active_plugins;
	}
	
	/////////////////////////////////////////////////////
	// The following functions are theme-stats helpers //
	/////////////////////////////////////////////////////
	 
	/** 
	 * Get list of all themes
	 * 
	 * @since	0.1.0
	 * @access	public
	 * @param 		array 			The search arguments. Optional. This array can have the key/value pairs below. Default: array( 'errors' => false , 'allowed' => null, 'blog_id' => 0 )
	 * @return 		array 			all themes. Defaults to false.
	 */
	public static function get_list_all_themes($args = array()) {
		// Get List of all themes using wp_get_themes()
		// or WP_Theme::get_allowed() - Returns an array of theme names that are allowed on the site or network. 
		// The $blog_id defaults to current blog. This method calls both get_allowed_on_network() and get_allowed_on_site( $blog_id ).
		$themes_all = wp_get_themes($args);
		//$themes_all = WP_Theme::get_allowed();

		return $themes_all;
	}



	/**
	 * Determine if the given theme is active on a list of blogs
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		string			$theme		Theme name.
	 * @param 		object 			$theme_key	Theme object
	 * @return    	array 			$active_on 	List of blogs
	 */
	public static function is_theme_active_on_blogs( $theme, $theme_key ) {
		// Get the list of blogs
		$blog_list = self::get_network_blog_list( );
	
		if ( isset( $blog_list ) && $blog_list != false ) {
			// Fetch the list from the transient cache if available
			$ns_active_themes = get_site_transient( 'ns_active_themes' );
			if ( ! is_array( $ns_active_themes ) ) {
				$ns_active_themes = array();
			}
			$transient_name = self::get_transient_friendly_name( $theme_key );
	
			if ( self::$use_transient !== true || ! array_key_exists( $transient_name, $ns_active_themes ) ) {
				// We're either not using or don't have the transient index
				$active_on = array();
	
				// Gather the list of blogs this theme is active on
				foreach ( $blog_list as $blog ) {
					// If the theme is active here then add it to the list
					if ( self::is_theme_active( $blog['blog_id'], $theme_key ) ) {
						array_push( $active_on, $blog['blog_id'] );
					}
				}
	
				// Store our list of blogs
				$ns_active_themes[$transient_name] = $active_on;
	
				// Store for one hour
				set_site_transient( 'ns_active_themes', $ns_active_themes, 3600 );
	
				return $active_on;
	
			} else {
				// The transient index is available, return it.
				$active_on = $ns_active_themes[$transient_name];
	
				return $active_on;
			}
		}
	}
	
	/**
	 * Given a blog id and theme object, determine if that theme is used on a this blog.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		blog id.
	 * @param 		object 			$theme_key	Theme object.
	 * @return    	bool
	 */
	public static function is_theme_active( $blog_id, $theme_key ) {
		// Get the active theme for this blog_id
		$active_theme = self::get_active_theme( $blog_id );
	
		// Is this theme listed in the active blogs?
		if ( isset( $active_theme ) && ( $active_theme == $theme_key ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get the active theme for a single blog
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		blog id.
	 * @return 		object 		$active_theme	Theme object.
	 */
	public static function get_active_theme( $blog_id ) {
		global $wpdb;
	
		$blog_prefix = self::get_blog_prefix( $blog_id );
	
		//$active_theme = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'stylesheet'" );
		$active_theme = get_option ( 'stylesheet' );
		
		return $active_theme;
	}
	
	/**
	 * Get the active theme name for a single blog
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		blog id.
	 * @return 		object 		$active_theme	Theme object.
	 */
	public static function get_active_theme_name( $blog_id ) {

		return wp_get_theme();
		/*
      global $wpdb;

      $blog_prefix = self::get_blog_prefix( $blog_id );

      // Determine parent-child theme relationships when possible
      if ( function_exists( 'wp_get_theme' ) ) {
          $template = get_option ( 'template' );
          	// = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'template'" );
          $stylesheet = get_option ( 'stylesheet' );
          	// = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'stylesheet'" );

          if ( $template !== $stylesheet ) {
              // The active theme is a child theme
              $template = wp_get_theme( $template );
              $stylesheet = wp_get_theme( $stylesheet );

              $active_theme = $stylesheet['Name'] . ' (' . sprintf( __( 'child of %s', 'npa'), $template['Name'] ) . ')';

          } else {
              $active_theme = get_option ( 'current_theme' );
              // = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'current_theme'" );
          }

      } else {
          $active_theme = get_option ( 'current_theme' );
          // = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'current_theme'" );
      }


      return $active_theme;
      */
  }
	
	/**
	 * Get the active theme for a single blog
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		blog id.
	 * @return 		object 		$active_theme	Theme object.
	 */
	public static function get_theme_link( $blog_id, $display='blog_name' ) {
	  $output = '';

	  $blog_details = get_blog_details( $blog_id, true );

	  if ( isset( $blog_details->siteurl ) && isset( $blog_details->blogname ) ) {
	      $blog_url  = $blog_details->siteurl;
	      $blog_name = $blog_details->blogname;

	      $output .= '<a title="' . esc_attr( sprintf( __( 'Manage themes on %s', 'npa' ), $blog_name ) ) .'" href="'. esc_url( $blog_url ).'/wp-admin/themes.php">';
	      if ( $display == 'blog' ) {
	          // Show the blog name
	          $output .= esc_html( $blog_name ) . '</a>';
	      } else {
	          // Show the theme name
	          $output .= self::get_active_theme_name( $blog_id ) . '</a>';
	      }
	  }

	  unset( $blog_details );

	  return $output;
	}



	/** 
	 * Get list of all users in multisite network
	 * 
	 * @since	0.1.0
	 * @access	public
	 * @return array
	 */
	public static function get_list_all_users() {
		// Get List of all users
		global $wpdb;
		$users_all = $wpdb->get_results("SELECT * FROM $wpdb->users");
		return $users_all;
	}

	public static function get_count_all_users() {
		//Get count of all WordPress Users
		global $wpdb;
		$count_users = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
		return $count_users;
	}

	///////////////////////////////////////////////////
	// The following are transient helper functions. //
	///////////////////////////////////////////////////

	/**
	 * Get transient friendly name
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		string			$file_name		file name.
	 * @return 		string
	 */
	public static function get_transient_friendly_name( $file_name ) {
		$transient_name = substr( $file_name, 0, strpos( $file_name, '/' ) );
		if ( $transient_name == false ) {
			$transient_name = $file_name;
		}
		if ( strlen( $transient_name ) >= 45 ) {
			$transient_name = substr( $transient_name, 0, 44 );
		}
		return esc_sql( $transient_name );
	}
	
	/**
	 * Clear plugin transient
	 *
	 * @since    0.1.0
	 * @access   public
	 */
	public function clear_plugin_transient( $plugin, $network_deactivating ) {
		delete_site_transient( 'ns_active_plugins' );
		return;
	}
	
	/**
	 * Clear theme transient
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		string			$file_name		file name.
	 * @return 		string
	 */
	public function clear_theme_transient( $new_name, $new_theme ) {
		delete_site_transient( 'ns_active_themes' );
		return;
	}
	

	/**
	 * This is run by the cron. The update check checks the core always, the
	 * plugins and themes if asked. If updates found email notification sent.
	 * @todo update description as needed
	 * @return void
	 */
	public static function do_update_check(&$wp_update_info) {

		$core_update_info = array();
		$plugin_update_info = array();
		$theme_update_info = array();

		$core_updated = self::core_update_check( $core_update_info ); // check the WP core for updates
		$plugins_updated = self::plugins_update_check( $plugin_update_info ); // check for plugin updates	
		$themes_updated = self::themes_update_check( $theme_update_info ); // check for theme updates
		/*
			$wp_update_info =['core'] = $core_update_info;
			$wp_update_info['plugins'] = $plugin_update_info;
			$wp_update_info['themes'] = $theme_update_info;
		*/
		$wp_update_info = array(
				'core' => $core_update_info,
				'plugins' => $plugin_update_info,
				'themes' => $theme_update_info
			);

		if ( $core_updated || $plugins_updated || $themes_updated ) { // Did anything come back as need updating?
			return true;
		}
		return false;
	}


	/**
	 * Checks to see if any WP core updates
	 *
	 * @param string $message holds message to be sent via notification
	 *
	 * @return bool
	 */
	public static function core_update_check( &$core_update_info ) {
		global $wp_version;
		do_action( "wp_version_check" ); // force WP to check its core for updates
		$update_core = get_site_transient( "update_core" ); // get information of updates
		if ( 'upgrade' == $update_core->updates[0]->response ) { // is WP core update available?
			require_once( ABSPATH . WPINC . '/version.php' ); // Including this because some plugins can mess with the real version stored in the DB.
			$new_core_ver = $update_core->updates[0]->current; // The new WP core version
			$old_core_ver = $wp_version; // the old WP core version
			
			$core_update_info['new_version'] = $new_core_ver;
			$core_update_info['old_version'] = $old_core_ver;
			
			return true; // we have updates so return true
		}
		return false; // no updates return false
	}


	/**
	 * Check to see if any plugin updates.
	 *
	 * @param string $plugin_update_info     holds information about plugins that need update
	 * 
	 * @return bool
	 */
	public static function plugins_update_check( &$plugin_update_info ) {
		do_action( "wp_update_plugins" ); // force WP to check plugins for updates
		$update_plugins = get_site_transient( 'update_plugins' ); // get information of updates
		if ( !empty( $update_plugins->response ) ) { // any plugin updates available?
			$plugin_update_info = $update_plugins->response; // plugins that need updating
			if ( count( $plugin_update_info ) >= 1 ) { // any plugins need updating after all the filtering gone on above?
				return true; // we have plugin updates return true
			}
		}
		return false; // No plugin updates so return false
	}

	public static function get_plugin_repository_info($plugin_file = '') {
		
		$plugin_file_pieces = explode("/", $plugin_file);
		$plugin_slug = $plugin_file_pieces[0];

		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // Required for plugin API
		$info = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'sections' => false )  ) ); // get repository plugin info

		return $info;
						
	}

	/**
	 * Check to see if any theme updates.
	 *
	 * @param string $theme_update_info     holds information about themes that need update
	 *
	 * @return bool
	 */
	public static function themes_update_check( &$theme_update_info ) {
		do_action( "wp_update_themes" ); // force WP to check for theme updates
		$update_themes = get_site_transient( 'update_themes' ); // get information of updates
		if ( !empty( $update_themes->response ) ) { // any theme updates available?
			$themes_need_update = $update_themes->response; // themes that need updating
			
			if ( count( $themes_need_update ) >= 1 ) { // any themes need updating after all the filtering gone on above?
				/*
				foreach ( $themes_need_update as $key => $data ) { // loop through the themes that need updating
					//$theme_info = wp_get_theme( $key ); // get theme info
					//$theme_update_info[$key]['data'] = $data;
					//$theme_update_info[$key]['info'] = $theme_info;
				}
				*/
				return true; // we have theme updates return true
			}
		}
		return false; // No theme updates so return false
	}


	/**
	 * Filter array by value
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param array $array
	 * @param int $index
	 * @param object $value
	 * @return array
	 */
	public static function filter_by_value( $array, $index, $value ) {
		$newarray = array();
		if ( is_array( $array ) && count( $array ) > 0 ) {
			foreach ( array_keys( $array ) as $key ) {
				$temp[$key] = $array[$key][$index];
	
				if ( $temp[$key] == $value ) {
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	}

    /**
     * Return Byte count of $val
     *
     * @link        http://wordpress.org/support/topic/how-to-exporting-a-lot-of-data-out-of-memory-issue?replies=2
     * @since       0.9.6
     */
    public static function return_bytes( $val )
    {

        $val = trim( $val );
        $last = strtolower($val[strlen($val)-1]);
        switch( $last ) {

            // The 'G' modifier is available since PHP 5.1.0
            case 'g':

                $val *= 1024;

            case 'm':

                $val *= 1024;

            case 'k':

                $val *= 1024;

        }

        return $val;
    }

    public static function validate_email($email) {
    	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			  //echo "This ($email_a) email address is considered valid.";
				return true;  
			} else {
				return false;
			}
    }

    public static function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
