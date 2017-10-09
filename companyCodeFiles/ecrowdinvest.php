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
 * @author 
 * @version 0.3
 * @date 2017-01-28
 * @package
 *
 *
 * Contains the code required for accessing the website of "Comunitae"
 *
 *
 *
 *
 * 2016-10-05	  version 2016_0.1
 * Basic version
 * function calculateLoanCost()											[OK not tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()												[OK not tested]
 * function collectUserInvestmentData()									[OK not tested]
 *
 * 2017-08-04
 *  collectCompanyMarketplaceData - Read completed investment
 *  collectHistorical - Added
 *
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 * PENDING:
 *
 *
 */
class ecrowdinvest extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how must it will cost in total to obtain a loan for a certain amount
     * 	from a company
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked:xx-xx-xxxx

        $minimumCommission = 12000;   // in  €cents

        $fixedCost = 3 * $amount / 100;
        if ($fixedCost < $minimumCommission) {
            $fixedCost = $minimumCommission;
        }

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }


    /**
     * Collects the marketplace data
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) { //ecrown doesnt have pagination
        
        
        $readController = 0;
        $investmentController = false;

        $totalArray = array();
        $str = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tag = 'div';
        $attribute = 'class';
        $value = 'panel panel-default';
        $projectwidgets = $this->getElements($dom, $tag, $attribute, $value);
        

          foreach ($projectwidgets as $key => $projectwidget) {
            
            if ($key == 0) { //Compare structures, only compare the first element
                
                $structureRevision = $this->htmlRevision($structure,'div',null,'class','col-xs-12',array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'filter-projects'));
                if($structureRevision[1]){
                    $totalArray = false; //Stop reading in error                         
                    break;
                }    
            }



            $tag2 = 'p';
            $ps = $this->getElements($projectwidget, $tag2);
            $purposeLocation = explode('- ', trim($ps[0]->nodeValue)); //gets purpose & location separated by "- "

            if (trim($ps[9]->nodeValue) == "-") {
                $value2 = 0;
            } else {
                $value2 = trim($ps[9]->nodeValue);
            }


            $tag3 = 'h2';
            $hs = $this->getElements($projectwidget, $tag3);

            $tag4 = 'div';
            $attribute4 = 'class';
            $value4 = 'progress-bar';
            $progress = $this->getElements($projectwidget, $tag4, $attribute4, $value4);

            $tag5 = 'a';
            $as = $this->getElements($projectwidget, $tag5);
            $timeLeft = explode(' ', trim($hs[0]->nodeValue))[1] . ' ' . explode(' ', trim($hs[0]->nodeValue))[2];

            $tempArray['marketplace_country'] = 'ES';
            $tempArray['marketplace_purpose'] = trim($purposeLocation[0]);
            $tempArray['marketplace_requestorLocation'] = trim($purposeLocation[1]);
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($ps[3]->nodeValue);
            $tempArray['marketplace_interestRate'] = $this->getPercentage($ps[5]->nodeValue);
            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($ps[7]->nodeValue);
            $tempArray['marketplace_numberOfInvestors'] = $value2;
            $timeLeft = trim($hs[0]->nodeValue);
            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(intval($progress[0]->getAttribute('aria-valuenow')));
            $tempArray['marketplace_loanReference'] = preg_replace('/\D/', '', $as[0]->getAttribute('id'));


            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {

                if ($tempArray['marketplace_status'] == '100% financiado') {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                    $tempArray['marketplace_status'] = CONFIRMED;
                } else {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con Tiempo';
                    $tempArray['marketplace_status'] = PERCENT;
                }
                foreach ($companyBackup as $inversionBackup) { //if completed and same status that in backup
                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                        echo HTML_ENDOFLINE . $tempArray['marketplace_loanReference'] . HTML_ENDOFLINE;
                        print_r($inversionBackup);
                        $readController++;
                        $investmentController = true;
                    }
                }
            } else if ($tempArray['marketplace_status'] == 'En estudio') {
                $tempArray['marketplace_statusLiteral'] = 'En estudio';
            } else {
                $tempArray['marketplace_statusLiteral'] = 'En proceso';
            }


            if ($investmentController) { //Don't save a already existing investment
                unset($tempArray);
                $investmentController = false;
            } else {
                $totalArray[] = $tempArray;
                $this->print_r2($totalArray);
                unset($tempArray);
            }
            echo $readController;
            if ($readController > 50) {  //If we find more than 25 completed investment existing in the backpup, stop reading
                echo 'Stop reading';
                echo $readController;
                break;
            }
    }
        $this->print_r2($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     *  Collect all investments
     * @param Array $structure
     * @return Array 
     */
    function collectHistorical($structure) { //ecrown doesnt have pagination
        $totalArray = array();
        $str = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tag = 'div';
        $attribute = 'class';
        $value = 'col-xs-12 col-md-4 col-sm-4 projectwidget';
        $projectwidgets = $this->getElements($dom, $tag, $attribute, $value);
        foreach ($projectwidgets as $key => $projectwidget) {
            
             if ($key == 0 && $structure) { //Compare structures, only compare the first element      
                $structureRevision = $this->htmlRevision($structure,'div',null,'class','col-xs-12',array('dom' => $dom, 'tag' => 'div', 'attribute' => 'id', 'attrValue' => 'filter-projects'));
                if($structureRevision[1]){
                    $totalArray = false; //Stop reading in error                         
                    break;
                }    
            }
            
            
            $tag2 = 'p';
            $ps = $this->getElements($projectwidget, $tag2);
            $purposeLocation = explode('- ', trim($ps[0]->nodeValue)); //gets purpose & location separated by "- "

            if (trim($ps[9]->nodeValue) == "-") {
                $value2 = 0;
            } else {
                $value2 = trim($ps[9]->nodeValue);
            }


            $tag3 = 'h2';
            $hs = $this->getElements($projectwidget, $tag3);

            $tag4 = 'div';
            $attribute4 = 'class';
            $value4 = 'progress-bar';
            $progress = $this->getElements($projectwidget, $tag4, $attribute4, $value4);

            $tag5 = 'a';
            $as = $this->getElements($projectwidget, $tag5);
            $timeLeft = explode(' ', trim($hs[0]->nodeValue))[1] . ' ' . explode(' ', trim($hs[0]->nodeValue))[2];

            $tempArray['marketplace_country'] = 'ES';
            $tempArray['marketplace_purpose'] = trim($purposeLocation[0]);
            $tempArray['marketplace_requestorLocation'] = trim($purposeLocation[1]);
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($ps[3]->nodeValue);
            $tempArray['marketplace_interestRate'] = $this->getPercentage($ps[5]->nodeValue);
            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($ps[7]->nodeValue);
            $tempArray['marketplace_numberOfInvestors'] = $value2;
            $tempArray['marketplace_status'] = trim($hs[0]->nodeValue);
            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($timeLeft);
            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(intval($progress[0]->getAttribute('aria-valuenow')));
            $tempArray['marketplace_loanReference'] = preg_replace('/\D/', '', $as[0]->getAttribute('id'));


            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {

                if ($tempArray['marketplace_status'] == '100% financiado') {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                    $tempArray['marketplace_status'] = CONFIRMED;
                } else {
                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con Tiempo';
                    $tempArray['marketplace_status'] = PERCENT;
                }
            } else if ($tempArray['marketplace_status'] == 'En estudio') {
                $tempArray['marketplace_statusLiteral'] = 'En estudio';
                $tempArray['marketplace_status'] = 3;
            } else {
                $tempArray['marketplace_statusLiteral'] = 'En proceso';
                $tempArray['marketplace_status'] = null;
            }


            $totalArray[] = $tempArray;
            unset($tempArray);
        }
        $this->print_r2($totalArray);
        return [$totalArray, false, null, $structureRevision[0], $structureRevision[2]]; //$totaArray -> Investments / false -> ecrown doesnt have pagination
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData() {
        
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
          FIELDS USED BY LOANBOOK DURING LOGIN PROCESS
          $credentials['signin']	 = 'Login';
          $credentials['csrf'] = "XXXXX";
         */
        $credentials['username'] = $user;
        $credentials['password'] = $password;

        if (!empty($options)) {
            foreach ($options as $key => $option) {
                $credentials[$key] = $option[$key];
            }
        }

        /*

          The csrf token is sent in the /https://www.loanbook.es/webuser/login message
          <div id="login-body" class="jumbotron" style="border: none !important;padding: 5px 5px 5px 5px;margin-bottom
          :5px;">
          <p style="font-size:13px;">Introduzca su login y password:</p>
          <div class="alert alert-error">
          <ul>
          </ul>
          </div>

          <form action="/webuser/login-ajax" method="post" name="LoginForm" class="form-horizontal"
          id="loginAjax">    <input type="hidden" name="csrf" value="55141da7b21d23187a5ba86f40766cc8" />
          <div class="control-group row">
          <div class="col-md-5 col-xs-12" style="padding-left:15px;padding-right
          : 5px">
          <input name="username" type="text" class="all-wide required active" id="username"
          placeholder="Usuario" value="">                </div>
          <div class="col-md-4 col-xs-12" style="padding-left:5px;padding-right: 5px;">
          <input name="password" type="password" class="all-wide required" id="password" placeholder
          ="Contraseña" onkeypress="capLock(event)" value="">                    <p class="size1" id="divMayus"
          style="display:none;text-align: left;">Bloq Mayús está activado</p>
          </div>
          <div class="col-md-2 col-xs-2" style="/*width:18%; padding-left:5px;padding-right:15px
          ;">
          <input name="signin" class="btn btn-warning" type="submit" id="formSubHeader" value
          ="Login">                </div>
          </div>
          </form>    <p class="size1"><a data-modal="modal" href="/user/forgotpassword">¿Olvid
          ó su contraseña? No hay problema. Por favor, haga clic aquí para recuperarla.</a></p>
          <div style="clear:both;"></div>
          <p class="size1"><a data-modal="modal" onclick="" data-containerwidth="800" data-backdrop="static"
          href="/contact/invest">
          ¿Aún no tiene un nombre de usuario y una contraseña?&nbsp;Regístrese ahora.
          </a></p>

          </div>
          </div>
          <div class="modal-footer">
          <div class="modal-footer-message">
          <label id="modal-error" class="error"></label>
          </div>


          </div>




         */


        $str = $this->doCompanyLogin($credentials);

// Check if user actually has entered the portal of the company.
// by means of checking of 2 unique identifiers of the portal
// This should be done by checking a field in the Webpage (button, link etc)
// and the email of the user (if aplicable)
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $confirm = 0;
        $uls = $dom->getElementsByTagName('ul');
        foreach ($uls as $ul) {
            $as = $ul->getElementsByTagName('a');
            foreach ($as as $a) {
                if (strcasecmp(trim($a->nodeValue), trim($user)) == 0) {
                    $confirm++;
                    break 2;
                }
            }
        }

        $as = $dom->getElementsByTagName('a');
        foreach ($as as $a) {
            if (strncasecmp(trim($a->getAttribute('href')), "/mi-posicion", 12) == 0) {
                $confirm++;
                break;
            }
        }

        if ($confirm == 2) {
            return 1;
        }
        return 0;
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
    
     /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {

                //We need remove this attribute directly from the div tag(the father)
        $node1->removeAttribute('class');
        $node1->removeAttribute('Style');
        $node2->removeAttribute('class');
        $node2->removeAttribute('Style');

        
        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
                ), array('a', 'href', 'id', 'alt', 'title', 'src', 'height', 'srcset', 'sizes', 'width'));

                
        $node1 = $this->cleanDom($node1, array( //Clear progress div
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class', 'style', 'data-toggle', 'data-placement', 'title', 'data-original-title', 'aria-valuenow'));

        $node1 = $this->cleanDomTag($node1, array(  
            array('typeSearch' => 'tagElement', 'tag' => 'strong'), //We dont have strong tag in completed investment
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'class', 'value' => 'blue'), //Span tag causes problems
        ));
        
        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'a'),
             array('typeSearch' => 'element', 'tag' => 'img'),
                ), array('a', 'href', 'id', 'alt', 'title', 'src', 'height', 'srcset', 'sizes', 'width'));
        
        $node2 = $this->cleanDom($node2, array( //Clear progress div
            array('typeSearch' => 'element', 'tag' => 'div'),
                ), array('class', 'style', 'data-toggle', 'data-placement', 'title', 'data-original-title', 'aria-valuenow'));
        
        $node2 = $this->cleanDomTag($node2, array(   
            array('typeSearch' => 'tagElement', 'tag' => 'strong'), //We dont have strong tag in completed investment
            array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'class', 'value' => 'blue'), //Span tag causes problems
        ));
        
        
        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }
    

}
