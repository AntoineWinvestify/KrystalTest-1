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
            $formulasByInvestor[$company][$key]['formula'] = "formula_A";
            $formulasByInvestor[$company][$key]['variables'] = "formula_A";
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
                    $data = null;
                    foreach ($variablesFormula as $variableFormula) {
                        $dateInit = date("Y-m-d",strtotime($variableFormula['dateInit'] . " days"));
                        $dateFinish = date("Y-m-d",strtotime($variableFormula['dateFinish'] . " days"));
                        $value = $this->winFormulas->getSumOfValue($variableFormula['table'], $variableFormula['type'], $linkaccountIdKey, $dateInit, $dateFinish);
                        $data = $this->winFormulas->doOperationByType($data, $value, $variableFormula['operation']);
                    }
                    $formulasByCompany[$linkaccountIdKey][$key]['formula']['variables'][$variablesKey] = $data;
                }
            }
        }
        
        print_r($formulasByCompany);
        exit;
        
        
        
        /*foreach ($data["companies"] as $linkedaccount) {
            unset($newComp);
            $index++;
            echo "<br>******** Executing the loop **********<br>";
            $companyId = $linkedaccount['Linkedaccount']['company_id'];
            echo "companyId = $companyId <br>";
            $companyConditions = array('Company.id' => $companyId);
            $result = $this->Company->getCompanyDataList($companyConditions);
            $newComp = $this->companyClass($result[$companyId]['company_codeFile']); // create a new instance of class zank, comunitae, etc.
            $newComp->defineConfigParms($result[$companyId]);  // Is this really needed??
            $newComp->setQueueId($data["queue_id"]);
            $newComp->setCompanyName($result[$companyId]['company_codeFile']);
            $newComp->setUserReference($data["queue_userReference"]);
            $newComp->setLinkAccountId($linkedaccount['Linkedaccount']['id']);
            ///Maybe this is needed 
            //https://book.cakephp.org/2.0/en/core-utility-libraries/time.html#CakeTime::dayAsSql
            $formulas = $newComp->getFormulas();
            foreach ($formulas as $formula) {
                foreach ($formula['param'] as $param) {
                    //Info about Variable variable 
                    //http://php.net/manual/en/language.variables.variable.php
                    $$param = $this->getValue($param, $linkedaccount['Linkedaccount']['id']);
                }
            }
            
        }*/
    }
    
    /**
     * Function to get a value from the database
     * @param string $var It is a variable with the table and the data that we should take from database
     * @param iny $linkaccountId It is the linkaccount from which we must take the data
     * @return array With the data containing the information needed
     */
    public function getValue($var, $linkaccountId) {
        $consult = explode('.', $this->config[$var]);
        $model;
        switch($consult[0]) {
            case "investment": 
                $model = $this->Invesment;
                break;
            case "userinvestmentdata":
                $model = $this->Userinvestmentdata;
                break;
            case "payments":
                $model = $this->Transaction;
                break;
        }
        return $result;
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
