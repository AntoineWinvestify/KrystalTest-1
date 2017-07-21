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
 * @version 0.2
 * @date 2017-06-11
 * @package
 *
 *
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 *
 *
 * App Controller
 *

  2017-06-11      version 0.2
  Corrected test for language cookie


  2017-06-14      version 0.21
  loginRedirect has changed to global market place


 * 2017-06-11      version 0.2
 * Corrected test for language cookie
 *
 * 
 * 2017-06-19      version 0.22
  Added a new crowdlending type and defined its "string" values globally.
  Added type of dashboard record


2017-06-11      version 0.2
Corrected test for language cookie 


2017-06-14      version 0.21
loginRedirect has changed to global market place 


2017-06-19      version 0.22
Added a new crowdlending type and defined its "string" values globally.
Added type of dashboard record
 
2017-06-22      version 0.23
Added isAuthorized function to verify if a determinated role has access
  
2017-06-27      version 0.24  
Added new function to get sectors of the leftnavigationmenu by role



 * 2017-06-23     version 0.3
 * OCr status defined
 * 
 * [2017-06-28] Version 0.4
 * Defined currencyName
 * Defined pfpStatus
 * 
 * [2017-06-29] Version 0.5
 * Defined new status for ocr and files 
 * 
 * [2017-07-03] Version 0.6
 * Defined checks
 * 
 * [2017-07-14] Version 0.7
 * Files type defined
 * 
 * 
 *  PENDING:
 * -
 *
 *
 *
 */

App::uses('Controller', 'Controller');


//Global constants for all the controllers, models and views

define("NOT_ACTIVE", 0);
define("ACTIVE", 1);
define("HOUR", 4);
define("DAY", 1);
define('MONTH', 2);
define('YEAR_CUARTER', 3);
define('TRUE', 1);
define('FALSE', 0);
define('LOAN_TO_PRIVATE_PERSON', 1);
define('LOAN_TO_COMPANY', 2);
define('FINANCING_TO_PRIVATE_PERSON', 4);
define('FINANCING_TO_COMPANY', 8);
define('ALLOW_LINKED_ACCOUNTS', 16);


// Sequences
define('LOGIN_SEQUENCE', 1);
define('LOGOUT_SEQUENCE', 2);
define('MARKETPLACE_SEQUENCE', 3);
define('MY_INVESTMENTS_SEQUENCE', 4);
define('MY_VIRTUAL_WALLET_SEQUENCE', 5);

define('CHAT_ACTIVE', 1);
define('CHAT_TEMPORARY_NOT_ACTIVE', 2);
define('CHAT_NOT_ACTIVE', 3);
define('CHAT_INACTIVITY', 4);


// type of financial product
define('PAGARE', 1);
define('LOAN', 2);
define('FINANCING', 3);


// account definition progress	
define('UNCONFIRMED_ACCOUNT', 1);
define('CONFIRMED_ACCOUNT_WITH_DEFAULT_DATA', 2);
define('FOLLOWERS_DEFINED', 4);
define('QUESTIONAIRE_FILLED_OUT', 32);  // represents from here that the account is fully created
define('NEW_DEFAULT_PERSONAL_DATA', 128);
define('NEW_DEFAULT_INVESTMENT_DATA', 256);
define('NEW_DEFAULT_FOLLOWER_DATA', 512);


// metrics	
define('TOTAL_NUMBER_OF_USERS', 1);
define('TOTAL_NUMBER_OF_ACTIVE_USERS', 2);
define('TOTAL_NUMBER_OF_USERS_WITH_LINKED_ACCOUNTS', 3);
define('NUMBER_OF_INVESTMENTS_PER_LINKED_ACCOUNT', 4);
define('TOTAL_NUMBER_OF_INVESTMENTS_PER_USER', 5);
define('NUMBER_OF_LINKED_ACCOUNTS_PER_USER', 6);
define('TOTAL_AMOUNT_INVESTED_PER_USER', 7);


// type of counters
define('ACCUMULATIVE_COUNTER', 1);
define('DELTA_COUNTER', 2);
define('LEVEL_COUNTER', 3);


// queue types
define('FIFO', 1);
define('LIFO', 2);


// queue states
define('IDLE', 1);
define('WAITING_FOR_EXECUTION', 2);
define('EXECUTING', 3);
define('FINISHED', 4);


// notifications states
define('WAITING_FOR_VISUALIZATION', 1);
define('READY_FOR_VISUALIZATION', 2);
define('READ_BY_USER', 3);
define('DELETED', 4);



