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
 * 2017/6/23 version 0.6
 * change query for find         
 * 
 * 2017/6/26 version 0.7
 * billCompanyFilter                         [OK]
 * 
 * 2017/6/28 version 0.8
 * zip creation                         [OK]
 * 
 * 2017/6/30 version 0.9
 * Event 
 * 
 * 2017/07/03
 * Generate and include json in zip file
 * 
 */
App::uses('CakeEvent', 'Event', 'File', 'Utility');
Configure::load('p2pGestor.php', 'default');

class ocrfile extends AppModel {

    var $useTable = 'files';
    var $name = 'Ocrfile';
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
    var $validate = array(
        'bill_number' => array(
            'rule1' => array('rule' => array('minLength', 1),
                'allowEmpty' => false,
                'message' => 'Number validation error'),
        ),
        'bill_amount' => array(
            'rule1' => array('rule' => array('number'),
                'allowEmpty' => false,
                'message' => 'Amount validation error'),
        ),
        'bill_concept' => array(
            'rule1' => array('rule' => array('minLength', 1),
                'allowEmpty' => false,
                'message' => 'Amount validation error'),
        ),
        'bill_currency' => array(
            'rule1' => array('rule' => array('notBlank'),
                'allowEmpty' => false,
                'message' => 'Amount validation error'),
        )
    );

    /**
     * Generate a json with the $data passed in the $path
     * @param type $data
     * @param type $path
     */
    public function generateJson($data, $path) {
        $fp = fopen($path . DS . 'results.json', 'w');
        if (fwrite($fp, json_encode($data))) {
            fclose($fp);
            return true;
        } else {
            return false;
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
                'id' => array(1, 2, 3, 4)), //1,2,3,4 are the documents
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

                if ($investorFile['FilesInvestor']['file_id'] == $fileName['Ocrfile']['id']) {

                    array_push($result, array("file" => $investorFile, "type" => $fileName['Ocrfile']));
                }
            }
        }
        return $result;
    }

    /**
     * Get the info of one document
     * @param type $id
     * @return type
     */
    public function readSimpleDocument($id) {
        $file = $this->FilesInvestor->find("first", array('conditions' => array('id' => $id)));
        return $file;
    }

    /**
     * Get the info of one bill
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

    /**
     * Get all the bills of a company
     * @param type $id
     * @return type
     */
    public function billCompanyFilter($id) {
        $bills = $this->CompaniesFile->find('all', array('conditions' => array('company_id' => $id)));
        return $bills;
    }

    /**
     * Create a zip of an investor documents and a json
     * 
     * @param type $files
     * @param type $destination
     * @param type $overwrite
     * @return boolean
     */
    function createZip($files = array(), $destination = '', $overwrite = false, $json = null) {
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
            $zip->addFromString(basename($json), file_get_contents($json));
            // $zip->addFromString('result.json', file_get_contents('result.json'));
            // 
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

    /**
     *
     * 	Callback Function Create mail event
     * 
     * @param type $created
     * @param type $options
     * 
     */
    function afterSave($created, $options = array()) {
        
    }

}
