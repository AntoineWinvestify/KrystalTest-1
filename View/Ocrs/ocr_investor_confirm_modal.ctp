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
* @date 2017-06-09
* @package
 * 
 * 
 * Modal to investor when he/she has to CONFIRM the data giving to Winvestify to register on the 
 * selected platforms shown on list
 * 
 * [2017-06-09] Version 0.1
 * First view.
 * Insert modal
 * Insert style rules.
 * 
 * [2017-06-14] Version 0.2
 * Added buttons to modal
 * Added js & css from paper bootstrap wizard
 * Added js to buttons
*/

?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script type="text/javascript" src="/modals/assets/js/jquery.bootstrap.wizard.js"></script>
<script type="text/javascript" src="/modals/assets/js/paper-bootstrap-wizard.js"></script>
<script> 
    $(function () {
        $(document).on("click", ".closeBtn", function(){
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            //Data saved on form
        });
        $(document).on("click", "#btnConfirm", function(){
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            //server validation
        });
        $(document).on("click", "#btnCancel", function() {
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            //data saved on form
        });
    });
</script>
<div id="1CR_investor_3_confirming" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" class="center-block" style="max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p align="justify"><?php echo __('paragraph about investor giving Winvestify all his investment data to register on the next list of selected platforms.')?></p>
                                    <ul>
                                        <li>platform1</li>
                                        <li>platform2</li>
                                    </ul>
                                </div>
                            </div>
                        </div> <!-- /tab-content -->
                        <div class="wizard-footer">
                            <div class="pull-right">
                                <input type='button' id="btnConfirm" class='btn btn-default btn-wd' name='confirm' value='Confirm' />
                            </div>

                            <div class="pull-left">
                                <input type='button' id="btnCancel" class='btn btn-default btn-wd' name='cancel' value='Cancel' />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>