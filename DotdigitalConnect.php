<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotdigital\V2\Client;
use Dotdigital\V2\Models\Contact;
use Dotdigital\V2\Models\ContactList;

class DotdigitalConnect {

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @param array $credentials
	 */
	public function __construct( array $credentials ) {
		$this->client = new Client();
		if ( $credentials['username'] && $credentials['password'] ) {
			$this->client::setApiUser( $credentials['username'] );
			$this->client::setApiPassword( $credentials['password'] );

			$endpoint = get_option( 'dm_api_endpoint' ) ?: 'https://r1-api.dotdigital.com';
			$this->client::setApiEndpoint( $endpoint );
		}
	}

	/**
	 * @return \Dotdigital\V2\Models\AccountInfo|bool
	 * @throws \Http\Client\Exception
	 */
	public function getAccountInfo() {
		try {
			return $this->client->accountInfo->show();
		} catch ( Dotdigital\Exception\ResponseValidationException $e ) {
			return false;
		}
	}

	/**
	 * @return \Dotdigital\V2\Models\AddressBook[]
	 * @throws \Http\Client\Exception
	 */
	public function listAddressBooks() {
		$apiLists = $this->client->addressBooks->show();
		return $apiLists->getList();
	}

	/**
	 * @return \Dotdigital\V2\Models\DataField[]
	 * @throws \Http\Client\Exception
	 */
	public function listDataFields() {
		$apiDataFields = $this->client->dataFields->show();
		return $apiDataFields->getList();
	}

	/**
	 * @param string $email
	 * @param string $addressBookId
	 * @param array  $datafields
	 *
	 * @return bool
	 * @throws \Http\Client\Exception
	 */
	public function addContactToAddressBook( $email, $addressBookId, $datafields = array() ) {
		try {
			$apiContact = new Dotdigital\V2\Models\Contact(
				array(
					'id'        => -1,
					'email'     => $email,
					'optInType' => 'Single',
					'emailType' => 'Html',
					'dataFields' => $datafields,
				)
			);

			$result = $addressBookId == -1 ?
				$this->client->contacts->postContacts( $apiContact->getEmail() ) :
				$this->client->addressBooks->addContactToAddressBook(
					$addressBookId,
					$apiContact->getEmail(),
					$apiContact->getDataFields(),
					$apiContact->getOptInType(),
					$apiContact->getEmailType()
				);
		} catch ( Exception $e ) {
			return false;
		}

		return (bool) $result->getId();
	}

	/**
	 * @param string $email
	 * @param string $addressBookId
	 * @param array  $datafields
	 *
	 * @return bool
	 * @throws \Dotdigital\Exception\ResponseValidationException
	 * @throws \Http\Client\Exception
	 */
	public function resubscribeContactToAddressBook( $email, $addressBookId, $datafields = array() ) {
		$apiContact = new Dotdigital\V2\Models\Contact(
			array(
				'id'        => -1,
				'email'     => $email,
				'optInType' => 'Single',
				'emailType' => 'Html',
				'dataFields' => $datafields,
			)
		);

		$result = $addressBookId == -1 ? $this->client->contacts->resubscribe( $apiContact->getEmail(), $apiContact->getDataFields() ) :
			$this->client->addressBooks->resubscribeContactToAddressBook( $addressBookId, $apiContact->getEmail(), $apiContact->getDataFields() );

		return ( $result instanceof ContactList ) && property_exists( $result, 'contact' );
	}

	/**
	 * @param string $email
	 *
	 * @return Contact|false
	 */
	public function getContactByEmail( $email ) {
		try {
			return $this->client->contacts->getByEmail( $email );
		} catch ( Http\Client\Exception $e ) {
			return false;
		}
	}
}
