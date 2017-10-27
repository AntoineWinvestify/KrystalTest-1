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
 * 2017-10-10 	  version 0.1
 * 
 * 2017-10-24      Version 0.2
 * getDashboard2SinglePfpData [Tested local, OK]
 * 
 * 2017-10-26
 * dashboardOverview moved from test [Tested local, OK]
 * 

  Pending:

 */


App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class Dashboard2sController extends AppController {

    var $name = 'Dashboard2s';
    var $helpers = array('Html', 'Js');
    var $uses = array("Userinvestmentdata", "Globalcashflowdata", "Linkedaccount");

    function beforeFilter() {

        parent::beforeFilter();
    }

    /**
     * [AJAX call]
     * 	Read the data of all active investments that belong to a linked account
     *
     */
    function getDashboard2SinglePfpData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        //Request data
        $linkedAccount = $this->request->data['id']; //Link account id
        $logo = $this->request->data['logo']; //Pfp Logo
        $name = $this->request->data['name']; //Pfp Name

        $this->layout = 'ajax';
        $this->disableCache();

        //Read investment info
        $investorReference = $this->Session->read('Auth.User.Investor.investor_identity');
        $filterConditions = array('userinvestmentdata_investorIdentity' => $investorReference, 'linkedaccount_id' => $linkedAccount);
        $dataResult = $this->Userinvestmentdata->getData($filterConditions);
        $dataResult['logo'] = $logo;
        $dataResult['name'] = $name;
        
       //Set result
        $result = array(true, $dataResult);
        $this->set('companyInvestmentDetails', $result);
    }

    /**
     *
     * Read all the data related to all the investments of an investor. 
     * $userReference is read from the session
     */
    function getDashboard2GlobalData() {
        
    }

    /**
     * Global dashboard view
     */
    function dashboardOverview() {

        $this->layout = 'azarus_private_layout';
        $this->Company = ClassRegistry::init('Company');
        $investorIdentity = $this->Session->read('Auth.User.Investor.investor_identity'); //Investor idnetity number
        
        //Get investment data from db
        $allInvestment = $this->Userinvestmentdata->getGlobalData($investorIdentity);

        //Get global data
        $global['totalVolume'] = 0; // totalVolume = investedAssets + reservedFunds + cash
        $global['investedAssets'] = 0;
        $global['reservedFunds'] = 0;
        $global['cash'] = 0;
        $global['activeInvestment'] = 0;
        $global['netDeposits'] = 0;
        foreach ($allInvestment as $globalKey => $individualPfpData) {
            foreach ($individualPfpData['Userinvestmentdata'] as $key => $individualData) {
                if ($key == "userinvestmentdata_activeInInvestments") { //Get global active in investment
                    $global['investedAssets'] = $global['investedAssets'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_myWallet") { //Get global wallet
                    $global['cash'] = $global['cash'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_reservedFunds") { //Get global reserved funds
                    $global['reservedFunds'] = $global['reservedFunds'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_investments") { //Get global active investmnent
                    $global['activeInvestment'] = $global['activeInvestment'] + $individualData;
                }
                if ($key == "id") {
                    $cashFlowData = $this->Globalcashflowdata->getData(array('userinvestmentdata_id' => $individualData), array('globalcashflowdata_platformDeposit'));
                    $global['netDeposits'] = $global['netDeposits'] + $cashFlowData[0]['Globalcashflowdata']['globalcashflowdata_platformDeposit'];
                }
                if ($key == "linkedaccount_id") {
                    //Get the pfp id of the linked acount
                    $companyIdLinkaccount = $this->Linkedaccount->getData(array('id' => $individualData), array('company_id'));
                    $pfpId = $companyIdLinkaccount[0]['Linkedaccount']['company_id'];
                    $allInvestment[$globalKey]['Userinvestmentdata']['pfpId'] = $pfpId;
                    //Get pfp logo and name
                    $pfpOtherData = $this->Company->getData(array('id' => $pfpId), array("company_logoGUID", "company_name"));
                    $allInvestment[$globalKey]['Userinvestmentdata']['pfpLogo'] = $pfpOtherData[0]['Company']['company_logoGUID'];
                    $allInvestment[$globalKey]['Userinvestmentdata']['pfpName'] = $pfpOtherData[0]['Company']['company_name'];
                }
            }
        }

        //Set global data
        $this->set('global', $global);
        //Set an array with individual info
        $this->set('individualInfoArray', $allInvestment);
    }

}
