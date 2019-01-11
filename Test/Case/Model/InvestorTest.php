<?php
/* 
 * Copyright (C) 2019 http://www.winvestify.com
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
This class tests all the methods of the Model Investor

 
*/
App::uses('Controller', 'Controller');
App::uses('Investor', 'Model');
App::uses('Check', 'Model');
App::uses('User', 'Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller');

class InvestorTest extends CakeTestCase {
    public $Auth;
    public $Controller = null;
    public $fixtures = array('app.User', 'app.Check', 'app.Investor');

 
    public function setUp() {
        parent::setUp();
        $this->Investor = ClassRegistry::init('Investor');
        $this->User = ClassRegistry::init('User');
        $this->Check = ClassRegistry::init('Check');
        $Collection = new ComponentCollection();
        $Controller = new Controller();
        $this->Auth = new AuthComponent($Collection);
        $this->Auth->initialize($Controller);
    }

/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }        
 */
    
 public function startup(Controller $controller) {
//        parent::startup($controller);
 //       $this->Controller = $controller;
         
  //      $this->Auth = new AuthComponent(); 
   //     $this->Auth->initialize();       
        // Make sure the controller is using pagination
      // $this->Component->load('Auth)');

    //        $this->Controller->Component->load = array();
        }
    
  

    public function testApi_addInvestor() {

        $data = ['investor_telephone' => '+34666555444',
                'investor_password' => 'myPassword_12',
                'investor_email' => 'user' . mt_rand(0, 100000) . '@winvestify.com',
                'investor_name' => 'John',
                'investor_surname' => 'Doe',
                'investor_accredited' => true
                ];
        
        $result = $this->Investor->api_addInvestor($data, $validate = true);        
        
        $message1 = "Cannot create new investor";
        $this->assertGreaterThanOrEqual(1, $result, $message1);  
   
        $infoInvestor = $this->Investor->find("first", array('conditions' => array('id' => $result),
                                                     'recursive' => -1));  
        $message2 = "Regular format of data for Saving to DB Model 'Investor' NOT approved";
        $this->assertEquals($infoInvestor['Investor']['investor_surname'], 'Doe', $message2);
        $this->assertEquals($infoInvestor['Investor']['investor_name'], 'John', $message2);  
        $this->assertEquals($infoInvestor['Investor']['investor_telephone'], '+34666555444', $message2);
        $this->assertEquals($infoInvestor['Investor']['investor_accredited'], true, $message2); 
        
        
        $infoUser = $this->User->find("first", array('conditions' => array('investor_id' => $result),
                                                     'recursive' => -1));  
        $message3 = "Regular format of data for Saving to DB 'User' NOT approved";
        $this->assertEquals($infoUser['User']['username'], $data['investor_email'], $message3);        
 //       $this->assertEquals($infoUser['User']['password'], $data['investor_password'], $message3); 
        $this->assertEquals($infoUser['User']['email'], $data['investor_email'], $message3);         
        $this->assertEquals($infoUser['User']['role_id'], ROLE_INVESTOR, $message3);  
        $this->assertEquals($infoUser['User']['active'], ACTIVE, $message3);  
        
        $infoCheck = $this->Check->find("first", array('conditions' => array('investor_id' => $result),
                                                     'recursive' => -1));  
        $message4 = "Regular format of data for Saving to DB 'Check' NOT approved";
        $this->assertEquals($infoCheck['Check']['check_telephone'], WIN_READONLY, $message4);     
        $this->assertEquals($infoCheck['Check']['check_email'], WIN_READONLY, $message4);        
        $this->assertEquals($infoCheck['Check']['check_name'], false, $message4);        
    }
    
    /*
    
    public function testCheckOver18() {
        $expected = 42;                                                        
        $result = $this->CheckOver18($companyId = 20, 
                                                       $investorId = 25, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected, $result);      

        
        $expected1 = 5;                                                        
        $result1 = $this->CheckOver18($companyId = 10, 
                                                        $investorId = 63, 
                                                        $username = "inigo.iturburua@gmail.com", 
                                                        $password = "8870mit");
        $this->assertEquals($expected1, $result1);         
        
    }
    
       
    public function testwriteProtected() {
        // Add new account to an already defined PFP
        $result = $this->writeProtected($companyId = 10, 
                                                $investorId = 63, 
                                                $username = __FILE__ . "inigo.iturburua@gmail.com", 
                                                $password = __FILE__ . "8870mit");
        $accountownerId = $result;
        $expected = 5;         
        $this->assertEquals($expected, $result);        
    
        $expected = true;
        $result1 = $this->Investor->accountAdded($accountownerId);
        $this->assertEquals($expected, $result1);      
    }
        
    
    
    */
    


    
}
