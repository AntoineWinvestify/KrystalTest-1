<?php
/**
//  Copyright (C) 2018, https://www.winvestify.com                         
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
* @author Antoine de Poorter
* @version 0.1
* @date 2018-06-08
* @package
 */

class UserFixture extends CakeTestFixture {

        public $useDbConfig = 'test';
        public $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'investor_id' => array('type' => 'integer'),
            'role_id' => array('type' => 'integer'),
            'username' => array('type' => 'string', 'length' => 45),
            'password' => array('type' => 'string', 'length' => 255),
            'email' => array('type' => 'string', 'length' => 255),
            'active' => array('type' => 'tinyinteger'),                 
            'created' =>'datetime',
            'modified' => 'datetime',
        );


 }
 
