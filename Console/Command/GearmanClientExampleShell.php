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

App::uses('AppShell', 'Console/Command');

class GearmanClientExampleShell extends AppShell {
    protected $GearmanClient;

    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {
        $this->GearmanClient->addServers();
        $this->GearmanClient->setFailCallback($this, "verifyFailCollectingData");
    }
    
    public function gearmanConnection() {
        $this->GearmanClient->addServers('127.0.0.1');
        return $this->GearmanClient->doNormal("reverse", "Hello World!");
    }
    
    public function example() {
        $this->GearmanClient->addServers('127.0.0.1');
        // simple array of parameters to serve as the test workload
        $params = array(
            'foo' => 'bar',
            'now' => time()
        );
        $this->GearmanClient->setFailCallback(array($this, 'fail_change'));
        $this->GearmanClient->setExceptionCallback();
        /* Callbacks that can be created
          $client->setCreatedCallback("create_change");

          $client->setDataCallback("data_change");

          $client->setStatusCallback("status_change");

          $client->setCompleteCallback("complete_change");

          $client->setFailCallback("fail_change"); */

        // synchronous (resp = A string representing the results of running a task.)
        /* $resp = $this->GearmanClient->doNormal("json_test", json_encode($params));
          if ($this->GearmanClient->returnCode() != GEARMAN_SUCCESS){
          $this->out("Bad return code!");
          return;
          }
          // do something with the response
          $this->out($resp);
          return; */
        // OR
        $resp = [];
        // asynchronous (resp = The job handle for the submitted task.)
        for ($i = 0; $i < 100; $i++) {
            $params["num"] = $i;
            $resp[] = $this->GearmanClient->addTask("json_test", json_encode($params));
        }
        $start = microtime(true);
        $this->GearmanClient->runTasks();
        $totaltime = number_format(microtime(true) - $start, 2);
        echo "Got user info in: $totaltime seconds:\n";
        /* $resp = $this->GearmanClient->doBackground("json_test", json_encode($params));
          if ($this->GearmanClient->returnCode() != GEARMAN_SUCCESS){
          $this->out("Bad return code!");
          return;
          }
          $this->out('Background job handle: '.$resp);
          // using the job handle, you can monitor the status of a job
          $status = $this->GearmanClient->jobStatus($resp);
          // the job status array contains: "job known", "job running", numerator, denominator
          $this->out('Known: '.$status[0]);
          $this->out('Running: '.$status[1]);
          $this->out('Progress: '.$status[2].'/'.$status[3]); */
        return;
    }

    public function reverseFN() {
        $this->GearmanClient->addServers('127.0.0.1');
        print $this->GearmanClient->doNormal("reverse", "Hello World!");
    }

    function fail_change($task) {
        echo "DATA: " . $task->data() . "\n";
    }
    
    /**
     * This function will make a Gearman Worker Explode BOOOM
     */
    public function try_exceptions() {
        $this->GearmanClient->addServers('127.0.0.1');
        $this->status_string = "cu";
        $this->status_string2 = "cu";
        $this->GearmanClient->setExceptionCallback(function(GearmanTask $task) {
            $m = $task->data();
            echo "ID Unique: " . $task->unique() . "\n";
            echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
            //return GEARMAN_WORK_EXCEPTION;
        });
        
        $this->GearmanClient->setFailCallback(function(GearmanTask $task) {
            $m = $task->data();
            echo "ID Unique: " . $task->unique() . "\n";
            echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
            //echo GEARMAN_WORK_FAIL;
        });
        
        $this->GearmanClient->setCompleteCallback(function(GearmanTask $task) {
            echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
            echo GEARMAN_SUCCESS;
        });
        
        $this->GearmanClient->addTask('testFail', 'workload', null, '123');
        $this->GearmanClient->addTask('testException', 'workload', null, '234');
        $this->GearmanClient->addTask('testFail', 'workload',null, '12345');
        $this->GearmanClient->runTasks();
        //echo "This is an error fail \n";
        //echo $this->status_string;
        //echo "This is an error exception \n";
        //echo $this->status_string2;
        //echo $this->GearmanClient->returnCode();
        /*if ($this->GearmanClient->returnCode() != GEARMAN_SUCCESS){
          $this->out("Bad return code!");
          return;
          }
        echo "\n";*/
        
        $this->GearmanClient->addTask('testException', 'workload', null, '22222');
        $this->GearmanClient->runTasks();
        //echo "This is an error exception \n";
        //echo $this->status_string2;
        //echo $this->GearmanClient->returnCode();
        echo "\n";
        
        /*$this->GearmanClient->addTask('test', 'workload');
        $this->GearmanClient->runTasks();
        echo "This is an error";
        var_dump($this->GearmanClient->returnCode());*/
        
        /*$this->GearmanClient->addTask('test', 'workload');
        $this->GearmanClient->runTasks();
        echo "This is an error";
        echo $this->GearmanClient->returnCode();*/ 
        
    }
    
    function complete_change($task) {
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
    }

}