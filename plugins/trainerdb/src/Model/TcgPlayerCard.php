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

		return (object) $card_info;
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
	public function get_card_type() : CardType {
		return null;
	}

	/**
	 * EnergyType for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType object representing this card's Energy type
	 */
	public function get_energy_type() : EnergyType {
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
		return '';
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
		return null;
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
		return '';
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
		return false;
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
		return '';
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
	public function get_weakness_type() : EnergyType {
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
	public function get_resistance_type() : EnergyType {
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
		return '';
	}

	/**
	 * Array of attacks possessed by this pokemon. Empty if not a pokemon.
	 * Pass 'true' to get as Attack objects (default); 'false' to get as associative array
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param bool $get_post_args false to get Attack objects; fatruelse to get associative array.
	 * @return array Array of attacks as specified. Empty if not applicable.
	 */
	public function get_attacks( $get_post_args = false ) : array {
		return [];
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
	public function get_ability() : Ability {
		return null;
	}
}
