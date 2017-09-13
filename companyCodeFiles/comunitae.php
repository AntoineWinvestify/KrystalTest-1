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
 * function calculateLoanCost()										[OK, tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * function isNewEntry()											[Not Done]
 * parallelization                                                                                         [OK, tested]
 *
 * 2016-08-29	  version 2016_0.1
 * Basic version
 *
 * 2017-02-16		version 2017_0.1
 *
 * "Error" in collectCompanyMarketplaceData function. The Web of comunitae generates bad html for the
 * ['marketplace_name'] field, it contained  ...CONSTRUCCIONES ALVEDRO SL "SYCA" (extra "") in title		[OK, tested]
 *
 * Added loading of ALL investments
 *
 * 2017-04-15          version 2017_0.2
 * Added modification to collectCompanyMarketplaceData(), added factoring with new for
 *
 * 2017-05-16          version 2017_0.3
 * Added parallelization
 * Added dom verification
 *
 *
 * 2017-07-24          version 2017_0.4
 * Added two urlsequences for marketplaces to get investment of next pages
 * Added code for marketplaces to verify if there is more investments in the next page
 *
 * 2017-08-03
 * Comunitae code adaptation for 100%
 * collectCompanyMarketplaceData - Added pagination loop control
 *  collectHistorical
 * 
 * 2017-08-11 version 2017_0.5
 * Structure Revision added
 * Status definition added
 * Added three urlsequences for userinvestment to get all the user's investment
 * Fixed code to take into account these investments
 *
 * 
 * 2017-09-06
 * Comunitae hidden investment fix
 * 
 * 
 * PENDING:
 * if subscriptionProgress = "finalizado" then write -1 in DB field
 * if (strcasecmp (trim($a->nodeValue), trim($user)) == 0) {   line 112 should also
 *
 *
 * have length indicator
 */
class comunitae extends p2pCompany {

    private $numberOfPages;
    private $index;
    private $random;
    protected $urlListarParticipaciones = null;
    protected $urlVerifyInvestments = null;
    protected $tempUrlInvestments = [];
    protected $numUrlInvestments;
    protected $listParticipation;
    protected $numberPage;
    protected $numMiCartera = 0;
    protected $comunitaeStates = [
        1, //OK
        2, //DUDOSO
        //3 AMORTIZADO
        4, //JUDICIAL
        5, //OK(1-2)
    ];

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
     * Collects the marketplace data.
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) {

        //echo 'Comunitae array loan id: ' .HTML_ENDOFLINE;
        $this->print_r2($loanIdList);
        
        $this->investmentDeletedList = $loanIdList;
        $totalArray = array();
        $subscriptionComplete = false;
        $structureRevision = 1;
        $pageNumber = 1;
        $url = null;
        $urlNextPage = null;
        $urlSequenceNumber = 0;
        $type = 1; // 1 = pagare, comunitae have 'pagares' and 'Factoring'
        for ($i = 0; $i < 2; $i++) {
            $numberOfInvestmentInPage = 0;
            if(empty($url)){
                $urlSequenceNumber++;
            }
            $str = $this->getCompanyWebpage($url);
            
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            if ($i == 0) {
                //pymeList
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                if (empty($url)) {
                    $listing = $dom->getElementById("pymeList");
                    $rows = $listing->getElementsByTagName('article');
                    if (count($listing) == 0) {
                        return [$totalArray, $structureRevision];
                    }
                } else if (!empty($url)) {
                    $rows = $dom->getElementsByTagName('article');
                }

                //$rows = $listing->getElementsByTagName('article');
            } else if ($i == 1) {
                //echo 'factoring url' . $url; 
                //Factoring
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $type = 4; //4 = factoring
                $rows = $dom->getElementsByTagName('article');
            }


            if ($totalArray !== false) { // If we find a structural error, dont read.
                foreach ($rows as $key => $row) {


                    if ($pageNumber == 1 && $key == 0 && $type == 1) { //Compare structures, olny compare the first element
                            $structureRevision = $this->htmlRevision($structure,'article',null,null,null,array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'pymeList'));
                            if($structureRevision[1]){
                                $totalArray = false; //Stop reading in error                         
                                break;
                            }                               
                    }



                    //$this->print_r($row);
                    $loanId = $row->getAttribute('id');
                    if ($loanId == 'collapse_check') {
                        break;
                    }
                    $tempArray['marketplace_loanReference'] = $loanId;


                    $nameFound = false;

                    $as = $row->getElementsByTagName('a');

                    foreach ($as as $a) {
                        $tempValue = $a->nodeValue;

                        $tempArray['marketplace_country'] = 'ES';
                        $tempArray['marketplace_productType'] = $type;
                        if ($nameFound == false) {   // first <a> tag contains the name
                            $tempArray['marketplace_name'] = $tempValue;
                            $tempArray['marketplace_purpose'] = $tempValue;
                            $nameFound = true;
                        }

                        //			if  (strcasecmp( trim($checkedAttribute), trim($tempValue)) == 0) {
                        //				$tempArray['marketplace_name'] = $tempValue;
                        //			}			

                        $checkedAttribute = $a->getAttribute('data-risk');
                        if (!empty(trim($checkedAttribute))) {
                            $tempArray['marketplace_rating'] = trim($a->nodeValue);
                        }

                        $checkedAttribute = $a->getAttribute('title');
                        if (strncasecmp(trim($checkedAttribute), 'Tipo de inter', 12) == 0) {
                            $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($a->nodeValue));
                        }

                        $checkedAttribute = $a->getAttribute('title');
                        if (strncasecmp(trim($checkedAttribute), 'Plazo del pr', 12) == 0) {
                            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($a->nodeValue);
                            //list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($a->nodeValue);
                        }

                        $checkedAttribute = $a->getAttribute('title');
                        if (strncasecmp(trim($checkedAttribute), 'Importe pr', 10) == 0) {
                            $tempArray['marketplace_amount'] = $this->getMonetaryValue($a->nodeValue);
                        }
                    }

                    $spans = $row->getElementsByTagName('span');
                    foreach ($spans as $span) {
                        $checkedAttribute = $span->getAttribute('class');
                        if (strcasecmp(trim($checkedAttribute), 'center-percentage') == 0) {

                            if (stristr(trim($span->nodeValue), "%") == true) {
                                //echo "Comunitae: % found, so store in marketplace" . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($span->nodeValue);
                                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                            } else {
                                $tempArray['marketplace_subscriptionProgress'] = 10000;  // completed, retrasado orr amortización ..
                                $tempArray['marketplace_statusLiteral'] = 'Completado';
                                $tempArray['marketplace_status'] = PERCENT;
                                foreach ($companyBackup as $inversionBackup) {
                                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                        $subscriptionComplete = true;
                                    }
                                }
                            }
                        }
                    }



                    if ($tempArray) {
                        $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList,$tempArray);       
                        $totalArray[] = $tempArray;
                        unset($tempArray);
                        $numberOfInvestmentInPage++;
                    }
                    unset($tempArray);

                    
                    //echo 'Investment' . $numberOfInvestmentInPage;
                    //If subscription of the investment is not complete and the number of investment is the 15th in the page
                    //We need to go to the next page to verify if there are investments or not
                    if (!$subscriptionComplete && $numberOfInvestmentInPage == 15) {
                        if (empty($urlNextPage)) {
                            $urlNextPage = array_shift($this->urlSequence);
                            $urlSequenceNumber++;
                        }
                        $numberOfInvestmentInPage = 0;
                        $pageNumber++;
                        $url = $urlNextPage . $pageNumber;
                        $i--;
                    } else if ($subscriptionComplete) {
                        if ($pageNumber == 1 && $i == 0) {
                            array_shift($this->urlSequence);
                        }
                        $subscriptionComplete = false;
                        $url = null;
                        $urlNextPage = null;
                        $pageNumber = 1;
                        $numberOfInvestmentInPage = 0;
                        break;
                    }
                }
            }
        }
        
        for($urlSequenceNumber ;$urlSequenceNumber < 4;$urlSequenceNumber++){ //Fix url sequence error. Factoring only have one page yet.
            array_shift($this->urlSequence);
        }
        
        /*echo 'Final loan id array: ' . HTML_ENDOFLINE;     
        $this->print_r2($this->investmentDeletedList);*/
        
        $hiddenInvestments = $this->readHiddenInvestment($this->investmentDeletedList);
        //$this->print_r2($hiddenInvestments);
        $totalArray = array_merge($totalArray,$hiddenInvestments); //Add hidden investent in the array
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
    }

    
    /**Read hidden investment.
     * 
     * @param array $investmentDeletedList loan id list
     * @return array investments info list
     */
    function readHiddenInvestment ($investmentDeletedList){
        
        $login = $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);
        
         if (!$login) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
            $tracings .= "Comunitae login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            //echo 'Login fail';
            return $this->getError(__LINE__, __FILE__);
        }
        
        
        $url = array_shift($this->urlSequence);
        
        $tempArray = array();
        $newTotalArray = array();
        //Read investment info
        foreach($investmentDeletedList as $loanId) {
            $str = $this->getCompanyWebpage($url . $loanId . '?np=1');
            /*echo 'Investment page : '  .  HTML_ENDOFLINE;
            echo $url . $loanId . '?np=1' .  HTML_ENDOFLINE;
            echo $str;*/
            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;
            
            $tempArray['marketplace_loanReference'] = $loanId;
            $tempArray['marketplace_country'] = 'ES';
            //echo 'Sections: ' . HTML_ENDOFLINE;
            $sections = $dom->getElementsByTagName('section');
            
            foreach($sections as $key=>$section){
                //echo 'Section ' . $key .":" . HTML_ENDOFLINE;
                //echo '  divs: ' . HTML_ENDOFLINE;
                switch($key){
                    case 2:
                        $divs = $section->getElementsByTagName('div');
                        //foreach($divs as $keyDiv => $div){
                            //echo '  div ' . $keyDiv . $div->nodeValue . HTML_ENDOFLINE;
                            $tempArray['marketplace_name'] = trim($divs[2]->nodeValue);
                            $tempArray['marketplace_purpose'] = trim($divs[2]->nodeValue);
                            $tempArray['marketplace_rating'] = trim($divs[11]->nodeValue);
                            $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($divs[12]->nodeValue));
                            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue(trim($divs[14]->nodeValue));
                            $tempArray['marketplace_amount'] = $this->getMonetaryValue(trim($divs[16]->nodeValue));
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($divs[19]->nodeValue));
                            if($tempArray['marketplace_subscriptionProgress'] == 10000){
                                $tempArray['marketplace_statusLiteral'] = 'Completado';
                                $tempArray['marketplace_status'] = PERCENT;
                            }else{
                                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                                $tempArray['marketplace_status'] = null;
                            }
                        //}
                        break;            
                }
               
               
            } 
            
            $newTotalArray[] = $tempArray;
            unset($tempArray);
        }     
            
        //$this->print_r2($newTotalArray);
        $this->companyUserLogout();
        return $newTotalArray; //Return hidden investments info in an array
    }
    
    /**
     * collect all investment
     * @param Array $structure
     * @param Int $page
     * @param Int $type
     * @return Array
     */
    function collectHistorical($structure, $pageNumber, $type) {

        $totalArray = array();
        if ($pageNumber == 0) {
            $pageNumber++;
        } //Advance page, $pageNumber start with 0

        if ($type == 1) {//Start with 'Pagares'
            if ($pageNumber == 1) {
                $url = array_shift($url); //Save 'Pagares' first url
                echo 'Url: ' . $url . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                $str = $this->getCompanyWebpage($url);
                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                //echo $str;
                $pagares = $this->getElements($dom, 'div', 'data-id', '4');
                //$this->print_r2($pagares);
                $rows = $pagares[1]->getElementsByTagName('article');
            } else {
                array_shift($this->urlSequence); //skip 'Pagares' first url
                $url = array_shift($this->urlSequence);  //save 'Pagares' pagination url
                echo 'Url: ' . $url . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                $str = $this->getCompanyWebpage($url . $pageNumber);

                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $rows = $dom->getElementsByTagName('article');
            }

            $pageNumber++; //Advance page
            $numberOfInvestmentInPage = $rows->length;
            //$this->print_r2($rows);
            echo 'Count: ' . $numberOfInvestmentInPage . HTML_ENDOFLINE . SHELL_ENDOFLINE;

            if ($numberOfInvestmentInPage == 0) { //When we don't find ivestment in 'pagares', go to 'Factoring'
                echo 'Change type' . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                $type = 4;
                $pageNumber = 1; //MUST BE 1, IF IS 0 THE LOOP WILL END
                //print_r($type . " " . $pageNumber);
            }
        } else if ($type == 4) { //Next 'Factoring'
            echo 'Enter factoring' . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            //Factoring
            if ($pageNumber == 1) {
                array_shift($this->urlSequence); //Skip 'Pagares' first url
                array_shift($this->urlSequence); //Skip 'Pagares' pagination url
                $url = array_shift($this->urlSequence); //Save 'Factoring' first url
                echo 'Url: ' . $url;
                $str = $this->getCompanyWebpage($url);
                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $factoring = $this->getElements($dom, 'div', 'data-id', '6');
                $rows = $factoring[0]->getElementsByTagName('article');
            } else {
                array_shift($this->urlSequence); //Skip 'Pagares' first url
                array_shift($this->urlSequence); //Skip 'Pagares' pagination url
                array_shift($this->urlSequence); //Skip 'Factoring First page' url
                $url = array_shift($this->urlSequence); //Save 'Factoring' url
                echo 'Url: ' . $url;
                $str = $this->getCompanyWebpage($url . $pageNumber);
                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $rows = $dom->getElementsByTagName('article');
            }

            $pageNumber++; //Advance page         
            $numberOfInvestmentInPage = $rows->length;
            //$this->print_r2($rows);
            echo 'Count: ' . $numberOfInvestmentInPage . HTML_ENDOFLINE . SHELL_ENDOFLINE;

            if ($numberOfInvestmentInPage == 0) { //When we don't find ivestment in 'Factoring', stop search
                $pageNumber = false;
            }
        }

        if ($totalArray !== false && $pageNumber) { // If we find a structural error, dont read.
            foreach ($rows as $key => $row) {
                
                if ($pageNumber == 1 && $key == 0 && $type == 1) { //Compare structures, olny compare the first element
                    $structureRevision = $this->htmlRevision($structure,'article',null,null,null,array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'pymeList'));
                    if($structureRevision[1]){
                        $totalArray = false; //Stop reading in error      
                        $pageNumber = false;
                        break;
                    }                               
                }
                
                
                echo 'Begining read: <br>';
                $investmentNumberControler = 0;

                if ($pageNumber == 2 && $key == 0 && $structure && $type == 1) { //Compare structures, olny compare the first element
                    $newStructure = new DOMDocument; //Load db html dom
                    $newStructure->loadHTML($structure['Structure']['structure_html']);
                    $newStructure->preserveWhiteSpace = false;
                    $trsNewStructure = $newStructure->getElementsByTagName('article');
                    $structureRevision = $this->structureRevision($trsNewStructure[1], $rows[2]);

                    echo 'structure: ' . $structureRevision . HTML_ENDOFLINE . SHELL_ENDOFLINE;

                    if (!$structureRevision) { //Save new structure
                        echo 'Structural error' . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                        $saveStructure = new DOMDocument();
                        $container = $this->getElements($dom, 'div', 'id', 'pymeList');
                        $clone = $container[0]->cloneNode(TRUE);
                        $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));

                        $structureRevision = $saveStructure->saveHTML();
                        $totalArray = false;  //Structure control, don't read more tr 

                        break; //Stop reading if we have a structural error
                    }
                    echo 'Structure good' . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                }

                if ($pageNumber == 2 && $key == 0 && !$structure && $type == 1) { //Save new structure if is first time
                    echo 'no structure readed, saving structure' . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                    $saveStructure = new DOMDocument();
                    $container = $this->getElements($dom, 'div', 'id', 'pymeList');
                    $clone = $container[0]->cloneNode(TRUE);
                    $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
                    $structureRevision = $saveStructure->saveHTML();
                }



                $loanId = $row->getAttribute('id');
                $tempArray['marketplace_loanReference'] = $loanId;
                $nameFound = false;

                $as = $row->getElementsByTagName('a');

                foreach ($as as $a) {
                    $tempValue = $a->nodeValue;

                    $tempArray['marketplace_country'] = 'ES';
                    $tempArray['marketplace_productType'] = $type;
                    if ($nameFound == false) {   // first <a> tag contains the name
                        $tempArray['marketplace_name'] = $tempValue;
                        $tempArray['marketplace_purpose'] = $tempValue;
                        $nameFound = true;
                    }

                    $checkedAttribute = $a->getAttribute('data-risk');
                    if (!empty(trim($checkedAttribute))) {
                        $tempArray['marketplace_rating'] = trim($a->nodeValue);
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Tipo de inter', 12) == 0) {
                        $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($a->nodeValue));
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Plazo del pr', 12) == 0) {
                        list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($a->nodeValue);
                        //list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($a->nodeValue);
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Importe pr', 10) == 0) {
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue($a->nodeValue);
                    }
                }

                $spans = $row->getElementsByTagName('span');
                foreach ($spans as $span) {
                    $checkedAttribute = $span->getAttribute('class');
                    if (strcasecmp(trim($checkedAttribute), 'center-percentage') == 0) {

                        if (stristr(trim($span->nodeValue), "%") == true) {
                            echo "Comunitae: % found, so store in marketplace" . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($span->nodeValue);
                            $tempArray['marketplace_statusLiteral'] = 'En proceso';
                        } else {
                            $tempArray['marketplace_subscriptionProgress'] = 10000;  // completed, retrasado orr amortización ..
                            $tempArray['marketplace_statusLiteral'] = 'Completado';
                            $tempArray['marketplace_status'] = PERCENT;
                        }
                    }
                }
                $investmentNumberControler++;
                echo 'Temp array: ';
                print_r($tempArray);
                $totalArray[] = $tempArray;

                unset($tempArray);
            }
        }   
        return [$totalArray, $pageNumber, $type, $structureRevision[0], $structureRevision[2]]; //Return an array and the page number, $pageNumber = false when we want end the loop
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
// user = "antoine@winvestify.com"
// pw = "Zastac2015"
// manoloherrero@msn.com    Mecano1980E
//$configurationParameters = array('appDebug' => true);
//$this->defineConfigParms($configurationParameters);

        switch ($this->idForSwitch) {
            case 0:
                $credentials['j_username'] = $this->user;
                $credentials['j_password'] = $this->password;
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //wwww.comunitae.com/j_spring_security_check
                break;
            case 1:
                // Check if user actually has entered the portal of the company.
                // by means of checking of 2 unique identifiers of the portal
                // This should be done by checking a field in the Webpage (button, link etc)
                // and the email of the user (if aplicable)
                $dom = new DOMDocument;
                libxml_use_internal_errors(true); //Use it when there is an error on the HTML coming from cURL
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $confirm = 0;
                $uls = $dom->getElementsByTagName('ul');
                $this->verifyNodeHasElements($uls);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($uls as $ul) {
                    $as = $ul->getElementsByTagName('a');
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    foreach ($as as $a) {
                        if (strcasecmp(trim($a->nodeValue), trim($this->user)) == 0) {
                            $confirm++;
                            break 2;
                        }
                    }
                }

                $as = $dom->getElementsByTagName('a');
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($as as $a) {
                    if (strncasecmp(trim($a->getAttribute('href')), "/mi-posicion", 12) === 0) {
                        $confirm++;
                        break;
                    }
                }
                $resultComunitae = 0;
                if ($confirm > 0) {
                    $this->mainPortalPage = $str;
                    $resultComunitae = 1;
                }
                if (!$resultComunitae) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
                    $tracings .= "Comunitae login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";
                $dom = new DOMDocument;
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // wwww.comunitae.com/miPosicion.html?method=Posicion load Webpage into a string variable so it can be parsed
                break;

            case 2:
                //echo $str;
                $this->random = rand(111, 900);
                $url = array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . time() . $this->random); // https://www.comunitae.com/pastillaCuenta.html?aleat=1487321358524	
                break;
            case 3:
                echo __FILE__ . " " . __LINE__ . "<br>";
                //echo $str;
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $accountSummarys = $this->getElements($dom, "div", "id", "pastillaCuenta_10");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $items = $this->getElements($accountSummarys[0], "span", "class", "list-group-item");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $spans = $this->getElements($items[2], "span", "class", "badge");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($spans[0]->nodeValue);

                $this->random++;
                $url = array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . time() . $this->random); // https://www.comunitae.com/pastillaCartera.html?aleat=
                break;
            case 4:
                $this->numberOfInvestments = 0;
                //We save the url that verify that there are investments on the "mi_cartera" link
                $this->urlVerifyInvestments = array_shift($this->urlSequence);
                //We save the url to save if there are investments
                $this->urlListarParticipaciones = array_shift($this->urlSequence);
                $url = array_shift($this->urlSequence);
                $this->numMiCartera++;
                $this->idForSwitch++;
                // We must go to this four url to take all the investment of a inversor
                // https://www.comunitae.com/mi_cartera/personales
                // https://www.comunitae.com/mi_cartera/pagares
                // https://www.comunitae.com/mi_cartera/confirming
                // https://www.comunitae.com/mi_cartera/factoring
                $this->getCompanyWebpageMultiCurl($url);
                break;
            case 5:
                $this->random = rand(111, 900);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->urlVerifyInvestments . time() . $this->random); //https://www.comunitae.com/listarParticipaciones.html?method=mostrarBloques&_=1502374083649
                break;
            case 6:
                //echo $str;
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                //echo $str;
                $dom->preserveWhiteSpace = false;
                // We verify if there is no investment on the url listarParticipaciones.html?method=mostrarBloques
                // It will appear a message with "No hay inversiones"
                $sections = $dom->getElementsByTagName("section");
                $emptyInvestments = true;
                if ($sections->length == 0) {
                    $articles = $dom->getElementsByTagName("article");
                    $i = 1;
                    $this->listParticipation = 0;
                    $this->numberPage = 1;
                    foreach ($articles as $article) {
                        $spans = $article->getElementsByTagName("span");
                        $classSpan = $spans[0]->getAttribute('class');
                        $pos = strpos($classSpan, 'label-state-');
                        if ($pos !== false) {
                            // " found after position 20
                            $stateInvestments = substr($classSpan, $pos + 12, 1);
                            if (in_array($stateInvestments, $this->comunitaeStates)) {
                                $this->tempUrlInvestments[] = $url = $this->urlListarParticipaciones . $i . "&PARAM_PAGINACION_PAGINA_ACTUAL=";
                            }
                            $i++;
                        }
                    }
                    $this->numUrlInvestments = 0;
                    if (!empty($this->tempUrlInvestments)) {
                        $this->numUrlInvestments = count($this->tempUrlInvestments);
                    }
                    if ($this->numUrlInvestments > 0) {
                        $emptyInvestments = false;
                        $url = $this->tempUrlInvestments[$this->listParticipation] . $this->numberPage;
                        $this->numberPage++;
                        $this->listParticipation++;
                        $this->idForSwitch++;
                        $this->getCompanyWebpageMultiCurl($url);
                    }
                    //$url = array_shift($this->urlSequence);  // https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=1&PARAM_PAGINACION_PAGINA_ACTUAL=
                }
                if ($emptyInvestments) {
                    if ($this->numMiCartera < 4) {
                        $url = array_shift($this->urlSequence);
                        $this->numMiCartera++;
                        $this->idForSwitch = 5;
                        // https://www.comunitae.com/mi_cartera/pagares
                        // https://www.comunitae.com/mi_cartera/personales
                        // https://www.comunitae.com/mi_cartera/confirming
                        // https://www.comunitae.com/mi_cartera/factoring
                        $this->getCompanyWebpageMultiCurl($url);
                    } else {
                        $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['totalInvestment'] - $this->tempArray['global']['totalAmortized'];
                        $this->tempArray['global']['profitibility'] = (int) ($this->tempArray['global']['totalPercentage'] / ($this->tempArray['global']['investments'] = $this->numberOfInvestments));
                        $this->print_r2($this->tempArray);
                        ///FIX THIS
                        $this->tempArray['global']['investments'] = $this->numberOfInvestments;

                        $this->tempArray['investments'] = $this->data1;
                        //	$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + $data1[$key]['amortized'];
                        //	$tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$key]['invested'];
                        return $this->tempArray;
                    }
                }
                break;
            case 7:
                //echo "KKKKKKKKK" . $str;
                $domAccount = new DOMDocument;
                libxml_use_internal_errors(true);
                $domAccount->loadHTML($str);
                $domAccount->preserveWhiteSpace = false;
                $accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->index = -1;
                foreach ($accountSummaries as $key => $account) {
                    $investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $as = $this->getElements($investmentInfos[0], "a");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $tempStatus = $this->getLoanState($as[0]->getAttribute("title")); // status of actual investment
                    if ($tempStatus == TERMINATED_OK) {
                        continue;         // skip this one as investment has finished
                    }
                    switch ($tempStatus) {
                        case 2:
                            $tempStatus = 1;
                            break;
                        case 3:
                            $tempStatus = 1;
                            break;
                    }

                    $this->index++;
                    //Changed index for numberOfInvestments variable because there are different investments on different urls
                    //https://www.comunitae.com/mi_cartera/.......
                    $this->data1[$this->numberOfInvestments]['status'] = $tempStatus; // status of actual investment
                    $this->data1[$this->numberOfInvestments]['loanId'] = trim($investmentInfos[1]->nodeValue);
                    $this->data1[$this->numberOfInvestments]['name'] = trim($investmentInfos[2]->nodeValue);
                    $tempData = explode("-", $investmentInfos[4]->nodeValue);
                    $this->data1[$this->numberOfInvestments]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
                    $as = $this->getElements($investmentInfos[3], "a");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $duration = $this->getDurationValue($as[2]->nodeValue);
                    $this->data1[$this->numberOfInvestments]['durationUnit'] = $duration[1];
                    switch ($duration[1]) {
                        case 1: $this->data1[$this->numberOfInvestments]['duration'] = $duration[0] . " Días";
                            break;
                        case 2: $this->data1[$this->numberOfInvestments]['duration'] = $duration[0] . " Meses";
                            break;
                    }
                    $this->data1[$this->numberOfInvestments]['interest'] = $this->getPercentage($as[1]->nodeValue);
                    $as = $this->getElements($investmentInfos[4], "a");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $spans = $this->getElements($investmentInfos[5], "span");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $this->data1[$this->numberOfInvestments]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
                    $as = $this->getElements($investmentInfos[0], "a");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] +
                            $this->data1[$this->numberOfInvestments]['profitGained'];
                    $this->tempArray['global']['totalAmortized'] = $this->tempArray['global']['totalAmortized'] + $this->data1[$this->numberOfInvestments]['amortized'];
                    $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$this->numberOfInvestments]['invested'];
                    $this->tempArray['global']['totalPercentage'] = $this->tempArray['global']['totalPercentage'] + $this->data1[$this->numberOfInvestments]['interest'];
                    $this->numberOfInvestments++;
                }

                $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['totalInvested'] - $this->tempArray['global']['totalAmortized'];
                $this->print_r2($this->data1);
                // Check number of pages. It seems the investments are shown in pages of 15 at the time.
                $pages = $this->getElements($domAccount, "ul", "class", "pagination no-margin");
                echo __FILE__ . " " . __LINE__ . "<br>";
                if (empty($pages)) {
                    $this->numberOfPages = 1;
                } else {
                    $as = $this->getElements($pages[0], "a");
                    $this->numberOfPages = count($as);
                }
                echo __FILE__ . " " . __LINE__ . "number of pages = $this->numberOfPages<br>";
                //$numberOfPages = 0;
                if ($this->numberOfPages > 1 && $this->numberPage <= $this->numberOfPages) {
                    $url = $this->tempUrlInvestments[$this->listParticipation] . $this->numberPage;
                    $this->numberPage++;
                    $this->idForSwitch = 7;
                    $this->getCompanyWebpageMultiCurl($url);
                    break;
                } else {
                    if ($this->numUrlInvestments > $this->listParticipation) {
                        $emptyInvestments = false;
                        $this->numberPage = 1;
                        $url = $this->tempUrlInvestments[$this->listParticipation] . $this->numberPage;
                        $this->listParticipation++;
                        $this->numberPage++;
                        $this->idForSwitch = 7;
                        $this->getCompanyWebpageMultiCurl($url);
                    } else if ($this->numMiCartera < 4) {
                        $url = array_shift($this->urlSequence);
                        $this->numMiCartera++;
                        $this->idForSwitch = 5;
                        // https://www.comunitae.com/mi_cartera/personales
                        // https://www.comunitae.com/mi_cartera/pagares
                        // https://www.comunitae.com/mi_cartera/confirming
                        // https://www.comunitae.com/mi_cartera/factoring
                        $this->getCompanyWebpageMultiCurl($url);
                    } else {
                        $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['totalInvestment'] - $this->tempArray['global']['totalAmortized'];
                        $this->tempArray['global']['profitibility'] = (int) ($this->tempArray['global']['totalPercentage'] / ($this->tempArray['global']['investments'] = $this->numberOfInvestments));
                        $this->print_r2($this->tempArray);
                        ///FIX THIS
                        $this->tempArray['global']['investments'] = $this->numberOfInvestments;

                        $this->tempArray['investments'] = $this->data1;
                        //	$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + $data1[$key]['amortized'];
                        //	$tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$key]['invested'];
                        return $this->tempArray;
                    }
                }
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	NOT READY
     */
    function collectUserInvestmentData($user, $password) {
// user = "antoine@winvestify.com"
// pw = "Zastac2015"
// manoloherrero@msn.com    Mecano1980E
//$configurationParameters = array('appDebug' => true);
//$this->defineConfigParms($configurationParameters);

        $resultComunitae = $this->companyUserLogin($user, $password);
        if (!$resultComunitae) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
            $tracings .= "Comunitae login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        echo __FILE__ . " " . __LINE__ . "<br>";
        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed
        echo $str;
        $random = rand(111, 900);
        $url = array_shift($this->urlSequence);
        $str = $this->getCompanyWebpage($url . time() . $random); // https://www.comunitae.com/pastillaCuenta.html?aleat=1487321358524	
        echo __FILE__ . " " . __LINE__ . "<br>";
        echo $str;
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $accountSummarys = $this->getElements($dom, "div", "id", "pastillaCuenta_10");
        $items = $this->getElements($accountSummarys[0], "span", "class", "list-group-item");
        $spans = $this->getElements($items[2], "span", "class", "badge");

        $random = $random + 1;
        $url = array_shift($this->urlSequence);
        $str = $this->getCompanyWebpage($url . time() . $random); // https://www.comunitae.com/www.comunitae.com/mi-cartera/pagares

        $random = $random + 1;
        $url = array_shift($this->urlSequence);
        $str = $this->getCompanyWebpage($url);   // https://www.comunitae.com/listarParticipaciones.html?method=mostrarBloques&_=1487322997347 

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $spanwa = $this->getElements($dom, "span", "class", "text-muted pull-right");
        $tempArray['global']['myWallet'] = $this->getMonetaryValue($spanwa[0]->nodeValue);


        $random = $random + 1;
        $url = array_shift($this->urlSequence);  // https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=1&PARAM_PAGINACION_PAGINA_ACTUAL=
        $str = $this->getCompanyWebpage($url . "1");

        $domAccount = new DOMDocument;
        $domAccount->loadHTML($str);
        $domAccount->preserveWhiteSpace = false;
        $accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
        $index = -1;
        foreach ($accountSummaries as $key => $account) {
            $investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
            $as = $this->getElements($investmentInfos[0], "a");
            $tempStatus = $this->getLoanState($as[0]->getAttribute("title")); // status of actual investment
            if ($tempStatus == TERMINATED_OK) {
                continue;         // skip this one as investment has finished
            }

            $index = $index + 1;
            $data1[$index]['status'] = $tempStatus; // status of actual investment
            $data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
            $data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
            $tempData = explode("-", $investmentInfos[4]->nodeValue);
            $data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
            $as = $this->getElements($investmentInfos[3], "a");
            $data1[$index]['duration'] = filter_var($as[2]->nodeValue, FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
            $data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);
            $as = $this->getElements($investmentInfos[4], "a");
            $spans = $this->getElements($investmentInfos[5], "span");
            $data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
            $as = $this->getElements($investmentInfos[0], "a");
            $data1[$index]['status'] = $this->getLoanState($as[0]->getAttribute("title")); // status of actual investment
            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
            $tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];
            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];
            $tempArray['global']['totalPercentage'] = $tempArray['global']['totalPercentage'] + $data1[$index]['interest'];
        }

        $tempArray['global']['activeInInvestments'] = $tempArray['global']['totalInvested'] - $tempArray['global']['totalAmortized'];
        $this->print_r2($data1);
