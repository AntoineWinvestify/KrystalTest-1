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
 * @date 2017-10-10
 * @package
 * 
 * DASHBOARD 2.0 - Dashboard overview by company
 * 
 * [2017-10-10] version 0.1
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">

<script>
    $(function (){
        
        $("#defaultedInvestmentTable").DataTable();
        $("#allInvestmentTable").DataTable();
        
        <?php //Tooltip clicks ?>
        $(".logo").hover(function() {
            id = $(this).attr("id");
            $("#showBtn").toggle();
        });
        
        $(document).on("click", ".chartIcon", function() {
            $("#chartInfo").css("display", "block");
        });
        //dismiss enlargeImg
        $(document).on("click", "#btnCloseChartInfo", function () {
            $("#chartInfo").css("display", "none");
        });
        
        var birdsCanvas = document.getElementById("birdsChart");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        var birdsData = {
          labels: ["Spring", "Summer", "Fall", "Winter"],
          datasets: [{
            data: [20, 10, 40, 30],
            backgroundColor: [
              "rgba(255, 0, 0, 0.6)",
              "rgba(0, 255,200, 0.6)",
              "rgba(200, 0, 200, 0.6)",
              "rgba(0, 255, 0, 0.6)"
            ],
            borderColor: "rgba(255, 255, 255, 0.8)"
          }]
        };

        var chartOptions = {
          startAngle: -Math.PI / 4,
          animation: {
            animateRotate: true
          },
          responsive: false
        };

        var polarAreaChart = new Chart(birdsCanvas, {
          type: 'polarArea',
          data: birdsData,
          options: chartOptions
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
        padding-bottom: 4px !important;
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
    .platformLogo {
        max-width: 150px !important;
    }
    .ion-ios-information-outline {
        color: lightslategray;
    }
    td.logo:hover {
        background-color: rgba(0,0,0,0.3);
    }
    #showBtn {
        position: absolute;
        margin-left: 80px;
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
                                <li>
                                    <a id="backOverview" href="/tests/dashboardOverview">
                                        Global Overview
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="#zankTab" id="globalOverviewTab" data-toggle="tab">
                                        Zank
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="zankTab">
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
                                                        <td class="left"><a class="chartIcon" id="netReturn" href="#"><?php echo __('Net Return')?> <i class="ion ion-arrow-graph-up-right" style="color:black"></i></a></td>
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
</div>
<div id="investments">
    <div class="row" id="overview">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#defaultedInvestments" id="defaultedTab" data-toggle="tab">
                                        Defaulted
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#allInvestments" id="allTab" data-toggle="tab">
                                        All
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="defaultedTab">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="defaultedInvestmentTable" class="investmentDetails table striped display" width="100%" cellspacing="0" data-page-length='25'>
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('Name')?></th>
                                                    <th><?php echo __('Purpose')?></th>
                                                    <th><?php echo __('Interest Rate')?></th>
                                                    <th><?php echo __('Duration')?></th>
                                                    <th><?php echo __('Rating')?></th>
                                                    <th><?php echo __('Progress')?></th>
                                                    <th><?php echo __('Amount')?></th>
                                                    <th><?php echo __('Action')?></th>
                                                </tr>
                                                <tr>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                    <td>aaaa</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="allTab">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="allInvestmentTable" class="investmentDetails table striped display" width="100%" cellspacing="0" data-page-length='25'>
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('Name')?></th>
                                                    <th><?php echo __('Purpose')?></th>
                                                    <th><?php echo __('Interest Rate')?></th>
                                                    <th><?php echo __('Duration')?></th>
                                                    <th><?php echo __('Rating')?></th>
                                                    <th><?php echo __('Progress')?></th>
                                                    <th><?php echo __('Amount')?></th>
                                                    <th><?php echo __('Action')?></th>
                                                </tr>
                                            </thead>
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