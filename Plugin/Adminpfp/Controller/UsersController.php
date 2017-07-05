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
App::uses('ClassRegistry', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AdminpfpAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User', 'Company');
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
	$this->Auth->allow('login','session', 'loginAction', 'showTallymanPanel', 'startTallyman', 'readtallymandata');    // allow the actions without logon
//$this->Security->unlockedActions('login');
//   echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     
//var_dump($_REQUEST);
//var_dump($this->request);
//      echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     

}



/**
 * 
 * Shows a list of investors, using dataTable, in order to select/view/modify the data of 1 
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

    $this->layout = 'Adminpfp.azarus_private_layout';
 

    $filterconditions = array('investor_identity', $investorIdentification);
 //   $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    $this->set('result', $result);
       
}



/**
 * 
 * Shows the Tallyman data of a user in a graphical manner
 * 
 */
public function readtallymandata() {

    if (!$this->request->is('ajax')) {
        throw new
        FatalErrorException(__('You cannot access this page directly'));
        }
    $this->layout = 'ajax';
    $this->disableCache();

//    $platformId = $this->Auth->user('AdminPFP.company_id');
   
//    $this->layout = 'Adminpfp.azarus_private_layout';

    $error = null;
    $platformId = 1;
    $inputId = $_REQUEST['inputId'];
    $userEmail = $_REQUEST['userEmail'];
    $userTelephone = $_REQUEST['userTelephone'];
    
    $userEmail ="antoine.de.poorter@gmail.com";
    $userTelephone = "+34675546946";  

// Get the unique user identification
    $inputParmCount = 0;
    if (!empty($inputId)) {     
        $key[] = "Investor.investor_DNI";
        $value[] = $inputId;
        ++$inputParmCount;
    } 
    if (!empty($userEmail)) { 
        $key[] = 'Investor.investor_email';
        $value[] = $userEmail;
        ++$inputParmCount;
    }  
    if (!empty($userTelephone)) { 
        $key[] = 'Investor.investor_telephone';
        $value[] = $userTelephone;
        ++$inputParmCount;
    }  

    if ($inputParmCount < 2) {
        $error = NOT_ENOUGH_PARAMETERS;
    }
    else {
         
        $filterConditions = array_combine($key, $value);
        $this->Investor = ClassRegistry::init('Investor');   
        $resultInvestor = $this->Investor->getInvestorData($filterConditions);
        $userIdentification = $resultInvestor[0]['Investor']['investor_identity'];  


        if (!$userIdentification) {
            $error = USER_DOES_NOT_EXIST;
        }
        else {
            $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');
            $resultTallymanData = $this->Investorglobaldata->readinvestorData($userIdentification, $platformId);

            if (!$resultTallymanData) {
                $error = NO_DATA_AVAILABLE;
            }   
            else {
                
echo "1";
$this->print_r2($resultTallymanData);   

                 $this->set('resultTallyman', $resultTallymanData);

                 // provide data for possible billing
                  
                 $this->Billingparm = ClassRegistry::init('Adminpfp.Billingparm');
                 $data = array();
                 $data['reference'] = $userIdentification;
                 $data['parm1'] = $userIdentification;
                 $data['parm2'] = $userIdentification;
                 $data['parm3'] =  null;       
                 $this->Billingparm->writeChargingData($data, "tallyman");
  
            }
        }
    }

    if (!$error) {                              // No error encountered, use default view
        return;
    }    

    $this->set("error", $error);         
    $this->render('tallymanErrorPage'); 
}



  
/**
 * 
 * Shows the initial, basic screen of the Tallyman service with the three input fields
 * 
 */
public function showTallymanPanel() {
  $this->layout = 'Adminpfp.azarus_private_layout';
  
}  
  
  





public function loginAction() {
        if ($this->Auth->login()) {
            $this->redirect($this->Auth->redirectUrl());
        }
        else {
            echo "User is not logged on<br>";
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







/**
*	Reads the data of an Administrator 
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