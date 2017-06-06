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

  2017/6/06 version 0.1
  function upload                         [OK]

 */
App::uses('CakeEvent', 'Event');

class file extends AppModel {

    var $name = 'File';

    public function ocrFileSave($data, $id) {
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
            echo 'Directorio ' . $uploadFolder . '</br>';
            $filename = $filename;
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
            echo 'Insertando en base de datos</br>';
            
             $query = "INSERT INTO `search`.`files_investor` (`investor_id`, `file_id`, `file_name`, `file_url`) VALUES ('" . $comp[$i] . "', '" . $ocrId['Ocr']['id'] . "', '0');";
        }
    }
    
    
    

}
