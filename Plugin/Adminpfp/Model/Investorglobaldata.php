<?php
/**
// @(#) $Id$
// +-----------------------------------------------------------------------+
// | Copyright (C) 2009, http://www.winvestify.com                         |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//

2016-06-23	  version 0.1





*/

App::uses('AppModel', 'Model');
class Investorglobaldata extends AppModel
{
        var $useDbConfig = 'mldata';    
	public $name = 'Investorglobaldata';
//        var $useTable = "investorglobaldata";
/*
	public $hasOne = array(	);


	var $hasMany = array(
		'Marketplace' => array(
			'className' => 'Marketplace',
			'foreignKey' => 'company_id',
		)
	);
*/



/**
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();





    /**
     * TO ADD if record has been taken, i.e. status = UNCONFIRMED_ACCOUNT. In the latter case
     * modify some data in the table, marking that user tried more then once
     *
     *  Create an account. 
     *  Will "re-use" an existing account if it is already in status UNCONFIRMED_ACCOUNT. Existing data will be 
     *  overwritten with the new data provided
     * 	
     * @param string $username 
     * @param string $userPassword   
     * @param string $telephone
     *
     * @return array  array    All data of the user
     */

public function loadInvestorDataOld($investoridentity) {
    Configure::load('p2pGestor.php', 'default');
    $serviceTallymanData = Configure::read('Tallyman');  
    $cutoffDateTime = date("Y-m-d H:i:s", time() - $refreshFrecuency * 3600);
        
    $businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
                                                'created >' => $cutoffDateTime);

    $conditions = array_merge($businessConditions, $filterConditions);
// only use link between investorglobal and investmentglobal

    $investorglobalResult = $this->find("list", $params = array('recursive'	=> 2,
								'conditions'	=> $conditions,
                                                                'limit'         => $serviceTallymanData['maxHistoryLengthNumber'],
					));
	
    return($investorglobalResult);
}




    /**
     *
     *  Returns all the data of an investor
     * 	
     * @param string $investorIdentity  Unique identification of the investor
     * @param string $platformId        Identification of the PFP   
     *
     * @return array  array    All data of the user
     * 
     */
