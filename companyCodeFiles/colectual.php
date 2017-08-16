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

  PENDING:
  Install dependencies of casperjs and phantomjs

 */

//require_once "../../vendors/autoload.php";
use Browser\Casper;

class colectual extends p2pCompany {

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
     *
     * 	Collects the marketplace data. We must login first in order to obtain the marketplace data
     *       Colectual use casperjs to get the information
     * 	@return array	Each investment option as an element of an array
     * 	
     */
    function collectCompanyMarketplaceData($companyBackup) {

        $readController = 0;
        $investmentController = false;
        $url = $this->urlSequence;
        $username = $this->config['company_username'];
        $password = $this->config['company_password'];
        $totalArray = array();
        //echo __FUNCTION__ . __LINE__ . " MARKETPLACE<br>";
        $casper = new Casper();
        //First we must go to https://app.colectual.com/#/endSession to end session if previously is opened
        $casper->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to login web page
        $casper->start($url[0]);
        // or wait for selector
        $casper->waitForSelector('form[name="loginForm"]', 5000);
        $casper->fillForm(
                'form[name="loginForm"]', array(
            'UserName' => $username,
            'Password' => $password
                ), false);
        $casper->click('.md-btn');

        $casper->addToScript(<<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
			casper.thenOpen('$url[1]');
		});
FRAGMENT
        );
        //$casper->click('a[href*="proyectosLista"]');
        // or wait for selector
        $casper->waitForSelector('.proyecto', 5000);
        // run the casper script
        $casper->run();
        $str = $casper->getCurrentPageContent();
        //echo $str;
        var_dump($casper->getOutput());
        echo __FUNCTION__ . __LINE__ . " END MARKETPLACE<br>";
        $casper_logout = new Casper();
        $casper_logout->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to make logout
        $casper_logout->start($url[2]);
        $casper_logout->wait(2000);
        $casper_logout->run();
        echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
        var_dump($casper_logout->getOutput());
        echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>";
        $dom = new DOMDocument;
        $classname = 'proyecto';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $projects = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $i = 0;
        foreach ($projects as $project) {
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
                    $tempArray["marketplace_status"] = 1;

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
        return $totalArray;
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
        $casper = new Casper();
        //First we must go to https://app.colectual.com/#/endSession to end session if previously is opened
        $casper->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to login web page
        $casper->start($url[0]);
        // or wait for selector
        $casper->waitForSelector('form[name="loginForm"]', 5000);
        $casper->fillForm(
                'form[name="loginForm"]', array(
            'UserName' => $username,
            'Password' => $password
                ), false);
        $casper->click('.md-btn');

        $casper->addToScript(<<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
			casper.thenOpen('$url[1]');
		});
FRAGMENT
        );
        //$casper->click('a[href*="proyectosLista"]');
        // or wait for selector
        $casper->waitForSelector('.proyecto', 5000);
        // run the casper script
        $casper->run();
        $str = $casper->getCurrentPageContent();
        //echo $str;
        var_dump($casper->getOutput());
        echo __FUNCTION__ . __LINE__ . " END MARKETPLACE<br>";
        $casper_logout = new Casper();
        $casper_logout->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to make logout
        $casper_logout->start($url[2]);
        $casper_logout->wait(2000);
        $casper_logout->run();
        echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
        var_dump($casper_logout->getOutput());
        echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>";
        $dom = new DOMDocument;
        $classname = 'proyecto';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $projects = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $i = 0;
        foreach ($projects as $project) {
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
                    $tempArray["marketplace_status"] = 1;
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
        return [$totalArray, false];
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
        $loginIn = false;
        $url = $this->urlSequence;
        $username = $user;
        $password = $password;
        $casper = new Casper();
        //First we must go to https://app.colectual.com/#/endSession to end session if previously is opened
        $casper->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to login web page
        $casper->start("$url[0]");
        // or wait for selector
        $casper->waitForSelector('form[name="loginForm"]', 5000);
        $casper->fillForm(
                'form[name="loginForm"]', array(
            'UserName' => $username,
            'Password' => $password
                ), false);
        $casper->click('.md-btn');

        $casper->addToScript(<<<FRAGMENT
casper.waitUntilVisible(".step-instructions", function() {
                     this.echo("I am in");
		});
FRAGMENT
        );

        // run the casper script
        $casper->run();
        $str = $casper->getCurrentPageContent();
        //This two lines are for debug purpose
        //var_dump($casper->getOutput());
        //echo __FUNCTION__ . __LINE__ . " LOGIN<br>";
        $this->companyUserLogout($url[1]);
        $dom = new DOMDocument;
        $classname = 'step-instructions';
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $dom_xpath = new DOMXPath($dom);
        $login = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        if ($login->length > 0) {
            $loginIn = true;
        }

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


        $casper_logout = new Casper();
        $casper_logout->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to make logout
        $casper_logout->start("$url");
        $casper_logout->wait(2000);
        $casper_logout->run();
        /* These lines are for debug purpose
          echo __FUNCTION__ . __LINE__ . " LOGOUT<br>";
          var_dump($casper_logout->getOutput());
          echo __FUNCTION__ . __LINE__ . " END LOGOUT<br>"; */
        return true;
    }

}

?>
