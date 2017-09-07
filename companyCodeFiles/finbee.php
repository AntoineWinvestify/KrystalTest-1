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
            echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
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
