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

/**
 * Description of CollectAmortizationDataWorker
 *
 */
class CollectAmortizationDataWorkerShell extends AppShell {
   
    
    protected $GearmanWorker;
    
    public $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    public $queueCurls;
    public $newComp = array();
    public $tempArray = array();
    public $companyId = array();

    
    public function startup() {
            $this->GearmanWorker = new GearmanWorker();
    }
    
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlAmortization', array($this, 'getDataMulticurlFiles'));
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
     *      $data["loandIds"]                   array It contains all the loandId needed to save on the file
     * @return string The variable must be in string because of Gearman but it is really a boolean 1 or 0
     */
    public function getDataMulticurlFiles($job) {
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
            $this->newComp[$i]->setFileType($result[$i][$this->companyId[$i]]['company_typeFileTransaction'], $result[$i][$this->companyId[$i]]['company_typeFileInvestment']);
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

        /*
        * This is the callback's queue for the companies cURLs, when one request is processed
        * Another enters the queue until finishes
        */
        $this->queueCurls->addListener('complete', function (\cURL\Event $event) {
            
            //We get the response of the request
            $response = $event->response;
            $error = null;
            // $info["companyIdForQueue"] is the company id
            // $info["idForSwitch"] is the switch id
            // $info["typeOfRequest"]  is the type of request (WEBPAGE, DOWNLOADFILE, LOGIN, LOGOUT)
            $info = json_decode($event->request->_page, true);
            
            if ($info["typeOfRequest"] == "DOWNLOADFILE") {
                fclose($this->newComp[$info["companyIdForQueue"]]->getFopen());
            }
            
            if ($response->hasError()) {
               $this->tempArray['global']['error']  = $this->errorCurl($response->getError(), $info, $response);
               $error = $response->getError();
            }
            if (empty($error) && $info["typeOfRequest"] != "LOGOUT") {
                 //We get the web page string
                $str = $response->getContent();
                $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch($info["idForSwitch"]);
                $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->collectAmortizationTablesParallel($str);
            }

           if (!empty($error) && $error->getCode() == CURL_ERROR_TIMEOUT && $this->newComp[$info["companyIdForQueue"]]->getTries() == 0) {
               $this->logoutOnCompany($info["companyIdForQueue"], $str);
               $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch(0); //Set the id for the switch of the function company
               $this->newComp[$info["companyIdForQueue"]]->setUrlSequence($this->newComp[$info]->getUrlSequenceBackup());  // provide all URLs for this sequence
               $this->newComp[$info["companyIdForQueue"]]->setTries(1);
               $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
               $this->newComp[$info["companyIdForQueue"]]->generateCookiesFile();
               $this->newComp[$info["companyIdForQueue"]]->collectAmortizationTablesParallel();
           } 
           else if ($info["typeOfRequest"] == "LOGOUT") {
               echo "LOGOUT FINISHED <br>";
               $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
           } 
           else if ((!empty($this->tempArray[$info["companyIdForQueue"]]) || (!empty($error)) && $info["typeOfRequest"] != "LOGOUT")) {
               if (!empty($error)) {
                   $this->newComp[$info["companyIdForQueue"]]->getError(__LINE__, __FILE__, $info["typeOfRequest"], $error);
               }
               
               $this->logoutOnCompany($info["companyIdForQueue"], $str);
               if ($info["typeOfRequest"] == "LOGOUT") {
                   unset($this->tempArray['global']['error']);
               }
           }
       });

       //This is the queue. It is working until there are requests
       while ($this->queueCurls->socketPerform()) {
           echo '*';
           $this->queueCurls->socketSelect();
       }
       
       $lengthTempArray = count($this->tempArray);
       $statusCollect = "1";
       for ($i = 0; $i < $lengthTempArray; $i++) {
           if (!empty($this->tempArray[$i]['global']['error'])) {
               $statusCollect = "0";
           }
       }
       
       return $statusCollect;
    }
    
    /**
     * Function to do logout of company
     * @param int $companyIdForQueue It is the companyId inside the array of newComp
     * @param string $str It is the webpage on string format
     */
    public function logoutOnCompany($companyIdForQueue, $str) {
        $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$companyIdForQueue], LOGOUT_SEQUENCE);
        //echo "Company = $this->companyId[$info["companyIdForQueue"]]";
        $this->newComp[$companyIdForQueue]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $this->newComp[$companyIdForQueue]->companyUserLogoutMultiCurl($str);
    }
    
    /*
     * 
     * Get the variable queueCurls
     */
    public function getQueueCurls() {
        return $this->queueCurls;
    }
    
    /**
     * 
     * Add a request to the queue to initiate the multi_curl
     * @param request $request It's the request to process
     */
    public function addRequestToQueueCurls($request) {
        $this->queueCurls->attach($request);
    }
    
    /**
     * Function to process if there is an error with the request on parallel
     * @param object $error It is the curl error
     * @param array $info They are the info of the company
     * @param object $response It is the curl response from the request on parallel
     */
    public function errorCurl($error, $info, $response) {
        $errorVar = 
        'Error code: ' . $error->getCode() . '\n' .
        'Message: "' . $error->getMessage() . '" \n' .
        'CompanyId:' . $this->companyId[$info["companyIdForQueue"]] . '\n';
        echo $errorVar;
        $testConfig = $this->newComp[$info["companyIdForQueue"]]->getTestConfig();
        if (!empty($testConfig['active']) == true) {
            print_r($response->getInfo());
            echo "<br>";
        }
        
        $config = $this->newComp[$info["companyIdForQueue"]]->getConfig();
        if ($config['tracingActive'] == true) {
            $this->newComp[$info["companyIdForQueue"]]->doTracing($config['traceID'], $info["typeOfRequest"], $str);
        }
        return $errorVar;
    }
    
    
}
