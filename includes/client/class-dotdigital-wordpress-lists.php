<?php
/**
 * Implementation of list requests.
 *
 * @package    Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Includes\Client;

use Dotdigital\V2\Resources\AddressBooks;

class Dotdigital_WordPress_Lists {

	/**
	 * Dotdigital client.
	 *
	 * @var Dotdigital_WordPress_Client $dotdigital_client
	 */
	private $dotdigital_client;

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->dotdigital_client = new Dotdigital_WordPress_Client();
	}

	/**
	 * Get lists.
	 *
	 * @return array
	 * @throws \Http\Client\Exception If request fails.
	 */
	public function get() {
		$formatted_lists = get_transient( 'dotdigital_wordpress_api_lists' );
		if ( ! $formatted_lists ) {
			$formatted_lists = array();
			try {
				do {
					$lists = $this->dotdigital_client->get_client()->addressBooks->show( count( $formatted_lists ) );
					foreach ( $lists->getList() as $list ) {
						$formatted_lists[ $list->getId() ] = $list;
					}
					$count_fetched_lists = count( $lists->getList() );
				} while ( AddressBooks::SELECT_LIMIT === $count_fetched_lists );
			} catch ( \Exception $exception ) {
				throw $exception;
			}

			set_transient( 'dotdigital_wordpress_api_lists', $formatted_lists, 15 );
		}
		return $formatted_lists;
	}
}
