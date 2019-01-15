<?php
/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2019, http://www.winvestify.com                         |
  // +-----------------------------------------------------------------------+
  // | This file is free software; you can redistribute it and/or modify     |
  // | it under the terms of the GNU General Public License as published by  |
  // | the Free Software Foundation; either version 2 of the License, or     |
  // | (at your option) any later version.                                   |
  // | This file is distributed in the hope that it will be useful           |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
  // | GNU General Public License for more details.                          |
  // +-----------------------------------------------------------------------+
  //
 * @author
 * @version 0.1
 * @date 2019-01-04
 * @package
 */
/*



 */

App::uses("AppModel", "Model");
class Check extends AppModel {

    var $name = 'Check';
    public $hasOne = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' => 'check_id',
            'fields' => '',
            'order' => '',
        )

    );
 
   var $validate = [
        'check_name' => [
            'rule' => ['boolean'],
            'message' => 'attribute for name should be a boolean'
            ],
        'check_surname' => [
            'rule' => ['boolean'],
            'message' => 'attribute for surname should be a boolean'
            ], 
         'check_DNI' => [
            'rule' => ['boolean'],
            'message' => 'attribute for DNI should be a boolean'
            ],
        'check_dateOfBirth' => [
            'rule' => ['boolean'],
            'message' => 'attribute for dateOfBirth should be a boolean'
            ], 
         'check_email' => [
            'rule' => ['boolean'],
            'message' => 'attribute for email should be a boolean'
            ],
        'check_telephone' => [
            'rule' => ['boolean'],
            'message' => 'attribute for telephone should be a boolean'
            ], 
         'check_postCode' => [
            'rule' => ['boolean'],
            'message' => 'attribute for postCode should be a boolean'
            ],
        'check_address1' => [
            'rule' => ['boolean'],
            'message' => 'attribute for address1 should be a boolean'
            ], 
        'check_address2' => [
            'rule' => ['boolean'],
            'message' => 'attribute for address2 should be a boolean'
            ],
        'check_city' => [
            'rule' => ['boolean'],
            'message' => 'attribute for city should be a boolean'
            ], 
         'check_country' => [
            'rule' => ['boolean'],
            'message' => 'attribute for country should be a boolean'
            ],
        'check_iban' => [
            'rule' => ['boolean'],
            'message' => 'attribute for iban should be a boolean'
            ],    
        'check_businessName' => [
            'rule' => ['boolean'],
            'message' => 'attribute for businessName should be a boolean'
            ],       
        ];
       
    
    /**
     * Updates the status of a field from R/W to R/O or vice versa
     * 
     * Example: $fieldsToChange = ["investor_name" => true,"investor_city" => false, ....]
     *      or $fieldsToChange = ["name" => true,"city" => false, ....];
     * 
     * @param int $investorId The database reference of its parent object Investor
     * @param array $fieldsToChange array with all the fields to be changed and their new status
     * @return boolean
     */
    public function api_editCheck($investorId, $fieldsToChange) {
        if (empty($investorId)) {
            return false;
        }        
        $date = new DateTime('now');
        $datetime = $date->format('Y-m-d H:i:s');
         
        foreach ($fieldsToChange as $key => $newStatus) {
            $checkKeyNames = explode("_", $key);
            $checkKey = (count($checkKeyNames) == 2 ? $checkKeyNames[1]: $checkKeyNames[0]);

            $data['check_'. $checkKey] = $newStatus;
            $data['check_' . $checkKey . 'Time'] = $datetime;
        } 

        $tempId = $this->find('first', $params =['conditions' => ['investor_id' => $investorId],
                                                'fields' => 'id',
                                                'recursive' => -1]
                             );
 
        if (empty($tempId)) {
            return false;
        }
        
        $data['id'] = $tempId['Check']['id'];

        if ($this->save($data, $validate = true)){
            return true;
        }
        return false;   
    }
    

    /**
     * Creates a new 'Check' object for an Investor
     * 
     * @param int $investorId The internal reference to the Investor
     * @param array $readOnlyFields array with all the fields which shall be defined as READ/ONLY
     *                                    example: ['investor_telephone', 'investor_email', 'investor_surname']
     * @return boolean
     */
    public function api_addCheck($investorId, $readOnlyFields) {
        if (empty($readOnlyFields) || empty($investorId)) {
            return false;
        }
        foreach ($readOnlyFields as $field) {
            $newReadOnlyIndex[$field] = WIN_READONLY;
        }
        $data['investor_id'] = $investorId;

        if ($this->save($data, $validate = true)){
            $result = $this->api_editCheck($investorId, $newReadOnlyIndex);
            if (!$result) {
                $this->delete($investorId);
                return false;
            }
            return $this->id;
        }
        return false;         
    }
    
    
}
