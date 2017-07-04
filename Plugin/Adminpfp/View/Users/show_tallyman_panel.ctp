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
* @version 0.2
* @date 2017-06-13
* @package
 * 
 * One Click Registration - PFD Admin Tallyman
 * PFP admin Tallyman service about investor profiles.
 * 
 * [2017-06-13] version 0.1
 * First view.
 * 
 * [2017-06-19] version 0.2
 * Added new table to insert Tallyman info about searched user.
 * Added plugins CSS & JS
 * 

 
 
 Pending
 * form validation
 

 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="/plugins/chartjs/Chartist.min.js"></script>

<script type="text/javascript">
 
function successTallymanData(data) {
    $("#TallymanResult").html(data);
    console.log("edit_user_profile_data: LINE 70");

}
 
 
 function errorTallymanData(data) {

    $("#TallymanResult").html(data);
	console.log("profile_data: LINE 99990");

}
 
 
 
 
$(document).ready(function() {
   
$("#tallymanBtnSearch").bind("click", function(event) {
console.log("btn clicked");    
     
    var link = $(this).attr( "href" );
console.log ("link = " + link); 
  // validar los parametros  
    var inputid = $("#tallymanInputId").val();
    var useremail = $("#tallymanInputEmail").val();
    var usertelephone = $("#tallymanInputTelephone").val();   
    var params = { inputId: inputid, userEmail:useremail, userTelephone: usertelephone };
    var data = jQuery.param( params );
    
    event.stopPropagation();
    event.preventDefault();    
    getServerData(link, data, successTallymanData, errorTallymanData);
         
         });   
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



<div id="1CR_pfpAdmin_3_tallyman">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - Tallyman') ?></strong></h4>
                    <p class="category"><?php echo __('Tallyman service about Investor Profile') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!--<div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>-->
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut' 
                                        . 'labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamc'
                                        . 'laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in' 
                                        . 'voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat' 
                                        . 'non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
                                        );
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div id="investorFilters" class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
                                            <label class= "invisible"></label>
                                            <h4 class="header1CR"><?php echo __('Search:') ?></h4>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('ID')?></label>
                                            <input type="text" id ="tallymanInputId" class="form-control blue_noborder3" placeholder="<?php echo __('ID')?>">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('Email')?></label>
                                            <input type="text" id ="tallymanInputEmail" class="form-control blue_noborder3" placeholder="<?php echo __('Email')?>">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('Telephone')?></label>
                                            <input type="text" id ="tallymanInputTelephone" class="form-control blue_noborder3" placeholder="<?php echo __('Telephone')?>">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label class= "invisible"> </label>
                                            <button type="submit" id="tallymanBtnSearch" href="/adminpfp/users/readtallymandata" class="btn btnPFPAdmin center-block btnRounded"><?php echo __('Search')?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
</div>

<div id="TallymanResult"></div>