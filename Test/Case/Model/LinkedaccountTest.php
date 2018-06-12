<?php
/* 
 * Copyright (C) 2018 http://www.winvestify.com
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
 */
/*
This class tests all the methods of the Model Linkedaccount
 * 
2018-05-08	  version 2018_0.1      
First version 
 * 
 *                  
 * 
Pending:

*/

App::uses('Linkedaccount', 'Model');
class LinkedaccountTest extends CakeTestCase {
    public $fixtures = array('app.Accountowner', 'app.Linkedaccount');
    
    
    var $companyId; 
    var $investorId; 
    var $username;
    var $password;
    
    
    public function setUp() {
        parent::setUp();
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
    }
    
    
   

    
/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    } 
 */   
    
    
    
    
    

    public function testDeleteLinkedaccount($filterConditions, $originator = WIN_USER_INITIATED) {

    }

 
    public function testGetLinkedaccountDataListxx($filterConditions) {

    }


    public function testGetLinkedaccountIdListxx($filterConditions) {

    }


    public function testCreateNewLinkedAccount() {
        
        $result = $this->Linkedaccount->createNewLinkedAccount($this->companyId, $this->investorId, $this->username, $this->password);
        
        $expected = array(
            array('Article' => array('id' => 1, 'title' => 'First Article')),
            array('Article' => array('id' => 2, 'title' => 'Second Article')),
            array('Article' => array('id' => 3, 'title' => 'Third Article'))
        );

        $this->assertEquals($expected, $result);
    }
    

    public function testGetLinkAccountsWithNothingInProcess($queueUserReference) {

    }
    

    public function testChangePasswordLinkaccount($linkedAccountId, $newPass){

    }


    public function testDisableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

    }   
    
    
    public function testEnableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

    }     
  

  
    
     
    
    
    
    
    
    
}