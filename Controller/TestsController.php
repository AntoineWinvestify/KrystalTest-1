<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-04-07
 * @package
 *

  2017-04-07	  version 2017_0.1
  function to copy a userdata photo to admin user.				[OK]

 */

/** Include path * */
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
//require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'php-bondora-api-master' . DS .  'bondoraApi.php');

/** PHPExcel_IOFactory */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

//App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS. 'Reader'. DS . 'IReadFilterWinvestify.php'));

use Petslane\Bondora;

/* use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Cell; */

class TestsController extends AppController {

    var $name = 'Tests';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Test', "Data", "Investor", "Userinvestmentdata", "Globalcashflowdata", "Linkedaccount", "Company");
    var $error;

    function beforeFilter() {
        parent::beforeFilter();


        //$this->Security->requireAuth();
        $this->Auth->allow(array('convertExcelToArray', "convertPdf", "bondoraTrying", "analyzeFile", 'getAmount', "dashboardOverview","arrayToExcel"));
    }

    function arrayToExcel(/*$array, $excelName*/) {
        $array = array(1,2,3,4,5,6,7,8,9,11,12,13,14,15,16);
        $excelName = "prueba";
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));


        $filter = null;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle($excelName);

        $objPHPExcel->setActiveSheetIndex(0)
            ->fromArray($array, NULL, 'A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $excelName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * syntax;  {DOMAIN}/tests/showUserData/2898786785624/-1
     * 
     * 	Show the user's data in a dashboard of Admin user
     * 
     *  @param 	string	$userIdentity   The unique identification of the user
     *  @param      $integer "photonumber. 0 = most recent photo, -1 one earlier, -2 two earlier etc.
     *  @return 	boolean true:  photo has been copied to current user's dashboard
     *  Another way is to "impersonate the user" copy his "auth profile and access his dashboard page. Advantage: no screwup 
     *  potential statistics functions
     */
    function showUserData($userIdentity, $number) {

        $this->autoRender = false;

        $this->layout = 'ajax';
        $this->disableCache();
        /*
         * THIS DOES NOT WORK
          $userIdentity = "41d0934670r943aed954932f";

          $investorFilterConditions = array('Investor.investor_identity' => $userIdentity);
          $investorResults = $this->Investor->find("first", array('conditions'  => $investorFilterConditions,
          'recursive' => 0,
          ));
          unset($investorResults['User']['password']);
          $temp = array();
          $temp['User'] = $investorResults['User'];
          $temp['User']['Investor'] = $investorResults['Investor'];

          $this->Session->write('AuthOriginal', $this->Session->read('Auth'));
          $this->Session->write('Auth', $temp);
          $this->print_r2($this->Session->read());
          exit;
         */






        $investorIdentity = $this->Auth->user('Investor.investor_identity');
        $dataFilterConditions = array('data_investorReference' => $userIdentity);

        $dataResults = $this->Data->find("all", array('conditions' => $dataFilterConditions,
            'order' => array('Data.id DESC'),
            'recursive' => -1,
        ));

        $absNumber = abs($number);

        if (array_key_exists($absNumber, $dataResults)) {
            $data = array('data_investorReference' => $investorIdentity,
                'data_JSONdata' => $dataResults[$absNumber]['Data']['data_JSONdata']
            );
            $this->Data->save($data, $validate = true);
            echo "Data is now available in Dashboard";
        } else {
            echo "Nothing found, try again with other data";
            exit;
        }
    }

    function showInitialPanel() {
        $this->layout = 'azarus_private_layout';
    }

    /**
     * Global dashboard view
     */
    function dashboardOverview() {
        $this->layout = 'azarus_private_layout';

        //Get data from db
        $investorIdentity = $this->Session->read('Auth.User.Investor.investor_identity');
        $globalData = $this->Userinvestmentdata->getGlobalData($investorIdentity);

        //Get global data
        $global['totalVolume'] = 0;
        $global['investedAssets'] = 0;
        $global['reservedFunds'] = 0;
        $global['cash'] = 0;
        $global['activeInvestment'] = 0;
        $global['netDeposits'] = 0;
        foreach ($globalData as $globalKey => $individualPfpData) {
            foreach ($individualPfpData['Userinvestmentdata'] as $key => $individualData) {
                if ($key == "userinvestmentdata_activeInInvestments") { //Get global active in investment
                    $global['investedAssets'] = $global['investedAssets'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_myWallet") { //Get global wallet
                    $global['cash'] = $global['cash'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_reservedFunds") { //Get global reserved funds
                    $global['reservedFunds'] = $global['reservedFunds'] + $individualData;
                    $global['totalVolume'] = $global['totalVolume'] + $individualData;
                }
                if ($key == "userinvestmentdata_investments") { //Get global active investmnent
                    $global['activeInvestment'] = $global['activeInvestment'] + $individualData;
                }
                if ($key == "id") {
                    $cashFlowData = $this->Globalcashflowdata->getData(array('userinvestmentdata_id' => $individualData), array('globalcashflowdata_platformDeposit'));
                    $global['netDeposits'] = $global['netDeposits'] + $cashFlowData[0]['Globalcashflowdata']['globalcashflowdata_platformDeposit'];
                }
                if ($key == "linkedaccount_id") {
                    //Get the pfp id of the linked acount
                    $companyIdLinkaccount = $this->Linkedaccount->getData(array('id' => $individualData), array('company_id'));
                    $pfpId = $companyIdLinkaccount[0]['Linkedaccount']['company_id'];
                    $globalData[$globalKey]['Userinvestmentdata']['pfpId'] = $pfpId;
                    //Get pfp logo and name
                    $pfpOtherData = $this->Company->getData(array('id' => $pfpId), array("company_logoGUID", "company_name"));
                    $globalData[$globalKey]['Userinvestmentdata']['pfpLogo'] = $pfpOtherData[0]['Company']['company_logoGUID'];
                    $globalData[$globalKey]['Userinvestmentdata']['pfpName'] = $pfpOtherData[0]['Company']['company_name'];
                }
            }
        }


        //Set global data
        $this->set('global', $global);
        //Set an array with individual info
        $this->set('individualInfoArray', $globalData);
    }

    function dashboardMyInvestments() {
        $this->layout = 'azarus_private_layout';
    }

    function dashboardStats() {
        $this->layout = 'azarus_private_layout';
    }

    function modal() {
        $this->layout = 'azarus_private_layout';
    }

    function convertPdf() {
        // Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile('/var/www/html/compare_local/OSchedule.pdf');
        // Retrieve all pages from the pdf file.
        $pages = $pdf->getPages();
        // Loop over each page to extract text.
        $page_string = [];

        foreach ($pages as $page) {
            echo "<br>";
            $page_string[] = $page->getText();
            echo "<br>";
        }

        foreach ($page_string as $page) {
            echo $page;
            echo "<br>";
        }

        /* $investments = explode("%", $page_string[0]);

          foreach ($investments as $investment) {
          echo $investment;
          echo "<br>";
          } */
        //echo $investments[0];
        //echo $page_string;
        //$text = $pdf->getText();
        //echo $text;
    }

    function bondoraTrying() {
        $config = array(
            'auth' => array(
                'url_base' => 'https://www.bondora.com',
                'client_id' => 'ff77b8a268b7437db6ada6cc4542a2ca',
                'secret' => 'AodG9hU9nsYyNgBku3z503wcejJLK9DrN07pm7fnEbWjuZRw',
                'scope' => 'BidsEdit BidsRead Investments SmBuy SmSell',
            ),
            'api_base' => 'https://api.bondora.com',
        );
        $api = new Bondora\Api($config);

        // Get login url
        $url = $api->getAuthUrl();
        echo $url;

        if (empty($_GET["code"])) {
            echo "patata";
            header("Location: " . $url);
            echo $url;
        }
        $code = $_GET["code"];
        echo $code;
        // redirect user to $url. After login, user will be redirected back with get parameter 'code'
        // get token from 'code' provided after user successful login. Store access_token and refresh_token
        //$token_object = $api->getToken($code);
    }

}
