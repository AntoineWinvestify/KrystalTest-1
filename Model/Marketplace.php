<?php
/**
// @(#) $Id$
// +-----------------------------------------------------------------------+
// | Copyright (C) 2009, http://yoursite                                   |
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
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-01-10
* @package
*


2016-01-10		version 0.1
function getMarketplaceDataList														[OK, tested]
function getGlobalMarketData														[OK, tested]


Pending:
getMarketplaceDataList --> search per country






*/

App::uses('CakeEvent', 'Event');
class Marketplace extends AppModel
{
	var $name= 'Marketplace';

	var $belongsTo = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
		)
	);




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);





/**
*
*	Returns an array with global data of our marketplace of the companies that fullfil
*	the filteringConditions
*	- TotalNoOfCompanies
*	- TotalNoOfInvestmentOptionsInCompanies
*	- TotalAmountPreInvestedInCompanies
*	- TotalInvestmentAmountAvailableInCompanies
*	- AvgInterestRateOfAllInvestmentOptionsInCompanies
*	- LowestInvestmentRate
*	- HighestInvestmentRate
*	
*	
*	@param 		array 	$filteringConditions
* 	@return 	array 	Data of each marketplace item as an element of an array
*			
*/
public function getGlobalMarketData($filterConditions = NULL) {
	$globalData = array();
	$tempGlobalData = array();
	
	$marketPlaceResults = $this->getMarketplaceDataList($filterConditions);
	if (empty($marketPlaceResults)) {
		return;
	}
//echo __FILE__ . " " . __LINE__ ."<br>";
//pr($marketPlaceResults);
/*	
	$globalData['TotalNoOfInvestmentOptionsInCompanies'] = count($marketPlaceResults);
	$globalData['lowestInvestmentRate'] = 10000;
	$globalData['highestInvestmentRate'] = 0;
	
	foreach ($marketPlaceResults as $result) {
		$tempGlobalData['tempCompanies'][$result['Marketplace']['company_id']] =
					$tempGlobalData['tempCompanies'][$result['Marketplace']['company_id']] + 1;
		
		$preinvested = $result['Marketplace']['marketplace_amount'] * $result['Marketplace']['marketplace_subscriptionProgress'] / 10000;
		$globalData['TotalAmountPreInvestedInCompanies'] =  $preinvested + $globalData['TotalAmountPreInvestedInCompanies'] ;
		
		$tempGlobalData['AvgInterestRateOfAllInvestmentOptionsInCompanies'] = $result['Marketplace']['marketplace_interestRate'] +
						$tempGlobalData['AvgInterestRateOfAllInvestmentOptionsInCompanies'];
	
		$globalData['TotalInvestmentAmountAvailableInCompanies'] = $result['Marketplace']['marketplace_amount'] + 
							$globalData['TotalInvestmentAmountAvailableInCompanies'];
							
		if ($globalData['lowestInvestmentRate'] > $result['Marketplace']['marketplace_interestRate']) {
			$globalData['lowestInvestmentRate'] = $result['Marketplace']['marketplace_interestRate'];
		};

		if ($globalData['highestInvestmentRate'] < $result['Marketplace']['marketplace_interestRate']) {
			$globalData['highestInvestmentRate'] = $result['Marketplace']['marketplace_interestRate'];
		};
	}
	$globalData['TotalAmountPreInvestedInCompanies'] = (int) ($globalData['TotalAmountPreInvestedInCompanies']);
	$globalData['TotalNoOfCompanies'] = count($tempGlobalData['tempCompanies']);

	$globalData['AvgInterestRateOfAllInvestmentOptionsInCompanies'] =
			(int) ($tempGlobalData['AvgInterestRateOfAllInvestmentOptionsInCompanies'] / $globalData['TotalNoOfInvestmentOptionsInCompanies']);
//	return($globalData);

//	pr($globalData);
*/	
	$globalData = array();	
	foreach ($marketPlaceResults as $result) {
		$globalData[$result['Marketplace']['company_id']]['TotalInvestmentAmountAvailableInCompany']=
					$globalData[$result['Marketplace']['company_id']]['TotalInvestmentAmountAvailableInCompany'] +
					$result['Marketplace']['marketplace_amount'];
					
		$globalData[$result['Marketplace']['company_id']]['TotalInvestmentOptionsAvailableInCompany'] = 
					$globalData[$result['Marketplace']['company_id']]['TotalInvestmentOptionsAvailableInCompany'] + 1;
		
		
		$globalData[$result['Marketplace']['company_id']]['InterestRateInCompany1'] =
					$globalData[$result['Marketplace']['company_id']]['InterestRateInCompany1'] + $result['Marketplace']['marketplace_interestRate'];

		$preinvested = $result['Marketplace']['marketplace_amount'] * $result['Marketplace']['marketplace_subscriptionProgress'] / 10000;
		$globalData[$result['Marketplace']['company_id']]['TotalAmountPreInvestedInCompany'] =  $preinvested +
								$globalData[$result['Marketplace']['company_id']]['TotalAmountPreInvestedInCompany'] ;
		$globalData[$result['Marketplace']['company_id']]['CompanyName'] = $result['Company']['company_name'];
		
	}
//	pr($globalData);
	
	
	foreach ($globalData as $key => $result) {
//		pr($result);
		$globalData[$key]['AvgInterestRateInCompany'] = (int) (
				$result['InterestRateInCompany1'] / $result['TotalInvestmentOptionsAvailableInCompany']);
		unset($globalData[$key]['InterestRateInCompany1']);
	}
	
//	pr($globalData);	
	return ($globalData);
}





/**
*
*	Returns an array of the marketplace items and their data that fullfil the filterConditions
*
*	@param 		array 	$filteringConditions
* 	@return 	array 	Data of each marketplace item as an element of an array
* 	
*			
*/
public function getMarketplaceDataList($filterConditions = NULL) {
	
	$businessConditions = array('Marketplace.marketplace_subscriptionProgress <' => 10000);
	if (!$filterConditions == NULL)	{
		$conditions = array_merge($businessConditions, $filterConditions);
	}
	else {
		$conditions = $businessConditions;
	}

	$this->Behaviors->load('Containable');
	$this->contain('Company');  // Own model is automatically included
	$marketPlaceResults = $this->find("all", $params = array('recursive'	=> 0,
															  'conditions'  => $conditions,
														  ));
	return $marketPlaceResults;
}





/**
*	Stores data of betatesters
*	
*	@param 		array 	$data	Data to be saved
*	@return 	array[0]	true	data has been saved
*							false	data could not be saved, unknown error
*				array[1]	information about errorfield(s)
*			
*/              
public function storeBetaTesterData($data) {

	$this->Betatester = ClassRegistry::init('Betatester');
	if ($this->Betatester->save($data, $validate = true) ) {
		$result[0] = true;	
	}

	else {
		$result[0] = false;
		$result[1] = $this->validationErrors;
	}
	return $result;
}




}