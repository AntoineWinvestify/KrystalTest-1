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
 *


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
     * Get defaulted percent range.
     * 
     * @param array $defaultedInvestments Defaulted investment.
     * @param int $outstanding Outstanding principal
     */
    public function getDefaultedRange($defaultedInvestments, $outstanding) {

        $range = array(">90" => 0, "61-90" => 0, "31-60" => 0, "8-30" => 0, "1-7" => 0);
        $value = array();

        $range['total'] = $outstanding;

        foreach ($defaultedInvestments as $defaultedInvestment) {
            switch ($defaultedInvestment['Investment']['investment_paymentStatus']) {
                case 0:
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 90):
                    $value[">90"] = $value[">90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range[">90"] = round(($value[">90"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 60):
                    $value["61-90"] = $value["61-90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["61-90"] = round(($value["61-90"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 30):
                    $value["31-60"] = $value["31-60"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["31-60"] = round(($value["31-60"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 7):
                    $value["8-30"] = $value["8-30"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["8-30"] = round(($value["8-30"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['investment_paymentStatus'] > 0):
                    $value["1-7"] = $value["1-7"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["1-7"] = round(($value["1-7"] / $outstanding) * 100, 2);
                    break;
            }
        }
        //Calculate current
        $range["current"] = 100 - $range["1-7"] - $range["8-30"] - $range["31-60"] - $range["61-90"] - $range[">90"];
        return $range;
    }


}
