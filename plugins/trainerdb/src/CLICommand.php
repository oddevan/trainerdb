<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to handle WP-CLI commands to import content from
 * external APIs.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

use WPCLI;
use Pokemon\Pokemon;

/**
 * Class to handle the WP-CLI commands. May refactor logic out to different class eventually.
 *
 * @since 0.1.0
 */
class CLICommand extends \WP_CLI_Command {
	/**
	 * Construct the object
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function __construct() {
		WP_CLI::log( 'Here we are' );
	}

	/**
	 * Import all sets from PTCG
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_sets() {
		WP_CLI::log( 'Querying pokemontcg.io...' );

		$sets = Pokemon::Set( [ 'verify' => false ] )->where( [ 'standardLegal' => 'true' ] )->all();
		foreach ( $sets as $set ) {
			print_r( $set->toArray() );
		}
	}
}
