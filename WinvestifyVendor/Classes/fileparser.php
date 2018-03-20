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
 * 
 * @author
 * @version 0.9.0
 * @date  2017-12-27
 * @package
 *
 *
 * 2017-10-09		version 0.1
 * Basic version
 *
 * This class parses the transaction/investments files etc by using a configuration file which is 
 * provided by each companyCodeFile. The result is returned in an array
 * 
 * 2017-10-15           version 0.2
 * support of configuration parameters 'offsetStart' and 'offsetEnd'
 * 
 * 2017-10-24           version 0.3
 * Added function to parse a html file
 * 
 * 2017-10-26           version 0.3
 * Due to use of bc-math functionality, the amounts are now ordinary string with the decimal point
 * getLastError is returning real data, for 'unknown concept'
 * E format in getAmount fixed. Format example: 2,31E-6.
 * Minor fixes.
 * 
 * 2017-11-09           Version 0.4
 * Amount and currency bug fixing.
 * 
 * 2017-11-10           version 0.5
 * Updated function extractDataFromString with an extra parameter
 * 
 * 2017-11-11           version 0.6
 * Functions getDefaultValue and getCountry added
 *  
 * 2017-11-14           version 0.7
 * Function fixes
 * extractDataFromString
 * getAmount
 * getCurrency
 * 
 * 2017-11-20           version 0.8
 * Added a new function "getConceptChars"; 
 * 
 * 2017-11-28           version0.8.1
 * New function, generateId, for generating a "random (unique)identifier" if cell is empty
 * Cell data is cleaned before sending it as '$input' to a function
 * Added configurations for Zank
 * New configuration parameter (changeCronologicalOrder)
 * 
 * 2017-12-07
 * rectified an error in saveExcelToArray. Error was related to removing an item at random
 * 
 * 2017-12-27           version 0.9.0
 * method setConfig: take the extra index level into account
 * 
 * 2018-01-02           version 0.9.1
 * A new characteric, REPAYMENT,  was added to the $transactionDetails array
 * 
 * 2018-02-04   version_0.9.1
 * Added new method, handleNumber, for dealing with numbers
 * 
 * 2018-02-06   version_0.9.2
 * Added many new concepts according to the latest contents of FlowData.xlsx
 * 
 * 
 * 
 * Pending:
 * chunking, csv file check
 * 
 * 
 */

/**
 *
 * Class that can analyze a xls/csv/pdf/html file(s) and writes the information to an array
 *
 *
 */
class Fileparser {
    
    protected $config = array ('offsetStart' => 0,
                            'offsetEnd'     => 0,
                            'separatorChar' => ";",
                            'sortParameter' => "",                  // used to "sort" the array and use $sortParameter as prime index.
                                                                    // if array does not have $sortParameter then "global" index is used
                                                                    // Typically used for sorting by loanId index
                            'changeCronologicalOrder' => 0          // Do not 'sort' order of the resulting array. This option is executed AFTER
                                                                    // the 'sortParameter' is checked. 
                            );

    protected $errorData = array();                                 // Contains the information of the last occurred error
    
    protected $defaultFinishDate;

    protected $currencies = array(EUR => ["EUR", "€"],
                                    GBP => ["GBP", "£"],
                                    USD => ["USD", "$"],
                                    ARS => ["ARS", "$"],
                                    AUD => ["AUD", "$"],
                                    NZD => ["NZD", "$"],
                                    BYN => ["BYN", "BR"],
                                    BGN => ["BGN", "лв"],
                                    CZK => ["CZK", "Kč"],
                                    DKK => ["DKK", "Kr"],
                                    CHF => ["CHF", "Fr"],
                                    MXN => ["MXN", "$"],
                                    RUB => ["RUB", "₽"],
                                    );

    // dictionary lookup for trying to identify an unknown concept
    protected $dictionaryWords = array('tax'    => WIN_CONCEPT_TYPE_COST,
                                'instalment'    => WIN_CONCEPT_TYPE_INCOME,
                                'installment'   => WIN_CONCEPT_TYPE_INCOME,
                                'payment'       => WIN_CONCEPT_TYPE_COST,
                                'withdraw'      => WIN_CONCEPT_TYPE_COST,
                                'back fee'      => WIN_CONCEPT_TYPE_COST,
                                'back tax'      => WIN_CONCEPT_TYPE_COST,
                                'cost'          => WIN_CONCEPT_TYPE_COST,
                                'purchase'      => WIN_CONCEPT_TYPE_COST,
                                'bid'           => WIN_CONCEPT_TYPE_COST,
                                'auction'       => WIN_CONCEPT_TYPE_COST,
                                'sale'          => WIN_CONCEPT_TYPE_INCOME,
                                'swap'          => WIN_CONCEPT_TYPE_INCOME,
                                'loan'          => WIN_CONCEPT_TYPE_COST,
                                'buy'           => WIN_CONCEPT_TYPE_INCOME,
                                'sell'          => WIN_CONCEPT_TYPE_INCOME,
                                'sale'          => WIN_CONCEPT_TYPE_INCOME,
                                'earning'       => WIN_CONCEPT_TYPE_INCOME

                            );   
 // "char" is a space seperated list of the following lables. Note that more then 1 lable can be assigned to the same concept.
 // Possible lables that can be applied to each concept are:
 // AM_TABLE        => Force the collection of the amortization table. This might be a brandnew table or an update of a table for 
 //                    an already existing loan if a extra participation is bought
 // REPAYMENT       => An amortization payment has taken place
 // REMOVE_AM_TABLE => Remove the mark that an amortization table is to be collected
 // PRE-ACTIVE      => Investment should go into PRE-ACTIVE state
 // READ_INVESTMENT_DATA => Re-read the data of the investment from the actual "investment.xls" file (assuming it exists). 
 //                         This is only aplicable to the content of the investment model   
 // TO_ACTIVE_STATUS ==> We put the investment as active
 // PREACTIVE ==> We put the investment as preactive by default
 // PREACTIVE_VERIFICATION ==> We verify first that the investment in preactive state is not already in DB in order to save it with preactive state
 // ACTIVE_VERIFICATION ==> We verify that the investment is not in preactive state, then we add it in DB with active state.
 //                                 If the investment is already on DB with preactive state, we change the state 
 //                                 and move the reservedFunds to outstandingPrincipal
    
 /*
  * The index corresponds to the number of the concepts as defined in document "Flow_Data.xlsx"
  * Note that the index "detail" and "type" are unique and are NOT repeated. This means that a search through this
  * array can be done using both "detail" or "type" as search key
  * 
  * Format:
  * "detail" => name of internal Winvestify concept as defined in Flow Data. Can be considered as en event
  * "transactionType" => Identifies if it is a cost or an income
  * "account" => NOT REALLY USED. MAY BE DELETED 
  * "type" => link to the variables as defined in 'internalVariablesConfiguration.php'
  * "chars" =>  a comma seperated list of lables as defined above
  * 
  */   

