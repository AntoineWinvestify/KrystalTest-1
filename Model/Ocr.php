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

  2017/5/29 version 0.1
  function ocrDataSave   Save ocr data in db                         [OK]
  function ocrGetData    Get info of ocr                             [OK]
 * 
  2017/5/30 version 0.2
  ocrFileSave         Upload files                                 [OK]
 * 
  2017/6/01 version 0.3
  saveCompaniesOcr                                                 [OK]
  getSelectedCompanies                                              [OK]
 * 
  2017/6/05  version 0.4
  deleteCompanyOcr                                                     [OK]
 *             
  2017/6/06  version 0.5
  upload deleted
  id problem fixed
 *
  2017/6/13  version 0.6
  checkStatus
 * 
  2017/6/14 version 0.7
  Confirm modal
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

            if ($dataParam['ocr_investmentVehicle'] == 1) {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataParam['investor_id'],
                    'ocr_investmentVehicle' => 1,
                    'investor_cif' => $dataParam['investor_cif'],
                    'investor_businessName' => $dataParam['investor_businessName'],
                    'investor_iban' => $dataParam['investor_iban'],
                    'ocr_status' => 1,
                );
            } else {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataParam['investor_id'],
                    'ocr_investmentVehicle' => 0,
                    'investor_iban' => $dataParam['investor_iban'],
                    'ocr_status' => 1,
                );
            }
        }

//Save
        if ($this->save($data)) {
            $result = json_encode($data); //Save ok
            $event = new CakeEvent("checkMessage", $this);
            $this->getEventManager()->dispatch($event);
//Insert OK        
            return 1 . "," . $result . "]";  //Return for a json
        } else {
            return 0; //Save failed
        }
    }

    /**
     * Get and return ocr data
     * @param type $id
     * @return type
     */
    public function ocrGetData($id) {

// Find ocr data
        $info = $this->find("all", array(
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
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
        print_r($id);

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
                    $query = "INSERT INTO `companies_ocrs` (`company_id`, `ocr_id`, `statusOcr`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
                } else {
                    $query = $query . "INSERT INTO `companies_ocrs` (`company_id`, `ocr_id`, `statusOcr`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
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
        $query = "UPDATE `companies_ocrs` SET `statusOcr`='1' WHERE `ocr_id`='" . $id . "' and `statusOcr`='0';";
        $query = $this->query($query);
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
     * Get selected companies
     * 
     * @param type $id
     * @return type
     */
    public function getSelectedCompanies($id) {
        //Ocr id
        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        // Select companies
        $query = "Select * from `companies_ocrs` where `ocr_id`=" . $ocrId['Ocr']['id'] . " and `statusOcr` = 0;";
        $companyList = $this->query($query);
        return $companyList;
    }

    /**
     * //Get sent companies
     * @param type $id
     * @return type
     */
    public function getRegisterSentCompanies($id) {
        //Ocr id
        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        //Sent companies
        $query = "Select `company_id` from `companies_ocrs` where `ocr_id`=" . $ocrId['Ocr']['id'] . " and `statusOcr` = 1;";
        return $this->query($query);
    }

}
