<?php
/**
 * A WordPress plugin that allows you to add, archive and display your product's changelog in your WordPress website.
 *
 * @package   axiom
 * @author    averta
 * @license   GPL-2.0+
 * @copyright 2014 
 *
 * @wordpress-plugin
 * Plugin Name:       Changelog
 * Plugin URI:        https://github.com/M4Gd/changelog
 * Description:       A WordPress plugin that allows you to add, archive and display your product's changelog in your WordPress website.
 * Version:           1.2.1
 * Author:            averta
 * Author URI:        http://averta.net
 * Text Domain:       changelog
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/M4Gd/changelog
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-changelog.php' );

register_activation_hook  ( __FILE__, array( 'Changelog', 'activate'   ) );
register_deactivation_hook( __FILE__, array( 'Changelog', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Changelog', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-changelog-admin.php' );
	add_action( 'plugins_loaded', array( 'ChangelogAdmin', 'get_instance' ) );

}
