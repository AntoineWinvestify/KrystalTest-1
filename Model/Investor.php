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




Pending:
function generateGUIDs(). 													[not Ok, not tested]



*/

class Investor extends AppModel
{
	var $name= 'Investor';


	public $hasMany = array(
			'Linkedaccount' => array(
				'className' => 'Linkedaccount',
				'foreignKey' => 'investor_id',
				'fields' => '',
				'order' => '',
				),
			);

	public $hasOne = array (
				'User' => array(
				'className' => 'User',
				'foreignKey' => 'investor_id',
				'fields' => '',
				'order' => '',
				),
			);





/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();







/**
*
*	Generates a GUID for an image
*	
* 	@return array  array with a GUIDs
*			
*/
public function getGUID() {
   if (function_exists('com_create_guid')){
        return com_create_guid();
    }
	else{
        mt_srand((double)microtime()*10000);                                    //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);							// "-"
        $uuid = chr(123)							// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
	return $GUIDs;	
}





/**
*
*	Updates the status of the account of an investor
*	@param 		int	$investorReference  The database handler of the investor
*	@param 		bitmap	$statusBit          The "status characteristic" to be checked of the current account
*                                                   The definition of the bitmap is defined in database table
* 	@return 	int	> 0 characteristic assigned
* 			0	characteristic not assigned
*/
function readAccountCreationStatus($investorReference, $statusBit) {

	$currentStatus = $this->find('first', array('conditions' => array('Investor.id' => $investorReference),
                                                                            'fields' 	=> 'investor_accountStatus', 
                                                                            'recursive' => -1,
							));
	return $currentStatus['Investor']['investor_accountStatus'] & $statusBit;
}





/**
*
*	Updates the status of the account of an investor
*	@param 		int	$investorReference	The database handler of the investor
*	@param 		bitmap	$addStatusBit		A new characteristic to be added to the current account creation status
*							The definition of the bitmap is defined in database table
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
        print_r($data);
	$this->save($data, $validate = true); 
echo __FILE__ . " " . __LINE__  ."<br>";
	return true;
}







/** NOT YET FINISHED
*
*	De-activates a user. The corresponding data in "user" and "investor" is marked as 'deleted'
*	@param 		int	$investor_Identity
*	@return 	boolean	true	All OK
* 				false	Error occured
* 						
*/
function deActiveUser($investorIdentity) {
// Locate user
// Mark user record as deleted, put fake password
// Mark investor record as deleted
    $this->id = $investorId;
    $this->saveField('investor_linkedAccounts',$this->field('investor_linkedAccounts')-1);	// decrement by 1

    $this->id = $investorId;
    $this->saveField('investor_linkedAccountChange', 1);	// Ideally should be in the beforeSave function, but
								// does not work
	return true;
}






/**
*
*	Decreases the number of linkedaccounts of an investor
*	@param 		int		$investorReference	The database handler of the investor
* 	@return 	boolean	true	All OK
* 				false	Error occured
* 						
*/
function decreaseLinkedAccounts($investorId) {
    $this->id = $investorId;
    $this->saveField('investor_linkedAccounts',$this->field('investor_linkedAccounts')-1);	// decrement by 1

    $this->id = $investorId;
    $this->saveField('investor_linkedAccountChange', 1);	// Ideally should be in the beforeSave function, but
								// does not work
	return true;
}





/**
*
*	Increases the number of linkedaccounts of an investor
*	@param 		int		$investorId	Identifier of the investor
* 	@return 	boolean	true	All OK, data has been saved
* 				false	Error occured
* 						
*/
function increaseLinkedAccounts($investorId) {
    $this->id = $investorId;
    $this->saveField('investor_linkedAccounts',$this->field('investor_linkedAccounts')+1);	// increment by 1

    $this->id = $investorId;
    $this->saveField('investor_linkedAccountChange', 1);	// Ideally should be in the beforeSave function, but
								// does not work
	return true;
}





/**
*
*	Checks if current stored investment information of the user is recent enough
*	
*	@param 		int		$investorId	Database reference of the investor
* 	@return 	boolean	true	New information is to be collected for this investor
* 				false	Existing information is OK
* 						
*/
function investmentInformationUpdate($investorId) {
	
    Configure::load('p2pGestor.php', 'default');
    $refreshFrecuency = Configure::read('CollectNewInvestmentData');
    $cutoffTime = date("Y-m-d H:i:s", time() - $refreshFrecuency * 3600);
    $resultInvestor = $this->find("first", array("fields"	=> array("investor_identity", "investor_linkedAccountChange"),
						 "recursive" => -1,
						"conditions" => array("id" => $investorId),
				));

	if ($resultInvestor['Investor']['investor_linkedAccountChange'] == 1) {
		$this->id = $investorId;
		$this->saveField('investor_linkedAccountChange', 0);							// remove change flag
		return true;
	}
		
	$this->Data = ClassRegistry::init('Data');

        $resultData = $this->Data->find('first', array( "fields"	=> array("created"),
									"order" => "id DESC",
									"recursive" => -1,
									"conditions" => array("data_investorReference" => $resultInvestor['Investor']['investor_identity'])
									  ));
	
	if (empty($resultData)) {		// No information exists for this investor
		return true;
	}
	
	if ($resultData['Data']['created'] < $cutoffTime) {		// existing information is too old, so refresh
		return true;
	}
	return false;
}





/**
*
*	Translates the unique userReference to the database reference
*	@param 		string	$investorReference Unique Identifier of the investor
* 	@return 	int	$investorId The database reference of the investor
* 					
*/
function investorReference2Id($investorReference) {
	$resultInvestor = $this->find("first", array("fields"	=> array("id"),
						 "recursive" => -1,
						"conditions" => array("investor_identity" => $investorReference),
                                    ));
	return $resultInvestor['Investor']['id'];
}






/*
***** CALLBACK FUNCTIONS *****
*/

/**
*
*	Rules are defined for what should happen when a database record is created or updated.
*	
*/
function afterSave ($created, $options = array()) {
	if (!empty($this->data['Investor']['investor_tempCode'])) {				// A confirmation code has been generated
		$event = new CakeEvent('confirmationCodeGenerated', $this, array('id' 	=> $this->id,
									'investor' 	=> $this->data[$this->alias],
					));
		$this->getEventManager()->dispatch($event);
	}
	
	if (!empty($this->data['Investor']['investor_accountStatus'])) {		// A user has succesfully and completely registered
            if (($this->data['Investor']['investor_accountStatus'] & QUESTIONAIRE_FILLED_OUT) ==  QUESTIONAIRE_FILLED_OUT) {
                $event = new CakeEvent('newUserCreated', $this, array('id' => $this->id,
								'investor' => $this->data[$this->alias],
						));
                        $this->getEventManager()->dispatch($event);
		}
	}
}


/**
*
*	Callback Function
*	Format the date
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
*	Rules are defined for what should happen when a database record is created or updated.
*	
*/

function beforeSave ( $options = array()) {

// Store telephone number without spaces
	if (!empty($this->data['Investor']['investor_dateOfBirth']))  {
		$this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
	}
	if (!empty($this->data['Investor']['investor_telephone']))  {
		$this->data['Investor']['investor_telephone'] = str_replace(' ', '',$this->data['Investor']['investor_telephone']);
	}	
}








}
