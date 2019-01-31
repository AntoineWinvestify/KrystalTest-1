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
        
        $selectedEvents['Model.Queue2.accountAddedToQueue'] = 'accountAdded';
        $selectedEvents['Model.Queue2.accountDataCollection'] = 'accountdataCollection';
        $selectedEvents['Model.Queue2.accountDataCollection'] = 'accountdataCollection';   //???????
        $selectedEvents['Model.Queue2.accountAnalysisStarted'] = 'accountAnalysisStarted';  
        $selectedEvents['Model.Queue2.accountAnalysisFinished'] = 'accountAnalysisFinished'; 
        $selectedEvents['Model.Queue2.processingError'] = 'accountlinkingProcessingError';
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
    public function accountAdded(CakeEvent $event) {
        $data['Linkedaccount']['id'] = $event->data['userIdentification'];
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'QUEUED';
        $this->Linkedaccount->save($data, $validate = true);
    }

    /**
     * The system has 'received' the order from the user to link the account
     * and the account data is being downloaded
     * 
     * @param CakeEvent $event
     */
    public function accountdataCollection(CakeEvent $event) {
        $data['Linkedaccount']['id'] = $event->data['userIdentification'];
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'QUEUED';
        $this->Linkedaccount->save($data, $validate = true);
    }    
   
    
    /**
     * All data for the account has been downloaded and analysis has started
     * 
     * @param CakeEvent $event
     */    
    public function accountAnalysisStarted(CakeEvent $event) {
        $data['Linkedaccount']['id'] = $event->data['userIdentification'];
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'ANALYZING';
        $this->Linkedaccount->save($data, $validate = true);
    }

    
    /**
     * The account data has been analyzed completely and account added to 
     * regular update queue
     * 
     * @param CakeEvent $event
     */
    public function accountAnalysisFinished(CakeEvent $event) {
        $data['Linkedaccount']['id'] = $event->data['userIdentification'];
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'MONITORED';
        $this->Linkedaccount->save($data, $validate = true);
    }

    
    /** SHALL WE HAVE THIS EVENT AND HOW TO CONVEY THE INFO TO USER???
     * 
     * 
     * @param CakeEvent $event
     */
    public function accountlinkingProcessingError(CakeEvent $event) {
        $data['Linkedaccount']['id'] = $event->data['userIdentification'];
        $data['Linkedaccount']['linkedaccount_visualStatus'] = 'QUEUED';
        $this->Linkedaccount->save($data, $validate = true);
    }

    
 
}
