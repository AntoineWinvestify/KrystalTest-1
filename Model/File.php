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

 * 2017/6/06 version 0.1
 * function upload                           [OK]
 *
 * 2017/6/08 version 0.2                 
 * function delete                           [OK]
 * 
 * 
 * 2017/6/14 version 0.3
 * url and name fixed                        [OK]
 * 
 * 2017/6/19 version 0.4
 * function delete getAllBills            [OK]
 * select rquired files deleted, now we use find
 * 
 * 2017/6/21 version 0.5
 * upload bill                            [OK]
 * 
 * 2017/6/23 version 0.5
 * change query for find         
 * 
 * 2017/6/26 version 0.5
 * billCompanyFilter                         [OK]
 * 
 * 2017/6/28 version 0.5
 * zip creation                         [OK]
 * 
 */
App::uses('CakeEvent', 'Event', 'File', 'Utility');
Configure::load('p2pGestor.php', 'default');

class file extends AppModel {

    var $name = 'File';
    public $hasAndBelongsToMany = array(
        'Company' => array(
            'className' => 'Company',
            'joinTable' => 'companies_files',
            'foreignKey' => 'file_id',
            'associationForeignKey' => 'company_id',
        ),
        'Investor' => array(
            'className' => 'Investor',
            'joinTable' => 'files_investors',
            'foreignKey' => 'file_id',
            'associationForeignKey' => 'investor_id',
        ),
        'requiredFiles' => array(
            'className' => 'Company',
            'joinTable' => 'requiredfiles',
            'foreignKey' => 'file_id',
            'associationForeignKey' => 'company_id',
        ),
    );

    /**
     * Upload investor file
     * @param type $data
     * @param type $identity
     * @param type $id
     * @param type $type
     * @return string|int
     */
    public function ocrFileSave($fileInfo, $folder, $id, $type, $path) {
        //Load files config
        $fileConfig = Configure::read('files');
        if ($path == "file") {
            $up = $fileConfig['investorPath'] . $folder;
        } else if ($path == "bill") {
            $up = $fileConfig['billsPath'] . $folder;
        }

        foreach ($fileInfo as $file) {

            //Error filter
            if ($file['size'] == 0 || $file['error'] !== 0) {
                continue;
            }
            //Type and size filter
            if (in_array($file['type'], $fileConfig['permittedFiles']) && $file['size'] < $fileConfig['maxSize']) {
                $name = basename($file['name']);
                $filename = time() . "_" . $name;
                $uploadFolder = $up;
                $uploadPath = $uploadFolder . DS . $filename;
                //Create the dir if not exist
                if (!file_exists($uploadFolder)) {
                    mkdir($uploadFolder, 0755, true);
                }

                //Move the uploaded file to the new dir
                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    return [false, __("Upload failed. Incorrect type or file too big.")];
                }

                //Save in db
                if ($path == "file") {

                    $query = "INSERT INTO `files_investors` (`investor_id`, `file_id`, `file_name`, `file_url`) VALUES (" . $id . ", " . $type . ", '" . $name . "', '" . $folder . DS . $filename . "');";
                    $query = $this->query($query);
                    $result = array(basename($file['name']), $folder . DS . $filename, $type);
                    return [true, __('Upload ok'), $result];
                    
                } else if ($path == "bill") {
                    $result = array(basename($file['name']), $folder . DS . $filename, $type);

                    $bill = array(
                        'CompaniesFile' => Array(
                            'company_id' => $id,
                            'file_id' => 50,
                            'bill_number' => $type['number'],
                            'bill_amount' => $type['amount'] * 100,
                            'bill_concept' => $type['concept'],
                            'bill_currency' => $type['currency'],
                            'bill_url' => $folder . DS . $filename
                        )
                    );

                    if ($this->CompaniesFile->save($bill)) {
                        return [true, __('Upload ok')];
                    } else {
                        return [false, __("Upload failed. Incorrect type or file too big.")];
                    }
                }
            } else {
                return [false, __("Upload failed. Incorrect type or file too big.")];
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
        $url = $fileConfig['investorPath'] . $url;

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
        //Id list of selected companies
        $selectedList = array();
        foreach ($data as $selectedId) {
            array_push($selectedList, $selectedId['ocrInfo']['company_id']);
        }
        //All company files
        $allCompanyFiles = $this->find('all', array(
            'conditions' => array(
                'id' => array(1, 2, 3)), //50 is the bill id
            'recursive' => 1,));

        //Filter required files
        $requiredFileIdList = array();

        foreach ($allCompanyFiles as $allFiles) {
            foreach ($allFiles["requiredFiles"] as $requiredFiles) {
                //Filter selected companies required files
                if (in_array($requiredFiles["id"], $selectedList)) {
                    $info = array("id" => $requiredFiles["Requiredfile"]["file_id"], "tooltip" => $allFiles["File"]["file_tooltip"]);
                    array_push($requiredFileIdList, $info);
                }
            }
        }
        //Delete duplicates
        $requiredFileResult = array_unique($requiredFileIdList, SORT_REGULAR);
        return $requiredFileResult;
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
     * Read the existing file for a user
     * @param type $id
     * @return type
     */
    public function readExistingFiles($id) {
        $investorFiles = $this->FilesInvestor->find("all", array('conditions' => array('investor_id' => $id)));
        $filesName = $this->find("all");
        $result = array();

        //Get existing file and type file info
        foreach ($investorFiles as $investorFile) {
            foreach ($filesName as $fileName) {
                if ($investorFile['FilesInvestor']['file_id'] == $fileName['File']['id']) {
                    array_push($result, array("file" => $investorFile, "type" => $fileName['File']));
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function readSimpleDocument($id) {
        $file = $this->FilesInvestor->find("first", array('conditions' => array('id' => $id)));
        return $file;
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function readSimpleBill($id) {
        $file = $this->CompaniesFile->find("first", array('conditions' => array('id' => $id)));
        return $file;
    }

    /**
     * 
     * Read the existing bills for WinAdmin
     * Filter for pfpAdmin
     * @return type
     */
    public function getAllBills() {
        $allBills = $this->find('all', array(
            'conditions' => array('id' => 50), //50 is the bill id
            'recursive' => 1,));
        $allBillInfo = array();

        //Info filter, we need only the company name and the bill info.
        foreach ($allBills as $allInfo) {
            foreach ($allInfo["Company"] as $info) {
                $companyName = $info["company_name"];
                $billInfo = $info["CompaniesFile"];
                $tempArray = array('Pfpname' => $companyName, 'info' => $billInfo);
                array_push($allBillInfo, $tempArray);
            }
        }
        return $allBillInfo;
    }

    public function billCompanyFilter($id) {
        $bills = $this->CompaniesFile->find('all', array('conditions' => array('company_id' => $id)));
        return $bills;
    }

    function createZip($files = array(), $destination = '', $overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) {
            return false;
        }


        //vars
        $validFiles = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            foreach ($files as $file) {
                //make sure the file exists
                if (file_exists($file)) {
                    $validFiles[] = $file;
                }
            }
        }

        //if we have good files...
        if (count($validFiles)) {
            //create the archive
            $zip = new ZipArchive();

            if (!file_exists($destination)) {
                if ($zip->open($destination, false ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
            } else {
                if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
            }

            //add the files
            foreach ($validFiles as $file) {
                $zip->addFromString(basename($file), file_get_contents($file));
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            //close the zip -- done!
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

}
