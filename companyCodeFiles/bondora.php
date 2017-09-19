<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by       |
 * | the Free Software Foundation; either version 2 of the License, or  	|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the           	|
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-08-25
 * @package
 *
 * 
 * 
 * 2017-08-24
 * Created
 * link account
 */
class bondora extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY Bondora DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */

        //First we need get te token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;


        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $key => $input) {
            //echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
            if ($key == 0) {
                continue;
            }
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $credentials['Email'] = $user;
        $credentials['Password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            //echo $span->nodeValue . HTML_ENDOFLINE;
            if (trim($span->nodeValue) == 'Account value') {
                $confirm = true;
                break;
            }
        }

        if ($confirm) {
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
    function companyUserLogout($url = null) {
        //$this->doCompanyLogout();
        $this->getCompanyWebpage();
        return true;
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserGlobalFilesParallel($str) {
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials['Email'] = $this->user;
                $credentials['Password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 2:
                echo 'Doing loging' . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                foreach ($spans as $span) {
                    echo $span->nodeValue . HTML_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        $confirm = true;
                        break;
                    }
                }


                if ($confirm) {
                    echo 'Login ok' . HTML_ENDOFLINE;
                    $this->idForSwitch++;
                    $this->getCompanyWebpageMultiCurl();
                }
                break;
            case 4:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $trs = $dom->getElementsByTagName('tr');
                foreach($trs as $tr){
                    echo $tr->nodeValue . HTML_ENDOFLINE;
                    if(strpos($tr->nodeValue,"Investments list")){
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadInvesment'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteInvesment'] = $urls[1]->getAttribute('href');
                    }
                    if(strpos($tr->nodeValue,"Account statement")){
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadCashFlow'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteCashFlow'] = $urls[1]->getAttribute('href');
                    }
                }

                if(empty($this->downloadDeleteUrl)){
                    $this->tempUrl['baseDownloadDelete'] = array_shift($this->urlSequence);
                }
                
                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadInvesment'];
                $referer = "https://www.bondora.com/en/reports/";
                //getPfpFileMulticurl($url = null, $fileName, $pfpName, $identity, $credentials, $referer)
                /*$this->headers = array(
                    
                );*/
                $this->getPfpFileMulticurl($url, 'Invesment', 'Bondora', 'TestUser', null, $referer); //'xlsx', 'https://www.bondora.com', 'bondora', 'testuser', null, 'https://www.bondora.com/en/reports/');
                break;
        }
    }

}