// MAPPING OF 'PAYMENT TRANSACTION' STATE OF LOAN ACCORDING TO AMORTIZATION TABLE
define('TERMINATED_OK', -1); // Investment has been successfully amortized according to predefined payment schedule
define('PENDING', 0);   // First repayment has not yet occured as repayment date is in future
define('OK', 1);    // Investment is being repayed according to predefined payment schedule
define('PAYMENT_DELAYED', 2); // Investment repayments is BEHIND repayment schedule
define('DEFAULTED', 3);   // User has defaulted on the loan and will NOT repay the full amount of the loan.
// Investment must be considered LOST
// DEFINITION OF WORK SECTORS				 // NOT ACTUALLY USED
define('EDUCATION', 110);
define('HEALTH_CARE', 120);
define('CIVIL_SERVANT', 150);
define('SOCIAL_SERVICES', 160);
define('ICT', 220);
define('AGRICULTURE', 311);
define('CONSTRUCTION', 323);
define('TURISM', 332);

define('ACCREDITED_INVESTOR', 2);
define('NOT_ACCREDITED_INVESTOR', 1);


// DEFINITION OF TYPE OF CROWDLENDING M0DALITIES
define('P2P', 1);
define('P2B', 2);
define('INVOICE_TRADING', 4);
define('CROWD_REAL_ESTATE', 8);
define('SOCIAL', 16);


// REGISTRATION PROGRESS WHEN USERS REGISTERS	
define('REGISTRATION_PROGRESS_1', 1);
define('REGISTRATION_PROGRESS_2', 2);
define('REGISTRATION_PROGRESS_3', 3);
define('REGISTRATION_PROGRESS_4', 4);
define('REGISTRATION_PROGRESS_5', 5);


//COMPANY SERVICE STATUS
define('SER_INACTIVE', 1);
define('SER_ACTIVE', 2);
define('SER_SUSPENDED', 3);

//OCR STATUS
define('NOT_SENT', 0);
define('SENT', 1);
define('ERROR', 2);
define('OCR_PENDING', 3);
define('OCR_FINISHED', 4);
define('FIXED', 5);

//OCR COMPANY STATUS
define('SELECTED', 0);
define('SENT', 1);
define('ACCEPTED', 2);
define('DENIED', 3);
define('DOWNLOADED', 4);

//CHECK DATA & FILES STATUS
define('UNCHECKED', 0);
define('CHECKED', 1);
define('ERROR', 2);

//FILE OPTIONAL OR REQUIRED
define('OPTIONAL', 1);
define('REQUIRED', 0);

//CHECK STATUS(for winadmin invcestor data)
define('YES', 1);
define('NO', 2);
define('PENDING', 0);


// CURL ERRORS
define('CURL_ERROR_TIMEOUT', 28);

// TYPES OF DASHBOARD RECORDS	
define('USER_GENERATED', 2);
define('SYSTEM_GENERATED', 1);


// DEFINED CURRENCIES
define('EUR', 1);           // Euro
define('GBP', 2);           // UK Pound Sterling
define('USD', 3);           // US Dollar

// APPLICATION THAT CAN PRODUCE BILLING DATA
define('TALLYMAN_APP', 1);

//DOCUMENT TYPE(Files table)
define('DNI_FRONT', 1);
define('DNI_BACK', 2);
define('IBAN', 3);
define('CIF', 4);

class AppController extends Controller {

    public $components = array('DebugKit.Toolbar',
        'RequestHandler',
        'Security',
        'Session',
        'Acl',
        'Auth' => array(
            /* 				'authorize' 	=> 'Controller', isAuthorized method not implemented in controller */
            'loginRedirect' => array('controller' => 'marketplaces',
                'action' => 'showMarketPlace'
            ),
            'logoutRedirect' => array('controller' => 'marketplaces',
                'action' => 'getGlobalMarketPlaceData'
            ),
        ),
        'Cookie',
    );
    var $uses = array('User', 'Role', 'Sector');

