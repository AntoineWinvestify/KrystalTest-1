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
* @date 2016-10-23
* @package
*

2016-10-23	  version 2016_0.1



Pending:



*/


class Newsticker extends AppModel
{
	var $name= 'Newsticker';

/*	
	public $hasMany = array(
			'Pollanswer' => array(
				'className' => 'Pollanswer',
				'foreignKey' => 'poll_id',
				'fields' => '',
				'order' => '',
				),

			'Pollquestion' => array(
				'className' => 'Pollquestion',
				'foreignKey' => 'poll_id',
				'fields' => '',
				'order' => '',
				),
			);

*/





/**NOT TESTED
*
*	Store data in the newsticker item. The text can be simple html content, basically
*	only formatting tags are permitted
*	
*	@param 		char	$text		text to be displayed
*	@param 		integer	$category	means to differentiate the messages by colour?
*	
*	@return 	bool	true			data succesfully stored
*						false			Data could not be stored, no reason known
*
*/
public function addItem($text, $category) {

	$data = array('newsticker_text' 	=> $text,
				  'newsticker_category' =>	$category,				  
				  );

	if ($this->save($data, $validate = true) ) {
		
		
		
	}
	
	else {	// error while saving
			return false;
	}
	return true;
}







}
