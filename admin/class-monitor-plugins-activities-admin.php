<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dipankar-team.business.site
 * @since      1.0.0
 *
 * @package    Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/admin
 * @author     Dipankar Pal <dipankarpal212@gmail.com>
 */
class Monitor_Plugins_Activities_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version , $loader) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->loader = $loader;
		$this->man_page_slug = 'mpa_main_menu';
		add_filter('set-screen-option', array( $this, 'c_screen_set_option'), 11, 3);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Monitor_Plugins_Activities_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Monitor_Plugins_Activities_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/monitor-plugins-activities-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Monitor_Plugins_Activities_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Monitor_Plugins_Activities_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/monitor-plugins-activities-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function main_menu(){
		$mpa_main_menu = add_menu_page( 
			__( 'Monitor Plugin Activities', 'textdomain' ),
			'Plugin Activities <br><small style="font-size: 10px;">Monitor plugin activities</small>',
			'manage_options',
			$this->man_page_slug,
			array( $this , 'mpa_main_menu_callback' ),
			'dashicons-backup',
			6
		); 
		add_action( "admin_print_scripts-$mpa_main_menu", array( $this, 'inject_main_menu_scripts') );
    	add_action( "admin_print_styles-$mpa_main_menu", array( $this, 'inject_main_menu_styles' ) );
		add_action("load-$mpa_main_menu" , array( $this, 'c_screen_show_option' ));
		
	}

	function mpa_main_menu_callback(){
		require_once  MPA_PATH . 'admin/partials/mpa_main_menu_list_function.php' ;
	}

	function inject_main_menu_scripts(){
		wp_enqueue_style( 'mpa_daterange_style', plugin_dir_url( __FILE__ ) .  'css/daterangepicker.css', array(), time() , 'all' ); 
		$path    = "/wp-includes/js/dist/vendor/moment.js";
		wp_enqueue_script( 'moment' , site_url($path) ,array( 'jquery'), '2.26.0');

		
		wp_enqueue_script( 'mpa_daterange_js', plugin_dir_url( __FILE__ ) . 'js/daterangepicker.min.js', array( 'jquery'), time(), true );
		wp_enqueue_script( 'mpa_main_menu_list', plugin_dir_url( __FILE__ ) . 'js/list.js', array( 'jquery' ), $this->version, false );
	}

	function inject_main_menu_styles(){
		wp_enqueue_style( 'mpa_css_fontawsome', plugin_dir_url( __FILE__ ) .  'css/font-awesome.min.css', array(), time() , 'all' );
		wp_enqueue_style( 'mpa_list_css', plugin_dir_url( __FILE__ ) . 'css/list.css', array(), time() , 'all' );
	}

	function c_screen_show_option(){
		require_once  MPA_PATH . 'classes/class-mpa-list.php' ;
		$lp_manage_countries_page = "toplevel_page_" . $this->man_page_slug;

		$screen = get_current_screen();

		// get out of here if we are not on our settings page
		if(!is_object($screen) || $screen->id != $lp_manage_countries_page)
			return;

		$args = array(
			'label' => __('Log per page'),
			'default' => 25,
			'option' => 'mpa_log_per_page'
		);
		add_screen_option( 'per_page', $args );
		new MPA_List_Log_Table();
	}

	
	function c_screen_set_option($keep, $option, $value) {
		if ($option === 'mpa_log_per_page') {
			if ($value > 1000) {
				$value = 1000;
			}
		}
		return $value;
	}


}
