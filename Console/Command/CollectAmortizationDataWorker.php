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

class CollectAmortizatioDataWorkerShell extends AppShell {
    
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
        
        $this->GearmanWorker->addFunction('collectamortizationtablesFileFlow', array($this, 'collectamortizationtablesFileFlow'));   
              
        while($this->GearmanWorker->work());
    }
            
  
    
    
    
    /**
     * Parse the content of a file (xls, xlsx, csv) into an array 
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      data['PFPname'['files']                  array of filenames, FQDN's
     *      data['PFPname'['files'][filename']       array of filenames, FQDN's
     *      data['PFPname'['files'][typeOfFile']     type of file, CASHFLOW, INVESTMENT,...
     *      data['PFPname'['files']['filetype']      CSV or XLS
     *      data['userReference']
     *      data['PFPname']
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
    public function collectamortizationtablesFileFlow($job) {

        $data = json_decode($job->workload(), true);
        $collectLoanIds = array();
 
        
        
 // use of multicurl for collecting the amortization tables, all platforms in parallel
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
//        tempArray['zank']['schedule'][]
//        tempArray['growly']['schedule'][]        
/*        etc.
                
        
        
        
        
        
        
        
        
        
        
        
        all tables ready
 */       
        amortizationtablesdownloaded(array $amortizationTables) {amortizationtablesdownloaded(array $amortizationTables) {
            
    }
        foreach ($data['files'] as $key => $filename) {             // check all the platform of the investor
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
                $mycompany->beforeamortizationlist($parsedFile);
                
                
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
//echo error back to Gearman client
            }
        }
        unset($myParser); 
        
        
        if (empty($collectLoanIds)) {
            $state = AMORTIZATION_TABLES_DOWNLOADED;                        // Do not collect amortization tables
        }
        else {
            $state = DATA_EXTRACTED;
        }
        // write new jobstatus
        
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



