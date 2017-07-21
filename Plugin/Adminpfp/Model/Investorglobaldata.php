<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, http://www.winvestify.com                         |
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

2016-07-05	  version 0.1






2017-07-15      version 0.1
Initial version.
 * added methods cronMoveToMLDatabase(), writeArray, resetInvestmentArray() and resetInvestorsArray()



Pending
Method "cronMoveToMLDatabase": fields 'userplatformglobaldata_reservedInvestments' and
  'userplatformglobaldata_finishedInvestments' are not yet available in the raw data





*/

App::uses('AppModel', 'Model');
class Investorglobaldata extends AppModel
{
        var $useDbConfig = 'mldata';    
	public $name = 'Investorglobaldatas';

	var $hasMany = array(
		'Userplatformglobaldata' => array(
			'className' => 'Userplatformglobaldata',
			'foreignKey' => 'investorglobaldata_id',
		)
	);




/**
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();










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

$resultTallyman[0]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_activePFPs'] = 4;
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 5;
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 11737;
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 113233;
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 202053;
$resultTallyman[0]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[0]['Investorglobaldata']['created'] = "2017-04-15 01:55:21";     
$resultTallyman[0]['Investorglobaldata']['createdDate'] = "2017-04-15";

$resultTallyman[0]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 44412;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1052;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 4442;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 22352;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 'Comunitae';
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 3;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[0]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 13;

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
$resultTallyman[0]['Userplatformglobaldata'][2]['userplatformglobaldata_numberOfActiveInvestments'] = 9;

$resultTallyman[0]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 733;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 15992;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 18;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = "Dummy";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[0]['Userplatformglobaldata'][3]['userplatformglobaldata_numberOfActiveInvestments'] = 43;
 
$resultTallyman[0]['Userplatformglobaldata'][4]['id'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_moneyInWallet'] = 733;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_finishedInvestments'] = 15992;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[0]['Userplatformglobaldata'][4]['userplatformglobaldata_numberOfActiveInvestments'] = 43;

$resultTallyman[0]['Userplatformglobaldata'][5]['id'] = 2;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_moneyInWallet'] = 733;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_currency'] = 1;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_finishedInvestments'] = 15992;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_companyId'] = 19;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_companyName'] = "Circulantis";
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_PFPType'] = 0;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[0]['Userplatformglobaldata'][5]['userplatformglobaldata_numberOfActiveInvestments'] = 43;


$resultTallyman[1]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[1]['Investorglobaldata']['investorglobaldata_activePFPs'] = 3;
$resultTallyman[1]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 3;
$resultTallyman[1]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 2472;
$resultTallyman[1]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 113633;
$resultTallyman[1]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[1]['Investorglobaldata']['created'] = "2017-04-08 01:51:21";     
$resultTallyman[1]['Investorglobaldata']['createdDate'] = "2017-04-08";

$resultTallyman[1]['Userplatformglobaldata'][1]['id'] = 11;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 45812;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1305;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 4442;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 21352;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 2;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = 'Comunitae';
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 3;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[1]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 16;

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
$resultTallyman[1]['Userplatformglobaldata'][2]['userplatformglobaldata_numberOfActiveInvestments'] = 8;

$resultTallyman[1]['Userplatformglobaldata'][3]['id'] = 19;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_activeInInvestments'] = 44410;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_moneyInWallet'] = 1152;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_currency'] = 1;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_finishedInvestments'] = 14992;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_companyName'] = "LendixIT";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPType'] = 3;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[1]['Userplatformglobaldata'][3]['userplatformglobaldata_numberOfActiveInvestments'] = 43;


$resultTallyman[2]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[2]['Investorglobaldata']['investorglobaldata_activePFPs'] = 2;
$resultTallyman[2]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[2]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 3514;
$resultTallyman[2]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 65821;
$resultTallyman[2]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[2]['Investorglobaldata']['created'] = "2017-01-04 01:51:21"; 
$resultTallyman[2]['Investorglobaldata']['createdDate'] = "2017-04-04";

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
$resultTallyman[2]['Userplatformglobaldata'][0]['userplatformglobaldata_numberOfActiveInvestments'] = 8;

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
$resultTallyman[2]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 45;


$resultTallyman[3]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[3]['Investorglobaldata']['investorglobaldata_activePFPs'] = 2;
$resultTallyman[3]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[3]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 5514;
$resultTallyman[3]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 52821;
$resultTallyman[3]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[3]['Investorglobaldata']['created'] = "2017-03-24 01:51:21"; 
$resultTallyman[3]['Investorglobaldata']['createdDate'] = "2017-03-24";

$resultTallyman[3]['Userplatformglobaldata'][0]['id'] = 12;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_activeInInvestments'] = 18411;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_moneyInWallet'] = 1952;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_currency'] = 1;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_finishedInvestments'] = 12952;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[3]['Userplatformglobaldata'][0]['globalIndicator'] = 112;
$resultTallyman[3]['Userplatformglobaldata'][0]['userplatformglobaldata_numberOfActiveInvestments'] = 8;

$resultTallyman[3]['Userplatformglobaldata'][1]['id'] = 19;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 34410;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1562;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 14392;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 4;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[3]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 42;


$resultTallyman[4]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[4]['Investorglobaldata']['investorglobaldata_activePFPs'] = 2;
$resultTallyman[4]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[4]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 3514;
$resultTallyman[4]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 69821;
$resultTallyman[4]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[4]['Investorglobaldata']['created'] = "2017-03-17 01:51:21"; 
$resultTallyman[4]['Investorglobaldata']['createdDate'] = "2017-03-17";

$resultTallyman[4]['Userplatformglobaldata'][0]['id'] = 12;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_activeInInvestments'] = 22411;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_moneyInWallet'] = 1952;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_currency'] = 1;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_finishedInvestments'] = 12952;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[4]['Userplatformglobaldata'][0]['globalIndicator'] = 112;
$resultTallyman[4]['Userplatformglobaldata'][0]['userplatformglobaldata_numberOfActiveInvestments'] = 8;

$resultTallyman[4]['Userplatformglobaldata'][1]['id'] = 19;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 47410;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1562;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 14392;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 1;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[4]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 45;


$resultTallyman[5]['Investorglobaldata']['investorglobaldata_investorIdentity'] = '39048098ab409be490A';
$resultTallyman[5]['Investorglobaldata']['investorglobaldata_activePFPs'] = 2;
$resultTallyman[5]['Investorglobaldata']['investorglobaldata_totalPFPs'] = 2;
$resultTallyman[5]['Investorglobaldata']['investorglobaldata_totalMoneyInWallets'] = 3514;
$resultTallyman[5]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'] = 65821;
$resultTallyman[5]['Investorglobaldata']['investorglobaldata_currency'] = 1;     // = Euro
$resultTallyman[5]['Investorglobaldata']['created'] = "2017-03-10 01:51:21"; 
$resultTallyman[5]['Investorglobaldata']['createdDate'] = "2017-03-10";

$resultTallyman[5]['Userplatformglobaldata'][0]['id'] = 12;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_activeInInvestments'] = 20411;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_moneyInWallet'] = 1952;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_currency'] = 1;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_reservedInvestments'] = 11;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_finishedInvestments'] = 12952;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_companyId'] = 1;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_companyName'] = "Zank";
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPType'] = 2;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_PFPCountry'] = "ES";
$resultTallyman[5]['Userplatformglobaldata'][0]['globalIndicator'] = 112;
$resultTallyman[5]['Userplatformglobaldata'][0]['userplatformglobaldata_numberOfActiveInvestments'] = 6;

$resultTallyman[5]['Userplatformglobaldata'][1]['id'] = 19;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_activeInInvestments'] = 45410;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_moneyInWallet'] = 1562;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_currency'] = 1;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_reservedInvestments'] = 0;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_finishedInvestments'] = 14392;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_companyId'] = 21;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_companyName'] = "Lendix";
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPType'] = 1;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_PFPCountry'] = "IT";
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_globalIndicator'] = 112;
$resultTallyman[5]['Userplatformglobaldata'][1]['userplatformglobaldata_numberOfActiveInvestments'] = 41;

// Do some simple calculations to get extra "new" values so they can be displayed
// enrich the information to be provided to the PFPAdmin user
  
    $this->Company = ClassRegistry::init('Company');
    $companyFilterConditions = array('id' => $platformId);
    $resultCompany = $this->Company->getCompanyDataList($companyFilterConditions);

//  Data for geographical distribution of PFPs as used by investor
    $homeCountryPFP = $resultCompany[$platformId]['company_country'];

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
    $platformTypes = 5;           // P2P, P2B, IT, R.E. SOCIAL
    $platformInvestmentsPerType = array_fill(0,  $platformTypes, 0);
    $platformInvestmentsPerAmount = array_fill(0,  $platformTypes, 0); 
   

    foreach ($resultTallyman as $key => $value) {
        unset($platformInvestmentsPerType);
        unset($platformInvestmentsPerAmount);
    $platformInvestmentsPerType = array_fill(0,  $platformTypes, 0);
    $platformInvestmentsPerAmount = array_fill(0,  $platformTypes, 0);        
        foreach($value['Userplatformglobaldata'] as $keyNew =>$platform) {
            $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']]  = 
            $platformInvestmentsPerType[$platform['userplatformglobaldata_PFPType']] + 
                    $platform['userplatformglobaldata_numberOfActiveInvestments'];
            
            $resultTallyman[$key]['investorglobaldata_numberOfActiveInvestments'] = 
                    $resultTallyman[$key]['investorglobaldata_numberOfActiveInvestments'] + 
                    $platform['userplatformglobaldata_numberOfActiveInvestments'];
            
            $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']] = 
                  $platformInvestmentsPerAmount[$platform['userplatformglobaldata_PFPType']] + 
                    $platform['userplatformglobaldata_activeInInvestments'];
        }
        $resultTallyman[$key]['investorglobaldata_PfpPerType_Abs'] = $platformInvestmentsPerType;
        $resultTallyman[$key]['investorglobaldata_PfpPerAmount_Abs'] = $platformInvestmentsPerAmount;     
    }

    
    foreach ($resultTallyman as $key => $value) {
        foreach ($value['investorglobaldata_PfpPerAmount_Abs'] as $newKey => $platformAmount) {
            $resultTallyman[$key]['investorglobaldata_PfpPerAmount_Norm'][$newKey] = 
                    round((100 * $resultTallyman[$key]['investorglobaldata_PfpPerAmount_Abs'][$newKey]) /  
                        $resultTallyman[$key]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'], 2);
        }
    }
 

    
// Normalize the data for the number of investments per platformtype    
    foreach ($resultTallyman as $key => $value) {
        foreach ($value['investorglobaldata_PfpPerType_Abs'] as $newKey => $platformAmount) {
            $resultTallyman[$key]['investorglobaldata_PfpPerType_Norm'][$newKey] = 
                    round((100 * $resultTallyman[$key]['investorglobaldata_PfpPerType_Abs'][$newKey]) /  
                        $resultTallyman[$key]['investorglobaldata_numberOfActiveInvestments'], 2);
        }
    }

    
    foreach ($resultTallyman as $key => $value) {
            foreach($value['Userplatformglobaldata'] as $platform) {
                if ($platform['userplatformglobaldata_companyId'] == $platformId) {
                    $resultTallyman[$key]['totalMyPlatform_Abs'] = $platform['userplatformglobaldata_activeInInvestments'];
                    break;
                }
            }
    }


    $PFPType = $resultCompany[$platformId]['company_PFPType'];
 
    foreach ($resultTallyman as $key => $platform) {
     //   if ($resultTallyman[$key]['investorglobaldata_PfpPerAmount_Abs'][$PFPType] <> 0) {
        $resultTallyman[$key]['totalModality_Norm'] = round (100 * $resultTallyman[$key]['totalMyPlatform_Abs'] / 
                                            $resultTallyman[$key]['investorglobaldata_PfpPerAmount_Abs'][$PFPType], 1);
    //    }
        $resultTallyman[$key]['totalPortfolio_Norm'] = round(100 * $resultTallyman[$key]['totalMyPlatform_Abs'] / 
                                            $resultTallyman[$key]['Investorglobaldata']['investorglobaldata_totalActiveInvestments'], 1);
     
    }

    
    // Create tendency arrows
    if ($resultTallyman[0]['totalPortfolio_Norm'] < $resultTallyman[1]['totalPortfolio_Norm']){
        $resultTallyman[0]['totalPortfolioTendency'] = DOWNWARDS;
    }
    if ($resultTallyman[0]['totalPortfolio_Norm'] > $resultTallyman[1]['totalPortfolio_Norm']){
        $resultTallyman[0]['totalPortfolioTendency'] = UPWARDS;
    }  
    if ($resultTallyman[0]['totalModality_Norm'] < $resultTallyman[1]['totalModality_Norm']){
        $resultTallyman[0]['totalModalityTendency'] = DOWNWARDS; 
    }
    if ($resultTallyman[0]['totalModality_Norm'] > $resultTallyman[0]['totalModality_Norm']){
        $resultTallyman[0]['totalModalityTendency'] = UPWARDS;
    } 

// Store "historical" data for "$totalPortfolio"
   foreach ($resultTallyman as $value) {            
       $totalPortfolioHistorical[] = round($value['totalPortfolio_Norm'], 1);        // in % 
       $totalPortfolioHistoricalDate[] = $value['Investorglobaldata']['createdDate'];
   }     
        
    $resultTallyman[0]['totalPortfolioHistorical'] = array_reverse($totalPortfolioHistorical);
    $resultTallyman[0]['totalPortfolioHistoricalDate'] = array_reverse($totalPortfolioHistoricalDate);  
    

    
// Show investments per geographical area     
    $homeCountryPFP = $resultCompany[$platformId]['company_country'];

    foreach ($resultTallyman[0]['Userplatformglobaldata'] as $platform) {
        if ($platform['userplatformglobaldata_PFPCountry'] == $homeCountryPFP) {
            $platformsHomeCountryInvestmentsAbs += $platform['userplatformglobaldata_activeInInvestments'];
        }
        else {
            $platformsForeignCountriesinvestmentsAbs += $platform['userplatformglobaldata_activeInInvestments'];
        }
    }

    $resultTallyman[0]['platformsHomeCountryAbs'] = $platformsHomeCountryInvestmentsAbs;
    $resultTallyman[0]['platformsForeignCountriesAbs'] = $platformsForeignCountriesinvestmentsAbs;    

// normalize the data in %
    $totalInvestmentAbs = $platformsHomeCountryInvestmentsAbs + $platformsForeignCountriesinvestmentsAbs; 
    $resultTallyman[0]['platformsHomeCountryNorm'] = round ($platformsHomeCountryInvestmentsAbs * 100 / $totalInvestmentAbs, 1);
    $resultTallyman[0]['platformsForeignCountriesNorm'] = round($platformsForeignCountriesinvestmentsAbs * 100 / $totalInvestmentAbs, 1);        
          
          
    $labelsPieChart1 = array("Local Investments [%]", "Foreign Investments [%]"); 

    $dataPieChart1 = array($resultTallyman[0]['platformsHomeCountryNorm'], $resultTallyman[0]['platformsForeignCountriesNorm']);
    $resultTallyman[0]['labelsPieChart1'] = $labelsPieChart1;
    $resultTallyman[0]['dataPieChart1'] = $dataPieChart1;
    
    return $resultTallyman;   
    }
}