<?php

/**
  // @(#) $Id$
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2009, http://yoursite                                   |
  // +-----------------------------------------------------------------------+
  // | This file is free software; you can redistribute it and/or modify     |
  // | it under the terms of the GNU General Public License as published by  |
  // | the Free Software Foundation; either version 2 of the License, or     |
  // | (at your option) any later version.                                   |
  // | This file is distributed in the hope that it will be useful           |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
  // | GNU General Public License for more details.                          |
  // +-----------------------------------------------------------------------+
  // | Author: Antoine de Poorter                                            |
  // +-----------------------------------------------------------------------+
  //
 * @author Antoine de Poorter
 * @version 0.2
 * @date 2017-03-04
 * @package
 *

  2016-10-18	  version 0.1
  function createAccount()                                                            [OK, tested]
  function readCode()                                                                 [OK, tested]
  function updateLastAccessed                                                         [OK, tested]


  2017-03-04	  version 0.2
  Modified functions readConfirmationCode and generateNewConfirmationCode. Both       [OK, tested]
  return an array.


  2017-04-02      version 0.3                                                         [Not OK, not tested]
  check if account exists with status UNCONFIRMED_ACCOUNT and delete it with new
  save attempt.
  Added new routine uncomfirmedUserExists()


  PENDING
  -

 */
App::uses('CakeEvent', 'Event');

class User extends AppModel {

