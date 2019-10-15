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
	abstract public function get_post_id() : int;
	abstract public function get_card_type() : CardType;
	abstract public function get_energy_type() : EnergyType;
	abstract public function get_title() : string;
	abstract public function get_slug() : string;
	abstract public function get_card_number() : string;
	abstract public function get_reverse_holo() : bool;
	abstract public function get_card_text() : string;
	abstract public function get_hp() : int;
	abstract public function get_evolves_from() : string;
	abstract public function get_retreat_cost() : int;
	abstract public function get_weakness_type() : EnergyType;
	abstract public function get_weakness_mod() : string;
	abstract public function get_resistance_type() : EnergyType;
	abstract public function get_resistance_mod() : string;
	abstract public function get_attacks() : array;
	abstract public function get_ability() : array;

	/**
	 * Get an argument array suitable for wp_insert_post
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @return array argument array for creating/updating post for this Card
	 */
	public function get_post_args() : array {
		return [
			'ID'          => $this->get_post_id(),
			'post_type'   => 'card',
			'post_title'  => $this->get_title(),
			'post_status' => 'publish',
			'post_name'   => $this->get_slug(),
			'meta_input'  => [
				'card_number'         => $this->get_card_number,
				//'ptcg_id'             => $has_ptcg ? $ptcg_cards[ $card_number ]['ptcg_id'] : '!err',
				//'tcgp_id'             => $sku->skuId,
				//'tcgp_url'            => $tcgp_card->url,
				'reverse_holographic' => $this->get_reverse_holo(),
				//'image_url'           => $has_ptcg ? $ptcg_cards[ $card_number ]['image_url'] : $tcgp_card->imageUrl,
				'card_text'           => $this->get_card_text(),
				'hp'                  => $this->get_hp(),
				'evolves_from'        => $this->get_evolves_from(),
				'retreat_cost'        => $this->get_retreat_cost(),
				'weakness_type'       => $this->get_weakness_type(),
				'weakness_mod'        => $this->get_weakness_mod(),
				'resistance_type'     => $this->get_resistance_type(),
				'resistance_mod'      => $this->get_resistance_mod(),
				'attacks'             => $this->get_attacks(),
				'ability'             => $this->get_ability(),
			],
		];
	}
}
