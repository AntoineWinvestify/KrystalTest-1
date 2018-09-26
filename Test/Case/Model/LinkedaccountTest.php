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
    public $fixtures = array('app.Accountowner', 'app.Linkedaccount', 'app.Investor');
    
    
    public function setUp() {
        parent::setUp();
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $this->username = "antoine@winvestify";
        $this->password = "8870mit";       
        
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
        // A linked account on a new PFP
        $expected = true;
        $companyId = 7;
        $investorId = 63;
        $result = $this->Linkedaccount->createNewLinkedAccount($companyId, $investorId, 
                                                    __LINE__ . $this->username, __LINE__ . $this->password);
        $this->assertEquals($expected, $result);

        
         // A new linked account on a PFP where the user already has at least one linked account       
        $expected1 = true;
        $companyId = 10;
        $investorId = 63;        
        $result1 = $this->Linkedaccount->createNewLinkedAccount($companyId, $investorId, 
                                                    __LINE__ . $this->username, __LINE__ . $this->password);        
        
        $this->assertEquals($expected1, $result1);        
        
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