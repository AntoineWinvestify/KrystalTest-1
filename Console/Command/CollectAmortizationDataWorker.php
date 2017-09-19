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
    
    var $uses = array('Amortizationtable', 'Queue');
    
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
     *      $data['PFPname']['files']                  array
     *      $data['PFPname']['files'][filename']       array of filenames, FQDN's
     *      $data['PFPname']['files'][typeOfFile']     type of file, CASHFLOW, INVESTMENT,...
     *      $data['PFPname']['files']['filetype']      CSV or XLS
     *      $data['userReference']
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
//define('AMORTIZATION_TABLE_FILE', 4);
//define('AMORTIZATION_TABLE_ARRAY',5);
//define('AMORTIZATION_TABLE_FILE',6);
        $data = json_decode($job->workload(), true);
        $collectLoanIds = array();
 
        $myCompany = companyClass($data['PFPname']);        // create instance of companyCodeClass        
        
 // use of multicurl for collecting the amortization tables, all platforms in parallel
        foreach ($data['PFPname'] as $platformkey => $platform) {
            if ($data['PFPname']['files']['typeOfFile'] == AMORTIZATION_TABLE_ARRAY) {
             //   if (not JSON then jmp over next part
            }
            else {  // CSV of XLS file
                if ($a) {
                    foreach ($data['files'] as $key => $filename) {     // read the amortization table files /perp PFP of the investor

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
        

        if (empty($collectLoanIds)) {
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
   
    */
    
    
    
   
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


}
