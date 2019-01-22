<?php
/**
  // +-----------------------------------------------------------------------+
  // | Copyright (C) 2009, http://www.winvestify.com                         |
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

App::uses('CakeEvent', 'Event');
App::uses("AppModel", "Model");
class Usertoken extends AppModel {

    var $name = 'UserToken';
    var $useTable = "usertokens";
    
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' =>  'user_id'
        ) 
    );


    
    /**
     * Deletes a refreshToken of a user. This is normally the result of a
     * logout action of the user.
     * 
     * @param array $refreshToken the object who contains the $refreshToken
     * @return integer Number of Objects deleted
     */   
    public function api_deleteUsertoken($refreshToken) { 

        $result = $this->find('all', $params = ['conditions' => ['usertoken_refreshToken' => $refreshToken],
                                               'recursive' => -1
                                       ]);

        $i = 0;
        foreach ($result as $token) {
            if ($token['Usertoken']['usertoken_refreshToken'] === $refreshToken) {
                $this->delete($token['Usertoken']['id']);
                $i++;
            }  
        }
        return $i;    
    }
    
    
    /**
     * Get a refresh token. This is part of the 'login sequence' of a user 
     * 
     * @param integer $userId
     * @param array $refreshToken An array of 1 or more tokens to be deleted
     * @return mixed refreshToken or false
     */   
    public function api_addUserToken($userId) {
       
        $data['usertoken_refreshToken'] = $this->random_str(100);
        $data['usertoken_accessTokenRenewalCounter'] = 1;
        $data['user_id'] = $userId;
        
        if ($this->save($data)) {
            return $data['usertoken_refreshToken'];
        }
        return false;
    }

    
    /**
     * Generate a new access token for the user. This is typically used to "extend" 
     * the current "user session"
     * 
     * @param array $refreshToken An array of 1 or more tokens to be deleted
     * @return mixed false or new refreshToken
     */   
    public function api_getNewAccessUserToken($refreshToken) {
        
        $result = $this->find('first', $params = ['conditions' => ['usertoken_refreshToken' => $refreshToken],
                                               'recursive' => -1
                                       ]);
     
        if (empty($result)) {
            return false;
        }

        $this->id = $result['Usertoken']['id'];
        $newToken = $this->random_str(100);
        $this->save(['id' => $result['Usertoken']['id'], 
                     'usertoken_refreshToken' => $newToken, 
                     'usertoken_accessTokenRenewalCounter' => $result['Usertoken']['usertoken_accessTokenRenewalCounter'] + 1]);
        return $newToken;       
    }

 
}