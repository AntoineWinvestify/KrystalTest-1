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

    //upload file
    public function ocrFileSave($data, $identity, $id, $type) {
        foreach ($data as $data) {
            if ($data['size'] == 0 || $data['error'] !== 0) {
                continue;
            }
            $filename = basename($data['name']);
            $uploadFolder = 'files/investors/' . $identity . '';
            $uploadPath = $uploadFolder . DS . $filename;

            if (!file_exists($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            if (!move_uploaded_file($data['tmp_name'], $uploadPath)) {
                continue;
            }
            print_r($data);
            $query = "INSERT INTO `search`.`files_investors` (`investor_id`, `file_id`, `file_name`, `file_url`) VALUES (" . $id . ", " . $type . ", '" . $filename . "', '" . $uploadPath . "');";
            echo "$query";
            $query = $this->query($query);
        }
    }

    //delete file
    public function ocrFileDelete($data, $identity) {
        print_r($data);
        $filename = basename($data['name']);
        $uploadFolder = 'files/investors/' . $identity . '';
        $uploadPath = $uploadFolder . DS . $filename;
        $file = new File($uploadFolder . DS . $itemId . DS . $filename);

        return $file->delete();
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

}
