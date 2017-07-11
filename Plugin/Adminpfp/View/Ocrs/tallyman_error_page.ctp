<?php
/**
* +--------------------------------------------------------------------------------------------+
* | Copyright (C) 2016, http://www.winvestify.com                                              |
* +--------------------------------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify                          |
* | it under the terms of the GNU General Public License as published by                       |
* | the Free Software Foundation; either version 2 of the License, or                          |
* | (at your option) any later version.                                                        |
* | This file is distributed in the hope that it will be useful                                |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of                             |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                               |
* | GNU General Public License for more details.                                               |
* +--------------------------------------------------------------------------------------------+
*
*
* @author
* @version 0.1
* @date 2017-06-29
* @package
 * 
 * PFD Admin Tallyman
 *  Generic Error message handling
 * 
 * 2017-06-29 version 0.1
 * First view.
 * 
 * 
 
 
 
 

 */

?>
<?php 
echo "0";                               // Mark application error
    switch ($error) { 
        case USER_DOES_NOT_EXIST:                         
            $errorText = __('The user does not exist in our database');   
            break;
        case NO_DATA_AVAILABLE:                         
            $errorText = __('No data is available for this user');
            break;
        case NOT_ENOUGH_PARAMETERS:
            $errorText = __("You must provide at least 2 parameters");
            break;
    }
             
echo $errorText;
?>

