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
* @date 2017-06-13
* @package
 * 
 * One Click Registration - WinAdmin Tallyman
 * Winadmin Tallyman service about investor profiles.
 * 
 * [2017-06-13] version 0.1
 * First view.
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
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
<div id="1CR_winAdmin_6_tallyman">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Tallyman') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('One Click Registration Le permite registrarse con un solo click en cualquier plataforma'
                                        . ' que Winvestify tenga habilitada. Para ello, cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                        . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                        . ' PFP puedan validar y autenticar su identidad.')
                                ?></p>
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
                                            <label><?php echo __('NIF')?></label>
                                            <input type="text" class="form-control blue_noborder3" placeholder="Enter NIF here">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('Email')?></label>
                                            <input type="text" class="form-control blue_noborder3" placeholder="Enter email here">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label><?php echo __('Telephone')?></label>
                                            <input type="text" class="form-control blue_noborder3" placeholder="Insert telephone here">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label class= "invisible"> </label>
                                            <button type="button" class="btn  btnWinAdmin center-block btnRounded"><?php echo __('Search')?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="header1CR"><?php echo __('Investor Tallyman Profile') ?></h4>
                                <div class="col-md-3 col-md-offset-2">dibujo</div>
                                <div class="col-md-5">
                                    <div class="progress" style="height:25px;">
                                        <div class="progress-bar progress-bar-aqua" role="progress-bar" aria-value="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
                                            <span>50%</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height:25px;">
                                        <div class="progress-bar progress-bar-red" role="progress-bar" aria-value="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                            <span>20%</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height:25px;">
                                        <div class="progress-bar progress-bar-yellow" role="progress-bar" aria-value="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                            <span>80%</span>
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