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
 * @version 0.4
 * @date 2017-05-29
 * @package
 * 
 * DASHBOARD 2.0 - Initial view where user can select it it's accredited user or not. Links go to
 * 1CR or Account Linking.
 * 
 * [2017-08-01] version 0.1
 */
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script>
    $(function (){
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            
        });
        //Click on Start btn
        $(document).on("click", "#btnStart", function(){
            
        });
    });
</script>
<style>
    .tab-content {
        padding: 10px 20px !important;
    }
</style>

<div id="dashboardInitialPanel" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile2">
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" style="max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <img src="http://via.placeholder.com/200x350"/>
                                    <input type='button' id="btnAccountLinking" class='btn btn-default center-block' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <img src="http://via.placeholder.com/200x350"/>
                                    <input type='button' id="btnStart" class='btn btn-default center-block' name='start' value='<?php echo __('Start')?>' />
                                </div>
                            </div>
                        </div> <!-- /tab-content -->
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>
