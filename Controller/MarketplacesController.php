<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 * 
 *  2016-08-05		version 0.1
 * basic version
 *
 * Remove all records which no longer appear in company market places, because they were		[OK, tested]
 * fully subscribed or were not fully subscribed during the publication phase
 *
 * function getNext																			[OK, tested]
 *
 *
 *
 * 2016-12-12		version 0.2
 * Added new field: "marketplace_origCreated" in database backup. At the same time rectified error:
 *  missing records in the marketplace_backup DB. -> added Model->clear in function "backupRecord"	[OK, tested]
 *
 *
 * 2017-01-12
 * added functions xxx yyy zzz . These are AJAX functions which the browser sends for requesting Dashboard data
 *
 * 2017-05-02      version 0.3                                                                     [OK, tested]
 * Removed initLoad and replaced with $this->getGeoLocationData in function getGlobalMarketPlaceData()
 *
 * 2017-08-01
 * cronMarketPlaceLoop remake
 * cronMarketPlaceHistorical new function
 * 
 * 2017-08-10
 * Status fixing
 * 
 * 2017-08-11
 * Status definition
 * Type definition
 * 
 * 2017-08-29
 * Application error message type
 *
 * Pending:
 * Checking for "country of residence" and show only marketplace for that country				[Not OK]
 * Send an email in case of error while writing to the database in function cronMarketPlaceLoop
 * function cronMarketStart: check the issue of "country"
 * function "storeBetaTester": check the validility of email address using "mailgun.com" email verifier interface


  2016-08-05		version 0.1
  basic version

  Remove all records which no longer appear in company market places, because they were		[OK, tested]
  fully subscribed or were not fully subscribed during the publication phase

  function getNext																			[OK, tested]



  2016-12-12		version 0.2
  Added new field: "marketplace_origCreated" in database backup. At the same time rectified error:
  missing records in the marketplace_backup DB. -> added Model->clear in function "backupRecord"	[OK, tested]


  2017-01-12
  added functions xxx yyy zzz . These are AJAX functions which the browser sends for requesting Dashboard data

  2017-05-02      version 0.3                                                                     [OK, tested]
  Removed initLoad and replaced with $this->getGeoLocationData in function getGlobalMarketPlaceData()


  2017-08-09    version 0.31
  Rectified Error 0009 as reported in Mantis.
  "incorrect calculation of average yield in case one or more platforms don't have any active investment
  Added new index counter variable $platformsZeroYield
 

  Pending:
  Checking for "country of residence" and show only marketplace for that country				[Not OK]
  Send an email in case of error while writing to the database in function cronMarketPlaceLoop
  function cronMarketStart: check the issue of "country"
  function "storeBetaTester": check the validility of email address using "mailgun.com" email verifier interface
 */
App::uses('CakeEvent', 'Event');
App::uses('CakeTime', 'Utility');
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');

class MarketPlacesController extends AppController {

    var $name = 'Marketplaces';
    var $helpers = array('Html', 'Form', 'Js');
    var $uses = array('Marketplace', 'Company', 'Urlsequence', 'Marketplacebackup');
    var $components = array('Security', 'Session');
    private $queueCurls;
    private $newComp = array();
    private $tempArray = array();
    private $companyId = array();

    function beforeFilter() {

        parent::beforeFilter(); // only call if the generic code for all the classes is required.
//	$this->Security->blackHoleCallback = '_blackHole'; 
//
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
         */
//	$this->Security->requireAuth();
        $this->Auth->allow(array('cronMarketStart', 'listMarketPlace', 'getGlobalMarketPlaceData',
            'readInvestmentData', 'readGlobalDashboardData', 'cronQueueEvent',
            'test_linkingAccount', 'cronQueueEventParallel'));
    }

    /**
     *
     * Provides some basic data to be used on the landing page
     *
     */
    function getGlobalMarketPlaceData() {

        $this->layout = 'winvestify_publicLandingPageLayout';
        $userIp = $this->request->clientIp($safe = false);      // To avoid that the user manipulates the HTTP_CLIENT_IP header.
        $geoData = $this->getGeoLocationData($userIp);          // Where is the user?

        $countryCode = $geoData['country_code'];
        $filterConditions = array('Company.company_country' => $countryCode, 'company_showInGlobalMarketplace' => 1);

        $results = $this->Company->getCompanyList($filterConditions);
        $filterConditions = array('company_id' => $results);
        $globalResults = $this->Marketplace->getGlobalMarketData($filterConditions);
        $this->set('globalResults', $globalResults);
    }

    /**
     *
     * Stores the location data of the user in the session
     *
     */
    function location11() {
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

// Store the location data in the user session
        foreach ($_REQUEST as $key => $value) {
            $data[$key] = $value;
            $this->Session->write("locationData." . $key, $data[$key]);
        }
    }

    /**
     *
     * Read all the GLOBAL dashboard data of an investor. This also includes
     * a list of all the companies where s/he has active investments and the globals per company
     *
     */
    function readGlobalDashboardData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $this->layout = 'ajax';
        $this->disableCache();

        $dashboardGlobals = $this->Session->read('dashboardGlobals');
        foreach ($dashboardGlobals['investments'] as $key => $investment) {
            $listOfCompanies[] = $key;
        }

