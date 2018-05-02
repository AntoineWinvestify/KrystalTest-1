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
 * @date
 * @package
 */

/**
 * BaseClass Winvestify
 *
 */
class Winvestify {
    
     /**
     * 
     * Read the runtime parameters of the application
     * 
     * @return array   list of all defined runtime parameters
     *                 
     */    
    public function readRunTimeParameters() {
        $this->Runtimeconfiguration = ClassRegistry::init('Runtimeconfiguration');      
        $runtimeParameters = $this->Runtimeconfiguration->getData(null, $field = "*");
        return [$runtimeParameters][0][0]['Runtimeconfiguration'];
    }    
    
    
}
