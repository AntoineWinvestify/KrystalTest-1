<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.3
 * @date 2017-01-28
 * @package
 *
 *
 * Contains the code required for accessing the website of "Comunitae"
 *
 *
 *
 *
 * 2016-10-05	  version 2016_0.1
 * Basic version
 * function calculateLoanCost()											[OK not tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()												[OK not tested]
 * function collectUserInvestmentData()									[OK not tested]
 *
 * 2017-08-04
 *  collectCompanyMarketplaceData - Read completed investment
 *  collectHistorical - Added
 *
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 * PENDING:
 *
 *
 */
class ecrowdinvest extends p2pCompany {

// ECROWD
// Fecha        Nombre del proyecto                                                     Cuota	Amortización de capital(€)	Intereses brutos(€) Retención IRPF(€)  Total(€)
// 25-07-2017	Ampliación de la red de fibra óptica de l'Ametlla de Mar - Fase 5 -	2	0,00                              1,09               0,21                0,88
    protected $values_ecrowd = [// All types/names will be defined as associative index in array
        "A" => [
            [
                "type" => "date", // Winvestify standardized name 
                "inputData" => [
                    "input2" => "D-M-Y", // Input parameters. The first parameter
                // is ALWAYS the contents of the cell
                ],
                "functionName" => "normalizeDate",
            ],
            [
                "type" => "date", // Complex format, calling external method
                "inputData" => [
                    "input2" => "#previous.date", // The calculated field "date" from the *previous* excel row (i.e. previous aray index) is loaded
                    // Note that "date" must be a field defined in this config file
                    // keywords are "#previous" and "#current.
                    // Be aware that #previous does NOT contain any data in case of parsing the
                    // first line of the file.
                    // #current.indexName is ONLY defined if this field is defined BEFORE this field in the
                    // configuration file
                    "input3" => false               // This parameter indicates if the defined field will be overwritten 
                // if it already contains a value.     
                // 
                ],
                "functionName" => "getRowData",
            ],
            [
                "type" => "date1", // Complex format, example of duplicating an existing value
                "inputData" => [
                    "input2" => "#current.date", // The calculated field "date" from the *previous* excel row (i.e. previous aray index) is loaded
                    // Note that "date" must be a field defined in this config file
                    // keywords are "#previous" and "#current".
                    // Be aware that #previous does NOT contain any data in case of parsing the
                    // first line of the file.
                    // #current.indexName is ONLY defined if this field is defined BEFORE this field in the
                    // configuration file
                    "input3" => true                // This parameter indicates if the defined field will be overwritten 
                // if it already contains a value.      
                // 
                ],
                "functionName" => "getRowData",
            ],
        ],
        "B" => [
            [
                "type" => "purpose", // trick to get the complete cell data as purpose
                "inputData" => [
                    "input2" => "", // May contain trailing spaces
                    "input3" => ",",
                ],
                "functionName" => "extractDataFromString",
            ],
            [
                "type" => "loanId", // Winvestify standardized name 
                "functionName" => "getHash", // An internal loanId is generated based on md5 hash of project name
            ]
        ],
        "C" => [
            "name" => "payment",
        ],
        /*              [
          "type" => "transactionType",                // Complex format, calling external method
          "inputData" => [                            // List of all concepts that the platform can generate
          // format ["concept string platform", "concept string Winvestify"]
          "input2" => [["Amortización de capital(€)", "Principal_repayment"],
          ["Intereses brutos(€)", "Regular_interest_income"],
          ["Retención IRPF(€)", "Tax_income_withholding_tax"],
          ]
          ],
          "functionName" => "getTransactionType",
          ],
          [
          "type" => "transactionDetail",              // Complex format, calling external method
          "inputData" => [                            // List of all concepts that the platform can generate
          // format ["concept string platform", "concept string Winvestify"]
          "input2" => [["Amortización de capital(€)", "Principal_repayment"],
          ["Intereses brutos(€)", "Regular_interest_income"],
          ["Retención IRPF(€)", "Tax_income_withholding_tax"],
          ]
          ],
          "functionName" => "getTransactionDetail",
          ]
          ],
         */
        "D" => [// Simply changing name of column to the Winvestify standardized name
            [
                "type" => "amortization",
                "inputData" => [
                    "input2" => ".", // Thousands seperator, typically "."
                    "input3" => ",", // Decimal seperator, typically ","
                    "input4" => 5, // Number of required decimals, typically 5
                // is ALWAYS the contents of the cell
                ],
                "functionName" => "getAmount"
            ]
        ],
        "E" => [// Simply changing name of column to the Winvestify standardized name
            [
                "type" => "interest",
                "inputData" => [
                    "input2" => ".", // Thousands seperator, typically "."
                    "input3" => ",", // Decimal seperator, typically ","
                    "input4" => 5, // Number of required decimals, typically 5
                // is ALWAYS the contents of the cell
                ],
                "functionName" => "getAmount"
            ]
        ],
        "F" => [// Simply changing name of column to the Winvestify standardized name
            [
                "type" => "retencionTax",
                "inputData" => [
                    "input2" => ".", // Thousands seperator, typically "."
                    "input3" => ",", // Decimal seperator, typically ","
                    "input4" => 5, // Number of required decimals, typically 5
                // is ALWAYS the contents of the cell
                ],
                "functionName" => "getAmount"
            ]
        ],
        "G" => [// Simply changing name of column to the Winvestify standardized name
            [
                "type" => "total",
                "inputData" => [
                    "input2" => ".", // Thousands seperator, typically "."
                    "input3" => ",", // Decimal seperator, typically ","
                    "input4" => 5, // Number of required decimals, typically 5
                // is ALWAYS the contents of the cell
                ],
                "functionName" => "getAmount"
            ]
        ]
    ];

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
     public function getParserConfigTransactionFile() {
        return $this->$valuesEcrowdInvestTransaction;
    }
 
    public function getParserConfigInvestmentFile() {
        return $this->$valuesEcrowdInvestInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesEcrowdInvestAmortization;
    }    
    
    
    /**
     *
     * 	Calculates how must it will cost in total to obtain a loan for a certain amount
     * 	from a company
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked:xx-xx-xxxx

        $minimumCommission = 12000;   // in  €cents

        $fixedCost = 3 * $amount / 100;
        if ($fixedCost < $minimumCommission) {
            $fixedCost = $minimumCommission;
        }

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }

    /**
     * Collects the marketplace data
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) { //ecrown doesnt have pagination
        $readController = 0;
        $investmentController = false;

        $totalArray = array();
        $str = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tag = 'div';
        $attribute = 'class';
        $value = 'col-xs-12 col-md-4 col-sm-4 projectwidget';
        $projectwidgets = $this->getElements($dom, $tag, $attribute, $value);


        foreach ($projectwidgets as $key => $projectwidget) {

            if ($key == 0) { //Compare structures, only compare the first element
                $structureRevision = $this->htmlRevision($structure, 'div', null, 'class', 'col-xs-12', array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'filter-projects'));
                if ($structureRevision[1]) {
                    $totalArray = false; //Stop reading in error                         
                    break;
                }
            }



            $tag2 = 'p';
            $ps = $this->getElements($projectwidget, $tag2);
            $purposeLocation = explode('- ', trim($ps[0]->nodeValue)); //gets purpose & location separated by "- "

            if (trim($ps[9]->nodeValue) == "-") {
                $value2 = 0;
            } else {
                $value2 = trim($ps[9]->nodeValue);
            }


            $tag3 = 'h2';
            $hs = $this->getElements($projectwidget, $tag3);

            $tag4 = 'div';
            $attribute4 = 'class';
            $value4 = 'progress-bar';
            $progress = $this->getElements($projectwidget, $tag4, $attribute4, $value4);

            $tag5 = 'a';
            $as = $this->getElements($projectwidget, $tag5);
            $timeLeft = explode(' ', trim($hs[0]->nodeValue))[1] . ' ' . explode(' ', trim($hs[0]->nodeValue))[2];

            $tempArray['marketplace_country'] = 'ES';
            $tempArray['marketplace_purpose'] = trim($purposeLocation[0]);
            $tempArray['marketplace_requestorLocation'] = trim($purposeLocation[1]);
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($ps[3]->nodeValue);
            $tempArray['marketplace_interestRate'] = $this->getPercentage($ps[5]->nodeValue);
            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($ps[7]->nodeValue);
            $tempArray['marketplace_numberOfInvestors'] = $value2;
            $tempArray['marketplace_status'] = trim($hs[0]->nodeValue);
            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(intval($progress[0]->getAttribute('aria-valuenow')));
            $tempArray['marketplace_loanReference'] = preg_replace('/\D/', '', $as[0]->getAttribute('id'));


            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {

                if ($tempArray['marketplace_status'] == '100% financiado') {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                    $tempArray['marketplace_status'] = CONFIRMED;
                } else {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con Tiempo';
                    $tempArray['marketplace_status'] = PERCENT;
                }
                foreach ($companyBackup as $inversionBackup) { //if completed and same status that in backup
                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                        $readController++;
                        $investmentController = true;
                    }
                }
            } else if ($tempArray['marketplace_status'] == 'En estudio') {
                $tempArray['marketplace_statusLiteral'] = 'En estudio';
            } else {
                $tempArray['marketplace_statusLiteral'] = 'En proceso';
            }


            if ($investmentController) { //Don't save a already existing investment
                unset($tempArray);
                $investmentController = false;
            } else {
                $totalArray[] = $tempArray;
                $this->print_r2($totalArray);
                unset($tempArray);
            }

            if ($readController > 25) {  //If we find more than 25 completed investment existing in the backpup, stop reading
                echo 'Stop reading';
                break;
            }
        }
        $this->print_r2($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     *  Collect all investments
     * @param Array $structure
     * @return Array 
     */
    function collectHistorical($structure) { //ecrown doesnt have pagination
        $totalArray = array();
        $str = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tag = 'div';
        $attribute = 'class';
        $value = 'col-xs-12 col-md-4 col-sm-4 projectwidget';
        $projectwidgets = $this->getElements($dom, $tag, $attribute, $value);
        foreach ($projectwidgets as $key => $projectwidget) {

            if ($key == 0 && $structure) { //Compare structures, only compare the first element      
                $structureRevision = $this->htmlRevision($structure, 'div', null, 'class', 'col-xs-12', array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'filter-projects'));
                if ($structureRevision[1]) {
                    $totalArray = false; //Stop reading in error                         
                    break;
                }
            }


            $tag2 = 'p';
            $ps = $this->getElements($projectwidget, $tag2);
            $purposeLocation = explode('- ', trim($ps[0]->nodeValue)); //gets purpose & location separated by "- "

            if (trim($ps[9]->nodeValue) == "-") {
                $value2 = 0;
            } else {
                $value2 = trim($ps[9]->nodeValue);
            }


            $tag3 = 'h2';
            $hs = $this->getElements($projectwidget, $tag3);

            $tag4 = 'div';
            $attribute4 = 'class';
            $value4 = 'progress-bar';
            $progress = $this->getElements($projectwidget, $tag4, $attribute4, $value4);

            $tag5 = 'a';
            $as = $this->getElements($projectwidget, $tag5);
            $timeLeft = explode(' ', trim($hs[0]->nodeValue))[1] . ' ' . explode(' ', trim($hs[0]->nodeValue))[2];

            $tempArray['marketplace_country'] = 'ES';
            $tempArray['marketplace_purpose'] = trim($purposeLocation[0]);
            $tempArray['marketplace_requestorLocation'] = trim($purposeLocation[1]);
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($ps[3]->nodeValue);
            $tempArray['marketplace_interestRate'] = $this->getPercentage($ps[5]->nodeValue);
            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($ps[7]->nodeValue);
            $tempArray['marketplace_numberOfInvestors'] = $value2;
            $tempArray['marketplace_status'] = trim($hs[0]->nodeValue);
            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(intval($progress[0]->getAttribute('aria-valuenow')));
            $tempArray['marketplace_loanReference'] = preg_replace('/\D/', '', $as[0]->getAttribute('id'));


            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {

                if ($tempArray['marketplace_status'] == '100% financiado') {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                    $tempArray['marketplace_status'] = CONFIRMED;
                } else {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con Tiempo';
                    $tempArray['marketplace_status'] = PERCENT;
                }
            } else if ($tempArray['marketplace_status'] == 'En estudio') {
                $tempArray['marketplace_statusLiteral'] = 'En estudio';
                $tempArray['marketplace_status'] = 3;
            } else {
                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                $tempArray['marketplace_status'] = null;
            }


            $totalArray[] = $tempArray;
            unset($tempArray);
        }
        $this->print_r2($totalArray);
        return [$totalArray, false, null, $structureRevision[0], $structureRevision[2]]; //$totaArray -> Investments / false -> ecrown doesnt have pagination
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }
    
    
    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData() {
        
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserGlobalFilesParallel($str) {
        switch ($this->idForSwitch) {
            //LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $csrf = $input[0]->getAttribute('value'); //this is the csrf token

                $this->credentials['_username'] = $this->$user;
                $this->credentials['_password'] = $this->$password;
                $this->credentials['_csrf_token'] = $csrf;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                // echo $str;

                $confirm = false;

                $lis = $dom->getElementsByTagName('li');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($lis as $li) {
                    //echo 'Entrando $li ' . 'href value; ' . $li->getAttribute('herf') . ' node value' . $li->nodeValue . HTML_ENDOFLINE;
                    if (trim($li->nodeValue) == 'resumen') {
                        //echo 'Li encontrado' . HTML_ENDOFLINE;
                        $confirm = true;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "EcrowdInvest login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                //LOGIN END
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
        }
    }
    
    /**
     * 
     * @param type $str
     * @return type
     */
    function collectAmortizationTablesParallel($str) { 
        switch ($this->idForSwitch) {
            //LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $csrf = $input[0]->getAttribute('value'); //this is the csrf token

                $this->credentials['_username'] = $this->$user;
                $this->credentials['_password'] = $this->$password;
                $this->credentials['_csrf_token'] = $csrf;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                // echo $str;

                $confirm = false;

                $lis = $dom->getElementsByTagName('li');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($lis as $li) {
                    //echo 'Entrando $li ' . 'href value; ' . $li->getAttribute('herf') . ' node value' . $li->nodeValue . HTML_ENDOFLINE;
                    if (trim($li->nodeValue) == 'resumen') {
                        //echo 'Li encontrado' . HTML_ENDOFLINE;
                        $confirm = true;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                //LOGIN END
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company                
                break;
        }        
    }

    /**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account
     * 	
     * 	@param string	$user		username
     * 	@param string	$password	password
     * 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
     * 					false: 		user could not log in
     * 	
     */
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY ECROWDINVEST DURING LOGIN PROCESS
          $credentials['_csrf_token'] = "XXXXX";
         */

        //First we need get the $csrf token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
        $csrf = $input[0]->getAttribute('value'); //this is the csrf token

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;
        $credentials['_csrf_token'] = $csrf;


        if (!empty($options)) {
            foreach ($options as $key => $option) {
                $credentials[$key] = $option[$key];
            }
        }

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login


        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        // echo $str;

        $confirm = false;

        $lis = $dom->getElementsByTagName('li');
        foreach ($lis as $li) {
            //echo 'Entrando $li ' . 'href value; ' . $li->getAttribute('herf') . ' node value' . $li->nodeValue . HTML_ENDOFLINE;
            if (trim($li->nodeValue) == 'resumen') {
                //echo 'Li encontrado' . HTML_ENDOFLINE;
                $confirm = true;
            }
        }

        if ($confirm) {
            return true;
        }
        return false;
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

    /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {

        //We need remove this attribute directly from the div tag(the father)
        $node1->removeAttribute('class');
        $node1->removeAttribute('Style');
        $node2->removeAttribute('class');
        $node2->removeAttribute('Style');


        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
                ), array('a', 'href', 'id', 'alt', 'title', 'src', 'height', 'srcset', 'sizes', 'width'));


        $node1 = $this->cleanDom($node1, array(//Clear progress div
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class', 'style', 'data-toggle', 'data-placement', 'title', 'data-original-title', 'aria-valuenow'));

        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'strong'), //We dont have strong tag in completed investment
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'class', 'value' => 'blue'), //Span tag causes problems
        ));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
                ), array('a', 'href', 'id', 'alt', 'title', 'src', 'height', 'srcset', 'sizes', 'width'));

        $node2 = $this->cleanDom($node2, array(//Clear progress div
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class', 'style', 'data-toggle', 'data-placement', 'title', 'data-original-title', 'aria-valuenow'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'strong'), //We dont have strong tag in completed investment
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'class', 'value' => 'blue'), //Span tag causes problems
        ));


        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}
