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
 * @version 0.2
 * @date 2018-06-18
 * @package
 */
 /*
 * This Client starts analyzing the data of the amortization tables and writes the data-elements 
 * 'investment_nextPaymentDate', 'investment_dateForPaymentDelayCalculation' and 
 * 'investment_paymentStatus' to the corresponding database tables.
 * Encountered errors are stored in the database table "applicationerrors".
 *
 *
 * 2017-12-23		version 0.1
 * Basic version
 *
 * 2018-06-18           version 0.2
 * Added "netPaymentDate"
 * 
 * 
 * PENDING:
 * get paidInstalments
 *
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');
class CalculationConsolidateClientShell extends GearmanClientShell {

    public $uses = array('Queue2', 'Investment', 'Investmentslice', 'Amortizationtable', 
                        'GlobalamortizationtablesInvestmentslice', 'Globalamortizationtable');
    public $today;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
        return;
    }

    
    public function initClient() {
        $this->today  = date('Y-m-d', time());
        
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
               
                echo "Calling consolidateData\n";
                $this->consolidateData($params);
                
                echo "Calling consolidatePaymentDelay\n";               
                $this->consolidatePaymentDelay($params);
                
                echo "Calling calculateNextPaymentDates\n";  
                $this->calculateNextPaymentDates($params);

                //?
                foreach ($this->queueInfo as $queueIdKey => $info) {
                    foreach ($info['companiesInFlow'] as $companieInFlow) {
                        $this->userResult[$queueIdKey][$companieInFlow] = 1;
                    }
                }

                $this->verifyStatus(WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED, "Amortization tables succesfully stored", WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
    
            } 
            else {
                $inActivityCounter++;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n";
                }
                sleep(4);                                                       // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {                          // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT\n";    
                }
                exit;
            }
        }
    }



    /** 
     * This method writes the 'investment_dateForPaymentDelayCalculation' in the investment object. 
     * This is done for the loanIds/loanslices whose amortization tables
     * are stored in the directory currently under processing. 
     * These files are of format:  amortizationtable_[investmentslice_id][loanId].html 
     * example: amortizationtable_120665_13730-01.html
     * 
     *  @param  $array          Array which holds global data of the P2P
     *  @return boolean 
     */
    public function consolidateData(&$linkedAccountData) {

        $timeStart = time();

        foreach ($linkedAccountData as $linkedAccount) {           
            foreach ($linkedAccount['files'] as $tempName) {
                $name = explode("_", $tempName);
                $sliceIdTemp = $name[count($name) - 2 ];
                $loanDataId[] = $sliceIdTemp;
            }

            foreach ($loanDataId as $sliceId) {
                $tempNextScheduledDate = "";

                $result = $this->Investmentslice->find("all", array('conditions' => ['Investmentslice.id' => $sliceId],       
                                                                     'recursive' => 1)
                                                                        );
                
                if ($this->Investmentslice->hasChildModel($sliceId, "Amortizationtable")) {
                    $reversedData = array_reverse($result[0]['Amortizationtable']);     // prepare to search backwards in amortization table
                    foreach ($reversedData as $table) {
                        if ($table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED || 
                                        $table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_LATE   ||
                                        $table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID) {

                            $tempNextScheduledDate = $table['amortizationtable_scheduledDate'];
                        }
                    } 
                   
                }
                else {  
                    echo "testing globals\n "; 
                    $lists = $this->GlobalamortizationtablesInvestmentslice->find("all",  array('conditions' => array('investmentslice_id' => $sliceId), 
                                                                      'fields' => array('id', 'globalamortizationtable_id')
                                                    )); 

                    foreach ($lists as $list) {
                        $filteringConditions = array('id' => $list['GlobalamortizationtablesInvestmentslice']['globalamortizationtable_id']);

                        $result = $this->Globalamortizationtable->find("first", array('conditions' => $filteringConditions,
                                                                                          'fields' => ['id', 'globalamortizationtable_scheduledDate',
                                                                                                       'globalamortizationtable_paymentStatus'],
                                                                                          'recursive' => -1
                                                                                           ));  
                        $globalTable[] = $result;
                    }
                
                    $amortizationTable = Hash::extract($globalTable, '{n}.Globalamortizationtable');                     
                    $reversedData =  array_reverse($amortizationTable);         // prepare to search backwards in amortization table

                    foreach ($reversedData as $table) { 
                        if ($table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED || 
                                        $table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_LATE   ||
                                        $table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID) {                      
                                $tempNextScheduledDate = $table['globalamortizationtable_scheduledDate'];
                        } 
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
        exit;         
        return true;
    }

    
    /** 
     * This method scans through *ALL* active loans per P2P of an investor and calculates the number of days of 
     * payment delay. The result is written in the investment model object.
     *  
     *  @param  $array      Array which holds global data of the P2P
     *  @return boolean
     */
    public function consolidatePaymentDelay(&$linkedAccountData) { 

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
                $result = $this->Investment->find("all", ['conditions' => $conditions,
                                                            'fields'    => ['id', 'investment_dateForPaymentDelayCalculation'],
                                                            'recursive'  => -1,
                                                            'limit' => $limit,
                                                            'offset' => $index * $limit]
                                                 );
                  
                if (count($result) < $limit) {                                  // No more results available
                    $controlIndex = 1;
                }

 echo __FUNCTION__ . " " . __LINE__ . "\n";               
                foreach ($result as $item) {                      

print_r($item);
                    if (empty($item['Investment']['investment_dateForPaymentDelayCalculation'])){           // skip over blank dates
                        continue;
                    }
                    if ($item['Investment']['investment_dateForPaymentDelayCalculation'] == "0000-00-00"){           // skip over dummy dates
                        continue;
                    }                    
                    $dateTimeForPaymentDelayCalculation = strtotime($item['Investment']['investment_dateForPaymentDelayCalculation']);
                   
                    $tempArray['id'] = $item['Investment']['id'];
                    if ($dateTimeForPaymentDelayCalculation < $todayTimeStamp) {                     
echo "Difference in seconds = " . abs($todayTimeStamp - $dateTimeForPaymentDelayCalculation) . "\n";
                        $tempArray['investment_paymentStatus'] = ceil(abs($todayTimeStamp - $dateTimeForPaymentDelayCalculation) / 86400);
                    }
                    else {
                        $tempArray['investment_paymentStatus'] = 0;
                    }
                   
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

    
    /** 
     * 
     * This method scans through *ALL* active loans per P2P of an investor and writes the field "investment_nextPaymentDate", 
     * based on the data in our amortization tables, in the investment model object.
     *  
     *  @param  $array      Array which holds the id's of Investment Model for which field "investment_nextPaymentDate
     *                      needs to be updated, based on the data available in the amortization tables
     *  @return boolean
     *
     */
    public function calculateNextPaymentDates(&$linkedAccountData) { 
        $timeStart = time();

        foreach ($linkedAccountData as $linkedAccountKey => $linkedAccount) {          
            $conditions = array("AND" => array( array('investment_statusOfLoan' => WIN_LOANSTATUS_ACTIVE), 
                                                      'investment_amortizationTableAvailable' => WIN_AMORTIZATIONTABLES_AVAILABLE,
                                                      'linkedaccount_id'  => $linkedAccountKey
                                              ));            

            $this->Investment->Behaviors->load('Containable');
            $this->Investment->contain('Investmentslice');            
            $investmentResults = $this->Investment->find("all", array('conditions' => $conditions,
                                                                    'recursive' => 1,
                                                                    'fields' => array('id')
                                                      ));
            
            foreach ($investmentResults as $result) {             
                if (isset($result['Investmentslice'][0]['id'])) {
                    $nextPaymentDate = $this->getNextPaymentDateForLoanSlice($result['Investmentslice'][0]['id']); 
                    $nextDates[] = array('id' => $result['Investment']['id'],
                                        'investment_nextPaymentDate' => $nextPaymentDate);
                }
            }     
            $this->Investment->saveMany($nextDates, array('validate' => true));            
            unset ($nextDates);
        }
  
        $timeStop = time();
        echo "\nNUMBER OF SECONDS EXECUTED IN " . __FUNCTION__ . " = " . ($timeStop - $timeStart) ."\n";
        return true;
    }      

    
    /** 
     * This method scans an amortization table and returns the NEXT payment date, based on the
     * dates of proposed payment date as stored in the amortization table¡
     *  
     *  @param  array       $investmentSliceId      id of model Investmentslice
     *  @return date
     */   
    public function getNextPaymentDateForLoanSlice($investmentSliceId) { 

        $scheduledDate = "";

        if ($this->Investmentslice->hasChildModel($investmentSliceId, "Amortizationtable")) {  
            $globalTable = $this->Amortizationtable->find("all", array('conditions' => array('investmentslice_id' => $investmentSliceId), 
                                                                      'fields' => array('id', 'amortizationtable_scheduledDate')
                                                    )); 
            $amortizationTable = Hash::extract($globalTable, '{n}.Amortizationtable.amortizationtable_scheduledDate');
        }
        else {                       
            $lists = $this->GlobalamortizationtablesInvestmentslice->find("all",  array('conditions' => array('investmentslice_id' => $investmentSliceId), 
                                                                      'fields' => array('id', 'globalamortizationtable_id')
                                                    )); 
            
            foreach ($lists as $list) {
                $filteringConditions = array('id' => $list['GlobalamortizationtablesInvestmentslice']['globalamortizationtable_id']);
                $result = $this->Globalamortizationtable->find("first", array('conditions' => $filteringConditions,
                                                                                  'fields' => array('id', 'globalamortizationtable_scheduledDate' )));  
                $globalTable[] = $result;
            }
            $amortizationTable = Hash::extract($globalTable, '{n}.Globalamortizationtable.globalamortizationtable_scheduledDate');
        }
 
        // scan through tables from "new" to "old"
        $reversedAmortizationTable = array_reverse($amortizationTable);

        foreach ($reversedAmortizationTable as $paymentSchedule) { 
            if ($this->today < $paymentSchedule) {
                $scheduledDate = $paymentSchedule;   
            }
            else {
                break;
            }
        }
        return $scheduledDate;
    }    
    
}
