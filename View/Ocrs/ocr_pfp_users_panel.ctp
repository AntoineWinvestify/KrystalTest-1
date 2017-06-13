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
* @date 2017-05-23
* @package
 * 
 * One Click Registration - PFD Admin Users Selection
 * Users registered by Winvestify or consulted by PFD Admin data panel
 * Tallyman Service.
 * 
 * [2017-05-23] Principal table done
 *              [pending] subdata table con every table row
 *              [pending] Update filters
 *              [pending] Add chart
 *              [pending] Update table view
 * 
 * [2017-06-13] Version 0.2
 * Added documentation
 * Added orange box
 * Added style to overlay
 * Deleted unnecessary script
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
<div id="OCR_PFDPanelB">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - New users Panel') ?></strong></h4>
                    <p class="category"><?php echo __('New users from Winvestify added to your PFP') ?></p>
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
                    <div class="row">
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
                                            <button type="button" class="btn  btnPFPAdmin center-block btnRounded"><?php echo __('Search')?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="usersTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                                <thead>
                                                        <tr>
                                                            <th width="5%"></th>
                                                            <th><?php echo __('Date')?></th>
                                                            <th><?php echo __('Name')?></th>
                                                            <th><?php echo __('Surname')?></th>
                                                            <th><?php echo __('Telephone')?></th>
                                                            <th><?php echo __('Email')?></th>
                                                            <th><?php echo __('Status')?></th>
                                                            <th><?php echo __('Action')?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="details-control">prueba</td>
                                                        <td><?php echo __('2017-01-01')?></td>
                                                        <td><?php echo __('Nameeeeeee')?></td>
                                                        <td><?php echo __('Surnameeee')?></td>
                                                        <td><?php echo __('+34123456789')?></td>
                                                        <td><?php echo __('example@example.com')?></td>
                                                        <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect')?></span></td>
                                                        <td><button class="btn  btnPFPAdmin btnRounded"><?php echo __('View')?></button></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="details-control"></td>
                                                        <td><?php echo __('2017-01-01')?></td>
                                                        <td><?php echo __('Nameeeeeee')?></td>
                                                        <td><?php echo __('Surnameeee')?></td>
                                                        <td><?php echo __('+34123456789')?></td>
                                                        <td><?php echo __('example@example.com')?></td>
                                                        <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning')?></span></td>
                                                        <td><button class="btn  btnPFPAdmin btnRounded"><?php echo __('View')?></button></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="details-control"></td>
                                                        <td><?php echo __('2017-01-01')?></td>
                                                        <td><?php echo __('Nameeeeeee')?></td>
                                                        <td><?php echo __('Surnameeee')?></td>
                                                        <td><?php echo __('+34123456789')?></td>
                                                        <td><?php echo __('example@example.com')?></td>
                                                        <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct')?></span></td>
                                                        <td><button class="btn  btnPFPAdmin btnRounded"><?php echo __('View')?></button></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="details-control"></td>
                                                        <td><?php echo __('2017-01-01')?></td>
                                                        <td><?php echo __('Nameeeeeee')?></td>
                                                        <td><?php echo __('Surnameeee')?></td>
                                                        <td><?php echo __('+34123456789')?></td>
                                                        <td><?php echo __('example@example.com')?></td>
                                                        <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating')?></span></td>
                                                        <td><button class="btn  btnPFPAdmin btnRounded"><?php echo __('View')?></button></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="details-control"></td>
                                                        <td><?php echo __('2017-01-01')?></td>
                                                        <td><?php echo __('Nameeeeeee')?></td>
                                                        <td><?php echo __('Surnameeee')?></td>
                                                        <td><?php echo __('+34123456789')?></td>
                                                        <td><?php echo __('example@example.com')?></td>
                                                        <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet')?></span></td>
                                                        <td><button class="btn  btnPFPAdmin btnRounded"><?php echo __('View')?></button></td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                                    <div class="row">
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
                        </div> <!-- /.col 9 -->
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
</div>