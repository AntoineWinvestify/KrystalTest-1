<?php
/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2017, https://www.winvestify.com                        |
  // +-----------------------------------------------------------------------+
  // | This file is free software; you can redistribute it and/or modify     |
  // | it under the terms of the GNU General Public License as published by  |
  // | the Free Software Foundation; either version 2 of the License, or     |
  // | (at your option) any later version.                                   |
  // | This file is distributed in the hope that it will be useful           |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
  // | GNU General Public License for more details.                          |
  // +-----------------------------------------------------------------------+
  // | Author: Antoine de Poorter                                            |
  // +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-10-18
 * @package
 */
/*
  holds the logic of an individual investment

2017-10-18		version 0.1
initial version

  2017-11-06		version 0.2
    getDefaulted                [Tested local, OK]
    getDefaultedByOutstanding   [Tested local, OK]
    getDefaultedRange           [Tested local, OK]

Pending:


 */

class Investment extends AppModel {
    
    var $name = 'Investment';

    
    public $hasMany = array(
        'Payment' => array(
            'className' => 'Payment',
            'foreignKey' => 'investment_id',
            'fields' => '',
            'order' => '',
        ),
        'Paymenttotal' => array(
            'className' => 'Paymenttotal',
            'foreignKey' => 'investment_id',
            'fields' => '',
            'order' => '',
        ),
        'Investmentslice' => array(
            'className' => 'Investmentslice',
            'foreignKey' => 'investment_id',
            'fields' => '',
            'order' => '',
        ),       
        
        
    );

/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);

    /**
     *
     * Creates a new 'investment' table.
     * 	
     * 	@param 		array 	$investmentdata 	All the data to be saved
     * 	@return         id 
     * 			
     */
    public function createInvestment($investmentdata) {

        $this->create();

        if ($this->save($investmentdata, $validate = true)) {   
            $investmentId = $this->id;
        return $investmentId;
        }
    }
    
    public function getInvestmentIdByLoanId($loanIds) {
        $fields = array('Investment.investment_loanId', 'Investment.id');
        $conditions = array('investment_loanId ' => $loanIds);
        $investmentIds = $this->find('list', $params = array('recursive' => -1,
            'fields' => $fields,
            'conditions' => $conditions
        ));
        return $investmentIds;
    }

    /**
     * Get data of defaulted investment(s)
     * 
     * @param Int $linkedaccount Link account id 
     * @return array Defaulted inversions of a linked account with the defaulted days
     */
    public function getDefaulted($linkedaccount) {

        // Get defaulted investment and field paymentStatus 
        $defaultedInvestments = $this->find("all", array(
            "fields" => array("id", "investment_loanId", "investment_outstandingPrincipal", "investment_paymentStatus", "investment_statusOfLoan"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_paymentStatus > " => 0),
            "recursive" => -1,
        ));

        return $defaultedInvestments;
    }

    /**
     * Get defaulted percent with the Outstanding principal.
     * 
     * @param Int       $linkedaccount Link account id 
     * @return Array    Percentage of each defaulted range
     */
    public function getDefaultedByOutstanding($linkedaccount) {

        //Get total outstanding principal for a P2P
        $outstandings = $this->find("all", array(
            "fields" => array("investment_outstandingPrincipal"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE),
            "recursive" => -1,
        ));

        $totalOutstanding = 0;
        foreach ($outstandings as $outstanding) {
            $totalOutstanding = bcadd($totalOutstanding, $outstanding['Investment']['investment_outstandingPrincipal'], 16);
        }

        $defaultedInvestments = $this->getDefaulted($linkedaccount);
        $defaultedRange = $this->getDefaultedRange($defaultedInvestments, $totalOutstanding);
        return $defaultedRange;
    }

    
    /**
     * Get defaulted percent based on the number of investment.
     * 
     * @param Int       $linkedaccount Link account id 
     * @return Array    Percentage of each defaulted range
     */
    public function getDefaultedByInvestmentNumber($linkedaccount) {

        //Get total outstanding principal for a P2P
        $activeInvestment = $this->find("all", array(
            "fields" => array("investment_paymentStatus"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE),
            "recursive" => -1,
        ));

        
        $activeInvestmentRange = array(
            'currentNumber' => 0, '1-7Number' => 0, '8-30Number' => 0, '31-60Number' => 0, '61-90Number' => 0, '+90Number' => 0,
            'current' => 0, '1-7' => 0, '8-30' => 0, '31-60' => 0, '61-90' => 0, '+90' => 0,
        );
        $total = count($activeInvestment);
        
        foreach ($activeInvestment as $investment) {
            switch ($investment['Investment']['investment_paymentStatus']) {
                case 0:
                    $activeInvestmentRange['currentNumber']++;
                    $activeInvestmentRange['current'] = bcdiv($activeInvestmentRange['currentNumber'], $total ,16);
                    break;
                case ($investment['Investment']['investment_paymentStatus'] > 90):
                    $activeInvestmentRange['+90Number']++;
                    $activeInvestmentRange['+90'] = bcdiv($activeInvestmentRange['+90Number'], $total ,16);
                    break;
                case ($investment['Investment']['investment_paymentStatus'] > 60):
                    $activeInvestmentRange['61-90Number']++;
                    $activeInvestmentRange['61-90'] = bcdiv($activeInvestmentRange['61-90Number'], $total ,16);
                    break;
                case ($investment['Investment']['investment_paymentStatus'] > 30):
                    $activeInvestmentRange['31-60Number']++;
                    $activeInvestmentRange['31-60'] = bcdiv($activeInvestmentRange['31-60Number'], $total ,16);
                    break;
                case ($investment['Investment']['investment_paymentStatus'] > 7):
                    $activeInvestmentRange['8-30Number']++;
                    $activeInvestmentRange['8-30'] = bcdiv($activeInvestmentRange['8-30Number'], $total ,16);
                    break;
                case ($investment['Investment']['investment_paymentStatus'] > 0):
                    $activeInvestmentRange['1-7Number']++;
                    $activeInvestmentRange['1-7'] = bcdiv($activeInvestmentRange['1-7Number'], $total ,16);
                    break;
            }
        }
        
        return $activeInvestmentRange;
    }    
    
    
    
    
    
    
    /**
     * Get defaulted percent range.
     * 
     * @param array $defaultedInvestments Defaulted investment.
     * @param int $outstanding Outstanding principal
     */
    public function getDefaultedRange($defaultedInvestments, $outstanding) {

        $range = array("+90" => 0, "61-90" => 0, "31-60" => 0, "8-30" => 0, "1-7" => 0);
        $value = array();

        $range['total'] = $outstanding;

        foreach ($defaultedInvestments as $defaultedInvestment) {
            switch ($defaultedInvestment['Investment']['investment_paymentStatus']) {
                case 0:
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 90):
                    $value["+90"] = $value["+90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["outstandingDebt"] =  $value["+90"];
                    $range["+90"] = round(($value["+90"] / $outstanding), 4);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 60):
                    $value["61-90"] = $value["61-90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["61-90"] = round(($value["61-90"] / $outstanding), 4);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 30):
                    $value["31-60"] = $value["31-60"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["31-60"] = round(($value["31-60"] / $outstanding), 4);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 7):
                    $value["8-30"] = $value["8-30"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["8-30"] = round(($value["8-30"] / $outstanding), 4);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 0):
                    $value["1-7"] = $value["1-7"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["1-7"] = round(($value["1-7"] / $outstanding), 4);
                    break;
            }
        }
        //Calculate current
        $range["current"] = 1 - $range["1-7"] - $range["8-30"] - $range["31-60"] - $range["61-90"] - $range["+90"];
        return $range;
    }


    /** 
     *  Reads the investmentslices of an investment. Currently the system can only handle 1 slice per investment
     * 
     *  @param  bigint  database reference of Investment, i.e. investmentId
     *  @return array   slices (database references) and sliceIdentifier of each slice
     */
    public function getInvestmentSlices ($investmentId) {	
        
        $this->Behaviors->load('Containable');
	$this->contain('Investmentslice');  	

        $slices = $this->find("first", array("conditions" => array("id" => $investmentId),                                   
                                            "recursive" => 1,
                                          ));

        return $slices['Investmentslice']; 
    }  
    
    
    /**
     * 
     * 
     * 
     * READING FUNCTION FOR API
     * 
     * 
     * 
     */

     /** HAY QUE DEFINIR LOS TOOLTIPS 
     * Read the data of an investment list
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */
    public function readActiveinvestmentsList($linkedAccountId, $filter) {
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $linkedAccountResult = $this->Linkedaccount->find("first", $param = ['conditions' => ['Linkedaccount.id' => $linkedAccountId],
            'fields' => ['Linkedaccount.linkedaccount_currency', 'Accountowner.company_id'],
            'recursive' => 0]);
        $this->virtualFields = [
            'myInvestmentFloat' => '(CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')))',
            'interestFloat' => 'CAST(`Investment.investment_nominalInterestRate` as decimal(30, ' . WIN_SHOW_DECIMAL . '))/100',
            'outstandingFloat' => 'CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
            'progressFloat' => 'CAST((((CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) - CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) / (CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))))*100 as decimal(30, ' . WIN_SHOW_DECIMAL . '))'
        ];
        $conditions = ['investment_statusOfLoan' => 2,
            'linkedaccount_id' => $linkedAccountId];

        $investmentResults = $this->find('all', $params = [
            'conditions' => $conditions,
            //        'limit'  => 2,
            'fields' => ['investment_loanId', 'myInvestmentFloat', 'date', 'interestFloat', 'progressFloat', 'outstandingFloat', 'investment_nextPaymentDate', 'investment_paymentStatus'],
            'recursive' => -1
        ]);

        $investmentResults['company_id'] = $linkedAccountResult['Accountowner']['company_id'];
        return $investmentResults;
    }

    /** HAY QUE DEFINIR LOS TOOLTIPS 
     * Read the data of an idefaulted nvestment list
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */
    public function readDefaultedInvestmentsList($linkedAccountId, $filter = null) {
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $linkedAccountResult = $this->Linkedaccount->find("first", $param = ['conditions' => ['Linkedaccount.id' => $linkedAccountId],
            'fields' => ['Linkedaccount.linkedaccount_currency', 'Accountowner.company_id'],
            'recursive' => 0]);
        $this->virtualFields = [
            'myInvestmentFloat' => '(CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')))',
            'interestFloat' => 'CAST(`Investment.investment_nominalInterestRate` as decimal(30, ' . WIN_SHOW_DECIMAL . '))/100',
            'outstandingFloat' => 'CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
            'progressFloat' => 'CAST((((CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) - CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) / (CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))))*100 as decimal(30, ' . WIN_SHOW_DECIMAL . '))'
        ];
        $conditions = ['investment_statusOfLoan' => 2,
            'linkedaccount_id' => $linkedAccountId,
            'investment_paymentStatus >' => 90,
                ];

        $investmentResults = $this->find('all', $params = [
            'conditions' => $conditions,
            //        'limit'  => 2,
            'fields' => ['investment_loanId', 'myInvestmentFloat', 'date', 'interestFloat', 'progressFloat', 'outstandingFloat', 'investment_nextPaymentDate', 'investment_paymentStatus'],
            'recursive' => -1
        ]);

        $investmentResults['company_id'] = $linkedAccountResult['Accountowner']['company_id'];
        return $investmentResults;
    }

}
