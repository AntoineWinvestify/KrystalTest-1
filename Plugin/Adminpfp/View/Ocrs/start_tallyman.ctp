<?php
/**
* +--------------------------------------------------------------------------------------------+
* | Copyright (C) 2017, http://www.winvestify.com                                              |
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
* @date 2017-07-08
* @package
 * 
 * One Click Registration - Adminpfp
 * Invoke the Tallyman service from the 1ClickRegistration adminpfp function
 * 
 * 2017-07-08        version 0.1
 * Two mandatory fields, investorTelephone and investor_email are added to the url as 
 * extra parameters
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * PENDING
 * 
 * 
 */
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">

 
<script>
    
function successTallymanData(data) {
    
console.log("successTallymanData(data): LINE 50");    
    $("#TallymanResult").html(data);
    
}
 
 
function errorTallymanData(data) {
    
console.log("errorTallymanData(data)");
    $("#TallymanResult").html(data);
	console.log("profile_data: LINE 60");

}
 
 
    
$(document).ready(function() {

    var link = $(this).attr( "href" );
    link = "/adminpfp/users/readtallymandata";  // FOR TESTING PURPOSES ONLY


  // validate the input parameters
    var inputid = "<?php echo $investorDNI ?>";
    var useremail = "<?php echo $investorEmail ?>";
    var usertelephone = "<?php echo $investorTelephone ?>";

    var params = { inputId: inputid, userEmail:useremail, userTelephone: usertelephone };
    var data = jQuery.param( params );
  
    getServerData(link, data, successTallymanData, errorTallymanData);
         });   

</script>


<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
</style>



<div id="TallymanResult"></div>