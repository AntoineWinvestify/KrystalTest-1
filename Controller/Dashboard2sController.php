<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2017, https://www.winvestify.com                        |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
*
*
* @author
* @version 0.1
* @date 2017-10-10
* @package
*

2017-10-10 	  version 0.1





Pending:

*/


App::uses('CakeTime', 'Utility');
App::uses('CakeEvent', 'Event');

class Dashboard2sController extends AppController
    {
        var $name = 'Dashboard2s';
        var $helpers = array('Html', 'Js');
        var $uses = array();




        function beforeFilter() {

            parent::beforeFilter();


    }





    /**
     *[AJAX call]
     *	Read the data of all active investments that belong to a linked account
     *
     */
    function getDashboard2SinglePfpData()  {

    }




    /**
     *
     * Read all the data related to all the investments of an investor. 
     * $userReference is read from the session
     */
    function getDashboard2GlobalData() {

    }

}