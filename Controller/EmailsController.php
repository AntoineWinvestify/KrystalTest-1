<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2019, http://winvestify.com                             |
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
*
 * This Controller represents a public endpoint "Email"
 * 
 * 
 * 
 * 
* @author Antoine de Poorter
* @version 0.1
* @date 2019-01-29
* @package
*

2019-01-29	  version 2016_0.1






*/
App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class EmailsController extends AppController
{
    var $name = 'Emails';
    var $uses = array('Email');
	
 
    function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow('v1_add', 'v1_index');       
            
    }
    
    /**
     * This method terminates the HTTP GET.
     * Format GET /api/1.0/emails.json&_fields=config
     * Example GET /api/1.0/emails.json&_fields=config
     * 
     * @return Response A list of configuration parameters
     */
    function v1_index()  {
        $this->checkAcl();

        if ($this->listOfFields <> ['config']) {
            $this->response->statusCode(404);           
        }
        else {
            $emailSubject = Configure::read('subjectContactForm');
            array_shift($emailSubject);        
            $data['emailconfig']['category'] = $emailSubject;

            $resultJson = json_encode($data);   
            $this->response->statusCode(200);
            $this->response->type('json');
            $this->response->body($resultJson); 
        }
        return $this->response;   	
    }

    
    /** 
     * This method terminates the HTTP POST
     * Format POST /api/1.0/emails.json
     * Example POST /api/1.0/emails.json
     * 
     * @return 
     */
    function v1_add() {
        $this->checkAcl();      
        $data = $this->request->data;
        $this->AppModel->apiVariableNameInAdapter($data);    
        $result = $this->Email->api_addEmail($data);
       
        if (empty($result)) {             
            $validationErrors = $this->Email->validationErrors;                 // Cannot retrieve all validation errors

            $this->Email->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('CANNOT_CREATE_EMAIL', 
                                                        "One or more mandatory fields are missing", 
                                                        $validationErrors);
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(400);                                    
        }
        else { // create the links
               
            $account['feedback_message_user'] = "Email successfully created. We'll get in touch with you as soon as possible";          
            $resultJson = json_encode($account);           
            $this->response->statusCode(201);
        }
        
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }

}
