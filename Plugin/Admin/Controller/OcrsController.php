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
    var $uses = array('Ocr', 'Company', 'Ocrfile');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    //One Click Registration - Winvestify Admin Views


    /*     * WinAdmin View #2
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

            $this->Investor = ClassRegistry::init('Investor');

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

    /**
     * FOR DEMO ONLY
     */
    public function resetInvestorDemo($investorId) {
        $ocrId = $this->Ocr->findOcrId($investorId);
        $this->Ocr->resetOcr($ocrId);
        $this->Ocrfile->ocrAllFileDelete($investorId);
        $this->Ocr->resetCompaniesOcr($ocrId);
    }

    //WinAdmin View #7
    function generateExcel() {
        $this->layout = 'Admin.azarus_private_layout';

        // Country Codes
        Configure::load('countryCodes.php', 'default');
        $countryData = Configure::read('countrycodes');


        //Set countrys
        $this->set('countryData', $this->countryArray);

        //Status selector
        $this->set('status', $this->marketpalceStatus);

        //Modality selector
        $this->set('type', $this->crowdlendingTypesLong);

        //Get companies info for the selector
        $companiesInfo = $this->Company->getCompanyDataList(null);
        $this->set("companies", $companiesInfo);
    }

    public function importBackupExcel() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        $dateRangeStart = $this->request->query['daterangestart'];
        $dateRangeeEnd = $this->request->query['daterangeend'];
        $state = $this->request->query['state'];
        $country = $this->request->query['country'];
        $pfp = $this->request->query['pfp'];
        $freeSearch = $this->request->query['freeSearch'];

        $filter = array();
        $pfpFilter = array();


        if ($dateRangeStart !== "" && $dateRangeeEnd !== "") { //Date filter
            $dateRange = array(
                'DATE(created)' => array(
                    '> DATE(' . $dateRangeStart . ')',
                    '< DATE(' . $dateRangeeEnd . ')'
            ));
            array_push($filter, $dateRange);
        }

        if ($state != 0) { //Status filter
            $stateFilter = array(
                'marketplace_status' => $state,
            );
            array_push($filter, $stateFilter);
        }

        if ($country !== 0) { //ITS PFP COUNTRY
            $pfpList = $this->Company->getCompanyDataList(array('company_country' => $country));
            foreach ($pfpList as $pfp) {
                array_push($pfpFilter, $pfp['id']);
            }
            $pfpFilter = array('company_id' => $pfpFilter);
            array_unique($pfpFilter);
        }

        if ($pfp !== 0) { //PFP filter
            $pfpFilter = array();
            foreach ($pfp as $id) {
                array_push($pfpFilter, $pfp['id']);
            }
            $pfpFilter = array('company_id' => $pfpFilter);
            array_unique($pfpFilter);
        }
        array_push($filter, $pfpFilter);



        $this->Marketplace = ClassRegistry::init('Marketplace');
        $this->Urlsequence = ClassRegistry::init('Urlsequence');
        $this->Marketplacebackup = ClassRegistry::init('Marketplacebackup');

        $currentDateTime = date('Y-m-d_H:i:s');

        $backup = $this->Marketplacebackup->getBackup($filter);
        $this->print_r2($backup);


        /* $objPHPExcel = new PHPExcel();
          $objPHPExcel->getProperties()->setTitle("BackupExcel");


          $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(13);
          $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
          $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(28);
          $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);

          $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1', 'id')
          ->setCellValue('B1', 'company_id')
          ->setCellValue('C1', 'marketplace_amount')
          ->setCellValue('D1', 'marketplace_amountTotal')
          ->setCellValue('E1', 'marketplace_duration')
          ->setCellValue('F1', 'marketplace_durationUnit')
          ->setCellValue('G1', 'marketplace_category')
          ->setCellValue('H1', 'marketplace_rating')
          ->setCellValue('I1', 'marketplace_interestRate')
          ->setCellValue('J1', 'marketplace_purpose')
          ->setCellValue('K1', 'marketplace_status')
          ->setCellValue('L1', 'marketplace_statusLiteral')
          ->setCellValue('M1', 'marketplace_timeLeft')
          ->setCellValue('N1', 'marketplace_timeLeftUnit')
          ->setCellValue('O1', 'marketplace_name')
          ->setCellValue('P1', 'marketplace_loanReference')
          ->setCellValue('Q1', 'marketplace_subscriptionProgress')
          ->setCellValue('R1', 'marketplace_sector')
          ->setCellValue('S1', 'marketplace_requestorLocation')
          ->setCellValue('T1', 'marketplace_numberOfInvestors')
          ->setCellValue('U1', 'marketplace_origCreated')
          ->setCellValue('V1', 'marketplace_productType')
          ->setCellValue('W1', 'marketplace_country')
          ->setCellValue('X1', 'marketplace_investmentCreationDate')
          ->setCellValue('Y1', 'created');


          $i = 2;
          foreach ($backup as $row) {

          $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A' . $i, $row['Marketplacebackup']['id'])
          ->setCellValue('B' . $i, $row['Marketplacebackup']['company_id'])
          ->setCellValue('C' . $i, $row['Marketplacebackup']['marketplace_amount'])
          ->setCellValue('D' . $i, $row['Marketplacebackup']['marketplace_amountTotal'])
          ->setCellValue('E' . $i, $row['Marketplacebackup']['marketplace_duration'])
          ->setCellValue('F' . $i, $row['Marketplacebackup']['marketplace_durationUnit'])
          ->setCellValue('G' . $i, $row['Marketplacebackup']['marketplace_category'])
          ->setCellValue('H' . $i, $row['Marketplacebackup']['marketplace_rating'])
          ->setCellValue('I' . $i, $row['Marketplacebackup']['marketplace_interestRate'])
          ->setCellValue('J' . $i, $row['Marketplacebackup']['marketplace_purpose'])
          ->setCellValue('K' . $i, $row['Marketplacebackup']['marketplace_status'])
          ->setCellValue('L' . $i, $row['Marketplacebackup']['marketplace_statusLiteral'])
          ->setCellValue('M' . $i, $row['Marketplacebackup']['marketplace_timeLeft'])
          ->setCellValue('N' . $i, $row['Marketplacebackup']['marketplace_timeLeftUnit'])
          ->setCellValue('O' . $i, $row['Marketplacebackup']['marketplace_name'])
          ->setCellValue('P' . $i, $row['Marketplacebackup']['marketplace_loanReference'])
          ->setCellValue('Q' . $i, $row['Marketplacebackup']['marketplace_subscriptionProgress'])
          ->setCellValue('R' . $i, $row['Marketplacebackup']['marketplace_sector'])
          ->setCellValue('S' . $i, $row['Marketplacebackup']['marketplace_requestorLocation'])
          ->setCellValue('T' . $i, $row['Marketplacebackup']['marketplace_numberOfInvestors'])
          ->setCellValue('U' . $i, $row['Marketplacebackup']['marketplace_origCreated'])
          ->setCellValue('V' . $i, $row['Marketplacebackup']['marketplace_productType'])
          ->setCellValue('W' . $i, $row['Marketplacebackup']['marketplace_country'])
          ->setCellValue('X' . $i, $row['Marketplacebackup']['marketplace_investmentCreationDate'])
          ->setCellValue('Y' . $i, $row['Marketplacebackup']['created']);

          $i++;
          }

          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment;filename="BackupMarketplace_' . $currentDateTime . '.xls"');
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit; */
    }

}
