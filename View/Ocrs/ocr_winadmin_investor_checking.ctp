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
 * @version 0.4
 * @date 2017-05-29
 * @package
 * 
 * 
 * List of new users to check data before sending it to PFP Admin.
 * 
 * [2017-05-29] Version 0.1
 * First view.
 * 
 * [2017-06-09] Version 0.2
 * MOVED: Investors data to another view & insert it on a MODAL. (like register panel).
 * 
 * [2017-06-11] Version 0.3
 * Deleted unnecessary info
 * 
 * [2017-06-13] Version 0.4
 * Added green boxes
 * Added style to overlay
 * 
 * [2017-06-21] Version 0.5
 * User data table
 * 
 * [2017-06-23] Version 0.6
 * User data table from db
 * 
 * [2017-06-28] Version 0.7
 * Deleted overlay
 * Added Datatable Javascript
 */
?>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
<script src="/plugins/intlTelInput/js/utils.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="/plugins/iCheck/all.css">
<script type="text/javascript" src="/plugins/iCheck/icheck.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
    $(function () {
        $("#usersTable").DataTable();
    });
</script>
<div id="1CR_winAdmin_1_investorChecking">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Investor Checking') ?></strong></h4>
                </div>
                <div class="card-content togetoverlay">
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
                            <h4 class="header1CR"><?php echo __('Investors registered on your platform') ?></h4>
                            <div class="table-responsive">  
                                <table id="usersTable" class="table table-striped dataTable display" width="100%" cellspacing="0"
                                                                                data-order='[[ 5, "asc" ]]' data-page-length='20'>
                                        <thead>
                                                <tr>
                                                    <th width="10%"><?php echo __('Date')?></th>
                                                    <th width="10%"><?php echo __('Name')?></th>
                                                    <th width="10%"><?php echo __('Surname')?></th>
                                                    <th width="10%"><?php echo __('Telephone')?></th>
                                                    <th><?php echo __('Email')?></th>
                                                    <th width="15%"><?php echo __('Status')?></th>
                                                    <th width="5%"><?php echo __('Action')?></th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th width="10%"><?php echo __('Date') ?></th>
                                            <th width="10%"><?php echo __('Name') ?></th>
                                            <th width="10%"><?php echo __('Surname') ?></th>
                                            <th width="10%"><?php echo __('Telephone') ?></th>
                                            <th><?php echo __('Email') ?></th>
                                            <th width="15%"><?php echo __('Status') ?></th>
                                            <th width="5%"><?php echo __('Action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($usersList as $usersTable) {
                                            ?>
                                            <tr>
                                                <td><?php echo __($usersTable['Ocr']['modified']) ?></td>
                                                <td><?php echo __($usersTable['Investor']['investor_name']) ?></td>
                                                <td><?php echo __($usersTable['Investor']['investor_surname']) ?></td>
                                                <td><?php echo __($usersTable['Investor']['investor_telephone']) ?></td>
                                                <td><?php echo __($usersTable['Investor']['investor_email']) ?></td>
                                                <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __($usersTable['Ocr']['ocr_status']) ?></span></td>
                                                <td><a href="/ocrs/ocrWinadminInvestorData/<?php echo __($usersTable['Investor']['id']) ?>"><button class="btn btn-default btnWinAdmin btnRounded view"><?php echo __('View') ?></button></a></td>
                                            </tr>
                                        <?php } ?>
                                        <!-- 
                                         <tr>
                                             <td><?php echo __('2017-01-01') ?></td>
                                             <td><?php echo __('Nameeeeeee') ?></td>
                                             <td><?php echo __('Surnameeee') ?></td>
                                             <td><?php echo __('+34123456789') ?></td>
                                             <td><?php echo __('example@example.com') ?></td>
                                             <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                             <td><button class="btn btn-default btnWinAdmin btnRounded"><?php echo __('View') ?></button></td>
                                         </tr>
                                         <tr>
                                             <td><?php echo __('2017-01-01') ?></td>
                                             <td><?php echo __('Nameeeeeee') ?></td>
                                             <td><?php echo __('Surnameeee') ?></td>
                                             <td><?php echo __('+34123456789') ?></td>
                                             <td><?php echo __('example@example.com') ?></td>
                                             <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                             <td><button class="btn btn-default btnWinAdmin btnRounded"><?php echo __('View') ?></button></td>
                                         </tr>
                                         <tr>
                                             <td><?php echo __('2017-01-01') ?></td>
                                             <td><?php echo __('Nameeeeeee') ?></td>
                                             <td><?php echo __('Surnameeee') ?></td>
                                             <td><?php echo __('+34123456789') ?></td>
                                             <td><?php echo __('example@example.com') ?></td>
                                             <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                             <td><button class="btn btn-default btnWinAdmin btnRounded"><?php echo __('View') ?></button></td>
                                         </tr>
                                         <tr>
                                             <td><?php echo __('2017-01-01') ?></td>
                                             <td><?php echo __('Nameeeeeee') ?></td>
                                             <td><?php echo __('Surnameeee') ?></td>
                                             <td><?php echo __('+34123456789') ?></td>
                                             <td><?php echo __('example@example.com') ?></td>
                                             <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                             <td>
                                                 <a href="/ocrs/ocrWinadminInvestorData"> 
                                                     <button class="btn btn-default btnWinAdmin btnRounded btnRounded" type="button">
                                        <?php echo __('View') ?>
                                                     </button>
                                                 </a>
                                             </td>
                                         </tr> -->

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