    /**
     * 	This code is common to all the classes that actively define a method for the beforeFilter
     * 	callback.
     * 	It includes:
     * 		name of cookie
     * 		identify if mobile of desktop layout is to be used.???
     */
    public function beforeFilter() {
        $this->Cookie->name = 'p2pManager';
        $this->Cookie->time = 3600;  // or '1 hour'
        $this->Cookie->secure = false;  // i.e. only sent if using secure HTTPS
        $this->Cookie->key = 'qSI232qs*&sXOw!adre@34SAv!@*(XSL#$%)asGb$@11~_+!@#HKis~#^';
        $this->Cookie->httpOnly = true;
        $this->Cookie->type('rijndael');

        $this->Security->blackHoleCallback = '_blackHole';
        $this->Security->requireSecure();

// Load the application configuration file. Now it is available to the whole application	 
        Configure::load('p2pGestor.php', 'default');

        $durationPublic = array(0 => "Undefined",
            1 => "Días",
            2 => "Meses",
            3 => "Trimestre",
            4 => "Horas",
        );

        // TRANSLATE CURRENCY NAME
        $this->currencyName = array(0 => "(select)", 1 => "€", 2 => "£", 3 => "$");

        //Investor Status to PFP Admin
        $this->pfpStatus = array(2 => __("New"), 4 => __("Viewed"));

        //Investor Ocr Status
        $this->ocrStatus = array(1 => __("New"), 2 => __("Error"), 3 => __("Pending"), 4 => __("Finished"), 5 => __("Fixed"));

        //Company ocr service status
        $this->serviceStatus = array(0 => __('Choose One'), 1 => __("Inactive"), 2 => __("Active"), 3 => __("Suspended"));

        $this->set('durationPublic', $durationPublic);
        $this->durationPublic = $durationPublic;

        $this->crowdlendingTypesLong = array(
            P2P => __('P2P Crowdlending'),
            P2B => __('P2B Crowdlending'),
            INVOICE_TRADING => __('P2P Invoice Trading'),
            CROWD_REAL_ESTATE => __('Crowd Real Estate'),
            SOCIAL => __('Social')
        );
        $this->set('crowdlendingTypesLong', $this->crowdlendingTypesLong);


        $this->crowdlendingTypesShort = array(
            P2P => __('P2P'),
            P2B => __('P2B'),
            INVOICE_TRADING => __('I.T.'),
            CROWD_REAL_ESTATE => __('R.E.'),
            SOCIAL => __('SOCIAL')
        );
        $this->set('crowdlendingTypesShort', $this->crowdlendingTypesShort);

        if (!$this->Cookie->check('p2pManager.language')) {        // first time that the user visits our Web
            $languages = $this->request->acceptLanguage();       // Array, something like     [0] => en-us [1] => es [2] => en
            $ourLanguage = explode('-', $languages[0]);        // in this case will be "en"
            $this->Cookie->write('p2pManager', array('language' => $ourLanguage[0]));
        } else {
            $ourLanguage[0] = $this->Cookie->read('p2pManager.language');
        }
        $this->Session->write('Config.language', $ourLanguage[0]);

        $subjectContactForm = array('Choose one...',
            'general' => __('General'),
            'billing' => __('Billing Dept'),
            'improvement' => __('Functional Improvement'),
            'feature' => __('New Feature'));
        $this->set('subjectContactForm', $subjectContactForm);

        //$this->documentTypes = array ('dni_front' => 1, 'dni_back' => 2, 'iban' => 3, 'cif' => 4);
        //$this->set('documentTypes', $this->documentTypes);

        $filterCompanies1 = array(__('Country filter'), 'Spain' => __('Spain'), 'Italy' => __('Italy'));
        $filterCompanies2 = array(__('Type filter'), 'P2P (Peer-to-Peer)' => __('P2P (Peer-to-Peer)'));
        $this->set('filterCompanies1', $filterCompanies1);
        $this->set('filterCompanies2', $filterCompanies2);

        //Use $this->params['controller'] to get the current controller.
        //Use $this->action to verify the current controller/action
        if ($this->Auth->user()) {

            $action = $this->action;
            $controller = $this->params['controller'];
            $action2 = $this->params['action'];
            //Here we verify if this user has authorization to acces the controller and the action
            $resultAcl = $this->isAuthorized($controller,$action);
            if (!$resultAcl) {
                //In contructions, we use this now before we create a error page
                throw new
                            FatalErrorException(__('You cannot access this page directly'));
            }
        }
        if ($this->Auth->user()) {
            $sectorExist = $this->Session->read('sectorsMenu');
            if (empty($sectorExist)) {
                $roleId = $this->Auth->User('role_id');
                /*$this->Role = ClassRegistry::init('Role');
                $sectors = $this->Role->getSectorsByRole($roleId);*/
                $sectors = $this->getSectorsByRole($roleId);
                $this->Session->write('sectorsMenu', $sectors);
            }
            $this->set('sectorsMenu', $this->Session->read('sectorsMenu'));
        }
       
        
        
    }

