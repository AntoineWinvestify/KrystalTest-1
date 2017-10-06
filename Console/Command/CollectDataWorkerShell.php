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

class CollectDataWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    
    var $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    public $queueCurls;
    public $newComp = array();
    public $tempArray = array();
    public $companyId = array();

    
    public function startup() {
        $this->GearmanWorker = new GearmanWorker();
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'));
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
    }
    
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlFiles', array($this, 'getDataMulticurlFiles'));
        $this->GearmanWorker->addFunction('casperFiles', array($this, 'getDataCasperFiles'));
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
     * @return string The variable must be in string because of Gearman but it is really a boolean 1 or 0
     */
    public function getDataMulticurlFiles($job) {
        $data = json_decode($job->workload(), true);
        $this->job = $job;
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
            $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$i], DOWNLOAD_PFP_FILE_SEQUENCE);
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
            $this->newComp[$companyNumber]->collectUserGlobalFilesParallel();
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
               $this->tempArray[$info["companyIdForQueue"]]['global']['error']  = $this->errorCurl($response->getError(), $info, $response);
               $error = $response->getError();
            }
            if (empty($error) && $info["typeOfRequest"] != "LOGOUT") {
                 //We get the web page string
                $str = $response->getContent();
                $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch($info["idForSwitch"]);
                $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->collectUserGlobalFilesParallel($str);
            }

           if (!empty($error) && $error->getCode() == CURL_ERROR_TIMEOUT && $this->newComp[$info["companyIdForQueue"]]->getTries() == 0) {
               $this->logoutOnCompany($info["companyIdForQueue"], $str);
               $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch(0); //Set the id for the switch of the function company
               $this->newComp[$info["companyIdForQueue"]]->setUrlSequence($this->newComp[$info]->getUrlSequenceBackup());  // provide all URLs for this sequence
               $this->newComp[$info["companyIdForQueue"]]->setTries(1);
               $this->newComp[$info["companyIdForQueue"]]->deletePFPFiles();
               $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
               $this->newComp[$info["companyIdForQueue"]]->generateCookiesFile();
               $this->newComp[$info["companyIdForQueue"]]->collectUserGlobalFilesParallel();
           } 
           else if ($info["typeOfRequest"] == "LOGOUT") {
               echo "LOGOUT FINISHED <br>";
               $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
           } 
           else if ((!empty($this->tempArray[$info["companyIdForQueue"]]) || (!empty($error)) && $info["typeOfRequest"] != "LOGOUT")) {
               if (!empty($error)) {
                   $this->newComp[$info["companyIdForQueue"]]->getError(__LINE__, __FILE__, $info["typeOfRequest"], $error);
               }
               else {
                   $this->newComp[$info["companyIdForQueue"]]->saveFilePFP("controlVariables.json", json_encode($this->tempArray[$info["companyIdForQueue"]]));
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
               break;
           }
       }
       
       return $statusCollect;
    }
    
    /**
     * 
     * @param type $job
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
            
            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MY_INVESTMENTS_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $configurationParameters = array('tracingActive' => false,
                'traceID' => $data["queue_userReference"],
            );
            $newComp->defineConfigParms($configurationParameters);
            echo "MICROTIME_START = " . microtime() . "<br>";
            $tempArray = $newComp->collectUserGlobalFilesCasper($linkedaccount['Linkedaccount']['linkedaccount_username'], $linkedaccount['Linkedaccount']['linkedaccount_password']);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $newComp->companyUserLogout();

            echo "MICROTIME_STOP = " . microtime() . "<br>";
            $tempArray['companyData'] = $result[$companyId];
        }

    }
    
    /*public function getDataMulticurlScraping() {
        $data = json_decode($job->workload(),true);
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
            $this->newComp[$i]->setFileType($result[$i][$this->companyId[$i]]['company_typeOfFile']);
            $this->newComp[$i]->setCompanyName($result[$i][$this->companyId[$i]]['company_codeFile']);
            $this->newComp[$i]->setUserReference($data["queue_userReference"]);
            $this->newComp[$i]->setLinkAccountId($linkedaccount['Linkedaccount']['id']);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$i], MY_INVESTMENTS_SEQUENCE);
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
            $this->newComp[$companyNumber]->collectUserInvestmentDataParallel();
            $companyNumber++;
        }

        /*
        * This is the callback's queue for the companies cURLs, when one request is processed
        * Another enters the queue until finishes
        */
    /*
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
               $this->errorCurl($response->getError(), $info, $response);
               $error = $response->getError();
            }
           if (empty($error) && $info["typeOfRequest"] != "LOGOUT") {
                //We get the web page string
                $str = $response->getContent();
                $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch($info["idForSwitch"]);
                $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->collectUserGlobalFilesParallel($str);
           }

           if (!empty($error) && $error->getCode() == CURL_ERROR_TIMEOUT && $this->newComp[$info["companyIdForQueue"]]->getTries() == 0) {
               $this->logoutOnCompany($info["companyIdForQueue"], $str);
               $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch(0); //Set the id for the switch of the function company
               $this->newComp[$info["companyIdForQueue"]]->setUrlSequence($this->newComp[$info]->getUrlSequenceBackup());  // provide all URLs for this sequence
               $this->newComp[$info["companyIdForQueue"]]->setTries(1);
               //$this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
               //$this->newComp[$info["companyIdForQueue"]]->generateCookiesFile();
               $this->newComp[$info["companyIdForQueue"]]->collectUserInvestmentDataParallel();
           } 
           else if ($info["typeOfRequest"] == "LOGOUT") {
               echo "LOGOUT FINISHED <br>";
               //$this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
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
       
       return "ok";
    }*/
    
    public function getDataCasperScraping() {
        
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
        $errorCurl = 
        'Error code: ' . $error->getCode() . '\n' .
        'Message: "' . $error->getMessage() . '" \n' .
        'CompanyId:' . $this->companyId[$info["companyIdForQueue"]] . '\n';
        echo $errorCurl;
        $testConfig = $this->newComp[$info["companyIdForQueue"]]->getTestConfig();
        if (!empty($testConfig['active']) == true) {
            print_r($response->getInfo());
            echo "<br>";
        }
        
        $config = $this->newComp[$info["companyIdForQueue"]]->getConfig();
        if ($config['tracingActive'] == true) {
            $this->newComp[$info["companyIdForQueue"]]->doTracing($config['traceID'], $info["typeOfRequest"], $str);
        }
        return $errorCurl;
    }
    
    public function exception_handler($code) {
        echo "\n exception code : " . $code . "\n";
        $this->job->sendException('Boom');
    }
   
    public function error_handler($code) {
        if ($code != E_WARNING && $code != E_NOTICE) {
            echo "\n error code : " . $code . "\n";
            $this->job->sendFail();
        }
    }
   
    public function fatalErrorShutdownHandler() {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            //echo "\n fatal error code : " . E_ERROR . "\n";
            $this->error_handler(E_ERROR);
        }
    }


    
}
