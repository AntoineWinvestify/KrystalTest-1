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
 * @version 0.1
 * @date 2017-12-23
 * @package
 *
 * This client deals with performing the parsing of the files that have been downloaded
 * from the PFP's. Once the data has been parsed by the Worker, the Client starts analyzing
 * the data and writes the data-elements to the corresponding database tables.
 * Encountered errors are stored in the database table "applicationerrors".
 *
 *
 * 2017-12-23		version 0.1
 * Basic version
 *
 *
 *to add:
 * get next paymentDate
 * get paidInstalments
 * get nextpaymentDate
 * 
 * The S
 * 
 * 
 * 
 * PENDING:
 * 
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');
class ParseDataClientShell extends GearmanClientShell {

    public $uses = array('Queue', 'Investment', 'Investmentslice');
    protected $variablesConfig;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
 //       return;

        echo "Deleting Paymenttotal\n";
        $this->Paymenttotal->deleteAll(array('Paymenttotal.id >' => 0), false);

        echo "Deleting all Amortization Tables\n";
        $this->Payment = ClassRegistry::init('Payment');
        $this->Payment->deleteAll(array('Payment.id >' => 0), false);

        return;
    }

    public function initDataAnalysisClient() {
        $handle = new UserDataShell();

        $this->resetTestEnvironment();      // Temporary function
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW2";
        $inActivityCounter = 0;
        $workerFunction = "parseFileFlow";

        echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 2 Client\n";
        }

        //$resultQueue = $this->Queue->getUsersByStatus(FIFO, GLOBAL_DATA_DOWNLOADED);
        $inActivityCounter++;

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        Configure::load('internalVariablesConfiguration.php', 'default');
        $this->variablesConfig = Configure::read('internalVariables');
        
        while (true) {
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, $jobsInParallel);
            print_r($pendingJobs);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }

            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue']['queue_userReference'];
                    $queueId = $job['Queue']['id'];
                    $this->queueInfo[$job['Queue']['id']] = json_decode($job['Queue']['queue_info'], true);
                    print_r($this->queueInfo);
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue']['id']]['date'] . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                    $i = 0;

                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();
                        if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue']['id']]['companiesInFlow'])) {
                            continue;
                        }
                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        $this->userLinkaccountIds[$job['Queue']['id']][$i] = $linkedAccountId;
                        $i++;
                        echo "pfp = " . $pfp . "\n";
                        $files[WIN_FLOW_TRANSACTION_FILE] = $dirs->findRecursive(WIN_FLOW_TRANSACTION_FILE . ".*", true);
                        $files[WIN_FLOW_INVESTMENT_FILE] = $dirs->findRecursive(WIN_FLOW_INVESTMENT_FILE . ".*", true);
                        $files[WIN_FLOW_EXPIRED_LOAN_FILE] = $dirs->findRecursive(WIN_FLOW_EXPIRED_LOAN_FILE . ".*", true);
                        $listOfActiveInvestments = $this->getListActiveInvestments($linkedAccountId);

                        $controlVariableFile = $dirs->findRecursive(WIN_FLOW_CONTROL_FILE. ".*", true);
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'activeInvestments' => count($listOfActiveInvestments),
                            'listOfCurrentActiveLoans' => $listOfActiveInvestments,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'controlVariableFile' => $controlVariableFile[0],
                            'files' => $files,
                            'actionOrigin' => WIN_ACTION_ORIGIN_ACCOUNT_LINKING);
                    }
                    debug($params);

                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue']['id'] . ".-;" .
                            $workerFunction . ".-;" . $job['Queue']['queue_userReference']);
                }

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }

                $this->GearmanClient->runTasks();

                // ######################################################################################################

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n";
                }
                foreach ($this->tempArray as $queueIdKey => $result) {
                    foreach ($result as $platformKey => $platformResult) {
                        // First check for application level errors
                        // if an error is found then all the files related to the actions are to be
                        // deleted including the directory structure.
                        if (!empty($platformResult['error'])) {         // report error
                            $this->Applicationerror = ClassRegistry::init('applicationerror');
                            $this->Applicationerror->saveAppError("ERROR ", json_encode($platformResult['error']), 0, 0, 0);
                            // Delete all files for this user for this regular update
                            // break
                            continue;
                        }
                        $userReference = $platformResult['userReference'];
                        $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue']['id']]['date'] . DS;
                        $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS;
// Add the status per PFP, 0 or 1
                        
                        $mapResult = $this->mapData($platformResult);

                        if ($mapResult == true) { 
                            $newLoans = $platformResult['newLoans'];
                            if (!empty($newLoans)) {
                      //          $controlVariableFile =  $platformData['controlVariableFile'];
                                file_put_contents($baseDirectory . "loanIds.json", json_encode(($newLoans)));
                                $newFlowState = WIN_QUEUE_STATUS_DATA_EXTRACTED;
                            } 
                            else {
                                $newFlowState = WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED;
                            }
                        }
                        else {
                            echo "ERROR ENCOUNTERED\n"; 
                        }
                    }

                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue_status' => $newFlowState,
                        'queue_info' => json_encode($this->queueInfo[$queueIdKey]),
                            ), $validate = true
                    );
                }
                break;
            } 
            else {
                $inActivityCounter++;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n";
                }
                sleep(4);                                          // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT\n";
                   
                }
                exit;
            }
        }
    }



    /**
     * Read the identifiers of all active loans.
     * check for each one of them the next payment date and write it in the investment table
     * calculate the number of days overdue if applicable
     * 
     * 
     * Calculates the following fields:
     *  - Number of days of payment delay (overdue)
     *  - Next Payment date
     *  - Next Payment amount 
     * and store the data in the corresponding database table
     * 
     * 
     *  @param  $array          Array which holds the list of identifiers of the active loans
     *
     *  @return boolean true
     *                  false
     *
     */
    public function mapData(&$platformData) {
ini_set('memory_limit','2048M');      
$timeStart = time();
 
       
echo "FINISHED_ACCOUNT = $FINISHED_ACCOUNT   \n";
echo "STARTED_NEW_ACCOUNTS = $STARTED_NEW_ACCOUNTS \n"; 
$myArray = array ('finished' => $FINISHED_ACCOUNT,
            'finished_list' => $FINISHED_ACCOUNT_LIST,
            'countFinishedList' => count($FINISHED_ACCOUNT_LIST),
            'started_new_accounts'  => $STARTED_NEW_ACCOUNTS,
            'started_new_accounts_list' => $STARTED_NEW_ACCOUNTS_LIST,
            'countNewAccountList' => count($STARTED_NEW_ACCOUNTS_LIST),
            'finished_duplicates_list' => $FINISHED_DUPLICATES_LIST,
            'countFinishedDuplicatesList' => count($FINISHED_DUPLICATES_LIST),
            'measurements' => $tempMeasurements,
            'workingNewLoans' => $platformData['workingNewLoans'], 
            'countWorkingNewLoans' => count($platformData['workingNewLoans']),
            'errorDeletingWorkingNewloans' => $errorDeletingWorkingNewloans,
        );
file_put_contents("/home/antoine/controlData6.json", json_encode(($myArray)));
       

        $calculationClassHandle->consolidatePlatformData($database);
        // remove duplicates from the 'newLoans'AND remove all loans whose loanId/slice are in expiredLoans
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";

        return true;
    }
    
    
    
    
    
    
    
    
  
    
}
