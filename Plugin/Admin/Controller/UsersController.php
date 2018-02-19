<?php
/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                         |
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
 * Functions for the Winadmin role
 * 
 * 2017-07-08	  version 0.1
 * Initial version. 
 * All methods are "protected" using the "isAuthorized" function
 *
 * [2017-09-04] version 0.2
 * Added correct logout
 * 
 * 
 * Pending
 * 
 */

App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AdminAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User');	
	var $components = array('Security');

  	var $error;
	var $layout = 'winvestify_admin_login_layout';		// use of "flan template" for intranet access



function beforeFilter() {
	parent::beforeFilter(); // only call if the generic code for all the classes is required.


//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test


//	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actions.
//	$this->Security->requireAuth();
	$this->Auth->allow('login','session', 'loginAction', 'testmodal', 'logout');    // allow the actions without logon
    

}



/**
 * 
 * Shows a list of investors, using dataTable, in order to select/view/modifiy the data of 1 
 * investor
 * 
 */
public function showInvestorList() {
    
    
}

/**
 * 
 * Shows the data of an individual investor. This section is divided into personal data,
 * investment data etc,
 * 
 */
public function showInvestorDataPanel() {
    
    
}

public function testmodal() {
 
    

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

	$resultInvestor = $this->Investor->find("all", $params = array('recursive'  => -1,
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

 







public function loginAction() {
        if ($this->Auth->login()) {
            $this->redirect($this->Auth->redirectUrl());
        }
        else {
            echo "User is not logged on<br>";
        }
}



/**
*
*	Shows the login panel
*
*/
public function login()
{
	if ( $this->request->is('ajax')) {
		$this->layout = 'ajax';
		$this->disableCache();
	}
	else {
		$this->layout = 'winvestify_admin_login_layout';
	}
	$error = false;
	$this->set("error", $error);
}




public function logout() {
	$user = $this->Auth->user();		// get all the data of the authenticated user
	$event = new CakeEvent('Controller.User_logout', $this, array('data' => $user,
				));
	$this->getEventManager()->dispatch($event);
        $this->Session->destroy();						// NOT NEEDED?
	$this->Session->delete('Auth');
        $this->Session->delete('Acl');
        $this->Session->delete('sectorsMenu');
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
