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
 * @author 0.1
 * @version
 * @date
 * @package
 */

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


/**
 * Generic class with method to start using a Gearman Client
 *
 */
class GearmanClientShell extends AppShell {
    
    protected $GearmanClient;
    protected $userResult = [];
    protected $userReference = [];
    protected $userLinkaccountIds = [];
    protected $queueInfo = [];
    protected $gearmanErrors = [];
    protected $date;
    protected $flowName;
    
    public $uses = array('Company', 'Queue');
    
    /**
     * Constructor of the class
     */
    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }
    
    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }
    
    /**
     * Function to catch a fail on a Gearman Worker
     * @param GearmanTask $task
     */
    public function verifyFailTask(GearmanTask $task) {
        $data = explode(".-;", $task->unique());
        if (empty($this->userReference[$data[0]])) {
            $this->userReference[$data[0]] = $data[2];
        }
        $this->userResult[$data[0]]['global'] = "0";
        $this->gearmanErrors[$data[0]]['global']['typeOfError'] = "GLOBAL ERROR on flow " . $this->flowName ;
        $this->gearmanErrors[$data[0]]['global']['detailedErrorInformation'] = "GLOBAL ERROR on " . $this->flowName
                . " with type of error: " . constant("WIN_ERROR_" . $this->flowName) . " AND subtype " . WIN_ERROR_FLOW_GEARMAN_FAIL ;
        $this->gearmanErrors[$data[0]]['global']['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
        $this->gearmanErrors[$data[0]]['global']['subtypeErrorId'] = WIN_ERROR_FLOW_GEARMAN_FAIL;
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: " . $task->data() . GEARMAN_WORK_FAIL . "\n";
    }
    
    /**
     * Function to catch a exception on a Gearman Worker
     * @param GearmanTask $task
     */
    public function verifyExceptionTask (GearmanTask $task) {
        $data = explode(".-;", $task->unique());
        if (empty($this->userReference[$data[0]])) {
            $this->userReference[$data[0]] = $data[2];
        }
        $this->userResult[$data[0]]['global'] = "0";
        $this->gearmanErrors[$data[0]]['global']['typeOfError'] = "GLOBAL ERROR on flow " . $this->flowName ;
        $this->gearmanErrors[$data[0]]['global']['detailedErrorInformation'] = "GLOBAL ERROR on " . $this->flowName
                . " with type of error: " . constant("WIN_ERROR_" . $this->flowName) . " AND subtype " . WIN_ERROR_FLOW_GEARMAN_EXCEPTION ;
        $this->gearmanErrors[$data[0]]['global']['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
        $this->gearmanErrors[$data[0]]['global']['subtypeErrorId'] = WIN_ERROR_FLOW_GEARMAN_EXCEPTION;
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: " . $task->data() . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    /**
     * Function that runs after a task was complete on the Gearman Worker
     * @param GearmanTask $task
     */
    public function verifyCompleteTask (GearmanTask $task) {
        $data = explode(".-;", $task->unique());
        if (empty($this->userReference[$data[0]])) {
            $this->userReference[$data[0]] = $data[2];
        }
        $dataWorker = json_decode($task->data(), true);
        foreach ($dataWorker['statusCollect'] as $linkaccountId => $status) {
            $this->userResult[$data[0]][$linkaccountId] = $status;
            $this->gearmanErrors[$data[0]][$linkaccountId] = $dataWorker['errors'][$linkaccountId];
        }
        print_r($this->userResult);
        print_r($this->userReference);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
    
    /**
     * Function to delete a folder of a day and a investor if there was some 
     * fail on the process to collect his data
     * @param string $queueId It is the queueId
     * @param integer $linkAccountId It is the link account id
     * @return boolean It's true if the deleted was successful
     */
    public function deleteFolderByDateAndLinkaccountId($queueId, $linkAccountId) {
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $path = $this->userReference[$queueId] . DS . $this->date . DS . $linkAccountId;
        print_r($this->userReference);
        $path = $partialPath . DS . $path;
        $folder = new Folder($path);
        $delete = false;
        if (!is_null($folder->path)) {
            $delete = $folder->delete();
        }
        return $delete;
    }
    
    /**
     * Function to verify that the collection of data was successful on all the 
     * workers per company and per user, if a company failed, the function will delete it.
     * If a massive fail occurs, the function will delete all the folders
     * @param string $userResult It is the result of the collection of data
     * @return boolean It is true if the process was successful
     */
    public function consolidationResult($userResult, $queueId) {
        $statusProcess = true;
        $globalDestruction = false;
        unset($this->queueInfo[$queueId]['companiesInProcess']);
        foreach ($userResult as $linkaccountId => $result) {
            if ($linkaccountId == 'global') {
                $globalDestruction = true;
                break;
            }
            if (!$result) {
                $statusProcess = false;
                $this->deleteFolderByDateAndLinkaccountId($queueId, $linkaccountId); //1 = $todaydate
                $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                $this->gearmanErrors[$queueId][$linkaccountId]['typeOfError'] = "ERROR on flow " . $this->flowName . " and linkAccountId " . $linkaccountId ;
                $this->gearmanErrors[$queueId][$linkaccountId]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                        . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] . " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId]['subtypeErrorId'] ;
                print_r($this->gearmanErrors);
                $this->queueInfo[$queueId]['companiesInProcess'][] = $linkaccountId; 
                $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId]);
            }
        }
        
        if ($globalDestruction) {
            foreach ($this->userLinkaccountIds[$queueId] as $key => $userLinkaccountId) {
                $this->deleteFolderByDateAndLinkaccountId($queueId, $userLinkaccountId);
                $this->queueInfo[$queueId]['companiesInProcess'][] = $userLinkaccountId;
            }
            $statusProcess = false;
        }
        return $statusProcess;
    }
    
    /**
     * Function to verify if a folder exist searching by path or it containing files
     * @param string $userReference It is the user reference used by our database
     * @param integer $linkaccountId It is the link account id
     * @param string $fileName It is the name of the file to look for inside the folder
     * @return boolean It's true if the folder exists
     */
    public function verifyCompanyFolderExist($userReference, $linkaccountId, $fileName = null) {
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $path = $userReference . DS . $this->date . DS . $linkaccountId;
        print_r($path);
        $path = $partialPath . DS . $path;
        $folder = new Folder($path);
        $folderExist = false;
        if (empty($fileName)) {
            if (!is_null($folder->path)) {
                $folderExist = true;
            }
        }
        else {
             $files = $folder->findRecursive($fileName . ".*");
             if ($files) {
                $folderExist = true;
             }
             
        }
        return $folderExist;
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
        $userAccess = 0;
        $jobList = $this->Queue->getUsersByStatus(FIFO, $presentStatus, $userAccess, $limit);
        return $jobList;
    }    
    
    public function saveGearmanError($error) {
        if (empty($this->Applicationerror)) {
            $this->Applicationerror = ClassRegistry::init('Applicationerror');
        }
        if (empty($error['line'])) {
            $error['line'] = null;
        }
        if (empty($error['file'])) {
            $error['file'] = null;
        }
        if (empty($error['urlsequenceUrl'])) {
            $error['urlsequenceUrl'] = null;
        }
        if (empty($error['subtypeErrorId'])) {
            $error['subtypeErrorId'] = null;
        }
        $this->Applicationerror->saveAppError($error['typeOfError'],$error['detailedErrorInformation'], $error['line'], $error['file'], $error['urlsequenceUrl'], $error['typeErrorId'], $error['subtypeErrorId']);
    }
    
    public function verifiedStatus($status, $message, $restartStatus, $errorStatus) {
        foreach ($this->userResult as $queueId => $userResult) {
            $statusProcess = $this->consolidationResult($userResult, $queueId);
            $this->Queue->id = $queueId;
            $queueInfo = null;
            if ($statusProcess) {
                $newState = $status;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . $message;
                }
            } else {
                $newState = $restartStatus;
                echo "There was an error downloading data";
                if (empty($this->queueInfo[$queueId]['numberTries'])) {
                    $this->queueInfo[$queueId]['numberTries'] = 1;
                } else if ($this->queueInfo[$queueId]['numberTries'] == 1) {
                    $this->queueInfo[$queueId]['numberTries'] = 2;
                } else {
                    $newState = $errorStatus; //UNRECOVERED_ERROR_ENCOUNTERED;
                }
                $queueInfo = $this->queueInfo[$queueId];
            }
            $this->Queue->save(array(
                    'queue_status' => $newState,
                    'queue_info' => $queueInfo
                ),
                $validate = true);
        }
    }
}
