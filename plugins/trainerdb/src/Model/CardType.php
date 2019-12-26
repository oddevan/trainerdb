<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Card Type taxonomy
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

use \WP_Term;

/**
 * Class to model the Card Type object
 *
 * @since 0.1.0
 */
class CardType {

	/**
	 * Store the WP_Term object this CardType represents.
	 *
	 * @since 0.1.0
	 * @var WP_Term $term the WP_Term object this object represents.
	 */
	private $term;

	public function __construct( string $term_slug ) {
		$wp_term = get_term_by( 'slug', $term_slug, 'card_type' );
		if ( ! $wp_term ) {
			// TODO create taxonomy.
		}

		$this->term = $wp_term;
	}
}
