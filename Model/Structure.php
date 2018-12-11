<?php
/**
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +---------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 */
/*
 * 
 * 
 * 2017-08-10
 * function getStructure
 * saveStructure
 * 
 */
App::uses('CakeEvent', 'Event');

class Structure extends AppModel {

    var $name = 'Structure';
    var $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        )
    );

    public function getStructure($companyId, $type) {
        echo $companyId . ' ' . $type;
        $structure = $this->find('first', array(
            'conditions' => array('company_id' => $companyId, 'structure_type' => $type),
            'order' => array('created DESC'),
            'recursive' => -1,
        ));
        return $structure;
    }

    public function saveStructure($data) {
        return $this->save($data);
    }

}
