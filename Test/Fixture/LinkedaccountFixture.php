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

class LinkedaccountFixture extends CakeTestFixture {

        public $useDbConfig = 'test';
        public $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'company_id' => array('type' => 'integer'),
            'accountowner_id' => array('type' => 'integer'),
            'linkedaccount_lastAccessed' => 'datetime',
            'linkedaccount_linkingProcess' => array('type' => 'integer'),
            'linkedaccount_status' => array('type' => 'tinyinteger'),
            'linkedaccount_statusExtended' => array('type' => 'tinyinteger'),
            'linkedaccount_statusExtendedOld' => array('type' => 'tinyinteger'),
            'linkedaccount_alias' => array('type' => 'string','length' => 45),
            'linkedaccount_accountIdentity' => array('type' => 'string', 'length' => 100),
            'linkedaccount_accountDisplayName' => array('type' => 'string','length' => 100),
            'linkedaccount_isControlledBy' =>  array('type' => 'tinyinteger'),                   
            'created' =>'datetime',
            'modified' => 'datetime',
        );
        
        public $records = array(
          array(
            'id' => 1,
            'company_id' => 10,
            'accountowner_id' => 5,
            'linkedaccount_lastAccessed' => '2007-03-18 10:41:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 1,                                        // ACTIVE
            'linkedaccount_statusExtended' => 2,                                // CREDENTIALS_VERIFIED
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "Business Account1",
            'linkedaccount_accountIdentity' => "776D01DC-4987-EF17-6FF2-70C312CDAA51",
            'linkedaccount_accountDisplayName' => "MLI LTD",
            'linkedaccount_isControlledBy' => 1,                                // USER_CONTROLLED
            'created' => '2007-03-18 10:39:23',
            'modified' => '2007-03-18 10:41:31'
          ),
          array(
            'id' => 2,
            'company_id' => 10,             
            'accountowner_id' => 5,
            'linkedaccount_lastAccessed' => '2007-03-18 10:41:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 1,                                        // ACTIVE
            'linkedaccount_statusExtended' => 2,                                // CREDENTIALS_VERIFIED
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "myPrivateAccount",
            'linkedaccount_accountIdentity' => "93335761-4057-8BB1-FADE-EF0ED8A973CD",
            'linkedaccount_accountDisplayName' => "Klaus Kukkovetz",
            'linkedaccount_isControlledBy' => 1,                                // USER_CONTROLLED
            'created' => '2007-03-18 10:41:23',
            'modified' => '2007-03-18 10:43:31'
          ),
           array(
            'id' => 3,
            'company_id' => 19,              
            'accountowner_id' =>25,
            'linkedaccount_lastAccessed' => '2017-12-28 10:43:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 1,                                        // ACTIVE
            'linkedaccount_statusExtended' => 2,                                // CREDENTIALS_VERIFIED
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "DUMMY_ALIAS",
            'linkedaccount_accountIdentity' => "DUMMY_IDENTITY_1",
            'linkedaccount_accountDisplayName' => "DUMMY_DISPLAYNAME_1",
            'linkedaccount_isControlledBy' => 2,                                // SYSTEM_CONTROLLED
            'created' => '2007-03-18 10:43:23',
            'modified' => '2007-03-18 10:45:31'
          ) ,          
          array(
            'id' => 4,
            'company_id' => 3,              
            'accountowner_id' => 41,
            'linkedaccount_lastAccessed' => '2017-07-28 10:43:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 1,                                        // ACTIVE
            'linkedaccount_statusExtended' => 2,                                // CREDENTIALS_VERIFIED
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "DUMMY_ALIAS",
            'linkedaccount_accountIdentity' => "DUMMY_IDENTITY_2",
            'linkedaccount_accountDisplayName' => "DUMMY_DISPLAYNAME_2",
            'linkedaccount_isControlledBy' => 2,                                // SYSTEM_CONTROLLED
            'created' => '2007-03-18 10:43:23',
            'modified' => '2007-03-18 10:45:31'
          ),            
          array(
            'id' => 5,
            'company_id' => 4,             
            'accountowner_id' => k,
            'linkedaccount_lastAccessed' => '2007-07-18 10:43:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 1,                                        // ACTIVE
            'linkedaccount_statusExtended' => 2,                                // CREDENTIALS_VERIFIED
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "DUMMY_ALIAS",
            'linkedaccount_accountIdentity' => "DUMMY_IDENTITY_3",
            'linkedaccount_accountDisplayName' => "DUMMY_DISPLAYNAME_3",
            'linkedaccount_isControlledBy' => 2,                                // SYSTEM_CONTROLLED 
            'created' => '2007-03-18 10:43:23',
            'modified' => '2007-03-18 10:45:31'
          ),
          array(
            'id' => 6,
            'company_id' => 1,             
            'accountowner_id' => 26,
            'linkedaccount_lastAccessed' => '2017-03-28 10:43:23',
            'linkedaccount_linkingProcess' => 0,
            'linkedaccount_status' => 2,                                        // NOT ACTIVE
            'linkedaccount_statusExtended' => 12,                               // DELETED BY USER
            'linkedaccount_statusExtendedOld' => 0,
            'linkedaccount_alias' => "DUMMY",
            'linkedaccount_accountIdentity' => "DUMMY_IDENIITY_4",
            'linkedaccount_accountDisplayName' => "DUMMY_DISPLAYNAME_4",
            'linkedaccount_isControlledBy' => 2,                                // SYSTEM_CONTROLLED
            'created' => '2007-03-18 10:43:23',
            'modified' => '2007-03-18 10:45:31',
          )               
    );
 }
 
