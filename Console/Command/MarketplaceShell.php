<?php

require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
App::uses('CakeEvent', 'Event');
App::uses('CakeTime', 'Utility');

class MarketplaceShell extends AppShell {

    var $helpers = array('Html', 'Form', 'Js');
    public $uses = array('Marketplace', 'Marketplacebackup', 'Structure', 'Company', 'Applicationerror', 'Urlsequence');
    var $components = array('Security', 'Session');
    private $queueCurls;
    private $newComp = array();
    private $tempArray = array();
    private $companyId = array();

    public function main() {
        $this->out('Hello world.');
    }

    function cronMarketStart() {


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


        foreach ($companyList as $companyId) {
            $this->Configuration->writeConfigParameter('lastScannedCompany', $companyId);
            $structure = $this->Structure->getStructure($companyId, WIN_STRUCTURE_MARKETPLACE);

            if ($this->args[0] == 1) {
                $this->cronMarketPlaceLoop($companyId, $structure);
            } else if ($this->args[0] == 2) {
                $this->cronMarketPlaceHistorical($companyId, $structure, $companyDataResult[$companyId]['company_hasMultiplePages']);
            }
        }
        $this->out( SHELL_ENDOFLINE . 'MARKETPLACE READING FINISHED' . SHELL_ENDOFLINE);
    }

    /**
     *
     * Obtains all the open investments for a company
     * 
     *
     */
    function cronMarketPlaceLoop($companyId, $structure) {

        $companyConditions = array('Company.id' => $companyId);
        $result = $this->Company->getCompanyDataList($companyConditions);

        $companyMarketplace = $this->Marketplace->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1));
        $companyBackup = $this->Marketplacebackup->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1, 'limit' => 1000));

        $loanIdList = array(); //This array contains the loan_reference of each investment, we need it to search in the pfp marketplace if the investment have been deleted. (COMUNITAE delete some finished investmenet)
        foreach($companyMarketplace as $ivestment){
            $loanId = $ivestment['Marketplace']['marketplace_loanReference'];
            array_push($loanIdList, $loanId);
        }
        
        
        $this->out(print_r($result[$companyId]['company_codeFile']) . " " . SHELL_ENDOFLINE);
        $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.	
        $newComp->defineConfigParms($result[$companyId]);

        $companyId = $result[$companyId]['id'];
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MARKETPLACE_SEQUENCE);

        $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $marketplaceArray = $newComp->collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList);


        if ($marketplaceArray[1] && $marketplaceArray[1] != 1) {
            echo 'Saving new structure';
            $this->Structure->saveStructure(array('company_id' => $companyId, 'structure_html' => $marketplaceArray[1], 'structure_type' => 1));
            if ($marketplaceArray[2] == APP_ERROR) {
                $this->Applicationerror->saveAppError('ERROR: Html/Json Structure', 'Html/Json structural error detected in Pfp id: ' . $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            } else if ($marketplaceArray[2] == WARNING) {
                $this->Applicationerror->saveAppError('WARNING: Html/Json Structure', 'Html/Json structural change detected in Pfp id: ' . $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            } else if ($marketplaceArray[2] == INFORMATION) {
                $this->Applicationerror->saveAppError('INFORMATION: Html/Json Structure', 'Html/Json structural change detected in Pfp id: ' . $companyId . ', html structure has changed.', null, __FILE__, 'Marketplace read');
            }
        }


        foreach ($marketplaceArray[0] as $investment) {
            $DontExist = true;
            $backup = true;
            $investment['company_id'] = $companyId;

            foreach ($companyMarketplace as $marketplaceInvestment) {

                if ($investment['marketplace_loanReference'] == $marketplaceInvestment['Marketplace']['marketplace_loanReference']) { //If exist in winvestify marketplace             
                    $DontExist = false; // "Investment already exist";
                    $investment['marketplace_investmentCreationDate'] = $marketplaceInvestment['Marketplace']['marketplace_investmentCreationDate'];
                    if ($investment['marketplace_subscriptionProgress'] == 10000 || $investment['marketplace_status'] == PERCENT || $investment['marketplace_status'] == CONFIRMED || $investment['marketplace_status'] == REJECTED) { //If is completed
                        // "Investment completed";
                        //Delete from maketplace
                        $this->Marketplace->delete($marketplaceInvestment['Marketplace']['id']);

                        //Save complete in backup
                        $investment['marketplace_origCreated'] = $marketplaceInvestment['Marketplace']['created'];
                        $this->Marketplacebackup->create();
                        $this->Marketplacebackup->save($investment);
                        continue;
                    } else { //If isn't completed
                        // "Investment incompleted<br>";
                        //Save in backup
                        $investment['marketplace_origCreated'] = $marketplaceInvestment['Marketplace']['created'];
                        $this->Marketplacebackup->create();
                        $this->Marketplacebackup->save($investment);
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
                    // "Investment completed<br>";
                    foreach ($companyBackup as $investmentBackup) {
                        $backup = false;
                        $investment['marketplace_investmentCreationDate'] = $investmentBackup['Marketplacebackup']['marketplace_investmentCreationDate'];
                        if ($investment['marketplace_loanReference'] == $investmentBackup['Marketplacebackup']['marketplace_loanReference']) { //If it exist in winvestify backup
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
                    continue;
                }
            }
        }
    }

    /* Collect all invesment of the user, open and closed */

    function cronMarketPlaceHistorical($companyId, $structure, $hasMultplePages) {

        $repeat = true; //Read another page
        $start = 0; //For pagination
        if ($hasMultplePages) {
            $type = PROMISSORY_NOTE; //Is definead as 1
        }

        $companyConditions = array('Company.id' => $companyId);
        $result = $this->Company->getCompanyDataList($companyConditions);

        $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.	
        $newComp->defineConfigParms($result[$companyId]);

        $companyId = $result[$companyId]['id'];


        while ($repeat != false) {

            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, HISTORICAL_SEQUENCE);
            //$this->print_r2($urlSequenceList);
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

            foreach ($marketplaceArray[0] as $investment) {
                $investment['company_id'] = $companyId;
                $date = new DateTime();
                $investment['marketplace_investmentCreationDate'] = $date->format('Y-m-d H:i:s');
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

}
