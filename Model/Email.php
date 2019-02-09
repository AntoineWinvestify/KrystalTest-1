<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2009, http://yoursite                                   |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//
* @author Antoine de Poorter
* @version 0.1
* @date 2019-02-08
* @package
*
 * 
 * 
 * Implements the  "Email" endpoint
 * 
 * 
*/

class Email extends AppModel
{
    var $name= 'Email';


    var $validate = [
        'email_senderName' => [
            'length_rule' => ['rule' => ['minLength', 2],
                'allowEmpty' => false,
                'message' => 'You forgot to add your name'
            ],
        ],
        'email_senderSurName' => [
            'length_rule' => ['rule' => ['minLength', 2],
                'allowEmpty' => false,
                'message' => 'You forgot to add your surname'
            ],
        ],        
        'email_senderSubject' => [
            'checkEmailSubject' => [
                'rule' => 'checkEmailSubject',
                'message' => 'Please selected one of the pssible subjects',
                'on' => 'update'
            ]            
        ],
        'email_senderTelephone' => [
            'length_rule' => ['rule' => ['minLength', 7],
            'allowEmpty' => false,
            'message' => 'The telephone number is incomplete'
            ],
        'checkTelephoneNumber' => [
            'rule' => 'checkTelephoneNumber',
            'message' => 'The telephone number cannot contain letters',
            'on' => 'update'
            ]
        ],
        'email_senderCompany' => [        
            'length_rule' => ['rule' => ['minLength', 3],
                'allowEmpty' => true,
                'message' => 'You should add the name of your company'
            ],
        ],
        'email_senderText' => [ 
            'length_rule' => ['rule' => ['minLength', 20],
                'allowEmpty' => false,
                'message' => 'Please add your text'
            ],         
        ],
        'email_sendJobTitle' => [ 
            'length_rule' => ['rule' =>  ['minLength', 3],
                'allowEmpty' => true,
                'message' => 'You should add your job title'
            ],
        ],      
        'email_senderEmail' => [
            'complex_rule' => ['rule' => '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
                'allowEmpty' => false,
                'message' => 'Email validation error'
            ],
        ]
    ];   
    
 
    /**
     * Checks if a telephone number contains only numbers and the "+" sign
     * 
     * @param array $check An array with the data to be checked 
     * @return boolean
     */  
    public function checkTelephoneNumber($check) {                               

        $tempKey = array_keys($check);
        $key = $tempKey[0];                                                     // contains current field

        if (preg_match("/\+\d[0-9]/", $key)) {   
            return true;
        }   
        return false;
    }   

    /**
     * Checks if the 'subject' of the email is one of the predefined ones according to the
     * configuration file 'p2pGestor.php'
     * 
     * @param array $check An array with the data to be checked. 
     * @return boolean
     */    
    public function checkEmailSubject($check) { 
        
        $tempKey = array_keys($check);
        $key = $tempKey[0];                                                     // contains current field
        
        $allowedEmailSubjects = Configure::read('subjectContactForm');
        array_shift($allowedEmailSubjects);
        if (in_array($key, $allowedEmailSubjects)) {
            return true;
        }
        return false;
    }    
    
    
    
    /**
     * Get the investor Identity by investor.id
     * 
     * @param array $emailData   The data that forms the basic for an email
     * @return boolean?
     */
    public function api_AddEmail($emailData) {
        // save file
        // if error return
      
        
        
        
    }

}