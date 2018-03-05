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
 * @version 0.2
 * @date 2017-12-22
 * @package
 * 
 * 
 * 
 * Pending:
 * methods to read the next payment date, next payment amount and numberOfPaymentDelay
 * 
 */
class UserDataShell extends AppShell {

    public $uses = array('Userinvestmentdata', 'Investment');

    /**
     * Constructor of the class
     */
    function __construct() {
        
    }

    /** not finished yet
     * Internally the control variables are calculated for each "DAY", but the checking is only done for 
     * the date of the readout. This means that if a reading period covers a week, the checking SHOULD be done 
     * only for the last calculation (= last day).
     * The structure of both arrays is:
     *     controlVariable['myWallet']
     *                    ['outstandingPrincipal']
     *                    ['activeInvestments']
     * 
     * @param  array       array with the calculated control variables for today's readout
     * @param  array       array with the control variables as provided by platform
     * @return integer     0 OK
     *                     integer: Error Number
     * 
     */
    public function consolidatePlatformControlVariables($externalControlVariables, $internalControlVariables) {
        $error = 0;
        echo "external values = \n";
        print_r($externalControlVariables);
        echo "\ninternal values = \n";
        print_r($internalControlVariables);
        foreach ($externalControlVariables as $variableKey => $variable) {
            switch ($variableKey) {
                case WIN_CONTROLVARIABLE_MYWALLET:
                    if ($internalControlVariables['myWallet'] <> $externalControlVariables['myWallet'] ) {
                        $error = $error + WIN_ERROR_CONTROLVARIABLE_CASH_IN_PLATFORM;
                    }
                    break;
                case WIN_CONTROLVARIABLE_OUTSTANDINGPRINCIPAL:
                    if ($internalControlVariables['outstandingPrincipal'] <> $externalControlVariables['outstandingPrincipal'] ) {
                        $error = $error + WIN_ERROR_CONTROLVARIABLE_OUTSTANDING_PRINCIPAL;
                    }
                    break;
                case WIN_CONTROLVARIABLE_ACTIVEINVESTMENT:
                    if ($internalControlVariables['activeInvestments'] <> $externalControlVariables['activeInvestments'] ) {
                        $error = $error + WIN_ERROR_CONTROLVARIABLE_ACTIVE_INVESTMENTS;
                    }
                    break;
            }
        }          
        return $error;
    }


