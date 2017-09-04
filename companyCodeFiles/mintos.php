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
 * Contains the code required for accessing the website of "Mintos"
 *
 * 
 * @author 
 * @version 0.1
 * @date 2017-08-16
 * @package  
 * 
 * 2017-08-23
 * link account
 * 
 */

/**
 * Description of mintos
 *
 */
class mintos extends p2pCompany {

    function __construct() {
        parent::__construct();
        // Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Collects the marketplace data. We must login first in order to obtain the marketplace data
     * 	@return array	Each investment option as an element of an array
     * 	
     */
    function collectCompanyMarketplaceData() {
        
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
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY ECROWDINVEST DURING LOGIN PROCESS
          $credentials['_csrf_token'] = "XXXXX";
         */

        //First we need get the $csrf token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
        $csrf = $input[0]->getAttribute('value'); //this is the csrf token

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;
        $credentials['_csrf_token'] = $csrf;
        $credentials['_submit'] = '';


        if (!empty($options)) {
            foreach ($options as $key => $option) {
                $credentials[$key] = $option[$key];
            }
        }

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login


        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        // echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            // echo 'Entrando ' . 'href value; ' . $a->getAttribute('herf') . ' node value' . $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Overview') {
                //echo 'a encontrado' . HTML_ENDOFLINE;
                $confirm = true;
            }

            //Get logout url
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $url = $a->getAttribute('href');
            }
        }


        if ($confirm) {
            return true;
        }
        return false;
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserInvestmentDataParallel($str) {


        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                echo 'Next: ' . $next . HTML_ENDOFLINE;
                break;
            case 1:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                //Login fixed
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
                $csrf = $input[0]->getAttribute('value'); //this is the csrf token

                $this->credentials['username'] = $this->user;
                $this->credentials['password'] = $this->password;
                $this->credentials['_csrf_token'] = $csrf;
                $this->credentials['_submit'] = '';

                echo 'Credentials: ' .HTML_ENDOFLINE;
                $this->print_r2($this->credentials);
                
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                unset($this->credentials);
                break;
            case 2:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                echo 'Next: ' . $next . HTML_ENDOFLINE;
                break;
            case 3:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                $dom = new DOMDocument;  //Check if works
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $resultLogin = false;
                echo 'CHeck login' . HTML_ENDOFLINE;
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $a) {
                    echo $a->nodeValue . HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Overview') {
                        echo 'Find' . HTML_ENDOFLINE;
                        $resultLogin = true;
                        break;
                    }
                }

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
                
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                echo 'Next: ' . $next . HTML_ENDOFLINE;
                break;
            ////////DOWNLOAD FILE
            case 4:
                echo $this->idForSwitch . HTML_ENDOFLINE;         
                echo 'Login ok';
                $fileUrl = $str;
                echo $fileUrl . HTML_ENDOFLINE;
                $credentialsFile = 'purchased_from=&purchased_till=&statuses%5B%5D=256&statuses%5B%5D=512&statuses%5B%5D=1024&statuses%5B%5D=2048&statuses%5B%5D=8192&statuses%5B%5D=16384&+=256&+=512&+=1024&+=2048&+=8192&+=16384&listed_for_sale_status=&min_interest=&max_interest=&min_term=&max_term=&with_buyback=&min_ltv=&max_ltv=&loan_id=&sort_field=&sort_order=DESC&max_results=20&page=1&include_manual_investments=';
                $fileName = 'Investment';
                $fileType = 'xlsx';
                $pfpBaseUrl = 'https://www.mintos.com/en/my-investments/?statuses[]=256&statuses[]=512&statuses[]=1024&statuses[]=2048&statuses[]=8192&statuses[]=16384&sort_order=DESC&max_results=20&page=1';
                $this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Mintos', 'prueba', $credentialsFile);
                echo 'Downloaded';
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            //////LOGOUT
            case 5:
                echo $this->idForSwitch . HTML_ENDOFLINE;
                //Get logout url
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $a) {
                    echo $a->getAttribute('class') . HTML_ENDOFLINE;
                    if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                        $logoutUrl = $a->getAttribute('href');
                        break;
                    }
                }
                echo 'Logout:' . $logoutUrl . HTML_ENDOFLINE;
                $this->getCompanyWebpageMultiCurl($logoutUrl); //Logout
                break;
        }
    }

    /**
     *
     * 	Logout of user from the company portal.
     * 	 @param type $url
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout($url) {

        $str =  $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) { //get logout url
            //echo $a->getAttribute('class') . HTML_ENDOFLINE;
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $logoutUrl = $a->getAttribute('href');
                break;
            }
        }

        $this->getCompanyWebpage($logoutUrl); //logout
        return true;
    }

}
