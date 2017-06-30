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
class Search extends AppModel
{
	public $name = 'Search';
//        var $useTable = "investorglobaldata";
/*
	public $hasOne = array(	);


	var $hasMany = array(
		'Marketplace' => array(
			'className' => 'Marketplace',
			'foreignKey' => 'company_id',
		)
	);
*/



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
     * @param array $searchParms    array of all search parameters, key = parameter name, value = parameter value
     *
     * @return boolean  true if data has been stored
     * 
     */


public function writeSearchData($searchParms, $application = null) {

    $data = array();
    $data['company_id'] = $this->Auth->user('AdminPFP.company_id');   
    $data['search_searchparms'] = json_encode($searchParms);     
    $data['user_id'] = $this->Auth->user('AdminPFP.user_id');
    $data['search_application'] = $application;    
    
    if ($this->save($data, $validate = true)) {
        return true;
    }
    else  { 
        return false;
    }
    
}






}