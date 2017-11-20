<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2017, https://www.winvestify.com                        |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-10-10
 * @package
 *
 * 2017-10-10       Version 0.1
 * 
 * 2017-10-24       Version 0.2
 * getDashboard2SinglePfpData [Tested local, OK]
 * 
 * 2017-10-26       Version 0.3
 * dashboardOverview moved from test [Tested local, OK]
 * 
 * 2017-11-09
 * calculateGlobalDefaulted [Tested local, OK]
 * 
 * 

  Pending:

 */


App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class Dashboard2sController extends AppController {

    var $name = 'Dashboard2s';
    var $helpers = array('Html', 'Js');
    var $uses = array("Userinvestmentdata", "Globalcashflowdata", "Linkedaccount", "Investment");

    function beforeFilter() {

        parent::beforeFilter();
    }

    /**
     * [AJAX call]
     * 	Read the data of all active investments that belong to a linked account
     * @throws FatalErrorException Error when you access winouth ajax
     */
    function getDashboard2SinglePfpData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        //echo 1;
        $executionStartTime = microtime(true);

        //Request data
        $idArray = explode(" ", $this->request->data['id']);
        $linkedAccount = $idArray[0]; //Link account id
        $userInvestmentData = $idArray[1];
        $logo = $this->request->data['logo']; //Pfp Logo
        $name = $this->request->data['name']; //Pfp Name

        $this->layout = 'ajax';
        $this->disableCache();

        //Read investment info
        $filterConditions = array('id' => $userInvestmentData, 'linkedaccount_id' => $linkedAccount);
        $dataResult = $this->Userinvestmentdata->getData($filterConditions, array('id', 'linkedaccount_id', '*'));
        $dataResult['logo'] = $logo;
        $dataResult['name'] = $name;

        // Get loans // 
        //$activeInvestments = $this->Investment->getData(array("linkedaccount_id" => $linkedAccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE), array("*"));
        $defaultedInvestments = $this->Investment->getData(array("linkedaccount_id" => $linkedAccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE, "investment_defaultedDays >" => 90), array("*"));
        //Set result
        $result = array(true, $dataResult);
        $this->set('companyInvestmentDetails', $result);
        //$this->set('activeInvestments', $activeInvestments);
        $this->set('defaultedInvestments', $defaultedInvestments);
        //Get and set range
        $this->set('defaultedRange', $this->Investment->getDefaultedByOutstanding($linkedAccount));
        
        $executionEndTime = microtime(true);
        //echo $executionEndTime - $executionStartTime;
    }

    /**
     * [AJAX call]
     * Get active loans of a linked account
     * @throws FatalErrorException Error when you access winouth ajax
     */
    function getActiveLoans(){
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        
        $linkedAccount = $this->request->data['id']; //Link account id
        $activeInvestments = $this->Investment->getData(array("linkedaccount_id" => $linkedAccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE), array("*"));
        
        if(!empty($activeInvestments)){
            $this->set('activeInvestments', [1,$activeInvestments]);
        } else {
            $this->set('activeInvestments', [1,"Not active loans found"]);
        }
    }
    
        /**
     * [AJAX call]
     * Get defaulted loans of a linked account
     * @throws FatalErrorException Error when you access winouth ajax
     */
    function getDefaultedLoans(){
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        
        $linkedAccount = $this->request->data['id']; //Link account id
        $defaultedInvestments = $this->Investment->getData(array("linkedaccount_id" => $linkedAccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE, "investment_defaultedDays >" => 90), array("*"));
       
        if(!empty($defaultedInvestments)){
            $this->set('defaultedInvestments', [1,$defaultedInvestments]);
        } else {
            $this->set('defaultedInvestments', [1,"Not defaulted loans found"]);
        }
    }
    
    
    /**
     * Global dashboard view
     */
    function dashboardOverview() {

        $executionStartTime = microtime(true);
        $this->layout = 'azarus_private_layout';
        $this->Company = ClassRegistry::init('Company');
        //$investorIdentity = $this->Session->read('Auth.User.Investor.investor_identity'); //Investor idnetity number
        $investorIdentityId = $this->Session->read('Auth.User.Investor.id');

        //Get investment data from db
        $allInvestment = $this->Userinvestmentdata->getLastInvestment($investorIdentityId);
        //print_r($allInvestment);
        //Get global data
        $global['totalVolume'] = 0;
        $global['investedAssets'] = 0;
        $global['reservedFunds'] = 0;
        $global['cash'] = 0;
        $global['activeInvestment'] = 0;

        //$global['netDeposits'] = 0; 
        foreach ($allInvestment as $globalKey => $individualPfpData) {
            foreach ($individualPfpData['Userinvestmentdata'] as $key => $individualData) {
                switch ($key) {
                    case "linkedaccount_id":
                        //Get the pfp id of the linked acount
                        $companyIdLinkaccount = $this->Linkedaccount->getData(array('id' => $individualData), array('company_id'));
                        $pfpId = $companyIdLinkaccount[0]['Linkedaccount']['company_id'];
                        $allInvestment[$globalKey]['Userinvestmentdata']['pfpId'] = $pfpId;
                        //Get pfp logo and name
                        $pfpOtherData = $this->Company->getData(array('id' => $pfpId), array("company_logoGUID", "company_name"));
                        $allInvestment[$globalKey]['Userinvestmentdata']['pfpLogo'] = $pfpOtherData[0]['Company']['company_logoGUID'];
                        $allInvestment[$globalKey]['Userinvestmentdata']['pfpName'] = $pfpOtherData[0]['Company']['company_name'];
                        break;
                    case "userinvestmentdata_totalVolume":
                        //Get global total volume
                        $global['totalVolume'] = bcadd($global['totalVolume'], $individualData, 16);
                        break;
                    case "userinvestmentdata_investedAssets":
                        //Get global  active in invesment
                        $global['investedAssets'] = bcadd($global['investedAssets'], $individualData, 16);
                        break;
                    case "userinvestmentdata_reservedAssets":
                        //Get global reserved funds
                        $global['reservedFunds'] = bcadd($global['reservedFunds'], $individualData, 16);
                        break;
                    case "userinvestmentdata_cashInPlatform":
                        //Get global wallet
                        $global['cash'] = bcadd($global['cash'], $individualData, 16);
                        break;
                    case "userinvestmentdata_numberActiveInvestments":
                        //get global active invesment:
                        $global['activeInvestment'] = $global['activeInvestment'] + $individualData;
                        break;                             
                    case "userinvestmentdata_totalNetDeposits": 
                        //get global net deposits:
                        $global['netDeposits'] = $global['netDeposits'] + $individualData;
                        break;
                }
            }
        }

        //Set global data
        $this->set('global', $global);
        //Set an array with individual info
        $this->set('individualInfoArray', $allInvestment);
        //Get and Set defaulted range
        $defaultedRange = $this->calculateGlobalDefaulted();
        $this->set('defaultedRange', $defaultedRange);
        
        $executionEndTime = microtime(true);
        //echo $executionEndTime - $executionStartTime;
    }

    /**
     * Calculate the global defaulted range of all linked accounts of a investor account
     * 
     * @return array Defaulted loans range
     */
    public function calculateGlobalDefaulted() {

        $investorId = $investorReference = $this->Session->read('Auth.User.Investor.id');
        $linkAccountList = $this->Linkedaccount->getData(array('investor_id' => $investorId), array('id'));

        //Get range of each pfp
        $defaultedRangeArray = array();
        foreach ($linkAccountList as $linkedAccount) {
            $defaultedRangeArray[] = $this->Investment->getDefaultedByOutstanding($linkedAccount['Linkedaccount']['id']);
        }

        //print_r($defaultedRangeArray);
        //Calculate global outstanding
        foreach ($defaultedRangeArray as $key => $defaultedRange) {
            $globalTotal = $globalTotal + $defaultedRange["total"];
        }

        $globalValue = array();
        $globalRange = array("1-7" => 0, "8-30" => 0, "31-60" => 0, "61-90" => 0, ">90" => 0);

        //Calculate global range
        foreach ($defaultedRangeArray as $defaultedRange) {
            foreach ($defaultedRange as $key => $range) {
                switch ($key) {
                    case "1-7":
                        $value = ($defaultedRange["1-7"] * $defaultedRange["total"]) / 100;
                        $globalValue["1-7"] = $globalValue["1-7"] + $value;
                        $globalRange["1-7"] = round(($globalValue["1-7"] / $globalTotal) * 100, 2);
                        break;
                    case "8-30":
                        $value = ($defaultedRange["8-30"] * $defaultedRange["total"]) / 100;
                        $globalValue["8-30"] = $globalValue["8-30"] + $value;
                        $globalRange["8-30"] = round(($globalValue["8-30"] / $globalTotal) * 100, 2);
                        break;
                    case "31-60":
                        $value = ($defaultedRange["31-60"] * $defaultedRange["total"]) / 100;
                        $globalValue["31-60"] = $globalValue["31-60"] + $value;
                        $globalRange["31-60"] = round(($globalValue["31-60"] / $globalTotal) * 100, 2);
                        break;
                    case "61-90":
                        $value = ($defaultedRange["61-90"] * $defaultedRange["total"]) / 100;
                        $globalValue["61-90"] = $globalValue["61-90"] + $value;
                        $globalRange["61-90"] = round(($globalValue["61-90"] / $globalTotal) * 100, 2);
                        break;
                    case ">90":
                        $value = ($defaultedRange[">90"] * $defaultedRange["total"]) / 100;
                        $globalValue[">90"] = $globalValue[">90"] + $value;
                        $globalRange[">90"] = round(($globalValue[">90"] / $globalTotal) * 100, 2);
                        break;
                }
            }
        }
             
        $globalRange["current"] = abs(round(100 - $globalRange["1-7"] - $globalRange["8-30"] -$globalRange["31-60"] - $globalRange["61-90"] - $globalRange[">90"], 2));
        return $globalRange;
    }

}
