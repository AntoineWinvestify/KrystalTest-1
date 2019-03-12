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
  // | Author: Antoine de Poorter                                            |
  // +-----------------------------------------------------------------------+
  //
* @author 
* @version 0.1
* @date 2019-01-31
* @package
*/
/*
2019-01-31	  version 0.1




Pending:



*/


class Pollingresource extends AppModel
{
    var $name= 'Pollingresource';

    var $defaultFields = [ 
        'investor' => ['id', 
                        'pollingresource_userIdentification', 
                        'pollingresource_newValueExists', 
                        'pollingresource_interval', 
                        'pollingresource_type', 
    //                  'pollingresource_value',
    //                  'pollingresource_resourceId',
                        'pollingresource_links'
                      ],
        'winAdmin' => ['id', 
                        'pollingresource_userIdentification', 
                        'pollingresource_newValueExists', 
                        'pollingresource_interval', 
                        'pollingresource_type', 
                        'pollingresource_value',
                        'pollingresource_resourceId',
                        'pollingresource_links',
                        'modified',
                        'created'
            ],              
        'superAdmin' => ['id', 
                        'pollingresource_userIdentification', 
                        'pollingresource_newValueExists', 
                        'pollingresource_interval', 
                        'pollingresource_type', 
                        'pollingresource_value',
                        'pollingresource_resourceId',
                        'pollingresource_links',
                        'modified',
                        'created'            
                      ],                
    ];
    
      
    /** 
     * Call back 
     * Rules are defined for what should happen after a database record has been read
     * - Resets the pollingresource_newValueExists flag after it has been read
     * 
     * @param array $results Array that contains the returned results from the model’s find operation
     * @param boolean $primary Indicates whether or not the current model was the model that the query originated on 
     * @return array The (possibly modified) result(s) of the find operation. 
     */    
    function afterFind($results, $primary = false)  {

        foreach ($results as $key => $result) {

            if (isset($result['Pollingresource']['pollingresource_newValueExists'])) {
                if ($result['Pollingresource']['pollingresource_newValueExists'] == 1) {
                    $this->clear();
                    $this->id = $result['Pollingresource']['id']; 
                    $this->saveField('pollingresource_newValueExists', 0);
                }
            }
        }     
        return $results;
    }  
    
    
    /**
     * Deletes a Pollingresources object.
     * 
     * @param int $id The internal identification of the object to delete
     * @return boolean
     */   
    public function api_deletePollingresource($id) {
        $this->delete($id);
        return;
    }
 
    
    
    /** 
     * Reads the list of defaultFields to read in case the webclient has not indicated any fields
     * in its GET requests
     * 
     * @param $roleName The name of the role for whom the list of defaults fields is read
     * @return array $list  An array with the names of the fields. The names are the internal names of the fields
     */
    public function getDefaultFields($roleName) {
        return $this->defaultFields[$roleName];        
    }    


    /** 
     * Determines if the current user (by means of its $investorId) is the direct or indirect owner
     * of the current Model. 
     * This functionality determines if a webclient may access the data of another webclient
     * with proper R/W permissions.
     * 
     * @param $investorId The internal reference of the investor Object
     * @param $id The internal reference of the Pollingresource object to be checked
     * @return int  (WIN_ACL_INVESTOR_IS_OWNER, WIN_ACL_INVESTOR_IS_NOT_OWNER, WIN_ACL_RESOURCE_DOES_NOT_EXIST)
     */
    public function isOwner($investorId, $id) {
      
        $result = $this->find("first", $params = ['conditions' => [ 'id' => $id,
                                                         'pollingresource_status' => ACTIVE],   
                                                  'fields' => ['pollingresource_useridentification'],
                                                  'recursive' => -1]);
      
        if (empty($result)) {
 //           echo __FILE__ . " " . __LINE__ ." WIN_ACL_RESOURCE_DOES_NOT_EXIST<br>";
            return WIN_ACL_RESOURCE_DOES_NOT_EXIST;
        }        
        if ($result['Pollingresource']['pollingresource_useridentification'] == $investorId) {
 //           echo __FILE__ . " " . __LINE__ ." Investor is not the owner, WIN_ACL_INVESTOR_IS_OWNER<br>";
            return WIN_ACL_INVESTOR_IS_OWNER;
        }
 //       echo __FILE__ . " " . __LINE__ ." Investor is not the owner, WIN_ACL_INVESTOR_IS_NOT_OWNER<br>";
        return WIN_ACL_INVESTOR_IS_NOT_OWNER;
    }


     
    
    
    
    
    
    
    
    
    
}
