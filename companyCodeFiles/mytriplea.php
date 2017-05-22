<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * Contains the code required for accessing the website of "MyTripleAAA"
 *
 * 
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-08-04
 * @package


  2016-08-04	  version 2016_0.1
  Basic version
  function calculateLoanCost()											[Not OK]
  function collectCompanyMarketplaceData()								[OK, tested]
  function companyUserLogin()												[OK, tested]
  function collectUserInvestmentData()									[Not OK]
  introduced the "rating" by doing an additional read of webpage with the detailed view of the loanrequest [OK]

  2017-04-18
  Rating fixed
 

  Pending
  More Ratings

 */

class mytriplea extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
// Dictionary to map data to the database table 'marketplaces'
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
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked: xx-xx-2016

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
     *
     * 	Collects the marketplace data
     * 	@return array	Each investment option as an element of an array
     *
     */
    function collectCompanyMarketplaceData() {

        $totalArray = array();

        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed;
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $rows = $dom->getElementsByTagName('article');

        foreach ($rows as $row) {
            $h3 = $row->getElementsByTagName('h3');  // Only 1 'h3' will be encountered
            foreach ($h3 as $item) {
                
            }

            $a = $row->getElementsByTagName('a');  // Get loanId. Only 1 'a' is required
            foreach ($a as $item) {
                $tempLoanId = $item->getAttribute('href');
                $temp = explode("-", $tempLoanId);
                $tempArray['marketplace_loanReference'] = trim(preg_replace('/\D/', '', $temp[count($temp) - 1]));
                $tempArray['marketplace_href'] = $tempLoanId;   // contains the href with more details about loanrequest
                break;
            }

            $headers = $row->getElementsByTagName('header');
            foreach ($headers as $header) {
                $tempArray['marketplace_purpose'] = trim($header->nodeValue);
            }

            $li = $row->getElementsByTagName('li');

            foreach ($li as $item) {
                $checkedAttribute = trim($item->nodeValue);
                echo "<br>___checkedAttribute = $checkedAttribute<br>";
                $is = $item->getElementsByTagNAme('i');

                $contentCheckedAttribute = "";
                foreach ($is as $subItem) {
                    $contentCheckedAttribute = trim($subItem->nodeValue);
                }

                if (strncasecmp($checkedAttribute, 'Sector', 6) == 0) {
                    $tempArray['marketplace_sector'] = $contentCheckedAttribute;
                }

                if (strncasecmp($checkedAttribute, 'Lugar', 5) == 0) {
                    $tempArray['marketplace_requestorLocation'] = $contentCheckedAttribute;
                }

                if (strncasecmp($checkedAttribute, 'Importe', 7) == 0) {
                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($contentCheckedAttribute);
                }

                if (strncasecmp($checkedAttribute, 'Tipo', 4) == 0) {
                    $tempArray['marketplace_interestRate'] = $this->getPercentage($contentCheckedAttribute);
                }

                if (strncasecmp($checkedAttribute, 'Plazo', 5) == 0) {
                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($contentCheckedAttribute);
                }
                if (strncasecmp($checkedAttribute, 'Durac', 5) == 0) {
                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue(trim($item->nodeValue));
                }

                if (strncasecmp($checkedAttribute, 'Completado', 10) == 0) {
                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($checkedAttribute);
                }

                if (strncasecmp($checkedAttribute, 'Forma pago', 10) == 0) {
//				echo "contentsChecked6 = $contentCheckedAttribute <br>";
//				$tempArray['marketplace_interestRate4'] = $this->getPercentage(trim($item->nodeValue));
                }

                if (stripos($checkedAttribute, 'inversores')) {
                    $tempArray['marketplace_inversores'] = $contentCheckedAttribute;
                }
            }

            $cols = $row->getElementsByTagName('span');
            foreach ($cols as $span) {
                $checkedAttribute = $span->getAttribute('class');
                if (strcasecmp(trim($checkedAttribute), 'center-percentage') == 0) {
                    echo "contentsChecked10 = $CheckedAttribute <br>";
                    //		$tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($span->nodeValue));
                }
            }

            $rating = $row->getElementsByTagName('div');
            foreach ($rating as $rating) {
                $checkedClass = $rating->getAttribute('class');
                $checkedAttribute = $rating->getAttribute('style');
                if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoFINALIZADA' || $checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoPENDIENTE' || $checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoPRORROGADA') {
                    if (!$checkedAttribute) {
                        $tempArray['marketplace_rating'] = 'SGR';
                    }
                    if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD.png');") {
                        $tempArray['marketplace_rating'] = 'D';
                    }
                    if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD_MAS.png');") {
                        $tempArray['marketplace_rating'] = 'D+';
                    }
                }
            }