        foreach ($dashboardGlobals['investments'] as $key => $element) {
            unset($dashboardGlobals['investments'][$key]['investments']);
        }
        $this->set('globalDashboardData', $dashboardGlobals);
    }

    /**
     *
     * Read the individual investment data of an investor for his/her dashboard
     *
     */
    function readInvestmentData($company) {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $this->layout = 'ajax';
        $this->disableCache();

        $companyInvestmentDetails = $this->Session->read('dashboardGlobals');
        $this->set('companyInvestmentDetails', $companyInvestmentDetails['investments'][$company]['investments']);
    }

    /**
     *
     * Shows a list of ALL the investments which are currently "open" for investing of
     * the country where the user "resides"	
     * 	They can be ordered in company alphabetical order, with company x on top
     * 	
     */
//function listMarketPlace($filterCondition) {
    function listMarketPlace() {
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->disableCache();
        } else {
            $this->layout = 'compare_public_layout';
            $this->pageTitle = "MarketPlace";
        }

        $locationData = $this->Session->read('locationData');
        $locationData['country_code'] = "ES";
        $filterConditions = array('Company.company_country' => $locationData['country_code'], 'company_showInGlobalMarketplace' => 1);

        $companyResults = $this->Company->getCompanyDataList($filterConditions);
        $this->set('companyResults', $companyResults);

        $filterConditions = array('company_showInGlobalMarketplace' => 1);
        $CompanyIdresults = $this->Company->getCompanyList($filterConditions);

        $filterConditions = array('company_id' => $CompanyIdresults);
        $marketPlaceResults = $this->Marketplace->getMarketplaceDataList($filterConditions);
        $this->set('marketPlaceResults', $marketPlaceResults);
    }

    /**
     *
     * Shows a list of ALL the investments which are currently "open" for investing of
     * the country where the user "resides"
     * This is the *non-public* version
     * 	They can be ordered in company alphabetical order, with company x on top
     * 	
     */
    function showMarketPlace() {
        $this->layout = 'azarus_private_layout';
        $this->pageTitle = "MarketPlace";

        $country_code = $this->Session->read('locationData.country_code');
        $filterConditions = array(/* 'Company.company_country' => $country_code, */ 'company_showInGlobalMarketplace' => 1);
        $results = $this->Company->getCompanyList($filterConditions);

        $filterConditions = array('company_id' => $results);
        $globalResults = $this->Marketplace->getGlobalMarketData($filterConditions);
        $this->set('globalResults', $globalResults);
    }

    /**
     *
     * ********************************************************************************************
     * CRONTAB OPERATIONS
     * ********************************************************************************************
     */

    /**
     *
     * 	cycles through ALL known and registered p2p companies and stores all the new found marketplaces
     * 	and marketplaces which were changed since the last reading, for instance  Number of investors
     *
     */
