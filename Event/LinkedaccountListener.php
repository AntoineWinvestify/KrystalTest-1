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

class LinkedaccountListener implements CakeEventListener {

    
    public function implementedEvents() {

        $selectedEvents['Model.Queue2.AccountAddedToQueue'] = 'accountQueued';
        $selectedEvents['Model.Queue2.AccountDataCollection'] = 'accountQueued';
        $selectedEvents['Model.Queue2.AccountDataCollection'] = 'accountQueued';   
        $selectedEvents['Model.Queue2.AccountAnalysisStarted'] = 'accountAnalysisStarted';  
        $selectedEvents['Model.Queue2.AccountAnalysisFinished'] = 'accountAnalysisFinished'; 
        $selectedEvents['Model.Queue2.ProcessingError'] = 'accountlinkingProcessingError';  // ???????
        return ($selectedEvents);
    }


    function __construct() {
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');    
    }


    /**
     * The system has 'received' the order from the user to link the account
     * and the account data is being downloaded
     * 
     * @param CakeEvent $event
     */
    public function accountQueued(CakeEvent $event) {

        $data = [];
        $queueInfo = json_decode($event->data['modelData']['Queue2']['queue2_info'], true);

        $data['Linkedaccount']['id'] = $queueInfo['companiesInFlow'][0];     
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'QUEUED';
        $this->Linkedaccount->save($data, $params = ['validate' => true,
                                                   // 'callbacks' => false
            ]);
    }
   
    
    /**
     * All data for the account has been downloaded and analysis has started
     * 
     * @param CakeEvent $event
     */    
    public function accountAnalysisStarted(CakeEvent $event) {

        $data = [];
        $queueInfo = json_decode($event->data['modelData']['Queue2']['queue2_info'], true);     

        $data['Linkedaccount']['id'] = $queueInfo['companiesInFlow'][0];         
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'ANALYZING';
        $this->Linkedaccount->save($data, $params = ['validate' => true,
                                                  //  'callbacks' => false
            ]);
    }

    
    /**
     * The account data has been analyzed completely and account is added to 
     * regular update queue
     * 
     * @param CakeEvent $event
     */
    public function accountAnalysisFinished(CakeEvent $event) {

        $data = [];
        $queueInfo = json_decode($event->data['modelData']['Queue2']['queue2_info'], true);    

        $data['Linkedaccount']['id'] = $queueInfo['companiesInFlow'][0]; 
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'MONITORED';
        $this->Linkedaccount->save($data, $params = ['validate' => true,
                                                 //   'callbacks' => false
            ]);
    }

    
    /** SHALL WE HAVE THIS EVENT AND HOW TO CONVEY THE INFO TO USER???
     * 
     * 
     * @param CakeEvent $event
     */
    public function accountlinkingProcessingError(CakeEvent $event) {

        $data = [];
        $queueInfo = json_decode($event->data['modelData']['Queue2']['queue2_info'], true);     

        $data['Linkedaccount']['id'] = $queueInfo['companiesInFlow'][0]; 
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'QUEUED';
        $this->Linkedaccount->save($data, $params = ['validate' => true,
                                                   // 'callbacks' => false
            ]);
    }

    
 
}
