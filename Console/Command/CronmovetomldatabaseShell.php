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
//	var $validate = array();


The "cronMoveToMLDatabaseShell" converts the static "photos" into the real database
 * 
2017-07-24      version 0.1
added methods cronMoveToMLDatabase(), writeArray, resetInvestmentArray() and resetInvestorsArray()


Pending:
Read the constants from a application system wide file
  
 
*/


// TYPES OF DASHBOARD RECORDS	
define('USER_GENERATED', 2);
define('SYSTEM_GENERATED', 1);


class CronmovetomldatabaseShell extends AppShell
{
    public $uses = array('Company', 'Userinvestmentdata');

    



/**
 * 
 * Copies raw data to the Tallyman database for ALL users, independent of the "type of user",
 * i.e. "free", "Premium" or "Pro" or "Family Office",....
 * 
 * 
 * @param 	
 * @param      
 * @return 	 	
 *
 */
public function cronMove() {
    $this->autoRender = false; 
    Configure::write('debug', 2);
    
    $currentDate = date("Y-m-d", time());     
    
    Configure::load('p2pGestor.php', 'default');
    $serviceData = Configure::read('Tallyman');   
    $limit = $serviceData['maxReadingsintoMldata'];
    if (empty($limit)) {
        $limit = 100;
    }
 
    $this->Mlqueue = ClassRegistry::init('Adminpfp.Mlqueue'); 
    $this->Userplatformglobaldata = ClassRegistry::init('Adminpfp.Userplatformglobaldata'); 
    $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata'); 
 
// Get the information of the previous readout    
    $resultMlqueue = $this->Mlqueue->find("all",$params = array('recursive' => -1,
                                               'conditions'  => array('id' => 1))); 

    $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
							  'conditions'  => array(// sort by queueid
                                                                           'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                                            'queue_id >' => $resultMlqueue[0]['Mlqueue']['mlqueue_actualQueueId'] ),
                                                              'limit' => $limit ));    
    $tempCount = 0;
    $actualQueueId = 0;

    while (!empty($userinvestmentdataResult)) {
        // Make sure that records belonging to the same queueId do not spill over into two reading.
        // so delete all records belonging to the *last* queue_id        
        $count = count($userinvestmentdataResult);
        if ($count == $limit) {         // only do this if I am able to maximum number of records
            $maxQueueId = $userinvestmentdataResult[$count - 1]['Userinvestmentdata']['queue_id'];
            $actualQueueId = $maxQueueId;
            
            $index = $count - 1;
            while ($actualQueueId == $maxQueueId) {
                unset($userinvestmentdataResult[$index]);
                $index = $index - 1;
                $actualQueueId = $userinvestmentdataResult[$index]['Userinvestmentdata']['queue_id'];
            }
        }

        $newCount = sizeof($userinvestmentdataResult);
        $newMaxQueueId = $userinvestmentdataResult[$newCount - 1]['Userinvestmentdata']['queue_id'] + 1;

        // start with everything clean
        unset($investorglobalData);
        unset($platformglobalData);  
        $oldQueueId = 0;
       
print_r($userinvestmentdataResult);
        foreach ($userinvestmentdataResult as $key => $result) {
            $companyId = $result['Userinvestmentdata']['company_id'];
            $companyResult = $this->Company->find("first", $params = array('recursive' => -1,
                                                          'conditions'  => array('id' => $companyId),
                                                          'fields'  => array('id', 'company_name','company_country', 'company_PFPType'),
                                )); 
           
            $queueId = $result['Userinvestmentdata']['queue_id'];
            if ($queueId <> $oldQueueId) {
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
                $this->resetInvestmentArray($platformglobalData[$index]);
 
                foreach ($result['Investment'] as $investmentKey => $data)  {                   
                    if ($data['investment_amount'] > 0) {
                        $platformglobalData[$index]['userplatformglobaldata_activeInInvestments'] += $data['investment_amount'];
                        $platformglobalData[$index]['userplatformglobaldata_numberOfInvestments']++;
                        $investorglobalData['investorglobaldata_activeInInvestments'] += $data['investment_amount'];
                        $activeInvestments = true;
                    }               
                    $platformglobalData[$index]['userplatformglobaldata_moneyInWallet'] += $result['Userinvestmentdata']['userinvestmentdata_myWallet']; 
                    $platformglobalData[$index]['userplatformglobaldata_currency'] = 1;
                    $platformglobalData[$index]['userplatformglobaldata_companyId'] = $companyId;            
                    $platformglobalData[$index]['userplatformglobaldata_companyName'] = $companyResult['Company']['company_name'];
                    $platformglobalData[$index]['userplatformglobaldata_PFPType'] = $companyResult['Company']['company_PFPType'];
                    $platformglobalData[$index]['userplatformglobaldata_PFPCountry'] = $companyResult['Company']['company_country']; 
                    $platformglobalData[$index]['userplatformglobaldata_globalIndicator'] = 3;      // This is a temporary dummy value  
        //               $investorglobalData['investorglobaldata_totalActiveInInvestments'] += $data[$index]['userinvestmentdata_activeInInvestments'];             
                }    
                if ($activeInvestments) {
                    $investorglobalData['investorglobaldata_activePFPs'] += 1;
                } 
                $investorglobalData['investorglobaldata_totalPFPs'] += 1;
            }
            else {
            }           
            $index++;
            $tempCount = count($userinvestmentdataResult);     
        }  
        $data1[] = array(
            'Investorglobaldata' => $investorglobalData,
            'Userplatformglobaldata' => $platformglobalData
           ); 
     
        $userinvestmentdataResult = $this->Userinvestmentdata->find("all", $params = array('recursive' => 1,
                                                              'conditions'  => array(// sort by queueid
                                                                      'userinvestmentdata_updateType' => SYSTEM_GENERATED,
                                                                                'queue_id > ' => $newMaxQueueId),
                                                                  'limit' => $limit ));  
//        $userinvestmentdataResult = null;   // Only needed for doing tests in a controlled environment
    }       // end of while      

