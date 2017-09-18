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
 * 
 * Each user has queueId, investorID
 * number of child processes, each childprocess takes a full sequence of a user
 * and writes the new job status for the user
 * Start instance of parser with configfile
 * 
 * constructor (configfile) cashflow
 * load xls file
 * start analysing
 * store resulting array as json file
 * 
 * 
 *  * constructor (configfile) investments
 * load xls file
 * start analysing
 * store resulting array as json file
 * 
 * startParsing
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 *
 * @author 
 * @version
 * @date
 * @package
 */

class ParseDataWorkerShell extends AppShell {
    
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
        
        $this->GearmanWorker->addFunction('parseFile', array($this, 'parseFile'));   
        
        while($this->GearmanWorker->work());
    }
            
  
    
    
    
    /**
     * Parse the content of a file (xls, xlsx, csv) into an array 
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      data['filenames'[]]       array of filenames, FQDN's
     *      data['userReference']
     * 
     * 
     * 
     * @return array  
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                  readErrorData 
     *                      array with all errorData related to occurred error
     * load config (perhaps via constructor: index = loanId
     */   
    public function parseFile($job) {
        $data = json_decode($job->workload(), true);
        $collectLoanIds = array();

        foreach ($data[filenames] as $key => $filename) {
            $config = array('seperatorChar' => ";",         // Only makes sense in case input file is a CSV.
                            'sortByLoanId'  => true,        // Make the loanId the main index and all XLS/CSV entries of
                                                            // same loan are stored under the same index.
                            'offset'    => 3,               // Number of "lines" to be discarded at beginning of file
                
                            );
            
            $myParser = new FileParser($config);
            
            if (!empty($parsedFileStructure = $myParser->analyse(data['filename'], $configFile)) ) {    // if successfull analysis, result is an array 
                                                                                                        // with loanId's as indice 
                //run through the array and search for new loans.
                $loans = array_keys($parsedFileStructure);
                $this->Investment = ClassRegistry::init('Investment');
                
                foreach ($loans as $loan) {
                    $this->Investment->create();
                    // check if loanId already exists for the current user
                    $conditions = array('investment_loanReference' => $loan); // for the user
                    
                    $resultInvestment = $this->Investment->find("all", $params = array('recursive' =>  -1,
							                              'conditions' => $conditions,
									));
                    if (empty($resultInvestment)) {                         // loanId does not yet exist for current user
                        $collectLoanIds[] = $loan;                          // A Gearman worker has to collect the amortization Tables
                    } 
                }
            }
            else {                                                          // Error encountered
                $myParser->analysisErrors();
            }
        }
        unset($myParser);  
    }    
    
    
    
    
    
    
   
    
    
    
    
    
    
}



