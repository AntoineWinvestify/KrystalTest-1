<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-04-07
 * @package
 *

  2017-04-07	  version 2017_0.1
 function to copy a userdata photo to admin user.				[OK]

 */


class TestsController extends AppController {

    var $name = 'Tests';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Test', "Data", "Investor");
    var $error;

    
function beforeFilter() {
        parent::beforeFilter();

	$this->Security->requireAuth();
        
 	$this->Auth->allow('linkCheckRecords');        
        
}

   

    
    
/**
 * syntax;  {DOMAIN}/tests/showUserData/2898786785624/-1
 * 
 * 	Show the user's data in a dashboard of Admin user
 * 
 *  @param 	string	$userIdentity   The unique identification of the user
 *  @param      $integer "photonumber. 0 = most recent photo, -1 one earlier, -2 two earlier etc.
 *  @return 	boolean true:  photo has been copied to current user's dashboard
 *  Another way is to "impersonate the user" copy his "auth profile and access his dashboard page. Advantage: no screwup 
 *  potential statistics functions
 */
function showUserData($userIdentity, $number) {

    $this->autoRender = false;

    $this->layout = 'ajax';
    $this->disableCache();
/*
 * THIS DOES NOT WORK
    $userIdentity = "41d0934670r943aed954932f";
    
    $investorFilterConditions = array('Investor.investor_identity' => $userIdentity);  
    $investorResults = $this->Investor->find("first", array('conditions'  => $investorFilterConditions,
                                                             'recursive' => 0,
                                    ));   
    unset($investorResults['User']['password']);
    $temp = array();
    $temp['User'] = $investorResults['User'];
    $temp['User']['Investor'] = $investorResults['Investor'];

    $this->Session->write('AuthOriginal', $this->Session->read('Auth'));
    $this->Session->write('Auth', $temp);   
    $this->print_r2($this->Session->read());
    exit;
*/    
    
    
    
    
    
    
    $investorIdentity = $this->Auth->user('Investor.investor_identity');
    $dataFilterConditions = array('data_investorReference' => $userIdentity);

    $dataResults = $this->Data->find("all", array('conditions'  => $dataFilterConditions,
                                                    'order'     => array('Data.id DESC'),
                                                    'recursive' => -1,
                                    ));

    $absNumber = abs($number);

    if (array_key_exists($absNumber,$dataResults)) {
        $data = array('data_investorReference' => $investorIdentity,
                   'data_JSONdata' => $dataResults[$absNumber]['Data']['data_JSONdata']
                );
        $this->Data->save($data, $validate = true);  
        echo "Data is now available in Dashboard";
    }
    else {
        echo "Nothing found, try again with other data";
        exit;
    }
}

    




function linkCheckRecords() {
    $this->autoRender = false;
    Configure::write('debug', 2);  
    
    $this->User = ClassRegistry::init('User'); 
  $dataFilterConditions = array("investor_id >"  => 0);
    
    $userResults = $this->User->find("all", array('conditions'  => $dataFilterConditions,
                                                    'fields' => array('id', 'email', 'investor_id' , 'username'),
                                                    'recursive' => -1,
                                    ));
     
$this->print_r2($userResults);    
     foreach ($userResults as $key => $result) {
         
         $this->print_r2($result);
         echo "key = $key, id = " . $result['User']['id'] . " investor_id = " .  $result['User']['investor_id'] . " and username = " .  $result['User']['username'] . "<br>";
         $this->createCheckdataTable($result['User']['investor_id']);
         echo "table create for investor_Id = " .  $result['User']['investor_id'] . "<br>"; 
     }
    
    
    

     
     
}



   /**
     * Create a check line in the checks table for the user
     * @param type $id  id of related User table
     * @return boolean
     */
    public function createCheckdataTable($id) {
        //Checks data
        
    $this->Check = ClassRegistry::init('Check'); 
    $this->Check->create();
        $checksArray = Array(
            'investor_id' => $id,
            'check_name' => 0,
            'check_surname' => 0,
            'check_dni' => 0,
            'check_dateOfBirth' => 0,
            'check_email' => 1,                 // Cannot be changed directly by user
            'check_telephone' => 1,             // Cannot be changed directly by user
            'check_postCode' => 0,
            'check_address' => 0,
            'check_city' => 0,
            'check_country' => 0,
            'check_iban' => 0,
            'check_ibanTime' => 0,
            'check_cif' => 0,
            'check_businessName' => 0,
        );

        if ($this->Check->save($checksArray)) {
            return true;
        } else {
            return false;
        }
    }






    
}
