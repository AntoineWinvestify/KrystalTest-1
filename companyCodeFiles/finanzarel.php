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

    protected $InstanceGlobal = '';
    
    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
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
            echo $h2->nodeValue . HTML_ENDOFLINE;
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