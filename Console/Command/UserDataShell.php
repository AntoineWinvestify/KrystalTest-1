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
    protected $data = [];

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
     */
    public function consolidatePlatformControlVariables($externalControlVariables, $internalControlVariables) {
             
        $globalPrecision = $resultData['configParms']['globalRoundingParm'];
        
        $error = 0;
        echo "external values = \n";
        print_r($externalControlVariables);
        echo "\ninternal values = \n";
        print_r($internalControlVariables);
        foreach ($externalControlVariables as $variableKey => $variable) {
            switch ($variableKey) {
                case WIN_CONTROLVARIABLE_MYWALLET:
                    $tempResult = bccomp($internalControlVariables['myWallet'], $externalControlVariables['myWallet'], 16);
                    if ($tempResult == 1) {
                        $difference = bcsub($internalControlVariables['myWallet'], $externalControlVariables['myWallet'], 16);
                    }
                    else {
                        $difference = bcsub($externalControlVariables['myWallet'], $internalControlVariables['myWallet'], 16);
                    }
                    $tempDifference = bccomp($difference, $globalPrecision, 16);

                    if (bccomp($difference, $globalPrecision, 16) == 1) {
                        $error = $error + WIN_ERROR_CONTROLVARIABLE_CASH_IN_PLATFORM;  
                    }                  
                    break;
                case WIN_CONTROLVARIABLE_RESERVED_FUNDS:
                    $tempResult = bccomp($internalControlVariables['reservedFunds'], $externalControlVariables['reservedFunds'], 16);
                    if ($tempResult == 1) {
                        $difference = bcsub($internalControlVariables['reservedFunds'], $externalControlVariables['reservedFunds'], 16);
                    }
                    else {
                        $difference = bcsub($externalControlVariables['reservedFunds'], $internalControlVariables['reservedFunds'], 16);
                    }
                    $tempDifference = bccomp($difference, $globalPrecision, 16);

                    if (bccomp($difference, $globalPrecision, 16) == 1) {
                        $error = $error + WIN_ERROR_CONTROLVARIABLE_RESERVED_FUNDS;  
                    }                                       
                    break;                    
                case WIN_CONTROLVARIABLE_OUTSTANDINGPRINCIPAL:
                    $tempResult = bccomp($internalControlVariables['outstandingPrincipal'], $externalControlVariables['outstandingPrincipal'], 16);
                    if ($tempResult == 1) {
                        $difference = bcsub($internalControlVariables['outstandingPrincipal'], $externalControlVariables['outstandingPrincipal'], 16);
                    }
                    else {
                        $difference = bcsub($externalControlVariables['outstandingPrincipal'], $internalControlVariables['outstandingPrincipal'], 16);
                    }
                    $tempDifference = bccomp($difference, $globalPrecision, 16);

                    if (bccomp($difference, $globalPrecision, 16) == 1) {
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

        if (isset($resultData['payment']['payment_myInvestment'])) {
            $result = bcadd($result, $resultData['payment']['payment_myInvestment'], 16);
        }
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
        if (isset($resultData['payment']['payment_currencyFluctuation'])) {
            $result = bcsub($result, $resultData['payment']['payment_currencyFluctuation'], 16);
        }
        if (isset($resultData['payment']['payment_secondaryMarketSell'])) {
            $result = bcsub($result, $resultData['payment']['payment_secondaryMarketSell'], 16);
        }
        if (isset($resultData['payment']['payment_principalRepaymentGuarantee'])) {
            $result = bcsub($result, $resultData['payment']['payment_principalRepaymentGuarantee'], 16);
        }
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
        print_r($resultData);
        if (empty($resultData['investment']['investment_loanId']) && empty($resultData['investment']['investment_sliceIdentifier'])) {
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'] = bcadd($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'],$transactionData['amount'], 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = bcsub($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $transactionData['amount'], 16);
            return;
        }
        if ($resultData['investment']['investment_tempState'] == WIN_LOANSTATUS_VERIFYACTIVE) {
            return $this->data['companyHandle']->manageMyInvestment($transactionData, $resultData);
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
    
    public function calculateLatePaymentFee(&$transactionData, &$resultData) {
        return -$transactionData['amount'];
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
    public function calculateCapitalRepaymentCost(&$transactionData, &$resultData) {
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
     */

    public function calculatePlatformWithdrawal(&$transactionData, &$resultData) {
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
     */
    public function calculateNumberOfActiveInvestments(&$transactionData, &$resultData) {
        $resultData['measurements']['state'] = $resultData['measurements']['state'] + 1;
        $tempOutstandingPrincipal = 1;     // Any value other than 0 

        if (isset($this->data['precision'])) {
            $precision = $this->data['precision'];
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
        if (isset($this->data['precision'])) {
            $precision = $this->data['precision'];
        }
// Determine properly if the outstandingPrincipal is within the precision limit. NOTE THAT the
// outstanding principal can be negative. In that case I don't count them 
        
        if (bccomp(abs($resultData['investment']['investment_outstandingPrincipal']), $precision, 16) < 0) {
            $tempOutstandingPrincipal = 0;
        }

        if ($resultData['investment']['investment_tempState'] === WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            return "PREACTIVE";
        }
        
        if ($resultData['investment']['investment_tempState'] === WIN_LOANSTATUS_CANCELLED) {
            return "CANCEL";
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
        /*$result = bcsub($result, $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp']);
        $result = bcadd($result, $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp']);*/
        return $result;
    }

    /**
     *  Get the amount which corresponds to the "cost secondary market" concept
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string
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
     */
    public function calculateGlobalTotalsNOTUSEDPerDay(&$transactionData, &$resultData) {
                
 
        
    }  
    
    
    


 
    /**
     * Sum in only payment
     * @param type $transactionData
     * @param type $resultData
     * @return type
     */
    public function calculateGlobalTotalDelayedInterestIncomeBuybackPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_delayedInterestIncomeBuyback']);    
    }   
    public function calculateGlobalTotalIncomeSecondaryMarket(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_incomeSecondaryMarket']);    
    }    
    public function calculateGlobalTotalLoanIncentivesAndBonusPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_loanIncentivesAndBonus']);    
    }   
    public function calculateGlobalTotalSecondaryMarketSell(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_secondaryMarketSell']);    
    }
    public function calculateGlobalTotalReapymentGuarantee(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_principalRepaymentGuarantee']);    
    }
    public function calculateGlobalTotalInterestIncomeGuarantee(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_interestIncomeGuarantee']);    
    }
    
    
    /**
     * Sum in paymeny and gobalcashflow
     * @param type $transactionData
     * @param type $resultData
     * @return type
     */
    public function calculateGlobalTotalDefaultInterestIncome(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_defaultInterestIncome'],$resultData['globalcashflowdata']['globalcashflowdata_defaultInterestIncome'], 16));   
    }
    public function calculateGlobalTotalDefaultInterestIncomeRebuy(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_defaultInterestIncomeRebuy'],$resultData['globalcashflowdata']['globalcashflowdata_defaultInterestIncomeRebuy'], 16));    
    }
    public function calculateGlobalTotalRecoveries(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_loanRecoveries'],$resultData['globalcashflowdata']['globalcashflowdata_platformRecoveries'], 16));    
    }
    public function calculateGlobalTotaltaxVATPerDay(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_taxVAT'],$resultData['globalcashflowdata']['globalcashflowdata_taxVat'], 16));    
    } 
    public function calculateGlobalTotalLatePaymentFeeIncomePerDay(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_latePaymentFeeIncome'], $resultData['globalcashflowdata']['globalcashflowdata_latePaymentFeeIncome'], 16));    
    }  
    public function calculateGlobalTotalCommissionPaidPerDay(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_commissionPaid'], $resultData['globalcashflowdata']['globalcashflowdata_commissionPaid'], 16));    
    } 
    public function calculateGlobalTotalRegularGrossInterestIncomePerDay(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_regularGrossInterestIncome'], $resultData['globalcashflowdata']['globalcashflowdata_regularGrossInterestIncome'], 16)); 
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
     */
    public function calculateGlobalTotalInterestIncomeBuybackPerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_interestIncomeBuyback']);
    }    
    

 

 
    /**
     *  Calculates the sum of the payment concept "myInvestment" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
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
        if (isset($resultData['investment']['investment_reservedFunds']) && !empty($resultData['investment']['investment_reservedFunds'])) {
            echo "enter here disinviestment \n";
            $resultData['investment']['investment_disinvestment'] = bcadd($resultData['investment']['investment_disinvestment'], $transactionData['amount'], 16);
            $resultData['investment']['investment_reservedFunds'] = bcsub($resultData['investment']['investment_reservedFunds'], $transactionData['amount'], 16);
            print_r($resultData);
            $this->calculateCancellationState($transactionData, $resultData);
            return $transactionData['amount'];
        }
        else if (!isset($resultData['investment']['investment_reservedFunds']) && empty($resultData['investment']['investment_reservedFunds'])) {
            echo "Before subbbb \n";
            print_r($resultData);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = bcsub(
                    $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], 
                    $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReference'], 
                    16);
            $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
            $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReference'] = bcadd(
                    $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReference'],
                    $transactionData['amount'], 
                    16);
            echo "after ssbbbb \n";
            print_r($resultData);
            return $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReference'];
        }
        else {
            return $transactionData['amount'];
        }
    }
    
    /*public function calculateDisinvestmentPrimaryMarketWinouthLoanReference(&$transactionData, &$resultData) {
        $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
        return $transactionData['amount'];
    }  */
    
    /**
     *  Calculates the new state of a cancelled investment.  It never matured to a real investment, i.e. it 
     *  started with state "reserved" and *never* reached status "active".
     *   
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateCancellationState(&$transactionData, &$resultData) {
        $resultData['investment']['investment_tempState'] = WIN_LOANSTATUS_CANCELLED;
        $resultData['investment']['investment.investment_statusOfLoan'] = WIN_LOANSTATUS_CANCELLED;
        $resultData['investment']['investment_technicalStateTemp'] = "CANCEL";
        $result = bcadd($resultData['investment']['investment_myInvestment'], $resultData['investment']['investment_reservedFunds'], 16);
        $resultVerification = bccomp($result, $resultData['investment']['investment_disinvestment'], 16);
        if ($resultVerification === 1) {
            echo "loan is still active after disinvestment \n";
            $resultData['investment']['investment.investment_statusOfLoan'] = WIN_LOANSTATUS_ACTIVE;
            $resultData['investment']['investment_tempState'] = WIN_LOANSTATUS_ACTIVE;
            $resultData['investment']['investment_technicalStateTemp'] = "INITIAL";
        }
    }    
 
    
    /**
     *  Determines the writtenOff amount, which is to be stored in the variable WrittenOff
     *
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large amount to be stored in writtenOff field
     */
    public function calculateBadDebt(&$transactionData, &$resultData) {
        
//        if ($resultData['investment']['investment_consolidatePlatformControlVariables'] == WIN_LOANSTATUS_WRITTEN_OFF) {
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
        if(isset($resultData['payment']['payment_partialPrincipalAndInterestPayment']) || !empty($resultData['payment']['payment_partialPrincipalAndInterestPayment'])){
            $resultData['payment']['payment_partialPrincipalAndInterestPayment'] = $resultData['payment']['payment_partialPrincipalAndInterestPayment'] + $transactionData['amount'];
            return;
        }
        return $transactionData['amount'];
    }
    
    /**
     *  Get the amount which corresponds to the "partialPrincipalAndInterestPayment" concept 
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculatePartialPrincipalAndInterestPayment(&$transactionData, &$resultData) {
        if(isset($resultData['payment']['payment_principalAndInterestPayment']) || !empty($resultData['payment']['payment_principalAndInterestPayment'])){
            $resultData['payment']['payment_principalAndInterestPayment'] = $resultData['payment']['payment_principalAndInterestPayment'] + $transactionData['amount'];
            return;
        }
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
echo __FUNCTION__ . " " . __LINE__  . "\n";  
        if (!isset($resultData['payment']['payment_principalAndInterestPayment']) || empty($resultData['payment']['payment_principalAndInterestPayment'])) {
            return;
        }
print_r($transactionData);
print_r($resultData);        
        if (empty($resultData['payment']['payment_capitalRepayment'])) {
echo __FUNCTION__ . " " . __LINE__  . "\n";              
            $capitalRepayment = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_regularGrossInterestIncome'], 16);
            $resultData['payment']['payment_capitalRepayment'] = $capitalRepayment;
            $cashInPlatform = bcadd($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $capitalRepayment, 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = $cashInPlatform;
echo __FUNCTION__ . " " . __LINE__  . "\n";            
        }
        else if (empty($resultData['payment']['payment_regularGrossInterestIncome'])) {
echo __FUNCTION__ . " " . __LINE__  . "\n";  
            $regularGrossInterest = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_capitalRepayment'], 16);
            $resultData['payment']['payment_regularGrossInterestIncome'] = $regularGrossInterest;
        }
 print_r($transactionData);
 print_r($resultData);         
echo __FUNCTION__ . " " . __LINE__  . "\n";         
        return;
    }
    
    /**
     *  Get the "capital repayment" or the "regular gross interest" concept payments from principalAndInterestPayment
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function calculateOfPartialCapitalRepaymentOrRegularGrossInterest(&$transactionData, &$resultData) {
echo __FUNCTION__ . " " . __LINE__  . "\n";          
        if (!isset($resultData['payment']['payment_partialPrincipalAndInterestPayment']) || empty($resultData['payment']['payment_partialPrincipalAndInterestPayment'])) {
            return;
        }
 print_r($transactionData);
 print_r($resultData);
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
 echo __FUNCTION__ . " " . __LINE__  . "\n";        
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
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
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
     */
    public function calculateGlobalTotalDelayedInterestIncomePerDay(&$transactionData, &$resultData) {
        return($resultData['payment']['payment_delayedInterestIncome']);    
    } 
    


    


    
    /**  
     *  Get the amount which corresponds to the "commission paid" concept.
     *  Note that this is both the in case a transaction record contains a loanId or not
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      amount expressed as a string
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
echo __FUNCTION__ . " " . __LINE__  . "\n";    
        echo "changing from preactive to active \n";
        print_r($resultData);
        $resultData['investment']['investment_technicalStateTemp'] = "ACTIVE";
        $resultData['investment']['investment_tempState'] = WIN_LOANSTATUS_ACTIVE_AM_TABLE;
        // move the corresponding part of the money from reserved funds to outstanding principal
        $resultData['Userinvestmentdata']['userinvestmentdata_reservedAssets'] = bcsub(
                    $resultData['Userinvestmentdata']['userinvestmentdata_reservedAssets'],
                    $resultData['investment']['investment_reservedFunds'],
                    16
                ); 
        $resultData['payment']['payment_myInvestment'] = bcadd( 
                    $resultData['payment']['payment_myInvestment'],
                    $resultData['investment']['investment_reservedFunds'],
                    16
                );     
        $resultData['investment']['investment_myInvestment'] = bcadd( 
                    $resultData['investment']['investment_myInvestment'],
                    $resultData['investment']['investment_reservedFunds'],
                    16
                );
        $resultData['investment']['investment_reservedFunds'] = bcsub( 
                    $resultData['investment']['investment_reservedFunds'],
                    $resultData['investment']['investment_reservedFunds'],
                    16
                );
echo __FUNCTION__ . " " . __LINE__  . "\n";    
        echo "state changed \n";
        print_r($resultData);
        return WIN_LOANSTATUS_ACTIVE;
    }  
    
    /**
     *  Calculates the sum of the payment concept "Currency Exchange Fee" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
     */
    public function calculateGlobalTotalCurrencyExchangeFeePerDay(&$transactionData, &$resultData) {
        return(bcadd($resultData['payment']['payment_currencyExchangeFee'],$resultData['globalcashflowdata']['globalcashflowdata_currencyExchangeFee'], 16));  
    }

    /**
     *  Calculates the sum of the payment concept "Currency Exchange Transaction" that happened during a day
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      accumulated amount
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
     * Call a function to fix rounding errors as "committed" by the platform.
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function recalculateRoundingErrors(&$transactionData, &$resultData) {
        $tempOustanding = null;
        if (isset($this->data['precision'])) {
            $precision = $this->data['precision'];
        }
        if (!empty($this->data['recalculateRoundingErrors']) 
                && bccomp($resultData['investment']['investment_outstandingPrincipal'], $precision, 16) < 0
                && bccomp("0", $resultData['investment']['investment_outstandingPrincipal'], 16) != 0) {
            $function = $this->data['recalculateRoundingErrors']['function'];
            $this->$function($transactionData, $resultData);
        }
echo __FUNCTION__ . " " . __LINE__  . "\n";          
print_r($transactionData);
print_r($resultData); 
    }
    
    /**
     *  Recalculate variables with rounding errors adjusting the variables as needed
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     */
    public function recalculationOfRoundingErrors(&$transactionData, &$resultData) {
        $variables = $this->data['recalculateRoundingErrors']['values'];
        $i = 1;
        foreach ($variables as $variable) {
echo "Loop variable = " ;
print_r($variable);
            $modelFrom = explode("_", $variable["from"][0]);
            $modelTo = explode("_", $variable["to"][0]);
            $value = $resultData[$modelFrom[0]][$variable["from"][0]];
            
            if ($variable["sign"] == "negative") {
                $value = 0 - $value;
            }
echo "Value = $value\n";            
print_r($modelTo);

            $resultData[$modelTo[0]][$variable["to"][0]] = bcadd($resultData[$modelTo[0]][$variable["to"][0]], $value, 16);
print_r($resultData);
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_variable' . $i . "From"] = $variable["from"][0];
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_variable' . $i . "To"] = $variable["to"][0];
            $resultData['roundingerrorcompensation']['roundingerrorcompensation_roundingError' . $i] = $value;
            $i++;
            
        }
echo __FUNCTION__ . " " . __LINE__  . "\n";        
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
    
    /** STILL NOT FINISHED
     *  Calculate the real state of a loan and if the loan must be on reservedAssets or outstandingPrincipal
     *  There are different states
     *  WIN_LOANSTATUS_WAITINGTOBEFORMALIZED
     *  The function write myInvestment in reservedFunds
     *  WIN_LOANSTATUS_VERIFYWAITINGTOBEFORMALIZED
     *  We verify first that the investment in preactive state is not already in DB in order to save it
     *  WIN_LOANSTATUS_VERIFYACTIVE
     *  We verify that the investment is not in preactive state, then we add it in DB with active state.
     *  If the investment is already on DB with preactive state, we change the state 
     *  and move the reservedFunds to outstandingPrincipal
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */
    public function calculateReservedFunds(&$transactionData, &$resultData) {
        if (empty($resultData['investment']['investment_loanId']) && empty($resultData['investment']['investment_sliceIdentifier'])) {
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'] = $transactionData['amount'];
            $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'] = bcadd($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReference'],$transactionData['amount'], 16);
            $resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = bcsub($resultData['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $transactionData['amount'], 16);
            return;
        }
        //$result = $resultData['investment']['investment_reservedFunds'];     // in case more slices were bought of same loan
        if ($resultData['investment']['investment_tempState'] == WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
            return $this->data['companyHandle']->manageReservedFunds($transactionData, $resultData, $this->data);
        }
        print_r($resultData);
        return;
    }
    
    /**
     *  Get the amount which corresponds to the "totalReservedAssets" concept
     *  for the controlVariables check
     * 
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB ( = shadow database)
     *  @return string      the string representation of a large integer
     */
    public function calculateTotalReservedAssets(&$transactionData, &$resultData) {
//        if (isset($resultData['investment']['investment_outstandingPrincipal)
        $result = $resultData['Userinvestmentdata']['userinvestmentdata_reservedAssets'];
        $result = bcsub($result, $resultData['payment']['payment_disinvestment'], 16);
        $resultData['investment']['investment_reservedFunds'] = bcsub($resultData['investment']['investment_reservedFunds'], $resultData['payment']['payment_disinvestment'], 16);
        $result = bcadd($result, $resultData['payment']['payment_reservedFunds'], 16);
        $result = bcsub($result, $resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_disinvestmentWithoutLoanReferenceTmp']);
        $result = bcadd($result, $resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp'], 16);
        unset($resultData['globalcashflowdata']['globalcashflowdata_investmentWithoutLoanReferenceTmp']);
        return $result;
    }
    
    public function setData($data) {
        foreach ($data as $key => $individualData) {
            $this->data[$key] = $individualData;
        }
    }
    
    /**
     * Function to calculate the payment reserved funds
     * 
     * @param  array $transactionData array with the current transaction data    
     * @param  array $resultData array with all data so far calculated and to be written to DB
     * @return string bonus amount
     */
    function calculatePaymentReservedFunds(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    
    /**
     * 
     * @param  array $transactionData array with the current transaction data    
     * @param  array $resultData array with all data so far calculated and to be written to DB
     * @return string bonus amount
     */
    function calculateRecoveries(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    

    
    function calculateSecondaryMarketSell(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }
    function calculateGenericAmountReturn(&$transactionData, &$resultData){
        return $transactionData['amount'];
    }
    function calculateGenericNegativeAmountReturn(&$transactionData, &$resultData){
        return -$transactionData['amount'];
    }
}


?>
