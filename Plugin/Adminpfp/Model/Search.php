<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, http://www.winvestify.com                         |
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


 The model that stores all the searches does by the adminpfp's for Tallyman data

2017-06-29	  version 0.1
First version




*/

App::uses('AppModel', 'Model');
class Search extends AppModel
{
	public $name = 'Search';



/**
*	Apparently it can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array();





    /**
     * 
     * Store the data related to user searches
     * 	
     * @param json  $searchParms    all search parameters, key = parameter name, value = parameter value
     * @param string $parameter1    parameter1, transparent data
     * @param string $parameter2    parameter2, transparent data
     * @param string $parameter3    parameter3, transparent data
     * @param int   $application    identification of application 
     * @return boolean  true if data has been stored
     * 
     */
public function writeSearchData($searchParms, $parameter1, $parameter2, $parameter3, $application = null) {

    $data = array();
    
    $data['search_searchparms'] = $searchParms;
    $data['search_parameter1'] = $parameter1;     
    $data['search_parameter2'] = $parameter2;
    $data['search_parameter3'] = $parameter3;   
    $data['search_application'] = $application;    
 
    if ($this->save($data, $validate = true)) {
        return true;
    }
    else  { 
        return false;
    }
}


}