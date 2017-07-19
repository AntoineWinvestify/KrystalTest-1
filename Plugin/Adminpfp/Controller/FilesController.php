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
 *  2017/07/18 version 0.1
 *  Migrated
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
        $dni = $this->Investor->getInvestorDni($userId);
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
        if ($this->Ocrfile->createZip($dni, $urlList, $pathToZipFile, true, $jsonPath)) {

          $this->set('result', true);
          $this->set('message', 'Zip downloaded');
          $this->download($pathToZipFile,  'investorData_' . $dni . '.Zip');
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
