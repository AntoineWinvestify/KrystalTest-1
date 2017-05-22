<?php

/**
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-02-29
 * @package
 *

  Version 0.1		2016-02-29







  Pending
  Phase 2 functions
  Cakify the function "importGoogleContacts"


 */
class InvitationsController extends AppController {

    var $name = 'Invitation';
    var $helpers = array('Html', 'Form', 'Js');
    var $uses = array('Invitation');
    var $error;
    var $layout = 'zastac_public_layout';

    function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow('cronValidateImportedEmails');    // allow all actions as these are public pages
    }

    /**
     *
     * 	A user creates an invitation for another user
     *
     *
     */
    public function createInvitation() {
        $result = array();

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $this->layout = 'ajax';
        $this->disableCache();

        foreach ($_REQUEST as $key => $value) {  // obtain name, surname, email, message
            $result['Invitation'][$key] = $_REQUEST[$key];
        }

        $result['Invitation']['invitation_parent'] = $this->Auth->user('Investor.id');

        $this->Promotion = ClassRegistry::init('Promotion');
        $result['Invitation']['invitation_accessCode'] = $this->Promotion->createSinglePromotionCode();

// store the information in DB for statistical purposes
        if (!$this->Invitation->Save($result, $validate = true)) {  // generate error exception so error modal pops up
            throw new
            InternalErrorException(__('Error sending the invitation'));
        }
    }

    /**
     *
     * 	A user creates an invitation for another user (main panel)
     * 	
     */
    public function createInvitationPanel() {
        
    }

    /**
     *
     * 	
     * 	
     *
     */
    public function deleteInvitation() {
        
    }

    /**
     *
     * 	
     * 	
     */
    public function editInvitationCode() {
//Configure::write('debug', 2);
//$this->autoRender = false;
    }

    /**
     *
     * 	Imports the contacts from an "external" provider like Google
     * 	
     */
    public function importGoogleContacts($providerName) {
        echo "providername = $providerName";

        $this->autoRender = false;
        Configure::write('debug', 2);
//	$contactsProvider = $_REQUEST['contactsProvider'];

        $client_id = '552443903358-udhmomb76d7muja0gaoj7d18nrbu04l9.apps.googleusercontent.com';
        $client_secret = 'v-QQK5qZbSMaKB510iUibIUM';
        $redirect_uri = 'https://www.zastac.com/invitations/importGoogleContacts';

        require_once APP . '/Plugin/GoogleAPI/Vendor/GoogleAPI/src/autoload.php';
        require_once APP . '/Plugin/GoogleAPI/Vendor/GoogleAPI/src/Client.php';
        /*         * **********************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
         * ********************************************** */
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);

        $client->addScope('profile');
        $client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        /*         * **********************************************
          Boilerplate auth management - see
          user-example.php for details.
         * ********************************************** */

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        } else
        if (isset($_GET['code'])) {
            // Received auth code from Google, exchange it for an access token, and
            // redirect to your base URL
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        } else

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            // You have an access token; use it to call the People API
            $client->setAccessToken($_SESSION['access_token']);
            $peopleService = new Google_Service_People($client);

            try {
                $people = $peopleService->people->get('people/me', $optParams = array('requestMask.includeField' => 'person.names,person.email_addresses,'));
//$this->print_r2($people);
                $userData = $people->getNames();
                $userDisplayName = $userData[0]['displayName'];
            } catch (Exception $e) {
//	Handle exception. You can also catch Exception here.
//	You can also get the error code from $e->getCode();
                echo 'error with authorized user data ' . $e->getMessage();
            }

            $mycontacts = array();
            try {
                $contacts = $peopleService->people_connections->listPeopleConnections('people/me', $optParams = array('requestMask.includeField' => 'person.names,person.email_addresses,'));
                foreach ($contacts as $item) {
                    $temp = $item->getEmailAddresses();
                    $tempContact['email'] = $temp[0]['value'];
                    $temp = $item->getNames();
                    $tempContact['displayName'] = $temp[0]['displayName'];
                    $tempContact['familyName'] = $temp[0]['familyName'];
                    $tempContact['givenName'] = $temp[0]['givenName'];
                    if ($userDisplayName <> $tempContact['displayName']) {
                        if (!empty($tempContact['email'])) {
                            $myContacts[] = $tempContact;  // Don't store the data of the authenticated user
                        }
                    }
                    $this->set('myContacts', $myContacts);
                    $this->set('userDisplayName', $userDisplayName);
//		$this->autoRender = true;	 
                }
                $this->print_r2($myContacts);
            } catch (Exception $e) {
                // Handle exception. You can also catch Exception here.
                // You can also get the error code from $e->getCode();
                echo 'error with list of user data ' . $e->getMessage();
            }

            $this->Session->delete('access_token');
            $contactData = array();

            $this->Contact = ClassRegistry::init('Contact');
            $totalCount = 0;
            foreach ($myContacts as $data) {
                $this->Contact->create();
                $this->print_r2($data);
                $contactData['investor_id'] = $this->Auth->user('Investor.id');
                $contactData['contact_familyName'] = $data['familyName'];
                $contactData['contact_givenName'] = $data['givenName'];
                $contactData['contact_middleName'] = $data['middleName'];
                $contactData['contact_tempEmail'] = $data['email'];
                $contactData['contact_importEntity'] = "Google";
                $contactData['contact_Id'] = $data['resourceName'];
                $contactData['contact_status'] = UNDEFINED_CONTACT_STATUS;

                $this->print_r2($contactData);

                if ($this->Contact->save($contactData, $validate = true)) {
                    $totalCount = $totalCount + 1;
                } else {
                    echo "Error(s) found,";
                }
                unset($contactData);
            }

            $this->autoRender = true;
            $this->redirect(
                    array('controller' => 'invitations', 'action' => 'createInvitationPanel'));
        } else {
            if ($providerName == 'Google') {
                $authUrl = $client->createAuthUrl();
                header("Location: " . $authUrl);
                die();
            }
        }

        if (strpos($client_id, "googleusercontent") == false) {
            echo missingClientSecretsWarning();
            exit;
        }
    }

    /**
     *
     * 	Displays a list of all the promotion codes and their characteristics.
     * 	Filters can be applied in order to reduce the number of entries
     *
     */
    public function showInvitationsList() {

//	$this->autoRender = false;

        $this->Startup->Behaviors->load('Containable');
        $resultStartupData = $this->Startup->find("all", $params = array(
            'conditions' => $conditions,
            'recursive' => 1,
            'contain' => 'Investoption',
        ));

        /*
          $resultBills = $this->Billdata->find("all", $params = array(
          'conditions' => $conditions,
          'recursive' => 1,
          'contain' =>'Studentdata',
          ));
         */


//	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  1,
//																'conditions'	=> array("id" => 1),
//																)
//										);
        $this->print_r2($resultStartupData);
        if (!empty($resultStartupData)) {    // User exists
        }
    }

