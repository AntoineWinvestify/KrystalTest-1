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
 * 
 * [2017-10-24] version 0.3
 * Added db data
 * Added ajax function for individual pfp data
 * 
 * [2017-10-27] version 0.4
 * Moved from test to dasboard2s
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
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
        
        /*$(document).on("click", "[data-toggle='tab']", function(){
            $("#btnMyInvestments").hide();
            $("#btnAccountLinkingB").hide();
            $("#dashboardMyInvestments").show();
            $("#keyIndividualPlatforms").hide();
        });*/
        
       /*$(document).on("click", "#globalOverviewTab", function(){
            $("#btnMyInvestments").show();
            $("#btnAccountLinkingB").show();
            $("#dashboardMyInvestments").hide();
            $("#keyIndividualPlatforms").hide();
        });*/
        
        $(document).on("click", ".logo", function(){ 
            id = $(this).attr("id").split(" ")[0];
            var params = {
                id : $(this).attr("id"),
                logo : $("#logo"+id).attr("src"),
                name : $("#logo"+id).attr("alt"),
            };
            var data = jQuery.param(params);
            link = $(this).attr("href");
            getServerData(link, data, successAjax, errorAjax);
               
        });
        
        <?php //Tooltip clicks ?>
        $(".logo").hover(function() {
            id = $(this).attr("id");
            $("#showBtn").toggle();
        });
        
        <?php //Chart invoke ?>
        $(document).on("click", ".chartIcon", function() {
            id = $(this).attr("id");
            $("#chart_" + id).slideToggle("slow");
            $(this).toggleClass("active");
        });
        <?php //Bootstrap tooltips ?>
        $('[data-toggle="tooltip"]').tooltip();
        
        /*var birdsCanvas = document.getElementById("birdsChart");

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
        });*/
    
        var ctx = document.getElementById('birdsChart').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ["jan","feb", "mar", "apr","may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"],
            datasets: [{
              label: 'Net Return',
              data: [20, 10, 40, 100, 33, 87, 56, 98, 45, 17, 26, 38],
              borderColor: ["rgba(0, 230, 77, 1)"],
              borderWidth: 2,
              fill: false,
              scales: {
                  yAxes: [{
                          ticks: {
                              beginAtZero: true,
                              min: 0
                          }
                  }]
              }
            }]
          }
        });
    
    
    });
    
    function successAjax(result){
       // alert("ok " + result);
       $(".dashboarGlobaldOverview").fadeOut();
       $(".ajaxResponse").html(result);
       
    }
    
    function errorAjax(result){
         //alert("not ok " + result);
    }
    
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
        font-weight: 500 !important;
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
    canvas {
        padding: 15px;
    }
    span.active {
        font-weight: bold;
        color: #00e64d;
    }
    .tooltip.top .tooltip-inner {
        border: 1px solid #87e14b;
        border-radius: 0px;
        background-color: rgba(255,255,255,1);
        color: black;
        text-align: center;
        padding: 8px 12px;
        -webkit-box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15);
        -moz-box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15);
        box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15); 
    }
    .tooltip.top .tooltip-arrow {
        background: white;
        border: 1px solid #87e14b;
        content: '';
        width: 10px;
        height: 10px;
        border-right: none;
        border-bottom: none;
        position: absolute;
        transform: rotate3d(0, 0, 1, -135deg);
    }
