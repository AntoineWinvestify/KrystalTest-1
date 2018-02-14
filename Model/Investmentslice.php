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


2017-10-18		version 0.1
initial version





Pending:







*/

class Investmentslice extends AppModel
{
	var $name = 'Investmentslice';


    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );


    public $belongsTo = array(
        'Investment' => array(
            'className' => 'Investment',
            'foreignKey' =>  'investment_id'
        )
    );   
    
    public $hasMany = array(
        'Amortizationtable' => array(
            'className' => 'Amortizationtable',
            'foreignKey' => 'investmentslice_id',
            'fields' => '',
            'order' => '',
        ),
    );   
    
    
    /** NOT YET TESTED
     * Creates a new slice for a loan.
     *        
     * 	@param 		bigint 	$investmentId    	Link to the corresponding Investment table
     *  @param          string  $sliceIdentifier        Unique identifier of the slice
     * 	@return 	bigint                          Database Id of the slice   
     * 			
     */
    public function getNewSlice ($investmentId, $sliceIdentifier) {

        $investmentSliceData = array('investment_id' => $investmentId,
                                     'investmentslice_identifier' => $sliceIdentifier);

        $this->create();
        if ($this->save($investmentSliceData, $validation = true)) {   // OK
            return $this->id;
        }
        else {
            return null;
        }
    }

    
 
    /** NOT YET TESTED
     * Reads the date of the next [expected] payment
     *        
     * 	@param 		bigint 	$investmentId    	Link to the corresponding Investment table
     * 	@return 	bigint                          Database Id of the slice   
     * 			
     */
    public function getNextPaymentDate ($investmentId) {

        $this->create();
	$this->Behaviors->load('Containable');
	$this->contain('Amortizationtable');  	

        $amortizationSchedule = $this->find("all", array(
                                                "conditions" => array("investment_id" => $investmentId),
                                                "recursive" => 0,
                                                ));
        // cycle through the resulting array for the *next* date
        foreach ($amortizationSchedule as $paymentKey => $payment) {
            
        }
        
        
        return;
    }
    
    /** NOT YET TESTED
     * Reads the amount of the next [expected] payment
     *        
     * 	@param 		bigint 	$investmentId    	Link to the corresponding Investment table
     * 	@return 	bigint                          Database Id of the slice   
     * 			
     */
    public function getNextPaymentAmount ($investmentId) {
        $this->create();
	$this->Behaviors->load('Containable');
	$this->contain('Amortizationtable');  	

        $amortizationSchedule = $this->find("all", array(
                                                "conditions" => array("investment_id" => $investmentId),
                                                "recursive" => 0,
                                                ));
        // cycle through the resulting array for the next amount due
        foreach ($amortizationSchedule as $paymentKey => $payment) {
            
        }
        
    }    






    /** NOT YET TESTED
     *  Reads the amortization table of an investment slice
     * 
     *  @param  array   $slice      Database reference of investmentslice model  
     *  @param  $filterConditions   filter conditions which apply to the amortization data
     *  @return array   
     */
    public function getAmortizationTable($slice, $filterConditions) {
        
        $conditions = array_merge(array("id" => $slice), $filterConditions);
        $result = $this->Amortizationtable->find('all', array(
                'conditions' => $conditions, 
                'recursive' => 1, 
            ));
        
        return $result;
    }
    
 
    /** NOT YET TESTED
     *  Updates the amortization table of an investment slice
     * 
     *  @param  array   array with the current transaction data, including database reference of table
     *  @return array   boolean  
     */
    public function UpdateAmortizationTable($data)  {
        
        if ($this->Amortizationtable->save($data, $validate = true)) {
           return true;
        }
        return false;
    }  
    
    
    /** NOT YET TESTED
     *  Deletes the *amortization table* of an investment slice. The sliceIdentifier model 
     *  IS NOT DELETED, 
     * 
     *  @param  bigint  Database reference of model investmentslice
     *  @return array   boolean     
     */
    public function DeleteAmortizationTable($slice)  {
        $conditions = array('investmentslice_id' => $slice);
        $result = $this->Amortizationtable->deleteAll($conditions, false, false);
        return $result;
    } 





    

}