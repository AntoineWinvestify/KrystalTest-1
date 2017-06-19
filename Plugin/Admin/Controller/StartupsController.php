<?php
/*
*
*
* StartupsController
* Deals with all administration procedures for handling of the investments
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-01-16
* @package
*
*
*
2016-01-16		version 0.1




Pending
Security
optimizations of DB reads, try to limit the amount of data using contains...




*/



class StartupsController extends AdminAppController
{
	var $name = 'Startups';
	var $helpers = array('Html', 'Form','Js');
	var $uses = array('Startup');
	var $components = array('Security');
	var $layout = "zastac_admin_layout";

	
function beforeFilter() {

	parent::beforeFilter(); // only call if the generic code for all the classes is required.

	$this->set('receivingController', strtolower($this->name));			// Required for AJAX callback	
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
	$this->set('receivingController', strtolower($this->name));
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




//----------------------------------------------------------------------
//----------------------------------------------------------------------
//----------------------------------------------------------------------


/** 
*	Show the basic layout for reading the startup data 
*	
*
* @param
* @return boolean
*
*/
public function companyDataPanel($startupId){

	$conditions = array('Startup.id' => $startupId);
	$resultStartupName = $this->Startup->find("first", $params = array('recursive'	=>  -1,
																	'fields' 		=> ('startup_name'),
																	'conditions'	=> $conditions,
																	)
											);
	$this->set("startupId", $startupId);
	$this->set('startupName', $resultStartupName['Startup']['startup_name']);
}





/**
*	
*	Lists all the basic data of the startup, like name, CIF, Contact info, Web,..
*
* @param
* @return boolean
*
*/
public function readStartupBasicCompanyData(){

	$startupId = $_REQUEST['startupId'];
	$conditions = array('Startup.id' => $startupId);
	
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  -1,
																	'conditions'	=> $conditions,
																	)
											);

	$this->set('resultStartupData', $resultStartupData);
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function readStartupGlobalData(){

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$startupId = $_REQUEST['startupId'];
	$conditions = array('Startup.id' => $startupId);

	$this->Startup->Behaviors->load('Containable');
	$this->Startup->contain('Companytext');  // Own model is automatically included	
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  -1,
																	'conditions'	=> $conditions,
																	)
											);

	$this->set('resultStartupData', $resultStartupData);
	$this->set('result', 1);	
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function readStartupInvestmentOptionsData(){

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$startupId = $_REQUEST['startupId'];
	$conditions = array('Startup.id' => $startupId);

	$this->Startup->Behaviors->load('Containable');
	$this->Startup->contain('Investoption');  // Own model is automatically included	
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  -1,
																	'conditions'	=> $conditions,
																	)
											);
	$this->set('resultStartupData', $resultStartupData);
	$this->set('result', 1);		
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function readStartupKeyPeopleData(){

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$startupId = $_REQUEST['startupId'];
	$conditions = array('Startup.id' => $startupId);

	$this->Startup->Behaviors->load('Containable');
	$this->Startup->contain('Teammember');  // Own model is automatically included	
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  -1,
																	'conditions'	=> $conditions,
																	)
											);
	$this->set('resultStartupData', $resultStartupData);	
}





/**
*	Read list of startups
*
* @param
* @return
*
*/
public function readStartupList(){

	$conditions = array("AND" => array(
					array('Investment.id' => $startup),
					array('Investment.investoption_startDateTime <' => $actualDateTime),
					array('Investment.investoption_finalDateTime >' => $actualDateTime),
				));

	$resultStartupList = $this->Startup->find("all", $params = array('recursive'	=> -1,
							//		'conditions'	=> $conditions,
										)
											);
	$this->set('resultStartupList', $resultStartupList);
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function writeStartupBasicCompanyData(){

	$result = false;
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$list = json_decode($_REQUEST['jsonList'], true);
	$this->Startup->id = $_REQUEST['startupId'];
	
	if ($this->Startup->save($list, $validate = TRUE)) {
		$result = true;
	}
	
	$conditions = array('Startup.id' => $startupId);										
	$resultStartupList = $this->Startup->find("all", $params = array('recursive'	=>  -1,
								'conditions'	=> $conditions,
								)
								);

	$this->set('resultStartupData', $resultStartupData);	
	$this->set("result", $result);
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function writeStartupGlobalData(){
	$result = true;
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$startupId = $_REQUEST['startupId'];
	$list = json_decode($_REQUEST['jsonList'], true);
	
	$conditions = array('id' => $startupId);
	$this->Startup->Behaviors->load('Containable');
	$this->Startup->contain('Companytext');  							// Own model is automatically included
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  1,
																	'conditions'	=> $conditions,
																	)
											);

	$this->Companytext = ClassRegistry::init('Companytext');	
	foreach ($resultStartupData[0]['Companytext'] as $text)  {
		$this->Companytext->create();
		if ($this->Companytext->save(array('id' => $text['id'],
										   'companytext_text' => $list[$text['companytext_textCode']]))) {
			
			if ($result <> false) {
				$result = true;
			}
		}
		else {
			$result = false;
		}	
	}
	$conditions = array('Startup.id' => $startupId);
	$this->Startup->Behaviors->load('Containable');
	$this->Startup->contain('Companytext');  							// Own model is automatically included	
	$resultStartupData = $this->Startup->find("all", $params = array('recursive'	=>  1,
																	'conditions'	=> $conditions,
																	)
											);
	$this->set('resultStartupData', $resultStartupData);	
	$this->set("result", $result);
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function writeStartupInvestmentOptionsData(){
	

	
}





/**
*	
*	
*
* @param
* @return boolean
*
*/
public function writeStartupKeyPeopleData(){
	

	
}







//*******************************************************************************************************
//  TESTED FUNCTIONS
//******************************************************************************************************



}