    protected $transactionDetails = [  
            1 => [
                "detail" => "Cash_deposit",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,                   // 1 = income, 2 = cost
                "account" => "CF",                                              // Not (yet) used
                "type" => "globalcashflowdata_platformDeposits"            
                ],
            2 => [
                "detail" => "Cash_withdrawal",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "CF",
                "type" => "globalcashflowdata_platformWithdrawals"          
                ],
            3 => [
                "detail" => "Primary_market_investment",                        //We want a primary_market_investment but in active state as default
                                                                                //For example Mintos
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "investment_myInvestment",  
                "chars" => "ACTIVE"
                ],
            4 => [
                "detail" => "Secondary_market_investment",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "payment_secondaryMarketInvestment",
                "chars" => "AM_TABLE"
                ],
            5 => [
                "detail" => "Capital_repayment",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "Capital",
                "type" => "payment_capitalRepayment",
                "chars" => "REPAYMENT"                   
                ],
            6 => [
                "detail" => "Partial_principal_repayment",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "Capital",
                "type" => "payment_partialPrincipalRepayment"
                ],
            7 => [
                "detail" => "Principal_buyback",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "Capital",
                "type" => "payment_principalBuyback"                     
                ],
            8 => [
                "detail" => "Principal_and_interest_payment",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "Mix",
                "type" => "payment_principalAndInterestPayment"
                ],
            9 => [
                "detail" => "Regular_gross_interest_income",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_regularGrossInterestIncome"           
                ],
            10 => [
                "detail" => "Delayed_interest_income",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_delayedInterestIncome"          
                ],
            11 => [ 
                "detail" => "Late_payment_fee_income",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_latePaymentFeeIncome"                  
                ],
            12 => [
                "detail" => "Interest_income_buyback",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_interestIncomeBuyback"                 
                ],
            13 => [
                "detail" => "Delayed_interest_income_buyback",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_delayedInterestIncomeBuyback"           
                ],
            14 => [
                "detail" => "Incentives_and_bonus",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_loanIncentivesAndBonus"  
                ],
            15 => [
                "detail" => "Compensation_positive",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "globalcashflowdata_platformCompensationPositive"    
                ],
            16 => [
                "detail" => "Income_secondary_market",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_incomeSecondaryMarket"        
                ],
            17 => [
                "detail" => "Currency_fluctuation_positive",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_currencyFluctuationPositive"  
                ],
            19 => [
                "detail" => "Recoveries",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "concept19"
                ],
            20 => [
                "detail" => "Commission",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_commissionPaid"
                ],
            21 => [
                "detail" => "Bank_charges",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "globalcashflowdata_bankCharges"
                ],
            22 => [
                "detail" => "Cost_secondary_market",
                "transactionType" => WIN_CONCEPT_TYPE_COST,                
                "account" => "PL",
                "type" => "payment_costSecondaryMarket"
                ],
            23 => [
                "detail" => "Interest_payment_secondary_market_purchase",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept23"
                ],           
            24 => [
                "detail" => "Currency_exchange_fee",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_currencyExchangeFee"
                ],
            25 => [
                "detail" => "Currency_fluctuation_negative",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_currencyFluctuationNegative"
                ],                        
            26 => [
                "detail" => "Tax_VAT",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_taxVAT"
                ],
            27 => [
                "detail" => "Tax_income_withholding_tax",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_incomeWithholdingTax"
                ],
            28 => [
                "detail" => "Write-off",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "payment_writtenOff"
                ],
            29 => [
                "detail" => "Registration",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept29"
                ],
            30 => [
                "detail" => "Incoming_currency_exchange_transaction",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "incomingCurrencyExchangeTransaction"
                ],
            31 => [
                "detail" => "Unknown_income",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept31"
                ],
            32 => [
                "detail" => "Unknown_cost",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept32"
                ],
            33 => [
                "detail" => "Unknown_concept",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept33"
                ], 
            34 => [
                "detail" => "Default_interest_income",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "defaultInterestIncome"
                ],     
            35 => [
                "detail" => "Partial_principal_and_interest_payment",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Mix",
                "type" => "payment_partialPrincipalAndInterestPayment"
                ],
            36 => [
                "detail" => "Outgoing_currency_exchange_transaction", 
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "outgoingCurrencyExchangeTransaction",
                ],
            37 => [
                "detail" => "Compensation_negative",  
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "globalcashflowdata_platformCompensationNegative",
                ],
            38 => [     // This is the "normal case" for disinvestments WITH a loan id (LoanBook)
                "detail" => "Disinvestment_primary_market", 
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "disinvestmentPrimaryMarket",
                //"chars" => "REMOVE_AM_TABLE" 
                ],
            39 => [
                "detail" => "Disinvestment_secundary_market", 
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "disinvestmentSecondaryMarket",
                ],
        
            40 => [
                "detail" => "Create_reserved_funds",                            
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "createReservedFunds",
                "chars" => "PRE-ACTIVE",
                ],

            41 => [
                "detail" => "Cashback_bonus",                            
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "createReservedFunds",
                ],

            42 => [ // This is for so-called Ghost Loans (example: Zank)
                "detail" => "Disinvestment_without_loanReference",                            
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "disinvestmentWithoutLoanReference",
                ],
            43 => [
                "detail" => "Commission",                                       // Some commission in Zank are 0,0000 €. getComplexTransactionDetail read this commission as income, we need this to resolve the unknow concept error.
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "payment_commissionPaid"
            ],
            44 => [
                "detail" => "Primary_market_investment_preactive",              //We want a primary_market_investment but in preactive state as default
                                                                                //For example Zank
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "investment_myInvestmentPreactive",  
                "chars" => "PREACTIVE"
                ],
            45 => [
                "detail" => "Primary_market_investment_active_verification",    //We want a primary_market_investment in active state as default but
                                                                                //it is needed a verification if before it was in preactive status
                                                                                //for example Finanzarel or loanbook
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "investment_myInvestmentActiveVerification",  
                "chars" => "ACTIVE_VERIFICATION"
                ],
        
            105 => [
                "detail" => "dummy_concept",    // This is a dummy concept
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "dummy",
                ],

     
        
            // The following are psuedo concepts, and used in cases where an investment in a loan has been done,
            // but at the end the loan was cancelled BEFORE reaching the 'active' state or if the investment
            // matured into a real loan

            10001 => [
                "detail" => "Change_to_active_state",                           // Move an investment from PRE-ACTIVE to ACTIVE
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "investment_activeStateChange",
                "chars" => "AM_TABLE, READ_INVESTMENT_DATA"                     // = Collect Amortization table *and* re-read the current investment data
                ],
 /*       Not NEEDED AS THIS IS DONE USING AN ORDINARY TRANSACTION RECORD
            10002 => [
                "detail" => "Change_to_badDebt_state",                          // Move an investment from ACTIVE to WRITTEN_OFF 
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "badDebtStateChange",
                ],
    */
            10003 => [
                "detail" => "Change_to_cancelled_state",                        // Move an investment from PRE-ACTIVE to CANCELLED
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "investment_cancelledStateChange",
                ],       
                

        
    /*         105 => [
                "detail" => "Create_reserved_funds",    // Move an investment from PRE-ACTIVE to CANCELLED
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "createReservedFundsNoImpactCashInPlatform",
                ]  
     */  


        
        ];

protected $countries = [
            'AF' => "Afghanistan",
            'AX' => "Åland Islands",
            'AL' => "Albania",
            'DZ' => "Algeria",
            'AS' => "American Samoa",
            'AD' => "Andorra",
            'AX' => "Angola",
            'AL' => "Anguilla",
            'AQ' => "Antarctica",
            'AG' => "Antigua and Barbuda",
            'AR' => "Argentina",
            'AM' => "Armenia",
            'AW' => "Aruba",
            'AU' => "Australia",
            'AT' => "Austria",
            'AZ' => "Azerbaijan",
            'BS' => "Bahamas",
            'BH' => "Bahrain",
            'BD' => "Bangladesh",
            'BB' => "Barbados",
            'BY' => "Belarus",
            'BE' => "Belgium",
            'BZ' => "Belize",
            'BJ' => "Benin",
            'BM' => "Bermuda",
            'BT' => "Bhutan",
            'BO' => "Bolivia",
            'BQ' => "Bonaire, Sint Eustatius and Saba",
            'BA' => "Bosnia and Herzegovina",
            'BW' => "Botswana",
            'BV' => "Bouvet Island",
            'BR' => "Brazil",
            'IO' => "British Indian Ocean Territory",
            'BN' => "Brunei Darussalam",
            'BG' => "Bulgaria",
            'BF' => "Burkina Faso",
            'BI' => "Burundi",
            'CV' => "Cabo Verde",
            'KH' => "Cambodia",
            'CM' => "Cameroon",
            'CA' => "Canada",
            'KY' => "Cayman Islands",
            'CF' => "Central African Republic",
            'TD' => "Chad",
            'CL' => "Chile",
            'CN' => "China",
            'CX' => "Christmas Island",
            'CC' => "Cocos (Keeling) Islands",
            'CO' => "Colombia",
            'KM' => "Comoros",
            'CD' => "Democratic Republic of the Congo",
            'CG' => "Congo",
            'CK' => "Cook Islands",
            'CR' => "Costa Rica",
            'CI' => "Ivory Coast",
            'HR' => "Croatia",
            'CU' => "Cuba",
            'CW' => "Curaçao",
            'CY' => "Cyprus",
            'CZ' => "Czechia",
            'DK' => "Denmark",
            'DJ' => "Djibouti",
            'DM' => "Dominica",
            'DO' => "Dominican Republic",
            'EC' => "Ecuador",
            'EG' => "Egypt",
            'SV' => "El Salvador",
            'GQ' => "Equatorial Guinea",
            'ER' => "Eritrea",
            'EE' => "Estonia",
            'ET' => "Ethiopia",
            'FK' => "Falkland Islands",
            'FO' => "Faroe Islands",
            'FJ' => "Fiji",
            'FI' => "Finland",
            'FR' => "France",
            'GF' => "French Guiana",
            'PF' => "French Polynesia",
            'TF' => "French Southern Territories",
            'GA' => "Gabon",
            'GM' => "Gambia",
            'GE' => "Georgia",
            'DE' => "Germany",
            'GH' => "Ghana",
            'GI' => "Gibraltar",
            'GR' => "Greece",
            'GL' => "Greenland",
            'GD' => "Grenada",
            'GP' => "Guadeloupe",
            'GU' => "Guam",
            'GT' => "Guatemala",
            'GG' => "Guernsey",
            'GN' => "Guinea",
            'GW' => "Guinea-Bissau",
            'GY' => "Guyana",
            'HT' => "Haiti",
            'VA' => "Holy See (Vatican City State)",
            'HN' => "Honduras",
            'HK' => "Hong Kong",
            'HU' => "Hungary",
            'IS' => "Iceland",
            'IN' => "India",
            'ID' => "Indonesia",
            'IR' => "Iran",
            'IQ' => "Iraq",
            'IE' => "Ireland",
            'IM' => "Isle of Man",
            'IL' => "Israel",
            'IT' => "Italy",
            'JM' => "Jamaica",
            'JP' => "Japan",
            'JE' => "Jersey",
            'JO' => "Jordan",
            'KZ' => "Kazakhstan",
            'KE' => "Kenya",
            'KI' => "Kiribati",
            'KP' => "Democratic People's Republic of Korea",
            'KR' => "Republic of Korea",
            'KW' => "Kuwait",
            'KG' => "Kyrgyzstan",
            'LA' => "Lao People's Democratic Republic",
            'LV' => "Latvia",
            'LB' => "Lebanon",
            'LS' => "Lesotho",
            'LR' => "Liberia",
            'LY' => "Libya",
            'LI' => "Liechtenstein",
            'LT' => "Lithuania",
            'LU' => "Luxembourg",
            'MO' => "Macao",
            'MK' => "Macedonia",
            'MW' => "Malawi",
            'MY' => "Malaysia",
            'MV' => "Maldives",
            'ML' => "Mali",
            'MT' => "Malta",
            'MQ' => "Martinique",
            'MR' => "Mauritania",
            'MU' => "Mauritius",
            'YT' => "Mayotte",
            'MX' => "Mexico",
            'FM' => "Micronesia",
            'MD' => "Moldova",
            'MC' => "Monaco",
            'MN' => "Mongolia",
            'ME' => "Montenegro",
            'MS' => "Montserrat",
            'MA' => "Morocco",
            'MZ' => "Mozambique",
            'MM' => "Myanmar",
            'NA' => "Namibia",
            'NR' => "Nauru",
            'NP' => "Nepal",
            'NL' => "Netherlands",
            'NC' => "New Caledonia",
            'NZ' => "New Zealand",
            'NI' => "Nicaragua",
            'NE' => "Niger",
            'NG' => "Nigeria",
            'NU' => "Niue",
            'NF' => "Norfolk Island",
            'MP' => "Northern Mariana Islands",
            'NO' => "Norway",
            'OM' => "Oman",
            'PK' => "Pakistan",
            'PW' => "Palau",
            'PS' => "State of Palestine",
            'PA' => "Panama",
            'PG' => "Papua New Guinea",
            'PY' => "Paraguay",
            'PE' => "Peru",
            'PH' => "Philippines",
            'PN' => "Pitcairn",
            'PL' => "Poland",
            'PT' => "Portugal",
            'PR' => "Puerto Rico",
            'QA' => "Qatar",
            'RE' => "Réunion",
            'RO' => "Romania",
            'RU' => "Russian Federation",
            'RW' => "Rwanda",
            'BL' => "Saint Barthélemy",
            'SH' => "Saint Helena, Ascension and Tristan da Cunha",
            'KN' => "Saint Kitts and Nevis",
            'LC' => "Saint Lucia",
            'MF' => "Saint Martin (French part)",
            'PM' => "Saint Pierre and Miquelon",
            'VC' => "Saint Vincent and the Grenadines",
            'WS' => "Samoa",
            'SM' => "San Marino",
            'ST' => "Sao Tome and Principe",
            'SA' => "Saudi Arabia",
            'SN' => "Senegal",
            'RS' => "Serbia",
            'SC' => "Seychelles",
            'SL' => "Sierra Leone",
            'SG' => "Singapore",
            'SX' => "Sint Maarten",
            'SK' => "Slovakia",
            'SI' => "Slovenia",
            'SB' => "Solomon Islands",
            'SO' => "Somalia",
            'ZA' => "South Africa",
            'GS' => "South Georgia and the South Sandwich Islands",
            'SS' => "South Sudan",
            'ES' => "Spain",
            'LK' => "Sri Lanka",
            'SD' => "Sudan",
            'SR' => "Suriname",
            'SJ' => "Svalbard and Jan Mayen",
            'SZ' => "Swaziland",
            'SE' => "Sweden",
            'CH' => "Switzerland",
            'SY' => "Syrian Arab Republic",
            'TW' => "Taiwan",
            'TJ' => "Tajikistan",
            'TZ' => "Tanzania",
            'TH' => "Thailand",
            'TL' => "Timor-Leste",
            'TG' => "Togo",
            'TK' => "Tokelau",
            'TO' => "Tonga",
            'TT' => "Trinidad and Tobago",
            'TN' => "Tunisia",
            'TR' => "Turkey",
            'TM' => "Turkmenistan",
            'TC' => "Turks and Caicos Islands",
            'TV' => "Tuvalu",
            'UG' => "Uganda",
            'UA' => "Ukraine",
            'AE' => "United Arab Emirates",
            'GB' => "United Kingdom",
            'US' => "United States",
            'UM' => "United States Minor Outlying Islands",
            'UY' => "Uruguay",
            'UZ' => "Uzbekistan",
            'VU' => "Vanuatu",
            'VE' => "Venezuela",
            'VN' => "Viet Nam",
            'VI' => "British Virgin Islands",
            'VI' => "Virgin Islands, U.S.",
            'WF' => "Wallis and Futuna",
            'EH' => "Western Sahara",
            'YE' => "Yemen",
            'ZM' => "Zambia",
            'ZW' => "Zimbabwe"
    ];       


