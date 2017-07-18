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
 *
 *  2016/29/2017 version 0.1
 *  function OneClickInvestorI, Save personal data in db                    [OK]
 *  function OneClickInvestorII Save selected companies                     [OK]
 *  function companyFilter      Company filter for platform selection panel [OK]
 *  function OneClickAdmin                                     [Not implemented]
 *  function OneClickCompany                                   [Not implemented]
 *
 * 2017/6/06 version 0.1
 * function upload                         [OK]
 * 
 * 2017/6/08 version 0.2
 * function delete                [ok]
 * 
 * 2017/6/14 version 0.3
 * url and name fixed                      [OK]
 * 
 * 2017/6/21 version 0.4
 * upload bill         [OK]
 * 
 * 2017/6/28 version 0.5
 * zip download         [OK]
 * 
 * 2017/6/30 version 0.6
 * zip download  
 * 
 * 2017/07/03 version 0.7
 * Json path in the zip
 * 
 * 2017/07/11 version 0.8
 * Delete all investor files
 * 
 * 2017/07/13 version 0.9
 * File binary validation
 * 
 * 2017/07/13 version 0.10
 * Zip now only contains the required files of the pfp
 * 
 */
App::uses('CakeEvent', 'Event');

class filesController extends AppController {

    var $name = 'Files';
    var $helpers = array('Session');
    var $uses = array('Investor', 'Ocrfile');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /**
     * Generate and download the zip, Adminpfp uses it.
     * @param type $id
     * @param type $userId
     * @return type
     */
    function generateZip($id, $userId) {

        //Zip path
        $fileConfig = Configure::read('files');
        $folder = $this->Investor->getInvestorIdentity($userId); //Get investor identity, not $this->Session->read, Adminpfp uses it.
        $pathToZipFile = $fileConfig['investorPath'] . $folder . DS . 'investorData.Zip';



        //Zip archives
        $investorFiles = $this->Ocrfile->readExistingFiles($id); //Read all investor files
        $companyId[0]['ocrInfo']['company_id'] = $this->Session->read('Auth.User.Adminpfp.company_id'); //Set the company id for the readRequiredFiles function
        $requiredFiles = $this->Ocrfile->readRequiredFiles($companyId);   //required files for the pfp
         
        $filter = array();  //Filter for download only the required files for the pfp
        foreach($requiredFiles as $file){
           array_push($filter,$file['id']);
        }

        
        $urlList = array();
        $jsonPath = $fileConfig['investorPath'] . $folder . DS . 'dataInvestor.json';

        foreach ($investorFiles as $investorFile) {
            if(in_array($investorFile['file']['FilesInvestor']['file_id'],$filter))
            $url = $fileConfig['investorPath'] . $investorFile['file']['FilesInvestor']['file_url'];
            array_push($urlList, $url);
        }


        //Create the zip
        if ($this->Ocrfile->createZip($urlList, $pathToZipFile, true, $jsonPath)) {

          $this->set('result', true);
          $this->set('message', 'Zip downloaded');
          $this->download($pathToZipFile,  'investorData_' . $this->Session->read('Auth.User.Investor.investor_DNI') . '.Zip');
          } else {

          }
    }

    /**
     * Download documents and bills
     */
    function downloadDocument($type, $id) {
        //Request data
        $data = $this->request['data'];
        //Load files config
        $fileConfig = Configure::read('files');

        //Path and file name
        if ($type == 'ocrfile') {
            $data = $this->Ocrfile->readSimpleDocument($id);
            $pathToFile = $fileConfig['investorPath'] . $data['FilesInvestor']['file_url'];
            $name = $data['FilesInvestor']['file_name'];
        } else if ($type == 'bill') {
            $data = $this->Ocrfile->readSimpleBill($id);
            $pathToFile = $fileConfig['billsPath'] . $data['CompaniesFile']['bill_url'];
            $name = $data['CompaniesFile']['bill_number'];
        }

        //Download
        $this->download($pathToFile, $name);
    }

    function download($path, $name) {

        $this->autoLayout = false;

        $this->response->file($path, array(
            'download' => true,
            'name' => $name,
        ));
        return $this->response;
    }

}
