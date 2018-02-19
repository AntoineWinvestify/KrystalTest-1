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
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) { //ecrowd doesnt have pagination
        $readController = 0;
        $investmentController = false;
        $this->investmentDeletedList = $loanIdList;


        $totalArray = array();
        $str = $this->getCompanyWebpage();

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $tag = 'div';
        $attribute = 'class';
        $value = 'project-lists-home';
        $container = $this->getElements($dom, $tag, $attribute, $value);
        
        $projectwidgets = $container[0]->getElementsByTagName('div');

        foreach ($projectwidgets as $key => $projectwidget) {
            $value = "projectselect projectwidget";
            
            if(strpos($projectwidget->getAttribute('class'),$value)){
                if ($key == 0) { //Compare structures, only compare the first element
                        $structureRevision = $this->htmlRevision($structure, 'div', null, null, null, array('dom' => $dom, 'tag' => 'div', 'attribute' => 'class', 'attrValue' => 'project-lists-home'), 1, 1);
                        if ($structureRevision[1]) {
                            $totalArray = false; //Stop reading in error                         
                            break;
                        }
                    }

                    $ps = $projectwidget->getElementsByTagName('p');
                    
                    /*foreach($ps as $subkey=>$p){
                        echo $subkey . " is " . $p->nodeValue . "\n";
                    }*/
                    
                    echo  trim(explode(":", $ps[3]->nodeValue)[1]);
                    $tempArray['marketplace_country'] = 'ES';
                    $tempArray['marketplace_purpose'] = trim(explode("-", $ps[1]->nodeValue)[0]);
                    $tempArray['marketplace_sector'] = trim($ps[0]->nodeValue);
                    $tempArray['marketplace_requestorLocation'] = trim(explode("-", $ps[1]->nodeValue)[1]);
                    list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue(trim(explode(":", $ps[3]->nodeValue)[1]));
                    $tempArray['marketplace_amount'] = $this->getMonetaryValue(trim(explode(":", $ps[4]->nodeValue)[1]));
                    $tempArray['marketplace_interestRate'] = $this->getPercentage(trim(explode(":", $ps[5]->nodeValue)[1]));
                    list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue(trim(explode(":", $ps[6]->nodeValue)[1]));
                    $tempArray['marketplace_numberOfInvestors'] = trim(explode(":", $ps[7]->nodeValue)[1]);
                    
                    print_r($tempArray);
                    $progress = $this->getElements($projectwidget, 'div', 'role', 'progressbar');
                    $tempArray['marketplace_subscriptionProgress'] =  $this->getPercentage(explode("|", $progress[0]->getAttribute('title'))[0]);
                
                    $references = $projectwidget->getElementsByTagName('a');
                    $tempArray['marketplace_loanReference'] = $references[0]->getAttribute('id');
                    
                    
                    if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                        if (empty($tempArray['marketplace_timeLeft'])) {
                            $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                            $tempArray['marketplace_status'] = CONFIRMED;
                        } else {
                            $tempArray['marketplace_statusLiteral'] = 'Completado/Con Tiempo';
                            $tempArray['marketplace_status'] = PERCENT;
                        }
                        //print_r($tempArray);
                        foreach ($companyBackup as $inversionBackup) { //if completed and same status that is in backup 
                            // echo '//////////////////// comapare with backup ' . $inversionBackup['Marketplacebackup']['marketplace_loanReference'] . ' and ' . $tempArray['marketplace_loanReference'];
                            if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] === $tempArray['marketplace_status']) {
                                echo HTML_ENDOFLINE . $tempArray['marketplace_loanReference'] . HTML_ENDOFLINE;
                                print_r($inversionBackup);
                                $readController++;
                                $investmentController = true;
                            }
                        }
                    } else {
                        $tempArray['marketplace_statusLiteral'] = 'En proceso';
                        $tempArray['marketplace_status'] = null;
                    }
                                   
                    if ($investmentController) { //Don't save a already existing investment
                        echo "unset, don't save";
                        unset($tempArray);
                        $investmentController = false;
                    } else {
                        /*echo "save:";
                        print_r($tempArray);*/
                        $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList, $tempArray);
                        $totalArray[] = $tempArray;
                        unset($tempArray);
                    }
                    echo $readController;
                    if ($readController > 15) {  //If we find more than 25 completed investment existing in the backpup, stop reading
                        echo 'Stop reading';
                        echo $readController;
                        break;
                    }
                }
        }
        
        if ($totalArray != false) {
            echo 'To delete';
            print_r($this->investmentDeletedList);
            $deletedInvestment = $this->deleteInvestment($this->investmentDeletedList);
            echo 'totalpremerge';
            print_r($totalArray);
            if (!empty($deletedInvestment)) {
                $totalArray = array_merge($totalArray, $deletedInvestment);
                echo 'total:';
                $this->print_r2($totalArray);
            }
        }
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    function deleteInvestment($referenceArray){
        foreach($referenceArray as $id){
            $tempArray["marketplace_loanReference"] = $id;
            $tempArray['marketplace_statusLiteral'] = 'Eliminada';
            $tempArray['marketplace_status'] = REJECTED;
            $totalArray[] = $tempArray;
        }
        return $totalArray;
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
        //$this->print_r2($totalArray);
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
          FIELDS USED BY Ecrowd DURING LOGIN PROCESS
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
        $node1->removeAttribute('style');
        $node2->removeAttribute('class');
        $node2->removeAttribute('style');

        /* $node1 = $this->cleanDomTag($node1, array(  
          array('typeSearch' => 'tagElement', 'tag' => 'strong'), //We dont have strong tag in completed investment
          array('typeSearch' => 'tagElement', 'tag' => 'span', 'attr' => 'class', 'value' => 'blue'), //Span tag causes problems
          array('typeSearch' => 'tagElement', 'tag' => 'img', 'attr' => 'rel', 'value' => 'popover2'),
          )); */


        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'span'),
            array('typeSearch' => 'element', 'tag' => 'div')
                ), array('class'));


        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'p'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('style', 'href', 'id', 'src', 'alt', 'title', 'srcset', 'height', 'width','data-placement', 'aria-valuenow'));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'span'),
            array('typeSearch' => 'element', 'tag' => 'div')
                ), array('class'));

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'p'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('style', 'href', 'id', 'src', 'alt', 'title', 'srcset', 'height', 'width','data-placement', 'aria-valuenow'));


        // print_r($node1->getAttribute('class'));
        //print_r($node2->attributes);


        $structureRevision = $this->verifyDomStructure($node1, $node2);

        return $structureRevision;
    }
    

}
