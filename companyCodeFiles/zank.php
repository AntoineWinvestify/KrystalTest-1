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
 * Contains all the code required for accessing the website of "Zank"
 *
 * 
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-08-04
 * @package


function calculateLoanCost()										[OK, tested]
function collectCompanyMarketplaceData()								[OK, tested]
function companyUserLogin()										[OK, tested]
function companyUserLogout										[OK, tested]
function collectUserInvestmentData()									[OK, tested]
parallelization on collectUserInvestmentData                                                            [OK, tested]

2016-08-04	  version 2016_0.1
Basic version

2016-11-30	  version 2016_0.2
* Zank introduced csrf code for improved security


2017-04-25
Estado amortizado

2017-04-26
Total invertido correcto, fecha

2017-05-16      version 2017_0.3

* Added parallelization to collectUserInvestmentData
* Added dom verification to collectUserInvestmentData
 
2017/06/01
* Added loop when we take json investments                                     [OK, STILL TO CHECK]

2017/06/22
 * Added mechanism to take more than 100 investments by Json
 
  Pending:
  Fecha en duda


 */

class zank extends p2pCompany {
    
    private $credentials = array();
    private $userId;
    private $resultMiZank = false;
    private $url;
    private $start = 0;

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how much it will cost in total to obtain a loan for a certain amount
     * 	from a company. This includes fixed fee amortization fee(s) etc.
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in months) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 2% of requested amount with a minimum of 20 €	Checked: 25-08-2016

        $minimumCommission = 12000;   // in  €cents

        $fixedCost = 2 * $amount / 100;
        if ($fixedCost < $minimumCommission) {
            $fixedCost = $minimumCommission;
        }

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }

    /**
     *
     * 	Collects the marketplace data.
     * 	ZANK is special as one has to logon in order to see all the details of the offers in their marketplace
     * 	@return array	Each investment option as an element of an array
     *
     */
    function collectCompanyMarketplaceData() {
        echo __FUNCTION__ . __LINE__ . "<br>";

        $result = $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);
        echo __FUNCTION__ . __LINE__ . "<br>";
