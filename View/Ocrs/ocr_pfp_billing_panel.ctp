<?php
/*
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
 * One Click Registration - PFD Billing Panel
 * Panel with all billings related to PFD
 * 
 * [2017-05-23] Completed view
 *              [pending] Update filters
 * 
 * [2017-06-13] Version 0.2
 * Added green box
 * Added style to overlay
 * Updated .blue_noborder to .blue_noborder3 (green to orange focus)
 * 
 * 
 *[2017-06-26] Version 0.3
 * Table from db
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
<div id="1CR_pfpadmin_2_billingPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - Billing Panel') ?></strong></h4>
                    <p class="category"><?php echo __('History of bills associated to your PFP') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
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
                                    <p align="justify"><?php echo __('Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas "Letraset", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.')?></p>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-md-offset-7 col-lg-offset-7">
                                            <div class="input-group input-group-sm blue_noborder3">
                                                <input type="text" style="border:none; border-radius:7px;" class="form-control" placeholder="Search for...">
                                                <span class="input-group-btn">
                                                  <button class="btn btnPFPAdmin btnRounded" type="button"><?php echo __('Go!')?></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="billingTable" class="table table-striped dataTable display " width="100%" cellspacing="0"
                                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25'>
                                                <thead>
                                                        <tr>
                                                                <th width="10%"><?php echo __('Date')?></th>
                                                                <th width="20%"><?php echo __('Number')?></th>
                                                                <th><?php echo __('Concept')?></th>
                                                                <th width="10%"><?php echo __('Amount')?></th>
                                                                <th width="10%"><?php echo __('Action')?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($bills as $bill){;?>
                                                    <tr>
                                                        <td><?php echo __($bill['CompaniesFile']['created']); ?></td>
                                                        <td><?php echo __($bill['CompaniesFile']['bill_number']); ?></td>
                                                        <td><?php echo __($bill['CompaniesFile']['bill_concept']); ?></td>
                                                        <td align="right"><?php echo __($bill['CompaniesFile']['bill_amount']); ?></td>
                                                        <td>
                                                            <button href = "<?php echo __($bill['CompaniesFile']['bill_url']); ?>" class="btn btn-default btnPFPAdmin btnRounded"><?php echo __('Download')?></button>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>                                        
                                                </tbody>
                                        </table>
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
