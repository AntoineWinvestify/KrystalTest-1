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
* @date 2018-05-17
* @package
*
*/

/*

2018-05-17		version 0.1
initial version






Pending:







*/

class Globalamortizationtable extends AppModel
{
	var $name= 'Globalamortizationtable';


    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );

/*    public $hasMany = array(
        'Amortizationpayment' => array(
            'className' => 'Amortizationpayment',
            'foreignKey' => 'amortizationtable_id',         
            'fields' => '',
            'order' => '',
        ),
    );    
 */
    public $hasAndBelongsToMany = array(
        'Investmentslice' =>
                array(
                    'className' => 'Investmentslice',
                    'joinTable' => 'globalamortizationtables_investmentslices',
                    'foreignKey' => 'globalamortizationtable_id',
                    'associationForeignKey' => 'investmentslice_id',   
                    'unique' => true,
                 )
        
    );   

  
    
    /**
     * Function to save the global amortization table of a loan in case no individual amortization tables exist for the PFP. 
     * It also writes a flag in the corresponding investment model indicating that the/an amortization table is available
     * 
     * @param array     $amortizationData   It contains the amortization data of an investment(slice)
     * @param integer   $companyId          It holds the company_id for which the global amortization table has to be stored. 
     * @return boolean
     */
    public function saveGlobalAmortizationtable($amortizationData, $companyId) {
        $this->GlobalamortizationtablesInvestmentslice = ClassRegistry::init('GlobalamortizationtablesInvestmentslice');
        $this->Investmentslice = ClassRegistry::init('Investmentslice');
        $this->Investment = ClassRegistry::init('Investment');
        $this->GlobalamortizationtablesInvestmentslice = ClassRegistry::init('GlobalamortizationtablesInvestmentslice');        
        $globalAmortizationtable = [];
        $investmentSliceIds = [];
       
        $existingListExtended = $this->find("all", array(
                                        'conditions' => array('globalamortizationtable_companyId' => $companyId, ), 
                                        'recursive' => -1,
                                        'fields' => array('id', 'globalamortizationtable_loanId'),
                                        'group' => array('globalamortizationtable_loanId'), 
                                    ));

        $existingLoanIdsList = Hash::extract($existingListExtended, '{n}.Globalamortizationtable.globalamortizationtable_loanId');
//echo "Cleaned existing list = \n"; 
//echo __FILE__ . " " . __LINE__ . " \n ";
//print_r($existingLoanIdsList);

 // if actual amortizationtable is not in list then add it to DB
        foreach ($amortizationData as $loanId => $loanData) {
            unset($globalAmortizationtable);
            
            $loanIdInformation = explode("_", $loanId);                                     // [0] = investmentsliceId and [1] = loanId
            $sliceId = $loanIdInformation[0];
            $investmentSliceIds[] = $sliceId;
         
            if (!in_array($loanIdInformation[1], $existingLoanIdsList)) {
                $existingLoanIdsList[] = $loanIdInformation[1];

                foreach ($loanData as $value) {                                             // data as obtained per file
                    $value['globalamortizationtable_companyId'] = $companyId;               // adding "table" to the database
                    $value['globalamortizationtable_loanId'] = $loanIdInformation[1];
                    $value['globalamortizationtable_scheduledDate'] = $value['amortizationtable_scheduledDate'];
  //                  $value['globalamortizationtable_quoteNumber'] = $value['amortizationtable_quoteNumber'];
                    $value['globalamortizationtable_paymentStatus'] = $value['amortizationtable_paymentStatus'];    
                    $value['globalamortizationtable_paymentStatusOriginal'] = $value['amortizationtable_paymentStatusOriginal'];                    
                    $globalAmortizationtable[] = $value;
                }

                $this->saveMany($globalAmortizationtable, array('validate' => true,         // save all records related to 1 table
                                                        'callbacks' => "before",
                                                        ));                  
            }
            
            // The table is in the list, for sure, so get id's of the database tables for globalamortizationtable
            $amortizationTableIndexes = $this->find("list", array(
                                    'conditions' => array('globalamortizationtable_companyId' => $companyId,
                                                            'globalamortizationtable_loanId' => $loanIdInformation[1]), 
                                    'recursive' => -1,
                                    'fields' => array('id'),
                                )); 
          
            unset($combinedTable);
            
            foreach ($amortizationTableIndexes as $index) {
                $tempTable['globalamortizationtable_id'] = $index; 
                $tempTable['investmentslice_id'] = $sliceId;       
                $combinedTable[] = $tempTable;    
            }  
 
            $this->GlobalamortizationtablesInvestmentslice->create();         
            $this->GlobalamortizationtablesInvestmentslice->saveMany($combinedTable);
        }
      
        foreach ($investmentSliceIds as $investmentSliceId) {
            $conditions = array("id" => $investmentSliceId);       
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
     *  @return array   boolean             true Table has been updated or already existed
     *
     */
    public function addPayment($companyId, $loanId, $sliceIdentifier, $data)  {
print_r($loanId);
print_r($sliceIdentifier);
print_r($data);           
echo __FUNCTION__ . " " . __LINE__ . "\n";
        // support for partial payment is NOT fully implemented
        $data['paymentStatus'] = WIN_AMORTIZATIONTABLE_PAYMENT_PAID;
// Should be using the hasOne or hasMany relationship between Investment model and Investmentslice model
        $slices = $this->Investment->getInvestmentSlices($loanId);
print_r($slices);
        // get internal database reference of Investmentslice object
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



//+++++++++++++++++++++++++++++++++++++++++++++++     
        $filterConditions = array("investmentslice_id" => $sliceDbreference);   // Get the complete table for the loan
        $globalAmortizationTable = $this->GlobalamortizationtableInvestmentslice->find('all', array(
                                                'conditions' => $filterConditions,  
                                                'recursive' => 0,  // or 1? I neede hasOne relationship, incorporate smortizationPayment 
                                                ));
echo __FUNCTION__ . " " . __LINE__ . "\n";
print_r($globalAmortizationTable);           
      
            
        if (!empty($globalAmortizationTable)) {                                 // This is an error, and should NEVER happen.
            // Collect data for error analysis:
            $collectedErrorData['companyId'] = $companyId;
            $collectedErrorData['loanId'] = $loanId; 
            $collectedErrorData['sliceIdentifier'] = $sliceIdentifier;
            $collectedErrorData['data'] = $data;
          
            $this->Applicationerror = ClassRegistry::init('Applicationerror');
            $this->Applicationerror->saveAppError("ERROR ", json_encode($collectedErrorData), __LINE__, __FILE__, 0, 
                                                WIN_ERROR_AMORTIZATION_DATA_INCONSISTENCY);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Applicationerror in model Globalamortizationtable\n";
            }
            return;
        }     
        
        $payment = WIN_PAYMENT_DATA_NOT_STORED;    
        foreach ($globalAmortizationTable as $table) {
            if ($table['GlobalAmortizationPayment']['globalamortizationpayment_paymentDate'] == $data['paymentDate']) {              
                if (isset($table['AmortizationPayment']['amortizationpayment_paymentinterest'])) {
                    if ($table['AmortizationPayment']['globalamortizationpayment_paymentinterest'] == $data['interest']) {
                        $payment = WIN_PAYMENT_ALREADY_STORED;         
                    }
                }        
                if (isset($table['AmortizationPayment']['amortizationpayment_capitalRepayment']))  {
                    if ($table['GlobalAmortizationPayment']['amortizationpayment_capitalRepayment'] == $data['capitalRepayment'] ) {
                        $payment = WIN_PAYMENT_ALREADY_STORED;  
                    }
                }
                if (isset($table['AmortizationPayment']['amortizationpayment_capitalandInterestPayment'])) {
                    if ($table['GlobalAmortizationPayment']['amortizationpayment_capitalandInterestPayment'] == $data['capitalAndInterestPayment']) {
                        $payment = WIN_PAYMENT_ALREADY_STORED;  
                    }
                }
            }
        } 
        
        if  ($payment == WIN_PAYMENT_DATA_NOT_STORED) {  
            foreach ($amortizationTable as $table) {
                   // look for the FIRST record which is still unpaid
                if ($table['GlobalAmortizationPayment_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {
                    $tableDbReference = $table['Amortizationtable']['id']; 
                    break;
                }
            } 
        }
        else {                                                                  // WIN_PAYMENT_ALREADY_STORED
            return true;
        }
            
 //+++++++++++++++++++++++++++++++++++++++++++++++                            
        
        $payment['globalamortizationtable_id'] = $tableDbReference;
        $payment['amortizationpayment_paymentDate'] = $data['paymentDate'];
        $payment['amortizationpayment_capitalAndInterestPayment'] = $data['capitalAndInterestPayment'];
        $payment['amortizationpayment_interest'] = $data['interest'];
        $payment['amortizationpayment_capitalRepayment'] = $data['capitalRepayment'];               

 echo __FUNCTION__ . " " . __LINE__ . "\n";   
        // Store the payment related data
        if ($this->Glbalamortizationpayment->save($payment, array('validate' => true))) {
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