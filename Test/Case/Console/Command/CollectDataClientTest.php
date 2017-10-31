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
    
    
    public function testCheckJobs() {
        $resultQueue = $this->GearmanClient->checkJobs(WIN_QUEUE_STATUS_START_COLLECTING_DATA, 1);
        foreach ($resultQueue as $result) {
            $this->assertArrayHasKey('id', $result['Queue']);
            $this->assertArrayHasKey('queue_userReference', $result['Queue']);
            $this->assertArrayHasKey('queue_info', $result['Queue']);
        }
    }
    
    public function testFlow1() {
        $this->Queue = ClassRegistry::init('Queue');
        $expected = $this->GearmanClient->initClient();
        $this->assertEquals(3, $expected['Queue']['queue_status']);
        return $expected;
    }
    
    /**
     * @depends testFlow1
     */
    public function testLinkAccountsAreCorrect(array $result_array) {
        $result = json_decode($result_array['Queue']['queue_info'], true);
        $queueId = $result_array['Queue']['id'];
        $companiesInFlow = $result['companiesInFlow'];
        $userLinkaccountsId = $this->GearmanClient->getUserLinkaccountIds();
        foreach ($userLinkaccountsId[$queueId] as $key => $linkaccount) {
            $this->assertEquals($linkaccount, $companiesInFlow[$key]);
        }
    }
    
    /*public function testFilesAreCorrect(array $result_array) {
        
    }*/
    
    
}
