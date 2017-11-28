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
        $this->GearmanWorker->addFunction('consolidation', array($this, 'consolidateUserData'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting GEARMAN_FLOW4 to listen to data from its Client\n";
        while( $this->GearmanWorker->work());
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
        //Future implementation
        //$formulaByInvestor = $this->getFormulasFromDB();
        foreach ($data["companies"] as $key => $company) {
            $i = 0;
            $formulasByInvestor[$company][$i]['formula'] = "formula_A";
            $formulasByInvestor[$company][$i]['variables'] = "formula_A";
            $i++;
            $formulasByInvestor[$company][$i]['formula'] = "formula_A";
            $formulasByInvestor[$company][$i]['variables'] = "formula_B";
        }
        
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
                        $dataFormula = $this->winFormulas->doOperationByType($dataFormula, current($value), $variableFormula['operation']);
                    }
                    $formulasByCompany[$linkaccountIdKey][$key]['formula']['variables'][$variablesKey] = $dataFormula;
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
                $result[$linkaccountIdKey] = $formula["result"];
            }
        }
        
        $data['tempArray'] = $result;
        
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
        } 
        
        return json_encode($data);
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
    
    /**
     * Function to initiate the formulas, in the future, this will be a config file
     */
    public function initFormula() {
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
        $this->config['chargeOffGlobal'] = 'userinvestmentdata.userinvestmentdata_myWallet';*/
    }
    
}
