<?php
/*
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
//
 * 
 * Application Controller for Adminpfp plugin
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
	  Adminpfp AppController
		  
2017-06-14		version 0.1
Simple first version 







*/
 

// Errors that can be detected in this function
    define('USER_DOES_NOT_EXIST', 1);           
    define('NO_DATA_AVAILABLE', 2);  
    define('NOT_ENOUGH_PARAMETERS', 3);
    
    define('UPWARDS', 1);
    define('DOWNWARDS', 2);   
    
    
class AdminpfpAppController extends AppController {
 
    public $components = array('DebugKit.Toolbar',
				'RequestHandler',
				'Session',
				'Security',
				'Cookie',
	
				'Auth' => array('authorize' => 'Controller',
                                                'loginRedirect'	=> array(
									'plugin' => 'adminpfp',
									'controller' 	=> 'users',
								//	'action' 	=> 'showTallyman'
                                                                        'action' 	=> 'readtallymandata'
									),
						'logoutRedirect' => array('controller' 	=> 'users',
									 'action' => 'login'
										 ),
									),								
								);
		
    var $uses = array('Administrators');	


/**
*	This code is common to all the classes that actively define a method for the beforeFilter
*	callback.
*	
*		
*		
*/
public function beforeFilter() {
   parent::beforeFilter();
    $this->Cookie->name = 'Winvestify_pfpadmin';


}




}