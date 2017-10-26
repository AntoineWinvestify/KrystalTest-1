<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, https://www.winvestify.com                        |
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
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-10-18
* @package
*

Hold "total" values of many of the concepts that are applicable to a loan

2017-10-18		version 0.1
initial version





Pending:







*/


class Paymenttotal extends AppModel
{
	var $name= 'Paymenttotal';
/*
	var $hasOne = array(
		'Investment' => array(
			'className' => 'Investment',
			'foreignKey' => '_id',
		)
	);
*/


        
        
        
    /*
     * 
     * Update the corresponding fields in the paymenttotal table 
     * 
     */
    function afterSave1($created, $options = array()) {  

        print_r($this->data['Paymenttotal']);
    }       
        
        

}