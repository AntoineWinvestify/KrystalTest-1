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
  function upload                      [OK]

  2017/6/08 version 0.2
  function delete                      [OK]
 */
App::uses('CakeEvent', 'Event', 'File', 'Utility');

class file extends AppModel {

    var $name = 'File';

    //upload file
    public function ocrFileSave($data, $identity, $id, $type) {
        foreach ($data as $data) {
            if ($data['size'] == 0 || $data['error'] !== 0) {
                continue;
            }
            if (($data['type'] == "image/png" || $data['type'] == "image/gif" ||  $data['type'] == "application/pdf") && $data['size'] < 10000000 ) {
                $filename = basename($data['name']);
                $uploadFolder = 'files/investors/' . $identity . '';
                $uploadPath = $uploadFolder . DS . $filename;

                if (!file_exists($uploadFolder)) {
                    mkdir($uploadFolder, 0755, true);
                }

                if (!move_uploaded_file($data['tmp_name'], $uploadPath)) {
                    return 0;
                }

                $query = "INSERT INTO `search`.`files_investors` (`investor_id`, `file_id`, `file_name`, `file_url`) VALUES (" . $id . ", " . $type . ", '" . $filename . "', '" . $uploadFolder . "');";
                $query = $this->query($query);
                $result = array($filename, $uploadFolder, $type);
                return $result;
            } else {
                return 0;
            }
        }
    }

    //delete file
    public function ocrFileDelete($url, $name, $file_id, $investor_id) {
        $url = WWW_ROOT . $url;

        if (unlink($url . DS . $name)) {
            $query = "DELETE FROM `search`.`files_investors` WHERE `file_id`=" . $file_id . " and `investor_id`=" . $investor_id . ";";
            print_r($query);
            $this->query($query);
            echo "borrado correctamente";
            return 1;
        }
        return 0;
    }

    //return requiered files of selected companies
    public function readRequiredFiles($data) {
        for ($i = 0; $i < count($data); $i++) {
            if ($i == 0) {
                $query = "Select * from `search`.`requieredfiles` where company_id =" . $data[$i]['companies_ocrs']['company_id'];
            } else {
                $query = $query . " OR company_id =" . $data[$i]['companies_ocrs']['company_id'];
            }
        }
        $result = $this->query($query);
        foreach ($result as $result) {
            $files[] = $result['requieredfiles']['file_id'];
        }
        $files = array_unique($files);
        return $files;
    }

    public function getFilesData($data) {
        foreach ($data as $data) {
            $files[] = $this->find('all', array(
                'conditions' => array(
                    'id' => $data),
                'recursive' => -1,));
        }

        return $files;
    }

    public function readExistingFiles($id) {
        $query = "Select * from `search`.`files_investors` where investor_id =" . $id;
        $result = $this->query($query);
        return $result;
    }

}
