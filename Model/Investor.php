<?php

/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2017, http://www.winvestify.com                         |
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
  function updateAccountCreationStatus						[Not OK, Not tested]

  2017-07-05
 * modified the rules in the $validate variable, mixing it 

  2017-01-17	  version 0.2
  function investmentInformationUpdate added					[OK]

  2017-06-06 version 0.3
  getInvestorId									[OK]
  getInvestorIdentity								[OK]

  2017-06-23 version 0.4
  db relation

 * [2017-07-03] Version 0.5
 * Update check data
 * File relation
 * 
 * [2017-07-04] Version 0.6
 * Create check data
 * 
 * [2017-07-10] Version 0.7
 * New Json array


  2017-07-05
 * modified the rules in the $validate variable, making it more flexible




  Pending:
  function generateGUIDs(). 							[not Ok, not tested]
  



 */
App::uses('CakeEvent', 'Event');

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
    public $hasAndBelongsToMany = array(
        'Ocrfile' => array(
            'className' => 'Ocrfile',
            'joinTable' => 'files_investors',
            'associationForeignKey' => 'file_id',
            'foreignKey' => 'investor_id',
        ),
    );

    /**
     * 	Apparently can contain any type field which is used in a field. It does NOT necessarily
     * 	have to map to a existing field in the database. Very useful for automatic checks
     * 	provided by framework
     */
    var $validate = array(
        'investor_name' => array(
            'rule1' => array('rule' => array('minLength', 2),
                'allowEmpty' => false,
                'message' => 'Name validation error'),
        /* 'rule2' => array('rule' => 'alphaNumeric',
          'allowEmpty' => false,
          'message' => 'Name validation error'), */
        ),
        'investor_surname' => array(
            'rule1' => array('rule' => array('minLength', 2),
                'allowEmpty' => false,
                'message' => 'Surname validation error'),
        /*   'rule2' => array('rule' => 'alphaNumeric',
          'allowEmpty' => false,
          'message' => 'Surname validation error'), */
        ),
        'investor_DNI' => array(
            'rule' => array('minLength', 3),
            'allowEmpty' => false,
            'message' => 'Id validation error',
        ),
        'investor_dateOfBirth' => array(
            'rule' => array('minLength', 6),
            'allowEmpty' => false,
            'message' => 'Date validation error',
        ),
        'investor_telephone' => array(
            'rule' => array('minLength', 4),
            'allowEmpty' => false,
            'message' => 'Telephone validation error',
        ),
        'investor_address1' => array(
            'rule' => array('minLength', 2),
            'allowEmpty' => false,
            'message' => 'Address validation error',
        ),
        'investor_postCode' => array(
            'rule' => array('minLength', 2),
            'allowEmpty' => false,
            'message' => 'Postcode validation error',
        ),
        'investor_city' => array(
            'rule' => array(array('minLength', 2), 'alphaNumeric'),
            'allowEmpty' => false,
            'message' => 'City validation error',
        ),
        'investor_country' => array(
            'rule' => array('minLength', 2),
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
        if ($this->createCheckdata($currentStatus['Investor']['id'])) {
            return true;
        }
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
     * Read the cheack data
     * @param type $investorId
     * @return type
     */
    public function readCheckData($investorId) {
        $checkData = $this->Check->find('all', array('conditions' => array('investor_id' => $investorId)));
        return $checkData;
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

    /**
     * Get all data of a investor
     * @param type $id
     * @return type
     */
    public function investorGetInfo($id) {

        $info = $this->find("all", array(
            'conditions' => array('Investor.user_id' => $id),
            'recursive' => -1,
        ));
        return $info;
    }

    /**
     * Get all data of a investor by investor.id
     * @param int $id It is the investor's id
     * @return array $info It is all the investor's data
     */
    public function getInvestorIdentityByInvestorId($id) {

        $info = $this->find("all", array(
            'conditions' => array('Investor.id' => $id),
            'recursive' => -1,
        ));
        return $info;
    }

    public function getJsonDataForPFP($id) {
        /* $options['joins'] = array(
          array('table' => 'ocrs',
          'alias' => 'Ocr',
          'type' => 'inner',
          'conditions' => array(
          'Investor.id = Ocr.investor_id'
          )
          )
          ); */

        $options['conditions'] = array(
            'Investor.id' => $id
        );
        $investorData = $this->find("all", $options);
        return $investorData;
    }
    
    /****************************/
    /**GET INVESTOR SINGLE DATA**/
    /****************************/

    /**
     * Get investor id
     * @param type $userid
     * @return type
     */
    public function getInvestorId($userid) {
        $data = $this->investorGetInfo($userid);
        $id = $data[0]['Investor']['id'];
        return $id;
    }
    
     /**
     * Get investor user_id
     * @param type $userid
     * @return type
     */
    public function getInvestorUserId($investorId) {
        $data = $info = $this->find("all", array(
            'conditions' => array('Investor.id' => $investorId),
            'recursive' => -1,
        ));
        print_r($data);
        $id = $data[0]['Investor']['user_id'];
        return $id;
    }
    

    /**
     * Get investor identification code
     * @param type $userid
     * @return type
     */
    public function getInvestorIdentity($userid) {
        $data = $this->investorGetInfo($userid);
        $identity = $data[0]['Investor']['investor_identity'];
        return $identity;
    }

    /**
     * Get investor dni
     * @param type $userid
     * @return type
     */
    public function getInvestorDni($userid) {
        $data = $this->investorGetInfo($userid);
        $dni = $data[0]['Investor']['investor_DNI'];
        return $dni;
    }

    
    
    /**
     * Create a check line in the checks table for the user
     * @param type $id
     * @return boolean
     */
    public function createCheckdata($id) {
        //Checks data
        $checksArray = Array(
            'investor_id' => $id,
            'check_name' => 0,
            'check_surname' => 0,
            'check_dni' => 0,
            'check_dateOfBirth' => 0,
            'check_email' => 1,
            'check_telephone' => 1,
            'check_postCode' => 0,
            'check_address' => 0,
            'check_city' => 0,
            'check_country' => 0,
            'check_iban' => 0,
            'check_ibanTime' => 0,
            'check_cif' => 0,
            'check_businessName' => 0,
        );
        if ($this->Check->save($checksArray)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the check data
     * @param type $checks
     * @param type $invesorId
     * @return int
     */
    public function updateCheckData($checks) {
        //Company id for mailing
        $compMail = array();
        //Get ocr id
        $ocr = $this->Ocr->findOcrId($checks['investorId']);

        //Get user id
        $userId = $this->find('first', array('fields' => 'user_id', 'conditions' => array('Investor.id' => $checks['investorId'])))['Investor']['user_id'];

        //Checks data
        $checksArray = Array(
            'id' => $checks['id'],
            'investor_id' => $checks['investorId'],
            'check_name' => ($checks['name']),
            'check_nameTime' => $checks['nameCheck'],
            'check_surname' => ($checks['surname']),
            'check_surnameTime' => $checks['surnameCheck'],
            'check_dni' => ($checks['dni']),
            'check_dniTime' => $checks['dniCheck'],
            'check_dateOfBirth' => ($checks['dateOfBirth']),
            'check_dateOfBirthTime' => $checks['dateOfBirthCheck'],
            'check_email' => ($checks['email']),
            'check_emailTime' => $checks['emailCheck'],
            'check_telephone' => ($checks['telephone']),
            'check_telephoneTime' => $checks['telephoneCheck'],
            'check_postCode' => ($checks['postCode']),
            'check_postCodeTime' => $checks['postCodeCheck'],
            'check_address' => ($checks['address']),
            'check_addressTime' => $checks['addressCheck'],
            'check_city' => ($checks['city']),
            'check_cityTime' => $checks['cityCheck'],
            'check_country' => ($checks['country']),
            'check_countryTime' => $checks['countryCheck'],
            'check_iban' => ($checks['iban']),
            'check_ibanTime' => $checks['ibanCheck'],
            'check_cif' => ($checks['cif']),
            'check_cifTime' => $checks['cifCheck'],
            'check_businessName' => ($checks['businessName']),
            'check_businessNameTime' => $checks['businessNameCheck'],
        );
        //Change status, at least one NO, status => ERROR, all YES status => OCR_PENDING
        //OCR_PENDING -> Default status change
        $statusFinal = OCR_PENDING;
        $statusArray = Array(
            'check_name' => ($checks['name']),
            'check_surname' => ($checks['surname']),
            'check_dni' => ($checks['dni']),
            'check_dateOfBirth' => ($checks['dateOfBirth']),
            'check_email' => ($checks['email']),
            'check_telephone' => ($checks['telephone']),
            'check_postCode' => ($checks['postCode']),
            'check_address' => ($checks['address']),
            'check_city' => ($checks['city']),
            'check_country' => ($checks['country']),
            'check_iban' => ($checks['iban']),
            'check_cif' => ($checks['cif']),
            'check_businessName' => ($checks['businessName']),
        );

        foreach ($statusArray as $status) {
            //If we find a NO, change the status
            if ($status == NO) {
                $statusFinal = ERROR;
            }
        }

echo false;        //If we click approve, change the status
        if ($checks['type'] == 'approve') {

            //Json path
            $fileConfig = Configure::read('files');
            $folder = $this->getInvestorIdentity($userId);
            $path = $fileConfig['investorPath'] . $folder;

            //Find investor info for the json
            $investorData = $this->find('first', array('conditions' => array('id' => $checks['investorId']), 'recursive' => -1));

            //Find ocr info for the json
            $ocrData = $this->Ocr->ocrGetData($checks['investorId']);

            //Json array, the json file is generated with this data.
            $jsonArray = Array(
                'name' => $investorData['Investor']['investor_name'],
                'check_nameTime' => $checks['nameCheck'],
                'surname' => $investorData['Investor']['investor_surname'],
                'check_surnameTime' => $checks['surnameCheck'],
                'dni' => $investorData['Investor']['investor_DNI'],
                'check_dniTime' => $checks['dniCheck'],
                'dateOfBirth' => $investorData['Investor']['investor_dateOfBirth'],
                'check_dateOfBirthTime' => $checks['dateOfBirthCheck'],
                'email' => $investorData['Investor']['investor_email'],
                'check_emailTime' => $checks['emailCheck'],
                'telephone' => $investorData['Investor']['investor_telephone'],
                'check_telephoneTime' => $checks['telephoneCheck'],
                'postCode' => $investorData['Investor']['investor_postCode'],
                'check_postCodeTime' => $checks['postCodeCheck'],
                'address' => $investorData['Investor']['investor_address1'],
                'check_addressTime' => $checks['addressCheck'],
                'city' => $investorData['Investor']['investor_city'],
                'check_cityTime' => $checks['cityCheck'],
                'country' => $investorData['Investor']['investor_country'],
                'check_countryTime' => $checks['countryCheck'],
                'iban' => $ocrData[0]['Ocr']['investor_iban'],
                'check_ibanTime' => $checks['ibanCheck']
            );
            if ($ocrData[0]['Ocr']['ocr_investmentVehicle'] == CHECKED) { //If we have investmentVehicle checked, write the cif and bussines name in the json
                array_push($jsonArray, array(
                    'cif' => $ocrData[0]['Ocr']['investor_cif'],
                    'check_cifTime' => $checks['cifCheck'],
                    'businessName' => $ocrData[0]['Ocr']['investor_businessName'],
                    'check_businessNameTime' => $checks['businessNameCheck']));
            }

            //Generate Json
            $created = $this->Ocrfile->generateJson($jsonArray, $path); //$JsonArray -> data for json, $path-> path where the json is created
            if ($created) {
                $statusFinal = OCR_FINISHED;

                //Change companies_ocr status
                foreach ($checks['company'] as $company) {

                    //Get id of the table
                    $companyOcrsId = $this->Ocr->getCompaniesOcrId($company['id'], $ocr);

                    //If company_ocr is accepted, send a mail
                    if ($company['status'] == ACCEPTED) {
                        $mail = $this->User->getPfpAdminMail($company['id']);
                    }

                    //Save company_ocr status
                    if ($this->Ocr->updateOcrCompanyStatus($companyOcrsId, $company['status'], $mail)) {
                        continue;
                    } else {
                        //error feedback
                        return [0, __("Error updating companies status.")];
                    }
                }
            } else {
                return [0, __("Error generating JSON.")];
            }
        }

        //Change files status
        foreach ($checks['file'] as $file) {
            if ($this->FilesInvestor->save(array('id' => $file['id'], 'file_status' => $file['status']))) {
                continue;
            } else {
                //error feedback
                return [0, __("Error updating files status.")];
            }
        }


        //Save the data
        if ($this->Check->save($checksArray)) {
            //Change ocr status

            if ($this->Ocr->save(array("id" => $ocr, "investor_id" => $checks['investorId'], "ocr_status" => $statusFinal))) {
                //Feedback
                return [1, __("Saved correctly.")];
            } else {
                //Feedback
                return [0, __("Error updating investor status.")];
            }
        } else {
            //Feedback
            return [0, __("Error saving.")];
        }
    }

    /**
     *
     * 	Get information of all investors according to the conditions as defined
     *  in $filterConditions.
     * 
     * 	@param 		string	$filterConditions     conditions of the investor
     * 	@return 	array	data of the investor
     * 					
     */
    public function getInvestorData($filterConditions) {

        $info = $this->find("all", array(
            'conditions' => array($filterConditions),
            'recursive' => -1,
        ));
        return $info;
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

        foreach ($results as $key => $val) {
            if (isset($val['Ocr']['investor_iban'])) {
                $results[$key]['Ocr']['investor_iban'] = $this->decryptDataAfterFind(
                        $val['Ocr']['investor_iban']);
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
