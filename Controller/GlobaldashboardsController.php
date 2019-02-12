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

class GlobaldashboardsController extends AppController
{
	var $name = 'Globaldashboards';
	var $uses = array('Globaldashboard', 'Company', 'Linkedaccount', 'Tooltip');
        protected $graphicsResults;         // contains the data of a graphic
        protected $investmentListsResult;   // contains the data of an investment list

	
	
function beforeFilter() {

	parent::beforeFilter();
//	$this->Security->requireAuth();

}

    /** PENDING: ERROR HANDLING TOWARDS HTTP
     * This methods terminates the HTTP GET.
     * Format:
     * GET /api/1.0/globaldashboards/{investorId}/
     * Example: GET /api/1.0/globaldashboards/1051/
     * 
     * @param -
     * 
     */
    public function v1_viewCustom(){
        
        //Call configuration
        $pathVendor = Configure::read('winvestifyVendor');
        Configure::load('dashboardConfig.php', 'default');
        $dashboardConfig = Configure::read('Globaldashboard');
        
        //Save the needed params and filters
        $id = $this->request->id;
        $type = $this->request->pass[0];
        $function = $this->request->pass[1];
        /*$companyId = $this->Linkedaccount->getCompanyFromLinkedaccount($id);
        if($dashboardConfig[$type][$function][2]['xAxis'] == 'currency'){
            $dashboardConfig[$type][$function][2]['xAxis'] = $this->Linkedaccount->getCurrency($id);
        }*/
        if( empty($dashboardConfig[$type]) || empty($dashboardConfig[$type][$function]) ){
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

        //Save the function name in another var to call it later
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
    
    
    /** PENDING: ERROR HANDLING TOWARDS HTTP

     * GET /api/1.0/globaldashboards/{investorId}/{graphicsIdentification}?period=year
     * Example: GET /api/1.0/globaldashboards/1051/graphics/active-investments-graph-data?period=year
     * 
     * @param -
     * 
     */
    public function v1_view(){
        
        Configure::load('dashboardConfig.php', 'default');
        $dashboardConfig = Configure::read('globalDashboardMainData');
        $id = $this->request->id;
        //$companyId = $this->Linkedaccount->getCompanyFromLinkedaccount($id);

        foreach($dashboardConfig as $key => $value){
            $data['data'][$key]['display_name'] = $value['display_name'];
            
            //Search tooltip if the field ave one
            if( !empty($value['tooltip']) ){
                $this->Tooltip = ClassRegistry::init('Tooltip');
                $tooltips = $this->Tooltip->getTooltip( array($value['tooltip']), $this->language, $companyId);
                $data['data'][$key]['tooltip_display_name'] = $tooltips[$value['tooltip']];
            }

            //Search value
            $model = $value['value']['model'];
            $this->model = ClassRegistry::init($model);
            if( !empty($value['value']) ){
                $field = $value['value']['field'];
                $data['data'][$key]['value']['amount'] = $this->model->getData(array('investor_id' => $id), $field, 'date DESC', null, 'first')[$model][$field];
                /*if($value['value']['type'] == 'currency'){      //Seacrh for the currency
                    $data['data'][$key]['value']['currency_code'] = $this->Linkedaccount->getCurrency($id);
                };*/
                if($value['value']['type'] == 'percent'){       //Percent in our db are from 0-1 range, we need multiply them-
                    $data['data'][$key]['value']['amount'] = $data['data'][$key]['value']['amount']*100;
                }
            }
            if( !empty($value['icon']) ){
                $data['data'][$key]['icon'] = $value['icon'];
            }
            foreach($value['graphLinksParams'] as $key2 => $linkParam){
                $data['data'][$key]['graph_data'][$key2]['url'] = $this->generateLink('dashboards', null, $linkParam['link'])['href'];
                if(!empty($linkParam['displayName'])){
                    $data['data'][$key]['graph_data'][$key2]['option_display_name'] = $linkParam['displayName'];
                }
                if($key2 == 0){
                    $data['data'][$key]['graph_data'][$key2]['default'] = true;
                }
                else{
                    $data['data'][$key]['graph_data'][$key2]['default'] = false;
                }
            }
        } 
        $resultJson = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;  
    }
} 
    
    
       
