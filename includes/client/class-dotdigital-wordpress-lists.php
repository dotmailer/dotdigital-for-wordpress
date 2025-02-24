<?php
/**
 * Implementation of list requests.
 *
 * @package    Dotdigital_WordPress
 */

namespace Dotdigital_WordPress\Includes\Client;

use Dotdigital\V2\Resources\AddressBooks;
use Dotdigital_WordPress\Includes\Setting\Dotdigital_WordPress_Config;
use Exception;

class Dotdigital_WordPress_Lists {

	private const SELECT_LIMIT = 1000;

	private const LIST_LIMIT = 5000;

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
			try {
				$iteration = 0;
				$limit = self::LIST_LIMIT;
				$formatted_lists = array();

				do {
					$lists = $this->dotdigital_client->get_client()
						->addressBooks
						->show( $iteration, self::SELECT_LIMIT );
					foreach ( $lists->getList() as $list ) {
						$formatted_lists[ $list->getId() ] = $list;
					}
					$iteration += self::SELECT_LIMIT;
					$list_count = count( $lists->getList() );
				} while ( $iteration < $limit && self::SELECT_LIMIT == $list_count );

			} catch ( Exception $exception ) {
				throw $exception;
			}

			set_transient(
				'dotdigital_wordpress_api_lists',
				$formatted_lists,
				Dotdigital_WordPress_Config::CACHE_LIFE
			);
		}
		return $formatted_lists;
	}
}
