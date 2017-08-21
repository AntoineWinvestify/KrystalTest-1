<?php

require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
App::uses('CakeEvent', 'Event');
App::uses('CakeTime', 'Utility');

class MarketplaceShell extends AppShell {

    var $helpers = array('Html', 'Form', 'Js');
    public $uses = array('Marketplace', 'Marketplacebackup', 'Structure', 'Company', 'Urlsequence');
    var $components = array('Security', 'Session');

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
            $structure = $this->Structure->getStructure($companyId, 1);

            if ($this->args[0] == 1) {
                $this->cronMarketPlaceLoop($companyId, $structure);
            } else if ($this->args[0] == 2) {
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

        $companyConditions = array('Company.id' => $companyId);
        $result = $this->Company->getCompanyDataList($companyConditions);

        $companyMarketplace = $this->Marketplace->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1));
        $companyBackup = $this->Marketplacebackup->find('all', array('conditions' => array('company_id' => $companyId), 'recursive' => -1, 'limit' => 1000));

        $this->out(print_r($result[$companyId]['company_codeFile']) . " " . SHELL_ENDOFLINE);
        $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.	
        $newComp->defineConfigParms($result[$companyId]);

        $companyId = $result[$companyId]['id'];
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, MARKETPLACE_SEQUENCE);

        $newComp->setUrlSequence($urlSequenceList);  // provide all URLs for this sequence
        $marketplaceArray = $newComp->collectCompanyMarketplaceData($companyBackup, $structure);


        if ($marketplaceArray[1] && $marketplaceArray[1] != 1) {
            $this->out('Saving new structure' . SHELL_ENDOFLINE);
            $this->Structure->saveStructure(array('company_id' => $companyId, 'structure_html' => $marketplaceArray[1], 'structure_type' => 1));
            if (!$marketplaceArray[0]) {

                $this->out('Sending error report' . SHELL_ENDOFLINE);
            }
        }


        foreach ($marketplaceArray[0] as $investment) {
            $DontExist = true;
            $backup = true;
            $investment['company_id'] = $companyId;

            foreach ($companyMarketplace as $marketplaceInvestment) {

                if ($investment['marketplace_loanReference'] == $marketplaceInvestment['Marketplace']['marketplace_loanReference']) { //If exist in winvestify marketplace
                    $DontExist = false;
                    // "Investment already exist";
                    $investment['marketplace_investmentCreationDate'] = $marketplaceInvestment['marketplace_investmentCreationDate'];
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
                        $investment['marketplace_investmentCreationDate'] = $investmentBackup['marketplace_investmentCreationDate'];
                        if ($investment['marketplace_loanReference'] == $investmentBackup['Marketplace']['marketplace_loanReference']) { //If it exist in winvestify backup
                            if ($investment['marketplace_status'] == $investmentBackup['Marketplace']['marketplace_status']) { //Same status
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
                // 'Saving new structure';
                $this->Structure->saveStructure(array('company_id' => $companyId, 'structure_html' => $marketplaceArray[1], 'structure_type' => 1));
                if (!$marketplaceArray[0]) {
                    // 'Sending error report';
                    break;
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

}
