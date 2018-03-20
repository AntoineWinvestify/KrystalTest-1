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
            ["B", "subtract"],
            ["C", "divide"],
            [1, "add"],
            [365, "pow"],
            [1, "subtract"]
        ],
        "result" => [
            "type" => "userinvestmentdata_netAnualReturnPast12Months",
            "table" => "Userinvestmentdata"
        ]
    ];
    
    protected $variablesFormula_netAnnualReturn_xirr = [
        "A" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_interestIncomeBuybackPerDay",             //Finanzarel
                    "globaltotalsdata_delayedInterestIncomePerDay",             //Finanzarel
                    "globaltotalsdata_capitalRepaymentPerDay",                  //Finanzarel
                    "globaltotalsdata_partialPrincipalRepaymentPerDay",         //Finanzarel
                    "globaltotalsdata_regularGrossInterestIncomePerDay",         //Finanzarel
                    "globaltotalsdata_interestGrossIncomePerDay",
                    "globaltotalsdata_delayedInterestIncomeBuybackPerDay",
                    "globaltotalsdata_latePaymentFeeIncomePerDay",
                    "globaltotalsdata_loanRecoveriesPerDay",
                    "globaltotalsdata_loanIncentivesAndBonusPerDay",
                    "globaltotalsdata_loanCompensationPerDay",
                    "globaltotalsdata_incomeSecondaryMarket",
                    "globaltotalsdata_principalBuybackPerDay",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "B" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_myInvestmentPerDay",
                    "globaltotalsdata_costSecondaryMarketPerDay",
                    "globaltotalsdata_secondaryMarketInvestmentPerDay",
                    "globaltotalsdata_commissionPaidPerDay",
                    "globaltotalsdata_taxVATPerDay",
                    "globaltotalsdata_currencyExchangeFeePerDay",
                    "globaltotalsdata_currencyExchangeTransactionPerDay",
                    "globaltotalsdata_writtenOff",
                    "globaltotalsdata_incomeWithholdingTaxPerDay"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        "C" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "Userinvestmentdata",
            "dateInit" => "-0",
            "dateFinish" => "-0",
            "intervals" => "latest",
            "operation" => "add"
        ],
        "D" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "Userinvestmentdata",
            "dateInit" => "-366",
            "dateFinish" => "-366",
            "intervals" => "latest",
            "operation" => "subtract"
        ],
        "E" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_platformCompensationPositive",            
                    "globalcashflowdata_platformRecoveries",            
                    "globalcashflowdata_platformIncentivesAndBonus",                  
                    "globalcashflowdata_regularGrossInterestIncome",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
    ];
    
    protected $variablesFormula_netAnnualTotalFundsReturn_xirr = [
        "A" => [
            "type" => [
                "variables" => [
                    "userinvestmentdata_outstandingPrincipal",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Userinvestmentdata",
            "dateInit" => "-0",
            "dateFinish" => "-0",
            "intervals" => "latest",
            "operation" => "add"
        ],
        "B" => [
            "type" => [
                "variables" => [
                    "userinvestmentdata_outstandingPrincipal",
                    //"userinvestmentdata_cashInPlatform",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Userinvestmentdata",
            "dateInit" => "-365",
            "dateFinish" => "-365",
            "intervals" => "latest",
            "operation" => "subtract"
        ],
        "C" => [
            "type" => [
                "variables" => [
                    "userinvestmentdata_cashInPlatform",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Userinvestmentdata",
            "dateInit" => "-0",
            "dateFinish" => "-0",
            "intervals" => "latest",
            "operation" => "add"
        ],
        "D" => [
            "type" => [
                "variables" => [
                    "userinvestmentdata_cashInPlatform",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Userinvestmentdata",
            "dateInit" => "-365",
            "dateFinish" => "-365",
            "intervals" => "latest",
            "operation" => "subtract"
        ],
        "E" => [
            "type" => [
                "variables" => [        
                    "globalcashflowdata_platformWithdrawals",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "F" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_platformDeposits",            
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
    ];
    
    protected $variablesFormula_netAnnualPastReturn_xirr = [
        "A" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_interestIncomeBuybackPerDay",             //Finanzarel
                    "globaltotalsdata_delayedInterestIncomePerDay",             //Finanzarel
                    "globaltotalsdata_capitalRepaymentPerDay",                  //Finanzarel
                    "globaltotalsdata_partialPrincipalRepaymentPerDay",         //Finanzarel
                    "globaltotalsdata_regularGrossInterestIncomePerDay",         //Finanzarel
                    "globaltotalsdata_interestGrossIncomePerDay",
                    "globaltotalsdata_delayedInterestIncomeBuybackPerDay",
                    "globaltotalsdata_latePaymentFeeIncomePerDay",
                    "globaltotalsdata_loanRecoveriesPerDay",
                    "globaltotalsdata_loanIncentivesAndBonusPerDay",
                    "globaltotalsdata_loanCompensationPerDay",
                    "globaltotalsdata_incomeSecondaryMarket",
                    "globaltotalsdata_principalBuybackPerDay",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "B" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_myInvestmentPerDay",
                    "globaltotalsdata_costSecondaryMarketPerDay",
                    "globaltotalsdata_secondaryMarketInvestmentPerDay",
                    "globaltotalsdata_commissionPaidPerDay",
                    "globaltotalsdata_taxVATPerDay",
                    "globaltotalsdata_currencyExchangeFeePerDay",
                    "globaltotalsdata_currencyExchangeTransactionPerDay",
                    "globaltotalsdata_writtenOff",
                    "globaltotalsdata_incomeWithholdingTaxPerDay"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        "C" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "userinvestmentdata",
            "operation" => "add",
            "dateInit" => [
                "month" => "12",
                "day" => "31"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "latest",
            "operation" => "add"
        ],
        "D" => [
            "type" => "userinvestmentdata_outstandingPrincipal",
            "table" => "userinvestmentdata",
            "operation" => "add",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "1",
                "day" => "1"
            ],
            "intervals" => "latest",
            "operation" => "subtract"
        ],
        "E" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_platformCompensationPositive",            
                    "globalcashflowdata_platformRecoveries",            
                    "globalcashflowdata_platformIncentivesAndBonus",                  
                    "globalcashflowdata_regularGrossInterestIncome",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
    ];
    
    protected $variablesFormula_netReturn = [
        //A is for loans calculations in globaltotalsdata
        "A" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_interestIncomeBuybackPerDay",             //Finanzarel
                    "globaltotalsdata_delayedInterestIncomePerDay",               //Finanzarel
                    "globaltotalsdata_regularGrossInterestIncomePerDay",         //Finanzarel
                    "globaltotalsdata_interestGrossIncomePerDay",
                    "globaltotalsdata_delayedInterestIncomeBuybackPerDay",
                    "globaltotalsdata_latePaymentFeeIncomePerDay",
                    "globaltotalsdata_loanRecoveriesPerDay",
                    "globaltotalsdata_loanIncentivesAndBonusPerDay",
                    "globaltotalsdata_loanCompensationPerDay",
                    "globaltotalsdata_incomeSecondaryMarket",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "B" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_platformRecoveries",             //Finanzarel
                    "globalcashflowdata_platformIncentivesAndBonus",             //Finanzarel
                    "globalcashflowdata_platformCompensationPositive",             //Finanzarel
                    //
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "C" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_costSecondaryMarketPerDay",
                    "globaltotalsdata_commissionPaidPerDay",
                    "globaltotalsdata_taxVATPerDay",
                    "globaltotalsdata_currencyExchangeFeePerDay",
                    "globaltotalsdata_incomeWithholdingTaxPerDay",
                    "globaltotalsdata_writtenOff"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        "D" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_bankCharges",             //Finanzarel
                    "globalcashflowdata_currencyExchangeFee",             //Finanzarel
                    "globalcashflowdata_TaxVat",             //Finanzarel
                    //"globalcashflowdata_TaxIncomeWithholdingTax"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => "-365",
            "dateFinish" => "-0",
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        /*"E" => [
            "type" => "userinvestmentdata_writtenOff",
            "table" => "Userinvestmentdata",
            "dateInit" => "-0",
            "dateFinish" => "-0",
            "intervals" => "latest",
            "operation" => "subtract"
        ]*/
    ];
    
    protected $variablesFormula_netPastReturn = [
        //A is for loans calculations in globaltotalsdata
        "A" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_interestIncomeBuybackPerDay",             //Finanzarel
                    "globaltotalsdata_delayedInterestIncomePerDay",             //Finanzarel
                    "globaltotalsdata_regularGrossInterestIncomePerDay",         //Finanzarel
                    "globaltotalsdata_interestGrossIncomePerDay",
                    "globaltotalsdata_delayedInterestIncomeBuybackPerDay",
                    "globaltotalsdata_latePaymentFeeIncomePerDay",
                    "globaltotalsdata_loanRecoveriesPerDay",
                    "globaltotalsdata_loanIncentivesAndBonusPerDay",
                    "globaltotalsdata_loanCompensationPerDay",
                    "globaltotalsdata_incomeSecondaryMarket",
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "B" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_platformRecoveries",             
                    "globalcashflowdata_platformIncentivesAndBonus",             
                    "globalcashflowdata_platformCompensationPositive",             
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "add"
        ],
        "C" => [
            "type" => [
                "variables" => [
                    "globaltotalsdata_costSecondaryMarketPerDay",
                    "globaltotalsdata_commissionPaidPerDay",
                    "globaltotalsdata_taxVATPerDay",
                    "globaltotalsdata_currencyExchangeFeePerDay",
                    "globaltotalsdata_incomeWithholdingTaxPerDay",
                    "globaltotalsdata_writtenOff"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globaltotalsdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        "D" => [
            "type" => [
                "variables" => [
                    "globalcashflowdata_bankCharges",             //Finanzarel
                    "globalcashflowdata_currencyExchangeFee",             //Finanzarel
                    "globalcashflowdata_TaxVat",             //Finanzarel
                    //"globalcashflowdata_TaxIncomeWithholdingTax"
                    //need more data to take values from database
                ],
                "operation" => "add"
            ],
            "table" => "Globalcashflowdata",
            "dateInit" => [
                "month" => "1",
                "day" => "1"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "inclusive",
            "operation" => "subtract"
        ],
        /*"E" => [
            "type" => "userinvestmentdata_writtenOff",
            "table" => "Userinvestmentdata",
            "dateInit" => [
                "month" => "12",
                "day" => "31"
            ],
            "dateFinish" => [
                "month" => "12",
                "day" => "31"
            ],
            "intervals" => "latest",
            "operation" => "subtract"
        ]*/
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
                "operation" => "subtract",
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
            [1, "subtract"]
        ],
        "result" => [
            "type" => "userinvestmentdata_netAnualReturnPastYear",
            "table" => "Userinvestmentdata"
        ]
    ];
    
    protected $variablesFormula_C = [
        
    ];
    
    /**
     * Function to make an operation depending of its type
     * @param string $inputA Value for inputA
     * @param string $inputB Value for inputB
     * @param string $type Type of operation
     * @return string
     */
    public function doOperationByType($inputA, $inputB, $type) {
        
        switch ($type) {
            case "add":
                $result = $this->addTwoValues($inputA, $inputB);
                break;
            case "subtract":
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
    
    /**
     * Function to add two values
     * @param string $inputA Value for inputA
     * @param string $inputB Value for inputB
     * @return string
     */
    public function addTwoValues($inputA, $inputB) {
        return bcadd($inputA, $inputB, 16);
    }
    
    /**
     * Function to subtract two values
     * @param string $inputA Value for inputA
     * @param string $inputB Value for inputB
     * @return string
     */
    public function subtractTwoValues($inputA, $inputB) {
        return bcsub($inputA, $inputB, 16);
    } 
    
    /**
     * Function to divide two values
     * @param string $inputA Value for inputA
     * @param string $inputB Value for inputB
     * @return string
     */
    public function divideTwoValues($inputA, $inputB) {
        return bcdiv($inputA, $inputB, 16);
    } 
    
    /**
     * Function to multiply two values
     * @param string $inputA Value for inputA
     * @param string $inputB Value for inputB
     * @return string
     */
    public function multiplyTwoValues($inputA, $inputB) {
        return bcmul($inputA, $inputB, 16);
    } 
    
    /**
     * Function to elevate one value by another value
     * @param string $inputA Value to be elevate
     * @param string $inputB Value pow
     * @return string
     */
    public function powTwoValues($inputA, $inputB) {
        return bcpow($inputA, $inputB, 16);
    }
    
    /**
     * Function to return the variable in function of a type
     * @param string $type Name of the variable
     * @return array
     */
    public function getFormulaParams($type) {
        $variableName = "variablesFormula_" . $type;  
        return $this->$variableName;
    }
    
    /**
     * Function to get the variable in function of a type
     * @param string $type Name of the variable
     * @return array
     */
    public function getFormula($type) {
        
        switch($type) {
            case "formula_A":
                return $this->configFormula_A;
            case "formula_B":
                return $this->configFormula_B;
        }
    }
    
}
