<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model a card from TCGPlayer
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

/**
 * Class to extract data from a card from TCGPlayer
 *
 * @since 0.1.0
 */
class TcgPlayerCard extends Card {
	/**
	 * Store the full TCGPlayer API response for this Card
	 *
	 * @since 0.1.0
	 * @var object $api_response
	 */
	private $api_response;

	/**
	 * Store the parsed attributes for this Card
	 *
	 * @since 0.1.0
	 * @var object $card_attributes
	 */
	private $card_attributes;

	/**
	 * Store whether this is the normal or reverse printing.
	 *
	 * @since 0.1.0
	 * @var bool $is_reverse
	 */
	private $is_reverse = false;

	/**
	 * Store the Set object for this card
	 *
	 * @since 0.1.0
	 * @var Set $set
	 */
	private $set;

	private $sku = '';
	private $parallel_sku = '';

	/**
	 * Construct a Card from a parsed TCGPlayer API response
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param object          $tcgp_api_response Parsed JSON from TCGPlayer.
	 * @param TCGPlayerHelper $tcgp_helper Object to query TCGPlayer API.
	 * @throws Exception When Set cannot be created.
	 */
	public function __construct( $tcgp_api_response, $tcgp_helper ) {
		$this->api_response    = $tcgp_api_response;
		$this->card_attributes = $this->parse_tcg_card_info( $tcgp_api_response );
		$this->set             = Set::create_from_tcg_player_id( $this->api_response->groupId, $tcgp_helper );
		if ( ! $this->set ) {
			throw new Exception();
		}
		$this->parse_printings_and_skus();
	}

