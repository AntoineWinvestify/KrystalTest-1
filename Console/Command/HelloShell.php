<?php

class HelloShell extends AppShell {

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

    /**
     * Analyze testing
     */
    public function testParserAnalyze() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "A" => [
                "name" => "investment.investment_country"                               // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
            "C" => [
                [
                    "type" => "investment.investment_issueDate", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "D" => [
                "name" => "investment.loanType"                                         // Winvestify standardized name   OK
            ],
            "E" => [
                "name" => "investment.amortizationMethod"                               // Winvestify standardized name  OK
            ],
            "F" => [
                "name" => "investment.loanOriginator"                                   // Winvestify standardized name  OK
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "investment.remainingPrincipalTotalLoan", // THIS FIELD IS NOT NEEDED?
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "I" => [
                [
                    "type" => "investment.nextPaymentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "J" => [
                [
                    "type" => "investment.nextPaymentAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "K" => [
                "name" => "investment.LTV"                                              // Winvestify standardized name   OK
            ],
            "L" => [
                [
                    "type" => "investment.nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "M" => [
                "name" => "investment_totalInstalments"                                 // Winvestify standardized name
            ],
            "N" => [
                "name" => "investment_paidInstalments"                                  // Winvestify standardized name
            ],
            "O" => [
                "name" => "investment_loanStatus"                                       // Winvestify standardized name
            ],
            "P" => [
                "name" => "investment.buyBackGuarantee"                                 // Winvestify standardized name  OK
            ],
            "Q" => [
                [
                    "type" => "investment.investment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_paidInstalmentsProgressTwo", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "#current.investment_paidInstalments",
                        "input3" => "#current.investment_totalInstalments",
                        "input4" => 0                                           // Number of decimals
                    ],
                    "functionName" => "DivisionInPercentage",
                ],
            ],
            "R" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "S" => [
                [
                    "type" => "investment.paymentsDone", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "T" => [
                [
                    "type" => "investment.outstandingPrincipal", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "U" => [
                [
                    "type" => "investment.amountSecondaryMarket", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
            "W" => [
                [
                    "type" => "investment.discount_premium", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);

        print_r($tempResult);
    }

    /**
     * Get & Set config testing
     */
    public function testParserConfig() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "A" => [
                "name" => "investment.investment_country"                               // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
            "C" => [
                [
                    "type" => "investment.investment_issueDate", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "D" => [
                "name" => "investment.loanType"                                         // Winvestify standardized name   OK
            ],
            "E" => [
                "name" => "investment.amortizationMethod"                               // Winvestify standardized name  OK
            ],
            "F" => [
                "name" => "investment.loanOriginator"                                   // Winvestify standardized name  OK
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "investment.remainingPrincipalTotalLoan", // THIS FIELD IS NOT NEEDED?
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "I" => [
                [
                    "type" => "investment.nextPaymentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "J" => [
                [
                    "type" => "investment.nextPaymentAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "K" => [
                "name" => "investment.LTV"                                              // Winvestify standardized name   OK
            ],
            "L" => [
                [
                    "type" => "investment.nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "M" => [
                "name" => "investment_totalInstalments"                                 // Winvestify standardized name
            ],
            "N" => [
                "name" => "investment_paidInstalments"                                  // Winvestify standardized name
            ],
            "O" => [
                "name" => "investment_loanStatus"                                       // Winvestify standardized name
            ],
            "P" => [
                "name" => "investment.buyBackGuarantee"                                 // Winvestify standardized name  OK
            ],
            "Q" => [
                [
                    "type" => "investment.investment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_paidInstalmentsProgressTwo", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "#current.investment_paidInstalments",
                        "input3" => "#current.investment_totalInstalments",
                        "input4" => 0                                           // Number of decimals
                    ],
                    "functionName" => "DivisionInPercentage",
                ],
            ],
            "R" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "S" => [
                [
                    "type" => "investment.paymentsDone", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "T" => [
                [
                    "type" => "investment.outstandingPrincipal", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "U" => [
                [
                    "type" => "investment.amountSecondaryMarket", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
            "W" => [
                [
                    "type" => "investment.discount_premium", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $tempResult = $myParser->getConfig();
        print_r($tempResult);
    }

    /**
     * Analyze and set/get config testing
     */
    public function testParserAnalyzeAndConfig() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "A" => [
                "name" => "investment.investment_country"                               // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment.investment_loanId"                                // Winvestify standardized name  OK
            ],
            "C" => [
                [
                    "type" => "investment.investment_issueDate", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "D" => [
                "name" => "investment.loanType"                                         // Winvestify standardized name   OK
            ],
            "E" => [
                "name" => "investment.amortizationMethod"                               // Winvestify standardized name  OK
            ],
            "F" => [
                "name" => "investment.loanOriginator"                                   // Winvestify standardized name  OK
            ],
            "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "investment.remainingPrincipalTotalLoan", // THIS FIELD IS NOT NEEDED?
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "I" => [
                [
                    "type" => "investment.nextPaymentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "J" => [
                [
                    "type" => "investment.nextPaymentAmount", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "K" => [
                "name" => "investment.LTV"                                              // Winvestify standardized name   OK
            ],
            "L" => [
                [
                    "type" => "investment.nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "M" => [
                "name" => "investment_totalInstalments"                                 // Winvestify standardized name
            ],
            "N" => [
                "name" => "investment_paidInstalments"                                  // Winvestify standardized name
            ],
            "O" => [
                "name" => "investment_loanStatus"                                       // Winvestify standardized name
            ],
            "P" => [
                "name" => "investment.buyBackGuarantee"                                 // Winvestify standardized name  OK
            ],
            "Q" => [
                [
                    "type" => "investment.investment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_paidInstalmentsProgressTwo", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "#current.investment_paidInstalments",
                        "input3" => "#current.investment_totalInstalments",
                        "input4" => 0                                           // Number of decimals
                    ],
                    "functionName" => "DivisionInPercentage",
                ],
            ],
            "R" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "S" => [
                [
                    "type" => "investment.paymentsDone", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "T" => [
                [
                    "type" => "investment.outstandingPrincipal", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "U" => [
                [
                    "type" => "investment.amountSecondaryMarket", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
            "W" => [
                [
                    "type" => "investment.discount_premium", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
                    
    
    
    
    
    
    
    public function testParserConfigFormat1() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "B" => [
                "name" => "transactionId"
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    public function testParserConfigFormat2() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "B" => [
                "name" => "transactionId"
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    public function testParserConfigFormat3() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "R" => [
                [
                    "type" => "investment.investmentDate", // Winvestify standardized name
                    "inputData" => [
                        "input2" => "D.M.Y",
                    ],
                    "functionName" => "normalizeDate",]
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
            'sortParameter' => "investment.investment_loanId",
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    public function testParserConfigFormat4() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    
    
    
    
    
    
    
    public function testError1() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    
    
    
    
    
    
    
    
    
    
    
    public function testDate1() { // D-M-Y

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
    public function testDate2() { // mm-dd-YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate3() { // MM-DD-YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate4() { // YYYY.MM.DD
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate5() { // YYYY.mm.dd
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate6() { // YYYY.DD.MM
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate7() { // dd/mm/YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
     public function testDate8() { // DD/MM/YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate9() { // mm/dd/YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testDate10() { // MM/DD/YYYY
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
    
        public function testCurrency() { 
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
    
    public function testAmount1() { // format 0,00453
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
        "G" => [
                [
                    "type" => "investment.fullLoanAmount", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
      public function testAmount2() { // format 2.400,5548
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testAmount3() { // format €24005,000995
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
     public function testAmount4() { // format €2.545,442424
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
    public function testAmount5() { // format 2.566,8778433868774
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    public function testAmount6() { // format 2500,45214€
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
    
    
    
     public function testExtracData() { // format 2500,45214€
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
     public function testHash() { // format 2500,45214€
        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
     public function testDivision() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

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
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }

    
    public function testTransactionDetail() {

        $filePath = DS . 'home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'my-investments(1).xlsx';
        echo $filePath;

        $parserConfig = [
            "D" => [
                [
                    "type" => "transactionDetail",                                      // Winvestify standardized name   OK
                    "inputData" => [                                                    // List of all concepts that the platform can generate
                                                                                        // format ["concept string platform", "concept string Winvestify"]
                                   "input8" => ["Incoming client payment" => "Cash_deposit",
                                                "Investment principal increase" => "Primary_market_investment",
                                                "Investment share buyer pays to a seller" => "Investment",
                                                "Investment principal repayment" => "Principal_repayment",
                                                "Investment principal rebuy" => "Principal_buyback",
                                                "Interest income" => "Regular_gross_interest_income",
                                                "Delayed interest income" => "Delayed_interest_income",
                                                "Late payment fee income" =>"Late_payment_fee_income",

                                                "Interest income on rebuy" => "Interest_income_buyback",
                                                "Delayed interest income on rebuy" => "Delayed_interest_income_buyback",
                                                "Disc/premum paid secondary market" => "Income"]
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
            'sortParameter' => "investment.investment_loanId",
            'OffsetStart' => 1,
            'offsetEnd' => 2,
        ));
        $myParser->getConfig();
        $tempResult = $myParser->analyzeFile($filePath, $parserConfig);
        print_r($tempResult);
    }
    
    
}

?>