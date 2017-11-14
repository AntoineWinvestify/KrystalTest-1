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
 * @version 0.5
 * @date  2017-11-11
 * @package
 *
 *
 * 2017-10-09		version 0.1
 * Basic version
 *
 * This class parses the transaction/investments files etc by using a configuration file which is 
 * provided by each companyCodeFile. The result is returned in an array
 * 
 * 
 * 
 * 2017-10-15           version 0.2
 * support of configuration parameters 'offsetStart' and 'offsetEnd'
 * 
 * 2017-10-24           version 0.3
 * Added function to parse a html file
 * 
 * 
 * 2017-10-26           version 0.3
 * Due to use of bc-math functionality, the amounts are now ordinary string with the decimal point
 * getLastError is returning real data, for 'unknown concept'
 * E format in getAmount fixed. Format example: 2,31E-6.
 * Minor fixes.
 * 
 * 2017-11-10           version 0.4
 * Updated function extractDataFromString with an extra parameter
 * 
 * 
 * 2017-11-11           version 0.5
 * Function getDefaultValue was added
 *  
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
                            'sortParameter' => ""   // used to "sort" the array and use $sortParameter as prime index.
                             );                     // if array does not have $sortParameter then "global" index is used
                                                    // Typically used for sorting by loanId index

    protected $errorData = array();                 // Contains the information of the last occurred error

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
    
    
    protected $transactionDetails = [  
            1 => [
                "detail" => "Cash_deposit",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,                                    // 1 = income, 2 = cost
                "account" => "CF",
                "type" => "globalcashflowdata_platformDeposits"            
                ],
            2 => [
                "detail" => "Cash_withdrawal",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "CF",
                "type" => "globalcashflowdata_platformWithdrawals"          
                ],
            3 => [
                "detail" => "Primary_market_investment",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "investment_myInvestment",                     
                ],
            4 => [
                "detail" => "Secondary_market_investment",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "Capital",
                "type" => "investment_secondaryMarketInvestment"              
                ],
            5 => [
                "detail" => "Capital_repayment",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "Capital",
                "type" => "payment_capitalRepayment"                      
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
                "type" => "payment_delayedInterestPayment"          
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
                "type" => "concept14"  
                ],
            15 => [
                "detail" => "Compensation",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "concept15"    
                ],
            16 => [
                "detail" => "Income_secondary_market",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "investment_incomeSecondaryMarket"        
                ],
            17 => [
                "detail" => "Currency_fluctuation_positive",
                "transactionType" => WIN_CONCEPT_TYPE_INCOME,
                "account" => "PL",
                "type" => "concept17"  
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
                "type" => "concept20"
                ],
            21 => [
                "detail" => "Bank_charges",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept21"
                ],
            22 => [
                "detail" => "Cost_secondary_market",
                "transactionType" => WIN_CONCEPT_TYPE_COST,                
                "account" => "PL",
                "type" => "investment_costSecondaryMarket"
                ],
            23 => [
                "detail" => "Interest_payment_secondary_market_purchase",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept23"
                ],           
            24 => [
                "detail" => "currency_exchange_fee",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept24"
                ],
            25 => [
                "detail" => "currency_fluctuation_negative",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept25"
                ],                        
            26 => [
                "detail" => "Tax_VAT",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept26"
                ],
            27 => [
                "detail" => "Tax_income_withholding_tax",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept27"
                ],
            28 => [
                "detail" => "Write-off",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept28"
                ],
            29 => [
                "detail" => "Registration",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept29"
                ],
            30 => [
                "detail" => "Currency_exchange_transaction",
                "transactionType" => WIN_CONCEPT_TYPE_COST,
                "account" => "PL",
                "type" => "concept30"
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
                ]
        ];

        private $filename;      // holds name of the file being analyzed
        
        
    function __construct() {
        echo "starting parser\n";
    }



    /**
     * Starts the process of analyzing the file and returns the results as an array
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @param  highestRow      Last written row, we need for offsetEnd
     *  @return array           $parsedData
     *          false in case an error occurred
     */
    public function analyzeFile($file, $configuration) {
echo "INPUT FILE = $file \n";
    $this->filename = $file;
echo __FUNCTION__ . " " . __LINE__ . " Memory = " . memory_get_usage (false)  . "\n"; 

       // determine first if it is a csv, if yes then run command
        $fileNameChunks = explode(DS, $file);
        if (stripos($fileNameChunks[count($fileNameChunks) - 1], "CSV")) {
    //        $command = "iconv -f cp1250 -t utf-8 " . $file " > " $file ";
            $inputFileType = 'CSV';
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setDelimiter($this->Config['separatorChar']);
            $objPHPExcel = $objReader->load($file);
            //execute command php has a function for this which works on a string
        }
        else {      // xls/xlsx file
            $objPHPExcel = PHPExcel_IOFactory::load($file);
        }

        ini_set('memory_limit','2048M');
        $sheet = $objPHPExcel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        echo " Number of rows = $highestRow and number of Columns = $highestColumn \n";


        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $datas = $this->saveExcelToArray($sheetData, $configuration, $highestRow);
        return $datas;
    }


    /**
     * Analyze the received data using the configuration data and store the result
     * in an array
     *
     * @param string $rowDatas  the excel data in an array.
     * @param int $totalRows    last row written, we need it for offsetEnd.
     * @return array $temparray the data after the parsing process.
     *
     */
    private function saveExcelToArray($rowDatas, $values, $totalRows) {
        $tempArray = [];
        $maxRows = count($rowDatas);

        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $this->config['offsetStart']) {
                break;
            }
            //echo "unset to happen, value of cell = " . print_r($rowDatas[$key][2]);
            echo "\n";
            unset($rowDatas[$key]);
            $i++;
        }

        echo "totalRows = $maxRows\n";
     
        for ($i = $maxRows; $i > 0; $i--) {
            if (empty($rowDatas[$i]["A"])) {
                unset($rowDatas[$i]);
            }
        }   
 
        $i = 0;
        //$totalRows = count($rowData);
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $this->config['offsetEnd']) {
                break;
            }
            unset($rowDatas[$totalRows]);
            $totalRows = $totalRows - 1;
            $i++;
        }  
       
         
        $i = 0;
        $outOfRange = false;

        foreach ($rowDatas as $rowData) {
            foreach ($values as $key => $value) {
                $previousKey = $i - 1;
                $currentKey = $i;
                
                if ($key == "callback") {
                    continue;
                }
                
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

                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters
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
                    $tempArray[$sortParam1] = $tempArray[$i];
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
        return $tempArray;
    }
    
    public function getFirstRow($file, $configParam) {
        $extension = $this->getExtensionFile($file);
        $inputType = $this->getInputFileType($extension);
        $data = $this->convertExcelByParts($file, $configParam["chunkInit"], $configParam["chunkSize"], $inputType);
        print_r($data[1]);
        return $data[1];
    }
    
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
        
        /**  Create a new Instance of our Read Filter  **/
        $chunkFilter = new readFilterWinvestify();
        /**  Tell the Read Filter, the limits on which rows we want to read this iteration  **/
        $chunkFilter->setRows($chunkInit,$chunkSize);
        /**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
        $objReader->setReadFilter($chunkFilter);
        
        $objPHPExcel = $objReader->load($filePath);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        echo "sheetDAta <br>";
        var_dump($sheetData);
        return $sheetData;
    }
    
    public function getInputFileType($extension) {
        
        switch($extension) {
            case "xlsx":
                $inputType = "Excel2007";
                break;
        }
        return $inputType;
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
     * an unknown "payment" concept was found.
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
     * Returns information of the last occurred error. Can also detect if
     * an unknown "payment" concept was found.
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
            case WIN_CONCEPT_TYPE_COST:                                  // A result was found.
                return "Unknown_cost";
                break;

            case WIN_CONCEPT_TYPE_INCOME:                                // A result was found
                return "Unknown_income";
                break;
            default:                                    // Nothing found, so do some maths to
                return "Unknown_concept";               // see if it is an income or a cost.
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
            $seperator = "\.";
        }   
        else {                                                              // seperator =>  ","
            $seperator = ",";
        }
        if(empty($input) || $input == 0 && ($input <! 0 && $input >! 0)){ // decimals like 0,0045 are true in $input == 0
            $input = "0.0";
            $seperator = "\.";
        }
        if(strpos($input, "E") || strpos($input, "e")){
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
            $seperator = "\.";
        }
       

        $allowedChars =  "/[^0-9" . $seperator . "]/";
        $normalizedInput = preg_replace($allowedChars, "", $input);         // only keep digits, and decimal seperator
        $normalizedInputFinal = preg_replace("/,/", ".", $normalizedInput);
        return $normalizedInputFinal;
    }
    

    /**
     *
     * Determines the 'transactiondetail' based on a translationtable (=$config) and the sign of the amount ( positive or negative).
     * Note that the amount must already have been calculated before executing this function
     *
     * @param string    $input
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
        $position = strpos($input, "-");
        
        if ($position !== false) {      // contains - sign 
            $type = WIN_CONCEPT_TYPE_COST;
        }    
        else {
            $type = WIN_CONCEPT_TYPE_INCOME;
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
            echo "unknown concept [$input] for complex, so start doing some guessing for concept $originalConcept\n";  
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
            if (strpos($loanCurrency, $currency[0]) != false || $currencySymbol == $currency[0]) {                // check the ISO code
              return $currencyIndex;
            }
            if (strpos($loanCurrency, $currency[1]) != false || $currencySymbol == $currency[1]) {              // check the symbol
              return $currencyIndex;
            }
        }
        echo HTML_ENDOFLINE;
    }


    /**
     * get hash of a string
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

        $found = null;
        foreach ($config as $configKey => $item) {
            $configItemKey = key($item);
            $configItem = $item[$configItemKey];
            foreach ($this->transactionDetails as $key => $detail) { 
                $position = strpos($input, $configItemKey );
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
     * Function to get details of transaction from multiple cells together
     * @param string $input It is the cell value
     * @param array $config Winvestify standardized concept
     * @param array $inputValues Values needed to calculate transaction details
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
     * Search for a something within a string, starting AFTER $search
     * and ending when $separator is found
     * If $search == "" then $extractedString starts from beginning of $input.
     * If $separator = "" then $extractedString contains the $input starting from $search to end
     *
     * @param string    $input      It is the string to which we search the information
     * @param string    $search     The character to search. 
     * @param string    $separator  The separator character
     * @param int       $mandatory  Indicates if it is mandatory that $search exists. If it does not exist 
     *                              then the function will return a string of format "global_xxxxxx" with 
     *                              xxxxxx being a random number
     * @return string   $extractedString    The value we were looking for
     *
     */
    private function extractDataFromString($input, $search, $separator, $mandatory = 0) {
        $position = stripos($input, $search);
        if ($position !== false) {  // == TRUE
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
            $length1 = 150;                 // ficticious value
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
        
        echo 'ENTRO';
        echo $input . HTML_ENDOFLINE;
        echo $field . HTML_ENDOFLINE;
        
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
     * Function to get the loanId from the file name of one amortization table
     * @param string $filePath It is the path to the file
     * @return string It is the loanId
     */
    public function getLoanIdFromFile($filePath) {
        $file = new File($filePath, false);
        $name = $file->name();
        $nameSplit = explode("_", $name);
        $loanId = $nameSplit[1];
        return $loanId;
    }
    
    /**
     * Function to get the extension of a file
     * @param string $filePath It is the path to the file
     * @return string It is the extension of the file
     */
    public function getExtensionFile($filePath) {
        $file = new File($filePath, false);
        $extension = $file->ext();
        return $extension;
    }
    
    /**
     * Function to analyze a file depending on its extension
     * @param string $filePath It is the path to the file
     * @param array  $parserConfig Array that contains the configuration data of a specific "document"
     * @param string $extension It is the extension of the file
     * @return array $parsedData
     *         false in case an error occurred
     */
    public function analyzeFileAmortization($filePath, $parserConfig, $extension) {
        
        switch($extension) {
            case "html":
                $tempArray = $this->getHtmlData($filePath, $parserConfig);
                break;
        }
        return $tempArray;
    }
    
    /**
     * Function to analyze a html file to get its content
     * @param string $filePath It is the path to the file
     * @param array  $parserConfig Array that contains the configuration data of a specific "document"
     * @return array $parsedData
     *         false in case an error occurred
     */
    public function getHtmlData($filePath, $parserConfig) {
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
                echo "value";
                print_r($value);
                $previousKey = $i - 1;
                $currentKey = $i;
                $valueTd = trim($tds[$key]->nodeValue);
                echo "tdValue";
                print_r($valueTd);
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
                                print_r($input);
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
                        print_r($userFunction['inputData']);
                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array($this,
                                                                       $userFunction['functionName']),
                                                                       $userFunction['inputData']
                                    );
                            print_r($tempResult);
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
}
