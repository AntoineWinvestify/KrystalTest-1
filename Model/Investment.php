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
     * creates a new 'investment' table and also links an 'investmentSlice' database table.
     * One field of the $investmentdata must be $investmentdata['sliceIdentifier']. This call
     * will FAIL if $investmentdata['investment_sliceIdentifier'] is empty or non-existent.
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
        if (!isset($investmentdata['investment_sliceIdentifier'])) {
            echo "ANANANANANAN%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%";
            $result[0] = false;            
            return $result;
        }
echo "€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€€ee";
        if ($this->save($investmentdata, $validate = true)) {   // OK
            $investmentId = $this->id;
            
            $this->Investmentslice = ClassRegistry::init('Investmentslice');
            $this->Investmentslice->create();
          
            if ($this->Investmentslice->getNewSlice($investmentId, $investmentdata['investment_sliceIdentifier'])) {
                $result[0] = true;
                $result[1] = $investmentId;  
            }
            else {
                $result[0] = false;
            }
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
     * Get defaulted investment of a linked account and save delayed days
     * 
     * @param Int $linkedaaccount Link account id 
     * @return array Defaulted inversions of a linked account with the defaulted days
     */
    public function getDefaulted($linkedaccount) {

        $today = date("Y-m-d");
        /*************************************/
        /* Get defaulted investment and days */
        /*************************************/
        $defaultedInvestments = $this->find("all", array(
            "fields" => array("id", "investment_loanId", "investment_outstandingPrincipal", "investment_nextPaymentDate", "investment_statusOfLoan"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_nextPaymentDate < " => $today, "investment_statusOfLoan" => 2),
            "recursive" => -1,
        ));

        foreach ($defaultedInvestments as $key => $defaultedInvestment) {
            //echo strtotime($today) . HTML_ENDOFLINE;
            //echo strtotime($defaultedInvestment['Investment']['investment_nextPaymentDate']) . HTML_ENDOFLINE;
            $defaultedInvestments[$key]['Investment']['investment_paymentStatus'] = -(strtotime($defaultedInvestment['Investment']['investment_nextPaymentDate']) - strtotime($today)) / (60 * 60 * 24);
        }

        $this->saveMany($defaultedInvestments); //Save delayed days
        
       // print_r($defaultedInvestments);
        return $defaultedInvestments;
    }

    /**
     * Get defaulted percent with the Outstanding principal.
     * 
     * @param Int $linkedaccount Link account id 
     */
    public function getDefaultedByOutstanding($linkedaccount) {

        //Get total outstanding principal
        $outstandings = $this->find("all", array(
            "fields" => array("investment_outstandingPrincipal"),
            "conditions" => array("linkedaccount_id" => $linkedaccount, "investment_statusOfLoan" => WIN_LOANSTATUS_ACTIVE),
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
        //print_r($defaultedInvestments);
        foreach ($defaultedInvestments as $defaultedInvestment) {
            switch ($defaultedInvestment['Investment']['investment_paymentStatus']) {
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
        //print_r($range);
        return $range;
    }

 

    /**
     *
     * 	Create a new Investmentslice when a new investment takes place in an existing loan
     * 	
     */
    function beforeSave($created, $options = array()) {

        if (isset($this->data[$this->alias]['id'])) {       // = UPDATE of existing model
            if (isset($this->data[$this->alias]['markCollectNewAmortizationTable'])) { // adding a new slice
                if ($this->data[$this->alias]['markCollectNewAmortizationTable'] == "AM_TABLE") {
                    $this->Investmentslice = ClassRegistry::init('Investmentslice');

                    $data = array( "investment_id" => $this->data[$this->alias]['id'],
                                    "sliceIdentifier" => $this->data[$this->alias]['sliceIdentifier']
                                );

                    if (!$this->Investmentslice->save($data, $validate = true)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}
