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
 * @date 2018-06-18
 * @package
 */

App::import('Shell','GearmanWorker');

class ParseAmortizationDataWorkerShell extends GearmanWorkerShell {
   
    /**
     * Function main that init when start the shell class
     */
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('parseAm', array($this, 'parseAmortizationtablesFileFlow'));   
        echo __CLASS__ . ": " . "ParseAmortizationDataworker starting to listen to data from its Client\n";
        while($this->GearmanWorker->work());
    }
    
    /**
     * Parse the content of a file (xls, xlsx, csv, html) into an array 
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data['linkedAccountId']['userReference']
     *      $data['linkedAccountId']['queue_id']
     *      $data['linkedAccountId']['pfp']
     *      $data['linkedAccountId']['files'][filename1']           => array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']
     * 
     * @return array  
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                  readErrorData 
     *                      array with all errorData related to occurred error
     */   
    public function parseAmortizationtablesFileFlow($job) {
        $timeStart = time();  
        $platformParametersFile = $job->workload(); 

        $parametersContent = file_get_contents($platformParametersFile);
        $platformData = json_decode($parametersContent, true);       
      
        $this->job = $job;
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if data has arrived correctly\n");
            print_r($platformData);
        }

        $i = 0;
        $tempArray = array();
        foreach ($platformData as $linkedAccountKey => $data) {        
            $companyHandle = $this->companyClass($data['pfp']);
            $callbacks = $companyHandle->getCallbackAmortizationTable();
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Current platform = " . $data['pfp'] . "\n";
            }

            $files = $data['files'];
            $extensionFile = null;
         
            foreach ($files as $file) {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Amortization table File\n";
                } 
                $parserConfig = $companyHandle->getParserConfigAmortizationTableFile();
                $IdInformation = $this->getIdInformationFromFile($file);
                $loanId = $IdInformation[0] . "_" . $IdInformation[1];
                if (empty($extensionFile)) {
                    $extensionFile = $this->getExtensionFile($file);
                }
                $configParameters = $companyHandle->getParserAmortizationConfigParms();
                $this->myParser->setConfig($configParameters[0]);
                $tempArray[$linkedAccountKey][$loanId] = $this->myParser->analyzeFile($file, $parserConfig, $extensionFile);
                echo "tempResult " . $loanId . "\n";
   //             $tempArray[$linkedAccountKey]['pfp'] = $data['pfp'];            // save name of p2p, as required in Client
                $i++;
            }
            $this->callbackInit($tempArray[$linkedAccountKey], $companyHandle, $callbacks);
            if (empty($tempArray[$linkedAccountKey]) && !empty($files)) {
                $dataQueue['statusCollect'][$linkedAccountKey] = "0";
                $errors[$linkedAccountKey] = $this->tempArray[$i]['global']['error'];
            }
            else {
                $dataQueue['statusCollect'][$linkedAccountKey] = "1";
            }     
            
        }

        $dataQueue['tempArray'] = $tempArray;
        $dataQueue['errors'] = $errors;

        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Sending back information of ParseAmortizationDataWorker");
            print_r($dataQueue);
        }
        
        print_r(array_keys($tempArray[0]));
        $timeStop = time();
        if (Configure::read('debug')) {
            echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) . "\n";
        }
        return json_encode($dataQueue);
    }
  
}
