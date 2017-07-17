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


2017-06-14	  version 0.2
Initial version. 
 * All methods are "protected" using the "isAuthorized" function
 * 
 * 


2017-07-15      version 0.2
added methods cronMoveToMLDatabase(), resetInvestmentArray() and resetInvestorsArray()



Pending
Method "cronMoveToMLDatabase": fields 'userplatformglobaldata_reservedInvestments' and
  'userplatformglobaldata_finishedInvestments' are not yet available in the raw data

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
	$this->Auth->allow('login', 'loginAction', 'cronMoveToMLDatabase' , 'readMLDatabase');    // allow the actions without logon
//$this->Security->unlockedActions('login');
 

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



/**PENDING DATABASE ERRORS
 * 
 * Copies raw data to the Tallyman database
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
    $serviceData = Configure::read('Tallyman');   
    $limit = $serviceData['maxReadingsintoMldata'];
    if (empty($limit)) {
        $limit = 100;
    }

    $this->Company = ClassRegistry::init('Company');   
    $this->Mlqueue = ClassRegistry::init('Adminpfp.Mlqueue'); 
    $this->Userplatformglobaldata = ClassRegistry::init('Adminpfp.Userplatformglobaldata'); 
    $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');   
    $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');

// Get the data from the previous readout    
    $resultMlqueue = $this->Mlqueue->find("all",$params = array('recursive' => -1,
                                               'conditions'  => array('id' => 1))); 

    $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
							  'conditions'  => array(// sort by queueid
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                           //                'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                                            'queue_id >=' => $resultMlqueue[0]['Mlqueue']['mlqueue_actualQueueId'] ),
                                                              'limit' => $limit ));    
    

    $tempCount = 0;
    $actualQueueId = 0;


// Make sure that records belonging to the same queueId do not spill over into two reading.
    while (!empty($userinvestmentdataResult)) {
        $count = count($userinvestmentdataResult);
        echo "count = $count<br>";
        if ($count == $limit) {         // only do this if I am able to maximum number of records
            $maxQueueId = $userinvestmentdataResult[$count - 1]['Userinvestmentdata']['queue_id'];
            $firstQueueId = $userinvestmentdataResult[0]['Userinvestmentdata']['queue_id'];
            echo "firstQueueId = $firstQueueId<br>";
            echo "maxQueueId = $maxQueueId<br>";
            $actualQueueId = $maxQueueId;

            $index = $count - 1;
            while ($actualQueueId == $maxQueueId) {
                unset($userinvestmentdataResult[$index]);
                $index = $index - 1;
                
                $actualQueueId = $userinvestmentdataResult[$index]['Userinvestmentdata']['queue_id'];
                echo "deleting <br>";
            }
        }
        else {
 // Iterate through the "new" array
        }
        $newCount = sizeof($userinvestmentdataResult);
//        echo "newcount = $newCount <br>";        
        $newMaxQueueId = $userinvestmentdataResult[$newCount - 1]['Userinvestmentdata']['queue_id'] + 1;
 //       echo "newMaxQueueId = $newMaxQueueId <br>";        
        

                     //$this->print_r2($userinvestmentdataResult);

 //       foreach ($userinvestmentdataResult as $key => $result) {
 //       }

// this is the list I have to deal with
echo "....  Deal with array where START = $firstQueueId and END = $newMaxQueueId<br>";   
        unset($investorglobalData);
        unset($platformglobalData);  
        $oldQueueId = 0;
        
$this->print_r2($userinvestmentdataResult);
        foreach ($userinvestmentdataResult as $key => $result) {
echo "KEY = $key<br>";
            $companyId = $result['Userinvestmentdata']['company_id'];
echo "     ---> companyId = $companyId of queueId " . $result['Userinvestmentdata']['queue_id'] . "<br>";
            $companyResult = $this->Company->find("first", $params = array('recursive' => -1,
                                                          'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
                                )); 
           
            $queueId = $result['Userinvestmentdata']['queue_id'];
            if ($queueId <> $oldQueueId) {
echo __FUNCTION__ . " " . __LINE__ ." ---> NEW queueId found, new queueId = $queueId . So save the data of the previous Investorglobal/userplatformglobaldata<br>";
                $oldQueueId = $queueId;
                $index = 0;

                $dataFin = array(
                    'Investorglobaldata' => $investorglobalData,
                    'Userplatformglobaldata' => $platformglobalData
                   );
                $data1[] = $dataFin;
                unset($dataFin);
                unset ($investorglobalData);
                unset ($platformglobalData);   
                $this->resetInvestorsArray($investorglobalData);
            }  
                
            $investorglobalData['createdDate'] = $currentDate;
            $investorglobalData['investorglobaldata_currency'] = 1;  
            $investorglobalData['investorglobaldata_investorIdentity'] = $result['Userinvestmentdata']['userinvestmentdata_investorIdentity'];             
            $investorglobalData['investorglobaldata_totalMoneyInWallets'] += $result['Userinvestmentdata']['userinvestmentdata_myWallet'];  
            $investorglobalData['queueId'] = $queueId;            
            $activeInvestments = false;

            if (!empty($result['Investment'])) {
echo " ---> Reading the data of all investments of this user in this platform<br>";                
                foreach ($result['Investment'] as $investmentKey => $data)  {
                    $this->resetInvestmentArray($platformglobalData[$index]);
echo "INVESTMENTKEY = $investmentKey<br>";
echo "new investment found<br>";
echo __FUNCTION__ . " " . __LINE__ ."<br>";

                    if ($data['investment_amount'] > 0) {
echo __FUNCTION__ . " " . __LINE__ . " investment found, value = " . $data['investment_amount'];
                        $platformglobalData[$index]['userplatformglobaldata_activeInInvestments'] += $data['investment_amount'];
                        $platformglobalData[$index]['userplatformglobaldata_numberOfInvestments']++;
                        $investorglobalData['investorglobaldata_activeInInvestments'] += $data['investment_amount'];
                        $activeInvestments = true;
                    }               
                    $platformglobalData[$index]['userplatformglobaldata_moneyInWallet'] += $data['userinvestmentdata_myWallet'];
                    $platformglobalData[$index]['userplatformglobaldata_currency'] = 1;
            //              $investorglobalData['userplatformglobaldata_reservedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
            //              $investorglobalData['userplatformglobaldata_finishedInvestments'] += xxx;    // NOT YET IMPLEMENTED IN THE ORIGINAL RAW DATA
                    $platformglobalData[$index]['userplatformglobaldata_companyId'] = $companyId;            
                    $platformglobalData[$index]['userplatformglobaldata_companyName'] = $companyResult['Company']['company_name'];
                    $platformglobalData[$index]['userplatformglobaldata_PFPType'] = $companyResult['Company']['company_PFPType'];
                    $platformglobalData[$index]['userplatformglobaldata_PFPCountry'] = $companyResult['Company']['company_country']; 
                    $platformglobalData[$index]['userplatformglobaldata_globalIndicator'] = 3;   
                    $investorglobalData['investorglobaldata_totalMoneyInWallets'] += $data['userinvestmentdata_myWallet'];
        //               $investorglobalData['investorglobaldata_totalActiveInInvestments'] += $data[$index]['userinvestmentdata_activeInInvestments'];  

                    if ($activeInvestments) {
                        $investorglobalData['investorglobaldata_activePFPs'] += 1;
                    }
                    $investorglobalData['investorglobaldata_totalPFPs'] += 1;


                 }       // foreach ($result['Investment'] as $investmentKey => $data) 
            }
            else {
 echo " ---> No ACTIVE investments in current platform<br>";               
                $investorglobalData['investorglobaldata_totalPFPs'] += 1;
            }
            
             $index++;
$this->print_r2($platformglobalData[$index]);             
            $tempCount = count($userinvestmentdataResult);     
    
        }  //          foreach ($userinvestmentdataResult as $key => $result) 
        // show the last one
        $dataFin = array(
            'Investorglobaldata' => $investorglobalData,
            'Userplatformglobaldata' => $platformglobalData
           ); 
        $data1[] = $dataFin;
echo "tempCount = $tempCount and newMaxQueueId = $newMaxQueueId<br>";
echo "just read next batch of records but within outer foreach<br><br>";       
    $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
							  'conditions'  => array(// sort by queueid
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                           //                'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                                            'queue_id >= ' => $newMaxQueueId),
                                                              'limit' => $limit ));  
    $userinvestmentdataResult = null;


    } // end of while      
echo "FINALIZED<br>";
$this->print_r2($data1);
    $this->writeArray($data1[1]);
} 




/**
 * 
 * Load default values in the array
 * 
 * @param 	$investmentArray  Array to be reset
 * @return 	$investmentArray 	
 *
 */
