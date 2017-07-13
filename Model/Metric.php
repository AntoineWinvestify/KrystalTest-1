<?php
/**
// @(#) $Id$
// +-----------------------------------------------------------------------+
// | Copyright (C) 2009, http://yoursite                                   |
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
* @author Antoine de Poorter
* @version 0.1
* @date 2016-11-01
* @package
*

2016-11-01	  version 2016_0.1






Pending:



*/


class Metric extends AppModel
{
	var $name= 'Metric';

/*
	public $hasOne = array(
			'Loanrequest' => array(
				'className' => 'Loanrequest',
				'foreignKey' => 'user_id',
				'fields' => '',
				'order' => '',
				),

			'Loan' => array(
				'className' => 'Loan',
				'foreignKey' => 'user_id',
				'fields' => '',
				'order' => '',
				),

			'Wallet' => array(
				'className' => 'Wallet',
				'foreignKey' => 'user_id',
				'fields' => '',
				'order' => '',
				),

			'Operation' => array(
				'className' => 'Operation',
				'foreignKey' => 'user_id',
				'fields' => '',
				'order' => '',
				),

			);


	var $belongsTo = array(
		'Marketplace' => array(
			'className' => 'Marketplace',
			'foreignKey' => 'marketplace_id',
		)
	);
*/


/* The following is for the filesharing function */
	//The Associations below have been created with all possible keys, those that are not needed can be removed
/*
	var $hasMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


	var $hasAndBelongsToMany = array(
		'SharedUpload' => array(
			'className' => 'Upload',
			'joinTable' => 'uploads_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'upload_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
*/



/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
/*
var $validate = array(
		'username' => array(
			'required' => array(
			'rule' => array('notEmpty'),
			'message' => 'A username is required',
				),
			),
		'password' => array(
			'required' => array(
			'rule' => array('notEmpty'),
			'message' => 'A password is required'),


		),
		
);
*/






}