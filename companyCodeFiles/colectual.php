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
 * Contains the code required for accessing the website of "Comunitae"
 *
 * 
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-11-05
 * @package

  function calculateLoanCost()							[not OK, not tested]
  function collectCompanyMarketplaceData()					[OK, testing]
  function companyUserLogin()							[OK, testing]
  function collectUserInvestmentData()						[not OK, not tested]
  function isNewEntry()								[not OK, not tested]

  2016-11-05	  version 2016_0.1
  Basic version

  2017-04-10      version 0.3
 * Added casperjs to make a login into colectual
 * It has angularjs and because of that, we need to use casperjs

  2017-07-25      version 0.3
 * Added login function to Colectual and urlsequences
 * Added logout function to Colectual

 * 2017-08-09      version 0.4
 * collectCompanyMarketplaceData -Completed investment read
 * collectHistorical  - added
 * 
 * 
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 */

class colectual extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
    public function getParserConfigTransactionFile() {
        return $this->$valuesColectualTransaction;
    }
 
    public function getParserConfigInvestmentFile() {
        return $this->$valuesColectualInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesColectualAmortization;
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
        /*
          $minimumCommission = 12000;			// in  €cents

          $fixedCost = 3 * $amount/100;
          if ($fixedCost < $minimumCommission) {
          $fixedCost = $minimumCommission;
          }

          $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12) ;
          $totalCost = $fixedCost + $interest + $amount;
          return $fixedCost + $interest + $amount;
         */
    }

    /**
     * 	Collects the marketplace data. We must login first in order to obtain the marketplace data
     *  Colectual use casperjs to get the information
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) {

        $readController = 0;
        $investmentController = false;
        $url = $this->urlSequence;
        $username = $this->config['company_username'];
        $password = $this->config['company_password'];
        $totalArray = array();
        
        $this->casperInit($url[0]);
        $this->casperWaitSelector('form[name="loginForm"]', 5000);
        $fillFormArray = array(
            'UserName' => $username,
            'Password' => $password
                );
        $this->casperFillForm('form[name="loginForm"]', $fillFormArray);
        $this->casperClick('.md-btn');

        $fragment = <<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
                     this.echo("I am in");
		});
FRAGMENT;
        $this->casperAddFragment($fragment);
        $this->casperRun();
        $str = $this->casperGetContent();
        $dom = new DOMDocument;
        $classname = 'step-instructions';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $resultColectual = false;
        $login = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        if ($login->length > 0) {
            $resultColectual = true;
            //echo "LOGIN CORRECT";
        }
        
         if (!$resultColectual) {   // Error while logging in
            echo __FUNCTION__ . __LINE__ . "login fail" . SHELL_ENDOFLINE;
            $tracings = "Tracing: " . SHELL_ENDOFLINE;
            $tracings .= __FILE__ . " " . __LINE__ . SHELL_ENDOFLINE;
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . SHELL_ENDOFLINE;
            $tracings .= SHELL_ENDOFLINE;
            $msg = "Error while entering user's portal. Wrong userid/password" . SHELL_ENDOFLINE;
            $msg = $msg . $tracings . SHELL_ENDOFLINE;
            $this->logToFile("Warning", $msg);
            exit;
        }

        $fragment = <<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
			casper.thenOpen('$url[1]');
		});
FRAGMENT;
        //function to add a script because casperjs dont give all the options to make it versatile
        $this->casperAddFragment($fragment);
        //$casper->click('a[href*="proyectosLista"]');
        // or wait for selector
        $this->casperWaitSelector('.proyecto', 5000);
        // run the casper script
        //function to run the script
        $this->casperRun();
        
        //function to get the current page content (str)
        
        $str = $this->casperGetContent();
        //echo $str;
        //var_dump($casper->getOutput());
        echo __FUNCTION__ . __LINE__ . " END MARKETPLACE<br>";
        echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
        $this->companyUserLogout($url[2]);
        echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
        //var_dump($casper_logout->getOutput());
        echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>";
        $dom = new DOMDocument;
        $classname = 'proyecto';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $projects = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $i = 0;
        foreach ($projects as $key => $project) {


            if ($key == 0) { //Compare structures, only compare the first element
                      
                $structureRevision = $this->htmlRevision($structure,'div',null,'class','col-lg-4',array('dom' => $dom, 'tag' => 'div', 'attribute' => 'class', 'attrValue' => 'row'),3,1);
                    if($structureRevision[1]){
                        $totalArray = false; //Stop reading in error    
                        break;
                    }
            }

            $name = $project->getElementsByTagName('h2');
            $tempArray['marketplace_name'] = $name[0]->nodeValue;
            $purpose = $project->getElementsByTagName('h3');
            $tempArray['marketplace_purpose'] = $purpose[0]->nodeValue;
            $labels = $project->getElementsByTagName('label');
            //$tempArray["marketplace_durationUnit"] = 2;
            $span_fund = $project->getElementsByTagName('span');
            $subscriptionProgress = true;
            if ($span_fund->length > 0) {
                $class_name = $span_fund[0]->getAttribute('class');
                if (strpos($class_name, "financiado") !== false) {

                    $class_subscription_progress = 'progress';
                    $dom_xpath_progress = new DOMXPath($dom);
                    $progress = $dom_xpath_progress->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class_subscription_progress ')]");
                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($progress[$i]->nodeValue));

                    //$tempArray['marketplace_subscriptionProgress'] = 10000;		// completed, retrasado orr amortización ..
                    $tempArray["marketplace_statusLiteral"] = 'Completado';
                    $tempArray["marketplace_status"] = PERCENT;

                    foreach ($companyBackup as $inversionBackup) { //If completed investment with same status in backup
                        if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                            echo 'already exist';
                            $readController++;
                            $investmentController = true;
                        }
                    }
                    $subscriptionProgress = false;
                }
            }
            if ($subscriptionProgress) {
                echo "Colectual: % found, so store in marketplace<br>";
                $class_subscription_progress = 'progress';
                $dom_xpath_progress = new DOMXPath($dom);
                $progress = $dom_xpath_progress->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class_subscription_progress ')]");
                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($progress[$i]->nodeValue));
                $tempArray["marketplace_statusLiteral"] = 'En proceso';
            }
            foreach ($labels as $label) {
                $value_per_attr = $label->getAttribute('class');
                switch ($value_per_attr) {
                    case "id":
                        $tempArray["marketplace_loanReference"] = $label->nodeValue;
                        break;
                    case "importe":
                        $amount = $label->nodeValue;
                        $amount = $this->getMonetaryValue($amount);
                        $amountTotal = $amount;
                        if ($tempArray['marketplace_subscriptionProgress'] < 10000) {
                            $amountTotal = ($amount * $tempArray['marketplace_subscriptionProgress']) / 10000;
                        }
                        $tempArray["marketplace_amount"] = $amount;
                        $tempArray["marketplace_amountTotal"] = $amountTotal;

                        break;
                    case "plazo":
                        $date = $this->getDurationValue($label->nodeValue);
                        $tempArray["marketplace_duration"] = $date[0];
                        $tempArray["marketplace_durationUnit"] = $date[1];
                        break;

                        break;
                    //Regex to take the rating value, always come with a letter, b, or c, or d
                    case (preg_match('/^rating.*/', $value_per_attr) ? true : false) :
                        $tempArray["marketplace_rating"] = $label->nodeValue;
                        break;
                    case "interes":
                        $tempArray["marketplace_interestRate"] = $this->getPercentage(trim($label->nodeValue));
                        break;
                    case "fecha":
                        //have to fix
                        $date_array = explode("/", $label->nodeValue);
                        $date_marketplace = $date_array[2] . "-" . $date_array[1] . "-" . $date_array[0];
                        $today = date("Y-m-d");
                        //$diff=date_diff($date_marketplace,$today);
                        //Bigger date
                        $datetime1 = strtotime($date_marketplace);
                        //It's suppose to be before ending marketplace
                        $datetime2 = strtotime($today);

                        $secs = $datetime1 - $datetime2; // == <seconds between the two times>
                        $days = $secs / 86400;
                        //$date2->diff($date1)->format("%a");
                        $tempArray["marketplace_timeLeft"] = $days;
                        $tempArray["marketplace_timeLeftUnit"] = 1;
                        break;
                    case "sector":
                        $tempArray["marketplace_sector"] = $label->nodeValue;
                        break;
                    case "ubicacion":
                        $tempArray["marketplace_requestorLocation"] = $label->nodeValue;
                        break;
                    case "inversores":
                        $tempArray["marketplace_numberOfInvestors"] = $label->nodeValue;
                        break;
                    default:
                        break;
                }
            }
            if ($investmentController) { //Don't save a already existing investment
                unset($tempArray);
                $investmentController = false;
            } else {
                $totalArray[] = $tempArray;
                unset($tempArray);
            }
            $i++;
            if ($readController > 2) {
                break;
            }
        }
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
                //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     * Collect all investment
     * @return type
     */
    function collectHistorical() { //Colectual doesnt have pagination
        $url = $this->urlSequence;
        $username = $this->config['company_username'];
        $password = $this->config['company_password'];
        $totalArray = array();
        //echo __FUNCTION__ . __LINE__ . " MARKETPLACE<br>";
        $this->casperInit($url[0]);
        $this->casperWaitSelector('form[name="loginForm"]', 5000);
        $fillFormArray = array(
            'UserName' => $username,
            'Password' => $password
                );
        $this->casperFillForm('form[name="loginForm"]', $fillFormArray);
        $this->casperClick('.md-btn');

        $fragment = <<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
                     this.echo("I am in");
		});
