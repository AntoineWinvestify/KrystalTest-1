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

class InvestorFixture extends CakeTestFixture {

    public $useDbConfig = 'test';
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'investor_identity' => array('type' => 'string', 'length' => 45),
        'investor_name' => array('type' => 'string', 'length' => 50),
        'investor_surname' => array('type' => 'string', 'length' => 50),
        'investor_dateOfBirth' => 'date',
        'investor_telephone' => array('type' => 'string', 'length' => 20),
        'investor_email' => array('type' => 'string', 'length' => 100),
        'investor_linkedAccounts' => array('type' => 'tinyinteger'),
        'created' => 'datetime',
        'modified' => 'datetime',
    );
    
    public $records = array(
      array(
        'id' => 25,
        'investor_identity' => "8918490A-A946-C54B-0184-B06BAE988A4B",
        'investor_name' => "Inigo",
        'investor_surname' => "Iturburua",
        'investor_dateOfBirth' => '1977-12-07',
        'investor_telephone' => "+34666555444",
        'investor_email' => "inigo@winvestify.com",
        'investor_linkedAccounts' => 3,
        'created' => '2007-03-18 10:41:23',
        'modified' => '2007-03-18 10:43:31'
      ),
      array(
        'id' => 63,
        'investor_identity' => "BCA58B8F-0802-F15D-95ED-3FF29AD80958",
        'investor_name' => "Daniel",
        'investor_surname' => "Velazquez",
        'investor_dateOfBirth' => '1997-03-07',
        'investor_telephone' => "+34666777444",
        'investor_email' => "daniel@winvestify.com",
        'created' => '2007-03-18 10:43:23',
        'modified' => '2007-03-18 10:45:31'
      )
    );
 }
