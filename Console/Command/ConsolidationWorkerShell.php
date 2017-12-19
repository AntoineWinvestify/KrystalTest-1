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
        $this->GearmanWorker->addFunction('netAnnualPastReturn', array($this, 'calculateNetAnnualPastReturn'));
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
        
    }
    
    
    public function calculateNetAnnualReturn($data) {
        $variables = $this->winFormulas->getFormulaParams("formula_A");
        foreach ($data["companies"] as $linkedaccountId) {
            $formula[$linkedaccountId] = $this->winFormulas->getFormula("formula_A");
            foreach ($variables as $variableKey => $variable) {
                $dateInit = $this->getDateForSum($variable['dateInit']);
                $dateFinish = $this->getDateForSum($variable['dateFinish']);
                $value = $this->getSumOfValue($variable['table'], $variable['type'], $linkedaccountId, $dateInit, $dateFinish);
                $formula[$linkedaccountId]['formula']['variables'][$variableKey] = $value;
                //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
            }
        }
        print_r($formula);
        exit;
    }
    
    public function calculateNetAnnualReturnXirr($data) {
        $variables = $this->winFormulas->getFormulaParams("formula_A_xirr");
        $values = [];
        foreach ($data["companies"] as $linkedaccountId) {
            $keyDataForTable['type'] = 'linkedaccount_id';
            $keyDataForTable['value'] = $linkedaccountId;
            //$variablesName = [];
            /*foreach ($variables as $variableKey => $variable) {
                $date['init'] = $this->getDateForSum($variable['dateInit']);
                $date['finish'] = $this->getDateForSum($variable['dateFinish']);
                $values[$linkedaccountId][$variableKey] = $this->getSumValuesOrderedByDate($variable['table'], $variable['type'], $keyDataForTable, $date);
            }*/
            //$dataMergeByDate = $this->mergeArraysByKey($values[$linkedaccountId], $variables);
            $dataMergeByDate = $this->returnDataPreformat();
        }
        Configure::load('p2pGestor.php', 'default');
        $vendorBaseDirectoryClasses = Configure::read('vendor') . "financial_class";          // Load Winvestify class(es)
        require_once($vendorBaseDirectoryClasses . DS . 'financial_class.php');
        $financialClass = new Financial;
        $xirr = $financialClass->XIRR($dataMergeByDate['values'], $dataMergeByDate['dates']);
        echo "this is the xiiiiiiiiiiiiiiir " . $xirr*100 . " \n" ;
        exit;
    }
    
    public function calculateNetAnnualTotalFunds($data) {
    }
    
    public function calculateNetAnnualPastReturn($data) {
        $variables = $this->winFormulas->getFormulaParams("formula_A");
        foreach ($data["companies"] as $linkedaccountId) {
            $formula[$linkedaccountId] = $this->winFormulas->getFormula("formula_A");
            foreach ($variables as $variableKey => $variable) {
                $dates = $this->getPeriodOfTime($data["date"], $linkedaccountId);
                foreach ($dates as $date) {
                    $value = $this->getSumOfValue($variable['table'], $variable['type'], $linkedaccountId, $dateInit, $dateFinish);
                    $formula[$linkedaccountId]['formula']['variables'][$variableKey] = $value;
                }
                
                //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
            }
        }
        exit;
    }
    
    public function getPeriodOfTime($dateFinish, $linkedaccountId) {
        if ($this->originExecution == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $dateInit = $this->getFirstInvestmentDate($linkedaccountId);
            $dates = $this->getDatesForPastReturn($dateInit, $dateFinish);
        }
        else {
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
                   'conditions' => array('Userinvestmentdata.linkedaccount_id' => $linkedaccountId)
            )  
        ); 
        return $dateInit;
    }
    
    /**
     * Function to initiate the process to save the files of a company
     * @param object $job It is the object of Gearmanjob that contains
     * The $job->workload() function read the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     *      $data["companies"]                  array It contains all the linkedaccount information
     *      $data["queue_userReference"]        string It is the user reference
     *      $data["queue_id"]                   integer It is the queue id
     * @return json Json containing all the status collect and errors by link account id
     */
    public function consolidateUserData($job) {
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
        $formulasByInvestor = [];
        
        foreach ($formulasByInvestor as $linkaccountIdKey => $formulas) {
            $i = 0;
            foreach ($formulas as $formula) {
                $formulasByCompany[$linkaccountIdKey][$i]['formula'] = $this->winFormulas->getFormula($formula['formula']);
                $formulasByCompany[$linkaccountIdKey][$i]['variablesFormula'] = $this->winFormulas->getFormulaParams($formula['variables']);
                $i++;
            }
        }
        
        foreach ($formulasByCompany as $linkaccountIdKey => $formulas) {
            foreach ($formulas as $key => $formula) {
                foreach ($formula['variablesFormula'] as $variablesKey => $variablesFormula) {
                    //$formulasValue = [];
                    $dataFormula = null;
                    foreach ($variablesFormula as $variableFormula) {
                        $dateInit = $this->getDateForSum($variableFormula['dateInit']);
                        $dateFinish = $this->getDateForSum($variableFormula['dateFinish']);
                        $value = $this->winFormulas->getSumOfValue($variableFormula['table'], $variableFormula['type'], $linkaccountIdKey, $dateInit, $dateFinish);
                        //$dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
                    }
                    $formulasByCompany[$linkaccountIdKey][$key]['formula']['variables'][$variablesKey] = $value;
                }
            }
        }

        foreach ($formulasByCompany as $linkaccountIdKey => $formulas) {
            foreach ($formulas as $key => $formula) {
                print_r($formula);
                $dataFormula = null;
                foreach ($formula['formula']['steps'] as $stepsKey => $stepsFormula) {
                    $value = $this->getValueFromFormula($stepsFormula, $formula['formula']['variables']);
                    $dataFormula = $this->winFormulas->doOperationByType($dataFormula, $value, $stepsFormula[1]);
                }
                $formulasByCompany[$linkaccountIdKey][$key]['formula']['result']['data'] = $dataFormula;
            }
        }
        $i = 0;
        $formulasInvestorTotal[$i]['formula'] = "formula_A";
        $formulasInvestorTotal[$i]['variables'] = "formula_A";
        $i++;
        $formulasInvestorTotal[$i]['formula'] = "formula_A";
        $formulasInvestorTotal[$i]['variables'] = "formula_B";
        $i = 0;
        foreach ($formulasInvestorTotal as $formula) {
            $formulasTotal[$i]['formula'] = $this->winFormulas->getFormula($formula['formula']);
            $formulasTotal[$i]['variablesFormula'] = $this->winFormulas->getFormulaParams($formula['variables']);
            $i++;
        }
        
        foreach ($formulasTotal as $key => $formula) {
            foreach ($formula['variablesFormula'] as $variablesKey => $variablesFormula) {
                //$formulasValue = [];
                $dataFormula = null;
                foreach ($variablesFormula as $variableFormula) {
                    $dateInit = $this->getDateForSum($variableFormula['dateInit']);
                    $dateFinish = $this->getDateForSum($variableFormula['dateFinish']);
                    $value = $this->winFormulas->getSumOfValueByUserReference($variableFormula['table'], $variableFormula['type'], $data["queue_userReference"], $dateInit, $dateFinish);
                    $dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
                }
                $formulasTotal[$key]['formula']['variables'][$variablesKey] = $dataFormula;
            }
        }
        
        foreach ($formulasTotal as $key => $formula) {
            $dataFormula = null;
            foreach ($formula['formula']['steps'] as $stepsKey => $stepsFormula) {
                $value = $this->getValueFromFormula($stepsFormula, $formula['formula']['variables']);
                $dataFormula = $this->winFormulas->doOperationByType($dataFormula, $value, $stepsFormula[1]);
            }
            $formulasTotal[$key]['formula']['result']['data'] = $dataFormula;
        }
        
        $result = [];
        
        foreach ($formulasByCompany as $linkaccountIdKey => $formulas) {
            foreach ($formulas as $key => $formula) {
                $result[$linkaccountIdKey][] = $formula["formula"]["result"];
            }
        }
        
        $returnData['tempArray'] = $result;
        
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
        } 
        print_r($returnData);
        return json_encode($returnData);
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
    
    public function getDateForSum($date) {
        if (is_numeric($date)) {
            $dataDate = date("Y-m-d",strtotime($date . " days"));
        }
        else {
            $year = date('Y') + $date['year']; // Get current year and subtract 1
            //$start = mktime(0, 0, 0, 1, 1, $year);
            $dataDate = date("M-d-Y", mktime(0, 0, 0, $date['month'], $date['day'], $year));
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
    
    public function getSumValuesOrderedByDate($modelName, $value, $keyValue, $date) {
        $model = ClassRegistry::init($modelName);
        $sumValues = $value;
        $nameSum = $value;
        if (is_array($value)) {
            $nameSum = null;
            foreach ($value as $string)  {
                if (empty($nameSum)) {
                    $nameSum = $string;
                }
                $sumValues = $string . " + ";
            }
            $sumValues = rtrim($sumValues,"+ ");
        }
        
        $model->virtualFields = array($nameSum . '_sum' => 'sum(' . $sumValues . ')');
        $sumValue  =  $model->find('list',array(
                'fields' => array('date', $nameSum . '_sum'),
                'group' => array('date'),
                'conditions' => array(
                    "date >=" => $date['init'],
                    "date <=" => $date['finish'],
                    $keyValue['type'] => $keyValue['value']
                )
            )
        );
        print_r($sumValue);
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
            $data['dates'][$i] = mktime(0,0,0,$dateParts[2],$dateParts[1],$dateParts[0]);
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
     * Function to initiate the formulas, in the future, this will be a config file
     */
    /*public function initFormula() {
        $this->formula[0]['eval'] = "$interestPaidGlobalOld-$interestPaidOld+$interestPaidNew";
        $this->formula[0]['externalName'] = 'interestPaidGlobal';
        $this->formula[0]['internalName'] = 'newuserinvestmentdatas.newuserinvestmentdata_interestPaidGlobal';
        $this->formula[0]['param'][0]['externalName'] = 'interestPaidNew';
        $this->formula[0]['param'][0]['internalName'] = 'newuserinvestmentdatas.newuserinvestmentdata_interestPaid';
        $this->formula[0]['param'][0]['period'] = 'exclusive';
        $this->formula[0]['param'][0]['date'] = '0';
        $this->formula[0]['param'][0]['externalName'] = 'interestPaidOld';
        $this->formula[0]['param'][0]['internalName'] = 'newuserinvestmentdatas.newuserinvestmentdata_interestPaid';
        $this->formula[0]['param'][0]['period'] = 'exclusive';
        $this->formula[0]['param'][0]['date'] = '365';
        $this->formula[0]['param'][0]['externalName'] = 'interestPaidGlobalOld';
        $this->formula[0]['param'][0]['internalName'] = 'newuserinvestmentdatas.newuserinvestmentdata_interestPaidGlobal';
        $this->formula[0]['param'][0]['period'] = 'exclusive';
        $this->formula[0]['param'][0]['date'] = '1';
        
        
        
        /*$this->formula[1]['eval'] = "(1+(($interestPaidGlobal+$chargeOffGlobal)/$outstandingPrincipalGlobal)^365)-1";
        $this->formula[1]['externalName'] = 'profitability';
        $this->formula[1]['internalName'] = 'newuserinvestmentdatas.newuserinvestmentdata_profitability';
        $this->formula[1]['param'][0] = 'interestPaidGlobal';
        $this->formula[1]['param'][1] = 'chargeOffGlobal';
        $this->formula[1]['param'][2] = 'outstandingPrincipalGlobal';
        
        $this->config['interestPaidGlobal'] = "newuserinvestmentdatas.newuserinvestmentdata_interestPaidGlobal";
        $this->config['chargeOffGlobal'] = 'userinvestmentdata.userinvestmentdata_myWallet';
    }*/
    
    
    
}
