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

App::import('Shell','GearmanWorker');

/**
 * Worker of Gearman to collect all the files of a investor
 */
class CollectDataWorkerShell extends GearmanWorkerShell {
    
    var $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    /**
     * Function main that init when start the shell class
     */
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlFiles', array($this, 'getDataMulticurlFiles'));
        $this->GearmanWorker->addFunction('casperFiles', array($this, 'getDataCasperFiles'));
        echo  __CLASS__ . ": " . "Starting to listen to data from its Client\n";
        while( $this->GearmanWorker->work() );
    }
    
    /**
     * Function to initiate the process to save the files of a company
     * @param object $job It is the object of Gearmanjob that contains
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data["companies"]                  array It contains all the linkedaccount information
     *      $data["queue_userReference"]        string It is the user reference
     *      $data["queue_id"]                   integer It is the queue id
     *      $data["date"]                       integer It is the today's date
     * @return json Json containing all the status collect and errors by link account id
     */
    public function getDataMulticurlFiles($job) {
        $data = json_decode($job->workload(), true);
        $this->job = $job;
        $this->queueCurlFunction = "collectUserGlobalFilesParallel";
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        $this->Structure = ClassRegistry::init('Structure');
        $this->Investment = ClassRegistry::init('Investment');
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if data arrive correctly\n");
            print_r($data);
        }
        
        if (empty($data['originExecution'])) {
            $data['originExecution'] = null;
        }
        
        $queueCurlFunction = $this->queueCurlFunction;
        $this->queueCurls = new \cURL\RequestsQueue;
        //If we use setQueueCurls in every class of the companies to set this queueCurls it will be the same?
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
            $this->initCompanyClass($data, $i, $linkedaccount, WIN_DOWNLOAD_PFP_FILE_SEQUENCE);
            
            $structure[] = $this->Structure->getStructure($linkedaccount['Linkedaccount']['company_id'], WIN_STRUCTURE_SINGLE_INVESTMENT_PAGE);
            $structure[] = $this->Structure->getStructure($linkedaccount['Linkedaccount']['company_id'], WIN_STRUCTURE_INVESTMENTS_FILE_HTML);
            $investmentList = $this->Investment->getData(array('linkedaccount_id' => $linkedaccount['Linkedaccount']['id']), array('investment_loanId'));
            $this->newComp[$i]->setTableStructure($structure);
            $this->newComp[$i]->setInvestmentList($investmentList);
            $this->newComp[$i]->$queueCurlFunction();

            $i++;
        }

        $this->queueCurls->addListener('complete', array($this, 'multiCurlQueue'));

        //This is the queue. It is working until there are requests
        while ($this->queueCurls->socketPerform()) {
            echo '*';
            $this->queueCurls->socketSelect();
        }

  //      $this->out(__FUNCTION__ . " " . __LINE__ . ": MICROTIME_FINISHED = " . microtime());
        
        $lengthTempArray = count($this->tempArray);        
        $statusCollect = [];
        $errors = null;
        for ($i = 0; $i < $lengthTempArray; $i++) {
            if (empty($this->tempArray[$i]['global']['error'])) {
                $statusCollect[$this->newComp[$i]->getLinkAccountId()] = "1";
            } else {
                $statusCollect[$this->newComp[$i]->getLinkAccountId()] = "0";
                $errors[$this->newComp[$i]->getLinkAccountId()] = $this->tempArray[$i]['global']['error'];
            }
        }

        $data['statusCollect'] = $statusCollect;
        $data['errors'] = $errors;
        if (Configure::read('debug')) {
  //          $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Sending back information of worker 1");
            print_r($data);
        }
        return json_encode($data);
    }
    
    /**
     * Function to initiate the process to save the files of a company
     * @param object $job It is the object of Gearmanjob that contains
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data["companies"]                  array It contains all the linkedaccount information
     *      $data["queue_userReference"]        string It is the user reference
     *      $data["queue_id"]                   integer It is the queue id
     * @return string The variable must be in string because of Gearman but it is really a boolean 1 or 0
     */
    public function getDataCasperFiles($job) {
        $data = json_decode($job->workload(),true);
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        $this->job = $job;
        print_r($data);
        $index = 0;
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
            unset($newComp);
            $index++;
            echo "<br>******** Executing the loop **********<br>";
            $companyId = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = $companyId <br>";
            $companyConditions = array('Company.id' => $companyId);
            $result = $this->Company->getCompanyDataList($companyConditions);
            $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $newComp->defineConfigParms($result[$companyId]);  // Is this really needed??
            $newComp->setQueueId($data["queue_id"]);
            $newComp->setBaseUrl($result[$companyId]['company_url']);
            $newComp->setFileType($result[$companyId]['company_typeFileTransaction'], $result[$companyId]['company_typeFileInvestment']);
            $newComp->setCompanyName($result[$companyId]['company_codeFile']);
            $newComp->setUserReference($data["queue_userReference"]);
            $newComp->setLinkAccountId($linkedaccount['Linkedaccount']['id']);           
            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_MY_INVESTMENTS_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $configurationParameters = array('tracingActive' => false,
                'traceID' => $data["queue_userReference"],
            );
            $newComp->defineConfigParms($configurationParameters);
            echo "MICROTIME_START = " . microtime() . "<br>";
            $tempArray = $newComp->collectUserGlobalFilesCasper($linkedaccount['Linkedaccount']['linkedaccount_username'], $linkedaccount['Linkedaccount']['linkedaccount_password']);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGOUT_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $newComp->companyUserLogout();

            echo "MICROTIME_STOP = " . microtime() . "<br>";
            $tempArray['companyData'] = $result[$companyId];
        }

    }  

}
