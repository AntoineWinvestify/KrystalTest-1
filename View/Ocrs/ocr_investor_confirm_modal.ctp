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
*/

?>
<style>
    .modal-dialog{
        overflow-y: initial !important
    }
    .modal-body{
        height: 450px;
        overflow-y: auto;
    }
    ul > li > a {
        cursor:default;
    }
    .modal { overflow-y:scroll; }
</style>
<div id="1CR_investorDataChecking" class="modal show" role="dialog">
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
                            <img src="/img/logo_winvestify/Logo.png" style="float:center; max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <p align="justify"><?php echo __('paragraph about investor giving Winvestify all his investment data to register on the next list of selected platforms.')?></p>
                                <ul>
                                    <li>platform1</li>
                                    <li>platform2</li>
                                </ul>
                            </div>
                        </div> <!-- /tab-content -->
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>