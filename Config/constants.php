<?php
/**
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2018, http://www.winvestify.com                         |
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
 * @version 0.3
 * @date 2018-06-01
 * @package
 */
/*
 * Global constants for all the controllers, models and views
 * 
 * 2018-06-01
 * File creation
 */

$config=[]; //Necesary to load the file

define('WIN_READONLY', 1); 
define('WIN_WRITEONLY', 2); 
define('WIN_READWRITE', 3);

define("NOT_ACTIVE", 0);
define("ACTIVE", 1);
//define("DELETED", 2);

if (!defined("HOUR")) {
    define("HOUR", 4);
}

if (!defined("DAY")) {
    define("DAY", 1);
}
if (!defined('MONTH')) {
    define('MONTH', 2);
}

define('WIN_JWT_DURATION', 400000000);                                               // Duration of the JWT for API clients
define('YEAR_CUARTER', 3);
define('YEAR_SEMESTER', 4);
define('4_MONTHS', 5);
define('YEAR_', 6);

define('TRUE', 1);
define('FALSE', 0);
define('LOAN_TO_PRIVATE_PERSON', 1);
define('LOAN_TO_COMPANY', 2);
define('FINANCING_TO_PRIVATE_PERSON', 4);
define('FINANCING_TO_COMPANY', 8);
define('ALLOW_LINKED_ACCOUNTS', 16);
define('IMPOSSIBLE_DATE', "9999-12-12");

//SPECIAL CHARACTERS
define('LINE_FEED', 10);

define('CHAT_ACTIVE', 1);
define('CHAT_TEMPORARY_NOT_ACTIVE', 2);
define('CHAT_NOT_ACTIVE', 3);
define('CHAT_INACTIVITY', 4);


// TYPE OF FINANCIAL PRODUCT
define('PAGARE', 1);
define('LOAN', 2);
define('FINANCING', 3);


// ACCOUNT DEFINITION PROGRESS	
define('UNCONFIRMED_ACCOUNT', 1);
define('CONFIRMED_ACCOUNT_WITH_DEFAULT_DATA', 2);
define('FOLLOWERS_DEFINED', 4);
define('QUESTIONAIRE_FILLED_OUT', 32);  // represents from here that the account is fully created
define('NEW_DEFAULT_PERSONAL_DATA', 128);
define('NEW_DEFAULT_INVESTMENT_DATA', 256);
define('NEW_DEFAULT_FOLLOWER_DATA', 512);


// METRICS	
define('TOTAL_NUMBER_OF_USERS', 1);
define('TOTAL_NUMBER_OF_ACTIVE_USERS', 2);
define('TOTAL_NUMBER_OF_USERS_WITH_LINKED_ACCOUNTS', 3);
define('NUMBER_OF_INVESTMENTS_PER_LINKED_ACCOUNT', 4);
define('TOTAL_NUMBER_OF_INVESTMENTS_PER_USER', 5);
define('NUMBER_OF_LINKED_ACCOUNTS_PER_USER', 6);
define('TOTAL_AMOUNT_INVESTED_PER_USER', 7);


// TYPE OF COUNTERS
define('ACCUMULATIVE_COUNTER', 1);
define('DELTA_COUNTER', 2);
define('LEVEL_COUNTER', 3);


// QUEUE TYPES
define('FIFO', 1);
define('LIFO', 2);


// QUEUE1 STATES
define('IDLE', 1);
define('WAITING_FOR_EXECUTION', 2);
define('EXECUTING', 3);
define('FINISHED', 4);


// NOTIFICATIONS STATES
define('WAITING_FOR_VISUALIZATION', 1);
define('READY_FOR_VISUALIZATION', 2);
define('READ_BY_USER', 3);
define('DELETED', 4);


// MAPPING OF 'PAYMENT TRANSACTION' STATE OF LOAN ACCORDING TO AMORTIZATION TABLE
define('TERMINATED_OK', -1);            // Investment has been successfully amortized according to predefined payment schedule
define('PENDING', 0);                   // First repayment has not yet occured as repayment date is in future
define('OK', 1);                        // Investment is being repayed according to predefined payment schedule
define('PAYMENT_DELAYED', 2);           // Investment repayments is BEHIND repayment schedule
define('DEFAULTED', 3);                 // User has defaulted on the loan and will NOT repay the full amount of the loan.
// Investment must be considered LOST
 

// DEFINITION OF WORK SECTORS				 // NOT ACTUALLY USED
define('EDUCATION', 110);
define('HEALTH_CARE', 120);
define('CIVIL_SERVANT', 150);
define('SOCIAL_SERVICES', 160);
define('ICT', 220);
define('AGRICULTURE', 311);
define('CONSTRUCTION', 323);
define('TURISM', 332);

define('ACCREDITED_INVESTOR', 2);
define('NOT_ACCREDITED_INVESTOR', 1);


