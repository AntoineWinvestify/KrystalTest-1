<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
* @author 
* @version 0.1
* @date 2012-02-12
* @package
*/
/*


2017-10-18		version 0.1
initial version

/**
 * Description of Dashboardoverviewdata
 *
 */
class Dashboardoverviewdata extends AppModel {
    
    var $name = 'Dashboardoverviewdata';
    var $useTable = "dashboardoverviewdatas";
    
    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );

    /**
     * Get data of the last global Overview of an investor.
     * 
     * @param string $investorId             investor database id.
     * @return array Last Dashboardoverviewdata rows for the user
     */
    public function getLastOverview($investorId) {
        return $this->getData(["investor_id" => $investorId], ['*'], ["date DESC"], 1, "first");
    }

    public $belongsTo = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' =>  'investor_id'
        )
    );
    
    
    /**
     * Generic search for a field to use in the graph of the api.
     * 
     * @param int $linkedAccountId
     * @param string $period                                                    Time period. For now can be "year" or "all"
     * @param string $field
     * @param boolean                                                           //If true, the $id is investor_id, if empty the $id id linkedaccountid
     * @return boolean
     */
    public function genericGraphSearch($id, $period, $field, $fromInvestor = null){
        
        if(empty($fromInvestor)){
            $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
            $investorId = $this->Linkedaccount->getInvestorFromLinkedaccount($id);      
        } 
        else{
            $investorId = $id;
        }
        $conditions = ['investor_id' => $investorId];
        switch ($period['period']) {
            case "all":
                break;
            case "year":
                App::uses('CakeTime', 'Utility');
                $conditions['date >='] = CakeTime::format('-1 year', '%Y-%m-%d');
                break;
            default:
                return false;
        }

        $result = $this->find('all', $param = [
            'conditions' => $conditions,
            'fields' => ['id', 'date',
                "$field as value"
            ],
            'order' => 'date ASC',
            'recursive' => -1,
        ]);
        return $result;
    }
    
    
}
