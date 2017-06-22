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




Pending:



*/


class Investorglobaldata extends AppModel
{
	var $name= 'Investorglobaldata';

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
public function loadInvestorData($investoridentity) {

	$businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
								'Company.company_state' => ACTIVE);

	$conditions = array_merge($businessConditions, $filterConditions);
// ony use link between investorglobal and investmentglobal
//        add a timelimit and number limit
	$investorglobalResult = $this->find("list", $params = array('recursive'	=> 2,
								'conditions'	=> $conditions,
					));
	
	return($investorglobalResult);
}





}