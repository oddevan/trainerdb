<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class to handle importing content from
 * external APIs.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Import;

/**
 * Handle importing information from external APIs
 *
 * @since 0.1.0
 */
class TCGPlayerHelper {
  /**
	 * Store the access token for this session.
	 *
	 * @var string access_token Access Token for the TCGPlayer API.
	 */
  private $access_token = false;

	/**
	 * Construct the object
	 *
	 * @author Evan Hildreth
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->access_token = get_transient( 'tcgp_access_key' );
		if ( false === $this->access_token ) {
			$this->access_token = $this->tcgp_login();
		}
	}
	
	public function get_from_tcgp( string $endpoint ) : object {
		$response = wp_remote_get(
			'http://api.tcgplayer.com/v1.32.0/' . $endpoint,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $this->access_token,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);

		$api_response = json_decode( $response['body'] );
		return $api_response->results;
	}
	
	/**
	 * Use the TCGPlayer app ids to get a temporary access token and store it in the database.
	 *
	 * @return string access token for TCGPlayer
	 */
  private function tcgp_login() : string {
		if ( ! defined( 'TCGP_PUBLIC_ID' ) || ! defined( 'TCGP_PRIVATE_ID' ) ) {
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				\WP_CLI::error( 'Please make sure TCGP_PUBLIC_ID and TCGP_PRIVATE_ID are set in your wp-config' );
			}
			wp_die( '<b>Error in oddEvan\TrainerDB\Import\TCGPlayerHelper</b>: Please make sure TCGP_PUBLIC_ID and TCGP_PRIVATE_ID are set in your wp-config' );
		}

		$http_options = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
			'body'    => 'grant_type=client_credentials&client_id=' . TCGP_PUBLIC_ID . '&client_secret=' . TCGP_PRIVATE_ID,
		);

		$response = wp_remote_post( 'https://api.tcgplayer.com/token', $http_options );
		if ( empty( $response ) || ( 200 !== $response['response']['code'] && 201 !== $response['response']['code'] ) ) {
			// This function will display an error and stop the script.
			WP_CLI::error( 'Error connecting to TCGplayer. Response: ' . $response['body'] );
		}

		$token = json_decode( $response['body'] );
		set_transient( 'tcgp_access_key', $token->access_token, $token->expires_in );

		return $token->access_token;
	}
}
