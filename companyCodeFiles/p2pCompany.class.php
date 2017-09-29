<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.3
 * @date 2017-01-28
 * @package
 *
 * Base class for all the p2p companies
 *
 * 
 *
 *
 * 2016-08-11		version 2016_0.1
 * Basic version
 * function getMonetaryValue							[OK, tested]
 * function getPercentage								[OK, tested]
 *  function getDurationValue							[OK, tested]
 * Testing system for "simulating" accesses to the different sites			[OK, tested]
 * Added UrlSequence array with all url's to be used for a particular sequence     [OK, tested]
 *
 *
 * 2016-12-12		version 2016_0.2
 * Error rectified in function "getPercentage". 7,02% was not properly detected.	[OK, tested]
 *
 *
 * 2017-01-28		version 0.3
 * Adding generic tracing capability 											[OK, NOT tested]
 *
 *
 * 2017-02-14		version 0.4
 * function "getCurrentAccumulativeRowValue" was updated with the capability	[NOT OK, not tested]
 * of ONLY adding cuotas realmente pagados.
 *
 * 2017-05-16              version 0.5
 * Added parallelization to collectUserInvestmentData
 * Added dom verification to collectUserInvestmentData
 *
 *
 * 2017-05-31              version 0.6
 * Function to save user investment data into DB
 *
 *
 * 2017-06-30              version 0.7
 * Added function to create an individual cookies file for company when a request and delete after logout
 *
 * 
 * 2017-08-10              version 0.8
 * Structure revision added
 * 
 * 2017-08-29
 * Json revision function
 * Html revision general function
 *
 * 
 * 2017-09-17
 * added callback functions for Dashboard2
 * 
 * 
 * PENDING
 * fix method  getMonetaryValue()
 */
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
//Configure::load('constants'); //Load all global constants

//require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'IReadFilterWinvestify.php'));
use Browser\Casper;

//require_once (ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'php-bondora-api-master' . DS . 'bondoraApi.php');
class p2pCompany {
    /* const DAY = 1;
      const MONTH = 2;
      const YEAR_CUARTER = 3;
      const HOUR = 4; */

// type of financial product
    /* const PAGARE = 1;
      const LOAN = 2;
      const FINANCING = 3; */
// http message type for method "getCompanyWebpage"
    /* const GET = 1; // GET a webpage
      const POST = 2; // POST some parameters, typically used for login procedure
      const PUT = 3; // Not implemented yet)
      const DELETE = 4; // DELETE a resource on the server typically used for logging out
      const OPTIONS = 5; // Not implemented yet)
      const TRACE = 6; // Not implemented yet)
      const CONNECT = 7; // Not implemented yet)
      const HEAD = 8; // Not implemented yet) */

    //Variable to use in this method
    // MarketplacesController

    protected $classContainer;
    protected $baseUrl;
    //Data for the queue
    protected $queueId;
    protected $idForQueue;
    protected $idForSwitch = 0;
    //User data
    protected $user = "";
    protected $password = "";
    //Data to take investor data
    protected $tempArray;
    protected $data1;
    protected $tempUrl;
    protected $numberOfInvestments;
    protected $accountPosition = 0;
    //Variable for debugging
    protected $hasElements = true;
    protected $tracingDir;
    protected $logDir;
    protected $testConfig;
    protected $cookiesDir;
    protected $config;
    protected $errorInfo;
    //Backup variables
    protected $urlSequenceBackup = array();
    protected $tries = 0;
    //Cookies
    protected $cookies_name = 'cookies.txt';
    //These are three variables 
    protected $typeUniqueElement = [];
    protected $valueUniqueElement = [];
    protected $verifyUniqueElement = [];
    protected $countUniqueElement = [];
    //This variables are for scanning purpose, if you find this variable, you don't have to scan the node anymore
    protected $typeNotMoreScanning = [];
    protected $valueNotMoreScanning = [];
    protected $sameStructure = true;
    //Variables to open stream to write a file
    protected $fp;
    //Variables to download files
    protected $typeFileTransaction;
    protected $typeFileInvestment;
    protected $typeFileAmortizationtable;
    protected $nameFileTransaction = "transaction_";
    protected $nameFileInvestment = "investment_";
    protected $nameFileAmortizationTable = "amortizationTable_";
    protected $nameFileAmortizationTableList = "amortizationTableList.json";
    protected $numFileTransaction = 1;
    protected $numFileInvestment = 1;
    protected $numFileAmortizationtable = 1;
    protected $companyName;
    protected $userReference;
    protected $linkAccountId;
    //Variables for casperjs
    protected $casperObject;
    //Variables for amortization tables
    protected $loanIds = [];
    

    /**
     *
     * Prepare all the default data of the class and its subclasses
     *
     */
    function __construct() {
        error_reporting(0);
        $this->urlSequence = array();    // contains the list of url's to be executed. The url must contain protocol, i.e.
        // http o https.
        $this->tracingDir = __DIR__ . "/tracings"; // Directory where tracing files are kept
        $this->logDir = __DIR__ . "/log";   // Directory where the log files are stored
        $this->testConfig['active'] = false;  // test system activated	
//	$this->testConfig['siteReadings'] = array('/var/www/compare_local/app/companyCodeFiles/tempTestFiles/lendix_marketplace');
        $createdFolder = $this->createFolder('cookies');
        $this->cookiesDir = $createdFolder;
        $this->config['tracingActive'] = false;
        $this->headers = array();


// ******************************** end of configuration parameters *************************************
        mkdir($this->tracingDir, 0777);
        mkdir($this->logDir, 0770);
    }

    /**
     *
     * 	Logout of user from to company portal with MultiCurl.
     * 	
     */
    function companyUserLogoutMultiCurl($str) {
        $this->doCompanyLogoutMultiCurl();
    }

    /**
     *
     * 	Stores all the possible configuration parameters. Provided parameters are merged with the already
     * 	existing parameters
     * 	@param array 		$configurationParameters
     * 	
     * 	Supported parameters:
     * 	
     * 	 PARAMETER		  VALUE
     * 	messageType                 1 "GET",			Message will be sent as a GET  (Default)
     * 					2 "DELETE"
     * 					3 "POST"
     * 	
     * 	tracingActive	true/false		Tracing of http messages is active and result is stored in tracing directory
     * 	traceID		string			String that forms part of the filename which holds the tracing information
     * 	appDebug	true/false		shows debug messages
     *
     */
    function defineConfigParms($configurationParameters) {
        $temp = $this->config;
        $this->config = array_merge($temp, $configurationParameters);
        $this->config['appDebug'] = false;
    }

    public function getConfig() {
        return $this->config;
    }

    /*
     * 	Add extra header(s) to the *next* HTTP messsage which is to be sent.  
     * 	@param	$headers	array		An array with the text for the extra headers
     * 									example: array ("sessionToken: $lendixSessionId", "userId: $userId");
     * 	@return	boolean
     * 							
     */

    function defineHeaderParms($headers) {
        $this->headers = $headers;
        return true;
    }

    /**
     *
     * 	Stores all the configuration parameters for test purposes
     * 	@param array 		$testParameters
     * 			parameters implemented:
     * 			'siteReadings'	array of files that contain the html files that
     * 							will be read in sequential order while 'debug' == true
     * 							Each "site access" will load the first entry in the array
     * 							and that entry will be deleted after a succesful read.
     * 							Note that entries can be absolute file names or relative
     * 							file names. Note that the webserver must have access to
     * 							the directory where the files are stored.
     * 							This variable is ONLY used when the test system is ACTIVE
     * 			'active'		false: 	Testing system not active
     * 							true:	testing system active 
     */
    function defineTestParms($testParameters) {
        $this->testConfig['active'] = true;
    }

    public function getTestConfig() {
        return $this->testConfig;
    }

    /*     * used by both the investors and the admin user for obtaining marketplace data
     *
     * 	Enter the Webpage of the user's portal
     * 	@param string 		$url	The url is read from the urlSequence array, i.e. contents of first element
     * 	@return	string		$str	html string
     *
     */

    function doCompanyLogin(array $loginCredentials) {


        $url = array_shift($this->urlSequence);
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "<strong>" . "TestSystem: file = $currentScreen<br>" . "</strong>";
                return $str;
            }
        }

//traverse array and prepare data for posting (key1=value1)
        foreach ($loginCredentials as $key => $value) {
            $postItems[] = $key . '=' . $value;
        }

//create the final string to be posted using implode()
        $postString = implode('&', $postItems);

        $curl = curl_init();
        if (!$curl) {
            echo __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

// check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);
        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        //set data to be posted
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);

        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);  // important
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);  // Important
        // Fetch the URL and save the content
        $str = curl_exec($curl);
        if (!empty($this->testConfig['active']) == true) {
            print_r(curl_getinfo($curl));
            echo "<br>";
            print_r(curl_error($curl));
            echo "<br>";
        }
        curl_close($curl);

        if ($this->config['appDebug'] == true) {
            echo "LOGIN URL = $url <br>";
        }
        if ($this->config['tracingActive'] == true) {
            $this->doTracing($this->config['traceID'], "LOGIN", $str);
        }
        return $str;
    }

    function doCompanyLoginRequestPayload($payload) {


        $url = array_shift($this->urlSequence);
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "<strong>" . "TestSystem: file = $currentScreen<br>" . "</strong>";
                return $str;
            }
        }


        $curl = curl_init();
        if (!$curl) {
            echo __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

// check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);
        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        //set data to be posted
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);  // important
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);  // Important
        // Fetch the URL and save the content
        $str = curl_exec($curl);
        if (!empty($this->testConfig['active']) == true) {
            print_r(curl_getinfo($curl));
            echo "<br>";
            print_r(curl_error($curl));
            echo "<br>";
        }
        curl_close($curl);

        if ($this->config['appDebug'] == true) {
            echo "LOGIN URL = $url <br>";
        }
        if ($this->config['tracingActive'] == true) {
            $this->doTracing($this->config['traceID'], "LOGIN", $str);
        }
        return $str;
    }

    /**
     *
     * 	Leave the Webpage of the user's portal. The url is read from the urlSequence array, i.e. contents of first element
     * 	
     */
    function doCompanyLogout($url = null) {
        /*
          //traverse array and prepare data for posting (key1=value1)
          foreach ( $logoutData as $key => $value) {
          $postItems[] = $key . '=' . $value;
          }
          //create the final string to be posted using implode()
          $postString = implode ('&', $postItems);
         */
//  barzana@gmail.com 	939233Maco048 
        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $curl = curl_init();

        if (!$curl) {
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

// check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Fail the cURL request if response code = 400 (like 404 errors) 
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);  // important
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);  // Important
        // Fetch the URL and save the content
        // cURL executed successfully
        $str = curl_exec($curl);
        if (!empty($this->testConfig['active']) == true) {
            print_r(curl_getinfo($curl));
            echo "<br>";
            print_r(curl_error($curl));
            echo "<br>";
        }

