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
 *  2017/07/18 version 0.1
 *  Migrated
 
 */
App::uses('CakeEvent', 'Event');

class filesController extends AdminAppController {

    var $name = 'Files';
    var $helpers = array('Session');
    var $uses = array('Company', 'Ocrfile', 'Investor');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /**
     * Upload a document
     */
    function uploadWinadmin() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();

                $data = $this->params['data']['bill']; //File info

                //Info about the bill like number, amount ...
                $extraInfo = array('number' => $this->params['data']['number'], 'concept' => $this->params['data']['concept'], 'amount' => $this->params['data']['amount'], 'currency' => $this->params['data']['currency']);
                $id = $this->params['data']['pfp']; //Pfp id
                $company = $this->Company->getCompanyDataList(array('id' => $id))[$id]['company_codeFile']; //Get company codeFile, is the folder of the bill
                $result = $this->Ocrfile->ocrFileSave($data, $company, $id, $extraInfo, "bill"); //Save the bill in db and return a result.
                $this->set("result", $result); //Set result into the view.
            
        }
    }

    /**
     * Download documents and bills
     */
    function downloadDocumentWinadmin($type, $id) {
        //Request data
        $data = $this->request['data'];
        //Load files config
        $fileConfig = Configure::read('files');

        //Path and file name
        if ($type == 'ocrfile') {
            $data = $this->Ocrfile->readSimpleDocument($id);
            $pathToFile = $fileConfig['investorPath'] . $data['FilesInvestor']['file_url'];
            $userId = $this->Investor->getInvestorUserId($data['FilesInvestor']['investor_id']);
            $dni = $this->Investor->getInvestorDni($userId);
            $name = $dni . '_' . $data['FilesInvestor']['file_name'];
        } else if ($type == 'bill') {
            $data = $this->Ocrfile->readSimpleBill($id);
            $pathToFile = $fileConfig['billsPath'] . $data['CompaniesFile']['bill_url'];
            $name = 'Winvestify'. '_' . $data['CompaniesFile']['bill_number'] . '_' . $data['CompaniesFile']['bill_concept'] . '.pdf';
        }

        //Download
         $this->downloadWinadmin($pathToFile, $name);
    }

    function downloadWinadmin($path, $name) {

        $this->autoLayout = false;

        $this->response->file($path, array(
            'download' => true,
            'name' => $name,
        ));
        return $this->response;
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
        $dateRangeEnd = $this->request->query['daterangeend'];
        $state = $this->request->query['state'];
        $country = $this->request->query['country'];
        $pfp = $this->request->query['pfp'];
        $freeSearch = $this->request->query['freeSearch'];

        $filter = array();
        $pfpFilter = array();


        if ($dateRangeStart !== "" && $dateRangeEnd !== "") { //Date filter
            $dateRange = array(
                "created >" => date($dateRangeStart),
                "created <" => date($dateRangeEnd),
            );
            $filter = array_merge($filter, $dateRange);
        }

        if ($state != 0) { //Status filter
            $stateFilter = array(
                'marketplace_status' => $state,
            );
            $filter = array_merge($filter, $stateFilter);
        }

        if ($country !== 0) { //ITS PFP COUNTRY
            $pfpList = $this->Company->getCompanyDataList(array('company_country' => $country));
            foreach ($pfpList as $pfpId) {
                array_push($pfpFilter, $pfpId['id']);
            }
        }

        if ($pfp != 0) { //PFP filter
            array_push($pfpFilter, $pfp);
        }

        if ($pfpFilter != null) {
            $pfpFilter = array('company_id' => $pfpFilter);
            $filter = array_merge($filter, $pfpFilter);
        }

        if ($freeSearch != "" || $freeSearch != null) { //Free search loan id
            $loanId = array('marketplace_loanReference LIKE' => "%$freeSearch%");
            $filter = array_merge($filter, $loanId);
        }

        $this->Marketplace = ClassRegistry::init('Marketplace');
        $this->Urlsequence = ClassRegistry::init('Urlsequence');
        $this->Marketplacebackup = ClassRegistry::init('Marketplacebackup');

        $currentDateTime = date('Y-m-d_H:i:s');

        $backup = $this->Marketplacebackup->getBackup($filter);


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("BackupExcel");


        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(13);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(28);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(25);
        
        

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
        exit;
    }
    
    

}