// DEFINITION OF TYPE OF CROWDLENDING M0DALITIES
define('P2P', 1);
define('P2B', 2);
define('INVOICE_TRADING', 4);
define('CROWD_REAL_ESTATE', 8);
define('SOCIAL', 16);


// REGISTRATION PROGRESS WHEN USERS REGISTERS	
define('REGISTRATION_PROGRESS_1', 1);
define('REGISTRATION_PROGRESS_2', 2);
define('REGISTRATION_PROGRESS_3', 3);
define('REGISTRATION_PROGRESS_4', 4);
define('REGISTRATION_PROGRESS_5', 5);


//COMPANY SERVICE STATUS
define('SERVICE_INACTIVE', 1);
define('SERVICE_ACTIVE', 2);
define('SERVICE_SUSPENDED', 3);


//OCR STATUS
define('NOT_SENT', 0);
if (!defined("SENT")) {
    define('SENT', 1);
}
if (!defined("ERROR")) {
    define('ERROR', 2);
}

define('OCR_PENDING', 3);
define('OCR_FINISHED', 4);
define('FIXED', 5);


//OCR COMPANY STATUS
define('SELECTED', 0);
if (!defined('SENT')) {
    define('SENT', 1);
}
define('ACCEPTED', 2);
define('DENIED', 3);
define('DOWNLOADED', 4);


//CHECK DATA & FILES STATUS
define('UNCHECKED', 0);
define('CHECKED', 1);
if (!defined('ERROR')) {
    define('ERROR', 2);
}


//FILE OPTIONAL OR REQUIRED
define('OPTIONAL', 1);
define('REQUIRED', 0);


//CHECK STATUS(for winadmin investor data)
define('YES', 1);
define('NO', 2);
if (!defined("PENDING")) {
    define('PENDING', 0);
}


// CURL ERRORS
define('CURL_ERROR_TIMEOUT', 28);

// TYPES OF DASHBOARD RECORDS	
define('USER_GENERATED', 2);
define('SYSTEM_GENERATED', 1);


// DEFINED CURRENCIES
define('EUR', 1);           // Euro
define('GBP', 2);           // UK Pound Sterling
define('USD', 3);           // US Dollar
define('ARS', 4);           // Argentinian peso
define('AUD', 5);           // Australina dollar  
define('NZD', 6);           // New zeeland dollar                                            
define('BYN', 7);           // Belarusian ruble         
define('BGN', 8);           // Bulgarian lev   
define('CZK', 9);           // Czech koruna                                         
define('DKK', 10);          // Danish krone                                         
define('CHF', 11);          // Swiss franc                                             
define('MXN', 12);          // Mexican peso  
define('RUB', 13);          // Russian ruble 


// APPLICATION THAT CAN PRODUCE BILLING DATA
define('TALLYMAN_APP', 1);

// DOCUMENT TYPE(Files table)
define('DNI_FRONT', 1);
define('DNI_BACK', 2);
define('IBAN', 3);
define('CIF', 4);

// ROLES FOR USER
define('ROLE_SUPERADMIN', 1);
define('ROLE_WINADMIN', 2);
define('ROLE_PFPADMIN', 3);
define('ROLE_INVESTOR', 4);
define('ROLE_WINADMINTECH', 5);


// INVESTMENT TYPE
define('PROMISSORY_NOTE', 1);
if (!defined("LOAN")) {
    define('LOAN', 2);
}
define('FUNDING', 3);


// CRON MARKETPLACE
define('MARKETPLACELOOP', 1);
define('HISTORICAL', 2);


// MARKETPLACE INVESTMENT TYPE STATUS
define('PERCENT', 1);
define('CONFIRMED', 2);
define('BEFORE_CONFIRMED', 3);
define('REJECTED', 4);


// HTTP MESSAGE TYPE FOR METHOD "getCompanyWebpage"
define ('GET', 1);                  // GET a webpage
define ('POST', 2);                 // POST some parameters, typically used for login procedure
define ('PUT', 3);                  // Not implemented yet)
define ('DELETE', 4);               // DELETE a resource on the server typically used for logging out
define ('OPTIONS', 5);              // Not implemented yet)
define ('TRACE', 6);                // Not implemented yet)
define ('CONNECT', 7);              // Not implemented yet)
define ('HEAD', 8);                 // Not implemented yet)


// DEFINE END OF LINE
define('HTML_ENDOFLINE', '<br>');
define('SHELL_ENDOFLINE', '\n'); 


