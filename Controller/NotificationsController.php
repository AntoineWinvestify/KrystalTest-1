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
* @date 2017-01-16
* @package
*

2017-01-16	  version 2017_0.1





Pending:
implement more counters



*/
App::uses('CakeTime', 'Utility');
class NotificationsController extends AppController
{
	var $name = 'Notifications';
//	var $uses = array('Notification');
	

	
	
function beforeFilter() {
	parent::beforeFilter();
	
}





/**
*
*	Gets list of all (not yet read) notifications of the user to be displayed on webpage
*	
*//*
function getNotificationsList()  {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	$investorId = $this->Auth->user('Notification.id');
	$filterConditions = array('notification_status' => READY_FOR_VISUALIZATION,
							  'investor_id' => $investorId,
							 );

	$resultNotifications = $this->Notification->getList($filterConditions);
	$this->set('resultNotifications', $resultNotifications);
	$this->set('error', $error);
}
*/




/**
*
*	Gets the contents of a notification as defined by the $filterConditions
*	
*//*
function readNotificationContent() {
	if (!$this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	$investorId = $this->Auth->user('Notification.id');
	$filterConditions = array('id' => $_REQUEST['id'],
							  'investor_id' => $investorId);

	$notificationResult = $this->Notification->readNotificationContents($filterConditions);

	if (!empty($notificationResult)) {
		$this->set('notificationResult', $notificationResult);
	}
	else {
		$error = true;
	}
}
*/



    /** NOT FINISHED
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/notifications.json&_fields=x,y,z
     * Example GET /api/1.0/notifications.json&notification_country=SPAIN&_fields=notification_name,notification_surname
     * 
     * @param -
     */
    public function v1_index() {
        $this->checkAcl();
        // search criteria are adjust to the acl-role. 

        $results = $this->Pollingresource->find("all", $params = ['conditions' => $this->listOfQueryParams,
                                                  'fields' => $this->listOfFields,
                                                  'recursive' => -1]);
        
        foreach ($apiResult['data'] as $key => $index) {            // DOES THIS  WORK??
            $this->Notification->apiVariableNameOutAdapter($apiResult['data'][$key]);
        }        
 
        $resultJson = json_encode($apiResult);        
        $this->response->type('json');
        if (!empty($resultJson)) {
            $this->response->body($resultJson); 
        }
        return $this->response;
   
    }     
   
    /** NOT FINISHED
     * This methods terminates the HTTP GET.
     * Format GET api/v1/notifications/[notificationId]&_fields=x,y,z
     * Example GET api/v1/notifications/1.json&_fields=Notification_name,Notification_surname...
     * 
     * @param integer $id The database identifier of the requested 'Notification' resource
     * 
     */
    public function v1_view(){
        $this->checkAcl();   
     // preconditions are that the investor is the owner of the notificationId
        $id = $this->request->params['id'];

        $results = $this->Notification->findById($id, $fields = $this->listOfFields, $recursive = -1);

        $apiResult['data'] = Hash::extract($results, '{n}.Notification');

        foreach ($apiResult['data'] as $key => $index) {            // DOES THIS  WORK??
            $this->Notification->apiVariableNameOutAdapter($apiResult['data'][$key]);
        }        

        $resultJson = json_encode($apiResult);        
        $this->response->type('json');
        if (!empty($resultJson)) {
            $this->response->body($resultJson); 
        }
        return $this->response;         
    }     
 
 
    /** NOT YET TESTED
     * Simple version is OK, i.e. for deleting *manually* an Notification
     * This methods terminates the HTTP POST for deleting a new Notification.
     * Format DELETE /api/1.0/notifications/{NotificationId}.json
     * Example DELETE /api/1.0/notifications/23.json
     * 
     * @return boolean
     */
    public function v1_delete() { 
        $this->checkAcl();
        
        $id = $this->request->params['id'];
     // preconditions are that the investor is the owner of the notificationId if role == investor
     // other roles: no problem
/*
        if ($this->roleName <> "superAdmin") {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }  
 */       
        $result = $this->Notification->api_deleteNotification($id);
        
        if ($result == false) {
            $validationErrors = $this->Notification->validationErrors;              // Cannot retrieve all validation errors
            $this->Notification->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('CANNOT_DELETE_NOTIFICATION_OBJECT', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(400);                                    
        }
        else { 
            $this->response->statusCode(200);
            $return['feedback_message_user'] = "Notification has been deleted";
            $resultJson = json_encode($return);
        }
        
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }     
    
    

    
    /** NOT YET TESTED
     * This methods terminates the HTTP POST for confirming that a notification has been read.
     * Format POST /api/1.0/notifications/{NotificationId}/notification-read
     * Example POST /api/1.0/notifications/23/notification-read
     * 
     * @return boolean
     * @throws UnauthorizedException
     */
    public function v1_notificationRead() { 
       // preconditions are that the investor is the owner of the notificationId      
        $this->checkAcl();  
        
        $id = $this->request->params['id'];
        $results = $this->Notification->api_NotificationRead($id);
         if (empty($results)) {
            $formattedError = $this->createErrorFormat('CANNOT_CHANGE_NOTIFICATION_STATUS', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(400);                                    
        }
        else { 
            $this->response->statusCode(204);
        }    
        
        $this->response->type('json');
        if (!empty($resultJson)) {
            $this->response->body($resultJson); 
        }
        return $this->response; 

    }  
    
    /** NOT YET TESTED
     * This methods terminates the HTTP POST for reading the latest additions to
     * the Notifications queue of a user (= investor)
     * Format POST /api/1.0/notifications/getLatestModifications
     * Example POST /api/1.0/notifications/getLatestModifications
     * 
     * @return boolean
     */
    public function v1_getLatestModifications() {
       // preconditions are that the investor is the owner of the notificationId      
        $this->checkAcl();  
        
        $id = $this->request->params['id'];
        
        $results = $this->api_getLatestModifications($this->investorId, $id);

        if (empty($results)) {
            $formattedError = $this->createErrorFormat('CANNOT_READ_NOTIFICATION_DATAS', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(400);                                    
        }
        else { 
            $apiResult['data'] = Hash::extract($results, '{n}.Notification');
            foreach ($apiResult['data'] as $key => $index) {            // DOES THIS  WORK??
                $this->Notification->apiVariableNameOutAdapter($apiResult['data'][$key]);
            }
            $resultJson = json_encode($apiResult['data']);            
            $this->response->statusCode(200);
        }    
        
        $this->response->type('json');
        if (!empty($resultJson)) {
            $this->response->body($resultJson); 
        }
        return $this->response; 

    }     

}