// start the cronjob
    function cronMarketStart($type = 1) {


        $this->autoRender = false;
        Configure::write('debug', 2);
        $this->Structure = ClassRegistry::init('Structure');


        $country = "ES";

        $filterConditions = array('Company.company_country' => $country);
        $companyDataResult = $this->Company->getCompanyDataList($filterConditions);

        $index = 0;
        $this->temp = array();

// Create linked list with an array of all valid company id's
        foreach ($companyDataResult as $result) {
            $this->temp[$index]['id'] = $result['id'];
            if ($index == 0) {
                $this->temp[$index]['next'] = -1;
            } else {
                $this->temp[$index - 1]['next'] = $index;
            }
            $index++;
        }
        $this->temp[$index - 1]['next'] = 0;

        $conditions = array('Configuration.id' => 1);
        $this->Configuration = ClassRegistry::init('Configuration');

        $lastScanned = $this->Configuration->find("first", $params = array('recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('lastScannedCompany')
                )
        );

        $lastScannedCompany = $lastScanned['Configuration']['lastScannedCompany'];
        $companyList = $this->getNext($lastScannedCompany);
        $this->print_r2($companyList);


        foreach ($companyList as $companyId) {
            $this->Configuration->writeConfigParameter('lastScannedCompany', $companyId);
            echo "companyId = $companyId <br>";
            $structure = $this->Structure->getStructure($companyId, 1);
            //print_r($structure);
            if ($type == 1) {
                $this->cronMarketPlaceLoop($companyId, $structure);
            } else if ($type == 2) {
                $this->cronMarketPlaceHistorical($companyId, $structure, $companyDataResult[$companyId]['company_hasMultiplePages']);
            }
        }
    }

    /**
     *
     * Obtains all the open investments for a company
     * 
     *
     */
    function cronMarketPlaceLoop($companyId, $structure) {
        $this->autoRender = false;
        $this->Structure = ClassRegistry::init('Structure');
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        //Configure::write('debug', 2);

        $companyConditions = array('Company.id' => $companyId);
        $result = $this->Company->getCompanyDataList($companyConditions);

        pr($result);
        echo "<br>____ Checking Company " . $result[$companyId]['company_name'] . " ____<br>";

        $companyMarketplace = $this->Marketplace->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1));
        $companyBackup = $this->Marketplacebackup->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1, 'limit' => 1000));
        
        $loanIdList = array(); //This array contains the loan_reference of each investment, we need it to search in the pfp marketplace if the investment have been deleted. (COMUNITAE delete some finished investmenet)
        foreach($companyMarketplace as $ivestment){
            $loanId = $ivestment['Marketplace']['marketplace_loanReference'];
            array_push($loanIdList, $loanId);
        }
        
        $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.	
        $newComp->defineConfigParms($result[$companyId]);

        $companyId = $result[$companyId]['id'];
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MARKETPLACE_SEQUENCE);

        $this->print_r2($urlSequenceList);
        $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $marketplaceArray = $newComp->collectCompanyMarketplaceData($companyBackup, $structure,$loanIdList);


        //echo 'Marketplace Result: ';
        //$this->print_r2($marketplaceArray);


        if ($marketplaceArray[1] && $marketplaceArray[1] != 1) {
            echo 'Saving new structure';
            $this->Structure->saveStructure(array('company_id' => $companyId, 'structure_html' => $marketplaceArray[1], 'structure_type' => 1));
            if ($marketplaceArray[2] == APP_ERROR) {
                $this->Applicationerror->saveAppError('ERROR: Html/Json Structure','Html/Json structural error detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            } else if($marketplaceArray[2] == WARNING) {
                $this->Applicationerror->saveAppError('WARNING: Html/Json Structure','Html/Json structural change detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            } else if($marketplaceArray[2] == INFORMATION) {
                $this->Applicationerror->saveAppError('INFORMATION: Html/Json Structure','Html/Json structural change detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            }
            
        }
        $marketplaceArray[0] = array_unique( $marketplaceArray[0]);

        foreach ($marketplaceArray[0] as $investment) { //Read the investment
            $DontExist = true;
            $backup = true;
            $investment['company_id'] = $companyId;

            foreach ($companyMarketplace as $marketplaceInvestment) {

                if ($investment['marketplace_loanReference'] == $marketplaceInvestment['Marketplace']['marketplace_loanReference']) { //If exist in winvestify marketplace
                    $DontExist = false;
                    echo "Investment already exist<br>";
                    $investment['marketplace_investmentCreationDate'] = $marketplaceInvestment['Marketplace']['marketplace_investmentCreationDate'];
                    if ($investment['marketplace_subscriptionProgress'] == 10000 || $investment['marketplace_status'] == PERCENT || $investment['marketplace_status'] == CONFIRMED || $investment['marketplace_status'] == REJECTED) { //If is completed
                        echo "Investment completed<br>";

                        //Delete from maketplace
                        $this->Marketplace->delete($marketplaceInvestment['Marketplace']['id']);

                        //Save complete in backup
                        $investment['marketplace_origCreated'] = $marketplaceInvestment['Marketplace']['created'];
                        $this->Marketplacebackup->create();
                        $this->Marketplacebackup->save($investment);
                        echo __FUNCTION__ . __LINE__ . "PFP Investment saving:<br>";
                        $this->print_r2($investment);
                        continue;
                    } else { //If isn't completed
                        echo "Investment incompleted<br>";
                        //Save in backup
                        $investment['marketplace_origCreated'] = $marketplaceInvestment['Marketplace']['created'];
                        $this->Marketplacebackup->create();
                        $this->Marketplacebackup->save($investment);
                        echo __FUNCTION__ . __LINE__ . "PFP Investment saving:<br>";
                        $this->print_r2($investment);
                        unset($investment['marketplace_origCreated']);

                        //Replace in marketplace
                        $investment['id'] = $marketplaceInvestment['Marketplace']['id'];
                        $this->Marketplace->save($investment);
                        continue;
                    }
                }
            }

            if ($DontExist) {//If not exist in winvestify marketplace
                if ($investment['marketplace_subscriptionProgress'] == 10000 || $investment['marketplace_status'] == PERCENT || $investment['marketplace_status'] == CONFIRMED || $investment['marketplace_status'] == REJECTED) { //If it is completed
                    echo "Investment completed<br>";
                    foreach ($companyBackup as $investmentBackup) {
                        //print_r($investmentBackup);
                        if ($investment['marketplace_loanReference'] == $investmentBackup['Marketplacebackup']['marketplace_loanReference']) { //If it exist in winvestify backup
                            $backup = false;
                            $investment['marketplace_investmentCreationDate'] = $investmentBackup['Marketplacebackup']['marketplace_investmentCreationDate'];
                            if ($investment['marketplace_status'] == $investmentBackup['Marketplacebackup']['marketplace_status']) { //Same status
                                echo 'Ignore<br>';
                                //Ignore
                                continue;
                            }
                        }
                    } if ($backup) { //If it not exist in winvestify backup
                        $date = new DateTime();
                        $investment['marketplace_investmentCreationDate'] = $date->format('Y-m-d H:i:s');

                        //Save in backup
                        $this->Marketplacebackup->create();
                        $this->Marketplacebackup->save($investment);
                        echo __FUNCTION__ . __LINE__ . "PFP Investment saving:<br>";
                        $this->print_r2($investment);
                        continue;
                    }
                } else {  //If isn't completed
                    echo "Investment incompleted<br>";

                    $date = new DateTime();
                    $investment['marketplace_investmentCreationDate'] = $date->format('Y-m-d H:i:s');

                    //Add to marketplace
                    $this->Marketplace->create();
                    $this->Marketplace->save($investment);

                    //Add to Backup
                    $this->Marketplacebackup->create();
                    $this->Marketplacebackup->save($investment);
                    echo __FUNCTION__ . __LINE__ . "PFP Investment saving:<br>";
                    $this->print_r2($investment);
                    continue;
                }
            }
        }
    }

    /* Collect all invesment of the user, open and closed */

    function cronMarketPlaceHistorical($companyId, $structure, $hasMultplePages) {
        $this->autoRender = false;
        $this->Structure = ClassRegistry::init('Structure');
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        //Configure::write('debug', 2);
        $repeat = true; //Read another page
        $start = 0; //For pagination
        if ($hasMultplePages) {
            $type = PROMISSORY_NOTE; //Is definead as 1
        }

        $companyConditions = array('Company.id' => $companyId);
        $result = $this->Company->getCompanyDataList($companyConditions);

        pr($result);
        echo "<br>____ Checking Company " . $result[$companyId]['company_name'] . " ____<br>";

        $companyMarketplace = $this->Marketplace->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1));
        $companyBackup = $this->Marketplacebackup->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1));

        $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.	
        $newComp->defineConfigParms($result[$companyId]);

        $companyId = $result[$companyId]['id'];


        while ($repeat != false) {

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, HISTORICAL_SEQUENCE);
            $this->print_r2($urlSequenceList);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence


            $marketplaceArray = $newComp->collectHistorical($structure, $start, $type); //$start is for pfp with paginations, $type is for comunitae.


            if ($marketplaceArray[3] && $marketplaceArray[3] != 1) {
                echo 'Saving new structure';
                $this->Structure->saveStructure(array('company_id' => $companyId, 'structure_html' => $marketplaceArray[3], 'structure_type' => 1));
                if ($marketplaceArray[4] == APP_ERROR) {
                    $this->Applicationerror->saveAppError('ERROR: Html/Json ','Html/Json structural error detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Historical read');
                }else if($marketplaceArray[4] == WARNING) {
                    $this->Applicationerror->saveAppError('WARNING: Html/Json Structure','Html/Json structural change detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
                } else if($marketplaceArray[4] == INFORMATION) {
                    $this->Applicationerror->saveAppError('INFORMATION: Html/Json Structure','Html/Json structural change detected in Pfp id: ' .  $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
                }
            }


            echo 'RESULTADO: ';
            $this->print_r2($marketplaceArray);
            foreach ($marketplaceArray[0] as $investment) {
                $investment['company_id'] = $companyId;
                $date = new DateTime();
                $investment['marketplace_investmentCreationDate'] = $date->format('Y-m-d H:i:s');
                echo __FUNCTION__ . __LINE__ . "PFP Backup Saving:<br>";
                $this->print_r2($investment);
                $this->Marketplacebackup->create();
                $this->Marketplacebackup->save($investment);
            }

            $start = $marketplaceArray[1];
            $repeat = $marketplaceArray[1];
            if ($hasMultplePages) {
                $type = $marketplaceArray[2];
            }
        }
    }

    /**
     *
     * 	Will backup a db record with reference $id and delete $id from "original" database
     * 	
     * 	@param 		string	$id		Database record reference (= id)
     * 	@return 	true	DB record saved in backup DB and original deleted
     * 				false	Error while saving to backup DB or deleting original
     *
     */
    public function backupRecord($id) {
        Configure::write('debug', 2);
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . " id = $id<br>";

        $this->Marketplacebackup = ClassRegistry::init('Marketplacebackup');
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
        $result = $this->Marketplace->find("first", $params = array('recursive' => -1,
            'conditions' => array('id' => $id),
        ));

        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "  backup of id = $id<br>";
        pr($result);
        $result['Marketplace']['marketplace_origCreated'] = $result['Marketplace']['created'];
        unset($result['Marketplace']['id']);
        unset($result['Marketplace']['created']);
        unset($result['Marketplace']['modified']);

        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
        pr($result);
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "Clearing of any possible old data<br>";
        $this->Marketplacebackup->clear();

        if ($this->Marketplacebackup->save($result['Marketplace'], $validate = TRUE)) {
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "  Delete id = $id from original DB<br>";
            $this->Marketplace->delete($id);
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * 	get n entries from a linked list starting from $current index
     * 	n = configuration parameter
     * 	
     * 	@param 		integer	$current	current index
     * 	@return 	array	$companyIdList	List of id (one or more)
     *
     */
    function getNext($current) {
        $requests = Configure::read('numberOfSimultaneousMarketplaceRequests');
        $found = false;

// Does value exist in array?
        $index = 0;

        foreach ($this->temp as $value) {
            if ($value['id'] == $current) {
                $found = true;
                break;
            }
            $index++;
        }
        if ($found) {
            $startIndex = $value['next'];
        } else {
            $startIndex = 0;
        }

        for ($i = 0; $i < $requests; $i++) {
            $companyIdLinkedList[] = $this->temp[$startIndex]['id'];
            $startIndex = $this->temp[$startIndex]['next'];
        }
        return $companyIdLinkedList;
    }

    /**
     *
     * 	Initiates the collection of the investment data in parallel of all linked accounts of the investor. The result is stored
     * 	as a JSON object in databasetable "datas"
     *
     */
    function cronQueueEventParallel() {

        $this->autoRender = false;
        Configure::write('debug', 2);

        $this->queueCurls = new \cURL\RequestsQueue;
        //If we use setQueueCurls in every class of the companies to set this queueCurls it will be the same?
        $this->Data = ClassRegistry::init('Data');     // needed for storing 
        $this->Applicationerror = ClassRegistry::init('Applicationerror');

        $userInvestment = array();
        $result = array();

        $this->Queue = ClassRegistry::init('Queue');
        $resultQueue = $this->Queue->getNextFromQueue(FIFO);

        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }

// Get internal database reference of the investor
        $this->Investor = ClassRegistry::init('Investor');
        $resultInvestor = $this->Investor->find("first", array('conditions' =>
            array('Investor.investor_identity' => $resultQueue['Queue']['queue_userReference']),
            'fields' => 'id',
            'recursive' => -1,
        ));
        $investorId = $resultInvestor['Investor']['id'];

//	***************************************************************************
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');

        $filterConditions = array('investor_id' => $investorId);
        $linkedaccountsResults = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
        $platformsZeroYield = 0;
        $index = 0;
        $i = 0;
        foreach ($linkedaccountsResults as $linkedaccount) {
            echo "<br>******** Executing the loop **********<br>";
            $index++;
            $this->companyId[$i] = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = " . $this->companyId[$i] . " <br>";
            $companyConditions = array('Company.id' => $this->companyId[$i]);
            $result[$i] = $this->Company->getCompanyDataList($companyConditions);
            $this->newComp[$i] = $this->companyClass($result[$i][$this->companyId[$i]]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $this->newComp[$i]->defineConfigParms($result[$i][$this->companyId[$i]]);  // Is this really needed??
            $this->newComp[$i]->setMarketPlaces($this);
            $this->newComp[$i]->setQueueId($resultQueue);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$i], MY_INVESTMENTS_SEQUENCE);
            $this->newComp[$i]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $this->newComp[$i]->setUrlSequenceBackup($urlSequenceList);  // It is a backup if something fails
            $this->newComp[$i]->generateCookiesFile();
            $this->newComp[$i]->setIdForQueue($i); //Set the id of the company inside the loop
            $this->newComp[$i]->setIdForSwitch(0); //Set the id for the switch of the function company
            $this->newComp[$i]->setUser($linkedaccount['Linkedaccount']['linkedaccount_username']); //Set the user on the class
            $this->newComp[$i]->setPassword($linkedaccount['Linkedaccount']['linkedaccount_password']); //Set the pass on the class
            $configurationParameters = array('tracingActive' => true,
                'traceID' => $resultQueue['Queue']['queue_userReference'],
            );
            $this->newComp[$i]->defineConfigParms($configurationParameters);
            $i++;
        }

        $companyNumber = 0;
        echo "MICROTIME_START = " . microtime() . "<br>";
        //We start at the same time the queue on every company
        foreach ($linkedaccountsResults as $linkedaccount) {
            $this->newComp[$companyNumber]->collectUserInvestmentDataParallel();
            $companyNumber++;
        }

        /*
         * This is the callback's queue for the companies cURLs, when one request is processed
         * Another enters the queue until finishes
         */
        $this->queueCurls->addListener('complete', function (\cURL\Event $event) {
            echo "<br>";
            // The ids[0] is the company id
            // The ids[1] is the switch id
            // The ids[2] is the type of request (WEBPAGE, LOGIN, LOGOUT)
            $ids = explode(";", $event->request->_page);
            //We get the response of the request
            $response = $event->response;
            //We get the web page string
            $str = $response->getContent();
            $error = "";
            //if (!empty($this->testConfig['active']) == true) {
            echo 'CompanyId:' . $this->companyId[$ids[0]] .
            '   HTTPCODE:' . $response->getInfo(CURLINFO_HTTP_CODE)
            . '<br>';

            if ($response->hasError()) {
                $this->errorCurl($response->getError(), $ids, $response);
                $error = $response->getError();
            } else {
                echo "<br>";
                //}
                //if ($this->config['tracingActive'] == true) {
                // $this->doTracing($this->config['traceID'], "WEBPAGE", $str);
                //}
                if ($ids[2] != "LOGOUT") {
                    $this->newComp[$ids[0]]->setIdForSwitch($ids[1]);
                    $this->tempArray[$ids[0]] = $this->newComp[$ids[0]]->collectUserInvestmentDataParallel($str);
                }
            }

            if ($response->hasError() && $error->getCode() == CURL_ERROR_TIMEOUT && $this->newComp[$ids[0]]->getTries() == 0) {
                $this->logoutOnCompany($ids, $str);
                $this->newComp[$ids[0]]->setIdForSwitch(0); //Set the id for the switch of the function company
                $this->newComp[$ids[0]]->setUrlSequence($this->newComp[$ids[0]]->getUrlSequenceBackup());  // provide all URLs for this sequence
                $this->newComp[$ids[0]]->setTries(1);
                $this->newComp[$ids[0]]->deleteCookiesFile();
                $this->newComp[$ids[0]]->generateCookiesFile();
                $this->newComp[$ids[0]]->collectUserInvestmentDataParallel();
            } 
            else if ($ids[2] == "LOGOUT") {
                echo "LOGOUT FINISHED <br>";
                $this->newComp[$ids[0]]->deleteCookiesFile();
            } 
            else if ((!empty($this->tempArray[$ids[0]]) || ($response->hasError()) && $ids[2] != "LOGOUT")) {
                if ($response->hasError()) {
                    //$this->tempArray[$ids[0]]['global']['error'] = "An error has ocurred with the data" . __FILE__ . " " . __LINE__;
                    $this->newComp[$ids[0]]->getError(__LINE__, __FILE__, $ids[2], $error);
                }
                $this->logoutOnCompany($ids, $str);
                if ($ids[2] == "LOGOUT") {
                    unset($this->tempArray['global']['error']);
                }
            }
        });

        //This is the queue. It is working until there are requests
        while ($this->queueCurls->socketPerform()) {
            echo '*';
            $this->queueCurls->socketSelect();
        }

        echo "FINISHED MICROTIME_STOP = " . microtime() . "<br>";
        for ($i = 0; $i < count($this->tempArray); $i++) {
            if (!empty($this->tempArray[$i]['global']['error'])) {
                echo $this->tempArray[$i]['global']['error'];
                unset($this->tempArray[$i]);
            }
        }
        $companyNumber = 0;
        for ($i = 0; $i < count($this->tempArray); $i++) {

            //$tempArray = $this->newComp[$i]->collectUserInvestmentData($linkedaccount['Linkedaccount']['linkedaccount_username'], $linkedaccount['Linkedaccount']['linkedaccount_password']);

            /* $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);
              $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
              $newComp->companyUserLogout(); */
            /*             * ************************************ */
            echo "PHOTO MICROTIME_START = " . microtime() . "<br>";
            if (empty($this->tempArray[$i]['global']['error'])) {
                $this->tempArray[$i]['companyData'] = $result[$i][$this->companyId[$i]];

                $userInvestments = $this->tempArray[$i];
                $result1 = array_merge($userInvestment['investments'], $this->tempArray[$i]);
                echo "RESULT1 = ";
                $this->print_r2($result1);
                //prepare all globals on total dashboard level	
                //			$dashboardGlobals['amountInvested']	= $dashboardGlobals['amountInvested'] + $userInvestments['global']['activeInInvestments'];
                $dashboardGlobals['amountInvested'] = $dashboardGlobals['amountInvested'] + $userInvestments['global']['totalInvestment'];
                $dashboardGlobals['wallet'] = $dashboardGlobals['wallet'] + $userInvestments['global']['myWallet'];
                $dashboardGlobals['totalEarnedInterest'] = $dashboardGlobals['totalEarnedInterest'] + $userInvestments['global']['totalEarnedInterest'];

// Mantis error: 0000009  date: 2017-09-12              
// Note that we only take values of the platforms that have a yield <> 0. 
// In theory it is possible to have some investments with positive yield and some
// with negative yield, making the total result = 0. In this case the ""Yield" = 0 MUST be taken into consideration when calculating the global yield
// for the user.
// Conclusion. We don't only look at yield = 0, but also if active investments exist.
                $dashboardGlobals['profitibilityAccumulative'] = $dashboardGlobals['profitibilityAccumulative'] + $userInvestments['global']['profitibility'];
                if ($userInvestments['global']['profitibility'] == 0) {
                    if (count($userInvestments['investments']) == 0) {      // Only discard yield if user has NO active investments in platform
                        $platformsZeroYield = $platformsZeroYield + 1;
                    }
                }

                // Amount that was invested totally in all the currently active investments
                $dashboardGlobals['totalInvestments'] = $dashboardGlobals['totalInvestments'] + $userInvestments['global']['totalInvestments'];

                // The number of active investments in all companies:
                $dashboardGlobals['activeInvestments'] = $dashboardGlobals['activeInvestments'] + count($userInvestments['investments']);

                $dashboardGlobals['investments'][$result[$i][$this->companyId[$i]]['company_name']] = $userInvestments;

                // *********************************************************************************************************		
                // Save "intermediate photos", so investor will always see something. The result is that for a user who has
                // investments in 4 platforms, the system will generate 4 photos, with each photo including the previous one
                // *********************************************************************************************************

                $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / $index);
                if ($this->Data->save(array('data_investorReference' => $resultQueue['Queue']['queue_userReference'],
                            'data_JSONdata' => JSON_encode($dashboardGlobals),
                            $validate = true))) {
                    $companyNumber++;
                    echo "WRITE AN INTERMEDIATE PHOTO OF INVESTMENTS OF USER <br>";
                } else {
                    // log error
                }
            } else {
                echo $this->tempArray[$i];
                unset($this->tempArray[$i]);
            }

            echo "<br>******* End of Loop ****** <br>";
        }

        if ($index == $platformsZeroYield) {                               
            $dashboardGlobals['meanProfitibility'] = 0;
        }
        else {
            $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / ($index - $platformsZeroYield));
        }
        
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
        $this->print_r2($dashboardGlobals);

