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
 * @version
 * @date
 * @package
 */

App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('AppShell', 'Console');
App::uses('CollectDataClientShell', 'Console/Command');

/**
 * Description of GearmanClientExampleTest
 *
 * @author antoiba
 */
class CollectDataClientTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);
        
        $this->GearmanClient = $this->getMock('CollectDataClientShell', 
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
            );
        $this->GearmanClient->startup();
    }
    
    public function testFlow1() {
        $actual = $this->GearmanClient->initClient();
        $this->assertTrue($actual);
    }
    
    public function testCheckJobs() {
        $resultQueue = $this->GearmanClient->checkJobs(WIN_QUEUE_STATUS_START_COLLECTING_DATA, 1);
        foreach ($resultQueue as $result) {
            
            $result['Queue']['id'];
            $result['Queue']['queue_userReference']
                    ;
        }
    }
    
}
