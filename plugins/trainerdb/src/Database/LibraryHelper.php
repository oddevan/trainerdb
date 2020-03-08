<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class LibrayHelper for TrainerDB
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Database;

/**
 * Class LibraryHelper
 *
 * Methods for interacting with the Library custom table
 *
 * @since 0.1.0
 */
class LibraryHelper {
	/**
	 * Adjust the quantity of the entry with the given user and card ids. A new
	 * entry will be created if none already exists.
	 *
	 * @param int    $user_id WP User ID of the user's library.
	 * @param string $card_id Slug of the card in question.
	 * @param int    $quantity Signed integer of how to adjust the quantity.
	 * @return int New quantity.
	 */
	public function adjust_quantity( $user_id, $card_id, $quantity ) : int {
		global $wpdb;
		$table_name = $wpdb->prefix . 'tdb_library';

		$existing_entry = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE `user_id`=%d AND `card_id`=%s", //phpcs:ignore
			$user_id,
			$card_id
		) );

		if ( null !== $existing_entry ) {
			$new_quantity = $quantity + $existing_entry->quantity;

			$wpdb->update(
				$table_name,
				[ 'quantity' => $new_quantity ],
				[ 'id' => $existing_entry->id ],
				[ '%d' ],
				[ '%d' ]
			);

			return $new_quantity;
		}

		$wpdb->insert(
			$table_name,
			[
				'user_id'  => $user_id,
				'card_id'  => $card_id,
				'quantity' => $quantity,
			],
			[ '%d', '%s', '%d' ]
		);

		return $quantity;
	}
}
