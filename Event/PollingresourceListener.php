<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2019, http://winvestify.com                             |
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

 * Used for listening to events
 * Based on article "http://martinbean.co.uk/blog/2013/11/22/getting-to-grips-with-cakephps-events-system/'
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2019-01-31
 * @package
 * 
 */



App::uses('Security', 'Utility');
App::uses('CakeEventListener', 'Event');

class PollingresourceListener implements CakeEventListener {

    public function implementedEvents() {

        $selectedEvents['Model.Notification.NewNotificationReceived'] = 'newNotificationEvent';
        $selectedEvents['Model.User.NewAccessTokenRequested'] = 'notificationInit';         
        $selectedEvents['Model.Linkedaccount.Queued'] = 'newEventReceived';
        $selectedEvents['Model.Linkedaccount.Analyzing'] = 'newEventReceived';
        $selectedEvents['Model.Linkedaccount.Monitored'] = 'newEventReceived';
        $selectedEvents['Model.Pubmessage.ReadyForVisualization'] = 'newEventReceivedXXX'; 
        return ($selectedEvents);
    }
    
    function __construct() {
        $this->Pollingresource = ClassRegistry::init('Pollingresource');     
    }    
    

    /** 
     * Set flag indicating that the value of the monitored resource has changed
     * 
     * @param CakeEvent $event
     */
    public function newEventReceived(CakeEvent $event) {
 
        $data = [];

        $conditions = ['pollingresource_userIdentification' => $event->data['userIdentification'],
                       'pollingresource_status' => ACTIVE,
                       'pollingresource_type' => $event->data['model'],
                       'pollingresource_resourceId' => $event->data['id']
                      ];
        // 0 or 1 entry will be returned
        $pollingResults = $this->Pollingresource->find("first", $params = ['conditions' => $conditions,
                                                                            'recursive' => -1
                                                      ]);
        
        if (empty($pollingResults)) {               //create new polling resource object
            $data['Pollingresource']['pollingresource_status'] = ACTIVE;
            $data['Pollingresource']['pollingresource_interval'] = WIN_POLLING_INTERVAL; 
            $data['Pollingresource']['pollingresource_userIdentification'] = $event->data['userIdentification'];
            $data['Pollingresource']['pollingresource_type'] = $event->data['model'];  
            $data['Pollingresource']['pollingresource_resourceId'] = $event->data['id'];
        }
        else {                                     // pollingresource already exists, so just update the "change" flag
            $data['Pollingresource']['id'] = $pollingResults['Pollingresource']['id'];
        }
        
        if ($event->data['isFinalEvent']) {
            $data['Pollingresource']['pollingresource_interval'] = 0;
        }
     
        $data['Pollingresource']['pollingresource_newValueExists'] = true;
        $this->Pollingresource->save($data, $validate = true); 
    }

    
    /** 
     * Set flag indicating that the value of the monitored Notification resource has changed
     * when a new JWT is generated. It is assumed that the Pollingresource object already exists.
     * 
     * @todo This does NOT work in a multi-login environment. The login session from the first
     *       device will receive the regular updates of new notifications
     * 
     * @param CakeEvent $event
     */
    public function newNotificationEvent(CakeEvent $event) {
 
        $data = [];

        $conditions = ['pollingresource_userIdentification' => $event->data['userIdentification'],
                       'pollingresource_status' => ACTIVE,
                       'pollingresource_type' => $event->data['model'],
                      ];

        // 1 entry will be returned but be prepared if something strange had happened and no entry is found
        $pollingResults = $this->Pollingresource->find("first", $params = ['conditions' => $conditions,
                                                                            'recursive' => -1
                                                      ]);
        
           // READ THE LAST NOTIFICATION ID OF THE INVESTOR AND ADD IT 
           // AS $data['Pollingresource']['pollingresource_resourceId']        
        
        if (empty($pollingResults)) {               //create new polling resource object
            $data['Pollingresource']['pollingresource_status'] = ACTIVE;
            $data['Pollingresource']['pollingresource_interval'] = WIN_POLLING_INTERVAL; 
            $data['Pollingresource']['pollingresource_userIdentification'] = $event->data['userIdentification'];
            $data['Pollingresource']['pollingresource_type'] = $event->data['model'];  
            $data['Pollingresource']['pollingresource_resourceId'] = $event->data['id'];
        }
        else {                                     // pollingresource already exists, so just update the "change" flag
            $data['Pollingresource']['id'] = $pollingResults['Pollingresource']['id'];
        }
        
        if ($event->data['isFinalEvent']) {
            $data['Pollingresource']['pollingresource_interval'] = 0;
        }
     
        $data['Pollingresource']['pollingresource_newValueExists'] = true;
        $this->Pollingresource->save($data, $validate = true); 
    }    
 

    /** 
     * Initialize the monitored Notification resource
     * 
     * @param CakeEvent $event
     */
    public function notificationInit(CakeEvent $event) {
 
        $data = [];

        $conditions = ['pollingresource_userIdentification' => $event->data['userIdentification'],
                       'pollingresource_status' => ACTIVE,
                       'pollingresource_type' => $event->data['model'],
                      ];

        // 0 or 1 entry will be returned
        $pollingResults = $this->Pollingresource->find("first", $params = ['conditions' => $conditions,
                                                                            'recursive' => -1
                                                      ]);
        
           // READ THE LAST NOTIFICATION ID OF THE INVESTOR AND ADD IT 
           // AS $data['Pollingresource']['pollingresource_resourceId']        
        $this->Notification = ClassRegistry::init('Notification');
        $conditions = ['AND' => ['pollingresource_userIdentification' => $event->data['userIdentification'],
                                 'pollingresource_type' => $event->data['model']
                                ],
                       'OR' => [
                                ['pollingresource_status' => WAITING_FOR_VISUALIZATION ],
                                ['pollingresource_status' => READY_FOR_VISUALIZATION ],
                                ['pollingresource_status' => READ_BY_USER ]
                               ]
                      ];
        $NotificationResult = $this->Notification->find("first", $params = ['conditions' => $conditions,
                                                                            'recursive' => -1,
                                                                            'order' => array('Notification.id DESC'),
            
                                                      ]); 

        $data['Pollingresource']['pollingresource_resourceId'] = $NotificationResult['Notification']['id'];
        $data['Pollingresource']['pollingresource_newValueExists'] = true; 
        
        if (empty($pollingResults)) {               //create new polling resource object
            $data['Pollingresource']['pollingresource_status'] = ACTIVE;
            $data['Pollingresource']['pollingresource_interval'] = WIN_POLLING_INTERVAL; 
            $data['Pollingresource']['pollingresource_userIdentification'] = $event->data['userIdentification'];
            $data['Pollingresource']['pollingresource_type'] = $event->data['model'];     
        }
        else {                                     // pollingresource already exists, so just update the "change" flag
            $data['Pollingresource']['id'] = $pollingResults['Pollingresource']['id'];
        }
        
        if ($event->data['isFinalEvent']) {
            $data['Pollingresource']['pollingresource_interval'] = 0;
        }

        $this->Pollingresource->save($data, $validate = true); 
    }    
    
    
    
    
}
