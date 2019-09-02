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
	}

	/**
	 * Import all sets from PTCG; ignoring TCGplayer for this
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_sets() {
		\WP_CLI::log( 'Querying pokemontcg.io...' );
		$sets = Pokemon::Set( [ 'verify' => false ] )->all();
		foreach ( $sets as $set_obj ) {
			$set  = $set_obj->toArray();
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
	 * Import all card and pokemon types from PTCG
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function import_types() {
		\WP_CLI::log( 'Querying pokemontcg.io...' );
		$types = Pokemon::Type( [ 'verify' => false ] )->all();
		foreach ( $types as $type ) {
			$args = [
				'name' => $type,
				'slug' => sanitize_title( $type ),
			];

			$existing = term_exists( $args['slug'], 'pokemon_type' );
			if ( isset( $existing['term_id'] ) ) {
				wp_update_term( $existing['term_id'], 'pokemon_type', $args );
			} else {
				$existing = wp_insert_term( $type, 'pokemon_type', $args );
			}
		}

		$types = Pokemon::Supertype( [ 'verify' => false ] )->all();
		foreach ( $types as $type ) {
			$args = [
				'name' => $type,
				'slug' => sanitize_title( $type ),
			];

			$existing = term_exists( $args['slug'], 'card_type' );
			if ( isset( $existing['term_id'] ) ) {
				wp_update_term( $existing['term_id'], 'card_type', $args );
			} else {
				$existing = wp_insert_term( $type, 'card_type', $args );
			}
		}

		$types = Pokemon::Subtype( [ 'verify' => false ] )->all();
		foreach ( $types as $type ) {
			$args = [
				'name' => $type,
				'slug' => sanitize_title( $type ),
			];

			$existing = term_exists( $args['slug'], 'card_type' );
			if ( isset( $existing['term_id'] ) ) {
				wp_update_term( $existing['term_id'], 'card_type', $args );
			} else {
				$existing = wp_insert_term( $type, 'card_type', $args );
			}
		}
		\WP_CLI::success( 'Types imported!' );
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

	/**
	 * Display all sets from both sources in a tabular data format for easy import
	 * into a spreadsheet program.
	 */
	public function sets_for_spreadsheet() {
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

		echo "\n\n\n";

		echo "Pokemon TCG Developers:\nID\tName\tCode\n";
		foreach ( $pk_sets as $set_obj ) {
			$pk_set = $set_obj->toArray();
			echo $pk_set['code'] . "\t" . $pk_set['name'] . "\t" . $pk_set['ptcgoCode'] . "\n";
		}

		echo "\n\n";

		echo "TCGPlayer:\nID\tName\tCode\n";
		foreach ( $tcg_sets as $tcg_set ) {
			echo $tcg_set->groupId . "\t" . $tcg_set->name . "\t" . $tcg_set->abbreviation . "\n";
		}
	}

	public function get_cards() {
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

		foreach ( $set_ids as $set_id ) {
			$quantity = 50;
			$offset   = 0;

			$tcgp_cards = $this->get_tcgp_cards( get_term_meta( $set_id, 'tcgp_id', true ), $quantity, $offset );
			$ptcg_cards = $this->get_ptcg_cards( get_term_meta( $set_id, 'ptcg_id', true ) );
			$set_slug   = get_term_by( 'id', $set_id, 'set' )->slug;

			while ( ! empty( $tcgp_cards ) ) {
				foreach ( $tcgp_cards as $card ) {
					$this->import_single_card( $card, $ptcg_cards, $set_slug, $set_id );
				}

				$offset    += $quantity;
				$tcgp_cards = $this->get_tcgp_cards( get_term_meta( $set_id, 'tcgp_id', true ), $quantity, $offset );
			}
		}
	}

	private function get_tcgp_cards( $set_tcgpid, $quantity, $offset ) {
		if ( ! $set_tcgpid || ! \is_numeric( $set_tcgpid ) || $set_tcgpid <= 0 ) {
			return [];
		}

		$response = wp_remote_get(
			'http://api.tcgplayer.com/v1.32.0/catalog/products?categoryId=3&productTypes=Cards&groupId=' .
				$set_tcgpid . '&getExtendedFields=true&includeSkus=true&offset=' . $offset . '&limit=' . $quantity,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . TCGPLAYER_ACCESS_TOKEN,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);

		$api_response = json_decode( $response['body'] );
		return $api_response->results;
	}

	private function get_ptcg_cards( $set_ptcgid ) {
		$pkm_cards    = Pokemon::Card( [ 'verify' => false ] )->where( [ 'setCode' => $set_ptcgid, 'pageSize' => 1000 ] )->all();
		$pk_api_cache = [];

		foreach ( $pkm_cards as $card_obj ) {
			$card = $card_obj->toArray();

			$pk_api_cache[ $card['number'] ] = [
				'name'         => $card['name'],
				'ptcg_id'      => $card['id'],
				'image_url'    => $card['imageUrlHiRes'],
				'card_types'   => [],
				'pkm_types'    => [],
				'hp'           => isset( $card['hp'] ) ? $card['hp'] : 0,
				'retreat_cost' => isset( $card['convertedRetreatCost'] ) ? $card['convertedRetreatCost'] : 0,
			];

			// Create the hash.
			$hash_text = $card['name'] . implode( ' ', $card['text'] );
			if ( isset( $card['attacks'] ) ) {
				foreach ( $card['attacks'] as $attack ) {
					$hash_text .= $attack['name'] . $attack['text'];
				}
			}
			if ( isset( $card['types'] ) ) {
				$hash_text .= \implode( ' ', $card['types'] );
			}
			$pk_api_cache[ $card['number'] ]['hash'] = md5( $hash_text );

			// Match the given types to taxonomies and save them.
			$supertype = term_exists( sanitize_title( $card['supertype'] ), 'card_type' );
			if ( isset( $supertype['type_id'] ) ) {
				$pk_api_cache[ $card['number'] ]['card_types'][] = $supertype['type_id'];
			}
			$subtype = term_exists( sanitize_title( $card['subtype'] ), 'card_type' );
			if ( isset( $subtype['type_id'] ) ) {
				$pk_api_cache[ $card['number'] ]['card_types'][] = $subtype['type_id'];
			}
			if ( isset( $card['types'] ) ) {
				foreach ( $card['types'] as $type ) {
					$pkm_type = term_exists( sanitize_title( $type ), 'pokemon_type' );
					if ( isset( $pkm_type['type_id'] ) ) {
						$pk_api_cache[ $card['number'] ]['pkm_types'][] = $pkm_type['type_id'];
					}
				}
			}
		}

		\WP_CLI::error( print_r( $pk_api_cache, true ) );
		return $pk_api_cache;
	}

	private function import_single_card( $tcgp_card, $ptcg_cards, $set_slug, $set_id ) {
		$card_number = 0;
		$card_slug   = false;
		foreach ( $tcgp_card->extendedData as $edat ) {
			if ( 'Number' === $edat->name ) {
				$card_number = $edat->value;
				break;
			}
			if ( 'Card Type' === $edat->name && 0 === strpos( $edat->value, 'Basic ' ) ) {
				switch ( $edat->value ) {
					case 'Basic Grass Energy':
						$card_slug = "$set_slug-eng-grs";
						break;
					case 'Basic Fighting Energy':
						$card_slug = "$set_slug-eng-fit";
						break;
					case 'Basic Lightning Energy':
						$card_slug = "$set_slug-eng-lgt";
						break;
					case 'Basic Metal Energy':
						$card_slug = "$set_slug-eng-met";
						break;
					case 'Basic Psychic Energy':
						$card_slug = "$set_slug-eng-psy";
						break;
					case 'Basic Fire Energy':
						$card_slug = "$set_slug-eng-fir";
						break;
					case 'Basic Fairy Energy':
						$card_slug = "$set_slug-eng-fay";
						break;
					case 'Basic Darkness Energy':
						$card_slug = "$set_slug-eng-drk";
						break;
					case 'Basic Water Energy':
						$card_slug = "$set_slug-eng-wtr";
						break;
				}
				break;
			}
		}
		if ( strpos( $card_number, '/' ) > 0 ) {
			$card_number = substr( $card_number, 0, strpos( $card_number, '/' ) );
		}

		$card_name = isset( $ptcg_cards[ $card_number ] ) ? $ptcg_cards[ $card_number ]['name'] : $tcgp_card->name;
		if ( ! $card_slug ) {
			$card_slug = "$set_slug-$card_number";
		}

		foreach ( $tcgp_card->skus as $sku ) {
			if ( 1 === $sku->languageId && 1 === $sku->conditionId ) {
				$is_reverse = ( 77 === $sku->printingId );

				$args = [
					'post_type'   => 'card',
					'post_title'  => $card_name,
					'post_status' => 'publish',
					'post_name'   => $card_slug . ( $is_reverse ? 'r' : '' ),
					'meta_input'  => [
						'card_number'         => $card_number,
						'ptcg_id'             => $ptcg_cards[ $card_number ]['ptcg_id'],
						'tcgp_id'             => $sku->skuId,
						'reverse_holographic' => $is_reverse,
					],
				];

				$result = wp_insert_post( $args, true );
				if ( is_wp_error( $result ) ) {
					\WP_CLI::error( $result->get_error_message() );
				}

				wp_set_object_terms( $result, $set_id, 'set' );
				wp_set_object_terms( $result, $ptcg_cards[ $card_number ]['hash'], 'card_hash' );
				if ( isset( $ptcg_cards[ $card_number ]['img'] ) ) {
					set_post_thumbnail( $result, $ptcg_cards[ $card_number ]['img'] );
				}

				\WP_CLI::success( 'Imported ' . $card_name );
			}
		}
	}

	/**
	 * Imports the media found at the given URL into the WP Media Library linked to the given post
	 *
	 * @param string $url Address of the remote media to import.
	 * @param string $name Description of image.
	 * @return int WordPress ID of imported media.
	 */
	private function sideload_media( $url, $name ) {
		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}
		$post_id    = 1;
		$file_array = array();
		// Set variables for storage
		// fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|mp4|m4v)/i', $url, $matches );
		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;
		// If error storing temporarily, unlink.
		if ( is_wp_error( $tmp ) ) {
			unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}
		// do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $name );
		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			unlink( $file_array['tmp_name'] );
			return $id;
		}
		return $id;
	}
}
