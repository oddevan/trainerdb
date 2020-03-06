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
	public function adjust_quantity( $user_id, $card_id, $quantity ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'tdb_library';

		if ( $wpdb->query( $wpdb->prepare(
			'SELECT 1 FROM wp_tdb_library WHERE `user_id`=%d AND `card_id`=%s',
			$user_id,
			$card_id
		) ) ) {
			$wpdb->update(
				$table_name,
				[ 'quantity' => $quantity ],
				[
					'user_id' => $user_id,
					'card_id' => $card_id,
				],
				[ '%d' ],
				[ '%d', '%s' ]
			);
		} else {
			$wpdb->insert(
				$table_name,
				[
					'user_id'  => $user_id,
					'card_id'  => $card_id,
					'quantity' => $quantity,
				],
				[ '%d', '%s', '%d' ]
			);
		}
	}
}
