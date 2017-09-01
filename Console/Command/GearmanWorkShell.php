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

class GearmanWorkShell extends AppShell {
    protected $GearmanWorker;
    
    var $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    public function startup() {
            $this->GearmanWorker = new GearmanWorker();
    }
    
    public function help() {
            $this->out('Gearman Worker as a CakePHP Shell');
    }
    
    public function main() {
            $this->GearmanWorker->addServers('127.0.0.1');
            $this->GearmanWorker->addFunction('json_test', array($this, 'my_json_test'));
            $this->GearmanWorker->addFunction('reverse', array($this, 'my_reverse_function'));
            while( $this->GearmanWorker->work() );
    }
    
    public function my_json_test($job) {
            $params = json_decode($job->workload(),true);
            // add a dummy response so we know that it worked
            $params['response'] = "hola";//$this->Company->find('first');
            
            //$params['response'] = 'it worked';
            return json_encode($params);
    }
    
    function my_reverse_function($job) {
      return strrev($job->workload());
    }
    
    
}
