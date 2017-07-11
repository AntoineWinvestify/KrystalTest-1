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

  2016-10-07	  version 2016_0.1
  function getCompanyDataList(). Revisit again and use pollId as id, not companyId	[OK, but retest due to changes]
  function getCompanyList()															[OK, tested]
  function readExtendedData()															[not OK, not tested]

  [2017-06-23] Version 1.0 (for ocr)
  companiesDataOCR                                    [OK, tested]
  db relation

  [2017-06-28] Version 1.1 (for ocr)
  Check status

  [2017-06-29] Version 1.1 (for ocr)
  Update pfp added

 */
class Company extends AppModel {

    var $name = 'Company';
    public $hasOne = array(
        'Serviceocr' => array(
            'joinTable' => 'Serviceocrs',
            'foreignKey' => 'company_id',
        ),
    );
    var $hasMany = array(
        'Marketplace' => array(
            'className' => 'Marketplace',
            'foreignKey' => 'company_id',
        ),
        'requiredfiles' => array(
            'className' => 'Ocrfile',
            'joinTable' => 'requiredfiles',
            'foreignKey' => 'company_id',
            'associationForeignKey' => 'file_id',
        )
    );
    public $hasAndBelongsToMany = array(
        'Ocr' => array(
            'className' => 'Ocr',
            'joinTable' => 'companies_ocrs',
            'foreignKey' => 'company_id',
            'associationForeignKey' => 'ocr_id',
        ),
        'Ocrfile' => array(
            'className' => 'Ocrfile',
            'joinTable' => 'companies_files',
            'foreignKey' => 'company_id',
            'associationForeignKey' => 'file_id',
        ),
    );

    /**
     * 	Apparently can contain any type field which is used in a field. It does NOT necessarily
     * 	have to map to a existing field in the database. Very useful for automatic checks
     * 	provided by framework
     */
    var $validate = array(
        'company_termsUrl' => array(
            'rule' => array('minLength', 1),
            'message' => 'Too short.'
        ),
        'company_privacyUrl' => array(
            'rule' => array('minLength', 1),
            'message' => 'Too short.'
        ),
        'company_type' => array(
            'rule' => array('notBlank'),
            'message' => 'Select one type.'
        ),
        'company_country' => array(
            'rule' => array('notBlank'),
            'message' => 'Select one country.'
        ),
    );

    /*     * STILL TO BE DONE
     *
     * 	Returns a *LIST* of companies that fullfil the filterConditions
     * 	
     * 	@return array  array of all company Ids that fullfil filtering conditions
     * 			
     */

    public function getCompanyList($filterConditions) {

        $businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
            'Company.company_state' => ACTIVE);

        $conditions = array_merge($businessConditions, $filterConditions);

        $companyResult = $this->find("list", $params = array('recursive' => -1,
            'conditions' => $conditions,
        ));

        return($companyResult);
    }

    /**
     *
     * 	Returns an array of the companies and their data that fullfil the filterConditions
     * 	
     * 	@param 		array 	$filteringConditions	
     * 	@return 	array 	 Data of each company as an element of an array
     * 			
     */
    public function getCompanyDataList($filterConditions) {

        $businessConditions = array('Company.company_isActiveInMarketplace' => ACTIVE,
            'Company.company_state' => ACTIVE);

        array_push($businessConditions, $filterConditions);

        $companyResult = $this->find("all", $params = array('recursive' => -1,
            'conditions' => $businessConditions,
        ));
// 'Normalize' the total array, index XX points to company with id = XX
        foreach ($companyResult as $value) {
            $companyResults[$value['Company']['id']] = $value['Company'];
        }
        return $companyResults;
    }

    /**
     * Get info needed for ocr.
     * @param type $filter
     * @return type
     */
    public function companiesDataOCR($filter = null) {

        $ocrServices = $this->Serviceocr->find('all', array('conditions' => array('serviceocr_status' => SER_ACTIVE)));
    
        $idList = array();
        foreach ($ocrServices as $ocrService) {
            array_push($idList, $ocrService['Serviceocr']['company_id']);
        }

        $conditions = array('Company.id' => $idList);

        //Platform selection filters
        if ($filter['country_filter']) {
            $filtro = array('Company.company_countryName' => $filter['country_filter']);
            $conditions = array_merge($conditions, $filtro);
        }

        if ($filter['type_filter']) {
            $filtro = array('Company.Company_type' => $filter['type_filter']);
            $conditions = array_merge($conditions, $filtro);
        }
       
        $data = $this->find("all", array(
            'fields' => array('id', 'Company.company_name', 'Company.company_country', 'Company.company_logoGUID', 'Company.company_countryName', 'Company.Company_termsUrl',
                'Company.Company_privacyUrl', 'Company.Company_type'),
            'recursive' => -1,
            'conditions' => $conditions,
        ));
        
        return $data;
    }

    /*     * Check the service status for the company
     * 
     * @param type $id
     * @return type
     */

    public function checkOcrServiceStatus($id) {

        //Search the company only if ocr service is active or suspended
        $status = $this->Serviceocr->find("first", array(
            'conditions' => array('company_id' => $id, 'serviceocr_status' => array(SER_INACTIVE, SER_ACTIVE, SER_SUSPENDED)),
        ));
        //If found, return true and the status
        if (count($status) > 0) {
            return [true, $status];
        } else {
            return false;
        }
    }

    /**
     *
     * 	Returns the extended data an array of the company and their data that fullfil the filterConditions.
     * 	If more then one record fullfils the criterion, then the first one is returned
     * 	
     * 	@param 		array 	$filteringConditions	(basically the companyId)
     * 	@return 	array 	 Data of each company as an element of an array
     * 			
     */
    public function readExtendedData($filterConditions) {

        $companyResult = $this->find("all", $params = array('recursive' => -1,
            'conditions' => $filterConditions,
        ));

// 'Normalize' the total array
        foreach ($companyResult as $value) {
            $companyResults[$value['Company']['id']] = $value['Company'];
        }
        return $companyResults;
    }

    public function UpdateCompany($data, $status) {
        if ($this->validates($this->save($data))) {

            if ($this->Serviceocr->save($status)) {
                return [true, __("Company updated correctly")];
            } else {
                return [false, __("Failed saving status")];
            }
        } else {
            return [false, __("Failed update")];
        }
    }

}
