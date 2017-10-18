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

class Investment extends AppModel
{
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
     * 			
     */
    public function createNewInvestment($investmentdata) {
        
        if ($this->save($investmentdata, $validation = true)) {   // OK
            $investmentId = $this->id;
            $data = array('investment_id' => $investmentId ,
                            'paymenttotal_status' => ACTIVE);

            if ($this->Paymenttotal->save($data, $validation = true)) {                                   // OK
                $paymentId = $this->Payment->id;

                if ($this->save(array('id' => $investmentId, 
                                'paymenttotal_id' => $paymentId))) {
                    $result[0] = true;
                }
            } 
            else {
                $result[0] = false;
                $result[1] = $this->Paymenttotal->validationErrors;
                $this->delete($investmentId);
            }
        } 
        else {                     // error occurred while trying to save the Investment data
            $result[0] = false;
            $result[1] = $this->validationErrors;
        }
        return $result;         
    }





    /*
     * 
     * Update the corresponding fields in the paymenttotal table 
     * 
     */
    function afterSave1($created, $options = array()) {
        if (!empty($this->data['Investor']['investor_tempCode'])) {    // A confirmation code has been generated
            $event = new CakeEvent('confirmationCodeGenerated', $this, array('id' => $this->id,
                'investor' => $this->data[$this->alias],
            ));
            $this->getEventManager()->dispatch($event);
        }

        if (!empty($this->data['Investor']['investor_accountStatus'])) {  // A user has succesfully and completely registered
            if (($this->data['Investor']['investor_accountStatus'] & QUESTIONAIRE_FILLED_OUT) == QUESTIONAIRE_FILLED_OUT) {
                $event = new CakeEvent('newUserCreated', $this, array('id' => $this->id,
                    'investor' => $this->data[$this->alias],
                ));
                $this->getEventManager()->dispatch($event);
            }
        }
    }

    /**
     *
     * 	Callback Function
     * 	Format the date
     *
     */
    public function afterFind1($results, $primary = false) {

        foreach ($results as $key => $val) {
            if (isset($val['Investor']['investor_dateOfBirth'])) {
                $results[$key]['Investor']['investor_dateOfBirth'] = $this->formatDateAfterFind(
                        $val['Investor']['investor_dateOfBirth']);
            }
        }
        return $results;

        foreach ($results as $key => $val) {
            if (isset($val['Ocr']['investor_iban'])) {
                $results[$key]['Ocr']['investor_iban'] = $this->decryptDataAfterFind(
                        $val['Ocr']['investor_iban']);
            }
        }
        return $results;
    }

    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function beforeSave1($created, $options = array()) {

// Store telephone number without spaces
        if (!empty($this->data['Investor']['investor_dateOfBirth'])) {
            $this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
        }
        if (!empty($this->data['Investor']['investor_telephone'])) {
            $this->data['Investor']['investor_telephone'] = str_replace(' ', '', $this->data['Investor']['investor_telephone']);
        }
    }




}