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

/**
 * Description of ThingyTest
 *
 * @author antoiba
 */
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('ThingyShell', 'Console/Command');

class ThingyTest extends CakeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);
        
        $this->Thingy = $this->getMock('ThingyShell', 
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
            );
    }
    
    public function testGetFour()
    {
        $expected = "4";
        $actual = $this->Thingy->getFour();
        $this->assertEquals($expected, $actual);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }
}