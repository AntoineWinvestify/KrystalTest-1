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
 * @author Antoine
 * @version 0.5
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
    protected $date;            // End date of collection period, i.e. the date when the files were collected
    protected $dateStart;       // Start date of collection period
    protected $flowName;
    protected $tempArray = [];
    
    public $uses = array('Company', 'Queue2');
    
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
     * 
     * @param GearmanTask $task It is a Gearman::Client's representation of a task to be done.
     *          $task->unique Returns the unique identifier for this task. This is assigned by the GearmanClient
     *                  $data[0] It is the queueId of the task
     *                  $data[1] It is the function name on the Gearman Worker
     *                  $data[2] It is the userReference        
     * 
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
        $this->gearmanErrors[$data[0]]['global']['EId'] = constant("WIN_ERROR_" . $this->flowName);
        $this->gearmanErrors[$data[0]]['global']['subtypeErrorId'] = WIN_ERROR_FLOW_GEARMAN_FAIL;
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: " . $task->data() . GEARMAN_WORK_FAIL . "\n";
    }
    
    /**
     * Function to catch a exception on a Gearman Worker
     * @param GearmanTask $task It is a Gearman::Client's representation of a task to be done.
     *          $task->unique Returns the unique identifier for this task. This is assigned by the GearmanClient
     *                  $data[0] It is the queueId of the task
     *                  $data[1] It is the function name on the Gearman Worker
     *                  $data[2] It is the userReference  
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
     * 
     * @param GearmanTask $task It is a Gearman::Client's representation of a task done.
     *          string $task->unique Returns the unique identifier for this task. This is assigned by the GearmanClient
     *                  $data[0] It is the queueId of the task
     *                  $data[1] It is the function name on the Gearman Worker
     *                  $data[2] It is the userReference  
     *          string $task->data Returns data being returned for a task by a worker
     *                  $data["statusCollect"] It is the status of the request by linkaccount Id
     *                  $data["errors"] If the statusCollect is 0, the error is saved on it
     *                  $data["tempArray"] The information to save on database by linkaccount id
     */
    public function verifyCompleteTask (GearmanTask $task) {
        echo "Received data from Worker \n";
        $data = explode(".-;", $task->unique());
        if (empty($this->userReference[$data[0]])) {
            $this->userReference[$data[0]] = $data[2];
        }
        $dataWorker = json_decode($task->data(), true);
        
        if (!empty($dataWorker['statusCollect'])) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Collecting status from worker \n");
            foreach ($dataWorker['statusCollect'] as $linkaccountId => $status) {
                $this->userResult[$data[0]][$linkaccountId] = $status;
                $this->gearmanErrors[$data[0]][$linkaccountId] = $dataWorker['errors'][$linkaccountId];
            }
        }
        if (!empty($dataWorker['tempArray'])) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "saving tempArray into global variable \n");
            $this->tempArray[$data[0]] = $dataWorker['tempArray'];
        }  
        if (!empty($dataWorker['tempUrlArray'])) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "saving tempArray into global variable \n");
            foreach ($dataWorker['tempUrlArray'] as $linkaccountId => $tempUrl) {
                $dataTempArray = $this->getFileTempArray($tempUrl);
                $this->tempArray[$data[0]][$linkaccountId] = $dataTempArray;
            }
        }
        echo "ID Unique: " . $task->unique() . "\n";
