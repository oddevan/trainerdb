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
	private $is_reverse;

	/**
	 * Construct a Card from a parsed TCGPlayer API response
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param object $tcgp_api_response Parsed JSON from TCGPlayer.
	 */
	public function __construct( $tcgp_api_response ) {
		$this->api_response    = $tcgp_api_response;
		$this->card_attributes = $this->parse_tcg_card_info( $tcgp_api_response );
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
					$card_info['attacks'][] = new Attack( $edat->value, $this->helper );
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
		$delimiters  = [ '(', '-' ];
		foreach ( $delimiters as $delimiter ) {
			if ( strpos( $clean_title, $delimiter ) > 0 ) {
				$clean_title = substr( $clean_title, 0, strpos( $clean_title, $delimiter ) );
			}
		}
		return $clean_title;
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
		$printings = array_filter( $this->api_response->skus, function( $value ) {
			return 1 === $value->languageId && 1 === $value->conditionId; //phpcs:ignore
		});
		return false;
	}

	/**
	 * Set whether this is the normal or parallel set printing
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param bool $is_reverse true if card has a parallel set printing.
	 */
	public function set_parallel_printing( bool $is_reverse ) {
		$this->is_reverse = is_reverse;
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
		return 0;
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
		return '';
	}

	/**
	 * Set this card belongs to
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return Set Set this card belongs to
	 */
	public function get_set() : Set {
		return Set::create_from_tcg_player_id( $this->api_response->groupId, null );
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
		$card_number = $this->card_attributes->card_number;
		if ( strpos( $card_number, '/' ) > 0 ) {
			$card_number = substr( $card_number, 0, strpos( $card_number, '/' ) );
		}
		return is_null( $card_number ) ? 0 : $card_number;
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
		$args = super::get_post_args();
		unset( $args['id'] );
		return $args;
	}

	/**
	 * Get info for debugging
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @return array associative array for print_r
	 */
	public function debug_dump() {
		return [
			'Set'                 => $this->get_set(),
			'post_title'          => $this->get_title(),
			'post_name'           => $this->get_slug(),
			'card_number'         => $this->get_card_number(),
			'card_type'           => $this->get_card_type(),
			'energy_type'         => $this->get_energy_type(),
			'reverse_holographic' => $this->get_reverse_holo(),
			'card_text'           => $this->get_card_text(),
			'hp'                  => $this->get_hp(),
			'evolves_from'        => $this->get_evolves_from(),
			'retreat_cost'        => $this->get_retreat_cost(),
			'weakness_type'       => $this->get_weakness_type(),
			'weakness_mod'        => $this->get_weakness_mod(),
			'resistance_type'     => $this->get_resistance_type(),
			'resistance_mod'      => $this->get_resistance_mod(),
			'attacks'             => $this->get_attacks( true ),
			'ability'             => $this->get_ability(),
		];
	}
}
