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

class AccountownerFixture extends CakeTestFixture {

    public $useDbConfig = 'test';
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'company_id'=> array('type' => 'integer'),
        'investor_id' => array('type' => 'integer'),
        'accountowner_username' => array('type' => 'binary'),
        'accountowner_password' => array('type' => 'binary'),
        'accountowner_status' => array('type' => 'integer'),
        'accountowner_linkedAccountCounter'  => array('type' => 'integer'), 
        'created' => 'datetime',
        'modified' => 'datetime'
    );
    
      public $records = array(
          array(
        'id' => 41,
        'company_id'=> 3,
        'investor_id' => 63,
        'accountowner_username' => "antoine@winvestify.com",
        'accountowner_password' => "8870mit",
        'accountowner_status' => 2,                                             // ACTIVE
        'accountowner_linkedAccountCounter' => 1, 
        'created' => '2007-03-18 10:39:23',
        'modified' => '2007-03-18 10:41:31'
        ),
          array(
        'id' => 5,
        'company_id'=> 10,
        'investor_id' => 63,
        'accountowner_username' => "inigo@winvestify.com",
        'accountowner_password' => "8870mit",
        'accountowner_status' => 2,                                             // ACTIVE
        'accountowner_linkedAccountCounter' => 2, 
        'created' => '2007-03-18 10:41:23',
        'modified' => '2007-03-18 10:43:31'
        ),
          array(
        'id' => 7,
        'company_id'=> 19,
        'investor_id' => 25,
        'accountowner_username' => "daniel@winvestify.com",
        'accountowner_password' => "8870mit",
        'accountowner_status' => 2,                                             // ACTIVE
        'accountowner_linkedAccountCounter' => 1, 
        'created' => '2007-03-18 10:43:23',
        'modified' => '2007-03-18 10:45:31'
        )
    );
 }