// Check number of pages. It seems the investments are shown in pages of 15 at the time.
        $pages = $this->getElements($domAccount, "ul", "class", "pagination no-margin");
        echo __FILE__ . " " . __LINE__ . "<br>";
        if (empty($pages)) {
            $numberOfPages = 1;
        } else {
            $as = $this->getElements($pages[0], "a");
            $numberOfPages = count($as);
        }
        echo __FILE__ . " " . __LINE__ . "$number of pages = $numberOfPages<br>";
        if ($numberOfPages > 1) {
            for ($i = 2; $i <= $numberOfPages; $i++) {
                $str = $this->getCompanyWebpage($url . $i); // https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=2&PARAM_PAGINACION_PAGINA_ACTUAL=
                $domAccount = new DOMDocument;
                $domAccount->loadHTML($str);
                $domAccount->preserveWhiteSpace = false;
                $accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");

                foreach ($accountSummaries as $key => $account) {
                    $index = $index + 1;
                    $investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
                    //			foreach ($investmentInfos as $summary){
                    $data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
                    $data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
                    $tempData = explode("-", $investmentInfos[4]->nodeValue);
                    $data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
                    $as = $this->getElements($investmentInfos[3], "a");
                    $data1[$index]['duration'] = filter_var($as[2]->nodeValue, FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
                    $data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);
                    $as = $this->getElements($investmentInfos[4], "a");
                    $spans = $this->getElements($investmentInfos[5], "span");
                    $data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
                    $as = $this->getElements($investmentInfos[0], "a");
                    $data1[$index]['status'] = $this->getLoanState($as[0]->getAttribute("title")); // status of actual investment
                    //			}
                    $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
                            $data1[$key]['profitGained'];
                    $tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];
                    $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];
                    $tempArray['global']['totalPercentage'] = $tempArray['global']['totalPercentage'] + $data1[$index]['interest'];
                }
            }
        }
        $tempArray['global']['activeInInvestments'] = $tempArray['global']['totalInvestment'] - $tempArray['global']['totalAmortized'];
        $tempArray['global']['profitibility'] = (int) ($tempArray['global']['totalPercentage'] / ($tempArray['global']['investments'] = $index + 1));
        $this->print_r2($tempArray);

