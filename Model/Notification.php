<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2009, http://www.winvestify.com                         |
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
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-10-18
* @package
*


2016-10-18		version 0.1
initial version





Pending:







*/

App::uses('CakeEvent', 'Event');
class Notification extends AppModel
{
	var $name= 'Notification';

	var $belongsTo = array(
		'Investor' => array(
			'className' => 'Investor',
			'foreignKey' => 'investor_id',
		)
	);




    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );



    var $defaultFields = [ 
        'investor' => ['id', 
                        'notification_textShort', 
                        'notification_textLong', 
                        'notification_icon', 
                        'notification_type', 
                        'notification_status',
                        'notification_url',
                        'notification_links'
                      ],
        'winAdmin' => ['id', 
                        'notification_textShort', 
                        'notification_textLong', 
                        'notification_icon', 
                        'notification_type', 
                        'notification_status',
                        'notification_textId',
                        'notification_url',
                        'notification_links',
                        'modified',
                        'created'
            ],              
        'superAdmin' => ['id', 
                        'notification_textShort', 
                        'notification_textLong', 
                        'notification_icon', 
                        'notification_type', 
                        'notification_status',
                        'notification_textId',
                        'notification_url',
                        'notification_links',
                        'modified',
                        'created'            
                      ],                
    ];



    /**
     *	Adds an item to the notification queue. The action should point to an url
     *
     *	@param  array 	$filteringConditions. Must be sufficient to identify 1 unique parent (=investor)
     *					for whom the notification is ment.
     *					Example: array("investor_id" => 12)
     *	@param  array 	$text	May be html with a link, including tooltip etc.
     *	@param	string	$icon	Link to an icon to be placed LEFT of the text (optional)
     *	@param  string	$extendedInfo	Extended information which will be shown in a popup
     *                                               window if the user clicked on link. If this is available
     *						the system will automatically create a "href" link
     *  @param string   $notificationDateTime   Date/time of publication of notification. If empty then 
     *                                              publication date/time is immediate   
     * 	@return boolean	true/false			
     */
    public function addNotification($filterConditions, $text, $icon , $extendedInfo, $notificationDateTime) {

            if (empty($icon)) {
                    $icon = "generalNotificationIcon";		// default icon
            }
            if (empty($notificationDateTime)) {
                    $notificationDateTime = date("Y-m-d H:i:s", time());
            }

            $data = array("investor_id"           => $filterConditions,
                        "notification_textShort"  => $text,
                        "notification_textLong"   => $extendedInfo,
                        "notification_icon"       => $icon,
                        "notification_publicationDateTime" => $notificationDateTime,
                        "notification_status"     => READY_FOR_VISUALIZATION,
                        );

            if ($this->save($data, $validate = true)) {
                    return true;
            }
            return false;
    }





    /**
     *
     *	Deletes a notification permanently from the notification stream, i.e. marks it as 'deleted'
     *
     *	@param int $id The internal reference of the Notification object to be checked
     * 	@return boolean		
     */
    public function api_deleteNotification($id) {
        $this->id = $id;
        $this->saveField('notification_status', DELETED, $validate = true);
        return true;
    }





    /**
     *	Get a list of notifications according to the filteringConditions provided
     *
     *	@param array $filterConditions
     *	@return	array $notifications List of notifications with slogan texts only
     */
    public function getList($filterConditions) {
        $result = $this->find("all", array('conditions' => $filterConditions, 
                                           'recursive'  => -1,
                                           'fields'	=> array('id', 'notification_textShort', 'notification_icon')
                                           ));
        return $result;
    }





    /**
     *	Read notification contents
     *
     *	@param 		array 	$filteringConditions. 
     *	@return		array	$notification 	It is possible to return contents of 0 or 1 notification
     */
    public function readNotificationContents($filterConditions) {

        $result = $this->find("first", array('conditions' => $filterConditions, 
                                             'recursive'  => -1,	
                             ));

        if (!empty($result)) {
            $this->id = $result['Notification']['id'];
        }
        return $result;
    }


    /** 
     * Determines if the current user (by means of its $investorId) is the direct or indirect owner
     * of the current Model. 
     * This functionality determines if a webclient may access the data of another webclient
     * with proper R/W permissions.
     * 
     * @param $investorId The internal reference of the investor Object
     * @param $id The internal reference of the Notification object to be checked
     * @return boolean   
     */
    public function isOwner($investorId, $id) {
        
        $filteringConditions = ['AND' => [
                                            'id' => $id,
                                            'investorId' => $investorId ],
                                'OR' => [
                                            ['notification_status' => READY_FOR_VISUALIZATION], 
                                            ['notification_status' => READ_BY_USER]
                                        ]
            
                               ];
        
        $result = $this->find("first", $params = ['conditions' => $filteringConditions,  
                                                  'recursive' => -1]);
        
        if (!empty($result)) {
            return true;
        }
        return false;
    }

   
    /** 
     * Mark the notification as "READ_BY_USER"
     * 
     * @param $investorId The internal reference of the investor Object
     * @param $id The internal reference of the Notification object to be checked
     * @return boolean   
     */
    public function api_NotificationRead($id) {
        $this->id = $id;
        $this->saveField('notification_status', READ_BY_USER, $validate = true);
        return true;
    }

    
    /** 
     * Get the "latest" modifications to the list of active Notifications of the
     * user (=investor).
     * The 
     * @param int $investorId The internal reference of the owner of the Notification
     * @param int $id The internal reference of the first New Notification to return
     * @return array $result One or more results
     * 
     */
    public function api_getLatestModifications($investorId, $id) {
// MUST SEARCH FOR STATE READYFOR VISUALIZATION ETC ETC
        $filterConditions = ['id >=' => $id,
                             'investor_id' => $investorId];
        
        $result = $this->find("all", array('conditions' => $filterConditions, 
                                           'recursive'  => -1,
                                           'fields'	=> array('id', 'notification_textShort')
                                           ));
        return $result;
    }    
    
    

    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function afterSave($created, $options = array()) {

        if ($created) {            
   
            $event = new CakeEvent("Model.Notification.NewAccessTokenRequested", $this, 
                       array(
                           'model' => "Notification",
                           'isFinalEvent' => false,
                           'userIdentification' => $this->data['Notification']['investor_id'],
                           'modelData' => $this->data,
                           'id' => $this->data['Notification']['id'],     
                           ));
            $this->getEventManager()->dispatch($event);                
                
                
                
            
        }
    }    
    
    
    
}
