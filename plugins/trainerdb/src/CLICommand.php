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
		\WP_CLI::log( 'TrainerDB activated.' );
	}

	/**
	 * Import all sets from PTCG
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_sets() {
		\WP_CLI::log( 'Querying pokemontcg.io...' );

		$sets = Pokemon::Set( [ 'verify' => false ] )->all();
		foreach ( $sets as $set_obj ) {
			$set = $set_obj->toArray();

			$args = [
				'name' => $set['name'],
				'slug' => $set['ptcgoCode'],
			];

			$existing = term_exists( $set['ptcgoCode'], 'set' );

			if ( isset( $existing['term_id'] ) ) {
				wp_update_term( $existing['term_id'], 'set', $args );
			} else {
				$existing = wp_insert_term( $set['name'], 'set', $args );
			}

			update_term_meta( $existing['term_id'], 'ptcg_id', $set['code'] );
		}
		\WP_CLI::success( 'Sets imported!' );
	}

	/**
	 * Import all cards in a given set from PTCG
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_cards() {
		\WP_CLI::log( 'Querying pokemontcg.io...' );

		$cards = Pokemon::Card( [ 'verify' => false ] )->where( [ 'setCode' => 'sm9', 'pageSize' => 1000 ] )->all();

		foreach ( $cards as $card_obj ) {
			$card = $card_obj->toArray();

			$hash_text = $card['name'] . implode( ' ', $card['text'] );
			if ( isset( $card['attacks'] ) ) {
				foreach ( $card['attacks'] as $attack ) {
					$hash_text .= $attack['name'] . $attack['text'];
				}
			}

			$args = [
				'post_type'   => 'card',
				'post_title'  => $card['name'],
				'post_status' => 'publish',
				'post_name'   => $card['id'],
				'tags_input'  => [ (string) md5( $hash_text ) ],
				'tax_input'   => [
					'set'       => [ 10 ],
					'card_hash' => (string) md5( $hash_text ),
				],
				'meta_input'  => [
					'card_number' => $card['number'],
					'ptcg_id'     => $card['id'],
				],
			];

			$existing = get_page_by_path( $card['id'], OBJECT, 'card' );
			if ( $existing ) {
				$args['ID'] = $existing->ID;
			}

			$result = wp_insert_post( $args, true );
			if ( is_wp_error( $result ) ) {
				\WP_CLI::error( $result->get_error_message() );
			}
			\WP_CLI::success( print_r( $args ) );
		}
		\WP_CLI::success( 'Cards imported!' );
	}

	public function lapras() {
		//$existing = get_page_by_path( 'sm9-31', OBJECT, 'card' );
		//print_r( $existing->ID );

		$cardArr = Pokemon::Card( [ 'verify' => false ] )->where( [ 'id' => 'sm9-171' ] )->all();
		$card    = $cardArr[0]->toArray();

		$hash_text = $card['name'] . implode( ' ', $card['text'] );
		if ( isset( $card['attacks'] ) ) {
			foreach ( $card['attacks'] as $attack ) {
				$hash_text .= $attack['name'] . $attack['text'];
			}
		}

		\WP_CLI::log( $hash_text );
	}
}
