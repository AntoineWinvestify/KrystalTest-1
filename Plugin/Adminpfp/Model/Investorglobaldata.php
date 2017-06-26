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
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();






/**
 *category of user is checked in order to know how many records need to be fetched (in number and time)
 *	Get the investor data as seen from a PFP platform
 *	

    
 * 
 * 
 * 
 *  @return array  array of all company Ids that fullfil filtering conditions
 *			
 */
public function loadInvestorData($investoridentity) {
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





}