//*********************************************************************************************
// CRONTAB OPERATIONS
//*********************************************************************************************

    /**
     *
     * 	Checks all unvalidated contacts and send an "invitation email" to each newly validated contact
     *
     */
    function cronValidateImportedEmails() {
        $this->autoRender = false;
        $this->Contact = ClassRegistry::init('Contact');

        $readMaxNumberOfUnconfirmedEmails = Configure::read('readMaxNumberOfUnconfirmedEmails');

        $conditions = array("AND" => array('contact_status' => UNDEFINED_CONTACT_STATUS)
        );

        $resultContacts = $this->Contact->find("all", $params = array('conditions' => $conditions,
            'recursive' => -1,
            'limit' => $readMaxNumberOfUnconfirmedEmails,
        ));

        foreach ($resultContacts as $contact) {
            $this->Investor = ClassRegistry::init('Investor');
            $resultInvestors = $this->Investor->find("all", $params = array(
                'conditions' => array('investor_email' => $contact['Contact']['contact_tempEmail']),
                'recursive' => -1,
            ));

            if (!empty($resultInvestors)) {     // contact is already a user, just delete contact
                $this->Contact->delete($contact['Contact']['id']);
            } else {
                $this->Contact->confirmEmailContact($contact['Contact']['contact_tempEmail']);
            }
        }
    }

}
