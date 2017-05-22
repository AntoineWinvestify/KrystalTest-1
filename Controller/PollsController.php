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
* @date 2016-10-07
* @package
*

2016-10-07	  version 2016_0.1





Pending:
Error Handling (also in related views)
Security
User Authentication
User Session



*/
App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class PollsController extends AppController
{
	var $name = 'Polls';
	var $helpers = array('Js', 'Text');
	var $uses = array('Pollanswer', 'Poll');
	var $layout = 'default';

	
	
function beforeFilter() {
	Configure::write('debug', 0);
	parent::beforeFilter();
//	$this->Security->requireAuth();
	$this->set('parentController', strtolower($this->name));			// Required for AJAX callback
	$this->set('parentAction', strtolower($this->name) . "Ajax");		// Generic AJAX callback function
	
	if (substr(env('REQUEST_URI'), strlen(env('REQUEST_URI')) - 5, 5) == "Panel") {   // Check if it ends in "Panel"
		$this->callbackFunction = substr(env('REQUEST_URI'), 0, strlen(env('REQUEST_URI')) - 5);
	}
	
	// read investorId from Session
//	$this->investorId = $this->Auth->user('Investor.id');
	$this->investorId = 1;
	
}





/**
*NOT USED???
*	Reads all the  data of ALL investments in all the companies where the investor
*	has a linked account
*
*/
function showPollData()  {
	$this->autoRender = false;
	Configure::write('debug', 2);
	
	$companyId = 1;
	$timeLine = array('start'	=> '2016-01-01 10:10:10',
					  'stop'	=> '2016-31-12 10:10:10');
	$atimeLine = 1;

	$result = $this->Poll->calculateRating($companyId, $atimeLine);
	echo "result = $result<br>";	
}





/**
*
*	Get all data related to the pollquestions
*
*/
function readPollQuestions()  {

	$error = false;
	Configure::write('debug', 0);

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$error = false;	
	$this->layout = 'ajax';
	$this->disableCache();
									 
	$pollId = $_REQUEST['pollId'];		
	$ratingDataResult = $this->Poll->readQuestionsData($pollId);

	$this->set('ratingDataResult', $ratingDataResult);
	$this->set('error', $error);
}





/** NOT TESTED
*AJAX
*	Stores the poll data
*
*/
function storePollData()  {
	$error = false;
	Configure::write('debug', 0);

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
	
	$pollId = $_REQUEST['pollId'];
	$answers = $_REQUEST['answers'];

	$result = $this->Poll->storeData($pollId, $this->investorId, $answers);
	
	echo "result = $result<br>";
	if ($result == true)  {
		$error = false;
	}
	$this->set('error', $error);
}


}