// NEW QUEUE STATES  [ range 1 - 999]
// The range 900 - 998 is is reserved for errors and is
// composed as 9<original queueStatus>
// Example: 904  --> error occurred in Flow 2, 
define('WIN_QUEUE_STATUS_START_COLLECTING_DATA', 1);
define('WIN_QUEUE_STATUS_DOWNLOADING_GLOBAL_DATA', 2);
define('WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED', 3);
define('WIN_QUEUE_STATUS_EXTRACTING_DATA_FROM_FILE', 4);
define('WIN_QUEUE_STATUS_DATA_EXTRACTED', 5);
define('WIN_QUEUE_STATUS_DOWNLOADING_AMORTIZATION_TABLES', 6);
define('WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED', 7);
define('WIN_QUEUE_STATUS_EXTRACTING_AMORTIZATION_TABLE_FROM_FILE', 8);
define('WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED', 9);
define('WIN_QUEUE_STATUS_STARTING_CALCULATION_CONSOLIDATION', 10);
define('WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED', 11);
define('WIN_QUEUE_STATUS_START_CONSOLIDATION', 12);
define('WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED', 13);
define('WIN_QUEUE_STATUS_START_PREPROCESS', 14);
define('WIN_QUEUE_STATUS_STARTING_PREPROCESS', 15);
define('WIN_QUEUE_STATUS_START_GLOBAL_CALCULATION', 16);
define('WIN_QUEUE_STATUS_GLOBAL_CALCULATION_FINISHED', 17);


define('WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED', 999);


// CONCEPTS
define('WIN_CONCEPT_TYPE_INCOME', 1);
define('WIN_CONCEPT_TYPE_COST', 2);


// USERTYPES TO COLLECT INVESTMENT
define('WEEKLY_USER', 1);
define('DAILY_USER', 2);
define('LINK_ACCOUNT_USER', 3);


// GENERAL DEFINITIONS
define('MAX_INACTIVITY', 3);
define('WIN_MAX_RETRIES_PER_FLOW', 3);                                          // The number of times a Flow (in particular Flow2 and Flw3B) will 
                                                                                // restart before quitting and generating an error
define('APP_ERROR',1);
define('WARNING',2);
define('INFORMATION',3);

 
// TYPE OF FILE USED IN DASHBOARD2 MAIN FLOW
define('WIN_FLOW_TRANSACTION_FILE', "transaction");
define('WIN_FLOW_EXTENDED_TRANSACTION_FILE', 2);
define('WIN_FLOW_INVESTMENT_FILE', "investment");
define('WIN_FLOW_TRANSACTIONTABLE_FILE', 8);
define('WIN_FLOW_AMORTIZATION_TABLE_FILE', "amortizationtable");
define('WIN_FLOW_AMORTIZATION_TABLE_ARRAY', "amortizationTableList");
define('WIN_FLOW_CONTROL_FILE', "controlVariables");
define('WIN_FLOW_EXPIRED_LOAN_FILE', "expiredLoan");
define('WIN_FLOW_NEW_LOAN_FILE', "loanIds");


// CHARACTERISTICS OF VARIABLES AS USED IN FLOW 2 CLIENT
define('WIN_FLOWDATA_VARIABLE_NOT_DONE',1);
define('WIN_FLOWDATA_VARIABLE_DONE',2);
define('WIN_FLOWDATA_VARIABLE_ACCUMULATIVE',3);
define('WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE',4);


//TYPE OF APPLICATION ERROR
define('WIN_ERROR_MARKETPLACE', 3);
define('WIN_ERROR_HISTORICAL_MARKETPLACE', 4);
define('WIN_ERROR_USER_INVESTMENT_DATA', 5);
define('WIN_ERROR_GEARMAN_FLOW1', 6);
define('WIN_ERROR_GEARMAN_FLOW2', 7);
define('WIN_ERROR_GEARMAN_FLOW3A', 8);
define('WIN_ERROR_GEARMAN_FLOW3B', 9);
define('WIN_ERROR_GEARMAN_FLOW3C', 13);
define('WIN_ERROR_GEARMAN_FLOW4', 10);
define('WIN_ERROR_GEARMAN_FLOW0', 11);
define('WIN_ERROR_CONTROLVARIABLE_CHECK_FLOW2', 12);


//subtype errors Control Variable
define('WIN_ERROR_CONTROLVARIABLE_CASH_IN_PLATFORM', 1);
define('WIN_ERROR_CONTROLVARIABLE_OUTSTANDING_PRINCIPAL', 2);
define('WIN_ERROR_CONTROLVARIABLE_ACTIVE_INVESTMENTS', 4);
define('WIN_ERROR_CONTROLVARIABLE_RESERVED_FUNDS', 8);


//SUBTYPE ERRORS FLOW1
define('WIN_ERROR_FLOW_CURL_TIMEOUT', 1);
define('WIN_ERROR_FLOW_CURL', 2);
define('WIN_ERROR_FLOW_LOGIN', 3);
define('WIN_ERROR_FLOW_STRUCTURE', 4);
define('WIN_ERROR_FLOW_URLSEQUENCE', 5);
define('WIN_ERROR_FLOW_WRITING_FILE', 6);
define('WIN_ERROR_FLOW_GEARMAN_EXCEPTION', 7);
define('WIN_ERROR_FLOW_GEARMAN_FAIL', 8);
define('WIN_ERROR_FLOW_WEB_MAINTENANCE', 9);
define('WIN_ERROR_FLOW_NEW_FINAL_HEADER',10);
define('WIN_ERROR_FLOW_NEW_MIDDLE_HEADER',11);
define('WIN_ERROR_FLOW_MIME_TYPE',12);
define('WIN_ERROR_FLOW_CURRENCY', 13);
define('WIN_ERROR_FLOW_EMPTY_FILE', 14);


