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

/*
 *	var $belongsTo = array(
		'Poll' => array(
			'className' => 'Poll',
			'foreignKey' => 'poll_id',
		)
	);
*/


        
        
    /*
     * **** CALLBACK FUNCTIONS *****
     */

    /**
     *  Rules are defined for what should happen after a database record has been read
     * 	@param $results
     *  @param $primary
     */
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['Investor']['investor_dateOfBirth'])) {
                $results[$key]['Investor']['investor_dateOfBirth'] = $this->formatDateAfterFind(
                        $val['Investor']['investor_dateOfBirth']);
            }
        }
        return $results;

        foreach ($results as $key => $val) {
            if (isset($val['Ocr']['investor_iban'])) {
                $results[$key]['Ocr']['investor_iban'] = $this->decryptDataAfterFind(
                        $val['Ocr']['investor_iban']);
            }
               
        }
        return $results;
    }

    /**
     * 	Rules are defined for what should happen before a database record is created or updated.
     * 
     * 	@param $options
     */ 
    function beforeSave($options = array()) {

        // Store telephone number without spaces
        if (!empty($this->data['Investor']['investor_dateOfBirth'])) {
            $this->data['Investor']['investor_dateOfBirth'] = $this->formatDateBeforeSave($this->data['Investor']['investor_dateOfBirth']);
        }
        if (!empty($this->data['Investor']['investor_telephone'])) {
            $this->data['Investor']['investor_telephone'] = str_replace(' ', '', $this->data['Investor']['investor_telephone']);
        } 
       
        // Observe that username is not saved to the Investor model, but only required for generating the "investor_identity"
        if (!$this->id && !isset($this->data[$this->alias][$this->primaryKey])) {
            $this->data['Investor']['investor_identity'] = $this->createInvestorReference($this->data['Investor']['investor_telephone'], $this->data['Investor']['username']); 
        }    
    }

 


    /**
     * 	Rules are defined for what should happen after a database record is created or updated.
     * 
     * 	@param $created
     *  @param $options
     */
    function afterSave($created, $options = array()) {
 /*       if (!empty($this->data['Investor']['investor_tempCode'])) {    // A confirmation code has been generated
            $event = new CakeEvent('confirmationCodeGenerated', $this, array('id' => $this->id,
                'investor' => $this->data[$this->alias],
            ));
            $this->getEventManager()->dispatch($event);
        }

        if (!empty($this->data['Investor']['investor_accountStatus'])) {  // A user has succesfully and completely registered
            if (($this->data['Investor']['investor_accountStatus'] & QUESTIONAIRE_FILLED_OUT) == QUESTIONAIRE_FILLED_OUT) {
                $event = new CakeEvent('newUserCreated', $this, array('id' => $this->id,
                    'investor' => $this->data[$this->alias],
                ));
                $this->getEventManager()->dispatch($event);
            }
        }
        */

        // Identify that the WebClient should request a new access token with the updated information
        if (!$created) {
            
            if (!empty($this->data['Investor']['investor_language']) ||
                    !empty($this->data['Investor']['investor_name']) ||
                    !empty($this->data['Investor']['investor_surname']
                    )) {           
                $this->data['Investor']['requireNewAccessToken'] = true;
            }
        }
    }

    
    
}
