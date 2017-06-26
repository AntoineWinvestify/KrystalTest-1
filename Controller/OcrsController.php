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
 * 2017/6/19 version 0.7
 * ocrWinadminBillingPanel-> bill table added
 * 
 * 2017/6/23 version 0.8
 * checking data table
 * user checking data
 * 
 * 2017/6/26 version 0.9
 * pfp admin tables ok
 * 
 */
App::uses('CakeEvent', 'Event');

class ocrsController extends AppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'File', 'Linkedaccount');
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
        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

        //First time ocr
        $this->Ocr->createOcr($id);
        $OcrData = $this->Ocr->checkStatus($id);

        //Check ocr status
        $status = $OcrData[0]['Ocr']['ocr_status'];
        //Control status
        if ($status == NOT_SENT) {
            $this->ocrInvestorPlatformSelection();
        } else if ($status == SENT) {
            $this->activatedService();
        } else if ($status == ERROR) {
            $this->ocrInvestorDataPanel();
        }
    }

    /**
     * Send the investor data to investor model and ocr data to ocr model
     */
    function oneClickInvestorII() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {

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

            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

            //Ocr data
            $datosOcr = array(
                'investor_id' => $id,
                'ocr_investmentVehicle' => $_REQUEST['investmentVehicle'],
                'investor_cif' => $_REQUEST['cif'],
                'investor_businessName' => $_REQUEST['businessName'],
                'investor_iban' => $_REQUEST['iban'],
            );
            $result2 = $this->Ocr->ocrDataSave($datosOcr);
            $ocrArray = json_decode("[" . $result2 . "]", true);

            //Update the companies status
            $idOcr = $ocrArray[1]["id"];
            $result3 = $this->Ocr->updateCompaniesStatus($idOcr);

            $this->set('result1', $result1);
            $this->set('result2', $result2);
            $this->set('result3', $result3);
        }
    }

    /**
     * Send the selected companies to ocr model
     */
    function oneClickInvestorI() {
        if (!$this->request->is('ajax')) {
            $this->set('result', 0);
        } else {
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
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
                $this->set('result', 0);
            }
        }
    }

    /**
     * Send the selected companies to ocr model
     */
    function deleteCompanyOcr() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
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
        //echo "1"; // echo  for ajax
        //Investor info
        $data = $this->Investor->investorGetInfo($this->Session->read('Auth.User.id'));

        //Investor id
        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

        //Ocr infe
        $data2 = $this->Ocr->ocrGetData($id);

        //Selected Companies info
        $companies = $this->Ocr->getSelectedCompanies($id);

        //Required  files
        $requiredFiles = $this->File->readRequiredFiles($companies);
        $filesData = $this->File->getFilesData($requiredFiles);

        //Read existing files 
        $existingFiles = $this->File->readExistingFiles($id);

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
            $file = substr($files, -3, 3);
            $typeString = $typeString . " ." . $file;
        }
        $this->set('filesType', $typeString);

        //Check data set
        $checkData = $this->Investor->readCheckData($id);
        $this->set('checkData', $checkData);

        echo " ";
    }

    /** Investor View #1
     * Select the companies you want register
     */
    function ocrInvestorPlatformSelection() {

        //Companies with ocr
        $this->set('company', $this->Company->companiesDataOCR());

        //Investor id
        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

        //Set selected companies(not sent)
        $this->set('selected', $this->Ocr->getSelectedCompanies($id));

        //Selected companies(sent)(Not show)
        $registered = $this->Ocr->getRegisterSentCompanies($id);
        $filter = array('investor_id' => $id);

        //Linked companies(Not show)
        $linked = $this->Linkedaccount->getLinkedaccountIdList($filter);
        $notShow = array();


        //Filter
        foreach ($registered as $registered) {
            array_push($notShow, $registered["company_id"]);
        }

        foreach ($linked as $linked) {
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
        echo " ";
    }

    /** Investor View #3
     * Modal to activate ocr
     */
    function ocrInvestorConfirmModal() {
        //Invesor od
        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));

        //Selected companies
        $companyId = $this->Ocr->getSelectedCompanies($id);

        //Selected companies info
        $idArray = array();
        foreach ($companyId as $id) {
            array_push($idArray, $id["companies_ocrs"]["company_id"]);
        }
        $idFilter = array("Company.id" => $idFilter);
        $companies = $this->Company->getCompanyDataList($idFilter);

        //Set info
        $this->set("companies", $companies);

        //$this->Auth->redirectUrl();


        echo " ";
    }

    //One Click Registration - PFPAdmin Views
    //PFPAdmin View #2
    function ocrPfpBillingPanel() {
        //Read all company bills
        $bills = $this->File->billCompanyFilter(6);
        $this->set('bills', $bills);

        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    /*     * PFPAdmin View #1
     * New accepted user
     */

    function ocrPfpUsersPanel() {
        //Read and accepted relations
        $ocrList = $this->Ocr->getAllOcrRelations(6);
        $this->set('ocrList', $ocrList);

        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //PFPAdmin View #3
    function ocrPfpTallyman() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //One Click Registration - Winvestify functions
    function addBill() {
        
    }

    //One Click Registration - Winvestify Admin Views


    /*     * WinAdmin View #2
     * 
     */
    function ocrWinadminInvestorChecking() {

        //Filter
        $filter = array('ocr_status' => SENT);

        //Search  and set investor data 
        $ocrList = $this->Ocr->ocrGetData(null, $filter);
        $this->set('usersList', $ocrList);
        //Get user data
        //$userList = $this->Ocr->getRegisterSentCompanies(null);


        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    /** WinAdmin View #1
     *  WinAdmin Bill panel
     */
    function ocrWinadminBillingPanel() {
        $this->layout = 'azarus_private_layout';

        //get all bills and set them in the view
        $billsInfo = $this->File->getAllBills();
        $this->set("bills", $billsInfo);

        //Get companies info for the select
        $companiesInfo = $this->Company->getCompanyDataList(null);
        $this->set("companies", $companiesInfo);
        echo " ";
    }

    /** Check data
     * WinAdmin View #3
     * @param type $id
     */
    function ocrWinadminInvestorData($id) {

        //Search and set investor data
        $userData = $this->Ocr->ocrGetData($id, null);
        $this->set('userData', $userData);

        //Search and set investor checking
        $checking = $this->Investor->readCheckData($id);
        $this->set('checking', $checking);

        //Search and set investor files
        $files = $this->File->readExistingFiles($id);
        $this->set('files', $files);

        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //WinAdmin View #4
    function ocrWinadminUpdatePfpData() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //WinAdmin View #5
    function ocrWinadminSoldUsers() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //WinAdmin View #6
    function ocrWinadminTallyman() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //Activated Service VIEW
    function activatedService() {
        $this->layout = 'azarus_private_layout';
        $this->render("../Layouts/activated_service");
    }

}
