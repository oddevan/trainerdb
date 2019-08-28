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
	 * Import all sets from PTCG / TCGP
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_sets() {
		\WP_CLI::log( 'Querying pokemontcg.io...' );

		$pk_sets  = Pokemon::Set( [ 'verify' => false ] )->all();

		\WP_CLI::log( 'Querying TCGplayer...' );

		$tcg_response = wp_remote_get(
			'http://api.tcgplayer.com/v1.32.0/catalog/categories/3/groups?limit=200',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . TCGPLAYER_ACCESS_TOKEN,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);

		$json_response = json_decode( $tcg_response['body'] );
		$tcg_sets      = $json_response->results;

		$all_sets = [];

		foreach ( $pk_sets as $set_obj ) {
			$pk_set = $set_obj->toArray();

			$all_sets[ $set['ptcgoCode'] ] = [
				'name'    => $set['name'],
				'ptcg_id' => $set['code'],
			];
		}

		foreach ( $tcg_sets as $tcg_set ) {
			if ( ! isset( $all_sets[ $tcg_set->abbreviation ] ) ) {
				$all_sets[ $tcg_set->abbreviation ] = [
					'name' => $tcg_set->name,
				];
			}

			$all_sets[ $tcg_set->abbreviation ]['tcgp_id'] = $tcg_set->groupId;
		}

		\WP_CLI::error( "Pokemon:\n" . print_r( $pk_sets, true ) . "\nTCGplayer:\n" . print_r( $tcg_sets, true ) . "\nAll:\n" . print_r( $all_sets, true ) );

		foreach ( $all_sets as $slug => $set ) {
			$existing = term_exists( $slug, 'set' );

			if ( isset( $existing['term_id'] ) ) {
				wp_update_term( $existing['term_id'], 'set', [
					'name' => $set['name'],
					'slug' => $slug,
				] );
			} else {
				$existing = wp_insert_term( $set['name'], 'set', [
					'name' => $set['name'],
					'slug' => $slug,
				] );
			}

			if ( is_wp_error( $existing ) ) {
				\WP_CLI::error( $existing->get_error_message() . "\n" . print_r( $set, true ) );
			}

			update_term_meta( $existing['term_id'], 'ptcg_id', $set['ptcg_id'] );
			update_term_meta( $existing['term_id'], 'tcgp_id', $set['tcgp_id'] );

			\WP_CLI::success( 'Set "' . $set['name'] . '" imported.' );
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

			wp_set_object_terms( $result, 10, 'set' );
			wp_set_object_terms( $result, md5( $hash_text ), 'card_hash' );

			\WP_CLI::success( 'Imported ' . $card['name'] );
		}
		\WP_CLI::success( 'Cards imported!' );
	}

	public function tcgp() {
		$response = wp_remote_get(
			'http://api.tcgplayer.com/v1.32.0/catalog/products?categoryId=3&productTypes=Cards&groupId=2377&getExtendedFields=true&includeSkus=true&offset=0&limit=200',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . TCGPLAYER_ACCESS_TOKEN,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);

		$api_response = json_decode( $response['body'] );
		$tcg_cards    = $api_response->results;

		$pkm_cards    = Pokemon::Card( [ 'verify' => false ] )->where( [ 'setCode' => 'sm9', 'pageSize' => 1000 ] )->all();
		$pk_api_cache = [];

		foreach ( $pkm_cards as $card_obj ) {
			$card = $card_obj->toArray();

			$pk_api_cache[ $card['number'] ] = [
				'name'    => $card['name'],
				'ptcg_id' => $card['id'],
			];

			$hash_text = $card['name'] . implode( ' ', $card['text'] );
			if ( isset( $card['attacks'] ) ) {
				foreach ( $card['attacks'] as $attack ) {
					$hash_text .= $attack['name'] . $attack['text'];
				}
			}
			$pk_api_cache[ $card['number'] ]['hash'] = md5( $hash_text );
		}

		foreach ( $tcg_cards as $card ) {
			$card_number = 0;
			foreach ( $card->extendedData as $edat ) {
				if ( 'Number' === $edat->name ) {
					$card_number = $edat->value;
					break;
				}
			}

			foreach ( $card->skus as $sku ) {
				if ( 1 === $sku->languageId && 1 === $sku->conditionId ) {
					$is_reverse = ( 77 === $sku->printingId );

					$args = [
						'post_type'   => 'card',
						'post_title'  => $pk_api_cache[ $card_number ]['name'],
						'post_status' => 'publish',
						'post_name'   => $pk_api_cache[ $card_number ]['ptcg_id'] . ( $is_reverse ? 'r' : '' ),
						'meta_input'  => [
							'card_number'         => $card_number,
							'ptcg_id'             => $pk_api_cache[ $card_number ]['ptcg_id'],
							'tcgp_id'             => $sku->skuId,
							'reverse_holographic' => $is_reverse,
						],
					];

					$result = wp_insert_post( $args, true );
					if ( is_wp_error( $result ) ) {
						\WP_CLI::error( $result->get_error_message() );
					}

					wp_set_object_terms( $result, 10, 'set' );
					wp_set_object_terms( $result, md5( $hash_text ), 'card_hash' );

					\WP_CLI::success( 'Imported ' . $pk_api_cache[ $card_number ]['name'] );
				}
			}
		}
	}

	/**
	 * Iterate through all cards and get updated market pricing from TCGplayer
	 */
	public function update_prices() {
		$query_args = [
			'post_type'           => [ 'card' ],
			'post_status'         => [ 'publish' ],
			'nopaging'            => false,
			'paged'               => 1,
			'posts_per_page'      => '50',
			'ignore_sticky_posts' => false,
			'order'               => 'ASC',
			'orderby'             => 'id',
		];

		$card_query = new \WP_Query( $query_args );

		// While there are posts left to traverse...
		while ( $card_query->have_posts() ) {
			$these_cards = [];

			// Traverse these posts.
			while ( $card_query->have_posts() ) {
				$card_query->the_post();

				$id  = get_the_ID();
				$sku = get_post_meta( $id, 'tcgp_id', true );

				if ( $sku ) {
					$these_cards[ $sku ] = $id;
				}
			}

			$sku_string = \implode( ',', array_keys( $these_cards ) );
			$response   = wp_remote_get(
				'http://api.tcgplayer.com/v1.32.0/pricing/sku/' . $sku_string,
				[
					'headers' => [
						'Authorization' => 'Bearer ' . TCGPLAYER_ACCESS_TOKEN,
						'Accept'        => 'application/json',
						'Content-Type'  => 'application/json',
					],
				]
			);

			$api_response = json_decode( $response['body'] );

			foreach ( $api_response->results as $sku_info ) {
				update_post_meta( $these_cards[ $sku_info->skuId ], 'tcgp_market_price', $sku_info->marketPrice );
			}

			// Get more posts if they exist.
			$query_args['paged']++;
			$card_query = new \WP_Query( $query_args );
		}
	}
}
