<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to model the Attack structure
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Model;

/**
 * Class to model the Attack structure
 *
 * @since 0.1.0
 */
class Attack {

	/**
	 * Stores the raw text of the attack
	 *
	 * @since 0.1.0
	 * @var string $raw
	 */
	private $raw = '';

	/**
	 * Stores the results of `preg_match`, typically the cost, name, and damage of the attack.
	 *
	 * @since 0.1.0
	 * @var array $match_results
	 */
	private $match_results = [];

	/**
	 * Stores any additional rules text for the attack
	 *
	 * @since 0.1.0
	 * @var string $text
	 */
	private $text = '';

	/**
	 * Construct an Attack object from the text from TCGPlayer.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param string $raw_text Text from TCGPlayer's API to be parsed.
	 */
	public function __construct( $raw_text ) {
		$this->raw     = $raw_text;
		$stripped_text = wp_strip_all_tags( $raw_text );

		preg_match( '/\[([0-9A-Z]+)\+?\]\s((\w+\s)+)(\(([0-9x]+)\+?\))?/', $stripped_text, $this->match_results );

		if ( strpos( $stripped_text, "\n" ) > 0 ) {
			$this->text = substr( $stripped_text, strpos( $stripped_text, "\n" ) + 1 );
		}
	}

	/**
	 * Energy cost of this attack. TODO: refactor to use EnergyType
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Energy cost for this attack.
	 */
	public function get_cost() {
		return $this->match_results[1] ?? 0;
	}

	/**
	 * Name of this attack
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Name of this attack.
	 */
	public function get_name() : string {
		return $this->match_results[2] ?? '';
	}

	/**
	 * Base damage for this attack. Does not take into account multipliers
	 * or additions. Status-only attacks return 0.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return int Base damage for this attack; 0 if not applicable.
	 */
	public function get_base_damage() : int {
		return $this->match_results[5] ?? 0;
	}

	/**
	 * Extra rules text for the attack. Empty string if not applicable.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return string Rules text for attack; empty if not applicable.
	 */
	public function get_text() : string {
		return $this->text;
	}

	/**
	 * Attack information as associative array for an update/insert post.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @return array Metadata arguments for this attack in associative array.
	 */
	public function get_post_args() {
		return [
			'cost'        => $this->get_cost(),
			'name'        => $this->get_name(),
			'base_damage' => $this->get_base_damage(),
			'text'        => $this->get_text(),
		];
	}
}
