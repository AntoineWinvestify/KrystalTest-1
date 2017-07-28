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
* @date 2016-08-18
* @package
* 

2016-08-18	  version 2016_0.1
Services offered to the end-user




Pending






*/


class ServicesController extends AppController
{
	var $name = 'Services';
	var $helpers = array('Js');
	var $uses = array('Service');	

  	var $error;
//	var $layout = 'zastac_admin_login_layout';


function beforeFilter() {
// Load the application configuration file. Now it is available to the whole application	 
	Configure::load('p2pGestor.php', 'default');

//	parent::beforeFilter(); // only call if the generic code for all the classes is required.

//	$this->Security->requireAuth();

//	$this->Security->blackHoleCallback = '_blackHole';
//	$this->Security->unlockedFields = array('Student.sex', 'Pubform.sex'); // Pubform is correct
//	$this->Security->requireSecure(
//							'login'
//							);

//	$this->Security->validatePost = true;
//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
																// as it is "dynamic" and would fail the CSRF test

// Allow only the following actions.
//	$this->Auth->allow(listCompany);    // allow all actions as these are public pages
}







 
/** tested as non ajax function
*  
*	Calculate the price of a loan from all companies that offer this service
*	
*/
function compareLoan() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
	
	$loanAmount = $_REQUEST['loanAmount'];
	$loanAmortizationDuration = $_REQUEST['loanAmortizationDuration'];

	$this->Company = ClassRegistry::init('Company');	

//	$loanAmount = 400000;				// in Eurocents
//	$loanAmortizationDuration = 15;		// in months
	$interestRate = 700;
	$conditions = array("AND" => array('company_isActiveInMarketplace'	=> ACTIVE,
									   'company_state' 					=> ACTIVE,
									   )
						);

	$companyDataResults = $this->Company->find("all", $params = array('recursive'	=>  -1, 		
																	'conditions'	=> $conditions)
											);
	$dir = Configure::read('companySpecificPhpCodeBaseDir');
	require_once($dir . 'p2pCompany.class' . '.php');			// include the base class IMPROVE with spl_autoload_register

	$loanCostsList = array();
	foreach ($companyDataResults as $result) {
		if (($result['Company']['company_featureList'] && LOAN_TO_PRIVATE_PERSON) == TRUE ) {	// only companies that offer loans to private persons
//			echo "<br>______________ Checking new Company__________<br>";
					
			$includeFile = $dir.$result['Company']['company_codeFile'].".php";
			require($includeFile);
		
			$newClass = $result['Company']['company_codeFile'];
			$newComp = new $newClass;
			$loanCost['cost'] = $newComp->calculateLoanCost($loanAmount, $loanAmortizationDuration, $interestRate);
			$loanCost['companyId'] = $result['Company']['id'];
			$loanCostsList[] = $loanCost;
		}	
	}

	$companyData = array();
	foreach ($companyDataResults as $result) {
		$companyData[$result['Company']['id']] = $result['Company'];
	}

	$this->set('companyData', $companyData);
	$this->set('loanCostsList', $loanCostsList);
//	$this->set('companyDataResults', $companyDataResults);
}





/**
*  
*	Show the panel/form for comparing the various loan offers
*	
*/
function compareLoanPanel() {

	$this->layout = 'zastac_public_layout';

/*	$userId = $this->Auth->user('id');	
	$userDataResult = $this->User->find("all", $params = array(	'recursive'		=>  1, 		
																'conditions'	=> array('User.id' => $userId),
																	)
											);
	$this->set('userDataResult', $userDataResult);
*/
}




//*********************************************************************************************
//* CRONTAB OPERATIONS
//*********************************************************************************************





}
