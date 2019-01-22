<?php
/**
* +-----------------------------------------------------------------------+
* | Copyright (C) 2019, http://www.winvestify.com                         |
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
*
*
* @author 
* @version 0.3
* @date 2017-05-25
* @package
*

2016-10-25 Version 0.1		
Basic version with simple user authentication							[Not fully tested, ]


Model checks for unique username key (i.e. email)						[OK]
Placeholder in login page which indicates that userid = email					[OK]
Added routine for field "lastAccessed"								[OK]


2017-03-28      Version 0.2		
also sent telephone number to view of registerPanelB to improve accuracy of Google Analytics function  [Not Tested yet]
New method, initLoad, for first loading location BEFORE presenting the real landingpage


2017-05-25      Version 0.3
Removal of initLoan.




Pending
Authentication: provide error message in case of wrong credentials
Problem with Security component: AJAX calls are blacklisted. Security is de-activated for the time being
User registration

Updating of user profile (and at the same time relevant info in Session)
	$this->Security->validatePost = false;  CHECK HOW TO CHANGE THIS
	
	
	each action shall check if user is actually logged on (in beforeFilter)
	
	$this->Auth->id to read the id of the authenticated user if he is logged on
	
	$this->Auth->loggedIn()
	
$id = $this->Auth->user('id');

If the current user is not logged in or the key doesn’t exist, null will be returned.	
	
	
*/
?>

