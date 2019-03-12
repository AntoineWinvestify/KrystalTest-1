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
            'length_rule' => ['rule' => ['lengthBetween', 2, 50],
                'allowEmpty' => false,
                'required'   => true,
                'message' => 'You forgot to add your name'
            ],
        ],
        'email_senderSurName' => [
            'length_rule' => ['rule' => ['lengthBetween', 2, 50],
                'allowEmpty' => true,
                'required'   => false,
                'message' => 'You forgot to add your surname'
            ],
        ],        
        'email_senderSubject' => [
            'checkEmailSubject' => [
                'rule' => 'checkEmailSubject',
                'required'   => true,
                'message' => 'Please selected one of the possible subjects',
            ]            
        ],
        'email_senderTelephone' => [
            'length_rule' => ['rule' => ['lengthBetween', 7, 20],
                'allowEmpty' => false,
                'required'   => true,
                'message' => 'The telephone number is incomplete'
            ],
            'checkTelephoneNumber' => [
                'rule' => 'checkTelephoneNumber',
                'allowEmpty' => false,
                'required'   => true,                
                'message' => 'The telephone number can only contain numbers and the + sign',
            ]
        ],
        'email_senderCompany' => [        
            'length_rule' => ['rule' => ['lengthBetween', 50],
                'allowEmpty' => true,
                'message' => 'You should add the name of your company'
            ],
        ],
        'email_senderText' => [ 
            'length_rule' => ['rule' => ['minLength', 5, 2000],
                'allowEmpty' => false,
                'required'   => true,
                'message' => 'Please add your message'
            ],         
        ],
        'email_senderJobTitle' => [ 
            'length_rule' => ['rule' =>  ['lengthBetween', 3, 50],
                'allowEmpty' => true,
                'message' => 'You should add your job title'
            ],
        ],      
        'email_senderEmail' => [
            'complex_rule' => ['rule' => '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
                'allowEmpty' => false,
                'required'   => true,
                'message' => 'Email validation error'
            ],
        ]
    ];   
    
 
    var $defaultFields = [ 
        'investor' => [''],                           // means N/A                     
        
        'winAdmin' => ['id', 
                        'email_senderName',
                        'email_senderSurname',            
                        'email_senderEmail', 
                        'email_senderCompany',
                        'email_senderTelephone',
                        'email_senderJobTitle',            
                        'email_senderSubject',             
                        'email_senderText', 
                        'email_links',
                        'modified',
                        'created'            
                      ],
        
        'superAdmin' => ['id', 
                        'email_senderName',
                        'email_senderSurname',            
                        'email_senderEmail', 
                        'email_senderCompany',
                        'email_senderTelephone',
                        'email_senderJobTitle',            
                        'email_senderSubject',             
                        'email_senderText', 
                        'email_links',
                        'modified',
                        'created'
                      ],               
    ];    
    
    
    /**
     * Checks if a telephone number contains only numbers and the "+" sign
     * 
     * @param array $check An array with the data to be checked 
     * @return boolean
     */  
    public function checkTelephoneNumber($check) {                               

        $tempKey = array_keys($check);
        $key = $tempKey[0];                                                     // contains index name

        $telephoneNumber = $check[$key];

        if (preg_match("/\+\d[0-9]/", $telephoneNumber)) {  
            return true;
        }  
        return false;
    }   

    /**
     * Checks if the 'subject' of the email is one of the predefined ones according to the
     * configuration file 'p2pGestor.php'. the subject should be 'general', 'improvement' etc.etc.
     * 
     * @param array $check An array with the data to be checked. 
     * @return boolean
     */    
    public function checkEmailSubject($check) { 

        $tempKey = array_keys($check);
        $subject = $check[$tempKey[0]];                                         // contains index name
  
        $allowedEmailSubjects = Configure::read('subjectContactForm');
        array_shift($allowedEmailSubjects);
        $allowedEmailSubjectsKeys = array_keys($allowedEmailSubjects);
        
        if (in_array($subject, $allowedEmailSubjectsKeys)) { 
            return true;
        }
        return false;
    }    
    
    
    
    /**
     * Save the data which a user has provided via any type of ContactForm
     * 
     * @param array $emailData The data that forms the basis for an email
     * @return boolean
     */
    public function api_addEmail($emailData) {

        if ($this->save($emailData, $validate = true)) {
            return true;
        }
        return false;   
    }

    
    
    function afterSave($created, $options = array()) {

        if ($created) {
            $event = new CakeEvent("Model.Email.SendMessage", $this, 
                                    array('id' => $this->id, 
                                        'model' => 'Email', 
                                        'modelData' => $this->data[$this->alias], 
                                        'userIdentification' => 0,
                                        'isFinalEvent' => true)
                                  );
                    
            $this->getEventManager()->dispatch($event);
        }
        return true;
    }    
    
    
    
    
    
    
    
}