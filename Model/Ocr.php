<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 *
 *
 * 2017/5/29 version 0.1
 * function ocrDataSave   Save ocr data in db                         [OK]
 * function ocrGetData    Get info of ocr                             [OK]
 * 
 * 2017/5/30 version 0.2
 * ocrFileSave         Upload files                                 [OK]
 * 
 * 2017/6/01 version 0.3
 * saveCompaniesOcr                                                 [OK]
 * getSelectedCompanies                                              [OK]
 * 
 * 2017/6/05  version 0.4
 * deleteCompanyOcr                                                     [OK]
 *             
 * 2017/6/06  version 0.5
 * upload deleted
 * id problem fixed
 *
 * 2017/6/13  version 0.6
 * checkStatus
 * 
 * 2017/6/14 version 0.7
 * Confirm modal
 * 
 * 2017/6/19 version 0.8
 * Select query deleted
 * 
 * 2017/6/23 version 0.8
 * ocr_sent(date)
 * checking table completed
 * 
 */
App::uses('CakeEvent', 'Event');

class ocr extends AppModel {

    var $name = 'Ocr';
    public $hasAndBelongsToMany = array(
        'Company' => array(
            'className' => 'Company',
            'joinTable' => 'companies_ocrs',
            'foreignKey' => 'ocr_id',
            'associationForeignKey' => 'company_id',
        ),
    );
    public $hasOne = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' => 'ocr_id',
            'associationForeignKey' => 'investor_id',
        ),
    );

    /* var $validate = array(
      'investor_cif' => array(
      'rule1' => array('rule' => array('minLength', 1),
      'allowEmpty' => false,
      'message' => 'Name validation error'),
      ),
      'investor_businessName' => array(
      'rule1' => array('rule' => array('minLength', 1),
      'allowEmpty' => false,
      'message' => 'Name validation error'),
      )
      ); */

    /**
     * 
     * Saves data in ocr table for first time 
     * @param type $id
     * @return int
     */
    public function createOcr($id) {

        //Ocr id find
        $idFind = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        //No ocr id = new ocr
        if (count($idFind) == 0) {
            $data = array(
                'investor_id' => $id,
                'ocr_investmentVehicle' => 0,
                'investor_cif' => null,
                'investor_businessName' => null,
                'investor_iban' => null,
                'ocr_status' => 0,
            );

            //Update
            if ($this->save($data)) {
                $this->Investor->save(array('id' => $id, 'ocr_id' => 6));
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    /**
     * Save or update ocr information
     * @param type $dataParam
     * @return boolean
     */
    public function ocrDataSave($dataParam) {

        // Find Ocr id
        $id = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $dataParam['investor_id']),
            'recursive' => -1,));


        //Ocr data
        if (count($id) > 0) {
            $time = date('Y-m-d H:i:s', time());

            if ($dataParam['ocr_investmentVehicle'] == 1) {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataParam['investor_id'],
                    'ocr_investmentVehicle' => 1,
                    'investor_cif' => $dataParam['investor_cif'],
                    'investor_businessName' => $dataParam['investor_businessName'],
                    'investor_iban' => $dataParam['investor_iban'],
                    'ocr_status' => 1,
                    'ocr_sent' => $time,
                );
            } else {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataParam['investor_id'],
                    'ocr_investmentVehicle' => 0,
                    'investor_iban' => $dataParam['investor_iban'],
                    'ocr_status' => 1,
                    'ocr_sent' => $time,
                );
            }
        }