FRAGMENT;
        $this->casperAddFragment($fragment);
        $this->casperRun();
        $str = $this->casperGetContent();
        $dom = new DOMDocument;
        $classname = 'step-instructions';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $resultColectual = false;
        $login = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        if ($login->length > 0) {
            $resultColectual = true;
            //echo "LOGIN CORRECT";
        }
        
         if (!$resultColectual) {   // Error while logging in
            echo __FUNCTION__ . __LINE__ . "login fail" . SHELL_ENDOFLINE;
            $tracings = "Tracing: " . SHELL_ENDOFLINE;
            $tracings .= __FILE__ . " " . __LINE__ . SHELL_ENDOFLINE;
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . SHELL_ENDOFLINE;
            $tracings .= SHELL_ENDOFLINE;
            $msg = "Error while entering user's portal. Wrong userid/password" . SHELL_ENDOFLINE;
            $msg = $msg . $tracings . SHELL_ENDOFLINE;
            $this->logToFile("Warning", $msg);
            exit;
        }

        $fragment = <<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
			casper.thenOpen('$url[1]');
		});
FRAGMENT;
        //function to add a script because casperjs dont give all the options to make it versatile
        $this->casperAddFragment($fragment);
        //$casper->click('a[href*="proyectosLista"]');
        // or wait for selector
        $this->casperWaitSelector('.proyecto', 5000);
        // run the casper script
        //function to run the script
        $this->casperRun();
        
        //function to get the current page content (str)
        
        $str = $this->casperGetContent();
        //echo $str;
        //var_dump($casper->getOutput());
        echo __FUNCTION__ . __LINE__ . " END MARKETPLACE<br>";
        echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
        $this->companyUserLogout($url[2]);
        echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>";
        $dom = new DOMDocument;
        $classname = 'proyecto';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $projects = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $i = 0;
        foreach ($projects as $project) {


            if ($key == 0) { //Compare structures, only compare the first element                
                $structureRevision = $this->htmlRevision($structure,'div',null,'class','col-lg-4',array('dom' => $dom, 'tag' => 'div', 'attribute' => 'class', 'attrValue' => 'row'));
                    if($structureRevision[1]){
                        $totalArray = false; //Stop reading in error                         
                        break;
                    }
            }


            $name = $project->getElementsByTagName('h2');
            $tempArray['marketplace_name'] = $name[0]->nodeValue;
            $purpose = $project->getElementsByTagName('h3');
            $tempArray['marketplace_purpose'] = $purpose[0]->nodeValue;
            $labels = $project->getElementsByTagName('label');
            $span_fund = $project->getElementsByTagName('span');
            $subscriptionProgress = true;
            if ($span_fund->length > 0) {
                $class_name = $span_fund[0]->getAttribute('class');
                if (strpos($class_name, "financiado") !== false) {

                    $class_subscription_progress = 'progress';
                    $dom_xpath_progress = new DOMXPath($dom);
                    $progress = $dom_xpath_progress->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class_subscription_progress ')]");
                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($progress[$i]->nodeValue));

                    //$tempArray['marketplace_subscriptionProgress'] = 10000;		// completed, retrasado orr amortización ..
                    $tempArray["marketplace_statusLiteral"] = 'Completado';
                    $tempArray["marketplace_status"] = PERCENT;
                    $subscriptionProgress = false;
                }
            }
            if ($subscriptionProgress) {
                echo "Colectual: % found, so store in marketplace<br>";
                $class_subscription_progress = 'progress';
                $dom_xpath_progress = new DOMXPath($dom);
                $progress = $dom_xpath_progress->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class_subscription_progress ')]");
                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($progress[$i]->nodeValue));
                $tempArray["marketplace_statusLiteral"] = 'En proceso';
            }
            foreach ($labels as $label) {
                $value_per_attr = $label->getAttribute('class');
                switch ($value_per_attr) {
                    case "id":
                        $tempArray["marketplace_loanReference"] = $label->nodeValue;
                        break;
                    case "importe":
                        $amount = str_replace(".", "", $label->nodeValue);
                        $amount = $this->getMonetaryValue(strstr($amount, ',', true));

                        if ($tempArray['marketplace_subscriptionProgress'] < 10000) {
                            $amountTotal = ($amount * $tempArray['marketplace_subscriptionProgress']) / 10000;
                        } else {
                            $amountTotal = $amount;
                        }
                        $tempArray["marketplace_amount"] = $amount;
                        $tempArray["marketplace_amountTotal"] = $amountTotal;
                        break;
                    case "plazo":

                        if (in_array("m", str_split($label->nodeValue))) {
                            $tempArray["marketplace_duration"] = trim(str_replace("m ", "", $label->nodeValue));
                            $tempArray["marketplace_durationUnit"] = 2;
                        } else if (in_array("d", str_split($label->nodeValue))) {
                            $tempArray["marketplace_duration"] = trim(str_replace("d ", "", $label->nodeValue));
                            $tempArray["marketplace_durationUnit"] = 1;
                        }
                        break;
                    //Regex to take the rating value, always come with a letter, b, or c, or d
                    case (preg_match('/^rating.*/', $value_per_attr) ? true : false) :
                        $tempArray["marketplace_rating"] = $label->nodeValue;
                        break;
                    case "interes":
                        $tempArray["marketplace_interestRate"] = $this->getPercentage(trim($label->nodeValue));
                        break;
                    case "fecha":
                        //have to fix
                        $date_array = explode("/", $label->nodeValue);
                        $date_marketplace = $date_array[2] . "-" . $date_array[1] . "-" . $date_array[0];
                        $today = date("Y-m-d");
                        //$diff=date_diff($date_marketplace,$today);
                        //Bigger date
                        $datetime1 = strtotime($date_marketplace);
                        //It's suppose to be before ending marketplace
                        $datetime2 = strtotime($today);

                        $secs = $datetime1 - $datetime2; // == <seconds between the two times>
                        $days = $secs / 86400;
                        //$date2->diff($date1)->format("%a");
                        $tempArray["marketplace_timeLeft"] = $days;
                        $tempArray["marketplace_timeLeftUnit"] = 1;
                        break;
                    case "importe":
                        $tempArray["marketplace_loandReference"] = $label->nodeValue;
                        break;
                    case "sector":
                        $tempArray["marketplace_sector"] = $label->nodeValue;
                        break;
                    case "ubicacion":
                        $tempArray["marketplace_requestorLocation"] = $label->nodeValue;
                        break;
                    case "inversores":
                        $tempArray["marketplace_numberOfInvestors"] = $label->nodeValue;
                        break;
                    default:
                        break;
                }
            }

            $totalArray[] = $tempArray;

            $i++;
        }
        return [$totalArray, false, null, $structureRevision[0],$structureRevision[2]];
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
    function collectUserInvestmentData($str = null) {
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
    
    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentDataParallel($str = null) {
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
//manu.azarus@gmail.com
//Azarus2016!
        /*
          https://api.colectual.com/token
          client_id=ngAuthApp
          grant_type=password
          password=Azarus2016!
          username=manu.azarus@gmail.com

          look for the class "step-instructions"
         */
        $url = $this->urlSequence;
        $username = $user;
        $password = $password;
        $this->casperInit($url[0]);
        $this->casperWaitSelector('form[name="loginForm"]', 5000);
        $fillFormArray = array(
            'UserName' => $username,
            'Password' => $password
                );
        $this->casperFillForm('form[name="loginForm"]', $fillFormArray);
        $this->casperClick('.md-btn');

        $fragment = <<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
                     this.echo("I am in");
		});
