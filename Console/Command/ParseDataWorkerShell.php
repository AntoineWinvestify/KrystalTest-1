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
 * Errors are taken care of in the worker and will spark the exception Callback with
 * some extra user data
 * Normal return also include some basic return data, like queue_id and user_reference
 * @author 
 * @version
 * @date
 * @package
 */

class ParseDataWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    
    var $uses = array('Investment', 'Userinvestmentdata', 'Queue');
    
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
        
        $this->GearmanWorker->addFunction('parseFileFlow', array($this, 'parseFileFlow'));   
        
        while($this->GearmanWorker->work());
        
    }
            
  
    
    
    
    /**
     * Parse the content of a file (xls, xlsx, csv) into an array 
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data['PFPname']['files']                  array 
     *      $data['PFPname']['files'][filename']       array of filenames, FQDN's
     *      $data['PFPname']['files'][typeOfFile']     type of file, CASHFLOW, INVESTMEN [one or more can be present,...
     *      $data['PFPname']['files']['filetype']      CSV, XLS or PDF
     *      $data['userReference']
     *      $data['queue_id']
     *      
     * 
     * 
     * @return array queue_id, userreference, también en el exception error
     * el worker genera tb los applicationerrors
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                      array with all errorData related to occurred error
     * load config (perhaps via constructor: index = loanId
     */   
    public function parseFileFlow($job) {
        $data = json_decode($job->workload(), true);
        $collectLoanIds = array();
        foreach ($data['files'] as $key => $filename) {             // check all the platforms of the investor
            if ($data['files'['filetype']] == "CSV") {
                    $config = array('seperatorChar' => ";",         // Only makes sense in case input file is a CSV.
                            'sortByLoanId'  => true,                // Make the loanId the main index and all XLS/CSV entries of
                                                                    // same loan are stored under the same index.
                            'offset'    => 3,                       // Number of "lines" to be discarded at beginning of file
                
                            );
                    //parse cvs
            }
            else {
                //parse XLS
            }
            
            $myParser = new FileParser($config);           

            $myCompany = companyClass($data['PFPname']);        // create instance of companyCodeClass
            
            if (!empty($parsedFile = $myParser->analyse(data['filename'], $configFile)) ) {    // if successfull analysis, result is an array 
                                                                                                        // with loanId's as indice 
               
                if ($myCompany->fileanalyzed($fileName, $typeOfFile, $fileContent)) {                   // Generate the callback function
                    // continue 
                }
                else {
                    // an error has occurred or been detected by companycode file. Exit gracefully
                    return false;
                }
                
                //run through the array and search for new loans.
                $loans = array_keys($parsedFileStructure);
                $this->Investment = ClassRegistry::init('Investment');
                
                // execute callback
                $result = $mycompany->beforeamortizationlist($parsedFile);
                
                foreach ($loans as $key => $loan) {
                    if ($key == "global") {
                        continue;
                    }
                    
                    $this->Investment->create();
                    // check if loanId already exists for the current user
                    $conditions = array('investment_loanReference' => $loan); // for the user
                    
                    $resultInvestment = $this->Investment->find("all", $params = array('recursive' =>  -1,
							                              'conditions' => $conditions,
									));
                    if (empty($resultInvestment)) {                                                     // loanId does not yet exist for current user
                        $collectLoanIds[$data['PFPname']] = $loan;                                      // A Gearman worker has to collect the amortization Tables
                    } 

                               
                }
                if ($mycompany->amortizationtablesdownloaded($XXXXXXXX) ) {
                    
                }
                else {
                    // store error
                }
     
            }
            else {                                                          // Error encountered
                $myParser->analysisErrors();
                $this->Applicationerror = ClassRegistry::init('Applicationerror');
                $par1 = "ERROR Parsing error of downloaded file";
                $par2 = $data;
                $par3 = __LINE__;
                $par4 = __FILE__;
                $par5 = "";
                $this->applicationerror->saveAppError($par1, $par2, $par3, $par4, $par5);
                // do cleaning up of all files which have been generated so far
//echo error back to Gearman client
            }
        }
        unset($myParser); 
        
        
        if (empty($collectLoanIds)) {
            $newState = AMORTIZATION_TABLES_DOWNLOADED;                        // Do not collect amortization tables
        }
        else {
            $newState = DATA_EXTRACTED;
        }

        // write new status
        $resultQueue = $this->Queue->find('all', array('conditions' => array('queue_userReference' => $data['queue_id']),
                'recursive' => -1,
            ));
        $this->Queue->id = $resultQueue[0]['id'];

        if ($this->Queue->save(array('queue_status' => $newState), $validate = true)) {
            $params = array('investor_investorReference' => $data['userReference'],
                            'queue_id'  => $data['queue_id']
                            );
            return json_encode($params);                                        // normal end of execution of worker         
        }
        
        // generate exception
        // return
        
        
        
        
        
    }    
    
    
    
    
    
    
   
    /** COPIED FROM APP.CONTROLLER
     *
     * 	Creates a new instance of class with name company, like zank, or comunitae....
     *
     * 	@param 		int 	$companyCodeFile		Name of "company"
     * 	@return 	object 	instance of class "company"
     *
     */
    function companyClass($companyCodeFile) {

        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
        return $newComp;
    }    
    
    
    
    
    
}



