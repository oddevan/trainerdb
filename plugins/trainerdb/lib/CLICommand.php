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
use oddEvan\TrainerDB\Model\TcgPlayerCard;

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
		$tcgp_cards = $this->tcgp_helper->get_cards_from_set( 2545, 3, 0 );

		foreach ( $tcgp_cards as $tcgp_card ) {
			WP_CLI::log( 'Creating object...' );
			$card = new Model\TcgPlayerCard( $tcgp_card, $this->tcgp_helper );

			WP_CLI::log( print_r( $card->get_post_args(), true ) );
		}
	}

	public function import_set( $args, $assoc_args ) {
		if ( ! $args && ! $assoc_args['all'] ) {
			WP_CLI::error( 'Please specifiy a set slug to import, or `--all` to import all available.' );
		}

		$set_ids = [];
		if ( $assoc_args['all'] ) {
			$set_ids = get_terms( array(
				'taxonomy'   => 'set',
				'hide_empty' => false,
				'fields'     => 'ids',
				'meta_query' => [
					'relation' => 'AND',
					[
						'key'     => 'tcgp_id',
						'compare' => '>',
						'value'   => 0,
					],
				],
			) );
		} else {
			foreach ( $args as $set_slug ) {
				$tax       = get_term_by( 'slug', sanitize_title( $set_slug ), 'set' );
				$set_ids[] = $tax ? $tax->term_id : null;
			}
		}

		$overwrite = $assoc_args['overwrite'] ? true : false;

		foreach ( $set_ids as $set_id ) {
			$this->import_single_set( $set_id, $overwrite );
		}
	}

	/** Load cards into the database */
	private function import_single_set( $set_id, $overwrite ) {
		$quantity = 100;
		$offset   = 0;
		$tcgp_set = get_term_meta( $set_id, 'tcgp_id', true );
		$cards    = $this->tcgp_helper->get_cards_from_set( $tcgp_set, $quantity, $offset );

		while ( ! empty( $cards ) ) {
			foreach ( $cards as $card ) {
				$new_card = new TcgPlayerCard( $card, $this->tcgp_helper );

				if ( $overwrite || $new_card->get_post_id() === 0 ) {
					$this->import_single_card( $new_card );
					WP_CLI::success( 'Imported ' . $card->name );
				}

				if ( $new_card->has_parallel_printing() ) {
					$new_card->set_parallel_printing( true );
					if ( $overwrite || $new_card->get_post_id() === 0 ) {
						$this->import_single_card( $new_card );
						WP_CLI::success( 'Imported ' . $card->name . ' (Reverse Holo)' );
					}
				}
			}

			$offset += $quantity;
			$cards   = $this->tcgp_helper->get_cards_from_set( $tcgp_set, $quantity, $offset );
		}
	}

	private function import_single_card( Model\Card $card ) {
		$result = wp_insert_post( $card->get_post_args(), true );
		if ( is_wp_error( $result ) ) {
			\WP_CLI::error( $result->get_error_message() );
		}
	}
}

// Kilroy was here