FRAGMENT;
        $this->casperAddFragment($fragment);
        $this->casperRun();
        $str = $this->casperGetContent();
        $dom = new DOMDocument;
        $classname = 'step-instructions';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $loginIn = false;
        $login = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        if ($login->length > 0) {
            $loginIn = true;
        }
        //This two lines are for debug purpose
        //var_dump($casper->getOutput());
        //echo __FUNCTION__ . __LINE__ . " LOGIN<br>";
        $this->companyUserLogout($url[1]);
        return $loginIn;
        
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout($url = null) {


        $this->casperInit($url);
        $this->casperWait(2000);
        $this->casperRun();
        /* These lines are for debug purpose
          echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
          var_dump($casper_logout->getOutput());
          echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>"; */
        return true;
    }

    /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {


        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('src', 'ng-src', 'aria-valuenow', 'style'));

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'label'), //label class contain rating
                ), array('class'));


        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'span'),
            array('typeSearch' => 'tagElement', 'tag' => 'label', 'attr' => 'ng-class', 'value' => 'vm.getObtenerRscClass(proyecto.RSCDelPromotor)'), //This label doesnt apperar in all investment, we must delete it
            array('typeSearch' => 'tagElement', 'tag' => 'label', 'attr' => 'data-ng-if', 'value' => 'proyecto.RSCDelPromotor != null'), //This label doesnt apperar in all investment, we must delete it
        ));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('src', 'ng-src', 'aria-valuenow', 'style'));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'label'), //label class contain rating
                ), array('class'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'span'),
            array('typeSearch' => 'tagElement', 'tag' => 'label', 'attr' => 'ng-class', 'value' => 'vm.getObtenerRscClass(proyecto.RSCDelPromotor)'), //This label doesnt apperar in all investment, we must delete it
            array('typeSearch' => 'tagElement', 'tag' => 'label', 'attr' => 'data-ng-if', 'value' => 'proyecto.RSCDelPromotor != null'), //This label doesnt apperar in all investment, we must delete it
        ));


        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}

?>
