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
 * @version 0.1
 * @date 2017-08-02
 * @package
 * 
 * DASHBOARD 2.0 - Dashboard My Investments table
 *  * 
 * [2017-08-01] version 0.1
 * Initial view
 * Added plugins
 * Added tab_panel
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
    $(function () {
        $("#allInvestmentsTable").DataTable();
    });
</script>
<style>

</style>
<div id="dashboardMyInvestments">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="blue">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <span class="nav-tabs-title">Tasks:</span>
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#allInvestments" data-toggle="tab">
                                        <i class="fa fa-circle-o"></i>
                                        All investments
                                        <div class="ripple-container"></div></a>
                                </li>
                                <li class="">
                                    <a href="#defaultInvestments" data-toggle="tab">
                                        <i class="fa fa-circle-o"></i>
                                        Default Investments
                                        <div class="ripple-container"></div></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="allInvestments">
                        <div class="row firstParagraph">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <table class="table table-striped dataTable display" id="allInvestmentsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th><?php echo __('Name')?></th>
                                            <th><?php echo __('Purpose')?></th>
                                            <th><?php echo __('Interest')?></th>
                                            <th><?php echo __('Duration')?></th>
                                            <th><?php echo __('Rating')?></th>
                                            <th><?php echo __('Progress')?></th>
                                            <th><?php echo __('Import')?></th>
                                            <th><?php echo __('Action')?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo __('1')?></td>
                                            <td><?php echo __('2')?></td>
                                            <td><?php echo __('3')?></td>
                                            <td><?php echo __('4')?></td>
                                            <td><?php echo __('5')?></td>
                                            <td><?php echo __('6')?></td>
                                            <td><?php echo __('7')?></td>
                                            <td><?php echo __('8')?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="defaultInvestments">
                        <div class="row firstParagraph">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <table class="table firstParagraph" id="allInvestmentsTable">
                                    <thead>
                                        <tr>
                                            <th><?php echo __('Name')?></th>
                                            <th><?php echo __('Purpose')?></th>
                                            <th><?php echo __('Interest')?></th>
                                            <th><?php echo __('Duration')?></th>
                                            <th><?php echo __('Rating')?></th>
                                            <th><?php echo __('Progress')?></th>
                                            <th><?php echo __('Import')?></th>
                                            <th><?php echo __('Action')?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo __('1')?></td>
                                            <td><?php echo __('2')?></td>
                                            <td><?php echo __('3')?></td>
                                            <td><?php echo __('4')?></td>
                                            <td><?php echo __('5')?></td>
                                            <td><?php echo __('6')?></td>
                                            <td><?php echo __('7')?></td>
                                            <td><?php echo __('8')?></td>
                                        </tr>
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