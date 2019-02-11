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

    var $uses = array('Dashboardoverviewdata', 'Tooltip', 'Globaldashboard');
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
            'foreignKey' =>  'investor_id'
        )
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
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readActiveInvestmentsGraphData($investorId, $period) {
        $field = 'globaldashboard_numberActiveInvestments';
        return $this->genericGraphSearch($investorId, $period, $field);
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_totalNetDeposits"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNetDepositsGraphData($investorId, $period) {
        $field = 'globaldashboard_totalNetDeposits';
        return $this->genericGraphSearch($investorId, $period, $field);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_cashDrag"      //This field is not implemented yet
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readCashDragGraphData($investorId, $period) {
        $field = 'globaldashboard_cashDrag';
        return $this->genericGraphSearch($investorId, $period, $field);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_outstandingPrincipal"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readInvestedAssetsGraphData($investorId, $period) {
        $field = 'globaldashboard_outstandingPrincipal';
        return $this->genericGraphSearch($investorId, $period, $field);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_reservedAssets"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readReservedFundsGraphData($investorId, $period) {
        $field = 'globaldashboard_reservedAssets';
        return $this->genericGraphSearch($investorId, $period, $field);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_cashInPlatform"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readCashGraphData($investorId, $period) {
        $field = 'globaldashboard_cashInPlatform';
        return $this->genericGraphSearch($investorId, $period, $field);   
    }

    /**
     * Read the historical data of the datum "globaldashboard_netAnnualReturnPastYear"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNarPastYearGraphData($investorId, $period) {
        $field = 'globaldashboard_netAnnualReturnPastYear';
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_netAnnualReturnPast12Months"
     * 
     * @param int  $investorId The object reference for the linked account             //Global historical not implemented
     * @param string $period  Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNarLast365daysMultiGraphData($investorId, $period) {
        $field = 'globaldashboard_netAnnualReturnPast12Months';
        $data['Dashboard'] = $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);
        return $data;
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_netAnnualTotalFundsReturn"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNarTotalFundsGraphData($investorId, $period) {
        $field = 'globaldashboard_netAnnualTotalFundsReturn';
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);   
    }
    
    /**
     * Read the historical data of the datum "globaldashboard_netReturnPast12Months"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNetEarningsLast365daysGraphData($investorId, $period) {
        $field = 'globaldashboard_netReturnPast12Months';
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);   
    }

    /**
     * Read the historical data of the datum "globaldashboard_netReturnPastYear"
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period  Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNetEarningsPastYearGraphData($investorId, $period) {
        $field = 'globaldashboard_netReturnPastYear';
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);   
    }

    /**
     * Read the historical data of the datum "globaldashboard_netTotal"      //This field is not implemented yet
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period  Time period. For now can be "year" or "all"
     * @return boolean
     */
    public function readNetEarningsTotalGraphData($investorId, $period) {
        $field = 'globaldashboard_netTotal';
        return $this->Dashboardoverviewdata->genericGraphSearch($investorId, $period, $field, true);   
    }    
    
    /**
     * Read the datum "globaldashboard_current"      //This field is not implemented yet
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period   //Not used
     * @return boolean
     */
    public function readCurrentGraphData($investorId, $period) {
        $data = $this->getData(['investor_id' => $investorId], ['globaldashboard_current'], 'Date DESC', null, 'first');
        return $data['Globaldashboard']['globaldashboard_current'];
    }   
    /**
     * Read the datum "globaldashboard_exposure"      //This field is not implemented yet
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period   //Not used
     * @return boolean
     */
    public function readExposureGraphData($investorId, $period) {
        $data = $this->getData(['investor_id' => $investorId], ['globaldashboard_exposure'], 'Date DESC', null, 'first');
        return $data['Globaldashboard']['globaldashboard_exposure'];
    }   
    /**
     * Read the datum "globaldashboard_exposure"      //This field is not implemented yet
     * 
     * @param int  $investorId The object reference for the linked account
     * @param string $period  //Not used
     * @return boolean
     */
    public function readPaymentDelayGraphData($investorId, $period) {
        

        $data['1-7'] = $this->getData(['investor_id' => $investorId], ['globaldashboard_delay_1-7'], 'Date DESC', null, 'first');
        $data['8-30'] = $this->getData(['investor_id' => $investorId], ['globaldashboard_delay_8-30'], 'Date DESC', null, 'first');
        $data['31-60'] = $this->getData(['investor_id' => $investorId], ['globaldashboard_delay_31-60'], 'Date DESC', null, 'first');
        $data['61-90'] = $this->getData(['investor_id' => $investorId], ['globaldashboard_delay_61-90'], 'Date DESC', null, 'first');
        $data['>90'] = $this->getData(['investor_id' => $investorId], ['globaldashboard_delay_>90'], 'Date DESC', null, 'first');

        return $data;
    }       
    
    
    
    
    
    /**
     * Generic search for a field to use in the graph of the api.
     * 
     * @param int $investorId
     * @param string $period  Time period. For now can be "year" or "all"
     * @param string $field
     * @return boolean
     */
    public function genericGraphSearch($investorId, $period, $field){
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
            'recursive' => -1,
        ]);
        return $result;
    }

}
