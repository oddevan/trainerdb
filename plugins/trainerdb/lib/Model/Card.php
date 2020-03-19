<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Card object
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

/**
 * Class to model the Card object. We can make cards from multiple sources,
 * so let's make an abstract class to model the base functionality.
 *
 * @since 0.1.0
 */
abstract class Card {
	/**
	 * WP Post ID for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int WordPress Post ID
	 */
	abstract public function get_post_id() : int;

	/**
	 * CardType for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return CardType object representing this card's type
	 */
	abstract public function get_card_type();// : CardType;

	/**
	 * EnergyType for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType object representing this card's Energy type
	 */
	abstract public function get_energy_type();// : EnergyType;

	/**
	 * Title/Name for this Card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Card title
	 */
	abstract public function get_title() : string;

	/**
	 * Slug (unique identifier) for this card
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Slug for this card
	 */
	abstract public function get_slug() : string;

	/**
	 * Set this card belongs to
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return Set|int Set this card belongs to
	 */
	abstract public function get_set( $get_post_args = false );

	/**
	 * Card number within its set
	 * (String because some promos/reprints can have letters)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Card number for this card
	 */
	abstract public function get_card_number() : string;

	/**
	 * Whether this is a reverse holographic (parallel set) printing
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return bool True if card is parallel set
	 */
	abstract public function get_reverse_holo() : bool;

	/**
	 * Card text (extra rules, etc)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string card text
	 */
	abstract public function get_card_text() : string;

	/**
	 * HP for this Pokémon. 0 if not a Pokémon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int HP for this pokemon. 0 if not applicable.
	 */
	abstract public function get_hp() : int;

	/**
	 * Title of card this evolves from. Empty if not a pokemon card or if it is a Basic.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Title of card this evolves from. Empty if not applicable.
	 */
	abstract public function get_evolves_from() : string;

	/**
	 * Retreat cost for this pokemon. 0 if not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int Retreat cost for this card. 0 if not applicable.
	 */
	abstract public function get_retreat_cost() : int;

	/**
	 * Energy Type of this pokemon's weakness. Null if no weakness or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType Type of pokemon's weakness. Null if not applicable.
	 */
	abstract public function get_weakness_type();// : EnergyType;

	/**
	 * Weakness modification. Usually 2x. Empty if no weakness or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Weakness modification. Empty if not applicable.
	 */
	abstract public function get_weakness_mod() : string;

	/**
	 * Energy Type of this pokemon's resistance. Null if no resistance or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return EnergyType Type of pokemon's resistance. Null if not applicable.
	 */
	abstract public function get_resistance_type();// : EnergyType;

	/**
	 * Resistance modification. Usually -20. Empty if no resistance or not a pokemon.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Resistance modification. Empty if not applicable.
	 */
	abstract public function get_resistance_mod() : string;

	/**
	 * Array of attacks possessed by this pokemon. Empty if not a pokemon.
	 * Pass 'false' to get as Attack objects (default); 'true' to get as associative array
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param bool $get_post_args false to get Attack objects; fatruelse to get associative array.
	 * @return array Array of attacks as specified. Empty if not applicable.
	 */
	abstract public function get_attacks( $get_post_args = false ) : array;

	/**
	 * Ability for this card. Null if not a pokemon or has no ability.
	 * (PokéPower or any other static rule change on the card counts.)
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return Ability Ability object for this card. Null if not applicable.
	 */
	abstract public function get_ability();// : Ability;

	/**
	 * Get an argument array suitable for wp_insert_post
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @return array argument array for creating/updating post for this Card
	 */
	public function get_post_args() : array {
		$this_ability = $this->get_ability();

		return [
			'ID'          => $this->get_post_id(),
			'post_type'   => 'card',
			'post_title'  => $this->get_title(),
			'post_status' => 'publish',
			'post_name'   => $this->get_slug(),
			'tax_input'   => [
				'set' => [ $this->get_set( true ) ],
			],
			'meta_input'  => [
				'card_number'         => $this->get_card_number(),
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
				'ability'             => $this_ability ? $this_ability->get_post_args() : null,
			],
		];
	}
}
