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
 * DASHBOARD 2.0 - Dashboard overview
 *  * 
 * [2017-08-01] version 0.1
 * Initial view
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
    $(function (){
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            window.location.replace('/investors/readLinkedAccounts');
        });
    });
</script>
<div class="dashboardOverview">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="card card-stats">
                <div class="card-content">
                    <h4 class="category"><?php echo __('Total Volume')?></h4>
                    <h3 class="title">76.125,11 €</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <table id="box1Table" class="table">
                            <tbody>
                                <tr>
                                    <td width="70%"><?php echo __('Invested Assets')?></td>
                                    <td><?php echo __('76.125,00 €')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Reserved Funds')?></td>
                                    <td><?php echo __('32.000,00 €')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Cash')?></td>
                                    <td><?php echo __('25.252,00 €')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Cash Drag')?></td>
                                    <td><?php echo __('25%')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Net Deposits')?></td>
                                    <td><?php echo __('13.000,00 €')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Number of Investments')?></td>
                                    <td><?php echo __('1254')?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="card card-stats">
                <div class="card-content">
                    <h4 class="category"><?php echo __('Actual Yield')?></h4>
                    <h3 class="title">12,25%</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <table id="box2Table" class="table">
                            <tbody>
                                <tr>
                                    <td><?php echo __('Return Past 12 Months')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Return Year to date')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Return Past Months')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __(' ')?></td>
                                    <td><?php echo __(' ')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Net Return')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Net Return Past Months')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="card card-stats">
                <div class="card-content">
                    <h4 class="category"><?php echo __('Defaulted')?></h4>
                    <h3 class="title">2,25%</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <table id="box3Table" class="table">
                            <tbody>
                                <tr>
                                    <td><?php echo __('Current')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('8-30 DPD')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('31-61 DPD')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('60-90 DPD')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Written Off')?></td>
                                    <td><?php echo __('')?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr width="90%">
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('Key Performance statistics of Individual Platforms') ?></strong></h4>
                </div>
                <div class="card-content table-responsive">
                    //Aquí va la tabla :D
                </div>
                <div class="card-footer">
                    <input type='button' id="btnAccountLinking" class='btn btn-default btn1CR btnRounded pull-left' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                    <input type='button' id="btnStart" class='btn btn-default btn1CR btnRounded pull-right' name='accountLinking' value='<?php echo __('Go to One Click Registration')?>' />
                    <br/><br/>
                </div>
            </div>
        </div>
    </div>
</div>