<?php
/*
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



Functions for the AdminPFP role


2017-06-14	  version 0.1
Initial version. 
 * All methods are "protected" using the "isAuthorized" function
 * 
 * 

added cronMoveToMLDatabase() method 

Pending



*/

App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AdminpfpAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User', 'Investorglobaldata', 'Company');
	var $components = array('Security');
  	var $error;
	



function beforeFilter() {
    parent::beforeFilter(); // only call if the generic code for all the classes is required.

//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test

//	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actions.
//	$this->Security->requireAuth();
	$this->Auth->allow('login','session', 'loginAction', 'showTallyman', 'startTallyman');    // allow the actions without logon
//$this->Security->unlockedActions('login');
//   echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     
//var_dump($_REQUEST);
//var_dump($this->request);
//      echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     

}



/**
 * 
 * Shows a list of investors, using dataTable, in order to select/view/modifiy the data of 1 
 * investor
 * 
 */
public function showInvestorList() {
  
}




/**
 * 
 * Shows the initial, basic screen of the Tallyman service
 * 
 */
public function startTallyman() {
 //   	Configure::write('debug', 2); 

    $this->layout = 'Adminpfp.azarus_private_layout';
 

    $filterconditions = array('investor_identity', $investorIdentification);
 //   $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    $this->set('result', $result);
       
}



/**
 * 
 * Shows the Tallyman data of a user
 * 
 */
public function showTallyman($investorIdentity = null, $platformId = 1) {
    Configure::write('debug', 2);

    $this->layout = 'Adminpfp.azarus_private_layout';
   /** 	
    $resultTallyman = $this->Investorglobaldata->loadInvestorData($investoridentity);
  */ 
    $resultCompany = $this->Company->getCompanyDataList(array('id' => $platformId));

 //   $this->print_r2($resultCompany);
 
$resultTallyman[0]['investorglobaldata_investorIdentity'] = '929094Akri445902';
$resultTallyman[0]['investorglobaldata_activePFPs'] = 3;
$resultTallyman[0]['investorglobaldata_totalPFPs'] = 3;
$resultTallyman[0]['investorglobaldata_totalMoneyInWallets'] = 5032;
$resultTallyman[0]['investorglobaldata_totalActiveInvestments'] = 133235;
$resultTallyman[0]['investorglobaldata_currency'] = 1;     // = Euro44412 + 
$resultTallyman[0]['created'] = "2017-04-15 01:55:21";     


$resultTallyman[0]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1052;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][1]['reservedInvestments'] = 4442;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 'Comunitae';
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[0]['Userplatformglobaldata'][2]['id'] = 12;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_activeInInvestments'] = 24411;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_reservedInvestments'] = 44411;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[0]['Userplatformglobaldata'][2]['globalIndicator'] = 112;

$resultTallyman[0]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;


