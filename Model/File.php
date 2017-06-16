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
 *
  2017/6/08 version 0.2
  function delete
 *                  [OK]
  2017/6/14 version 0.3
  url and name fixed                      [OK]
 *  
 */
App::uses('CakeEvent', 'Event', 'File', 'Utility');
Configure::load('p2pGestor.php', 'default');

class file extends AppModel {

    var $name = 'File';

    /**
     * Upload investor file
     * @param type $data
     * @param type $identity
     * @param type $id
     * @param type $type
     * @return string|int
     */
    public function ocrFileSave($data, $identity, $id, $type) {
        //Load files config
        $fileConfig = Configure::read('files');

        foreach ($data as $file) {

            //Error filter
            if ($file['size'] == 0 || $file['error'] !== 0) {
                continue;
            }

            //Type and size filter
            if (in_array($file['type'], $fileConfig['permittedFiles']) && $file['size'] < $fileConfig['maxSize']) {
                $name = basename($file['name']);
                $filename = time() . "_" . $name;
                $uploadFolder = $fileConfig['investorPath'] . $identity . '';
                $uploadPath = $uploadFolder . DS . $filename;

                //Create the dir if not exist
                if (!file_exists($uploadFolder)) {
                    mkdir($uploadFolder, 0755, true);
                }

                //Move the uploaded file to the new dir
                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    return 0;
                }

                //Save in db
                $query = "INSERT INTO `files_investors` (`investor_id`, `file_id`, `file_name`, `file_url`) VALUES (" . $id . ", " . $type . ", '" . $name . "', '" . $identity . DS . $filename . "');";
                $query = $this->query($query);
                $result = array(basename($file['name']), $identity . DS . $filename, $type);
                return $result;
            } else {
                return 0;
            }
        }
    }

    /**
     * Delete investor file
     * @param string $url
     * @param type $file_id
     * @param type $investor_id
     * @return int
     */
    public function ocrFileDelete($url, $file_id, $investor_id) {
        $fileConfig = Configure::read('files');
        $url = WWW_ROOT . $fileConfig['investorPath'] . $url;

        if (unlink($url)) {
            $query = "DELETE FROM `files_investors` WHERE `file_id`=" . $file_id . " and `investor_id`=" . $investor_id . ";";
            $this->query($query);
            return 1;
        }
        return 0;
    }

    /**
     * return required files of selected companies
     * @param type $data
     * @return type
     */
    public function readRequiredFiles($data) {
        for ($i = 0; $i < count($data); $i++) {
            if ($i == 0) {
                $query = "Select * from `requieredfiles` where company_id =" . $data[$i]['companies_ocrs']['company_id'];
            } else {
                $query = $query . " OR company_id =" . $data[$i]['companies_ocrs']['company_id'];
            }
        }
        $result = $this->query($query);
        foreach ($result as $value) {
            $files[] = $value['requieredfiles']['file_id'];
        }
        $files = array_unique($files);
        return $files;
    }

    /**
     * Get files type data
     * @param type $data
     * @return type
     */
    public function getFilesData($data) {
        foreach ($data as $value) {
            $files[] = $this->find('all', array(
                'conditions' => array(
                    'id' => $value),
                'recursive' => -1,));
        }
        return $files;
    }

    /**
     * Read existing file for a user
     * @param type $id
     * @return type
     */
    public function readExistingFiles($id) {
        $query = "Select * from `files_investors` where investor_id =" . $id;
        $result = $this->query($query);
        return $result;
    }

}