// Store the dashboard data for 
        $this->Data = ClassRegistry::init('Data');
        if ($this->Data->save(array('data_investorReference' => $resultQueue['Queue']['queue_userReference'],
                    'data_JSONdata' => JSON_encode($dashboardGlobals),
                    $validate = true))) {
            if (count($linkedaccountsResults) == $companyNumber) {
                return true;
            }
        } else {
            // log error
            return false;
        }
    }

    /**
     *
     * 	Initiates the collection of the investment data of all linked accounts of the investor. The result is stored
     * 	as a SON object in databasetable "datas"
     *
     */
    function cronQueueEvent($queueType) {

        $this->autoRender = false;
        Configure::write('debug', 2);

        $this->Data = ClassRegistry::init('Data');     // needed for storing 


        $userInvestment = array();

        $this->Queue = ClassRegistry::init('Queue');
        $resultQueue = $this->Queue->getNextFromQueue(FIFO);

        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }

// Get internal database reference of the investor
        $this->Investor = ClassRegistry::init('Investor');
        $resultInvestor = $this->Investor->find("first", array('conditions' =>
            array('Investor.investor_identity' => $resultQueue['Queue']['queue_userReference']),
            'fields' => 'id',
            'recursive' => -1,
        ));
        $investorId = $resultInvestor['Investor']['id'];

