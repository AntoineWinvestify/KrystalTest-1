<?php
/* 
 * Copyright (C) 2018 http://www.winvestify.com
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
/*
This class tests all the methods of the AppModel related to Api V1.
 * 
2018-05-08	  version 2018_0.1      
First version 
 * 
 *                  
 * 
Pending:

*/

App::uses('AppModel', 'Model');
class AppModelTest extends CakeTestCase {
    
    
    
    
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
        $this->AppModel = ClassRegistry::init('AppModel');
    }    

    
 
/*    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Thingy);
    }    
*/  
    

    public function testApiVariableNameInAdapter() {   
     
    $parms = [
        'investor_DNI' => '54286464F',
        'investor_date_of_birth' => '2003-12-05',
        'investor_address1' => 'Calle del Rio 23',
        'investor_address2' => 'Piso Bajo',
        'investor_city' => 'Madrid',
        'investor_country' => 'Spain'
        ];
        
    $parmsResult = [
        'investor_DNI' => '54286464F',
        'investor_dateOfBirth' => '2003-12-05',
        'investor_address1' => 'Calle del Rio 23',
        'investor_address2' => 'Piso Bajo',
        'investor_city' => 'Madrid',
        'investor_country' => 'Spain',
        ];  
      
        $this->AppModel->apiVariableNameInAdapter($parms);
        $this->assertArrayStructure($parms, $parmsResult, $msg = 'Regular format for Saving to DB NOT approved');  
    }       
    
    
     public function testApiVariableNameOutAdapter() {   
    $parms = [
        'investor_DNI' => '54286464F',
        'investor_date_of_birth' => '2003-12-05',
        'investor_address1' => 'Calle del Rio 23',
        'investor_address2' => 'Piso Bajo',
        'investor_city' => 'Madrid',
        'investor_country' => 'Spain'
        ];
        
    $parmsResult = [
        'investor_DNI' => '54286464F',
        'investor_dateOfBirth' => '2003-12-05',
        'investor_address1' => 'Calle del Rio 23',
        'investor_address2' => 'Piso Bajo',
        'investor_city' => 'Madrid',
        'investor_country' => 'Spain',
        ]; 
        $this->AppModel->apiVariableNameOutAdapter($parmsResult);
        $this->assertArrayStructure($parms, $parmsResult, $msg = 'Data from Database with incorrect keys');  
    }   
     
    
    public function testApiVariableNameOutAdapterForValidationErrorFormat() {   

    $validationError = [
        'investor_name' => [ 'Name validation error'],
        'investor_dateOfBirth' => ['You must be over 18 years old']
    ];    
    $validationErrorResult = [
        'investor_name' => [ 'Name validation error'],
        'investor_date_of_birth' => ['You must be over 18 years old']
    ]; 
        $this->AppModel->apiVariableNameOutAdapter($validationError);
        $this->assertArrayStructure($validationError, $validationErrorResult, $msg = 'ValidationError format NOT approved');  
    }


    public function testApiFieldListAdapter() {
        
        $myFieldList1 = ['investor_name', 'investor_date_of_birth', 'investor_surname'];
        $myFieldList1Result = ['investor_name', 'investor_dateOfBirth', 'investor_surname'];
        $this->AppModel->apiFieldListAdapter($myFieldList1);
        $this->assertArrayStructure($myFieldList1, $myFieldList1Result, $msg = 'Incorrect Definition of investor_dateOfBirth');  
 
        
        $myFieldList2 = ['Investor.investor_name', 'Investor.investor_date_of_birth', 'Investor.investor_surname'];      
        $myFieldList2Result = ['Investor.investor_name', 'Investor.investor_dateOfBirth', 'Investor.investor_surname']; 
        $this->AppModel->apiFieldListAdapter($myFieldList2);
        $this->assertArrayStructure($myFieldList2, $myFieldList2Result, $msg = 'Incorrect Definition of investor_dateOfBirth');        
        
        
        $myFieldList3 = ['investor_name', 'Investor.investor_date_of_birth', 'investor_surname'];         
        $myFieldList3Result = ['investor_name', 'Investor.investor_dateOfBirth', 'investor_surname'];  
        $this->AppModel->apiFieldListAdapter($myFieldList3);
        $this->assertArrayStructure($myFieldList3, $myFieldList3Result, $msg = 'Incorrect Definition of investor_dateOfBirth');      
        
        
        $myFieldList4 = ['Investor.investor_name', 'investor_date_of_birth', 'Investor.investor_surname', 'linkedaccount_currency_code'];
        $myFieldList4Result = ['Investor.investor_name', 'investor_dateOfBirth', 'Investor.investor_surname', 'linkedaccount_currencyCode'];
        $this->AppModel->apiFieldListAdapter($myFieldList4);
        $this->assertArrayStructure($myFieldList4, $myFieldList4Result, $msg = 'Incorrect Definition of linkedaccount_currencyCode');        
        
        
        $myFieldList5 = ['Investor.investor_name', 'investor_date_of_birth', 
                         'Investor.investor_address1', 'Linkedaccount.linkedaccount_currency_code'];
        $myFieldList5Result = ['Investor.investor_name', 'investor_dateOfBirth', 
                         'Investor.investor_address1', 'Linkedaccount.linkedaccount_currencyCode']; 
        $this->AppModel->apiFieldListAdapter($myFieldList5);
        $this->assertArrayStructure($myFieldList5, $myFieldList5Result, $msg = 'Incorrect Definition of linkedaccount_currencyCode');        
        
        
        $myFieldList6 = ['investor_date_of_birth'];         
        $myFieldList6Result = ['investor_dateOfBirth'];    
        $this->AppModel->apiFieldListAdapter($myFieldList6);
        $this->assertArrayStructure($myFieldList6, $myFieldList6Result, $msg = 'Incorrect Definition of investor_dateOfBirth');         
        
        
        $myFieldList7 = ['Investor.investor_date_of_birth']; 
        $myFieldList7Result = ['Investor.investor_dateOfBirth']; 
        $this->AppModel->apiFieldListAdapter($myFieldList7);
        $this->assertArrayStructure($myFieldList7, $myFieldList7Result, $msg = 'Incorrect Definition of investor_dateOfBirth');        
        
        
        $myFieldList8 = ['investor_name'];         
        $myFieldList8Result = ['investor_name']; 
        $this->AppModel->apiFieldListAdapter($myFieldList8);
        $this->assertArrayStructure($myFieldList8, $myFieldList8Result, $msg = 'Incorrect Definition of investor_name');         
        
        
        $myFieldList9 = ['Investor.investor_name'];
        $myFieldList9Result = ['Investor.investor_name'];
        $this->AppModel->apiFieldListAdapter($myFieldList9);
        $this->assertArrayStructure($myFieldList9, $myFieldList9Result, $msg = 'Incorrect Definition of investor_name');  
    }

    
}