define('WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT', 1);


// STATUS INFORMATION PAYMENTTOTAL DATABASE TABLE
define('WIN_PAYMENTTOTALS_LAST', 2);
define('WIN_PAYMENTTOTALS_OLD', 1);


// STATUS OF LOAN AS STORED IN INVESTMENT TABLE
define('WIN_LOANSTATUS_WAITINGTOBEFORMALIZED', 1);
define('WIN_LOANSTATUS_ACTIVE', 2);
define('WIN_LOANSTATUS_FINISHED', 3);
define('WIN_LOANSTATUS_CANCELLED', 4);
define('WIN_LOANSTATUS_WRITTEN_OFF', 5);
define('WIN_LOANSTATUS_VERIFYWAITINGTOBEFORMALIZED',6);
define('WIN_LOANSTATUS_VERIFYACTIVE',7);
define('WIN_LOANSTATUS_ACTIVE_AM_TABLE', 8);
define('WIN_LOANSTATUS_UNKNOWN', 99);


// 'MARKET' AS STORED IN INVESTMENT TABLE
define('WIN_MARKET_PRIMARYMARKET', 1);
define('WIN_MARKET_SECONDARYMARKET', 2);


// 'TYPE OF INVESTMENT' AS STORED IN INVESTMENT TABLE
define('WIN_INVESTMENT_TYPE_MANUALINVESTMENT', 1);
define('WIN_INVESTMENT_TYPE_AUTOMATEDINVESTMENT', 2);


// 'TYPE OF LOAN' AS STORED IN INVESTMENT TABLE
define('WIN_TYPEOFLOAN_MORTGAGE', 1);
define('WIN_TYPEOFLOAN_CARLOAN', 2);
define('WIN_TYPEOFLOAN_BUSINESSLOAN', 3);
define('WIN_TYPEOFLOAN_PERSONAL', 4);
define('WIN_TYPEOFLOAN_INVOICETRADING', 5);
define('WIN_TYPEOFLOAN_PAGARE', 6);
define('WIN_TYPEOFLOAN_SHORTTERM', 7);
define('WIN_TYPEOFLOAN_AGRICULTURAL', 8);
define('WIN_TYPEOFLOAN_PAWNBROKING', 9);
define('WIN_TYPEOFLOAN_CONFIRMING', 10);
define('WIN_TYPEOFLOAN_UNKNOWN', 99);


// 'STATUS OF PAYMENT' AS STORED IN INVESTMENT TABLE
define('WIN_PAYMENTSTATUS_CURRENT', 1);
define('WIN_PAYMENTSTATUS_MAX7DAYSDELAY', 2);
define('WIN_PAYMENTSTATUS_MAX30DAYSDELAY', 3);
define('WIN_PAYMENTSTATUS_MAX60DAYSDELAY', 4);
define('WIN_PAYMENTSTATUS_MAX90DAYSDELAY', 5);
define('WIN_PAYMENTSTATUS_OVER90DAYSDELAY', 6);


// 'BUYBACK GUARANTEE' VALUES
define('WIN_BUYBACKGUARANTEE_PROVIDED', 1);
define('WIN_BUYBACKGUARANTEE_NOT_PROVIDED', 2);


// CONTROL VARIABLES
define('WIN_CONTROLVARIABLE_MYWALLET', "myWallet");
define('WIN_CONTROLVARIABLE_OUTSTANDINGPRINCIPAL', "activeInInvestments");
define('WIN_CONTROLVARIABLE_ACTIVEINVESTMENT', "totalEarnedInterest");
define('WIN_CONTROLVARIABLE_RESERVED_FUNDS', "reservedFunds");
//define('WIN_', 4);


// TYPE OF AMORTIZATION METHOD
define('WIN_AMORTIZATIONMETHOD_BULLET_PAYMENT', 1);
define('WIN_AMORTIZATIONMETHOD_INSTALLMENT_LOAN', 2);
define('WIN_AMORTIZATIONMETHOD_FULL', 3);
define('WIN_AMORTIZATIONMETHOD_PARTIAL', 4);
define('WIN_AMORTIZATIONMETHOD_INTEREST_ONLY', 5);
define('WIN_AMORTIZATIONMETHOD_UNKNOWN', 99);
                        
                
// PAYMENTS FREQUENCY 
define('WIN_PAYMENTFREQUENCY_DAY', 1);
define('WIN_PAYMENTFREQUENCY_MONTH', 2); 
define('WIN_PAYMENTFREQUENCY_YEAR_CUARTER', 3);
define('WIN_PAYMENTFREQUENCY_YEAR_SEMESTER', 4);
define('WIN_PAYMENTFREQUENCY_4_MONTHS', 5); 
define('WIN_PAYMENTFREQUENCY_YEAR', 6); 
define('WIN_PAYMENTFREQUENCY_ONE_PAYMENT', 7); 
define('WIN_PAYMENTFREQUENCY_UNKNOWN', 99);


