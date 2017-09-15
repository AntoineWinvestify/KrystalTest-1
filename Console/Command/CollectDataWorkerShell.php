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
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');

class CollectDataWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    
    var $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    public $queueCurls;
    public $newComp = array();
    public $tempArray = array();
    public $companyId = array();

    
    public function startup() {
            $this->GearmanWorker = new GearmanWorker();
    }
    
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlFiles', array($this, 'getDataMulticurlFiles'));
        $this->GearmanWorker->addFunction('casperFiles', array($this, 'getDataCasperFiles'));
        $this->GearmanWorker->addFunction('testFail', function(GearmanJob $job) {

            try {
                throw new Exception('Boom');
            } catch (Exception $e) {
                $job->sendException($e->getMessage());
                $job->sendFail();

            }
        });
        while( $this->GearmanWorker->work() );
    }
    
    /*$data["companies"] = $linkedaccountsByType;
    $data["queue_userReference"] = $resultQueue[$key]['Queue']['queue_userReference'];
    $data["queue_id"] = $resultQueue[$key]['Queue']['id'];*/
    
    /**
     * Function to initiate the process to save the files of a company
     * @param object $job It is the object of Gearmanjob that contains
     * companies It is the id of the company;
     * queue_userReference It is the userReference
     * queue_id It is the id of the queue
     */
    public function getDataMulticurlFiles($job) {
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
        exit();
    }
    
    public function getDataCasperFiles($job) {
        $data = json_decode($job->workload(),true);
        
        $index = 0;
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
            unset($newComp);
            $index = $index + 1;
            echo "<br>******** Executing the loop **********<br>";
            $companyId = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = $companyId <br>";
            $companyConditions = array('Company.id' => $companyId);

            $result = $this->Company->getCompanyDataList($companyConditions);

            $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $newComp->defineConfigParms($result[$companyId]);  // Is this really needed??

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MY_INVESTMENTS_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence

            $configurationParameters = array('tracingActive' => false,
                'traceID' => $data["queue_userReference"],
            );

            $newComp->defineConfigParms($configurationParameters);


            echo "MICROTIME_START = " . microtime() . "<br>";
            $tempArray = $newComp->collectUserInvestmentData($linkedaccount['Linkedaccount']['linkedaccount_username'], $linkedaccount['Linkedaccount']['linkedaccount_password']);

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $newComp->companyUserLogout();

            echo "MICROTIME_STOP = " . microtime() . "<br>";
            $tempArray['companyData'] = $result[$companyId];

            $userInvestments = $tempArray;
//prepare all globals on total dashboard level	
//			$dashboardGlobals['amountInvested']	= $dashboardGlobals['amountInvested'] + $userInvestments['global']['activeInInvestments'];
            $dashboardGlobals['amountInvested'] = $dashboardGlobals['amountInvested'] + $userInvestments['global']['totalInvestment'];
            $dashboardGlobals['wallet'] = $dashboardGlobals['wallet'] + $userInvestments['global']['myWallet'];
            $dashboardGlobals['totalEarnedInterest'] = $dashboardGlobals['totalEarnedInterest'] + $userInvestments['global']['totalEarnedInterest'];
            $dashboardGlobals['profitibilityAccumulative'] = $dashboardGlobals['profitibilityAccumulative'] + $userInvestments['global']['profitibility'];

// Amount that was invested totally in all the currently active investments
            $dashboardGlobals['totalInvestments'] = $dashboardGlobals['totalInvestments'] + $userInvestments['global']['totalInvestments'];

// The number of active investments in all companies:
            $dashboardGlobals['activeInvestments'] = $dashboardGlobals['activeInvestments'] + count($userInvestments['investments']);

            $dashboardGlobals['investments'][$result[$companyId]['company_name']] = $userInvestments;
            unset($newComp);

// *********************************************************************************************************		
// Save "intermediate photos", so investor will always see something. The result is that for a user who has
// investments in 4 platforms, the system will generate 4 photos, with each photo including the previous one
// *********************************************************************************************************
            $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / $index);
            if ($this->Data->save(array('data_investorReference' => $resultQueue['Queue']['queue_userReference'],
                        'data_JSONdata' => JSON_encode($dashboardGlobals),
                        $validate = true))) {
                // DO NOTHING
                echo "WRITE AN INTERMEDIATE PHOTO OF INVESTMENTS OF USER <br>";
            } else {
                // log error
            }

            foreach ($dashboardGlobals['investments'] as $company => $value) {
                $inversiones = count($dashboardGlobals['investments'][$company]['investments']);
                echo '<h1>';
                print_r($inversiones);
                echo '</h1>';
                for ($key = 0; $key < $inversiones; $key++) {
                    echo "comprobando" . $key . "</br>";
                    if ($dashboardGlobals['investments'][$company]['investments'][$key]['status'] == -1) {
                        echo '<h1>' . $key . "eliminada</h1></br>";
                        unset($dashboardGlobals['investments'][$company]['investments'][$key]);
                        $dashboardGlobals['investments'][$company]['global']['investments'] --;
                        $dashboardGlobals['activeInvestments'] --;
                        continue;
                    }
                }
                $dashboardGlobals['investments'][$company]['investments'] = array_values($dashboardGlobals['investments'][$company]['investments']);
            }
            echo "<h1>            aqui";
            $this->print_r2($dashboardGlobals);
            echo "</h1>";

            echo "<br>******* End of Loop ****** <br>";
        }

        $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / $index);
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
        $this->print_r2($dashboardGlobals);

// Store the dashboard data for 
        $this->Data = ClassRegistry::init('Data');
        if ($this->Data->save(array('data_investorReference' => $data["queue_userReference"],
                    'data_JSONdata' => JSON_encode($dashboardGlobals),
                    $validate = true))) {
            
        } else {
            // log error
        } 

    }
    
    public function getDataMulticurlScraping() {
        
    }
    
    public function getDataCasperScraping() {
        
    }
    
    /**
     * Function to do logout of company
     * @param int $companyIdForQueue It is the companyId inside the array of newComp
     * @param string $str It is the webpage on string format
     */
    function logoutOnCompany($companyIdForQueue, $str) {
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
    function errorCurl($error, $info, $response) {
        echo
        'Error code: ' . $error->getCode() . "<br>" .
        'Message: "' . $error->getMessage() . '" <br>';
        echo 'CompanyId:' . $this->companyId[$info["companyIdForQueue"]] . '<br>';
        $testConfig = $this->newComp[$info["companyIdForQueue"]]->getTestConfig();
        if (!empty($testConfig['active']) == true) {
            print_r($response->getInfo());
            echo "<br>";
        }
        $config = $this->newComp[$info["companyIdForQueue"]]->getConfig();
        if ($config['tracingActive'] == true) {
            $this->newComp[$info["companyIdForQueue"]]->doTracing($config['traceID'], $info["typeOfRequest"], $str);
        }
    }


    
}