<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, http://www.winvestify.com                         |
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



Functions for the AdminPFP role


2017-06-14	  version 0.1
Initial version. 
 * All methods are "protected" using the "isAuthorized" function
 * 
 * 



Pending



*/

App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AdminpfpAppController
{

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Js');
	var $uses = array('User', 'Investorglobaldata');	
	var $components = array('Security');
  	var $error;
	



function beforeFilter() {
    parent::beforeFilter(); // only call if the generic code for all the classes is required.

//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test

//	$this->Security->requireSecure(	'login'	);
	$this->Security->csrfCheck = false;
	$this->Security->validatePost = false;	
// Allow only the following actions.
//	$this->Security->requireAuth();
	$this->Auth->allow('login','session', 'loginAction', 'showTallyman', 'startTallyman');    // allow the actions without logon
//$this->Security->unlockedActions('login');
//   echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     
//var_dump($_REQUEST);
//var_dump($this->request);
//      echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";     

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
    $investorIdentification = $this->Auth('User.Investor.investor_identity'); // read user identification
    $filterconditions = array('investor_identity', $investorIdentification);
    $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    $this->set('result', $result);
       
}



/**
 * 
 * Shows the initial, basic screen of the Tallyman service
 * 
 */
public function startTallyman() {
 //   	Configure::write('debug', 2); 

    $this->layout = 'Adminpfp.azarus_private_layout';
 

 //   $investorIdentification = $this->Auth('User.Investor.investor_identity'); // read user identification
    $filterconditions = array('investor_identity', $investorIdentification);
 //   $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    $this->set('result', $result);
       
}



/**
 * 
 * Shows the Tallyman data of a user
 * 
 */
public function showTallyman($investorIdentity = null, $platformId = null) {
 //   $this->autoRender = false;
  //  	Configure::write('debug', 2); 

    $this->layout = 'Adminpfp.azarus_private_layout';	
 ///   $investorIdentification = $this->Auth('User.Investor.investor_identity'); // read user identification
//    $resultTallyman = $this->xxxxx->find("all", maxíum 10 entries)
 
            
 //   $filterconditions = array('investor_identity', $investorIdentification);
 //   $result = $this->Investorglobaldata->readInvestorData($filterConditions);
    
 //   $this->set('result', $resultTallyman);
  
}




/** ajax call
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







/**ajax call
*	Write (= modifies) some data of an existing Administrator
*
*/
function editAdministratorData($id) {


}




public function loginAction() {
Configure::write('debug', 0); 
    echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
//$this->print_r2($this->request->data);
//$this->autoRender = false;
	 
        if ($this->Auth->login()) {
            echo "SESSION155 <br>";
            echo "We have succesfully logged in <br>";
  //          print_r($this->Session->read()) ."<br>";
            echo "<br>" . $this->Auth->redirectUrl()."<br>"."<br>";
         //   return $this->Auth->redirectUrl();
            $this->redirect($this->Auth->redirectUrl());
  echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";        
        }
        else {
            echo "User is not logged on<br>";
            echo __FILE__ . " " .  __METHOD__ . " " .  __LINE__  ."<br>";
        }
        
  exit;  
        if ($this->Auth->loggedIn()){
		echo "user has logged on";
                return $this->redirect($this->Auth->redirectUrl());
	}
	else {
		echo "User not logged on";
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
		$this->layout = 'Adminpfp.winvestify_adminpfp_login_layout';
	}
	$error = false;
	$this->set("error", $error);
}




public function logout() {
	$user = $this->Auth->user();		// get all the data of the authenticated user
	$event = new CakeEvent('Controller.User_logout', $this, array('data' => $user,
                            ));
	$this->getEventManager()->dispatch($event);
	return $this->redirect($this->Auth->logout());
}







/**ajax call
*	Reads the data from an Administrator 
*
*/
function readAdministratorData($adminId) {
	$this->layout = 'zastac_admin_layout';

}






}