// stored all available information in array, but rating is still missing, so fetch it from the detailed view		
            if (!empty($tempArray['marketplace_loanReference'])) {

                if ($tempArray['marketplace_subscriptionProgress'] < 10000) {
                    $pos = strpos($sequence, "/", 10);
                    $host = substr($sequence, 0, $pos);

                    $strTemp = $this->getCompanyWebpage($host . $tempArray['marketplace_href']); // load Webpage into a string variable so it can be parsed;
                    $domTemp = new DOMDocument;
                    $domTemp->loadHTML($strTemp);
                    $domTemp->preserveWhiteSpace = false;
                    $divs = $domTemp->getElementsByTagName('div');
                    foreach ($divs as $div) {
                        $className = $div->getAttribute('class');
                        if (strcasecmp($className, 'inversionFicha') == 0) {  // correct div found
                            $lis = $div->getElementsByTagName('li');
                            foreach ($lis as $li) {
                                $bs = $li->getElementsByTagName('b');
                                foreach ($bs as $b) {
                                    $tempArray['marketplace_rating'] = trim($b->nodeValue);
                                    break 3;
                                }
                            }
                        }
                    }
                }
                $totalArray[] = $tempArray;   // Do not store the last <article> tag as it contain no real data
                $this->print_r2($tempArray);
            }
            unset($tempArray);
        }
        $this->print_r2($totalArray);
        return $totalArray;
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {
        error_reporting(0);
//$this->config['appDebug'] = true;
        $resultMyTripleAAA = $this->companyUserLogin($user, $password);

        if (!$resultMyTripleAAA) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . "<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "MyTripleAAA login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo "LOGIN CONFIRMED<br>";
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage);
        $dom->preserveWhiteSpace = false;
//echo "BBBB" . $this->mainPortalPage;	
        /*
          $str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed SHOULD SHOW LIST OF INVESTMENTS
          echo "AAAAA" . $str;
          $dom = new DOMDocument;
          $dom->loadHTML($str);
          $dom->preserveWhiteSpace = false;
         */


// Get global data
        $infoBodys = $this->getElements($dom, "div", "class", "panel-info-body");
        $tempProfitibility = $this->getElements($infoBodys[2], "div", "class", "panel-info-cell-value panel-info-cell-value-big");
        $tempArray['global']['profitibility'] = $this->getPercentage($tempProfitibility[0]->nodeValue);
//echo __FILE__ . " " . __LINE__ . "<br>";
        $tempWallet = $this->getElements($infoBodys[5], "div", "class", "panel-info-cell-value");
        $tempArray['global']['myWallet'] = $this->getMonetaryValue($tempWallet[0]->nodeValue);

        $str = $this->getCompanyWebpage();  // page "mi-posicion/cartera-viva"
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
//echo __FILE__ . " " . __LINE__ . "<br>";

        $baseUrl = array_shift($this->urlSequence);
        echo "baseUrl = $baseUrl<br>";
        $tables = $this->getElements($dom, "table", "id", "tablaPaginadaInversiones");
        $tbodys = $this->getElements($tables[0], "tbody");
        $trs = $this->getElements($tbodys[0], "tr"); // table with all active investments