	/**
	 * Parse the Extended Data from TCGPlayer
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param object $tcgp_card Parsed JSON from TCGPlayer.
	 * @return object parsed Extended Data
	 */
	private function parse_tcg_card_info( $tcgp_card ) : object {
		$card_info = [];

		foreach ( $tcgp_card->extendedData as $edat ) { // phpcs:ignore
			switch ( $edat->name ) {
				case 'Number':
					$card_info['card_number'] = $edat->value;
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
					$card_info['attacks'][] = new Attack( $edat->value );
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

		return (object) $card_info;
	}

	/**
	 * Remove extra info from TCGP's card title (descriptors like "Full Art" or extra numbers)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param string $raw_title Raw title from TCGPlayer.
	 * @return string Title of the card
	 */
	private function normalize_title( string $raw_title ) : string {
		$clean_title = $raw_title;
		$delimiters  = [ '(', ' -' ];
		foreach ( $delimiters as $delimiter ) {
			if ( strpos( $clean_title, $delimiter ) > 0 ) {
				$clean_title = substr( $clean_title, 0, strpos( $clean_title, $delimiter ) );
			}
		}
		return $clean_title;
	}

	private function parse_printings_and_skus() {
		$printings = array_filter( $this->api_response->skus, function( $value ) {
			return 1 === $value->languageId && 1 === $value->conditionId; //phpcs:ignore
		});
		foreach ( $printings as $sku ) {
			if ( 77 === $sku->printingId ) { //phpcs:ignore
				$this->parallel_sku = $sku->skuId; //phpcs:ignore
			} else {
				$this->sku = $sku->skuId; //phpcs:ignore
			}
		}
	}

	/**
	 * Indicate if this TCGP card has a parallel set (reverse holo) printing
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return bool true if card has a parallel set printing
	 */
	public function has_parallel_printing() : bool {
		return $this->parallel_sku ? true : false;
	}

	/**
	 * Set whether this is the normal or parallel set printing
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param bool $is_reverse true if card is a parallel set printing.
	 */
	public function set_parallel_printing( bool $is_reverse ) {
		$this->is_reverse = $this->has_parallel_printing() && $is_reverse;
	}

	/**
	 * Get the TCGPlayer SKU for this card. We are assuming "Near Mint"
	 * condition.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string TCGPlayer SKU for this printing
	 */
	public function get_tcgplayer_sku() {
		return $this->get_reverse_holo() ? $this->parallel_sku : $this->sku;
	}

	public function get_tcgplayer_url() {
		return $this->api_response->url;
	}

	/**
	 * WP Post ID for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int WordPress Post ID
	 */
	public function get_post_id() : int {
		$check_query = new \WP_Query( [
			'meta_key'   => 'tcgp_sku',
			'meta_value' => $this->get_tcgplayer_sku(),
			'post_type'  => 'card',
			'fields'     => 'ids',
		] );

		return $check_query->post_count > 0 ? $check_query->posts[0] : 0;
	}

	/**
	 * CardType for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return CardType object representing this card's type
	 */
	public function get_card_type() { //: CardType {
		// Is this a Trainer card? Then use card_type.
		if ( in_array( $this->card_attributes->card_type, [ 'Item', 'Supporter', 'Stadium' ], true ) ) {
			return [ 'Trainer', $this->card_attributes->card_type ];
		}

		// Is it an Energy card? Then say so.
		// Strict comparison to `false` because `strpos` returning '0' is true for us.
		if ( false !== strpos( $this->card_attributes->card_type, 'Energy' ) ) {
			return [ 'Energy', 'Energy' === $this->card_attributes->card_type ? 'Special Energy' : $this->card_attributes->card_type ];
		}

		// It's a Pokémon card, so use Stage.
		return [ 'Pokemon', property_exists( $this->card_attributes, 'stage' ) ? $this->card_attributes->stage : 'Basic' ];
	}

	/**
	 * EnergyType for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType object representing this card's Energy type
	 */
	public function get_energy_type() { //: EnergyType {
		if ( ! in_array( $this->card_attributes->card_type, [ 'Item', 'Supporter', 'Stadium', 'Energy' ], true ) ) {
			return $this->card_attributes->card_type;
		}

		return null;
	}

	/**
	 * Title/Name for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Card title
	 */
	public function get_title() : string {
		return $this->normalize_title( $this->api_response->name );
	}

	/**
	 * Slug (unique identifier) for this card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Slug for this card
	 */
	public function get_slug() : string {
		return $this->set->get_prefix() . '-' . $this->get_card_number() . ( $this->get_reverse_holo() ? '-r' : '' );
	}

	/**
	 * Set this card belongs to
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return Set Set this card belongs to
	 */
	public function get_set( $get_post_args = false ) {
		return $get_post_args ? $this->set->get_term_id() : $this->set;
	}

	/**
	 * Card number within its set
	 * (String because some promos/reprints can have letters)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Card number for this card
	 */
	public function get_card_number() : string {
		$card_number    = $this->card_attributes->card_number;
		$number_matches = [];
		if ( strpos( $card_number, '/' ) > 0 ) {
			$card_number = substr( $card_number, 0, strpos( $card_number, '/' ) );
		}
		if ( preg_match( '/([a-z]+)0+([1-9]+)/', strtolower( $card_number ), $number_matches ) ) {
			$card_number = $number_matches[1] . $number_matches[2];
		}
		return $card_number ?? 0;
	}

	/**
	 * Whether this is a reverse holographic (parallel set) printing
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return bool True if card is parallel set
	 */
	public function get_reverse_holo() : bool {
		return $this->is_reverse && $this->has_parallel_printing();
	}

	/**
	 * Card text (extra rules, etc)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string card text
	 */
	public function get_card_text() : string {
		return is_null( $this->card_attributes->text ) ? '' : $this->card_attributes->text;
	}

	/**
	 * HP for this Pokémon. 0 if not a Pokémon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int HP for this pokemon. 0 if not applicable.
	 */
	public function get_hp() : int {
		if ( property_exists( $this->card_attributes, 'hp' ) ) {
			return $this->card_attributes->hp;
		}
		return 0;
	}

	/**
	 * Title of card this evolves from. Empty if not a pokemon card or if it is a Basic.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Title of card this evolves from. Empty if not applicable.
	 */
	public function get_evolves_from() : string {
		if ( property_exists( $this->card_attributes, 'stage' ) && 'Basic' !== $this->card_attributes->stage ) {
			return 'TODO find previous';
		}
		return '';
	}

	/**
	 * Retreat cost for this pokemon. 0 if not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int Retreat cost for this card. 0 if not applicable.
	 */
	public function get_retreat_cost() : int {
		if ( property_exists( $this->card_attributes, 'retreat_cost' ) ) {
			return $this->card_attributes->retreat_cost;
		}
		return 0;
	}

	/**
	 * Energy Type of this pokemon's weakness. Null if no weakness or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType Type of pokemon's weakness. Null if not applicable.
	 */
	public function get_weakness_type() { //} : EnergyType {
		return null;
	}

	/**
	 * Weakness modification. Usually 2x. Empty if no weakness or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Weakness modification. Empty if not applicable.
	 */
	public function get_weakness_mod() : string {
		if ( property_exists( $this->card_attributes, 'weakness' ) ) {
			return $this->card_attributes->weakness;
		}
		return '';
	}

	/**
	 * Energy Type of this pokemon's resistance. Null if no resistance or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType Type of pokemon's resistance. Null if not applicable.
	 */
	public function get_resistance_type() {//}: EnergyType {
		return null;
	}

	/**
	 * Resistance modification. Usually -20. Empty if no resistance or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Resistance modification. Empty if not applicable.
	 */
	public function get_resistance_mod() : string {
		if ( property_exists( $this->card_attributes, 'resistance' ) ) {
			return $this->card_attributes->resistance;
		}
		return '';
	}

	/**
	 * Array of attacks possessed by this pokemon. Empty if not a pokemon.
	 * Pass 'true' to get as Attack objects (default); 'false' to get as associative array
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param bool $get_post_args false to get Attack objects; true to get associative array.
	 * @return array Array of attacks as specified. Empty if not applicable.
	 */
	public function get_attacks( $get_post_args = false ) : array {
		if ( ! $this->card_attributes->attacks ) {
			return [];
		}

		if ( $get_post_args ) {
			$args = [];
			foreach ( $this->card_attributes->attacks as $attack ) {
				$args[] = $attack->get_post_args();
			}
			return $args;
		}

		return $this->card_attributes->attacks;
	}

	/**
	 * Ability for this card. Null if not a pokemon or has no ability.
	 * (PokéPower or any other static rule change on the card counts.)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return Ability Ability object for this card. Null if not applicable.
	 */
	public function get_ability() {//} : Ability {
		return null;
	}

	/**
	 * Get an argument array suitable for wp_insert_post
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @return array argument array for creating/updating post for this Card
	 */
	public function get_post_args() : array {
		$args = parent::get_post_args();

		$args['meta_input']['tcgp_sku'] = $this->get_tcgplayer_sku();
		$args['meta_input']['tcgp_url'] = $this->get_tcgplayer_url();

		return $args;
	}
}
