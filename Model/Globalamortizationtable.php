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
     * It also writes a flag in the corresponding investment model indicating that the amortization table is available
     * 
     * @param array     $amortizationData   It contains the amortization data of an investment(slice)
     * @param int       $companyId          It holds the company_id for which the global amortization table has to be stored. 
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
                                        'conditions' => array('globalamortizationtable_companyId' => $companyId), 
                                        'recursive' => -1,
                                        'fields' => array('id', 'globalamortizationtable_loanId'),
                                        'group' => array('globalamortizationtable_loanId'), 
                                    ));

        $existingLoanIdsList = Hash::extract($existingListExtended, '{n}.Globalamortizationtable.globalamortizationtable_loanId');

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
                    $value['globalamortizationtable_quoteNumber'] = $value['amortizationtable_quoteNumber'];
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
            
            foreach ($amortizationTableIndexes as $index) {
                $tempTable['globalamortizationtable_id'] = $index; 
                $tempTable['investmentslice_id'] = $sliceId;       
                $combinedTable[] = $tempTable;    
            }  
 
            $this->GlobalamortizationtablesInvestmentslice->create();         
            $this->GlobalamortizationtablesInvestmentslice->saveMany($combinedTable);
            unset($combinedTable);
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
     *  @param  bigint  $investmentId             The investment_id of the investment 
     *  @param  bigint  $sliceIdentifier    Identifier of the investmentSlice to update
     *  @param  array   $data               Array with the payment data
     *  @return array   boolean             true Table has been updated
     */
    public function addPayment($companyId, $investmentId, $sliceIdentifier, $data)  {
        $this->Investment = ClassRegistry::init('Investment');
        $this->Amortizationpayment = ClassRegistry::init('Amortizationpayment');
$this->print_r2($investmentId);
$this->print_r2($sliceIdentifier);
$this->print_r2($data);           

        // support for partial payment is NOT fully implemented
        $data['paymentStatus'] = WIN_AMORTIZATIONTABLE_PAYMENT_PAID;
        $slices = $this->Investment->getInvestmentSlices($investmentId);

        if (Configure::read('debug')) {
            $this->print_r2($slices);
        }
        
        // get internal database reference of Investmentslice object, we will find only 1 slice
        foreach ($slices as $slice) {                                           
            if ($slice['investmentslice_identifier'] == $sliceIdentifier) {
                $sliceDbreference = $slice['id'];
                break;
            }
        }
         
        $globalAmortizationTable = $this->readFullAmortizationTable($sliceDbreference);

        if (empty($globalAmortizationTable)) {                                  // This is an error, and *should* NEVER happen.
            // Collect data for error analysis:
            $collectedErrorData['companyId'] = $companyId;
            $collectedErrorData['loanId'] = $loanId; 
            $collectedErrorData['sliceIdentifier'] = $sliceIdentifier;
            $collectedErrorData['data'] = $data;
          
            $this->Applicationerror = ClassRegistry::init('Applicationerror');
            $this->Applicationerror->saveAppError("ERROR ", json_encode($collectedErrorData), __LINE__, __FILE__, 0, 
                                                WIN_ERROR_AMORTIZATION_DATA_INCONSISTENCY);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Applicationerror in model Globalamortizationtable<br/>";
            }
            return false;
        }   
        
        $payment = WIN_PAYMENT_DATA_NOT_STORED; 
    
        foreach ($globalAmortizationTable as $table) {        
            if ($table['globalamortizationtable_paymentDate'] == $data['paymentDate']) {
echo __FUNCTION__ . " " . __LINE__ . " Payment already stored<br/>"; 
                $payment = WIN_PAYMENT_ALREADY_STORED;
            }
        } 
               
        if  ($payment == WIN_PAYMENT_DATA_NOT_STORED) {                         // We encountered a new payment for the first time          
            foreach ($globalAmortizationTable as $table) {
                // look for the FIRST record which is *still* unpaid
                if ($table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {                 
                    $tableDbReference = $table['id'];                 
                    break;
                }
            } 
        }
        else {                                                                  // WIN_PAYMENT_ALREADY_STORED
            return true;
        }
              
        // Store the payment related data
        $paymentData['amortizationpayment_paymentDate'] = $data['paymentDate'];
        
        if (isset($data['capitalAndInterestPayment'])) {
            $paymentData['amortizationpayment_capitalAndInterestPayment'] = $data['capitalAndInterestPayment'];
        }
        if (isset($data['interest'])) {       
            $paymentData['amortizationpayment_interest'] = $data['interest'];
        }
        if (isset($data['capitalRepayment'])) {
            $paymentData['amortizationpayment_capitalRepayment'] = $data['capitalRepayment']; 
        } 
       
        $paymentData['globalamortizationtable_id']= $tableDbReference;
            
        if ($this->Amortizationpayment->save( $paymentData, array('validate' => true))) {     // update the amortizationTable
            $amortizationPaymentId = $this->Amortizationpayment->id;       
            $tableData = ['id' => $tableDbReference, 
                          'globalamortizationtable_paymentStatus' => $data['paymentStatus'],
                          'globalamortizationtable_paymentDate'   => $data['paymentDate']
                         ];
            
            if ($this->save($tableData, array('validate' => true))) { 
                return true;
            }
            else {
                $this->delete($amortizationPaymentId );                         // Do a rollback of the previous save operation
            }
        } 
        return false;
    }   

    
 
    
    /** 
     *  Reads a complete Globalamortization table
     *
     *  @param  bigint  $sliceId            The database Id of a investmentslice Model object (id)
     *  @return array   Globalamortizationtable The complete global amortization table. It contains *only* numeric indexes for the tables,
     */
    public function readFullAmortizationTable($sliceId) {

        $this->GlobalamortizationtablesInvestmentslice = ClassRegistry::init('GlobalamortizationtablesInvestmentslice');

        $lists = $this->GlobalamortizationtablesInvestmentslice->find("all",  array('conditions' => array('investmentslice_id' => $sliceId), 
                                                          'fields' => array('id', 'globalamortizationtable_id')
                                        )); 

        foreach ($lists as $list) {
            $filteringConditions = array('id' => $list['GlobalamortizationtablesInvestmentslice']['globalamortizationtable_id']);
            $result = $this->find("first", array('conditions' => $filteringConditions,
                                  ));  
            $globalTable[] = $result;
        }  
        
        $globalAmortizationTableNormalized = Hash::extract($globalTable, '{n}.Globalamortizationtable');     
        return $globalAmortizationTableNormalized;
    }
   
  
    
    /** 
     *  Reads a next pending payment data according to the number of instalment that have been paid (or better: are still pending)
     *
     *  @param  bigint  $sliceId            The database Id of a investmentslice Model object (id)
     *  @return date of next pending instalment. Empty if all instalment have been paid
     */
    public function getNextPendingPaymentDate($sliceId) {
        
        $globalAmortizationTable = $this->readFullAmortizationTable($sliceId);

        foreach ($globalAmortizationTable as $table) {
            // look for the FIRST record which is *still* unpaid
            if ($table['globalamortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {                 
                return $table['globalamortizationtable_scheduledDate'];                 
            }  
        }                                 
        return;                                                                 // ALL INSTALMENTS HAVE BEEN PAID
    }    
   
}