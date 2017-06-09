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

<div id="OCR_WinAdminPanelA">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h4 class="header1CR"><?php echo __('Investors registered on your platform')?></h4>
                    <div class="table-responsive">  
                        <table id="usersTable" class="table table-striped dataTable display" width="100%" cellspacing="0"
                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25'>
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
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect')?></span></td>
                                        <td><button class="btn btn-default btn-invest btnRounded"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning')?></span></td>
                                        <td><button class="btn btn-default btn-invest btnRounded"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct')?></span></td>
                                        <td><button class="btn btn-default btn-invest btnRounded"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating')?></span></td>
                                        <td><button class="btn btn-default btn-invest btnRounded"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet')?></span></td>
                                        <td>
                                            <a href="/ocrs/ocrWinadminInvestorModal"> 
                                                <button class="btn btn-default btn-invest btnRounded btnRounded" type="button">
                                                    <?php echo __('View') ?>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                    <h4 class="header1CR"><?php echo __('Uploaded Documents')?></h4>
                    <div class="table-responsive">  
                        <table id="documentsTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                               data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                            <thead>
                                <tr>
                                    <th><?php echo __('Type of Document') ?></th>
                                    <th><?php echo __('Name') ?></th>
                                    <th><?php echo __('Status') ?></th>
                                    <th><?php echo __('Download') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4 class="header1CR"><?php echo __('Selected Platforms')?></h4>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

