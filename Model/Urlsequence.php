<?php
/**
// +-----------------------------------------------------------------------+
*  | Copyright (C) 2017, http://www.winvestify.com                         |
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
* @author Antoine de Poorter
* @version 0.1
* @date 2016-09-26
* @package
*/
/*

2016-09-26	  version 2016_0.1
function getUrlsequence()													[OK, tested]





Pending:



*/


class Urlsequence extends AppModel
{
	var $name= 'Urlsequence';

/*

*/


/* The following is for the filesharing function */
	//The Associations below have been created with all possible keys, those that are not needed can be removed
/*

*/



/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
/*

*/

 



/**
*
*	Returns a array with URL's

*	@param 		int		$companyId		identifier of company
* 	@rparam 	int		$sequence		identifier of the requested sequence
* 	@return array  array of all URL's that form part of the requested sequence
*		
*/
public function getUrlsequence($companyId, $sequence) {

	$filteringConditions = array('company_id'			=> $companyId,
								'urlsequence_sequence'	=> $sequence);

	$sequenceList = $this->find('all', $params = array('recursive'	=> -1,
													  'order'		=> 'Urlsequence.urlsequence_sequenceNumber ASC',
													  'conditions'  => $filteringConditions,
													));
	foreach ($sequenceList as $sequence) {
		$tempSequence[] = $sequence['Urlsequence']['urlsequence_URL'];
	}
	return $tempSequence;	
}





}