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
 * @package Changelog
 */
class Changelog {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'changelog';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Add new post type for changelog
		add_action( 'init', array( $this, 'changelog_post_type_init' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'changelog-public-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}


	/**
	 * Adds new post type for Changelog
	 * @since 	 1.0.0 
	 */
	public function changelog_post_type_init() {

	    $labels = array(
	        'name'              => __('Changelogs'			, 'changelog'),
	        'singular_name'     => __('changelog'			, 'changelog'),
	        'add_new'           => __('Add Log'				, 'changelog'),
	        'all_items'         => __('All Logs'			, 'changelog'),
	        'add_new_item'      => __('Add New Log'			, 'changelog'),
	        'edit_item'         => __('Edit Log'			, 'changelog'),
	        'new_item'          => __('New Log'				, 'changelog'),
	        'view_item'         => __('View ChangeLogs'		, 'changelog'),
	        'search_items'      => __('Search ChangeLogs'	, 'changelog'),
	        'not_found'         => __('No Log found'		, 'changelog'),
	        'not_found_in_trash'=> __('No Log found in Trash', 'changelog'), 
	        'parent_item_colon' => ''
	    );

	    $rewrite = array(	'slug' 		=> apply_filters('axiom_plugin_changelog_structure', 'log'),
	    					'with_front'=> true);
	      
	    $args = array(
	        'labels'            => $labels,
	        'public'            => true,
	        'publicly_queryable'=> true,
	        'show_ui'           => true, 
	        'query_var'         => true,
	        'rewrite'           => $rewrite,
	        'capability_type'   => 'post',
	        'hierarchical'      => false,
	        'menu_position'     => 34,
	        'supports'          => array('title','editor','excerpt','thumbnail', 'page-attributes'),
	        'has_archive'       => apply_filters('axiom_plugin_changelog_archive_structure', 'log/all')
	    ); 

	    register_post_type( "changelog", $args);

		 
	    // labels for changelog Category
	    $log_category_labels = array(
	        'name'              => __( 'ChangeLog Categories' , 'changelog' ),
	        'singular_name'     => __( 'ChangeLog Category'   , 'changelog' ),
	        'search_items'      => __( 'Search in ChangeLog Categories'   , 'changelog'),
	        'all_items'         => __( 'All ChangeLog Categories'         , 'changelog'),
	        'most_used_items'   => null,
	        'parent_item'       => null,
	        'parent_item_colon' => null,
	        'edit_item'         => __( 'Edit ChangeLog Category'          , 'changelog'), 
	        'update_item'       => __( 'Update ChangeLog Category'        , 'changelog'),
	        'add_new_item'      => __( 'Add new ChangeLog Category'       , 'changelog'),
	        'new_item_name'     => __( 'New ChangeLog Category'           , 'changelog'),
	        'menu_name'         => __( 'Categories'             	      , 'changelog'),
	    );
	    
	    register_taxonomy('changelog-cat', array('changelog'), array(
	        'hierarchical'      => true,
	        'labels'            => $log_category_labels,
	        'singular_name'     => 'ChangeLog Category',
	        'show_ui'           => true,
	        'query_var'         => true,
	        'rewrite'           => array('slug' => 'changelog' )
	    ));
	}


	/**
	 * On plugin activation.
	 *
	 * @since    1.0.0
	 */
	public function activate() {
		flush_rewrite_rules();
	}

	/**
	 * On plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

}
