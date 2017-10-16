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
 * @author antoiba
 */
class ConsolidationWorkerShell extends GearmanWorkerShell {
    
    protected $formula = [];
    protected $config = [];
    
   
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
        $this->queueCurls = new \cURL\RequestsQueue;
        //If we use setQueueCurls in every class of the companies to set this queueCurls it will be the same?
        $index = 0;
        $i = 0;
        foreach ($data["companies"] as $linkedaccount) {
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
            $formulas = $newComp->getFormulas();
            foreach ($formulas as $formula) {
                foreach ($formula['param'] as $param) {
                    //Info about Variable variable 
                    //http://php.net/manual/en/language.variables.variable.php
                    $$param = $this->getValue($param, $linkedaccount['Linkedaccount']['id']);
                }
            }
            
        }
    }
    
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
                $options['joins'] = array(
                    array('table' => 'roles_sectors',
                        'alias' => 'RolesSector',
                        'type' => 'inner',
                        'conditions' => array(
                            'Sector.id = RolesSector.sector_id'
                        )
                    ),
                    array('table' => 'roles',
                        'alias' => 'Role',
                        'type' => 'inner',
                        'conditions' => array(
                            'RolesSector.role_id = Role.id'
                        )
                    )
                );
                break;
        }
        $params['fields'] = array($var);
         $options['conditions'] = array(
            'Role.id' => $roleId
        );
        //$options['field'] = array('Sector.*');
        $options['recursive'] = -1;
        $options['order'] = array(
            'Sector.sectors_father',
            'Sector.sectors_subSectorSequence'
        );
        $result = $model->find('first', $options);
        return $result;
    }
    
    public function initFormula() {
        $this->formula[0]['eval'] = $investment - $total;
        $this->formula[0]['externalName'] = 'cashDraw';
        $this->formula[0]['internalName'] = 'userinvestmentdata.userinvestmentdata_cashDraw';
        $this->formula[0]['param'][0] = 'investment';
        $this->formula[0]['param'][1] = 'total';
        
        $this->config['investment'] = "investment.investment_deposits";
        $this->config['total'] = 'userinvestmentdata.userinvestmentdata_myWallet';
    }
    
    
}