//echo __FILE__ . " " . __LINE__ . "<br>";
        $numberOfInvestments = 0;
        foreach ($trs as $key => $tr) {  // cycle through all the investments and get the data, including amortization table
            $numberOfInvestments = $numberOfInvestments + 1;
            $tds = $this->getElements($tr, "td");
//echo __FILE__ . " " . __LINE__ . "<br>";
            $data1[$key]['loanId'] = trim($tds[0]->nodeValue);     // Get decimals of loanId
            $data1[$key]['interest'] = $this->getPercentage($tds[3]->nodeValue);
            $data1[$key]['invested'] = $this->getMonetaryValue($tds[1]->nodeValue);
            $data1[$key]['date'] = trim($tds[4]->nodeValue);
//echo __FILE__ . " " . __LINE__ . "<br>";	
// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED	
            $tempStatus = trim($tds[6]->nodeValue);
            if (strncasecmp($tempStatus, "Vivo / Al", 9) == 0) {
                $data1[$key]['status'] = OK;
            }
            if (strncasecmp($tempStatus, "Vivo / Retras", 13) == 0) {
                $data1[$key]['status'] = PAYMENT_DELAYED;
            }
            if (strncasecmp($tempStatus, "En retraso", 10) == 0) {
                $data1[$key]['status'] = PAYMENT_DELAYED;
            }

            if (strncasecmp($tempStatus, "En mora", 7) == 0) {
                $data1[$key]['status'] = DEFAULTED;
            }
//echo __FILE__ . " " . __LINE__ . "<br>";
            $as = $this->getElements($tds[10], "a");
            $tempUrl = $baseUrl . $as[0]->getAttribute("href");
            $str = $this->getCompanyWebpage($tempUrl);     // Load amortization Table

            $domAmortizationTable = new DOMDocument;
            $domAmortizationTable->loadHTML($str);
            $domAmortizationTable->preserveWhiteSpace = false;
            $tempAmortizationData = $this->getElements($domAmortizationTable, "table", "id", "tablaPaginadaCuotas"); // only 1 found
            $amortizationData = $this->getElements($tempAmortizationData[0], "tr"); // only 1 found
//echo __FILE__ . " " . __LINE__ . "<br>";
// deal with amortization table and normalize the loan state
            $mainIndex = -1;
            foreach ($amortizationData as $key1 => $trAmortizationTable) {
                $mainIndex = $mainIndex + 1;
                $subIndex = -1;
                $tdsAmortizationTable = $trAmortizationTable->getElementsByTagName('td');
                foreach ($tdsAmortizationTable as $tdAmortizationTable) {
                    $subIndex = $subIndex + 1;
                    if ($subIndex == 8) {
                        $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
                        /*
                          getLoanState($actualState)
                         */
                    } else {
                        $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
                    }
                }
            }
//echo "TABLE = ";
//$this->print_r2($amortizationTable);		

            $data1[$key]['commission'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 6, 8);
            $data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 2, 8);
            $data1[$key]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 3, 8);
            $data1[$key]['profitGained'] = $data1[$key]['profitGained'] + $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 4, 8);
//echo __FILE__ . " " . __LINE__ . "<br>";		
            $data1[$key]['duration'] = count($amortizationTable) . " " . __('Meses');

            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
            $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] +
                    $this->getMonetaryValue($tds[8]->nodeValue);
            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
                    $data1[$key]['profitGained'];
            $tempArray['global']['totalInvestments'] = $tempArray['global']['totalInvestments'] +
                    $data1[$key]['invested'];
        }
        $tempArray['global']['investments'] = $numberOfInvestments;
//echo __FILE__ . " " . __LINE__ . "<br>";
        $tempArray['investments'] = $data1;
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
//user = vnamm78@hotmail.com
//pw = Vania2016

        $tempCredentials = array();
        $credentials = array();
        $credentials['emailAcceso'] = $user;
        $credentials['passAcceso'] = $password;

        $str = $this->getCompanyWebpage();  // Go to home page of the company

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $hiddenInputFields = $this->getElements($dom, "input", "type", "hidden");

        foreach ($hiddenInputFields as $hiddenInputField) {
            $tempCredentials[$hiddenInputField->getAttribute('name')] = $hiddenInputField->getAttribute('value');
        }

        $credentials['token'] = $tempCredentials['token'];
        $credentials['paginaOrigen'] = $tempCredentials['paginaOrigen'];
        $credentials['comprobar'] = "Entrar";
        $credentials['_sourcePage'] = $tempCredentials['_sourcePage'];
        $credentials['__fp'] = $tempCredentials['__fp'];
        ;

        $str = $this->doCompanyLogin($credentials);

        // check if user actually has entered the portal of the company
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $labels = $this->getElements($dom, "a", "href", "/mi-posicion/resumen");

        foreach ($labels as $label) {
            if (strcasecmp($label->nodeValue, "Resumen") == 0) {
                $this->mainPortalPage = $str;
                return 1;  // logged in
            }
        }
        return 0;  // Credential error
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
     * 	@param	string		html of loanstate
     * 	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * NOT TESTED FRO MYTRIPLEAAA
     */
    function getLoanState($actualState) {
        if (empty($actualState)) {
            return PENDIENTE;
        }
        $loanStates = array("al d" => OK,
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

}

?>