//$this->getElements($investmentInfos[15], "span");	// 	Generate Error
//=================================================================================
        /* 		
          // Get the investments which DEFAULTED  NOT YET CORRECT, first page
          $random = $random + 1;
          $url = array_shift($this->urlSequence);
          echo __FILE__ . " " . __LINE__  . " 666<br>";
          $str = $this->getCompanyWebpage($url . "1");
          //echo "666 " . $str;
          $domAccount = new DOMDocument;
          $domAccount->loadHTML($str);
          $domAccount->preserveWhiteSpace = false;
          $accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");

          foreach ($accountSummaries as $key1 => $account) {
          $index = $index + 1;
          $investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
          foreach ($investmentInfos as $summary){
          $data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
          $data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
          $tempData = explode("-", $investmentInfos[4]->nodeValue);
          $data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
          $as = $this->getElements($investmentInfos[3], "a");
          $data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
          $data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);
          $as = $this->getElements($investmentInfos[4], "a");
          $spans = $this->getElements($investmentInfos[5], "span");
          $data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
          $data1[$index]['status'] = PENDING;

          $tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];
          $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];
          $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments']
          + $data1[$index]['invested'] - $data1[$index]['amortized'];
          }
          }



          // Check number of pages. It seems the investments are shown in pages of 15 at the time.
          $pages = $this->getElements($domAccount, "ul", "class", "pagination no-margin");
          $as = $this->getElements($pages[0], "a");
          $numberOfPages = count($as);

          if ($numberOfPages > 1) {
          for ($i = 2; $i <= $numberOfPages; $i++)  {
          $str = $this->getCompanyWebpage($url . $i);	// https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=2&PARAM_PAGINACION_PAGINA_ACTUAL=
          $domAccount = new DOMDocument;
          $domAccount->loadHTML($str);
          $domAccount->preserveWhiteSpace = false;
          $accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
          $tempArray['global']['totalInvested'] = 0;

          foreach ($accountSummaries as $key => $account) {
          $index = $index + 1;
          $investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
          foreach ($investmentInfos as $summary){
          $data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
          $data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
          $tempData = explode("-", $investmentInfos[4]->nodeValue);
          $data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
          $as = $this->getElements($investmentInfos[3], "a");
          $data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
          $data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);
          $as = $this->getElements($investmentInfos[4], "a");
          $spans = $this->getElements($investmentInfos[5], "span");
          $data1[$index]['investment'] = $this->getMonetaryValue($spans[0]->nodeValue);
          $data1[$index]['status'] = PENDING;

          $tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];
          $tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$index]['invested'];
          $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments']
          + $data1[$index]['invested'] - $data1[$index]['amortized'];
          }
          }
          }
          }
         */

        $tempArray['global']['investments'] = $index + 1;
        $tempArray['investments'] = $data1;

