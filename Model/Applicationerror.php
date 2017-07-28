<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, http://yoursite                                   |
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
* @date 2017-07-28
* @package
*


2017-07-28		version 0.1


Saves data related to application errors
 
 



Pending:







*/

class Applicationerror extends AppModel
{
	var $name= 'Applicationerror';

/*
	var $hasOne = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'marketplace_id',
		)
	);
*/



/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
//var $validate = array(
// );







    /**
     *
     *  Saves all the data related to an application error in a centralized database
     * 	
     * @param string $par1
     * @param string $par2
     * @param string $par3
     * @param string $par4
     * @param string $par5
     * 
     * @return boolean
     * 
     */
public function saveAppError($par1, $par2, $par3, $par4, $par5) {
    $dataArray['applicationerror_typeOfError'] = $par1;
    $dataArray['applicationerror_detailedErrorInformation'] = $par2;
    $dataArray['applicationerror_line'] = $par3;
    $dataArray['applicationerror_file'] = $par4;
    $dataArray['applicationerror_urlsequenceUrl'] = $par5;
    if ($this->save($dataArray, $validate = true)) {
        return true ;       
    }
    return false;
}




}