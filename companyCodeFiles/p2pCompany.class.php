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
 * PENDING
 * fix method  getMonetaryValue()
 */
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
//Configure::load('constants'); //Load all global constants

class p2pCompany {

    /*const DAY = 1;
    const MONTH = 2;
    const YEAR_CUARTER = 3;
    const HOUR = 4;*/
// type of financial product
    /*const PAGARE = 1;
    const LOAN = 2;
    const FINANCING = 3;*/
// http message type for method "getCompanyWebpage"
    /*const GET = 1; // GET a webpage
    const POST = 2; // POST some parameters, typically used for login procedure
    const PUT = 3; // Not implemented yet)
    const DELETE = 4; // DELETE a resource on the server typically used for logging out
    const OPTIONS = 5; // Not implemented yet)
    const TRACE = 6; // Not implemented yet)
    const CONNECT = 7; // Not implemented yet)
    const HEAD = 8; // Not implemented yet)*/

    //Variable to use in this method
    // MarketplacesController

    protected $marketplaces;
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
        $this->cookiesDir = dirname(__FILE__) . "/cookies";
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
        echo 'login url' . $url . HTML_ENDOFLINE;
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

    /**
     *
     * 	Leave the Webpage of the user's portal. The url is read from the urlSequence array, i.e. contents of first element
     * 	
     */
    function doCompanyLogout() {
        /*
          //traverse array and prepare data for posting (key1=value1)
          foreach ( $logoutData as $key => $value) {
          $postItems[] = $key . '=' . $value;
          }
          //create the final string to be posted using implode()
          $postString = implode ('&', $postItems);
         */
//  barzana@gmail.com 	939233Maco048 
        $url = array_shift($this->urlSequence);
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
    function getCompanyWebpage($url) {

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
    function doCompanyLoginMultiCurl(array $loginCredentials) {

        $url = array_shift($this->urlSequence);
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

        $request = new \cURL\Request();

        // check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            $request->getOptions()
                    ->set(CURLOPT_HTTPHEADER, $this->headers);
            unset($this->headers);   // reset fields
        }

        $request->getOptions()
                ->set(CURLOPT_URL, $url)
                ->set(CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0')
                ->set(CURLOPT_FOLLOWLOCATION, true)
                ->set(CURLOPT_POSTFIELDS, $postString)
                ->set(CURLOPT_FAILONERROR, true)
                ->set(CURLOPT_RETURNTRANSFER, true)
                ->set(CURLOPT_CONNECTTIMEOUT, 30)
                ->set(CURLOPT_TIMEOUT, 100)
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name)
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);

        $request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";" . "LOGIN";
        // Add the url to the queue
        $this->marketplaces->addRequetsToQueueCurls($request);
    }

    /**
     *
     * 	Leave the Webpage of the user's portal. The url is read from the urlSequence array, i.e. contents of first element
     * 	
     */
    function doCompanyLogoutMultiCurl(array $logoutCredentials = null) {
        /*
          //traverse array and prepare data for posting (key1=value1)
          foreach ( $logoutData as $key => $value) {
          $postItems[] = $key . '=' . $value;
          }
          //create the final string to be posted using implode()
          $postString = implode ('&', $postItems);
         */
        //  barzana@gmail.com 	939233Maco048 
        $url = array_shift($this->urlSequence);
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

        $request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";" . "LOGOUT";

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

        $this->marketplaces->addRequetsToQueueCurls($request);
    }

