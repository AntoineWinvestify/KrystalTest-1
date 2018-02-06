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
    public $components = array('DataTable');
    var $uses = array("Userinvestmentdata", "Globalcashflowdata", "Linkedaccount", "Investment");

    function beforeFilter() {

        parent::beforeFilter();
    }

    /**
     * [AJAX call]
     * 	Read the data of all active investments that belong to a linked account
     * @throws FatalErrorException Error when you access without ajax
     */
    function getDashboard2SinglePfpData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        //echo 1;
        //$executionStartTime = microtime(true);
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
        $defaultedInvestments = $this->Investment->getData(array("linkedaccount_id" => $linkedAccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE, "investment_paymentStatus >" => 90), array("*"));
        //Set result
        $result = array(true, $dataResult);
        $this->set('companyInvestmentDetails', $result);
        //$this->set('activeInvestments', $activeInvestments);
        $this->set('defaultedInvestments', $defaultedInvestments);
        //Get and set range
        $this->set('defaultedRange', $this->Investment->getDefaultedByOutstanding($linkedAccount));
    }

    /**
     * [AJAX call]
     * Get defaulted loans for data table with server side pagination
     */
    function ajaxDataTableDefaultedInvestments($linkedAccount) {

        $this->autoRender = false;

        $this->Investment->virtualFields = array(
            'MyInvestmentFloat' => '(CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')))',
            'InterestFloat' => 'CAST(`Investment.investment_nominalInterestRate` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
            'OutstandingFloat' => 'CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
        );

        $this->paginate = array(
            'fields' => array('Investment.investment_loanId', 'Investment.investment_myInvestmentDate', 'MyInvestmentFloat', 'InterestFloat', 'Investment.investment_instalmentsProgress', 'OutstandingFloat', 'Investment.investment_nextPaymentDate', 'Investment.investment_statusOfLoan', 'Investment.investment_paymentStatus', "Investment.linkedaccount_id"),
            'conditions' => array("Investment.investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE, "Investment.investment_paymentStatus >" => 90, "Investment.linkedaccount_id" => $linkedAccount),
        );


        $this->DataTable->mDataProp = true;

        $investments = $this->DataTable->getResponse(null, 'Investment');
        $recordNumber = count($investments['aaData']);
        echo json_encode($investments);
    }

    /**
     * [AJAX call]
     * Get defaulted loans for data table with server side pagination
     */
    function ajaxDataTableActiveInvestments($linkedAccount) {

        $this->autoRender = false;

        $this->Investment->virtualFields = array(
            'MyInvestmentFloat' => '(CAST(`Investment.investment_myInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')))',
            'InterestFloat' => 'CAST(`Investment.investment_nominalInterestRate` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
            'OutstandingFloat' => 'CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
        );
        $this->paginate = array(
            'fields' => array('Investment.investment_loanId', 'Investment.investment_myInvestmentDate', 'MyInvestmentFloat', 'InterestFloat', 'Investment.investment_instalmentsProgress', 'OutstandingFloat', 'Investment.investment_nextPaymentDate', 'Investment.investment_statusOfLoan', 'Investment.investment_paymentStatus', "Investment.linkedaccount_id"),
            'conditions' => array("Investment.investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE, "Investment.linkedaccount_id" => $linkedAccount),
        );
        $this->DataTable->mDataProp = true;

        $investments = $this->DataTable->getResponse(null, 'Investment');
        echo json_encode($investments);
    }

    /**
     * [AJAX call]
     * Get active loans view for datatables.
     * @throws FatalErrorException Error when you access without ajax
     */
    function getActiveLoans() {
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
    }

    /**
     * [AJAX call]
     * Get active Defaulted view for datatables.
     * @throws FatalErrorException Error when you access winouth ajax
     */
    function getDefaultedLoans() {
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
    }

    /**
     * Global dashboard view
     */
    function dashboardOverview() {
        $this->layout = 'azarus_private_layout';
    }

    function dashboardOverviewData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        echo 1;
        $this->Company = ClassRegistry::init('Company');
        //$investorIdentity = $this->Session->read('Auth.User.Investor.investor_identity'); //Global Investor Identity number
        $investorId = $this->Session->read('Auth.User.Investor.id');

        //Get investment data from db
        $allInvestment = $this->Userinvestmentdata->getLastInvestment($investorId);

        print_r($allInvestment);
        //Get global data 
        $this->range = array();
        $global['totalVolume'] = 0;
        $global['investedAssets'] = 0;
        $global['reservedFunds'] = 0;
        $global['cash'] = 0;
        $global['activeInvestment'] = 0;

        /* $global['netAnnualTotal'] = 0;
          $global['netAnnual12Months'] = 0;
          $global['netAnnualReturnLastYear'] = 0; */

        $i = 0;
        //$global['netDeposits'] = 0; 
        foreach ($allInvestment as $globalKey => $individualPfpData) {        
            if (empty($individualPfpData)) {
                unset($allInvestment[$globalKey]);              
                continue;
            }
            foreach ($individualPfpData['Userinvestmentdata'] as $key => $individualData) {
                echo "key = $key \n";
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
                        $this->range[$i] = $this->Investment->getDefaultedByOutstanding($individualData);
                        $allInvestment[$globalKey]['Userinvestmentdata']['current'] = $this->range[$i]['current'];
                        $i++;
                        break;
                    case "userinvestmentdata_outstandingPrincipal":
                        //Get global  active in invesment
                        $global['investedAssets'] = bcadd($global['investedAssets'], $individualData, 16);
                        //Get global total volume
                        $global['totalVolume'] = bcadd($global['totalVolume'], $individualData, 16);
                        break;
                    case "userinvestmentdata_reservedAssets":
                        //Get global reserved funds
                        $global['reservedFunds'] = bcadd($global['reservedFunds'], $individualData, 16);
                        //Get global total volume
                        $global['totalVolume'] = bcadd($global['totalVolume'], $individualData, 16);
                        break;
                    case "userinvestmentdata_cashInPlatform":
                        //Get global wallet
                        $global['cash'] = bcadd($global['cash'], $individualData, 16);
                        //Get global total volume
                        $global['totalVolume'] = bcadd($global['totalVolume'], $individualData, 16);
                        break;
                    case "userinvestmentdata_numberActiveInvestments":
                        //get global active invesment:
                        $global['activeInvestment'] = $global['activeInvestment'] + $individualData;
                        break;
                    case "userinvestmentdata_totalNetDeposits":
                        //get global net deposits:
                        $global['netDeposits'] = bcadd($global['netDeposits'], $individualData, 16);
                        break;
                }
            }
        }

        $global['cashDrag'] = bcmul(bcdiv($global['cash'], $global['totalVolume'], 16), 100, 16);

        //Set global data
        $this->set('global', $global);

        //Get and Set defaulted range
        $defaultedRange = $this->calculateGlobalDefaulted();
        $this->set('defaultedRange', $defaultedRange);

        //Set an array with individual info
        $this->set('individualInfoArray', $allInvestment);

        $graphData = array(12, 24, 48, 24, 12, 6, 18, 36, 24, 48, 60); //Must be data from DB
        $graphLabel = array(__("Jan"), __("Feb"), __("Mar"), __("Apr"), __("May"), __("Jun"), __("Jul"), __("Aug"), __("Sep"), __("Oct"), __("Nov"), __("Dec"));
        $this->set('graphLabel', json_encode($graphLabel, true));
        $this->set('graph', json_encode($graphData));
    }

    /**
     * Calculate the global defaulted range of all linked accounts of a investor account
     * 
     * @return array Defaulted loans range
     */
    public function calculateGlobalDefaulted() {

        $investorId = $investorReference = $this->Session->read('Auth.User.Investor.id');
        // $linkAccountList = $this->Linkedaccount->getData(array('investor_id' => $investorId), array('id'));
        //Get range of each pfp
        //$defaultedRangeArray = array();
        /* foreach ($linkAccountList as $linkedAccount) {
          $defaultedRangeArray[] = $this->Investment->getDefaultedByOutstanding($linkedAccount['Linkedaccount']['id']);
          } */

        //print_r($defaultedRangeArray);
        //Calculate global outstanding
        foreach ($this->range as $key => $defaultedRange) {
            $globalTotal = $globalTotal + $defaultedRange["total"];
        }

        $globalValue = array();
        $globalRange = array("1-7" => 0, "8-30" => 0, "31-60" => 0, "61-90" => 0, ">90" => 0);

        //Calculate global range
        foreach ($this->range as $defaultedRange) {
            foreach ($defaultedRange as $key => $range) {
                switch ($key) {
                    case "1-7":
                        $value = ($defaultedRange["1-7"] * $defaultedRange["total"]) / 100;
                        $globalValue["1-7"] = $globalValue["1-7"] + $value;
                        $globalRange["1-7"] = round(($globalValue["1-7"] / $globalTotal) * 100, WIN_SHOW_DECIMAL);
                        break;
                    case "8-30":
                        $value = ($defaultedRange["8-30"] * $defaultedRange["total"]) / 100;
                        $globalValue["8-30"] = $globalValue["8-30"] + $value;
                        $globalRange["8-30"] = round(($globalValue["8-30"] / $globalTotal) * 100, WIN_SHOW_DECIMAL);
                        break;
                    case "31-60":
                        $value = ($defaultedRange["31-60"] * $defaultedRange["total"]) / 100;
                        $globalValue["31-60"] = $globalValue["31-60"] + $value;
                        $globalRange["31-60"] = round(($globalValue["31-60"] / $globalTotal) * 100, WIN_SHOW_DECIMAL);
                        break;
                    case "61-90":
                        $value = ($defaultedRange["61-90"] * $defaultedRange["total"]) / 100;
                        $globalValue["61-90"] = $globalValue["61-90"] + $value;
                        $globalRange["61-90"] = round(($globalValue["61-90"] / $globalTotal) * 100, WIN_SHOW_DECIMAL);
                        break;
                    case ">90":
                        $value = ($defaultedRange[">90"] * $defaultedRange["total"]) / 100;
                        $globalValue[">90"] = $globalValue[">90"] + $value;
                        $globalRange[">90"] = round(($globalValue[">90"] / $globalTotal) * 100, WIN_SHOW_DECIMAL);
                        break;
                }
            }
        }

        $globalRange["current"] = abs(round(100 - $globalRange["1-7"] - $globalRange["8-30"] - $globalRange["31-60"] - $globalRange["61-90"] - $globalRange[">90"], WIN_SHOW_DECIMAL));
        return $globalRange;
    }

    function showInitialPanel() {
        $this->layout = 'azarus_private_layout';
    }

}
