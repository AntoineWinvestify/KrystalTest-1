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
* @date 2016-10-18
* @package
*

2016-10-18	  version 0.1
														[almost OK]



Pending:
Error Handling (also in related views)
Security
User Authentication
User Session



*/


class PreferredfollowersController extends AppController
{
	var $name = 'Preferredfollowers';
	var $helpers = array('Js', 'Text');
	var $uses = array('Preferredfollower');
  	var $error;
	var $layout = 'default';

	
	
function beforeFilter() {
/*
	parent::beforeFilter();
//	$this->Security->requireAuth();
	$this->set('parentController', strtolower($this->name));			// Required for AJAX callback
	$this->set('parentAction', strtolower($this->name) . "Ajax");		// Generic AJAX callback function
	
	if (substr(env('REQUEST_URI'), strlen(env('REQUEST_URI')) - 5, 5) == "Panel") {   // Check if it ends in "Panel"
		$this->callbackFunction = substr(env('REQUEST_URI'), 0, strlen(env('REQUEST_URI')) - 5);
	}
	
	// read investorId from Session
//	$this->investorId = $this->Auth->user('Investor.id');
$this->investorId = 1;		// TEMP FIX
*/
}








}