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
     * Format GET /api/1.0/dashboards.json&_fields=x,y,z
     * Example GET /api/1.0/dashboard.json&investor_country=SPAIN&_fields=investor_name,investor_surname
     * 
     * Other format:
     * GET /api/1.1/dashboards/{linkedAccountId}/{graphicsIdentification}?period=year
     * Example: GET /api/1.1/dashboards/1051/graphics/active-investments-graph-data?period=year
     * 
     * @param -
     * 
     */
    public function v1_view(){
        $id = $this->request->params['id'];
        if (!empty($this->request->pass)) {                 // Format for collecting a graphics item or investmentlist  
            switch ($this->request->pass[1]) {
                case "lists":
                    $this->readDashboardInvestmentLists($this->request->pass[2]);
                    $result = &$this->investmentListsResult;
                    break;      
                case "graphics":                 
                    $this->readDashboardgraphics($this->request->pass[2]);
                    $result = &$this->graphicsResults;
                    break;
                default:
                    $this->response->statusCode(400);   
                    $this->response->type('json'); 
                    return $this->response; 
            }

        $resultJson = json_encode($result);
 
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response; 
        }
        
        // ALL THE REST OF THE GET METHOD

    }
  
  
    
    /**
     * Switch function for connecting a function to the collection of data for an investment list
     * 
     * @param string $investmentListName Name of investmentlist to collect
     * @return boolean
     */
    public function readDashboardInvestmentLists($investmentListName) {
        switch ($investmentListName) {
            case "duplicityinvestmentslist":
                $result = $this->readDuplicityInvestmentslist($this->request->pass[0]);
                break;      
            case "activeinvestmentslist":
                $result = $this->readActiveInvestmentsList($this->request->pass[0]);
                break;
            case "defaultedinvestmentslist":
                $result = $this->readDefaultedInvestmentsList($this->request->pass[0]);
                break;                   
            default:
                $result = false;       
        }
        return $result;
    }    
    
    
    /**   We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies More info + Got it!
     * Switch function for connecting a function to the collection of data for a graphic
     * 
     * @param string $graphicsName Name of graphic to collect
     * @return boolean
     */
    public function readDashboardgraphics($graphicsName) {
        switch ($graphicsName) {
            case "nar-graph-data":
                $result = $this->readNarGraphData($this->request->pass[0]);
                break;      
            case "cash-drag-graph-data":
                $result = $this->readCashDragGraphData($this->request->pass[0]);
                break;
            case "active-investments-graph-data":
                $result = $this->readActiveInvestmentsGraphData($this->request->pass[0]);
                break;      
            case "current-graph-data":
                $result = $this->readCurrentGraphData($this->request->pass[0]);
                break;        
            case "payment-delays-graph-data":
                $result = $this->readPaymentDelaysGraphData($this->request->pass[0]);
                break;      
            case "net-deposits-graph-data":
                $result = $this->readNetDepositsGraphData($this->request->pass[0]);
                break;
            case "netannual-returns-graph-data":
                $result = $this->readNetAnnualReturnsGraphData($this->request->pass[0]);
                break;      
            case "nar-last365days-graph-data":
                $result = $this->readNarLast365daysGraphData($this->request->pass[0]);
                break; 
            case "financial-exposure-graph-data":
                $result = $this->readFinancialExposureGraphData($this->request->pass[0]);
                break;              
            default:
                $result = false;                               
        }  
        return $result;
    }
   
    
    
    /**
     * Read the historical data of the datum "userinvestmentdata_activeInvestments"
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */  
    public function readActiveInvestmentsGraphData($linkedAccountId)  {
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
        $this->listOfQueryParams['period'];
        
        $conditions = ['linkedaccount_id' => $linkedAccountId];
  
        switch ($this->listOfQueryParams['period']) {
            case "all":              
                break; 
            case "year":
                App::uses('CakeTime', 'Utility');   
                $conditions['date >'] = CakeTime::format('-1 year', '%Y-%m-%d'); 
                break;              
            default:
                return false;        
        }

        $result = $this->Userinvestmentdata->find('all', $param = [
                            'conditions' => $conditions,
                                'fields' => ['id', 'date', 
                                'userinvestmentdata_numberActiveInvestments as value'
                                            ],
                             'recursive' => -1,
        ]);       
        
        $resultNormalized = Hash::extract($result, '{n}.Userinvestmentdata');
 
        $this->graphicsResults = ["graphics_data" => ["dataset" => 
                                                    ["display_name" => "Mintos",
                                                       "data" => $resultNormalized]]];
        return true;
    }
    

      



    /** HAY QUE DEFINIR LOS TOOLTIPS 
     * Read the data of an investment list
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */  
    public function readActiveinvestmentsList($linkedAccountId)  {
        $this->Investment = ClassRegistry::init('Investment');
        $this->language = "en";
        $linkedAccountResult = $this->Linkedaccount->find("first", $param = 
                                            ['conditions' => ['id' => $linkedAccountId],
                                                'fields' => ['linkedaccount_currency'],
                                                'recursive' => -1]);
        $currency = $linkedAccountResult['Linkedaccount']['linkedaccount_currency'];   
        
        $this->Investment->virtualFields = [
            'myInvestmentFloat' => '(CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . ')))',
            'interestFloat' => 'CAST(`Investment.investment_nominalInterestRate` as decimal(30, ' . WIN_SHOW_DECIMAL . '))/100',
            'outstandingFloat' => 'CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))',
            'progressFloat' => 'CAST((((CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) - CAST(`Investment.investment_outstandingPrincipal` as decimal(30, ' . WIN_SHOW_DECIMAL . '))) / (CAST(`Investment.investment_myInvestment` as decimal(30,' . WIN_SHOW_DECIMAL . ')) + CAST(`Investment.investment_secondaryMarketInvestment` as decimal(30, ' . WIN_SHOW_DECIMAL . '))))*100 as decimal(30, ' . WIN_SHOW_DECIMAL . '))'
        ];
        $conditions = ['investment_statusOfLoan' => 2,
                            'linkedaccount_id' => $linkedAccountId];

        $investmentResults = $this->Investment->find('all', $params = [
                                                    'conditions' => $conditions,
                                            //        'limit'  => 2,
            'fields' => ['investment_loanId', 'myInvestmentFloat', 'date', 'interestFloat', 'interestFloat','outstandingFloat', 'progressFloat', 'investment_paymentStatus' ],
                                                    'recursive' => -1
            
        ]);
        $investmentResultsNormalized = Hash::extract($investmentResults, '{n}.Investment');

        foreach ($investmentResultsNormalized as $key => $item) {
            $i = 0;
            foreach ($item as $key1 => $value) {
                switch ($i + 1) {
                    case 1:
                        $temp[$key][$i] = $value;
                        $i++;
                    break;
                    case 2:
                    case 7:
                        $temp[$key][$i]['date'] = $value;
                        $temp[$key][$i]['date_alias'] = str_replace("-", "", $value);
                        $i++;
                    break;   
                    case 3:
                    case 6:
                        $temp[$key][$i]["value"]["amount"] = $value;
                        $temp[$key][$i]["value"]["currency_code"] = $currency;     
                        $i++;
                    break;
                    case 4:
                    case 5:
                        $temp[$key][$i]["value"]["percent"] = $value;
                        $i++;
                    break;           
                    case 8:
                        $temp[$key][$i]["value"]["delay"] = $value;
                        $temp[$key][$i]["value"]["unit"] = "days";
                    break;
                }
            }
        }

        $this->investmentListsResult["display_name"] = "Active Investments";
        $this->investmentListsResult['header'] = $this->createActiveInvestmentsListHeader();
        
        $tooltip = $this->Tooltip->getTooltip([ INVESTMENT_LIST_GLOBALTOOLTIP], $this->language);  
        $this->investmentListsResult['tooltip_display_name'] = $tooltip[INVESTMENT_LIST_GLOBALTOOLTIP];     
        $this->investmentListsResult['data'] = $temp;
        return true;    
    }
     
     
    /**
     * Read the data of an investment list
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */  
    public function createActiveInvestmentsListHeader()  { 

        $tooltipIdentifiers = [
            INVESTMENT_LIST_LOANID,
            INVESTMENT_LIST_INVESTMENTDATE, 
            INVESTMENT_LIST_MYINVESTMENT, 
            INVESTMENT_LIST_INTEREST, 
            INVESTMENT_LIST_INSTALLMENTPROGRESS,
            INVESTMENT_LIST_OUTSTANDINGPRINCIPAL,
            INVESTMENT_LIST_NEXTPAYMENT, 
            INVESTMENT_LIST_STATUS,
        ];

        $displayHeaders = [
            "Loan ID", 
            "Investment Date",
            "My Investment",
            "Interest", 
            "Instalment Progress",
            "Outstanding Principal",
            "Next Payment",
            "Status"
        ];

        $tooltips = $this->Tooltip->getTooltip($tooltipIdentifiers, $this->language);

        $i = 0;
        foreach ($displayHeaders as $key => $displayHeader) {
            $header[$i]['displayName'] = $displayHeader;
            if (array_key_exists($tooltipIdentifiers[$key], $tooltips)) {
                $header[$i]['tooltipDisplayName'] = $tooltips[$tooltipIdentifiers[$key]] . "key = $key";
            }
            $i++;
        }
        return $header;
    }    
        
    
} 
    
    
       