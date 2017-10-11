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
 */
class investly extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
    public function getParserConfigTransactionFile() {
        return $this->$valuesInvestlyTransaction;
    }
 
    public function getParserConfigInvestmentFile() {
        return $this->$valuesInvestlyInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesInvestlyAmortization;
    }     
    
    
    
    
    
    function companyUserLogin($user = "", $password = "", $options = array()) {
        //Need casper js
        
    }

}