</style>
<div class="dashboarGlobaldOverview">
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
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="globalOverviewTab">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Total Volume')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 01" data-toggle="tooltip" data-placement="top"class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title"><?php echo round($global['totalVolume'], 2)  ?> €</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box1Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 04" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo round($global['investedAssets'], 2)  ?> €</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 05" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo round($global['reservedFunds'], 2)  ?> €</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 06" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo round($global['cash'], 2)  ?> €</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 07" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash Drag')?></td>
                                                        <td class="right">4,28%</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 08" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Deposits')?></td>
                                                        <td class="right">39.000,00 €</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 09" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('Active Investments')?></td>
                                                        <td class="right"><?php echo $global['activeInvestment']  ?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Actual Yield')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 02" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title">9,45%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box2Table" class="table" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 10" data-toggle="tooltip" data-placement="top" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR Total Funds')?></td>
                                                        <td class="right"><?php echo __('9,05%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 11" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR')?></td>
                                                        <td class="right"><?php echo __('9,45%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 12" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR, past year')?></td>
                                                        <td class="right"><?php echo __('8,90%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left">
                                                            <i data-toggle="tooltip" data-placement="top" title="some text to tooltip 13" class="ion ion-ios-information-outline" ></i> 
                                                            <span class="chartIcon" id="netReturn">
                                                                <?php echo __('Net Return')?> 
                                                                <i class="ion ion-arrow-graph-up-right" style="color:black"></i>
                                                            </span>
                                                        </td>
                                                        <td class="right"><?php echo __('3.743,82 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 14" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Return, past year')?></td>
                                                        <td class="right"><?php echo __('3.439,10 €')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Defaulted')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 03" class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title"><?php echo $defaultedRange['1-7'] + $defaultedRange['8-30'] + $defaultedRange['31-60'] + $defaultedRange['61-90'] + $defaultedRange['>90'] . "%"?></h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box3Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 15" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></td>
                                                        <td class="right"><?php echo $defaultedRange['current'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('1-7 DPD')?></td>
                                                        <td class="right"><?php echo $defaultedRange['1-7'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo $defaultedRange['8-30'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-60 DPD')?></td>
                                                        <td class="right"><?php echo $defaultedRange['31-60'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('61-90 DPD')?></td>
                                                        <td class="right"><?php echo $defaultedRange['61-90'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Default (> 90 DPD)')?></td>
                                                        <td class="right"><?php echo $defaultedRange['>90'] . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 16" class="ion ion-ios-information-outline" ></i> <?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('869,11 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display:none;" id="chart_netReturn">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                                    <canvas id="birdsChart" class="center-block" width="400" align="center"></canvas>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div align="right"><small><strong><?php echo __('Last Update:')?></strong> 13:23</small></div>
                                </div>
                            </div>
                        </div>                                         
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php //if(count($individualInfoArray) == 0) {?>
    <div class="row" id="btnAL">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type='button' id="btnAccountLinkingB" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
        </div>
    </div> <?php //} else {?>
    <div class="row" id="keyIndividualPlatforms">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="gray">
                    <h4 class="title"><?php echo __('Key Performance statistics of Individual Platforms') ?></h4>
                </div>
                <div class="card-content table-responsive">
                    <table id="keyPerformanceStatistics" class="table">
                        <thead>
                            <tr>
                                <th><?php echo __('Lending Company')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="tooltip 17" class="ion ion-ios-information-outline"></i> <?php echo __('Total Volume')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="tooltip 18" class="ion ion-ios-information-outline"></i> <?php echo __('Cash')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="tooltip 19" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Explosure to platform')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="tooltip 20" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Actual Yield')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="some text to tooltip 21" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php //Here go pfp data
                            foreach($individualInfoArray as $individualInfo){ 
                                $total = round($individualInfo['Userinvestmentdata']['userinvestmentdata_totalVolume'], 2);
                                ?>
                            <tr>
                                <td class="logo" href='getDashboard2SinglePfpData' id="<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id']  .  " " . $individualInfo['Userinvestmentdata']["id"] ?>" >
                                    <img id="logo<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id'] ?>" src="/img/logo/<?php echo $individualInfo['Userinvestmentdata']['pfpLogo'] ?>" class="img-responsive center-block platformLogo" alt="<?php echo $individualInfo['Userinvestmentdata']['pfpName']?>"/>
                                </td>
                                
                                <td><?php echo $total . " &euro;"?></td>
                                <td><?php echo round($individualInfo['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], 2) . " &euro;"?></td>
                                <td><?php echo round(bcmul(bcdiv($total, $global['totalVolume'],16), 100, 16), 2, PHP_ROUND_HALF_UP) . " %"?></td>
                                <td>12,11</td>
                                <td>63,22%</td>
                            </tr>
                            <?php } ?>
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
    </div> <?php // } ?>
</div>
<div class = "ajaxResponse"> 
</div>