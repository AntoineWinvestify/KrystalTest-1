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

class Payment extends AppModel
{
	var $name= 'Payment';


        
/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);






    /*
     * **** CALLBACK FUNCTIONS *****
     */

    /*
     * 
     * Update the corresponding fields in the 'paymenttotal' table 
     * 
     */
    function afterSave($created, $options = array()) {

        $data = array();
        $paymentPrefix = "payment";
        $paymentTotalPrefix = "paymenttotal";  
        echo __FUNCTION__ . " " . __LINE__ . "\nPayment data = \n";
 print_r($this->data['Payment']);

echo __FUNCTION__ . " " . __LINE__ . " InvestmentId = " . $this->data['Payment']['investment_id'] . " and " . $this->data['Payment']['id'] . " \n";
        $this->Paymenttotal = ClassRegistry::init('Paymenttotal');
         
        // get the *latest* paymenttotal table
        $latestValuesPaymenttotals = $this->Paymenttotal->find("first",array(
                                                        'conditions' => array('investment_id' => $this->data['Payment']['investment_id']),
                                                        'order' => array('Paymenttotal.id DESC'),
                                                         ) );
echo "latestValues\n";
        print_r($latestValuesPaymenttotals);

        $this->Paymenttotal->create();
        foreach ($this->data['Payment'] as $paymentKey => $value) {
            $paymentKeyNames = explode("_", $paymentKey);

            if ($paymentKeyNames[0] == $paymentPrefix) {   // check if the field exists in table paymenttotals
                foreach ($latestValuesPaymenttotals['Paymenttotal'] as $paymentTotalKey => $paymentItem) {
                    if ($paymentTotalKey === $paymentTotalPrefix . "_" .$paymentKeyNames[1]) {
                        $data [$paymentTotalKey] = bcadd($paymentItem, $value, 16);
                    }
                }
            } 
        }    
        $data['investment_id'] = $this->data['Payment']['investment_id'];
        $data['status'] = WIN_PAYMENTTOTALS_LAST;
        $this->Paymenttotal->save($data, $validate = true); 
         
        $this->Paymenttotal->create();
        $this->Paymenttotal->id = $latestValuesPaymenttotals['Paymenttotal']['id'];
        $this->Paymenttotal->save(array("status" => WIN_PAYMENTTOTALS_OLD));              
    }


}