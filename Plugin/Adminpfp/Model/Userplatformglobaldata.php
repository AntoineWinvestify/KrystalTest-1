<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, http://www.winvestify.com                         |
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
//

2017-07-05	  version 0.1





*/

App::uses('AppModel', 'Model');
class Userplatformglobaldata extends AppModel
{
        var $useDbConfig = 'mldata';   
	public $name = 'Userplatformglobaldatas';

        public $belongsTo = array(
            'Investorglobaldata' => array(
            'className' => 'Investorglobaldata',
            'foreignKey' => 'investorglobaldata_id'
            )
        );


/**
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();






}