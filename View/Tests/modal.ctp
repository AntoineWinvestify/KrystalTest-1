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
 * @author
 * @version 0.1
 * @date 2017-08-03
 * @package
 * 
 * MODALS new styling
 * 
 * [2017-08-03] version 0.1
 * Initial modal
 */
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script>
    $(document).ready(function () {
        $(document).on("click", ".btnClose", function() {
            $("#dashboardInitialPanel").removeClass("show");
            $("#dashboardInitialPanel").hide();
        });
    });
</script>
<style>
    .wizard-card-small {
        padding: 0px !important;
        border: 1px solid #87e14b;
        -webkit-box-shadow: 7px 7px 5px 0px rgba(97,97,97,0.5);
        -moz-box-shadow: 7px 7px 5px 0px rgba(97,97,97,0.5);
        box-shadow: 7px 7px 5px 0px rgba(97,97,97,0.5);
    }
    .wizard-header {
        padding-top: 15px;
        padding-bottom: 10px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    .wizard-footer {
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        background-color: #e2f8d3;
    }
    .marginbtn {
        margin-top: 15px;
        margin-bottom: 10px;
        background-color: white;
    }
    .btnClose {
        top: 5px;
        right: 5px;
        position: absolute;
        cursor: pointer;
        color: #a6a6a6;
    }
    p {
        color: #66615B;
    }
    .tab-content {
        padding: 0px 20px !important;
    }
</style>

<div id="dashboardInitialPanel" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="wizard-container-small">
                    <div class="card wizard-card-small" data-color="green" id="wizardProfile2">
                        <div class="wizard-header text-center">
                            <i class='ion ion-close-circled btnClose'></i>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" style="max-width:250px;"/>
                            <p align="center"><?php echo __('subtitle if needed')?></p>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p align="justify"><?php //echo __('Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) descon')?></p>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <img src="http://via.placeholder.com/220x150" style="height: 150px; width: 220px;" class="center-block"/><br/>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <img src="http://via.placeholder.com/220x150" style="height: 150px; width: 220px;" class="center-block"/><br/>
                                </div>
                            </div>
                        </div> <!-- /tab-content -->
                        <div class="wizard-footer">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <input type='button' id="btnStart" class='btn marginbtn btnPFPAdmin center-block' name='start' value='<?php echo __('Go to One Click Registration')?>' />
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <input type='button' id="btnAccountLinking" class='btn btn-default marginbtn center-block' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                                </div>
                            </div>
                        </div>
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>