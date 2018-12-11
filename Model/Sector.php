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


class Sector extends AppModel {
    var $name= 'Sector';
    
    public $hasAndBelongsToMany = array(
        'Role' =>
            array(
                'className' => 'Role',
                'joinTable' => 'roles_sectors',
                'foreignKey' => 'sector_id',
                'associationForeignKey' => 'role_id'
            )
    );
    
    
}
