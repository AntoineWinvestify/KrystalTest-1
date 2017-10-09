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
 * 2017-08-25
 * Created
 */
class finbee extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
    public function getParserConfigTransactionFile() {
        return $this->$valuesFinbeeTransaction;
    }
 
     public function getParserConfigInvestmentFile() {
        return $this->$valuesFinbeeInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesFinbeeAmortization;
    }  
    
    
    
    function collectUserGlobalFilesParallel($str) {
        switch ($this->idForSwitch) {
            //LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($inputs as $input) {
                    //echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
                    $name = $input->getAttribute('name');
                    switch ($name) {
                        case 'option':
                            $option = $input->getAttribute('value');
                            break;
                        case 'view':
                            $view = $input->getAttribute('value');
                            break;
                        case 'op2':
                            $op2 = $input->getAttribute('value');
                            break;
                        case 'return':
                            $return = $input->getAttribute('value');
                            break;
                        case 'message':
                            $message = $input->getAttribute('value');
                            break;
                        case 'loginfrom':
                            $loginfrom = $input->getAttribute('value');
                            break;
                    }
                }


                $this->credentials['username'] = $this->user;
                $this->credentials['passwd'] = $this->password;
                $this->credentials['Submit'] = 'Log in';
                $this->credentials['option'] = $option;
                $this->credentials['view'] = $view;
                $this->credentials['op2'] = $op2;
                $this->credentials['return'] = $return;
                $this->credentials['message'] = $message;
                $this->credentials['loginfrom'] = $loginfrom;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                // echo $str;

                $confirm = false;
                $as = $dom->getElementsByTagName('a');

                foreach ($as as $key => $a) {
                    echo $key . " " . $a->nodeValue;
                    if (trim($a->nodeValue) == 'My Lending Account') {
                        $confirm = true;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    echo 'login fail';
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Finbee login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }
                echo 'login ok';
                //LOGIN END
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 4:
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 5:
                $fileName = "ControlVariables.xlsx"; //$this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 6:
                $this->numFileInvestment++;
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 7:
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 8:
                return $this->tempArray;
        }
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY finbee DURING LOGIN PROCESS
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
                case 'option':
                    $option = $input->getAttribute('value');
                    break;
                case 'view':
                    $view = $input->getAttribute('value');
                    break;
                case 'op2':
                    $op2 = $input->getAttribute('value');
                    break;
                case 'return':
                    $return = $input->getAttribute('value');
                    break;
                case 'message':
                    $message = $input->getAttribute('value');
                    break;
                case 'loginfrom':
                    $loginfrom = $input->getAttribute('value');
                    break;
            }
        }


        $credentials['username'] = $user;
        $credentials['passwd'] = $password;
        $credentials['Submit'] = 'Log in';
        $credentials['option'] = $option;
        $credentials['view'] = $view;
        $credentials['op2'] = $op2;
        $credentials['return'] = $return;
        $credentials['message'] = $message;
        $credentials['loginfrom'] = $loginfrom;

        $str = $this->doCompanyLogin($credentials); //do login


        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;
        $as = $dom->getElementsByTagName('a');

        $confirm = false;
        foreach ($as as $a) {

            if (trim($a->nodeValue) == 'My Lending Account') {
                $confirm = true;
            }
        }

        return $confirm;
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

}
