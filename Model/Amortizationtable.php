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

// update mark in investment model stating that amortizationtable is available        
 //       $index = 0;
 //       $controlIndex = 0;
 //       $limit = WIN_DATABASE_READOUT_LIMIT;       
    //    do {    
            foreach ($investmentsliceIds as $investmentsliceId) {
                $conditions = array("id" => $investmentsliceId);       
                $result = $this->Investmentslice->find('first', $params = array('recursive' => -1,
                                                                               'fields' => array("id", "investment_id"),
                                                                               'conditions' => $conditions
                                            ));

 //               if (count($result) < $limit) {          // No more results available
 //                   $controlIndex = 1;
 //               }
                
                $tempArray = array("id" => $result['Investmentslice']['investment_id'], 
                                   'investment_amortizationTableAvailable' => WIN_AMORTIZATIONTABLES_AVAILABLE  );
                $investmentIds[] = $tempArray;
            }
    //        $index++;
    //    }
    //    while($controlIndex < 1); 
            
        $this->Investment->saveMany($investmentIds, array('validate' => true));      
    return true;
    }


    
    
    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function beforeSave($created, $options = array()) {

        if ($created) {
            if ($this->data['Amortizationtable']['amortizationtable_paymentStatus'] !=  WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED) {
                if ($this->data['Amortizationtable']['amortizationtable_paymentDate'] ==  "" ||
                        $this->data['Amortizationtable']['amortizationtable_paymentDate'] ==  "0000-00-00") {
                    $this->data['Amortizationtable']['amortizationtable_paymentDate'] = date("Y-m-d");
                }
            }

        }
    }    
    
}