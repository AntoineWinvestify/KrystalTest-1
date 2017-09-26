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
 * 
 * 2017-08-23
 * Created
 */
class finanzarel extends p2pCompany {

    protected $pInstanceGlobal = '';
    protected $credentialsGlobal = array();
 
 
    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }   
    
    
    
    public function getParserConfigTransactionFile() {
        return $this->$valuesFinanzarelTransaction;
    }
 
     public function getParserConfigInvestmentFile() {
        return $this->$valuesFinanzarelInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesFinanzarelAmortization;
    }    


    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY finanzarel DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */



        //Get credentials from form in pfp login page
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $input) {
            //echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
            $name = $input->getAttribute('name');
            switch ($name) {
                case 'p_flow_id':
                    $pFlowId = $input->getAttribute('value');
                    break;
                case 'p_flow_step_id':
                    $pFlowStepId = $input->getAttribute('value');
                    break;
                case 'p_instance':
                    $pInstance = $input->getAttribute('value');
                    break;
                case 'p_page_submission_id':
                    $pPageSubmissionId = $input->getAttribute('value');
                    break;
                case 'p_request':
                    $pRequest = $input->getAttribute('value');
                    break;
                case 'p_reload_on_submit':
                    $pReloadOnSubmit = $input->getAttribute('value');
                    break;
            }
            if ($input->getAttribute('id') == 'pSalt') {
                $pSalt = $input->getAttribute('value');
            }
            if ($input->getAttribute('id') == 'pPageItemsProtected') {
                $pPageItemsProtected = $input->getAttribute('value');
            }
        }


        $credentials['p_json'] = '{"salt":"' . $pSalt . '","pageItems":{"itemsToSubmit":[{"n":"P101_USERNAME","v":"' . $user . '"},{"n":"P101_PASSWORD","v":"' . $password . '"}],"protected":"' . $pPageItemsProtected . '","rowVersion":""}}';
        $credentials['p_flow_id'] = $pFlowId;
        $credentials['p_flow_step_id'] = $pFlowStepId;
        $credentials['p_instance'] = $pInstance;
        $this->pInstanceGlobal = $pInstance;
        $credentials['p_page_submission_id'] = $pPageSubmissionId;
        $credentials['p_request'] = 'Login';
        $credentials['p_reload_on_submit'] = $pReloadOnSubmit;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login

        $url = array_shift($this->urlSequence);
        //echo $url . HTML_ENDOFLINE;
        $str = $this->getCompanyWebpage($url . $pInstance);
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;
        $h2s = $dom->getElementsByTagName('h2');
        foreach ($h2s as $h2) {
            //echo $h2->nodeValue . HTML_ENDOFLINE;
            if (trim($h2->nodeValue) == 'Dashboard') {
                //echo 'ok' . HTML_ENDOFLINE;
                return true;
            }
        }
        return false;
    }
    
    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserGlobalFilesParallel($str = null) {
        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;

                $inputs = $dom->getElementsByTagName('input');
                foreach ($inputs as $input) {
                    echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
                    $name = $input->getAttribute('name');
                    switch ($name) {
                        case 'p_flow_id':
                            $pFlowId = $input->getAttribute('value');
                            break;
                        case 'p_flow_step_id':
                            $pFlowStepId = $input->getAttribute('value');
                            break;
                        case 'p_instance':
                            $pInstance = $input->getAttribute('value');
                            break;
                        case 'p_page_submission_id':
                            $pPageSubmissionId = $input->getAttribute('value');
                            break;
                        case 'p_request':
                            $pRequest = $input->getAttribute('value');
                            break;
                        case 'p_reload_on_submit':
                            $pReloadOnSubmit = $input->getAttribute('value');
                            break;
                    }
                    if ($input->getAttribute('id') == 'pSalt') {
                        $pSalt = $input->getAttribute('value');
                    }
                    if ($input->getAttribute('id') == 'pPageItemsProtected') {
                        $pPageItemsProtected = $input->getAttribute('value');
                    }
                }
                
                $this->credentialsGlobal['p_json'] = '{"salt":"' . $pSalt . '","pageItems":{"itemsToSubmit":[{"n":"P101_USERNAME","v":"' . $this->user . '"},{"n":"P101_PASSWORD","v":"' . $this->password . '"}],"protected":"' . $pPageItemsProtected . '","rowVersion":""}}';
                $this->credentialsGlobal['p_flow_id'] = $pFlowId;
                $this->credentialsGlobal['p_flow_step_id'] = $pFlowStepId;
                $this->credentialsGlobal['p_instance'] = $pInstance;
                $this->credentialsGlobal['p_page_submission_id'] = $pPageSubmissionId;
                $this->credentialsGlobal['p_request'] = 'Login';
                $this->credentialsGlobal['p_reload_on_submit'] = $pReloadOnSubmit;

                //print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentialsGlobal); //do login
                break;
            case 2:
                $url = array_shift($this->urlSequence);
                //echo $url . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . $this->credentialsGlobal['p_instance']);
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;
                $h2s = $dom->getElementsByTagName('h2');
                $resultLogin = false;
                foreach ($h2s as $h2) {
                    echo $h2->nodeValue . HTML_ENDOFLINE;
                    if (trim($h2->nodeValue) == 'Dashboard') {
                        //echo 'ok' . HTML_ENDOFLINE;
                        $resultLogin = true;
                    }
                }
                
                if (!$resultLogin) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Finanzarel login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                echo 'Login ok';
                
                //Get credentials to download the file
                $inputs = $dom->getElementsByTagName('input');
                foreach ($inputs as $input) {
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }
                //print_r($credentials);
                //Get the request to download the file
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $key => $a) {
                    //echo $key . " => " . $a->getAttribute('href') . "   " . $a->nodeValue .  HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Descargar en csv') {
                        $request[] = explode("'", $a->getAttribute('href'))[1];
                        
                    }
                }
                
                $fileUrl =  array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => $credentials['p_flow_step_id'], 
                        'p_instance' => $credentials['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $request[0]);
                print_r($credentialsFile);
                //echo $fileUrl . HTML_ENDOFLINE;
                $fileName = 'Investment';
                //$fileType = 'csv';
                $referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:' . $this->credentialsGlobal['p_flow_step_id'] . ":" . $this->credentialsGlobal['p_instance'];
                //$referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:{$credential_p_flow_step_id}:{$credential_p_instance}';
                $this->baseUrl = 'marketplace.finanzarel.com';
                //$path = 'prueba';
                
                //$this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Finanzarel', 'prueba');
                //echo 'Downloaded';
                
                
                echo 'URL: ' . $fileUrl;
                $cookiesFileOpened = fopen($this->cookiesDir . DS . $this->cookies_name, "r");
                $stringCookies = file_get_contents($this->cookiesDir . DS . $this->cookies_name);
                $stringCookieOne = "FNZRL_WORLD";
                $cookiePosFirst = stripos($stringCookies, $stringCookieOne);
                $cookieSubstr = substr($stringCookies, $cookiePosFirst + strlen($stringCookieOne) + 1);
                $separator = "#HttpOnly";
                $cookiePosSecond = stripos($cookieSubstr, $separator);
                $firstCookie = str_split($cookieSubstr, $cookiePosSecond);
                $stringCookieTwo = "LOGIN_USERNAME_COOKIE";
                $cookiePosThird = stripos($stringCookies, $stringCookieTwo);
                $secondCookie = substr($stringCookies, $cookiePosThird + strlen($stringCookieTwo) + 1);
                /*$header[] = 'Accept-language: es-ES,es;q=0.8';
                $header[] = 'Upgrade-insecure-requests: 1';
                $header[] = 'Host: ' . $this->baseUrl;
                //$header[] = 'Content-type: application/x-www-form-urlencoded';                                    
                $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*//*Delete a SLASH frombefore;q=0.8';
                $header[] = 'Cookie: LOGIN_USERNAME_COOKIE=' . trim($secondCookie) . '; FNZRL_WORLD=' . trim($firstCookie) . '; ' 
                        . '_ga=GA1.2.127419396.1505923123; _gid=GA1.2.984339975.1505923123; mp_5cc54fb25fbf8152c17f1bd71396f8fa_mixpanel=%7B%22distinct_id%22%3A%20%22kkukovetz%40mli-ltd.com%22%2C%22%24search_engine%22%3A%20%22duckduckgo%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fduckduckgo.com%2F%22%2C%22%24initial_referring_domain%22%3A%20%22duckduckgo.com%22%7D; mp_mixpanel__c=1';
                //$header[] = 'authority: ' . $pfpBaseUrl;
                //$header[] = 'cache-control: max-age=0';
                $header[] = 'Connection: keep-alive';
                $header[] = 'Upgrade-Insecure-Requests: 1';*/
                $header = array('Expect:');
                //How we get fix Finanzarel
                //https://chrismckee.co.uk/curl-http-417-expectation-failed/
                //https://stackoverflow.com/questions/3755786/php-curl-post-request-and-error-417
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($fileUrl,$referer, $credentialsFile, $header, $fileName);
                break; 
            case 4:
                return $tempArray["global"] = "waiting_for_global";
                /*
                echo "case 4!!!!!!!";
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //$this->print_r2($dom);

                //Get credentials to download the file
                $inputs = $dom->getElementsByTagName('input');
                foreach ($inputs as $input) {
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                //Get the request to download the file
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $key => $a) {
                    //echo $key . " => " . $a->getAttribute('href') . HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Descargar en csv') {
                        $request = explode("'", $a->getAttribute('href'))[1];
                        echo $request . HTML_ENDOFLINE;
                        break;
                    }
                }

                $url = array_shift($this->urlSequence);
                $fileUrl = $url . "p_flow_id=" . $credentials['p_flow_id'] . "&p_flow_step_id=" . $credentials['p_flow_step_id'] . "&p_instance=" . $credentials['p_instance'] . "&p_debug&p_request=" . $request;
                echo $fileUrl . HTML_ENDOFLINE;
                $fileName = 'Finanzarel';
                $fileType = 'csv';

                $pfpBaseUrl = 'http://www.finanzarel.com';
                $path = 'prueba';
                
                //$this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Finanzarel', 'prueba');
                echo 'Downloaded';
                break;*/
        }
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserInvestmentData($user, $password) {

        $resultLogin = $this->companyUserLogin($user, $password);

        if (!$resultLogin) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Finazarel login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo 'Login ok';

        //echo $this->pInstanceGlobal;

        $url = array_shift($this->urlSequence); //Load the page that contains the file url
        $dom = new DOMDocument;
        $str = $this->getCompanyWebpage($url . $this->pInstanceGlobal);
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //$this->print_r2($dom);

        //Get credentials to download the file
        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $input) {
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }


        //Get the request to download the file
        $as = $dom->getElementsByTagName('a');
        foreach ($as as $key => $a) {
            //echo $key . " => " . $a->getAttribute('href') . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Descargar en csv') {
                $request = explode("'", $a->getAttribute('href'))[1];
                echo $request . HTML_ENDOFLINE;
                break;
            }
        }

        $url = array_shift($this->urlSequence);
        $fileUrl = $url . "p_flow_id=" . $credentials['p_flow_id'] . "&p_flow_step_id=" . $credentials['p_flow_step_id'] . "&p_instance=" . $credentials['p_instance'] . "&p_debug&p_request=" . $request;
        echo $fileUrl . HTML_ENDOFLINE;
        $fileName = 'Finanzarel';
        $fileType = 'csv';

        $pfpBaseUrl = 'http://www.finanzarel.com';
        $path = 'prueba';

        $this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Finanzarel', 'prueba');
        echo 'Downloaded';
    }

}
