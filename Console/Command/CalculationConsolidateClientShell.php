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
 * This client deals with performing the parsing of the amortization table data that has been downloaded
 * from the PFP's. The Client starts analyzing
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
class CalculationConsolidateClientShell extends GearmanClientShell {

    public $uses = array('Queue2', 'Investment', 'Investmentslice');


// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
        return;
    }

    
    public function initClient() {

        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW3C";
        $inActivityCounter = 0;
        $workerFunction = "parseFileFlow";

        echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 3C Client\n";
        }

        $inActivityCounter++;

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        
        while (true) {
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, WIN_QUEUE_STATUS_STARTING_CALCULATION_CONSOLIDATION),
                                                  WIN_QUEUE_STATUS_STARTING_CALCULATION_CONSOLIDATION,
                                                $jobsInParallel);              
            
            print_r($pendingJobs);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }

            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue2']['queue2_userReference'];
                    $queueId = $job['Queue2']['id'];
                    $this->queueInfo[$job['Queue2']['id']] = json_decode($job['Queue2']['queue2_info'], true);
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                    $i = 0;

                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();
                        if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue2']['id']]['companiesInFlow'])) {
                            continue;
                        }
                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        
                        $this->userLinkaccountIds[$job['Queue2']['id']][$i] = $linkedAccountId;
                        $i++;
                        echo "pfp = " . $pfp . "\n";
                       
                        $allFiles = $dirs->findRecursive(WIN_FLOW_AMORTIZATION_TABLE_FILE . ".*");
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'userReference' => $job['Queue2']['queue2_userReference'],
                            'files' => $allFiles,
                            'actionOrigin' => $job['Queue2']['queue2_type'],          // this was WIN_ACTION_ORIGIN_ACCOUNT_LINKING,
                            'finishDate' => $this->queueInfo[$queueId]['date'],
                            'startDate' => $this->queueInfo[$queueId]['startDate'][$linkedAccountId],

                            'queueInfo' => json_decode($job['Queue2']['queue2_info'], true));
                    }

                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue2']['id'] . ".-;" .
                            $workerFunction . ".-;" . $job['Queue2']['queue2_userReference']);
                }

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }
               
                // before calling the method you should download all amortization tables and store them in the database (Flow 3A and 3B)
                
                
                
                echo "Calling consolidateData\n";
                $this->consolidateData($params);
                

 echo "Calling consolidatePaymentDelay\n";               
                $this->consolidatePaymentDelay($params);
                
                foreach ($this->queueInfo as $queueIdKey => $info) {
                    foreach ($info['companiesInFlow'] as $companieInFlow) {
                        $this->userResult[$queueIdKey][$companieInFlow] = 1;
                    }
                }


                $this->verifyStatus(WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED, "Amortization tables succesfully stored", WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);


        //        $this->GearmanClient->runTasks();

                // ######################################################################################################
            /*
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
                        $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                        $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS;
// Add the status per PFP, 0 or 1
                        
                        $newFlowState = WIN_queue2_STATUS_CALCULATION_CONSOLIDATION_FINISHED;

                    }

                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue2_status' => $newFlowState,
                        'queue2_info' => json_encode($this->queueInfo[$queueIdKey]),
                            ), $validate = true
                    );
                }
                break;
            */    
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
     * This method writes the 'investment_dateForPaymentDelayCalculation' in the investment object. 
     * This is done for the loanIds/loanslices whose amortization tables
     * are stored in the directory currently under processing. 
     * These files are of format:  amortizationtable_[investmentslice_id][loanId].html 
     * example: amortizationtable_120665_13730-01.html
     * 
     *  @param  $array          Array which holds global data of the P2P
     *  @return boolean 
     *
     */
    public function consolidateData(&$linkedAccountData) {
 
echo __FUNCTION__ . " " . __LINE__ . "\n";
        $timeStart = time();
print_r($linkedAccountData);

        foreach ($linkedAccountData as $linkedAccountKey => $linkedAccount) {           
            foreach ($linkedAccount['files'] as $tempName) {
                $name = explode("_", $tempName);
                $tempIdData = explode(".", $name[2]);
                $loanDataId[] = $tempIdData[0];
            }

            foreach ($loanDataId as $loanId) { 
                $tempNextScheduledDate = "";
                //$this->Investmentslice->Behaviors->load('Containable');
                //$this->Investmentslice->contain('Amortizationtable');              
                //$this->Investmentslice->contain('GlobalamortizationtableInvestmentslice');
                $result = $this->Investmentslice->find("all", array('conditions' => array('Investmentslice_identifier' => $loanId),
                                                                           'recursive' => 1)
                                                                        );

                if (isset($result[0]['Globalamortizationtable'])) {
                    $reversedData = array_reverse($result[0]['Globalamortizationtable']);     // prepare to search backwards in amortization table
                }
                else {
                    $reversedData = array_reverse($result[0]['Amortizationtable']);           // prepare to search backwards in amortization table
                }

                
                foreach ($reversedData as $table) {
                    if ($table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED || 
                                    $table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_LATE   ||
                                    $table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID) {
  
                            $tempNextScheduledDate = $table['amortizationtable_scheduledDate'];
                        }
                    if ($table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED || 
                                    $table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_LATE   ||
                                    $table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID) {                      
                            $tempNextScheduledDate = $table['globalamortizationtable_scheduledDate'];
                    }                 
                }
                if (Configure::read('debug')) {
                    echo "tempNextScheduledDate = $tempNextScheduledDate\n"; 
                }                
               
                $this->Investment->save(array('id' => $result[0]['Investmentslice']['investment_id'],
                                               'investment_dateForPaymentDelayCalculation' =>  $tempNextScheduledDate )
                                               );             
            } 
        }
                             
        $timeStop = time();
echo "\nNUMBER OF SECONDS EXECUTED IN " . __FUNCTION__ . " = " . ($timeStop - $timeStart) ."\n";
        return true;
    }
    
    
    
    
    /** 
     * 
     * This method scans through *ALL* active loans per P2P of an investor and calculates the number of days of 
     * payment delay. The result is written in the investment model object.
     *  
     *  @param  $array      Array which holds global data of the P2P
     *  @return boolean
     *
     */
    public function consolidatePaymentDelay(&$linkedAccountData) { 
 
echo __FUNCTION__ . " " . __LINE__ . "\n";
        $timeStart = time();


        foreach ($linkedAccountData as $linkedAccountKey => $linkedAccount) {
            $conditions = array("AND" => array( array('investment_statusOfLoan' => WIN_LOANSTATUS_ACTIVE), 
                                                      'linkedaccount_id'  => $linkedAccountKey
                                              ));
            $index = 0;
            $controlIndex = 0;
            $limit = WIN_DATABASE_READOUT_LIMIT;
            $investment = array();

echo "finishDate = "  . $linkedAccount['finishDate'] . "\n";              
                $todayYear = substr($linkedAccount['finishDate'], 0, 4); 
                $todayMonth = substr($linkedAccount['finishDate'], 4, 2);
                $todayDay = substr($linkedAccount['finishDate'], 6 ,2);
                $today = $todayYear . "-" . $todayMonth . "-" . $todayDay;
                $todayTimeStamp = strtotime($today);
echo "todayTimeStamp = $todayTimeStamp\n";
            
            do {
                $result = $this->Investment->find("all", array('conditions' => $conditions,
                                                                'fields'    => array('id', 
                                                                                     'investment_dateForPaymentDelayCalculation'),
                                                                'recursive'  => -1,
                                                                'limit' => $limit,
                                                                'offset' => $index * $limit)
                                                 );
                  
                if (count($result) < $limit) {          // No more results available
                    $controlIndex = 1;
                }

                foreach ($result as $item) {                      
echo "ITEM  = " . $item['Investment']['investment_dateForPaymentDelayCalculation'] . " \n";
print_r($item);


                    $dateTimeForPaymentDelayCalculation = strtotime($item['Investment']['investment_dateForPaymentDelayCalculation']);

                    if ($dateTimeForPaymentDelayCalculation < $todayTimeStamp) {
                        $tempArray['id'] = $item['Investment']['id'];                       
echo "Difference in seconds = " . abs($todayTimeStamp - $dateTimeForPaymentDelayCalculation) . "\n";
                        $tempArray['investment_paymentStatus'] = ceil(abs($todayTimeStamp - $dateTimeForPaymentDelayCalculation) / 86400);
                    }
                    else {
                        $tempArray['investment_paymentStatus'] = 0;
                    }
                    $tempArray['id'] = $item['Investment']['id'];
echo __FUNCTION__ . " " . __LINE__ . "\n";
print_r($tempArray);
                    $investment[] = $tempArray;
                }
                $index++;
            } 
            while($controlIndex < 1);
                $this->Investment->saveMany($investment, array('validate' => true));
        }
        
        $timeStop = time();
        echo "\nNUMBER OF SECONDS EXECUTED IN " . __FUNCTION__ . " = " . ($timeStop - $timeStart) ."\n";

        return true;
    }  
    
}
