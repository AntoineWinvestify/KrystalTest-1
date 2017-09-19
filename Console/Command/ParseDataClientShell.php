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
        
        $response = [];
        while (true){
        $pendingJobs = $this->checkJobs(GLOBAL_DATA_DOWNLOADED, EXTRACTING_DATA_FROM_FILE, MAX_PARSERJOBS_IN_PARALLEL);     // One job at the time
            if (!empty($pendingJobs)) {
                // read job contents
                // determine the FQDN of the file
                $FQDNfile = 6;
                $parseResult = $this->parseFile($FQDNfile);
                if ($parseResult)  {
 // add jobs on Gearman level
                    // first collect all the relevant data (per investor) to be sent to Gearman worker
                    // queue_id, investorId, files to be decoded,
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                  parseFileFlow
                    
                    
                    
                    
                    
                    
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
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit
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