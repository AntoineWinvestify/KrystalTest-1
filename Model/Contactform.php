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
 * @date 2017-03-29
 * @package
 * 
  2017-03-29
  validation
  function createContactMessage()

  2017-03-30
  fixed createContactMessage()

  2017-04-05
  added select subject
 * 
 * 
 * 
 * 
 */

App::uses('CakeEvent', 'Event');

class Contactform extends AppModel {

    var $name = 'Contactform';

    /**
     * 
     * 	Validate the contact form
     * 
     */
    public $validate = array(
        'name' => array(
            'rule' => array('minLength', 3),
            'message' => 'Name too short.'
        ),
        'email' => array(
            'rule' => 'email',
            'message' => 'EMAIL provided is not a valid email address.'
        ),
        'subject' => array(
            'rule' => array('minLength', 3),
            'message' => 'Select a subject.'
        ),
        'text' => array(
            'rule' => array('minLength', 10),
            'message' => 'Message too short.'
        ),
    );

    /**
     *
     * 	Create the message in the data base and the event for send the mail.
     * 	
     * 	@param 		string	$name
     * 	@param 		string	$email
     * 	@param		string	$text
     *  @param          string  $subject
     *
     * 	@return 	array	
     * 							
     */
public function createContactMessage($userName, $email, $subjectval ,$subjecttext ,$text) {
        $data = array(
            'name' => $userName,
            'email' => $email,
            'subject' => $subjectval,
            'text' => $text,
            'subjecttext'=> $subjecttext,
        );
        $this->data = $data;
        $this->set($data);
        if ($this->validates()) {   // OK
            $this->save($data);
        } else {                     // validation false
            $result[0] = 0;
            $errors = array('errors' => 'Form error', $this->validationErrors);
            $result[1] = $errors;
            return $result;
        }

        /* $event = new CakeEvent("sendContactMessage", $this, array('name' => $userName, 'email' => $email, 'subject' => $subject, 'text' => $text)); //Create the event to send the mail
          $this->getEventManager()->dispatch($event); */
        $result[0] = 1;
        //Insert OK
        return $result;
    }

    function afterSave($created, $options = array()) {

        if ($created) {
            $event = new CakeEvent("sendContactMessage", $this, array('name' => $this->data['name'], 'email' => $this->data['email'], 'subject' => $this->data['subjecttext'], 'text' => $this->data['text']));
            $this->getEventManager()->dispatch($event);
        }
        return true;
    }

}
