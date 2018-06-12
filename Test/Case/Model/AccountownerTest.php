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
This class tests all the methods of the Model Accountowner
 * 
2018-05-08	  version 2018_0.1      
First version 
 * 
 *                  
 * 
Pending:

*/

App::uses('Accountowner', 'Model');
class AccountownerTest extends CakeTestCase {

    public $fixtures = array('app.Accountowner', 'app.Linkedaccount', 'app.Investor');

    public function setUp() {
        parent::setUp();
        $this->Accountowner = ClassRegistry::init('Accountowner');
    }

    

    
 
/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }    
*/  
    
    

    public function testDeleteAccountOwner($accountOwnerId) {

    }
    
    
    public function testCreateAccountOwner() {
        $expected = 42;                                                          // Create a new accountowner
        $result = $this->Accountowner->CreateAccountOwner($companyId = 20, 
                                                        $investorId = 25, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected, $result);      
        
        $expected1 = 5;                                                         // Return existing one
        $result1 = $this->Accountowner->CreateAccountOwner($companyId = 10, 
                                                        $investorId = 63, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected1, $result1);         
        
    }
    
       
    public function testAccountAdded ($accountownerId) {


    }
    
    
    public function testAccountDeleted($accountownerId) {
 
 
    }     
    
 
    public function testChangeAccountPassword($accountownerId, $newPass){

    }



    
}