<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2016, http://beyond-language-skills.com                 |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
* | Author: Antoine de Poorter                                            |
* +-----------------------------------------------------------------------+
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-11-02
* @package
*

2016-11-02	  version 2016_0.1
function cronCollectGlobalMetrics										[Ok, tested]




Pending:
implement rest of counters



*/
App::uses('CakeTime', 'Utility');
class MetricsController extends AppController
{
	var $name = 'Metrics';
	var $helpers = array('Text');
	var $uses = array('Investor', 'Metric');
	var $layout = 'default';		// ????????????????

	
	
function beforeFilter() {

	parent::beforeFilter();
//	$this->Security->requireAuth();
	$this->set('parentController', strtolower($this->name));						// Required for AJAX callback
	$this->set('parentAction', strtolower($this->name) . "Ajax");					// Generic AJAX callback function

	$this->Auth->allow('cronCollectGlobalMetrics');    								// allow these actions without logon	
		
}





/**
*
*	Reads the global metrics data and store it in the metrics database table
*
*/
function cronCollectGlobalMetrics()  {
	
	$this->autoRender = false;
	Configure::write('debug', 2);	


/*
It could be sufficient to load the global counters and per user:
ALL COUNTERS ARE ALSO PER COUNTRY
$actualDateTime = date("Y-m-d H:i:s");
* sequence number (Year+Day_of_year) example: 2016132 = day 132 of 2016
* investor_id
* company_id
* country origin
* investment account
*/

		
// Counter = 'TotalNumberOfUsers', CounterID = 1				Suggested timeperiod = daily
	$db = $this->Investor->getDataSource();
	$directResults = $db->fetchAll(
    'SELECT investor_country, COUNT(*) from investors where investor_accountStatus > 31 GROUP BY investor_country');

	$reference = rand(1000000000000, 9999999999999);

	foreach ($directResults as $result) {
		$this->Metric->create();
		
		$data = array(
			'metric_country' 		=> $result['investors']['investor_country'],
			'metric_typeOfCounter' 	=> ACCUMULATIVE_COUNTER,
			'metric_counterName' 	=> 'TotalNumberOfUsers',
			'metric_counterId' 		=> 1,
			'metric_counterValue' 	=> $result[0]['COUNT(*)'],
			'metric_reference'		=> $reference
		);

		if ($this->Metric->save($data, $validate = true))   {
			$this->Metric->clear();
		}
		else {
			echo "Error while saving, data = xxxx";
		}
	}


// Counter = 'TotalNumberOfActiveUsers', CounterID = 2			Suggested timeperiod = daily
	$days = Configure::read('DefinitionActiveUser');
	$referenceTime = CakeTime::format('-'. $days . ' days', '%Y-%m-%d %H:%M:%S');
	$directResults = $db->fetchAll(
    'SELECT investor_country, COUNT(*) from investors where lastAccessed > "' . $referenceTime . '" GROUP BY investor_country');

	$reference = rand(1000999000000, 9999999999999);
	foreach ($directResults as $result) {
		$this->Metric->create();
		$data = array(
			'metric_country' 		=> $result['investors']['investor_country'],
			'metric_typeOfCounter' 	=> ACCUMULATIVE_COUNTER,
			'metric_counterName' 	=> 'TotalNumberOfActiveUsers',
			'metric_counterId' 		=> 2,
			'metric_counterValue' 	=> $result[0]['COUNT(*)'],
			'metric_reference'		=> $reference
		);
 
		if ($this->Metric->save($data, $validate = true))   {
			$this->Metric->clear();
		}
		else {
			echo "Error while saving, data = xxxx";
		}
	}


// Counter = 'TotalNumberOfUsersWithLinkedAccounts', CounterID = 3				Suggested timeperiod = weekly
	$directResults = $db->fetchAll(
    'SELECT investor_country, COUNT(*) from investors where investor_linkedAccounts > 0 GROUP BY investor_country');
	
	$reference = rand(1000000000000, 9999999999999);
	foreach ($directResults as $result) {
		$this->Metric->create();
		$data = array(
			'metric_country' 		=> $result['investors']['investor_country'],
			'metric_typeOfCounter' 	=> ACCUMULATIVE_COUNTER,
			'metric_counterName' 	=> 'TotalNumberOfUsersWithLinkedAccounts',
			'metric_counterId' 		=> 3,
			'metric_counterValue' 	=> $result[0]['COUNT(*)'],
			'metric_reference'		=> $reference
		);

		if ($this->Metric->save($data, $validate = true))   {
			$this->Metric->clear();
		}
		else {
			echo "Error while saving, data = xxxx";
		}
	}



// Counter = 'NumberOfInvestmentsPerLinkedAccount', CounterID = 4				Suggested timeperiod = weekly





// Counter = 'TotalNumberOfInvestmentsPerUser', CounterID = 5					Suggested timeperiod = weekly





// Counter = 'NumberOfLinkedAccountsPerUser', CounterID = 6						Suggested timeperiod = weekly


	
	
	
// Counter = 'TotalAmountInvestedPerUser', CounterID = 7						Suggested timeperiod = weekly




// Amount invested since last reading for users who have 1 or more accounts linked / per country (timeperiod2)   8



				
				

}

}
