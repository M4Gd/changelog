<?php
/**
 * Changelog
 *
 * @package   Changelog
 * @author    averta
 * @license   GPL-2.0+
 * @copyright 2014 
 */

/**
 *
 * @package ChangelogAdmin
 */
class ChangelogAdmin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Changelog::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// 
		add_action( 'add_meta_boxes', array( $this, 'add_changelog_custom_meta_box'  ) );
		add_action( 'save_post'		, array( $this, 'save_changelog_custom_meta_box' ) );
		add_action( 'admin_footer'	, array( $this, 'flush_plugin_rewrite_rules' 	 ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		// just load on changelog screen
		$screen = get_current_screen();
	 	if( isset($screen) && $screen->post_type == 'changelog' ) {
	 		wp_enqueue_style( 'jquery-cu-ui', plugins_url( 'assets/css/jquery-ui.css', __FILE__ ), array(), Changelog::VERSION );
	 		wp_enqueue_style( 'changelog-admin-style', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Changelog::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		// just load on changelog screen
		$screen = get_current_screen();
		if( isset($screen) && $screen->post_type == 'changelog' ) {
	 		wp_enqueue_script( 'jquery-ui-datepicker' );
	 		wp_enqueue_script( 'maskedinput', 
	 		                  	plugins_url( 'assets/js/jquery.maskedinput.min.js', __FILE__ ), 
	 		                  	array( 'jquery' ), 
	 		                  	Changelog::VERSION, true );

	 		wp_enqueue_script( 'changelog_admin_script', 
	 		                  	plugins_url( 'assets/js/admin.js', __FILE__ ), 
	 		                  	array( 'jquery', 'maskedinput', 'jquery-ui-datepicker' ), 
	 		                  	Changelog::VERSION, true );
		}

	}

	// Add the Meta Box  
	public function add_changelog_custom_meta_box () {  
	    add_meta_box(  
	        'log_options', // $id  
	        'Changelog Options', // $title   
	        array( $this, 'show_changelog_meta_box' ), // $callback  
	        'changelog', // $page  
	        'normal', // $context  
	        'high'); // $priority  
	}


	public function show_changelog_meta_box(){
		global $post;
		
		wp_nonce_field( 'log_options' , 'log_options_nonce' );
		
		$date    = get_post_meta($post->ID, "release_date"		, true);
		$version = get_post_meta($post->ID, "release_version"	, true);
		$tested  = get_post_meta($post->ID, "compatibility_version"	, true);
		
		echo '<label>'.__('Version Release Date', 'changelog').' : <input type="text" class="datepickerField" name="release_date" id="release_date" value="'.$date.'" /></label><br ><br />';
		echo '<label>'.__('Version Number', 'changelog').' : <input type="text" class="" name="release_version" id="release_version" value="'.$version.'" /></label>';
		echo '<label>'.__('Compatibility with WordPress version', 'changelog').' : <input type="text" class="" name="compatibility_version" id="compatibility_version" value="'.$tested.'" /></label>';

	}

	/**
	 * Save custom metabox values
	 *
	 * @since    1.0.0
	 */
	public function save_changelog_custom_meta_box($post_id){
		global $post;
	        
        // Verify the nonce before proceeding. 
        if ( !isset( $_POST['log_options_nonce'] ) || !wp_verify_nonce( $_POST['log_options_nonce'], 'log_options' ) )
        return $post->ID;
        
        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post->ID;
        }
        
        $old_date = get_post_meta( $post->ID, "release_date", true );
        $new_date = $_POST['release_date'];
        
        if($old_date !== $new_date){
        	update_post_meta( $post->ID, "release_date", $new_date );
        }
        
        
        $old_ver = get_post_meta( $post->ID, "release_version", true );
        $new_ver = $_POST['release_version'];
        
        if($old_ver !== $new_ver){
        	update_post_meta( $post->ID, "release_version", $new_ver );
        }

        $old_tested = get_post_meta( $post->ID, "compatibility_version", true );
        $new_tested = $_POST['compatibility_version'];
        
        if($old_ver !== $new_ver){
        	update_post_meta( $post->ID, "compatibility_version", $new_tested );
        }
	}

	/**
	 * Flush rewrite rules on first run
	 */
	public function flush_plugin_rewrite_rules() {

		$is_flushed = get_option( 'axiom_plugin_changelog_flushed');
		if( $is_flushed  != "1" ) {
			update_option( 'axiom_plugin_changelog_flushed', 1 );
			flush_rewrite_rules();
		}

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

}
