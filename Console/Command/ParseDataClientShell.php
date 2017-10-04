<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version
 * @date
 * @package
 * 
 
 TODO:

 
 */


App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class ParseDataClientShell extends AppShell {
    protected $GearmanClient;
    private $newComp = [];
//    public $uses = array('Queue');
    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {
        
        echo "Nothing\n";
    } 
        
        
        
        
    public function initDataAnalysisClient() {

        $inActivityCounter = 0;
        $this->GearmanClient->addServers();
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        
        $this->Queue = ClassRegistry::init('Queue');
        $resultQueue = $this->Queue->getUsersByStatus(FIFO, GLOBAL_DATA_DOWNLOADED);
 
        $inActivityCounter++;                                           // Gearman client 

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallelToParse');

        $response = [];

        while (true){
            $pendingJobs = $this->checkJobs(GLOBAL_DATA_DOWNLOADED, $jobsInParallel);

            if (!empty($pendingJobs)) {
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue']['queue_userReference'];
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;

                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories

                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];

                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();

                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];   
                        $files = $this->readFilteredFiles($allFiles,  TRANSACTION_FILE + INVESTMENT_FILE);
                        $params[$linkedAccountId] = array('queue_id' => $job['Queue']['id'],
                                                                'pfp' => $pfp,
                                                    'userReference' => $job['Queue']['queue_userReference'], 
                                                            'files' => $files);   
                    }
                    print_r($params); 
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                } 
                $this->GearmanClient->runTasks();
            
            
            
                
                $result = json_decode($this->workerResult, true);
                $newLoansFound = NO;
                foreach ($result as $platformKey => $platformResult) {
                    if (!empty($platformResult['error'])) {         // report error
                        $this->Applicationerror = ClassRegistry::init('applicationerror');
                        $this->Applicationerror->saveAppError("ERROR ", json_encode($platformResult['error']), 0, 0, 0);
                        continue;
                    }
                    $userReference = $platformResult['userReference'];                  // for later use
                    $queueId = $platformResult['queue_id'];                             // for later use
                    $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;
                    $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS; 
       
                    $listOfLoans = $this->getListActiveLoans($platformKey);
                    foreach ($platformResult['parsingResult'] as  $loanIdKey => $tempPlatformResult) {                  
                        if (array_search($loanIdKey, $listOfLoans) !== false) {         // Check if new investments have appeared
                            $newLoans[] = $loanIdKey;
                            $newLoansFound = YES;
                        } 
                        if (!empty($newLoans)) {
                            $fileHandle = new File($baseDirectory .'loanIds.json', true, 0644);
                            if ($fileHandle) {
                                if ($fileHandle->append(json_encode($newLoans), true)) {  
                                    $fileHandle->close();
                                    echo "File " .  $baseDirectory . "loanIds.json written\n";
                                }
                            } 
                            print_r($newLoans);
                            unset($newLoans);
                        }
                    }

 //check if no error occured. If no error then store the data in the database.
 // if error occurred then use applicationerror to store it.
                } 
                
                if ($newLoansFound == NO) {
                    $newState = AMORTIZATION_TABLES_DOWNLOADED;
                    echo "No new loans found\n";
                }
                else {
                    $newState = DATA_EXTRACTED;
                    echo "Files parsed and new loans detected, loanIds need to be collected\n";
                }
                   
                $this->Queue = ClassRegistry::init('Queue');    
                $this->Queue->id = $queueId;
                $this->Queue->save(array('queue_status' => $newState), $validate = true);           
      
                }
            else {
                $inActivityCounter++;
                echo __METHOD__ . " " . __LINE__ . " Nothing in queue, so sleeping \n";                
                sleep (4);                                          // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                echo __METHOD__ . " " . __LINE__ . "Maximum Waiting time expired, so EXIT \n";                  
                exit;
            }
 break;
        }        
    }
    

    
    
    /**
     * checks to see if jobs are waiting in the queue for processing
     * 
     * @param int $presentStatus    status of job to be located
     * @param int $newStatus        status to change to when pulling job out of queue 
     * @param int $limit            Maximum number of jobs to be pulled out of the queue
     * @return array 
     * 
     */   
    public function checkJobs ($presentStatus, $limit) {

        // bad implementation, initializing x time the same model Queue
        $this->Queue = ClassRegistry::init('Queue');
    
        $userAccess = 0;
        $jobList = $this->Queue->getUsersByStatus(FIFO, $presentStatus, $userAccess, $limit);
        return $jobList;
    }    
        
  
    
    /**
     * Get the list of all active investments for a PFP as identified by the
     * linkedaccount identifier.
     * 
     * @param int $linkedaccount_id    linkedaccount reference
     * @return array 
     * 
     */
    public function getListActiveLoans($linkedaccount_id) {

        $this->Investment = ClassRegistry::init('Investment');    
 
// CHECK THESE FILTERCONDITIONS        
        $filterConditions = array( //'linkedaccount_id' => $linkedaccount_id,
                                    "investment_status" => -1,
                                );
	
	$investmentListResult = $this->Investment->find("all", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                                        "fields" => array("id", "investment_loanReference"),
									));   
        $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanReference');
        $list[] = "20729-01";
        return $list;
    }
    

    
    
    
    

      
   

    public function verifyFailTask(GearmanTask $task) {
        $data = $task->data();
        $this->workerResult = $task->data();       
        echo __METHOD__ . " " . __LINE__ . "\n";
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    public function verifyExceptionTask (GearmanTask $task) {
        $data = $task->data();
        $this->workerResult = $task->data();
        echo __METHOD__ . " " . __LINE__ .  "\n";
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    public function verifyCompleteTask (GearmanTask $task) {
        echo __METHOD__ . " " . __LINE__ . "\n";
        $data = explode(".-;", $task->unique());
        $this->workerResult = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "COMPLETE: ";
  //              $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;       

    }
    
    
    
    
 /*   
    
        foreach ($result as $platformKey => $platformResult) {
            foreach ($platformResult['parsingResult'] as  $loanIdKey => $tempPlatformResult) { 
  
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
*/    
    
    
    
    
    
}
