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
* @date 2016-10-28
* @package
*


2016-10-28		version 0.1
function readFollowers(											[OK, not tested]





Pending:







*/

App::uses('CakeEvent', 'Event');
class Follower extends AppModel
{
	var $name= 'Follower';




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);






/**
*not tested
*	Returns an array of follower items and their data that fullfil the filterConditions
*	If more then 1 record is identified by the filterConditions an error is returned.
*
*	@param 		array 	$filteringConditions. Must be sufficient to identify 1 unique record
* 	@return 	array 	Data of each follower item as an element of an array
*			
*/
public function followAction($filterConditions, $action) {
	
	
	
	
	
	
}	 





/**
*
*	Returns an array of follower items and their data that fullfil the filterConditions
*	IMPORTANT NOTICE: SOME OF THE FIELDS OF THIS TABLE ARE AUTOMICALLY UPDATED FROM THE 
*	INVESTOR TABLE USING A MYSQL TRIGGER. (not yet implemented, but will be soon)
*	@param 		array 	$filteringConditions. Typically this is the investor_id
* 	@return 	array 	Data of each follower item as an element of an array
*			
*/
public function readFollowers($filterConditions) {

	$businessConditions = array('follower_state' => ACTIVE);
	$conditions = (!empty($filterConditions)) ? array_merge($businessConditions, $filterConditions) : $businessConditions;

	$followerResults = $this->find("all", $params = array('recursive'	=> -1,
														  'conditions'  => $conditions,
														  ));
	return $followerResults;
}

}