    /**
     *
     * 	Creates a new instance of class with name company, like zank, or comunitae....
     *
     * 	@param 		int 	$companyCodeFile		Name of "company"
     * 	@return 	object 	instance of class "company"
     *
     */
    function companyClass($companyCodeFile) {

        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
        return $newComp;
    }

    function print_r2($val) {
        echo '<pre>';
        print_r($val);
        echo '</pre>';
    }

    /**
     * 	Redirect an action to using https
     *
     */
    function _blackHole($type) {

//	$this->redirect('https://' . env('SERVER_NAME') . env('REQUEST_URI'));
    }

    /**
     * 
     * 	Check if a refresh of the investor's investment information (= his Dashboard) is required
     *
     */
    function checkUserInvestmentData() {
        $this->Investor = ClassRegistry::init('Investor');
        $this->Queue = ClassRegistry::init('Queue');

        $globalInvestorReference = $this->Session->read('Auth.User.Investor.investor_identity');

// Do this ONLY for authenticated sessions.
        if (empty($globalInvestorReference)) {
            return;
        }

        if ($this->Queue->checkQueue($globalInvestorReference)) {  // a request already exists in the queue
            return;
        }

        if ($this->Investor->investmentInformationUpdate($this->Session->read('Auth.User.investor_id')) == true) {
            $this->Queue->addToQueue($globalInvestorReference, FIFO, "/marketplaces/getXYZdata");
        }
    }

    /**
     * 	Redirect an action to using http
     *
     */
    function _notblackHole() {
        $this->redirect('http://' . env('SERVER_NAME') . env('REQUEST_URI'));
    }

    /**
     * 	returns the oldest file of a certain filetype , according to its timestamp,  
     * 	in a directory

     */
    function getOldestFile($dir, $filetype) {
        $directory = $dir . "/*." . $filetype;
        $files = glob($directory);
        array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_ASC, $files);
        return $files[0];
    }

    public function session() {
        $this->autoRender = FALSE;

        $test1 = "apple " . "peer";
        echo $test1;
        echo "Now = : " . date('Y-m-d H:i:s', strtotime(now)) . "<br>";
        echo '5 minutes ago = : ' . date('Y-m-d h:i:s', strtotime('- 5 minutes')) . "<br><br>";

        echo "AAAN" . $this->request->domain() . "<br>";

        echo __FILE__ . " " . __LINE__ . "<br>";
        $languages = $this->request->acceptLanguage();
        echo __FILE__ . " " . __LINE__ . "<br>";
        $this->print_r2($languages);

        echo __FILE__ . " " . __LINE__ . "<br>";
        $this->print_r2($this->Session->read('Config.language'));
        $this->print_r2($this->Session->read());

//	$this->Session->delete('Config.language');
    }

    /** DOES NOT WORK WITH $x=1
     * 	Round up to an integer, then to the nearest multiple of 5
     * 	Behaviour: 50 outputs 50, 52 outputs 55, 50.25 outputs 55
      http://stackoverflow.com/questions/4133859/round-up-to-nearest-multiple-of-five-in-php
     */
    function roundUpToAny($n, $x = 5) {
        return (ceil($n) % $x === 0) ? round($n) : round(($n + $x / 2) / $x) * $x;
    }

    /**
     * 	Merge two arrays with overlapping indexes into a new one
     * 	The indexes MUST be consecutive
     */
    function merge_arrays($array1, $array2) {
        $count_old = count($array1);
        $count = count($array2);
        for ($i = 0; $i < $count; $i++) {
            $array1[$count_old + $i] = $array2[$i];
//		unset( $array1[$i]);
        }
        return ($array1);
    }

    /**
     *
     * 	Determines the FQDN of the directory where the user files are (to be ) stored.
     * 	Returned directory name does NOT end with a "/"
     *
     * @param int 		$userId
     * @return string	$directory
     *
     */
    public function getUserFileDirectory($userId) {
        $dir = Configure::read('directory_user_files');

        $userDir = rtrim($dir, "/");
        if ($userDir[0] != "/") {
            $userDirFQDN = APP . $userDir;
        } else {
            $userDirFQDN = $userDir;
        }

        $temp = (int) ($userId / 1000);      // forget about the decimal part
        $normTemp = str_pad($temp, 4, '0', STR_PAD_LEFT);    // normalize it to 4 digits
        $directory = $userDirFQDN . DS . $normTemp;
        return $directory;
    }

    function deleteDir($dir) {
        $iterator = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }

