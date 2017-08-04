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
 * [2017-06-28] Version 0.3
 * Added JS validation
 * Added All input error fields
 * Added general input error
 * 
 * [2017-06-29] Version 0.4
 * Added scripts
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="/plugins/chartjs/Chartist.min.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script src="/plugins/intlTelInput/js/utils.js"></script>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
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
<script>
    $(function () {
        //telephone
        //$('#tallymanTelephone').intlTelInput();
        //chart doughnut
        var ctx = document.getElementById("pieChart1").getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ["M", "T"],
            datasets: [{
              backgroundColor: [
                "#2ecc71",
                "#3498db"
              ],
              data: [30, 70]
            }]
          }
        });
        
        $(document).on("click", "#searchBtn", function() {
            //Javascript Validation
            if ((result = app.visual.checkFormPFPAdminTallyman()) === true) {
                //server validation
                alert("server validation!!!!");
            }
        });
});
</script>
<div id="1CR_pfpAdmin_3_tallyman">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-12">
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
                                echo __('Para activar la búsqueda de uno de sus inversores es necesario rellenar obligatoriamente el campo del DNI/NIE más uno de los dos campos restantes. Ej: DNI(obligatorio)+ teléfono o email.')
                                ?></p>
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div id="investorFilters" class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('NIF')?></label>
                                            <?php
                                            $class = "form-control blue_noborder3 tallymanNIF tallymanGeneral";
                                            echo $this->Form->input('/*AQUÍ NO SÉ LO QUE TIENE QUE IR!!!!!*/', array(
                                                'name' => 'nif',
                                                'id' => 'tallyman_nif',
                                                'placeholder' => 'Enter NIF here',
                                                'label' => false,
                                                'class' => $class,
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorNIF";
                                            if (array_key_exists('tallyman_nif', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['tallyman_nif'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                            <label><?php echo __('Email')?></label>
                                            <?php
                                            $class = "form-control blue_noborder3 tallymanEmail tallymanGeneral";
                                            echo $this->Form->input('/*AQUÍ NO SÉ LO QUE TIENE QUE IR!!!!!*/', array(
                                                'name' => 'email',
                                                'id' => 'tallyman_email',
                                                'placeholder' => 'Enter email here',
                                                'label' => false,
                                                'class' => $class,
                                            ));
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
                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                <label><?php echo __('Telephone')?></label>
                                                <?php
                                                $class = "form-control blue_noborder3";
                                                echo $this->Form->input('/*AQUÍ NO SÉ LO QUE TIENE QUE IR!!!!!*/', array(
                                                    'name' => 'telephone',
                                                    'id' => 'tallymanTelephone',
                                                    'label' => false,
                                                    'placeholder' => 'Enter telephone here',
                                                    'class' => $class,
                                                    'type' => 'tel'
                                                ));
                                                $errorClassesText = "errorInputMessage ErrorTelephone tallymanTelephone tallymanGeneral";
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
                                        <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
                                            <label class= "invisible"> </label>
                                            <button type="button" id="searchBtn" class="btn btnPFPAdmin pull-right btnRounded"><?php echo __('Search')?></button>
                                        </div>
                                        <div class="col-md-10">
                                            <?php 
                                            $errorClassesText = "errorInputMessage ErrorTallyman";
                                            if (array_key_exists('tallyman_general', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
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