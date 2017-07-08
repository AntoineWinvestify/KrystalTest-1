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






}