//	$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + $data1[$key]['amortized'];
//	$tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$key]['invested'];

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

        $credentials['j_username'] = $user;
        $credentials['j_password'] = $password;

        $str = $this->doCompanyLogin($credentials);

// Check if user actually has entered the portal of the company.
// by means of checking of 2 unique identifiers of the portal
// This should be done by checking a field in the Webpage (button, link etc)
// and the email of the user (if aplicable)
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $confirm = 0;
        $uls = $dom->getElementsByTagName('ul');
        foreach ($uls as $ul) {
            $as = $ul->getElementsByTagName('a');
            foreach ($as as $a) {
                if (strcasecmp(trim($a->nodeValue), trim($user)) == 0) {
                    $confirm++;
                    break 2;
                }
            }
        }

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            if (strncasecmp(trim($a->getAttribute('href')), "/mi-posicion", 12) === 0) {
                $confirm++;
                break;
            }
        }
        if ($confirm > 0) {
            $this->mainPortalPage = $str;
            return 1;
        }

        return 0;
    }

    /**
     *
     * 	Logout of user from to company portal.
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
     * 	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * 	only OK is considered so far
     */
    function getLoanState($actualState) {

        $loanStates = array("PENDIENTE" => PENDING,
            "OK" => OK,
            "AMORTIZADO" => TERMINATED_OK,
            "ATRASADO" => PAYMENT_DELAYED,
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

        //We need remove this attribute directly from the article tag

        $node1->removeAttribute('data-href');
        $node1->removeAttribute('id');

        $node2->removeAttribute('data-href');
        $node2->removeAttribute('id');



        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('title', 'data-risk', 'id', 'style', 'aria-valuenow', 'data-toggle', 'data-placement'));


        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'col-xs-5'),
        ));

        $node1 = $this->cleanDom($node1, array(//We only want delete class of the span tag, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'span'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'article'),
                ), array('class'));


        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('title', 'data-risk', 'id', 'style', 'aria-valuenow', 'data-toggle', 'data-placement'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'col-xs-5'),
        ));

        $node2 = $this->cleanDom($node2, array(//We only want delete class of the span tag, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'span'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'article'),
                ), array('class'));



        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}

?>
