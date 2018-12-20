<?php
/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 200, http://www.winvestify.com                         |
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
 * @version 0.2
 * @date 2017-03-04
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
            ]     
        ];
       
    
    /**
     * Updates the status of a field from R/W to R/O or vice versa
     * 
     * Example: $fieldsToChange = ["investor_name" => "Pedro","investor_city" => "Madrid",....]
     *      or $fieldsToChange = ["name" => "Pedro","city" => "Madrid",....];

     * @param array $fieldsToChange array with all the fields to be changed and their new status
     * @return boolean
     */
    public function api_editCheck($investorId, $fieldsToChange) {
        
        $date = new DateTime('now');
        $datetime = $date->format('Y-m-d H:i:s');
            
        foreach ($fieldsToChange as $key => $newStatus) {
            $checkKeyNames = explode("_", $key);
            $checkKey = (count($checkKeyNames) == 2 ? $checkKeyNames[1]: $checkKeyNames[0]);

            $data['check_'.$checkKey] = $newStatus;
            $data['check_' . $checkKey . 'Time'] = $datetime;
        } 

        $tempId = $this->find('first', $params =['conditions' => ['investor_id' => $investorId],
                                                'fields' => 'id',
                                                'recursive' => -1]
                             );

        $data['id'] = $tempId['Check']['id'];

        if ($this->save($data, $validate = true)){
            return true;
        }
        else {
            return false;   
        }
    }
    


        
    
}