//Save
        if ($this->save($data)) {
            $result = json_encode($data); //Save ok
            $event = new CakeEvent("checkMessage", $this);
            $this->getEventManager()->dispatch($event);
//Insert OK        
            return 1 . "," . $result;  //Return for a json
        } else {

            /*
             * 
             * SAVE ERROR
             */
            return 0 . ","; //Save failed
        }
    }

    /**
     * Get and return ocr data
     * @param type $id
     * @return type
     */
    public function ocrGetData($id, $filter = null) {
        if ($id != null && $filter != null) {
            array_push($filter, array('investor_id' => $id));
        } else if ($filter == null && $id != null) {
            $filter = array('investor_id' => $id);
        }

        // Find ocr data
        $info = $this->find("all", array(
            'conditions' => array($filter),
            'recursive' => 1,
        ));
        if ($info) {
            return $info; //Return info
        } else {
            return 0; //No info
        }
    }

    /**
     * Get only the ocr status
     * @param type $id
     * @return type
     */
    public function checkStatus($id) {
        $info = $this->find("all", array(
            'fields' => 'ocr_status',
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
        ));
        return $info; //Return info
    }

    /**
     * Save the selected companies in company_ocr
     * @param type $data
     * @return boolean
     */
    public function saveCompaniesOcr($data) {

        if (count($data) > 2) {   // Data is array, $data > 2 are the id of selected companies $data<2 are number if companies and
            $ocrId = $this->find('first', array(
                'fields' => array(
                    'id',
                ),
                'conditions' => array(
                    'investor_id' => $data['investorId']),
                'recursive' => -1,));


            $comp = $data["idCompanies"];

            for ($i = 0; $i < count($comp); $i++) {

                if ($i == 0) {
                    $query = "INSERT INTO `companies_ocrs` (`company_id`, `ocr_id`, `company_status`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
                } else {
                    $query = $query . "INSERT INTO `companies_ocrs` (`company_id`, `ocr_id`, `company_status`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
                }
            }
            $query = $this->query($query);
            $this->set('data', $data);
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Update the sent companies status
     * @param type $id
     */
    public function updateCompaniesStatus($id) {
        $query = "UPDATE `companies_ocrs` SET `company_status`='1' WHERE `ocr_id`='" . $id . "' and `company_status`='0';";
        $query = $this->query($query);
        return "," . 1 . "]";
    }

    /**
     * Delete a selected company from ocr
     * @param type $data
     * @return type
     */
    public function deleteCompanyOcr($data) {
        //Find ocrId
        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $data['investorId']),
            'recursive' => -1,));
        /* Delete company */
        $query = "DELETE FROM `companies_ocrs` WHERE `company_id`='" . $data['companyId'] . "' and`ocr_id`='" . $ocrId['Ocr']['id'] . "';";
        $this->query($query);
        return 1;
    }

    /**
     * Get all companies info related to a investor
     * 
     * @param type $id
     * @return array
     */
    public function getAllCompanies($id) {
        $companiesArray = $this->find('all', array('recursive' => 1, 'conditions' => array('investor_id' => $id)));
        $companies_ocrs = array();
        foreach ($companiesArray as $company) {
            foreach ($company["Company"] as $companyOcr) {
                array_push($companies_ocrs, array('ocrInfo' => $companyOcr["CompaniesOcr"], 'name' => $companyOcr['company_name']));
            }
        }
        return $companies_ocrs;
    }

    /**
     * Get selected companies
     * 
     * @param type $id
     * @return type
     */
    public function getSelectedCompanies($id) {

        // Read all the companies_ocrof the user
        $companyListNotFilter = $this->getAllCompanies($id);
        $companyList = array();
        //status filter
        foreach ($companyListNotFilter as $filterStatus) {
            if ($filterStatus["company_status"] == 0) {
                array_push($companyList, $filterStatus);
            }
        }
        return $companyList;
    }

    /**
     * //Get sent companies
     * @param type $id
     * @return type
     */
    public function getRegisterSentCompanies($id) {

        // Read all the companies_ocrof the user
        $companyListNotFilter = $this->getAllCompanies($id);
        $companyList = array();

        //status filter
        foreach ($companyListNotFilter as $filterStatus) {
            if ($filterStatus["company_status"] == 1) {
                array_push($companyList, $filterStatus);
            }
        }
        return $companyList;
    }

    /**
     *
     * 	Callback Function
     * 	Decrypt the sensitive data provided by the investor
     *
     */
    /* public function afterFind($results, $primary = false) {

      foreach ($results as $key => $val) {
      if (isset($val['Ocr']['investor_iban'])) {
      $results[$key]['Ocr']['investor_iban'] = $this->decryptDataAfterFind(
      $val['Ocr']['investor_iban']);
      }
      }
      return $results;
      }

      /**
     *
     * 	Callback Function
     * 	Encrypt the sensitive fields of the information provided by the investor
     *
     */
    /* public function beforeSave($options = array()) {

      if (!empty($this->data['Ocr']['investor_iban'])) {
      $this->data['Ocr']['investor_iban'] = $this->encryptDataBeforeSave($this->data['Ocr']['investor_iban']);
      }

      return true;
      } */
}
