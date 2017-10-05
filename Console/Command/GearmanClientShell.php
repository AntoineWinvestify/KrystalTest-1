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
 * Description of GearmanClientShell
 *
 */
class GearmanClientShell extends AppShell {
    
    protected $GearmanClient;
    
    public function startup() {
        $this->GearmanClient = new GearmanClient();
        $this->Applicationerror = ClassRegistry::init('Applicationerror');
    }
    
    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }
    
    /**
     * Function to catch a fail on a Gearman Worker
     * @param GearmanTask $task
     */
    public function verifyFailTask(GearmanTask $task) {
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: " . $task->data() . GEARMAN_WORK_FAIL . "\n";
    }
    
    /**
     * Function to catch an exception on a Gearman Worker
     * @param GearmanTask $task
     */
    public function verifyExceptionTask (GearmanTask $task) {
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: " . $task->data() . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    /**
     * 
     * @param GearmanTask $task
     */
    public function verifyCompleteTask (GearmanTask $task) {
        echo "ID Unique: " . $task->unique() . "\n";
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
    
    public function deleteFolderByDate($key, $date) {
        $configPath = Configure::read('files');
        $partialPath = $configPath['investorPath'];
        $path = $this->userReference[$key] . DS . $date;
        $path = $partialPath . DS . $path;
        $folder = new Folder($path);
        if (!is_null($folder->path)) {
            $delete = $folder->delete();
        }
        return $delete;
    }
}
