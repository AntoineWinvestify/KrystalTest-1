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
* @author Antoine de Poorter
* @version 0.7
* @date 2017-17-10
* @package
*/
/*
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

    public $actsAs = array('Containable');
    
    var $name = 'Investor';
    public $hasMany = array(
        'Accountowner' => array(
            'className' => 'Accountowner',
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
        ),
        'Globaldashboard' => array(
            'className' => 'Globaldashboard',
            'ForeignKey' => 'investor_id',
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

//    $Check = ClassRegistry::init('Check');
    /**
     * 	Apparently can contain any type field which is used in a field. It does NOT necessarily
     * 	have to map to a existing field in the database. Very useful for automatic checks
     * 	provided by framework
     */
    var $validate = [
        'investor_name' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 50],
                'allowEmpty' => false,
                'message' => 'Name validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change the Name',
                'on' => 'update'
            ]
        /* 'rule2' => array('rule' => 'alphaNumeric',
          'allowEmpty' => false,
          'message' => 'Name validation error'), */
        ],
        'investor_surname' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 50],
                'allowEmpty' => false,
                'message' => 'Surname validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your Surname',
                'on' => 'update'
            ]            
        /*   'rule2' => array('rule' => 'alphaNumeric',
          'allowEmpty' => false,
          'message' => 'Surname validation error'), */
        ],
        'investor_DNI' => [
            'length_rule' => ['rule' => ['lengthBetween', 3, 20],
                'allowEmpty' => false,
                'message' => 'Id validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your legal Identification',
                'on' => 'update'
            ]
        ],
        'investor_dateOfBirth' => [        
            'age_rule' => [
                'rule' => 'checkOver18',
                'message' => 'You must be over 18 years old'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your Date of birth',
                'on' => 'update'
            ]
        ],
        'investor_telephone' => [ 
            'length_rule' => ['rule' => ['lengthBetween', 4, 20],
                'allowEmpty' => false,
                'message' => 'Telephone validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change the Telephone number',
                'on' => 'update'
            ]            
        ],
        'investor_address1' => [ 
            'length_rule' => ['rule' =>  ['lengthBetween', 2, 60],
                'allowEmpty' => false,
                'message' => 'Address validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your address',
                'on' => 'update'
            ]
        ],
         'investor_address2' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 60],
                'allowEmpty' => false,
                'message' => 'Address validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your address',
                'on' => 'update'
            ]
        ],       
        'investor_postCode' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 45],
                'allowEmpty' => false,
                'message' => 'Postcode validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your postCode',
                'on' => 'update'
            ]
        ],
        'investor_city' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 45], 
                'allowEmpty' => false,
                'message' => 'City validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your city of residence',
                'on' => 'update'
            ]
        ],
        'investor_country' => [ 
            'length_rule' => ['rule' => ['lengthBetween', 2, 3],
                'allowEmpty' => false,
                'message' => 'Country validation error'
            ],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your country of residence',
                'on' => 'update'
            ]
        ],
        'investor_email' => [
            'complex_rule' => ['rule' => '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
                'allowEmpty' => false,
                'message' => 'Email validation error'],
            'writeprotected_rule' => [
                'rule' => 'writeProtected',
                'message' => 'You cannot change your email',
                'on' => 'update'
            ]
        ]
    ];

    
   var $defaultFields = [ 
        'investor' => ['id',            //causes problems with PATCH, is never r/w
                        'investor_name', 
                        'investor_surname',
                        'investor_DNI',
                        'investor_dateOfBirth',
                        'investor_telephone',
                        'investor_email',
                        'investor_address1', 
                        'investor_address2', 
                        'investor_postCode',
                        'investor_city',
                        'investor_country', 
                        'investor_language',
                      ],

        'winAdmin' => ['id', 
                        'investor_identity',
                        'investor_name', 
                        'investor_surname',
                        'investor_DNI',
                        'investor_dateOfBirth',
                        'investor_telephone',
                        'investor_email',
                        'investor_address1', 
                        'investor_address2', 
                        'investor_postCode',
                        'investor_city',
                        'investor_country', 
                        'investor_language',
                        'investor_accredited',            
                        'modified',
                        'created'
            ],              
        'superAdmin' => ['id', 
                        'investor_identity',
                        'investor_name', 
                        'investor_surname',
                        'investor_DNI',
                        'investor_dateOfBirth',
                        'investor_telephone',
                        'investor_email',
                        'investor_address1', 
                        'investor_address2', 
                        'investor_postCode',
                        'investor_city',
                        'investor_country', 
                        'investor_language',
                        'investor_accredited',
                        'modified',
                        'created'            
                      ],                
    ];   
    
    
    
    
    public function checkOver18($check) {                                       //Calculate age for validation
        $birthDate = explode("/", $check['investor_dateOfBirth']);
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
        if ($age < 18) {
            return false;
        }
        return true;
    }
    
    
    /**
     * 	Generates a GUID for an image
     * 	
     * 	@return string	
     */
    public function getGUID() {
        return CakeText::uuid();       
    }

    /**
     * 	Reads the status of the account of an investor
     * 
     * 	@param 		int	$investorReference  The database handler of the investor
     * 	@param 		bitmap	$statusBit          The "status characteristic" to be checked of the current account
     *                                                   The definition of the bitmap is defined in database table
     * 	@return 	int	> 0 characteristic assigned
     * 			0	characteristic not assigned
     *//*
    function readAccountCreationStatus($investorReference, $statusBit) {

        $currentStatus = $this->find('first', array('conditions' => array('Investor.id' => $investorReference),
            'fields' => 'investor_accountStatus',
            'recursive' => -1,
        ));
        return $currentStatus['Investor']['investor_accountStatus'] & $statusBit;
    }*/

    /**
     * 	Updates the status of the account of an investor
     * 
     * 	@param 		int	$investorReference	The database handler of the investor
     * 	@param 		bitmap	$addStatusBit		A new characteristic to be added to the current account creation status
     * 							The definition of the bitmap is defined in database table
     * 	@return 	boolean	true	All OK
     * 				false	Error occured
     *//*
    function updateAccountCreationStatus($investorReference, $addStatusBit) {

        $currentStatus = $this->find('first', array('conditions' => array('Investor.id' => $investorReference),
            'recursive' => -1,
        ));

        $tempStatus = $currentStatus['Investor']['investor_accountStatus'];
        $newStatus = $tempStatus + $addStatusBit;

        $data = array('id' => $investorReference,
            'investor_accountStatus' => $newStatus,
        );
        if ($this->save($data, $validate = true)) {
            echo __FILE__ . " " . __LINE__ . "<br>";
            return true;
        }
    }*/

    /** NOT YET FINISHED
     * 	De-activates a user. The corresponding data in "user" and "investor" is marked as 'deleted'
     * 
     * 	@param 	int $investor_Identity
     * 	@return boolean	true	All OK
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
     * 	Decreases the number of linkedaccounts of an investor
     * 
     * 	@param 		int		$investorReference	The database handler of the investor
     * 	@return 	boolean	true	All OK
     * 				false	Error occured					
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
     * 	Increases the number of linkedaccounts of an investor
     * 
     * 	@param int $investorId	Identifier of the investor
     * 	@return boolean	true	All OK, data has been saved
     * 				false	Error occured				
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
     * Read the check data
     * 
     * @param type $investorId
     * @return type
     */
    public function readCheckData($investorId) {
        $checkData = $this->Check->find('all', array('conditions' => array('investor_id' => $investorId)));
        return $checkData;
    }

    /**
     * 	Checks if current stored investment information of the user is recent enough
     * 	
     * 	@param 		int		$investorId	Database reference of the investor
     * 	@return 	boolean	true	New information is to be collected for this investor
     * 				false	Existing information is OK					
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
            $this->saveField('investor_linkedAccountChange', 0);                // remove change flag
            return true;
        }

        $this->Data = ClassRegistry::init('Data');

        $resultData = $this->Data->find('first', array("fields" => array("created"),
            "order" => "id DESC",
            "recursive" => -1,
            "conditions" => array("data_investorReference" => $resultInvestor['Investor']['investor_identity'])
        ));

        if (empty($resultData)) {                                               // No information exists for this investor
            return true;
        }

        if ($resultData['Data']['created'] < $cutoffTime) {                     // existing information is too old, so refresh
            return true;
        }
        return false;
    }

    /**
     * 	Translates the unique userReference to the database reference
     * 
     * 	@param 	string $investorReference Unique Identifier of the investor
     * 	@return int $investorId The database reference of the investor				
     */
    function investorReference2Id($investorReference) {
        $resultInvestor = $this->find("first", array("fields" => array("id"),
            "recursive" => -1,
            "conditions" => array("investor_identity" => $investorReference),
        ));
        return $resultInvestor['Investor']['id'];
    }

    
    
    
    
    
    
    
    
    /**
     * 	Save the data of a NEW investor
     * 
     * 	@param array $data Array of the data elements to be modified/saved for an 
     *                     existing "investor" table
     * 	@return 				
     */   
    public function investorDataSave($data) {
        $id = $this->find('first', ['fields' => ['Investor.id'],
                                    'conditions' => ['Investor.user_id' => $data['id']],
                                    'recursive' => -1]);

        $checks = $this->readCheckData($id['Investor']['id']);

        $infoInvestor = array(
            'id' => $id['Investor']['id'],
            'user_id' => $data['id'],
            'investor_name' => $data['investor_name'],
            'investor_surname' => $data['investor_surname'],
            'investor_DNI' => $data['investor_DNI'],
            'investor_dateOfBirth' => $data['investor_dateOfBirth'],
            'investor_telephone' => $data['investor_telephone'],
            'investor_address1' => $data['investor_address1'],
            'investor_postCode' => $data['investor_postCode'],
            'investor_city' => $data['investor_city'],
            'investor_country' => $data['investor_country'],
            'investor_email' => $data['investor_email'],
        );


        //Checks control, if check is 1 can't change the field in db
        foreach ($checks[0]['Check'] as $keyCheck => $check) {
            $checkField = strtolower(explode('_', $keyCheck)[1]);               //Get the check field name  check_name ----> name
            foreach ($infoInvestor as $keyData => $dataInvestor) {
                $dataField = strtolower(explode('_', $keyData)[1]);             //Get data field name  investor_name ---> name
                if ($checkField == $dataField && $check == CHECKED) {           //Compare names and unset array if data is CHECKED
                    unset($infoInvestor[$keyData]);
                    unset($this->validate[$keyData]);                           //Unset field validation, cant validate with a null field;
                }
            }
        }

        $this->set($infoInvestor);
        if ($this->validates()) {  //validation ok     
            $this->save($infoInvestor);
            $data = JSON_encode($infoInvestor);
            return 1 . "[" . 1 . "," . $data . ",";
        } 
        else {                     // validation false
            $errors = array('errors' => 'Form error', $this->validationErrors);
            $errors = json_encode($errors);
            return 0 . "[" . 0 . "," . $errors;
        }
    }

    /**
     * Get all data of a investor
     * 
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
     * Get the investor Identity by investor.id
     * 
     * @param int $id It is the investor's id
     * @return array $info It is all the investor's data
     */
    public function getInvestorIdentityByInvestorId($id) {
        $info = $this->find("first", array(
            'fields' => array('Investor.investor_identity'),
            'conditions' => array('Investor.id' => $id),
            'recursive' => -1,
        ));
        return $info['Investor']['investor_identity'];
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

    /*     * ************************* */
    /*     * GET INVESTOR SINGLE DATA* */
    /*     * ************************* */

    /**
     * Get investor id
     * 
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
     * 
     * @param type $userid
     * @return type
     */
    public function getInvestorUserId($investorId) {
        $data = $info = $this->find("all", array(
            'conditions' => array('Investor.id' => $investorId),
            'recursive' => -1,
        ));

        $id = $data[0]['Investor']['user_id'];
        return $id;
    }

    /**
     * Get investor identification code
     * 
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
     * 
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
     * 
     * @param type $id  id of related User table
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
            'check_email' => 1, // Cannot be changed directly by user
            'check_telephone' => 1, // Cannot be changed directly by user
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
     * 
     * @param type $checks
     * @param type $investorId
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
     * 	Get information of all investors according to the conditions as defined
     *  in $filterConditions.
     * 
     * 	@param 		string	$filterConditions     conditions of the investor
     * 	@return 	array	data of the investor	
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
 /*       if (!empty($this->data['Investor']['investor_tempCode'])) {    // A confirmation code has been generated
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
        */

        // Identify that the WebClient should request a new access token with the updated information
        if (!$created) {
            
            if (!empty($this->data['Investor']['investor_language']) ||
                    !empty($this->data['Investor']['investor_name']) ||
                    !empty($this->data['Investor']['investor_surname']
                    )) {           
                $this->data['Investor']['requireNewAccessToken'] = true;
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
    function beforeSave($options = array()) {

        // Store telephone number without spaces
        if (!empty($this->data['Investor']['investor_dateOfBirth'])) {
            $this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
        }
        if (!empty($this->data['Investor']['investor_telephone'])) {
            $this->data['Investor']['investor_telephone'] = str_replace(' ', '', $this->data['Investor']['investor_telephone']);
        } 
       
        // Observe that username is not saved to the Investor model, but only required for generating the "investor_identity"
        if (!$this->id && !isset($this->data[$this->alias][$this->primaryKey])) {
            $this->data['Investor']['investor_identity'] = $this->createInvestorReference($this->data['Investor']['investor_telephone'], $this->data['Investor']['investor_email']); 
        }    
    }

 
   
    /**
     * Check if an investor datum is write protected
     * 
     * @param int $investorId The internal database id of the investor
     * @param string $fieldName The name of the variable to check
     * @return boolean
     */
    public function isFieldWriteProtected($investorId, $fieldName) {
        $tempFieldName = explode("_", $fieldName);
        $fieldName = "check_" . $tempFieldName[1];

        $checkData = $this->Check->find('first', $params = ['conditions' => ['investor_id' => $investorId],
                                                  'fields' => $fieldName,
                                                  'recursive' => -1
                                                 ]);

        return $checkData['Check'][$fieldName];
    }  
    
    
    /**
     * Checks if a field is write protected
     * 
     * @param array $check An array with the data to be checked
     * @return boolean
     */
    public function writeProtected($check) { 
        
        $tempKey = array_keys($check);
        $key = $tempKey[0];

        if ($this->isFieldWriteProtected($this->data['Investor']['id'], $key)) {
            return false;   
        }
        return true;
    }    
    
 




    /**
     * CHECKING OF THE CONFIRMATION THE CODE (IN MESSAGE OBJECT) AS SENT VIA SMS IS 
     * NOT TAKEN INTO CONSIDERATION.
     * 
     * Create a new 'Investor' object with the minimum set of data: email/username, password
     * and telephone
     * 
     * @param array $userData The data required for defining a new investor
     * @return mixed false or the database reference of the created Investor object
     */
    public function api_addInvestor($userData) {  
        
        if (!array_key_exists('Investor', $userData)) {
            foreach ($userData as $key => $userItem) {
                $userData['Investor'][$key] = $userItem;
            }
        } 
      
        // Require the minimum set of data for creating the Investor object
        if (empty($userData['Investor']['investor_email']) || 
            empty($userData['Investor']['password']) ||
            empty($userData['Investor']['investor_telephone'])) {
                return false;
        }
        if ($this->save($userData, $validate = true)) {
            $investorId = $this->id;           
            $this->Check = ClassRegistry::init('Check');
           
            if (!$this->Check->api_addCheck($investorId, ['telephone', 'email', 'identity'])) {
                $this->delete($investorId);

                return false;
            }
            $checkId = $this->Check->id;

            $this->User = ClassRegistry::init('User');

            if (!$this->User->api_addUser($investorId, $userData['Investor']['investor_email'], 
                                                  $userData['Investor']['password'], 
                                                  $userData['Investor']['investor_email'])) {
              
                $this->Check->delete($checkId);                   
                $this->delete($investorId);
                return false;
            }
            
            $userId = $this->User->id;
   /*          
            $this->Pmessage = ClassRegistry::init('Pmessage');
            if (!$this->Pmessage->addPmessage($investorId)) {
                $this->Check->delete($checkId);
                $this->User->delete($userId);
                $this->delete($investorId);               
                return false;
            }
   */           
            $this->id = $investorId;
            $this->save(array('user_id' => $userId));
            return $investorId;
        }   
    }
    
    
    
    
    /**
     * THIS IS NOT THE COMPLETE VERSION, IN WHICH WE SHOULD HAVE A STATE IN INVESTOR
     * 
     * @param int $investorId The id of the investor to be deleted
     * @return boolean
     */
    public function api_deleteInvestor($investorId) {  
        $this->Check = ClassRegistry::init('Check'); 
        $this->User = ClassRegistry::init('User');    
      
        $resultInvestor = $this->find('first', $params = ['recursive' => -1,
                                                  'conditions' => ['id' => $investorId]
                                                 ]);

        $resultCheck = $this->Check->find('first', $params = ['recursive' => -1,
                                                  'conditions' => ['investor_id' => $investorId]
                                                 ]);    
       
        if ($resultInvestor) {
            $this->User->delete($resultInvestor['Investor']['user_id']);
            $this->Check->delete($resultCheck['Check']['id']);
            $this->delete($investorId);
            return true;
        }
        
        
    }
     
 
    /**
     * Determines if the current user (by means of its $investorId) is the direct or indirect owner
     * of the current Model. 
     * This functionality determines if a webclient may access the data of another webclient
     * with proper R/W permissions.
     * 
     * @param $investorId The internal reference of the investor Object
     * @param $id The internal reference of the Investor object to be checked
     * @return boolean  
     */
    public function isOwner($investorId, $id) {  
  //      echo __CLASS__ . "::"  .__FUNCTION__ . " " . __LINE__ . "<br>";         
        if ($investorId == $id) {
  //       echo __CLASS__ . "::" . __FUNCTION__ . " " . __LINE__ . " returning true<br>";            
            return true;
        }
  //        echo __CLASS__ . "::" . __FUNCTION__ . " " . __LINE__ . " returning false <br>";       
        return false;
    }
    
    
    /** 
     * Reads the list of defaultFields to read in case the webclient has not indicated any fields
     * in its GET request
     * 
     * @param $roleName The name of the role for whom the list of defaults fields is read
     * @return array  An array with the names of the fields. The names are the *internal* names of the fields
     */
    public function getDefaultFields($roleName) {
        return $this->defaultFields[$roleName];        
    }   
    
    
    
    
    
}
