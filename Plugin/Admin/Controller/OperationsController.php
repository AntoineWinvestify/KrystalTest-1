<?php
/*

2015-11-09		version 0.1
It is  checked if file has been treated before, i.e. to avoid duplicates
Checking of related direct debit order when a rejected direct debit is received					[OK]


Pending

*/



class OperationsController extends AppController
{
	var $name = 'Operations';
	var $helpers = array('Html', 'Form','Js');
	var $uses = array('Operation');
//	var $components = array('Security');	
	var $layout = 'zastac_admin_layout';
	var $components = array('Security',
							'Auth' => array('authorize' => 'Controller',
												'loginRedirect'	=> array('controller' 	=> 'administrators',
																		 'action' 		=> 'home'
																	 ),
												'logoutRedirect' => array('controller' 	=> 'administrators',
																		 'action' 		=> 'login'
																		 ),
												),	);
	

function beforeFilter() {
//	Configure::write('debug', 2);	
	parent::beforeFilter(); // only call if the generic code for all the classes is required.

//	$this->Security->blackHoleCallback = '_blackHole'; 
//	$this->Security->unlockedFields = array('Student.sex', 'Pubform.sex'); // Pubform is correct
/*	
	$this->Security->requireSecure(	
							'checkHoliday'
							);
*/
//	$this->Security->validatePost = false;
/*
//	$this->Security->disabledFields = array('Participant.club'); // this excludes the club1 field from CSRF protection
															// as it is "dynamic" and would fail the CSRF test

// Allow only the following actions.
	$this->Auth->allow(array('view', 'index'));
*/		
//	$this->Security->requireAuth();
//	$this->Auth->allow('cronjob_check_duedate');
//	$this->Auth->allow();    // allow all actions as these are public pages

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




/**THIS IS CORRECT FOR THE VERSION AS FUNCIONA HAS IMPLEMENTED, DOWNLOADING FULL DB.
 *TO BE UPDATED WITH SERVER FILTERING
*	Reads basic data of ALL the loanrequests
*
* @param string $element_name
* @return WebElement found element or null
* 
*/
public function readAllOperationsData(){

//	Configure::write('debug', 2);
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
						
	$readAllOperationsDataResult = $this->Operation->find("all", $params = array('recursive'		=>  0, 		
																					'conditions' 	=> array('Operation.id >' => 0),
																					)
															);
	
//$this->print_r2($readAllOperationsDataResult);	
	$this->set('readAllOperationsData', $readAllOperationsDataResult);
}






//*******************************************************************************************************
//  TESTED FUNCTIONS
//******************************************************************************************************



}
