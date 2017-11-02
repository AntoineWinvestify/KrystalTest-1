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
    
    /**
     * @depends testFlow1
     */
    public function testLinkAccountsAreCorrect(array $data) {
        $result = json_decode($data['expected']['Queue']['queue_info'], true);
        $queueId = $data['expected']['Queue']['id'];
        $companiesInFlow = $result['companiesInFlow'];
        $userLinkaccountsId = $data['userLinkaccountIds'];
        $linkaccounts = $userLinkaccountsId[$queueId];
        foreach ($linkaccounts as $key => $linkaccount) {
            $this->assertEquals($linkaccount, $companiesInFlow[$key]);
        }
    }
    
    /**
     * @depends testFlow1
     */
    public function testFileAmortizationIsCorrect(array $data) {
        $queueId = $data['expected']['Queue']['id'];
        $date = date("Ymd", strtotime(date("Ymd")-1));
        $directoryParent = Configure::read('dashboard2Files') . $data['userReference'][$queueId] . DS . $date . DS ;
        echo "directory " . $directoryParent;
        $userLinkaccountsId = $data['userLinkaccountIds'];
        $linkaccounts = $userLinkaccountsId[$queueId];
        foreach ($linkaccounts as $linkaccount) {
            $directory = $directoryParent . $linkaccount . DS;
            echo "directory2 ". $directory;
            $dir = new Folder($directory);
            $allTransactionFiles = $dir->findRecursive("amortizationtables.*");
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": allFiles ";
                print_r($allTransactionFiles);
            }
            $tempPfpName = explode("/", $allTransactionFiles[0]);
            $pfp = $tempPfpName[count($tempPfpName) - 2];
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": company ";
                print_r($pfp);
            }
            $companyClass = $this->GearmanClient->companyClass($pfp);
            $valuesTestHeaderTransaction = $companyClass->getValuesTestHeaderTransaction();
            $configParam = $companyClass->getConfigParamTestTransaction();
            foreach ($allTransactionFiles as $file) {
                $data = $this->myParser->getFirstRow($file, $configParam);
                $this->assertEquals(count($valuesTestHeaderTransaction), count($data));
                foreach ($data as $excelKey => $value) {
                    $this->assertEquals($value, $valuesTestHeaderTransaction[$excelKey]);
                }
            }
        }
    }
    
}
