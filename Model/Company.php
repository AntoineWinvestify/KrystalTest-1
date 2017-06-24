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
//

2016-10-07	  version 2016_0.1
function getCompanyDataList(). Revisit again and use pollId as id, not companyId	[OK, but retest due to changes]
function getCompanyList()															[OK, tested]
function readExtendedData()															[not OK, not tested]


Pending:



*/


class Company extends AppModel
{
	var $name= 'Company';

/*
	public $hasOne = array(	);

*/
	var $hasMany = array(
		'Marketplace' => array(
			'className' => 'Marketplace',
			'foreignKey' => 'company_id',
		)
	);




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();







/**STILL TO BE DONE
*
*	Returns a *LIST* of companies that fullfil the filterConditions
*	
* 	@return array  array of all company Ids that fullfil filtering conditions
*			
*/
public function getCompanyList($filterConditions) {

	$businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
								'Company.company_state' => ACTIVE);

	$conditions = array_merge($businessConditions, $filterConditions);

	$companyResult = $this->find("list", $params = array('recursive'	=> -1,
								'conditions'	=> $conditions,
					));
	
	return($companyResult);
}





/**
*
*	Returns an array of the companies and their data that fullfil the filterConditions
*	
*	@param 		array 	$filteringConditions	
* 	@return 	array 	 Data of each company as an element of an array
*			
*/
public function getCompanyDataList($filterConditions) {

	$businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
								'Company.company_state' => ACTIVE);

	$conditions = array_merge($businessConditions, $filterConditions);

	$companyResult = $this->find("all", $params = array('recursive'		=> -1,
                                                                  'conditions'	=> $conditions,
                                    ));
// 'Normalize' the total array, index XX points to company with id = XX
	foreach($companyResult as $value){
		$companyResults[$value['Company']['id']] = $value['Company'];
	}
	return $companyResults;	
}





/**
*
*	Returns the extended data an array of the company and their data that fullfil the filterConditions.
*	If more then one record fullfils the criterion, then the first one is returned
*	
*	@param 		array 	$filteringConditions	(basically the companyId)
* 	@return 	array 	 Data of each company as an element of an array
*			
*/
public function readExtendedData($filterConditions) {

	$companyResult = $this->find("all", $params = array('recursive'		=> -1,
                                                            'conditions'	=> $filterConditions,
				));
	
// 'Normalize' the total array
	foreach($companyResult as $value){
		$companyResults[$value['Company']['id']] = $value['Company'];
	}
	return $companyResults;	
}

}