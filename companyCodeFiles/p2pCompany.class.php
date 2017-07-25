<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2016, http://beyond-language-skills.com                 |
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
*
* Base class for all the p2p companies
*
* 
* @author Antoine de Poorter
* @version 0.3
* @date 2017-01-28
* @package


2016-08-11		version 2016_0.1
Basic version
function getMonetaryValue													[OK, tested] 
function getPercentage														[OK, tested] 
function getDurationValue													[OK, tested] 
Testing system for "simulating" accesses to the different sites				[OK, tested] 
Added UrlSequence array with all url's to be used for a particular sequence [OK, tested]


2016-12-12		version 2016_0.2
Error rectified in function "getPercentage". 7,02% was not properly detected.	[OK, tested]


2017-01-28		version 0.3
Adding generic tracing capability 											[OK, NOT tested]


2017-02-14		version 0.4
function "getCurrentAccumulativeRowValue" was updated with the capability	[NOT OK, not tested]
of ONLY adding cuotas realmente pagados.


*/

require_once "../Vendor/autoload.php";		

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

	$this->config['tracingActive'] = false;
	$this->headers = array();


// ******************************** end of configuration parameters *************************************
	mkdir ($this->tracingDir, 0777 );
	mkdir ($this->logDir, 0770);
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
 
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies.txt');		// important
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies.txt');		// Important

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

    $result = curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies.txt');		// important
    $result = curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies.txt');		// Important

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
public function getElements($dom, $tag, $attribute, $value) {

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
}

/**
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
*
*	Extracts the amount as an integer from n input string
*
*	@param 		string	$inputValue in string format like 1,23€ -> 123 and 10.400€ -> 1040000 and 12.235,66€ -> 1223566
*	@return 	int		$outputValue in €cents
*	
*/
function getMonetaryValue($inputValue)  {

	$tempValue = trim(preg_replace('/\D/', '', $inputValue));

	if (stripos($inputValue, ',') === false) {
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
*															8,5 % -> 850
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
function logToFile($filename, $msg)	{
	$fileName = $this->logDir . "/" . $filename;
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


}
?>