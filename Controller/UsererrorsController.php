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
* @date 2017-01-01
* @package
*

	Deals with error reporting by the user

2017-01-01	  version 0.1




Pending:




*/

App::uses('CakeEvent', 'Event');
class UsererrorsController extends AppController
{
	var $name = 'Usererrors';
	var $uses = array('Usererror');
  	var $error;
//	var $layout = 'default';

	
	
function beforeFilter() {

	parent::beforeFilter();

}







/**
*
*	Creates an error Report 
*	inputs optionalText by user
*
*/
public function createReport() {
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
	
	$data['javascriptData'] = $_REQUEST['javascriptData'];		// For possible future extension 
	$data['session'] = $this->Session->read();
	$error_data['error_data'] = json_encode($data);
	$error_data['error_nameSurname'] = $this->Session->read('Auth.User.Investor.investor_name') . " " .
									$this->Session->read('Auth.User.Investor.investor_surname');
	$error_data['error_investorReference']  = $this->Session->read('Auth.User.Investor.investor_identity');
	$error_data['error_investorEmail']  = $this->Session->read('Auth.User.Investor.investor_email');
	$error_data['error_optionalText']  = $_REQUEST['optionalText'];

	if ($this->Usererror->save($error_data, $validate = true)) {
		$error = false;
	}
	else {
		$error = true;
	}

// prepare a screen answer to the error submittor
	$this->set('error', $error);
}





/**
*
*	Send modal for error reporting to browser
*
*
*/
public function getErrorModal() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$error = false;
	$this->layout = 'ajax';
	$this->disableCache();
}	



}