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

class ParseAmortizationDataWorkerShell extends GearmanWorkerShell {
   
    
    var $uses = array('Amortizationtable', 'Queue');
    
    /**
     * Function main that init when start the shell class
     */
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('collectamortizationtablesFileFlow', array($this, 'collectamortizationtablesFileFlow'));   
        echo __FUNCTION__ . " " . __LINE__ . ": " . "ParseAmortizationDataworker starting to listen to data from its Client\n";
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
     * 
     * @return array  
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                  readErrorData 
     *                      array with all errorData related to occurred error
     */   
    public function collectamortizationtablesFileFlow($job) {
 $timeStart = time();
        $filename = $job->workload();
        $data = file_get_contents($filename);
        $platformData = json_decode($data, true);
        $this->job = $job;
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if data arrive correctly\n");
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
                $i++;
            }
            $this->callbackInit($tempArray[$linkedAccountKey], $companyHandle, $callbacks);
            if (empty($tempArray[$linkedAccountKey])) {
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
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) . "\n"; 
        return json_encode($dataQueue);
    }
    
    
    /*public function collectamortizationtablesFileFlow($job) {

        $data = json_decode($job->workload(), true);
        $myCompany = companyClass($data['PFPname']);        // create instance of companyCodeClass        

        foreach ($data['PFPname'] as $platformkey => $platform) {
            if ($data['PFPname']['files']['typeOfFile'] == AMORTIZATION_TABLE_ARRAY) {
                //   if (not JSON then jmp over next part
            } else {  // CSV of XLS file
                if ($a) {
                    foreach ($data['files'] as $key => $filename) {     // read the amortization table files /perp PFP of the investor
                        if ($data['files'['filetype']] == "CSV") {
                            $config = array('seperatorChar' => ";", // Only makes sense in case input file is a CSV.
                                'sortByLoanId' => true, // Make the loanId the main index and all XLS/CSV entries of
                                // same loan are stored under the same index.
                                'offset' => 3, // Number of "lines" to be discarded at beginning of file
                            );
                            //parse cvs
                        } else {
                            //parse XLS
                        }
                        // this is n
                    }
                    unset($myParser);
                }
            }

            // *ALL* new amortization tables have been downloaded
        $result = $mycompany->amortizationtablesdownloaded($parsedFiles);

       // go throuh all tables to normalize the data for at least the fields: duration, status and rating
        foreach ($parsesTables as $platformKey => $platform) {
            foreach($platform as $loanKey => $loanid) {
                if (!empty($normalizedStatus = $mycompany->normalizeLoanStatus($data['PFPname']))) {
                    $parsedTables[$platformKey]['status'] = $normalizedStatus;
                }

                if (!empty($normalizedRating = $mycompany->normalizeLoanRating($data['PFPname']))) {
                    $parsedTables[$platformKey]['rating'] = $normalizedRating;
                }

                if (!empty($normalizedDuration = $mycompany->normalizeLoanDuration($data['PFPname']))) {
                    $parsedTables[$platformKey]['duration_value'] = $normalizedStatus[0];
                    $parsedTables[$platformKey]['duration_unit'] = $normalizedStatus[1];
                }
            }
        }
        
        // prepare to save the tables to db. link to corresponding loan, without overwriting already stored data
        
//        tempArray['zank']['schedule'][]
//        tempArray['growly']['schedule'][]        
/*        etc.
                
        loop [
            load existing data 
            store the *complete* table for the loan and Investor:
 *          store totals at bottom of amortization table
        ]    
           
  */        
        

       /* if (empty($collectLoanIds)) {
            $jobState = AMORTIZATION_TABLES_DOWNLOADED;                        // Do not collect amortization tables
        }
        // write new jobstatus
        
        }
        /*
    
                 $myParser->analysisErrors();
                $this->Applicationerror = ClassRegistry::init('Applicationerror');
                $par1 = "ERROR Parsing error of downloaded file";
                $par2 = $data;
                $par3 = __LINE__;
                $par4 = __FILE__;
                $par5 = "";
                $this->applicationerror->saveAppError($par1, $par2, $par3, $par4, $par5);

    
    
    }*/

}
