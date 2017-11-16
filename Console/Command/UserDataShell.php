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
 * @version 0.5
 * @date
 * @package
 */

class UserDataShell extends AppShell {
   
    public $uses = array('Userinvestmentdata', 'Investment');    
   
    /**
     * Constructor of the class
     */
    function __construct() {

    }    
    
   
   /*
    * The control variables are calculated for each "DAY", but the checking is only done for the date of the readout.
    * This means that if a reading period covers a week, the checking SHOULD be done only for the last calculation (= last day)
    * The structure of both arrays is:
    *               'myWallet'
    *               'outstandingPrincipal'
    *               'activeInvestments'
    * @param  array       array with the calculated control variables for today's readout
    * @param  array       array with the control variables as provided by platform
    * @return boolean     
    * 
    */
    public function consolidateControlVariables($externalControlVariables, $internalControlVariables) {        
        $result = false;
        $error = true;
        foreach ($externalControlVariables as $variableKey => $variable) {
             switch ($variableKey) {
                case WIN_CONTROLVARIABLE_MYWALLET:
                    if ($internalControlVariables['myWallet'] == $externalControlVariables) {
                        
                    }
                    else {
                        $error = true;
                    }
                    break;
                case WIN_CONTROLVARIABLE_OUTSTANDINGPRINCIPAL:
                    if ($internalControlVariables['outstandingPrincipal'] == $externalControlVariables) {
                    
                    }
                    else {
                        $error = true;
                    }
                    break;
                case WIN_CONTROLVARIABLE_ACTIVEINVESTMENT:
                    if ($internalControlVariables['activeInvestments'] == $externalControlVariables) {
                        
                    }
                    else {
                        $error = true;
                    }
                    break;
            }  
        }  
        if ($error == true) {
            return $result;
        }
        else {
            //generate application error
        }
        // If approved, write the new values to DB
    }
    
    
    
