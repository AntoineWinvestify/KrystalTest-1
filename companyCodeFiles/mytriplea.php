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
 *
 * function calculateLoanCost()										[Not OK]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * introduced the "rating" by doing an additional read of webpage with the detailed view of the loanrequest [OK]
 * function companyUserLogout()                                                                            [OK, tested]
 * parallelization                                                                                         [OK, tested]
 *
 * 2016-08-04	  version 2016_0.1
 * Basic version
 * introduced the "rating" by doing an additional read of webpage with the detailed view of the loanrequest [OK] 
 * 2017-04-18
 * Rating fixed
 * 2017-05-16      version 2017_0.2
 * Added parallelization
 *
 * 2017/08/04
 * Code adaptation for 100%
 *      collectCompanyMarketplaceData   -   Pagination loop added
 *      collectHistorical       -       Added
 * 
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 * Pending
 * More Ratings
 *
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
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) {

        $page = 0;
        $reading = true;
        $readController = 0;
        $investmentController = false;
        $totalArray = array();
        $url = array_shift($this->urlSequence);

        $str = $this->getCompanyWebpage($url);  // load Webpage into a string variable so it can be parsed;

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        //Structure comparation, not in the same place where we colect the data because we get the mytriplea data from ajax response.
        if ($page == 0) { //Compare structures, only compare the first element

            $structureRevision = $this->htmlRevision($structure,'article',null,null,null, array('dom' => $dom, 'tag' => 'div', 'attribute' => 'class', 'attrValue' => 'row divTarjetasPymeAjax' ));
            if($structureRevision[1]){
                $totalArray = false; //Stop reading in error
                $reading = false;
            }   
        
        }                
                    
            


        while ($reading) { //Pagination loop
            $investmentNumber = 0;
            $form = [//MyTripleA is like zank, need curl.
                'cargarMas' => true, //This must by true
                'numeroPaginaMostrar' => $page, //Page number, first page is 0
            ];

            $str = $this->getCompanyWebpageAjax($url, $form);  // load Webpage into a string variable so it can be parsed;

            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;

            $rows = $dom->getElementsByTagName('article');


            if ($totalArray !== false) {
                foreach ($rows as $key => $row) {

                    $h3 = $row->getElementsByTagName('h3');  // Only 1 'h3' will be encountered
                    foreach ($h3 as $item) {
                        
                    }
                    $tempArray['marketplace_country'] = 'ES';
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
                        if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoFINALIZADA' || $checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoPENDIENTE' || $checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoPRORROGADA' || $checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoCOMPLETADA') {

                            if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoFINALIZADA') {
                                $tempArray['marketplace_statusLiteral'] = 'Formalizado';
                                $tempArray['marketplace_status'] = CONFIRMED;
                            } else if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoCOMPLETADA') {
                                $tempArray['marketplace_statusLiteral'] = 'Completado';
                                $tempArray['marketplace_status'] = PERCENT;
                            }

                            if (!$checkedAttribute) {
                                $tempArray['marketplace_rating'] = 'SGR';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionA.png');") {
                                $tempArray['marketplace_rating'] = 'A';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionA_MAS.png');") {
                                $tempArray['marketplace_rating'] = 'A+';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionB.png');") {
                                $tempArray['marketplace_rating'] = 'B';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionB_MAS.png');") {
                                $tempArray['marketplace_rating'] = 'B+';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionC.png');") {
                                $tempArray['marketplace_rating'] = 'C';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionC_MAS.png');") {
                                $tempArray['marketplace_rating'] = 'C+';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD.png');") {
                                $tempArray['marketplace_rating'] = 'D';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD_MAS.png');") {
                                $tempArray['marketplace_rating'] = 'D+';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionE.png');") {
                                $tempArray['marketplace_rating'] = 'E';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionE_MAS.png');") {
                                $tempArray['marketplace_rating'] = 'E+';
                            } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracion.png');") {
                                $tempArray['marketplace_rating'] = 'Vacio';
                            }
                        }
                    }

