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
    
}
