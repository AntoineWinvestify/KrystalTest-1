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




Contains certain configuration parameters. Note that only 1 database record exist for
this table

2016-09-12		version 2016_01
added functions writeConfigParameter readonfigParameter						[OK]



*/


class Configuration extends AppModel
{
	var $name= 'Configuration';





/*
*	
*	Read a value from the configuration database table
*	@param 		string	$key
*	@return 	
*						
*						
*/
public function readConfigParameter($key) {
 
	$this->Configuration = ClassRegistry::init('Configuration');

//	$this->Configuration->create();
	$newData = array('id' 	=> 1,
					 $key	=> $newValue,
					 );

	$conditions = array('Configuration.id' => 1);
	$result = $this->Configuration->find("first", $params = array('recursive'		=> -1, 		
			 														'conditions'	=> $conditions,
																	'fields' 		=> array($key)
																	)
											);					 
					 
	return $result['Configuration'][$key];
}





/*
*	
*	write a new value to the configuration database table
*	@param 		string	$key	
*	@param 		string	$newValue
*	@return 	boolean	true 	item saved succesfully
*						false
*/
public function writeConfigParameter($key, $newValue) {

	$this->Configuration = ClassRegistry::init('Configuration');

	$this->Configuration->create();
	$newData = array('id' 	=> 1,
					 $key	=> $newValue,
					 );

	if ($this->Configuration->save($newData, $validate = true)) {
		return true;
	}	
	return false;
}







}
