<?php
/*
Plugin Name: wp-svbtle editor
Plugin URI: http://www.themeskult.com/wp-svbtle-editor
Description: Simple markdown editor for Wordpress.
Version: 1.0
Author: Themes Kult
Author URI: http://themeskult.com
Author Email: themeskult@gravityonmars.com
License:

  Copyright 2012 Themes Kult (themeskult@gravityonmars.com)
  
*/

// wp-svbtle editor: rename this class to a proper name for your plugin
class WpSvbtleEditor {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
		
		// load plugin text domain
		add_action( 'init', array( $this, 'textdomain' ) );

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		add_action('admin_bar_menu', array( $this, 'add_items' ),  100);
	
		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
		
	    /*
	     * TODO:
	     * Define the custom functionality for your plugin. The first parameter of the
	     * add_action/add_filter calls are the hooks into which your code should fire.
	     *
	     * The second parameter is the function name located within this class. See the stubs
	     * later in the file.
	     *
	     * For more information: 
	     * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
	     */
	    // add_action( 'wp-svbtle editor', array( $this, 'action_method_name' ) );
	    // add_filter( 'wp-svbtle editor', array( $this, 'filter_method_name' ) );

		// add_filter( 'rewrite_rules_array', array( $this, 'my_insert_rewrite_rules' ) );
		// add_action( 'wp_loaded', array( $this, 'my_flush_rules' ) );

		add_action( 'init',  array( $this,'wp_ozh_plu_rewrite') );
		add_action('wp_ajax_upload_attachment',  array( $this, 'upload_attachment'));


	} // end constructor
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	    global $wp_rewrite;
    	$wp_rewrite->flush_rules(true);  

	} // end activate
	
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here	
	    global $wp_rewrite;	
	    $wp_rewrite->flush_rules(true);  
	} // end deactivate
	
	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @params	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function uninstall( $network_wide ) {
		// TODO define uninstall functionality here		
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation
	 */
	public function textdomain() {
		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		load_plugin_textdomain( 'plugin-name-locale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'plugin-name-admin-styles', plugins_url( 'wp-svbtle-editor/css/admin.css' ) );
	
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function register_admin_scripts() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'plugin-name-admin-script', plugins_url( 'wp-svbtle-editor/js/admin.js' ) );
	
	} // end register_admin_scripts
	
	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'plugin-name-plugin-styles', plugins_url( 'wp-svbtle-editor/css/display.css' ) );
	
	} // end register_plugin_styles
	
	public function add_items($admin_bar)
	{
	 	global $post;

	    $args = array(
					'id'    => 'wp-svbtle-editor',
					'title' => 'wp-svbtle editor',
					'href'  => get_bloginfo('url') . '/wp-svbtle/',
					'meta'  => array('title' => __('wp-svbtle editor'))
	            );

	    //This is where the magic works.
	    $admin_bar->add_menu( $args);
	}

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	
		// TODO change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'plugin-name-plugin-script', plugins_url( 'wp-svbtle-editor/js/display.js' ) , array("jquery"));
	
	} // end register_plugin_scripts




	function upload_attachment(){

        $file_return = wp_handle_upload($_FILES['file'], array('test_form' => false));
        
        if(isset($file_return['error']) || isset($file_return['upload_error_handler'])) {

            header('HTTP/1.1 500 Internal Server Error');

        }else{

	        $filename = $file_return['name'];

			$wp_filetype = wp_check_filetype(basename($filename), null );
			$wp_upload_dir = wp_upload_dir();

			$attachment = array(
			 'guid' => $file_return['path'], 
			 'post_mime_type' => $wp_filetype['type'],
			 'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
			 'post_content' => '',
			 'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $filename, 37 );
			// you must first include the image.php file
			// for the function wp_generate_attachment_metadata() to work
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			print_r($file_return['url']);

        }

		die();
	}
	

	function wp_ozh_plu_rewrite() {
    	add_rewrite_rule( 'wp-svbtle/', 'wp-content/plugins/wp-svbtle-editor/', 'top');
	}

  
} // end class

// TODO: update the instantiation call of your plugin to the name given at the class definition
$plugin_name = new WpSvbtleEditor();
