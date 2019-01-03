<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2014, http://beyond-language-skills.com                 |
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


Creates and manages a unique link per resource. A maximum number of accesses can
be defined, and an "expiry date" is available.
If the users tries to access a link which is not valid/no longer valid/expired the
component will generate a HTTP 404 error.
This component uses a database table called "uniquelinks"
It uses the session to manage the linkToken.


Version 2014-09-21   2014_0.1
Basic version with simple error handling, but only for the main data

	


PENDING:

*/







/**
* 
*
*/
App::uses('Component', 'Controller');
class UniqueLinkComponent extends Component
{
	var $myController;



function initialize(Controller $controller) {
	$this->myController = $controller;
}





/**  
*	Validates a linktoken. If successful then some userdata is returned, else
*	an HTTP-404 error is returned
*
*/
function validateUniqueLink($linkToken) {
	$this->myController->Uniquelink = ClassRegistry::init('Uniquelink');

	$linkData = $this->myController->Uniquelink->find("all", $params = array('recursive'	=> -1,
																			'conditions'	=> array("uniquelink_linktoken" => $linkToken),
																)
													);
	
	if (empty($linkData)) {
		throw new NotFoundException(__('Could not find the resource'))	;
	}

	$now = CakeTime::convert(time(), 'Europe/Madrid');		
	
	$start = $linkData[0]['Uniquelink']['uniquelink_firstUsageTimestamp'];
	$end = $linkData[0]['Uniquelink']['uniquelink_lastUsageTimestamp'];
	
	if ($now < $start OR $now > $end) {
		throw new NotFoundException(__('Could not find the resource'))	;
	}

	if ($linkData[0]['Uniquelink']['uniquelink_leftUsage'] == 0) {
		throw new NotFoundException(__('Could not find the resource'))	;
	}
	return ($linkData);
}





/**
*	Checks a link token. This should be called every-time for each "screen" of a multi-page form page
*
*	@param $linkToken	
*	@return boolean	
*
*/
function checkUniqueLinkToken($linkToken) {

	$linkData = $this->myController->Uniquelink->find("all", $params = array('recursive'	=> -1,
																		   'fields'		=> array('id','uniquelink_leftUsage'),
																			'conditions'	=> array("uniquelink_linktoken" => $linkToken),
																)
													);	
	
	if (empty($linkData)) {
		throw new NotFoundException(__('Could not find the resource: Not Existent'))	;
	}
	return true;
}





/**
*	Revokes the current link token. If it is a *one time* link then this link will become invalid,
*	else the "stock" counter is decremented
*	@param array 	
*	@return boolean	
*
*/
function revokeCurrentUniqueLinkToken($linkToken) {
	$result = $this->myController->Uniquelink->find("all", $params = array('recursive'	=> -1,
																		   'fields'		=> array('id','uniquelink_leftUsage'),
																			'conditions'	=> array("uniquelink_linktoken" => $linkToken),
																)
													);
	$left = $result[0]['Uniquelink']['uniquelink_leftUsage'];
	$this->myController->Uniquelink->read(NULL,$result[0]['Uniquelink']['id'] );
	$this->myController->Uniquelink->set('left_usage', $left);
	$this->myController->Uniquelink->save(array('id' => $result[0]['Uniquelink']['id'],
												'uniquelink_leftUsage' => $left - 1));
	return true;
}





/**???????????????????
*	Interface for reading the new unique linktoken
*	@param array 	 	'user' data required for the unique link request
*	@return 
*
*/
function readLinkToken($id) {
	$this->myController->Uniquelink->id = $id;
	return $this->myController->Uniquelink->field('uniquelink_linktoken');
}





/** 
*	Admin interface for defining a new unique link
*	
*	@param array 		'user' data required for the unique link request
*	@return linktoken
*			false
*
*/
function createLinkToken($userData) {
	$result = openssl_random_pseudo_bytes(40);

	$userData['Uniquelink']['uniquelink_linktoken'] = bin2hex($result);
	$this->myController->Uniquelink = ClassRegistry::init('Uniquelink');
	$fieldList = array('uniquelink_linktoken', 'uniquelink_firstUsageTimestamp', 'uniquelink_lastUsageTimestamp', 'uniquelink_leftUsage');

	if ($this->myController->Uniquelink->save($userData, $validate = true, $fieldList)) {
		return $userData['Uniquelink']['uniquelink_linktoken'];
	}
	else 	{
		return false;
	}
}

}	// end class
?>