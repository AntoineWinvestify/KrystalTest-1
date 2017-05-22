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
* @date 2017-01-01
* @package
*


2017-01-01		version 0.1
																	[OK, not tested]





Pending:







*/

App::uses('CakeEvent', 'Event');
class Usererror extends AppModel
{
	var $name= 'Usererror';




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);




/**
*
*	Callback Function
*	Rules are defined for what should happen when a database record is created or updated
*	Generate event of object creation
*/
function afterSave ($created, $options = array()) {

	if ($created) {

		$event = new CakeEvent('errorReported', $this, array('id' 	=> $this->id,
														'error' 	=> $this->data[$this->alias],
																	));

		$this->getEventManager()->dispatch($event);
	}			
	return true;
}


}