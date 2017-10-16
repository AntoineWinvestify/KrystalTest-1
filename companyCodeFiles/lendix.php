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
 * Contains the code required for accessing the website of "lendinx.com"
 *
 *
 * 2017-04-12	  version 2017_0.3
 * Updated according to new structure of web of Lendix
 *
 * function calculateLoanCost()										[Not OK, not tested]
 * function collectCompanyMarketplaceData(): write the same value in the fields       			[OK, tested]
 * $tempArray['marketplace_purpose'] and $tempArray['marketplace_name']
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * function companyUserLogout()                                                                            [OK, tested]
 * parallelization                                                                                         [OK, tested]
 *
 * 2016-11-06	  version 2016_0.2
 * Updated according to new structure of web of Lendix
 *
 * 2017-04-24
 * collectUserInvestmentData fixed partial
 *
 * 2017-04-24
 * collectUserInvestmentData fixed total
 *
 * 2017-05-16      version 2017_0.4
 * Added parallelization
 * Added dom verification
 * 
 * 2017-08-07
 * collectCompanyMarketplaceData- added pagination loop
 * collectHistorical - added
 *
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 *
 * Pending
 * Date
 * 
 * 
 * 2017-06-13	  version 2017_0.4"
 * Rectified double function "collectCompanyMarketplaceData". Deleted one of them
 * 
 * 
 * 
 * 
 * TODO
 * no real loanId exists in the public market place
 * GET https://api.lendix.com/projects?limit=10&offset=0 in order to get the marketplace list including loanId
 * This can only be done after logging . Result comes back as a JSON list
 * $tempArray['marketplace_durationUnit'] = 2; is hardcoded.
 */
class lendix extends p2pCompany {

    private $session;

    
    protected $transactionConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
 
    protected $investmentConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );

