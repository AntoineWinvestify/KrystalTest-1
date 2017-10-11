<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version
 * @date
 * @package
 */

class fellowfinance extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
    public function getParserConfigTransactionFile() {
        return $this->$valuesFellowfinanceTransaction;
    }
 
    public function getParserConfigInvestmentFile() {
        return $this->$valuesFellowfinanceInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesFellowfinanceAmortization;
    }     
    
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY fellowfinance DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        $credentials['_username'] = $user;
        $credentials['_password'] = $password;

        //print_r($credentials);

       /* $str = $this->doCompanyLogin($credentials); //do login
        
        
        
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        /*$h2s = $dom->getElementsByTagName('h2');
        foreach ($h2s as $h2) {
            //echo $h2->nodeValue . HTML_ENDOFLINE;
            if (trim($h2->nodeValue) == 'Saldo en cuenta') {
                $confirm = true;
                break;
            }
        }*/

        if ($confirm) {
            return true;
        }
        return false;
    }

}