// OLD SEQUENCES
define('LOGIN_SEQUENCE', 1);
define('LOGOUT_SEQUENCE', 2);
define('MARKETPLACE_SEQUENCE', 3);
define('MY_INVESTMENTS_SEQUENCE', 4);
define('MY_VIRTUAL_WALLET_SEQUENCE', 5);
define('HISTORICAL_SEQUENCE',6);
define('DOWNLOAD_PFP_FILE_SEQUENCE',7);
define('GENERATE_REPORT_SEQUENCE',8);  
define('DOWNLOAD_AMORTIZATION_TABLES_SEQUENCE', 9);


// DASHBOARD 2.0 SEQUENCES
define('WIN_LOGIN_SEQUENCE', 1);
define('WIN_LOGOUT_SEQUENCE', 2);
define('WIN_MARKETPLACE_SEQUENCE', 3);
define('WIN_MY_INVESTMENTS_SEQUENCE', 4);
define('WIN_MY_VIRTUAL_WALLET_SEQUENCE', 5);
define('WIN_HISTORICAL_SEQUENCE',6);
define('WIN_DOWNLOAD_PFP_FILE_SEQUENCE',7);
define('WIN_GENERATE_REPORT_SEQUENCE',8);
define('WIN_DOWNLOAD_AMORTIZATION_TABLES_SEQUENCE', 9);


// ACTION REASON
define('WIN_ACTION_ORIGIN_ACCOUNT_LINKING', 1);
define('WIN_ACTION_ORIGIN_REGULAR_UPDATE', 2);


// TECHNICAL STATE---ASK EDU
define('WIN_TECH_STATE_NOT_ACTIVE', 1);
define('WIN_TECH_STATE_ACTIVE', 2);
define('WIN_TECH_STATE_PREACTIVE', 3);


// TECHNICAL DATA --- ASK EDU
define('WIN_TECH_DATA_ZOMBIE_LOAN', 2);
//define('WIN_TECH_STATE_ACTIVE', 4);
//define('WIN_TECH_STATE_NOT_ACTIVE', 8;


// HAS THE SYSTEM DOWNLOADED THE AMORTIZATION TABLE(S) FOR A LOAN
define('WIN_AMORTIZATIONTABLES_NOT_AVAILABLE', 1);
define('WIN_AMORTIZATIONTABLES_AVAILABLE', 2);


define('WIN_UNDEFINED_DATE', "0000-00-00");
define('WIN_DATABASE_READOUT_LIMIT', 500);                                      // Maximum number of records to read at once using CakePHP
define('WIN_SLEEP_DURATION', 4);                                                // Interval of readings of Queue2 database
define('WIN_SHOW_DECIMAL', 2);


// STRUCTURES TYPES FOR STRUCTURE COMPARATION
define('WIN_STRUCTURE_MARKETPLACE', 1);
define('WIN_STRUCTURE_AMORTIZATION_TABLE', 2);
define('WIN_STRUCTURE_SINGLE_INVESTMENT_PAGE', 3);
define('WIN_STRUCTURE_INVESTMENTS_FILE_HTML', 4);


// STATUS OF STATE OF WORKERS
define('WIN_STATUS_COLLECT_CORRECT', 1);
define('WIN_STATUS_COLLECT_ERROR', 0);
define('WIN_STATUS_COLLECT_WARNING', 2);
define('WIN_STATUS_COLLECT_GLOBAL_ERROR', 'global');


// TYPE OF APPLICATION WARNING/ERROR
define('WIN_WARNING_MARKETPLACE', 3);
define('WIN_WARNING_HISTORICAL_MARKETPLACE', 4);
define('WIN_WARNING_USER_INVESTMENT_DATA', 5);
define('WIN_WARNING_GEARMAN_FLOW0', 11);
define('WIN_WARNING_GEARMAN_FLOW1', 6);
define('WIN_WARNING_GEARMAN_FLOW2', 7);
define('WIN_WARNING_GEARMAN_FLOW3A', 8);
define('WIN_WARNING_GEARMAN_FLOW3B', 9);
define('WIN_WARNING_GEARMAN_FLOW3C',12);
define('WIN_WARNING_GEARMAN_FLOW4', 10);
define('WIN_ERROR_AMORTIZATION_DATA_INCONSISTENCY', 1);


