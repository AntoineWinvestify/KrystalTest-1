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

  2016/29/2017 version 0.1
  function ocrDataSave   Save ocr data in db                         [OK]
  function ocrGetData    Get info of ocr                             [OK]


 */
App::uses('CakeEvent', 'Event');

class ocr extends AppModel {

    var $name = 'Ocr';
    var $validate = array();
    public $hasMany = array(
        'Comapany' => array(
            'className' => 'Comapany',
            'foreignKey' => 'ocr_id',
            'fields' => '',
            'order' => '',
        ),
    );

    /*
     *
     * Saves data in ocr table
     * 
     */

    public function ocrDataSave($datos) {

        echo '1';
        print_r('id = ' . $datos['Investor_id']);
        $id = $this->find('first', array(
            'fields' => array(
                'id',
            ),
            'conditions' => array(
                'Investor_id' => $datos['Investor_id']),
            'recursive' => -1,));
        print_r($id);
        //Si ya existe, actualizo esa fila del ocr
        if (count($id) > 0) {

            echo 'existe';
            if ($datos['Ocr_vehicle'] == 1) {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'Investor_id' => $datos['Investor_id'],
                    'Ocr_vehicle' => 1,
                    'Investor_cif' => $datos['Investor_cif'],
                    'Investor_businessName' => $datos['Investor_businessName'],
                    'Investor_iban' => $datos['Investor_iban'],
                );
            } else {
                $data = array(
                    'id' => $id['Ocr']['id'],
                    'Investor_id' => $datos['Investor_id'],
                    'Ocr_vehicle' => 0,
                    'Investor_cif' => null,
                    'Investor_businessName' => null,
                    'Investor_iban' => $datos['Investor_iban'],
                );
            }
            //Si no existe, creo una nueva fila ocr    
        } else {
            echo 'no existe';
            if ($datos['Ocr_vehicle'] == 1) {
                $data = array(
                    'Investor_id' => $datos['Investor_id'],
                    'Ocr_vehicle' => 1,
                    'Investor_cif' => $datos['Investor_cif'],
                    'Investor_businessName' => $datos['Investor_businessName'],
                    'Investor_iban' => $datos['Investor_iban'],
                );
            } else {
                $data = array(
                    'Investor_id' => $datos['Investor_id'],
                    'Ocr_vehicle' => 0,
                    'Investor_cif' => null,
                    'Investor_businessName' => null,
                    'Investor_iban' => $datos['Investor_iban'],
                );
            }
        }



        print_r($data);


        $this->save($data);
        $result[0] = 1;
        //Insert OK
        return $result;
    }

    /*
     * 
     * Get data of ocr table
     * 
     */

    public function ocrGetData($id) {

        $info = $this->find("all", array(
            'conditions' => array('Investor_id' => $id),
            'recursive' => -1,
        ));

        return $info;
    }

    public function ocrFileSave($data, $id) {
        echo 'patata' . $id;
        print_r($data);

        foreach ($data as $data) {
            echo 'procesando archivo</br>';
            if ($data['size'] == 0 || $data['error'] !== 0) {
                echo 'Error al subir archivo';
                continue;
            }
            $filename = basename($data['name']);
            echo 'Nombre base ' . $filename . '</br>';
            $uploadFolder = 'files/investors/' . $id . '';
            echo 'Dirctorio ' . $uploadFolder . '</br>';
            $filename = time() . '_' . $filename;
            echo 'nombre completo ' . $filename . '</br>';
            $uploadPath = $uploadFolder . DS . $filename;
            echo 'ruta ' . $uploadPath . '</br>';

            if (!file_exists($uploadFolder)) {
                echo 'carpeta no existe, creandola </br>';
                mkdir($uploadFolder, 0755, true);
            }

            if (!move_uploaded_file($data['tmp_name'], $uploadPath)) {
                echo 'fallo al mover';
                continue;
            }
            echo 'terminado de guardar directorio</br>';
            /* echo 'Insertando en base de datos</br>'
              $dataDb = {



              } */
        }
    }

}
