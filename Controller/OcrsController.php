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
 * 2016/29/2017 version 0.1
 * function OneClickInvestorI, Save personal data in db                    [OK]
 * function OneClickInvestorII Save selected companies                     [OK]
 * function companyFilter      Company filter for platform selection panel [OK]
 *
 * 2017/6/01  version 0.2
 * upload                                                            [OK]
 * 2017/6/05  version 0.3
 * deleteCompanyOcr                                                     [OK]
 *                                       
 * 2017/6/06  version 0.4
 * upload deleted
 * id problem fixed
 *     
 * 2017/6/13  version 0.5
 * Ocr status added
 * 
 * 2017/6/16  version 0.6
 * oneClickInvestorI error 500 fixed
 * 
  2017/6/19 version 0.7
  ocrWinadminBillingPanel-> bill table added

 * 2017/6/23 version 0.8
 * checking data table
 * user checking data
 * 
 * 2017/6/26 version 0.9
 * pfp admin tables ok
 * 
 * [2017-06-28] Version 0.10
 * Added countryCodes to VinAdmin View #4  (Update PFP Data, to select country)
 * Added currencyName to WinAdmin View #3 (Billing Panel)
 * Added 
 * server validation
 * 
 * 
 * [2017-06-28] Version 0.11
 * Set status name
 * Bills table refesh
 * 
 * [2017-06-29] Version 0.12
 *   Update pfp added
 * 
 * [2017-06-30] Version 0.13
 * Update pfp completed
 * Upload cif
 * 
 * [2017-07-03] Version 0.14
 * Update Checks
 */
App::uses('CakeEvent', 'Event');