// SUBTYPE ERRORS FLOW1
define('WIN_WARNING_FLOW_CURL_TIMEOUT', 1);
define('WIN_WARNING_FLOW_CURL', 2);
define('WIN_WARNING_FLOW_LOGIN', 3);
define('WIN_WARNING_FLOW_STRUCTURE', 4);
define('WIN_WARNING_FLOW_URLSEQUENCE', 5);
define('WIN_WARNING_FLOW_WRITING_FILE', 6);
define('WIN_WARNING_FLOW_GEARMAN_EXCEPTION', 7);
define('WIN_WARNING_FLOW_GEARMAN_FAIL', 8);
define('WIN_WARNING_FLOW_WEB_MAINTENANCE', 9);


// LINKING PROCESS
define('WIN_LINKING_WORK_IN_PROCESS', 1);
define('WIN_LINKING_NOTHING_IN_PROCESS', 2);


// TYPE OF WINFORMULAS
define('WIN_FORMULAS_NET_ANNUAL_RETURN', 1);
define('WIN_FORMULAS_NET_RETURN', 2);


// SUBTYPE ERRORS FLOW4
define('WIN_ERROR_FLOW4_SERVICE_NOT_CALCULATE', 1);


// STATUS OF INDIVIDUAL AMORTIZATIONTABLE PAYMENTS
define('WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED', 1);                           // Payment is to take place in the future
define('WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID', 2);
define('WIN_AMORTIZATIONTABLE_PAYMENT_PAID_AFTER_DUE_DATE', 3);                 // This may include a (short) technical delay
define('WIN_AMORTIZATIONTABLE_PAYMENT_PAID', 4);
define('WIN_AMORTIZATIONTABLE_PAYMENT_LATE', 5);                                // A payment is pending and its payment date has already passed
define('WIN_AMORTIZATIONTABLE_PAYMENT_FAILURE', 6);                             // Investment has been written off
define('WIN_AMORTIZATIONTABLE_PAYMENT_UNKNOWN', 99);


// TYPE OF RUNTIME ENVIRONMENT
define('WIN_LOCAL_TEST_ENVIRONMENT', 1);                                        // The database is completely reset when Flow2 is run
define('WIN_REMOTE_TEST_ENVIRONMENT', 2);
define('WIN_LIVE_ENVIRONMENT', 3);                                              // The database is never reset


// LINKED ACCOUNT STATUS AND EXTENDED STATUS
define('WIN_LINKEDACCOUNT_UNDEFINED', 0);
define('WIN_LINKEDACCOUNT_ACTIVE', 1);
define('WIN_LINKEDACCOUNT_NOT_ACTIVE', 2);
define('WIN_LINKEDACCOUNT_ACTIVE_AND_CREDENTIALS_VERIFIED', 2);
define('WIN_LINKEDACCOUNT_ACTIVE_AND_REQUESTING_HISTORICAL_DATA', 3);
define('WIN_LINKEDACCOUNT_ACTIVE_AND_PART_OF_REGULAR_UPDATE', 4);
define('WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_SYSTEM', 11);
define('WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_USER', 13);
define('WIN_LINKEDACCOUNT_NOT_ACTIVE_AND_DELETED_BY_USER', 10);
define('WIN_LINKEDACCOUNT_NOT_ACTIVE_DELETED_BY_SYSTEM', 12);


// ORIGIN OF WHOM STARTED THE DELETE PROCESS OF A LINKED ACCOUNT
define('WIN_USER_INITIATED', 1);
define('WIN_SYSTEM_INITIATED', 2);


// STATUS OF ALIAS FIELD IN LINKED_ACCOUNT
define('WIN_ALIAS_USER_CONTROLLED', 1);
define('WIN_ALIAS_SYSTEM_CONTROLLED', 2);


// TECHNICAL FEATURES/CAPABILITIES OF A PFP
define('WIN_MULTI_ACCOUNT_FEATURE', 1);
define('WIN_PREPROCESSING_REQUIRED', 2);
define('WIN_GLOBAL_AMORTIZATION_TABLES', 4);
define('WIN_PROVIDE_UP_TO_DATE_FILES', 8);                                      // The downloaded XLS/CSV Files include information of transactions 
                                                                                // during the date of the download 


// FOR CHECKING IF DATA HAS BEEN STORED IN AMORTIZATION TABLE
define('WIN_PAYMENT_DATA_NOT_STORED', 1);
define('WIN_PAYMENT_ALREADY_STORED', 2);


// THE STATUS OF THE MODEL ACCOUNTOWNER
define('WIN_ACCOUNTOWNER_NOT_ACTIVE', 1);
define('WIN_ACCOUNTOWNER_ACTIVE', 2);


// TYPE OF NOTIFICATIONS:
define('WIN_AUTO_NOTIFICATION', 1);
define('WIN_NORMAL_NOTIFICATION', 2);
define('WIN_PRIVACY_POLICY_NOTIFICATION', 3);
define('WIN_ADVERTISING_NOTIFICATION', 4);


