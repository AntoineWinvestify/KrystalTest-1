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
 */
App::uses('CakeEvent', 'Event');

class filesController extends AppController {

    var $name = 'Files';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'Ocrfile');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /**
     * Upload a document
     */
    function upload() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

            if (count($this->params['data']['Files']) > 0) {
                $data = $this->params['data']['Files'];
                $type = $data['info'];
                $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
                $identity = $this->Investor->getInvestorIdentity($this->Session->read('Auth.User.id'));
                $result = $this->Ocrfile->ocrFileSave($data, $identity, $id, $type, "file");
                $this->set("result", $result);
            } else if (count($this->params['data']['bill']) > 0) {
                $data = $this->params['data']['bill'];
                $info = array('number' => $this->params['data']['number'], 'concept' => $this->params['data']['concept'], 'amount' => $this->params['data']['amount'], 'currency' => $this->params['data']['currency']);
                $id = $this->params['data']['pfp'];
                $company = $this->Company->getCompanyDataList(array('id' => $id))[$id]['company_codeFile'];
                $result = $this->Ocrfile->ocrFileSave($data, $company, $id, $info, "bill");
                $this->set("result", $result);
            }
        }
    }

    /**
     * Delete a document
     */
    function delete() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

            $url = $this->request->data('url');
            $file_id = $this->request->data('id');
            $investor_id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));


            $result = $this->Ocrfile->ocrFileDelete($url, $file_id, $investor_id);
            $this->set("result", $result);
        }
    }

    /**
     * Generate and download the zip
     * @param type $id
     * @param type $userId
     * @return type
     */
    function generateZip($id, $userId) {

        //Zip path
        $fileConfig = Configure::read('files');
        $folder = $this->Investor->getInvestorIdentity($userId);
        $pathToZipFile = $fileConfig['investorPath'] . $folder . DS . 'investorData.Zip';


        //Zip archives
        $investorFiles = $this->Ocrfile->readExistingFiles($id);
        $urlList = array();
        $jsonPath = $fileConfig['investorPath'] . $folder . DS . 'results.json';
        foreach ($investorFiles as $investorFile) {
           
            $url = $fileConfig['investorPath'] . $investorFile['file']['FilesInvestor']['file_url'];
            array_push($urlList, $url);
        }

        //Create the zip
        if ($this->Ocrfile->createZip($urlList, $pathToZipFile, true, $jsonPath)) {

            $this->set('result', true);
            $this->set('message', 'Zip downloaded');
            $this->download($pathToZipFile, 'investorData.Zip');
        } else {
            $this->set('result', false);
            $this->set('message', 'Zip download failed');
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
            print_r($data);
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