class ocrsController extends AppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'Ocrfile', 'Linkedaccount');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /*
     * 
     * Ciclo Principal
     * 
     */

    /**
     *  Main ocr view for investor
     */
    function ocrInvestorView() {
        $this->layout = 'azarus_private_layout';

        //Investor id
        $id = $this->Session->read('Auth.User.investor_id');

        //First time ocr
        $this->Ocr->createOcr($id);
        $OcrData = $this->Ocr->checkStatus($id);

        //Check ocr status
        $status = $OcrData[0]['Ocr']['ocr_status'];

        //Control status
        if ($status == NOT_SENT || $status == OCR_FINISHED) {
            //$this->ocrInvestorPlatformSelection();
            $this->set('link', 'ocrInvestorPlatformSelection');
        } else if ($status == ERROR) {
            //$this->ocrInvestorDataPanel();
            $this->set('link', 'ocrInvestorDataPanel');
        } else if ($status == SENT || $status == OCR_PENDING || $status = FIXED) {
            $this->activatedService();
        }
    }

    /**
     * Data panel actions
     */
    function oneClickInvestorII() {

        App::import("Vendor", "ibanhandler/oophp-iban");
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            //if ($this->Verify($_REQUEST['iban'])) {
            $this->layout = 'ajax';
            $this->disableCache();



            //Investor data
            $investor_name = strip_tags(htmlspecialchars($_REQUEST['investor_name']));
            $investor_surname = strip_tags(htmlspecialchars($_REQUEST['investor_surname']));
            $investor_DNI = strip_tags(htmlspecialchars($_REQUEST['investor_DNI']));
            $investor_dateOfBirth = $_REQUEST['investor_dateOfBirth'];
            $investor_telephone = $_REQUEST['investor_telephone'];
            $investor_address1 = strip_tags(htmlspecialchars($_REQUEST['investor_address1']));
            $investor_postCode = strip_tags(htmlspecialchars($_REQUEST['investor_postCode']));
            $investor_city = strip_tags(htmlspecialchars($_REQUEST['investor_city']));
            $investor_country = $_REQUEST['investor_country'];
            $investor_email = $_REQUEST['investor_email'];

            $datosInvestor = array(
                'id' => $this->Session->read('Auth.User.id'),
                'investor_name' => $investor_name,
                'investor_surname' => $investor_surname,
                'investor_DNI' => $investor_DNI,
                'investor_dateOfBirth' => $investor_dateOfBirth,
                'investor_telephone' => $investor_telephone,
                'investor_address1' => $investor_address1,
                'investor_postCode' => $investor_postCode,
                'investor_city' => $investor_city,
                'investor_country' => $investor_country,
                'investor_email' => $investor_email,
            );

            $result1 = $this->Investor->investorDataSave($datosInvestor);

            $id = $this->Session->read('Auth.User.investor_id');
            $status = $this->Ocr->checkStatus($id);

            //Ocr data          
            $datosOcr = array(
                'investor_id' => $id,
                'ocr_investmentVehicle' => $_REQUEST['investmentVehicle'],
                'investor_cif' => $_REQUEST['cif'],
                'investor_businessName' => $_REQUEST['businessName'],
                'investor_iban' => $_REQUEST['iban'],
                'ocr_status' => $status,
            );
            $result2 = $this->Ocr->ocrDataSave($datosOcr);
            $ocrArray = json_decode("[" . $result2 . "]", true);

            //Update the companies status
            $idOcr = $ocrArray[1]["id"];
            $result3 = $this->Ocr->updateCompaniesStatus($idOcr);

            $this->set('result1', $result1);
            $this->set('result2', $result2);
            $this->set('result3', $result3);
            /* } else {
              $this->set('result1', false);
              } */
        }
    }

    /**
     * Select platform actions
     */
    function oneClickInvestorI() {
        if (!$this->request->is('ajax')) {
            $this->set('result', false);
        } else {
            $id = $this->Session->read('Auth.User.investor_id');
            $this->layout = 'ajax';
            $this->disableCache();

            $companyNumber = $_REQUEST['numberCompanies'];

            if ($companyNumber != 0) {
                $companies = array(
                    'investorId' => $id,
                    'number' => $companyNumber,
                    'idCompanies' => $_REQUEST['idCompany']
                );

                //Save the comapnies
                $result = $this->Ocr->saveCompaniesOcr($companies);
                $this->set('result', $result);
            } else {
                $this->set('result', false);
            }
        }
    }

    /**
     * delete the selected company
     */
    function deleteCompanyOcr() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $id = $this->Session->read('Auth.User.investor_id');
            $this->layout = 'ajax';
            $this->disableCache();

            $companyId = $_REQUEST['id_company'];

            $delComp = array(
                'investorId' => $id,
                'companyId' => $companyId,
            );

            //Delete the companies
            $result = $this->Ocr->deleteCompanyOcr($delComp);
            $this->set('result', $result);
        }
    }
    
    /**
     * Delete all NOT_SENT companies_ocrs of a investor
     */
    function deleteCompanyOcrAll(){
         $ocrId = $this->Session->read('Auth.User.Investor.ocr_id');
         $result = $this->Ocr->deleteCompanyOcrAll($ocrId);
         $this->set('result', $result);
    }
    
    /**
     * Company filter
     * Send the filter conditions and return a companies list
     */
    function companyFilter() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();
            $filter = ['country_filter' => $_REQUEST['country_filter'], 'type_filter' => $_REQUEST['type_filter'],];

            //Filter
            $result = $this->Company->companiesDataOCR($filter);
            $this->set('company', $result);
        }
    }

    //One Click Registration - Investor Views

    /** Investor View #2
     * The panel contains all required data and files for ocr
     * 
     * @return int
     */
    function ocrInvestorDataPanel() {
        if (!$this->request->is('ajax')) {
            //Ajax result
            $this->set('result', false);
        } else {
            //Investor info
            $data = $this->Investor->investorGetInfo($this->Session->read('Auth.User.id'));

            //Investor id
            $id = $this->Session->read('Auth.User.investor_id');

            //Ocr infe
            $data2 = $this->Ocr->ocrGetData($id);

            //Selected Companies info
            $companies = array();
            $companies = array_merge($this->Ocr->getSelectedCompanies($id), $this->Ocr->getRegisterSentCompanies($id));

            //Required  files
            $requiredFiles = $this->Ocrfile->readRequiredFiles($companies);
            $filesData = $this->Ocrfile->getFilesData($requiredFiles);

            //Read existing files 
            $existingFiles = $this->Ocrfile->readExistingFiles($id);


            //Set all info
            $this->set('investor', $data);
            $this->set('ocr', $data2);
            $this->set('requiredFiles', $filesData);
            $this->set('existingFiles', $existingFiles);
            Configure::load('countryCodes.php', 'default');
            $countryData = Configure::read('countrycodes');
            $this->set('countryData', $countryData);

            //Type set
            $fileConfig = Configure::read('files');
            $typeString = null;
            foreach (array_unique($fileConfig['permittedFiles']) as $files) {
                $file = explode('/',$files)[1];
                $typeString = $typeString . " ." . $file;
            }
            $this->set('filesType', $typeString);

            //Check data set
            $checkData = $this->Investor->readCheckData($id);
            $this->set('checkData', $checkData);

            //Ajax result
            $this->set('result', true);
        }
    }

    /** Investor View #1
     * Select the companies you want register
     */
    function ocrInvestorPlatformSelection() {
//        Configure::write('debug', 2); 
        if (!$this->request->is('ajax')) {
            //Ajax result
            $this->set('result', false);
        } else {
            //Companies with ocr
            $this->set('companies', $this->Company->companiesDataOCR());

            //Types
            $this->set('CompanyType', $this->crowdlendingTypesLong);

            //Investor id
            $id = $this->Session->read('Auth.User.investor_id');

            //Set selected companies(not sent)
            $this->set('selected', $this->Ocr->getSelectedCompanies($id));

            //Selected companies(sent)(Not show)
            $registeredList = $this->Ocr->getRegisterSentCompanies($id);
            $filter = array('investor_id' => $id);

            //Linked companies(Not show)
            $linkedList = $this->Linkedaccount->getLinkedaccountIdList($filter);
            $notShow = array();


            //Filter
            foreach ($registeredList as $registered) {
                array_push($notShow, $registered["ocrInfo"]["company_id"]);
            }

            foreach ($linkedList as $linked) {
                array_push($notShow, $linked["Linkedaccount"]["company_id"]);
            }
            $notShowList = array_unique($notShow);
            $filterList = array('id' => $notShowList);

            $companyInfo = $this->Company->getCompanyDataList($filterList);
            $result = array();
            foreach ($companyInfo as $info) {
                array_push($result, array('id' => $info['id'], 'name' => $info["company_name"]));
            }

            //Set companies filter
            $this->set('notShow', $result);

            //Ajax result
            $this->set('result', true);
        }
    }

    /** Investor View #3
     * Modal to activate ocr
     */
    function ocrInvestorConfirmModal() {
        //Invesor od
        $id = $this->Session->read('Auth.User.investor_id');

        //Selected companies
        $companyId = $this->Ocr->getSelectedCompanies($id);

        //Status
        $status = $this->Ocr->checkStatus($id);

        //Set info
        $this->set("companies", $companyId);
        $this->set("status", $status);
    }

    /** Investor View #4
     * Modal to completed process
     */
    function ocrCompletedProcess() {
        $this->layout = 'azarus_private_layout';
    }

    //Activated Service VIEW
    function activatedService() {
        $this->layout = 'azarus_private_layout';
        $this->render("/Layouts/activated_service");
    }

    /**
     * 
     */
    public function resetInvestorDemo() {
        $ocrId = $this->Session->read('Auth.User.Investor.ocr_id');
        $this->Ocr->resetOcr($ocrId);
        $this->Ocrfile->ocrAllFileDelete($this->Session->read('Auth.User.Investor.id'));
        $this->Ocr->resetCompaniesOcr($ocrId);
    }

}
