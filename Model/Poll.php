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
* @date 2016-10-07
* @package
*

2016-10-07	  version 2016_0.1
function calculateRating(). Revisit again and use pollId as id, not companyId	[OK, but retest due to changes]
function readQuestionsData()													[OK, tested]
function storeData()															[OK, tested]


Pending:



*/


class Poll extends AppModel
{
	var $name= 'Poll';

	
	public $hasMany = array(
			'Pollanswer' => array(
				'className' => 'Pollanswer',
				'foreignKey' => 'poll_id',
				'fields' => '',
				'order' => '',
				),

			'Pollquestion' => array(
				'className' => 'Pollquestion',
				'foreignKey' => 'poll_id',
				'fields' => '',
				'order' => '',
				),
			);







/**Should run as a background job during the night
*
*	Calculate a new rating based on historical data.
*	This rating is also stored in the database as a datum with 2 decimals, i.e.
*	a rating of 3.62 is stored as integer value 362
*	This function is called after saving a set of answers belonging to one user and one poll.
*	
*	Contains the total result for the pre-defined actual time period, typically a month.
*	On October 12 this value will show the value for the part of the actual period, 
*	contemplating all answers from Oct 1 to Oct 12.
*	
*	@param 		integer	$companyId	Identification of the company
*	@param 		array 	$timeLine	mark start and stop of selected period, both dates inclusive
*	
*	@return 	int		$result		The calculated value 
*
*/
public function calculateRating($companyId, $timeLine) {

//read all records related to poll
	$filterConditions = array('company_id' => $companyId);						  

	$pollResult = $this->find("all", $params = array('recursive'	=> -1,
													  'conditions'  => $filterConditions,
													  'fields'		=> array('id'),
														  ));
	$pollId = $pollResult[0]['Poll']['id'];
	$filterConditions = array('poll_id' => $pollId);
	
	$this->Pollanswer = ClassRegistry::init('Pollanswer');
	$pollAnswerResults = $this->Pollanswer->find("all", $params = array('recursive'	=> -1,
																	  'order'		=> 'Pollanswer.pollanswer_group',
																	  'conditions'  => $filterConditions,
																	  ));	
	
	$weightTotal = 0;
	$numberOfRecords = 0;
	$group = "";
	$grandTotal	= 0;
	
	foreach ($pollAnswerResults as $answer) {
		if ($group == $answer['Pollanswer']['pollanswer_group']) {
			$numberOfQuestions++;
			echo "group = $group and values are weight: " . $answer['Pollanswer']['pollanswer_weight'] . " and answer: " .$answer['Pollanswer']['pollanswer_answer'] ."<br>";
			$weightTotal = $weightTotal + $answer['Pollanswer']['pollanswer_weight'] ;
			$weightedAnswer = $weightedAnswer + $answer['Pollanswer']['pollanswer_weight'] * $answer['Pollanswer']['pollanswer_answer'];
			$temp = $weightedAnswer / $weightTotal;
			echo "Total weighted answer /per group = $temp<br>";
		}
		else {
			$numberOfQuestions = 1;
			if ($numberOfRecords > 0) {
				$grandTotal = ($grandTotal + $temp) / $numberOfRecords;			
			}
			$numberOfRecords++;

			$group = $answer['Pollanswer']['pollanswer_group'];;
			$weightTotal = $answer['Pollanswer']['pollanswer_weight'];
			$weightedAnswer = $answer['Pollanswer']['pollanswer_weight'] * $answer['Pollanswer']['pollanswer_answer'];
		
			echo "group = $group and values are weight: " . $answer['Pollanswer']['pollanswer_weight'] . " and answer: " .$answer['Pollanswer']['pollanswer_answer'] . "<br>";
			$temp = $weightedAnswer / $weightTotal;
			echo "<br>number of records = $numberOfRecords<br>";
		}
		echo "weighttotal = $weightTotal<br>";
	}
	$grandTotal = ($grandTotal + $temp) / $numberOfRecords;	
	$grandTotal = (int) ($grandTotal * 100);
	return $grandTotal;
}





/**
*
*	Provide the necesary data (html) to the browser so a user can rate a company
*	
*	@param 		integer	$pollId		Identification of the poll
*	@return 	array	$result		All related data
*
*/
public function readQuestionsData($pollId) {
	$filterConditions = array('id' => $pollId);						  

	$this->Behaviors->load('Containable');
	$this->contain('Pollquestion');

	$pollResult = $this->find("all", $params = array('recursive'	=> 1,
													  'conditions'  => $filterConditions,
												  ));

	return($pollResult);
}





/**
*
*	Store the poll data as provided by a user
*	
*	@param 		integer	$pollId			Identification of the poll
*	@param 		integer	$investorId		Identification of the investor  NOT (YET) USED
*	@param		array	$datas			All the individual answers to each question
*	(index represents the sequenceNumber)
*	
*	@return 	bool	true			data succesfully stored
*						false			Data could not be stored, no reason known
*
*/
public function storeData($pollId, $investorId, $datas) {

	$this->Pollanswer = ClassRegistry::init('Pollanswer');
	$reference = "";

	foreach ($datas as $key => $data) {
		$this->Pollanswer->clear();
		$tempData['poll_id'] = $pollId;
		$tempData['investor_id'] = $investorId;
		$tempData['pollanswer_group'] = $reference;
		$tempData['pollanswer_sequenceNumber'] = $key; 
		$tempData['pollanswer_answer'] = $data;
		echo __FILE__ . " " . __LINE__ . "<br>";
		pr($tempData);
		
		if ($this->Pollanswer->save($tempData)) {
			echo __FILE__ . " " . __LINE__ . " item saved <br>";
			if (empty( $reference)) {
				$reference = $this->Pollanswer->id;
			}
		}
		else {
			return false;
		}
	}
	$this->Pollanswer->clear();
	$this->Pollanswer->id = $reference;
	$this->Pollanswer->save(array('pollanswer_group' => $reference));
	return true;
}





/**
*JUST AN EXAMPLE
* rules are defined what should happen after a database record is created or updated
*
*
*/
function afterSave ($created, $options = array()) {
	if ($created) {
		if ($this->data[$this->alias]['investment_state'] == PAID_IMMEDIATELY) {		// PAID
			$eventName = 'investmentFinished';
		}
		else {																			// UNPAID
			$eventName = 'investmentConfirmedNotPaid';
		}

		
	
	}
	
}





//
//	Callback Functions
//JUST AN EXAMPLE
public function beforeSave($options = array()) {
// ADD CALCULATE_RATING
//calculateRating()
		if(isset($this->data['Investor']['investor_dateOfBirth']))  {
			$this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
		}
		if(isset($this->data['Investor']['investor_DNI']))  {
			$this->data['Investor']['investor_DNI'] = strtoupper($this->data['Investor']['investor_DNI']);
		}
		
		if ($this->data['Investor']['investor_isCompany'] == 0) {		// reset the company related data
			$this->data['Investor']['investor_companyId'] = "";
			$this->data['Investor']['investor_companyName'] = "";
		}
		
		if(isset($this->data['Investor']['investor_CompanyId']))  {
			$this->data['Investor']['inv
									estor_companyId'] = strtoupper($this->data['Investor']['investor_companyId']);
		}
    return true;
}




}
