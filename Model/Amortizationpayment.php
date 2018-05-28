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
* @date 2018-03-25
* @package
*


2018-03-25		version 0.1
initial version

*/



/**
 * Description of Amortizationpayment
 *
 */
class Amortizationpayment extends AppModel {
    
    var $name = 'Amortizationpayment';

    
    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );

    public $belongsTo = array(
        'Amortizationtable' => array(
            'className' => 'Amortizationtable',
            'foreignKey' =>  'amortizationtable_id'
        ),
        'Globalamortizationtable' => array(
            'className' => 'Globalamortizationtable',
            'foreignKey' =>  'globalamortizationtable_id'
        )
    );    
  
}
