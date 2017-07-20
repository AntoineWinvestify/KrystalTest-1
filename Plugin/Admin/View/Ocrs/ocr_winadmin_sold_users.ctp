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
 * One Click Registration - WinAdmin Tallyman
 * WinAdmin Tallyman service about investor profiles.
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
<div id="1CR_winAdmin_5_soldUsers">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('Sold Users') ?></strong></h4>
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
                            adhasjkd
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('Sold Users History') ?></strong></h4>
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
                        <div id="investorFilters" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <?php 
                            $class = "form-control blue_noborder investorCountry". ' ' . $errorClass;
                            $countries = ["select PFP", "pfp1", "pfp2", "pfp3"];      
                                                                                echo $this->Form->input('Investor.investor_country', array(
                                                                                        'name'			=> 'country',
                                                                                        'id' 			=> 'ContentPlaceHolder_country',
                                                                                        'label' 		=> false,
                                                                                        'options'               => $countries,
                                                                                        'placeholder' 	=>  __('Country'),
                                                                                        'class' 		=> $class,
                                                                                        'value'			=> $resultUserData[0]['Investor']['investor_country'],						
                                                        ));
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table id="uploadedBills" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                       data-order='[[ 3, "desc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="15%"><?php echo __('PFP') ?></th>
                                        <th><?php echo __('Number') ?></th>
                                        <th><?php echo __('Concept') ?></th>
                                        <th><?php echo __('Amount') ?></th>
                                    </tr>

                                    <?php foreach ($bills as $billsTable) { //Bills table creation ?>
                                        <tr>
                                            <td><?php echo __($billsTable['name']) ?></td>
                                            <td><?php echo __($billsTable['info']['bill_number']) ?></td>
                                            <td><?php echo __($billsTable['info']['bill_concept']) ?></td>
                                            <td align="left"><?php echo __($billsTable['info']['bill_amount']) ?></td>
                                        </tr>
                                    <?php } ?>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>