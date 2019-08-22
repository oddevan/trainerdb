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
