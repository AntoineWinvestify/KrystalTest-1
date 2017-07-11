<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2014, http://beyond-language-skills.com                 |
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
//App::uses('Controller', 'Controller');
//App::uses('AppController', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 */

/*
		  App Controller
		  
2014-05-14		version 2014_0.1
Simple first version 






PENDING:
---
 * 
 * 
 * 
 * 
*/
 
class AdminAppController extends AppController {
  
    public $components = array('DebugKit.Toolbar',
				'RequestHandler',
				'Session',
				'Security',
				'Cookie',
	
				'Auth' => array('authorize' => 'Controller',
                                                'loginRedirect'	=> array(
									'plugin' => 'admin',
									'controller' 	=> 'ocrs',
									'action' 	=> 'ocr_winadmin_investor_checking'
									),
						'logoutRedirect' => array(
                                                                        'plugin' => 'admin',
                                                                        'controller' => 'users',
									 'action' => 'login'
										 ),
									),								
								);

/**
*	This code is common to all the classes that actively define a method for the beforeFilter
*	callback.
*	It includes:
*		name of cookie
*		determining the url of the last real external request
*		identify if mobile of desktop layout is to be used.
*/
public function beforeFilter() {

    $this->Cookie->name = 'equity';

    $this->Security->blackHoleCallback = 'blackhole';
 
/*	


*/
}





/**
*	Redirect an action to using https
*
*/

function blackHole()  {
//	$this->redirect('https://' . env('SERVER_NAME') . env('REQUEST_URI'));
}





/**
*	Redirect an action to using http
*
*/

function _notblackHole()  {
	$this->redirect('http://' . env('SERVER_NAME') . env('REQUEST_URI'));
}





/**
 *
 *	Normalize the values of an array to at least 2 characters, 6-> 06
 *
*/
public function norm_array_values(&$myArray, $key, $prefixdata) {
	if ($myArray < 10) {
		$myArray = str_pad($myArray, 2, "0", STR_PAD_LEFT);
	}
}





function deleteDir($dir) {
   $iterator = new RecursiveDirectoryIterator($dir);
   foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) 
   {
      if ($file->isDir()) {
         rmdir($file->getPathname());
      } else {
         unlink($file->getPathname());
      }
   }
   rmdir($dir);
}




}