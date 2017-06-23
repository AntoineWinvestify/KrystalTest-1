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


class Sector extends AppModel {
    var $name= 'Sector';
    
    public $hasAndBelongsToMany = array(
        'Role' =>
            array(
                'className' => 'Role',
                'joinTable' => 'roles_sectors',
                'foreignKey' => 'sector_id',
                'associationForeignKey' => 'role_id',
                'unique' => true,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            )
    );
    
    /*public $hasMany = array(
			'Linkedaccount' => array(
				'className' => 'Linkedaccount',
				'foreignKey' => 'investor_id',
				'fields' => '',
				'order' => '',
				),
			);*/

    /*public $hasOne = array (
				'User' => array(
				'className' => 'User',
				'foreignKey' => 'investor_id',
				'fields' => '',
				'order' => '',
				),
			);*/
    
    /**
    *
    *	Translates the unique userReference to the database reference
    *	@param 		string	$role It is the role that user has in the website
    * 	@return 	int	$investorId The database reference of the investor
    * 					
    */
    function getSectorsByRole($roleId = null){
        if (empty($roleId)) {
            return false;
        }
        $sectors = $this->find('all', array(
            'joins' => array(
                 array('table' => 'roles_sectors',
                    //'alias' => 'KitchensRestaurant',
                    'type' => 'INNER',
                    'conditions' => array(
                        'roles_sectors.role_id' => $roleId,
                        'roles_sectors.sector_id = sectors.id'
                    )
                )
            ),
            'group' => 'sectors.id'
        ));
        return $sectors;
    }
}
