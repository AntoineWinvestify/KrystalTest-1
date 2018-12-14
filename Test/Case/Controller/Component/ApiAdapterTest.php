<?php
/* 
 * Copyright (C) 2018 http://www.winvestify.com
 *
 * This program is free software => you can redistribute it and/or modify
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
/*
This class tests all the methods of the Model Accountowner
 * 
2018-05-08	  version 2018_0.1      
First version 
 * 
 *                  
 * 
Pending:

*/


App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('ApiAdapterComponent', 'Controller/Component');

// A fake controller to test against
class ApiAdapterControllerTest extends Controller {
    
}

class ApiAdapterTest extends CakeTestCase {
    public $ApiAdapterComponent = null;
    public $Controller = null;
    
    var $incomingArray = [
            "service_status" => 'ACTIVE',
            "data" => [
                    [
                    "id" => 325938,
                    "service_status" => "NOT_ACTIVE",
                    "linkedaccount_status" => "ACTIVE",
                    "linkedaccount_visual_state" => "ANALYZING",
                    "polling_type" => "NOTIFICATION_CHECK",
                    "links" => 
                        [
                        "metadata_type_of_document" => "DNI_FRONT",
                        "linkedaccount_status" => "NON_EXISTENT_VALUE"
                        ]

                    ],
                    [
                    "id" => 432456,
                    "metadata_type_of_document" => "DNI_BACK",
                    "service_status" => "SUSPENDED",
                    "polling_type" => "LINKEDACCOUNT_CHECK",
                    "linkedaccount_status" => "NOT_ACTIVE",
                    "linkedaccount_visual_state" => "QUEUED",
                    "linkedaccount_username" => "antoine@gmail.com"
                    ],
                    [
                    "id" => 432458,
                    "metadata_type_of_document" => "BANK_CERTIFICATE",
                    "polling_type" => "PMESSAGE_CHECK",
                    "linkedaccount_status" => "UNDEFINED",
                    "linkedaccount_visual_state" => "MONITORED"
                    ]
                ]
              ];
    

    var $outgoingArray = [
            "service_status" => 20,
              "data" => [
                [
                "id" => 325938,
                "service_status" => 10,
                "linkedaccount_status" => 1,
                "linkedaccount_visual_state" => 20,
                "polling_type" => 10,
                "links" => [
                    "metadata_type_of_document" => 10,
                    "linkedaccount_status" => "NON_EXISTENT_VALUE"
                    ]
                ],
                [
                "id" => 432456,
                "metadata_type_of_document" => 20,
                "service_status" => 30,
                "polling_type" => 20,
                "linkedaccount_status" => 2,
                "linkedaccount_visual_state" => 10,
                "linkedaccount_username" => "antoine@gmail.com"
                ],
                [
                "id" => 432458,
                "metadata_type_of_document" => 30,
                "polling_type" => 30,
                "linkedaccount_status" => 0,
                "linkedaccount_visual_state" => 30
                ]
            ]
            ];    
    
    /**
     * Assert Array structures are the same
     *
     * @param array       $expected Expected Array
     * @param array       $actual   Actual Array
     * @param string|null $msg      Message to output on failure
     *
     * @return bool
     */
    public function assertArrayStructure($actual, $expected, $msg = '') {
        ksort($expected);
        ksort($actual);
        $this->assertSame($actual, $expected, $msg);
    }
    
    
    public function setUp() {
        parent::setUp();
        // Setup our component and fake test controller
        $Collection = new ComponentCollection();
        $this->ApiAdapterComponent = new ApiAdapterComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new ApiAdapterControllerTest($CakeRequest, $CakeResponse);
        $this->ApiAdapterComponent->startup($this->Controller);
    }

    
    public function testNormalizeIncomingJson() {
 
        $this->ApiAdapterComponent->normalizeIncomingJson($this->incomingArray);
        $this->assertArrayStructure($this->incomingArray, $this->outgoingArray);        
    } 
    
    
    
    public function testNormalizeOutgoingJson() {

        $this->ApiAdapterComponent->normalizeOutgoingJson($this->outgoingArray);
   //     $this->assertArrayStructure($this->outGoingArray, $this->Controller->ApiAdapter->normalizeOutgoingJson($this->outGoingArray));
    }
    
    
    public function tearDown() {
        parent::tearDown();
        // Clean up after we're done
        unset($this->ApiAdapterComponent);
        unset($this->Controller);       
    }
   
}
