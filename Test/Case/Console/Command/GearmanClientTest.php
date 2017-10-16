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
App::uses('GearmanClientShell', 'Console/Command');

/**
 * Description of GearmanClientExampleTest
 *
 * @author antoiba
 */
class GearmanClientTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);
        
        $this->GearmanClientExample = $this->getMock('GearmanClientShell', 
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
            );
        $this->GearmanClientExample->startup();
    }
    
    public function testGearmanConnection() {
        $expected = strrev("Hello World!");
        $actual = $this->GearmanClientExample->gearmanConnection();
        $this->assertEquals($expected, $actual);
    }
    
    public function testTypeError() {
        
    }
    
    public function tearDown() {
        parent::tearDown();
        unset($this->GearmanClientExample);
    }
    
}
