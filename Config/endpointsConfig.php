<?php
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2019, http://www.winvestify.com                   	  	|
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
 * 
 * @author
 * @version 0.1
 * @date  2019-01-30
 * @package
 *
 *
 * 2019-01-30		version 0.1
 * Basic version
 */


/**
 * 
 * endpoints configuration which are contained in the JWT
 * This dashboard tell us the search function and the formatter function for the api 
 * 
 * This configuration tells us the search function and the formatting function according to the type (graph or list), the name (active or defaluted investments ...).
 * The structure of the configuration is strict.
 * In the first level is the type (graph or list), below this level is the name and then the functions to call. 
 * In the first, the name of the model must always be indexed and the name of the function as a value, 
 * in the second the name of the formatting class must be indexed and the name of the formatting function must be value
 * 
 * Example:
 * array (
 *      [TYPE] => //For now only graphics or lists, the type MUST be the same that the one from the url host/api/1.0/dashboards/linkedaccountId/TYPE/name/*
 *          array (
 *              [NAME] => //name of the graphics or lists, active-investments-list for example, this name MUST be the same that the one from the url host/api/1.0/dashboards/linkedaccountId/type/NAME/*
 *                  array => (
 *                      [MODEL] => "searchfunction"                 //The model name and the formatter function ALWAYS FIRST.
 *                      [FORMATTERCLASS] => "formatterfunction"     //The formatter class and function ALWAYS SECOND.   
 *                  )
 *          )        
 *      )
 * 
 * 
 */







/*  "endpoints": [
    {
      "href": "/pollingresources",
      "rel": "list",
      "method": "GET"
    },
    {
      "href": "/fileuploads",
      "rel": "list",
      "method": "POST"
    },
    {
      "href": "/notifications",
      "rel": "list",
      "method": "GET"
    },
    {
      "href": "/messages",
      "rel": "list",
      "method": "GET"
    }
  ]
}
Example:
    "endpoint_name" => array(
        "href" => "/pollingresources",
        "rel"  => "polling",
        "method" => "GET"
        ),
 * 
*/
$config['endpoints'] = [
            'Pollingresource' => [
                'href' => '/pollingresources',
                'rel'  => 'polling',
                'method' => 'GET'
            ],
            'Notification' => [
                'href' => '/notifications',
                'rel'  => 'polling',
                'method' => 'GET'
            ],  
            'Pubmessage' => [
                'href' => '/pubmessages',
                'rel'  => 'publicity',
                'method' => 'GET'
            ],
            'Message' => [
                'href' => '/messages',
                'rel'  => 'privacy',
                'method' => 'GET'
            ],  
            'Fileupload' => [ [
                'href' => '/fileuploads',
                'rel'  => 'polling',
                'method' => 'GET'
                ],
                [
                'href' => '/fileuploads',
                'rel'  => 'polling',
                'method' => 'POST'
                ],
            ],
        ];


              