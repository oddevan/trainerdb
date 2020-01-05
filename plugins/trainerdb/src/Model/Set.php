<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Set taxonomy
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

use \WP_Term;
use oddEvan\TrainerDB\Import\TcgPlayerHelper;

/**
 * Class to model the Set object
 *
 * @since 0.1.0
 */
class Set {
	/**
	 * Store the WP_Term object this CardType represents.
	 *
	 * @since 0.1.0
	 * @var WP_Term $term the WP_Term object this object represents.
	 */
	private $term;

	/**
	 * Construct a Set object from a given WP_Term
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @param WP_Term $term WP_Term to create this Set from.
	 * @throws Exception Thrown if $term is not a Set.
	 */
	public function __construct( WP_Term $term ) {
		if ( 'set' !== $term->taxonomy ) {
			throw new Exception();
		}

		$this->term = $term;
	}

	/**
	 * Creates a new Set object from a given TCGPlayer ID. If a Set taxonomy exists
	 * with the given ID, it is used to create the object. Otherwise, a new Set is
	 * created using info from TCGP.
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @param int             $tcgp_id TCGPlayer API ID (collection id) of the set.
	 * @param TcgPlayerHelper $helper Initialized TCGPlayerHelper to make the API call.
	 * @return Set new Set object
	 */
	public static function create_from_tcg_player_id( $tcgp_id, $helper = null ) : Set {
		$term_query = new WP_Term_Query( [
			'taxonomy'   => [ 'set' ],
			'meta_key'   => 'tcgp_id',
			'meta_value' => $tcgp_id,
		] );

		if ( ! empty( $term_query ) && ! is_wp_error( $term_query ) ) {
			foreach ( $term_query->get_terms() as $set ) {
				return new Set( $set );
			}
		} else {
			$tcgp_set = $helper->get_set_info( $tcgp_id );

			return new Set( self::create_new_term( 'slug', 'name' ) );
		}
	}

	/**
	 * Create a new Set taxomomy with the given slug and name.
	 *
	 * @author Evan Hildreth <me@eph.me>
	 * @since 0.1.0
	 *
	 * @param string $slug short lowercase abbreviation for the set.
	 * @param string $name full name for the set.
	 * @return WP_Term created WP_Term object
	 */
	protected static function create_new_term( $slug, $name ) : WP_Term {
		$wp_term = get_term_by( 'slug', $slug, 'set' );
		if ( ! $wp_term ) {
			$result = wp_insert_term(
				$name,
				'set',
				[
					'name' => $name,
					'slug' => $slug,
				]
			);

			$wp_term = get_term_by( 'id', $result['term_id'], 'set' );
		}

		return $wp_term;
	}
}
