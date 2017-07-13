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
//	var $helpers = array('Text');
//	var $uses = array('Investor', 'Metric');
	

	
	
function beforeFilter() {
	parent::beforeFilter();
	
}





/**
*
*	Gets list of all (not yet read) notifications of the user to be displayed on webpage
*	
*/
function getNotificationsList()  {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	$investorId = $this->Auth->user('Investor.id');
	$filterConditions = array('notification_status' => READY_FOR_VISUALIZATION,
							  'investor_id' 		=> $investorId,
							 );

	$resultNotifications = $this->Notification->getList($filterConditions);
	$this->set('resultNotifications', $resultNotifications);
	$this->set('error', $error);
}





/**
*
*	Gets the contents of a notification as defined by the $filterConditions
*	
*/
function readNotificationContent() {
	if (!$this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	$investorId = $this->Auth->user('Investor.id');
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




}