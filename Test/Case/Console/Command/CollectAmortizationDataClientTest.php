<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
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
 * @author 
 * @version 0.1
 * @date 2017-11-02
 * @package
 */

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('AppShell', 'Console');
App::uses('CollectAmortizationDataClientShell', 'Console/Command');

/**
 * Description of CollectAmortizationDataClientTest
 *
 * @author antoiba
 */
class CollectAmortizationDataClientTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);
        
        $this->GearmanClient = $this->getMock('CollectAmortizationDataClientShell', 
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
            );
        $this->GearmanClient->startup();
        
        $pathVendor = Configure::read('winvestifyVendor');
        include_once ($pathVendor . 'Classes' . DS . 'fileparser.php');
        $this->myParser = new Fileparser();
    }
    
    public function testFlow3A() {
        $this->Queue = ClassRegistry::init('Queue');
        $expected = $this->GearmanClient->initClient();
        $this->assertEquals(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, $expected['Queue']['queue_status']);
        $data['expected'] = $expected;
        $data['userLinkaccountIds'] = $this->GearmanClient->getUserLinkaccountIds();
        $data['userReference'] = $this->GearmanClient->getUserReference();
        return $data;
    }
    
}
