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
    var $uses = array('Pollingresource');
	
 
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
        $this->checkAcl();
        $id = $this->request->id;
          
        $key = array_search('pollingresource_links', $this->listOfFields);

        if (!empty($key)) {
            unset($this->listOfFields[$key]);
            $linksField = true;
        }       

        $apiResult = $this->Pollingresource->find('first', $params = ['conditions' => ['id' => $id],
                                                          'fields' => $this->listOfFields, 
                                                          'recursive' => -1
                                                         ]);
       
        if ($linksField) {
            $apiResult['Pollingresource']['links'][] = $this->generateLink("pollingresources", "delete" , $id . '.json'); 
            $apiResult['Pollingresource']['links'][] = $this->generateLink("pollingresources", "self" , $id . '.json');
        }       
        
        $this->Pollingresource->apiVariableNameOutAdapter($apiResult['Pollingresource']);
       
        $resultJson = json_encode($apiResult['Pollingresource']);
        $this->response->statusCode(200);
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;  

    }
    
    
    /** 
     * This method terminates the HTTP GET.
     * Format GET /api/1.0/pollingresources.json&par1=value1, par2=value2
     * Example GET /api/1.0/pollingresources.json&pollingresource_newValueExists=1,pollingresource_type=NOTIFICATION_CHECK
     *                                 &_fields=pollingresource_type,pollingresource_interval
     * 
     * @return array $apiResult A list of elements of array "pollingresource"
     */
    function v1_index()  {
        $this->checkAcl();    
        $linksField = false;
        $idField = true;

        if (in_array('pollingresource_links',$this->listOfFields)) {
            $key = array_search('pollingresource_links', $this->listOfFields); 
            unset ($this->listOfFields[$key]);
            $linksField = true;
        }
         
        $key = in_array('id', $this->listOfFields);
        if (!$key) {
            array_push($this->listOfFields, 'id');
            $idField = false;
        }

        $results = $this->Pollingresource->find("all", $params = ['conditions' => $this->filterConditionQueryParms,
                                                          'fields' => $this->listOfFields,
                                                          'recursive' => -1]);
       
        $j = 0;    
        foreach ($results as $resultItem) { 
            $this->Pollingresource->apiVariableNameOutAdapter( $resultItem['Pollingresource']);
            foreach ($resultItem['Pollingresource'] as $key => $value) {
                if ($key === "id"){
                    $id = $value;
                }
                $apiResult[$j][$key] = $value;  
            }
            if (!$idField) {
                unset ($apiResult[$j]['id']);
            } 
            if ($linksField) {
                $apiResult[$j]['links'][] = $this->generateLink("pollingresources", "delete" , $id . '.json'); 
                $apiResult[$j]['links'][] = $this->generateLink("pollingresources", "self" , $id . '.json');
            }
            $j++;
        }

        $resultJson = json_encode(['data' => $apiResult]);
    
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
     * @return Object resp
     * @throws UnauthorizedException
     */
    function v1_delete($id)  {
        $this->checkAcl();        
        $id = $this->request->id;

        // Only pollingresource owner can delete it
        $results = $this->Pollingresource->findById($id);
        if ($this->investorId <> $results['Pollingresource']['pollingresource_userIdentification']) {
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }
          
        $apiResult = $this->Pollingresource->delete($id);
        $this->response->statusCode(204);
        return $this->response; 	
    }

 
    /** 
     * Simple version for creating a Pollingresource. USED FOR TESTING PURPOSES ONLY
     * This methods terminates the HTTP POST for defining a new Pollingresource object.
     * Format POST /api/1.0/pollingresources.json
     * Example POST /api/1.0/pollingresources.json
     * All the data is located in the POST body as a json object 
     * This command can be used by superAdmin only
     * 
     * @return mixed false or the Links object of the new 'Investor' object
     */
    public function v1_add() { 
        $this->checkAcl(); 
        if ($this->roleName <> "superAdmin") {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }   
        
        $result = $this->Pollingresource->api_addPollingresource($this->request->data);
        
        if (!($result)) {
            $validationErrors = $this->Pollingresource->validationErrors;              // Cannot retrieve all validation errors
            $this->Pollingresource->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('CANNOT_CREATE_POLLINGRESOURCE_OBJECT', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(400);                                    
        }
        else { // create the links
            $account['feedback_message_user'] = 'Account successfully created.';
            $account['data']['links'][] = $this->generateLink("pollingresources", "self", $result . '.json'); 
            $account['data']['links'][] = $this->generateLink("pollingresources", "delete" , $result . '.json');             
            $resultJson = json_encode($account);           
            $this->response->statusCode(201);
        }
        
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }      
    
}
