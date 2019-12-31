<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Attack structure
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

use oddEvan\TrainerDB\Import\TcgPlayerHelper;

/**
 * Class to model the Attack structure
 *
 * @since 0.1.0
 */
class Attack {
	private $raw = '';
	private $match_results = [];
	private $text = '';

	public function __construct( $raw_text ) {
		$this->raw     = $raw_text;
		$stripped_text = wp_strip_all_tags( $raw_text );

		preg_match( '/\[([0-9A-Z]+)\+?\]\s((\w+\s)+)(\(([0-9x]+)\+?\))?/', $stripped_text, $this->match_results );

		if ( strpos( $stripped_text, "\n" ) > 0 ) {
			$this->text = substr( $stripped_text, strpos( $stripped_text, "\n" ) + 1 );
		}
	}

	public function get_cost() {
		return $this->match_results[1] ?? 0;
	}

	public function get_name() {
		return $this->match_results[2] ?? '';
	}

	public function get_base_damage() {
		return $this->match_results[5] ?? 0;
	}

	public function get_text() {
		return $this->text;
	}

	public function get_post_args() {
		return [
			'cost'        => $this->get_cost(),
			'name'        => $this->get_name(),
			'base_damage' => $this->get_base_damage(),
			'text'        => $this->get_text(),
		];
	}
}
