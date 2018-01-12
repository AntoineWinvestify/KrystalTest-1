<?php

/**
 * AppShell file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Shell', 'Console');
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
    
    public function startup() {
        Configure::load('p2pGestor.php', 'default');
    }

    /**
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


     /**
     * checks to see if jobs are waiting in the queue for processing
     *
     * @param int $presentStatus    status of job to be located
     * @param int $limit            Maximum number of jobs to be pulled out of the queue
     * @return array                List of pending jobs
     *
     */
    public function checkJobs ($presentStatus, $limit) {

        if (empty($this->Queue) ) {
            $this->Queue = ClassRegistry::init('Queue');
            echo __FUNCTION__ . " " . "Queue instance created\n";
        }

        $userAccess = 0;
        $jobList = $this->Queue->getUsersByStatus(FIFO, $presentStatus, $userAccess, $limit);
        return $jobList;
    }



    /**
     * Read the names in directory $dir of the files (FDQN) that fulfill the $typeOfFiles bitmap
     *
     * @param string $dir           Directory in which to search
     * @param int $typeOfFiles      bitmap of constants of Type Of File:
     *                              INVESTMENT_FILE, TRANSACTION_TABLE_FILE, CONTROL_FILE, ....
     * @return array    $approveFileNameList    list of FQDN filenames
     */
    public function readDirFiles($dir, $typeOfFiles)  {

        $fileNameList = array();
        $handle = opendir($dir);

        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $fileNameList[] = $dir . "/" . $entry;
                }
            }
            closedir($handle);
        }

        $approvedFileNameList = $this->readFilteredFiles($fileNameList, $typeOfFiles);
        return $approvedFileNameList;
    }


    
    /**
     * Checks if an element with value $element exists in a two dimensional array
     * @param type $element
     * @param type $array
     *
     * @return array with data
     *          or false of $element does not exist in two dimensional array
     */
    public function in_multiarray($element, $array) {
        while (current($array) !== false) {
            if (current($array) == $element) {
                return true;
            } elseif (is_array(current($array))) {
                if ($this->in_multiarray($element, current($array))) {
                    return(current($array));
                }
            }
            next($array);
        }
        return false;
    }    
    
    

    /**
     * Read the names in a list of files (FDQN) that fulfill the $typeOfFiles bitmap
     *
     * @param array $fileNameList   list of filenames to be analyzed
     * @param int $typeOfFiles      bitmap of constants of Type Of File:
     *                              INVESTMENT_FILE, TRANSACTION_TABLE_FILE, CONTROL_FILE, ....
     * @return array  $approveFileNameList    list of FQDN filenames
     */
    function readFilteredFiles($fileNameList,  $typeOfFiles) {
        $approvedFileNameList = array();

        $knownFileTypesNames = array (
            WIN_FLOW_TRANSACTION_FILE => "transaction",
            WIN_FLOW_EXTENDED_TRANSACTION_FILE => "extendentransaction",
            WIN_FLOW_INVESTMENT_FILE => "investment",
//            WIN_FLOW_TRANSACTIONTABLE_FILE =>
            WIN_FLOW_AMORTIZATION_TABLE_FILE => "amortizationTable",
//            WIN_FLOW_AMORTIZATION_TABLE_ARRAY =>
            WIN_FLOW_AMORTIZATION_TABLE_FILE => "amortizationTableList",
            WIN_FLOW_CONTROL_FILE => "controlVariables",
            WIN_FLOW_EXPIRED_LOAN_FILE => "expiredLoan"
            );

 
        
        $requiredFileType = array();
        foreach ($knownFileTypesNames as $keyKnownFileTypeName => $knownFileTypeName) {
            $temp = $keyKnownFileTypeName & $typeOfFiles;
            if (($keyKnownFileTypeName & $typeOfFiles) == $keyKnownFileTypeName) {
                $requiredFileTypes[] = $knownFileTypeName;
            }
        }
 // end temp

        foreach ($fileNameList as $file) {
            foreach ($requiredFileTypes as $fileType) {
                $pos = strpos($file, $fileType);
                if ($pos !== false) {
                    $approvedFileNameList[] = $file;
                    continue;
                }
            }
        }
        return($approvedFileNameList);
    }
    
    private function tryErrorOnGearman() {
        //fake code
    }
    
    /**
     * Function to get the extension of a file
     * @param string $filePath FQDN of the file to analyze
     * @return string It is the extension of the file
     */
    public function getExtensionFile($file) {
        $file = new File($file);
        $extension = $file->ext();
        return $extension;
    }
    
    /** CAN BE DELETED, IT IS NOT USED AND 
     * Function to get the loanId from the file name of one amortization table
     * @param string $filePath It is the path to the file
     * @return string It is the loanId
     */
    public function getLoanIdFromFile($filePath) {
        $file = new File($filePath, false);
        $name = $file->name();
        $nameSplit = explode("_", $name);
        $loanId = $nameSplit[1];
        echo "loanId = $loanId\n";
        print_r($nameSplit);
        exit;
        return $loanId;
    }
        
    /** 
     * Function to get the loanId from the file name of an amortization table
     * @param   string  $filePath   It is the full path to the file
     * @return  array               [0] contains investmentslice_id
     *                              [1] contains the loanId
     */
    public function getIdInformationFromFile($filePath) {
        $file = new File($filePath, false);
        $name = $file->name();
        $nameSplit = explode("_", $name);
        return array_slice($nameSplit, 1, 2);
    } 
    
    
    /**
     * Get the list of all active investments for a P2P as identified by the
     * linkedaccount identifier.
     * Take into account that an investment can have 1 or more investmentslices.
     *
     * @param int $linkedaccount_id    linkedaccount reference
     * @return array
     *
     */
    public function getListActiveInvestments($linkedaccount_id) {
        $this->Investment = ClassRegistry::init('Investment');
        $filterConditions = array(
            'linkedaccount_id' => $linkedaccount_id,
            "investment_statusOfloan" => WIN_LOANSTATUS_ACTIVE,
        );

        $investmentListResult = $this->Investment->find("all", array("recursive" => -1,
            "conditions" => $filterConditions,
            "fields" => array("id", "investment_loanId"),
        ));

        $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanId');
        return $list;
    }    
    
    
    
    
    
}