    public function consolidatePlatformData(&$database) {
        echo "FxF";
        $database['userinvestmentdata']['userinvestmentdata_capitalRepayment'] = $this->consolidateCapitalRepayment();  // 38
        echo "FtF";
        $database['userinvestmentdata']['userinvestmentdata_partialPrincipalRepayment'] = $this->consolidatePartialPrincipalRepayment();
        echo "FccFgF";
        $database['userinvestmentdata']['userinvestmentdata_outstandingPrincipal'] = $this->consolidateOutstandingPrincipal();  // 37
        echo "FFgF";
        $database['userinvestmentdata']['userinvestmentdata_receivedPrepayments'] = $this->consolidateReceivedPrepayment();
        echo "FtytyFgF";
        $database['userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $this->consolidateTotalGrossIncome();
        echo "FhF";
        $database['userinvestmentdata']['userinvestmentdata_interestgrossIncome'] = $this->consolidateInterestgrossIncome();
        echo "FFF";
        //        $database['userinvestmentdata']['userinvestmentdata_totalCost'] = $this->consolidateTotalCost();
        // substract the corresponding amounts of loans that have terminated
        // also using a loop
    }



    /* OK
     *  Get the amount which corresponds to the "PartialPrincipalPayment" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function consolidatePartialPrincipalRepayment() {
        $sum = 0;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_partialPrincipalRepayment'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /* var 37
     *  Get the amount which corresponds to the "OutstandingPrincipal" concept
     *  "Outstanding principal" = total amount of investment - paymenttotal_capitalRepayment
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateOutstandingPrincipal(&$transactionData, &$resultData) {
        $result = "0.0";

        if (!empty($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcsub($result,$resultData['payment']['payment_capitalRepayment'], 16); 
        }
        if (!empty($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcsub($result,$resultData['payment']['payment_partialPrincipalRepayment'], 16); 
        }  
        if (!empty($resultData['payment']['payment_principalBuyback'])) {
            $result = bcsub($result,$resultData['payment']['payment_principalBuyback'], 16); 
        }
        if (!empty($resultData['investment']['investment_priceInSecondaryMarket'])) { // read from db
            $result = bcsub($result,$resultData['investment']['investment_priceInSecondaryMarket'], 16); 
        }
        if (!empty($resultData['payment']['payment_currencyFluctuationNegative'])) {
            $result = bcsub($result,$resultData['payment']['payment_currencyFluctuationNegative'], 16);            
        }  
        if (!empty($resultData['payment']['payment_currencyFluctuationPositive'])) {
            $result = bcadd($result,$resultData['payment']['payment_currencyFluctuationPositive'], 16);   
        } 
        return $result;
    }

    /* var 38
     * Get the amount which corresponds to the "ReceivedPrepayments" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function calculateReceivedRepayment(&$transactionData, &$resultData) {
        $result = 0.0;
        if (!empty($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcadd($result,$resultData['payment']['payment_capitalRepayment'], 16);   
        }
        if (!empty($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcadd($result,$resultData['payment']['payment_partialPrincipalRepayment'], 16);   
        }
         if (!empty($resultData['payment']['payment_principalBuyback'])) {
            $result = bcadd($result,$resultData['payment']['payment_principalBuyback'], 16);   
        }
        if (!empty($resultData['investment']['investment_priceInSecondaryMarket'])) {  // read from db
            $result = bcadd($result,$resultData['investment']['investment_priceInSecondaryMarket'], 16);  
        } 
        $result1 = 0.0;
        if (!empty($resultData['investment']['investment_myInvestment'])) {  // read from db
            $result1 = bcadd($result1,$resultData['investment']['investment_myInvestment'], 16);  
        }        
        if (!empty($resultData['investment']['investment_secondaryMarketInvestment'])) {  // read from db
            $result1 = bcadd($result1,$resultData['investment']['investment_secondaryMarketInvestment'], 16);  
        }        
        $result1 = bcdiv($result,$result, 16);  
        return $result;
    }

    /*
     * Get the amount which corresponds to the "TotalGrossIncome" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function xxxconsolidateTotalGrossIncome() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_totalGrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /*
     * Get the amount which corresponds to the "InterestgrossIncome" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function consolidateInterestgrossIncome() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_interestgrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /* NOT YET
     * Get the amount which corresponds to the "TotalLoanCost" concept CHECK
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function calculateTotalLoanCost(&$transactionData, &$resultData) {
        $result = "0.0";
        
        if (!empty($resultData['payment']['payment_commissionPaid'])) {
            $result = bcadd($resultData['payment']['payment_regularGrossInterestIncome'],$result, 16);
        }
        if (!empty($resultData['globalcashflowdata']['globalcashflowdata_bankCharges'])) {
            $result = bcadd($resultData['globalcashflowdata']['payment_interestIncomeBuyback'],$result, 16);
        }        
        if (!empty($resultData['payment']['payment.payment_taxVAT'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment.payment_incomeWithholdingTax'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncomeBuyback'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment.payment_interestPaymentSecondaryMarketPurchase'])) {
            $result = bcadd($resultData['payment']['payment_latePaymentFeeIncome'],$result, 16);
        }
        if (!empty($resultData['investment']['investment_currencyExchangRateFee'])) {
            $result = bcadd($resultData['investment']['investment_currencyExchangRateFee'],$result, 16);
        }       
        if (!empty($resultData['payment']['payment.payment_costSecondaryMarket'])) {
            $result = bcadd($resultData['payment']['payment_costSecondaryMarket'],$result, 16);
        }       
        
        
        return $result;   
    }

    /* NOT YET
     * Get the amount which corresponds to the "NextPaymentDate" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function consolidateNextPaymentDate() {
        $sum = 0;
        return $sum;
    }

    /* NOT YET
     * Get the amount which corresponds to the "EstimatedNextPayment" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function consolidateEstimatedNextPayment() {
        $sum = 0;
        return $sum;
    }





    /* NOT YET
     *  Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateTotalGrossIncome(&$transactionData, &$resultData) {
        $result = 0.0;
        
        if (!empty($resultData['payment']['payment_regularGrossInterestIncome'])) {
            $result = bcadd($resultData['payment_regularGrossInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_interestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_interestIncomeBuyback'],$result, 16);
        }        
        if (!empty($resultData['payment']['payment_delayedInterestIncome'])) {
            $result = bcadd($resultData['payment_delayedInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_delayedInterestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_delayedInterestIncomeBuyback'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment_latePaymentFeeIncome'])) {
            $result = bcadd($resultData['payment_latePaymentFeeIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_loanRecoveries'])) {
            $result = bcadd($resultData['payment_loanRecoveries'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment_loanIncentivesAndBonus'])) {
            $result = bcadd($resultData['payment_loanIncentivesAndBonus'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_loanCompensation'])) {
            $result = bcadd($resultData['payment_loanCompensation'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_incomeSecondaryMarket'])) {
            $result = bcadd($resultData['payment_incomeSecondaryMarket'],$result, 16);
        }  
        return $result;   
    }
    
    
    
    
    
    
    
    
    

    /* NOT YET
     *  Get the amount which corresponds to the "InstallmentPaymentProgress" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */

    public function consolidateInstalmentPaymentProgress() {
        $sum = 0;
        return $sum;
    }

    /*
     *  Get the amount which corresponds to the "Primary_market_investment" concept, which is a new investment
     *  @param  $transactionData    array      array with the current transaction data
     *  @param  $resultData         array       array of shadow database with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     * 12
     */
    public function calculateMyInvestment(&$transactionData, &$resultData) {
        $resultData['payment']['payment_myInvestment'] = $transactionData['amount'];        // THIS IS A HARDCODED RULE
        return $transactionData['amount'];
    }   
    

    public function calculateMyInvestmentFromPayment(&$transactionData, &$resultData) {
        echo "----------------->  BBBBBBBBB\n";
        print_r($transactionData);
        return $transactionData['investment']['investment_myInvestment'];
    }

    /*
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     * 17
     */
    public function calculateRemainingTerm(&$transactionData, &$resultData) {
        return 44332211;
        return $transactionData['amount'];
        //investment.investment_remainingDuration
    }

    /*
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 47
     */
    public function calculateLatePaymentFeeIncome(&$transactionData, &$resultData) {

        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "capitalRepayment Winvestify Format" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 34
     */
    public function calculateCapitalRepayment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 45
     */
    public function calculateDelayedInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "InterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 44
     */
    public function calculateInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 36
     */
    public function calculatePrincipalBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "DelayedInterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 46
     */
    public function calculateDelayedInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /* DONE
     *  Get the amount which corresponds to the "PlatformDeposit" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 66
     */
    public function calculatePlatformDeposit(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /* DONE
     *  Get the amount which corresponds to the "Platformwithdrawal" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 67
     */
    public function calculatePlatformWithdrawal(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return string
     * 43
     */
    public function calculateRegularGrossInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     * Calculates the number of active investments. This function SHOULD be the very last function to execute
     * and will execute only once per DAY
     *
     *  @param  array       array with the current transaction data [NOT REALLY NEEDED]
     *  @param  array       array with all data so far calculated and to be written to DB [NOT REALLY NEEDED]
     *  @return string
     * 20000
     */
    public function calculateNumberOfActiveInvestments(&$transactionData, &$resultData) {
        $filterConditions = array('Investment.investment_statusOfLoan' => WIN_LOANSTATUS_ACTIVE);
        $activeInvestments = $this->Investment->find('count', array(
            'conditions' => $filterConditions));
        return $activeInvestments;
    }

    /*
     *  Get the amount which corresponds to the "PlatformbankCharges" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 55
     */
    public function calculatePlatformBankCharges(&$transactionData, &$resultData) {
        return $transactionData[0]['amount'];
    }



    /**
     * Gets the latest (=last entry in DB) data of a model table
     * @param string    $model
     * @param array     $filterConditions
     *
     * @return array with data
     *          or false if $elements do not exist in two dimensional array
     */
    public function getLatestTotals($model, $filterConditions) {

        $temp = $this->$model->find("first", array('conditions' => $filterConditions,
            'order' => array($model . '.id' => 'desc'),
            'recursive' => -1
        ));

        if (empty($temp)) {
            return false;
        }

        foreach ($temp[$model] as $key => $item) {
            $keyName = explode("_", $key);
            if (strtoupper($model) <> strtoupper($keyName[0])) {
                unset($temp[$model][$key]);
            }
        }
        return $temp;
    }

    
    
    
    
    
    
    
    
    
    /* NOT YET  chewck if the index is investment or payment
     * Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function calculateTotalNetIncome(&$transactionData, &$resultData) {
        if (empty($resultData['investment']['investment_loanTotalCost'])) {
            $resultData['investment_loanTotalCost'] = 0.0;
        }
        if (empty($resultData['investment']['investment_totalGrossIncome'])) {
            $resultData['investment_totalGrossIncome'] = 0.0;
        }        
        $result = bcsub($resultData['investment_totalGrossIncome'],$resultData['investment_loanTotalCost'], 16);
        return $result;
    }    
    



    /*
     * Get the amount which corresponds to the "InterestgrossIncome" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function calculateMyWallet() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_interestgrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }
    
    /*
     * 
     * 
     * 
     * Get the amount which corresponds to the "totalOutstandingPrincipal" concept
     * for the controlVariables check
     * 
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */
    public function calculateTotalOutstandingPrincipal() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_interestgrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }
   
    
    
}

/*
 // these are the total values per PFP

            if ($this->variablesConfig[30]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // principal and interest payment [30]
                $varName = $this->variablesConfig[30]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidatePrincipalAndInterestPayment($database);
                $this->variablesConfig[30]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[31]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // installmentPaymentProgress [31]
                $varName = $this->variablesConfig[31]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateInstallmentPaymentProgress($database);
                $this->variablesConfig[31]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[34]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // capital repayment (34)
                $varName = $this->variablesConfig[34]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateCapitalRepayment($database);
                $this->variablesConfig[34]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[35]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // partial principal payment(35
                $varName = $this->variablesConfig[35]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidatePartialPrincipalPayment($database);
                $this->variablesConfig[35]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[37]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // outstanding principal (37)
                $varName = $this->variablesConfig[37]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateOutstandingPrincipal($database);
                $this->variablesConfig[37]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[38]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // received repayments( 38)
                $varName = $this->variablesConfig[38]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateReceivedPrepayments($database);
                $this->variablesConfig[38]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[42]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // total gross income (42
                $varName = $this->variablesConfig[42]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateTotalGrossIncome($database);
                $this->variablesConfig[42]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[43]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // interest gross income (43)
                $varName = $this->variablesConfig[43]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateInterestgrossIncome($database);
                $this->variablesConfig[43]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // total cost (53)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateTotalCost($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // next payment date (39)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateNextPaymentDate($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // estimated next payment (40)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateEstimatedNextPayment($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

 */
    
    
        

?>