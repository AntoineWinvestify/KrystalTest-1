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
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-06-14
* @package
*

Manage the data of an investor for the service "Tallyman"
2017-06-14		version 0.1
initial version.
 * methods 
 * readInvestorData
 * 




Pending:

*/




class Investorglobaldata extends AppModel
{
	var $name= 'Investorglobaldata';


 	public $hasMany = array(
			'Userplatformglobaldatas' => array(
				'className' => 'Userplatformglobaldata',
				'foreignKey' => 'investorglobaldata_id',
				),
			);
        

/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);



/** 
 * 
 * Returns all the data of an investor, as defined by filterConditions,  as an array.
 * The system will return a maximum of  "historical data of 6 months
 * 
 * @param 	array 	$filterConditions
 * @return 	array 	Investor data with all relevant data	
 *
 */
public function readInvestorData($filterConditions) {
    
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
    if (!$data) {
        return;             // Nothing found 
    }
    foreach ($result['userplatformglobaldata'] as $key => $data ) {
        $globalIndicator = $this->calculateGlobalIndicator()
        
    }
    return $result;
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
public function cronMoveToML() {
    $currentDate = date("Y-m-d", time());     
    $businessConditions = array('Company.created >' => $cutoffTime);
    
    Configure::load('p2pGestor.php', 'default');
    $serviceData = Configure::read('Tallyman');   
    
    $this->Company = ClassRegistry::init('Company');   
    $this->MLqueue = ClassRegistry::init('MLqueue'); 
    
    $queueResult = $this->($this->read("first", $params = array('recursive' => -1,
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
        $tempResult = $this->Userinvestmentdata->find("all",$params = array('conditions'  => array('investorglobaldata_internalRawDataReference' => $internalRawDataReference )

        if (!empty($tempResult])) {     // Already dealt with this queueID
            $UserinvestmentdataResult = $this->Userinvestmentdata->read("first", $params = array('recursive' => 2,
							  'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                                           'created >= '  => $queueResult[0]['MLqueue_dateLastId'],
                                                              ),
				));       
        }
        else {              // Deal with this database record,          here we have a new queue_id so we have to
            $userData['investorglobaldata_investorIdentity'] = $UserinvestmentdataResult[0]['investorglobaldata_investorIdentity'];
            If ($this->Userinvestmentdata->save($userData, $validate = true)) {
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
                            ))

        $userResult = $this->Userinvestmentdata->read("all", $params = array('recursive' => 2,
							  'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                              'investorglobaldata_internalRawDataReference' => $internalRawDataReference,
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED),
                        			));    
        foreach ($Userinvestmentdata as data) {
          // mapping of data from "raw" format to MLData format    

            $companyResult = $this->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
				));        

            $platformData['id'] = $companyId,
            $platformData['userplatformglobaldata_moneyInWallet'] = data['userinvestmentdata_myWallet']; 
            $platformData['userplatformglobaldata_numberOfInvestments'] = sizeof(data['investments'];
            $platformData['userplatformglobaldata_activeInInvestments'] = data['userinvestmentdata_activeInInvestments'];    
            $platformData['userplatformglobaldata_reservedInvestments'] = 0;    
            $platformData['userplatformglobaldata_finishedInvestments'] = data[''];
            $platformData['companyId'] = $companyId,  
            $platformData['userplatformglobaldata_companyName'] = $companyResult[0]['company_name'], 
            $platformData['userplatformglobaldata_PFPType'] = $companyResult[0]['company_PFPType'], 
            $platformData['userplatformglobaldata_PFPCountry'] = $companyResult[0]['company_country'], 
            $platformData['userplatformglobaldata_globalIndicator'] = 0;    

            $userData['investorglobaldata_totalPFPs'] = $userData['investorglobaldata_totalPFPs'] + 1;
            if ([sizeof(data['investments'] > 0) {
                $userData['investorglobaldata_activePFPs'] = $userData['investorglobaldata_activePFPs'] + 1;
            }            
            $userData['investorglobaldata_totalMoneyInWallets'] = $userData['investorglobaldata_totalMoneyInWallets'] + data['userinvestmentdata_myWallet'];
            $userData['investorglobaldata_totalActiveInInvestments'] = $userData['investorglobaldata_totalActiveInvestments'] + data['userinvestmentdata_activeInInvestments'];
            if ($this->save->Userplatformglobaldata($platformData, $validate = true) {
                
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


/** 
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
