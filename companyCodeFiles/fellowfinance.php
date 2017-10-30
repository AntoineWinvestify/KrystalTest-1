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

    protected $transactionConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
 
    protected $investmentConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );

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

    /**
     * 
     * @param type $user
     * @param type $password
     * @param type $options
     * @return boolean
     */
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY fellowfinance DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */
        $url = $this->urlSequence;
        $totalArray = array();
        $this->casperInit("https://secure.fellowfinance.fi/Login/Index?LanguageCode=en");
        $this->casperWaitSelector('#Login_UserName', 3000);
        $this->casperSendKey("input#Login_UserName", $user);
        $this->casperSendKey("input#Login_Password", $password);
        $this->casperClick("button#Login_LoginWithUserId");
        
        $this->casperWaitSelector('#WithdrawMoneyDialog', 5000);
        $this->casperRun();
        $str = $this->casperGetContent();
        //echo $str;
        
        $confirmLogin = false;
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $as = $this->getElements($dom, "a", "class", "action-button-header");
        foreach ($as as $a) {
            $href = $a->getAttribute('href');
            if ($href == "/DepositMoneyProcess") {
                $confirmLogin = true;
                break;
            }
        }
        return $confirmLogin;
    }
    
    /**
     * 	Collects the marketplace data. We must login first in order to obtain the marketplace data
     * 	@return array	Each investment option as an element of an array
     * 	
     */
    function collectCompanyMarketplaceData() {
        
    }

}

