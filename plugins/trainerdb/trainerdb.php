<?php
/**
 * Plugin Name: TrainerDB
 * Plugin URI:  https://github.com/oddevan/trainerdb
 * Description: Track your Pokemon TCG collection
 * Version:     0.1.0
 * Author:      Evan Hildreth
 * Author URI:  https://eph.me/
 * Text Domain: trainerdb
 * Domain Path: /languages
 * License:     GPL2
 *
 * @package oddEvan\TrainerDB
 * @since 2019-05-29
 */

namespace oddEvan\TrainerDB;

defined( 'ABSPATH' ) || die( 'Please do not.' );

$plugin = new TrainerDB();
$plugin->run();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_command( 'trainerdb', new CLICommand() );
}

register_activation_hook( __FILE__, function() {
	$db = new Database\Installer();
	$db->create_library_table();
} );

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  2.1.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  2.1.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}
