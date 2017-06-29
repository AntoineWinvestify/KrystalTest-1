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
* @date 2016-12-19
* @package
*


2016-12-19		version 0.1

2017-01-18		version 0.2
Added callback routine "afterSave"												[OK]





Pending:







*/

App::uses('CakeEvent', 'Event');
class Data extends AppModel
{
	var $name= 'Data';
	public $useTable = 'data';
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
//var $validate = array(
// );





/**
*
*	Callback Function
*	Generates the "created" field
*
*/
public function beforeSave($options = array()) {

    $this->data[$this->alias]['created'] = date("Y-m-d H:i:s", time());
    return true;
}





/**
*	
*	Write a notification about this event
*
*/
function afterSave ($created, $options = array()) {
	
	if ($created) {
		$investorReference = $this->data['Data']['data_investorReference'];

		$this->Investor = ClassRegistry::init('Investor');	
		$investorId = $this->Investor->investorReference2Id($investorReference);

		$this->Notification = ClassRegistry::init('Notification');
		$text = __('Your dashboard has been updated');
		$longText = __('The system has updated your dashboard data. You can access your dashboard here');
		$this->Notification->addNotification($investorId, $text, "", $longText, "");
	}



}


}