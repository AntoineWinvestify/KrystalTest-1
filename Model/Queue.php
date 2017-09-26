<?php
/**
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by       |
 * | the Free Software Foundation; either version 2 of the License, or          |
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-01-11
 * @package
 *


2017-01-11		version 0.1






Pending:







*/

App::uses('CakeEvent', 'Event');
class Queue extends AppModel {
	var $name= 'Queue';
/*
	var $hasOne = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'marketplace_id',
		)
	);
*/



/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
//var $validate = array(
// );





/*
*
*	Put a new request into the queue
*	@param	queueReference	array 			the reference, as given by the user of the queue, to an item.
*	@param	queueType		int				LIFO, FIFO, CIRCULAR
*	@param	action			varchar			url string of the action to be perfomed
*										
*	@return boolean			true			queueItem created
*							false			undefined error, item NOT created
*						
*/
public function addToQueue($queueReference, $queueType, $queueAction) {
	$data = array("queue_userReference" => $queueReference,
				  "queue_action"		=> $queueAction,
				  "queue_type"			=> FIFO,
				  "queue_status"		=> WAITING_FOR_EXECUTION,
				 );
	
	if ($this->save($data, $validate = true)) {
		return true;
	}
	else {
		return false;
	}
}





/*
*
*	Removes all requests with value queueReference and which are not (yet) executing from the queue.
*	@param	queueReference	varchar		the reference, as given by the user of the queue, to an item
*	@return boolean			true		reference deleted
*							false		reference not found
*						
*/
public function removeFromQueue($queueReference) {
	
	
	
}





/*
*
*	Check if an item exists in the queue, with status 'IDLE', 'WAITING_FOR_EXECUTION' or 'EXECUTING'	
*	@param	queueReference	varchar		the reference, as given by the user of the queue, to an item
*	@return boolean			true		one or more items found with requested reference
*							false		reference not found
*
*/
public function checkQueue($queueReference) {
	$result = $this->find("first", array("recursive" => -1,
										"conditions" => array("queue_userReference" => $queueReference,
															  "queue_status" 		=> WAITING_FOR_EXECUTION),
						 ));
	if (empty($result)) {
		return false;
	}
	else {
		return true;
	}
}





/*
*
*	Get the next request from the queue for executing purposes
*	@return queueReference	array		Array holding the relevant information of the item in the queue
*							empty 		queue is empty
*							
*/ 
public function getNextFromQueue($queuetype) {
	// check queue type
	switch ($queuetype) {
		case FIFO:
			$order = "Queue.id ASC";
			break;	
		case LIFO:
			$order = "Queue.id DESC";
			break;
	}
	
	$result = $this->find("first", array("conditions"	=> array("queue_type" 	=> $queuetype,
																 "queue_status" => WAITING_FOR_EXECUTION),
										 "order" 		=> $order,
										  "limit" 		=> 1)
						);

	if (empty($result)) {
		return;
	}
	
	$this->id = $result['Queue']['id'];	
	$this->save(array("queue_status" => EXECUTING));
	return $result;
}





/**
*
*	Callback Function
*	Generates the "created" field
*
*/
public function beforeSave1($options = array()) {

    $this->data[$this->alias]['created'] = date("Y-m-d H:i:s", time());
    return true;
}

    public function getUsersByStatus($queuetype, $status, $userAccess = null, $limit = null) {
        
        switch ($queuetype) {
            case FIFO:
                $order = "Queue.id ASC";
                break;
            case LIFO:
                $order = "Queue.id DESC";
                break;
        }

        if (empty($status)) {
            $status =  START_COLLECTING;
        }
        if (empty($limit)) {
            $limit = 100;
        }
        if (empty($userAccess)) {
            $usertype = DAILY_USER;
        }

echo "queue_status = $status queue_type = $queuetype, userAccess = $userAccess, limit = $limit \n";
        $result = $this->find("all", array("conditions" => array(
                                                        "queue_type" => $queuetype,
                                                        "queue_status" => $status,
                                                  //      "queue_accessType" => $userAccess
                ),
                                            "order" => $order,
                                            "limit" => $limit)
                            );
        return $result;
    }
}