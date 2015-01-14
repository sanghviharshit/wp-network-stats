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
	 */
	public static function get_network_blog_list( ) {
		global $wpdb;
		$blog_list = array();
	
		$args = array(
				'limit'  => 10000 // use the wp_is_large_network upper limit
		);
	
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
			if ( self::use_transient !== true || $blog_list === false ) {
				$blog_list = $wpdb->get_results( "SELECT blog_id, domain FROM " . $wpdb->base_prefix . "blogs", ARRAY_A );
	
				// Store for one hour
				set_transient( 'ns_blog_list', $blog_list, 3600 );
			}
		}
	
		//error_log( print_r( $blog_list, true ) );
		return $blog_list;
	}
	
	/** 
	 * The following functions are plugin-stats helpers
	 * 
	 */
	
	/** 
	 * Get list of all plugins
	 * 
	 * @since	0.1.0
	 * @access	public
	 * @return array
	 */
	public static function get_list_all_plugins() {
		// Get List of all plugins using get_plugins()
		if (! function_exists ( 'is_plugin_active_for_network' ) || ! function_exists ( 'get_plugins' )) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		return get_plugins();
	}
	
	/**
	 * Check if plugin is network activated
	 *
	 * @since	0.1.0
	 * @access	public
	 * @return	bool
	 */
	public static function is_plugin_network_activated($plugin) {
	
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
	
				// Store our list of blogs
				$ns_active_plugins[$transient_name] = $active_on;
	
				// Store for one hour
				set_site_transient( 'ns_active_plugins', $ns_active_plugins, 3600 );
	
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
	
		$blog_prefix = self::get_blog_prefix( $blog_id );
	
		$active_plugins = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'active_plugins'" );
	
		return $active_plugins;
	}
	
	/**
	 * The following functions are theme-stats helpers
	 *
	 */
	
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
	
			if ( self::use_transient !== true || ! array_key_exists( $transient_name, $ns_active_themes ) ) {
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
	
		$active_theme = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'stylesheet'" );
	
		return $active_theme;
	}
	
	/**
	 * Get the active theme for a single blog
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param		int			$blog_id		blog id.
	 * @return 		object 		$active_theme	Theme object.
	 */
	public static function get_active_theme_name( $blog_id ) {
		global $wpdb;
	
		$blog_prefix = self::get_blog_prefix( $blog_id );
	
		// Determine parent-child theme relationships when possible
		if ( function_exists( 'wp_get_theme' ) ) {
			$template = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'template'" );
			$stylesheet = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'stylesheet'" );
	
			if ( $template !== $stylesheet ) {
				// The active theme is a child theme
				$template = wp_get_theme( $template );
				$stylesheet = wp_get_theme( $stylesheet );
	
				$active_theme = $stylesheet['Name'] . ' (' . __( 'child of', 'npa' ) . ' ' .  $template['Name'] . ')';
	
			} else {
				$active_theme = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'current_theme'" );
			}
	
		} else {
			$active_theme = $wpdb->get_var( "SELECT option_value FROM " . $blog_prefix . "options WHERE option_name = 'current_theme'" );
		}
	
	
		return $active_theme;
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
	
			$output .= '<a title="' . esc_attr( __( 'Manage themes on ', 'npa' ) . $blog_name ) .'" href="'. esc_url( $blog_url ).'/wp-admin/themes.php">';
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
}