<?php
//App::import('Vendor', 'Firebase', array('file' => 'firebase' . DS . 'php-jwt' . DS .'src' . DS . 'JWT.php'));
 
 
App::uses('CakeEvent', 'Event');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AppController
{
	var $name = 'Users';
	var $uses = array('User', 'Investor');
  	var $error;



function beforeFilter() {
	parent::beforeFilter(); // only call if the generic code for all the classes is required.

        $this->Auth->allow('login');

}





/**
*
*	Changes the display language for the user
*
*	@param 		string 	$language	ISO string for language 
*	@return 	boolean	true/users/loginAction
*
*//*
function changeDisplayLanguage() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$newLanguage = $_REQUEST['id'];			// two letter language code	

	$this->Cookie->write('p2pManager',	array('language' => $newLanguage));						// values are stored encrypted
	$this->Session->write('Config.language', $newLanguage);
}
*/


    /**
     *	
     *	Checking of login credentials and forwarding browser to default page	
     *
     */ 
    public function login() {
//echo __FILE__ . " " . __LINE__ . "<br>";
        $this->request->data['User'] = $this->request->data ; 

	if ($this->request->is('post')) {    
            $isUserIdentified = $this->Auth->identify($this->request, $this->response);

            if ($isUserIdentified) {                 
                $token = $this->getNewJWT($isUserIdentified, WIN_ACCESS_TOKEN);
                $result['token'] = $token;  
                $resultJson = json_encode($result);
                $this->response->statusCode(200);         
                $this->response->type('json');
                $this->response->body($resultJson); 
                return $this->response;                 
            }
            else {
                throw new UnauthorizedException('Email or password is wrong');                   
            }
	}
    }


/**
*
*	Shows the login panel
*
*//*
public function loginOld()
{
	if ( $this->request->is('ajax')) {
		$this->layout = 'ajax';
		$this->disableCache();      exit;
	}
	else {
		$this->layout = 'winvestify_publicLandingPageLayout';	
	}
	$error = false;
	$this->set("error", $error);
}


public function loginRedirect22() {
    $this->layout = "winvestify_login";
    $error = false;
    $this->set("error", $error);
}
*/

    


/**
*	
* logout of the user
*
*/
public function logoutOld() {
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





/**
*
*	returns the language as defined in a cookie of the user, or "".
*
*//*
public function readUsedLanguage() {
    if ($this->request->is('requested')) {
		if (empty($this->Cookie->read('p2pManager.language') == true)) {
			return "en";
		}
		return $this->Cookie->read('p2pManager.language');
	}
}
*/ 
 
 


/**
*
*	Registration of an investor, step 1
*	
*//*
public function registerPanel() {

	$this->layout = 'winvestify_publicLandingPageLayout';
	$error = false;
	$this->set("error", $error);
	$this->set('ownDomain', $this->request->domain());
	$this->render('registerPanelA');
}
*/



/**
*
*	Registration of an investor, step 1
*	
*	Request username, password and mobile phone number
*
*//*
public function registerPanelA() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$username = $_REQUEST['username'];	
	$password = $_REQUEST['password'];	
	$telephone = $_REQUEST['telephone'];								// Is a telephone number in international format, +xx yyyyzzzaa
	
	$userData = array('username' => $username,
			  'password' => $password,
			  'telephone' => $telephone
			  );	

	$locationData = $this->Session->read(locationData);
	$countryCode = $locationData['country_code'];	
	$validationResult = $this->User->createAccount($username, $password, $telephone, $countryCode);
print_r($validationResult);
	$error = ($validationResult[0]) ? false:true;							// basically inverting $result
	$this->set("error", $error);	
	$this->set('userData', $userData);	
	$this->set('validationResult', $validationResult[1]);						// contains validation error(s), if any
	
	if ($validationResult[0] == true) {								// No error detected
		$this->set('username', $username);
		$this->set('telephone', $telephone);
                $this->set('newRequestsAllowed', true);
		$this->render('registerPanelB');
	}
	else { 												// error detected, re-send it to the view for processing       
                $this->render('registerPanelA');
	}	
}
*/




/**
*
*	Check the code as entered by user. If correct prepare view for next screen
*
*//*
public function registerPanelB() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	
	$this->layout = 'ajax';
	$this->disableCache();
        $newRequestsAllowed = false;
	$receivedCode = $_REQUEST['code'];	
	$requestNewCode = $_REQUEST['requestNewCode'];	
	$username = $_REQUEST['username'];
	$investorId = $this->User->username2Id($username); 

	if ($requestNewCode == true) {								// user requested a new code
		$newConfirmationCode = $this->User->generateNewConfirmationCode($username);
		$this->print_r2($newConfirmationCode);
                if ($newConfirmationCode[1] < 4) {
                    $newRequestsAllowed = true;							// stupid or malicious user, ignore	
		}
                echo "new = $newConfirmationCode";
                $this->set('newRequestsAllowed', $newRequestsAllowed);	
	}
	else {
		$confirmationCode = $this->User->readConfirmationCode($username);
		if ($confirmationCode[0] === $receivedCode) {
			$this->Investor->updateAccountCreationStatus($investorId, CONFIRMED_ACCOUNT_WITH_DEFAULT_DATA);
			
//*******************************************************************************			
// the following lines only apply when social network becomes available 			
//			$this->Preferredfollower = ClassRegistry::init('Preferredfollower');
//			$resultPreferredFollowers = $this->Preferredfollower->listPreferredFollowers($countryCode);
//			$this->set('resultPreferredFollowers', $resultPreferredFollowers);
//*******************************************************************************

			$this->User->resetConfirmationCodeInformation($username);
			$this->render('registerPanelD');
		}
		
		if  ($confirmationCode[0] <> $receivedCode){		// show again the panel for requesting the confirmation code
			$errorMsg = __('You entered an incorrect code, please try again');
			$this->set('errorMsg', $errorMsg);
			$this->set("error", true);
		}
	}
}
*/




/** NOT FINISHED, BUT NOT YET USED
*
*	Check if at least one follower is selected and initiate the action to follow the selected investors/influencers 
*	
*//*
public function registerPanelC() {
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
	
	$this->User->resetConfirmationCodeInformation($username);	
	
	//Check if at least one follower selected

//	if (at least one request selected) {
	if (count($dd) > 0)  {
		//launch follow request
	// read InvestorID  from Session
		$user = $this->Auth->user();		// get all the data of the authenticated user
		/* DO THIS IN A DIFFERENT WAY AS USER IS NOT REALLY AUTHENTICATED
		$this->Investor->updateAccountCreationStatus($user['investor']['id'], FOLLOWERS_DEFINED);
	
	
		$this->Company = ClassRegistry::init('Company');
		$country = "ES";
		$filterCondition = array('Company.company_country' => $country);
		$resultCompanys = $this->Company->getCompanyDataList($filterCondition);
	
		$this->set('resultCompanys', $resultCompanys);
		$this->render('registerPanelD');
	}
	else {
		//generate error and return the registerPanelC with some error indication

	}
}	
*/	




/**
*
*	Store result of simple questions about investments
*
*//*
public function registerPanelD() {

    if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}
	$this->layout = 'ajax';
	$this->disableCache();

        $platformcount = $_REQUEST['platformcount'];	
	$platformtypes = $_REQUEST['platformtypes'];	
	$accreditedInvestor = $_REQUEST['accreditedInvestor'];
	$username = $_REQUEST['username'];
	$investorId = $this->User->username2Id($username);

// Store result of questions
	$data = array('investor_investmentPlatforms' => $platformtypes,
				  'investor_accredited'     => $accreditedInvestor,
				  'investor_platformCount'  => $platformcount);

	$this->Investor->id = $investorId;
	$this->Investor->save($data);

// Save the information in DB
        $this->Investor->updateAccountCreationStatus($investorId, QUESTIONAIRE_FILLED_OUT);
	$this->render('registerPanelE');
}
*/




/**
*
*	Registration of an investor, step 5, All done. 
*
*//*
public function registerPanelE() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
	
	$error = false;
	$this->set('error', $false);
}
*/





/*
********************************************************************************
****************************  Testing functionality  ***************************
********************************************************************************
*/



/**
*
*
*//*
public function testReadPreferredFollowers() {
	
Configure::write('debug', 2); 
$this->autoRender = false;

	$countryCode = "ES";

	$this->Preferredfollower = ClassRegistry::init('Preferredfollower');
	$result = $this->Preferredfollower->listPreferredFollowers($countryCode);
	$this->print_r2($result);
}
*/




/*
********************************************************************************
*****************  pre existing functionality from ZASTAC.COM  *****************
********************************************************************************
*/

/**
*	Define a hashed password
*
*/
function generatePassword($password) {
Configure::write('debug', 2);
$this->autoRender = false;

$this->layout = 'compare_public_layout';
	
echo "password = $password";
	$pw = new SimplePasswordHasher;
	$hashedPassword = $pw->hash($password);
echo "Clear password = $password and hashed password = $hashedPassword";
echo "<br/>";

}

/**not used
*
*	Collects new passwords from user after requesting a password reset
*
*//*
public function changePasswordOneTimePanel($linkToken) {
	$this->layout = 'zastac_public_login_layout';		// very simple login screen

	$this->UniqueLink = $this->Components->load('UniqueLink');
	$this->UniqueLink->initialize();
	$this->UniqueLink->validateUniqueLink($linkToken);

	$this->UniqueLink->revokeCurrentUniqueLinkToken($linkToken);
	$this->set('linkToken', $linkToken);
}
*/




/**not used
*
*	Executes the change of password
*
*//*
public function changePasswordOneTime() {
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	foreach($_REQUEST as $key => $value){
		$result[$key] = $_REQUEST[$key];
	}

	if ($result['password'] <> $result['password1']) {
		throw new
			FatalErrorException(__('Password Not Valid'));		
	}

	$conditions = array("AND" => array(array('User.user_linkToken' => $result['token']),
											));

	$resultUserData= $this->User->find("all", $params = array('recursive'		=>  -1,
																'conditions'	=> $conditions,
									));

	$this->User->create();
	$userData = array();
	$userData['User']['id'] = $resultUserData[0]['User']['id'];	
	$userData['User']['password'] = $result['password'];

	if (!$this->User->save($userData, $validate = true)) {
		throw new
			FatalErrorException(__('Password Not Valid'));			
	}
}	
*/
	
	
	
	
/**
*NOT TESTED YET
*	password change function
*	
*//*
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
*/




/**
*
*	User has forgotten his/her password and requests a new one.
*	"OK" will ALWAYS be returned to the browser
*
*//*
public function provideNewPassword() {
	Configure::write('debug', 2);
	App::uses('CakeTime', 'Utility');	
	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$userName = $_REQUEST['username'];				// i.e. email of the user

	$resultUserData = $this->User->find("first", $params = array('recursive'	=>  -1,
									'conditions'	=> array("username" => $userName),
                                            ));
        
	if (!empty($resultUserData)) {				// User exists
		$actualDateTime = date("Y-m-d H:i:s");
		$this->UniqueLink = $this->Components->load('UniqueLink');
		$this->UniqueLink->initialize($this);
		
		$userData = array();
		$userData['Uniquelink']['uniquelink_firstUsageTimestamp'] = time() - HOUR;
		$userData['Uniquelink']['uniquelink_lastUsageTimestamp'] = time() + DAY;
		$userData['Uniquelink']['uniquelink_leftUsage'] = 3;
		$userData['Uniquelink']['uniquelink_description'] = "Password Reset, User Requested";
		
		$linkToken = $this->UniqueLink->createLinkToken($userData);
		$this->User->save(array('id' => $resultUserData['User']['id'],
								'user_linkToken' => $linkToken));
	}
	// We don't do anything  if user does not exist, i.e. let request slowly die.
	sleep(3);
}
*/




/**
*
*	The  user requests a new password.
*
*//*
public function requestNewPasswordPanel() {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();
}
*/




/**MARK THE USER RECORD AS DELETED.
*
*
*
*/
function deleteUser() {
	
	
}





/*
public function cronDBbackup() {
	Configure::write('debug', 0); 
	$this->autoRender = false;
	$this->export_tables("localhost","root","8870mit","search");
}
*/








/** 
*
*	Download the data from the data tables and convert them to a csv format
*	account data and social network data
*THIS IS A *TEMPORARY* FIXED UNTILL A COMPLETE DATABASE STRUCTURE IS DEFINED.	
*	
*//*
public function cronAnalyzeUserDatas($startDateParm, $endDateParm) {
Configure::write('debug', 2);
$this->autoRender = false;

	echo APP . DS . 'Vendor' . DS . 'PHPExcel' . DS . 'PHPExcel.php';
	require_once(APP . DS . 'Vendor' . DS . 'PHPExcel' . DS . 'PHPExcel.php');

	$startDate = $startDateParm . " " . '00:00:01';		// Normalize to datebase DATETIME format, YYYY-MM-DD HH:MM:SS
	$endDate = $endDateParm . ' ' . '23:59:59';			// Normalize to datebase DATETIME format, YYYY-MM-DD HH:MM:SS
echo "startDate = $startDate and endDate = $endDate <br>";
	$this->Data = ClassRegistry::init('Data');
	$resultData = $this->Data->find("all", array("recursive" => -1,
                                                    'order' => array('Data.data_investorReference'), 
                                                "conditions" => array("created > " => $startDate,
                                                                        "created < " => $endDate),
                                        ));

// ResultData contains ALL entries between the mentioned dates. Duplicates will exist.
// Remove duplicates
	$this->print_r2($resultData);
// remove duplicates of a user, i.e. only take the last entry of each user
	$oldUserId = 0;
	$oldIndex = -1;
	
	foreach ($resultData as $key=> $item) {	
		$newUserId = $item['Data']['data_investorReference'];
		if ($newUserId == $oldUserId) {
			unset($resultData[$oldIndex]);		
		}
		$oldIndex = $key;
		$oldUserId = $newUserId;
	}

	$this->print_r2($resultData);

// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

// Set document properties
// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Users');

	$objPHPExcel->getProperties()->setCreator("Winvestify - Antoine de Poorter")
								 ->setLastModifiedBy("Winvestify - Antoine de Poorter")
								 ->setTitle("Office 2007 XLSX Document")
								 ->setSubject("Office 2007 XLSXDocument")
								 ->setDescription("File with user investment data")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("result file");

// Set header for first sheet
	$index = 1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $index, 'userId')
				->setCellValue('B' . $index, 'amountInvested')
				->setCellValue('C' . $index, 'wallet')
				->setCellValue('D' . $index, 'totalEarnedInterest')
				->setCellValue('E' . $index, 'profitibilityAccumulative')
				->setCellValue('F' . $index, 'totalInvestments')
				->setCellValue('G' . $index, 'activeInvestments')
				->setCellValue('H' . $index, 'meanProfitibility');
	
	foreach ($resultData as $item) {
		$index = $index + 1;
		$userId = $item['Data']['data_investorReference'];
		$tempResultData = json_decode($item['Data']['data_JSONdata'], true);
//$this->print_r2($tempResultData);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . $index, $userId)
					->setCellValue('B' . $index, $tempResultData['amountInvested'])
					->setCellValue('C' . $index, $tempResultData['wallet'])
					->setCellValue('D' . $index, $tempResultData['totalEarnedInterest'])
					->setCellValue('E' . $index, $tempResultData['profitibilityAccumulative'])
					->setCellValue('F' . $index, $tempResultData['totalInvestments'])
					->setCellValue('G' . $index, $tempResultData['activeInvestments'])
					->setCellValue('H' . $index, $tempResultData['meanProfitibility']);
	}

	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1);
	
// Set header for second sheet	
	$objPHPExcel->getActiveSheet()->setTitle('Investments');

	$index = 1;
	$objPHPExcel->setActiveSheetIndex(1)
				->setCellValue('A' . $index, 'userId')
				->setCellValue('B' . $index, 'company')
				->setCellValue('C' . $index, 'loanId')
				->setCellValue('D' . $index, 'name')
				->setCellValue('E' . $index, 'date')
				->setCellValue('F' . $index, 'duration')
				->setCellValue('G' . $index, 'interest')
				->setCellValue('H' . $index, 'invested')				
				->setCellValue('I' . $index, 'status');

	foreach ($resultData as $item) {
		$userId = $item['Data']['data_investorReference'];
		$tempResultData = json_decode($item['Data']['data_JSONdata'], true);
		foreach ($tempResultData['investments'] as $companyKey => $companyItem) {
			foreach ($companyItem['investments'] as $investment) {
				$index = $index + 1;
				$objPHPExcel->setActiveSheetIndex(1)
							->setCellValue('A' . $index, $userId)
							->setCellValue('B' . $index, $companyKey)
							->setCellValue('C' . $index, $investment['loanId'])
							->setCellValue('D' . $index, $investment['name'])
							->setCellValue('E' . $index, $investment['date'])
							->setCellValue('F' . $index, $investment['duration'])
							->setCellValue('G' . $index, $investment['interest'])
							->setCellValue('H' . $index, $investment['invested'])					
							->setCellValue('I' . $index, $investment['status']);		
			}
		}
	}	

// Rename third worksheet
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(2);
	$objPHPExcel->getActiveSheet()->setTitle('Companytotals');

	$index = 1;
	$objPHPExcel->setActiveSheetIndex(2)
				->setCellValue('A' . $index, 'userId')
				->setCellValue('B' . $index, 'company')
				->setCellValue('C' . $index, 'myWallet')
				->setCellValue('D' . $index, 'activeInInvestments')
				->setCellValue('E' . $index, 'totalEarnedInterest')
				->setCellValue('F' . $index, 'profitibility')
				->setCellValue('G' . $index, 'totalInvestment')
				->setCellValue('H' . $index, 'activeInvestments')				
				->setCellValue('I' . $index, 'investments');
	
	foreach ($resultData as $item) {
		$userId = $item['Data']['data_investorReference'];
		$tempResultData = json_decode($item['Data']['data_JSONdata'], true);
		foreach ($tempResultData['investments'] as $companyKey => $companyItem) {
			$this->print_r2($companyItem['global']);
			$index = $index + 1;
			$objPHPExcel->setActiveSheetIndex(2)
						->setCellValue('A' . $index, $userId)
						->setCellValue('B' . $index, $companyKey)
						->setCellValue('C' . $index, $companyItem['global']['myWallet'])
						->setCellValue('D' . $index, $companyItem['global']['totalEarnedInterest'])		
						->setCellValue('E' . $index, $companyItem['global']['totalAmortized'])
						->setCellValue('F' . $index, $companyItem['global']['totalInvestment'])
						->setCellValue('G' . $index, $companyItem['global']['totalPercentage'])
						->setCellValue('H' . $index, $companyItem['global']['activeInInvestments'])					
						->setCellValue('I' . $index, $companyItem['global']['investments'])
						->setCellValue('J' . $index, $companyItem['global']['profitibility']);
		}
	}	

// Set active sheet index to the first
//   sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$backupDir = Configure::read('companySpecificPhpCodeBaseDir') . 'tracings' . DS;
        
	$filename = $startDateParm ."_" . $endDateParm . '.xls';
        echo "FILENAME = " . $backupDir . $filename;
	$objWriter->save($backupDir . $filename);
}
*/



    /** 
     * Calculates a new access JWT or a new refresh JWT 
     *
     * @param array $userData array with all the (relevant) userdata required for generating
     * the JWT
     * @param int   $typeOfToken Value = WIN_ACCESS_TOKEN or WIN_REFRESH_TOKEN
     * @param string $refreshToken The token used to generate a new access token
     * @return string $token The generated JSON Webtoken
     */
    public function getNewJWT($userData, $typeOfToken, $refreshToken = NULL) {
   
        $initialMenuData = $this->getSectorsByRole($roleId = $userData['Role']['id']);

        foreach($initialMenuData as $item) {
            $tempData['icon'] = $item['Sector']['sectors_icon'];
            $tempData['href'] = $item['Sector']['sectors_href']; 
            $tempData['display_name'] = $item['Sector']['sectors_name']; 
            $tempData['initial_status'] = $item['Sector']['sectors_initialStatus'];
            $menuData[] = $tempData;
        }

        $payload['iat'] = time();
        $payload['exp'] = $payload['iat'] + WIN_JWT_DURATION;
        $payload['iss'] = "www.winvestify.com";  
        $payload['sub'] = $userData['Investor']['id'];
        $payload['menu_options'] = $menuData;
        $payload['language'] = $userData['Investor']['investor_language'];
        $payload['role'] = $userData['Role']['role_name'];                    
        $payload['pmessage'] = false; 

        $payload['endpoints'] = 888;   
        
        if ($typeOfToken == WIN_ACCESS_TOKEN) {
            $payload['refresh_token'] = $this->User->api_getNewToken($userData['id']);
            $payload['account_display_name'] = $userData['Investor']['investor_name'] . " " . $userData['Investor']['investor_surname']; 
        } 
        else {      // type = WIN_REFRESH_TOKEN
            $payload['refresh_token'] = $this->User->api_getNewAccessToken($refreshToken);
            $payload['account_display_name'] = $this->accountDisplayName;
        }
   
        $token = JWT::encode($payload, Configure::read('Security.salt')); 
        return $token;
    }


    /**
     * 
     * Check if a proposed username already exists in the system
     * This methods terminates the HTTP POST for actions
     * Format POST /api/1.0/users/pre-check
     * 
     * @return boolean
     */
    public function v1_precheck() {
        $data = $this->listOfQueryParams;
        if (!empty($id)) {
            $data['id'] = $id;              //?????? not required in this context
        }

        if (!$this->User->api_usernameExists($this->listOfQueryParams['username'])) { 
            $apiResult = ['result' => false];
        }
        else {
            $apiResult = ['result' => true]; 
        }
        
        $this->response->statusCode(200);      
        $resultJson = json_encode($apiResult);
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;         
    }    
    
  
 
    /** PENDING: NOT FINISHED, AND ERROR HANDLING TOWARDS HTTP
     * This methods terminates the HTTP POST.
     * Deal with error of missing data
     * Format POST /api/1.0/investors.json
     * 
     * @param -
     *//*
    public function v1_add() { 

        echo __FILE__ . " " . __LINE__ . "\n";    
        $this->print_r2($this->listOfFields);  

        echo __FILE__ . " " . __LINE__ . "\n";  
        $this->print_r2($this->listOfQueryParams);     

        echo __FILE__ . " " . __LINE__ . "\n";
        $this->print_r2($this->request->data);
        
        if ($this->Investor->save($this->listOfQueryParams, $validate = true)) {
            $apiResult['investor']['id'] = $this->Investor->id;    
            $resultJson = json_encode($apiResult); 
var_dump($apiResult); 
            $this->response->statusCode(201);              
        }
        else {
            $validationErrors = $this->Investor->validationErrors;
            $this->Investor->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('USER_NOT_CREATED', 
                                                        "User could not be created. More detailed information available", 
                                                        $validationErrors);
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(403);                                       // 403 Forbidden              
        }

        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;       
    } */
 

    /**
     *	
     * logout of the user
     *
     */
    public function logout() {
    
        $this->User->api_logout($this->data['refresh-token']);
        $this->response->statusCode(200);         
        return $this->response;
    }    

    
    /**
     *	
     *  a new access token for a user
     * 
     * @param string $refreshToken The token to use for generating a new token
     */
    public function refreshtoken() {
        // Collect the relevant user data for JWT generation 

        $this->Role = ClassRegistry::init('Role');
        $userData['Role']['role_name'] = $this->roleName;        
        $userData['Role']['id'] = $this->Role->translateRoleName2RoleId($this->roleName);
        $userData['Investor']['investor_language'] = $this->language;
        $userData['Investor']['id'] = $this->investorId;
        $userData['Investor']['investor_name'] = $this->accountDisplayName;

        $token = $this->getNewJWT($userData, WIN_REFRESH_TOKEN, $this->refreshToken);
          
        if (!empty($token)) {
            $result['token'] = $token;  
            $resultJson = json_encode($result);
            $this->response->statusCode(200);         
            $this->response->type('json');
            $this->response->body($resultJson);         
            return $this->response;
        }
        else {
            throw new UnauthorizedException('Authentication error');  
        }
    }   
    
    
    
    
 }     
    