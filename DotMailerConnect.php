<?php

namespace DotMailer\Api;

require_once __DIR__ . '/vendor/autoload.php';

use DotMailer\Api\DataTypes;

class DotMailerConnect {

	private $resources;

	public function __construct( array $credentials ) {
		if ( $credentials[Container::USERNAME] && $credentials[Container::PASSWORD] ) {
			$this->resources = Container::newResources( $credentials );
		}
	}

	function getAccountInfo() {

		if ( isset( $this->resources ) ) {

			try {
				return json_decode( $this->resources->GetAccountInfo(), true );
			}
			catch (Exception $e) {
				return false;
			}

		}

	}


    function listAddressBooks() {

		if ( isset( $this->resources ) ) {

			try {
				return json_decode( $this->resources->GetAddressBooks(), true );
			}
			catch (Exception $e) {
				return false;
			}

		}

    }


	function listDataFields() {

		if ( isset( $this->resources ) ) {

			try {
				return json_decode( $this->resources->GetDataFields(), true );
			}
			catch (Exception $e) {
				return false;
			}

		}

	}


	function AddContactToAddressBook( $email, $addressBookId, $datafields = [] ) {

		try {
			$apiContact = new DataTypes\ApiContact( array(
				'Id'		=> -1,
				'Email'		=> $email,
				'OptInType'	=> "Single",
				'EmailType'	=> "Html",
				'DataFields' => $datafields,
			) );

			return $this->createOrResubscribeContact( $addressBookId , $apiContact );
		}
		catch (Exception $e) {
			return false;
		}

	}

	function getContactByEmail( $email ) {

		try {
			return json_decode( $this->resources->GetContactByEmail( $email ), true );
		}
		catch (Exception $e) {
			return false;
		}

    }

	function reSubscribeContact( $email, $addressBookId, $datafields = [] ) {

		try {

			$apiContact = new DataTypes\ApiContact( array(
				'Id'		=> -1,
				'Email'		=> $email,
				'OptInType'	=> "Single",
				'EmailType'	=> "Html",
				'DataFields' => $datafields,
			) );

			$reSubscribeContact = new DataTypes\ApiContactResubscription( array (
				'UnsubscribedContact' => $apiContact,
				'PreferredLocale' => '',
				'ReturnUrlToUseIfChallenged' => '',
		 	) );

			return $this->createOrResubscribeContact( $addressBookId, $reSubscribeContact);

		}

		catch (Exception $e) {
			return false;
		}

    }

	private function createOrResubscribeContact( $addressBookId, $contact )
	{
		if ( $contact instanceof DataTypes\ApiContact ) {
			$result = $addressBookId == -1 ? $this->resources->PostContacts( $contact ) : $this->resources->PostAddressBookContacts( $addressBookId ,$contact );
		} else {
			$result = $addressBookId == -1 ? $this->resources->PostContactsResubscribe( $contact ) : $this->resources->PostAddressBookContactsResubscribe( $addressBookId ,$contact );
		}

		return json_decode( $result, true );
	}
}
