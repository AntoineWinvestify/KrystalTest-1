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
        'Amortizationtable' => array(
            'className' => 'Amortizationtable',
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
     * creates a new 'investment' table and also links the 'paymenttotal' database table
     * 	
     * 	@param 		array 	$investmentdata 	All the data to be saved
     * 	@return 	array[0]    => boolean
     *                  array[1]    => detailed error information if array[0] = false
     *                                 id if array[0] = true
     * 			
     */
    public function createInvestment($investmentdata) {
        $result = array();
        $this->create();
        if ($this->save($investmentdata, $validation = true)) {   // OK
            $investmentId = $this->id;
            $result[0] = true;
            $result[1] = $investmentId;
        } 
 
        else {                     // error occurred while trying to save the Investment data
            $result[0] = false;
            $result[1] = $this->validationErrors;
        }
        return $result;
    }

    public function getInvestmentIdByLoanId($loanIds) {
        $fields = array('Investment.investment_loanReference', 'Investment.id');
        $conditions = array('investment_loanReference' => $loanIds);
        $investmentIds = $this->find('list', $params = array('recursive' => -1,
            'fields' => $fields,
            'conditions' => $conditions
        ));
        return $investmentIds;
    }

    /**
     * Get defaulted investment of a linked account
     * 
     * @param Int $linkedaaccount Link account id 
     * @return array Defaulted inversions of a linked account with the defaulted days
     */
    public function getDefaulted($linkedaccount) {

        $today = date("Y-m-d");

        //Get defaulted investment and days
        $defaultedInvestments = $this->find("all", array(
            "fields" => array("investment_outstandingPrincipal", "investment_nextPaymentDate", "investment_statusOfLoan"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_nextPaymentDate < " => $today, "investment_statusOfLoan" => 2),
            "recursive" => -1,
        ));

        foreach ($defaultedInvestments as $key => $defaultedInvestment) {
            //echo strtotime($today) . HTML_ENDOFLINE;
            //echo strtotime($defaultedInvestment['Investment']['investment_nextPaymentDate']) . HTML_ENDOFLINE;
            $defaultedInvestments[$key]['Investment']['defaultedTime'] = -(strtotime($defaultedInvestment['Investment']['investment_nextPaymentDate']) - strtotime($today)) / (60 * 60 * 24);
        }


        return $defaultedInvestments;
    }

    /**
     * Get defaulted percent with the Outstanding principal.
     * 
     * @param Int $linkedaaccount Link account id 
     */
    public function getDefaultedByOutstanding($linkedaccount) {

        //Get total outstanding principal
        $outstandings = $this->find("all", array(
            "fields" => array("investment_outstandingPrincipal"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_statusOfLoan" => 2),
            "recursive" => -1,
        ));

        $totalOutstanding = 0;
        foreach ($outstandings AS $outstanding) {
            $totalOutstanding = $totalOutstanding + $outstanding['Investment']['investment_outstandingPrincipal'];
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
            switch ($defaultedInvestment['Investment']['defaultedTime']) {
                case ($defaultedInvestment['Investment']['defaultedTime'] > 90):
                    $value[">90"] = $value[">90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range[">90"] = round(($value[">90"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['defaultedTime'] > 60):
                    $value["61-90"] = $value["61-90"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["61-90"] = round(($value["61-90"] / $outstanding) * 100, 2);

                    break;
                case ($defaultedInvestment['Investment']['defaultedTime'] > 30):
                    $value["31-60"] = $value["31-60"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["31-60"] = round(($value["31-60"] / $outstanding) * 100, 2);

                    break;
                case ($defaultedInvestment['Investment']['defaultedTime'] > 7):
                    $value["8-30"] = $value["8-30"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["8-30"] = round(($value["8-30"] / $outstanding) * 100, 2);
                    break;
                case ($defaultedInvestment['Investment']['defaultedTime'] > 0):
                    $value["1-7"] = $value["1-7"] + $defaultedInvestment['Investment']['investment_outstandingPrincipal'];
                    $range["1-7"] = round(($value["1-7"] / $outstanding) * 100, 2);
                    break;
            }
        }
        //Calculate current
        $range["current"] = 100 - $range["1-7"] - $range["8-30"] - $range["31-60"] - $range["61-90"] - $range[">90"];
        //print_r($range);
        return $range;
    }

    /*
     * 
     * Update the corresponding fields in the paymenttotal table 
     * 
     */

    function afterSave1($created, $options = array()) {

    }

    /**
     *
     * 	Callback Function
     * 	Format the date
     *
     */
    public function afterFind1($results, $primary = false) {


        return $results;
    }

    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function beforeSave1($created, $options = array()) {

    }

}
