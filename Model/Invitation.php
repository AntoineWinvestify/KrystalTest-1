<?php
/**
// @(#) $Id$
// +-----------------------------------------------------------------------+
// | Copyright (C) 2016, http://beyond-language-skills.com                 |
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

*/


class Invitation extends AppModel
{
	var $name= 'Invitation';




/**
*	Apparently can contain any type field which is used in a field. It does NOT necessarily
*	have to map to a existing field in the database. Very useful for automatic checks
*	provided by framework
*/
var $validate = array(
		'invitation_name' => array(
			'ruleName1_minLength' => array(
				'rule'		=> array('notEmpty'),
				'message' 	=> 'Su nombre es obligatorio',
				),
			),
		
		'invitation_surnames' => array(
			'ruleName1_minLength' => array(
				'rule' 		=> array('notEmpty'),
				'message' 	=> 'El/Los apellido(s) es/son obligatorio(s)',
				),
			),

		'invitation_message' => array(
			'ruleName1_minLength' => array(
				'rule' 		=> array('notEmpty'),
				'message' 	=> 'El mensaje es obligatorio',
				),
			),
			
		'email' => array(
			'ruleEmail' => array(			
				'rule' 		=>  array('email', true),
				'message'	=> 'Email no válido',
				),
			'ruleUniqueEmailAddress' => array(			
				'rule' 		=>  array('isUnique', true),
				'message' 	=> 'Email no válido',
				),			
		),				
	);





/**
*
* rules are defined what should happen when a database record is created or updated
*
*
*/
function afterSave ($created, $options = array()) {
	if ($created) {		// TRUE, when a *new* database record is created
		$event = new CakeEvent('growthHackingInviteNewPerson', $this, array(
																	'id' 		=> $this->id,
																	'invitation' 	=> $this->data[$this->alias],
																	));
		$this->getEventManager()->dispatch($event);
	}
	return true;
}


}
