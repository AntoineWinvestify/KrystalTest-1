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
 * 
 * 
 * 
 * 
 * PENDING:
 * 
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');
class CalculationConsolidationClientShell extends GearmanClientShell {

    public $uses = array('Queue', 'Investment', 'Investmentslice');
    protected $variablesConfig;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {

        return;
    }

    public function initConsolidationClient() {
        $handle = new UserDataShell();

        $this->resetTestEnvironment();      // Temporary function
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW3C";
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
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_START_CALCULATION_CONSOLIDATION, $jobsInParallel);
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
                        $allFiles = $dirs->findRecursive(WIN_FLOW_AMORTIZATION_TABLE_FILE . ".*");
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'files' => $allFiles,
                            'actionOrigin' => WIN_ACTION_ORIGIN_ACCOUNT_LINKING,
                            'queueInfo' => json_decode($job['Queue']['queue_info'], true));
                    }
                    print_r($params);

                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue']['id'] . ".-;" .
                            $workerFunction . ".-;" . $job['Queue']['queue_userReference']);
                }

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }
                

                // before calling the method you should download all amortization tables and store them in the database (Flow 3A and 3B)
                $this->consolidateData($params);
                exit;
                $this->consolidatePaymentDelay($params);
exit;





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
                        
                        $newFlowState = WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED;

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



    /** FLOW 3C
     * This method writes the 'nextPaymentDateTech' in the investment object. 
     * This is done for ALL the loanIds/loanslices whose amortization tables
     * are stored on the directory currently under processing. These files are of
     * format amortizationtable_[investmentslice_id][loanId].html 
     * example: amortizationtable_120665_13730-01.html
     * 
     * Check each of them and writes the next payment date in the investment table.
     * 
     *  @param  $array          Array which holds the list of identifiers of the active loans
     *
     *  @return boolean true
     *                  false
     *
     */
    public function consolidateData(&$linkedAccounts) {
 
echo __FUNCTION__ . " " . __LINE__ . "\n";
$timeStart = time();

        

        foreach ($linkedAccounts as $linkedAccountKey => $linkedAccount) {
            print_r($linkedAccount['queueInfo']);
            $tempNames = explode("_", $linkedAccount['files']);
            foreach ($linkedAccount['files'] as $tempName) {
                print_r($tempName);
                $name = explode("_", $tempName);
                $jsonLoanIds[] = $name[3];
                
            }
            print_r($jsonLoanIds);
            exit;
            $readoutDate = $file['queueInfo']['date'];
            $linkedAccountId = $paramsKey;
            echo $readoutDate . $linkedAccountId;
       //     $file = new File($params[$linkedAccountId]['files'][0]);        
       //     $jsonLoanIds = $file->read(true, 'r');
       //     $loanIds = json_decode($jsonLoanIds, true); 
    //        print_r($loanIds);            
            
echo "ANANA";
print_r($jsonLoanIds);
            foreach ($loanIds as $loanKey => $loanId) { // chunking is required
                echo "\n$loanKey and $loanId ";
    exit;
                $this->Investmentslice->Behaviors->load('Containable');
                $this->Investmentslice->contain('Amortizationtable');              

                $result = $this->Investmentslice->find("all", array('conditions' => array('Investmentslice.id' => $loanKey),
                                                                           'recursive' => 1)
                                                                        );

                foreach ($result[0]['Amortizationtable'] as $table) {
                    if ($table['amortizationtable_paymentDate'] == WIN_UNDEFINED_DATE) {
                        $scheduledDate = $table['amortizationtable_scheduledDate'];
                        echo $scheduledDate . " = " . $result[0]['Investmentslice']['investment_id'] . "\n";

               //         $this->Investment->save(array('id' => $result[0]['Investmentslice']['investment_id'],
               //                                        'investment_nextPaymentDate' =>  $scheduledDate )
               //                                        );
                        break;
                    }
                }
            } 
        }
                                   

$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";

        return true;
    }
    
    
    
    
    /** THIS FIELD IS NEEDED FOR THE CALCULATION OF DEFAULTED ETC.. FLOW 3D
     * This method scans through *ALL* active loans per P2P of an investor and calculates the number of days of 
     * payment delay. The result is written in the investment model object.
     *  
     * 
     *  @param  $array          Array which holds the list of identifiers of the active loans
     *
     *  @return boolean true
     *                  false
     *
     */
    public function consolidatePaymentDelay($params) {    
echo __FUNCTION__ . " " . __LINE__ . "\n";
$timeStart = time();
print_r($params);

        $conditions = array("AND" => array( array('investment_statusOfLoan' => WIN_LOANSTATUS_ACTIVE),
                                        //    array('investment_nextPaymentDate <' => "2017-11-30"
                                                
									));

        $index = 0;
        $controlIndex = 0;
        $limit = WIN_DATABASE_READOUT_LIMIT;
        $investment = array();
        
        do {
            $result = $this->Investment->find("all", array('conditions' => $conditions,
                                                            'fields'    => array('id', 
                                                                                 'investment_nextPaymentDate'),
                                                            'recursive'  => -1,
                                                            'limit' => $limit,
                                                            'offset' => $index * $limit)
                                             );

            if (count($result) < $limit) {          // No more results available
                $controlIndex = 1;
            }
           
            foreach ($result as $item) {   
                $nextPaymentDate = strtotime($item['Investment']['investment_nextPaymentDate']);
                $today = strtotime('2017-11-30');
 
                if ($nextPaymentDate < $today) {
                    $tempArray['id'] = $item['Investment']['id'];
                    $tempArray['investment_paymentStatus'] = ceil(abs($today - $nextPaymentDate) / 86400);
                    print_r($tempArray);
                    
                }
                else {
                    $tempArray['investment_paymentStatus'] = 0;
                }
                $tempArray['id'] = $item['Investment']['id'];
                $investment[] = $tempArray;
            }
            $index++;
        } 
        while($controlIndex < 1); 
        
        $this->Investment->saveMany($investment, array('validate' => true));

        
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";

        return true;
    }    
    
    
    
  
    
}
