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
 * [2017-07-121] version 0.3
 * form validation with jd

 
 
 

 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="/plugins/chartjs/Chartist.min.js"></script>

<script type="text/javascript">
 
function successTallymanData(data) {
    $("#TallymanResult").html(data);
    
}
 
 
function errorTallymanData(data) {
    $("#tallymanGeneral").html(data);
	console.log("profile_data: LINE 60 CONFIRMATION REQUIRED");
    $(".ErrorTallyman").show();
}
 
 

function successTallymanCheckCharging(data) {
    
console.log("Charging check has been done, data string = " + data);    
    $("#TallymanResult").html(data);

}
 
 
function errorTallymanCheckCharging(data) {
    
console.log("Error occured while checking the charging of the investorrequest");
    $("#TallymanResult").html(data);
	
}
 
 
$(document).ready(function() {
   
    $("#tallymanBtnSearch").bind("click", function(event) {
        console.log("btn clicked");    
        $(".ErrorTallyman").hide();
         if ((result = app.visual.checkFormPFPAdminTallyman()) === true) { 
            var link = $(this).attr( "href" );

            // validate the input parameters
            var inputid = $("#tallymanInputId").val();
            var useremail = $("#tallymanInputEmail").val();
            var usertelephone = $("#tallymanInputTelephone").val(); 
            var chargingconfirmed = 0;

            var params = { inputId: inputid, userEmail:useremail, userTelephone: usertelephone, chargingConfirmed:chargingconfirmed };
            var data = jQuery.param( params );

            event.stopPropagation();
            event.preventDefault();    
            getServerData(link, data, successTallymanData, errorTallymanData);
        }
    }); 
    
    $(document).on("click", "#telephoneIconTooltip", function(){
        $("#telephoneTooltip").toggle();
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
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - Tallyman') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!--<div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>-->
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('Para activar la búsqueda de uno de sus inversores es necesario rellenar obligatoriamente dos campos por ejemplo: teléfono y email. ');
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div id="investorFilters" class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('ID')?></label>
                                            <input type="text" id ="tallymanInputId" class="form-control blue_noborder3 tallymanID tallymanGeneral" placeholder="<?php echo __('ID')?>">
                                            <?php
                                            $errorClassesText = "errorInputMessage ErrorID";
                                            if (array_key_exists('tallyman_id', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['tallyman_id'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4">
                                            <label><?php echo __('Email')?></label>
                                            <input type="text" id ="tallymanInputEmail" class="form-control blue_noborder3 tallymanEmail tallymanGeneral" placeholder="<?php echo __('Email')?>">
                                            <?php
                                            $errorClassesText = "errorInputMessage ErrorEmail";
                                            if (array_key_exists('tallyman_email', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['tallyman_email'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4">
                                            <label><?php echo __('Telephone')?> <i id="telephoneIconTooltip" class="fa fa-exclamation-circle"></i></label>
                                            <input type="text" id ="tallymanInputTelephone" class="form-control blue_noborder3 tallymanTelephone tallymanGeneral" placeholder="<?php echo __('Telephone')?>">
                                            <?php 
                                            $errorClassesText = "errorInputMessage ErrorTelephone";
                                            if (array_key_exists('tallyman_telephone', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['tallyman_telephone'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-1">
                                            <label class= "invisible"> </label>
                                            <button type="submit" id="tallymanBtnSearch" href="/adminpfp/ocrs/readtallymandata" class="btn btnPFPAdmin pull-right btnRounded"><?php echo __('Search')?></button>
                                        </div>
                                        <div class="col-md-10" id="telephoneTooltip" style="display:none; margin-top: 10px;"><?php echo __('El teléfono debe de contener el código internacional (EJ: España +34)')?></div>
                                        <div class="col-md-10" style="margin-top: 10px;">
                                            <?php 
                                            $errorClassesText = "errorInputMessage ErrorTallyman";
                                            if (array_key_exists('tallyman_general', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage" id="tallymanGeneral">
                                                    <?php echo $billValidationErrors['tallyman_general'][0] ?>
                                                </span>
                                            </div>
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
