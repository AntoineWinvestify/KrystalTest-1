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
class estateguru extends p2pCompany {

    protected $transactionConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
 
    protected $investmentConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );

/*    NOT YET READY
    protected $investmentConfigParms = array ('offsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );      
 
 */    
    
    
    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

 
    
    
    
    
    
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY estateguru DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        $credentials['j_username'] = $user;
        $credentials['j_password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login



        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            //echo $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Logout') {
                $confirm = true;
                break;
            }
        }


        return $confirm;
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserInvestmentDataParallel($str) {
        $this->tempArray['global']['activeInInvestments'] = 0;
        switch ($this->idForSwitch) {
            case 0:

                $credentials['j_username'] = $this->user;
                $credentials['j_password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 1:
                echo 'Doing loging' . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 2:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;

                $confirm = false;

                $as = $dom->getElementsByTagName('a');
                foreach ($as as $a) {
                    //echo $a->nodeValue . HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Logout') {
                        $confirm = true;
                        break;
                    }
                }

                if ($confirm) {
                    echo 'login ok';
                    $this->idForSwitch++;
                }
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;
                $inputs = $dom->getElementsByTagName('input');
                foreach($inputs as $input){
                    if($input->getAttribute('name') == 'filterProject'){
                        $id = $input->getAttribute('onclick');
                    }
                }
                $id = preg_replace("/[^0-9]/", "", $id);
                $id = trim(substr($id, 1));
                echo $id;
                $url = array_shift($this->urlSequence);
                $credentials = array(
                    "filterProjectValue" => 0,
                    "userId" => $id,
                );
                echo $url  . '?' . http_build_query($credentials);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url, $credentials);
                break;
            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                echo $str;
                $tbody = $dom->getElementsByTagName('tbody');
                $trs = $tbody[0]->getElementsByTagName('tr');
                
                //Individual invesmnet data
                foreach($trs as $tr){
                    $tds = $tr->getElementsByTagName('td');
                    foreach($tds as $key=>$td){
                        //Invesment data
                        echo $key . " " . $td->nodeValue . HTML_ENDOFLINE;
                        
                        
                    }
                    $as = $tr->getElementsByTagName('a');
                    if(!empty($as)){
                        //Invesmnet id
                        $loanId = explode(" ",$as[0]->getAttribute('href'))[0];
                        $loanId = explode("/",$as[0]->getAttribute('href'))[3];
                        echo print_r($loanId) . HTML_ENDOFLINE;
                        
                        
                    }
                    $this->tempArray['global']['activeInInvestments']++;
                }
                
                //Global data
                $tfoot = $dom->getElementsByTagName('tfoot');
                $tdsFoot = $tfoot[0]->getElementsByTagName('td');
                foreach($tdsFoot as $keyFoot=>$tdFoot){
                     echo $keyFoot . " " . $tdFoot->nodeValue;
                }
                break;
        }
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
