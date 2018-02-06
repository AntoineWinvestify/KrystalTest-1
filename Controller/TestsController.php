
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
        $this->Auth->allow(array('convertExcelToArray', "convertPdf", "bondoraTrying",
            "analyzeFile", 'getAmount', "dashboardOverview", "arrayToExcel", "insertDummyData", "downloadTimePeriod",
            "testDateDiff", "xlsxConvert", "read"));
    }

    var $dateFinish = "20171129";
    var $numberOfFiles = 0;
    
    public function xlsxConvert() {
        echo 'Inicio';

        $unoconv = Unoconv\Unoconv::create();
        echo APP .  "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS . 'test.xlsx';
        $meh = fopen (APP .  "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS . 'test.xlsx', "r+");
        echo fread($meh);
         //$unoconv->transcode('\home ' . DS . 'eduardo' . DS . 'Downloads' . DS . 'testmenor.xlsx', 'pdf','\home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'testmenorConvertido.pdf');
        $unoconv->transcode(APP . "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS  . 'test.xlsx', 'pdf', APP . "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS  . 'testConvertido.pdf');

        echo 'Fin';
    }

    public function read() {
        $file = "test.xlsx";
        $finfo = finfo_open();
        $fileinfo = finfo_file($finfo, $file, FILEINFO_MIME);
        finfo_close($finfo);
        print_r($fileinfo);
        
        
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        
        $inputFileType = 'Excel2007';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
         $objPHPExcel = $objReader->load($file);
        $worksheet = $objPHPExcel->getActiveSheet();
        
        foreach ($worksheet->getRowIterator() as $row) {
            echo 'Row number: ' . $row->getRowIndex() . "\r\n";
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
                }
            }
        }
        
    }

    public function xlsxRead() {
        //$file =  APP  . "files" . DS . "investors" . DS . "39048098ab409be490A" . DS . "20171217" . DS . "898" . DS . "bondora" . DS . "investment_1_1.xlsx";

        $file = APP . "investment_1.csv";

        $finfo = finfo_open();
        $fileinfo = finfo_file($finfo, $file, FILEINFO_MIME);
        finfo_close($finfo);
        echo "///////////////////////////////////" . $fileinfo;

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        //WE MUST CLEAR CSV OF SPECIAL CHARACTERS
        $csv = fopen($file, "r");
        $csvString = mb_convert_encoding(fread($csv, filesize($file)), "UTF-8"); //Convert special characters
        fclose($csv);
        $csv = fopen($file, "w+");   //Rewrite old csv
        fwrite($csv, $csvString);
        fclose($csv);


        $inputFileType = 'CSV';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setDelimiter(';');
        $objPHPExcel = $objReader->load($file);
        $worksheet = $objPHPExcel->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            echo 'Row number: ' . $row->getRowIndex() . "\r\n";
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
                }
            }
        }
    }

    function clearCsv($string) {
        $not_permited = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permited = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        return str_replace($not_permited, $permited, $string);
    }

    public function downloadTimePeriod($dateMin, $datePeriod) {
        $dateMin = "20090101";
        $datePeriod = 120;
        echo "start" . HTML_ENDOFLINE;
        do {
            if ($this->numberOfFiles == 0) {
                $this->dateInit = date("Ymd", strtotime($this->dateFinish . " " . -$datePeriod . " days")); //First init date must be Finish date - time period
                echo "Empiezo en " . $this->dateInit . " Termino en " . $this->dateFinish . " ";
                $this->numberOfFiles++;
                echo $this->numberOfFiles . HTML_ENDOFLINE;
            } else {
                $this->dateFinish = date("Ymd", strtotime($this->dateInit . " " . -1 . " days")); //Next finish date will we the previous day of the last Init date
                $this->dateInit = date("Ymd", strtotime($this->dateInit . " " . -$datePeriod . " days"));
                if (date($this->dateInit) < date($dateMin)) {
                    $this->dateInit = date($dateMin); //Condition for dont go a previus date than $dateMin;
                }
                echo "Otro Empiezo en " . $this->dateInit . " Termino en " . $this->dateFinish . " ";
                $this->numberOfFiles++;
                echo $this->numberOfFiles . HTML_ENDOFLINE;
            }
        } while (date($this->dateInit) > date($dateMin));


        /* if($this->dateInit <= date("Ymd", $dateMin)){
          return false; //End period download
          }
          return true; //Continue period download */
    }

    function arrayToExcel($array, $excelName) {
        /*$array = array("market" => 1, "q" => 2, "a" => 3, "s" => 4, "d" => 5, "f" => 6, "e" => 7, "r" => 8, "t" => 9, "y" => 11, "u" => 12, "i" => 13, "o" => 14, "p" => 15, "l" => 16);
        $excelName = "prueba";*/
        $keyArray = array();
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        foreach ($array as $key => $val) {
            $keyArray[] = $key;
        }

        $filter = null;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle($excelName);

        $objPHPExcel->setActiveSheetIndex(0)
                ->fromArray($keyArray, NULL, 'A1')
                ->fromArray($array, NULL, 'A2');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($excelName);
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

    public function insertDummyData() {
        //$model = ClassRegistry::init("Userinvestmentdata");

        /* for ($i = 0; $i < 600; $i++) {
          $random = rand(500, 15000);
          $random2 = rand(100, 5000);
          $random3 = rand(300, 5000);
          $date = date("Y-m-d",strtotime("-$i days"));

          $data['Userinvestmentdata']['linkedaccount_id'] = 35705;
          $data['Userinvestmentdata']['userinvestmentdata_investorIdentity'] = "39048098ab409be490A";
          $data['Userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $random;
          $data['Userinvestmentdata']['userinvestmentdata_totalLoansCost'] = $random2;
          $data['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'] = $random3;
          $data['Userinvestmentdata']['date'] = $date;
          round(bcmul(bcdiv($global['cash'], $global['totalVolume'],16), 100, 16), 2, PHP_ROUND_HALF_UP);
          //echo $random . "<br>";
          //echo $date . "<br>";
          $model->create();
          $model->save($data);
          } */

        $model = ClassRegistry::init("Paymenttotal");
        $model->virtualFields = array('paymenttotal_totalCost' . '_sum' => 'sum(paymenttotal_myInvestment + paymenttotal_secondaryMarketInvestment)');
        $sumValue = $model->find('list', array(
            'fields' => array('date', 'paymenttotal_totalCost' . '_sum'),
            'group' => array('date')
                /* 'conditions' => array(
                  "date >=" => $dateInit,
                  "date <=" => $dateFinish,
                  "linkedaccount_id" => $linkedaccountId
                  ) */
                )
        );
        print_r($sumValue);
        exit;

        /* $total = $this->Model->find('all', array(
          'fields' => array(
          'SUM(Model.price + OtherModel.price) AS total'
          ),
          'group' => 'Model.id'
          )); */

        /* $model->virtualFields = array('paymenttotal_regularGrossInterestIncome' . '_sum' => 'sum(paymenttotal_regularGrossInterestIncome)');
          $sumValue2  =  $model->find('list',array(
          'fields' => array('date', 'paymenttotal_regularGrossInterestIncome' . '_sum'),
          'group' => array('date')
          /*'conditions' => array(
          "date >=" => $dateInit,
          "date <=" => $dateFinish,
          "linkedaccount_id" => $linkedaccountId
          ) */
        /* )
          ); */

        /* foreach ($sumValue as $key => $value) {
          $totalSum[$key] = $value + $sumValue2[$key];
          }

          print_r($totalSum);
          /*$sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          );

          /* $model->virtualFields = array('paymenttotal_regularGrossInterestIncome' . '_sum' => 'sum(paymenttotal_regularGrossInterestIncome)');
          $sumValue2  =  $model->find('list',array(
          'fields' => array('date', 'paymenttotal_regularGrossInterestIncome' . '_sum'),
          'group' => array('date')
          /*'conditions' => array(
          "date >=" => $dateInit,
          "date <=" => $dateFinish,
          "linkedaccount_id" => $linkedaccountId
          ) */
        /* )
          ); */

        /* foreach ($sumValue as $key => $value) {
          $totalSum[$key] = $value + $sumValue2[$key];
          }

          print_r($totalSum);
          /*$sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          )
          ); */

        $model2 = ClassRegistry::init("Userinvestmentdata");

        $idByDate = $model2->find('list', array(
            'fields' => array('date', 'id'),
            'group' => array('date')
                /* 'conditions' => array(
                  "date >=" => $dateInit,
                  "date <=" => $dateFinish,
                  "linkedaccount_id" => $linkedaccountId
                  ) */
                )
        );


        foreach ($sumValue as $key => $sum) {
            //$data['Userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $sum;
            $model2->id = $idByDate[$key];
            $model2->saveField('userinvestmentdata_totalLoansCost', $sum);
        }

        /* $sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          )
          ); */
    }

    public function testDateDiff() {
        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-06-26");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170626 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        echo 20170626 - 20130324 . "<br>";
        echo 20170000 - 20130000 . "<br>";
        if ($resultDate1 <= $resultDate2) {
            echo $years = 2017 - 2013;
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";

        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-03-24");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170324 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        if ($resultDate1 <= $resultDate2) {
            echo $years = 2017 - 2013 . "<br>";
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";
        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-01-26");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170126 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        if ($resultDate1 <= $resultDate2) {
            echo $resultDate1 . "<br>";
            echo $years = 2017 - 2013 . "<br>";
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";
        echo 20170326 - 20130324 . "<br>";
        echo 20170323 - 20130324 . "<br>";
        echo 20170000 - 20130000 . "<br>";
    }

}
