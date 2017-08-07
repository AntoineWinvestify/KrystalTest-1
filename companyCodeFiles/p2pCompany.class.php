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


2016-08-11		version 2016_0.1
Basic version
function getMonetaryValue							[OK, tested] 
function getPercentage								[OK, tested] 
function getDurationValue							[OK, tested] 
Testing system for "simulating" accesses to the different sites			[OK, tested] 
Added UrlSequence array with all url's to be used for a particular sequence     [OK, tested]


2016-12-12		version 2016_0.2
Error rectified in function "getPercentage". 7,02% was not properly detected.	[OK, tested]


2017-01-28		version 0.3
Adding generic tracing capability 											[OK, NOT tested]


2017-02-14		version 0.4
function "getCurrentAccumulativeRowValue" was updated with the capability	[NOT OK, not tested]
of ONLY adding cuotas realmente pagados.
 
2017-05-16              version 0.5
Added parallelization to collectUserInvestmentData
Added dom verification to collectUserInvestmentData


2017-05-31              version 0.6
Function to save user investment data into DB


2017-06-30              version 0.7
Added function to create an individual cookies file for company when a request and delete after logout


PENDING
 * fix method  getMonetaryValue()
*/

require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'autoload.php');

class p2pCompany{
	const DAY 		= 1;
	const MONTH 		= 2;
	const YEAR_CUARTER 	= 3;
	const HOUR 		= 4;

	
// type of financial product
	const PAGARE 		= 1;
	const LOAN		= 2;
	const FINANCING		= 3;		

	
// http message type for method "getCompanyWebpage"
	const GET 		= 1;	// GET a webpage
	const POST 		= 2;	// POST some parameters, typically used for login procedure
	const PUT 		= 3;	// Not implemented yet)
	const DELETE            = 4;	// DELETE a resource on the server typically used for logging out
	const OPTIONS           = 5;	// Not implemented yet)
	const TRACE		= 6;	// Not implemented yet)
	const CONNECT           = 7;	// Not implemented yet)
	const HEAD 		= 8;	// Not implemented yet)
        
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
	$this->urlSequence = array();				// contains the list of url's to be executed. The url must contain protocol, i.e.
												// http o https.
	$this->tracingDir =  __DIR__ . "/tracings";	// Directory where tracing files are kept
	$this->logDir = __DIR__ . "/log";			// Directory where the log files are stored
	$this->testConfig['active'] = false;		// test system activated	
//	$this->testConfig['siteReadings'] = array('/var/www/compare_local/app/companyCodeFiles/tempTestFiles/lendix_marketplace');
        $this->cookiesDir = dirname(__FILE__). "/cookies"; 
	$this->config['tracingActive'] = false;
	$this->headers = array();


// ******************************** end of configuration parameters *************************************
	mkdir ($this->tracingDir, 0777 );
	mkdir ($this->logDir, 0770);
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
*	Stores all the possible configuration parameters. Provided parameters are merged with the already
*	existing parameters
*	@param array 		$configurationParameters
*	
*	Supported parameters:
*	
*	 PARAMETER		  VALUE
*	messageType                 1 "GET",			Message will be sent as a GET  (Default)
*					2 "DELETE"
*					3 "POST"
*	
*	tracingActive	true/false		Tracing of http messages is active and result is stored in tracing directory
*	traceID		string			String that forms part of the filename which holds the tracing information
*	appDebug	true/false		shows debug messages
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
*	Add extra header(s) to the *next* HTTP messsage which is to be sent.  
*	@param	$headers	array		An array with the text for the extra headers
*									example: array ("sessionToken: $lendixSessionId", "userId: $userId");
*	@return	boolean
*							
*/
function defineHeaderParms($headers) {
	$this->headers = $headers;
	return true;
}





/**
*
*	Stores all the configuration parameters for test purposes
*	@param array 		$testParameters
*			parameters implemented:
*			'siteReadings'	array of files that contain the html files that
*							will be read in sequential order while 'debug' == true
*							Each "site access" will load the first entry in the array
*							and that entry will be deleted after a succesful read.
*							Note that entries can be absolute file names or relative
*							file names. Note that the webserver must have access to
*							the directory where the files are stored.
*							This variable is ONLY used when the test system is ACTIVE
*			'active'		false: 	Testing system not active
*							true:	testing system active 
*/	
function defineTestParms($testParameters) {
	$this->testConfig['active'] = true;
}

public function getTestConfig() {
    return $this->testConfig;
}




/**used by both the investors and the admin user for obtaining marketplace data
*
*	Enter the Webpage of the user's portal
*	@param string 		$url	The url is read from the urlSequence array, i.e. contents of first element
*	@return	string		$str	html string
*
*/
function doCompanyLogin(array $loginCredentials) {

        
	$url = array_shift($this->urlSequence);	
	if (!empty($this->testConfig['active']) == true) {		// test system active, so read input from prepared files
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
	foreach ( $loginCredentials as $key => $value) {
	    $postItems[] = $key . '=' . $value;
	}

//create the final string to be posted using implode()
	$postString = implode ('&', $postItems);	

	$curl = curl_init(); 
    if (!$curl) {
		echo __FILE__ . " " . __LINE__  . "Could not initialize cURL handle for url: " . $url . " \n";
		$msg = __FILE__ . " " . __LINE__  . "Could not initialize cURL handle for url: " . $url . " \n";
		$msg = $msg . " \n";
		$this->logToFile("Warning", $msg);
		exit;
    }
	
// check if extra headers have to be added to the http message  
	if (!empty($this->headers)) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		unset($this->headers);			// reset fields
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
    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir. '/' . $this->cookies_name);		// important
    curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);		// Important

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
		$this->doTracing($this->config['traceID'], "LOGIN" , $str);
	}
	return $str;
}

    



	
/**
*
*	Leave the Webpage of the user's portal. The url is read from the urlSequence array, i.e. contents of first element
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
	if (!empty($this->testConfig['active']) == true) {		// test system active, so read input from prepared files
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
		$msg = __FILE__ . " " . __LINE__  . "Could not initialize cURL handle for url: " . $url . " \n";
		$msg = $msg . " \n";
		$this->logToFile("Warning", $msg);
		exit;
    }

// check if extra headers have to be added to the http message  
	if (!empty($this->headers)) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		unset($this->headers);			// reset fields
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
    
    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);		// important
    curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);		// Important
 
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
		$this->doTracing($this->config['traceID'], "LOGOUT" , $str);
	}
	return($str);
}





function doTracing($tracingID, $action, $messageContent) {
	if (empty($tracingID)) {
		return;						// this is to filter out the messages from marketplace scanning
	}
	
	$fileName = $this->tracingDir . "/" . $tracingID . "_" .date("Y-m-d_H:i:s"). $action . ".html";
	$result = file_put_contents ($fileName , $messageContent);

	return;
}





/**
*
*	Load the received Webpage into a string.
*	If an url is provided then that url is used instead of reading it from the urlSequence array
*	@param string 		$url	The url the connect to
*
*/
function getCompanyWebpage($url) {

	if (empty($url)) {
		$url = array_shift($this->urlSequence);
	}

	if (!empty($this->testConfig['active']) == true) {		// test system active, so read input from prepared files
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
		$msg = __FILE__ . " " . __LINE__  . "Could not initialize cURL handle for url: " . $url . " \n";
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
		unset($this->headers);			// reset fields
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

    $result = curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiesDir . '/' . $this->cookies_name);		// important
    $result = curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiesDir . '/' . $this->cookies_name);		// Important

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
		$this->doTracing($this->config['traceID'], "WEBPAGE" , $str);
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
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir. '/' . $this->cookies_name)
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir. '/' . $this->cookies_name);

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
                ->set(CURLOPT_COOKIEFILE, $this->cookiesDir. '/' . $this->cookies_name)
                ->set(CURLOPT_COOKIEJAR, $this->cookiesDir. '/' . $this->cookies_name);

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
        $request->_page = $this->idForQueue . ";". $this->idForSwitch . ";WEBPAGE";
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
*	Calculates the accumulative values of a column for the actual date and which cuotes have been paid, i.e.
*	not subject to payment delay
*
*	@param	array 	$amortizationTable 	Shall only have rows with valid data, i.e. no headers and footers	
*	@param	string	$date			The date until when the accumulative value needs to be calculated.
*									If current date == $date the value of the corresponding contentRow is added.
*									format of date = yyyy-mm-dd						
*	@param	string	$dateFormat		format of date in table. Formats supported are 'dd-mm-yyyy' and 'dd/mm/yyyy'
*	@param	integer	$dateRow		This is the INDEX which holds the date information		
*	@param	integer	$contentRow		row that contains the values to add
*	@param	integer	$conditionRow	Row that must have value 0 or 1 in order to add the value of $contentRow to $value
*	@return integer	$value			accumulative value of all items in $contentrow. Value in €cents
*/
function getCurrentAccumulativeRowValue($amortizationTable, $date, $dateFormat, $dateRow, $contentRow, $conditionRow) {
	$total = 0;

	$format = array("dd/mm/yy" 		=> "/",
					"dd-mm-yy"		=> "-",
					"dd/mm/yyyy"	=> "/",
					"dd-mm-yyyy"	=> "-");
	
	foreach ($format as $key => $item) {
		if ($key == $dateFormat) {
			$seperator = $item;
			break;
		}
	}

	foreach ($amortizationTable as $v1) {
		$tempCalculatedRowDate = explode($seperator, $v1[$dateRow]);	// Change to internal format, DATE  YYYY-MM-DD
		$calculatedRowDate = $tempCalculatedRowDate[2] . "-" . $tempCalculatedRowDate[1] . "-" . $tempCalculatedRowDate[0];

		if ($calculatedRowDate <= $date)	{		// encountered date is smaller so add value
			if ($v1[$conditionRow] < 2) {			// but only for payment status PENDIENTE or OK
				$total = $total + $this->getMonetaryValue($v1[$contentRow]);
			}
		}
		else {
			break;
		}
	}
	return $total;
}





