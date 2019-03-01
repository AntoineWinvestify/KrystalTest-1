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

class GlobaldashboardsController extends AppController {

    var $name = 'Globaldashboards';
    var $uses = array('Globaldashboard', 'Company', 'Linkedaccount', 'Tooltip', 'Globaldashboarddelay');
    protected $graphicsResults;         // contains the data of a graphic
    protected $investmentListsResult;   // contains the data of an investment list

    function beforeFilter() {

        parent::beforeFilter();
//	$this->Security->requireAuth();
    }

    public function v1_index() {
        $fields = $this->listOfFields;
        if (empty($fields)) {
            $resultData['data'][] = $this->Globaldashboard->getData(array('Globaldashboard.investor_id' => $this->investorId), 'Globaldashboard.id', 'date DESC', null, 'first')['Globaldashboard'];
        }
        else {
            $resultData['data'][] = $this->Globaldashboard->getData(array('Globaldashboard.investor_id' => $this->investorId), $fields, 'date DESC', null, 'first')['Globaldashboard'];
        }

        $resultJson = json_encode($resultData);
        $this->response->type('json');
        $this->response->body($resultJson);
        return $this->response;
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
    public function v1_viewCustom() {

        //Call configuration
        $pathVendor = Configure::read('winvestifyVendor');
        Configure::load('dashboardConfig.php', 'default');
        $dashboardConfig = Configure::read('Globaldashboard');

        //Save the needed params and filters
        $id = $this->request->id;
        $type = $this->request->pass[0];
        $function = $this->request->pass[1];
        
        if ($dashboardConfig[$type][$function][2]['xAxis'] == 'currency') {
            $dashboardConfig[$type][$function][2]['xAxis'] = 'EUR'/* $this->Linkedaccount->getCurrency($id) */;
        }
        if (empty($dashboardConfig[$type]) || empty($dashboardConfig[$type][$function])) {
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
        $resultData = $this->formatter->$formatterFunction($data, null, $dashboardConfig[$type][$function][2]);

        $resultJson = json_encode($resultData);
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
    public function v1_view() {

        Configure::load('dashboardConfig.php', 'default');
        $dashboardConfigBlock = Configure::read('globalDashboardMainData');
        $id = $this->request->id;
        //$companyId = $this->Linkedaccount->getCompanyFromLinkedaccount($id);  Linkedaccount list
        //Prepare the data for each block
        foreach ($dashboardConfigBlock as $blockKey => $dashboardConfig) {
            if (!empty($dashboardConfig['tooltip_display_name'])) {
                $data['data'][$blockKey]['display_name'] = $dashboardConfig['display_name'];
            }
            if (!empty($dashboardConfig['tooltip_display_name1'])) {
                $data['data'][$blockKey]['display_name1'] = $dashboardConfig['display_name1'];
            }
            if (!empty($dashboardConfig['tooltip'])) {
                $this->Tooltip = ClassRegistry::init('Tooltip');
                $tooltips = $this->Tooltip->getTooltip(array($dashboardConfig['tooltip']), $this->language, $companyId);
                $data['data'][$blockKey]['tooltip_display_name'] = $tooltips[$value['tooltip']];
            }

            foreach ($dashboardConfig['data'] as $key => $value) {
                $data['data'][$blockKey][$key]['display_name'] = $value['display_name'];

                //Search tooltip if the field ave one
                if (!empty($value['tooltip'])) {
                    $this->Tooltip = ClassRegistry::init('Tooltip');
                    $tooltips = $this->Tooltip->getTooltip(array($value['tooltip']), $this->language, $companyId);
                    $data['data'][$blockKey][$key]['tooltip_display_name'] = $tooltips[$value['tooltip']];
                }
                //Defalut graph 1 in the main globaldashboard view.
                if ($value['default_graph']) {
                    $data['data'][$blockKey][$key]['default_graph'] = true;
                }
                //Search value of the data tho show
                $model = $value['value']['model'];
                $this->model = ClassRegistry::init($model);
                if (!empty($value['value'])) {
                    $field = $value['value']['field'];
                    if (empty($value['value']['recursive'])) {
                        $fieldSearch = $field;
                        $keyResult = $model;
                    }
                    else {
                        $fieldSearch = $value['value']['recursive'] . ".$field";
                        $keyResult = $value['value']['recursive'];
                    }
                    $data['data'][$blockKey][$key]['value']['amount'] = $this->model->getData(array('investor_id' => $this->investorId), $fieldSearch, $model . '.date DESC', null, 'first', 1)[$keyResult][$field];
                    if ($value['value']['type'] == 'currency') {      //Seacrh for the currency
                        $data['data'][$blockKey][$key]['value']['currency_code'] = 'EUR'/* $this->Linkedaccount->getCurrency($id) */;
                        $data['data'][$blockKey][$key]['value']['value'] = round($data['data'][$blockKey][$key]['value']['amount'], WIN_SHOW_DECIMAL);
                        unset($data['data'][$blockKey][$key]['value']['amount']);
                    }
                    if ($value['value']['type'] == 'percent') {       //Percent in our db are from 0-1 range, we need multiply them-
                        $data['data'][$blockKey][$key]['value']['percent'] = round($data['data'][$blockKey][$key]['value']['amount'] * 100, WIN_SHOW_DECIMAL);
                        unset($data['data'][$blockKey][$key]['value']['amount']);
                    }
                }
                //Give value to the icon show
                if (!empty($value['icon'])) {
                    $data['data'][$blockKey][$key]['icon'] = $value['icon'];
                }
                //Prepare link to the graphs
                foreach ($value['graphLinksParams'] as $key2 => $linkParam) {
                    $data['data'][$blockKey][$key]['graph_data'][$key2]['url'] = $this->generateLink('globaldashboards', null, $id . DS . $linkParam['link'])['href'];
                    if (!empty($linkParam['displayName'])) {
                        $data['data'][$blockKey][$key]['graph_data'][$key2]['option_display_name'] = $linkParam['displayName'];
                    }
                    if ($key2 == 0) {
                        $data['data'][$blockKey][$key]['graph_data'][$key2]['default'] = true;
                    }
                    else {
                        $data['data'][$blockKey][$key]['graph_data'][$key2]['default'] = false;
                    }
                }
            }
        }

        if (!empty($data['data']['payment_delay'])) {
            $data['data']['payment_delay']['graph_data'] = array(
                "url" => $this->generateLink("dashboards", "list", $id . "/graphs/payment-delay-graph-data")['href'],
                "default" => true,
            );
        }
        
        $data['data']['kpis'] = $this->generateLink('globaldashboards', null, "$id/lists/kpis")['href'];
        
        
        $linkedAccountList = $this->Linkedaccount->find('list', array(
            'conditions' => array('Accountowner.investor_id' => $this->investorId),
            'field' => 'Linkedaccount.id',
            'recursive' => 1,
        ));
        foreach ($linkedAccountList as $linkedaccout) {
            $data['data']['links'][] = $this->generateLink('dashboards', 'list', $linkedaccout);
        }

        $resultJson = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJson);
        return $this->response;
    }

}