//        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
    
    /**
     * Function to delete a folder of a day and a investor if there was some 
     * fail on the process to collect his data
     * 
     * @param string $queueId It is the queueId
     * @param integer $linkAccountId It is the link account id
     * @return boolean It's true if the deleted was successful
     */
    public function deleteFolderByDateAndLinkaccountId($queueId, $linkAccountId) {
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $flow = constant("WIN_ERROR_" . $this->flowName);
        $date = date("Ymd", strtotime($this->date));
        $path = $this->userReference[$queueId] . DS . $date . DS . $linkAccountId;
        print_r($this->userReference);
        $path = $partialPath . DS . $path;
        $folder = new Folder($path);
        $delete = false;
        if ($flow < WIN_ERROR_GEARMAN_FLOW3A) {
            if (!is_null($folder->path)) {
                $delete = $folder->delete();
            }
        }
        else {
            $allFiles = $folder->findRecursive($this->fileName . ".*");
            foreach ($allFiles as $file) {
                $fileInit = new File($file);
                $fileInit->delete();
            }
        }
        return $delete;
    }
    
    /**
     * Function to verify that the collection of data was successful on all the 
     * workers per company and per user, if a company failed, the function will delete it.
     * If a massive fail occurs, the function will delete all the folders
     *  
     * @param string $userResult It is the result of the collection of data
     * @return boolean It is true if the process was successful
     */
    public function consolidationResult($userResult, $queueId) {
        $statusProcess = true;
        $globalDestruction = false;
        unset($this->queueInfo[$queueId]['companiesInFlow']);
        foreach ($userResult as $linkaccountId => $result) {
            if ($linkaccountId == WIN_STATUS_COLLECT_GLOBAL_ERROR) {
                $globalDestruction = true;
                break;
            }
            if (!$result) {
                //$statusProcess = false;
                $this->deleteFolderByDateAndLinkaccountId($queueId, $linkaccountId); //1 = $todaydate
                $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                $this->gearmanErrors[$queueId][$linkaccountId]['typeOfError'] = "ERROR on flow " . $this->flowName . " and linkAccountId " . $linkaccountId ;
                $this->gearmanErrors[$queueId][$linkaccountId]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                        . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] . " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId]['subtypeErrorId'] ;
                print_r($this->gearmanErrors);
                $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId]);
                $this->requeueFailedCompany($queueId, $linkaccountId);
                continue;
            }
            $this->queueInfo[$queueId]['companiesInFlow'][] = $linkaccountId; 
        }
        
        if ($globalDestruction) {
            foreach ($this->userLinkaccountIds[$queueId] as $key => $userLinkaccountId) {
                $this->deleteFolderByDateAndLinkaccountId($queueId, $userLinkaccountId);
                $this->queueInfo[$queueId]['companiesInFlow'][] = $userLinkaccountId;
            }
            $statusProcess = false;
        }
        return $statusProcess;
    }
    
    /**
     * Function to verify if a folder exist searching by path or it containing files
     * 
     * @param string $userReference It is the user reference used by our database
     * @param integer $linkaccountId It is the link account id
     * @param string $fileName It is the name of the file to look for inside the folder
     * @return boolean It's true if the folder exists
     */
    public function verifyCompanyFolderExist($userReference, $linkaccountId, $fileName = null) {
        $partialPath = Configure::read('dashboard2Files');
        $internalPath = $userReference . DS . $this->date . DS . $linkaccountId; 
        $path = $partialPath . $internalPath;

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
     * Checks to see if jobs are waiting in the queue for processing
     * 
     * @param int $presentStatus    status of job to be located
     * @param int $newStatus        status to change to when pulling job out of queue 
     * @param int $limit            Maximum number of jobs to be pulled out of the queue
     * @return array 
     * 
     */   
    public function checkJobs ($presentStatus, $newStatus, $limit) {

        if (is_array($presentStatus)) {
            foreach ($presentStatus as $presentStatusKey => $status) {
                $list[] = array("queue2_status" => $status);
            }
                $conditions = array("AND" => array($list));
        }
        else {
            $conditions = array("AND" => array('investor_id' => $presentStatus));
        }

        $userAccess = 0;

        $jobList = $this->Queue2->getUsersByStatus(FIFO, $presentStatus, $userAccess, $limit);
        if (!empty($jobList)) {
            $tempData = array();
            foreach ($jobList as $job) {
                $jobListId = $job['Queue2']['id'];
                $tempData[] = array('id' => $jobListId,
                                  'queue2_status' => $newStatus
                                  );
            }

            $this->Queue2->saveMany($tempData, array('validate' => true));
        }
        
        return $jobList;
    }    
    
    /**
     * Function to save an application error produced on a Gearman Worker
     * @param array $error It contains all the information about the error
     *              $error['line'] It is the line where the error happened
     *              $error['file'] It is the file where the error happened
     *              $error['urlsequenceUrl'] It is the url sequence if applied where the error happened
     *              $error['subtypeErrorId'] It is the subtype of the error
     *              $error['typeOfError'] It is the type of error or the summary of the detailed information of the error
     *              $error['detailedErrorInformation'] It is the detailed information of the error
     *              $error['typeErrorId'] It is the principal id of the error
     */
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
    
    /**
     * Function to verify that the job was successful per user and per company
     * @param int $status It is the Id of the next queue status
     * @param string $message It is the message to show on console
     * @param int $restartStatus It is the Id of the queue if something fail
     * @param int $errorStatus It is the Id of the queue if the error repeats and it is irrecoverable
     * @param array $this->userResult This array is a variable class where we save the completion status of a pfp
     *                                The variable is created as: this->userResult[$queueId][$linkedaccountId]
     */
    public function verifyStatus($status, $message, $restartStatus, $errorStatus) {       
        foreach ($this->userResult as $queueId => $userResult) {
            $globalDestruction = false;
            unset($this->queueInfo[$queueId]['companiesInFlow']);
            /*
             * foreach to verify if there was an error in the worker collecting the data
             * $linkaccountId if it's defined as global, there was a crash error in the worker
             * $result it is true if everything was correct and false if there was an error
             */
            foreach ($userResult as $linkaccountId => $result) {
                if ($linkaccountId == WIN_STATUS_COLLECT_GLOBAL_ERROR) {
                    $globalDestruction = true;
                    break;
                }
                if (!$result) {
                    $this->requeueFailedCompany($queueId, $linkaccountId, $restartStatus, $errorStatus, count($userResult));
                    if (!empty($this->tempArray)) {
                        unset($this->tempArray[$queueId][$linkaccountId]);
                    }
                    continue;
                }
                else if ($result === WIN_STATUS_COLLECT_WARNING) {
                    $dataStatus['nextStatus'] = $status;
                    $dataStatus['restartStatus'] = $restartStatus;
                    $dataStatus['errorStatus'] = $errorStatus;
                    $this->requeueCompanyWithWarning($queueId, $linkaccountId, $dataStatus, count($userResult));
                    if (!empty($this->tempArray)) {
                        unset($this->tempArray[$queueId][$linkaccountId]);
                    }
                    continue;
                }
                $this->queueInfo[$queueId]['companiesInFlow'][] = $linkaccountId; 
            }
            /*
             * When there is a globalDestruction, all content of the files are deleted
             */
            if ($globalDestruction) {
                foreach ($this->userLinkaccountIds[$queueId] as $key => $userLinkaccountId) {
                    $this->deleteFolderByDateAndLinkaccountId($queueId, $userLinkaccountId);
                    $this->queueInfo[$queueId]['companiesInProcess'][] = $userLinkaccountId;
                    if (!empty($this->tempArray)) {
                        unset($this->tempArray[$queueId][$linkaccountId]);
                    }
                }
            }
            if (!empty($this->queueInfo[$queueId]['companiesInFlow'])) {
                    $this->Queue2->id = $queueId;
                if (!$globalDestruction) {
                    $newState = $status;
                    $this->queueInfo[$queueId]['numberTries'] = 0;
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . $message;
                    }
                } 
                else {
                    $data = $this->getFailStatus($queueId, $restartStatus, $errorStatus);
                    $newState = $data["newStatus"];
                    $this->queueInfo[$queueId]["numberTries"] = $data["numberTries"];
                }
                $this->Queue2->save(array(
                            'queue2_status' => $newState,
                            'queue2_info' => json_encode($this->queueInfo[$queueId])
                        ),
                        $validate = true
                );
            }
            //$statusProcess = $this->consolidationResult($userResult, $queueId);
        }
    }
    
    /**
     * Function to requeue a company when fails to collect the data or parse the information
     * @param int $queueId It is the queueId of the request
     * @param int $linkaccountId It is the linkaccountId of the company to requeue
     * @param int $restartStatus It is the status to which we must put if the request is to be restarted
     * @param int $errorStatus It is the status to which we must put if the request fails
     * @param int $numberOfCompanies It is the number of companies in the actual flow
     */
    public function requeueFailedCompany($queueId, $linkaccountId, $restartStatus, $errorStatus, $numberOfCompanies) {
        $this->deleteFolderByDateAndLinkaccountId($queueId, $linkaccountId); //1 = $todaydate
        $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
        $this->gearmanErrors[$queueId][$linkaccountId]['typeOfError'] = "ERROR on flow " . $this->flowName . " and linkAccountId " . $linkaccountId ;
        $this->gearmanErrors[$queueId][$linkaccountId]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] . " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId]['subtypeErrorId'] ;
        print_r($this->gearmanErrors);
        $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId]);
        $data = [];
        $newData = $this->getFailStatus($queueId, $restartStatus, $errorStatus);
        $data["numberTries"] = $newData["numberTries"];
        $data["companiesInFlow"][0] = $linkaccountId;
        $userReference = $this->userReference[$queueId];
        $data["date"] = $this->queueInfo[$queueId]["date"];
        $data["originExecution"] = $this->queueInfo[$queueId]['originExecution'];
        $newQueueId = null;
        if ($numberOfCompanies == 1) {
            $newQueueId = $queueId;
        }
        $result = $this->Queue2->addToQueueDashboard2($userReference, json_encode($data), $newData["newStatus"], $newQueueId);
    }
    
    /**
     * Function to get the status we must put on the queue request if it failed
     * @param int $queueId It is the queueId of the request
     * @param int $restartStatus It is the status to which we must put if the request is to be restarted
     * @param int $errorStatus It is the status to which we must put if the request fails
     */
    public function getFailStatus($queueId, $restartStatus, $errorStatus) {
        $data["newStatus"] = $restartStatus;
        echo "There was an error downloading data";
        if (empty($this->queueInfo[$queueId]['numberTries'])) {
            $data["numberTries"] = 1;
        } 
        else if ($this->queueInfo[$queueId]['numberTries'] == 1) {
            $data['numberTries'] = 2;
        } 
        else {
            $data["newStatus"] = $errorStatus; //UNRECOVERED_ERROR_ENCOUNTERED;
        }
        return $data;
    }
    
    public function requeueCompanyWithWarning($queueId, $linkaccountId, $status, $numberOfCompanies) {
        $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] = constant("WIN_WARNING_" . $this->flowName);
        $this->gearmanErrors[$queueId][$linkaccountId]['typeOfError'] = "WARNING on flow " . $this->flowName . " and linkAccountId " . $linkaccountId ;
        $this->gearmanErrors[$queueId][$linkaccountId]['detailedErrorInformation'] = "WARNING on " . $this->flowName
                . " with type of warning: " . $this->gearmanErrors[$queueId][$linkaccountId]['typeErrorId'] . " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId]['subtypeErrorId'];
        $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId]);
        $data = [];
        $newData = $this->getWarningStatus($queueId, $status['restartStatus'], $status['nextStatus']);
        $data["numberTriesWarning"] = $newData["numberTriesWarning"];
        $data["companiesInFlow"][0] = $linkaccountId;
        $userReference = $this->userReference[$queueId];
        $data["date"] = $this->queueInfo[$queueId]["date"];
        $data["originExecution"] = $this->queueInfo[$queueId]['originExecution'];
        $newQueueId = null;
        if ($numberOfCompanies == 1) {
            $newQueueId = $queueId;
        }
        $result = $this->Queue2->addToQueueDashboard2($userReference, json_encode($data), $newData["newStatus"], $newQueueId);
    }
    
    public function getWarningStatus($queueId, $restartStatus, $nextStatus) {
        $data["newStatus"] = $restartStatus;
        echo "There was a warning downloading data";
        if (empty($this->queueInfo[$queueId]['numberTriesWarning'])) {
            $data["numberTriesWarning"] = 1;
        } 
        else if ($this->queueInfo[$queueId]['numberTriesWarning'] == 1) {
            $data['numberTriesWarning'] = 2;
        } 
        else {
            $data["newStatus"] = $nextStatus; //UNRECOVERED_WARNING_ENCOUNTERED;CONTINUE EXECUTION 
            $data['numberTriesWarning'] = 0;
        }
        return $data;
    }
}
