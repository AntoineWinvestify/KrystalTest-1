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
        
        $(document).on("click", "[data-toggle='tab']", function(){
            $("#btnMyInvestments").hide();
            $("#btnAccountLinkingB").hide();
            $("#dashboardMyInvestments").show();
            $("#keyIndividualPlatforms").hide();
        });
        
        $(document).on("click", "#globalOverviewTab", function(){
            $("#btnMyInvestments").show();
            $("#btnAccountLinkingB").show();
            $("#dashboardMyInvestments").hide();
            $("#keyIndividualPlatforms").hide();
        });
    });
</script>
<style>
    td {
        text-align: center;
    }
    #box1Table td, #box2Table td, #box3Table td {
        font-size: 12px;
        padding: 5px 0px !important;
    }
    th {
        text-align: center;
        font-weight: bold;
    }
    td.right { 
        text-align: right; 
    }
    td.left {
        text-align: left;
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
                                    <a href="#globalOverviewTab" id="globalOverviewTab" data-toggle="tab">
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
                            <div class="row">
                                <div class="col-md-4 col-md-offset-8 col-lg-offset-8 col-lg-4">
                                    <?php
                                    $class = "filter form-control blue_noborder investorCountry" . ' ' . $errorClass;
                                    echo $this->Form->input('Investor.investor_country', array(
                                    'name' => 'country',
                                    'id' => 'filterCountry',
                                    'label' => false,
                                    'options' => $filterCompanies1,
                                    'placeholder' => __('Country'),
                                    'class' => $class,
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-offset-4 col-lg-4">
                                    <h2 align="center"><?php echo __('global overview')?></h2>
                                </div>
                            </div>
                            <div class="row">
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
                                                        <td class="left"><?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo __('76.125,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo __('32.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo __('25.252,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo __('13.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments')?></td>
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
                                                        <td class="left"><?php echo __('Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return')?></td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months')?></td>
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
                                                        <td class="left"><?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="zankTab">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                                    <img src="/img/logo/Zank.png" class="img-responsive center-block"/>
                                </div>
                            </div>
                            <div class="row firstParagraph">
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
                                                        <td class="left"><?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo __('76.125,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo __('32.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo __('25.252,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo __('13.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments')?></td>
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
                                                        <td class="left"><?php echo __('Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return')?></td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months')?></td>
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
                                                        <td class="left"><?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="arboribusTab">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                                    <img src="/img/logo/Arboribus.png" class="img-responsive center-block"/>
                                </div>
                            </div>
                            <div class="row firstParagraph">
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
                                                        <td class="left"><?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo __('76.125,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo __('32.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo __('25.252,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo __('13.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments')?></td>
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
                                                        <td class="left"><?php echo __('Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return')?></td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months')?></td>
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
                                                        <td class="left"><?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="myTripleATab">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                                    <img src="/img/logo/MyTripleA.png" class="img-responsive center-block"/>
                                </div>
                            </div>
                            <div class="row firstParagraph">
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
                                                        <td class="left"><?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo __('76.125,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo __('32.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo __('25.252,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo __('13.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments')?></td>
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
                                                        <td class="left"><?php echo __('Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return')?></td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months')?></td>
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
                                                        <td class="left"><?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
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
    </div>
    <div class="row" id="btnAL">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type='button' id="btnAccountLinkingB" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
        </div>
    </div>
    <div class="row" style="display:none;" id="keyIndividualPlatforms">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="gray">
                    <h4 class="title"><?php echo __('Key Performance statistics of Individual Platforms') ?></h4>
                </div>
                <div class="card-content table-responsive">
                    <table id="keyPerformanceStatistics" class="table">
                        <thead>
                            <tr>
                                <th><?php echo __('')?></th>
                                <th><?php echo __('Invested Asset')?></th>
                                <th><?php echo __('Cash')?></th>
                                <th><?php echo __('Yield')?></th>
                                <th><?php echo __('Net Return')?></th>
                                <th><?php echo __('Past Net Return')?></th>
                                <th><?php echo __('Current')?></th>
                                <th><?php echo __('Defaulted')?></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="/img/logo/MyTripleA.png" class="img-responsive center-block platformLogo"/></td>
                                <td>30.000,00 €</td>
                                <td>500,00 €</td>
                                <td>5,67%</td>
                                <td>1.710,00 €</td>
                                <td>1.860,00 €</td>
                                <td>87%</td>
                                <td>3,11%</td>
                            </tr>
                            <tr>
                                <td><img src="/img/logo/Zank.png" class="img-responsive center-block platformLogo"/></td>
                                <td>30.000,00 €</td>
                                <td>500,00 €</td>
                                <td>5,67%</td>
                                <td>1.710,00 €</td>
                                <td>1.860,00 €</td>
                                <td>87%</td>
                                <td>3,11%</td>
                            </tr>
                            <tr>
                                <td><img src="/img/logo/Arboribus.png" class="img-responsive center-block platformLogo"/></td>
                                <td>30.000,00 €</td>
                                <td>500,00 €</td>
                                <td>5,67%</td>
                                <td>1.710,00 €</td>
                                <td>1.860,00 €</td>
                                <td>87%</td>
                                <td>3,11%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <input type='button' id="btnAccountLinking" class='btn btn-default btnDefault pull-left' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                    <input type='button' id="btnStart" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to One Click Registration')?>' />
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
                                                <th><?php echo __('Transaction Id') ?></th>
                                                <th><?php echo __('Due Date') ?></th>
                                                <th><?php echo __('Interest') ?></th>
                                                <th><?php echo __('Invested') ?></th>
                                                <th><?php echo __('Amortized') ?></th>
                                                <th><?php echo __('Gained profit') ?></th>
                                                <th><?php echo __('Comission') ?></th>
                                                <th><?php echo __('Period of<br>Investment') ?></th>
                                                <th><?php echo __('Status') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="defaultInvestments">
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <table class="table table-striped dataTable display" id="allInvestmentsTable">
                                        <thead>
                                            <tr>
                                                <th><?php echo __('Transaction Id') ?></th>
                                                <th><?php echo __('Due Date') ?></th>
                                                <th><?php echo __('Interest') ?></th>
                                                <th><?php echo __('Invested') ?></th>
                                                <th><?php echo __('Amortized') ?></th>
                                                <th><?php echo __('Gained profit') ?></th>
                                                <th><?php echo __('Comission') ?></th>
                                                <th><?php echo __('Period of<br>Investment') ?></th>
                                                <th><?php echo __('Status') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo __('CPP_013606')?></td>
                                                <td><?php echo __('10-2016')?></td>
                                                <td><?php echo __('16,00 %')?></td>
                                                <td><?php echo __('150,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('0,00 €')?></td>
                                                <td><?php echo __('153 Días')?></td>
                                                <td></td>
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