// close cURL resource to free up system resources		
        curl_close($curl);
        if ($this->config['appDebug'] == true) {
            echo "LOGOUT URL = $url <br>";
        }
        if ($this->config['tracingActive'] == true) {
            $this->doTracing($this->config['traceID'], "LOGOUT", $str);
        }
        return($str);
    }

    function doTracing($tracingID, $action, $messageContent) {
        if (empty($tracingID)) {
            return;      // this is to filter out the messages from marketplace scanning
        }

        $fileName = $this->tracingDir . "/" . $tracingID . "_" . date("Y-m-d_H:i:s") . $action . ".html";
        $result = file_put_contents($fileName, $messageContent);

        return;
    }

    /**
     *
     * 	Load the received Webpage into a string.
     * 	If an url is provided then that url is used instead of reading it from the urlSequence array
     * 	@param string 		$url	The url the connect to
     *
     */
    function getCompanyWebpage($url = null, $credentials = null) {

        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }

        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                echo "currentScreen = $currentScreen";
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $curl = curl_init();

        if (!$curl) {
            $msg = __FILE__ . " " . __LINE__ . "Could not initialize cURL handle for url: " . $url . " \n";
            $msg = $msg . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }

        if ($this->config['postMessage'] == true) {
            curl_setopt($curl, CURLOPT_POST, true);
//		echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

// check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            echo "EXTRA HEADERS TO BE ADDED<br>";
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }
        
        if (!empty($credentials)) {
                foreach ($credentials as $key => $value) {
                $postItems[] = $key . '=' . $value;
            }
            $postString = implode('&', $postItems);
            //set data to be posted
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
        }

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Set a different user agent string (Googlebot)
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Fail the cURL request if response code = 400 (like 404 errors) 
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        // Return the actual result of the curl result instead of success code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Wait for 10 seconds to connect, set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);  // important
        $result = curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);  // Important
        // Fetch the URL and save the content
        $str = curl_exec($curl);
        if (!empty($this->testConfig['active']) == true) {
            print_r(curl_getinfo($curl));
            echo "<br>";
            print_r(curl_error($curl));
            echo "<br>";
        }

        if ($this->config['appDebug'] == true) {
            echo "VISITED COMPANY URL = $url <br>";
        }
        if ($this->config['tracingActive'] == true) {
            $this->doTracing($this->config['traceID'], "WEBPAGE", $str);
        }
        return($str);
    }

    /** used by both the investors and the admin user for obtaining marketplace data
     *
     * 	Enter the Webpage of the user's portal
     * 	@param string 		$url	The url is read from the urlSequence array, i.e. contents of first element
     * 	@return	string		$str	html string
     *
     */
    function doCompanyLoginMultiCurl($loginCredentials, $payload = null) {
        $url = array_shift($this->urlSequence);
        $this->errorInfo = $url;
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "<strong>" . "TestSystem: file = $currentScreen<br>" . "</strong>";
                return $str;
            }
        }
        
        $request = new \cURL\Request();
        // check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            $request->getOptions()
                    ->set(CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }
        if(!empty($loginCredentials)) {
            if ($payload) {
                $postString = $loginCredentials;
                $request->getOptions()
                            ->set(CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            }
            else {
                $postString = http_build_query($loginCredentials);
            }    
            $request->getOptions()
                ->set(CURLOPT_POSTFIELDS, $postString);
        } 

        $request->getOptions()
                ->set(CURLOPT_URL, $url)
                ->set(CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0')
                ->set(CURLOPT_FOLLOWLOCATION, true)
                ->set(CURLOPT_FAILONERROR, true)
                ->set(CURLOPT_RETURNTRANSFER, true)
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                ->set(CURLOPT_TIMEOUT, 100)
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name)
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);

        //$request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";" . "";
        $info = [
            "companyIdForQueue" => $this->idForQueue,
            "idForSwitch" => $this->idForSwitch,
            "typeOfRequest" => "LOGIN"
        ];
        
        $request->_page = json_encode($info);
        // Add the url to the queue
        $this->classContainer->addRequestToQueueCurls($request);
    }

    /**
     *
     * 	Leave the Webpage of the user's portal. The url is read from the urlSequence array, i.e. contents of first element
     * 	
     */
    function doCompanyLogoutMultiCurl(array $logoutCredentials = null, $url = null) {
        /*
          //traverse array and prepare data for posting (key1=value1)
          foreach ( $logoutData as $key => $value) {
          $postItems[] = $key . '=' . $value;
          }
          //create the final string to be posted using implode()
          $postString = implode ('&', $postItems);
         */
        //  barzana@gmail.com 	939233Maco048 
        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }
        echo $url;
        $this->errorInfo = $url;
        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $request = new \cURL\Request();

        if (!empty($this->headers)) {
            $request->getOptions()
                    ->set(CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }

        if (!empty($logoutCredentials)) {
            foreach ($logoutCredentials as $key => $value) {
                $postItems[] = $key . '=' . $value;
            }

            //create the final string to be posted using implode()
            $postString = implode('&', $postItems);

            $request->getOptions()
                    ->set(CURLOPT_POSTFIELDS, $postString);
        }

        //$request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";" . "LOGOUT";
        $info = [
            "companyIdForQueue" => $this->idForQueue,
            "idForSwitch" => $this->idForSwitch,
            "typeOfRequest" => "LOGOUT"
        ];
        
        $request->_page = json_encode($info);

        $request->getOptions()
                ->set(CURLOPT_URL, $url)
                ->set(CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0')
                ->set(CURLOPT_FOLLOWLOCATION, true)
                ->set(CURLOPT_FAILONERROR, true)
                ->set(CURLOPT_RETURNTRANSFER, true)
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                ->set(CURLOPT_TIMEOUT, 100)
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name)
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);

        $this->classContainer->addRequestToQueueCurls($request);
    }

    /**
     *
     * 	Add a request to the multicurl Queue
     * 	If an url is  provided then that url is used instead of reading it from the urlSequence array
     * 	@param string $url The url the connect to
     *  @param string $credentials The credentials used to connect to the url provided
     *  @param boolean $payload If payload is true, then, the credentials are json type
     *
     */
    function getCompanyWebpageMultiCurl($url = null,$credentials = null, $payload = null) {

        if (empty($url)) {
            $url = array_shift($this->urlSequence);
            echo $url;
        }
        echo 'The url is: ' . $url;
        $this->errorInfo = $url;

        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                echo "currentScreen = $currentScreen";
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $request = new \cURL\Request();
        
        if(!empty($credentials)) {
            if ($payload) {
                $postString = $credentials;
                $request->getOptions()
                            ->set(CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            }
            else {
                $postString = http_build_query($credentials);
            }    
            $request->getOptions()
                ->set(CURLOPT_POSTFIELDS, $postString);
        }
        

        if ($this->config['postMessage'] == true) {
            $request->getOptions()
                    ->set(CURLOPT_POST, true);
            //echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

        // check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            $request->getOptions()
                    //->set(CURLOPT_HEADER, true) Esto fue una prueba, no funciona, quitar
                    ->set(CURLOPT_HTTPHEADER, $this->headers);

            unset($this->headers);   // reset fields
        }
        
        //$request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";WEBPAGE";
        $info = [
            "companyIdForQueue" => $this->idForQueue,
            "idForSwitch" => $this->idForSwitch,
            "typeOfRequest" => "WEBPAGE"
        ];
        
        $request->_page = json_encode($info);
        $request->getOptions()
                // Set the file URL to fetch through cURL
                ->set(CURLOPT_URL, $url)
                // Set a different user agent string (Googlebot)
                ->set(CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0')
                // Follow redirects, if any
                ->set(CURLOPT_FOLLOWLOCATION, true)
                // Fail the cURL request if response code = 400 (like 404 errors) 
                ->set(CURLOPT_FAILONERROR, true)
                ->set(CURLOPT_AUTOREFERER, true)
                //->set(CURLOPT_VERBOSE, 1)
                // Return the actual result of the curl result instead of success code
                ->set(CURLOPT_RETURNTRANSFER, true)
                // Wait for 10 seconds to connect, set 0 to wait indefinitely
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                // Execute the cURL request for a maximum of 50 seconds
                ->set(CURLOPT_TIMEOUT, 100)
                // Do not check the SSL certificates
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name) // important
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name); // Important

                
        //Add the request to the queue in the classContainer controller
        $this->classContainer->addRequestToQueueCurls($request);

        if ($this->config['appDebug'] == true) {
            echo "VISITED COMPANY URL = $url <br>";
        }
    }

    /*
     *
     * 	Calculates the accumulative values of a column for the actual date and which cuotes have been paid, i.e.
     * 	not subject to payment delay
     *
     * 	@param	array 	$amortizationTable 	Shall only have rows with valid data, i.e. no headers and footers	
     * 	@param	string	$date			The date until when the accumulative value needs to be calculated.
     * 									If current date == $date the value of the corresponding contentRow is added.
     * 									format of date = yyyy-mm-dd						
     * 	@param	string	$dateFormat		format of date in table. Formats supported are 'dd-mm-yyyy' and 'dd/mm/yyyy'
     * 	@param	integer	$dateRow		This is the INDEX which holds the date information		
     * 	@param	integer	$contentRow		row that contains the values to add
     * 	@param	integer	$conditionRow	Row that must have value 0 or 1 in order to add the value of $contentRow to $value
     * 	@return integer	$value			accumulative value of all items in $contentrow. Value in €cents
     */

    function getCurrentAccumulativeRowValue($amortizationTable, $date, $dateFormat, $dateRow, $contentRow, $conditionRow) {
        $total = 0;

        $format = array("dd/mm/yy" => "/",
            "dd-mm-yy" => "-",
            "dd/mm/yyyy" => "/",
            "dd-mm-yyyy" => "-");

        foreach ($format as $key => $item) {
            if ($key == $dateFormat) {
                $seperator = $item;
                break;
            }
        }

        foreach ($amortizationTable as $v1) {
            $tempCalculatedRowDate = explode($seperator, $v1[$dateRow]); // Change to internal format, DATE  YYYY-MM-DD
            $calculatedRowDate = $tempCalculatedRowDate[2] . "-" . $tempCalculatedRowDate[1] . "-" . $tempCalculatedRowDate[0];

            if ($calculatedRowDate <= $date) {  // encountered date is smaller so add value
                if ($v1[$conditionRow] < 2) {   // but only for payment status PENDIENTE or OK
                    $total = $total + $this->getMonetaryValue($v1[$contentRow]);
                }
            } else {
                break;
            }
        }
        return $total;
    }

    /**
     *
     * 	Extracts the duration as an integer from an input string
     *
     * 	@param 		string	$inputValue in duration in format like '126d', '126 d', '3 meses', '100 días', ' 5 horas'
     * 						'quedan 3 meses'
     * 	@return 	array	[0] contains the number/value
     * 						[1] contains duration unit as defined. DAY->1, MONTH->2, YEAR_CUARTER -> 3, HOUR -> 4
     * 								or -1 if no unit defined
     *
     */
    function getDurationValue($inputValue) {

        $value = array();
        $value[0] = trim(preg_replace('/\D/', ' ', $inputValue));

        $temp = trim(preg_replace('/\d/', ' ', $inputValue));
        $tempDurationValueUnit = explode(' ', $temp);

        $firstChar = strtoupper(substr($tempDurationValueUnit[count($tempDurationValueUnit) - 1], 0, 1));

        switch ($firstChar) {
            case 'H':
                $value[1] = 4;
                break;
            case 'D':
                $value[1] = 1;
                break;
            case 'M':
                $value[1] = 2;
                break;
            default:
                $value[1] = -1;
        }
        return $value;
    }

    /**
     *
     * 	Look for ALL elements (or only first) which fullfil the tag item. 
     * 	Obtain the following:
     * 		<div id="myId"....>      getElements($dom, "div", "id", "myId");
     * 		or
     * 		<div class="myClass" ....>  getElements($dom, "div", "class", "myClass");
     * 		
     * 	@param $dom
     * 	@param $tag			string 	name of tag, like "div"
     * 	@param $attribute	string	name of the attribute like "id"   optional parameter
     * 	@param $value		string	value of the attribute like< "myId"  optional parameter. Must be defined if $attribute is defined
     * 	@return array $list of doms
     * 	$list is empty if no match was found
     *
     */
    /* public function getElements($dom, $tag, $attribute, $value) {

      $list = array();

      $attributeTrimmed = trim($attribute);
      $valueTrimmed = trim($value);
      $tagTrimmed = trim($tag);
      $tags = $dom->getElementsByTagName($tagTrimmed);

      foreach ($tags as $tagFound) {
      $attValue = trim($tagFound->getAttribute($attributeTrimmed));
      if ( strncasecmp ($attValue, $valueTrimmed, strlen($valueTrimmed)) == 0) {
      $list[] = $tagFound;
      }
      }
      return $list;
      } */

    /**
     *
     * 	Look for ALL elements (or only first) which fullfil the tag item. 
     * 	Obtain the following:
     * 		<div id="myId"....>      getElements($dom, "div", "id", "myId");
     * 		or
     * 		<div class="myClass" ....>  getElements($dom, "div", "class", "myClass");
     * 		
     * 	@param $dom
     * 	@param $tag			string 	name of tag, like "div"
     * 	@param $attribute	string	name of the attribute like "id"   optional parameter
     * 	@param $value		string	value of the attribute like< "myId"  optional parameter. Must be defined if $attribute is defined
     * 	@return array $list of doms
     * 	$list is empty if no match was found
     *
     */
    public function getElements($dom, $tag, $attribute = null, $value = null) {

        $list = array();

        $attributeTrimmed = trim($attribute);
        $valueTrimmed = trim($value);
        $tagTrimmed = trim($tag);
        libxml_use_internal_errors(true);
        $tags = $dom->getElementsByTagName($tagTrimmed);
        if ($tags->length > 0) {
            foreach ($tags as $tagFound) {
                $attValue = trim($tagFound->getAttribute($attributeTrimmed));
                if (strncasecmp($attValue, $valueTrimmed, strlen($valueTrimmed)) == 0) {
                    $list[] = $tagFound;
                }
            }
            $this->hasElements = true;
            return $list;
        } else {
            $this->hasElements = false;
        }
    }

    /**
     * Verify if a node has elements or it is empty
     * @param node $elements They are the nodes to verify if contains a element
     * @param type $limit If we need to verify that a node has certain number of nodes
     */
    public function verifyNodeHasElements($elements, $limit = null) {
        if ($elements->length == 0) {
            $this->hasElements = false;
        } else if (!empty($limit) && $elements->length < $limit) {
            $this->hasElements = false;
        } else {
            $this->hasElements = true;
        }
    }

    /*
     * Get a list of dom elements searching by class
     * @param dom element $dom It is the dom element of a website
     * @param string $class It is the class which is used to search the elements
     * @return node It is all the elements that coincide with the class
     */

    public function getElementsByClass($dom, $class) {
        $dom_xpath = new DOMXPath($dom);
        $element = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
        return $element;
    }

    /*
     * 	@param	table		$amortizationTable shall only have rows with valid data, i.e. no headers and footers	
     * 	@param	integer 	$dateRow	This is the INDEX which holds the date information
     * 	@param	$dateFormat	format of date in tabe. Formats supported are 'dd-mm-yyyy' and 'dd/mm/yyyy'
     *
     * 	@param $date	highest date encountered in table
     * 	
     */

    function getHighestDateValue($amortizationTable, $dateFormat, $dateRow) {
        $oldCalculatedValueDate = "0000-00-00";

        $total = 0;

        $format = array("dd/mm/yy" => "/",
            "dd-mm-yy" => "-",
            "dd/mm/yyyy" => "/",
            "dd-mm-yyyy" => "-");

        foreach ($format as $key => $item) {
            if ($key == $dateFormat) {
                $seperator = $item;
                break;
            }
        }

        foreach ($amortizationTable as $v1) {
            $tempNewCalculatedRowDate = explode($seperator, $v1[$dateRow]); // translate date to internal format -> yyyy-mm-dd
            $calculatedRowDate = $tempNewCalculatedRowDate[2] . "-" . $tempNewCalculatedRowDate[1] . "-" . $tempNewCalculatedRowDate[0];
            if ($calculatedRowDate >= $oldCalculatedValueDate) {  // encountered date is smaller so add value
                $oldCalculatedValueDate = $calculatedRowDate;
            }
        }
        $tempDate = explode('-', $oldCalculatedValueDate);
        return ($tempDate[2] . $seperator . $tempDate[1] . $seperator . $tempDate[0]);
    }

    /**
     *   This requires a permanent solution. Some PFPs have as format 1,000,345€ and 1.000.345€ and 1 000 345 € (LENDIX) dpending
     * on the selected language.
     * The second parameter is a quick fix for arboribus
     * 	Extracts the amount as an integer from n input string
     *       
     * 	@param 		string	$inputValue in string format like 1,23€ -> 123 and 10.400€ -> 1040000 and 12.235,66€ -> 1223566
     * 	@return 	int		$outputValue in €cents
     * 	
     */
    function getMonetaryValue($inputValue, $separating = null) {

        if (empty($separating)) {
            $separating = ',';
        }
        $tempValue = trim(preg_replace('/\D/', '', $inputValue));

        if (stripos($inputValue, $separating) === false) {
            return $tempValue * 100;
        }
        return $tempValue * 1;
    }
    
    public function getPFPName() {
        $position = stripos($file, 'companyCodeFiles');
        $substring = substr($file, $position+17);
        $company = explode(".", $substring)[0];
        return $company;
    }

    /**
     *
     * 	Extracts the percentage as an integer from an input string
     *
     * 	@param 		string	$inputPercentage in string format like 5,4% or 5,43% or 5%. Note that 1,23% generates 123 and 33% -> 3300
     * 															5,5% TAE -> 550
     * 															7,02% -> 702
     *                                                                                                                   	8,5 % -> 850
     * º                                                            format like 'This is a string 54%' -> 5400
     * 	@return 	int		$outputPercentage
     * 	
     */
    function getPercentage($inputPercentage) {

        $progress = trim(preg_replace('/\D/', ' ', $inputPercentage));
        $tempValues = explode(" ", $progress);

        if (strlen($tempValues[1]) == 1) {
            $tempValues[1] = $tempValues[1] * 10;
        }

        $outputPercentage = $tempValues[0] * 100 + $tempValues[1];
        if ($inputPercentage < 0) {
            return -$outputPercentage;
        } else {
            return $outputPercentage;
        }
    }

    /**
     *
     * 	Translates the name of a month in Spanish to its number.
     * 	@param	string	$monthStr	name of the month, like "mar" or "MARZO"
     * 	@return	string	$m	numner of month as a 2 character string: "mar" => "03"
     */
    function getSpanishMonthNumber($monthStr) {

        $m = ucfirst(strtolower(trim($monthStr)));
        switch ($m) {
            case "Enero":
            case "Ene":
                $m = "01";
                break;
            case "Febrero":
            case "Feb":
                $m = "02";
                break;
            case "Marzo":
            case "Mar":
                $m = "03";
                break;
            case "Abril":
            case "Abr":
                $m = "04";
                break;
            case "May":
                $m = "05";
                break;
            case "Junio":
            case "Jun":
                $m = "06";
                break;
            case "Julio":
            case "Jul":
                $m = "07";
                break;
            case "Agosto":
            case "Ago":
                $m = "08";
                break;
            case "Septiembre":
            case "Sep":
                $m = "09";
                break;
            case "Octubre":
            case "Oct":
                $m = "10";
                break;
            case "Noviembre":
            case "Nov":
                $m = "11";
                break;
            case "Diciembre":
            case "Dic":
                $m = "12";
                break;
            default:
                $m = false;
                break;
        }
        return $m;
    }

    /**
     *
     * 	Saves information to a logfile
     *
     * 	@param string	$filename		Name of the logfile to be used
     * 	@param string	$msg			Content to be logged
     *
     */
    function logToFile($filename, $msg, $dirFile = "") {
        //Like this function, change later tomorrow
        //$fileName =  "/var/www/html/cake_branch/app/companyCodeFiles/log/" . $filename;
        $fileName = $this->logDir . $filename;
        if (!$dirFile == "") {
            $fileName = $dirFile . "/log/" . $filename;
        }
        $fd = fopen($fileName, "a");
        $msg = date("d-m-y H:i:s") . " " . $msg;
        fwrite($fd, $msg . "\n");
        fclose($fd);
    }

    /**
     *
     * 	Sets the url data for the sequence which is to be started. Any
     * 	existing data will be overwritten
     *
     * 	@param 	array	urlData		array of all the urls to be loaded
     * 	@return	boolean			
     *
     */
    function setUrlSequence($urlSequence) {
        $this->urlSequence = $urlSequence;
    }

    function print_r2($val) {
        echo '<pre>';
        print_r($val);
        echo '</pre>';
    }

    /**
     *
     * 	Sets the url data for the sequence if something fails. Any
     * 	existing data will be overwritten
     *
     * 	@param 	array	urlData		array of all the urls to be loaded		
     *
     */
    function setUrlSequenceBackup($urlSequence) {
        $this->urlSequenceBackup = $urlSequence;
    }

    /**
     * 	Gets the url backup data for the sequence if something fails.
     *
     *      @return array	urlData		array of all the urls to be loaded
     */
    function getUrlSequenceBackup() {
        return $this->urlSequenceBackup;
    }

    /**
     * Sets the class that uses multicurl queue
     * 
     * @param object $classContainer It is the class that uses multicurl queue
     */
    public function setClassForQueue($classContainer) {
        $this->classContainer = $classContainer;
    }

    /**
     * Sets the id's company on the queue
     * 
     * @param number $id It is the id's company on the queue
     */
    public function setIdForQueue($id) {
        $this->idForQueue = $id;
    }

    /**
     * Gets the id for the function of collectUserInvestmentData
     * 
     * @return number The id for the switch
     */
    public function getIdForSwitch() {
        return $this->idForSwitch;
    }

    /**
     * Sets the id for the function of collectUserInvestmentData
     * 
     * @param number $id It is the id for the switch
     */
    public function setIdForSwitch($id) {
        $this->idForSwitch = $id;
    }

    /**
     * Gets the investor's username
     * 
     * @return string It is the investor's username
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the investor's username
     * 
     * @param string $user It is the investor's username
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Gets the investor's password
     * 
     * @return string It is the investor's password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Sets the investor's password
     * 
     * @param string $password It is the investor's password 
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Generate a cookies file per user and per company to keep their cookies
     * @return string It is the name of the cookies file
     */
    public function generateCookiesFile() {
        $randomName = $this->getGUID();
        $randomName = str_replace(array('{', '}', '-'), array(''), $randomName);
        //$randomName = str_replace("{", "", $randomName);
        $nameFileCookies = 'cookies' . $randomName . '.txt';
        $this->createCookiesFile($nameFileCookies);
        return $nameFileCookies;
    }
    
    /**
     * Create the cookies folder if not exists 
     * @param string $name It is the name for the folder
     * @return boolean True if the folder is created
     */
    public function createFolder($name = null, $originPath = null) {
        if (empty($name)) {
            return false;
        }
        if (empty($originPath)) {
            $originPath = dirname(__FILE__);
        }
        $dir = $originPath . DS . $name;
        $folderCreated = false;
        if (!file_exists($dir)) {
            $folderCreated = mkdir($dir, 0770, true);
        }
        else {
            $folderCreated = true;
        }
        if ($folderCreated) {
            return $dir;
        } else {
            return null;
        }
    }
    
    public function createFolderPFPFile() {
        $date = date("Ymd");
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $path = $this->userReference . DS . $date . DS . $this->linkAccountId . DS . $this->companyName ;
        $pathCreated = $this->createFolder($path, $partialPath);
        return $pathCreated;
    }
    
    public function saveControlVariables() {
        $pathCreated = $this->createFolderPFPFile();
        $info = json_encode($this->tempArray);
        $fileName = "controlVariables.json";
        $fp = fopen($pathCreated . DS . $fileName, 'w');
        fwrite($fp, $info);
        fclose($fp);
    }


    /**
     * Create the cookies file inside the directory selected with the permissions selected
     * @param string $nameFile It is the name generated
     */
    public function createCookiesFile($nameFile) {
        if (!file_exists($this->cookiesDir . '/' . $nameFile)) {
            //Be careful with this function because maybe cannot work on Windows
            $fh = fopen($this->cookiesDir . '/' . $nameFile, 'w');
            fclose($fh);
            chmod($this->cookiesDir . '/' . $nameFile, 0770);
            if ($fh) {
                $this->cookies_name = $nameFile;
            }
        } else {
            $this->cookies_name = $nameFile;
        }

        //$nameFile = $this->generatedNameForCookies();
        //or die("Can't create file");
    }

    /**
     * Delete the cookies file generated for the request
     */
    public function deleteCookiesFile() {
        if ($this->cookies_name != "cookies.txt" && file_exists($this->cookiesDir . '/' . $this->cookies_name)) {
            unlink($this->cookiesDir . '/' . $this->cookies_name);
        }
    }

    /**
     * Get the tries to make a the Curl call
     * @return integer It is the number of times what we try to make the curl call
     */
    public function getTries() {
        return $this->tries;
    }

    /**
     * Set the number of tries we spent to get the information by company
     * @param int $tries
     */
    public function setTries($tries) {
        $this->tries = $tries;
    }

    /**
     * Function to get the id that has the company in the queue
     * @return integer
     */
    public function getQueueId() {
        return $this->queueId;
    }

    /**
     * Set the id that has the company in the queue
     * @param integer $queueId
     */
    public function setQueueId($queueId) {
        $this->queueId = $queueId;
    }

    /**
     * Function to show and save error if there is any when taking data of userInvestmentData
     * @param int $line It is the line where the error occurred
     * @param string $file It is the reference of the file where the error occurred
     * @param int $id It is the type of request (WEBPAGE, LOGIN, LOGOUT)
     * @param object $error It is the error that pass the plugin of multicurl
     * @return array It is the principal array with only the error variable
     */
    public function getError($line, $file, $id = null, $error = null) {
        $newLine = "\n";
        $type_sequence = null;
        if (!empty($id)) {
            $type_sequence = "$newLine The sequence is " . $id;
        }
        $error_request = null;
        if (!empty($error)) {
            $error_request = "$newLine The error code of the request: " . $error->getCode()
                    . "$newLine The error message of the request: " . $error->getMessage();
        }
        $this->tempArray['global']['error'] = "ERROR START $newLine"
                . "An error has ocurred with the data on the line " . $line . $newLine . " and the file " . $file
                . "$newLine The queueId is " . $this->queueId
                . "$newLine The error was caused in the urlsequence: " . $this->errorInfo
                . $type_sequence
                . $error_request
                . "$newLine The time is : " . date("Y-m-d H:i:s")
                . "$newLine ERROR FINISHED<br>";
        $errorDetailed = "An error has ocurred with the data on the line " . $line . $newLine . " and the file " . $file
                . ". The queueId is " . $this->queueId['Queue']['id']
                . ". The error was caused in the urlsequence: " . $this->errorInfo
                . " " . $type_sequence
                . " " . $error_request;
        $position = stripos($file, 'companyCodeFiles');
        $substring = substr($file, $position+17);
        $company = explode(".", $substring)[0];
        $dirFile = dirname(__FILE__);
        $this->logToFile("errorCurl", $this->tempArray['global']['error'], $dirFile);
        $this->classContainer->Applicationerror->saveAppError('ERROR: Userinvestmentdata','Error detected in PFP id: ' .  $company . ',' . $errorDetailed, $line, $file, 'Userinvestmentdata');
        return $this->tempArray;
    }

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
     * Function to convert an Spreadsheet to array with PHPExcel
     * @param string $nameSpreadsheet It is the name of the spreadsheet
     * @param string $folderSpreadsheet It is the folder where the spreadsheet is
     */
    function convertExcelToArray($nameSpreadsheet, $folderSpreadsheet) {
        if (empty($nameSpreadsheet)) {
            $nameSpreadsheet = "mintos.xlsx";
        }
        if (empty($folderSpreadsheet)) {
            $folderSpreadsheet = "/var/www/html/cake_branch/";
        }


        $objPHPExcel = PHPExcel_IOFactory::load($folderSpreadsheet . $nameSpreadsheet);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        /* $loadedSheetNames = $objPHPExcel->getSheetNames();
          foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
          echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Raw)</b><br />';
          $objPHPExcel->setActiveSheetIndexByName($loadedSheetName);
          $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, true);
          //var_dump($sheetData);
          echo '<br />';
          } */
        $values = [
            "A" => "TransactionId",
            "B" => "date",
            "C" => [
                [
                    "name" => "interest",
                    "regex" => "Interest income"
                ],
                [
                    "name" => "repayment",
                    "regex" => "Investment principal repayment"
                ],
                [
                    "type" => "loanId",
                    "regex" => "Loan ID",
                    "initPos" => 9,
                    "finalPos" => null
                ]
            ],
            "D" => "turnover",
            "E" => "balance",
            "F" => "currency"
        ];
        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        var_dump($datas);
    }

    /**
     * Function to convert an Spreadsheet to array with PHPExcel by parts
     * @param int $chunkInit
     * @param int $chunkSize
     * @param string $inputFileType
     * @param type $values
     */
    function convertExcelByParts($chunkInit, $chunkSize, $inputFileType, $values) {
        if (empty($inputFileType)) {
            $inputFileType = "Excel2007";
        }
        if (empty($chunkInit)) {
            $chunkInit = 1;
        }
        if (empty($chunkSize)) {
            $chunkSize = 500;
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        /**  Create a new Instance of our Read Filter  * */
        $chunkFilter = new readFilterWinvestify();
        /**  Tell the Read Filter, the limits on which rows we want to read this iteration  * */
        $chunkFilter->setRows($chunkInit, $chunkSize);
        /**  Tell the Reader that we want to use the Read Filter that we've Instantiated  * */
        $objReader->setReadFilter($chunkFilter);

        $objPHPExcel = $objReader->load("/var/www/html/cake_branch/mintos.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        var_dump($datas);
    }

    /**
     * Function to convert from an array with PHPExcel structure to a more manipulable structure
     * @param array $rowDatas It is the array with PHPExcel structure
     * @param array $values They are the variables that will have the new array structure
     * @return array $tempArray It is the array with the new structure
     */
    function saveExcelArrayToTemp($rowDatas, $values) {
        $i = 0;
        $tempArray = [];
        foreach ($rowDatas as $rowData) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    $tempArray[$i] = $this->getValueExcelFromArray($rowData[$key], $value);
                } else {
                    $tempArray[$i][$value] = $rowData[$key];
                }
            }
            $i++;
        }
        return $tempArray;
    }

    /**
     * Function to take more values from cell that could be more than one type of variable
     * @param string $rowData It is the cell  
     * @param array $values It is the possible results that can be on the cell
     * @return array $tempArray with all the data inserted
     */
    function getValueFromDynamicCell($rowData, $values) {
        foreach ($values as $key => $value) {
            $pos = $this->getPosInit($rowData, $value["regex"]);
            //$pos = strpos($rowData, $value["regex"]);
            if ($pos !== false) {
                // " found after position X
                //$tempArray["loanId"] = substr($value, $pos + $variable["initPos"], $variable["finalPos"]);
                if (!empty($value["name"])) {
                    $tempArray["type"] = $value["name"];
                } else {
                    $tempArray[$value["type"]] = $this->getValueBySubstring($rowData, $value, $pos);
                }
            }
        }
        return $tempArray;
    }

    /**
     * Function to get the necessary value with substring function
     * @param string $rowData It is the cell  
     * @param array $value It is an array with the initial position from we must take the value and the final position
     * @param int $pos It is the position from we take the value
     * @return string It is the value
     */
    function getValueBySubstring($rowData, $value, $pos) {
        $posFinal = $this->getPosFinal($rowData, $value, $pos);
        if (empty($posFinal)) {
            $data = substr($rowData, $pos + $value["initPos"]);
        } else {
            $data = substr($rowData, $pos + $value["initPos"], $posFinal);
        }
        return trim($data);
    }

    /**
     * Function to get the initial position to get the variable from a string
     * @param string $rowData It is the cell
     * @param array|string $regex It is the variable to get the initial position of the value
     * @return int It is the position
     */
    function getPosInit($rowData, $regex) {
        if (is_array($regex)) {
            $posStart = strpos($rowData, $regex["init"]);
            $pos = strpos($rowData, $regex["final"], $posStart);
        } else {
            $pos = strpos($rowData, $regex);
        }
        return $pos;
    }

    /**
     * Function to get the final position to get the variable from a string
     * @param string $rowData It is the cell
     * @param string $value It is the variable to get the final position
     * @param int $pos It is the initial position from we init the search
     * @return int It is the final position to get the string
     */
    function getPosFinal($rowData, $value, $pos) {
        $posFinal = null;
        if (!is_int($value["finalPos"])) {
            $positionFinal = strpos($rowData, $value["finalPos"], $pos);
            if ($positionFinal !== false) {
                $posFinal = $positionFinal - $pos - $value["initPos"];
            }
        } else if (is_int($value["finalPos"])) {
            $posFinal = $value["finalPos"];
        }
        return $posFinal;
    }

    /**
     * 
     * @param string $fileUrl url that download the file
     * @param string $fileName name of the file to save
     * @param string $fileType extension of the file
     * @param string $pfpBaseUrl download url referer (like http://www.zank.com.es for zank)
     * @param string $path path where you want save the file
     */
    public function downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, $pfpName, $identity, $credentials, $referer, $cookie1, $cookie2) {

        print_r(http_build_query($credentials));
        echo 'Download: ' . $fileUrl . HTML_ENDOFLINE;

        $date = date("d-m-Y");
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $identity = 'testUser';
        $path = $partialPath . $identity . DS . 'Investments' . DS . $date . DS . $pfpName . DS;

        echo 'Saving in: ' . $path . HTML_ENDOFLINE;



        $output_filename = $fileName . '_' . $date . "." . $fileType;
        echo 'File name: ' . $output_filename . HTML_ENDOFLINE;
        $output_filename = 'prueba.' . $fileType;
        echo $fileUrl . HTML_ENDOFLINE;
        echo $path . $output_filename . HTML_ENDOFLINE;

        $ch = curl_init(); //'cookie: __cfduid=d21a834ccb1e60740448f41c2268cf12e1501673244; PHPSESSID=h3jp268d06961sjlsiiuf8du11; _gat_UA-53926147-5=1; alive=1; _ga=GA1.2.199063307.1501673247; _gid=GA1.2.1698279269.1504852937; __zlcmid=hogdmMCQMh0blo'  
        //--data 'currency=978&+=978&purchased_from=&purchased_till=&statuses%5B%5D=256&statuses%5B%5D=512&statuses%5B%5D=1024&statuses%5B%5D=2048&statuses%5B%5D=8192&statuses%5B%5D=16384&+=256&+=512&+=1024&+=2048&+=8192&+=16384&listed_for_sale_status=&min_interest=&max_interest=&min_term=&max_term=&with_buyback=&min_ltv=&max_ltv=&loan_id=&sort_field=&sort_order=DESC&max_results=20&page=1&include_manual_investments='  --compressed");
        $fp = fopen($path . DS . $output_filename, 'w+');
        if (!file_exists($fp)) {
            echo 'Creating dir' . HTML_ENDOFLINE;
            mkdir($path, 0770, true);
            $fp = fopen($path . DS . $output_filename, 'w+');
        }

        $header[] = 'Accept-language: es-ES,es;q=0.8';
        $header[] = 'Upgrade-insecure-requests: 1';
        $header[] = 'Host: ' . $pfpBaseUrl;
        //$header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
        //$header[] = 'Cookie: LOGIN_USERNAME_COOKIE=' . trim($cookie2) . '; FNZRL_WORLD=' . trim($cookie1) . ';';
        //$header[] = 'authority: ' . $pfpBaseUrl;
        //$header[] = 'cache-control: max-age=0';
        $header[] = 'Connection: keep-alive';
        $header[] = 'Upgrade-Insecure-Requests: 1';
        //$header[] = 'Cookie:LOGIN_USERNAME_COOKIE=kkukovetz%40mli-ltd.com; FNZRL_WORLD=ORA_WWV-ZAgVByw0EpmLmzqlT-HVNunp; _ga=GA1.2.66072991.1505302706; _gid=GA1.2.993900017.1505302706; mp_5cc54fb25fbf8152c17f1bd71396f8fa_mixpanel=%7B%22distinct_id%22%3A%20%22kkukovetz%40mli-ltd.com%22%2C%22%24initial_referrer%22%3A%20%22%24direct%22%2C%22%24initial_referring_domain%22%3A%20%22%24direct%22%7D; mp_mixpanel__c=2';
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        //¡curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, br");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($credentials));
        curl_setopt($ch, CURLOPT_REFERER, $referer); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name); // important
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name); // Important

       /*if($credentials){
          $postString = http_build_query($credentials);
          //set data to be posted
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        }*/
        $result = curl_exec($ch);
        echo "ohgfjkfkhgfAAAAAAAAAAAAAAAAAAAAAAAA";
        print_r($header);

        $redirectURL = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        print_r($result); // prints the contents of the collected file before writing..
        $fichero = fwrite($fp,$result);//False if the file is not created
        //echo "file writed: " . $fichero . HTML_ENDOFLINE;
        fclose($fp);
        
        if ($statusCode == 200 && $fichero) {
            echo 'Downloaded!' . HTML_ENDOFLINE;
        } else {
            echo "Status Code: " . $statusCode . HTML_ENDOFLINE;
        }
    }
    
    /**
     * Function to download a file with multicurl
     * If the referer, credentials or headers are null, it will used from urlSequence, if false, it is not used
     * @param string $url It is the url to download the file
     * @param string $referer They are the referer to download the file
     * @param string $credentials They are the credentials to download the file
     * @param array $headers The headers needed to download the file
     * @param string $fileName It is the name of the file to save with
     */
    public function getPFPFileMulticurl($url = null, $referer = null, $credentials = null, $headers = null, $fileName = null) {

        echo "urls: ";
        print_r($this->urlSequence);
        
        if (empty($url)) {
            $url = array_shift($this->urlSequence);
            //echo $pfpBaseUrl;
        }
        if ($referer !== false && empty($referer)) {
            $referer = array_shift($this->urlSequence);
            //echo $pfpBaseUrl;
        }
        if ($credentials !== false && empty($credentials)) {
            $credentials = array_shift($this->urlSequence);
            //echo $pfpBaseUrl;
        }
        
        if ($headers !== false && empty($headers)) {
            $headersJson = array_shift($this->urlSequence);
            $headers = json_decode($headersJson,true);
        }

        $this->errorInfo = $url;
        echo "File name is " . $fileName;
        
        $pathCreated = $this->createFolderPFPFile();
        //echo 'Saving in: ' . $path . HTML_ENDOFLINE;
        if (empty($pathCreated)) {
            //$path = $partialPath . DS . $path;
            //echo "The path is " . $partialPath . $path;
            echo "url download File: " . $this->errorInfo . " \n";
            echo "Cannot create folder \n";
            //We should implement a method to fail
        }
        
        
        $this->fp = fopen($pathCreated . DS . $fileName, 'w');
        if (!$this->fp) {
            echo "Couldn't created the file \n";
        }

        if (!empty($this->testConfig['active']) == true) {  // test system active, so read input from prepared files
            if (!empty($this->testConfig['siteReadings'])) {
                $currentScreen = array_shift($this->testConfig['siteReadings']);
                echo "currentScreen = $currentScreen";
                $str = file_get_contents($currentScreen);

                if ($str === false) {
                    echo "cannot find file<br>";
                    exit;
                }
                echo "TestSystem: file = $currentScreen<br>";
                return $str;
            }
        }

        $request = new \cURL\Request();
        
        if ($this->config['postMessage'] == true) {
            $request->getOptions()
                    ->set(CURLOPT_POST, true);
            //echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

        if (!empty($headers)) {
            echo "EXTRA HEADERS TO BE ADDED<br>";
            $request->getOptions()
                    ->set(CURLOPT_HTTPHEADER, $headers);

            unset($headers);   // reset fields
        }

        $info = [
            "companyIdForQueue" => $this->idForQueue,
            "idForSwitch" => $this->idForSwitch,
            "typeOfRequest" => "DOWNLOADFILE"
        ];
        
        $request->_page = json_encode($info);
        
        if($credentials){
            //set data to be posted
            $request->getOptions()
                    //->set(CURLOPT_HEADER, true) Esto fue una prueba, no funciona, quitar
                    ->set(CURLOPT_POSTFIELDS, $credentials);
        }
        
        $request->getOptions()
                // Set the file URL to fetch through cURL
                ->set(CURLOPT_URL, $url)
                // Set a different user agent string (Googlebot)
                ->set(CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0')
                // Follow redirects, if any
                ->set(CURLOPT_FOLLOWLOCATION, false)
                // Fail the cURL request if response code = 400 (like 404 errors) 
                ->set(CURLOPT_FAILONERROR, true)
                ->set(CURLOPT_REFERER, $referer)
                //->set(CURLOPT_VERBOSE, 1)
                // Return the actual result of the curl result instead of success code
                ->set(CURLOPT_RETURNTRANSFER, false)
                ->set(CURLOPT_FILE, $this->fp)
                // Wait for 10 seconds to connect, set 0 to wait indefinitely
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                // Execute the cURL request for a maximum of 50 seconds
                ->set(CURLOPT_TIMEOUT, 100)
                ->set(CURLOPT_ENCODING, "gzip,deflate,br")
                // Do not check the SSL certificates
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name) // important
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name); // Important
        //Add the request to the queue in the classContainer controller
        $this->classContainer->addRequestToQueueCurls($request);
    }

  
    /**
     * Transform an array amortization table to a html structure with <table> tag
     * array stricture
     * array (                  //<table>
     *  [0] => array (          //  <tr>
     *      [key1] => value1    //      <td>value1</td> 
     *      [key2] => value2    //      <td>value2</td> 
     *        ...               //       ...
     *      )                   //  </tr>
     *  [1] => ...              // ...
     * )                        //</table>
     * @param array $rows array with amortization table info.
     * @return string string with table structure.
     */
    function arrayToTableConversion($rows) {
        ob_start();
        
        echo "<table>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>$column</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        $table = ob_get_clean();
        
        return $table;
    }
    
    /**
     * Compares two dom structures., attributes name and length 
     * 
     * @param dom $node1 It is the dom node that we get from our DB
     * @param dom $node2 It is the dom node that we get from the website
     * @param array $uniquesElement It is an array with elements that we must not verify
     * @param int $limit It is the limit to read a element of the uniqueElement
     * @return bool True if it is the same structure or false if it is not
     */
    function verifyDomStructure($node1, $node2, $uniquesElement = null, $limit = null) {
        //echo 'Begin comparation<br>';
        $this->sameStructure;
        $repeatedStructureFound = false;

        //echo 'We have' . $node1->nodeName . ' and ' . $node2->nodeName . HTML_ENDOFLINE;
        //We verify if nodes has attributes
        if(!$node1 && !$node2){
            return  $this->sameStructure;
        }
        if ($node1->hasAttributes() && $node2->hasAttributes() && $this->sameStructure) {
            $node1Attr = $node1->attributes;
            $node2Attr = $node2->attributes;
            //If there are attr, we have a foreach to verify if every attr is the same
            if ($node1Attr->length == $node2Attr->length) {
                for ($i = 0; $i < $node1Attr->length; $i++) {
                    $nameAttrNode1 = $node1Attr[$i]->nodeName;
                    $nameAttrNode2 = $node2Attr[$i]->nodeName;
                    $valueAttrNode1 = $node1Attr[$i]->nodeValue;
                    $valueAttrNode2 = $node2Attr[$i]->nodeValue;

                    /* echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
                      echo $nameAttrNode1 . '=>' . $valueAttrNode1 . '<br>';
                      echo $nameAttrNode2 . '=>' . $valueAttrNode2 . '<br>'; */

                    if ($nameAttrNode1 != $nameAttrNode2) {
                        echo 'Node attr name error';
                        $this->same_structure = false;
                    }
                    if ($valueAttrNode1 != $valueAttrNode2) {
                        echo 'Node attr value error';
                        $this->sameStructure = false;
                    }
                    if ($this->sameStructure) {
                        //We verify if the node is repeated with typeUniquelements
                        //If it is a repetitive structure, we don't verify if the node is the same more than once
                        $uniqueStructureFound = $this->nodeRepeated($nameAttrNode1, $valueAttrNode1);
                        if (!empty($uniqueStructureFound) && $uniqueStructureFound > 1) {
                            $repeatedStructureFound = true;
                            break;
                        }
                    }
                }
            } else if ($node1Attr->length != $node2Attr->length) {
                /* echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
                  echo $node1Attr->length . '<br>';
                  echo $node2Attr->length . '<br>';
                  echo 'Node attr length error'; */
                $this->sameStructure = false;
            }
        } else if ($node1->hasAttributes() && !$node2->hasAttributes()) {
            echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
            echo 'Node2 has attr error';
            $this->sameStructure = false;
        } else if (!$node1->hasAttributes() && $node2->hasAttributes()) {
            echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
            echo 'Node1 has attr error';
            $this->sameStructure = false;
        }
        if ($this->sameStructure && !$repeatedStructureFound) {
            if ($node1->hasChildNodes() && $node2->hasChildNodes()) {
                $limit = 0;
                $childrenNode1 = $node1->childNodes;
                $childrenNode2 = $node2->childNodes;
                $limitChildren = $childrenNode1->length;

                for ($i = 0; $i < $limitChildren; $i++) {
                    
                    /*echo 'Children node 1: ' . $i . HTML_ENDOFLINE;
                    var_dump($childrenNode1[$i]);
                    echo 'Children node 2: ' . $i . HTML_ENDOFLINE;
                    var_dump($childrenNode2[$i]);*/
                                                
                    if($childrenNode1[$i]->nodeName == "#text" || $childrenNode2[$i]->nodeName == "#text"){ //Delete text nodes
                        if($childrenNode1[$i]->nodeName == "#text"){
                            //echo 'Deleting text from node 1' . HTML_ENDOFLINE;
                            $childrenNode1[$i]->parentNode->removeChild($childrenNode1[$i]);
                            array_values($childrenNode1);     
                        }
                        if($childrenNode2[$i]->nodeName == "#text"){
                            //echo 'Deleting text from node 2' . HTML_ENDOFLINE;           
                            $childrenNode2[$i]->parentNode->removeChild($childrenNode2[$i]);      
                            array_values($childrenNode2);
                        }
                        $i--;
                        continue;
                    }

                    if($childrenNode1[$i]->nodeName == "#comment" || $childrenNode2[$i]->nodeName == "#comment"){ //Delete comment nodes  
                        //echo 'comment finded in i=' . $i . HTML_ENDOFLINE;
                        if($childrenNode1[$i]->nodeName == "#comment"){
                            //echo 'Deleting comment from node 1' . HTML_ENDOFLINE;
                            $childrenNode1[$i]->parentNode->removeChild($childrenNode1[$i]);
                            array_values($childrenNode1);     
                        }
                        if($childrenNode2[$i]->nodeName == "#comment"){
                            //echo 'Deleting comment from node 2' . HTML_ENDOFLINE;           
                            $childrenNode2[$i]->parentNode->removeChild($childrenNode2[$i]);      
                            array_values($childrenNode2);
                        }
                        $i--;
                        continue;
                    }
                                        
                    if (!$childrenNode1[$i] && $childrenNode2[$i]) { //First we verify if node exist
                        echo 'Node1 doesnt exist, child' . $i . ': <br>';
                        echo 'parent => ' . $childrenNode1[$i]->parentNode->nodeName . ' of ' . $childrenNode1[$i]->nodeName . ' value ' . $childrenNode1[$i]->nodeValue . '<br>';
                        echo 'parent => '  . $childrenNode2[$i]->parentNode->nodeName . ' of ' . $childrenNode2[$i]->nodeName . ' value ' . $childrenNode2[$i]->nodeValue . '<br>';

                        $this->sameStructure = false;
                    } else if($childrenNode1[$i] && !$childrenNode2[$i]){
                        echo 'Node2 doesnt exist, child' . $i . ': <br>';
                        echo $childrenNode1[$i]->parentNode->nodeName . ' ' . $childrenNode1[$i]->nodeName . ' is 1' . $childrenNode1[$i]->nodeValue . '<br>';
                        echo $childrenNode2[$i]->parentNode->nodeName . ' ' . $childrenNode2[$i]->nodeName . ' is 2' . $childrenNode2[$i]->nodeValue . '<br>';

                        $this->sameStructure = false;
                    }

                    if (!$this->sameStructure) {
                        break;
                    }

                    $this->verifyDomStructure($childrenNode1[$i], $childrenNode2[$i], $uniquesElement, $limit);
                }
            } /*else if (!$node1->hasChildNodes() && $node2->hasChildNodes()) {
                echo 'Node has attr error 2';
                $this->sameStructure = false;
            } else if ($node1->hasChildNodes() && !$node2->hasChildNodes()) {
                echo 'Node has attr error 2';
                $this->sameStructure = false;
            }*/
        }
        return $this->sameStructure;
    }

    /**
     * Function to get the repeated nodes
     * @param  $nameAttrNode1
     * @param type $valueAttrNode1
     * @return type
     */
    function nodeRepeated($nameAttrNode1, $valueAttrNode1) {
        $uniqueStructureFound = null;
        for ($i = 0; $i < count($typeUniqueElement); $i++) {
            if ($typeUniqueElement[$i] == $nameAttrNode1) {
                if ($valueUniqueElement[$i] == $valueAttrNode1) {
                    $countUniqueElement[$i] ++;
                    $uniqueStructureFound = $countUniqueElement[$i];
                    break;
                }
            }
        }
        return $uniqueStructureFound;
    }

    /**
     * Function to delete unnecessary elements before we compared the two dom elements
     * @param dom $dom It is the dom to clean
     * @param array $elementsToSearch Elements to search on the dom
     * @param array $attributesToClean Attributes to clean of the dom
     * @return dom $dom Return cleaned dom object
     */
    function cleanDom($dom, $elementsToSearch, $attributesToClean) { //CLEAR ATTRIBUTES
        foreach ($elementsToSearch as $element) {

            $nodes = $this->getElementsToClean($dom, $element["typeSearch"], $element["tag"], $element["value"]);

            foreach ($nodes as $node) {              // Iterate over found elements
                //$this->print_r2($node);
                //print_r($node->attributes);
                foreach ($attributesToClean as $attr) {
                    if ($node->hasAttribute($attr)) {
                        $node->removeAttribute($attr);    // Remove style attribute
                    }
                }
            }
        }
        return $dom;
    }

    /**
     * Function to delete unnecessary tags
     * @param dom $dom 
     * @param array $elementsToDelete
     * @return dom
     */
    function cleanDomTag($dom, $elementsToDelete) { //CLEAR A TAG
        foreach ($elementsToDelete as $element) {
            $nodes = $this->getElementsToClean($dom, $element["typeSearch"], $element["tag"], $element['attr'], $element["value"]);
            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }
        return $dom;
    }

    /**
     * Search the elements to delete in cleanDom and cleanDomTag functions.
     * 
     * @param dom $dom
     * @param string $typeSearch
     * @param string $tag
     * @param string $attribute
     * @param string $value
     * @return array
     */
    public function getElementsToClean($dom, $typeSearch, $tag, $attribute = null, $value = null) {
        $tagTrimmed = trim($tag);
        libxml_use_internal_errors(true);

        if ($typeSearch == "attribute") {
            $xpath = new DOMXPath($dom);            // create a new XPath
            $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@$tagTrimmed), ' '), ' $value ')]");
        } else if ($typeSearch == "element") {
            $elements = $dom->getElementsByTagName($tagTrimmed);
        }
        if ($typeSearch == "tagElement") {
            //echo 'Elements: ';
            $elements = $this->getElements($dom, $tag, $attribute, $value);
        }
        return $elements;
    }
     
    /**
     * 
     * 
     * @param array $structure 
     * @param string $tag elements to compare
     * @param Dom $nodeToClone node that contains the element to compare
     * @param string $attribute attribute of the element to compare
     * @param string $attrValue value of the  attribute of the element to compare
     * @param array $containerData Use it to find a node if you dont have $nodeToClone, contains original dom, a tag, a attribute and his value.
     * @param int $node1Index db node index
     * @param int $node2Index pfp page node index
     * @return array [$structureRevision,$break,$type] $structureRevision - boolean $break - boolean $type - int
     */
    public function htmlRevision($structure, $tag, $nodeToClone, $attribute = null, $attrValue = null, $containerData = null, $node1Index = 1, $node2Index = 3) {

        $break = false; //To break the read loop.
        $type = false; //Error type

        if ($structure) {

            $newStructure = new DOMDocument;  //Get the old structure in db
            $newStructure->loadHTML($structure['Structure']['structure_html']);
            $newStructure->preserveWhiteSpace = false;

            if (!$attribute && !$attrValue) {
                $trsNewStructure = $newStructure->getElementsByTagName($tag);
            } else {
                $trsNewStructure = $this->getElements($newStructure, $tag, $attribute, $attrValue);
            }


            $saveStructure = new DOMDocument(); //CLone original structure in pfp paga
            //print_r($containerData);
            if ($containerData) {
                if ($containerData['attribute'] && $containerData['attrValue']) {
                    $nodeToClone = $this->getElements($containerData['dom'], $containerData['tag'], $containerData['attribute'], $containerData['attrValue'])[0];
                } else {
                    $nodeToClone = $containerData['dom']->getElementsByTagName($containerData['tag'])[0];
                }
            }

            $clone = $nodeToClone->cloneNode(TRUE);
            $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
            $saveStructure->saveHTML();

            if (!$attribute && !$attrValue) {
                $originalStructure = $saveStructure->getElementsByTagName($tag);
            } else {
                $originalStructure = $this->getElements($saveStructure, $tag, $attribute, $attrValue);
            }

            $structureRevision = $this->structureRevision($trsNewStructure[$node1Index], $originalStructure[$node2Index]);

            echo 'structure: ' . $structureRevision . '<br>';

            if (!$structureRevision) { //Save new structure
                echo 'Structural error<br>';
                $saveStructure = new DOMDocument();

                if ($containerData) {
                    if ($containerData['attribute'] && $containerData['attrValue']) {
                        $nodeToClone = $this->getElements($containerData['dom'], $containerData['tag'], $containerData['attribute'], $containerData['attrValue'])[0];
                    } else {
                        $nodeToClone = $containerData['dom']->getElementsByTagName($containerData['tag'])[0];
                    }
                }

                $clone = $nodeToClone->cloneNode(TRUE);
                $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));

                $structureRevision = $saveStructure->saveHTML();
                $break = true; //Stop reading if we have a structural error
                $type = APP_ERROR; //We must sent a error
            }
        }

        if (!$structure) { //Save new structure if is first time
            echo 'no structure readed, saving structure <br>';
            $saveStructure = new DOMDocument();

            if ($containerData) {
                if ($containerData['attribute'] && $containerData['attrValue']) {
                    $nodeToClone = $this->getElements($containerData['dom'], $containerData['tag'], $containerData['attribute'], $containerData['attrValue'])[0];
                } else {
                    $nodeToClone = $containerData['dom']->getElementsByTagName($containerData['tag'])[0];
                }
            }

            $clone = $nodeToClone->cloneNode(TRUE);
            $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
            $structureRevision = $saveStructure->saveHTML();
        }
        return [$structureRevision, $break, $type];
    }

    /**
     * Compares json structure.
     * To success the two json keys must have the same name.
     * Values are not compared.
     * 
     * @param array $structure Structure stored in bd
     * @param array $jsonEntry json entry to compare
     * @return array [$structureRevision,$break,$type] $structureRevision - boolean $break - boolean $type - int
     */
    public function jsonRevision($structure, $jsonEntry) {
        $break = false;
        $type = false;

        if ($structure) { //Compare structures, olny compare the first element
            $compareStructure = json_decode($structure['Structure']['structure_html'], true);
            print_r($compareStructure);
            $structureCountMax = count($compareStructure);
            echo $structureCountMax;
            $jsonEntryCount = count($jsonEntry);
            print_r($jsonEntry);
            echo $jsonEntryCount;

            $structureCount = 0;
            $type = false;

            foreach ($jsonEntry as $key => $value) {
                foreach ($compareStructure as $key2 => $value2) {
                    if ($key == $key2) { //Compares key names
                        $structureCount++;
                        echo 'hecho ' . $structureCount;
                        break;
                    }
                }
            }

            if ($structureCountMax == $structureCount) {
                echo 'structure good';
                $structureRevision = 1;
                if ($structureCountMax < $jsonEntryCount) {
                    $type = INFORMATION;
                }
            } else {
                echo 'structural error';
                $structureRevision = $jsonEntry;
                $totalArray = false;
                $break = true;
                $type = APP_ERROR;
            }
        }

        if (!$structure) {
            $structureRevision = json_encode($jsonEntry);
        }
        return [$structureRevision, $break, $type];
    }
    
    
    /**
     * Search in the pfp marketplace the winvestify marketplace loan id. If we find it we can delete from the array.
     * The array will contain the deleted/hidden invesment that we cant update from the pfp marketplace.
     * @param array $loanReferenceList loan reference id list that we have in our marketplace
     * @param array $investment single investment that we compare
     */
    public function marketplaceLoanIdWinvestifyPfpComparation($loanReferenceList,$investment){  
         foreach($loanReferenceList as $key => $winvestifyMarketplaceLoanId){
            if($winvestifyMarketplaceLoanId == $investment['marketplace_loanReference']){
                echo 'Loan finded, deleting from array' . HTML_ENDOFLINE;
                unset($loanReferenceList[$key]); 
            }
        }
        
        return $loanReferenceList;
    }
    
    /**
     * Function to get the stream open when we download a file
     * @return function fopen It is the stream opened when download a file
     */
    public function getFopen() {
        return $this->fp;
    }
    
    /**
     * Function to set the stream open or close
     * @param fopen $fp It is the stream fopen
     */
    public function setFopen($fp) {
        $this->fp = $fp;
    }
    
    /**
     * Function to get the extension for the files downloaded for a PFP company
     * @return string It is the extension of the file
     */
    function getFileType() {
        return $this->fileType;
    }

    /**
     * Function to set the extension for files downloaded for a PFP company
     * @param string $fileType It is the extension of a file
     */
    function setFileType($typeFileTransaction, $typeFileInvestment) {
        $this->typeFileTransaction = $typeFileTransaction;
        $this->typeFileInvestment = $typeFileInvestment;
    }
    
    /**
     * Function to get the base url of a PFP company
     * @return string It is the base url
     */
    function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Function to set the base url of a PFP company
     * @param string $baseUrl It is the base url
     */
    function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Function to get the user reference that initiates the queue
     * @return string It is the user reference
     */
    function getUserReference() {
        return $this->userReference;
    }

    /**
     * Function to set the user reference
     * @param string $userReference It is the user reference
     */
    function setUserReference($userReference) {
        $this->userReference = $userReference;
    }

    /**
     * Function to get the name of the company
     * @return string It is the name of the company
     */
    function getCompanyName() {
        return $this->companyName;
    }

    /**
     * Function to set the name of the company
     * @param string $companyName It is the name of the company
     */
    function setCompanyName($companyName) {
        $this->companyName = $companyName;
    }
    
    /**
     * Function to get the linkaccount id of the petition
     * @return integer It is the linkaccount id
     */
    function getLinkAccountId() {
        return $this->linkAccountId;
    }

    /**
     * Function to set the linkaccount id
     * @param integer $linkAccountId It is the linkaccount id
     */
    function setLinkAccountId($linkAccountId) {
        $this->linkAccountId = $linkAccountId;
    }
    
    function getLoanIds() {
        return $this->loanIds;
    }

    function setLoandIds($loanIds) {
        $this->loandIds = $loanIds;
    }

    
    
    

    