//set_time_limit(25);		// Zank is very very slow
        echo $result;

        if (!$result) {   // Error while logging in
            echo __FUNCTION__ . __LINE__ . "<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while entering user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

        echo __FUNCTION__ . __LINE__ . "start with first read<br>";
        $str = $this->getCompanyWebpage();
//print_r($str);
        echo __FUNCTION__ . __LINE__ . "start with second read<br>";
//	$str = $this->getCompanyWebpage('https://www.zank.com.es/inversor/listaPrestamosAjax');		
        $str = $this->getCompanyWebpage();
//print_r($str);
        echo __FUNCTION__ . __LINE__ . "<br>";
        $totalArray = array();
        $pos1 = stripos($str, '[');
        $pos2 = stripos($str, ']');
        $resultPreJSON = substr($str, $pos1, ($pos2 - $pos1 + 1));

        $jsonResults = json_decode($resultPreJSON, true);
//print_r($jsonEntry);
        foreach ($jsonResults as $jsonEntry) {
            $tempArray = array();
            $tempArray['marketplace_loanReference'] = strip_tags($jsonEntry['Prestamo']);
            $tempArray['marketplace_category'] = strtoupper(strip_tags($jsonEntry['Categoria']));
            $tempArray['marketplace_rating'] = strtoupper(strip_tags($jsonEntry['Categoria']));
            $tempArray['marketplace_interestRate'] = $this->getPercentage(strip_tags($jsonEntry['Rentabilidad']));

            $tempInformation = explode("€", strip_tags($jsonEntry['Informacion']));
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($tempInformation[0]);

            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($tempInformation[1]);

            $dom = new DOMDocument;
            $dom->loadHTML($jsonEntry['Completado']);
            $dom->preserveWhiteSpace = false;
            $divs = $dom->getElementsByTagName('div');


            /* 	
              <div class="tabla-faltan info-tooltip clompletado">
              <a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
              23
              <i class="fa fa-user">
              </i>
              </a>
              </div>

              <div class="progress">
              <div class="progress-bar progress-bar-striped active" role="progressbar" style="width:24.66%">24,66 %
              </div>
              </div>

              <div class="tabla-faltan text-warning">Quedan 30 días
              </div>

              -------------------

              <div class="tabla-faltan info-tooltip clompletado">
              <a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
              16
              <i class="fa fa-user">
              </i><
              /a>
              </div>

              <div class="progress">
              <div class="progress-bar progress-bar-success" role="progressbar" style="width:100%">Completado
              </div>
              </div>

             */



            $index = 0;
            foreach ($divs as $div) {
                switch ($index) {
                    case 0:
                        $tempArray['marketplace_numberOfInvestors'] = strtoupper($div->nodeValue);
                        break;
                    case 1:
                        if (stristr(trim($div->nodeValue), "%") == true) {
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($div->nodeValue);
                        } else {
                            $tempArray['marketplace_subscriptionProgress'] = 10000;  // completed, retrasado orr amortización ..
                        }
                        break;
                    case 2:
                        // Error in HTML of ZANK website source. It generates and extra "/div" tag. Do not do anything
                        break;
                    case 4:  //
                        list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
                        break;
                    default:
                }
                $index++;
            }

            $dom = new DOMDocument;
            $dom->loadHTML($jsonEntry['Finalidad']);
            $dom->preserveWhiteSpace = false;

            $as = $dom->getElementsByTagName('a');
            foreach ($as as $a) {
                $tempArray['marketplace_purpose'] = $a->getAttribute('data-original-title');
            }
            $totalArray[] = $tempArray;
            echo __FILE__ . " " . __LINE__ . "<br>";
            $this->print_r2($tempArray);
            unset($tempArray);
        }
        return $totalArray;
    }
    
    /**
    * Collects the investment data of the user
    *	
    * @param string $str   It is the web converted to string of the company
    * @return array	Data of each investment of the user as an element of an array
    */
    function collectUserInvestmentDataParallel($str) {
    
    
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // needed so I can read the csrf code
                break;
            case 1:
                //Change account
                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                //$this->credentials['_username'] = "Klauskuk@gmail.com";
                //$this->credentials['_password'] = "P2Pes2017";

                // get login page
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                $this->verifyNodeHasElements($forms);
                $index = 0;
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                
                foreach ($forms as $form) {
                    $index = $index + 1;
                    if ($index == 1) {
                        continue;
                    }
                    $inputs = $form->getElementsByTagName('input');
                    $this->verifyNodeHasElements($inputs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                            $this->credentials[$name] = $input->getAttribute('value');
                        }
                    }
                    
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                break;
            case 2:
                //This is an error because we don't verify if we have entered
                if ($str == 200 or $str == 103) {
                    //echo "CODE 103 or 200 received, so do it again , OK <br>";
                    $this->idForSwitch++;
                    $this->doCompanyLoginMultiCurl($this->credentials);
                    //$this->mainPortalPage = $str;
                    $this->resultMiZank = true;
                }
                break;
            case 3:
                
                $this->mainPortalPage = $str;
                
                //echo "user = $user and pw = $password<br>";
                if (!$this->resultMiZank) {			// Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__  . " \n";
                    $tracings .= "Zank login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    //fix this problem
                    return $this->getError(__LINE__, __FILE__);
                }
                echo "LOGIN CONFIRMED";
                // We are at page: "MI ZANK". Look for the "internal user identification"
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                $this->verifyNodeHasElements($scripts);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                
                foreach ($scripts as $script) {
                    $position = stripos($script->nodeValue, "$.ajax");
                    if ($position !== false) {  // We found an entry
                        echo "ENTRY FOUND";
                        break;
                    }
                }
                $testArray = explode(":", $script->nodeValue);
                $this->print_r2($testArray);
                $this->userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

                if (!is_numeric($this->userId)) {
                    echo "<br>An error has occured, could not find internal userId<br>";
                }

                $needle = "kpi_panel";

                $index = 0;
                $ps = $dom->getElementsByTagName('p');
                /*if ($ps.length > 0) {
                    Verify there are som elements
                }*/
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($ps as $p) {
                    $class = trim($p->getAttribute('class'));
                    $position = stripos($class, $needle);
                    if ($position !== false) {  // found a kpi
                        switch ($index) {
                            case 0:
                                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 1:
                                $this->tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 2:
                                $this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 4:
                                $this->tempArray['global']['profitibility'] = $this->getPercentage($p->nodeValue);
                                break;
                        }
                        $index++;
                    }
                }
                // goto page "MI CARTERA"
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // load Webpage into a string variable so it can be parsed	
                break;
                
            case 4:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $needle = "kpi_panel";
                $index = 0;

                // Look for the kpi's 
                $ps = $dom->getElementsByTagName('p');
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($ps as $p) {
                    $class = trim($p->getAttribute('class'));
                    $position = stripos($class, $needle);
                    if ($position !== false) {  // found a kpi
                        switch ($index) {
                            case 0:
                                $this->tempArray['global']['totalInvestments'] = $this->getMonetaryValue($p->nodeValue); // Money still tied up in active investment(s)
                                break;
                            case 1:
                                $this->tempArray['global']['activeInvestments'] = $p->nodeValue; // The number of active investments
                                break;
                        }
                        $index++;
                    }
                }

                // Download the list of the individual investments of this user
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 5:
                $url = array_shift($this->urlSequence);
                $this->url = $url . $this->userId . "/0";
                //We put a postitem into the curl call because initialy we don't take the first investment
                //Nor more than 25 investment, so we alter those values
                $form = [
                    "length" => 100,
                    "start" => $this->start];

                $this->idForSwitch++;
                //Add here the Edu's adaption to the url, we add the form into the call
                $this->getCompanyWebpageJsonMultiCurl($this->url, $form);
                break;
            case 6:
                $temp = json_decode($str, $assoc = true);
                $this->numberOfInvestments = 0;
                $numberJsonInvestments = count($temp['data']);
                foreach ($temp['data'] as $key => $item) {  // mapping of the data to a generic, own format.
                    // Keep all which don't have status == "amortizado"
                    //$this->data1[$key]['status'] = 10;    // dummy value											
                    if (strpos($item['Estado'], "Retrasado")) {
                        $this->data1[$key]['status'] = PAYMENT_DELAYED;
                    }
                    if (strpos($item['Estado'], "Amortizaci")) {
                        $this->data1[$key]['status'] = OK;
                    }

                    if (strpos($item['Estado'], "Amortizado")) {
                        $this->data1[$key]['status'] = TERMINATED_OK;
                    }

                    //		if (!($data1[$key]['status'] <> OK OR $data1[$key]['status'] <> PAYMENT_DELAYED)) {
                    //			continue;									// flush non required loans
                    //		echo "FLUSH";
                    //		}

                    if ($this->data1[$key]['status'] == TERMINATED_OK) {
                        unset($this->data1[$key]);
                        continue;
                    }

                    $this->numberOfInvestments++;
                    $day = 1; //substr($item['Fecha'],0,2);
                    
                    if ($item['Plazo'] <= 50) {
                        $month = substr($item['Fecha'], 3, 2) + 1;

                        $year = substr($item['Fecha'], 6, 4);
                        if ($month == 13) {
                            $month = 1;
                            $year++;
                        }
                    }
                    if ($item['Plazo'] >= 50) {
                        $month = substr($item['Fecha'], 3, 2) - 1;

                        $year = substr($item['Fecha'], 6, 4);
                        if ($month == 0) {
                            $month = 1;
                        }
                    }

                    //if($month==13){$month=1; $year = $year+1; } 
                    $date = $year . "/" . $month . "/" . $day;
                    $date = date('d/m/Y', strtotime("+" . $item['Plazo'] . "months", strtotime($date)));
                    //$date = date('d/m/Y', strtotime("+".$item['Plazo']." months", strtotime($date)));

                    $this->data1[$key]['loanId'] = $item['Prestamo'];
                    $this->data1[$key]['dateOriginal'] = $item['Fecha'];
                    $this->data1[$key]['date'] = $date;
                    $this->data1[$key]['interest'] = $this->getPercentage($item['Rentabilidad']);
                    $this->data1[$key]['invested'] = $this->getMonetaryValue($item['Inversion']);
                    $this->data1[$key]['amortized'] = $this->getMonetaryValue($item['Amortizado']);
                    $this->data1[$key]['profitGained'] = $this->getPercentage($item['InteresesOrdinarios']);
                    $this->data1[$key]['duration'] = $item['Plazo'] . " Meses";
                    $this->data1[$key]['commission'] = $this->getMonetaryValue($item['Comision']);
                    $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$key]['invested'];
            }
            if ($numberJsonInvestments != 0 && $numberJsonInvestments % 100 == 0) {
                //If investments are 100, we verify that there is no more, so we recall starting at 100 investments
                $this->start = $this->start + 100;
                $form = [
                "length" => 100,
                "start" => $this->start];

                $this->idForSwitch = 6;
                //Add here the Edu's adaption to the url
                $this->getCompanyWebpageJsonMultiCurl($this->url, $form);

            }
            else {
                $this->data1 = array_values($this->data1);
                $this->tempArray['global']['investments'] = count($this->data1);
                //echo __FILE__ . " " . __LINE__ . "<br>";
                $this->tempArray['investments'] = $this->data1;
                $this->print_r2($this->tempArray);
                return $this->tempArray; 
            }
                
                                    
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {
// velascogestorpatrimonial@hotmail.com
// D547336  wrong
        $resultMiZank = $this->companyUserLogin($user, $password);
        echo "user = $user and pw = $password<br>";
        if (!$resultMiZank) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Zank login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            return;
        }
        echo "LOGIN CONFIRMED";
