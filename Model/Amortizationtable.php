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
    
   public $belongsTo = array(
       'Investmentslice' => array(
            'className' => 'Investmentslice',
            'foreignKey' => 'investmentslice_id'
        )
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
     *  @param  bigint  $investmentId             The investment_id of the investment 
     *  @param  bigint  $sliceIdentifier    Identifier of the investmentSlice to update
     *  @param  array   $data               Array with the payment data
     *  @return array   boolean             true Table has been updated
     */
    public function addPayment($companyId, $investmentId, $sliceIdentifier, $data)  {
$this->print_r2($investmentId);
$this->print_r2($sliceIdentifier);
$this->print_r2($data);           
     $this->Investment = ClassRegistry::init('Investment');
echo __FUNCTION__ . " " . __LINE__ . "\n";
        // support for partial payment is not fully implemented
        $data['paymentStatus'] = WIN_AMORTIZATIONTABLE_PAYMENT_PAID;
        
        $slices = $this->Investment->getInvestmentSlices($investmentId);
        if (Configure::read('debug')) {
            $this->print_r2($slices);
        }

        // get internal database reference, initially only 1 slice will be found
        foreach ($slices as $slice) {    
            if ($slice['investmentslice_identifier'] == $sliceIdentifier) {
                $sliceDbreference = $slice['id'];
                break;
            }
        }

        $amortizationTable = $this->readFullAmortizationTable($sliceDbreference);
        
        foreach ($amortizationTable as $table) {
            // look for the FIRST record which is *still* unpaid
            if ($table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {                 
                $tableDbReference = $table['id'];                 
                break;
            }
        }
                        
        if (empty($tableDbReference)) {
            return false;
        }    
  
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
        
        $paymentData['amortizationtable_id'] = $tableDbReference;
        
        if ($this->Amortizationpayment->save( $paymentData, array('validate' => true))) {     // update the amortizationTable
            $amortizationPaymentId = $this->Amortizationpayment->id;       
            $tableData = ['id' => $tableDbReference, 
                          'amortizationtable_paymentStatus' => $data['paymentStatus'],
                          'amortizationtable_paymentDate'   => $data['paymentDate']
                         ];
            
            if ($this->save($tableData, array('validate' => true))) { 
                return true;
            }
            else {
                $this->delete($amortizationPaymentId);                          // Do a rollback of the previous save operation
            }
        } 
        return false;        
    }   
    
    /** 
     *  Reads a next pending payment data according to the number of instalment that have been paid (or better: are still pending)
     *
     *  @param  bigint  $sliceId            The database Id of a investmentslice Model object (id)
     *  @return date of next pending instalment. Empty if all instalment have been paid
     */
    public function getNextPendingPaymentDate($sliceId) {
        $amortizationTable = $this->readFullAmortizationTable($sliceId);

        foreach ($amortizationTable as $table) {         
            // look for the FIRST record which is *still* unpaid
            if ($table['amortizationtable_paymentStatus'] == WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {                 
                return $table['amortizationtable_scheduledDate'];                 
            }  
        }                                 
        return;                                                                 // ALL INSTALMENTS HAVE BEEN PAID
    } 

    
    /** 
     *  Reads a complete amortization table
     *
     *  @param  bigint  $sliceId            The database Id of a investmentslice Model object (id)
     *  @return array   Amortizationtable   The complete amortization table. It contains *only* numeric indexes for the tables,
     */
    public function readFullAmortizationTable($sliceId) {
        $amortizationTable = $this->find("all", $params = [ 'recursive' => -1,
                                                            'conditions' => ['investmentslice_id' => $sliceId] ]
                                        );
       
        $amortizationTableNormalized = Hash::extract($amortizationTable, '{n}.Amortizationtable');       
        return $amortizationTableNormalized;
    }    
    
}