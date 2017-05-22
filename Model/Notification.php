<?php
/**
// @(#) $Id$
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
/*
	var $hasOne = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'marketplace_id',
		)
	);
*/



/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);





/**
*
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
*       @param string   $notificationDateTime   Date/time of publication of notification. If empty then 
 *                                              publication date/time is immediate   
* 	@return 	boolean	true
*                               false
*			
*/
public function addNotification($filterConditions, $text, $icon , $extendedInfo, $notificationDateTime) {

	if (empty($icon)) {
		$icon = "generalNotificationIcon";		// default icon
	}
	if (empty($notificationDateTime)) {
		$notificationDateTime = date("Y-m-d H:i:s", time());
	}
	
	$data = array("investor_id"             => $filterConditions,
                    "notification_textShort"  => $text,
                    "notification_textLong"   => $extendedInfo,
                    "notification_icon"       => $icon,
                    "notification_publicationDateTime" => $notificationDateTime,
                    "notification_status"     => READY_FOR_VISUALIZATION,
                    );
        
	if ($this->save($data, $validate = true)) {
		return true;
	}
	else {
		return false;
	}
}
  




/**
*not tested
*	Deletes a notification permanently from the notification stream, i.e. marks it as read
*
*	@param 		array 	$filteringConditions. Must be sufficient to identify 1 unique record.
*				Example: array("id" => 12)
* 	@return 	boolean		true	notification *deleted*
* 					false	notification NOT deleted as filtering conditions did not
* 						identify a UNIQUE notification
*			
*/
public function deleteNotification($filterConditions) {
	
	
	
	
		
}





/**
*
*	Get a list of notifications according to the filteringConditions provided
*
*	@param 		array 	$filteringConditions. 
*	@return		array	$notifications 	List of notifications with slogan texts only
*
*/
public function getList($filterConditions) {
	$result = $this->find("all", array('conditions' => $filterConditions, 
									'recursive' => -1,
									'fields'	=> array('id', 'notification_textShort', 'notification_icon')
						));
	return $result;
}





/**
 *
 *	Read notification contents
 *      A flag is set indicating that the contents has been read by user.
 *
 *	@param 		array 	$filteringConditions. 
 *	@return		array	$notification 	It is possible to return contents of 0 or 1 notification
 *
 */
public function readNotificationContents($filterConditions) {

	$result = $this->find("first", array('conditions' => $filterConditions, 
									'recursive' => -1,	
						));

	if (!empty($result)) {
            $this->id = $result['Notification']['id'];
            $this->saveField('notification_status', READ_BY_USER);			// mark as read
	}
	return $result;
}




}