//	***************************************************************************
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');

        $filterConditions = array('investor_id' => $investorId);
        $linkedaccountsResults = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);

        $index = 0;
        foreach ($linkedaccountsResults as $linkedaccount) {
            unset($newComp);
            $index = $index + 1;
            echo "<br>******** Executing the loop **********<br>";
            $companyId = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = $companyId <br>";
            $companyConditions = array('Company.id' => $companyId);

            $result = $this->Company->getCompanyDataList($companyConditions);

            $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $newComp->defineConfigParms($result[$companyId]);  // Is this really needed??

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MY_INVESTMENTS_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence

            $configurationParameters = array('tracingActive' => false,
                'traceID' => $resultQueue['Queue']['queue_userReference'],
            );

            $newComp->defineConfigParms($configurationParameters);


            echo "MICROTIME_START = " . microtime() . "<br>";
            $tempArray = $newComp->collectUserInvestmentData($linkedaccount['Linkedaccount']['linkedaccount_username'], $linkedaccount['Linkedaccount']['linkedaccount_password']);

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);
            $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
            $newComp->companyUserLogout();

            echo "MICROTIME_STOP = " . microtime() . "<br>";
            $tempArray['companyData'] = $result[$companyId];

            $userInvestments = $tempArray;
            $result1 = array_merge($userInvestment['investments'], $tempArray);
            echo "RESULT1 = ";
            $this->print_r2($result1);