    var $name = 'User';
    public $hasOne = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' => 'user_id',
            'fields' => '',
            'order' => '',
        ),
    );
 
    /**
     * 	Apparently can contain any type field which is used in a field. It does NOT necessarily 
     * 	have to map to a existing field in the database. Very useful for automatic checks 
     * 	provided by framework
     */
    public $validate = array(
        'username' => array(
            'ruleEmail' => array(
                'rule' => array('email', true),
                'message' => 'Email provided is not a valid email address',
            ),
            'ruleEmail' => array(
                'rule' => array('isUnique', true),
                'message' => 'Email provided is not available',
            ),
        ),
        'password' => array(
            'ruleName1_minLength' => array(
                'rule' => array('minLength', 8),
                'message' => 'Incorrect format. Your password should be at least 8 characters long and contain uppercase and lowercase characters and a number.',
            ),
            'personalizedAlgorythm' => array(
                'rule' => 'passwordAlgorythm',
                'message' => 'Incorrect format. Your password should be at least 8 characters long and contain uppercase and lowercase characters and a number.',
            ),
        )
    );

    /**
     * TO ADD if record has been taken, i.e. status = UNCONFIRMED_ACCOUNT. In the latter case
     * modify some data in the table, marking that user tried more then once
     *
     *  Create an account. 
     *  Will "re-use" an existing account if it is already in status UNCONFIRMED_ACCOUNT. Existing data will be 
     *  overwritten with the new data provided
     * 	
     * @param string $username 
     * @param string $userPassword   
     * @param string $telephone
     *
     * @return array[0]	true	account created
     * 				false	account not created, unknown error
     * 				array[1]	information about errorfield(s)
     */
    public function createAccount($username, $userPassword, $telephone, $country) {

        $data = array('username' => $username,
            'password' => $userPassword,
            'email' => $username,
        );


        $this->Investor = ClassRegistry::init('Investor');

        /* Deletes a record that has not yet been confirmed */
        $tempUserData = $this->isUncomfirmedAccount($username);

        if (!empty($tempUserData)) {
            print_r($tempUserData);
            $this->Investor->delete($tempUserData[0]['User']['investor_id']);
            $this->delete($tempUserData[0]['User']['id']);
        }

        if ($this->save($data, $validation = true)) {   // OK
            $userId = $this->id;
            $data = array('user_id' => $userId,
                'investor_identity' => $this->createInvestorReference(), // unique systemwide reference
                'investor_tempCode' => $this->createReference(),
                'investor_privacyPolicy' => 1,
                'investor_receivePublicityInformation' => 1,
                'investor_numberOfCodesSent' => 1,
                'investor_email' => $username,
                'investor_telephone' => $telephone,
                'investor_country' => $country,
                'investor_accountStatus' => UNCONFIRMED_ACCOUNT,
            );


            if ($this->Investor->save($data, $validation = true)) {                                   // OK
                $investorId = $this->Investor->id;
                // store this id in user model		
                $this->id = $userId;
                $this->save(array('investor_id' => $investorId));
                $result[0] = true;
            } else {
                $result[0] = false;
                $result[1] = $this->Investor->validationErrors;
                $this->delete($userId);
            }
        } else {                     // error occurred
            $result[0] = false;
            $result[1] = $this->validationErrors;
        }
        return $result;
    }

    /**
     *
     * 	Generate a new SMS confirmation code
     * 	
     * @param string	$username name of the user. Typically the email of the user
     * @return array	[0] requested code
     * 		[1] number of times a code has been requested 
     * 	
     */
    public function generateNewConfirmationCode($username) {

        $tempCode[0] = $this->createReference();    // generate a new code
        $resultUser = $this->find("first", array('conditions' => array('username' => $username),
            'recursive' => 0));

        $this->Investor = ClassRegistry::init('Investor');
        $this->Investor->id = $resultUser['User']['investor_id'];

        $data = array('investor_tempCode' => $tempCode[0],
            'investor_numberOfCodesSent' => $resultUser['Investor']['investor_numberOfCodesSent'] + 1);

        $resultUser = $this->Investor->save($data, $validate = true);
        $tempCode[1] = $resultUser['Investor']['investor_numberOfCodesSent'] + 1;
        return($tempCode);
    }

    /**
     *
     * 	Read the confirmation code used in the process of account registration/confirmation
     * 	
     * @param string	$username	name of the user
     * @return array				[0] requested code
     *                                       [1] number of times a code has been requested 
     * 	
     */
    public function readConfirmationCode($username) {
        $resultUser = $this->find('all', array('conditions' => array('User.username' => $username),
            'recursive' => 0,
        ));

        $codeInformation[0] = $resultUser[0]['Investor']['investor_tempCode'];
        $codeInformation[1] = $resultUser[0]['Investor']['investor_numberOfCodesSent'];
        return $codeInformation;
    }

    /**
     *
     * 	Reset the (counter) information related to a confirmation code used in the process of account registration/confirmation
     * 	
     * 	@param 		string	$username	name of the user
     * 	@return 	boolean	true/false
     * 	
     */
    public function resetConfirmationCodeInformation($username) {

        $resultUser = $this->find("all", array('conditions' => array('User.username' => $username),
            'recursive' => -1));

        $this->Investor = ClassRegistry::init('Investor');
        $this->Investor->id = $resultUser[0]['User']['investor_id'];
        $resultUser = $this->Investor->save(array('investor_numberOfCodesSent' => 0), $validate = true);
        return true;
    }

    /**
     *
     * 	Read the confirmation code used in the process of account registration
     * 	
     * 	@param 		string	$username	name of the user
     * 	@return 	string				internal DB reference or 
     * 	
     */
    public function username2Id($username) {
        $resultUser = $this->find('all', array('conditions' => array('User.username' => $username),
            'recursive' => 0,
        ));
        return($resultUser[0]['Investor']['id']);
    }

    /**
     *
     *   Update the field "lastAccessed" of the user table
     * 	
     *   @param 	string	$investorId		DB Reference of the investor object
     *   @return 	boolean	true	field updated
     * 			false	field NOT updated due to unspecified error
     * 	
     */
    public function updateLastAccessed($investorId) {

        $investorData = array('id' => $investorId,
            'lastAccessed' => date("Y-m-d H:i:s"),
        );

        $this->Investor = ClassRegistry::init('Investor');
        if ($this->Investor->save($investorData, $validation = true)) {
            return true;
        }
        return false;
    }

    /**
     *
     *  Checks if a user record with status UNCONFIRMED_ACCOUNT exist
     *  but it may have been "reserved" by current user.
     * 
     *  @param 	string	$username The username
     *  @return 	array   $userData the user data of an unconfirmed account 
     * 
     */
    public function isUncomfirmedAccount($username) {

        $resultUser = $this->find("all", array('conditions' => array('username' => $username),
            'recursive' => 0));

        if (empty($resultUser)) {
            return $resultUser;
        }
        if ($resultUser[0]['Investor']['investor_accountStatus'] == UNCONFIRMED_ACCOUNT) {
            return $resultUser;
        }

        $resultUser = [];
        return $resultUser;
    }

    /**
     *
     * 	Password algorithm. At least one lower case, one upper case character, at least one number and a symbol
     *
     */
    public function passwordAlgorythm($check) {

        $value = array_values($check);
        $value = $value[0];

        $a = preg_match('/[A-Z]/', $value);
        $b = preg_match('/[a-z]/', $value);
        $c = preg_match('/[0-9]/', $value);
//	$d = preg_match('/[!,%,&,@,#,$,^,*,?,_,~]/', $value);
        return ($a * $b * $c);
    }

    function checkCurrentPassword($check) {
        $this->id = AuthComponent::user('id');
        $password = $this->field('password');

        $pw = new SimplePasswordHasher;
        $hashedPassword = $pw::hash($check['currentPassword']);

        if ($password == $hashedPassword) {
            return true;
        }
        return false;
    }

    function passwordsMatch($check) {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->pw = $check['passwordNew2'];
        if ($this->pw1 == $check['passwordNew2'])
            return true;
        return false;
    }

    /** NOT USED ANYMORE??
     *
     * 	Generates and stores a new password which the user MUST change
     *
     */
    public function generateNewPassword($id) {

        $newRandomPassword = substr(md5(rand()), 0, 5) . date("Hs") . "!";
        $pw = new SimplePasswordHasher;
        $hashedPassword = $pw->hash($newRandomPassword);

        $this->User = ClassRegistry::init('User');
        $this->User->create();
        $this->User->id = $id;

        $this->save(array('password' => $hashedPassword,
            'newRandomPassword' => $newRandomPassword
                )
        );
    }

    /**
     *
     * 	Callback Function
     * 	Generates and stores a new password which the user SHOULD change
     *
     */
    public function beforeSave($options = array()) {

        if (isset($this->data[$this->alias]['password'])) {                         // in case a new user is defined
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }

    /**
     *
     * 	Callback Function
     * 	Rules are defined for what should happen when a database record is created or updated
     *
     */
    function afterSave($created, $options = array()) {

        if (!empty($this->data['User']['user_linkToken'])) {    // A password reset has been requested by the user
            echo "generate event newpasswordRequested<br>";
            $event = new CakeEvent('newPasswordRequested', $this, array('id' => $this->id,
                'user' => $this->data[$this->alias],
            ));

            $this->getEventManager()->dispatch($event);
        }

        return true;
    }

}
