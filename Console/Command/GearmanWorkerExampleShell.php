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

class GearmanWorkerExampleShell extends AppShell {
    
    protected $GearmanWorker;
    
    var $uses = array('Marketplace', 'Company', 'Urlsequence');
    
    public function startup() {
        $this->GearmanWorker = new GearmanWorker();
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'));
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
    }
    
    public function help() {
            $this->out('Gearman Worker as a CakePHP Shell');
    }
    
    public function main() {
            $this->GearmanWorker->addServers('127.0.0.1');
            $this->GearmanWorker->addFunction('json_test', array($this, 'my_json_test'));
            $this->GearmanWorker->addFunction('reverse', array($this, 'my_reverse_function'));
            $this->GearmanWorker->addFunction('typeError', array($this, 'tryTypeError'));
            $this->GearmanWorker->addFunction('Error', array($this, 'tryErrorGearman'));
            $this->GearmanWorker->addFunction('fatalError', array($this, 'fatalErrorUnrecoverable'));
            $this->GearmanWorker->addFunction('testException', function(GearmanJob $job) {
                throw new Exception('Boom');
            });
            $this->GearmanWorker->addFunction('testFail', function(GearmanJob $job) {
                
                try {
                    throw new Exception('Boom');
                } catch (Exception $e) {
                    /*syslog(LOG_ERR, $e);
                    exit(1);*/
                    $job->sendException($e->getMessage());
                    $job->sendFail();
                    
                }
            });
            while( $this->GearmanWorker->work() );
    }
    
    public function my_json_test($job) {
            $params = json_decode($job->workload(),true);
            // add a dummy response so we know that it worked
            //sleep(3);
            echo "Sending email: params";
            $sectors = $this->getSectorsByRole(4);
            $params['response'] = $sectors;//$this->Company->find('first');
            $this->out('Background job handle: '.json_encode($params));
            $this->out('/n' . microtime());
            
            //$params['response'] = 'it worked';
            return json_encode($params);
    }
    
    function my_reverse_function($job) {
      return strrev($job->workload());
    }
    
    function getSectorsByRole($roleId = null) {
        if (empty($roleId)) {
            return false;
        }
        $this->Sector = ClassRegistry::init('Sector');
        $options['joins'] = array(
            array('table' => 'roles_sectors',
                'alias' => 'RolesSector',
                'type' => 'inner',
                'conditions' => array(
                    'Sector.id = RolesSector.sector_id'
                )
            ),
            array('table' => 'roles',
                'alias' => 'Role',
                'type' => 'inner',
                'conditions' => array(
                    'RolesSector.role_id = Role.id'
                )
            )
        );

        $options['conditions'] = array(
            'Role.id' => $roleId
        );
        //$options['field'] = array('Sector.*');
        $options['recursive'] = -1;
        $options['order'] = array(
            'Sector.sectors_father',
            'Sector.sectors_subSectorSequence'
        );

        $sectors = $this->Sector->find('all', $options);
        return $sectors;
    }
    
    public function tryTypeError($job) {
        $this->typeErrorFunction(true);
    }
    
    public function typeErrorFunction(array $typeError) {
        //code fake
    }
    
    public function tryErrorGearman($job) {
        $this->tryErrorOnGearman();
    }
    
    public function tryfatalErrorUnrecoverable() {
        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
    }
    
    public function tryfatalError() {
        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
    }
    
    public function exception_handler($code) {
        echo "\n exception code : " . $code . "\n";
        $this->job->sendException('Boom');
    }
   
    public function error_handler($code) {
        if ($code != E_WARNING && $code != E_NOTICE) {
            echo "\n error code : " . $code . "\n";
            $this->job->sendFail();
        }
    }
   
    public function fatalErrorShutdownHandler() {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            //echo "\n fatal error code : " . E_ERROR . "\n";
            $this->error_handler(E_ERROR);
        }
    }
    
}
