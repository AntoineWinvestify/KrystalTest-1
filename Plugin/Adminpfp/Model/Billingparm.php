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
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//


 The model that stores all the searches for Tallyman data

2016-06-29	  version 0.1
First version




*/

App::uses('AppModel', 'Model');
class Billingparm extends AppModel
{
	public $name = 'Billingparm';


/**
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();





    /**
     * 
     * Store the data needed for billing purposes
     * 	
     * @param array $data    array with charging data/indicators to be stored
     *
     * @return boolean  true if data has been stored
     * 
     */
public function writeChargingData($chargingData, $application = null) {

    $data = array();
    $data['billingparm_reference'] = $chargingData['reference'];   
    $data['billingparm_parm1'] = $chargingData['parm1'];     
    $data['billingparm_parm2'] = $chargingData['parm2']; 
    $data['billingparm_parm3'] = $chargingData['parm3'];   
    $data['billingparm_serviceName'] = $application;    
     
    if ($this->save($data, $validate = true)) {
        return true;
    }
    else  { 
        return false;
    }
}


   /** 
     *
     *  Checks if Tallyman event is to be charged, i.e. if charging data must be stored in database
     * 
     *  @param      $reference  parameter to be checked
     *  @param      string      transparent parameter 2 to be checked
     *  @param      string      transparent parameter 3 to be checked
     *  @param      string      transparent parameter 4 to be checked
     *  @param      string      name of application
     *
     *  @return     boolean	true	Event needs to be charged
     * 				false	Event need not to be charged
     * 						
     */
    public function isChargeableEvent($reference, $parameter1, $parameter2, $parameter3, $application) {

//  Calculate cutoff date for billing purposes
        Configure::load('p2pGestor.php', 'default');
        $validBeforeExpiration = Configure::read('CollectNewInvestmentData');
        $cutoffTime = date("Y-m-d H:i:s", time() - $validBeforeExpiration * 3600 * 7 * 24);

        $result = $this->find('first', array(
            "fields" => array("created"),
            "order" => "id DESC",
            "recursive" => -1,
            "conditions" => array("billingparm_reference" => $reference,
                "billingparm_parm2" => $parameter2,
                "billingparm_serviceName" => $application),
        ));

        if (empty($result)) {  // No information found, so this is a chargeable event
            return true;
        }

        if ($result['Billingparm']['created'] > $cutoffTime) {          // This request should NOT be counted as a new chargeable request
            return false;
        }
        return true;
    }



}