    /**
     *
     * 	Load the received Webpage into a string.
     * 	If an url is provided then that url is used instead of reading it from the urlSequence array
     * 	@param string 		$url	The url the connect to
     *
     */
    function getCompanyWebpageMultiCurl($url) {

        if (empty($url)) {
            $url = array_shift($this->urlSequence);
            echo $url;
        }
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


        if ($this->config['postMessage'] == true) {
            $request->getOptions()
                    ->set(CURLOPT_POST, true);
            //echo " A POST MESSAGE IS GOING TO BE GENERATED<br>";
        }

        // check if extra headers have to be added to the http message  
        if (!empty($this->headers)) {
            echo "EXTRA HEADERS TO BE ADDED<br>";
            $request->getOptions()
                    //->set(CURLOPT_HEADER, true) Esto fue una prueba, no funciona, quitar
                    ->set(CURLOPT_HTTPHEADER, $this->headers);

            unset($this->headers);   // reset fields
        }
        $request->_page = $this->idForQueue . ";" . $this->idForSwitch . ";WEBPAGE";
        $request->getOptions()
                // Set the file URL to fetch through cURL
                ->set(CURLOPT_URL, $url)
                // Set a different user agent string (Googlebot)
                ->set(CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36")
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
        //Add the request to the queue in the marketplaces controller
        $this->marketplaces->addRequetsToQueueCurls($request);

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
    *	Look for ALL elements (or only first) which fullfil the tag item. 
    *	Obtain the following:
    *		<div id="myId"....>      getElements($dom, "div", "id", "myId");
    *		or
    *		<div class="myClass" ....>  getElements($dom, "div", "class", "myClass");
    *		
    *	@param $dom
    *	@param $tag			string 	name of tag, like "div"
    *	@param $attribute	string	name of the attribute like "id"   optional parameter
    *	@param $value		string	value of the attribute like< "myId"  optional parameter. Must be defined if $attribute is defined
    *	@return array $list of doms
    *	$list is empty if no match was found
    *
    */
    /*public function getElements($dom, $tag, $attribute, $value) {

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
    }*/


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
    public function getElements($dom, $tag, $attribute, $value) {

	$list = array();
		
	$attributeTrimmed = trim($attribute);
	$valueTrimmed = trim($value);
	$tagTrimmed = trim($tag);
        libxml_use_internal_errors(true);
	$tags = $dom->getElementsByTagName($tagTrimmed);
	if ($tags->length > 0) {
            foreach ($tags as $tagFound) {
		$attValue = trim($tagFound->getAttribute($attributeTrimmed));
		if ( strncasecmp ($attValue, $valueTrimmed, strlen($valueTrimmed)) == 0) {
			$list[] = $tagFound;	
		}
            }
            $this->hasElements = true;
            return $list;
        }
        else {
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
        } 
        else if (!empty($limit) && $elements->length < $limit) {
            $this->hasElements = false;
        } 
        else {
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
     * Sets the controller that uses the queue
     * 
     * @param object $marketPlacesController It is the controller to be used
     */
    public function setMarketPlaces($marketPlacesController) {
        $this->marketplaces = $marketPlacesController;
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
                . "$newLine The queueId is " . $this->queueId['Queue']['id']
                . "$newLine The error was caused in the urlsequence: " . $this->errorInfo
                . $type_sequence
                . $error_request
                . "$newLine The time is : " . date("Y-m-d H:i:s")
                . "$newLine ERROR FINISHED<br>";
        $dirFile = dirname(__FILE__);
        $this->logToFile("errorCurl", $this->tempArray['global']['error'], $dirFile);
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

    //These are three variables 
    protected $typeUniqueElement = [];
    protected $valueUniqueElement = [];
    protected $verifyUniqueElement = [];
    protected $countUniqueElement = [];
    //This variables are for scanning purpose, if you find this variable, you don't have to scan the node anymore
    protected $typeNotMoreScanning = [];
    protected $valueNotMoreScanning = [];
    protected $sameStructure = true;

    
    /**
     * Compares two dom structures., attributes name and length 
     * 
     * @param dom $node1
     * @param dom $node2
     * @param type $uniquesElement
     * @param int $limit
     * @return bool
     */
    function verifyDomStructure($node1, $node2, $uniquesElement = null, $limit = null) {
        //echo 'Begin comparation<br>';
        $this->sameStructure;
        $repeatedStructureFound = false;

        //echo 'We have' . $node1->nodeName . ' and ' . $node2->nodeName . HTML_ENDOFLINE;
        //We verify if nodes has attributes
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

                   echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
                    echo $nameAttrNode1 . '=>' . $valueAttrNode1 . '<br>';
                    echo $nameAttrNode2 . '=>' . $valueAttrNode2 . '<br>';

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
                echo $node1->tagName . ' / ' . $node2->tagName . '<br>';
                echo $node1Attr->length . '<br>';
                echo $node2Attr->length . '<br>';
                echo 'Node attr length error';
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

                    if($childrenNode1[$i]->nodeName == "#text" || $childrenNode2[$i]->nodeName == "#text"){ //Skip text nodes
                        continue;
                    }
                    
                    if (!$childrenNode1[$i] && $childrenNode2[$i]) { //First we verify if node exist
                        echo 'Node1 doesnt exist: <br>';
                        echo 'parent => ' . $childrenNode1[$i]->parentNode->nodeName . ' of ' . $childrenNode1[$i]->nodeName . ' value ' . $childrenNode1[$i]->nodeValue . '<br>';
                        echo 'parent => '  . $childrenNode2[$i]->parentNode->nodeName . ' of ' . $childrenNode2[$i]->nodeName . ' value ' . $childrenNode2[$i]->nodeValue . '<br>';

                        $this->sameStructure = false;
                    } else if($childrenNode1[$i] && !$childrenNode2[$i]){
                        echo 'Node2 doesnt exist: <br>';
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
     * 
     * @param type $nameAttrNode1
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
     * @param dom $dom
     * @param array $elementsToSearch
     * @param array $attributesToClean
     * @return dom
     */
    function cleanDom($dom, $elementsToSearch, $attributesToClean) { //CLEAR ATTRIBUTES
        //https://stackoverflow.com/questions/35534654/php-domdocument-delete-elements
        //https://duckduckgo.com/?q=delete+attributes+dom+element+php+dom&t=canonical&ia=qa	
        //$dom = new DOMDocument;                 // init new DOMDocument
        //$dom->loadHTML($html);                  // load HTML into it
        //$xpath = new DOMXPath($dom);            // create a new XPath
        //$nodes = $xpath->query('//*[@style]');  // Find elements with a style attribute

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
     *  Function to delete unnecessary  tags
     * @param dom $dom
     * @param array $elementsToDelete
     * @return dom
     */
    function cleanDomTag($dom, $elementsToDelete) { //CLEAR A TAG
        foreach ($elementsToDelete as $element) {
            $nodes = $this->getElementsToClean($dom, $element["typeSearch"], $element["tag"], $element['attr'], $element["value"]);
            //echo 'Nodes: <br>';
            //print_r($nodes);
            foreach ($nodes as $node) {
                //echo 'Delete: <br>';
                //print_r($node);
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
       /* echo 'Type: ' . $typeSearch . '<br>';
        echo 'Tag: ' . $tag . '<br>';
        echo 'Attribute: ' . $attribute . '<br>';
        echo 'Value: ' . $value . '<br>';*/

        /* $list = array();
          $attributeTrimmed = trim($attribute); */
        $tagTrimmed = trim($tag);
        libxml_use_internal_errors(true);

        if ($typeSearch == "attribute") {
            $xpath = new DOMXPath($dom);            // create a new XPath
            $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@$tagTrimmed), ' '), ' $value ')]");
            //$elements = $xpath->query('//*[@style]');  // Find elements with a style attribute
        } else if ($typeSearch == "element") {
            $elements = $dom->getElementsByTagName($tagTrimmed);
        }
        if ($typeSearch == "tagElement") {
            //echo 'Elements: ';
            $elements = $this->getElements($dom, $tag, $attribute, $value);
            //print_r($elements);
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
    public function  htmlRevision($structure,$tag,$nodeToClone, $attribute = null, $attrValue = null, $containerData = null, $node1Index = 1, $node2Index = 3){
        
        $break = false; //To break the read loop.
        $type = false; //Error type
        
        if($structure){
            
            $newStructure = new DOMDocument;  //Get the old structure in db
            $newStructure->loadHTML($structure['Structure']['structure_html']);
            $newStructure->preserveWhiteSpace = false;
            
            if(!$attribute && !$attrValue){
                $trsNewStructure = $newStructure->getElementsByTagName($tag);
            }else{
                $trsNewStructure = $this->getElements($newStructure, $tag, $attribute, $attrValue);
            }
            
            
            $saveStructure = new DOMDocument(); //CLone original structure in pfp paga
            
            //print_r($containerData);
            if($containerData){
                if($containerData['attribute'] && $containerData['attrValue']){
                    $nodeToClone = $this->getElements($containerData['dom'], $containerData['tag'], $containerData['attribute'], $containerData['attrValue'])[0];
                } else {
                    $nodeToClone = $containerData['dom']->getElementsByTagName($containerData['tag'])[0];
                }
            }
            
            $clone = $nodeToClone->cloneNode(TRUE);
            $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
            $saveStructure->saveHTML();
            
            if(!$attribute && !$attrValue){
                $originalStructure = $saveStructure->getElementsByTagName($tag);
             }else{
                $originalStructure = $this->getElements($saveStructure, $tag, $attribute, $attrValue);
            }
            
            $structureRevision = $this->structureRevision($trsNewStructure[$node1Index], $originalStructure[$node2Index]);

            echo 'structure: ' . $structureRevision . '<br>';

            if (!$structureRevision) { //Save new structure
                echo 'Structural error<br>';
                $saveStructure = new DOMDocument();
                
                if($containerData){
                    if($containerData['attribute'] && $containerData['attrValue']){
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
            
            if($containerData){
                if($containerData['attribute'] && $containerData['attrValue']){
                        $nodeToClone = $this->getElements($containerData['dom'], $containerData['tag'], $containerData['attribute'], $containerData['attrValue'])[0];
                    } else {
                        $nodeToClone = $containerData['dom']->getElementsByTagName($containerData['tag'])[0];
                    }
            }
            
            $clone = $nodeToClone->cloneNode(TRUE);
            $saveStructure->appendChild($saveStructure->importNode($clone, TRUE));
            $structureRevision = $saveStructure->saveHTML();
        }
        return [$structureRevision,$break,$type];
    }


    
    /**
     * Compares json structure.
     * To success the two json keys must have the same name.
     * Values are not compared.
     * 
     * @param array $structure Structure stroed in bd
     * @param array $jsonEntry json entry to compare
     * @return array [$structureRevision,$break,$type] $structureRevision - boolean $break - boolean $type - int
     */
    public function jsonRevision($structure, $jsonEntry){
        $break = false;
        $type = false;
        
        if ($structure) { //Compare structures, olny compare the first element
            $compareStructure = json_decode($structure['Structure']['structure_html'],true);
            print_r($compareStructure);
            $structureCountMax = count($compareStructure);
            echo  $structureCountMax;
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
                if($structureCountMax < $jsonEntryCount){
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
        return [$structureRevision,$break,$type];
    }
    
    
    /**Search in the pfp marketplace the winvestify marketplace loan id. If we find it we can delete from the array.
     * The array will contain the deleted/hidden invesment that we cant update from the pfp marketplace.
     * @param array $loanReferenceList loan reference id list that we have in our marketplace
     * @param array $investment single investment that we compare
     */
    public function marketplaceLoanIdWinvestifyPfpComparation($loanReferenceList,$investment){  
        print_r($investment);
        print_r($loanReferenceList);
         foreach($loanReferenceList as $key => $winvestifyMarketplaceLoanId){
            if($winvestifyMarketplaceLoanId == $investment['marketplace_loanReference']){
                echo 'Loan finded, deleting from array' . HTML_ENDOFLINE;
                unset($loanReferenceList[$key]); 
            }
        }
        
        return $loanReferenceList;
    }
    

}

?>