// ***********  TESTED, EXTERNAL CODE  ************

    /**
     *
     * 	borrowed from "http://guid.us/"
     * 	Generates a GUID
     *
     *
     */
    public function getGUID() {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000);    //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);       // "-"
            $uuid = chr(123)       // "{"
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125); // "}"
            return $uuid;
        }
    }

    /** LOGGING IS NOT WORKING YET --> Delete this
     *
     * 	Get the location data of the user
     *
     */
    /* public function getLocationData111() {

      $curl = curl_init();
      if (!$curl) {
      $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
      $msg = $msg . " \n";
      return "";
      }
      $url = "http://icanhazip.com";
      // Set the file URL to fetch through cURL
      curl_setopt($curl, CURLOPT_URL, $url);

      // Set a different user agent string (Googlebot)
      curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');

      // Follow redirects, if any
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

      // Fail the cURL request if response code = 400 (like 404 errors)
      curl_setopt($curl, CURLOPT_FAILONERROR, true);

      // Return the actual result of the curl result instead of success code
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

      // Wait for 10 seconds to connect, set 0 to wait indefinitely
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);

      // Execute the cURL request for a maximum of 50 seconds
      curl_setopt($curl, CURLOPT_TIMEOUT, 50);

      // Do not check the SSL certificates
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies1.txt');  // important
      curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies1.txt');  // Important
      // Fetch the URL and save the content
      $ip = trim(curl_exec($curl));
      $url = "http://freegeoip.net/json/" . $ip;


      // Set the file URL to fetch through cURL
      curl_setopt($curl, CURLOPT_URL, $url);

      // Fetch the URL and save the content
      $str = curl_exec($curl);
      curl_close($curl);

      $this->print_r2(json_decode($str, true));
      return json_decode($str, true);
      } */

    /**
     *
     * 	Get the geographical location data of the user
     *
     */
    public function getGeoLocationData($ip) {

        $curl = curl_init();
        if (!$curl) {
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            return "";
        }

        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 20 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);

        // Execute the cURL request for a maximum of 30 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies1.txt');  // important
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies1.txt');  // Important
        // Fetch the URL and save the content

        $url = "http://freegeoip.net/json/" . $ip;
        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Fetch the URL and save the content
        $str = curl_exec($curl);
        curl_close($curl);

        $geoData = json_decode($str, true);

// Also store it in the Session
        foreach ($geoData as $key => $element) {
            $this->Session->write("locationData." . $key, $element);
        }
        return $geoData;
    }

    /**
     * Function to verify is an user has access to the controller or function
     * @param string $controller It is the route to the controller
     * @param string $access It is the access that the user has
     * @return boolean It is the access, it can be true or false
     */
    function isAuthorized($controller, $access = '*') {
	//$userId = $this->Auth->user('id');
	$aro = $this->Auth->user('Role.id');
        // Get internal database reference of the investor
        //$this->Role = ClassRegistry::init('Role');
        //$aro = $this->Role->getRoleNameById($roleId);
        
	return $this->Acl->check($aro, $controller, $access);
    }
    
    /**
     * Function to get the sectors for the leftnavigationmenu by User's role
     * We do a three table query using the joins option
     * @param int $roleId It is the user's role id
     * @return boolean|array Return false if there is not roleId or the array with the sectors
     */
    function getSectorsByRole($roleId = null){
        if (empty($roleId)) {
            return false;
        }
        $this->Sector = ClassRegistry::init('Sector');
        $options['joins'] = array(
            array('table' => 'roles_sectors',
                'alias' => 'RolesSector',
                'type' => 'inner',
                'conditions' => array(
                    'Sector.id = RolesSector.sector_id'
                )
            ),
            array('table' => 'roles',
                'alias' => 'Role',
                'type' => 'inner',
                'conditions' => array(
                    'RolesSector.role_id = Role.id'
                )
            )
        );

        $options['conditions'] = array(
            'Role.id' => $roleId
        );
        //$options['field'] = array('Sector.*');
        $options['recursive'] = -1;
        $options['order'] = array(
            'Sector.sectors_father',
            'Sector.sectors_subSectorSequence'
        );

        $sectors = $this->Sector->find('all', $options);
        return $sectors;
    }

}
