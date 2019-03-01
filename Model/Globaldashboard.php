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
class Globaldashboard extends AppModel {

    var $name = 'Globaldashboard';
    var $useTable = "globaldashboards";
    public $hasMany = array(
        'Userinvestmentdata' => array(
            'className' => 'Userinvestmentdata',
            'foreignKey' => 'globaldashboard_id',
        ),
    );
    public $belongsTo = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' => 'investor_id'
        )
    );
    public $hasOne = array(
        'Globaldashboarddelay' => array(
            'className' => 'Globaldashboarddelay',
            'foreignKey' => 'globaldashboard_id',
        ),
    );

    /**
     * 
     * 
     * Read data for api.
     * 
     * 
     */

    /**
     * Read the historical data of the datum "globaldashboard_activeInvestments"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readActiveInvestmentsGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_numberActiveInvestments';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_totalNetDeposits"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetDepositsGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_totalNetDeposits';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_cashDrag"      //This field is not implemented yet
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readCashDragGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_cashDrag';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_outstandingPrincipal"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readInvestedAssetsGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_outstandingPrincipal';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_reservedAssets"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readReservedFundsGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_reservedAssets';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_cashInPlatform"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readCashGraphData($globalDashboardId, $period) {
        $field = 'globaldashboard_cashInPlatform';
        return $this->genericGraphSearch($globalDashboardId, $period, $field);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netAnnualReturnPastYear"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarPastYearGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netAnnualReturnPastYear';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netAnnualReturnPast12Months"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarLast365daysGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netAnnualReturnPast12Months';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netAnnualTotalFundsReturn"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNarTotalFundsGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netAnnualTotalFundsReturn';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netReturnPast12Months"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsLast365daysGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netReturnPast12Months';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netReturnPastYear"
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsPastYearGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netReturnPastYear';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the historical data of the datum "globaldashboard_netTotal"      //This field is not implemented yet
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @return array
     */
    public function readNetEarningsTotalGraphData($globalDashboardId, $period) {
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        $field = 'dashboardoverviewdata_netTotal';
        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
    }

    /**
     * Read the datum "globaldashboard_current"      //This field is not implemented yet
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readCurrentTotalGraphData($globalDashboardId, $period) {
        $this->Globaldashboarddelay = ClassRegistry::init('Globaldashboarddelay');
        $data['data'] = $this->Globaldashboarddelay->getData(['globaldashboard_id' => $globalDashboardId], ['globaldashboarddelay_currentOutstanding'], null, null, 'first');
        $data['tooltip'] = array(GLOBALDASHBOARD_CURRENT);
        return $data;
    }

    /**
     * Read the datum "globaldashboard_exposure"      //This field is not implemented yet
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readExposureGraphData($globalDashboardId, $period) {
        $data = $this->getData(['investor_id' => $globalDashboardId], ['globaldashboard_exposure'], 'Date DESC', null, 'first');
        return $data['Globaldashboard']['globaldashboard_exposure'];
    }

    /**
     * Read the data of the delay ranged based on outstanding
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period                                                   //Not used
     * @return array
     */
    public function readPaymentDelayGraphData($globalDashboardId, $period) {

        $delayRanges['data'] = $this->Globaldashboarddelay->getData(array('globaldashboard_id' => $globalDashboardId), array('Globaldashboarddelay.globaldashboarddelay_delay1-7Outstanding', 'Globaldashboarddelay.globaldashboarddelay_delay8-30Outstanding',
            'Globaldashboarddelay.globaldashboarddelay_delay31-60Outstanding', 'Globaldashboarddelay.globaldashboarddelay_delay61-90Outstanding',
            'Globaldashboarddelay.globaldashboarddelay_delay>90Outstanding', 'Globaldashboarddelay.globaldashboarddelay_currentOutstanding',
            'Globaldashboarddelay.globaldashboarddelay_outstandingDebts'), null, null, 'first');

        $delayRanges['tooltip'] = array(GLOBALDASHBOARD_PAYMENT_DELAY);
        return $delayRanges;
    }

    /**
     * Generic search for a field to use in the graph of the api.
     * 
     * @param int  $globalDashboardId The object reference for the globaldashboard
     * @param string $period  Time period. For now can be "year" or "all"
     * @param string $field
     * @return array
     */
    public function genericGraphSearch($globalDashboardId, $period, $field) {

        $investorId = $this->getData(array('id' => $globalDashboardId), 'investor_id', null, null, 'first')['Globaldashboard']['investor_id'];
        $conditions = ['investor_id' => $investorId];
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
