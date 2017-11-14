<?php

App::uses('AppController', 'Controller');

class CasesController extends AppController {

    public function main() {
        $this->out('Hello world.');


        echo "this is the test script running \n";
        $serial_result = "Antoine de Poorter";
        $fhandle = fopen("/home/antoine/testname_own_dir.txt", "a+");
        fwrite($fhandle, $serial_result);
        fclose($fhandle);
        $fhandle1 = fopen("testname.txt", "a+");
        fwrite($fhandle1, $serial_result);
        fclose($fhandle1);
    }

    function beforeFilter() {
        parent::beforeFilter();

        $this->Security->requireAuth();
        $this->Auth->allow(array('testDivision', 'testParserAnalyze', 'testParserAnalyzeAndConfig', 'testParserConfig', 'testParserConfigFormat1'
            , 'testDate1', 'testDate2', 'testDate3', 'testDate4', 'testCurrency', 'testAmount1', 'testAmount2', 'testAmount3', 'testAmount4', 'testAmount5',
            'testAmount6', 'testAmount7', 'testExtracData', 'testExtracData2', 'testHash', 'testRowData', 'testTransactionDetail', "testHtmlData"
        ));
        $this->filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'ParserTestCasesDocument.xlsx';
        $this->TransactionfilePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'transaction.xlsx';
        $this->amortizationPath = DS . "home" . DS . "eduardo" . DS . "Downloads" . DS . "amortization.html";
    }

