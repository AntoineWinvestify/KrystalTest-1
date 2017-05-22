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

Version 0.1
Basic version with simple user authentication

2014-09-01	  version 2014_0.2
Changed the logout routine. Also introduced events for "User logged on"
and "User logged off"
Optimization of code


Pending

*/

App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class AdministratorsController extends AppController
{

	var $name = 'Administrators';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('Administrator');	// Teacher not needed???
	var $components = array('Security');
/*
							'Auth' => array('authorize' => 'Controller',
												'loginRedirect'	=> array('controller' 	=> 'administrators',
																		 'action' 		=> 'home'
																	 ),
												'logoutRedirect' => array('controller' 	=> 'administrators',
																		 'action' 		=> 'login'
																		 ),
												),	);
*/
  	var $error;
	var $layout = 'zastac_admin_login_layout';		// use of "flan template" for intranet access



function beforeFilter() {

	parent::beforeFilter(); // only call if the generic code for all the classes is required.
	$this->Security->blackHoleCallback = '_blackHole';
//	$this->Security->unlockedFields = array('Student.sex', 'Pubform.sex'); // Pubform is correct
	$this->Security->requireSecure(
							'login'
							);

	$this->Security->validatePost = true;
//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test

// Allow only the following actions.
/*
$this->Auth->allow(array('view', 'index', 'job','prepareAttendanceRecord', 'cronjob',
																	'checkAttendanceRecord', 'checkHoliday',
																	'showListReadyForInputRecords',
																	'showListUnconfirmedRecords',
																	 ));
*/

	$this->Security->requireAuth();
}




/**
*	Redirect an action to using https
*
*/

function _blackHole()  {
	$this->redirect('https://' . env('SERVER_NAME') . env('REQUEST_URI'));
}





/**
*	Redirect an action to using http
*
*/

function _notblackHole()  {
	$this->redirect('http://' . env('SERVER_NAME') . env('REQUEST_URI'));
}





public function login() {
	$layout = 'default';		// very simple login screen
	if ($this->request->is('post')) {
		if ($this->Auth->login()) {
			$user = $this->Auth->user();		// get all the data of the authenticated user
			$event = new CakeEvent('Controller.User_login', $this, array(
												'data' => $user,
												));
			$this->getEventManager()->dispatch($event);
			return $this->redirect($this->Auth->redirectUrl());
		}
		else {
			$this->Session->setFlash(__('Username or password is incorrect'),
											'default',array(),	'auth');
		}
	}
}





public function logout() {
	$user = $this->Auth->user();		// get all the data of the authenticated user
	$event = new CakeEvent('Controller.User_logout', $this, array(
										'data' => $user,
										));
	$this->getEventManager()->dispatch($event);
	return $this->redirect($this->Auth->logout());
}





/**
*	password change function
*/
public function changepw() {
	$this->layout = 'intranet_layout';
	if ($this->Auth->user('id')) {   // Just to  make sure User is logged
		$this->User->id = $this->Auth->user('id');  // Set User Id
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__("Password has been changed"), 'default', array('class' => 'intranet_flash_msg'));
				$this->redirect(array('controller' => 'startpanels', 'action' => 'index' ));
			}
			else {
				$this->Session->setFlash(__("Password could not be changed."), 'default', array('class' => 'flash_msg_error'));
			}
		}
		else
			{
		}
	}
}





/**
*	Define a hashed password
*
*/
function generatepassword($password) {

	$pw = new SimplePasswordHasher;
	$hashedPassword = $pw::hash($password);
echo "Clear password = $password and hashed password = $hashedPassword";
echo "<br/>";

}







/**READ THE DATA OF ALL ADMINISTRATORS. TYPICALLY THIS IS A SMALL LIST (<50?)
*
*
*/
function readAdministratorList() {
	$this->layout = 'zastac_admin_layout';
	Configure::write('debug', 0);
/*
	$searchConditions = array(
		'AND' => array('Loanrequest.id >' 			=> 0,
						'Student.surname LIKE' 		=> "%". $_REQUEST['surname'] . "%", DATE_START
						'Student.telephone LIKE' 	=> "%". $_REQUEST['telephone'] . "%", DATE_FIN
      	  ),
		'OR' => array('s '  => '122',			STATE_ADMISION
					  's'  => '122222' )		STATE_ANALISIS
		);
*/

	$AdministratorListResult = $this->Administrator->find("all", $params = array('recursive'		=>  0,
																					'conditions' 	=> array('Loanrequest.id >' => 0),
																					)
															);
	$this->set('AdministratorListResult', $AdministratorListResult);


}









/**
*	Define new administrator and its corresponding access rights
*
*/
function addAdministrator() {
	$this->layout = 'zastac_admin_layout';

}




/**
 *THE RECORD IS NOT DELETED, JUST MARKED AS DELETED
 *
*	Delete an administrator
*
*/
function deleteAdministrator() {


}





/**ajax call
*	Reads the data from an Administrator
*
*/
function readAdministratorData($adminId) {
	$this->layout = 'zastac_admin_layout';

}





/**ajax call
*	Write (= modifies) some data of an existing Administrator
*
*/
function editAdministratorData() {


}





}
