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
 * Class CollectAmortizationDataWorker to collect all the amortization tables
 *
 */
class CollectAmortizationDataWorkerShell extends GearmanWorkerShell {
    
    public $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    /**
     * Function main that init when start the shell class
     */
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlAmortization', array($this, 'getAmortizationDataMulticurl'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting to listen to data from its Client\n"; 
        while( $this->GearmanWorker->work() );
    }
    
    /**
     * Function to initiate the process to save the amortization tables of a company
     * @param object $job It is the object of Gearmanjob that contains
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data["companies"]                  array It contains all the linkedaccount information
     *      $data["queue_userReference"]        string It is the user reference
     *      $data["queue_id"]                   integer It is the queue id
     *      $data["loandIds"]                   array It contains all the loandId needed to save from the companies
     *      $data["date"]                       integer It is the today's date
     * @return json Json containing all the status collect and errors by link account id
     */
    public function getAmortizationDataMulticurl($job) {
        $data = json_decode($job->workload(),true);
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        $this->queueCurlFunction = "collectAmortizationTablesParallel";
        print_r($data);
        $queueCurlFunction = $this->queueCurlFunction;
        $this->queueCurls = new \cURL\RequestsQueue;
        //If we use setQueueCurls in every class of the companies to set this queueCurls it will be the same?
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
            $this->initCompanyClass($data, $i, $linkedaccount, WIN_DOWNLOAD_AMORTIZATION_TABLES_SEQUENCE);
            $this->newComp[$i]->setLoanIds($data["loanIds"][$i]);
            $i++;
        }
        $companyNumber = 0;
        echo "MICROTIME_START = " . microtime() . "<br>";
        //We start at the same time the queue on every company
        foreach ($data["companies"] as $linkedaccount) {
            $this->newComp[$companyNumber]->$queueCurlFunction();
            $companyNumber++;
        }
        
        $this->queueCurls->addListener('complete', array($this, 'multiCurlQueue'));

       //This is the queue. It is working until there are requests
       while ($this->queueCurls->socketPerform()) {
           echo '*';
           $this->queueCurls->socketSelect();
       }
       
       $lengthTempArray = count($this->tempArray);
       $statusCollect = [];
       $errors = null;
       for ($i = 0; $i < $lengthTempArray; $i++) {
           if (empty($this->tempArray[$i]['global']['error'])) {
               $statusCollect[$this->newComp[$i]->getLinkAccountId()] = "1";
           }
           else {
               $statusCollect[$this->newComp[$i]->getLinkAccountId()] = "0";
               $errors[$this->newComp[$i]->getLinkAccountId()] = $this->tempArray[$i]['global']['error'];
           }
       }
       
       $data['statusCollect'] = $statusCollect;
       $data['errors'] = $errors;
       print_r($data);
       return json_encode($data);
    }

}