public function readinvestorData($investorIdentity, $platformId) {
 
$resultTallyman[0]['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[0]['investorglobaldata_activePFPs'] = 3;
$resultTallyman[0]['investorglobaldata_totalPFPs'] = 3;
$resultTallyman[0]['investorglobaldata_totalMoneyInWallets'] = 11737;
$resultTallyman[0]['investorglobaldata_totalActiveInvestments'] = 113233;
$resultTallyman[0]['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[0]['created'] = "2017-04-15 01:55:21";     
$resultTallyman[0]['createdDate'] = "2017-04-15";

$resultTallyman[0]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1052;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 4442;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 22352;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 'Comunitae';
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[0]['Userplatformglobaldata'][2]['id'] = 12;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_activeInInvestments'] = 24411;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 9952;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_finishedInvestments'] = 18952;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[0]['Userplatformglobaldata'][2]['globalIndicator'] = 112;

$resultTallyman[0]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 733;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 15992;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;


$resultTallyman[1]['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[1]['investorglobaldata_activePFPs'] = 3;
$resultTallyman[1]['investorglobaldata_totalPFPs'] = 3;
$resultTallyman[1]['investorglobaldata_totalMoneyInWallets'] = 2472;
$resultTallyman[1]['investorglobaldata_totalActiveInvestments'] = 113633;
$resultTallyman[1]['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[1]['created'] = "2017-04-08 01:51:21";     
$resultTallyman[1]['createdDate'] = "2017-04-08";

$resultTallyman[1]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 45812;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1305;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 4442;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 21352;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 2;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 'Comunitae';
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;

$resultTallyman[1]['Userplatformglobaldata'][2]['id'] = 12;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_activeInInvestments'] = 23411;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_moneyInWallet'] = 15;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_finishedInvestments'] = 14952;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[1]['Userplatformglobaldata'][2]['globalIndicator'] = 112;

$resultTallyman[1]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 1152;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 14992;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;



$resultTallyman[2]['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[2]['investorglobaldata_activePFPs'] = 2;
$resultTallyman[2]['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[2]['investorglobaldata_totalMoneyInWallets'] = 3514;
$resultTallyman[2]['investorglobaldata_totalActiveInvestments'] = 65821;
$resultTallyman[2]['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[2]['created'] = "2017-04-01 01:51:21"; 
$resultTallyman[2]['createdDate'] = "2017-04-01";

$resultTallyman[2]['Userplatformglobaldata'][0]['id'] = 12;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_activeInInvestments'] = 21411;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_moneyInWallet'] = 1952;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_currency'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_finishedInvestments'] = 12952;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[2]['Userplatformglobaldata'][0]['globalIndicator'] = 112;

$resultTallyman[2]['Userplatformglobaldata'][1]['id'] = 19;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1562;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 14392;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;



// Do some simple calculations to get extra "new" values so they can be displayed
// enrich the information to be provided to the PFPAdmin user
// index 0 is the most recent read-out of the user investment data
  
    $this->Company = ClassRegistry::init('Company');
    $companyFilterConditions = array('id' => $platformId);
    $resultCompany = $this->Company->getCompanyDataList($companyFilterConditions);

//  Data for geographical distribution of PFPs as used by investor
    $homeCountryPFP = $resultCompany[$platformId][company_country];

    foreach ($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        if ($platform['userplatformglobaldata_PFPCountry'] == $homeCountryPFP) {
            $platformsHomeCountry = $platformsHomeCountry + 1;
        }
        else {
            $platformsForeignCountries = $platformsForeignCountries + 1;
        }
    }

    $resultTallyman[0]['platformsHomeCountry'] = $platformsHomeCountry;
    $resultTallyman[0]['platformsForeignCountries'] = $platformsForeignCountries;    
    $labelsPieChart1 = array("Local", "Foreign");   
    $dataPieChart1 = array($resultTallyman[0]['platformsHomeCountry'], $resultTallyman[0]['platformsForeignCountries']);
    $resultTallyman[0]['labelsPieChart1'] = $labelsPieChart1;
    $resultTallyman[0]['dataPieChart1'] = $dataPieChart1;


// How many types of platforms do we have?
    $platformTypes = count($this->crowdlendingTypesShort);
    $platformInvestmentsPerType = array_fill(0,  $platformTypes, 0);
    $platformInvestmentsPerAmount = array_fill(0,  $platformTypes, 0); 

    foreach ($resultTallyman as $key => $value) {
        unset($platformInvestmentsPerType);
        unset($platformInvestmentsPerAmount);
        foreach($value['Userplatformglobaldata'] as $platform) {
            $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']] = 
                    $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']] + 1;
            $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']]  = 
                    $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']] + 
                    $platform['userplatformglobaldata_activeInInvestments'];
        }
        $resultTallyman[$key]['userplatformglobaldata_PFPPerType'] = $platformInvestmentsPerType;
        $resultTallyman[$key]['userplatformglobaldata_PFPPerAmount'] = $platformInvestmentsPerAmount;     
    }



// Also provide "normalized" data, i.e. in %.
// Total represents 100
    $totalPerType = array_sum($platformInvestmentsPerType);
    $totalPerAmount = array_sum($platformInvestmentsPerAmount);


/*
    foreach ($resultTallyman as $key => $value) {
        $i = 0;        
        foreach ($value['userplatformglobaldata_PFPPerType'] as $type) {
     //       CHECK THIS
     //       $resultTallyman[$key]['userplatformglobaldata_PFPPerTypeNorm'][$i] = (int) (100 * $value / $totalPerType); 
            $i = $i + 1;
        }
    }
*/
    $j = 0;
    foreach ($platformInvestmentsPerAmount as $key => $value) {
        $resultTallyman[$key]['userplatformglobaldata_PFPPerAmountNorm'][$j] = (int) (100 *$value / $totalPerAmount); 
        $j = $j + 1;       
    }


    foreach ($resultTallyman as $key => $value) {
        foreach($value['Userplatformglobaldata'] as $platform) {
            if ($platform['userplatformglobaldata_companyId'] == $platformId) {
                $resultTallyman[$key]['totalMyPlatform'] = $platform['userplatformglobaldata_activeInInvestments'];
                break;
            }
        }
    }
    
    foreach ($resultTallyman as $key => $value) {
        foreach($value['Userplatformglobaldata'] as $platform) {
            if ($platform['userplatformglobaldata_PFPType'] ==  $resultCompany[$platformId]['company_typeOfCrowdlending']) {
                $resultTallyman[$key]['totalMyModality'] = $resultTallyman[$key]['totalMyModality'] + 
                                                $platform['userplatformglobaldata_activeInInvestments'];
                break;
            }
        }    
    }
    

    
    
 // Calculate some values for this view
        $resultTallyman[$key]['AtotalPortfolio'] = $value['totalMyPlatform'] / 
                $value['investorglobaldata_totalActiveInvestments'];
        $totalMyModality = $value['totalMyPlatform'] /
                $value['userplatformglobaldata_PFPPerAmount'][$resultCompany[$platformId]['company_typeOfCrowdlending']];
       echo "<br>DED"  . $value['userplatformglobaldata_PFPPerAmount'][$resultCompany[$platformId]['company_typeOfCrowdlending']];
 
        $resultTallyman[$key]['AtotalMyModality'] = $totalMyModality;
   
   
    // create tendency arrows
    if ($resultTallyman[0]['AtotalPortfolio'] < $resultTallyman[1]['AtotalPortfolio']){
        $resultTallyman[0]['AtotalPortfolioTendency'] = DOWNWARDS;
    }
    if ($resultTallyman[0]['AtotalPortfolio'] > $resultTallyman[1]['AtotalPortfolio']){
        $resultTallyman[0]['AtotalPortfolioTendency'] = UPWARDS;
    }       
    if ($resultTallyman[0]['AtotalMyModality'] < $resultTallyman[1]['AtotalMyModality']){
        $resultTallyman[0]['AtotalMyModalityTendency'] = DOWNWARDS; 
    }
    if ($resultTallyman[0]['AtotalMyModality'] > $resultTallyman[1]['AtotalMyModality']){
        $resultTallyman[0]['AtotalMyModalityTendency'] = UPWARDS;
    } 
    
 
    

// Store "historical" data for "$totalPortfolio"
    foreach ($resultTallyman as $key => $value) {
    // get value of investment in present platform
        $found = NO;

        foreach ($value['Userplatformglobaldata'] as $data) {
            if ($data['userplatformglobaldata_companyId'] == $platformId) {
                $resultTallyman[$key]['totalMyPlatform'] = $data['userplatformglobaldata_activeInInvestments'];
                $resultTallyman[$key]['totalPortfolio'] =  100 * $resultTallyman[$key]['totalMyPlatform'] / 
                                            $resultTallyman[$key]['investorglobaldata_totalActiveInvestments'];
                $found = YES;
                break;
            } 
        }
        if ($found == NO) {
            $resultTallyman[$key]['totalPortfolio'] = 0;
        }
    }
 
        
   foreach ($resultTallyman as $value) {
       $totalPortfolioHistorical[] = round($value['totalPortfolio'], 1);        // in %
       $totalPortfolioHistoricalDate[] = $value['createdDate'];
   }     
        
    $resultTallyman[0]['totalPortfolioHistorical'] = array_reverse($totalPortfolioHistorical);
    $resultTallyman[0]['totalPortfolioHistoricalDate'] = array_reverse($totalPortfolioHistoricalDate);  

    return $resultTallyman;
}




}