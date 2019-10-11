<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Card object
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

/**
 * Class to model the Card object
 *
 * @since 0.1.0
 */
class Card {
	abstract public function get_title();
	abstract public function get_slug();
	abstract public function get_card_number();
	abstract public function get_reverse_holo();
	abstract public function get_card_text();
	abstract public function get_hp();
	abstract public function get_evolves_from();
}

/*
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
*/