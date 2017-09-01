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
 * link account
 */
class twino extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY twino DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        $credentials['name'] = $user;
        $credentials['password'] = $password;

        $payload =  json_encode('{"name":' . $credentials['name'] . ',"password"' . $credentials['password'] . ',"googleAnalyticClientId":"1778227581.1503479723"}');
        echo $payload;
        $this->doCompanyLoginRequestPayload($payload); //do login

        $str = $this->getCompanyWebpage();
        echo $str;
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;


        $confirm = false;

        $pres = $dom->getElementsByTagName('pre');
        foreach ($pres as $pre) {
            echo 'Entrando node value; ' . $pre->nodeValue . HTML_ENDOFLINE;
            if (trim($pre->nodeValue) == 'true') {
                $confirm = true;
            }
        }




        //$this->companyUserLogout($url);
        if ($confirm) {
            return true;
        }
        return false;
    }

}
