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
	echo "AAAA";														// as it is "dynamic" and would fail the CSRF test

//	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actons.
//	$this->Security->requireAuth();
	$this->Auth->allow('login', 'loginAction', 'cronMoveToMLDatabase' , 'readMLDatabase');    // allow the actions without logon
//$this->Security->unlockedActions('login');
//   echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     
//var_dump($_REQUEST);
//var_dump($this->request);
//      echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     

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





public function readMLDatabase() {
     $this->autoRender = false;
    Configure::write('debug', 2);   
    echo "Antoine";
     $this->Userplatformglobaldata = ClassRegistry::init('Adminpfp.Userplatformglobaldata'); 
    $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');     
     
    $investorResult = $this->Investorglobaldata->find('all', $params = array('recursive' => 1,
							  'conditions'  => array(
                                                         //       'id >' => $queueResult[0]['MLqueue_actualId'],
                                                         //       'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                           //     'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                         //       'queue_id' => 57
                                                              ),
                                                          'limit' => 3));

    $this->print_r2($investorResult);
    
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
    $this->Userplatformglobaldata = ClassRegistry::init('Adminpfp.Userplatformglobaldata'); 
    $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');   
    $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');

    $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
							  'conditions'  => array(
                                                         //                   'id >' => $queueResult[0]['MLqueue_actualId'],
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                           //                'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                                            'queue_id >' => 57 ),
                                                          'limit' => 3)
				); 

    $this->print_r2($userinvestmentdataResult);

    unset($platformglobalData);
    $index = 0;
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
            
  //          unset($platformglobalData);
            foreach ($result['Investment'] as $investmentKey => $data)  {
                
 $this->print_r2($data);               
                 
                if ($data['investment_amount'] > 0) {
                    echo "investment found, value = " . $data['investment_amount'];
                    $platformglobalData[$index]['userplatformglobaldata_activeInInvestments'] += $data['investment_amount'];
                    $platformglobalData[$index]['userplatformglobaldata_numberOfInvestments']++;
                    $investorglobalData['investorglobaldata_activeInInvestments'] += $data['investment_amount'];
                    $activeInvestments = true;
                }               
                $data[$index]['userplatformglobaldata_moneyInWallet'] += $data['userinvestmentdata_myWallet'];
                $platformglobalData[$index]['userplatformglobaldata_currency'] = 1;
//              $investorglobalData['userplatformglobaldata_reservedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
//              $investorglobalData['userplatformglobaldata_finishedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
                $platformglobalData[$index]['userplatformglobaldata_companyId'] = $companyId;            
                $platformglobalData[$index]['userplatformglobaldata_companyName'] = $companyResult['Company']['company_name'];
                $platformglobalData[$index]['userplatformglobaldata_PFPType'] = $companyResult['Company']['company_PFPType'];
                $platformglobalData[$index]['userplatformglobaldata_PFPCountry'] = $companyResult['Company']['company_country']; 
                $platformglobalData[$index]['userplatformglobaldata_globalIndicator'] = 3;   
                
                $investorglobalData['investorglobaldata_totalMoneyInWallets'] += $data[$index]['userinvestmentdata_myWallet'];
 //               $investorglobalData['investorglobaldata_totalActiveInInvestments'] += $data[$index]['userinvestmentdata_activeInInvestments'];
                
 
 //$temp['Investorglobaldata']['Userplatformglobaldata'][$index]['userplatformglobaldata_companyId'] = $companyId; 
 //$temp['Investorglobaldata']['Userplatformglobaldata'][$index]['userplatformglobaldata_activeInInvestments'] = $data[$index]['investment_amount'];
 //$temp['investorglobaldata_totalActiveInInvestments'] = 3333;          
                
                
               
            }
            if ($activeInvestments) {
                $investorglobalData['investorglobaldata_activePFPs'] += 1;
            }
            
            $investorglobalData['investorglobaldata_totalPFPs'] += 1;
            
            $index++;
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
        $this->print_r2($investorglobalData);
        $this->print_r2($platformglobalData);
       
        echo "AANAN";
        
        $data = array(
            'Investorglobaldata' => $investorglobalData,
            'Userplatformglobaldata' => $platformglobalData
            );
        $this->print_r2($data);
        echo "going to save<br>";
         
  //      $this->Investorglobaldata->create($data);
        $rr = $this->Investorglobaldata->save($data, array('validate' => false, 'deep' => true));
        if ($rr == true) {
            echo "OK";
            echo $rr['Investorglobaldata']['id'];
            echo "BB = " . $this->print_r2($rr);
        }
        else {
            echo "error while saving";
        }
        echo "CHARO";
        foreach ($platformglobalData as $item) {
            $item['investorglobaldata_id'] = $rr['Investorglobaldata']['id'];
            $this->Userplatformglobaldata->create($item1);
            $item1 = $item;
            $this->Userplatformglobaldata->save($item1);

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