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
  Function ContactFormSend()
  Function Form()
 
  2017-03-30
  function beforeFilter()
  Fixed  ContactFormSend()

  2017-04-05
  Added select subject
 
  2017-04-20
  Captcha
 *
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */


App::uses('CakeEvent', 'Event');

class ContactformsController extends AppController {

    var $name = 'Contactforms';
    var $uses = array('Contactform');
    public $components = array('MathCaptcha', array(
            'timer' => 3,      
            'tabsafe' => true,
        
    ));
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow('ContactFormSend', 'form', 'login');
        //allow these actions without logon
    }

    function form() {
        $this->set('captcha', $this->MathCaptcha->getCaptcha());
        $this->set('captcha_result', $this->MathCaptcha->getResult());
        $this->layout = 'winvestify_publicjs_layout';
        
    }

     function contactFormSend() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
                $this->layout = 'ajax';
                $this->disableCache();
                $capResult = $_REQUEST['result'];
                $captcha = $_REQUEST['captcha'];
                
            if ($capResult == $captcha) {
                $name = $_REQUEST['name'];
                $email = $_REQUEST['email'];
                $subjectval = $_REQUEST['subjectval'];
                $subjecttext = $_REQUEST['subjecttext'];
                $text = $_REQUEST['text'];
                $result = $this->Contactform->createContactMessage($name, $email, $subjectval,$subjecttext ,$text);
                $this->set('result', $result);
            } else {
                $result[0] = 2;
                $this->set('result', $result);
               
            }
        }
    }
}
