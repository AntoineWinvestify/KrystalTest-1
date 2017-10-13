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
     * @return json Json containing all the status collect and errors by link account id
     */
    public function getAmortizationDataMulticurl($job) {
        $data = json_decode($job->workload(),true);
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        print_r($data);
        $this->queueCurls = new \cURL\RequestsQueue;
        //If we use setQueueCurls in every class of the companies to set this queueCurls it will be the same?
        $index = 0;
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
            echo "<br>******** Executing the loop **********<br>";
            $index++;
            $this->companyId[$i] = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = " . $this->companyId[$i] . " <br>";
            $companyConditions = array('Company.id' => $this->companyId[$i]);
            $result[$i] = $this->Company->getCompanyDataList($companyConditions);
            $this->newComp[$i] = $this->companyClass($result[$i][$this->companyId[$i]]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $this->newComp[$i]->defineConfigParms($result[$i][$this->companyId[$i]]);  // Is this really needed??
            $this->newComp[$i]->setClassForQueue($this);
            $this->newComp[$i]->setQueueId($data["queue_id"]);
            $this->newComp[$i]->setBaseUrl($result[$i][$this->companyId[$i]]['company_url']);
            $this->newComp[$i]->setTypeFileAmortizationtable($result[$i][$this->companyId[$i]]['company_typeFileAmortizationtable']);
            $this->newComp[$i]->setCompanyName($result[$i][$this->companyId[$i]]['company_codeFile']);
            $this->newComp[$i]->setUserReference($data["queue_userReference"]);
            $this->newComp[$i]->setLinkAccountId($linkedaccount['Linkedaccount']['id']);
            $this->newComp[$i]->setLoanIds($data["loanIds"][$i]);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$i], DOWNLOAD_AMORTIZATION_TABLES_SEQUENCE);
            $this->newComp[$i]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $this->newComp[$i]->setUrlSequenceBackup($urlSequenceList);  // It is a backup if something fails
            $this->newComp[$i]->generateCookiesFile();
            $this->newComp[$i]->setIdForQueue($i); //Set the id of the company inside the loop
            $this->newComp[$i]->setIdForSwitch(0); //Set the id for the switch of the function company
            $this->newComp[$i]->setUser($linkedaccount['Linkedaccount']['linkedaccount_username']); //Set the user on the class
            $this->newComp[$i]->setPassword($linkedaccount['Linkedaccount']['linkedaccount_password']); //Set the pass on the class
            $configurationParameters = array('tracingActive' => true,
                'traceID' => $data["queue_userReference"],
            );
            $this->newComp[$i]->defineConfigParms($configurationParameters);
            $i++;
        }
        $companyNumber = 0;
        echo "MICROTIME_START = " . microtime() . "<br>";
        //We start at the same time the queue on every company
        foreach ($data["companies"] as $linkedaccount) {
            $this->newComp[$companyNumber]->collectAmortizationTablesParallel();
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
       return json_encode($data);
    }
    
    /**
     * This is the callback's queue for the companies cURLs, when one request is processed
     * Another enters the queue until finishes
     * @param \cURL\Event $event Object that passes the multicurl Plugin with data concerned to 
     * the url
     *          $info["companyIdForQueue"] is the company id
     *          $info["idForSwitch"] is the switch id
     *          $info["typeOfRequest"]  is the type of request (WEBPAGE, DOWNLOADFILE, LOGIN, LOGOUT)   
     */
    public function multiCurlQueue(\cURL\Event $event) {
        //We get the response of the request
        $response = $event->response;
        $error = null;
        $info = json_decode($event->request->_page, true);

        if ($info["typeOfRequest"] == "DOWNLOADFILE") {
            fclose($this->newComp[$info["companyIdForQueue"]]->getFopen());
        }

        if ($response->hasError()) {
            $this->tempArray[$info["companyIdForQueue"]]['global']['error'] = $this->errorCurl($response->getError(), $info, $response);
            $error = $response->getError();
        }
        if (empty($error) && $info["typeOfRequest"] != "LOGOUT") {
            //We get the web page string
            $str = $response->getContent();
            $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch($info["idForSwitch"]);
            $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->collectAmortizationTablesParallel($str);
        }

        if ($info["typeOfRequest"] == "LOGOUT") {
            echo "LOGOUT FINISHED <br>";
            $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
        } else if ((!empty($this->tempArray[$info["companyIdForQueue"]]) || (!empty($error)) && $info["typeOfRequest"] != "LOGOUT")) {
            if (!empty($error)) {
                $this->newComp[$info["companyIdForQueue"]]->getError(__LINE__, __FILE__, $info["typeOfRequest"], $error);
            } else {
                $this->newComp[$info["companyIdForQueue"]]->saveAmortizationTable();
            }
            $this->logoutOnCompany($info["companyIdForQueue"], $str);
            if ($info["typeOfRequest"] == "LOGOUT") {
                unset($this->tempArray['global']['error']);
            }
        }
    }

}