$resultTallyman[1]['investorglobaldata_investorIdentity'] = '929094Akri445902';
$resultTallyman[1]['investorglobaldata_activePFPs'] = 3;
$resultTallyman[1]['investorglobaldata_totalPFPs'] = 3;
$resultTallyman[1]['investorglobaldata_totalMoneyInWallets'] = 20035;
$resultTallyman[1]['investorglobaldata_totalActiveInvestments'] = 18989079;
$resultTallyman[1]['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[1]['created'] = "2017-04-08 01:51:21";     

$resultTallyman[1]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 4412;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 112;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[1]['Userplatformglobaldata'][2]['id'] = 12;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_companyName'] = "ZAnk";
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPType'] = "IT";
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPCountry'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[1]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 4412;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = 112;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = "I";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;



$resultTallyman[2]['investorglobaldata_investorIdentity'] = '929094Akri445902';
$resultTallyman[2]['investorglobaldata_activePFPs'] = 2;
$resultTallyman[2]['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[2]['investorglobaldata_totalMoneyInWallets'] = 24333;
$resultTallyman[2]['investorglobaldata_totalActiveInvestments'] = 18989079;
$resultTallyman[2]['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[2]['created'] = "2017-04-01 01:51:21"; 

$resultTallyman[2]['Userplatformglobaldata'][1]['id'] = 12;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 4412;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 112;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[2]['Userplatformglobaldata'][2]['id'] = 19;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_currency'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_reservedInvestments'] = 44412;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_finishedInvestments'] = 152;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_companyId'] = 4412;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_companyName'] = 112;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPType'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[2]['Userplatformglobaldata'][2]['userplatformglobaldata_globalIndicator'] = 112;



// Do some simple calculations to get extra "new" values so they can be displayed
// enrich the information to be provided to the PFPAdmin user
// index 0 is the most recent read-out of the user investment data
   
    $homeCountryPFP = $resultCompany[$platformId][company_country];

    foreach ($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        if ($platform['userplatformglobaldata_PFPCountry'] == $homeCountryPFP) {
            $platformsHomeCountry = $platformsHomeCountry + 1;
        }
        else {
            $platformsForeignCountries = $platformsForeignCountries + 1;
        }
    }
    $resultTallyman[0]['platformsHomeCountry'] = $platformsHomeCountry;
    $resultTallyman[0]['platformsForeignCountries'] = $platformsForeignCountries;    
    

// How many types of platforms do we have?
    $platformTypes = count($this->crowdlendingTypesShort);
    $platformInvestmentsPerType = array_fill(0,  $platformTypes, 0);
    $platformInvestmentsPerAmount = array_fill(0,  $platformTypes, 0); 
  

    foreach($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']] = 
                $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']] + 1;
        $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']]  = 
                $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']] + 
                $platform['userplatformglobaldata_activeInInvestments'];
    }

    $resultTallyman[0]['userplatformglobaldata_PFPPerType'] = $platformInvestmentsPerType;
    $resultTallyman[0]['userplatformglobaldata_PFPPerAmount'] = $platformInvestmentsPerAmount;

// Also provide "normalized" data, i.e. in %.
// Total represents 100
    $totalPerType = array_sum($platformInvestmentsPerType);
    $totalPerAmount = array_sum($platformInvestmentsPerAmount);

    echo $totalPerType;
    echo $totalPerAmount;
$i = 0;
    foreach ($platformInvestmentsPerType as $value) {
        $resultTallyman[0]['userplatformglobaldata_PFPPerTypeNorm'][$i] = (int) (100 * $value / $totalPerType); 
        $i = $i + 1;
    }
$i = 0;
    foreach ($platformInvestmentsPerAmount as $value) {
        $resultTallyman[0]['userplatformglobaldata_PFPPerAmountNorm'][$i] = (int) (100 *$value / $totalPerAmount); 
        $i = $i + 1;       
    }










    
    
//$this->print_r2($resultTallyman[0]['userplatformglobaldata_PFPPerAmount']);    

    foreach($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        if ($platform['userplatformglobaldata_companyId'] == $platformId) {
            $resultTallyman[0]['totalMyPlatform'] = $platform['userplatformglobaldata_activeInInvestments'];
            break;
        }
    }
   
    foreach($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        if ($platform['userplatformglobaldata_PFPType'] ==  $resultCompany[$platformId]['company_typeOfCrowdlending']) {
            $resultTallyman[0]['totalMyPlatform'] = $platform['userplatformglobaldata_activeInInvestments'];
            break;
        }
    }    
    
    
     $resultTallyman[0]['platformsHomeCountry'] = $platformsHomeCountry;
    $resultTallyman[0]['platformsForeignCountries'] = $platformsForeignCountries;    
    $labelsPieChart1 = array("Local", "Foreign");   
    $dataPieChart1 = array($resultTallyman[0]['platformsHomeCountry'], $resultTallyman[0]['platformsForeignCountries']);
$resultTallyman[0]['labelsPieChart1'] = $labelsPieChart1;
$resultTallyman[0]['dataPieChart1'] = $dataPieChart1;

    
    
    
 // Calculate some values for this view

    $totalPortfolio = $resultTallyman[0][totalMyPlatform] / $resultTallyman[0]['investorglobaldata_totalActiveInvestments'];
    $this->set('totalPortfolio', $totalPortfolio);
    $totalMyModality = $resultTallyman[0]['totalMyPlatform'] /
    $resultTallyman[0]['userplatformglobaldata_PFPPerAmount'][$resultCompany[$platformId]['company_typeOfCrowdlending']];
   
    $this->set('totalMyModality', $totalMyModality);
    
 //   $dataPieChart[] = $value;
 //   $labelsPieChart[] = $key;

//$this->print_r2($this->crowdlendingTypes);
$this->print_r2($resultTallyman);

    $this->set('resultCompany', $resultCompany);
    $this->set('resultTallyman', $resultTallyman);
    $this->set('crowdlendingTypes', $this->crowdlendingTypes);
   
  }





