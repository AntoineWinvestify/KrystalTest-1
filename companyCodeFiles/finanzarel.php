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

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY finanzarel DURING LOGIN PROCESS
          $credentials['csrf'] = "XXXXX";
         */

        //Get credentials from form in pfp login page
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $input) {
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
        $credentials['p_page_submission_id'] = $pPageSubmissionId;
        $credentials['p_request'] ='Login';
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
        foreach($h2s as $h2){
            //echo $h2->nodeValue . HTML_ENDOFLINE;
            if(trim($h2->nodeValue) == 'Dashboard' ){
                return true;
            }
        }
        return false;
    }

}
