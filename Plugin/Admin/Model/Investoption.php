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
* @date 2016-01-10
* @package
*


2016-01-10		version 0.1
multi-language support added




Pending:

*/


class Investoption extends AppModel
{
	var $name= 'Investoption';

	var $belongsTo = array(
		'Startup' => array(
			'className' => 'Startup',
			'foreignKey' => 'startup_id',
		)
	);




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(

);





/**
*
*
*
*/
public function addConfirmedPayment($amount, $investOption)  {
	$this->Investoption = ClassRegistry::init('Investoption');
	$result = $this->Investoption->find("all", $params = array('recursive'	=>  -1,
															'conditions'	=> array ('id' => $investOption),
																	)
											);

	$newAmount = $result[0]['Investoption']['investoption_totalSubscribedConfirmed'] + $amount;

	$this->Investoption->create();
	$requestData = array('id' 										=> $investOption,
						 'investoption_totalSubscribedConfirmed' 	=> $newAmount
						 );
	if ($this->Investoption->save($requestData, $validate = TRUE)) {
		$id = $this->Investoption->id;
		return true;
	}
}





/**
*this should eventually always be 0
*
*/
public function addUnConfirmedPayment($requestData) {
	Configure::write('debug', 0);
	$this->Investoption = ClassRegistry::init('Investoption');
	$result = $this->Investoption->find("all", $params = array('recursive'	=>  -1,
															'conditions'	=> array ('id' => $investOption),
																	)
											);

	$newAmount = $result[0]['Investoption']['investoption_totalSubscribed'] + $amount;

	$this->Investoption->create();
	$requestData = array('id' 							=> $investOption,
						 'investoption_totalSubscribed' => $newAmount
						 );
	if ($this->Investoption->save($requestData, $validate = TRUE)) {
		$id = $this->Investoption->id;
		return true;
	}
}





/**
*	Moves money from unconfirmed payment to confirmed payment
*
*
*/
public function convertToConfirmedPayment($amount, $investOption)  {
	$this->Investoption = ClassRegistry::init('Investoption');
	$result = $this->Investoption->find("all", $params = array('recursive'	=>  -1,
															'conditions'	=> array ('id' => $investOption),
																	)
											);

	$newTotalSubscribedConfirmedAmount = $result[0]['Investoption']['investoption_totalSubscribedConfirmed'] + $amount;
	$newTotalSubscribedAmount = $result[0]['Investoption']['investoption_totalSubscribedConfirmed'] + $amount;

	$this->Investoption->create();
	$requestData = array('id' 										=> $investOption,
						 'investoption_totalSubscribedUConfirmed' 	=> $newTotalSubscribedConfirmedAmount,
						 'investoption_totalSubscribed'				=> $newTotalSubscribedAmount
						 );
	if ($this->Investoption->save($requestData, $validate = TRUE)) {
		$id = $this->Investoption->id;
		return true;
	}
}





/** NOT TESTED
*	User has not paid the investment, and eventually the investment is removed. This can/is also be done automatically via
*	a cron job.
*
*
*/
public function deleteUnConfirmedPayment($amount, $investOption)  {
	$this->Investoption = ClassRegistry::init('Investoption');
	$result = $this->Investoption->find("all", $params = array('recursive'	=>  -1,
															'conditions'	=> array ('id' => $investOption),
																	)
											);

	$newTotalSubscribedConfirmedAmount = $result[0]['Investoption']['investoption_totalSubscribedConfirmed'] + $amount;
	$newTotalSubscribedAmount = $result[0]['Investoption']['investoption_totalSubscribedConfirmed'] + $amount;

	$this->Investoption->create();
	$requestData = array('id' 										=> $investOption,
						 'investoption_totalSubscribedUConfirmed' 	=> $newTotalSubscribedConfirmedAmount,
						 'investoption_totalSubscribed'				=> $newTotalSubscribedAmount
						 );
	if ($this->Investoption->save($requestData, $validate = TRUE)) {
		$id = $this->Investoption->id;
		return true;
	}
}





/**
*
*
*
*/
public function beforeSave1($options = array()) {

	if (!$this->id && empty($this->data[$this->alias]['id'])) {		 // = create a new record
		$tempTime = CakeTime::convert(time(), new DateTimeZone('Europe/Madrid'));
		$this->data['Operation']['dateChargeInitiated'] = CakeTime::format($tempTime, '%Y-%m-%d');
	}

    return true;
}







public function beforeDelete1($cascade = true) {
		$infoString  =  "Student : ";
		$infoString  .=  " and dob = " ;
		CakeLog::write('beforeDelete', $infoString);
	return true;
}







public function onError()  {
		$infoString  =  "Student : " ;
		$infoString  .=  " and dob = " . " has been registered";
		CakeLog::write('onError', $infoString);
	return true;
}





/**
*
*/
public function afterFind1($results, $primary = false) {

// If "dateOfBirth" is request then add as bonus the age of the client
	foreach ($results as $key => $val) {
		if (isset($val['User']['dateOfBirth'])) {
			$results[$key]['User']['age'] = $this->calculateAge($results[$key]['User']['dateOfBirth']);
		}

	}
	return $results;
}








}
