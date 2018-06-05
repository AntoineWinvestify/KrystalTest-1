<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2018, https://www.winvestify.com                        |
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
* @date 2018-04-04
* @package
*


2018-04-04		version 0.1
initial version





Pending:







*/

class Amortizationtable extends AppModel
{
    var $name= 'Amortizationtable';


    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );

    public $hasMany = array(
        'Amortizationpayment' => array(
            'className' => 'Amortizationpayment',
            'foreignKey' => 'amortizationtable_id',
            'fields' => '',
            'order' => '',
        ),
    );    
    
   

    
    /**
     * Function to save the amortization table of a pfp 
     * It also writes a flag in the corresponding investment model indicating that the/an amortization table is available
     * 
     * @param array $amortizationData   It contains the amortization data of an investment(slice)
     * @return boolean
     */
    public function saveAmortizationtable($amortizationData) {
        $this->Investmentslice = ClassRegistry::init('Investmentslice');
        $this->Investment = ClassRegistry::init('Investment');
        $amortizationtable = [];
        $investmentsliceIds = [];

// connect amortization table to the correct Investmentslice model        
        foreach ($amortizationData as $loanId => $loanData) {
            foreach ($loanData as $value) {
                $loanIdInformation = explode("_", $loanId);
                $value['investmentslice_id'] = $loanIdInformation[0];
                if (!in_array($loanIdInformation[0], $investmentsliceIds)) {
                    $investmentsliceIds[] = $loanIdInformation[0];
                }
                $amortizationtable[] = $value;
            }
        }
        $this->saveMany($amortizationtable, array('validate' => true,
                                                'callbacks' => "before",
                                                ));
   
        foreach ($investmentsliceIds as $investmentsliceId) {
            $conditions = array("id" => $investmentsliceId);       
            $result = $this->Investmentslice->find('first', $params = array('recursive' => -1,
                                                                           'fields' => array("id", "investment_id"),
                                                                           'conditions' => $conditions
                                        ));

            $tempArray = array("id" => $result['Investmentslice']['investment_id'], 
                               'investment_amortizationTableAvailable' => WIN_AMORTIZATIONTABLES_AVAILABLE  );
            $investmentIds[] = $tempArray;
        }
    
        $this->Investment->saveMany($investmentIds, array('validate' => true));      
        return true;
    }


    
 
    
    /** 
     *  Updates the amortization table of an investment slice and creates the corresponding payment.
     *
     *  @param  int     $companyId          The company_id of the PFP
     *  @param  bigint  $loanId             The unique loanId 
     *  @param  bigint  $sliceIdentifier    Identifier of the investmentSlice to update
     *  @param  array   $data               Array with the payment data
     *  @return array   boolean             true Table has been updated
     */
    public function addPayment($companyId, $loanId, $sliceIdentifier, $data)  {
print_r($loanId);
print_r($sliceIdentifier);
print_r($data);           
echo __FUNCTION__ . " " . __LINE__ . "\n";
        // support for partial payment is not fully implemented
        $data['paymentStatus'] = WIN_AMORTIZATIONTABLE_PAYMENT_PAID;
        
// Should be using the hasOne or hasMany relationship between Investment model and Investmentslice model
        $slices = $this->Investment->getInvestmentSlices($loanId);
print_r($slices);
        // get internal database reference
// Should be using the hasOne or hasMany relationship between Investment model and Investmentslice model
        foreach ($slices as $slice) {                                           // Initially we will find only 1 slice
            echo __FUNCTION__ . " " . __LINE__ . " sliceIdentifier = $sliceIdentifier to be compared with " . $slice['investmentslice_identifier'] . "\n";
            if ($slice['investmentslice_identifier'] == $sliceIdentifier) {
                echo __FUNCTION__ . " " . __LINE__ . " \n";
                $sliceDbreference = $slice['id'];
                echo "REFERENCE = $sliceDbreference\n";
                break;
            }
        }

        // support for partial payment is not fully implemented
        $filterConditions = array('amortizationtable_paymentStatus' => WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED);           
        $amortizationTable = $this->Investmentslice->getAmortizationTable($sliceDbreference, $filterConditions);    // all entries of table which are not yet paid 
            echo __FUNCTION__ . " " . __LINE__ . " sliceDbreference = $sliceDbreference\n";
            print_r($amortizationTable);

        $tableDbReference = $amortizationTable[0]['Amortizationtable']['id'];   // Take the first one with status 'WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED'                               
        
        $payment['amortizationtable_id'] = $tableDbReference;
        $payment['amortizationpayment_paymentDate'] = $data['paymentDate'];
        $payment['amortizationpayment_capitalAndInterestPayment'] = $data['capitalAndInterestPayment'];
        $payment['amortizationpayment_interest'] = $data['interest'];
        $payment['amortizationpayment_capitalRepayment'] = $data['capitalRepayment'];               
  
 echo __FUNCTION__ . " " . __LINE__ . "\n";   
        // Store the payment related data
        if ($this->Amortizationpayment->save($payment, array('validate' => true))) {
            // update the amortizationTable
            $amortizationId = $this->Amortizationpayment->id;
            $tableData['id'] = $tableDbReference;
            $tableData['amortizationtable_paymentStatus'] = $data['paymentStatus'];
            if ($this->Amortizationtable->save($tableData, array('validate' => true))) {           
                return true;
            }
            else {
                $this->Amortizationpayment->delete($tableDbReference);
                return false;
            }
        } 
        else {
            return false;
        }
    }   

}