private function resetInvestmentArray(& $investmentArray) {

    $investmentArray['userplatformglobaldata_companyId'] = 0;
    $investmentArray['userplatformglobaldata_companyName'] = "Error";
    $investmentArray['userplatformglobaldata_PFPType'] = 0;
    $investmentArray['userplatformglobaldata_moneyInWallet'] = 0;
    $investmentArray['userplatformglobaldata_activeInInvestments'] = 0;
    $investmentArray['userplatformglobaldata_numberOfInvestments'] = 0; 
    $investmentArray['investorglobal_id'] = 0;
    return ($investmentArray);
}


/**
 * 
 * Load default values in the array
 * 
 * @param 	$investorArray  Array to be reset
 * @return 	$investorArray 	
 *
 */
private function resetInvestorsArray(& $investorArray) {

    $investorArray['investorglobaldata_totalMoneyInWallets'] = 0;
    $investorArray['investorglobaldata_activeInInvestments'] = 0;
    $investorArray['investorglobaldata_activePFPs'] = 0;
    $investorArray['investorglobaldata_totalPFPs'] = 0;
    $investorArray['investorglobaldata_globalIndicator'] = 0;

    return ($investorArray);
}



/**
 * 
 * Writes an array of data to its corresponding model(s). The writing
 * is database transaction protected
 * 
 * @param 	$investorArray  Array to be reset
 * @return 	boolean	
 *
 */
private function writeArray(& $array) {

    $models = array_keys($array);
    foreach ($models as $model) {
        $this->$model = ClassRegistry::init('adminpfp.'. $model); 
 
        if (is_array($array[$model][0]) == true) {
            foreach ($array[$model] as $key => $tempModel) {
                $this->$model->create();
                $result = $this->$model->save($tempModel, $validate = true);
            }
        }
        else {
            $result = $this->$model->save($array[$model], $validate = true);
        }
    }
 
 exit;   
    return true;
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