    /**
     * Analyze testing
     */
    public function testParserAnalyze() {


        $parserConfig = [
            "A" => [
                "name" => "investment.investment_country"                               // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);

        print_r($tempResult);
        return $tempResult;
    }

    /**
     * Get & Set config testing
     */
    public function testParserConfig() {

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $tempResult = $myParser->getConfig();
        $this->print_r2($tempResult);
        return $tempResult;
    }

    /**
     * Analyze and set/get config testing
     */
    public function testParserAnalyzeAndConfig() {

        $parserConfig = [
            "A" => [
                "name" => "investment.investment_country"                               // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 4,
            'offsetEnd' => 4,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        $this->print_r2($tempResult);
        //print_r($final);
        return $tempResult;
    }

    public function testParserConfigFormat1() {  //NAME + TYPE
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "date",
                    "inputData" => [
                        "input2" => "D-M-Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 3,
            'offsetEnd' => 3,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testParserConfigFormat2() { // NAME + NAME
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "A" => [
                "name" => "Country"
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testParserConfigFormat3() { //TYPE + TYPE
        $parserConfig = [
            "R" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate"
                ],
            ],
            "V" => [
                [
                    "type" => "investment.priceInSecondaryMarket", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.priceInSecondaryMarket",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testParserConfigFormat4() { //" 2 TYPE IN ONE
        $parserConfig = [
            "G" => [
                [
                    "type" => "LoanAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment.division", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "#current.LoanAmount",
                        "input3" => "#previous.LoanAmount",
                        "input4" => 16
                    ],
                    "functionName" => "divisionInPercentage",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testError1() {

        $parserConfig = [
            "G" => [
                [
                    "type" => "LoanAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment.division", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "#current.LoanAmount",
                        "input3" => "#previous.LoanAmount",
                        "input4" => 16
                    ],
                    "functionName" => "DivisionInPercentage",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
    }

    public function testDate1() { // D-M-Y
        $parserConfig = [
            "B" => [
                "name" => "investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array("investment_loanId"),
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate2() { // mm-dd-YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "m.d.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate3() { // MM-DD-YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "M.D.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate4() { // YYYY.MM.DD
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "Y.M.D",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate5() { // YYYY.mm.dd
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "Y.m.d",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate6() { // YYYY.DD.MM
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "Y.D.M",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate7() { // dd/mm/YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "d.m.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate8() { // DD/MM/YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate9() { // mm/dd/YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "m.d.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDate10() { // MM/DD/YYYY
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "C" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "M.D.Y",
                    ],
                    "functionName" => "normalizeDate",]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testCurrency() {
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "X" => [
                [
                    "type" => "investment.currency", // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount1() { // format 0,00453
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount2() { // format 2.400,5548
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => ".",
                        "input3" => ",",
                        "input4" => 5
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount3() { // format €24005,000995
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 7
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount4() { // format €2.545,442424
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => ".",
                        "input3" => ",",
                        "input4" => 7
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount5() { // format 2.566,8778433868774
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => ".",
                        "input3" => ",",
                        "input4" => 14
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount6() { // format 2500,45214€
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 6
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testAmount7() { // format 2500,45214€
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => "E",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testExtracData() {
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "Y" => [
                [
                    "type" => "investment.test", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "a",
                        "input3" => "e",
                    ],
                    "functionName" => "extractDataFromString",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testExtracData2() {
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "Y" => [
                [
                    "type" => "investment.test", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "a",
                        "input3" => ";",
                    ],
                    "functionName" => "extractDataFromString",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }
    
    
    public function testHash() {
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"
            ],
            "A" => [
                [
                    "type" => "investment.hashCoutry", // Winvestify standardized name   OK
                    "functionName" => "getHash",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testDivision() {

        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
            "G" => [
                [
                    "type" => "LoanAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment.division", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "#current.LoanAmount",
                        "input3" => "#previous.LoanAmount",
                        "input4" => 16
                    ],
                    "functionName" => "DivisionInPercentage",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $config = array ( 'offsetStart' => 1, 'offsetEnd' => 0, 'sortParameter' => array(), 'separatorChar' => ";");
        $myParser = new Fileparser();
        $myParser->setConfig($config);
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        $this->print_r2($tempResult);
        return $tempResult;
    }

    public function testRowData() {
        $parserConfig = [
            "B" => [
                "name" => "investment.investment_loanId",
            ],
            "D" => [
                "name" => "loanType",
            ],
            "Z" => [
                [
                    "type" => "loanType", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "#previous.loanType", // The calculated field  "Type" from the *previous* excel row (i.e. previous aray index) is loaded
                        // Note that "Type" must be a field defined in this config file
                        // keywords are "#previous" and "#current" 
                        // Be aware that #previous does NOT contain any data in case of parsing the
                        // first line of the file.
                        "input3" => false               // This parameter indicates if the defined field will be overwritten 
                    // if it already contains a value.
                    // 
                    ],
                    "functionName" => "getRowData",
                ]
            ]
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "investment.investment_loanId",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->filePath, $parserConfig);
        $this->print_r2($tempResult);
        return $tempResult;
    }

    public function testTransactionDetail() {

        $parserConfig = [
            "A" => [
                "name" => "Trasaction.TransactionID",
            ],
            "C" => [
                [
                    "type" => "transactionDetail", // Winvestify standardized name   OK
                    "inputData" => [// List of all concepts that the platform can generate
                        // format ["concept string platform", "concept string Winvestify"]
                        "input3" => [0 => ["Incoming client payment" => "Cash_deposit"], // OK
                            1 => ["Investment principal increase" => "Primary_market_investment"],
                            2 => ["Investment share buyer pays to a seller" => "Secondary_market_investment"],
                            3 => ["Investment principal repayment" => "Capital_repayment"], //OK
                            4 => ["Investment principal rebuy" => "Principal_buyback"], // OK                               
                            5 => ["Interest income on rebuy" => "Interest_income_buyback"], // OK
                            6 => ["Interest income" => "Regular_gross_interest_income"], //
                            7 => ["Delayed interest income" => "Delayed_interest_income"], // OK
                            8 => ["Late payment fee income" => "Late_payment_fee_income"], // OK                                       
                            9 => ["Delayed interest income on rebuy" => "Delayed_interest_income_buyback"], // OK
                            10 => ["Discount/premium for secondary market" => "Income_secondary_market"], // For seller
                            11 => ["Discount/premium for secondary market" => "Cost_secondary_market"], // for buyer
                        ]
                    ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');

        $myParser = new Fileparser();
        $myParser->setConfig(array(
            'sortParameter' => "Trasaction.TransactionID",
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => array()
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($this->TransactionfilePath, $parserConfig);
        print_r($tempResult);
        return $tempResult;
    }

    public function testHtmlData() {
        //$filePath = DS . "var" . DS . "www" . DS . "html" . DS . "cakephp" . DS . "app" . DS . "files" . DS . "investors" . DS . "39048098ab409be490A" . DS . "20171004" . DS . "682" . DS . "twino" . DS . "amortizationtable_629331.html";
       /* echo $filePath;
        $file = fopen($filePath, 'r+');
        print_r($file);
        echo fgets($file);*/
        $parserConfig = [
            3 => [
                [
                    "type" => "amortizationtable_scheduledDate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "Y-M-D",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            4 => [
                [
                    "type" => "amortizationtable_capitalAndInterestPayment", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            5 => [
                [
                    "type" => "amortizationtable_capitalRepayment", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            6 => [
                [
                    "type" => "amortizationtable_interest", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            12 => [
                "name" => "amortizationtable_paymentStatus"
            ]
        ];

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');
        
        $myParser = new Fileparser();

        $result = $myParser->getHtmlData($this->amortizationPath, $parserConfig);
        $this->print_r2($result);
    }

}
