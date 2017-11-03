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
 * function calculateLoanCost()										[Not OK]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * function companyUserLogout()										[Not OK, testing]
 * parallelization on collectUserInvestmentData                                                            [OK, tested]
 *
 * 2016-11-05	  version 2016_0.1
 * Basic version
 *
 * 2017-03-27
 * line 251 - changed parameter for url.
 *
 * 2017-03-18
 * Captured exceptions for table errors.
 *
 * 2017-4-18
 * Login error fixed always forcing login
 *
 * 2017-06-01
 * Added new urlSequences for Arboribus, it changed because we had some errors
 * Fixed logout and login problem
 *
 *
 *  2017-08-07
 *  Arboribus 100% adaptation
 *  collectCompanyMarketplaceData - Read completed investment table
 *  collectHistorical - Added
 * 
 * 2017-08-10
 * Structure revision added
 * 
 * 2017-08-11
 * Status definition added
 * 
 * 
 */
class arboribus extends p2pCompany {

    private $credentials = array();
    private $strListInvestments;
    private $tempDataInvestment;
    private $investmentSequence = 0;
    private $numberIfInvestments;

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how much it will cost in total to obtain a loan for a certain amount
     * 	from a company
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked: 26-08-2016

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
     * Collects the marketplace data, Arboribus don't need pagination.
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) {


        $readController = 0;
        $investmentController = false;
        $totalArray = array();
        $this->investmentDeletedList = $loanIdList;

        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tableNumber = 1; //Arboribus have two tables.

        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $keyTable => $table) {
            $classy = $table->getAttribute("class");

            //echo 'CLASE' . $classy;
            //echo '<br>';


            if ($classy == "arb_subasta_table") { //Read the tables with investment
                $trs = $table->getElementsByTagName('tr');
                if ($totalArray !== false) {
                    foreach ($trs as $key => $tr) {
                        $tempArray = array();



                        if ($key == 0 && $keyTable == 0) { //Compare structures, olny compare the first element
                            $structureRevision = $this->htmlRevision($structure, 'tr', $table, null, null, null, 0, 1);
                            if ($structureRevision[1]) {
                                $totalArray = false; //Stop reading in error                 
                                break;
                            }
                        }


                        $scripts = $tr->getElementsByTagName('script');
                        foreach ($scripts as $script) { //Time left is in a script, read the script and calculate.
                            $date = explode(',', preg_replace('/[^0-9\-,]/', '', $script->nodeValue));
                            $limitDay = $date[0] . '-' . explode('-', $date[1])[0] . '-' . $date[2] . ' ' . $date[7] . ':' . substr(trim($date[8]), 0, -1);
                            $limitDate = strtotime($limitDay);
                            $now = strtotime(date("Y-m-d H:i"));
                            $timeleft = $limitDate - $now;
                            $daysleft = round((($timeleft / 24) / 60) / 60);


                            echo $limitDate;
                            echo '<br>';
                            echo $now;
                            echo '<br>';
                            echo $timeleft;
                            echo '<br>';
                            echo $daysleft;
                            echo '<br>';

                            $tempArray['marketplace_timeLeft'] = $daysleft;
                            $tempArray['marketplace_timeLeftUnit'] = 1;
                        }

                        $tds = $tr->getElementsByTagName('td');

                        $index = -1;

                        $tempArray['marketplace_country'] = 'ES';

                        foreach ($tds as $td) {

                            $index = $index + 1;
                            switch ($index) {
                                case 1:
                                    $tempArray['marketplace_loanReference'] = $td->nodeValue;
                                    break;
                                case 2:
                                    $innerIndex = 0;
                                    $as = $td->getElementsByTagName('a');

                                    foreach ($as as $a) {  // only 1 will be found
                                        $tempArray['marketplace_purpose'] = trim($a->nodeValue);
                                    }
                                    $tempArray['marketplace_requestorLocation'] = trim(str_replace($tempArray['marketplace_purpose'], "", $td->nodeValue));
                                    break;
                                case 3:
                                    $tempArray['marketplace_rating'] = $td->nodeValue;
                                    break;
                                case 4:
                                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                                    break;
                                case 6:
                                    $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($td->nodeValue));
                                    break;
                                case 7:
                                    $divs = $td->getElementsByTagName('div');
                                    $innerIndex = 0;
                                    foreach ($divs as $div) {

                                        if ($innerIndex == 1) {
                                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($div->nodeValue));

                                            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                                                if ($tableNumber == 1) {
                                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
                                                    $tempArray['marketplace_status'] = PERCENT;
                                                } else {
                                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                                                    $tempArray['marketplace_status'] = CONFIRMED;
                                                }
                                                foreach ($companyBackup as $inversionBackup) {
                                                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                                        $readController++;
                                                        $investmentController = true;
                                                        $tempArray['marketplace_timeLeft'] = 0;
                                                        $tempArray['marketplace_timeLeftUnit'] = -1;
                                                    }
                                                }
                                            } else {
                                                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                                            }
                                        }
                                        $innerIndex = $innerIndex + 1;
                                    }
                                    break;
                                case 8:
                                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($td->nodeValue);
                                    break;
                            }
                        }

                        $this->print_r2($tempArray);

                        if ($investmentController) { //Don't save a already existing investment
                            unset($tempArray);
                            $investmentController = false;
                        } else {
                            $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList, $tempArray);
                            $totalArray[] = $tempArray;
                            unset($tempArray);
                        }
                    }
                }
            }
            $tableNumber++;
        }

        echo 'Search this investments: ' . SHELL_ENDOFLINE;
        $this->print_r2($this->investmentDeletedList);
        $hiddenInvestments = $this->readHiddenInvestment($this->investmentDeletedList);
        echo 'Hidden: ' . SHELL_ENDOFLINE;
        $this->print_r2($hiddenInvestments);
        $totalArray = array_merge($totalArray, $hiddenInvestments);
        echo 'Marketplace:' . HTML_ENDOFLINE;
        $this->print_r2($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /*     * Read hidden investment.
     * 
     * @param array $investmentDeletedList loan id list
     * @return array investments info list
     */

    function readHiddenInvestment($investmentDeletedList) {


        $url = array_shift($this->urlSequence);
        $tempArray = array();
        $newTotalArray = array();
        //Read investment info
        foreach ($investmentDeletedList as $loanId) {

            $str = $this->getCompanyWebpage($url . $loanId . ".html");
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($str);

            $tempArray['marketplace_loanReference'] = $loanId;

            $tables = $dom->getElementsByTagName('table');
            foreach ($tables as $key => $table) {
                echo $key . '=>' . $table->nodeValue . HTML_ENDOFLINE;
                if ($key == 0) {
                    if (strpos($table->nodeValue, "100%")) {
                        $tempArray['marketplace_subscriptionProgress'] == 10000;
                        $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                        $tempArray['marketplace_status'] = CONFIRMED;
                    } else {
                        $tempArray['marketplace_statusLiteral'] = 'Cancelado';
                        $tempArray['marketplace_status'] = REJECTED;
                    }
                }
            }

            $price = $this->getElements($dom, "div", "class", "price");
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($price[0]->nodeValue);

            $lis = $dom->getElementsByTagName("li");
            foreach ($lis as $keyLi => $li) {
                echo 'li ' . $keyLi . " is " . $li->nodeValue . HTML_ENDOFLINE;
                switch ($keyLi) {
                    case 7:
                        $str = explode(":", $li->nodeValue);
                        print_r($str);
                        $tempArray['marketplace_interestRate'] = $this->getPercentage($str[1]);
                        break;
                    case 9:
                        $str = explode(":", $li->nodeValue);
                        $tempArray['marketplace_rating'] = trim($str[1]);
                        break;
                    case 10:
                        $str = explode(":", $li->nodeValue);
                        list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit']) = $this->getDurationValue($str[1]);
                        break;
                    case 11:
                        $str = explode(":", $li->nodeValue);
                        $tempArray['marketplace_sector'] = trim($str[1]);
                        break;
                    case 15:
                        $str = explode("-", $li->nodeValue);
                        $tempArray['marketplace_purpose'] = trim($str[1]);
                        break;
                    case 16:
                        $str = explode(":", $li->nodeValue);
                        $tempArray['marketplace_requestorLocation'] = trim($str[1]);
                        break;
                }
            }
            echo 'Hidden investment: ' . SHELL_ENDOFLINE;
            echo print_r($tempArray) . SHELL_ENDOFLINE;
            $newTotalArray[] = $tempArray;
            unset($tempArray);
        }
        return $newTotalArray;
    }

    /**
     * collect all investment
     * @param Array $structure
     * @param Int $page
     * @param Int $type
     * @return Array
     */
    function collectHistorical($structure, $page = null, $type = null) { //Arboribus dont have paginaticon
        $totalArray = array();

        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;


        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $keyTable => $table) {   // only deal with FIRST table in document
            $classy = $table->getAttribute("class");

            echo 'CLASE' . $classy;
            echo '<br>';


            if ($classy == "arb_subasta_table") {

                $trs = $table->getElementsByTagName('tr');

                if ($totalArray !== false) {
                    foreach ($trs as $key => $tr) {


                        if ($key == 0 && $keyTable == 0) { //Compare structures, olny compare the first element
                            $structureRevision = $this->htmlRevision($structure, 'tr', $table, null, null, null, 0, 1);
                            if ($structureRevision[1]) {
                                $totalArray = false; //Stop reading in error                 
                                break;
                            }
                        }


                        $tds = $tr->getElementsByTagName('td');
                        $index = -1;

                        $tempArray = array();

                        $scripts = $tr->getElementsByTagName('script');
                        foreach ($scripts as $script) {

                            $date = explode(',', preg_replace('/[^0-9\-,]/', '', $script->nodeValue));
                            $limitDay = $date[0] . '-' . explode('-', $date[1])[0] . '-' . $date[2] . ' ' . $date[7] . ':' . substr(trim($date[8]), 0, -1);
                            $limitDate = strtotime($limitDay);
                            $now = strtotime(date("Y-m-d H:i"));
                            $timeleft = $limitDate - $now;
                            $daysleft = round((($timeleft / 24) / 60) / 60);


                            echo $limitDate;
                            echo '<br>';
                            echo $now;
                            echo '<br>';
                            echo $timeleft;
                            echo '<br>';
                            echo $daysleft;
                            echo '<br>';

                            $tempArray['marketplace_timeLeft'] = $daysleft;
                            $tempArray['marketplace_timeLeftUnit'] = 1;
                        }


                        $tempArray['marketplace_country'] = 'ES';
                        $tempArray['marketplace_timeLeft'] = $daysleft;
                        $tempArray['marketplace_timeLeftUnit'] = 1;

                        foreach ($tds as $td) {
                            $index = $index + 1;

                            switch ($index) {
                                case 1:
                                    $tempArray['marketplace_loanReference'] = $td->nodeValue;
                                    break;
                                case 2:
                                    $innerIndex = 0;
                                    $as = $td->getElementsByTagName('a');

                                    foreach ($as as $a) {  // only 1 will be found
                                        $tempArray['marketplace_purpose'] = trim($a->nodeValue);
                                    }
                                    $tempArray['marketplace_requestorLocation'] = trim(str_replace($tempArray['marketplace_purpose'], "", $td->nodeValue));
                                    break;
                                case 3:
                                    $tempArray['marketplace_rating'] = $td->nodeValue;
                                    break;
                                case 4:
                                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                                    break;
                                case 6:
                                    $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($td->nodeValue));
                                    break;
                                case 7:
                                    $divs = $td->getElementsByTagName('div');
                                    $innerIndex = 0;
                                    foreach ($divs as $div) {

                                        if ($innerIndex == 1) {
                                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($div->nodeValue));
                                        }
                                        $innerIndex = $innerIndex + 1;
                                    }
                                    break;
                                case 8:
                                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($td->nodeValue);
                                    break;
                            }
                        }

                        if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                            if ($tableNumber == 1) {
                                $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
                                $tempArray['marketplace_status'] = PERCENT;
                            } else {
                                $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                                $tempArray['marketplace_status'] = CONFIRMED;
                                $tempArray['marketplace_timeLeft'] = 0;
                                $tempArray['marketplace_timeLeftUnit'] = -1;
                            }
                        } else {
                            $tempArray['marketplace_statusLiteral'] = 'En proceso';
                        }

                        $this->print_r2($tempArray);

                        $totalArray[] = $tempArray;

                        unset($tempArray);
                    }
                }
            }
        }
        return [$totalArray, false, null, $structureRevision[0], $structureRevision[2]];
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
    function collectUserInvestmentDataParallel($str) {

        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                //Login fixed
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $this->credentials['username'] = $this->user;
                $this->credentials['password'] = $this->password;
                $forms = $dom->getElementsByTagName('form');
                foreach ($forms as $form) {
                    $inputs = $form->getElementsByTagName('input');
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // look for the post variables
                            if ($input->getAttribute('type') == "hidden") {
                                $this->credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                            }
                        }
                    }
                }
                $this->credentials['task'] = "user.logout";
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                unset($this->credentials);
                break;
            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                //$resultMyArboribus = $this->companyUserLogin($user, $password);
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $this->credentials['username'] = $this->user;
                $this->credentials['password'] = $this->password;

                $forms = $dom->getElementsByTagName('form');
                foreach ($forms as $form) {
                    $inputs = $form->getElementsByTagName('input');
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // look for the post variables
                            if ($input->getAttribute('type') == "hidden") {
                                $this->credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                            }
                        }
                    }
                }
                $this->credentials['task'] = "user.login";
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                break;
            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $as = $dom->getElementsByTagName('a');
                $resultMyArboribus = false;

                foreach ($as as $a) {
                    if (strcasecmp(trim($a->nodeValue), "Salir") == 0) {  // Login confirmed           
                        $this->mainPortalPage = $str;
                        $resultMyArboribus = true;
                    }
                }


                if (!$resultMyArboribus) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . " LOGIN ERROR<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Arboribus login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";
                $dom = new DOMDocument;
                $dom->loadHTML($this->mainPortalPage); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
                $dom->preserveWhiteSpace = false;
                //        echo $this->mainPortalPage;
                echo __FILE__ . " " . __LINE__ . "<br>";

                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 5:
                $summary = $str;
                //echo $summary;
                $dom = new DOMDocument;
                $dom->loadHTML($summary); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
                $dom->preserveWhiteSpace = false;


                echo __FILE__ . " " . __LINE__ . "<br>";

                $divs = $this->getElements($dom, "div", "class", "arb_detail_right col-xs-12 col-sm-6");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . "<br>";

                foreach ($divs as $key => $div) {
                    echo "key = $key, " . $div->nodeValue . "<br>";
                }

                $trs = $this->getElements($divs[1], "td", "class", "tcell-align-right");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($trs[1]->nodeValue);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $h3s = $this->getElements($dom, "h3", "style", "font-size:xx-large;");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->tempArray['global']['profitibility'] = $this->getPercentage($h3s[0]->nodeValue);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();     // list of investments as JSON
                break;
            case 6:
                $strListInvestments = $str;
                $investmentListItems = json_decode($strListInvestments, true);
                echo __FILE__ . " " . __LINE__ . "<br>";
                //$this->print_r2($investmentListItems);
                echo __FILE__ . " " . __LINE__ . "<br>";
                // Get next msg from the urlSequence queue:
                $url = array_shift($this->urlSequence);
                echo __FILE__ . " " . __LINE__ . "<br>";
                //echo $url;
                print_r($this->urlSequence);
                //echo __FILE__ . " " . __LINE__ . "<br>";
                $this->numberIfInvestments = 0;
                foreach ($investmentListItems as $key => $investmentListItem) {
                    // mapping of the investment data to internal dashboard format of Winvestify
                    $this->tempDataInvestment[$this->numberIfInvestments]['loanId'] = $investmentListItem['id_company'];
                    $this->tempDataInvestment[$this->numberIfInvestments]['interest'] = $this->getPercentage(trim($investmentListItem['interes']));
                    $this->tempDataInvestment[$this->numberIfInvestments]['xxxx'] = $this->getMonetaryValue(trim($investmentListItem['capitalpendiente']), '.');
                    //$this->print_r2($tempDataInvestment);
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    //Changed the parameter for the url
                    $this->tempUrl[$this->numberIfInvestments] = $url . $investmentListItem['id_company'];
                    //$str = $this->getCompanyWebpage($url . $investmentListItem['id_company']);   // is the amortization table
                    $this->numberIfInvestments++;
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->investmentSequence]);
                break;
            case 7:
                //echo $str;
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                echo __FILE__ . " " . __LINE__ . "<br>";
                // deal with amortization table and normalize the loan state
                /* try { */
                $projectAmortizationData = $this->getElements($dom, "table", "class", "resumen"); // only 1 found
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . "<br>";
                $trs = $projectAmortizationData[0]->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . "<br>";
                $mainIndex = -1;
                foreach ($trs as $key1 => $tr) {
                    $mainIndex = $mainIndex + 1;
                    $subIndex = -1;
                    $tds = $tr->getElementsByTagName('td');
                    /* $this->verifyNodeHasElements($tds);
                      if (!$this->hasElements) {
                      return $this->getError(__LINE__, __FILE__);
                      } */
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    foreach ($tds as $td) {
                        $subIndex = $subIndex + 1;
                        //                       echo __FILE__ . " " . __LINE__ . "<br>";
                        //¿
                        if ($subIndex == 7) {
                            $imgs = $this->getElements($td, "img", "title", "pagado");
                            if (!empty($imgs)) { // We found the footer, simply ignore			
                                $actualState = $imgs[0]->getAttribute("title");
                                $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                            }
                        } else {
                            $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                        }
                    }
                }
