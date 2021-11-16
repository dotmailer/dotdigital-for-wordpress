<?php

namespace DotMailer\Api;

use DotMailer\Api\DataTypes\ApiContact;

class DotMailerConnect {

    private $username;
    private $password;
	private $resources;

    function __construct( $username, $password ) {

		require_once( 'vendor/autoload.php' );

		$this->username = $username;
        $this->password = $password;
		$credentials = array(
		    Container::USERNAME => $username,
		    Container::PASSWORD => $password
		);

		if ( ( $this->username != null ) || ( $this->password != null ) ) $this->resources = Container::newResources( $credentials );

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

	// NEED TO TRY THIS!!!
	function ApiCampaignSend( $campaignID, $contactID ) {
		try {
			$apiCampaignSend = new DataTypes\ApiCampaignSend( array(
				'CampaignId' => $campaignID,
				'ContactIds' => '[' . $contactID . ']',
			) );

			return json_decode( $this->resources->PostCampaignsSend( $apiCampaignSend ), true );

		}
		catch (Exception $e) {
			return false;
		}
	}

	private function createOrResubscribeContact( $addressBookId, $contact )
	{
		if ( $contact instanceof ApiContact) {
			$result = $addressBookId == -1 ? $this->resources->PostContacts( $contact ) : $this->resources->PostAddressBookContacts( $addressBookId ,$contact );
		} else {
			$result = $addressBookId == -1 ? $this->resources->PostContactsResubscribe( $contact ) : $this->resources->PostAddressBookContactsResubscribe( $addressBookId ,$contact );
		}

		return json_decode( $result, true );
	}
}
