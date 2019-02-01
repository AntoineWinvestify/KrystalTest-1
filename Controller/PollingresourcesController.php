<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2019, http://winvestify.com                             |
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
* @author Antoine de Poorter
* @version 0.1
* @date 2019-01-29
* @package
*

2019-01-29	  version 2016_0.1






*/
App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class PollingresourcesController extends AppController
{
    var $name = 'Pollingresources';
    var $uses = array('');
	
 
    function beforeFilter() {
            parent::beforeFilter();

    }




    /** 
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/pollingresources/[pollingresourceId]&fields=x,y,z
     * Example GET /api/1.0/pollingresources/1.json&_fields=company_name,company_countryName
     * 
     * @param int   $id The database identifier of the requested 'Pollingresource' resource
     * @return array $apiResult A list of field (variables) of array "pollingresource"
     */
    function v1_view()  {
        $id = $this->request->id;
                
        if (empty($this->listOfFields)) {
            $this->listOfFields = ['id', 'pollingresource_type','pollingresource_interval', 
                                    'pollingresource_newValueExists'
                                  ]; 
        }  

        $apiResult = $this->Pollingresource->find('first', $params= ['conditions' => ['id' => $id],
                                                          'fields' => $this->listOfFields, 
                                                          'recursive' => -1
                                                         ]);
         
        $this->Pollingresource->apiVariableNameOutAdapter($apiResult['Pollingresource']);
    
        $this->set(['data' => $apiResult['Pollingresource'],
                  '_serialize' => ['data']]
                   ); 	
    }
    
    
    /** 
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/pollingresources.json&par1=value1, par2=value2
     * Example GET /api/1.0/pollingresources.json&pollingresource_newValueExists=1,pollingresource_type=NOTIFICATION_CHECK
     *                                 &_fields=pollingresource_type,pollingresource_interval
     * 
     * @return array $apiResult A list of elements of array "pollingresource"
     */
    function v1_index()  {

        if (empty($this->listOfFields)) {
            $this->listOfFields = ['id', 'pollingresource_type','pollingresource_interval', 
                                    'pollingresource_newValueExists'
                                  ]; 
        } 
        
        // Add condition of pollingresources_status = ACTIVE if not present, and belonging to user
        if (!empty($this->listOfQueryParams)) {
            if (array_key_exists('AND', $this->listOfQueryParams)) {
                if (empty(array_key_exists('pollingresources_status', $data["AND"]) )) {
                    $this->listOfQueryParams["AND"]['pollingresource_userIdentification'] = $this->investorId;
                    $this->listOfQueryParams["AND"]['pollingresource_status'] = ACTIVE;
                    $this->listOfQueryParams["AND"]['pollingresource_interval > '] = 0;
                }
            }
            else {
                if (array_key_exists('OR', $this->listOfQueryParams)) {
                    $this->listOfQueryParams["AND"]['pollingresource_userIdentification'] = $this->investorId;
                    $this->listOfQueryParams["AND"]['pollingresource_status'] = ACTIVE; 
                    $this->listOfQueryParams["AND"]['pollingresource_interval > '] = 0;
                }
                else {
                    $this->listOfQueryParams['pollingresource_userIdentification'] = $this->investorId;
                    $this->listOfQueryParams['pollingresource_status'] = ACTIVE; 
                    $this->listOfQueryParams['pollingresource_interval > '] = 0;
                }
            }    
        }        
        
        $results = $this->Pollingresource->find("all", $params = ['conditions' => $this->listOfQueryParams,
                                                          'fields' => $this->listOfFields,
                                                          'recursive' => -1]);
        $j = 0;
      
        foreach ($results as $resultItem) { 
            $this->Pollingresource->apiVariableNameOutAdapter( $resultItem['Pollingresource']);

            foreach ($resultItem['Pollingresource'] as $key => $value) {
                $apiResult[$j][$key] = $value;  
            }
            $j++;
        }

        $resultJson = json_encode($apiResult);
    
        $this->response->statusCode(200);
        $this->response->type('json');
        $this->response->body($resultJson); 

        return $this->response;   	
    }

    /** 
     * This methods terminates the HTTP DELETE.
     * Format DELETE /api/1.0/pollingresources/[pollingresourceId]
     * Example DELETE /api/1.0/pollingresources/18.json
     * 
     * @param int   $id The database identifier of the 'Pollingresource' resource
     */
    function v1_delete($id)  {
        $id = $this->request->id;

        $apiResult = $this->Pollingresource->api_deletePollingresource($id);

        $this->response->statusCode(204);
        return $this->response; 	
    }


}
