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
 * @date
 * @package
 */

/**
 * Class to keep Winvestify formulas to calculate Yield, outstanding
 *
 */
class WinFormulas {
    
    protected $variablesFormula_A = [
        "A" => [
            [
                "type" => "userinvestmentdata_totalGrossIncome",
                "table" => "Userinvestmentdata",
                "operation" => "add",
                "dayInit" => "1",
                "dayFinish" => "367",
                "intervals" => "inclusive"
            ],
            [
                "type" => "globalcashflowdata_platformTotalCost",
                "operation" => "substract",
                "dayInit" => "1",
                "dayFinish" => "367",
                "intervals" => "inclusive"
            ]
        ],
        "B" => [
            [
                "type" => "userinvestmentdata_outstandingPrincipal",
                "table" => "userinvestmentdata",
                //"operation" => "substract",
                "dayInit" => "1",
                "dayFinish" => "367",
                "intervals" => "inclusive"
            ],
            /*[
                "type" => "userinvestmentdata_outstandingPrincipal",
                //"operation" => "substract",
                "days" => "367",
                "intervals" => "exclusive"
            ],*/
            /*[
                "type" => "userinvestmentdata_partialPrincipalRepayment",
                "operation" => "substract",
                "days" => "1",
                "intervals" => "exclusive"
            ],
            [
                "type" => "userinvestmentdata_capitalRepayment",
                "operation" => "substract",
                "days" => "1",
                "intervals" => "exclusive"
            ],
            [
                "type" => "userinvestmentdata_capitalRepayment",
                "operation" => "substract",
                "days" => "1",
                "intervals" => "exclusive"
            ],
            [
                "type" => "userinvestmentdata_capitalRepayment",
                "operation" => "substract",
                "days" => "1",
                "intervals" => "exclusive"
            ],
            [
                "type" => "userinvestmentdata_capitalRepayment",
                "operation" => "substract",
                "days" => "1",
                "intervals" => "exclusive"
            ],*/
            
        ]
    ];
    
    protected $configFormula_A = [
        ["steps"] => [
            ["A", "B", "substract"],
            [""]
        ],
        ["type"] => [
            "type" => "yield"
        ]
        
    ];
    
    protected $variablesFormula_B;
    
    public function doOperationByType($inputA, $inputB, $type) {
        
        switch ($type) {
            case "add":
                $result = $this->addTwoValues($inputA, $inputB);
                break;
            case "substract":
                $result = $this->subtractTwoValues($inputA, $inputB);
                break;
            case "divide":
                $result = $this->divideTwoValues($inputA, $inputB);
                break;
            case "multiply":
                $result = $this->multiplyTwoValues($inputA, $inputB);
                break;
            case "pow":
                $result = $this->powTwoValues($inputA, $inputB);
                break;
        }
        return $result;
    }
    
    public function addTwoValues($inputA, $inputB) {
        return bcadd($inputA, $inputB, 2);
    }
    
    public function subtractTwoValues($inputA, $inputB) {
        return bcsub($inputA, $inputB, 2);
    } 
    
    public function divideTwoValues($inputA, $inputB) {
        return bcmul($inputA, $inputB, 2);
    } 
    
    public function multiplyTwoValues($inputA, $inputB) {
        return bcdiv($inputA, $inputB, 2);
    } 
    
    public function powTwoValues($inputA, $inputB) {
        return bcpow($inputA, $inputB, 2);
    }
    
    public function getFormulaParams($type) {
        
        switch($type) {
            case "formula_A":
                return $this->variablesFormula_A;
            case "formula_B":
                return $this->variablesFormula_B;
        }
    }
    
    public function getFormula($type) {
        
        switch($type) {
            case "formula_A":
                return $this->configFormula_A;
            case "formula_B":
                return $this->configFormula_B;
        }
    }
    
    public function getSumOfValue($modelName, $value, $dateInit, $dateFinish) {
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
        $model->virtualFields = array($value . '_sum' => 'sum('. $value. ')');
        $sumValue  =  $model->find('list',array(
                'fields' => array($modelName . 'linkedaccount_id', $value . '_sum'),
                'conditions' => array(
                    $modelName .  ".created >=" => $dateInit,
                    $modelName .  ".created <=" => $dateFinish
                )
            )
        );
        
        return $sumValue;
        
    }
    
}
