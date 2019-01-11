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

class CheckFixture extends CakeTestFixture {

        public $useDbConfig = 'test';

        public $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'investor_id' => array('type' => 'integer'),
            'check_name' => array('type' => 'tinyinteger'),
            'check_nameTime' => 'datetime',
            'check_surname' => array('type' => 'tinyinteger'),
            'check_surnameTime' => 'datetime', 
            'check_DNI'=> array('type' => 'tinyinteger'),
            'check_DNITime' => 'datetime',
            'check_dateOfBirth' => array('type' => 'tinyinteger'),
            'check_dateOfBirthTime' => 'datetime', 
            'check_email'  => array('type' => 'tinyinteger'),
            'check_emailTime' => 'datetime',
            'check_telephone'=> array('type' => 'tinyinteger'),
            'check_telephoneTime' => 'datetime',
            'check_postCode'=> array('type' => 'tinyinteger'),
            'check_postCodeTime' => 'datetime',
            'check_address1' => array('type' => 'tinyinteger'),
            'check_address1Time'  => 'datetime',
            'check_city' => array('type' => 'tinyinteger'),
            'check_cityTime'  => 'datetime',
            'check_country' => array('type' => 'tinyinteger'),
            'check_countryTime' => 'datetime',
            'check_iban' => array('type' => 'tinyinteger'),
            'check_ibanTime' => 'datetime', 
            'check_cif' => array('type' => 'tinyinteger'),
            'check_cifTime' => 'datetime',
            'check_businessName'=> array('type' => 'tinyinteger'),
            'check_businessNameTime'  => 'datetime',
            'check_address2' => array('type' => 'tinyinteger'),
            'check_address2Time' => 'datetime',
            'check_identity' => array('type' => 'tinyinteger'),
            'check_identityTime' => 'datetime',
            'check_investor_photoChatGUID' => array('type' => 'string', 'length' => 45),
            'check_investor_photoChatGUIDTime' => 'datetime',
            'created'  => 'datetime',
            'modified' => 'datetime',
          ); 

    }        