//                echo __FILE__ . " " . __LINE__ . "<br>";
                $tempInvested = array_pop($amortizationTable);  // get contents of "footer" and remove it from the amortization table 
//		$tempDataInvestment['invested'] = stripos(trim($tempInvested[3]));
                $this->tempDataInvestment[$this->investmentSequence]['invested'] = trim(preg_replace('/\D/', '', $tempInvested[3]));

// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED		
//		if (strncasecmp($investmentListItem['estado'], "Al d", 2) == 0) {		// checking for status words "Al día"
//			$tempDataInvestment['status'] = OK;
//		}
                //               echo __FILE__ . " " . __LINE__ . "<br>";
                $this->tempDataInvestment[$this->investmentSequence]['commission'] = 0;
//Duration	Unit (=meses) is hard coded		
                $this->tempDataInvestment[$this->investmentSequence]['duration'] = count($amortizationTable) . " Meses";
                $this->tempDataInvestment[$this->investmentSequence]['date'] = $this->getHighestDateValue($amortizationTable, "dd-mm-yyyy", 1);
                $this->tempDataInvestment[$this->investmentSequence]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 7);
                $this->tempDataInvestment[$this->investmentSequence]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 7);
                $this->tempArray['investments'][] = $this->tempDataInvestment[$this->investmentSequence];
                //               echo __FILE__ . " " . __LINE__ . "<br>";
