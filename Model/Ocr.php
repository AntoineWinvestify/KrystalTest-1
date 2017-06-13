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

    public function ocrDataSave($datos) {

        $id = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'investor_id' => $datos['investor_id']),
            'recursive' => -1,));

        //Si ya existe, actualizo esa fila del ocr
        if (count($id) > 0) {

            if ($datos['ocr_investmentVehicle'] == 1) {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $datos['investor_id'],
                    'ocr_investmentVehicle' => 1,
                    'investor_cif' => $datos['investor_cif'],
                    'investor_businessName' => $datos['investor_businessName'],
                    'investor_iban' => $datos['investor_iban'],
                );
            } else {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'investor_id' => $datos['investor_id'],
                    'ocr_investmentVehicle' => 0,
                    'investor_cif' => null,
                    'investor_businessName' => null,
                    'investor_iban' => $datos['investor_iban'],
                );
            }
            //Si no existe, creo una nueva fila ocr    
        } else {

            if ($datos['ocr_investmentVehicle'] == 1) {
                $data = array(
                    'investor_id' => $datos['investor_id'],
                    'ocr_investmentVehicle' => 1,
                    'investor_cif' => $datos['investor_cif'],
                    'investor_businessName' => $datos['investor_businessName'],
                    'investor_iban' => $datos['investor_iban'],
                );
            } else {
                $data = array(
                    'investor_id' => $datos['investor_id'],
                    'ocr_investmentVehicle' => 0,
                    'investor_cif' => null,
                    'investor_businessName' => null,
                    'investor_iban' => $datos['investor_iban'],
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

    public function ocrGetData($id) {

        $info = $this->find("all", array(
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
        ));

        return $info;
    }

    public function checkStatus($id) {

        $info = $this->find("all", array(
            'fields' => 'ocr_status',
            'conditions' => array('investor_id' => $id),
            'recursive' => -1,
        ));

        return $info;
    }

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

    public function updateCompaniesStatus($id) {

        $query = "UPDATE `search`.`companies_ocrs` SET `statusOcr`='1' WHERE `ocr_id`='" . $id . "' and `statusOcr`='0';";
        $query = $this->query($query);
    }

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
