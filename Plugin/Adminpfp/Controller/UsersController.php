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
Method "cronMoveToMLDatabase": fields 'userplatformglobaldata_reservedInvestments' and
  'userplatformglobaldata_finishedInvestments' are not yet available in the raw data

isChargeableEvent should also keep special conditions in mind, like NEVER charge the user,
or charge only xx events/time-period/user, etc etc.

*/

App::uses('ClassRegistry', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AdminpfpAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User');
	var $components = array('Security');
  	var $error;
	



function beforeFilter() {
    parent::beforeFilter(); // only call if the generic code for all the classes is required.

//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club33 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test

//	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actons.
//	$this->Security->requireAuth();
	$this->Auth->allow('login','session', 'loginAction', 'showTallymanPanel', 'cronMoveToMLDatabase',
                                'startTallyman', 'readtallymandata', 'testmodal');    // allow the actions without logon
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
public function startTallyman($investorEmail, $investorTelephone) {
 
    $this->layout = 'Adminpfp.azarus_private_layout';
 // check inputparameters against dangerous inputs
 //   $investorTelephone = "+" . $investorTelephone;
    
    $investorDNI = "";
    $investorTelephone = ""; 
    $investorEmail = "";

    $this->set("investorEmail", $investorEmail);
    $this->set("investorDNI", $investorDNI);
    $this->set("investorTelephone", $investorTelephone);            
            

    $filterconditions = array('investor_identity', $investorIdentification);
 //   $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    $this->set('result', $result);
       
}




// to test if modal shows up correctly. to be deleted
/*
 * should receive the request for one or more users
 * It checks if charging is to be applied. IF so then show the modal with information
 * about how many requests will be charged ("this event will be charged")
 * 
 * 
 * 
 */
public function testmodal() {
    
    

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

    $platformId = $this->Session->read('Auth.User.Adminpfp.company_id');
    $error = null;
    
    $inputId = $_REQUEST['inputId'];
    $userEmail = $_REQUEST['userEmail'];
    $userTelephone = $_REQUEST['userTelephone'];
 
    
    $userEmail ="antoine.de.poorter@gmail.com";
    $userTelephone = "+34675546946";  

      
// Get the unique investor identification
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
        $searchData =  json_encode($filterConditions);
   
        $this->Search = ClassRegistry::init('Adminpfp.Search');   
        $result = $this->Search->writeSearchData($searchData, $platformId, null, null, TALLYMAN_APP); 
      
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
                 $this->set('resultTallyman', $resultTallymanData);

                 // provide data for possible billing
                $this->Billingparm = ClassRegistry::init('Adminpfp.Billingparm'); 
                if ($this->isChargeableEvent($userIdentification, null, $platformId, null, "tallyman")) {
                    $data = array();
                    $data['reference'] = $userIdentification;                           // investor unique identification
                    $data['parm1'] = $this->Session->read('Auth.User.Adminpfp.adminpfp_identity');       // adminpfp unique identification
                    $data['parm2'] = $platformId;                                      // platformId of the adminfp user
                    $data['parm3'] = null;       
                    $this->Billingparm->writeChargingData($data, "tallyman");
                 }
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
     * 	Checks if Tallyman event is to be charged, i.e. if charging data must be stored in database
     * 	@param 		$reference      parameter to be checked
     *  @param      string      transparent parameter 2 to be checked
     *  @param      string      transparent parameter 3 to be checked
     *  @param      string      transparent parameter 4 to be checked
     *  @param      string      name of application
     *
     * 	@return 	boolean	true	All OK, data has been saved
     * 				false	Error occured
     * 						
     */
public function isChargeableEvent($reference, $parameter1, $parameter2, $parameter3, $application) {
return false;
//  Calculate cutoff date for billing purposes
    Configure::load('p2pGestor.php', 'default');
    $validBeforeExpiration = Configure::read('CollectNewInvestmentData');
    $cutoffTime = date("Y-m-d H:i:s", time() - $validBeforeExpiration * 3600 * 7 *24);    
  
    $result = $this->Billingparm->find('first', array(
                                            "fields" => array("created"),
                                            "order" => "id DESC",
                                            "recursive" => -1,
                                            "conditions" => array("billingparm_reference" => $reference,
                                                                    "billingparm_parm2" => $parameter2,
                                                                "billingparm_serviceName" => $application),
                                             ));
            
    if (empty($result)) {  // No information found, 
        return false;
    }

    if ($result['Billingparm']['created'] > $cutoffTime) {          // This request should NOT be counted as a new chargeable request
        return true;           
    }
    return false;
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
    $this->autoRender = false;
    Configure::write('debug', 2);    
    $currentDate = date("Y-m-d", time());     
   
    Configure::load('p2pGestor.php', 'default');
//    $serviceData = Configure::read('Tallyman');   
    
    $this->Company = ClassRegistry::init('Company');   
    $this->MLqueue = ClassRegistry::init('MLqueue'); 
   
    $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');

 //   $queueResult = $this->$Userinvestmentdata->read("first", $params = array('recursive' => -1,
//							  'conditions'  => array('id' => 1),
//				));

    $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
							  'conditions'  => array(
                                                         //                   'id >' => $queueResult[0]['MLqueue_actualId'],
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                           //                'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                                            'queue_id' => 57 ),
                                                          'limit' => 3)
				); 

    $this->print_r2($userinvestmentdataResult);



    foreach ($userinvestmentdataResult as $key => $result) {
   
 //       $internalRawDataReference = $result['Userinvestmentdata']['investorglobaldata_internalRawDataReference'];
//        $tempResult = $this->Userinvestmentdata->find("all", $params = array('conditions'  => array('investorglobaldata_internalRawDataReference' => $internalRawDataReference),
 //           ));
/*
        if (!empty($tempResult)) {     // Already dealt with this queueID
            $userinvestmentdataResult = $this->Userinvestmentdata->read("first", $params = array('recursive' => 1,
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
                continue;
            }
        }
 */
  //      $nextRecord = $result[0]['Userinvestmentdata'][0]['id'];
 //       $this->MLqueue->save(array('id' => 1, 
 //                                   'MLqueue_actualId' => $nextRecord,
 //                                   'MLqueue_dateActualId' => $currentDate,
 //                           ));
   

        $companyId = $result['Userinvestmentdata']['company_id'];
          // mapping of data from "raw" format to MLData format    
            $companyResult = $this->Company->find("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
				));        
            $investorglobalData['createdDate'] = $currentDate;
            $investorglobalData['investorglobaldata_currency'] = 1;  
            $investorglobalData['investorglobaldata_investorIdentity'] = $result['Userinvestmentdata']['userinvestmentdata_investorIdentity'];             
            $investorglobalData['investorglobaldata_totalMoneyInWallets'] += $result['Userinvestmentdata']['userinvestmentdata_myWallet'];          

            $activeInvestments = false;
            
            
            foreach ($result['Investment'] as $investmentKey => $data)  {
                unset($platformglobalData);
 $this->print_r2($data);               
                 
                 if ($data['investment_amount'] > 0) {
                    $platformglobalData['userplatformglobaldata_activeInInvestments'] += $data['investment_amount'];
                    $platformglobalData['userplatformglobaldata_numberOfInvestments']++;
                    $investorglobalData['investorglobaldata_totalActiveInInvestments'] += $data['investment_amount'];
                    $activeInvestments = true;
                }               
                $platformglobalData['userplatformglobaldata_moneyInWallet'] += $data['userinvestmentdata_myWallet'];
                $platformglobalData['userplatformglobaldata_currency'] = 1;
//                $investorglobalData['userplatformglobaldata_reservedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
//                $investorglobalData['userplatformglobaldata_finishedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
                $platformglobalData['userplatformglobaldata_companyId'] = $companyId;            
                $platformglobalData['userplatformglobaldata_companyName'] = $companyResult['Company']['company_name'];
                $platformglobalData['userplatformglobaldata_PFPType'] = $companyResult['Company']['company_PFPType'];
                $platformglobalData['userplatformglobaldata_PFPCountry'] = $companyResult['Company']['company_country']; 
                $platformglobalData['globalIndicator'] = 3;   
                
                $investorglobalData['investorglobaldata_totalMoneyInWallets'] += $data['userinvestmentdata_myWallet'];
                $investorglobalData['investorglobaldata_totalActiveInInvestments'] += $data['userinvestmentdata_activeInInvestments'];
            }
            if ($activeInvestments) {
                $investorglobalData['investorglobaldata_activePFPs'] += 1;
            }
            
            $investorglobalData['investorglobaldata_totalPFPs'] += 1;
            $this->print_r2($investorglobalData);
            $this->print_r2($platformglobalData);
   echo "END OF LIST";         
        /*    
         * 
         * 



            if ($this->save->Userplatformglobaldata($platformData, $validate = true)) {
                
            }
            else {
                echo "ERROR OCCURED, TAKE ACTION";  
                // add possible errors in interface errors table
                // reset something ????
            }
         
         */
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
public function checkIntegrityMLData1() {
    
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