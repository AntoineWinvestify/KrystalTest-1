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
* @date 2016-01-10
* @package
*


2016-01-10		version 0.1
multi-language support added




Pending:

*/


class Investment extends AppModel
{
	var $name= 'Investment';

	var $belongsTo = array(
		'Startup' => array(
			'className' => 'Startup',
			'foreignKey' => 'startup_id',
		)
	);




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);













/**
*
*if state has changed to paid -> generate a new event
*
*/
public function afterSave($options = array()) {
echo __FILE__ . " " . __LINE__ . "<br>";	
pr($this->alias);

exit;																		
	$eventName = 'investmentConfirmedDelayedPayment';
	$event = new CakeEvent($eventName, $this, array('id' 		=> $this->id,
													'investment'=> $this->data[$this->alias],
													));
	$this->getEventManager()->dispatch($event);

	return true;
}











}