//prepare all globals on total dashboard level	
//			$dashboardGlobals['amountInvested']	= $dashboardGlobals['amountInvested'] + $userInvestments['global']['activeInInvestments'];
            $dashboardGlobals['amountInvested'] = $dashboardGlobals['amountInvested'] + $userInvestments['global']['totalInvestment'];
            $dashboardGlobals['wallet'] = $dashboardGlobals['wallet'] + $userInvestments['global']['myWallet'];
            $dashboardGlobals['totalEarnedInterest'] = $dashboardGlobals['totalEarnedInterest'] + $userInvestments['global']['totalEarnedInterest'];
            $dashboardGlobals['profitibilityAccumulative'] = $dashboardGlobals['profitibilityAccumulative'] + $userInvestments['global']['profitibility'];

// Amount that was invested totally in all the currently active investments
            $dashboardGlobals['totalInvestments'] = $dashboardGlobals['totalInvestments'] + $userInvestments['global']['totalInvestments'];

// The number of active investments in all companies:
            $dashboardGlobals['activeInvestments'] = $dashboardGlobals['activeInvestments'] + count($userInvestments['investments']);

            $dashboardGlobals['investments'][$result[$companyId]['company_name']] = $userInvestments;
            unset($newComp);

// *********************************************************************************************************		
// Save "intermediate photos", so investor will always see something. The result is that for a user who has
// investments in 4 platforms, the system will generate 4 photos, with each photo including the previous one
// *********************************************************************************************************
            $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / $index);
            if ($this->Data->save(array('data_investorReference' => $resultQueue['Queue']['queue_userReference'],
                        'data_JSONdata' => JSON_encode($dashboardGlobals),
                        $validate = true))) {
                // DO NOTHING
                echo "WRITE AN INTERMEDIATE PHOTO OF INVESTMENTS OF USER <br>";
            } else {
                // log error
            }

            foreach ($dashboardGlobals['investments'] as $company => $value) {
                $inversiones = count($dashboardGlobals['investments'][$company]['investments']);
                echo '<h1>';
                print_r($inversiones);
                echo '</h1>';
                for ($key = 0; $key < $inversiones; $key++) {
                    echo "comprobando" . $key . "</br>";
                    if ($dashboardGlobals['investments'][$company]['investments'][$key]['status'] == -1) {
                        echo '<h1>' . $key . "eliminada</h1></br>";
                        unset($dashboardGlobals['investments'][$company]['investments'][$key]);
                        $dashboardGlobals['investments'][$company]['global']['investments'] --;
                        $dashboardGlobals['activeInvestments'] --;
                        continue;
                    }
                }
                $dashboardGlobals['investments'][$company]['investments'] = array_values($dashboardGlobals['investments'][$company]['investments']);
            }
            echo "<h1>            aqui";
            $this->print_r2($dashboardGlobals);
            echo "</h1>";

            echo "<br>******* End of Loop ****** <br>";
        }

        $dashboardGlobals['meanProfitibility'] = (int) ($dashboardGlobals['profitibilityAccumulative'] / $index);
        echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
        $this->print_r2($dashboardGlobals);

