<?php
/*
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
* @date 2016-09-29
* @package
*

2016-09-29	  version 2016_0.1
														[almost OK]



Pending:
Error Handling (also in related views)
Security
User Authentication
User Session



*/

class ChatsController extends AppController
{
	var $name = 'Chats';
	var $helpers = array('Html', 'Form', 'Js', 'Text');
	var $uses = array('Investor', 'Linkedaccount', 'Company');
  	var $error;
	var $layout = 'default';

	
	
function beforeFilter() {

	parent::beforeFilter();
//	$this->Security->requireAuth();
	$this->set('parentController', strtolower($this->name));			// Required for AJAX callback
	$this->set('parentAction', strtolower($this->name) . "Ajax");		// Generic AJAX callback function
	
	if (substr(env('REQUEST_URI'), strlen(env('REQUEST_URI')) - 5, 5) == "Panel") {   // Check if it ends in "Panel"
		$this->callbackFunction = substr(env('REQUEST_URI'), 0, strlen(env('REQUEST_URI')) - 5);
	}
	
	// read investorId from Session
//	$this->investorId = $this->Auth->user('Investor.id');
$this->investorId = 1;		// TEMP FIX
}





/**
*
*	Shows the panel with the options to initiate a new chat or add to an existing chat
*
*/
function chatsPanel() {
	$this->Linkedaccount = ClassRegistry::init('Linkedaccount');			// Load the "Company" model

	$conditions = array('investor_id' => $this->investorId);
	$linkedAccountResult = $this->Linkedaccount->find("all",  $params = array('recursive' => -1,
																			'conditions' => $conditions)
											);
	
	$conditions = array('Company.company_state'   => ACTIVE,
						'Company.company_country' => 'ES');
		
	$this->Company = ClassRegistry::init('Company');				
	$companyResult = $this->Company->find("all",  $params = array('recursive' => -1,
																'conditions' => $conditions)
											);

	foreach ($companyResult as $index => $result) {
		if (($result['Company']['company_featureList'] & ALLOW_LINKED_ACCOUNTS) == 0 ) {
			unset($companyResult[$index]);
		}	
	}

// normalize the array
	foreach($companyResult as $value){
		$companyResults[$value['Company']['id']] = $value['Company'];
		$companyList[$value['Company']['id']] = $value['Company']['company_name'];
	}

	$this->set("linkedAccountResult", $linkedAccountResult);	
	$this->set("companyResults", $companyResults);
	$this->set("companyList", $companyList);
}





/**
*
*	Searches for an existing chat, show results to user and open it so user can add his/her comment
*
*/
function editChat() {


}





/**
*
*	Get the list of the most active chat thread 
*
*
*/
function getActiveChatsList() {
	
	$fields = array( 'id', 'chat_text', 'chat_subject',
					'chat_sequenceNumber', 
					'chat_totalComments', 'company_id');
	
	$chatThreadResults = $this->Chat->getChatThreadData($filteringConditions, $fields, 5);
$this->print_r2($chatThreadResults);
	$this->set('chatThreadResults', $chatThreadResults);
	
	
	
	
}





/**
*
*	Returns a list of chats according to filtering conditions
*	@param 		array 	$filteringConditions
*	@param 		bool	$multiple	true 	delete all if more then one record is found
*									false	delete only first record if more found
*
*	@return 	true	record(s) deleted
*	getActiveChatsList
maximum of 5 entries
sorted by activity

	

*/
function listChat() {
	
	$filteringConditions = array(' e' => e);
	$resultList = $this->getChatList($filteringConditions);



}





/** 
*basically writing the "first record" with some *extra* data. All this is done AFTER the user has
*finalized his comment.
*	Creates a new chat for a defaulted loan
*	input loanId
*			companyId
*			
*	return; error
*		loanID does not exist. Create chat anyway
*	
*
*	
*	or html for starting a new chat
*	
*	investor_id is required
*/
function newChat() {
	Configure::write('debug', 0);
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
//echo __FUNCTION__ . " " . __LINE__ . "<br>";
	$error = false;	
	$this->layout = 'ajax';
	$this->disableCache();

	
	$companyFilterConditions = array('id' => $_REQUEST['companyId']);
	$companyResults = $this->Company->getCompanyDataList($companyFilterConditions);	
	if (empty($companyResults) ) {
		$error = true;
		echo __FUNCTION__ . " " . __LINE__ . "<br>";
		return;
	}
//echo __FUNCTION__ . " " . __LINE__ . "<br>";
	$companyId = $_REQUEST['companyId'];

//echo __FUNCTION__ . " " . __LINE__ . "<br>";	
	$newComp = $this->companyClass($companyResults[$companyId]['company_codeFile']);	
	$newComp->setUrlSequence($urlSequenceList);		
	$userInvestment = $newComp->companyUserLogin($_REQUEST['userName'], $_REQUEST['password']);
//echo __FUNCTION__ . " " . __LINE__ . "<br>";
	if (!$userInvestment) {
		$error = true;
	}
	else {
		$linkedAccountData['Linkedaccount'] = array('company_id' 			=> $_REQUEST['companyId'],
												   'investor_id' 			=> $this->investorId,
												   'linkedaccount_username' => $_REQUEST['userName'],
												   'linkedaccount_password' => $_REQUEST['password']
														   );
		if ($this->Linkedaccount->save($linkedAccountData, $validate = true) ) {
			$urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);	
			$newComp->companyUserLogout();
		}
		else {
			echo __FUNCTION__ . " " .  __LINE__ . "<br>";
			
			//$error = true;  IS THIS NEEDED?
		}
	}

	// load the list of all companies for printout purposes
	$companyFilterConditions = array('id >' => 0);		// Load ALL company data as array
	$companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

	$linkedaccountFilterConditions = array('investor_id' => $this->investorId);
	$linkedaccountsResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);

	$this->set('linkedaccountsResult', $linkedaccountsResult);
	$this->set('companyResults', $companyResults);
	$this->set('error', $error);
	$this->render('linkAccount');
}


}
