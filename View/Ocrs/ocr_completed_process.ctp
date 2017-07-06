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
* @date 2017-07-06
* @package
 * 
 * 
 * Modal to feedback user about the completed service
 * 
 * [2017-07-06] Version 0.1
 * First view.
 * Insert modal
 * Insert JS & CSS from Paper Bootstrap Wizard
 * Added JS to buttons
*/

?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script> 
    $(document).ready(function() {
        $(document).on("click", ".closeBtn", function(){
            $("#activatedService").removeClass("show");
            $("#activatedService").hide();
            window.history.back();
        });
        $(document).on("click", "#btnBack", function(){
            $("#activatedService").removeClass("show");
            $("#activatedService").hide();
            window.history.back();
        });
    });
</script>
<div id="serviceComplete" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="float:center; max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="cols-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p align="justify"><?php echo __("Congratulations, you have concluded the process of one click registration service")?></p>
                                </div>
                            </div>
                        </div> <!-- /tab-content -->
                        <div class="wizard-footer">
                            <div class="pull-right">
                                <button type='button' id="btnBack" class='btn btn-default btn-wd' name='back'><?php echo __('Back')?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>