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
 * 2017-08-28
 * Created
 * link account
 */
class viainvest extends p2pCompany {

    
    protected $transactionConfigParms =  ['offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 ];
 
    protected $investmentConfigParms =  ['OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 ];

/*    NOT YET READY
    protected $investmentConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );      
 
 */   
    
    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

  
    
    
    function companyUserLogin($user = "", $password = "", $options = []) {
        /*
          FIELDS USED BY viainvest DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        //Get credentials
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $this->getElements($dom, 'form', 'action', '/users/login');
        $inputs = $forms[0]->getElementsByTagName('input');

        foreach ($inputs as $key => $input) {
            //echo $key . ' => ' . $input->getAttribute('name') . " => " . $input->getAttribute('value') . HTML_ENDOFLINE;
            //$credentials[$input->getAttribute('name')] = $input->getAttribute('value');
            switch ($key) {
                case 1:
                    $credentials[urlencode($input->getAttribute('name'))] = $input->getAttribute('value');
                    break;
                case 7:
                    $credentials[urlencode($input->getAttribute('name'))] = $input->getAttribute('value');
                    break;
                case 8:
                    $credentials[urlencode($input->getAttribute('name'))] = $input->getAttribute('value');
                    break;
            }
        }

        $credentials['_method'] = 'POST';
        $credentials[urlencode('data[User][email]')] = $user;
        $credentials[urlencode('data[User][passwd]')] = $password;
        $credentials[urlencode('data[User][is_remember]')] = 0;
        //print_r($credentials);


        $str = $this->doCompanyLogin($credentials); //do login

        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            //echo $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->getAttribute('title')) == 'Logout') {
                $confirm = true;
                break;
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
