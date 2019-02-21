<?php
/**
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
addInvestorToSearchCriteria, checkFields, checkOwner, setListOfFields

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
 * 2017/08/17
 * All constant moved.
 * 
 *  PENDING:
 * -
 *
 *
 *
 */
App::uses('Controller', 'Controller');
App::import('Vendor', 'Firebase', array('file' => 'firebase' . DS . 'php-jwt' . DS .'src' . DS . 'JWT.php'));
class AppController extends Controller {

    protected $listOfFields;                    // Array that holds the list of requested fields
                                                // in normalized, internal DB format
    protected $listOfQueryParams;               // Array that holds the list of Query parameters
                                                // in normalized, internal DB format
                                                // An item in this array can be another array. In
                                                // that case all entries are considered as OR condition.
                                                // This is a list as received from the webclient
    protected $filterConditionQueryParms;       // Query parms converted to MySQL filterconditions
    protected $listOfWriteFields;               // the list of variables which will be written during a
                                                // PUT or PATCH message
    protected $action;                          // The 'action' of a POST operation
    protected $investorId;                      // The investorId as obtained in the JWT
    protected $roleName;                        // The name of the role assigned to the user. Obtainted from JWT
    protected $refreshToken;                    // The token required for refreshing, as obtainted from JWT
    protected $accountDisplayName;              // The display name of the user as obtained frm JWT
    protected $language;                        // Language for translations.  We get it from the token

    
    
    public $components = array('DebugKit.Toolbar',
        'RequestHandler',
  //      'Security',
        
         'Auth' => array(
            'authenticate' => array(
                'Form' => array(
                    'fields' => array(
                        'username' => 'username',
                        'password' => 'password'
                    ),
                    'userModel' => 'User',
                    'scope' => array(
                        'User.active' => 1,
                    )
                ),
                'BzUtils.JwtToken' => array(
                    'fields' => array(
                        'username' => 'username',
                        'password' => 'password',
                    ),
                    'header' => 'Authorization',
                    'userModel' => 'User',
                    'scope' => array(
                        'User.active' => 1
                    )
                )
            )  
        ),       
      
       'Acl',
   /*     'Auth' => array(
            //				'authorize' 	=> 'Controller', isAuthorized method not implemented in controller 
            'loginRedirect' => array('controller' => 'marketplaces',
                'action' => 'showMarketPlace'
            ),
            'logoutRedirect' => array('controller' => 'users',
                'action' => 'loginRedirect'
            ),
        ),*/ 
    );
    
    var $uses = array('User', 'Role', 'Sector');

    
    
