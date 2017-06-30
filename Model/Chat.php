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
* @author Antoine de Poorter
* @version 0.1
* @date 2016-09-30
* @package
*

2016-09-30	  version 2016_0.1





Pending:



*/


class Chat extends AppModel
{
	var $name= 'Chat';


/*
public function beforeSave($options = array()) {
	echo __FILE__ . " " . __LINE__ . "<br>";
	print_r($this->data['Linkedaccount']);
	print_r($options);
echo "<br>";
//CakeLog::write('testing.txt', 'data = ' . json_encode($options));	


}

function afterSave ($created, $options = array()) {
	echo __FILE__ . " " . __LINE__ . "<br>";
	print_r($options);
	print_r($this->data['Linkedaccount']);	
	pr($created);
	echo "<br>";

}	





/**
*
*	Read a (or more) Chat thread(s) that fullfils the filteringConditions
*	
*	@param 		array 	$filteringConditions
*	@param 		array	$fields			names of the fields to include in result
*	@param 		int		$maxThreads		maximum number of threads to collect
*	@return 	array	$result			records that fullfill filteringConditions
*				
*active chat is defined as a chat with comment(s) added during the last three months
*
*
*/
public function getChatThreadData($filteringConditions, $fields, $maxThreads = 1) {
// read the chat threads that fullfil filteringConditions
	$filteringConditions = array_merge($filteringConditions, array("chat_status" => ACTIVE));
	$indexList = $this->find('list', $params = array('recursive'	=> -1,
													  'order'		=> array('Chat.chat_comments ASC'),
													  'conditions'  => $filteringConditions,
													  'limit'		=> $maxThreads
													));
	
	if (empty($indexList)) {
		return false;
	}

// for each found thread, read the required data
	$result = array();
	foreach ($indexList as $index) {
		$result[] = $this->Chat("all",$params = array('recursive'	=> -1,
													  'order' 		=> array('Chat.chat_sequenceNumber ASC'),
													  'fields'  	=> $fields,
													));	
	}
	return $result;
}





/**
*
*	get list of all chats that fullfils the filteringConditions
*	
*	@param 		array 	$filteringConditions
*	@return 	array	$result			list of chats that fullfill filteringConditions
*
*/
public function getChatList($filteringConditions) {
	
	$indexList = $this->find('list', $params = array('recursive'	=> -1,
													  'conditions'  => $filteringConditions,
													));
	
	if (empty($indexList)) {
		return false;
	}

	$numberInList = count($indexList);
	
	if ($numberInList == 1) {
		$this->delete($indexList);
		return true;
	}
	else {
		if ($multiple) {
			$this->deleteAll($filteringConditions);
			return true;
		}
		else {
		list($key, $val) = each($indexList);
		$this->delete($key);
		}
	}
	return true;
}





}