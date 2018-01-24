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
 * @date 2017-10-06
 * @package
 */

/**
 * Description of GearmanWorkerShell
 */
class GearmanWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    protected $queueCurls;
    protected $newComp = array();
    protected $tempArray = array();
    protected $companyId = array();
    protected $queueCurlFunction;
    protected $myParser;  
    
    /**
     * Constructor of the class
     */
    public function startup() {
        $this->GearmanWorker = new GearmanWorker();
        $pathVendor = Configure::read('winvestifyVendor');
        include_once ($pathVendor . 'Classes' . DS . 'fileparser.php');
        $this->myParser = new Fileparser();  
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'));
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
    }
    
    /**
     * This is the callback's queue for the companies cURLs, when one request is processed
     * Another enters the queue until finishes
     * @param cURL\Event $event Object that passes the multicurl Plugin with data concerned to 
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
        $queueCurlFunction = $this->queueCurlFunction;
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
            $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->$queueCurlFunction($str);
        }

        if ($info["typeOfRequest"] == "LOGOUT") {
            echo "LOGOUT FINISHED <br>";
            $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
        } else if ((!empty($this->tempArray[$info["companyIdForQueue"]]) || (!empty($error)) && $info["typeOfRequest"] != "LOGOUT")) {
            if (!empty($error)) {
                $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_CURL,$info["typeOfRequest"], $error);
            }
            else if ($this->queueCurlFunction == "collectUserGlobalFilesParallel") {
                $this->newComp[$info["companyIdForQueue"]]->saveFilePFP("controlVariables.json", json_encode($this->tempArray[$info["companyIdForQueue"]]));
            }
            else if ($this->queueCurlFunction == "collectAmortizationTablesParallel") {
                $this->newComp[$info["companyIdForQueue"]]->saveAmortizationTable();
                $this->newComp[$info["companyIdForQueue"]]->verifyErrorAmortizationTable();
            }
            $this->logoutOnCompany($info["companyIdForQueue"], $str);
            if ($info["typeOfRequest"] == "LOGOUT") {
                unset($this->tempArray[$info["companyIdForQueue"]]['global']['error']);
            }
        }
    }
    
    /**
     * Function to do logout of company
     * @param int $companyIdForQueue It is the companyId inside the array of newComp
     * @param string $str It is the webpage on string format
     */
    public function logoutOnCompany($companyIdForQueue, $str) {
        $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$companyIdForQueue], WIN_LOGOUT_SEQUENCE);
        //echo "Company = $this->companyId[$info["companyIdForQueue"]]";
        $this->newComp[$companyIdForQueue]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $this->newComp[$companyIdForQueue]->companyUserLogoutMultiCurl($str);
    }
    
    /**
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
    
    /**
     * Function to handle an exception
     * @param integer $code It is the code of the exception
     */
    public function exception_handler($code) {
        echo "\n exception code : " . $code . "\n";
        $this->job->sendException('Boom');
    }
   
    /**
     * Function to handle an error
     * @param integer $code It is the code of the error
     */
    public function error_handler($code) {
        if ($code != E_WARNING && $code != E_NOTICE) {
            echo "\n error code : " . $code . "\n";
            $this->job->sendFail();
        }
    }
   
    /**
     * Function to handle a fatal error
     */
    public function fatalErrorShutdownHandler() {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            //echo "\n fatal error code : " . E_ERROR . "\n";
            $this->error_handler(E_ERROR);
        }
    }
    
    public function initCompanyClass($data, $i, $linkedaccount, $typeUrlSequence) {
        echo "<br>******** Executing the loop **********<br>";
        $this->companyId[$i] = $linkedaccount['Linkedaccount']['company_id'];
        echo "companyId = " . $this->companyId[$i] . " <br>";
        $companyConditions = array('Company.id' => $this->companyId[$i]);
        $result[$i] = $this->Company->getCompanyDataList($companyConditions);
        $this->newComp[$i] = $this->companyClass($result[$i][$this->companyId[$i]]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
        $this->newComp[$i]->defineConfigParms($result[$i][$this->companyId[$i]]);  // Is this really needed??
        $this->newComp[$i]->setClassForQueue($this);
        $this->newComp[$i]->setQueueId($data["queue_id"]);
        $this->newComp[$i]->setBaseUrl($result[$i][$this->companyId[$i]]['company_url']);
        $this->newComp[$i]->setCompanyName($result[$i][$this->companyId[$i]]['company_codeFile']);
        $this->newComp[$i]->setUserReference($data["queue_userReference"]);
        $this->newComp[$i]->setLinkAccountId($linkedaccount['Linkedaccount']['id']);
        $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$i], $typeUrlSequence);
        $this->newComp[$i]->setDateInit($linkedaccount['Linkedaccount']['linkedaccount_lastAccessed']);
        $this->newComp[$i]->setDateFinish($data["date"]);
        $this->newComp[$i]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $this->newComp[$i]->setUrlSequenceBackup($urlSequenceList);  // It is a backup if something fails
        $this->newComp[$i]->setOriginExecution($data['originExecution']);
        $this->newComp[$i]->generateCookiesFile();
        $this->newComp[$i]->setIdForQueue($i); //Set the id of the company inside the loop
        $this->newComp[$i]->setIdForSwitch(0); //Set the id for the switch of the function company
        $this->newComp[$i]->setUser($linkedaccount['Linkedaccount']['linkedaccount_username']); //Set the user on the class
        $this->newComp[$i]->setPassword($linkedaccount['Linkedaccount']['linkedaccount_password']); //Set the pass on the class
        $configurationParameters = array('tracingActive' => true,
            'traceID' => $data["queue_userReference"],
        );
        $this->newComp[$i]->defineConfigParms($configurationParameters);
    }
}
