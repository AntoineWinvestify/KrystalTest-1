<?php
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
 * @date 2018-05-16
 * @package
 */

/*
2017-01-11	version 0.1


2018-05-16      version 0.2
aftersave method with new event for sending information to user via SMS


Pending:



*/

App::uses('CakeEvent', 'Event');
class Queue2 extends AppModel {
	var $name= 'Queue2';
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





    /**
     *	Put a new request into the queue
     * 
     *	@param	queueReference	array 		the reference, as given by the user of the queue, to an item.
     *	@param	queueType	int		LIFO, FIFO, CIRCULAR
     *	@param	action		varchar		url string of the action to be perfomed 
     *										
     *	@return boolean		true		queueItem created
     *				false		undefined error, item NOT created
     * NOT USED CAN BE DELETED						
     */
    public function addToQueueREMOVE($queueReference, $queueType, $queueAction) {
	$data = array("queue2_userReference" => $queueReference,
				  "queue2_action"   => $queueAction,
				  "queue2_type"     => FIFO,
				  "queue2_status"   => WAITING_FOR_EXECUTION,
				 );
	
	if ($this->save($data, $validate = true)) {
		return true;
	}
	else {
		return false;
	}
}

    /**
     * Put a new request into the queue for Dashboard 2.0
     * 
     * @param array $queueReference The reference, as given by the user of the queue, to an item
     * @param json $queueInfo It is the information about the queue request
     * @param int $queueStatus It is the status to init the process of collecting information about the user's companies
     * @param int $queueId It is the queueId of the request
     * @param int $queueType LIFO, FIFO, CIRCULAR
     * @return boolean true queueItem created
     *                 false undefined error, item NOT created
     */
    public function addToQueueDashboard2($queueReference , $queueInfo= null, $queueStatus = WIN_QUEUE_STATUS_START_COLLECTING_DATA, $queueId = null, $queueType = FIFO) {
        
            $data = array(
                "id" => $queueId,
                "queue2_userReference" => $queueReference,
                "queue2_info" => $queueInfo,
                "queue2_type" => $queueType,
                "queue2_status" => $queueStatus,
            );

            if ($this->save($data, $validate = true)) {
                return true;
            } else {
                return false;
            }
        }

    /**
     *  Removes all requests with value queueReference and which are not (yet) executing from the queue.
     * 
     *  @param	queueReference	varchar	the reference, as given by the user of the queue, to an item
     *	@return boolean		true	reference deleted
     *				false	reference not found					
     */
    public function removeFromQueue($queueReference) {



    }





    /**
     *	Check if an item exists in the queue, with status 'IDLE', 'WAITING_FOR_EXECUTION' or 'EXECUTING'	
     * 
     *	@param	queueReference	varchar	the reference, as given by the user of the queue, to an item
     *	@return boolean			true		one or more items found with requested reference
     *					false		reference not found
     */
    public function checkQueue($queueReference) {
	$result = $this->find("first", array("recursive" => -1,
			"conditions" => array("queue2_userReference" => $queueReference,
			"queue2_status"  => WAITING_FOR_EXECUTION),
			));
	if (empty($result)) {
		return false;
	}
	return true;
    }





    /**
     *	Get the next request from the queue for executing purposes
     * 
     *	@return queueReference	array	Array holding the relevant information of the item in the queue
     *				empty 	queue is empty						
     */ 
    public function getNextFromQueue($queuetype) {
	// check queue type
	switch ($queuetype) {
		case FIFO:
			$order = "Queue2.id ASC";
			break;	
		case LIFO:
			$order = "Queue2.id DESC";
			break;
	}
	
	$result = $this->find("first", array("conditions"	=> array("queue2_type" 	=> $queuetype,
						"queue2_status" => WAITING_FOR_EXECUTION),
						"order" 	=> $order,
						"limit" 	=> 1)
						);

	if (empty($result)) {
		return;
	}
	
	$this->id = $result['Queue2']['id'];	
	$this->save(array("queue2_status" => EXECUTING));
	return $result;
    }


    /**
     *	Callback Function
     *	Generates the "created" field
     *
     */
    public function beforeSave1($options = array()) {

        $this->data[$this->alias]['created'] = date("Y-m-d H:i:s", time());
        return true;
    }
    
    /**
     * Function to get queue request by status
     * 
     * @param int $queuetype It is the type of the queue
     * @param int $status It is the status of the queue
     * @param array $info It is a json with info data about the queue
     * @param int $limit It is the limit of queue to collect from DB 
     * @return array It is the queue request
     */
    public function getUsersByStatus($queuetype, $status, $info = null, $limit = null) {
        switch ($queuetype) {
            case FIFO:
                $order = "Queue2.id ASC";
                break;
            case LIFO:
                $order = "Queue2.id DESC";
                break;
        }

        if (empty($status)) {
            $status =  START_COLLECTING;
        }

        $conditions = [];
        if (empty($limit)) {
            $limit = 100;
        }
        if (!empty($info)) {
            $conditions["queue2_info"] = $info;
        }
//        $conditions["queue2_type"] = $queuetype;
        $conditions["queue2_status"] = $status;
        print_r($conditions);
        $result = $this->find("all", array(
                                "conditions" => $conditions,
                                "order" => $order,
                                "limit" => $limit
                            ));

        if (empty($result)) {
            return;
        }

        return $result;
    }
    
    /**
     * Function to retrieve the date of the last time the user get the data
     * 
     * @param string $userReference It is the user's internal id
     * @return array It is the information of the user
     */
    public function calculateDate($userReference) {
        $conditions["queue2_userReference"] = $userReference;
        $result = $this->find("all", array(
                            "conditions" => $conditions,
                            'order' => array('id DESC'),
                            "limit" => 2
                        ));
        return $result;
    }
    
    /**
     *
     * 	Rules are defined for what should happen when a database record is created or updated.
     * 	
     */
    function afterSave($created, $options = array()) {

        if (isset($this->data['Queue2']['queue2_status'])) {                    // A job in Queue2 has finished
            if ($this->data['Queue2']['queue2_status'] == WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED) { 

                $filterConditions = array("queue2_info" => $this->data['Queue2']['queue2_info'], true);
                $result = $this->find("first", array(
                                    "conditions" => $filterConditions,
                                    ));
                
                // Lets personalize the message a little bit, add the company name                
                $queue2_infoDecoded = json_decode($result['Queue2']['queue2_info']);
                $this->print_r2($queue2_infoDecoded);
                $linkedAccountId = $queue2_infoDecoded['companiesInFlow'];
                
                $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
                
                $linkedAccountData = $this->Linkedaccount->getLinkedaccountDataList(['id' => $linkedAccountId]);
                $this->print_r2($companyId);    
                $this->Company = ClassRegistry::init('Company');
                $data = $this->Company->getData( ['id' => $linkedAccountData[0]['Linkedaccount']['company_id'] ]);
 
                $companyName = $data[0]['Company']['company_name'];
                
                if ($result['Queue2']['queue2_type'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
                    $event = new CakeEvent("accountLinkingFullyFinished", $this, 
                                            array('investor_userReference' => $result['Queue2']['queue2_userReference'], 
                                                'messageContent'        => __('Your account on platform') . " " . $companyName . " " .
                                                                           __('has been succesfully linked and analyzed. Your data is now available in your Winvestify Dashboard') 
                                                ));

                    $this->getEventManager()->dispatch($event);
                }
            }
        }
    }   
    

}
 