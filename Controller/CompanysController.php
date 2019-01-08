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
 * @date 2016-08-02	
 * @package
 * 

  2016-08-02	  version 2016_0.1





  Pending






 */

//App::uses('CakeEvent', 'Event');
class CompanysController extends AppController {

    var $name = 'Companys';
    var $helpers = array('Session');
    var $uses = array('Company', 'Poll');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
    }

    /**
     *
     * 	Add a new company
     * 	
     */
    function addCompany() {

        $userId = $this->Auth->user('id');
    }

    /**
     *
     * 	Modify one or more data of a company
     * 	
     * 	
     */
    function changeCompany() {

//	$userId = $this->Auth->user('id');	
    }

    /**
     *
     * 	Deletes (make it "invisible" to normal admin) of a company
     * 	
     * 	
     */
    function deleteCompany($companyId) {

//	$userId = $this->Auth->user('id');	
    }

    /**
     *  
     * 	Get all the data of a company
     * 	
     */
    function readCompany() {

//	$userId = $this->Auth->user('id');	
    }

    /**
     *  
     * 	Get all the data of a company
     * 	
     */
    function readCompanyListData() {

        $companyFilterConditions = array('id >' => 0); // read all companies
        $companyResult = getCompanyDataList($companyFilterConditions);
        $this->set('companyResult', $companyResult);
    }

    /**
     *  
     * 	Show "public" data of the companies, some provided by our own users, some 'mined' from
     * 	"public" sources and some generated by ourselves
     * 	The RATE button is only active for the companies where the user has a linked account (AND? an investment??)
     */
    function showCompanyDataPanel() {
        Configure::write('debug', 0);

        $user = $this->Auth->user();

        $companyFilterConditions = array('Company.id >' => 0);   // read all companies
        $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

        $this->set('companyResults', $companyResults);


//	 Get the data for the rating system
    }

    /**
     *  
     * 	Provides extra data about a company
     *
     */
    function readCompanyExtendedData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $error = false;
        $this->layout = 'ajax';
        $this->disableCache();

        $companyId = $_REQUEST['companyId'];

        $companyExtendedDataResult = $this->Company->readExtendedData($companyId);

        $this->set('companyExtendedDataResult', $companyExtendedDataResult);
        $this->set('companyId', $companyId);
        $this->set('error', $error);
    }

    
    /** 
     * This methods terminates the HTTP GET.
     * Format GET /v1/companies.json&_fields=x,y,z
     * Example GET /v1/companies.json&company_country=ES,company_countryName=SPAIN&_fields=company_name,company_country,company_logoGUID
     * 
     * @param -
     * @return array $apiResult A list of elements of array "company"
     */
    public function v1_index(){       
        $this->autoRender = false;
        $this->Company = ClassRegistry::init('Company');

        if (empty($this->listOfFields)) {
            $this->listOfFields = ['id', 'company_name','company_url', 
                                    'company_country', 'company_countryName', 
                                    'company_privacyUrl', 'company_termsUrl',
                                    'company_logoGUID'
                                  ]; 
        } 

        $results = $this->Company->find("all", $params = ['conditions' => $this->listOfQueryParams,
                                                          'fields' => $this->listOfFields,
                                                          'recursive' => -1]);

        $j = 0;
        foreach ($results as $resultItem) { 
            $this->Company->apiVariableNameOutAdapter( $resultItem['Company']);

            foreach ($resultItem['Company'] as $key => $value) {
                $apiResult[$j][$key] = $value;  
            }
            $j++;
        }
        
        $this->Investor->apiVariableNameOutAdapter($apiResult);
        $this->set(['data' => $apiResult,
                  '_serialize' => ['data']]
                   ); 
    }
    
     /** 
     * This methods terminates the HTTP GET.
     * Format GET /v1/companies/[companyId]&fields=x,y,z
     * Example GET /v1/companies/1.json&_fields=company_name,company_countryName
     * 
     * @param int   $id The database identifier of the requested 'Company' resource
     * @return array $apiResult A list of elements of array "company"
     */   
   public function v1_view($id){
        $this->autoRender = false;
                    
        $this->Company = ClassRegistry::init('Company');
        
        if (empty($this->listOfFields)) {
            $this->listOfFields = ['company_name','company_url', 
                                    'company_country', 'company_countryName', 
                                    'company_privacyUrl', 'company_termsUrl',
                                    'company_logoGUID'
                                  ]; 
        }  

        $apiResult = $this->Company->find('first', $params= ['conditions' => ['id' => $id],
                                                          'fields' => $this->listOfFields, 
                                                          'recursive' => -1
                                                         ]);
        
        $this->Investor->apiVariableNameOutAdapter($apiResult)['Company'];
        $this->set(['data' => $apiResult['Company'],
                  '_serialize' => ['data']]
                   );      
    }    
    
    
    
}
