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
* @date 2017-11-08
* @package
*/
/*

2017-11-08		version 0.1
initial version





Pending:







*/

class Payment extends AppModel
{
    var $name= 'Payment';
    
    public $belongsTo = array(
        'Investment' => array(
            'className' => 'Investment',
            'foreignKey' =>  'investment_id'
        )
    );
        
/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);




    /*
     * 
     * Update the corresponding fields in the 'paymenttotal' table 
     * 
     */
    function afterSave($created, $options = array()) {

        $data = array();
        $paymentPrefix = "payment";
        $paymentTotalPrefix = "paymenttotal";  

        $this->Paymenttotal = ClassRegistry::init('Paymenttotal');
         
        // get the *latest* paymenttotal table
        $latestValuesPaymenttotals = $this->Paymenttotal->find("first",array(
                                                        'conditions' => array('investment_id' => $this->data['Payment']['investment_id']),
                                                        'order' => array('Paymenttotal.id DESC'),
                                                         ) );
        
        // Copy all the 'totalvalue's data of the latest paymenttotal, if it exists
        if (!empty($latestValuesPaymenttotals['Paymenttotal'])) {
            foreach ($latestValuesPaymenttotals['Paymenttotal'] as $paymentTotalsKey => $paymentItem) {
                $paymentTotalsKeyNames = explode("_", $paymentTotalsKey);
                if ($paymentTotalsKeyNames[0] == $paymentTotalPrefix) {
                    $data[$paymentTotalsKey] = $paymentItem;
                }              
            }
        }   
        
        foreach ($this->data['Payment'] as $paymentKey => $value) {
              // check if the field also exists in table 'paymenttotals'
            $paymentKeyNames = explode("_", $paymentKey);
            if (count($paymentKeyNames) == 2) {         // in order to remove the "Notice Error" message
                $paymentTotalsKey = $paymentTotalPrefix . "_" . $paymentKeyNames[1];          
                if (!empty($latestValuesPaymenttotals['Paymenttotal'])) {
                    if ($paymentKeyNames[0] == $paymentPrefix) { 
                        $tempTotals = $latestValuesPaymenttotals['Paymenttotal'][$paymentTotalPrefix . "_" . $paymentKeyNames[1]];
                        $data [$paymentTotalsKey] = bcadd($value, $tempTotals, 16);
                    }
                }    
                else {
                    $data [$paymentTotalsKey] = $value;
                }
            }
        }    
        
        $this->Paymenttotal->create();
        $data['investment_id'] = $this->data['Payment']['investment_id'];
        $data['date'] = $this->data['Payment']['date'];
        $data['status'] = WIN_PAYMENTTOTALS_LAST;
        $this->Paymenttotal->save($data, $validate = true); 

        if (!empty($latestValuesPaymenttotals['Paymenttotal'])) {  // probably NOT necessary to save status
            $this->Paymenttotal->create();
            $this->Paymenttotal->id = $latestValuesPaymenttotals['Paymenttotal']['id'];
            $this->Paymenttotal->saveField("status", WIN_PAYMENTTOTALS_OLD); 
        }
    }
}