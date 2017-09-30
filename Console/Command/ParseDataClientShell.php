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
 callbacks need to write some data to the database
 
 */



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
 
        $inActivityCounter++;           // Gearman client 

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallelToParse');

        $response = [];
        echo __METHOD__ . " " . __LINE__ . "\n";  
        while (true){
            $pendingJobs = $this->checkJobs(GLOBAL_DATA_DOWNLOADED, $jobsInParallel);

            if (!empty($pendingJobs)) {
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue']['queue_userReference'];
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;
                    $newDir = array();
                   
                    foreach (new DirectoryIterator($directory) as $fullFilename => $fileInfo) {
                        if(!$fileInfo->isDot()){ 
                            if($fileInfo->isDir()){
                                $dirs = $fileInfo->getPath(). DS . $fileInfo->getFilename(); // get next directory level
                                $newDir[] = $dirs;
                            }
                        }
                    }

                    foreach ($newDir as $level1NewDir) {
                         foreach (new DirectoryIterator($level1NewDir) as $fullFilename => $fileInfo) {
                            if(!$fileInfo->isDot()){ 
                                if($fileInfo->isDir()){
                                    $tempDir = $fileInfo->getPath(). DS . $fileInfo->getFilename(); // get next directory level
                                    $newDirLevel[] = $tempDir;
                                }
                            }
                        }    
                    }    

                    foreach ($newDirLevel as $dirKey => $subDirectory) {
                        $tempPfpName = explode("/", $subDirectory);
                        $linkedAccountId = $tempPfpName[count($tempPfpName) - 2];
                        $pfp = $tempPfpName[count($tempPfpName) - 1];
                        // we have the platform, and the directory where all the files are stored.
                        $files = $this->readDirFiles($subDirectory, TRANSACTION_FILE + INVESTMENT_FILE );

                        $params[$linkedAccountId] = array('queue_id' => $job['Queue']['id'],
                                                                'pfp' => $pfp,
                                                    'userReference' => $job['Queue']['queue_userReference'], 
                                                            'files' => $files);   
                    }    
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                    unset($tempPfpName);
                    echo "PARAMETERS = ";
                    print_r($params);                   
                    unset($params);
                } 
            

            $this->GearmanClient->runTasks();
            
echo __METHOD__ . " " . __LINE__ . "\n";           

            $result = json_decode($this->workerResult);
            print_r($result);
            foreach ($result as $key => $userResult) {
 //check if no error occured. If no error then store the data in the database.
 // if error occurred then use applicationerror to store it.
                $this->Queue->id = $key;
                if ($statusProcess) {
                    $newState = AMORTIZATION_TABLES_DOWNLOADED;
                    echo "Files succcessfully parsed, no new loans found";
                }
                else {
                    $newState = DATA_EXTRACTED;
                    echo "Files succcessfully parsed and new loans detected, loanIds need to be collected\n";
                }
                $this->Queue->save(array('queue_status' => $newState), $validate = true);
        }
        
        
            
            
            
            
            
            
            
/*            
            else {  // error occured, so deal with it
                    // store error data using applicationError
                     //    * @param string $par1 The first word is used in the subject of the mail send, this word must be ERROR, WARNING or INFORMATION
                $par1 = 8;
                $this->Applicationerror->saveAppError("ERROR", $par1, $par2, $par3, $par4, $par5);

                }
            }
*/           

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
    
   
    
    
    
    
    
    
    
 public function recursiveDirectoryIterator ($directory = null, $files = array()) {
    $iterator = new \DirectoryIterator ( $directory );

    foreach ( $iterator as $info ) {
        if ($info->isFile ()) {
            $files [$info->__toString ()] = $info;
        } elseif (!$info->isDot ()) {
            $list = array($info->__toString () => $this->recursiveDirectoryIterator(
                        $directory.DIRECTORY_SEPARATOR.$info->__toString ()
            ));
            if(!empty($files))
                $files = array_merge_recursive($files, $filest);
            else {
                $files = $list;
            }
        }
    }
    return $files;
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
        
  
    

      
   

    public function verifyFailTask(GearmanTask $task) {
        $m = $task->data();
        echo __METHOD__ . " " . __LINE__ . "\n";
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    public function verifyExceptionTask (GearmanTask $task) {
        $m = $task->data();
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
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;       
        
    }
}
