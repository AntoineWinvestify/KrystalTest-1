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
 * @version 0.2
 * @date 2017-08-02
 * @package
 * 
 * DASHBOARD 2.0 - Dashboard overview
 * 
 * [2017-08-01] version 0.1
 * Initial view
 * 
 * [2017-08-02] version 0.2
 * Added plugins
 * Added style
 * Added Js
 * Added all content
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script>
    $(function (){
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            window.location.replace('/investors/readLinkedAccounts');
        });
        
        $(document).on("click", "#btnAccountLinkingB", function(){
            $("#btnAccountLinkingB").hide();
            $("#keyIndividualPlatforms").show();
        });
        
        $(document).on("click", "#btnMyInvestments", function(){
            $("#btnMyInvestments").hide();
            $("#dashboardMyInvestments").show();
        });
    });
</script>
<style>
    td {
        font-size: 12px;
        padding: 5px 0px !important;
    }
    td.right { 
        text-align: right; 
    }
    .card-footer {
        border-top: 1px solid #00e64d !important;
    }
    .headerBox {
        font-size: large;
    }
    hr {
        margin: 8px !important;
        border-color: white !important;
    }
</style>
<div class="dashboardOverview">
    <div class="row" id="overview">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#globalOverviewTab" data-toggle="tab">
                                        Global Overview
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#zankTab" data-toggle="tab">
                                        Zank
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#arboribusTab" data-toggle="tab">
                                        Arboribus
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="#myTripleATab" data-toggle="tab">
                                        MyTripleA
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="globalOverviewTab">
                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="card card-stats">
                                    <div class="card-content">
                                        <p class="headerBox"><strong><?php echo __('Total Volume')?></strong></p>
                                        <h3 class="title">76.125,11 €</h3>
                                    </div>
                                    <div class="card-footer">
                                        <table id="box1Table" class="table">
                                            <tbody>
                                                <tr>
                                                    <td><?php echo __('Invested Assets')?></td>
                                                    <td class="right"><?php echo __('76.125,00 €')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Reserved Funds')?></td>
                                                    <td class="right"><?php echo __('32.000,00 €')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Cash')?></td>
                                                    <td class="right"><?php echo __('25.252,00 €')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Cash Drag')?></td>
                                                    <td class="right"><?php echo __('25%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Net Deposits')?></td>
                                                    <td class="right"><?php echo __('13.000,00 €')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Number of Investments')?></td>
                                                    <td class="right"><?php echo __('1254')?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="card card-stats">
                                    <div class="card-content">
                                        <p class="headerBox"><strong><?php echo __('Actual Yield')?></strong></p>
                                        <h3 class="title">12,25%</h3>
                                    </div>
                                    <div class="card-footer">
                                        <table id="box2Table" class="table" width="100%" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td><?php echo __('Return Past 12 Months')?></td>
                                                    <td class="right"><?php echo __('12,15%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Return Year to date')?></td>
                                                    <td class="right"><?php echo __('11,33%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Return Past Months')?></td>
                                                    <td class="right"><?php echo __('9,22%')?></td>
                                                </tr>
                                                <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                <tr>
                                                    <td><?php echo __('Net Return')?></td>
                                                    <td class="right"><?php echo __('995,00 €')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Net Return Past Months')?></td>
                                                    <td class="right"><?php echo __('935,00 €')?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="card card-stats">
                                    <div class="card-content">
                                        <p class="headerBox"><strong><?php echo __('Defaulted')?></strong></p>
                                        <h3 class="title">2,25%</h3>
                                    </div>
                                    <div class="card-footer">
                                        <table id="box3Table" class="table">
                                            <tbody>
                                                <tr>
                                                    <td><?php echo __('Current')?></td>
                                                    <td class="right"><?php echo __('2,25%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('8-30 DPD')?></td>
                                                    <td class="right"><?php echo __('2,99%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('31-61 DPD')?></td>
                                                    <td class="right"><?php echo __('2,25%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('60-90 DPD')?></td>
                                                    <td class="right"><?php echo __('1,99%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('90 - DPD')?></td>
                                                    <td class="right"><?php echo __('1,22%')?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo __('Written Off')?></td>
                                                    <td class="right"><?php echo __('3.678,00 €')?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="zankTab">
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p>zankkkkkkkk</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="arboribusTab">
                            arboribussssssss
                        </div>
                        <div class="tab-pane" id="myTripleATab">
                            mytripleaaaaaaaaa
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="btnAL">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type='button' id="btnAccountLinkingB" class='btn btn-default pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
            <input type='button' id="btnMyInvestments" class='btn btn-default pull-left' name='myInvestments' value='<?php echo __('My Investments')?>' />
        </div>
    </div>
    <div class="row" style="display:none;" id="keyIndividualPlatforms">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="gray">
                    <h4 class="title"><strong><?php echo __('Key Performance statistics of Individual Platforms') ?></strong></h4>
                </div>
                <div class="card-content table-responsive">
                    //Aquí va la tabla :D
                </div>
                <div class="card-footer">
                    <input type='button' id="btnAccountLinking" class='btn btn-default pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                    <input type='button' id="btnStart" class='btn btn-default pull-left' name='accountLinking' value='<?php echo __('Go to One Click Registration')?>' />
                    <br/><br/>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dashboardMyInvestments" style="display:none;">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <span class="nav-tabs-title"><strong><?php echo __('Investments:')?></strong></span>
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#allInvestments" data-toggle="tab">
                                        All
                                        <div class="ripple-container"></div></a>
                                </li>
                                <li class="">
                                    <a href="#defaultInvestments" data-toggle="tab">
                                        Defaulted
                                        <div class="ripple-container"></div></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
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
</div>