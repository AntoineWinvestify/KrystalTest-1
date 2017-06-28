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

  2016-10-18	  version 0.1
  function updateAccountCreationStatus										[Not OK, Not tested]


  2017-01-17	  version 0.2
  function investmentInformationUpdate added									[OK]

  2017-06-06 version 0.3
  getInvestorId									[OK]
  getInvestorIdentity									[OK]

  2017-06-23 version 0.4
  db relation


  Pending:
  function generateGUIDs(). 													[not Ok, not tested]



 */
class Investor extends AppModel {

    var $name = 'Investor';
    public $hasMany = array(
        'Linkedaccount' => array(
            'className' => 'Linkedaccount',
            'foreignKey' => 'investor_id',
            'fields' => '',
            'order' => '',
        ),
    );
    public $hasOne = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'investor_id',
            'fields' => '',
            'order' => '',
        ),
        'Check' => array(
            'joinTable' => 'checks',
            'foreignKey' => 'investor_id',
        ),
        'Ocr' => array(
            'className' => 'Ocr',
            'ForeignKey' => 'investor_id',
            'associationForeignKey' => 'ocr_id',
        )
    );

    /**
     * 	Apparently can contain any type field which is used in a field. It does NOT necessarily
     * 	have to map to a existing field in the database. Very useful for automatic checks
     * 	provided by framework
     */
    var $validate = array(
        'investor_name' => array(
            'rule1' => array('rule' => array('minLength', 1),
                'allowEmpty' => false,
                'message' => 'Name validation error'),
        /* 'rule2' => array('rule' => 'alphaNumeric',
          'allowEmpty' => false,
          'message' => 'Name validation error'), */
        ),
        'investor_surname' => array(
            'rule1' => array('rule' => array('minLength', 1),
                'allowEmpty' => false,
                'message' => 'Surname validation error'),
            'rule2' => array('rule' => 'alphaNumeric',
                'allowEmpty' => false,
                'message' => 'Surname validation error'),
        ),
        'investor_DNI' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Id validation error',
        ),
        'investor_dateOfBirth' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Date validation error',
        ),
        'investor_telephone' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Telephone validation error',
        ),
        'investor_address1' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Address validation error',
        ),
        'investor_postCode' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Postcode validation error',
        ),
        'investor_city' => array(
            'rule' => array(array('minLength', 1), 'alphaNumeric'),
            'allowEmpty' => false,
            'message' => 'City validation error',
        ),
        'investor_country' => array(
            'rule' => array('minLength', 1),
            'allowEmpty' => false,
            'message' => 'Country validation error',
        ),
        'investor_email' => array(
            'rule' => '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
            'allowEmpty' => false,
            'message' => 'Email validation error',
        ),
    );

    /**
     *
     * 	Generates a GUID for an image
     * 	
     * 	@return array  array with a GUIDs
     * 			
     */
    public function getGUID() {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000);                                    //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);       // "-"
            $uuid = chr(123)       // "{"
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125); // "}"
            return $uuid;
        }
        return $GUIDs;
    }

    /**
     *
     * 	Updates the status of the account of an investor
     * 	@param 		int	$investorReference  The database handler of the investor
     * 	@param 		bitmap	$statusBit          The "status characteristic" to be checked of the current account
     *                                                   The definition of the bitmap is defined in database table
     * 	@return 	int	> 0 characteristic assigned
     * 			0	characteristic not assigned
     */
    function readAccountCreationStatus($investorReference, $statusBit) {

        $currentStatus = $this->find('first', array('conditions' => array('Investor.id' => $investorReference),
            'fields' => 'investor_accountStatus',
            'recursive' => -1,
        ));
        return $currentStatus['Investor']['investor_accountStatus'] & $statusBit;
    }

    /**
     *
     * 	Updates the status of the account of an investor
     * 	@param 		int	$investorReference	The database handler of the investor
     * 	@param 		bitmap	$addStatusBit		A new characteristic to be added to the current account creation status
     * 							The definition of the bitmap is defined in database table
     * 	@return 	boolean	true	All OK
     * 				false	Error occured
     */
    function updateAccountCreationStatus($investorReference, $addStatusBit) {

        $currentStatus = $this->find('first', array('conditions' => array('Investor.id' => $investorReference),
            'recursive' => -1,
        ));

        $tempStatus = $currentStatus['Investor']['investor_accountStatus'];
        $newStatus = $tempStatus + $addStatusBit;

        $data = array('id' => $investorReference,
            'investor_accountStatus' => $newStatus,
        );
        $this->save($data, $validate = true);
        echo __FILE__ . " " . __LINE__ . "<br>";
        return true;
    }

    /** NOT YET FINISHED
     *
     * 	De-activates a user. The corresponding data in "user" and "investor" is marked as 'deleted'
     * 	@param 		int	$investor_Identity
     * 	@return 	boolean	true	All OK
     * 				false	Error occured
     * 						
     */
    function deActiveUser($investorIdentity) {
// Locate user
// Mark user record as deleted, put fake password
// Mark investor record as deleted
        $this->id = $investorId;
        $this->saveField('investor_linkedAccounts', $this->field('investor_linkedAccounts') - 1); // decrement by 1

        $this->id = $investorId;
        $this->saveField('investor_linkedAccountChange', 1); // Ideally should be in the beforeSave function, but
        // does not work
        return true;
    }

    /**
     *
     * 	Decreases the number of linkedaccounts of an investor
     * 	@param 		int		$investorReference	The database handler of the investor
     * 	@return 	boolean	true	All OK
     * 				false	Error occured
     * 						
     */
    function decreaseLinkedAccounts($investorId) {
        $this->id = $investorId;
        $this->saveField('investor_linkedAccounts', $this->field('investor_linkedAccounts') - 1); // decrement by 1

        $this->id = $investorId;
        $this->saveField('investor_linkedAccountChange', 1); // Ideally should be in the beforeSave function, but
        // does not work
        return true;
    }

    /**
     *
     * 	Increases the number of linkedaccounts of an investor
     * 	@param 		int		$investorId	Identifier of the investor
     * 	@return 	boolean	true	All OK, data has been saved
     * 				false	Error occured
     * 						
     */
    function increaseLinkedAccounts($investorId) {
        $this->id = $investorId;
        $this->saveField('investor_linkedAccounts', $this->field('investor_linkedAccounts') + 1); // increment by 1

        $this->id = $investorId;
        $this->saveField('investor_linkedAccountChange', 1); // Ideally should be in the beforeSave function, but
        // does not work
        return true;
    }

    /**
     *
     * 	Checks if current stored investment information of the user is recent enough
     * 	
     * 	@param 		int		$investorId	Database reference of the investor
     * 	@return 	boolean	true	New information is to be collected for this investor
     * 				false	Existing information is OK
     * 						
     */
    function investmentInformationUpdate($investorId) {

        Configure::load('p2pGestor.php', 'default');
        $refreshFrecuency = Configure::read('CollectNewInvestmentData');
        $cutoffTime = date("Y-m-d H:i:s", time() - $refreshFrecuency * 3600);
        $resultInvestor = $this->find("first", array("fields" => array("investor_identity", "investor_linkedAccountChange"),
            "recursive" => -1,
            "conditions" => array("id" => $investorId),
        ));

        if ($resultInvestor['Investor']['investor_linkedAccountChange'] == 1) {
            $this->id = $investorId;
            $this->saveField('investor_linkedAccountChange', 0);       // remove change flag
            return true;
        }

        $this->Data = ClassRegistry::init('Data');

        $resultData = $this->Data->find('first', array("fields" => array("created"),
            "order" => "id DESC",
            "recursive" => -1,
            "conditions" => array("data_investorReference" => $resultInvestor['Investor']['investor_identity'])
        ));

        if (empty($resultData)) {  // No information exists for this investor
            return true;
        }

        if ($resultData['Data']['created'] < $cutoffTime) {  // existing information is too old, so refresh
            return true;
        }
        return false;
    }

    /**
     *
     * 	Translates the unique userReference to the database reference
     * 	@param 		string	$investorReference Unique Identifier of the investor
     * 	@return 	int	$investorId The database reference of the investor
     * 					
     */
    function investorReference2Id($investorReference) {
        $resultInvestor = $this->find("first", array("fields" => array("id"),
            "recursive" => -1,
            "conditions" => array("investor_identity" => $investorReference),
        ));
        return $resultInvestor['Investor']['id'];
    }

    public function investorDataSave($datos) {
        $id = $this->find('first', array(
            'fields' => array(
                'Investor.id',
            ),
            'conditions' => array(
                'Investor.user_id' => $datos['id']),
            'recursive' => -1,));

        $data = array(
            'id' => $id['Investor']['id'],
            'user_id' => $datos['id'],
            'investor_name' => $datos['investor_name'],
            'investor_surname' => $datos['investor_surname'],
            'investor_DNI' => $datos['investor_DNI'],
            'investor_dateOfBirth' => $datos['investor_dateOfBirth'],
            'investor_telephone' => $datos['investor_telephone'],
            'investor_address1' => $datos['investor_address1'],
            'investor_postCode' => $datos['investor_postCode'],
            'investor_city' => $datos['investor_city'],
            'investor_country' => $datos['investor_country'],
            'investor_email' => $datos['investor_email'],
        );
        $this->set($data);
        if ($this->validates()) {  //validation ok     
            $this->save($data);
            $data = JSON_encode($data);
            return 1 . "[" . 1 . "," . $data . ",";
        } else {                     // validation false
            $errors = array('errors' => 'Form error', $this->validationErrors);
            $errors = json_encode($errors);
            return 0 . "[" . 0 . "," . $errors;
        }
    }

    public function investorGetInfo($id) {

        $info = $this->find("all", array(
            'conditions' => array('Investor.user_id' => $id),
            'recursive' => -1,
        ));
        return $info;
    }

    public function getInvestorId($userid) {
        $data = $this->investorGetInfo($userid);
        $id = $data[0]['Investor']['id'];
        return $id;
    }

    public function getInvestorIdentity($userid) {
        $data = $this->investorGetInfo($userid);
        $identity = $data[0]['Investor']['investor_identity'];
        return $identity;
    }

    /**
     * Read the cheack data
     * @param type $investorId
     * @return type
     */
    public function readCheckData($investorId) {
        $checkData = $this->Check->find('all', array('conditions' => array('investor_id' => $investorId)));
        return $checkData;
    }

    /**
     * Update the check data
     * @param type $checks
     * @param type $invesorId
     * @return int
     */
    public function updateCheckData($checks, $invesorId) {

        $checksArray = Array(
            'id' => $checks['id'],
            'investor_id' => $invesorId,
            'check_name' => $checks['name'],
            'check_surname' => $checks['surname'],
            'check_dni' => $checks['dni'],
            'check_dateOfBirth' => $checks['dateOfBirth'],
            'check_email' => $checks['email'],
            'check_telephone' => $checks['telephone'],
            'check_postCode' => $checks['postCode'],
            'check_address' => $checks['address'],
            'check_city' => $checks['city'],
            'check_country' => $checks['country'],
            'check_iban' => $checks['iban'],
            'check_cif' => $checks['cif'],
            'check_bussinesName' => $checks['bussinesName']
        );

        if ($this->Check->save($checksArray)) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * **** CALLBACK FUNCTIONS *****
     */

    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function afterSave($created, $options = array()) {
        if (!empty($this->data['Investor']['investor_tempCode'])) {    // A confirmation code has been generated
            $event = new CakeEvent('confirmationCodeGenerated', $this, array('id' => $this->id,
                'investor' => $this->data[$this->alias],
            ));
            $this->getEventManager()->dispatch($event);
        }

        if (!empty($this->data['Investor']['investor_accountStatus'])) {  // A user has succesfully and completely registered
            if (($this->data['Investor']['investor_accountStatus'] & QUESTIONAIRE_FILLED_OUT) == QUESTIONAIRE_FILLED_OUT) {
                $event = new CakeEvent('newUserCreated', $this, array('id' => $this->id,
                    'investor' => $this->data[$this->alias],
                ));
                $this->getEventManager()->dispatch($event);
            }
        }
    }

    /**
     *
     * 	Callback Function
     * 	Format the date
     *
     */
    public function afterFind($results, $primary = false) {

        foreach ($results as $key => $val) {
            if (isset($val['Investor']['investor_dateOfBirth'])) {
                $results[$key]['Investor']['investor_dateOfBirth'] = $this->formatDateAfterFind(
                        $val['Investor']['investor_dateOfBirth']);
            }
        }
        return $results;
    }

    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function beforeSave($created, $options = array()) {

// Store telephone number without spaces
        if (!empty($this->data['Investor']['investor_dateOfBirth'])) {
            $this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
        }
        if (!empty($this->data['Investor']['investor_telephone'])) {
            $this->data['Investor']['investor_telephone'] = str_replace(' ', '', $this->data['Investor']['investor_telephone']);
        }
    }

}
