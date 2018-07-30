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
*/
/*


2017-10-18		version 0.1
initial version



Pending:


*/

class Globalcashflowdata extends AppModel
{
    var $name = 'Globalcashflowdata';
    var $useTable = "globalcashflowdatas";


    
    public $belongsTo = array(
        'Userinvestmentdata' => array(
            'className' => 'Userinvestmentdata',
            'foreignKey' =>  'userinvestmentdata_id'
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
     */
    function afterSave($created, $options = array()) {

    // RULE: net desposits => globalcashflowdata_platformDeposits - globalcashflowdata_platformWithdrawals   
        if ($created) {		
            $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
            $userInvestmentdataId = $this->data['Globalcashflowdata']['userinvestmentdata_id'];

            $latestUserInvestmentData = $this->Userinvestmentdata->find("first",array(
                                                            'conditions' => array('id' => $userInvestmentdataId),
                                                            'order' => array('id DESC'),
                                                            'recursive' => -1
                                                             ));        

            if ($latestUserInvestmentData['Userinvestmentdata']['userinvestmentdata_totalNetDeposits'] == NULL ) {
                $originalNetDeposits = "0.0";
            }
            else {
                $originalNetDeposits = $latestUserInvestmentData['Userinvestmentdata']['userinvestmentdata_totalNetDeposits'];
            }

            $newNetDeposits = bcadd($originalNetDeposits, $this->data['Globalcashflowdata']['globalcashflowdata_platformDeposits'], 16 );
            $newNetDeposits = bcsub($newNetDeposits, $this->data['Globalcashflowdata']['globalcashflowdata_platformWithdrawals'], 16);

            $this->Userinvestmentdata->clear();
            $temp = array('userinvestmentdata_totalNetDeposits' => $newNetDeposits,
                            'id' => $userInvestmentdataId
                        );
            
            if (!$this->Userinvestmentdata->save($temp, $validate = true)) {
                echo "WRITE ERROR OCCURED IN USERINVESTMENTDATA"; 
            } 
 	}
    }  
 
   
    
    
}
