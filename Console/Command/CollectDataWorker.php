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
    
    public function getDataMulticurlFiles($job) {
        $data = json_decode($job->workload(),true);
        
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
            $this->newComp[$i]->setMarketPlaces($this);
            $this->newComp[$i]->setQueueId($data["queue_id"]);
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
            $this->queueCurls->addListener('complete', function (\cURL\Event $event) {
               echo "<br>";
               // The $info["companyIdForQueue"] is the company id
               // The ids[1] is the switch id
               // The ids[2] is the type of request (WEBPAGE, LOGIN, LOGOUT)
               $info = json_decode($event->request->_page);
               //We get the response of the request
               $response = $event->response;
               //We get the web page string
               $str = $response->getContent();
               $error = "";
               //if (!empty($this->testConfig['active']) == true) {
               echo 'CompanyId:' . $this->companyId[$info["companyIdForQueue"]] .
               '   HTTPCODE:' . $response->getInfo(CURLINFO_HTTP_CODE)
               . '<br>';

               if ($response->hasError()) {
                   $this->errorCurl($response->getError(), $info, $response);
                   $error = $response->getError();
               } else {
                   echo "<br>";
                   //}
                   //if ($this->config['tracingActive'] == true) {
                   // $this->doTracing($this->config['traceID'], "WEBPAGE", $str);
                   //}
                   if ($info["typeOfRequest"] != "LOGOUT") {
                       $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch($info["idForSwitch"]);
                       $this->tempArray[$info["companyIdForQueue"]] = $this->newComp[$info["companyIdForQueue"]]->collectUserInvestmentDataParallel($str);
                   }
               }

               if ($response->hasError() && $error->getCode() == CURL_ERROR_TIMEOUT && $this->newComp[$info["companyIdForQueue"]]->getTries() == 0) {
                   $this->logoutOnCompany($info, $str);
                   $this->newComp[$info["companyIdForQueue"]]->setIdForSwitch(0); //Set the id for the switch of the function company
                   $this->newComp[$info["companyIdForQueue"]]->setUrlSequence($this->newComp[$info]->getUrlSequenceBackup());  // provide all URLs for this sequence
                   $this->newComp[$info["companyIdForQueue"]]->setTries(1);
                   $this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
                   //$this->newComp[$info["companyIdForQueue"]]->generateCookiesFile();
                   $this->newComp[$info["companyIdForQueue"]]->collectUserInvestmentDataParallel();
               } else if ($info["typeOfRequest"] == "LOGOUT") {
                   echo "LOGOUT FINISHED <br>";
                   //$this->newComp[$info["companyIdForQueue"]]->deleteCookiesFile();
               } else if ((!empty($this->tempArray[$info["companyIdForQueue"]]) || ($response->hasError()) && $info["typeOfRequest"] != "LOGOUT")) {
                   if ($response->hasError()) {
                       //$this->tempArray[$info["companyIdForQueue"]]['global']['error'] = "An error has ocurred with the data" . __FILE__ . " " . __LINE__;
                       $this->newComp[$info["companyIdForQueue"]]->getError(__LINE__, __FILE__, $info["typeOfRequest"], $error);
                   }
                   $this->logoutOnCompany($info, $str);
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
        }
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
    
}