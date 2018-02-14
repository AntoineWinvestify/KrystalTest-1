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
 * Description of ConsolidationWorkerShell
 *
 */
class ConsolidationWorkerShell extends GearmanWorkerShell {
    
    protected $formula = [];
    protected $config = [];
    protected $originExecution;
    protected $dashboardOverviewLinkaccountIds = [];
    
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
        //$this->GearmanWorker->addFunction('consolidation', array($this, 'consolidateUserData'));
        $this->GearmanWorker->addFunction('netAnnualReturn', array($this, 'calculateNetAnnualReturn'));
        $this->GearmanWorker->addFunction('netAnnualTotalFunds', array($this, 'calculateNetAnnualTotalFunds'));
        //$this->GearmanWorker->addFunction('netAnnualPastReturn', array($this, 'calculateNetAnnualPastReturn'));
        $this->GearmanWorker->addFunction('getFormulaCalculate', array($this, 'getFormulaCalculate'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting GEARMAN_FLOW4 to listen to data from its Client\n";
        while( $this->GearmanWorker->work());
    }
    
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
    
    public function calculateNetAnnualReturnXirr($data) {
        $variables = $this->winFormulas->getFormulaParams("netAnnualReturn_xirr");
        $values = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $this->dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
            }
            $dataMergeByDate[$linkedaccountId] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
        }
        $returnData = null;
        Configure::load('p2pGestor.php', 'default');
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "financial_class";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'financial_class.php');
        $financialClass = new Financial;
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            $returnData[$linkedaccountId]['netAnnualReturnXirr'] = $financialClass->XIRR($dataByLinkedaccountId['values'], $dataByLinkedaccountId['dates']);
        }
        /*** IMPROVED STRUCTURE **********/
        foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $this->dashboardOverviewLinkaccountIds)) {
                $this->dashboardOverviewLinkaccountIds[] = $companyNothingInProgress;
            }
           
        }
        
        $keyDataForTable['type'] = 'linkedaccount_id';
        $keyDataForTable['value'] = $this->dashboardOverviewLinkaccountIds;
        $values = [];
        foreach ($variables as $variableKey => $variable) {
            $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
            $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
            $values[$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
        }
        $dataMergeByDateForInvestor = $this->mergeArraysByKey($values, $variables);
        $returnData['investor'][$data["queue_userReference"]]['netAnnualReturnXirr'] = $financialClass->XIRR($dataMergeByDateForInvestor['values'], $dataMergeByDateForInvestor['dates']);
        
        //print_r($returnData);
        /////////////////////
        $statusCollect = [];
        foreach ($returnData as $linkedaccountIdKey => $variable) {
            $statusCollect[$linkedaccountIdKey] = "0";
            if ($variable == "0") {
                $statusCollect[$linkedaccountIdKey] = "1";
            }
            else if (!empty($variable)){
                $statusCollect[$linkedaccountIdKey] = "1";
            }
        }
        /*print_r($returnData);
        
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "PHPExcel/PHPExcel/Calculation";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'Financial.php');
        $financialClass = new PHPExcel_Calculation_Financial;
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            $returnData[$linkedaccountId]['netAnnualReturnXirr'] = $financialClass->XIRR($dataByLinkedaccountId['values'], $dataByLinkedaccountId['dates']);
        }
        */
        print_r($returnData);
        $dataArray['statusCollect'] = $statusCollect;
        $dataArray['tempArray'] = $returnData;
        return json_encode($dataArray);
    }
    
    public function calculateNetAnnualTotalFundsReturnXirr($data) {
        $variables = $this->winFormulas->getFormulaParams("netAnnualTotalFundsReturn_xirr");
        $values = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $this->dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
            }
            $dataMergeByDate[$linkedaccountId] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
            //$dataMergeByDate = $this->returnDataPreformat();
        }
        //print_r($dataMergeByDate);
        $returnData = null;
        Configure::load('p2pGestor.php', 'default');
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "financial_class";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'financial_class.php');
        $financialClass = new Financial;
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            $returnData[$linkedaccountId]['netAnnualTotalFundsReturnXirr'] = $financialClass->XIRR($dataByLinkedaccountId['values'], $dataByLinkedaccountId['dates']);
        }
        
        /*** IMPROVED STRUCTURE **********/
       foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $this->dashboardOverviewLinkaccountIds)) {
                $this->dashboardOverviewLinkaccountIds[] = $companyNothingInProgress;
            }
           
        }
        
        $keyDataForTable['type'] = 'linkedaccount_id';
        $keyDataForTable['value'] = $this->dashboardOverviewLinkaccountIds;
        $values = [];
        foreach ($variables as $variableKey => $variable) {
            $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
            $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
            $values[$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
        }
        $dataMergeByDateForInvestor = $this->mergeArraysByKey($values, $variables);
        $returnData['investor'][$data["queue_userReference"]]['netAnnualTotalFundsReturnXirr'] = $financialClass->XIRR($dataMergeByDateForInvestor['values'], $dataMergeByDateForInvestor['dates']);
        
        //print_r($returnData);
        /////////////////////
        
        print_r($returnData);
        $dataArray['tempArray'] = $returnData;
        return json_encode($dataArray);
    }
    
    public function calculateNetAnnualReturnPastYearXirr($data) {
        $variables = $this->winFormulas->getFormulaParams("netAnnualPastReturn_xirr");
        $this->originExecution = $data['originExecution'];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $this->dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            $dates = $this->getPeriodOfTime($data["date"], $linkedaccountId);
            foreach ($dates as $keyDate => $dateYear) {
                foreach ($variables as $variableKey => $variable) {
                    $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                    $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                    $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
                }
                $dataMergeByDate[$linkedaccountId][$dateYear] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
                //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
            }
        }
        //print_r($dataMergeByDate);
        $returnData = null;
        Configure::load('p2pGestor.php', 'default');
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "financial_class";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'financial_class.php');
        $financialClass = new Financial;
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            foreach ($dataByLinkedaccountId as $keyDate => $dataByDate) {
                $returnData[$linkedaccountId]['netAnnualReturnPastYearXirr'][$keyDate] = $financialClass->XIRR($dataByDate['values'], $dataByDate['dates']);
            }
        }
        
        /*** IMPROVED STRUCTURE **********/
        foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $this->dashboardOverviewLinkaccountIds)) {
                $this->dashboardOverviewLinkaccountIds[] = $companyNothingInProgress;
            }
           
        }
        $keyDataForTable['type'] = 'linkedaccount_id';
        $keyDataForTable['value'] = $this->dashboardOverviewLinkaccountIds;
        $dates = $this->getPeriodOfTime($data["date"], $this->dashboardOverviewLinkaccountIds);
        print_r($dates);
        $values = [];
        $dataMergeByDate = [];
        
        foreach ($dates as $keyDate => $dateYear) {
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                $values[$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
            }
            $dataMergeByDate[$dateYear] = $this->mergeArraysByKey($values, $variables);
            //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
        }

        foreach ($dataMergeByDate as $keyDate => $dataByDate) {
            $returnData['investor'][$data["queue_userReference"]]['netAnnualReturnPastYearXirr'][$keyDate] = $financialClass->XIRR($dataByDate['values'], $dataByDate['dates']);
        }
        
        //print_r($returnData);
        /////////////////////
        $dataArray['tempArray'] = $returnData;
        return json_encode($dataArray);
    }
    
    public function calculateNetReturn($data) {
        $variables = $this->winFormulas->getFormulaParams("netReturn");
        $values = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $this->dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
            }
            $dataMergeByDate[$linkedaccountId] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
        }
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            $returnData[$linkedaccountId]['netReturn'] = $this->consolidateResults($dataByLinkedaccountId['values']);
        }
        
         /*** IMPROVED STRUCTURE **********/
       foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $this->dashboardOverviewLinkaccountIds)) {
                $this->dashboardOverviewLinkaccountIds[] = $companyNothingInProgress;
            }
           
        }
        
        $keyDataForTable['type'] = 'linkedaccount_id';
        $keyDataForTable['value'] = $this->dashboardOverviewLinkaccountIds;
        $values = [];
        foreach ($variables as $variableKey => $variable) {
            $date['init'] = $this->getDateForSum($data['date'], $variable['dateInit']);
            $date['finish'] = $this->getDateForSum($data['date'], $variable['dateFinish']);
            $values[$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
        }
        $dataMergeByDateForInvestor = $this->mergeArraysByKey($values, $variables);
        $returnData['investor'][$data["queue_userReference"]]['netReturn'] = $this->consolidateResults($dataMergeByDateForInvestor['values']);
        
        //print_r($returnData);
        /////////////////////
        
        print_r($returnData);
        $dataArray['tempArray'] = $returnData;
        return json_encode($dataArray);
    }
    
    public function calculateNetReturnPastYear($data) {
        $variables = $this->winFormulas->getFormulaParams("netPastReturn");
        $this->originExecution = $data['originExecution'];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            $this->dashboardOverviewLinkaccountIds[] = $linkedaccountId;
            $dates = $this->getPeriodOfTime($data["date"], $linkedaccountId);
            foreach ($dates as $keyDate => $dateYear) {
                foreach ($variables as $variableKey => $variable) {
                    $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                    $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                    $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
                }
                $dataMergeByDate[$linkedaccountId][$dateYear] = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
                //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
            }
        }
        $returnData = null;
        foreach ($dataMergeByDate as $linkedaccountId => $dataByLinkedaccountId) {
            foreach ($dataByLinkedaccountId as $keyDate => $dataByDate) {
                $returnData[$linkedaccountId]['netReturnPastYear'][$keyDate] = $this->consolidateResults($dataByDate['values']);
            }
        }
        
          /*** IMPROVED STRUCTURE **********/
       foreach ($data['companiesNothingInProgress'] as $companyNothingInProgress) {
            if (!in_array($companyNothingInProgress, $this->dashboardOverviewLinkaccountIds)) {
                $this->dashboardOverviewLinkaccountIds[] = $companyNothingInProgress;
            }
           
        }
        
        $keyDataForTable['type'] = 'linkedaccount_id';
        $keyDataForTable['value'] = $this->dashboardOverviewLinkaccountIds;
        $dates = $this->getPeriodOfTime($data["date"], $this->dashboardOverviewLinkaccountIds);
        $values = [];
        $dataMergeByDate = [];
        
        foreach ($dates as $keyDate => $dateYear) {
            foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($dateYear, $variable['dateInit']);
                $date['finish'] = $this->getDateForSum($dateYear, $variable['dateFinish']);
                $values[$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date, $variable['intervals']);
            }
            $dataMergeByDate[$dateYear] = $this->mergeArraysByKey($values, $variables);
            //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
        }

        foreach ($dataMergeByDate as $keyDate => $dataByDate) {
            $returnData['investor'][$data["queue_userReference"]]['netReturnPastYear'][$keyDate] = $this->consolidateResults($dataByDate['values']);
        }
        
        /////////////////
        
        print_r($returnData);
        $dataArray['tempArray'] = $returnData;
        return json_encode($dataArray);
    }
    
    public function getPeriodOfTime($dateFinish, $linkedaccountId) {
        if ($this->originExecution == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $dateInit = $this->getFirstInvestmentDateByLinkedaccount($linkedaccountId);
            $dates = $this->getDatesForPastReturn($dateInit, $dateFinish);
        }
        //Else is not working yet
        else {
            print_r($this->originExecution);
            echo "\n";
            exit;
            $dateFinishYear = date("Y",  strtotime($dateFinish));
            $pastReturnExist = $this->verifyPastReturnThisYearExist($dateFinishYear);
            $dates = null;
            if (!$pastReturnExist) {
                $dates = $dateFinishYear;
            }
        }
        return $dates;
    }
    
    public function getDatesForPastReturn($dateInit, $dateFinish) {
        $dateInitYear = date("Y",  strtotime($dateInit));
        //$dateInitTotal = date("Ymd",  strtotime($dateInit));
        $dateFinishYear = date("Y",  strtotime($dateFinish));
        //$dateFinishTotal = date("Ymd",  strtotime($dateFinish));
        $totalYears = $dateFinishYear - $dateInitYear;
        $dates = [];
        for ($i = 1; $i <= $totalYears; $i++) {
            $dates[] = $dateFinishYear - $i;
        }
        return $dates;
        //$resultDate1 = $dateFinishTotal - $dateInitTotal;
        //$resultDate2 = ($dateFinishYear . "0000") - ($dateInitYear . "0000");
        /*if ($resultDate1 <= $resultDate2) {
            
        }
        else {

        }*/
    }
    
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
    
    public function getSumOfValue($modelName, $value, $linkedaccountId, $dateInit, $dateFinish) {
        /*$total = $this->RequestedItem->find('all', 
                    array(
                        array(
                            'fields' => array(
                                'sum(Model.cost * Model.quantity)   AS ctotal'
                                ), 'conditions'=>array(
                                        'RequestedItem.purchase_request_id'=>$this->params['named']['po_id']
                                    )
                            )
                        )
                );
        
        $virtualFields = array('total' => 'SUM(Model.cost * Model.quantity)');
        $total = $this->RequestedItem->find('all', array(array('fields' => array('total'), 'conditions'=>array('RequestedItem.purchase_request_id'=>$this->params['named']['po_id']))));
        
        $this->Member->Point->virtualFields['total'] = 'SUM(Point.points)';
        $totalPoints = $this->Member->Point->find('all', array('fields' => array('total')));*/
        
        //get sum of value depending on another field with cakephp
        //http://discourse.cakephp.org/t/how-to-sum-value-according-to-other-column-value-in-cakephp/1314/3
        //https://book.cakephp.org/2.0/en/models/virtual-fields.html
        
        
        $model = ClassRegistry::init($modelName);
        
        echo "value is $value \n";
        echo "dateInit is $dateInit";
        echo "dateFinish is $dateFinish";
        
        
        $model->virtualFields = array($value . '_sum' => 'sum('. $value. ')');
        $sumValue  =  $model->find('list',array(
                'fields' => array('linkedaccount_id', $value . '_sum'),
                'conditions' => array(
                    "date >=" => $dateInit,
                    "date <=" => $dateFinish,
                    "linkedaccount_id" => $linkedaccountId
                )
            )
        );
        return $sumValue;
        
    }
    
    public function getSumOfValueByUserReference($modelName, $value, $userReference, $dateInit, $dateFinish) {
        $model = ClassRegistry::init($modelName);
        
        echo "value is $value \n";
        echo "dateInit is $dateInit";
        echo "dateFinish is $dateFinish";
        
        
        $model->virtualFields = array($value . '_sum' => 'sum('. $value. ')');
        $sumValue  =  $model->find('list',array(
                'fields' => array('userinvestmentdata_investorIdentity', $value . '_sum'),
                'conditions' => array(
                    "date >=" => $dateInit,
                    "date <=" => $dateFinish,
                    "userinvestmentdata_investorIdentity" => $userReference
                )
            )
        );
        return $sumValue;
        
    }
    
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
                $options['fields'] = array('date');
                $options['order'] = array('date' => 'asc');
                $options['recursive'] = -1;
                $temp = $model->find("first", $options);
                $sumValue[$temp['Userinvestmentdata']['date']] = 0;
            }
             
        }
        return $sumValue;
    }
    
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
     * @param array     $filterConditions
     *
     * @return array with data
     *          or false if $elements do not exist in two dimensional array
     */
    public function getLatestTotalsConsolidation($model, $options) {
        $options['recursive'] = -1;
        $options['order'] = array('date' => 'desc');
        $temp = $model->find("first", $options);
        return $temp;
    }   
    
    public function consolidateResults($values) {
        $result = "0";
        foreach ($values as $value) {
            $result = $this->winFormulas->doOperationByType($result, $value, "add");
            /*echo "\n result ====>   ";
            print_r($result);*/
        }
        return $result;
    }
    
}
