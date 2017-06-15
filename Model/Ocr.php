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
        'Comapany' => array(
            'className' => 'Comapany',
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



    /*
     *
     * Saves data in ocr table
     * 
     */

    //Create ocr info in db for first time
    public function createOcr($id) {

        $idFind = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        //Actualizo esa fila del ocr
        if (count($idFind) == 0) {
            $data = array(
                'investor_id' => $id,
                'ocr_investmentVehicle' => 0,
                'investor_cif' => null,
                'investor_businessName' => null,
                'investor_iban' => null,
                'ocr_status' => 0,
            );
            $this->save($data);
            return 1;
        }
        return 0;
    }

    //Save ocr information
    public function ocrDataSave($dataP) {

        $id = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $dataP['investor_id']),
            'recursive' => -1,));

        //Update the data in OCR
        if (count($id) > 0) {

            if ($dataP['ocr_investmentVehicle'] == 1) {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataP['investor_id'],
                    'ocr_investmentVehicle' => 1,
                    'investor_cif' => $dataP['investor_cif'],
                    'investor_businessName' => $dataP['investor_businessName'],
                    'investor_iban' => $dataP['investor_iban'],
                    'ocr_status' => 1,
                );
            } else {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $dataP['investor_id'],
                    'ocr_investmentVehicle' => 0,
                    'investor_cif' => null,
                    'investor_businessName' => null,
                    'investor_iban' => $dataP['investor_iban'],
                    'ocr_status' => 1,
                );
            }
        }
        $this->save($data);
        //$result[0] = 1;
        $result = json_encode($data);
        // $result = json_encode($result);
        $event = new CakeEvent("checkMessage", $this);
        $this->getEventManager()->dispatch($event);
        //Insert OK        
        return 1 . "," . $result . "]";
    }

    /*
     * 
     * Get data of ocr table
     * 
     */
    //Get all ocr data
    public function ocrGetData($id) {

        $info = $this->find("all", array(
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
        ));

        return $info;
    }
    //Get only the status
    public function checkStatus($id) {

        $info = $this->find("all", array(
            'fields' => 'ocr_status',
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
        ));

        return $info;
    }

    
    //Save the selected companies in company_ocr
    public function saveCompaniesOcr($data) {
        if (count($data) > 2) {


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
                    $query = "INSERT INTO `search`.`companies_ocrs` (`company_id`, `ocr_id`, `statusOcr`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
                } else {
                    $query = $query . "INSERT INTO `search`.`companies_ocrs` (`company_id`, `ocr_id`, `statusOcr`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
                }
            }
            $query = $this->query($query);
            $this->set('data', $data);
            return $result[0] = 1;
        } else {
            return $result[0] = 0;
        }
    }

    //Update the sent companies status
    public function updateCompaniesStatus($id) {

        $query = "UPDATE `search`.`companies_ocrs` SET `statusOcr`='1' WHERE `ocr_id`='" . $id . "' and `statusOcr`='0';";
        $query = $this->query($query);
    }

    //Delete a selected company from ocr
    public function deleteCompanyOcr($data) {
        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $data['investorId']),
            'recursive' => -1,));

        $query = "DELETE FROM `search`.`companies_ocrs` WHERE `company_id`='" . $data['companyId'] . "' and`ocr_id`='" . $ocrId['Ocr']['id'] . "';";
        $query = $this->query($query);
        $this->set('data', $data);
        return $result[0] = 1;
    }

    //Get all selected companies(no sent)
    public function getSelectedCompanies($id) {

        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        $query = "Select * from `search`.`companies_ocrs` where `ocr_id`=" . $ocrId['Ocr']['id'] . " and `statusOcr` = 0;";
        $companyList = $this->query($query);
        return $companyList;
    }

    //Get sent compnies
    public function getRegisterSentCompanies($id) {

        $ocrId = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $id),
            'recursive' => -1,));

        $query = "Select `company_id` from `search`.`companies_ocrs` where `ocr_id`=" . $ocrId['Ocr']['id'] . " and `statusOcr` = 1;";
        return $this->query($query);
    }

}