/**
*
*	Extracts the duration as an integer from an input string
*
*	@param 		string	$inputValue in duration in format like '126d', '126 d', '3 meses', '100 días', ' 5 horas'
*						'quedan 3 meses'
*	@return 	array	[0] contains the number/value
*						[1] contains duration unit as defined. DAY->1, MONTH->2, YEAR_CUARTER -> 3, HOUR -> 4
*								or -1 if no unit defined
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
	return  $value;
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
public function getElementsByClass ($dom, $class) {
    $dom_xpath = new DOMXPath($dom);
    $login = $dom_xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
    return $login;
}




	
/*
*	@param	table		$amortizationTable shall only have rows with valid data, i.e. no headers and footers	
*	@param	integer 	$dateRow	This is the INDEX which holds the date information
*	@param	$dateFormat	format of date in tabe. Formats supported are 'dd-mm-yyyy' and 'dd/mm/yyyy'
*
*	@param $date	highest date encountered in table
*	
*/
function getHighestDateValue($amortizationTable, $dateFormat, $dateRow) {
	$oldCalculatedValueDate = "0000-00-00";

	$total = 0;

	$format = array("dd/mm/yy" 		=> "/",
					"dd-mm-yy"		=> "-",
					"dd/mm/yyyy"	=> "/",
					"dd-mm-yyyy"	=> "-");
	
	foreach ($format as $key => $item) {
		if ($key == $dateFormat) {
			$seperator = $item;
			break;
		}
	}
	
	foreach ($amortizationTable as $v1) {
		$tempNewCalculatedRowDate = explode($seperator, $v1[$dateRow]);	// translate date to internal format -> yyyy-mm-dd
		$calculatedRowDate = $tempNewCalculatedRowDate[2] . "-" .$tempNewCalculatedRowDate[1] . "-" .$tempNewCalculatedRowDate[0];
			if ($calculatedRowDate >= $oldCalculatedValueDate)	{		// encountered date is smaller so add value
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
*	Extracts the amount as an integer from n input string
*       
*	@param 		string	$inputValue in string format like 1,23€ -> 123 and 10.400€ -> 1040000 and 12.235,66€ -> 1223566
*	@return 	int		$outputValue in €cents
*	
*/
function getMonetaryValue($inputValue, $separating = null)  {

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
*	Extracts the percentage as an integer from an input string
*
*	@param 		string	$inputPercentage in string format like 5,4% or 5,43% or 5%. Note that 1,23% generates 123 and 33% -> 3300
*															5,5% TAE -> 550
*															7,02% -> 702
*                                                                                                                   	8,5 % -> 850
 * º                                                            format like 'This is a string 54%' -> 5400
*	@return 	int		$outputPercentage
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
	}
	else {
		return $outputPercentage;
	}
}





/**
*
*	Translates the name of a month in Spanish to its number.
*	@param	string	$monthStr	name of the month, like "mar" or "MARZO"
*	@return	string	$m	numner of month as a 2 character string: "mar" => "03"
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
*	Saves information to a logfile
*
*	@param string	$filename		Name of the logfile to be used
*	@param string	$msg			Content to be logged
*
*/
function logToFile($filename, $msg, $dirFile = "")	{
        //Like this function, change later tomorrow
        //$fileName =  "/var/www/html/cake_branch/app/companyCodeFiles/log/" . $filename;
        $fileName =  $this->logDir . $filename;
        if (!$dirFile == "") {
            $fileName =  $dirFile . "/log/" . $filename;
        }
	$fd = fopen($fileName, "a");
	$msg = date("d-m-y H:i:s") . " " . $msg;  
	fwrite($fd, $msg . "\n");
	fclose($fd); 
}





/**
*
*	Sets the url data for the sequence which is to be started. Any
*	existing data will be overwritten
*
*	@param 	array	urlData		array of all the urls to be loaded
*	@return	boolean			
*
*/
function setUrlSequence($urlSequence){
	$this->urlSequence = $urlSequence;	
}





function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

    /**
    *
    *	Sets the url data for the sequence if something fails. Any
    *	existing data will be overwritten
    *
    *	@param 	array	urlData		array of all the urls to be loaded		
    *
    */
    function setUrlSequenceBackup($urlSequence){
            $this->urlSequenceBackup = $urlSequence;	
    }


     /**
     *	Gets the url backup data for the sequence if something fails.
     *
     *      @return array	urlData		array of all the urls to be loaded
     */
    function getUrlSequenceBackup(){
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
            $fh = fopen($this->cookiesDir . '/' .  $nameFile, 'w');
            fclose($fh);
            chmod($this->cookiesDir . '/' . $nameFile, 0770); 
            if ($fh) {
                $this->cookies_name = $nameFile;
            }
        }
        else {
            $this->cookies_name = $nameFile;
        }
        
        //$nameFile = $this->generatedNameForCookies();
         //or die("Can't create file");
    }
    
    /**
     * Delete the cookies file generated for the request
     */
    public function deleteCookiesFile() {
        if (file_exists($this->cookiesDir . '/' . $this->cookies_name)) {
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
                . "An error has ocurred with the data on the line " . $line . $newLine." and the file " . $file
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


}
?>
