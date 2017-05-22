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
class UsersController extends AdminAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User');	
	var $components = array('Security',
/*
							'Auth' => array('authorize' => 'Controller',
												'loginRedirect'	=> array('plugin' 		=> 'admin',
																		 'controller' 	=> 'investments',
																		 'action' 		=> 'readInvestmentsList'
																	 ),
												'logoutRedirect' => array('controller' 	=> 'users',
																		 'action' 		=> 'login'
																		 ),
												),
*/
							);

  	var $error;
	var $layout = 'zastac_admin_layout';		// use of "flan template" for intranet access



function beforeFilter() {
//	Configure::write('debug', 2);
	parent::beforeFilter(); // only call if the generic code for all the classes is required.


//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test


	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actions.
	$this->Security->requireAuth();
	$this->Auth->allow('login','session');    // allow the actions without logon
//$this->Security->unlockedActions('login');

	
}



/**
*	must eventually be moved to the admin section. After action this user does no longer exist
*	neither ANY of his data
*/
public function deleteUser($email) {
	$this->autoRender = false;
	$this->Investor = ClassRegistry::init('Investor');			// Load the "Operation" model

	$conditions = array("AND" => array(array('investor_email' => $email),
						));

	$resultInvestor = $this->Investor->find("all", $params = array('recursive'		=> -1,
																	'conditions'	=> $conditions,
																	)
											);
	if (!empty($resultInvestor)) {
		$result = $this->Investor->delete($resultInvestor[0]['Investor']['id'],$cascade = true);
	}
}






/**
*	Define new administrator and its corresponding access rights
*
*/
function addAdministrator() {


}




/**ajax call
 *
 *
*	Reads the data from an Administrator 
*
*/
public function adminHome() {
echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";	


echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";		
}

 






/**
*	password change function
*/
public function changeAdminPw() {

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
 *THE RECORD IS NOT DELETED, JUST MARKED AS DELETED
 *
*	Delete an administrator
*
*/
function deleteAdministrator() {


}





/**ajax call
*	Write (= modifies) some data of an existing Administrator
*
*/
function editAdministratorData() {


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

 








public function login() {
		
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
	if ($this->Auth->loggedIn()){
		echo "user is logged on";	
	}
	else {
		echo "User not logged on";
	}
	
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";	
//	echo $this->Auth->authError();
var_dump($this->params);	
	$this->layout = 'zastac_admin_login_layout';
//echo $this->Session->flash();
//echo $this->Session->flash('auth');	
var_dump($_REQUEST);		
pr($this->Session->read());
pr($this->request->data);
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
echo $this->Auth->user('id');
debug($this->request->data);
echo  $this->Auth->loggedIn();
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
var_dump($this->request);	
	if ($this->request->is('post')) {
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";			
	
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";

		if ($this->Auth->login()) {
		echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
		pr($this->request);
			$user = $this->Auth->user();		// get all the data of the authenticated user
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";			
			exit;
			return $this->redirect($this->Auth->redirectUrl());
		}
		else {
		echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";			
			$this->Session->setFlash(__('Username or password is incorrect'),
											'default',array(),	'auth');
			echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
			exit;
		}
	}
	echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
//	exit;
}






public function logout() {
	$user = $this->Auth->user();		// get all the data of the authenticated user
	$event = new CakeEvent('Controller.User_logout', $this, array(
										'data' => $user,
										));
	$this->getEventManager()->dispatch($event);
	return $this->redirect($this->Auth->logout());
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





/**ajax call
*	Reads the data from an Administrator 
*
*/
function readAdministratorData($adminId) {
	$this->layout = 'zastac_admin_layout';

}






}
