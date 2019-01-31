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

        $selectedEvents['Model.Notification.newNotificationReceived'] = 'newEventReceived';
        $selectedEvents['Model.Linkedaccount.Queued'] = 'newEventReceived';
        $selectedEvents['Model.Linkedaccount.Analyzing'] = 'newEventReceived';
        $selectedEvents['Model.Linkedaccount.Monitored'] = 'newEventReceived';
        $selectedEvents['Model.Pubmessage.readyForVisualization'] = 'newEventReceived'; 
        return ($selectedEvents);
    }

    /** 
     * Set flag that value of monitored resource has changed
     * 
     * @param CakeEvent $event
     */
    public function newEventReceived(CakeEvent $event) {
        $data = [];
        
        $Model = array_keys[$event->data];          // Only 1 index should exists
        $conditions = ['pollingresource_userIdentification' => $event->data['userIdentification'],
                       'pollingresource_status' => ACTIVE,
                       'pollingresource_type' => $Model[0],
                       'pollingresource_resourceId' => $event->data['id']
                      ];
        // 0 or 1 entry will be returned
        $pollingResults = $this->Pollingresource->find("first", $params = ['conditions' => $conditions,
                                                                            'recursive' => -1
                                            ]);
        if (empty($pollingResults)) {
            //create new polling resource
            $data['Pollingresource']['pollingresource_status'] = ACTIVE;
            $data['Pollingresource']['pollingresource_interval'] = WIN_POLLING_INTERVAL; 
            $data['Pollingresource']['pollingresource_userIdentification'] = $event->data['userIdentification'];
            $data['Pollingresource']['pollingresource_type'] = $Model;  
        }
        else {  // pollingresource already exists, so just update the "change" flag
            $data['Pollingresource']['Pollingresource']['id'] = $pollingResults['Pollingresource']['id'];
        }

        $data['Pollingresource']['pollingresource_newValueExists'] = true;
        $this->Pollingresource->save($data, $validate = true); 
    }
 
}
