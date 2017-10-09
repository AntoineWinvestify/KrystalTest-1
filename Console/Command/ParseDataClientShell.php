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
 *
 *
 * 2017-08-11		version 0.1
 * Basic version
 *
 *
 */


App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class ParseDataClientShell extends AppShell {
    protected $GearmanClient;
    private $newComp = [];
    public $uses = array('Queue');
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
                        echo "pfp = " . $pfp . "\n";
                        $files = $this->readFilteredFiles($allFiles,  TRANSACTION_FILE + INVESTMENT_FILE);
                        $listOfActiveLoans = $this->getListActiveLoans($linkedAccountId);
                        $params[$linkedAccountId] = array('queue_id' => $job['Queue']['id'],
                                                        'pfp' => $pfp,
                                                        'listOfCurrentActiveLoans' => $listOfActiveLoans,
                                                        'userReference' => $job['Queue']['queue_userReference'],
                                                        'files' => $files);
                    }
                    print_r($params);
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                }
                echo __METHOD__ . " " . __LINE__ . " \n";
                $this->GearmanClient->runTasks();




                $result = json_decode($this->workerResult, true);
                foreach ($result as $platformKey => $platformResult) {
                    echo "\nplatformkey = $platformKey\n";

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
                    $queueId = $platformResult['queue_id'];
                    $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;
                    $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS;

                    $mapResult = $this->mapData($platformResult);

                    if (!empty($platformResult['newLoans'])) {
                        $fileHandle = new File($baseDirectory .'loanIds.json', true, 0644);
                        if ($fileHandle) {
                            if ($fileHandle->append(json_encode($platformResult['newLoans']), true)) {
                                $fileHandle->close();
                                echo "File " .  $baseDirectory . "loanIds.json written\n";
                            }
                        }
                        $newState = DATA_EXTRACTED;
print_r($platformResult['newLoans']);
                    }
                    else {
                        $newState = AMORTIZATION_TABLES_DOWNLOADED;
                    }

                    $this->Queue->id = $queueId;
                    $this->Queue->save(array('queue_status' => $newState,
                                             'queue_info' => json_encode($platformResult['newLoans']),
                                            ), $validate = true
                                        );

                }
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
 break;   // ?????
        }
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

// CHECK THE FILTERCONDITION for status
        $filterConditions = array(
            //'linkedaccount_id' => $linkedaccount_id,
                                    "investment_status" => -1,
                                );

	$investmentListResult = $this->Investment->find("all", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                                        "fields" => array("id", "investment_loanReference"),
									));
        $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanReference');
        $list[] = "20729-01";       // ONLY FOR TESTING PURPOSES, TO BE DELETED.
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




    /**
     * Starts the process of analyzing the file and returns the results as an array
     *  @param  $array          Array with all data received from Worker
     *  @param  $referenceFile  Name of the file that contains configuration data of a specific "document"
     *  @param  $offset_top     The number of lines (=rows) from the TOP OF THE FILE which are not to be included in parser
     *  @param  $offset_bottom  The number of lines (=rows) from, counted from the BOTTOM OF THE FILE which are not to be included in parser
     *  @return boolean true
     *                  false
     *
     *  @return array   array with all the data to be written to the database
     * the data is available in two or three subarrays which are to be written (before checking if it is a duplicate) to the corresponding
     * database table.g
     *     platform - (1-n)loanId - (1-n) concepts
     */
    public function mapData(&$data) {
        $dbInvestmentTable = array('loanId' => "",
                                    'country' => "",
                                    'loanType'  => "",
                                    'amortizationMethod' => "",


                            );

        $dbUserInvestmentData = array (

                            );

        $dbAmortizationTable = array(

                             );




        foreach ($result as $platformKey => $platformResult) {
            foreach ($platformResult['parsingResult'] as $loanIdKey => $tempPlatformResult) { // tempPlatformResult holds the real array
                                                                                              // and loanIdkey a number between 0 ...4..
             //   if regular_interest_income then map to item




            }





        }



    }







}