/** ajax call
 *
 *
*	Reads the data from an Administrator 
*
*/
public function adminHome() {
echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";	


echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";		
}

 






/**
*	password change function
*/
public function changeAdminPw() {

	if ($this->Auth->user('id')) {   // Just to  make sure User is logged
		$this->User->id = $this->Auth->user('id');  // Set User Id
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__("Password has been changed"), 'default', array('class' => 'intranet_flash_msg'));
				$this->redirect(array('controller' => 'startpanels', 'action' => 'index' ));
			}
			else {
				$this->Session->setFlash(__("Password could not be changed."), 'default', array('class' => 'flash_msg_error'));
			}
		}
		else
			{
				
		}
	}
}







/**ajax call
*	Write (= modifies) some data of an existing Administrator
*
*/
function editAdministratorData($id) {


}




public function loginAction() {
Configure::write('debug', 0); 
    echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
//$this->print_r2($this->request->data);
//$this->autoRender = false;
	 
        if ($this->Auth->login()) {
            echo "SESSION155 <br>";
            echo "We have succesfully logged in <br>";
  //          print_r($this->Session->read()) ."<br>";
            echo "<br>" . $this->Auth->redirectUrl()."<br>"."<br>";
         //   return $this->Auth->redirectUrl();
            $this->redirect($this->Auth->redirectUrl());
  echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";        
        }
        else {
            echo "User is not logged on<br>";
            echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
        }
        
  exit;  
        if ($this->Auth->loggedIn()){
		echo "user has logged on";
                return $this->redirect($this->Auth->redirectUrl());
	}
	else {
		echo "User not logged on";
	}


}



/**
*
*	Shows the login panel
*
*/
public function login()
{
	if ( $this->request->is('ajax')) {
		$this->layout = 'ajax';
		$this->disableCache();
	}
	else {
		$this->layout = 'Adminpfp.winvestify_adminpfp_login_layout';
	}
	$error = false;
	$this->set("error", $error);
}




public function logout() {
	$user = $this->Auth->user();		// get all the data of the authenticated user
	$event = new CakeEvent('Controller.User_logout', $this, array('data' => $user,
                            ));
	$this->getEventManager()->dispatch($event);
	return $this->redirect($this->Auth->logout());
}







/**ajax call
*	Reads the data from an Administrator 
*
*/
function readAdministratorData($adminId) {
	$this->layout = 'zastac_admin_layout';

}







/**DURING THE NIGHT WE WILL DOWNLOAD THE RELEVANT DATA FROM THE RAW DATABASE TO THIS MLDATA DATABASE  
 * 
 * Moves in 'semi-realtime' raw data to the database which is used by the Tallyman service
 * 
 * 
 * @param 	
 * @param      
 * @return 	 	
 *
 */