    /**
     * STILL PENDING
     * 
     *  @param type $database
     *  @return type
     */     
    public function consolidatePlatformData(&$database) {
        return;
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

    /** OK
     *  Get the amount which corresponds to the "PartialPrincipalPayment" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculatePartialPrincipalBuyback() {
        $this->Paymenttotal = ClassRegistry::init('Paymenttotal');
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

    /** STILL NOT FINISHED
     *  Get the amount which corresponds to the "OutstandingPrincipal" concept. 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateOutstandingPrincipal(&$transactionData, &$resultData) {

        $result = $resultData['investment']['investment_outstandingPrincipal'];     // in case more slices were bought of same loan

        /*if (isset($resultData['payment']['payment_myInvestment'])) {
            $result = bcadd($result, $resultData['payment']['payment_myInvestment'], 16);
        }*/
        if (isset($resultData['payment']['payment_secondaryMarketInvestment'])) {
            $result = bcadd($result, $resultData['payment']['payment_secondaryMarketInvestment'], 16);
        }
        if (isset($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcsub($result, $resultData['payment']['payment_capitalRepayment'], 16);
        }
        if (isset($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcsub($result, $resultData['payment']['payment_partialPrincipalRepayment'], 16);
        }
        if (isset($resultData['payment']['payment_principalBuyback'])) {
            $result = bcsub($result, $resultData['payment']['payment_principalBuyback'], 16);
        }
        if (isset($resultData['investment']['investment_priceInSecondaryMarket'])) {
            $result = bcsub($result, $resultData['investment']['investment_priceInSecondaryMarket'], 16);
        }
        if (isset($resultData['payment']['payment_currencyFluctuationNegative'])) {
            $result = bcsub($result, $resultData['payment']['payment_currencyFluctuationNegative'], 16);
        }
        if (isset($resultData['payment']['payment_currencyFluctuationPositive'])) {
            $result = bcadd($result, $resultData['payment']['payment_currencyFluctuationPositive'], 16);
        }
        /*if (isset($resultData['payment']['payment_disinvestment'])) {
            $result = bcsub($result, $resultData['payment']['payment_disinvestment'], 16);
        }*/
        return $result;
    }

    /** var 38 not fully tested
     *  Get the amount which corresponds to the "ReceivedPrepayments" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateReceivedRepayment(&$transactionData, &$resultData) {
        $result = '0.0';
        if (isset($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcadd($result, $resultData['payment']['payment_capitalRepayment'], 16);
        }
        if (isset($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcadd($result, $resultData['payment']['payment_partialPrincipalRepayment'], 16);
        }
        if (isset($resultData['payment']['payment_principalBuyback'])) {
            $result = bcadd($result, $resultData['payment']['payment_principalBuyback'], 16);
        }
        if (isset($resultData['investment']['investment_priceInSecondaryMarket'])) {  // read from db
            $result = bcadd($result, $resultData['investment']['investment_priceInSecondaryMarket'], 16);
        }
        $result1 = '0.0';
        if (isset($resultData['investment']['investment_myInvestment'])) {  // read from db
            $result1 = bcadd($result1, $resultData['investment']['investment_myInvestment'], 16);
        }
        if (isset($resultData['investment']['investment_secondaryMarketInvestment'])) {  // read from db
            $result1 = bcadd($result1, $resultData['investment']['investment_secondaryMarketInvestment'], 16);
        }
        $result1 = bcdiv($result, $result, 16);
        return $result;
    }

    /**
     *  Get the amount which corresponds to the "TotalGrossIncome" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
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

    /**
     *  Get the amount which corresponds to the "InterestgrossIncome" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
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

    /** NOT YET
     *  Get the amount which corresponds to the "TotalLoanCost" concept CHECK
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateTotalLoanCost(&$transactionData, &$resultData) {
        $result = 0.0;

        if (isset($resultData['payment']['payment_commissionPaid'])) {
            $result = bcadd($resultData['payment']['payment_regularGrossInterestIncome'], $result, 16);
        }
        if (isset($resultData['globalcashflowdata']['globalcashflowdata_bankCharges'])) {
            $result = bcadd($resultData['globalcashflowdata']['payment_interestIncomeBuyback'], $result, 16);
        }
        if (isset($resultData['payment']['payment.payment_taxVAT'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncome'], $result, 16);
        }
        if (isset($resultData['payment']['payment.payment_incomeWithholdingTax'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncomeBuyback'], $result, 16);
        }
        if (isset($resultData['payment']['payment.payment_interestPaymentSecondaryMarketPurchase'])) {
            $result = bcadd($resultData['payment']['payment_latePaymentFeeIncome'], $result, 16);
        }
        if (isset($resultData['investment']['investment_currencyExchangRateFee'])) {
            $result = bcadd($resultData['investment']['investment_currencyExchangRateFee'], $result, 16);
        }
        if (isset($resultData['payment']['payment.payment_costSecondaryMarket'])) {
            $result = bcadd($resultData['payment']['payment_costSecondaryMarket'], $result, 16);
        }
        return $result;
    }

    /** NOT YET
     *  Get the amount which corresponds to the "NextPaymentDate" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function consolidateNextPaymentDate() {
        $sum = 0;
        return $sum;
    }

    /** NOT YET
     *  Get the amount which corresponds to the "EstimatedNextPayment" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function consolidateEstimatedNextPayment() {
        $sum = 0;
        return $sum;
    }

    /** NOT YET
     *  Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateTotalGrossIncome(&$transactionData, &$resultData) {
        $result = 0.0;

        if (isset($resultData['payment']['payment_regularGrossInterestIncome'])) {
            $result = bcadd($resultData['payment_regularGrossInterestIncome'], $result, 16);
        }
        if (isset($resultData['payment']['payment_interestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_interestIncomeBuyback'], $result, 16);
        }
        if (isset($resultData['payment']['payment_delayedInterestIncome'])) {
            $result = bcadd($resultData['payment_delayedInterestIncome'], $result, 16);
        }
        if (isset($resultData['payment']['payment_delayedInterestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_delayedInterestIncomeBuyback'], $result, 16);
        }
        if (isset($resultData['payment']['payment_latePaymentFeeIncome'])) {
            $result = bcadd($resultData['payment_latePaymentFeeIncome'], $result, 16);
        }
        if (isset($resultData['payment']['payment_loanRecoveries'])) {
            $result = bcadd($resultData['payment_loanRecoveries'], $result, 16);
        }
        if (isset($resultData['payment']['payment_loanIncentivesAndBonus'])) {
            $result = bcadd($resultData['payment_loanIncentivesAndBonus'], $result, 16);
        }
        if (isset($resultData['payment']['payment_loanCompensation'])) {
            $result = bcadd($resultData['payment_loanCompensation'], $result, 16);
        }
        if (isset($resultData['payment']['payment_incomeSecondaryMarket'])) {
            $result = bcadd($resultData['payment_incomeSecondaryMarket'], $result, 16);
        }
        return $result;
    }

    /** NOT YET
     *  Get the amount which corresponds to the "InstallmentPaymentProgress" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function consolidateInstalmentPaymentProgress() {
        $sum = 0;
        return $sum;
    }

    /**
     *  Get the amount which corresponds to the "Primary_market_investment" concept, which is a new investment
     * 
     *  @param  $transactionData    array      array with the current transaction data
     *  @param  $resultData         array       array of shadow database with all data so far calculated and to be written to DB
     *  @return string      the string representation of a float
     * 12
     */
    public function calculateMyInvestment(&$transactionData, &$resultData) {
        if (empty($resultData['investment']['investment_loanId']) && empty($resultData['investment']['investment_sliceIdentifier'])) {
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'] = bcadd($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'],$transactionData['amount'], 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = bcsub($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $transactionData['amount'], 16);
            return;
        }
        else {
            return $transactionData['amount'];
        }
        
    }

    /**
     * STILL PENDING
     * 
     * @param type $database
     * @return type
     */    
    public function calculateMyInvestmentFromPayment(&$transactionData, &$resultData) {
        echo "----------------->  BBBBBBBBB\n";
        print_r($transactionData);
        return $transactionData['investment']['investment_myInvestment'];
    }
    
    /**
     *  Get the amount which corresponds to the "Remaining Term" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     * 17
     */
    public function calculateRemainingTerm(&$transactionData, &$resultData) {
        return $transactionData['amount'];
        //investment.investment_remainingDuration
    }
    
    /**
     *  Get the amount which corresponds to the "late payment fee" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 47
     */
    public function calculateLatePaymentFeeIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "capitalRepayment Winvestify Format" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 34
     */
    public function calculateCapitalRepayment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 45
     */
    public function calculateDelayedInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "InterestIncomeBuyback" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 44
     */
    public function calculateInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 36
     */
    public function calculatePrincipalBuyback(&$transactionData, &$resultData) {
        echo "PRINCIPAL BUYBACK, amount =  " . $transactionData['amount'];
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "DelayedInterestIncomeBuyback" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 46
     */
    public function calculateDelayedInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /** DONE
     *  Get the amount which corresponds to the "PlatformDeposit" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 66
     */

    public function calculatePlatformDeposit(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /** 
     *  Get the amount which corresponds to the "Platformwithdrawal" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
     * 
     */

    public function calculatePlatformWithdrawal(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "Regular Gross Interest Income" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
     * 
     */
    public function calculateRegularGrossInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
      
    /**
     *  Calculates the number of active investments. Various investments in the same loan 
     *  are counted as 1 investment
     *  Note that some platforms use rounding for some of the concepts that determines if
     *  the outstanding principal = 0, which means that our absolute values ARE NOT ALWAYS 
     *  0 for a "fully amortized loan".
     *  Due to this a parameter, $precision, is used to decide how many decimals are taken into
     *  account for deciding if the return value is really 0.  
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB 
     *  @return int         number of active loans
     * 
     */
    public function calculateNumberOfActiveInvestments(&$transactionData, &$resultData) {
        $resultData['measurements']['state'] = $resultData['measurements']['state'] + 1;
        $tempOutstandingPrincipal = 1;     // Any value other than 0 

        if (isset($resultData['configParms']['outstandingPrincipalRoundingParm'])) {
            $precision = $resultData['configParms']['outstandingPrincipalRoundingParm'];
        }

        if (bccomp($resultData['investment']['investment_outstandingPrincipal'], $precision, 16) < 0) {
            $tempOutstandingPrincipal = 0;
        }
        $resultData['measurements']['stateCounting'] = $resultData['measurements']['stateCounting'] + 1;
        
     //   if ($resultData['investment']['technicalState'] == WIN_TECH_STATE_ACTIVE) {
            $resultData['measurements'][$transactionData['investment_loanId']]['winTechActiveStateCounting'] = $resultData['measurements'][$transactionData['investment_loanId']]['winTechActiveStateCounting'] + 1;
            if ($tempOutstandingPrincipal == 0) {
      //          $resultData['investment']['technicalState'] = WIN_TECH_STATE_NOT_ACTIVE;
                $resultData['measurements'][$transactionData['investment_loanId']]['decrements'][]  = 'YES';
                $resultData['measurements'][$transactionData['investment_loanId']]['technicalState'][] = 
                                                $resultData['investment']['technicalState'];
                return ($resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments'] - 1);          
            }
     //   }

        if ($resultData['investment']['investment_new'] == YES) {            //CHECK THE StatusOfLoan field instead
            $resultData['measurements'][$transactionData['investment_loanId']]['winTechNewLoanCounting'] = $resultData['measurements'][$transactionData['investment_loanId']]['winTechNewLoanCounting'] + 1;
            $resultData['measurements'][$transactionData['investment_loanId']]['increments'][] = 'NO';
            $resultData['measurements'][$transactionData['investment_loanId']]['technicalState'][] = 
                                                $resultData['investment']['technicalState'];           
            return ($resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments'] + 1);   
        } else {
            return $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments'];
        }
    }

    
    
    
// ONLY FOR TESTING
     public function calculateTechnicalState(&$transactionData, &$resultData) {

/*
  Technical states description:
  WIN_LOANSTATUS_WAITINGTOBEFORMALIZED : Investment is still to be formalized
  INITIAL   : Investment has started succesfully. No amortization has yet taken place
  ACTIVE    : One or more amortizations have taken place
  FINISHED  : The investment has finished, either succesfully or as writtenOff 
  CANCELLED : The investment never materialized, i.e. never went to ACTIVE or INITIAL
  WRITTEN-OFF : The investment is completely lost

statusOfLoan can have the following values: 
    WIN_LOANSTATUS_WAITINGTOBEFORMALIZED 
    WIN_LOANSTATUS_ACTIVE 
    WIN_LOANSTATUS_FINISHED
    WIN_LOANSTATUS_CANCELLED  
    WIN_LOANSTATUS_WRITTEN_OFF 
    WIN_LOANSTATUS_UNKNOWN
 */
        $tempOutstandingPrincipal = 1;
        if (isset($resultData['configParms']['outstandingPrincipalRoundingParm'])) {
            $precision = $resultData['configParms']['outstandingPrincipalRoundingParm'];
        }

        if (bccomp($resultData['investment']['investment_outstandingPrincipal'], $precision, 16) < 0) {
            $tempOutstandingPrincipal = 0;
        }       
        
// the following is perhaps not needed
        if ($resultData['investment']['investment_technicalStateTemp'] == 'FINISHED') {
            $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_FINISHED;
echo __FUNCTION__ . " " . __LINE__ . " Setting loan status to FINISHED\n";         
            return "FINISHED";             
        }    
        
        if ($tempOutstandingPrincipal == 0) {
            if ($resultData['investment']['investment_technicalStateTemp'] <> 'FINISHED') {
                $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']--;
                $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestmentsdecrements']++;
                $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_FINISHED;
echo __FUNCTION__ . " " . __LINE__ . " Setting loan status to FINISHED due to 0 outstanding principle\n";   
                return "FINISHED";              
            }
        }        
        
        if ($resultData['investment']['investment_statusOfLoan'] == WIN_LOANSTATUS_ACTIVE) {
            $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']++;
            $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestmentsincrements']++;
echo __FUNCTION__ . " " . __LINE__ . " Setting loan status to INITIAL\n";            
            return "INITIAL";               
        } 
        $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_ACTIVE;
echo __FUNCTION__ . " " . __LINE__ . " Setting loan status to INITIAL\n";         
        return "ACTIVE";                    
    }
    
 
    
    /**
     *  Get the amount which corresponds to the "PlatformbankCharges" concept
     * 
     *  @param  array       array with the current transaction data$resultData
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
     * 55
     */
    public function calculatePlatformBankCharges(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }


    /** NOT YET  checck if the index is investment or payment
     *  Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      amount expressed as a string
     */
    public function calculateTotalNetIncome(&$transactionData, &$resultData) {
        if (empty($resultData['investment']['investment_loanTotalCost'])) {
            $resultData['investment_loanTotalCost'] = 0.0;
        }
        if (empty($resultData['investment']['investment_totalGrossIncome'])) {
            $resultData['investment_totalGrossIncome'] = 0.0;
        }
        $result = bcsub($resultData['investment_totalGrossIncome'], $resultData['investment_loanTotalCost'], 16);
        return $result;
    }

    /**
     *  Get the amount which corresponds to the "totalOutstandingPrincipal" concept
     *  for the controlVariables check
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      the string representation of a large integer
     */
    public function calculateTotalOutstandingPrincipal(&$transactionData, &$resultData) {
//        if (isset($resultData['investment']['investment_outstandingPrincipal)
        $result = bcsub($resultData['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'], $resultData['investment']['investment_outstandingPrincipalOriginal'], 16);
        $result = bcadd($result, $resultData['investment']['investment_outstandingPrincipal'], 16);
        $result = bcsub($result, $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp']);
        $result = bcadd($result, $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp']);
        return $result;
    }

    /**
     *  Get the amount which corresponds to the "cost secondary market" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string
     * 47
     */
    public function calculateCostSecondaryMarket(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "income secondary market" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string
     * 
     */
    public function calculateIncomeSecondaryMarket(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /**
     *  Get the amount which corresponds to the "SecondaryMarketInvestment" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string
     * 26
     */
    public function calculateSecondaryMarketInvestment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    
   
    /**
     *  Calculates the sum of all payment concepts that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      don't care
     *
     */
    public function calculateGlobalTotalsNOTUSEDPerDay(&$transactionData, &$resultData) {
                
 
        
    }  
    
    
    
    /**
     *  Calculates the sum of the payment concept "LatePaymentFeeIncome" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalLatePaymentFeeIncomePerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_latePaymentFeeIncome']);    
    }   
    
    /**
     *  Calculates the sum of the payment concept "CapitalRepayment" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalCapitalRepaymentPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_capitalRepayment']);
    }    
 
    
    /**
     *  Calculates the sum of the payment concept "PrincipalBuyback" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalPrincipalBuybackPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_principalBuyback']);            
    }    
    
    /**
     *  Calculates the sum of the payment concept "InterestIncomeBuyback" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalInterestIncomeBuybackPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_interestIncomeBuyback']);
    }    
    
    /**
     *  Calculates the sum of the payment concept "RegularGrossInterestIncome" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalRegularGrossInterestIncomePerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_regularGrossInterestIncome']);    
    }

 
    /**
     *  Calculates the sum of the payment concept "myInvestment" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalMyInvestmentPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_myInvestment']);
    }      
    
    
    /**
     *  Calculates the sum of the payment concept "secondaryMarketInvestment" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalSecondaryMarketInvestmentPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_secondaryMarketInvestment']);    
    }      
    
    /**
     *  Calculates the sum of the payment concept "costSecondaryMarket" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalCostSecondaryMarketPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_costSecondaryMarket']);     
    }     
    
    
    
    /** NOT FINISHED YET. Only taking into account the simple model of Mintos, 1 investment and 1 investmentSlice
     *  
     *  If more slices, then the paidInstalments is the same for all slices
     * 
     *  Get the amount which corresponds to the "paidInstalments" concept. 
     * 
     *  It can distinguish on an per investmentSlice base, i.e. each investmentslice can have their own amortization table
     *  with its own repayment amount.
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculatePaidInstalments(&$transactionData, &$resultData) {
        $resultData['investment']['investment_paidInstalments']++;
        return $resultData['investment']['investment_paidInstalments']; 
    }    
    
    
    /**
     *  Calculates the effect of a disinvestment of an investment of the primary market which never matured to a real investment, 
     *  i.e. it started with state "reserved" and never reached status "active". It basically means that the platform returns the
     *  money which the investor had assigned to the "failed" investment
     *   
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a float
     */
    public function calculateDisinvestmentPrimaryMarket(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }   
    public function calculateDisinvestmentPrimaryMarketWinouthLoanReference(&$transactionData, &$resultData) {
        $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
        return $transactionData['amount'];
    }  
    
    /**
     *  Calculates the new state of a cancelled investment.  It never matured to a real investment, i.e. it 
     *  started with state "reserved" and *never* reached status "active".
     *   
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateCancellationState(&$transactionData, &$resultData) {
        return WIN_LOANSTATUS_CANCELLED;
    }    
 
    
    /**
     *  Determines the writtenOff amount, which is to be stored in the variable WrittenOff
     *
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large amount to be stored in writtenOff field
     */
    public function calculateBadDebt(&$transactionData, &$resultData) {
        
//        if ($resultData['investment']['investment_statusOfLoan'] == WIN_LOANSTATUS_WRITTEN_OFF) {
//            return $resultData['payment']['payment_writtenOff'];
//        }
        $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_WRITTEN_OFF;
        
        if (isset( $transactionData['amount'])) {           // We take the value as provided in the transaction record by the P2P
                                                            // This should be identical to the outstanding principal of investment
            $tempOutstandingPrincipal = $transactionData['amount'];
        }
        else {
            $tempOutstandingPrincipal = $resultData['investment']['investment_outstandingPrincipal'];
        }
        $resultData['investment']['investment_writtenOff'] = $tempOutstandingPrincipal;
        $resultData['investment']['investment_outstandingPrincipal'] = 0;
        return $tempOutstandingPrincipal;
    } 

    /**
     *  Determines the reservedAssets amount, which is to be stored in the variable reservedAssets
     *  The amount allocated to "reserved funds" is taken into account when calculating concept "cash" 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large amount to be stored in reservedAssets field
     */
    public function calculateReservedSimple(&$transactionData, &$resultData) {
        return WIN_LOANSTATUS_CANCELLED;
    } 
    
    /**
     *  Determines the reservedAssets amount, which is to be stored in the variable reservedAssets. 
     *  The amount is taken from "CashInPlatform" and moved to "reservedAssets".
     *  The amount allocated to "reserved funds" is taken into account when calculating concept "cash" 
     *
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large amount to be stored in reservedAssets field
     */  
    public function calculateReservedComplex(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }    
    
  
    /**
     *  Get the amount which corresponds to the "principalAndInterestPayment" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculatePrincipalAndInterestPayment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "partialPrincipalAndInterestPayment" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculatePartialPrincipalAndInterestPayment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "loanCompensation" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateLoanCompensation(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "Platform Compensation Positive" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculatePlatformCompensationPositive(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "tax VAT" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateTaxVAT(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the "capital repayment" or the "regular gross interest" concept payments from principalAndInterestPayment
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateOfCapitalRepaymentOrRegularGrossInterest(&$transactionData, &$resultData) {
        if (!isset($resultData['payment']['payment_principalAndInterestPayment']) || empty($resultData['payment']['payment_principalAndInterestPayment'])) {
            return;
        }
        
        if (empty($resultData['payment']['payment_capitalRepayment'])) {
            $capitalRepayment = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_regularGrossInterestIncome'], 16);
            $resultData['payment']['payment_capitalRepayment'] = $capitalRepayment;
            $cashInPlatform = bcadd($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $capitalRepayment, 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = $cashInPlatform;
        }
        else if (empty($resultData['payment']['payment_regularGrossInterestIncome'])) {       
            $regularGrossInterest = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_capitalRepayment'], 16);
            $resultData['payment']['payment_regularGrossInterestIncome'] = $regularGrossInterest;
        }
        return;
    }
    
    /**
     *  Get the "capital repayment" or the "regular gross interest" concept payments from principalAndInterestPayment
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateOfPartialCapitalRepaymentOrRegularGrossInterest(&$transactionData, &$resultData) {
        if (!isset($resultData['payment']['payment_partialPrincipalAndInterestPayment']) || empty($resultData['payment']['payment_partialPrincipalAndInterestPayment'])) {
            return;
        }
        
        if (empty($resultData['payment']['payment_partialCapitalRepayment'])) {
            $capitalRepayment = bcsub($resultData['payment']['payment_partialPrincipalAndInterestPayment'], $resultData['payment']['payment_regularGrossInterestIncome'], 16);
            $resultData['payment']['payment_partialPrincipalRepayment'] = $capitalRepayment;
            $cashInPlatform = bcadd($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $capitalRepayment, 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = $cashInPlatform;
        }
        else if (empty($resultData['payment']['payment_regularGrossInterestIncome'])) {
            $regularGrossInterest = bcsub($resultData['payment']['payment_partialPrincipalAndInterestPayment'], $resultData['payment']['payment_partialPrincipalRepayment'], 16);
            $resultData['payment']['payment_regularGrossInterestIncome'] = $regularGrossInterest;
            $cashInPlatform = bcadd($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $regularGrossInterest, 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = $cashInPlatform;
        }
        return;
    }
    
    /**
     *  Get the amount which corresponds to the "payment_taxVAT" concept
     * 
     *  @param type $transactionData
     *  @param type $resultData
     *  @return type
     */
    public function calculatePaymentTax(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "payment_incomeWithholdingTax" concept
     * 
     *  @param type $transactionData
     *  @param type $resultData
     *  @return type
     */
    public function calculateIncomeWithholdingTax(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
   
    /**
     *  Calculates the sum of the payment concept "PartialPrincipalRepayment" that happened during a day
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalPartialPrincipalRepaymentPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_partialPrincipalRepayment']);    
    }    
    
    /**
     *  Calculates the sum of the payment concept "DelayedInterestIncome" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalDelayedInterestIncomePerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_delayedInterestIncome']);    
    } 
    
    /**
     *  Calculates the sum of the payment concept "Commission Paid" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalCommissionPaidPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_commissionPaid']);    
    } 
    
    /**
     *  Calculates the sum of the payment concept "Tax VAT" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotaltaxVATPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_taxVAT']);    
    } 
    
    /**  
     *  Get the amount which corresponds to the "commission paid" concept.
     *  Note that this is both the in case a transaction record contains a loanId or not
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
     * 
     */
    public function calculateCommissionPaid(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    

    /**
     *  Get the amount which corresponds to the "written off" concept for the userinvestmentdata
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
     * 
     */
    public function calculateGlobalTotalWrittenOffPerDay(&$transactionData, &$resultData) {
        //$result = bcadd($resultData['Userinvestmentdata']['userinvestmentdata_writtenOff'], $resultData['investment']['investment_writtenOff'], 16);
        $result = "0.0";
        if (!empty($resultData['payment']['payment_writtenOff'])) {
            $result = $resultData['payment']['payment_writtenOff'];
        }
        return $result;      
    }
 
    
    /**
     *  Get the amount which corresponds to the "Platform Compensation Negative" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculatePlatformCompensationNegative(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }   
    
    /**
     *  Deals with the internals actions when an investments changes from state "pre-active" to "active"
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return int         new status of the loan
     */
    public function calculateActiveStateChange(&$transactionData, &$resultData) {
        $resultData['investment']['investment_technicalStateTemp'] = "ACTIVE";
        // move the corresponding part of the money from reserved funds to outstanding principal
            
        if ($resultData['investment']['investment_statusOfLoan'] ==  WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            $resultData['Userinvestmentdata']['userinvestmentdata_reservedFunds'] = bcsub(
                        $resultData['Userinvestmentdata']['userinvestmentdata_reservedFunds'],
                        $resultData['investment']['investment_myInvestment']
                    );
            $resultData['investment']['investment_outstandingPrincipal'] = bcadd( 
                        $resultData['investment']['investment_outstandingPrincipal'],
                        $resultData['investment']['investment_myInvestment']
                    );     
            return WIN_LOANSTATUS_ACTIVE;
        }
    }  
    
    /**
     *  Calculates the sum of the payment concept "Currency Exchange Fee" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalCurrencyExchangeFeePerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_currencyExchangeFee']);  
    }
    
    /**
     *  Calculates the sum of the payment concept "Currency Exchange Transaction" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalCurrencyExchangeTransactionPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_currencyExchangeTransaction']);  
    }
    
    /**
     *  Calculates the sum of the payment concept "Income with holding Tax" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     *
     */
    public function calculateGlobalTotalIncomeWithholdingTaxPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_incomeWithholdingTax']);  
    }
 
 
    /**
     *  State Change Management.
     *  Implements the state changes based on events. These events can be:
     *      a transaction record
     *      a state of a variable (example outstanding principle for a loan, investment_outstandingPrincipal 
     * 
     *  @param  array       Array with the current transaction data
     *  @param  array       Array with all data so far calculated and to be written to DB
     *  @event  string      This is an optional parameter and may NOT always be needed or present
     *                      'ChangeToBadDebtState'
     *                      'ChangeToCancelState'
     *                      'ChangeToActiveState'
     *                      OutstandingPrincipal = 0;
     * statusOfLoan can have the following values: 
     *   WIN_LOANSTATUS_WAITINGTOBEFORMALIZED 
     *   WIN_LOANSTATUS_ACTIVE 
     *   WIN_LOANSTATUS_FINISHED
     *   WIN_LOANSTATUS_CANCELLED  
     *   WIN_LOANSTATUS_WRITTEN_OFF 
     *   WIN_LOANSTATUS_UNKNOWN
     *  variables taken into consideration:
     *                 $resultData['investment']['investment_statusOfLoan']
     *              
     *  @return new state
     */
    public function manageState(&$transactionData, &$resultData, $event) {
      
        $initialStatusOfLoan = $resultData['investment']['investment_statusOfLoan'];       
        $tempOutstandingPrincipal = 21;
        
        switch ($initialStatusOfLoan) {
            case WIN_LOANSTATUS_WAITINGTOBEFORMALIZED:
                if ($event == "changeToActiveState") {
                    $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_ACTIVE;
                    $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']++;
                }
                if ($event == "changeToCancelledState") {
                    $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_CANCELLED;

                }
                return $resultData['investment']['investment_statusOfLoan'];
            break;
        
            case WIN_LOANSTATUS_ACTIVE:
                if ($tempOutstandingPrincipal == 0) {       // A loan has finished
                    $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']--;
                    $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_FINISHED;
                } 
                if ($event == "changeToBadDebtState") {
                    $resultData['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']--;
                    $resultData['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_WRITTEN_OFF;
                }
                return $resultData['investment']['investment_statusOfLoan'];
            break;
            
            case WIN_LOANSTATUS_FINISHED:                                       // Don't do anything 
                return WIN_LOANSTATUS_WRITTEN_FINISHED;           
            break;           
        
            case WIN_LOANSTATUS_CANCELLED:                                      // Don't do anything    
                return WIN_LOANSTATUS_CANCELLED;
            break;
        
            case WIN_LOANSTATUS_WRITTEN_OFF:                                    // Don't do anything    
                return WIN_LOANSTATUS_WRITTEN_OFF;
            break;

            case WIN_LOANSTATUS_UNKNOWN:                                        // Don't do anything    
                return WIN_LOANSTATUS_UNKNOWN;  
            break;       
        } 
    }    
    
  
     /**
     *  Get the amount which corresponds to the "Global WrittenOff" concept for visualization
     *  on dashboard2
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateDashboard2GlobalWrittenOff(&$transactionData, &$resultData) {
        if (isset($resultData['payment']['payment_writtenOff'])) {
            return $resultData['payment']['payment_writtenOff'];
        }
    }   
 
    /**
     * Call a function to fix rounding errors happened on the platform.
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function recalculateRoundingErrors(&$transactionData, &$resultData) {
        $tempOustanding = null;
        if (isset($resultData['configParms']['outstandingPrincipalRoundingParm'])) {
            $precision = $resultData['configParms']['outstandingPrincipalRoundingParm'];
        }
        if (!empty($resultData['configParms']['recalculateRoundingErrors']) 
                && bccomp($resultData['investment']['investment_outstandingPrincipal'], $precision, 16) < 0
                && bccomp("0", $resultData['investment']['investment_outstandingPrincipal'], 16) != 0) {
            $function = $resultData['configParms']['recalculateRoundingErrors']['function'];
            $this->$function($transactionData, $resultData);
        }
    }
    
    /**
     *  Recalculate variables with rounding errors adjusting the variables as need
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function recalculationOfRoundingErrors(&$transactionData, &$resultData) {
        $variables = $resultData['configParms']['recalculateRoundingErrors']['values'];
        $i = 1;
        foreach ($variables as $variable) {
            $modelFrom = explode("_", $variable["from"][0]);
            $modelTo = explode("_", $variable["to"][0]);
            $value = $resultData[$modelFrom[0]][$variable["from"][0]];
            if ($variable["sign"] == "negative") {
                $value = 0 - $value;
            }
            $resultData[$modelTo[0]][$variable["to"][0]] = bcadd($resultData[$modelTo[0]][$variable["to"][0]], $value, 16);
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_variable' . $i . "From"] = $variable["from"][0];
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_variable' . $i . "To"] = $variable["to"][0];
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_roundingError' . $i] = $value;
            $i++;
        }   
    }
    
    /**
     * 
     * @param  array $transactionData array with the current transaction data    
     * @param  array $resultData array with all data so far calculated and to be written to DB
     * @return string bonus amount
     */
    function calculateIncentivesAndBonus(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    function getStatusFromInvestment(&$transactionData, &$resultData) {
        $status = $resultData['investment']['investment_statusOfLoan'];
        return $status;
    }
    
    /** STILL NOT FINISHED
     *  Get the amount which corresponds to the "OutstandingPrincipal" concept. 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateReservedFunds(&$transactionData, &$resultData) {

        $result = $resultData['investment']['investment_reservedFunds'];     // in case more slices were bought of same loan
        if ($resultData['investment']['investment_tempState'] == WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            if (isset($resultData['payment']['payment_myInvestment'])) {
                $result = bcadd($result, $resultData['payment']['payment_myInvestment'], 16);
            }
            if (isset($resultData['payment']['payment_disinvestment'])) {
                $result = bcsub($result, $resultData['payment']['payment_disinvestment'], 16);
            }
        }
        return $result;
    }
    
    public function verifyStatusWaitingToBeFormalized(&$transactionData, &$resultData) {
        if ($resultData['investment']['investment_tempState'] == WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            if (!empty($resultData['configParms']['changeStatusToActive'])) {
                echo "change Status or possible to change \n";
                $functionToCall = $resultData['configParms']['changeStatusToActive']['function'];
                echo "function to call is $functionToCall \n";
                $resultData['investment']['investment_tempState'] = $this->$functionToCall($transactionData, $resultData);
                echo "investment tempState is " . $resultData['investment']['investment_tempState'];
            }
        }
        if (!empty($resultData['investment']['investment_tempState']) && $resultData['investment']['investment_tempState'] != WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            //$calculationClassHandle->manageState(&$transactionData, &$resultData, $event);
            ///NEW CODE TO TRY
            ///MOVE FROM RESERVED FUNDS IF EXIST TO OUTSTANDING PRINCIPAL
            $resultData['investment']['investment_statusOfLoan'] = $this->calculateActiveStateChange($transactionData, $resultData);
            if ($resultData['investment']['investment_tempState'] == WIN_LOANSTATUS_ACTIVE) {
//                                unset ($sliceIdentifier);
                echo "TAKING AMORTIZATION TABLE IS ON FIRE BABY \n";
                $sliceIdentifier = $this->getSliceIdentifier($transactionData, $resultData);
                // Check if sliceIdentifier has already been defined in $slicesAmortizationTablesToCollect,
                // if not then create a new array with the data available so far, sliceIdentifier and loanId
                $isNewTable = YES;
                foreach ($slicesAmortizationTablesToCollect as $tableCollectKey => $tableToCollect) {
                    if ($tableToCollect['sliceIdentifier'] == $sliceIdentifier) {
                        $isNewTable = NO;
                        break;
                    }
                }
                if ($isNewTable == YES) {
                    $collectTablesIndex++;
                    $slicesAmortizationTablesToCollect[$collectTablesIndex]['loanId'] = $transactionData['investment_loanId'];    // For later processing
                    $slicesAmortizationTablesToCollect[$collectTablesIndex]['sliceIdentifier'] = $sliceIdentifier;
                }
            }
        }
    }
    
}


?>