    /**
     * 	This code is common to all the classes that actively define a method for the beforeFilter
     * 	callback.
     */
    public function beforeFilter() {
 
        if (Configure::read('debug')) {
            $this->print_r2($this->request);
        } 
     
        $tokenRough = $this->request->header('Authorization');   
        $token1 = explode(" ", $tokenRough);
        ((!empty($token1[1])) ? $jwt = $token1[1] : $jwt = $token1[0]);
        
        if (!empty($jwt)) {
            $tokenObject = JWT::decode($jwt, Configure::read('Security.salt'), $allowed_algs = ['HS256']);      
            $this->investorId = $tokenObject->sub;   
            $this->roleName = $tokenObject->role;  
            $this->language = $tokenObject->language;
            $this->refreshToken = $tokenObject->refresh_token;
            $this->accountDisplayName = $tokenObject->account_display_name; 
//var_dump($tokenObject);            
        }
  
// Load the application configuration file. Now it is available to the *whole* application	 
/*        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'winVestify.php');
        $runtime = new Winvestify();
        $this->runTimeParameters = $runtime->readRunTimeParameters();
        $this->set('runTimeParameters', $this->runTimeParameters);

        $durationPublic = array(0 => "Undefined",
            1 => "Días",
            2 => "Meses",
            3 => "Trimestre",
            4 => "Horas",
        );

        // TRANSLATE CURRENCY NAME
        $this->currencyName = array(EUR => "€",
            GBP => "£",
            USD => "$",
            ARS => "$",
            AUD => "$",
            NZD => "$",
            BYN => "BR",
            BGN => "лв",
            CZK => "Kč",
            DKK => "Kr",
            CHF => "Fr",
            MXN => "$",
            RUB => "₽",
        );


        //Investor Status to PFP Admin
        $this->pfpStatus = array(2 => __("New"), 4 => __("Viewed"));

        //Investor Ocr Status
        $this->ocrStatus = array(1 => __("New"), 2 => __("Error"), 3 => __("Pending"), 4 => __("Finished"), 5 => __("Fixed"));

        //Company ocr service status
        $this->serviceStatus = array(0 => __('Choose One'), 1 => __("Inactive"), 2 => __("Active"), 3 => __("Suspended"));


        //Investment Status in marketplace
        $this->marketplaceStatus = array(0 => __('Choose One'), 1 => __("Status 1"), 2 => __("Status 2"), 3 => __("Status 3"));


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
            'feature' => __('New Feature'),
            'unsubscribe' => __('Unsubscribe Account'));
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
            //Here we verify if this user has authorization to acces the controller and the action
            $resultAcl = $this->isAuthorized($controller, $action);
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
                // $this->Role = ClassRegistry::init('Role');
                //  $sectors = $this->Role->getSectorsByRole($roleId); 
                $sectors = $this->getSectorsByRole($roleId);
                $this->Session->write('sectorsMenu', $sectors);
            }
        }

        switch ($this->runTimeParameters['runtimeconfiguration_executionEnvironment']) {
            case WIN_LOCAL_TEST_ENVIRONMENT:
            case WIN_REMOTE_TEST_ENVIRONMENT:
     //           rename($fileName, $fileName1);
            case WIN_LIVE_ENVIRONMENT:
   //            rename ($fileName1, $fileName);       
            default:
        }  
  */       
        $result = $this->loadParameterFields();                                      // Extract parameters from HTTP message 
 
    // Is the user authorized to access the requested resource?
        $inflectorInstance = new Inflector();
        $model = $inflectorInstance->singularize(ucfirst($this->request->params['controller']));
        if (empty($this->roleName)) {
            $this->roleName = "nobody";          // Access without authentication
        }
  
        if (!empty($this->request->params['acl_action'])) {
            $methodAction = $this->request->params['acl_action'];    
        }
        else {
            $methodAction = $this->request->params['[method]'];
        }
        
        if ($this->checkAcl($model, $this->roleName, $methodAction) == false) { 
           // throw new ForbiddenException('You are not allowed to access the requested resource'); 
            echo __FILE__ . " " . __LINE__ . " NOT AUTHORIZED<br>";             // remove this code in production
            exit;                                                               // remove this code in production
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
 //   function _blackHole($type) {

//	$this->redirect('https://' . env('SERVER_NAME') . env('REQUEST_URI'));
  //  }

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
/*
    public function session() {
        $this->autoRender = FALSE;
        Configure::write('debug', 2);
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
    }*/

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

    /**
     *
     * 	Get the geographical location data of the user
     *
     */
    public function getGeoLocationData($ip) {
        $authKey = $this->runTimeParameters['runtimeconfiguration_geoLocationAuthKey'];

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
     * Function to verify if a user has access to the controller or function
     * @param string $controller It is the route to the controller
     * @param string $access It is the access that the user has
     * @return boolean It is the access, it can be true or false
     */
    function isAuthorized($controller, $access = '*') {
        $aro = $this->Auth->user('Role.id');
        return $this->Acl->check($aro, $controller, $access);
    }

    /**
     * Function to get the sectors for the leftnavigationmenu by User's role
     * We do a three table query using the joins option
     * 
     * @param int $roleId It is the user's role id
     * @return boolean|array Return false if there is not roleId or the array with the sectors
     */
    function getSectorsByRole($roleId = null) {
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

    /**
     * 
     * This function check if the user request fulfill the permissions assigned to his role.
     * If is true, the petition is done without problems, if we have at least a data that doesn't fulfill the permissions,
     * we stop the petition and return an error.
     * @param int $request The type of request, can be read, write, delete, ....
     * @param array $data   The data to check.
     * @param string $role  The role of the user.
     * @return boolean
     */
    public function api_accessFilter($request, $data, $role){
    //Filter json
    return true;
    }
  
    
    /**
     * Formats the error information into the error object for the API-V1
     * 
     * @param string $errorName Short one word description of error
     * @param string $errorMessage The message in clear language which may be displayed to the user
     * @param array $validationErrors This is an array with all the error messages per variable 
     * @return array 
     */   
    public function createErrorFormat($errorName, $errorMessage, $validationErrors){      
        if (!empty($validationErrors)) {
            foreach ($validationErrors as $key => $item) {
                $tempArray['field'] = $key;
                $tempArray['issue'] = $item[0];
                $errorDetails[] = $tempArray;
            }
        }
        $errorArray['error_name'] = $errorName;
        $errorArray['error_message'] = $errorMessage;
        if (!empty($validationErrors)) {
            $errorArray['error_details'] = $errorDetails;
        }
        return ($errorArray);    
    }
    
    
    /**
     * Loads the class variables $listOfFields, $listOfQueryParams, $action
     * and the query parameters ($filterConditionQueryParms) converted to 
     * CakePHP Filtering Conditions for Model operations
     * 
     * @param - 
     * @return true
     */   
    public function  loadParameterFields(){ 
        $this->AppModel = ClassRegistry::init('AppModel');
        $this->listOfQueryParams = $this->request->query; 

        if (array_key_exists('_fields', $this->listOfQueryParams )){              
            $this->listOfFields = explode(",", $this->listOfQueryParams['_fields']);
            $this->AppModel->apiFieldListAdapter($this->listOfFields);         
            unset($this->listOfQueryParams['_fields']);
        }
       
        $this->AppModel->apiVariableNameInAdapter($this->listOfQueryParams);

        foreach($this->listOfQueryParams as $key => $value) {
            $parms = explode(",", $value); 

            if (count($parms) > 1) {
                $this->listOfQueryParams[$key] = $parms;
            }
        }

        foreach ($this->listOfQueryParams as $principalField => $condition) {
            if (is_array($condition)){
                foreach ($condition as $key => $item) {
                    $orCondition[][$principalField] = $item;
                }
            }
            else {
                $andCondition[$principalField] = $condition;
            } 
        }

        if (!empty($orCondition)) {
            $this->filterConditionQueryParms['OR'] = $orCondition;
        }    
        if (!empty($andCondition)) {
            $this->filterConditionQueryParms['AND'] = $andCondition;
        }        
   
        if (!empty($this->request['data'])) {
                $newData = $this->request['data'];
                $this->AppModel->apiVariableNameInAdapter($newData);
                $this->listOfWriteFields = array_keys($newData); 
        }
        
        if (Configure::read('debug')) {
                if (!empty($this->listOfQueryParams)) {
                    echo "listOfQueryParams =\n<br>";
                    var_dump($this->listOfQueryParams);
                }
                if (!empty($this->listOfFields)) {
                     echo "listOfFields = \n<br>";
                    var_dump($this->listOfFields);
                }
                if (!empty($this->filterConditionQueryParms)) {
                    echo "filterConditionQueryParms = \n<br>";
                    var_dump($this->filterConditionQueryParms);
                }
                if (!empty($this->listOfWriteFields)) {
                    echo "listOfWriteFields = \n<br>";
                    var_dump($this->listOfWriteFields);
                }
                if (!empty($this->request->data)) {
                    echo "request->data = \n<br>";
                    var_dump($this->request->data);
                }  
        }        
        return true;
    }
    
 
    
    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }   
    
    /**
     * Generate a link for inclusion in results to be returned to API users
     * 
     * @param string $endpoint endpoint that reference the link example: linkedaccounts
     * @param string $rel Action of the url like delete, edit, ...
     * @param string $parameter Extra optional parameter, normally the id ex: /api/1.0/linkedaccounts/2.json  the 2.json
     * @return string
     */
    function generateLink($endpoint, $rel, $parameter) {
        $this->endpointsVersion = Configure::read('generateLink');
        $version = $this->endpointsVersion[$endpoint];
        switch ($rel) {
            case 'edit':
                $link['method'] = 'PATCH';
                break;
            case 'delete':
                $link['method'] = 'DELETE';
                break;
            case 'monitor':
                $link['method'] = 'GET';
                break;
            case 'self':
                $link['method'] = 'GET';
            case 'list':
                $link['method'] = 'GET';
                break;
        }
        if(!empty($rel)){
            $link['rel'] = $rel;
        }
        $link['href'] = 'api' . DS . $version . DS . $endpoint . DS . $parameter;
        return $link;
    }
            


    /**
     * This method checks if access is to be granted to the user to the specified resource.
     * All "positive" permissions must be specifically defined. If a combination of Model, Role and Method is not defined
     * in the configuration aaray then this is interpreted as *no permission is granted*.
     * The following 'help' functions can be added to the analysis tree of the array:
     *  - addInvestorToSearchCriteria
     *  - approve 
     *  - checkOwner
     *  - checkFields
     *  - setListOfFields
     * 
     * The called 'help' function can also receive "class variables" as input parameter, They are identified in the configuration
     * array using the '$' sign, example '$this->listOfFields, $this->request->params['id'], $this->investorId',...
     * See the descriptions of each individual function for more details on how to use them.
     * 
     * Each called function, like for instance addOwner, checkFields etc.. will return a status code 
     * with the following meaning:
     *   1 Error encountered, stop analysis. This means no access will be granted.
     *   2 OK, but continue with analysis. Does not mean yet that access is granted.
     *   3 OK and access granted. Analysis can be stopped.
     * 
     * The aforementioned functions can any class variables of the Controller class like for instance:
     * $this->roleName,
     * $this->investorId,
     * $this->filterConditionQueryParms
     * 
     * It requires the following support in all the Models that are exposed to the Webclient using
     * the API:
     *      method =>  isOwnerpublic function isOwner($investorId, $id)  
     *      method =>  public function getDefaultFields($roleName)
     * and variable:   
     *  var $defaultFields = [ 
     *              'investor' => [
     *                           'id', 
     *                           '...',
     *                           '...'
     *                            ] 
     *              'winAdm' => [
     *                           'id', 
     *                           '...',
     *                           '...' 
     *                           ]
     *              'superAdmin' => [
     *                              'id', 
     *                              '...',
     *                              '...'
     *                              ]   
     * 
     * @param string $model The name of the Model that the user likes to access
     * @param string $roleName The role of the user (investor, superAdmin, winAdmin,...)
     * @param string $action The action/Method that is to be applied to the selected resource
     * @return boolean
     * @throws UnauthorizedException
     */  
    public function checkAcl ($model, $roleName, $requestedAction){
//       http://compare_local.com/tests/check_ACL/Investor/investor/PATCH   
       
        $accessGranted = NO;                // 1 = yes 2 = no
        if (Configure::read('debug')) {       
            echo "Input parameters for Authorization check are:<br>";        
            echo "model = $model<br>"; 
            echo "roleName = $roleName<br>";
            echo "requestedAction = $requestedAction<br>";

            echo "accessGranted = $accessGranted  [1 = yes, 2 = no]<br>"; 
            echo "<br><br>";  
            var_dump ($this->listOfFields);
        }
        $acl_tree_array = Configure::read('acl_tree_array');

        $level0_item = $acl_tree_array;                                         // top level
        
        foreach ($level0_item  as $level1_item) {
   //         echo "Model = " . $level1_item['category_name'] . "<br>";           
            if ($level1_item['category_name'] == $model ) {                     // Model
     //           echo "&nbsp;&nbsp;&nbsp;&nbsp;==> Found<br>";
                if (!empty($level1_item['actions'])) {
     //               var_dump($level1_item['actions']);
                    
                    foreach ($level1_item['actions'] as $actionName => $params) {
                        foreach ($params as $key => $param) {
                            if ($param[0] == "$") {
                                eval ('$temp = ' . $param . ';'); 
                                unset($params[$key]);
                                $params[$key] = $temp;
                            }
                        }
 
     //                   echo __FILE__ . " " . __LINE__ . " action to execute =  " .  $actionName . "<br>";  
                        $status = call_user_func_array([__CLASS__, $actionName], $params);
                        switch ($status) {
                            case WIN_ACL_ANALYSIS_ERROR:
       //                         echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                break 2;
                            case WIN_ACL_ANALYSIS_CONTINUE:
      //                          echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                // don't do anything, just continue with analysis
                                break;
                            case WIN_ACL_GRANT_ACCESS:
                                $accessGranted = YES;
     //                           echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                break 2;
                        }
                    }    
                } 

                foreach ($level1_item['children'] as $level2_item) {
      //              var_dump($level2_item['category_name']);
                    foreach ($level2_item['category_name'] as $roleNameList) {
      //                  echo "Role = " . $roleNameList . "<br>";
      //                  echo __FUNCTION__ . " " . __LINE__ . " roleNameList = $roleNameList<br>";
                        if ($roleNameList == $roleName) {     // Role
       //                     echo "   ===> Found<br>";
                            if (!empty($level2_item['actions'])) {
          //                      var_dump($level2_item['actions']);

                                foreach ($level2_item['actions'] as $actionName => $params) {
                                    foreach ($params as $key => $param) {
          //                              var_dump($param);
                                        if ($param[0] == "$") {
            //                                echo "key = $key<br>";
                                            eval ('$temp = ' . $param . ';'); 
                                            unset($params[$key]);
                                            $params[$key] = $temp;
                                        }
                                    }

             //                       echo __FILE__ . " " . __LINE__ . " action to execute =  " .  $actionName . "<br>";   
                                    $status = call_user_func_array([__CLASS__, $actionName], $params);
                                    switch ($status) {
                                        case WIN_ACL_ANALYSIS_ERROR:
                       //                     echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                            break 4;
                                        case WIN_ACL_ANALYSIS_CONTINUE:
                                            // don't do anything, just continue with analysis
                         //                   echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                            break;
                                        case WIN_ACL_GRANT_ACCESS:
                                            $accessGranted = YES;
                         //                   echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                            break 4;
                                    }   
                                }
                                echo __FUNCTION__ . " " . __LINE__ . "<br>";
                            }


                            foreach ($level2_item['children'] as $level3_item) {
               //                 var_dump($level3_item['category_name']);
                                    foreach ($level3_item['category_name'] as $requestActionList) { 
                   //                 echo "Method = " . $requestActionList . "<br>";
                    //                echo __FUNCTION__ . " " . __LINE__ . " requestActionList = $requestActionList<br>";
                                    }   
                                    if ($requestActionList == $requestedAction) {            // Method/action
                    //                    echo "   ====> Found<br>";                                                               
                                        if (!empty($level3_item['actions'])) {
                       //                     var_dump($level3_item['actions']);

                                            foreach ($level3_item['actions'] as $actionName => $params) {
                                                foreach ($params as $key => $param) {
                                                    if ($param[0] == "$") {
                                                        eval ('$temp = ' . $param . ';'); 
                                                        unset($params[$key]);
                                                        $params[$key] = $temp;
                                                    }
                                                }

                      //                          echo __FILE__ . " " . __LINE__ . " action to execute =  " .  $actionName . "<br>";  
                                                $status = call_user_func_array([__CLASS__, $actionName], $params);
                                                switch ($status) {
                                                    case WIN_ACL_ANALYSIS_ERROR:
                      //                                  echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                                        break 5;
                                                    case WIN_ACL_ANALYSIS_CONTINUE:
                                                        // don't do anything, just continue with analysis
                       //                                 echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                                        break;
                                                    case WIN_ACL_GRANT_ACCESS:
                                                        $accessGranted = YES;
                         //                               echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                                        break 5;
                                                }
                                            }
                                        }                               
                //                       echo __FUNCTION__ . " " . __LINE__ . "<br>";
                                        break 4;                                        // This is the normal end. 
                                    } 
                //                echo __FUNCTION__ . " " . __LINE__ . "<br>";
                            }
               //             echo __FUNCTION__ . " " . __LINE__ . "<br>";
                            break 3;
                        }
                    }
                }
           //     echo __FUNCTION__ . " " . __LINE__ . "<br>";
                break ;                
            }
        }
        if ($accessGranted == NO) {
            if (Configure::read('debug')) {            
                echo __FILE__ . " " . __LINE__ . " NOT AUTHORIZED<br>"; 
                return false;
            }                                         
        }
        else {
            if (Configure::read('debug')) {
                echo __FILE__ . " " . __LINE__ . " THE ACCESS IS AUTHORIZED<br>";
                return true;
            }                                        
        }
    }    
    

    
 

    /** 
     * This is a stub code. It will ALWAYS grant access to the requested resource
     * 
     * @return int  (WIN_ACL_GRANT_ACCESS) 
     */   
    public function approve () {
 //   echo __FUNCTION__ . " " . __LINE__ . "<br>";

 //   echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_GRANT_ACCESS<br>";          
        return WIN_ACL_GRANT_ACCESS;
    }
    
    
    /** 
     * Adds the filtering conditions, to the class variable $this->filterConditionQueryParms.
     * New conditions are simply added and existing ones will be overwritten.
     * 
     * The condition(s) (one or more) is/are formed in the following way:
     *      array key => name of the key to add 
     *      array value => the value to add
     * The original $this->filterConditionQueryParms should not have SQL operands as the results
     * may be unpredictable.
     * Note that in the following case, a new condition,  "myCondition >" => 4 will replace an already
     * existing condition of "myCondition" => 4.
     * This function applies to HTTP-GET message only.
     * 
     * @param $param An array searchConditions that will be added to the conditions as provided by the Web-Client
     * @return  int (WIN_ACL_ANALYSIS_CONTINUE) 
     */   
    public function addInvestorToSearchCriteria($param) {
//    echo __FUNCTION__ . " " . __LINE__ . "<br>";
    
var_dump($this->filterConditionQueryParms);

        foreach ($param as $key => $condition) {
            $isUpperCase = false;
            $upperCondition = ucfirst($condition);

            if ($condition[0] == $upperCondition[0]) {
                $isUpperCase = true;
            }
            if ($condition[0] == "$" OR $isUpperCase == true) {
                eval ('$temp = ' . $condition . ';'); 
                unset($param[$key]);
                $param[$key] = $temp;
            }
        }

        $conditionKeys = array_keys($param);
        foreach ($conditionKeys as $index => $conditionKey) {
            $conditionKeys[$index] = str_replace(['<', '>', '>=', '<=', '<>'], '', $conditionKey);
        }
//        var_dump($conditionKeys);
    
        if (!empty($this->filterConditionQueryParms)) {    
            if (array_key_exists('AND', $this->filterConditionQueryParms)) {
                foreach ($conditionKeys as $conditionKey) {
                    unset( $this->filterConditionQueryParms["AND"][$conditionKey]);
                }                 
                $this->filterConditionQueryParms["AND"] = $param + $this->filterConditionQueryParms["AND"];
            }
            else {            
                if (array_key_exists('OR', $this->filterConditionQueryParms)) {
                    $this->filterConditionQueryParms["AND"] = $param;
                }
                else { 
                    foreach ($conditionKeys as $conditionKey) {
                        unset( $this->filterConditionQueryParms[$conditionKey]);
                    }
                    $this->filterConditionQueryParms = $param;
                }
            }    
        }
        else {   
            foreach ($conditionKeys as $conditionKey) {
                unset( $this->filterConditionQueryParms[$conditionKey]);
            }            
            $this->filterConditionQueryParms = $param;       
        }

//var_dump($this->filterConditionQueryParms); 
 //   echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_ANALYSIS_CONTINUE<br>";          
        return WIN_ACL_ANALYSIS_CONTINUE;
    }    
     
    /** 
     * Checks if the list of fields provided in $fields ALL have the permission according to
     * the role of the user to perform the requested operation as defined in $property. The check is 
     * done against the array $this->referenceVariablePermissions with keys "modelName" and "roleName".
     * If a field which is provided in '$this->listOfFields' is NOT present in the 
     * $referenceVariablePermissions then access will be denied.
     * This is a very fine-grained permission check.
     * 
     * @param $property Values permitted: "R" [= Read access] or "W" [= Write access]
     * @param $model The name of the model that the current user likes to access
     * @param $roleName The name of the role of the current user
     * @param $fields Array with the name of the fields to be checked. Typically this is '$this->listOfFields'
     * @return int    (WIN_ACL_ANALYSIS_ERROR or WIN_ACL_GRANT_ACCESS)
     */
    public function checkFields($property, $model, $roleName, $fields) {  
//    echo __FUNCTION__ . " " . __LINE__ . "<br>";
//    var_dump($fields);

        // Special treatment for fields "id" and "xxx_links"
        $key = array_search("id", $fields);
        if ($key !== false) {
            unset($fields[$key]);
        }
        $modifiedModel = strtolower($model);  
        $key = array_search($modifiedModel . "_links", $fields);
        if ($key !== false) {
            unset($fields[$key]);
        }
        
        $acl_referenceVariablePermissions = Configure::read('acl_referenceVariablePermissions');         
     
        echo __FUNCTION__ . " " . __LINE__ . " property = $property, model = $model and role = $roleName<br>";
        $this->print_r2($fields); 
        $referenceRolePermissions = $acl_referenceVariablePermissions[$model][$roleName];
             
        foreach ($fields as $item) {
//echo __FILE__ . " " . __LINE__ . " item = $item<br>";            
            if (strpos ($referenceRolePermissions[$item], $property) === false) {
     //           echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_ANALYSIS_ERROR<br>"; 
                return WIN_ACL_ANALYSIS_ERROR;
            } 
        }
 //       echo __FUNCTION__ . " " . __LINE__ . " Returning  WIN_ACL_GRANT_ACCESS<br>"; 
        return WIN_ACL_GRANT_ACCESS; 
    }    
      
    /** 
     * Checks if the investor is the (in)direct owner of the Model which s/he likes to access.
     * This function can only be applied where the 'id' as provided in the 
     * HTTP-GET/HTTP-PUT/HTTP-DELETE/HTTP-PUT by the user. It DOES NOT work correctly on a HTTP-GET which 
     * searches for one or more results. ( = HTTP-GET -> v1_index)
     * In HTTP-POST the result might be unpredictable, as POSTs may or may not contain an 'id'.
     * 
     * @param string Name of the model which is to be accessed
     * @param int $investorId The internal reference to the Investor object of the user
     * @param int $id The internal reference to the Object that is going to be accessed
     * @return int  (WIN_ACL_ANALYSIS_ERROR or WIN_ACL_ANALYSIS_CONTINUE)
     */
    public function checkOwner($model, $investorId, $id) {
 //      echo __FUNCTION__ . " " . __LINE__ . " investorId_user = $investorId  and investorId_requestedresource = $id<br>";  

        $this->$model = ClassRegistry::init($model);
        if (!$this->$model->isOwner($investorId, $id) ) {
                       
  //      echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_ANALYSIS_ERROR<br>";            
            return WIN_ACL_ANALYSIS_ERROR;
        }
//       echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_ANALYSIS_CONTINUE<br>";      
        return WIN_ACL_ANALYSIS_CONTINUE; 
    }   
    
    /** 
     * Reads the list of fields which the server will provide to the webclient if NO fields
     * were defined in the HTTP message. This is only useful for the HTTP-GET message
     * 
     * @param $model The name of the Model from whom to read the list of fields
     * @param $roleName The name of the role for whom the list shall be retrieved
     * @return int  (WIN_ACL_ANALYSIS_CONTINUE)
     */
    public function setListOfFields($model, $roleName) {
 //       echo __FUNCTION__ . " " . __LINE__ . " Model = $model and rolename = $roleName <br>";  

        if (empty($this->listOfFields)) {
            $this->$model = ClassRegistry::init($model);
            $this->listOfFields = $this->$model->getDefaultFields($roleName);
        }
   //     var_dump($this->listOfFields);       
   //     echo __FUNCTION__ . " " . __LINE__ . " Returning WIN_ACL_ANALYSIS_CONTINUE <br>"; 
        return WIN_ACL_ANALYSIS_CONTINUE;
    }

   
    
 
    
}
