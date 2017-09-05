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

class GearmanClientShell extends AppShell {
    protected $GearmanClient;

    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {
        $this->GearmanClient->addServers('127.0.0.1');
        // simple array of parameters to serve as the test workload
        $params = array(
            'foo' => 'bar',
            'now' => time()
        );
        $this->GearmanClient->setFailCallback("fail_change");
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

}