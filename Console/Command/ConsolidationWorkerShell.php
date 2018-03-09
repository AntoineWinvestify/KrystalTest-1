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
 * @date 2017-10-13
 * @package
 */

App::import('Shell','GearmanWorker');

/**
 * Class to make all the calculation of NAR and Net Return
 *
 */
class ConsolidationWorkerShell extends GearmanWorkerShell {
    
    protected $formula = [];
    protected $config = [];
    protected $originExecution;
    protected $dashboardOverviewLinkaccountIds = [];
    protected $financialClass;
    
   public function startup() {
        parent::startup();
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'winFormulas.php');    
   }
    
    /**
     * Function main that init when start the shell class
     */
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('netAnnualReturn', array($this, 'calculateNetAnnualReturn'));
        $this->GearmanWorker->addFunction('netAnnualTotalFunds', array($this, 'calculateNetAnnualTotalFunds'));
        $this->GearmanWorker->addFunction('getFormulaCalculate', array($this, 'getFormulaCalculate'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting GEARMAN_FLOW4 to listen to data from its Client\n";
        while( $this->GearmanWorker->work());
    }
    
    /**
     * Function to calculate the formulas by type of variable 
     * @param object $job It is the object of Gearmanjob that contains
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data["companies"]                  array It contains all the linkedaccount information
     *      $data["queue_userReference"]        string It is the user reference
     *      $data["queue_id"]                   integer It is the queue id
     *      $data["date"]                       integer It is the today's date
     *      $data["service"]                    string  It is the function to call in order to calculate the variable
     *      $data['companiesNothingInProgress'] array   All the companies that are not in progress
     *      $data["originExecution"]            integer Account linking or regular update
     * @return json Json containing all the status collect and errors by link account id
     */
    public function getFormulaCalculate($job) {
        $data = json_decode($job->workload(), true);
        $this->job = $job;
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
        print_r($data);
        //$dateYearBack = date("Y-m-d",strtotime(date('Y-m-d') . "-1 Year"));
        $index = 0;
        $i = 0;
        $this->winFormulas = new WinFormulas();
        //Get investor ID by queue_userReference
        //$investorId = $this->investor->find("userReference");
        Configure::load('internalVariablesConfiguration.php', 'default');
        $this->variablesConfig = Configure::read('internalVariables');
        $service = $data['service'];
        $serviceFunction = $service['service'];
        $result = $this->$serviceFunction($data);
        return $result;
    }
    
    /**
     * Function to calculate formulas of past 12 months
     *          functions: NetAnnualReturnPast12Months
     *                     NetAnnualTotalFundsReturn
     *                     NetReturn
     * @param array $data
     *              $data["companies"]                  array It contains all the linkedaccount information
     *              $data["queue_userReference"]        string It is the user reference
     *              $data["queue_id"]                   integer It is the queue id
     *              $data["date"]                       integer It is the today's date
     *              $data["service"]                    string  It is the function to call in order to calculate the variable
     *              $data['companiesNothingInProgress'] array   All the companies that are not in progress
     *              $data["originExecution"]            integer Account linking or regular update
     * @param string $nameVariables It is the name of parameters to calculate the formula
     * @param string $nameFunction  It is the name of the formula we calculate
     * @param integer $typeOfFormula It is the type of formula, NAR or Net Return
     * @return json We return all the variable
     */
    public function calculatePast12Months($data, $nameVariables, $nameFunction, $typeOfFormula) {
        $variables = $this->winFormulas->getFormulaParams($nameVariables);
        $values = [];
        $dashboardOverviewLinkaccountIds = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            //We need to save the companies in the flow in order to delete from companiesNothingInProgress
            $dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            //We need to get the data from DB by value of winFormulas
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
            }
            $dataMergeByDate[$linkedaccountId] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
        }
        
        if ($typeOfFormula === WIN_FORMULAS_NET_ANNUAL_RETURN) {
            $returnData = [];
            foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
                $returnData[$linkedaccountId][$nameFunction] = $this->financialClass->XIRR($dataByLinkedaccountId['values'], $dataByLinkedaccountId['dates']);
            }
        }
        else if ($typeOfFormula == WIN_FORMULAS_NET_RETURN) {
            foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
                $returnData[$linkedaccountId][$nameFunction] = $this->consolidateResults($dataByLinkedaccountId['values']);
            }
        }
        
        /*** IMPROVED STRUCTURE FOR GLOBAL DASHBOARD OVERVIEW **********/
        $companiesNotInProgress = [];
        foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $dashboardOverviewLinkaccountIds)) {
                $companiesNotInProgress[] = $companyNothingInProgress;
            }
           
        }
        foreach ($companiesNotInProgress as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
            }
            //$dataMergeByDate[$linkedaccountId] 
        }
        $values = $this->joinTwoDimensionArrayTogether($values);
        $dataMergeByDateForInvestor = $this->mergeArraysByKey($values, $variables);
        
        if ($typeOfFormula === WIN_FORMULAS_NET_ANNUAL_RETURN) {
            $returnData['investor'][$data["queue_userReference"]][$nameFunction] = $this->financialClass->XIRR($dataMergeByDateForInvestor['values'], $dataMergeByDateForInvestor['dates']); 
        }
        else if ($typeOfFormula === WIN_FORMULAS_NET_RETURN) {
            $returnData['investor'][$data["queue_userReference"]][$nameFunction] = $this->consolidateResults($dataMergeByDateForInvestor['values']);
        }
        
        /////////////////////FINISHED
        
        /***** START VERIFICATION OF STATUS***********/
        
        $statusCollect = [];
        $error = [];
        
        foreach ($returnData as $linkedaccountIdKey => $variableService) {
            if ($linkedaccountIdKey == 'investor') {
                $keyInvestor = key($variableService);
                $keyService = key($variableService[$keyInvestor]);
                if ($variableService[$keyInvestor][$keyService] == "0") {
                    $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService] = WIN_STATUS_COLLECT_CORRECT;
                }
                else if (empty($variableService[$keyInvestor][$keyService])) {
                    $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService] = WIN_STATUS_COLLECT_ERROR;
                    $error[$linkedaccountIdKey][$keyInvestor][$keyService] = [
                            'typeOfError' => "There was an error calculating the $keyService",
                            'detailedErrorInformation' => "The service $keyService has given an error with the calculation",
                            'line' => __LINE__,
                            'file' => __FILE__,
                            'urlsequenceUrl' => null,
                            'typeErrorId' => WIN_ERROR_GEARMAN_FLOW4,
                            'subtypeErrorId' => WIN_ERROR_FLOW4_SERVICE_NOT_CALCULATE
                        ];
                }
                else {
                    $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService] = WIN_STATUS_COLLECT_CORRECT;
                }
            }
            else {
                $keyService = key($variableService);
                if ($variableService[$keyService] == "0") {
                    $statusCollect[$linkedaccountIdKey][$keyService] = WIN_STATUS_COLLECT_CORRECT;
                }
                else if  (empty($variableService[$keyService])){
                    $statusCollect[$linkedaccountIdKey][$keyService] = WIN_STATUS_COLLECT_ERROR;
                    $error[$linkedaccountIdKey][$keyService] = [
                            'typeOfError' => "There was an error calculating the $keyService",
                            'detailedErrorInformation' => "The service $keyService has given an error with the calculation",
                            'line' => __LINE__,
                            'file' => __FILE__,
                            'urlsequenceUrl' => null,
                            'typeErrorId' => WIN_ERROR_GEARMAN_FLOW4,
                            'subtypeErrorId' => WIN_ERROR_FLOW4_SERVICE_NOT_CALCULATE
                        ];
                }
                else {
                    $statusCollect[$linkedaccountIdKey][$keyService] = WIN_STATUS_COLLECT_CORRECT;
                }
            }
        }
        echo "\nStatus collect ======>   ";
        print_r($statusCollect);
        echo "\nTempData  ======> $nameFunction ===>  ";
        print_r($returnData);
        $dataArray['tempArray'] = $returnData;
        $dataArray['statusCollect'] = $statusCollect;
        $dataArray['errors'] = $error;
        return json_encode($dataArray);
    }
    
    /**
     * Function to calculate formulas of past years
     *          functions: NetAnnualReturnPastYears
     *                     NetReturnPastYears
     * @param array $data
     *              $data["companies"]                  array It contains all the linkedaccount information
     *              $data["queue_userReference"]        string It is the user reference
     *              $data["queue_id"]                   integer It is the queue id
     *              $data["date"]                       integer It is the today's date
     *              $data["service"]                    string  It is the function to call in order to calculate the variable
     *              $data['companiesNothingInProgress'] array   All the companies that are not in progress
     *              $data["originExecution"]            integer Account linking or regular update
     * @param string $nameVariables It is the name of parameters to calculate the formula
     * @param string $nameFunction  It is the name of the formula we calculate
     * @param integer $typeOfFormula It is the type of formula, NAR or Net Return
     * @return json We return all the variable
     */
    public function calculatePastYears($data, $nameVariables, $nameFunction, $typeOfFormula) {
        $variables = $this->winFormulas->getFormulaParams($nameVariables);
        $this->originExecution = $data['originExecution'];
        $dashboardOverviewLinkaccountIds = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            $dates = $this->getPeriodOfTime($data["date"], $linkedaccountId);
            $datesForGlobal[$linkedaccountId] = $dates; 
            foreach ($dates as $keyDate => $dateYear) {
                foreach ($variables as $variableKey => $variable) {
                    $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                    $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                    $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
                    $valuesForGlobal[$dateYear][$linkedaccountId][$variableKey] = $values[$linkedaccountId][$variableKey];
                }
                $dataMergeByDate[$linkedaccountId][$dateYear] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
                //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
            }
        }
        if ($typeOfFormula === WIN_FORMULAS_NET_ANNUAL_RETURN) {
            $returnData = null;
            foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
                foreach ($dataByLinkedaccountId as $keyDate => $dataByDate) {
                    $returnData[$linkedaccountId][$nameFunction][$keyDate] = $this->financialClass->XIRR($dataByDate['values'], $dataByDate['dates']);
                }
            }
        }
        else if ($typeOfFormula === WIN_FORMULAS_NET_RETURN) {
            foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
                foreach ($dataByLinkedaccountId as $keyDate => $dataByDate) {
                    $returnData[$linkedaccountId][$nameFunction][$keyDate] = $this->consolidateResults($dataByDate['values']);
                }
            }
        }
        
        /*** IMPROVED STRUCTURE FOR GLOBAL DASHBOARD OVERVIEW **********/
        $companiesNotInProgress = [];
        foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $dashboardOverviewLinkaccountIds)) {
                $companiesNotInProgress[] = $companyNothingInProgress;
            }
           
        }
        foreach ($companiesNotInProgress as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $dates = $this->getPeriodOfTime($data["date"], $linkedaccountId);
            $datesForGlobal[$linkedaccountId] = $dates; 
            foreach ($dates as $keyDate => $dateYear) {
                foreach ($variables as $variableKey => $variable) {
                    $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                    $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                    $valuesForGlobal[$dateYear][$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
                }
            }
        }
        $values = [];
        $datesForGlobal = $this->mergeDatesByYear($datesForGlobal);
        foreach ($datesForGlobal as $keyDate => $dateYear) {
            $values[$dateYear] = $this->joinTwoDimensionArrayTogether($valuesForGlobal[$dateYear]);
        }
        $dataMergeByDate = [];
        
        foreach ($datesForGlobal as $keyDate => $dateYear) {
            $dataMergeByDate[$dateYear] = $this->mergeArraysByKey($values[$dateYear], $variables);
            //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
        }
        
        if ($typeOfFormula === WIN_FORMULAS_NET_ANNUAL_RETURN) {
            foreach ($dataMergeByDate as $keyDate => $dataByDate) {
                $returnData['investor'][$data["queue_userReference"]][$nameFunction][$keyDate] = $this->financialClass->XIRR($dataByDate['values'], $dataByDate['dates']);
            }
        }
        else if ($typeOfFormula === WIN_FORMULAS_NET_RETURN) {
            foreach ($dataMergeByDate as $keyDate => $dataByDate) {
                $returnData['investor'][$data["queue_userReference"]][$nameFunction][$keyDate] = $this->consolidateResults($dataByDate['values']);
            }
        }
        
        /////////////////////
        
        $statusCollect = [];
        $error = [];
        foreach ($returnData as $linkedaccountIdKey => $variableService) {
            if ($linkedaccountIdKey == 'investor') {
                $keyInvestor = key($variableService);
                $keyService = key($variableService[$keyInvestor]);
                foreach ($variableService[$keyInvestor][$keyService] as $dateKey => $result) {
                    if ($result == "0") {
                    $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService][$dateKey] = WIN_STATUS_COLLECT_CORRECT;
                    }
                    else if (empty($result)) {
                        $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService][$dateKey] = WIN_STATUS_COLLECT_ERROR;
                        $error[$linkedaccountIdKey][$keyInvestor][$keyService][$dateKey] = [
                            'typeOfError' => "There was an error calculating the $keyService",
                            'detailedErrorInformation' => "The service $keyService has given an error with the calculation",
                            'line' => __LINE__,
                            'file' => __FILE__,
                            'urlsequenceUrl' => null,
                            'typeErrorId' => WIN_ERROR_GEARMAN_FLOW4,
                            'subtypeErrorId' => WIN_ERROR_FLOW4_SERVICE_NOT_CALCULATE
                        ];
                    }
                    else {
                        $statusCollect[$linkedaccountIdKey][$keyInvestor][$keyService][$dateKey] = WIN_STATUS_COLLECT_CORRECT;
                    }
                }
            }
            else {
                $keyService = key($variableService);
                foreach ($variableService[$keyService] as $dateKey => $result) {
                    if ($result == "0")  {
                        $statusCollect[$linkedaccountIdKey][$keyService][$dateKey] = WIN_STATUS_COLLECT_CORRECT;
                    }
                    else if (empty($result)) {
                        $statusCollect[$linkedaccountIdKey][$keyService][$dateKey] = WIN_STATUS_COLLECT_ERROR;
                        $error[$linkedaccountIdKey][$keyService][$dateKey] = [
                            'typeOfError' => "There was an error calculating the $keyService",
                            'detailedErrorInformation' => "The service $keyService has given an error with the calculation",
                            'line' => __LINE__,
                            'file' => __FILE__,
                            'urlsequenceUrl' => null,
                            'typeErrorId' => WIN_ERROR_GEARMAN_FLOW4,
                            'subtypeErrorId' => WIN_ERROR_FLOW4_SERVICE_NOT_CALCULATE
                        ];
                    }
                    else {
                        $statusCollect[$linkedaccountIdKey][$keyService][$dateKey] = WIN_STATUS_COLLECT_CORRECT;
                    }

                }
            }
            
        }
        echo "\nStatus collect ======>   ";
        print_r($statusCollect);
        echo "\nTempData  ======> $nameFunction ===>  ";
        print_r($returnData);
        $dataArray['tempArray'] = $returnData;
        $dataArray['statusCollect'] = $statusCollect;
        $dataArray['errors'] = $error;
        return json_encode($dataArray);
    }
    
    /**
     * Function to calculate net annual return of past 12 months with XIRR formula of financial class
     * @param array $data Data needed to calculate the net annual return
     * @return json Json that contain all the information needed to store in database
     */
    public function calculateNetAnnualReturnXirr($data) {
        $this->includeVendorFolder();
        return $this->calculatePast12Months($data, "netAnnualReturn_xirr", 'netAnnualReturnXirr', WIN_FORMULAS_NET_ANNUAL_RETURN);
    }
    
    /**
     * Function to calculate net annual total funds return of past 12 months with XIRR formula of financial class
     * @param array $data Data needed to calculate the net annual total funds return
     * @return json Json that contain all the information needed to store in database
     */
    public function calculateNetAnnualTotalFundsReturnXirr($data) {
        $this->includeVendorFolder();
        return $this->calculatePast12Months($data, "netAnnualTotalFundsReturn_xirr", 'netAnnualTotalFundsReturnXirr', WIN_FORMULAS_NET_ANNUAL_RETURN);
    }
    
    /**
     * Function to calculate net annual return of past years with XIRR formula of financial class
     * @param array $data Data needed to calculate the net annual return of past years
     * @return json Json that contain all the information needed to store in database
     */
    public function calculateNetAnnualReturnPastYearXirr($data) {
        $this->includeVendorFolder();
        return $this->calculatePastYears($data, "netAnnualPastReturn_xirr", 'netAnnualReturnPastYearXirr', WIN_FORMULAS_NET_ANNUAL_RETURN);
    }
    
    /**
     * Function to calculate net return of past 12 months
     * @param array $data Data needed to calculate the net return
     * @return json Json that contain all the information needed to store in database
     */
    public function calculateNetReturn($data) {
        return $this->calculatePast12Months($data, "netReturn", 'netReturn', WIN_FORMULAS_NET_RETURN);
    }
    
    /**
     * Function to calculate net return of past years
     * @param array $data Data needed to calculate the net return
     * @return json Json that contain all the information needed to store in database
     */
    public function calculateNetReturnPastYear($data) {
        return $this->calculatePastYears($data, "netPastReturn", 'netReturnPastYear', WIN_FORMULAS_NET_RETURN);
    }
    
    /**
     * Function to get a period of years between two dates
     * 
     * @param string $dateFinish It is the final date
     * @param integer $linkedaccountId It is the linkedaccount id that we calculate the period of time
     * @return array
     */
    public function getPeriodOfTime($dateFinish, $linkedaccountId) {
        $dates = null;
        $dateInit = $this->getFirstInvestmentDateByLinkedaccount($linkedaccountId);
        $dates = $this->getDatesForPastReturn($dateInit, $dateFinish);
        //future implementation
        /*if ($this->originExecution == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $dateInit = $this->getFirstInvestmentDateByLinkedaccount($linkedaccountId);
            $dates = $this->getDatesForPastReturn($dateInit, $dateFinish);
        }
        else {
            print_r($this->originExecution);
            $dates= $this->verifyPastReturnThisYearExist($dateFinish);
        }*/
        return $dates;
    }
    
    /**
     * Function to calculate the years between two dates
     * 
     * @param string $dateInit The initial date
     * @param string $dateFinish The final date
     * @return array
     */
    public function getDatesForPastReturn($dateInit, $dateFinish) {
        $dates = null;
        if (!empty($dateInit)) {
            $dateInitYear = date("Y",  strtotime($dateInit));
            //$dateInitTotal = date("Ymd",  strtotime($dateInit));
            $dateFinishYear = date("Y",  strtotime($dateFinish));
            //$dateFinishTotal = date("Ymd",  strtotime($dateFinish));
            $totalYears = $dateFinishYear - $dateInitYear;
            $dates = [];
            for ($i = 1; $i <= $totalYears; $i++) {
                $dates[] = $dateFinishYear - $i;
            }
        }
        return $dates;
        //$resultDate1 = $dateFinishTotal - $dateInitTotal;
        //$resultDate2 = ($dateFinishYear . "0000") - ($dateInitYear . "0000");
        /*if ($resultDate1 <= $resultDate2) {
            
        }
        else {

        }*/
    }
    
    /**
     * Function to get the first date there is movement in a linked account
     * 
     * @param integer $linkedaccountId
     * @return string The date of the first movement
     */
    public function getFirstInvestmentDateByLinkedaccount($linkedaccountId) {
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
        $dateInit = $this->Userinvestmentdata->find('first',
            array( 'order' => array('date ASC'),
                   'conditions' => array('Userinvestmentdata.linkedaccount_id' => $linkedaccountId),
                   'recursive' => -1,
                   'fields' => array('Userinvestmentdata.date')
            )  
        ); 
        return $dateInit['Userinvestmentdata']['date'];
    }
    
    /**
     * Function to get the correct value for the Formula
     * 
     */
    public function getValueFromFormula($stepsFormula, $formulaVariables) {
        $data = $stepsFormula[0];
        if (!is_numeric($stepsFormula[0])) {
            $data = $formulaVariables[$stepsFormula[0]];
        }
        return $data;
    }
    
    /**
     * Function to get a date converted for the format Y-m-d
     * 
     * @param string $date It is the date to convert
     * @param string $datePeriod  It is the format to calculate the date
     * @return string
     */
    public function getDateForSum($date, $datePeriod) {
        if (is_numeric($datePeriod)) {
            $time = strtotime($date . $datePeriod . " days");
            $dataDate = date("Y-m-d",$time);
        }
        else {
            //$start = mktime(0, 0, 0, 1, 1, $year);
            $dataDate = date("Y-m-d", mktime(0, 0, 0, $datePeriod['month'], $datePeriod['day'], $date));
        }
        return $dataDate;
    }
    
    /**
     * Function to get the sum of different values ordered by date
     * Documentation:
     * http://discourse.cakephp.org/t/how-to-sum-value-according-to-other-column-value-in-cakephp/1314/3
     * https://book.cakephp.org/2.0/en/models/virtual-fields.html
     * 
     * @param string $modelName It is the model name
     * @param array $values All the values we need from database to sum
     * @param array $keyValue It is the database key and the database value we need to get the values
     * @param array $date It contains the initial date and the final date
     * @param string $interval It is the type of interval for the values
     * @return array The sum values ordered by date
     */
    public function getSumValuesOrderedByDate($modelName, $values, $keyValue, $date, $interval = null) {
        $sumValues = $values;
        $nameSum = $values;
        if (is_array($values)) {
            $sumValues = null;
            $nameSum = null;
            foreach ($values['variables'] as $string)  {
                if (empty($nameSum)) {
                    $nameSum = $string;
                }
                $sumValues .= $string . " + ";
            }
            $sumValues = rtrim($sumValues,"+ ");
        }
        $model = ClassRegistry::init($modelName);
        $model->virtualFields = array('sum' => 'sum(' . $sumValues . ')');
        if ($interval !== "latest") {
            $sumValue  =  $model->find('list',array(
                    'fields' => array('date', 'sum'),
                    'group' => array('date'),
                    'conditions' => array(
                        "date >=" => $date['init'],
                        "date <=" => $date['finish'],
                        $keyValue['type'] => $keyValue['value']
                    )
                )
            );
        }
        else if ($interval === "latest") {
            $options['conditions'] = array(
                //"date >=" => $date['init'],
                "date <=" => $date['finish'],
                $keyValue['type'] => $keyValue['value']
            );
            $options['fields'] = array('date', 'sum');
            $options['group'] = array('date');
            $latestValue = $this->getLatestTotalsConsolidation($model, $options);
            if (!empty($latestValue)) {
                $sumValue[$date['finish']] = $latestValue['Userinvestmentdata']['sum'];
            }
            else {
                $options = [];
                $options['conditions'] = array($keyValue['type'] => $keyValue['value']);
                $options['fields'] = array('date');
                $options['order'] = array('date' => 'asc');
                $options['recursive'] = -1;
                $temp = $model->find("first", $options);
                $sumValue[$temp['Userinvestmentdata']['date']] = 0;
            }
             
        }
        return $sumValue;
    }
    
    /**
     * Function to merge different arrays by keyDate
     * @param array $arrays All the arrays to merge
     * @param array $variables All the variables in order to merge correctly the data
     * @return array with all the information merge correctly
     */
    public function mergeArraysByKey($arrays, $variables) {
        $dates = [];
        $data = [];
        foreach ($arrays as $array) {
            foreach ($array as $keyDate => $value) {
                if (!in_array($keyDate, $dates)) {
                    $dates[] = $keyDate;
                }
            }
        }
        sort($dates);
        $i = 0;
        foreach ($dates as $date) {
            $dataFormula = null;
            foreach ($variables as $variableKey => $variable) {
                if (!empty($arrays[$variableKey][$date])) {
                    $value = $arrays[$variableKey][$date];
                    $dataFormula = $this->winFormulas->doOperationByType($dataFormula, $value, $variable['operation']);
                }
            }
            $dateParts = explode("-", $date);
            if (empty($dataFormula)) {
                continue;
            }
            $data['dates'][$i] = mktime(0,0,0,$dateParts[1],$dateParts[2],$dateParts[0]);
            $data['values'][$i] = $dataFormula;
            $i++;

        }
        return $data;
    }
    
    /**
     * Function to get preformatted data in order to test the XIRR function of the financial class
     * @return string
     */
    public function returnDataPreformat() {
        $dataArray = [];
        $array = [
            "2015-11-19" => "-250",
            "2015-12-17" => "3.99809457497",
            "2016-01-17" => "4.09253899219",
            "2016-02-17" => "0.13543599831",
            "2016-02-22" => "4.03804840497",
            "2016-03-17" => "4.09253599219",
            "2016-04-17" => "0.0883495868",
            "2016-04-26" => "4.10946849198",
            "2016-07-08" => "8.80608016374",
            "2016-08-01" => "4.10939773645",
            "2016-08-22" => "4.1095392475",
            "2016-11-01" => "8.81551003905",
            "2016-11-28" => "4.10946849198",
            "2017-01-12" => "4.39823618313",
            "2017-02-07" => "4.37652967769",
            "2017-03-30" => "8.21893698396",
            "2017-04-17" => "0.63696138898",
            "2017-05-02" => "4.10946849197796",
            "2017-06-12" => "4.40357384839829",
            "2017-07-18" => "0.866697620601355",
            "2017-07-31" => "1.65663337787617",
            "2017-08-24" => "3.20259916247211",
            "2017-08-29" => "5.61380048746335",
            "2017-10-02" => "4.10946849197793",
            "2017-10-30" => "4.09980247375654",
            "2017-11-17" => "0.009666018221385",
            "2017-11-21" => "4.0828729739667",
            "2017-11-29" => "216.59873618302"
        ];
        $i = 0;
        $dataArray['dates'] = [];
        $dataArray['values'] = [];
        foreach ($array as $keyDate => $data) {
            $dateParts = explode("-", $keyDate);
            $dataArray['dates'][$i] = mktime(0,0,0,$dateParts[1],$dateParts[2],$dateParts[0]);
            $dataArray['values'][$i] = $data;
            $i++;
        }
        return $dataArray;
    }
    
    /**
     * Gets the latest (=last entry in DB) data of a model table
     * @param string    $model
     * @param array     $options Variable with all the options for the query
     * @return $temp array with the information needed
     */
    public function getLatestTotalsConsolidation($model, $options) {
        $options['recursive'] = -1;
        $options['order'] = array('date' => 'desc');
        $temp = $model->find("first", $options);
        return $temp;
    }   
    
    /**
     * Function to sum all the data of an array
     * 
     * @param array $values All the data
     * @return integer with the result
     */
    public function consolidateResults($values) {
        $result = "0";
        foreach ($values as $value) {
            $result = $this->winFormulas->doOperationByType($result, $value, "add");
        }
        return $result;
    }
    
    /**
     * Join together two or more arrays with the same keys with two levels
     * 
     * @param array $arrays It is an array of arrays
     * @param array $orderParam With orderParams if needed
     */
    public function joinTwoDimensionArrayTogether($arrays, $orderParam) {
        $dates = [];
        foreach ($arrays as $array) {
            foreach ($array as $key => $variableArray) {
                foreach ($variableArray as $keyDate => $value) {
                    if (!in_array($keyDate, $datesVariable[$key])) {
                        $datesVariable[$key][] = $keyDate;
                    }
                }
            }
        }
        foreach ($datesVariable as $key => $date) {
            sort($datesVariable[$key]);
        }
        $fullArray = [];
        //$fullArray = array_shift($arrays);
        $i = 0;
        foreach ($datesVariable as $key => $variableKey) {
            foreach ($variableKey as $key2 => $date) {
                $value = null;
                foreach ($arrays as $arrayKey => $array) {
                    if (empty($array[$key][$date])) {
                        continue;
                    }
                    if (empty($fullArray[$key][$date])) {
                        $fullArray[$key][$date] = "0";
                    }
                    $fullArray[$key][$date] = $this->winFormulas->doOperationByType($fullArray[$key][$date], $array[$key][$date], "add");
                }
            }
        }
        return $fullArray;
    }
    
    /**
     * Function to merge different arrays of dates to use in the global dashboard overview calculation
     * 
     * @param array $datesByLinkaccount Dates ordered by linkaccount id
     * @return array
     */
    public function mergeDatesByYear($datesByLinkaccount) {
        $datesForGlobal = [];
        foreach ($datesByLinkaccount as $dates) {
            foreach ($dates as $date) {
                if (!in_array($date, $datesForGlobal)) {
                    $datesForGlobal[] = $date;
                }
            }
        }
        rsort($datesForGlobal);
        return $datesForGlobal;
    }
    
    /**
     * Function to verify if it is a new year to calculate the past year NAR or Net Return
     * 
     * @param string $date It is the date we calculate the data
     */
    public function verifyPastReturnThisYearExist($date) {
        $dates = null;
        $dateYearNextDay = date("Y",  strtotime($date . "+ 1 day"));
        $dateYearToday = date("Y",  strtotime($date));
        if ($dateYearNextDay !== $dateYearToday) {
            $dates[] = $dateYearToday;
        }
        return $dates;
    }
    
    /**
     * Function to include the financial class needed to calculate XIRR
     */
    public function includeVendorFolder() {
        Configure::load('p2pGestor.php', 'default');
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "financial_class";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'financial_class.php');
        $this->financialClass = new Financial;
    }
    
}
