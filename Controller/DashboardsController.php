<?php
/**
* +-----------------------------------------------------------------------+
* | Copyright (C) 2019, http://www.winvestify.com                         |
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
*
*
* @author
* @version 0.1
* @date 2016-09-22
* @package
*
*/


App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class DashboardsController extends AppController
{
	var $name = 'Dashboards';
	var $helpers = array('Html', 'Form', 'Js', 'Text');
	var $uses = array('Dashboard', 'Company', 'Linkedaccount', 'Tooltip');
        protected $graphicsResults;         // contains the data of a graphic
        protected $investmentListsResult;   // contains the data of an investment list

	
	
function beforeFilter() {

	parent::beforeFilter();
//	$this->Security->requireAuth();

}





/**
*
*	Reads all the  data of ALL investments in all the companies where the investor
*	has a linked account
*
*/
function getDashboardData()  {

	$this->layout = "azarus_private_layout";
	
	$this->Data = ClassRegistry::init('Data');
	$this->Linkedaccount = ClassRegistry::init('Linkedaccount');
	
	$investorReference = $this->Session->read('Auth.User.Investor.investor_identity');
	$filterConditions = array('data_investorReference' => $investorReference);
	
	$dataResult = $this->Data->find("first", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                                        "order"     => "created DESC",
									));

// Check if investor already has linked one or more accounts. 									
	$resultLinkedaccounts = $this->Linkedaccount->find("count", array("investor_id" => $this->Session->read('Auth.User.Investor.id'),
                                                            'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE
                                                            ));
																											
	if ($resultLinkedaccounts > 0) {	// user has one or more linked accounts
		$dashboardGlobals = JSON_decode($dataResult['Data']['data_JSONdata'], true);
	
		$this->set('dashboardGlobals', $dashboardGlobals);
		$this->set('refreshDate', $dataResult['Data']['created']);
		$this->set('investmentRefreshInProgress', $this->Session->read('investmentRefreshInProgress'));
	
		$dashboardGlobals = JSON_decode($dataResult['Data']['data_JSONdata'], true);
	
	
// MY BALANCE
		$labelsPieChart = array();
		$dataPieChart = array();
			foreach ($dashboardGlobals['investments'] as $key => $companyInvestment) {
				$value = (int) $companyInvestment['global']['myWallet'] / 100;
				$dataPieChart[] = $value;
				$labelsPieChart[] = $key;
				$this->set('pieChart1Empty', false);				
			}
		if (empty($labelsPieChart)) {
                    $this->set('pieChart1Empty', true);
		}
                if (empty($dataPieChart)) {
                    $dataPieChart[0] = "No data";
                }
		$this->set('labelsPieChart1', $labelsPieChart);
		$this->set('dataPieChart1', $dataPieChart);
	
		
// SALDO VIVO
		$labelsPieChart = array();
		$dataPieChart = array();
			foreach ($dashboardGlobals['investments'] as $key => $companyInvestment) {
				$value = (int) $companyInvestment['global']['activeInInvestments'] / 100;
				$dataPieChart[] = $value;
				$labelsPieChart[] = $key;
				$this->set('pieChart2Empty', false);
			}
		if (empty($labelsPieChart)) {
                    $this->set('pieChart2Empty', true);
		}
                if (empty($dataPieChart)) {
                    $dataPieChart[0] = "No data";
                }
		$this->set('labelsPieChart2', $labelsPieChart);
		$this->set('dataPieChart2', $dataPieChart);
	
	
// DINERO INVERTIDO EN INVERSIONES ACTIVAS
		$labelsPieChart = array();
		$dataPieChart = array();
			foreach ($dashboardGlobals['investments'] as $key => $companyInvestment) {
				$value = (int) $companyInvestment['global']['totalInvestment'] / 100;
				$dataPieChart[] = $value;
				$labelsPieChart[] = $key;
				$this->set('pieChart3Empty', false);
			}
		if (empty($labelsPieChart)) {
                    $this->set('pieChart3Empty', true);
		}
                if (empty($dataPieChart)) {
                    $dataPieChart[0] = "No data";
                }
		$this->set('labelsPieChart3', $labelsPieChart);
		$this->set('dataPieChart3', $dataPieChart);
		
		
		$this->set('dashboardGlobals', $dashboardGlobals);
//		$this->print_r2($dashboardGlobals);
	}
	else {	// User does not have linked accounts
		$noAccountsLinked = true; 
	}
	$this->set('noAccountsLinked', $noAccountsLinked);
}





/**
*
* Read the individual investment data of an investor for his/her dashboard
*
*/
function readInvestmentData($company) {

	if (! $this->request->is('ajax')) {
		throw new
			FatalErrorException(__('You cannot access this page directly'));
	}

	$this->layout = 'ajax';
	$this->disableCache();

	$this->Data = ClassRegistry::init('Data');

	$investorReference = $this->Session->read('Auth.User.Investor.investor_identity');
	$filterConditions = array('data_investorReference' => $investorReference);
	
	$dataResult = $this->Data->find("first", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                        		"order"     => "created DESC",
									));

	$companyInvestmentDetails = JSON_decode($dataResult['Data']['data_JSONdata'], true);
	$this->set('companyInvestmentDetails', 	$companyInvestmentDetails['investments'][$company]['investments']);
}






    /** PENDING: ERROR HANDLING TOWARDS HTTP
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/dashboards.json?_fields=x,y,z
     * Example GET /api/1.0/dashboard.json?investor_country=SPAIN&_fields=investor_name,investor_surname
     * 
     * Other format:
     * GET /api/1.1/dashboards/{linkedAccountId}/{graphicsIdentification}?period=year
     * Example: GET /api/1.1/dashboards/1051/graphics/active-investments-graph-data?period=year
     * 
     * @param -
     * 
     */
    public function v1_viewCustom(){
        
        //Call configuration
        $pathVendor = Configure::read('winvestifyVendor');
        Configure::load('dashboardConfig.php', 'default');
        $dashboardConfig = Configure::read('Dashboard');
        
        //Save needed params and filters
        $id = $this->request->id;
        $type = $this->request->pass[0];
        $function = $this->request->pass[1];
        $companyId = $this->Linkedaccount->getCompanyFromLinkedaccount($id);
        if($dashboardConfig[$type][$function][2]['xAxis'] == 'currency'){
            $dashboardConfig[$type][$function][2]['xAxis'] = $this->Linkedaccount->getCurrency($id);
        }
        if(empty($dashboardConfig[$type]) || empty($dashboardConfig[$type][$function])){
            $this->response->statusCode(400);   
            $this->response->type('json'); 
            return $this->response; 
        }

        
        //1 is always the model used for the data search 2 is always the vendor used for formatting
        $key1 = key($dashboardConfig[$type][$function][0]);
        $key2 = key($dashboardConfig[$type][$function][1]); 

        //Call formatter and model
        $this->Searchmodel = ClassRegistry::init($key1);
        include_once ($pathVendor . 'Classes' . DS . "$key2.php");
        $this->formatter = new $key2();
 
        //Save the functions name in another var to call it later
        $searchModelFunction = $dashboardConfig[$type][$function][0][$key1];
        $formatterFunction = $dashboardConfig[$type][$function][1][$key2];

        //Search the data and format it
        $data = $this->Searchmodel->$searchModelFunction($id, $this->listOfQueryParams);
        $result = $this->formatter->$formatterFunction($data, $companyId, $dashboardConfig[$type][$function][2]);

        $resultJson = json_encode($result);
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;  
    }
} 
    
    
       