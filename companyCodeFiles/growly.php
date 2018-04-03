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
 * Contains the code required for accessing the website of "grow.ly"
 *
 *
 *
 * function calculateLoanCost()										[Not OK]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * parallelization                                                                                         [OK, tested]
 *
 *
 * 2016-08-12	  version 2016_0.1
 * Basic version
 *
 * 2017-05-24
 * Added parallelization 
 * Added dom verification
 *
 * 2017-08-03
 * Growly adaptation 100%
 *  collectCompanyMarketplaceData - Pagination loop added
 *  collectHistorical   -   Added
 *
 * 
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 *
 *
 */
class growly extends p2pCompany {

    
    protected $transactionConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
 
    protected $investmentConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );

/*    NOT YET READY
    protected $investmentConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );      
 
 */    
    
    
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
// Fixed cost: 2% of requested amount

        $fixedCost = 2 * $amount / 100;

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }

    /**
     * Collects the marketplace data
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     * 
     * SHOULD also use getMonetaryValue, getPercentage and getDurationValue functions
     * add check for "dìas" and marketplace_daysLeft (not tested yet)
     * redo marketplace_duration and use methods in base class  (not tested yet)
     */
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) {
        
        //Marketplace
        $this->investmentDeletedList = $loanIdList;
        $page = 1;
        $url = array_shift($this->urlSequence);
        $reading = true;
        $readController = 0;
        $investmentController = false;

        while ($reading) { //Read loop
            $investmentNumber = 0;

            $str = $this->getCompanyWebpage($url . $page);  // load Webpage into a string variable so it can be parsed

            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;

            $table = $dom->getElementsByTagName('table');
            $tbody = $dom->getElementsByTagName('tbody')[0];
//get all rows from the table
            $rows = $table->item(0)->getElementsByTagName('tr');
            $number = count($rows);
            echo 'Number of investment in page: ' . $number . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $index = -1;
            if ($totalArray !== false) {
                foreach ($rows as $key => $row) {

                    
                    if ($page == 1 && $key == 0) { //Compare structures, only compare the first element     
                        $structureRevision = $this->htmlRevision($structure,'tr',$tbody);
                        if($structureRevision[1]){
                            $totalArray = false; //Stop reading in error        
                            $reading = false;
                            break;
                        }      
                    }

                    $index++;

                    if ($index == 0) {
                        continue;  // don't check contents of first "tr"
                    }

                    $loanId = preg_replace('/\D/', '', $row->getAttribute('id'));
                    $tempArray['marketplace_loanReference'] = $loanId;
                    $tempArray['marketplace_country'] = 'ES'; //Growly is from Spain
                    $tds = $row->getElementsByTagName('td');
                    foreach ($tds as $td) {

                        $checkedAttribute = $td->getAttribute('class');
                        if (strcasecmp(trim($checkedAttribute), 'intro') == 0) {
                            $as = $td->getElementsByTagName('a');
                            foreach ($as as $item) {   // only 1 <strong> exists
                                $tempArray['marketplace_purpose'] = $item->nodeValue;
                            }

                            $ps = $td->getElementsByTagName('p');
                            foreach ($ps as $item) {   // only 1 <strong> exists
                                $tempArray['marketplace_vencimiento'] = $item->nodeValue;
                            }
                        }

                        $checkedAttribute = $td->getAttribute('data-meta');
                        if (strcasecmp(trim($checkedAttribute), 'interest') == 0) {
                            $strongs = $td->getElementsByTagName('strong');
                            foreach ($strongs as $strong) { // only 1 <strong> exists
                                $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($strong->nodeValue));
                            }
                        }

                        $checkedAttribute = $td->getAttribute('data-meta');
                        if (strcasecmp(trim($checkedAttribute), 'rating') == 0) {
                            $tempArray['marketplace_rating'] = trim($td->nodeValue);
                        }

                        $checkedAttribute = $td->getAttribute('data-meta');
                        if (strcasecmp(trim($checkedAttribute), 'term') == 0) {

                            $strongs = $td->getElementsByTagName('strong');
                            foreach ($strongs as $strong) { // only 1 <strong> exists
                                if (is_numeric(trim($strong->nodeValue))) {
                                    $tempDuration = trim($strong->nodeValue);
                                }
                            }
                            $spans = $td->getElementsByTagName('span');
                            foreach ($spans as $span) { // only 1 <span> exists	
                                $duration = $tempDuration . " " . trim($span->nodeValue);
                            }
                            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($duration);
                        }

                        $checkedAttribute = $td->getAttribute('data-meta');
                        if (strcasecmp(trim($checkedAttribute), 'time') == 0) {

                            $strongs = $td->getElementsByTagName('strong');
                            foreach ($strongs as $strong) { // only 1 <strong> exists
                                if (is_numeric(trim($strong->nodeValue))) {
                                    $tempTimeLeft = trim($strong->nodeValue);
                                }
                            }

                            $spans = $td->getElementsByTagName('span');
                            foreach ($spans as $span) { // only 1 <span> exists	
                                $timeLeft = $tempTimeLeft . " " . trim($span->nodeValue);
                                $timeStatus = trim($span->nodeValue);
                            }
                            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
                        }

                        $checkedAttribute = $td->getAttribute('data-meta');
                        if (strcasecmp(trim($checkedAttribute), 'funding') == 0) {
                            $strongs = $td->getElementsByTagName('strong');
                            foreach ($strongs as $strong) { // only 1 <strong> exists
                                $tempArray['marketplace_amount'] = $this->getMonetaryValue(trim($strong->nodeValue));
                            }

                            $spans = $td->getElementsByTagName('span');
                            foreach ($spans as $span) {  // only 1 <span> exists
                                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($span->nodeValue));
                                if ($tempArray['marketplace_subscriptionProgress'] == 10000) { //Completed
                                    if ($tempArray['marketplace_timeLeftUnit'] == -1) {
                                        //If we dont have time left    
                                        $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                                        $tempArray['marketplace_status'] = CONFIRMED;
                                    } else {
                                        //If we have time left    
                                        $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
                                        $tempArray['marketplace_status'] = PERCENT;
                                    }

                                    foreach ($companyBackup as $inversionBackup) { // If we have the completed inversion with the same status
                                        if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                            $readController++;
                                            $investmentController = true;
                                        }
                                    }
                                } else {
                                    $tempArray['marketplace_statusLiteral'] = 'En proceso';
                                }
                            }
                        }
                    }
                    
                    $investmentNumber++; //Add investment

                    if ($investmentController) { //Don't save a already existing investment
                        unset($tempArray);
                        $investmentController = false;
                    } else {
                        $totalArray[] = $tempArray;
                        $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList, $tempArray);
                        unset($tempArray);
                    }
                }
                
                
            }
            $page++; //Adance page
            if ($readController > 15 || $investmentNumber < 10) {
                $reading = false;
            } //Stop reading
        }
        
        
        if($totalArray){
            $this->print_r2($this->investmentDeletedList);
            $hiddenInvestments = $this->readHiddenInvestment($this->investmentDeletedList);
            echo 'Hidden: ' . SHELL_ENDOFLINE;
            $this->print_r2($hiddenInvestments);
            $totalArray = array_merge($totalArray, $hiddenInvestments);
        }
        
        print_r($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    
    function readHiddenInvestment($investmentDeletedList) {

        $url = array_shift($this->urlSequence);

        $tempArray = array();
        $newTotalArray = array();
        //Read investment info
        foreach ($investmentDeletedList as $loanId) {
            echo "Next Investment Url: " . $url . $loanId . HTML_ENDOFLINE;
            $str = $this->getCompanyWebpage($url . $loanId);
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($str);
            $tempArray['marketplace_country'] = 'ES'; //Loanbook is in spain
            $tempArray['marketplace_loanReference'] = $loanId;

            $title = $this->getElements($dom, 'h1', 'property', 'v:title');

            $tempArray['marketplace_purpose'] = trim($title[0]->nodeValue);

            $info = $this->getElements($dom, 'ul', 'class', 'info');
            $spans = $info[0]->getElementsByTagName('span');
            foreach ($spans as $key => $value) {
                //echo $key . ' value is ' . $value->nodeValue;
                switch ($key) {
                    case 0:
                        $tempArray['marketplace_requestorLocation'] = trim($value->nodeValue);
                        break;
                    case 1:
                        $tempArray['marketplace_sector'] = trim($value->nodeValue);
                        break;
                    /* case 2:
                      $tempArray['marketplace_location'] =  trim($value->nodeValue);
                      break; */
                }
            }
            $tbodys = $dom->getElementsByTagName('tbody');
            $tds = $tbodys[0]->getElementsByTagName('td');

            foreach ($tds as $key2 => $value2) {
                //echo $key2 . ' value is ' . $value2->nodeValue . "\n";
                switch ($key2) {
                    case 0:
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue(trim($value2->nodeValue));
                        break;
                    case 1:
                        $tempArray['marketplace_vencimiento'] = trim($value2->nodeValue);
                        break;
                    case 2:
                        $tempArray['marketplace_interestRate'] = list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($value2->nodeValue);
                        ;
                        break;
                    case 3:
                        $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($value2->nodeValue));
                        break;
                    case 7:
                        $tempArray['marketplace_rating'] = trim($value2->nodeValue);
                        break;
                }
            }

            $progress = $this->getElements($dom, 'div', 'class', 'progress-bar');
            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($progress[0]->getAttribute('style'));
            $tempArray['marketplace_statusLiteral'] = 'Eliminada';
            $tempArray['marketplace_status'] = REJECTED;

            print_r($tempArray);
            $newTotalArray[] = $tempArray;         
        }
        return $newTotalArray;
    }

    /**
     * Collect historical
     * @param Array $structure
     * @param Int $pageNumber
     * @return Array
     */
    function collectHistorical($structure, $pageNumber) {

        $totalArray = array();
        $pageNumber++; //Advance page, we sent 0, growly first page is 1.
        $investmentNumber = 0;

        $url = array_shift($this->urlSequence);

        $str = $this->getCompanyWebpage($url . $pageNumber);  // load Webpage into a string variable so it can be parsed
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $table = $dom->getElementsByTagName('table');
        $tbody = $dom->getElementsByTagName('tbody')[0];

//get all rows from the table

        $rows = array();
        if ($table) {
            $rows = $table->item(0)->getElementsByTagName('tr');
        }
        $index = -1;
        if ($totalArray !== false) {
            foreach ($rows as $key => $row) {

                if ($pageNumber == 1 && $key == 0) { //Compare structures, only compare the first element                  
                     $structureRevision = $this->htmlRevision($structure,'tr',$tbody);
                        if($structureRevision[1]){
                            $totalArray = false; //Stop reading in error        
                            $pageNumber = false;
                            break;
                        }    
                }

                $index++;

                if ($index == 0) {
                    continue;  // don't check contents of first "tr"
                }

                $loanId = preg_replace('/\D/', '', $row->getAttribute('id'));
                $tempArray['marketplace_loanReference'] = $loanId;
                $tempArray['marketplace_country'] = 'ES';
                $tds = $row->getElementsByTagName('td');

                foreach ($tds as $td) {
                    $checkedAttribute = $td->getAttribute('class');
                    if (strcasecmp(trim($checkedAttribute), 'intro') == 0) {
                        $as = $td->getElementsByTagName('a');
                        foreach ($as as $item) {   // only 1 <strong> exists
                            $tempArray['marketplace_purpose'] = $item->nodeValue;
                        }

                        $ps = $td->getElementsByTagName('p');
                        foreach ($ps as $item) {   // only 1 <strong> exists
                            $tempArray['marketplace_vencimiento'] = $item->nodeValue;
                        }
                    }

                    $checkedAttribute = $td->getAttribute('data-meta');
                    if (strcasecmp(trim($checkedAttribute), 'interest') == 0) {
                        $strongs = $td->getElementsByTagName('strong');
                        foreach ($strongs as $strong) { // only 1 <strong> exists
                            $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($strong->nodeValue));
                        }
                    }

                    $checkedAttribute = $td->getAttribute('data-meta');
                    if (strcasecmp(trim($checkedAttribute), 'rating') == 0) {
                        $tempArray['marketplace_rating'] = trim($td->nodeValue);
                    }

                    $checkedAttribute = $td->getAttribute('data-meta');
                    if (strcasecmp(trim($checkedAttribute), 'term') == 0) {

                        $strongs = $td->getElementsByTagName('strong');
                        foreach ($strongs as $strong) { // only 1 <strong> exists
                            if (is_numeric(trim($strong->nodeValue))) {
                                $tempDuration = trim($strong->nodeValue);
                            }
                        }
                        $spans = $td->getElementsByTagName('span');
                        foreach ($spans as $span) { // only 1 <span> exists	
                            $duration = $tempDuration . " " . trim($span->nodeValue);
                            $timeStatus = trim($span->nodeValue);
                        }
                        list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($duration);
                    }

                    $checkedAttribute = $td->getAttribute('data-meta');
                    if (strcasecmp(trim($checkedAttribute), 'time') == 0) {

                        $strongs = $td->getElementsByTagName('strong');
                        foreach ($strongs as $strong) { // only 1 <strong> exists
                            if (is_numeric(trim($strong->nodeValue))) {
                                $tempTimeLeft = trim($strong->nodeValue);
                            }
                        }

                        $spans = $td->getElementsByTagName('span');
                        foreach ($spans as $span) { // only 1 <span> exists	
                            $timeLeft = $tempTimeLeft . " " . trim($span->nodeValue);
                        }
                        list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
                    }

                    $checkedAttribute = $td->getAttribute('data-meta');
                    if (strcasecmp(trim($checkedAttribute), 'funding') == 0) {
                        $strongs = $td->getElementsByTagName('strong');
                        foreach ($strongs as $strong) { // only 1 <strong> exists
                            $tempArray['marketplace_amount'] = $this->getMonetaryValue(trim($strong->nodeValue));
                        }

                        $spans = $td->getElementsByTagName('span');
                        foreach ($spans as $span) {  // only 1 <span> exists
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($span->nodeValue));
                            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {


                                if ($tempArray['marketplace_timeLeftUnit'] == -1) {
                                    //If we dont have time left    
                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                                    $tempArray['marketplace_status'] = CONFIRMED;
                                } else {
                                    //If we have time left    
                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
                                    $tempArray['marketplace_status'] = PERCENT;
                                }
                            } else {
                                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                            }
                        }
                    }
                }

                $totalArray[] = $tempArray;
                unset($tempArray);
                $investmentNumber++;
            }
        }

        echo 'Number of investment in page: ' . $investmentNumber . HTML_ENDOFLINE . SHELL_ENDOFLINE;
        if ($investmentNumber < 10) {
            $pageNumber = false;
        } //Stop reading when we dont find investmet
        return [$totalArray, $pageNumber, null, $structureRevision[0], $structureRevision[2]]; //$pageNumber is the next page, return false if is the last page
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
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                $credentials = array();
                $credentials['login.email'] = $this->user;
                $credentials['login.password'] = $this->password;
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 2:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $lis = $dom->getElementsByTagName('li');
                $this->verifyNodeHasElements($lis);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $resultMyGrowly = false;
                foreach ($lis as $li) {
                    if (strncasecmp(trim($li->nodeValue), "Mi cuenta", 9) == 0) { // Look for words "Mi cuenta"
                        $this->mainPortalPage = $str;
                        $resultMyGrowly = true;
                        break;
                    }
                }

                if (!$resultMyGrowly) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
                    $tracings .= "Growly login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                $dom = new DOMDocument;
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // load Webpage into a string variable so it can be parsed SHOULD SHOW LIST OF INVESTMENTS
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $lists = $this->getElements($dom, "div", "class", "user-area-nav row");

                foreach ($lists as $key => $list) {
                    echo "key = $key " . $list->nodeValue . "<br>";
                }
                if (empty($this->tempArray['global']['myWallet'])) {
                    $spans = $lists[0]->getElementsByTagName('span');
                    foreach ($spans as $key => $span) {
                        echo "Key = $key " . $span->nodeValue . "<br>";
                        if ($key == 0) {
                            $this->tempArray['global']['myWallet'] = $this->getMonetaryValue(trim($span->nodeValue));
                        }
                    }
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $this->print_r2($this->tempArray);
                }
                //echo "STRING = " . $str;
                $projectListTable = $this->getElements($dom, "table", "class", "m-project-list");
                if (count($projectListTable) > 0 ) {
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    
                    /*if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }*/
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    foreach ($projectListTable as $project) {    // Only 1 exists
                        $projectListData = $this->getElements($project, "tr", "class", "toggle-title");
                        foreach ($projectListData as $key => $projectList) {  // Per project
                            $tds = $projectList->getElementsByTagName('td');
                            $this->verifyNodeHasElements($tds);
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__);
                            }
                            echo __FILE__ . " " . __LINE__ . "<br>";
                            // loanId
                            $tempLoanId = $tds[0]->nodeValue;
                            $as = $tds[0]->getElementsByTagName('a');
                            $this->verifyNodeHasElements($as);
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__);
                            }
                            $this->data1[$this->numberOfInvestments]['loanId'] = trim(preg_replace('/\D/', ' ', $as[0]->getAttribute('href')));  // Get decimals of loanId

                            $this->data1[$this->numberOfInvestments]['interest'] = $this->getPercentage($tds[5]->nodeValue);

                            // duration
                            $tempDuration = $tds[6]->nodeValue;
                            $strongs = $tds[6]->getElementsByTagName('strong');
                            $this->verifyNodeHasElements($strongs);
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__);
                            }
                            $spans = $tds[6]->getElementsByTagName('span');
                            $this->verifyNodeHasElements($spans);
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__);
                            }
                            $this->data1[$this->numberOfInvestments]['duration'] = trim($strongs[0]->nodeValue) . " " . trim($spans[0]->nodeValue);
                            $this->data1[$this->numberOfInvestments]['profitGained'] = $this->getMonetaryValue($tds[4]->nodeValue);
                            $this->data1[$this->numberOfInvestments]['invested'] = $this->getMonetaryValue($tds[2]->nodeValue);
                            $this->data1[$this->numberOfInvestments]['pending'] = $this->getMonetaryValue($tds[3]->nodeValue);
    //			$data1[$key]['purpose'] = $tds[1]->nodeValue;			

                            $this->data1[$this->numberOfInvestments]['status'] = OK;
    //		}
                            echo __FILE__ . " " . __LINE__ . "<br>";
                            // deal with amortization table
                            $projectAmortizationData = $this->getElements($project, "tr", "class", "toggle-content"); // only 1 found
                            // convert into table
                            $trs = $projectAmortizationData[$key]->getElementsByTagName('tr');
                            unset($amortizationTable);
                            $mainIndex = -1;
                            foreach ($trs as $key1 => $tr) {
                                $mainIndex = $mainIndex + 1;
                                $subIndex = -1;
                                $tds = $tr->getElementsByTagName('td');
                                foreach ($tds as $td) {
                                    $subIndex = $subIndex + 1;
                                    if ($subIndex == 9) {    // normalize the status, needed for payment calculations
                                        $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState(trim($td->nodeValue));
                                    } else {
                                        $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                                    }
                                }
                            }
                            if (count($amortizationTable) <> 1) {  // if only 1 payment exist, then table contains NO footer
                                array_pop($amortizationTable);  // remove "footer"
                            }

                            $this->print_r2($amortizationTable);

                            // Duration (unit [= meses] is read before)
                            if (count($amortizationTable) == 1) {  // This is valid for loans which have a duration of 3 (4) months
                                $numberOfMonths = 4;     // and just 1 payment at end of period.
                            } else {
                                $numberOfMonths = count($amortizationTable);
                            }
                            $this->data1[$this->numberOfInvestments]['duration'] = $numberOfMonths . " " . "Meses";
                            $this->data1[$this->numberOfInvestments]['commission'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd/mm/yyyy", 1, 6);
                            $this->data1[$this->numberOfInvestments]['date'] = $this->getHighestDateValue($amortizationTable, "dd/mm/yyyy", 1);
                            $this->data1[$this->numberOfInvestments]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd/mm/yyyy", 1, 2);
                            $this->print_r2($this->data1[$this->numberOfInvestments]);

                            $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['activeInInvestments'] + $this->data1[$this->numberOfInvestments]['pending'];
                            $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] + $this->data1[$this->numberOfInvestments]['profitGained'];
                            $this->tempArray['global']['profitibility'] = $this->tempArray['global']['profitibility'] + $this->data1[$this->numberOfInvestments]['interest'];
                            $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$this->numberOfInvestments]['invested'];
                            $this->numberOfInvestments++;
                        }
                    }
                    if (empty($this->tempUrl)) {
                        $this->tempUrl = array_shift($this->urlSequence);
                    }
                    $this->idForSwitch=3;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl . $this->pageNum);
                    $this->pageNum++;
                    break;
                }
                $this->tempArray['global']['investments'] = 0;
                if (!empty($this->numberOfInvestments)) {
                    $this->tempArray['global']['investments'] = $this->numberOfInvestments;
                }
                if (!empty($this->tempArray['global']['profitibility']) && !empty($this->numberOfInvestments)) {
                    $this->tempArray['global']['profitibility'] = $this->tempArray['global']['profitibility'] / ($this->numberOfInvestments);
                }
                else {
                    $this->tempArray['global']['profitibility'] = 0;
                }
                
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->tempArray['investments'] = $this->data1;

                $this->print_r2($this->data1);
                echo "GROW.LY tempArray = ";
                $this->print_r2($this->tempArray);
                return $this->tempArray;
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {
        echo __FILE__ . " " . __LINE__ . "$user, $password<br>";
        $resultMyGrowly = $this->companyUserLogin($user, $password);

        if (!$resultMyGrowly) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
            $tracings .= "Growly login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed SHOULD SHOW LIST OF INVESTMENTS

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $lists = $this->getElements($dom, "div", "class", "user-area-nav row");

        foreach ($lists as $key => $list) {
            echo "key = $key " . $list->nodeValue . "<br>";
        }

        $spans = $lists[0]->getElementsByTagName('span');
        foreach ($spans as $key => $span) {
            echo "Key = $key " . $span->nodeValue . "<br>";
            if ($key == 0) {
                $tempArray['global']['myWallet'] = $this->getMonetaryValue(trim($span->nodeValue));
            }
        }
        echo __FILE__ . " " . __LINE__ . "<br>";
        $this->print_r2($tempArray);

        echo "STRING = " . $str;

        echo __FILE__ . " " . __LINE__ . "<br>";
        $projectListTable = $this->getElements($dom, "table", "class", "m-project-list");
        echo __FILE__ . " " . __LINE__ . "<br>";
        foreach ($projectListTable as $project) {    // Only 1 exists
            $projectListData = $this->getElements($project, "tr", "class", "toggle-title");
            $numberOfInvestments = 0;

            foreach ($projectListData as $key => $projectList) {  // Per project
                $numberOfInvestments = $numberOfInvestments + 1;
                $tds = $projectList->getElementsByTagName('td');
                echo __FILE__ . " " . __LINE__ . "<br>";
                // loanId
                $tempLoanId = $tds[0]->nodeValue;
                $as = $tds[0]->getElementsByTagName('a');
                $data1[$key]['loanId'] = trim(preg_replace('/\D/', ' ', $as[0]->getAttribute('href')));  // Get decimals of loanId

                $data1[$key]['interest'] = $this->getPercentage($tds[5]->nodeValue);

                // duration
                $tempDuration = $tds[6]->nodeValue;
                $strongs = $tds[6]->getElementsByTagName('strong');
                $spans = $tds[6]->getElementsByTagName('span');

                $data1[$key]['duration'] = trim($strongs[0]->nodeValue) . " " . trim($spans[0]->nodeValue);
                $data1[$key]['profitGained'] = $this->getMonetaryValue($tds[4]->nodeValue);
                $data1[$key]['invested'] = $this->getMonetaryValue($tds[2]->nodeValue);
                $data1[$key]['pending'] = $this->getMonetaryValue($tds[3]->nodeValue);
//			$data1[$key]['purpose'] = $tds[1]->nodeValue;			

                $data1[$key]['status'] = OK;
//		}
                echo __FILE__ . " " . __LINE__ . "<br>";
                // deal with amortization table
                $projectAmortizationData = $this->getElements($project, "tr", "class", "toggle-content"); // only 1 found
                // convert into table
                $trs = $projectAmortizationData[$key]->getElementsByTagName('tr');
                unset($amortizationTable);
                $mainIndex = -1;
                foreach ($trs as $key1 => $tr) {
                    $mainIndex = $mainIndex + 1;
                    $subIndex = -1;
                    $tds = $tr->getElementsByTagName('td');
                    foreach ($tds as $td) {
                        $subIndex = $subIndex + 1;
                        if ($subIndex == 9) {    // normalize the status, needed for payment calculations
                            $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState(trim($td->nodeValue));
                        } else {
                            $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                        }
                    }
                }
                if (count($amortizationTable) <> 1) {  // if only 1 payment exist, then table contains NO footer
                    array_pop($amortizationTable);  // remove "footer"
                }

                $this->print_r2($amortizationTable);

// Duration (unit [= meses] is read before)
                if (count($amortizationTable) == 1) {  // This is valid for loans which have a duration of 3 (4) months
                    $numberOfMonths = 4;     // and just 1 payment at end of period.
                } else {
                    $numberOfMonths = count($amortizationTable);
                }
                $data1[$key]['duration'] = $numberOfMonths . " " . "Meses";
                $data1[$key]['commission'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd/mm/yyyy", 1, 6);
                $data1[$key]['date'] = $this->getHighestDateValue($amortizationTable, "dd/mm/yyyy", 1);
                $data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd/mm/yyyy", 1, 2);
                $this->print_r2($data1[$key]);

                $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + $data1[$key]['pending'];
                $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
                $tempArray['global']['profitibility'] = $tempArray['global']['profitibility'] + $data1[$key]['interest'];
                $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
            }
        }
        $tempArray['global']['investments'] = $numberOfInvestments;
        $tempArray['global']['profitibility'] = $tempArray['global']['profitibility'] / ($key + 1);
        echo __FILE__ . " " . __LINE__ . "<br>";
        $tempArray['investments'] = $data1;

        $this->print_r2($data1);
        echo "GROW.LY tempArray = ";
        $this->print_r2($tempArray);
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
    function companyUserLogin($user, $password) {
// user = crismillanmiras@hotmail.com
// pw = Cristi2016
        $str = $this->getCompanyWebpage();  // Go to main site

        $credentials = array();
        $credentials['login.email'] = $user;
        $credentials['login.password'] = $password;

        $str = $this->doCompanyLogin($credentials);  // POST

        $dom = new DOMDocument;
        $dom->loadHTML($str);

        $dom->preserveWhiteSpace = false;
        $lis = $dom->getElementsByTagName('li');

        foreach ($lis as $li) {
            if (strncasecmp(trim($li->nodeValue), "Mi cuenta", 9) == 0) { // Look for words "Mi cuenta"
                $this->mainPortalPage = $str;
                return 1;
                break;
            }
        }
        return 0;
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
     *
     * 	translate the html of loan state to the winvestify normalized state
     * 	@param	string		$str html of loanstate
     * 	@return integer		Normalized state, TERMINATED_OK, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * 	
     */
    function getLoanState($actualState) {

        $loanStates = array("PENDIENTE" => PENDING,
            "DONE" => OK,
            "AMORTIZADO" => TERMINATED_OK,
            "RETRASO" => PAYMENT_DELAYED,
            "JUDICIAL" => DEFAULTED);

        $actualState = trim($actualState);
        foreach ($loanStates as $key => $state) {
            if (strcasecmp($key, $actualState) == 0) {
                return $state;
            }
        }
        return PAYMENT_DELAYED;    // Nothing found so I invent something
    }

    /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {


        //We need remove this attribute directly from the td tag(the father)
        $node1->removeAttribute('class');
        $node1->removeAttribute('id');
        $node1->removeAttribute('style');
        $node2->removeAttribute('class');
        $node2->removeAttribute('id');
        $node2->removeAttribute('style');

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'p'),
                ), array('href', 'src', 'alt', 'title', 'aria-valuenow', 'style'));
        
        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'rel', 'value' => 'tooltip'), 
        ));
        
        
        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'p'),
                ), array('href', 'src', 'alt', 'title', 'aria-valuenow', 'style'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'rel', 'value' => 'tooltip'), 
        ));

        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}

?>
