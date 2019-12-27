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
						'Authorization' => 'Bearer ' . $this->access_token,
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

		$tcg_sets = $this->tcgp_helper->get_sets();

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

	public function get_cards( $args, $assoc_args ) {
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
		if ( ! $set_tcgpid || ! is_numeric( $set_tcgpid ) || $set_tcgpid <= 0 ) {
			return [];
		}

		return $this->tcgp_helper->get_cards_from_set( $set_tcgpid, $quantity, $offset );
	}

	private function get_ptcg_cards( $set_ptcgid ) {
		$pkm_cards    = Pokemon::Card( [ 'verify' => false ] )->where( [ 'setCode' => $set_ptcgid, 'pageSize' => 1000 ] )->all();
		$pk_api_cache = [];

		foreach ( $pkm_cards as $card_obj ) {
			$card = $card_obj->toArray();

			$pk_api_cache[ $card['number'] ] = [
				'name'            => $card['name'],
				'ptcg_id'         => $card['id'],
				'image_url'       => $card['imageUrlHiRes'],
				'card_types'      => [],
				'pkm_types'       => [],
				'hp'              => $card['hp'],
				'retreat_cost'    => is_array( $card['retreatCost'] ) ? count( $card['retreatCost'] ) : 0,
				'attacks'         => $card['attacks'],
				'ability'         => $card['ability'],
				'text'            => implode( ' ', $card['text'] ),
				'weakness_type'   => null,
				'weakness_mod'    => null,
				'resistance_type' => null,
				'resistance_mod'  => null,
				'evolves_from'    => isset( $card['evolvesFrom'] ) ? $card['evolvesFrom'] : null,
			];

			// Match the given types to taxonomies and save them.
			$pk_api_cache[ $card['number'] ]['card_types'][] = sanitize_title( $card['supertype'] );
			$pk_api_cache[ $card['number'] ]['card_types'][] = sanitize_title( $card['subtype'] );

			if ( isset( $card['types'] ) ) {
				foreach ( $card['types'] as $type ) {
					$pk_api_cache[ $card['number'] ]['pkm_types'][] = sanitize_title( $type );
				}
			}
			if ( isset( $card['weaknesses'] ) ) {
				$pk_api_cache[ $card['number'] ]['weakness_type'] = get_term_by( 'slug', sanitize_title( $card['weaknesses'][0]['type'] ), 'pokemon_type' );
				$pk_api_cache[ $card['number'] ]['weakness_mod']  = $card['weaknesses'][0]['value'];
			}
			if ( isset( $card['resistances'] ) ) {
				$pk_api_cache[ $card['number'] ]['resistance_type'] = get_term_by( 'slug', sanitize_title( $card['resistances'][0]['type'] ), 'pokemon_type' );
				$pk_api_cache[ $card['number'] ]['resistance_mod']  = $card['resistances'][0]['value'];
			}
		}

		return $pk_api_cache;
	}

	private function import_single_card( $tcgp_card, $ptcg_cards, $set_slug, $set_id ) {
		$card_slug = false;
		$card_info = $this->parse_tcg_card_info( $tcgp_card );

		/*////////

		WP_CLI::log( 'Attacks for ' . $tcgp_card->name );
		foreach ( $card_info['attacks'] as $atk ) {
			$this->parse_attack_text( $atk );
		}
		echo "\n----------\n";
		return;

		////////*/

		if ( 0 === strpos( $card_info['card_type'], 'Basic ' ) ) {
			switch ( $card_info['card_type'] ) {
				case 'Basic Grass Energy':
					$card_slug = "$set_slug-eng-g";
					break;
				case 'Basic Fighting Energy':
					$card_slug = "$set_slug-eng-f";
					break;
				case 'Basic Lightning Energy':
					$card_slug = "$set_slug-eng-l";
					break;
				case 'Basic Metal Energy':
					$card_slug = "$set_slug-eng-m";
					break;
				case 'Basic Psychic Energy':
					$card_slug = "$set_slug-eng-p";
					break;
				case 'Basic Fire Energy':
					$card_slug = "$set_slug-eng-r";
					break;
				case 'Basic Fairy Energy':
					$card_slug = "$set_slug-eng-y";
					break;
				case 'Basic Darkness Energy':
					$card_slug = "$set_slug-eng-d";
					break;
				case 'Basic Water Energy':
					$card_slug = "$set_slug-eng-w";
					break;
			}
		}

		$card_number = $card_info['card_number'];
		$has_ptcg    = isset( $ptcg_cards[ $card_number ] );
		$is_pokemon  = $has_ptcg && in_array( 'pokemon', $ptcg_cards[ $card_number ]['card_types'], true );
		$card_name   = $has_ptcg ? $ptcg_cards[ $card_number ]['name'] : $tcgp_card->name;
		if ( ! $card_slug ) {
			$card_slug = $set_slug . '-' . $card_number;
		}

		foreach ( $tcgp_card->skus as $sku ) {
			if ( 1 === $sku->languageId && 1 === $sku->conditionId ) {
				$check_query   = new WP_Query( [
					'meta_key'   => 'tcgp_id',
					'meta_value' => $sku->skuId,
					'post_type'  => 'card',
					'fields'     => 'ids',
				] );

				$is_reverse = ( 77 === $sku->printingId );

				$args = [
					'ID'          => $check_query->post_count > 0 ? $check_query->posts[0] : 0,
					'post_type'   => 'card',
					'post_title'  => $card_name,
					'post_status' => 'publish',
					'post_name'   => $card_slug . ( $is_reverse ? 'r' : '' ),
					'meta_input'  => [
						'card_number'         => filter_var( $card_number, FILTER_SANITIZE_NUMBER_INT ),
						'ptcg_id'             => $has_ptcg ? $ptcg_cards[ $card_number ]['ptcg_id'] : '!err',
						'tcgp_id'             => $sku->skuId,
						'tcgp_url'            => $tcgp_card->url,
						'reverse_holographic' => $is_reverse,
						'image_url'           => $has_ptcg ? $ptcg_cards[ $card_number ]['image_url'] : $tcgp_card->imageUrl,
						'card_text'           => $has_ptcg ? $ptcg_cards[ $card_number ]['text'] : $card_info['text'],
						'hp'                  => $is_pokemon ? $ptcg_cards[ $card_number ]['hp'] : null,
						'evolves_from'        => $is_pokemon ? $ptcg_cards[ $card_number ]['evolves_from'] : null,
						'retreat_cost'        => $is_pokemon ? $ptcg_cards[ $card_number ]['retreat_cost'] : null,
						'weakness_type'       => $is_pokemon ? $ptcg_cards[ $card_number ]['weakness_type'] : null,
						'weakness_mod'        => $is_pokemon ? $ptcg_cards[ $card_number ]['weakness_mod'] : null,
						'resistance_type'     => $is_pokemon ? $ptcg_cards[ $card_number ]['resistance_type'] : null,
						'resistance_mod'      => $is_pokemon ? $ptcg_cards[ $card_number ]['resistance_mod'] : null,
					],
				];

				foreach ( $ptcg_cards[ $card_number ]['attacks'] as $attack ) {
					array_walk( $attack['cost'], function( &$value, $key ) {
						$tax   = get_term_by( 'slug', sanitize_title( $value ), 'pokemon_type' );
						$value = $tax ? $tax->term_id : $value;
					} );
					unset( $attack['convertedEnergyCost'] );

					$args['meta_input']['attacks'][] = $attack;
				}

				if ( isset( $ptcg_cards[ $card_number ]['ability'] ) ) {
					$args['meta_input']['ability'] = [
						'text' => $ptcg_cards[ $card_number ]['ability']['text'],
						'name' => $ptcg_cards[ $card_number ]['ability']['name'],
					];
				}

				$result = wp_insert_post( $args, true );
				if ( is_wp_error( $result ) ) {
					\WP_CLI::error( $result->get_error_message() );
				}

				wp_set_object_terms( $result, $set_id, 'set' );
				if ( isset( $ptcg_cards[ $card_number ] ) ) {
					wp_set_object_terms( $result, $ptcg_cards[ $card_number ]['card_types'], 'card_type' );
					wp_set_object_terms( $result, $ptcg_cards[ $card_number ]['pkm_types'], 'pokemon_type' );
				} else {
					wp_set_object_terms( $result, 'needs-ptcg', 'process' );
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

	private function parse_tcg_card_info( $tcgp_card ) {
		$card_info = [];

		foreach ( $tcgp_card->extendedData as $edat ) { // phpcs:ignore
			switch ( $edat->name ) {
				case 'Number':
					$card_info['card_number'] = $edat->value;
					if ( strpos( $card_info['card_number'], '/' ) > 0 ) {
						$card_info['card_number'] = substr( $card_info['card_number'], 0, strpos( $card_info['card_number'], '/' ) );
					}
					break;
				case 'Rarity':
					$card_info['rarity'] = $edat->value;
					break;
				case 'Card Type':
					$card_info['card_type'] = $edat->value;
					break;
				case 'CardText':
					$card_info['text'] = $edat->value;
					break;
				case 'HP':
					$card_info['hp'] = $edat->value;
					break;
				case 'Stage':
					$card_info['pokemon_stage'] = $edat->value;
					break;
				case 'Attack 1':
				case 'Attack 2':
				case 'Attack 3':
				case 'Attack 4':
					$card_info['attacks'][] = $edat->value;
					break;
				case 'Weakness':
					$card_info['weakness'] = $edat->value;
					break;
				case 'Resistance':
					$card_info['resistance'] = $edat->value;
					break;
				case 'RetreatCost':
					$card_info['retreat_cost'] = $edat->value;
					break;
			}
		}

		return $card_info;
	}

	public function reload_cards() {
		$query = new WP_Query( [
			'post_type' => 'card',
			'tax_query' => [
				[
					'taxonomy' => 'process',
					'field'    => 'slug',
					'terms'    => 'reload-ptcg',
				],
			],
		] );

		// While there are posts left to traverse...
		while ( $query->have_posts() ) {
			$query->the_post();

			$ptcg_id  = get_post_meta( get_the_ID(), 'ptcg_id', true );
			$card_obj = Pokemon::Card()->find( $ptcg_id );
			if ( ! $card_obj ) {
				WP_CLI::log( 'Could not find ' . get_the_title() . ' with ID ' . $ptcg_id );
				continue;
			}

			$card       = $card_obj->toArray();
			$is_pokemon = ( 'pokemon' === sanitize_title( $card['supertype'] ) );

			$card_types   = [];
			$card_types[] = sanitize_title( $card['supertype'] );
			$card_types[] = sanitize_title( $card['subtype'] );

			$pokemon_types = [];
			if ( isset( $card['types'] ) ) {
				foreach ( $card['types'] as $type ) {
					$pokemon_types[] = sanitize_title( $type );
				}
			}

			$args = [
				'ID'         => get_the_ID(),
				'post_title' => $card['name'],
				'meta_input' => [
					'image_url' => $card['imageUrlHiRes'],
					'card_text' => implode( ' ', $card['text'] ),
					'hp'        => $is_pokemon ? $card['hp'] : null,
				],
			];

			foreach ( $card['attacks'] as $attack ) {
				array_walk( $attack['cost'], function( &$value, $key ) {
					$tax   = get_term_by( 'slug', sanitize_title( $value ), 'pokemon_type' );
					$value = $tax ? $tax->term_id : $value;
				} );
				unset( $attack['convertedEnergyCost'] );

				$args['meta_input']['attacks'][] = $attack;
			}

			if ( isset( $card['ability'] ) ) {
				$args['meta_input']['ability'] = [
					'text' => $card['ability']['text'],
					'name' => $card['ability']['name'],
				];
			}

			if ( isset( $card['weaknesses'] ) ) {
				$args['meta_input']['weakness_type'] = get_term_by( 'slug', sanitize_title( $card['weaknesses'][0]['type'] ), 'pokemon_type' );
				$args['meta_input']['weakness_mod']  = $card['weaknesses'][0]['value'];
			}
			if ( isset( $card['resistances'] ) ) {
				$args['meta_input']['resistance_type'] = get_term_by( 'slug', sanitize_title( $card['resistances'][0]['type'] ), 'pokemon_type' );
				$args['meta_input']['resistance_mod']  = $card['resistances'][0]['value'];
			}

			$result = wp_update_post( $args, true );
			if ( is_wp_error( $result ) ) {
				\WP_CLI::error( $result->get_error_message() );
			}

			wp_set_object_terms( get_the_ID(), $card_types, 'card_type' );
			wp_set_object_terms( get_the_ID(), $pokemon_types, 'pokemon_type' );

			wp_remove_object_terms( get_the_ID(), 'reload-ptcg', 'process' );

			\WP_CLI::success( 'Synced ' . get_the_title() );
		}
	}

	private function parse_attack_text( $attack_text ) {
		$output_array = [];
		preg_match( '/\[([0-9A-Z]+)\+?\]\s((\w+\s)+)(\(([0-9x]+)\+?\))?/', wp_strip_all_tags( $attack_text ), $output_array );

		echo wp_strip_all_tags( $attack_text ) . "\n";
		print_r( $output_array );
		echo "\n";
	}
}