// We are at page: "MI ZANK". Look for the "internal user identification"
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $position = stripos($script->nodeValue, "$.ajax");
            if ($position !== false) {  // We found an entry
                echo "ENTRY FOUND";
                break;
            }
        }
        $testArray = explode(":", $script->nodeValue);
        $this->print_r2($testArray);
        $userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

        if (!is_numeric($userId)) {
            echo "<br>An error has occured, could not find internal userId<br>";
        }

        $needle = "kpi_panel";

        $index = 0;
        $ps = $dom->getElementsByTagName('p');
        foreach ($ps as $p) {
            $class = trim($p->getAttribute('class'));
            $position = stripos($class, $needle);
            
            if ($position !== false) {  // found a kpi
                switch ($index) {
                    case 0:
                        $tempArray['global']['myWallet'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 1:
                        $tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 2:
                        $tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 4:
                        $tempArray['global']['profitibility'] = $this->getPercentage($p->nodeValue);
                        break;
                }
                $index++;
            }
        }

// goto page "MI CARTERA"
        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $needle = "kpi_panel";
        $index = 0;

// Look for the kpi's 
        $ps = $dom->getElementsByTagName('p');
        foreach ($ps as $p) {
            $class = trim($p->getAttribute('class'));
            $position = stripos($class, $needle);
            if ($position !== false) {  // found a kpi
                switch ($index) {
                    case 0:
                        $tempArray['global']['totalInvestments'] = $this->getMonetaryValue($p->nodeValue); // Money still tied up in active investment(s)
                        break;
                    case 1:
                        $tempArray['global']['activeInvestments'] = $p->nodeValue; // The number of active investments
                        break;
                }
                $index++;
            }
        }

// Download the list of the individual investments of this user
        $str = $this->getCompanyWebpage();

// build the Web URL for downloading the list of the individual investments of this user
        $url = array_shift($this->urlSequence);
        $url = $url . $userId . "/0";
        //Start is the value from we get the investments
        $start = 0;
        $form = [
            "length" => 100,
            "start" => $start];
        //This value is 100 to enter in the while but when we are inside we change to the real number of investments
        $numberJsonInvestments = 100;
        while ($numberJsonInvestments % 100 == 0) {
            $str = $this->getCompanyWebpageJson($url, $form);
            $temp = json_decode($str, $assoc = true);
            //We take the real number of investments
            $numberJsonInvestments = count($temp['data']);

            $numberOfInvestments = 0;
            foreach ($temp['data'] as $key => $item) {  // mapping of the data to a generic, own format.										// Keep all which don't have status == "amortizado"
                //$data1[$key]['status'] = 10;  // dummy value											
                if (strpos($item['Estado'], "Retrasado")) {
                    $data1[$key]['status'] = PAYMENT_DELAYED;
                }
                if (strpos($item['Estado'], "Amortizaci")) {
                    $data1[$key]['status'] = OK;
                }
                if (strpos($item['Estado'], "Amortizado")) {
                    $data1[$key]['status'] = TERMINATED_OK;
                }

    //		if (!($data1[$key]['status'] <> OK OR $data1[$key]['status'] <> PAYMENT_DELAYED)) {
    //			continue;									// flush non required loans
    //		echo "FLUSH";
    //		}
                if ($data1[$key]['status'] == TERMINATED_OK) {
                    unset($data1[$key]);
                    continue;
                }

                $numberOfInvestments = $numberOfInvestments + 1;
                $day = 1; //substr($item['Fecha'],0,2);
                if ($item['Plazo'] <= 50) {
                    $month = substr($item['Fecha'], 3, 2) + 1;

                    $year = substr($item['Fecha'], 6, 4);
                    if ($month == 13) {
                        $month = 1;
                        $year++;
                    }
                }
                if ($item['Plazo'] >= 50) {
                    $month = substr($item['Fecha'], 3, 2) - 1;

                    $year = substr($item['Fecha'], 6, 4);
                    if ($month == 0) {
                        $month = 1;
                    }
                }


                //if($month==13){$month=1; $year = $year+1; } 
                $date = $year . "/" . $month . "/" . $day;
                $date = date('d/m/Y', strtotime("+" . $item['Plazo'] . "months", strtotime($date)));
                //$date = date('d/m/Y', strtotime("+".$item['Plazo']." months", strtotime($date)));
                $data1[$key]['loanId'] = $item['Prestamo'];
                $data1[$key]['dateOriginal'] = $item['Fecha'];
                $data1[$key]['date'] = $date;
                $data1[$key]['interest'] = $this->getPercentage($item['Rentabilidad']);
                $data1[$key]['invested'] = $this->getMonetaryValue($item['Inversion']);
                $data1[$key]['amortized'] = $this->getMonetaryValue($item['Amortizado']);
                $data1[$key]['profitGained'] = $this->getPercentage($item['InteresesOrdinarios']);
                $data1[$key]['duration'] = $item['Plazo'] . " Meses";
                $data1[$key]['commission'] = $this->getMonetaryValue($item['Comision']);
                $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
            }
            //If the investments are 100, we repeat the process to take more investments
            //And increasea start by 100
            if ($numberJsonInvestments % 100 == 0) {
                $start = $start + 100;
            }
        }
        
        $data1 = array_values($data1);
        $tempArray['global']['investments'] = count($data1);
        $tempArray['investments'] = $data1;

        $this->print_r2($tempArray);
        return $tempArray;
    }

    /**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account.
     * 	For Zank we actually have to do a "double" login. The first login returns a 200 OK
     * 	and the 
     * 	
     * 	@return boolean	true: user has succesfully logged in 
     * 					false: user could not log in
     * 	
     */
    function companyUserLogin($user, $password) {
        $totalArray = array();
        $credentials = array();

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;

// get login page
        $str = $this->getCompanyWebpage();  // needed so I can read the csrf code
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');

        $index = 0;
        foreach ($forms as $form) {
            $index = $index + 1;
            if ($index == 1) {
                continue;
            }
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                    $credentials[$name] = $input->getAttribute('value');
                }
            }
        }

        $str = $this->doCompanyLogin($credentials);

        if ($str == 200 or $str == 103) {
//		echo "CODE 103 or 200 received, so do it again , OK <br>";
            $str = $this->doCompanyLogin($credentials);
            $this->mainPortalPage = $str;
            return true;
        }
        return false;
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
     * Function that is used to pick up inversions by 100 instead of 25 with a json
     * The normal function of Zank is to give 25 but we change the petition with the posts item
     * with curl
     * @param string $url It is the url of the company, if it's empty we take the url from $urlSquence
     * @return string $str It is the website resulted of the curl petition
     */
    function getCompanyWebpageJson($url, $form) {

        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }

        if (!empty($this->testConfig['active']) == true) {    // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                echo "currentScreen = $currentScreen";
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $curl = curl_init();

        if (!$curl) {
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

        if ($this->config['postMessage'] == true) {
            curl_setopt($curl, CURLOPT_POST, true);
//    echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

// check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            echo "EXTRA HEADERS TO BE ADDED<br>";
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);      // reset fields
        }
        
        foreach ($form as $key => $value) {
            $postItems[] = $key . '=' . $value;
        }
        $postString = implode('&', $postItems);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);


        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Fail the cURL request if response code = 400 (like 404 errors) 
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);   // important
        $result = curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);    // Important
        // Fetch the URL and save the content
        $str = curl_exec($curl);
        if (!empty($this->testConfig['active']) == true) {
            print_r(curl_getinfo($curl));
            echo "<br>";
            print_r(curl_error($curl));
            echo "<br>";
        }

        if ($this->config['appDebug'] == true) {
            echo "VISITED COMPANY URL = $url <br>";
        }
        if ($this->config['tracingActive'] == true) {
            $this->doTracing($this->config['traceID'], "WEBPAGE", $str);
        }
        return $str;
    }
    
     /**
     * Function that is used to pick up inversions by 100 instead of 25 with a json
     * The normal function of Zank is to give 25 but we change the petition with the posts item
     * with curl
     * @param string $url It is the url of the company, if it's empty we take the url from $urlSquence
     * @return string $str It is the website resulted of the curl petition
     */
    function getCompanyWebpageJsonMultiCurl($url, $form) {

        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }
        $this->errorInfo = $url;
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                echo "currentScreen = $currentScreen";
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $request = new \cURL\Request();

        if ($this->config['postMessage'] == true) {
            $request->getOptions()
                ->set(CURLOPT_POST, true);
        //echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

        // check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            echo "EXTRA HEADERS TO BE ADDED<br>";
            $request->getOptions()
                //->set(CURLOPT_HEADER, true) Esto fue una prueba, no funciona, quitar
                ->set(CURLOPT_HTTPHEADER, $this->headers);
            
            unset($this->headers);   // reset fields
        }
        
        foreach ($form as $key => $value) {
            $postItems[] = $key . '=' . $value;
        }
        $postString = implode('&', $postItems);

        $request->_page = $this->idForQueue . ";". $this->idForSwitch . ";WEBPAGE";
        $request->getOptions()
                // Set the file URL to fetch through cURL
                ->set(CURLOPT_URL, $url)
                // Set a different user agent string (Googlebot)
                ->set(CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36")
                // Follow redirects, if any
                ->set(CURLOPT_FOLLOWLOCATION, true)
                ->set(CURLOPT_POSTFIELDS, $postString)
                // Fail the cURL request if response code = 400 (like 404 errors) 
                ->set(CURLOPT_FAILONERROR, true)
                
                ->set(CURLOPT_AUTOREFERER, true)
                //->set(CURLOPT_VERBOSE, 1)
                // Return the actual result of the curl result instead of success code
                ->set(CURLOPT_RETURNTRANSFER, true)
                // Wait for 10 seconds to connect, set 0 to wait indefinitely
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                // Execute the cURL request for a maximum of 50 seconds
                ->set(CURLOPT_TIMEOUT, 100)
                // Do not check the SSL certificates
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name) // important
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name); // Important

        //Add the request to the queue in the marketplaces controller
        $this->marketplaces->addRequetsToQueueCurls($request);
        
        if ($this->config['appDebug'] == true) {
            echo "VISITED COMPANY URL = $url <br>";
        }
    }


}

?> 