// TYPE OF STATUS NOTIFICATION
define('WIN_WAITING_FOR_VISUALIZATION', 1);
define('WIN_READY_FOR_VISUALIZATION', 2);
define('WIN_READ_BY_USER', 3);
define('WIN_EXPIRED_READ', 4);
define('WIN_EXPIRED_NOT_READ', 5);
define('WIN_DELETED_BY_USER', 6);
define('WIN_MESSAGE_ACCEPTED', 7);
define('WIN_EXCEEDED_BY_NEWER_MESSAGE', 8);
define('WIN_DELETED_BY_SYSTEM', 9);
define('WIN_REJECTED_BY_USER', 10);


// TYPE OF EXECUTION OF A FLOW                                                  // To detect if a flow was interrupteddue to an (software?) error
define('WIN_FLOW_IS_EXECUTING', 1);                                             // Flow has started and not (yet) fully finished
define('WIN_FLOW_HAS_ALREADY_EXECUTED_CORRECTLY', 99);                          // To identify that a flow has already been run succesfully for the
                                                                                // and hence no need to execute again

// STATUS OF TABLE CREATION IN CASE OF GLOBALAMORTIZATION TABLES
define('WIN_NEWLY_CREATED_TABLE', 1);                                           // The globalamortization table was created from scratch
define('WIN_TABLE_WAS_CREATED_BEFORE', 2);                                      // the globalamortization table already exists.

define("WIN_MAX_TRANSACTIONS", 50);                                            //Number of max dates in a transaction file. 

// STATUS OF FLOW2 BACKUP
define('WIN_BACKUP_NOT_DONE', 1);                                               // Backup has NOT been done for investment_id
define('WIN_BACKUP_DONE', 2);                                                   // Backup has been done for investment_id

//Tooltips types
define("WIN_TOOLTIP_COMMON", 1);
define("WIN_TOOLTIP_UNIQUE", 2);
define("WIN_TOOLTIP_GLOBAL", 3);


// Last id used 71  -- id 10 is not used
// TOOLTIP IDENTIFIERS FOR DASHBOARD 2
define('GLOBALDASHBOARD_INVESTMENT_INDICATORS', 11);
define('GLOBALDASHBOARD_ACTIVE_INVESTMENTS', 59);
define('GLOBALDASHBOARD_NET_DEPOSITS', 61);
define('GLOBALDASHBOARD_CASH_DRAG', 62);

define('GLOBALDASHBOARD_STATEMENT_OF_FUNDS', 70);
define('GLOBALDASHBOARD_INVESTED_ASSETS', 63);
define('GLOBALDASHBOARD_RESERVED_FUNDS', 48);
define('GLOBALDASHBOARD_CASH', 2);

define('GLOBALDASHBOARD_NET_ANNUAL_RETURNS', 1);
define('GLOBALDASHBOARD_NAR_LAST_365_DAYS', 64);
define('GLOBALDASHBOARD_NAR_LAST_YEAR', 65);
define('GLOBALDASHBOARD_NAR_TOTAL_FUNDS', 66);

define('GLOBALDASHBOARD_NET_EARNINGS', 12);
define('GLOBALDASHBOARD_NET_EARNINGS_LAST_365_DAYS', 67);
define('GLOBALDASHBOARD_NET_EARNINGS_LAST_YEAR', 68);
define('GLOBALDASHBOARD_NET_EARNINGS_TOTAL_FUNDS', 69);

define('GLOBALDASHBOARD_KPIS', 3);
define('GLOBALDASHBOARD_KPI_PLATFORM', 4);
define('GLOBALDASHBOARD_KPI_YIELD', 5);
define('GLOBALDASHBOARD_KPI_TOTAL_VOLUME', 6);
define('GLOBALDASHBOARD_KPI_CASH', 7);
define('GLOBALDASHBOARD_KPI_EXPOSURE', 8);
define('GLOBALDASHBOARD_KPI_CURRENT', 9);

define('GLOBALDASHBOARD_PAYMENT_DELAY',13);
define('GLOBALDASHBOARD_CURRENT', 14);

define('COMPANY_TOOLTIP', 15);

define('DASHBOARD_INVESTMENT_INDICATORS', 60);
define('DASHBOARD_ACTIVE_INVESTMENTS', 16);
define('DASHBOARD_NET_DEPOSITS', 17);
define('DASHBOARD_CASH_DRAG', 18);

define('DASHBOARD_STATEMENT_OF_FUNDS', 71);
define('DASHBOARD_INVESTED_ASSETS', 19);
define('DASHBOARD_RESERVED_FUNDS', 20);
define('DASHBOARD_CASH', 21);

define('DASHBOARD_NET_ANNUAL_RETURNS', 25);
define('DASHBOARD_NAR_LAST_365_DAYS', 22);
define('DASHBOARD_NAR_LAST_YEAR', 23);
define('DASHBOARD_NAR_TOTAL_FUNDS', 24);