// update the global data of Arboribus
                $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['activeInInvestments'] +
                        $this->tempDataInvestment[$this->investmentSequence]['xxxx'];
                $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] +
                        $this->tempDataInvestment[$this->investmentSequence]['profitGained'];
                $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->tempDataInvestment[$this->investmentSequence]['invested'];
//                echo __FILE__ . " " . __LINE__ . "<br>";
                // The normal logout procedure does not work so do a workaround
                // Force a logout with data elements provided in the last read page.
                //$this->companyUserLogout();
                if (($this->numberIfInvestments - 1) != $this->investmentSequence) {
                    $this->idForSwitch = 7;
                    $this->investmentSequence++;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->investmentSequence]);
                    break;
                } else {
                    $this->tempArray['global']['investments'] = $this->numberIfInvestments;
                    $this->idForSwitch++;
                    $this->getCompanyWebpageMultiCurl();
                    break;
                }
            case 8:
                //Added urlsequences on database for logout
                //When it is here, the logout is already made so if we make the logout,
                //it is unnecessary but we make it otherwise
                return $this->tempArray;
        }
        //return $tempArray;
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {

        $resultMyArboribus = $this->companyUserLogin($user, $password);
        if (!$resultMyArboribus) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . " LOGIN ERROR<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Arboribus login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        //echo array_shift($this->urlSequence);
        echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        //echo array_shift($this->urlSequence);
        $resumen = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($resumen); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        $tables = $dom->getElementsByTagName("table");
        /*foreach ($tables as $key => $table) { //DEBUG
            echo "Tables Value  ";
            echo $key . " => " . $table->nodeValue . HTML_ENDOFLINE;
        }*/

        $tds = $tables[1]->getElementsByTagName("tr");

        /*foreach ($tds as $key => $td) { //DEBUG
            echo "TD VALUE ";
            echo $key . " => " . $td->nodeValue;
        }*/
        $tempArray['global']['myWallet'] = $this->getMonetaryValue($tds[1]->nodeValue);

        $spans = $dom->getElementsByTagName("span");
        /*foreach ($spans as $key => $span) { //DEBUG
            echo "span Value ";
            echo $key . " => " . $span->nodeValue;
        }*/
        $tempArray['global']['profitibility'] = $this->getPercentage($spans[8]->nodeValue);

        $str1 = $this->getCompanyWebpage();     // list of investments as JSON
        echo $str1;
        $strListInvestments = $str1;
        $investmentListItems = json_decode($strListInvestments, true);
        print_r($investmentListItem);
        echo __FILE__ . " " . __LINE__ . "<br>";

        echo __FILE__ . " " . __LINE__ . "<br>";
        // Get next msg from the urlSequence queue:
        $url = array_shift($this->urlSequence);
        echo __FILE__ . " " . __LINE__ . "<br>";


        //       echo __FILE__ . " " . __LINE__ . "<br>";
        $numberIfInvestments = 0;
        foreach ($investmentListItems as $key => $investmentListItem) {
            echo "KEY" . $key;
            print_r($investmentListItem);
            $numberIfInvestments = $numberIfInvestments + 1;

            // mapping of the investment data to internal dashboard format of Winvestify
            $tempDataInvestment['loanId'] = $investmentListItem['id_company'];
            $tempDataInvestment['interest'] = $this->getPercentage(trim($investmentListItem['interes']));
            $tempDataInvestment['xxxx'] = $this->getMonetaryValue(trim($investmentListItem['capitalpendiente']));
            //           $this->print_r2($tempDataInvestment);
            /*echo __FILE__ . " " . __LINE__ . "<br>";*/
            //Changed the parameter for the url

            $str = $this->getCompanyWebpage($url . $investmentListItem['id_company']);   // is the amortization table
            //echo $str;
            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;
            echo __FILE__ . " " . __LINE__ . "<br>";
            // deal with amortization table and normalize the loan state

            $projectAmortizationData = $this->getElementsByTagName("table"); // only 1 found
            $trs = $projectAmortizationData[0]->getElementsByTagName('tr');
            echo __FILE__ . " " . __LINE__ . "<br>";

            $mainIndex = -1;
            foreach ($trs as $key1 => $tr) {
                $mainIndex = $mainIndex + 1;
                $subIndex = -1;
                $tds = $tr->getElementsByTagName('td');
                foreach ($tds as $td) {
                    $subIndex = $subIndex + 1;

                    if ($subIndex == 7) {
                        $imgs = $this->getElements($td, "img", "title", "pagado");
                        if (!empty($imgs)) { // We found the footer, simply ignore
                            $actualState = $imgs[0]->getAttribute("title");
                            $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                        }
                    } else {
                        $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                    }
                }
            }
//                echo __FILE__ . " " . __LINE__ . "<br>";
            /* $tempInvested = array_pop($amortizationTable);  // get contents of "footer" and remove it from the amortization table 
              //		$tempDataInvestment['invested'] = stripos(trim($tempInvested[3]));
              $tempDataInvestment['invested'] = trim(preg_replace('/\D/', '', $tempInvested[3]));

              // map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED
              //		if (strncasecmp($investmentListItem['estado'], "Al d", 2) == 0) {		// checking for status words "Al día"
              //			$tempDataInvestment['status'] = OK;
              //		}

              $tempDataInvestment['commission'] = 0;
              //Duration	Unit (=meses) is hard coded
              $tempDataInvestment['duration'] = count($amortizationTable) . " Meses";
              $tempDataInvestment['date'] = $this->getHighestDateValue($amortizationTable, "dd-mm-yyyy", 1);
              $tempDataInvestment['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 7);
              $tempDataInvestment['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 7);
              $tempArray['investments'][] = $tempDataInvestment;

              // update the global data of Arboribus
              $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] +
              $tempDataInvestment['xxxx'];
              $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
              $tempDataInvestment['profitGained'];
              $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $tempDataInvestment['invested'];
              $tempArray['global']['investments'] = $tempArray['global']['investments'] + $numberOfInvestments + 1;

              unset($tempDataInvestment);
              /* } catch (Exception $e) {
              echo 'Excepción capturada: ', $e->getMessage(), "\n";
              $tempArray['global']['myWallet'] = 0;
              $tempArray['global']['profitibility'] = 0;
              $tempArray['global']['activeInInvestments'] = 0;
              $tempArray['global']['totalEarnedInterest'] = 0;
              $tempArray['global']['totalInvestment'] = 0;
              $tempArray['global']['investments'] = 0;
              } */
        }
        echo __FILE__ . " " . __LINE__ . "<br>";

        // The normal logout procedure does not work so do a workaround
        // Force a logout with data elements provided in the last read page.

        $credentials['username'] = $user;
        $credentials['password'] = $password;
        $this->companyUserLogout($credentials);
        return $tempArray;
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
    function companyUserLogin($user = "", $password = "") {
        /* 	
          FIELDS USED BY ARBORI DURING LOGIN PROCESS

          5f48fa2e824927e1d3e1f04cd...	1
          Submit.x						12
          Submit.y						7
          option							com_users
          password						Pepa2016
          remember						yes
          return							aW5kZXgucGhwP0l0ZW1pZD0xMjU=
          task							user.login
          username						pepamiras@gmail.com
         */
        $credentials['username'] = $user;
        $credentials['password'] = $password;


//Login fixed
        $this->companyUserLogout($credentials);

        /* $credentials = companyUserLogout($credentials); */

        $str = $this->getCompanyWebpage();    // Load main page, needed so I can read the csrf code
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');

        foreach ($forms as $form) {
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('name'))) {  // look for the post variables
                    if ($input->getAttribute('type') == "hidden") {
                        $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                    }
                }
            }
        }


        //Forced user.login
        $credentials['task'] = "user.login";
        $str = $this->doCompanyLogin($credentials);   // Send the post request with authentication information

        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');
        $as = $dom->getElementsByTagName('a');

        foreach ($as as $a) {
            if (strcasecmp(trim($a->nodeValue), "Salir") == 0) {  // Login confirmed           
                $this->mainPortalPage = $str;
                return true;
            }
        }
        return 0; // Log in fails due to authentication error 
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout($credentials) {

        $str = $this->getCompanyWebpage();    // Load main page, needed so I can read the csrf code
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $as = $dom->getElementsByTagName('a');
        $forms = $dom->getElementsByTagName('form');

        foreach ($forms as $form) {
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('name'))) {  // look for the post variables
                    if ($input->getAttribute('type') == "hidden") {
                        $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                    }
                }
            }
        }

        foreach ($as as $a) {
            if (strcasecmp(trim($a->nodeValue), "Salir") == 0) {  // Login confirmed
                $this->mainPortalPage = $str;
                $credentials['task'] = "user.logout";
                $str = $this->doCompanyLogin($credentials);
            }
        }
    }

    function companyUserLogoutMultiCurl($str) {


        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $credentials['username'] = $this->user;
        $credentials['password'] = $this->password;

        $as = $dom->getElementsByTagName('a');
        $forms = $dom->getElementsByTagName('form');

        foreach ($forms as $form) {
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('name'))) {  // look for the post variables
                    if ($input->getAttribute('type') == "hidden") {
                        $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                    }
                }
            }
        }

        foreach ($as as $a) {
            if (strcasecmp(trim($a->nodeValue), "Salir") == 0) {  // Login confirmed
                $this->mainPortalPage = $str;
                $credentials['task'] = "user.logout";
                $str = $this->doCompanyLogoutMultiCurl($credentials);
            }
        }
    }

    /**
     *
     * 	translate the html of loan state to the winvestify normalized state
     * 	@param	string		html of loanstate
     * 	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * 	
     */
    function getLoanState($actualState) {

        $loanStates = array("pendiente" => PENDING,
            "pagado" => OK,
            "impago" => PAYMENT_DELAYED,
            "retrasado" => DEFAULTED);
        foreach ($loanStates as $key => $state) {
            if ($key == $actualState) {
                return $state;
            }
        }
        echo "normalizedState = $normalizedState<br>";
        return $normalizedState;
    }

    /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('src', 'alt', 'href', 'style', 'id'));

        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'script'),
        ));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('src', 'alt', 'href', 'style', 'id'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'script'),
        ));

        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}

?>
