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
 * @version 0.5
 * @date 2017-08-23
 * @package
 *
 * 
 * 2017-08-23 version_0.1
 * Created
 * 
 * 2017-08-24 version_0.2
 * Added login
 * 
 * 2017-09-21 version_0.3
 * Added download file and integration with Gearman
 * 
 * 2017-09-26 version_0.4
 * Download all files correctly with Gearman
 * Added logout
 * 
 * 2017-09-28 version_0.5
 * Added new file to download
 * 
 */

/**
 * Contains the code required for accessing the website of "Finanzarel".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Not OK]
 * parallelization                                                      [OK, tested]
 */
class finanzarel extends p2pCompany {

    protected $pInstanceGlobal = '';
    protected $credentialsGlobal = array();
    protected $requestFiles = array();
    
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
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo 'Login ok';
                
                //Get the request to download the file
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $key => $a) {
                    //echo $key . " => " . $a->getAttribute('href') . "   " . $a->nodeValue .  HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Descargar en csv') {
                        $this->request[] = explode("'", $a->getAttribute('href'))[1];
                        
                    }
                }
                $url =  array_shift($this->urlSequence);
                //echo "The url is " . $url . "\n";
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                    '{$p_flow_step_id}' => 1,
                    '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[0]);
                print_r($credentialsFile);
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->numFileInvestment++;
                //$fileType = 'csv';
                //$referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:' . $this->credentialsGlobal['p_flow_step_id'] . ":" . $this->credentialsGlobal['p_instance'];
                //$referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:{$credential_p_flow_step_id}:{$credential_p_instance}';
                $this->baseUrl = 'marketplace.finanzarel.com';
                //How we get fix Finanzarel
                //https://chrismckee.co.uk/curl-http-417-expectation-failed/
                //https://stackoverflow.com/questions/3755786/php-curl-post-request-and-error-417
                $headers = array('Expect:');
                //array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url,$referer, $credentialsFile, $headers, $fileName);
                break; 
            case 4:
                $this->url =  array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $this->referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 1,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[1]);
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->numFileInvestment++;
                $headers = array('Expect:');
                if (count($this->request) > 2) {
                    $this->idForSwitch++;
                }
                else {
                    $this->idForSwitch = 6;
                }
                $this->getPFPFileMulticurl($this->url,$this->referer, $credentialsFile, $headers, $fileName);
                break;
            case 5:
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[2]);
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->numFileInvestment++;
                $headers = array('Expect:');
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->url,$this->referer, $credentialsFile, $headers, $fileName);
                break;
            case 6:
                $url = array_shift($this->urlSequence);
                //echo $url . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . $this->credentialsGlobal['p_instance']);
                break;
            case 7:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $buttons = $this->getElementsByClass($dom, "a-IRR-button");
                foreach ($buttons as $button) {
                    $id = $button->getAttributeNode("id")->nodeValue;
                    //echo "El id es $id \n";
                    $pos = stripos($id, "actions_button");
                    if ($pos !== false) {
                        echo "cashflow $id";
                        $credentialCashflows = explode("_", $id);
                        $this->credentialCashflow = $credentialCashflows[0];
                        echo "Found cashflow $this->credentialCashflow";
                        break;
                    }
                        
                }
                $url = array_shift($this->urlSequence);
                echo "The url of last is : ".$url;
                $url = strtr($url, array(
                            '{$p_instance}' => $this->credentialsGlobal['p_instance'],
                            '{$credentialCashflow}' => $this->credentialCashflow
                        ));
                echo "now the url is " . $url;
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 11,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $headers = array('Expect:');
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url,$referer, false, $headers, $fileName);
                break;
            case 7:
                return $tempArray["global"] = "waiting_for_global";
                
            /*case 6:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $buttons = $this->getElementsByClass($dom, "a-IRR-button");
                foreach ($buttons as $button) {
                    $id = $button->getAttributeNode("id")->nodeValue;
                    //echo "El id es $id \n";
                    $pos = stripos($id, "actions_button");
                    if ($pos !== false) {
                        echo "cashflow $id";
                        $credentialCashflows = explode("_", $id);
                        $this->credentialCashflow = $credentialCashflows[0];
                        echo "Found cashflow $this->credentialCashflow";
                        break;
                    }
                        
                }
                
                $inputs = $this->getElements($dom, "input");
                
                foreach ($inputs as $input) {
                    $id = $input->getAttributeNode("id")->nodeValue;
                    //echo "El id es $id \n";
                    $pos = stripos($id, "worksheet_id");
                    if ($pos !== false) {
                        echo "worksheet $id \n";
                        $x1 = $input->getAttributeNode("value")->nodeValue;
                        echo "Found x01 $x1";
                    }
                    $pos = stripos($id, "report_id");
                    if ($pos !== false) {
                        //GET THE NODE VALUE
                        echo "report $id \n";
                        $x2 = $input->getAttributeNode("value")->nodeValue;
                        echo "Found x02 $x2";
                        break;
                    }
                }
                
                echo "The worksheet id is $x1 and $x2 \n";
                // get ONLY the <script> nodes that dont have the src attribute
                $xpath = new DOMXPath($dom);
                $script_tags = $xpath->query('//body//script[not(@src)]');
                
                foreach ($script_tags as $script_tag) {
                    $value = $script_tag->nodeValue;
                    $pos = stripos($value, "LEGACY");
                    if ($pos !== false) {
                        echo "Found LEGACY";
                        $posInit = stripos($value, ":", $pos);
                        $posFinal = stripos($value, "}", $pos);
                        $request = substr($value, $posInit+2, $posFinal - $posInit -3);
                        echo "The request is : " . $request . "\n";
                    }
                }
                
                $credentials = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 11, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => 'PLUGIN=' . $request,
                        'p_widget_name' => 'worksheet',
                        'p_widget_mod' => 'ACTION',
                        'p_widget_action'=> 'QUICK_FILTER',
                        'p_widget_num_return' => 100,
                        'x01' => $x1,
                        'x02' => $x2,
                        'f01' => $this->credentialCashflow . '_column_search_current_column',
                        'f01' => $this->credentialCashflow . '_search_field',
                        'f01' => $this->credentialCashflow . '_row_select',
                        'f02' => 'FECHA',
                        'f02' => '21/09/17',
                        'f02' => 100,
                         'p_json' => '{"pageItems":null,"salt":"204941615398798274516216840009288834792"}'
                    );
                $url = array_shift($this->urlSequence);
                echo "The url of last is : ".$url;
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 11,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $this->headers = array(
                    "X-Requested-With: XMLHttpRequest", 
                    "Content-Type: application/x-www-form-urlencoded; charset=UTF-8", 
                    "Host: $this->baseUrl");
                //$fileName = "cashflow_1";
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url, $credentials, null, $referer);
                //$this->getPFPFileMulticurl($url,$referer, false, $headers, $fileName);
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
    
    public function companyUserLogout($url = null) {
        $this->doCompanyLogout(); //logout
        return true;
    }
    
    public function companyUserLogoutMultiCurl($str = null) {
        //Get logout url
        $this->doCompanyLogoutMultiCurl(); //Logout

    }

}
