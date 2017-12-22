<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.1
 * @date
 * @package
 */

/**
 * Class to keep Winvestify formulas to calculate Yield, outstanding
 *
 */
class WinFormulas {
    
    protected $variablesFormula_A = [
        "A" => [
            "type" => "userinvestmentdata_totalGrossIncome",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive"
        ],
        "B" => [
            "type" => "userinvestmentdata_totalLoansCost",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive"
        ],
        "C" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive"
        ]
    ];
    
    protected $configFormula_A = [
        "functionName" => "stepByStep",
        "steps" => [
            ["A"],
            ["B", "substract"],
            ["C", "divide"],
            [1, "add"],
            [365, "pow"],
            [1, "substract"]
        ],
        "result" => [
            "type" => "userinvestmentdata_netAnualReturnPast12Months",
            "table" => "Userinvestmentdata"
        ]
    ];
    
    protected $variablesFormula_A_xirr = [
        "A" => [
            "type" => [
                "variables" => [
                    "userinvestmentdata_cashInPlatform",
                    "userinvestmentdata_totalNetDeposits",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "B" => [
            "type" => "userinvestmentdata_totalLoansCost",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive",
            "operation" => "substract"
        ],
        "C" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-1",
            "intervals" => "inclusive",
            "operation" => "substract"
        ]
    ];
    
    protected $variablesFormula_B = [
        "A" => [
            [
                "type" => "userinvestmentdata_totalGrossIncome",
                "table" => "Userinvestmentdata",
                "dateInit" => [
                    "year" => -1,
                    "month" => "1",
                    "day" => "1"
                ],
                "dateFinish" => [
                    "year" => -1,
                    "month" => "12",
                    "day" => "31"
                ],
                "intervals" => "inclusive"
            ],
            [
                "type" => "userinvestmentdata_totalLoansCost",
                "table" => "Userinvestmentdata",
                "operation" => "substract",
                "dateInit" => [
                    "year" => -1,
                    "month" => "1",
                    "day" => "1"
                ],
                "dateFinish" => [
                    "year" => -1,
                    "month" => "12",
                    "day" => "31"
                ],
                "intervals" => "inclusive"
            ]
        ],
        "B" => [
            [
                "type" => "userinvestmentdata_outstandingPrincipal",
                "table" => "userinvestmentdata",
                "operation" => "add",
                "dateInit" => [
                    "year" => -1,
                    "month" => "1",
                    "day" => "1"
                ],
                "dateFinish" => [
                    "year" => -1,
                    "month" => "12",
                    "day" => "31"
                ],
                "intervals" => "inclusive"
            ]
        ]
    ];
    
    protected $configFormula_B = [
        "steps" => [
            ["A"],
            ["B", "divide"],
            [1, "add"],
            [365, "pow"],
            [1, "substract"]
        ],
        "result" => [
            "type" => "userinvestmentdata_netAnualReturnPastYear",
            "table" => "Userinvestmentdata"
        ]
    ];
    
    protected $variablesFormula_C = [
        
    ];
    
    public function doOperationByType($inputA, $inputB, $type) {
        
        switch ($type) {
            case "add":
                $result = $this->addTwoValues($inputA, $inputB);
                break;
            case "substract":
                $result = $this->subtractTwoValues($inputA, $inputB);
                break;
            case "divide":
                $result = $this->divideTwoValues($inputA, $inputB);
                break;
            case "multiply":
                $result = $this->multiplyTwoValues($inputA, $inputB);
                break;
            case "pow":
                $result = $this->powTwoValues($inputA, $inputB);
                break;
            default:
                $result = $inputB;
        }
        return $result;
    }
    
    public function addTwoValues($inputA, $inputB) {
        return bcadd($inputA, $inputB, 16);
    }
    
    public function subtractTwoValues($inputA, $inputB) {
        return bcsub($inputA, $inputB, 16);
    } 
    
    public function divideTwoValues($inputA, $inputB) {
        return bcdiv($inputA, $inputB, 16);
    } 
    
    public function multiplyTwoValues($inputA, $inputB) {
        return bcmul($inputA, $inputB, 16);
    } 
    
    public function powTwoValues($inputA, $inputB) {
        return bcpow($inputA, $inputB, 16);
    }
    
    public function getFormulaParams($type) {
        
        switch($type) {
            case "formula_A":
                return $this->variablesFormula_A;
            case "formula_B":
                return $this->variablesFormula_B;
            case "formula_A_xirr":
                return $this->variablesFormula_A_xirr;
        }
    }
    
    public function getFormula($type) {
        
        switch($type) {
            case "formula_A":
                return $this->configFormula_A;
            case "formula_B":
                return $this->configFormula_B;
        }
    }
    
}
