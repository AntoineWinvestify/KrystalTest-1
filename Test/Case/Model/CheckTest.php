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
//App::uses('Controller', 'Controller');
App::uses('Check', 'Model');
//App::uses('AuthComponent', 'Controller/Component');
//App::uses('ComponentCollection', 'Controller');

class CheckTest extends CakeTestCase {
    public $Auth;
 //   public $Controller = null;
    public $fixtures = array('app.Check');
    public $investorId = 204;
 
    
    public function setUp() {
        parent::setUp();
        $this->Check = ClassRegistry::init('Check');
   //     $Collection = new ComponentCollection();
   //     $Controller = new Controller();
 //       $this->Auth = new AuthComponent($Collection);
 //       $this->Auth->initialize($Controller);
    }

/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }        
 */
    
 public function startup(Controller $controller) {

        }
        
        
        
    /**
     * add new object
     * check the fields if they are readonly
     * check both for format 'check_name' and 'name' and mixed
     * 
     * 
     *//*
    public function testApi_addCheck() {

        $data0 = [];
        
        $data1 = ['investor_telephone', 'investor_email', 
                'investor_name', 'investor_surname', 'investor_dateOfBirth'
                ];
        $data2 = ['telephone', 'email', 
                'name', 'surname', 'dateOfBirth'
                ];
        $data3 = ['telephone', 'investor_email', 
                'investor_name', 'surname', 'investor_dateOfBirth'
                ];        
        
        $message1 = "Cannot create a new Check object";        
        $message2 = "Regular format of data for Saving to DB Model 'Investor' NOT approved";         
        
        
        $result = $this->Check->api_addCheck($this->investorId, $data1);  
        $this->assertEquals($result, true, $message1); 

      
        $infoCheck = $this->Check->find("first", array('conditions' => array('id' => $result),
                                                     'recursive' => -1));  
        $this->assertEquals($infoCheck['Check']['check_telephone'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_email'], true, $message2);        
        $this->assertEquals($infoCheck['Check']['check_name'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_surname'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_dateOfBirth'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_postCode'], false, $message2);     
        
      
        $result = $this->Check->api_addCheck($this->investorId+10, $data2);  
        $this->assertEquals($result, true, $message1); 

        $message2 = "Regular format of data for Saving to DB Model 'Investor' NOT approved";       
        $infoCheck = $this->Check->find("first", array('conditions' => array('id' => $result),
                                                     'recursive' => -1));  
        $this->assertEquals($infoCheck['Check']['check_telephone'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_email'], true, $message2);        
        $this->assertEquals($infoCheck['Check']['check_name'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_surname'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_dateOfBirth'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_postCode'], false, $message2);        
        
    
        $result = $this->Check->api_addCheck($this->investorId+20, $data3);  
        $this->assertEquals($result, true, $message1); 

        $message2 = "Regular format of data for Saving to DB Model 'Investor' NOT approved";       
        $infoCheck = $this->Check->find("first", array('conditions' => array('id' => $result),
                                                     'recursive' => -1));  
        $this->assertEquals($infoCheck['Check']['check_telephone'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_email'], true, $message2);        
        $this->assertEquals($infoCheck['Check']['check_name'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_surname'], true, $message2);  
        $this->assertEquals($infoCheck['Check']['check_dateOfBirth'], true, $message2);
        $this->assertEquals($infoCheck['Check']['check_postCode'], false, $message2);         
  
        
        $result = $this->Check->api_addCheck($this->investorId+30, $data0); 
        $this->assertEquals($result, false, $message1);         

    }
   */
    
    /**
     * change lock for one or more fields to R/W, read it value
     * change lock for one or more fields to R/O and read its value
     * for both formats of field, and mixed
     * 
     */    
    public function testApi_editCheck() {
        $fieldsToChange0 = [];
        $fieldsToChange1 = ['check_name' => WIN_READONLY,
                            'check_postCode' => WIN_READONLY,
                            'check_city' => WIN_READONLY
                          ];
        $fieldsToChange2 = ['name' => WIN_READWRITE,
                            'postCode' => WIN_READWRITE,
                            'city' => WIN_READWRITE
                          ];
        $fieldsToChange3 = ['check_name' => WIN_READONLY,
                            'postCode' => WIN_READONLY,
                            'check_city' => WIN_READONLY
                          ];        
        $message1 = "Cannot make the changes to Check object";
        $result = $this->Check->api_addCheck($this->investorId+1, ['investor_telephone', 'investor_email']);  
        $result = $this->Check->api_editCheck($this->investorId+1, $fieldsToChange1);        
        $this->assertEquals($result, true, $message1);  

        $infoCheck = $this->Check->find("first", array('conditions' => array('investor_id' => $this->investorId+1),
                                                     'recursive' => -1)); 
   
        $message2 = "Regular format of data for Saving to DB Model 'Check' NOT approved";       
        $this->assertEquals($infoCheck['Check']['check_name'], true, $message2);        
        $this->assertEquals($infoCheck['Check']['check_postCode'], true, $message2);   
        $this->assertEquals($infoCheck['Check']['check_city'], true, $message2);       
        
        
        $result = $this->Check->api_addCheck($this->investorId+2, ['telephone', 'investor_email']);        
        $result = $this->Check->api_editCheck($this->investorId+2, $fieldsToChange2);
        $this->assertEquals($result, true, $message1); 
        $infoCheck = $this->Check->find("first", array('conditions' => array('investor_id' => $this->investorId+2),
                                                     'recursive' => -1));        
        $this->assertEquals($infoCheck['Check']['check_name'], 0 ,$message2."33");      
        $this->assertEquals($infoCheck['Check']['check_postCode'], 0, $message2."44");   
        $this->assertEquals($infoCheck['Check']['check_city'], 0, $message2."55");   
        

        $result = $this->Check->api_addCheck($this->investorId+3, ['investor_telephone', 'investor_email']);       
        $result = $this->Check->api_editCheck($this->investorId+3, $fieldsToChange3);
        $this->assertEquals($result, true, $message1); 
        $infoCheck = $this->Check->find("first", array('conditions' => array('investor_id' => $this->investorId+3),
                                                     'recursive' => -1));       
        $this->assertEquals($infoCheck['Check']['check_name'], true, $message2);        
         $this->assertEquals($infoCheck['Check']['check_postCode'], true, $message2);   
        $this->assertEquals($infoCheck['Check']['check_city'], true, $message2);       
         
        
        $result = $this->Check->api_addCheck($this->investorId+4, ['investor_telephone', 'investor_email']);  
        $result = $this->Check->api_editCheck($this->investorId+4, $fieldsToChange0); 
        $this->assertEquals($result, true, $message1);     


        
        $fieldsToChange10 = ['check_name' => WIN_READONLY,
                            'check_surname' => WIN_READONLY,
                            'check_city' => WIN_READONLY
                          ];       
        $message5 = "Check object does not exist";  
        $result5 = $this->Check->api_editCheck($this->investorId+13, $fieldsToChange10); 
        $this->assertEquals($result5, false, $message5);        
        
    } 
        
    
    
    
    


    
}
