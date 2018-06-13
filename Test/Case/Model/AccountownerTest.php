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

    protected $exampleVar; 
    
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
    
    

    public function testDeleteAccountOwner() {
        $expected = true;
        $accountOwnerId = $this->Accountowner->createAccountOwner($companyId = 25, 
                                                       $investorId = 25, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");        
        
        $result = $this->Accountowner->deleteAccountOwner($accountOwnerId);
              
        $this->assertEquals($expected, $result);  
    }
    
    
    
    public function testCreateAccountOwner() {
        $expected = 42;                                                         // Create a new accountowner
        $result = $this->Accountowner->createAccountOwner($companyId = 20, 
                                                       $investorId = 25, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected, $result);      

        
        $expected1 = 5;                                                         // Return existing one
        $result1 = $this->Accountowner->createAccountOwner($companyId = 10, 
                                                        $investorId = 63, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected1, $result1);         
        
    }
    
       
    public function testNewLinkedAccountAddedToExistingAccountOwner() {
        // Add new account to an already defined PFP
        $result = $this->Accountowner->createAccountOwner($companyId = 10, 
                                                $investorId = 63, 
                                                $username = __FILE__ . "inigo.iturburua@gmail.com", 
                                                $password = __FILE__ . "8870mit");
        $accountownerId = $result;
        $expected = 5;         
        $this->assertEquals($expected, $result);        
    
        $expected = true;
        $result1 = $this->Accountowner->accountAdded($accountownerId);
        $this->assertEquals($expected, $result1);      
    }
        
    public function testNewLinkedAccountAddedToNewAccountOwner() {      
        // add a new account to a new accountowner
        $result10 = $this->Accountowner->createAccountOwner($companyId = 11, 
                                                $investorId = 63, 
                                                $username = __FILE__ . "inigo.iturburua@gmail.com", 
                                                $password = __FILE__ . "8870mit");
        $accountownerId10 = $result10;      
        $expected10 = 42;  
        $this->assertEquals($expected10, $result10);         
        // Return existing one
        $expected11 = true;
        $result11 = $this->Accountowner->accountAdded($accountownerId10);
        $this->assertEquals($expected11, $result11);         
        
    }
    
    
    public function testAccountDeleted()  {
        // add a new account to a new accountowner
        $accountOwnerId = $this->Accountowner->createAccountOwner($companyId = 11, 
                                                $investorId = 63, 
                                                $username = __FILE__ . "inigo.iturburua@gmail.com", 
                                                $password = __FILE__ . "8870mit");                                              
        $result11 = $this->Accountowner->accountAdded($accountOwnerId);
        $result12 = $this->Accountowner->accountAdded($accountOwnerId);  
        $result13 = $this->Accountowner->accountAdded($accountOwnerId); 


        $expected = true;
        $result = $this->Accountowner->accountDeleted($accountOwnerId );    
        $this->assertEquals($expected, $result);   
    }     
    
 
    public function testChangeAccountPassword(){
        $newPass = "newPassword";   
        $expected = true;

        $accountOwnerId = $this->Accountowner->createAccountOwner($companyId = 10, 
                                                $investorId = 63, 
                                                $username = __FILE__ . "inigo.iturburua@gmail.com", 
                                                $password = __FILE__ . "8870mit");                                              

        $result = $this->Accountowner->changeAccountPassword($accountOwnerId, $newPass);
        $this->assertEquals($expected, $result);          
    }

    
}