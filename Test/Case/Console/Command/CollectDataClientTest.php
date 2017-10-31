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

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
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
        
        $pathVendor = Configure::read('winvestifyVendor');
        include_once ($pathVendor . 'Classes' . DS . 'fileparser.php');
        $this->myParser = new Fileparser(); 
        
        
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
    public function testFileTransactionIsCorrect(array $data) {
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
            $allTransactionFiles = $dir->findRecursive("transaction.*");
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
    
    /**
     * @depends testFlow1
     */
    public function testFileInvestmentIsCorrect(array $data) {
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
            $allInvestmentFiles = $dir->findRecursive("investment.*");
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": allFiles ";
                print_r($allInvestmentFiles);
            }
            $tempPfpName = explode("/", $allInvestmentFiles[0]);
            $pfp = $tempPfpName[count($tempPfpName) - 2];
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": company ";
                print_r($pfp);
            }
            $companyClass = $this->GearmanClient->companyClass($pfp);
            $valuesTestHeaderInvestment = $companyClass->getValuesTestHeaderInvestment();
            $configParam = $companyClass->getConfigParamTestInvestment();
            foreach ($allInvestmentFiles as $file) {
                $data = $this->myParser->getFirstRow($file, $configParam);
                $this->assertEquals(count($valuesTestHeaderInvestment), count($data));
                foreach ($data as $excelKey => $value) {
                    $this->assertEquals($value, $valuesTestHeaderInvestment[$excelKey]);
                }
            }
        }
    }
    
    
}