// Store the dashboard data for 
        $this->Data = ClassRegistry::init('Data');
        if ($this->Data->save(array('data_investorReference' => $resultQueue['Queue']['queue_userReference'],
                    'data_JSONdata' => JSON_encode($dashboardGlobals),
                    $validate = true))) {
            
        } else {
            // log error
        }
    }

    public function clearCache() {
        $this->autoRender = false;

        Cache::clear();
        clearCache();
        $files = array();
        $files = array_merge($files, glob(CACHE . '*')); // remove cached css
        $files = array_merge($files, glob(CACHE . 'css' . DS . '*')); // remove cached css
        $files = array_merge($files, glob(CACHE . 'js' . DS . '*'));  // remove cached js           
        $files = array_merge($files, glob(CACHE . 'models' . DS . '*'));  // remove cached models           
        $files = array_merge($files, glob(CACHE . 'persistent' . DS . '*'));  // remove cached persistent           

        foreach ($files as $f) {
            if (is_file($f)) {
                unlink($f);
            }
        }

        if (function_exists('apc_clear_cache')):
            apc_clear_cache();
            apc_clear_cache('user');
        endif;

        $this->set(compact('files'));
        $this->layout = 'ajax';
        echo "cache eliminada";
    }

    /**
     * Function to do logout of company
     * @param array $ids They are the ids of the company
     * @param string $str It is the webpage on string format
     */
    function logoutOnCompany($ids, $str) {
        $urlSequenceList = $this->Urlsequence->getUrlsequence($this->companyId[$ids[0]], LOGOUT_SEQUENCE);
        //echo "Company = $this->companyId[$ids[0]]";
        $this->newComp[$ids[0]]->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $this->newComp[$ids[0]]->companyUserLogoutMultiCurl($str);
    }

    /**
     * Function to process if there is an error with the request on parallel
     * @param object $error It is the curl error
     * @param array $ids They are the ids of the company
     * @param object $response It is the curl response from the request on parallel
     */
    function errorCurl($error, $ids, $response) {
        echo
        'Error code: ' . $error->getCode() . "<br>" .
        'Message: "' . $error->getMessage() . '" <br>';
        echo 'CompanyId:' . $this->companyId[$ids[0]] . '<br>';
        $testConfig = $this->newComp[$ids[0]]->getTestConfig();
        if (!empty($testConfig['active']) == true) {
            print_r($response->getInfo());
            echo "<br>";
        }
        $config = $this->newComp[$ids[0]]->getConfig();
        if ($config['tracingActive'] == true) {
            $this->newComp[$ids[0]]->doTracing($config['traceID'], $ids[2], $str);
        }
    }

    /*
     * 
     * Get the variable queueCurls
     */

    public function getQueueCurls() {
        return $this->queueCurls;
    }

    /**
     * 
     * Add a request to the queue to initiate the multi_curl
     * @param type $request It's the request to process
     */
    public function addRequestToQueueCurls($request) {
        $this->queueCurls->attach($request);
    }

    public function importBackupExcel() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        $currentDateTime = date('Y-m-d_H:i:s');

        // $filter = $this->request->params['filters'];
        $filter = null;
        $backup = $this->Marketplacebackup->getBackup($filter);
        //$this->print_r2($backup);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("BackupExcel");


        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(13);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(28);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'company_id')
                ->setCellValue('C1', 'marketplace_amount')
                ->setCellValue('D1', 'marketplace_amountTotal')
                ->setCellValue('E1', 'marketplace_duration')
                ->setCellValue('F1', 'marketplace_durationUnit')
                ->setCellValue('G1', 'marketplace_category')
                ->setCellValue('H1', 'marketplace_rating')
                ->setCellValue('I1', 'marketplace_interestRate')
                ->setCellValue('J1', 'marketplace_purpose')
                ->setCellValue('K1', 'marketplace_status')
                ->setCellValue('L1', 'marketplace_statusLiteral')
                ->setCellValue('M1', 'marketplace_timeLeft')
                ->setCellValue('N1', 'marketplace_timeLeftUnit')
                ->setCellValue('O1', 'marketplace_name')
                ->setCellValue('P1', 'marketplace_loanReference')
                ->setCellValue('Q1', 'marketplace_subscriptionProgress')
                ->setCellValue('R1', 'marketplace_sector')
                ->setCellValue('S1', 'marketplace_requestorLocation')
                ->setCellValue('T1', 'marketplace_numberOfInvestors')
                ->setCellValue('U1', 'marketplace_origCreated')
                ->setCellValue('V1', 'marketplace_productType')
                ->setCellValue('W1', 'marketplace_country')
                ->setCellValue('X1', 'marketplace_investmentCreationDate')
                ->setCellValue('Y1', 'created');


        $i = 2;
        foreach ($backup as $row) {

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $row['Marketplacebackup']['id'])
                    ->setCellValue('B' . $i, $row['Marketplacebackup']['company_id'])
                    ->setCellValue('C' . $i, $row['Marketplacebackup']['marketplace_amount'])
                    ->setCellValue('D' . $i, $row['Marketplacebackup']['marketplace_amountTotal'])
                    ->setCellValue('E' . $i, $row['Marketplacebackup']['marketplace_duration'])
                    ->setCellValue('F' . $i, $row['Marketplacebackup']['marketplace_durationUnit'])
                    ->setCellValue('G' . $i, $row['Marketplacebackup']['marketplace_category'])
                    ->setCellValue('H' . $i, $row['Marketplacebackup']['marketplace_rating'])
                    ->setCellValue('I' . $i, $row['Marketplacebackup']['marketplace_interestRate'])
                    ->setCellValue('J' . $i, $row['Marketplacebackup']['marketplace_purpose'])
                    ->setCellValue('K' . $i, $row['Marketplacebackup']['marketplace_status'])
                    ->setCellValue('L' . $i, $row['Marketplacebackup']['marketplace_statusLiteral'])
                    ->setCellValue('M' . $i, $row['Marketplacebackup']['marketplace_timeLeft'])
                    ->setCellValue('N' . $i, $row['Marketplacebackup']['marketplace_timeLeftUnit'])
                    ->setCellValue('O' . $i, $row['Marketplacebackup']['marketplace_name'])
                    ->setCellValue('P' . $i, $row['Marketplacebackup']['marketplace_loanReference'])
                    ->setCellValue('Q' . $i, $row['Marketplacebackup']['marketplace_subscriptionProgress'])
                    ->setCellValue('R' . $i, $row['Marketplacebackup']['marketplace_sector'])
                    ->setCellValue('S' . $i, $row['Marketplacebackup']['marketplace_requestorLocation'])
                    ->setCellValue('T' . $i, $row['Marketplacebackup']['marketplace_numberOfInvestors'])
                    ->setCellValue('U' . $i, $row['Marketplacebackup']['marketplace_origCreated'])
                    ->setCellValue('V' . $i, $row['Marketplacebackup']['marketplace_productType'])
                    ->setCellValue('W' . $i, $row['Marketplacebackup']['marketplace_country'])
                    ->setCellValue('X' . $i, $row['Marketplacebackup']['marketplace_investmentCreationDate'])
                    ->setCellValue('Y' . $i, $row['Marketplacebackup']['created']);

            $i++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BackupMarketplace_' . $currentDateTime . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

}
