<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                               |
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by        |
 * | the Free Software Foundation; either version 2 of the License, or           |
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-06-16
 * @package
 */
/*
 * 
 * 
 */
class Userinvestmentdata extends AppModel {

    var $name = 'Userinvestmentdata';
    var $useTable = "userinvestmentdatas";
    public $hasMany = array(
        'Globalcashflowdata' => array(
            'className' => 'Globalcashflowdata',
            'foreignKey' => 'userinvestmentdata_id',
            'fields' => '',
            'order' => '',
        ),
    );
    public $hasOne = array(
        'Dashboarddelay' => array(
            'className' => 'Dashboarddelay',
            'foreignKey' => 'userinvestmentdata_id',
        ),
    );
    public $belongsTo = array(
        'Globaldashboard' => array(
            'className' => 'Globaldashboard',
            'foreignKey' => 'globaldashboard_id'
        ),
        'Linkedaccount' => array(
            'className' => 'Linkedaccount',
            'foreignKey' => 'linkedaccount_id'
        ),
    );

    /**
     * Get data of the last linked accounts investments of an investor.
     * 
     * @param string $investorId             investor database id.
     * @return array Last Userinvestmentdata rows for the linked accounts
     */
    public function getLastInvestment($investorId) {

        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');

        //Get linked accounts id
        $linkedAccountsId = $this->Linkedaccount->find("all", array("recursive" => -1,
            "conditions" => array("investor_id" => $investorId,
                "linkedaccount_linkingProcess" => WIN_LINKING_NOTHING_IN_PROCESS,
                'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE),
            "fields" => array("id"),
        ));

        //Get last Userinvestmentdata table row for a linked account id
        $resultInvestorData = array();
        foreach ($linkedAccountsId as $linkedAccountId) {
            $resultInvestorData[] = $this->find("first", array("recursive" => -1,
                "conditions" => array("linkedaccount_id" => $linkedAccountId['Linkedaccount']['id']),
                "fields" => array("*"),
                "order" => "date DESC",
            ));
        }
        return $resultInvestorData;
    }

    /**
     * Get data of all the linked accounts of an investor.
     * 
     * @param string $investorIdentity investor identity number
     * @return array Global data
     */
    public function getGlobalData($investorIdentity) {

        $resultInvestorData = $this->find("all", array("recursive" => -1,
            "conditions" => array("userinvestmentdata_investorIdentity" => $investorIdentity),
        ));

        return $resultInvestorData;
    }

    /**
     * NOT FINISHED: does Globalcashflowdatatotal really need to exist?? or only Globalcashflowdata?
     * creates a new 'investment' table and also links the 'paymenttotal' database table
     * 	
     * 	@param 		array 	$investmentdata 	All the data to be saved
     * 	@return 	array[0]    => boolean
     *                  array[1]    => detailed error information if array[0] = false
     *                                 id if array[0] = true		
     */
    public function createUserInvestmentData($userInvestmentData) {
        $this->create();
        if ($this->save($userInvestmentData, $validation = true)) {   // OK
            $userInvestmentDataId = $this->id;
            $data = array('investment_id' => $userInvestmentDataId, 'status' => WIN_PAYMENTTOTALS_LAST);
            $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
            //       $this->Globalcashflowdata->create();
            //       if ($this->Globalcashflowdata->save($data, $validation = true)) { 
            $result[0] = true;
            $result[1] = $userInvestmentDataId;
            //     } 
            //   else {
            //      $result[0] = false;
            //     $result[1] = $this->Globalcashflowdata->validationErrors;
            //     $this->delete($userInvestmentDataId);
            //  }
        }
        else {                     // error occurred while trying to save the Investment data
            $result[0] = false;
            $result[1] = $this->validationErrors;
        }
        return $result;
    }

    public function getInvestmentIdByLoanId($loanIds) { // NOT NEEDED?? replace with getData
        $fields = array('Investment.investment_loanReference', 'Investment.id');
        $conditions = array('investment_loanReference' => $loanIds);
        $investmentIds = $this->find('list', $params = array('recursive' => -1,
            'fields' => $fields,
            'conditions' => $conditions
        ));
        return $investmentIds;
    }

    public function saveDataByType($linkedaccountId, $date, $data) {
        $conditions = array(
            'linkedaccount_id' => $linkedaccountId,
            'date'
        );
        $this->saveField($data['type'], $data['data']);
    }

    /**
     * 
     * 
     * Read data for api.
     * 
     * 
     */

    /**
     * Read the historical data of the datum "userinvestmentdata_activeInvestments"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readActiveInvestmentsGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_numberActiveInvestments';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_totalNetDeposits"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetDepositsGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_totalNetDeposits';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_cashDrag"      //This field is not implemented yet
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readCashDragGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_cashDrag';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_outstandingPrincipal"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readInvestedAssetsGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_outstandingPrincipal';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_reservedAssets"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readReservedFundsGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_reservedAssets';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_cashInPlatform"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readCashGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_cashInPlatform';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netAnnualReturnPastYear"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarPastYearGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_netAnnualReturnPastYear';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netAnnualReturnPast12Months"
     * 
     * @param int  $linkedAccountId The object reference for the linked account             //Global historical not implemented
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarLast365daysMultiGraphData($linkedAccountId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'userinvestmentdata_netAnnualReturnPast12Months';
        $data['Dashboard'] = $this->genericGraphSearch($linkedAccountId, $period, $field);
        $field = 'dashboardoverviewdata_netAnnualReturnPast12Months';
        $data['GlobalDashboard'] = $this->Dashboardoverviewdata->genericGraphSearch($linkedAccountId, $period, $field);
        return $data;
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netAnnualTotalFundsReturn"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarTotalFundsGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_netAnnualTotalFundsReturn';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netReturnPast12Months"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsLast365daysGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_netReturnPast12Months';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netReturnPastYear"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsPastYearGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_netReturnPastYear';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the historical data of the datum "userinvestmentdata_netTotal"      //This field is not implemented yet
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsTotalGraphData($linkedAccountId, $period) {
        $field = 'userinvestmentdata_netTotal';
        return $this->genericGraphSearch($linkedAccountId, $period, $field);
    }

    /**
     * Read the datum "userinvestmentdata_current"      //This field is not implemented yet
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readCurrentGraphData($linkedAccountId, $period) {
        $data = $this->getData(['linkedaccount_id' => $linkedAccountId], ['userinvestmentdata_current'], 'Date DESC', null, 'first');
        return $data['Userinvestmentdata']['userinvestmentdata_current'];
    }

    /**
     * Read the datum "userinvestmentdata_exposure"      //This field is not implemented yet
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readExposureGraphData($linkedAccountId, $period) {
        $data = $this->getData(['linkedaccount_id' => $linkedAccountId], ['userinvestmentdata_exposure'], 'Date DESC', null, 'first');
        return $data['Userinvestmentdata']['userinvestmentdata_exposure'];
    }

    /**
     * Read the data of the delay ranged based on outstanding
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readPaymentDelayGraphData($linkedAccountId, $period) {

        $userinvestmentdataId = $this->getData(array('linkedaccount_id' => $linkedAccountId), 'id', 'date DESC', null, 'first')['Userinvestmentdata']['id'];

        $delayRanges['data'] = $this->find('first', array(
            'conditions' => array('Dashboarddelay.userinvestmentdata_id' => $userinvestmentdataId),
            'fields' => array('Dashboarddelay.dashboarddelay_delay1-7Outstanding', 'Dashboarddelay.dashboarddelay_delay8-30Outstanding',
                'Dashboarddelay.dashboarddelay_delay31-60Outstanding', 'Dashboarddelay.dashboarddelay_delay61-90Outstanding',
                'Dashboarddelay.dashboarddelay_delay>90Outstanding', 'Dashboarddelay.dashboarddelay_currentOutstanding',
                'Dashboarddelay.dashboarddelay_outstandingDebts'),
            'recursive' => 0,
        ));

        $delayRanges['tooltip'] = array(DASHBOARD_PAYMENT_DELAY);
        return $delayRanges;
    }

    /**
     * Read the data of the current outstanding
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readCurrentTotalGraphData($linkedAccountId, $period) {

        $userinvestmentdataId = $this->getData(array('linkedaccount_id' => $linkedAccountId), 'id', 'date DESC', null, 'first')['Userinvestmentdata']['id'];

        $current['data'] = $this->find('first', array(
            'conditions' => array('Dashboarddelay.userinvestmentdata_id' => $userinvestmentdataId),
            'fields' => array('Dashboarddelay.dashboarddelay_currentOutstanding'),
//dashboarddelay_delay_1-7_active, dashboarddelay_delay_8-30_active, dashboarddelay_delay_31-60_active, dashboarddelay_delay_61-90_active, 
//dashboarddelay_delay_>91_active, dashboarddelay_activeDebts, dashboarddelay_current_active),
            'recursive' => 0,
        ));
        $current['tooltip'] = array(DASHBOARD_CURRENT);
        return $current;
    }

    /**
     * Read the list of each individual dashboard of a investor.
     * 
     * @param type $investorId
     * @param type $dummy                                                       //Not used
     * @return array                                                            //List of the individual dashboard for each linkedaccount
     */
    public function readDashboardList($globaldashboardId, $dummy) {

        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
        $this->Company = ClassRegistry::init('Company');
        $this->Globaldashboard = ClassRegistry::init('Globaldashboard');

        $investorId = $this->Globaldashboard->getData(array('Globaldashboard.id' => $globaldashboardId), 'Globaldashboard.investor_id', null, null, 'first', 1)['Globaldashboard']['investor_id'];

        $linkedAccountList = $this->Linkedaccount->find('all', array(
            'conditions' => array('Accountowner.investor_id' => $investorId, "linkedaccount_linkingProcess" => WIN_LINKING_NOTHING_IN_PROCESS, "linkedaccount_status" => ACTIVE),
            'fields' => array('Linkedaccount.id', 'Accountowner.company_id', 'Linkedaccount.linkedaccount_accountDisplayName', 'Linkedaccount.linkedaccount_currency'),
            'recursive' => 1,
        ));

        foreach ($linkedAccountList as $key => $linkedaccount) {

            $companyName = $this->Company->find('first', array(
                'conditions' => array('Company.id' => $linkedaccount['Accountowner']['company_id']),
                'fields' => 'Company.company_name',
                'recursive' => -1,
            ));

            $kpisDataList[$key] = $this->Userinvestmentdata->find('first', array(
                'conditions' => array('Userinvestmentdata.linkedaccount_id' => $linkedaccount['Linkedaccount']['id']),
                'fields' => array('Userinvestmentdata.userinvestmentdata_netAnnualReturnPast12Months', 'Userinvestmentdata.userinvestmentdata_outstandingPrincipal',
                    'Userinvestmentdata.userinvestmentdata_cashInPlatform', 'Userinvestmentdata.userinvestmentdata_reservedAssets', 'Dashboarddelay.dashboarddelay_currentOutstanding'),
                'recursive' => 1,
                'order' => 'Userinvestmentdata.Date DESC'
            ));

            $kpisDataList[$key]['Linkedaccount']['linkedaccount_id'] = $linkedaccount['Linkedaccount']['id'];
            $kpisDataList[$key]['Userinvestmentdata']['userinvestmentdata_netAnnualReturnPast12Months'] = round($kpisDataList[$key]['Userinvestmentdata']['userinvestmentdata_netAnnualReturnPast12Months'] * 100, WIN_SHOW_DECIMAL);
            $kpisDataList[$key]['Userinvestmentdata']['pfp'] = $companyName['Company']['company_name'];
            $kpisDataList[$key]['Userinvestmentdata']['linkedaccount_accountDisplayName'] = $linkedaccount['Linkedaccount']['linkedaccount_accountDisplayName'];
            $kpisDataList[$key]['Userinvestmentdata']['linkedaccount_currency'] = $linkedaccount['Linkedaccount']['linkedaccount_currency'];
            $kpisDataList[$key]['Dashboarddelay']['dashboarddelay_currentOutstanding'] = round($kpisDataList[$key]['Dashboarddelay']['dashboarddelay_currentOutstanding'] * 100, WIN_SHOW_DECIMAL);
        }

        return $kpisDataList;
    }

    /**
     * Generic search for a field to use in the graph of the api.
     * 
     * @param int $linkedAccountId
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @param string $field
     * @return array
     */
    public function genericGraphSearch($linkedAccountId, $period, $field) {
        $conditions = ['linkedaccount_id' => $linkedAccountId];

        switch ($period['period']) {
            case "all":
                break;
            case "year":
                App::uses('CakeTime', 'Utility');
                $conditions['date >='] = CakeTime::format('-1 year', '%Y-%m-%d');
                break;
            default:
                return false;
        }

        $result = $this->find('all', $param = [
            'conditions' => $conditions,
            'fields' => ['id', 'date',
                "$field as value"
            ],
            'order' => 'date ASC',
            'recursive' => -1,
        ]);
        return $result;
    }

}
