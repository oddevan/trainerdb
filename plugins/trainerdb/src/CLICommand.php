<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to handle WP-CLI commands to import content from
 * external APIs.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB;

use Pokemon\Pokemon;
use \WP_CLI;
use \WP_Query;

/**
 * Class to handle the WP-CLI commands. May refactor logic out to different class eventually.
 *
 * @since 0.1.0
 */
class CLICommand extends \WP_CLI_Command {

	/**
	 * Helper object for accessing the TCGPlayer API
	 *
	 * @var Import\TcgPlayerHelper tcgp_helper Object for accessing TCGPlayer API
	 */
	private $tcgp_helper = false;

	/**
	 * Construct the object
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->tcgp_helper = new Import\TcgPlayerHelper();
	}

	/**
	 * Run whatever I'm testing now.
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function test() {
		WP_CLI::log( 'Querying TCGPlayer...' );
		$tcgp_cards = $this->tcgp_helper->get_cards_from_set( 2534, 3, 0 );

		foreach ( $tcgp_cards as $tcgp_card ) {
			WP_CLI::log( 'Creating object...' );
			$card = new Model\TcgPlayerCard( $tcgp_card );

			WP_CLI::log( print_r( $card->debug_dump(), true ) );
		}

		//////

		WP_CLI::log( 'Querying TCGPlayer...' );
		$tcgp_cards = $this->tcgp_helper->get_cards_from_set( 2377, 1, 152 );

		WP_CLI::log( 'Creating object...' );
		$card = new Model\TcgPlayerCard( $tcgp_cards[0] );

		WP_CLI::log( print_r( $card->debug_dump(), true ) );

		//////

		WP_CLI::log( 'Querying TCGPlayer...' );
		$tcgp_cards = $this->tcgp_helper->get_cards_from_set( 2377, 1, 200 );

		WP_CLI::log( 'Creating object...' );
		$card = new Model\TcgPlayerCard( $tcgp_cards[0] );

		WP_CLI::log( print_r( $card->debug_dump(), true ) );
	}
}
