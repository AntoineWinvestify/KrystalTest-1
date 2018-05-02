<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2016, http://beyond-language-skills.com                 |
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

Used for listening to events
Based on article "http://martinbean.co.uk/blog/2013/11/22/getting-to-grips-with-cakephps-events-system/'

*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-03-05
* @package
*


2017-03-05		Version 0.1		
Added function sendConfirmationCode									[Ok, tested]


PENDING:


*/




App::uses('CakeEmail', 'Network/Email');
App::uses('Security', 'Utility');
App::uses('CakeEventListener', 'Event');

class GlobalCommunicationListener implements CakeEventListener {

    
   
    function __construct() {
        Configure::load('p2pGestor.php', 'default');  
    }

    
/**
* IMPLEMENTED EVENTS:
* confirmationCodeGenerated			Send a SMS with a code
*
* 
*/
public function implementedEvents() {

// Determine which events have been selected in the config file
	$allImplementedEvents  =  array(
			'confirmationCodeGenerated' => 'sendConfirmationCode',
	);

	$configuredEvents = Configure::read('event');

	foreach ($configuredEvents as $key => $value) {
		if ($value == true) {
			$selectedEvents[$key] = $allImplementedEvents[$key];
		}
	}	
	return ($selectedEvents);
}





/** 
*
* A user is registering and needs to confirm the registration using a code sent via SMS
*
*/
public function sendConfirmationCode(CakeEvent $event) {
    
	$adminData = Configure::read('SMSadmin');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'winVestify.php');                      
        $runtime = new Winvestify();
        $runTimeParameters = $runtime->readRunTimeParameters();   
        $authKey = $runTimeParameters['runtimeconfiguration_smsProviderAuthKey'];        
        
	App::import('Vendor', 'php-rest-api-master', array('file'=>'autoload.php'));
	require APP . 'Vendor/php-rest-api-master/autoload.php';
	
	$this->Investor = ClassRegistry::init('Investor');
	$this->Investor->recursive = -1;	

// First collect all the required data
	$resultInvestor = $this->Investor->findById($event->data['id']);

//	$MessageBird = new \MessageBird\Client($adminData['AuthKey']); 
	$MessageBird = new \MessageBird\Client($authKey);  
        
	$Message             = new \MessageBird\Objects\Message();
	$Message->originator = $adminData['SMS_Originator'];
	$Message->recipients = array($resultInvestor['Investor']['investor_telephone']);
	$Message->body       = $resultInvestor['Investor']['investor_tempCode'];

	try {
		$MessageResult = $MessageBird->messages->create($Message);
//		var_dump($MessageResult);
	
	} catch (\MessageBird\Exceptions\AuthenticateException $e) {
		// That means that your accessKey is unknown
		echo 'wrong login';
		CakeLog::write('SMS_LOG', 'writing error to log. error is ' . $e->getMessage());
	
	} catch (\MessageBird\Exceptions\BalanceException $e) {
		// That means that you are out of credits, so do something about it.
		echo 'no balance';
		CakeLog::write('SMS_LOG', 'writing error to log. error is ' . $e->getMessage());
	
	} catch (\Exception $e) {
		echo "Exception:";
		echo $e->getMessage();
		CakeLog::write('SMS_LOG', 'writing error to log. error is ' . $e->getMessage());
	}
}	


}