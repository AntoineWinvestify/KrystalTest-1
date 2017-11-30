<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                               |
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by        |
 * | the Free Software Foundation; either version 2 of the License, or           |
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-06-16
 * @package
 *
 * 
 * 
 */
class Userinvestmentdata extends AppModel {

    var $name = 'Userinvestmentdata';
    var $useTable = "userinvestmentdatas";
    public $hasMany = array(
        'Globalcashflowdata' => array(
            'className' => 'Globalcashflowdata',
            'foreignKey' => 'userinvestmentdata_id',
            'fields' => '',
            'order' => '',
        ),
    );
    
    /**
     * Get data of the last linked accounts investments of an investor.
     * @param string $investorIdentityId investor id.
     * @return array Last Userinvestmentdata rows for the linked accounts
     */
    public function getLastInvestment($investorIdentityId) {

        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');

        //Get linked accounts id
        $linkedAccountsId = $this->Linkedaccount->find("all", array("recursive" => -1,
            "conditions" => array("investor_id" => $investorIdentityId),
            "fields" => array("id"),
        ));

        //Get last Userinvestmentdata table row for a linked account id
        $resultInvestorData = array();
        foreach ($linkedAccountsId as $linkedAccountId) {
            $resultInvestorData[] = $this->find("first", array("recursive" => -1,
                "conditions" => array("linkedaccount_id" => $linkedAccountId['Linkedaccount']['id']),
                "order" => "created DESC",
            ));
        }
        
        //print_r($resultInvestorData);
        return $resultInvestorData;
    }

    /**
     * Get data of all the linked accounts of an investor.
     * @param string $investorIdentity investor identity number
     * @return array Global data
     */
    public function getGlobalData($investorIdentity) {

        $resultInvestorData = $this->find("all", array("recursive" => -1,
            "conditions" => array("userinvestmentdata_investorIdentity" => $investorIdentity),
        ));

        return $resultInvestorData;
    }
    
       
   /**
     *NOT FINISHED: does Globalcashflowdatatotal really need to exist?? or only Globalcashflowdata?
     * creates a new 'investment' table and also links the 'paymenttotal' database table
     * 	
     * 	@param 		array 	$investmentdata 	All the data to be saved
     * 	@return 	array[0]    => boolean
     *                  array[1]    => detailed error information if array[0] = false
     *                                 id if array[0] = true
     * 			
     */
    public function createUserInvestmentData($userInvestmentData) {
        $this->create();
        if ($this->save($userInvestmentData, $validation = true)) {   // OK
            $userInvestmentDataId = $this->id;
            $data = array('investment_id' => $userInvestmentDataId, 'status' => WIN_PAYMENTTOTALS_LAST);
            $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
     //       $this->Globalcashflowdata->create();
     //       if ($this->Globalcashflowdata->save($data, $validation = true)) { 
                $result[0] = true;
                $result[1] = $userInvestmentDataId;
       //     } 
         //   else {
          //      $result[0] = false;
           //     $result[1] = $this->Globalcashflowdata->validationErrors;
           //     $this->delete($userInvestmentDataId);
          //  }
        } 
        else {                     // error occurred while trying to save the Investment data
            $result[0] = false;
            $result[1] = $this->validationErrors;
        }
        return $result;         
    }
    
    
    public function getInvestmentIdByLoanId($loanIds) { // NOT NEEDED?? repalce with getData
        $fields = array('Investment.investment_loanReference', 'Investment.id');
        $conditions = array('investment_loanReference' => $loanIds);
        $investmentIds = $this->find('list', $params = array('recursive' => -1,
            'fields' => $fields,
            'conditions' => $conditions
        ));
        return $investmentIds;
    }       
    
    public function saveDataByType($linkedaccountId, $date, $data) {
        $conditions = array(
            'linkedaccount_id' => $linkedaccountId,
            'date'
            );
        $this->saveField($data['type'], $data['data']);
    }
}
