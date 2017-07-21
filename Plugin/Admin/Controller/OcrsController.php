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

class ocrsController extends AdminAppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'Ocrfile', 'Linkedaccount');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /*     WinAdmin View #2
     * 
     */
    function ocrWinadminInvestorChecking() {

        //Filter
        $filter = array('ocr_status' => array(SENT, ERROR, OCR_PENDING, OCR_FINISHED, FIXED));

        //Search  and set investor data 
        $ocrList = $this->Ocr->ocrGetData(null, $filter);
        $this->set('usersList', $ocrList);

        //Get user data
        //$userList = $this->Ocr->getRegisterSentCompanies(null);
        //Set Status name
        $this->set('status', $this->ocrStatus);

        $this->layout = 'Admin.azarus_private_layout';
    }

    /** WinAdmin View #1
     *  WinAdmin Bill panel
     */
    function ocrWinadminBillingPanel() {
        $this->layout = 'Admin.azarus_private_layout';

        //get all bills and set them in the view
        $billsInfo = $this->Ocrfile->getAllBills();
        $this->set("bills", $billsInfo);

        //Get companies info for the select
        $companiesInfo = $this->Company->getCompanyDataList(null);
        $this->set("companies", $companiesInfo);

        // Currency names
        $this->set('currencyName', $this->currencyName);
    }

    function billsTable() {
        if (!$this->request->is('ajax')) {
            $this->set("result", false);
            $this->set("message", __('Error at refreshing the bills table.'));
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

            //get all bills and set them in the view
            $billsInfo = $this->Ocrfile->getAllBills();

            $this->set("result", true);
            $this->set("bills", $billsInfo);
        }
    }

    /** Check data
     * WinAdmin View #3
     * @param type $id
     */
    function ocrWinadminInvestorData($id) {
        App::import('Controller', 'Investors');
        //Search and set investor data
        $userData = $this->Ocr->ocrGetData($id, null);
        $this->set('userData', $userData);

        $investorsController = new InvestorsController;
        // Call a method from
        //Search and set investor checking
        $checking = $investorsController->readCheckData($id);
        $this->set('checking', $checking);

        //Search and set investor files
        $files = $this->Ocrfile->readExistingFiles($id);
        $this->set('files', $files);

        $this->layout = 'Admin.azarus_private_layout';
    }

    /**
     * Update checks
     */
    function updateChecks() {
        if (!$this->request->is('ajax')) {
            $result = array(false, __('Error updating data check.'));
            $this->set("result", $result);
        } else {
            $this->layout = 'ajax';
            $this->disableCache();
            //Request the data
            $data = $this->request['data'];
            $result = $this->Investor->updateCheckData($data);

            //set result
            $this->set("result", $result);
        }
    }

    /**
     * WinAdmin View #4
     */
    function ocrWinadminUpdatePfpData() {
        $this->layout = 'Admin.azarus_private_layout';

        // Country Codes
        Configure::load('countryCodes.php', 'default');
        $countryData = Configure::read('countrycodes');
        $this->set('countryData', $countryData);

        //Status selector
        $this->set('serviceStatus', $this->serviceStatus);

        //Modality selector
        $this->set('type', $this->crowdlendingTypesLong);

        //Get companies info for the selector
        $companiesInfo = $this->Company->getCompanyDataList(null);
        $this->set("companies", $companiesInfo);
    }

    /**
     * Update a company
     */
    function updateCompanyOcrData() {

        // Country Codes
        Configure::load('countryCodes.php', 'default');
        $countryData = Configure::read('countrycodes');

        //Request data
        $data = array(
            'id' => $this->request['data']['pfp'],
            'company_termsUrl' => $this->request['data']['temrs'],
            'company_privacyUrl' => $this->request['data']['privacy'],
            'company_PFPType' => $this->request['data']['modality'],
            'company_country' => $this->request['data']['country'],
            'company_countryName' => $countryData[$this->request['data']['country']]
        );

        //Check actual statuos
        $id = $this->Company->checkOcrServiceStatus($data['id'])[1]['Serviceocr']['id'];

        //If have a status, update it. If not, create it.
        if ($id != null || $id != 0) {
            $status = array(
                'id' => $id,
                'company_id' => $this->request['data']['pfp'],
                'serviceocr_status' => $this->request['data']['ocr']
            );
        } else {
            $status = array(
                'company_id' => $this->request['data']['pfp'],
                'serviceocr_status' => $this->request['data']['ocr']
            );
        }

        //Set result
        $result = $this->Company->UpdateCompany($data, $status);

        $this->set('result', $result);
    }

    //WinAdmin View #5
    function ocrWinadminSoldUsers() {
        $this->layout = 'Admin.azarus_private_layout';
    }

    //WinAdmin View #6
    function ocrWinadminTallyman() {
        $this->layout = 'Admin.azarus_private_layout';
    }
}
