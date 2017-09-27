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
 */



class ParseDataClientShell extends AppShell {
    protected $GearmanClient;
    private $newComp = [];

    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {
        $this->GearmanClient->addServers();
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        
        $this->Queue = ClassRegistry::init('Queue');
        $resultQueue = $this->Queue->getUsersByStatus(FIFO, START_COLLECTING);
        
        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }
        
 
        if (empty($input)) {
            return $field;
        }    
        else {
            if ($overwrite) {
                return $field;
            }
        }      
  //       return "";        
        
        $inActivityCounter++;           // Gearman client 

        Configure::load('p2pGestor.php', 'default');
        $baseDirectory = Configure::read('dashboard2Files') . $userReference . "//" . date("Ymd",time());

        
        $response = [];
        while (true){
        $pendingJobs = $this->checkJobs(GLOBAL_DATA_DOWNLOADED, EXTRACTING_DATA_FROM_FILE, MAX_PARSERJOBS_IN_PARALLEL);
        // $pendingJobs contain the information as stored in Queue table, including id
            if (!empty($pendingJobs)) {
                // read job contents
                // determine the FQDN of the file
               //get the userReference and read all the required info of user, like its platforms
                $parseResult = $this->parseFile($FQDNfile);
                if ($parseResult)  {
 // add jobs on Gearman level
                    // first collect all the relevant data (per investor) to be sent to Gearman worker
                    // queue_id, investorId, files to be decoded,
      /*        
       *    $data['PFPname']['files']                  array 
     *      $data['PFPname']['files'][filename']       array of filenames, FQDN's
     *      $data['PFPname']['files'][typeOfFile']     type of file, CASHFLOW, INVESTMENT [one or more can be present,...
     *      $data['PFPname']['files']['filetype']      CSV, XLS or PDF
     *      $data['userReference']
     *      $data['queue_id']          */
                    
                    
                    
                    
                    foreach ($pendingJob as $jobKey => $job) {
                        $param = array('queue_id' => $pendingJob[$jobKey]['id'],
                                   'userReference' => $pendingJob[$jobKey]['queue_userReference'], 
                             );
                        
                    }
                    
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                  parseFileFlow;
                    

                }
                else {  // error occured, so deal with it
                        // store error data using applicationError
                         //    * @param string $par1 The first word is used in the subject of the mail send, this word must be ERROR, WARNING or INFORMATION
                    $par1 = 8;
                    $this->Applicationerror->saveAppError("ERROR", $par1, $par2, $par3, $par4, $par5);

                    }
            }
            else {
                $inActivityCounter++;
                sleep (4);                                          // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                exit;
            }
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
    public function checkJobs ($presentStatus, $newStatus, $limit) {
        // identify current sta
        $this->Queue->getNext();      
        
        
        
        return;
    }    
        
  
    
    /**
     * Read the names of the files (FDQN) that fullfil the $typeOfFiless bitmap
     * 
     * @param int $userReference    The unique user reference
     * @param int $typeOfFiles      bitmap of constants:
     *                              INVESTMENT_FILE, TRANSACTION_TABLE_FILE, CONTROL_FILE,
     * 
     */
    public function readDirFiles($userReference,$typeOfFiles)  {
        /*read configure parm from config file

        read userref 

        read contents*/

        $fileNameList = array();
        
        $baseDir = config . $userReference . "//" . date("Ymd",time()) . zank . 16034 ;
        if ($handle = opendir($baseDir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $dirname = $dir . "//" . $entry . "\n";
                    echo $dirname;
                    $fileNameList[] = $dirname;
                }
            }
            closedir($handle);
        }
        $print_r($fileNameList);
    } 
        
        
        
        

    
    private function verifyFailTask(GearmanTask $task) {
        $m = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    private function verifyExceptionTask (GearmanTask $task) {
        $m = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    private function verifyCompleteTask (GearmanTask $task) {
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
}