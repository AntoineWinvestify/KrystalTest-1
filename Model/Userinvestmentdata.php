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
     * Generic function to  get data of the table
     * 
     * @param array $filter filter of the table  ---> array("key" => value, ...)
     * @param array $field Fields you want get   ---> array(field, ...);
     */
    public function getData($filter, $field = null) {

        $resultData = $this->find("all", array("recursive" => -1,
            "conditions" => $filter,
            "fields" => $field,
        ));

        return $resultData;
    }

}