    unset($data1[0]);       // Remove first index as it contains only dummy data
print_r($data1);

// Copy the data to the MLData tables and confirm that copy was succesfull
    foreach ($data1 as $tempData){
        $this->Mlqueue->id = 1;         // only one instance exists
        $savedQueueId = $tempData['Investorglobaldata']['queueId'];  
        echo "savedQueueId = $savedQueueId";
        
        if ($this->writeArray($tempData, "Investorglobaldata")) {
            // update the data in the mlqueues table
            $dataArray = array('mlqueue_actualQueueId' => $savedQueueId,
                                'mlqueue_lastIdDate' => $currentDate);
            $this->Mlqueue->save($dataArray);        
        }
        else {      // Error, store it in error database
            echo "error happened";
            
        }  
    }
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
    $investmentArray['userinvestmentdata_totalEarnedInterest'] = 0; 
    $investmentArray['userinvestmentdata_totalPercentage'] = 0;    
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
 * Writes an array of data to its corresponding model(s). This is done using transaction support of
 * the database.
 * The array is MODIFIED by this routine
 * 
 * @param 	$investorArray  Array to be reset
 * @param       $masterModel    String  Name of "master" model, of the hasOne or hasMany relationships
 * @return 	boolean	
 *
 */
private function writeArray(& $array, $masterModel) {
    $this->$masterModel = ClassRegistry::init('Adminpfp.' . $masterModel);    
    $this->$masterModel->transaction();   

    $this->$masterModel->create();
    $saved = $this->$masterModel->save($array[$masterModel], $validate = true);  
    $masterId = $this->$masterModel->id;

    unset($array[$masterModel]);    
    $models = array_keys($array);
 
    foreach ($models as $key => $model) {
        $this->$model = ClassRegistry::init('adminpfp.'. $model); 

        if (count($array[$model] > 0)) {
            foreach ($array[$model] as $key => $tempModel) {       
                $this->$model->create();
                $tempModel[strtolower($masterModel) . "_id"] = $masterId;
                $saved = $saved && $this->$model->save($tempModel, $validate = true);
            }
        }                          
    }
    if ($saved) {
    $this->$masterModel->transaction(true);
        return true;
    }
    $this->$masterModel->transaction(false);
    return false;    
}




}
