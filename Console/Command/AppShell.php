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

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {

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
     * Read the names in a list of files (FDQN) that fulfill the $typeOfFiles bitmap
     * 
     * @param array $fileNameList   list of filesnames to be analyzed
     * @param int $typeOfFiles      bitmap of constants of Type Of File:
     *                              INVESTMENT_FILE, TRANSACTION_TABLE_FILE, CONTROL_FILE, ....
     * @return array  $approveFileNameList    list of FQDN filenames
     */
    function readFilteredFiles($fileNameList,  $typeOfFiles) {
        $approvedFileNameList = array();       
// start temp
        $knownFileTypesNames = array (
            TRANSACTION_FILE => "transaction",
            INVESTMENT_FILE => "investment",
//            TRANSACTIONTABLE_FILE =>  
            AMORTIZATION_TABLE_FILE => "amortizationTable",
//            AMORTIZATION_TABLE_ARRAY => 
            AMORTIZATION_TABLE_FILE => "amortizationTableList",
            CONTROL_FILE => "controlVariables"
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
        
        
}
