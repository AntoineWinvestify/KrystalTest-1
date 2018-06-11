<?php

/* 
 * Copyright (C) 2018 antoine
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
App::uses('Accountowner', 'Model');
class AccountownerTest extends CakeTestCase {

    public $fixtures = array('app.Accountowner');

    public function setUp() {
        parent::setUp();
        $this->Accountowner = ClassRegistry::init('Accountowner');
    }

    

    
    public function testPublished() {
        $result = $this->Article->published(array('id', 'title'));
        $expected = array(
            array('Article' => array('id' => 1, 'title' => 'First Article')),
            array('Article' => array('id' => 2, 'title' => 'Second Article')),
            array('Article' => array('id' => 3, 'title' => 'Third Article'))
        );

        $this->assertEquals($expected, $result);
    }   
 
/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }    
*/  
    
    

    public function testDeleteAccountOwner($accountOwnerId) {

    }
    
    
    public function testCreateAccountOwner($companyId, $investorId, $username, $password) {

    }
    
       
    public function testAccountAdded ($accountownerId) {


    }
    
    
    public function testAccountDeleted($accountownerId) {
 
 
    }     
    
 
    public function testChangeAccountPassword($accountownerId, $newPass){

    }


    
    
    
    
    
    
    
    
    
    
}