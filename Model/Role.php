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
*/

App::uses('CakeEvent', 'Event');
App::uses("AppModel", "Model");
class Role extends AppModel {
    var $name= 'Role';
    
    public $hasAndBelongsToMany = array(
        'Sector' =>
            array(
                'className' => 'Sector',
                'joinTable' => 'roles_sectors',
                'foreignKey' => 'role_id',
                'associationForeignKey' => 'sector_id'
            )
    );
    
    public $hasMany = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'role_id',
            ),
    );
    public $actsAs = array('Containable');
    
    /**
     * 
     * Translates the role_name to its role_id
     * 
     * @param string $roleName Name of the role to translate
     * @return int id of the role
     */
    function translateRoleName2RoleId($roleName) {

        $roleMenuData = $this->findByRoleName($roleName, $fields = ['Role.role_name', 'Role.id']);
        return($roleMenuData['Role']['id']);
    
    }
   
}