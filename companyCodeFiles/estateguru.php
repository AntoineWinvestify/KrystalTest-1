<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 *
 * 
 * 
 * 2017-08-28
 * Created
 * link account
 */
class estateguru extends p2pCompany {

    protected $transactionConfigParms = ['offsetStart' => 1,
        'offsetEnd' => 0,
        'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    ];
    protected $investmentConfigParms = ['offsetStart' => 1,
        'offsetEnd' => 0,
        'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    ];
    protected $transactionHeader = [
        'A' => 'UniqueId',
        'B' => 'Payment Date',
        'C' => 'Confirmation Date',
        'D' => 'Cash Flow Type',
        'E' => 'Cash Flow Status',
        'F' => 'Project Name',
        'G' => 'Currency',
        'H' => 'Amount',
        'I' => 'Available to invest'
    ];

    /*    NOT YET READY
      protected $investmentConfigParms = array ('offsetStart' => 1,
      'offsetEnd'     => 0,
      'separatorChar' => ";",
      'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
      );

     */

    function __construct() {
        parent::__construct();
        $this->typeFileTransaction = "xls";
        $this->typeFileInvestment = "html";
        $this->typeFileExpiredLoan = "html";
        $this->transactionErrorRevision = false;
// Do whatever is needed for this subsclass
    }

    function companyUserLogin($user = "", $password = "", $options = []) {
        /*
          FIELDS USED BY estateguru DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        $credentials['j_username'] = $user;
        $credentials['j_password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login



        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            //echo $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Logout') {
                $confirm = true;
                break;
            }
        }


        return $confirm;
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserGlobalFilesParallel($str) {
        switch ($this->idForSwitch) {
            case 0:

                $credentials['j_username'] = $this->user;
                $credentials['j_password'] = $this->password;
                $this->userId = null;
                $this->tempUrl['referer'] = null;
                
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 1:
                echo 'Doing loging' . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 2:
                if (!$this->login) {
                    $dom = new DOMDocument;  //Check if works
                    $dom->loadHTML($str);
                    $dom->preserveWhiteSpace = false;
                    //echo $str;

                    $confirm = false;

                    $as = $dom->getElementsByTagName('a');
                    foreach ($as as $a) {
                        //echo $a->nodeValue . HTML_ENDOFLINE;
                        if (trim($a->nodeValue) == 'Logout') {
                            $confirm = true;
                            break;
                        }
                    }

                    if ($confirm) {
                        echo 'login ok';
                        $this->login = true;
                        $this->idForSwitch++;
                    }
                }

                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                if (empty($this->tempUrl['referer'])) {
                    $this->tempUrl['downloadUrl'] = array_shift($this->urlSequence);
                    $this->tempUrl['referer'] = array_shift($this->urlSequence);
                    $this->tempUrl['headers'] = json_decode(array_shift($this->urlSequence));
                }

                $button = $this->getElements($dom, 'a', 'class', 'btn-u');

                $credentialsString = $button[5]->getAttribute('href');
                $credentialsStringArray = explode("=", $credentialsString);

                $this->userId = explode("&", $credentialsStringArray[3])[0];

                $credentials['format'] = explode("&", $credentialsStringArray[1])[0];
                $credentials['extension'] = explode("&", $credentialsStringArray[2])[0];
                $credentials['userId'] = $this->userId;
                $credentials['userCurrency'] = explode("&", $credentialsStringArray[4])[0];
                $credentials['accountBalance'] = $credentialsStringArray[5];
                print_r($credentials);
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "_" . $this->numPartFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transactionHeader;
                echo '\n download: ' . $this->tempUrl['downloadUrl'] . $downloadUrl . ' \n';
                echo 'referer is' . $this->tempUrl['referer'] . '  \n';
                print_r($this->tempUrl['headers']);

                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->tempUrl['downloadUrl'] . $downloadUrl, $this->tempUrl['referer'], $credentials, $this->tempUrl['headers'], $this->fileName);
                break;

            case 4:
                if(!$this->transactionErrorRevision){
                    if (!$this->verifyFileIsCorrect()) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                    }
                    if (mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "application/vnd.ms-office") {  //Compare mine type for mintos files
                        echo 'mine type incorrect: ';
                        echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                    }
                    $headerError = $this->compareHeader();
                    if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    }
                    else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    }
                    $this->transactionErrorRevision = true;
                }

                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                if (empty($this->credentialsInvestments)) {
                    $this->credentialsInvestments = [
                        "filterProjectValue" => 1, //1->Funded loans(contain late) 2->Repaid loans 3->Late Loans(only late) 4->Deaulted loans
                        "userId" => $this->userId,
                    ];
                }
                if (empty($this->tempUrl['investmentFilter'])) {
                    $this->tempUrl['investmentFilter'] = array_shift($this->urlSequence);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentFilter'], $this->credentialsInvestments);
                break;
            case 5:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $thead = $dom->getElementsByTagName('thead');
                $header = new DOMDocument();
                $clone = $thead[0]->cloneNode(TRUE); //Clone the table
                $header->appendChild($header->importNode($clone, TRUE));
                $headerString = $header->saveHTML();


                $tbody = $dom->getElementsByTagName('tbody');
                $table = new DOMDocument();
                $clone = $tbody[0]->cloneNode(TRUE); //Clone the table
                $table->appendChild($table->importNode($clone, TRUE));
                $tableString = $table->saveHTML();

                //Compare structure

                $revision = $this->structureRevisionInvestmentTable($AmortizationTableString, $this->tableStructure[1]);
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->numFileInvestment++;
                if ($revision) {
                    echo "Comparation ok";
                    $this->credentialsInvestments['filterProjectValue'] ++;
                    $this->saveFilePFP($this->fileName, $headerString . $tableString);
                }
                else {
                    echo 'Comparation Not ok';
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                if ($this->credentialsInvestments['filterProjectValue'] > 4) {
                    $this->idForSwitch++;
                }
                else {
                    $this->idForSwitch--;
                }
                if (empty($this->tempUrl['accountDetail'])) {
                    $this->tempUrl['accountDetail'] = array_shift($this->urlSequence);
                }
                $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentFilter'], $this->credentialsInvestments);
                break;
            case 6:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 7:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $h3s = $dom->getElementsByTagName('h3');
                /*foreach($h3s as $key => $h3){
                    echo $key . " => " . $h3->nodeValue;
                }*/
                
                $this->tempArray["global"]["myWallet"] = $h3s[4]->nodeValue;
                $this->tempArray['global']['outstandingPrincipal'] = $h3s[2]->nodeValue;
                return $this->tempArray;
        }
    }

    
    function structureRevisionInvestmentTable($node1, $node2){
        
        $dom1 = new DOMDocument();
        $dom1->loadHTML($node1);
        
        $dom2 = new DOMDocument();
        $dom2->loadHTML($node2);
        
        $dom1 = $this->cleanDomTag($dom1, [
            ['typeSearch' => 'tagElement', 'tag' => 'tbody'],
        ]);
         
        $dom2 = $this->cleanDomTag($dom2, [
            ['typeSearch' => 'tagElement', 'tag' => 'tbody'],
        ]);
        
        echo 'compare structure';
        $structureRevision = $this->verifyDomStructure($dom1, $dom2);
        return $structureRevision;
        
        
    }
    
    
    
    /**
     *
     * 	Logout of user from the company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout() {

        $str = $this->doCompanyLogout();
        return true;
    }

}