define('DASHBOARD_NET_EARNINGS', 58);
define('DASHBOARD_NET_EARNINGS_LAST_365_DAYS', 26);
define('DASHBOARD_NET_EARNINGS_LAST_YEAR', 27);
define('DASHBOARD_NET_EARNINGS_TOTAL_FUNDS', 28);

define('DASHBOARD_PAYMENT_DELAY', 29);
define('DASHBOARD_CURRENT', 30);
define('DASHBOARD_EXPOSURE', 31);


define('PROFILE_NAME', 32);
define('PROFILE_SURNAMES', 33);
define('PROFILE_ADDRESS', 34);
define('PROFILE_POSTCODE', 35);
define('PROFILE_CITY', 36);
define('PROFILE_COUNTRY', 37);
define('PROFILE_IBAN', 38);
define('PROFILE_ID', 39);
define('PROFILE_TELEPHONE', 40);
define('PROFILE_DATE_OF_BIRTH', 41);
define('PROFILE_COMPANY', 42);
define('PROFILE_FISCAL_ID', 43);
define('PROFILE_PASSWORD', 44);

define('ACCOUNT_LINKING_USERNAME', 45);
define('ACCOUNT_LINKING_PASSWORD', 46);
define('ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME', 47);



define('INVESTMENT_LIST_INVESTMENTDATE', 49);
define('INVESTMENT_LIST_MYINVESTMENT', 50);
define('INVESTMENT_LIST_INTEREST', 51);
define('INVESTMENT_LIST_INSTALLMENTPROGRESS', 52);
define('INVESTMENT_LIST_OUTSTANDINGPRINCIPAL', 53);
define('INVESTMENT_LIST_NEXTPAYMENT', 54);
define('INVESTMENT_LIST_STATUS', 55);
define('INVESTMENT_LIST_GLOBALTOOLTIP', 56);
define('INVESTMENT_LIST_LOANID', 57);

// Values of API variable 'linkedaccount_visual_state'
define('WIN_QUEUED', 'QUEUED');
define('API_QUEUED', 10);
define('WIN_ANALYZING', 'ANALYZING');
define('API_ANALYZING', 20);
define('WIN_MONITORED', 'MONITORED');
define('API_MONITORED', 30); 

// Values of API variable 'linkedaccount_status'
define('WIN_LINKEDACCOUNT_STATUS_UNDEFINED', 'UNDEFINED');
define('API_LINKEDACCOUNT_STATUS_UNDEFINED', "0");
define('WIN_LINKEDACCOUNT_STATUS_ACTIVE', 'ACTIVE');   
define('API_LINKEDACCOUNT_STATUS_ACTIVE', 1); 
define('WIN_LINKEDACCOUNT_STATUS_NOT_ACTIVE', 'NOT_ACTIVE');
define('API_LINKEDACCOUNT_STATUS_NOT_ACTIVE', 2);

// Values of API variable 'metadata_type_of_document' 
define('WIN_DNI_FRONT', 'DNI_FRONT');
define('API_DNI_FRONT', 10);
define('WIN_DNI_BACK','DNI_BACK');
define('API_DNI_BACK', 20);
define('WIN_BANK_CERTIFICATE','BANK_CERTIFICATE');
define('API_BANK_CERTIFICATE', 30);

// Values of API variable 'polling_type' 
define('WIN_NOTIFICATION_CHECK', 'NOTIFICATION_CHECK');
define('API_NOTIFICATION_CHECK', 10);
define('WIN_LINKEDACCOUNT_CHECK', 'LINKEDACCOUNT_CHECK');
define('API_LINKEDACCOUNT_CHECK', 20);
define('WIN_PMESSAGE_CHECK', 'PMESSAGE_CHECK');
define('API_PMESSAGE_CHECK', 30);

// Values of API variable 'service_state'
define('WIN_SERVICE_STATE_NOT_ACTIVE', 'NOT_ACTIVE');
define('API_SERVICE_STATE_NOT_ACTIVE',10);
define('WIN_SERVICE_STATE_ACTIVE', 'ACTIVE');    
define('API_SERVICE_STATE_ACTIVE', 20);
define('WIN_SERVICE_STATE_SUSPENDED', 'SUSPENDED');
define('API_SERVICE_STATE_SUSPENDED', 30); 

define('WIN_ACCESS_TOKEN', 1);
define('WIN_REFRESH_TOKEN', 2);

//CONSTANT FOR TESTING, FILTER INVESTMENTS THAT WE WANT TO BE MAPPED IN FLOW 2
define('WANTEDINVESTMENT', array('09-174058001'));

// Constant required for Pollingresource
define('WIN_POLLING_INTERVAL', 60);                                             // Recommended duration between readouts of Pollingresource Object

// Required for the generic ACL function
define('WIN_MODEL', 10);
define('WIN_ACL_METHOD', 20);
define('WIN_ROLE', 30);
define('WIN_ACL_ANALYSIS_ERROR', 1);
define('WIN_ACL_ANALYSIS_CONTINUE', 2);
define('WIN_ACL_GRANT_ACCESS', 3);