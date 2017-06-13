<?php
/**
 * +---------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                               |
 * +---------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 				 |
 * | it under the terms of the GNU General Public License as published by  			 |
 * | the Free Software Foundation; either version 2 of the License, or                           |
 * | (at your option) any later version.                                      			 |
 * | This file is distributed in the hope that it will be useful   				 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                          	 |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                |
 * | GNU General Public License for more details.        			              	 |
 * +---------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-05-23
 * @package
 * 
 */
?>

<script>
    $(document).ready(function () {
        link = "../Ocrs/ocrInvestorPlatformSelection";
        var data = null;
        getServerData(link, data, successInvestoPlatform, errorInvestoPlatform);
    });
    function successInvestoPlatform(result) {
        $("#content").html(result);
    }
    function errorInvestoPlatform(result) {
        $("#content").html();
    }
    
    
</script>
<div id="content">
</div>