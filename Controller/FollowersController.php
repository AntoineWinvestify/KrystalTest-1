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



*/


class FollowersController extends AppController
{
	var $name = 'Followers';
	var $helpers = array('Js', 'Text');
	var $uses = array('Follower');
  	var $error;
	var $layout = 'default';

	
	
function beforeFilter() {

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
}







/**
*
*	Cancels an earlier created "Follow" request
*inputs:investorId parent, InvestorId child
*request is also removed from the NOTIFICATION stream JSON response
*/
public function cancelFollowRequest() {


}





/**
*
*	Create a "Follow" request from child to parent
*inputs:investorId parent, InvestorId child
*request is stored as a NOTIFICATION JSON response
*/
public function createFollowRequest() {


}





/**
*
*	Create an "UnFollow" request from child to parent
*inputs: investorId parent investorId child JSON response
*/
public function createUnFollowRequest() {
	$this->autoRender = false;
	Configure::write('debug', 2);
/*
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
*/
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();



	$this->set('error', $error);	// NEEDED????
	$this->set('followers', $followers);
}





/**
*
*	Accepts a "Follow" request by parent
*	User picked up the item from the notification stream
*input : investorId JSON response
*/
public function followRequest() {
	$this->autoRender = false;
	Configure::write('debug', 2);
/*
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
*/
	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	
	if ($this->Follower->followAction(parent, child, "follow")   ){
		
		
	}
	else {
//		return $error;
	}
	
	$this->set('error', $error);
	$this->set('followers', $followers);
}





/**
*
*	Reads the list of followers of an investor. It will show name (or alias) using a tooltip
*	and (small) photograph 
*
*/
public function readAllFollowers() {

	Configure::write('debug', 2);

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();

	$followers = $this->Follower->readFollowers(array('follower_parentId' => $this->investorId));
	$this->set('error', $error);
	$this->set('followers', $followers);
}






}