        private $filename;      // holds name of the file being analyzed
        
        
    function __construct() {
        //echo "starting parser\n";
    }
    
    /**
     * Function to analyze a file depending on its extension
     * 
     * @param string $file FQDN of the file to analyze
     * @param array  $configuration Array that contains the configuration data of a specific "document"
     * @param string $extension It is the extension of the file
     * @return array $parsedData
     *         false in case an error occurred
     */
    public function analyzeFile($file, $configuration, $extension) {
        switch($extension) {
            case "xlsx":
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . "analyzing xlsx file";
                }
                $tempArray = $this->analyzeFileExcel($file, $configuration);
                break;
            case "xls":
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . "analyzing xls file";
                }
                $tempArray = $this->analyzeFileExcel($file, $configuration);
                break;
            case "csv":
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . "analyzing csv file";
                }
                $tempArray = $this->analyzeFileCSV($file, $configuration);
                break;
            case "json":
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . "analyzing json file";
                }
                $tempArray = $this->analyzeFileJson($file, $configuration);
                break;
            case "html":
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . "analyzing html file";
                }
                $tempArray = $this->analyzeFileHtml($file, $configuration);
                break;
        }
        return $tempArray;
    }



    /**
     * Starts the process of analyzing the file and returns the results as an array
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return array           $parsedData
     *          false in case an error occurred
     */
    public function analyzeFileExcel($file, $configuration) {
echo "INPUT FILE = $file \n";
        $this->filename = $file;
echo __FUNCTION__ . " " . __LINE__ . " Memory = " . memory_get_usage (false)  . "\n";
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        ini_set('memory_limit','4096M');
        $sheet = $objPHPExcel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        //In the future to clean empty cells
        //https://stackoverflow.com/questions/24936905/phpexcel-finding-first-column-with-blank-cell
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $datas = $this->saveExcelToArray($sheetData, $configuration, $this->config['offsetStart']);
        $objPHPExcel->disconnectWorksheets();
        $objPHPExcel->garbageCollect();
        unset($objPHPExcel);
        return $datas;
    }
    
    public function analyzeFileCSV($file, $configuration) {
        //$command = "iconv -f cp1250 -t utf-8 " . $file " > " $file ";
        $inputFileType = 'CSV';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setDelimiter($this->config['separatorChar']);
        $objPHPExcel = $objReader->load($file);
        ini_set('memory_limit','2048M');
        $sheet = $objPHPExcel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        echo " Number of rows = $highestRow and number of Columns = $highestColumn \n";
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $datas = $this->saveExcelToArray($sheetData, $configuration, $this->config['offsetStart']);
        $objPHPExcel->disconnectWorksheets();
        $objPHPExcel->garbageCollect();
        unset($objPHPExcel, $objReader);
        return $datas;
    }
    
    /**
     * Starts the process of analyzing the file and returns the results as an array
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return array           $parsedData
     *          false in case an error occurred
     */
    public function analyzeFileJson($file, $configuration) {        
        $fileString = file_get_contents($file);
        $data = json_decode($fileString, true);
        return $this->saveExcelToArray($data, $configuration, $this->config["offsetStart"]);
    }


    /**
     * Analyze the received data using the configuration data and store the result
     * in an array
     *
     * @param string $rowDatas  the excel data in an array.
     * @param string $values    The values from which we take the data
     * @param int $totalRows    last row written, we need it for offsetEnd.
     * @return array $temparray the data after the parsing process.
     *
     */
    private function saveExcelToArray(&$rowDatas, $values, $totalRows) {     
        $tempArray = [];
        $maxRows = count($rowDatas);

        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $this->config['offsetStart']) {
                break;
            }

            unset($rowDatas[$key]);
            $i++;
        }

        echo "totalRows = $maxRows\n";
     
        for ($i = $maxRows; $i > 0; $i--) {
            if (empty($rowDatas[$i]["A"])) {
                unset($rowDatas[$i]);
            }
        }   
 
        // remove items at from the end of the array
        for ($i == 0; $i < $this->config['offsetEnd']; $i++) {
            array_pop($rowDatas);
        }
   
        $i = 0;
        $outOfRange = false;
        foreach ($rowDatas as $rowData) {
            foreach ($values as $key => $value) {
                $previousKey = $i - 1;
                $currentKey = $i;

                // check for subindices and construct them
                if (array_key_exists("name", $value)) {     
                    $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $value['name']) . "']";
                    $tempString = $finalIndex  . "= '" . $rowData[$key] .  "'; ";
                    eval($tempString);                   
                }
                else {          // "type" => .......
                    foreach ($value as $myKey => $userFunction ) {
                        if (!array_key_exists('inputData',$userFunction)) {
                            $userFunction['inputData'] = [];
                        }
                        else {  // input parameters are defined in config file
                        // check if any of the input parameters require data from
                        // another cell in current row, or from the previous row
                            foreach ($userFunction["inputData"] as $keyInputData => $input) {   // read "input data from config file
                                if (!is_array($input)) {        // Only check if it is a "string" value, i.e. not an array
                                    if (stripos ($input, "#previous.") !== false) {
                                        if ($previousKey == -1) {
                                            $outOfRange = true;
                                            break;
                                        }
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$previousKey][$temp[1]];
                                    }
                                    if (stripos ($input, "#current.") !== false) {
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$currentKey][$temp[1]];
                                    }
                                }
                            }
                        }

                        array_unshift($userFunction['inputData'], trim($rowData[$key]));       // Add cell content to list of input parameters
                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array(__NAMESPACE__ .'Fileparser',
                                                                       $userFunction['functionName']),
                                                                       $userFunction['inputData']);

                            if (is_array($tempResult)) {                                
                                $userFunction = $tempResult;
                                $tempResult = $tempResult[0];
                            }

                            // Write the result to the array with parsing result. The first index is written
                            // various variables if $tempResult is an array
                            if (!empty($tempResult)) {                             
                                $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $userFunction["type"]) . "']";
                                $tempString = $finalIndex  . "= '" . $tempResult .  "';  ";
                                eval($tempString);
                            }
                        }
                        else {
                             $outOfRange = false;        // reset
                        }
                    }
                }
            }  

            $countSortParameters = count($this->config['sortParameter']);
            switch ($countSortParameters) {
                case 1:
                    $sortParam1 = $tempArray[$i][$this->config['sortParameter'][0]];     
                    $tempArray[$sortParam1][] = $tempArray[$i];
                    unset($tempArray[$i]); 
                break; 
            
                case 2:
                    $sortParam1 = $tempArray[$i][$this->config['sortParameter'][0]];
                    $sortParam2 = $tempArray[$i][$this->config['sortParameter'][1]];        
                    $tempArray[$sortParam1][$sortParam2][] = $tempArray[$i];
                    unset($tempArray[$i]);
                break;               
            }
            $i++;
            
        }
        if ($this->config['changeCronologicalOrder'] == YES) {                      // inverse the order of the records
            return(array_reverse($tempArray));
        }
        return $tempArray;    
    }



    /**
     * Returns the progress indicator in the form of a simple string, like 3/17
     * Both input variables *should* be integer values.
     * 
     * @param   string  $input   Content of row   (=dummy variable)
     * @param   int     $divident
     * @param   int     $divisor

     * @return  string
     *
     * example:  getProgressString(12,27,0)   => 12/27
     * 
     */
    private function getProgressString($input, $divident, $divisor)  {
        return $divident . "/" . $divisor;
    }
    
    
    /**
     * Returns the quotient * 100 of a division. This represents the %
     * 
     * @param   string  $input   Content of row
     * @param   int     $divident
     * @param   int     $divisor
     * @param   int     $precision Number of decimals
     * @return
     *
     * example:  DivisionInPercentage(12,27,0)   => 44
     *           DivisionInPercentage(12,27,1)   => 44.4
     */
    private function divisionInPercentage($input, $divident, $divisor, $precision)  {
        if($divisor == 0 || ($divisor <! 0 && $divisor >! 0)){
            return 0;
        }
        return round(($divident * 100 )/$divisor, $precision, PHP_ROUND_HALF_UP);
    }
    
    /**
     *
     * 	Extracts the percentage as an integer from an input string
     *
     * 	@param 		string	$inputPercentage in string format like 5,4% or 5,43% or 5%. 
     *                          Note that 1,23% generates 123 and 33% -> 3300
     * 				5,5% TAE -> 550
     * 				7,02% -> 702
     *                          8,5 % -> 850
     *                          format like 'This is a string 54%' -> 5400
     * 
     * 	@return 	int		$outputPercentage
     * 	
     */
    function getPercentage($inputPercentage) {
        
        $progress = trim(preg_replace('/\D/', ' ', $inputPercentage));
        $tempValues = explode(" ", $progress);

        if (strlen($tempValues[1]) == 1) {
            $tempValues[1] = $tempValues[1] * 10;
        }

        $outputPercentage = $tempValues[0] * 100 + $tempValues[1];
        if ($inputPercentage < 0) {
            return -$outputPercentage;
        } else {
            return $outputPercentage;
        }
    }


    /**
     * Returns information of the last occurred error. Can also detect if
     * an unknown "payment" concept was found.
     * 
     *  @return JSON   
     *         
     */
    public function getLastError()  {
        $this->errorData['file'] = $this->filename;
        return json_decode($this->errorData);
    }

    
    /**
     * Sets one or more configuration parameters.
     * The following parameters can be configured:
     *
     *  sortParameter   The name of variable by which the array is to be sorted. The contents of the variable is used as index key
     *                  No default value defined
     *  separatorChar   default value = ";". This parameter is only useful for "csv" files
     *  offsetStart     The number of lines (=rows) from the TOP OF THE FILE which are not to be included in parser
     *                  Default value = 1
     *  offsetEnd       The number of lines (=rows) from, counted from the BOTTOM OF THE FILE which are not to be included in parser
     *                  Default value = 0
     * @param   array   $configurations     list of configuration parameter
     * @return  boolean OK
     *
     */
    public function setConfig($configurations)  {
        
        foreach ($configurations as $configurationKey => $configuration) {
            $this->config[$configurationKey] = $configuration;          // avoid deleting already specified config parameters
        }
        return;
    }

    
    /*
     * Translate the country name to its corresponding 2 letter ISO code
     * Example:
     * "Spain"   => ES
     * "France"  => FR
     * 
     * @param   $input      string of name of country (in english)
     * @return  $result     2-letter ISO code if country
     *
     */
    public function getCountry($input)  {
        $result = "XX";                 // unknown country
        
        foreach ($this->countries as $countryKey => $country) {
            if ($country == $input) {
                $result = $countryKey;
            break;
            }
        }
        return $result;
    }
   

    /**
     * Reads the current configuration parameter(s).
     *
     */
    public function getConfig()  {
        return($this->config);
    }

    
    /**
     * Analyze and determine the (preliminary) scope of an undefined payment concept.
     * The algorithm uses a combination of a dictionary search and checking of transaction data
     * to determine (at least) if it is an income or a cost.
     *
     * @param string
     * @param string
     *
     * @return string
     *
     */
    private function analyzeUnknownConcept($input, $config = null) {
        $result = 0;

        foreach ($this->dictionaryWords as $wordKey => $word) {
            $position = stripos($input, $wordKey);
            if ($position !== false) {      // A match was found
                $result = $word;
                break;
            }
        }

        switch($result) {
            case WIN_CONCEPT_TYPE_COST:                                         // A result was found.
                return "Unknown_cost";
                break;

            case WIN_CONCEPT_TYPE_INCOME:                                       // A result was found
                return "Unknown_income";
                break;
            default:                                                            // Nothing found, so do some maths to
                return "Unknown_concept";                                       // see if it is an income or a cost.
        }
    }


    /**
     * Converts any type of date format to internal format: yyyy-mm-dd
     *
     * @param string $date
     * @param string $currentFormat:  Y = 4 digit year, y = 2 digit year
     *                                M = 2 digit month, m = 1 OR 2 digit month (no leading 0)
     *                                D = 2 digit day, d = 1 OR 2 digit day (no leading 0)
     *
     * @return string   date in format yyyy-mm-dd
     *
     */
    private function normalizeDate($date, $currentFormat) {
        $internalFormat = $this->multiexplode(array(":", " ", ".", "-", "/"), $currentFormat);
        (count($internalFormat) == 1 ) ? $dateFormat = $currentFormat : $dateFormat = $internalFormat[0] . $internalFormat[1] . $internalFormat[2];
        $tempDate = $this->multiexplode(array(":", " ", ".", "-", "/"), $date);

        if (count($tempDate) == 1) {
           return;
        }

        $finalDate = array();

        $length = strlen($dateFormat);
        for ($i = 0; $i < $length; $i++) {
            switch ($dateFormat[$i]) {
                case "d":
                    $finalDate[2] = $this->norm_date_element($tempDate[$i]);
                break;
                case "D":
                    $finalDate[2] = $tempDate[$i];
                break;
                case "m":
                    $finalDate[1] = $this->norm_date_element($tempDate[$i]);
                break;
                case "M":
                    $finalDate[1] = $tempDate[$i];
                break;
                case "y":
                    $finalDate[0] = "20" . $tempDate[$i];
                break;
                case "Y":
                    $finalDate[0] = $tempDate[$i];
                break;
            }
        }

        $returnDate = $finalDate[0] . "-" . $finalDate[1] . "-" . $finalDate[2];
        list($y, $m, $d) = array_pad(explode('-', $returnDate, 3), 3, 0);

        if (ctype_digit("$y$m$d") && checkdate($m, $d, $y)) {                           // check if date is a real date according to internal format
            return $returnDate;
        }
        return;
    }


    /**
     * Normalize a day or month element of a date to two (2) characters, adding a 0 if needed
     *
     * @param string $val  Value to be normalized to 2 digits
     * @return string
     *
     */
    private function norm_date_element($val) {
	if ($val < 10) {
		return (str_pad($val, 2, "0", STR_PAD_LEFT));
	}
	return $val;
    }

    /** 
     * 
     * This function uses the bcmath package of PHP.
     * Format is converted to internal format, which is using the "." as a decimal separator, and
     * the thousands separator is removed
     * 
     * 
     * 
     * Gets an amount. The "length" of the number is determined by the required number
     * of decimals. If there are more decimals then required, the number is truncated and rounded
     * else 0's are added.
     * Examples:
     * getAmount("1.234,56789€", ".", ",", 3) => 1234568
     * getAmount("1234.56789€", "", ".", 7) => 12345678900
     * getAmount("1,234.56 €", ",", ".", 2) => 123456
     * @param string    $input      
     * @param string  $thousandsSep character that separates units of 1000 in a number
     * @param string  $decimalSep   character that separates the decimals
     * 
     * @param int     $decimals     number of required decimals in the amount to be returned   NOT NEEDED, TO BE DELETED
     * @return string    represents the amount, including a decimal separator (= ".") in case of decimals
     *
     */  
    private function getAmount($input, $thousandsSep, $decimalSep, $decimals = null) {
        if ($decimalSep == ".") {
            $separator = "\.";
        }        
        else {                                                              // seperator =>  ","
            $separator = ",";
        }
        if (empty($input) || $input == 0 && ($input <! 0 && $input >! 0)){ // decimals like 0,0045 are true in $input == 0
            $input = "0.0";
            $separator = "\.";
        }
        if (strpos($input, "E") || strpos($input, "e")){
            if(strpos($input, "E")){
                $char = "E";
            } else {
                $char = "e";
            }
            
            if(strpos($input, "-")){              
                $decArray = explode($char, $input);
                $dec = preg_replace("/[-]/", "", $decArray[1]);
                $dec2 =  strlen((string)explode(".", $decArray[0])[1]);             
                $input = strtr($input, array(',' => '.'));    
                $input = number_format(floatval($input), $dec + $dec2);
            } else{
                $input = strtr($input, array(',' => '.'));    
                $input = number_format(floatval($input), 0);
            }
            $separator = "\.";
        }       
        $allowedChars =  "/[^0-9" . $separator . "]/";
        $normalizedInput = preg_replace($allowedChars, "", $input);         // only keep digits, and decimal seperator
        $normalizedInputFinal = preg_replace("/,/", ".", $normalizedInput);
        return $normalizedInputFinal;
    }
    

    /**
     *
     * Determines the 'transactiondetail' based on a translationtable (=$config) and the sign of the amount ( positive or negative).
     * Note that the amount must already have been calculated before executing this function
     *
     * @param string    $input          Represents an amount with - 
     * @param string    $originalConcept
     * @param array     $config             Translation table
     * @return array    [0] => Winvestify standardized concept
     *                  [1] => array of parameter, i.e. list of variables in which the result
     *                         of this function is to be stored. In practice it is normally
     *                         only 1 variable, but the same value could be replicated in many
     *                         variables.
     *                  The variable name is read from "internal variable" $this->transactionDetails.
     */   
    private function getComplexTransactionDetail($input, $originalConcept, $config) {

        $type = WIN_CONCEPT_TYPE_INCOME;
// take care of amounts in scientific notation
        
        $input = strtoupper($input);
        $sciencePosition = strpos($input, "E");
        if ($sciencePosition !== false) {
            $input = $this->getAmount($input, "", ".", 16);   
            $temp = explode("-", $input);
            if (count($temp) == 3) {
                $type = WIN_CONCEPT_TYPE_COST;
            }
        }
        else {
            $position = strpos($input, "-");        
            if ($position !== false) {      // contains - sign 
                $type = WIN_CONCEPT_TYPE_COST;
            }    
        }
        
        $found = NO;        
        foreach ($config as $configKey => $item) {
            $configItemKey = key($item);
            $configItem = $item[$configItemKey];
            foreach ($this->transactionDetails as $key => $detail) {  
                $position = strpos($originalConcept, $configItemKey );
                if ($position !== false) {
                    if ($detail['detail'] == $configItem){
                        if ($type == $detail['transactionType']) {
                            $internalConceptName = $detail['type'];
                            $found = YES;
                            break 2;
                        }
                    }
                }
            }
        }        
          
        if ($found == YES) {
            $result = array($internalConceptName,"type" => "internalName");
            return $result;
        }
        else {
            echo "unknown concept [$input] for complex, so start doing some guessing for concept '$originalConcept'\n";  
        }
    } 
    

    
    
    /**
     * Translates the currency to internal representation.
     * The currency can be the ISO code or the currency symbol.
     * Not full-proof as many currencies share the $ sign
     *
     * @param string $loanCurrency
     * @return integer  constant representing currency
     *
     */
    private function getCurrency($loanCurrency) {

        $filter = array(".", ",", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", " ");
        $currencySymbol = str_replace($filter, "", $loanCurrency);

        foreach ($this->currencies as $currencyIndex => $currency) {        
            if (strpos($loanCurrency, $currency[0]) != false || $currencySymbol == $currency[0]) {              // check the ISO code
              return $currencyIndex;
            }
            if (strpos($loanCurrency, $currency[1]) != false || $currencySymbol == $currency[1]) {              // check the symbol
              return $currencyIndex;
            }
        }
    }

    /**
     *  Extracts the loanId from the file, and if no loanId exists, a global loanId will be generated.
     *
     * @param string    $input
     * @return string   $extractedString
     *
     */
    private function getHash($input) {
        return  hash ("md5", $input, false);
    }    

    /**
     *
     * Reads the transaction detail of the transaction operation and the variable where to store
     * the result of this function
     *
     * @param string   $input
     * @return array    [0] => Winvestify standardized concept
     *                  [1] => array of parameter, i.e. list of variables in which the result
     *                         of this function is to be stored. In practice it is normally
     *                         only 1 variable, but the same value could be replicated in many
     *                         variables.
     *                  The variable name is read from "internal variable" $this->transactionDetails.
     */
    private function getTransactionDetail($input, $config) {

        foreach ($config as $configKey => $item) {
            $configItemKey = key($item);
            $configItem = $item[$configItemKey];

            foreach ($this->transactionDetails as $key => $detail) { 
                $position = strpos($input, $configItemKey);
                if ($position !== false) {   
                    if ($detail['detail'] == $configItem){
                        $internalConceptName = $detail['type'];
                        $found = YES;
                        break 2;
                    }
                }
            }
        }        
        if ($found == YES) {
            $result = array($internalConceptName,"type" => "internalName");
            return $result;
        }
        else {
            echo "unknown concept, so start doing some Guessing for concept $input\n";  
         // an unknown concept was found, do some intelligent guessing about its meaning
            $result = $this->analyzeUnknownConcept($input);          // will return "unknown_income" or unknown_cost"
            
            // collect error information 
            unset($errorMsg);
            $errorMsg['input'] = $input;
            $errorMsg['config'] = $config;
            $this->errorData = $errorMsg;

            return $result;           
        }     
    }
    
    /**
     * Function to get details of transaction but when it is needed to get the content from
     * multiples columns of the file
     * 
     * @param string $input It is the column value
     * @param array $config Winvestify standardized concept
     * @param array $inputValues Values needed to calculate transaction details from other columns
     * @return array  [0] => Winvestify standardized concept
     *                [1] => array of parameter, i.e. list of variables in which the result
     *                         of this function is to be stored. In practice it is normally
     *                         only 1 variable, but the same value could be replicated in many
     *                         variables.
     *                  The variable name is read from "internal variable" $this->transactionDetails.
     */
    private function getMultipleInputTransactionDetail($input, $config, ...$inputValues) {
        foreach ($inputValues as $inputValue) {
            $input .= " " . $inputValue ;
        }
        return $this->getTransactionDetail(trim($input), $config);
    }

    /**
     * Search for something within a string, starting AFTER $search
     * and ending when $separator is found
     * If $search == "" then $extractedString starts from beginning of $input.
     * If $separator = "" then $extractedString contains the $input starting from $search to end
     *
     * @param string    $input      It is the string in which we search the information
     * @param string    $search     The character to search. 
     * @param string    $separator  The separator character
     * @param int       $mandatory  Indicates if it is mandatory that $search exists. 
     *                              If mandatory is 1 and it does not exist then the function will return 
     *                              a string of format "global_xxxxxx" with xxxxxx being a random number
     *                              If mandatory is 2 and it does exists, then the function will return 
     *                              a string of format "global_xxxxxx" with xxxxxx being a random number
     * @return string   $extractedString    The value we were looking for
     *
     */
    private function extractDataFromString($input, $search, $separator, $mandatory = 0) {

        $position = stripos($input, $search);
        if ($position !== false) {  // == TRUE
            if ($mandatory == 2){    
                return "global_" . mt_rand();
            }
            $start = $position;
            $length = strlen($search);
        }
        else { // FALSE
            $start = 0;
            $length = 0;
            
            if ($mandatory == 1){    
                return "global_" . mt_rand();
            }
        }       

        $position1 = stripos($input, $separator);
        if ($position1 !== false) {  // == TRUE
            $length1 = $position1;
        }
        else { // FALSE
            $length1 = 100;                 // ficticious value
        }       
        $start = $start + $length;
        $finish = $length1 - $start;
        return substr($input, $start, $finish) ;
    }

    /**
     *
     * Reads a field from a row. Note that the field must be
     * a "calculated" field, i.e it must be defined in the config file
     *
     * @param string    $input   cell data
     * @param array     $field   field to read
     * @param boolean   overwrite     overwrite current value of the $input
     *
     */
    private function getRowData($input, $field, $overwrite) {

        if (empty($input)) {
            return $field;
        }
        else {
            if ($overwrite) {
                return $field;
            }
        }
         return "";
    }


    
    /**
     *
     * Reads a default value from the config file
     *
     * @param string    $input          cell data [Not used]
     * @param array     $defaultValue   The value to be returned
     *
     */
    private function getDefaultValue($input, $defaultValue) {
        return $defaultValue;
    }    
    
    
  
    /**
     * Function to get the loanId from the file name of one amortization table
     * 
     * @param array $delimiters     array with all the delimiter characters
     * @param string                input string
     *  
     * @return array 
     */    
    private function multiexplode ($delimiters, $string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }
    
    /**
     * Function to analyze a file depending on its extension
     * 
     * @param string $filePath FQDN of the file to analyze
     * @param array  $parserConfig Array that contains the configuration data of a specific "document"
     * @param string $extension It is the extension of the file
     * @return array $parsedData
     *         false in case an error occurred
     */
    public function analyzeFileAmortization($filePath, $parserConfig, $extension) {
        
        switch($extension) {
            case "html":
                $tempArray = $this->analyzeFileHtml($filePath, $parserConfig);
                break;
        }
        return $tempArray;
    }
    
    /**
     * Function to analyze a html file to get its content
     * 
     * @param string $filePath FQDN of the file to analyze
     * @param array  $parserConfig Array that contains the configuration data of a specific "document"
     * @return array $parsedData
     *         false in case an error occurred
     */
    public function analyzeFileHtml($filePath, $parserConfig) {
        $dom = new DOMDocument();
        $dom->loadHTMLFile($filePath);
        $trs = $dom->getElementsByTagName('tr');
        $tempArray = [];
        $i = 0;

        foreach ($trs as $tr) {
            if ($i == $this->config['offsetStart']) {
                break;
            }
            $tr->parentNode->removeChild($tr);
            $i++;
        }

        $trsNumber = $trs->length - 1;
        for ($i = $trsNumber; $i > $trsNumber-$this->config['offsetEnd']; $i--) {
            $tr = $trs[$i]->parentNode->lastChild;
            $tr->parentNode->removeChild($tr);
        }

        $i = 0;
        foreach ($trs as $tr) {
            $tds = $tr->getElementsByTagName('td');
            $keyTr = 0;
            $outOfRange = false;
            foreach ($parserConfig as $key => $value) {
 //               echo "value";
//                print_r($value);
                $previousKey = $i - 1;
                $currentKey = $i;
                $valueTd = trim($tds[$key]->nodeValue);
//                echo "tdValue";
//                print_r($valueTd);
                if (array_key_exists("name", $value)) {      // "name" => .......
                    $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $value['name']) . "']";
                    $tempString = $finalIndex  . "= '" . $valueTd .  "'; ";
                    eval($tempString);
                }
                else {
                    foreach ($value as $userFunction ) {
                        if (!array_key_exists('inputData',$userFunction)) {
                            $userFunction['inputData'] = [];
                        }
                        else {
                            foreach ($userFunction["inputData"] as $keyInputData => $input) {   // read "input data from config file
//                                print_r($input);
                                if (!is_array($input)) {        // Only check if it is a "string" value, i.e. not an array
                                    if (stripos ($input, "#previous.") !== false) {
                                        if ($previousKey == -1) {
                                            $outOfRange = true;
                                            break;
                                        }
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$previousKey][$temp[1]];
                                    }
                                    if (stripos ($input, "#current.") !== false) {
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$currentKey][$temp[1]];
                                    }
                                }
                            }
                        }
                        array_unshift($userFunction['inputData'], $valueTd);       // Add cell content to list of input parameters
 //                       print_r($userFunction['inputData']);
                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array($this,
                                                                       $userFunction['functionName']),
                                                                       $userFunction['inputData']
                                    );
//                            print_r($tempResult);
                            if (is_array($tempResult)) {
                                $userFunction = $tempResult;
                                $tempResult = $tempResult[0];
                            }

                            // Write the result to the array with parsing result. The first index is written
                            // various variables if $tempResult is an array
                            if (!empty($tempResult)) {
                                $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $userFunction["type"]) . "']";
                                $tempString = $finalIndex  . "= '" . $tempResult .  "';  ";
                                eval($tempString);
                            }
                        }
                        else {
                            $outOfRange = false;        // reset
                        }
                    }
                }
            }
            $keyTr++;
            $i++;
        }
        return $tempArray;
    }
    
    /**
     * Documentantion needed
     * https://github.com/PHPOffice/PHPExcel/blob/1.8/Documentation/Examples/Reader/exampleReader04.php
     * https://github.com/PHPOffice/PHPExcel/blob/1.8/Documentation/Examples/Reader/exampleReader16.php
     * https://github.com/PHPOffice/PHPExcel/blob/1.8/Documentation/Examples/Reader/exampleReader17.php
     * https://github.com/PHPOffice/PHPExcel/blob/1.8/Documentation/Examples/Reader/exampleReader18.php
     * https://github.com/PHPOffice/PHPExcel/blob/1.8/Documentation/Examples/Reader/exampleReader19.php
     */
    public function analyzeFileBySheetName($file, $configuration) {
        $inputFileType = PHPExcel_IOFactory::identify($file);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $worksheetNames = $objReader->listWorksheetNames($file);
        $datas = "";
        if (in_array($this->config['sheetName'], $worksheetNames)) {
            $objReader->setLoadSheetsOnly($this->config['sheetName']); 
            $objPHPExcel = $objReader->load($file);
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            echo __FILE__ . " " . __LINE__ . " Number of rows = $highestRow and number of Columns = $highestColumn \n";
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            $datas = $this->saveExcelToArray($sheetData, $configuration, $this->config['offsetStart']);
        }
        return $datas;
    }
    
    /**
     * Function to join values together
     * 
     * @param string $input
     * @param string $joinSeparator
     * @param string $order It could be FIFO or LIFO
     * @param array $inputValues All the values we want to join together
     * @return string
     */
    public function joinDataCells($input, $joinSeparator, $order, ...$inputValues) {
        if ($order == FIFO) {
            foreach ($inputValues as $inputValue) {
                $input .= $joinSeparator . trim($inputValue);
            }
        }
        else if ($order == LIFO) {
            foreach ($inputValues as $inputValue) {
                $inputNew .= trim($inputValue) . $joinSeparator ;
            }
            $inputNew .= $input;
            $input = $inputNew;
        }
        
        return trim($input);
    }
    
    /**
     * Function to clean a string of unnecessary characters
     * 
     * @param string $input cell data
     * @param array $charactersToClean Array of chars to clean
     * @return string Cleaned value to be returned
     */
    private function cleanStringInput($input, ...$charactersToClean) {
        $input = str_replace($charactersToClean, "", $input);
        return trim($input);
    }
    
    
    
   /**
     *
     * Reads the characteristics of a concept
     *
     * @param string   $input       Not relevant
     * @param string   $search      Can either be the "detail" or the "type" index of the array "tranactionDetails"
     * 
     * @return string  space delimited set of characteristics, 0,1 or more
     *
     */
    private function getConceptChars($input, $search) {
        foreach ($this->transactionDetails as $detail) { 
            if ($detail['detail'] == $search) {
                return $detail['chars'];
            }
            if ($detail['type'] == $search) {  
                return $detail['chars'];
            }
        }
        return "";  // empty string, no characteristics found
    }   
    
    
    /**
     * Generates a 'uuid' string
     +
     * @return string   
     */
    private function guidv4()
    {
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }   
    

    /**
     * Return an "ID". This is the contents of the cell or an id as generated by the system.
     *
     * @param string    $input
     * @param string    $prefix     Optional prefix which which the "id" is to start
     * @param string    $algorithm  The algorithm to be used for generating the ID 
     * @return string   $id
     */
    private function generateId($input, $prefix = "", $algorithm = ""){
        if (!empty($input))  {
           return $input;
        }
        
        switch ($algorithm) {
            case "md5":
                return $prefix . hash ("md5", $input, false);
            break;
        
            case "rand":
                return $prefix . mt_rand();
            break;
        
            case "uuid":
                return $prefix . $this->guidv4();
            break;
        }
    }
    
    
    /**
     * Get the header for compare.
     * 
     * @param type $file
     * @param type $configParam
     * @return type
     */
    public function getFirstRow($file, $configParam) {
        $this->config = $configParam;
        $extension = $this->getExtensionFile($file);
        $inputType = $this->getInputFileType($extension);
        if (!empty($configParam[0]['chunkInit'])) {  //Multi sheet
            $data = $this->convertExcelMultiSheetByParts($file, $inputType);
            echo "HEADER IS: ";
            print_r($data);

            return $data;
        } else { //Simple sheet
            $data = $this->convertExcelByParts($file, $configParam["chunkInit"], $configParam["chunkSize"], $inputType);
            echo "HEADER IS: ";
            print_r(array_filter($data[1]));
            return $data[1];
        }
    }
      
    /**
     * Function to get the extension of a file
     * 
     * @param string $file FQDN of the file to analyze
     * @return string It is the extension of the file
     */
    public function getExtensionFile($file) {              
        $file = new File($file);
        $extension = $file->ext();            
        return $extension;
    }
    
    
    public function getInputFileType($extension) {
        
        switch($extension) {
            case "xlsx":
                $inputType = "Excel2007";
                break;
            case "xls":
                $inputType = "Excel5";
                break;
            case "csv":
                $inputType = "CSV";
                break;
        }
        return $inputType;
    }
    
    /**
     * Get an excel sheet by parts, from example from cell 1 to 500
     * 
     * @param string $filePath FQDN of the file to analyze
     * @param int $chunkInit The first cell from we start taking data
     * @param int $chunkSize The last cell from we finish taking data
     * @param string $inputFileType It is the extension
     * @return array
     */
    function convertExcelByParts($filePath, $chunkInit, $chunkSize, $inputFileType = null) {
        if (empty($inputFileType)) {
            $inputFileType = "Excel2007";
        }
        if (empty($chunkInit)) {
            $chunkInit = 1;
        }
        if (empty($chunkSize)) {
            $chunkSize = 500;
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        if($inputFileType == 'CSV'){
            echo $this->config['separatorChar'];
            $objReader->setDelimiter($this->config['separatorChar']);            
            $this->clearCsv($filePath);
        }
        /**  Create a new Instance of our Read Filter  **/
        $chunkFilter = new readFilterWinvestify();
        /**  Tell the Read Filter, the limits on which rows we want to read this iteration  **/
        $chunkFilter->setRows($chunkInit,$chunkSize);
        /**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
        $objReader->setReadFilter($chunkFilter);
     
        $objPHPExcel = $objReader->load($filePath);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        echo "sheetDAta <br>";   
        foreach($sheetData as $key => $value){
            if(empty($value)){
                unset($sheetData[$key]);
            }
        }
        $objPHPExcel->disconnectWorksheets();
        $objPHPExcel->garbageCollect();
        unset($objPHPExcel, $objReader);
        return $sheetData;
    }

    /**
     * Get all the data from different sheets of an excel
     * @param string $filePath FQDN of the file to analyze
     * @param string $inputFileType It is the extension
     * @return array
     */
    function convertExcelMultiSheetByParts($filePath, $inputFileType) {
        if (empty($inputFileType)) {
            $inputFileType = "Excel2007";
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $worksheetNames = $objReader->listWorksheetNames($filePath);
        foreach($this->config as $sheet){
            if (in_array($sheet['sheetName'], $worksheetNames)) {
                /**  Create a new Instance of our Read Filter  **/
               $chunkFilter = new readFilterWinvestify();
               /**  Tell the Read Filter, the limits on which rows we want to read this iteration  **/
               $chunkFilter->setRows($sheet['chunkInit'],$sheet['chunkSize']);
               /**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
               $objReader->setReadFilter($chunkFilter);
               
               //Read this sheet
               $objReader->setLoadSheetsOnly($sheet['sheetName']);
               
               $objPHPExcel = $objReader->load($filePath);
               $sheetData[] = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
           }
        }
        $objPHPExcel->disconnectWorksheets();
        $objPHPExcel->garbageCollect();
        unset($objPHPExcel, $objReader);
        return $sheetData;
    }
    
    /**
     * Function to clear a csv
     * 
     * @param string $filePath FQDN of the file to analyze
     */
    function clearCsv($filePath) {
            //WE MUST CLEAR CSV OF SPECIAL CHARACTERS
            $csv = fopen($filePath, "r");
            $csvString = mb_convert_encoding(fread($csv, filesize($filePath)), "UTF-8"); //Convert special characters
            fclose($csv);
            $csv = fopen($filePath, "w+");   //Rewrite old csv
            fwrite($csv,$csvString);
            fclose($csv);
    }
    
    /**
     * Function to set the finish date when we starting the process
     * 
     * @param string $defaultFinishDate It is the default date
     */
    function setDefaultFinishDate($defaultFinishDate) {
        $defaultFinishDate = date("Y-m-d", strtotime($defaultFinishDate));
        $this->defaultFinishDate = $defaultFinishDate;
    } 
    
    /**
     * Function to get a defaulted date for a company
     * 
     * @param string    $input          cell data [Not used]
     * @param array     $defaultValue   The value to be returned
     * @return type
     */
    function getDefaultDate($input, $defaultValue) {
        return $this->normalizeDate($this->defaultFinishDate,"Y-M-D");
    }
    
    /**
     * Function to clear the config variable with new values
     * 
     * @param array $config The new values
     */
    public function cleanConfig($config) {
        $this->config = $config;
    }


    /**
     * Function to manipulate a number
     * Example:
     *  21.903 -> 2090
     * 
     * @param string $input             The number to manipulate. It is assumed that only a *number* is received,
     *                                  with or without a "," or "."
     * @param string $multiplyFactor    The factor which shall be used to multiply the input
     * @param string $decimals          The (maximum) number of decimals that the end result may have
     * @param string $separator         Decimal separator, can be "," or ".".
     * @return  string                  The manipulated number as a string
     */
    public function handleNumber($input, $multiplyFactor, $decimals, $separator) {
        $cleanInput = preg_replace("/[^0-9,.-]/", "",$input);
        if($separator === "."){
           $cleanInput =  str_replace(",", "", $cleanInput);
        } 
        else if($separator === ","){
           $cleanInput =  str_replace(",", ".", str_replace(".", "",$cleanInput));
        }
        
         if (empty($cleanInput) || $cleanInput == 0 && ($cleanInput <! 0 && $cleanInput >! 0)){ // decimals like 0,0045 are true in $input == 0
            return "0.0";
        }

        $temp = bcmul((string)$cleanInput, (string)$multiplyFactor, $decimals);  
        return $temp;
    }    
}