/** 
 * 
 * 
 * Callback functions required for Dashboard 2. The companycodeFile class can override these methods
 * 
 */

    
    
    
     /** 
     * Callback functions required for Dashboard 2. 
     * The companycodeFile class can override these methods.
     * these callback also exist in case the platform does not support xls/csv file download and the information
     * had to be collected using webscraping
     * The companycodefiles can "delete or modify" any index of the array $fileContent and as such influence "the 
     * process of writing the data to the database. Could be used to delete one or more indices at beginning or 1 or 
     * more at end of array.
     * 
     * @param string $fileName      The filename (as FQDN) which has been analyzed
     * @param string $typeOfFile    the type of file was analyzed, CASHFLOW_FILE, INVESTMENT_FILE, TRANSACTIONTABLE_FILE,.etc.etc
     * @param array $fileContent    The array which contains the result of the parsing of the downloaded file
     * @return  boolean true    All OK, continue with execution
     *                  false   Error Detected, Stop execution 
     */   
    public function fileanalyzed($fileName, $typeOfFile, array $fileContent) {
        return true;
    }


    /** 
     * Callback functions required for Dashboard 2. 
     * The system is ready to construct the list of amortization tables to be downloaded. The default
     * algorithm is to go through the list of indices of $fileContents( = loanId) and check one by one if an entry 
     * exists for the investor. If no entry exists the loanId is added to the list of amortization tables
     * to be collected.
     * If a array is returned then the internal algorithm is bypassed.
     * 
     * @param string $fileName      The filename (as FQDN) which has been analyzed
     * @param string $typeOfFile    the type of file was analyzed, CASHFLOW_FILE, INVESTMENT_FILE, TRANSACTIONTABLE_FILE,.etc.etc
     * @param array $fileContent    The array which contains the result of the parsing of the downloaded file
     * @return  array   list of loanId's to be downloaded
     */ 
    public function beforeamortizationlist(array $fileContent){
         return ;   
    }

    
    /** 
     * Callback functions required for Dashboard 2. 
     * The system has constructed the list of amortization tables to be downloaded. 
     * This callback is only called if one or more amortizationtable(s) need(s) to be downloaded. 
     * Also note that this callback is ALSO called in case the companycodefile has facilitated the list using the
     * "beforeamortizationList" callback function. 
     * 
     * @param array $fileContent    The array which contains the result of the parsing of the downloaded file
     * @return ??
     */ 
    public function afteramortizationlist(array $amortizationtables){
         return ;   
    }    
    

    /** 
     * Callback functions required for Dashboard 2.  
     * All the amortization tables have been downloaded and analyzed and are available in array $amortizationTables. 
     * No processing of the table(s) has yet been done.
     * 
     * @param array $amortizationTables    The array that contains the data of the amortization tables. Main index is
     *                                     the loanId
     * @return  boolean true    All OK, continue with execution
     *                  false   Error Detected, Stop execution 
     */ 
    public function amortizationtablesdownloaded(array $amortizationTables) {
        return true;
    }


    /** 
     * Callback functions required for Dashboard 2. 
     * The main flow loops through all the new loans in which the investor has invested during this data reading period
     * and will calculate the Winvestify normalized loan status 
     * 
     * @param string $loanStatus    Ccontains the data of the amortization tables. Main index is the loanId
     * @return  boolean true    All OK, continue with execution
     *                  false   Error Detected, Stop execution 
     */ 
    public function normalizeLoanStatus($loanStatus) {
        return $loanStatus;
    }

    /** 
     * Callback functions required for Dashboard 2. 
     * The main flow loops through all the new loans in which the investor has invested during this data reading period
     * and will calculate the Winvestify normalized loan rate 
     * 
     * @param string    Contains the data of the amortization tables. Main index is the loanId
     * @return  integer     Loan duration as defined by Winvestify
     */ 
    public function normalizeLoanRate($loanRate) {
        return $loanRate;
    }

    /** 
     * Callback functions required for Dashboard 2. 
     * The main flow loops through all the new loans in which the investor has invested during this data reading period
     * and will calculate the Winvestify normalized loan duration 
     * 
     * @param string $durationString    Contains the data of the amortization tables. Main index is
     *                                  the loanId
     * @return  array $duration  $duration['value']
     *                           $duration['unit']   
     */ 
    public function normalizeLoanDuration($durationString) {
        
        //$amortiza 
        
        return ;
    }


    
    
    
    
    
    
    
    /** 
     * Callback functions required for Dashboard 2. 
     * The table was downloaded in pdf format and its content is available as pure text. This must be converted to
     * html >table> format
     * 
     * @param string $contentsString    Contains the data of the amortization tables. Main index is
     *                                  the loanId
     * @return  boolean  true   All Ok
     *                   false  An error has occurred during the processing
     */ 
    public function amortizationTableDownloaded($contentsString) {
        
    
        
        return ;
    }    
    
    
    /** 
     * Callback functions required for Dashboard 2. 
     * The amortization table has been analyzed 
     * 
     * @param string $durationString    Contains the data of the amortization tables. Main index is
     *                                  the loanId
     * @return  boolean  true   All Ok
     *                   false  An error has occurred during the processing 
     */ 
    public function amortizationTableAnalyzed(array $table) {
        
        //$amortiza 
        
        return ;
    }    
    
    /**
     * Function to start the casper object
     * @param string $url It is the url where casper open initially
     */
    public function casperInit($url = null) {
        if (empty($url)) {
            $url = array_shift($this->urlSequence);
        }
        $this->casperObject = new Casper();
        $this->casperObject->setOptions([
            'ignore-ssl-errors' => 'yes'
        ]);
        // navigate to login web page
        $this->casperObject->start($url);
    }
    
    /**
     * Function to wait for an element to appear for a determined time when open a url 
     * @param string $element It is the element to wait for
     * @param integer $time It is the time we wait for the element to appear in microseconds   
     */
    public function casperWaitSelector($element, $time) {
        $this->casperObject->waitForSelector($element, $time);
    }
    
    /**
     * Function to fill a form
     * @param string $element It is the form element
     * @param array $fillFormArray They are all the elements which needed to be filled
     * @param boolean $submit If it's true, the form is filled and submitted
     */
    public function casperFillForm ($element, $fillFormArray, $submit = false) {
        $this->casperObject->fillForm(
                $element, $fillFormArray, $submit);
    }
    
    /**
     * Function to click an element
     * @param string $element It is the element to click
     */
    public function casperClick ($element) {
        $this->casperObject->click($element);
    }
    
    /**
     * Function to insert a fragment with casperjs code when the wrapper do not give options to accomplish our way in
     * @param FRAGMENT $fragment It is the casperjs code inside a FRAGMENT code
     */
    public function casperAddFragment($fragment) {
        $this->casperObject->addToScript(<<<FRAGMENT
$fragment
FRAGMENT
        );
    }
    
    /**
     * Function to wait for a determined amount of time
     * @param integer $time It is the time we wait in microseconds 
     */
    public function casperWait($time) {
        $this->casperObject->wait($time);
    }
    
    /**
     * Function to run the code we wrote
     */
    public function casperRun() {
        $this->casperObject->run();
    }
    
    /**
     * Function to return the content of the webpage on that moment
     * @return string It is the content of the url at the moment we call the function
     */
    public function casperGetContent() {
        return $this->casperObject->getCurrentPageContent();
    }
    
    public function casperDownloadFile() {
        //Needed to read documentation
        //https://stackoverflow.com/questions/32697172/download-csv-after-clicking-link-using-casperjs
        //https://stackoverflow.com/questions/35037313/casperjs-download-and-save-file-to-specific-location
        //https://stackoverflow.com/questions/16144252/downloading-a-file-that-comes-as-an-attachment-in-a-post-request-response-in-pha/31124037#31124037
        //http://docs.casperjs.org/en/latest/modules/casper.html#download
    }
    
    
    
    
    
    


}
?>

