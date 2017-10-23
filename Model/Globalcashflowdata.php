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

class Globalcashflowdata extends AppModel
{
	var $name = 'Globalcashflowdata';
        var $useTable = "globalcashflowdatas";

        

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
        $prefix = "globalcashflowdata";
        $totalPrefix = "globalcashflowdatatotal";  
 
        foreach ($this->data['Globalcashflowdata'] as $globalcashflowKey => $value) {
            if ($globalcashflowKey == "userinvestmentdata_id") {
                $userinvestmentdataId = $value;
                break;
            }         
        }

        $this->Globalcashflowdatatotal = ClassRegistry::init('Globalcashflowdatatotal');
         
        // get the *latest* globalcashflowdatatotal table
        $latestValuesGlobalCashflowdata = $this->Globalcashflowdata->find("first",array(
                                                        'conditions' => array('userinvestmentdata_id' => $userinvestmentdataId),
                                                        'order' => array('Globalcashflowdata.id DESC'),
                                                         ) );

        foreach ($this->data['Globalcashflowdata'] as $globalCashflowKey => $value) {
            $globalCashflowKeyNames = explode("_", $globalCashflowKey);

            if ($globalCashflowKeyNames[0] == $prefix) {   // check if the field exists in table paymenttotals
                foreach ($latestValuesGlobalCashflowdata['Globalcashflowdata'] as $globalCashflowTotalKey => $globalcashflowItem) {
                    if ($globalCashflowTotalKey === $totalPrefix . "_" . $globalCashflowKeyNames[1]) {
                        $data [$globalCashflowTotalKey] = $globalcashflowItem + $value;
                        $data[$globalCashflowTotalKey] = sprintf("%017d", $data[$globalCashflowTotalKey]);  // Normalize length with leading 0's
                    }
                }
            } 
        }    
        $data ['userinvestmentdata_id'] = $userinvestmentdataId;
        $this->Globalcashflowdatatotal->save($data, $validate = true); 
    }

}