/*    NOT YET READY
    protected $investmentConfigParms = array ('OffsetStart' => 1,
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
     * 	@param  int	$amount 	: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int			: Total cost (in Eurocents) of the loan
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
     * Collect the marketplace data
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) {
        $tempArray = array();
        $totalArray = array();

        $offset = 0;  //Lendix have offsets of 12 invesments
        $url = array_shift($this->urlSequence);
        $reading = true;
        $readController = 0;
        $investmentController = false;

        while ($reading) { //Pagination loop
            $investmentNumber = 0;
            $str = $this->getCompanyWebpage($url . $offset);  // load Webpage into a string variable so it can be parsed
            $dom = new DOMDocument;
            $dom->loadHTML($str);

            $dom->preserveWhiteSpace = false;
            $divs = $this->getElements($dom, "li", "class", "card clickable project");

           foreach ($divs as $key2 => $div2) {
              echo "key2 = $key2, and value = " . $div2->nodeValue . "<br>";
              } //Debug

            if ($totalArray !== false) {
                foreach ($divs as $key => $div) {


                    if ($offset == 0 && $key == 0) { //Compare structures, only compare the first element                      
                        $structureRevision = $this->htmlRevision($structure,'li',null,null,null,array('dom' => $dom, 'tag' => 'ul', 'attribute' => 'class', 'attrValue' => 'projects'));
                        if($structureRevision[1]){
                            $totalArray = false; //Stop reading in error
                            $reading = false;
                            break;
                        }      
                    }


                    $projectDivs = $this->getElements($div, "div");
                    foreach ($projectDivs as $key11 => $div11) {
                      echo "key11 = $key11, and value = " . $projectDivs[$key11]->nodeValue . " ,and attr = " . $projectDivs[$key11]->getAttribute('title') . "<br>";
                      }  //Debug

                    $tempArray['marketplace_rating'] = trim($projectDivs[6]->nodeValue);
                    $tempArray['marketplace_name'] = trim($projectDivs[0]->nodeValue);
                    $tempArray['marketplace_interestRate'] = $this->getPercentage($projectDivs[2]->nodeValue);
                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($projectDivs[4]->nodeValue);
                    /*                     * **************************************************** */
                    /* HARD CODED AS PREVIOUS STATEMENT GENERATES AN ERROR */
                    $tempArray['marketplace_durationUnit'] = 2;
                    /*                     * **************************************************** */

                    if (count($projectDivs) >= 26) {

                        $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($projectDivs[12]->getAttribute('title'));
                        $tempArray['marketplace_purpose'] = trim($projectDivs[21]->nodeValue);
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue($projectDivs[16]->nodeValue);
                        $tempArray['marketplace_country'] = strtoupper(trim($projectDivs[9]->nodeValue));
                        $tempArray['marketplace_requestorLocation'] = trim($projectDivs[19]->nodeValue);
                        $tempArray['marketplace_sector'] = trim($projectDivs[22]->nodeValue);
                    }
                    if (count($projectDivs) <= 25) { //if we dont have country ( $projectDivs[9]), the array positions displace, we need to fix them.(Index -2)
                        $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($projectDivs[10]->getAttribute('title'));
                        $tempArray['marketplace_purpose'] = trim($projectDivs[19]->nodeValue);
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue($projectDivs[14]->nodeValue);
                        $tempArray['marketplace_country'] = 'N/A'; //We dont have country
                        $tempArray['marketplace_requestorLocation'] = trim($projectDivs[17]->nodeValue);
                        $tempArray['marketplace_sector'] = trim($projectDivs[20]->nodeValue);
                    }




                    $as = $this->getElements($div, "a");
                    $loanId = explode(":", $as[0]->getAttribute("title"));
                    $tempArray['marketplace_loanReference'] = trim($loanId[1]);

                    if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                        $tempArray['marketplace_statusLiteral'] = 'Completado';
                        $tempArray['marketplace_status'] = PERCENT;
                        foreach ($companyBackup as $inversionBackup) {//If completed investment status is the same than backup
                            if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                echo 'Already exist';
                                $readController++;
                                $investmentController = true;
                            }
                        }
                    } else {
                        $tempArray['marketplace_statusLiteral'] = 'En proceso';
                    }

                    $investmentNumber++; //Add investment
                    echo 'number : ' . $investmentNumber . '<br>';
                    $this->print_r2($tempArray);
                    if ($investmentController) { //Don't save a already existing investment
                        unset($tempArray);
                        $investmentController = false;
                    } else {
                        if ($tempArray) {
                            $totalArray[] = $tempArray;
                            unset($tempArray);
                        }
                    }
                }
            }
            $offset = $offset + 12; //We have 12 investment in each offset
            if ($readController > 2 || $investmentNumber < 12) {
                echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $page;
                $reading = false;
            } //Stop reading
        }


        $this->print_r2($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     * Collect all investment
     * @param Array $structure
     * @param Int $offset Lendix have offset of 12 investment
     * @return Array
     */
    function collectHistorical($structure, $offset) {
        $tempArray = array();
        $totalArray = array();


        $url = array_shift($this->urlSequence);


        $investmentNumber = 0;
        $str = $this->getCompanyWebpage($url . $offset);  // load Webpage into a string variable so it can be parsed
        $dom = new DOMDocument;
        $dom->loadHTML($str);

        $dom->preserveWhiteSpace = false;
        $divs = $this->getElements($dom, "li", "class", "card clickable project");

        /* foreach ($divs as $key2 => $div2) {
          echo "key2 = $key2, and value = " . $div2->nodeValue . "<br>";
          } */ //DEBUG

        if ($totalArray !== false) {
            foreach ($divs as $key => $div) {

                if ($offset == 0 && $key == 0) { //Compare structures, only compare the first element
                    $structureRevision = $this->htmlRevision($structure,'li',null,null,null,array('dom' => $dom, 'tag' => 'ul', 'attribute' => 'class', 'attrValue' => 'projects'));
                    if($structureRevision[1]){
                        $totalArray = false; //Stop reading in error
                        $offset = false;
                        break;
                    }           
                }


                $projectDivs = $this->getElements($div, "div");

                /* foreach ($projectDivs as $key11 => $div11) {
                  echo "key11 = $key11, and value = " . $projectDivs[$key11]->nodeValue . " ,and attr = " . $projectDivs[$key11]->getAttribute('title') . "<br>";
                  } */ //DEBUG



                $tempArray['marketplace_rating'] = trim($projectDivs[6]->nodeValue);
                $tempArray['marketplace_name'] = trim($projectDivs[0]->nodeValue);
                $tempArray['marketplace_interestRate'] = $this->getPercentage($projectDivs[2]->nodeValue);
                list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($projectDivs[4]->nodeValue);
                /*                 * **************************************************** */
                /* HARD CODED AS PREVIOUS STATEMENT GENERATES AN ERROR */
                $tempArray['marketplace_durationUnit'] = 2;
                /*                 * **************************************************** */

                if (count($projectDivs) >= 27) {

                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($projectDivs[12]->getAttribute('title'));
                    $tempArray['marketplace_purpose'] = trim($projectDivs[21]->nodeValue);
                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($projectDivs[16]->nodeValue);
                    $tempArray['marketplace_country'] = strtoupper(trim($projectDivs[9]->nodeValue));
                    $tempArray['marketplace_requestorLocation'] = trim($projectDivs[19]->nodeValue);
                    $tempArray['marketplace_sector'] = trim($projectDivs[22]->nodeValue);
                }
                if (count($projectDivs) <= 25) { //if we dont have country ( $projectDivs[9]), the array positions displace, we need to fix them.(Index -2)
                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($projectDivs[10]->getAttribute('title'));
                    $tempArray['marketplace_purpose'] = trim($projectDivs[19]->nodeValue);
                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($projectDivs[14]->nodeValue);
                    $tempArray['marketplace_country'] = 'N/A'; //We dont have country
                    $tempArray['marketplace_requestorLocation'] = trim($projectDivs[17]->nodeValue);
                    $tempArray['marketplace_sector'] = trim($projectDivs[20]->nodeValue);
                }




                $as = $this->getElements($div, "a");
                $loanId = explode(":", $as[0]->getAttribute("title"));
                $tempArray['marketplace_loanReference'] = trim($loanId[1]);

                if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                    $tempArray['marketplace_statusLiteral'] = 'Completado';
                    $tempArray['marketplace_status'] = PERCENT;
                } else {
                    $tempArray['marketplace_statusLiteral'] = 'En proceso';
                }

                $investmentNumber++; //Add invesment
                echo 'number : ' . $investmentNumber . '<br>';
                $this->print_r2($tempArray);

                $totalArray[] = $tempArray;
                unset($tempArray);
            }
        }

        $offset = $offset + 12; //12 investment in the offset
        if ($investmentNumber < 12) {
            echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $page;
            $offset = false;
        } //Stop reading



        $this->print_r2($totalArray);
        return [$totalArray, $offset, null, $structureRevision[0], $structureRevision[2]]; //$offset -> Number of next first offset investment, false when it is the last offset.
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	also do logout
     * 	
     */
    function collectUserInvestmentDataParallel($str) {
        // user: inigo.iturburua@gmail.com
        // password: Ap_94!56
        // $this->config['appDebug'] = true;
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                $credentials = array();
                $credentials['email'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['user'] = null;
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 2:
                $this->mainPortalPage = $str;
                $result = json_decode($this->mainPortalPage, $assoc = true);
                // check if user actually has entered the portal of the company
                $resultLendix = false;
                if (!empty($result['session']['user']['id'])) {
                    $resultLendix = true;
                }
                if (!$resultLendix) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Lendix login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->session = json_decode($this->mainPortalPage, $assoc = true);
                //$this->print_r2($session);
                $lendixSessionId = $this->session['session']['id'];
                $lendixSessionToken = $this->session['session']['token'];
                $userId = $this->session['session']['user']['id'];
                $header1 = "sessionToken: $lendixSessionToken";
                $header2 = "userId: $userId";
                // construct extra headers for next http message
                $this->defineHeaderParms(array($header1, $header2));
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // https://api.lendix.com/transactions/summary?finsquare=true
                break;
            case 3:
                $summaryData = json_decode($str, $assoc = true);
                $this->print_r2($summaryData);
                foreach ($summaryData['investments'] as $key => $item) {
                    $this->data1[$key]['name'] = $item['project']['name'];
                    $this->data1[$key]['loanId'] = $item['project']['name'];
                    $this->data1[$key]['date'] = $date;
                    $this->data1[$key]['duration'] = $item['investment']['monthsLeft'] . " Meses";
                    $this->data1[$key]['invested'] = (int) (preg_replace('/\D/', '', $item['investment']['total'])) * 100;
                    $this->data1[$key]['commission'] = 0; //(int) (preg_replace('/\D/', '', $item['investment']['taxes'])) * 100;
                    $this->data1[$key]['interest'] = $this->getPercentage($item['project']['rate']);
                    $this->data1[$key]['amortized'] = $item['investment']['received'] * 100;
                    $this->data1[$key]['profitGained'] = $item['investment']['interests'] * 100;

                    $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] + $this->data1[$key]['profitGained'];
                    $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$key]['invested'];
                    $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['activeInInvestments'] + ($this->data1[$key]['invested'] - $this->data1[$key]['amortized'] );
                    $this->tempArray['global']['totalInvestments'] = $this->tempArray['global']['totalInvestments'] + $this->data1[$key]['invested'];
                }
                $this->tempArray['global']['profitibility'] = $this->getPercentage($summaryData['averageRate']);
                $this->tempArray['global']['investments'] = count($summaryData['investments']);
                $this->tempArray['investments'] = $this->data1;
                $this->tempArray['global']['myWallet'] = $this->session['session']['user']['credit'];
                $this->print_r2($this->tempArray);
                return $this->tempArray;
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	also do logout
     * 	
     */
    function collectUserInvestmentData($user, $password) {
// user: inigo.iturburua@gmail.com
// password: Ap_94!56
//	$this->config['appDebug'] = true;
        $resultLendix = $this->companyUserLogin($user, $password);
        if (!$resultLendix) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Lendix login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        $session = json_decode($this->mainPortalPage, $assoc = true);
//$this->print_r2($session);
        $lendixSessionId = $session['session']['id'];
        $lendixSessionToken = $session['session']['token'];
        $userId = $session['session']['user']['id'];
        $header1 = "sessionToken: $lendixSessionToken";
        $header2 = "userId: $userId";
// construct extra headers for next http message
        $this->defineHeaderParms(array($header1, $header2));

        $str = $this->getCompanyWebpage();  // https://api.lendix.com/transactions/summary?finsquare=true
        $summaryData = json_decode($str, $assoc = true);
        echo "<h1>ARRAY</h1>";
        $this->print_r2($summaryData);
        echo "<h1>ARRAY FIN</h1>";
        foreach ($summaryData['investments'] as $key => $item) {

            $data1[$key]['name'] = $item['project']['name'];
            $data1[$key]['loanId'] = $item['project']['name'];
//            $data1[$key]['date'] = $date; So far we cannot deal with the date
            $data1[$key]['duration'] = $item['investment']['monthsLeft'] . " Meses";
            $data1[$key]['invested'] = (int) (preg_replace('/\D/', '', $item['investment']['total'])) * 100;
            $data1[$key]['commission'] = 0; //(int) (preg_replace('/\D/', '', $item['investment']['taxes']))*10;
            $data1[$key]['interest'] = $this->getPercentage($item['project']['rate']);
            $data1[$key]['amortized'] = $item['investment']['received'] * 100;
            $data1[$key]['profitGained'] = $item['investment']['interests'] * 100;

            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
            $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + ($data1[$key]['invested'] - $data1[$key]['amortized'] );
            $tempArray['global']['totalInvestments'] = $tempArray['global']['totalInvestments'] + $data1[$key]['invested'];
        }
        $tempArray['global']['profitibility'] = $this->getPercentage($summaryData['averageRate']);
        $tempArray['global']['investments'] = count($summaryData['investments']);
        $tempArray['investments'] = $data1;
        $tempArray['global']['myWallet'] = $session['session']['user']['credit'];
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
// user = inigo.iturburua@gmail.com
// pw = Ap_94!56
        $str = $this->getCompanyWebpage();
        $credentials = array();
        $credentials['email'] = $user;
        $credentials['password'] = $password;
        $credentials['user'] = null;

        $this->mainPortalPage = $this->doCompanyLogin($credentials);
        $result = json_decode($this->mainPortalPage, $assoc = true);
// check if user actually has entered the portal of the company
        if (!empty($result['session']['user']['id'])) {
            return 1;
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
// logout sequence is https://api.lendix.com/sessions/58a03f48b04a650016a7d72c with session-id, send as a DELETE msg
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

        //This class indicates status
        $node1->removeAttribute('class');
        $node2->removeAttribute('class');

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('srcset', 'src', 'alt', 'href', 'style', 'title', 'height'));

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'div'), //the div class contains the rating
            array('typeSearch' => 'element', 'tag' => 'li'), //the li class contains the status
                ), array('class'));


        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('srcset', 'src', 'alt', 'href', 'style', 'title', 'height'));

        $node2 = $this->cleanDom($node2, array(//the div class contains the rating
            array('typeSearch' => 'element', 'tag' => 'div'), //the div class contains the rating
            array('typeSearch' => 'element', 'tag' => 'li'), //the li class contains the status
                ), array('class'));




        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}
