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
 * Description of GearmanWorkerShell
 *
 * @author antoiba
 */
class GearmanWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    
    public function startup() {
        $this->GearmanWorker = new GearmanWorker();
        @set_exception_handler(array($this, 'exception_handler'));
        @set_error_handler(array($this, 'exception_handler'));
    }
    
    
    public function exception_handler($exception) {
        $this->job->sendException('Boom');
        $this->job->sendFail();
   }
}
