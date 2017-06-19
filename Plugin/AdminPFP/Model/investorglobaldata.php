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
    
   We read the whole shit in one go, this is done from a "different server, during the night"
    check for any pending request for move_

      
    $this->Company = ClassRegistry::init('Company');   
    $this->MLqueue = ClassRegistry::init('MLqueue');  
    $queueResult = $this->($this->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => 1),
				)); 
    
   
    read next record (record AFTER 507);
        $result = $this->Userinvestmentdata->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id >' => $queueResult[0]['MLqueue_actualId'],
                                                                                'updateType = SYSTEM_GENERATED'),
				)); 
    $nextRecord = $result[0]['Userinvestmentdata'][0]['id'];
    store new number in queueResult 508)
    $this->MLqueue->save(array('id' => 1, 
                                actualId =>
                                dateActualId
                        ))
    
    
    Configure::load('p2pGestor.php', 'default');
    $serviceData = Configure::read('Tallyman');
	           
    store reference of last record
    we cycle through the whole population (of thousands) and read "record by record, to avoid huge memory demands of PHP"
    Once done, mark reference of this record in DB, to avoid duplicates
    $resultRawData = $this->.........
           
keep in mind 
            // TYPES OF DASHBOARD RECORD	
define('USER_GENERATED', 1);
define('SYSTEM_GENERATED', 2);

    // mapping of data from "raw" format to MLData format    
    $data['Userplatformglobaldatas']['ddd'] = totalvalue,
        ['Investorglobaldata'][platformId][platformid] = 
    foreach (platforms as platform)  {  
        $companyResult = $this->read("first", $params = array('recursive' => -1,
							  'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
				));        



        
        $platformData['id'] = $companyId,
        $platformData['activeInInvestments'] =     
        $platformData['moneyInWallet'] = 
        $platformData['reservedInvestments'] =              
        $platformData['finishedInvestments'] = 
        $platformData['companyId'] = $companyId,    
        $platformData['companyName'] = $companyResult[0]['company_name'], 
        $platformData['PFPType'] = $companyResult[0]['company_PFPType'], 
        $platformData['PFPCountry'] = $companyResult[0]['company_country'], 
        $platformData['globalIndicator'] = 0;    
        $data['Userplatformglobaldatas']['Investorglobaldata'][$platformData['id']] = $platformData;
    }
    
    

    $cutoffTime = date("Y-m-d H:i:s", time() - $serviceData['maxHistoryLength'] * 3600);     
    $businessConditions = array('Company.created >' => $cutoffTime);
    $conditions = array_merge($businessConditions, $filterConditions);

    $data = $this->find('all', array('conditions'       => $conditions,
                                          'recursive'   => 2,
			));
    
    return $data;   
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