// stored all available information in array, but rating is still missing, so fetch it from the detailed view		
                    if (!empty($tempArray['marketplace_loanReference'])) {

                        if ($tempArray['marketplace_subscriptionProgress'] < 10000) {
                            $tempArray['marketplace_statusLiteral'] = 'En proceso';
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
                        } else {
                            foreach ($companyBackup as $inversionBackup) { //If we have the completed investment in backup with the same status
                                if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                    $readController++;
                                    $investmentController = true;
                                }
                            }
                        }
                        $investmentNumber++; //Add investment
                        if ($investmentController) { //Don't save a already existing investment
                            unset($tempArray);
                            $investmentController = false;
                        } else {
                            $totalArray[] = $tempArray;
                            unset($tempArray);
                        }
                    }
                    unset($tempArray);
                }
            }
            $page++;
            if ($readController > 12 || $investmentNumber < 12) {
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
     * 
     * @param Array $structure
     * @param Int $pageNumber
     * @return Array
     */
    function collectHistorical($structure, $pageNumber) {


        $totalArray = array();
        $investmentNumber = 0;
        $max = 12;

        $url = array_shift($this->urlSequence);

        $str = $this->getCompanyWebpage($url);  // load Webpage into a string variable so it can be parsed;

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        //Structure comparation, not in the same place where we colect the data because we get the mytriplea data from ajax response.
        if ($pageNumber == 0) { //Compare structures, only compare the first element
            $structureRevision = $this->htmlRevision($structure,'article',null,null,null, array('dom' => $dom, 'tag' => 'div', 'attribute' => 'class', 'attrValue' => 'row divTarjetasPymeAjax' ));
            if($structureRevision[1]){
                $totalArray = false; //Stop reading in error
                $pageNumber = false;
            }     
        }

        if ($pageNumber == 0 && !$structure) { //Save new structure if is first time
            echo 'no structure readed, saving structure <br>';
            $saveStructure = new DOMDocument();
            $container = $this->getElements($dom, 'div', 'class', 'row divTarjetasPymeAjax')[0];
            $clone = $container->cloneNode(TRUE);
            $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
            $structureRevision = $saveStructure->saveHTML();
        }
        
        
        

        $form = [//MyTripleA is like zank, need curl.
            'cargarMas' => true, //Must be true
            'numeroPaginaMostrar' => $pageNumber, //Start with 0 
        ];

        $str = $this->getCompanyWebpageAjax($url, $form);  // load ajax reponse
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $rows = $dom->getElementsByTagName('article');

        if ($totalArray !== false) {
            foreach ($rows as $key => $row) {

                $tempArray['marketplace_country'] = 'ES';
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

                        if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoFINALIZADA') {
                            $tempArray['marketplace_statusLiteral'] = 'Formalizado';
                            $tempArray['marketplace_status'] = CONFIRMED;
                        } else if ($checkedClass == 'avaladoTarjeta avaladoTarjetaEstadoCOMPLETADA') {
                            $tempArray['marketplace_statusLiteral'] = 'Completado';
                            $tempArray['marketplace_status'] = PERCENT;
                        }

                        if (!$checkedAttribute) {
                            $tempArray['marketplace_rating'] = 'SGR';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionA.png');") {
                            $tempArray['marketplace_rating'] = 'A';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionA_MAS.png');") {
                            $tempArray['marketplace_rating'] = 'A+';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionB.png');") {
                            $tempArray['marketplace_rating'] = 'B';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionB_MAS.png');") {
                            $tempArray['marketplace_rating'] = 'B+';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionC.png');") {
                            $tempArray['marketplace_rating'] = 'C';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionC_MAS.png');") {
                            $tempArray['marketplace_rating'] = 'C+';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD.png');") {
                            $tempArray['marketplace_rating'] = 'D';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionD_MAS.png');") {
                            $tempArray['marketplace_rating'] = 'D+';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionE.png');") {
                            $tempArray['marketplace_rating'] = 'E';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracionE_MAS.png');") {
                            $tempArray['marketplace_rating'] = 'E+';
                        } else if ($checkedAttribute == "background-image:url('https://d1b1eeq5q8spqf.cloudfront.net/recursos/images/background/valoracion.png');") {
                            $tempArray['marketplace_rating'] = 'Vacio';
                        }
                    }
                }

                if ($tempArray['marketplace_subscriptionProgress'] < 10000) {
                    $tempArray['marketplace_statusLiteral'] = 'En proceso';
                }

                $investmentNumber++; //Add investmet
                $totalArray[] = $tempArray;
                unset($tempArray);
            }
        }
        echo 'Aqui ' . $investmentNumber;
        if ($investmentNumber < $max) {
            $pageNumber = false;
        } //Stop reading
        else {
            echo 'Advance page ' . $pageNumber;
            $pageNumber++;
            echo 'to ' . $pageNumber;
        }
        $this->print_r2($totalArray);
        return [$totalArray, $pageNumber, null, $structureRevision[0], $structureRevision[2]]; //$pageNumber is the next page, false when we read the last page
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     * Function that is used to pick up inversions page of MytripleA because ajax response
     * @param string $url It is the url of the company, if it's empty we take the url from $urlSquence
     * @return string $str It is the website resulted of the curl petition
     */
    function getCompanyWebpageAjax($url, $form) {

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
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentDataParallel($str) {

        switch ($this->idForSwitch) {
            case 0:
                error_reporting(0);
                //$this->config['appDebug'] = true;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:
                $tempCredentials = array();
                $credentials = array();
                $credentials['emailAcceso'] = $this->user;
                $credentials['passAcceso'] = $this->password;
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $hiddenInputFields = $this->getElements($dom, "input", "type", "hidden");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($hiddenInputFields as $hiddenInputField) {
                    $tempCredentials[$hiddenInputField->getAttribute('name')] = $hiddenInputField->getAttribute('value');
                }

                $credentials['token'] = $tempCredentials['token'];
                $credentials['paginaOrigen'] = $tempCredentials['paginaOrigen'];
                $credentials['comprobar'] = "Entrar";
                $credentials['_sourcePage'] = $tempCredentials['_sourcePage'];
                $credentials['__fp'] = $tempCredentials['__fp'];
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;


            case 2:
                // check if user actually has entered the portal of the company
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $labels = $this->getElements($dom, "a", "href", "/mi-posicion/resumen");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                $resultMyTripleAAA = false;
                foreach ($labels as $label) {
                    if (strcasecmp($label->nodeValue, "Resumen") == 0) {
                        $this->mainPortalPage = $str;
                        $resultMyTripleAAA = true;  // logged in
                    }
                }

                if (!$resultMyTripleAAA) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "MyTripleAAA login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                echo "LOGIN CONFIRMED<br>";
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage);
                $dom->preserveWhiteSpace = false;

                $infoBodys = $this->getElements($dom, "div", "class", "panel-info-body");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                $tempProfitibility = $this->getElements($infoBodys[2], "div", "class", "panel-info-cell-value panel-info-cell-value-big");
                $this->tempArray['global']['profitibility'] = $this->getPercentage($tempProfitibility[0]->nodeValue);
//echo __FILE__ . " " . __LINE__ . "<br>";
                $tempWallet = $this->getElements($infoBodys[5], "div", "class", "panel-info-cell-value");
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($tempWallet[0]->nodeValue);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // page "mi-posicion/cartera-viva"
                break;
            case 3:

                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
//echo __FILE__ . " " . __LINE__ . "<br>";
                //$dom->saveHTMLFile("test1.html");
                $baseUrl = array_shift($this->urlSequence);
                echo "baseUrl = $baseUrl<br>";
                $tables = $this->getElements($dom, "table", "id", "tablaPaginadaInversiones");
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $tbodys = $this->getElements($tables[0], "tbody");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $trs = $this->getElements($tbodys[0], "tr"); // table with all active investments
//echo __FILE__ . " " . __LINE__ . "<br>";
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->numberOfInvestments = 0;
                for ($key = 0; $key < count($trs); $key++) { // cycle through all the investments and get the data, including amortization table
                    $this->numberOfInvestments++;
                    $tds = $this->getElements($trs[$key], "td");
//echo __FILE__ . " " . __LINE__ . "<br>";
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $this->data1[$key]['loanId'] = trim($tds[0]->nodeValue);     // Get decimals of loanId
                    $this->data1[$key]['interest'] = $this->getPercentage($tds[3]->nodeValue);
                    $this->data1[$key]['invested'] = $this->getMonetaryValue($tds[1]->nodeValue);
                    $this->data1[$key]['date'] = trim($tds[4]->nodeValue);
                    $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['activeInInvestments'] + $this->getMonetaryValue($tds[8]->nodeValue);

                    $tempStatus = trim($tds[6]->nodeValue);
                    if (strncasecmp($tempStatus, "Vivo / Al", 9) == 0) {
                        $this->data1[$key]['status'] = OK;
                    }
                    if (strncasecmp($tempStatus, "Vivo / Retras", 13) == 0) {
                        $this->data1[$key]['status'] = PAYMENT_DELAYED;
                    }
                    if (strncasecmp($tempStatus, "En retraso", 10) == 0) {
                        $this->data1[$key]['status'] = PAYMENT_DELAYED;
                    }

                    if (strncasecmp($tempStatus, "En mora", 7) == 0) {
                        $this->data1[$key]['status'] = DEFAULTED;
                    }
//echo __FILE__ . " " . __LINE__ . "<br>";
                    $as = $this->getElements($tds[10], "a");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $this->tempUrl[$key] = $baseUrl . $as[0]->getAttribute("href");
                    //$this->getCompanyWebpage($tempUrl);     // Load amortization Table
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->accountPosition]);     // Load amortization Table
                break;
            case 4:
                $domAmortizationTable = new DOMDocument;
                libxml_use_internal_errors(true);
                $domAmortizationTable->loadHTML($str);
                $domAmortizationTable->preserveWhiteSpace = false;
                $tempAmortizationData = $this->getElements($domAmortizationTable, "table", "id", "tablaPaginadaCuotas"); // only 1 found
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $amortizationData = $this->getElements($tempAmortizationData[0], "tr"); // only 1 found
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                //echo __FILE__ . " " . __LINE__ . "<br>";
                // deal with amortization table and normalize the loan state
                $mainIndex = -1;
                foreach ($amortizationData as $key1 => $trAmortizationTable) {
                    $mainIndex = $mainIndex + 1;
                    $subIndex = -1;
                    $tdsAmortizationTable = $trAmortizationTable->getElementsByTagName('td');
                    /* THIS ELEMENT DOESN'T HAVE TO BE ANALYZED
                      $this->verifyNodeHasElements($tdsAmortizationTable);
                      if (!$this->hasElements) {
                      return $this->getError(__LINE__, __FILE__);
                      } */
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
                $this->data1[$this->accountPosition]['commission'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 6, 8);
                $this->data1[$this->accountPosition]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 2, 8);
                $this->data1[$this->accountPosition]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 3, 8);
                $this->data1[$this->accountPosition]['profitGained'] = $this->data1[$this->accountPosition]['profitGained'] + $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 0, 4, 8);
                //echo __FILE__ . " " . __LINE__ . "<br>";		
                $this->data1[$this->accountPosition]['duration'] = count($amortizationTable) . " " . __('Meses');

                $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$this->accountPosition]['invested'];
                $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] +
                        $this->data1[$this->accountPosition]['profitGained'];
                $this->tempArray['global']['totalInvestments'] = $this->tempArray['global']['totalInvestments'] +
                        $this->data1[$this->accountPosition]['invested'];

                if ($this->accountPosition != ($this->numberOfInvestments - 1)) {
                    $this->idForSwitch = 4;
                    $this->accountPosition++;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->accountPosition]);     // Load amortization Table
                    break;
                } else {
                    $this->tempArray['global']['investments'] = $this->numberOfInvestments;
                    //echo __FILE__ . " " . __LINE__ . "<br>";
                    $this->tempArray['investments'] = $this->data1;
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
                ), array('src', 'href', 'style'));


        $node1 = $this->cleanDom($node1, array(//We only want delete class of the div  tag because contain the status
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class'));

        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'li'), //li lenght can change
        ));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('src', 'href', 'style'));

        $node2 = $this->cleanDom($node2, array(//We only want delete class of the div tag because contain the status
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'li'), //li lenght can change
        ));
        
        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}