public function cronMoveToMLDatabase() {
    $currentDate = date("Y-m-d", time());     
   
    Configure::load('p2pGestor.php', 'default');
    $serviceData = Configure::read('Tallyman');   
    
    $this->Company = ClassRegistry::init('Company');   
    $this->MLqueue = ClassRegistry::init('MLqueue'); 
    
    $queueResult = $this->$this->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => 1),
				));  

    $UserinvestmentdataResult = $this->Userinvestmentdata->read("first", $params = array('recursive' => 2,
							  'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                                           'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                              ),
				)); 

    while (!empty($UserinvestmentdataResult)) {
        $internalRawDataReference = $result[0]['Userinvestmentdata']['investorglobaldata_internalRawDataReference'];
        $tempResult = $this->Userinvestmentdata->find("all", $params = array('conditions'  => array('investorglobaldata_internalRawDataReference' => $internalRawDataReference),
            ));

        if (!empty($tempResult)) {     // Already dealt with this queueID
            $UserinvestmentdataResult = $this->Userinvestmentdata->read("first", $params = array('recursive' => 2,
                                                                'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                                           'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                              ),
				));       
        }
        else {              // Deal with this database record,          here we have a new queue_id so we have to
            $userData['investorglobaldata_investorIdentity'] = $UserinvestmentdataResult[0]['investorglobaldata_investorIdentity'];
            if ($this->Userinvestmentdata->save($userData, $validate = true)) {
                $userinvestmentpointer = $this->Userinvestmentdata->id;
            }
            else {
                echo "ERROR OCCURED, TAKE ACTION";  
                // add possible errors in interface errors table  
                $userinvestmentpointer = 0;
            }
        }
        $nextRecord = $result[0]['Userinvestmentdata'][0]['id'];
        $this->MLqueue->save(array('id' => 1, 
                                    'MLqueue_actualId' => $nextRecord,
                                    'MLqueue_dateActualId' => $currentDate,
                            ));

        $userResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 2,
							  'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                              'investorglobaldata_internalRawDataReference' => $internalRawDataReference,
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED),
                        			));    
        foreach ($Userinvestmentdata as $data) {
          // mapping of data from "raw" format to MLData format    
            $companyResult = $this->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
				));        

            $platformData['id'] = $companyId;
            $platformData['userplatformglobaldata_moneyInWallet'] = data['userinvestmentdata_myWallet']; 
            $platformData['userplatformglobaldata_numberOfInvestments'] = sizeof(data['investments']);
            $platformData['userplatformglobaldata_activeInInvestments'] = data['userinvestmentdata_activeInInvestments'];    
            $platformData['userplatformglobaldata_reservedInvestments'] = 0;    
            $platformData['userplatformglobaldata_finishedInvestments'] = data['']; // TO BE CALCULATED
            $platformData['companyId'] = $companyId;
            $platformData['userplatformglobaldata_companyName'] = $companyResult[0]['company_name'];
            $platformData['userplatformglobaldata_PFPType'] = $companyResult[0]['company_PFPType'];
            $platformData['userplatformglobaldata_PFPCountry'] = $companyResult[0]['company_country']; 
            $platformData['userplatformglobaldata_globalIndicator'] = 0;    

            $userData['investorglobaldata_totalPFPs'] = $userData['investorglobaldata_totalPFPs'] + 1;
            if (sizeof(data['investments'] > 0)) {
                $userData['investorglobaldata_activePFPs'] = $userData['investorglobaldata_activePFPs'] + 1;
            }      
            $userData['investorglobaldata_totalMoneyInWallets'] = $userData['investorglobaldata_totalMoneyInWallets'] + data['userinvestmentdata_myWallet'];
            $userData['investorglobaldata_totalActiveInInvestments'] = $userData['investorglobaldata_totalActiveInvestments'] + data['userinvestmentdata_activeInInvestments'];
            if ($this->save->Userplatformglobaldata($platformData, $validate = true)) {
                
            }
            else {
                echo "ERROR OCCURED, TAKE ACTION";  
                // add possible errors in interface errors table
                // reset something ????
            }
        }
    }    
}









/**DURING THE MIGRATION SOMETHING HAPPENED, AND THE MLDATA DATABASE MIGHT HAVE AN EXTRA RECORD WHICH
 * IS UNACCEPTABLE, DETECT IT AND REPAIR IT. this is normally a duplicate.
 *  NOT YET FUNCTIONAL
 * do this for all which were added during this day
 * filter:
 * duplicate internal reference
 * during today's DATE
 * 
 * duplicates will be delete without event notifying the system admin
 * 
 * 
 * @param 	
 * @param      
 * @return 	 	
 *
 */
public function checkIntegrityMLData() {
    
}


/** NOT YET FUNCTIONAL
 * 
 * Returns a global indicator which represents the "value" of an investor for a PFP platform
 * 
 * 
 * @param 	array 	$data
 * @param       arrat   $id     $id of company for which 
 * @return 	array 	Investor data 	
 *
 */
public function calculateGlobalindicator($data) {
    
// Calculate earlier date
//    $referenceDate = actualData - $historyLength "months";
    Configure::load('p2pGestor.php', 'default');
    $serviceData = Configure::read('Tallyman');

    $cutoffTime = date("Y-m-d H:i:s", time() - $serviceData['maxHistoryLength'] * 3600);     
    $businessConditions = array('Company.created >' => $cutoffTime);
    $conditions = array_merge($businessConditions, $filterConditions);

    $data = $this->find('all', array('conditions'       => $conditions,
                                          'recursive'   => 2,
			));
    
    return $data;   
}









}
